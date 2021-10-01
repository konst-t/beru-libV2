<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main\Loader,
	\Iplogic\Beru\BoxTable,
	\Iplogic\Beru\YMAPI,
	\Iplogic\Beru\Control,
	\Iplogic\Beru\OrderTable;

Loader::includeModule("iplogic.beru");

function saveFile($name, $body) {
	$arPath = explode('/',$name);
	unset($arPath[0]);
	$sPath = $_SERVER["DOCUMENT_ROOT"];
	for($i = 1; $i<count($arPath); $i++) {
		$sPath .= "/".$arPath[$i];
		if(!is_dir($sPath)) {
			if(!mkdir($sPath, 0777, true))
				return false;
		}
	}
	if(file_put_contents($_SERVER["DOCUMENT_ROOT"].$name, $body))
		return true;
	return false;
}


$box = BoxTable::getById($_GET["box"]);
$order = OrderTable::getById($box["ORDER_ID"]);

$arParams = [
	"ORDER_ID" => $order["EXT_ID"],
	"SHIPMENT_ID" => $order["SHIPMENT_ID"],
	"BOX_ID" => $box["EXT_ID"]
];

if (isset($_GET["filename"])) {
	$api = new YMAPI($order["PROFILE_ID"]);
	$result = $api->getLabel($arParams);
	if ($result["status"] == 200) {
		$res = saveFile($_GET["filename"], $result["body"]);
	}
	if ($res){
		echo "OK";
	}
	else {
		echo "ERROR";
	}
} else {
	$api = new YMAPI($order["PROFILE_ID"]);
	$result = $api->getLabel($arParams);
	if ($result["status"] == 200) {
		header("Content-type:application/pdf");
		echo $result["body"];
	}
	else {
		echo "ERROR";
	}
}
?>