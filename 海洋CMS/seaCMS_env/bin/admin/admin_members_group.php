<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

if($action=='editsave')
{
	$gname = trim($gname);
	if(empty($gname)){
		ShowMsg("用户组名称没有填写","-1");
		exit();
	}
	$gtype = implode(',',$gtype);
	$g_auth = implode(',',$g_auth);
	$g_upgrade = intval($g_upgrade);
	$g_authvalue = intval($g_authvalue);
	$gname = cn_substrR($gname,20);
	$query = "update `sea_member_group` set gname='$gname',gtype='$gtype',g_auth='$g_auth',g_upgrade='$g_upgrade',g_authvalue='$g_authvalue' where gid='$id'";
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg("更新成功！","admin_members_group.php");
	}
	exit();
}
elseif($action=='addsave')
{
	$gname = trim($gname);
	if(empty($gname)){
		ShowMsg("用户组名称没有填写","-1");
		exit();
	}
	$row = $dsql->GetOne("Select gid From sea_member_group where gname='$gname'");
	if(is_array($row))
	{
		ShowMsg("已经存在同名的用户组！","-1");
		exit();
	}
	$gtype = implode(',',$gtype);
	$g_auth = implode(',',$g_auth);
	$g_upgrade = intval($g_upgrade);
	$g_authvalue = intval($g_authvalue);
	$gname = cn_substrR($gname,20);
	$query = "INSERT INTO `sea_member_group`(`gname`,`gtype`,`g_auth`,`g_upgrade`,`g_authvalue`) VALUES ('$gname','$gtype','$g_auth','$g_upgrade','$g_authvalue');";
	if($dsql->ExecuteNoneQuery($query))
	{
		ShowMsg("成功增加一个用户组！","admin_members_group.php");
	}
	exit();
}
elseif($action=='edit')
{
	$row = $dsql->GetOne("Select * From `sea_member_group` where gid=$id");
}
elseif($action=='del')
{
	if($dsql->ExecuteNoneQuery("Delete From `sea_member_group` where gid='$id' "))
	{
		ShowMsg("删除成功！","admin_members_group.php");
	}
	exit;
}
elseif($action=='delall')
{
	if(empty($g_id))
	{
		ShowMsg("请选择需要删除的用户组","-1");
		exit();
	}
	foreach($g_id as $id){
		$dsql->ExecuteNoneQuery("Delete From `sea_member_group` where gid='$id' ");
	}
	ShowMsg("删除成功！","admin_members_group.php");
	exit;
}
include(sea_ADMIN.'/templets/admin_members_group.htm');
exit();