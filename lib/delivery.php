<?php
namespace Iplogic\Beru;

use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields\BooleanField;
use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

/**
 * Class DeliveryTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> ACTIVE bool optional default 'Y'
 * <li> SORT int optional default 100
 * <li> TYPE string(8) mandatory
 * <li> NAME string(50) mandatory
 * <li> PAYMENT_ALLOW bool ('N', 'Y') optional default 'N'
 * <li> DAY_FROM string(3) mandatory
 * <li> DAY_TO string(3) mandatory
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class DeliveryTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_delivery';
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
					'title' => Loc::getMessage('DELIVERY_ENTITY_ID_FIELD')
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('DELIVERY_ENTITY_PROFILE_ID_FIELD')
				]
			),
			new BooleanField(
				'ACTIVE',
				[
					'values' => array('N', 'Y'),
					'default' => 'Y',
					'title' => Loc::getMessage('DELIVERY_ENTITY_ACTIVE_FIELD')
				]
			),
			new IntegerField(
				'SORT',
				[
					'default' => 100,
					'title' => Loc::getMessage('DELIVERY_ENTITY_SORT_FIELD')
				]
			),
			new StringField(
				'TYPE',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateType'],
					'title' => Loc::getMessage('DELIVERY_ENTITY_TYPE_FIELD')
				]
			),
			new StringField(
				'NAME',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateName'],
					'title' => Loc::getMessage('DELIVERY_ENTITY_NAME_FIELD')
				]
			),
			new BooleanField(
				'PAYMENT_ALLOW',
				[
					'values' => array('N', 'Y'),
					'default' => 'N',
					'title' => Loc::getMessage('DELIVERY_ENTITY_PAYMENT_ALLOW_FIELD')
				]
			),
			new StringField(
				'DAY_FROM',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateDayFrom'],
					'title' => Loc::getMessage('DELIVERY_ENTITY_DAY_FROM_FIELD')
				]
			),
			new StringField(
				'DAY_TO',
				[
					'required' => true,
					'validation' => [__CLASS__, 'validateDayTo'],
					'title' => Loc::getMessage('DELIVERY_ENTITY_DAY_TO_FIELD')
				]
			),
		];
	}

	/**
	 * Returns validators for TYPE field.
	 *
	 * @return array
	 */
	public static function validateType()
	{
		return [
			new LengthValidator(null, 8),
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
			new LengthValidator(null, 50),
		];
	}

	/**
	 * Returns validators for DAY_FROM field.
	 *
	 * @return array
	 */
	public static function validateDayFrom()
	{
		return [
			new LengthValidator(null, 3),
		];
	}

	/**
	 * Returns validators for DAY_TO field.
	 *
	 * @return array
	 */
	public static function validateDayTo()
	{
		return [
			new LengthValidator(null, 3),
		];
	}


	public static function delete($ID)
	{
		$result = parent::delete($ID);
		if ($result->isSuccess()) {
			$conn = Application::getConnection();
			$helper = $conn->getSqlHelper();
			$conn->query("DELETE FROM b_iplogicberu_interval WHERE PROFILE_ID=".$ID);
			$conn->query("DELETE FROM b_iplogicberu_delivery WHERE PROFILE_ID=".$ID);
			unset($helper, $conn);
		}
		if ($result->isSuccess())
			return $result;
		else
			return ["error"=>$result->getErrorMessages()];
	}
}