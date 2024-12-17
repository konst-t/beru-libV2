<?php
define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_CHECK", true);

use Bitrix\Main\Loader;

$_SERVER["DOCUMENT_ROOT"] = __DIR__ . '/../../..';

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// искуственная авторизация в роли админа
//$_SESSION['SESS_AUTH']['USER_ID'] = 1;

// подключение автозаргрузки Composer
require_once($_SERVER["DOCUMENT_ROOT"] . '/local/vendor/autoload.php');

Loader::includeModule("iplogic.beru");

require_once('BitrixTestCase.php');