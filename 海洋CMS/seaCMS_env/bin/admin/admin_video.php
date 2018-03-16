<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/config.user.inc.php");

if(empty($action))
{
	$action = '';
}

if($action=="add")
{
$makePlayerSelectStr=str_replace("'","\'",makePlayerSelect(""));
$makeDownSelectStr = str_replace("'","\'",makedownSelect(""));

include(sea_ADMIN.'/templets/admin_video.htm');
exit();
}
elseif($action=="edit")
{
	$id = isset($id) && is_numeric($id) ? $id : 0;
	//读取影片信息
	$query = "select d.*,c.body as v_content,p.body as v_playdata,p.body1 as v_downdata from sea_data d left join `sea_content` c on c.v_id=d.v_id left join `sea_playdata` p on p.v_id=d.v_id  where d.v_id='$id' ";
	$vrow = $dsql->GetOne($query);
	if(!is_array($vrow))
	{
		ShowMsg("读取影片基本信息出错!","-1");
		exit();
	}
	$v_color = $vrow['v_color'];
	$vtype = $vrow['tid'];
	$vextratype = $vrow['v_extratype'];
	$vextrajqtype = $vrow['v_jq'];
	$v_playdata=$vrow['v_playdata'];
	$v_content=$vrow['v_content'];
	$v_downdata=$vrow['v_downdata'];
	$makePlayerSelectStr=str_replace("'","\'",makePlayerSelect(""));
	$makeDownSelectStr = str_replace("'","\'",makedownSelect(""));
	include(sea_ADMIN.'/templets/admin_video_edit.htm');
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
	$v_name = str_replace(array('\\','()','\''),'/',$v_name);
	$v_actor = htmlspecialchars(cn_substrR($v_actor,200));
	$v_actor = str_replace('%', ' ', $v_actor);
	if($v_actor=="" OR empty($v_actor)){$v_actor="内详";}
	$v_publishyear = empty($v_publishyear) ? date(Y) : intval($v_publishyear);
	$v_publisharea = cn_substrR($v_publisharea,10);
	$v_note = cn_substrR($v_note,30);
	$v_tags = cn_substrR(strtolower($v_tags),255);
	$v_tags = str_replace('%', ' ', $v_tags);
	$v_tags = htmlspecialchars($v_tags);
	$v_director = htmlspecialchars(cn_substrR($v_director,200));
	$v_director = str_replace('%', ' ', $v_director);
	if($v_director=="" OR empty($v_director)){$v_director="内详";}
	$v_lang = cn_substrR($v_lang,10);
	$v_commend =  empty($v_commend) ? 0 : intval($v_commend);
	$v_enname = empty($v_enname)?Pinyin($v_name):$v_enname;
	$v_letter = strtoupper(substr($v_enname,0,1));
	$v_extratype = $_POST[v_type_extra];
	$v_extrajqtype = $_POST[v_jqtype_extra];
	$v_longtxt = htmlspecialchars($_POST[v_longtxt]);
	$v_psd = $_POST[v_psd];

	$v_extratype=implode(",",$v_extratype); //获取扩展分类数组
	$v_jq=implode(",",$v_extrajqtype); //获取剧情分类数组
	
	$v_nickname = htmlspecialchars(cn_substrR($v_nickname,200));
	$v_reweek = implode(",",$v_reweek);
	$v_douban = empty($v_douban) ? 0 : $v_douban;
	$v_mtime = empty($v_mtime) ? 0 : $v_mtime;
	$v_imdb = empty($v_imdb) ? 0 : $v_imdb;
	$v_tvs = cn_substrR($v_tvs,200);
	$v_company = cn_substrR($v_company,200);
	$v_dayhit = empty($v_dayhit) ? 0 : intval($v_dayhit);
	$v_weekhit = empty($v_weekhit) ? 0 : intval($v_weekhit);
	$v_monthhit = empty($v_monthhit) ? 0 : intval($v_monthhit);
	$v_score = empty($v_score) ? 0 : intval($v_score);
	$v_scorenum = empty($v_scorenum) ? 0 : intval($v_scorenum);
	$v_daytime = $v_addtime;
	$v_weektime = $v_addtime;
	$v_monthtime = $v_addtime;
	$v_len = cn_substrR($v_len,200);
	$v_total = cn_substrR($v_total,200);
	
	if (substr($v_tags, -1) == ',') {
		$v_tags = substr($v_tags, 0, strlen($v_tags)-1);
	}
	$v_pic = cn_substrR($v_pic,255);
	$v_spic = cn_substrR($v_spic,255);
	$v_gpic = cn_substrR($v_gpic,255);
	$v_content=HtmlReplace(stripslashes($v_content),-1);
	switch (trim($acttype)) 
	{
		case "add":
			$insertSql = "insert into sea_data(tid,v_name,v_letter,v_state,v_topic,v_hit,v_money,v_rank,v_actor,v_color,v_publishyear,v_publisharea,v_pic,v_spic,v_gpic,v_addtime,v_note,v_tags,v_lang,v_score,v_scorenum,v_director,v_enname,v_commend,v_extratype,v_jq,v_nickname,v_reweek,v_douban,v_mtime,v_imdb,v_tvs,v_company,v_dayhit,v_weekhit,v_monthhit,v_len,v_total,v_daytime,v_weektime,v_monthtime,v_ver,v_psd,v_longtxt) values ('$tid','$v_name','$v_letter','$v_state','$v_topic','$v_hit','$v_money','$v_rank','$v_actor','$v_color','$v_publishyear','$v_publisharea','$v_pic','$v_spic','$v_gpic','$v_addtime','$v_note','$v_tags','$v_lang','$v_score','$v_scorenum','$v_director','$v_enname','$v_commend','$v_extratype','$v_jq','$v_nickname','$v_reweek','$v_douban','$v_mtime','$v_imdb','$v_tvs','$v_company','$v_dayhit','$v_weekhit','$v_monthhit','$v_len','$v_total','$v_daytime','$v_weektime','$v_monthtime','$v_ver','$v_psd','$v_longtxt')";
			if($dsql->ExecuteNoneQuery($insertSql))
			{
				$v_id = $dsql->GetLastID();
				$dsql->ExecuteNoneQuery("INSERT INTO `sea_content`(`v_id`,`tid`,`body`) VALUES ('$v_id','$tid','$v_content')");
				$dsql->ExecuteNoneQuery("INSERT INTO `sea_playdata`(`v_id`,`tid`,`body`,`body1`) VALUES ('$v_id','$tid','$v_playdata','$v_downdata')");
				addtags($v_tags,$v_id);
				clearTypeCache();
				
				//推送新增
				if($ping==1)	
				{   
					@include("../data/admin/ping.php");
					$u='http://';
					$u.=$_SERVER['HTTP_HOST'];
					$u.=getContentLink($tid,$v_id,"",date('Y-n',$v_addtime),$v_enname);
					$urls = array($u);
					$api = 'http://data.zz.baidu.com/urls?site=';
					$api.= $weburl;
					$api.= '&token=';
					$api.= $token;
					$ch = curl_init();
					$options =  array(
						CURLOPT_URL => $api,
						CURLOPT_POST => true,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_POSTFIELDS => implode("\n", $urls),
						CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
					);
					curl_setopt_array($ch, $options);
					$result = curl_exec($ch);
				}
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
			$v_extratype = $_POST[v_type_extra];
			$v_extrajqtype = $_POST[v_jqtype_extra];
			$v_extratype=implode(",",$v_extratype); //获取扩展分类数组
			$v_jq=implode(",",$v_extrajqtype); //获取扩展分类数组
	
			$updateSql = "tid = '$tid',v_name = '$v_name',v_letter = '$v_letter',v_state = '$v_state',v_topic = '$v_topic',v_hit = '$v_hit',v_money = '$v_money',v_rank = '$v_rank',v_actor = '$v_actor',v_color = '$v_color',v_publishyear = '$v_publishyear',v_publisharea = '$v_publisharea',v_pic = '$v_pic',v_spic = '$v_spic',v_gpic = '$v_gpic',v_note = '$v_note',v_tags = '$v_tags',v_lang='$v_lang',v_director='$v_director',v_enname='$v_enname',v_extratype='$v_extratype',v_jq='$v_jq',v_nickname='$v_nickname',v_reweek='$v_reweek',v_douban='$v_douban',v_mtime='$v_mtime',v_imdb='$v_imdb',v_tvs='$v_tvs',v_company='$v_company',v_dayhit='$v_dayhit',v_weekhit='$v_weekhit',v_monthhit='$v_monthhit',v_len='$v_len',v_total='$v_total',v_score='$v_score',v_scorenum='$v_scorenum',v_ver='$v_ver',v_psd='$v_psd',v_longtxt='$v_longtxt'";
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
			
			//推送新增
				if($ping==1)	
				{   
					@include("../data/admin/ping.php");
					$u='http://';
					$u.=$_SERVER['HTTP_HOST'];
					$u.=getContentLink($tid,$v_id,"",date('Y-n',$v_addtime),$v_enname);
					$urls = array($u);
					$api = 'http://data.zz.baidu.com/update?site=';
					$api.= $weburl;
					$api.= '&token=';
					$api.= $token;
					$ch = curl_init();
					$options =  array(
						CURLOPT_URL => $api,
						CURLOPT_POST => true,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_POSTFIELDS => implode("\n", $urls),
						CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
					);
					curl_setopt_array($ch, $options);
					$result = curl_exec($ch);
				}
				
			if($cfg_runmode=='0'){
				$trow = $dsql->GetOne("select ishidden from sea_type where tid=".$tid);
				if($trow['ishidden']==1){
					ShowMsg("影片更新成功",$v_back);
					exit();
				}else{
					ShowMsg("影片更新成功，转向生成页面！","admin_makehtml.php?action=single&id=".$v_id."&from=".urlencode($v_back));
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
	include(sea_ADMIN.'/templets/admin_video_main.htm');
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
	$str = '<select name="select1" id="select1"  onChange="v_publisharea.value=select1.value;v_publisharea.select()">';
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publisharea as &$area)
	{
		$area=str_replace("\r\n","",$area);
		if(!empty($areaId) && ($area==$areaId)) $str .= "<option value='".$area."' selected>$area</option>";
		else $str .= "<option value='".$area."'>$area</option>";
	}
	if(!in_array($areaId,$publisharea)&&!empty($areaId))
	$str .= "<option value='".$areaId."' selected>$areaId</option>";
	$str .= '</select><input type="text" style="width:60px;" value="'.$areaId.'" name="v_publisharea">';
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
	$str = '<select name="select2" id="select2" onChange="v_lang.value=select2.value;v_lang.select()">';
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publisharea as &$area)
	{
		$area=str_replace("\r\n","",$area);
		if(!empty($yuyanId) && ($area==$yuyanId)) $str .= "<option value='".$area."' selected>$area</option>";
		else $str .= "<option value='".$area."'>$area</option>";
	}
	if(!in_array($yuyanId,$publisharea)&&!empty($yuyanId))
	$str .= "<option value='".$yuyanId."' selected>$yuyanId</option>";
	$str .= '</select><input type="text" style="width:60px;" value="'.$yuyanId.'" name="v_lang">';
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
	$str = '<select name="select3" id="select3"  onChange="v_publishyear.value=select3.value;v_publishyear.select()">';
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($publishyear as &$year)
	{
		$year=str_replace("\r\n","",$year);
		if(!empty($yearId) && ($year==$yearId)) $str .= "<option value='".$year."' selected>$year</option>";
		else $str .= "<option value='".$year."'>$year</option>";
	}
	if(!in_array($yearId,$publishyear)&&!empty($yearId))
	$str .= "<option value='".$yearId."' selected>$yearId</option>";
	$str .= '</select><input type="text" style="width:60px;" value="'.$yearId.'" name="v_publishyear">';
	return $str;
}

function getVerSelect($selectName,$strSelect,$verId)
{
	$vertxt=sea_DATA."/admin/verlist.txt";
	$ver = array();
	if(filesize($vertxt)>0)
	{
		$ver = file($vertxt);
	}
	$str = '<select name="select4" id="select4"  onChange="v_ver.value=select4.value;v_ver.select()">';
	if(!empty($strSelect)) $str .= "<option value=''>".$strSelect."</option>";
	foreach($ver as &$ver)
	{
		$ver=str_replace("\r\n","",$ver);
		if(!empty($verId) && ($ver==$verId)) $str .= "<option value='".$ver."' selected>$ver</option>";
		else $str .= "<option value='".$ver."'>$ver</option>";
	}
	if(!in_array($verId,$ver)&&!empty($verId))
	$str .= "<option value='".$verId."' selected>$verId</option>";
	$str .= '</select><input type="text" style="width:60px;" value="'.$verId.'" name="v_ver">';
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
		if($flag==$player['flag']){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			$allstr .="<option value='".$player['flag']."' $selectstr>".$player['flag']."</option>";
			
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
		if($flag==$player['flag']){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			$allstr .="<option value='".$player['flag']."' $selectstr>".$player['flag']."</option>";
			
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