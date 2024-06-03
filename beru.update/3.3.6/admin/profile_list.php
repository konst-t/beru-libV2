<?
$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Main\Localization\Loc,
	Iplogic\Beru\ProfileTable;

$checkParams = [];

include($baseFolder."/modules/".$moduleID."/prolog.php");

Loc::loadMessages(__FILE__);

if ($MODULE_ACCESS == "D") {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

$arOpts = [
	[
		"NAME" => "name",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_NAME"),
		"FILTER" => [
			"COMPARE" => "?",
		],
		"VIEW" => [
			"AddInputField" => [
				"PARAM" => array("size"=>20)
			],
			"AddViewField" => [
				"PARAM" => "/bitrix/admin/iplogic_beru_profile_edit.php?ID=##id##&lang=".LANGUAGE_ID,
				"TYPE" => "HREF",
			],
		],
	],
	[
		"NAME" => "active",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_ACTIVE"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => [
					Loc::getMessage("IPL_MA_YES"),
					Loc::getMessage("IPL_MA_NO"),
				],
				"reference_id" => [
					"Y",
					"N",
				]
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW" => [
			"AddCheckField" =>[],
		],
		"REPLACE" => [
			"Y" => Loc::getMessage("IPL_MA_YES"),
			"N"  => Loc::getMessage("IPL_MA_NO"),
		],
	],
	[
		"NAME" => "sort",
		"CAPTION" => Loc::getMessage("IPL_MA_CAPTION_SORT"),
		"FILTER" => [],
		"VIEW" => [
			"AddInputField" => [],
		],
	],
	[
		"NAME" => "scheme",
		"CAPTION" => Loc::getMessage("IPL_MA_SCHEME"),
		"FILTER" => [
			"VIEW" => "select",
			"VALUES" => [
				"reference" => [
					"FBS",
					"DBS",
				],
				"reference_id" => [
					"FBS",
					"DBS",
				]
			],
			"DEFAULT" => Loc::getMessage("IPL_MA_ALL"),
		],
		"VIEW" => [
			"AddSelectField" =>[
				"PARAM" => [
					"FBS" => "FBS",
					"DBS" => "DBS",
				],
			]
		],
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
	$arContextMenu[] = [
		"TEXT"=>Loc::getMessage("IPL_MA_PRIFILE_ADD"),
		"LINK"=>"iplogic_beru_profile_edit.php?mode=new&lang=".LANG,
		"TITLE"=>Loc::getMessage("IPL_MA_PRIFILE_ADD_TITLE"),
		"ICON"=>"btn_new",
	];
}



/* context menu for each line */
$arItemContextMenu = [
	[
		"TEXT" => Loc::getMessage("MAIN_ADMIN_LIST_EDIT"),
		"TITLE" => Loc::getMessage("MAIN_ADMIN_LIST_EDIT"),
		"ICON" => "edit",
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_profile_edit.php?ID=##ID##&lang=".LANG,
		],
		"DEFAULT" => true
	],
	[
		"TEXT" => Loc::getMessage("IPL_MA_COPY"),
		"TITLE" => Loc::getMessage("IPL_MA_COPY"),
		"ICON" => "copy",
		"ACTION" => [
			"TYPE" => "REDIRECT",
			"HREF" => "iplogic_beru_profile_list.php?act=copy&ID=##ID##&lang=".LANG,
		],
		"DEFAULT" => true
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
	"TITLE" => Loc::getMessage("IPL_MA_LIST_TITLE"),
	"COPY" => Loc::getMessage("IPL_MA_LIST_COPY"),
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
$adminControl = new Iplogic\Beru\Admin\TableList($moduleID);
$adminControl->POST_RIGHT = $MODULE_ACCESS;
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $arContextMenu;
$adminControl->arItemContextMenu = $arItemContextMenu;
$adminControl->sTableClass = "\Iplogic\Beru\ProfileTable";


/* exec actions */
$adminControl->initList("tbl_iplogic_beru_profiles");
$adminControl->EditAction();
$adminControl->GroupAction();
if( $MODULE_ACCESS >= "W"
	&& $fatalErrors == ""
) {
	if( $request->get("act") == "copy" ) {
		$profile = ProfileTable::getById($request->get("ID"));
		unset($profile["ID"]);
		$profile["NAME"] = $profile["NAME"] . " copy";
		// echo "<pre>"; print_r($profile); echo "</pre>"; die();
		$newID = ProfileTable::add($profile);
		if($newID) {
			foreach($profile["PROP"] as $prop) {
				unset($prop["ID"]);
			}
			$res = true;
			foreach($profile["PROP"] as $arFields) {
				unset($arFields["ID"]);
				$_res = ProfileTable::setAccordance($newID, $arFields);
				if(!$_res) $res = false;
			}
			if(!$res) {
				$adminControl->errors = array_merge($adminControl->errors, Loc::getMessage('IPL_MA_PROP_ADD_ERROR'));
			}
			LocalRedirect("/bitrix/admin/iplogic_beru_profile_list.php?mess=ok&lang=".LANG);
		}
		else {
			$adminControl->errors = array_merge($adminControl->errors, Loc::getMessage('IPL_MA_PRIFILE_ADD_ERROR'));
		}
	}
}


/* get list and put it in control object */
$rsData = ProfileTable::getList(['order' => $adminControl->arSort, 'filter' => $adminControl->arFilter, 'select' => $adminControl->arSelect]);
$adminControl->prepareData($rsData);


/* starting output */
$APPLICATION->SetTitle(Loc::getMessage('IPL_MA_LIST_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/* ok message */
if($request->get("mess") === "ok")
	CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage('IPL_MA_PRIFILE_ADD_SUCCESS'), "TYPE"=>"OK"));


/* action errors */
if( count($adminControl->errors) ) {
	foreach($adminControl->errors as $error) {
		CAdminMessage::ShowMessage($error);
	}
}

$adminControl->renderList();

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/epilog_admin.php');
?>