<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}
if($method!='')
{
	$m = be('get','m');
	$f= $method;
	$s= substr($m,strpos($m,'.'),strlen($m)-strpos($m,'.'));
	$path = MAC_ROOT_TEMPLATE.'/label_'.$f.$s;
	
	if(file_exists($path)){
		$tpl->P['cp'] = 'app';
		$tpl->P['cn'] = $m.$tpl->P['pg'];
		echoPageCache($tpl->P['cp'],$tpl->P['cn']);
		$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
		$tpl->H = loadFile($path);
		$tpl->mark();
		$tpl->pageshow();
	}
	else{
		showErr('System','未找到指定自定义页面');
	}
}

else
{
	showErr('System','未找到指定系统模块');
}
?>