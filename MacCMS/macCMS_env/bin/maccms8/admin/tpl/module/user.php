<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
$col_manager=array('m_id','m_name','m_password','m_levels','m_random','m_status','m_logintime','m_loginip','m_loginnum');
$col_group=array('ug_id','ug_name','ug_type','ug_popedom','ug_upgrade','ug_popvalue');
$col_user=array('u_id','u_qid','u_name','u_password','u_qq','u_email','u_phone','u_status','u_flag','u_question','u_answer','u_group','u_points','u_regtime','u_logintime','u_loginnum','u_extend','u_loginip','u_random','u_fav','u_plays','u_downs','u_start','u_end');
$col_card=array('c_id','c_number','c_pass','c_money','c_point','c_used','c_sale','c_user','c_addtime','c_usetime');

if($method=='manager')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$sql = 'SELECT count(*) FROM {pre}manager where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}manager where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY m_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_manager;
	
	$rn='manager';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['m_logintime'] = $row['m_logintime']==0 ? '' : getColorDay($row['m_logintime']);
		$valarr['m_loginip'] = long2ip($row['m_loginip']);
		$valarr['m_status'] = $row['m_status']==1 ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		
	}
	unset($rs);
	$pageurl = '?m=user-manager-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='managerinfo')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$m_id=$p['id'];
	$flag=empty($m_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_manager;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}manager where m_id='.$m_id);
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
	
	
	
	$arr=array(
		array('a'=>'status','c'=>$valarr['m_status'],'t'=>1,'n'=>array('禁用','启用'),'v'=>array(0,1)),
		array('a'=>'levels','c'=>$valarr['m_levels'],'t'=>0,'l'=>'chk','n'=>array('系统','扩展','视频','文章','用户','模板','生成','采集','数据库'),'v'=>array('b','c','d','e','f','g','h','i','j')),
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
			
			if($a['l']=='chk'){
				$c = strpos(",".$a['c'],$v) ? $cv: '';
			}
			else{
				$c = $a['c']==$v ? $cv: '';
			}
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	
	unset($colarr);
	unset($valarr);
}

elseif($method=='group')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	$sql = 'SELECT count(*) FROM {pre}user_group where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}user_group where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY ug_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_group;
	array_push($colarr,'ug_count');
	
	$rn='group';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		$ug_count = $db->getOne('SELECT count(u_id) FROM {pre}user WHERE u_group='.$row['ug_id']);
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['ug_count'] = $ug_count;
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($rs);
	$pageurl = '?m=user-group-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='groupinfo')
{
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$ug_id=$p['id'];
	$flag=empty($ug_id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_group;
	array_push($colarr,'flag','backurl');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}user_group where ug_id='.$ug_id);
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
	
	
	$typearr = $MAC_CACHE['vodtype'];
	$typearrn = array();
	$typearrv = array();
	foreach ($typearr as $arr)
	{
		array_push($typearrn,$arr['t_name']);
		array_push($typearrv,$arr['t_id']);
	}
	
	$arr=array(
		array('a'=>'popedom','c'=>$valarr['ug_popedom'],'t'=>0,'l'=>'chk','n'=>array('浏览分类页','浏览内容页','浏览播放页','浏览下载页'),'v'=>array('1','2','3','4')),
		array('a'=>'type','c'=>$valarr['ug_type'],'t'=>0,'l'=>'chk','n'=>$typearrn,'v'=>$typearrv)
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
			$s = $i>0 && $i%5 ==0 ? '<br>' : '';
			if($a['l']=='chk'){
				$c = strpos(",,".$a['c'], ','.$v.',') ? $cv: '';
			}
			else{
				$c = $a['c']==$v ? $cv: '';
			}
			$plt->set_var('v', $v );
			$plt->set_var('n', $n );
			$plt->set_var('c', $c );
			$plt->set_var('s', $s );
			$plt->parse('rows_'.$rn,'list_'.$rn,true);
		}
	}
	unset($typearrn);
	unset($typearrv);
	unset($typearr);
	unset($colarr);
	unset($valarr);
}

elseif($method=='list')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$group=$p['group']; if(isN($group)){ $group=999; } else { $group=intval($group); }
	$status=$p['status']; if(isN($status)){ $status=999; } else { $status=intval($status); }
	$wd=$p['wd'];
	
	if($group!=999){
		$where .=' and u_group='.$group.' ';
	}
	if($status!=999){
		$where .=' and u_status='.$status.' ';
	}
	if(!empty($wd) && $wd!='可搜索(会员名称)'){
		$where .= ' and instr(u_name,\''.$wd.'\')>0 ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(会员名称)');
	}
	
	$grouparr = $MAC_CACHE['usergroup'];
	$grouparrn = array();
	$grouparrv = array();
	foreach($grouparr as $arr){
		array_push($grouparrn,$arr['ug_name']);
		array_push($grouparrv,$arr['ug_id']);
	}
	
	$arr=array(
		array('a'=>'status','c'=>$status,'n'=>array('禁用','启用'),'v'=>array(0,1)),
		array('a'=>'group','c'=>$group,'n'=>$grouparrn,'v'=>$grouparrv)
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
	
	
	$sql = 'SELECT count(*) FROM {pre}user where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}user where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY u_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_user;
	
	$rn='user';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		$ug_count = $db->getOne('SELECT count(u_id) FROM {pre}user WHERE u_group='.$row['ug_id']);
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['u_group'] = $MAC_CACHE['usergroup'][$row['u_group']]['ug_name'];
		$valarr['u_logintime'] = $row['u_logintime']==0 ? '' : getColorDay($row['u_logintime']);
		$valarr['u_status'] = $row['u_status']==1 ? '<font color=green>启用</font>' : '<font color=red>禁用</font>';
		if($valarr['u_flag']==0){
			$valarr['u_flag']='计点';
		}
		elseif($valarr['u_flag']==1){
			$valarr['u_flag']='计时';
		}
		elseif($valarr['u_flag']==2){
			$valarr['u_flag']='IP段';
		}
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($rs);
	$pageurl = '?m=user-list-group-'.$group.'-status-'.$status.'-pg-{pg}-wd-'.urlencode($wd);;
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

elseif($method=='info')
{
	
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$id=$p['id'];
	$flag=empty($id) ? 'add' : 'edit';
	$backurl=getReferer();
	
	$colarr=$col_user;
	array_push($colarr,'flag','backurl','u_starttime','u_endtime','u_startip','u_endip');
	
	if($flag=='edit'){
		$row=$db->getRow('select * from {pre}user where u_id='.$id);
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
	
	if($valarr['u_flag']==1){
		
		$valarr['u_starttime'] = date('Y-m-d',$valarr['u_start']);
		$valarr['u_endtime'] = date('Y-m-d',$valarr['u_end']);
	}
	elseif($valarr['u_flag']==2){
		$valarr['u_startip'] = long2ip($valarr['u_start']);
		$valarr['u_endip'] = long2ip($valarr['u_end']);
	}
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$n];
		$plt->set_var($n, $v );
	}
	
	
	$grouparr = $MAC_CACHE['usergroup'];
	$grouparrn = array();
	$grouparrv = array();
	foreach($grouparr as $arr){
		array_push($grouparrn,$arr['ug_name']);
		array_push($grouparrv,$arr['ug_id']);
	}
	
	$arr=array(
		array('a'=>'status','c'=>$valarr['u_status'],'n'=>array('禁用','启用'),'v'=>array(0,1)),
		array('a'=>'flag','c'=>$valarr['u_flag'],'n'=>array('计点','包时','IP段'),'v'=>array(0,1,2)),
		array('a'=>'group','c'=>$valarr['u_group'],'n'=>$grouparrn,'v'=>$grouparrv),
		
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
	
	unset($grouparr);
	unset($grouparrv);
	unset($grouparrn);
	unset($colarr);
	unset($valarr);
}

elseif($method=='card')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$page = intval($p['pg']);
	if ($page < 1) { $page = 1; }
	
	$used=$p['used']; if(isN($used)){ $used=999; } else { $used=intval($used); }
	$sale=$p['sale']; if(isN($sale)){ $sale=999; } else { $sale=intval($sale); }
	$wd=$p['wd'];
	
	if($used!=999){
		$where .=' and c_used='.$used.' ';
	}
	if($sale!=999){
		$where .=' and c_sale='.$sale.' ';
	}
	if(!empty($wd) && $wd!='可搜索(充值卡号)'){
		$where .= ' and instr(c_number,\''.$wd.'\')>0 ';
		$plt->set_var('wd',$wd);
	}
	else{
		$plt->set_var('wd','可搜索(充值卡号)');
	}
	
	
	$arr=array(
		array('a'=>'used','c'=>$used,'n'=>array('未使用','已使用'),'v'=>array(0,1)),
		array('a'=>'sale','c'=>$sale,'n'=>array('未出售','已出售'),'v'=>array(0,1)),
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
	
	
	$sql = 'SELECT count(*) FROM {pre}user_card where 1=1 '.$where;
	$nums = $db->getOne($sql);
	$pagecount=ceil($nums/$MAC['app']['pagesize']);
	$sql = "SELECT * FROM {pre}user_card where 1=1 ";
	$sql .= $where;
	$sql .= " ORDER BY c_id DESC limit ".($MAC['app']['pagesize'] * ($page-1)) .",".$MAC['app']['pagesize'];
	$rs = $db->query($sql);
	
	if($nums==0){
		$plt->set_if('main','isnull',true);
		return;
	}
	$plt->set_if('main','isnull',false);
	
	$colarr=$col_card;
	
	$rn='card';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
	while ($row = $db ->fetch_array($rs))
	{
		$valarr=array();
		$c_user = $db->getOne('SELECT u_name FROM {pre}user WHERE u_id='.$row['c_user']);
		for($i=0;$i<count($colarr);$i++){
			$n=$colarr[$i];
			$valarr[$n]=$row[$n];
		}
		$valarr['c_user'] = $c_user;
		$valarr['c_sale'] = $row['c_sale']==1 ? '<font color=red>已使用</font>' : '<font color=green>未使用</font>';
		$valarr['c_used'] = $row['c_used']==1 ? '<font color=red>已出售</font>' : '<font color=green>未出售</font>';
		$valarr['c_usetime'] = getColorDay($row['c_usetime']);
		
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$n];
			$plt->set_var($n, $v );
		}
		
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
	}
	unset($rs);
	$pageurl = '?m=user-card-sale-'.$sale.'-used-'.$used.'-pg-{pg}';
	$pages = '共'.$nums.'条数据&nbsp;当前:'.$page.'/'.$pagecount.'页&nbsp;'.pageshow($page,$pagecount,3,$pageurl,'pagego(\''.$pageurl.'\','.$pagecount.')');
	$plt->set_var('pages', $pages );
}

else
{
	showErr('System','未找到指定系统模块');
}
?>