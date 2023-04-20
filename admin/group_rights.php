<?

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;

$ACCESS = new \Iplogic\Beru\Access($groupRightsBind);

$bindAdd = $ACCESS::getDefaultVarIns($groupRightsBind, $ID);
$defaultTaskVar = "GROUP_" . $bindAdd . "DEFAULT_TASK";
$defaultRightVar = "GROUP_" . $bindAdd . "DEFAULT_RIGHT";


if($groupRightsAction == "POST")
{
	if(strlen($RestoreDefaults)>0) {
		foreach($ACCESS->arGROUPS as $group) {
			$ACCESS::delGroupRight($groupRightsBind, [$group["ID"]]);
		}
	}
	else
	{
		$arTasksInModule = [];

		$letter = ($$defaultTaskVar) ? CTask::GetLetter($$defaultTaskVar) : '';
		if ($letter <> '' && $letter != "NOT_REF") {
			Option::set($module_id, $defaultTaskVar, $$defaultTaskVar);
			Option::set($module_id, $defaultRightVar, $letter);
		}

		foreach($ACCESS->arGROUPS as $group)
		{
			$tid = ${"TASKS_".$group["ID"]};
			$arTasksInModule[$group["ID"]] = Array('ID' => $tid);

			$letter = ($tid) ? CTask::GetLetter($tid) : '';
			if ($letter <> '' && $letter != "NOT_REF") {
				$ACCESS::setGroupRight($groupRightsBind, $group["ID"], $letter, $tid, $ID);
			}
			else {
				$ACCESS::delGroupRight($groupRightsBind, [$group["ID"]], $ID);
			}
		}

		if($groupRightsBind == "module") {
			CGroup::SetTasksForModule($module_id, $arTasksInModule);
		}

	}
}
else{
	$GROUP_DEFAULT_TASK = Option::get($module_id, $defaultTaskVar, "");
	if ($GROUP_DEFAULT_TASK == '')
	{
		$GROUP_DEFAULT_RIGHT = Option::get($module_id, $defaultRightVar, "D");
		$GROUP_DEFAULT_TASK = CTask::GetIdByLetter($GROUP_DEFAULT_RIGHT, $module_id, $groupRightsBind);
		if ($GROUP_DEFAULT_TASK) {
			Option::set($module_id, $defaultTaskVar, $GROUP_DEFAULT_TASK);
		}
	}
	?>
	<tr>
		<td width="50%"><b><?=Loc::getMessage("MAIN_BY_DEFAULT");?></b></td>
		<td width="50%">
			<?
			$arTasksInModule = CTask::GetTasksInModules(true, $module_id, $groupRightsBind);
			$arTasks = $arTasksInModule[$module_id];
			echo SelectBoxFromArray($defaultTaskVar, $arTasks, htmlspecialcharsbx($GROUP_DEFAULT_TASK));
			?>
		</td>
		<td width="0%"></td>
	</tr>
	<?
	$arUsedGroups = array();
	$arTaskInModule = $ACCESS::getTasksForModule($groupRightsBind, $ID);
	foreach($ACCESS->arGROUPS as $group):
		$v = (isset($arTaskInModule[$group["ID"]]['ID'])? $arTaskInModule[$group["ID"]]['ID'] : false);
		if($v == false)
			continue;
		$arUsedGroups[$group["ID"]] = true;
		?>
		<tr valign="top">
			<td><?=$group["NAME"]." [<a title=\"".Loc::getMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".$group["ID"]."&amp;lang=".LANGUAGE_ID."\">".$group["ID"]."</a>]:"?></td>
			<td>
				<?
				echo SelectBoxFromArray("TASKS_".$group["ID"], $arTasks, $v, Loc::getMessage("MAIN_DEFAULT"));
				?>
			</td>
			<td width="0%"><a href="javascript:void(0)" onClick="__GroupRightsDeleteRow(this)"><img src="/bitrix/themes/.default/images/actions/delete_button.gif" border="0" width="20" height="20"></a></td>
		</tr>
	<?endforeach;?>

	<?
	if(count($ACCESS->arGROUPS) > count($arUsedGroups)):
		?>
		<tr valign="top">
			<td><select onchange="settingsSetGroupID(this)">
					<option value=""><?echo Loc::getMessage("group_rights_select")?></option>
					<?
					foreach($ACCESS->arGROUPS as $group):
						if($arUsedGroups[$group["ID"]] == true)
							continue;
						?>
						<option value="<?=$group["ID"]?>"><?=$group["NAME"]." [".$group["ID"]."]"?></option>
					<?endforeach?>
				</select></td>
			<td>
				<?
				echo SelectBoxFromArray("", $arTasks, "", Loc::getMessage("MAIN_DEFAULT"));
				?>
			</td>
			<td width="0%"></td>
		</tr>
		<tr>
			<td colspan="3">
				<script type="text/javascript">
					function settingsSetGroupID(el)
					{
						var tr = jsUtils.FindParentObject(el, "tr");
						var sel = jsUtils.FindChildObject(tr.cells[1], "select");
						sel.name = "TASKS_"+el.value;

						var div = jsUtils.FindNextSibling(sel, "div");
						sel = jsUtils.FindChildObject(div, "select");
						sel.name = "subordinate_groups_"+el.value+"[]";
					}

					function settingsAddRights(a)
					{
						var row = jsUtils.FindParentObject(a, "tr");
						var tbl = row.parentNode;

						var tableRow = tbl.rows[row.rowIndex-1].cloneNode(true);
						tbl.insertBefore(tableRow, row);

						var sel = jsUtils.FindChildObject(tableRow.cells[1], "select");
						sel.name = "";
						sel.selectedIndex = 0;

						var div = jsUtils.FindNextSibling(sel, "div");
						div.style.display = "none";
						sel = jsUtils.FindChildObject(div, "select");
						sel.name = "";
						sel.selectedIndex = -1;

						sel = jsUtils.FindChildObject(tableRow.cells[0], "select");
						sel.selectedIndex = 0;
					}

					if ('__GroupRightsDeleteRow' != typeof window.noFunc) {
						function __GroupRightsDeleteRow(el)
						{
							BX.remove(BX.findParent(el, {'tag': 'tr'}));
							return false;
						}
					}
				</script>
				<a href="javascript:void(0)" onclick="settingsAddRights(this)" hidefocus="true" class="bx-action-href"><?echo Loc::getMessage("group_rights_add")?></a>
			</td>
		</tr>
	<?endif?>
<?}?>