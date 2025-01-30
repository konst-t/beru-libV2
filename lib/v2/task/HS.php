<?php

namespace Iplogic\Beru\V2\Task;

use \Iplogic\Beru\V2\Task;
use \Iplogic\Beru\V2\ApiRequest;
use \Iplogic\Beru\V2\ORM\TaskTable;
use \Iplogic\Beru\V2\ORM\ProductTable;

class HS implements TaskInterface
{

	public function execute($arTask): void
	{
		$IDs = [];
		$arProducts = [];
		if(isset($arTask["PROFILE_ID"])) {
			$rsData = TaskTable::getList(
				[
					"filter" => ["TYPE" => "HP", "PROFILE_ID" => $arTask["PROFILE_ID"]],
					"order"  => ["UNIX_TIMESTAMP" => "ASC"],
					'limit'  => 500,
					'offset' => 0,
				]
			);
			while( $arData = $rsData->Fetch() ) {
				$IDs[$arData["ID"]] = $arData["ENTITY_ID"];
			}
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
			$arBody['hiddenOffers'] = [];
			$unseted = [];
			foreach( $IDs as $key => $val ) {
				if( !isset($arProducts[$val]) || !$arProducts[$val]["MARKET_SKU"] ) {
					$unseted[] = $val;
					TaskTable::delete($key);
				}
				else {
					$arBody['hiddenOffers'][] = [
						"marketSku"  => (int)$arProducts[$val]["MARKET_SKU"],
						"comment"    => "",
						"ttlInHours" => 720,
					];
				}
			}
			$res = (new ApiRequest\setHidden($arTask["PROFILE_ID"]))->send($arBody);
			if( $res["status"] == 200 ) {
				foreach( $IDs as $key => $val ) {
					if( !in_array($val, $unseted) ) {
						ProductTable::update($val, ["HIDDEN" => "Y"]);
						TaskTable::delete($key);
					}
				}
			}
		}
		else {
			foreach( $IDs as $key => $val ) {
				TaskTable::delete($key);
			}
		}
		if(isset($arTask["ID"]) && $arTask["ID"] > 0) {
			TaskTable::delete($arTask["ID"]);
		}
		Task::scheduleTask($arTask["PROFILE_ID"], "HS", 60);
	}
}