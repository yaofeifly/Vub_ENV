<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/config.user.inc.php");


if(empty($action))
{
	$action = '';
}

if($action=="add")
{
include(sea_ADMIN.'/templets/admin_news_add.htm');
exit();
}
elseif($action=="edit")
{
	$id = isset($id) && is_numeric($id) ? $id : 0;
	//读取文章信息
	$query = "select * from sea_news  where n_id='$id' ";
	$vrow = $dsql->GetOne($query);
	if(!is_array($vrow))
	{
		ShowMsg("读取文章基本信息出错!","-1");
		exit();
	}
	$n_color = $vrow['n_color'];
	$vtype = $vrow['tid'];
	$n_content=$vrow['n_content'];
	include(sea_ADMIN.'/templets/admin_news_edit.htm');
	exit();
}
elseif($action=="save")
{
	if(trim($n_title) == '')
	{
		ShowMsg("文章名不能为空！","-1");
		exit();
	}
	if(empty($v_type))
	{
		ShowMsg("请选择分类！","-1");
		exit();
	}
	$tid = empty($v_type) ? 0 : intval($v_type);
	$n_hit = empty($n_hit) ? 0 : intval($n_hit);
	$n_addtime = time();
	$n_money = empty($n_money) ? 0 : intval($n_money);
	$n_rank = empty($n_rank) ? 0 : intval($n_rank);
	$n_title = htmlspecialchars(cn_substrR($n_title,250));
	$n_author = cn_substrR($n_author,200);
	$n_note = cn_substrR($n_note,30);
	$n_outline = cn_substrR($n_outline,200);
	$n_keyword = cn_substrR(strtolower(addslashes($n_keyword)),30);
	$n_keyword = str_replace('，', ',', $n_keyword);
	$n_keyword = str_replace(',,', ',', $n_keyword);
	$n_from = cn_substrR($n_from,10);
	$n_commend = empty($n_commend) ? 0 : intval($n_commend);
	if(empty($n_entitle))
	{
		$n_entitle = Pinyin($n_title); 
	}
	$n_letter = strtoupper(substr($n_entitle,0,1));
	if (substr($n_keyword, -1) == ',') {
		$n_keyword = substr($n_keyword, 0, strlen($n_keyword)-1);
	}
	$n_pic = cn_substrR($v_pic,255);
	switch (trim($acttype)) 
	{
		case "add":
			$insertSql = "insert into sea_news(tid,n_title,n_letter,n_hit,n_money,n_rank,n_author,n_color,n_pic,n_addtime,n_note,n_from,n_entitle,n_keyword,n_outline,n_content,n_commend) values ('$tid','$n_title','$n_letter','$n_hit','$n_money','$n_rank','$n_author','$n_color','$n_pic','$n_addtime','$n_note','$n_from','$n_entitle','$n_keyword','$n_outline','$n_content','$n_commend')";
			if($dsql->ExecuteNoneQuery($insertSql))
			{
				$n_id = $dsql->GetLastID();
				addtags($n_keyword,$n_id);
				clearTypeCache();
				selectMsg("添加成功,是否继续添加","admin_news.php?action=add","admin_news.php");
			}
			else
			{
				$gerr = $dsql->GetError();
				ShowMsg("把数据保存到数据库主表 `sea_news` 时出错。".str_replace('"','',$gerr),"javascript:;");
				exit();
			}
		break;
		case "edit":
			$n_id = isset($v_id) && is_numeric($v_id) ? $v_id : 0;
			$updateSql = "n_content = '$n_content',n_outline = '$n_outline',tid = '$tid',n_title = '$n_title',n_letter = '$n_letter',n_hit = '$n_hit',n_money = '$n_money',n_rank = '$n_rank',n_author = '$n_author',n_color = '$n_color',n_pic = '$n_pic',n_note = '$n_note',n_keyword = '$n_keyword',n_from='$n_from',n_entitle='$n_entitle'";
			if(!empty($isupdatetime)) $updateSql .= ",n_addtime='$n_addtime'";
			$updateSql = "update sea_news set ".$updateSql." where n_id=".$n_id;
//			echo $updateSql;die();
			if(!$dsql->ExecuteNoneQuery($updateSql))
			{
				ShowMsg('更新文章出错，请检查',-1);
				exit();
			}
			if($cfg_runmode2=='0'){
				$trow = $dsql->GetOne("select ishidden from sea_type where tid=".$tid);
				if($trow['ishidden']==1){
					delArticleFile($tid,$n_id);
					ShowMsg("文章更新成功",$v_back);
					exit();
				}else{
					ShowMsg("文章更新成功，转向生成页面！","admin_makehtml.php?action=singleNews&id=".$n_id."&from=".$v_back);
					exit();
				}
			}else{
				ShowMsg("文章更新成功",$v_back);
				exit();
			}
			break;
	}
}
elseif($action=="del")
{
	$back=$Pirurl;
	$id = isset($id) && is_numeric($id) ? $id : 0;
	$vtypeAndPic=$dsql->GetOne("select tid,n_pic from sea_news where n_id=".$id);
	$vtype=$vtypeAndPic['tid'];
	$vpic=$vtypeAndPic['n_pic'];
	if(substr($vpic,0,8)=='/uploads') delFile("../".$vpic);
	if($cfg_runmode2=='0')
	{
		$vFolder='..'.getArticleLink($vType,$id,"");
		if(is_dir($vFolder)) delFolder($vFolder);
	}
	$dsql->ExecuteNoneQuery("delete from sea_news where n_id=".$id);
	clearTypeCache();
	ShowMsg("文章删除成功",$back);
	exit();
}
elseif($action=="restoreall")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要还原的文章","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$sqlStr="update sea_news set n_recycled=0 where n_id in(".$ids.")";
	$dsql->ExecuteNoneQuery($sqlStr);
	ShowMsg("还原操作成功",$back);
	exit();


}
elseif($action=="delall")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要删除的文章","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("delete from sea_news where n_id in(".$ids.")");
	clearTypeCache();
	ShowMsg("文章删除成功",$back);
	exit();
}
elseif($action=="psettype")
{
	$back=$Pirurl;
	if(empty($e_id))
	{
		ShowMsg("请选择需要移动分类的文章","-1");
		exit();
	}
	$ids = implode(',',$e_id);
	$dsql->ExecuteNoneQuery("update sea_news set tid=".$movetype." where n_id in(".$ids.")");
	ShowMsg("批量移动文章成功",$back);
	exit();
}
elseif($action=="deltypedata")
{
	$back=$Pirurl;
	$movetype = isset($movetype) && is_numeric($movetype) ? $movetype : 0;
	$dsql->ExecuteNoneQuery("delete from sea_news where tid=".$movetype);
	ShowMsg("删除分类数据成功",$back);
	exit();
}
elseif($action=="hide")
{
	$back=$Pirurl;
	if(empty($id))
	{
		ShowMsg("请选择需要隐藏的文章","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_news set n_recycled=1 where n_id=".$id);
	ShowMsg("隐藏文章成功",$back);
	exit();
	
		
}
elseif($action=="restore")
{
	$back=$Pirurl;
	if(empty($id))
	{
		ShowMsg("请选择需要还原的文章","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_news set n_recycled=0 where n_id=".$id);
	ShowMsg("还原文章成功",$back);
	exit();	
}
else
{
	include(sea_ADMIN.'/templets/admin_news.htm');
	exit();
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

function isNewsMake($v_id,$contentUrl)
{
	$contentUrl=str_replace($GLOBALS['cfg_cmspath'],'',$contentUrl);
	echo "<a href=\"admin_makehtml.php?action=singleNews&id=$v_id\">";
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

function delArticleFile($v_type,$v_id)
{
	$contentPath=getArticleLink($v_type,$v_id,"");
	if(!empty($contentPath)){
		delFile('..'.$contentPath);
	}
}

?>