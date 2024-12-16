<?php

namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class BusinessTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> BID int mandatory
 * <li> NAME string(255) optional
 * <li> API_KEY string(255) optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class BusinessTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_business';
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
					'title'   => Loc::getMessage('BUSINESS_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'BID',
				[
					'required' => true,
					'title'    => Loc::getMessage('BUSINESS_ENTITY_BID_FIELD'),
				]
			),
			new StringField(
				'NAME',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('BUSINESS_ENTITY_NAME_FIELD'),
				]
			),
			new StringField(
				'API_KEY',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('BUSINESS_ENTITY_API_KEY_FIELD'),
				]
			),
		];
	}


	/**
	 * Returns business entity by Business ID as object or array
	 *
	 * @param int $BID
	 * @param bool $asArray
	 * @return mixed
	 */
	public static function getByBid($BID, $asArray = true)
	{
		$result = static::getList(["filter" => ["BID" => $BID]]);
		if( $asArray ) {
			return $result->fetch();
		}
		return $result;
	}

}