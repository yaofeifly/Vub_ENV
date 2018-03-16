<?php
	/*
	'软件名称：苹果CMS
	'开发作者：MagicBlack    官方网站：http://www.maccms.com/
	'--------------------------------------------------------
	'适用本程序需遵循 CC BY-ND 许可协议
	'这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用；
	'不允许对程序代码以任何形式任何目的的再发布。
	'--------------------------------------------------------
	*/
	if(!file_exists('inc/install.lock')) { echo '<script>location.href=\'install.php\';</script>';exit; }
	require("inc/conn.php");
	require(MAC_ROOT.'/inc/common/360_safe3.php');
    $m = be('get','m');
    if(strpos($m,'.')){ $m = substr($m,0,strpos($m,'.')); }
    $par = explode('-',$m);
    $parlen = count($par);
    $ac = $par[0];
    
    if(empty($ac)){ $ac='vod'; $method='index'; }
    
    $colnum = array("id","pg","yaer","typeid","classid");
    if($parlen>=2){
    	$method = $par[1];
    	 for($i=2;$i<$parlen;$i+=2){
            $tpl->P[$par[$i]] = in_array($par[$i],$colnum) ? intval($par[$i+1]) : urldecode($par[$i+1]);
        }
    }
    if($tpl->P['pg']<1){ $tpl->P['pg']=1; }
    unset($colnum);
    $acs = array('vod','art','map','user','gbook','comment','label');
    if(in_array($ac,$acs)){
    	$tpl->P["module"] = $ac;
    	include MAC_ROOT.'/inc/module/'.$ac.'.php';
    }
    else{
    	showErr('System','未找到指定系统模块');
    }
    unset($par);
    unset($acs);
    
    $tpl->ifex();
	setPageCache($tpl->P['cp'],$tpl->P['cn'],$tpl->H);
	$tpl->run();
	echo $tpl->H;
?>