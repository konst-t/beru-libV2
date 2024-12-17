<?php

namespace Iplogic\Beru\V2\Task;

use Iplogic\Beru\V2\Task;
use Iplogic\Beru\V2\ORM\TaskTable;

/**
 * Checks for old unfinished tasks and deletes them
 *
 * Class CT
 * @package Iplogic\Beru\V2\Task
 */
class CT implements TaskInterface
{

	public function execute($arTask): void
	{
		$time = time() - 300;
		$result = TaskTable::getList(["filter" => ["<=UNIX_TIMESTAMP" => $time, "=STATE" => "IW"]]);
		while( $task = $result->fetch() ) {
			TaskTable::delete($task["ID"]);
		}
		TaskTable::delete($arTask["ID"]);
		Task::scheduleTask(0, "CT", 600);
		return;
	}

}