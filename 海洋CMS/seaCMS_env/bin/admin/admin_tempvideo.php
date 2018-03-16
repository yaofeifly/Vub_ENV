<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

if($action=="import")
{
	if(empty($e_id))
	{
		ShowMsg("请选择数据","-1");
		exit();
	}
	if(empty($type))
	{
		ShowMsg("请选择导入的分类","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$tid=$type;
	$sql="SELECT v_id,tid,v_name,v_letter,v_state,v_pic,v_actor,v_des,v_playdata,v_publishyear,v_publisharea,v_note,v_enname,v_lang,v_note,v_director FROM sea_temp WHERE v_id IN (".$ids.")";
	$dsql->SetQuery($sql);
	$dsql->Execute('import_list');
	echo "<div style='font-size:13px'><font color=red>从临时数据库导出数据开始：</font><br>";
	while($row=$dsql->GetAssoc('import_list'))
	{
		@session_write_close();
		$row['v_ismake'] = 0;
		$row['tid'] = $tid;
		echo $col->_into_database($row);
		$dsql->ExecuteNoneQuery("delete from `sea_temp` where v_id=".$row['v_id']);
		@ob_flush();
	    @flush();
	}
	alertMsg("导入数据完成","admin_tempvideo.php");
	exit();
}
elseif($action=="del")
{
	$dsql->ExecuteNoneQuery("delete from `sea_temp` where v_id='$id'");
	header("Location:$Pirurl");
	exit;
}
elseif($action=="delall")
{
	if($do=="all")
	{
		$dsql->ExecuteNoneQuery("delete from `sea_temp`");
	}else{
		if(empty($e_id))
		{
			ShowMsg("请选择需要删除的数据","-1");
			exit();
		}
		$ids = implode(',',$e_id);
		$dsql->ExecuteNoneQuery("delete from `sea_temp` where v_id in ($ids)");
	}
	header("Location:$Pirurl");
	exit;
}
elseif($action=="edit")
{
	$id = isset($id) && is_numeric($id) ? $id : 0;
	//读取影片信息
	$query = "select * from sea_temp where v_id='$id' ";
	$vrow = $dsql->GetOne($query);
	if(!is_array($vrow))
	{
		ShowMsg("读取影片基本信息出错!","-1");
		exit();
	}
	$vtype = $vrow['tid'];
	$v_playdata=$vrow['v_playdata'];
	$v_downdata=$vrow['v_downdata'];
	$v_content=$vrow['v_des'];
	$makePlayerSelectStr=str_replace("'","\'",makePlayerSelect(""));
	$makeDownSelectStr = str_replace("'","\'",makedownSelect(""));
	include(sea_ADMIN.'/templets/admin_tempvideo_edit.htm');
	exit();
}
elseif($action=="save")
{
	if(trim($v_name) == '')
	{
		ShowMsg("影片名不能为空！","-1");
		exit();
	}
	$v_playurl = empty($v_playurl) ? $v_playurl : repairUrlForm($v_playurl,$v_playfrom);
	$v_playdata=transferUrl($v_playfrom,$v_playurl);
	$v_state = empty($v_state) ? 0 : intval($v_state);
	$v_name = htmlspecialchars(cn_substrR($v_name,60));
	$v_actor = cn_substrR($v_actor,200);
	$v_director = cn_substrR($v_director,10);
	$v_publishyear = empty($v_publishyear) ? date(Y) : intval($v_publishyear);
	$v_publisharea = cn_substrR($v_publisharea,10);
	$v_note = cn_substrR($v_note,30);
	$v_enname = Pinyin($v_name);
	$v_letter = strtoupper(substr($v_enname,0,1));
	$v_pic = cn_substrR($v_pic,100);
	$v_lang = cn_substrR($v_lang,10);
	$v_content=HtmlReplace(stripslashes($v_content),-1);
	$v_id = isset($v_id) && is_numeric($v_id) ? $v_id : 0;
	$updateSql = "tid = '0',v_name = '$v_name',v_state = '$v_state',v_pic = '$v_pic',v_actor = '$v_actor',v_publishyear = '$v_publishyear',v_publisharea = '$v_publisharea',v_letter = '$v_letter',v_note = '$v_note',v_enname='$v_enname',v_playdata='$v_playdata',v_des='$v_content',v_director='$v_director',v_lang='$v_lang'";
	$updateSql = "update sea_temp set ".$updateSql." where v_id=".$v_id;
	if(!$dsql->ExecuteNoneQuery($updateSql))
	{
		ShowMsg('更新影片出错，请检查',-1);
		exit();
	}
		ShowMsg("影片更新成功",$v_back);
		exit();
}
else
{
	include(sea_ADMIN.'/templets/admin_tempvideo.htm');
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
	
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==stripslashes($player['flag'])){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			if($player['open']==1)
			$allstr .="<option value='".stripslashes($player['flag'])."' $selectstr>".stripslashes($player['flag'])."</option>";
			
	}
return $allstr;
}

function makedownSelect($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/downKinds.xml";
	
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==stripslashes($player['flag'])){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			$allstr .="<option value='".stripslashes($player['flag'])."' $selectstr>".stripslashes($player['flag'])."</option>";
			
	}
return $allstr;
}

function dwonUrlForm($downurl,$downfrom)
{
	if (count($downurl)!=count($downurl))
	{
		ShowMsg("未为每个数据选择数据来源！","-1");
		return ;
	}
	foreach($downurl as $k=>$v)
	{
	$v = trim($v);
	if($v!='')
	{
	$rstr=$rstr.repairStr($v,'down, ');
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
	}*/
	if($fromLen==0){
		$transferUrl=trim($fromArray[0])."$$".trim(rtrim($playArray[0],"#"));
		return  $transferUrl;
	}
	$resultStr="";
	for($i=1;$i<=$fromLen;$i++){
	$j=$i-1;
	if(empty($fromArray[$i]) and !empty($playArray[$j])){
		ShowMsg("来源没有填写完整！","-1");
		exit;
	}else{
		$resultStr=$resultStr.trim($fromArray[$i])."$$".trim(rtrim($playArray[$j],"#"))."$$$";
	}
	
	}
	$transferUrl=rtrim($resultStr,"$$$");
	return  $transferUrl;
}





?>