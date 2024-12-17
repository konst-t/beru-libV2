<?php

namespace Iplogic\Beru\V2\Command;

/**
 * Interface CommandInterface
 * @package Iplogic\Beru\V2\Command
 */
interface CommandInterface
{
	public function execute(): void;

	public function getStatus(): string;

	public function setParams(array $arParams): void;
}