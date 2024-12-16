<?php
namespace Iplogic\Beru\V2\Task;

use Iplogic\Beru\V2\ORM\TaskTable;

/**
 * Updates product cache
 *
 * Class DU
 * @package Iplogic\Beru\V2\Task
 */
class DU implements TaskInterface
{

	public function execute($arTask): void
	{
		\Iplogic\Beru\V2\Executor::go(new \Iplogic\Beru\V2\Command\updateCache(), ["ID" => $arTask["ENTITY_ID"]]);
		TaskTable::delete($arTask["ID"]);
	}
}