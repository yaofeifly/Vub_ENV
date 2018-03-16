<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}
if($method=='index')
{
	$tpl->P["siteaid"] = 10;
	if($tpl->P['pg']<1){ $tpl->P['pg']=1; }
	$tpl->P['cp'] = 'app';
	$tpl->P['cn'] = 'vod_index'.$tpl->P['pg'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/vod_index.html");
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->mark();
	$tpl->pageshow();
}

elseif($method=='map')
{
	$tpl->P["siteaid"] = 11;
	$tpl->P['cp'] = 'app';
	$tpl->P['cn'] = 'vod_map';
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/vod_map.html");
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->mark();
}

elseif($method=='list')
{
	$tpl->P["siteaid"] = 12;
    $tpl->P['cp'] = 'vodlist';
	$tpl->P['cn'] = $tpl->P['id'].'-'.$tpl->P['pg'].'-'.$tpl->P['order'].'-'.$tpl->P['by'].'-'.$tpl->P['year'].'-'.$tpl->P['letter'].'-'.urlencode($tpl->P['area']).'-'.urlencode($tpl->P['lang']);
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->P['vodtypeid'] = $tpl->P['id'];
	
	$tpl->T = $MAC_CACHE['vodtype'][$tpl->P['vodtypeid']];
	if(!is_array($tpl->T)){ showMsg("获取数据失败，请勿非法传递参数","../"); }
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	if(!getUserPopedom($tpl->P['id'], 'list')){ showMsg('您没有权限浏览此列表页', '../user/'); }
	$tpl->P['dp']=true;
	$tpl->loadlist ('vod');
	$tpl->P['dp']=false;
	$tpl->mark();
	$tpl->pageshow();
}

elseif($method=='type')
{
	$tpl->P["siteaid"] = 12;
    $tpl->P['cp'] = 'vodtype';
	$tpl->P['cn'] = $tpl->P['id'].'-'.$tpl->P['pg'] ;
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->P['vodtypeid'] = $tpl->P['id'];
	$tpl->T = $MAC_CACHE['vodtype'][$tpl->P['vodtypeid']];
	if(!is_array($tpl->T)){ showMsg("获取数据失败，请勿非法传递参数","../"); }
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	if(!getUserPopedom($tpl->P['id'], 'list')){ showMsg('您没有权限浏览此列表页', '../user/'); }
	$tpl->P['dp']=true;
	$tpl->loadtype ('vod');
	$tpl->P['dp']=false;
	$tpl->mark();
	$tpl->pageshow();
}

elseif($method=='topicindex')
{
	$tpl->P["siteaid"] = 13;
	$tpl->P['cp'] = 'vodtopicindex';
	$tpl->P['cn'] = $tpl->P['pg'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/vod_topicindex.html");
	$tpl->mark();
	$tpl->pageshow();
}

elseif($method=='topic')
{
	$tpl->P["siteaid"] = 14;
	$tpl->P['cp'] = 'vodtopic';
	$tpl->P['cn'] = $tpl->P['id'].'-'.$tpl->P['pg'].'-'.$tpl->P['order'].'-'.$tpl->P['by'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->P['vodtopicid'] = $tpl->P['id'];
	$tpl->T = $MAC_CACHE['vodtopic'][$tpl->P['vodtopicid']];
	if (!is_array($tpl->T)){ showMsg("获取数据失败，请勿非法传递参数","../"); }
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->loadtopic('vod');
	$tpl->pageshow();
}

elseif($method=='search')
{
	$tpl->P["siteaid"] = 15;
	$wd = be("all", "wd");
	
	if(!empty($wd)){
		$tpl->P["wd"] = $wd;
	}
	
	//if(isN($tpl->P["wd"]) && isN($tpl->P["ids"]) && isN($tpl->P["pinyin"]) && isN($tpl->P["starring"]) && isN($tpl->P["directed"]) && isN($tpl->P["area"]) && isN($tpl->P["lang"]) && isN($tpl->P["year"]) && isN($tpl->P["letter"]) && isN($tpl->P["tag"]) && isN($tpl->P["type"]) && isN($tpl->P["typeid"]) && isN($tpl->P["classid"]) ){ alert ("搜索参数不正确"); }
	
    $tpl->P['cp'] = 'vodsearch';
	$tpl->P['cn'] = urlencode($tpl->P['wd']).'-'.$tpl->P['pg'].'-'.$tpl->P['order'].'-'.$tpl->P['by'].'-'.$tpl->P['ids']. '-'.$tpl->P['pinyin']. '-'.$tpl->P['type'].  '-'.$tpl->P['year']. '-'.$tpl->P['letter'].'-'.$tpl->P['typeid'].'-'.$tpl->P['classid'].'-'.urlencode($tpl->P['area']) .'-'.urlencode($tpl->P['lang'])  .'-'.urlencode($tpl->P['tag']) .'-'.urlencode($tpl->P['starring']) .'-'.urlencode($tpl->P['directed']) ;
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	
	
	if (!isN($tpl->P["year"])){
		$tpl->P["key"]=$tpl->P["year"];
		$tpl->P["des"] = $tpl->P["des"] ."&nbsp;上映年份为".$tpl->P["year"];
		$tpl->P["where"] = $tpl->P["where"] . " AND d_year=". $tpl->P["year"] ." ";
	}
    if (!isN($tpl->P["letter"])){
    	$tpl->P["key"]=$tpl->P["letter"];
    	$tpl->P["des"] = $tpl->P["des"] . "&nbsp;首字母为" . $tpl->P["letter"];
    	$tpl->P["where"] = $tpl->P["where"] . " AND d_letter='" . $tpl->P["letter"] ."' ";
    }
    if(!isN($tpl->P["area"])){
    	$tpl->P["key"]=$tpl->P["area"];
    	$tpl->P["des"] = $tpl->P["des"] . "&nbsp;地区为" . $tpl->P["area"];
    	$tpl->P["where"] = $tpl->P["where"] . " AND d_area='" . $tpl->P["area"] ."' ";
    }
    if (!isN($tpl->P["lang"])){
    	$tpl->P["key"]=$tpl->P["lang"];
    	$tpl->P["des"] = $tpl->P["des"] . "&nbsp;语言为" . $tpl->P["lang"];
    	$tpl->P["where"] = $tpl->P["where"] . " AND d_lang='" . $tpl->P["lang"] ."' ";
    }
    if (!isN($tpl->P["wd"])) {
    	$tpl->P["key"]=$tpl->P["wd"] ;
    	$tpl->P["des"] = $tpl->P["des"] . "&nbsp;名称或主演为" . $tpl->P["wd"];
    	$tpl->P["where"] = $tpl->P["where"] . " AND ( instr(d_name,'".$tpl->P['wd']."')>0 or instr(d_starring,'".$tpl->P['wd']."')>0 ) ";
    }
    
    if (!isN($tpl->P["pinyin"])){
    	$tpl->P["key"]=$tpl->P["pinyin"] ;
    	$tpl->P["des"] = $tpl->P["des"] . "&nbsp;拼音为" . $tpl->P["pinyin"];
    	$tpl->P["where"] = $tpl->P["where"] . " AND instr(d_enname,'".$tpl->P['pinyin']."')>0 ";
    }
	    
	if (!isN($tpl->P["starring"])){
		$tpl->P["key"]=$tpl->P["starring"] ;
		$tpl->P["des"] = $tpl->P["des"] . "&nbsp;主演为" . $tpl->P["starring"];
		$tpl->P["where"] = $tpl->P["where"] . " AND instr(d_starring,'".$tpl->P['starring']."')>0 ";
	}
	
	if (!isN($tpl->P["directed"])){
		$tpl->P["key"]=$tpl->P["directed"] ;
		$tpl->P["des"] = $tpl->P["des"] . "&nbsp;导演为" . $tpl->P["directed"];
		$tpl->P["where"] = $tpl->P["where"] . " AND instr(d_directed,'".$tpl->P['directed']."')>0 ";
	}
    
    if (!isN($tpl->P["tag"])){
		$tpl->P["key"]=$tpl->P["tag"] ;
		$tpl->P["des"] = $tpl->P["des"] . "&nbsp;Tag为" . $tpl->P["tag"];
		$tpl->P["where"] = $tpl->P["where"] . " AND instr(d_tag,'".$tpl->P['tag']."')>0 ";
	}
	
    $tpl->P['typepid'] = 0;
	if(!isN($tpl->P["typeid"])){
		$typearr = $MAC_CACHE['vodtype'][$tpl->P['typeid']];
		if (is_array($typearr)){
			$tpl->P['typepid'] = $typearr['t_pid'];
			if (isN($tpl->P["key"])){ $tpl->P["key"]= $typearr["t_name"];  }
			$tpl->P["des"] = $tpl->P["des"] . "&nbsp;分类为" . $typearr["t_name"];
			$tpl->P["where"] = $tpl->P["where"] . " AND d_type in (" . $typearr["childids"] . ") ";
		}
		unset($typearr);
	}
	if(!isN($tpl->P["classid"])){
		$classarr = $MAC_CACHE['vodclass'][$tpl->P['classid']];
		if (is_array($classarr)){
			if (isN($tpl->P["key"])){ $tpl->P["key"]= $classarr["c_name"];  }
			$tpl->P["des"] = $tpl->P["des"] . "&nbsp;剧情分类为" . $classarr["c_name"];
			$tpl->P["where"] = $tpl->P["where"] .  ' AND instr(d_class,\','.$tpl->P['classid'].',\')>0  ';
		}
		unset($classarr);
	}
	
	
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->H = loadFile(MAC_ROOT_TEMPLATE."/vod_search.html");
	$tpl->mark();
	$tpl->pageshow();
	
	$colarr = array('{page:des}','{page:key}','{page:now}','{page:order}','{page:by}','{page:wd}','{page:wdencode}','{page:pinyin}','{page:letter}','{page:year}','{page:starring}','{page:starringencode}','{page:directed}','{page:directedencode}','{page:area}','{page:areaencode}','{page:lang}','{page:langencode}','{page:typeid}','{page:typepid}','{page:classid}');
	$valarr = array($tpl->P["des"],$tpl->P["key"],$tpl->P["pg"],$tpl->P["order"],$tpl->P["by"],$tpl->P["wd"],urlencode($tpl->P["wd"]),$tpl->P["pinyin"],$tpl->P["letter"],$tpl->P['year']==0?'':$tpl->P['year'],$tpl->P["starring"],urlencode($tpl->P["starring"]),$tpl->P["directed"],urlencode($tpl->P["directed"]),$tpl->P["area"],urlencode($tpl->P["area"]),$tpl->P["lang"],urlencode($tpl->P["lang"]),$tpl->P['typeid'],$tpl->P['typepid'] ,$tpl->P['classid']  );
	
	$tpl->H = str_replace($colarr, $valarr ,$tpl->H);
    unset($colarr,$valarr);
    $linktype = $tpl->getLink('vod','search','',array('typeid'=>$tpl->P['typepid']));
    $linkyear = $tpl->getLink('vod','search','',array('year'=>''));
    $linkletter = $tpl->getLink('vod','search','',array('letter'=>''));
    $linkarea = $tpl->getLink('vod','search','',array('area'=>''));
    $linklang = $tpl->getLink('vod','search','',array('lang'=>''));
    $linkclass = $tpl->getLink('vod','search','',array('classid'=>''));
    
    
    $linkorderasc = $tpl->getLink('vod','search','',array('order'=>'asc'));
    $linkorderdesc = $tpl->getLink('vod','search','',array('order'=>'desc'));
    $linkbytime = $tpl->getLink('vod','search','',array('by'=>'time'));
    $linkbyhits = $tpl->getLink('vod','search','',array('by'=>'hits'));
    $linkbyscore = $tpl->getLink('vod','search','',array('by'=>'score'));
    
    $tpl->H = str_replace(array('{page:linkyear}','{page:linkletter}','{page:linkarea}','{page:linklang}','{page:linktype}','{page:linkclass}','{page:linkorderasc}','{page:linkorderdesc}','{page:linkbytime}','{page:linkbyhits}','{page:linkbyscore}',), array($linkyear,$linkletter,$linkarea,$linklang,$linktype,$linkclass,$linkorderasc,$linkorderdesc,$linkbytime,$linkbyhits,$linkbyscore) ,$tpl->H);
    
}

elseif($method=='detail')
{
	$tpl->P["siteaid"] = 16;
	$tpl->P['cp'] = 'vod';
	$tpl->P['cn'] = $tpl->P['id'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_id=" . $tpl->P['id'];
	$row = $db->getRow($sql);
	if(!$row){ showMsg("获取数据失败，请勿非法传递参数",MAC_PATH); }
	if(!getUserPopedom($row["d_type"], "vod")){ showMsg ("您没有权限浏览内容页", MAC_PATH."index.php?m=user-index.html"); }
	$tpl->T = $MAC_CACHE['vodtype'][$row['d_type']];
	$tpl->D = $row;
	unset($row);
	$tpl->loadvod("detail");
	$tpl->replaceVod();
	$tpl->playdownlist ("play");
	$tpl->playdownlist ("down");
}
	
elseif($method=='play')
{
	$tpl->P["siteaid"] = 17;
    $tpl->P['cp'] = 'vodplay';
	$tpl->P['cn'] = $tpl->P['id'].'-'.$tpl->P['src'].'-'.$tpl->P['num'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_id=" . $tpl->P['id'];
	$row = $db->getRow($sql);
	if(!$row){ showMsg("获取数据失败，请勿非法传递参数",MAC_PATH); }
	if (!getUserPopedom($row["d_type"],"play")){ showMsg ("您没有权限浏览播放页",MAC_PATH."index.php?m=user-index.html"); }
	if ($MAC['user']['status']==1){
		$uid = intval($_SESSION['userid']);
		if($row["d_stint"]>0 && $uid==0 ){ showMsg ("此为收费数据请先登录再观看",MAC_PATH."index.php?m=user-index.html"); }
		
		$rowu = $db->getRow("SELECT * FROM {pre}user where u_id=".$uid);
		if ($rowu){
			$stat =false;
			$upoint = $rowu["u_points"];
			$playf = ",".$tpl->P['id']."-".$tpl->P['src']."-".$tpl->P['num'].",";
			if($rowu["u_flag"]==1){
				if(time() >= $rowu["u_end"]){ $msg = "对不起,您的会员时间已经到期,请联系管理员续费!"; }
			}
			elseif ($rowu["u_flag"] == 2){
				if(($rowu["u_start"]>= $rowu["u_ip"]) &&  ($rowu["u_ip"] <= $rowu["u_end"])){$stat=true; }
				if(!$stat){ $msg = "对不起,您登录IP段不在受理范围，请联系管理员续费!";}
			}
			else{
				if ($rowu["u_points"] < $row["d_stint"]){
					if(strpos(",".$rowu["u_plays"],$playf)){ $stat=true; }
					if(!$stat){ $msg = "对不起,您的积分不够，无法观看收费数据，请推荐本站给您的好友、赚取更多积分";}
				}
				$upoint = $rowu["u_points"] - $row["d_stint"];
			}
			if (!empty($msg)){ alertUrl ($msg,MAC_PATH."index.php?m=user-index.html");exit;}
			if (strpos(",".$rowu["u_plays"],$playf) > 0){ $stat = true;}
			if (!$stat){
				$uplays = ",".$rowu["u_plays"].$playf;
				$uplays = str_replace(",,",",",$uplays);
				$db->Update ("{pre}user" ,array("u_points","u_plays"),array($upoint,$uplays),"u_id=".$uid);
			}
		}
		unset($rowu);
	}
	$tpl->T = $MAC_CACHE['vodtype'][$row['d_type']];
	$tpl->D = $row;
	unset($row);
	$tpl->loadvod('play');
	$tpl->replaceVod();
	$tpl->playdownlist('play');
	$tpl->H = str_replace('[vod:playnum]',$tpl->P['num'],$tpl->H);
	$tpl->H = str_replace('[vod:playsrc]',$tpl->P['src'],$tpl->H);
	$tpl->getUrlName('play');
	$tpl->H = str_replace('[vod:playerinfo]', '<script>' .$tpl->getUrlInfo('play'). ' </script>'. "\n" ,$tpl->H);
	$tpl->H = str_replace('[vod:player]', '<script src="'.$MAC['site']['installdir'].'js/playerconfig.js"></script><script src="'.$MAC['site']['installdir'].'js/player.js"></script>'. "\n" ,$tpl->H);
	
}

elseif($method=='down')
{
	$tpl->P['siteaid'] = 18;
	$tpl->P['cp'] = 'voddown';
	$tpl->P['cn'] = $tpl->P['id'].'-'.$tpl->P['src'].'-'.$tpl->P['num'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_id=" . $tpl->P['id'];
	$row = $db->getRow($sql);
	if(!$row){ showMsg("获取数据失败，请勿非法传递参数",MAC_PATH); }
	if (!getUserPopedom($row["d_type"],"down")){ showMsg ("您没有权限浏览播放页",MAC_PATH."index.php?m=user-index.html"); }
	if ($MAC['user']['status']==1){
		$uid = intval($_SESSION['userid']);
		if($row["d_stint"]>0 && $uid==0 ){ showMsg ("此为收费数据请先登录再观看",MAC_PATH."index.php?m=user-index.html"); }
		$rowu = $db->getRow("SELECT * FROM {pre}user where u_id=".$uid);
		if ($rowu){
			$stat =false;
			$upoint = $rowu["u_points"];
			$downf = ",".$tpl->P['id']."-".$tpl->P['src']."-".$tpl->P['num'].",";
			if($rowu["u_flag"]==1){
				if(time() >= $rowu["u_end"]){ $msg = "对不起,您的会员时间已经到期,请联系管理员续费!"; }
			}
			elseif ($rowu["u_flag"] == 2){
				if(($rowu["u_start"]>= $rowu["u_ip"]) &&  ($rowu["u_ip"] <= $rowu["u_end"])){$stat=true; }
				if(!$stat){ $msg = "对不起,您登录IP段不在受理范围，请联系管理员续费!";}
			}
			else{
				if ($rowu["u_points"] < $row["d_stint"]){
					if(strpos(",".$rowu["u_downs"],$downf)){ $stat=true; }
					if(!$stat){ $msg = "对不起,您的积分不够，无法下载收费数据，请推荐本站给您的好友、赚取更多积分";}
				}
				$upoint = $rowu["u_points"] - $row["d_stint"];
			}
			if (!empty($msg)){ alertUrl ($msg,MAC_PATH."index.php?m=user-index.html");exit;}
			if (strpos(",".$rowu["u_downs"],$downf) > 0){ $stat = true;}
			if (!$stat){
				$udowns = ",".$rowu["u_downs"].$downf;
				$udowns = str_replace(",,",",",$udowns);
				$db->Update ("{pre}user" ,array("u_points","u_downs"),array($upoint,$udowns),"u_id=".$uid);
			}
		}
		unset($rowu);
	}
	$tpl->T = $MAC_CACHE['vodtype'][$row['d_type']];
	$tpl->D = $row;
	unset($row);
	$tpl->loadvod ("down");
	$tpl->replaceVod();
	$tpl->playdownlist ("down");
	$tpl->H = str_replace("[vod:downnum]",$tpl->P["num"],$tpl->H);
	$tpl->H = str_replace("[vod:downsrc]",$tpl->P["src"],$tpl->H);
	$tpl->getUrlName("down");
	$tpl->H = str_replace("[vod:downinfo]", "<script>" .$tpl->getUrlInfo("down"). " </script>". "\n" ,$tpl->H);
	$tpl->H = str_replace('[vod:downer]', '<script src="'.$MAC['site']['installdir'].'js/playerconfig.js"></script><script src="'.$MAC['site']['installdir'].'js/player.js"></script>'. "\n" ,$tpl->H);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>