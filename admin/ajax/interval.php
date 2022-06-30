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
use \Iplogic\Beru\IntervalTable;

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
	$arParams["PROFILE_ID"] = $request->get('PROFILE_ID');
	$arParams["DELIVERY_ID"] = $request->get('ID');

	$result = IntervalTable::add($arParams);
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
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/intervals.php");
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
			<b><?=Loc::getMessage("IPL_MA_DAY")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="DAY">
				<? for($i = 1; $i < 8; $i++) { ?>
					<option value="<?=$i?>"><?=Loc::getMessage("IPL_MA_DAY_".$i)?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_TIME_FROM")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="TIME_FROM">
				<option value="00:00">00:00</option>
				<option value="01:00">01:00</option>
				<option value="02:00">02:00</option>
				<option value="03:00">03:00</option>
				<option value="04:00">04:00</option>
				<option value="05:00">05:00</option>
				<option value="06:00">06:00</option>
				<option value="07:00">07:00</option>
				<option value="08:00">08:00</option>
				<option value="09:00" selected="selected">09:00</option>
				<option value="10:00">10:00</option>
				<option value="11:00">11:00</option>
				<option value="12:00">12:00</option>
				<option value="13:00">13:00</option>
				<option value="14:00">14:00</option>
				<option value="15:00">15:00</option>
				<option value="16:00">16:00</option>
				<option value="17:00">17:00</option>
				<option value="18:00">18:00</option>
				<option value="19:00">19:00</option>
				<option value="20:00">20:00</option>
				<option value="21:00">21:00</option>
				<option value="22:00">22:00</option>
				<option value="23:00">23:00</option>
				<option value="23:59">23:59</option>
			</select>
		</td>
	</tr>
	<tr>
		<td style="width:40%;" class="adm-detail-content-cell-l">
			<b><?=Loc::getMessage("IPL_MA_TIME_TO")?></b>
		</td>
		<td class="adm-detail-content-cell-r">
			<select name="TIME_TO">
				<option value="00:00">00:00</option>
				<option value="01:00">01:00</option>
				<option value="02:00">02:00</option>
				<option value="03:00">03:00</option>
				<option value="04:00">04:00</option>
				<option value="05:00">05:00</option>
				<option value="06:00">06:00</option>
				<option value="07:00">07:00</option>
				<option value="08:00">08:00</option>
				<option value="09:00">09:00</option>
				<option value="10:00">10:00</option>
				<option value="11:00">11:00</option>
				<option value="12:00">12:00</option>
				<option value="13:00">13:00</option>
				<option value="14:00">14:00</option>
				<option value="15:00">15:00</option>
				<option value="16:00">16:00</option>
				<option value="17:00">17:00</option>
				<option value="18:00" selected="selected">18:00</option>
				<option value="19:00">19:00</option>
				<option value="20:00">20:00</option>
				<option value="21:00">21:00</option>
				<option value="22:00">22:00</option>
				<option value="23:00">23:00</option>
				<option value="23:59">23:59</option>
			</select>
		</td>
	</tr>
</table>
<script>
	//
</script>
<?
}


