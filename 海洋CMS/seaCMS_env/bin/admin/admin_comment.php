<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

$id = empty($id) ? 0 : intval($id);

if($action=="editcomsave")
{
	delcommentcache($id);
	$msg = cn_substrR($msg,2500);
	$adminmsg = trim($adminmsg);
	if($adminmsg!="")
	{
		$adminmsg = cn_substrR($adminmsg,1500);
		$adminmsg = str_replace("<","&lt;",$adminmsg);
		$adminmsg = str_replace(">","&gt;",$adminmsg);
		$adminmsg = str_replace("  ","&nbsp;&nbsp;",$adminmsg);
		$adminmsg = str_replace("\r\n","<br/>\n",$adminmsg);
		$msg = $msg."<br/>\n"."<font color=red>管理员回复： $adminmsg</font>\n";
	}
	$query = "update `sea_comment` set username='$username',msg='$msg',ischeck=1 where id=$id";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功回复一则评论！",$v_back);
	exit();
}
if($action=="editgbooksave")
{
	$msg = cn_substrR($msg,2500);
	$adminmsg = trim($adminmsg);
	if($adminmsg!="")
	{
		$adminmsg = cn_substrR($adminmsg,1500);
		$adminmsg = str_replace("<","&lt;",$adminmsg);
		$adminmsg = str_replace(">","&gt;",$adminmsg);
		$adminmsg = str_replace("  ","&nbsp;&nbsp;",$adminmsg);
		$adminmsg = str_replace("\r\n","<br/>\n",$adminmsg);
		$msg = $msg."<br/>\n"."<font color=red>管理员回复： $adminmsg</font>\n";
	}
	$query = "update `sea_guestbook` set uname='$uname',msg='$msg',ischeck=1 where id=$id";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功回复一则留言！",$v_back);
	exit();
}
elseif($action=="delcomment")
{
	delcommentcache($id);
	$dsql->ExecuteNoneQuery("delete from sea_comment where id=".$id);
	ShowMsg("成功删除一则评论！","admin_comment.php");
	exit();
}
elseif($action=="delallcomment")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的评论","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	delcommentcache($ids);
	$dsql->ExecuteNoneQuery("delete from sea_comment where id in(".$ids.")");
	ShowMsg("成功删除所选评论！","admin_comment.php");
	exit();
}
elseif($action=="deltotalcomment")
{
	@cache_clear(sea_ROOT.'/data/cache/review/0');
	$dsql->ExecuteNoneQuery("delete from sea_comment");
	ShowMsg("成功删除所有评论！","admin_comment.php");
	exit();
}
elseif($action=="checkcomment")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要审核的评论","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("update sea_comment set ischeck=1 where id in(".$ids.")");
	ShowMsg("成功审核所选评论！","admin_comment.php");
	exit();
}
elseif($action=="gbook" || $action=="viewgbook" || $action=="editgbook")
{
	include(sea_ADMIN.'/templets/admin_gbook.htm');
	exit();
}
elseif($action=="delgbook")
{
	$dsql->ExecuteNoneQuery("delete from sea_guestbook where id=".$id);
	ShowMsg("成功删除一则留言！","admin_comment.php?action=gbook");
	exit();
}
elseif($action=="delallgbook")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的留言","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_guestbook where id in(".$ids.")");
	ShowMsg("成功删除所选留言！","admin_comment.php?action=gbook");
	exit();
}
elseif($action=="deltotalgbook")
{
	$dsql->ExecuteNoneQuery("delete from sea_guestbook");
	ShowMsg("成功删除所有留言！","admin_comment.php?action=gbook");
	exit();
}
elseif($action=="checkgbook")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要审核的留言","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("update sea_guestbook set ischeck=1 where id in(".$ids.")");
	ShowMsg("成功审核所选留言！","admin_comment.php?action=gbook");
	exit();
}
elseif($action=="delreporterror")
{
	$dsql->ExecuteNoneQuery("delete from sea_erradd where id=".$id);
	ShowMsg("成功删除一条报错信息！","admin_comment.php?action=reporterror");
	exit();
}
elseif($action=="delallreporterror")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的信息","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_erradd where id in(".$ids.")");
	ShowMsg("成功删除所选信息！","admin_comment.php?action=reporterror");
	exit();
}
else
{
	include(sea_ADMIN.'/templets/admin_comment.htm');
	exit();
}

function delcommentcache($id)
{
	global $dsql;
	$dsql->setQuery("select v_id from sea_comment where id in (".$id.")");
	$dsql->Execute("delcommentcache");
	while($row = $dsql->GetArray("delcommentcache"))
	{
		if(file_exists(sea_DATA.'/cache/review/0/'.$row['v_id'].'.js'))
		{
			delfile(sea_DATA.'/cache/review/0/'.$row['v_id'].'.js');
		}
	}
}

function IsCheck($st)
{
	return $st==1 ? "[已审核]" : "<font color='red'>[未审核]</font>";
}
?>