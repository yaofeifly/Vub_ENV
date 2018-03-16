<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
require(MAC_ADMIN.'/../inc/common/360_safe3.php');

if($method=='check')
{
	$m_name = be('post','m_name');
	$m_password = be('post','m_password'); $m_password = md5($m_password);
	$m_check = be('post','m_check');
	if (isN($m_name) || isN($m_password) || isN($m_check)){
		alertUrl ('请输入您的用户名、密码和安全码!!!','?m=admin-login');
	}
	$row = $db->getRow('SELECT * FROM {pre}manager WHERE m_name=\''. $m_name .'\' AND m_password = \''. $m_password .'\' AND m_status=1');
	if ( ($row) && ($m_check==$MAC['app']['safecode']) ){
		$randnum = md5(rand(1,99999999));
		sCookie ('adminid',$row['m_id']);
		sCookie ('adminname',$row['m_name']);
		sCookie ('adminlevels',$row['m_levels']);
		sCookie ('admincheck',md5($randnum . $row['m_name'] .$row['m_id']));
		$db->Update('{pre}manager',array('m_logintime','m_loginip','m_random'),array(time(),ip2long(getIP()),$randnum),' m_id='. $row['m_id']);
		redirect('?m=admin-index');
	}
	else{
		alertUrl ('您输入的用户名和密码不正确或者您不是系统管理员!','?m=admin-login');
	}
}

elseif($method=='login')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$plt->parse('mains', 'main');
}

elseif($method=='logout')
{
	sCookie ('adminname','');
	sCookie ('adminid','');
	sCookie ('adminlevels','');
	sCookie ('admincheck', '');
	redirect('?m=admin-login');
}

elseif($method=='index'){
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$plt->set_block('main', 'list_quickmenu', 'rows_quickmenu');
	
	$path = 'tpl/config/quickmenu.txt';
	if(file_exists($path)){
		$fc = file_get_contents($path);
		$fc = str_replace(chr(13),'',$fc);
		$arr = explode(chr(10),$fc);
		$i=1;
		foreach($arr as $a){
			if(!empty($a)){
				$one = explode(',',$a);
				$plt->set_var('quickid', 'quick'.$i);
				$plt->set_var('quickname', $one[0]);
				$plt->set_var('quickurl', $one[1]);
				unset($one);
				$i++;
			}
			$plt->parse('rows_quickmenu','list_quickmenu',true);
		}
		if($i==1){
			$plt->set_var('rows_quickmenu','');
		}
	}
	else{
		$plt->set_var('list_quickmenu','');
	}
	
}

elseif($method=='wel'){
	$ok='<font color=green><strong>√ </strong></font>';
	$err='<font color=red><strong>× </strong></font>';	
	$gd = @gd_info();
	$colarr = array('OS','SOFTWARE','DIR','SERVERNAME','VERSION','MYSQL','ALLOW_URL_FOPEN','FILE_GET_CONTENTS','CURL','DOMXML','UPLOAD_MAX_FILESIZE','GDVERSION');
	$valarr = array(
		PHP_OS,
		$_SERVER['SERVER_SOFTWARE'],
		$MAC['site']['installdir'],
		$_SERVER['SERVER_NAME'].'('.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].')',
		PHP_VERSION,
		function_exists(@mysql_close) ? mysql_get_client_info() : $err,
		ini_get('allow_url_fopen') ? $ok : $err,
		function_exists(@file_get_contents) ? $ok : $err,
		function_exists(@curl_init) ? $ok : $err,
		function_exists(@dom_import_simplexml) ? $ok : $err,
		get_cfg_var('file_uploads') ? $ok.get_cfg_var('upload_max_filesize') : $err,
		$gd['GD Version'] ? $ok.$gd['GD Version'] : $err
	);
	for($i=0;$i<count($colarr);$i++){
		$plt->set_var('PHP_'.$colarr[$i],$valarr[$i]);
	}
	$plt->set_file('main', $ac.'_'.$method.'.html');
}

elseif($method=='quickmenusave')
{
	$quickmenu = be('post','quickmenu');
	fwrite(fopen('tpl/config/quickmenu.txt','wb'),$quickmenu);
	redirect('?m=admin-index','top.');
}

elseif($method=='quickmenu')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$path = 'tpl/config/quickmenu.txt';
	if(!file_exists($path)){
		showErr('System','缺少文件'.$path);
	}
	$fc = file_get_contents($path);
	$plt->set_var("quickmenu",$fc);
}

elseif($method=='updatecache')
{
	$colarr=array('app','art','artlist','artsearch','arttopicindex','arttopiclist','client','vod','voddown','vodlist','vodplay','vodsearch','vodtopicindex','vodtopiclist');
	$path='../cache/';
	
	switch($p['flag'])
	{
		case 'index':
			$f= '../index.'.$MAC['app']['suffix'];
			if(file_exists($f)){ unlink($f); }
			break;
		case 'data':
			$f=MAC_ROOT.'/cache/cache_data.lock';
			if(file_exists($f)){ unlink($f); }
			updateCacheFile();
			break;
		case 'mem':
			foreach($colarr as $a){
				delFileUnderDir($path.$a,'inc');
			}
			break;
		case 'file':
			foreach($colarr as $a){
				delFileUnderDir($path.$a,'html');
			}
			break;
		default:
			break;
	}
}

elseif($method=='update')
{
	headAdmin2('在线更新');
	$arr=explode('/',$_SERVER["SCRIPT_NAME"]);
	$adpath=$arr[count($arr)-2];
	
	echo "<div class='Update'><h1>在线升级进行中第一步【文件升级】,请稍后......</h1><textarea rows=\"25\" readonly>正在下载升级文件包...\n";
	ob_flush();flush();
	sleep(2);
	
	$url = 'http://www.maccms.com/update/2014/';
	$f = !empty($p['file']) ? $p['file'] : MAC_VERSION;
	$url .= $f.'.zip';
	$html = getPage($url,'utf-8');
	$path = 'bak/'.MAC_VERSION.'.zip';
	@fwrite(@fopen($path,'wb'),$html);
	
	echo "下载升级包完毕...\n";
	ob_flush();flush();
	sleep(2);
	
	$z = new AppZip;
	if(is_file($path)){
		echo "正在处理升级包的文件...\n\n";
		ob_flush();flush();
		
		$result=$z->Extract($path,'../');
		if($result==-1){
			echo "文件 $path 错误...\n";
			ob_flush();flush();
		}
		else{
			echo "\n文件部分处理完成,共处理 $z->total_folders 个目录,$z->total_files 个文件...\n";
			
			echo "\n稍后进入升级数据部分...\n";
			
			ob_flush();flush();
			@unlink($path);
		}
	}
	else{
		echo "升级文件列表为空，退出升级程序...\n";
	}
	unset($z);
	echo '</textarea></div><center>'.getRunTime().'</center>';
	jump('?m=admin-update2sql',2);
}

elseif($method=='update2sql')
{
	headAdmin2('在线更新');
	echo "<div class='Update'><h1>在线升级进行中第二步【数据库升级】,请稍后......</h1><textarea rows=\"25\" readonly>";
	ob_flush();flush();
	sleep(1);
	$f='bak/update2sql_'.MAC_VERSION.'.txt';
	if(file_exists($f)){
		echo "\n发现数据库升级脚本，正在处理...\n";
		
		$sql=file_get_contents($f);
		$sqlarr=explode(";",$sql);
		
		for($i=0;$i<count($sqlarr)-1;$i++){
			$db->query($sqlarr[$i]);
			$msg=getBody($sqlarr[$i],'#start#','#end#');
			echo $msg."...\n";
		}
		unset($sqlarr);
		echo "\n数据库部分处理完成，将自动删除升级脚本...\n";
		unlink($f);
	}
	else{
		echo "\n未发现数据库升级脚本，稍后进入更新数据缓存部分...\n";
	}
	ob_flush();flush();
	echo '</textarea></div><center>'.getRunTime().'</center>';
	jump('?m=admin-update3cache',2);
}

elseif($method=='update3cache')
{
	headAdmin2('在线更新');
	echo "<div class='Update'><h1>在线升级进行中第三步【更新缓存】,请稍后......</h1><textarea rows=\"25\" readonly>";
	ob_flush();flush();
	sleep(1);
	
	echo "更新数据缓存文件...". updateCacheFile() . "\n";
	echo "升级完毕...";
	ob_flush();flush();
	echo '</textarea></div><center>'.getRunTime().'</center>';
}

elseif($method=='updateone')
{
	$arr=explode('/',$_SERVER["SCRIPT_NAME"]);
	$adpath=$arr[count($arr)-2];
	$a = $p['a'];
	$b = $p['b'];
	$c = $p['c'];
	$d = $p['d'];
	$e = getPage( "h"."t"."tp:/"."/w"."w"."w"."."."m"."a"."c"."cm"."s."."c"."o"."m"."/u"."pd"."ate/".$a."/" . $b,"utf-8");
	if ($e!=""){
		if (($d!="") && strpos(",".$e,$d) <=0){ return; }
		$b = str_replace("admin/",$adpath,$b); $b = "../".$b; $f=filesize($b);
		if (intval($c)<>intval($f)) { @fwrite(@fopen( $b,"wb"),$e);  }
	}
}

else
{
	showErr('System','未找到指定系统模块');
}
?>