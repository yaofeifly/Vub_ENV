<?php
/******************************
海洋CMS版权所有 www.seacms.net
功能：资源发布zyAPI模块
版本：1.0
开发：海洋
致谢：该模块参考了MacCms的设计
******************************/
require_once("include/common.php");
require_once("include/main.class.php");
require_once("data/config.cache.inc.php");

//判断是否开启api服务
$isopenapi=file_get_contents("data/admin/isapi.txt");
if($isopenapi=="0"){echo "服务已关闭";exit;}

$app_apipagenum=20; //每页显示条数
//接收相关参数
$action = addslashes($_GET['ac']);
$rtype  = addslashes($_GET['t']);
$rpage  = addslashes($_GET['pg']);
$rkey   = addslashes($_GET['wd']);
$rday   = addslashes($_GET['h']);
$ids    =addslashes($_GET['ids']);

//判断相关参数并格式化
if (!isNum($rtype)) { $rtype=0;} else { $rtype= intval($rtype);}
if (!isNum($rpage)) { $rpage=1;} else { $rpage= intval($rpage);}
if ($rpage < 1){ $rpage=1;}
if (!isNum($rday)) { $rday=0;} else { $rday= intval($rday);}

$app_apiver="5.0"; 

//判断操作类型
switch($action)
{
	case "videolist":
		cj();
		break;
	default:
		vlist();
		break;
}

function cj()
{
	global $dsql,$rtype,$rpage,$rkey,$rday,$action,$app_apiver,$app_apipagenum,$cfg_basehost,$ids;
	$xmla = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xmla .= "<rss version=\"".$app_apiver."\">";

	$sql = "select d.*,p.body as v_playdata,p.body1 as v_playdata1,t.tname from sea_data d left join `sea_type` t on t.tid=d.tid left join `sea_playdata` p on p.v_id=d.v_id where d.v_recycled=0 ";
	$sql1 = "select count(*) as dd from sea_data where v_recycled=0 ";
	
	if($ids!=""){
		$sql .= " AND d.v_id in (". $ids .")";
		$sql1 .= " AND v_id in (". $ids .")";
	}
	if($rtype>0){
		$sql .= " AND d.tid =".$rtype;
		$sql1 .= " AND tid =".$rtype;
	}
	if($rday>0){
		if (!isNum($rday)){ $rday=1; }
		$whereStr=" AND d.v_addtime > UNIX_TIMESTAMP(date_sub(now(),interval ".$rday." hour)) " ;
		$whereStr1=" AND v_addtime > UNIX_TIMESTAMP(date_sub(now(),interval ".$rday." hour)) " ;
		$sql .=  $whereStr;
		$sql1 .= $whereStr1;
	}
	
	//获取页数
	$row1 = $dsql->GetOne($sql1);
    if(is_array($row1))
	{$nums = $row1['dd'];}else{$nums = 0;}
	
	$pagecount=ceil($nums/$app_apipagenum);
	$sql = $sql ." limit ".($app_apipagenum * ($rpage-1)).",".$app_apipagenum;
	
	$dsql->SetQuery($sql);
	$dsql->Execute('video_c');
	$xml .= "<list page=\"".$rpage."\" pagecount=\"".$pagecount."\" pagesize=\"".$app_apipagenum."\" recordcount=\"".$nums."\">";
	while($row=$dsql->GetObject('video_c'))
		{
			//处理播放地址信息
			if($row->v_playdata1 !=""){$allplayurl=$row->v_playdata."$$$".$row->v_playdata1;}else{$allplayurl=$row->v_playdata;}
			$tempurl = getplayurl($allplayurl);
			
		    if (strpos(",".$row->v_pic,"http://")>0) { $temppic = $row->v_pic; } else { $temppic = $cfg_basehost."/".$row->v_pic; } //图片
			
			$query = "select body  from sea_content  where v_id='$row->v_id' ";
			$rowccc = $dsql->GetOne($query);
			$ccc=$rowccc['body'];

			$plink = $cfg_basehost."/detail/?".$row->v_id.".html"; //来源页面网址
		    $xml .= "<video>";
		    $xml .= "<last>".MyDate('Y-m-d H:i:s',$row->v_addtime)."</last>";
			$xml .= "<id>".$row->v_id."</id>";
			$xml .= "<tid>".$row->tid."</tid>";
			$xml .= "<name><![CDATA[".$row->v_name."]]></name>";
			$xml .= "<type>".$row->tname."</type>";
			$xml .= "<pic>".$temppic."</pic>";
			$xml .= "<lang>".$row->v_lang."</lang>";
			$xml .= "<area>".$row->v_publisharea."</area>";
			$xml .= "<year>".$row->v_publishyear."</year>";
			$xml .= "<state>".$row->v_state."</state>";
			$xml .= "<keywords>".$row->v_tags."</keywords>";
			$xml .= "<len>".$row->v_len."</len>";
			$xml .= "<total>".$row->v_total."</total>";
			$xml .= "<jq>".$row->v_jq."</jq>";
			$xml .= "<nickname>".$row->v_nickname."</nickname>";
			$xml .= "<reweek>".$row->v_reweek."</reweek>";
			$xml .= "<douban>".$row->v_douban."</douban>";
			$xml .= "<mtime>".$row->v_mtime."</mtime>";
			$xml .= "<imdb>".$row->v_imdb."</imdb>";
			$xml .= "<tvs>".$row->v_tvs."</tvs>";
			$xml .= "<company>".$row->v_company."</company>";
			$xml .= "<ver>".$row->v_ver."</ver>";
			$xml .= "<longtxt>".$row->v_longtxt."</longtxt>";
			$xml .= "<note><![CDATA[".$row->v_note."]]></note>";
			$xml .= "<actor><![CDATA[".$row->v_actor."]]></actor>";
			$xml .= "<director><![CDATA[".$row->v_director."]]></director>";
			$xml .= "<dl>".$tempurl."</dl>";
			$xml .= "<des><![CDATA[".$ccc."]]></des>";
			$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
		}
		$xml .= "</list>";
	unset($row);
	$xmla .= $xml . "</rss>";
	echo $xmla;
}

function vlist()
{
	global $dsql,$rtype,$rpage,$rkey,$rday,$action,$app_apiver,$app_apipagenum,$cfg_basehost ;
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xml .= "<rss version=\"".$app_apiver."\">";
		
	//视频列表开始
	
	$sql ="select d.v_id,d.v_name,d.v_state,d.v_note,d.tid,d.v_addtime,p.body as v_playdata,p.body1 as v_playdata1,t.tname from sea_data d left join `sea_type` t on t.tid=d.tid left join `sea_playdata` p on p.v_id=d.v_id where d.v_recycled=0 ";
	$sql1 = "select count(*) as dd from sea_data where v_recycled=0 ";
	
	if ($rtype > 0) { $where .= " and d.tid=" . $rtype; $where1 .= " and tid=" . $rtype;}
	if ($rkey !="") { $where .= " and d.v_name like '%".$rkey."%' "; $where1 .= " and v_name like '%".$rkey."%' "; }
	$sql .= $where. " order by d.v_addtime desc";
	$sql1 .= $where1;
	
	//获取页数
	$row1 = $dsql->GetOne($sql1);
    if(is_array($row1)){$nums = $row1['dd'];}else{$nums = 0;}
	
	$pagecount=ceil($nums/$app_apipagenum);
	$sql = $sql ." limit ".($app_apipagenum * ($rpage-1)).",".$app_apipagenum;
	$dsql->SetQuery($sql);
	$dsql->Execute('video_list');
	
	if($nums==0){
		$xml .= "<list page=\"".$rpage."\" pagecount=\"0\" pagesize=\"".$app_apipagenum."\" recordcount=\"0\">";
	}
	else{
		$xml .= "<list page=\"".$rpage."\" pagecount=\"".$pagecount."\" pagesize=\"".$app_apipagenum."\" recordcount=\"".$nums."\">";
		
		while($row=$dsql->GetObject('video_list'))
	  	{
			$plink = $cfg_basehost."/detail/?".$row->v_id.".html"; //来源页面网址
			$fromstr=getFromStr($row->v_playdata)." ".getFromStr($row->v_playdata1); //获取播放和下载组名
			$xml .= "<video>";
			$xml .= "<last>".MyDate('Y-m-d H:i:s',$row->v_addtime)."</last>";
			$xml .= "<id>".$row->v_id."</id>";
			$xml .= "<tid>".$row->tid."</tid>";
			$xml .= "<name><![CDATA[".$row->v_name."]]></name>";
			$xml .= "<type>".$row->tname."</type>";
			$xml .= "<dt>".$fromstr."</dt>";
			$xml .= "<note><![CDATA[".$row->v_note."]]></note>";
			$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
	  	}
	}
	unset($row);
	$xml .= "</list>";
	//视频列表结束
	
	//分类列表开始
	$xml .= "<class>";
	$sqltype = "select * from sea_type where tptype=0 ";
	$dsql->SetQuery($sqltype);
	$dsql->Execute('video_type');
	while($rowtype=$dsql->GetObject('video_type'))
	{
		$xml .= "<ty id=\"". $rowtype->tid."\">". $rowtype->tname."</ty>";
	}
	unset($rowtype);
	$xml .= "</class>";
	//分类列表结束
	
	$xml .= "</rss>";
	echo $xml;
	
}


function getplayurl($urls)
{
	
	$urls=str_replace('$','|*|',$urls);
	$arr1 = explode("|*||*||*|",$urls);
	
		$zzt=count($arr1);
		$playerKindsfile="data/admin/playerKinds.xml";
		$xml = simplexml_load_file($playerKindsfile);
		if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
		$z=array();
		foreach($xml as $player){
		$k=$player['flag'];
		$z["$k"]=$player['postfix'];
		}
	
	foreach ($arr1 as $v){
		$arr2=explode("|*||*|",$v);
		for($i=0;$i<$zzt;$i++)
		{
		   if($arr2['0']=='下载地址' or $arr2['0']=='下载地址一' or $arr2['0']=='下载地址二'  or $arr2['0']=='下载地址三'  or $arr2['0']=='下载地址四' or $arr2['0']=='下载地址五'){$flag= "down";} 
		   else	
		      {
				  $f=$arr2['0'];
				  $flag=$z["$f"];
			  } 
		} 
		$str = $str . "<dd flag=\"". $flag ."\"><![CDATA[" . $arr2['1']. "]]></dd>";	
	}
	$str=str_replace('|*|','$',$str);
	return $str;
}

function isnum($varnum){	
  $string_var = "0123456789";
  $len_string = strlen($varnum);
  if(substr($varnum,0,1)=="0"){
  return false;
   die();
  }else{
	   for($i=0;$i<$len_string;$i++){
	   $checkint = strpos($string_var,substr($varnum,$i,1));
	   if($checkint===false){
	    	return false;
	  	 die();
		  }
	  }
	  return true;
   }
 }

?>