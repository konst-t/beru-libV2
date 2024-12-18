<?php

namespace Iplogic\Beru\V2\Task;

use \Iplogic\Beru\V2\Task;
use \Iplogic\Beru\V2\ApiRequest;
use \Iplogic\Beru\V2\ORM\TaskTable;
use \Iplogic\Beru\V2\ORM\ProductTable;

class US implements TaskInterface
{

	public function execute($arTask): void
	{
		$rsData = TaskTable::getList(
			[
				"filter" => ["TYPE" => "UP", "PROFILE_ID" => $arTask["PROFILE_ID"]],
				"order"  => ["UNIX_TIMESTAMP" => "ASC"],
				'limit'  => 500,
				'offset' => 0,
			]
		);
		$IDs = [];
		$arProducts = [];
		while( $arData = $rsData->Fetch() ) {
			$IDs[$arData["ID"]] = $arData["ENTITY_ID"];
		}
		if( count($IDs) ) {
			$rsData = ProductTable::getList(
				[
					"filter" => ["ID" => $IDs],
				]
			);
			while( $arData = $rsData->Fetch() ) {
				$arProducts[$arData["ID"]] = $arData;
			}
		}
		if( count($arProducts) ) {
			$arResult['hiddenOffers'] = [];
			$unseted = [];
			foreach( $IDs as $key => $val ) {
				if( !isset($arProducts[$val]) || !$arProducts[$val]["MARKET_SKU"] ) {
					$unseted[] = $val;
					TaskTable::delete($key);
				}
				else {
					$arResult['hiddenOffers'][] = [
						"marketSku" => (int)$arProducts[$val]["MARKET_SKU"],
					];
				}
			}
			$res = (new ApiRequest\setShown($arResult))->send();
			if( $res["status"] == 200 ) {
				foreach( $IDs as $key => $val ) {
					if( !in_array($val, $unseted) ) {
						ProductTable::update($val, ["HIDDEN" => "N"]);
						TaskTable::delete($key);
					}
				}
			}
			else {
				//
			}
		}
		TaskTable::delete($arTask["ID"]);
		Task::scheduleTask($arTask["PROFILE_ID"], "US", 60);
	}
}