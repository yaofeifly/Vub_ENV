<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}
if($MAC['other']['gbook']==0){ echo 'gbook closed'; return; }
if($method=='show')
{
	$tpl->P["siteaid"] = 30;
	if($tpl->P['pg']<1){ $tpl->P['pg']=1; }
	$tpl->P['cp'] = 'app';
	$tpl->P['cn'] = 'gbook'.$tpl->P['pg'];
	//echoPageCache($tpl->P['cp'],$tpl->P['cn']);
	$tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/home_gbook.html");
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$tpl->mark();
	$tpl->H = str_replace("{maccms:gbookverify}", $MAC['other']['gbookverify'] ,$tpl->H);
	if(strpos($tpl->H,'{maccms:count_gbook_all}')){
		$tpl->H = str_replace("{maccms:count_gbook_all}", $tpl->getDataCount('gbook',"all")  ,$tpl->H);
	}
	if(strpos($tpl->H,'{maccms:count_gbook_day}')){
		$tpl->H = str_replace("{maccms:count_gbook_day}", $tpl->getDataCount('gbook',"day")  ,$tpl->H);
	}
	$tpl->pageshow();
}

elseif($method=='save')
{
	$g_vid = be("all", "g_vid");
    $g_name = be("all", "g_name");
    $g_content = be("all", "g_content");
    $g_code = be("all","g_code");
    
    if (!isNum($g_vid)){ $g_vid=0; } 
    if (isN($g_name) || isN($g_content)){ alert('请输入昵称和内容'); exit;}
    if ($MAC['other']['gbookverify']==1 && $_SESSION["code_gbook"] != $g_code){ alert('验证码错误');exit; }
    if (getTimeSpan("last_gbooktime") < $MAC['other']['gbooktime']){ alert('请不要频繁操作');exit; }
    $pattern = '/[^\x00-\x80]/'; 
	if(!preg_match($pattern,$g_content)){
		alert('内容必须包含中文,请重新输入!'); exit;
	}
    
    $g_name = badFilter($g_name);
    $g_content = badFilter($g_content);
    $g_ip = ip2long(getIP());
    $g_time = time();
    
    if ($MAC['other']['gbookaudit']==1){ $g_hide=1; } else { $g_hide=0; }
	if (strlen($g_name) >64){ $g_name = substring($g_name,64);}
	if (strlen($g_content) >255){ $g_content = substring($g_content,255);}
	
	$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
	$db->Add ("{pre}gbook", array("g_vid","g_hide","g_name", "g_ip", "g_time", "g_content"), array($g_vid, $g_hide, $g_name, $g_ip, $g_time, $g_content));
	
	$_SESSION["last_gbook"]  = time();
	$_SESSION["code_gbook"] = "";
	alert('留言成功');
}

else
{
	showErr('System','未找到指定系统模块');
}
?>