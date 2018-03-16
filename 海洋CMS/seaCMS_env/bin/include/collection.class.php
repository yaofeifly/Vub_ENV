<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

$dcollect = $col = new Collect();
class Collect
{
	/*
	 * 官方资源库采集入库
	 * $video xml单个simplexml数据
	 * $localId 入库后本地id
	 * */
	public function xml_db($video,$localId)
	{  
		$v_data['v_name'] =  htmlspecialchars($video->name);//影片名称
		$v_data['v_name'] = str_replace(array('\\','()','\''),'/',$v_data['v_name']);
		$v_data['v_pic'] = (String)$video->pic;//影片图片地址
		$v_data['v_state'] = (String)$video->state;//影片连载状态
		$v_data['v_lang'] = (String)$video->lang;//影片语言
		$v_data['v_publisharea'] =(String) $video->area;//影片地区
		$v_data['v_publishyear'] = (String)$video->year;//影片年份
		$v_data['v_note'] = (String)$video->note;//影片备注
		$v_data['v_tags'] = htmlspecialchars($video->keywords);//影片关键词
		$v_data['v_nickname'] = htmlspecialchars($video->nickname);//影片别名
		$v_data['v_reweek'] =(String) $video->reweek;//影片更新周期
		$v_data['v_douban'] = (String)$video->douban;//影片豆瓣评分
		$v_data['v_mtime'] = (String)$video->mtime;//影片时光网评分
		$v_data['v_imdb'] = (String)$video->imdb;//影片imdb评分
		$v_data['v_tvs'] = (String)$video->tvs;//影片上映电视台
		$v_data['v_company'] = (String)$video->company;//影片发行公司
		$v_data['v_ver'] = (String)$video->ver;//影片版本
		$v_data['v_longtxt'] =(String) $video->longtxt;//影片备用备注信息
		$v_data['v_actor'] = htmlspecialchars($video->actor);//影片演员
		$v_data['v_actor'] = str_replace('%', ' ', $v_data['v_actor']);
		$v_data['v_director'] = htmlspecialchars($video->director);//影片导演
		$v_data['v_director']  = str_replace('%', ' ', $v_data['v_director'] );
		$v_data['v_des'] = htmlspecialchars($video->des);//影片简介
		$v_data['v_total'] = (String)$video->episode;//总集数
		$v_data['v_len'] = (String)$video->len;//影片时长
		$v_data['v_total'] = (String)$video->total;//影片集数
		$v_data['v_jq'] = (String)$video->jq;//剧情分类
		if($v_data['v_actor']=="" OR empty($v_data['v_actor'])){$v_data['v_actor']="内详";}
		if($v_data['v_director']=="" OR empty($v_data['v_director'])){$v_data['v_director']="内详";}
		//$flag = $video->dl->dd['flag'];//影片前缀，属于哪个资源库
		//$v_data['v_playdata'] = $flag."$$".$video->dl->dd;//影片数据地址
		$zzt=count($video->dl->dd);
		$playerKindsfile="../data/admin/playerKinds.xml";
					$xml = simplexml_load_file($playerKindsfile);
					if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
					$id=0;
					$z=array();
					foreach($xml as $player){
					$k=$player['postfix'];
					$z["$k"]=$player['flag'];
					}
		for($i=0;$i<$zzt;$i++)
		{
		   if($video->dl->dd[$i]['flag']=='down')
		      {$v_data['v_downdata'] .= "下载地址一$$".$video->dl->dd[$i]."$$$";} 
		   else	
		      {
				  $f=$video->dl->dd[$i]['flag'];
				  $flag=$z["$f"];
				  $v_data['v_playdata'] .= $flag."$$".$video->dl->dd[$i]."$$$";
			  } 
		} 
		$v_data['v_playdata'] = substr($v_data['v_playdata'],0,-3);
		$v_data['v_downdata'] = substr($v_data['v_downdata'],0,-3);
		
		
		$v_data['v_enname'] = Pinyin($v_data['v_name']);
		$v_data['v_letter'] = strtoupper(substr($v_data['v_enname'],0,1));
		if(is_numeric($localId))
		{		
			$v_data['v_ismake'] = 0;
			$v_data['tid'] = $localId;
			return $this->_into_database($v_data);
		}else 
		{
			return $this->_into_tempdatabase($v_data);
		}
	}
	
	/*
	 * 自定义采集入库
	 * $listconf:列表采集规则配置
	 * $itemconf:单条数据采集规则配置
	 * $commconf:采集全局配置
	 * $row :单条数据url,id,pic
	 * $loopstr:单条数据采集规则配置
	 * $echo_id:排序位
	*/
	public function collect_db($listconf,$itemconf,$commconf,$row,$loopstr,$echo_id)
	{
		global $dsql;
		$html = cget($row['url'],$commconf['sock']);
		$lurl = '<a href="'.$row['url'].'" target="_blank">'.$row['url'].'</a>';
		if($html){
			$html = ChangeCode($html,$commconf['coding']);
			$pdate = getAreaValue($loopstr,"pdate",$html,$listconf["removecode"]);
			//判断时间处理
			if((!$commconf['getherday'])||($commconf['getherday']&&$this->isLeftDates($pdate,$commconf['getherday']))){
				if(trim($row['pic'])!=''){
					$v_data['v_pic']=$row['pic'];
				}else{
					$v_data['v_pic']=FillUrl($row['url'],getAreaValue($loopstr,"pic",$html,$listconf["removecode"]));
				}
				if($commconf['autocls']){
					$tname=getAreaValue($loopstr,"cls",$html,$listconf["removecode"]);
					$tid=$this->getTidFromCls($tname);
					$v_data['tname'] = $tname;
				}else{
					$tid=$commconf['classid'];
				}
			    $v_data['v_from'] = $row['url'];
			    $v_data['v_name']=getAreaValue($loopstr,"name",$html,$listconf["removecode"]);
			    $v_data['v_name']=$this->filterWord($v_data['v_name'],0);
				$v_data['v_enname']=Pinyin($v_data['v_name']);
				$v_data['v_name'] =  htmlspecialchars($v_data['v_name']);
				$v_data['v_name'] = str_replace(array('\\','()','\''),'/',$v_data['v_name']);
				
			    
			    $v_data['v_letter'] = strtoupper(substr( $v_data['v_enname'],0,1));
			    $v_data['v_state']=getAreaValue($loopstr,"state",$html,$listconf["removecode"]);
			    $v_data['v_actor']=getAreaValue($loopstr,"actor",$html,$listconf["removecode"]);
				$v_data['v_actor'] =  htmlspecialchars($v_data['v_actor']);
				$v_data['v_actor'] = str_replace('%', ' ', $v_data['v_actor']);
				
			    $v_data['v_director']=getAreaValue($loopstr,"director",$html,$listconf["removecode"]);
				$v_data['v_director'] =  htmlspecialchars($v_data['v_director']);
				$v_data['v_director'] = str_replace('%', ' ', $v_data['v_director']);
				
			    $v_data['v_note']=getAreaValue($loopstr,"note",$html,$listconf["removecode"]);
			    $v_data['v_des']=getAreaValue($loopstr,"des",$html,$listconf["removecode"]);
			    $v_data['v_des']=$this->filterWord($v_data['v_des'],1);
				$v_data['v_des'] =  htmlspecialchars($v_data['v_des']);
			    $v_data['v_publishyear']=getAreaValue($loopstr,"pyear",$html,$listconf["removecode"]);
			    $v_data['v_publisharea']=getAreaValue($loopstr,"parea",$html,$listconf["removecode"]);
			    $v_data['v_lang']=getAreaValue($loopstr,"plang",$html,$listconf["removecode"]);
				$v_data['tid']=$tid;
				if($v_data['v_actor']=="" OR empty($v_data['v_actor'])){$v_data['v_actor']="内详";}
				if($v_data['v_director']=="" OR empty($v_data['v_director'])){$v_data['v_director']="内详";}
				
			   if($listconf["inithit"]=='-1'){
				   $v_data['v_hit']=mt_rand(1,9999);
			   }else{
				   $v_data['v_hit']=0;
			   }
			   
			  //开始处理播放区域
			  if(trim($itemconf["splay"])==1){
				  $playareahtml=Geturlarray($html,getrulevalue($loopstr,"plista").'[内容]'.getrulevalue($loopstr,"plistb"));
			  }else{
				  $playareahtml[0]=$html;
			  }
			  //结束处理播放区域
			  
			  //获取下载地址开始
			  $downurlarray=array();
			  $downa=getrulevalue($loopstr,"downa");
			  $downb=getrulevalue($loopstr,"downb");
			  $down_trim=getrulevalue($loopstr,"down_trim");
			  if(trim($downa) !='' && trim($downb) != ''){
				  $downurlarray[]=Geturlarray($html,$downa.'[内容]'.$downb,$down_trim);
			  }
			  //获取下载地址结束
			  
			  //获取播放地址开始
			  $playlinka=getrulevalue($loopstr,"playlinka");
			  $playlinkb=getrulevalue($loopstr,"playlinkb");
			  $playlink_trim=getrulevalue($loopstr,"playlink_trim");
			  $msrca=getrulevalue($loopstr,"msrca");
			  $msrcb=getrulevalue($loopstr,"msrcb");
			  $msrc_trim=getrulevalue($loopstr,"msrc_trim");
			  $playurlarray=array();
			  $weburl=array();
			  $weburltemp=array();
			  if(trim($itemconf["playgetsrc"])==1 && trim($playlinka) !='' && trim($playlinkb) != ''){
				  foreach($playareahtml as $sv){
					$weburltemp=Geturlarray($sv,$playlinka.'[内容]'.$playlinkb,$playlink_trim);
					$weburl[]=$weburltemp;
				  }
				  $playurlarray=Getplayurlarr($weburl,$msrca.'[内容]'.$msrcb,$msrc_trim,$row['url'],$commconf['sock'],$commconf['coding']);
			  }else{
				  if(trim($msrca) !='' && trim($msrcb) != ''){
					  foreach($playareahtml as $sv){
					  $weburl[]=Geturlarray($sv,$msrca.'[内容]'.$msrcb,$msrc_trim);
					  }
					  $playurlarray=$weburl;
				  }
			  }
			  //获取播放地址结束
			  unset($weburl);
			  unset($weburltemp);
			  //截取分集名称开始
			  $parta=getrulevalue($loopstr,"parta");
			  $partb=getrulevalue($loopstr,"partb");
			  $part_trim=getrulevalue($loopstr,"part_trim");
			  $partarray=array();
			  $webparttemp=array();
			  if(trim($itemconf["getpart"])==1 && trim($parta) !='' && trim($partb) != ''){
			  	  	foreach($playareahtml as $sv){
						$webparttemp=Geturlarray($sv,$parta.'[内容]'.$partb,$part_trim);
						$webpart[]=$webparttemp;
					}
					$partarray=$webpart;
				  }
				  //截取分集名称结束
				  unset($webpart);
				  unset($webparttemp);
				  //播放器获取开始
				  if($itemconf["serveron"]==2){
					  $server[0]=$commconf['playfrom'];
				  }else{
					  $servera=getrulevalue($loopstr,"servera");
					  $serverb=getrulevalue($loopstr,"serverb");
					  $server_trim=getrulevalue($loopstr,"server_trim");
					  if($itemconf["serveron"]==1) $server=Geturlarray($playareahtml,$servera.'(.*)'.$serverb,$server_trim);
					  if($itemconf["serveron"]==0) $server=Geturlarray($html,$servera.'(.*)'.$serverb,$server_trim);
				  }
				  //播放器获取结束
				  //根据播放来源生成地址开始
				  if($itemconf["serveron"]==2 && $commconf['playfrom']==''){
					  $geturl='';
					  foreach($playurlarray as $psv){
						  $geturltemp='';
						  foreach($psv as $ppsv){
							  $geturltemp=$geturltemp.$ppsv.'#';
						  }
						  $geturltemp=rtrim($geturltemp,'#');
						  $geturl=$geturl.$geturltemp.'$$$';
					  }
					  $geturl=rtrim($geturl,'$$$');
				  }else if($itemconf["serveron"]==2 && $commconf['playfrom']!=''){
					  if($itemconf["getpart"]==2)
					  {
						  $geturl='';
						  foreach($playurlarray as $psv){
							  $geturltemp='';
							  foreach($psv as $ppsv){
								  $geturltemp .= $ppsv.'#';
							  }
							  $geturltemp=rtrim($geturltemp,'#');
							  $geturl .= $commconf['playfrom'].'$$'.$geturltemp.'$$$';
						  }
						  $geturl=rtrim($geturl,'$$$');
					  }else
					  {
					  	$geturl=transferUrlatr($commconf['playfrom'],$playurlarray,$partarray);
					  }
				  }else{
					  if($itemconf["getpart"]==2)
					  {
						  $geturl='';
						  foreach($playurlarray as $k=>$psv){
							  $geturltemp='';
							  foreach($psv as $ppsv){
								  $geturltemp .= $ppsv.'#';
							  }
							  $geturltemp=rtrim($geturltemp,'#');
							  $geturl .= $server[$k].'$$'.$geturltemp.'$$$';
						  }
						  $geturl=rtrim($geturl,'$$$');
					  }else
					  {
						  $geturl=transferUrlarr($server,$playurlarray,$partarray);
					  }
				  }
				  //根据播放来源生成地址结束
				  //生成下载地址
				  $v_data['v_downdata'] = GenerateDownUrl($listconf["downfrom"],$downurlarray,$partarray);
				  $v_data['v_playdata'] = $geturl;
				  unset($playurlarray);
				  unset($downurlarray);
				  if(empty($v_data['v_name']))
				  {
					  return "{$echo_id} ".$lurl."\t<font color=red>影片名为空，跳过保存</font>.<br>";
				  }
				  
			      $sql = "update `sea_co_url` set succ='1' where uid=".$row['uid'];
				  $dsql->ExecuteNoneQuery($sql);
				  
				  if($tid&&$itemconf["intodatabase"]){
					  return $this->_into_database($v_data,$echo_id);
				  }else
				  {
				  	  return $this->_into_tempdatabase($v_data,$echo_id);
				  }
			   }else{
				   return "{$echo_id} ".$lurl."\t<font color=red>只采集最近".$commconf['getherday']."天的数据，跳过保存</font>.<br>";
			   }
			}else{
				$sql = "update `sea_co_url` set err=err+1 where uid=".$row['uid'];
				$dsql->ExecuteNoneQuery($sql);
				return "{$echo_id} ".$lurl."\t<font color=red>远程读取失败</font>.<br>";
			}
	}
	
	public function _insert_database($v_data)
	{
		global $dsql;
		$v_data['v_pic'] = gatherPicHandle($v_data['v_pic']);
		$v_des = $v_data['v_des'];
		$v_playdata = $v_data['v_playdata'];
		$v_downdata = $v_data['v_downdata'];
		unset($v_data['v_des']);
		unset($v_data['v_playdata']);
		unset($v_data['v_downdata']);
		if(insert_record('sea_data',$v_data))
		{
			$v_id = $dsql->GetLastID();
			$desdata=array('v_id'=>$v_id,'tid'=>$v_data['tid'],'body'=>$v_des);
			$playdata=array('v_id'=>$v_id,'tid'=>$v_data['tid'],'body'=>$v_playdata,'body1'=>$v_downdata);
			insert_record('sea_content',$desdata);
			insert_record('sea_playdata',$playdata);
			return "数据<font color=red>".$v_data['v_name']."</font>已经采集成功<br>";
		}
	}
	
	public function _into_database($v_data,$i=0)
	{
		global $cfg_gatherset,$dsql; 
		$autocol_str = !empty($v_data['v_from']) ? $i.' <a href="'.$v_data['v_from'].'" target="_blank">'.$v_data['v_from'].'</a>的' :'';
		//数据部分处理
		$v_data['v_name'] = str_replace(array('\\','()','\''),'/',$v_data['v_name']);
		if($v_data['v_actor']=="" OR empty($v_data['v_actor'])){$v_data['v_actor']="内详";}
		if($v_data['v_director']=="" OR empty($v_data['v_director'])){$v_data['v_director']="内详";}


		$v_data['v_addtime'] = time();
		unset($v_data['v_id']);
		unset($v_data['v_from']);
		unset($v_data['tname']);
		
		/*if(strpos($v_data['v_name'],'/')!==FALSE)
		{
			$titleArray=explode('/',$v_data['v_name']);
			foreach($titleArray as $v_title){
				if(!empty($v_title)) $v_where.=" or locate('/$v_title/',concat('/',d.v_name,'/'))";
			}
			$v_where=ltrim($v_where," or ");
			$v_where="(".$v_where.")";
		}else 
		{*/
			$v_where="d.v_name='".$v_data['v_name']."'";
//		}
		
		$v_sql="select d.v_id,d.v_pic,d.v_isunion,p.body as v_playdata ,p.body1 as v_downdata from sea_data d left join sea_playdata p on p.v_id=d.v_id where $v_where order by d.v_id desc";
		$rs = $dsql->GetOne($v_sql);
		//if 同名
		if(is_array($rs))
		{
			
			//if 勾选[开启分类识别]
			if(strpos($cfg_gatherset,'0')!==false)
			{
				
				$v_sql1="select d.v_id,p.body as v_playdata,p.body1 as v_downdata,d.v_pic from sea_data d left join sea_playdata p on p.v_id=d.v_id where $v_where and d.tid='".$v_data['tid']."' order by d.v_id desc";
				$rs1 = $dsql->GetOne($v_sql1);
				if(is_array($rs1))
				{
					$v_data['v_playdata'] = gatherIntoLibTransfer($rs1['v_playdata'],$v_data['v_playdata']);
					$v_data['v_downdata'] = gatherIntoLibTransfer($rs1['v_downdata'],$v_data['v_downdata']);
					if($rs['v_isunion']=='1')
					{
						return $autocol_str."数据<font color=red>".$v_data['v_name']."</font>处于锁定状态,不更新数据<br>";
					}
					if($v_data['v_downdata']==$rs1['v_downdata']&&$v_data['v_playdata']==$rs1['v_playdata'])
					{
						return $autocol_str.'数据<font color=red>'.$v_data['v_name'].'</font>地址无变化,无需更新<br>';
					}
					//if 勾选[只更新影片地址]
				   if(strpos($cfg_gatherset,'2')!==false)
				   {
				       return $autocol_str.$this->update_playdata_only($rs1,$v_data);
				   }
				   //else 不勾选[只更新影片地址]
				   else
				   {
				   	   return $autocol_str.$this->update_movie_info($rs1,$v_data);
				   }
				}else
				{
					//if 勾选[开启不添加新影片]
				   if(strpos($cfg_gatherset,'1')!==false)
				   {
					  return $autocol_str."数据<font color=red>".$v_data['v_name']."</font>您开启了不添加新影片功能，跳过<br>";
				   }
				   //else 不勾选[开启不添加新影片]
				   return $autocol_str.$this->_insert_database($v_data);
				}
			}else
			{
				$v_data['v_playdata'] = gatherIntoLibTransfer($rs['v_playdata'],$v_data['v_playdata']);
				$v_data['v_downdata'] = gatherIntoLibTransfer($rs['v_downdata'],$v_data['v_downdata']);
				if($rs['v_isunion']=='1')
				{
					return "数据<font color=red>".$v_data['v_name']."</font>处于锁定状态,不更新数据<br>";
				}
			//$actarr1 = explode(' ',$v_data['v_actor']);
			//$actarr2 = explode(' ',str_replace(array(',','/','，'),' ',$rs['v_actor']));
			//有相同演员时更新该影片
			//if(array_intersect($actarr1,$actarr2))
			//{
				if($v_data['v_downdata']==$rs['v_downdata']&&$v_data['v_playdata']==$rs['v_playdata'] && (strpos($cfg_gatherset,'3')!==false))
				{
					return $autocol_str.'数据<font color=red>'.$v_data['v_name'].'</font>地址无变化,无需更新<br>';
				}
				//if 勾选[只更新影片地址]
				 if(strpos($cfg_gatherset,'2')!==false)
				 {
					return $autocol_str.$this->update_playdata_only($rs,$v_data);
				 }
				 //else 不勾选[只更新影片地址]
				 elseif(strpos($cfg_gatherset,'4')!==false)
				 {
					return $autocol_str.$this->update_movie_info_pic($rs,$v_data);
				 }
				 else
				 {
					return $autocol_str.$this->update_movie_info($rs,$v_data);
				 }
			}
		}//else 不 同名
		else{
			
			//if 开启不添加新影片
			if(strpos($cfg_gatherset,'1')!==false)
			{
				return $autocol_str."数据<font color=red>".$v_data['v_name']."</font>跳过，您开启了不添加新影片功能<br>";
			}
			// else 以新影片添加
			return $autocol_str.$this->_insert_database($v_data);
		}
	}
	
	public function _into_tempdatabase($v_data,$i=0)
	{
		global $dsql;
		$v_data['v_addtime'] = time();
		$autocol_str = !empty($v_data['v_from']) ? $i.'. <a href="'.$v_data['v_from'].'" target="_blank">'.$v_data['v_from'].'</a>' :'';
		$db = !empty($v_data['v_from'])  ? 'sea_co_data':'sea_temp';
		if($db == 'sea_temp')
		{
			unset($v_data['tname']);
			$v_data['tid'] = '000000';
		}
		/*if(strpos($v_data['v_name'],'/')!==FALSE)
		{
			$titleArray=explode('/',$v_data['v_name']);
			foreach($titleArray as $v_title){
				if(!empty($v_title)) $v_where.=" or locate('/$v_title/',concat('/',v_name,'/'))>0 ";
			}
			$v_where=ltrim($v_where," or ");
			$v_where="(".$v_where.")";
		}else 
		{*/
			$v_data['v_name'] = str_replace(array('HD','BD','DVD','VCD','TS','【完结】','【】','[]','()','\''),'',$v_data['v_name']);
			$v_where="v_name='".$v_data['v_name']."'";
//		}
		
		$v_sql="select v_id,v_playdata,v_pic from ".$db." where $v_where order by v_id desc";
		$rs = $dsql->GetOne($v_sql);
		//if 不同名
		if(!is_array($rs))
		{
			$v_data['v_pic'] = gatherPicHandle($v_data['v_pic']);
			insert_record($db,$v_data);
			return $autocol_str."数据<font color=red>".$v_data['v_name']."</font>已经采集成功<br>";
		}//else 同名
		else{
			$v_data['v_playdata'] = gatherIntoLibTransfer($rs['v_playdata'],$v_data['v_playdata']);
			$v_data = $this->update_pic($v_data,$rs);
			update_record($db,"where v_id=".$rs['v_id'],$v_data);
			return $autocol_str."数据<font color=red>".$v_data['v_name']."</font>已存在,更新数据<br>";
			
		}
	}
	
	function update_playdata_only($rs,$v_data)
	{
		update_record('sea_data',"where v_id=".$rs['v_id'],array('v_ismake'=>'0','v_state'=>$v_data['v_state'],'v_note'=>$v_data['v_note'],'v_addtime'=>time()));
		update_record('sea_playdata',"where v_id=".$rs['v_id'],array('body'=>$v_data['v_playdata'],'body1'=>$v_data['v_downdata']));
		return "数据<font color=red>".$v_data['v_name']."</font>已存在,仅更新地址及状态<br>";
	}
	
	function update_movie_info($rs,$v_data)
	{
		
		$v_des = $v_data['v_des'];
		$v_playdata = $v_data['v_playdata'];
		$v_downdata = $v_data['v_downdata'];
		unset($v_data['v_des']);
		unset($v_data['v_pic']);
		unset($v_data['v_playdata']);
		unset($v_data['v_downdata']);
		update_record('sea_data',"where v_id=".$rs['v_id'],$v_data);
		update_record('sea_data',"where v_id=".$rs['v_id'],array('v_ismake'=>'0','v_addtime'=>time()));
		update_record('sea_playdata',"where v_id=".$rs['v_id'],array('body'=>$v_playdata,'body1'=>$v_downdata));
		update_record('sea_content',"where v_id=".$rs['v_id'],array('body'=>$v_des));
		return "数据<font color=red>".$v_data['v_name']."</font>已存在,更新数据，不更新图片<br>";
	}
	
	function update_movie_info_pic($rs,$v_data)
	{
		
		$v_des = $v_data['v_des'];
		$v_playdata = $v_data['v_playdata'];
		$v_downdata = $v_data['v_downdata'];
		unset($v_data['v_des']);
		unset($v_data['v_playdata']);
		unset($v_data['v_downdata']);
		update_record('sea_data',"where v_id=".$rs['v_id'],$v_data);
		update_record('sea_data',"where v_id=".$rs['v_id'],array('v_ismake'=>'0','v_addtime'=>time()));
		update_record('sea_playdata',"where v_id=".$rs['v_id'],array('body'=>$v_playdata,'body1'=>$v_downdata));
		update_record('sea_content',"where v_id=".$rs['v_id'],array('body'=>$v_des));
		return "数据<font color=red>".$v_data['v_name']."</font>已存在,更新数据,更新图片。<br>";
	}
	
	function update_pic($v_data,$rs)
	{
		global $cfg_gatherset;
		if(strpos($cfg_gatherset,'4'))
		{
			unset($v_data['v_pic']);
		}
		
		return $v_data;
	}
	
	function isLeftDates($pdate,$getherday)
	{
		if(empty($pdate)) return false;
		$date2 = time();
		$dateArr=explode("-",$pdate);
		$date1Int = mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]);
		$daysleft = floor(($date2-$date1Int) / 86400);
		if($getherday>=$daysleft) return true; else return false;
	}
	
	function getTidFromCls($name)
	{
		global $dsql;
		$trow = $dsql->GetOne("select sysclsid from sea_co_cls where clsname='$name'");
		if(is_array($trow)) return $trow['sysclsid'];
		else return 0;
	}
	
	function filterWord($string,$rCol)
	{
		global $dsql;
		if($string=='')
		return $string;
		$sql = "SELECT rColumn,uesMode,sFind,sReplace,sStart,sEnd FROM sea_co_filters WHERE Flag=1 and cotype=0";
		$dsql->SetQuery($sql);
		$dsql->Execute('filterWord');
		while ($row =$dsql->GetArray('filterWord'))
		{
			if($row['rColumn']==$rCol)
			{
				if($row['uesMode']==1)
				$string=preg_replace("/".addslashes($row['sStart'])."([\s\S]+?)".addslashes($row['sEnd'])."/ig", $row['sReplace'], $string);
				else
				$string=str_replace($row['sFind'], $row['sReplace'], $string);
			}
		}
		return $string;
	}
	
}