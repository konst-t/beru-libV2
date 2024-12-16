<?php
namespace Iplogic\Beru\V2;

/**
 * Starts command execution
 *
 * Class Executor
 * @package Iplogic\Beru\V2
 */
class Executor
{

	public static function go(Command\CommandInterface $command, array $arParams = []): string
	{
		if(!empty($arParams)) {
			$command->setParams($arParams);
		}
		$command->execute();
		return $command->getStatus();
	}

}