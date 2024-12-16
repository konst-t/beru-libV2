<?
$moduleID = 'iplogic.beru';
$moduleNamespace = 'Iplogic\\Beru\\';

use \Bitrix\Main\Loader;

include(__DIR__ . "/lib/admin/autoload.php");
include(__DIR__ . "/lib/v2/apirequest/autoload.php");
include(__DIR__ . "/lib/v2/command/autoload.php");
include(__DIR__ . "/lib/v2/ORM/autoload.php");
include(__DIR__ . "/lib/v2/task/autoload.php");

$arClasses_ = array_merge(
	$arApiClasses,
	$arAdminClasses
);
$arClasses_ = array_merge(
	$arClasses_,
	$arCommandClasses
);
$arClasses_ = array_merge(
	$arClasses_,
	$arORMClasses
);
$arClasses_ = array_merge(
	$arClasses_,
	$arTaskClasses
);

$arClasses = array_merge(
	[
		$moduleNamespace . "Control"       => "lib/v1/control.php",
		$moduleNamespace . "YMAPI"         => "lib/v1/ymapi.php",
		$moduleNamespace . "ProfileTable"  => "lib/v1/profile.php",
		$moduleNamespace . "ProductTable"  => "lib/v1/product.php",
		$moduleNamespace . "OrderTable"    => "lib/v1/order.php",
		$moduleNamespace . "ErrorTable"    => "lib/v1/error.php",
		$moduleNamespace . "ApiLogTable"   => "lib/v1/apilog.php",
		$moduleNamespace . "TaskTable"     => "lib/v1/task.php",
		$moduleNamespace . "BoxTable"      => "lib/v1/box.php",
		$moduleNamespace . "BoxLinkTable"  => "lib/v1/boxlink.php",
		$moduleNamespace . "DeliveryTable" => "lib/v1/delivery.php",

		$moduleNamespace . "Access" => "lib/access.php",

		$moduleNamespace . "V2\Agent"      => "lib/v2/agent.php",
		$moduleNamespace . "V2\ApiRequest" => "lib/v2/apirequest.php",
		$moduleNamespace . "V2\Rest"       => "lib/v2/rest.php",
		$moduleNamespace . "V2\Event"      => "lib/v2/event.php",
		$moduleNamespace . "V2\Execute"    => "lib/v2/execute.php",
		$moduleNamespace . "V2\Helper"     => "lib/v2/helper.php",
		$moduleNamespace . "V2\Product"    => "lib/v2/product.php",
		$moduleNamespace . "V2\Task"       => "lib/v2/task.php",
	],
	$arClasses_
);

Loader::registerAutoLoadClasses(
	$moduleID,
	$arClasses
);
