<?php

namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class ApiLogTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> UNIX_TIMESTAMP int mandatory
 * <li> HUMAN_TIME string(19) mandatory
 * <li> TYPE string(2) mandatory
 * <li> STATE string(2) mandatory
 * <li> URL string(255) mandatory
 * <li> REQUEST_TYPE string(6) mandatory
 * <li> REQUEST text optional
 * <li> REQUEST_H text optional
 * <li> RESPOND text optional
 * <li> RESPOND_H text optional
 * <li> STATUS int optional
 * <li> ERROR string(255) optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class ApiLogTable extends DataManager
{
	public static $moduleID = "iplogic.beru";

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_api_log';
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
					'title'        => Loc::getMessage('API_LOG_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title'    => Loc::getMessage('API_LOG_ENTITY_PROFILE_ID_FIELD'),
				]
			),
			new IntegerField(
				'UNIX_TIMESTAMP',
				[
					'required' => true,
					'title'    => Loc::getMessage('API_LOG_ENTITY_UNIX_TIMESTAMP_FIELD'),
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
					'title'      => Loc::getMessage('API_LOG_ENTITY_HUMAN_TIME_FIELD'),
				]
			),
			new StringField(
				'TYPE',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 2),
						];
					},
					'title'      => Loc::getMessage('API_LOG_ENTITY_TYPE_FIELD'),
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
					'title'      => Loc::getMessage('API_LOG_ENTITY_STATE_FIELD'),
				]
			),
			new StringField(
				'URL',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('API_LOG_ENTITY_URL_FIELD'),
				]
			),
			new StringField(
				'REQUEST_TYPE',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 6),
						];
					},
					'title'      => Loc::getMessage('API_LOG_ENTITY_REQUEST_TYPE_FIELD'),
				]
			),
			new TextField(
				'REQUEST',
				[
					'title' => Loc::getMessage('API_LOG_ENTITY_REQUEST_FIELD'),
				]
			),
			new TextField(
				'REQUEST_H',
				[
					'title' => Loc::getMessage('API_LOG_ENTITY_REQUEST_H_FIELD'),
				]
			),
			new TextField(
				'RESPOND',
				[
					'title' => Loc::getMessage('API_LOG_ENTITY_RESPOND_FIELD'),
				]
			),
			new TextField(
				'RESPOND_H',
				[
					'title' => Loc::getMessage('API_LOG_ENTITY_RESPOND_H_FIELD'),
				]
			),
			new IntegerField(
				'STATUS',
				[
					'title' => Loc::getMessage('API_LOG_ENTITY_STATUS_FIELD'),
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
					'title'      => Loc::getMessage('API_LOG_ENTITY_ERROR_FIELD'),
				]
			),
		];
	}


	public static function add(array $arFields)
	{
		$arFields["UNIX_TIMESTAMP"] = time();
		$arFields["HUMAN_TIME"] = date('d.m.Y H:i:s');
		$arFields["STATE"] = "EX";
		return parent::add($arFields);
	}

	public static function update($ID, array $arFields)
	{
		if( $arFields["close"] ) {
			if( Option::get(self::$moduleID, 'use_log', 'Y') == "N" ) {
				return self::delete($ID);
			}
			if( Option::get(self::$moduleID, 'dont_log_ok', 'N') == "Y" && $arFields["STATE"] == "OK" ) {
				return self::delete($ID);
			}
		}
		return parent::update($ID, $arFields);
	}

	public static function clear()
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "TRUNCATE TABLE " . $helper->quote(self::getTableName());
		$rsData = $conn->query($strSql);
		unset($helper, $conn);
		return true;
	}

	public static function clearOld($time)
	{
		if( $time < 1 ) {
			return false;
		}
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql =
			"DELETE FROM " . $helper->quote(self::getTableName()) . " WHERE " . $helper->quote('UNIX_TIMESTAMP') . "<" .
			$time;
		$rsData = $conn->query($strSql);
		unset($helper, $conn);
		return true;
	}
}