<?php
require_once(dirname(__FILE__)."/config.php");
$defaultIcoFile = sea_ROOT.'/data/admin/quickmenu.txt';
$myIcoFile = sea_ROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
if(!file_exists($myIcoFile)) {
	$myIcoFile = $defaultIcoFile;
}
if(empty($dopost)) {
	$dopost = '';
}
if($dopost=='edit'){
	$menu = stripslashes($menu);
	$myIcoFileTrue = sea_ROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
	$fp = fopen($myIcoFileTrue,'w');
	fwrite($fp,$menu);
	fclose($fp);
	ShowMsg("成功修改快捷操作项目！","admin_menu.php");
	exit();
}
else
{
	$fp = fopen($myIcoFile,'r');
	$oldct = trim(fread($fp,filesize($myIcoFile)+1));
	fclose($fp);
	include(sea_ADMIN.'/templets/admin_menu.htm');
	exit();
}
?>