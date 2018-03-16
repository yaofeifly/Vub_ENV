<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
$col_type=array('t_id','t_name','t_enname','t_pid','t_hide','t_sort','t_tpl','t_tpl_list','t_tpl_vod','t_tpl_play','t_tpl_down','t_key','t_des','t_title');
$col_topic=array('t_id','t_name','t_enname','t_sort','t_tpl','t_pic','t_content','t_key','t_des','t_title','t_hide','t_level','t_up','t_down','t_score','t_scoreall','t_scorenum','t_hits','t_dayhits','t_weekhits','t_monthhits','t_addtime','t_time');
$col_vod=array('d_id', 'd_name', 'd_subname', 'd_enname', 'd_letter', 'd_color', 'd_pic', 'd_picthumb', 'd_picslide', 'd_starring', 'd_directed', 'd_tag', 'd_remarks', 'd_area', 'd_lang', 'd_year', 'd_type', 'd_type_expand', 'd_class', 'd_hide', 'd_lock', 'd_state', 'd_level', 'd_usergroup', 'd_stint', 'd_stintdown', 'd_hits', 'd_dayhits', 'd_weekhits', 'd_monthhits', 'd_duration', 'd_up', 'd_down', 'd_score','d_scoreall', 'd_scorenum', 'd_addtime', 'd_time', 'd_hitstime', 'd_maketime', 'd_content', 'd_playfrom', 'd_playserver', 'd_playnote', 'd_playurl', 'd_downfrom', 'd_downserver', 'd_downnote', 'd_downurl');

$col_class=array('c_id','c_name','c_pid','c_hide','c_sort');

if($method=='type'){
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$sql = 'SELECT count(*) FROM {pre}vod_type where t_pid=0';
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
	
	$sql = 'SELECT * FROM {pre}vod_type WHERE t_pid=0 ORDER BY t_sort,t_id ASC';
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		$t_count=0;
		$t_span='';
		$typearr = $MAC_CACHE['vodtype'][$row['t_id']];
		if(is_array($typearr)){
			$ids = $typearr['childids'];
			$t_count = $db->getOne('SELECT count(*) FROM {pre}vod WHERE d_type in('.$ids.')');
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
		
		
		$sql = 'SELECT * FROM {pre}vod_type WHERE t_pid = \''.$row['t_id'].'\' ORDER BY t_sort,t_id ASC';
		$rs1 = $db->query($sql);
		while ($row1 = $db ->fetch_array($rs1)){
			$t_count=0;
			$t_span='&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;';
			$valarr=array();
			$t_count = $db->getOne('SELECT count(*) FROM {pre}vod WHERE d_type='.$row1['t_id']);
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
		$t_tpl_vod = be('post','t_tpl_vod' .$id);
		$t_tpl_play = be('post','t_tpl_play' .$id);
		$t_tpl_down = be('post','t_tpl_down' .$id);
		
		if (isN($t_name)) { $t_name='未知';}
		if (isN($t_enname)) { $t_enname='weizhi';}
		if (!isNum($t_sort)) { $t_sort=0;}
		if (isN($t_tpl)) { $t_tpl = 'vodlist.html';}
		if (isN($t_tpl_vod)) { $t_tpl_vod = 'vod.html';}
		if (isN($t_tpl_play)) { $t_tpl_play = 'vodplay.html';}
		if (isN($t_tpl_down)) { $t_tpl_down = 'voddown.html';}
		
		$db->Update ('{pre}vod_type',array('t_name','t_enname', 't_sort','t_tpl','t_tpl_vod','t_tpl_play','t_tpl_down'),array($t_name,$t_enname,$t_sort,$t_tpl,$t_tpl_vod,$t_tpl_play,$t_tpl_down),'t_id='.$id);
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
	
	$valarr['t_tpl']='vod_type.html';
	$valarr['t_tpl_list']='vod_list.html';
	$valarr['t_tpl_vod']='vod_detail.html';
	$valarr['t_tpl_play']='vod_play.html';
	$valarr['t_tpl_down']='vod_down.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_type where t_id='.$t_id);
		if($row){
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
	foreach($MAC_CACHE['vodtype'] as $a){
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

elseif($method=='classsaveall')
{
	$c_id = be('arr','c_id');
	$ids = explode(',',$c_id);
	
	foreach($ids as $id){
		$c_name = be('post','c_name' .$id);
		$c_sort = be('post','c_sort' .$id);
		
		if (isN($c_name)) { $c_name='未知'; }
		if (!isNum($c_sort)) { $c_sort=0; }
		
		$db->Update ('{pre}vod_class',array('c_name','c_sort'),array($c_name,$c_sort),'c_id='.$id);
	}
	updateCacheFile();
	redirect( getReferer() );
}


elseif($method=='class')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$sql = 'SELECT count(*) FROM {pre}vod_type where t_pid=0';
	$nums = $db->getOne($sql);
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='type';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	$rn1='class';
	$plt->set_block('list_'.$rn, 'list_'.$rn1, 'rows_'.$rn1);
		
	$sql = 'SELECT * FROM {pre}vod_type WHERE t_pid=0 ORDER BY t_sort,t_id ASC';
	$rs = $db->query($sql);
	while ($row = $db ->fetch_array($rs)){
		
		$plt->set_var('rows_'.$rn1);
		
		$colarr=$col_class;
		array_push($colarr,'c_span');
		$c_span='&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;';
		$sql = 'SELECT * FROM {pre}vod_class WHERE c_pid = \''.$row['t_id'].'\' ORDER BY c_sort,c_id ASC';
		$rs1 = $db->query($sql);
		while ($row1 = $db ->fetch_array($rs1)){;
			$valarr=array();
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row1[$n];
			}
			$valarr['c_span']=$c_span;
			
			for($i=0;$i<count($colarr);$i++){
				$n = $colarr[$i];
				$v = $valarr[$n];
				$plt->set_var($n, $v );
			}
			$plt->parse('rows_'.$rn1,'list_'.$rn1,true);
			if($row1['c_hide']==1) { $plt->set_if('rows_'.$rn1,'ishide',true); } else { $plt->set_if('rows_'.$rn1,'ishide',false); }
		}
		unset($rs1);
		
		
		$colarr=$col_type;
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['c_span']='';
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if($row['t_hide']==1) { $plt->set_if('rows_'.$rn,'ishide',true); } else { $plt->set_if('rows_'.$rn,'ishide',false); }
		$plt->set_if('rows_'.$rn,'isparent',true);
	}
	unset($colarr);
	unset($valarr);
	unset($rs);
}

elseif($method=='classinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$c_id=$p['id'];
	$pid=$p['pid'];
	$flag=empty($c_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_class;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_class where c_id='.$c_id);
		if($row){
			for($i=0;$i<count($colarr);$i++){
				$n=$colarr[$i];
				$valarr[$n]=$row[$n];
			}
		}
		unset($row);
	}
	else{
		$valarr['c_pid']=intval($pid);
	}
	$valarr['flag']=$flag;
	$valarr['backurl']=$backurl;
	
	
	$rn='ptype';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	foreach($MAC_CACHE['vodtype'] as $a){
		if($a['t_pid']==0){
			$plt->set_var('n',$a['t_name']);
			$plt->set_var('v',$a['t_id']);
			$c= $a['t_id']==$valarr['c_pid'] ? 'selected' :'';
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
	$sql = 'SELECT count(*) FROM {pre}vod_topic where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}vod_topic where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY t_time DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
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
		$t_count = $db->getOne('SELECT count(*) FROM {pre}vod_relation WHERE r_type=2 and r_a='.$row['t_id']);
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
	$pageurl = '?m=vod-topic-pg-{pg}';
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
	
	$valarr['t_tpl']='vod_topiclist.html';
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod_topic where t_id='.$t_id);
		if($row){
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
	
	if(!empty($wd) && $wd!='可搜索(视频名称,视频主演)'){
		$where .= ' and (instr(d_name,\''.$wd.'\')>0  or instr(d_starring,\''.$wd.'\')>0) ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(视频名称,视频主演)');
	}
	$plt->set_var('tid',$tid);
	
	$pagesize=16;
	$sql = 'SELECT count(*) FROM {pre}vod where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$pagesize);
	$sql = "SELECT d_id,d_name,d_enname,d_type,d_starring FROM {pre}vod where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY d_time DESC limit ".($pagesize * ($page-1)) .",".$pagesize;
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=array('d_id','d_name','d_starring','d_link');
	$rn='topicdata';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$typearr = $GLOBALS['MAC_CACHE']['vodtype'][$valarr['d_type']];
		$d_link = "../".$tpl->getLink('vod','detail',$typearr,$row);
		
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
	$pageurl = '?m=vod-topicdata-pg-{pg}-tid-'.$tid.'-wd-'.urlencode($wd);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,6,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='server')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$xp = '../inc/config/vodserver.xml';
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xp);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName('server');
	
	if($nodes->length==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='server';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$colarr=array('status','sort','from','show','des','tip');
	$num=0;
	foreach($nodes as $node){
		$num++;
		
		$status = $node->attributes->item(0)->nodeValue;
		$sort = $node->attributes->item(1)->nodeValue;
		$from = $node->attributes->item(2)->nodeValue;
		$show = $node->attributes->item(3)->nodeValue;
		$des = $node->attributes->item(4)->nodeValue;
		$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
		$status = $status=='1' ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		$valarr=array($status,$sort,$from,$show,$des,$tip);
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

elseif($method=='serverinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$xp = '../inc/config/vodserver.xml';
	
	$flag=empty($p['from']) ? 'add' : 'edit';
	$backurl=getReferer();
	
	if($flag=='edit'){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("server");
		foreach($nodes as $node){
			$from = $node->attributes->item(2)->nodeValue;
			if ($p['from'] == $from){
				$status = $node->attributes->item(0)->nodeValue;
				$sort = $node->attributes->item(1)->nodeValue;
				$show = $node->attributes->item(3)->nodeValue;
				$des = $node->attributes->item(4)->nodeValue;
				$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
				break;
			}
		}
		unset($xmlnode);
    	unset($nodes);
    	unset($doc);
	}
	
	$colarr=array('flag','backurl','status','sort','from','show','des','tip');
	$valarr=array($flag,$backurl,$status,$sort,$from,$show,$des,$tip);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	$colarr=array('禁用','启用');
	$valarr=array('0','1');
	$rn='status';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		
		$c = $v==$status ? 'selected': '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='player')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	$xp = '../inc/config/vodplay.xml';
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xp);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName('play');
	
	if($nodes->length==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$rn='player';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	$colarr=array('status','sort','from','show','des','tip');
	$num=0;
	foreach($nodes as $node){
		$num++;
		
		$status = $node->attributes->item(0)->nodeValue;
		$sort = $node->attributes->item(1)->nodeValue;
		$from = $node->attributes->item(2)->nodeValue;
		$show = $node->attributes->item(3)->nodeValue;
		$des = $node->attributes->item(4)->nodeValue;
		$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
		$status = $status=='1' ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		$valarr=array($status,$sort,$from,$show,$des,$tip);
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

elseif($method=='playerup')
{
	$s = file_get_contents($_FILES['file1']['tmp_name']);
	$labelRule = buildregx('<([\s\S]+?)>([\s\S]+?)</\1>',"");
	preg_match_all($labelRule,$s,$iar);
	$arlen=count($iar[1]);
	for($m=0;$m<$arlen;$m++){
		$play[$iar[1][$m]] = $iar[2][$m];
	}
	unset($iar);
	
	
	if($play['from']!=''){
		$xp = '../inc/config/vodplay.xml';
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName('play');
		$flag='add';
		foreach($nodes as $node){
			if ($play['from'] == $node->attributes->item(2)->nodeValue){
				$flag='edit';
				break;
			}
		}
		unset($xmlnode,$nodes,$doc);
		fwrite(fopen('../player/'.$play['from'].'.js','wb'),$play['code']);
		
		$play['flag']=$flag;
		$play['tab']='vodplay';
		$play['code']='';
		$play['backurl'] = 'index.php?m=vod-player';
		$sHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head>	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">	<title导入播放器代码</title></head>';
		$sHtml .= "<form id='formsubmit' name='formsubmit' action='admin_xml.php?ac=savexml' method='post'>";
		foreach($play as $k=>$v){
            $sHtml.= "<input type='hidden' name='".$k."' value='".$v."'/>";
        }
        $sHtml = $sHtml."<input type='submit' value='提交'></form>";
		$sHtml = $sHtml."<script>document.forms['formsubmit'].submit();</script>";
		echo $sHtml;
	}
	else{
		jump('?m=vod-player');
	}
}

elseif($method=='playerinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$xp = '../inc/config/vodplay.xml';
	
	$flag=empty($p['from']) ? 'add' : 'edit';
	$backurl=getReferer();
	
	if($flag=='edit'){
		$doc = new DOMDocument();
		$doc -> formatOutput = true;
		$doc -> load($xp);
		$xmlnode = $doc -> documentElement;
		$nodes = $xmlnode->getElementsByTagName("play");
		foreach($nodes as $node){
			$from = $node->attributes->item(2)->nodeValue;
			if ($p['from'] == $from){
				$status = $node->attributes->item(0)->nodeValue;
				$sort = $node->attributes->item(1)->nodeValue;
				$show = $node->attributes->item(3)->nodeValue;
				$des = $node->attributes->item(4)->nodeValue;
				$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
				break;
			}
		}
		unset($xmlnode);
    	unset($nodes);
    	unset($doc);
	}
	
	$colarr=array('flag','backurl','status','sort','from','show','des','tip');
	$valarr=array($flag,$backurl,$status,$sort,$from,$show,$des,$tip);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n, $v );
	}
	
	$colarr=array('禁用','启用');
	$valarr=array('0','1');
	$rn='status';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		
		$c = $v==$status ? 'selected': '';
		$plt->set_var('v', $v );
		$plt->set_var('n', $n );
		$plt->set_var('c', $c );
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='batch')
{
	headAdmin2('视频批量操作');
	
	$ckdel = $p['ckdel'];
	$ckrq = $p['ckrq'];
	$cktj = $p['cktj'];
	$cklock = $p['cklock'];
	$ckhide = $p['ckhide'];
	$batch_hits1 = $p['batch_hits1'];
	$batch_hits2 = $p['batch_hits2'];
	$batch_level = $p['batch_level'];
	$batch_lock = $p['batch_lock'];
	$batch_hide = $p['batch_hide'];
	
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$type=$p['type']; if(isN($type)){ $type=999; } else { $type=intval($type); }
	$topic=$p['topic']; if(isN($topic)){ $topic=999; } else { $topic=intval($topic); }
	$level=$p['level']; if(isN($level)){ $level=999; } else { $level=intval($level); }
	$hide=$p['hide']; if(isN($hide)){ $hide=999; } else { $hide=intval($hide); }
	$lock=$p['lock']; if(isN($lock)){ $lock=999; } else { $lock=intval($lock); }
	$state=$p['state']; if(isN($state)){ $state=999; } else { $state=intval($state); }
	$pic=$p['pic']; if(isN($pic)){ $pic=999; } else { $pic=intval($pic); }
	
	$id=$p['id'];
	$repeat=$p['repeat'];
	$repeatlen=$p['repeatlen'];
	$by=$p['by']; if(isN($by)) { $by='d_time'; }
	$wd=$p['wd'];
	$play=$p['play'];
	$down=$p['down'];
	$area=$p['area'];
	$lang=$p['lang'];
	$server=$p['server'];
	
	if($id!=''){
		$where .= ' and d_id='.$id;
	}
	if($type!=999){
		$where .=' and d_type='.$type.' ';
	}
	if($hide!=999){
		$where .=' and d_hide='.$hide.' ';
	}
	if($lock!=999){
		$where .=' and d_lock='.$lock.' ';
	}
	if($level!=999){
		$where .=' and d_level='.$level.' ';
	}
	if($topic!=999){
		$where .=' and d_id in(select r_b from {pre}vod_relation where r_type=2 and r_a='.$topic.') ';
	}
	if($state!=999){
		if($state==0){ $where.=' and d_state=0 '; } else{ $where.=' and d_state>0 '; }
	}
	if(!empty($play)){
		$where .= ' AND instr(d_playfrom,\''.$play.'\')>0 ';
	}
	if(!empty($down)){
		$where .= ' AND instr(d_downfrom,\''.$down.'\')>0 ';
	}
	if(!empty($server)){
		$where .= ' AND instr(d_playserver,\''.$server.'\')>0 ';
	}
	
	if($pic!=999){
		if($pic==0){
	    	$where .= ' AND d_pic = \'\' ';
	    }
	    elseif($pic==1){
	    	$where .= ' AND instr(d_pic,\'ttp://\')>0 ';
	    }
	    elseif($pic==2){
	    	$where .= ' AND instr(d_pic,\'#err\')>0  ';
	    }
	}
    
	if(!empty($wd) && $wd!='可搜索(视频名称、视频主演)'){
		$where .= ' and ( instr(d_name,\''.$wd.'\')>0 or instr(d_starring,\''.$wd.'\')>0 ) ';
	}
	
	$pagesize=100;
	
	if($ckdel=='1'){
		$sql = $where=="" ? "truncate table {pre}vod " : "delete from {pre}vod where 1=1 ".$where;
	    $status = $db->query($sql);
	    showMsg('批量删除数据完成!',"index.php?m=vod-list");
	}
	elseif($ckdel=='2'){
		$sql = "SELECT count(*) FROM {pre}vod where 1=1 ".$where;
		$nums = $db->getOne($sql);
		$pagecount=ceil($nums/$pagesize);
		
	    $sql = "SELECT d_id,d_name,d_playfrom,d_playurl,d_playserver,d_playnote FROM {pre}vod where 1=1 ".$where . " ORDER BY d_id desc limit ".($pagesize * ($pagecount-1)) .",".$pagesize;
	    
		$rs = $db->query($sql);
		if($nums==0){
			showMsg ("数据处理完毕!", "index.php?m=vod-list");
		}
		else{
			echo "<font color=red>共".$nums."条数据包含".$play."播放器,共".$pagecount."页正在开始删除第".$pagecount."页数据</font><br>";
			$n=0;
			while ($row = $db ->fetch_array($rs))
			{
				$n++;
				$d_playfrom = $row["d_playfrom"];
				$d_playserver = $row["d_playserver"];
				$d_playurl = $row["d_playurl"];
				$d_playnote = $row['d_playnote'];
				
				$playfromarr = explode("$$$",$d_playfrom);
				$playserverarr = explode("$$$",$d_playserver);
				$playurlarr = explode("$$$",$d_playurl);
				$playnotearr = explode("$$$",$d_playnote);
				
				$new_playfrom = "";
				$new_playserver = "";
				$new_playurl = "";
				$new_playnote = "";
				
				$rc=false;
				for ($i=0;$i<count($playfromarr);$i++){
					if($playfromarr[$i]==$play){
						
					}
					else{
						if($rc){
							$new_playfrom .= "$$$";
							$new_playserver .= "$$$";
							$new_playurl .= "$$$";
							$new_playnote .= "$$$";
						}
						$new_playfrom .= $playfromarr[$i];
						$new_playserver .= $playserverarr[$i];
						$new_playnote .= $playnotearr[$i];
						$new_playurl .= str_replace("'","''",$playurlarr[$i]);
						$rc=true;
					}
				}
				
				$sql = "UPDATE {pre}vod set d_playfrom='".$new_playfrom."',d_playserver='".$new_playserver."',d_playnote='".$new_playnote."',d_playurl='".$new_playurl."' where d_id='".$row["d_id"]."'";
				$db->query($sql);
				echo $n.'.'.$row["d_name"] . "---ok<br>";
			}
			
			$url = '?m=vod-batch-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-'.($page+1).'-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang).'-ckdel-'.$ckdel;
			
			jump($url,3);
		}
		unset($rs);
	}
	elseif($ckrq=='1'|| $cktj=='1' || $cklock=='1' || $ckhide=='1'){
		$sql = "SELECT count(*) FROM {pre}vod where 1=1 ".$where;
		$nums = $db->getOne($sql);
		$pagecount=ceil($nums/$pagesize);
			
	    $sql = "SELECT d_id,d_name FROM {pre}vod where 1=1 ".$where . " ORDER BY d_id desc limit ".($pagesize * ($page-1)) .",".$pagesize;
	    
	    
		$rs = $db->query($sql);
		if($page>$pagecount){
			showMsg ("数据处理完毕!", "index.php?m=vod-list");
		}
		else{
			echo "<font color=red>共".$nums."条数据需要处理，共".$pagecount."页正在处理第".$page."页数据</font><br>";
			$n=0;
			while ($row = $db ->fetch_array($rs))
			{
				$n++;
				$des='';
				$sql1='';
				if($ckrq=='1'){
					$d_hits = rndNum($batch_hits1,$batch_hits2);
					$sql1 .= ' d_hits='.$d_hits;
					$des .= '&nbsp;随机人气：'.$d_hits.'；';
				}
				if($cktj=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_level='.$batch_level;
					$des .= '&nbsp;推荐值：'.$batch_level.'；';
				}
				if($cklock=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_lock='.$batch_lock;
					$des .= $batch_lock==1 ? '&nbsp;[锁定]' : '&nbsp;[解锁]';
				}
				if($ckhide=='1'){
					$sql1 .= $sql=='' ? '' : ',';
					$sql1 .= ' d_hide='.$batch_hide;
					$des .= $batch_hide==1 ? '&nbsp;[隐藏]' : '&nbsp;[显示]';
				}
				$sql = "UPDATE {pre}vod set ".$sql1." where d_id=".$row["d_id"];
				$db->query($sql);
				echo $n.'.'.$row["d_name"] . "-" . $des ."---ok<br>";
			}
			
			$url = '?m=vod-batch-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-'.($page+1).'-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang).'-ckrq-'.$ckrq.'-batch_hits1-'.$batch_hits1.'-batch_hits2-'.$batch_hits2.'-cktj-'.$cktj.'-batch_level-'.$batch_level.'-cklock-'.$cklock.'-batch_lock-'.$batch_lock.'-ckhide-'.$ckhide.'-batch_ckhide-'.$batch_ckhide;
			
			jump($url,3);
		}
		unset($rs);
	}
	else{
		showMsg ("参数不正确!", "index.php?m=vod-list");
	}
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
	$state=$p['state']; if(isN($state)){ $state=999; } else { $state=intval($state); }
	$pic=$p['pic']; if(isN($pic)){ $pic=999; } else { $pic=intval($pic); }
	
	$id=$p['id'];
	$repeat=$p['repeat'];
	$repeatlen=$p['repeatlen'];
	$by=$p['by']; if(isN($by)) { $by='d_time'; }
	$wd=$p['wd'];
	$play=$p['play'];
	$down=$p['down'];
	$area=$p['area'];
	$lang=$p['lang'];
	$server=$p['server'];
	
	if($id!=''){
		$where .= ' and d_id='.$id;
	}
	if($type!=999){
		$where .=' and d_type='.$type.' ';
	}
	if($hide!=999){
		$where .=' and d_hide='.$hide.' ';
	}
	if($lock!=999){
		$where .=' and d_lock='.$lock.' ';
	}
	if($level!=999){
		$where .=' and d_level='.$level.' ';
	}
	if($topic!=999){
		$where .=' and d_id in(select r_b from {pre}vod_relation where r_type=2 and r_a='.$topic.') ';
	}
	if($state!=999){
		if($state==0){ $where.=' and d_state=0 '; } else{ $where.=' and d_state>0 '; }
	}
	if(!empty($play)){
		$where .= ' AND instr(d_playfrom,\''.$play.'\')>0 ';
	}
	if(!empty($down)){
		$where .= ' AND instr(d_downfrom,\''.$down.'\')>0 ';
	}
	if(!empty($server)){
		$where .= ' AND instr(d_playserver,\''.$server.'\')>0 ';
	}
	
	if($pic!=999){
		if($pic==0){
	    	$where .= ' AND d_pic = \'\' ';
	    }
	    elseif($pic==1){
	    	$where .= ' AND instr(d_pic,\'ttp://\')>0 ';
	    }
	    elseif($pic==2){
	    	$where .= ' AND instr(d_pic,\'#err\')>0  ';
	    }
	}
	$repeat_status=0;
	if($repeat == 'ok'){
        $repeat_field = ' d_name as `d_name1` ';
        $tmptab=',tmptable as `tmp` ';
        if($repeatlen>0){
        	$repeat_status=1;
			$repeat_field = ' left(d_name,'.$repeatlen.') as `d_name1` ';
		}
		else{
			$where .= ' AND d_name=`tmp`.d_name1 ';
		}
		if($page==1){
			//temporary
			$db->query('DROP TABLE IF EXISTS tmptable;');
			$tmpsql='create table IF NOT EXISTS `tmptable` as (SELECT ' . $repeat_field . ' FROM {pre}vod GROUP BY d_name1 HAVING COUNT(d_name1)>1); ';
			
			$db->query($tmpsql);
		}
    }
    $plt->set_var('repeatlen',$repeatlen);
    
	if(!empty($wd) && $wd!='可搜索(视频名称、视频主演)'){
		$where .= ' and ( instr(d_name,\''.$wd.'\')>0 or instr(d_starring,\''.$wd.'\')>0 ) ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(视频名称、视频主演)');
	}
	
	$topicarr = $MAC_CACHE['vodtopic'];
	$topicarrn = array();
	$topicarrv = array();
	foreach($topicarr as $arr){
		array_push($topicarrn,$arr['t_name']);
		array_push($topicarrv,$arr['t_id']);
	}
	$typearr = $MAC_CACHE['vodtype'];
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
	
	$areaarr = explode(',',$MAC['app']['area']);
	$langarr = explode(',',$MAC['app']['lang']);
	
	$playarr = $GLOBALS['MAC_CACHE']['vodplay'];
	$playarrn = array();
	$playarrv = array();
	foreach($playarr as $k=>$v){
		array_push($playarrn,$v['show']);
		array_push($playarrv,$k);
	}
	$donwarr = $GLOBALS['MAC_CACHE']['voddown'];
	$donwarrn = array();
	$donwarrv = array();
	foreach($donwarr as $k=>$v){
		array_push($donwarrn,$v['show']);
		array_push($donwarrv,$k);
	}
	$serverarr = $GLOBALS['MAC_CACHE']['vodserver'];
	$serverarrn = array();
	$serverarrv = array();
	foreach($serverarr as $k=>$v){
		array_push($serverarrn,$v['show']);
		array_push($serverarrv,$k);
	}
	
	$arr=array(
		array('a'=>'hide','c'=>$hide,'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'lock','c'=>$lock,'n'=>array('未锁定','已锁定'),'v'=>array(0,1)),
		array('a'=>'state','c'=>$state,'n'=>array('完结了','连载中'),'v'=>array(0,1)),
		array('a'=>'pic','c'=>$pic,'n'=>array('无图片','远程图片','同步出错图'),'v'=>array(0,1,2)),
		array('a'=>'level','c'=>$level,'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'by','c'=>$by,'n'=>array('编号','总人气','日人气','周人气','月人气'),'v'=>array('d_id','d_hits','d_dayhits','d_weekhits','d_monthhits')),
		array('a'=>'topic','c'=>$topic,'n'=>$topicarrn,'v'=>$topicarrv),
		array('a'=>'type','c'=>$type,'n'=>$typearrn,'v'=>$typearrv),
		array('a'=>'area','c'=>$area,'n'=>$areaarr,'v'=>$areaarr),
		array('a'=>'lang','c'=>$lang,'n'=>$langarr,'v'=>$langarr),
		array('a'=>'play','c'=>'','n'=>$playarrn,'v'=>$playarrv),
		array('a'=>'down','c'=>'','n'=>$donwarrn,'v'=>$donwarrv),
		array('a'=>'server','c'=>'','n'=>$serverarrn,'v'=>$serverarrv)
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
		$sql='select count(*) from {pre}vod a INNER JOIN (SELECT d_id,left(d_name,'.$repeatlen.') as d_name1 from {pre}vod where CHAR_LENGTH(d_name)>='.$repeatlen.' ) b on a.d_id = b.d_id  INNER JOIN (select d_name1 from tmptable) c on b.d_name1 = c.d_name1 ';
		$nums = $db->getOne($sql);
		
		$sql='select a.`d_id`, `d_name`, `d_enname`, `d_color`, `d_pic`, `d_remarks`, `d_type`, `d_type_expand` ,`d_hide`, `d_lock`, `d_state`, `d_level`,  `d_hits`,  `d_addtime`, `d_time`, `d_maketime`, `d_playfrom`, `d_downfrom`,b.d_name1 from {pre}vod a INNER JOIN (SELECT d_id,left(d_name,'.$repeatlen.') as d_name1 from {pre}vod where CHAR_LENGTH(d_name)>='.$repeatlen.' ) b on a.d_id = b.d_id  INNER JOIN (select d_name1 from tmptable) c on b.d_name1 = c.d_name1 where 1=1 ORDER BY d_name asc  limit '.($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
		$rs = $db->query($sql);
	}
	else{
		$sql = 'SELECT count(*) FROM {pre}vod'.$tmptab.' where 1=1 '.$where;
		$nums = $db->getOne($sql);
		$sql = "SELECT `d_id`, `d_name`, `d_enname`, `d_color`, `d_pic`, `d_remarks`, `d_type`, `d_type_expand` ,`d_hide`, `d_lock`, `d_state`, `d_level`,  `d_hits`,  `d_addtime`, `d_time`,`d_maketime`, `d_playfrom`, `d_downfrom`  FROM {pre}vod".$tmptab." where 1=1 ";
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
	
	$colarr=$col_vod;
	array_push($colarr,'d_link');
	
	$rn='vod';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$typearr = $MAC_CACHE['vodtype'][$row['d_type']];
		
		$valarr['d_state'] = $row['d_state']==0 ? '' : '['.$row['d_state'].']';
		$valarr['d_time'] = $row['d_time']==0 ? '' : getColorDay($row['d_time']);
		$valarr['d_hide'] = $row['d_hide']==0 ? '' : '<font color=red>[隐]</font>';
		$valarr['d_lock'] = $row['d_lock']==0 ? '' : '<font color=red>[锁]</font>';
		$valarr['d_type'] = $typearr['t_name'];
		$valarr['d_playfrom'] = str_replace("$$$",",",$row["d_playfrom"]);
		$valarr['d_downfrom'] = str_replace("$$$",",",$row["d_downfrom"]);
		
	 	$d_link = "../".$tpl->getLink('vod','detail',$typearr,$row);
		$d_link = str_replace("../".$MAC['site']['installdir'],"../",$d_link);
		if (substring($d_link,1,strlen($d_link)-1)=="/") { $d_link .= "index.". $MAC['app']['suffix'];}
		$valarr['d_link'] = $d_link;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		if(($GLOBALS['MAC']['view']['voddetail']==2 || $GLOBALS['MAC']['view']['vodplay']==2 || $GLOBALS['MAC']['view']['voddown']==2) &&($row['d_maketime']<$row['d_time'])) {
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
	unset($playarr);
	unset($downarr);
	unset($serverarr);
	
	$pageurl = '?m=vod-list-type-'.$type.'-topic-'.$topic.'-level-'.$level.'-hide-'.$hide.'-lock-'.$lock.'-by-'.$by.'-pg-{pg}-wd-'.urlencode($wd).'-repeat-'.$repeat.'-repeatlen-'.$repeatlen.'-state-'.$state.'-pic-'.$pic.'-play-'.$play.'-down-'.$down.'-server-'.$server.'-area-'.urlencode($area).'-lang-'.urlencode($lang);
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,6,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='info')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$id=$p['id'];
	$flag=empty($id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_vod;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}vod where d_id='.$id);
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
	if($valarr['d_time']!=''){ $valarr['d_time']=date('Y-m-d H:i:s',$valarr['d_time']); }
	
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	if($valarr['d_lock']==0){
		$plt->set_if('main','islock',false);
	}
	else{
		$plt->set_if('main','islock',true);
	}
	
	if($MAC['app']['expandtype']==0){
		$plt->set_if('main','isexpandtype',false);
	}
	else{
		$plt->set_if('main','isexpandtype',true);
	}
	
	$select_play = makeSelectXml('vodplay','play',"");
	$select_down = makeSelectXml('voddown','down',"");
	$select_server = makeSelectXml('vodserver','server',"");
	
	$plt->set_var('select_play',str_replace("'","\'",$select_play));
	$plt->set_var('select_down',str_replace("'","\'",$select_down));
	$plt->set_var('select_server',str_replace("'","\'",$select_server));
	
	$playnum = 1;
	$rn='play';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	if(!empty($valarr['d_playfrom'])){
		$playfromarr = explode('$$$',$valarr['d_playfrom']);
	    $playserverarr = explode('$$$',$valarr['d_playserver']);
	    $playnotearr = explode('$$$',$valarr['d_playnote']);
	    $playurlarr = explode('$$$',$valarr['d_playurl']);
	    $i=0;
		foreach($playfromarr as $a){
			$playfrom = $playfromarr[$i];
			$playserver = $playserverarr[$i];
	    	$playnote = $playnotearr[$i];
	    	$playurl = str_replace('#', Chr(13),$playurlarr[$i]);
	    	
	    	$select_play_sel = str_replace('<option value=\''.$playfrom.'\' >','<option value=\''.$playfrom.'\' selected>',$select_play);
	    	
	    	$select_server_sel = str_replace('<option value=\''.$playserver.'\' >','<option value=\''.$playserver.'\' selected>',$select_server);
	    	
			$plt->set_var('n',$playnum);
			$plt->set_var('playurl',$playurl);
			$plt->set_var('playnote',$playnote);
			$plt->set_var('select_play_sel',$select_play_sel);
			$plt->set_var('select_server_sel',$select_server_sel);
			$i++;
			$playnum++;
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($arr);
	}
	else{
		$plt->set_var('rows_'.$rn,'');
	}
	$plt->set_var('playcount',$playnum);
	
	$downnum = 1;
	$rn='down';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	if(!empty($valarr['d_downfrom'])){
		$downfromarr = explode('$$$',$valarr['d_downfrom']);
	    $downserverarr = explode('$$$',$valarr['d_downserver']);
	    $downnotearr = explode('$$$',$valarr['d_downnote']);
	    $downurlarr = explode('$$$',$valarr['d_downurl']);
	    $i=0;
		foreach($downfromarr as $a){
			$downfrom = $downfromarr[$i];
			$downserver = $downserverarr[$i];
	    	$downnote = $downnotearr[$i];
	    	$downurl = str_replace('#', Chr(13),$downurlarr[$i]);
	    	
	    	$select_down_sel = str_replace('<option value=\''.$downfrom.'\' >','<option value=\''.$downfrom.'\' selected>',$select_down);
	    	$select_server_sel = str_replace('<option value=\''.$playserver.'\' >','<option value=\''.$playserver.'\' selected>',$select_server);
	    	
	    	
	    	
			$plt->set_var('n',$downnum);
			$plt->set_var('downurl',$downurl);
			$plt->set_var('downnote',$downnote);
			$plt->set_var('select_down_sel',$select_down_sel);
			$plt->set_var('select_server_sel',$select_server_sel);
			$i++;
			$downnum++;
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
		unset($arr);
	}
	else{
		$plt->set_var('rows_'.$rn,'');
	}
	$plt->set_var('downcount',$downnum);
	
	
	
	$typearr = $MAC_CACHE['vodtype'];
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
	
	$grouparr = $MAC_CACHE['usergroup'];
	$grouparrn = array();
	$grouparrv = array();
	foreach($grouparr as $arr){
		array_push($grouparrn,$arr['ug_name']);
		array_push($grouparrv,$arr['ug_id']);
	}
	
	$areaarr = explode(',',$MAC['app']['area']);
	$langarr = explode(',',$MAC['app']['lang']);
	
	$arr=array(
		array('a'=>'hide','c'=>$valarr['d_hide'],'n'=>array('显示','隐藏'),'v'=>array(0,1)),
		array('a'=>'level','c'=>$valarr['d_level'],'n'=>array('推荐1','推荐2','推荐3','推荐4','推荐5'),'v'=>array(1,2,3,4,5)),
		array('a'=>'type','c'=>$valarr['d_type'],'n'=>$typearrn,'v'=>$typearrv),
		array('a'=>'group','c'=>$valarr['d_group'],'n'=>$grouparrn,'v'=>$grouparrv),
		array('a'=>'area','c'=>$valarr['d_area'],'n'=>$areaarr,'v'=>$areaarr),
		array('a'=>'lang','c'=>$valarr['d_lang'],'n'=>$langarr,'v'=>$langarr)
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
	unset($grouparrn);
	unset($typearr);
	unset($areaarr);
	unset($langarr);
}

else
{
	showErr('System','未找到指定系统模块');
}
?>