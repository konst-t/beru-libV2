<?

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;

use \Iplogic\Beru\V2\ORM\ProfileTable;
use \Iplogic\Beru\V2\Helper;

$request = Application::getInstance()->getContext()->getRequest();

Loc::loadMessages(__FILE__);

$fatalErrors = "";

//$license =  Loader::includeSharewareModule($moduleID);

if (!Loader::includeModule($moduleID)){
	$fatalErrors = Loc::getMessage("MODULE_INCLUDE_ERROR")."<br>";
}
if (!Loader::includeModule("catalog")){
	$fatalErrors = Loc::getMessage("MODULE_INCLUDE_ERROR")."<br>";
}
if (!Loader::includeModule("sale")){
	$fatalErrors = Loc::getMessage("MODULE_INCLUDE_ERROR")."<br>";
}

$MODULE_ACCESS = \Iplogic\Beru\Access::getGroupRight("module");

$POST_REQUEST = false;

$pass = (time() - Option::get($moduleID,"system_check_last_time",0))/86400;
if ( $pass > 30 ) {
	include($_SERVER['DOCUMENT_ROOT'].BX_ROOT."/modules/".$moduleID."/conf_check.php");
	if(!$arConfigCheck["RESULT"]){
		$arErrMess = Helper::getMessFromAllLangFiles(__FILE__, "CONFIG_CHECK_MESSAGE");
		$arFields = array(
			'MODULE_ID' => $moduleID,
			'TAG' => 'iplogic_beru_config_check_error',
			'MESSAGE' => $arErrMess["en"],
		);
		unset($arErrMess["en"]);
		$arFields['LANG'] = $arErrMess;
		CAdminNotify::Add($arFields);
		unset($arFields);
	}
	Option::set($moduleID,"system_check_last_time",time());
}


if($checkParams["PROFILE"]) {
	$PROFILE_ID = $request->get("PROFILE_ID");
	if ($PROFILE_ID > 0){
		$arProfile = ProfileTable::getByIdFull($PROFILE_ID);
		if (!$arProfile){
			$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
		}
	}
	else {
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
	}
}

if($checkParams["ID"]) {
	$ID = $request->get("ID");
	if ($ID > 0){
		$class = $checkParams["CLASS"];
		$arFields = $class::getRowById($ID);
		if (!$arFields){
			$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
		}
	}
	else {
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
	}
}

?>