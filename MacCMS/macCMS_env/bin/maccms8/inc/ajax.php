<?php
require("conn.php");
require(MAC_ROOT.'/inc/common/360_safe3.php');

$ac = be("get", "ac");
$ac2=be("get","ac2");
$tab=be("get","tab");
$id = intval(be("get", "id"));

$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
if($ac=='desktop')
{
	$url = be("get", "url");
	$name = strip_tags(be("get", "name"));
	if (empty($name)){ $name = $MAC['app']['name']; $url = "http://".$MAC['app']['url']; }
	if (strpos($url,"ttp://")<=0){ $url = "http://".$MAC['app']['url'].$url; }
	$Shortcut = "[InternetShortcut]
	URL=".$url."
	IDList=
	IconIndex=1
	[{000214A0-0000-0000-C000-000000000046}]
	Prop3=19,2";
	Header("Content-type: application/octet-stream");
	if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")){
 		Header("Content-Disposition: attachment; filename=". urlencode($name) .".url;");
 	}
 	else{
 		Header("Content-Disposition: attachment; filename=". $name .".url;");
 	}
	echo $Shortcut;
}

elseif($ac=='hits')
{
	if($id<1){ echo "err"; exit;}
	$res = 0;
	$sday = date('Y-m-d',time());
	$smonth = date('Y-m',time());
	switch($tab)
	{
		case 'art':
			$pre='a';
			break;
		case 'vod':
			$pre='d';
			break;
		case 'art_topic':
		case 'vod_topic':
			$pre='t';
			break;
	}
	$col_id=$pre.'_id';
	$col_hits=$pre.'_hits';
	$col_dayhits=$pre.'_dayhits';
	$col_weekhits=$pre.'_weekhits';
	$col_monthhits=$pre.'_monthhits';
	$col_hitstime=$pre.'_hitstime';
	
	$sql='SELECT '.$col_hits.','.$col_dayhits.','.$col_weekhits.','.$col_monthhits.','.$col_hitstime.' FROM {pre}'.$tab.' WHERE '.$col_id.'='.$id;
	
	$row = $db->getRow($sql);
	if($row){
		$hits=$row[$col_hits];
		$dayhits=$row[$col_dayhits];
		$weekhits=$row[$col_weekhits];
		$monthhits=$row[$col_monthhits];
		$path=MAC_ROOT."/inc/config/hitstime_".$tab.".txt";
		if(file_exists($path)){
			$hitstime = @file_get_contents($path);
		}
		if(!isN($hitstime)){
			if ( date('m',time()) != date('m',$hitstime) ){
				$db->Update ("{pre}".$tab,array($col_monthhits),array(0),$col_id.">0");
				$monthhits=0; 
			}
			if ( date('W',time()) != date('W',$hitstime) ){
				$db->Update ("{pre}".$tab,array($col_weekhits),array(0),$col_id.">0");
				$weekhits=0;
			}
			if ( date('d',time()) != date('d',$hitstime) ){
				$db->Update ("{pre}".$tab,array($col_dayhits),array(0),$col_id.">0");
				$dayhit=0;
			}
		}
		$res = $hits + 1;
		$res1 = $dayhits + 1;
		$res2 = $weekhits + 1;
		$res3 = $monthhits + 1;
		$db->Update ('{pre}'.$tab,array($col_hitstime,$col_hits,$col_dayhits,$col_weekhits,$col_monthhits),array(time(),$res,$res1,$res2,$res3),$col_id.'='.$id);
		@fwrite(fopen($path,"wb"),time());
	}
	unset($row);
	echo $res;
}

elseif($ac=='digg')
{
	if($id<1){ echo "err"; return; }
	if($ac2 =='up'|| $ac2=='down' || empty($ac2) ){ } else {  echo "err"; return; }
	if($tab=='art') { $pre='a_'; } else { $pre='d_'; }
	
	$colid=$pre.'id'; $col1=$pre.'up'; $col2=$pre.'down'; $col=$pre.$ac2;
	$res="0:0";
	
	$row = $db->getRow("SELECT ".$col1.",".$col2." FROM {pre}".$tab." WHERE ".$colid."=".$id);
	if($row){
		$col1val = $row[$col1];
		$col2val = $row[$col2];
		$colval = $row[$col];
	}
	unset($row);
	
	if(!empty($ac2)){
		if (getCookie($ac.$tab.$ac2.$id) == "ok"){ echo "haved"; return; }
		$db->Update ("{pre}".$tab,array($col),array($colval+1),$colid."=".$id);
		if($ac2=='up') { $col1val++; }
		if($ac2=='down') { $col2val++; }
		sCookie($ac.$tab.$ac2.$id, "ok");
	}
	echo $col1val .":". $col2val;
}

elseif($ac=='score')
{
	if($id<1){ echo "err"; return;}
	$score = intval(be("get", "score"));
	$res = '{"scoreall":0,"scorenum":0,"score":0.0}';
	if($score<0) { $score = 0;} elseif( $score > 10) { $score = 10; }
	if($tab=='art') { $col='a'; } else { $col='d'; }
	
	$sql="SELECT ".$col."_score,".$col."_scoreall,".$col."_scorenum FROM {pre}".$tab." WHERE ".$col."_id=" .$id;
	$row=$db->getRow($sql);
	if($row){
		$d_score = $row["d_score"];
		$d_scoreall = $row["d_scoreall"];
		$d_scorenum = $row["d_scorenum"];
		
		if($score>0){
			if(getCookie($tab."score".$id)=="ok"){ echo "haved"; return;}
			$d_scoreall +=  $score;
			$d_scorenum++;
			$d_score = round( $d_scoreall / $d_scorenum ,1);
			$db->Update ("{pre}vod",array($col."_score",$col."_scoreall",$col."_scorenum"),array($d_score,$d_scoreall,$d_scorenum),$col."_id=".$id);
			sCookie ($tab."score".$id,"ok");
		}
		if($d_score>10) { $d_score=10; }
		$res = '{"scoreall":'.$d_scoreall.',"scorenum":'.$d_scorenum.',"score":'.$d_score.'}';
	}
	unset ($row);
	echo $res;
}

elseif($ac=='userfav')
{
	if($id<1){ echo "err"; exit;}
	if (isN($_SESSION["userid"])) { echo "login";exit; }
	
	$res = "err";
	$row = $db->getRow("select * from {pre}user where u_id=".$_SESSION["userid"]);
	if ($row){
		$u_fav = $row["u_fav"];
		
		if (isN($u_fav)){
			$u_fav = ",". $id . ",";
			$res = "ok";
		}
		else{
			if (strpos( ",".$u_fav ,",".$id.",")>0){
				$res = "haved";
			}
			else{
				$u_fav = $u_fav . $id . ",";
				$res = "ok";
			}
		}
		$db->Update ("{pre}user",array("u_fav"),array($u_fav),"u_id=".$_SESSION["userid"]);
	}
	unset($row);
	echo $res;
}

elseif($ac=='reporterr')
{
	$g_vid = be("post","g_vid");
	$g_name = be("post","g_name");
	$g_content = be("post","g_content"); 
	
	if (!isNum($g_vid)){ $g_vid=0; } 
    if (isN($g_name) || isN($g_content)){ alert('请输入昵称和内容'); exit;}
    if (getTimeSpan("last_gbooktime") < $MAC['other']['gbooktime']){ alert('请不要频繁操作');exit; }
    $pattern = '/[^\x00-\x80]/'; 
	if(!preg_match($pattern,$g_content)){
		alert('内容必须包含中文,请重新输入!'); exit;
	}
    
    $g_name = badFilter($g_name);
    $g_content = badFilter($g_content);
    $g_ip = ip2long(getIP());
    $g_time = time();
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$db->Add ("{pre}gbook", array("g_vid","g_hide","g_name", "g_ip", "g_time", "g_content"), array($g_vid, $g_hide, $g_name, $g_ip, $g_time, $g_content));
	
	$_SESSION["last_gbook"]  = time();
	$_SESSION["code_gbook"] = "";
	echo "<script>alert('报错成功,多谢支持!');window.close();</script>";
}

elseif($ac=='suggest')
{
	$q=be("get","q");
	$t=be("get","t");
	$res = '{"status":0,"info":"err","data":[{}]}';
	if(!empty($q)){
		$sql="SELECT d_name from {pre}vod WHERE d_name like '".$q."%' or d_enname like '".$q."%' ";
		if($t=='art'){
			"SELECT a_name from {pre}art WHERE a_name like '".$q."%' or a_enname like '".$q."%' ";
		}
		$rs = $db->queryArray($sql,false);
		if($rs){
			echo '{"status":1,"info":"ok","data":'. json_encode($rs) . '}';
			return;
		}
		unset($rs);
	}
	echo $res;
}

else
{
}
?>