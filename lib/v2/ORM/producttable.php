<?php

namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class ProductTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> PRODUCT_ID int optional
 * <li> SKU_ID string(150) optional
 * <li> MARKET_SKU int optional
 * <li> NAME text optional
 * <li> VENDOR string(255) optional
 * <li> AVAILABILITY bool ('N', 'Y') optional default 'N'
 * <li> STATE string(100) optional
 * <li> REJECT_REASON string(255) optional
 * <li> REJECT_NOTES text optional
 * <li> DETAILS text optional
 * <li> PRICE string(12) optional
 * <li> OLD_PRICE string(12) optional
 * <li> STOCK_FIT string(5) optional
 * <li> PRICE_TIME string(19) optional
 * <li> STOCK_TIME string(19) optional
 * <li> HIDDEN bool ('N', 'Y') optional default 'N'
 * <li> FOR_DELETE bool ('N', 'Y') optional default 'N'
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class ProductTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_product';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			new IntegerField(
				'ID',
				[
					'primary'      => true,
					'autocomplete' => true,
					'title'        => Loc::getMessage('PRODUCT_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title'    => Loc::getMessage('PRODUCT_ENTITY_PROFILE_ID_FIELD'),
				]
			),
			new IntegerField(
				'PRODUCT_ID',
				[
					'title' => Loc::getMessage('PRODUCT_ENTITY_PRODUCT_ID_FIELD'),
				]
			),
			new StringField(
				'SKU_ID',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 150),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_SKU_ID_FIELD'),
				]
			),
			new IntegerField(
				'MARKET_SKU',
				[
					'title' => Loc::getMessage('PRODUCT_ENTITY_MARKET_SKU_FIELD'),
					'size'  => 8,
				]
			),
			new TextField(
				'NAME',
				[
					'title' => Loc::getMessage('PRODUCT_ENTITY_NAME_FIELD'),
				]
			),
			new StringField(
				'VENDOR',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_VENDOR_FIELD'),
				]
			),
			new BooleanField(
				'AVAILABILITY',
				[
					'values'  => ['N', 'Y'],
					'default' => 'N',
					'title'   => Loc::getMessage('PRODUCT_ENTITY_AVAILABILITY_FIELD'),
				]
			),
			new StringField(
				'STATE',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 100),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_STATE_FIELD'),
				]
			),
			new StringField(
				'REJECT_REASON',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_REJECT_REASON_FIELD'),
				]
			),
			new TextField(
				'REJECT_NOTES',
				[
					'title' => Loc::getMessage('PRODUCT_ENTITY_REJECT_NOTES_FIELD'),
				]
			),
			new TextField(
				'DETAILS',
				[
					'title' => Loc::getMessage('PRODUCT_ENTITY_DETAILS_FIELD'),
				]
			),
			new StringField(
				'PRICE',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 12),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_PRICE_FIELD'),
				]
			),
			new StringField(
				'OLD_PRICE',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 12),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_OLD_PRICE_FIELD'),
				]
			),
			new StringField(
				'STOCK_FIT',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 5),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_STOCK_FIT_FIELD'),
				]
			),
			new StringField(
				'PRICE_TIME',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 19),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_PRICE_TIME_FIELD'),
				]
			),
			new StringField(
				'STOCK_TIME',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 19),
						];
					},
					'title'      => Loc::getMessage('PRODUCT_ENTITY_STOCK_TIME_FIELD'),
				]
			),
			new BooleanField(
				'HIDDEN',
				[
					'values'  => ['N', 'Y'],
					'default' => 'N',
					'title'   => Loc::getMessage('PRODUCT_ENTITY_HIDDEN_FIELD'),
				]
			),
			new BooleanField(
				'FOR_DELETE',
				[
					'values'  => ['N', 'Y'],
					'default' => 'N',
					'title'   => Loc::getMessage('PRODUCT_ENTITY_FOR_DELETE_FIELD'),
				]
			),
		];
	}

	public static function getBySkuId($ID, $PROFILE_ID)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"SELECT * FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('SKU_ID') . "='" .
			$ID . "' AND " . $helper->quote('PROFILE_ID') . "=" . $PROFILE_ID;  //echo $strSql;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return $result->Fetch();
	}


	public static function getByProductId($ID)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"SELECT * FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('PRODUCT_ID') . "='" .
			$ID . "'";  //echo $strSql;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return $result;
	}


	public static function markAllForDelete()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("UPDATE " . $helper->quote(self::getTableName()) . " SET FOR_DELETE='Y'");
		unset($helper, $conn);
		return;
	}


	public static function deleteMarked()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE FOR_DELETE='Y'");
		unset($helper, $conn);
		return;
	}

	public static function deleteByProfile($profileId)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$conn->query("DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE PROFILE_ID=" . $profileId);
		unset($helper, $conn);
		return;
	}

}