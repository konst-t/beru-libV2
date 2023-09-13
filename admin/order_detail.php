<?

$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

$baseFolder = realpath(__DIR__ . "/../../..");

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use \Bitrix\Main\Localization\Loc,
	\Iplogic\Beru\Control,
	\Iplogic\Beru\YMAPI,
	\Iplogic\Beru\BoxTable,
	\Iplogic\Beru\BoxLinkTable,
	\Iplogic\Beru\OrderTable,
	\Iplogic\Beru\ProfileTable;

Loc::loadMessages(__FILE__);

CJSCore::Init(array("jquery"));

/* fatal errors check, creat control object and get table data */
$checkParams = [];

include($baseFolder."/modules/".$moduleID."/prolog.php");

$PROFILE_ACCESS = \Iplogic\Beru\Access::getGroupRight("profile", $PROFILE_ID);

if ($MODULE_ACCESS == "D" || $PROFILE_ACCESS == "D") {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(Loc::getMessage("ACCESS_DENIED"));
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

$ID = $request->get("ID");
if ($ID > 0){
	$arFields = OrderTable::getById($ID);
	if (!$arFields){
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS_NOT_FOUND")."<br>";
	}
	else {
		$arProfile = ProfileTable::getById($arFields["PROFILE_ID"]);
		if (!$arProfile){
			$fatalErrors = Loc::getMessage("WRONG_PARAMETERS_PROFILE_NOT_FOUND")."<br>";
		}
		$api = new YMAPI($arFields["PROFILE_ID"]);
		for ($i=0; $i<5; $i++) {
			$res = $api->getOrders(["orderId"=>$arFields["EXT_ID"]]); 
			if ($res["status"]==200)
				break;
			sleep(1);
		}
		if ($res["status"]!=200)
			$fatalErrors = Loc::getMessage("ORDER_NOT_FOUND_ON_MARKET")."<br>";
		else
			$arMrktOrder = $res["body"];
	}
}
else {
	$fatalErrors = Loc::getMessage("WRONG_PARAMETERS")."<br>";
}


$adminControl = new \Iplogic\Beru\Admin\Info($moduleID);


/* get service data and preforms*/

$arState = [
	"S_NEW" 								=> "<span style='color:red;'>".Loc::getMessage("IPL_MA_STATUS_NEW")."</span>",
	"S_PROCESSING_STARTED" 					=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_PROCESSING_STARTED")."</span>",
	"S_PROCESSING_READY_TO_SHIP" 			=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_PROCESSING_READY_TO_SHIP")."</span>",
	"S_PROCESSING_COURIER_FOUND" 			=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_PROCESSING_COURIER_FOUND")."</span>",
	"S_PROCESSING_SHIPPED" 					=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_PROCESSING_SHIPPED")."</span>",
	"S_CANCELLED_SHOP_FAILED" 				=> "<span style='color:#686868;'>".Loc::getMessage("IPL_MA_STATUS_CANCELLED_SHOP_FAILED")."</span>",
	"S_DELIVERED" 							=> "<span style='color:#38a915;'>".Loc::getMessage("IPL_MA_STATUS_DELIVERED")."</span>",
	"S_DELIVERY" 							=> "<span style='color:#38a915;'>".Loc::getMessage("IPL_MA_STATUS_DELIVERY")."</span>",
	"S_PICKUP" 								=> "<span style='color:#38a915;'>".Loc::getMessage("IPL_MA_STATUS_PICKUP")."</span>",
	"S_CANCELLED" 							=> "<span style='color:#686868;'>".Loc::getMessage("IPL_MA_STATUS_CANCELLED_BY_MARKETPLACE")."</span>",
	"S_UNPAID_WAITING_USER_INPUT" 			=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_UNPAID_WAITING_USER_INPUT")."</span>",
	"S_UNKNOWN" 							=> "<span style='color:#4527cb;'>".Loc::getMessage("IPL_MA_STATUS_UNKNOWN")."</span>",
	"S_PROCESSING_COURIER_ARRIVED_TO_SENDER"=> "<span style='color:#38a915;'>".Loc::getMessage("IPL_MA_STATUS_PROCESSING_COURIER_ARRIVED_TO_SENDER")."</span>",
];
$arBoxes = [];
$rsBoxes = BoxTable::getList(["filter"=>["ORDER_ID"=>$ID],"order"=>["NUM"=>"ASC"]]);
while ($arBox = $rsBoxes->Fetch()) {
	$arBoxes[] = $arBox;
}
foreach ($arBoxes as $box) {
	$arBoxesMod[$box["ID"]] = $box["NUM"];
	$arBoxesIds[$arFields["EXT_ID"]."-".$box["NUM"]] = $box["ID"];
}
$arLinks = [];
$rsLinks = BoxLinkTable::getList(["filter"=>["ORDER_ID"=>$ID]]);
while ($arLink = $rsLinks->Fetch()) {
	$arLinks[] = $arLink;
}
$arMktProductsT = $arMrktOrder["order"]["items"]; 
$arMktProducts = [];
if (is_array($arMktProductsT)) {
	foreach($arMktProductsT as $prod) {
		$arMktProducts[$prod["offerId"]] = TruncateText($prod["offerName"],30);
	}
}


/* order info */
$info = Loc::getMessage("IPL_MA_TIME").": ".$arFields["HUMAN_TIME"]."<br><br>".
		Loc::getMessage("IPL_MA_SHIPMENT_DATE").": ".$arFields["SHIPMENT_DATE"]."<br><br>".
		Loc::getMessage("IPL_MA_EXT_ID").": <b>".$arFields["EXT_ID"]."</b><br><br>".
		Loc::getMessage("IPL_MA_ORDER_ID").": <a href=\"sale_order_view.php?ID=".$arFields["ORDER_ID"]."&lang=".LANG."\">".$arFields["ORDER_ID"]."</a><br><br>".
		Loc::getMessage("IPL_MA_PROFILE").": <a href=\"/bitrix/admin/iplogic_beru_profile_edit.php?ID=".
			$arFields["PROFILE_ID"]."&lang=".LANGUAGE_ID."\">".$arProfile["NAME"]."</a><br><br>".
		Loc::getMessage("IPL_MA_STATE").": ".$arState[$arFields["STATE"]]."<br><br>".
		Loc::getMessage("IPL_MA_STATE_CODE").": ".$arFields["STATE_CODE"]."<br><br>"; 
$info.= Loc::getMessage("IPL_MA_TEST").": ";
if ($arFields["FAKE"] == "Y") {
	$info .= "<span style=\"color:red;\">".Loc::getMessage("IPL_MA_YES")."</span>";
}
else {
	$info .= Loc::getMessage("IPL_MA_NO");
}
if (strlen($arFields["COURIER"])) {
	$info .= "<br><br>".Loc::getMessage("IPL_MA_COURIER").": ".$arFields["COURIER"];
}
$info .= "<br><br><h3>".Loc::getMessage("IPL_MA_PRODUCTS")."</h3><hr><br>";
$info .= "<div style=\"display:table;\">";
$info .= "<div style=\"display:table-row;\"><div class=\"table-cell\">";
$info .= "<b>SKU ID</b>";
$info .= "</div><div class=\"table-cell\">";
$info .= "<b>".Loc::getMessage("IPL_MA_NAME")."</b>";
$info .= "</div><div class=\"table-cell\">";
$info .= "<b>".Loc::getMessage("IPL_MA_COUNT")."</b>";
$info .= "</div><div class=\"table-cell\">";
$info .= "<b>".Loc::getMessage("IPL_MA_PRICE")."</b>";
$info .= "</div></div>";
if (is_array($arMktProductsT)) {
	foreach($arMktProductsT as $prod) {
		$info .= "<div style=\"display:table-row;\"><div class=\"table-cell\">";
		$info .= $prod["offerId"];
		$info .= "</div><div class=\"table-cell\">";
		$info .= $prod["offerName"];
		$info .= "</div><div class=\"table-cell\">";
		$info .= $prod["count"];
		$info .= "</div><div class=\"table-cell\">";
		$info .= $prod["price"];
		$info .= "</div></div>";
	}
}
$info .= "</div>";



/* boxes */
if( count($arBoxes) ){
	$boxes .= "<div style=\"display:table;\">";
	foreach ($arBoxes as $arBox) {
		$boxes .= "<div style=\"display:table-row;\"><div class=\"table-cell\">
			".Loc::getMessage("IPL_MA_BOX")." ".$arFields["EXT_ID"]."-".$arBox["NUM"]." (".
			$arBox["WIDTH"]." / ".$arBox["HEIGHT"]." / ".$arBox["DEPTH"]." ".Loc::getMessage("IPL_MA_CM").", ".$arBox["WEIGHT"]." ".Loc::getMessage("IPL_MA_GR").")</div><div class=\"table-cell\">
			<span class=\"adm-table-item-edit-wrap adm-table-item-edit-single\"><a class=\"adm-table-btn-delete\" href=\"javascript:deleteBoxConfirm(".$arBox["ID"].");\"></a></span>
			&nbsp;&nbsp;
			<a href=\"/bitrix/services/iplogic/mkpapi/sticker.php?box=".$arBox["ID"]."\" target=\"_blank\">".Loc::getMessage("IPL_MA_STICKER")."</a>
		</div></div>";
	}
	$boxes .= "</div>";
}
else {
	$boxes .= Loc::getMessage("IPL_MA_NO_BOXES");
}
$boxes .= "<br><h3>".Loc::getMessage("IPL_MA_NEW_BOX")."</h3><hr>
		<tr>
			<td width=\"40%\">
				<b>".Loc::getMessage("IPL_MA_CREATE_FROM")."</b>
			</td>
			<td>
				<select name=\"box-type\">
					<option value=\"props\">".Loc::getMessage("IPL_MA_PROPS")."</option>
					<option value=\"product\">".Loc::getMessage("IPL_MA_PRODUCT")."</option>
				</select>
			</td>
		</tr>

		<tr class=\"box-from-parameters\">
			<td width=\"50%\"><b>".Loc::getMessage("IPL_MA_WEIGHT")."</b></td>
			<td><input type=\"text\" size=\"6\" name=\"weight\"></td>
		</tr>
		<tr class=\"box-from-parameters\">
			<td><b>".Loc::getMessage("IPL_MA_WIDTH")."</b></td>
			<td><input type=\"text\" size=\"6\" name=\"width\"></td>
		</tr>
		<tr class=\"box-from-parameters\">
			<td><b>".Loc::getMessage("IPL_MA_HEIGHT")."</b></td>
			<td><input type=\"text\" size=\"6\" name=\"height\"></td>
		</tr>
		<tr class=\"box-from-parameters\">
			<td><b>".Loc::getMessage("IPL_MA_DEPTH")."</b></td>
			<td><input type=\"text\" size=\"6\" name=\"depth\"></td>
		</tr>

		<tr style=\"display:none;\" class=\"box-from-product\">
			<td width=\"40%\">
				<b>".Loc::getMessage("IPL_MA_PRODUCTS")."</b>
			</td>
			<td>
				<select name=\"product-box\">";
foreach ($arMktProducts as $id => $arProd) {
	$boxes .= "<option value=\"".$id."\">[".$id."] ".$arProd."</option>";
}
$boxes .= "</select>
			</td>
		</tr>

		<tr>
			<td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"add_box\" value=\"".Loc::getMessage("IPL_MA_SUBMIT_BOX")."\"></td>
		</tr>
";



/* tabs and opts */
$arTabs = [
	["DIV" => "edit1", "TAB" => Loc::getMessage("IPL_MA_DETAIL"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("IPL_MA_DETAIL_TITLE")],
];
if ($arProfile["SCHEME"] != "DBS") {
	$arTabs[] = ["DIV" => "edit2", "TAB" => Loc::getMessage("IPL_MA_BOXES"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("IPL_MA_BOXES_TITLE")];
}
$arOpts = [
	[
		"TAB" 	=> 0,
		"INFO" 	=> $info
	],
	[
		"TAB" 	=> 1,
		"INFO" 	=> $boxes
	],
];


/* context menu */
$arContextMenu = [
	[
		"TEXT"  => Loc::getMessage("IPL_MA_LIST"),
		"TITLE" => Loc::getMessage("IPL_MA_LIST_TITLE"),
		"LINK"  => "iplogic_beru_order_list.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&lang=".LANG,
		"ICON"  => "btn_list",
	],
	[
		"SEPARATOR" => "Y"
	],
];

if($PROFILE_ACCESS >= "W") {

	$bShowDoneButton = false;
	if( $arFields["STATE"] == "S_PROCESSING_READY_TO_SHIP" && $arFields["BOXES_SENT"] == "Y" ) {
		$bShowDoneButton = true;
		if( $arFields["READY_TIME"] > 0 && ($arFields["READY_TIME"] + 60) > time() ) {
			$bShowDoneButton = false;
		}
	}
	$bShowDeliveredButton = false;
	if( $arFields["STATE"] == "S_DELIVERY" ) {
		$bShowDeliveredButton = true;
		if( $arFields["READY_TIME"] > 0 && ($arFields["READY_TIME"] + 60) > time() ) {
			$bShowDeliveredButton = false;
		}
	}


	if(
		$arFields["STATE"] == "S_PROCESSING_STARTED" && $arFields["BOXES_SENT"] == "Y" && $arProfile["SCHEME"] != "DBS"
	) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_STATE_READY"),
			"TITLE" => Loc::getMessage("IPL_MA_STATE_READY_TITLE"),
			"LINK"  => "iplogic_beru_order_detail.php?PROFILE_ID=" . $arFields["PROFILE_ID"] . "&ID=" . $ID .
				"&action=state_ready&lang=" . LANG,
		];
	}
	if( $arFields["STATE"] == "S_PROCESSING_STARTED" && $arProfile["SCHEME"] == "DBS" ) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_STATE_DELIVERY"),
			"TITLE" => Loc::getMessage("IPL_MA_STATE_DELIVERY_TITLE"),
			"LINK"  => "iplogic_beru_order_detail.php?PROFILE_ID=" . $arFields["PROFILE_ID"] . "&ID=" . $ID .
				"&action=state_delivery&lang=" . LANG,
		];
	}
	if( $bShowDoneButton ) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_STATE_DONE"),
			"TITLE" => Loc::getMessage("IPL_MA_STATE_DONE_TITLE"),
			"LINK"  => "iplogic_beru_order_detail.php?PROFILE_ID=" . $arFields["PROFILE_ID"] . "&ID=" . $ID .
				"&action=state_done&lang=" . LANG,
		];
	}
	if( $bShowDeliveredButton ) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_STATE_DELIVERED"),
			"TITLE" => Loc::getMessage("IPL_MA_STATE_DELIVERED_TITLE"),
			"LINK"  => "iplogic_beru_order_detail.php?PROFILE_ID=" . $arFields["PROFILE_ID"] . "&ID=" . $ID .
				"&action=state_delivered&lang=" . LANG,
		];
	}
	if(
		($arFields["STATE"] == "S_PROCESSING_STARTED" ||
			$arFields["STATE"] == "S_PROCESSING_READY_TO_SHIP" ||
			$arFields["STATE"] == "S_UNPAID_WAITING_USER_INPUT") &&
		$arProfile["SCHEME"] != "DBS" &&
		$arFields["BOXES_SENT"] != "Y"
	) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_SEND_BOXES"),
			"TITLE" => Loc::getMessage("IPL_MA_SEND_BOXES_TITLE"),
			"LINK"  => "iplogic_beru_order_detail.php?PROFILE_ID=" . $arFields["PROFILE_ID"] . "&ID=" . $ID .
				"&action=send_boxes&lang=" . LANG,
		];
	}
	if(
		$arFields["STATE"] == "S_PROCESSING_STARTED" ||
		$arFields["STATE"] == "S_PROCESSING_READY_TO_SHIP" ||
		($arFields["STATE"] == "S_DELIVERY" && $arProfile["SCHEME"] == "DBS")
	) {
		$arContextMenu[] = [
			"TEXT"  => Loc::getMessage("IPL_MA_STATE_CANCEL"),
			"TITLE" => Loc::getMessage("IPL_MA_STATE_CANCEL_TITLE"),
			"LINK"  => "javascript:cancelConfirm();",
		];
	}
	$arContextMenu[] = [
		"TEXT"  => Loc::getMessage("IPL_MA_DELETE"),
		"TITLE" => Loc::getMessage("IPL_MA_DELETE_TITLE"),
		"LINK"  => "javascript:deleteConfirm();",
		"ICON"  => "btn_delete",
	];
}



/* lang messages in classes */
$Messages = [
	"DELETE_CONF" => Loc::getMessage("IPL_MA_DELETE_CONF"),
];



/* prepare control object */
$adminControl->arTabs = $arTabs;
$adminControl->arOpts = $arOpts;
$adminControl->Mess = $Messages;
$adminControl->arContextMenu = $arContextMenu;
$adminControl->initDetailPage();



/* executing */

/* actions */
if( $PROFILE_ACCESS >= "W"
	&& $fatalErrors == ""
) {

	if( $request->get("action") == "send_boxes" ) {
		if ($arFields["BOXES_SENT"] == "Y") {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_BOXES_SENT"));
		}
		else {
			$res = OrderTable::sendOrderBoxes($ID);
			if ($res["status"]==200) {
				$arUpdateFields = ["BOXES_SENT"=>"Y"];
				OrderTable::update($ID,$arUpdateFields);
				$arSentBoxes = $res["body"]["result"]["boxes"];
				foreach($arSentBoxes as $arSentBox) {
					if ($arBoxesIds[$arSentBox["fulfilmentId"]] > 0) {
						BoxTable::update($arBoxesIds[$arSentBox["fulfilmentId"]], ["EXT_ID"=>$arSentBox["id"]]);
					}
				}
				LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_SEND_BOXES")."<br>".$res["error"]);
			}
		}
	}


	if( $request->get("action") == "delete_box" && $request->get("box_id")>0 ) {
		$result = BoxTable::delete($request->get("box_id"));
		if ($result->isSuccess()) {
			$i = 1;
			foreach($arBoxes as $arBox) {
				if ($arBox["ID"]!=$request->get("box_id")) {
					BoxTable::update($arBox["ID"],["NUM"=>$i]);
					$i++;
				}
			}
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
		}
	}


	if( $request->get("action") == "delete" ) {
		$result = OrderTable::delete($ID);
		if ($result->isSuccess()) {
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
		}
	}


	if( $request->get("action") == "state_ready" ) {
		$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		$statusKey = "S_PROCESSING_READY_TO_SHIP";
		$newStatusCode = "PROCESSING READY_TO_SHIP";
		$arStatus = [
			"PROCESSING",
			"READY_TO_SHIP"
		];
		$cl = new YMAPI($arFields["PROFILE_ID"]);
		$result = $cl->setOrderStatus($arFields["EXT_ID"], $arStatus[0], $arStatus[1]);
		if ($result["status"] == 200) {
			$arOUFields = [
				"STATE" => $statusKey,
				"STATE_CODE" => $newStatusCode,
				"READY_TIME" => time(),
			];
			OrderTable::update($ID,$arOUFields);
			$arStFields = [
				'STATUS_ID' => $arProfile["STATUSES"][$statusKey],
			];
			\CSaleOrder::Update($arFields["ORDER_ID"], $arStFields);
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_STATUS_UPDATE"));
		}
	}


	if( $request->get("action") == "state_done" ) {
		$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		$statusKey = "S_PROCESSING_SHIPPED";
		$newStatusCode = "PROCESSING SHIPPED";
		$arStatus = [
			"PROCESSING",
			"SHIPPED"
		];
		$cl = new YMAPI($arFields["PROFILE_ID"]);
		$result = $cl->setOrderStatus($arFields["EXT_ID"], $arStatus[0], $arStatus[1]);
		if ($result["status"] == 200) {
			$arOUFields = [
				"STATE" => $statusKey,
				"STATE_CODE" => $newStatusCode,
			];
			OrderTable::update($ID,$arOUFields);
			$arStFields = [
				'STATUS_ID' => $arProfile["STATUSES"][$statusKey],
			];
			\CSaleOrder::Update($arFields["ORDER_ID"], $arStFields);
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_STATUS_UPDATE"));
		}
	}


	if( $request->get("action") == "state_cancel" ) {
		$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		$statusKey = "S_CANCELLED_SHOP_FAILED";
		$newStatusCode = "CANCELLED SHOP_FAILED";
		$arStatus = [
			"CANCELLED",
			"SHOP_FAILED"
		];
		$cl = new YMAPI($arFields["PROFILE_ID"]);
		$result = $cl->setOrderStatus($arFields["EXT_ID"], $arStatus[0], $arStatus[1]);
		if ($result["status"] == 200) {
			$arOUFields = [
				"STATE" => $statusKey,
				"STATE_CODE" => $newStatusCode,
			];
			OrderTable::update($ID,$arOUFields);
			$arStFields = [
				'STATUS_ID' => $arProfile["STATUSES"][$statusKey],
			];
			\CSaleOrder::Update($arFields["ORDER_ID"], $arStFields);
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_STATUS_UPDATE"));
		}
	}


	if( $request->get("action") == "state_delivery" ) {
		$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		$statusKey = "S_DELIVERY";
		$newStatusCode = "DELIVERY";
		$arStatus = [
			"DELIVERY",
			""
		];
		$cl = new YMAPI($arFields["PROFILE_ID"]);
		$result = $cl->setOrderStatus($arFields["EXT_ID"], $arStatus[0], $arStatus[1]);
		if ($result["status"] == 200) {
			$arOUFields = [
				"STATE" => $statusKey,
				"STATE_CODE" => $newStatusCode,
				"READY_TIME" => time(),
			];
			OrderTable::update($ID,$arOUFields);
			$arStFields = [
				'STATUS_ID' => $arProfile["STATUSES"][$statusKey],
			];
			\CSaleOrder::Update($arFields["ORDER_ID"], $arStFields);
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_STATUS_UPDATE"));
		}
	}


	if( $request->get("action") == "state_delivered" ) {
		$arOrder = CSaleOrder::GetByID($arFields["ORDER_ID"]);
		$statusKey = "S_DELIVERED";
		$newStatusCode = "DELIVERED";
		$arStatus = [
			"DELIVERED",
			""
		];
		$cl = new YMAPI($arFields["PROFILE_ID"]);
		$result = $cl->setOrderStatus($arFields["EXT_ID"], $arStatus[0], $arStatus[1]);
		if ($result["status"] == 200) {
			$arOUFields = [
				"STATE" => $statusKey,
				"STATE_CODE" => $newStatusCode,
			];
			OrderTable::update($ID,$arOUFields);
			$arStFields = [
				'STATUS_ID' => $arProfile["STATUSES"][$statusKey],
			];
			\CSaleOrder::Update($arFields["ORDER_ID"], $arStFields);
			LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
		}
		else {
			$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_STATUS_UPDATE"));
		}
	}


	if( $request->isPost() && check_bitrix_sessid() ) {
		if ($request->get("add_box")!="") {


			if ($request->get("box-type") == "props") {
				$arBoxFields = [
					"ORDER_ID" 		=> $ID,
					"PROFILE_ID" 	=> $arFields["PROFILE_ID"],
					"NUM" 			=> (count($arBoxes)+1),
					"WEIGHT" 		=> $request->get("weight"),
					"WIDTH" 		=> $request->get("width"),
					"HEIGHT" 		=> $request->get("height"),
					"DEPTH" 		=> $request->get("depth"),
				];
			}
			elseif ($request->get("box-type") == "product") {
				$con = new Control($arFields["PROFILE_ID"]);
				$boxProduct = $con->getSKU($request->get("product-box"));
				$weight = $boxProduct["WEIGHT"]*1000;
				$arDim = explode("/",$boxProduct["DIMENSIONS"]);
				$arBoxFields = [
					"ORDER_ID" 		=> $ID,
					"PROFILE_ID" 	=> $arFields["PROFILE_ID"],
					"NUM" 			=> (count($arBoxes)+1),
					"WEIGHT" 		=> $weight,
					"WIDTH" 		=> $arDim[0],
					"HEIGHT" 		=> $arDim[1],
					"DEPTH" 		=> $arDim[2],
				];
			}
			$result = BoxTable::add($arBoxFields);
			if ($result->isSuccess()) {
				LocalRedirect("/bitrix/admin/iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_BOX_ADD")."<br>".implode("<br>",$result->getErrorMessages()));
			}
		}

	}
}


/* starting output */
$APPLICATION->SetTitle(Loc::getMessage("IPL_MA_PAGE_TITLE")." ID: ".$arFields["EXT_ID"]);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


/* fatal errors */
if ($fatalErrors != ""){
	CAdminMessage::ShowMessage($fatalErrors);
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
	die();
}

/* ok message */
if($request->get("mess") === "ok")
	CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("SAVED"), "TYPE"=>"OK"));


/* action errors */
if($message)
	echo $message->Show();


/* content */
echo "<style>
	.table-cell { display:table-cell; padding: 0 10px 10px 0; }
</style>";
$adminControl->buildPage();
echo ("<script>
	function deleteConfirm() {
		if (window.confirm('".Loc::getMessage("IPL_MA_DELETE_CONF")."')) {
			window.location.href='iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&action=delete&lang=".LANG."';
		}
	}
	function cancelConfirm() {
		if (window.confirm('".Loc::getMessage("IPL_MA_CANCEL_CONF")."')) {
			window.location.href='iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&action=state_cancel&lang=".LANG."';
		}
	}
	function deleteBoxConfirm(id) {
		if (window.confirm('".Loc::getMessage("IPL_MA_DELETE_BOX_CONF")."')) {
			window.location.href='iplogic_beru_order_detail.php?PROFILE_ID=".$arFields["PROFILE_ID"]."&ID=".$ID."&action=delete_box&box_id='+id+'&lang=".LANG."&tabControl_active_tab=edit2';
		}
	}
	$(document).ready(function(){
		$('select[name=\"box-type\"]').on('change', function(){ 
			var type = $('select[name=\"box-type\"]').val();
			if(type=='props'){
				$('.box-from-parameters').show();
				$('.box-from-product').hide();
			}
			if(type=='product'){
				$('.box-from-parameters').hide();
				$('.box-from-product').show();
			}
		});
	});
</script>");


require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>