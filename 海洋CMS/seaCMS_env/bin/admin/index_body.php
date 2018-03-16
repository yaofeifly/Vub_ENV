<?php
require_once(dirname(__FILE__)."/config.php");
require(sea_INC.'/image.func.php');
	require(sea_INC.'/inc/inc_fun_funAdmin.php');
	$verLockFile = sea_ROOT.'/data/admin/ver.txt';
	$fp = fopen($verLockFile,'r');
	$upTime = trim(fread($fp,64));
	fclose($fp);
	//$oktime = substr($upTime,0,4).'-'.substr($upTime,4,2).'-'.substr($upTime,6,2);
	$oktimear = array(0,0,0,0,0);
	$oktimear = explode('.',$upTime);
	$oktime = $oktimear[2].'-'.$oktimear[3].'-'.$oktimear[4];
	$offUrl = SpGetNewInfo();
	include(sea_ADMIN.'/templets/index_body.htm');
	exit();
?>