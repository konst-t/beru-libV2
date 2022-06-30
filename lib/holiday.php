<?php
namespace Iplogic\Beru;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class HolidayTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> DELIVERY_ID int mandatory
 * <li> DATE string(10) mandatory
 * <li> TIMESTAMP int mandatory
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class HolidayTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_holiday';
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
					'title' => Loc::getMessage('HOLIDAY_ENTITY_ID_FIELD')
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('HOLIDAY_ENTITY_PROFILE_ID_FIELD')
				]
			),
			new IntegerField(
				'DELIVERY_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('HOLIDAY_ENTITY_DELIVERY_ID_FIELD')
				]
			),
			new StringField(
				'DATE',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateDate'],
					'title' => Loc::getMessage('HOLIDAY_ENTITY_DATE_FIELD')
				]
			),
			new IntegerField(
				'TIMESTAMP',
				[
					'required' => true,
					'title' => Loc::getMessage('HOLIDAY_ENTITY_TIMESTAMP_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for DATE field.
	 *
	 * @return array
	 */
	public static function validateDate()
	{
		return [
			new LengthValidator(null, 10),
		];
	}
}
