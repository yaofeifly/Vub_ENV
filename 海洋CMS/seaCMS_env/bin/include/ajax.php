<?php
require_once('common.php');

AjaxHead();
$action = isset($action) ? trim($action) : '';
$id = (isset($id) && is_numeric($id)) ? $id : 0;
$id = RemoveXSS(stripslashes($id));
$id = addslashes(cn_substr($id,20));
if($action=="" or empty($action))
{
	exit();
}
switch ($action) {
	case "digg":
	case "tread":
	case "score":
		echo scoreVideo($action);
	break;
	case "diggnews":
	case "treadnews":
	case "scorenews":
		echo scoreNews($action);
	break;
	case "hit":
		echo updateHit();
	break;
	case "hitnews":
		echo updateHitNews();
	break;
	case "addfav":
		echo addfav();
	break;
	case "videoscore":
	case "newsscore":
		echo getScore($action);
	break;
	case "vpingfen":
		echo vpingfen($action);
	break;
	case "npingfen":
		echo npingfen($action);
	break;
	case "member":
		echo member();
	break;
}

function getScore($operType){
	global $id,$dsql;
	if($operType=="videoscore")
	{
		$sql="select v_digg,v_tread,v_score,v_scorenum from sea_data where v_id=".$id;
		$row=$dsql->GetOne($sql);
		if(is_array($row))
		{
			return "[".$row['v_digg'].",".$row['v_tread'].",".$row['v_score'].",".$row['v_scorenum']."]";
		}else{
			return 0;
		}
	}elseif($operType=="newsscore")
	{
		$sql="select n_digg,n_tread,n_score,n_scorenum from sea_news where n_id=".$id;
		$row=$dsql->GetOne($sql);
		if(is_array($row))
		{
			return "[".$row['n_digg'].",".$row['n_tread'].",".$row['n_score'].",".$row['n_scorenum']."]";
		}else{
			return 0;
		}
	}else{
		return "err";
	}
}

function scoreVideo($operType){
	global $id,$dsql,$score;
	$score = RemoveXSS(stripslashes($score));
	$score = addslashes(cn_substr($score,20));
	
	if($id < 1) return "err";
	if ($operType=="digg") {
		if(GetCookie("ssea2_score".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_digg = v_digg + 1 where v_id=$id");
		$row = $dsql->GetOne("Select v_digg From `sea_data` where v_id=$id ");
		if(is_array($row))
		{
			$digg=$row['v_digg'];
		}else{
			$digg=0;
		}
		PutCookie("ssea2_score".$id,"ok",3600 * 24,'/');
		return $digg;
	}elseif($operType=="tread"){
		if(GetCookie("ssea2_score".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_tread = v_tread + 1 where v_id=$id");
		$row = $dsql->GetOne("Select v_tread From `sea_data` where v_id=$id ");
		if(is_array($row))
		{
			$tread=$row['v_tread'];
		}else{
			$tread=0;
		}
		PutCookie("ssea2_score".$id,"ok",3600 * 24,'/');
		return $tread;
	}elseif($operType=="score"){
		if(GetCookie("ssea3_score".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_scorenum=v_scorenum+1,v_score=v_score+".$score." where v_id=$id");
		PutCookie("ssea3_score".$id,"ok",3600 * 24,'/');
		return '';
	}else{
		return "err";
	}
}

function scoreNews($operType){
	global $id,$dsql,$score;
	$score = RemoveXSS(stripslashes($score));
	$score = addslashes(cn_substr($score,20));
	if($id < 1) return "err";
	if ($operType=="diggnews") {
		if(GetCookie("ssea2_newsscore".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_news` set n_digg = n_digg + 1 where n_id=$id");
		$row = $dsql->GetOne("Select n_digg From `sea_news` where n_id=$id ");
		if(is_array($row))
		{
			$digg=$row['n_digg'];
		}else{
			$digg=0;
		}
		PutCookie("ssea2_newsscore".$id,"ok",3600 * 24,'/');
		return $digg;
	}elseif($operType=="treadnews"){
		if(GetCookie("ssea2_newsscore".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_news` set n_tread = n_tread + 1 where n_id=$id");
		$row = $dsql->GetOne("Select n_tread From `sea_news` where n_id=$id ");
		if(is_array($row))
		{
		$tread=$row['n_tread'];
		}else{
			$tread=0;
		}
		PutCookie("ssea2_newsscore".$id,"ok",3600 * 24,'/');
		return $tread;
	}elseif($operType=="scorenews"){
		if(GetCookie("ssea3_newsscore".$id)=="ok") return "havescore";
		$dsql->ExecuteNoneQuery("Update `sea_news` set n_scorenum=n_scorenum+1,n_score=n_score+".$score." where n_id=$id");
		PutCookie("ssea3_newsscore".$id,"ok",3600 * 24,'/');
		return '';
	}else{
		return "err";
	}
}

function updateHit(){
	global $id,$dsql;
	if($id < 1) return "err";
	$dsql->ExecuteNoneQuery("Update `sea_data` set v_hit = v_hit + 1 where v_id=$id");
	$row = $dsql->GetOne("Select v_hit,v_daytime,v_weektime,v_monthtime From `sea_data` where v_id=$id ");
	$n=time(); //当前时间
	$day=$row['v_daytime']; 
	$week=$row['v_weektime']; 
	$month=$row['v_monthtime']; 

	if(($n-$day)<86400)
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_dayhit = v_dayhit + 1 where v_id=$id");
	}
	else
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_dayhit = 1 where v_id=$id");
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_daytime = '$n' where v_id=$id");
	}

	if(($n-$week)<604800)
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_weekhit = v_weekhit + 1 where v_id=$id");
	}
	else
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_weekhit = 1 where v_id=$id");
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_weektime = '$n' where v_id=$id");
	}
	
	if(($n-$month)<2592000)
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_monthhit = v_monthhit + 1 where v_id=$id");
	}
	else
	{
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_monthhit = 1 where v_id=$id");
		$dsql->ExecuteNoneQuery("Update `sea_data` set v_monthtime = '$n' where v_id=$id");
	}
	
	
	if(is_array($row))
	{
		$hit=$row['v_hit'];
	}else{
		return "err";
	}
	return $hit;
}

function updateHitNews(){
	global $id,$dsql;
	if($id < 1) return "err";
	$dsql->ExecuteNoneQuery("Update `sea_news` set n_hit = n_hit + 1 where n_id=$id");
	$row = $dsql->GetOne("Select n_hit From `sea_news` where n_id=$id ");
	if(is_array($row))
	{
		$hit=$row['n_hit'];
	}else{
		return "err";
	}
	return $hit;
}

function addfav(){
	@session_start();
	global $id,$dsql,$cfg_user;
	$uid=$_SESSION['sea_user_id'];
	$uid = RemoveXSS(stripslashes($uid));
	$uid = addslashes(cn_substr($uid,200));
	$uid = intval($uid);
	if($uid < 1) return "err";
	$row = $dsql->GetOne("Select id From `sea_favorite` where vid=$id and uid=$uid ");
	if(!is_array($row))
	{
		$dsql->ExecuteNoneQuery("insert into `sea_favorite` values('','$uid','$id','".time()."')");
	}
	return "ok";
}

function vpingfen(){
	global $id,$dsql;
	$row = $dsql->GetOne("Select v_score,v_scorenum From `sea_data` where v_id=$id ");
	$num=$row['v_scorenum']; 
	$sum=$row['v_score']; 
	$sc=number_format($sum/$num,1);
	return "$num,$sum,$sc";
}

function npingfen(){
	global $id,$dsql;
	$row = $dsql->GetOne("Select n_score,n_scorenum From `sea_news` where n_id=$id ");
	$num=$row['n_scorenum']; 
	$sum=$row['n_score']; 
	$sc=number_format($sum/$num,1);
	return "$num,$sum,$sc";
}
function member()
{
	@session_start();
	global $cfg_user;
	if($cfg_user==0) return '';
	global $cfg_phpurl;
	if(!empty($_SESSION['sea_user_id'])) {
		
		$member = "您好<font color='red'>".$_SESSION['sea_user_name']." </font>[<a href='".$cfg_phpurl."exit.php'>退出</a>] [<a href='".$cfg_phpurl."member.php' target='_blank'>个人空间</a>]";
	} else {
		$member = "<a href='".$cfg_phpurl."login.php'>登录</a> <a href='".$cfg_phpurl."reg.php'>注册</a>";
	}
	return $member;
}
?>