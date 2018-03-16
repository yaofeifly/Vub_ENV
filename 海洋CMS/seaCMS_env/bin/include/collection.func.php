<?php
/*
	[seacms1.0] (C)2011-2012 seacms.net
*/
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
@set_time_limit(0);
ob_implicit_flush();

function Testlists($listconfig,$coding='gb2312',$sock=0)
{
	@session_write_close();
	$labelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($labelRule,$listconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	$pageset=$attrDictionary["pageset"];
	if($pageset==0){
		$pageurl0=$attrDictionary["pageurl0"];
		$dourl=$pageurl0;
	}else{
		$pageurl1=$attrDictionary["pageurl1"];
		$pageurl2=$attrDictionary["pageurl2"];
		$istart=$attrDictionary["istart"];
		$iend=$attrDictionary["iend"];
		$pageurlarr=GetUrlFromListRule($pageurl1,$pageurl2,$istart,$iend);
		$dourl=$pageurlarr[0][0];
	}
	$lista=getrulevalue($loopstr,"lista");
	$listb=getrulevalue($loopstr,"listb");
	$mlinka=getrulevalue($loopstr,"mlinka");
	$mlinkb=getrulevalue($loopstr,"mlinkb");
	$picmode=getrulevalue($loopstr,"picmode");
	$pica=getrulevalue($loopstr,"pica");
	$picb=getrulevalue($loopstr,"picb");
	$pic_trim=getrulevalue($loopstr,"pic_trim");
		if(empty($dourl))
		{
			return "配置中指定列表的网址错误!\r\n";
		}
		else
		{
			$html = cget($dourl,$sock);
			$html = ChangeCode($html,$coding);
			if($html=='')
			{
				return "读取网址： $dourl 时失败！\r\n";
			}
			if( trim($lista) !='' && trim($listb) != '' )
			{
				$areabody = $lista.'[var:区域]'.$listb;
				$html = GetHtmlArea('[var:区域]',$areabody,$html);
			}
			if( trim($mlinka) !='' && trim($mlinkb) != '' )
			{
				$linkrulex = $mlinka.'(.*)'.$mlinkb;
				$link = GetHtmlarray($html,$linkrulex);
				foreach($link as $key=>$s)
				{
					$links[$key][url] = FillUrl($dourl,$s);
				}
			}
			if(trim($picmode)==1 && trim($pica) !='' && trim($picb) != '' )
			{
				$picrulex = $pica.'(.*)'.$picb;
				$piclink = GetHtmlarray($html,$picrulex);
				foreach($piclink as $key=>$s)
				{
					if(!empty($pic_trim)) $s=Gettrimvalue($pic_trim,$s);
					$links[$key][pic] = FillUrl($dourl,$s);
				}
			}
		}
		unset($attrDictionary);
return $links;
}

function GetUrlFromListRule($regxurl='',$handurl='',$startid=0,$endid=0)
{
	$lists = array();
	$n = 0;
	if($handurl!='')
	{
		$handurls = explode("\n",$handurl);
		foreach($handurls as $handurl)
		{
			$handurl = trim($handurl);
			if(m_eregi("^http://",$handurl))
			{
				$lists[$n][0] = $handurl;
				$lists[$n][1] = 0;
				$n++;
			}
		}
		return $lists;
	}
	if($regxurl!='')
	{
		//没指定(#)和(*)
		if(!m_ereg("\(\*\)",$regxurl))
		{
			$lists[$n][0] = $regxurl;
			$lists[$n][1] = 0;
			$n++;
		}
		else
		{
			if($addv <= 0)
			{
				$addv = 1;
			}

			//没指定多栏目匹配规则
			if($usemore==0)
			{
				while($startid <= $endid)
				{
					$lists[$n][0] = str_replace("(*)",sprintf('%0'.strlen($startid).'d',$startid),$regxurl);
					$lists[$n][1] = 0;
					$startid = sprintf('%0'.strlen($startid).'d',$startid + $addv);
					$n++;
					if($n>2000 || $islisten==1)
					{
						break;
					}
				}
			}

		} //End使用规则匹配的情况

	}

	return $lists;
}

function getTestItemRule($tid,$previewurl,$previewpic='')
{
	@session_write_close();
	$tid = empty($tid) ? 0 : intval($tid);
	if($tid==0) return false;
	global $dsql;
	$row = $dsql->GetOne("Select coding,sock,playfrom,autocls,classid,itemconfig,listconfig,getherday from `sea_co_type` where tid='$tid'");
	$itemconfig=$row['itemconfig'];
	$listconfig=$row['listconfig'];
	$labelRule1 = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($labelRule1,$listconfig,$ar1);
	$listattrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar1[1][0]));
	$listDictionary=parseAttr($listattrStr);
	$removecode = $listDictionary['removecode'];
	$coding=$row['coding'];
	$sock=$row['sock'];
	$playfrom=$row['playfrom'];
	$classid=$row['classid'];
	$autocls=$row['autocls'];
	$getherday=$row['getherday'];
	$labelRule = buildregx("{seacms:itemconfig(.*?)}(.*?){/seacms:itemconfig}","is");
	preg_match_all($labelRule,$itemconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	$html = cget($previewurl,$sock);
	$html = ChangeCode($html,$coding);
	echo "<font color='red'>测试地址</font>：".$previewurl."<br>";
	getTestAreaValue($loopstr,"name","影片名称",$html,$removecode);
	if(trim($previewpic)!=''){
		echo "<font color='red'>图片地址</font>：".$previewpic."<br>";
	}else{
		getTestAreaValue($loopstr,"pic","影片图片",$html,$removecode);
	}
	getTestAreaValue($loopstr,"actor","影片演员",$html,$removecode);
	getTestAreaValue($loopstr,"director","影片导演",$html,$removecode);
	getTestAreaValue($loopstr,"parea","影片地区",$html,$removecode);
	getTestAreaValue($loopstr,"pyear","影片年份",$html,$removecode);
	getTestAreaValue($loopstr,"plang","影片语言",$html,$removecode);
	getTestAreaValue($loopstr,"state","影片连载",$html,$removecode);
	getTestAreaValue($loopstr,"note","影片备注",$html,$removecode);
	getTestAreaValue($loopstr,"des","影片介绍",$html,$removecode);
	//处理时间
	if($getherday){
		getTestAreaValue($loopstr,"pdate","更新时间",$html,$removecode);
	}
	//处理分类
	if($autocls){
		getTestAreaValue($loopstr,"cls","影片分类",$html,$removecode);
	}else{
		echo "<font color='red'>影片分类</font>：".getTypeName($classid)."<br>";
	}
	//开始处理播放区域
	$splay=$attrDictionary["splay"];
	if(trim($splay)==1){
		$playareahtml=Geturlarray($html,getrulevalue($loopstr,"plista").'[内容]'.getrulevalue($loopstr,"plistb"));
	}else{
		$playareahtml[0]=$html;
	}
	$playgetsrc=$attrDictionary["playgetsrc"];
	$playlinka=getrulevalue($loopstr,"playlinka");
	$playlinkb=getrulevalue($loopstr,"playlinkb");
	$playlink_trim=getrulevalue($loopstr,"playlink_trim");
	$msrca=getrulevalue($loopstr,"msrca");
	$msrcb=getrulevalue($loopstr,"msrcb");
	$msrc_trim=getrulevalue($loopstr,"msrc_trim");
	$playurlarray=array();
	$weburl=array();
	$weburltemp=array();
	
	//获取下载地址开始
			  $downurlarray=array();
			  $downa=getrulevalue($loopstr,"downa");
			  $downb=getrulevalue($loopstr,"downb");
			  $down_trim=getrulevalue($loopstr,"down_trim");
			  if(trim($downa) !='' && trim($downb) != ''){
				  $downurlarray[]=Geturlarray($html,$downa.'[内容]'.$downb,$down_trim);
			  }
			  //获取下载地址结束
	//获取播放地址
	if(trim($playgetsrc)==1 && trim($playlinka) !='' && trim($playlinkb) != ''){
		foreach($playareahtml as $sv){
			$weburltemp=Geturlarray($sv,$playlinka.'[内容]'.$playlinkb,$playlink_trim);
			$weburltemp=array_unique($weburltemp);
			sort($weburltemp);
			$weburl[]=$weburltemp;
		}
		$playurlarray=Getplayurlarr($weburl,$msrca.'[内容]'.$msrcb,$msrc_trim,$previewurl,$sock,$coding);
	}else{
		if(trim($msrca) !='' && trim($msrcb) != ''){
			foreach($playareahtml as $sv){
			$weburl[]=Geturlarray($sv,$msrca.'[内容]'.$msrcb,$msrc_trim);
			}
			$playurlarray=$weburl;
		}
	}
	unset($weburl);
	unset($weburltemp);
	//截取分集名称
	$getpart=$attrDictionary["getpart"];
	$parta=getrulevalue($loopstr,"parta");
	$partb=getrulevalue($loopstr,"partb");
	$part_trim=getrulevalue($loopstr,"part_trim");
	$partarray=array();
	$webparttemp=array();
	if(trim($getpart)==1 && trim($parta) !='' && trim($partb) != ''){
		foreach($playareahtml as $sv){
			$webparttemp=Geturlarray($sv,$parta.'[内容]'.$partb,$part_trim);
			$webparttemp=array_unique($webparttemp);
			sort($webparttemp);
			$webpart[]=$webparttemp;
		}
		$partarray=$webpart;
	}
	unset($webpart);
	unset($webparttemp);
	//播放器获取
	$serveron=$attrDictionary["serveron"];
	if($serveron==2){
		$server[0]=$playfrom;
	}else{
		$servera=getrulevalue($loopstr,"servera");
		$serverb=getrulevalue($loopstr,"serverb");
		$server_trim=getrulevalue($loopstr,"server_trim");
		if($serveron==1) $server=Geturlarray($playareahtml,$servera.'(.*)'.$serverb,$server_trim);
		if($serveron==0) $server=Geturlarray($html,$servera.'(.*)'.$serverb,$server_trim);
	}
	if($serveron==2 && $playfrom==''){
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
	}else if($serveron==2 && $playfrom!=''){
		if($getpart==2)
		{
			$geturl='';
			foreach($playurlarray as $psv){
				$geturltemp='';
				foreach($psv as $ppsv){
					$geturltemp .= $ppsv.'#';
				}
				$geturltemp=rtrim($geturltemp,'#');
				$geturl .= $playfrom.'$$'.$geturltemp.'$$$';
			}
			$geturl=rtrim($geturl,'$$$');
		}else
		{
			$geturl=transferUrlatr($playfrom,$playurlarray,$partarray);
		}
	}else{
		if($getpart==2)
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
	$v_downdata = GenerateDownUrl($listDictionary["downfrom"],$downurlarray,$partarray);
	if(!empty($geturl))
	{
		echo "<font color='red'>播放地址</font>：".$geturl.'<br>';
	}
	if(!empty($v_downdata))
	{
		echo "<font color='red'>下载地址</font>：".$v_downdata;
	}
	unset($attrDictionary);
}

function getNewsTestItemRule($tid,$previewurl,$previewpic='')
{
	@session_write_close();
	$tid = empty($tid) ? 0 : intval($tid);
	if($tid==0) return false;
	global $dsql;
	$row = $dsql->GetOne("Select coding,sock,playfrom,autocls,classid,itemconfig,getherday from `sea_co_type` where tid='$tid'");
	$itemconfig=$row['itemconfig'];
	$coding=$row['coding'];
	$sock=$row['sock'];
	$playfrom=$row['playfrom'];
	$classid=$row['classid'];
	$autocls=$row['autocls'];
	$getherday=$row['getherday'];
	$labelRule = buildregx("{seacms:itemconfig(.*?)}(.*?){/seacms:itemconfig}","is");
	preg_match_all($labelRule,$itemconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	$html = cget($previewurl,$sock);
	$html = ChangeCode($html,$coding);
	echo "<font color='red'>测试地址</font>：".$previewurl."<br>";
	getTestAreaValue($loopstr,"name","新闻标题",$html);
	if(trim($previewpic)!=''){
		echo "<font color='red'>图片地址</font>：".$previewpic."<br>";
	}else{
		getTestAreaValue($loopstr,"pic","新闻图片",$html);
	}
	if($autocls){
		getTestAreaValue($loopstr,"cls","新闻分类",$html);
	}else{
		echo "<font color='red'>新闻分类</font>：".getNewsTypeName($classid)."<br>";
	}
	getTestAreaValue($loopstr,"author","新闻作者",$html);
	getTestAreaValue($loopstr,"pdate","新闻日期",$html);
	getTestAreaValue($loopstr,"parea","新闻来源",$html);
	getTestAreaValue($loopstr,"state","新闻关键字",$html);	
	getTestAreaValue($loopstr,"des","新闻正文",$html);
	unset($attrDictionary);
}

function getTestAreaValue($loopstr,$str,$strname,$html,$removecode='')
{
	$namea=getrulevalue($loopstr,$str."a");
	$nameb=getrulevalue($loopstr,$str."b");
	$trimRule=getrulevalue($loopstr,$str."_trim");
	if( trim($namea) !='' && trim($nameb) != '' ){
		$name=GetHtmlArea('[内容]',$namea.'[内容]'.$nameb,$html);
		if($removecode!=''){
			$name=removeHTMLCode($name,$removecode);
		}
		if($trimRule!='') 
		{ 
			$name=Gettrimvalue($trimRule,$name);
			$name=stripslashes($name);
		}
		echo "<font color='red'>".$strname."</font>：".htmlspecialchars($name)."<br>";
	}else{
		return '';
	}
}

function getAreaValue($loopstr,$str,$html,$removecode='')
{
	$namea=getrulevalue($loopstr,$str."a");
	$nameb=getrulevalue($loopstr,$str."b");
	$trimRule=getrulevalue($loopstr,$str."_trim");
	if( trim($namea) !='' && trim($nameb) != '' ){
		$name=GetHtmlArea('[内容]',$namea.'[内容]'.$nameb,$html);
		if($removecode!=''){
			$name=removeHTMLCode($name,$removecode);
		}
		if($trimRule!=''){
			$name=Gettrimvalue($trimRule,$name);
		}
		return trim($name);
	}else{
		return '';
	}
}

function GetHtmlArea($sptag,$areaRule,$html)
{
	$areaRules = explode($sptag,$areaRule);
	if($html=='' || $areaRules[0]=='')
	{
		return '';
	}
	$posstart = @strpos($html,$areaRules[0]);
	if($posstart===false)
	{
		return '';
	}
	$posend = @strpos($html,$areaRules[1],$posstart);
	if($posend > $posstart && $posend!==false)
	{
		return substr($html,$posstart+strlen($areaRules[0]),$posend-$posstart-strlen($areaRules[0]));
	}
	else
	{
		return '';
	}
}

function Gettrimvalue($trimRule='',$trimvalue)
{
	if($trimRule=='' || strpos($trimRule,'{seacms:trim')=== false){
		return $trimvalue;
	}else{
		$labelRule = buildregx("{seacms:trim(.*?)}(.*?){/seacms:trim}","is");
		preg_match_all($labelRule,$trimRule,$arr);
		foreach($arr[2] as $k=>$nv){
			$replacestr=trim(preg_replace("/[ \r\n\t\f]{1,}/","",$arr[1][$k]));
			preg_match("/replace='(.*?)'/is",$replacestr,$matches);
			$replacestr=$matches[1];
			if($nv=='') continue;
			$nvs = preg_transform($nv);
			$trimvalue = preg_replace("/".$nvs."/isU",$replacestr,$trimvalue);
		}
	}
	return $trimvalue;
}

function preg_transform($str)
{
	$str = str_replace("/","\\/",$str);
	$str = str_replace("'","\'",$str);
	$str = str_replace('"','\"',$str);
	$str = str_replace('$','\$',$str);
	return $str;
}

//正则抓取网页中的内容
function GetHtmlarray($html,$sRule)
{
	$saR=array();
	$sRegx = buildregx($sRule,'isU');
	preg_match_all($sRegx,$html,$saR);
	return $saR[1];
}

//获取配置属性
function getrulevalue($content,$str)
{
	if(!empty($content) && !empty($str)){
		$labelRule = buildregx("{seacms:".$str."}(.*?){/seacms:".$str."}","is");
		preg_match_all($labelRule,$content,$ar);
		return $ar[1][0];
	}
}

//在html中显示 需要转义特殊字符 否则会出现排版错乱
function getrulevalueh($content,$str)
{
	if(!empty($content) && !empty($str)){
		$labelRule = buildregx("{seacms:".$str."}(.*?){/seacms:".$str."}","is");
		preg_match_all($labelRule,$content,$ar);
		return htmlspecialchars($ar[1][0]);
	}
}

//抓取网页中的内容
function Geturlarray($html,$matchRule,$trimRule='')
{
	if($matchRule=='' || trim($matchRule)=='[内容]'){
		return array();
	}else{
		$matchRule=str_replace("[","\[",$matchRule);
		$matchRule=str_replace("]","\]",$matchRule);
		$matchRule=str_replace("(","\(",$matchRule);
		$matchRule=str_replace(")","\)",$matchRule);
		$matchRule=str_replace("?","\?",$matchRule);
		$matchRule=str_replace('\[内容\]','(.*?)',$matchRule);
		$sRegx = buildregx($matchRule,'is');
		preg_match_all($sRegx,$html,$saR);
		$Getarrayvalue=$saR[1];
		$urlarray=array();
			if(isset($Getarrayvalue[0])){
				foreach($Getarrayvalue as $uv){
					$urlarray[]= Gettrimvalue($trimRule,$uv);
				}
			}
	}
	return $urlarray;
}

function Getplayurlarr($weburlarray,$match_purl,$trim_purl,$viewurl,$isref,$sourcelang)
{
	$parray=array();
	foreach($weburlarray as $wuv){
		$plarraytemp=array();
		foreach($wuv as $puv){
			$playurlstr=FillUrl($viewurl,$puv);
			$playurlhtml=cget($playurlstr,$isref);
			$playurlhtml=ChangeCode($playurlhtml,$sourcelang);
			$plarraytemp[]=GetHtmlvalue($playurlhtml,$match_purl,$trim_purl);
		}
		$parray[]=$plarraytemp;
	}
	return $parray;
}

function Getplayurlarr2($html,$match_purl,$match_ptitle)
{
	$sRegx = buildregx($match_purl,'is');
	preg_match_all($sRegx,$html,$saR);
	$Getarrayvalue=$saR[1];
	$urlarray=array();
	if(isset($Getarrayvalue[0])){
		foreach($Getarrayvalue as $k=>$uv){
			$urlarray[$k][url]=$uv;
		}
	}
	if($match_ptitle!='' && trim($match_ptitle)!='(.*)'){
		$sRegxt = buildregx($match_ptitle,'is');
		preg_match_all($sRegxt,$html,$saRt);
		$Getarrayvaluet=$saRt[1];
		if(isset($Getarrayvaluet[0])){
			foreach($Getarrayvaluet as $k=>$uvt){
				$urlarray[$k][url]=$uvt;
			}
		}
	}
	return $urlarray;
}

function Getserverarray($html,$matchRule='',$trimRule='')
{
	if($matchRule=='' || trim($matchRule)=='(.*)'){
		return array();
	}else{
		$sRegx = buildregx($matchRule,'is');
		preg_match_all($sRegx,$html,$saR);
		$Getarrayvalue=$saR[1];
		$serverarray=array();
		if(isset($Getarrayvalue[0])){
			foreach($Getarrayvalue as $uv){
				$serverarray[]=Gettrimvalue($trimRule,$uv);
			}
		}
	}
	return $serverarray;
}

//补全影片地址，预先未定义好播放来源，通过采集获取播放来源
function transferUrlarr($fromarray,$playarray,$part=array())
{
	$fai=count($fromarray);
	$pai=count($playarray);
	$parti=!empty($part)?count($part):0;
	if($fai<$pai){$n=$fai;}else{$n=$pai;}
	$n=!empty($part)&&$parti<$n?$parti:$n;
	$url='';
	for($m=0;$m<$n;$m++){
		$playurltempa=$playarray[$m];
		$fromstr=$fromarray[$m];
		$partstr=$part[$m];
		$k=0;
		$urlstr='';
		foreach($playurltempa as $j=>$plv){
			$k++;
			if(!empty($partstr[$j]))
			$urlstr=$urlstr.$partstr[$j].'$'.$plv.'$'.getReferedId($fromstr).'#';
			else
			$urlstr=$urlstr.'第'.$k.'集$'.$plv.'$'.getReferedId($fromstr).'#';
		}
		$urlstr=rtrim($urlstr,'#');
		$url=$url.$fromstr.'$$'.$urlstr.'$$$';
	}
		$url=rtrim($url,'$$$');
	return $url;
}

//补全影片地址，预先定义好播放来源
function transferUrlatr($fromstr,$playarray,$part=array())
{
	if(!isset($playarray[0])) return '';
	$pai=count($playarray);
	$parti=!empty($part)?count($part):0;
	if(!empty($part)&&$pai>$parti){$n=$parti;}else{$n=$pai;}
	$url='';
	for($m=0;$m<$n;$m++){
		$playurltempa=$playarray[$m];
		$k=0;
		$urlstr='';
		$partstr=$part[$m];
		foreach($playurltempa as $j=>$plv){
			$k++;
			if(!empty($partstr[$j]))
			$urlstr=$urlstr.$partstr[$j].'$'.$plv.'$'.getReferedId($fromstr).'#';
			else
			$urlstr=$urlstr.'第'.$k.'集$'.$plv.'$'.getReferedId($fromstr).'#';
		}
		$urlstr=rtrim($urlstr,'#');
		$url=$url.$fromstr.'$$'.$urlstr.'$$$';
	}
	$url=rtrim($url,'$$$');
	return $url;
}

function GenerateDownUrl($fromstr,$playarray,$part=array())
{
	if(!isset($playarray[0])) return '';
	$pai=count($playarray);
	$parti=!empty($part)?count($part):0;
	if(!empty($part)&&$pai>$parti){$n=$parti;}else{$n=$pai;}
	$url='';
	for($m=0;$m<$n;$m++){
		$playurltempa=$playarray[$m];
		$k=0;
		$urlstr='';
		$partstr=$part[$m];
		foreach($playurltempa as $j=>$plv){
			$k++;
			if(!empty($partstr[$j]))
			$urlstr=$urlstr.$partstr[$j].'$'.$plv.'$down#';
			else
			$urlstr=$urlstr.'第'.$k.'集$'.$plv.'$down#';
		}
		$urlstr=rtrim($urlstr,'#');
		$url=$url.$fromstr.'$$'.$urlstr.'$$$';
	}
	$url=rtrim($url,'$$$');
	return $url;
}

//生成下载地址
function GetHtmlvalue($html,$matchRule='',$trimRule='')
{
	if($matchRule=='' || trim($matchRule)=='[内容]'){
	return '';
	}else{
	$Getvalue=GetHtmlArea('[内容]',$matchRule,$html);
	$Getvalue=Gettrimvalue($trimRule,$Getvalue);
	}
	return $Getvalue;
}

//补全网址
function FillUrl($refurl,$surl)
{
	$refurl = trim($refurl);
	//判断文档相对于当前的路径
	$urls = @parse_url($refurl);
	$HomeUrl = $urls['host'];
	$BaseUrlPath = $HomeUrl.$urls['path'];
	$BaseUrlPath = preg_replace("/\/([^\/]*)\.(.*)$/","/",$BaseUrlPath);
	$BaseUrlPath = preg_replace("/\/$/",'',$BaseUrlPath);

	$i = $pathStep = 0;
	$dstr = $pstr = $okurl = '';

	$surl = trim($surl);
		if($surl == '')
		{
			return '';
		}
		$pos = strpos($surl,'#');
		if($pos>0)
		{
			$surl = substr($surl,0,$pos);
		}
		if($surl[0]=='/')
		{
			$okurl = $HomeUrl.'/'.$surl;
		}
		else if($surl[0]=='.')
		{
			if(!isset($surl[2]))
			{
				return '';
			}
			else if($surl[0]=='/')
			{
				$okurl = $BaseUrlPath."/".substr($surl,2,strlen($surl)-2);
			}
			else
			{
				$urls = explode('/',$surl);
				foreach($urls as $u)
				{
					if($u=='..')
					{
						$pathStep++;
					}
					else if($i<count($urls)-1)
					{
						$dstr .= $urls[$i].'/';
					}
					else
					{
						$dstr .= $urls[$i];
					}
					$i++;
				}
				$urls = explode('/',$BaseUrlPath);
				if(count($urls) <= $pathStep)
				{
					return '';
				}
				else
				{
					$pstr = '';
					for($i=0;$i<count($urls)-$pathStep;$i++){ $pstr .= $urls[$i].'/'; }
					$okurl = $pstr.$dstr;
				}
			}
		}
		else
		{
			if( strlen($surl) < 7 )
			{
				$okurl = $BaseUrlPath.'/'.$surl;
			}
			else if( strtolower(substr($surl,0,7))=='http://' )
			{
				$okurl = m_eregi_replace('^http://','',$surl);
			}
			else
			{
				$okurl = $BaseUrlPath.'/'.$surl;
			}
		}
		$okurl = m_eregi_replace('/{1,}','/',$okurl);
		return 'http://'.$okurl;
}

//编码转换
function ChangeCode($str,$sourcelang)
{
	global $cfg_soft_lang;
	require_once(sea_INC.'/charset.func.php');
	if($cfg_soft_lang=='utf-8')
	{
		if($sourcelang=="gb2312")
		{
			$str = gb2utf8($str);
		}
		if($sourcelang=="big5")
		{
			$str = gb2utf8(big52gb($str));
		}
	}
	else
	{
		if($sourcelang=="utf-8")
		{
				$str = utf82gb($str);
		}
		if($sourcelang=="big5")
		{
			$str = big52gb($str);
		}
	}
	return $str;
}

function parseAttr($attrStr){
	$attrArray=explode(' ', $attrStr);
	$strLen=count($attrArray);
	for($i=0; $i<$strLen; $i++){
	$singleAttr=explode(chr(61).'"',$attrArray[$i]);
	$singleAttrKey=$singleAttr[0];
	$singleAttrValue=rtrim($singleAttr[1],'"');
	$attrDictionary[$singleAttrKey]=$singleAttrValue;
	}
	return $attrDictionary;
}

function removeHTMLCode($Str,$cHas){
	if($cHas=="" || $Str===false) return $Str;
	$cHas=strtoupper($cHas);
	$cHasArr = explode('|',$cHas);
	for($i=0;$i<count($cHasArr);$i++){
		switch($cHasArr[$i]){
			case "TABLE":
				$Str=preg_replace("/<\/?(table|thead|tbody|tr|th|td)[^>]*>/is","",$Str);
			break;
			case "OBJECT":
				$Str=preg_replace("/<\/?(object|param|embed)[^>]*>/is","",$Str);
			break;
			case "SCRIPT":
				$Str=preg_replace("/<scr"."ipt.*>[\w\W]+?<\/scr"."ipt>/is","",$Str);
				$Str=preg_replace("/\son[\w]+=[\'\"].+?[\'\"](\s|\>)/is","$1",$Str);
			break;
			case "STYLE":
				$Str=preg_replace("/<style.*>[\w\W]+?<\/style>/is","",$Str);
				$Str=preg_replace("/\sstyle=.+(\s|>)/is","$1",$Str);
			break;
			case "CLASS":
				$Str=preg_replace("/\sclass=.+(\s|>)/is","$1",$Str);
			break;
			default:
				$Str=preg_replace("/<\/?".$cHasArr[$i]."[^>]*>/is","",$Str);
			break;
		}
	}
	return $Str;
}