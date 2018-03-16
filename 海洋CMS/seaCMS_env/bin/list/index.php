<?php
require_once(dirname(__FILE__)."/../include/common.php");
require_once(sea_INC."/main.class.php");

if($GLOBALS['cfg_runmode']==2||$GLOBALS['cfg_paramset']==0){
	$paras=str_replace(getfileSuffix(),'',$_SERVER['QUERY_STRING']);
	if(strpos($paras,"-")>0){
		$parasArray=explode("-",$paras);
		$tid=$parasArray[0];
		$page=$parasArray[1];
	}else{
		$tid=intval($paras);
		$page=1;
	}
	$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
	$page = isset($page) && is_numeric($page) ? $page : 1;
}else{
	$tid = $$GLOBALS['cfg_paramid'];
	$page = $$GLOBALS['cfg_parampage'];
	$tid = isset($tid) && is_numeric($tid) ? $tid : 0;
	$page = isset($page) && is_numeric($page) ? $page : 1;
}
if($tid==0){
	showmsg('参数丢失，请返回！', -1);
	exit;
}
$GLOBALS[tid]=$tid;
echoChannel($tid);

function echoChannel($typeId)
{
	global $dsql,$cfg_iscache,$mainClassObj,$page,$t1,$cfg_user,$cfg_basehost;
	$channelTmpName=getTypeTemplate($typeId);
	$channelTmpName=empty($channelTmpName) ? "channel.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	if (strpos(" ,".getHideTypeIDS().",",",".$typeId.",")>0) exit("<font color='red'>视频列表为空或被隐藏</font><br>");
	if ($cfg_user == 1){
        if (!getUserAuth($typeId, "list")){exit("<font color='red'>您没有权限浏览此内容!</font><script>function JumpUrl(){history.go(-1);}setTimeout('JumpUrl()',1000);</script>");}
    }
	$pSize = getPageSizeOnCache($channelTemplatePath,"channel",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeId($typeId);
	$typename=getTypeName($typeId);
	if($typeId!="")
		$extrasql = " or FIND_IN_SET('".$typeId."',v_extratype)<>0 ";
	else
		$extrasql = "";
	$sql="select count(*) as dd from sea_data where (tid in (".$typeIds.") ".$extrasql.")";
	$row = $dsql->GetOne($sql);
	if(is_array($row))
	{
		$TotalResult = $row['dd'];
	}
	else
	{
		$TotalResult = 0;
	}
	$pCount = ceil($TotalResult/$pSize);
	$currentTypeId = $typeId;
	$cacheName = "parse_channel_".$currentTypeId;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseChannelPart($channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
			$content = str_replace("{channelpage:typeid}",$currentTypeId,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseChannelPart($channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
			$content = str_replace("{channelpage:typeid}",$currentTypeId,$content);
	}
	$content = str_replace("{channelpage:page}",$page,$content);
	$content=$mainClassObj->ParsePageList($content,$typeIds,$page,$pCount,$TotalResult,"channel",$currentTypeId);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	$content = str_replace("{channelpage:order-hit-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=hit&tid=".$typeId,$content);
	$content = str_replace("{channelpage:order-hitasc-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=hitasc&tid=".$typeId,$content);
	
	$content = str_replace("{channelpage:order-id-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=id&tid=".$typeId,$content);
	$content = str_replace("{channelpage:order-idasc-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=idasc&tid=".$typeId,$content);
	
	$content = str_replace("{channelpage:order-time-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=time&tid=".$typeId,$content);
	$content = str_replace("{channelpage:order-timeasc-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=timeasc&tid=".$typeId,$content);
	
	$content = str_replace("{channelpage:order-commend-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=commend&tid=".$typeId,$content);
	$content = str_replace("{channelpage:order-commendasc-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=commendasc&tid=".$typeId,$content);
	
	$content = str_replace("{channelpage:order-score-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=score&tid=".$typeId,$content);
	$content = str_replace("{channelpage:order-scoreasc-link}",$cfg_basehost."/search.php?page=1&searchtype=5&order=scoreasc&tid=".$typeId,$content);
	
	
	echo str_replace("{seacms:runinfo}",getRunTime($t1),$content) ;
}

function parseChannelPart($templatePath,$currentTypeId)
{
	global $mainClassObj;
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId);
	$content=$mainClassObj->parseTopicList($content);
	$content = str_replace("{channelpage:typetext}",getTypeText($currentTypeId),$content);
	$content = str_replace("{channelpage:keywords}",getTypeKeywords($currentTypeId),$content);
	$content = str_replace("{channelpage:description}",getTypeDescription($currentTypeId),$content);
	$content = str_replace("{channelpage:title}",getTypeTitle($currentTypeId),$content);
	return $content;
}
?>