<?
use \Bitrix\Main\Localization\Loc;
use \Iplogic\Beru\HolidayTable;

function getMonth($monthNum, $year) {
	$month = [
		"NUM" => $monthNum,
		"YEAR" => $year,
	];

	$arWeeks = [];
	$weekNum = 0;
	$monthNumM = $monthNum;
	if ($monthNum < 10) {
		$monthNumM = "0".$monthNum;
	}
	for($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $monthNum, $year); $i++) {
		$date = $i."-".$monthNumM."-".$year;
		$dayOfWeek = date('N', strtotime($year."-".$monthNumM."-".$i));
		$dateM = $date;
		if ($date < 10) {
			$dateM = "0".$date;
		}
		$arWeeks[$weekNum][$dayOfWeek] = [
			"DATE" => $dateM,
			"DAY"  => $i
		];
		if($dayOfWeek == 7) {
			$weekNum++;
		}
	}
	$month["WEEKS"] = $arWeeks;
	return $month;
}

$arHolidays = [];
$rsHolidays = HolidayTable::getList(["filter" => ["DELIVERY_ID" => $ID], "order" => ["TIMESTAMP" => "ASC"]]);
while($arHoliday = $rsHolidays->fetch()) {
	$arHolidays[] = $arHoliday;
}

$body = "";
if (!count($arHolidays)) {
	$body .= "<h3>".Loc::getMessage("IPL_MA_NO_HOLIDAYS")."</h3>";
}

$arHolidayDates = [];
$arHolidayIDs = [];
if (count($arHolidays)) {
	foreach($arHolidays as $arHoliday) {
		$arHolidayDates[] = $arHoliday["DATE"];
		$arHolidayIDs[$arHoliday["DATE"]] = $arHoliday["ID"];
	}
}

$arMonths = [];
$curMonth = date('n');
$remained = 13 - $curMonth;
$fromNextYear = 12 - $remained;
for($i = $curMonth; $i <= 12; $i++) {
	$arMonths[] = getMonth($i, date('Y'));
}
for($i = 1; $i <= $fromNextYear; $i++) {
	$arMonths[] = getMonth($i, date('Y')+1);
}


$body .= '
<style>
	.calendar {
		display: -webkit-flex; 
		-webkit-justify-content: center; 
		display: flex; 
		justify-content: center; 
		flex-wrap: wrap;
	}
	.calendar-month {
		/*float: left;*/
		margin: 20px;
	}
	.calendar-month-title {
		text-align: center;
		font-size: 18px;
		padding: 10px;
	}
	.calendar-week-day {
		float: left;
		width:42px;
		font-size:12px;
		color:#727272;
		padding: 8px 0;
		text-align: center;
	}
	.calendar-day {
		float: left;
		width:40px;
		/*font-size:12px;*/
		color:#fff;
		padding: 10px 0;
		margin:1px;
		text-align: center;
	}
	.calendar-holiday {
		background-color: #ffae00;
	}
	.calendar-holiday:hover {
		background-color: #ffc65c;
	}
	.calendar-working {
		background-color: #808080;
	}
	.calendar-working:hover {
		background-color: #aaaaaa;
	}
	.calendar-empty-day {
		float: left;
		width:42px;
	}
	.today {
		background-color: #1d2bec !important;
	}
</style>
';

$body .= "<div class='calendar'>";
foreach($arMonths as $arMonth) {
	$body .= "<div class='calendar-month'>";
	$body .= "<div class='calendar-month-title'>";
	$body .= Loc::getMessage("IPL_MA_MONTH_".$arMonth["NUM"])." ".$arMonth["YEAR"];
	$body .= "</div>";
	for($i = 1; $i < 8; $i++) {
		$body .= "<div class='calendar-week-day'>";
		$body .= Loc::getMessage("IPL_MA_DAY_SHORT_".$i);
		$body .= "</div>";
	}
	$body .= "<div style='clear: both;'></div>";
	foreach($arMonth["WEEKS"] as $arWeek) {
		for($i = 1; $i < 8; $i++) {
			if(!isset($arWeek[$i])) {
				$body .= "<div class='calendar-empty-day'>&nbsp;</div>";
			}
			else{
				$class = "";
				$today = date('d-m-Y');
				if($arWeek[$i]["DATE"] == $today) {
					$class = " today";
				}
				if(in_array($arWeek[$i]["DATE"], $arHolidayDates)) {
					$body .= "<a class='holiday-action' data-action='delete' data-content='".$arHolidayIDs[$arWeek[$i]["DATE"]]."' href='#'>";
					$body .= "<div class='calendar-day calendar-holiday".$class."'>";
					$body .= $arWeek[$i]["DAY"];
					$body .= "</div>";
					$body .= "</a>";
				}
				else {
					$body .= "<a class='holiday-action' data-action='add' data-content='".$arWeek[$i]["DATE"]."' href='#'>";
					$body .= "<div class='calendar-day calendar-working".$class."'>";
					$body .= $arWeek[$i]["DAY"];
					$body .= "</div>";
					$body .= "</a>";
				}
			}
		}
		$body .= "<div style='clear: both;'></div>";
	}
	$body .= "</div>";
}
$body .= "<div style='clear: both;'></div>";
$body .= "</div>";

/*$body .= "<pre>";
$body .= print_r($arMonths, true);
$body .= "</pre>";*/