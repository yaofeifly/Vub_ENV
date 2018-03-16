<?php
require_once(dirname(__FILE__)."/../include/common.php");
require_once(sea_INC."/main.class.php");


if($GLOBALS['cfg_newsparamset']==0||$GLOBALS['cfg_runmode2']==2){
	$paras=str_replace(getnewsfileSuffix(),'',$_SERVER['QUERY_STRING']);
	if(strpos($paras,"-")>0){
		$parasArray=explode("-",$paras);
		$id=$parasArray[0];
		$page=$parasArray[1];
	}else{
		$id=$paras;
		$page=1;
	}
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$page = isset($page) && is_numeric($page) ? $page : 1;
}else{
	$id=$$GLOBALS['cfg_newsparamid'];
	$page = $$GLOBALS['cfg_newsparamid'];
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$page = isset($page) && is_numeric($page) ? $page : 1;
}
if($id==0){
	showmsg('参数丢失，请返回！', -1);
	exit;
}

echoContent($id);

function echoContent($vId)
{
	global $dsql,$cfg_iscache,$mainClassObj,$t1,$page;
	$row=$dsql->GetOne("Select * From `sea_news` where n_id='$vId'");
	if(!is_array($row)){
		echo "<font color='red'>文章ID:".$vId." 该文章所属分类被隐藏</font><br>";
		return false;
	}
	$vType=$row['tid'];
	$contentTmpName=getContentTemplate($vType,1);
	$contentTmpName=empty($contentTmpName) ? "news.html" : $contentTmpName;
	$contentTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$contentTmpName;
	if (strpos(" ,".getHideTypeIDS(1).",",",".$vType.",")>0) exit("<font color='red'>该文章被删除或隐藏</font><br>");
	$typeText = getTypeText($vType,1);
	$contentLink = getArticleLink($vType,$vId,"link");
	$currentTypeId=$vType;
	$typeFlag = "parse_content_" ;
	$cacheName = $typeFlag.$vType;
	$vtag=$row['n_keyword'];
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = parseContentPart($contentTemplatePath,$currentTypeId,$vtag);
		}else{
			$content = parseContentPart($contentTemplatePath,$currentTypeId,$vtag);
			$content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
			$content=str_replace("{news:typeid}",$vType,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseContentPart($contentTemplatePath,$currentTypeId,$vtag);
			$content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
			$content=str_replace("{news:typeid}",$vType,$content);
	}
	$content=str_replace("{news:typeid}",$vType,$content);
	$content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
	$content=str_replace("{news:title}",$row['n_title'],$content);
	$content=str_replace("{news:colortitle}","<span style='color:".$row['n_color']."'>".$row['n_title']."</span>",$content);
	$content=str_replace("{news:encodetitle}",urlencode($row['n_title']),$content);
	$content=str_replace("{news:note}",$row['n_note'],$content);
	$content=str_replace("{news:hit}",$row['n_hit'],$content);
	$content=str_replace("{news:link}",$contentLink,$content);
	$content=str_replace("{news:typelink}",getnewspageLink($vType),$content);
	$content=str_replace("{news:url}",'http://'.str_replace("http://","",$GLOBALS['cfg_basehost']).$contentLink,$content);
	$content=str_replace("{news:upid}",getUpId($vType,1),$content);
	if (strpos($content,"{news:subcontent}")>0||strpos($content,"{news:subtitle}")>0||strpos($content,"{news:subpagenumber}")>0)
	{
		$subarr=parseSubpage($row['n_content']);
		$content=str_replace("{news:subtitle}",$subarr[1],$content);
		$content=str_replace("{news:subcontent}",$subarr[2],$content);
		$substr=getSubStrByFromAndEnd_en($content, "{news:subpagenumber","}","");
		$substrarr=$mainClassObj->parseAttr($substr);
		if($subarr[0]>1)
		$subpagenum=newsSubPageLinkInfo($page,$substrarr['len'],$subarr[0],$vType,$vId);
		else
		$subpagenum='';
		$content=str_replace("{news:subpagenumber".$substr."}",$subpagenum,$content);
	}
	$content=str_replace("{news:diggnum}",$row['n_digg'],$content);
	$score=number_format($row[n_score]/$row[n_scorenum],1);
	$content=str_replace("{news:score}",$score,$content);
	$content=str_replace("{news:scorenum}",$row['n_score'],$content);
	$content=str_replace("{news:scorenumer}",$row['n_scorenum'],$content);
	$content=str_replace("{news:treadnum}",$row['n_tread'],$content);
	$content=str_replace("{news:nolinkkeywords}",$row['n_keyword'],$content);
	if (strpos($content,"{news:keywords}")>0)
	$content=str_replace("{news:keywords}",getKeywordsList($row['n_keyword'],"&nbsp;&nbsp;"),$content);
	$n_pic=$row['n_pic'];
	if(!empty($n_pic)){
		if(strpos(' '.$n_pic,'://')>0){
			$content=str_replace("{news:pic}",$n_pic,$content);
		}else{
			$content=str_replace("{news:pic}",'/'.$GLOBALS['cfg_cmspath'].ltrim($n_pic,'/'),$content);
		}
	}else{
		$content=str_replace("{news:pic}",'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$content);
	}
	$content=str_replace("{news:author}",$row['n_author'],$content);
	$content=str_replace("{news:from}",$row['n_from'],$content);
	$content=str_replace("{news:addtime}",MyDate('Y-m-d H:i',$row['n_addtime']),$content);
	$content=str_replace("{news:scorenum}",$row['n_score'],$content);
	$content=str_replace("{news:outline}",$row['n_outline'],$content);
	$content=str_replace("{news:commend}",$row['n_commend'],$content);
	$content=str_replace("{news:content}",$row['n_content'],$content);
	$content = parseNewsLabelHaveLen($content,$row['n_title'],"title");
	$content = parseNewsLabelHaveLen($content,$row['n_outline'],"outline");
	$content = parseNewsLabelHaveLen($content,$row['n_content'],"content");
	$content = $mainClassObj->paresPreNextNews($content,$vId,$typeFlag,$vType);
	$content = $mainClassObj->paresVideoInNews($content);
	$content = str_replace("{news:textlink}",$typeText."&nbsp;&nbsp;&raquo;&nbsp;&nbsp;".$row['n_title'],$content);
	$content=$mainClassObj->parseIf($content);
	$content=$mainClassObj->parseNews($content,$row['n_title'],$row['n_color'],$row['n_content'],$row['n_addtime']);
	$content=str_replace("{news:id}",$row['n_id'],$content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	echo str_replace("{seacms:runinfo}",getRunTime($t1),$content) ;
}

function parseContentPart($templatePath,$currentTypeId,$vtag)
{
	global $mainClassObj;
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
	$content=$mainClassObj->parseNewsPageSpecial($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);	
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseNewsAreaList($content);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId,$topid,$vtag);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId,$vtag);
	$content=$mainClassObj->parseTopicList($content);
	return $content;
}

function parseSubpage($des)
{
	global $page;
	$desarr=explode("#p#",$des);
	$ret=array(count($desarr),"","");
	$i=$page-1;
	if($i>-1&&$ret[0]>=$i)
	{
		if(strpos(" ".$desarr[$i],"#e#")>0)
		{
			$y=explode("#e#",$desarr[$i]);$ret[1]=$y[0];$ret[2]=$y[1];
		}else
		{
			$ret[1]='';$ret[2]=$desarr[$i];
		}
	}
	return $ret;
}
?>