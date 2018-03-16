<?php 
//获取视频首页链接
function getIndexLink()
{
	switch ($GLOBALS['cfg_runmode2'])
	{
		case 0:
		case 1:
			$linkStr="/".$GLOBALS['cfg_cmspath'];
			break;
		case 2:
			$linkStr="/".$GLOBALS['cfg_cmspath']."index".$GLOBALS['cfg_filesuffix2'];
			break;	
		default:break;		
	}
	return $linkStr;
}

//获取新闻首页链接
function getnewsxLink()
{
	switch ($GLOBALS['cfg_runmode2'])
	{
		case 0:
			$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name'].".html";break;
		case 1:
			$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name']."/";break;
		case 2:
			$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name'].$GLOBALS['cfg_filesuffix3'];break;
		default:break;
	}
	return $linkStr;
}

//获取视频分类链接
function getChannelPagesLink($typeId,$page=1,$linktype='')
{
	if($GLOBALS['cfg_runmode']=='0')
	{
		switch($GLOBALS['cfg_makemode'])
		{
			case "dir1":
			case "dir3":
			case "dir5":
			case "dir7":
				$typePath=getTypePathOnCache($typeId);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath;
				if (intval($page)==1) $page="";
				$linkStr.=$GLOBALS['cfg_channelpage_name2'].$page.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir2":
			case "dir4":
				if (intval($page)==1) $page="";
				else $page='_'.$page;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_channel_name2']."/".$GLOBALS['cfg_channelpage_name2'].$typeId.$page.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir6":
			case "dir8":
				if (intval($page)==1) $page="";
				$linkStr='/'.$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_channel_name2']."/".$GLOBALS['cfg_channelpage_name2'].getTypeEnNameOnCache($typeId).$page.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir9":
				$typePath=getTypePathOnCache($typeId,true);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath;
				if (intval($page)==1) $page="";
				$linkStr.=$GLOBALS['cfg_channelpage_name2'].$page.$GLOBALS['cfg_filesuffix2'];
				break;
			
		}
		
	}
 	elseif($GLOBALS['cfg_runmode']=='1')
	{
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_channel_name']."/?";
		if($GLOBALS['cfg_paramset']){
		if (intval($page)==1) $tempStr=""; else $tempStr="&".$GLOBALS['cfg_parampage'].'='.$page;
		$linkStr.=$GLOBALS['cfg_paramid'].'='.$typeId.$tempStr;	
		}else{
		if (intval($page)==1) $tempStr=""; else $tempStr="-".$page;
		$linkStr.=$typeId.$tempStr.$GLOBALS['cfg_filesuffix2'];
		}
	}
	elseif($GLOBALS['cfg_runmode']=='2')
	{
		if (intval($page)==1) $tempStr=""; else $tempStr="-".$page;
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_channel_name3']."/".$GLOBALS['cfg_channelpage_name3'].$typeId.$tempStr.$GLOBALS['cfg_filesuffix2'];
	}
	return $linkStr;
}

//获取新闻分类链接
function getnewspageLink($typeId,$page=1)
{
	if($GLOBALS['cfg_runmode2']=='0')
	{
		switch($GLOBALS['cfg_makemode2'])
		{
			case "dir1":
			case "dir3":
			case "dir5":
			case "dir7":
				$typePath=getTypePathOnCache($typeId,false,1);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2'].'/'.$typePath;
				if (intval($page)==1) $page="";
				$linkStr.=$GLOBALS['cfg_newspartpage_name2'].$page.$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir2":
			case "dir4":
				if (intval($page)==1) $page="";
				else $page="_".$page;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_newspart_name2'].'/'.$GLOBALS['cfg_newspartpage_name2'].$typeId.$page.$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir6":
			case "dir8":
				$tpenname=getNewsTypeEnNameOnCache($typeId);
				if(empty($tpenname))$tpenname=Pinyin(getNewsTypeNameOnCache($typeId));
				if (intval($page)==1) $page="";
				else $page="_".$page;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_newspart_name2'].'/'.$GLOBALS['cfg_newspartpage_name2'].$tpenname.$page.$GLOBALS['cfg_filesuffix3'];
				break;
		}
	}
	elseif($GLOBALS['cfg_runmode2']=='1')
	{
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_newspart_name']."/?";
		if($GLOBALS['cfg_newsparamset']){
			if (intval($page)==1) $tempStr=""; else $tempStr='&'.$GLOBALS['cfg_parampage'].'='.$page;
			$linkStr.=$GLOBALS['cfg_newsparamid']."=".$typeId.$tempStr;
		}
		else{
			if (intval($page)==1) $tempStr=""; else $tempStr="-".$page;
			$linkStr.=$typeId.$tempStr.$GLOBALS['cfg_filesuffix3'];
		}
	}
	elseif($GLOBALS['cfg_runmode2']=='2')
	{
		if (intval($page)==1) $tempStr=""; else $tempStr="-".$page;
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name3']."/".$GLOBALS['cfg_newspart_name3'].'/'.$GLOBALS['cfg_newspartpage_name3'].$typeId.$tempStr.$GLOBALS['cfg_filesuffix3'];
	}
	return $linkStr;
}

//获取详情链接
function getContentLink($typeId,$videoId,$linkType,$sDate,$videoenname)
{
	global $cfg_createid;
	if($GLOBALS['cfg_runmode']=='0')
	{
		
		switch($GLOBALS['cfg_makemode'])
		{
			case "dir1":
				$typePath=getTypePathOnCache($typeId);
				if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				if($linkType!='link') $linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$videoId.'/'.$GLOBALS['cfg_contentpage_name2'].$GLOBALS['cfg_filesuffix2'];
				else $linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$videoId.'/'; 
				break;
			case "dir2":
				if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				else $videoId=$GLOBALS['cfg_contentpage_name2'].$videoId;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name2']."/".$videoId.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir3":
				$typePath=getTypePathOnCache($typeId);
				if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$sDate.'/'.$videoId.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir4":
				if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				else $videoId=$GLOBALS['cfg_contentpage_name2'].$videoId;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name2']."/".$sDate."/".$videoId.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir5":
				$typePath=getTypePathOnCache($typeId);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$videoenname.'/';
				if($linkType!='link')$linkStr.=$GLOBALS['cfg_contentpage_name2'].$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir6":
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name2']."/".$GLOBALS['cfg_contentpage_name2'].$videoenname.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir7":
				$typePath=getTypePathOnCache($typeId);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$sDate.'/'.$videoenname.$GLOBALS['cfg_filesuffix2'];
				break;
			case "dir8":
				$typePath=getTypePathOnCache($typeId);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name2']."/".$sDate.'/'.$GLOBALS['cfg_contentpage_name2'].$videoenname.$GLOBALS['cfg_filesuffix2'];
				break;		
			case "dir9":
				$typePath=getTypePathOnCache($typeId,true);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$videoenname.'/';
				if($linkType!='link')$linkStr.=$GLOBALS['cfg_contentpage_name2'].$GLOBALS['cfg_filesuffix2'];
				break;	
		}
	}
	elseif($GLOBALS['cfg_runmode']=='1')
	{
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name']."/?";
		if($GLOBALS['cfg_paramset'])$linkStr.=$GLOBALS['cfg_paramid'].'='.$videoId;
		else $linkStr.=$videoId.$GLOBALS['cfg_filesuffix2'];
	}
	elseif($GLOBALS['cfg_runmode']=='2')
	{
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_content_name3']."/".$GLOBALS['cfg_contentpage_name3'].$videoId.$GLOBALS['cfg_filesuffix2'];
	}
	return $linkStr;
}

//获取新闻详情链接
function getArticleLink($typeId,$videoId,$linkType,$page=1)
{
	global $cfg_newscreateid;
	if($GLOBALS['cfg_runmode2']==0)
	{
		switch ($GLOBALS['cfg_makemode2'])
		{
			case "dir1":
				$typePath=getTypePathOnCache($typeId,false,1);
				if($cfg_newscreateid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2'].'/'.$typePath.$videoId.'/';
				if($linkType!='link'||$page!=1) $linkStr.=$GLOBALS['cfg_articlepage_name2'].($page==1?'':$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir3":
				$typePath=getTypePathOnCache($typeId,false,1);
				$sDate = date('Y-n',getNewsSdate($videoId));
				if($cfg_newscreateid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2'].'/'.$typePath.$sDate.'/'.$videoId.($page==1?'':'-'.$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir5":
				$typePath=getTypePathOnCache($typeId,false,1);
				$newsenname = getNewsEnname($videoId);
				$newsenname = empty($newsenname)?Pinyin(getNewsTitle($videoId)):$newsenname;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2'].'/'.$typePath.$newsenname.'/';
				if($linkType!='link'||$page!=1)$linkStr.=$GLOBALS['cfg_articlepage_name2'].($page==1?'':$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir7":
				$newsenname = getNewsEnname($videoId);
				$newsenname = empty($newsenname)?Pinyin(getNewsTitle($videoId)):$newsenname;
				$typePath=getTypePathOnCache($typeId,false,1);
				$sDate = date('Y-n',getNewsSdate($videoId));
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2'].'/'.$typePath.$sDate.'/'.$newsenname.($page==1?'':$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir2":
				if($cfg_newscreateid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				else $videoId=$GLOBALS['cfg_articlepage_name2'].$videoId;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_article_name2']."/".$videoId.($page==1?'':'-'.$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir4":
				$sDate = date('Y-n',getNewsSdate($videoId));
				if($cfg_newscreateid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
				else $videoId=$GLOBALS['cfg_articlepage_name2'].$videoId;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_article_name2']."/".$sDate."/".$videoId.($page==1?'':'-'.$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir6":
				$newsenname = getNewsEnname($videoId);
				$newsenname = empty($newsenname)?Pinyin(getNewsTitle($videoId)):$newsenname;
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_article_name2']."/".$GLOBALS['cfg_articlepage_name2'].$newsenname.($page==1?'':$page).$GLOBALS['cfg_filesuffix3'];
				break;
			case "dir8":
				$sDate = date('Y-n',getNewsSdate($videoId));
				$newsenname = getNewsEnname($videoId);
				$newsenname = empty($newsenname)?Pinyin(getNewsTitle($videoId)):$newsenname;
				$typePath=getTypePathOnCache($typeId,false,1);
				$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name2a'].'/'.$GLOBALS['cfg_article_name2']."/".$sDate.'/'.$GLOBALS['cfg_articlepage_name2'].$newsenname.($page==1?'':$page).$GLOBALS['cfg_filesuffix3'];
				break;
		}
		return $linkStr;
	}elseif($GLOBALS['cfg_runmode2']==1){
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_article_name']."/?";
		if($GLOBALS['cfg_newsparamset'])
		$linkStr.=$GLOBALS['cfg_newsparamid'].'='.$videoId.($page==1?'':'&'.$GLOBALS['cfg_newsparampage'].'='.$page);
		else $linkStr.=$videoId.($page==1?'':'-'.$page).$GLOBALS['cfg_filesuffix3'];
		return $linkStr;
	}elseif($GLOBALS['cfg_runmode2']==2){
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_news_name3'].'/'.$GLOBALS['cfg_article_name3']."/".$GLOBALS['cfg_articlepage_name3'].$videoId.($page==1?'':'-'.$page).$GLOBALS['cfg_filesuffix3'];
		return $linkStr;
	}
	}

//获取视频播放链接
function getPlayLink($typeId,$videoId,$sDate,$enname,$filesuffix='')
{
	global $cfg_createid;
	$runmode=$GLOBALS['cfg_runmode'];
	$filesuffix=empty($filesuffix)?$GLOBALS['cfg_filesuffix2']:$filesuffix;
	if(!$GLOBALS['cfg_ismakeplay']&&$runmode==0)$runmode=1;
	switch ($runmode){
		case 0:
			switch($GLOBALS['cfg_makemode'])
			{
				case "dir1":
					$typePath=getTypePathOnCache($typeId);
					if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
					$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$videoId."/".$GLOBALS['cfg_playpage_name2a'].$filesuffix;  //播放的连接 
					break;
				case "dir2":
					if($cfg_createid=='md5') $linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_playpage_name2'].'/'.substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8)).$filesuffix;
					else $linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name2'].'/'.$GLOBALS['cfg_playpage_name2'].$videoId.$filesuffix;
					break;
				case "dir3":
					$typePath=getTypePathOnCache($typeId);
					if($cfg_createid=='md5') $videoId=substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8));
					$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$sDate."/".$videoId."/".$GLOBALS['cfg_playpage_name2a'].$filesuffix;
					break;
				case "dir4":
					if($cfg_createid=='md5') $linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_playpage_name2'].'/'.$sDate.'/'.substr(md5($videoId),8,8).strtoupper(substr(md5($videoId),16,8)).$filesuffix;
					else $linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name2'].'/'.$sDate.'/'.$GLOBALS['cfg_playpage_name2'].$videoId.$filesuffix;
					break;
				case "dir5":
					$typePath=getTypePathOnCache($typeId);
					$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$enname."/".$GLOBALS['cfg_playpage_name2a'].$filesuffix;
					break;
				case "dir6":
					$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name2'].'/'.$enname.'/'.$GLOBALS['cfg_playpage_name2'].$filesuffix;
					break;
				case "dir7":
					$typePath=getTypePathOnCache($typeId);
					$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$sDate."/".$enname."/".$GLOBALS['cfg_playpage_name2a'].$filesuffix;
					break;	
				case "dir8":
					$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name2'].'/'.$sDate.'/'.$enname.'/'.$GLOBALS['cfg_playpage_name2'].$filesuffix;
					break;	
				case "dir9":
					$typePath=getTypePathOnCache($typeId,true);
					$linkStr="/".$GLOBALS['cfg_cmspath'].$typePath.$enname."/".$GLOBALS['cfg_playpage_name2a'].$filesuffix;
					break;	
			}
		break;
		case 1:
			$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name']."/";
		break;
		case 2:
			$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_play_name3']."/";
		break;
	}
	return $linkStr;
}

//真正视频播放链接
function getPlayLink2($typeId,$vId,$sDate,$enname,$from=0,$index=0)
{
	if($GLOBALS['cfg_runmode']=='0' && $GLOBALS['cfg_ismakeplay']=='1')
	{
		$getPlayLink2 = "-".$from."-".$index.$GLOBALS['cfg_filesuffix2'];
		$getPlayLink2 = getPlayLink($typeId, $vId,$sDate,$enname,$getPlayLink2);
	}elseif($GLOBALS['cfg_runmode']=='2')
	{
		$getPlayLink2=getPlayLink($typeId,$vId,$sDate,$enname).$vId."-".$from."-".$index.$GLOBALS['cfg_filesuffix2'];
	}else{
		$getPlayLink2=getPlayLink($typeId,$vId,$sDate,$enname);
		if($GLOBALS['cfg_paramset']==0)$getPlayLink2.="?".$vId."-".$from."-".$index;
		else $getPlayLink2.="?".$GLOBALS['cfg_paramid'].'='.$vId.'&'.$GLOBALS['cfg_parampage'].'='.$from.'&'.$GLOBALS['cfg_paramindex'].'='.$index;
		if($GLOBALS['cfg_runmode']!='0'&&$GLOBALS['cfg_paramset']!=1||($GLOBALS['cfg_runmode']=='0'&&$GLOBALS['cfg_ismakeplay']=='0'&&$GLOBALS['cfg_paramset']!=1))$getPlayLink2.=$GLOBALS['cfg_filesuffix2'];
	}
	return $getPlayLink2;
}

//获取专题链接
function getTopicIndexLink($page)
{
	if($GLOBALS['cfg_runmode']=='1')
	{
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_album_name']."/";
		$pageStr='?'.$page.$GLOBALS['cfg_filesuffix2'];
		$linkStr.=$pageStr;
	}
	elseif($GLOBALS['cfg_runmode']=='0')
	{
		$pageStr=$page!=1?$page:'';
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_album_name']."/index".$pageStr.$GLOBALS['cfg_filesuffix2'];
	}
	elseif($GLOBALS['cfg_runmode']=='2')
	{
		$pageStr=$page!=1?$page:'';
		$linkStr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_album_name']."/index".$pageStr.$GLOBALS['cfg_filesuffix2'];
	}
	return $linkStr;
}

//获取专题页链接格式
function getTopicPageLinkStyle()
{
	if($GLOBALS['cfg_runmode']=='0') return 6;
	if($GLOBALS['cfg_runmode']=='1') return 1;
	if($GLOBALS['cfg_runmode']=='2') return 5;
}

//获取专题首页链接格式
function getTopicIndexLinkStyle()
{
	if($GLOBALS['cfg_runmode']=='0') return 6;
	if($GLOBALS['cfg_runmode']=='1') return 7;
	if($GLOBALS['cfg_runmode']=='2') return 5;
}

function getChannellinkStr()
{
	if($GLOBALS['cfg_runmode']=='0')
	{
		switch($GLOBALS['cfg_makemode'])
		{
			case "dir1":
				$pageUrlStyle=2;
				break;
			case "dir2":
				$pageUrlStyle=3;
				break;
			case "dir3":
				$pageUrlStyle=2;
				break;
			case "dir4":
				$pageUrlStyle=3;			
				break;
			case "dir5":
				$pageUrlStyle=2;
				break;
			case "dir6":
				$pageUrlStyle=3;			
				break;
			case "dir7":
				$pageUrlStyle=2;			
				break;
			
		}


	}
	elseif($GLOBALS['cfg_runmode']=='1')
	{
		$pageUrlStyle=1;
	}
	elseif($GLOBALS['cfg_runmode']=='2')
	{
		$pageUrlStyle=5;
	}
	return $pageUrlStyle;
}

function getfileSuffix()
{
	return $GLOBALS['cfg_filesuffix2'];
}

function getnewsfileSuffix()
{
	return $GLOBALS['cfg_filesuffix3'];
}

function getTopicLink($topicEnname,$i)
{
	if($i==1){
		if($GLOBALS['cfg_runmode']=='0') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname.$GLOBALS['cfg_filesuffix2'];
		if($GLOBALS['cfg_runmode']=='1') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname.$GLOBALS['cfg_filesuffix2'];
		if($GLOBALS['cfg_runmode']=='2') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname.$GLOBALS['cfg_filesuffix2'];
	}else{
		if($GLOBALS['cfg_runmode']=='0') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname."-".$i.$GLOBALS['cfg_filesuffix2'];
		if($GLOBALS['cfg_runmode']=='1') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname."-".$i.$GLOBALS['cfg_filesuffix2'];
		if($GLOBALS['cfg_runmode']=='2') $linkstr="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$topicEnname."-".$i.$GLOBALS['cfg_filesuffix2'];
	}
	return $linkstr;
}

function gettaglink($tag,$page=1)
{
	global $cfg_runmode;
	if($page==1){
		if ($cfg_runmode==2) {
			return "/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_tags_name3']."/".urlencode($tag).$GLOBALS['cfg_filesuffix2'];
		} else {
			return "/".$GLOBALS['cfg_cmspath'].'tag.php?tag='.urlencode($tag);
		}
	}else{
		if ($cfg_runmode==2) {
			return "/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_tags_name3']."/".urlencode($tag)."_".$page.$GLOBALS['cfg_filesuffix2'];
		} else {
			return "/".$GLOBALS['cfg_cmspath'].'tag.php?tag='.urlencode($tag)."&page=".$page;
		}
	}
}

function getCascadeLink($str,$value,$tid)
{
	$tid=$GLOBALS[tid];
	switch(trim($str))
	{
		case "type":
		case "sectype":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($value);
		break;
		case "year":					
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&year=".urlencode($value);
		break;
		case "area":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&area=".urlencode($value);
		break;
		case "letter":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&letter=".urlencode($value);
		break;
		case "lang":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&yuyan=".urlencode($value);
		break;
		case "jq":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&jq=".urlencode($value);
		break;
		case "state":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&state=".urlencode($value);
		break;
		case "ver":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&ver=".urlencode($value);
		break;
		case "money":
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?searchtype=5&tid=".urlencode($tid)."&money=".urlencode($value);
		break;
	}
	return $Link;
}

function getItemLink($str,$value,$schwhere)
{
	$schwhere = preg_replace("/\&?page\=[^\&]*/i","",$schwhere);
	switch(trim($str))
	{
		case "type":
		case "sectype":
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?tid\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?tid\=[^\&]*/i","",$schwhere)."&tid=".urlencode($value);
		break;
		case "year":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?year\=[^\&]*/i","",$schwhere);
			else				
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?year\=[^\&]*/i","",$schwhere)."&year=".urlencode($value);
		break;
		case "area":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?area\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?area\=[^\&]*/i","",$schwhere)."&area=".urlencode($value);
		break;
		case "letter":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?letter\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?letter\=[^\&]*/i","",$schwhere)."&letter=".urlencode($value);
		break;
		case "lang":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?yuyan\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?yuyan\=[^\&]*/i","",$schwhere)."&yuyan=".urlencode($value);
		break;
		case "jq":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?jq\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?jq\=[^\&]*/i","",$schwhere)."&jq=".urlencode($value);
		break;
		case "ver":	
			if($value=="全部")
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?ver\=[^\&]*/i","",$schwhere);
			else
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?ver\=[^\&]*/i","",$schwhere)."&ver=".urlencode($value);
		break;
		case "state":	
			if($value=="全部")
			{
				$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?state\=[^\&]*/i","",$schwhere);
			}
			elseif($value=="完结")
			{
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?state\=[^\&]*/i","",$schwhere)."&state=w";
			}
			else
			{
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?state\=[^\&]*/i","",$schwhere)."&state=l";
			}
		break;
		case "money":	
			if($value=="全部")
			{
				$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?money\=[^\&]*/i","",$schwhere);
			}
			elseif($value=="免费")
			{
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?money\=[^\&]*/i","",$schwhere)."&money=m";
			}
			else
			{
			$Link="/".$GLOBALS['cfg_cmspath']."search.php?".preg_replace("/\&?money\=[^\&]*/i","",$schwhere)."&money=s";
			}
		break;
	}
	return $Link;
}