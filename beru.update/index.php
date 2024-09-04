<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use \Bitrix\Main;
use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;

$MODULE_ID = "iplogic.beru";

if (is_dir(Application::getDocumentRoot() . '/bitrix/modules/' . $MODULE_ID)) {
	$moduleDir = Application::getDocumentRoot() . '/bitrix/modules/' . $MODULE_ID;
}
else {
	$moduleDir = Application::getDocumentRoot() . '/local/modules/' . $MODULE_ID;
}

function getModuleVersion($mod) {
	if($info = \CModule::CreateModuleObject($mod)){
		return $info->MODULE_VERSION;
	}
}

Loader::includeModule($MODULE_ID);
$ver = getModuleVersion($MODULE_ID);

$conn = Application::getConnection();

echo $ver . "<br>";

if($ver < "3.3.0") {
	$strSql = "ALTER TABLE `b_iplogicberu_box_link`
ADD ORDER_PROD_ID int(11) NULL,
ADD IS_PART varchar(1) NOT NULL DEFAULT 'N',
ADD QUANTITY int(3) NULL,
ADD PART int(2) NULL,
ADD PARTS int(2) NULL";
	$result = $conn->query($strSql);

	$strSql = "ALTER TABLE `b_iplogicberu_box`
MODIFY COLUMN NUM int(3) NULL DEFAULT '0',
MODIFY COLUMN WEIGHT int(64) NULL,
MODIFY COLUMN WIDTH int(64) NULL,
MODIFY COLUMN HEIGHT int(64) NULL,
MODIFY COLUMN DEPTH int(64) NULL";
	$result = $conn->query($strSql);

	CopyDirFiles(__DIR__ . '/3.3.0/', $moduleDir, true, true);
	echo "Установлен 3.3.0<br>";
}

if($ver < "3.3.1") {
	CopyDirFiles(__DIR__ . '/3.3.1/', $moduleDir, true, true);
	echo "Установлен 3.3.1<br>";
}

if($ver < "3.3.2") {
	CopyDirFiles(__DIR__ . '/3.3.2/', $moduleDir, true, true);
	echo "Установлен 3.3.2<br>";
}

if($ver < "3.3.3") {
	CopyDirFiles(__DIR__ . '/3.3.3/', $moduleDir, true, true);
	echo "Установлен 3.3.3<br>";
}

if($ver < "3.3.4") {
	CopyDirFiles(__DIR__ . '/3.3.4/', $moduleDir, true, true);
	echo "Установлен 3.3.4<br>";
}

if($ver < "3.3.5") {
	CopyDirFiles(__DIR__ . '/3.3.5/', $moduleDir, true, true);
	echo "Установлен 3.3.5<br>";
}

if($ver < "3.3.6") {
	CopyDirFiles(__DIR__ . '/3.3.6/', $moduleDir, true, true);
	echo "Установлен 3.3.6<br>";
}

if($ver < "3.3.7") {
	CopyDirFiles(__DIR__ . '/3.3.7/', $moduleDir, true, true);
	echo "Установлен 3.3.7<br>";
}