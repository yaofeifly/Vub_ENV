<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

if($action=="update")
{
	if($ids = implodeids($delete)) {
		$dsql->ExecuteNoneQuery("DELETE FROM sea_crons WHERE cronid IN ($ids) AND type='user'");
	}
	if(is_array($namenew)) {
		foreach($namenew as $id => $name) {
			$dsql->ExecuteNoneQuery("UPDATE sea_crons SET name='$name',available='".(!isset($availablenew[$id])?'0':$availablenew[$id])."' ".($availablenew[$id] ? '' : ', nextrun=\'0\'')." WHERE cronid='$id'");
		}
	}

	$sql="SELECT cronid, filename FROM sea_crons";
	$dsql->SetQuery($sql);
	$dsql->Execute('crons_list');
	$filename=$row->filename;
	if(strpos($filename,'$')!==false){$filenameArr=explode("$",$filename);$filename=$filenameArr[0];}
	if(strpos($filename,'#')!==false){$filenameArr=explode("#",$filename);$filename=$filenameArr[0];}
	while($cron = $dsql->GetObject('crons_list')) {
		if(!file_exists(sea_INC.'/crons/'.$filename)) {
			$dsql->ExecuteNoneQuery("UPDATE sea_crons SET available='0', nextrun='0' WHERE cronid='".$cron->cronid."'");
		}
	}
	updatecronscache();
	header("Location:admin_cron.php?");
	exit;
}
elseif($action=="edit")
{
	$cron = $dsql->GetOne("SELECT * FROM sea_crons WHERE cronid='$id'");
	if(!is_array($cron)){
		ShowMsg("出现错误","-1");
		exit;
	}
	$cron['filename'] = str_replace(array('..', '/', '\\'), array('', '', ''), $cron['filename']);
	$cronminute = $cron['minute'];
	$cron['minute'] = explode(",", $cron['minute']);
	$weekdayselect = $dayselect = $hourselect = '';
	for($i = 0; $i <= 6; $i++) {
		$weekdayselect .= "<option value=\"$i\" ".($cron['weekday'] == $i ? 'selected' : '').">".$lang['misc_cron_wesea_day_'.$i]."</option>";
	}

	for($i = 1; $i <= 31; $i++) {
		$dayselect .= "<option value=\"$i\" ".($cron['day'] == $i ? 'selected' : '').">$i 日</option>";
	}

	for($i = 0; $i <= 23; $i++) {
		$hourselect .= "<option value=\"$i\" ".($cron['hour'] == $i ? 'selected' : '').">$i 时</option>";
	}
	include(sea_ADMIN.'/templets/admin_cron.htm');
	exit();
}
elseif($action=="editsave")
{
	$daynew = $weekdaynew != -1 ? -1 : $daynew;
	if(strpos($minutenew, ',') !== FALSE) {
		$minutenew = explode(',', $minutenew);
		foreach($minutenew as $key => $val) {
			$minutenew[$key] = $val = intval($val);
			if($val < 0 || $var > 59) {
				unset($minutenew[$key]);
			}
		}
		$minutenew = array_slice(array_unique($minutenew), 0, 12);
		asort($minutenew);
		$minutenew = implode(",", $minutenew);
	} else {
		$minutenew = intval($minutenew);
		$minutenew = $minutenew >= 0 && $minutenew < 60 ? $minutenew : '';
	}

	if(preg_match("/[\\\\\/\:\*\?\"\<\>\|]+/", $filenamenew)) {
		ShowMsg("文件名非法","-1");
		exit;
	} elseif(!is_readable(sea_INC.($cronfile = "/crons/$filenamenew"))) {
		ShowMsg("您指定的任务脚本文件($cronfile)不存在或包含语法错误，请返回修改","-1");
		exit;
	} elseif($weekdaynew == -1 && $daynew == -1 && $hournew == -1 && $minutenew === '') {
		ShowMsg("时间不能都为空","-1");
		exit;
	}
	if(!isset($collectIDa))
	{
		$rfromarr =  explode('#',$_POST['resourcefrom']);
		$rid = $rfromarr[0];
		$url = $rfromarr[1];
		$filename = "autoreslib.php".'$'.$rid.'$'.$url;
	}else
	{
		$filename = "autocollect.php".'#'.$_POST['collectIDa'].'#'.$_POST['collectPageNum'].'#'.$_POST['autogetconnum'];
	}
	if(!isset($filenamenew)){
		$dsql->ExecuteNoneQuery("UPDATE sea_crons SET weekday='$weekdaynew', day='$daynew', hour='$hournew', minute='$minutenew', filename='".trim($filename)."' WHERE cronid='$id'");
	}
	else{
		$dsql->ExecuteNoneQuery("UPDATE sea_crons SET weekday='$weekdaynew', day='$daynew', hour='$hournew', minute='$minutenew', filename='".trim($filenamenew)."' WHERE cronid='$id'");
	}
	require_once sea_INC.'/cron.func.php';
	$cron = $dsql->GetOne("SELECT * FROM sea_crons WHERE cronid='$id'");
	cronnextrun($cron,false);
	updatecronscache();
	ShowMsg("计划任务更新成功","admin_cron.php");
	exit;
}
elseif($action=="addCron")
{
	$daynew = $weekdaynew != -1 ? -1 : $daynew;
	if(strpos($minutenew, ',') !== FALSE) {
		$minutenew = explode(',', $minutenew);
		foreach($minutenew as $key => $val) {
			$minutenew[$key] = $val = intval($val);
			if($val < 0 || $var > 59) {
				unset($minutenew[$key]);
			}
		}
		$minutenew = array_slice(array_unique($minutenew), 0, 12);
		asort($minutenew);
		$minutenew = implode(",", $minutenew);
	} else {
		$minutenew = intval($minutenew);
		$minutenew = $minutenew >= 0 && $minutenew < 60 ? $minutenew : '';
	}
	if($newname==''){
		ShowMsg("请填写计划名称","-1");
		exit;
	}
	if($weekdaynew == -1 && $daynew == -1 && $hournew == -1 && $minutenew == '') {
		ShowMsg("时间不能都为空","-1");
		exit;
	}
	$newname = trim($newname);
	if($PlanMode==0)
	{
		$downpic = isset($downpic)?1:0;
		$rfromarr =  explode('#',$_POST['resourcefrom']);
		$rid = $rfromarr[0];
		$url = $rfromarr[1];
		$filename = "autoreslib.php".'$'.$rid.'$'.$url.'$'.$downpic;
		$dsql->ExecuteNoneQuery("INSERT INTO sea_crons (name, type, available, weekday, day, hour, minute, nextrun,filename)
			VALUES ('".dhtmlspecialchars($newname)."', 'user', '0', '$weekdaynew', '$daynew', '$hournew', '$minutenew', '$timestamp','$filename')");
	}
	elseif($PlanMode==1)
	{
		$dsql->ExecuteNoneQuery("INSERT INTO sea_crons (name, type, available, weekday, day, hour, minute, nextrun,filename)
			VALUES ('".dhtmlspecialchars($newname)."', 'user', '0', '$weekdaynew', '$daynew', '$hournew', '$minutenew', '$timestamp','automakehtml.php')");
	}
	elseif($PlanMode==2)
	{
		$filename = "autocollect.php".'#'.$_POST['collectID'].'#'.$_POST['collectPageNum'].'#'.$_POST['autogetconnum'];
		$dsql->ExecuteNoneQuery("INSERT INTO sea_crons (name, type, available, weekday, day, hour, minute, nextrun,filename)
			VALUES ('".dhtmlspecialchars($newname)."', 'user', '0', '$weekdaynew', '$daynew', '$hournew', '$minutenew', '$timestamp','$filename')");
	}
	elseif($PlanMode==3)
	{
		$dsql->ExecuteNoneQuery("INSERT INTO sea_crons (name, type, available, weekday, day, hour, minute, nextrun,filename)
			VALUES ('".dhtmlspecialchars($newname)."', 'user', '0', '$weekdaynew', '$daynew', '$hournew', '$minutenew', '$timestamp','bak-table.php')");
	}
	updatecronscache();
	ShowMsg("计划任务更新成功","admin_cron.php");
	exit;
}
elseif($action=="addCustomCron")
{
	if($newname = trim($newname)) {
		$dsql->ExecuteNoneQuery("INSERT INTO sea_crons (name, type, available, weekday, day, hour, minute, nextrun)
			VALUES ('".dhtmlspecialchars($newname)."', 'user', '0', '-1', '-1', '-1', '', '0')");
	}
	header("Location:admin_cron.php?");
	exit;
}
elseif($action=="run")
{
	$cron = $dsql->GetOne("SELECT * FROM sea_crons WHERE cronid='$id'");
	if(!is_array($cron)){
		ShowMsg("出现错误","-1");
		exit;
	}	
	$filename=$cron[filename];
	if(strpos($filename,'$')!==false){
	    $filenameArr=explode("$",$filename);
		$filename=$filenameArr[0];
		$rid1=$filenameArr[1];
		$var_url1=$filenameArr[2];
	}elseif(strpos($filename,'#')!==false){
	    $filenameArr=explode("#",$filename);
		$filename=$filenameArr[0];
		$collectID=$filenameArr[1];
		$collectPageNum=$filenameArr[2]; 
		$getconnum=$filenameArr[3];   
	}
	if(!@include_once sea_INC.($cronfile = "/crons/$filename")) {
		ShowMsg("您指定的任务脚本文件($cronfile)不存在或包含语法错误，请返回修改","-1");
		exit;
	} else {
		require_once sea_INC.'/cron.func.php';
		cronnextrun($cron);
		ShowMsg("计划任务执行成功","admin_cron.php");
		exit;
	}
}
else
{
	$sqlStr="SELECT * FROM sea_crons ORDER BY type DESC";
	$dsql->SetQuery($sqlStr);
	$dsql->Execute('crons_list');
	while($row=$dsql->GetObject('crons_list'))
	{
		$disabled = $row->weekday == -1 && $row->day == -1 && $row->hour == -1 && $row->minute == '' ? 'disabled' : '';
		if($row->day > 0 && $row->day < 32) {
			$cron[time] = '每月'.$row->day.'日';
		} elseif($row->weekday >= 0 && $row->weekday < 7) {
			$cron[time] = '每周'.$lang['misc_cron_wesea_day_'.$row->weekday];
		} elseif($row->hour >= 0 && $row->hour < 24) {
			$cron[time] = '每日';
		} else {
			$cron[time] = '每小时';
		}

		$cron[time] .= $row->hour >= 0 && $row->hour < 24 ? sprintf('%02d', $row->hour).'时' : '';

		if(!in_array($row->minute, array(-1, ''))) {
			foreach($row->minute = explode(",", $row->minute) as $k => $v) {
				$row->minute[$k] = sprintf('%02d', $v);
			}
			$row->minute = implode(',', $row->minute);
			$cron[time] .= $row->minute.'分';
		} else {
			$cron[time] .= '00分';
		}
		$filename=$row->filename;
		if(strpos($filename,'$')!==false){$filenameArr=explode("$",$filename);$filename=$filenameArr[0];}
		if(strpos($filename,'#')!==false){$filenameArr=explode("#",$filename);$filename=$filenameArr[0];}
		$cron[lastrun] = $row->lastrun ? gmdate("Y-n-j<\b\\r />H:i", $row->lastrun + $cfg_cli_time * 3600) : '<b>N/A</b>';
		$cron[nextcolor] = $row->nextrun && $row->nextrun + $cfg_cli_time * 3600 < $timestamp ? 'style="color: #ff0000"' : '';
		$cron[nextrun] = $row->nextrun ? gmdate("Y-n-j<\b\\r />H:i", $row->nextrun + $cfg_cli_time * 3600) : '<b>N/A</b>';
		$showtablerow[]=array(
			"<input class=\"checkbox\" type=\"checkbox\" name=\"delete[]\" value=\"$row->cronid\" ".($row->type == 'system' ? 'disabled' : '').">",
			"<input type=\"text\" class=\"txt\" name=\"namenew[$row->cronid]\" size=\"20\" value=\"$row->name\"><br /><b>$filename</b>",
			"<input class=\"checkbox\" type=\"checkbox\" name=\"availablenew[$row->cronid]\" value=\"1\" ".($row->available ? 'checked' : '')." $disabled>",
			$crom[mtype]=$row->type == 'system' ? '内置' :'自定义',
			$cron[time],
			$cron[lastrun],
			$cron[nextrun],
			"<a href=\"?action=edit&id=$row->cronid\" class=\"act\">编辑</a><br />".
			($row->available ? " <a href=\"?action=run&id=$row->cronid\" class=\"act\">运行</a>" : " <a href=\"###\" class=\"act\" disabled>运行</a>")
		);
	}
	$weekdayselect = $dayselect = $hourselect = '';
	for($i = 0; $i <= 6; $i++) {
		$weekdayselect .= "<option value=\"$i\">".$lang['misc_cron_wesea_day_'.$i]."</option>";
	}

	for($i = 1; $i <= 31; $i++) {
		$dayselect .= "<option value=\"$i\">$i 日</option>";
	}

	for($i = 0; $i <= 23; $i++) {
		$hourselect .= "<option value=\"$i\">$i 时</option>";
	}
	include(sea_ADMIN.'/templets/admin_cron.htm');
	exit();
}
?>