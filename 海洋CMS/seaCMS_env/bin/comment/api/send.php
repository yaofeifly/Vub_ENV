<?php
session_start();
require_once("../../include/common.php");
require_once(sea_INC.'/main.class.php');
require_once(sea_INC."/filter.inc.php");
if(!isset($action))
{
	$action = '';
}

$ischeck = $cfg_feedbackcheck=='Y' ? 0 : 1;
$id = (isset($gid) && is_numeric($gid)) ? $gid : 0;
$itype = (isset($ctype) && is_numeric($ctype)) ? $ctype : 0;
$cparent = (isset($cparent) && is_numeric($cparent)) ? $cparent : 0;

if(empty($id))
{
	echo "err";
	exit();
}
if($action=='send')
{
	$validate = $captcha;
	if($cfg_feedback_ck=='1')
	{
		$svali = strtolower(trim(GetCkVdValue()));
		if(strtolower($validate) != $svali || $svali=='')
		{
			ResetVdValue();
			if($validate!=$svali)
			{
				echo "<script>alert('验证码错误！');</script>";
				exit();
			}
		}
	}
	$tmpname = empty($tmpname) ? '' : $tmpname;
	$ip = GetIP();
	$dtime = time();
	$msg = $talkwhat;
	$itype = $ctype;
	//检查评论间隔时间；
	if(!empty($cfg_comment_times))
	{
		$row = $dsql->GetOne("SELECT dtime FROM `sea_comment` WHERE `ip` = '$ip' ORDER BY `id` desc ");
		if($dtime - $row['dtime'] < $cfg_comment_times)
		{
			echo "<script>alert('评论太快，请休息一下再来评论！');</script>";
			exit();
		}
	}
		//检查留言IP
	/*if(!empty($cfg_banIPS))
	{
		$myarr = explode ('|',$cfg_banIPS);
		for($i=0;$i<count($myarr);$i++)
		{
			if($ip==$myarr[$i])
			{
			echo "<script>alert('您所在的IP不能评论！');</script>";
			exit();
			}
		}
		
	}*/
	$msg = cn_substrR(TrimMsg(unescape($msg)),1000);
	$tmpname = cn_substrR(HtmlReplace(unescape($tmpname),2),20);
	$tmpname = _Replace_Badword($tmpname);
	//检查禁止词语
	if(!empty($cfg_banwords))
	{
		$myarr = explode ('|',$cfg_banwords);
		for($i=0;$i<count($myarr);$i++)
		{
			$userisok = strpos($username, $myarr[$i]);
			$msgisok = strpos($msg, $myarr[$i]);
			if(is_int($userisok)||is_int($msgisok))
			{
			echo "<script>alert('您发表的评论中有禁用词语！');</script>";
			exit();
			}
		}
		
	}
	//保存评论内容

	$uid =$_SESSION['sea_user_id'];
	$uid = RemoveXSS(stripslashes($uid));
	$uid = addslashes(cn_substr($uid,20));
	$tmpname=$_SESSION['sea_user_name'];
	$tmpname = RemoveXSS(stripslashes($tmpname));
	$tmpname = addslashes(cn_substr($tmpname,20));
	if($msg!='')
	{
		$msg = _Replace_Badword($msg);
		$inquery = "INSERT INTO `sea_comment`(`v_id`,`uid`,`username`,`ip`,`ischeck`,`reply`,`agree`,`anti`,`dtime`,`msg`,`m_type`) VALUES ('$id','$uid','$tmpname','$ip',$ischeck,$cparent,0,0,'$dtime','$msg','$itype'); ";
		$rs = $dsql->ExecuteNoneQuery($inquery);
		if(!$rs)
		{
			echo $dsql->GetError();
			exit();
		}
	}
	delfile("../../data/cache/review/$itype/$id.js");
	echo "<script>parent.success();</script>";
	exit();
}elseif($action=='2')
{
	$addagree = "update `sea_comment` set agree=agree+1 where id=".$id;
	$dsql->ExecuteNoneQuery($addagree);
	$rs = $dsql->GetOne("select v_id,typeid from sea_comment where id=".$id);
	delfile("../../data/cache/review/".$rs['typeid']."/".$rs['v_id'].".js");
}
elseif($action=='3')
{
	$addagree = "update `sea_comment` set anti=anti+1 where id=".$id;
	$dsql->ExecuteNoneQuery($addagree);
	$rs = $dsql->GetOne("select v_id,typeid from sea_comment where id=".$id);
	delfile("../../data/cache/review/".$rs['typeid']."/".$rs['v_id'].".js");
}
