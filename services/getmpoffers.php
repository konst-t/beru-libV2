<?
define('BX_SECURITY_SESSION_VIRTUAL', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('iplogic.beru');

\Iplogic\Beru\V2\Helper::checkIP();

$p = explode("__", $_GET["param"]);
$arParams = ["business_id" => $p[0], "page_token" => $p[1]];

\Iplogic\Beru\V2\Executor::go(new \Iplogic\Beru\V2\Command\getMpOffers(), $arParams);

?>