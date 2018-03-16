<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_INC.'/collection.func.php');
AjaxHead();
if(empty($action))
{
	$action = '';
}

if($action==''){
	echo '';
}else if($action=='gather'){
	if(empty($url))
	{
		echo '';
	}else if(strpos($url,"youku.com")>0)
	{
		if(strpos($url,"playlist_show")>0){
			$youkuRss = "http://www.youku.com/playlist/rss/id/".gsubStr($url,"id_",".html");
			$pageStr = get($youkuRss);
			preg_match_all("/\<item\>(.*?)\<\/item>/s",$pageStr,$itemblocks);
				foreach($itemblocks[1] as $block)
				{
					preg_match_all( "/\<title\>(.*?)\<\/title\>/",$block,$title);
					preg_match_all( "/\<guid\>(\d{3,}?)\<\/guid\>/",$block,$guid);
					if(empty($title[1][0])||strpos($result,$guid[1][0]))continue;
					$result = $result.$title[1][0].'$'.$guid[1][0].'$youku'."\r\n";
				}
			$result=rtrim($result,"\r\n");
			echo $result;
		}elseif(strpos($url,"show_page")>0){
			for($i=1;$i<=50;$i++){
				$youkuurl = "http://www.youku.com/show_eplist/showid_".gsubStr($url,"id_",".html")."_page_".$i.".html";
				$pageStr = get($youkuurl);
				if(empty($pageStr) || strpos($pageStr,"暂无剧集"))
				{
					echo $result;
					exit;
				}
				preg_match_all("/href=\"http:\/\/v.youku.com\/v_show\/id_(.*?).html\" target=\"video\"\>(.*?)\<\/a\>/",$pageStr,$temparr);
				$j=count($temparr[1]);
				for($k=0;$k<$j;$k++){
					if(empty($temparr[2][$k])||strpos($result,$temparr[1][$k]))continue;
					$result .= $temparr[2][$k].'$'.$temparr[1][$k].'$youku'."\r\n";
				}
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			$pageStr = get($url);
			preg_match_all("/\<meta name=\"title\" content=\"(.*?)\"\>/",$pageStr,$title);
			preg_match_all("/var videoId = '(\d{3,}?)'/",$pageStr,$guid);
			$result = $result.$title[1][0].'$'.$guid[1][0].'$youku';
			echo $result;
		}
	}else if(strpos($url,"www.tudou.com")>0)
	{
		echo gatherTuDou($url);
	}else if(strpos($url,"www.56.com")>0)
	{
		if(strpos($url,"/album")>0){
			$substring1 = gsubStr($url,"http://","album");
			$vid=gsubStr($url,"-aid-",".");
			$playurl = "http://".$substring1."album_v2/album_videolist.phtml?callback=albumV2.callback&aid=".$vid."&o=0";
			$pageStr = get($playurl);
			preg_match_all("/\"video_id\":\"(.*?)\",\"video_title\":\"(.*?)\"/is",$pageStr,$matches);
			$matcheslen=count($matches[1]);
			for($j=0;$j<$matcheslen;$j++)
			{	
				$id=$matches[1][$j];
				$title=$matches[2][$j];
				$title=trim($title);
				if(empty($title)||strpos($result,$id))continue;
				$result = $result.$title.'$'.$id.'$hd_56'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}elseif(strpos($url,"/play_album-")>0){
			$allstr=get($url);
			$paras=gsubstr($allstr,"img_host=","'");
			$title=trim(gsubstr($allstr,"tit=","&"));
			$id=gsubstr($allstr,"&vid=","&");
			$result=$result.$title.'$'.$id.'$hd_56'."\r\n";
			echo $result;
		}
		else{
			$allstr=get($url);
			$title=trim(gsubstr($allstr,"\"Subject\":\"","\""));
			$title=decodeSohu($title);
			$id=gsubstr($url,"/v_",".html");
			if(!empty($id))
				$result=$title.'$'.$id.'$hd_56';
			else 
				$id=gsubstr($allstr,"var _oFlv_o = '","'");
				$result=$title.'$'.de56($id).'$hd_56';
			echo $result;
		}
	}else if(strpos($url,"v.ku6.com")>0)
	{
		if(strpos($url,"playlist/")>0)
		{
		$playlistid = gsubStr($url."/","program/","/");
		gatKu6List($playlistid,1);
		}else{ 
		$pagestr=get($url);
		if(!empty($pagestr))
		{
		preg_match_all("/,\s*id:\s*\"(.*?)\",\s*uid/is", $pagestr, $ids);
		$id=$ids[1][0];
		$title=gsubStr($pagestr,"<title>","</title>");
		$title=substr($title,0,strpos($title," 在线观看"));
		$result=$title.'$'.$id.'$ku6';
		echo $result;
		}
		else 
		{
		echo '';
		}
		}
	}else if(strpos($url,"video.sina.com.cn")>0)
	{
		$url= str_replace("/playlist/","/a/", $url);
//		if(strpos($url,"/movie/detail/")>0){
//			gatherSinaHd($url)
//		}
		if(strpos($url,"/m/")>0)
			$result=gatherSinaHdPlay($url);
		elseif(strpos($url,"/a/")>0){
			$paraArray = explode("-",gsubStr($url,"/a/",".html"));
			$url1 = "http://you.video.sina.com.cn/api/catevideoList.php?uid=".$paraArray[1]."&tid=".$paraArray[0];
			$pageStr = substr(get($url1),0,40);
			preg_match_all("/count\":\"(\d+)\"/",$pageStr,$allVideos);
			$url2=$url1."&pagesize=".$allVideos[1][0];
			$pageStr = get($url2);
			preg_match_all("/\"vid\":\"(\d+)\",\"uid\":\"(.*?)\",\"nick\":\"(.*?)\",\"name\":\"(.*?)\"/",$pageStr,$matches);
			$matcheslen=count($matches[1]);
			for($j=0;$j<$matcheslen;$j++)
			{
				if(empty($matches[4][$j])||strpos($result,$matches[2][$j].'&uid='.$matches[1][$j]))continue;
				$result = $result.decodeSohu($matches[4][$j]).'$'.$matches[2][$j].'&uid='.$matches[1][$j].'$iask'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}
		elseif(strpos($url,"/v/b/")>0)
		{
			$paraArray = explode("-",gsubStr($url,"/v/b/",".html"));
			$allStr = gsubStr(get($url),"<div id=\"Interfix\">","</ul>");
			preg_match_all("@<a class=\"plicon\" href=\"/v/b/(.*?)\.html\"@is", $allStr,$matches);
			preg_match_all("/alt=\"(.*?)\"/is", $allStr,$matches1);
			foreach ($matches[1] as $k=>$match)
			{
				$id = str_replace("-", "&uid=", $match);
				if(empty($matches[1][$j])||strpos($result,$id))continue;
				$result = $result.$matches1[1][$k]."$".$id.'$iask'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}
		elseif(strpos($url,"/pg/")>0)
		{
		$allStr=get($url);
		$pages=gsubStr($allStr, "var scope", "}");
		$pages=gsubStr($pages, "\"\$tid\":\"", "\"")."&uid=".gsubStr($str, "\"\$uid\":\"", "\"");
		if($pages!="&uid="){
			$url="http://you.video.sina.com.cn/api/catevideoList.php?tid=".$pages."&page=1&pagesize=1000";
			$allStr=get($url);
			preg_match_all("/\"vid\":\"(\d+?)\",\"uid\":\"(\d+?)\",\"nick\":\".+?\",\"name\":\"(.*?)\",/is", $allStr, $matches);
			foreach($matches[3] as $k=>$match)
			{
				if(empty($match)||strpos($result,$matches[1][$k]."&uid=".$matches[2][$k]))continue;
					$result=$result.decodeSohu($match)."$".$matches[1][$k]."&uid=".$matches[2][$k].'$iask'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;	
		}
		}
		else{
			$pageStr = get($url);
			preg_match_all("/<title>(.*?)_/is",$pageStr,$title);
			$guid=gsubStr($url,"/b/","-").gsubStr($url,"-",".html");
			$result = $result.$title[1][0].'$'.$guid.'$iask';
			echo $result;
		}
	}else if(strpos($url,"qiyi.com")>0)
	{
		$allStr = get($url);
		if(strpos($url,"-firstEpisodeUrl\"")>0){
			$url=str_replace("href=\"", "", gsubStr($allStr, "-firstEpisodeUrl\"", "\" title=\""));
			$allStr = get($url);
			$pid=gsubStr($allStr, "pid : \"", "\"");
			$ptype=gsubStr($allStr, "ptype : \"", "\"");
			$albid=gsubStr($allStr, "albumId : \"", "\"");
			$tmp=get("http://cache.video.qiyi.com/l/".$pid."/".$ptype."/".$albid);
			preg_match_all("/\"videoUrl\":\"(.*?)\"/is", $tmp, $videos);
			preg_match_all("/\"videoName\":\"(.*?)\"/is", $tmp, $videoNames);
			foreach ($videos[1] as $k=>$video)
			{
				$allStr=get($video);
				$pid=gsubStr($allStr, "pid : \"", "\"");
				$ptype=gsubStr($allStr, "ptype : \"", "\"");
				$albid=gsubStr($allStr, "albumId : \"", "\"");
				$tvId=gsubStr($allStr, "tvId : \"", "\"");
				$videoId=gsubStr($allStr, "videoId : \"", "\"");
				if(empty($videoNames[1][$k])||strpos($result,"vid=".trim($videoId)."-pid=".$pid."-ptype=".$ptype."-albumId=".$albid."-tvId=".$tvId))continue;
				$result = $result.$videoNames[1][$k]."\$vid=".trim($videoId)."-pid=".$pid."-ptype=".$ptype."-albumId=".$albid."-tvId=".$tvId.'$qiyi'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else 
		{
			$pid=gsubStr($allStr, "pid : \"", "\"");
//			echo $pid;die();
			$ptype=gsubStr($allStr, "ptype : \"", "\"");
			$albid=gsubStr($allStr, "albumId : \"", "\"");
			$title=gsubStr($allStr, "title : \"", "\"");
			$videoId=gsubStr($allStr, "videoId : \"", "\"");
			$tvId=gsubStr($allStr, "tvId : \"", "\"");
			$result=$title."\$vid=".trim($videoId)."-pid=".$pid."-ptype=".$ptype."-albumId=".$albid."-tvId=".$tvId.'$qiyi';
			echo $result;
		}
	}/*else if(strpos($url,"video.qq.com")>0)
	{
		if(strpos($url,"group?g")>0){
			$vid=gsubStr($url."#","g=","#");
			$pageStr = get($url);
			preg_match_all("/专辑所有视频<font>\((\d+)\)<\/font>/",$pageStr,$itemCountarr);
			$itemCount = $itemCountarr[1][0];
			if ($itemCount <= 10){
				$pcount = 1;
			}else{
				if($itemCount % 10 == 0) {$pcount = ceil($itemCount / 10);}else{$pcount = ceil($itemCount / 10)+1;}
			}
			for($i=1;$i<=$pcount;$i++){
				$purl = "http://video.qq.com/v1/group/video?p=".$i."&g=".$vid;
				preg_match_all("/<a onClick=\"playVideo\('(.+?)'\)\">(.+?)<\/a>/",$pageStr,$matches);
				$matcheslen=count($matches[1]);
				for($j=0;$j<$matcheslen;$j++)
				{
					$result = $result.$matches[2][$j].'$'.$matches[1][$j].'$qq'."\r\n";
				}
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			$pageStr = get($url);
			preg_match_all("/vp_title=\"视频：(.+?)\";/",$pageStr,$title);
			preg_match_all("/vp_vid=\"(.+?)\";/",$pageStr,$guid);
			$result = $title[1][0].'$'.$guid[1][0].'$qq';
			echo $result;
		}
	}else if(strpos($url,"v.blog.sohu.com")>0)
	{
		if(strpos($url,"/pw/")>0){
			$id=gsubStr($url."/","/pw/","/");
			$sohuDataUrl = "http://v.blog.sohu.com/playlistVideo.jhtml?outType=3&from=2&m=viewlist&playlistId=".$id."&size=300";
			$pageStr = get($sohuDataUrl);
			preg_match_all("/\"id\":(\d+),\"title\":\"(.*?)\"/",$pageStr,$matches);
			$matcheslen=count($matches[1]);
			for($j=0;$j<$matcheslen;$j++)
			{
				$result = $result.decodeSohu($matches[2][$j]).'$'.$matches[1][$j].'$sohu'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			$pageStr = get($url);
			preg_match_all("/<title>(.*?) -/",$pageStr,$title);
			$guid=gsubStr($url."/","/vw/","/");
			$result = $result.$title[1][0].'$'.$guid.'$sohu';
			echo $result;
		}
	}else if(strpos($url,"6.cn")>0)
	{
		$pageStr = get($url);
		if(strpos($url,"playlist/")>0){
			$topicId=gsubStr($url."/","playlist/","/");
			preg_match_all("/<em class=\"hit\">(\d+)<\/em>/",$pageStr,$itemCountarr);
			$itemCount=$itemCountarr[1][0];
			for($j=0;$j<$itemCount;$j++)
			{
				$vPageStr=get("http://6.cn/plist/".$topicId."/".$j.".html");
				preg_match_all( "/<h2 class=\"pvt\">(.*?)<\/h2>/",$vPageStr,$title);
				preg_match_all( "/pageMessage.evid = '(.*?)'/",$vPageStr,$guid);
				$result = $result.$title[1][0].'$'.$guid[1][0].'$6rooms'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			$pageStr=$pageStr;
			preg_match_all( "/<title>(.*?) -/",$pageStr,$title);
			preg_match_all( "/pageMessage.evid = '(.*?)'/",$pageStr,$guid);
			$result = $title[1][0].'$'.$guid[1][0].'$6rooms';
			echo $result;
		}
	}else if(strpos($url,"tv.56.com")>0)
	{
		$pageStr = get($url);
		preg_match_all("/<li><a href=\"(.+?)\">(.+?)<\/a><\/li>/",$pageStr,$matches);
		$matcheslen=count($matches[1]);
		for($j=0;$j<$matcheslen;$j++)
		{
			$title=$matches[2][$j];
			$pageurl = $matches[1][$j];
			$pageStr = get($pageurl);
			preg_match_all("/<title>(.+?)-/",$pageStr,$title2);
			$title=str_replace("【播放】",trim($title2[1][0]),$title);
			$paras = gsubStr($pageStr,"img_host=","'");
			$id = gsubStr($paras,"host=",".")."_/".gsubStr($paras,"pURL=","&")."_/".gsubStr($paras,"sURL=","&")."_/".gsubStr($paras,"user=","&")."_/".gsubStr($paras,"URLid=","&");
			$result = $result.$title.'$'.$id.'$hd_56'."\r\n";
		}
		$result=rtrim($result,"\r\n");
		echo $result;
	}else if(strpos($url,"hd.tudou.com")>0)
	{
		$playlistid = gsubStr($url."/","program/","/");
		$pagenum = getTudouHdPage($playlistid);
		for($i=1;$i<=$pagenum;$i++){
			$pageStr = get("http://hd.tudou.com/ajax/albumVideos.html?videoId=".$playlistid."&pageNumer=".$i);
			preg_match_all("/<a href=\"(.+?)\"/",$pageStr,$matches);
			$matcheslen=count($matches[1]);
			for($j=0;$j<$matcheslen;$j++)
			{
				$pageurl = "http://hd.tudou.com".$matches[1][$j];
				$pageStr = get($pageurl);
				preg_match_all("/iid: \"(\d+)\",/",$pageStr,$guid);
				preg_match_all("/title: \"(.+?)\",/",$pageStr,$title);
				$result = $result.$title[1][0].'$'.$guid[1][0].'$hd_tudou'."\r\n";
			}
		}
		$result=rtrim($result,"\r\n");
		echo $result;
	}else if(strpos($url,"tv.sohu.com")>0)
	{
		$pageStr = get($url);
		$result=gatherSohuHdPlay($pageStr);
		if(strpos($pageStr,"<div id=\"roll\" class=\"area\">")>0)
		{
			preg_match_all("/var pid =\"(\d+)\"/",$pageStr,$pid);
			$playlistidarr = explode("$",$result);
			$playlistid = $playlistidarr[1];
			$result="";
			$pageStr = get("http://hot.vrs.sohu.com/vrs_videolist.action?vid=".$playlistid."&pid=".$pid[1][0]);
			preg_match_all("/\"videoName\":\"(.+?)\".+?\"videoId\":(\d+),\"/",$pageStr,$matches);
			$matcheslen=count($matches[1]);
			for($j=0;$j<$matcheslen;$j++)
			{
				$result = $result.$matches[1][$j].'$'.$matches[2][$j].'$hd_sohu'."\r\n";
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			echo $result;
		}
	}else if(strpos($url,"hd.openv.com")>0)
	{
		if(strpos($url,"/tv_show")>0)
		{
			$pageStr = get($url);
			if(strpos($pageStr,"<li><a href=")==0){
				preg_match_all("/<a class=\"alink\"[\s]href=\"tv_play-(\S+?)html\">(.+?)<\/a>/",$pageStr,$matches);
				$matcheslen=count($matches[1]);
				for($j=0;$j<$matcheslen;$j++)
				{
					if(strpos($matches[1][$j],"cartoon")>0){
						$id = "hdcartoon_".gsubStr($matches[1][$j],"n_",".");
					}else if(strpos($matches[1][$j],"hddoc_")>0){
						$id = "hddoc_".gsubStr($matches[1][$j],"c_",".");
					}else{
					$id = "hdteleplay_".gsubStr($matches[1][$j],"y_",".");
					}
					$result = $result.$matches[2][$j].'$'.$id.'$hd_openv'."\r\n";
				}
			}
			$result=rtrim($result,"\r\n");
			echo $result;
		}else{
			echo gatherOpenvHdPlay($url);
		}
	}else if(strpos($url,"youtube.com")>0)
	{
		$id=gsubStr($url."&","v=","&");
		$pageStr = get($url);
		preg_match_all("/<title>YouTube - (.+?)<\/title>/",$pageStr,$title);
		$result = $title[1][0].'$'.$id."$youtube";
		echo $result;
	}*/
}

function getTudouHdPage($vid){
	$pageurl = "http://hd.tudou.com/ajax/albumVideos.html?videoId=".$vid."&pageNumer=";
	for($i=1;$i<=20;$i++){
		$pagestr = subStr(get($pageurl.$i),0,100);
		$pagestr2 = subStr(get($pageurl.($i+1)),0,100);
		if($pagestr==$pagestr2) return $i;
	}
}

function gatherSohuHdPlay($pageStr){
	preg_match_all("/var vid=\"(\d+)\"/",$pageStr,$id);
	preg_match_all("/<title>(.+?)<\/title>/",$pageStr,$title);
	return str_replace("》","",str_replace("《","",str_replace("-搜狐视频","",$title[1][0]))).'$'.$id[1][0].'$hd_sohu';
}

function gatherOpenvHdPlay($url){
	$pageStr = get($url);
	if(strpos($url,"clips")>0){
		$id="hdmovieclips_".gsubStr($url,"s_",".");
		preg_match_all("/<title>(.+?)<\/title>/",$pageStr,$titlearr);
		$title=str_replace("-高清电影","",$titlearr[1][0]);
	}else if(strpos($url,"prog_")>0){
		$id="CTIYuLeprog_".gsubStr($url,"g_",".");
		preg_match_all("/<title>(.+?)<\/title>/",$pageStr,$titlearr);
		$title=str_replace("-高清电视节目","",$titlearr[1][0]);
	}else{
		$id="hdmovie_".gsubStr($url,"e_",".");
		preg_match_all("/<title>(.+?)<\/title>/",$pageStr,$titlearr);
		$title=str_replace("-高清电影","",$titlearr[1][0]);
	}
	return $title.'$'.$id.'$hd_openv';
}

function gatherSinaHdPlay($url){
	$pageStr = get($url);
	preg_match_all("/vid:'(.*?)'/",$pageStr,$id);
	preg_match_all("/episode:'(.*?)'/",$pageStr,$title);
	return trim("第".$title[1][0]).'集$'.$id[1][0].'$hd_iask';
}

/*function gatherSinaHd($url){
	$pageStr = get($url);
	$getStr = gsubStr($pageStr, "分集点播 begin", "分集点播 end");
	preg_match_all("/<div class=\"pic\"><a href=\"(.*?)\"/is", $getStr, $matches);
	foreach ($matches[1] as $match)
	{
		$result=$result.gatherSinaHdPlay("http://video.sina.com.cn".$match)."\r\n";
	}
	$result=rtrim($result,"\r\n");
	echo $result;
}*/

function gsubStr($str,$startStr,$endStr){
	$strtemp1=explode($startStr, $str);
	$strtemp2=explode($endStr, $strtemp1[1]);
	return $strtemp2[0];
}

function decodeSohu($str){
$str=str_replace('\u','%u',$str);
$str=UnicodeUrl2Gbk($str);
return $str;
}

function gatKu6List($lid,$p)
{
	$url="http://v.ku6.com/playlistVideo.htm?t=list&playlistId=".$lid."&p=".$p."&s=8";
	$allstr=get($url);
	preg_match_all("/\"count\"\s*:\s*(\d+)/is", $allstr,$matches);
	$itemcount=$matches[1][0];
	preg_match_all("/\"vid\":\"(.*?)\"/is", $allstr, $ids);
	preg_match_all("/\"title\":\"(.*?)\"/is", $allstr, $titles);
	foreach ($ids[1] as $k=>$id)
	{
		$title = $titles[1][$k];
		$result = $result.decodesohu($title).'$'.decodesohu($id).'$ku6'."\r\n";
	}
	if(($p*8<$itemcount)&&($itemcount<1000))
	{
		$p=$p+1;
		gatKu6List($lid,$p);
	}
}

function gatherTudou($url)
{
	if(strpos($url,"/playlist/id")>0)
	{
		$pageStr=gbutf8(get($url));
		$getser=gsubStr($pageStr,"<div class=\"pl_panel\" id=\"playItems\">","</div><div class=\"page_nav\"");
		preg_match_all("/class=\"inner\" target=\"new\" title=\"(.*?)\" href=\"(.*?)\"/is",$getser,$p);
		foreach ($p[1] as $k=>$res)
		{
			if(strpos($result,$res))continue;
			$res = gatherTudou($p[2][$k]);
			if(empty($res))
			{
				continue;
			} 
			$result .= $res."\r\n";
		}
		return $result;			
	}
	else if(strpos($url,"/albumcover/")>0)
	{
		$pageStr=gbutf8(get($url));
		$getser=gsubStr($pageStr,"<div id=\"playItems\" class=\"playitems playalbum\">","<div class=\"page_nav\" id=\"pageBar\" style=\"display:none;\">");
		preg_match_all("/<div class=\"pic\">\s*<a target=\"new\" title=\"(.*?)\" href=\"(.*?)\"/is",$getser,$p);
		foreach ($p[1] as $k=>$res)
		{
			if(strpos($result,$res))continue;
			$res = gatherTudou(trim($p[2][$k]));
			if(empty($res))
			{
				continue;
			} 
			$result .= $res."\r\n";
		}
		return $result;
	}		
	
	/*else if(strpos($url,"playlist/playindex")>0||strpos($url,"playlist/id/")>0){
		if(strpos($url,"playindex.do?lid=")>0)
		$url = str_replace("playindex.do?lid=","id/",$url);
		if(substr($url,-5)==".html")
		$url = str_replace(".html","",str_replace("/playlist/id","/playlist/id/",$url));
		$tudouRss = "http://www.tudou.com/playlist/rss.do?lid=".gsubStr($url,"/id/","/");
		$pageStr = get($tudouRss);
		preg_match_all("/\<item\>(.*?)\<\/item>/s",$pageStr,$itemblocks);
			foreach($itemblocks[1] as $block)
			{
				preg_match_all( "/\<title\>(.*?)\<\/title\>/",$block,$title);
				preg_match_all( "/\<tudou:info id=\"(\d{3,}?)\"/",$block,$guid);
				if(empty($title[1][0])||strpos($result,$guid[1][0]))continue;
				$result = $result.$title[1][0].'$'.$guid[1][0].'$tudou'."\r\n";
			}
		$result=rtrim($result,"\r\n");
		return $result;
	}*/
	else{
		$pageStr=gbutf8(get($url));
		preg_match_all("/,kw:\s?\"(.*?)\"/is",$pageStr,$title);
		preg_match_all("/iid:\s?(\d+)/is",$pageStr,$guid);
		$result = $result.$title[1][0].'$'.$guid[1][0].'$tudou';
		return $result;
	}
	
}