<?php
require("conn.php");
require(MAC_ROOT.'/inc/common/360_safe3.php');

if($MAC['APP']['api']['vod']['status']==0){ echo "closed"; exit; }
$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);

$ac = be("get","ac");
$t = intval(be("get","t"));
$pg = intval(be("get","pg"));
$wd = be("get","wd");
$h= intval(be("get","h"));
if ($pg < 1){ $pg=1;}

$app_apiver="5.0";
$apicp=10;

if($ac=='videolist')
{
	$ids= be("all","ids");
	
	$apicn = "maccmsapi-videolist-" . $t . "-" . $pg . "-" . $wd . "-" . $h . "-" . str_replace(",","",$ids); ;
	if (chkCache($apicn)){
		echo getCache($apicn);
		exit;
	}
	
	$xmla = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xmla .= "<rss version=\"".$app_apiver."\">";
	
	$sql = "select * from {pre}vod where 1=1 ";
	$sql1 = "select count(*) from {pre}vod where 1=1 ";
	
	if($ids!=""){
		$sql .= " AND d_id in (". $ids .")";
		$sql1 .= " AND d_id in (". $ids .")";
	}
	if($t>0){
		$sql .= " AND d_type =".$t;
		$sql1 .= " AND d_type =".$t;
	}
	if($h>0){
		if (!isNum($h)){ $h=1; }
		$whereStr=" AND d_time > date_sub(now(),interval ".$h." hour) " ;
		$sql .=  $whereStr;
		$sql1 .= $whereStr;
	}
	
	$nums = $db->getOne($sql1);
	$pagecount=ceil($nums/app_apipagenum);
	$sql = $sql ." limit ".(app_apipagenum * ($pg-1)).",".app_apipagenum;
	$rs = $db->query($sql);
	if (!$rs){
		echo "err：" . "<br>" .$sql;exit;
	}
	else{
		$xml .= "<list page=\"".$pg."\" pagecount=\"".$pagecount."\" pagesize=\"".app_apipagenum."\" recordcount=\"".$nums."\">";
		
		while ($row = $db ->fetch_array($rs))
		{
			$tempurl = urlDeal($row["d_playurl"],$row["d_playfrom"]);
		    if (strpos(",".$row["d_pic"],"http://")>0) { $temppic = $row["d_pic"]; } else { $temppic = app_apicjflag . $row["d_pic"]; }
		    
		    $typearr = getValueByArray($cache[0], "t_id", $row["d_type"]);
			$plink = app_siteurl . $template->getVodPlayUrl($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"],1,1);
				
		    $xml .= "<video>";
		    $xml .= "<last>".$row["d_time"]."</last>";
			$xml .= "<id>".$row["d_id"]."</id>";
			$xml .= "<tid>".$row["d_type"]."</tid>";
			$xml .= "<name><![CDATA[".$row["d_name"]."]]></name>";
			$xml .= "<type>".$typearr["t_name"]."</type>";
			$xml .= "<pic>".$temppic."</pic>";
			$xml .= "<lang>".$row["d_language"]."</lang>";
			$xml .= "<area>".$row["d_area"]."</area>";
			$xml .= "<year>".$row["d_year"]."</year>";
			$xml .= "<state>".$row["d_state"]."</state>";
			$xml .= "<note><![CDATA[".$row["d_remarks"]."]]></note>";
			$xml .= "<actor><![CDATA[".$row["d_starring"]."]]></actor>";
			$xml .= "<director><![CDATA[".$row["d_directed"]."]]></director>";
			$xml .= "<dl>".$tempurl."</dl>";
			$xml .= "<des><![CDATA[".$row["d_content"]."]]></des>";
			//$xml .= "<vlink><![CDATA[".$vlink."]]></vlink>";
			//$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
		}
		$xml .= "</list>";
	}
	unset($rs);
	$xmla .= $xml . "</rss>";
	setCache ($apicn,$xmla,0);
	echo $xmla;
}

else
{
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	$xml .= "<rss version=\"".$app_apiver."\">";
	
	$apicn = "maccmsapi-list-" . $t . "-" . $pg . "-" . $wd . "-" . $h ;
	if (chkCache($apicn)){
		echo getCache($apicn);
		exit;
	}
	
	//视频列表开始
	if (maccms_field_vod_source !="") {
		$tempmaccms_field_vod_source = ",".maccms_table_vod.".".maccms_field_vod_source;
	}
	
	$sql = "select d_id,d_name,d_enname,d_type,d_time,d_remarks,d_playfrom,d_addtime from {pre}vod where 1=1 ";
	$sql1 = "select count(*) from {pre}vod where 1=1 ";
	
	if ($t > 0) { $where .= " and d_type=" . $t; }
	if (app_apivodfilter != "") { $where .= " ". app_apivodfilter." "; }
	if ($wd !="") { $where .= " and d_name like '%".$wd."%' "; }
	$sql .= $where. " order by d_time desc";
	$sql1 .= $where;
	
	$nums= $db -> getOne($sql1);
	$pagecount=ceil($nums/app_apipagenum);
	$sql = $sql ." limit ".(app_apipagenum * ($pg-1)).",".app_apipagenum;
	$rs = $db->query($sql);	
	if (!$rs){
		$nums=0;
		echo "err：" . "<br>" .$sql;exit;
	}
	
	if($nums==0){
		$xml .= "<list page=\"".$pg."\" pagecount=\"0\" pagesize=\"".app_apipagenum."\" recordcount=\"0\">";
	}
	else{
		$xml .= "<list page=\"".$pg."\" pagecount=\"".$pagecount."\" pagesize=\"".app_apipagenum."\" recordcount=\"".$nums."\">";
		
		while ($row = $db ->fetch_array($rs))
	  	{
	  		$typearr = getValueByArray($cache[0], "t_id", $row["d_type"]);
			$plink = app_siteurl . $template->getVodPlayUrl($row["d_id"],$row["d_name"],$row["d_enname"],$row["d_addtime"],$row["d_type"],$typearr["t_name"],$typearr["t_enname"],1,1);
			
			$xml .= "<video>";
			$xml .= "<last>".$row["d_time"]."</last>";
			$xml .= "<id>".$row["d_id"]."</id>";
			$xml .= "<tid>".$row["d_type"]."</tid>";
			$xml .= "<name><![CDATA[".$row["d_name"]."]]></name>";
			$xml .= "<type>".$typearr["t_name"]."</type>";
			$xml .= "<dt>".replaceStr($row["d_playfrom"],'$$$',',')."</dt>";
			$xml .= "<note><![CDATA[".$row["d_remarks"]."]]></note>";
			//$xml .= "<vlink><![CDATA[".$vlink."]]></vlink>";
			//$xml .= "<reurl><![CDATA[".$plink."]]></reurl>";
			$xml .= "</video>";
	  	}
	}
	unset($rs);
	$xml .= "</list>";
	//视频列表结束
	
	//分类列表开始
	$xml .= "<class>";
	$sql = "select * from {pre}vod_type where 1=1 ";
	if (app_apitypefilter != "") { $sql .= app_apitypefilter ; }
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs))
	{
		$xml .= "<ty id=\"". $row["t_id"] ."\">". $row["t_name"] . "</ty>";
	}
	unset($rs);
	$xml .= "</class>";
	//分类列表结束
	
	$xml .= "</rss>";
	if ($pg<=$apicp){
		setCache ($apicn,$xml,0);
	}
	echo $xml;
}

function urlDeal($urls,$froms)
{
	$arr1 = explode("$$$",$urls); $arr1count = count($arr1);
	$arr2 = explode("$$$",$froms); $arr2count = count($arr2);
	for ($i=0;$i<$arr2count;$i++){
		if ($arr1count >= $i){
			$str = $str . "<dd flag=\"". $arr2[$i] ."\"><![CDATA[" . $arr1[$i]. "]]></dd>";
		}
	}
	$str = replaceStr($str,chr(10),"#");
	$str = replaceStr($str,chr(13),"#");
	$str = replaceStr($str,"##","#");
	return $str;
}
?>