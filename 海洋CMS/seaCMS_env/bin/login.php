<?php
session_start();
require_once("include/common.php");
require_once(sea_INC."/main.class.php");
if($cfg_user==0)
{
	ShowMsg('系统已关闭会员功能!','-1');
	exit();
}
$hashstr=md5($cfg_dbpwd.$cfg_dbname.$cfg_dbuser); //构造session安全码
$svali = $_SESSION['sea_ckstr'];
if($dopost=='login')
{
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
	if($userid=='')
	{
		ShowMsg('请输入用户名!','-1');
		exit();
	}
	if($pwd=='')
	{
		ShowMsg('请输入密码!','-1');
		exit();
	}

$userid = RemoveXSS(stripslashes($userid));
$userid = addslashes(cn_substr($userid,60));

$pwd = substr(md5($pwd),5,20);
$row1=$dsql->GetOne("select * from sea_member where state=1 and username='$userid'");
if($row1['username']==$userid AND $row1['password']==$pwd)
		{
					$_SESSION['sea_user_id'] = $row1['id'];
					$_SESSION['sea_user_name'] = $row1['username'];
					$_SESSION['sea_user_group'] = $row1['gid'];
					$_SESSION['hashstr']=$hashstr;
					$dsql->ExecuteNoneQuery("UPDATE `sea_member` set logincount=logincount+1");
					ShowMsg("成功登录，正在转向首页！","index.php",0,3000);
					exit();

		}
		else
		{
			ShowMsg("密码错误或账户已被禁用","login.php",0,3000);
			exit();
		}
}
else
{
	$tempfile = sea_ROOT."/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/login.html";
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
	{$t=str_replace("{login:viewLogin}",viewLogin(),$t);}
	else
	{$t=str_replace("{login:viewLogin}",viewLogin2(),$t);}
	$t=str_replace("{login:main}",viewMain(),$t);
	$t=str_replace("{seacms:runinfo}",getRunTime($t1),$t);
	$t=str_replace("{seacms:member}",front_member(),$t);
	echo $t;
	exit();
}

function viewMain(){
	$main="<div class='leaveNavInfo'><h3><span id='adminleaveword'></span>".$GLOBALS['cfg_webname']."会员登录</h3></div>";
	return $main;
}

function viewLogin(){
	$mystr="<div id=\"login\">".
"<form id=\"f_login\"   action=\"/".$GLOBALS['cfg_cmspath']."login.php\" method=\"post\">".
"<input type=\"hidden\" value=\"login\" name=\"dopost\" />".
"<table align=\"center\" style=\"margin:0 auto\">". 
"<tr>".
"<td height=\"25\" align=\"right\">用户名:</td><td><input type=\"input\" name=\"userid\" style=\"width:150px\" />".
"</td>".
"</tr>".
"<tr>".
"<td height=\"25\" align=\"right\">密码:</td><td><input type=\"password\" name=\"pwd\" style=\"width:150px\" /></td>".
  "</tr>".
	 "<tr>".
	 	"<td height=\"25\" align=\"right\">验证码:</td><td><input name=\"validate\" type=\"text\" style='width:50px;text-transform:uppercase;' class=\"text\" /> <img id=\"vdimgck\" src=\"./include/vdimgck.php\" alt=\"看不清？点击更换\" align=\"absmiddle\" style=\"cursor:pointer\" onClick=\"this.src=this.src+'?'\"/></td>".
  "</tr>".
  
  "<tr>".
	"<td height=\"30\"></td><td><input type=\"submit\" value=\"登录\" class=\"btn\"/>&nbsp;&nbsp;<a href=\"./reg.php\">没有账号，注册一个？</a></td> </tr>".
"</table></form>".
"</div>";
	return $mystr;
}

function viewLogin2(){
	$mystr="<div id=\"login\">".
"<form id=\"f_login\"   action=\"/".$GLOBALS['cfg_cmspath']."login.php\" method=\"post\">".
"<input type=\"hidden\" value=\"login\" name=\"dopost\" />".
"<table align=\"center\" style=\"margin:0 auto\">". 
"<tr>".
"<td height=\"25\" align=\"right\">用户名:</td><td><input type=\"input\" name=\"userid\" style=\"width:150px\" />".
"</td>".
"</tr>".
"<tr>".
"<td height=\"25\" align=\"right\">密码:</td><td><input type=\"password\" name=\"pwd\" style=\"width:150px\" /></td>".
  "</tr>".

  
  "<tr>".
	"<td height=\"30\"></td><td><input type=\"submit\" value=\"登录\" class=\"btn\"/>&nbsp;&nbsp;<a href=\"./reg.php\">没有账号，注册一个？</a></td> </tr>".
"</table></form>".
"</div>";
	return $mystr;
}