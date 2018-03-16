<?php
header('Content-Type:text/html;charset=utf-8');
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if($action=="set")
{
	$weburl= $_POST['weburl'];
	$token = $_POST['token'];
	$open=fopen("../data/admin/ping.php","w" );
	$str='<?php ';
	$str.='$weburl = "';
	$str.="$weburl";
	$str.='"; ';
	$str.='$token = "';
	$str.="$token";
	$str.='"; ';
	$str.=" ?>";
	fwrite($open,$str);
	fclose($open);
}
$weburl = 't.seacms.net';
$token='kvs09mzW6lzfhH43';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理登陆验证码</title>
<link  href="img/admin.css" rel="stylesheet" type="text/css" />
<link  href="img/style.css" rel="stylesheet" type="text/css" />
<script src="js/common.js" type="text/javascript"></script>
<script src="js/main.js" type="text/javascript"></script>
</head>
<body>
<script type="text/JavaScript">if(parent.$('admincpnav')) parent.$('admincpnav').innerHTML='后台首页&nbsp;&raquo;&nbsp;管理员&nbsp;&raquo;&nbsp;管理登陆验证码 ';</script>
<div class="r_main">
  <div class="r_content">
    <div class="r_content_1">
<form action="admin_ping.php?action=set" method="post">	
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_style">
<tbody><tr class="thead">
<td colspan="5">百度主动推送设置</td>
</tr>
<tr>
<td width="80%" align="left" height="30" class="td_btop3">
<?php require_once("../data/admin/ping.php"); ?>
登记域名：<input  name="weburl" value="<?php echo $weburl;?>">
 * 百度站长平台里登记的域名，必须保持完全一致，如www.seacms.net、demo.seacms.net 
</td>
</tr>

<tr>
<td width="80%" align="left" height="30" class="td_btop3">
准入密钥：<input name="token" value="<?php echo $token;?>">
 * 百度站长平台里提供的准入秘钥，在百度站长平台-链接提交-修改准入密钥处查看
</td>
</tr>

<tr>
<td width="10%" align="left" height="30" class="td_btop3">
<input type="submit" value="确认" class="btn" >
</td>
</tr>
<tr>
<td width="90%" align="left" height="30" class="td_btop3">
* 请先到百度站长平台http://zhanzhang.baidu.com注册，然后严格按照要求填写域名和密钥。
</td>
</tr>
<tr>
<td width="90%" align="left" height="30" class="td_btop3">
* 如果修改无效，请检查/data/admin/ping.php文件权限是否可写。
</td>
</tr>
</tbody></table>	
	

</form>
</div>
	</div>
</div>
<?php
viewFoot();
?>
</body>
</html>