<?php

namespace Iplogic\Beru\V2\Command;

use \Iplogic\Beru\V2\Product;
use \Iplogic\Beru\V2\Task;
use \Iplogic\Beru\V2\ORM\ProfileTable;
use \Iplogic\Beru\V2\ORM\ProductTable;

/**
 * Updates product cache
 *
 * Class updateCache
 * @package Iplogic\Beru\V2\Command
 */
class updateCache implements CommandInterface
{
	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";

	/**
	 * @var string
	 */
	protected $status = "Not started";

	/**
	 * @var int
	 */
	protected $ID;

	public function execute(): void
	{
		$this->status = "Started";
		if( $product = ProductTable::getRowById($this->ID) ) {
			if( $product["PRODUCT_ID"] == 0 || $product["PRODUCT_ID"] == "" ) {
				$this->status = "Error: Product ID is empty";
				return;
			}
			$arProfile = ProfileTable::getRowById($product["PROFILE_ID"]);
			if( $arProfile["ACTIVE"] != "Y" ) {
				$this->status = "Error: Product is not empty";
				return;
			}
			$set = Product::getSKU($product["SKU_ID"], [], true);
			if( $set["STOCK_FIT"] === NULL || $set["STOCK_FIT"] === "" ) {
				$set["STOCK_FIT"] = 0;
			}
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventsList = $eventManager->findEventHandlers('iplogic.beru', 'OnIplogicBeruBeforeProductCacheSave');
			foreach( $eventsList as $arEvent ) {
				if( ExecuteModuleEventEx($arEvent, [$product["PRODUCT_ID"], &$set]) === false ) {
					$this->status = "Stopped by event handler";
					return;
				}
			}
			if(
				(   // very old statuses
					$product["STATE"] == "READY" ||
					$product["STATE"] == "NEED_CONTENT" ||
					// old statuses
					$product["STATE"] == "PUBLISHED" ||
					$product["STATE"] == "NO_STOCKS" ||
					// new statuses
					$product["STATE"] == "HAS_CARD_CAN_NOT_UPDATE" ||
					$product["STATE"] == "HAS_CARD_CAN_UPDATE" ||
					$product["STATE"] == "HAS_CARD_CAN_UPDATE_ERRORS" ||
					$product["STATE"] == "HAS_CARD_CAN_UPDATE_PROCESSING") &&
				$product["PRODUCT_ID"] > 0
			) {
				if( intval($product["PRICE"]) != intval($set["PRICE"]) && intval($set["PRICE"]) > 0 ) {
					Task::addPriceUpdateTask($this->ID, $product["PROFILE_ID"]);
				}
				if(
					intval($product["OLD_PRICE"]) != intval($set["OLD_PRICE"]) &&
					intval($set["OLD_PRICE"]) > intval($set["PRICE"])
				) {
					Task::addPriceUpdateTask($this->ID, $product["PROFILE_ID"]);
				}
				if( $product["STOCK_FIT"] !== $set["STOCK_FIT"] ) {
					Task::addStockUpdateTask($this->ID, $product["PROFILE_ID"]);
				}
			}
			$eventManager = \Bitrix\Main\EventManager::getInstance();
			$eventsList = $eventManager->findEventHandlers('iplogic.beru', 'OnIplogicBeruProductCacheSave');
			foreach( $eventsList as $arEvent ) {
				if( ExecuteModuleEventEx($arEvent, [$product["PRODUCT_ID"], &$set]) === false ) {
					$this->status = "Stopped by event handler";
					return;
				}
			}
			$cache = serialize($set);
			$arFields = ["DETAILS" => $cache];
			$this->status = "Finished";
			ProductTable::update($this->ID, $arFields);
		}
		$this->status = "Product not found";
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setParams($arParams): void
	{
		$this->ID = $arParams["ID"];
	}
}