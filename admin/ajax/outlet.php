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
use \Iplogic\Beru\OutletTable;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$moduleID.'/admin/delivery_edit.php');

CJSCore::Init(array("jquery"));

/* fatal errors check, creat control object and get table data */
$checkParams = [
	"PROFILE" => true,
];

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/prolog.php");

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
	//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	ShowError($fatalErrors);
	//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if ($request->get('action')=='save' && $APPLICATION->GetGroupRight($moduleID)=="W") {
	$arParams = $request->get('params');

	if ($arParams["NAME"] == "") {
		echo(Json::encode(
			[
				"result"  => "error",
				"message" => Loc::getMessage("IPL_MA_ERROR_NAME")
			]
		));
		die();
	}
	if ($arParams["CODE"] == "") {
		echo(Json::encode(
			[
				"result"  => "error",
				"message" => Loc::getMessage("IPL_MA_ERROR_CODE")
			]
		));
		die();
	}

	$arParams["PROFILE_ID"] = $request->get('PROFILE_ID');
	$arParams["DELIVERY_ID"] = $request->get('ID');

	$result = OutletTable::add($arParams);
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
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/outlets.php");
	echo(Json::encode(
		[
			"result"  => "success",
			"body" => $body
		]
	));
}
else {
	?><div class="ipl-error-mes"></div>
	<table class="adm-detail-content-table edit-table">
		<tr>
			<td style="width:40%;" class="adm-detail-content-cell-l">
				<b><?=Loc::getMessage("IPL_MA_NAME")?></b>
			</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="NAME" value="">
			</td>
		</tr>
		<tr>
			<td style="width:40%;" class="adm-detail-content-cell-l">
				<b><?=Loc::getMessage("IPL_MA_CODE")?></b>
			</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="CODE" value="">
			</td>
		</tr>
	</table>
	<script>
		//
	</script>
	<?
}



