<?

$moduleID = 'iplogic.beru';
define("ADMIN_MODULE_NAME", $moduleID);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use \Bitrix\Main\Localization\Loc;
use \Iplogic\Beru\Control;
use \Iplogic\Beru\IntervalTable;
use \Iplogic\Beru\OutletTable;
use \Iplogic\Beru\DeliveryTable;
use \Iplogic\Beru\HolidayTable;

Loc::loadMessages(__FILE__);

CJSCore::Init(array("jquery"));
CUtil::InitJSCore(array('window'));

/* fatal errors check, creat control object and get table data */
$checkParams = [
	"PROFILE" => true,
];

include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/prolog.php");

if ($ID > 0){
	$arFields = DeliveryTable::getRowById($ID);
	if (!$arFields){
		$fatalErrors = Loc::getMessage("WRONG_PARAMETERS_NOT_FOUND")."<br>";
	}
}

$adminControl = new \Iplogic\Beru\Admin\Info($moduleID);


/* get service data and preforms*/

if ($ID > 0) {
	$arOutlets = [];
	$rsOutlets = OutletTable::getList(["filter" => ["DELIVERY_ID" => $ID], "order" => ["ID" => "ASC"]]);
	while( $arOutlet = $rsOutlets->Fetch() ) {
		$arOutlets[] = $arOutlet;
	}
	$arIntervals = [];
	$rsIntervals = IntervalTable::getList(["filter" => ["DELIVERY_ID" => $ID], "order" => ["ID" => "ASC"]]);
	while( $arInterval = $rsIntervals->Fetch() ) {
		$arIntervals[] = $arInterval;
	}
}



/* form */

$main = '<div class="ipl-main-tab-wrappper">';
if ($ID > 0) {
	include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/delivery_info.php");
	$main .= $body;
}
else {
	$main .= '<h3>'.Loc::getMessage("IPL_MA_NEW_SETTINGS").'</h3>';
}
$main .= '<div style="clear:both;"></div></div><div class="ipl-buttons-wrappper">';
if ($ID > 0) {
	$main .= '<a 
				href="/bitrix/services/iplogic/mkpapi/ajax/delivery.php"  
				data-action="main"class="adm-btn adm-btn-save adm-btn-edit open-popup"
				data-title="'.Loc::getMessage("IPL_MA_EDIT_TITLE").'"
				>'.
		Loc::getMessage("IPL_MA_EDIT_SETTINGS");
}
else {
	$main .= '<a 
				href="/bitrix/services/iplogic/mkpapi/ajax/delivery.php"  
				data-action="main"class="adm-btn adm-btn-save adm-btn-add open-popup"
				data-title="'.Loc::getMessage("IPL_MA_NEW_TITLE").'"
				>'.
		Loc::getMessage("IPL_MA_START_NEW_SETTINGS");
}
$main .= '</a>
</div>';


$intervals = '<div class="ipl-interval-tab-wrappper">';
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/intervals.php");
$intervals .= $body;
$intervals .= '<div style="clear:both;"></div>
</div>
<div class="ipl-buttons-wrappper">
	<a 
		href="/bitrix/services/iplogic/mkpapi/ajax/interval.php"  
		data-action="interval"
		class="adm-btn adm-btn-save adm-btn-add open-popup"
		data-title="'.Loc::getMessage("IPL_MA_NEW_INTERVAL_TITLE").'"
	>'.
		Loc::getMessage("IPL_MA_ADD").'
	</a>
</div>';


$outlets = '<div class="ipl-outlet-tab-wrappper">';
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/outlets.php");
$outlets .= $body;
$outlets .= '<div style="clear:both;"></div>
</div>
<div class="ipl-buttons-wrappper">
	<a 
		href="/bitrix/services/iplogic/mkpapi/ajax/outlet.php"  
		data-action="outlet"
		class="adm-btn adm-btn-save adm-btn-add open-popup"
		data-title="'.Loc::getMessage("IPL_MA_NEW_OUTLET_TITLE").'"
	>'.
	Loc::getMessage("IPL_MA_ADD").'
	</a>
</div>';


$holidays = '<div class="ipl-holidays-tab-wrappper">';
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$moduleID."/admin/include/holidays.php");
$holidays .= $body;
$holidays .= '</div>';




/* tabs and opts */
$arTabs = [
	["DIV" => "edit1", "TAB" => Loc::getMessage("IPL_MA_COMMON"), "ICON"=>"", "TITLE"=>Loc::getMessage("IPL_MA_COMMON_TITLE")],
];
if ($ID > 0) {
	$arTabs[] = ["DIV" => "edit2", "TAB" => Loc::getMessage("IPL_MA_INTERVALS"), "ICON"=>"", "TITLE"=>Loc::getMessage("IPL_MA_INTERVALS_TITLE")];
	$arTabs[] = ["DIV" => "edit3", "TAB" => Loc::getMessage("IPL_MA_OUTLETS"), "ICON"=>"", "TITLE"=>Loc::getMessage("IPL_MA_OUTLETS_TITLE")];
	$arTabs[] = ["DIV" => "edit4", "TAB" => Loc::getMessage("IPL_MA_HOLIDAYS"), "ICON"=>"", "TITLE"=>Loc::getMessage("IPL_MA_HOLIDAYS_TITLE")];
}
$arOpts = [
	[
		"TAB" 	=> 0,
		"INFO" 	=> $main
	],
	[
		"TAB" 	=> 1,
		"INFO" 	=> $intervals
	],
	[
		"TAB" 	=> 2,
		"INFO" 	=> $outlets
	],
	[
		"TAB" 	=> 3,
		"INFO" 	=> $holidays
	],
];



/* context menu */
$arContextMenu = [
	[
		"TEXT"  => Loc::getMessage("IPL_MA_LIST"),
		"TITLE" => Loc::getMessage("IPL_MA_LIST_TITLE"),
		"LINK"  => "iplogic_beru_delivery.php?PROFILE_ID=".$request->get("PROFILE_ID")."&lang=".LANG,
		"ICON"  => "btn_list",
	],
];
if ($ID > 0) {
	$arContextMenu[] = [
		"SEPARATOR" => "Y"
	];
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
if ($adminControl->POST_RIGHT == "D") {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}
else {

	/* actions */
	if( $APPLICATION->GetGroupRight($moduleID)=="W"
		&& $fatalErrors == ""
	) {

		if( $request->get("action") == "delete_int" && $request->get("int_id")>0 ) {
			$result = IntervalTable::delete($request->get("int_id"));
			if ($result->isSuccess()) {
				LocalRedirect("/bitrix/admin/iplogic_beru_delivery_edit.php?PROFILE_ID=".$request->get("PROFILE_ID")."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
			}
		}


		if( $request->get("action") == "delete_out" && $request->get("out_id")>0 ) {
			$result = OutletTable::delete($request->get("out_id"));
			if ($result->isSuccess()) {
				LocalRedirect("/bitrix/admin/iplogic_beru_delivery_edit.php?PROFILE_ID=".$request->get("PROFILE_ID")."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
			}
		}


		if( $request->get("action") == "delete_hol" && $request->get("hol_id")>0 ) {
			$result = HolidayTable::delete($request->get("hol_id"));
			if ($result->isSuccess()) {
				LocalRedirect("/bitrix/admin/iplogic_beru_delivery_edit.php?PROFILE_ID=".$request->get("PROFILE_ID")."&ID=".$ID."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
			}
		}


		if( $request->get("action") == "delete" ) {
			$result = DeliveryTable::delete($ID);
			if ($result->isSuccess()) {
				LocalRedirect("/bitrix/admin/iplogic_beru_delivery.php?PROFILE_ID=".$request->get("PROFILE_ID")."&mess=ok&lang=".LANG."&".$adminControl->ActiveTabParam());
			}
			else {
				$message = new CAdminMessage(Loc::getMessage("IPL_MA_ERROR_DELETE")."<br>".implode("<br>",$result->getErrorMessages()));
			}
		}

	}


	/* starting output */
	if($ID > 0) {
		$mess = Loc::getMessage("IPL_MA_PAGE_TITLE")." ".$arFields["NAME"];
	}
	else {
		$mess = Loc::getMessage("IPL_MA_PAGE_TITLE_NEW");
	}
	$APPLICATION->SetTitle($mess);
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
	?><style>
		.ipl-main-tab-wrappper { text-align:center; }
		.ipl-table {    }
		.ipl-table-row {  display: table; width: 100%; table-layout: fixed;  }
		.ipl-table-cell {  display: table-cell; padding: 8px; width:50%;  }
		.ipl-table-cell.left { text-align:right; }
		.ipl-table-cell.right { text-align:left; }
		.ipl-buttons-wrappper { text-align:center; margin-top:20px; }
		.ipl-error-mes { color:#ff0000 !important; margin-bottom:20px; display:none; }
		.ipl-row { display: table-row; }
		.ipl-cell { display: table-cell; padding: 4px 8px; }
	</style><?
	$adminControl->buildPage();
	?><script>
		function deleteConfirm() {
			if (window.confirm('<?=Loc::getMessage("IPL_MA_DELETE_CONF")?>')) {
				window.location.href='iplogic_beru_delivery_edit.php?PROFILE_ID=<?=$arFields["PROFILE_ID"]."&ID=".$ID."&action=delete&lang=".LANG?>';
			}
		}
		function deleteIntConfirm(id) {
			if (window.confirm('<?=Loc::getMessage("IPL_MA_DELETE_CONF_INT")?>')) {
				window.location.href='iplogic_beru_delivery_edit.php?PROFILE_ID=<?=$arFields["PROFILE_ID"]."&ID=".$ID."&action=delete_int&int_id='+id+'&lang=".LANG?>&tabControl_active_tab=edit2';
			}
		}
		function deleteOutConfirm(id) {
			if (window.confirm('<?=Loc::getMessage("IPL_MA_DELETE_CONF_OUT")?>')) {
				window.location.href='iplogic_beru_delivery_edit.php?PROFILE_ID=<?=$arFields["PROFILE_ID"]."&ID=".$ID."&action=delete_out&out_id='+id+'&lang=".LANG?>&tabControl_active_tab=edit3';
			}
		}
		$(document).ready(function(){
			$('.open-popup').on('click', function(e)
			{
				e.preventDefault();
				
				var btn_save = {
					title: BX.message('JS_CORE_WINDOW_SAVE'),
					id: 'savebtn',
					name: 'savebtn',
					className: BX.browser.IsIE() && BX.browser.IsDoctype() && !BX.browser.IsIE10() ? '' : 'adm-btn-save',
					action: function () {
						var self = this;
						var parent = $(this.parentWindow.DIV);
						var errorDiv = parent.find(".ipl-error-mes");
						var showError = function(mess) {
							errorDiv.html(mess);
							errorDiv.show();
						}
						errorDiv.hide();
						switch (this.parentWindow.PARAMS.action) {
							case 'main':
								if (parent.find('input[name=ACTIVE]').is(':checked')){
									var active = "Y";
								} else {
									var active = "N";
								}
								if (parent.find('input[name=PAYMENT_ALLOW]').is(':checked')){
									var payment_allow = "Y";
								} else {
									var payment_allow = "N";
								}
								var params = {
									"NAME": parent.find('input[name=NAME]').val(),
									"ACTIVE": active,
									"SORT": parent.find('input[name=SORT]').val(),
									"TYPE": parent.find('select[name=TYPE]').val(),
									"PAYMENT_ALLOW": payment_allow,
									"DAY_FROM": parent.find('select[name=DAY_FROM]').val(),
									"DAY_TO": parent.find('select[name=DAY_TO]').val()
								};
								if (params.NAME === '') {
									showError('<?=Loc::getMessage("IPL_MA_ERROR_NAME")?>');
									return false;
								}
								var req = {
									action: 'save',
									PROFILE_ID: <?=$request->get("PROFILE_ID")?>,
									ID: '<?=$ID?>',
									params: params
								}
								$.post(
									this.parentWindow.PARAMS.content_url,
									req,
									function( data ) {
										data = JSON.parse(data);
										if(data["result"] === "error") {
											showError(data["message"]);
										}
										if (data["result"] === "redirect") {
											window.location.href = data["url"];
										}
										if(data["result"] === "success") {
											$('.ipl-main-tab-wrappper').html(data["body"]);
											self.parentWindow.Close();
										}
									}
								);
								break;
							case 'interval':
								var params = {
									"DAY": parent.find('select[name=DAY]').val(),
									"TIME_FROM": parent.find('select[name=TIME_FROM]').val(),
									"TIME_TO": parent.find('select[name=TIME_TO]').val(),
								};
								var req = {
									action: 'save',
									PROFILE_ID: <?=$request->get("PROFILE_ID")?>,
									ID: '<?=$ID?>',
									params: params
								}
								$.post(
									this.parentWindow.PARAMS.content_url,
									req,
									function( data ) {
										data = JSON.parse(data);
										if(data["result"] === "error") {
											showError(data["message"]);
										}
										if(data["result"] === "success") {
											$('.ipl-interval-tab-wrappper').html(data["body"]);
											self.parentWindow.Close();
										}
									}
								);
								break;
							case 'outlet':
								var params = {
									"NAME": parent.find('input[name=NAME]').val(),
									"CODE": parent.find('input[name=CODE]').val(),
								};
								if (params.NAME === '') {
									showError('<?=Loc::getMessage("IPL_MA_ERROR_NAME")?>');
									return false;
								}
								if (params.CODE === '') {
									showError('<?=Loc::getMessage("IPL_MA_ERROR_CODE")?>');
									return false;
								}
								var req = {
									action: 'save',
									PROFILE_ID: <?=$request->get("PROFILE_ID")?>,
									ID: '<?=$ID?>',
									params: params
								}
								$.post(
									this.parentWindow.PARAMS.content_url,
									req,
									function( data ) {
										data = JSON.parse(data);
										if(data["result"] === "error") {
											showError(data["message"]);
										}
										if(data["result"] === "success") {
											$('.ipl-outlet-tab-wrappper').html(data["body"]);
											self.parentWindow.Close();
										}
									}
								);
								break;
							default:
								this.parentWindow.Close();
						}
					}
				};

				var popup = new BX.CAdminDialog({
					'title': $(this).attr('data-title'),
					'content_url': $(this).attr('href'),
					'content_post': 'PROFILE_ID=<?=$request->get("PROFILE_ID").($ID>0 ? "&ID=".$ID : "")?>',
					'action': $(this).attr('data-action'),
					'draggable': true,
					'resizable': true,
					'height': 300,
					'buttons': [btn_save, BX.CDialog.btnCancel]
				});
				popup.Show();
			});
			$(document).on('click', '.holiday-action', function(e) {
				e.preventDefault();

				var params = {
					"CONTENT": $(this).attr('data-content')
				};
				if (params.CONTENT === '') {
					showError('<?=Loc::getMessage("IPL_MA_ERROR_CODE")?>');
					return false;
				}
				var req = {
					action: $(this).attr('data-action'),
					PROFILE_ID: <?=$request->get("PROFILE_ID")?>,
					ID: '<?=$ID?>',
					params: params
				}
				$.post(
					'/bitrix/services/iplogic/mkpapi/ajax/holidays.php',
					req,
					function( data ) {
						data = JSON.parse(data);
						if(data["result"] === "success") {
							$('.ipl-holidays-tab-wrappper').html(data["body"]);
						}
					}
				);

			});
		});
	</script><?

}

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>