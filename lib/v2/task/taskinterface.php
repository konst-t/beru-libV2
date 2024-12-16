<?php
namespace Iplogic\Beru\V2\Task;

/**
 * Interface TaskInterface
 * @package Iplogic\Beru\V2\Task
 */
interface TaskInterface
{
	public function execute($arTask): void;
}