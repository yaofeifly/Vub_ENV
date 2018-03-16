<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
$col_type=array('t_id','t_name','t_enname','t_pid','t_hide','t_sort','t_tpl','t_tpl_list','t_tpl_art','t_tpl_play','t_tpl_down','t_key','t_des','t_title');
$col_topic=array('t_id','t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
$col_art=array('a_id', 'a_name', 'a_subname', 'a_enname', 'a_letter', 'a_color', 'a_from', 'a_author', 'a_tag', 'a_pic', 'a_type', 'a_level', 'a_hide', 'a_lock', 'a_up', 'a_down', 'a_hits', 'a_dayhits', 'a_weekhits', 'a_monthhits', 'a_addtime', 'a_time', 'a_hitstime', 'a_maketime', 'a_remarks', 'a_content');

if($method=='type'){
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$sql = 'SELECT count(*) FROM {pre}art_type where t_pid=0';
	$nums = $db->getOne($sql);
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_type;
	array_push($colarr,'t_span','t_count');
	
	$rn='type';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	$sql = 'SELECT * FROM {pre}art_type WHERE t_pid=0 ORDER BY t_sort,t_id ASC';
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		$t_count=0;
		$t_span='';
		$typearr = $MAC_CACHE['arttype'][$row['t_id']];
		if(is_array($typearr)){
			$ids = $typearr['childids'];
			$t_count = $db->getOne('SELECT count(*) FROM {pre}art WHERE a_type in('.$ids.')');
		}
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['t_span']=$t_span;
		$valarr['t_count']=$t_count;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
		$plt->set_if('rows_'.$rn,'isparent',true);
		
		
		$sql = 'SELECT * FROM {pre}art_type WHERE t_pid = \''.$row['t_id'].'\' ORDER BY t_sort,t_id ASC';
		$rs1 = $db->query($sql);
		while ($row1 = $db ->fetch_array($rs1)){
			$t_count=0;
			$t_span='&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;';
			$valarr=array();
			$t_count = $db->getOne('SELECT count(*) FROM {pre}art WHERE a_type='.$row1['t_id']);
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row1[$n];
			}
			$valarr['t_span']=$t_span;
			$valarr['t_count']=$t_count;
			
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$n];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
			if($row1['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
			$plt->set_if('rows_'.$rn,'isparent',false);
		}
		unset($rs1);
	}
	unset($colarr);
	unset($valarr);
	unset($rs);
}

elseif($method=='typesaveall')
{
	$t_id = be('arr','t_id');
	$ids = explode(',',$t_id);
	foreach($ids as $id){
		$t_name = be('post','t_name' .$id);
		$t_enname = be('post','t_enname' .$id) ;
		$t_sort = be('post','t_sort' .$id);
		$t_tpl = be('post','t_tpl' .$id);
		$t_tpl_art = be('post','t_tpl_art' .$id);
		
		if (isN($t_name)) { $t_name='未知';}
		if (isN($t_enname)) { $t_enname='weizhi';}
		if (!isNum($t_sort)) { $t_sort=0;}
		if (isN($t_tpl)) { $t_tpl = 'artlist.html';}
		if (isN($t_tpl_art)) { $t_tpl_art = 'art.html';}
		
		$db->Update ('{pre}art_type',array('t_name','t_enname', 't_sort','t_tpl','t_tpl_art'),array($t_name,$t_enname,$t_sort,$t_tpl,$t_tpl_art),'t_id='.$id);
	}
	updateCacheFile();
	redirect( getReferer() );
}

elseif($method=='typeinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$t_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($t_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_type;
	array_push($colarr,'flag','backurl');
	$valarr['t_tpl']='art_type.html';
	$valarr['t_tpl_list']='art_list.html';
	$valarr['t_tpl_art']='art_detail.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}art_type where t_id='.$t_id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['t_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	$rn='ptype';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	foreach($MAC_CACHE['arttype'] as $a){
		if($a['t_pid']==0){
			$plt->set_var('n',$a['t_name']);
			$plt->set_var('v',$a['t_id']);
			$c= $a['t_id']==$valarr['t_pid'] ? 'selected' :'';
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	unset($colarr);
	unset($valarr);
}

elseif($method=='topic')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$sql = 'SELECT count(*) FROM {pre}art_topic where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}art_topic where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY t_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_topic;
	array_push($colarr,'t_count');
	
	$rn='topic';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		$t_count = $db->getOne('SELECT count(*) FROM {pre}art_relation WHERE r_type=2 and r_a='.$row['t_id']);
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['t_addtime'] = getColorDay($row['t_addtime']);
		$valarr['t_time'] = getColorDay($row['t_time']);
		$valarr['t_count'] = $t_count;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
	}
	unset($rs);
	$pageurl = '?m=art-topic-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='topicinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$t_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($t_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_topic;
	array_push($colarr,'flag','backurl');
	$valarr['t_tpl']='art_topiclist.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}art_topic where t_id='.$t_id);
		if($row){
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['t_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	
	$arr=array(
		array('a'=>'level','c'=>$valarr['t_level'],'t'=>1,'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'hide','c'=>$valarr['t_hide'],'t'=>1,'n'=>array('显示','隐藏'),'v'=>array(0,1))
	);
	foreach($arr as $a){
		$colarr=$a['n'];
		$valarr=$a['v'];
		$rn=$a['a'];
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
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='topicdata')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$tid = intval($p['tid']);
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$wd=$p['wd'];
	
	if(!empty($wd) && $wd!='可搜索(文章名称)'){
		$where .= ' and instr(a_name,\''.$wd.'\')>0  ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(文章名称)');
	}
	$plt->set_var('tid',$tid);
	
	$pagesize=16;
	$sql = 'SELECT count(*) FROM {pre}art where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$pagesize);
	$sql = "SELECT a_id,a_name,a_enname,a_type,a_author FROM {pre}art where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY a_time DESC limit ".($pagesize * ($page-1)) .",".$pagesize;
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=array('a_id','a_name','a_author','a_link');
	$rn='topicdata';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$typearr = $GLOBALS['MAC_CACHE']['arttype'][$valarr['d_type']];
		
		$d_link = "../".$tpl->getLink('art','detail',$typearr,$row,true);
		$d_link = str_replace("../".$MAC['site']['installdir'],"../",$d_link);
		if (substring($d_link,1,strlen($d_link)-1)=="/") { $d_link .= "index.". $MAC['app']['suffix'];}
		$valarr['d_link'] = $d_link;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		unset($typearr);
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	$pageurl = '?m=art-topicdata-pg-{pg}-tid-'.$tid.'-wd-'.urlencode($wd);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='list')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$type=$p['type']; if(isN($type)){ $type=999; } else { $type=intval($type); }
	$topic=$p['topic']; if(isN($topic)){ $topic=999; } else { $topic=intval($topic); }
	$level=$p['level']; if(isN($level)){ $level=999; } else { $level=intval($level); }
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$lock=$p['lock']; if(isN($lock)){ $lock=999; } else { $lock=intval($lock); }
	
	$repeat=$p['repeat'];
	$repeatlen=$p['repeatlen'];
	$by=$p['by']; if(isN($by)) { $by='a_time'; }
	$wd=$p['wd'];
	
	if($type!=999){
		$where .=' and a_type='.$type.' ';
	}
	if($hide!=999){
		$where .=' and a_hide='.$hide.' ';
	}
	if($hide!=999){
		$where .=' and a_lock='.$lock.' ';
	}
	if($level!=999){
		$where .=' and a_level='.$level.' ';
	}
	if($topic!=999){
		$where .=' and a_id in(select r_b from {pre}art_relation where r_type=2 and r_a='.$topic.') ';
	}
	
    $repeat_status=0;
	if($repeat == 'ok'){
        $repeat_field = ' a_name as `a_name1` ';
        $tmptab=',tmptable as `tmp` ';
        if($repeatlen>0){
        	$repeat_status=1;
			$repeat_field = ' left(a_name,'.$repeatlen.') as `a_name1` ';
		}
		else{
			$where .= ' AND a_name=`tmp`.a_name1 ';
		}
		if($page==1){
			//temporary
			$db->query('DROP TABLE IF EXISTS tmptable;');
			$tmpsql='create table IF NOT EXISTS `tmptable` as (SELECT ' . $repeat_field . ' FROM {pre}art GROUP BY a_name1 HAVING COUNT(a_name1)>1); ';
			$db->query($tmpsql);
		}
    }
    $plt->set_var('repeatlen',$repeatlen);
    
    
	if(!empty($wd) && $wd!='可搜索(文章名称)'){
		$where .= ' and instr(a_name,\''.$wd.'\')>0 ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(文章名称)');
	}
	
	$topicarr = $MAC_CACHE['arttopic'];
	$topicarrn = array();
	$topicarrv = array();
	foreach($topicarr as $arr){
		array_push($topicarrn,$arr['t_name']);
		array_push($topicarrv,$arr['t_id']);
	}
	$typearr = $MAC_CACHE['arttype'];
	$typearrn = array();
	$typearrv = array();
		foreach($typearr as $arr1){
		$s='&nbsp;|—';
		if($arr1['t_pid']==0){
			array_push($typearrn,$s.$arr1['t_name']);
			array_push($typearrv,$arr1['t_id']);
			foreach($typearr as $arr2){
				if($arr1['t_id']==$arr2['t_pid']){
					$s='&nbsp;|&nbsp;&nbsp;&nbsp;|—';
					array_push($typearrn,$s.$arr2['t_name']);
					array_push($typearrv,$arr2['t_id']);
				}
			}
		}
	}
	
	
	$arr=array(
		array('a'=>'hide','c'=>$hide,'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'lock','c'=>$lock,'n'=>array('未锁定','已锁定'),'v'=>array(0,1)),
		array('a'=>'level','c'=>$level,'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'by','c'=>$by,'n'=>array('编号','总人气','日人气','周人气','月人气'),'v'=>array('a_id','a_hits','a_dayhits','a_weekhits','a_monthhits')),
		array('a'=>'topic','c'=>$topic,'n'=>$topicarrn,'v'=>$topicarrv),
		array('a'=>'type','c'=>$type,'n'=>$typearrn,'v'=>$typearrv)
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
	
	if($repeat == 'ok'){
		$plt->set_if('main','isrepeat',true);
	}
	else{
		$plt->set_if('main','isrepeat',false);
	}
	
	if($repeat_status==1){
		$sql='select count(*) from {pre}art a INNER JOIN (SELECT a_id,left(a_name,'.$repeatlen.') as a_name1 from {pre}art where CHAR_LENGTH(a_name)>='.$repeatlen.' ) b on a.a_id = b.a_id  INNER JOIN (select a_name1 from tmptable) c on b.a_name1 = c.a_name1 ';
		$nums = $db->getOne($sql);
		
		$sql='select a.`a_id`, `a_name`, `a_enname`,  `a_color`, `a_pic`, `a_type`, `a_level`, `a_hide`, `a_lock`,`a_hits`, `a_addtime`, `a_time`,`a_maketime`, b.a_name1 from {pre}vod a INNER JOIN (SELECT a_id,left(a_name,'.$repeatlen.') as a_name1 from {pre}art where CHAR_LENGTH(a_name)>='.$repeatlen.' ) b on a.a_id = b.a_id  INNER JOIN (select a_name1 from tmptable) c on b.a_name1 = c.a_name1 where 1=1 ORDER BY a_name asc  limit '.($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
		$rs = $db->query($sql);
	}
	else{
		$sql = 'SELECT count(*) FROM {pre}art where 1=1 '.$where;
		$nums = $db->getOne($sql);
		
		$sql = "SELECT `a_id`, `a_name`, `a_enname`,  `a_color`, `a_pic`, `a_type`, `a_level`, `a_hide`, `a_lock`,`a_hits`, `a_addtime`, `a_time`,`a_maketime`  FROM {pre}art where 1=1 ";
		$sql .= $where;
		$sql .= " ORDER BY ".$by." DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
		$rs = $db->query($sql);
	}
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_art;
	array_push($colarr,'a_link');
	
	$rn='art';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$typearr = $MAC_CACHE['arttype'][$row['a_type']];
		
		$valarr['a_time'] = $row['a_time']==0 ? '' : getColorDay($row['a_time']);
		$valarr['a_hide'] = $row['a_hide']==0 ? '' : '<font color=red>[隐]</font>';
		$valarr['a_lock'] = $row['a_lock']==0 ? '' : '<font color=red>[锁]</font>';
		$valarr['a_type'] = $typearr['t_name'];
		
		$alink = '../'. $tpl->getLink('art','detail',$typearr,$row,true);
		$alink = str_replace( '../'.$MAC['site']['installdir'],'../',$alink);
		if (substring($alink,1,strlen($alink)-1)=='/') { $alink .= 'index.'. $MAC['app']['suffix']; }
		$valarr['a_link'] = $alink;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if(($GLOBALS['MAC']['view']['artdetail']==2) &&($row['a_maketime']<$row['a_time'])) {
			$plt->set_if('rows_'.$rn,'ismake',true);
		}
		else{
			$plt->set_if('rows_'.$rn,'ismake',false);
		}
	}
	unset($rs);
	unset($colarr);
	unset($valarr);
	unset($topicarr);
	unset($typearr);
	
	$pageurl = '?m=art-list-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-{pg}-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen;
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='info')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$id=$p['id'];
	$flag=empty($id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_art;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}art where a_id='.$id);
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
	if($valarr['a_lock']==0){
		$plt->set_if('main','islock',false);
	}
	else{
		$plt->set_if('main','islock',true);
	}
	
	$num = 1;
	$rn='content';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	if(!empty($valarr['a_content'])){
		$arr = explode('[art:page]',$valarr['a_content']);
		foreach($arr as $a){
			$plt->set_var('n',$num);;
			$plt->set_var('v',$a);
			$num++;
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($arr);
	}
	else{
		$plt->set_var('rows_'.$rn,'');
	}
	$plt->set_var('contentcount',$num);
	
	
	$typearr = $MAC_CACHE['arttype'];
	$typearrn = array();
	$typearrv = array();
		foreach($typearr as $arr1){
		$s='&nbsp;|—';
		if($arr1['t_pid']==0){
			array_push($typearrn,$s.$arr1['t_name']);
			array_push($typearrv,$arr1['t_id']);
			foreach($typearr as $arr2){
				if($arr1['t_id']==$arr2['t_pid']){
					$s='&nbsp;|&nbsp;&nbsp;&nbsp;|—';
					array_push($typearrn,$s.$arr2['t_name']);
					array_push($typearrv,$arr2['t_id']);
				}
			}
		}
	}
	
	$arr=array(
		array('a'=>'hide','c'=>$valarr['a_hide'],'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'level','c'=>$valarr['a_level'],'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'type','c'=>$valarr['a_type'],'n'=>$typearrn,'v'=>$typearrv)
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
	unset($colarr);
	unset($valarr);
	unset($topicarr);
	unset($typearr);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>