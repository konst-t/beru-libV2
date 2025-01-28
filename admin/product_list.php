<?
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

use \Iplogic\Beru\V2\ORM\ProductTable;
use \Iplogic\Beru\V2\Task;


$checkParams = [
	"PROFILE" => true,
];

include($baseFolder . "/modules/" . $moduleID . "/prolog.php");

Loc::loadMessages(__FILE__);

$PROFILE_ACCESS = \Iplogic\Beru\Access::getGroupRight("profile", $PROFILE_ID);

if( $MODULE_ACCESS == "D" || $PROFILE_ACCESS == "D" ) {
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
	die();
}


class ListEx extends Iplogic\Beru\Admin\TableList
{

	public function GroupActionEx($ID)
	{

		switch( $_REQUEST['action'] ) {
			case "set_price":
				Task::scheduleTaskComplex($ID, $_REQUEST['PROFILE_ID'], "PR", "SP");
				break;
			case "gr_hide":
				Task::scheduleTaskComplex($ID, $_REQUEST['PROFILE_ID'], "HP", "HS");
				break;
			case "gr_unhide":
				Task::scheduleTaskComplex($ID, $_REQUEST['PROFILE_ID'], "UP", "US");
				break;
		}
	}


	protected function filterMod()
	{
		$this->arFilter["PROFILE_ID"] = $_REQUEST['PROFILE_ID'];
	}

}


$arOpts = [
	[
		"NAME"    => "sku_id",
		"CAPTION" => "SKU ID",
		"FILTER"  => [
			"COMPARE" => "%",
		],
		"VIEW"    => [
			"AddViewField" => [
				"PARAM" => "iplogic_beru_product_detail.php?PROFILE_ID=" . $PROFILE_ID . "&ID=##id##&lang=" . LANG,
				"TYPE"  => "HREF",
			],
		],
	],
	[
		"NAME"    => "market_sku",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_MARKET_SKU"),
		"FILTER"  => [
			"COMPARE" => "%",
		],
	],
	[
		"NAME"    => "product_id",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_PRODUCT_ID"),
		"FILTER"  => [
			"COMPARE" => "%",
		],
	],
	[
		"NAME"    => "name",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_NAME"),
		"FILTER"  => [
			"COMPARE" => "%",
		],
	],
	[
		"NAME"       => "vendor",
		"CAPTION"    => Loc::getMessage("IPL_MA_CAPTION_VENDOR"),
		"FILTER"     => [
			"COMPARE" => "%",
		],
		"HEADER_KEY" => [
			"default" => false,
		],
	],
	[
		"NAME"    => "state",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_STATE"),
		"FILTER"  => [
			"VIEW"    => "select",
			"VALUES"  => [
				"reference"    => [
					// very old
					Loc::getMessage("IPL_MA_STATE_READY"),
					Loc::getMessage("IPL_MA_STATE_IN_WORK"),
					Loc::getMessage("IPL_MA_STATE_NEED_INFO"),
					Loc::getMessage("IPL_MA_STATE_NEED_CONTENT"),
					Loc::getMessage("IPL_MA_STATE_REJECTED"),
					Loc::getMessage("IPL_MA_STATE_SUSPENDED"),
					Loc::getMessage("IPL_MA_STATE_OTHER"),
					// old
					Loc::getMessage("IPL_MA_STATE_PUBLISHED"),
					Loc::getMessage("IPL_MA_STATE_CHECKING"),
					Loc::getMessage("IPL_MA_STATE_DISABLED_BY_PARTNER"),
					Loc::getMessage("IPL_MA_STATE_REJECTED_BY_MARKET"),
					Loc::getMessage("IPL_MA_STATE_DISABLED_AUTOMATICALLY"),
					Loc::getMessage("IPL_MA_STATE_CREATING_CARD"),
					Loc::getMessage("IPL_MA_STATE_NO_CARD"),
					Loc::getMessage("IPL_MA_STATE_NO_STOCKS"),
					// new
					Loc::getMessage("HAS_CARD_CAN_NOT_UPDATE"),
					Loc::getMessage("HAS_CARD_CAN_UPDATE"),
					Loc::getMessage("HAS_CARD_CAN_UPDATE_ERRORS"),
					Loc::getMessage("HAS_CARD_CAN_UPDATE_PROCESSING"),
					Loc::getMessage("NO_CARD_NEED_CONTENT"),
					Loc::getMessage("NO_CARD_MARKET_WILL_CREATE"),
					Loc::getMessage("NO_CARD_ERRORS"),
					Loc::getMessage("NO_CARD_PROCESSING"),
					Loc::getMessage("NO_CARD_ADD_TO_CAMPAIGN"),
				],
				"reference_id" => [
					// old
					"READY",
					"IN_WORK",
					"NEED_INFO",
					"NEED_CONTENT",
					"REJECTED",
					"SUSPENDED",
					"OTHER",
					// new
					"PUBLISHED",
					"CHECKING",
					"DISABLED_BY_PARTNER",
					"REJECTED_BY_MARKET",
					"DISABLED_AUTOMATICALLY",
					"CREATING_CARD",
					"NO_CARD",
					"NO_STOCKS",
				],
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"REPLACE" => [
			// very old
			"READY"                          => "<span style='color:#1cc43b;'>" .
				Loc::getMessage("IPL_MA_STATE_READY") . "</span>",
			"IN_WORK"                        => "<span style='color:#1d2bec;'>" .
				Loc::getMessage("IPL_MA_STATE_IN_WORK") . "</span>",
			"NEED_INFO"                      => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_NEED_INFO") . "</span>",
			"NEED_CONTENT"                   => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_NEED_CONTENT") . "</span>",
			"REJECTED"                       => "<span style='color:red;'>" . Loc::getMessage("IPL_MA_STATE_REJECTED") .
				"</span>",
			"SUSPENDED"                      => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_SUSPENDED") . "</span>",
			"OTHER"                          => "<span style='color:red;'>" . Loc::getMessage("IPL_MA_STATE_OTHER") .
				"</span>",
			// old
			"PUBLISHED"                      => "<span style='color:#1cc43b;'>" .
				Loc::getMessage("IPL_MA_STATE_PUBLISHED") . "</span>",
			"CHECKING"                       => "<span style='color:#1d2bec;'>" .
				Loc::getMessage("IPL_MA_STATE_CHECKING") . "</span>",
			"DISABLED_BY_PARTNER"            => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_DISABLED_BY_PARTNER") . "</span>",
			"REJECTED_BY_MARKET"             => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_REJECTED_BY_MARKET") . "</span>",
			"DISABLED_AUTOMATICALLY"         => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_DISABLED_AUTOMATICALLY") . "</span>",
			"CREATING_CARD"                  => "<span style='color:red;'>" .
				Loc::getMessage("IPL_MA_STATE_CREATING_CARD") . "</span>",
			"NO_CARD"                        => "<span style='color:red;'>" . Loc::getMessage("IPL_MA_STATE_NO_CARD") .
				"</span>",
			"NO_STOCKS"                      => "<span style='color:#ff8c00;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_STOCKS") . "</span>",
			// new
			"HAS_CARD_CAN_NOT_UPDATE"        => "<span style='color:#1cc43b;'>" .
				Loc::getMessage("IPL_MA_STATE_HAS_CARD_CAN_NOT_UPDATE") . "</span>",
			"HAS_CARD_CAN_UPDATE"            => "<span style='color:#1d2bec;'>" .
				Loc::getMessage("IPL_MA_STATE_HAS_CARD_CAN_UPDATE") . "</span>",
			"HAS_CARD_CAN_UPDATE_ERRORS"     => "<span style='color:#ff8c00;'>" .
				Loc::getMessage("IPL_MA_STATE_HAS_CARD_CAN_UPDATE_ERRORS") . "</span>",
			"HAS_CARD_CAN_UPDATE_PROCESSING" => "<span style='color:#ff8c00;'>" .
				Loc::getMessage("IPL_MA_STATE_HAS_CARD_CAN_UPDATE_PROCESSING") . "</span>",
			"NO_CARD_NEED_CONTENT"           => "<span style='color:#ff0000;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_CARD_NEED_CONTENT") . "</span>",
			"NO_CARD_MARKET_WILL_CREATE"     => "<span style='color:#ff0000;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_CARD_MARKET_WILL_CREATE") . "</span>",
			"NO_CARD_ERRORS"                 => "<span style='color:#ff0000;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_CARD_ERRORS") . "</span>",
			"NO_CARD_PROCESSING"             => "<span style='color:#ff0000;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_CARD_PROCESSING") . "</span>",
			"NO_CARD_ADD_TO_CAMPAIGN"        => "<span style='color:#ff0000;'>" .
				Loc::getMessage("IPL_MA_STATE_NO_CARD_ADD_TO_CAMPAIGN") . "</span>",
		],
		"VIEW"    => [
			"AddField" => [
				"PARAM" => "##state##",
			],
		],
	],
	[
		"NAME"    => "price",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_PRICE"),
		/*"FILTER" => [
			"COMPARE" => "%",
		],*/
	],
	[
		"NAME"    => "old_price",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_OLD_PRICE"),
	],
	[
		"NAME"    => "price_time",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_PRICE_TIME"),
	],
	[
		"NAME"    => "stock_fit",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_STOCK_FIT"),
	],
	[
		"NAME"    => "stock_time",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_STOCK_TIME"),
	],
	[
		"NAME"    => "hidden",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_HIDDEN"),
		"FILTER"  => [
			"VIEW"    => "select",
			"VALUES"  => [
				"reference"    => [
					Loc::getMessage("IPL_MA_HIDDEN"),
					Loc::getMessage("IPL_MA_SHOW"),
				],
				"reference_id" => [
					"Y",
					"N",
				],
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW"    => [
			"AddViewField" => [
				"PARAM" => "<div class=\"hidden marker ##hidden##\"></div>",
				"TYPE"  => "HTML",
			],
		],
	],
	[
		"NAME"       => "id",
		"CAPTION"    => "ID",
		"PROPERTY"   => "N",
		"UNIQ"       => "Y",
		"FILTER"     => [
			"VIEW" => "text",
		],
		"HEADER_KEY" => [
			"align"   => "right",
			"default" => false,
		],
	],
];

$arContext = [];
if( $PROFILE_ACCESS >= "W" ) {
	$arContext = [
		[
			"TEXT"  => Loc::getMessage("IPL_MA_REFRESH"),
			"LINK"  => "iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&refresh=Y&lang=" . LANG,
			"TITLE" => Loc::getMessage("IPL_MA_REFRESH_TITLE"),
		],
		[
			"TEXT"  => Loc::getMessage("IPL_MA_CACHE"),
			"LINK"  => "iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&cache=Y&lang=" . LANG,
			"TITLE" => Loc::getMessage("IPL_MA_CACHE_TITLE"),
		],
	];
}


$arItemContext = [
	[
		"TEXT"    => Loc::getMessage("IPL_MA_DETAIL"),
		"TITLE"   => Loc::getMessage("IPL_MA_DETAIL"),
		"ACTION"  => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_product_detail.php?PROFILE_ID=" . $PROFILE_ID . "&ID=##ID##&lang=" . LANG,
		],
		"DEFAULT" => true,
	],
	[
		"TEXT"   => Loc::getMessage("IPL_MA_SET_PRICE"),
		"TITLE"  => Loc::getMessage("IPL_MA_SET_PRICE"),
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&ID=##ID##&action=send_price&lang=" .
				LANG,
		],
	],
	[
		"TEXT"   => Loc::getMessage("IPL_MA_HIDE"),
		"TITLE"  => Loc::getMessage("IPL_MA_HIDE"),
		//"ICON" => "edit",
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&ID=##ID##&action=hide&lang=" . LANG,
		],
	],
	[
		"TEXT"   => Loc::getMessage("IPL_MA_UNHIDE"),
		"TITLE"  => Loc::getMessage("IPL_MA_UNHIDE"),
		//"ICON" => "edit",
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&ID=##ID##&action=show&lang=" . LANG,
		],
	],
];

$Messages = [
	"ACCESS_DENIED"      => Loc::getMessage("ACCESS_DENIED"),
	"DELETE_CONF"        => Loc::getMessage("IPL_MA_DELETE_CONF"),
	"SELECTED"           => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
	"CHECKED"            => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
	"DELETE"             => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
	"ACTIVATE"           => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"DEACTIVATE"         => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	"EDIT"               => Loc::getMessage("MAIN_ADMIN_LIST_EDIT"),
	"SAVE_ERROR_NO_ITEM" => Loc::getMessage("IPL_MA_SAVE_ERROR_NO_ITEM"),
	"SAVE_ERROR_UPDATE"  => Loc::getMessage("IPL_MA_SAVE_ERROR_UPDATE"),
	"SAVE_ERROR_DELETE"  => Loc::getMessage("IPL_MA_SAVE_ERROR_DELETE"),

];

$adminControl = new ListEx($moduleID);
$adminControl->POST_RIGHT = $PROFILE_ACCESS;
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $arContext;
$adminControl->arItemContextMenu = $arItemContext;
$adminControl->defaultBy = 'ID';
$adminControl->defaultOrder = "DESC";
$adminControl->gaCopy = "N";
$adminControl->gaDelete = "Y";
$adminControl->gaActivate = "N";
$adminControl->gaDeactivate = "N";
$adminControl->sTableClass = "\Iplogic\Beru\V2\ORM\ProductTable";
$adminControl->filterFormAction = "/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID;

$adminControl->arGroupActions = [
	"set_price" => Loc::getMessage("IPL_MA_SET_PRICE"),
	"gr_hide"   => Loc::getMessage("IPL_MA_HIDE"),
	"gr_unhide" => Loc::getMessage("IPL_MA_UNHIDE"),
];


$adminControl->initList("tbl_product");

$adminControl->EditAction();
$adminControl->GroupAction();


if(
	$PROFILE_ACCESS >= "W"
	&& $fatalErrors == ""
) {
	if( $request->get("refresh") == "Y" ) {
		exec(
			"wget -b -q -O - https://" . Option::get($moduleID, "domen") .
			"/bitrix/services/iplogic/mkpapi/getmpoffers.php"
		);
		LocalRedirect(
			"/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&mess=ok&lang=" . LANG
		);
	}
	if( $request->get("cache") == "Y" ) {
		$rsData = ProductTable::getList(
			['order' => $adminControl->arSort, 'filter' => $adminControl->arFilter, 'select' => ["ID"]]
		);
		while( $prod = $rsData->Fetch() ) {
			Task::scheduleTask($PROFILE_ID, "PU", 0, $prod["ID"]);
		}
		LocalRedirect(
			"/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&mess=ok&lang=" . LANG
		);
	}
	if( $request->get("action") == "hide" ) {
		Task::scheduleTaskComplex($request->get("ID"), $PROFILE_ID, "HP", "HS");
		LocalRedirect(
			"/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&mess=ok&lang=" . LANG
		);
	}
	if( $request->get("action") == "show" ) {
		Task::scheduleTaskComplex($request->get("ID"), $PROFILE_ID, "UP", "US");
		LocalRedirect(
			"/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&mess=ok&lang=" . LANG
		);
	}
	if( $request->get("action") == "send_price" ) {
		Task::scheduleTaskComplex($request->get("ID"), $PROFILE_ID, "PR", "SP");
		LocalRedirect(
			"/bitrix/admin/iplogic_beru_product_list.php?PROFILE_ID=" . $PROFILE_ID . "&mess=ok&lang=" . LANG
		);
	}
}


$rsData = ProductTable::getList(
	['order' => $adminControl->arSort, 'filter' => $adminControl->arFilter, 'select' => $adminControl->arSelect]
);
$adminControl->prepareData($rsData);

$APPLICATION->SetTitle(Loc::getMessage('IPL_MA_LIST_TITLE') . " #" . $PROFILE_ID . " (" . $arProfile["NAME"] . ")");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if( $fatalErrors != "" ) {
	CAdminMessage::ShowMessage($fatalErrors);
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
	die();
}

echo '<style>
.marker { width:12px; height:12px; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; }
.marker.availability.Y { background-color:#55d80e; }
.marker.availability.N { background-color:red; }
.marker.hidden.Y { background-color:#8f4d2c; }
.marker.hidden.N { background-color:none; }
</style>';


/* ok message */
if( $request->get("mess") === "ok" ) {
	CAdminMessage::ShowMessage(["MESSAGE" => Loc::getMessage("SAVED"), "TYPE" => "OK"]);
}


/* action errors */
if( count($adminControl->errors) ) {
	foreach( $adminControl->errors as $error ) {
		CAdminMessage::ShowMessage($error);
	}
}

$adminControl->renderList();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>