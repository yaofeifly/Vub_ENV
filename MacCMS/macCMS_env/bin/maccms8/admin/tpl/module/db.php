<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
$backurl=getReferer();

if($method=='sql')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
}

elseif($method=='sqlexe')
{
	$rn='sql';
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$sql = be("post","sql");
	if (!isN($sql)){
		$sql= stripslashes($sql);
		if (strtolower(substr($sql,0,6))=="select"){
			$isselect=true;
		}
		else{
			$isselect=false;
		}
		$rs = $db->query($sql);
		$num= $db->affected_rows();
	}
	
	if ($isselect){
		if($num==0){
			$plt->set_if('main','isnull',true);
			return;
		}
		$plt->set_if('main','isnull',false);
		$plt->set_if('main','isselect',true);
		$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
		$i=0;
		if($rs){
		    while($row=$db->fetch_array($rs)){
				if($i==0){
					$strcol = '';
					foreach($row as $k=>$v){
						$strcol .= '<th><strong>'.$k.'</strong></th>';
					}
					$plt->set_var('rows_'.$rn, '<tr>'.$strcol.'</tr>');
				}
				$s='';
				$one='';
				foreach( $row as $k=>$v){
					$one .= '<td>'.strip_tags($v).'</td>';
				}
				$i++;
		  	  	$plt->set_var('data', '<tr>'.$one.'</tr>');
		  	  	$plt->parse('rows_'.$rn,'list_'.$rn,true);
			}
		}
	}
	else{
		$plt->set_if('main','isnull',false);
		$plt->set_if('main','isselect',false);
		$plt->set_var('count',$num);
	}
}

elseif($method=='datarep')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$colarr=array('v','n');
	$valarr = array();
	$rn='table';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$sql='SHOW TABLES FROM `'.$MAC['db']['name'].'`';
	$rs = $db->query($sql);
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

elseif($method=='list')
{
	$rn='db';
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	$colarr=array('name','time','size','num');
	$num=0;
	$arr = glob('bak'.'/*.sql');
	
	if(!is_array($arr) || count($arr)==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	
	$plt->set_if('main','isnull',false);
	foreach($arr as $a){
		$num++;
		$tmp = explode("-",$a);
		if(intval($tmp[1])==1){
			$name = str_replace('bak/','',$tmp[0]);
			$time = date( 'Y-m-d H:i:s',filemtime($a) );
			$size = round(filesize($a)/1024);
			$valarr=array($name,$time,$size,$num);
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$i];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
}

elseif($method=='getsize')
{
	$file = $p['file'];
	$fsize=0;
	foreach( glob('bak'.'/*.sql') as $f){
		if(strpos($f,$file)>0){
			$fsize = $fsize + round(filesize($f)/1024);
    	}
	}
	echo $fsize;
}

elseif($method=='compress')
{
	$status = $db->query('OPTIMIZE  TABLE `{pre}art` , `{pre}art_relation` , `{pre}art_topic` , `{pre}art_type` , `{pre}comment` , `{pre}gbook` , `{pre}link` , `{pre}manager` , `{pre}user` , `{pre}user_card` , `{pre}user_group` , `{pre}user_pay` , `{pre}user_visit` , `{pre}vod` , `{pre}vod_relation` , `{pre}vod_topic` , `{pre}vod_type`  ');
	if($status){
		showMsg('压缩优化成功','?m=db-list');
	}
	else{
		showMsg('压缩优化失败','?m=db-list');
	}
}

elseif($method=='repair')
{
	$status = $db->query('REPAIR TABLE `{pre}art` , `{pre}art_relation` , `{pre}art_topic` , `{pre}art_type` , `{pre}comment` , `{pre}gbook` , `{pre}link` , `{pre}manager` , `{pre}user` , `{pre}user_card` , `{pre}user_group` , `{pre}user_pay` , `{pre}user_visit` , `{pre}vod` , `{pre}vod_relation` , `{pre}vod_topic` , `{pre}vod_type`  ');
	if($status){
		showMsg('修复成功','?m=db-list');
	}
	else{
		showMsg('修复失败','?m=db-list');
	}
}

elseif($method=='del')
{
	$file = $p['file'];
	if(isN($file)){
		$file = be('arr','file');
	}
	$arr = explode(',',$file);
	foreach ($arr as $a){
		foreach( glob('bak'.'/*.sql') as $f){
			if(strpos($f,$a)>0){
				unlink($f);
			}
		}
	}
	unset($arr);
	redirect('?m=db-list');
}

elseif($method=='reduction')
{
	$file = $p['file'];
	$num = $p['num'];
	$fcount = $p['fcount'];
	
	if(!isNum($num)){ $num=1;} else{ $num=intval($num); }
	if(!isNum($fcount)){ $fcount=-1;} else { $fcount = intval($fcount); }
	if($fcount==-1){
		$fcount=0;
	    foreach( glob('bak/*') as $f){
	    	$f = str_replace('bak/','',$f);
			if(strpos(",".$f,$file)>0){
				$fcount++;
	    	}
		}
	}
	
	if($num>$fcount){
		showMsg ( '数据库还原完毕，请重新登录后更新系统缓存', '?m=db-list' );
	}
    else{
    	
    	for($j=1;$j<=$fcount;$j++){
    		if($j==$num){
				$fpath = 'bak/'.$file . '-'.$j.'.sql';
		    	$sqls = file($fpath);
		    	
				foreach($sqls as $sql)
				{
					$sql = str_replace(chr(10),'',$sql);
					$sql = str_replace(chr(13),'',$sql);
					if (!isN($sql)){
						$db->query(trim($sql));
					}
					unset($sql);
				}
				unset($sqls);
			}
	    }
	    ob_flush();flush();
	    showMsg ( '共有'.$fcount.'个备份分卷文件需要还原，正在还原第'.$num.'个文件...', '?m=db-reduction-num-'.($num+1).'-fcount-'.$fcount.'-file-'.$file );
    }
}

elseif($method=='bak')
{
	$fpath = 'bak/' . date('Ymd',time()) . '_'. getRndStr(10) ;
	$sql='';
	$p=1;
	$tables = ' `{pre}art_relation` , `{pre}art_topic` , `{pre}art_type` , `{pre}comment` , `{pre}gbook` , `{pre}link` , `{pre}user` , `{pre}user_card` , `{pre}user_group` , `{pre}user_pay` , `{pre}user_visit`  , `{pre}vod_relation` , `{pre}vod_topic` , `{pre}vod_type` , `{pre}art`, `{pre}vod`';
	$tables = str_replace('{pre}',$GLOBALS['MAC']['db']['tablepre'],$tables);
	$tablearr = explode(',',$tables);
	$pagesize = 800;
	
	foreach( $tablearr as $table ){
		$table = trim($table);
		$sql.= make_header($table);
		
		$i=0;
		$fs=array();
		$res = $db->query("SHOW COLUMNS FROM ".$table);
 		while ($row = mysql_fetch_array($res)) $fs[]=$row[0]; 
		unset($res);
		
		$fsd=count($fs)-1;
		$nums = $db->getOne('select count(*) from '.$table);
		$pagecount = 1;
		if($nums>$pagesize){
			$pagecount = ceil($nums/$pagesize);
		}
		
		for($n=1;$n<=$pagecount;$n++){
			$rsdata = $db->getAll('select * from '.$table.' limit '.($pagesize * ($n-1)).','.$pagesize);
			$rscount = count($rsdata);
			$intable = 'INSERT INTO '.$table.' VALUES(';
			for($j=0;$j<$rscount;$j++){
				$line = $intable;
				for($k=0;$k<=$fsd;$k++){
					if($k < $fsd){
						$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."',";
					}
					else{
						$line.="'".mysql_escape_string($rsdata[$j][$fs[$k]])."');\r\n";
					}
				}
				$sql.=$line;
				if(strlen($sql)>= 1500000){
					$fname = $fpath . '-'.$p.'.sql' ;
					fwrite(fopen($fname,'wb'),$sql);
					$p++;
					unset($sql);
				}
			}
			unset($rsdata);
		}
		unset($fs);
	}
	unset($tablearr);
	
	$sql .= make_manager( str_replace('{pre}',$GLOBALS['MAC']['db']['tablepre'],'{pre}manager') );
	$fname = $fpath . '-'.$p.'.sql' ;
	fwrite(fopen($fname,'wb'),$sql)	;
	showMsg('备份成功','?m=db-list');
}

else
{
	showErr('System','未找到指定系统模块');
}
?>