<?php

namespace Iplogic\Beru\V2\Task;

use Iplogic\Beru\V2\Task;
use Iplogic\Beru\V2\ORM\TaskTable;

/**
 * Checks for old and unfinished tasks and deletes them
 *
 * Class CT
 * @package Iplogic\Beru\V2\Task
 */
class CT implements TaskInterface
{

	public function execute($arTask): void
	{
		$time = time() - 3000;
		$result = TaskTable::getList(["filter" => ["<=UNIX_TIMESTAMP" => $time, "=STATE" => "IW"]]);
		while( $task = $result->fetch() ) {
			TaskTable::delete($task["ID"]);
		}
		$time = time() - 2592000;
		$result = TaskTable::getList(["filter" => ["<=UNIX_TIMESTAMP" => $time]]);
		while( $task = $result->fetch() ) {
			TaskTable::delete($task["ID"]);
		}
		if(isset($arTask["ID"]) && $arTask["ID"] > 0) {
			TaskTable::delete($arTask["ID"]);
		}
		Task::scheduleTask(0, "CT", 600);
		return;
	}

}