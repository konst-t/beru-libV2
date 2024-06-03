<?
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\Config\Option,
	\Bitrix\Main\Application,
	\Iplogic\Beru\ProfileTable as Profile;

$checkParams = [];

include($baseFolder."/modules/".$moduleID."/prolog.php");

Loc::loadMessages($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($MODULE_ACCESS == "D") {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

class TableFormEx extends Iplogic\Beru\Admin\TableForm {

	protected function addButtons() {
		$this->tabControl->Buttons(["disabled"=>($this->POST_RIGHT<"W"), "back_url"=>"/bitrix/admin/iplogic_beru_profile_list.php?lang=".LANG]);
	}

}

$ID = $request->get("ID");
if ($ID > 0){
	$arFields = Profile::getById($ID, true);
	if (!$arFields){
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
	}
}

CJSCore::Init(array("jquery"));

$LID = Iplogic\Beru\Admin\TableForm::getLID();

if( $request->isPost()
	&& ($request->get("save") != "" || $request->get("apply") != "")
	&& $MODULE_ACCESS >= "W"
	&& check_bitrix_sessid()
	&& $fatalErrors == ""
) {
	$POST_REQUEST = true;
}

$groupRightsBind = "profile";
$module_id = $moduleID;
if( $POST_REQUEST ) {
	$groupRightsAction = "POST";
	include("group_rights.php");
	$groupRightsAction = false;
}
ob_start();
include("group_rights.php");
$sRights = ob_get_contents();
ob_end_clean();

$aTabs = [
	["DIV" => "edit1", "TAB" => Loc::getMessage("IPL_MA_GENERAL"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("IPL_MA_GENERAL_TITLE")],
	["DIV" => "edit2", "TAB" => Loc::getMessage("IPL_MA_API"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("IPL_MA_API_TITLE")],
	["DIV" => "edit10", "TAB" => Loc::getMessage("IPL_MA_RIGHTS"), "TITLE" => Loc::getMessage("IPL_MA_RIGHTS_TITLE")],
];
$arOpts = [

	/* GENERAL */
	"ACTIVE" => [
		"TAB"       => "edit1", 
		"TYPE"      => "checkbox", 
		"DEFAULT"   => 'Y',
		"NAME"      => Loc::getMessage("IPL_MA_ACTIVE"),
	],
	"NAME" => [
		"TAB"       => "edit1", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_NAME"),
		"REQURIED"  => "Y",
	],
	"SITE" => [
		"TAB"       => "edit1", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_SITE"),
		"OPTIONS"   => "sites",
	],
	"SCHEME" => [
		"TAB"       => "edit1",
		"TYPE"      => "select",
		"DEFAULT"   => "FBS",
		"NAME"      => Loc::getMessage("IPL_MA_SCHEME"),
		"OPTIONS"   => [
			"FBS" => "FBS",
			"DBS" => "DBS",
		],
	],
	"SORT" => [
		"TAB"       => "edit1", 
		"TYPE"      => "text", 
		"DEFAULT"   => 100,
		"NAME"      => Loc::getMessage("IPL_MA_SORT"),
	],
	"IBLOCK_TYPE" => [
		"TAB"       => "edit1", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_IBLOCK_TYPE"),
		"OPTIONS"   => "iblock_types",
		"REQURIED"  => "Y",
	],
	"IBLOCK_ID" => [
		"TAB"       => "edit1", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_IBLOCK_ID"),
		"OPTIONS"   => "iblocks",
		"REQURIED"  => "Y",
	],
	"COMPANY" => [
		"TAB"       => "edit1", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_YML_COMPANY"),
	],
	"TAX_SYSTEM" => [
		"TAB"       => "edit1", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_TAX_SYSTEM"),
		"OPTIONS"   => [
			"NONE" 				=> Loc::getMessage("IPL_MA_NOT_USE"),
			"ECHN" 				=> Loc::getMessage("IPL_MA_ECHN"),
			"ENVD" 				=> Loc::getMessage("IPL_MA_ENVD"),
			"OSN" 				=> Loc::getMessage("IPL_MA_OSN"),
			"PSN" 				=> Loc::getMessage("IPL_MA_PSN"),
			"USN" 				=> Loc::getMessage("IPL_MA_USN"),
			"USN_MINUS_COST" 	=> Loc::getMessage("IPL_MA_USN_MINUS_COST"),
		],
	],
	"VAT" => [
		"TAB"       => "edit1", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_VAT"),
		"OPTIONS"   => [
			"NONE" => Loc::getMessage("IPL_MA_NOT_USE"),
			"NO_VAT"  => Loc::getMessage("IPL_MA_NO_VAT"),
			"VAT_0" => Loc::getMessage("IPL_MA_VAT_0"),
			"VAT_10" => Loc::getMessage("IPL_MA_VAT_10"),
			"VAT_20" => Loc::getMessage("IPL_MA_VAT_20"),
		],
	],


	/* API */
	"COMPAIN_ID" => [
		"TAB"       => "edit2", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_COMPAIN_ID"),
	],
	"BUSINESS_ID" => [
		"TAB"       => "edit2",
		"TYPE"      => "text",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_BUSINESS_ID") . " (business_id)",
	],
	"CLIENT_ID" => [
		"TAB"       => "edit2",
		"TYPE"      => "text",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_CLIENT_ID") . " OAuth",
	],
	"SEND_TOKEN" => [
		"TAB"       => "edit2", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_SEND_TOKEN"),
	],
	"GET_TOKEN" => [
		"TAB"       => "edit2", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_GET_TOKEN"),
	],
	"BASE_URL" => [
		"TAB"       => "edit2", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_BASE_URL"),
	],
	"STORE" => [
		"TAB"       => "edit2",
		"TYPE"      => "text",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STORE"),
	],

	"heading".randString(8) => [
		"TAB"       => "edit2", 
		"TYPE"      => "heading", 
		"TEXT"      => Loc::getMessage("IPL_MA_ORDER"),
	],
	"USER_ID" => [
		"TAB"       => "edit2", 
		"TYPE"      => "text", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_USER_ID"),
	],
	"DELIVERY" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_DELIVERY"),
		"OPTIONS"   => "delivery",
	],
	"PAYMENTS" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_PAYMENTS"),
		"OPTIONS"   => "payments",
	],
	"PERSON_TYPE" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_PERSON_TYPE"),
		"OPTIONS"   => "person_types",
	],

	"heading".randString(8) => [
		"TAB"       => "edit2", 
		"TYPE"      => "heading", 
		"TEXT"      => Loc::getMessage("IPL_MA_STATUSES"),
	],
	"html".randString(8) => [
		"TAB"       => "edit2", 
		"TYPE"      => "html", 
		"NAME"      => "",
		"HTML"      => Loc::getMessage("IPL_MA_START_STATUS"),
	],
	"S_NEW" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_NEW"),
		"OPTIONS"   => "order_statuses",
	],
	"html".randString(8) => [
		"TAB"       => "edit2",
		"TYPE"      => "html",
		"NAME"      => "",
		"HTML"      => Loc::getMessage("IPL_MA_GENERAL_STATUSES"),
	],
	"S_DELIVERED" => [
		"TAB"       => "edit2",
		"TYPE"      => "select",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_DELIVERED")."<br><i class=\"order_status\">DELIVERED</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_DELIVERY" => [
		"TAB"       => "edit2",
		"TYPE"      => "select",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_DELIVERY")."<br><i class=\"order_status\">DELIVERY</i>",
		"OPTIONS"   => "order_statuses",
	],
	"html".randString(8) => [
		"TAB"       => "edit2", 
		"TYPE"      => "html", 
		"NAME"      => "",
		"HTML"      => Loc::getMessage("IPL_MA_SHOP_STATUSES"),
	],
	"S_PROCESSING_READY_TO_SHIP" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PROCESSING_READY_TO_SHIP")."<br><i class=\"order_status\">PROCESSING READY_TO_SHIP</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_PROCESSING_SHIPPED" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PROCESSING_SHIPPED")."<br><i class=\"order_status\">PROCESSING SHIPPED</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_CANCELLED_SHOP_FAILED" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_CANCELLED_SHOP_FAILED")."<br><i class=\"order_status\">CANCELLED SHOP_FAILED</i>",
		"OPTIONS"   => "order_statuses",
	],
	"html".randString(8) => [
		"TAB"       => "edit2", 
		"TYPE"      => "html", 
		"NAME"      => "",
		"HTML"      => Loc::getMessage("IPL_MA_MKTPLS_STATUSES"),
	],
	"S_PROCESSING_STARTED" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PROCESSING_STARTED")."<br><i class=\"order_status\">PROCESSING STARTED</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_PROCESSING_COURIER_FOUND" => [
		"TAB"       => "edit2",
		"TYPE"      => "select",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PROCESSING_COURIER_FOUND")."<br><i class=\"order_status\">PROCESSING COURIER_FOUND</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_PROCESSING_COURIER_ARRIVED_TO_SENDER" => [
		"TAB"       => "edit2",
		"TYPE"      => "select",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PROCESSING_COURIER_ARRIVED_TO_SENDER")."<br><i class=\"order_status\">PROCESSING COURIER_ARRIVED_TO_SENDER</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_PICKUP" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_PICKUP")."<br><i class=\"order_status\">PICKUP</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_CANCELLED_BY_MARKETPLACE" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_CANCELLED_BY_MARKETPLACE")."<br><i class=\"order_status\">CANCELLED any_reason</i>",
		"OPTIONS"   => "order_statuses",
	],
	"S_UNPAID_WAITING_USER_INPUT" => [
		"TAB"       => "edit2", 
		"TYPE"      => "select", 
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_STATUS_UNPAID_WAITING_USER_INPUT")."<br><i class=\"order_status\">UNPAID WAITING_USER_INPUT</i>",
		"OPTIONS"   => "order_statuses",
	],
	"STATUSES" => [
		"TAB"       => "edit2", 
		"TYPE"      => "group", 
		"ITEMS"   => [
			"S_NEW",
			"S_PROCESSING_STARTED",
			"S_PROCESSING_READY_TO_SHIP",
			"S_PROCESSING_COURIER_FOUND",
			"S_PROCESSING_SHIPPED",
			"S_CANCELLED_SHOP_FAILED",
			"S_DELIVERED",
			"S_DELIVERY",
			"S_PICKUP",
			"S_CANCELLED_BY_MARKETPLACE",
			"S_UNPAID_WAITING_USER_INPUT",
			"S_PROCESSING_COURIER_ARRIVED_TO_SENDER",
		],
	],

	/* RIGHTS */
	"html".randString(8) => [
		"TAB"       => "edit10",
		"TYPE"      => "html",
		"NAME"      => "",
		"HTML"      => $sRights,
	],

];

if($arFields["SCHEME"] == "DBS") {
	$arOpts["PAYMENT_METHODS"] = [
		"TAB"       => "edit1",
		"TYPE"      => "select",
		"MULTIPLE"  => "Y",
		"SIZE"      => 8,
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_PAYMENT_METHODS"),
		"OPTIONS"   => [
			"YANDEX" => Loc::getMessage("IPL_MA_PM_YANDEX"),
			"APPLE_PAY" => Loc::getMessage("IPL_MA_PM_APPLE_PAY"),
			"GOOGLE_PAY" => Loc::getMessage("IPL_MA_PM_GOOGLE_PAY"),
			"TINKOFF_CREDIT" => Loc::getMessage("IPL_MA_PM_TINKOFF_CREDIT"),
			"TINKOFF_INSTALLMENTS" => Loc::getMessage("IPL_MA_PM_TINKOFF_INSTALLMENTS"),
			"SBP" => Loc::getMessage("IPL_MA_PM_SBP"),
			"CARD_ON_DELIVERY" => Loc::getMessage("IPL_MA_PM_CARD_ON_DELIVERY"),
			"CASH_ON_DELIVERY" => Loc::getMessage("IPL_MA_PM_CASH_ON_DELIVERY"),
		],
	];
}


$aMenu = [
	[
		"TEXT"  => Loc::getMessage("IPL_MA_LIST"),
		"TITLE" => Loc::getMessage("IPL_MA_LIST_TITLE"),
		"LINK"  => "iplogic_beru_profile_list.php?lang=".LANG,
		"ICON"  => "btn_list",
	]
];

if($ID>0)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = [
		"TEXT"  => Loc::getMessage("IPL_MA_ACCORDANCES"),
		"TITLE" => Loc::getMessage("IPL_MA_ACCORDANCES_TITLE"),
		"LINK"  => "iplogic_beru_accordances_edit.php?PROFILE_ID=".$ID."&lang=".LANG,
	];

	if ($arFields["SCHEME"] == "DBS") {
		$aMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_DELIVERIES"),
			"TITLE" => Loc::getMessage("IPL_MA_DELIVERIES_TITLE"),
			"LINK"  => "iplogic_beru_delivery.php?PROFILE_ID=".$ID."&lang=".LANG,
		];
	}

}


$Messages = [
	"NOT_CHOSEN" => Loc::getMessage("NOT_CHOSEN"),
	"ALL" => Loc::getMessage("ALL"),
];


$adminControl = new TableFormEx($moduleID);
$adminControl->POST_RIGHT = $MODULE_ACCESS;
$adminControl->arTabs = $aTabs;
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $aMenu;
$adminControl->initDetailPage();


$adminControl->setFields($arFields);

if( $POST_REQUEST ) {

	$adminControl->getRequestData();

	if( !count($adminControl->errors) ) {
		$arFields = $adminControl->extractQueryValues();
		if($ID > 0) {
			$res = Profile::update($ID,$arFields);
			if (is_array($res) && isset($res["error"])) {
				$adminControl->errors[] = implode("<br>",$res["error"]);
				$res = false;
			}
		}
		else {
			$ID = Profile::add($arFields);
			$res = $ID;
			if (isset($res["error"])) {
				$adminControl->errors[] = implode("<br>",$res["error"]);
				$res = false;
			}
		}
		if($res) {
			if ($request->get("apply") != ""){
				LocalRedirect("/bitrix/admin/iplogic_beru_profile_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				LocalRedirect("/bitrix/admin/iplogic_beru_profile_list.php?lang=".LANG);
			}
		}
	}

}

$APPLICATION->SetTitle((
	$ID > 0 ?
		Loc::getMessage("IPL_MA_PROFILE_EDIT_TITLE")." ".$arFields["NAME"]." #".$ID :
		Loc::getMessage("IPL_MA_PROFILE_NEW_TITLE")
));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo "<style>.order_status{color:#5e5e5e;font-size:12px;}</style>";

if ($fatalErrors != ""){
	CAdminMessage::ShowMessage($fatalErrors);
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

if($request->get("mess") === "ok")
	CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("SAVED"), "TYPE"=>"OK"));

elseif( count($adminControl->errors) ) {
	foreach($adminControl->errors as $error) {
		CAdminMessage::ShowMessage($error);
	}
}

$adminControl->buildPage();

$adminControl->getIBlockChooseScript("IBLOCK_ID", "IBLOCK_TYPE", "SITE");


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>