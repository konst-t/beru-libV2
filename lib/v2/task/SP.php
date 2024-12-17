<?php

namespace Iplogic\Beru\V2\Task;

use \Iplogic\Beru\V2\Helper;
use \Iplogic\Beru\V2\Task;
use \Iplogic\Beru\V2\ApiRequest;
use \Iplogic\Beru\V2\Product;
use \Iplogic\Beru\V2\ORM\ProfileTable;
use \Iplogic\Beru\V2\ORM\TaskTable;
use \Iplogic\Beru\V2\ORM\ProductTable;


class SP implements TaskInterface
{
	/**
	 * @var int
	 */
	protected $profileId;

	/**
	 * @var array
	 */
	protected $taskIDs;


	public function execute($arTask): void
	{
		if(($this->profileId = (int)$arTask["PROFILE_ID"]) < 1) {
			return;
		}
		if(Helper::getOption("send_prices") == "Y") {
			$this->sendCampaignPrices();
		}
		if(Helper::getOption("send_stocks") == "Y") {
			$this->sendStocks();
		}
		TaskTable::delete($arTask["ID"]);
		Task::scheduleTask($arTask["PROFILE_ID"], "SP", 60);
	}


	protected function sendStocks(): void
	{
		$arProfile = ProfileTable::getByIdFull($this->profileId);
		if( (int)$arProfile["STORE"] <= 0 ) {
			return;
		}
		$bContinue = true;
		$steps = 1;
		while ($bContinue == true) {
			$arProducts = $this->getProducts("ST", 2000);
			if( is_array($arProducts) ) {
				$arStocks = [];
				$arResult['offers'] = [];
				$arRequest["skus"] = [];
				$arSelect = ["STOCK_FIT", "CHANGE_TIME"];
				foreach( $arProducts as $id => $arProduct ) {
					$details = unserialize($arProduct["DETAILS"]);
					if( $details["STOCK_FIT"] != "" ) {
						$arStocks[$id] = $details["STOCK_FIT"];
						$arFeatures = Product::getSKU($arProduct["SKU_ID"], $arProfile, $arSelect);
						$arRequest["skus"][] = [
							"sku"         => (string)$arProduct["SKU_ID"],
							"warehouseId" => (int)$arProfile["STORE"],
							"items"       => [
								[
									"count"     => (int)$details["STOCK_FIT"],
									"type"      => "FIT",
									"updatedAt" => $arFeatures["CHANGE_TIME"]
								]
							]
						];
					}
					else {
						TaskTable::delete($this->taskIDs[$id]);
					}
				}
				if (count($arRequest["skus"])) {
					$res = (new ApiRequest\setShown($arRequest))->send();
					if( $res["status"] == 200 ) {
						foreach( $arProducts as $id => $arProduct ) {
							if( array_key_exists($id, $arStocks) ) {
								ProductTable::update($id, [
									"STOCK_FIT" => $arStocks[$id],
									"STOCK_TIME" => date('d.m.Y H:i:s', time())
								]);
								TaskTable::delete($this->taskIDs[$id]);
							}
						}
					}
				}
				$steps++;
				if($steps > 500) {
					$bContinue = false;
				}
			}
			else {
				$bContinue = false;
			}
		}
	}


	protected function sendCampaignPrices(): void
	{
		$arProducts = $this->getProducts("PR", 500);
		if( count($arProducts) ) {
			$arPrices = [];
			$arOldPrices = [];
			$arResult['offers'] = [];
			$arMarketSKUs = [];
			foreach( $arProducts as $id => $arProduct ) {
				if( in_array($arProduct["SKU_ID"], $arMarketSKUs)) {
					TaskTable::delete($this->taskIDs[$id]);
				}
				else {
					$details = unserialize($arProduct["DETAILS"]);
					if( $details["PRICE"] > 0 ) {
						$arMarketSKUs[] = $arProduct["SKU_ID"];
						$new_price = $details["PRICE"];
						$old_price = $details["OLD_PRICE"];
						$arPrices[$id] = $new_price;
						$price = [
							"currencyId" => "RUR",
							"value"      => (double)$new_price,
						];
						if( $old_price > $new_price ) {
							$price["discountBase"] = (double)$old_price;
							$arOldPrices[$id] = $old_price;
						}
						$arResult['offers'][] = [
							"offerId"   => $arProduct["SKU_ID"],
							"delete"    => false,
							"price"     => $price,
						];
					}
					else {
						self::delete($this->taskIDs[$id]);
					}
				}
			}
			if (count($arResult['offers'])) {
				$res = (new ApiRequest\setPrices($arResult))->send();
				if( $res["status"] == 200 ) {
					foreach( $arProducts as $id => $arProduct ) {
						if( array_key_exists($id, $arPrices) ) {
							$arFields = [
								"PRICE"      => $arPrices[$id],
								"PRICE_TIME" => date('d.m.Y H:i:s', time())
							];
							if( isset($arOldPrices[$id]) ) {
								$arFields["OLD_PRICE"] = $arOldPrices[$id];
							}
							else {
								$arFields["OLD_PRICE"] = "";
							}
							ProductTable::update($id, $arFields);
							TaskTable::delete($this->taskIDs[$id]);
						}
					}
				}
			}
		}
	}


	protected function getProducts($type, $limit)
	{
		$rsData = TaskTable::getList(
			[
				"filter" => ["TYPE" => $type, "PROFILE_ID" => $this->profileId],
				"order"  => ["UNIX_TIMESTAMP" => "ASC"],
				'limit'  => $limit,
				'offset' => 0,
			]
		);
		$IDs = [];
		$arProducts = [];
		while( $arData = $rsData->Fetch() ) {
			$IDs[$arData["ID"]] = $arData["ENTITY_ID"];
			$this->taskIDs[$arData["ENTITY_ID"]] = $arData["ID"];
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
			foreach( $IDs as $key => $val ) {
				if(
					!isset($arProducts[$val]) ||
					!$arProducts[$val]["SKU_ID"]
				) {
					TaskTable::delete($key);
				}
			}
		}
		else {
			return false;
		}
		return $arProducts;
	}

}