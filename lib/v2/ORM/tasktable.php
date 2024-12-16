<?php

namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Iplogic\Beru\V2\Task;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class TaskTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> UNIX_TIMESTAMP int mandatory
 * <li> HUMAN_TIME string(19) mandatory
 * <li> TYPE string(20) optional
 * <li> STATE string(2) mandatory
 * <li> ENTITY_ID string(255) optional
 * <li> TRYING int optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class TaskTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_task';
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
					'title'        => Loc::getMessage('TASK_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title'    => Loc::getMessage('TASK_ENTITY_PROFILE_ID_FIELD'),
				]
			),
			new IntegerField(
				'UNIX_TIMESTAMP',
				[
					'required' => true,
					'title'    => Loc::getMessage('TASK_ENTITY_UNIX_TIMESTAMP_FIELD'),
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
					'title'      => Loc::getMessage('TASK_ENTITY_HUMAN_TIME_FIELD'),
				]
			),
			new StringField(
				'TYPE',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 20),
						];
					},
					'title'      => Loc::getMessage('TASK_ENTITY_TYPE_FIELD'),
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
					'title'      => Loc::getMessage('TASK_ENTITY_STATE_FIELD'),
				]
			),
			new StringField(
				'ENTITY_ID',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('TASK_ENTITY_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'TRYING',
				[
					'title' => Loc::getMessage('TASK_ENTITY_TRYING_FIELD'),
				]
			),
		];
	}


	/**
	 * Adds task row to entity table
	 *
	 * @param array $arFields
	 * @return object AddResult Contains ID of inserted row
	 */
	public static function add(array $arFields): AddResult
	{
		$arFields["HUMAN_TIME"] = date('d.m.Y H:i:s', $arFields["UNIX_TIMESTAMP"]);
		return parent::add($arFields);
	}


	/**
	 * Updates task row in entity table
	 *
	 * @param int $ID
	 * @param array $arFields
	 * @return UpdateResult
	 */
	public static function update($ID, array $arFields): UpdateResult
	{
		if( isset($arFields["UNIX_TIMESTAMP"]) ) {
			$arFields["HUMAN_TIME"] = date('d.m.Y H:i:s', $arFields["UNIX_TIMESTAMP"]);
		}
		return parent::update($ID, $arFields);
	}


	/**
	 * Deletes all entries from the task table
	 */
	public static function clear(): void
	{
		$conn = Application::getConnection();
		$strSql = "TRUNCATE TABLE " . static::getTableName();
		$conn->query($strSql);
	}


	/**
	 * Removes all entries of the specified profile from the task table.
	 *
	 * @param int $ID
	 */
	public static function deleteByProfileId($ID): void
	{
		$conn = Application::getConnection();
		$conn->query("DELETE FROM " . static::getTableName() . " WHERE PROFILE_ID=" . $ID);
	}


	/**
	 * Returns the entity of the next task to execute
	 *
	 * @return mixed
	 */
	public static function getNextTask()
	{
		$result = static::getList(["filter" => ["<=UNIX_TIMESTAMP" => time(), "=STATE" => "WT", "=TYPE" => "CT"]]);
		$task = $result->fetch();
		if( !$task ) {
			Task::scheduleTask(0, "CT", 600);
			$result = static::getList(
				[
					"filter" => ["<=UNIX_TIMESTAMP" => time(), "=STATE" => "WT", "=TYPE" => "SP"],
					"order"  => ["UNIX_TIMESTAMP" => "ASC"],
				]
			);
			$task = $result->fetch();
			if( !$task ) {
				$result = static::getList(
					[
						"filter" => [
							"<=UNIX_TIMESTAMP" => time(),
							"=STATE"           => "WT",
							"!@TYPE"           => ["HP", "UP", "SP", "ST", "PR"],
						],
						"order"  => ["UNIX_TIMESTAMP" => "ASC"],
					]
				);
				$task = $result->fetch();
			}
		}
		return $task;
	}

}