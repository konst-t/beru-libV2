<?php
namespace Iplogic\Beru;

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot().BX_ROOT."/modules/iplogic.beru/lib/lib.php");

/**
 * Class RightsTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> ENTITY_TYPE string(20) mandatory
 * <li> ENTITY_ID int mandatory
 * <li> GROUP_ID int mandatory
 * <li> TASK_ID int mandatory
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class RightsTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_rights';
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
					'primary' => true,
					'autocomplete' => true,
					'title' => Loc::getMessage('RIGHTS_ENTITY_ID_FIELD')
				]
			),
			new StringField(
				'ENTITY_TYPE',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateEntityType'],
					'title' => Loc::getMessage('RIGHTS_ENTITY_ENTITY_TYPE_FIELD')
				]
			),
			new IntegerField(
				'ENTITY_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('RIGHTS_ENTITY_ENTITY_ID_FIELD')
				]
			),
			new IntegerField(
				'GROUP_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('RIGHTS_ENTITY_GROUP_ID_FIELD')
				]
			),
			new IntegerField(
				'TASK_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('RIGHTS_ENTITY_TASK_ID_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for ENTITY_TYPE field.
	 *
	 * @return array
	 */
	public static function validateEntityType()
	{
		return [
			new LengthValidator(null, 20),
		];
	}

	public static function deleteByGroup($ID) {
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "DELETE FROM " . self::getTableName() . " WHERE GROUP_ID = " . $ID;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		return $result;
	}
}