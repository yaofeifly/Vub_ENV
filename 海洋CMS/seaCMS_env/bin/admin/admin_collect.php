<?php
/*
	[seacms1.0] (C)2011-2012 seacms.net
*/
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
require_once(sea_INC."/collection.func.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}
$id = empty($id) ? 0 : intval($id);

if($action=="add")
{
	include(sea_ADMIN.'/templets/admin_collect_add.htm');
	exit();
}
elseif($action=="apizy")
{
	header("location: api.php");
}
elseif($action=="addsave")
{
	if(empty($cname))
	{
		ShowMsg("请填写采集名称！","-1");
		exit();
	}
	$inquery = " INSERT INTO `sea_co_config`(`cname`,`getlistnum`,`getconnum`)VALUES ('$cname','$getlistnum','$getconnum')";
	$rs = $dsql->ExecuteNoneQuery($inquery);
	if(!$rs)
	{
		ShowMsg("保存信息时出现错误！".$dsql->GetError(),"-1");
		exit();
	}
	$cid = $dsql->GetLastID();
	header("Location:?action=addrule&id=$cid");
	die;
}
if($action=="addrule")
{
	if($step==2){
		if(empty($itemname))
		{
			ShowMsg("请填写采集名称！","-1");
			exit();
		}
		$removecode = implode('|',$removecode);
		$listconfig = "{seacms:listrule cid=\"$id\" tname=\"$itemname\" intodatabase=\"$intodatabase\" getherday=\"$getherday\" siteurl=\"$siteurl\" playfrom=\"$playfrom\" downfrom=\"$downfrom\" autocls=\"$autocls\" classid=\"$classid\" removecode=\"$removecode\" inithit=\"$inithit\" pageset=\"$pageset\" pageurl0=\"$pageurl0\" pageurl1=\"$pageurl1\"  pageurl2=\"$pageurl2\" istart=\"$istart\" iend=\"$iend\" reverse=\"$reverse\"}";
		include(sea_ADMIN.'/templets/admin_collect_ruleadd2.htm');
		exit();
	}elseif($step==test){
		$listconfig = urldecode($listconfig);
		$listconfig.="
			{seacms:lista}$lista{/seacms:lista}
			{seacms:listb}$listb{/seacms:listb}
			{seacms:mlinka}$mlinka{/seacms:mlinka}
			{seacms:mlinkb}$mlinkb{/seacms:mlinkb}
			{seacms:picmode}$picmode{/seacms:picmode}
			{seacms:pica}$pica{/seacms:pica}
			{seacms:picb}$picb{/seacms:picb}
			{seacms:pic_trim}$pic_trim{/seacms:pic_trim}
{/seacms:listrule}\r\n";
		$tmplistconfig = stripslashes($listconfig);
		$links=array();
		$links = Testlists($tmplistconfig,$coding,$sock);
		include(sea_ADMIN.'/templets/admin_collect_ruleadd2test.htm');
		exit();
	}elseif($step==3){
		$listconfig = urldecode($listconfig);
		$row = $dsql->GetOne("Select tid From `sea_co_type` where isok=0 And tname like '$itemname' ");
		if(!is_array($row)){
			$inquery = " INSERT INTO `sea_co_type`(`cid`,`tname`,`siteurl`,`getherday`,`coding`,`sock`,`playfrom`,`autocls`,`classid`,`addtime`,`listconfig`)VALUES ('$id','$itemname','$siteurl','$getherday','$coding','$sock','$playfrom','$autocls','$classid','".time()."','$listconfig')";
			$rs = $dsql->ExecuteNoneQuery($inquery);
			if(!$rs)
			{
				ShowMsg("保存信息时出现错误！".$dsql->GetError(),"-1");
				exit();
			}
			$tid = $dsql->GetLastID();
		}else{
			$tid=$row['tid'];
			$upquery = "update `sea_co_type` set `cid`='$id',`tname`='$itemname',`siteurl`='$siteurl',`getherday`='$getherday',`coding`='$coding',`sock`='$sock',`playfrom`='$playfrom',`autocls`='$autocls',`classid`='$classid',`addtime`='".time()."',`listconfig`='$listconfig' where tid='$tid'";
			$dsql->ExecuteNoneQuery($upquery);
		}
		include(sea_ADMIN.'/templets/admin_collect_ruleadd3.htm');
		exit();
	}elseif($step==4){
		if(!empty($fields)) $fds = implode(',',$fields); else $fds="";
		if(empty($serveron)) $serveron=2;
		$itemconfig = "{seacms:itemconfig fields=\"$fds\" splay=\"$splay\" serveron=\"$serveron\" playgetsrc=\"$playgetsrc\" getpart=\"$getpart\" intodatabase=\"$intodatabase\"}";
		foreach($item as $field){
			$stra = $GLOBALS[$field."a"];
			$strb = $GLOBALS[$field."b"];
			$trimstr = $GLOBALS[$field."_trim"];
			$itemconfig .="
			{seacms:".$field."a}$stra{/seacms:".$field."a}";
			$itemconfig .="
			{seacms:".$field."b}$strb{/seacms:".$field."b}";
			$itemconfig .="
			{seacms:".$field."_trim}$trimstr{/seacms:".$field."_trim}";
		}
		$itemconfig .="
		{/seacms:itemconfig}\r\n";
		$upquery = "update `sea_co_type` set `itemconfig`='$itemconfig' where tid='$tid'";
		$dsql->ExecuteNoneQuery($upquery);
		include(sea_ADMIN.'/templets/admin_collect_ruleadd4.htm');
		exit();
	}elseif($step==5){
		$dsql->ExecuteNoneQuery("Update `sea_co_type` set isok='1' where tid='$tid' ");
		ShowMsg("添加采集规则成功！","admin_collect.php");
		exit();
	}else{
		include(sea_ADMIN.'/templets/admin_collect_ruleadd.htm');
		exit();
	}
}
elseif($action=="editrulesingle")
{
	$query = "select * from `sea_co_type` where tid='$id' ";
	$row = $dsql->GetOne($query);
	if(!is_array($row))
	{
		ShowMsg("读取配置基本信息出错!","-1");
		exit();
	}
	$labelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($labelRule,$row[listconfig],$ar);
	$attrStr = trim($ar[1][0]);
	$loopstr=$ar[2][0];
	$atd=parseAttr($attrStr);
	if($step==2){
		$removecode = implode('|',$removecode);
		$listconfig = "{seacms:listrule cid=\"$id\" tname=\"$itemname\" intodatabase=\"$intodatabase\" getherday=\"$getherday\" siteurl=\"$siteurl\" playfrom=\"$playfrom\" downfrom=\"$downfrom\" autocls=\"$autocls\" classid=\"$classid\" removecode=\"$removecode\" inithit=\"$inithit\" pageset=\"$pageset\" pageurl0=\"$pageurl0\" pageurl1=\"$pageurl1\" pageurl2=\"$pageurl2\" istart=\"$istart\" iend=\"$iend\" reverse=\"$reverse\"}";
		include(sea_ADMIN.'/templets/admin_collect_ruleedit2.htm');
		exit();
	}elseif($step==test){
		$listconfig = urldecode($listconfig);
		$listconfig.="
			{seacms:lista}$lista{/seacms:lista}
			{seacms:listb}$listb{/seacms:listb}
			{seacms:mlinka}$mlinka{/seacms:mlinka}
			{seacms:mlinkb}$mlinkb{/seacms:mlinkb}
			{seacms:picmode}$picmode{/seacms:picmode}
			{seacms:pica}$pica{/seacms:pica}
			{seacms:picb}$picb{/seacms:picb}
			{seacms:pic_trim}$pic_trim{/seacms:pic_trim}
{/seacms:listrule}\r\n";
		$tmplistconfig = stripslashes($listconfig);
		$links=array();
		$links = Testlists($tmplistconfig,$coding,$sock);
		include(sea_ADMIN.'/templets/admin_collect_ruleadd2test.htm');
		exit();
	}elseif($step==3){
		$labelitemRule = buildregx("{seacms:itemconfig(.*?)}(.*?){/seacms:itemconfig}","is");
		preg_match_all($labelitemRule,str_replace('&nbsp;','#n#',$row[itemconfig]),$itemar);
		$itemattrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$itemar[1][0]));
		$itemloopstr=$itemar[2][0];
		$atr=parseAttr($itemattrStr);
		$flds=$atr["fields"];
		$listconfig = urldecode($listconfig);
			$upquery = "update `sea_co_type` set `tname`='$itemname',`siteurl`='$siteurl',`getherday`='$getherday',`coding`='$coding',`sock`='$sock',`playfrom`='$playfrom',`autocls`='$autocls',`classid`='$classid',`addtime`='".time()."',`listconfig`='$listconfig' where tid='$id'";
			$dsql->ExecuteNoneQuery($upquery);
		include(sea_ADMIN.'/templets/admin_collect_ruleedit3.htm');
		exit();
	}elseif($step==4){
		if(!empty($fields)) $fds = implode(',',$fields); else $fds="";
		if(empty($serveron)) $serveron=2;
		$itemconfig = "{seacms:itemconfig fields=\"$fds\" splay=\"$splay\" serveron=\"$serveron\" playgetsrc=\"$playgetsrc\" getpart=\"$getpart\" intodatabase=\"$intodatabase\"}";
		foreach($item as $field){
			$stra = ${$field."a"};
			$strb = ${$field."b"};
			$trimstr = ${$field."_trim"};
			$itemconfig .="
			{seacms:".$field."a}$stra{/seacms:".$field."a}";
			$itemconfig .="
			{seacms:".$field."b}$strb{/seacms:".$field."b}";
			$itemconfig .="
			{seacms:".$field."_trim}$trimstr{/seacms:".$field."_trim}";
		}
		$itemconfig .="
		{/seacms:itemconfig}\r\n";
		$itemconfig=str_replace('#n#','&nbsp;',$itemconfig);
		$upquery = "update `sea_co_type` set `itemconfig`='$itemconfig' where tid='$id'";
		$dsql->ExecuteNoneQuery($upquery);
		include(sea_ADMIN.'/templets/admin_collect_ruleedit4.htm');
		exit();
	}elseif($step==5){
		$dsql->ExecuteNoneQuery("Update `sea_co_type` set isok='1' where tid='$tid' ");
		ShowMsg("更新采集规则成功！","admin_collect.php");
		exit();
	}else{
		include(sea_ADMIN.'/templets/admin_collect_ruleedit.htm');
		exit();
	}
}
elseif($action=="filters")
{
	include(sea_ADMIN.'/templets/admin_collect_filters.htm');
	exit();
}
elseif($action=="filtersadd")
{
	include(sea_ADMIN.'/templets/admin_collect_filtersadd.htm');
	exit();
}
elseif($action=="filtersedit")
{
	include(sea_ADMIN.'/templets/admin_collect_filtersedit.htm');
	exit();	
}
elseif($action=="filterssave")
{
//	print_r($_POST); die();
	if(empty($id))
	{
		if(!$uesMode)
		$dsql->ExecNoneQuery("insert into sea_co_filters(Name,rColumn,uesMode,sFind,sReplace,Flag) values ('".$name."',$rColumn,0,'".$sFind."','".$sReplace."',$Flag)");
		else 
		$dsql->ExecNoneQuery("insert into sea_co_filters(Name,rColumn,uesMode,sFind,sReplace,Flag,sStart,sEnd) values('".$name."',$rColumn,1,'".$sFind."','".$sReplace."',$Flag,'".$sStart."','".$sEnd."')");
		ShowMsg('添加成功！', 'admin_collect.php?action=filters');
//		exit();
	}
	else
	{
		if(!$uesMode)
		$dsql->ExecNoneQuery("update sea_co_filters set Name='".$name."',rColumn=".$rColumn.",uesMode=".$uesMode.",sFind='".$sFind."',sReplace='".$sReplace."',Flag=".$Flag." where ID=".$id );
		else 
		$dsql->ExecNoneQuery("update sea_co_filters set Name='".$name."',rColumn=".$rColumn.",uesMode=".$uesMode.",sFind='".$sFind."',sReplace='".$sReplace."',Flag=".$Flag.",sStart='".$sStart."',sEnd='".$sEnd."' where ID=".$id );
		ShowMsg('修改成功！', 'admin_collect.php?action=filters');		
//		exit();
	}
}
elseif ($action=="filtersdel")
{
	$sql = "delete from sea_co_filters where ID=".$id;
	$dsql ->ExecNoneQuery($sql);
	ShowMsg('删除成功！', 'admin_collect.php?action=filters');	
}
elseif ($action=="filtersdelall")
{
	$ids=$_POST['id'];
	foreach ($ids as $id)
	{
		$ida.="$id,";
	}
	$ida = rtrim($ida,",");
	$sql = "delete from sea_co_filters where ID in ($ida)";
	$dsql ->ExecNoneQuery($sql);
	ShowMsg('删除成功！', 'admin_collect.php?action=filters');	
	
}
elseif($action=="delrulesingle")
{
	$dsql->ExecuteNoneQuery("delete from sea_co_config where tid=".$id);
	$dsql->ExecuteNoneQuery("delete from sea_co_type where tid=".$id);
	$dsql->ExecuteNoneQuery("delete from sea_co_url where tid=".$id);
	ShowMsg("规则删除成功","admin_collect.php");
	exit();
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的规则","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_co_config where cid in (".$ids.")");
	$dsql->ExecuteNoneQuery("delete from sea_co_type where tid in(".$ids.")");
	$dsql->ExecuteNoneQuery("delete from sea_co_url where tid in(".$ids.")");
	ShowMsg("规则删除成功","admin_collect.php");
	exit();
}
elseif($action=="customercls")
{
	include(sea_ADMIN.'/templets/admin_collect_customercls.htm');
	exit();
}
elseif($action=="getlistsingle")
{
	getlistsbyid($id,$cid,$action2,$index);
}
elseif($action=="getconsingle")
{
	getconbyid($id,$cid,$action2,$index);
}
elseif($action=="getlistall")
{
	getlistall($id);
}
elseif($action=="getconall")
{
	getconall($id);
}
elseif($action=="getcontestsingle")
{
	$sql = "select coding,sock,listconfig from  sea_co_type where tid=$id";
	$vrow = $dsql->GetOne($sql);
	$coding=$vrow['coding'];
	$sock=$vrow['sock'];
	$listconfig=$vrow['listconfig'];
	$links=array();
	$links = Testlists($listconfig,$coding,$sock);	
	if(isset($links[0])) 
	{
		$url=$links[0]['url'];
		$pic=$links[0]['pic'];
	}
	include(sea_ADMIN.'/templets/admin_collect_getcontest.htm');
	exit();
}
elseif($action=="resetlist")
{
	$dsql->ExecuteNoneQuery("update sea_co_url set succ='0' where cid='$id'");
	header("Location:admin_collect.php");
	die;
}
elseif($action=="reseterr")
{
	$dsql->ExecuteNoneQuery("update sea_co_url set err='0' where cid='$id'");
	header("Location:admin_collect.php");
	die;
}
elseif($action=="clearlist")
{
	$dsql->ExecuteNoneQuery("delete from sea_co_url where cid='$id'");
	header("Location:admin_collect.php");
	die;
}
elseif($action=="editrule")
{
	$row=$dsql->GetOne("select * from sea_co_config where cid='$id'");
	include(sea_ADMIN.'/templets/admin_collect_edit.htm');
	exit();
}
elseif($action=="editsave")
{
	if(empty($cname))
	{
		ShowMsg("请填写采集名称！","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_co_config set cname='$cname',getlistnum='$getlistnum',getconnum='$getconnum' where cid='$id'");
	header("Location:admin_collect.php");
	die;
}
elseif($action=="delrule")
{
	$dsql->ExecuteNoneQuery("delete from sea_co_config where cid='$id'");
	$dsql->ExecuteNoneQuery("delete from sea_co_type where cid='$id'");
	$dsql->ExecuteNoneQuery("delete from sea_co_url where cid='$id'");
	header("Location:admin_collect.php");
	die;
}
elseif($action=="exportrule")
{
	$row=$dsql->GetOne("select * from sea_co_config where cid='$id'");
	$rule['config']=$row;
	$dsql->SetQuery("Select * From `sea_co_type` where cid='$id' order by tid");
	$dsql->Execute('cotype_list');
	while($rowt=$dsql->GetObject('cotype_list'))
	{
		$tid=$rowt->tid;
		$rowr=$dsql->GetOne("select tid,cid,tname,siteurl,getherday,playfrom,autocls,classid,coding,sock,addtime,cjtime,listconfig,itemconfig,isok from sea_co_type where tid='$tid'");
		$rule['type'][$tid]=$rowr;
	}
		//print_r($rule);

	
	$export = serialize($rule);
	$export = "BASE64:".base64_encode($export).":END";
	unset($rule);
	include(sea_ADMIN.'/templets/admin_collect_exportrule.htm');
	exit();
}
elseif($action=="importrule")
{
	include(sea_ADMIN.'/templets/admin_collect_importrule.htm');
	exit();
}
elseif($action=="importok")
{
	$importrule = trim($importrule);
	if(empty($importrule))
	{
		ShowMsg("规则内容为空！","-1");
		exit();
	}
	//对Base64格式的规则进行解码
	if(m_ereg('^BASE64:',$importrule))
	{
		if(!m_ereg(':END$',$importrule))
		{
			ShowMsg('该规则不合法，Base64格式的采集规则为：BASE64:base64编码后的配置:END !','-1');
			exit();
		}
		$importrules = explode(':',$importrule);
		$importrule = $importrules[1];
		$importrule = unserialize(base64_decode($importrule)) OR  die('配置字符串有错误！'); 
	}
	else
	{
			ShowMsg('该规则不合法，Base64格式的采集规则为：BASE64:base64编码后的配置:END !','-1');
			exit();
	}
	if(!is_array($importrule) || !is_array($importrule['config']) || !is_array($importrule['type']))
	{
		ShowMsg('该规则不合法，无法导入!','-1');
		exit();
	}
	$data = $importrule['config'];
	unset($data['cid']);
	$data['cname'].="(导入时间:".date("Y-m-d H:i:s").")";
	$sql = si("sea_co_config",$data,1);
	$dsql->ExecuteNoneQuery($sql);
	$cid = $dsql->GetLastID();
	if (!empty($importrule['type'])){
		foreach ($importrule['type'] as $type){
			unset($type['tid']);
			$type['cid'] = $cid;
			$type['addtime'] = time();
			$type['cjtime'] = '';
			$data = $type;
			$sql = si("sea_co_type",$data,1);
			
			$dsql->ExecuteNoneQuery2($sql);
		}
	}
	ShowMsg('成功导入规则!','admin_collect.php');
	exit;
}
elseif($action=="copyrulesingle")
{
	$row = $dsql->GetOne("Select * From `sea_co_type` where tid='$id'");
	foreach($row as $k=>$v)
	{
		if(!isset($$k))
		{
			$$k = addslashes($v);
		}
	}
	$tname=$tname."[复制]";
	$addtime=time();
	$inquery = " INSERT INTO `sea_co_type`(`cid`,`tname`,`siteurl`,`getherday`,`playfrom`,`autocls`,`classid`,`coding`,`sock`,`addtime`,`listconfig`,`itemconfig`,`isok`)
               VALUES ('$cid','$tname','$siteurl','$getherday','$playfrom','$autocls','$classid','$coding','$sock','$addtime','$listconfig','$itemconfig','$isok'); ";
	$dsql->ExecuteNoneQuery($inquery);
	header("Location:$Pirurl");
	die;
}
elseif($action=="list")
{
	include(sea_ADMIN.'/templets/admin_collect_list.htm');
	exit();
}
elseif($action=="customernewcls")
{
	if(empty($clsname))
	{
		ShowMsg("要转换有分类名还没填写","-1");
		exit();
	}
	if(empty($tid))
	{
		ShowMsg("未选择影射到系统分类","-1");
		exit();
	}
	$in_query = "insert into `sea_co_cls`(clsname,sysclsid) Values('$clsname','$tid')";
	if(!$dsql->ExecuteNoneQuery($in_query))
	{
		ShowMsg("增加自定义分类失败，请检查您的输入是否存在问题！","-1");
		exit();
	}
	ShowMsg("成功创建一个自定义分类！","?action=customercls");
	exit();
}
elseif($action=="customersavecls")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要更改的分类","?action=customercls");
		exit();
	}
	foreach($e_id as $id)
	{
		$clsname=$_POST["clsname$id"];
		$tid=$_POST["tid$id"];
		if(empty($clsname))
		{
			ShowMsg("分类名称没有填写，请返回检查","-1");
			exit();
		}
		$dsql->ExecuteNoneQuery("update sea_co_cls set clsname='$clsname',sysclsid='$tid' where id=".$id);
	}
	header("Location:?action=customercls");
	die;
}
elseif($action=="customerdelallcls")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要更改的分类","admin_type.php");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_co_cls where id in(".$ids.")");
	header("Location:?action=customercls");
	die;
}
elseif($action=="customerdelcls")
{
	$dsql->ExecuteNoneQuery("delete from sea_co_cls where id=".$id);
	header("Location:?action=customercls");
	die;
}
elseif($action=="tempdatabase")
{
	include(sea_ADMIN.'/templets/admin_co_tempdatabase.htm');
	exit();
}
elseif($action=="editVideo")
{
	$id = isset($id) && is_numeric($id) ? $id : 0;
	//读取影片信息
	$query = "select * from sea_co_data where v_id='$id' ";
	$vrow = $dsql->GetOne($query);
	if(!is_array($vrow))
	{
		ShowMsg("读取影片基本信息出错!","-1");
		exit();
	}
	$vtype = $vrow['tid'];
	$v_playdata=$vrow['v_playdata'];
	$v_content=$vrow['v_des'];
	$makePlayerSelectStr=str_replace("'","\'",makePlayerSelect(""));
	include(sea_ADMIN.'/templets/admin_co_editvideo.htm');
	exit();
}
elseif($action=="saveVideo")
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
	$v_playdata=transferUrl($v_playfrom,$v_playurl);
	$tid = empty($v_type) ? 0 : intval($v_type);
	$v_state = empty($v_state) ? 0 : intval($v_state);
	$v_hit = empty($v_hit) ? 0 : intval($v_hit);
	$v_addtime = time();
	$v_name = htmlspecialchars(cn_substrR($v_name,60));
	$v_letter = strtoupper(substr(Pinyin($v_name),0,1));
	$v_actor = cn_substrR($v_actor,200);
	$v_publishyear = empty($v_publishyear) ? date(Y) : intval($v_publishyear);
	$v_publisharea = cn_substrR($v_publisharea,10);
	$v_note = cn_substrR($v_note,30);
	$v_tags = cn_substrR(strtolower(addslashes($v_tags)),30);
	$v_tags = str_replace('，', ',', $v_tags);
	$v_tags = str_replace(',,', ',', $v_tags);	
	if (substr($v_tags, -1) == ',') {
		$v_tags = substr($v_tags, 0, strlen($v_tags)-1);
	}
	$v_pic = cn_substrR($v_pic,250);
	$v_content=HtmlReplace(stripslashes($v_content),-1);
	switch (trim($acttype)) 
	{
		case "edit":
			$updateSql = "tid = '$tid',v_name = '$v_name',v_letter = '$v_letter',v_state = '$v_state',v_hit = '$v_hit',v_actor = '$v_actor',v_publishyear = '$v_publishyear',v_publisharea = '$v_publisharea',v_pic = '$v_pic',v_note = '$v_note',v_tags = '$v_tags',v_des = '$v_content',v_playdata = '$v_playdata'";
			if(!empty($isupdatetime)) $updateSql .= ",v_addtime='$v_addtime'";
			$updateSql = "update sea_co_data set ".$updateSql." where v_id=".$v_id;
			if(!$dsql->ExecuteNoneQuery($updateSql))
			{
				ShowMsg('更新影片出错，请检查',-1);
				exit();
			}
			ShowMsg("影片更新成功",$v_back);
			exit();
			break;
	}
}
elseif($action=="delTempData")
{
	$dsql->ExecuteNoneQuery("delete from sea_co_data where v_id=".$id);
	header("Location:?action=tempdatabase");
	die;
}
elseif($action=="delallTempData")
{
	if($do=="all")
	{
		$dsql->ExecuteNoneQuery("delete from sea_co_data");
		ShowMsg("影片全部删除成功","?action=tempdatabase");
		exit();
	}
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的影片","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_co_data where v_id in(".$ids.")");
	ShowMsg("影片删除成功","?action=tempdatabase");
	exit();
}
elseif($action=="import")
{
	$vtype=$type;
	if(!empty($smode)){
		$numPerPage=30;
		$page = isset($page) ? intval($page) : 1;
		if($page==0) $page=1;
		$idsArray=array();
		if($smode=='all')
		{
			$wherestr="";
			$start = ($page-1)*$numPerPage;
		}else{
			$wherestr=" where v_inbase='0'";
			$start = 0;
		}
		if(!isset($TotalResult))
		{
			$csqlStr="select count(*) as dd from `sea_co_data` ".$wherestr;
			$rowc = $dsql->GetOne($csqlStr);
			if(is_array($rowc)){
				$TotalResult = $rowc['dd'];
			}else{
				$TotalResult = 0;
			}
		}
		$TotalPage = isset($TotalPage)?$TotalPage:ceil($TotalResult/$numPerPage);
		if ($page>$TotalPage) $page=$TotalPage;
		$sql="select v_id from `sea_co_data` ".$wherestr." limit $start,$numPerPage";
		$dsql->SetQuery($sql);
		$dsql->Execute('import_co_data');
		while($row=$dsql->GetObject('import_co_data'))
		{
			$idsArray[]=$row->v_id;
		}
		if(count($idsArray)>0){
			echo "正在导入影片,当前是第<font color='red'>".$page."</font>页,共<font color='red'>".$TotalPage."</font>页,共<font color='red'>".$TotalResult."</font>部影片<hr />";
			import2Base($idsArray,$vtype);
			unset($idsArray);
			echo "<br>暂停3秒后继续导入<script language=\"javascript\">setTimeout(function (){location.href='?action=".$action."&smode=".$smode."&type=".$vtype."&page=".($page+1)."&TotalPage=".$TotalPage."&TotalResult=".$TotalResult."';},3000);</script>";
		}else{
			alertMsg("导入完成",'admin_collect.php?action=tempdatabase');
		}
		
	}else{
		if(empty($e_id))
		{
			ShowMsg("请选择需要导入的影片","-1");
			exit();
		}
		import2Base($e_id,$vtype);
		alertMsg ("导入完成",'admin_collect.php?action=tempdatabase');
	}
}
else
{
	include(sea_ADMIN.'/templets/admin_collect.htm');
	exit();
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

function getlistsbyid($id,$cid='',$action2='',$index='')
{
	@session_write_close();
	if($id==0) return false;
	global $dsql,$page,$pcount;
	$row = $dsql->GetOne("Select t.coding,t.sock,t.playfrom,t.autocls,t.classid,t.getherday,t.listconfig,c.cid,c.getlistnum from `sea_co_type` t left join `sea_co_config` c on c.cid=t.cid where t.tid='$id'");
	$listconfig=$row['listconfig'];
	$coding=$row['coding'];
	$sock=$row['sock'];
	$playfrom=$row['playfrom'];
	$classid=$row['classid'];
	$autocls=$row['autocls'];
	$getherday=$row['getherday'];
	$cid=$row['cid'];
	$getlistnum=$row['getlistnum'];
	$labelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($labelRule,$listconfig,$ar);
	$attrStr = trim($ar[1][0]);
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	$currentPage = empty($page) ? 1 : intval($page);
	$lista=getrulevalue($loopstr,"lista");
	$listb=getrulevalue($loopstr,"listb");
	$mlinka=getrulevalue($loopstr,"mlinka");
	$mlinkb=getrulevalue($loopstr,"mlinkb");
	$picmode=getrulevalue($loopstr,"picmode");
	$pica=getrulevalue($loopstr,"pica");
	$picb=getrulevalue($loopstr,"picb");
	$pic_trim=getrulevalue($loopstr,"pic_trim");
	//处理页面链接
	$pageset=$attrDictionary["pageset"];
	if($pageset==0){
		$pageurl0=$attrDictionary["pageurl0"];
		$istart=0;
		$iend=0;
		$dourl[0][0]=$pageurl0;
	}else{
		$pageurl1=$attrDictionary["pageurl1"];
		$pageurl2=$attrDictionary["pageurl2"];
		$istart=$attrDictionary["istart"];
		$iend=$attrDictionary["iend"];
		$pageurlarr=GetUrlFromListRule($pageurl1,$pageurl2,$istart,$iend);
		$dourl=$pageurlarr;
	}
	$k=count($dourl);
	$getpagebegin = $getlistnum*($currentPage-1);
	$getpageend = $getpagebegin+$getlistnum-1;
	if ($getpageend>$k) $getpageend=$k-1;
	$pcount=ceil($k/$getlistnum);
	if($currentPage>$pcount){
		if(empty($action2)){
			echo "<div style='font-size:13px'>采集列表完成，转向内容采集页面</div>";
			echo "<script>location='?action=getconsingle&id=$id';</script>";
			exit;
		}else{
			echoListSuspend2($cid,$index+1);
			exit;
		}
	}

	echo "<div style='font-size:13px'>正在采集分类ID{$id}的列表：<b>第 $getpagebegin 页 ～ 第 $getpageend 页</b>\n";
	//调用进度条表格
	include_once('js/progress');
	$echo_id =1;	//为了显示第几条
	$per_count = null;
	if ($getpagebegin<=$k)
	{
		for ($i=$getpagebegin; $i<=$getpageend; $i++)
		{
			$listurl =$dourl[$i][0];
			$html = cget($listurl,$sock);
			$html = ChangeCode($html,$coding);
			if($html=='')
			{
				echo "读取网址： $listurl 时失败！\r\n";
			}
			if( trim($lista) !='' && trim($listb) != '' )
			{
				$areabody = $lista.'[var:区域]'.$listb;
				$html = GetHtmlArea('[var:区域]',$areabody,$html);
			}
			if( trim($mlinka) !='' && trim($mlinkb) != '' )
			{
				$linkrulex = $mlinka.'(.*)'.$mlinkb;
				$link = GetHtmlarray($html,$linkrulex);
				foreach($link as $key=>$s)
				{
					$links[$key][url] = FillUrl($listurl,$s);
				}
			}
			if(trim($picmode)==1 && trim($pica) !='' && trim($picb) != '' )
			{
				$picrulex = $pica.'(.*)'.$picb;
				$piclink = GetHtmlarray($html,$picrulex);
				foreach($piclink as $key=>$s)
				{
					if(!empty($pic_trim)) $s=Gettrimvalue($pic_trim,$s);
					$links[$key][pic] = FillUrl($listurl,$s);
				}
			}
			$per_count = !$per_count?count($links):$per_count;
			if (!empty($links))
			{
				for ($j=0;$j<count($links);$j++)
				{
					$url=$links[$j][url];
					$pic=$links[$j][pic];
					$rowt=$dsql->GetOne("Select uid from `sea_co_url` where tid='$id' and url='$url'");
					if(is_array($rowt)){
						$dsql->ExecuteNoneQuery("update `sea_co_url` set succ='0',err='0',pic='$pic' where uid=".$rowt['uid']);
						echo "{$echo_id}. $url\t<font color=red>已存在，更新采集数据</font><br>";
					}else{
						$sql="insert into `sea_co_url`(cid,tid,url,pic) values ('$cid','$id','$url','$pic')";
						if($dsql->ExecuteNoneQuery($sql))
							echo "{$echo_id}. $url\t<font color=red>保存成功</font><br>";
						else
							echo "{$echo_id}. $url\t<font color=red>保存失败</font><br>";
					}
					$echo_id++;

				//以下显示进度条-----------
				$pro_count = $per_count * ($getpageend-$getpagebegin+1);
				$last_pro = isset($progress)?$progress:0;
				$progress = round($echo_id/$pro_count * 100);
				@ob_flush();
				@flush();
				if ($progress>$last_pro) echo "<script>progress($progress);</script>";
				}//for
			}//if
			unset($links);
		}
	}
	echoListSuspend($id,$currentPage+1,ceil($iend/$getlistnum),$cid,$action2,$index);
}

function echoListSuspend($id,$curPage,$pCount,$cid,$action2,$index)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=getlistsingle&id=".$id."&cid=".$cid."&page=".$curPage."&pcount=".$pCount."&action2=".$action2."&index=".$index."';}</script>";
}

function echoListSuspend2($id,$index)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续采集下个分类<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=getlistall&id=".$id."&index=".$index."';}</script>";
}

function getconbyid($id,$cid='',$action2='',$index='')
{
	@session_write_close();
	if($id==0) return false;
	global $dsql,$col,$page,$pcount,$cfg_gatherset;
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$row = $dsql->GetOne("Select t.coding,t.sock,t.playfrom,t.autocls,t.classid,t.getherday,t.listconfig,t.itemconfig,c.cid,c.getconnum from `sea_co_type` t left join `sea_co_config` c on c.cid=t.cid where t.tid='$id'");
	$listconfig=$row['listconfig'];
	$itemconfig=$row['itemconfig'];
	$getconnum=$row['getconnum'];
	//列表规则
	$listlabelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($listlabelRule,$listconfig,$listar);
	$listattrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$listar[1][0]));
	$listloopstr=$listar[2][0];
	$listattrDictionary=parseAttr($listattrStr);
	$reverse=$listattrDictionary["reverse"];
	//页面规则
	$labelRule = buildregx("{seacms:itemconfig(.*?)}(.*?){/seacms:itemconfig}","is");
	preg_match_all($labelRule,$itemconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	//读出列表url
	$wheresql=" where succ='0' and err<3 and tid='$id'";
	$csql="select count(*) as dd from `sea_co_url` $wheresql";
	$rowd = $dsql->GetOne($csql);
	if(is_array($rowd)){
	$TotalResult = $rowd['dd'];
	}else{
	$TotalResult = 0;
	}
	if(empty($pcount)) $pcount=ceil($TotalResult/$getconnum);
	$sqlStr="select * from `sea_co_url` $wheresql ".($reverse?"order by uid desc":"")." limit 0,$getconnum ";
	if($TotalResult<$getconnum)$lastpernum=$TotalResult;
	else $lastpernum=$getconnum;
	if($page>$pcount){
		if(empty($action2)){
			echo "<div style='font-size:13px'>采集内容完成\n\n<a href='admin_collect.php'>返回采集列表页</a></div>";
			exit;
		}else{
			echoConSuspend2($cid,$index+1);
			exit;
		}
	}
	echo "<div style='font-size:13px'>正在采集分类ID{$id}的内容<b>第 $page 页,共 $pcount 页 ,本次采集 $lastpernum 条</b>\n";

	//调用进度条表格
	include_once('js/progress');

	if($TotalResult!=0){
		$echo_id=1;	//为显示序号
		$pro_count = $lastpernum;	//为显示进度条
		$dsql->SetQuery($sqlStr);
		$dsql->Execute('url_list');
		while($rowt=$dsql->GetAssoc('url_list'))
		{
			echo $col->collect_db($listattrDictionary,$attrDictionary,$row,$rowt,$loopstr,$echo_id);
			$echo_id++;
			 //以下显示进度条-----------
			$last_pro = isset($progress)?$progress:0;
			$progress = round($echo_id/$pro_count * 100);
			@ob_flush();
			@flush();
			if ($progress>$last_pro) echo "<script>progress($progress);</script>";
		}
	}
	unset($attrDictionary);
	unset($listattrDictionary);
	$dsql->ExecuteNoneQuery("update sea_co_type set cjtime='".time()."' where tid='$id'");
	echoConSuspend($id,$page+1,$pcount,$cid,$action2,$index);
}

function echoConSuspend($id,$curPage,$pcount,$cid,$action2,$index)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续采集<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=getconsingle&id=".$id."&cid=".$cid."&page=".$curPage."&pcount=".$pcount."&action2=".$action2."&index=".$index."';}</script>";
}

function echoConSuspend2($id,$index)
{
	global $cfg_stoptime;
	echo "<br>暂停".$cfg_stoptime."秒后继续采集下个分类<script language=\"javascript\">setTimeout(\"makeNextPage();\",".$cfg_stoptime."000);function makeNextPage(){location.href='?action=getconall&id=".$id."&index=".$index."';}</script>";
}

function getlistall($id)
{
	global $dsql,$index,$action;
	$dsql->SetQuery("select tid from `sea_co_type` where cid='$id' order by tid asc");
	$dsql->Execute('listall');
	while($crow=$dsql->GetObject('listall'))
	{
		$typeIdArray[]=$crow->tid;
	}
	$curTypeIndex=$index;
	$typeIdArrayLen = count($typeIdArray);
	if (empty($curTypeIndex)){
		$curTypeIndex=0;
	}else{
		if(intval($curTypeIndex)>intval($typeIdArrayLen-1)){
			echo "完成采集列表，转向内容采集页面";
			echo "<script>location='?action=getconall&id=$id';</script>";
			exit;
		}
	}
	$typeId = $typeIdArray[$curTypeIndex];
	if(empty($typeId)){
		exit("分类丢失");
	}else{
		getlistsbyid($typeId,$id,$action,$curTypeIndex);
	}
	unset($typeIdArray);
}

function getconall($id)
{
	global $dsql,$index,$action;
	$dsql->SetQuery("select tid from `sea_co_type` where cid='$id' order by tid asc");
	$dsql->Execute('listall');
	while($crow=$dsql->GetObject('listall'))
	{
		$typeIdArray[]=$crow->tid;
	}
	$curTypeIndex=$index;
	$typeIdArrayLen = count($typeIdArray);
	if (empty($curTypeIndex)){
		$curTypeIndex=0;
	}else{
		if(intval($curTypeIndex)>intval($typeIdArrayLen-1)){
			echo "完成采集内容";
			exit;
		}
	}
	$typeId = $typeIdArray[$curTypeIndex];
	if(empty($typeId)){
		exit("分类丢失");
	}else{
		getconbyid($typeId,$id,$action,$curTypeIndex);
	}
	unset($typeIdArray);
}

function si($table, $data, $needQs=false)
{
	if (count($data)>1)
	{
		$t1 = $t2 = array();
		$i=0;
		foreach($data as $key=>$value)
		{
			if($i!=0&&$i%2==0)
			{
				$t1[] = $key;
				
				$t2[] = $needQs?qs($value):"'$value'";
			}
			
			$i+=1;
		}
		
		
		$sql =  "INSERT INTO `$table` (`".implode("`,`",$t1)."`) VALUES(".implode(",",$t2).")";
	}
	else
	{
		$arr = array_keys($data);
		$feild = $arr[0];
		$value = $data[$feild];
		$value = $needQs?$this->qs($value):"'$value'";
		$sql = "INSERT INTO `$table` (`$feild`) VALUES ($value)";
	}
	return $sql;
}

function qs($s)
{
	return "'".addslashes($s)."'";
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
	if($fromLen!=$playLen){
		ShowMsg("来源或者地址没有填写完整！","-1");
		exit;
	}
	if($fromLen==0){
		$transferUrl=trim($fromArray[0])."$$".trim($playArray[0]);
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
				$tag  = $dsql->GetOne("SELECT tagid,vids FROM sea_tags WHERE tag='$arrtag[$i]' LIMIT 1");
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
			$tag = $dsql->GetOne("SELECT vids FROM sea_tags WHERE tag='$arrold[$i]' LIMIT 1");
			$tag['vids'] = str_replace(','.$videoid, '', $tag['vids']);
			$tag['vids'] = str_replace($videoid.',', '', $tag['vids']);
			$dsql->ExecuteNoneQuery("UPDATE sea_tags SET usenum=usenum-1, vids='".$tag['vids']."' WHERE tag='$arrold[$i]'");
		}
	}
	$dsql->ExecuteNoneQuery("DELETE FROM sea_tags WHERE usenum='0'");
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

function import2Base($idsArray,$vtype)
{
	global $dsql,$cfg_gatherset,$col;
	if(count($idsArray)>0)
	{
		$ids = implode(',',$idsArray);
		$sql="SELECT v_id,v_name,v_letter,tid,v_state,v_pic,v_director,v_actor,v_des,v_playdata,v_publishyear,v_publisharea,v_hit,v_note,v_tags,v_lang FROM sea_co_data WHERE v_id IN (".$ids.")";
		$dsql->SetQuery($sql);
		$dsql->Execute('import_list');
		echo "<div style='font-size:13px'><font color=red>从临时数据库导出数据：</font><br>";
		while($row=$dsql->GetAssoc('import_list'))
		{
			$row['tid'] = ($row['tid']>0) ? $row['tid'] : $vtype;
			if($row['tid']!=''){
				echo $col->_into_database($row);
				$dsql->ExecuteNoneQuery("update `sea_co_data` set v_inbase='1',tid='".$row['tid']."' where v_id=".$row['v_id']);
			}else{
				echo "<font color=red>没选择分类</font> 导入失败 ID:".$row['v_id']."	".$row['v_name']."<br>";
			}//if $tid
		}//while
	}//if count
}


