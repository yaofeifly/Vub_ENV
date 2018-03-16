<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
@set_time_limit(0);
if(!class_exists("MainClass_Template")) require_once(sea_INC.'/main.class.php');

//清理缓存
autocache_clear(sea_ROOT.'/data/cache');
//生成首页
makeIndex();
//生成地图页，默认关闭，如果想开启，去掉前面的//即可
//makeAllmovie();
//生成自定义页面
$flag = 1 ;
automakeallcustom();
//生成百度地图，默认关闭，如果想开启，去掉前面的//即可
//makeBaidu();
//生成百度结构化数据，默认关闭，如果想开启，去掉前面的//即可
//makeBaidux();
//生成google地图，默认关闭，如果想开启，去掉前面的//即可
//makeGoogle();
//生成rss页面，默认关闭，如果想开启，去掉前面的//即可
//makeRss();
//如果是静态运行
if($cfg_runmode=='0'){
	//生成今日更新内容
	automakeDay();
	//生成专辑首页，默认关闭，如果想开启，去掉前面的//即可
	//automakeTopicIndex();
	//生成专辑页，默认关闭，如果想开启，去掉前面的//即可
	//automakeAllTopic();
}

function automakeDay()
{
	global $dsql;
	$today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y'));
	$wheresql = " and `v_addtime` BETWEEN '{$today_start}' AND '{$today_end}'";
	$pagesize=100;
	if(!$pCount){
	$rowc=$dsql->GetOne("SELECT count(*) as dd FROM `sea_data` WHERE `v_wrong`=0 ".$wheresql);
	$totalnum = $rowc['dd'];
	if($totalnum==0) return false;
	$TotalPage = ceil($totalnum/$pagesize);
	}else{
	$TotalPage = $pCount;
	}
	$sql="select v_id from sea_data where v_wrong=0 $wheresql";
	$dsql->SetQuery($sql);
	$dsql->Execute('makeDay');
	while($row=$dsql->GetObject('makeDay'))
	{
		makeContentById($row->v_id);
	}
	$ids="";
	$sqlt="SELECT tid from sea_data where v_wrong=0 ".$wheresql." GROUP BY tid";
	$dsql->SetQuery($sqlt);
	$dsql->Execute('makeDayt');
	while($rowt=$dsql->GetObject('makeDayt'))
	{
		if(!isTypeHide($rowt->tid)){
			if(empty($ids)) $ids=$rowt->tid; else $ids.=",".$rowt->tid;
		}
	}

	if(!empty($ids)){
		$tl=getTypeListsOnCache();
		foreach($tl as $vv){
			if (strpos(" ,".$ids.",",",".$vv->tid.",")>0){
				if ($vv->upid>0 && strpos(" ,".$ids.",",",".$vv->tid.",")==0) $ids=$vv->tid.",".$ids;
			}
		}
	}
	if(!empty($ids)){
		automakeChannelByIDS($ids);
		return true;
	}
}

function automakeChannelByIDS($ids)
{
	$typeIdArray = array();
	$typeIdArray = explode(",",$ids);
	foreach($typeIdArray as $typeId)
	{
		automakeChannelById($typeId);
	}
}

function automakeChannelById($typeId)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$typeId = empty($typeId) ? 0 : intval($typeId);
	$channelTmpName=getTypeTemplateOnCache($typeId);
	$channelTmpName=empty($channelTmpName) ? "channel.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	$pSize = getPageSizeOnCache($channelTemplatePath,"channel",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeIdOnCache($typeId);
	$typename=getTypeNameOnCache($typeId);
	//echoBegin($typename,"channel");
	$sql="select count(*) as dd from sea_data where tid in (".$typeIds.")";
	$row = $dsql->GetOne($sql);
	if(is_array($row))
	{
		$TotalResult = $row['dd'];
	}
	else
	{
		$TotalResult = 0;
	}
	$pagesize = $pSize;
	$pCount = ceil($TotalResult/$pSize);
	$currentTypeId = $typeId;
	$cacheName = "parse_channel_".$currentTypeId;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("channel",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("channel",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
	}
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
	$tempStr = $content;
	if (isTypeHide($typeId)){
		return true;
	}
	if($TotalResult == 0){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,1));
		$tempStr = str_replace("{channelpage:page}",1,$tempStr);
		$content=$tempStr;
		$content=$mainClassObj->ParsePageList($content,$typeIds,1,$pCount,$TotalResult,"channel",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
	}
	for($i=1;$i<=$pCount;$i++){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,$i));
		$tempStr2 = str_replace("{channelpage:page}",$i,$tempStr);
		$content=$tempStr2;
		$content=$mainClassObj->ParsePageList($content,$typeIds,$i,$pCount,$TotalResult,"channel",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
	}
}

function automakeTopicIndex()
{
	global $mainClassObj, $dsql;
	$row = $dsql->GetOne("select template from sea_topic");
	$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/topicindex.html";
	$rowc=$dsql->GetOne("select count(*) as dd from sea_topic");
	$page_size = getPageSizeOnCache($templatePath,"topicindex",$row['template']);
	if (empty($page_size)) $page_size=12;
	if(is_array($rowc))
	{
		$TotalResult = $rowc['dd'];
	}
	else
	{
		$TotalResult = 0;
	}
	$pCount=ceil($TotalResult/$page_size);
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"");
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseVideoList($content);
	$content=$mainClassObj->parseLinkList($content);
	$content=replaceCurrentTypeId($content,-444);
		$content=str_replace("{seacms:member}",front_member(),$content);
	$tempStr = $content;
	for($i=1;$i<=$pCount;$i++)
	{
		$content=$tempStr;
		$content=$mainClassObj->parseTopicIndexList($content,$i);
		$content=$mainClassObj->parseIf($content);
		if($i==1)$topicindexname=sea_ROOT."/".$GLOBALS['cfg_album_name']."/index".$GLOBALS['cfg_filesuffix2'];
		else $topicindexname=sea_ROOT."/".$GLOBALS['cfg_album_name']."/index".$i.$GLOBALS['cfg_filesuffix2'];
		createTextFile($content,$topicindexname);
	}
	
}

function automakeAllTopic()
{
	global $dsql;
	$dsql->SetQuery("select id from sea_topic order by sort asc");
	$dsql->Execute('altopic');
	while($rowr=$dsql->GetObject('altopic'))
	{
		$rows[]=$rowr;
	}
	unset($rowr);
	if(!is_array($rows)) return false;
	foreach($rows as $row){
		automakeTopicById($row->id);
	}
}

function automakeTopicById($topicId)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$sql="select id,name,template,enname from sea_topic where id =".$topicId;
	$row = $dsql->GetOne($sql);
	if(!is_array($row)) return FALSE;
	$rowc=$dsql->GetOne("select count(*) as dd from sea_data where v_topic=".$topicId);
	$topicTemplatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$row['template'];
	$cacheName="parse_topic_".$topicId;
	$page_size = getPageSizeOnCache($topicTemplatePath,"topicpage",$row['template']);
	if (empty($page_size)) $page_size=12;
	if(is_array($rowc))
	{
		$TotalResult = $rowc['dd'];
	}
	else
	{
		$TotalResult = 0;
	}
	$pCount = ceil($TotalResult/$page_size);
	$topicName=$row['name'];
	$topicEnname=$row['enname'];
	$currentTopicId = $row['id'];
	$currrent_topic_id=$row['id'];
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("topic",$topicTemplatePath);
			$content = str_replace("{seacms:topicname}",$topicName,$content);
			$content = str_replace("{seacms:currrent_topic_id}",$currrent_topic_id,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("topic",$topicTemplatePath);
			$content = str_replace("{seacms:topicname}",$topicName,$content);
			$content = str_replace("{seacms:currrent_topic_id}",$currrent_topic_id,$content);
	}
		$content=str_replace("{seacms:member}",front_member(),$content);
	$mystr = $content;
	if($TotalResult == 0){
		$content=$mystr;
		$content=$mainClassObj->ParsePageList($content,$topicId,1,$pCount,$TotalResult,"topicpage",$currrent_topic_id);
		$content=$mainClassObj->parseIf($content);
		$topiclink=sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,1));
		createTextFile($content,$topiclink);
	}else{
		for($i=1;$i<=$pCount;$i++){
			$content =$mystr;
			$content=$mainClassObj->ParsePageList($content,$topicId,$i,$pCount,$TotalResult,"topicpage",$currrent_topic_id);
			$content=$mainClassObj->parseIf($content);
			$topiclink=sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,$i));
			createTextFile($content,$topiclink);
		}
	}
}


function autoparseCachePart($pageType,$templatePath,$currentTypeId="-444")
{
	global $mainClassObj;
	switch ($pageType) {
		case "channel":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{channelpage:typetext}",getTypeText($currentTypeId),$content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
		break;
		case "newspage":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{newspagelist:typetext}",getTypeText($currentTypeId,1),$content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
		break;
		case "parse_content_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parsePlayPageSpecial($content);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
		break;
		case "parse_play_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parsePlayPageSpecial($content);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
		break;
		case "topic":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content=$mainClassObj->parseLinkList($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
		break;
		case "parse_article_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content=$mainClassObj->parseNewsPageSpecial($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content=$mainClassObj->parseLinkList($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
	}
	return $content;
}


function automakeCustomInfo($templatename)
{
	global $mainClassObj,$dsql,$customLink;
	$self_str="self_";
	$pcount=0;
	$templatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$templatename; 
    $customLink="/".$GLOBALS['cfg_cmspath'].str_replace(".html","",str_replace("#", "/", str_replace($self_str,"",$templatename)))."<page>.html";
	$content=loadFile(sea_ROOT.$templatePath);
	if(strpos($content, "{/seacms:customvideolist}")>0){
		$pSize = getPageSizeOnCache($templatePath,"customvideo",$templatename);
		if (empty($pSize)) $pSize=12;
		$sql="select count(*) as dd from sea_data where v_recycled=0";
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
	}
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseNewsAreaList($content);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId);
	$content=$mainClassObj->parseTopicList($content);
	$content=$mainClassObj->parseLinkList($content);
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{seacms:runinfo}","",$content);
		$content=str_replace("{seacms:member}",front_member(),$content);
	if(strpos($content, "{/customvideolist}")===false)$pcount=1;
	for($i=1;$i<=100;$i++)
	{
		$tmp=$content;
		$tmp=str_replace("{customvideo:page}", $i, str_replace("{customvideopage:page}",$i,$tmp));
		$tmp=$mainClassObj->parsePageList($tmp, 0, $i, $pCount,$TotalResult, "customvideo");
		$link=getCustomLink($i);
		$dir=str_replace($GLOBALS['cfg_cmspath'],'',$link);
		createTextFile ($tmp,sea_ROOT.$dir);
		if($i>=$pCount)break;
	}
}

function autocache_clear($dir) {
  $dh=@opendir($dir);
  while ($file=@readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(is_file($fullpath)) {
          @unlink($fullpath);
      }
    }
  }
  closedir($dh); 
}


function automakeallcustom()
{
	global $cfg_basedir,$cfg_df_style,$cfg_df_html;
	$templetdird = $cfg_basedir."templets/".$cfg_df_style."/".$cfg_df_html."/";
	$dh = dir($templetdird);
	while($filename=$dh->read())
	{
	if(strpos($filename,"elf_")>0) automakeCustomInfo($filename);
	}
}
?>