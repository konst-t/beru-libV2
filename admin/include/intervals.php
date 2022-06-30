<?
use \Bitrix\Main\Localization\Loc;
use \Iplogic\Beru\IntervalTable;

$arIntervals = [];
$rsIntervals = IntervalTable::getList(["filter" => ["DELIVERY_ID" => $ID], "order" => ["DAY" => "ASC"]]);
while($arInterval = $rsIntervals->fetch()) {
	$arIntervals[] = $arInterval;
}

$body = "";
if (!count($arIntervals)) {
	$body = "<h3>".Loc::getMessage("IPL_MA_NO_INTERVALS")."</h3>";
}
else {
	foreach($arIntervals as $arInterval) {
		$body .= '<div class="ipl-row">
	<div class="ipl-cell">'.Loc::getMessage("IPL_MA_DAY_".$arInterval["DAY"]).'</div>
	<div class="ipl-cell">'.$arInterval["TIME_FROM"].' - '.$arInterval["TIME_TO"].'</div>
	<div class="ipl-cell"><span class="adm-table-item-edit-wrap adm-table-item-edit-single"><a class="adm-table-btn-delete" href="javascript:deleteIntConfirm('.$arInterval["ID"].');"></a></span></div>
</div>';
	}
}