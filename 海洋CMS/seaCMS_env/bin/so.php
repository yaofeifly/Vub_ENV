<?php
require_once("include/common.php");
require_once(sea_INC."/main.class.php");
require_once(sea_INC."/splitword.class.php");
$page = (isset($page) && is_numeric($page)) ? $page : 1;
$searchtype = (isset($searchtype) && is_numeric($searchtype)) ? $searchtype : -1;
$searchword = isset($searchword) && $searchword ? $searchword:'';
$searchword = FilterSearch(stripslashes($searchword));
$searchword = addslashes(cn_substr($searchword,20));
$searchword = RemoveXSS(stripslashes($searchword));
$searchword = trim($searchword);
if($cfg_notallowstr !='' && m_eregi($cfg_notallowstr,$searchword))
{
	ShowMsg("你的搜索关键字中存在非法内容，被系统禁止！","-1","0",$cfg_search_time*1000);
	exit();
}
if($searchword=='')
{
	ShowMsg('关键字不能为空！','-1','0',$cfg_search_time*1000);
	exit();
}

echoSearchPage();

function echoSearchPage()
{
	global $dsql,$cfg_iscache,$mainClassObj,$page,$t1,$cfg_search_time,$searchword,$searchtype;
	if($cfg_search_time) checkSearchTimes($cfg_search_time);
	$searchTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/newssearch.html";
	$pSize = getPageSizeOnCache($searchTemplatePath,"newssearch","");
	if (empty($pSize)) $pSize=12;
	switch (intval($searchtype)) {
		case -1:
			$whereStr=" where n_recycled=0 and (n_title like '%$searchword%' or n_keyword like '%$searchword%')";
		break;
		case 0:
			$whereStr=" where n_recycled=0 and n_title like '%$searchword%'";	
		break;
		case 1:
			$whereStr=" where n_recycled=0 and n_author like '%$searchword%'";
		break;
		case 2:
			$whereStr=" where n_recycled=0 and n_from like '%$searchword%'";
		break;
		case 3:
			$whereStr=" where n_recycled=0 and n_outline like '%$searchword%'";
		break;
		case 4:
			$whereStr=" where n_recycled=0 and n_letter='".strtoupper($searchword)."'";
		break;
	}
	$sql="select count(*) as dd from sea_news ".$whereStr;
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
	$cacheName="parse_searchnews_";
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseSearchPart($searchTemplatePath);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseSearchPart($searchTemplatePath);
	}
	$tempStr = $content;
	$tempStr = str_replace("{seacms:newssearchword}",$searchword,$tempStr);
	$tempStr = str_replace("{seacms:newssearchnum}",$TotalResult,$tempStr);
	$tempStr = str_replace("{seacms:page}",$page,$tempStr);
	$content=$tempStr;
	$content=$mainClassObj->parseNewsPageList($content,"",$page,$pCount,"newssearch");
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	$searchPageStr = $content;
	GetKeywords($searchword);
	echo str_replace("{seacms:runinfo}",getRunTime($t1),$searchPageStr) ;
}

function parseSearchPart($templatePath)
{
	global $mainClassObj;
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"");
	$content=$mainClassObj->parseVideoList($content);
	$content=$mainClassObj->parseNewsList($content);
	$content=$mainClassObj->parseTopicList($content);
	return $content;
}

function checkSearchTimes($searchtime)
{
	if(GetCookie("ssea2_search")=="ok")
	{
		ShowMsg('搜索限制为'.$searchtime.'秒一次','-1','0');
		//PutCookie("ssea2_search","ok",$searchtime);
		exit;
	}else{
		PutCookie("ssea2_search","ok",$searchtime);
	}
	
}

//获得关键字的分词结果，并保存到数据库
function GetKeywords($keyword)
{
	global $dsql;
	$keyword = cn_substr($keyword,50);
	$row = $dsql->GetOne("Select spwords From `sea_search_keywords` where keyword='".addslashes($keyword)."'; ");
	if(!is_array($row))
	{
		if(strlen($keyword)>7)
		{
			$sp = new SplitWord();
			$keywords = $sp->SplitRMM($keyword);
			$sp->Clear();
			$keywords = m_ereg_replace("[ ]{1,}"," ",trim($keywords));
		}
		else
		{
			$keywords = $keyword;
		}
		$inquery = "INSERT INTO `sea_search_keywords`(`keyword`,`spwords`,`count`,`result`,`lasttime`)
  VALUES ('".addslashes($keyword)."', '".addslashes($keywords)."', '1', '0', '".time()."'); ";
		$dsql->ExecuteNoneQuery($inquery);
	}
	else
	{
		$dsql->ExecuteNoneQuery("Update `sea_search_keywords` set count=count+1,lasttime='".time()."' where keyword='".addslashes($keyword)."'; ");
		$keywords = $row['spwords'];
	}
	return $keywords;
}
?>