<?
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Main\Localization\Loc,
	Iplogic\Beru\ProfileTable,
	Iplogic\Beru\TaskTable;

$checkParams = [];

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/prolog.php");

Loc::loadMessages(__FILE__);


/* add extends class if needed */


/* get service data and preforms*/
$rsProfiles = ProfileTable::GetList();
while($arProfile = $rsProfiles->Fetch()){
	$arProfiles[$arProfile["ID"]] = $arProfile["NAME"]." [".$arProfile["ID"]."]";
	$profile_reference[] = $arProfile["NAME"]." [".$arProfile["ID"]."]";
	$profile_reference_id[] = $arProfile["ID"];
}



/* opts */
$arOpts = [
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
		"NAME" => "human_time",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_TIME"),
		"VIEW" => [
		],
	],
	[
		"NAME" => "type",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_TYPE"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => [
					Loc::getMessage("IPL_MA_TYPE_PU"),
					Loc::getMessage("IPL_MA_TYPE_DU"),
					Loc::getMessage("IPL_MA_TYPE_PR"),
					Loc::getMessage("IPL_MA_TYPE_SP"),
					Loc::getMessage("IPL_MA_TYPE_HP"),
					Loc::getMessage("IPL_MA_TYPE_UP"),
					Loc::getMessage("IPL_MA_TYPE_RQ"),
					Loc::getMessage("IPL_MA_TYPE_HS"),
					Loc::getMessage("IPL_MA_TYPE_US"),
					Loc::getMessage("IPL_MA_TYPE_CT"),
				],
				"reference_id" => [
					"PU",
					"DU",
					"PR",
					"SP",
					"HP",
					"UP",
					"RQ",
					"HS",
					"US",
					"CT",
				]
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW" => [
			"AddField" => [
				"PARAM" => "##type##",
			],
		],
		"REPLACE" => [
			"PU" => Loc::getMessage("IPL_MA_TYPE_PU"),
			"DU" => Loc::getMessage("IPL_MA_TYPE_DU"),
			"PR" => Loc::getMessage("IPL_MA_TYPE_PR"),
			"SP" => Loc::getMessage("IPL_MA_TYPE_SP"),
			"HP" => Loc::getMessage("IPL_MA_TYPE_HP"),
			"UP" => Loc::getMessage("IPL_MA_TYPE_UP"),
			"RQ" => Loc::getMessage("IPL_MA_TYPE_RQ"),
			"HS" => Loc::getMessage("IPL_MA_TYPE_HS"),
			"US" => Loc::getMessage("IPL_MA_TYPE_US"),
			"CT" => Loc::getMessage("IPL_MA_TYPE_CT"),
		]
	],
	[
		"NAME" => "state",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_STATE"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => [
					Loc::getMessage("IPL_MA_STATE_WT"),
					Loc::getMessage("IPL_MA_STATE_IW"),
				],
				"reference_id" => [
					"WT",
					"IW",
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
		"NAME" => "entity_id",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_ENTITY"),
		"VIEW" => [
		],
		"FILTER" => [
			"VIEW" => "text",
		],
	],
	[
		"NAME" => "trying",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_TRYING"),
		"VIEW" => [
		],
	],
	[
		"NAME" => "id",
		"CAPTION" => "ID",
		"PROPERTY" => "N",
		"UNIQ" => "Y",
		"HEADER_KEY" => [
			"align" => "right",
			"default" => false,
		],
	],
];




/* context menu */
$arContextMenu = [
	[
		"TEXT"=>Loc::getMessage("IPL_MA_CLEAN"),
		"LINK"=>"javascript:deleteConfirm();",
		"TITLE"=>Loc::getMessage("IPL_MA_CLEAN_TITLE"),
		"ICON"=>"btn_delete",
	],
];



/* context menu for each line */
$arItemContextMenu = [
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
	"SELECTED" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
	"CHECKED" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
	"DELETE" => Loc::getMessage("MAIN_ADMIN_LIST_DELETE"),
	"ACTIVATE" => Loc::getMessage("MAIN_ADMIN_LIST_ACTIVATE"),
	"DEACTIVATE" => Loc::getMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	"EDIT" => Loc::getMessage("MAIN_ADMIN_LIST_EDIT"),
	"SAVE_ERROR_NO_ITEM" => Loc::getMessage("IPL_MA_SAVE_ERROR_NO_ITEM"),
	"SAVE_ERROR_DELETE" => Loc::getMessage("IPL_MA_SAVE_ERROR_DELETE"),
	"DELETE_CONF" => Loc::getMessage("IPL_MA_DELETE_CONF"),
];



/* prepare control object */
$adminControl = new Iplogic\Beru\Admin\TableList($moduleID);
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $arContextMenu;
$adminControl->arItemContextMenu = $arItemContextMenu;
$adminControl->defaultBy = 'UNIX_TIMESTAMP';
$adminControl->defaultOrder = "DESC";
$adminControl->gaCopy = "N";
$adminControl->gaActivate = "N";
$adminControl->gaDeactivate = "N";
$adminControl->sTableClass = "\Iplogic\Beru\TaskTable";


/* nonstandard group actions */
$adminControl->arGroupActions = [];



if ($adminControl->POST_RIGHT == "D") $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));


/* exec actions */
$adminControl->initList("tbl_iplogic_beru_tasks");
$adminControl->EditAction();
$adminControl->GroupAction();
if ($request->get("clean") == "Y"){
	if(!TaskTable::clear()){
		$adminControl->errors[] = Loc::getMessage("IPL_MA_SAVE_ERROR_DELETE");
	}
	else {
		LocalRedirect("/bitrix/admin/iplogic_beru_tasks.php?lang=".LANG);
	}
}


/* get list and put it in control object */
$rsData = TaskTable::getList(['order' => $adminControl->arSort, 'filter' => $adminControl->arFilter, 'select' => $adminControl->arSelect]);
$adminControl->prepareData($rsData);



/* starting output */
$APPLICATION->SetTitle(Loc::getMessage('IPL_MA_LIST_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

echo '<style>
.state_marker { width:12px; height:12px; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; }
.state_marker.WT { background-color:#55d80e; }
.state_marker.IW { background-color:#ffae00; }
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

echo ("<script>
	function deleteConfirm() {
		if (window.confirm('".Loc::getMessage("IPL_MA_CLEAR_CONFIRM")."')) {
			window.location.href='iplogic_beru_tasks.php?clean=Y&lang=".LANG."';
		}
	}
</script>");

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>
