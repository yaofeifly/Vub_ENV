<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/config.user.inc.php");
CheckPurview();
$action=addslashes($_GET[action]);
$tid=addslashes($_GET[tid]);
$vid=addslashes($_GET[vid]);

if(empty($action))
{
	$action = '';
}
	$sql="select * from sea_topic where id=$tid"; //获取之前的影片id'数据
	$dsql->SetQuery($sql);
	$dsql->Execute('al') ;
	while($row=$dsql->GetObject('al'))
	{
	$info1=$row->vod;
	}
	$zall=$info1;

	if($info1 !== "")
	{$info1="$info1"."ttttt";}
	else
	{$info1="";}
if($action=="topicadd")
{
	if(empty($vid))
	{
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;	
	}
	$zinfo="$info1$vid";
	$usql = "update sea_topic set vod='$zinfo' where id='$tid'";
	if(!$dsql->ExecuteNoneQuery($usql))
	{echo "'alert('插入数据失败')";}
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;
}
elseif($action=="topicdel")
{
	$delvid1="$vid".'ttttt';
	$del1=str_replace($delvid1,"",$zall);
	$delvid2='ttttt'."$vid";
	$del2=str_replace($delvid2,"",$del1);
	$del=str_replace($vid,"",$del2);
	$sql = "update sea_topic set vod='$del' where id='$tid'";
	if(!$dsql->ExecuteNoneQuery($sql))
	{echo "'alert('插入数据失败')";}
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;
}


if($action=="topicaddall")
{	
    $allvid=$_POST[e_id];
	if(empty($allvid))
	{
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;	
	}
	
	for($i=0;$i<count($allvid);$i++)
	{
	$allstr1.='ttttt'."$allvid[$i]";
	}
	$allstr=substr($allstr1,5);
	
	$all="$info1$allstr";
	$usql = "update sea_topic set vod='$all' where id='$tid'";
	if(!$dsql->ExecuteNoneQuery($usql))
	{echo "'alert('插入数据失败')";}
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;	
}


elseif($action=="topicdelall")
{
	$dallsql = "update sea_topic set vod='0' where id='$tid'";
	if(!$dsql->ExecuteNoneQuery($dallsql))
	{echo "'alert('失败')";}
	header("Location: admin_topic_vod.php?tid=$tid"); 
	exit;
}



elseif($action=="add")
{
$makePlayerSelectStr=str_replace("'","\'",makePlayerSelect(""));
$makeDownSelectStr = str_replace("'","\'",makedownSelect(""));

include(sea_ADMIN.'/templets/admin_topic_vod.htm');
exit();
}
elseif($action=="save")
{
	if(trim($v_name) == '')
	{
		ShowMsg("影片名不能为空！","-1");
		exit();
	}
	if(empty($v_type))
	{
		ShowMsg("请选择分类！","-1");
		exit();
	}
	$v_playurl = empty($v_playurl) ? $v_playurl : repairUrlForm($v_playurl,$v_playfrom);
	$m_downurl = empty($m_downurl) ? $m_downurl : dwonUrlForm($m_downurl,$m_downfrom);
	$v_playdata=transferUrl($v_playfrom,$v_playurl);
	$v_downdata=transferUrl($m_downfrom,$m_downurl);
	$tid = empty($v_type) ? 0 : intval($v_type);
	$v_state = empty($v_state) ? 0 : intval($v_state);
	$v_topic = empty($v_topic) ? 0 : intval($v_topic);
	$v_hit = empty($v_hit) ? 0 : intval($v_hit);
	$v_addtime = time();
	$v_money = empty($v_money) ? 0 : intval($v_money);
	$v_rank = empty($v_rank) ? 0 : intval($v_rank);
	$v_name = htmlspecialchars(cn_substrR($v_name,60));
	$v_actor = cn_substrR($v_actor,200);
	$v_publishyear = empty($v_publishyear) ? date(Y) : intval($v_publishyear);
	$v_publisharea = cn_substrR($v_publisharea,10);
	$v_note = cn_substrR($v_note,30);
	$v_tags = cn_substrR(strtolower(addslashes($v_tags)),30);
	$v_tags = str_replace('，', ',', $v_tags);
	$v_tags = str_replace(',,', ',', $v_tags);
	$v_director = cn_substrR($v_director,200);
	$v_lang = cn_substrR($v_lang,10);
	$v_commend =  empty($v_commend) ? 0 : intval($v_commend);
	$v_enname = empty($v_enname)?Pinyin($v_name):$v_enname;
	$v_letter = strtoupper(substr($v_enname,0,1));
	$v_extratype = "";
	
	if($checkbox)
	{
		for($i=0;$i<count($v_type_extra);$i++)
		{
			if($i==0)
				$v_extratype = $v_type_extra[$i];
			elseif($i>0 && $i<=count($v_type_extra)-1)
				$v_extratype = $v_extratype.",".$v_type_extra[$i];
		}
	}
	
	if (substr($v_tags, -1) == ',') {
		$v_tags = substr($v_tags, 0, strlen($v_tags)-1);
	}
	$v_pic = cn_substrR($v_pic,100);
	$v_content=HtmlReplace(stripslashes($v_content),-1);
	switch (trim($acttype)) 
	{
		case "add":
			$insertSql = "insert into sea_data(tid,v_name,v_letter,v_state,v_topic,v_hit,v_money,v_rank,v_actor,v_color,v_publishyear,v_publisharea,v_pic,v_addtime,v_note,v_tags,v_lang,v_director,v_enname,v_commend,v_extratype) values ('$tid','$v_name','$v_letter','$v_state','$v_topic','$v_hit','$v_money','$v_rank','$v_actor','$v_color','$v_publishyear','$v_publisharea','$v_pic','$v_addtime','$v_note','$v_tags','$v_lang','$v_director','$v_enname','$v_commend','$v_extratype')";
			if($dsql->ExecuteNoneQuery($insertSql))
			{
				$v_id = $dsql->GetLastID();
				$dsql->ExecuteNoneQuery("INSERT INTO `sea_content`(`v_id`,`tid`,`body`) VALUES ('$v_id','$tid','$v_content')");
				$dsql->ExecuteNoneQuery("INSERT INTO `sea_playdata`(`v_id`,`tid`,`body`,`body1`) VALUES ('$v_id','$tid','$v_playdata','$v_downdata')");
				addtags($v_tags,$v_id);
				clearTypeCache();
				selectMsg("添加成功,是否继续添加","admin_video.php?action=add","admin_video.php");
			}
			else
			{
				$gerr = $dsql->GetError();
				ShowMsg("把数据保存到数据库主表 `sea_data` 时出错。".str_replace('"','',$gerr),"javascript:;");
				exit();
			}
		break;
		case "edit":
			$v_id = isset($v_id) && is_numeric($v_id) ? $v_id : 0;
			$updateSql = "tid = '$tid',v_name = '$v_name',v_letter = '$v_letter',v_state = '$v_state',v_topic = '$v_topic',v_hit = '$v_hit',v_money = '$v_money',v_rank = '$v_rank',v_actor = '$v_actor',v_color = '$v_color',v_publishyear = '$v_publishyear',v_publisharea = '$v_publisharea',v_pic = '$v_pic',v_note = '$v_note',v_tags = '$v_tags',v_lang='$v_lang',v_director='$v_director',v_enname='$v_enname',v_extratype='$v_extratype'";
			if(!empty($isupdatetime)) $updateSql .= ",v_addtime='$v_addtime'";
			$updateSql = "update sea_data set ".$updateSql." where v_id=".$v_id;
			if(!$dsql->ExecuteNoneQuery($updateSql))
			{
				ShowMsg('更新影片出错，请检查',-1);
				exit();
			}
			if(!$dsql->ExecuteNoneQuery("update `sea_content` set `body`='$v_content' where v_id='$v_id'"))
			{
				ShowMsg("更新影片内容时出错，请检查原因！",-1);
				exit();
			}
			if(!$dsql->ExecuteNoneQuery("update `sea_playdata` set `body`='$v_playdata',`body1`='$v_downdata' where v_id='$v_id'"))
			{
				ShowMsg("更新影片播放数据时出错，请检查原因！",-1);
				exit();
			}
			$v_oldtags = $v_oldtags ? strtolower(addslashes($v_oldtags)) : '';
			updatetags($v_id, $v_tags, $v_oldtags);
			if($cfg_runmode=='0'){
				$trow = $dsql->GetOne("select ishidden from sea_type where tid=".$tid);
				if($trow['ishidden']==1){
					ShowMsg("影片更新成功",$v_back);
					exit();
				}else{
					ShowMsg("影片更新成功，转向生成页面！","admin_makehtml.php?action=single&id=".$v_id."&from=".$v_back);
					exit();
				}
			}else{
				ShowMsg("影片更新成功",$v_back);
				exit();
			}
			break;
	}
}
elseif($action=="lock")
{
	$back=$Pirurl;
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$dsql->ExecuteNoneQuery("update `sea_data` set v_isunion = '1' where v_id='$id'");
	ShowMsg("影片锁定成功",$back);
	exit();
}
elseif($action=="unlock")
{
	$back=$Pirurl;
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$dsql->ExecuteNoneQuery("update `sea_data` set v_isunion = '0' where v_id='$id'");
	ShowMsg("影片解锁成功",$back);
	exit();
}
elseif($action=="lockall")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要锁定/解锁的影片","-1");
		exit();
	}
	$dsql->SetQuery("select v_id,v_isunion from `sea_data` where v_id in (".implode(',',$e_id).")");
	$dsql->Execute("lockdata");
	while($rs = $dsql->GetArray("lockdata"))
	{
		$dsql->ExecuteNoneQuery("update `sea_data` set v_isunion = '".($rs['v_isunion']==1?0:1)."' where v_id=".$rs['v_id']);
	}
	ShowMsg("影片锁定/解锁成功",$back);
	exit();
}
elseif($action=="del")
{
	$back=$Pirurl;
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$vtypeAndPic=$dsql->GetOne("select tid,v_pic,v_addtime,v_enname from sea_data where v_id=".$id);
	$vtype=$vtypeAndPic['tid'];
	$vpic=$vtypeAndPic['v_pic'];
	if(strpos($vpic,'uploads/')===0) delFile("../".$vpic);
	//delContentFile($vtype,$id,date('Y-n',$vtypeAndPic['v_addtime']),$vtypeAndPic['v_enname']);
	$dsql->ExecuteNoneQuery("delete from sea_data where v_id=".$id);
	$dsql->ExecuteNoneQuery("delete From `sea_content` where v_id='$id'");
	$dsql->ExecuteNoneQuery("delete From `sea_playdata` where v_id='$id'");
	clearTypeCache();
	ShowMsg("影片删除成功",$back);
	exit();
}
elseif($action=="restoreall")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要还原的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$sqlStr="update sea_data set v_recycled=0 where v_id in(".$ids.")";
	$dsql->ExecuteNoneQuery($sqlStr);
	ShowMsg("还原操作成功",$back);
	exit();


}
elseif($action=="delall")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	/*$sqlStr="select v_id,tid,v_enname,v_addtime from sea_data where v_id in(".$ids.")";
	$dsql->SetQuery($sqlStr);
	$dsql->Execute('video_delall');
	while($row=$dsql->GetObject('video_delall'))
	{
		delContentFile($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname);
	}*/
	$dsql->ExecuteNoneQuery("delete from sea_data where v_id in(".$ids.")");
	$dsql->ExecuteNoneQuery("delete From `sea_content` where v_id in(".$ids.")");
	$dsql->ExecuteNoneQuery("delete From `sea_playdata` where v_id in(".$ids.")");
	clearTypeCache();
	ShowMsg("影片删除成功",$back);
	exit();
}
elseif($action=="psettopic")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要设置专题的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("update sea_data set v_topic=".$ptopic." where v_id in(".$ids.")");
	ShowMsg("专题操作成功",$back);
	exit();
}
elseif($action=="psettype")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要移动分类的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("update sea_data set tid=".$movetype." where v_id in(".$ids.")");
	ShowMsg("批量移动影片成功",$back);
	exit();
}
elseif($action=="deltypedata")
{
	$back=$Pirurl;
	$movetype = isset($movetype) && is_numeric($movetype) ? $movetype : 0;
	$dsql->ExecuteNoneQuery("delete from sea_data where tid=".$movetype);
	ShowMsg("删除分类数据成功",$back);
	exit();
}
elseif($action=="hide")
{
	$back=$Pirurl;
	if(empty($id))
	{
		ShowMsg("请选择需要隐藏的影片","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_data set v_recycled=1 where v_id=".$id);
	ShowMsg("隐藏影片成功",$back);
	exit();
	
		
}
elseif($action=="restore")
{
	$back=$Pirurl;
	if(empty($id))
	{
		ShowMsg("请选择需要还原的影片","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_data set v_recycled=0 where v_id=".$id);
	ShowMsg("还原影片成功",$back);
	exit();	
}
else
{
	require_once(sea_DATA."/config.ftp.php");
include(sea_ADMIN.'/templets/admin_topic_vod.htm');
	exit();
}

function getAreaSelect($selectName,$strSelect,$areaId)
{
	$publishareatxt=sea_DATA."/admin/publisharea.txt";
	$publisharea = array();
	if(filesize($publishareatxt)>0)
	{
		$publisharea = file($publishareatxt);
	}
	$str = "<select name='".$selectName."' >";
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publisharea as &$area)
	{
		$area=str_replace("\r\n","",$area);
		if(!empty($areaId) && ($area==$areaId)) $str .= "<option value='".$area."' selected>$area</option>";
		else $str .= "<option value='".$area."'>$area</option>";
	}
	if(!in_array($areaId,$publisharea)&&!empty($areaId))
	$str .= "<option value='".$areaId."' selected>$areaId</option>";
	$str .= "</select>";
	return $str;
}

function getYuyanSelect($selectName,$strSelect,$yuyanId)
{
	$publishareatxt=sea_DATA."/admin/publishyuyan.txt";
	$publisharea = array();
	if(filesize($publishareatxt)>0)
	{
		$publisharea = file($publishareatxt);
	}
	$str = "<select name='".$selectName."' >";
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publisharea as &$area)
	{
		$area=str_replace("\r\n","",$area);
		if(!empty($yuyanId) && ($area==$yuyanId)) $str .= "<option value='".$area."' selected>$area</option>";
		else $str .= "<option value='".$area."'>$area</option>";
	}
	if(!in_array($yuyanId,$publisharea)&&!empty($yuyanId))
	$str .= "<option value='".$yuyanId."' selected>$yuyanId</option>";
	$str .= "</select>";
	return $str;

}


function getYearSelect($selectName,$strSelect,$yearId)
{
	$publishyeartxt=sea_DATA."/admin/publishyear.txt";
	$publishyear = array();
	if(filesize($publishyeartxt)>0)
	{
		$publishyear = file($publishyeartxt);
	}
	$str = "<select name='".$selectName."' >";
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publishyear as &$year)
	{
		$year=str_replace("\r\n","",$year);
		if(!empty($yearId) && ($year==$yearId)) $str .= "<option value='".$year."' selected>$year</option>";
		else $str .= "<option value='".$year."'>$year</option>";
	}
	if(!in_array($yearId,$publishyear)&&!empty($yearId))
	$str .= "<option value='".$yearId."' selected>$yearId</option>";
	$str .= "</select>";
	return $str;
}

function makeRankSelect($selectName,$strSelect,$rankId)
{
	global $dsql,$cfg_iscache;
	$sql="select rank,membername from sea_arcrank order by id asc";
	if($cfg_iscache){
	$mycachefile=md5('array_Rank_Lists_all');
	setCache($mycachefile,$sql);
	$rows=getCache($mycachefile);
	}else{
	$rows=array();
	$dsql->SetQuery($sql);
	$dsql->Execute('al');
	while($rowr=$dsql->GetObject('al'))
	{
	$rows[]=$rowr;
	}
	unset($rowr);
	}
	$str = "<select name='".$selectName."' >";
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($rows as $row)
	{
		if(!empty($rankId) && ($row->rank==$rankId)) $str .= "<option value='".$row->rank."' selected>$row->membername</option>";
		$str .= "<option value='".$row->rank."'>$row->membername</option>";
	}
	$str .= "</select>";
	return $str;
}

function makePlayerSelect($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/playerKinds.xml";
	$allstr = '';
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==gbutf8($player['flag'])){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			if($player['open']==1)
			$allstr .="<option value='".gbutf8($player['flag'])."' $selectstr>".gbutf8($player['flag'])."</option>";
			
	}
return $allstr;
}

function makedownSelect($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/downKinds.xml";
	
	$allstr = '';
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==gbutf8($player['flag'])){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			$allstr .="<option value='".gbutf8($player['flag'])."' $selectstr>".gbutf8($player['flag'])."</option>";
			
	}
return $allstr;
}

function dwonUrlForm($downurl,$downfrom)
{
	foreach($downurl as $k=>$v)
	{
	$v = trim($v);
	if($v!='')
	{
	$rstr=$rstr.repairStr($v,'down').', ';
	}else{
	$rstr=$rstr.', ';
	}
	}
return rtrim($rstr, ", ");
}

function repairUrlForm($playurl,$playfrom)
{
	if (count($playurl)!=count($playfrom))
	{
		ShowMsg("未为每个数据选择数据来源！","-1");
		return ;
	}
	foreach($playurl as $k=>$v)
	{
	$v = trim($v);
	if($v!='')
	{
	$rstr=$rstr.repairStr($v,getReferedId($playfrom[$k])).', ';
	}else{
	$rstr=$rstr.', ';
	}
	}
return rtrim($rstr, ", ");
}

function repairStr($vstr,$playfrom)
{
	$vstr = str_replace(chr(10),"",$vstr);
	$vstr = explode(chr(13),$vstr);
	$stru="";
	foreach($vstr as $j=>$playurl)
	{
	$strurl = explode('$',$playurl);
	$i=count($strurl);
	if ($i==1){
	$jj=$j+1;
	$stru=$stru.'第'.$jj.'集$'.$playurl.'$'.$playfrom.chr(13).chr(10);
	}elseif ($i==2){
	$stru=$stru.$playurl.'$'.$playfrom.chr(13).chr(10);
	}else{
	$stru=$stru.$playurl.chr(13).chr(10);
	}
	}
return $stru;
}

function transferUrl($fromStr,$playStr)
{
	$playStr=str_replace(chr(13),"#",str_replace(chr(10),"",str_replace("$$","",str_replace("#","",$playStr))));
	$playStr=rtrim(rtrim($playStr, ","),"#");
	$fromArray=$fromStr;
	$playArray=explode(", ",$playStr);
	$fromLen=count($fromArray);
	$playLen=count($playArray);
/*	if($fromLen!=$playLen){
		ShowMsg("来源或者地址没有填写完整！","-1");
		exit;
	}
	if($fromLen==0){
		$transferUrl=trim($fromArray[0])."$$".trim(rtrim($playArray[0],"#"));
		return  $transferUrl;
	}*/
	$resultStr="";
	/*for($i=1;$i<=$fromLen;$i++){
		$j=$i-1;
		if(empty($fromArray[$i]) and !empty($playArray[$j])){
			ShowMsg("来源没有填写完整！$i","-1");
			exit;
		}elseif($playArray[$j]=='')
		{	
			$resultStr.='';
		}
		else{
			$resultStr=$resultStr.trim($fromArray[$i])."$$".trim(rtrim($playArray[$j],"#"))."$$$";
		}
	}*/
	$j=0;
	foreach($fromArray as $i=>$from)
	{
		
		if(empty($from) and !empty($playArray[$j])){
			ShowMsg("来源没有填写完整！$i","-1");
			exit;
		}elseif($playArray[$j]=='')
		{	
			$resultStr.='';
		}
		else{
			$resultStr=$resultStr.trim($from)."$$".trim(rtrim($playArray[$j],"#"))."$$$";
		}
		$j++;
	}
	$transferUrl=rtrim($resultStr,"$$$");
	return  $transferUrl;
}

function clearTypeCache()
{
	global $cfg_iscache,$cfg_cachemark;
	if($cfg_iscache)
	{
		$TypeCacheFile=sea_DATA."/cache/".$cfg_cachemark.md5('array_Type_Lists_all').".inc";
		if(is_file($TypeCacheFile)) unlink($TypeCacheFile);
	}
}

function addtags($v_tags,$v_id)
{
	global $dsql;
	if($v_tags)
	{
		if(strpos($v_tags,',')>0)
		{
			$tagdb = explode(',', $v_tags);
		}else{
			$tagdb = explode(' ', $v_tags);
		}
		$tagnum = count($tagdb);
		for($i=0; $i<$tagnum; $i++)
		{
			$tagdb[$i] = trim($tagdb[$i]);
			if ($tagdb[$i]) 
			{
				$tag = $dsql->GetOne("SELECT tagid,vids FROM sea_tags WHERE tag='$tagdb[$i]'");
				if(!$tag) {
					$dsql->ExecuteNoneQuery("INSERT INTO sea_tags (tag,usenum,vids) VALUES ('$tagdb[$i]', '1', '$v_id')");
				}else{
					$vids = $tag['vids'].','.$v_id;
					$dsql->ExecuteNoneQuery("UPDATE sea_tags SET usenum=usenum+1, vids='$vids' WHERE tag='$tagdb[$i]'");
				}
			}
			unset($vids);
		}
	}
}

function makeTopicOptions($strSelect)
{
	global $dsql,$cfg_iscache;
	$sql="select id,name from sea_topic order by sort asc";
	if($cfg_iscache){
	$mycachefile=md5('array_Topic_Lists_all');
	setCache($mycachefile,$sql);
	$rows=getCache($mycachefile);
	}else{
	$rows=array();
	$dsql->SetQuery($sql);
	$dsql->Execute('al');
	while($rowr=$dsql->GetObject('al'))
	{
	$rows[]=$rowr;
	}
	unset($rowr);
	}
	if(count($rows)==0) $str = "<option value='-1'>".$strSelect."</option>";
	foreach($rows as $row)
	{
		$str .= "<option value='".$row->id."'>$row->name</option>";
	}
	return $str;
}

function isVideoMake($v_id,$contentUrl)
{
	$contentUrl=str_replace($GLOBALS['cfg_cmspath'],'',$contentUrl);
	echo "<a href=\"admin_makehtml.php?action=single&id=$v_id\">";
	if(file_exists('..'.$contentUrl)){
		echo "<img src='img/yes.gif' border='0' title='点击生成HTML' />";
	}else{
		echo "<img src='img/no.gif' border='0' title='点击生成HTML' />";
	}
	echo "</a>";
}

// 修改Tags并处理数量
function updatetags($videoid, $newkeywords, $oldkeywords) {
	global $dsql;
	if (substr($newkeywords, -1) == ',') {
		$newkeywords = substr($newkeywords, 0, strlen($newkeywords)-1);
	}
	$arrtag		= explode(',', $newkeywords);
	$arrold		= explode(',', $oldkeywords);
	$arrtag_num	= count($arrtag);
	$arrold_num	= count($arrold);

	for($i=0; $i<$arrtag_num; $i++) {
		if (!in_array($arrtag[$i], $arrold)) {
			$arrtag[$i] = trim($arrtag[$i]);
			if ($arrtag[$i]) {
				$tag  = $dsql->GetOne("SELECT tagid,vids FROM sea_tags WHERE tag='$arrtag[$i]'");
				if(!$tag) {
					$dsql->ExecuteNoneQuery("INSERT INTO sea_tags (tag,usenum,vids) VALUES ('$arrtag[$i]', '1', '$videoid')");
				} else {						
					$vids = $tag['vids'].','.$videoid;
					$dsql->ExecuteNoneQuery("UPDATE sea_tags SET usenum=usenum+1, vids='$vids' WHERE tag='$arrtag[$i]'");
				}
			}
		}
		unset($aids);
	}

	for($i=0; $i<$arrold_num; $i++) {
		if ($arrold[$i] && !in_array($arrold[$i], $arrtag)) {
			$tag = $dsql->GetOne("SELECT vids FROM sea_tags WHERE tag='$arrold[$i]'");
			$tag['vids'] = str_replace(','.$videoid, '', $tag['vids']);
			$tag['vids'] = str_replace($videoid.',', '', $tag['vids']);
			$dsql->ExecuteNoneQuery("UPDATE sea_tags SET usenum=usenum-1, vids='".$tag['vids']."' WHERE tag='$arrold[$i]'");
		}
	}
	$dsql->ExecuteNoneQuery("DELETE FROM sea_tags WHERE usenum='0'");
}

/*function delContentFile($v_type,$v_id,$sdate,$enname)
{
	$contentPath=getContentLink($v_type,$v_id,"",$sdate,$enname);
	if(!empty($contentPath)){
		delFile('..'.$contentPath);
	}
}*/

function zzget($txt)
{
	if($txt=='area'){$txt=sea_DATA."/admin/publisharea.txt";}
	elseif($txt=='year'){$txt=sea_DATA."/admin/publishyear.txt";}
	elseif($txt=='yuyan'){$txt=sea_DATA."/admin/publishyuyan.txt";}
	elseif($txt=='ver'){$txt=sea_DATA."/admin/verlist.txt";}
	else{return '<option value=0>无内容</option>';exit();}
	$cc = array();
	if(filesize($txt)>0)
	{
		$cc = file($txt);
	}
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($cc as &$cc)
	{
		$cc=str_replace("\r\n","",$cc);
		$str .= "<option value='".$cc."'>$cc</option>";
	}
	return $str;
}

?>