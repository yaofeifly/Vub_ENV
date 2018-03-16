<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');

$col_gbook=array('g_id','g_vid','g_hide','g_sort','g_name','g_content','g_ip','g_reply','g_time','g_replytime');
$col_comment=array('c_id','c_vid','c_type','c_rid','c_hide','c_name','c_ip','c_content','c_time');
$col_link=array('l_id','l_name','l_url','l_logo','l_sort','l_type');
$backurl=getReferer();

if($method=='pic')
{
	$path = $p['path'];
	if(empty($path)){ $path = "../upload"; }
	if(substring($path,9) != "../upload") { $path = "../upload"; }
	$uppath = substring($path,strrpos($path,"/"));
	
	if (count( explode("../",$path) ) > 2) {
		showErr('System','非法目录请求');
		return;
	}
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	if ($path !="../upload"){
		$plt->set_var('uppath',$uppath);
		$plt->set_if('main','ischild',true);
	}
	else{
		$plt->set_if('main','ischild',false);
	}
	
	$filters = ",,cache,break,artcollect,downdata,playdata,export,vodcollect,";
	$rn1='picpath';
	$rn2='picfile';
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
					if (strpos($f,".html") <=0 && strpos($f,".htm") <=0){
						$num_file++;
						$fsize = filesize($f);
						$sumsize = $sumsize + $fsize;
						$fsize = round($fsize/1024,2);
						$filetime = getColorDay(filemtime($f));
						$f = convert_encoding($f,"UTF-8","GB2312");
						$f = str_replace($path."/","",$f);
						$colarr=array('name','path','size','time');
						$valarr=array($f,$path."/".$f,$fsize,$filetime);
						for($i=0;$i<count($colarr);$i++){
							$plt->set_var($colarr[$i],$valarr[$i]);
						}
						$plt->parse('rows_'.$rn2,'list_'.$rn2,true);
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

elseif($method=='picdel')
{
	$fnames = be("arr","fname");
    $arr = explode(",",$fnames);
    foreach($arr as $a){
		if ( (substring($a,9) != "../upload") || count( explode("../",$a) ) > 2) {
		}
		else{
			$a = convert_encoding($a,"UTF-8","GB2312");
			if(file_exists($a)){ unlink($a); }
		}
    }
    redirect($backurl);
}

elseif($method=='picchk')
{
	headAdmin2('视频无效图片检测');
	$_SESSION["picchkpath"] = "";
	$path = "../upload/vod";
	$arr = array();
	$num = 0;
	foreach( glob($path.'/*',GLOB_ONLYDIR) as $single){
		if(is_dir($single)){
			$num++;
			echo $single."<br>";
			$arr[] = $single;
		}
	}
	$_SESSION["picchkpath"] = $arr;
	showMsg('需检测图片目录收集完毕，共有'.$num.'个目录，稍后进入检测文件','?m=extend-picchkfile');
}

elseif($method=='picchkfile')
{
	headAdmin2('视频无效图片检测');
	$arr = $_SESSION["picchkpath"];
	$num = intval($p['num']);
	$e = intval($p['e']);
	$s = intval($p['s']);
	
	if( $num > count($arr)-1 ){
		showMsg('无效图片检测完毕，一共有'.$e.'个无效图片...','?m=extend-pic');
		return;
	}
	
	$d = $arr[$num];
	if (is_dir($d)){
		$farr = glob($d.'/*');
		$fcount=0;
		if($farr){ $fcount= count($farr); }
		echo "<font color=red>".$d." >> 共&nbsp;".$fcount." 个文件,开始位置".($s+1).",已累计清理".$e."个无效图片 </font> <br>";
		
		$i=0;
		$endnum = $s + 29;
		$rc=false;
		
		if($fcount>0){
			foreach( $farr as $single){ 
		      		if($i>=$s){
						if($s>$endnum){
							$rc=true;
							echo "目录图片过多...";
							jump('?m=extend-picchkfile-num-'.$num.'-s-'.$s.'-e-'.$e,3);
							break;
						}
			      		
			      		$fsingle = $single;
			      		$single = convert_encoding($single,"UTF-8","GB2312");
			      		$fname = str_replace("../upload/","upload/",$single);
			      		
						$sql="select count(*) from {pre}vod where d_pic='$fname'";
						$cc=$db->getOne($sql);
						if($cc==0){
							$e++;
							unlink($fsingle);
							echo "".$fname."<font color=red>无效</font><br/>";
						}
						else{
							echo "".$fname."<font color=green>有效</font><br/>";
						}
						$s++;
					}
					$i++;
					ob_flush();flush();
	   		}
		}
		unset($farr);
		if(!$rc){
			echo "该目录的无效图片检测完毕...";
			jump('?m=extend-picchkfile-num-'.($num+1).'-s-0-e-'.$e,3);
		}
	}
	footAdmin();
}

elseif($method=='picsyncvod')
{
	headAdmin2 ("视频远程图片同步");
	$page = intval($p['pg']);
	$pic_fw = $p['pic_fw']; if($pic_fw==''){ $pic_fw=be('post','pic_fw'); }
	$pic_fwdate = $p['pic_fwdate']; if($pic_fwdate==''){ $pic_fwdate=be('post','pic_fwdate'); }
	$pic_xx = $p['pic_xx']; if($pic_xx==''){ $pic_xx=be('post','pic_xx'); }
	
	
	$flag = "#err". date('Y-m-d',time());
	$sql = "SELECT count(*) FROM {pre}vod WHERE 1=1 ";
	if ($pic_fw=="2" && $pic_fwdate!=""){
		$pic_fwdate = str_replace('|','-',$pic_fwdate);
		$todayunix1 = strtotime($pic_fwdate);
		$todayunix2 = $todayunix1 +  86400;
		$where = ' AND (d_time>= '. $todayunix1 . ' AND d_time<='. $todayunix2 .') ';
	}
	if ($pic_xx=="1"){
		$where = $where . " AND instr(d_pic,'#err')=0 ";
	}
	else if ($pic_xx=="2"){
		$where = $where . " AND instr(d_pic,'".$flag."')=0 ";
	}
	else if ($pic_xx=="3"){
		$where = $where . " AND instr(d_pic,'#err')>0 ";
	}
	$where .= " AND instr(d_pic,'http://')>0  ";
	$nums = $db->getOne($sql.$where);
	
	if($nums>0){
		$pagecount = ceil($nums/20);
		echo "<font color=red>当前共".$nums."条数据需要同步,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		ob_flush();flush();
		
		$sql = "SELECT d_id,d_pic,d_picthumb FROM {pre}vod WHERE 1=1 ". $where;
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		$num=0;
		
		$ftypes=array('jpg','gif','bmp','png');
		while ($row = $db ->fetch_array($rs))
		{
				$colarr=array();
				$valarr=array();
				$d_id = $row['d_id'];
				$d_pic = $row["d_pic"];
				if (strpos($d_pic,"#err")){
					$picarr = explode("#err",$d_pic);
					$d_pic =$picarr[0];
				}
				
		    	$fname = time() .''. $num;
		    	if(!in_array(substr($d_pic,-3,3),$ftypes)){
		    		$fname.='.jpg';
		    	}
		    	else{
		    		$fname.='.'.substr($d_pic,-3,3);
		    	}
		    	$path = "upload/vod/" . getSavePicPath('') . "/";
		    	$thumbpath = "upload/vodthumb/" . getSavePicPath('vodthumb') . "/";
		    	$d_picthumb = '';
		    	$ps = savepic($d_pic,$path,$thumbpath,$fname,'vod',$msg);
		    	if($ps){
		    		$d_pic=$path.$fname;
		    		if($GLOBALS['MAC']['upload']['thumb']==1){ $d_picthumb= $thumbpath.$fname; }
		    		$colarr=array("d_pic","d_picthumb");
		    		$valarr=array($d_pic,$d_picthumb);
		    	}
		    	else{
		    		$d_pic .= $flag;
		    		$colarr=array("d_pic");
		    		$valarr=array($d_pic);
		    	}
				$num++;
				$db->Update("{pre}vod",$colarr,$valarr," d_id=".$d_id);
		    	echo $msg."<br>";
		    	ob_flush();flush();
		}
		unset($rs);
		jump("?m=extend-picsyncvod-pg-".($page+1)."-pic_fw-".$pic_fw."-pic_fwdate-".str_replace('-','|',$pic_fwdate)."-pic_xx-".$pic_xx,3);
	}
	else{
		showMsg('所有外部图片已经成功同步到本地','?m=vod-list');
		return;
	}
	footAdmin();
}

elseif($method=='picsyncart')
{
	headAdmin2 ("文章远程图片同步");
	$page = intval($p['pg']);
	$flag = "#err". date('Y-m-d',time());
	$sql = "SELECT count(*) FROM {pre}art WHERE a_content LIKE '%src=\"http://%' ";
	
	$nums = $db->getOne($sql.$where);
	if($nums>0){
		$pagecount = ceil($nums/20);
		echo "<font color=red>共".$nums."条数据需要同步,每次同步20个数据,正在开始同步第".$pagecount."页数据的的图片</font><br>";
		ob_flush();flush();
		
		$sql = "SELECT a_id,a_content FROM {pre}art WHERE a_content LIKE '%src=\"http://%' " .$where;
		$sql .= " limit ". ($pagecount-1) .",20";
		$rs = $db->query($sql);
		$num=0;
		while ($row = $db ->fetch_array($rs))
		{
				$colarr=array();
				$valarr=array();
				$a_id = $row['a_id'];
				$a_content = $row["a_content"];
				$status = false;
				$rule = buildregx("<img[^>]*src\s*=\s*['".chr(34)."]?([\w/\-\:.]*)['".chr(34)."]?[^>]*>","is");
				preg_match_all($rule,$a_content,$matches);
				
				$matchfieldarr=$matches[1];
				$matchfieldstrarr=$matches[0];
				$matchfieldvalue="";
				foreach($matchfieldarr as $f=>$matchfieldstr)
				{
					$matchfieldvalue=$matchfieldstrarr[$f];
					$a_pic = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
					
					$st = strrpos($a_pic,'/');
			    	$fname = substring($a_pic,strlen($a_pic)-$st,$st+1);
			    	$path = "upload/art/" . getSavePicPath('') . "/";
			    	$thumbpath = "";
			    	$ps = savepic($a_pic,$path,$thumbpath,$fname,'art',$msg);
					if($ps){
						$a_content = str_replace($a_pic, $GLOBALS['MAC']['app']['installdir'].$path.$fname,$a_content );
					}
					else{
						$a_content = str_replace($a_pic,"",$a_content);
					}
					echo $msg."<br>";
					ob_flush();flush();
				}
				$num++;
				$db->query("UPDATE {pre}art set a_content='".$a_content."' where a_id='".$a_id."'");
		}
		unset($rs);
		jump("?m=extend-picsyncart-pg-".($page+1),3);
	}
	else{
		showMsg('所有外部图片已经成功同步到本地','?m=art-list');
		return;
	}
	footAdmin();
}

elseif($method=='link')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$sql = 'SELECT count(*) FROM {pre}link';
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = 'SELECT * FROM {pre}link ORDER BY l_sort,l_id ASC limit '.($MAC['app']['pagesize'] * ($page-1)) .','.$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	$colarr=$col_link;
	array_push($colarr,'l_type_c0','l_type_c1');
	
	$rn='link';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$l_type_c0 = $row['l_type'] ==0 ? 'selected' : '';
		$l_type_c1 = $row['l_type'] ==1 ? 'selected' : '';
		$valarr['l_type_c0'] = $l_type_c0;
		$valarr['l_type_c1'] = $l_type_c1;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	
	$pageurl = '?m=extend-link-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,8,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='linkinfo')
{
	$ac2=$p['ac2'];
	$l_id=$p['id'];
	$flag=empty($l_id) ? 'add' : 'edit';
	
	if(!empty($ac2)){
		$l_id = be('arr','l_id');
		$ids = explode(',',$l_id);
		foreach($ids as $id){
			$l_name = be('post','l_name' .$id);
			$l_sort = be('post','l_sort'.$id);
			$l_url = be('post','l_url' .$id);
			$l_type = be('post','l_type'. $id);
			$l_logo = be('post','l_logo'. $id);
			
			if (isN($l_name)){ $l_name='未知';}
			if (isN($l_url)){ $l_url='';}
			if (isN($l_logo)) { $l_logo=''; }
			if (!isNum($l_type)){ $l_type=0; }
			if (!isNum($l_sort)){ $l_sort=0; }
			$db->Update ('{pre}link',array('l_name','l_url', 'l_sort','l_type','l_logo'),array($l_name,$l_url,$l_sort,$l_type,$l_logo),'l_id='.$id);
		}
		redirect($backurl);
		return;
	}
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$colarr=$col_link;
	array_push($colarr,'flag','backurl','l_type_c0','l_type_c1');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}link where l_id='.$l_id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
			$l_type_c0 = $row['l_type'] ==0 ? 'selected' : '';
			$l_type_c1 = $row['l_type'] ==1 ? 'selected' : '';
		}
		unset($row);
	}
	$valarr['l_type_c0'] = $l_type_c0;
	$valarr['l_type_c1'] = $l_type_c1;
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n,$v);
	}
}

elseif($method=='gbook')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$vid=$p['vid']; if(isN($vid)){ $vid=999; } else { $vid=intval($vid); }
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$reply=$p['reply']; if(isN($reply)){ $reply=999; } else { $reply=intval($reply); }
	$wd=$p['wd'];
	
	if($vid!=999){
		$where = $vid==0 ? ' and g_vid=0 ' : ' and g_vid>0 ';
	}
	if($hide!=999){
		$where .=' and g_hide='.$hide.' ';
	}
	if($reply!=999){
		$where .=  $reply==0 ? ' and g_reply is null ' : ' and g_reply is not null ';
	}
	if(!empty($wd) && $wd!='可搜索(留言内容,用户呢称)'){
		$where .= ' and (instr(g_name,\''.$wd.'\')>0  or instr(g_content,\''.$wd.'\')>0) ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(留言内容,用户呢称)');
	}
	
	$arr=array(
		array('a'=>'hide','c'=>$hide,'n'=>array('留言显隐','显示','隐藏'),'v'=>array(999,0,1)),
		array('a'=>'reply','c'=>$reply,'n'=>array('留言回复','未回复','已回复'),'v'=>array(999,0,1)),
		array('a'=>'vid','c'=>$vid,'n'=>array('留言类型','留言数据','报错数据'),'v'=>array(999,0,1))
	);
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? 'selected': '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	
	$sql = 'SELECT count(*) FROM {pre}gbook where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}gbook where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY g_sort desc,g_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_gbook;
	array_push($colarr,'g_flag');
	
	$rn='gbook';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['g_content'] = regReplace($row['g_content'], "\[em:(\d{1,})?\]", "<img src=\"../images/face/$1.gif\" border=0/>");
		$valarr['g_ip'] = long2ip($row['g_ip']);
		$valarr['g_reply'] = !empty($row['g_reply']) ? '<font color=green>已回复</font>' : '<font color=red>未回复</font>';
		$valarr['g_time'] = getColorDay($row['g_time']);
		$valarr['g_replytime'] = getColorDay($row['g_replytime']);
		$valarr['g_flag'] = $row['g_vid']==0 ? '留言数据' : '报错数据';
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['g_sort']==1) { $plt->set_if('rows_'.$rn,'isorder',true); } else { $plt->set_if('rows_'.$rn,'isorder',false); }
		if($row['g_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
		if($row['g_vid']>0) { $plt->set_if('rows_'.$rn,'isflag',true); } else { $plt->set_if('rows_'.$rn,'isflag',false); }
	}
	unset($colarr);
	unset($valarr);
	
	$pageurl = '?m=extend-gbook-pg-{pg}-hide-'.$hide.'-reply-'.$reply.'-vid-'.$vid.'-wd-'.urlencode($wd);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,4,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='gbookorder')
{
	$g_id=intval($p['id']);
	$val=intval($p['val']);
	$db->Update('{pre}gbook',array('g_sort'),array($val),'g_id='.$g_id);
	redirect( getReferer() );
}

elseif($method=='gbookinfo')
{
	$g_id=$p['id'];
	$flag=empty($g_id) ? 'add' : 'edit';
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$colarr=$col_gbook;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}gbook where g_id='.$g_id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
}

elseif($method=='comment')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$wd=$p['wd'];
	
	if($hide!=999){
		$where .=' and c_hide='.$hide.' ';
	}
	if(!empty($wd) && $wd!='可搜索(评论内容,用户呢称)'){
		$where .= ' and (instr(c_name,\''.$wd.'\')>0  or instr(c_content,\''.$wd.'\')>0) ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(评论内容,用户呢称)');
	}
	
	$arr=array(
		array('a'=>'hide','c'=>$hide,'n'=>array('评论显隐','显示','隐藏'),'v'=>array(999,0,1))
	);
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$c = $a['c']==$v ? 'selected': '';
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	
	$sql = 'SELECT count(*) FROM {pre}comment where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}comment where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY c_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_comment;
	$rn='comment';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['c_ip']=long2ip($row['c_ip']);
		$valarr['c_content']=regReplace($row['c_content'], "\[em:(\d{1,})?\]", "<img src=\"../images/face/$1.gif\" border=0/>");
		$valarr['c_time']=getColorDay($row['c_time']);
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['c_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
	}
	
	$pageurl = '?m=extend-comment-pg-{pg}-hide-'.$hide.'-wd-'.urlencode($wd);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,8,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='commentinfo')
{
	$c_id=$p['id'];
	$flag=empty($c_id) ? 'add' : 'edit';
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$colarr=$col_comment;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}comment where c_id='.$c_id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
}

elseif($method=='datarep')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$colarr=array('v','n');
	$valarr = array();
	$rn='table';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	$rs = $db->query('SHOW TABLES FROM '.$MAC['db']['name']);
	while($row = $db ->fetch_array($rs)){
		$v = $row['Tables_in_'.$MAC['db']['name']];
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			if($n=='n'){
				$v .= ' ('.getTabName($v) .')';
			}
			//$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($rs);
	
}

elseif($method=='datarepexe')
{
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	if($page==1){
		$table = be('post','table');
		$field = be('post','field');
		$findstr = be('post','findstr');
		$tostr = be('post','tostr');
		$where = be('post','where');
	}
	$sql = "UPDATE ".$table." set ".$field."=Replace(".$field.",'".$findstr."','".$tostr."') where 1=1 ". $where;
	$db->query($sql);
	showMsg('批量替换完成!SQL执行语句!<br>'.$sql,"");
}

else
{
	showErr('System','未找到指定系统模块');
}
?>