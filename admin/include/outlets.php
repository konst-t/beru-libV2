<?
use \Bitrix\Main\Localization\Loc;
use \Iplogic\Beru\OutletTable;

$arOutlets = [];
$rsOutlets = OutletTable::getList(["filter" => ["DELIVERY_ID" => $ID], "order" => ["CODE" => "ASC"]]);
while($arOutlet = $rsOutlets->fetch()) {
	$arOutlets[] = $arOutlet;
}

$body = "";
if (!count($arOutlets)) {
	$body = "<h3>".Loc::getMessage("IPL_MA_NO_OUTLETS")."</h3>";
}
else {
	foreach($arOutlets as $arOutlet) {
		$body .= '<div class="ipl-row">
	<div class="ipl-cell">'.$arOutlet["CODE"].'</div>
	<div class="ipl-cell">'.$arOutlet["NAME"].'</div>
	<div class="ipl-cell"><span class="adm-table-item-edit-wrap adm-table-item-edit-single"><a class="adm-table-btn-delete" href="javascript:deleteOutConfirm('.$arOutlet["ID"].');"></a></span></div>
</div>';
	}
}
