<?php
namespace Iplogic\Beru;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class OutletTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> DELIVERY_ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> CODE int mandatory
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class OutletTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_outlet';
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
					'title' => Loc::getMessage('OUTLET_ENTITY_ID_FIELD')
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('OUTLET_ENTITY_PROFILE_ID_FIELD')
				]
			),
			new IntegerField(
				'DELIVERY_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('OUTLET_ENTITY_DELIVERY_ID_FIELD')
				]
			),
			new StringField(
				'NAME',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateName'],
					'title' => Loc::getMessage('OUTLET_ENTITY_NAME_FIELD')
				]
			),
			new IntegerField(
				'CODE',
				[
					'required' => true,
					'title' => Loc::getMessage('OUTLET_ENTITY_CODE_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for NAME field.
	 *
	 * @return array
	 */
	public static function validateName()
	{
		return [
			new LengthValidator(null, 255),
		];
	}
}