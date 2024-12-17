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


	public static function scheduleTask($PROFILE_ID, $CODE, $DELAY): void
	{
		$result = TaskTable::getList(
			["filter" => ["TYPE" => $CODE, "STATE" => "WT", "PROFILE_ID" => $PROFILE_ID]]
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
			TaskTable::add($arFields);
		}
	}


	public static function addPriceUpdateTask($ID, $PROFILE_ID)
	{
		if( Option::get(self::$moduleID, "send_prices") == "Y" ) {
			$rsTask = self::getList(
				["filter" => ["TYPE" => "PR", "STATE" => "WT", "ENTITY_ID" => $ID, "PROFILE_ID" => $PROFILE_ID]]
			);
			if( !$rsTask->Fetch() ) {
				$arFields = [
					"PROFILE_ID"     => $PROFILE_ID,
					"UNIX_TIMESTAMP" => time(),
					"TYPE"           => "PR",
					"STATE"          => "WT",
					"ENTITY_ID"      => $ID,
					"TRYING"         => 0,
				];
				self::add($arFields);
				self::scheduleTask($PROFILE_ID, "SP", 60);
			}
		}
	}

	public static function addStockUpdateTask($ID, $PROFILE_ID)
	{
		$mod = new Control($PROFILE_ID);
		if( (int)$mod->arProfile["STORE"] > 0 && Option::get(self::$moduleID, "send_stocks") == "Y" ) {
			$rsTask = self::getList(
				["filter" => ["TYPE" => "ST", "STATE" => "WT", "ENTITY_ID" => $ID, "PROFILE_ID" => $PROFILE_ID]]
			);
			if( !$rsTask->Fetch() ) {
				$arFields = [
					"PROFILE_ID"     => $PROFILE_ID,
					"UNIX_TIMESTAMP" => time(),
					"TYPE"           => "ST",
					"STATE"          => "WT",
					"ENTITY_ID"      => $ID,
					"TRYING"         => 0,
				];
				self::add($arFields);
				self::scheduleTask($PROFILE_ID, "SP", 60);
			}
		}
	}

	public static function hideProductTask($ID, $PROFILE_ID)
	{
		$rsTask = self::getList(["filter" => ["TYPE" => "HP", "STATE" => "WT", "ENTITY_ID" => $ID]]);
		if( !$rsTask->Fetch() ) {
			$arFields = [
				"PROFILE_ID"     => $PROFILE_ID,
				"UNIX_TIMESTAMP" => time(),
				"TYPE"           => "HP",
				"STATE"          => "WT",
				"ENTITY_ID"      => $ID,
				"TRYING"         => 0,
			];
			self::add($arFields);
			self::scheduleTask($PROFILE_ID, "HS", 60);
		}
	}

	public static function showProductTask($ID, $PROFILE_ID)
	{
		$rsTask = self::getList(["filter" => ["TYPE" => "UP", "STATE" => "WT", "ENTITY_ID" => $ID]]);
		if( !$rsTask->Fetch() ) {
			$arFields = [
				"PROFILE_ID"     => $PROFILE_ID,
				"UNIX_TIMESTAMP" => time(),
				"TYPE"           => "UP",
				"STATE"          => "WT",
				"ENTITY_ID"      => $ID,
				"TRYING"         => 0,
			];
			self::add($arFields);
			self::scheduleTask($PROFILE_ID, "US", 60);
		}
	}

}


