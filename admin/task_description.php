<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
	"IPLOGIC_BERU_DENIED" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_DENIED'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_DENIED'),
	),
	"IPLOGIC_BERU_READ" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_READ'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_READ'),
	),
	"IPLOGIC_BERU_WRITE" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_WRITE'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_WRITE'),
	),
	"IPLOGIC_BERU_FULL" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_FULL'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_FULL'),
	),
	"IPLOGIC_BERU_PROFILE_DENIED" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_PROFILE_DENIED'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_PROFILE_DENIED'),
	),
	"IPLOGIC_BERU_PROFILE_READ" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_PROFILE_READ'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_PROFILE_READ'),
	),
	"IPLOGIC_BERU_PROFILE_WRITE" => array(
		"title" => Loc::getMessage('TASK_NAME_IPLOGIC_BERU_PROFILE_WRITE'),
		"description" => Loc::getMessage('TASK_DESC_IPLOGIC_BERU_PROFILE_WRITE'),
	),
	"MODULE" => array(
		"title" => Loc::getMessage("TASK_BINDING_MODULE"),
	),
	"PROFILE" => array(
		"title" => Loc::getMessage("TASK_BINDING_PROFILE"),
	),
);
