<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}
$id = empty($id) ? 0 : intval($id);

if($action=="add")
{
	if(empty($webname))
	{
		ShowMsg("网站名称没有填写，请返回检查","-1");
		exit();
	}
	if(empty($url))
	{
		ShowMsg("网站地址没有填写，请返回检查","-1");
		exit();
	}
	if(empty($sortrank)) 
		{
		$trow = $dsql->GetOne("select max(sortrank)+1 as dd from sea_flink");
		$sortrank = $trow['dd'];
		}
	if (!is_numeric($sortrank)) $sortrank=1;
	$dtime = time();
	$query = "Insert Into `sea_flink`(sortrank,url,webname,logo,msg,dtime,ischeck) Values('$sortrank','$url','$webname','$logo','$email','$dtime','1'); ";
	$rs = $dsql->ExecuteNoneQuery($query);
	if($rs)
	{
		ShowMsg("成功增加一个链接!","admin_link.php");
		exit();
	}
	else
	{
		ShowMsg("增加链接时出错，请向官方反馈，原因：".$dsql->GetError(),"javascript:;");
		exit();
	}
}
elseif($action=="save")
{
	if(empty($webname))
	{
		ShowMsg("网站名称没有填写，请返回检查","-1");
		exit();
	}
	if(empty($url))
	{
		ShowMsg("网站地址没有填写，请返回检查","-1");
		exit();
	}
	if(empty($sortrank)) 
		{
		$trow = $dsql->GetOne("select max(sortrank)+1 as dd from sea_flink");
		$sortrank = $trow['dd'];
		}
	if (!is_numeric($sortrank)) $sortrank=1;
	$query = "Update `sea_flink` set sortrank='$sortrank',url='$url',webname='$webname',logo='$logo',msg='$msg',ischeck='1' where id='$id' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功更改一个链接！","admin_link.php");
	exit();
}
elseif($action=="last")
{
	$row=$dsql->GetOne("select sortrank from `sea_flink` where id='$id'");
	$cur=$row['sortrank'];
	$row=$dsql->GetOne("select count(*) as dd from `sea_flink` where sortrank<'$cur'");
	$cou=$row['dd'];
	if($cou>0)
	{
		$row=$dsql->GetOne("select sortrank from `sea_flink` where sortrank<'$cur' order by sortrank desc");
		$flag=$row['sortrank'];
		$dsql->ExecuteNoneQuery("update `sea_flink` set sortrank='$flag' where id='$id'");
	}
	else
	{
		$dsql->ExecuteNoneQuery("update `sea_flink` set sortrank=sortrank-1 where id='$id'");
	}
	header("Location:admin_link.php?id=$id");
	exit;
}
elseif($action=="next")
{
	$row=$dsql->GetOne("select sortrank from `sea_flink` where id='$id'");
	$cur=$row['sortrank'];
	$row=$dsql->GetOne("select count(*) as dd from `sea_flink` where sortrank>'$cur'");
	$cou=$row['dd'];
	if($cou>0)
	{
		$row=$dsql->GetOne("select sortrank from `sea_flink` where sortrank>'$cur' order by sortrank desc");
		$flag=$row['sortrank'];
		$dsql->ExecuteNoneQuery("update `sea_flink` set sortrank='$flag' where id='$id'");
	}
	else
	{
		$dsql->ExecuteNoneQuery("update `sea_flink` set sortrank=sortrank+1 where id='$id'");
	}
	header("Location:admin_link.php?id=$id");
	exit;
}
elseif($action=="del")
{
	$dsql->ExecuteNoneQuery("delete from `sea_flink` where id='$id'");
	header("Location:admin_link.php?id=$id");
	exit;
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的链接","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from `sea_flink` where id in ($ids)");
	header("Location:admin_link.php");
	exit;
}
elseif($action=="editall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要修改的链接","-1");
		exit();
	}
	foreach($e_id as $id)
	{
		$webname=$_POST["webname$id"];
		$url=$_POST["url$id"];
		$sortrank=$_POST["sortrank$id"];
	if(empty($webname))
	{
		ShowMsg("网站名称没有填写，请返回检查","-1");
		exit();
	}
	if(empty($url))
	{
		ShowMsg("网站地址没有填写，请返回检查","-1");
		exit();
	}
	if(empty($sortrank)) 
		{
		$trow = $dsql->GetOne("select max(sortrank)+1 as dd from sea_flink");
		$sortrank = $trow['dd'];
		}
	if (!is_numeric($sortrank)) $sortrank=1;
	$dsql->ExecuteNoneQuery("update sea_flink set webname='$webname',url='$url',sortrank='$sortrank' where id=".$id);
	}
	header("Location:admin_link.php");
	exit;
}
else
{
	include(sea_ADMIN.'/templets/admin_link.htm');
	exit();
}
?>