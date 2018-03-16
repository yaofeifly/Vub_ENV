<?php
require_once(dirname(__FILE__).'/../include/common.php');
require_once(sea_INC.'/check.admin.php');
$cuserLogin = new userLogin();
$cuserLogin->exitUser();
session_unset();
session_destroy();
if(empty($needclose))
{
	header('location:index.php');
}
else
{
	$msg = "<script language='javascript'>
	if(document.all) window.opener=true;
	window.close();
	</script>";
	echo $msg;
}
?>