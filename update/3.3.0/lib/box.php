<?php

namespace Iplogic\Beru;

use \Bitrix\Main,
	\Bitrix\Main\Application,
	\Bitrix\Main\Localization\Loc,
	\Bitrix\Main\ORM\Data\DataManager,
	\Bitrix\Main\ORM\Fields\IntegerField;

IncludeModuleLangFile(Application::getDocumentRoot().BX_ROOT."/modules/iplogic.beru/lib/lib.php");

/**
 * Class BoxTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> PROFILE_ID int mandatory
 * <li> ORDER_ID int mandatory
 * <li> EXT_ID int optional default 0
 * <li> NUM int optional
 * <li> WEIGHT int optional
 * <li> WIDTH int optional
 * <li> HEIGHT int optional
 * <li> DEPTH int optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/

class BoxTable extends DataManager
{

	public static $moduleID = "iplogic.beru";

	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_box';
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
					'title' => Loc::getMessage('BOX_ENTITY_ID_FIELD')
				]
			),
			new IntegerField(
				'PROFILE_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('BOX_ENTITY_PROFILE_ID_FIELD')
				]
			),
			new IntegerField(
				'EXT_ID',
				[
					'default' => 0,
					'title' => Loc::getMessage('BOX_ENTITY_EXT_ID_FIELD')
				]
			),
			new IntegerField(
				'ORDER_ID',
				[
					'required' => true,
					'title' => Loc::getMessage('BOX_ENTITY_ORDER_ID_FIELD')
				]
			),
			new IntegerField(
				'NUM',
				[
					'title' => Loc::getMessage('BOX_ENTITY_NUM_FIELD')
				]
			),
			new IntegerField(
				'WEIGHT',
				[
					'title' => Loc::getMessage('BOX_ENTITY_WEIGHT_FIELD')
				]
			),
			new IntegerField(
				'WIDTH',
				[
					'title' => Loc::getMessage('BOX_ENTITY_WIDTH_FIELD')
				]
			),
			new IntegerField(
				'HEIGHT',
				[
					'title' => Loc::getMessage('BOX_ENTITY_HEIGHT_FIELD')
				]
			),
			new IntegerField(
				'DEPTH',
				[
					'title' => Loc::getMessage('BOX_ENTITY_DEPTH_FIELD')
				]
			),
		];
	}

	public static function getById($ID) 
	{
		$result = parent::getById($ID);
		return $result->Fetch();
	}

	public static function getCountInOrder($ID) 
	{
		$conn = Application::getConnection(); 
		$helper = $conn->getSqlHelper();
		$strSql = "SELECT COUNT(*) AS count FROM ".$helper->quote(self::getTableName())." WHERE ".$helper->quote('ORDER_ID')."=".$ID;
		$result = $conn->query($strSql);
		unset($helper, $conn);
		$ar_res = $result->Fetch();
		return $ar_res["count"];
	}

	public static function delete($ID)
	{
		$conn = Application::getConnection();
		$helper = $conn->getSqlHelper();
		$strSql = "DELETE FROM `b_iplogicberu_box_link` WHERE ".$helper->quote('BOX_ID')."=".$ID;
		$conn->query($strSql);
		unset($helper, $conn);
		return parent::delete($ID);
	}
}