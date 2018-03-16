<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}
$tpl->P['static']=true;
if($method=='index')
{
	$tab = 'vod';
	$jump=$p['jump'];
	$tpl->P['pg']=1;
	if(!empty($p['tab'])){
		$tab=$p['tab'];
		$tpl->P["siteaid"] = 20;
		$ln = 'art'.$method;
	}
	else{
		$tpl->P["siteaid"] = 10;
		$ln = $method;
	}
	
	if($MAC['view'][$tab.$method] !=2){
		echo '首页浏览模式非静态，不需要生成静态文件<br>';
	}
	else{
		$tpl->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$tab.'_'.$method.'.html');
		$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
		$tpl->mark();
		$tpl->pageshow();
		$tpl->ifex();
		$lnk = '../'.$ln.'.'.$MAC['app']['suffix'];;
		fwrite(fopen($lnk,'wb'),$tpl->H);
		echo  '生成完毕 <a target="_blank" href="'. $lnk.'"><font color=red>浏览</font></a><br>';
	}
}

elseif($method=='map')
{
	$tab = 'vod';
	$jump=$p['jump'];
	$tpl->P['pg']=1;
	if(!empty($p['tab'])){
		$tab=$p['tab'];
		$tpl->P["siteaid"] = 21;
		$ln = 'art'.$method;
	}
	else{
		$tpl->P["siteaid"] = 11;
		$ln = $method;
	}
	if($MAC['view'][$tab.$method] !=2){
		echo '地图页面浏览模式非静态，不需要生成静态文件<br>';
	}
	else{
	$tpl->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$tab.'_'.$method.'.html');
	$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
	$tpl->mark();
	$tpl->pageshow();
	$tpl->ifex();
	$lnk = '../'.$ln.'.'.$MAC['app']['suffix'];;
	fwrite(fopen($lnk,'wb'),$tpl->H);
	echo  '生成完毕 <a target="_blank" href="'. $lnk.'"><font color=red>浏览</font></a><br>';
	}
}

elseif($method=='rss')
{
	if($MAC['view']['rss'] !=2){
		echo 'Rss页面浏览模式非静态，不需要生成静态文件<br>';
	}
	else{
	$ac2=$p['ac2'];
	if($ac2=='google'){
		$all = be("all","googleall");
		$num = be("all","google");
	}
	elseif($ac2=='baidu'){
		$all = be("all","baiduall");
		$num = be("all","baidu");
	}
	elseif($ac2=='rss'){
		$all = be("all","rss");
		$num = be("all","rss");
	}
	$pagesize = ceil($all / $num);
	$tpl->H = loadFile(MAC_ROOT.'/inc/map/'.$ac2.'.html');
	
	$tpl->base();
	
	for($i=1;$i<=$pagesize;$i++){
		$sql = 'SELECT d_id,d_name,d_enname,d_type,d_addtime,d_time,d_content FROM {pre}vod WHERE d_hide=0 and d_type >0 ORDER BY d_time DESC ';
		$sql .= 'limit '.($num*($i-1)).','.$num;
		
		$labelRule = buildregx("{maccms:vod([\s\S]*?)}([\s\S]*?){/maccms:vod}","");
		preg_match_all($labelRule ,$tpl->H,$matches1);
		$markhtml='';
		$n=0;
		
        for($j=0;$j<count($matches1[0]);$j++)
		{
			$markval = $matches1[0][$j];
            $markpar = $matches1[1][$j];
            $markdes = $matches1[2][$j];
            
            $rs = $db->query($sql);
			if(!$rs){
				$markhtml = '';
			}
			else{
				$labelRule = buildregx("\[vod:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$markdes,$matches2);
				while ($row = $db ->fetch_array($rs))
				{
			        $n++;
			        $marktemp = $markdes;
			        for($k=0;$k<count($matches2[0]);$k++)
					{
						$marktemp = $tpl->parse("vod", $marktemp, $matches2[0][$k], $matches2[1][$k], $matches2[2][$k], $row, $n);
					}
			        $markhtml .= $marktemp;
				}
			}
			unset($rs);
		}
		unset($matches2);
		unset($matches1);
		if($i>1){ $pn='_'.$i;}
		
		$fn = $ac2.$pn.'.xml';
		$lnk = '../'.$fn;
		$html = str_replace($markval, $markhtml,$tpl->H);
		fwrite(fopen($lnk,'wb'),$html);
		echo  '<a target="_blank" href="'. $lnk.'">'.$fn.'</a><br>';
		ob_flush();flush();
	}
	}
}

elseif($method=='label')
{
	if($MAC['view']['label'] !=2){
		echo '自定义页面浏览模式非静态，不需要生成静态文件';
		exit;
	}
	
	$ac2=$p['ac2'];
	$jump=$p['jump'];
	$ids = be('arr','label');
	$num=0;
	if(empty($ids)){
		$ids=$p['ids'];
		$num=intval($p['num']);
	}
	$arr = explode(',',$ids);
	$len = count($arr);
	
	$id = $arr[$num];
	
	$tpl->P['pagetype'] = "label";
	$tpl->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$id);
	$tpl->P['make'] = true;
	$tpl->mark();
	$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
    $html = $tpl->H;
	$tpl->P['make']='';
	$tpl->mark();
	$maxpage = intval($tpl->P['maxpage']);
	if($maxpage==0) { $maxpage=1; }
	$tpl->pageshow();
	$tpl->ifex();
	
	$fname = str_replace(array('label_','$$'),array('','/'),$id);
	$path = strpos($fname,'/')>1 ? substring($fname,strrpos($fname,'/')+1) : '';
	$path = '../' . $path;
	$fname = substring($fname, strlen($fname) - strrpos($fname,"/"),strrpos($fname,"/") );
	$lnk = $path . $fname;
	$path = dirname($lnk);
	mkdirs($path);
	fwrite(fopen($lnk,"wb"),$tpl->H);
	echo '<a target="_blank" href="'.$lnk.'">'.str_replace( '../', '',$lnk).'</a><br>';
	
	$arr = explode(".",$fname);
	if (count($arr) >0){ $dname=$arr[0]; $suffix = $arr[1];} else { $dname=$arr[0] ; $suffix="html";}
    unset($arr);
    
    for($i=2;$i<=$maxpage;$i++){
    	$tpl->H = $html;
    	$tpl->P['pg']=$i;
    	$tpl->P["auto"] = false;
    	$tpl->mark();
    	$tpl->ifex();
    	$lnk = $path .'/'. $dname . $i . '.' . $suffix;
    	fwrite(fopen($lnk,"wb"),$tpl->H);
    	echo '<a target="_blank" href="'.$lnk.'">'.str_replace( '../', '',$lnk).'</a><br>';
    }
}

elseif($method=='type')
{
	$tab = 'vod';
	if(!empty($p['tab'])){
		$tab=$p['tab'];
	}
	if($tab=='art'){
		$tpl->P["siteaid"] = 22;
		$pre='a';
		$typearr=array();
		$sql='select distinct a_type from {pre}art where a_time >='.strtotime("today");
		$rs = $db->queryarray($sql,false);
		foreach($rs as $a){
			array_push($typearr,$a['d_type']);
		}
		unset($rs);
	}
	else{
		$tpl->P["siteaid"] = 12;
		$pre='d';
		$typearr=array();
		$sql='select distinct d_type from {pre}vod where d_time >='.strtotime("today");
		$rs = $db->queryarray($sql,false);
		foreach($rs as $a){
			array_push($typearr,$a['d_type']);
		}
		unset($rs);
	}
	if($MAC['view'][$tab.'type'] !=2){
 		echo '列表浏览模式非静态，不需要生成静态文件<br>';
 		exit;
	}
	
	$num=0;
	if(!isNum($start)) { $start = 1;}
	
	foreach($typearr as $id)
	{
		$tpl->T  = $MAC_CACHE[$tab.'type'][$id];
		$tpl->P[$tab.'typeid'] = $id;
		$tpl->P['id'] = $id;
		$tpl->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$tpl->T['t_tpl']);
			
		if(empty($pagesize)){ $pagesize = $tpl->getPageListSizeByCache($tab,'type'); }
		if(!isNum($pagesize)) { $pagesize = 10; }
		if(empty($datacount)){
			$sql = 'select count(*) from {pre}'.$tab.' where '.$pre.'_type IN ('. $tpl->T["childids"].')';
			$datacount = $db->getOne($sql);
			$pagecount = ceil($datacount/$pagesize);
		}
		if($datacount==0){ $pagecount=1; }
		echo '正在开始生成分类<font color=red>'.$tpl->T["t_name"].'</font>的列表<br>';;
		$rc=true;
		$tpl->P['datacount'] = $datacount;
		$tpl->P['make']=true;
		$tpl->loadtype($tab);
		$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
		$html = $tpl->H;
		$maxpage = intval($tpl->P['maxpage']);
		$tpl->P['make']='';
		$pageurl = $tpl->getLink($tab,'type',$tpl->T,'','{pg}');
		$pageurl = str_replace('pg-{pg}','{pg}',$pageurl);
		
		$n=1;
		$pagego=1;
		if($maxpage>0 && $maxpage<$pagecount){
			$pagecount=$maxpage;
		}
		elseif($maxpage>5 && $pagecount>5){
			$maxpage=5;
		}
		
		for($i=$start;$i<=$pagecount;$i++){
			$tpl->P['pg'] = $i;
			$tpl->H = $html;
			$tpl->P["auto"] = false;
			$tpl->mark();
			$tpl->pageshow('',$pageurl);
			$tpl->ifex();
			if($i==1){
				$lnk = str_replace('-{pg}','',$pageurl);
			}
			else{
				$lnk = str_replace('{pg}',$i,$pageurl);
			}
			if($MAC['site']['installdir']!="/"){ $lnk=str_replace($MAC['site']['installdir'],"../",$lnk); } else { $lnk="..".$lnk; }
			if(substr($lnk,strlen($lnk)-1,1) =="/"){ $lnk .= "index.".$MAC['app']['suffix']; }
			$path = dirname($lnk);
			mkdirs($path);
			fwrite(fopen($lnk,"wb"),$tpl->H);
			echo '<a target="_blank" href="'.$lnk.'">第'.$i.'页'.str_replace( "../", "",$lnk)."</a><br>";
			$n++;
			ob_flush();flush();
			if(10==$n){
			break;
			}
		}
	}
	unset($typearr);
}

elseif($method=='topicindex')
{
	$tab = 'vod';
	if(!empty($p['tab'])){
		$tab=$p['tab'];
	}
	if($MAC['view'][$tab.'topicindex'] !=2){
		echo '专题首页浏览模式非静态，不需要生成静态文件<br>';
		exit;
	}
	if($tab=='art'){
		$tpl->P["siteaid"] = 23;
	}
	else{
		$tpl->P["siteaid"] = 13;
	}
		
	$tpl->H = loadFile(MAC_ROOT_TEMPLATE."/".$tab."_topicindex.html");
	if(empty($pagesize)){ $pagesize = $tpl->getPageListSizeByCache('topic',$tab); }
	if(!isNum($pagesize)) { $pagesize = 10;}
	if(empty($datacount)){
		$sql = 'select count(*) from {pre}'.$tab.'_topic where t_hide=0 ';
		$datacount = $db->getOne($sql);
	   	$pagecount = ceil($datacount/$pagesize);
	}
	if($datacount==0){ $pagecount=1; }
	    
	echo '正在开始生成专题首页列表,共<font color=red>'.$pagecount.'</font>页<br>';
	$rc=true;
	$tpl->P['datacount'] = $datacount;
	$tpl->P['make']=true;
	$tpl->mark();
	$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
	$html = $tpl->H;
	$tpl->P['make']='';
	$pageurl = $tpl->getLink($tab,'topicindex','','','{pg}');
	$pageurl = str_replace('pg-{pg}','{pg}',$pageurl);
	    
	for($i=1;$i<=$pagecount;$i++){
		$tpl->P['pg'] = $i;
		$tpl->H = $html;
		$tpl->mark();
		$tpl->pageshow('',$pageurl);
		$tpl->ifex();
		if($i==1){
		$lnk = str_replace('-{pg}','',$pageurl);
		}
		else{
		$lnk = str_replace('{pg}',$i,$pageurl);
		}
		if($MAC['site']['installdir']!="/"){ $lnk=str_replace($MAC['site']['installdir'],"../",$lnk); } else { $lnk="..".$lnk; }
		if(substr($lnk,strlen($lnk)-1,1) =="/"){ $lnk .= "index.".$MAC['app']['suffix']; }
		$path = dirname($lnk);
	        
		mkdirs($path);
		fwrite(fopen($lnk,"wb"),$tpl->H);
		echo '<a target="_blank" href="'.$lnk.'">第'.$i.'页'.str_replace( "../", "",$lnk)."</a><br>";
		ob_flush();flush();
	}
}


elseif($method=='topic')
{
	$tab = 'vod';
	if(!empty($p['tab'])){
		$tab=$p['tab'];
	}
	if($MAC['view'][$tab.'topic'] !=2){
		echo '专题浏览模式非静态，不需要生成静态文件';
		exit;
	}
	if($tab=='art'){
		$tpl->P["siteaid"] = 24;
		$pre='a';
	}
	else{
		$tpl->P["siteaid"] = 14;
		$pre='d';
	}
	$ids = be('arr',$tab.'topic');
	$num=0;
	if(empty($ids)){
		$ids=$p['ids'];
		$num=intval($p['num']);
		$jump=$p['jump'];
		$pagesize=$p['pagesize'];
		$pagecount=$p['pagecount'];
		$datacount=$p['datacount'];
		$start=intval($p['start']);
	}
	if(!isNum($start)) { $start = 1;}
	
	$arr = explode(',',$ids);
	$len = count($arr);
			$id = $arr[$num];
	$tpl->T  = $MAC_CACHE[$tab.'topic'][$id];
	$tpl->P[$tab.'topicid'] = $id;
	$tpl->P['id'] = $id;
	$tpl->H = loadFile(MAC_ROOT_TEMPLATE."/".$tpl->T['t_tpl']);
	
	if(empty($pagesize)){ $pagesize = $tpl->getPageListSizeByCache($tab,'topic'); }
	if(!isNum($pagesize)) { $pagesize = 10;}
	if(empty($datacount)){
		$sql = 'select count(*) from {pre}'.$tab.' where '.$pre.'_id IN ( select r_b from {pre}'.$tab.'_relation where r_type=2 and r_a='.$id.')';
		$datacount = $db->getOne($sql);
		$pagecount = ceil($datacount/$pagesize);
	}
	if($datacount==0){ $pagecount=1; }
	    
	echo '正在开始生成专题<font color=red>'.$tpl->T["t_name"].'</font>的列表<br>';
	$rc=true;
	$tpl->P['datacount'] = $datacount;
	$tpl->P['make']=true;
	$tpl->loadtopic($tab);
	$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
	$html = $tpl->H;
	$tpl->P['make']='';
	$pageurl = $tpl->getLink($tab,'topic',$tpl->T,'','{pg}');
	$pageurl = str_replace('pg-{pg}','{pg}',$pageurl);
	    
	$n=1;
	$pagego=1;
	for($i=$start;$i<=$pagecount;$i++){
		$tpl->P['pg'] = $i;
		$tpl->H = $html;
		$tpl->P['auto'] = false;
		$tpl->mark();
		$tpl->pageshow('',$pageurl);
		$tpl->ifex();
	    	
		if($i==1){
			$lnk = str_replace('-{pg}','',$pageurl);
		}
		else{
			$lnk = str_replace('{pg}',$i,$pageurl);
		}
	    
		if($MAC['site']['installdir']!="/"){ $lnk=str_replace($MAC['site']['installdir'],"../",$lnk); } else { $lnk="..".$lnk; }
		if(substr($lnk,strlen($lnk)-1,1) =="/"){ $lnk .= "index.".$MAC['app']['suffix']; }
		$path = dirname($lnk);
		($path);
		fwrite(fopen($lnk,"wb"),$tpl->H);
		echo '<a target="_blank" href="'.$lnk.'">第'.$i.'页'.str_replace( "../", "",$lnk)."</a><br>";
		$n++;
		ob_flush();flush();
		if(10==$n){
			break;
		}
	}
}

elseif($method=='info')
{
	$tab = 'vod';
	if(!empty($p['tab'])){
		$tab=$p['tab'];
	}
	
	if($tab=='art'){
		if($MAC['view']['artdetail'] !=2){
			echo '内容页面浏览模式非静态，不需要生成静态文件';
			exit;
		}
		$tpl->P["siteaid"] = 26;
		$pre='a';
	}
	else{
		if($MAC['view']['voddetail'] !=2 && $MAC['view']['vodplay'] <2 && $MAC['view']['voddown'] <2){
			echo '内容、播放、下载页面浏览模式均非静态，不需要生成静态文件';
			exit;
		}
		$tpl->P["siteaid"] = 16;
		$pre='d';
	}
	
	$no=$p['no'];
	$ac2=$p['ac2'];
	$num=0;
	$page=1;
	
	if($ac2=='nomake'){
		$where .= ' and '.$pre.'_maketime <'.$pre.'_time';
		$page=1;
	}
	if($ac2=='day'){
		if($id==''){ $where.= ' and '.$pre.'_type=-99'; }
		else{
			$today_start = mktime(0,0,0,date('m'),date('d'),date('Y'));
			$today_end = mktime(0,0,0,date('m'),date('d')+1,date('Y')); 
			$where .=' and  ('.$pre.'_time >='.$today_start.' and '.$pre.'_time < '.$today_end.') '; 
		}
	}
	if(!empty($min)){
		$where .= ' and '.$pre.'_time >'.(time()- $min*60);
	}
	if(!empty($no)){
		$where .= ' and '.$pre.'_id in ('.$no.') ';
		$datacount='';
	}
	$pagesize = 100;
	$pagego=1;
	$html='';
	$upids='';
	$rc=false;
	if(empty($datacount)){
		$sql = 'select count(*) from {pre}'.$tab.' where '.$pre.'_hide=0 ' .$where;
		$datacount = $db->getOne($sql);
    	$pagecount = ceil($datacount/$pagesize);
    }
    
    if($datacount==0){
    	$pagego=1;
    }
    elseif($page>$pagecount){
    	$pagego=2;
    }
    else{
    	$tpl->P['make']=true;
    	$sql = 'select * from {pre}'.$tab.' where '.$pre.'_hide=0 ' .$where;
    	$sql .= ' limit '.($pagesize*($page-1)).','.$pagesize;
    	
    	$rs = $db->query($sql);
    	$n=1;
    	echo '正在开始生成内容页面';
    	while($row = $db ->fetch_array($rs)){
    		if($rc){ $upids.=','; }
    		
    		$tpl->T = $MAC_CACHE[$tab.'type'][$row[$pre.'_type']];
    		$tpl->D = $row;
    		if($tab=='art'){
    			$upids.= $row['a_id'];
    			if(empty($html)){
    				$tpl->loadart();
    				$html = $tpl->H;
    				$html = str_replace('{maccms:runtime}','',$html);
    			}
    			
		        $pagearr = explode('[art:page]',$row['a_content']);
		        $pagearrlen = count($pagearr);
		        $pageurl = $tpl->getLink('art','detail',$tpl->T,$row,'{pg}');
		        $pageurl = str_replace('pg-{pg}','{pg}',$pageurl);
		        
    			for($i=1;$i<=$pagearrlen;$i++){
    				$tpl->H = $html;
    				$tpl->P['pg'] = $i;
    				$tpl->P['content'] = $pagearr[$i-1];
					$tpl->P['pagesize'] = 1;
					$tpl->P['datacount'] = $pagearrlen;
					$tpl->P['pagecount'] = $pagearrlen;
					$tpl->pageshow('',$pageurl);
					$tpl->replaceArt();
			        if($i==1){
			    		$lnk = str_replace('-{pg}','',$pageurl);
			    	}
			    	else{
			    		$lnk = str_replace('{pg}',$i,$pageurl);
			    	}
			    	$tpl->ifex();
			    	if($MAC['site']['installdir']!="/"){ $lnk=str_replace($MAC['site']['installdir'],"../",$lnk); } else { $lnk="..".$lnk; }
			    	if(substr($lnk,strlen($lnk)-1,1) =="/"){ $lnk .= "index.".$MAC['app']['suffix']; }
			    	$path = dirname($lnk);
					mkdirs($path);
			        fwrite(fopen($lnk,"wb"),$tpl->H);
			        if($i==1){ echo '<br><a target="_blank" href="'.$lnk.'">'.$row['a_id'].'. '.$row['a_name']."</a>"; }
			        ob_flush();flush();
    			}
    			unset($pagearr);
    			
    		}
    		else{
    			$upids.= $row['d_id'];
    			$tpl->P['make']='';
    			$tpl->P['pg']=1;
    			
    			echo '<br>'.$row[$pre.'_id'].'. '.$row[$pre.'_name']."&nbsp;&nbsp;";
    			
    			if($MAC['view']['voddetail'] ==2){
		    		if(empty($html)){
		    			$tpl->loadvod('detail');
		    			$html = $tpl->H;
		    			$tpl->H = str_replace('{maccms:runtime}','',$tpl->H);
		    		}
		    		$tpl->H = $html;
		    		$tpl->playdownlist ("play");
					$tpl->playdownlist ("down");
		    		$tpl->replaceVod();
		    		$tpl->ifex();
		    		$lnk = $tpl->getLink('vod','detail',$tpl->T,$row);
					if($MAC['site']['installdir']!="/"){ $lnk=str_replace($MAC['site']['installdir'],"../",$lnk); } else { $lnk="..".$lnk; }
					if(substr($lnk,strlen($lnk)-1,1) =="/"){ $lnk .= "index.".$MAC['app']['suffix']; }
					$path = dirname($lnk);
					mkdirs($path);
					fwrite(fopen($lnk,"wb"),$tpl->H);
					echo '<a target="_blank" href="'.$lnk.'">detail</a>&nbsp;&nbsp;';
				}
				
				for($m=0;$m<2;$m++){
					$flag = $m==0 ? 'play' : 'down';
					$v=$MAC['view'][$tab.$flag];
					$html='';
					
					if($v>1 && $v<5){
						if(empty($html)){
		    				$tpl->loadvod($flag);
		    				$tpl->replaceVod();
		    				if($flag=='play'){
		    					$tpl->playdownlist("down");
		    				}
		    				else{
		    					$tpl->playdownlist("play");
		    				}
		    				
		    				if($v!=3){
		    					$tpl->H = str_replace(array('[vod:'.$flag.'num]','[vod:'.$flag.'src]','[vod:'.$flag.'name]','[vod:'.$flag.'urlpath]'),array('','','',''),$tpl->H);
		    				}
		    				
		    				if( ($v==3 || $v==4) && strpos($tpl->H,"from=current")){
		    					$rcfrom=true;
		    				}
		    				else{
		    					$tpl->playdownlist($flag);
		    				}
		    				
		    				$html = $tpl->H;
		    				$html = str_replace('[vod:'.$flag.'er]', '<script src="'.$MAC['site']['installdir'].'js/playerconfig.js"></script><script src="'.$MAC['site']['installdir'].'js/player.js"></script>'. "\n" ,$html);
		    				$html = str_replace('{maccms:runtime}','',$html);
		    			}
		    			$tpl->ifex();
		    			
		    			$playstr = $tpl->getUrlInfo($flag);
		    			$playfile = "upload/".$flag."data/" . getDatet("Ymd",$row["d_addtime"])."/".$row['d_id']."/".$row['d_id'].".js";
				        $path = dirname( "../". $playfile);
						mkdirs($path);
						fwrite(fopen( "../".  $playfile,"wb"),$playstr);
				        $html = str_replace("[vod:".$flag."erinfo]", "<script src=\"". MAC_PATH . $playfile ."\"></script>",$html);
	        			
		    			if($v==2){
							$tpl->H = $html;
							$tpl->H = str_replace(array('[vod:'.$flag.'num]','[vod:'.$flag.'src]','[vod:'.$flag.'name]','[vod:'.$flag.'urlpath]'),array('','','',''),$tpl->H);
							$tpl->ifex();
							$lnkflag = $tpl->getLink('vod',$flag,$tpl->T,$row);
							$lnk2 = substring($lnkflag,strpos($lnkflag,"?"));
							if($MAC['site']['installdir']!="/"){ $lnk2=str_replace($MAC['site']['installdir'],"../",$lnk2); } else { $lnk2="..".$lnk2; }
							if(substr($lnk2,strlen($lnk2)-1,1) =="/"){ $lnk2 .= "index.".$MAC['app']['suffix']; }
							$path = dirname($lnk2);
							mkdirs($path);
							fwrite(fopen($lnk2,"wb"),$tpl->H);
						}
						elseif($v==3){
							$fromarr = explode('$$$',$row['d_'.$flag.'from']);
							$urlarr = explode('$$$',$row['d_'.$flag.'url']);
							$serverarr = explode('$$$',$row['d_'.$flag.'server']);
							$notearr = explode('$$$',$row['d_'.$flag.'note']);
							
							if($rcfrom){
								$tpl->playdownlist($flag);
							}
							for ($i=0;$i<count($fromarr);$i++){
								$from = $fromarr[$i];
								$show = $GLOBALS['MAC_CACHE']['vod'.$flag][$fromarr[$j]]['show'];
								$url = $urlarr[$i];
								$server = $serverar[$i];
								$note = $notearr[$i];
								$uarr = explode("#",$url);
								for ($j=0;$j<count($uarr);$j++){
									$tpl->H = $html;
									if(!empty($uarr[$j])){
										$urlone = explode("$",$uarr[$j]);
										$urlname = "";
										$urlpath = "";
										if (count($urlone)==2){
											$urlname = $urlone[0];
											$urlpath = $urlone[1];
										}
										else{
											$urlname = "第" . $j + 1 . "集";
											$urlpath = $urlone[0];
										}
										
										
										$tpl->H = str_replace(array('[vod:'.$flag.'from]','[vod:'.$flag.'show]','[vod:'.$flag.'server]','[vod:'.$flag.'note]','[vod:'.$flag.'num]','[vod:'.$flag.'src]','[vod:'.$flag.'name]','[vod:'.$flag.'urlpath]'),array($from,$show,$server,$note,$j+1,$i+1,$urlname,$urlpath),$tpl->H);
										$tpl->ifex();
										$lnkflag = $tpl->getLink('vod',$flag,$tpl->T,$row);
										$lnk2 = str_replace(array('{src}','{num}'),array($i+1,$j+1),$lnkflag);
										
										if($MAC['site']['installdir']!="/"){ $lnk2=str_replace($MAC['site']['installdir'],"../",$lnk2); } else { $lnk2="..".$lnk2; }
										if(substr($lnk2,strlen($lnk2)-1,1) =="/"){ $lnk2 .= "index.".$MAC['app']['suffix']; }
										$path = dirname($lnk2);
										mkdirs($path);
										fwrite(fopen($lnk2,"wb"),$tpl->H);
									}
								}
								unset($uarr);
							}
							unset($fromarr);
							unset($urlarr);
							unset($serverarr);
							unset($notearr);
						}
						elseif($v==4){
							$fromarr = explode('$$$',$row['d_'.$flag.'from']);
							$serverarr = explode('$$$',$row['d_'.$flag.'server']);
							$notearr = explode('$$$',$row['d_'.$flag.'note']);
							for ($i=0;$i<count($fromarr);$i++){
								$tpl->P["src"]= $i+1;
								$from = $fromarr[$i];
								$show = $GLOBALS['MAC_CACHE']['vod'.$flag][$fromarr[$j]]['show'];
								$server = $serverar[$i];
								$note = $notearr[$i];
								
								$tpl->H = $html;
								if($rcfrom){
									$tpl->playdownlist($flag);
								}
								$tpl->H = str_replace(array('[vod:'.$flag.'from]','[vod:'.$flag.'show]','[vod:'.$flag.'serve	r]','[vod:'.$flag.'note]'),array($from,$show,$server,$note),$tpl->H);
								$tpl->ifex();
								$lnkflag = $tpl->getLink('vod',$flag,$tpl->T,$row);
								$lnk2 = substring($lnkflag,strpos($lnkflag,"?"));
								$lnk2 = str_replace('{src}',$i+1,$lnk2);
								if($MAC['site']['installdir']!="/"){ $lnk2=str_replace($MAC['site']['installdir'],"../",$lnk2); } else { $lnk2="..".$lnk2; }
								if(substr($lnk2,strlen($lnk2)-1,1) =="/"){ $lnk2 .= "index.".$MAC['app']['suffix']; }
								$path = dirname($lnk2);
								mkdirs($path);
								fwrite(fopen($lnk2,"wb"),$tpl->H);
							}
							unset($fromarr);
							unset($serverarr);
							unset($notearr);
						}
						$lnkflag = str_replace(array('{src}','{num}'),array(1,1),$lnkflag);
						echo '<a target="_blank" href="'.$lnkflag.'">'.$flag.'</a>&nbsp;&nbsp;';
					}
				}
				ob_flush();flush();
    		}
    		$rc=true;
    		$n++;
    	}
    	unset($rs);
    	$pagego= empty($no) ? 3:4;
    }
    if(!empty($upids)){
    	$sql = 'update {pre}'.$tab.' set '.$pre.'_maketime='.time().' where '.$pre.'_id in ('.$upids.')';
    	$db->query($sql);
    }
    
}

else
{
	showErr('System','未找到指定系统模块');
}
?>