<?php
require_once(dirname(__FILE__)."/config.php");
if(empty($action))
{
	$action = '';
}

$dirTemplate="../templets";

if($action=='edit')
{
	if(substr(strtolower($filedir),0,11)!=$dirTemplate){
		ShowMsg("只允许编辑templets目录！","admin_template.php");
		exit;
	}
	$filetype=getfileextend($filedir);
	if ($filetype!="html" && $filetype!="htm" && $filetype!="js" && $filetype!="css" && $filetype!="txt")
	{
		ShowMsg("操作被禁止！","admin_template.php");
		exit;
	}
	$filename=substr($filedir,strrpos($filedir,'/')+1,strlen($filedir)-1);
	$content=loadFile($filedir);
	$content = m_eregi_replace("<textarea","##textarea",$content);
	$content = m_eregi_replace("</textarea","##/textarea",$content);
	$content = m_eregi_replace("<form","##form",$content);
	$content = m_eregi_replace("</form","##/form",$content);
	include(sea_ADMIN.'/templets/admin_template.htm');
	exit();
}
elseif($action=='editCus')
{
	if(substr(strtolower($filedir),0,11)!=$dirTemplate){
		ShowMsg("只允许编辑templets目录！","admin_template.php");
		exit;
	}
	$filetype=getfileextend($filedir);
	if ($filetype!="html" && $filetype!="htm" && $filetype!="js" && $filetype!="css" && $filetype!="txt")
	{
		ShowMsg("操作被禁止！","admin_template.php");
		exit;
	}
	$filename=substr($filedir,strrpos($filedir,'/')+1,strlen($filedir)-1);
	$content=loadFile($filedir);
	$content = m_eregi_replace("<textarea","##textarea",$content);
	$content = m_eregi_replace("</textarea","##/textarea",$content);
	$content = m_eregi_replace("<form","##form",$content);
	$content = m_eregi_replace("</form","##/form",$content);
	include(sea_ADMIN.'/templets/admin_template.htm');
	exit();
}
elseif($action=='saveCus')
{
	if($filedir == '')
	{
		ShowMsg('未指定要编辑的文件或文件名不合法', '-1');
		exit();
	}
	if(substr(strtolower($filedir),0,11)!=$dirTemplate){
		ShowMsg("只允许编辑templets目录！","admin_template.php");
		exit;
	}
	$filetype=getfileextend($filedir);
	if ($filetype!="html" && $filetype!="htm" && $filetype!="js" && $filetype!="css" && $filetype!="txt")
	{
		ShowMsg("操作被禁止！","admin_template.php");
		exit;
	}
	$folder=substr($filedir,0,strrpos($filedir,'/'));
	if(!is_dir($folder)){
		ShowMsg("目录不存在！","admin_template.php");
		exit;
	}
	$content = stripslashes($content);
	$content = m_eregi_replace("##textarea","<textarea",$content);
	$content = m_eregi_replace("##/textarea","</textarea",$content);
	$content = m_eregi_replace("##form","<form",$content);
	$content = m_eregi_replace("##/form","</form",$content);
	createTextFile($content,$filedir);
	ShowMsg("操作成功！","admin_template.php?action=custom");
	exit;
}
elseif($action=='save')
{
	if($filedir == '')
	{
		ShowMsg('未指定要编辑的文件或文件名不合法', '-1');
		exit();
	}
	if(substr(strtolower($filedir),0,11)!=$dirTemplate){
		ShowMsg("只允许编辑templets目录！","admin_template.php");
		exit;
	}
	$filetype=getfileextend($filedir);
	if ($filetype!="html" && $filetype!="htm" && $filetype!="js" && $filetype!="css" && $filetype!="txt")
	{
		ShowMsg("操作被禁止！","admin_template.php");
		exit;
	}
	$folder=substr($filedir,0,strrpos($filedir,'/'));
	if(!is_dir($folder)){
		ShowMsg("目录不存在！","admin_template.php");
		exit;
	}
	$content = stripslashes($content);
	$content = m_eregi_replace("##textarea","<textarea",$content);
	$content = m_eregi_replace("##/textarea","</textarea",$content);
	$content = m_eregi_replace("##form","<form",$content);
	$content = m_eregi_replace("##/form","</form",$content);
	createTextFile($content,$filedir);
	ShowMsg("操作成功！","admin_template.php?path=".$folder);
	exit;
}
elseif($action=='del')
{
	if($filedir == '')
	{
		ShowMsg('未指定要删除的文件或文件名不合法', '-1');
		exit();
	}
	if(substr(strtolower($filedir),0,11)!=$dirTemplate){
		ShowMsg("只允许删除templets目录内的文件！","admin_template.php");
		exit;
	}
	$folder=substr($filedir,0,strrpos($filedir,'/'));
	if(!is_dir($folder)){
		ShowMsg("目录不存在！","admin_template.php");
		exit;
	}
	unlink($filedir);
	ShowMsg("操作成功！","admin_template.php?path=".$folder);
	exit;
}
elseif($action=='add')
{
	include(sea_ADMIN.'/templets/admin_template.htm');
	exit();
}
elseif($action=='custom')
{
	include(sea_ADMIN.'/templets/admin_template.htm');
	exit();
}
elseif($action=='savenew')
{
	if(empty($name)){
		ShowMsg("请填写文件名","-1");
		exit;
	}
	if(!m_ereg("^[0-9a-z-]+$",$name)){
		ShowMsg("文件名不合法","-1");
		exit;
	}
	$defaultfolder="../templets/".$cfg_df_style."/".$cfg_df_html;
	if(empty($filedir)) $filedir=$defaultfolder;
	if($filedir!=$defaultfolder){
		ShowMsg("只能把模板添加在{$defaultfolder}文件夹","admin_template.php?path=".$filedir);
		exit;
	}
	if(file_exists($filedir."/self_".$name.".html")){
		ShowMsg("已存在该文件请更换名称","-1");
		exit;
	}
	createTextFile($content,$filedir."/self_".$name.".html");
	ShowMsg("操作成功！","admin_template.php?action=custom");
	exit;
}
else
{
	if(empty($path)) $path=$dirTemplate; else $path=strtolower($path);
	if(substr($path,0,11)!=$dirTemplate){
		ShowMsg("只允许编辑templets目录！","admin_template.php");
		exit;
	}
	$flist=getFolderList($path);
	include(sea_ADMIN.'/templets/admin_template.htm');
	exit();
}
?>