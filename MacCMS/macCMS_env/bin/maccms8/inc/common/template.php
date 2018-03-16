<?php
class AppTpl
{
    var $markname,$markpar,$markdes,$markval,$markhtml;
    function AppTpl()
    {
    	$this->P = array("vodtypeid"=>-1,"vodtypepid"=>-1,"vodtopicid"=>-1,"arttypeid"=>-1,"arttypepid"=>-1,"arttopicid"=>-1,"auto"=>false,"pg"=>1);
	}
	
    function getLink($f,$l,$t,$d,$pg='')
    {
    	$ext = '.'.$GLOBALS['MAC']['app']['suffix'];
    	$rgext = $ext;
	    $v = $GLOBALS['MAC']['view'][$f.$l];
	    $r = $GLOBALS['MAC']['rewrite'][$f.$l];
	    $p = $GLOBALS['MAC']['path'][$f.$l];
	    if($pg!=''){
			$strpg = '-pg-'.$pg;
		}
		$vi = $GLOBALS['MAC']['view']['vodindex'];
		$is = $vi==2 ? 'index.php':'' ;
		if(is_array($t)){
			if($t['t_pid']>0){ $tp=$GLOBALS['MAC_CACHE'][$f.'type'][$t['t_pid']]; }
		}
		
	    switch($l)
	    {
	    	case 'index':
	    		$str= '?m='.$f.'-index';
	    		if($v>0){
	    			$str= $v==1 ? $r : $p;
	    			if($v==1 && empty($pg)){
						$pg=1;
					}
	    		}
	    		if($f=='art' && $v<1) { $str = $is.$str; }
	    		break;
	    	case 'map':
	    		$str= $is.'?m='.$f.'-map';
	    		if($v>0){
	    			$str= $v==1 ? $r : $p;
	    		}
	    		break;
	    	case 'search':
	    		$str = 'index.php?m='.$f.'-search';
	    		if($v>0){
	    			$str = $r;
	    		}
	    		$strpg='';
	    		$colarr=array('pg','wd','year','starring','directed','pinyin','letter','ids','area','lang','tag','typeid','classid','order','by');
	    		
	    		$valarr=array();
	    		foreach($colarr as $k=>$v){
	    			$c = $colarr[$k];
	    			$valarr[$c] = $this->P[$c];
	    		}
	    		foreach($d as $k=>$v){
					$valarr[$k]=$v;
				}
	    		foreach($colarr as $k=>$v){
	    			$c = $colarr[$k];
	    			$vv = $valarr[$c];
	    			if(!empty($vv)){
	    				if($vv!='{'.$c.'}') { $vv=urlencode($vv); }
	    				$str.= '-'.$c.'-'.$vv;
	    				$str = str_replace('{'.$c.'}',$vv,$str);
	    			}
	    		}
				break;
	    	case 'list':
	    		$strpg='';
	    		$str = 'index.php?m='.$f.'-list-id-{id}-pg-{pg}-order-{order}-by-{by}';
	    		if($f=='vod'){
	    			$str .='-class-{class}-year-{year}-letter-{letter}-area-{area}-lang-{lang}';
	    		}
				if($v>0){
					$str = $r;
				}
				$colarr = array('{id}','{pg}','{order}','{by}','{class}','{year}','{letter}','{area}','{lang}');
				$valarr = array('id'=>$this->P['id'],'pg'=>'1','order'=>$this->P['order'],'by'=>$this->P['by'],'class'=>$this->P['class'],'year'=>$this->P['year'],'letter'=>$this->P['letter'],'area'=>$this->P['area'],'lang'=>$this->P['lang']);
				foreach($d as $k=>$v){
					$valarr[$k]=$v;
				}
				
				$str=str_replace($colarr,$valarr,$str);
				
	    		break;
	    	case 'type':
	    		$str = $is.'?m='.$f.'-type-id-'.$t['t_id'];
	    		if(!empty($GLOBALS['m']) && $au) { $v=0; }
	    		if($v>0){
	    			$str = str_replace(array('{id}','{en}','{md5}','{p_id}','{p_en}','{p_md5}'),array($t['t_id'],$t['t_enname'],md5($t['t_id']),$tp['t_id'],$tp['t_enname'],md5($tp['t_id'])),$v==1 ? $r : $p);
	    			if($v==1 && empty($pg)){
						$pg=1;
					}
	    		}
		   		break;
		   	case 'topicindex':
	    		$str = $is.'?m='.$f.'-topicindex';
	    		if($v>0){
	    			$str= $v==1 ? $r : $p;
	    		}
	    		break;
		   	case 'topic':
		   		$str = $is.'?m='.$f.'-topic-id-'.$t['t_id'];
		   		if($v>0){
		   			$str = str_replace(array("{id}","{en}","{md5}"),array($t['t_id'],$t['t_enname'],md5($t['t_id'])),$v==1 ? $r : $p);
		   		}
		   		break;
		   	case 'detail':
		   		$pre = $f=='art'?'a_':'d_';
		   		$str = $is.'?m='.$f.'-detail-id-'.$d[$pre.'id'];
		   		
		   		if($v>0){
		    		$str = str_replace(array("{type_id}","{type_en}","{type_pid}","{type_pen}","{id}","{md5}"),array($t['t_id'],$t['t_enname'],$tp['t_id'],$tp['t_enname'],$d[$pre.'id'],md5($d[$pre.'id'])),$v==1 ? $r : $p);
			        if(strpos(",".$str,"{en}")){ $str = str_replace("{en}", repSpecialChar($d[$pre.'enname']),$str); }
			        if(strpos(",".$str,"{year}")){ $str = str_replace("{year}", getDatet("Y",$d[$pre.'addtime']) ,$str); }
			        if(strpos(",".$str,"{month}")){ $str = str_replace("{month}", getDatet("m",$d[$pre.'addtime']) ,$str); }
			        if(strpos(",".$str,"{day}")){ $str = str_replace("{day}", getDatet("d",$d[$pre.'addtime']) ,$str); }
		   		}
		   		break;
		   	case 'play':
		   	case 'down':
		   		$pre = 'd_';
				if ($GLOBALS['app]']['isopen']==0){ $jstart = ""; $jend = ""; }
				else{$jstart = "javascript:MAC.Open('"; $jend = "',mac_widthpop,mac_heightpop);"; }
				$str = $is."?m=vod-".$l."-id-".$d['d_id']."-src-{src}-num-{num}";
				if($v>0){
					$str = str_replace(array("{type_id}","{type_en}","{type_pid}","{type_pen}","{id}","{md5}"),array($t['t_id'],$t['t_enname'],$tp['t_id'],$tp['t_enname'],$d[$pre.'id'],md5($d[$pre.'id'])),$v==1 ? $r : $p);
					if (strpos(",".$str,"{en}")){ $str = str_replace("{en}", repSpecialChar($d['d_enname']),$str); }
					if (strpos(",".$str,"{year}")){ $str = str_replace("{year}", getDatet("Y",$d['d_addtime']) ,$str); }
					if (strpos(",".$str,"{month}")){ $str = str_replace("{month}", getDatet("m",$d['d_addtime']) ,$str); }
					if (strpos(",".$str,"{day}")){ $str = str_replace("{day}", getDatet("d",$d['d_addtime']) ,$str); }
					
					if($v==2){
						$str .= $ext."?".$d['d_id']."-{src}-{num}";
						$ext='';
					}
					elseif ($v == 3){
						$str .= "-{src}-{num}".$ext;
						$ext='';
					}
					elseif ($v == 4){
						$str .= "-{src}-1".$ext."?".$d['d_id']."-{src}-{num}";
						$ext='';
					}
				}
				break;
			case 'gbook':
				$str = 'index.php?m=gbook-show';
				if($v>0){
					$str = $r;
				}
				break;
			case 'comment':
				$str = 'index.php?m=comment-show-aid-'.$this->P['siteaid'].'-vid-'. ($f=='art' ? $d['a_id']:$d['d_id']);
				if($v>0){
					$str = $r;
				}
				break;
			case 'rss':
			case 'baidu':
			case 'google':
				$v = $GLOBALS['MAC']['view']['rss'];
				$str = 'index.php?m=map-'.$l;
				if($v==1){
					$str = $GLOBALS['MAC']['rewrite']['rss'];
					if(empty($pg)){
						$pg=1;
					}
				}
				elseif($v==2){
					$str = $l;
					$ext = '.xml';
				}
				$str = str_replace('{method}',$l,$str);
				break;
			case 'label':
				$v = $GLOBALS['MAC']['view']['label'];
				$path = trim($f);
				$f = substr($path,0,strpos($path,'.'));
				$ext = '.'.substr($path,strpos($path,'.')+1,strlen($path)-strpos($path,'.'));
				$str = 'index.php?m=label-'.$f;
				if($v>0){
					$r = $GLOBALS['MAC']['rewrite']['label'];
					if($v==1 && empty($pg)){
						$pg=1;
					}
					$str = $v==1 ? str_replace('{label}',$f,$r) : str_replace('$$','/',$f);
				}
				break;
	    }
	    if(strpos($str,'{pg}')){
	    	$str=str_replace('{pg}',$pg,$str);
	    	$strpg='';
	    }
    	$str = $jstart . MAC_PATH . $str . $strpg . $ext. $jend;
    	return str_replace(array('//','/index'.$rgext),array('/','/'),$str);
    }
    
    function getPreNextLink($tab,$id,$flag)
    {
    	$col = $tab=='vod' ? 'd'  : 'a';
    	if ($flag==0) {
    		$str1="上一篇"; $where = " and ".$col."_id<".$id." order by ".$col."_id desc";
    	} 
    	else{
    		$str1="下一篇"; $where = " and ".$col."_id>".$id." order by ".$col."_id asc";
    	}
    	$sql = "select ".$col."_id,".$col."_name,".$col."_enname,".$col."_type,".$col."_addtime from {pre}".$tab." where 1=1 and ".$col."_hide=0 and ".$col."_type>0 " .$where . " limit 0,1";
    	
    	$row = $GLOBALS['db']->getRow($sql);
    	if (!$row){
    		$str = "<em>".$str1.":没有了</em> ";
    	}
    	else{
    		$typearr = $GLOBALS['MAC_CACHE'][$tab.'type'][$row[$col."_type"]];
    		$str = "<em>".$str1.":<a href=". $this->getLink($tab,'detail',$typearr,$row).">".$row[$col."_name"]."</a></em> ";
    	}
    	unset($row);
    	return $str;
    }
    
    function getPageListSizeByCache($flag,$label)
    {
    	$cp = $flag.$label;
    	$cn = $flag.'-'.$this->P['id'].'-pagesize';
    	
        if (chkCache($cp,$cn)){
            $psize = getCache($cp,$cn);
        }
        else{
            $labelRule = "\{maccms:".$flag."[\s\S]+?pagesize=([\d]+)[\s\S]*\}";
		    $labelRule = buildregx($labelRule,"is");
		    preg_match_all($labelRule,$this->H,$arr);
			for($i=0;$i<count($arr[1]);$i++)
			{
				$psize=$arr[1][$i];
				break;
			}
			if(!isNum($psize)) { $psize=10; }
			setCache($cp,$cn,$psize);
        }
        return $psize;
    }
    
    function run()
    {
		$this->H = str_replace("{maccms:runtime}", getRunTime(),$this->H);
    }
    
    function getDataCount($table,$flag)
	{
		switch($table)
		{
			case 'vod':
				$col = 'd_time';
				$sql = 'SELECT count(*) FROM {pre}vod WHERE d_hide=0 AND d_type>0 ';
				break;
			case 'art':
				$col = 'a_time';
				$sql = 'SELECT count(*) FROM {pre}art WHERE a_hide=0 AND a_type>0 ';
				break;
			case 'user':
				$col = 'u_regtime';
				$sql = 'SELECT count(*) FROM {pre}user WHERE u_status=1 ';
				break;
			case 'gbook':
				$col = 'g_time';
				$sql = 'SELECT count(*) FROM {pre}gbook WHERE g_hide=0 ';
				break;
			case 'comment':
				$col = 'c_time';
				$sql = 'SELECT count(*) FROM {pre}comment WHERE c_hide=0 ';
				break;
			case 'art_relation':
			case 'vod_relation':
				$col = '';
				$sql = 'SELECT count(*) FROM {pre}'.$table .' WHERE 1=1 ';
				break;
		}
		if ($flag == 'day'){
			$todaydate = date('Y-m-d');
			$tommdate = date('Y-m-d',strtotime('+1 day'));
			$todayunix = strtotime($todaydate);
			$tommunix = strtotime($tommdate);
			$where = ' AND '.$col.'>= '. $todayunix . ' AND '.$col.'<='. $tommunix;
		}
		elseif($flag=='all'){
			
		}
		else{
			$where .= $flag;
		}
		return $GLOBALS['db']->getOne($sql.$where);
	}
	
    function mark_sql()
    {
    	global $MAC,$MAC_CACHE;
		$labelRule = buildregx("([a-z0-9]+)=([\x{4e00}-\x{9fa5}|a-zA-Z0-9|,|$|.|+|-]+)","") . "u";
		preg_match_all($labelRule,$this->markpar,$matches);
		
		$this->L=array();
		$lp=array();
		for($j=0;$j< count($matches[0]);$j++)
		{
			$parname = $matches[1][$j];
			$parval = $matches[2][$j];
			$lp[$parname] = $parval;
		}
		
		if(isN($lp['table'])) { $lp['table'] = 'vod'; }
        if(isN($lp['type'])) { $lp['type'] = 'all'; }
        if($lp['order']!='asc' && $lp['order']!='desc') { $lp['order'] = 'asc'; }
		unset($matches);
		
		if(!isNum($lp['start'])) { $lp['start']=0; } else { $lp['start']=intval($lp['start']); }
    	if($lp['start']>0){ $lp['start']--; }
    	if(!isNum($lp['num'])){ $lp['num']=12;} else { $lp['num']=intval($lp['num']); }
    	if(!empty($lp['label'])){ $this->P['label']=$lp['label']; }
    	if(!empty($lp["pagesize"])){ $auto='';$lp['num']=intval($lp['pagesize']); } else { $auto='false'; }
    	
        $order = ' ORDER BY ';
        $limit = ' limit 0,'. ($lp['num'] + $lp['start']);
		switch($this->markname)
		{
			case 'link':
				$tb = 'link';
				$col = '`l_id`, `l_name`, `l_url`, `l_logo`, `l_type`, `l_sort`';
				$limit = '';
				switch($lp['by'])
	            {
	                case 'id': $order .= ' l_id ';break;
	                default: $order .= ' l_sort ';break;
	            }
				$order .=$lp['order'];
				if($lp['type']=='all'){
				}
				else{
					$where .= $lp['type']=='pic' ? ' AND l_type=1 ' : " AND l_type=0 ";
				}
				break;
			case 'gbook':
				$tb = 'gbook';
				$col = '`g_id`, `g_vid`, `g_hide`, `g_name`, `g_content`, `g_reply`, `g_ip`, `g_time`, `g_replytime`';
				switch($lp['by'])
	            {
	                case 'id': $order .= ' g_id ';break;
	                default: $order .= ' g_time ';break;
	            }
				$order .=$lp['order'];
				$where .= ' AND g_hide=0 ';
				if (!empty($this->P['wd'])){
					$where .= ' AND instr(g_name,\''.$this->P['wd'].'\')>0 ';
				}
		        
				$this->P['pageflag'] = '';
				$this->P['pagetype'] = 'gbook';
		        $limitstart = $lp['num'] * ($this->P['pg']-1);
		        $limit = ' limit '.$limitstart.','. ($lp['num'] + $lp['start']);
		        
		        break;
		    case 'comment':
				$tb = 'comment';
				$col = '`c_id`, `c_vid`, `c_hide`, `c_name`, `c_content`, `c_ip`, `c_time`, `c_type`';
				switch($lp['by'])
	            {
	                case 'id': $order .= ' c_id ';break;
	                default: $order .= ' c_time ';break;
	            }
				$order .=$lp['order'];
				$where .= ' AND c_hide=0 ';
				
				if(isNum($this->P['vid'])){
					$where .=' AND c_vid='.$this->P['vid'];
				}
				elseif($lp['vid']=='current'){
					$where .=' AND c_vid='.$this->D['d_id'];
				}
				
				$this->P['pageflag'] = '';
				$this->P['pagetype'] = 'comment';
		        $limitstart = $lp['num'] * ($this->P['pg']-1);
		        $limit = ' limit '.$limitstart.','. ($lp['num'] + $lp['start']);
		        
		        break;
			case 'matrix':
			case 'menu': 
				$tb = $lp['table'].'_type';
				$col = ' `t_id`, `t_name`, `t_enname`,  `t_pid`, `t_sort`, `t_hide`, `t_tpl`, `t_key`, `t_des` ';
				$limit = '';
				switch($lp['by'])
	            {
	                case 'id': $order .= ' t_id ';break;
	                default: $order .= ' t_sort ';break;
	            }
				
				$order .=$lp['order'];
				$where .= ' AND t_hide=0 ';
	            
				if($lp['type']=='tag'){
					return;
				}
				elseif($lp['type']=='all'){
	        	}
		        elseif($lp['type']=='parent'){
		        	$where .= ' AND t_pid =0 ';
		        }
		        elseif($lp['type']== 'child'){
		        	$where .= ' AND t_pid >0 ';
		        }
		        elseif($lp['type']=='auto'){
		        	if(!is_array($this->T)){
		        		$where .=  " AND t_pid >0 ";
		        	}
		        	else{
		        		$this->P["auto"]=true;
		        		if($this->T['t_pid']==0){
			        		$where .= " AND t_pid = " . $this->T["t_id"];
			        	}
			        	else{
			        		$where .= " AND t_pid = " . $this->T["t_pid"];
			        	}
		        	}
		        }
		        elseif(!isN($lp['type'])){ 
		        	$where .= ' AND t_id IN(' . $lp['type'] . ') ';
		        }
		        
		        if($lp['parent']=='all'){
	        		$where .= ' AND t_pid =0 ';
	        	}
		        elseif(!isN($lp['parent'])){
		        	$where .= ' AND t_pid IN(' . $lp['parent'] . ') ';
		        }
		        
		        
		        if($lp['table']=='vod'){
		        	$col .=',`t_tpl_vod`, `t_tpl_play`, `t_tpl_down` ';
		        	if($GLOBALS['MAC']['user']['status']==1){ $where .= getTypeByPopedomFilter('menu'); }
				}
				else{
					$col .=',`t_tpl_art`';
				}
				break;
			case 'topic':
				$tb = $lp['table'].'_topic';
				$col ='`t_id`, `t_name`, `t_enname`,`t_sort`, `t_level`, `t_pic`,`t_key` ,`t_des`,`t_title`,`t_content`,`t_hits`,`t_dayhits`,`t_weekhits`,`t_monthhits`,`t_up`,`t_down`,`t_score`,`t_scoreall`,`t_scorenum`,`t_addtime`,`t_time` ';
	            
	            switch($lp['by'])
		        {
		            case "id": $order .= " t_id " ;break;
		            case "hits": $order .= " t_hits " ;break;
		            case "dayhits": $order .= " t_dayhits " ;break;
		            case "weekhits": $order .= "  t_weekhits ";break;
		            case "monthhits": $order .= " t_monthhits " ;break;
		            case "addtime": $order .= " t_addtime ";break;
		            case "level": $order .= " t_level ";break;
		            case "up": $order .= " t_up ";break;
		            case "down": $order .= " t_down ";break;
		            default: $order .= " t_time ";break;
		        }
		        
				$order .=$lp['order'];
				$where .= ' AND t_hide=0 ';
				
				if( !isN($lp['id']) && $lp['id'] != "all"){ 
		        	$where = $where . ' AND t_id IN(' . $lp['id'] . ') ';
		        }
		        if($auto==''){
		        	$this->P['pageflag'] = $lp['table'];
		        	$this->P['pagetype'] = 'topicindex';
		        	$limitstart = $lp['num'] * ($this->P['pg']-1);
		        	$limit = ' limit '.$limitstart.','. ($lp['num'] + $lp['start']);
		        }
				break;
			case 'class':
				$tb = 'vod_class';
				$col = ' `c_id`, `c_name`, `c_pid`, `c_sort`, `c_hide` ';
				$limit = '';
				switch($lp['by'])
	            {
	                case 'id': $order .= ' c_id ';break;
	                default: $order .= ' c_sort ';break;
	            }
				
				$order .=$lp['order'];
				$where .= ' AND c_hide=0 ';
	            
				if($lp['type']=='auto'){
		        	if(is_array($this->T)){
		        		$this->P["auto"]=true;
		        		if($this->T['t_pid']==0){
			        		$where .= " AND c_pid = " . $this->T["t_id"];
			        	}
			        	else{
			        		$where .= " AND c_pid = " . $this->T["t_pid"];
			        	}
		        	}
		        }
		        elseif($lp['type']!='' && $lp['type']!='all'){ 
		        	$where .= ' AND c_pid IN(' . $lp['type'] . ') ';
		        }
		        
		        if(!isN($lp['id'])){ 
		        	$where .= ' AND c_id IN(' . $lp['id'] . ') ';
		        }
				break;
			case 'vod':
				$tb = 'vod';
				$col = '`d_id`, `d_name`, `d_subname`, `d_enname`, `d_letter`, `d_color`, `d_pic`, `d_picthumb`, `d_picslide`, `d_starring`, `d_directed`, `d_tag`, `d_remarks`, `d_area`, `d_lang`, `d_year`, `d_type`, `d_class`, `d_hide`, `d_lock`, `d_state`, `d_level`, `d_usergroup`, `d_stint`, `d_stintdown`, `d_hits`, `d_dayhits`, `d_weekhits`, `d_monthhits`, `d_duration`, `d_up`, `d_down`, `d_score`,`d_scoreall`, `d_scorenum`, `d_addtime`, `d_time`, `d_hitstime`, `d_maketime`, `d_playfrom`, `d_playserver`, `d_playnote`,`d_downfrom`, `d_downserver`, `d_downnote` ';
				if(strpos($this->markdes,':content')>0){
					$col .= ', `d_content`';
				}
				
            	if (!isN($this->P["order"])){ $lp['order'] = $this->P["order"]; $this->P["auto"] = true; }
            	if (!isN($this->P["by"])){ $lp['by'] = $this->P["by"]; $this->P["auto"] = true; }
            	if (!isN($this->P["area"])){ $lp['area'] = $this->P["area"]; $this->P["auto"] = true; }
            	if (!isN($this->P["year"])){ $lp['year']  = $this->P["year"]; $this->P["auto"] = true; }
				if (!isN($this->P["lang"])){ $lp['lang']  = $this->P["lang"]; $this->P["auto"] = true; }
				if (!isN($this->P["letter"])){ $lp['letter']  = $this->P["letter"]; $this->P["auto"] = true; }
				if (!isN($this->P["class"])){ $lp['class']  = $this->P["class"]; $this->P["auto"] = true; }
				
				switch($lp['by'])
		        {
		            case "id": $order .= " d_id " ;break;
		            case "hits": $order .= " d_hits " ;break;
		            case "dayhits": $order .= " d_dayhits " ;break;
		            case "weekhits": $order .= "  d_weekhits ";break;
		            case "monthhits": $order .= " d_monthhits " ;break;
		            case "addtime": $order .= " d_addtime ";break;
		            case "level": $order .= " d_level ";break;
		            case "up": $order .= " d_up ";break;
		            case "down": $order .= " d_down ";break;
		            case "score": $order .= " d_score ";break;
		            case "scoreall": $order .= " d_scoreall ";break;
		            case "scorenum": $order .= " d_scorenum ";break;
		            case "rnd": 
		            	$datacount = $this->getDataCount('vod','all');
            			$rand = mt_rand(0,$datacount-$lp['num']);
            			if($rand<0) { $rand=0; }
		            	$where .= ' AND d_id >=' .$rand ;
		            	$order .= " d_time ";
		            	$lp['order']='asc';
		            	break;
		            default: $order .= " d_time ";break;
		        }
		        $order .=$lp['order'];
		        
		        $where .= ' AND d_hide=0 AND d_type>0 ';
		        
		        if ($lp['state']=='series'){ 
		        	$where .= ' and d_state > 0';
		        }
		        elseif(isNum($lp['state']) ){
		        	$where .=  ' and d_state = ' . $lp['state'] ;
		        }
		        
		        if (!isN($lp['level'])){
		            if($lp['level'] != 'all'){
		                $where .= ' and d_level in(' . $lp['level'] . ')';
		            }
		            else{
		                $where .= ' and d_level >0';
		        	}
		        }
		        
		        if($auto==''){
		        	$this->P['pageflag'] = 'vod';
		        	$limitstart = $lp['num'] * ($this->P['pg']-1);
		        	$limit = ' limit '.$limitstart.','. ($lp['num'] + $lp['start']);
		        	
		        	if ($this->P['vodtypeid'] != -1){
		        		$typearr = $MAC_CACHE['vodtype'][$this->P['vodtypeid']];
		        		$expand='';
		                
						
						
		                $where .= ' AND ( d_type IN (' . $typearr['childids'] . ') '.$expand.' )';
		                
		                
		                $this->P['pagetype'] = 'type';
		                if($GLOBALS['method']!='type'){
		                	$this->P['pagetype'] = 'list';
		                }
		                $this->P['maxpage'] = $lp['maxpage'];
		            }
		            elseif ($this->P['vodtopicid'] != -1){
		                $where .= ' AND d_id IN(select r_b from {pre}vod_relation where r_type=2 and r_a='.$this->P['vodtopicid'].')';
		                $this->P['pagetype'] = 'topic';
		            }
		            elseif (!isN($this->P['ids'])){
		                $where .= ' AND d_id IN( ' . $this->P['ids']. ' )';
		                $this->P['pagetype'] = 'search';
		            }
		            elseif(!isN($lp['label'])){
		            	$this->P['pageflag'] = $lp['label'];
		            	$this->P['pagetype'] = 'label';
		            	
		            }
		            else{
		                $where = $this->P['where'];
		                if (!isN($this->P['des'])){ $this->P['pagetype'] = 'search';  }
		            }
		        }
		        else{
			        if (!isN($lp['type'])){
				        if ($lp['type'] != 'all'){
				            if ($lp['type'] == 'current' && $this->P['vodtypeid'] > -1){
				                $typearr = $MAC_CACHE['vodtype'][$this->P['vodtypeid']];
				                $where .= ' and d_type in (' . $typearr['childids'].')';
				            }
							else{
								if ( strpos ($lp['type'],',')>0){
									$where .= ' and d_type in (' . $lp['type']. ')';
								}
								else{
									$typearr = $MAC_CACHE['vodtype'][$lp['type']];
									if(is_array($typearr)){
										$where .= ' and d_type in (' . $typearr['childids'] . ')';
									}
								}
				            }
				        }
			        }
			        
			        if (!isN($lp['topic'])){
				        if ($lp['topic'] != 'all'){
				            if ($lp['topic'] == 'current' && $this->P['vodtopicid'] > -1){
				                $where .= ' AND d_id IN(select r_b from {pre}vod_relation where r_type=2 and r_a='.$this->P['vodtopicid'].')';
				                ' and d_topic in (' . $this->P['vodtopicid'] . ')';
				            }
					        else{
				                $where .= ' AND d_id IN(select r_b from {pre}vod_relation where r_type=2 and r_a('.$lp['topic'].'))';
				            }
				        }
			        }
		        }
		        
		        if(!isN($lp['class'])){
					$where .= ' AND instr(d_class,\','.$this->P['class'].',\')>0  ';
				}
				
		        if(!isN($lp['year'])){
		        	$where .= ' and d_year=' . $lp['year'] . '';
		        }
		        if(!isN($lp['letter'])){
		        	$where .= ' and d_letter=\'' . $lp['letter'] . '\'';
		        }
				if(!isN($lp['day'])){
					$symbol='';
					if(strpos(','.$lp['day'],'+')){ 
						$symbol='+'; 
					}
					elseif(strpos(','.$lp['day'],'-')==false){
						$symbol='-'; 
					}
					
					$todaydate = date('Y-m-d');
					$tommdate = date('Y-m-d',strtotime($symbol.$lp['day'].' day'));
					$tommunix = strtotime($tommdate);
					$todayunix = $tommunix + 86400;
					$where .= ' AND ( d_time>'. $tommunix . ' AND d_time<'.$todayunix.') ' ;
				}
				if(!isN($lp['days'])){
					$symbol='-';
					if(strpos(','.$lp['days'],'+')){ $symbol='+'; }
					$todaydate = date('Y-m-d');
					$tommdate = date('Y-m-d',strtotime($symbol.$lp['days'].' day'));
					$todayunix = strtotime($todaydate);
					$tommunix = strtotime($tommdate);
					$where .= ' AND d_time>'. $tommunix;
				}
				if (!isN($lp['area'])){
		        	$where .= ' and d_area=\'' . $lp['area'] . '\'';
		        }
		        if (!isN($lp['lang'])){
		        	$where .=  ' and d_lang=\'' . $lp['lang'] . '\'';
		        }
		        if(!isN($lp['id'])){ 
		        	$where .= ' AND d_id IN(' . $lp['id'] . ') ';
		        }
				if ($GLOBALS['MAC']['view']['vodtype']!=2 && $GLOBALS['MAC']['user']['status']==1){
					$where .= getTypeByPopedomFilter('vod');
				}
		        if (!isN($lp['starring'])){
		        	$where .= ' AND instr(d_starring,\''.$lp['starring'].'\')>0  ';
		        }
		        if (!isN($lp['tag'])){
		        	$where .= ' AND instr(d_tag,\''.$lp['tag'].'\')>0  ';
		        }
		        
				break;
			case 'art':
				$tb = 'art';
				$col = '`a_id`, `a_name`, `a_subname`, `a_enname`, `a_letter`, `a_color`, `a_from`, `a_author`, `a_tag`, `a_pic`, `a_type`, `a_level`, `a_hide`, `a_lock`, `a_hits`, `a_dayhits`, `a_weekhits`, `a_monthhits`, `a_addtime`, `a_time`, `a_hitstime`, `a_maketime`';
				if(strpos($this->markdes,':content')>0){
					$col .= ', `a_content`';
				}
				if (!isN($this->P["order"])){ $lp['order'] = $this->P["order"]; $this->P["auto"] = true; }
            	if (!isN($this->P["by"])){ $lp['by'] = $this->P["by"]; $this->P["auto"] = true; }
				if (!isN($this->P["letter"])){ $lp['letter']  = $this->P["letter"]; $this->P["auto"] = true; }
				
				switch($lp['by'])
	            {
	                case 'id': $order .= ' a_id ';break;
	                case 'level': $order .= ' a_level ';break;
	                case 'hits': $order .= ' a_hits ';break;
	                case 'dayhits': $order .= ' a_dayhits ';break;
	                case 'weekhits': $order .= ' a_weekhits ';break;
	                case 'monthhits': $order .= ' a_monthhits ';break;
	                case 'addtime': $order .= ' a_addtime ';break;
	                case 'level': $order .= ' a_level ';break;
	                case 'up': $order .= ' a_up ';break;
	                case 'down': $order .= ' a_down ';break;
	                case 'rnd': 
		            	$datacount = $this->getDataCount('art','all');
            			$rand = mt_rand(0,$datacount-$lp['num']);
            			if($rand<0) { $rand=0; }
		            	$where .= ' AND a_id >=' .$rand ;
		            	$order .= ' a_time ';
		            	$lp['order']='asc';
		            	break;
	                default: $order .= ' a_time ';break;
	            }
				$order .=$lp['order'];
				
		        $where .= ' AND a_hide=0 AND a_type>0 ';
		        
		        if (!isN($lp['level'])){
		            if($lp['level'] != 'all'){
		                $where .= ' and a_level in(' . $lp['level'] . ')';
		            }
		            else{
		                $where .= ' and a_level >0';
		        	}
		        }
		        
		        if($auto==''){
		        	$this->P['pageflag'] = 'art';
		        	$limitstart = $lp['num'] * ($this->P['pg']-1);
		        	$limit = ' limit '.$limitstart.','. ($lp['num'] + $lp['start']);
		        	
			        if ($this->P['arttypeid'] != -1){
		                $typearr= $MAC_CACHE['arttype'][$this->P['arttypeid']];
		                $where .= ' AND a_type IN (' . $typearr['childids'] . ')';
		                $this->P['pagetype'] = 'type';
		                if($GLOBALS['method']!='type'){
		                	$this->P['pagetype'] = 'list';
		                }
		                $this->P['maxpage'] = $lp['maxpage'];
		            }
		            elseif ($this->P['arttopicid'] != -1){
		                $where .= ' AND a_id IN(select r_b from {pre}art_relation where r_type=2 and r_a='.$this->P['arttopicid'].')';
		                $this->P['pagetype'] = 'topic';
		            }
		            elseif (!isN($this->P['ids'])){
		                $where .= ' AND a_id IN( ' . $this->P['ids'] . ' )';
		                $this->P['pagetype'] = 'search';
		            }
		            else{
		                $where = $this->P['where'];
		                if (!isN($this->P['des'])){ $this->P['pagetype'] = 'search';  }
		            }
		        }
		        else{
		        	if (!isN($lp['type'])){
				        if ($lp['type'] != 'all'){
				            if ($lp['type'] == 'current' && $this->P['arttypeid'] > -1){
				                $typearr = $MAC_CACHE['arttype'][$this->P['arttypeid']];
				                $where .=  ' and a_type in (' . $typearr['childids'] .')';
				            }
				            else{
				                if ( strpos ($lp['type'],',')>0){
									$where .= ' and a_type in (' . $lp['type'] . ')';
								}
								else{
									$typearr = $MAC_CACHE['arttype'][$lp['type']];
									if(is_array($typearr)){
										$where .=  ' and a_type in (' . $typearr['childids'] . ')';
									}
								}
								
				            }
				        }
			        }
			        
			        if (!isN($lp['topic'])){
				        if ($lp['topic'] != 'all'){
				            if ($lp['topic'] == 'current' && $this->P['arttopicid'] > -1){
				                $where .=  ' AND a_id IN(select r_b from {pre}art_relation where r_type=2 and r_a='.$this->P['arttopicid'].')';
				            }
					        else{
				                $where .=  ' AND a_id IN(select r_b from {pre}art_relation where r_type=2 and r_a in('.$lp['topic'].'))';
				            }
					    }
			        }
		        }
		        
		        if(!isN($lp['letter'])){
		        	$where .= ' and a_letter=\'' . $lp['letter'] . '\'';
		        }
				if(!isN($lp['day'])){
					$symbol='';
					if(strpos(','.$lp['day'],'+')){ 
						$symbol='+'; 
					}
					elseif(strpos(','.$lp['day'],'-')==false){
						$symbol='-'; 
					}
					$todaydate = date('Y-m-d');
					$tommdate = date('Y-m-d',strtotime($symbol.$lp['day'].' day'));
					$tommunix = strtotime($tommdate);
					$todayunix = $tommunix + 86400;
					$where .= ' AND ( a_time>'. $tommunix . ' AND a_time<'.$todayunix.') ' ;
				}
				
				if(!isN($lp['days'])){
					$symbol='-';
					if(strpos(','.$lp['days'],'+')){ $symbol='+'; }
					$todaydate = date('Y-m-d');
					$tommdate = date('Y-m-d',strtotime($symbol.$lp['days'].' day'));
					$todayunix = strtotime($todaydate);
					$tommunix = strtotime($tommdate);
					$where .= ' AND a_time>'. $tommunix;
				}
				if(!isN($lp['id'])){ 
		        	$where .= ' AND a_id IN(' . $lp['id'] . ') ';
		        }
		        if (!isN($lp['tag'])){
		        	$where .= ' AND instr(a_tag,\''.$lp['tag'].'\')>0  ';
		        }
		        
				break;
		}
		$this->L = $lp;
		unset($lp);
		
		$this->sql = 'SELECT '.$col.' FROM {pre}'.$tb . ' WHERE 1=1 ' . $where . $order . $limit ;
		$this->sql1 = 'SELECT count(*) FROM {pre}'.$tb . ' WHERE 1=1 ' . $where ;
		
		if($this->markname=='art'){
			//echo $this->sql.'<br>';
			//exit;
		}
	}
    
    
    
    function ifex()
    {
        if (!strpos(",".$this->H,"{if-")) { return; }
		$labelRule = buildregx('{if-([\s\S]*?):([\s\S]+?)}([\s\S]*?){endif-\1}',"is");
		preg_match_all($labelRule,$this->H,$iar);
		
		$arlen=count($iar[2]);
		
		for($m=0;$m<$arlen;$m++){
			$strn = $iar[1][$m];
			$strif= asp2phpif( $iar[2][$m] ) ;
			$strThen= $iar[3][$m];
			$elseifFlag=false;
			
			$labelRule2="{elseif-".$strn."";
			$labelRule3="{else-".$strn."}";
			
			if (strpos(",".$strThen,$labelRule2)>0){
				$elseifArray=explode($labelRule2,$strThen);
				$elseifArrayLen=count($elseifArray);
				$elseifSubArray=explode($labelRule3,$elseifArray[$elseifArrayLen-1]);
				$resultStr=$elseifSubArray[1];
				@eval("if($strif){\$resultStr='$elseifArray[0]';\$elseifFlag=true;}");
				if(!$elseifFlag){
					for($elseifLen=1;$elseifLen<$elseifArrayLen-1;$elseifLen++){
						$strElseif=getSubStrByFromAndEnd($elseifArray[$elseifLen],":","}","");
						$strElseif=asp2phpif($strElseif);
						$strElseifThen=getSubStrByFromAndEnd($elseifArray[$elseifLen],"}","","start");
						$strElseifThen=str_replace("'","\'",$strElseifThen);
						@eval("if($strElseif){\$resultStr='$strElseifThen'; \$elseifFlag=true;}");
						if ($elseifFlag) {break;}
					}
				}
				if(!$elseifFlag){
					$strElseif0=getSubStrByFromAndEnd($elseifSubArray[0],":","}","");
					$strElseif0=asp2phpif($strElseif0);
					$strElseifThen0=getSubStrByFromAndEnd($elseifSubArray[0],"}","","start");
					$strElseifThen0=str_replace("'","\'",$strElseifThen0);
					@eval("if($strElseif0){\$resultStr='$strElseifThen0';\$elseifFlag=true;}");
				}
				$this->H=str_replace($iar[0][$m],$resultStr,$this->H);
			}
			else{
				$ifFlag = false;
				if (strpos(",".$strThen,$labelRule3)>0){
					$elsearray=explode($labelRule3,$strThen);
					$strThen1=$elsearray[0];
					$strElse1=$elsearray[1];
					@eval("if($strif){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $this->H=str_replace($iar[0][$m],$strThen1,$this->H);} else {$this->H=str_replace($iar[0][$m],$strElse1,$this->H);}
				}
				else{
					@eval("if($strif){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $this->H=str_replace($iar[0][$m],$strThen,$this->H);} else { $this->H=str_replace($iar[0][$m],"",$this->H); }
				 }
			}
		}
		unset($elsearray);
		unset($elseifArray);
		unset($iar);
		if (strpos(",".$this->H,"{if-")) { $this->ifex(); }
    }
    
    function mark()
    {
        $this->headfoot();
        $this->labelload();
        $this->labellink();
        $this->base();
        $this->matrix();
        
       	$this->H = str_replace('{maccms:sitetid}', $this->P['sitetid'] ,$this->H);
       	$this->H = str_replace('{maccms:siteid}', $this->P['siteid'] ,$this->H);
       	
        $labelRule = buildregx('{maccms:([\S]+)\s+(.*?)}([\s\S]+?){/maccms:\1}',"");
		preg_match_all($labelRule ,$this->H,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
			$this->markval = $matches1[0][$i];
            $this->markname = $matches1[1][$i];
            $this->markpar = $matches1[2][$i];
            $this->markdes = $matches1[3][$i];
            $this->mark_sql();
            
            switch($this->markname)
            {
            	case "php":
            		$this->runphp();
            		break;
            	case "area":
                case "lang":
                case "year":
                case "letter":
                case "tag":
                	$this->expandlist();
                	break;
                case "menu":
                case "class":
                case "art":
                case "vod":
                case "topic":
                case "link":
                case "gbook":
                case "comment":
                	$this->datalist();
                	break;
            }
    	}
        unset($matches1);
        replaceTplCustom();
        if($GLOBALS['MAC']['app']['compress']==1){
			$this->H = compress_html($this->H);
		}
    }
    
	function base()
    {
    	$colarr = array("{maccms:url}","{maccms:path}","{maccms:path_tpl}",'{maccms:path_ads}',"{maccms:name}","{maccms:keywords}","{maccms:description}","{maccms:icp}","{maccms:qq}","{maccms:email}","{maccms:siteaid}","{maccms:curvodtypeid}","{maccms:curvodtypepid}","{maccms:curvodtopicid}","{maccms:curarttypeid}","{maccms:curarttypepid}","{maccms:curarttopicid}","{maccms:userid}","{maccms:username}","{maccms:usergroupid}","{maccms:desktop}","{maccms:visits}",'{maccms:date}','{maccms:suffix}');
    	$valarr = array($GLOBALS['MAC']['site']['url'],MAC_PATH,MAC_PATH."template/" .$GLOBALS['MAC']['site']['templatedir']."/",MAC_PATH."template/" .$GLOBALS['MAC']['site']['templatedir'].'/'.$GLOBALS['MAC']['site']['adsdir']."/",$GLOBALS['MAC']['site']['name'],$GLOBALS['MAC']['site']['keywords'],$GLOBALS['MAC']['site']['description'],$GLOBALS['MAC']['site']['icp'],$GLOBALS['MAC']['site']['qq'],$GLOBALS['MAC']['site']['email'],$this->P["siteaid"],$this->P["vodtypeid"],$this->P["vodtypepid"],$this->P["vodtopicid"],$this->P["arttypeid"],$this->P["arttypepid"],$this->P["arttopicid"],$_SESSION["userid"],$_SESSION["username"],$_SESSION["usergroup"],"<a href=\"javascript:void(0)\" onclick=\"desktop('');return false;\"/>保存到桌面</a>","<script src=\"".MAC_PATH."js/tj.js\"></script>",date('Y-m-d',time()),$GLOBALS['MAC']['app']['suffix']);
    	
    	
    	array_push($colarr,'{maccms:link_index}');array_push($valarr,MAC_PATH);
    	array_push($colarr,'{maccms:link_index_art}');array_push($valarr,$this->getLink('art','index','',''));
    	
    	array_push($colarr,'{maccms:link_map_vod}');array_push($valarr,$this->getLink('vod','map','',''));
    	array_push($colarr,'{maccms:link_map_art}');array_push($valarr,$this->getLink('art','map','',''));
    	
    	array_push($colarr,'{maccms:link_topic_vod}');array_push($valarr,$this->getLink('vod','topicindex','',''));
    	array_push($colarr,'{maccms:link_topic_art}');array_push($valarr,$this->getLink('art','topicindex','',''));
    	
    	array_push($colarr,'{maccms:link_search_vod}');array_push($valarr,MAC_PATH."index.php?m=vod-search");
    	array_push($colarr,'{maccms:link_search_art}');array_push($valarr,MAC_PATH."index.php?m=art-search");
    	
    	array_push($colarr,'{maccms:link_gbook}');array_push($valarr,$this->getLink('','gbook','',''));
    	
    	array_push($colarr,'{maccms:link_map_rss}');array_push($valarr,$this->getLink('','rss','',''));
    	array_push($colarr,'{maccms:link_map_baidu}');array_push($valarr,$this->getLink('','baidu','',''));
    	array_push($colarr,'{maccms:link_map_google}');array_push($valarr,$this->getLink('','google','',''));
    	
        if(strpos($this->H,"{maccms:count_vod_all}")){ array_push($colarr,'{maccms:count_vod_all}');array_push($valarr,$this->getDataCount('vod',"all")); }
		if(strpos($this->H,"{maccms:count_vod_day}")){ array_push($colarr,'{maccms:count_vod_day}');array_push($valarr,$this->getDataCount('vod',"day")); }
		if(strpos($this->H,"{maccms:count_art_all}")){ array_push($colarr,'{maccms:count_art_all}');array_push($valarr,$this->getDataCount('art',"all")); }
		if(strpos($this->H,"{maccms:count_art_day}")){ array_push($colarr,'{maccms:count_art_day}');array_push($valarr,$this->getDataCount('art',"day")); }
        if(strpos($this->H,"{maccms:count_user_all}")){ array_push($colarr,'{maccms:count_user_all}');array_push($valarr,$this->getDataCount('user',"all")); }
        if(strpos($this->H,"{maccms:count_user_day}")){ array_push($colarr,'{maccms:count_user_day}');array_push($valarr,$this->getDataCount('user',"day")); }
        
        $this->H = str_replace($colarr,$valarr,$this->H);
    	unset($colarr);
    	unset($valarr);
    }
    
    function headfoot()
    {
    	$this->H = str_replace("{maccms:include}", loadFile(MAC_ROOT_TEMPLATE . "/home_include.html"),$this->H);
        $this->H = str_replace("{maccms:head}", loadFile(MAC_ROOT_TEMPLATE . "/home_head.html"),$this->H);
        $this->H = str_replace("{maccms:foot}", loadFile(MAC_ROOT_TEMPLATE . "/home_foot.html"),$this->H);
    }
    
    function labelload()
    {	
    	$labelRule = buildregx("{maccms:load([\s\S]*?)}","");
		preg_match_all($labelRule ,$this->H,$matches1);
		
		for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->H = str_replace($matches1[0][$i], loadFile(MAC_ROOT_TEMPLATE."/label_".trim($markpar)) ,$this->H);
        }
        unset($matches1);
    }
    
    function labellink()
    {
    	$labelRule = buildregx("{maccms:getlink([\s\S]*?)}","");
		preg_match_all($labelRule ,$this->H,$matches1);
		
		for($i=0;$i<count($matches1[0]);$i++)
		{
            $markpar = $matches1[1][$i];
            $this->H = str_replace($matches1[0][$i], $this->getLink($markpar,'label','','') ,$this->H);
        }
        unset($matches1);
    }
    
    function runphp()
    {
    	$resval='';
    	if($this->L['type']=='file'){
    		@include(MAC_ROOT.'/'.$this->L['file']);
    	}
    	else{
    		$phpcode = $this->markdes;
    		$phpcode = @preg_replace("/'@me'|\"@me\"|@me/i", '$resval', $phpcode);
    		@eval($phpcode);
    	}
    	$this->H = str_replace($this->markval,$resval,$this->H);
    }
    
    function expandlist()
    {
		$markhtml='';
		$l = str_replace('list','',$this->markname);
		$fn = '?m=vod-search';
		$t=0;
		switch($l)
		{
			case 'area':
				$str = "-area-{area}";
				$arr = explode(',',$GLOBALS['MAC']['app']['area']);
				break;
			case 'lang':
				$str = "-lang-{lang}";
				$arr = explode(',',$GLOBALS['MAC']['app']['lang']);
				break;
			case 'year':
				$str = "-year-{year}";
				if(!isNum($this->L['start'])){ $this->L['start']=2000; } else { $this->L['start']=intval($this->L['start']); }
				if(!isNum($this->L['end'])){ $this->L['end']=date('Y',time()); }else { $this->L['end']=intval($this->L['end']); }
				$arr=array();
				for($i=$this->L['start']+1;$i<=$this->L['end'];$i++){
					array_push($arr,$i);
				}
				break;
			case 'letter':
				$str = "-letter-{letter}";
				$arr = explode(",","A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z");
				break;
			case 'tag':
				$str = "-tag-{tag}";
				if($this->L['table']=='art'){$fn = '?m=art-search'; }
				$arr = explode(",",$this->L['tag']);
				break;
		}
		
		if($this->L['type']=="auto"){
			$an = $this->P['vodtypeid']>0 ? 'list' : 'search';
	    	$alink = $this->getLink('vod',$an,$this->T,array($l=>'{'.$l.'}'));
		}
		else{
	    	$alink = MAC_PATH. $fn. $str.".html";
		}
		
		$labelRule = buildregx("\[".$l.":\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->markdes,$matches2);
		
		foreach($arr as $v)
		{
		   	$num++;
		   	$marktemp = $this->markdes;
			for($j=0;$j<count($matches2[0]);$j++)
			{
				
				switch($matches2[1][$j])
				{
					case "num":
						$marktemp = str_replace($matches2[0][$j], $num,$marktemp);
						break;
					case "name":
						$marktemp = str_replace($matches2[0][$j], $v,$marktemp);
						break;
					case "link":
						$marktemp = str_replace($matches2[0][$j], str_replace("{".$l."}", urlencode($v),$alink),$marktemp );
						break;
				}
			}
			if ($this->L['order'] == "desc"){
				$markhtml = $marktemp . $markhtml;
			}
			else{
				$markhtml .= $marktemp;
			}
		}
		unset($matches2);
		$this->H = str_replace($this->markval, $markhtml,$this->H);
    }
    
    function matrix()
    {
    	global $db;
    	
    	$labelRule = buildregx("{maccms:matrix([\s\S]*?)}([\s\S]*?){/maccms:matrix}","");
		preg_match_all($labelRule ,$this->H,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
			$markhtml='';
			$num=0;
			$this->markname = 'matrix';
			$this->markval = $matches1[0][$i];
            $this->markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->mark_sql();
            
            $rs = $db->query($this->sql);
			if(!$rs){
				$markhtml = 'matrix标签出错!';
			}
			else{
				$labelRule = buildregx("\[matrix:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
				preg_match_all($labelRule ,$this->markdes,$matches2);
				while ($row = $db ->fetch_array($rs))
				{
					if($js>=$this->L['start']){
			        	$num++;
			        	$marktemp = $this->markdes;
			        	for($j=0;$j<count($matches2[0]);$j++)
						{
							$marktemp = $this->parse("menu", $marktemp, $matches2[0][$j], $matches2[1][$j], $matches2[2][$j], $row, $num);
						}
			        	$markhtml .= $marktemp;
					}
					$js++;
				}
			}
		    unset($rs);
		    $this->H = str_replace($this->markval, $markhtml,$this->H);
	        unset($matches2);
        }
        unset($matches1);
    }
    
    function datalist()
    {
    	global $db;
    	$markhtml='';
		
		if(!empty($this->L['pagesize'])){
			if($this->P['make']){ return; }
			if($this->P['dp']){ return; }
			$this->P['pagesize'] = $this->L['pagesize'];
			if(empty($this->P['datacount'])) { $this->P['datacount'] = $db->getOne($this->sql1); }
		}
		$rs = $db->query($this->sql);
		if(!$rs){
			$markhtml = $this->markname. '标签出错!';
		}
		else{
			
			$labelRule = buildregx("\[".$this->markname.":\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
			preg_match_all($labelRule ,$this->markdes,$matches2);
			
			while ($row = $db->fetch_array($rs))
			{
				if($js>=$this->L['start']){
					$num++;
					$marktemp = $this->markdes;
					for($j=0;$j<count($matches2[0]);$j++)
					{
						$marktemp = $this->parse($this->markname,$marktemp,$matches2[0][$j],$matches2[1][$j],$matches2[3][$j],$row,$num);
					} 
					$markhtml .= $marktemp;
				}
				$js++;
			}
		    unset($matches2);
		}
		unset($rs);
	    $this->H = str_replace($this->markval, $markhtml,$this->H);
    }
    
    function parse($f, $mdes, $m1, $m2, $m3, $mrs, $mnum)
    {
        if($mnum<10){ $numfill="0".$mnum; } else{ $numfill=$mnum;}
		$val=$m1;
        switch($f)
        {
            case "menu":
                switch($m2)
            	{
                    case "num": $val=$mnum; break;
                    case "numfill": $val=$numfill; break;
                    case "id": $val=$mrs["t_id"];break;
                    case "name": $val=getTextt($m3, $mrs["t_name"]);break;
                    case "enname": $val=getTextt($m3, $mrs["t_enname"]);break;
                    case "pid": $val=$mrs["t_pid"];break;
                    case "title": $val=$mrs["t_title"];break;
                    case "key": $val=$mrs["t_key"];break;
                    case "des": $val=$mrs["t_des"];break;
                    case "link":
                        if($this->L['type']=='auto'){
                        	$an = $this->P['vodtypeid']>0 ? 'list' : 'search';
                        	$col = $this->P['vodtypeid']>0 ? 'id' : 'typeid';
                        	$val=$this->getLink($this->L['table'],$an,$this->T,array($col=>$mrs['t_id']));
                    	}
                        else{
                        	$val=$this->getLink($this->L['table'],'type',$mrs,$row);
                        }
                        break;
                    case "count":
                        if ($this->L['table'] == "art"){
                        	$typearr = $GLOBALS['MAC_CACHE']['arttype'][$mrs["t_id"]];
							$val = $this->getDataCount('art', " and a_type in (" . $typearr["childids"] . ")" );
                    	}
                        else{
                        	$typearr = $GLOBALS['MAC_CACHE']['vodtype'][$mrs["t_id"]];
							$val = $this->getDataCount('vod', " and d_type in (" . $typearr["childids"] . ")" );
                        }
                        break;
            	}
            	break;
            case "class":
				switch($m2)
            	{
                    case "num": $val=$mnum; break;
                    case "numfill": $val=$numfill; break;
                    case "id": $val=$mrs["c_id"];break;
                    case "name": $val=getTextt($m3, $mrs["c_name"]);break;
                    case "enname": $val=getTextt($m3, $mrs["c_enname"]);break;
                    case "pid": $val=$mrs["c_pid"];break;
                    case "link":
                        if($this->L['type']=='auto'){
                        	$an = $this->P['vodtypeid']>0 ? 'list' : 'search';
                        	$col = $this->P['vodtypeid']>0 ? 'class' : 'classid';
                        	$val=$this->getLink('vod',$an,$this->T,array($col=>$mrs['c_id']));
                    	}
                        else{
                        	$val=$this->getLink('vod','search',$mrs,array('classid'=>$mrs['c_id']));
                        }
                        break;
            	}
            	break;
            case "topic":
            	switch($m2)
				{
                    case "num": $val=$mnum;break;
                    case "numfill": $val=$numfill;break;
                    case "id": $val=$mrs["t_id"];break;
                    case "name": $val=getTextt($m3, $mrs["t_name"]);break;
                    case "enname": $val=getTextt($m3, $mrs["t_enname"]);break;
                    case "sort": $val=$mrs["t_sort"];break;
                    case "title": $val=$mrs["t_title"];break;
                    case "key": $val=$mrs["t_key"];break;
                    case "des": $val=$mrs["t_des"];break;
                    case "addtime": $val=getDatet($m3, $mrs["t_addtime"]);break;
                    case "time": $val=getDatet($m3, $mrs["t_time"]);break;
                    case "level": $val=$mrs["t_level"];break;
                    case "hits": $val=$mrs["t_hits"];break;
                    case "dayhits": $val=$mrs["t_dayhits"];break;
                    case "weekhits": $val=$mrs["t_weekhits"];break;
                    case "monthhits": $val=$mrs["t_monthhits"];break;
                    case "content": $val=getTextt($m3, $mrs["t_content"]);break;
                    case "contenttext":$val=getTextt($m3, strip_tags($mrs["t_content"]));break;
                    case "remarks": $val=getTextt($m3, $mrs["t_remarks"]);break;
                    case "pic":
                    	$val = $mrs["t_pic"];
                    	if(strpos(",".$val,"http://")<=0){
                    		if($GLOBALS['MAC']['upload']['remote']==1){ $val = $GLOBALS['MAC']['upload']['remoteurl'].$val; } else { $val= MAC_PATH.$val; }
                    	}
                    	break;
                    case "count":
                        $val = $this->getDataCount($this->L['table'].'_relation', " and r_type=2 and r_a=".$mrs["t_id"] );
	                    break;
                    case "link": $val=$this->getLink($this->L['table'],'topic',$mrs,$row);break;
                }
            	break;
            case "link":
            	switch($m2)
            	{
            		case "num": $val=$mnum;break;
				    case "numfill": $val=$numfill;break;
                    case "id": $val=$mrs["l_id"];break;
                    case "name": $val=getTextt($m3, $mrs["l_name"]);break;
                    case "type": $val= $mrs["l_type"]==1 ? "图片": "文字";break;
                    case "link": $val=$mrs["l_url"];break;
                    case "pic": $val=$mrs["l_logo"];break;
            	}
                break;
            case "gbook":
            	$bgcolorArr=array("D66203","513DBD","784E1A","C55200","DA6912","537752","C58200","519DBD","D60103","531752");
            	$reg2 = '~(\d+)\.(\d+)\.(\d+)\.(\d+)~';
            	switch($m2)
            	{
	            	case "num": $val=$mnum;break;
					case "numfill": $val=$numfill;break;
	            	case "id":$val=$mrs["g_id"];break;
	            	case "name": $val=getTextt($m3, $mrs["g_name"]);break;
	            	case "content": $val=regReplace(getTextt($m3, $mrs["g_content"]), "\[em:(\d{1,})?\]", "<img src=\"".MAC_PATH."images/face/$1.gif\" border=0/>"); ;break;
	            	case "reply": $val=getTextt($m3, $mrs["g_reply"]);break;
	            	case "ip": $val= preg_replace($reg2, "$1.$2.*.*", long2ip($mrs["g_ip"]));;break;
	            	case "time": $val=getDatet($m3, $mrs["g_time"]);break;
	            	case "replytime": $val=getDatet($m3, $mrs["g_replytime"]);break;
	            	case "color": $val='#'.$bgcolorArr[rand(1,9)];break;
            	}
            	break;
            case "comment":
            	$bgcolorArr=array("D66203","513DBD","784E1A","C55200","DA6912","537752","C58200","519DBD","D60103","531752");
            	$reg2 = '~(\d+)\.(\d+)\.(\d+)\.(\d+)~';
            	switch($m2)
            	{
	            	case "num": $val=$mnum;break;
					case "numfill": $val=$numfill;break;
	            	case "id":$val=$mrs["c_id"];break;
	            	case "name": $val=getTextt($m3, $mrs["c_name"]);break;
	            	case "content": $val=regReplace(getTextt($m3, $mrs["c_content"]), "\[em:(\d{1,})?\]", "<img src=\"".MAC_PATH."images/face/$1.gif\" border=0/>"); ;break;
	            	case "ip": $val= preg_replace($reg2, "$1.$2.*.*", long2ip($mrs["c_ip"]));;break;
	            	case "time": $val=getDatet($m3, $mrs["c_time"]);break;
	            	case "color": $val='#'.$bgcolorArr[rand(1,9)];break;
            	}
            	break;
            case "vod":
                $typearr = $GLOBALS['MAC_CACHE']['vodtype'][$mrs["d_type"]];
                if (!is_array($typearr)){ return; }
                $tp = $GLOBALS['MAC_CACHE']['vodtype'][$typearr["t_pid"]];
                
                switch($m2)
                {
                    case "num": $val=$mnum;break;
                    case "numfill": $val=$numfill;break;
                    case "numjoin": $val=$this->L['start']+$mnum;if($val<10){ $val="0".$val; } break;
                    case "id": $val=$mrs["d_id"];break;
                    case "name": $val=getTextt($m3, $mrs["d_name"]);break;
                    case "encodename": $val=urlencode($mrs["d_name"]);break;
                    case "colorname":
                    	$val = getTextt($m3, $mrs["d_name"]);
                    	if(!empty($mrs["d_color"])){
                        	$val = "<font color=".$mrs["d_color"].">".$val."</font>";
                        }
                    	break;
                    case "subname": $val=getTextt($m3, $mrs["d_subname"]);break;
                    case "enname": $val=getTextt($m3, $mrs["d_enname"]);break;
                    case "ennamelink": $val=getKeysLink($mrs["d_enname"], "pinyin");break;
                    case "state": $val=$mrs["d_state"];break;
                    case "color": $val=$mrs["d_color"];break;
                    case "pic":
                        $val = $mrs["d_pic"];
                        if(strpos(",".$val,"http://")<=0){
                        	if($GLOBALS['MAC']['upload']['remote']==1){ $val = $GLOBALS['MAC']['upload']['remoteurl'].$val; }else { $val= MAC_PATH.$val; }
                        }
                        break;
                    case "picthumb":
                        $val = $mrs["d_picthumb"];
                        if(strpos(",".$val,"http://")<=0){
                        	if($GLOBALS['MAC']['upload']['remote']==1){ $val = $GLOBALS['MAC']['upload']['remoteurl'].$val; }else { $val= MAC_PATH.$val; }
                        }
                        break;
                    case "picslide":
                    	$val = $mrs["d_picslide"];
                        if(strpos(",".$val,"http://")<=0){
                        	if($GLOBALS['MAC']['upload']['remote']==1){ $val = $GLOBALS['MAC']['upload']['remoteurl'].$val; }else { $val= MAC_PATH.$val; }
                        }
                        break;
                    case "tag" : $val=getTextt($m3, $mrs["d_tag"]);break;
                    case "taglink": $val=getKeysLink($mrs["d_tag"], "tag");break;
                    case "starring": $val=getTextt($m3, $mrs["d_starring"]);break;
                    case "starringlink": $val=getKeysLink($mrs["d_starring"], "starring");break;
                    case "directed":  $val=getTextt($m3, $mrs["d_directed"]);break;
                    case "directedlink": $val=getKeysLink($mrs["d_directed"], "directed");break;
                    case "area": $val=$mrs["d_area"];break;
                    case "arealink": $val=getKeysLink($mrs["d_area"], "area");break;
                    case "year": $val=$mrs["d_year"]==0?'未知':$mrs["d_year"];break;
                    case "yearlink": $val=getKeysLink($mrs["d_year"]==0?'未知':$mrs["d_year"], "year");break;
                    case "lang": $val=$mrs["d_lang"];break;
                    case "langlink": $val=getKeysLink($mrs["d_lang"], "lang");break;
                    case "level": $val=$mrs["d_level"];break;
                    case "stint": $val=$mrs["d_stint"];break;
                    case "stintdown": $val=$mrs["d_stintdown"];break;
                    case "hits": $val=$mrs["d_hits"];break;
                    case "dayhits": $val=$mrs["d_dayhits"];break;
                    case "weekhits": $val=$mrs["d_weekhits"];break;
                    case "monthhits": $val=$mrs["d_monthhits"];break;
                    case "content": $val=getTextt($m3, $mrs["d_content"]);break;
                    case "contenttext":$val=getTextt($m3, strip_tags($mrs["d_content"]));break;
                    case "remarks": $val=getTextt($m3, $mrs["d_remarks"]);break;
                    case "up": $val=$mrs["d_up"];break;
                    case "down": $val=$mrs["d_down"];break;
                    case "score": $val=$mrs["d_score"];break;
                    case "scoreall": $val=$mrs["d_scoreall"];break;
                    case "scorenum": $val=$mrs["d_scorenum"];break;
                    case "duration": $val=$mrs["d_duration"];break;
                    case "addtime": $val=getDatet($m3, $mrs["d_addtime"]);break;
                    case "time": $val=getDatet($m3, $mrs["d_time"]);break;
                    case "from": $val=getVodXmlText("vodplay","play",$mrs["d_playfrom"]);break;
                    case "fromdown": $val=getVodXmlText("voddown","down",$mrs["d_downfrom"]);break;
                    case "link": $val=$this->getLink('vod','detail',$typearr,$mrs);break;
                    case "playlink": $val=str_replace(array('{src}','{num}'),array('1','1'),$this->getLink('vod','play',$typearr,$mrs));break;
                    case "playlinks":
                    	$val='';
                    	$arr = explode('$$$',$mrs['d_playfrom']);
                    	$cc = count($arr);
                    	if($cc==0) { break; }
                    	$xmlarr = $GLOBALS['MAC_CACHE']['vodplay'];
                    	$url = $this->getLink('vod','play',$typearr,$mrs);
                    	for($i=0;$i<$cc;$i++){
                    		$show = $xmlarr[$arr[$i]]['show'];
                    		$lnk = str_replace(array('{src}','{num}'),array($i+1,'1'),$url);
                    		$val.= '<a href="'.$lnk.'" target="_blank" class="playlink_'.$arr[$i].'">'.$show.'</a> ';
                    	}
                    	unset($arr,$xmlarr);
                    	break;
                    case "downlink": $val=str_replace(array('{src}','{num}'),array('1','1'),$this->getLink('vod','down',$typearr,$mrs));break;
                    case "downlinks":
                    	$val='';
                    	$arr = explode('$$$',$mrs['d_downfrom']);
                    	$cc = count($arr);
                    	if($cc==0) { break; }
                    	$xmlarr = $GLOBALS['MAC_CACHE']['voddown'];
                    	$url = $this->getLink('vod','down',$typearr,$mrs);
                    	for($i=0;$i<$cc;$i++){
                    		$show = $xmlarr[$arr[$i]]['show'];
                    		$lnk = str_replace(array('{src}','{num}'),array($i+1,'1'),$url);
                    		$val.= '<a href="'.$lnk.'" target="_blank" class="downlink_'.$arr[$i].'">'.$show.'</a> ';
                    	}
                    	unset($arr,$xmlarr);
                    	break;
                    case "type": $val=$mrs["d_type"];break;
                    case "typepid": $val=$typearr["t_pid"];break;
                    case "typeplink": $val=$this->getLink('vod','type',$tp,$mrs);break;
                    case "typepname": $val=$tp["t_name"];break;
                    case "typepenname": $val=$tp["t_enname"];break;
                    case "typepkey": $val=$tp["t_key"];break;
                   	case "typepdes": $val=$tp["t_des"];break;
                    case "typelink": $val=$this->getLink('vod','type',$typearr,$mrs);break;
                    case "typename": $val=$typearr["t_name"];break;
                    case "typeenname": $val=$typearr["t_enname"];break;
                    case "typekey": $val=$typearr["t_key"];break;
                   	case "typedes": $val=$typearr["t_des"];break;
                   	case "typetitle": $val=$typearr["t_title"];break;
                   	case "typeexpandlink" :$val='';break;
					case "classlink" :
                   		$val='';
                   		
                   		if(!empty($mrs['d_class'])){
							$rc=false;
                			$ids = explode(',',$mrs['d_class']);
                			foreach($ids as $a){
                				if(!empty($a)){
                					$arr = $GLOBALS['MAC_CACHE']['vodclass'][$a];
                					$mrs['id']=$typearr['t_id'];
                					$mrs['class']=$arr['c_id'];
                					$lnk = $this->getLink('vod','list',$typearr,$mrs);
                					if($rc){ $val.='&nbsp;'; }
                					$val .="<a target='_blank' href='".$lnk."' />".$arr['c_name']."</a>";
                					$rc=true;
                				}
                			}
                			unset($ids);
                		}
						break;
					case "topiclink":
						$val='';
						if(!empty($mrs['d_topic'])){
							$rc=false;
                			$ids = explode(',',$mrs['d_topic']);
                			foreach($ids as $a){
                				if(!empty($a)){
                					$arr = $GLOBALS['MAC_CACHE']['vodtopic'][$a];
                					$lnk = $this->getLink('vod','topic',$arr,$mrs);
                					if($rc){ $val.='&nbsp;'; }
                					$val .="<a target='_blank' href='".$lnk."' />".$arr['t_name']."</a>";
                					$rc=true;
                				}
                			}
                			unset($ids);
                		}
						break;
                    case "userfav":$val="<a href=\"javascript:void(0)\" onclick=\"MAC.UserFav('".$mrs["d_id"]."');return false;\"/>会员收藏</a>";break;
                    default:
                    	$val=$m1;break;
        		}
            	break;
            case "art":
                $typearr = $GLOBALS['MAC_CACHE']['arttype'][$mrs["a_type"]];
                if (!is_array($typearr)){ return; }
                $tp = $GLOBALS['MAC_CACHE']['arttype'][$typearr["t_pid"]];
                
                switch($m2)
				{
                    case "num": $val=$mnum;break;
                    case "numfill": $val=$numfill;break;
                    case "numjoin": $val=$this->P['start']+$mnum;break;
                    case "id": $val=$mrs["a_id"];break;
                    case "name": $val=getTextt($m3, $mrs["a_name"]);break;
                    case "encodename": $val=urlencode($mrs["a_name"]);break;
                    case "colorname":
                    	$val=getTextt($m3, $mrs["a_name"]);
                    	if(!empty($mrs["a_color"])){
                    		$val = "<font color=".$mrs["a_color"].">".$val."</font>";
                        }
                    	break;
                    case "subname": $val=getTextt($m3, $mrs["a_subname"]);break;
                    case "enname": $val=getTextt($m3, $mrs["a_enname"]);break;
                    case "from": $val=getTextt($m3, $mrs["a_from"]);break;
                    case "remarks": $val=getTextt($m3, $mrs["a_remarks"]);break;
                    case "tag" : $val=getTextt($m3, $mrs["a_tag"]);break;
                    case "taglink": $val=getKeysLink($mrs["a_tag"], "tag");break;
                    case "content":
                    	$val = $mrs["a_content"];
                    	if ($this->P['pagetype']=="detail"){ $val = $this->P['content']; }
                    	$val = getTextt($m3, $val);
                    	break;
                    case "contenttext":
                    	$val = $mrs["a_content"];
                    	if ($this->P['pagetype']=="detail"){ $val = $this->P['content']; }
                    	$val = strip_tags($val);
                    	$val = getTextt($m3, strip_tags($val));
                    	break;
                    case "author": $val=getTextt($m3, $mrs["a_author"]);break;
                    case "color": $val=$mrs["a_color"];break;
                    case "hits": $val=$mrs["a_hits"];break;
                    case "dayhits": $val=$mrs["a_dayhits"];break;
                    case "weekhits": $val=$mrs["a_weekhits"];break;
                    case "monthhits": $val=$mrs["a_monthhits"];break;
                    case "addtime": $val=getDatet($m3, $mrs["a_addtime"]);break;
                    case "time": $val=getDatet($m3, $mrs["a_time"]);break;
                    case "pic":
                    	$val = $mrs["a_pic"];
                        if(strpos(",".$val,"http://")<=0){
                        	if($GLOBALS['MAC']['upload']['remote']==1){ $val = $GLOBALS['MAC']['upload']['remoteurl'].$val; }else { $val= MAC_PATH.$val; }
                        }
                        break;
                    case "link": $val=$this->getLink('art','detail',$typearr,$mrs);break;
                    case "level": $val=$mrs["a_level"];break;
                    case "type": $val=$mrs["a_type"];break;
                    case "typepid": $val=$typearr["t_pid"];break;
                    case "typeplink": $val=$this->getLink('art','type',$tp,$mrs);break;
                    case "typepname": $val=$tp["t_name"];break;
                    case "typepenname": $val=$tp["t_enname"];break;
                    case "typepkey": $val=$tp["t_key"];break;
                   	case "typepdes": $val=$tp["t_des"];break;
                   	case "typeptitle": $val=$tp["t_title"];break;
                    case "typelink": $val=$this->getLink('art','type',$typearr,$mrs);break;
                    case "typename": $val=$typearr["t_name"];break;
                    case "typeenname": $val=$typearr["t_enname"];break;
                    case "typekey": $val=$typearr["t_key"];break;
                   	case "typedes": $val=$typearr["t_des"];break;
                   	case "typetitle": $val=$typearr["t_title"];break;
                    case "topiclink":
                    	$val='';
						if(!empty($mrs['a_topic'])){
							$rc=false;
                			$ids = explode(',',$mrs['a_topic']);
                			foreach($ids as $a){
                				if(!empty($a)){
                					$arr = $GLOBALS['MAC_CACHE']['arttopic'][$a];
                					$lnk = $this->getLink('art','topic',$arr,$mrs);
                					if($rc){ $val.='&nbsp;'; }
                					$val .="<a target='_blank' href='".$lnk."' />".$arr['t_name']."</a>";
                					$rc=true;
                				}
                			}
                			unset($ids);
                		}
					break;
					default:
                    	$val=$m1;break;
                }
            	break;
            default:
                break;
        }
        unset($typearr,$tp);
        $markstr = str_replace($m1, $val,$mdes);
        return $markstr;
    }
    
    function pageshow($ps='',$pl='')
	{
        $labelRule = buildregx("{maccms:pages([\s\S]*?)}","");
		preg_match_all($labelRule ,$this->H,$matches1);
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
            $markstr = $matches1[0][$i];
            $labelRule = buildregx("([a-z0-9]+)=([a-z0-9|,]+)","");
			preg_match_all($labelRule ,$markstr,$matches2);
			
            for($j=0;$j<count($matches2[0]);$j++)
			{
            	switch($matches2[1][$j])
            	{
            		case "len" : $pagenum = intval($matches2[2][$j]);break;
            		case "linktype" : $this->P['pagetype'] = $matches2[2][$j];break;
            	}
            }
            unset($matches2);
        }
        unset($matches1);
        
        
        if($this->P['pagesize']==0){ $this->P['pagesize']=1; }
        $this->P['pagecount'] = ceil($this->P['datacount']/$this->P['pagesize']);
        if ($this->P['pagecount']<1){ $this->P['pagecount']=1;}
        
        if(!empty($pl)){
        	$pageurl = $pl;
        }
        else{
        	if($this->P['pagetype']=='list' || $this->P['pagetype']=='search'){
        		$pageurl = $this->getLink($this->P['pageflag'],$this->P['pagetype'],$this->T,array('pg'=>'{pg}'),'{pg}');
        	}
        	else{
        		$pageurl = $this->getLink($this->P['pageflag'],$this->P['pagetype'],$this->T,$this->D,'{pg}');
        	}
        }
        
        $pages = '共'.$this->P['datacount'].'条数据&nbsp;当前:'.$this->P['pg'].'/'.$this->P['pagecount'].'页&nbsp;';
        if(empty($ps)){
        	$pagego = 'pagego(\''.$pageurl.'\','.$this->P['pagecount'].')';
        }
        else{
        	$tmpurl = str_replace('{url}',$pageurl,$ps);
        	$pageurl = 'javascript:void(0)" onclick="' . $tmpurl . '';
        	$pagego = $tmpurl;
        }
        $pages.=pageshow($this->P['pg'],$this->P['pagecount'],$pagenum,$pageurl,$pagego,$pl);
        $this->H = str_replace(array($markstr,'{page:now}','{page:datacount}','{page:size}','{page:count}'), array($pages,$this->P['pg'],$this->P['datacount'],$this->P['pagesize'],$this->P['pagecount']),$this->H);
    }
    
    function playdownlist($flag)
    {
    	if(strpos($this->H,"{maccms:".$flag."")){} else { return; }
    	if($flag=='play'){
    		$url = $this->D['d_playurl'];
    		$from = $this->D['d_playfrom'];
    		$server = $this->D['d_playserver'];
    		$note = $this->D['d_playnote'];
    		$path = 'play';
    	}
    	elseif($flag=='down'){
    		$url = $this->D['d_downurl'];
    		$from = $this->D['d_downfrom'];
    		$server = $this->D['d_downserver'];
    		$note = $this->D['d_downnote'];
    		$path = 'down';
    	}
    	$urlarrlen=0;
    	$fromarrlen=0;
    	$serverarrlen=0;
    	$notearrlen=0;
    	if(!empty($url)){ $urlarr = explode("$$$",$url); $urlarrlen = count($urlarr); }
    	if(!empty($from)){ $fromarr = explode("$$$",$from); $fromarrlen = count($fromarr); }
    	if(!empty($server)){ $serverarr = explode("$$$",$server); $serverarrlen = count($serverarr); }
    	if(!empty($note)){ $notearr = explode("$$$",$note); $notearrlen = count($notearr); }
    	$lnk = $this->getLink('vod',$flag,$this->T,$this->D);
    	
        $labelRule = buildregx("{maccms:".$flag."([\s\S]*?)}([\s\S]*?){/maccms:".$flag."}","");
		preg_match_all($labelRule ,$this->H,$matches1);
		
		
        for($i=0;$i<count($matches1[0]);$i++)
		{
			$this->L['type']="";
            $this->markpar = $matches1[1][$i];
            $this->markdes = $matches1[2][$i];
            $this->markval = $matches1[0][$i];
            $this->mark_sql();
            
            $markhtml="";
			$markslist1=array();
            $num=0;
            $oldsort=0;
            
            if($this->L['type'] =='mode2'){
            	
            }
            else{
	            for ($j=0;$j<$fromarrlen;$j++){
	                $num = $num + 1;
	                $fromrc=true;
	                $from = $fromarr[$j];
	                $note = $notearr[$j];
	                $xmlarr = $GLOBALS['MAC_CACHE']['vod'.$flag][$fromarr[$j]];
	                $show = $xmlarr['show'];
	                $des = $xmlarr['des'];
	                $sortt = $xmlarr['sort'];
	                $tip = $xmlarr['tip'];
	                unset($xmlarr);
	                
	                if ($serverarrlen >= $j){
	                	$xmlarr = $GLOBALS['MAC_CACHE']['vodserver'][$serverarr[$j]];
	                    $server = $xmlarr['show'];
	                    $serverdes = $xmlarr['des'];
	                    $serversortt = $xmlarr['sort'];
	                    $servertip = $xmlarr['tip'];
	                    unset($xmlarr);
	                }
	                
	                
	                if ($GLOBALS['MAC']['view']['vod'.$flag] !=3 && $this->L['from']=="current"){
		            	if ($num==$this->P["src"]){ $fromrc=true; } else { $fromrc=false; $markhtml=""; }
		            }
	                
	                if ($fromrc && $urlarrlen >= $j){
	                    $dzarr = explode("#",$urlarr[$j]);
	                    $dzarrlen = count($dzarr);
	                    $n=0;
	                    $labelRule = buildregx("{maccms:url([\s\S]*?)}([\s\S]*?){/maccms:url}","");
						preg_match_all($labelRule ,$this->markval,$matches3);
						
						if(count($matches3[0])==0){
							$markhtml =  $this->markdes;
						}
						else{
		                    for($k=0;$k<count($matches3[0]);$k++)
							{
								$markorder = $matches3[1][$k];
		                        $marktemp = $matches3[2][$k];
		                        $markslist2 = "";
		                        
								for ($m=0;$m<$dzarrlen;$m++){
									if (!empty($dzarr[$m])){
										$urlone = explode("$",$dzarr[$m]);
										if (count($urlone) == 2){
											$urlname = $urlone[0];
											$urlpath = $urlone[1];
										}
										else{
											$urlpath = $urlone[0];
											$urlname = "第" . ($m + 1)  ."集";
										}
										$url = str_replace(array('{src}','{num}'),array($j+1,$m+1),$lnk);
										$markstr = str_replace('[url:num]', $m+1 ,$marktemp);
										$markstr = str_replace(array('[url:name]','[url:path]','[url:link]'), array($urlname,$urlpath,$url),$markstr);
										if ( strpos($markorder,"desc")>0){ $markslist2 = $markstr . $markslist2; } else { $markslist2 .= $markstr; }
									}
								}
		                        
		                        if ($GLOBALS['MAC']['app']['isopen']==1){
		                        	$markslist2 = str_replace("target=\"_blank\"","target=\"_self\"",$markslist2);
		                        }
		                        if($n==0){
		                        	$markhtml = str_replace($matches3[0][$k] ,$markslist2,$this->markdes);
		                        }
		                        else{
		                        	$markhtml = str_replace( $matches3[0][$k] ,$markslist2,$markhtml);
		                        }
		                        $n++;
		                    }
		                    unset($matches3);
	                    }
	                }
	                
	                $markhtml = str_replace(array('['.$flag.':urlcount]','['.$flag.':count]','['.$flag.':num]','['.$flag.':from]','['.$flag.':show]','['.$flag.':des]','['.$flag.':sort]','['.$flag.':tip]','['.$flag.':server]','['.$flag.':serversort]','['.$flag.':serverurl]','['.$flag.':servertip]','['.$flag.':note]'), array($dzarrlen,$fromarrlen,$num,$from,$show,$des,$sortt,$tip,$server,$serversortt,$serverdes,$servertip,$note) ,$markhtml);
	                
	                
	                if($GLOBALS['MAC']['app']['playersort']==1){
	                	$markslist1[$sortt + ($fromarrlen-$num)] = $markhtml;
	                }
	                else{
	                	$markslist1[$num] = $markhtml ;
	                }
	            }
	            if($GLOBALS['MAC']['app']['playersort']==1){
	            	krsort($markslist1);
	            }
	        }
            unset($matches2);
            
            $playlisthtml = join("",$markslist1);
            $this->H = str_replace($this->markval, $playlisthtml,$this->H);
            
	    }
	    unset($markslist1);
	    unset($dzarr);
	    unset($urlone);
	    unset($urlarr);
	    unset($fromarr);
	    unset($serverarr);
	    unset($notearr);
	    unset($matches1);
    }
    
    function getTypeText($f)
    {
    	$res="";
    	$cp = $f.'list';
    	$cn = $f."_typetext_".$this->T['t_id'];
    	
		if (!chkCache($cp,$cn)){
			$typelink = $this->getLink($f,'type',$this->T,$row);
			$res = "<a href='". MAC_PATH ."'>首页</a>{typeplink}&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $typelink ."' >". $this->T['t_name'] ."</a>";
			if ($this->T['t_pid']>0){
				$typearr = $GLOBALS[$f.'type'][$this->T['t_pid']];
				$typeplink =  $this->getLink($f,'type',$typearr,$row);
				$typeplink = "&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $typeplink ."' >". $typearr["t_name"] ."</a>";
			}
			$res = str_replace('{typeplink}','',$res);
		}
		else{
			$res = getCache($cp,$cn);
		}
		return $res;
	}
    
    function getUrlInfo($flag)
	{
	    $res= "var mac_flag='".$flag."',mac_link='".$this->getLink('vod',$flag,$this->T,$this->D)."', mac_name='".str_replace("'","\'",$this->D['d_name'])."',mac_from='".$this->D['d_'.$flag.'from']."',mac_server='".$this->D['d_'.$flag.'server']."',mac_note='".$this->D['d_'.$flag.'note']."',mac_url={list}";
	    
	    $url = str_replace("'","\'",$this->D['d_'.$flag.'url']);
	    if ($GLOBALS['MAC']['app']['encrypt'] == 1){
	        $url = "unescape('".escape($url)."');";
	    }
	    else if ($GLOBALS['MAC']['app']['encrypt'] == 2){
	        $url = "unescape(base64decode('".base64_encode(escape($url))."'));";
	    }
	    else{
	        $url = "'".$url."';";
		}
	    return  str_replace('{list}',$url,$res);
	}
	
    function getUrlName($flag)
    {
    	$urlarr = explode("$$$",$this->D['d_'.$flag.'url']); $urlarrlen = count($urlarr);
    	$fromarr = explode("$$$",$this->D['d_'.$flag.'from']); $fromarrlen = count($fromarr);
	    //$serverarr = explode("$$$",$this->D['d_'.$flag.'server']); $serverarrlen = count($serverarr);
	    //$notearr = explode("$$$",$this->D['d_'.$flag.'note']); $notearrlen = count($notearr);
    	
    	if($flag=="down"){ $f="voddown"; $t="down"; } else { $f="vodplay"; $t="player"; }
    	$curfrom = $fromarr[$this->P['src']-1];
    	$xmlarr = $GLOBALS['MAC_CACHE']['vod'.$flag][$curfrom];
        $urlfromshow = $xmlarr['show'];
        unset($xmlarr);
        
    	$cururl = $urlarr[$this->P['src']-1];
    	$listarr = explode("#",$cururl);
    	$urlone = explode("$",$listarr[$this->P['num']-1]);
    	if (count($urlone)==2){
			$urlname = $urlone[0];
			$urlpath = $urlone[1];
		}
		else{
			$urlname = "第".$this->P['num']."集";
			$urlpath = $urlone[0];
		}
		
		$this->H = str_replace("[vod:".$flag."from]",$urlfrom,$this->H);
		$this->H = str_replace("[vod:".$flag."fromshow]",$urlfromshow,$this->H);
		$this->H = str_replace("[vod:".$flag."name]",$urlname,$this->H);
		$this->H = str_replace("[vod:".$flag."urlpath]",$urlpath,$this->H);
		
	    unset($urlarr);
	    unset($urlone);
	    unset($fromarr);
	    unset($urlarr);
    }
	
    function loadtype($flag)
    {
        if($this->P['make']!=true && chkCache($this->P['cp'],$this->P['cn'])){
            $this->H = getCache($this->P['cp'],$this->P['cn']);
        }
        else{
        	$this->P[$flag."typepid"] = $this->T['t_pid'];
            $this->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$this->T['t_tpl']);
            $this->H = str_replace(array('{page:id}','{page:name}','{page:enname}','{page:pid}','{page:key}','{page:des}','{page:title}','{page:link}','{page:textlink}'),array($this->T['t_id'],$this->T['t_name'],$this->T['t_enname'],$this->T['t_pid'],$this->T['t_key'],$this->T['t_des'],$this->T['t_title'],$this->getLink($flag,'type',$this->T,$row),$this->getTypeText($flag)),$this->H);
            $this->mark();
            setCache ($this->P['cp'],$this->P['cn'],$this->H);
        }
        
        $this->P['sitetid'] = $this->T['t_id'];
        if($this->P['make']!=true) { $this->H = str_replace('{page:now}',$this->P['pg'],$this->H); }
        if ($flag == 'art'){
        	
        }
        else{
			$linkbytime = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'time'));
			$linkbyhits = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'hits'));
			$linkbyscore = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'score'));
			$this->H = str_replace(array('{page:linkbytime}','{page:linkbyhits}','{page:linkbyscore}'),array($linkbytime,$linkbyhits,$linkbyscore),$this->H);
        }
    }
    
    function loadlist($flag)
    {
    	if(chkCache($this->P['cp'],$this->P['cn'])){
            $this->H = getCache($this->P['cp'],$this->P['cn']);
        }
        else{
        	$this->P[$flag."typepid"] = $this->T['t_pid'];
            $this->H = loadFile(MAC_ROOT_TEMPLATE.'/'.$this->T['t_tpl_list']);
            $this->H = str_replace(array('{page:id}','{page:name}','{page:enname}','{page:pid}','{page:key}','{page:des}','{page:title}','{page:link}','{page:textlink}'),array($this->T['t_id'],$this->T['t_name'],$this->T['t_enname'],$this->T['t_pid'],$this->T['t_key'],$this->T['t_des'],$this->T['t_title'],$this->getLink($flag,'list',$this->T,array()),$this->getTypeText($flag)),$this->H);
            $this->mark();
            setCache ($this->P['cp'],$this->P['cn'],$this->H);
        }
        $this->P['sitetid'] = $this->T['t_id'];
        $this->H = str_replace('{page:now}',$this->P['pg'],$this->H);
        
        
        if($flag == 'art'){
        	
        }
        else{
        	$order = $this->P['order']=='' ? 'desc' : $this->P['order'];
			$by = $this->P['by']=='' ? 'time' : $this->P['by'];
			
			$linkorderasc = $this->getLink($flag,'list',$this->T,array('pg'=>1,'order'=>'asc'));
			$linkorderdesc = $this->getLink($flag,'list',$this->T,array('pg'=>1,'order'=>'desc'));
			$linkbytime = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'time'));
			$linkbyhits = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'hits'));
			$linkbyscore = $this->getLink($flag,'list',$this->T,array('pg'=>1,'by'=>'score'));
			
			$linktype = $this->getLink($flag,'list',$this->T,array('pg'=>1,'id'=>$this->T['t_pid']));
			
			$colarr=array('{page:order}','{page:by}','{page:linktype}','{page:linkorderasc}','{page:linkorderdesc}','{page:linkbytime}','{page:linkbyhits}','{page:linkbyscore}');
			$valarr=array($order,$by,$linktype,$linkorderasc,$linkorderdesc,$linkbytime,$linkbyhits,$linkbyscore);
        	$linkyear = $this->getLink($flag,'list',$this->T,array('pg'=>1,'year'=>''));
        	$linkletter = $this->getLink($flag,'list',$this->T,array('pg'=>1,'letter'=>''));
        	$linkarea = $this->getLink($flag,'list',$this->T,array('pg'=>1,'area'=>''));
        	$linklang = $this->getLink($flag,'list',$this->T,array('pg'=>1,'lang'=>''));
        	$linkclass = $this->getLink($flag,'list',$this->T,array('pg'=>1,'class'=>''));
        	
        	$col=array('{page:area}','{page:year}','{page:class}','{page:classname}','{page:lang}','{page:letter}','{page:areaencode}','{page:langencode}','{page:linkyear}','{page:linkletter}','{page:linkarea}','{page:linklang}','{page:linkclass}');
        	
        	$classid = $this->P['class']==0?'':$this->P['class'];
        	$classname =  $GLOBALS['MAC_CACHE']['vodclass'][$classid]['c_name'];
        	$val=array($this->P['area'],$this->P['year']==0?'':$this->P['year'],$classid,$classname,$this->P['lang'],$this->P['letter'],urlencode($this->P['area']),urlencode($this->P['lang']),$linkyear,$linkletter,$linkarea,$linklang,$linkclass);
        	
        	$colarr=array_merge($colarr,$col);
        	$valarr=array_merge($valarr,$val);
        	unset($col);
        	unset($val);
        	$this->H = str_replace($colarr,$valarr,$this->H);
			unset($colarr);
        	unset($valarr);
       	}
		
    }
    
    function loadtopic($flag)
    {
        if(!is_array($this->T)){ showMsg ("找不到此数据", "../"); }
        
        if($this->P['make']!=true &&  chkCache($this->P['cp'],$this->P['cn'])){
            $this->H = getCache($this->P['cp'],$this->P['cn']);
        }
        else{
            $this->H = loadFile(MAC_ROOT_TEMPLATE."/".$this->T['t_tpl']);
            $this->H = str_replace(array('{page:id}','{page:name}','{page:enname}','{page:pic}','{page:key}','{page:des}','{page:title}','{page:content}','{page:link}'),array($this->T['t_id'],$this->T['t_name'],$this->T['t_enname'],$this->T['t_pic'],$this->T['t_key'],$this->T['t_des'],$this->T['t_title'],$this->T['t_content'],$this->getLink($flag,'topic',$this->T,$row)),$this->H);
			$this->mark();
            setCache ($this->P['cp'],$this->P['cn'],$this->H);
        }
        $this->P['sitetid'] = $this->T['t_id'];
        $this->H = str_replace("{page:now}", $this->P['pg'],$this->H);
        $this->H = str_replace(array('{page:hits}','{page:fav}','{page:share}','{page:desktop}'),array('<em id="hits">加载中</em><script>MAC.Hits("'.$flag.'_topic","'.$this->T['t_id'].'")</script>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Fav(document.URL,document.title);return false;\'>我要收藏</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Copy(document.title+"  " +document.URL);return false;\'>我要分享</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Desktop("'.$this->T['t_name'].'");return false;\'>保存到桌面</a>'),$this->H);
	        
    }
    
    function loadvod($flag)
    {
        if($this->P['make']!=true && chkCache($this->P['cp'],$this->P['cn'])){
            $this->H = getCache($this->P['cp'],$this->P['cn']);
        }
        else{
        	
        	$tp = MAC_ROOT_TEMPLATE."/" ;
	        if($flag=="detail"){ $tp .= $this->T['t_tpl_vod']; }
		    elseif($flag=="play"){ $tp .= $GLOBALS['MAC']['vod']['playisopen']==1 ? "vodplayopen.html" : $this->T["t_tpl_play"];  }
	        elseif($flag=="down"){ $tp .= $this->T['t_tpl_down']; }
	        elseif($flag=='rss') { $tp = MAC_ROOT.'/inc/map/rssid.html'; }
	        
	        if(!file_exists($tp)){
	        	$this->H = '';
	        	return;
	        }
            $this->H = loadFile($tp);
            $this->P['sitetid'] = $this->D['d_type'];
            $this->P['siteid'] = $this->D['d_id'];
            $this->P['vodtypeid'] = $this->T['t_id'];
            $this->P['vodtypepid'] = $this->T['t_pid'];
            
            if(strpos($this->H,"[vod:comment]")){
            	$commentH = loadFile(MAC_ROOT_TEMPLATE.'/home_comment.html');
            	$commentH = str_replace("{maccms:commentverify}", $GLOBALS['MAC']['other']['commentverify'] ,$commentH);
            	$this->H = str_replace('[vod:comment]','<div id="comment" class="comment">'.$commentH.'</div>',$this->H);
            }
            $this->mark();
            if(strpos($this->H,"{maccms:pages")){
            	$ps='MAC.Comment.Show(\'{url}\')';
            	$this->pageshow($ps);
            }
            
            if($this->P['make']){
            	return;
            }
	        setCache ($this->P['cp'],$this->P['cn'],$this->H);
        }
    }
    
    function replaceVod()
    {
    	$id = $this->D['d_id'];
        $name = $this->D['d_name'];
		$this->H = str_replace(array('[vod:hits]','[vod:fav]','[vod:share]','[vod:error]','[vod:desktop]','[vod:digg]','[vod:scoremark1]','[vod:scoremark2]'),array('<em id="hits">加载中</em><script>MAC.Hits("vod","'.$id.'")</script>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Fav(document.URL,document.title);return false;\'>我要收藏</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Copy(document.title+"  " +document.URL);return false;\'>我要分享</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Error("vod","'.$id.'","'.$name.'");return false;\'>我要报错</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Desktop("'.$name.'");return false;\'>保存到桌面</a>','<a target="_self" href="javascript:void(0)" class="digg_vodup">顶(<span>0</span>)</a><a target="_self" href="javascript:void(0)" class="digg_voddown">踩(<span>0</span>)</a>','<script>MAC.Score.Show(0,"vod",'.$id.');</script>','<script>MAC.Score.Show(1,"vod",'.$id.');</script>'),$this->H);
	    
		if (strpos($this->H,"[vod:history]")){
			$this->H = str_replace('[vod:history]', '<script>MAC.History.Insert("'.$name.'","'.$this->getLink('vod','detail',$this->T,$this->D).'","'.$this->T['t_name'].'","'.$this->getLink('vod','type',$typearr,$row,true).'","'.$this->D['d_pic'].'");</script>',$this->H);
		}
		if (strpos($this->H,"[vod:textlink]")){
			$this->H = str_replace("[vod:textlink]", $this->getTypeText("vod")."&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $this->getLink('vod','detail',$this->T,$this->D) ."'>".$name."</a>" ,$this->H );
		}
		if (strpos($this->H,"[vod:prelink]")){
			$this->H = str_replace("[vod:prelink]", $this->getPreNextLink('vod',$id,0),$this->H);
		}
		
		if  (strpos($this->H,"[vod:nextlink]")){
			$this->H = str_replace("[vod:nextlink]", $this->getPreNextLink('vod',$id,1),$this->H);
		}
		
		$labelRule = buildregx("\[vod:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->H,$matches2);
		for($j=0;$j<count($matches2[0]);$j++)
		{
			$marktemp = $this->parse("vod", $matches2[0][$j], $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $this->D, 0);
			$this->H = str_replace($matches2[0][$j], $marktemp,$this->H);
		}
		unset($matches2);
		$this->H = str_replace( "[vod:".$flag."er]", "<script src=\"". MAC_PATH ."js/playerconfig.js\"></script><script src=\"". MAC_PATH ."js/player.js\"></script><script>MacPlayer.init('".$GLOBALS['MAC']['vod']['suffix']."','".$flag."');</script>",$this->H);
    }
    
    function loadart()
    {
    	if($this->P['make']!=true && chkCache($this->P['cp'],$this->P['cn'])){
            $this->H = getCache($this->P['cp'],$this->P['cn']);
        }
        else{
        	$this->H = loadFile(MAC_ROOT_TEMPLATE."/".$this->T['t_tpl_art']);
        	$this->P['sitetid'] = $this->D['a_type'];
	        $this->P['siteid'] = $this->D['a_id'];
	        $this->P['arttypeid'] = $this->T['t_id'];
            $this->P['arttypepid'] = $this->T['t_pid'];
            
	        $this->mark();
	        $this->P['pageflag'] = 'art';
        	$this->P['pagetype'] = 'detail';
        	
        	if($this->P['make']){
            	return;
            }
            
	        $this->H = str_replace("[art:page]",$this->P["pg"],$this->H);
	        $arr = explode("[art:page]",$this->D["a_content"]);
	        $arrlen = count($arr);
	        if($this->P['pg']>$arrlen){ $this->P['pg']=$arrlen; }
	        $this->P['content'] = $arr[$this->P["pg"]-1];
			$this->P['pagesize'] = 1;
			$this->P['datacount'] = $arrlen;
			$this->P['pagecount'] = $arrlen;
			$this->pageshow();
	        unset($arr);
	        $this->replaceArt();
			setCache ($this->P['cp'],$this->P['cn'],$this->H);
        }
    }
    
    function replaceArt()
    {
    	$id = $this->D['a_id'];
        $name = $this->D['a_name'];
    	$slink = $this->getLink('art','detail',$typearr,$this->D);
		
		$this->H = str_replace(array('[art:hits]','[art:fav]','[art:share]','[art:desktop]','[art:digg]','[art:comment]'),array('<em id="hits">加载中</em><script>MAC.Hits("art","'.$id.'")</script>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Fav(document.URL,document.title);return false;\'>我要收藏</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Copy(document.title+"  " +document.URL);return false;\'>我要分享</a>','<a target="_self" href="javascript:void(0)" onclick=\'MAC.Desktop("'.$this->D['a_name'].'");return false;\'>保存到桌面</a>', '<div class="digg_artup" onmouseout="this.style.backgroundPosition=\'-189px 0\'" onmouseover="this.style.backgroundPosition=\'0 0\'"><div class="digg_bar"><div id="digg_artup_img"></div></div><span id="digg_artup_num"><span id="digg_artup_sp">0%</span> (<span id="digg_artup_val">0</span>)</span></div><div class="digg_artdown" onmouseout="this.style.backgroundPosition=\'-378px 0\'" onmouseover="this.style.backgroundPosition=\'-567px 0\'"><div class="digg_bar"><div id="digg_artdown_img"></div></div><span id="digg_artdown_num"><span id="digg_artdown_sp">0%</span> (<span id="digg_artdown_val">0</span>)</span></div>','<div id="comment" class="comment">评论加载中...</div>'),$this->H);
	        
	        
		if (strpos($this->H,"[art:textlink]")){
			$this->H = str_replace("[art:textlink]", $this->getTypeText("art")."&nbsp;&nbsp;&raquo;&nbsp;&nbsp;<a href='". $slink ."'>".$this->D["a_name"]."</a>" ,$this->H );
		}
		if (strpos($this->H,"[art:prelink]")){
			$this->H = str_replace("[art:prelink]", $this->getPreNextLink('art',$this->D["a_id"],0),$this->H);
		}
		if (strpos($this->H,"[art:nextlink]")){ 
			$this->H = str_replace("[art:nextlink]", $this->getPreNextLink('art',$this->D["a_id"],1),$this->H);
		}
		
		$labelRule = buildregx("\[art:\s*([0-9a-zA-Z]+)([\s]*[len|style]*)[=]??([\da-zA-Z\-\\\/\:\s]*)\]","");
		preg_match_all($labelRule ,$this->H,$matches2);
		for($j=0;$j<count($matches2[0]);$j++)
		{
			$marktemp = $this->parse("art", $matches2[0][$j], $matches2[0][$j], $matches2[1][$j], $matches2[3][$j], $this->D, 0);
			$this->H = str_replace($matches2[0][$j], $marktemp,$this->H);
		}
		unset($matches2);
    }
}
$tpl = new AppTpl();
?>