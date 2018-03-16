<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

function cronnextrun($cron,$isRun=true) {
	global $dsql,$cfg_cli_time, $timestamp;
	if(empty($cron)) return FALSE;

	list($yearnow, $monthnow, $daynow, $weekdaynow, $hournow, $minutenow) = explode('-', gmdate('Y-m-d-w-H-i', $timestamp + $cfg_cli_time * 3600));

	if($cron['weekday'] == -1) {
		if($cron['day'] == -1) {
			$firstday = $daynow;
			$secondday = $daynow + 1;
		} else {
			$firstday = $cron['day'];
			$secondday = $cron['day'] + gmdate('t', $timestamp + $cfg_cli_time * 3600);
		}
	} else {
		$firstday = $daynow + ($cron['weekday'] - $weekdaynow);
		$secondday = $firstday + 7;
	}

	if($firstday < $daynow) {
		$firstday = $secondday;
	}

	if($firstday == $daynow) {
		$todaytime = crontodaynextrun($cron);
		if($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
			$cron['day'] = $secondday;
		} else {
			$cron['day'] = $firstday;
			$cron['hour']=$todaytime['hour'];
			$cron['minute']=$todaytime['minute'];
		}
	} else {
		$cron['day'] = $firstday;
	}

	$nextrun = @gmmktime($cron['hour'], $cron['minute'] > 0 ? $cron['minute'] : 0, 0, $monthnow, $cron['day'], $yearnow) - $cfg_cli_time * 3600;
//	echo $nextrun;die();
	$cronid = $cron['cronid'];
	$availableadd = $nextrun >= $timestamp ? '' : ', available=\'0\'';
	$dsql->ExecuteNoneQuery("UPDATE sea_crons SET ".($isRun?"lastrun='$timestamp',":"")." nextrun='$nextrun' $availableadd  WHERE cronid='$cronid'");
	return TRUE;
}

function crontodaynextrun($cron, $hour = -2, $minute = -2) {
	global $timestamp, $cfg_cli_time;

	$hour = $hour == -2 ? gmdate('H', $timestamp + $cfg_cli_time * 3600) : $hour;
	$minute = $minute == -2 ? gmdate('i', $timestamp + $cfg_cli_time * 3600) : $minute;
	if(strpos($cron['minute'],',')!==FALSE) $cron['minute']=explode(',', $cron['minute']);
	$nexttime = array();
	if($cron['hour'] == -1) {
		$nexttime['hour'] = $hour;
		if(($nextminute = cronnextminute($cron['minute'], $minute)) === false) {
			++$nexttime['hour'];
			if(is_array($cron['minute']))$nextminute = $cron['minute'][0];
			else $nextminute = $cron['minute'];
		}
		$nexttime['minute'] = $nextminute;
	} elseif($cron['hour'] != -1 ) {
		$nextminute = cronnextminute($cron['minute'], $minute);
		if($cron['hour'] < $hour||($cron['hour'] == $hour && $nextminute === false)) {
			$nexttime['hour'] = $nexttime['minute'] = -1;
		} 
		else{
			$nexttime['hour']=$cron['hour'];
			if(is_array($cron['minute']))$nexttime['minute'] = $cron['minute'][0];
			else $nexttime['minute'] = $cron['minute'];
		}
	}

	return $nexttime;
}

function cronnextminute($nextminutes, $minutenow) {
	if(is_array($nextminutes)){
		foreach($nextminutes as $nextminute) {
			if($nextminute > $minutenow) {
				return $nextminute;
			}
		}
	}else{
		if($nextminutes > $minutenow) {
				return $nextminutes;
		}
	}
	return false;
}
?>