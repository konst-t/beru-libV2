<?
use \Bitrix\Main\Localization\Loc;

$paymentMethods = "";
$arPaymentMethods = [];
foreach(unserialize($arFields["PAYMENT_METHODS"]) as $method) {
	$arPaymentMethods[] = Loc::getMessage("IPL_MA_PM_".$method);
}
$paymentMethods = implode("<br>", $arPaymentMethods);

$body = '<div class="ipl-table">
	<div class="ipl-table-row">
		<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_NAME").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.$arFields["NAME"].'
		</div>
	</div>
	<div class="ipl-table-row">
		<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_TYPE").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.Loc::getMessage("IPL_MA_TYPE_".$arFields["TYPE"]).'
		</div>
	</div>
	<div class="ipl-table-row">
		<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_ACTIVE").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.($arFields["ACTIVE"] == "Y" ? Loc::getMessage("IPL_MA_YES") : Loc::getMessage("IPL_MA_NO")).'
		</div>
	</div>
	<div class="ipl-table-row">
		<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_SORT").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.$arFields["SORT"].'
		</div>
	</div>
	<div class="ipl-table-row">';
if($arFields["TYPE"] == "POST"){
	$body .= '<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_PAYMENT_ALLOW").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.($arFields["PAYMENT_ALLOW"] == "Y" ? Loc::getMessage("IPL_MA_YES") : Loc::getMessage("IPL_MA_NO")).'
		</div>
	</div>
	<div class="ipl-table-row">';
}
$body .= '<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_DAY_FROM").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.Loc::getMessage("IPL_MA_DF_".$arFields["DAY_FROM"]).'
		</div>
	</div>
	<div class="ipl-table-row">
		<div class="ipl-table-cell left">
			<b>'.Loc::getMessage("IPL_MA_DAY_TO").'</b>
		</div>
		<div class="ipl-table-cell right">
			'.Loc::getMessage("IPL_MA_DF_".$arFields["DAY_TO"]).'
		</div>
	</div>
</div>';