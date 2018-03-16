<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}

if($method=='vod')
{
	$tpl->P['cp'] = 'map';
	$tpl->P['cn'] = $method.$tpl->P['id'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$sql = "SELECT * FROM {pre}vod WHERE d_hide=0 AND d_id=" . $tpl->P['id'];
	$row = $db->getRow($sql);
	if(!row){ showErr('System','未找到指定数据'); }
	$tpl->T = $MAC_CACHE['vodtype'][$row['d_type']];
	$tpl->D = $row;
	unset($row);
	$tpl->loadvod("rss");
	$tpl->replaceVod();
	$tpl->playdownlist ("play");
	$tpl->playdownlist ("down");
	
}

elseif($method=='rss' || $method=='baidu' || $method=='google'|| $method=='360')
{
    $tpl->P['cp'] = 'map';
	$tpl->P['cn'] = $method.'-'.$tpl->P['pg'];
	echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->H = loadFile(MAC_ROOT.'/inc/map/'.$method.'.html');
	$tpl->mark();
}

else
{
	showErr('System','未找到指定系统模块');
}
?>