<?

namespace Iplogic\Beru\V2\Command;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;
use \Iplogic\Beru\V2\ORM\ProfileTable;
use \Iplogic\Beru\V2\ORM\ProductTable;
use \Iplogic\Beru\V2\ORM\BusinessTable;
use \Iplogic\Beru\V2\ApiRequest;
use \Iplogic\Beru\V2\Product;


/**
 * Provides downloading of offers from the marketplace and storing their instances in the module database
 *
 * Class getMpOffers
 * @package Iplogic\Beru\V2\Command
 */
class getMpOffers implements CommandInterface
{
	/**
	 * @var bool
	 */
	protected $DEBUG = true;

	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";

	/**
	 * @var int
	 */
	protected $businessId;

	/**
	 * @var string
	 */
	protected $pageToken;

	/**
	 * @var array
	 */
	protected $arProfiles = [];

	/**
	 * @var string
	 */
	protected $status = "Not started";


	public function execute(): void
	{
		if( !$this->getBusinessAndProfiles() ) {
			return;
		}

		if( $this->DEBUG ) {
			$logFileName = realpath(__DIR__ . "/../../..") . "tmp/" . time() . "-" .
				randString(3, ["0123456789"]) . ".log";
		}

		$this->status = "Iteration started";

		// getting current hidden offers from market
		$arHidden = [];
		foreach( $this->arProfiles as $ar_Profile ) {
			$arHidden[$ar_Profile["COMPAIN_ID"]] = [];
			$result = (new ApiRequest\getHidden($ar_Profile["ID"]))->send();
			foreach( $result["body"]["result"]["hiddenOffers"] as $offer ) {
				$arHidden[$ar_Profile["COMPAIN_ID"]][] = $offer["offerId"];
			}
		}

		// data for getBusinessOffers
		$arParams = ["limit" => Option::get(self::$moduleID, "products_add_num", 50)];
		$arProfileId = current($this->arProfiles)["ID"];

		// getting 5 request per step
		for( $i = 0; $i < 5; $i++ ) {
			if( $this->pageToken ) {
				$arParams["page_token"] = $this->pageToken;
			}
			$result =
				(new ApiRequest\getBusinessOffers($arProfileId))->send($arParams, ["archived" => false]);
			if( $result["status"] != 200 ) {
				$this->pageToken = "";
				$this->status = "Bad respond";
				break;
			}
			if( !count($result["body"]["result"]["offerMappings"]) ) {
				$this->pageToken = "";
				$this->status = "Bad respond";
				break;
			}
			$arFieldsSet = [];
			$arForCheck = [];
			foreach( $result["body"]["result"]["offerMappings"] as $offer ) {
				if( $this->DEBUG ) {
					file_put_contents($logFileName, print_r($offer, true), FILE_APPEND);
				}
				if( is_array($offer["offer"]["campaigns"]) && count($offer["offer"]["campaigns"]) ) {
					// common data for all campaigns
					$arFields = [
						"SKU_ID"     => $offer["offer"]["offerId"],
						"MARKET_SKU" => $offer["mapping"]["marketSku"],
						"NAME"       => $offer["offer"]["name"],
						"VENDOR"     => $offer["offer"]["vendor"],
						"STATE"      => $offer["offer"]["cardStatus"],
						"FOR_DELETE" => "N",
					];
					// data for every campaign
					foreach( $offer["offer"]["campaigns"] as $arComp ) {
						if( isset($this->arProfiles[$arComp["campaignId"]]) ) {
							$arCurProfile = $this->arProfiles[$arComp["campaignId"]];
							if( $arComp["status"] == "NO_STOCKS" ) {
								$arForCheck[$arCurProfile["ID"]][] = $offer["offer"]["offerId"];
							}
							$id = Product::getProductIdBySku($offer["offer"]["offerId"], $arCurProfile);
							$hidden = "N";
							if(
								$offer["offer"]["offerId"] &&
								in_array($offer["offer"]["offerId"], $arHidden[$arComp["campaignId"]])
							) {
								$hidden = "Y";
							}
							$arFields["PROFILE_ID"] = $arCurProfile["ID"];
							$arFields["PRODUCT_ID"] = $id;
							$arFields["HIDDEN"] = $hidden;
							$arFieldsSet[] = $arFields;
						}
					}
				}
			}
			// offerIds for every campaign we dont need to add
			$arDontAdd = [];
			foreach( $arForCheck as $profileId => $arOffers ) {
				$res = (new ApiRequest\getCampaignOffers($profileId))->send(
					[],
					["offerIds" => $arOffers]
				);
				if( isset($res["body"]["result"]["offers"]) ) {
					foreach( $res["body"]["result"]["offers"] as $offer ) {
						if( $this->DEBUG ) {
							file_put_contents($logFileName, $profileId, FILE_APPEND);
							file_put_contents($logFileName, print_r($offer, true), FILE_APPEND);
						}
						if( !isset($offer["campaignPrice"]) ) {
							$arDontAdd[$profileId][] = $offer["offerId"];
						}
					}
				}
			}
			// adding or updating products in the module
			foreach( $arFieldsSet as $key => $arFields ) {
				if(
					isset($arDontAdd[$arFields["PROFILE_ID"]]) &&
					in_array($arFields["SKU_ID"], $arDontAdd[$arFields["PROFILE_ID"]])
				) {
					continue;
				}
				$res = ProductTable::getList(
					[
						"filter" => [
							"PROFILE_ID" => $arFields["PROFILE_ID"],
							"SKU_ID"     => $arFields["SKU_ID"],
						],
					]
				);
				if( $pr = $res->Fetch() ) {
					ProductTable::update($pr["ID"], $arFields);
				}
				else {
					ProductTable::add($arFields);
				}
			}
			if( $result["body"]["result"]["paging"]["nextPageToken"] != "" ) {
				$this->pageToken = $result["body"]["result"]["paging"]["nextPageToken"];
			}
			else {
				$this->pageToken = "";
			}
		}
		/*file_put_contents(__DIR__."/1.txt", "[".date('d.m.Y H:i:s')."] https://" . Option::get(self::$moduleID, "domen") .
			"/bitrix/services/iplogic/mkpapi/getmpoffers.php?param=" . $this->businessId . "__" . $this->pageToken . "
			
", FILE_APPEND );*/
		exec(
			"wget --no-check-certificate -b -q -O - https://" . Option::get(self::$moduleID, "domen") .
			"/bitrix/services/iplogic/mkpapi/getmpoffers.php?param=" . $this->businessId . "__" . $this->pageToken
		);
		die();
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setParams(array $arParams = []): void
	{
		$this->businessId = $arParams['business_id'];
		if( $this->businessId > 0 ) {
			$this->arProfiles = $this->getBusinessProfiles(BusinessTable::getById($this->businessId)->fetch());
		}
		$this->pageToken = $arParams['page_token'];
	}

	protected function getBusinessAndProfiles(): bool
	{
		if( $this->businessId > 0 && $this->pageToken != "" ) {
			return true;
		}
		$arBusinesses = [];
		$rsBusinesses = BusinessTable::getList();
		while( $arBusiness = $rsBusinesses->Fetch() ) {
			$arProfiles = $this->getBusinessProfiles($arBusiness);
			if( count($arProfiles) ) {
				$arBusiness["PROFILES"] = $arProfiles;
				$arBusinesses[] = $arBusiness;
			}
		}
		if( !count($arBusinesses) ) {
			$this->status = "No businesses found";
			return false;
		}
		// first step
		if( !$this->businessId ) {
			$this->businessId = $arBusinesses[0]["ID"];
			$this->arProfiles = $arBusinesses[0]["PROFILES"];
			ProductTable::markAllForDelete();
			Option::set(self::$moduleID, "products_check_last_time", time());
		}
		// not first step - next business
		elseif( $this->businessId > 0 && $this->pageToken == "" ) {
			foreach( $arBusinesses as $key => $bis ) {
				if( $bis["ID"] == $this->businessId ) {
					// next business exists
					if( array_key_exists($key + 1, $arBusinesses) ) {
						$this->businessId = $arBusinesses[$key + 1]["ID"];
						$this->arProfiles = $arBusinesses[$key + 1]["PROFILES"];
					}
					// no next business - end of execution
					else {
						ProductTable::deleteMarked();
						Option::set(self::$moduleID, "products_check_last_time", time());
						$this->status = "Last business executed";
						return false;
					}
					break;
				}
			}
		}
		if( !$this->businessId ) {
			$this->status = "No current business found";
			return false;
		}
		return true;
	}

	protected function getBusinessProfiles($arBusiness): array
	{
		$arProfiles = [];
		$arFilter = ["=ACTIVE" => "Y", "!COMPAIN_ID" => "", "BUSINESS_ID" => $arBusiness["BID"]];
		if( $arBusiness["API_KEY"] == "" ) {
			$arFilter["!CLIENT_ID"] = "";
			$arFilter["!SEND_TOKEN"] = "";
		}
		$rsProfiles = ProfileTable::getList(["order" => ["ID"], "filter" => $arFilter]);
		while( $arProfile = $rsProfiles->Fetch() ) {
			$arProfiles[$arProfile["COMPAIN_ID"]] = ProfileTable::getByIdFull($arProfile["ID"]);
		}
		return $arProfiles;
	}
}