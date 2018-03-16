<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($action))
{
	$action = '';
}

$id = empty($id) ? 0 : intval($id);

if($action=="edit")
{
	$row = $dsql->GetOne("Select * From `sea_mytag` where aid='$id'");
	include(sea_ADMIN.'/templets/admin_selflabel.htm');
	exit;
}
elseif($action=="editsave")
{
	$query = "Update `sea_mytag` set tagname='$tagname',tagdes='$tagdes',tagcontent='$tagcontent' where aid='$id' ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功更改一个自定义标签！","admin_selflabel.php?page=".$page);
	exit();
}
elseif($action=="addsave")
{
	$tagname = trim($tagname);
	$row = $dsql->GetOne("Select aid From sea_mytag where tagname='$tagname'");
	if(is_array($row))
	{
		ShowMsg("在相同栏目下已经存在同名的标签！","-1");
		exit();
	}
	$addtime = time();
	$inQuery = "Insert Into sea_mytag(tagname,tagdes,tagcontent,addtime) Values('$tagname','$tagdes','$tagcontent','$addtime'); ";
	$dsql->ExecuteNoneQuery($inQuery);
	ShowMsg("成功增加一个自定义标签！","admin_selflabel.php");
	exit();
}
elseif($action=="add")
{
	include(sea_ADMIN.'/templets/admin_selflabel.htm');
	exit;
}
elseif($action=="del")
{
	$dsql->ExecuteNoneQuery("delete from sea_mytag where aid='$id'");
	ShowMsg("成功删除一个自定义标签！","admin_selflabel.php");
	exit();
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("没有选择标签，请返回选择！","admin_selflabel.php");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_mytag where aid in(".$ids.")");
	ShowMsg("成功删除所选自定义标签！","admin_selflabel.php");
	exit();
}
else
{
	include(sea_ADMIN.'/templets/admin_selflabel.htm');
	exit;
}
?>