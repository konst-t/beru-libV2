<?php

namespace Iplogic\Beru;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\Application;
use \Bitrix\Main\ORM\Fields\BooleanField;
use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot().BX_ROOT."/modules/iplogic.beru/lib/lib.php");

/**
 * Class BoxLinkTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> ORDER_ID int mandatory
 * <li> BOX_ID int mandatory
 * <li> SKU_ID string(150) mandatory
 * <li> ORDER_PROD_ID int optional
 * <li> IS_PART bool ('N', 'Y') optional default 'N'
 * <li> QUANTITY int optional
 * <li> PART int optional
 * <li> PARTS int optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class BoxLinkTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_box_link';
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
					'title' => Loc::getMessage('BOX_LINK_ENTITY_ID_FIELD'),
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('BOX_LINK_ENTITY_PROFILE_ID_FIELD'),
				]
			),
			new IntegerField(
				'ORDER_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('BOX_LINK_ENTITY_ORDER_ID_FIELD'),
				]
			),
			new IntegerField(
				'BOX_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('BOX_LINK_ENTITY_BOX_ID_FIELD'),
				]
			),
			new StringField(
				'SKU_ID',
				[
					'required' => true,
					'validation' => function()
					{
						return[
							new LengthValidator(null, 150),
						];
					},
					'title' => Loc::getMessage('BOX_LINK_ENTITY_SKU_ID_FIELD'),
				]
			),
			new IntegerField(
				'ORDER_PROD_ID',
				[
					'title' => Loc::getMessage('BOX_LINK_ENTITY_ORDER_PROD_ID_FIELD'),
				]
			),
			new BooleanField(
				'IS_PART',
				[
					'values' => array('N', 'Y'),
					'default' => 'N',
					'title' => Loc::getMessage('BOX_LINK_ENTITY_IS_PART_FIELD'),
				]
			),
			new IntegerField(
				'QUANTITY',
				[
					'title' => Loc::getMessage('BOX_LINK_ENTITY_QUANTITY_FIELD'),
				]
			),
			new IntegerField(
				'PART',
				[
					'title' => Loc::getMessage('BOX_LINK_ENTITY_PART_FIELD'),
				]
			),
			new IntegerField(
				'PARTS',
				[
					'title' => Loc::getMessage('BOX_LINK_ENTITY_PARTS_FIELD'),
				]
			),
		];
	}
}