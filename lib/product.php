<?php

namespace Iplogic\Beru;

use \Bitrix\Main,
	\Bitrix\Main\Application,
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Web\Json,
	\Bitrix\Main\Config\Option,
	\Iplogic\Beru\Control,
	\Iplogic\Beru\YMAPI,
	\Iplogic\Beru\TaskTable,
	\Iplogic\Beru\ProfileTable;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class ProductTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> PRODUCT_ID int optional
 * <li> SKU_ID string(150) optional
 * <li> MARKET_SKU int optional
 * <li> NAME string optional
 * <li> VENDOR string(255) optional
 * <li> AVAILABILITY bool optional default 'N'
 * <li> STATE string(12) optional
 * <li> REJECT_REASON string(255) optional
 * <li> REJECT_NOTES string optional
 * <li> DETAILS string optional
 * <li> PRICE string(12) optional
 * <li> OLD_PRICE string(12) optional
 * <li> STOCK_FIT string(5) optional
 * <li> PRICE_TIME string(19) optional
 * <li> STOCK_TIME string(19) optional
 * <li> HIDDEN bool optional default 'N'
 * <li> FOR_DELETE bool optional default 'N'
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class ProductTable extends Main\Entity\DataManager
{

	public static $moduleID = "iplogic.beru";

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_product';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			'ID'            => [
				'data_type'    => 'integer',
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage('PRODUCT_ENTITY_ID_FIELD'),
			],
			'PROFILE_ID'    => [
				'data_type' => 'integer',
				'required'  => true,
				'title'     => Loc::getMessage('PRODUCT_ENTITY_PROFILE_ID_FIELD'),
			],
			'PRODUCT_ID'    => [
				'data_type' => 'integer',
				'title'     => Loc::getMessage('PRODUCT_ENTITY_PRODUCT_ID_FIELD'),
			],
			'SKU_ID'        => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateSkuId'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_SKU_ID_FIELD'),
			],
			'MARKET_SKU'    => [
				'data_type' => 'integer',
				'title'     => Loc::getMessage('PRODUCT_ENTITY_MARKET_SKU_FIELD'),
			],
			'NAME'          => [
				'data_type' => 'text',
				'title'     => Loc::getMessage('PRODUCT_ENTITY_NAME_FIELD'),
			],
			'VENDOR'        => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateVendor'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_VENDOR_FIELD'),
			],
			'AVAILABILITY'  => [
				'data_type' => 'boolean',
				'values'    => ['N', 'Y'],
				'title'     => Loc::getMessage('PRODUCT_ENTITY_AVAILABILITY_FIELD'),
			],
			'STATE'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateState'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_STATE_FIELD'),
			],
			'REJECT_REASON' => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateRejectReason'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_REJECT_REASON_FIELD'),
			],
			'REJECT_NOTES'  => [
				'data_type' => 'text',
				'title'     => Loc::getMessage('PRODUCT_ENTITY_REJECT_NOTES_FIELD'),
			],
			'DETAILS'       => [
				'data_type' => 'text',
				'title'     => Loc::getMessage('PRODUCT_ENTITY_DETAILS_FIELD'),
			],
			'PRICE'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validatePrice'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_PRICE_FIELD'),
			],
			'OLD_PRICE'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateOldPrice'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_OLD_PRICE_FIELD'),
			],
			'STOCK_FIT'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateStockFit'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_STOCK_FIT_FIELD'),
			],
			'PRICE_TIME'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validatePriceTime'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_PRICE_TIME_FIELD'),
			],
			'STOCK_TIME'         => [
				'data_type'  => 'string',
				'validation' => [__CLASS__, 'validateStockTime'],
				'title'      => Loc::getMessage('PRODUCT_ENTITY_STOCK_TIME_FIELD'),
			],
			'HIDDEN'        => [
				'data_type' => 'boolean',
				'values'    => ['N', 'Y'],
				'title'     => Loc::getMessage('PRODUCT_ENTITY_HIDDEN_FIELD'),
			],
			'FOR_DELETE'          => [
				'data_type' => 'boolean',
				'values'    => ['N', 'Y'],
				'title'     => Loc::getMessage('PRODUCT_ENTITY_FOR_DELETE_FIELD'),
			],
		];
	}

	/**
	 * Returns validators for SKU_ID field.
	 *
	 * @return array
	 */
	public static function validateSkuId()
	{
		return [
			new Main\Entity\Validator\Length(null, 150),
		];
	}

	/**
	 * Returns validators for VENDOR field.
	 *
	 * @return array
	 */
	public static function validateVendor()
	{
		return [
			new Main\Entity\Validator\Length(null, 255),
		];
	}

	/**
	 * Returns validators for STATE field.
	 *
	 * @return array
	 */
	public static function validateState()
	{
		return [
			new Main\Entity\Validator\Length(null, 12),
		];
	}

	/**
	 * Returns validators for REJECT_REASON field.
	 *
	 * @return array
	 */
	public static function validateRejectReason()
	{
		return [
			new Main\Entity\Validator\Length(null, 255),
		];
	}

	/**
	 * Returns validators for PRICE field.
	 *
	 * @return array
	 */
	public static function validatePrice()
	{
		return [
			new Main\Entity\Validator\Length(null, 12),
		];
	}

	/**
	 * Returns validators for OLD_PRICE field.
	 *
	 * @return array
	 */
	public static function validateOldPrice()
	{
		return [
			new Main\Entity\Validator\Length(null, 12),
		];
	}

	/**
	 * Returns validators for STOCK_FIT field.
	 *
	 * @return array
	 */
	public static function validateStockFit()
	{
		return [
			new Main\Entity\Validator\Length(null, 5),
		];
	}

	/**
	 * Returns validators for PRICE_TIME field.
	 *
	 * @return array
	 */
	public static function validatePriceTime()
	{
		return [
			new Main\Entity\Validator\Length(null, 19),
		];
	}

	/**
	 * Returns validators for STOCK_TIME field.
	 *
	 * @return array
	 */
	public static function validateStockTime()
	{
		return [
			new Main\Entity\Validator\Length(null, 19),
		];
	}


	public static function getById($ID)
	{
		$result = parent::getById($ID);
		return $result->Fetch();
	}


	public static function getBySkuId($ID, $PROFILE_ID)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"SELECT * FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('SKU_ID') . "='" .
			$ID . "' AND " . $helper->quote('PROFILE_ID') . "=" . $PROFILE_ID;  //echo $strSql;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return $result->Fetch();
	}


	public static function getByProductId($ID)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"SELECT * FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('PRODUCT_ID') . "='" .
			$ID . "'";  //echo $strSql;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return $result;
	}


	public static function markAllForDelete()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("UPDATE " . $helper->quote(self::getTableName()) . " SET FOR_DELETE='Y'");
		unset($helper, $conn);
		return;
	}


	public static function deleteMarked()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE FOR_DELETE='Y'");
		unset($helper, $conn);
		return;
	}

	public static function deleteByProfile($profileId)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE PROFILE_ID=".$profileId);
		unset($helper, $conn);
		return;
	}


	public static function getBusinessProducts($business_id = false, $page_token = false)
	{
		$arProfiles = [];
		$arBusinesses = [];
		$rsProfiles = ProfileTable::getList(["filter" => ["ACTIVE" => "Y", "!COMPAIN_ID" => "", "!BUSINESS_ID" => ""]]);
		while( $ar_Profile = $rsProfiles->Fetch() ) {
			$arProfiles[$ar_Profile["COMPAIN_ID"]] = ProfileTable::getById($ar_Profile["ID"]);
			if(!in_array($ar_Profile["BUSINESS_ID"], $arBusinesses)) {
				$arBusinesses[] = $ar_Profile["BUSINESS_ID"];
			}
		}
		if( !count($arBusinesses) ) {
			return;
		}
		sort($arBusinesses);
		// first step
		if( !$business_id ) {
			$business_id = $arBusinesses[0];
			self::markAllForDelete();
			Option::set(self::$moduleID, "products_check_last_time", time());
		}
		// not first step - next business
		elseif( $business_id && $page_token == "" ) {
			foreach($arBusinesses as $key => $bid) {
				if($bid == $business_id) {
					// next business exists
					if(array_key_exists($key+1, $arBusinesses)) {
						$business_id = $arBusinesses[$key+1];
					}
					// no next business - end of execution
					else {
						self::deleteMarked();
						Option::set(self::$moduleID, "products_check_last_time", time());
						return;
					}
					break;
				}
			}
		}
		if( !$business_id ) {
			return;
		}
		$arProfile = false;
		foreach($arProfiles as $ar_Profile) {
			if(
				$ar_Profile["BUSINESS_ID"] == $business_id &&
				$ar_Profile["CLIENT_ID"] != "" &&
				$ar_Profile["SEND_TOKEN"] != ""
			) {
				$arProfile = $ar_Profile;
			}
		}
		if( !$arProfile ) {
			return;
		}

		$con = new Control();
		$api = new YMAPI();

		$arHidden = [];
		foreach($arProfiles as $ar_Profile) {
			$api->setProfile($ar_Profile);
			$arHidden[$ar_Profile["COMPAIN_ID"]] = [];
			$result = $api->getHidden();
			foreach( $result["body"]["result"]["hiddenOffers"] as $offer ) {
				$arHidden[$ar_Profile["COMPAIN_ID"]][] = $offer["offerId"];
			}
		}

		$arParams = ["limit" => Option::get(self::$moduleID, "products_add_num", 50)];

		for( $i = 0; $i < 5; $i++ ) {
			if( $page_token ) {
				$arParams["page_token"] = $page_token;
			}
			$api = new YMAPI();
			$api->setProfile($arProfile);
			$result = $api->getOffers($arParams);
			if( $result["status"] != 200 ) {
				$page_token = "";
				break;
			}
			if( !count($result["body"]["result"]["offerMappings"]) ) {
				$page_token = "";
				break;
			}
			foreach( $result["body"]["result"]["offerMappings"] as $offer ) {
				if(is_array($offer["offer"]["campaigns"]) && count($offer["offer"]["campaigns"])) {
					$arFields = [
						"SKU_ID"        => $offer["offer"]["offerId"],
						"NAME"          => $offer["offer"]["name"],
						"VENDOR"        => $offer["offer"]["vendor"],
						//"AVAILABILITY"  => ($offer["offer"]["availability"] == "ACTIVE" ? "Y" : "N"),
						"FOR_DELETE"    => "N"
					];
					foreach($offer["offer"]["campaigns"] as $arComp) {
						if(isset($arProfiles[$arComp["campaignId"]])) {
							$con->arProfile = $arProfiles[$arComp["campaignId"]];
							$id = $con->getProductId($offer["offer"]["offerId"]);
							$hidden = "N";
							if(
								$offer["offer"]["offerId"] &&
								in_array($offer["offer"]["offerId"], $arHidden[$arComp["campaignId"]])
							) {
								$hidden = "Y";
							}
							$arFields["PROFILE_ID"] = $con->arProfile["ID"];
							$arFields["PRODUCT_ID"] = $id;
							$arFields["STATE"] = $arComp["status"];
							$arFields["HIDDEN"] = $hidden;
							$res = self::getList(
								["filter" => ["PROFILE_ID" => $con->arProfile["ID"], "SKU_ID" => $offer["offer"]["offerId"]]]
							);
							if( $pr = $res->Fetch() ) {
								self::update($pr["ID"], $arFields);
							}
							else {
								self::add($arFields);
							}
						}
					}
				}
			}
			if( $result["body"]["result"]["paging"]["nextPageToken"] != "" ) {
				$page_token = $result["body"]["result"]["paging"]["nextPageToken"];
			}
			else {
				$page_token = "";
			}
		}
		exec(
			"wget --no-check-certificate -b -q -O - https://" . Option::get(self::$moduleID, "domen") .
			"/bitrix/services/iplogic/mkpapi/products.php?param=" . $business_id . "__" . $page_token
		);
		die();
	}


	/*
	 * Depricated
	 */
	public static function checkMarketProducts($profile_id = false, $page_token = false)
	{
		$rsProfiles = ProfileTable::getList(["order" => ["ID" => "ASC"], "filter" => ["ACTIVE" => "Y"]]);
		// first step
		if( !$profile_id ) {
			$arProfile = $rsProfiles->Fetch();
			if( !$arProfile ) {
				return;
			}
			self::markAllForDelete();
			Option::set(self::$moduleID, "products_check_last_time", time());
		}
		// not first step for known profile
		elseif( $profile_id && $page_token != "" ) {
			while( $ar_Profile = $rsProfiles->Fetch() ) {
				if( $ar_Profile["ID"] == $profile_id ) {
					$arProfile = $ar_Profile;
					break;
				}
			}
		}
		// not first step - next profile
		else {
			while( $ar_Profile = $rsProfiles->Fetch() ) {
				if( $ar_Profile["ID"] == $profile_id ) {
					$arProfile = $rsProfiles->Fetch();
					break;
				}
			}
			// no next profile - end of execution
			if( !$arProfile ) {
				self::deleteMarked();
				Option::set(self::$moduleID, "products_check_last_time", time());
			}
		}
		if( !$arProfile ) {
			return;
		}

		if(
			$arProfile["CLIENT_ID"] != ""
			&& $arProfile["COMPAIN_ID"] != ""
			&& $arProfile["SEND_TOKEN"] != ""
		) {
			$con = new Control();
			$con->arProfile = ProfileTable::getById($arProfile["ID"]);

			$arHidden = [];
			$api = new YMAPI($arProfile["ID"]);
			$result = $api->getHidden();
			foreach( $result["body"]["result"]["hiddenOffers"] as $offer ) {
				$arHidden[] = $offer["marketSku"];
			}

			$arParams = ["limit" => Option::get(self::$moduleID, "products_add_num", 50)];
			for( $i = 0; $i < 5; $i++ ) {
				if( $page_token ) {
					$arParams["page_token"] = $page_token;
				}
				$api = new YMAPI($arProfile["ID"]);
				$result = $api->getOffersMapping($arParams);
				if( $result["status"] != 200 ) {
					$page_token = "";
					break;
				}
				if( !count($result["body"]["result"]["offerMappingEntries"]) ) {
					$page_token = "";
					break;
				}
				foreach( $result["body"]["result"]["offerMappingEntries"] as $offer ) {
					$id = $con->getProductId($offer["offer"]["shopSku"]);
					$market_sku = null;
					if( isset($offer["mapping"]) ) {
						$market_sku = $offer["mapping"]["marketSku"];
					}
					elseif( isset($offer["awaitingModerationMapping"]) ) {
						$market_sku = $offer["awaitingModerationMapping"]["marketSku"];
					}
					elseif( isset($offer["rejectedMapping"]) ) {
						$market_sku = $offer["rejectedMapping"]["marketSku"];
					}
					$hidden = "N";
					if( $market_sku && in_array($market_sku, $arHidden) ) {
						$hidden = "Y";
					}
					$arReason = [];
					$arNote = [];
					foreach( $offer["offer"]["processingState"]["notes"] as $rn ) {
						$arReason[] = $rn["type"];
						$arNote[] = [];
						if( $rn["payload"] != "" ) {
							$n = Json::decode($rn["payload"]);
							$arNote[] = $n["itemsAsString"];
						}
					}
					if( count($arReason) ) {
						$stReason = implode(", ", $arReason);
					}
					else {
						$stReason = null;
					}
					if( count($arNote) ) {
						$stNote = implode(". ", $arNote);
					}
					else {
						$stNote = null;
					}
					$arFields = [
						"PROFILE_ID"    => $arProfile["ID"],
						"PRODUCT_ID"    => $id,
						"SKU_ID"        => $offer["offer"]["shopSku"],
						"MARKET_SKU"    => $market_sku,
						"NAME"          => $offer["offer"]["name"],
						"VENDOR"        => $offer["offer"]["vendor"],
						"AVAILABILITY"  => ($offer["offer"]["availability"] == "ACTIVE" ? "Y" : "N"),
						"STATE"         => $offer["offer"]["processingState"]["status"],
						"REJECT_REASON" => $stReason,
						"REJECT_NOTES"  => $stNote,
						"HIDDEN"        => $hidden,
						"FOR_DELETE"    => "N"
					];
					$res = self::getList(
						["filter" => ["PROFILE_ID" => $arProfile["ID"], "SKU_ID" => $offer["offer"]["shopSku"]]]
					);
					if( $pr = $res->Fetch() ) {
						self::update($pr["ID"], $arFields);
					}
					else {
						self::add($arFields);
					}
				}
				if( $result["body"]["result"]["paging"]["nextPageToken"] != "" ) {
					$page_token = $result["body"]["result"]["paging"]["nextPageToken"];
				}
				else {
					$page_token = "";
				}
			}
		}

		exec(
			"wget --no-check-certificate -b -q -O - https://" . Option::get(self::$moduleID, "domen") .
			"/bitrix/services/iplogic/mkpapi/products.php?param=" . $arProfile["ID"] . "__" . $page_token
		);
		die();

	}


	public static function updateCache($ID)
	{
		if( $product = self::getById($ID) ) {
			$arProfile = ProfileTable::getById($product["PROFILE_ID"]);
			if( $arProfile["ACTIVE"] != "Y" ) {
				return false;
			}
			$con = new Control($product["PROFILE_ID"]);
			$set = $con->getSKU($product["SKU_ID"], [], true);
			$eventManager = Main\EventManager::getInstance();
			$eventsList = $eventManager->findEventHandlers('iplogic.beru', 'OnIplogicBeruBeforeProductCacheSave');
			foreach( $eventsList as $arEvent ) {
				if( ExecuteModuleEventEx($arEvent, [$product["PRODUCT_ID"], &$set]) === false ) {
					return false;
				}
			}
			if(
				// old statuses
				$product["STATE"] == "READY" ||
				$product["STATE"] == "NEED_CONTENT" ||
				// new statuses
				$product["STATE"] == "PUBLISHED" ||
				$product["STATE"] == "NO_STOCKS"
			) {
				if( $product["PRICE"] != $set["PRICE"] && $set["PRICE"] > 0 ) {
					TaskTable::addPriceUpdateTask($ID, $product["PROFILE_ID"]);
				}
				if( $product["OLD_PRICE"] != $set["OLD_PRICE"] ) {
					TaskTable::addPriceUpdateTask($ID, $product["PROFILE_ID"]);
				}
				if( $product["STOCK_FIT"] != $set["STOCK_FIT"] ) {
					TaskTable::addStockUpdateTask($ID, $product["PROFILE_ID"]);
				}
			}
			$eventManager = Main\EventManager::getInstance();
			$eventsList = $eventManager->findEventHandlers('iplogic.beru', 'OnIplogicBeruProductCacheSave');
			foreach( $eventsList as $arEvent ) {
				if( ExecuteModuleEventEx($arEvent, [$product["PRODUCT_ID"], &$set]) === false ) {
					return false;
				}
			}
			$cache = serialize($set);
			$arFields = ["DETAILS" => $cache];
			return self::update($ID, $arFields);
		}
		return false;
	}

}