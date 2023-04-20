<?

namespace Iplogic\Beru;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Iplogic\Beru\RightsTable;


class Access
{
	const MODULE_ID = "iplogic.beru";
	const ADMIN_RIGHT = 'X';

	public $arGROUPS;
	public $binding;


	public function __construct($binding) {
		$this->binding = $binding;
		$this->arGROUPS = self::getGroups();
	}


	public static function getGroups()
	{
		$groups = [];
		$arFilter = [];
		$z = \CGroup::GetList("sort", "asc", $arFilter);
		while($zr = $z->Fetch())
		{
			$ar["ID"] = intval($zr["ID"]);
			if($ar["ID"] == 1) {
				continue;
			}
			$ar["NAME"] = htmlspecialcharsbx($zr["NAME"]);
			$groups[] = $ar;
		}
		return $groups;
	}


	public static function getTasksForModule($binding, $entityID = false)
	{
		if($binding == "module") {
			return \CGroup::GetTasksForModule(self::MODULE_ID);
		}
		else {
			if(!$entityID) {
				$entityID = 0;
			}
			$arResult = [];
			$conn = Application::getConnection();
			$helper = $conn->getSqlHelper();
			$strSql = "SELECT * FROM b_iplogicberu_rights MR " .
				"INNER JOIN b_task T ON (MR.TASK_ID = T.ID) WHERE " .
				"MR.ENTITY_TYPE = '" . $binding."' AND " .
				"MR.ENTITY_ID = " . $entityID;
			$result = $conn->query($strSql);
			unset($helper, $conn);
			while ($arRec = $result->Fetch()) {
				$arResult[$arRec["GROUP_ID"]] = [
					"ID" => $arRec["TASK_ID"],
					"NAME" => $arRec["NAME"],
				];
			}
			return $arResult;
		}
	}


	public static function getGroupRight($binding, $entityID = false, $arGroups = false, $site_id = false)
	{
		global $USER;
		$arTasksInModule = \CTask::GetTasksInModules(false, self::MODULE_ID, $binding);
		$bindAdd = self::getDefaultVarIns($binding, $entityID);
		$defaultRightVar = "GROUP_" . $bindAdd . "DEFAULT_RIGHT";
		if(!is_array($arTasksInModule) || count($arTasksInModule) <= 0) {
			if($USER->isAdmin()) {
				return self::ADMIN_RIGHT;
			}
			return Option::get(self::MODULE_ID, $defaultRightVar, "D");
		}
		$arTasks = [];
		foreach($arTasksInModule[self::MODULE_ID] as $key => $val) {
			$arTasks[$val["LETTER"]] = $val["ID"];
		}
		krsort($arTasks);
		$maxRight = array_key_first($arTasks);
		if($USER->isAdmin()) {
			return $maxRight;
		}
		if(!$arGroups) {
			$arGroups = $USER->GetUserGroupArray();
		}
		if(!is_array($arGroups) || count($arGroups) <= 0) {
			return Option::get(self::MODULE_ID, $defaultRightVar, "D");
		}
		$right = "";
		if($binding == "module") {
			$conn = Application::getConnection();
			$helper = $conn->getSqlHelper();
			$gIds = implode(", ", $arGroups);
			$strSql = "SELECT * FROM b_group_task WHERE GROUP_ID IN (" . $gIds . ")";
			$result = $conn->query($strSql);
			unset($helper, $conn);
			while ($arRec = $result->Fetch()) {
				$letter = array_search($arRec["TASK_ID"], $arTasks);
				if($letter > $right) {
					$right = $letter;
				}
			}
		}
		else {
			$rsRight = RightsTable::getList([
				"filter" => ["=ENTITY_TYPE" => $binding, "=ENTITY_ID" => $entityID, "=GROUP_ID" => $arGroups]
			]);
			while($arRight = $rsRight->Fetch()) {
				$letter = array_search($arRight["TASK_ID"], $arTasks);
				if($letter > $right) {
					$right = $letter;
				}
			}
		}
		if($right == "") {
			return Option::get(self::MODULE_ID, $defaultRightVar, "D");
		}
		return $right;
	}


	public static function canDoOperation($op, $entityID = false, $userId = 0) {
		global $USER;
		if($userId > 0) {
			$arGroups = \CUser::GetUserGroup($userId);
			if(in_array(1, $arGroups)) {
				return true;
			}
		}
		else {
			if($USER->isAdmin()) {
				return true;
			}
			$arGroups = $USER->GetUserGroupArray();
		}
		$res = \COperation::GetList([], ["NAME" => $op, "MODULE_ID" => self::MODULE_ID]);
		if(!$arOp = $res->Fetch()) {
			return false;
		}
		$binding = $arOp["BINDING"];
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "SELECT * FROM b_task_operation OT " .
			"INNER JOIN b_task T ON (OT.TASK_ID = T.ID) WHERE " .
			"OT.OPERATION_ID = " . $arOp["ID"];
		$result = $conn->query($strSql);
		$arPasTasks = [];
		$arPasLetters = [];
		while ($arRec = $result->Fetch()) {
			$arPasLetters[] = $arRec["LETTER"];
			$arPasTasks[] = $arRec["TASK_ID"];
		}
		if(count($arPasTasks) <= 0) {
			return false;
		}

		$bindAdd = self::getDefaultVarIns($binding, $entityID);
		$defaultRightVar = "GROUP_" . $bindAdd . "DEFAULT_RIGHT";
		$gIds = implode(", ", $arGroups);
		if($binding == "module") {
			$strSql = "SELECT * FROM b_task T  " .
				"INNER JOIN b_group_task GT ON (GT.TASK_ID = T.ID) WHERE " .
				"GT.GROUP_ID IN (" . $gIds . ") AND " .
				"T.MODULE_ID = '" . self::MODULE_ID . "'";
		}
		else {
			$strSql = "SELECT * FROM b_task T  " .
				"INNER JOIN " . RightsTable::getTableName() . " GT ON (GT.TASK_ID = T.ID) WHERE " .
				"GT.GROUP_ID IN (" . $gIds . ") AND " .
				"GT.ENTITY_ID = " . $entityID . " AND " .
				"GT.ENTITY_TYPE = '" . $binding . "'";
		}
		$result = $conn->query($strSql);
		unset($helper, $conn);
		while($arRec = $result->Fetch()) {
			if(in_array($arRec["LETTER"], $arPasLetters)) {
				return true;
			}
			unset($arGroups[array_search($arRec["GROUP_ID"], $arGroups)]);
		}
		foreach($arGroups as $gr) {
			if(in_array(Option::get(self::MODULE_ID, $defaultRightVar, "D"), $arPasLetters)) {
				return true;
			}
		}
		return false;
	}


	public static function setGroupRight($binding, $group_id, $right, $task, $entityID = false, $site_id=false)
	{
		if($binding == "module") {
			return \CMain::SetGroupRight(self::MODULE_ID, $group_id, $right, $site_id=false);
		}
		else {
			$group_id = intval($group_id);

			//get and delete old values
			$rsRight = RightsTable::getList([
				"filter" => ["=ENTITY_TYPE" => $binding, "=ENTITY_ID" => $entityID, "=GROUP_ID" => $group_id]
			]);
			while($arOldRight = $rsRight->Fetch()) {
				$oldRecord = $arOldRight["ID"];
				RightsTable::delete($oldRecord);
			}

			$arFields = array(
				"ENTITY_TYPE" => $binding,
				"ENTITY_ID" => $entityID,
				"GROUP_ID" => $group_id,
				"TASK_ID" => $task,
			);

			RightsTable::add($arFields);
		}
	}


	public static function delGroupRight($binding, $arGroups = [], $entityID = false, $site_id = false)
	{
		if($binding == "module") {
			return \CMain::DelGroupRight(self::MODULE_ID, $arGroups = [], $site_id = false);
		}
		else {

			if(!is_array($arGroups) && strlen($arGroups) > 0) {
				$arGroups = [intval($arGroups)];
			}

			$rsRight = RightsTable::getList([
				"filter" => ["=ENTITY_TYPE" => $binding, "=ENTITY_ID" => $entityID, "=GROUP_ID" => $arGroups]
			]);
			while($arRight = $rsRight->Fetch()) {
				RightsTable::delete($arRight["ID"]);
			}

		}
	}


	public static function getDefaultVarIns($binding, $entityID = false)
	{
		$bindAdd = "";
		if($binding != "module") {
			$bindAdd .= strtoupper($binding) . "_";
			if($entityID > 0) {
				$bindAdd .= strtoupper($entityID) . "_";
			}
		}
		return $bindAdd;
	}
}
