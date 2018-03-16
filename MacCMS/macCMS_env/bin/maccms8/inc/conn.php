<?php
define('MAC_ROOT', substr(__FILE__, 0, -13));
require(MAC_ROOT.'/inc/config/config.php');
require(MAC_ROOT.'/inc/config/cache.php');
require(MAC_ROOT.'/inc/common/class.php');
require(MAC_ROOT.'/inc/common/function.php');
require(MAC_ROOT.'/inc/common/template.php');
require(MAC_ROOT."/inc/common/template_diy.php");
define('MAC_PATH', $MAC['site']['installdir']);
define('MAC_ROOT_TEMPLATE', MAC_ROOT.'/template/'.$MAC['site']['templatedir'].'/'.$GLOBALS['MAC']['site']['htmldir']);
define('MAC_STARTTIME',execTime());
define('MAC_URL','http://www.maccms.com/');
define('MAC_NAME','Æ»¹ûCMS');
@session_start();
@header('Content-Type:text/html;Charset=utf-8');
@date_default_timezone_set('Etc/GMT-8');
@ini_set('display_errors','On');
@error_reporting(7);
@set_error_handler('my_error_handler');
@ob_start();
?>