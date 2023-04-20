<?
$moduleID = 'iplogic.beru';
$moduleNamespace = 'Iplogic\\Beru\\';

use \Bitrix\Main\Loader;

include(__DIR__ . "/lib/admin/autoload.php");

$arClasses = array_merge(
	[
		$moduleNamespace . "Control"       => "lib/control.php",
		$moduleNamespace . "YMAPI"         => "lib/ymapi.php",
		$moduleNamespace . "Profile"       => "lib/profile.php",
		$moduleNamespace . "Product"       => "lib/product.php",
		$moduleNamespace . "Order"         => "lib/order.php",
		$moduleNamespace . "Error"         => "lib/error.php",
		$moduleNamespace . "ApiLog"        => "lib/apilog.php",
		$moduleNamespace . "Task"          => "lib/task.php",
		$moduleNamespace . "Box"           => "lib/box.php",
		$moduleNamespace . "BoxLink"       => "lib/boxlink.php",
		$moduleNamespace . "DeliveryTable" => "lib/delivery.php",
		$moduleNamespace . "Access"        => "lib/access.php",
		$moduleNamespace . "RightsTable"   => "lib/rightstable.php",
	],
	$arAdminClasses
);

Loader::registerAutoLoadClasses(
	$moduleID,
	$arClasses
);
