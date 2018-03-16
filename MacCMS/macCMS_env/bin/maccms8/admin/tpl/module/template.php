<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
if($method=='list')
{
	$path = $p['path'];
	$label = $p['label'];
	
	if(empty($path)){ $path = "../template"; }
	if(substring($path,11) != "../template") { $path = "../template"; }
	$uppath = substring($path,strrpos($path,"/"));
	
	if (count( explode("../",$path) ) > 2) {
		showErr('System','非法目录请求');
		return;
	}
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	if ($label!='show' && $path !="../template"){
		$plt->set_var('uppath',$uppath);
		$plt->set_if('main','ischild',true);
	}
	else{
		$plt->set_if('main','ischild',false);
	}
	
	if($label=='show'){
		$path = '../template/'.$MAC['site']['templatedir'].'/'.$MAC['site']['htmldir'];
	}
	
	$plt->set_var('curpath',$path);
	$rn1='templatepath';
	$rn2='templatefile';
	$plt->set_block('main', 'list_'.$rn1, 'rows_'.$rn1);
	$plt->set_block('main', 'list_'.$rn2, 'rows_'.$rn2);
	
	
	if(is_dir($path)){
		$farr = glob($path.'/*');
		$num_path = 0;
		$num_file =0;
		$sumsize = 0;
		
		if($farr){
			foreach($farr as $f){
				if ( is_dir($f) ){
					$f = str_replace($path."/","",$f);
					if(strpos($filters,",".$f.",")<=0){
						$num_path++;
						$colarr=array('name','path');
						$valarr=array($f,$path."/".$f);
						for($i=0;$i<count($colarr);$i++){
							$plt->set_var($colarr[$i],$valarr[$i]);
						}
						$plt->parse('rows_'.$rn1,'list_'.$rn1,true);
					}
				}
				elseif(is_file($f)){
						
						$fs = filesize($f);
						
						$fsize = round($fs/1024,2);
						$filetime = getColorDay(filemtime($f));
						$f = convert_encoding($f,"UTF-8","GB2312");
						$f = str_replace($path."/","",$f);
						$flag = getTemplateFlag($f);
						$f1 = str_replace('label_','',$f);
						
						$mark='内嵌标签：{maccms:load '.$f1.'}';
						$status=false;
						
						
						if(substring($f,6)== "label_"){
							if($label=='show'){ $status=true; }
						}
						else{
							$mark='';
							if($label!='show'){ $status=true; }
						}
						$colarr=array('name','name2','flag','path','size','time','mark');
						$valarr=array($f,str_replace('label_','label-',$f),$flag,$path."/".$f,$fsize,$filetime,$mark);
						if($status){
							$num_file++;
							$sumsize = $sumsize + $fs;
							
							for($i=0;$i<count($colarr);$i++){
								$plt->set_var($colarr[$i],$valarr[$i]);
							}
							$plt->parse('rows_'.$rn2,'list_'.$rn2,true);
							if($label=='show'){ $plt->set_if('rows_'.$rn2,'islabel',true); } else { $plt->set_if('rows_'.$rn2,'islabel',false); }
							if(strpos($f,'.html') || strpos($f,'.htm') || strpos($f,'.js') || strpos($f,'.xml') || strpos($f,'.css')){
								$plt->set_if('rows_'.$rn2,'isedit',true);
							}
							else{
								$plt->set_if('rows_'.$rn2,'isedit',false);
							}
						}
						
				}
			}
		}
		unset($colarr);
		unset($valarr);
		unset($farr);
		$sumsize=getFormatSize($sumsize);
		
		
		if($num_path==0){
			$plt->set_var('rows_'.$rn1,'');
		}
		if($num_file==0){
			$plt->set_var('rows_'.$rn2,'');
		}
	}
	else{
		$plt->set_var('rows_'.$rn1,'');
		$plt->set_var('rows_'.$rn2,'');
	}
	$plt->set_var('sumsize',$sumsize);
	$plt->set_var('filecount',$num_file);
	$plt->set_var('pathcount',$num_path);
}

elseif($method=='info')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$backurl = getReferer();
	$path = $p['path'];
	$file = $p['file'];
	
	if(!empty($path)){
		$plt->set_if('main','isadd',true);
	}
	else{
		$plt->set_if('main','isadd',false);
		
		$filename = substr($file,strrpos($file,'/')+1);
		if (substring($file,11)!='../template' || count( explode('../',$file) ) > 2) {
			showErr('System','非法目录请求');
			return;
		}
		$filecontent = @file_get_contents($file);
		$readonly = 'readonly';
	}
	
	$colarr=array('backurl','filename','filecontent','path','file','readonly');
	$valarr=array($backurl,$filename,$filecontent,$path,$file,$readonly);
	for($i=0;$i<count($colarr);$i++){
		$plt->set_var($colarr[$i],$valarr[$i]);
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='save')
{
	$path = be('post','path');
	$file =  be('post','file');
	$filename = be('post','filename');
	$suffix = be('post','suffix');
	$filecontent =  stripslashes(be('post','filecontent'));
	
	if(isN($path)){
		if (substring($file,11)!='../template' || count( explode('../',$file) ) > 2) {
			showErr('System','非法目录请求');
			return;
		}
		if(!file_exists($file)){
			showErr('System','非法文件请求');
			return;
		}
		fwrite(fopen($file.$suffix,'wb'),$filecontent);
	}
	else{
		if (substring($path,11)!='../template' || count( explode('../',$path) ) > 2) {
			showErr('System','非法目录请求');
			return;
		}
		$extarr = array('.html','.htm','.js','.xml','.wml');
		if(!in_array($suffix,$extarr)){
			$suffix='.html';
		}
		
		fwrite(fopen($path.'/'.$filename.$suffix,'wb'),$filecontent);
	}
	showMsg('文件内容保存完毕','');
}

elseif($method=='del')
{
	$file = $p['file'];
	if (substring($file,11)!='../template' || count( explode('../',$file) ) > 2) {
		showErr('System','非法目录请求');
		return;
	}
	
	if(file_exists( $file)){
		unlink($file);
	}
	redirect( getReferer() );
}

elseif($method=='ads')
{
	$path = '../template/'.$MAC['site']['templatedir'] .'/'.$MAC['site']['adsdir'].'/';
	if(!is_dir($path)){
		showErr('System','未找到指定系统路径：'.$path);
		return;
	}
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$fcount=0;
	$farr = glob($path.'/*.js');
	if($farr){
		$fcount = count($farr);
	}
	
	if($fcount==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	$colarr=array('file','size','time','path','filecode');
	$rn='ads';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	foreach( $farr as $f){
		if ( is_file($f) ){
			$fsize= round( filesize( $f ) / 1024,2 );
			$ftime = getColorDay (  filemtime ($f) );
			$file = str_replace($path.'/','',$f);
			$filecode = str_replace('-','$$$',$file);
			$fjs = '{maccms:path_ads}' .$file;
			$valarr=array($file,$fsize,$ftime,$fjs,$filecode);
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$i];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	$plt->set_var('adspath',$path);
}

elseif($method=='adsinfo')
{
	$path = '../template/'.$MAC['site']['templatedir'] .'/'.$MAC['site']['adsdir'].'/';
	$file = str_replace('$$$','-',$p['file']);
	$backurl = getReferer();
	if ($file!=''){
		if(!file_exists($path . $file)){
			showErr('System','缺少文件'.$path . $file);
		}
		$filecontent = file_get_contents($path .$file);
		$file = str_replace('.js','',$file);
		$readonly = 'readonly="readonly"';
	}
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$colarr=array('readonly','backurl','file','filecontent');
	$valarr=array($readonly,$backurl,$file,$filecontent);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
}

elseif($method=='adsdel')
{
	$path = '../template/'.$MAC['site']['templatedir'] .'/'.$MAC['site']['adsdir'].'/';
	$file = str_replace('$$$','-',$p['file']);
	if ($file!=''){
		if(!file_exists($path . $file)){
			showErr('System','缺少文件'.$path . $file);
		}
		else{
			unlink($path.$file);
		}
	}
	redirect( getReferer() );
}

elseif($method=='adssave')
{
	$path = '../template/'.$MAC['site']['templatedir'] .'/'.$MAC['site']['adsdir'].'/';
	$file = be('post','file');
	$filecontent = stripslashes(be('post','filecontent'));
	if(!is_dir($path)){
		mkdir($path);
	}
	fwrite(fopen( $path. $file.'.js','wb'),$filecontent);
	showMsg('数据已保存','');
}

elseif($method=='wizard')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
}

else
{
	showErr('System','未找到指定系统模块');
}
?>