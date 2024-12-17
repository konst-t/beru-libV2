<?php

namespace Iplogic\Beru\V2;

use \Bitrix\Main\Config\Option;
use \Iplogic\Beru\V2\ORM\ApiLogTable;
use \Iplogic\Beru\TaskTable;


/**
 * Module agents
 *
 * Class Agent
 * @package Iplogic\Beru\V2
 */
class Agent
{
	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";


	public static function executeTasksAgent()
	{
		$can_execute_tasks = Option::get(self::$moduleID, "can_execute_tasks", "Y");
		$task_time = time() - Option::get(self::$moduleID, "last_task_time", 0);
		if( $task_time > 600 && $can_execute_tasks == "N" ) {
			Option::set(self::$moduleID, "can_execute_tasks", "Y");
			$can_execute_tasks = "Y";
		}
		if( $can_execute_tasks == "Y" ) {
			if( Option::get(self::$moduleID, "allow_multichain_tasks", "N") == "N" ) {
				Option::set(self::$moduleID, "can_execute_tasks", "N");
			}
			TaskTable::executeNextTask();
		}
		if( Option::get(self::$moduleID, "products_check_disable", "N") != "Y" ) {
			$pass = (time() - Option::get(self::$moduleID, "products_check_last_time", 0)) / 3600;
			if( $pass > Option::get(self::$moduleID, "products_check_period", 0) ) {
				exec(
					"wget -b -q -O - https://" . Option::get(self::$moduleID, "domen") .
					"/bitrix/services/iplogic/mkpapi/getmpoffers.php"
				);
			}
		}
		if( Option::get(self::$moduleID, "keep_log_days", 0) > 0 ) {
			$pass = (time() - Option::get(self::$moduleID, "log_clear_last_time", 0)) / 86400;
			if( $pass > Option::get(self::$moduleID, "keep_log_days") ) {
				$time = time() - (Option::get(self::$moduleID, "keep_log_days") * 86400);
				ApiLogTable::clearOld($time);
				Option::set(self::$moduleID, "log_clear_last_time", time());
			}
		}
		if( Option::get(self::$moduleID, "keep_temp_files_days", 0) > 0 ) {
			$pass = (time() - Option::get(self::$moduleID, "temp_files_clear_last_time", 0)) / 86400;
			if( $pass > Option::get(self::$moduleID, "keep_temp_files_days") ) {
				Helper::clearOldFiles();
				Option::set(self::$moduleID, "temp_files_clear_last_time", time());
			}
		}
		return "\Iplogic\Beru\V2\Agent::executeTasksAgent();";
	}

}