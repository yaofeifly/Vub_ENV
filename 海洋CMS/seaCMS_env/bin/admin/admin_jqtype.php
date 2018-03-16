<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

$id = empty($id) ? 0 : intval($id);

if($action=="add")
{
	if(empty($tname))
	{
		ShowMsg("剧情分类名称没有填写，请返回检查","-1");
		exit();
	}
	
	$in_query = "insert into `sea_jqtype`(tid,tname,ishidden) Values('','$tname',0)";
	if(!$dsql->ExecuteNoneQuery($in_query))
	{
		ShowMsg("增加分类失败，请检查您的输入是否存在问题！","-1");
		exit();
	}
	clearTypeCache();
	ShowMsg("成功创建一个剧情分类！","admin_jqtype.php");
	exit();
}
elseif($action=="hide")
{
	$typePath = getTypePath($id);
	$dsql->ExecuteNoneQuery("update sea_jqtype set ishidden=1 where tid=".$id);
	clearTypeCache();
	header("Location:admin_jqtype.php");
	exit;
}
elseif($action=="nohide")
{
	$dsql->ExecuteNoneQuery("update sea_jqtype set ishidden=0 where tid=".$id);
	clearTypeCache();
	header("Location:admin_jqtype.php");
	exit;
}
elseif($action=="del")
{
	delVideoTypeById($id);
	header("Location:admin_jqtype.php");
	exit;
}
elseif($action=="delall")
{
	if(empty($e_id))
	{
		ShowMsg("没有选择分类，请返回选择！","admin_jqtype.php");
		exit();
	}
	foreach($e_id as $id)
	{
		delVideoTypeById($id);
	}
	clearTypeCache();
	header("Location:admin_jqtype.php");
	exit;
}

elseif($action=="edit")
{
	if(empty($e_id))
	{
		ShowMsg("请选择需要更改的分类","admin_jqtype.php");
		exit();
	}
	foreach($e_id as $id)
	{
		$tname=$_POST["tname$id"];
	if(empty($tname))
	{
		ShowMsg("分类名称没有填写，请返回检查","-1");
		exit();
	}
	$dsql->ExecuteNoneQuery("update sea_jqtype set tname='$tname' where tid=".$id);
	clearTypeCache();
	}
	header("Location:admin_jqtype.php");
	exit;
}
else
{
include(sea_ADMIN.'/templets/admin_jqtype.htm');
exit();
}

function clearTypeCache()
{
	global $cfg_iscache,$cfg_cachemark;
	if($cfg_iscache)
	{
		$TypeCacheFile=sea_DATA."/cache/".$cfg_cachemark."obj_get_jqtype_list_0.inc";
		if(is_file($TypeCacheFile)) unlink($TypeCacheFile);
	}
}

function delTypeFirstPage($typeid)
{
	$channelFirstPage=getChannelPagesLink($typeid,1);
	if (!empty($channelFirstPage)) delFile('..'.$channelFirstPage);
}

function delVideoTypeById($id)
{
	global $dsql;
	$typePath = getTypePath($id);
	if (isExistSub($id,"type"))
	{
		ShowMsg("请先删除该分类的子类","admin_jqtype.php");
		exit();
	}else{
		$dsql->ExecuteNoneQuery("delete from sea_jqtype where tid=".$id);
		clearTypeCache();
		
	}
}

function isExistSub($bigType,$operFlag)
{
	global $dsql;
	switch ($operFlag) 
	{
		case "type":
			$row = $dsql->GetOne("select count(*) as dd from sea_jqtype where upid=".$bigType);
		break;
		case "video":
			$row = $dsql->GetOne("select count(*) as dd from sea_data where tid=".$bigType);
		break;
	}
	if($row[dd]>0)
	{
		return true;
	}else{
		return false;
	}
}

function getjqnumber($tname)
{
	global $dsql;
	$sql="select count(*) as dd from sea_data where v_jq like '%$tname%'";
	$row = $dsql->GetOne($sql);
	return $row[dd];
}

function typeList($topId,$separateStr,$span="")
{
	$tlist=getjqTypeListsOnCache();
	if ($topId!=0){$span.=$separateStr;}else{$span="";}
	foreach($tlist as $row)
	{
		if($row->upid==$topId)
		{
			if(!$row->tptype){
?>
	<tr>
            <td height="30" bgcolor="#FFFFFF" class="td_border"><?php echo $span;?>&nbsp;<input type="checkbox" name="e_id[]" id="E_ID"  value="<?php echo $row->tid;?>" class="checkbox"/><?php echo $row->tid;?>.<a href="admin_video.php?jqtype=<?php echo $row->tname;?>"><?php echo $row->tname;?></a>(<font color="red"><?php echo getjqnumber($row->tname);?></font>)</td>
            <td height="30" align="left" bgcolor="#FFFFFF"  class="td_border"><input size="13" type="text" name="tname<?php echo $row->tid;?>" value="<?php echo $row->tname;?>"></td>
            <td height="30" align="center" bgcolor="#FFFFFF" class="td_border"><?php if($row->ishidden==0){?><input type="button" value="隐藏"  onClick="location.href='?action=hide&id=<?php echo $row->tid;?>';" class="rb1"><?php }else{?><input type="button" value="取消隐藏"  onClick="location.href='?action=nohide&id=<?php echo $row->tid;?>';" class="rb1"><?php }?><input type="button" value="删除" onclick="del(<?php echo $row->tid;?>)" class="rb1"></td>
	</tr>
<?php
		typeList($row->tid,$separateStr,$span);
			}
		}
	}
    
if (!empty($span)){$span=substr($span,(strlen($span)-strlen($separateStr)));}
}

?>