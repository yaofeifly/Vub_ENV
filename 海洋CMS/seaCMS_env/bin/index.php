<?php
/*
	[seacms1.0] (C)2011-2012 seacms.net
*/
if(!file_exists("data/common.inc.php"))
{
    header("Location:install/index.php");
    exit();
}
require_once ("include/common.php");
require_once sea_INC."/main.class.php";
if($cfg_runmode=='0')
{
	header("Location:index".$cfg_filesuffix2);
}
//if($cfg_runmode=='0' || file_exists($cfg_cmspath."index".$cfg_filesuffix2)){
//	 header("Location:/".$cfg_cmspath."index".$cfg_filesuffix2);
//}else{

	echoIndex();
//}
function echoIndex()
{
	global $cfg_iscache,$t1;
	$cacheName="parsed_index";
	$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/index.html";
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$indexStr = getFileCache($cacheName);
		}else{
			$indexStr = parseIndexPart($templatePath);
			setFileCache($cacheName,$indexStr);
		}
	}else{
			$indexStr = parseIndexPart($templatePath);
	}
	$indexStr=str_replace("{seacms:member}",front_member(),$indexStr);
	echo str_replace("{seacms:runinfo}",getRunTime($t1),$indexStr) ;
}

function parseIndexPart($templatePath)
{
	global $mainClassObj;
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseNewsAreaList($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId);
	$content=$mainClassObj->parseTopicList($content);
	$content=$mainClassObj->parseLinkList($content);
	$content=$mainClassObj->parseIf($content);
	return $content;
}
