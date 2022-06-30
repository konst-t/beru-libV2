<?

use \Bitrix\Main\Web\Json;
use \Bitrix\Main\Config\Option;
use \Iplogic\Beru\Control;
use \Iplogic\Beru\IntervalTable;
use \Iplogic\Beru\OutletTable;
use \Iplogic\Beru\DeliveryTable;
use \Iplogic\Beru\HolidayTable;

function mkaAction()
{

	global $mod;

	$request = Json::decode(file_get_contents('php://input'));

	if(
		!count($mod->arProfile["PROP"])
		|| !isset($mod->arProfile["PROP"]["SHOP_SKU_ID"])
		|| !isset($mod->arProfile["PROP"]["STOCK_FIT"])
		|| !isset($mod->arProfile["PROP"]["PRICE"])
	) {
		$mod->error = [
			"500",
			"Internal Server Error",
			"Wrong profile settings",
		];
		return false;
	}

	$arSelect = ["STOCK_FIT"];

	$data["cart"]["items"] = [];

	$allEmpty = true;
	$sendReal = true;
	if( Option::get(Control::$moduleID, "send_zero_stocks") == "Y" ) {
		$sendReal = false;
	}

	foreach( $request["cart"]["items"] as $SKU ) {
		$arFitures = $mod->getSKU($mod->prepareRequestText(Control::fixUnicode($SKU["offerId"])), $arSelect);
		if($SKU["count"] > $arFitures["STOCK_FIT"]){
			$arFitures["STOCK_FIT"] = 0;
		}
		$arSKU = [
			"feedId"   => $SKU["feedId"],
			"offerId"  => Control::fixUnicode($SKU["offerId"]),
			"count"    => ($sendReal ? (int)$arFitures["STOCK_FIT"] : 0),
			"delivery" => true,
		];
		$data["cart"]["items"][] = $arSKU;
		if( $arFitures["STOCK_FIT"] > 0 ) {
			$allEmpty = false;
		}
	}

	if( $allEmpty ) {
		$data["cart"]["items"] = [];
	}

	if( $mod->arProfile["SCHEME"] == "DBS" ) {
		$data["cart"]["deliveryCurrency"] = "RUR";
		$data["cart"]["deliveryOptions"] = [];
		$rsDelivery =
			DeliveryTable::getList(
				["filter" => ["PROFILE_ID" => $mod->arProfile["ID"], "ACTIVE" => "Y"], "order" => ["SORT" => "ASC"]]
			);
		$arIntervals = [];
		$rsIntervals =
			IntervalTable::getList(
				["filter" => ["PROFILE_ID" => $mod->arProfile["ID"]], "order" => ["DAY" => "ASC"]]
			);
		while($arInterval = $rsIntervals->fetch()) {
			$arIntervals[$arInterval["DELIVERY_ID"]][] = $arInterval;
		}
		$arOutlets = [];
		$rsOutlets =
			OutletTable::getList(
				["filter" => ["PROFILE_ID" => $mod->arProfile["ID"]], "order" => ["CODE" => "ASC"]]
			);
		while($arOutlet = $rsOutlets->fetch()) {
			$arOutlets[$arOutlet["DELIVERY_ID"]][] = $arOutlet;
		}
		$arHolidays = [];
		$rsHolidays =
			HolidayTable::getList(
				["filter" => ["PROFILE_ID" => $mod->arProfile["ID"]]]
			);
		while($arHoliday = $rsHolidays->fetch()) {
			$arHolidays[$arHoliday["DELIVERY_ID"]][] = $arHoliday["DATE"];
		}
		while($arDelivery = $rsDelivery->fetch()) {
			$option = [];
			$option["id"] = (string)$arDelivery["ID"];
			$option["price"] = 100;
			$option["serviceName"] = (string)$arDelivery["NAME"];
			$option["type"] = (string)$arDelivery["TYPE"];
			$days = [];
			for($i = $arDelivery["DAY_FROM"]; $i<=$arDelivery["DAY_TO"]; $i++) {
				$key = time() + ($i * 86400);
				$val = date('d-m-Y', $key);
				$days[$key] = $val;
			}
			$intervals = [];
			if(count($arIntervals[$arDelivery["ID"]])) {
				$arInts = [];
				foreach($arIntervals[$arDelivery["ID"]] as $int) {
					$arInts[$int["DAY"]][] = $int;
				}
				foreach($days as $timestamp => $day) {
					$dow = date('N', $timestamp);
					if(array_key_exists($dow, $arInts) && !in_array($day, $arHolidays[$arDelivery["ID"]])) {
						foreach($arInts[$dow] as $int) {
							$intervals[] = [
								"date" => $day,
								"fromTime" => $int["TIME_FROM"],
								"toTime" => $int["TIME_TO"],
							];
						}
					}
					else{
						unset($days[$timestamp]);
					}
				}
			}
			elseif (count($arHolidays[$arDelivery["ID"]])) {
				foreach($days as $timestamp => $day) {
					if(in_array($day, $arHolidays[$arDelivery["ID"]])) {
						unset($days[$timestamp]);
					}
				}
			}
			$option["dates"] = [
				"fromDate" => reset($days),
				"toDate" => $days[array_key_last($days)]
			];
			if(count($intervals)) {
				$option["dates"]["intervals"] = $intervals;
			}
			if(count($arOutlets[$arDelivery["ID"]])) {
				$arOuts = [];
				foreach($arOutlets[$arDelivery["ID"]] as $out) {
					$arOuts[] = ["code" => (string)$out["CODE"]];
				}
				$option["outlets"] = $arOuts;
			}
			$data["cart"]["deliveryOptions"][] = $option;
		}
		$data["cart"]["paymentMethods"] = unserialize($mod->arProfile["PAYMENT_METHODS"]);
	}

	return $data;
}

?>