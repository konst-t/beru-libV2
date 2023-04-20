<?
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Main\Localization\Loc,
	Iplogic\Beru\ProfileTable,
	Iplogic\Beru\ErrorTable;

$checkParams = [];

include($baseFolder."/modules/".$moduleID."/prolog.php");

Loc::loadMessages(__FILE__);

if ($MODULE_ACCESS == "D") {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}


/* add extends class if needed */
class ListEx extends \Iplogic\Beru\Admin\TableList
{

	public function GroupActionEx($ID) {

		switch($_REQUEST['action']) {
			case "read":
				$arFields = ["STATE"=>"RD"];
				if(!$this->update($ID, $arFields)) {
					$this->obList->AddGroupError(Loc::getMessage("IPL_MA_SAVE_ERROR_UPDATE"), $ID);
				}
			break;
		}
	}


	protected function filterMod() {
		if (isset($this->arFilter[">=UNIX_TIMESTAMP"])) {
			if ($udate = $this->getUnixDate($this->arFilter[">=UNIX_TIMESTAMP"]))
				$this->arFilter[">=UNIX_TIMESTAMP"] = $udate;
			else
				unset ($this->arFilter[">=UNIX_TIMESTAMP"]);
		}
		if (isset($this->arFilter["<=UNIX_TIMESTAMP"])) {
			if ($udate = $this->getUnixDate($this->arFilter["<=UNIX_TIMESTAMP"],true))
				$this->arFilter["<=UNIX_TIMESTAMP"] = $udate;
			else
				unset ($this->arFilter["<=UNIX_TIMESTAMP"]);
		}
	}


}


/* get service data and preforms*/
$rsProfiles = ProfileTable::getList();
while($arProfile = $rsProfiles->Fetch()){
	$arProfiles[$arProfile["ID"]] = $arProfile["NAME"]." [".$arProfile["ID"]."]";
	$profile_reference[] = $arProfile["NAME"]." [".$arProfile["ID"]."]";
	$profile_reference_id[] = $arProfile["ID"];
}



/* opts */
$arOpts = [
	[
		"NAME" => "state",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_STATE"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => [
					Loc::getMessage("IPL_MA_STATE_NEW"),
					Loc::getMessage("IPL_MA_STATE_READ"),
				],
				"reference_id" => [
					"NW",
					"RD",
				]
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW" => [
			"AddViewField" => [
				"PARAM" => "<div class=\"state_marker ##state##\"></div>",
				"TYPE" => "HTML",
			],
		],
	],
	[
		"NAME" => "human_time",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_TIME"),
	],
	[
		"NAME" => "unix_timestamp",
		"VIEW" => "hidden",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_TIME"),
		"FILTER" => [
			"VIEW" => "date-from-to",
		],
	],
	[
		"NAME" => "error",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_ERROR"),
		"FILTER" => [
			"COMPARE" => "?",
		],
		"VIEW" => [
			"AddViewField" => [
				"PARAM" => "/bitrix/admin/iplogic_beru_error_detail.php?ID=##id##&lang=".LANGUAGE_ID,
				"TYPE" => "HREF",
			],
		],
	],
	[
		"NAME" => "profile_id",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_PROFILE"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => $profile_reference,
				"reference_id" => $profile_reference_id
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW" => [
			"AddViewField" => [
				"PARAM" => "/bitrix/admin/iplogic_beru_profile_edit.php?ID=##profile_id_real##&lang=".LANGUAGE_ID,
				"TYPE" => "HREF",
			],
		],
		"REPLACE" => $arProfiles,
	],
	[
		"NAME" => "id",
		"CAPTION" => "ID",
		"PROPERTY" => "N", 
		"UNIQ" => "Y",
		"FILTER" => [
			"VIEW" => "text", 
		],
		"HEADER_KEY" => [
			"align" => "right", 
			"default" => false, 
		],
	],
];




/* context menu */
$arContextMenu = [];
if($MODULE_ACCESS >= "W") {
	$arContextMenu = [
		[
			"TEXT"  => Loc::getMessage("IPL_MA_CLEAN_READ"),
			"LINK"  => "iplogic_beru_error_list.php?clean_read=Y&lang=" . LANG,
			"TITLE" => Loc::getMessage("IPL_MA_CLEAN_READ_TITLE"),
			"ICON"  => "btn_delete",
		],
		[
			"TEXT"  => Loc::getMessage("IPL_MA_ALL_READ"),
			"LINK"  => "iplogic_beru_error_list.php?all_read=Y&lang=" . LANG,
			"TITLE" => Loc::getMessage("IPL_MA_ALL_READ_TITLE"),
		]
	];
}



/* context menu for each line */
$arItemContextMenu = [
	[
		"TEXT" => Loc::getMessage("IPL_MA_DETAIL"),
		"TITLE" => Loc::getMessage("IPL_MA_DETAIL"),
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_error_detail.php?ID=##ID##&lang=".LANG,
		],
		"DEFAULT" => "Y",
	],
	[
		"TEXT" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
		"TITLE" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
		"ICON" => "delete",
		"ACTION" => [
			"TYPE" => "DELETE",
		]
	],
];



/* lang messages in classes */
$Messages = [
	"ACCESS_DENIED" => Loc::getMessage("ACCESS_DENIED"),
	"SELECTED" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
	"CHECKED" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
	"DELETE" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"), 
	"ACTIVATE" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"), 
	"DEACTIVATE" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"), 
	"EDIT" => Loc::getMessage("MAIN_ADMIN_LIST_EDIT"), 
	"SAVE_ERROR_NO_ITEM" => Loc::getMessage("IPL_MA_SAVE_ERROR_NO_ITEM"),
	"SAVE_ERROR_UPDATE" => Loc::getMessage("IPL_MA_SAVE_ERROR_UPDATE"),
	"SAVE_ERROR_DELETE" => Loc::getMessage("IPL_MA_SAVE_ERROR_DELETE"),
	"DELETE_CONF" => Loc::getMessage("IPL_MA_DELETE_CONF"),
];



/* prepare control object */
$adminControl = new ListEx($moduleID);
$adminControl->POST_RIGHT = $MODULE_ACCESS;
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $arContextMenu;
$adminControl->arItemContextMenu = $arItemContextMenu;
$adminControl->defaultBy = 'UNIX_TIMESTAMP';
$adminControl->defaultOrder = "DESC";
$adminControl->gaCopy = "N";
$adminControl->gaActivate = "N";
$adminControl->gaDeactivate = "N";
$adminControl->sTableClass = "\Iplogic\Beru\ErrorTable";


/* nonstandard group actions */
$adminControl->arGroupActions = [
	"read" => Loc::getMessage("IPL_MA_READ")
];



/* exec actions */
$adminControl->initList("tbl_iplogic_beru_errors");
$adminControl->EditAction();
$adminControl->GroupAction();
if ($request->get("clean_read") == "Y" && $MODULE_ACCESS >= "W"){
	if(!ErrorTable::clearRead()){
		$adminControl->errors = Loc::getMessage("IPL_MA_SAVE_ERROR_DELETE");
	}
	else {
		LocalRedirect("/bitrix/admin/iplogic_beru_error_list.php?lang=".LANG);
	}
}
elseif ($request->get("clean_read") == "Y" && $MODULE_ACCESS < "W"){
	$adminControl->errors[] = Loc::getMessage("ACCESS_DENIED");
}
if ($request->get("all_read") == "Y" && $MODULE_ACCESS >= "W"){
	if(!ErrorTable::allRead()){
		$adminControl->errors = Loc::getMessage("IPL_MA_SAVE_ERROR_UPDATE");
	}
	else {
		LocalRedirect("/bitrix/admin/iplogic_beru_error_list.php?lang=".LANG);
	}
}
elseif ($request->get("all_read") == "Y" && $MODULE_ACCESS < "W"){
	$adminControl->errors[] = Loc::getMessage("ACCESS_DENIED");
}


/* get list and put it in control object */
$rsData = ErrorTable::getList(['order' => $adminControl->arSort, 'filter' => $adminControl->arFilter, 'select' => $adminControl->arSelect]);
$adminControl->prepareData($rsData);



/* starting output */
$APPLICATION->SetTitle(Loc::getMessage('IPL_MA_LIST_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo '<style>
.state_marker { width:12px; height:12px; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; }
.state_marker.NW { background-color:red; }
.state_marker.RD { background-color:#ccc; }
</style>';


/* ok message */
if($request->get("mess") === "ok")
	CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("SAVED"), "TYPE"=>"OK"));


/* action errors */
if( count($adminControl->errors) ) {
	foreach($adminControl->errors as $error) {
		CAdminMessage::ShowMessage($error);
	}
}

$adminControl->renderList();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>