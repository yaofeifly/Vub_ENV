<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

if($action=="add")
{
	if(m_ereg("[^0-9a-zA-Z_@!\.-]",$pwd) || m_ereg("[^0-9a-zA-Z_@!\.-]",$username) || m_ereg("[^0-9a-zA-Z_@!\.-]",$pwd2))
	{
		ShowMsg("密码或用户名不合法，<br />请使用[0-9a-zA-Z_@!.-]内的字符！","-1",0,3000);
		exit();
	}
	if($pwd!=$pwd2)
	{
		ShowMsg("密码和确认密码不一样，请返回修改！","-1",0,3000);
		exit();
	}
	$row = $dsql->GetOne("Select count(*) as dd from `sea_admin` where name like '$username' ");
	if($row['dd']>0)
	{
		ShowMsg('用户名已存在！','-1');
		exit();
	}
	$groupid = $groupid ? intval($groupid) : 2;
	$mpwd = md5($pwd);
	$pwd = substr(md5($pwd),5,20);

	$inquery = "Insert Into `sea_admin`(password,name,groupid,state) values('$pwd','$username',$groupid,1)";
	$dsql->ExecuteNoneQuery($inquery);
	ShowMsg('成功增加一个用户！','admin_manager.php');
	exit();
}
elseif($action=="save")
{
	$pwd = trim($pwd);
	$pwd2 = trim($pwd2);
	if(m_ereg("[^0-9a-zA-Z_@!\.-]",$pwd) || m_ereg("[^0-9a-zA-Z_@!\.-]",$username) || m_ereg("[^0-9a-zA-Z_@!\.-]",$pwd2))
	{
		ShowMsg("密码或用户名不合法，<br />请使用[0-9a-zA-Z_@!.-]内的字符！","-1",0,3000);
		exit();
	}
	if($pwd!=$pwd2)
	{
		ShowMsg("密码和确认密码不一样，请返回修改！","-1",0,3000);
		exit();
	}
	$pwdm = '';
	if($pwd!='')
	{
		$pwdm = ",pwd='".md5($pwd)."'";
		$pwd = ",password='".substr(md5($pwd),5,20)."'";
	}
	$groupid = $groupid ? intval($groupid) : 2;
	$query = "Update `sea_admin` set name='$username',groupid='$groupid',state='$state' $pwd where id='$id'";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功更改一个帐户！","admin_manager.php");
	exit();
}
elseif($action=="del")
{
	$rs = $dsql->ExecuteNoneQuery2("delete from `sea_admin` where id='$id' And id<>1 And id<>'".$cuserLogin->getUserID()."'");
	if($rs>0)
	{
		header("Location:admin_manager.php");
	}
	else
	{
		ShowMsg("不能删除id为1的创建人帐号，不能删除自己！","admin_manager.php",0,3000);
	}
	exit();
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的链接","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from `sea_admin` where id in ($ids) And id<>1 And id<>'".$cuserLogin->getUserID()."'");
	header("Location:admin_manager.php");
	exit();
}
else
{
	include(sea_ADMIN.'/templets/admin_manager.htm');
	exit;
}

function getManagerLevel($groupid)
{
	if($groupid==1){
		return "系统管理员";
	}else if($groupid==2){
		return "网站编辑员";
	}else{
		return "未知类型";
	}
}

function getManagerState($s)
{
	if($s==1){
		return "激活";
	}else if($s==0){
		return "锁定";
	}else{
		return "未知";
	}
}
?>