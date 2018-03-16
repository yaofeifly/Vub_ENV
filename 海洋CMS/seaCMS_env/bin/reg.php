<?php
session_start();
require_once("include/common.php");
require_once(sea_INC.'/main.class.php');
if($cfg_user==0)
{
	ShowMsg('系统已关闭会员功能!','-1');
	exit();
}

$svali = $_SESSION['sea_ckstr'];
$action = isset($action) ? trim($action) : '';
if($action=='reg')
{
$dtime = time();

if($cfg_feedback_ck=='1')
{
$validate = empty($validate) ? '' : strtolower(trim($validate));
if($validate=='' || $validate != $svali)
	{
		ResetVdValue();
		ShowMsg('验证码不正确!','-1');
		exit();
	}
}

if(trim($m_pwd)<>trim($m_pwd2) || trim($m_pwd)=='')
	{
		ShowMsg('两次输入密码不一致或密码为空','-1');	
		exit();	
	}	
	
$username = $m_user;
$username = RemoveXSS(stripslashes($username));
$username = addslashes(cn_substr($username,200));
$row1=$dsql->GetOne("select username  from sea_member where username='$username'");
if($row1['username']==$username)
{
		ShowMsg('用户已存在','-1');	
		exit();	
}

	$pwd = substr(md5($m_pwd),5,20);
	$ip = GetIP();  
	$email = RemoveXSS(stripslashes($email));
	$email = addslashes(cn_substr($email,200));
	if($username) {
		$dsql->ExecuteNoneQuery("INSERT INTO `sea_member`(id,username,password,email,regtime,regip,state,gid,points,logincount)
                  VALUES ('','$username','$pwd','$email','$dtime','$ip','1','2','0','1')");

		ShowMsg('恭喜您，注册成功！','login.php',0,3000);
		exit;
	}
}
else
{
	$tempfile = sea_ROOT."/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/reg.html";
	$content=loadFile($tempfile);
	$t=$content;
	$t=$mainClassObj->parseTopAndFoot($t);
	$t=$mainClassObj->parseHistory($t);
	$t=$mainClassObj->parseSelf($t);
	$t=$mainClassObj->parseGlobal($t);
	$t=$mainClassObj->parseAreaList($t);
	$t=$mainClassObj->parseNewsAreaList($t);
	$t=$mainClassObj->parseMenuList($t,"");
	$t=$mainClassObj->parseVideoList($t,-444);
	$t=$mainClassObj->parseNewsList($t,-444);
	$t=$mainClassObj->parseTopicList($t);
	$t=replaceCurrentTypeId($t,-444);
	$t=$mainClassObj->parseIf($t);
	if($cfg_feedback_ck=='1')
	{$t=str_replace("{register:viewRegister}",viewRegister(),$t);}
	else
	{$t=str_replace("{register:viewRegister}",viewRegister2(),$t);}
	
	$t=str_replace("{register:main}",viewMain(),$t);

	$t=str_replace("{seacms:runinfo}",getRunTime($t1),$t);
	$t=str_replace("{seacms:member}",front_member(),$t);
	echo $t;

} 

function viewMain(){
	$main="<div class='leaveNavInfo'><h3><span id='adminleaveword'></span>".$GLOBALS['cfg_webname']."会员注册</h3></div>";
	return $main;
}



function viewActivation($activeuser,$activepwd){
	$mystr="<div id=\"register\">".
"<form id=\"f_register\" action=\"/".$GLOBALS['cfg_cmspath']."reg.php?action=activationsubmit\" method=\"post\">".
"<input type=\"hidden\" name=\"activeuser\" value=\"$activeuser\">".
"<input type=\"hidden\" name=\"activepwd\" value=\"$activepwd\">".
"<table align=\"center\" style=\"margin:0 auto\">". 
"<tr>".
"<td>用户名$activeuser</td> </tr>".
"<tr>".
"<td><input type=\"submit\" value=\"激活\" class=\"btn\"/></td> </tr>".
"</table></form>".
"</div>";
	return $mystr;
}

function viewRegister()
{
	$mystr="<div id=\"register\">".
"<form id=\"f_Activation\"   action=\"/".$GLOBALS['cfg_cmspath']."reg.php?action=reg\" method=\"post\">".
"<table align=\"center\" style=\"margin:0 auto\">". 
"<tr>".
"<td height=\"25\" align=\"right\">用户名:</td><td><input type=\"input\" name=\"m_user\" id=\"m_user\" style=\"width:150px\" />".
"<span class=\"red\">*用户名由6-14位字母数字和'_'组成</span></td>".
"</tr>".
"<tr>".
"<td height=\"25\" align=\"right\">密码:</td><td><input type=\"password\" name=\"m_pwd\" style=\"width:150px\"/><span class=\"red\">*密码由6-14位字母数字和'_'组成</span></td>".
  "</tr>".
	 "<tr>".
	 	"<td height=\"25\" align=\"right\">确认密码:</td><td><input type=\"password\" name=\"m_pwd2\" style=\"width:150px\"/><span class=\"red\">*再次输入密码确认</span></td>".
  "</tr>".
	 "<tr>".
	 	"<td height=\"25\" align=\"right\">邮箱:</td><td><input type=\"text\" name=\"email\" style=\"width:150px\"/><span class=\"red\">*邮箱地址</span></td>".
  "</tr>".
  "<tr>".
	 	"<td height=\"25\" align=\"right\">验证码:</td><td><input type=\"text\" name=\"validate\" id=\"vdcode\" style=\"width:150px;text-transform:uppercase;\"/><img id=\"vdimgck\" src=\"include\/vdimgck.php\" alt=\"看不清？点击更换\" align=\"absmiddle\" style=\"cursor:pointer\" onClick=\this.src=this.src+'?get=' + new Date()\"/><span class=\"red\">*验证码</span></td>".
  "</tr>". 
  "<tr>".
	"<td height=\"30\"></td><td><input type=\"submit\"  value=\"注册\" class=\"btn\"/>&nbsp;&nbsp;<a href=\"./login.php\">已有账号，直接登录？</a></td> </tr>".
"</table></form>".
"</div>";
	return $mystr;
}

function viewRegister2()
{
	$mystr="<div id=\"register\">".
"<form id=\"f_Activation\"   action=\"/".$GLOBALS['cfg_cmspath']."reg.php?action=reg\" method=\"post\">".
"<table align=\"center\" style=\"margin:0 auto\">". 
"<tr>".
"<td height=\"25\" align=\"right\">用户名:</td><td><input type=\"input\" name=\"m_user\" id=\"m_user\" style=\"width:150px\" />".
"<span class=\"red\">*用户名由6-14位字母数字和'_'组成</span></td>".
"</tr>".
"<tr>".
"<td height=\"25\" align=\"right\">密码:</td><td><input type=\"password\" name=\"m_pwd\" style=\"width:150px\"/><span class=\"red\">*密码由6-14位字母数字和'_'组成</span></td>".
  "</tr>".
	 "<tr>".
	 	"<td height=\"25\" align=\"right\">确认密码:</td><td><input type=\"password\" name=\"m_pwd2\" style=\"width:150px\"/><span class=\"red\">*再次输入密码确认</span></td>".
  "</tr>".
	 "<tr>".
	 	"<td height=\"25\" align=\"right\">邮箱:</td><td><input type=\"text\" name=\"email\" style=\"width:150px\"/><span class=\"red\">*邮箱地址</span></td>".
  "</tr>".

  "<tr>".
	"<td height=\"30\"></td><td><input type=\"submit\"  value=\"注册\" class=\"btn\"/>&nbsp;&nbsp;<a href=\"./login.php\">已有账号，直接登录？</a></td> </tr>".
"</table></form>".
"</div>";
	return $mystr;
}
