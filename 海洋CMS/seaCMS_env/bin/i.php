<?php
session_start();
require_once("include/common.php");
require_once(sea_INC."/main.class.php");

$i=file_get_contents("data/admin/i.txt");
if($i==0){showmsg('系统已经关闭积分推广系统', 'index.php');exit;}

$u=addslashes($_GET['uid']);
if(empty($u) OR !is_numeric($u)){showmsg('无法获取目标用户ID', 'index.php');exit;}

 
$bip = GetIP();   

$row = $dsql->GetOne("Select * from sea_ie where ip='$bip'");
//print_r($row);
if(!is_array($row))
{
	$dsql->ExecuteNoneQuery("insert into sea_ie values('','$bip',NOW())");
	$sql="Update sea_member set points = points+$i where id=$u";
	$dsql->ExecuteNoneQuery("$sql");
	showmsg('感谢您的支持，现在将自动跳转到首页', 'index.php');exit;
}

$btime=time();
$stime=strtotime($row['2']);
if($btime-$stime > 86400)
{
	$dsql->ExecuteNoneQuery("Update sea_ie set addtime = NOW() where ip='$bip'");
	$sql="Update sea_member set points = points+$i where id=$u";
	$dsql->ExecuteNoneQuery("$sql");
	showmsg('感谢您的支持，现在将自动跳转到首页', 'index.php');exit;
}
else
{
	showmsg('无效的提交，现在将转向首页', 'index.php');exit;
}
?>