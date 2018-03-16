<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>资源站一键采集接口</title>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/main.js" type="text/javascript"></script>
<script src="js/drag.js" type="text/javascript"></script>
<link type="text/css" href="img/alerts.css" rel="stylesheet" media="screen">
<script language="javascript">

</script>
<link  href="img/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<!--当前导航-->
<script type="text/JavaScript">if(parent.$('admincpnav')) parent.$('admincpnav').innerHTML='后台首页&nbsp;&raquo;&nbsp;采集&nbsp;&raquo;&nbsp;第三方资源库 ';</script>
<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
CheckPurview();
if(RWCache('collect_xml'))
echo "<script>openCollectWin(400,'auto','上次采集未完成，继续采集？','".RWCache('collect_xml')."')</script>";
?>
<div class="S_info">&nbsp;海洋cms不提供资源采集 以下采集资源来自第三方资源库接口</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td><span id="xmllist"><script language="JavaScript" type="text/javascript" charset="utf-8" src="http://www.seacms.net/api/union.js"></script></span></td>
</tr>
</table>
<!----第三方资源站接入开始---->
<!----第三方资源站接入开始---->
<!----第三方资源站接入开始---->







<!----第三方资源站接入结束---->
<!----第三方资源站接入结束---->
<!----第三方资源站接入结束---->
<?php
exit();	
?>
</body>
</html>
