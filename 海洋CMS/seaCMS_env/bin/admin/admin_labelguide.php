<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('');

include(sea_ADMIN.'/templets/admin_labelguide.htm');
exit();

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
?>