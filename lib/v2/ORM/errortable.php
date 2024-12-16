<?php

namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class ErrorTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> UNIX_TIMESTAMP int mandatory
 * <li> HUMAN_TIME string(19) mandatory
 * <li> ERROR string(255) optional
 * <li> DETAILS text mandatory
 * <li> STATE string(2) mandatory
 * <li> LOG int optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class ErrorTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_error';
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
					'title'        => Loc::getMessage('ERROR_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title'    => Loc::getMessage('ERROR_ENTITY_PROFILE_ID_FIELD'),
				]
			),
			new IntegerField(
				'UNIX_TIMESTAMP',
				[
					'required' => true,
					'title'    => Loc::getMessage('ERROR_ENTITY_UNIX_TIMESTAMP_FIELD'),
				]
			),
			new StringField(
				'HUMAN_TIME',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 19),
						];
					},
					'title'      => Loc::getMessage('ERROR_ENTITY_HUMAN_TIME_FIELD'),
				]
			),
			new StringField(
				'ERROR',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('ERROR_ENTITY_ERROR_FIELD'),
				]
			),
			new TextField(
				'DETAILS',
				[
					'required' => true,
					'title'    => Loc::getMessage('ERROR_ENTITY_DETAILS_FIELD'),
				]
			),
			new StringField(
				'STATE',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 2),
						];
					},
					'title'      => Loc::getMessage('ERROR_ENTITY_STATE_FIELD'),
				]
			),
			new IntegerField(
				'LOG',
				[
					'title' => Loc::getMessage('ERROR_ENTITY_LOG_FIELD'),
				]
			),
		];
	}

	public static function add(array $arFields)
	{
		$arFields["UNIX_TIMESTAMP"] = time();
		$arFields["HUMAN_TIME"] = date('d.m.Y H:i:s');
		$arFields["STATE"] = "NW";
		return parent::add($arFields);
	}


	public static function newCount()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "SELECT COUNT(*) AS count FROM " . $helper->quote(self::getTableName()) . " WHERE " .
			$helper->quote('STATE') . "='NW'";
		$result = $conn->query($strSql);
		unset($helper, $conn);
		$ar_res = $result->Fetch();
		return $ar_res["count"];
	}


	public static function clearRead()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('STATE') . "='RD'";
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return true;
	}


	public static function allRead()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"UPDATE " . $helper->quote(self::getTableName()) . " SET " . $helper->quote('STATE') . "='RD' WHERE " .
			$helper->quote('STATE') . "='NW'";
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return true;
	}
}