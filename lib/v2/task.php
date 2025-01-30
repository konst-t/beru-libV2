<?

namespace Iplogic\Beru\V2;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;

use \Iplogic\Beru\V2\ORM\TaskTable;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class Task
 *
 * @package Iplogic\Beru
 **/
class Task
{
	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";


	public static function executeNextTask(): void
	{
		Loader::includeModule("catalog");
		if( $task = TaskTable::getNextTask() ) {
			Option::set(static::$moduleID, "last_task_time", time());
			$arFields = ["STATE" => "IW"];
			TaskTable::update($task["ID"], $arFields);
			$task["TYPE"] = "RQ";
			$class = "\\Iplogic\\Beru\\V2\\Task\\" . $task["TYPE"];

			(new $class)->execute($task);

			$v = randString(12, "0123456789");
			$comm = "wget --no-check-certificate ––tries=0 -b -q -O - https://" .
				Option::get(self::$moduleID, "domen") . "/bitrix/services/iplogic/mkpapi/task.php?v=" . $v;
			exec($comm);
		}
		else {
			if(
				Option::get(self::$moduleID, "can_execute_tasks", "N") == "N"
				&& Option::get(self::$moduleID, "allow_multichain_tasks", "N") == "N"
			) {
				Option::set(self::$moduleID, "can_execute_tasks", "Y");
			}
		}
	}


	public static function scheduleTask($PROFILE_ID, $CODE, $DELAY, $ENTITY_ID = false): void
	{
		$result = TaskTable::getList(
			["filter" => ["TYPE" => $CODE, "STATE" => "WT", "PROFILE_ID" => $PROFILE_ID, "ENTITY_ID" => $ENTITY_ID]]
		);
		$task = $result->Fetch();
		if( !$task ) {
			$arFields = [
				"PROFILE_ID"     => $PROFILE_ID,
				"UNIX_TIMESTAMP" => time() + $DELAY,
				"TYPE"           => $CODE,
				"STATE"          => "WT",
				"TRYING"         => 0,
			];
			if ($ENTITY_ID !== false) {
				$arFields["ENTITY_ID"] = $ENTITY_ID;
			}
			TaskTable::add($arFields);
		}
	}


	public static function scheduleTaskComplex($ID, $PROFILE_ID, $CODE, $SCODE)
	{
		$rsTask = TaskTable::getList(
			["filter" => ["TYPE" => "PR", "STATE" => "WT", "ENTITY_ID" => $ID, "PROFILE_ID" => $PROFILE_ID]]
		);
		if( !$rsTask->Fetch() ) {
			self::scheduleTask($PROFILE_ID, $CODE, 0, $ID);
			self::scheduleTask($PROFILE_ID, $SCODE, 60);
		}
	}

}


