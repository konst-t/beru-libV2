<?php
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);
define("STOP_STATISTICS", true);
define("BX_SECURITY_SHOW_MESSAGE", true);

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;
use \Bitrix\Main\Web\Json;
use \Iplogic\Beru\DeliveryTable;
use \Iplogic\Beru\HolidayTable;

$baseFolder = realpath(__DIR__ . "/../../../..");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($baseFolder."/modules/".$moduleID.'/admin/delivery_edit.php');

CJSCore::Init(array("jquery"));

/* fatal errors check, creat control object and get table data */
$checkParams = [
	"PROFILE" => true,
];

include($baseFolder."/modules/".$moduleID."/prolog.php");

$ID = $request->get("ID");
if ($ID > 0){
	$arFields = DeliveryTable::getRowById($ID);
	if (!$arFields){
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
	}
}
else {
	$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
}


if ($fatalErrors != "") {
	echo(Json::encode(
		[
			"result"  => "error",
			"message" => $fatalErrors
		]
	));
	die();
}

$arParams = $request->get('params');

if ($arParams["CONTENT"] == "") {
	echo(Json::encode(
		[
			"result"  => "error",
			"message" => Loc::getMessage("IPL_MA_ERROR_DATES")
		]
	));
	die();
}

if ($request->get('action')=='add' && $MODULE_ACCESS >= "W") {

	$arParams["DATE"] = $arParams["CONTENT"];
	unset($arParams["CONTENT"]);

	$arParams["PROFILE_ID"] = $request->get('PROFILE_ID');
	$arParams["DELIVERY_ID"] = $request->get('ID');

	$d = DateTime::createFromFormat('d-m-Y H:i:s', $arParams["DATE"].' 12:00:00');
	if ($d === false) {
		echo(Json::encode(
			[
				"result"  => "error",
				"message" => "Incorrect date string"
			]
		));
		die();
	} else {
		$arParams["TIMESTAMP"] = $d->getTimestamp();
	}

	$result = HolidayTable::add($arParams);
	if (!$result->isSuccess()) {
		$errors = implode("<br>", $result->getErrorMessages());
		echo(Json::encode(
			[
				"result"  => "error",
				"message" => $errors
			]
		));
		die();
	}
	include($baseFolder."/modules/".$moduleID."/admin/include/holidays.php");
	echo(Json::encode(
		[
			"result"  => "success",
			"body" => $body
		]
	));
}

if ($request->get('action')=='delete' && $APPLICATION->GetGroupRight($moduleID)=="W") {

	$result = HolidayTable::delete($arParams["CONTENT"]);
	if (!$result->isSuccess()) {
		$errors = implode("<br>", $result->getErrorMessages());
		echo(Json::encode(
			[
				"result"  => "error",
				"message" => $errors
			]
		));
		die();
	}
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/holidays.php");
	echo(Json::encode(
		[
			"result"  => "success",
			"body" => $body
		]
	));
}





