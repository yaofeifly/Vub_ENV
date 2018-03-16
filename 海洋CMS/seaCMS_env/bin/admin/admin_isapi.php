<?php
header('Content-Type:text/html;charset=utf-8');
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if($action=="set")
{
	$v2=$_POST['v'];
	$open=fopen("../data/admin/isapi.txt","w" );
	fwrite($open,$v2);
	fclose($open);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>资源API开关</title>
<link  href="img/admin.css" rel="stylesheet" type="text/css" />
<link  href="img/style.css" rel="stylesheet" type="text/css" />
<script src="js/common.js" type="text/javascript"></script>
<script src="js/main.js" type="text/javascript"></script>
</head>
<body>
<script type="text/JavaScript">if(parent.$('admincpnav')) parent.$('admincpnav').innerHTML='后台首页&nbsp;&raquo;&nbsp;资源发布&nbsp;&raquo;&nbsp;资源API开关';</script>
<div class="r_main">
  <div class="r_content">
    <div class="r_content_1">
<form action="admin_isapi.php?action=set" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tb_style">
<tbody><tr class="thead">
<td colspan="5">资源API插件</td>
</tr>
<tr>
<td width="80%" align="left" height="30" class="td_btop3">
<?php $v1=file_get_contents("../data/admin/isapi.txt"); ?>
<input type="radio" name="v" value="0" <?php if($v1==0) echo 'checked';?>>关闭
&nbsp;&nbsp;
<input type="radio" name="v" value="1" <?php if($v1==1) echo 'checked';?>>开启
</td>
</tr>

<tr>
<td width="10%" align="left" height="30" class="td_btop3">
<input type="submit" value="确认" class="btn" >
</td>
</tr>
<tr>
<td width="90%" align="left" height="30" class="td_btop3">
* 如果修改无效，请检查/data/admin/isapi.txt文件权限是否可写。资源库API访问地址是：http://您的域名/zyapi.php。
</td>
</tr>
</tbody></table>
</form>
</div>

		</div>
	</div>
</div>

<?php
viewFoot();
?>
</body>
</html>