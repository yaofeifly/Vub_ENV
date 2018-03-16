<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
if($method=='configsave')
{
	$tempcacheid= time();
	$suffix = trim(be('post','app_suffix'));
	if($suffix!='htm' && $suffix!='shtml') { $suffix='html'; }
	
	$config = $MAC;
	
	$adsdir='ads';
	$tc='../template/'.trim(be('post','site_templatedir')).'/config.xml';
	if(file_exists($tc)){
		$fc=@file_get_contents($tc);
		$adsdir=getBody($fc,'<adspath>','</adspath>');
	}
	$tjstr = str_replace('\'','\\\'', stripslashes(trim(be('post','site_tj'))) );
	
	if( trim(be('post','site_name'))=='' ){
		alert('请输入网站名称');
		exit;
	}
	
	$config['site']['name'] = trim(be('post','site_name'));
	$config['site']['installdir'] = trim(be('post','site_installdir'));
	$config['site']['url'] = trim(be('post','site_url'));
	$config['site']['keywords'] = trim(be('post','site_keywords'));
	$config['site']['description'] = trim(be('post','site_description'));
	$config['site']['templatedir'] = trim(be('post','site_templatedir'));
	$config['site']['adsdir'] = $adsdir;
	$config['site']['htmldir'] = trim(be('post','site_htmldir'));
	$config['site']['icp'] = trim(be('post','site_icp'));
	$config['site']['email'] = trim(be('post','site_email'));
	$config['site']['qq'] = trim(be('post','site_qq'));
	$config['site']['tj'] = $tjstr;
	
	$config['app']['cachetype'] = intval(trim(be('post','app_cachetype')));
	$config['app']['cache'] = intval(trim(be('post','app_cache')));
	$config['app']['dynamiccache'] = intval(trim(be('post','app_dynamiccache')));
	$config['app']['compress'] = intval(trim(be('post','app_compress')));
	$config['app']['cachetime'] = !isNum(be('post','app_cachetime')) ? 60 : intval(trim(be('post','app_cachetime')));
	$config['app']['cacheid'] = trim(be('post','app_cacheid'));
	$config['app']['memcachedhost'] = trim(be('post','app_memcachedhost'));
	$config['app']['memcachedport'] = trim(be('post','app_memcachedport'));
	$config['app']['safecode'] = trim(be('post','app_safecode'));
	$config['app']['pagesize'] = !isNum(be('post','app_pagesize')) ? 20 : intval(trim(be('post','app_pagesize')));
	$config['app']['expandtype'] = intval(trim(be('post','app_expandtype')));
	$config['app']['playersort'] = intval(trim(be('post','app_playersort')));
	$config['app']['encrypt'] = intval(trim(be('post','app_encrypt')));
	$config['app']['isopen'] = intval(trim(be('post','app_isopen')));
	$config['app']['area'] = trim(be('post','app_area'));
	$config['app']['lang'] = trim(be('post','app_lang'));
	$config['app']['maketime'] = trim(be('post','app_maketime'));
	$config['app']['makesize'] = trim(be('post','app_makesize'));
	$config['app']['suffix'] = trim(be('post','app_suffix'));
	
	$config['user']['status'] = !isNum(be('post','user_status')) ? 1 : intval(trim(be('post','user_status')));
	$config['user']['reg'] = !isNum(be('post','user_reg')) ? 1 : intval(trim(be('post','user_reg')));
	$config['user']['regpoint'] = !isNum(be('post','user_regpoint')) ? 1 : intval(trim(be('post','user_regpoint')));
	$config['user']['regstate'] = !isNum(be('post','user_regstate')) ? 1 : intval(trim(be('post','user_regstate')));
	$config['user']['popularize'] = !isNum(be('post','user_popularize')) ? 2 : intval(trim(be('post','user_popularize')));
	$config['user']['popularizestate'] = !isNum(be('post','art_makeinterval')) ? 1 : intval(trim(be('post','user_popularizestate')));
	$config['user']['reggroup'] = intval(trim(be('post','user_reggroup')));
	$config['user']['weekpoint'] = !isNum(be('post','user_weekpoint')) ? 100 : intval(trim(be('post','user_weekpoint')));
	$config['user']['monthpoint'] = !isNum(be('post','user_monthpoint')) ? 1000 : intval(trim(be('post','user_monthpoint')));
	$config['user']['yearpoint'] = !isNum(be('post','user_yearpoint')) ? 5000 : intval(trim(be('post','user_yearpoint')));
	
	$config['other']['filter'] = trim(be('post','other_filter'));
	$config['other']['gbook'] = !isNum(be('post','other_gbook')) ? 1 : intval(trim(be('post','other_gbook')));
	$config['other']['gbooknum'] = !isNum(be('post','other_gbooknum')) ? 10 : intval(trim(be('post','other_gbooknum')));
	$config['other']['gbooktime'] = !isNum(be('post','other_gbooktime')) ? 10 : intval(trim(be('post','other_gbooktime')));
	$config['other']['gbookverify'] = intval(trim(be('post','other_gbookverify')));
	$config['other']['gbookaudit'] = intval(trim(be('post','other_gbookaudit')));
	$config['other']['comment'] = !isNum(be('post','other_comment')) ? 1 : intval(trim(be('post','other_comment')));
	$config['other']['commentnum'] = !isNum(be('post','other_commentnum')) ? 10 : intval(trim(be('post','other_commentnum')));
	$config['other']['commenttime'] = !isNum(be('post','other_commenttime')) ? 10 : intval(trim(be('post','other_commenttime')));
	$config['other']['commentverify'] = intval(trim(be('post','other_commentverify')));
	$config['other']['commentaudit'] = intval(trim(be('post','other_commentaudit')));
	$config['other']['mood'] = !isNum(be('post','other_mood'))=='' ? 1 : intval(trim(be('post','other_mood')));
	
	$config['upload']['thumb'] = intval(trim(be('post','upload_thumb')));
	$config['upload']['thumbw'] = intval(trim(be('post','upload_thumbw')));
	$config['upload']['thumbh'] = intval(trim(be('post','upload_thumbh')));
	$config['upload']['picpath'] = !isNum(be('post','upload_picpath')) ? 1 : intval(trim(be('post','upload_picpath')));
	$config['upload']['watermark'] = intval(trim(be('post','upload_watermark')));
	$config['upload']['waterlocation'] = !isNum(be('post','upload_waterlocation')) ? 2 : intval(trim(be('post','upload_waterlocation')));
	$config['upload']['waterfont'] = trim(be('post','upload_waterfont'));
	
	$config['upload']['remote'] = intval(trim(be('post','upload_remote')));
	$config['upload']['remoteurl'] = trim(be('post','upload_remoteurl'));
	
	$config['upload']['ftp'] = intval(trim(be('post','upload_ftp')));
	$config['upload']['ftphost'] = trim(be('post','upload_ftphost'));
	$config['upload']['ftpuser'] = trim(be('post','upload_ftpuser'));
	$config['upload']['ftppass'] = trim(be('post','upload_ftppass'));
	$config['upload']['ftpdir'] = trim(be('post','upload_ftpdir'));
	$config['upload']['ftpport'] = trim(be('post','upload_ftpport'));
	$config['upload']['ftpdel'] = intval(trim(be('post','upload_ftpdel')));
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	
	if(!empty($tjstr)){
		$tjstr = str_replace('\\\'','\'',$tjstr);
		fwrite(fopen('../js/tj.js','wb'),htmltojs( $tjstr ) );
	}
	redirect('?m=system-config');
}

elseif($method=='configurlsave')
{
	$config = $MAC;
	$config['view']['vodindex'] = intval(trim(be('post','view_vodindex')));
	$config['view']['vodmap'] = intval(trim(be('post','view_vodmap')));
	$config['view']['vodtype'] = intval(trim(be('post','view_vodtype')));
	$config['view']['vodlist'] = intval(trim(be('post','view_vodlist')));
	$config['view']['vodtopicindex'] = intval(trim(be('post','view_vodtopicindex')));
	$config['view']['vodtopic'] = intval(trim(be('post','view_vodtopic')));
	$config['view']['voddetail'] = intval(trim(be('post','view_voddetail')));
	$config['view']['vodplay'] = intval(trim(be('post','view_vodplay')));
	$config['view']['voddown'] = intval(trim(be('post','view_voddown')));
	$config['view']['vodsearch'] = intval(trim(be('post','view_vodsearch')));
	
	$config['view']['artindex'] = intval(trim(be('post','view_artindex')));
	$config['view']['artmap'] = intval(trim(be('post','view_artmap')));
	$config['view']['arttype'] = intval(trim(be('post','view_arttype')));
	$config['view']['artlist'] = intval(trim(be('post','view_artlist')));
	$config['view']['arttopicindex'] = intval(trim(be('post','view_arttopicindex')));
	$config['view']['arttopic'] = intval(trim(be('post','view_arttopic')));
	$config['view']['artdetail'] = intval(trim(be('post','view_artdetail')));
	$config['view']['artsearch'] = intval(trim(be('post','view_artsearch')));
	$config['view']['gbook'] = intval(trim(be('post','view_gbook')));
	$config['view']['rss'] = intval(trim(be('post','view_rss')));
	$config['view']['label'] = intval(trim(be('post','view_label')));
	
	$config['path']['vodindex'] = trim(be('post','path_vodindex'));
	$config['path']['vodmap'] = trim(be('post','path_vodmap'));
	$config['path']['vodtype'] = trim(be('post','path_vodtype'));
	$config['path']['vodtopicindex'] = trim(be('post','path_vodtopicindex'));
	$config['path']['vodtopic'] = trim(be('post','path_vodtopic'));
	$config['path']['voddetail'] = trim(be('post','path_voddetail'));
	$config['path']['vodplay'] = trim(be('post','path_vodplay'));
	$config['path']['voddown'] = trim(be('post','path_voddown'));
	$config['path']['artindex'] = trim(be('post','path_artindex'));
	$config['path']['artmap'] = trim(be('post','path_artmap'));
	$config['path']['arttype'] = trim(be('post','path_arttype'));
	$config['path']['arttopicindex'] = trim(be('post','path_arttopicindex'));
	$config['path']['arttopic'] = trim(be('post','path_arttopic'));
	$config['path']['artdetail'] = trim(be('post','path_artdetail'));
	
	$config['rewrite']['vodindex'] = trim(be('post','rewrite_vodindex'));
	$config['rewrite']['vodmap'] = trim(be('post','rewrite_vodmap'));
	$config['rewrite']['vodtype'] = trim(be('post','rewrite_vodtype'));
	$config['rewrite']['vodlist'] = trim(be('post','rewrite_vodlist'));
	$config['rewrite']['vodtopicindex'] = trim(be('post','rewrite_vodtopicindex'));
	$config['rewrite']['vodtopic'] = trim(be('post','rewrite_vodtopic'));
	$config['rewrite']['voddetail'] = trim(be('post','rewrite_voddetail'));
	$config['rewrite']['vodplay'] = trim(be('post','rewrite_vodplay'));
	$config['rewrite']['voddown'] = trim(be('post','rewrite_voddown'));
	$config['rewrite']['vodsearch'] = trim(be('post','rewrite_vodsearch'));
	$config['rewrite']['artindex'] = trim(be('post','rewrite_artindex'));
	$config['rewrite']['artmap'] = trim(be('post','rewrite_artmap'));
	$config['rewrite']['artlist'] = trim(be('post','rewrite_artlist'));
	$config['rewrite']['arttype'] = trim(be('post','rewrite_arttype'));
	$config['rewrite']['arttopicindex'] = trim(be('post','rewrite_arttopicindex'));
	$config['rewrite']['arttopic'] = trim(be('post','rewrite_arttopic'));
	$config['rewrite']['artdetail'] = trim(be('post','rewrite_artdetail'));
	$config['rewrite']['artsearch'] = trim(be('post','rewrite_artsearch'));
	$config['rewrite']['gbook'] = trim(be('post','rewrite_gbook'));
	$config['rewrite']['rss'] = trim(be('post','rewrite_rss'));
	$config['rewrite']['label'] = trim(be('post','rewrite_label'));
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	
	redirect('?m=system-configurl');
}

elseif($method=='config')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	foreach($GLOBALS['MAC'] as $k1=>$v1){
		foreach($GLOBALS['MAC'][$k1] as $k2=>$v2){
			if($k1=='app' && $k2=='tj'){
				$plt->set_var($k1.'_'.$k2, str_replace('\\\'','\'',$v2) );
			}
			else{
				$plt->set_var($k1.'_'.$k2,$v2);
			}
		}
	}
	
	$plt->set_block('main', 'list_site_templatedir', 'rows_site_templatedir');
	foreach( glob('../template'.'/*',GLOB_ONLYDIR) as $v){
		if(is_dir($v)){
			$v = str_replace('../template/','',$v);
			$c = $MAC['site']['templatedir']==$v ? 'selected' : '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $v );
			$plt->set_var('c', $c );
			$plt->parse('rows_site_templatedir','list_site_templatedir',true);
		}
	}
	
	$plt->set_block('main', 'list_user_reggroup', 'rows_user_reggroup');
	foreach($MAC_CACHE['usergroup'] as $a){
		$n = $a['ug_name'];
		$v = $a['ug_id'];
		$c = $MAC['user']['reggroup']==$v ? 'selected' : '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_user_reggroup','list_user_reggroup',true);
	}
	
	
	$arr=array(
		array('p'=>'app','a'=>'cachetype','c'=>$MAC['app']['cachetype'],'t'=>0,'n'=>array('文件缓存','Memcached缓存'),'v'=>array(0,1)),
		array('p'=>'app','a'=>'cache','c'=>$MAC['app']['cache'],'t'=>0),
		array('p'=>'app','a'=>'dynamiccache','c'=>$MAC['app']['dynamiccache'],'t'=>0),
		array('p'=>'app','a'=>'compress','c'=>$MAC['app']['compress'],'t'=>0),
		array('p'=>'app','a'=>'playersort','c'=>$MAC['app']['playersort'],'t'=>0,'n'=>array('添加顺序','全局顺序'),'v'=>array(0,1)),
		array('p'=>'app','a'=>'encrypt','c'=>$MAC['app']['encrypt'],'t'=>1,'n'=>array('不加密','escape编码','base64编码'),'v'=>array(0,1,2)),
		array('p'=>'app','a'=>'isopen','c'=>$MAC['app']['isopen'],'t'=>1,'n'=>array('普通窗口','弹出窗口'),'v'=>array(0,1)),
		array('p'=>'app','a'=>'suffix','c'=>$MAC['app']['suffix'],'t'=>1,'n'=>array('htm','html','shtml'),'v'=>array('htm','html','shtml')),
		
		array('p'=>'user','a'=>'status','c'=>$MAC['user']['status'],'t'=>0),
		array('p'=>'user','a'=>'reg','c'=>$MAC['user']['reg'],'t'=>0),
		array('p'=>'user','a'=>'regstate','c'=>$MAC['user']['regstate'],'t'=>0),
		array('p'=>'user','a'=>'popularizestate','c'=>$MAC['user']['popularizestate'],'t'=>0),
						
		array('p'=>'other','a'=>'gbook','c'=>$MAC['other']['gbook'],'t'=>0),
		array('p'=>'other','a'=>'gbookaudit','c'=>$MAC['other']['gbookaudit'],'t'=>0),
		array('p'=>'other','a'=>'gbookverify','c'=>$MAC['other']['gbookverify'],'t'=>0),
		array('p'=>'other','a'=>'comment','c'=>$MAC['other']['comment'],'t'=>0),
		array('p'=>'other','a'=>'commentaudit','c'=>$MAC['other']['commentaudit'],'t'=>0),
		array('p'=>'other','a'=>'commentverify','c'=>$MAC['other']['commentverify'],'t'=>0),
		
		array('p'=>'upload','a'=>'thumb','c'=>$MAC['upload']['thumb'],'t'=>0),
		array('p'=>'upload','a'=>'watermark','c'=>$MAC['upload']['watermark'],'t'=>0),
		array('p'=>'upload','a'=>'waterlocation','c'=>$MAC['upload']['waterlocation'],'t'=>1,'n'=>array('居中','右上','右下','左上','左下'),'v'=>array(0,1,2,3,4)),
		array('p'=>'upload','a'=>'picpath','c'=>$MAC['upload']['picpath'],'t'=>1,'n'=>array('按日期','按月份','每目录500文件'),'v'=>array(0,1,2)),
		array('p'=>'upload','a'=>'ftp','c'=>$MAC['upload']['ftp'],'t'=>0),
		array('p'=>'upload','a'=>'remote','c'=>$MAC['upload']['remote'],'t'=>0),
		array('p'=>'upload','a'=>'ftpdel','c'=>$MAC['upload']['ftpdel'],'t'=>0)
	);
	
	foreach($arr as $a){
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['p'].'_'.$a['a'];
		$cv=$a['t']==0 ?'checked':'selected';
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? $cv: '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	unset($arr);
}

elseif($method=='configurl')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	foreach($GLOBALS['MAC'] as $k1=>$v1){
		foreach($GLOBALS['MAC'][$k1] as $k2=>$v2){
			if($k1=='app' && $k2=='tj'){
				$plt->set_var($k1.'_'.$k2, str_replace('\\\'','\'',$v2) );
			}
			else{
				$plt->set_var($k1.'_'.$k2,$v2);
			}
		}
	}
	
	$arr=array(
		array('p'=>'view','a'=>'gbook','c'=>$MAC['view']['gbook'],'t'=>1,'n'=>array('动态模式','rewrite伪静态'),'v'=>array(0,1)),
		array('p'=>'view','a'=>'rss','c'=>$MAC['view']['rss'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'label','c'=>$MAC['view']['label'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		
		array('p'=>'view','a'=>'vodsearch','c'=>$MAC['view']['vodsearch'],'t'=>1,'n'=>array('动态模式','rewrite伪静态'),'v'=>array(0,1)),
		array('p'=>'view','a'=>'artsearch','c'=>$MAC['view']['artsearch'],'t'=>1,'n'=>array('动态模式','rewrite伪静态'),'v'=>array(0,1)),
			
		array('p'=>'view','a'=>'vodlist','c'=>$MAC['view']['vodlist'],'t'=>1,'n'=>array('动态模式','rewrite伪静态'),'v'=>array(0,1)),
		array('p'=>'view','a'=>'artlist','c'=>$MAC['view']['artlist'],'t'=>1,'n'=>array('动态模式','rewrite伪静态'),'v'=>array(0,1)),
			
		array('p'=>'view','a'=>'vodindex','c'=>$MAC['view']['vodindex'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'vodmap','c'=>$MAC['view']['vodmap'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'vodtype','c'=>$MAC['view']['vodtype'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'vodtopicindex','c'=>$MAC['view']['vodtopicindex'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'vodtopic','c'=>$MAC['view']['vodtopic'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'voddetail','c'=>$MAC['view']['voddetail'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
			
		array('p'=>'view','a'=>'vodplay','c'=>$MAC['view']['vodplay'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态每数据一页','静态每集一页','静态每组一页'),'v'=>array(0,1,2,3,4)),
		array('p'=>'view','a'=>'voddown','c'=>$MAC['view']['voddown'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态每数据一页','静态每集一页','静态每组一页'),'v'=>array(0,1,2,3,4)),
		array('p'=>'view','a'=>'artindex','c'=>$MAC['view']['artindex'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'artmap','c'=>$MAC['view']['artmap'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'arttopicindex','c'=>$MAC['view']['arttopicindex'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'arttype','c'=>$MAC['view']['arttype'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'arttopic','c'=>$MAC['view']['arttopic'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		array('p'=>'view','a'=>'artdetail','c'=>$MAC['view']['artdetail'],'t'=>1,'n'=>array('动态模式','rewrite伪静态','静态模式'),'v'=>array(0,1,2)),
		
	);
	
	foreach($arr as $a){
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['p'].'_'.$a['a'];
		$cv=$a['t']==0 ?'checked':'selected';
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? $cv: '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	
	unset($arr);
}

elseif($method=='configplaysave')
{
	if( trim(be('post','mac_width'))=='' ){
		alert('请输入播放器宽度');
		exit;
	}
	
	$mac_flag = be('post','mac_flag');
	$mac_second = be('post','mac_second');
	$mac_width = be('post','mac_width');
	$mac_height = be('post','mac_height');
	$mac_widthpop = be('post','mac_widthpop');
	$mac_heightpop = be('post','mac_heightpop');
	$mac_showtop = be('post','mac_showtop');
	$mac_showlist = be('post','mac_showlist');
	$mac_autofull = be('post','mac_autofull');
	$mac_buffer = be('post','mac_buffer');
	$mac_prestrain = be('post','mac_prestrain');
	$mac_colors = be('post','mac_colors');
	
	
	$fp = '../js/playerconfig.js.bak';
	if(!file_exists($fp)){ $fp .= '.bak'; }
	$fc = file_get_contents( $fp );
	$fc = regReplace($fc,"var\smac_flag\=(\d+?)\;","var mac_flag=".$mac_flag.";");
	$fc = regReplace($fc,"var\smac_second\=(\d+?)\;","var mac_second=".$mac_second.";");
	$fc = regReplace($fc,"var\smac_width\=(\d+?)\;","var mac_width=".$mac_width.";");
	$fc = regReplace($fc,"var\smac_height\=(\d+?)\;","var mac_height=".$mac_height.";");
	$fc = regReplace($fc,"var\smac_widthpop\=(\d+?)\;","var mac_widthpop=".$mac_widthpop.";");
	$fc = regReplace($fc,"var\smac_heightpop\=(\d+?)\;","var mac_heightpop=".$mac_heightpop.";");
	$fc = regReplace($fc,"var\smac_showtop\=(\d+?)\;","var mac_showtop=".$mac_showtop.";");
	$fc = regReplace($fc,"var\smac_showlist\=(\d+?)\;","var mac_showlist=".$mac_showlist.";");
	$fc = regReplace($fc,"var\smac_autofull\=(\d+?)\;","var mac_autofull=".$mac_autofull.";");
	$fc = regReplace($fc,"var\smac_buffer\=*\"*(\S+?)'*\"*\;","var mac_buffer=\"".$mac_buffer."\";");
	$fc = regReplace($fc,"var\smac_prestrain\=*\"*(\S+?)'*\"*\;","var mac_prestrain=\"".$mac_prestrain."\";");
	$fc = regReplace($fc,"var\smac_colors\=*\"*(\S+?)'*\"*\;","var mac_colors=\"".$mac_colors."\";");
	
	fwrite(fopen('../js/playerconfig.js','wb'),$fc);
    redirect('?m=system-configplay');
}

elseif($method=='configplay')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$fp = '../js/playerconfig.js';
	if(!file_exists($fp)){ $fp .= '.bak'; }
	$fc = file_get_contents( $fp );
	$mac_flag = regMatch($fc,"var\smac_flag\=(\d+?)\;");
	$mac_second = regMatch($fc,"var\smac_second\=(\d+?)\;");
	$mac_width = regMatch($fc,"var\smac_width\=(\d+?)\;");
	$mac_height = regMatch($fc,"var\smac_height\=(\d+?)\;");
	$mac_widthpop= regMatch($fc,"var\smac_widthpop\=(\d+?)\;");
	$mac_heightpop=regMatch($fc,"var\smac_heightpop\=(\d+?)\;");
	$mac_showtop = regMatch($fc,"var\smac_showtop\=(\d+?)\;");
	$mac_showlist = regMatch($fc,"var\smac_showlist\=(\d+?)\;");
	$mac_autofull=regMatch($fc,"var\smac_autofull\=(\d+?)\;");
	$mac_buffer=regMatch($fc,"var\smac_buffer\=*\"*(\S+?)'*\"*\;");
	$mac_prestrain=regMatch($fc,"var\smac_prestrain\=*\"*(\S+?)'*\"*\;");
	$mac_colors=regMatch($fc,"var\smac_colors\=*\"*(\S+?)'*\"*\;");
	
	$colarr=array('mac_second','mac_width','mac_height','mac_widthpop','mac_heightpop','mac_buffer','mac_prestrain','mac_colors');
	$valarr=array($mac_second,$mac_width,$mac_height,$mac_widthpop,$mac_heightpop,$mac_buffer,$mac_prestrain,$mac_colors);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	$arr=array(
		array('p'=>'mac','a'=>'autofull','c'=>$mac_autofull,'t'=>0),
		array('p'=>'mac','a'=>'showtop','c'=>$mac_showtop,'t'=>0),
		array('p'=>'mac','a'=>'showlist','c'=>$mac_showlist,'t'=>0),
		array('p'=>'mac','a'=>'flag','c'=>$mac_flag,'t'=>0,'n'=>array('1，本地播放器文件') ,'v'=>array(1) )
	);
	foreach($arr as $a){
		
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['p'].'_'.$a['a'];
		$cv=$a['t']==0 ?'checked':'selected';
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? $cv: '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	unset($arr);
	unset($colarr);
	unset($valarr);
}

elseif($method=='configconnectsave')
{
	$config = $MAC;
	$config['connect']['qq']['status'] = intval(trim(be('post','qq_status')));
	$config['connect']['qq']['id'] = trim(be('post','qq_id'));
	$config['connect']['qq']['key'] = trim(be('post','qq_key'));
	
	$config['connect']['uc']['status'] = intval(trim(be('post','uc_status')));
	$config['connect']['uc']['id'] = trim(be('post','uc_id'));
	$config['connect']['uc']['key'] = trim(be('post','uc_key'));
	$config['connect']['uc']['url'] = trim(be('post','uc_url'));
	$config['connect']['uc']['ip'] = trim(be('post','uc_ip'));
	$config['connect']['uc']['dbhost'] = trim(be('post','uc_dbhost'));
	$config['connect']['uc']['dbuser'] = trim(be('post','uc_dbuser'));
	$config['connect']['uc']['dbpass'] = trim(be('post','uc_dbpass'));
	$config['connect']['uc']['dbname'] = trim(be('post','uc_dbname'));
	$config['connect']['uc']['dbpre'] = trim(be('post','uc_dbpre'));
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	redirect('?m=system-configconnect');
}

elseif($method=='configconnect')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	foreach($MAC['connect'] as $k1=>$v1){
		foreach($MAC['connect'][$k1] as $k2=>$v2){
			$plt->set_var($k1.'_'.$k2,$v2);
		}
	}
	
	$arr=array(
		array('p'=>'qq','a'=>'status','c'=>$MAC['connect']['qq']['status'],'t'=>0,'n'=>array('关闭','开启'),'v'=>array(0,1))
	);
	foreach($arr as $a){
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['p'].'_'.$a['a'];
		$cv=$a['t']==0 ?'checked':'selected';
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? $cv: '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	unset($arr);
	unset($colarr);
	unset($valarr);
}

elseif($method=='configapisave')
{
	$config = $MAC;
	
	$config['api']['vod']['status'] = intval(trim(be('post','vod_status')));
	$config['api']['vod']['cjflag'] = trim(be('post','vod_cjflag'));
	$config['api']['vod']['typefilter'] = trim(be('post','vod_typefilter'));
	$config['api']['vod']['vodfilter'] = trim(be('post','vod_vodfilter'));
	$config['api']['vod']['pagesize'] = !isNum(be('post','vod_pagesize')) ? 20 : intval(trim(be('post','vod_pagesize')));
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	redirect('?m=system-configapi');
}

elseif($method=='configapi')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	foreach($MAC['api'] as $k1=>$v1){
		foreach($MAC['api'][$k1] as $k2=>$v2){
			$plt->set_var($k1.'_'.$k2,$v2);
		}
	}
	$colarr=array('关闭','开启');
	$valarr=array(0,1);
	$rn='vod_status';
	
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$c = $MAC['api']['vod']['status']==$v ? 'checked': '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='configpaysave')
{
	$config = $MAC;
	$config['pay']['app']['min'] = !isNum(be('post','app_min')) ? 10 : intval(trim(be('post','app_min')));
	$config['pay']['app']['exc'] = !isNum(be('post','app_exc')) ? 1 : intval(trim(be('post','app_exc')));
	$config['pay']['ys']['id'] = trim(be('post','ys_id'));
	$config['pay']['ys']['key'] = trim(be('post','ys_key'));
	$config['pay']['alipay']['no'] = trim(be('post','alipay_no'));
	$config['pay']['alipay']['id'] = trim(be('post','alipay_id'));
	$config['pay']['alipay']['key'] = trim(be('post','alipay_key'));
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	redirect('?m=system-configpay');
}

elseif($method=='configpay')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	foreach($MAC['pay'] as $k1=>$v1){
		foreach($MAC['pay'][$k1] as $k2=>$v2){
			$plt->set_var($k1.'_'.$k2,$v2);
		}
	}
}

elseif($method=='configcollectsave')
{
	$config = $MAC;
	
	$config['collect']['vod']['key'] = trim(be('post','vod_key'));
	$config['collect']['vod']['hitsstart'] = intval(trim(be('post','vod_hitsstart')));
	$config['collect']['vod']['hitsend'] = intval(trim(be('post','vod_hitsend')));
	$config['collect']['vod']['score'] = intval(be('post','vod_score'));
	$config['collect']['vod']['hide'] = intval(be('post','vod_hide'));
	$config['collect']['vod']['pic'] = intval(be('post','vod_pic'));
	$config['collect']['vod']['tag'] = intval(be('post','vod_tag'));
	$config['collect']['vod']['psernd'] = intval(be('post','vod_psernd'));
	$config['collect']['vod']['psesyn'] = intval(be('post','vod_psesyn'));
	$config['collect']['vod']['inrule'] = ','.trim(be('arr','vod_inrule'));
	$config['collect']['vod']['uprule'] = ','.trim(be('arr','vod_uprule'));
	$config['collect']['vod']['filter'] = trim(be('post','vod_filter'));
	
	$config['collect']['art']['key'] = trim(be('post','art_key'));
	$config['collect']['art']['hitsstart'] = intval(trim(be('post','art_hitsstart')));
	$config['collect']['art']['hitsend'] = intval(trim(be('post','art_hitsend')));
	$config['collect']['art']['hide'] = intval(be('post','art_hide'));
	$config['collect']['art']['pic'] = intval(be('post','art_pic'));
	$config['collect']['art']['tag'] = intval(be('post','art_tag'));
	$config['collect']['art']['psernd'] = intval(be('post','art_psernd'));
	$config['collect']['art']['psesyn'] = intval(be('post','art_psesyn'));
	$config['collect']['art']['inrule'] = ','.trim(be('arr','art_inrule'));
	$config['collect']['art']['uprule'] = ','.trim(be('arr','art_uprule'));
	$config['collect']['art']['filter'] = trim(be('post','art_filter'));
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	redirect('?m=system-configcollect');
}

elseif($method=='configcollect')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	foreach($MAC['collect'] as $k1=>$v1){
		foreach($MAC['collect'][$k1] as $k2=>$v2){
			$plt->set_var($k1.'_'.$k2,$v2);
		}
	}
	
	$arr=array(
		array('p'=>'vod','a'=>'hide','c'=>$MAC['collect']['vod']['hide'],'t'=>0,'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('p'=>'vod','a'=>'score','c'=>$MAC['collect']['vod']['score'],'t'=>0),
		array('p'=>'vod','a'=>'pic','c'=>$MAC['collect']['vod']['pic'],'t'=>0),
		array('p'=>'vod','a'=>'tag','c'=>$MAC['collect']['vod']['tag'],'t'=>0),
		array('p'=>'vod','a'=>'psernd','c'=>$MAC['collect']['vod']['psernd'],'t'=>0),
		array('p'=>'vod','a'=>'psesyn','c'=>$MAC['collect']['vod']['psesyn'],'t'=>0),
		array('p'=>'art','a'=>'hide','c'=>$MAC['collect']['art']['hide'],'t'=>0,'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('p'=>'art','a'=>'pic','c'=>$MAC['collect']['art']['pic'],'t'=>0),
		array('p'=>'art','a'=>'tag','c'=>$MAC['collect']['art']['tag'],'t'=>0),
		array('p'=>'art','a'=>'psernd','c'=>$MAC['collect']['art']['psernd'],'t'=>0),
		array('p'=>'art','a'=>'psesyn','c'=>$MAC['collect']['art']['psesyn'],'t'=>0),
		
		array('p'=>'vod','a'=>'inrule','c'=>$MAC['collect']['vod']['inrule'],'t'=>1,'n'=>array('分类','年代','地区','语言','主演','导演') ,'v'=>array('b','c','d','e','f','g') ),
		array('p'=>'vod','a'=>'uprule','c'=>$MAC['collect']['vod']['uprule'],'t'=>1,'n'=>array('播放地址','下载地址','状态','备注','导演','主演','年代','地区','语言','图片','介绍','TAG') ,'v'=>array('a','b','c','d','e','f','g','h','i','j','k','l') ),
		array('p'=>'art','a'=>'inrule','c'=>$MAC['collect']['art']['inrule'],'t'=>1,'n'=>array('分类') ,'v'=>array('b') ),
		array('p'=>'art','a'=>'uprule','c'=>$MAC['collect']['art']['uprule'],'t'=>1,'n'=>array('介绍','作者','来源','图片','TAG') ,'v'=>array('a','b','c','d','e') )
	);
	foreach($arr as $a){
		
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['p'].'_'.$a['a'];
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			
			if($a['t']==0){
				$c = $a['c']==$v ? 'checked': '';
			}
			else{
				$c = strpos($a['c'],$v) ? 'checked': '';
			}
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	unset($arr);
	unset($colarr);
	unset($valarr);
	
}

elseif($method=='configinterfacesave')
{
	$vodtype = be("post", "vodtype");
    $arttype = be("post", "arttype");
    
    fwrite(fopen("../inc/config/interface_vodtype.txt","wb"),$vodtype);
    fwrite(fopen("../inc/config/interface_arttype.txt","wb"),$arttype);
    
    $config = $MAC;
    $config['interface']['pass'] = trim(be('post','pass'));
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen('../inc/config/config.php','wb'),$configstr);
	
	redirect('?m=system-configinterface');
}

elseif($method=='configinterface')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$fc1 = file_get_contents("../inc/config/interface_vodtype.txt");
    $fc2 = file_get_contents("../inc/config/interface_arttype.txt");
    
    $colarr=array('vodtype','arttype','pass');
    $valarr=array($fc1,$fc2,$MAC['interface']['pass']);
    for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='timminginfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$xp = '../inc/config/timmingset.xml';
	
	$flag=empty($p['name']) ? 'add' : 'edit';
	$backurl=getReferer();
	
	if($flag=='edit'){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("timming");
		foreach($nodes as $node){
			if ($p['name'] == $node->getElementsByTagName("name")->item(0)->nodeValue){
				$name = $node->getElementsByTagName("name")->item(0)->nodeValue;
				$des = $node->getElementsByTagName("des")->item(0)->nodeValue;
				$status = $node->getElementsByTagName("status")->item(0)->nodeValue;
				$file = $node->getElementsByTagName("file")->item(0)->nodeValue;
				$paramets = $node->getElementsByTagName("paramets")->item(0)->nodeValue;
				$weeks = ','.$node->getElementsByTagName("weeks")->item(0)->nodeValue;
				$hours = ','.$node->getElementsByTagName("hours")->item(0)->nodeValue;
				break;
			}
		}
		unset($xmlnode);
    	unset($nodes);
    	unset($doc);
    	$plt->set_if('main','isedit',true);
	}
	else{
		$plt->set_if('main','isedit',false);
	}
	
	$colarr=array('flag','backurl','name','des','status','runtime','file','paramets');
	$valarr=array($flag,$backurl,$name,$des,$status,$runtime,$file,$paramets);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	
	$arr=array(
		array('a'=>'status','c'=>$status,'t'=>0,'n'=>array('禁用','启用'),'v'=>array('0','1')),
		
		array('a'=>'weeks','c'=>$weeks,'t'=>1,'n'=>array('周一','周二','周三','周四','周五','周六','周日') ,'v'=>array('1','2','3','4','5','6','0') ),
		array('a'=>'hours','c'=>$hours,'t'=>1,'n'=>array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23') ,'v'=>array('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23') )
	);
	foreach($arr as $a){
		
		if(!is_array($a['n'])){
			$colarr=array('关闭','开启');
			$valarr=array(0,1);
		}
		else{
			$colarr=$a['n'];
			$valarr=$a['v'];
		}
		
		$rn=$a['a'];
		
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			
			if($a['t']==0){
				$c = $a['c']==$v ? 'selected': '';
			}
			else{
				$c = strpos($a['c'],$v) ? 'checked': '';
			}
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($colarr);
		unset($valarr);
	}
	unset($arr);
	unset($colarr);
	unset($valarr);
}

elseif($method=='timming')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$xp = '../inc/config/timmingset.xml';
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xp);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName('timming');
	
	if($nodes->length==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='timming';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$colarr=array('name','des','status','runtime','file','paramets');
	$num=0;
	foreach($nodes as $node){
		$num++;
		$name = $node->getElementsByTagName('name')->item(0)->nodeValue;
		$des = $node->getElementsByTagName('des')->item(0)->nodeValue;
		$status = $node->getElementsByTagName('status')->item(0)->nodeValue;
		$runtime = $node->getElementsByTagName('runtime')->item(0)->nodeValue;
		if(!empty($runtime)) { $runtime = getColorDay($runtime); }
		$file = $node->getElementsByTagName('file')->item(0)->nodeValue;
		$paramets = $node->getElementsByTagName('paramets')->item(0)->nodeValue;
		$paramets = str_replace('&amp;','&',$paramets);
		$status = $status=='1' ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		$valarr=array($name,$des,$status,$runtime,$file,$paramets);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($xmlnode);
    unset($nodes);
    unset($doc);
	unset($colarr);
	unset($valarr);
}

elseif($method=='cache')
{
	$flag = $p['flag'];
	if($flag=='data'){
		updateCacheFile();
	}
	elseif($flag=='file'){
		$cachePath= root.'upload/cache';
		if ($handle = opendir($cachePath)){
			if (is_dir("$cachePath/app")){
				delFileUnderDir("$cachePath/app");
			}
			if (is_dir("$cachePath/vodlist")){
				delFileUnderDir("$cachePath/vodlist");
			}
			if (is_dir("$cachePath/artlist")){
				delFileUnderDir("$cachePath/artlist");
			}
			if (is_dir("$cachePath/search")){
				delFileUnderDir("$cachePath/search");
			}
			if (is_dir("$cachePath/client")){
				delFileUnderDir("$cachePath/client");
			}
		}
		closedir( $handle );
		unset($handle);
	}
	elseif($flag=='index'){
		if(file_exists("../index.html")){ unlink("../index.html"); }
		if(file_exists("../index.htm")){ unlink("../index.htm"); }
		if(file_exists("../index.shtml")){ unlink("../index.shtml"); }
	}
}

elseif($method=='configpsesave')
{
	$flag = be("post","flag");
	if(empty($flag)){$flag='vod'; }
	
	$psernd = be("post", "psernd");
    $psesyn = be("post", "psesyn");
    @fwrite(fopen('../inc/config/pse_'.$flag.'rnd.txt','wb'),$psernd);
	@fwrite(fopen('../inc/config/pse_'.$flag.'syn.txt','wb'),$psesyn);
	redirect('?m=system-configpse-flag-'.$flag);
}

elseif($method=='configpse')
{
	$flag = $p['flag'];
	if(empty($flag)){$flag='vod'; }
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$psernd = @file_get_contents('../inc/config/pse_'.$flag.'rnd.txt');
	$psesyn = @file_get_contents('../inc/config/pse_'.$flag.'syn.txt');
	$colarr=array('psernd','psesyn','flag');
    $valarr=array($psernd,$psesyn,$flag);
    for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	unset($colarr);
	unset($valarr);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>