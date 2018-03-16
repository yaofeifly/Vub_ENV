<?php 
function makeIndex($by='video')
{
	global $mainClassObj;
	switch ($by){
		case 'video':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/index.html";
			break;
		case 'news':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/newsindex.html";
			break;
	}
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseNewsAreaList($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId);
	$content=$mainClassObj->parseTopicList($content);
	$content=$mainClassObj->parseLinkList($content);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{seacms:runinfo}","",$content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	switch ($by){
		case 'video':
			$indexname=sea_ROOT."/index".getfileSuffix();
			createTextFile($content,$indexname);
			return "首页生成完毕 <a target='_blank' href='../index".getfileSuffix()."'><font color=red>浏览首页</font></a><br>";
			break;
		case 'news':
			$indexname=sea_ROOT."/news".getnewsfileSuffix();
			createTextFile($content,$indexname);
			return "新闻首页生成完毕 <a target='_blank' href='../news".getnewsfileSuffix()."'><font color=red>浏览首页</font></a><br>";
			break;
	}
}

function makeAllmovie($by='video')
{
	global $mainClassObj;
	switch ($by){
		case 'video':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/map.html";
		break;
		case 'news':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/newsmap.html";
		break;
	}
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
	$content=str_replace("{seacms:runinfo}","",$content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	switch ($by){
		case 'video':
			$allmoviename=sea_ROOT."/allmovie".getfileSuffix();
			createTextFile($content,$allmoviename);
			return "地图页生成完毕 <a target='_blank' href='../allmovie".getfileSuffix()."'><font color=red>浏览地图页</font></a><br>";
		break;
		case 'news':
			$allmoviename=sea_ROOT."/allnews".getnewsfileSuffix();
			createTextFile($content,$allmoviename);
			return "新闻地图页生成完毕 <a target='_blank' href='../allnews".getnewsfileSuffix()."'><font color=red>浏览地图页</font></a><br>";
		break;
	}
}

function makeVideoJs($by='video')
{
	global $mainClassObj;
	switch ($by){
		case 'video':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/js.html";
		break;
		case 'news':
			$templatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/newsjs.html";
		break;
	}
	$content=loadFile(sea_ROOT.$templatePath);
	$content=$mainClassObj->parseTopAndFoot($content);
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseNewsAreaList($content);
	$content=$mainClassObj->parseVideoList($content,$currentTypeId);
	$content=$mainClassObj->parseNewsList($content,$currentTypeId);
	$content=$mainClassObj->parseTopicList($content);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	switch ($by){
		case 'video':
			$jsname=sea_ROOT."/js.js";
			createTextFile($content,$jsname);
			$jsname="../js.js";
			echo "数据JS调用文件生成完毕 <a target='_blank' href='".$jsname."'><font color=red>浏览</font></a><br>";
		break;
		case 'news':
			$jsname=sea_ROOT."/newsjs.js";
			createTextFile($content,$jsname);
			$jsname="../newsjs.js";
			echo "数据JS调用文件生成完毕 <a target='_blank' href='".$jsname."'><font color=red>浏览</font></a><br>";
		break;
	}
}

function makeDay()
{
	global $dsql,$page,$pCount;
	$today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y'));
	$wheresql = " and `v_addtime` BETWEEN '{$today_start}' AND '{$today_end}'";
	$pagesize=30;
	if(!$pCount){
	$rowc=$dsql->GetOne("SELECT count(*) as dd FROM `sea_data` WHERE `v_wrong`=0 and v_recycled=0 ".$wheresql);
	$totalnum = $rowc['dd'];
	if($totalnum==0) exit("当天没有视频<br>");
	$TotalPage = ceil($totalnum/$pagesize);
	}else{
	$TotalPage = $pCount;
	}
	$currentPage = empty($page) ? 1 : intval($page);
	$limitstart = ($currentPage-1) * $pagesize;
	if($limitstart<0) $limitstart=0;
	$sql="select v_id from sea_data where v_wrong=0 and v_recycled=0 $wheresql limit $limitstart,$pagesize";
	echoBegin ("正在开始生成当天影片,当前是第<font color='red'>".$currentPage."</font>页,共<font color='red'>".$TotalPage."</font>页<br>","content");
	$dsql->SetQuery($sql);
	$dsql->Execute('makeDay');
	while($row=$dsql->GetObject('makeDay'))
	{
		echo makeContentById($row->v_id);
		@ob_flush();
		@flush();

	}
	if($currentPage>$TotalPage)
	{
		$ids="";
		$sqlt="SELECT tid from sea_data where v_wrong=0 and v_recycled=0 ".$wheresql." GROUP BY tid";
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
			echo "生成当天影片搞定<script language='javascript'>self.location='?action=channelbyids&ids=".$ids."&action3=site';</script>";
			exit;
		}else{
			alertMsg ("一键生成全部搞定","");
			exit;
		}
	}
	echoDaySuspend($currentPage+1,$TotalPage);
}

function makeNewsDay()
{
	global $dsql,$page,$pCount;
	$today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y'));
	$wheresql = " and `n_addtime` BETWEEN '{$today_start}' AND '{$today_end}'";
	$pagesize=100;
	if(!$pCount){
	$rowc=$dsql->GetOne("SELECT count(*) as dd FROM `sea_news` WHERE n_recycled=0 ".$wheresql);
	$totalnum = $rowc['dd'];
	if($totalnum==0) exit("当天没有文章<br>");
	$TotalPage = ceil($totalnum/$pagesize);
	}else{
	$TotalPage = $pCount;
	}
	$currentPage = empty($page) ? 1 : intval($page);
	$limitstart = ($currentPage-1) * $pagesize;
	if($limitstart<0) $limitstart=0;
	$sql="select n_id from sea_news where n_recycled=0 $wheresql limit $limitstart,$pagesize";
	echoBegin ("正在开始生成当天文章,当前是第<font color='red'>".$currentPage."</font>页,共<font color='red'>".$TotalPage."</font>页<br>","content");
	$dsql->SetQuery($sql);
	$dsql->Execute('makeNewsDay');
	while($row=$dsql->GetObject('makeNewsDay'))
	{
		makeArticleById($row->n_id);
		@ob_flush();
		@flush();
	}
	if($currentPage>$TotalPage)
	{
		$ids="";
		$sqlt="SELECT tid from sea_news where n_recycled=0 ".$wheresql." GROUP BY tid";
		$dsql->SetQuery($sqlt);
		$dsql->Execute('makeDayt');
		while($rowt=$dsql->GetObject('makeDayt'))
		{
			if(!isTypeHide($rowt->tid)){
				if(empty($ids)) $ids=$rowt->tid; else $ids.=",".$rowt->tid;
			}
		}

		if(!empty($ids)){
			$tl=getTypeListsOnCache(1);
			foreach($tl as $vv){
				if (strpos(" ,".$ids.",",",".$vv->tid.",")>0){
					if ($vv->upid>0 && strpos(" ,".$ids.",",",".$vv->tid.",")==0) $ids=$vv->tid.",".$ids;
				}
			}
		}
		if(!empty($ids)){
			echo "生成当天文章搞定<script language='javascript'>self.location='?action=partbyids&ids=".$ids."&action3=site';</script>";
			exit;
		}else{
			alertMsg ("一键生成全部搞定","");
			exit;
		}
	}
	echoNewsDaySuspend($page+1,$TotalPage);
}


function echoDaySuspend($curPage,$pCount)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=day&page=".$curPage."&pcount=".$pCount."';}</script>";
}

function echoNewsDaySuspend($curPage,$pCount)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=newsday&page=".$curPage."&pcount=".$pCount."';}</script>";
}

function makeArticleById($vId)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$playn = 0;
	$row=$dsql->GetOne("Select * From `sea_news` where n_id='$vId'");
	if(!is_array($row)){
		echo "<font color='red'>影片ID:".$vId." 该影片所属分类被隐藏，跳过生成</font><br>";
		return false;
	}
	$vType=$row['tid'];
	$vtag=$row['n_keyword'];
	$contentTmpName=getContentTemplate($vType,1);
	$contentTmpName=empty($contentTmpName) ? "news.html" : $contentTmpName;
	$contentTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$contentTmpName;
	$contentLink = str_replace($GLOBALS['cfg_cmspath'],"",getArticleLink($vType, $vId, ''));
	$contentLink2=getArticleLink($vType,$vId,"link");
	$typeText = getTypeText($vType,1);
	$currentTypeId=$vType;
	$typeFlag = "parse_article_";
	$templatePath=$contentTemplatePath;
	$cacheName = $typeFlag.$vType;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
			if(strpos($content,"{news:typename}")>0) $content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
			$content=str_replace("{news:typeid}",$vType,$content);
		}else{
			$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
			if(strpos($content,"{news:typename}")>0) $content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
			$content=str_replace("{news:typeid}",$vType,$content);
			//setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
			if(strpos($content,"{news:typename}")>0) $content=str_replace("{news:typename}",getNewsTypeNameOnCache($vType),$content);
			$content=str_replace("{news:typeid}",$vType,$content);
	}
	$content=str_replace("{news:title}",$row['n_title'],$content);
	$content=str_replace("{news:colortitle}","<span style='color:".$row['n_color']."'>".$row['n_title']."</span>",$content);
	$content=str_replace("{news:encodetitle}",urlencode($row['n_title']),$content);
	$content=str_replace("{news:note}",$row['n_note'],$content);
	$content=str_replace("{news:hit}",$row['n_hit'],$content);
	$content=str_replace("{news:diggnum}",$row['n_digg'],$content);
	$content=str_replace("{news:scorenum}",$row['n_score'],$content);
	$content=str_replace("{news:scorenumer}",$row['n_scorenum'],$content);
	$score=number_format($row[n_score]/$row[n_scorenum],1);
	$content=str_replace("{news:score}",$score,$content);
	$content=str_replace("{news:treadnum}",$row['n_tread'],$content);
	$content=str_replace("{news:nolinkkeywords}",$row['n_keyword'],$content);
	$content=str_replace("{news:link}",$contentLink,$content);
	$content=str_replace("{news:url}",'http://'.str_replace("http://","",$GLOBALS['cfg_basehost']).$contentLink,$content);
	$content=str_replace("{news:upid}",getUpId($vType,1),$content);
	if (strpos($content,"{news:keywords}")>0) $content=str_replace("{news:keywords}",getKeywordsList($row['n_keyword'],"&nbsp;"),$content);
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
	$content=str_replace("{news:scorenumer}",$row['n_scorenum'],$content);
	$score=number_format($row[n_score]/$row[n_scorenum],1);
	$content=str_replace("{news:score}",$score,$content);
	$content=str_replace("{news:outline}",$row['n_outline'],$content);
	$content=str_replace("{news:commend}",$row['n_commend'],$content);
	$content=str_replace("{news:content}",$row['n_content'],$content);
	$content = parseNewsLabelHaveLen($content,$row['n_title'],"title");
	$content = parseNewsLabelHaveLen($content,$row['n_outline'],"outline");
	$content = parseNewsLabelHaveLen($content,$row['n_content'],"content");
	$content = $mainClassObj->paresPreNextNews($content,$vId,$typeFlag,$vType);
	$content = str_replace("{news:textlink}",$typeText."&nbsp;&raquo;&nbsp;".$row['n_title'],$content);
	$content=$mainClassObj->parseIf($content);
	$content=str_replace("{news:id}",$row['n_id'],$content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	if (strpos($content,"{news:subcontent}")>0||strpos($content,"{news:subtitle}")>0||strpos($content,"{news:subpagenumber}")>0){	
		$desarr=explode("#p#",$row['n_content']);
		for($i=0;$i<count($desarr);$i++)
		{
			$tmp=$content;
			if(strpos(" ".$desarr[$i],"#e#")>0)
			{
				$y=explode("#e#",$desarr[$i]);
				$tmp=str_replace("{news:subtitle}",$y[0],$tmp);
				$tmp=str_replace("{news:subcontent}",$y[1],$tmp);
				$contentLink = str_replace($GLOBALS['cfg_cmspath'],"",getArticleLink($vType,$vId,"",$i+1));
			}else
			{
				$tmp=str_replace("{news:subtitle}",'',$tmp);
				$tmp=str_replace("{news:subcontent}",$desarr[$i],$tmp);
				$contentLink = str_replace($GLOBALS['cfg_cmspath'],"",getArticleLink($vType,$vId,"",$i+1));
			}
			$substr=getSubStrByFromAndEnd_en($content, "{news:subpagenumber","}","");
			$substrarr=$mainClassObj->parseAttr($substr);
			if(count($desarr)>1)
				$subpagenum=newsSubPageLinkInfo($i+1,$substrarr['len'],count($desarr),$vType,$vId);
			else
				$subpagenum='';
			$tmp=str_replace("{news:subpagenumber".$substr."}",$subpagenum,$tmp);
			createTextFile($tmp,sea_ROOT.$contentLink,"");
			echoEach($row["n_title"],$i,'..'.$contentLink,"article");
		}
	}else{
		createTextFile($content,sea_ROOT.$contentLink,"");
		echoEach($row["n_title"],$i,'..'.$contentLink,"article");
	}
}

function makeContentById($vId)
{
	global $dsql,$cfg_isalertwin,$cfg_ismakeplay,$cfg_iscache,$mainClassObj;
	$playn = 0;
	$playTemFileName=($cfg_isalertwin==1) ? "openplay.html" : "play.html";
	$playTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$playTemFileName;
	$row=$dsql->GetOne("Select d.*,p.body as v_playdata,p.body1 as v_downdata,c.body as v_content From `sea_data` d left join `sea_playdata` p on p.v_id=d.v_id left join `sea_content` c on c.v_id=d.v_id where d.v_id='$vId'");
	if(!is_array($row)){
		return "<font color='red'>影片ID:".$vId." 该影片所属分类被隐藏，跳过生成</font><br>";
	}
	if($row['v_recycled']==1){return "<font color='red'>影片ID:".$vId." 该影片被隐藏，跳过生成</font><br>";}
	$GLOBALS['zid']=$vId;
	
	$vType=$row['tid'];
	$vExtraType = $row['v_extratype'];
	$contentTmpName=getContentTemplateOnCache($vType);
	$contentTmpName=empty($contentTmpName) ? "content.html" : $contentTmpName;
	$contentTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$contentTmpName;
	$row['v_enname'] = empty($row['v_enname']) ? Pinyin($row['v_name']) : $row['v_enname'];
	$contentLink = str_replace($GLOBALS['cfg_cmspath'],"",getContentLink($vType,$vId,"",date('Y-n',$row['v_addtime']),$row['v_enname']));
	$contentLink2=getContentLink($vType,$vId,"link",date('Y-n',$row['v_addtime']),$row['v_enname']);
	$typeText = getTypeText($vType);
	$currentTypeId=$vType;
	$GLOBALS[tid]=$currentTypeId;
	if ($cfg_ismakeplay=='0') $playn=1;
	$stringecho = '';
	for($n=$playn;$n<=1;$n++)
	{
		switch ($n) {
			case 0:
				$typeFlag = "parse_play_";
				$templatePath=$playTemplatePath;
			break;
			case 1:
				$typeFlag = "parse_content_";
				$templatePath=$contentTemplatePath;
			break;
		}
		$vtag=$row['v_name'];
		$cacheName = $typeFlag.$vType;
		if($cfg_iscache){
			if(chkFileCache($cacheName)){
				$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
			}else{
				$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
			}
		}else{
				$content = parseCachePart($typeFlag,$templatePath,$currentTypeId,$vtag);
		}
		$content=$mainClassObj->parsePlayList($content,$vId,$vType,date('Y-n',$row['v_addtime']),$row['v_enname'],$row['v_playdata'],'play');
		$content=$mainClassObj->parsePlayList($content,$vId,$vType,date('Y-n',$row['v_addtime']),$row['v_enname'],$row['v_downdata'],'down');
		$content=$mainClassObj->parsePlayPageSpecial($content);
		$content=str_replace("{playpage:id}",$row['v_id'],$content);
		$content=str_replace("{playpage:upid}",getUpId($vType),$content);
		$content=str_replace("{playpage:name}",$row['v_name'],$content);
		$content=str_replace("{playpage:url}",'http://'.str_replace("http://","",$GLOBALS['cfg_basehost']).$contentLink2,$content);
		$content=str_replace("{playpage:link}",$contentLink2,$content);
		$content=str_replace("{playpage:playlink}",getPlayLink2($vType,$vId,date('Y-n',$row['v_addtime']),$row['v_enname']),$content);
		if(strpos($content,"{playpage:typename}")>0) 
		{
			$content=str_replace("{playpage:typename}",getTypeName($vType).getExtraTypeName($vExtraType),$content);	
		}
		if(strpos($content,"{playpage:linktypename}")>0) 
		{
			$connector = "</a>";
			$content=str_replace("{playpage:linktypename}","<a href=\"".getChannelPagesLink($vType)."\">".getTypeName($vType).$connector.getExtraTypeName($vExtraType,$connector).$connector,$content);	
		}
		$content=str_replace("{playpage:typelink}",getChannelPagesLink($vType),$content);
		$content=str_replace("{playpage:lang}",$row['v_lang'],$content);
		$content=str_replace("{playpage:encodename}",urlencode($row['v_name']),$content);
		$content=str_replace("{playpage:typeid}",$row['tid'],$content); 
		$content=str_replace("{playpage:note}",$row['v_note'],$content);
		$content=str_replace("{playpage:longtxt}",$row['v_longtxt'],$content);		
		$content=str_replace("{playpage:diggnum}",$row['v_digg'],$content);
		$score=number_format($row[v_score]/$row[v_scorenum],1);
	    $content=str_replace("{playpage:score}",$score,$content);
		$content=str_replace("{playpage:scorenum}",$row['v_score'],$content);
		$content=str_replace("{playpage:scorenumer}",$row['v_scorenum'],$content);
		$content=str_replace("{playpage:treadnum}",$row['v_tread'],$content);
		$content=str_replace("{playpage:nolinkkeywords}",$row['v_tags'],$content);
		$content=str_replace("{playpage:nolinkjqtype}",$row['v_jq'],$content);
		$content=str_replace("{playpage:dayhit}",$row['v_dayhit'],$content);
		$content=str_replace("{playpage:weekhit}",$row['v_weekhit'],$content);
		$content=str_replace("{playpage:monthhit}",$row['v_monthhit'],$content);
		$content=str_replace("{playpage:nickname}",$row['v_nickname'],$content);
		$content=str_replace("{playpage:reweek}",$row['v_reweek'],$content);
		$content=str_replace("{playpage:vodlen}",$row['v_len'],$content);
		$content=str_replace("{playpage:vodtotal}",$row['v_total'],$content);
		$content=str_replace("{playpage:douban}",$row['v_douban'],$content);
		$content=str_replace("{playpage:mtime}",$row['v_mtime'],$content);
		$content=str_replace("{playpage:imdb}",$row['v_imdb'],$content);
		$content=str_replace("{playpage:tvs}",$row['v_tvs'],$content);
		$content=str_replace("{playpage:company}",$row['v_company'],$content); 
		$content=str_replace("{playpage:desktopurl}",'/'.$GLOBALS['cfg_cmspath'].'desktop.php?name='.urlencode($row['v_name']).'&url='.urlencode('http://'.str_replace("http://","",$GLOBALS['cfg_basehost']).$contentLink),$content);
		if (strpos($content,"{playpage:keywords}")>0) $content=str_replace("{playpage:keywords}",getKeywordsList($row['v_tags'],"&nbsp;"),$content);
		if (strpos($content,"{playpage:jqtype}")>0) $content=str_replace("{playpage:jqtype}",getJqList($row['v_jq'],"&nbsp;"),$content);
		$v_pic=$row['v_pic'];
		if(!empty($v_pic)){
		if(strpos(' '.$v_pic,'://')>0){
		$content=str_replace("{playpage:pic}",$v_pic,$content);
		}else{
		$content=str_replace("{playpage:pic}",'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$content);
		}
		}else{
		$content=str_replace("{playpage:pic}",'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$content);
		}
		
		$v_spic=$row['v_spic'];
		if(!empty($v_spic)){
		if(strpos(' '.$v_spic,'://')>0){
		$content=str_replace("{playpage:spic}",$v_spic,$content);
		}else{
		$content=str_replace("{playpage:spic}",'/'.$GLOBALS['cfg_cmspath'].ltrim($v_spic,'/'),$content);
		}
		}else{
		$content=str_replace("{playpage:spic}",'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$content);
		}
		
		$v_gpic=$row['v_gpic'];
		if(!empty($v_gpic)){
		if(strpos(' '.$v_gpic,'://')>0){
		$content=str_replace("{playpage:gpic}",$v_gpic,$content);
		}else{
		$content=str_replace("{playpage:gpic}",'/'.$GLOBALS['cfg_cmspath'].ltrim($v_gpic,'/'),$content);
		}
		}else{
		$content=str_replace("{playpage:gpic}",'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$content);
		}
		
		$v_actor=$row['v_actor'];
		$v_tags=$row['v_tags'];
		$v_des=$row['v_content'];
		$v_des=doPseudo($v_des, $vId);
		$v_des=htmlspecialchars_decode($v_des);
		$content=str_replace("{playpage:actor}",getKeywordsList($v_actor,"&nbsp;"),$content);
	    $content=str_replace("{playpage:director}",getKeywordsList($row['v_director'],"&nbsp;"),$content);
		$content=str_replace("{playpage:money}",getKeywordsList($row['v_money'],"&nbsp;"),$content);
		$content=str_replace("{playpage:tags}",getTagsList($v_tags,"&nbsp;"),$content);
		$content=str_replace("{playpage:nolinkactor}",$v_actor,$content);
		$content=str_replace("{playpage:nolinkdirector}",$row['v_director'],$content);
		$content=str_replace("{playpage:nolinkatags}",$v_tags,$content);
		$content=str_replace("{playpage:publishtime}",$row['v_publishyear'],$content);
		$content=str_replace("{playpage:ver}",$row['v_ver'],$content);
		$content=str_replace("{playpage:publisharea}",$row['v_publisharea'],$content);
		$content=str_replace("{playpage:lang}",$row['v_lang'],$content);
		$content=str_replace("{playpage:addtime}",MyDate('Y-m-d H:i',$row['v_addtime']),$content);
		$content=str_replace("{playpage:state}",$row['v_state'],$content);
		$content=str_replace("{playpage:commend}",$row['v_commend'],$content);
		$content=str_replace("{playpage:des}",$v_des,$content);
		$content=str_replace("{playpage:url}",$GLOBALS['cfg_basehost'].$contentLink2,$content);
		$content=str_replace("{playpage:link}",$contentLink2,$content);
		$content = parseLabelHaveLen($content,$v_actor,"actor");
		$content = parseLabelHaveLen($content,$v_actor,"nolinkactor");
		$content = parseLabelHaveLen($content,$v_tags,"tags");
		$content = parseLabelHaveLen($content,$v_tags,"nolinktags");
		$content = parseLabelHaveLen($content,Html2Text($v_des),"des");
		$content = parseLabelHaveLen($content,$row['v_name'],"name");
		$content = parseLabelHaveLen($content,$row['v_note'],"note");
		$content=str_replace("{seacms:member}",front_member(),$content);
		switch ($typeFlag) {
			case "parse_content_":
				$content = $mainClassObj->paresPreNextVideo($content,$vId,$typeFlag,$vType);
				$content = str_replace("{playpage:textlink}",$typeText."&nbsp;&raquo;&nbsp;".$row['v_name'],$content);
				$content=$mainClassObj->parseIf($content);
				createTextFile($content,sea_ROOT.$contentLink,"");
				$stringecho .= echoEach($row["v_name"],$i,'..'.$contentLink,"content");
			break;
			case "parse_play_":
				global $cfg_playaddr_enc;
//隐藏的播放地址start
$str=$row['v_playdata'];
$arr1=array();
$arr2=array();
$arr1=explode('$$$',$str);
$p=getPlayerKindsArray2();
foreach($p as $key=>$player2)
{
	if($player2[0]==0)
	{$arr2[]=$key;}
}
foreach($arr2 as $player)
{
	foreach($arr1 as $key=>$dz)
	{
		if(strstr($dz,$player)!==false)
		{$arr1[$key]='该组已屏蔽$$已屏蔽';}
	}
}
$str=implode('$$$',$arr1); //最终地址
//隐藏的播放地址end
				$content = $mainClassObj->paresPreNextVideo($content,$vId,$typeFlag,$vType);
				if($cfg_playaddr_enc=='escape'){
					$content = str_replace("{playpage:playurlinfo}","<script>var VideoInfoList=unescape(\"".escape($str)."\")</script>",$content);
				}elseif($cfg_playaddr_enc=='base64'){
					$content = str_replace("{playpage:playurlinfo}","<script>var VideoInfoList=base64decode(\"".base64_encode($str)."\")</script>",$content);
				}else{
					$content = str_replace("{playpage:playurlinfo}","<script>var VideoInfoList=\"".$str."\"</script>",$content);
				}
				$content = str_replace("{playpage:textlink}",$typeText."&nbsp;&raquo;&nbsp;<a href='".$contentLink2."'>".$row['v_name']."</a>",$content);
				$content = str_replace("{playpage:player}","<script>var paras=getHtmlParas('".$GLOBALS['cfg_filesuffix2']."');_lOlOl10l(paras[2],paras[1])</script><iframe id='cciframe' scrolling='no' frameborder='0' allowfullscreen></iframe>",$content);
				$content=$mainClassObj->parseIf($content);
				$playArr = playData2Ary($row['v_playdata']);
				makePlayByData($vType,$vId,$playArr,$content,date('Y-n',$row['v_addtime']),$row['v_enname'],$stringecho);
			break;
		}
	}
	$dsql->ExecuteNoneQuery("update sea_data set v_ismake='1' where v_id=".$vId);
	return $stringecho;
}

function makePlayByData($vType,$vId,$playArr,$content,$sdate,$enname,$stringecho)
{
	if($GLOBALS['cfg_ismakeplay']==1){
		for($i=0;$i<$playArr[0];$i++)
		{
			$tmp =$content;
			$tmp = str_replace("{playpage:from}",$playArr[1][$i],$tmp);
			foreach ($playArr[2][$i] as $n=>$play){
				$tmp1 =$tmp;
				$playLink = str_replace($GLOBALS['cfg_cmspath'],"",getPlayLink2($vType,$vId,$sdate,$enname,$i,$n));
				$tmp1 = str_replace("{playpage:part}",$play,$tmp1);
				createTextFile($tmp1,sea_ROOT.$playLink,"");
				$stringecho .= echoEach($play, $i, '..'.$playLink, "play");
			}
		}
	}else{
		$content = str_replace("{playpage:part}","",$content);
		$content = str_replace("{playpage:from}","",$content);
		$playLink = str_replace($GLOBALS['cfg_cmspath'],"",getPlayLink2($vType,$vId,$sdate,$enname));
		createTextFile($content,sea_ROOT.$playLink,"");
	}
	
}

function echoPartSuspend2($ids,$typeIdIndex,$action3)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextChannel();\",".$cfg_stoptime."000);function makeNextChannel(){location.href='?action=partbyids&ids=".$ids."&index=".$typeIdIndex."&action3=".$action3."';}</script>";
}

function isTypeHide($channel)
{
	$isTypeHide=strpos(" ,".getHideTypeIDS().",",",".trim($channel).",")>0;
	return $isTypeHide;
}

function isNewsTypeHide($channel)
{
	$isNewsTypeHide=strpos(" ,".getHideTypeIDS(1).",",",".trim($channel).",")>0;
	return $isNewsTypeHide;
}

function makeContentByChannel($channel,$isIncludeSub,$makeNoncreate=0)
{
	global $dsql,$page,$pcount,$action2,$action3,$index,$curTypeIndex;
	$curTypeIndex=$index;
	if (empty($curTypeIndex)) $curTypeIndex=0;
	if ($isIncludeSub){
		$typeIds = getTypeId($channel);
		$sqlStr=" where tid in (".$typeIds.")".($makeNoncreate?' and v_ismake=0':'');
	}else{
		$typeIds=$channel;
		$sqlStr=" where tid=".$typeIds.($makeNoncreate?' and v_ismake=0':'');
	}
	$typeZnName=getTypeNameOnCache(intval($channel));
	$currentPage = empty($page) ? 1 : intval($page);
	$rowc = $dsql->GetOne("Select count(*) as dd From `sea_data` $sqlStr");
	$totalnum = $rowc['dd'];
	if (isTypeHide($channel) || $totalnum==0){
		if (empty($action2)){
			echo "该分类<font color='red'>".$typeZnName."</font>无视频或被隐藏（一二级栏目模板名称一样时，一级栏目不生成）<br>";
			return true;
		}elseif($action2=="allcontent"){
			echo "该分类<font color='red'>".$typeZnName."</font>无视频或被隐藏（一二级栏目模板名称一样时，一级栏目不生成）<br>";
			echoContentSuspendPerChannel(($curTypeIndex+1),$action2,$action3,$makeNoncreate);
			return true;
		}
	}
	$pagesize=100;
	$TotalPage = empty($pcount)?ceil($totalnum/$pagesize):$pcount;
	$limitstart = ($currentPage-1) * $pagesize;
	if($limitstart<0) $limitstart=0;
	$sql="select v_id from sea_data $sqlStr limit ".($makeNoncreate?0:$limitstart).",$pagesize";
	echoBegin ("正在开始生成栏目<font color='red'>".$typeZnName."</font>的内容页,当前是第<font color='red'>".$currentPage."</font>页,共<font color='red'>".$TotalPage."</font>页<br>","content");

	$dsql->SetQuery($sql);
	$dsql->Execute('makeContentByChannel');
	while($row=$dsql->GetObject('makeContentByChannel'))
	{
		echo makeContentById($row->v_id);
		@ob_flush();
		@flush();
	}
	if($currentPage>=$TotalPage){
		if (empty($action2)){
			echo "恭喜此分类搞定";
			return true;
		}elseif($action2=="allcontent"){
			echo "恭喜此分类搞定";
			echoContentSuspendPerChannel(($curTypeIndex+1),$action2,$action3,$makeNoncreate);
			return true;
		}
	}
	echoContentSuspend($curTypeIndex,($currentPage+1),$TotalPage,$channel,$action2,$action3,$makeNoncreate);
}

function makeArticleByChannel($channel,$isIncludeSub)
{
	global $dsql,$page,$action2,$action3,$index,$curTypeIndex;
	$curTypeIndex=$index;
	if (empty($curTypeIndex)) $curTypeIndex=0;
	if ($isIncludeSub){
		$typeIds = getTypeId($channel,1);
		$sqlStr=" where tid in (".$typeIds.")";
	}else{
		$typeIds=$channel;
		$sqlStr=" where tid=".$typeIds;
	}
	$typeZnName=getNewsTypeNameOnCache(intval($channel));
	$currentPage = empty($page) ? 1 : intval($page);
	$rowc = $dsql->GetOne("Select count(*) as dd From `sea_news` $sqlStr");
	$totalnum = $rowc['dd'];
	if (isNewsTypeHide($channel) || $totalnum==0){
		if (empty($action2)){
			echo "该分类<font color='red'>".$typeZnName."</font>无视频或已生成完毕<br>";
			return true;
		}elseif($action2=="allnewscontent"){
			echo "该分类<font color='red'>".$typeZnName."</font>无视频或已生成完毕<br>";
			echoContentSuspendPerPart(($curTypeIndex+1),$action2,$action3);
			return true;
		}
	}
	$pagesize=100;
	$TotalPage = ceil($totalnum/$pagesize);
	$limitstart = ($currentPage-1) * $pagesize;
	if($limitstart<0) $limitstart=0;
	$sql="select n_id from sea_news $sqlStr limit $limitstart,$pagesize";
	echoBegin ("正在开始生成栏目<font color='red'>".$typeZnName."</font>的内容页,当前是第<font color='red'>".$currentPage."</font>页,共<font color='red'>".$TotalPage."</font>页<br>","content");
	$dsql->SetQuery($sql);
	$dsql->Execute('makeContentByChannel');
	while($row=$dsql->GetObject('makeContentByChannel'))
	{
		makeArticleById($row->n_id);
		@ob_flush();
		@flush();
	}
	if($currentPage>=$TotalPage){
		if (empty($action2)){
			echo "恭喜此分类搞定";
			return true;
		}elseif($action2=="allnewscontent"){
			echo "恭喜此分类搞定";
			echoContentSuspendPerPart(($curTypeIndex+1),$action2,$action3);
			return true;
		}
	}
	echoNewsContentSuspend($curTypeIndex,($currentPage+1),$channel,$action2,$action3);
}


function makeChannelById($typeId)
{
	global $dsql,$cfg_iscache,$mainClassObj,$page,$index, $action3,$action,$cfg_basehost ;
	@session_write_close();
	$typeId = empty($typeId) ? 0 : intval($typeId);
	$channelTmpName=getTypeTemplateOnCache($typeId);
	$channelTmpName=empty($channelTmpName) ? "channel.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	$pSize = getPageSizeOnCache($channelTemplatePath,"channel",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeId($typeId);
	$typename=getTypeNameOnCache($typeId);
	$sql="select count(*) as dd from sea_data where v_recycled=0 and (tid in (".$typeIds.") or v_extratype in (".$typeIds."))";
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
	if($page<1) $page=1;
	$mstart = ($page-1)*100+1;
	$mend = $page*100;
	if($mend>$pCount) $mend = $pCount;
	echoBegin($typename,"channel",$mstart,$mend);
	$currentTypeId = $typeId;
	$GLOBALS[tid]=$currentTypeId;
	$cacheName = "parse_channel_".$currentTypeId;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("channel",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
			$content = str_replace("{channelpage:typeid}",$currentTypeId,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("channel",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{channelpage:typename}",$typename,$content);
			$content = str_replace("{channelpage:typeid}",$currentTypeId,$content);
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
		echo "分类<font color=red >".$typename."</font>为隐藏状态";
	}
	if($TotalResult == 0||strpos($content,'{/seacms:channellist}')===false){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,1));
		$tempStr = str_replace("{channelpage:page}",1,$tempStr);
		$content=$tempStr;
		$content=$mainClassObj->ParsePageList($content,$typeIds,1,$pCount,$TotalResult,"channel",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,1,'all')));
		echoEach($typename,1,'..'.$channelLink,"channel");
		if($action=='channel')
		{
			echo '恭喜此分类搞定';
			die;
		}
		$page=1;
		echoChannelSuspend($index+1, $action3 , $page ,$typeId);die;
	}
	for($i=$mstart;$i<=$mend;$i++){
		$tmp=$content;
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,$i));
		$tmp = str_replace("{channelpage:page}",$i,$tmp);
		$tmp=$mainClassObj->ParsePageList($tmp,$typeIds,$i,$pCount,$TotalResult,"channel",$currentTypeId);
		$tmp=$mainClassObj->parseIf($tmp);
		createTextFile($tmp,sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,$i,'all')));
		echoEach($typename,$i,'..'.$channelLink,"channel");
		unset($tmp);
		@ob_flush();
		@flush();
	}
	
	$n = ceil($pCount/100);
	if($page<$n)
	{
		$page++;
		echoChannelSuspend($index, $action3 , $page ,$typeId);
	}else
	{
		if($action=='channel')
		{
			echo '恭喜此分类搞定';
			die;
		}
		$page=1;
		echoChannelSuspend($index+1, $action3 , $page ,$typeId);
	}
}

function makeNewsChannelById($typeId)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$typeId = empty($typeId) ? 0 : intval($typeId);
	$channelTmpName=getTypeTemplate($typeId,1);
	$channelTmpName=empty($channelTmpName) ? "newspage.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	$pSize = getPageSizeOnCache($channelTemplatePath,"newspage",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeId($typeId,1);
	$typename=getNewsTypeName($typeId);
	echoBegin($typename,"channel");
	$sql="select count(*) as dd from sea_news where tid in (".$typeIds.")";
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
	$cacheName = "parse_newschannel_".$currentTypeId;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("newspage",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{newspagelist:typename}",$typename,$content);
			$content = str_replace("{newspagelist:keywords}",getNewsTypeKeywords($currentTypeId),$content);
			$content = str_replace("{newspagelist:description}",getNewsTypeDescription($currentTypeId),$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("newspage",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{newspagelist:typename}",$typename,$content);
			$content = str_replace("{newspagelist:keywords}",getNewsTypeKeywords($currentTypeId),$content);
			$content = str_replace("{newspagelist:description}",getNewsTypeDescription($currentTypeId),$content);
	}
	$content=str_replace("{seacms:member}",front_member(),$content);
	$tempStr = $content;
	if (isTypeHide($typeId)){
		echo "分类<font color=red >".$typename."</font>为隐藏状态、跳过生成";
		return true;
	}
	if($TotalResult == 0){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getnewspageLink($currentTypeId,1));
		$tempStr = str_replace("{channelpage:page}",1,$tempStr);
		$content=$tempStr;
		$content=$mainClassObj->ParseNewsPageList($content,$typeIds,1,$pCount,"newspage",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
		echoEach($typename,1,'..'.$channelLink,"newspage");
	}
	for($i=1;$i<=$pCount;$i++){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getnewspageLink($currentTypeId,$i));
		$tempStr = str_replace("{channelpage:page}",$i,$tempStr);
		$content=$tempStr;
		$content=$mainClassObj->ParseNewsPageList($content,$typeIds,$i,$pCount,"newspage",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
		echoEach($typename,$i,'..'.$channelLink,"newspage");
		@ob_flush();
		@flush();
	}
}

function makeLengthChannelById($typeId,$startpage,$endpage)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$typeId = empty($typeId) ? 0 : intval($typeId);
	$channelTmpName=getTypeTemplateOnCache($typeId);
	$channelTmpName=empty($channelTmpName) ? "channel.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	$pSize = getPageSizeOnCache($channelTemplatePath,"channel",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeId($typeId);
	$typename=getTypeNameOnCache($typeId);
	echoBegin($typename,"channel");
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
	if ($TotalResult==0){
		echo "分类<font color=red >该目录无数据</font>";
		return true;
	}

	for($i=1;$i<=$pCount;$i++){
		if ($i>=$startpage && $i<=$endpage){
		$tempStr2 = str_replace("{channelpage:page}",$i,$tempStr);
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getChannelPagesLink($currentTypeId,$i));
		$content=$tempStr2;
		$content=$mainClassObj->ParsePageList($content,$typeIds,$i,$pCount,$TotalResult,"channel",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
		echoEach($typename,$i,'..'.$channelLink,"channel");
		@ob_flush();
		@flush();
		}
	}
}

function makeLengthPartById($typeId,$startpage,$endpage)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$typeId = empty($typeId) ? 0 : intval($typeId);
	$channelTmpName=getTypeTemplate($typeId,1);
	$channelTmpName=empty($channelTmpName) ? "newspage.html" : $channelTmpName;
	$channelTemplatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$channelTmpName;
	$pSize = getPageSizeOnCache($channelTemplatePath,"newspage",$channelTmpName);
	if (empty($pSize)) $pSize=12;
	$typeIds = getTypeId($typeId,1);
	$typename=getNewsTypeName($typeId);
	echoBegin($typename,"channel");
	$sql="select count(*) as dd from sea_news where tid in (".$typeIds.")";
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
	$cacheName = "parse_newschannel_".$currentTypeId;
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("newspage",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{newspagelist:typename}",$typename,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("newspage",$channelTemplatePath,$currentTypeId);
			$content = str_replace("{newspagelist:typename}",$typename,$content);
	}
	$content=str_replace("{seacms:member}",front_member(),$content);
	$tempStr = $content;
	if ($TotalResult==0){
		echo "分类<font color=red >该目录无数据</font>";
		return true;
	}

	for($i=1;$i<=$pCount;$i++){
		if ($i>=$startpage && $i<=$endpage){
		$channelLink=str_replace($GLOBALS['cfg_cmspath'],"",getnewspageLink($currentTypeId,$i));
		$tempStr = str_replace("{channelpage:page}",$i,$tempStr);
		$content=$tempStr;
		$content=$mainClassObj->ParseNewsPageList($content,$typeIds,$i,$pCount,"newspage",$currentTypeId);
		$content=$mainClassObj->parseIf($content);
		createTextFile($content,sea_ROOT.$channelLink);
		echoEach($typename,$i,'..'.$channelLink,"newspage");
		@ob_flush();
		@flush();
		}
	}
}

function makeChannelByIDS()
{
	global $index,$action3,$ids;
	$curTypeIndex=$index;
	$typeIdArray = explode(",",$ids);
	$typeIdArrayLen = count($typeIdArray);
	if (empty($curTypeIndex)){
		$curTypeIndex=$index=0;
	}else{
		if(intval($curTypeIndex)>intval($typeIdArrayLen-1)){
			if (empty($action3)){
				alertMsg ("生成所有栏目全部搞定","");
				exit();
			}elseif($action3=="site"){
				makeIndex();
				makeAllmovie();
				alertMsg ("一键生成全部搞定","");
				exit();
			}
		}
	}
	$typeId = $typeIdArray[$curTypeIndex];
	if(empty($typeId)){
		exit("分类丢失");
	}else{
		makeChannelById($typeId);
	}
}

function makePartByIDS()
{
	global $index,$action3,$ids;
	$curTypeIndex=$index;
	$typeIdArray = explode(",",$ids);
	$typeIdArrayLen = count($typeIdArray);
	if (empty($curTypeIndex)){
		$curTypeIndex=0;
	}else{
		if(intval($curTypeIndex)>intval($typeIdArrayLen-1)){
			if (empty($action3)){
				alertMsg ("生成所有栏目全部搞定","");
				exit();
			}elseif($action3=="site"){
				makeIndex('news');
				makeAllmovie('news');
				alertMsg ("一键生成全部搞定","");
				exit();
			}
		}
	}
	$typeId = $typeIdArray[$curTypeIndex];
	if(empty($typeId)){
		exit("分类丢失");
	}else{
		makeNewsChannelById($typeId);
		echoPartSuspend2($ids,($curTypeIndex+1),$action3);
	}
}

function makeTopicIndex()
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
	$content=replaceCurrentTypeId($content,-444);
	$content=$mainClassObj->parseHistory($content);
	$content=$mainClassObj->parseSelf($content);
	$content=$mainClassObj->parseGlobal($content);
	$content=$mainClassObj->parseMenuList($content,"");
	$content=$mainClassObj->parseAreaList($content);
	$content=$mainClassObj->parseVideoList($content);
	$content=$mainClassObj->parseTopicList($content);
	$content=$mainClassObj->parseNewsList($content);
	$content=$mainClassObj->parseLinkList($content);
	$content=str_replace("{seacms:member}",front_member(),$content);
	$tempStr = $content;
	for($i=1;$i<=$pCount;$i++)
	{
		$content=$tempStr;
		$content=$mainClassObj->parseTopicIndexList($content,$i);
		$content=$mainClassObj->parseIf($content);
		$topicindexname=sea_ROOT."/".$GLOBALS['cfg_album_name']."/index".($i==1?'':$i).$GLOBALS['cfg_filesuffix2'];
		createTextFile($content,$topicindexname);
		$topicindexname="../".$GLOBALS['cfg_album_name']."/".($i==1?'':'index'.$i.$GLOBALS['cfg_filesuffix2']);
		echo "专题首页第".$i."页生成完毕 <a target='_blank' href='".$topicindexname."'><font color=red>浏览专题首页</font></a><br>";
	}
	
}

function makeAllTopic()
{
	global $dsql;
	$dsql->SetQuery("select id from sea_topic order by sort asc");
	$dsql->Execute('altopic');
	while($rowr=$dsql->GetObject('altopic'))
	{
		$rows[]=$rowr;
	}
	unset($rowr);
	if(!is_array($rows)) exit("不存在专题");
	foreach($rows as $row){
		makeTopicById($row->id);
	}
}

function makeTopicById($topicId)
{
	global $dsql,$cfg_iscache,$mainClassObj;
	$sql="select * from sea_topic where id =".$topicId;
	$row = $dsql->GetOne($sql);
	if(!is_array($row)) exit("不存在此专题");
						$sql="select vod from sea_topic where id='$topicId'";
						$rows=array();
						$dsql->SetQuery($sql);
						$dsql->Execute('al');
						while($rowr=$dsql->GetObject('al'))
						{
						$rows[]=$rowr;
						}
						unset($rowr);
						$aa=explode("ttttt",$rows[0]->vod);
						$zlistvid= str_replace("ttttt",",",$rows[0]->vod);
	$topicTemplatePath="/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$row['template'];
	$cacheName="parse_topic_".$topicId;
	$page_size = getPageSizeOnCache($topicTemplatePath,"topicpage",$row['template']);
	if (empty($page_size)) $page_size=12;
	if(is_array($aa))
	{
		$TotalResult = count($aa)-1;
	}
	else
	{
		$TotalResult = 0;
	}
	$pCount = ceil($TotalResult/$page_size);
	$topicName=$row['name'];
	$topicDes=$row['des'];
	$topicKeyword=$row['keyword'];
	$topicPic=$row['pic'];
	$topicPic=$GLOBALS['cfg_cmspath']."uploads/zt/".$topicPic;
	$topicEnname=$row['enname'];
	$currentTopicId = $row['id'];
	$currrent_topic_id=$row['id'];
	if($cfg_iscache){
		if(chkFileCache($cacheName)){
			$content = getFileCache($cacheName);
		}else{
			$content = parseCachePart("topic",$topicTemplatePath);
			$content = str_replace("{seacms:topicname}",$topicName,$content);
			$content = str_replace("{seacms:topicdes}",$topicDes,$content);
			$content = str_replace("{seacms:topickeyword}",$topicKeyword,$content);
			$content = str_replace("{seacms:currrent_topic_id}",$currrent_topic_id,$content);
			$content = str_replace("{seacms:topicpic}",$topicPic,$content);
			setFileCache($cacheName,$content);
		}
	}else{
			$content = parseCachePart("topic",$topicTemplatePath);
			$content = str_replace("{seacms:topicname}",$topicName,$content);
			$content = str_replace("{seacms:topicdes}",$topicDes,$content);
			$content = str_replace("{seacms:topickeyword}",$topicKeyword,$content);
			$content = str_replace("{seacms:currrent_topic_id}",$currrent_topic_id,$content);
			$content = str_replace("{seacms:topicpic}",$topicPic,$content);
	}
	$content=str_replace("{seacms:member}",front_member(),$content);
	$mystr = $content;
	if($TotalResult == 0){
		$content=$mystr;
		$content=$mainClassObj->ParsePageList($content,$topicId,1,$pCount,$TotalResult,"topicpage",$currrent_topic_id);
		$content=$mainClassObj->parseIf($content);
		$topiclink=sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,1));
		createTextFile($content,$topiclink);
		$topiclink='..'.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,1));
		echo "专题<font color='red'>".$topicName."</font>第1页生成完毕&nbsp;<a target='_blank' href='".$topiclink."'><font color=red>".$topiclink."</font></a><br>";
	}else{
		for($i=1;$i<=$pCount;$i++){
			$content =$mystr;
			$content=$mainClassObj->ParsePageList($content,$topicId,$i,$pCount,$TotalResult,"topicpage",$currrent_topic_id);
			$content=$mainClassObj->parseIf($content);
			$topiclink=sea_ROOT.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,$i));
			createTextFile($content,$topiclink);
			$topiclink='..'.str_replace($GLOBALS['cfg_cmspath'],"",getTopicLink($topicEnname,$i));
			echo "专题<font color='red'>".$topicName."</font>第".$i."页生成完毕&nbsp;<a target='_blank' href='".$topiclink."'><font color=red>".$topiclink."</font></a><br>";
			@ob_flush();
			@flush();
		}
	}
}

function parseCachePart($pageType,$templatePath,$currentTypeId="-444",$vtag)
{
	global $mainClassObj;
	switch ($pageType) {
		case "channel":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{channelpage:typetext}",getTypeText($currentTypeId),$content);
			$content = str_replace("{channelpage:keywords}",getTypeKeywords($currentTypeId),$content);
			$content = str_replace("{channelpage:description}",getTypeDescription($currentTypeId),$content);
			$content = str_replace("{channelpage:title}",getTypeTitle($currentTypeId),$content);
		break;
		case "newspage":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId);
			$content=$mainClassObj->parseTopicList($content);
			$content = str_replace("{newspagelist:typetext}",getTypeText($currentTypeId,1),$content);
		break;
		case "parse_content_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parsePlayPageSpecial($content);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId,$vtag);
			$content=$mainClassObj->parseTopicList($content);
		break;
		case "parse_play_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parsePlayPageSpecial($content);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId,$vtag);
			$content=$mainClassObj->parseTopicList($content);
		break;
		case "topic":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
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
		break;
		case "parse_article_":
			$content=loadFile(sea_ROOT.$templatePath);
			$content=$mainClassObj->parseTopAndFoot($content);
			$content = str_replace("{seacms:currenttypeid}",$currentTypeId,$content);
			$content=$mainClassObj->parseNewsPageSpecial($content);
			$content=$mainClassObj->parseSelf($content);
			$content=$mainClassObj->parseHistory($content);
			$content=$mainClassObj->parseGlobal($content);
			$content=$mainClassObj->parseMenuList($content,"",$currentTypeId);
			$content=$mainClassObj->parseAreaList($content);
			$content=$mainClassObj->parseNewsAreaList($content);
			$content=$mainClassObj->parseVideoList($content,$currentTypeId,$topic,$vtag);
			$content=$mainClassObj->parseNewsList($content,$currentTypeId,$vtag);
			$content=$mainClassObj->parseTopicList($content);
			$content=$mainClassObj->parseLinkList($content);
	}
	return $content;
}

function echoBegin($str,$pType,$mstart='',$mend='')
{
	switch ($pType){
		case "channel":
			echo "正在开始生成栏目<font color='red'>".$str."</font>".$mstart."到".$mend."页的列表<br>";
		break;
		case "content":
			echo $str;
		break;
	}
}

function echoChannelSuspend($typeIdIndex,$action3,$page=1,$typeId='')
{
	global $cfg_stoptime,$action,$ids;
	@ob_flush();
	@flush();
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextChannel();\",".$cfg_stoptime."000);function makeNextChannel(){location.href='?action=".$action."&index=".$typeIdIndex."&action3=".$action3."&page=".$page."&channel=".$typeId."&ids=".$ids."';}</script>";
}

function echoPartSuspend($typeIdIndex,$action3)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextChannel();\",".$cfg_stoptime."000);function makeNextChannel(){location.href='?action=allpart&index=".$typeIdIndex."&action3=".$action3."';}</script>";
}

function echoContentSuspend($typeIdIndex,$page,$pcount,$channel,$action2,$action3,$makeNoncreate)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=content&index=".$typeIdIndex."&channel=".$channel."&page=".$page."&pcount=".$pcount."&action2=".$action2."&action3=".$action3."&makeNoncreate=".$makeNoncreate."';}</script>";
}

function echoNewsContentSuspend($typeIdIndex,$page,$channel,$action2,$action3)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=newscontent&index=".$typeIdIndex."&channel=".$channel."&page=".$page."&action2=".$action2."&action3=".$action3."';}</script>";
}

function echoContentSuspendPerChannel($typeIdIndex,$action2,$action3,$makeNoncreate)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextType();\",".$cfg_stoptime."000);function makeNextType(){location.href='?action=allcontent&index=".$typeIdIndex."&action2=".$action2."&action3=".$action3."&makenoncreate=".$makeNoncreate."';}</script>";
}

function echoContentSuspendPerPart($typeIdIndex,$action2,$action3)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续生成<script language=\"javascript\">setTimeout(\"makeNextType();\",".$cfg_stoptime."000);function makeNextType(){location.href='?action=allnewscontent&index=".$typeIdIndex."&action2=".$action2."&action3=".$action3."';}</script>";
}

function echoEach($str,$cur,$link,$pType)
{
	switch ($pType){
		case "channel":
			echo " 成功生成栏目<font color='red'>".$str."</font>的第<font color='red'>".$cur."</font>页列表：<a href='".$link."' target='_blank'><font color='red'>".$link."</font></a><br>";
		break;
		case "content":
			return " 成功生成<font color='red'>".$str."</font>的地址：<a href='".$link."' target='_blank'><font color='red'>".$link."</font></a><br>";
		break;
		case "newspage":
			echo " 成功生成栏目<font color='red'>".$str."</font>的第<font color='red'>".$cur."</font>页列表：<a href='".$link."' target='_blank'><font color='red'>".$link."</font></a><br>";
		break;
		case "article":
			echo " 成功生成<font color='red'>".$str."</font>的地址：<a href='".$link."' target='_blank'><font color='red'>".$link."</font></a><br>";
		break;
		case "play":
			return " 成功生成<font color='blue'>".$str."</font>的播放地址：<a href='".$link."' target='_blank'><font color='blue'>".$link."</font></a><br>";
		break;
	}
}

function getTypeIdArrayBySort($topId,$tptype=0)
{
	return explode(",",handleArrayIdStr(getTypeIdStrBySort($topId,$tptype)));
}

function handleArrayIdStr($str)
{
	if (substr($str,0,2) == "0,") return substr($str,2,(strlen($str)-2)); else return $str;
}

function getTypeIdStrBySort($topId,$tptype=0)
{
	return getTypeId($topId,$tptype);
}

function makeCustomInfo($templatename)
{
	global $mainClassObj,$dsql,$customLink;
	$self_str="self_";
	$pcount=0;
	$templatePath = "/templets/".$GLOBALS['cfg_df_style']."/".$GLOBALS['cfg_df_html']."/".$templatename; 
    $customLink="/".$GLOBALS['cfg_cmspath'].str_replace(".html","",str_replace("#", "/", str_replace($self_str,"",$templatename)))."<page>.html";
	$content=loadFile(sea_ROOT.$templatePath);
	$content2=str_replace("}"," }",$content);
	
	
	$pSize=ZgetPagesize($content2,$Flag);
    $order=ZgetPageorder($content2,$Flag);
	$lang=ZgetPagelang($content2,$Flag);
	$type=ZgetPagetype($content2,$Flag);
	$maxpage=ZgetPagemaxpage($content2,$Flag);
	$time=ZgetPagetime($content2,$Flag);
	$area=ZgetPagearea($content2,$Flag);
	$year=ZgetPageyear($content2,$Flag);
	$letter=ZgetPageletter($content2,$Flag);
	$commend=ZgetPagecommend($content2,$Flag);
	$state=ZgetPagestate($content2,$Flag);
	$jq=ZgetPagejq($content2,$Flag);
	if($type=="all"){$type="";}

	
	if(!empty($type)) $whereStr.=" and tid in ($type)";
	if(!empty($year)) $whereStr.=" and v_publishyear='$year'";
	if(!empty($letter)) $whereStr.=" and v_letter='$letter'";
	if(!empty($area)) $whereStr.=" and v_publisharea='$area'";
	if(!empty($lang)) $whereStr.=" and v_lang='$lang'";
	if(!empty($jq)) $whereStr.=" and v_jq like '%$jq%'";
	if(strpos($content, "{/seacms:customvideolist}")>0){
		$sql="select count(*) as dd from sea_data where v_recycled=0".$whereStr;
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
	if(strpos($content, "{/customvideolist}")===false)$pCount=1;
	if(!empty($maxpage)){$pCount=$maxpage;}
	for($i=1;$i<=100;$i++)
	{
		$tmp=$content;
		$tmp=str_replace("{customvideo:page}", $i, str_replace("{customvideopage:page}",$i,$tmp));
		$tmp=$mainClassObj->parseCustomList($pSize,$order,$lang,$type,$maxpage,$time,$area,$year,$letter,$commend,$state,$jq,$tmp, 0, $i, $pCount,$TotalResult, "customvideo");
		$link=getCustomLink($i);
		$dir=str_replace($GLOBALS['cfg_cmspath'],'',$link);
		createTextFile ($tmp,sea_ROOT.$dir);
		echo "自定义文件<font color=red>".$dir."</font>生成完毕 <a target='_blank' href=".$link."><font color=red>浏览页面</font></a><br>";
		if($i>=$pCount)break;
		@ob_flush();
		@flush();
	}
}

function makeBaidu()
{
	global $dsql,$flag,$makenum,$allmakenum;
	if ($flag!=1){
		return "<br><div align=center><b>生成baidu地图</b>： 总输出数量<input type='text' id='allmakenum' value='500'>每页数量<input type='text' id='makenum' value='100'> <input type='button' class='rb1' value='开始生成' onclick=\"javascript:location.href='?action=baidu&flag=1&allmakenum='+$('allmakenum').value+'&makenum='+$('makenum').value\" /></div>";
	}else{
		$stringEcho = '';
		$makenum = empty($makenum) ? 100 : intval($makenum);
		$allmakenum = empty($allmakenum) ? 500 : intval($allmakenum);
		$pagesize = $makenum;
		$pCount = ceil($allmakenum/$pagesize);
		$allcount=getDataCount("all");
		$allpage=ceil($allcount/$pagesize);
		if ($pCount>$allpage) $pCount=$allpage;
		for($i=1;$i<=$pCount;$i++){
			$limitstart = ($i-1) * $pagesize;
			$sql="select d.v_id,d.v_addtime,d.tid,d.v_enname from sea_data d order by d.v_addtime desc limit $limitstart,$pagesize";
			$dsql->SetQuery($sql);
			$dsql->Execute('makeBaidu');
			$baiduStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n
							<urlset>\n";
			while($row=$dsql->GetObject('makeBaidu'))
			{
				$baiduStr .= "<url>\n
							  <loc><![CDATA[".$GLOBALS['cfg_basehost'].getContentLink($row->tid,$row->v_id,"link",date('Y-n',$row->v_addtime),$row->v_enname)."]]></loc>\n
							  <lastmod><![CDATA[".MyDate('Y-m-d',$row->v_addtime)."]]></lastmod>\n
							  <changefreq>always</changefreq>\n
							  <priority>1.0</priority>\n	
							  </url>\n";
			}
			$baiduStr .= "</urlset>\n";
			$baiduStr=$baiduStr;
			if ($i==1) $xmlUrl=""; else $xmlUrl="_".$i;
			createTextFile($baiduStr,sea_ROOT."/xml/baidu".$xmlUrl.".xml");
			$stringEcho .= $GLOBALS['cfg_basehost']."/xml/".$GLOBALS['cfg_cmspath']."baidu".$xmlUrl.".xml"." 生成完毕 <a target='_blank' href='../xml/baidu".$xmlUrl.".xml'><font color=red>浏览</font></a><br>";
			@ob_flush();
			@flush();
			if($i==$pCount){
				$stringEcho .="生成完毕";
				return $stringEcho;
			}
		}
	}
}

function makeGoogle()
{
	global $dsql,$flag,$makenum,$allmakenum;
	if ($flag!=1){
		return "<br><div align=center><b>生成google地图</b>： 总输出数量<input type='text' id='allmakenum' value='500'>每页数量<input type='text' id='makenum' value='100'> <input type='button' class='rb1' value='开始生成' onclick=\"javascript:location.href='?action=google&flag=1&allmakenum='+$('allmakenum').value+'&makenum='+$('makenum').value\" /></div>";
	}else{
		$stringEcho = '';
		$makenum = empty($makenum) ? 100 : intval($makenum);
		$allmakenum = empty($allmakenum) ? 500 : intval($allmakenum);
		$pagesize = $makenum;
		$pCount = ceil($allmakenum/$pagesize);
		$allcount=getDataCount("all");
		$allpage=ceil($allcount/$pagesize);
		if ($pCount>$allpage) $pCount=$allpage;
		for($i=1;$i<=$pCount;$i++){
			$limitstart = ($i-1) * $pagesize;
			$sql="select v_id,v_addtime,v_enname,tid from sea_data order by v_addtime desc limit $limitstart,$pagesize";
			$dsql->SetQuery($sql);
			$dsql->Execute('makeGoogle');
			$googleStr =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
							<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n
							<url>\n
							<loc>".$GLOBALS['cfg_basehost']."</loc>\n
							<lastmod>".MyDate('Y-m-d',time())."</lastmod>\n
							<changefreq>hourly</changefreq>\n
							<priority>1.0</priority>\n
							</url>\n";
			while($row=$dsql->GetObject('makeGoogle'))
			{
				$vDes = empty($row->v_des) ? "" : $row->v_des;
				$vName = empty($row->v_name) ? "" : $row->v_name;
				$googleStr .= "<url>\n
										  <loc>".$GLOBALS['cfg_basehost'].getContentLink($row->tid,$row->v_id,"link",date('Y-n',$row->v_addtime),$row->v_enname)."</loc>\n
										  <lastmod>".MyDate('Y-m-d',$row->v_addtime)."</lastmod>\n
										  <changefreq>daily</changefreq>\n
										  <priority>1.0</priority>\n
										  </url>\n";
			}
			$googleStr .= "</urlset>\n";
			$googleStr=$googleStr;
			if ($i==1) $xmlUrl=""; else $xmlUrl="_".$i;
			createTextFile($googleStr,sea_ROOT."/xml/google".$xmlUrl.".xml");
			$stringEcho .= $GLOBALS['cfg_basehost']."/xml/".$GLOBALS['cfg_cmspath']."google".$xmlUrl.".xml"." 生成完毕 <a target='_blank' href='../xml/google".$xmlUrl.".xml'><font color=red>浏览</font></a><br>";
			@ob_flush();
			@flush();
			if($i==$pCount){
				$stringEcho .= "生成完毕";
				return $stringEcho;
			}
		}
	}
}

function makeRss()
{
	require_once(sea_INC.'/charset.func.php');
	global $dsql,$flag,$makenum,$allmakenum;
	if ($flag!=1){
		return "<br><div align=center><b>生成RSS地图</b>： 输出数量<input type='text' id='makenum' value='100'> <input type='button' class='rb1' value='开始生成' onclick=\"javascript:location.href='?action=rss&flag=1&makenum='+$('makenum').value\" /></div>";
	}else{
		$makenum = empty($makenum) ? 100 : intval($makenum);
		$sql="select d.v_id,d.v_name,d.v_pic,d.v_actor,d.v_addtime,d.v_enname,d.tid,c.body as v_des from sea_data d left join sea_content c on c.v_id=d.v_id order by d.v_addtime desc limit 0,$makenum";
		$dsql->SetQuery($sql);
		$dsql->Execute('makeRss');
		$rssStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n
					<rss version='2.0'>\n
					<channel>\n
					<title><![CDATA[".$GLOBALS['cfg_webname']."]]></title>\n
					<description><![CDATA[".$GLOBALS['cfg_description']."]]></description>\n
					<link>".$GLOBALS['cfg_basehost']."</link>\n
					<language>zh-cn</language>\n
					<docs>".$GLOBALS['cfg_webname']."</docs>\n
					<generator>Rss Powered By ".str_replace("http://","",$GLOBALS['cfg_basehost'])."</generator>\n
					<image>\n
						<url>".$GLOBALS['cfg_basehost']."/pic/logo.gif</url>\n
					</image>\n";
		while($row=$dsql->GetObject('makeRss'))
		{
			$vDes = empty($row->v_des) ? "" : $row->v_des;
			$vName = empty($row->v_name) ? "" : $row->v_name;
			$rssStr .= "<item>\n
							<title><![CDATA[".$vName."]]></title>\n
							<link>".$GLOBALS['cfg_basehost'].getContentLink($row->tid,$row->v_id,"link",date('Y-n',$row->v_addtime),$row->v_enname)."</link>\n
							<author><![CDATA[".$row->v_actor."]]></author>\n
							<pubDate>".MyDate('Y-m-d H:i:s',$row->v_addtime)."</pubDate>\n
							<description><![CDATA[".msubstr(html2text($vDes),0,300,'utf-8',false)."]]></description>\n	
						   </item>\n";
		}
		$rssStr .= "</channel></rss>\n";
		createTextFile($rssStr,sea_ROOT."/xml/rss.xml");
		return $GLOBALS['cfg_basehost']."/xml/".$GLOBALS['cfg_cmspath']."rss.xml"." 生成完毕 <a target='_blank' href='../xml/rss.xml'><font color=red>浏览</font></a><br>";
	}
}


//生成百度站内搜索数据

function makeBaidux()
{
	global $dsql,$flag,$makenum,$allmakenum;
	if ($flag!=1){
		return "<br><div align=center><b>生成百度站内搜索数据</b>： 总输出数量<input type='text' id='allmakenum' value='10000'>每页数量<input type='text' id='makenum' value='2000'> <input type='button' class='rb1' value='开始生成' onclick=\"javascript:location.href='?action=baidux&flag=1&allmakenum='+$('allmakenum').value+'&makenum='+$('makenum').value\" /></div>";
	}else{
		$sqlt="select tid,tname from sea_type";
		$dsql->SetQuery($sqlt);
		$dsql->Execute('ztype');
		while($rowt=$dsql->GetObject('ztype'))
		{
			$t[$rowt->tid]="$rowt->tname";
		}
			
		$stringEcho = '';
		$makenum = empty($makenum) ? 1000 : intval($makenum);
		$allmakenum = empty($allmakenum) ? 10000 : intval($allmakenum);
		$pagesize = $makenum;
		$pCount = ceil($allmakenum/$pagesize);
		$allcount=getDataCount("all");
		$allpage=ceil($allcount/$pagesize);
		if ($pCount>$allpage) $pCount=$allpage;
		for($i=1;$i<=$pCount;$i++){
			$limitstart = ($i-1) * $pagesize;
			$sql="select a.*,b.* from sea_data as a,sea_content as b WHERE a.v_id=b.v_id order by a.v_id desc limit $limitstart,$pagesize";
			$dsql->SetQuery($sql);
			$dsql->Execute('makeBaidux');
$baiduxStr =  "<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<urlset>\n";
while($row=$dsql->GetObject('makeBaidux'))
{
if(strpos($row->v_pic,"http")=== false)
{$v_pic=$GLOBALS['cfg_basehost']."/".$row->v_pic;}
else
{$v_pic=$row->v_pic;}
if($row->v_publishyear=="" or $row->v_publishyear==0)
{$row->v_publishyear=2015;}
$row->v_publishyear =intval($row->v_publishyear);
$baiduxStr .= "<url>
<loc>".$GLOBALS['cfg_basehost'].getContentLink($row->tid,$row->v_id,"link",date('Y-n',$row->v_addtime),$row->v_enname)."</loc>
<lastmod>".date('Y-m-d',$row->v_addtime)."T".date('H:i:s',$row->v_addtime)."</lastmod>
<changefreq>always</changefreq>
<priority>1.0</priority>
<data>
<display>
<name>".str_replace("&","",strip_tags($row->v_name))."</name>
<image>".$v_pic."</image>
<description>".str_replace("&","",strip_tags(cn_substr_utf8($row->body,200,0)))."</description>
<genre>".$t[$row->tid]."</genre>
<actor><name>".str_replace("&","",strip_tags($row->v_actor))."</name></actor>
<director><name>".str_replace("&","",strip_tags($row->v_director))."</name></director>
<inLanguage>>".str_replace("&","",strip_tags($row->v_lang))."</inLanguage>
<contentLocation>".str_replace("&","",strip_tags($row->v_publisharea))."</contentLocation>
<premiere>
<datePublished>".$row->v_publishyear."-".rand(10,12)."-".rand(10,30)."</datePublished>
</premiere>
<aggregateRating>
<ratingValue>".rand(1,9).".0</ratingValue>
<bestRating>10</bestRating>
</aggregateRating>
</display>
</data>
</url>\n";
}
$baiduxStr .= "</urlset>\n";
			$baiduxStr=$baiduxStr;
			if ($i==1) $xmlUrl=""; else $xmlUrl="_".$i;
			createTextFile($baiduxStr,sea_ROOT."/xml/baidux".$xmlUrl.".xml");
			$stringEcho .= $GLOBALS['cfg_basehost']."/xml/".$GLOBALS['cfg_cmspath']."baidux".$xmlUrl.".xml"." 生成完毕 <a target='_blank' href='../xml/baidux".$xmlUrl.".xml'><font color=red>浏览</font></a><br>";
			@ob_flush();
			@flush();
			if($i==$pCount){
				$stringEcho .="生成完毕";
				return $stringEcho;
			}
		}
	}
}

