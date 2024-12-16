<?php
namespace Iplogic\Beru\V2;

use \Iplogic\Beru\V2\ORM\ProductTable;
use \Iplogic\Beru\V2\ORM\TaskTable;

/**
 * Event handlers
 *
 * Class Event
 * @package Iplogic\Beru\V2
 */
class Event
{

	public static function iblockAfterUpdateHandler($arFields)
	{
		self::setUpdateTask($arFields["ID"]);
	}


	public static function productUpdateHandler($ID, $arFields)
	{
		self::setUpdateTask($ID);
	}


	public static function priceUpdateHandler($obEvent)
	{
		$arEventParam = $obEvent->getParameters();
		$ID = $arEventParam["fields"]["PRODUCT_ID"];
		self::setUpdateTask($ID);
	}


	public static function storeUpdateHandler($ID, $arFields)
	{
		self::setUpdateTask($ID);
	}


	public static function productDeleteHandler($ID)
	{
		$rsProducts = ProductTable::getByProductId($ID);
		while( $arProduct = $rsProducts->Fetch() ) {
			$arFields = [];
			$arFields["DETAILS"] = null;
			$arFields["PRODUCT_ID"] = 0;
			ProductTable::update($arProduct["ID"], $arFields);
		}
	}


	public static function discountAddHandler($ID, $arFields)
	{
		self::discountUpdate($ID);
	}


	public static function discountUpdateHandler($ID, $arFields)
	{
		self::discountUpdate($ID);
	}


	public static function discountDeleteHandler($ID)
	{
		$dbProductDiscounts = \CCatalogDiscount::GetList(
			[],
			[
				"ID" => $ID,
				"ACTIVE" => "Y",
				"COUPON" => ""
			],
			false,
			false,
			["ID", "PRODUCT_ID"]
		);
		while ($arProductDiscounts = $dbProductDiscounts->Fetch())
		{
			self::setUpdateTask($arProductDiscounts["PRODUCT_ID"]);
		}
	}


	public static function groupDeleteHandler($ID) {
		\Iplogic\Beru\V2\ORM\RightsTable::deleteByGroup($ID);
	}


	protected static function discountUpdate($ID)
	{
		$dbProductDiscounts = \CCatalogDiscount::GetList(
			[],
			[
				"ID" => $ID,
				"ACTIVE" => "Y",
				"COUPON" => ""
			],
			false,
			false,
			["ID", "ACTIVE_FROM", "ACTIVE_TO", "PRODUCT_ID"]
		);
		while ($arProductDiscounts = $dbProductDiscounts->Fetch())
		{
			if($arProductDiscounts["ACTIVE_FROM"] != "") {
				self::setDeferredUpdateTask($arProductDiscounts["PRODUCT_ID"], $arProductDiscounts["ACTIVE_FROM"]);
			}
			else {
				self::setUpdateTask($arProductDiscounts["PRODUCT_ID"]);
			}
			if($arProductDiscounts["ACTIVE_TO"] != "") {
				self::setDeferredUpdateTask($arProductDiscounts["PRODUCT_ID"], $arProductDiscounts["ACTIVE_TO"]);
			}
		}
	}


	protected static function setUpdateTask($ID)
	{
		$rsProducts = ProductTable::getByProductId($ID);
		while( $arProduct = $rsProducts->Fetch() ) {
			$arProfile = ProfileTable::getById($arProduct["PROFILE_ID"]);
			if( $arProfile["ACTIVE"] != "Y" ) {
				continue;
			}
			$rsTask = TaskTable::getList(
				[
					"filter" => [
						"TYPE"       => "PU",
						"STATE"      => "WT",
						"ENTITY_ID"  => $arProduct["ID"],
						"PROFILE_ID" => $arProduct["PROFILE_ID"],
					],
				]
			);
			if( !$rsTask->Fetch() ) {
				$arFields = [
					"PROFILE_ID"     => $arProduct["PROFILE_ID"],
					"UNIX_TIMESTAMP" => time(),
					"TYPE"           => "PU",
					"STATE"          => "WT",
					"ENTITY_ID"      => $arProduct["ID"],
					"TRYING"         => 0,
				];
				TaskTable::add($arFields);
			}
		}
	}


	protected static function setDeferredUpdateTask($ID, $time)
	{
		$rsProducts = ProductTable::getByProductId($ID);
		while( $arProduct = $rsProducts->Fetch() ) {
			$arProfile = ProfileTable::getRowById($arProduct["PROFILE_ID"]);
			if( $arProfile["ACTIVE"] != "Y" ) {
				continue;
			}
			$arFields = [
				"PROFILE_ID"     => $arProduct["PROFILE_ID"],
				"UNIX_TIMESTAMP" => strtotime($time),
				"TYPE"           => "DU",
				"STATE"          => "WT",
				"ENTITY_ID"      => $arProduct["ID"],
				"TRYING"         => 0,
			];
			TaskTable::add($arFields);
		}
	}

}