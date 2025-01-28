<?

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;

use \Iplogic\Beru\V2\ORM\BusinessTable;
use \Iplogic\Beru\V2\ORM\ProfileTable;

$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);
$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');

$checkParams = [];

include($baseFolder . "/modules/" . $moduleID . "/prolog.php");

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/options.php");
Loc::loadMessages(__FILE__);

if( $MODULE_ACCESS == "D" ) {
	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
	die();
}

class TableFormEx extends Iplogic\Beru\Admin\TableForm
{

	protected function addButtons()
	{
		$this->tabControl->Buttons(
			[
				"disabled" => ($this->POST_RIGHT < "W"),
				"back_url" => "/bitrix/admin/iplogic_beru_business_list.php?lang=" . LANG,
			]
		);
	}

}

$ID = $request->get("ID");
if( $ID > 0 ) {
	$arFields = BusinessTable::getRowById($ID, true);
	if( !$arFields ) {
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS") . "<br>";
	}
}

CJSCore::Init(["jquery"]);

$LID = Iplogic\Beru\Admin\TableForm::getLID();

if(
	$request->isPost()
	&& ($request->get("save") != "" || $request->get("apply") != "")
	&& $MODULE_ACCESS >= "W"
	&& check_bitrix_sessid()
	&& $fatalErrors == ""
) {
	$POST_REQUEST = true;
}

$aTabs = [
	[
		"DIV"   => "edit1",
		"TAB"   => Loc::getMessage("IPL_MA_GENERAL"),
		"ICON"  => "main_user_edit",
		"TITLE" => Loc::getMessage("IPL_MA_GENERAL_TITLE"),
	],
];


$arOpts = [

	/* GENERAL */
	"BID"     => [
		"TAB"      => "edit1",
		"TYPE"     => "text",
		"DEFAULT"  => "",
		"NAME"     => Loc::getMessage("IPL_MA_ID"),
		"REQURIED" => "Y",
	],
	"NAME"    => [
		"TAB"     => "edit1",
		"TYPE"    => "text",
		"DEFAULT" => "",
		"NAME"    => Loc::getMessage("IPL_MA_NAME"),
	],
	"API_KEY" => [
		"TAB"     => "edit1",
		"TYPE"    => "text",
		"DEFAULT" => "",
		"NAME"    => "Api-Key",
	],
];

if( $ID > 0 ) {
	$arProfiles = [0 => Loc::getMessage("NOT_CHOSEN")];
	$rsProfiles = ProfileTable::getList(["filter" => ["BUSINESS_ID" => $arFields["BID"]]]);
	while($arProfile = $rsProfiles->fetch()) {
		$arProfiles[$arProfile["ID"]] = $arProfile["NAME"] . " [" . $arProfile["ID"] . "]";
	}
	$arOpts["BASE_PROFILE"] = [
		"TAB"       => "edit1",
		"TYPE"      => "select",
		"DEFAULT"   => "",
		"NAME"      => Loc::getMessage("IPL_MA_BASE_PROFILE"),
		"OPTIONS"   => $arProfiles,
	];
}


$aMenu = [
	[
		"TEXT"  => Loc::getMessage("IPL_MA_LIST"),
		"TITLE" => Loc::getMessage("IPL_MA_LIST_TITLE"),
		"LINK"  => "iplogic_beru_business_list.php?lang=" . LANG,
		"ICON"  => "btn_list",
	],
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
		if( $ID > 0 ) {
			$res = BusinessTable::update($ID, $arFields);
			if( is_array($res) && isset($res["error"]) ) {
				$adminControl->errors[] = implode("<br>", $res["error"]);
				$res = false;
			}
		}
		else {
			$res = BusinessTable::add($arFields);
			if( !$res->isSuccess() ) {
				$adminControl->errors[] = implode("<br>", $res->getErrorMessages());
				$res = false;
			}
			else {
				$ID = $res->getId();
			}
		}
		if( $res ) {
			if( $request->get("apply") != "" ) {
				LocalRedirect(
					"/bitrix/admin/iplogic_beru_business_edit.php?ID=" . $ID . "&mess=ok&lang=" . LANG . "&" .
					$adminControl->ActiveTabParam()
				);
			}
			else {
				LocalRedirect("/bitrix/admin/iplogic_beru_business_list.php?lang=" . LANG);
			}
		}
	}

}

$APPLICATION->SetTitle(
	(
	$ID > 0 ?
		Loc::getMessage("IPL_MA_BUSINESS_EDIT_TITLE") . " " . $arFields["NAME"] . " #" . $ID :
		Loc::getMessage("IPL_MA_BUSINESS_NEW_TITLE")
	)
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

if( $fatalErrors != "" ) {
	CAdminMessage::ShowMessage($fatalErrors);
	require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
	die();
}

if( $request->get("mess") === "ok" ) {
	CAdminMessage::ShowMessage(["MESSAGE" => Loc::getMessage("SAVED"), "TYPE" => "OK"]);
}

elseif( count($adminControl->errors) ) {
	foreach( $adminControl->errors as $error ) {
		CAdminMessage::ShowMessage($error);
	}
}

$adminControl->buildPage();

$adminControl->getIBlockChooseScript("IBLOCK_ID", "IBLOCK_TYPE", "SITE");


require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>