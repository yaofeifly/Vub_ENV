<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/config.user.inc.php");
CheckPurview();

if(empty($action))
{
	$action = '';
}
elseif($action=="add")
{
	$num=$_POST['num'];
	$limit=$_POST['limit'];
	for($i=0;$i<$num;$i++)
	{
		$md5=uniqid()."-".rand();
		$key=$limit."-".md5($md5);
		$addsql="INSERT INTO `sea_cck`(id,ckey,climit,maketime,usetime,uname,status) VALUES (NULL, '$key', '$limit', NOW(), NULL, NULL, '0')";
		$dsql->ExecuteNoneQuery($addsql);
	}
	ShowMsg("执行成功","admin_pay.php");
	exit;
}
elseif($action=="del")
{
		
		$delsql="DELETE FROM `sea_cck` WHERE id = '$id'";
		$dsql->ExecuteNoneQuery($delsql);
		ShowMsg("删除成功","-1");
		exit;
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_cck where id in(".$ids.")");
	ShowMsg("批量删除成功","admin_pay.php");
	exit();
}


include(sea_ADMIN.'/templets/admin_pay.htm');
exit();

?>