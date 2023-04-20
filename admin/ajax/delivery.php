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

$baseFolder = realpath(__DIR__ . "/../../../..");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile($baseFolder."/modules/".$moduleID.'/admin/delivery_edit.php');

CJSCore::Init(array("jquery"));

/* fatal errors check, creat control object and get table data */
$checkParams = [
	"PROFILE" => true,
];

include($baseFolder."/modules/".$moduleID."/prolog.php");

if ($ID > 0){
	$arFields = DeliveryTable::getRowById($ID);
	if (!$arFields){
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS_NOT_FOUND")."<br>";
	}
	$arFields["PAYMENT_METHODS"] = unserialize($arFields["PAYMENT_METHODS"]);
}
else {
	$arFields = [
		"ACTIVE" => "Y",
		"SORT" => 100,
	];
}

if ($fatalErrors != "") {
	ShowError($fatalErrors);
	//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

if ($request->get('action')=='save' && $MODULE_ACCESS >= "W") {
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
	$arParams["PROFILE_ID"] = $request->get('PROFILE_ID');
	if ($ID > 0){
		$result = DeliveryTable::update($ID, $arParams);
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
		$arFields = DeliveryTable::getRowById($ID);
		include($baseFolder."/modules/".$moduleID."/admin/include/delivery_info.php");
		echo(Json::encode(
			[
				"result"  => "success",
				"body" => $body
			]
		));
	}
	else {
		$result = DeliveryTable::add($arParams);
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
		$ID = $result->getId();
		echo(Json::encode(
			[
				"result"  => "redirect",
				"url" => "iplogic_beru_delivery_edit.php?PROFILE_ID=".$request->get('PROFILE_ID')."&ID=".$ID."&lang=".LANG
			]
		));
	}
}
else {
	?><div class="ipl-error-mes"></div>
<table class="adm-detail-content-table edit-table">
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_NAME")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<input type="text" name="NAME" value="<?=$arFields["NAME"]?>">
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_TYPE")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="TYPE">
				<option value="DELIVERY" <?=$arFields["TYPE"]=="DELIVERY" ? ' selected="selected"' : ""?>><?=Loc::getMessage("IPL_MA_TYPE_DELIVERY")?></option>
				<option value="PICKUP" <?=$arFields["TYPE"]=="PICKUP" ? ' selected="selected"' : ""?>><?=Loc::getMessage("IPL_MA_TYPE_PICKUP")?></option>
				<option value="POST" <?=$arFields["TYPE"]=="POST" ? ' selected="selected"' : ""?>><?=Loc::getMessage("IPL_MA_TYPE_POST")?></option>
				<option value="DIGITAL" <?=$arFields["TYPE"]=="DIGITAL" ? ' selected="selected"' : ""?>><?=Loc::getMessage("IPL_MA_TYPE_DIGITAL")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_ACTIVE")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<input type="checkbox" name="ACTIVE" value="Y"<?=$arFields["ACTIVE"]=="Y" ? ' checked="checked"' : ""?>>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_SORT")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<input type="text" name="SORT" value="<?=$arFields["SORT"]?>">
		</td>
	</tr>
	<tr class="payment-allow-row">
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_PAYMENT_ALLOW")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<input type="checkbox" name="PAYMENT_ALLOW" value="Y"<?=$arFields["PAYMENT_ALLOW"]=="Y" ? ' checked"' : ""?>>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_DAY_FROM")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="DAY_FROM">
				<? for($i = 0; $i < 32; $i++) { ?>
				<option value="<?=$i?>" <?=($arFields["DAY_FROM"]==$i ? ' selected="selected"' : "")?>><?=Loc::getMessage("IPL_MA_DF_".$i)?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_DAY_TO")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="DAY_TO">
				<? for($i = 0; $i < 32; $i++) { ?>
					<option value="<?=$i?>" <?=($arFields["DAY_TO"]==$i ? ' selected="selected"' : "")?>><?=Loc::getMessage("IPL_MA_DF_".$i)?></option>
				<? } ?>
			</select>
		</td>
	</tr>
</table>
<script>
	var payAllowHide = function() {
		if ($('select[name=TYPE]').val() == "POST") {
			$(".payment-allow-row").show();
		}
		else {
			$(".payment-allow-row").hide();
		}
	}
	$(document).on('change', $('select[name=TYPE]'), function() { payAllowHide(); });
	payAllowHide()
</script>
<?
}


