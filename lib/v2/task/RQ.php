<?php
namespace Iplogic\Beru\V2\Task;

use \Iplogic\Beru\V2\ORM\ApiLogTable;
use \Iplogic\Beru\V2\ORM\TaskTable;
use \Bitrix\Main\Config\Option;

/**
 * Resends a previously rejected request
 *
 * Class RQ
 * @package Iplogic\Beru\V2\Task
 */
class RQ extends \Iplogic\Beru\V2\ApiRequest implements TaskInterface
{
	const MODULE_ID = "iplogic.beru";

	public function execute($arTask): void
	{
		if(!isset($arTask["ID"])) {
			return;
		}
		$arLog = ApiLogTable::getRowById($arTask["ENTITY_ID"]);
		if( $arLog ) {
			$res = $this->query($arLog["REQUEST_TYPE"], $arLog["URL"], $arLog["REQUEST"], $arTask);
			if( $res["status"] == 200 ) {
				TaskTable::delete($arTask["ID"]);
				return;
			}
			if( $res["stop_repeating"] ) {
				TaskTable::delete($arTask["ID"]);
				$arFields = [
					"STATE" => "RJ",
				];
				ApiLogTable::update($arTask["ENTITY_ID"], $arFields);
			}
			else {
				$arFields = [
					"STATE"          => "WT",
					"TRYING"         => ($arTask["TRYING"] + 1),
					"UNIX_TIMESTAMP" => time() + Option::get(self::MODULE_ID, "task_trying_period", 60),
				];
				TaskTable::update($arTask["ID"], $arFields);
			}
		}
		else {
			TaskTable::delete($arTask["ID"]);
		}
	}

	public function send()
	{
		return;
	}
}