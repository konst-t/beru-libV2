<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
	"PROFILES_READ" => array(
		"title" => Loc::getMessage('OP_NAME_PROFILES_READ'),
		"description" => Loc::getMessage('OP_DESC_PROFILES_READ'),
	),
	"PROFILES_EDIT" => array(
		"title" => Loc::getMessage('OP_NAME_PROFILES_EDIT'),
		"description" => Loc::getMessage('OP_DESC_PROFILES_EDIT'),
	),
	"LOGS_READ" => array(
		"title" => Loc::getMessage('OP_NAME_LOGS_READ'),
		"description" => Loc::getMessage('OP_DESC_LOGS_READ'),
	),
	"LOGS_EDIT" => array(
		"title" => Loc::getMessage('OP_NAME_LOGS_EDIT'),
		"description" => Loc::getMessage('OP_DESC_LOGS_EDIT'),
	),
	"TASKS_READ" => array(
		"title" => Loc::getMessage('OP_NAME_TASKS_READ'),
		"description" => Loc::getMessage('OP_DESC_TASKS_READ'),
	),
	"TASKS_EDIT" => array(
		"title" => Loc::getMessage('OP_NAME_TASKS_EDIT'),
		"description" => Loc::getMessage('OP_DESC_TASKS_EDIT'),
	),
	"OPTIONS_EDIT" => array(
		"title" => Loc::getMessage('OP_NAME_OPTIONS_EDIT'),
		"description" => Loc::getMessage('OP_DESC_OPTIONS_EDIT'),
	),
	"PROFILE_READ" => array(
		"title" => Loc::getMessage('OP_NAME_PROFILE_READ'),
		"description" => Loc::getMessage('OP_DESC_PROFILE_READ'),
	),
	"PROFILE_EDIT" => array(
		"title" => Loc::getMessage('OP_NAME_PROFILE_EDIT'),
		"description" => Loc::getMessage('OP_DESC_PROFILE_EDIT'),
	),
);

