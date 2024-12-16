<?php
namespace Iplogic\Beru\V2\ORM;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

IncludeModuleLangFile(Application::getDocumentRoot() . BX_ROOT . "/modules/iplogic.beru/lib/lib.php");

/**
 * Class ProfileTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> ACTIVE bool ('N', 'Y') optional default 'Y'
 * <li> SORT int optional default 100
 * <li> SITE string(2) mandatory
 * <li> SCHEME string(3) optional
 * <li> IBLOCK_TYPE string(50) mandatory
 * <li> IBLOCK_ID int mandatory
 * <li> COMPANY string(255) optional
 * <li> TAX_SYSTEM string(14) optional
 * <li> VAT string(6) optional
 * <li> BASE_URL string(100) optional
 * <li> CLIENT_ID string(255) optional
 * <li> COMPAIN_ID string(100) optional
 * <li> SEND_TOKEN string(255) optional
 * <li> GET_TOKEN string(255) optional
 * <li> STORE string(255) optional
 * <li> BUSINESS_ID string(100) optional
 * <li> FORMAT string(8) optional default 'json'
 * <li> USER_ID int optional
 * <li> DELIVERY int optional
 * <li> PAYMENTS int optional
 * <li> PERSON_TYPE int optional
 * <li> STATUSES text optional
 * <li> PAYMENT_METHODS string(255) optional
 * </ul>
 *
 * @package Iplogic\Beru
 **/
class ProfileTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iplogicberu_profile';
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
					'title'        => Loc::getMessage('PROFILE_ENTITY_ID_FIELD'),
				]
			),
			new StringField(
				'NAME',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_NAME_FIELD'),
				]
			),
			new BooleanField(
				'ACTIVE',
				[
					'values'  => ['N', 'Y'],
					'default' => 'Y',
					'title'   => Loc::getMessage('PROFILE_ENTITY_ACTIVE_FIELD'),
				]
			),
			new IntegerField(
				'SORT',
				[
					'default' => 100,
					'title'   => Loc::getMessage('PROFILE_ENTITY_SORT_FIELD'),
				]
			),
			new StringField(
				'SITE',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 2),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_SITE_FIELD'),
				]
			),
			new StringField(
				'SCHEME',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 3),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_SCHEME_FIELD'),
				]
			),
			new StringField(
				'IBLOCK_TYPE',
				[
					'required'   => true,
					'validation' => function() {
						return [
							new LengthValidator(null, 50),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_IBLOCK_TYPE_FIELD'),
				]
			),
			new IntegerField(
				'IBLOCK_ID',
				[
					'required' => true,
					'title'    => Loc::getMessage('PROFILE_ENTITY_IBLOCK_ID_FIELD'),
				]
			),
			new StringField(
				'COMPANY',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_COMPANY_FIELD'),
				]
			),
			new StringField(
				'TAX_SYSTEM',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 14),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_TAX_SYSTEM_FIELD'),
				]
			),
			new StringField(
				'VAT',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 6),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_VAT_FIELD'),
				]
			),
			new StringField(
				'BASE_URL',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 100),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_BASE_URL_FIELD'),
				]
			),
			new StringField(
				'CLIENT_ID',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_CLIENT_ID_FIELD'),
				]
			),
			new StringField(
				'COMPAIN_ID',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 100),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_COMPAIN_ID_FIELD'),
				]
			),
			new StringField(
				'SEND_TOKEN',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_SEND_TOKEN_FIELD'),
				]
			),
			new StringField(
				'GET_TOKEN',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_GET_TOKEN_FIELD'),
				]
			),
			new StringField(
				'STORE',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_STORE_FIELD'),
				]
			),
			new StringField(
				'BUSINESS_ID',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 100),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_BUSINESS_ID_FIELD'),
				]
			),
			new StringField(
				'FORMAT',
				[
					'default'    => 'json',
					'validation' => function() {
						return [
							new LengthValidator(null, 8),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_FORMAT_FIELD'),
				]
			),
			new IntegerField(
				'USER_ID',
				[
					'title' => Loc::getMessage('PROFILE_ENTITY_USER_ID_FIELD'),
				]
			),
			new IntegerField(
				'DELIVERY',
				[
					'title' => Loc::getMessage('PROFILE_ENTITY_DELIVERY_FIELD'),
				]
			),
			new IntegerField(
				'PAYMENTS',
				[
					'title' => Loc::getMessage('PROFILE_ENTITY_PAYMENTS_FIELD'),
				]
			),
			new IntegerField(
				'PERSON_TYPE',
				[
					'title' => Loc::getMessage('PROFILE_ENTITY_PERSON_TYPE_FIELD'),
				]
			),
			new TextField(
				'STATUSES',
				[
					'title' => Loc::getMessage('PROFILE_ENTITY_STATUSES_FIELD'),
				]
			),
			new StringField(
				'PAYMENT_METHODS',
				[
					'validation' => function() {
						return [
							new LengthValidator(null, 255),
						];
					},
					'title'      => Loc::getMessage('PROFILE_ENTITY_PAYMENT_METHODS_FIELD'),
				]
			),
		];
	}


	/**
	 * Returns the profile entity with all additional parameters
	 *
	 * @param int $ID
	 * @return mixed
	 */
	public static function getByIdFull($ID)
	{
		$result = static::getById($ID);
		if( $arFields = $result->Fetch() ) {
			$arFields["STATUSES"] = unserialize($arFields["STATUSES"]);
			//$arFields["PAYMENT_METHODS"] = unserialize($arFields["PAYMENT_METHODS"]);
			$conn = Application::getConnection();
			$helper = $conn->getSqlHelper();
			$strSql = "SELECT * FROM b_iplogicberu_prop WHERE " . $helper->quote('PROFILE_ID') . " = " . $ID;
			$result = $conn->query($strSql);
			$arFields["PROP"] = [];
			while( $arPropFields = $result->Fetch() ) {
				unset($arPropFields["PROFILE_ID"]);
				$arFields["PROP"][$arPropFields["NAME"]] = $arPropFields;
			}
			$arFields["BUSINESS"] = \Iplogic\Beru\V2\ORM\BusinessTable::getByBid($arFields["BUSINESS_ID"]);
			return $arFields;
		}
		return false;
	}


	/**
	 * Removes an entity from the profile table and related data from other database tables.
	 *
	 * @param int $ID
	 * @return object DeleteResult
	 */
	public static function delete($ID)
	{
		$arBefore = static::getRowById($ID, true);
		$result = parent::delete($ID);
		if( $result->isSuccess() ) {
			$conn = Application::getConnection();
			$conn->query("DELETE FROM b_iplogicberu_prop WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_attr WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_order WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_api_log WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_task WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_error WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_product WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_box WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_box_link WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_outlet WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_interval WHERE PROFILE_ID=" . $ID);
			$conn->query("DELETE FROM b_iplogicberu_delivery WHERE PROFILE_ID=" . $ID);
		}

		\CUrlRewriter::Delete(
			[
				'CONDITION' => '#^' . $arBefore["BASE_URL"] . '#',
			]
		);

		return $result;
	}


	/**
	 * @param int $ID
	 * @param array $arFields
	 * @return mixed
	 */
	public static function update($ID, array $arFields)
	{
		$ID = intval($ID);
		if( $ID < 1 ) {
			return false;
		}
		$arBefore = static::getRowById($ID, true);
		if( is_array($arFields["STATUSES"]) ) {
			$arFields["STATUSES"] = serialize($arFields["STATUSES"]);
		}
		if( is_array($arFields["PAYMENT_METHODS"]) ) {
			$arFields["PAYMENT_METHODS"] = serialize($arFields["PAYMENT_METHODS"]);
		}
		$result = parent::update($ID, $arFields);
		if( $result->isSuccess() ) {
			if( $arBefore["ACTIVE"] != $arFields["ACTIVE"] ) {
				TaskTable::deleteByProfileId($ID);
			}
			if( (!isset($arFields["BASE_URL"]) || $arFields["BASE_URL"] == "") && $arBefore["BASE_URL"] != "" ) {
				\CUrlRewriter::Delete(
					[
						'CONDITION' => '#^' . $arBefore["BASE_URL"] . '#',
					]
				);
			}
			elseif( isset($arFields["BASE_URL"]) && $arBefore["BASE_URL"] != $arFields["BASE_URL"] ) {
				\CUrlRewriter::Delete(
					[
						'CONDITION' => '#^' . $arBefore["BASE_URL"] . '#',
					]
				);
				\Bitrix\Main\UrlRewriter::add(
					$arFields["SITE"],
					[
						"CONDITION" => "#^" . $arFields["BASE_URL"] . "#",
						"RULE"      => "",
						"ID"        => "iplogic:beru",
						"PATH"      => "/bitrix/services/iplogic/mkpapi/index.php",
						"SORT"      => 100,
					]
				);
			}
		}
		return $result;
	}


	/**
	 * Adds an entity to the profile tables and routing data
	 *
	 * @param array $arFields
	 * @return mixed
	 */
	public static function add(array $arFields)
	{
		if( isset($arFields["ID"]) ) {
			if( static::getById($arFields["ID"], true) ) {
				return self::update($arFields["ID"], $arFields);
			}
		}
		$arFields["STATUSES"] = serialize($arFields["STATUSES"]);
		$arFields["PAYMENT_METHODS"] = serialize($arFields["PAYMENT_METHODS"]);
		$result = parent::add($arFields);

		if( $result->isSuccess() ) {
			if( $arFields["BASE_URL"] != "" ) {
				\Bitrix\Main\UrlRewriter::add(
					$arFields["SITE"],
					[
						"CONDITION" => "#^" . $arFields["BASE_URL"] . "#",
						"RULE"      => "",
						"ID"        => "iplogic:beru",
						"PATH"      => "/bitrix/services/iplogic/mkpapi/index.php",
						"SORT"      => 100,
					]
				);
			}
			return $result->getId();
		}
		return ["error" => $result->getErrorMessages()];
	}


	/**
	 * Adds profile field mapping
	 *
	 * @param int $PROFILE_ID
	 * @param array $arFields
	 * @return bool
	 */
	public static function setFieldsMapping($PROFILE_ID, $arFields)
	{
		global $DB;
		if( $arFields["TYPE"] == "permanent_text" ) {
			$arFields["VALUE"] = $arFields["TEXT_VALUE"];
		}
		unset($arFields["TEXT_VALUE"]);
		$arFields["PROFILE_ID"] = $PROFILE_ID;
		$conn = Application::getConnection();
		if( is_int($arFields["ID"]) && $arFields["ID"] > 0 ) {
			$ID = intval($arFields["ID"]);
			if( $arFields["TYPE"] == "empty" ) {
				$conn->query("DELETE FROM b_iplogicberu_prop WHERE ID=" . $ID);
			}
			else {
				$strUpdate = $DB->PrepareUpdate("b_iplogicberu_prop", $arFields);
				if( $strUpdate != "" ) {
					$conn->query("UPDATE b_iplogicberu_prop SET " . $strUpdate . " WHERE ID=" . $ID);
				}
			}
		}
		else {
			if( $arFields["TYPE"] != "empty" ) {
				$DB->Add("b_iplogicberu_prop", $arFields);
			}
		}
		return true;
	}
}