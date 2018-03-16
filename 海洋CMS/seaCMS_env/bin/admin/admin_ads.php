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
	include(sea_ADMIN.'/templets/admin_ads_add.htm');
	exit();
}
elseif($action=="edit")
{
	$row = $dsql->GetOne("Select * From `sea_myad` where aid='$id'");
	include(sea_ADMIN.'/templets/admin_ads_edit.htm');
	exit();
}
elseif($action=="editsave")
{
	$adenname = trim($adenname);
	if(empty($adname) || empty($adenname)){
		ShowMsg("广告位名称或者广告位标识没有填写","-1");
		exit();
	}
	if(!m_ereg("^[0-9a-zA-Z_\-]+$",$adenname)){
		ShowMsg("文件名不合法","-1");
		exit;
	}
	$timeset=time();
	$query = "Update `sea_myad` set adname='$adname',adenname='$adenname',timeset='$timeset',intro='$intro',adsbody='$adsbody' where aid='$id'";
	$dsql->ExecuteNoneQuery($query);
	createTextFile(stripslashes($adsbody),sea_ROOT."/js/".$cfg_ads_dir."/".$adenname.".js");
	ShowMsg("成功更改一则广告代码！","admin_ads.php?id=".$id."&page=".$page);
	exit();
}
elseif($action=="addsave")
{
	$adenname = trim($adenname);
	if(empty($adname) || empty($adenname)){
		ShowMsg("广告位名称或者广告位标识没有填写","-1");
		exit();
	}
	if(!m_ereg("^[0-9a-zA-Z_\-]+$",$adenname)){
		ShowMsg("文件名不合法","-1");
		exit;
	}
	$timeset=time();
	$row = $dsql->GetOne("Select aid From sea_myad where adenname like '$adenname'");
	if(is_array($row))
	{
		ShowMsg("已经存在同名的标记！","-1");
		exit();
	}
	$query = "Insert Into sea_myad(adname,adenname,timeset,intro,adsbody) Values('$adname','$adenname','$timeset','$intro','$adsbody');";
	$dsql->ExecuteNoneQuery($query);
	createTextFile(stripslashes($adsbody),sea_ROOT."/js/".$cfg_ads_dir."/".$adenname.".js");
	ShowMsg("成功增加一个广告！","admin_ads.php");
	exit();
}
elseif($action=="del")
{
	$row = $dsql->GetOne("Select adenname From `sea_myad` where aid='$id'");
	$filename=$row['adenname'];
	@unlink("../js/".$cfg_ads_dir."/".$filename.".js");
	$dsql->ExecuteNoneQuery("Delete From `sea_myad` where aid='$id' ");
	ShowMsg("删除成功！","admin_ads.php");
	exit;
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的广告","-1");
		exit();
	}
	foreach($e_id as $id){
		$row = $dsql->GetOne("Select adenname From `sea_myad` where aid='$id'");
		$filename=$row['adenname'];
		@unlink("../js/".$cfg_ads_dir."/".$filename.".js");
		$dsql->ExecuteNoneQuery("Delete From `sea_myad` where aid='$id' ");
	}
	ShowMsg("删除成功！","admin_ads.php");
	exit;
}
elseif($action=="single")
{
	$row = $dsql->GetOne("Select adenname,adsbody From `sea_myad` where aid='$id'");
	$adsbody=$row['adsbody'];
	$adenname=$row['adenname'];
	createTextFile($adsbody,sea_ROOT."/js/".$cfg_ads_dir."/".$adenname.".js");
	header("Location:?admin_ads.php");
	die;
}
else
{
	include(sea_ADMIN.'/templets/admin_ads.htm');
	exit();
}

function isAdsFileExist($filename,$id)
{
	global $cfg_ads_dir;
	$adFile="../js/".$cfg_ads_dir."/".$filename.".js";
	if(file_exists($adFile)) echo "<a href='".$adFile."'><img src='img/yes.gif' border='0'/></a>"; else echo "<a href='?action=single&id=".$id."'><img src='img/no.gif' border='0'/></a>";
}
?>