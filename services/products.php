<?
define('BX_SECURITY_SESSION_VIRTUAL', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('iplogic.beru');

\Iplogic\Beru\Control::checkIP();

$arParam = explode("__",$_GET["param"]);

\Iplogic\Beru\ProductTable::checkMarketProducts($arParam[0], $arParam[1]);

?>