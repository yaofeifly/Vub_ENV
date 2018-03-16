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
	if(empty($name))
	{
		ShowMsg("专题名称没有填写，请返回检查","-1");
		exit();
	}
	$name = str_replace('\'', ' ', $name);
	if(empty($template)) $template='topic.html';
	if(empty($pic)) $pic='zt.jpg';
	if(empty($vod)) $vod='0';
	if(empty($keyword)) $vod='';
	if(empty($enname)) $enname=Pinyin(stripslashes($name));;
	if(empty($sort)) 
		{
		$trow = $dsql->GetOne("select max(sort)+1 as dd from sea_topic");
		$sort = $trow['dd'];
		}
	if (!is_numeric($sort)) $sort=1;
	$in_query = "insert into `sea_topic`(name,enname,template,pic,sort,vod,keyword) Values('$name','$enname','$template','$pic','$sort',0,'$keyword')";
	if(!$dsql->ExecuteNoneQuery($in_query))
	{
		ShowMsg("增加专题失败，请检查您的输入是否存在问题！","-1");
		exit();
	}
	clearTopicCache();
	ShowMsg("成功创建一个专题！","admin_topic.php");
	exit();
}
elseif($action=="last")
{
	$row=$dsql->GetOne("select sort from `sea_topic` where id='$id'");
	$cur=$row['sort'];
	$row=$dsql->GetOne("select count(*) as dd from `sea_topic` where sort<'$cur'");
	$cou=$row['dd'];
	if($cou>0)
	{
		$row=$dsql->GetOne("select sort from `sea_topic` where sort<'$cur' order by sort desc");
		$flag=$row['sort'];
		$dsql->ExecuteNoneQuery("update `sea_topic` set sort='$flag' where id='$id'");
	}
	else
	{
		$dsql->ExecuteNoneQuery("update `sea_topic` set sort=sort-1 where id='$id'");
	}
	header("Location:admin_topic.php?id=$id");
	exit;
}
elseif($action=="next")
{
	$row=$dsql->GetOne("select sort from `sea_topic` where id='$id'");
	$cur=$row['sort'];
	$row=$dsql->GetOne("select count(*) as dd from `sea_topic` where sort>'$cur'");
	$cou=$row['dd'];
	if($cou>0)
	{
		$row=$dsql->GetOne("select sort from `sea_topic` where sort>'$cur' order by sort desc");
		$flag=$row['sort'];
		$dsql->ExecuteNoneQuery("update `sea_topic` set sort='$flag' where id='$id'");
	}
	else
	{
		$dsql->ExecuteNoneQuery("update `sea_topic` set sort=sort+1 where id='$id'");
	}
	header("Location:admin_topic.php?id=$id");
	exit;
}
elseif($action=="del")
{
	$dsql->ExecuteNoneQuery("delete from `sea_topic` where id='$id'");
	$dsql->ExecuteNoneQuery("update `sea_data` set v_topic=0 where v_topic='$id'");
	clearTopicCache();
	header("Location:admin_topic.php?id=$id");
	exit;
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的专题","admin_topic.php");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from `sea_topic` where id in ($ids)");
	$dsql->ExecuteNoneQuery("update `sea_data` set v_topic=0 where v_topic in ($ids)");
	clearTopicCache();
	header("Location:admin_topic.php");
	exit;
}
elseif($action=="edit")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要修改的专题","admin_topic.php");
		exit();
	}
	foreach($e_id as $id)
	{
		$name=$_POST["name$id"];
		$template=$_POST["template$id"];
		$enname=$_POST["enname$id"];
		$pic=$_POST["pic$id"];
		$sort=$_POST["sort$id"];
		$keyword=$_POST["keyword$id"];
	if(empty($name))
	{
		ShowMsg("专题名称没有填写，请返回检查","-1");
		exit();
	}
	$name = str_replace('\'', ' ', $name);
	if(empty($template)) $template='topic.html';
	if(empty($enname)) $enname=Pinyin(stripslashes($name));;
	if(empty($sort)) 
		{
		$trow = $dsql->GetOne("select max(torder)+1 as dd from sea_topic");
		$sort = $trow['dd'];
		}
	if (!is_numeric($sort)) $sort=1;
	$dsql->ExecuteNoneQuery("update sea_topic set name='$name',enname='$enname',template='$template',pic='$pic',sort='$sort',keyword='$keyword' where id=".$id);
	}
	clearTopicCache();
	header("Location:admin_topic.php");
	exit;
}
else
{
include(sea_ADMIN.'/templets/admin_topic.htm');
exit();
}

function clearTopicCache()
{
	global $cfg_iscache,$cfg_cachemark;
	if($cfg_iscache)
	{
		$TypeCacheFile=sea_DATA."/cache/".$cfg_cachemark.md5('array_Topic_Lists_all').".inc";
		if(is_file($TypeCacheFile)) unlink($TypeCacheFile);
	}
}
?>