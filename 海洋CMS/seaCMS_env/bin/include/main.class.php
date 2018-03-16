<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

$t1=ExecTime();
class MainClass_Template
{
	function __construct()
	{
		global $dsql,$cfg_basedir,$cfg_df_style,$cfg_df_html;
		$this->dsql = $dsql;
		$this->templateDir=$cfg_basedir."templets/".$cfg_df_style."/".$cfg_df_html."/";
	}
	function parseTopAndFoot($content)
	{
		global $cfg_gbookstart;
		$content=str_replace("{seacms:top}",loadFile($this->templateDir."head.html"),$content);
		$content=str_replace("{seacms:foot}",loadFile($this->templateDir."foot.html"),$content);
		if(strpos($content, "{seacms:load "))
		{
			preg_match_all("/{seacms:load (.*?)}/is", $content, $matches);
			foreach ($matches[1] as $k=>$match){
				$m=trim($match);
				if(file_exists($this->templateDir.$m)){
				$content=str_replace($matches[0][$k],$this->parseStrip(loadFile($this->templateDir.$m)),$content);
				}
			}
		}
		$content=str_replace("images/","/".$GLOBALS['cfg_cmspath']."templets/".$GLOBALS['cfg_df_style']."/images/",$content);
		if($cfg_gbookstart){
			$content=str_replace("{seacms:gbook}","",$content);
		}else{
			$content=str_replace("{seacms:gbook}","<a href=\"/{seacms:sitepath}gbook.php\" target=\"_blank\">留言求片</a>",$content);
		}
		$content=$this->parseStrip($content);
		return $content;
	}
	function parseSelf($content){
		if (strpos($content,'{self:')=== false){
			return $content;
		}else{
			global $cfg_issqlcache;
			$labelRule = buildregx("{self:(.*?)}","is");
			preg_match_all($labelRule,$content,$sar);
			$sql="select tagname,tagcontent from sea_mytag";
			if($cfg_issqlcache){
				$mycachefile=md5('mytag');
				setCache($mycachefile,$sql);
				$rows=getCache($mycachefile);
			}else{
				$rows=array();
				$this->dsql->SetQuery($sql);
				$this->dsql->Execute('parseSelf');
				while($rowr=$this->dsql->GetObject('parseSelf'))
				{
					$rows[]=$rowr;
				}
					unset($rowr);
				}
				$trow=array();
				foreach($rows as $row){
					$singleAttrValue=explode("$$$",$row->tagcontent);
					$singleLength=count($singleAttrValue);
					if($singleLength>0){
						$singleNum = mt_rand(0,$singleLength-1);
						$trow[$row->tagname]=$singleAttrValue[$singleNum];
					}else{
						$trow[$row->tagname]=$row->tagcontent;
				}
			}
	
			$arlen=count($sar[1]);
			for($m=0;$m<$arlen;$m++){
				$labelName=trim($sar[1][$m]);
				if(isset($trow[$labelName])){
					$content=str_replace($sar[0][$m],$trow[$labelName],$content);
				}
			}
			return $content;
		}
	}
	
	function parseStrip($content){
		if(strpos($content, "{/seacms:strip}")===false)
		{
			return $content;
		}
		else{
			preg_match_all("@(<script.*?>.*?</script>)@is", $content, $scptMch);
			$i=-1;
			$scptArr=array();
			foreach ($scptMch[1] as $scpt){
				$i++;
				$scptArr[]=$scpt;
				$content=str_replace($scpt,"<[script.".$i."]>",$content);
			}
			preg_match_all("@{seacms:strip}(.*?){/seacms:strip}@is", $content, $matches);
			foreach ($matches[1] as $k=>$match){
				$content=str_replace($matches[0][$k],preg_replace("/[\t ]*[\r\n]+[\t ]*/is", "", $match),$content);
			}
			if($i>-1){
				for($i;$i>=0;$i--){
					$content=str_replace("<[script.".$i."]>", $scptArr[$i], $content);
				}
			}
			return $content;
		}
	}
	
	function parseGlobal($content){
		if (strpos($content,'{seacms:letterlist}')>0) $content=str_replace("{seacms:letterlist}",getletterlist(),$content);
		if (strpos($content,'{seacms:indexlink}')>0) $content=str_replace("{seacms:indexlink}",getIndexLink(),$content);
		if (strpos($content,'{seacms:newslink}')>0) $content=str_replace("{seacms:newslink}",getnewsxLink(),$content);		
		if (strpos($content,'{seacms:topiclink}')>0) $content=str_replace("{seacms:topiclink}",getTopicIndexLink(1),$content);
		$content=str_replace("{seacms:siteurl}",str_replace("http://","",$GLOBALS['cfg_basehost']),$content);
		$content=str_replace("{seacms:sitepath}",$GLOBALS['cfg_cmspath'],$content);
		$content=str_replace("{seacms:adfolder}",$GLOBALS['cfg_ads_dir'],$content);
		$content=str_replace("{seacms:sitename}",$GLOBALS['cfg_webname'],$content);
		$content=str_replace("{seacms:copyright}",decodeHtml($GLOBALS['cfg_powerby']),$content);
		$content=str_replace("{seacms:des}",decodeHtml($GLOBALS['cfg_description']),$content);
		$content=str_replace("{seacms:sitevisitjs}",stripslashes($GLOBALS['cfg_sitevisitejs']),$content);
		$content=str_replace("{seacms:sitenotice}",decodeHtml($GLOBALS['cfg_site_notice']),$content);
		if(strpos($content,"{seacms:hotkeywords")!==false)
		{
			$labelHaveLen = buildregx("{seacms:hotkeywords\s+len=(\d+)?\s*}","is");
			preg_match_all($labelHaveLen,$content,$labelHaveLenar);
			$HaveLenarcount=count($labelHaveLenar[0]);
			if($HaveLenarcount){
				for($hm=0;$hm<$HaveLenarcount;$hm++){
					$strLen=$labelHaveLenar[1][$hm];
					$strByLen=lib_hotwords($strLen);
					$content=str_replace($labelHaveLenar[0][$hm],$strByLen,$content);
					
				}
			}else
			{
				$content=str_replace("{seacms:hotkeywords}",lib_hotwords(),$content);	
			}
		}
		$content=str_replace("{seacms:keywords}",site_keywords(),$content);
		//$content=str_replace("{seacms:beian}",$GLOBALS['cfg_beian'],$content);
		if (strpos($content,'{seacms:allcount}')>0) $content=str_replace("{seacms:allcount}",getDataCount("all"),$content);
		if (strpos($content,'{seacms:daycount}')>0) $content=str_replace("{seacms:daycount}",getDataCount("day"),$content);
		$content=$this->parseSlide($content);
		$content=$this->parseCascadeList($content,'type');
		$content=$this->parseCascadeList($content,'year');
		$content=$this->parseCascadeList($content,'area');
		$content=$this->parseCascadeList($content,'letter');
		$content=$this->parseCascadeList($content,'lang');
		$content=$this->parseCascadeList($content,'jq');
		$content=$this->parseCascadeList($content,'state');
		$content=$this->parseCascadeList($content,'ver');
		$content=$this->parseCascadeList($content,'money');
		if($GLOBALS['cfg_runmode']=='0') $content=str_replace("{seacms:runinfo}","",$content);
		$content=str_replace("{seacms:shang}",'<a href="#" onclick="shang(prePage,sssss)">上一集</a>',$content) ;
		$content=str_replace("{seacms:xia}",'<a href="#" onclick="xia(nextPage,zno)">下一集</a>',$content) ;		
		return $content;
	}
	
	function parseCascadeList($content,$str="")
	{
		global $lang;
		if (strpos($content,'caslist}')=== false){
			return $content;
		}
		$labelRule="{seacms:".$str."caslist(.*?)}(.*?){/seacms:".$str."caslist}";
		$attrDictionary=array();
		$labelRule = buildregx($labelRule,"is");
		preg_match_all($labelRule,$content,$mar);
		$arlen=count($mar[1]);
			for($m=0;$m<$arlen;$m++){
				$loopstrTotal="";
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$mar[1][$m]));
				$loopstrMenulist=$mar[2][$m];
				$attrDictionary=$this->parseAttr($attrStr);
				$vtype=empty($attrDictionary["type"]) ? "all" : $attrDictionary["type"];
				switch(trim($str))
				{
					case "type":
					case "sectype":
						if($vtype=='top')
						{
							$rsArray=getMenuArray(0,"upid");
						}elseif($vtype=='all'||$vtype=='')
						{
							$rsArray=getMenuArray(0,"upid",0,true);
						}else
						{
							$vtypear=explode(',',$vtype);
							foreach($vtypear as $vtypestr){
								$vtypearr[]=trim($vtypestr);
								if(!is_numeric(trim($vtypestr))) exit($lang['channellistInfo']['1']);
							}
							$vtype=implode(',',$vtypearr);
							unset($vtypearr);
							$rsArray=getMenuArray($vtype,"tid");
						}
					break;
					case "year":					
						$publishyeartxt=sea_DATA."/admin/publishyear.txt";
						$publishyear = array();
						if(filesize($publishyeartxt)>0)
						{
							$publishyear = file($publishyeartxt);
						}
						$rsArray=$publishyear;
						array_push($rsArray,"more");
					break;
					case "area":
						$publishareatxt=sea_DATA."/admin/publisharea.txt";
						$publisharea = array();
						if(filesize($publishareatxt)>0)
						{
							$publisharea = file($publishareatxt);
						}
						$rsArray=$publisharea;
					break;
					case "lang":
						$publishyuyantxt=sea_DATA."/admin/publishyuyan.txt";
						$publishyuyan = array();
						if(filesize($publishyuyantxt)>0)
						{
							$publishyuyan = file($publishyuyantxt); 
						}
						$rsArray=$publishyuyan;
					break;
					case "ver":
						$vertxt=sea_DATA."/admin/verlist.txt";
						$ver = array();
						if(filesize($vertxt)>0)
						{
							$ver = file($vertxt); 
						}
						$rsArray=$ver;
					break;
					case "letter":
						$rsArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0-9');
					break;
					case "state":
						$rsArray = array('w','l');
					break;
					case "money":
						$rsArray = array('s','m');
					break;
					case "jq":
						$sql="select tname from sea_jqtype where ishidden=0";
						$this->dsql->SetQuery($sql);
						$this->dsql->Execute('zz');
						while($rowr=$this->dsql->GetObject('zz'))
						{
							$rows[]=$rowr->tname;
						}
						$rsArray = $rows;
					break;
				}
				$labelRuleField = buildregx("\[".$str."caslist:(.*?)\]","is");
				preg_match_all($labelRuleField,$loopstrMenulist,$menuar);
				$matchfieldarr=$menuar[1];
				$matchfieldstrarr=$menuar[0];
				$loopstrTotal="";
				$i=1;
				if(is_array($rsArray)){
					foreach($rsArray as $row){
						$loopstrMlistNew=$loopstrMenulist;
						foreach($matchfieldarr as $f=>$matchfieldstr){
							$matchfieldvalue=$matchfieldstrarr[$f];
							$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
							$fieldName=$matchfieldstr;
							switch (trim($fieldName)) {
								case "i":
									$loopstrMlistNew=str_replace($matchfieldvalue,$i,$loopstrMlistNew);
								break;
								case "value":
									if(trim($str)=="type"||trim($str)=="sectype")
									{
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row['tname'])),$loopstrMlistNew);
									}
									elseif(trim($str)=="state")
									{
									if($row=='w'){$state="完结";}else{$state="连载中";}
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$state)),$loopstrMlistNew);
									}
									elseif(trim($str)=="money")
									{
									if($row=='s'){$money="收费";}else{$money="免费";}
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$money)),$loopstrMlistNew);
									}
									else
									{
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row)),$loopstrMlistNew);
									}									
								break;
								case "link":
									$v = (trim($str)=="type"||trim($str)=="sectype")?$row['tid']:trim($row);
									$loopstrMlistNew=str_replace($matchfieldvalue,getCascadeLink($str,$v),$loopstrMlistNew);
								break;
								case "tid":
									$loopstrMlistNew=str_replace($matchfieldvalue,$row['tid'],$loopstrMlistNew);
								break;
							}
						}
					$i=$i+1;
					$loopstrTotal.=$loopstrMlistNew;
				}
				unset($rsArray);
			}
			$content=str_replace($mar[0][$m],$loopstrTotal,$content);
		}
		if(strpos($content,"{seacms:sectypecaslist")){
			return $this->parseCascadeList($content,"sectype");
		}else{
			return $content;
		}
		
	}
	
	function parseSearchItemList($content,$str="")
	{
		if (strpos($content,'itemlist}')=== false){
			return $content;
		}
		$labelRule="{seacms:".$str."itemlist(.*?)}(.*?){/seacms:".$str."itemlist}";
		$attrDictionary=array();
		$labelRule = buildregx($labelRule,"is");
		preg_match_all($labelRule,$content,$mar);
		$arlen=count($mar[1]);
			for($m=0;$m<$arlen;$m++){
				$loopstrTotal="";
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$mar[1][$m]));
				$loopstrMenulist=$mar[2][$m];
				$attrDictionary=$this->parseAttr($attrStr);
				$vtype=empty($attrDictionary["type"]) ? "all" : $attrDictionary["type"];
				switch(trim($str))
				{
					case "type":
					case "sectype":
						if($vtype=='top')
						{
							$rsArray=getMenuArray(0,"upid");
						}elseif($vtype=='all')
						{
							$rsArray=getMenuArray(0,"upid",0,true);
						}else
						{
							$vtypear=explode(',',$vtype);
							foreach($vtypear as $vtypestr){
								$vtypearr[]=trim($vtypestr);
								if(!is_numeric(trim($vtypestr))) exit($lang['channellistInfo']['1']);
							}
							$vtype=implode(',',$vtypearr);
							unset($vtypearr);
							$rsArray=getMenuArray($vtype,"tid");
						}
						array_splice($rsArray,0,0,array(array('tid'=>'全部','tname'=>'全部')));
					break;
					case "year":					
						$publishyeartxt=sea_DATA."/admin/publishyear.txt";
						$publishyear = array();
						if(filesize($publishyeartxt)>0)
						{
							$publishyear = file($publishyeartxt);
						}
						$rsArray=$publishyear;
						array_push($rsArray,"more");
						array_splice($rsArray,0,0,'全部');
					break;
					case "ver":					
						$vertxt=sea_DATA."/admin/verlist.txt";
						$ver = array();
						if(filesize($vertxt)>0)
						{
							$ver = file($vertxt);
						}
						$rsArray=$ver;
						array_splice($rsArray,0,0,'全部');
					break;
					case "area":
						$publishareatxt=sea_DATA."/admin/publisharea.txt";
						$publisharea = array();
						if(filesize($publishareatxt)>0)
						{
							$publisharea = file($publishareatxt);
						}
						$rsArray=$publisharea;
						 array_splice($rsArray,0,0,'全部');
					break;
					case "lang":
						$publishyuyantxt=sea_DATA."/admin/publishyuyan.txt";
						$publishyuyan = array();
						if(filesize($publishyuyantxt)>0)
						{
							$publishyuyan = file($publishyuyantxt);
						}
						$rsArray=$publishyuyan;
						 array_splice($rsArray,0,0,'全部');
					break;
					case "letter":
						$rsArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0-9');
						array_splice($rsArray,0,0,'全部');
					break;
					case "state":
						$rsArray = array('w','l');
						array_splice($rsArray,0,0,'全部');
					break;
					case "money":
						$rsArray = array('s','m');
						array_splice($rsArray,0,0,'全部');
					break;
					case "jq":
						$sql="select tname from sea_jqtype where ishidden=0";
						$this->dsql->SetQuery($sql);
						$this->dsql->Execute('zz');
						while($rowr=$this->dsql->GetObject('zz'))
						{
							$rows[]=$rowr->tname;
						}
						$rsArray = $rows;
						array_splice($rsArray,0,0,'全部');
					break;

				}
				$labelRuleField = buildregx("\[".$str."itemlist:(.*?)\]","is");
				preg_match_all($labelRuleField,$loopstrMenulist,$menuar);
				$matchfieldarr=$menuar[1];
				$matchfieldstrarr=$menuar[0];
				$loopstrTotal="";
				$i=1;
				if(is_array($rsArray)){
					foreach($rsArray as $row){
						$loopstrMlistNew=$loopstrMenulist;
						foreach($matchfieldarr as $f=>$matchfieldstr){
							$matchfieldvalue=$matchfieldstrarr[$f];
							$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
							$fieldName=$matchfieldstr;
							switch (trim($fieldName)) {
								case "i":
									$loopstrMlistNew=str_replace($matchfieldvalue,$i,$loopstrMlistNew);
								break;
								case "value":
									if(trim($str)=="type"||trim($str)=="sectype")
									{
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row['tname'])),$loopstrMlistNew);
									}
									elseIf(trim($str)=="state")
									{
									if($row=='w'){$row="完结";}elseif($row=='l'){$row="连载中";}else{$row="全部";}
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row)),$loopstrMlistNew);
									}
									elseIf(trim($str)=="money")
									{
									if($row=='s'){$row="收费";}elseif($row=='m'){$row="免费";}else{$row="全部";}
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row)),$loopstrMlistNew);
									}
									else
									{
									$loopstrMlistNew=str_replace($matchfieldvalue,trim(str_replace("\r\n","",$row)),$loopstrMlistNew);
									}									
								break;
								case "link":
									global $schwhere;
									$v = (trim($str)=="type"||trim($str)=="sectype")?$row['tid']:trim($row);
									$loopstrMlistNew=str_replace($matchfieldvalue,getItemLink($str,$v,$schwhere),$loopstrMlistNew);
								break;
								case "tid":
									$loopstrMlistNew=str_replace($matchfieldvalue,($row['tid']=="全部"?'':$row['tid']),$loopstrMlistNew);
								break;
							}
						}
					$i=$i+1;
					$loopstrTotal.=$loopstrMlistNew;
				}
				unset($rsArray);
			}
			$content=str_replace($mar[0][$m],$loopstrTotal,$content);
		}
		if(strpos($content,"{seacms:sectypeitemlist")){
			return $this->parseCascadeList($content,"sectype");
		}else{
			return $content;
		}
		
	}
	
	function parseMenuList($content,$str="",$currentTypeId=-444)
	{
		$labelRule="{seacms:".$str."menulist(.*?)}(.*?){/seacms:".$str."menulist}";
		$attrDictionary=array();
		$labelRule = buildregx($labelRule,"is");
		preg_match_all($labelRule,$content,$mar);
		$arlen=count($mar[1]);
			for($m=0;$m<$arlen;$m++){
				$loopstrTotal="";
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$mar[1][$m]));
				$loopstrMenulist=$mar[2][$m];
				$attrDictionary=$this->parseAttr($attrStr);
				$vtype=empty($attrDictionary["type"]) ? "top" : $attrDictionary["type"];
				$vby=$attrDictionary["by"];
				unset($attrDictionary);
				switch (trim($vtype)) {
					case "top":
						if($vby=='news')
						$rsArray=getMenuArray(0,"upid",1);
						else
						$rsArray=getMenuArray(0,"upid");
					break;
					case "son":
						$curUpId=GetTopid($currentTypeId);
						if($curUpId!="") $curUpId=$curUpId; else $curUpId=-444;
						if($vby=='news')
						{
							if($curUpId!=0){
								$rsArray=getMenuArray($curUpId,"upid",1);
							}else{
								$rsArray=getMenuArray($currentTypeId,"upid",1);
							}	
						}
						else{
							if($curUpId!=0){
								$rsArray=getMenuArray($curUpId,"upid");
							}else{
								$rsArray=getMenuArray($currentTypeId,"upid");
							}
						}
					break;
					case "all":
						if($vby=='news')
						{
						$rsArray=getMenuArray(0,"upid",1,true);	
						}
						else{
						$rsArray=getMenuArray(0,"upid",0,true);
						}
					break;
					default:
						$vtypear=explode(',',$vtype);
						foreach($vtypear as $vtypestr){
							$vtypearr[]=trim($vtypestr);
							if(!is_numeric(trim($vtypestr))) exit($lang['channellistInfo']['1']);
						}
						$vtype=implode(',',$vtypearr);
						unset($vtypearr);
						if($vby=='news')
						{
							if(empty($str)){
								$rsArray=getMenuArray($vtype,"tid",1);
							}else{
								$rsArray=getMenuArray($vtype,"upid",1);
							}
						}else{
							if(empty($str)){
								$rsArray=getMenuArray($vtype,"tid");
							}else{
								$rsArray=getMenuArray($vtype,"upid");
							}	
						}
				}
				$labelRuleField = buildregx("\[".$str."menulist:(.*?)\]","is");
				preg_match_all($labelRuleField,$loopstrMenulist,$menuar);
				$matchfieldarr=$menuar[1];
				$matchfieldstrarr=$menuar[0];
				$loopstrTotal="";
				$i=1;
				if(is_array($rsArray)){
					foreach($rsArray as $row){
						$loopstrMlistNew=$loopstrMenulist;
						foreach($matchfieldarr as $f=>$matchfieldstr){
							$matchfieldvalue=$matchfieldstrarr[$f];
							$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
							$fieldName=$matchfieldstr;
							switch (trim($fieldName)) {
								case "i":
									$loopstrMlistNew=str_replace($matchfieldvalue,$i,$loopstrMlistNew);
								break;
								case "typeid":
									$loopstrMlistNew=str_replace($matchfieldvalue,$row['tid'],$loopstrMlistNew);
								break;
								case "typename":
									$loopstrMlistNew=str_replace($matchfieldvalue,$row['tname'],$loopstrMlistNew);
								break;
								case "upid":
									$loopstrMlistNew=str_replace($matchfieldvalue,$row['upid'],$loopstrMlistNew);
								break;
								case "link":
									if($vby=='news')
									$loopstrMlistNew=str_replace($matchfieldvalue,getnewspageLink($row['tid']),$loopstrMlistNew);
									else
									$loopstrMlistNew=str_replace($matchfieldvalue,getChannelPagesLink($row['tid']),$loopstrMlistNew);
								break;
							}
						}
					$i=$i+1;
					$loopstrTotal.=$loopstrMlistNew;
				}
				unset($rsArray);
			}
			$content=str_replace($mar[0][$m],$loopstrTotal,$content);
		}	
		if(strpos($content,"{seacms:smallmenulist")){
			return $this->parseMenuList($content,"small",$currentTypeId);
		}else{
			return $content;
		}
	}

	function parseAreaList($content){
		if (strpos($content,'{seacms:arealist')=== false){
			return $content;
		}else{
			$attrDictionary=array();
			$labelRule = buildregx("{seacms:arealist(.*?)}(.*?){/seacms:arealist}","is");
			$labelRuleVideolist=buildregx("{seacms:videolist(.*?)}(.*?){/seacms:videolist}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			$typeStr=getTypeId(0);
			$tids=getMenuArray($typeStr,'tid');
			for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopArealist=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$areaType=empty($attrDictionary["areatype"]) ? "all" : $attrDictionary["areatype"];
			$letterHas=$attrDictionary["letter"];
			$tagHas=$attrDictionary["tag"];
			if (!empty($letterHas)) $areaType="letter";
			if($areaType=="letter"){
				if (empty($letterHas) || $letterHas=="all") $letterHas="A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
				$letterHasArray=explode(",",strtoupper($letterHas));
				$letterlen=count($letterHasArray);
				$k=0;
				for($j=0;$j<$letterlen;$j++){
					if(!empty($letterHasArray[$j])){
						$k=$j+1;
						$singleStrAreaList=$loopArealist;
						$singleStrAreaList=str_replace("[arealist:i]",$k,$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typename]",$letterHasArray[$j],$singleStrAreaList);
						if(strpos($singleStrAreaList,"[arealist:count]")>0){
							$row=$this->dsql->GetOne("select count(*) as dd from `sea_data` where v_letter='".$letterHasArray[$j]."'");
							$singleStrAreaList=str_replace("[arealist:count]",$row['dd'],$singleStrAreaList);
							$singleStrAreaList=str_replace("[arealist:link]","/".$GLOBALS['cfg_cmspath']."search.php?searchword=".$letterHasArray[$j],$singleStrAreaList);
							preg_match_all($labelRuleVideolist,$singleStrAreaList,$arv);
							$arvlen=count($arv[1]);
							for($n=0;$n<$arvlen;$n++){
								$videoListStr=str_replace("arealetter",$letterHasArray[$j],$arv[0][$n]);
								$videoListStr=str_replace("areatype","all",$videoListStr);
								$singleStrAreaList=str_replace($arv[0][$n],$videoListStr,$singleStrAreaList);
							}
						}
						$totalStrAreaList=$totalStrAreaList.$singleStrAreaList;
					}
				}
			}elseif($areaType=="tag"){
				if(empty($tagHas))
				$tagHas="all";
				$tagHas=explode(",", $tagHas);
				foreach($tagHas as $j=>$tg)
				{
					if(!empty($tg))
					{
						$k=$j+1;
						$tmp=$loopArealist;
						$tmp=str_replace("[arealist:i]",$k,$tmp);
						$tmp=str_replace("[arealist:typename]",$tagHas[$j],$tmp);
						$row=array();
						if(strpos($tmp,"[arealist:count]")>0){
							$row=$this->dsql->GetOne("SELECT COUNT(*) as dd FROM sea_data WHERE v_name LIKE '%".$tagHas[$j]."%' OR m_actor like '%".$tagHas[$j]."%' OR  m_director like '%".$tagHas(j)."%'");
						}
						$tmp=str_replace("[arealist:count]",$row['dd'],$tmp);
						$tmp=str_replace("[arealist:link]","/".$GLOBALS['cfg_cmspath']."search.php?searchword=".$tagHas[$j],$tmp);
						preg_match_all($labelRuleVideolist,$tmp,$arv);
						$arvlen=count($arv[1]);
						for($n=0;$n<$arvlen;$n++){
							$videoListStr=str_replace("areatag",$tagHas[$j],$arv[0][$n]);
							$videoListStr=str_replace("areatype","all",$videoListStr);
							$tmp=str_replace($arv[0][$n],$videoListStr,$tmp);
						}
						$totalStrAreaList=$totalStrAreaList.$tmp;
					}
				}
			}else{
				if($areaType=="all"){$areaTypeArray=explode(",",$typeStr);}else{$areaTypeArray=explode(",",$areaType);}
				$arealen=count($areaTypeArray);
				$k=0;
				for($j=0;$j<$arealen;$j++){
					$currentAreaType=intval(trim($areaTypeArray[$j]));
					if(empty($currentAreaType)){
						$singleStrAreaList="";
					}else{
						$k=$j+1;
						$singleStrAreaList=$loopArealist;
						$singleStrAreaList=str_replace("[arealist:i]",$k,$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typename]",getTypeName($currentAreaType),$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typeid]",$currentAreaType,$singleStrAreaList);
						if(strpos($singleStrAreaList,"[arealist:count]")>0) $singleStrAreaList=str_replace("[arealist:count]",getNumPerType($currentAreaType),$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:link]",getChannelPagesLink($currentAreaType),$singleStrAreaList);
						preg_match_all($labelRuleVideolist,$singleStrAreaList,$arv);
						$arvlen=count($arv[1]);
						for($n=0;$n<$arvlen;$n++){
							$videoListStr=str_replace("areatype",$currentAreaType,$arv[0][$n]);
							$singleStrAreaList=str_replace($arv[0][$n],$videoListStr,$singleStrAreaList);
						}
					}
					$totalStrAreaList=$totalStrAreaList.$singleStrAreaList;
				}
			}
			$content=str_replace($ar[0][$m],$totalStrAreaList,$content);
			$totalStrAreaList="";
			}
		return $content;
		}
	}

	function parseNewsAreaList($content){
		if (strpos($content,'{seacms:newsarealist')=== false){
			return $content;
		}else{
			$attrDictionary=array();
			$labelRule = buildregx("{seacms:newsarealist(.*?)}(.*?){/seacms:newsarealist}","is");
			$labelRuleVideolist=buildregx("{seacms:newslist(.*?)}(.*?){/seacms:newslist}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			$typeStr=getTypeId(0,1);
			$tids=getMenuArray($typeStr,'tid',1);
			for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopArealist=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$areaType=empty($attrDictionary["areatype"]) ? "all" : $attrDictionary["areatype"];
			$letterHas=$attrDictionary["letter"];
			if (!empty($letterHas)) $areaType="letter";
			if($areaType=="letter"){
				if (empty($letterHas) || $letterHas=="all") $letterHas="A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
				$letterHasArray=explode(",",strtoupper($letterHas));
				$letterlen=count($letterHasArray);
				$k=0;
				for($j=0;$j<$letterlen;$j++){
					if(!empty($letterHasArray[$j])){
						$k=$j+1;
						$singleStrAreaList=$loopArealist;
						$singleStrAreaList=str_replace("[arealist:i]",$k,$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typename]",$letterHasArray[$j],$singleStrAreaList);
						if(strpos($singleStrAreaList,"[arealist:count]")>0){
							$row=$this->dsql->GetOne("select count(*) as dd from `sea_news` where n_letter='".$letterHasArray[$j]."'");
							$singleStrAreaList=str_replace("[arealist:count]",$row['dd'],$singleStrAreaList);
							$singleStrAreaList=str_replace("[arealist:link]","/".$GLOBALS['cfg_cmspath']."search.php?searchtype=4&searchword=".$letterHasArray[$j],$singleStrAreaList);
							preg_match_all($labelRuleVideolist,$singleStrAreaList,$arv);
							$arvlen=count($arv[1]);
							for($n=0;$n<$arvlen;$n++){
								$videoListStr=str_replace("arealetter",$letterHasArray[$j],$arv[0][$n]);
								$videoListStr=str_replace("areatype","all",$videoListStr);
								$singleStrAreaList=str_replace($arv[0][$n],$videoListStr,$singleStrAreaList);
							}
						}
						$totalStrAreaList=$totalStrAreaList.$singleStrAreaList;
					}
				}
			}else{
				if($areaType=="all"){$areaTypeArray=explode(",",$typeStr);}else{$areaTypeArray=explode(",",$areaType);}
				$arealen=count($areaTypeArray);
				$k=0;
				for($j=0;$j<$arealen;$j++){
					$currentAreaType=intval(trim($areaTypeArray[$j]));
					if(empty($currentAreaType)){
						$singleStrAreaList="";
					}else{
						$k=$j+1;
						$singleStrAreaList=$loopArealist;
						$singleStrAreaList=str_replace("[arealist:i]",$k,$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typename]",getNewsTypeName($currentAreaType),$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:typeid]",$currentAreaType,$singleStrAreaList);
						if(strpos($singleStrAreaList,"[arealist:count]")>0) $singleStrAreaList=str_replace("[arealist:count]",getNumPerTypeOfNews($currentAreaType),$singleStrAreaList);
						$singleStrAreaList=str_replace("[arealist:link]",getnewspageLink($currentAreaType),$singleStrAreaList);
						preg_match_all($labelRuleVideolist,$singleStrAreaList,$arv);
						$arvlen=count($arv[1]);
						for($n=0;$n<$arvlen;$n++){
							$videoListStr=str_replace("areatype",$currentAreaType,$arv[0][$n]);
							$singleStrAreaList=str_replace($arv[0][$n],$videoListStr,$singleStrAreaList);
						}
					}
					$totalStrAreaList=$totalStrAreaList.$singleStrAreaList;
				}
			}
			$content=str_replace($ar[0][$m],$totalStrAreaList,$content);
			$totalStrAreaList="";
			}
		return $content;
		}
	}
	
	function parseAttr($attrStr){
		$attrArray=explode(' ', $attrStr);
		$strLen=count($attrArray);
		for($i=0; $i<$strLen; $i++){
		$singleAttr=explode(chr(61),$attrArray[$i]);
		$singleAttrKey=$singleAttr[0];
		$singleAttrValue=$singleAttr[1];
		$attrDictionary[$singleAttrKey]=$singleAttrValue;
		}
		return $attrDictionary;
	}

	function parseNewsList($content,$currentTypeId=-444,$vtag){
		if (strpos($content,'{seacms:newslist')=== false){
			return $content;
		}else{
		global $cfg_issqlcache;
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:newslist(.*?)}(.*?){/seacms:newslist}","is");
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopstrVideoList=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$vnum=empty($attrDictionary["num"]) ? 10 : intval($attrDictionary["num"]);
			$vorder=$attrDictionary["order"];
			$vtype=empty($attrDictionary["type"]) ? "all" : $attrDictionary["type"];
			$vtime=empty($attrDictionary["time"]) ? "time" : $attrDictionary["time"];
			$vstart=empty($attrDictionary["start"]) ? 0 : intval($attrDictionary["start"])-1;
			$vid=$attrDictionary["id"];
			$vcommend=$attrDictionary["commend"];
			$vletter=$attrDictionary["letter"];
			$vrel=$attrDictionary["rel"];
			unset($attrDictionary);
			switch ($vorder) {
				case "id":
					$orderStr =" order by n_id desc";
				break;
				case "hit":
					$orderStr =" order by n_hit desc";
				break;
				case "time":
					$orderStr =" order by n_addtime desc";
				break;
				case "commend":
					$orderStr =" order by n_commend desc";
				break;
				case "digg":
					$orderStr =" order by n_digg desc";
				break;
				case "hot":
					$orderStr =" order by n_hit desc";
				break;
				case "random":
					$orderStr =" order by rand() desc";
				break;
			}
			$vtypeStr="";
			if($vtype!="all"){
				if ($vtype=="current"){
					$vtypeStr=getTypeId($currentTypeId);
				}else{
					if(strpos($vtype,',')>0){
					$vtypeArray=explode(",",$vtype);
					foreach($vtypeArray as $vtypestra){
					$vtypeStr=$vtypeStr.getTypeId($vtypestra).",";
					}
					$vtypeStr=rtrim($vtypeStr,",");
					}else{
					$vtypeStr=getTypeId($vtype);
					}
				}
				if(strpos($vtypeStr,',')>0) $whereType=" and m.tid in ($vtypeStr) "; else $whereType=" and m.tid='$vtypeStr' ";
			}else{
				$whereType="";
			}
			if(!empty($vid)){				
				if(strpos($vid,',')>0) $whereId=" and m.n_id in ($vid) "; else $whereId=" and m.n_id='$vid' ";
			}else{
				$whereId="";
			}
			if(!empty($vtag) AND $vrel=="n"){				
				$whereVtag=" and m.n_keyword ='$vtag'"; 
			}else{
				$whereVtag="";
			}
			if(!empty($vletter)) $whereLetter=" and m.n_letter ='".strtoupper($vletter)."' "; else $whereLetter="";
			if(!empty($vcommend)){
				switch (trim($vcommend)) {
					case "all":
						$whereCommend=" and  m.n_commend>0";
					break;
					default:
						if(strpos($vcommend,',')>0) $whereCommend=" and m.n_commend in($vcommend)"; else $whereCommend=" and m.n_commend='$vcommend'";
				}
			}else{
				$whereCommend="";
			}
			switch (trim($vtime)) {
				case "day":
					$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
					$limitday = $ntime - (1 * 24 * 3600);
					$whereTime=" and m.n_addtime>".$limitday;
				break;
				case "week":
					$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
					$limitday = $ntime - (7 * 24 * 3600);
					$whereTime=" and m.n_addtime>".$limitday;
				break;
				case "month":
					$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
					$limitday = $ntime - (30 * 24 * 3600);
					$whereTime=" and m.n_addtime>".$limitday;
				break;
				default:
					$whereTime="";
			}
			$whereStr=str_replace("where  and ","where "," where m.n_recycled=0".$whereType.$whereLetter.$whereTime.$whereCommend.$whereId.$whereVtag);
			if(trim($whereStr)=="where") $whereStr="";
			$sql="select m.n_id,m.n_title,m.n_pic,m.tid,m.n_hit,m.n_author,m.n_color,m.n_addtime ,m.n_commend,m.n_note,m.n_letter,m.n_content,m.n_outline,m.n_from from sea_news m ".$whereStr.$orderStr." LIMIT $vstart,$vnum";
			if($cfg_issqlcache){
			$mycachefile=md5('videolist'.$whereStr.$orderStr.$vstart.$vnum);
			setCache($mycachefile,$sql);
			$rows=getCache($mycachefile);
			}else{
			$rows=array();
			$this->dsql->SetQuery($sql);
			$this->dsql->Execute('al');
			while($rowr=$this->dsql->GetObject('al'))
			{
			$rows[]=$rowr;
			}
			unset($rowr);
			}
			$labelRuleField = buildregx("\[newslist:(.*?)\]","is");
			preg_match_all($labelRuleField,$content,$lar);
			$matchfieldarr=$lar[1];
			$matchfieldstrarr=$lar[0];
			$loopstrTotal="";
			$i=$vstart+1;
			$n=1;
			foreach($rows as $row)
			{
				$loopstrVlistNew=$loopstrVideoList;
				foreach($matchfieldarr as $f=>$matchfieldstr){
					$matchfieldvalue=$matchfieldstrarr[$f];
					$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
					if (strpos($matchfieldstr," ")>0){
						$fieldtemparr=explode(" ",$matchfieldstr);
						$fieldName=$fieldtemparr[0];
						$fieldAttr =$fieldtemparr[1];
					}else{
						$fieldName=$matchfieldstr;
						$fieldAttr ="";
					}
					switch (trim($fieldName)) {
						case "i":
							$loopstrVlistNew=str_replace($matchfieldvalue,$i,$loopstrVlistNew);
						break;
						case "n":
							$loopstrVlistNew=str_replace($matchfieldvalue,$n,$loopstrVlistNew);
						break;
						case "id":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_id,$loopstrVlistNew);
						break;
						case "typename":
							$loopstrVlistNew=str_replace($matchfieldvalue,getNewsTypeName($row->tid),$loopstrVlistNew);
						break;
						case "typelink":
							$loopstrVlistNew=str_replace($matchfieldvalue,getnewspageLink($row->tid),$loopstrVlistNew);
						break;
						case "name":
						case "title":
							$v_name=$row->n_title;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_name=!empty($namelen)&&strlen($v_name)>$namelen ? trimmed_title($v_name,$namelen) : $v_name;
							$loopstrVlistNew=str_replace($matchfieldvalue,$v_name,$loopstrVlistNew);
						break;
						case "colorname":
						case "colortitle":
							$v_color=$row->n_color;
							$v_name=$row->n_title;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$colornamelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_name=!empty($colornamelen)&&strlen($v_name)>$colornamelen ? trimmed_title($v_name,$colornamelen) : $v_name;
							$v_name=$v_color ? "<font color=".$v_color.">".$v_name."</font>" : $v_name;
							$loopstrVlistNew=str_replace($matchfieldvalue,$v_name,$loopstrVlistNew);
						break;
						case "note":
							$v_note=$row->n_note;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$notelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_note=!empty($notelen)&&strlen($v_note)>$notelen ? trimmed_title($v_note,$notelen) : $v_note;
							$loopstrVlistNew=str_replace($matchfieldvalue,$v_note,$loopstrVlistNew);
						break;
						case "link":
						case "newslink":
						case "playlink":
							$loopstrVlistNew=str_replace($matchfieldvalue,getArticleLink($row->tid,$row->n_id,''),$loopstrVlistNew);
						break;
						case "pic":
							$v_pic=$row->n_pic;
							if(!empty($v_pic)){
								if(strpos(' '.$v_pic,'://')>0){
									$loopstrVlistNew=str_replace($matchfieldvalue,$v_pic,$loopstrVlistNew);
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$loopstrVlistNew);
								}
							}else{
								$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrVlistNew);
							}
						break;
						case "author":
							$v_actor=$row->n_author;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
							$loopstrVlistNew=str_replace($matchfieldvalue,$v_actor,$loopstrVlistNew);
						break;
						case "des":
						case "content":
							$v_des=Html2Text($row->n_content);
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$deslen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
							$v_des=preg_replace('/\{news\:video(.*?)\}/is','',$v_des);
							$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
							$loopstrVlistNew=str_replace($matchfieldvalue,$v_des,$loopstrVlistNew);
						break;
						case "outline":
							$outline=Html2Text($row->n_outline);
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$deslen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
							$outline=!empty($deslen)&&strlen($outline)>$deslen ? trimmed_title($outline,$deslen) : $outline;
							$loopstrVlistNew=str_replace($matchfieldvalue,$outline,$loopstrVlistNew);
						break;
						case "hit":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_hit,$loopstrVlistNew);
						break;
						case "addtime":
						case "time":
							$timestyle="";
							$videoTime=$row->n_addtime;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
							switch (trim($timestyle)) {
									case "yyyy-mm-dd":
										$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrVlistNew);
									break;
									case "yy-mm-dd":
										$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrVlistNew);
									break;
									case "yyyy-m-d":
										$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrVlistNew);
									break;
									case "mm-dd":
									default:
										$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrVlistNew);
								}
						break;
						case "from":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_from,$loopstrVlistNew);
						break;
						case "commend":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_commend,$loopstrVlistNew);
						break;
						case "letter":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_letter,$loopstrVlistNew);
						break;
						case "digg":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_digg,$loopstrVlistNew);
						break;
						case "tread":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_tread,$loopstrVlistNew);
						break;
						case "scorenum":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_score,$loopstrVlistNew);
						break;
						case "scorenumer":
							$loopstrVlistNew=str_replace($matchfieldvalue,$row->n_scorenum,$loopstrVlistNew);
						break;
						case "score":
							$score=number_format($row->n_score/$row->n_scorenum,1);
							$loopstrVlistNew=str_replace($matchfieldvalue,$score,$loopstrVlistNew);
						break;
						
					}
				
				
				}
				$i=$i+1;
				$n=$n+1;
				$loopstrTotal=$loopstrTotal.$loopstrVlistNew;
			}
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
			}
			return $content;
		}
	}
	
	function parseVideoList($content,$currentTypeId=-444,$topicId=10,$vtag){
		if (strpos($content,'{seacms:videolist')=== false){
			return $content;
		}else{
			global $cfg_issqlcache,$cfg_runmode,$id,$vid;
			$attrDictionary=array();
			$labelRule = buildregx("{seacms:videolist(.*?)}(.*?){/seacms:videolist}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			for($m=0;$m<$arlen;$m++){
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
				$loopstrVideoList=$ar[2][$m];
				if (strpos($loopstrVideoList,"[videolist:des")>0){
					$field_des="c.body as v_content";
					$left_des=" left join `sea_content` c on c.v_id=m.v_id ";
				}else{
					$field_des=0;
					$left_des="";
				}
				if (strpos($loopstrVideoList,"[videolist:from]")>0){
					$field_playdata="p.body as v_playdata";
					$left_playdata=" left join `sea_playdata` p on p.v_id=m.v_id ";
				}else{
					$field_playdata=0;
					$left_playdata="";
				}
				$attrDictionary=$this->parseAttr($attrStr);
				$vnum=empty($attrDictionary["num"]) ? 10 : intval($attrDictionary["num"]);
				$vorder=$attrDictionary["order"];
				$vtype=empty($attrDictionary["type"]) ? "all" : $attrDictionary["type"];
				$vtime=empty($attrDictionary["time"]) ? "time" : $attrDictionary["time"];
				$vstart=empty($attrDictionary["start"]) ? 0 : intval($attrDictionary["start"])-1;
				$vstate=!isset($attrDictionary["state"]) ? "all" : $attrDictionary["state"];
				$vcommend=$attrDictionary["commend"];
				$vletter=$attrDictionary["letter"];
				$vlang=$attrDictionary["lang"];
				$varea=$attrDictionary["area"];
				$vyear=$attrDictionary["year"];
				$vsid=$attrDictionary["sid"];
				$vtopic=$attrDictionary["zt"];
				$vjq=$attrDictionary["jq"];
				$vtvs=$attrDictionary["tvs"];
				$vver=$attrDictionary["ver"];
				$vcompany=$attrDictionary["company"];
				$vreweek=$attrDictionary["reweek"];
				$vrel=$attrDictionary["rel"];
				unset($attrDictionary);
				switch ($vorder) {
					case "id":
						$orderStr =" order by v_id desc";
					break;
					case "year":
					$orderStr =" order by v_publishyear desc";
					break;
					case "scorenum":
					$orderStr =" order by v_scorenum desc";
					break;
					case "hit":
						$orderStr =" order by v_hit desc";
					break;
					case "dayhit":
						$orderStr =" order by v_dayhit desc";
					break;
					case "weekhit":
						$orderStr =" order by v_weekhit desc";
					break;
					case "monthhit":
						$orderStr =" order by v_monthhit desc";
					break;
					case "time":
						$orderStr =" order by v_addtime desc";
					break;
					case "commend":
						$orderStr =" order by v_commend desc";
					break;
					case "digg":
						$orderStr =" order by v_digg desc";
					break;
					case "hot":
						$orderStr =" order by v_hit desc";
					break;
					case "score":
						$orderStr =" order by v_score desc";
					break;
					case "douban":
						$orderStr =" order by v_douban desc";
					break;
					case "mtime":
						$orderStr =" order by v_mtime desc";
					break;
					case "imdb":
						$orderStr =" order by v_imdb desc";
					break;
					case "idasc":
						$orderStr =" order by v_id asc";
					break;
					case "yearasc":
					$orderStr =" order by v_publishyear asc";
					break;
					case "scorenumasc":
					$orderStr =" order by v_scorenum asc";
					break;
					case "hitasc":
						$orderStr =" order by v_hit asc";
					break;
					case "timeasc":
						$orderStr =" order by v_addtime asc";
					break;
					case "commendasc":
						$orderStr =" order by v_commend asc";
					break;
					case "diggasc":
						$orderStr =" order by v_digg asc";
					break;
					case "hotasc":
						$orderStr =" order by v_hit asc";
					break;
					case "random":
						$orderStr =" order by rand() desc";
					break;
				}
				$vtypeStr="";
				$extrasql = "";
				if($vtype!="all"){
					if ($vtype=="current"){
						$vtypeStr=getTypeId($currentTypeId);
					}else{
						if(strpos($vtype,',')>0){
							$vtypeArray=explode(",",$vtype);
							foreach($vtypeArray as $vtypestra){
								$vtypeStr=$vtypeStr.getTypeId($vtypestra).",";
								$extrasql .= " or FIND_IN_SET('".$vtypestra."',m.v_extratype)<>0 ";
							}
							$vtypeStr=rtrim($vtypeStr,",");
						}else{
							$vtypeStr=getTypeId($vtype);
							$extrasql .= " or FIND_IN_SET('".$vtypeStr."',m.v_extratype)<>0 ";
						}
					}
					if($currentTypeId!="")$extrasql .= " or FIND_IN_SET('".$currentTypeId."',m.v_extratype)<>0 ";
					if(strpos($vtypeStr,',')>0) $whereType=" and (m.tid in ($vtypeStr) ".$extrasql.") "; else $whereType=" and (m.tid='$vtypeStr'  ".$extrasql.") ";
				}else{
					$whereType="";
				}
				if(!empty($vrel))
				{
					$zid=$GLOBALS['zid'];	
				if($id !=0 or $id !="")
					{$sql="select v_name,v_actor,v_director from sea_data where v_id=$id limit 1";}
				if($vid !=0 or $vid !="")
					{$sql="select v_name,v_actor,v_director from sea_data where v_id=$vid limit 1";}
				if($zid !=0 or $zid !="")
					{$sql="select v_name,v_actor,v_director from sea_data where v_id=$zid limit 1";}
					$zrel=array();
					$this->dsql->SetQuery($sql);
					$this->dsql->Execute('al');
					while($relr=$this->dsql->GetObject('al'))
					{
					$zrel[]=$relr;
					}
					
					$rel_d = explode(",",str_replace(" ",",",$zrel['0']->v_director));
					$rel_y = explode(",",str_replace(" ",",",$zrel['0']->v_actor));
					$rel_r = substr($zrel['0']->v_name,0,9);
					unset($zrel);
					switch ($vrel) 
					{
						case "d":
							foreach($rel_d as $value)
							{
								$d_str .= "'%".$value."%'#";
							}
							$d_str=rtrim($d_str,"#");
							$d_str=str_replace("#"," or m.v_director like ",$d_str);
							$whereRel=" and m.v_director like $d_str ";
						break;
						case "y":
							foreach($rel_y as $value)
							{
								$y_str .= "'%".$value."%'#";
							}
							$y_str=rtrim($y_str,"#");
							$y_str=str_replace("#"," or m.v_actor like ",$y_str);
							$whereRel=" and m.v_actor like $y_str ";
						break;
						case "r":
							$whereRel=" and m.v_name like '%$rel_r%'";
						break;
					}
				}
				else
				{
					$whereRel="";
				}

				if($vrel=="v")
				{
					$whereNtag=" and m.v_name = '$vtag'";
				}
				else{
					$whereNtag="";
				}
				
				if(!empty($vletter)) $whereLetter=" and m.v_letter ='".strtoupper($vletter)."' "; else $whereLetter="";
				if(!empty($vlang)) $whereLang=" and m.v_lang ='".strtoupper($vlang)."' "; else $whereLang="";
				if(!empty($varea)) $whereArea=" and m.v_publisharea ='".strtoupper($varea)."' "; else $whereArea="";
				if(!empty($vyear)) $whereYear=" and m.v_publishyear ='".strtoupper($vyear)."' "; else $whereYear="";
				if(!empty($vjq)) $whereJq=" and m.v_jq like '%$vjq%'"; else $whereJq="";
				if(!empty($vreweek)) $whereReweek=" and m.v_reweek like '%$vreweek%'"; else $whereReweek="";
				if(!empty($vtvs)) $whereTvs=" and m.v_tvs ='".strtoupper($vtvs)."' "; else $whereTvs="";
				if(!empty($vver)) $whereVer=" and m.v_ver ='".strtoupper($vver)."' "; else $whereVer="";
				if(!empty($vcompany)) $whereCompany=" and m.v_company ='".strtoupper($vcompany)."' "; else $whereCompany="";
				if(!empty($vstate))
				{
					switch(trim($vstate))
					{
						case "l":
							$whereState=" and m.v_state>0"; 
						break;
						case "w":
							$whereState=" and m.v_state=0"; 
						break;
						case "all":
							$whereState="";
						break;
						
					}
				}else{
					$whereState="";
				}
				if(!empty($vcommend)){
				switch (trim($vcommend)) {
					case "all":
						$whereCommend=" and  m.v_commend>0";
					break;
					default:
						if(strpos($vcommend,',')>0) $whereCommend=" and m.v_commend in($vcommend)"; else $whereCommend=" and m.v_commend='$vcommend'";
				}
				}else{
							$whereCommend="";
				}
				//按专题
				if(!empty($vtopic))
				{
					$sql="select vod from sea_topic where id=$vtopic limit 1";
					$this->dsql->SetQuery($sql);
					$this->dsql->Execute('al');
					$relr=$this->dsql->GetObject('al');
					$topic_vid = str_replace("ttttt",",",$relr->vod);
					$topic_vid = str_replace("0,","",$topic_vid);
					if(!empty($topic_vid) AND $topic_vid !="")
					{	
					$whereTopic=" and  m.v_id in ($topic_vid)";
					}
					else{
						$whereTopic=" and  m.v_id = 0";
						}
				
				}
				else{
							$whereTopic="";
				}
				
				//按指定id
				if(!empty($vsid))
				{
					
					$whereSid=" and  m.v_id in ($vsid)";
				
				}
				else{
							$whereSid="";
				}

				switch (trim($vtime)) {
					case "day":
						$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
						$limitday = $ntime - (1 * 24 * 3600);
						$whereTime=" and m.v_addtime>".$limitday;
					break;
					case "week":
						$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
						$limitday = $ntime - (7 * 24 * 3600);
						$whereTime=" and m.v_addtime>".$limitday;
					break;
					case "month":
						$ntime = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
						$limitday = $ntime - (30 * 24 * 3600);
						$whereTime=" and m.v_addtime>".$limitday;
					break;
					default:
						$whereTime="";
				}
				$whereStr=str_replace("where  and ","where "," where m.v_recycled=0".$whereType.$whereLetter.$whereLang.$whereArea.$whereYear.$whereTopic.$whereTime.$whereState.$whereCommend.$whereJq.$whereReweek.$whereTvs.$whereCompany.$whereRel.$whereSid.$whereNtag.$whereVer);
				if(trim($whereStr)=="where") $whereStr="";
				$sql="select m.*,".$field_des.",".$field_playdata." from sea_data m ".$left_des.$left_playdata.$whereStr.$orderStr." LIMIT $vstart,$vnum";
				//echo $sql.'<br>';
				if($cfg_issqlcache||$cfg_runmode=='0'){
				$mycachefile=md5('videolist'.$whereStr.$orderStr.$vstart.$vnum);
				setCache($mycachefile,$sql);
				$rows=getCache($mycachefile);
				}else{
				$rows=array();
				$this->dsql->SetQuery($sql);
				$this->dsql->Execute('al');
				while($rowr=$this->dsql->GetObject('al'))
				{
				$rows[]=$rowr;
				}
				unset($rowr);
				}
				$labelRuleField = buildregx("\[videolist:(.*?)\]","is");
				preg_match_all($labelRuleField,$content,$lar);
				$matchfieldarr=$lar[1];
				$matchfieldstrarr=$lar[0];
				$loopstrTotal="";
				$i=$vstart+1;
				$n=1;
				foreach($rows as $row)
				{
					$loopstrVlistNew=$loopstrVideoList;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "id":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_id,$loopstrVlistNew);
							break;
							case "typeid":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->tid,$loopstrVlistNew);
							break;
							case "typename":
								$loopstrVlistNew=str_replace($matchfieldvalue,getTypeNameOnCache($row->tid),$loopstrVlistNew);
							break;
							case "director":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_director,$loopstrVlistNew);
							break;
							case "money":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_money,$loopstrVlistNew);
							break;
							case "lang":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_lang,$loopstrVlistNew);
							break;
							case "letter":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_letter,$loopstrVlistNew);
							break;
							case "name":
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($namelen)&&strlen($v_name)>$namelen ? trimmed_title($v_name,$namelen) : $v_name;
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_name,$loopstrVlistNew);
							break;
							case "colorname":
								$v_color=$row->v_color;
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$colornamelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($colornamelen)&&strlen($v_name)>$colornamelen ? trimmed_title($v_name,$colornamelen) : $v_name;
								$v_name=$v_color ? "<font color=".$v_color.">".$v_name."</font>" : $v_name;
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_name,$loopstrVlistNew);
							break;
							case "note":
								$v_note=$row->v_note;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$notelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_note=!empty($notelen)&&strlen($v_note)>$notelen ? trimmed_title($v_note,$notelen) : $v_note;
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_note,$loopstrVlistNew);
							break;
							case "actor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$v_actor=getKeywordsList($v_actor,"&nbsp;");
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_actor,$loopstrVlistNew);
							break;
							case "nolinkactor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_actor,$loopstrVlistNew);
							break;
							case "des":
								$v_des=htmlspecialchars_decode($row->v_content);
								$v_des=Html2Text($v_des);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
								$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_des,$loopstrVlistNew);
							break;
							case "time":
								$timestyle="";
								$videoTime=$row->v_addtime;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
								switch (trim($timestyle)) {
									case "yyyy-mm-dd":
								$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrVlistNew);
									break;
									case "yy-mm-dd":
								$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrVlistNew);
									break;
									case "yyyy-m-d":
								$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrVlistNew);
									break;
									case "mm-dd":
									default:
								$loopstrVlistNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrVlistNew);
								}
							break;
							case "i":
								$loopstrVlistNew=str_replace($matchfieldvalue,$i,$loopstrVlistNew);
							break;
							case "n":
								$loopstrVlistNew=str_replace($matchfieldvalue,$n,$loopstrVlistNew);
							break;
							case "typelink":
								$loopstrVlistNew=str_replace($matchfieldvalue,getChannelPagesLink($row->tid),$loopstrVlistNew);
							break;
							case "link":
								$loopstrVlistNew=str_replace($matchfieldvalue,getContentLink($row->tid,$row->v_id,"link",date('Y-n',$row->v_addtime),$row->v_enname),$loopstrVlistNew);
							break;
							case "playlink":
								if($GLOBALS['cfg_isalertwin']==1) $playlink_str="javascript:openWin('".getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname)."',".($GLOBALS['cfg_alertwinw']).",".($GLOBALS['cfg_alertwinh']).",250,100,1)"; else $playlink_str=getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname);
								$loopstrVlistNew=str_replace($matchfieldvalue,$playlink_str,$loopstrVlistNew);
							break;								
							case "favLink":
								@session_start();
								if(isset($_SESSION['sea_user_auth'])||$GLOBALS['cfg_user']==1)
								{														
									$uid=$_SESSION['sea_user_id'];
									$favLink_str = "javascript:AddFav('".$row->v_id."','$uid')";
								}else
								{
									$favLink_str = "#";
								}
								$loopstrVlistNew=str_replace($matchfieldvalue,$favLink_str,$loopstrVlistNew);
							break;
							case "pic":
								$v_pic=$row->v_pic;
								if(!empty($v_pic)){
								if(strpos(' '.$v_pic,'://')>0){
									$loopstrVlistNew=str_replace($matchfieldvalue,$v_pic,$loopstrVlistNew);
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$loopstrVlistNew);
								}
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrVlistNew);
								}
							break;
							case "spic":
								$v_spic=$row->v_spic;
								if(!empty($v_spic)){
								if(strpos(' '.$v_spic,'://')>0){
									$loopstrVlistNew=str_replace($matchfieldvalue,$v_spic,$loopstrVlistNew);
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_spic,'/'),$loopstrVlistNew);
								}
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrVlistNew);
								}
							break;
							case "gpic":
								$v_gpic=$row->v_gpic;
								if(!empty($v_gpic)){
								if(strpos(' '.$v_gpic,'://')>0){
									$loopstrVlistNew=str_replace($matchfieldvalue,$v_gpic,$loopstrVlistNew);
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_gpic,'/'),$loopstrVlistNew);
								}
								}else{
									$loopstrVlistNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrVlistNew);
								}
							break;
							case "hit":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_hit,$loopstrVlistNew);
							break;
							case "dayhit":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_dayhit,$loopstrVlistNew);
							break;
							case "weekhit":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_weekhit,$loopstrVlistNew);
							break;
							case "monthhit":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_monthhit,$loopstrVlistNew);
							break;
							case "nickname":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_nickname,$loopstrVlistNew);
							break;
							case "reweek":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_reweek,$loopstrVlistNew);
							break;
							case "vodlen":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_len,$loopstrVlistNew);
							break;
							case "vodtotal":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_total,$loopstrVlistNew);
							break;
							case "douban":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_douban,$loopstrVlistNew);
							break;
							case "mtime":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_mtime,$loopstrVlistNew);
							break;
							case "imdb":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_imdb,$loopstrVlistNew);
							break;
							case "tvs":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_tvs,$loopstrVlistNew);
							break;
							case "company":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_company,$loopstrVlistNew);
							break;
							case "state":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_state,$loopstrVlistNew);
							break;
							case "publishtime":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_publishyear,$loopstrVlistNew);
							break;
							case "ver":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_ver,$loopstrVlistNew);
							break;
							case "publisharea":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_publisharea,$loopstrVlistNew);
							break;
							case "commend":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_commend,$loopstrVlistNew);
							break;
							case "digg":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_digg,$loopstrVlistNew);
							break;
							case "tread":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_tread,$loopstrVlistNew);
							break;
							case "scorenum":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_score,$loopstrVlistNew);
							break;
							case "scorenumer":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_scorenum,$loopstrVlistNew);
							break;
							case "score":
								$score=number_format($row->v_score/$row->v_scorenum,1);
								$loopstrVlistNew=str_replace($matchfieldvalue,$score,$loopstrVlistNew);
							break;
							case "from":
								$loopstrVlistNew=str_replace($matchfieldvalue,getFromStr($row->v_playdata),$loopstrVlistNew);
							break;
							case "keyword":
								$v_tags=getKeywordsList($row->v_tags,"&nbsp;");
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_tags,$loopstrVlistNew);
							break;
							case "nolinkkeyword":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_tags,$loopstrVlistNew);
							break;
							case "jqtype":
								$v_jq=getJqList($row->v_jq,"&nbsp;");
								$loopstrVlistNew=str_replace($matchfieldvalue,$v_jq,$loopstrVlistNew);
							break;
							case "nolinkjqtype":
								$loopstrVlistNew=str_replace($matchfieldvalue,$row->v_jq,$loopstrVlistNew);
							break;
						}
					}
					$i=$i+1;
					$n=$n+1;
					$loopstrTotal=$loopstrTotal.$loopstrVlistNew;
				}
				unset($rows);
				$content=str_replace($ar[0][$m],$loopstrTotal,$content);
			}
			return $content;
		}	
	}

	function parsePageList($content,$typeId,$currentPage,$TotalPage,$TotalResult,$pageListType,$currentTypeId=-444)
	{
		global $cfg_issqlcache,$lang;
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:".$pageListType."list(.*?)}(.*?){/seacms:".$pageListType."list}","is");
		$labelRuleField="\[".$pageListType."list:(.*?)\]";
		$labelRulePagelist="\[".$pageListType."list:pagenumber(.*?)\]";
		if (strpos($content,"[".$pageListType."list:des")>0){
			$field_des="c.body as v_content";
			$left_des=" left join `sea_content` c on c.v_id=m.v_id ";
		}else{
			$field_des=0;
			$left_des="";
		}
		if (strpos($content,"[".$pageListType."list:from")>0){
			$field_playdata="p.body as v_playdata";
			$left_playdata=" left join `sea_playdata` p on p.v_id=m.v_id ";
		}else{
			$field_playdata=0;
			$left_playdata="";
		}
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopstrChannel=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$vsize=empty($attrDictionary["size"]) ? 12 : intval($attrDictionary["size"]);
			$vorder=empty($attrDictionary["order"]) ? "time" : $attrDictionary["order"];
			unset($attrDictionary);
			if(intval($currentPage)>intval($TotalPage)) $currentPage=$TotalPage;
			$limitstart = ($currentPage-1) * $vsize;
			if($limitstart<0) $limitstart=0;
			switch ($vorder) {
				case "id":
					$orderStr =" order by m.v_id desc";
				break;
				case "year":
					$orderStr =" order by m.v_publishyear desc";
				break;
				case "hit":
					$orderStr =" order by m.v_hit desc";
				break;
				case "dayhit":
					$orderStr =" order by v_dayhit desc";
				break;
				case "weekhit":
					$orderStr =" order by v_weekhit desc";
				break;
				case "monthhit":
					$orderStr =" order by v_monthhit desc";
				break;				
				case "time":
					$orderStr =" order by m.v_addtime desc";
				break;
				case "name":
					$orderStr =" order by m.v_name asc";
				break;
				case "digg":
					$orderStr =" order by m.v_digg desc";
				break;
				case "hot":
					$orderStr =" order by m.v_hit desc";
				break;
				case "score":
					$orderStr =" order by m.v_score desc";
				break;
				case "douban":
					$orderStr =" order by m.v_douban desc";
				break;
				case "mtime":
					$orderStr =" order by m.v_mtime desc";
				break;
				case "imdb":
					$orderStr =" order by m.v_imdb desc";
				break;
				case "letter":
					$orderStr =" order by m.v_letter asc";
				break;
				//asc
				case "idasc":
					$orderStr =" order by m.v_id asc";
				break;
				case "yearasc":
					$orderStr =" order by m.v_publishyear asc";
				break;
				case "hitasc":
					$orderStr =" order by m.v_hit asc";
				break;
				case "timeasc":
					$orderStr =" order by m.v_addtime asc";
				break;	
				case "diggasc":
					$orderStr =" order by m.v_digg asc";
				break;
				case "hotasc":
					$orderStr =" order by m.v_hit asc";
				break;
			}
			/****修复扩展分类不生成
			if($typeId!=""){
				$nowid = $typeId;
				
				if(strpos($typeId, ",")>0)
				{
					$tempid = preg_split('[,]',$typeId);
					$nowid = $tempid[0];
				}
				$extrasql = " or FIND_IN_SET('".$nowid."',m.v_extratype)<>0 ";	
				
			}
			else{
				$extrasql = "";
			}
			修复扩展分类不生成********/
			$extrasql = " or m.v_extratype in (".$typeId.")";
			switch ($pageListType) {
				case "channel":
					$whereStr=" where (m.tid in (".$typeId.") ".$extrasql.") and m.v_recycled=0 ";
				break;
				case "tag":
					$whereStr=" where m.v_recycled=0 and m.v_id in (".$typeId.")";
				break;
				case "search":
					global $searchtype,$searchword;
					switch (intval($searchtype)) {
						case -1:
							$whereStr=" where m.v_recycled=0 and (m.v_name like '%$searchword%' or m.v_actor like '%$searchword%' or m.v_director like '%$searchword%' or m.v_publisharea like '%$searchword%' or m.v_nickname like '%$searchword%'  or m.v_publishyear like '%$searchword%' or m.v_letter='$searchword' or m.v_tags='$searchword')";
						break;
						case 0:
							$whereStr=" where m.v_recycled=0 and m.v_name like '%$searchword%'";	
						break;
						case 1:
							$whereStr=" where m.v_recycled=0 and m.v_actor like '%$searchword%'";
						break;
						case 2:
							$whereStr=" where m.v_recycled=0 and m.v_publisharea like '%$searchword%'";
						break;
						case 3:
							$whereStr=" where m.v_recycled=0 and m.v_publishyear like '%$searchword%'";
						break;
						case 4:
							$whereStr=" where m.v_recycled=0 and m.v_letter='".strtoupper($searchword)."'";
						break;
					}
				break;
				case "cascade":
					global $tid,$year,$letter,$area,$yuyan,$order,$jq,$state,$money,$ver;
					if($year=="全部"){$year="";}
					if($letter=="全部"){$letter="";}
					if($area=="全部"){$area="";}
					if($yuyan=="全部"){$yuyan="";}
					if($jq=="全部"){$jq="";}
					if($state=="全部"){$state="";}
					if($money=="全部"){$money="";}
					if($ver=="全部"){$ver="";}
					$whereStr=" where v_recycled=0";
					if(!empty($tid)) $whereStr.=" and (m.tid in (".getTypeIdOnCache($tid).") or FIND_IN_SET('".$tid."',m.v_extratype)<>0)";
					if($year=="more")
					{
					$publishyeartxt=sea_DATA."/admin/publishyear.txt";
							$publishyear = array();
							if(filesize($publishyeartxt)>0)
							{
								$publishyear = file($publishyeartxt);
							}
							$yearArray=$publishyear;
							$yeartxt= implode(',',$yearArray);
							$whereStr.=" and v_publishyear not in ($yeartxt)";
					}
					if(!empty($year) AND $year!="more")
					{$whereStr.=" and v_publishyear='$year'";}
					
					if($letter=="0-9")
					{$whereStr.=" and v_letter in ('0','1','2','3','4','5','6','7','8','9')";}
					if(!empty($letter) AND $letter!="0-9")
					{$whereStr.=" and v_letter='$letter'";}
					
					if($state=='l') $whereStr.=" and v_state!=0";
					if($state=='w') $whereStr.=" and v_state=0";
					if($money=='s') $whereStr.=" and v_money!=0";
					if($money=='m') $whereStr.=" and v_money=0";
					if(!empty($ver) AND $ver!="") $whereStr.=" and v_ver='$ver'";
					if(!empty($area) AND $area!="") $whereStr.=" and v_publisharea='$area'";
					if(!empty($yuyan) AND $yuyan!="") $whereStr.=" and v_lang='$yuyan'";
					if(!empty($jq) AND $jq!="") $whereStr.=" and v_jq like '%$jq%'";
					if($order=='id') $orderStr=" order by v_id desc";
					if($order=='time') $orderStr=" order by v_addtime desc";
					if($order=='hit') $orderStr=" order by v_hit desc";
					if($order=='score') $orderStr=" order by v_score desc";
					if($order=='douban') $orderStr=" order by v_douban desc";
					if($order=='mtime') $orderStr=" order by v_mtime desc";
					if($order=='imdb') $orderStr=" order by v_imdb desc";
					if($order=='commend') $orderStr=" order by v_commend desc";
					if($order=='idasc') $orderStr=" order by v_id asc";
					if($order=='timeasc') $orderStr=" order by v_addtime asc";
					if($order=='hitasc') $orderStr=" order by v_hit asc";
					if($order=='commendasc') $orderStr=" order by v_commend asc";
				break;
				break;
				case "topicpage":
					
						//echo '====='.$typeId.'===='; //专题id
						$sql="select vod from sea_topic where id='$typeId'";
						$rows=array();
						$this->dsql->SetQuery($sql);
						$this->dsql->Execute('al');
						while($rowr=$this->dsql->GetObject('al'))
						{
						$rows[]=$rowr;
						}
						unset($rowr);
						$zpagevid= str_replace("ttttt",",",$rows[0]->vod);
						$whereStr=" where m.v_recycled=0 and m.v_id in ($zpagevid)";
				break;
			}
			$sql="select m.*,".$field_des.",".$field_playdata." from sea_data m ".$left_des.$left_playdata.$whereStr." ".$orderStr." limit $limitstart,$vsize";
			$labelRuleField = buildregx($labelRuleField,"is");
			preg_match_all($labelRuleField,$content,$lar);
			$matchfieldarr=$lar[1];
			$matchfieldstrarr=$lar[0];
			$loopstrTotal="";
			if($TotalResult==0){
				if($pageListType=="channel") $loopstrTotal=$lang['channellistInfo']['0'];
				if($pageListType=="search") $loopstrTotal=$lang['searchlistInfo']['0'].$searchword.$lang['searchlistInfo']['1'];
				if($pageListType=="topicpage") $loopstrTotal=$lang['topicpageInfo']['0'];
			}else{
				if($cfg_issqlcache){
					$mycachefile=md5('PageList'.$whereStr.$orderStr.$limitstart.$row);
					setCache($mycachefile,$sql);
					$rows=getCache($mycachefile);
				}else{
					$rows=array();
					$this->dsql->SetQuery($sql);
					$this->dsql->Execute($pageListType.'list');
					while($rowr=$this->dsql->GetObject($pageListType.'list'))
					{
						$rows[]=$rowr;
					}
					unset($rowr);
				}
				$i=1;
				foreach($rows as $row)
				{
					$loopstrChannelNew=$loopstrChannel;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "i":
								$loopstrChannelNew=str_replace($matchfieldvalue,$i,$loopstrChannelNew);
							break;
							case "id":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_id,$loopstrChannelNew);
							break;
							case "typeid":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->tid,$loopstrChannelNew);
							break;
							case "typename":
								$loopstrChannelNew=str_replace($matchfieldvalue,getTypeNameOnCache($row->tid).getExtraTypeName($row->v_extratype),$loopstrChannelNew);
							break;
							case "linktypename":
								$connector = "</a>";
								$loopstrChannelNew=str_replace($matchfieldvalue,"<a href=\"".getChannelPagesLink($row->tid)."\">".getTypeName($row->tid).$connector.getExtraTypeName($row->v_extratype,$connector).$connector,$loopstrChannelNew);
							break;
							case "typelink":
								$loopstrChannelNew=str_replace($matchfieldvalue,getChannelPagesLink($row->tid),$loopstrChannelNew);
							break;
							case "name":
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($namelen)&&strlen($v_name)>$namelen ? trimmed_title($v_name,$namelen) : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "colorname":
								$v_color=$row->v_color;
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$colornamelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($colornamelen)&&strlen($v_name)>$colornamelen ? trimmed_title($v_name,$colornamelen) : $v_name;
								$v_name=$v_color ? "<font color=".$v_color.">".$v_name."</font>" : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "note":
								$v_note=$row->v_note;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$notelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_note=!empty($notelen)&&strlen($v_note)>$notelen ? trimmed_title($v_note,$notelen) : $v_note;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_note,$loopstrChannelNew);
							break;
							case "link":
								$sdate = date('Y-n',$row->v_addtime);
								$loopstrChannelNew=str_replace($matchfieldvalue,getContentLink($row->tid,$row->v_id,"link",$sdate,$row->v_enname),$loopstrChannelNew);
							break;
							case "pic":
								$v_pic=$row->v_pic;
								if(!empty($v_pic)){
									if(strpos(' '.$v_pic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_pic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;
							case "spic":
								$v_spic=$row->v_spic;
								if(!empty($v_spic)){
									if(strpos(' '.$v_spic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_spic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_spic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;
							case "gpic":
								$v_gpic=$row->v_gpic;
								if(!empty($v_gpic)){
									if(strpos(' '.$v_gpic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_gpic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_gpic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;							
							case "actor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$v_actor=getKeywordsList($v_actor,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_actor,$loopstrChannelNew);
							break;
							case "nolinkactor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_actor,$loopstrChannelNew);
							break;
							case "hit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_hit,$loopstrChannelNew);
							break;
							case "dayhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_dayhit,$loopstrChannelNew);
							break;
							case "weekhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_weekhit,$loopstrChannelNew);
							break;
							case "monthhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_monthhit,$loopstrChannelNew);
							break;
							case "nickname":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_nickname,$loopstrChannelNew);
							break;
							case "reweek":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_reweek,$loopstrChannelNew);
							break;
							case "vodlen":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_len,$loopstrChannelNew);
							break;
							case "vodtotal":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_total,$loopstrChannelNew);
							break;
							case "douban":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_douban,$loopstrChannelNew);
							break;
							case "mtime":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_mtime,$loopstrChannelNew);
							break;
							case "imdb":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_imdb,$loopstrChannelNew);
							break;
							case "tvs":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tvs,$loopstrChannelNew);
							break;
							case "company":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_company,$loopstrChannelNew);
							break;
							case "des":
								$v_des=htmlspecialchars_decode($row->v_content);
								$v_des=Html2Text($v_des);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_des,$loopstrChannelNew);
							break;
							case "time":
								$timestyle="";
								$videoTime=$row->v_addtime;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
								switch (trim($timestyle)) {
									case "yyyy-mm-dd":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yy-mm-dd":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yyyy-m-d":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrChannelNew);
									break;
									case "mm-dd":
									default:
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrChannelNew);
								}
							break;
							case "from":
								$loopstrChannelNew=str_replace($matchfieldvalue,getFromStr($row->v_playdata),$loopstrChannelNew);
							break;
							case "state":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_state,$loopstrChannelNew);
							break;
							case "commend":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_commend,$loopstrChannelNew);
							break;
							case "publishtime":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_publishyear,$loopstrChannelNew);
							break;
							case "ver":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_ver,$loopstrChannelNew);
							break;
							case "publisharea":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_publisharea,$loopstrChannelNew);
							break;
							case "playlink":
								if($GLOBALS['cfg_isalertwin']==1) $playlink_str="javascript:openWin('".getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname)."',".($GLOBALS['cfg_alertwinw']).",".($GLOBALS['cfg_alertwinh']).",250,100,1)";
								else $playlink_str=getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname);
								$loopstrChannelNew=str_replace($matchfieldvalue,$playlink_str,$loopstrChannelNew);
							break;
							case "favLink":
								@session_start();
								if(isset($_SESSION['sea_user_auth'])||$GLOBALS['cfg_user']==1)
								{														
									$uid=$_SESSION['sea_user_id'];
									$favLink_str = "javascript:AddFav('".$row->v_id."','$uid')";
								}else
								{
									$favLink_str = "#";
								}
								$loopstrChannelNew=str_replace($matchfieldvalue,$favLink_str,$loopstrChannelNew);
							break;
							case "director":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_director,$loopstrChannelNew);
							break;
							case "money":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_money,$loopstrChannelNew);
							break;
							case "lang":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_lang,$loopstrChannelNew);
							break;
							case "digg":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_digg,$loopstrChannelNew);
							break;
							case "tread":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tread,$loopstrChannelNew);
							break;
							case "scorenum":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_score,$loopstrChannelNew);
							break;
							case "scorenumer":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_scorenum,$loopstrChannelNew);
							break;
							case "score":
								$score=number_format($row->v_score/$row->v_scorenum,1);
								$loopstrChannelNew=str_replace($matchfieldvalue,$score,$loopstrChannelNew);
							break;
							case "keyword":
								$v_tags=getKeywordsList($row->v_tags,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_tags,$loopstrChannelNew);
							break;
							case "nolinkkeyword":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tags,$loopstrChannelNew);
							break;
							case "jqtype":
								$v_jq=getJqList($row->v_jq,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_jq,$loopstrChannelNew);
							break;
							case "nolinkjqtype":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_jq,$loopstrChannelNew);
							break;
						}
					}
					$i=$i+1;
					$loopstrTotal=$loopstrTotal.$loopstrChannelNew;
				}
				unset($rows);
			}
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
		}
		$labelRulePagelist = buildregx($labelRulePagelist,"is");
		preg_match_all($labelRulePagelist,$content,$plar);
		$arlen=count($plar[1]);
		for($p=0;$p<$arlen;$p++){
			if($TotalResult==0){
				$content=str_replace($plar[0][$p],"",$content);
			}else{
				$fieldAttr =$plar[1][$p];
				$fieldAttr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$fieldAttr));
				$fieldAttrarr=explode(chr(61),$fieldAttr);
				$lenPagelist=empty($fieldAttr) ? 10 : intval($fieldAttrarr[1]);
				$strPagelist=pageNumberLinkInfo($currentPage,$lenPagelist,$TotalPage,$pageListType,$TotalResult,$currentTypeId);
				$content=str_replace($plar[0][$p],$strPagelist,$content);
			}
		}
		$tpages=$TotalPage==0?1:$TotalPage;
		if(strpos($content,"{/".$pageListType."list:pagenumber}")>0)
		{
			preg_match_all("|{".$pageListType."list:pagenumber(.*?)}(.*?){/".$pageListType."list:pagenumber}|is", $content, $matchesPagelist);
			foreach ($matchesPagelist[1] as $k=>$matchPagelist){
				$attr=$this->parseAttr($matchPagelist);
				$len=$attr['len'];
				$len=empty($len)?10:$len;
				$strPagelist=$matchesPagelist[2][$k];
				$strPagelist=makePageNumberLoop2($currentPage,$len,$tpages,$strPagelist,$pageListType,$currentTypeId);
				$content=str_replace($matchesPagelist[0][$k],$strPagelist,$content);	
			}
		}
		$content=str_replace("{".$pageListType."list:page}",$currentPage,$content);	
		$content=str_replace("{".$pageListType."list:pagecount}",$tpages,$content);
		$content=str_replace("{".$pageListType."list:recordcount}",$TotalResult,$content);	
		$content=str_replace("{".$pageListType."list:firstlink}",getPageLink(1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:backlink}",getPageLink($currentPage-1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:nextlink}",getPageLink($currentPage+1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:lastlink}",getPageLink($tpages,$pageListType,$currentTypeId),$content);		
		return $content;
	}
	
	function parseNewsPageList($content,$typeId,$currentPage,$totalPages,$pageListType,$currentTypeId=-444)
	{
		global $cfg_issqlcache,$lang;
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:".$pageListType."list(.*?)}(.*?){/seacms:".$pageListType."list}","is");
		$labelRuleField="\[".$pageListType."list:(.*?)\]";
		$labelRulePagelist="\[".$pageListType."list:pagenumber(.*?)\]";
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopstrChannel=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$vsize=empty($attrDictionary["size"]) ? 12 : intval($attrDictionary["size"]);
			$vorder=empty($attrDictionary["order"]) ? "time" : $attrDictionary["order"];
			if(intval($currentPage)>intval($totalPages)) $currentPage=$totalPages;
			unset($attrDictionary);
					$limitstart = ($currentPage-1) * $vsize;
					if($limitstart<0) $limitstart=0;
					$row = $vsize;
			switch ($vorder) {
				case "id":
					$orderStr =" order by n_id desc";
				break;
				case "hit":
					$orderStr =" order by n_hit desc";
				break;
				case "time":
					$orderStr =" order by n_addtime desc";
				break;
				case "name":
					$orderStr =" order by n_title asc";
				break;
				case "digg":
					$orderStr =" order by n_digg desc";
				break;
				case "hot":
					$orderStr =" order by n_hit desc";
				break;
				case "letter":
					$orderStr =" order by n_letter asc";
				break;
			}
			switch ($pageListType) {
				case "newspage":
					$whereStr=" where n_recycled=0 and tid in (".$typeId.")";
				break;
				case "newssearch":
					global $searchtype,$searchword;
					switch (intval($searchtype)) {
						case -1:
							$whereStr=" where n_recycled=0 and (n_title like '%$searchword%' or n_keyword like '%$searchword%')";
						break;
						case 0:
							$whereStr=" where n_recycled=0 and n_title like '%$searchword%'";	
						break;
						case 1:
							$whereStr=" where n_recycled=0 and n_author like '%$searchword%'";
						break;
						case 2:
							$whereStr=" where n_recycled=0 and n_from like '%$searchword%'";
						break;
						case 3:
							$whereStr=" where n_recycled=0 and n_outline like '%$searchword%'";
						break;
						case 4:
							$whereStr=" where n_recycled=0 and n_letter='".strtoupper($searchword)."'";
						break;
					}
				break;
			}
			$sql="select * from sea_news ".$whereStr." ".$orderStr." limit $limitstart,$row";
			$cquery = "Select count(*) as dd From `sea_news` ".$whereStr;
			$row = $this->dsql->GetOne($cquery);
			if(is_array($row))
			{
				$TotalResult = $row['dd'];
			}
			else
			{
				$TotalResult = 0;
			}
			$TotalPage = ceil($TotalResult/$vsize);
			$labelRuleField = buildregx($labelRuleField,"is");
			preg_match_all($labelRuleField,$content,$lar);
			$matchfieldarr=$lar[1];
			$matchfieldstrarr=$lar[0];
			$loopstrTotal="";
			$i=1;
			if($TotalResult==0){
				$loopstrTotal=$lang['channellistInfo']['0'];
			}else{
				if($cfg_issqlcache){
					$mycachefile=md5('newsPageList'.$whereStr.$orderStr.$limitstart.$row);
					setCache($mycachefile,$sql);
					$rows=getCache($mycachefile);
				}else{
					$rows=array();
					$this->dsql->SetQuery($sql);
					$this->dsql->Execute('newsPageList');
					while($rowr=$this->dsql->GetObject('newsPageList'))
					{
					$rows[]=$rowr;
					}
					unset($rowr);
				}
				foreach($rows as $row)
				{
					$loopstrChannelNew=$loopstrChannel;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "id":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_id,$loopstrChannelNew);
							break;
							case "typeid":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->tid,$loopstrChannelNew);
							break;
							case "name":
							case "title":
								$v_name=$row->n_title;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($namelen)&&strlen($v_name)>$namelen ? trimmed_title($v_name,$namelen) : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "colortitle":
							case "colorname":
								$v_color=$row->n_color;
								$v_name=$row->n_title;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$colornamelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($colornamelen)&&strlen($v_name)>$colornamelen ? trimmed_title($v_name,$colornamelen) : $v_name;
								$v_name=$v_color ? "<font color=".$v_color.">".$v_name."</font>" : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "note":
								$v_note=$row->n_note;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$notelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_note=!empty($notelen)&&strlen($v_note)>$notelen ? trimmed_title($v_note,$notelen) : $v_note;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_note,$loopstrChannelNew);
							break;
							case "author":
								$v_actor=$row->n_author;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;		
								$v_actor=getKeywordsList($v_actor,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_actor,$loopstrChannelNew);
							break;
							case "content":
							case "des":
								$v_des=Html2Text($row->n_content);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_des=preg_replace('/\{news\:video(.*?)\}/is','',$v_des);
								$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_des,$loopstrChannelNew);
							break;
							case "outline":
								$v_des=Html2Text($row->n_outline);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_des,$loopstrChannelNew);
							break;
							case "time":
								$timestyle="";
								$videoTime=$row->n_addtime;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
								switch (trim($timestyle)) {
									case "yyyy-mm-dd":
								$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yy-mm-dd":
								$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yyyy-m-d":
								$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrChannelNew);
									break;
									case "mm-dd":
									default:
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrChannelNew);
								}
							break;
							case "i":
								$loopstrChannelNew=str_replace($matchfieldvalue,$i,$loopstrChannelNew);
							break;
							case "digg":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_digg,$loopstrChannelNew);
							break;
							case "score":
								$score=number_format($row->n_score/$row->n_scorenum,1);
								$loopstrChannelNew=str_replace($matchfieldvalue,$score,$loopstrChannelNew);
							break;
							case "tread":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_tread,$loopstrChannelNew);
							break;
							case "scorenum":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_score,$loopstrChannelNew);
							break;
							case "scorenumer":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_scorenum,$loopstrChannelNew);
							break;
							case "typename":
								$loopstrChannelNew=str_replace($matchfieldvalue,getNewsTypeNameOnCache($row->tid),$loopstrChannelNew);
							break;
							case "typelink":
								$loopstrChannelNew=str_replace($matchfieldvalue,getChannelPagesLink($row->tid),$loopstrChannelNew);
							break;
							case "link":
								$loopstrChannelNew=str_replace($matchfieldvalue,getArticleLink($row->tid,$row->n_id,''),$loopstrChannelNew);
							break;
							case "pic":
								$v_pic=$row->n_pic;
								if(!empty($v_pic)){
									if(strpos(' '.$v_pic,'://')>0){
									$loopstrChannelNew=str_replace($matchfieldvalue,$v_pic,$loopstrChannelNew);
									}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
		
							break;
							case "hit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_hit,$loopstrChannelNew);
							break;
							case "letter":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_letter,$loopstrChannelNew);
							break;
							case "commend":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->n_commend,$loopstrChannelNew);
							break;
							case "from":
								$loopstrChannelNew=str_replace($matchfieldvalue,getFromStr($row->n_from),$loopstrChannelNew);
							break;
						}
					}
				$i=$i+1;
				$loopstrTotal=$loopstrTotal.$loopstrChannelNew;
				}
			}
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
		}
		$labelRulePagelist = buildregx($labelRulePagelist,"is");
		preg_match_all($labelRulePagelist,$content,$plar);
		$arlen=count($plar[1]);
		for($p=0;$p<$arlen;$p++){
			if($TotalResult==0){
				$content=str_replace($plar[0][$p],"",$content);
			}else{
				$fieldAttr =$plar[1][$p];
				$fieldAttr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$fieldAttr));
				$fieldAttrarr=explode(chr(61),$fieldAttr);
				$lenPagelist=empty($fieldAttr) ? 10 : intval($fieldAttrarr[1]);
				$strPagelist=pageNumberLinkInfo($currentPage,$lenPagelist,$TotalPage,$pageListType,$TotalResult,$currentTypeId);
				$content=str_replace($plar[0][$p],$strPagelist,$content);
			}
		}
		$tpages=$TotalPage==0?1:$TotalPage;
		if(strpos($content,"{/".$pageListType."list:pagenumber}")>0)
		{
			preg_match_all("|{".$pageListType."list:pagenumber(.*?)}(.*?){/".$pageListType."list:pagenumber}|is", $content, $matchesPagelist);
			foreach ($matchesPagelist[1] as $k=>$matchPagelist){
				  $attr=$this->parseAttr($matchPagelist);
				  $len=$attr['len'];
				  $len=empty($len)?10:$len;
				  $strPagelist=$matchesPagelist[2][$k];
				  $strPagelist=makePageNumberLoop2($currentPage,$len,$tpages,$strPagelist,$pageListType,$currentTypeId);
				  $content=str_replace($matchesPagelist[0][$k],$strPagelist,$content);	
			}
		}
		$content=str_replace("{".$pageListType."list:page}",$currentPage,$content);	
		$content=str_replace("{".$pageListType."list:pagecount}",$tpages,$content);
		$content=str_replace("{".$pageListType."list:recordcount}",$TotalResult,$content);	
		$content=str_replace("{".$pageListType."list:firstlink}",getPageLink(1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:backlink}",getPageLink($currentPage-1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:nextlink}",getPageLink($currentPage+1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:lastlink}",getPageLink($tpages,$pageListType,$currentTypeId),$content);	
		return $content;
	}
	
	function parseTopicList($content)
	{
		global $lang;
		if (strpos($content,'{seacms:topiclist')=== false){
			return $content;
		}else{
			global $cfg_issqlcache;
			$attrDictionary=array();
			$labelRule = buildregx("{seacms:topiclist(.*?)}(.*?){/seacms:topiclist}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			for($m=0;$m<$arlen;$m++){
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
				$loopstrTopiclist=$ar[2][$m];
				$attrDictionary=$this->parseAttr($attrStr);
				$id=empty($attrDictionary["id"])?'all':$attrDictionary["id"];
				$num=empty($attrDictionary["num"])?10:$attrDictionary["num"];
				$start=empty($attrDictionary["start"])?0:$attrDictionary["start"]-1;
				unset($attrDictionary);
				if($id!='all')
				{
					if(strpos($id,',')>0)
					$whereTopic = " where id in (".$id.")";
					else
					$whereTopic = " where id = ".$id;
				}else
				{
					$whereTopic = "";
				}
				$cquery = "Select count(*) as dd From `sea_topic`";
				$row = $this->dsql->GetOne($cquery);
				if(is_array($row))
				{
					$TotalResult = $row['dd'];
				}
				else
				{
					$TotalResult = 0;
				}
				$sql="select name,id,pic,enname,des from sea_topic".$whereTopic." order by sort desc limit $start,$num";
				if($TotalResult == 0){
					$loopstrTotal=$lang['topicpageInfo']['0'];
				}else{
					if($cfg_issqlcache){
						$mycachefile=md5('topiclist');
						setCache($mycachefile,$sql);
						$rows=getCache($mycachefile);
					}else{
						$rows=array();
						$this->dsql->SetQuery($sql);
						$this->dsql->Execute('topiclist');
						while($rowr=$this->dsql->GetObject('topiclist'))
						{
							$rows[]=$rowr;
						}
						unset($rowr);
					}
				}
				$labelRuleField = buildregx("\[topiclist:(.*?)\]","is");
				preg_match_all($labelRuleField,$content,$lar);
				$matchfieldarr=$lar[1];
				$matchfieldstrarr=$lar[0];
				$loopstrTotal="";
				$i=$start;
				$n=1;
				foreach($rows as $row){
					$loopstrTopiclistNew=$loopstrTopiclist;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "i":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$i,$loopstrTopiclistNew);
							break;
							case "n":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$n,$loopstrTopiclistNew);
							break;
							case "id":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$row->id,$loopstrTopiclistNew);
							break;
							case "name":
								$name=$row->name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$name=!empty($namelen)&&strlen($name)>$namelen ? trimmed_title($name,$namelen) : $name;
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$name,$loopstrTopiclistNew);
							break;
							case "count":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,getTopicNum($row->id),$loopstrTopiclistNew);
							break;
							case "pic":
								$pic=$row->pic;
								if(!empty($pic)){
									if(strpos(' '.$pic,'://')>0){
										$loopstrTopiclistNew=str_replace($matchfieldvalue,$pic,$loopstrTopiclistNew);
									}else{
										$loopstrTopiclistNew=str_replace($matchfieldvalue,"/".$GLOBALS['cfg_cmspath']."uploads/zt/".$pic,$loopstrTopiclistNew);
									}
								}else{
									$loopstrTopiclistNew=str_replace($matchfieldvalue,"/".$GLOBALS['cfg_cmspath']."images/defaultpic.gif",$loopstrTopiclistNew);
								}
							break;
							case "link":
								if($GLOBALS['cfg_runmode']=='0')
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$row->enname.$GLOBALS['cfg_filesuffix2'];
								}
								if($GLOBALS['cfg_runmode']=='1')
								
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/?".$row->id.$GLOBALS['cfg_filesuffix2'];
								}
								if($GLOBALS['cfg_runmode']=='2')
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$row->id.$GLOBALS['cfg_filesuffix2'];
								}
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$topicLink,$loopstrTopiclistNew);
							break;
							case "des":
								$des=htmlspecialchars_decode($row->des);
								$des=Html2Text($des);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
								$des=!empty($deslen)&&strlen($des)>$deslen ? trimmed_title($des,$deslen) : $des;
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$des,$loopstrTopiclistNew);
							break;
						}
					}
					$i=$i+1;
					$n=$n+1;
					$loopstrTotal.=$loopstrTopiclistNew;
				}
				$content=str_replace($ar[0][$m],$loopstrTotal,$content);
			}		
			return $content;
		}
	}
	
	function parseTopicIndexList($content,$currentPage)
	{
		global $lang;
		if (strpos($content,'{seacms:topicindexlist')=== false){
			return $content;
		}else{
			global $cfg_issqlcache;
			$attrDictionary=array();
			$labelRule = buildregx("{seacms:topicindexlist(.*?)}(.*?){/seacms:topicindexlist}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			for($m=0;$m<$arlen;$m++){
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
				$loopstrTopiclist=$ar[2][$m];
				$attrDictionary=$this->parseAttr($attrStr);
				$vsize=empty($attrDictionary["size"])?12:$attrDictionary["size"];
				unset($attrDictionary);
				$cquery = "Select count(*) as dd From `sea_topic`";
				$row = $this->dsql->GetOne($cquery);
				if(is_array($row))
				{
					$TotalResult = $row['dd'];
				}
				else
				{
					$TotalResult = 0;
				}
				$TotalPage = ceil($TotalResult/$vsize);
				$limitstart = ($currentPage-1) * $vsize;
				$sql="select name,id,pic,enname,des from sea_topic order by sort desc limit $limitstart,$vsize";
				if($TotalResult == 0){
				$loopstrTotal=$lang['topicpageInfo']['0'];
				}else{
					if($cfg_issqlcache){
						$mycachefile=md5('topicindexlist');
						setCache($mycachefile,$sql);
						$rows=getCache($mycachefile);
					}else{
						$rows=array();
						$this->dsql->SetQuery($sql);
						$this->dsql->Execute('topicindexlist');
						while($rowr=$this->dsql->GetObject('topicindexlist'))
						{
							$rows[]=$rowr;
						}
						unset($rowr);
					}
				}
				$labelRuleField = buildregx("\[topicindexlist:(.*?)\]","is");
				preg_match_all($labelRuleField,$content,$lar);
				$matchfieldarr=$lar[1];
				$matchfieldstrarr=$lar[0];
				$i=1;
				foreach($rows as $row){
					$loopstrTopiclistNew=$loopstrTopiclist;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "i":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$i,$loopstrTopiclistNew);
							break;
							case "name":
								$name=$row->name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$name=!empty($namelen)&&strlen($name)>$namelen ? trimmed_title($name,$namelen) : $name;
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$name,$loopstrTopiclistNew);
							break;
							case "count":
								$loopstrTopiclistNew=str_replace($matchfieldvalue,getTopicNum($row->id),$loopstrTopiclistNew);
							break;
							case "pic":
								$pic=$row->pic;
								if(!empty($pic)){
									if(strpos(' '.$pic,'://')>0){
										$loopstrTopiclistNew=str_replace($matchfieldvalue,$pic,$loopstrTopiclistNew);
									}else{
										$loopstrTopiclistNew=str_replace($matchfieldvalue,"/".$GLOBALS['cfg_cmspath']."uploads/zt/".$pic,$loopstrTopiclistNew);
									}
								}else{
									$loopstrTopiclistNew=str_replace($matchfieldvalue,"/".$GLOBALS['cfg_cmspath']."images/defaultpic.gif",$loopstrTopiclistNew);
								}
							break;
							case "link":
								if($GLOBALS['cfg_runmode']=='0')
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$row->enname.$GLOBALS['cfg_filesuffix2'];
								}
								if($GLOBALS['cfg_runmode']=='1')
								
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/?".$row->id.$GLOBALS['cfg_filesuffix2'];
								}
								if($GLOBALS['cfg_runmode']=='2')
								{
									$topicLink="/".$GLOBALS['cfg_cmspath'].$GLOBALS['cfg_filesuffix']."/".$row->id.$GLOBALS['cfg_filesuffix2'];
								}
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$topicLink,$loopstrTopiclistNew);
							break;
							case "des":
								$des=Html2Text($row->des);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
								$des=!empty($deslen)&&strlen($des)>$deslen ? trimmed_title($des,$deslen) : $des;
								$loopstrTopiclistNew=str_replace($matchfieldvalue,$des,$loopstrTopiclistNew);
							break;
						}
					}
					$i=$i+1;
					$loopstrTotal.=$loopstrTopiclistNew;
				}
				$content=str_replace($ar[0][$m],$loopstrTotal,$content);
			}
			preg_match_all("/\[topicindexlist:pagenumber(.*?)\]/is", $content, $matchesPagelist);
			foreach ($matchesPagelist[1] as $k=>$matchPagelist){
				if($TotalPage==0)
				{
				$content=str_replace($matchesPagelist[0][$k],'',$content);	
				}
				else 
				{
				$attr=$this->parseAttr($matchPagelist);
				$len=$attr['len'];
				$len=empty($len)?10:$len;
				$strPagelist=pageNumberLinkInfo($currentPage,$len,$TotalPage,"topicindex",$TotalResult);
				$content=str_replace($matchesPagelist[0][$k],$strPagelist,$content);	
				}
			}
			$tpages=$TotalPage==0?1:$TotalPage;
			if(strpos($content,"{/topicindexlist:pagenumber}")>0)
			{
				preg_match_all("|{topicindexlist:pagenumber(.*?)}(.*?){/topicindexlist:pagenumber}|is", $content, $matchesPagelist);
				foreach ($matchesPagelist[1] as $k=>$matchPagelist){
					$attr=$this->parseAttr($matchPagelist);
					$len=$attr['len'];
					$len=empty($len)?10:$len;
					$strPagelist=$matchesPagelist[2][$k];
					$strPagelist=makePagenumberLoop($currentPage,$len,$tpages,$strPagelist);
					$content=str_replace($matchesPagelist[0][$k],$strPagelist,$content);
				}
			}
			$content=str_replace("{topicindexlist:page}",$currentPage,$content);	
			$content=str_replace("{topicindexlist:pagecount}",$tpages,$content);
			$content=str_replace("{topicindexlist:recordcount}",$TotalResult,$content);	
			$content=str_replace("{topicindexlist:firstlink}",getTopicIndexLink(1),$content);	
			$content=str_replace("{topicindexlist:backlink}",getTopicIndexLink($currentPage-1),$content);	
			$content=str_replace("{topicindexlist:nextlink}",getTopicIndexLink($currentPage+1),$content);	
			$content=str_replace("{topicindexlist:lastlink}",getTopicIndexLink($tpages),$content);			
			return $content;
		}
	}

	function parsePlayList($content,$dataId,$typeid,$sdate,$enname,$playorDownData,$str='play'){
		if (strpos($content,'{playpage:playlist')=== false&&strpos($content,'{playpage:downlist')=== false){
			return $content;
		}else{
			$PlayerIntroArray=getPlayerIntroArray();
			$playerDic=getPlayerKindsArray();
			$DownIntroArray=getDownIntroArray();
			$labelRule = buildregx("{playpage:".$str."list(.*?)}(.*?){/playpage:".$str."list}","is");
			preg_match_all($labelRule,$content,$ar);
			$arlen=count($ar[1]);
			for($m=0;$m<$arlen;$m++){
				$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
				$loopstrPlaylist=$ar[2][$m];
				$playDataArray=getPlayurlArray($playorDownData);
				//$vnum=count($playDataArray);
				//echo $playorDownData;
				//echo "<hr>";
				$vnum1= substr_count($playorDownData,'$$');
				if($vnum1==0)
				{$vnum=0;}
				else
				{$vnum=count($playDataArray);}
				$labelRuleField = buildregx("\[".$str."list:(.*?)\]","is");
				preg_match_all($labelRuleField,$loopstrPlaylist,$lar);
				$matchfieldarr=$lar[1];
				$matchfieldstrarr=$lar[0];
				$loopstrTotal=array();
				$k=0;
				if($str=='down'){
					for($i=0;$i<$vnum;$i++){
						$singlePlayData=explode("$$",$playDataArray[$i]) ;
						$videoFrom=$i ; 
						$videoUrl=$singlePlayData[1];
						$loopstrPlaylistNew=$loopstrPlaylist;
						if($videoUrl!=""){
						$k=$k+1;
						foreach($matchfieldarr as $f=>$matchfieldstr){
							$matchfieldvalue=$matchfieldstrarr[$f];
							$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
							if (strpos($matchfieldstr," ")>0){
								$fieldtemparr=explode(" ",$matchfieldstr);
								$fieldName=$fieldtemparr[0];
								$fieldAttr =$fieldtemparr[1];
							}else{
								$fieldName=$matchfieldstr;
								$fieldAttr ="";
							}
							switch (trim($fieldName)) {
								case "from":
									$loopstrPlaylistNew=str_replace($matchfieldvalue,$singlePlayData[0],$loopstrPlaylistNew);
								break;
								case "link":
									$fieldAttrarr=explode(chr(61),$fieldAttr);
									$target=empty($fieldAttr) ? "" : $fieldAttrarr[1];
									$urlStr=getDownUrlList($videoUrl,$target);
									$loopstrPlaylistNew=str_replace($matchfieldvalue,$urlStr,$loopstrPlaylistNew);
								break;
								case "linkstr":
									$fieldAttrarr=explode(chr(61),$fieldAttr);
									$target=empty($fieldAttr) ? "" : $fieldAttrarr[1];
									$urlStr=getDownUrlList2($videoUrl,$target,'li',$k,true);
									$loopstrPlaylistNew=str_replace($matchfieldvalue,$urlStr,$loopstrPlaylistNew);
								break;
								case "i":
									$loopstrPlaylistNew=str_replace($matchfieldvalue,$k,$loopstrPlaylistNew);
								break;
							}
					    }
						}else{
							$loopstrPlaylistNew="";
						}
						$j=getArrayElementID($DownIntroArray,"flag",$singlePlayData[0]);
						$loopstrTotal[$j].=$loopstrPlaylistNew;
					}	
			    }else{
					for($i=0;$i<=$vnum;$i++){
						$singlePlayData=explode("$$",$playDataArray[$i]) ; $videoFrom=$i ; $videoUrl=$singlePlayData[1];
						$playerSingleInfoArray=$playerDic[$singlePlayData[0]];
						$loopstrPlaylistNew=$loopstrPlaylist;
						if($playerSingleInfoArray['open']==1){
							$k=$k+1;
							foreach($matchfieldarr as $f=>$matchfieldstr){
								$matchfieldvalue=$matchfieldstrarr[$f];
								$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
								if (strpos($matchfieldstr," ")>0){
									$fieldtemparr=explode(" ",$matchfieldstr);
									$fieldName=$fieldtemparr[0];
									$fieldAttr =$fieldtemparr[1];
								}else{
									$fieldName=$matchfieldstr;
									$fieldAttr ="";
								}
								switch (trim($fieldName)) {
									case "from":
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$singlePlayData[0],$loopstrPlaylistNew);
									break;
									case "intro":
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$playerSingleInfoArray['intro'],$loopstrPlaylistNew);
									break;
									case "ename":
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$playerSingleInfoArray['postfix'],$loopstrPlaylistNew);
									break;
									case "link":
										$fieldAttrarr=explode(chr(61),$fieldAttr);
										$target=empty($fieldAttr) ? "" : $fieldAttrarr[1];
										$urlStr=getPlayUrlList($videoFrom,$videoUrl,$typeid,$dataId,$target,$sdate,$enname);
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$urlStr,$loopstrPlaylistNew);
									break;
									case "url":
										$fieldAttrarr=explode(chr(61),$fieldAttr);
										$target=empty($fieldAttr) ? "" : $fieldAttrarr[1];
										$urlStr=getPlayUrlList2($videoFrom,$videoUrl,$typeid,$dataId,$target,$sdate,$enname);
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$urlStr,$loopstrPlaylistNew);
									break;
									case "nolinkurl":
										$fieldAttrarr=explode(chr(61),$fieldAttr);
										$target=empty($fieldAttr) ? "" : $fieldAttrarr[1];
										$urlStr=getPlayUrlList3($videoFrom,$videoUrl,$typeid,$dataId,$target,$sdate,$enname);
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$urlStr,$loopstrPlaylistNew);
									break;
									case "i":
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$k,$loopstrPlaylistNew);
									break;
									case "s":
										$loopstrPlaylistNew=str_replace($matchfieldvalue,$playerSingleInfoArray['sort'],$loopstrPlaylistNew);
									break;
								}
							}
						}else{
							$loopstrPlaylistNew="";
						}
						$j=getArrayElementID($PlayerIntroArray,"flag",$singlePlayData[0]);
						$loopstrTotal[$j].=$loopstrPlaylistNew;
					}
			}
			krsort($loopstrTotal);
			$loopstrTotal=join("\n",$loopstrTotal);
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
			$content=str_replace("{playpage:".$str."listlen}",$vnum,$content);
			}
			return $content;
		}
	}

	function paresPreNextVideo($content,$dataId,$typeFlag,$vtype)
	{
		$preNextLabel="{playpage:prenext}";
		if (strpos($content,$preNextLabel)=== false){
			return $content;
		}else{
			$rown = $this->dsql->GetOne("select v_id as nextid ,v_name as nextname,v_addtime,v_enname from sea_data where tid='$vtype' and v_id<".$dataId." order by v_id desc");
			if(is_array($rown)){
				$nextid=$rown["nextid"];
				$nextname=$rown["nextname"];
			}else{
				$nextid=0;
			}
			$rowl = $this->dsql->GetOne("select v_id as lastid ,v_name as lastname,v_addtime,v_enname from sea_data where tid='$vtype' and v_id>".$dataId." order by v_id asc");
			if(is_array($rowl)){
				$lastid=$rowl["lastid"];
				$lastname=$rowl["lastname"];
			}else{
				$lastid=0;
			}
			if($typeFlag=="parse_play_"){
				if ($lastid == 0) $mystr = "<span>上一篇:没有了</span> "; else $mystr = "<span>上一篇:<a href=".getPlayLink2($vtype,$lastid,date('Y-n',$rowl['v_addtime']),$rowl['v_enname']).">".$lastname."</a></span> ";
				if ($nextid == 0) $mystr .= "<span>下一篇:没有了</span>"; else $mystr .= "<span>下一篇:<a href=".getPlayLink2($vtype,$nextid,date('Y-n',$rown['v_addtime']),$rown['v_enname']).">".$nextname."</a></span>";
			}else{
				if ($lastid == 0) $mystr = "<span>上一篇:没有了</span> "; else $mystr = "<span>上一篇:<a href=".getContentLink($vtype,$lastid,"link",date('Y-n',$rowl['v_addtime']),$rowl['v_enname']).">".$lastname."</a></span> ";
				if ($nextid == 0) $mystr .= "<span>下一篇:没有了</span>"; else $mystr .= "<span>下一篇:<a href=".getContentLink($vtype,$nextid,"link",date('Y-n',$rown['v_addtime']),$rown['v_enname']).">".$nextname."</a></span>";
			}
			$content=str_replace($preNextLabel,$mystr,$content);
			return $content;
		}
	}

	function parseLinkList($content){
		global $cfg_issqlcache;
		if (strpos($content,'{seacms:linklist')=== false){
		return $content;
		}else{
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:linklist(.*?)}(.*?){/seacms:linklist}","is");
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
		$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
		$loopstrLinklist=$ar[2][$m];
		$attrDictionary=$this->parseAttr($attrStr);
		$vtype=empty($attrDictionary["type"]) ? "font" : $attrDictionary["type"];
		switch ($vtype) {
			case "font":
				$whereStr =" logo='' ";
			break;
			case "pic":
				$whereStr =" logo!='' ";
			break;
			default:
				$whereStr =" logo='' ";
		}
		$sql="select url,webname,msg,logo from sea_flink where ".$whereStr." order by sortrank";
		$labelRuleField = buildregx("\[linklist:(.*?)\]","is");
		preg_match_all($labelRuleField,$loopstrLinklist,$flar);
		$matchfieldarr=$flar[1];
		$matchfieldstrarr=$flar[0];
		$loopstrTotal="";
		$i=1;
		if($cfg_issqlcache){
		$mycachefile=md5('flinklist'.$whereStr);
		setCache($mycachefile,$sql);
		$rows=getCache($mycachefile);
		}else{
		$rows=array();
		$this->dsql->SetQuery($sql);
		$this->dsql->Execute('flinklist');
		while($rowr=$this->dsql->GetObject('flinklist'))
		{
		$rows[]=$rowr;
		}
		unset($rowr);
		}
		foreach($rows as $row){
		$loopstrLinklistNew=$loopstrLinklist;
		foreach($matchfieldarr as $f=>$matchfieldstr){
					$matchfieldvalue=$matchfieldstrarr[$f];
					$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
					if (strpos($matchfieldstr,chr(32))>0){
					$fieldtemparr=explode(chr(32),$matchfieldstr);
					$fieldName=$fieldtemparr[0];
					$fieldAttr =$fieldtemparr[1];
					}else{
					$fieldName=$matchfieldstr;
					$fieldAttr ="";
					}
					switch (trim($fieldName)) {
						case "name":
							$loopstrLinklistNew=str_replace($matchfieldvalue,$row->webname,$loopstrLinklistNew);
						break;
						case "link":
							$loopstrLinklistNew=str_replace($matchfieldvalue,$row->url,$loopstrLinklistNew);
						break;	
						case "pic":
							$loopstrLinklistNew=str_replace($matchfieldvalue,$row->logo,$loopstrLinklistNew);
						break;
						case "des":
							$v_des=Html2Text($row->msg);
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$deslen=empty($fieldAttr) ? 100 : intval($fieldAttrarr[1]);
							$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
							$loopstrLinklistNew=str_replace($matchfieldvalue,$v_des,$loopstrLinklistNew);
						break;
						case "i":
							$loopstrLinklistNew=str_replace($matchfieldvalue,$i,$loopstrLinklistNew);
						break;
					}
		}
		$i=$i+1;
		$loopstrTotal=$loopstrTotal.$loopstrLinklistNew;
		}
		$content=str_replace($ar[0][$m],$loopstrTotal,$content);
		}
		return $content;
		}
	}

	function parseStrIf($strIf)
	{
		if(strpos($strIf,'=')===false)
		{
			return $strIf;
		}
		if((strpos($strIf,'==')===false)&&(strpos($strIf,'=')>0))
		{
			$strIf=str_replace('=', '==', $strIf);
		}
		$strIfArr =  explode('==',$strIf);
		return (empty($strIfArr[0])?'NULL':$strIfArr[0])."==".(empty($strIfArr[1])?'NULL':$strIfArr[1]);
	}

	function parseIf($content){
		if (strpos($content,'{if:')=== false){
		return $content;
		}else{
		$labelRule = buildregx("{if:(.*?)}(.*?){end if}","is");
		$labelRule2="{elseif";
		$labelRule3="{else}";
		preg_match_all($labelRule,$content,$iar);
		$arlen=count($iar[0]);
		$elseIfFlag=false;
		for($m=0;$m<$arlen;$m++){
			$strIf=$iar[1][$m];
			$strIf=$this->parseStrIf($strIf);
			$strThen=$iar[2][$m];
			$strThen=$this->parseSubIf($strThen);
			if (strpos($strThen,$labelRule2)===false){
				if (strpos($strThen,$labelRule3)>=0){
					$elsearray=explode($labelRule3,$strThen);
					$strThen1=$elsearray[0];
					$strElse1=$elsearray[1];
					@eval("if(".$strIf."){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $content=str_replace($iar[0][$m],$strThen1,$content);} else {$content=str_replace($iar[0][$m],$strElse1,$content);}
				}else{
				@eval("if(".$strIf.") { \$ifFlag=true;} else{ \$ifFlag=false;}");
				if ($ifFlag) $content=str_replace($iar[0][$m],$strThen,$content); else $content=str_replace($iar[0][$m],"",$content);}
			}else{
				$elseIfArray=explode($labelRule2,$strThen);
				$elseIfArrayLen=count($elseIfArray);
				$elseIfSubArray=explode($labelRule3,$elseIfArray[$elseIfArrayLen-1]);
				$resultStr=$elseIfSubArray[1];
				$elseIfArraystr0=addslashes($elseIfArray[0]);
				@eval("if($strIf){\$resultStr=\"$elseIfArraystr0\";}");
				for($elseIfLen=1;$elseIfLen<$elseIfArrayLen;$elseIfLen++){
					$strElseIf=getSubStrByFromAndEnd($elseIfArray[$elseIfLen],":","}","");
					$strElseIf=$this->parseStrIf($strElseIf);
					$strElseIfThen=addslashes(getSubStrByFromAndEnd($elseIfArray[$elseIfLen],"}","","start"));
					@eval("if(".$strElseIf."){\$resultStr=\"$strElseIfThen\";}");
					@eval("if(".$strElseIf."){\$elseIfFlag=true;}else{\$elseIfFlag=false;}");
					if ($elseIfFlag) {break;}
				}
				$strElseIf0=getSubStrByFromAndEnd($elseIfSubArray[0],":","}","");
				$strElseIfThen0=addslashes(getSubStrByFromAndEnd($elseIfSubArray[0],"}","","start"));
				if(strpos($strElseIf0,'==')===false&&strpos($strElseIf0,'=')>0)$strElseIf0=str_replace('=', '==', $strElseIf0);
				@eval("if(".$strElseIf0."){\$resultStr=\"$strElseIfThen0\";\$elseIfFlag=true;}");
				$content=str_replace($iar[0][$m],$resultStr,$content);
			}
		}
		return $content;
		}
	}
	
	function parseSubIf($content){
		if (strpos($content,'{subif:')=== false){
		return $content;
		}else{
		$labelRule = buildregx("{subif:(.*?)}(.*?){end subif}","is");
		$labelRule2="{elseif";
		$labelRule3="{else}";
		preg_match_all($labelRule,$content,$iar);
		$arlen=count($iar[0]);
		$elseIfFlag=false;
		for($m=0;$m<$arlen;$m++){
			$strIf=$iar[1][$m];
			$strIf=$this->parseStrIf($strIf);
			$strThen=$iar[2][$m];
			$strThen=$this->parseIf($strThen);
			if (strpos($strThen,$labelRule2)===false){
				if (strpos($strThen,$labelRule3)>=0){
					$elsearray=explode($labelRule3,$strThen);
					$strThen1=$elsearray[0];
					$strElse1=$elsearray[1];
					@eval("if(".$strIf."){\$ifFlag=true;}else{\$ifFlag=false;}");
					if ($ifFlag){ $content=str_replace($iar[0][$m],$strThen1,$content);} else {$content=str_replace($iar[0][$m],$strElse1,$content);}
				}else{
				@eval("if(".$strIf.") { \$ifFlag=true;} else{ \$ifFlag=false;}");
				if ($ifFlag) $content=str_replace($iar[0][$m],$strThen,$content); else $content=str_replace($iar[0][$m],"",$content);}
			}else{
				$elseIfArray=explode($labelRule2,$strThen);
				$elseIfArrayLen=count($elseIfArray);
				$elseIfSubArray=explode($labelRule3,$elseIfArray[$elseIfArrayLen-1]);
				$resultStr=$elseIfSubArray[1];
				$elseIfArraystr0=addslashes($elseIfArray[0]);
				@eval("if($strIf){\$resultStr=\"$elseIfArraystr0\";}");
				for($elseIfLen=1;$elseIfLen<$elseIfArrayLen;$elseIfLen++){
					$strElseIf=getSubStrByFromAndEnd($elseIfArray[$elseIfLen],":","}","");
					$strElseIfThen=addslashes(getSubStrByFromAndEnd($elseIfArray[$elseIfLen],"}","","start"));
					$strElseIf=$this->parseStrIf($strElseIf);
					@eval("if(".$strElseIf."){\$resultStr=\"$strElseIfThen\";}");
					@eval("if(".$strElseIf."){\$elseIfFlag=true;}else{\$elseIfFlag=false;}");
					if ($elseIfFlag) {break;}
				}
				$strElseIf0=getSubStrByFromAndEnd($elseIfSubArray[0],":","}","");
				$strElseIfThen0=addslashes(getSubStrByFromAndEnd($elseIfSubArray[0],"}","","start"));
				$strElseIf0=$this->parseStrIf($strElseIf0);
				@eval("if(".$strElseIf0."){\$resultStr=\"$strElseIfThen0\";\$elseIfFlag=true;}");
				$content=str_replace($iar[0][$m],$resultStr,$content);
			}
		}
		return $content;
		}
	}

	function parsePlayPageSpecial($content){
		if(strpos($content,"{playpage:mark")>0)
		{
			$y=getSubStrByFromAndEnd_en($content,"{playpage:mark","}","");
			$q=$this->parseAttr($y);
			$l=$q['len'];
			$c=$q['style'];
			$l=empty($l)?5:$l;
			$c=empty($c)?0:1;
			$content=str_replace("{playpage:mark".$y."}", "<script type=\"text/javascript\">markVideo({playpage:id},0,0,0,".$l.",".$c.");markVideo2({playpage:id},".$c.",".$l.");</script>", $content);
		}
		$content=str_replace("{playpage:reporterr}","<a  href=\"javascript:viod()\" onclick=\"reportErr({playpage:id})\">报 错</a>",$content);
		$content=str_replace("{playpage:digg}","<span id=\"digg_num\">{playpage:diggnum}</span><a  href=\"javascript:viod()\" onclick=\"diggVideo({playpage:id},'digg_num')\">顶一下</a>",$content);
		$content=str_replace("{playpage:tread}","<span id=\"tread_num\">{playpage:treadnum}</span><a  href=\"javascript:viod()\" onclick=\"treadVideo({playpage:id},'tread_num')\">踩一下</a>",$content);
		$content=str_replace("{playpage:comment}","<div  id=\"comment_list\">评论加载中...</div><script>viewComment(\"/".$GLOBALS['cfg_cmspath']."comment.php?id={playpage:id}&type=0\",\"\")</script>",$content);
		$content=str_replace("{playpage:hit}","<span id=\"hit\">加载中...</span><script>getVideoHit('{playpage:id}')</script>",$content);
		return $content;
	}

	function parseSlide($content){
		if (strpos($content,'{seacms:slide')=== false){
		return $content;
		}else{
			$slideStr2=getSubStrByFromAndEnd_en($content,"{seacms:slide","}","");
			$slidewarr=$this->parseAttr($slideStr2);
			$slidew=$slidewarr['width'];
			if(empty($slidew)) $slidew='400';
			$slideharr=$this->parseAttr($slideStr2);
			$slideh=$slideharr['height'];
			if(empty($slideh)) $slideh='280';
			$content=str_replace("{seacms:slide".$slideStr2."}","<script>loadSlide('".$slidew."','".$slideh."',sitePath)</script>",$content);
			return $content;
		}
	}

	function parseCommentList($content,$vId,$currentPage,$totalPages,$pageListType,$type=0){
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:".$pageListType."list(.*?)}(.*?){/seacms:".$pageListType."list}","is");
		$labelRuleField="\[".$pageListType."list:(.*?)\]";
		$labelRulePagelist="\[".$pageListType."list:pagenumber(.*?)\]";
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopstrcomment=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$vsize=empty($attrDictionary["size"]) ? 12 : intval($attrDictionary["size"]);
			$vorder=empty($attrDictionary["order"]) ? "time" : $attrDictionary["order"];
			unset($attrDictionary);
			$limitstart = ($currentPage-1) * $vsize;
			$row = $vsize;
			switch ($vorder) {
				case "id":
					$orderStr =" order by id desc";
				break;
				case "time":
					$orderStr =" order by dtime desc";
				break;
			}
			$sql="select * from `sea_comment` where v_id='$vId' and ischeck='1' ".$orderStr." limit $limitstart,$row";
			$cquery = "Select count(*) as dd From `sea_comment` where v_id='$vId' and ischeck='1'";
			$row = $this->dsql->GetOne($cquery);
			if(is_array($row))
			{
				$TotalResult = $row['dd'];
			}
			else
			{
				$TotalResult = 0;
			}
			$TotalPage = ceil($TotalResult/$vsize);
			$labelRuleField = buildregx($labelRuleField,"is");
			preg_match_all($labelRuleField,$content,$lar);
			$matchfieldarr=$lar[1];
			$matchfieldstrarr=$lar[0];
			$loopstrTotal="";
			$i=1;
			$this->dsql->SetQuery($sql);
			$this->dsql->Execute($pageListType.'list');
			if($TotalResult==0){
				$commentlistInfo[0]="<font color='red'> 还没有评论，等您来抢沙发呢！ </font>";
				if($pageListType=="comment") $loopstrTotal=$commentlistInfo[0];
			}else{
				$rows=array();
				$this->dsql->SetQuery($sql);
				$this->dsql->Execute($pageListType.'list');
				while($rowr=$this->dsql->GetObject($pageListType.'list'))
				{
				$rows[]=$rowr;
				}
				unset($rowr);
				foreach($rows as $row)
				{
					$loopstrcommentNew=$loopstrcomment;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "id":
								$loopstrcommentNew=str_replace($matchfieldvalue,$row->id,$loopstrcommentNew);
							break;
							case "vid":
								$loopstrcommentNew=str_replace($matchfieldvalue,$row->v_id,$loopstrcommentNew);
							break;
							case "username":
								$v_username=$row->username;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_username=!empty($namelen)&&strlen($v_username)>$namelen ? trimmed_title($v_username,$namelen) : $v_username;
								$loopstrcommentNew=str_replace($matchfieldvalue,$v_username,$loopstrcommentNew);
							break;
							case "msg":
								$v_msg=$row->msg;
							$fieldAttrarr=explode(chr(61),$fieldAttr);
							$msglen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
							$v_msg=!empty($msglen)&&strlen($v_msg)>$msglen ? trimmed_title($v_msg,$msglen) : $v_msg;
								$loopstrcommentNew=str_replace($matchfieldvalue,showFace($v_msg),$loopstrcommentNew);
							break;
							case "time":
								$timestyle="";
								$videoTime=$row->dtime;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
								switch (trim($timestyle)) {
									case "yyyy-mm-dd":
								$loopstrcommentNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrcommentNew);
									break;
									case "yy-mm-dd":
								$loopstrcommentNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrcommentNew);
									break;
									case "yyyy-m-d":
								$loopstrcommentNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrcommentNew);
									break;
									case "mm-dd":
									default:
										$loopstrcommentNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrcommentNew);
								}
							break;
							case "i":
								$loopstrcommentNew=str_replace($matchfieldvalue,($TotalResult-$i-($currentPage-1)*$vsize+1),$loopstrcommentNew);
							break;
							case "ip":
								$loopstrcommentNew=str_replace($matchfieldvalue,preg_replace('/((?:\d+\.){3})\d+/',"\\1*",$row->ip),$loopstrcommentNew);
							break;
					}
					}
				$i=$i+1;
				$loopstrTotal=$loopstrTotal.$loopstrcommentNew;
				}
			}
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
		}
		$labelRulePagelist = buildregx($labelRulePagelist,"is");
		preg_match_all($labelRulePagelist,$content,$plar);
		$arlen=count($plar[1]);
		for($p=0;$p<$arlen;$p++){
			if($TotalResult==0){
				$content=str_replace($plar[0][$p],"",$content);
			}else{
				$fieldAttr =$plar[1][$p];
				$fieldAttr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$fieldAttr));
				$fieldAttrarr=explode(chr(61),$fieldAttr);
				$lenPagelist=empty($fieldAttr) ? 10 : intval($fieldAttrarr[1]);
				$strPagelist=pageNumberLinkInfo($currentPage,$lenPagelist,$TotalPage,$pageListType,$TotalResult,$vId);
				$content=str_replace($plar[0][$p],$strPagelist,$content);
			}
		}
		return $content;
	}

	function parseHistory($content){
		$content=str_replace("{seacms:showhistory}","<a href=\"javascript:void(0)\" onclick=\"\$MH.showHistory(1);\">我的观看历史</a>",$content);
		if (strpos($content,'{seacms:maxhistory')=== false){
			return $content;
		}else{
			$y=getSubStrByFromAndEnd_en($content, "{seacms:maxhistory","}","");
			$q=$this->parseAttr($y);
			$w=$q['width'];
			$h=$q['height'];
			$i=$q['num'];
			$c=$q['style'];
			unset($q);
			if(empty($w)) $w=960;
			if(empty($h)) $h=170;
			if(empty($i)) $i=10;
			$content=str_replace("{seacms:maxhistory".$y."}","<script type=\"text/javascript\" src=\"/".$GLOBALS['cfg_cmspath']."js/history.js\"></script><script type=\"text/javascript\">\$MH.limit=".$i.";\$MH.WriteHistoryBox(".$w.",".$h.",'".$c."');\$MH.recordHistory({name:'{playpage:name}',link:'{playpage:url}',pic:'{playpage:pic}'})</script>",$content);
			return $content;
		}
		return $content;
	}
	
	function parseNewsPageSpecial($content){
		if (strpos($content,'{news:mark')>0){
			$y=getSubStrByFromAndEnd_en($content, "{news:mark","}","");
			$q=$this->parseAttr($y);
			$l=$q['len'];
			
			$c=$q['style'];
			$l=empty($l)?5:$l;
			$c=(empty($c)||$c=='radio')?0:1;
			$content=str_replace("{news:mark".$y."}","<script type=\"text/javascript\">markNews({news:id},0,0,0,".$l.",".$c.");markNews2({news:id},".$c.",".$l.");</script>", $content);
			
		}
		$content=str_replace("{news:digg}","<span id=\"digg_num\">{news:diggnum}</span><a href=\"javascript:void(0)\" onclick=\"diggNews({news:id},'digg_num')\">顶一下</a>",$content);
		$content=str_replace("{news:tread}","<span id=\"tread_num\">{news:treadnum}</span><a href=\"javascript:void(0)\" onclick=\"treadNews({news:id},'tread_num')\">踩一下</a>",$content);
		$content=str_replace("{news:comment}","<div  id=\"comment_list\">评论加载中..</div><script>viewComment(\"/".$GLOBALS['cfg_cmspath']."comment.php?id={news:id}&type=1\",\"\")</script>",$content);
		$content=str_replace("{news:hit}","<span id=\"hit\">加载中</span><script type='text/javascript'>getNewsHit('{news:id}')</script>",$content);
		return $content;
	}


	function paresPreNextNews($content,$dataId,$typeFlag,$vtype)
	{
		$preNextLabel="{news:prenext}";
		if (strpos($content,$preNextLabel)=== false){
			return $content;
		}else{
			$rown = $this->dsql->GetOne("select n_id as nextid ,n_title as nextname from sea_news where tid='$vtype' and n_id<".$dataId." order by n_id desc");
			if(is_array($rown)){
				$nextid=$rown["nextid"];
				$nextname=$rown["nextname"];
			}else{
				$nextid=0;
			}
			$rowl = $this->dsql->GetOne("select n_id as lastid ,n_title as lastname from sea_news where tid='$vtype' and n_id>".$dataId." order by n_id asc");
			if(is_array($rowl)){
				$lastid=$rowl["lastid"];
				$lastname=$rowl["lastname"];
			}else{
				$lastid=0;
			}
			if ($lastid == 0) $mystr = "<span>上一篇:没有了</span> "; else $mystr = "<span>上一篇:<a href=".getArticleLink($vtype,$lastid,'').">".$lastname."</a></span> ";
			if ($nextid == 0) $mystr .= "<span>下一篇:没有了</span>"; else $mystr .= "<span>下一篇:<a href=".getArticleLink($vtype,$nextid	,'').">".$nextname."</a></span>";
			$content=str_replace($preNextLabel,$mystr,$content);
			return $content;
		}
	}
	
	function parseNews($content,$title,$color,$txt,$addtime)
	{
		$labelRule = buildregx("{news:([\s\S]+?)}","is");
		preg_match_all($labelRule,$content,$matches);
		$mtlen=count($matches[1]);
		for($m=0;$m<$mtlen;$m++)
		{
			$matchfieldvalue=$matches[0][$m];
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matches[1][$m]));
			if (strpos($attrStr," ")>0){
				$fieldtemparr=explode(" ",$attrStr);
				$fieldName=$fieldtemparr[0];
				$fieldAttr =$fieldtemparr[1];
			}else{
				$fieldName=$attrStr;
				$fieldAttr ="";
			}
			switch (trim($fieldName)) {
				case "title":
					$fieldAttrarr=explode(chr(61),$fieldAttr);
					$titlelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
					$title=!empty($titlelen)&&strlen($title)>$titlelen ? trimmed_title($title,$titlelen) : $title;
					$content=str_replace($matchfieldvalue,$title,$content);
				break;
				case "colortitle":
					$fieldAttrarr=explode(chr(61),$fieldAttr);
					$titlelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
					$title=!empty($titlelen)&&strlen($title)>$titlelen ? trimmed_title($title,$titlelen) : $title;
					if(!empty($color))$title="<font color='".$color."'>".$title."</font>";
					$content=str_replace($matchfieldvalue,$title,$content);
				break;
				case "content":
					$txt=Html2Text($txt);
					$fieldAttrarr=explode(chr(61),$fieldAttr);
					$txtlen=empty($fieldAttr) ? 200 : intval($fieldAttrarr[1]);
					$txt=!empty($txtlen)&&strlen($txt)>$txtlen ? trimmed_title($txt,$txtlen) : $txt;
					$content=str_replace($matchfieldvalue,$txt,$content);
				break;
				case "addtime":
				case "time":
					$fieldAttrarr=explode(chr(61),$fieldAttr);
					$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
					switch (trim($timestyle)) {
						case "yyyy-mm-dd":
							$content=str_replace($matchfieldvalue,MyDate("Y-m-d",$addtime),$content);
						break;
						case "yy-mm-dd":
							$content=str_replace($matchfieldvalue,MyDate("y-m-d",$addtime),$content);
						break;
						case "yyyy-m-d":
							$content=str_replace($matchfieldvalue,MyDate("Y-n-j",$addtime),$content);
						break;
						case "mm-dd":
						default:
							$content=str_replace($matchfieldvalue,MyDate("m-d",$addtime),$content);
						break;
					}
				break;
			}
		}
		return $content;
	}
	
	function paresVideoInNews($content)
	{
		global $cfg_playaddr_enc,$dsql;
		if (strpos($content,'{news:video')=== false){
			return $content;
		}else{
			$labelRule = buildregx("{news:video([\s\S]+?)}","is");
			preg_match_all($labelRule,$content,$matches);
			$matchfieldvalue=$matches[0][0];
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matches[1][0]));
			$attrDictionary=$this->parseAttr($attrStr);
			$vid=empty($attrDictionary["vid"]) ? '' : $attrDictionary["vid"];
			if(empty($vid)) return $content;
			$vidArr=explode(',',$vid);
			$row=$dsql->GetOne("select body as v_playdata from sea_playdata where v_id=".$vidArr[0]);
			if($cfg_playaddr_enc=='escape'){
				$content = str_replace($matchfieldvalue,"<script>var VideoInfoList=unescape(\"".escape($row['v_playdata'])."\");var paras='".$vid."'.split(',');_lOlOl10l(paras[2],paras[1])</script>",$content); 
			}elseif($cfg_playaddr_enc=='base64'){
				$content = str_replace($matchfieldvalue,"<script>var VideoInfoList=base64decode(\"".base64_encode($row['v_playdata'])."\");var paras='".$vid."'.split(',');_lOlOl10l(paras[2],paras[1])</script>",$content); 
			}else{
				$content = str_replace($matchfieldvalue,"<script>var VideoInfoList=\"".$row['v_playdata']."\";var paras='".$vid."'.split(',');_lOlOl10l(paras[2],paras[1])</script>",$content); 
			}
		}
		return $content;
	}
	
	
	function parseCustomList($pSize,$order,$lang,$type,$maxpage,$time,$area,$year,$letter,$commend,$state,$jq,$content,$typeId,$currentPage,$TotalPage,$TotalResult,$pageListType,$currentTypeId=-444)
	{
		global $cfg_issqlcache;
		$attrDictionary=array();
		$labelRule = buildregx("{seacms:".$pageListType."list(.*?)}(.*?){/seacms:".$pageListType."list}","is");
		$labelRuleField="\[".$pageListType."list:(.*?)\]";
		$labelRulePagelist="\[".$pageListType."list:pagenumber(.*?)\]";
		if (strpos($content,"[".$pageListType."list:des")>0){
			$field_des="c.body as v_content";
			$left_des=" left join `sea_content` c on c.v_id=m.v_id ";
		}else{
			$field_des=0;
			$left_des="";
		}
		if (strpos($content,"[".$pageListType."list:from")>0){
			$field_playdata="p.body as v_playdata";
			$left_playdata=" left join `sea_playdata` p on p.v_id=m.v_id ";
		}else{
			$field_playdata=0;
			$left_playdata="";
		}
		$whereStr=" where v_recycled=0";
					if(!empty($type)) $whereStr.=" and m.tid in ($type)";
					if(!empty($year)) $whereStr.=" and m.v_publishyear='$year'";
					if(!empty($letter)) $whereStr.=" and m.v_letter='$letter'";
					if(!empty($area)) $whereStr.=" and m.v_publisharea='$area'";
					if(!empty($lang)) $whereStr.=" and m.v_lang='$lang'";
					if(!empty($jq)) $whereStr.=" and m.v_jq like '%$jq%'";
		preg_match_all($labelRule,$content,$ar);
		$arlen=count($ar[1]);
		for($m=0;$m<$arlen;$m++){
			$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][$m]));
			$loopstrChannel=$ar[2][$m];
			$attrDictionary=$this->parseAttr($attrStr);
			$vsize=empty($attrDictionary["size"]) ? 12 : intval($attrDictionary["size"]);
			$vorder=empty($attrDictionary["order"]) ? "time" : $attrDictionary["order"];
			unset($attrDictionary);
			if(intval($currentPage)>intval($TotalPage)) $currentPage=$TotalPage;
			$limitstart = ($currentPage-1) * $vsize;
			if($limitstart<0) $limitstart=0;
			switch ($order) {
				case "id":
					$orderStr =" order by m.v_id desc";
				break;
				case "year":
					$orderStr =" order by m.v_publishyear desc";
				break;
				case "hit":
					$orderStr =" order by m.v_hit desc";
				break;
				case "dayhit":
					$orderStr =" order by v_dayhit desc";
				break;
				case "weekhit":
					$orderStr =" order by v_weekhit desc";
				break;
				case "monthhit":
					$orderStr =" order by v_monthhit desc";
				break;
				case "time":
					$orderStr =" order by m.v_addtime desc";
				break;
				case "name":
					$orderStr =" order by m.v_name asc";
				break;
				case "digg":
					$orderStr =" order by m.v_digg desc";
				break;
				case "hot":
					$orderStr =" order by m.v_hit desc";
				break;
				case "score":
					$orderStr =" order by m.v_score desc";
				break;
				case "douban":
					$orderStr =" order by m.v_douban desc";
				break;
				case "mtime":
					$orderStr =" order by m.v_mtime desc";
				break;
				case "imdb":
					$orderStr =" order by m.v_imdb desc";
				break;
				case "letter":
					$orderStr =" order by m.v_letter asc";
				break;
				//asc
				case "idasc":
					$orderStr =" order by m.v_id asc";
				break;
				case "yearasc":
					$orderStr =" order by m.v_publishyear asc";
				break;
				case "hitasc":
					$orderStr =" order by m.v_hit asc";
				break;
				case "timeasc":
					$orderStr =" order by m.v_addtime asc";
				break;	
				case "diggasc":
					$orderStr =" order by m.v_digg asc";
				break;
				case "hotasc":
					$orderStr =" order by m.v_hit asc";
				break;
			}
					
			
			$sql="select m.*,".$field_des.",".$field_playdata." from sea_data m ".$left_des.$left_playdata.$whereStr." ".$orderStr." limit $limitstart,$vsize";
			$labelRuleField = buildregx($labelRuleField,"is");
			preg_match_all($labelRuleField,$content,$lar);
			$matchfieldarr=$lar[1];
			$matchfieldstrarr=$lar[0];
			$loopstrTotal="";
			if($TotalResult==0){
				$loopstrTotal="暂无结果";
			}else{
				if($cfg_issqlcache){
					$mycachefile=md5('PageList'.$whereStr.$orderStr.$limitstart.$row);
					setCache($mycachefile,$sql);
					$rows=getCache($mycachefile);
				}else{
					$rows=array();
					$this->dsql->SetQuery($sql);
					$this->dsql->Execute($pageListType.'list');
					while($rowr=$this->dsql->GetObject($pageListType.'list'))
					{
						$rows[]=$rowr;
					}
					unset($rowr);
				}
				$i=1;
				foreach($rows as $row)
				{
					$loopstrChannelNew=$loopstrChannel;
					foreach($matchfieldarr as $f=>$matchfieldstr){
						$matchfieldvalue=$matchfieldstrarr[$f];
						$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
						if (strpos($matchfieldstr," ")>0){
							$fieldtemparr=explode(" ",$matchfieldstr);
							$fieldName=$fieldtemparr[0];
							$fieldAttr =$fieldtemparr[1];
						}else{
							$fieldName=$matchfieldstr;
							$fieldAttr ="";
						}
						switch (trim($fieldName)) {
							case "i":
								$loopstrChannelNew=str_replace($matchfieldvalue,$i,$loopstrChannelNew);
							break;
							case "id":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_id,$loopstrChannelNew);
							break;
							case "typeid":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->tid,$loopstrChannelNew);
							break;
							case "typename":
								$loopstrChannelNew=str_replace($matchfieldvalue,getTypeNameOnCache($row->tid).getExtraTypeName($row->v_extratype),$loopstrChannelNew);
							break;
							case "linktypename":
								$connector = "</a>";
								$loopstrChannelNew=str_replace($matchfieldvalue,"<a href=\"".getChannelPagesLink($row->tid)."\">".getTypeName($row->tid).$connector.getExtraTypeName($row->v_extratype,$connector).$connector,$loopstrChannelNew);
							break;
							case "typelink":
								$loopstrChannelNew=str_replace($matchfieldvalue,getChannelPagesLink($row->tid),$loopstrChannelNew);
							break;
							case "name":
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$namelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($namelen)&&strlen($v_name)>$namelen ? trimmed_title($v_name,$namelen) : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "colorname":
								$v_color=$row->v_color;
								$v_name=$row->v_name;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$colornamelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_name=!empty($colornamelen)&&strlen($v_name)>$colornamelen ? trimmed_title($v_name,$colornamelen) : $v_name;
								$v_name=$v_color ? "<font color=".$v_color.">".$v_name."</font>" : $v_name;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_name,$loopstrChannelNew);
							break;
							case "note":
								$v_note=$row->v_note;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$notelen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_note=!empty($notelen)&&strlen($v_note)>$notelen ? trimmed_title($v_note,$notelen) : $v_note;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_note,$loopstrChannelNew);
							break;
							case "link":
								$sdate = date('Y-n',$row->v_addtime);
								$loopstrChannelNew=str_replace($matchfieldvalue,getContentLink($row->tid,$row->v_id,"link",$sdate,$row->v_enname),$loopstrChannelNew);
							break;
							case "pic":
								$v_pic=$row->v_pic;
								if(!empty($v_pic)){
									if(strpos(' '.$v_pic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_pic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_pic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;
							case "spic":
								$v_spic=$row->v_spic;
								if(!empty($v_spic)){
									if(strpos(' '.$v_spic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_spic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_spic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;
							case "gpic":
								$v_gpic=$row->v_gpic;
								if(!empty($v_gpic)){
									if(strpos(' '.$v_gpic,'://')>0){
										$loopstrChannelNew=str_replace($matchfieldvalue,$v_gpic,$loopstrChannelNew);
									}else{
										$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].ltrim($v_gpic,'/'),$loopstrChannelNew);
									}
								}else{
									$loopstrChannelNew=str_replace($matchfieldvalue,'/'.$GLOBALS['cfg_cmspath'].'images/defaultpic.gif',$loopstrChannelNew);
								}
							break;
							case "actor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$v_actor=getKeywordsList($v_actor,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_actor,$loopstrChannelNew);
							break;
							case "nolinkactor":
								$v_actor=$row->v_actor;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$actorlen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_actor=!empty($actorlen)&&strlen($v_actor)>$actorlen ? trimmed_title($v_actor,$actorlen) : $v_actor;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_actor,$loopstrChannelNew);
							break;
							case "hit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_hit,$loopstrChannelNew);
							break;
							case "dayhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_dayhit,$loopstrChannelNew);
							break;
							case "weekhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_weekhit,$loopstrChannelNew);
							break;
							case "monthhit":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_monthhit,$loopstrChannelNew);
							break;
							case "nickname":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_nickname,$loopstrChannelNew);
							break;
							case "reweek":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_reweek,$loopstrChannelNew);
							break;
							case "vodlen":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_len,$loopstrChannelNew);
							break;
							case "vodtotal":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_total,$loopstrChannelNew);
							break;
							case "douban":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_douban,$loopstrChannelNew);
							break;
							case "mtime":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_mtime,$loopstrChannelNew);
							break;
							case "imdb":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_imdb,$loopstrChannelNew);
							break;
							case "tvs":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tvs,$loopstrChannelNew);
							break;
							case "company":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_company,$loopstrChannelNew);
							break;
							case "des":
								$v_des=htmlspecialchars_decode($row->v_content);
								$v_des=Html2Text($v_des);
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$deslen=empty($fieldAttr) ? "" : intval($fieldAttrarr[1]);
								$v_des=!empty($deslen)&&strlen($v_des)>$deslen ? trimmed_title($v_des,$deslen) : $v_des;
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_des,$loopstrChannelNew);
							break;
							case "time":
								$timestyle="";
								$videoTime=$row->v_addtime;
								$fieldAttrarr=explode(chr(61),$fieldAttr);
								$timestyle=empty($fieldAttr) ? "m-d" : $fieldAttrarr[1];
								switch (trim($timestyle)) {
									case "yyyy-mm-dd":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yy-mm-dd":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("y-m-d",$videoTime),$loopstrChannelNew);
									break;
									case "yyyy-m-d":
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("Y-n-j",$videoTime),$loopstrChannelNew);
									break;
									case "mm-dd":
									default:
										$loopstrChannelNew=str_replace($matchfieldvalue,MyDate("m-d",$videoTime),$loopstrChannelNew);
								}
							break;
							case "from":
								$loopstrChannelNew=str_replace($matchfieldvalue,getFromStr($row->v_playdata),$loopstrChannelNew);
							break;
							case "state":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_state,$loopstrChannelNew);
							break;
							case "commend":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_commend,$loopstrChannelNew);
							break;
							case "publishtime":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_publishyear,$loopstrChannelNew);
							break;
							case "ver":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_ver,$loopstrChannelNew);
							break;
							case "publisharea":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_publisharea,$loopstrChannelNew);
							break;
							case "playlink":
								if($GLOBALS['cfg_isalertwin']==1) $playlink_str="javascript:openWin('".getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname)."',".($GLOBALS['cfg_alertwinw']).",".($GLOBALS['cfg_alertwinh']).",250,100,1)";
								else $playlink_str=getPlayLink2($row->tid,$row->v_id,date('Y-n',$row->v_addtime),$row->v_enname);
								$loopstrChannelNew=str_replace($matchfieldvalue,$playlink_str,$loopstrChannelNew);
							break;
							case "favLink":
								@session_start();
								if(isset($_SESSION['sea_user_auth'])||$GLOBALS['cfg_user']==1)
								{														
									$uid=$_SESSION['sea_user_id'];
									$favLink_str = "javascript:AddFav('".$row->v_id."','$uid')";
								}else
								{
									$favLink_str = "#";
								}
								$loopstrChannelNew=str_replace($matchfieldvalue,$favLink_str,$loopstrChannelNew);
							break;
							case "director":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_director,$loopstrChannelNew);
							break;
							case "money":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_money,$loopstrChannelNew);
							break;
							case "lang":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_lang,$loopstrChannelNew);
							break;
							case "digg":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_digg,$loopstrChannelNew);
							break;
							case "tread":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tread,$loopstrChannelNew);
							break;
							case "scorenum":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_score,$loopstrChannelNew);
							break;
							case "scorenumer":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_scorenum,$loopstrChannelNew);
							break;
							case "score":
								$score=number_format($row->v_score/$row->v_scorenum,1);
								$loopstrChannelNew=str_replace($matchfieldvalue,$score,$loopstrChannelNew);
							break;
							case "keyword":
								$v_tags=getKeywordsList($row->v_tags,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_tags,$loopstrChannelNew);
							break;
							case "nolinkkeyword":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_tags,$loopstrChannelNew);
							break;
							case "jqtype":
								$v_jq=getJqList($row->v_jq,"&nbsp;");
								$loopstrChannelNew=str_replace($matchfieldvalue,$v_jq,$loopstrChannelNew);
							break;
							case "nolinkjqtype":
								$loopstrChannelNew=str_replace($matchfieldvalue,$row->v_jq,$loopstrChannelNew);
							break;
						}
					}
					$i=$i+1;
					$loopstrTotal=$loopstrTotal.$loopstrChannelNew;
				}
				unset($rows);
			}
			$content=str_replace($ar[0][$m],$loopstrTotal,$content);
		}
		$labelRulePagelist = buildregx($labelRulePagelist,"is");
		preg_match_all($labelRulePagelist,$content,$plar);
		$arlen=count($plar[1]);
		for($p=0;$p<6;$p++){
			if($TotalResult==0){
				$content=str_replace($plar[0][$p],"",$content);
			}else{
				$fieldAttr =$plar[1][$p];
				$fieldAttr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$fieldAttr));
				$fieldAttrarr=explode(chr(61),$fieldAttr);
				$lenPagelist=empty($fieldAttr) ? 10 : intval($fieldAttrarr[1]);
				$strPagelist=pageNumberLinkInfo($currentPage,$lenPagelist,$TotalPage,$pageListType,$TotalResult,$currentTypeId);
				$content=str_replace($plar[0][$p],$strPagelist,$content);
			}
		}
		$tpages=6;
		if(strpos($content,"{/".$pageListType."list:pagenumber}")>0)
		{
			preg_match_all("|{".$pageListType."list:pagenumber(.*?)}(.*?){/".$pageListType."list:pagenumber}|is", $content, $matchesPagelist);
			foreach ($matchesPagelist[1] as $k=>$matchPagelist){
				$attr=$this->parseAttr($matchPagelist);
				$len=$attr['len'];
				$len=empty($len)?10:$len;
				$strPagelist=$matchesPagelist[2][$k];
				$strPagelist=makePageNumberLoop($currentPage,$len,$tpages,$strPagelist,$pageListType,$currentTypeId);
				$content=str_replace($matchesPagelist[0][$k],$strPagelist,$content);	
			}
		}
		$content=str_replace("{".$pageListType."list:page}",$currentPage,$content);	
		$content=str_replace("{".$pageListType."list:pagecount}",$tpages,$content);
		$content=str_replace("{".$pageListType."list:recordcount}",$TotalResult,$content);	
		$content=str_replace("{".$pageListType."list:firstlink}",getPageLink(1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:backlink}",getPageLink($currentPage-1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:nextlink}",getPageLink($currentPage+1,$pageListType,$currentTypeId),$content);	
		$content=str_replace("{".$pageListType."list:lastlink}",getPageLink($tpages,$pageListType,$currentTypeId),$content);		
		return $content;
	}

}

$mainClassObj=new MainClass_Template;
?>