<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_ADMIN.'/inc_menu.php');
$defaultIcoFile = sea_ROOT.'/data/admin/quickmenu.txt';
$myIcoFile = sea_ROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
if(!file_exists($myIcoFile)) {
	$myIcoFile = $defaultIcoFile;
}
include(sea_ADMIN.'/templets/index.htm');
exit();
?>