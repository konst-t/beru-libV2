<?php
namespace Iplogic\Beru;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\ORM\Data\DataManager,
	Bitrix\Main\ORM\Fields\IntegerField,
	Bitrix\Main\ORM\Fields\StringField,
	Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class IntervalTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> DELIVERY_ID int mandatory
 * <li> DAY string(3) mandatory
 * <li> TIME_FROM string(5) mandatory
 * <li> TIME_TO string(5) mandatory
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class IntervalTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_interval';
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
					'title' => Loc::getMessage('INTERVAL_ENTITY_ID_FIELD')
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
				'DAY',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateDay'],
					'title' => Loc::getMessage('INTERVAL_ENTITY_DAY_FIELD')
				]
			),
			new StringField(
				'TIME_FROM',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateTimeFrom'],
					'title' => Loc::getMessage('INTERVAL_ENTITY_TIME_FROM_FIELD')
				]
			),
			new StringField(
				'TIME_TO',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateTimeTo'],
					'title' => Loc::getMessage('INTERVAL_ENTITY_TIME_TO_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for DAY field.
	 *
	 * @return array
	 */
	public static function validateDay()
	{
		return [
			new LengthValidator(null, 3),
		];
	}

	/**
	 * Returns validators for TIME_FROM field.
	 *
	 * @return array
	 */
	public static function validateTimeFrom()
	{
		return [
			new LengthValidator(null, 5),
		];
	}

	/**
	 * Returns validators for TIME_TO field.
	 *
	 * @return array
	 */
	public static function validateTimeTo()
	{
		return [
			new LengthValidator(null, 5),
		];
	}
}