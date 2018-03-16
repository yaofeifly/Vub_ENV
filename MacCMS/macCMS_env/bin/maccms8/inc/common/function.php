<?php
function redirect($url,$obj='')
{
	echo '<script>'.$obj.'location.href="' .$url .'";</script>';
	exit;
}

function head()
{
	return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
}

function alert($str)
{
	echo '<script>alert("' .$str. '\t\t");history.go(-1);</script>';
}

function alertUrl($str,$url)
{
	echo '<script>alert("' .$str. '\t\t");location.href="' .$url .'";</script>';
}

function jump($url,$sec=0)
{
	echo '<script>setTimeout(function (){location.href="'.$url.'";},'.($sec*1000).');</script><span>暂停'.$sec.'秒后继续  >>>  </span><a href="'.$url.'" >如果您的浏览器没有自动跳转，请点击这里</a><br>';
}

function confirmMsg($msg,$url1,$url2)
{
	echo '<script>if(confirm("' .$msg. '")){location.href="' .$url1. '"}else{location.href="' .$url2. '"}</script>';
}

function showMsg($msg,$url,$t=1500)
{
	ob_end_clean();
    if($url==""){ $url='javascript:void(0)'; $urljs="history.go(-1);"; } else{ $urljs="location='".$url."';"; }
echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统提示</title>
<style>
.container{ padding:9px 20px 20px; text-align:left; }
.infobox{ clear:both; margin-bottom:10px; padding:30px; text-align:center; border-top:4px solid #DEEFFA; border-bottom:4px solid #DEEEFA; background:#F2F9FD; zoom:1; }
.infotitle1{ margin-bottom:10px; color:#09C; font-size:14px; font-weight:700;color:#ff0000; }
h3{ margin-bottom:10px; font-size:14px; color:#09C; }
</style>
</head>
<body>
<div class="container" id="cpcontainer"><h3>系统提示</h3><div class="infobox"><h4 class="infotitle1">$msg</h4><p class="marginbot"><a href="$url" class="lightlink">如果您的浏览器没有自动跳转，请点击这里</a></p></div>
</div>
<span style="display:none"><script>function jump(){ $urljs } setTimeout("jump()",$t);</script></span>
</body>
</html>
EOT;
exit;
}

function errMsg($e,$d)
{
echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统提示</title>
</head>
<body>
<table border="0" align="center" cellpadding="5" cellspacing="1" style="color:#333333;margin-top:100px;background:#D3E9F8">
<tr><th style="color:#FFFFFF;height:20px;">$e</th></tr>
<tr><td align="center" style="background:#FFF; padding:20px 10px; font-size:12px; line-height:30px">
<font style="color:#FF0000;font-size:14px;">$d</font>
</td></tr>
</table>
</body>
</html>
EOT;
exit;
}

function dispseObj()
{
	unset($GLOBALS['db']);
	unset($GLOBALS['tbl']);
	unset($GLOBALS['MAC']);
	unset($GLOBALS['MAC_CACHE']);
	unset($GLOBALS['MAC_PAR']);
	
	$GLOBALS['db']=null;
	$GLOBALS['tbl']=null;
	$GLOBALS['MAC']=null;
	$GLOBALS['MAC_CACHE']=null;
	$GLOBALS['MAC_PAR']=null;
}


function showErr($type,$msg)
{
	$phpmsg = debuginfo();
	ob_end_clean();
	$host = $_SERVER['HTTP_HOST'];
	echo <<<EOT
		
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>$host - $type 系统错误</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
	body { background-color: white; color: black; }
	#container { width: 650px; }
	#message   { width: 650px; color: black; background-color: #FFFFCC; }
	#bodytitle { font: 13pt/15pt verdana, arial, sans-serif; height: 35px; vertical-align: top; }
	.bodytext  { font: 10pt/11pt verdana, arial, sans-serif; }
	.help  { font: 12px verdana, arial, sans-serif; color: red;}
	.red  {color: red;}
	a:link     { font: 10pt/11pt verdana, arial, sans-serif; color: red; }
	a:visited  { font: 10pt/11pt verdana, arial, sans-serif; color: #4e4e4e; }
</style>
</head>
<body>
<table cellpadding="1" cellspacing="5" id="container">
<tr>
	<td id="bodytitle" width="100%">$type Error </td>
</tr><tr>
	<td class="bodytext">Your request has encountered a problem. </td>
</tr><tr><td><hr size="1"/></td></tr>
<tr><td class="bodytext">Error messages: </td></tr>
<tr>
	<td class="bodytext" id="message">
		<ul> $msg</ul>
	</td>
</tr><tr><td class="bodytext">&nbsp;</td></tr>
<tr><td class="bodytext">Program messages: </td></tr>
<tr>
	<td class="bodytext">
		<ul> $phpmsg </ul>
	</td>
</tr><tr>
	<td class="help"><br /><br /><a href="http://$host">$host</a> 系统出现错误, 由此给您带来的访问不便我们深感歉意</td>
</tr>
</table>
</body>
</html>
EOT;
exit;
}

function debuginfo()
{
	$arr = debug_backtrace();
	krsort($arr);
	foreach ($arr as $k => $error) {
		$file = str_replace(MAC_ROOT,'',$error['file']);
		
		$file = str_replace(str_replace('/','\\',MAC_ROOT),'',$file);
		
		$func = isset($error['class']) ? $error['class'] : '';
		
		$func .= isset($error['type']) ? $error['type'] : '';
		
		$func .= isset($error['function']) ? $error['function'] : '';
		
		$error[line] = sprintf('%04d', $error['line']);
		
		$show .= "<li>[Line: $error[line]]".$file."($func)</li>";
	}
	unset($arr);
	return $show;
}

function my_error_handler($errno, $errmsg, $filename, $linenum, $vars) 
{
	$the_time = date("Y-m-d H:i:s (T)");
	$errno = $errno & error_reporting();
    if($errno === 0) return;
	$filename=str_replace(getcwd(),"",$filename);
    $errorType = array(1=>"Error", 2=>"Warning", 4=>"Parsing Error",8=>"Notice", 16=>"Core Error", 32=>"Core Warning", 64=>"Complice Error", 128=>"Compile Warning", 256=>"User Error", 512=>"User Warning", 1024=>"User Notice", 2048=>"Strict Notice");
    
    $err .= '<li>[Type] ' . $errorType[$errno] .'</li>';
    $err .= '<li>[Msg]: ' . $errmsg .'</li>';
    showErr('System',$err);
}

function chkArray($arr1,$arr2)
{
	$res = true;
	if(is_array($arr1) && is_array($arr2)){
		if(count($arr1) != count($arr2)){
			$res = false;
		}
	}
	else{
		$res = false;
	}
	return $res;
}

function isN($str)
{
	if (is_null($str) || $str==''){ return true; }else{ return false;}
}

function isNum($str)
{
	if(!isN($str)){
		if(is_numeric($str)){return true;}else{ return false;}
  	}
}

function ifEcho($s1,$s2,$res='checked')
{
	if($s1!=$s2){
		$res="";
	}
	echo $res;
}

function ifPosEcho($s1,$s2,$res='checked')
{
	if(!strpos($s1,$s2)){
		$res="";
	}
	echo $res;
}

function isIp($ip)
{
	$e="([0-9]|1[0-9]{2}|[1-9][0-9]|2[0-4][0-9]|25[0-5])";  
	if(ereg("^$e\.$e\.$e\.$e$",$ip)){ return true; } else{ return false; }
}

function getRndNum($length)
{
	$pattern = "1234567890";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,10)};
	}
	return $res;
}

function rndNum($minnum,$maxuum)
{
	return rand($minnum,$maxuum);
}

function getRndStr($length)
{
	$pattern = "1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ";
	for($i=0; $i<$length; $i++){
		$res .= $pattern{mt_rand(0,36)};
	}
	return $res;
}

function be($mode,$key,$sp=',')
{
	ini_set("magic_quotes_runtime", 0);
	$magicq= get_magic_quotes_gpc();
	switch($mode)
	{
		case 'post':
			$res=isset($_POST[$key]) ? $magicq?$_POST[$key]:@addslashes($_POST[$key]) : '';
			break;
		case 'get':
			$res=isset($_GET[$key]) ? $magicq?$_GET[$key]:@addslashes($_GET[$key]) : '';
			break;
		case 'arr':
			$arr =isset($_POST[$key]) ? $_POST[$key] : '';
			if($arr==""){
				$value="0";
			}
			else{
				for($i=0;$i<count($arr);$i++){
					$res=implode($sp,$arr);
				} 
			}
			break;
		default:
			$res=isset($_REQUEST[$key]) ? $magicq ? $_REQUEST[$key] : @addslashes($_REQUEST[$key]) : '';
			break;
	}
	return $res;
}


function mkdirs($path)
{
	if (!is_dir(dirname($path))){
		mkdirs(dirname($path));
	}
	if(!file_exists($path)){
		mkdir($path);
	}
}

function getIP()
{
	if(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$cip = $_SERVER["HTTP_CLIENT_IP"];
	}
	else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else if(!empty($_SERVER["REMOTE_ADDR"])){
		$cip = $_SERVER["REMOTE_ADDR"];
	}
	else{
		$cip = '0.0.0.0';
	}
	preg_match("/[\d\.]{7,15}/", $cip, $cips);
	$cip = isset($cips[0]) ? $cips[0] : '0.0.0.0';
	unset($cips);
	return $cip;
}

function getReferer()
{
	return $_SERVER["HTTP_REFERER"];
}

function getUrl()
{
  if(!empty($_SERVER["REQUEST_URI"])){
		$nowurl = $_SERVER["REQUEST_URI"];
	}
	else{
		$nowurl = $_SERVER["PHP_SELF"];
	}
	return $nowurl;
}

function delCookie($key)
{
	setcookie($key,"",time()-3600,MAC_PATH);
}

function getCookie($key)
{
	if(!isset($_COOKIE[$key])){
		return '';
	}
	else{
		return $_COOKIE[$key];
	}
}

function sCookie($key,$val)
{
	setcookie($key,$val,0,MAC_PATH);
}

function execTime()
{
	$time = explode(" ", microtime());
	$usec = (double)$time[0];
	$sec = (double)$time[1];
	return $sec + $usec;
}

function getTimeSpan($sn)
{
	$lastTime = $_SESSION[$sn];
	if (isN($lastTime)){
		$lastTime= "1228348800";
	}
	$res = time() - intval($lastTime);
	return $res;
}

function getFormatSize($s)
{
	if($s==0){ return '0 kb'; }
	$unit=array('b','kb','mb','gb','tb','pb');
	return round($s/pow(1024,($i=floor(log($s,1024)))),2).' '.$unit[$i];
}

function getRunTime()
{
	$t2= execTime() - MAC_STARTTIME;
	$size=memory_get_usage();
    $memory=getFormatSize($size);
    unset($unit);
    return 'Processed in: '.round($t2,4).' second(s),&nbsp;' . $GLOBALS['db']->sql_qc . '  queries ' . $memory . ' Mem On.';
}

function repSpecialChar($str)
{
	$str = str_replace('/','_',$str);
	$str = str_replace('\\','_',$str);
	$str = str_replace('[','',$str);
	$str = str_replace(']','',$str);
	$str = str_replace('<','',$str);
	$str = str_replace('>','',$str);
	$str = str_replace('*','',$str);
	$str = str_replace(':','',$str);
	$str = str_replace('?','',$str);
	$str = str_replace('|','',$str);
	$str = str_replace('\'','',$str);
	$str = str_replace('"','',$str);
	$str = str_replace(' ','',$str);
	$str = trim($str);
	return $str;
}

function getTextt($num,$sname)
{
	if (isNum($num)){
		if (!isN($sname)){
			$res= substring($sname,$num);
		}
		else{
			$res="";
		}
	}
	else{
		$res=$sname;
	}
	return $res;
}

function getDatet($iformat,$itime)
{
	$iformat = str_replace("yyyy","Y",$iformat);
	$iformat = str_replace("yy","Y",$iformat);
	$iformat = str_replace("hh","H",$iformat);
	$iformat = str_replace("mm","m",$iformat);
	$iformat = str_replace("dd","d",$iformat);
	
	if(empty($iformat)){ $iformat = 'Y-m-d';}
	$res = date($iformat,$itime);
	return $res;
}

function buildregx($regstr,$regopt)
{
	return '/'.str_replace('/','\/',$regstr).'/'.$regopt;
}

function replaceStr($text,$search,$replace)
{
	if(isN($text)){ return "" ;}
	$res=str_replace($search,$replace,$text);
	return $res;
}

function regReplace($str,$rule,$value)
{
	$rule = buildregx($rule,"is");
	if (!isN($str)){
		$res = preg_replace($rule,$value,$str);
	}
	return $res;
}

function getSubStrByFromAndEnd($str,$startStr,$endStr,$operType)
{
	switch ($operType)
	{
		case "start":
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strlen($str)+1;
			break;
		case "end":
			$location1=1;
			$location2=strpos($str,$endStr,$location1);
			break;
		default:
			$location1=strpos($str,$startStr)+strlen($startStr);
			$location2=strpos($str,$endStr,$location1);
			break;
	}
	$location3 = $location2-$location1;
	$res= substring1($str,$location3,$location1);
	return $res;
}

function regMatch($str, $rule)
{
	$rule = buildregx($rule,"is");
	preg_match_all($rule,$str,$MatchesChild);
	$matchfieldarr=$MatchesChild[1];
	$matchfieldstrarr=$MatchesChild[0];
	$matchfieldvalue="";
	foreach($matchfieldarr as $f=>$matchfieldstr)
	{
		$matchfieldvalue=$matchfieldstrarr[$f];
		$matchfieldstr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$matchfieldstr));
		break;
	}
	unset($MatchesChild);
	return $matchfieldstr;
}

function XmlSafeStr($s)
{
	return preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/","",$s);
}

function utf2ucs($str)
{
	$n=strlen($str);
	if ($n=3) {
		$highCode = ord($str[0]);
		$midCode = ord($str[1]);
		$lowCode = ord($str[2]);
		$a   = 0x1F & $highCode;
		$b   = 0x7F & $midCode;
		$c   = 0x7F & $lowCode;
		$ucsCode = (64*$a + $b)*64 + $c;
	}
	elseif ($n==2) {
		$highCode = ord($str[0]);
		$lowCode = ord($str[1]);
		$a   = 0x3F & $highCode;
		$b   = 0x7F & $lowCode;
		$ucsCode = 64*$a + $b; 
	}
	elseif($n==1) {
		$ucscode = ord($str);
	}
	return dechex($ucsCode);
}

function escape($str)
{
	preg_match_all("/[\xC0-\xE0].|[\xE0-\xF0]..|[\x01-\x7f]+/",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
	$ord = ord($v[0]);
	    if( $ord<=0x7F)
	      $ar[$k] = rawurlencode($v);
	    elseif ($ord<0xE0) {
	      $ar[$k] = "%u".utf2ucs($v);
	    }
		elseif ($ord<0xF0) {
	      $ar[$k] = "%u".utf2ucs($v);
		}
	}
	return join("",$ar);
}

function unescape($str)
{
	$str = rawurldecode($str);
	preg_match_all("/(?:%u.{4})|&#x.{4};|&#\d+;|.+/U",$str,$r);
	$ar = $r[0];
	foreach($ar as $k=>$v) {
		if(substr($v,0,2) == "%u"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,-4)));
		}
		else if(substr($v,0,3) == "&#x"){
			$ar[$k] = iconv("UCS-2","GB2312",pack("H4",substr($v,3,-1)));
		}
		else if(substr($v,0,2) == "&#") {
			$ar[$k] = iconv("UCS-2","GB2312",pack("n",substr($v,2,-1)));
		}
	}
	unset($r);
	return join("",$ar);
}

function htmlEncode($str)
{
	if (!isN($str)){
		$str = str_replace(chr(38), "&#38;",$str);
		$str = str_replace(">", "&gt;",$str);
		$str = str_replace("<", "&lt;",$str);
		$str = str_replace(chr(39), "&#39;",$str);
		$str = str_replace(chr(32), "&nbsp;",$str);
		$str = str_replace(chr(34), "&quot;",$str);
		$str = str_replace(chr(9), "&nbsp;&nbsp;&nbsp;&nbsp;",$str);
		$str = str_replace(chr(13), "",$str);
		$str = str_replace(chr(10), "<br />",$str);
	}
	return $str;
}

function htmlDecode($str)
{
	if (!isN($str)){
		$str = str_replace("<br/>", chr(13)&chr(10),$str);
		$str = str_replace("<br>", chr(13)&chr(10),$str);
		$str = str_replace("<br />", chr(13)&chr(10),$str);
		$str = str_replace("&nbsp;&nbsp;&nbsp;&nbsp;", Chr(9),$str);
		$str = str_replace("&amp;", chr(38),$str);
		$str = str_replace("&#39;", chr(39),$str);
		$str = str_replace("&apos;", chr(39),$str);
		$str = str_replace("&nbsp;", chr(32),$str);
		$str = str_replace("&quot;", chr(34),$str);
		$str = str_replace("&gt;", ">",$str);
		$str = str_replace("&lt;", "<",$str);
		$str = str_replace("&#38;", chr(38),$str);
	}
	return $str;
}

function htmlFilter($str)
{
	$str = strip_tags($str);
	$str = str_replace("\"","",$str);
	$str = str_replace("'","",$str);
	return $str;
}

function htmltojs($content)
{
	$arrLines = explode(chr(10),$content);
	for ($i=0 ;$i<count($arrLines);$i++){
		$sLine = str_replace("\\" , "\\\\",$arrLines[$i] );
		$sLine = str_replace("/" , "\/" ,$sLine);
		$sLine = str_replace("'" , "\'",$sLine);
		$sLine = str_replace("\"\"" , "\"",$sLine);
		$sLine = str_replace(chr(13) , "" ,$sLine);
		$strNew = $strNew . "document.writeln('". $sLine  . "');" . chr(10);
	}
	unset($arrLines);
	return $strNew;
}

function jstohtml($str)
{
	if (!isN($str)){
		$str = str_replace("document.writeln('" , "",$str);
		$str = str_replace("\'" , "'",$str);
		$str = str_replace("\"" , "\"\"",$str);
		$str = str_replace("\\\\" , "\\",$str);
		$str = str_replace("\/" , "/",$str);
		$str = str_replace("');" , "",$str);
	}
    return $str;
}

function jsEncode($str)
{
	if (!isN($str)){
		$str = str_replace(chr(92),"\\",$str);
		$str = str_replace(chr(34),"\"",$str);
		$str = str_replace(chr(39),"\'",$str);
		$str = str_replace(chr(9),"\t",$str);
		$str = str_replace(chr(13),"\r",$str);
		$str = str_replace(chr(10),"\n",$str);
		$str = str_replace(chr(12),"\f",$str);
		$str = str_replace(chr(8),"\b",$str);
	}
	return $str;
}

function badFilter($str)
{
	$arr=explode(",",$GLOBALS['MAC']['other']['filter']);
	for ($i=0;$i<count($arr);$i++){
		$str= str_replace($arr[$i],"***",$str);
	}
	unset($arr);
	return $str;
}

function asp2phpif($str)
{
	$str= str_replace("not","!",$str);
	$str= str_replace("==","=",$str);
	$str= str_replace("=","==",$str);
	$str= str_replace("<>","!=",$str);
	$str= str_replace("and","&&",$str);
	$str= str_replace("or","||",$str);
	$str= str_replace("mod","%",$str);
	return $str;
}

function substring1($str,$len, $start) {
     $tmpstr = "";
     $len = $start + $len;
     for($i = $start; $i < $len; $i++){
         if(ord(substr($str, $i, 1)) > 0xa0) {
             $tmpstr .= substr($str, $i, 2);
             $i++;
         } else
             $tmpstr .= substr($str, $i, 1);
     }
     return $tmpstr;
} 

function substring($str, $lenth, $start=0) 
{ 
	$len = strlen($str); 
	$r = array(); 
	$n = 0;
	$m = 0;
	
	for($i=0;$i<$len;$i++){ 
		$x = substr($str, $i, 1); 
		$a = base_convert(ord($x), 10, 2); 
		$a = substr( '00000000 '.$a, -8);
		
		if ($n < $start){ 
            if (substr($a, 0, 1) == 0) { 
            }
            else if (substr($a, 0, 3) == 110) { 
              $i += 1; 
            }
            else if (substr($a, 0, 4) == 1110) { 
              $i += 2; 
            } 
            $n++; 
		}
		else{ 
            if (substr($a, 0, 1) == 0) { 
             	$r[] = substr($str, $i, 1); 
            }else if (substr($a, 0, 3) == 110) { 
             	$r[] = substr($str, $i, 2); 
            	$i += 1; 
            }else if (substr($a, 0, 4) == 1110) { 
            	$r[] = substr($str, $i, 3); 
             	$i += 2; 
            }else{ 
             	$r[] = ' '; 
            } 
            if (++$m >= $lenth){ 
              break; 
            } 
        }
	}
	return  join('',$r);
}


function Hanzi2PinYin($str){
	global $pinyins;
	$str = iconv("UTF-8","GBK",$str);
	$res = '';
	$str = trim($str);
	$slen = strlen($str);
	
	if($slen<2){
		return $str;
	}
	if(count($pinyins)==0){
		$fp = fopen(MAC_ROOT .'/inc/common/pinyin.dat','r');
		while(!feof($fp)){
			$line = trim(fgets($fp));
			$pinyins[$line[0].$line[1]] = substr($line,3,strlen($line)-3);
		}
		fclose($fp);
	}
	for($i=0;$i<$slen;$i++){
		if(ord($str[$i])>0x80){
			$c = $str[$i].$str[$i+1];
			$i++;
			if(isset($pinyins[$c])){
				$res .= $pinyins[$c];
			}else{
				//$res .= "_";
			}
		}else if( eregi("[a-z0-9]",$str[$i]) ){
			$res .= $str[$i];
		}
		else{
			//$res .= "_";
		}
	}
	return $res;
}

function getFolderItem($tmppath){
	$fso=@opendir($tmppath);
    $attr=array();
    $i=0;
	while (($file=@readdir($fso))!==false){
		if($file!=".." && $file!="."){
			array_unshift($attr,$file);
			$i=$i+1;
		}
	}
	closedir($fso);
	unset($fso);
	return $attr;
}

function convert_encoding($str,$nfate,$ofate){
	if ($ofate=="UTF-8"){ return $str; }
	if ($ofate=="GB2312"){ $ofate="GBK"; }
	
	if(function_exists("mb_convert_encoding")){
		$str=mb_convert_encoding($str,$nfate,$ofate);
	}
	else{
		$ofate.="//IGNORE";
		$str=iconv(  $nfate , $ofate ,$str);
	}
	return $str;
}

function getPage($url,$charset)
{
	$charset = strtoupper($charset);
	$content = "";
	if(!empty($url)) {
		if( function_exists('curl_init') ){
			$ch = @curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; )');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_COOKIE, 'domain=www.baidu.com');
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$content = @curl_exec($ch);
			curl_close($ch);
		}
		else if( ini_get('allow_url_fopen')==1 ){
			$content = @file_get_contents($url);
		}
		else{
			die('当前环境不支持采集【curl 或 allow_url_fopen】，请检查php.ini配置；');
		}
		$content = convert_encoding($content,"utf-8",$charset);
	}
	return $content;
}

function getBody($strBody,$strStart,$strEnd)
{
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
    $strStart=stripslashes($strStart);
   	$strEnd=stripslashes($strEnd);
	
	if(strpos($strBody,$strStart)!=""){
		$str = substr($strBody,strpos($strBody,$strStart)+strlen($strStart));
		$str = substr($str,0,strpos($str,$strEnd));
	}
	else{
		$str=false;
	}	
	return $str;
}

function getArray($strBody,$strStart,$strEnd)
{
	$strStart=stripslashes($strStart);
    $strEnd=stripslashes($strEnd);
	if(isN($strBody)){ return false; }
	if(isN($strStart)){ return false; }
	if(isN($strEnd)){ return false; }
	
	$strStart = str_replace("(","\(",$strStart);
	$strStart = str_replace(")","\)",$strStart);
	$strStart = str_replace("'","\'",$strStart);
	$strStart = str_replace("?","\?",$strStart);
	$strEnd = str_replace("(","\(",$strStart);
	$strEnd = str_replace(")","\)",$strStart);
	$strEnd = str_replace("'","\'",$strStart);
	$strEnd = str_replace("?","\?",$strStart);
	
	$labelRule = $strStart."(.*?)".$strEnd;
	$labelRule = buildregx($labelRule,"is");
	preg_match_all($labelRule,$strBody,$tmparr);
	$tmparrlen=count($tmparr[1]);
	$rc=false;
	for($i=0;$i<$tmparrlen;$i++)
	{
		if($rc){ $str .= "{Array}"; }
		$str .= $tmparr[1][$i];
		$rc=true;
	}
	
	if (isN($str)) { return false ;}
	$str=str_replace($strStart,"",$str);
	$str=str_replace($strEnd,"",$str);
	$str=str_replace("\"\"","",$str);
	$str=str_replace("'","",$str);
	$str=str_replace(" ","",$str);
	if (isN($str)) { return false ;}
	return $str;
}

function getColorText($txt,$color,$lens)
{
	if (isN($txt)) { return '';}
	if ($lens>0){ $txt = substring($txt,$lens); }
	if (!isN($color)){ $txt= '<font color='.$color.'>'. $txt . '</font>'; }
	return $txt;
}
function getColorDay($t)
{
	if (isN($t)) { return ''; }
	$t = date('Y-m-d H:i:s',$t);
	$now = date('Y-m-d',time());
	if (strpos(','.$t,$now)>0){ $c = 'color="#FF0000"'; }
	return  '<font ' .$c. '>' .$t. '</font>';
}

function compress_html($s){
    $s = str_replace(array("\r\n","\n","\t"), array('','','') , $s);
    $pattern = array (
                    "/> *([^ ]*) *</",
                    "/[\s]+/",
                    "/<!--[\\w\\W\r\\n]*?-->/",
                   // "/\" /",
                    "/ \"/",
                    "'/\*[^*]*\*/'"
                    );
    $replace = array (
                    ">\\1<",
                    " ",
                    "",
                    //"\"",
                    "\"",
                    ""
                    );
    return preg_replace($pattern, $replace, $s);
}

function array_sort($arr,$keys,$type='asc')
{
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}
	else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array;
} 

/* xml 相关操作*/
function getVodXmlText($f,$t,$v)
{
	$fromarr = explode("$$$",$v);
	$arr1 = $GLOBALS['MAC_CACHE']['vod'.$t];
	$rc=false;
	$res="";
	for($i=0;$i<count($fromarr);$i++){
		if($rc){ $res.=","; }
		
		$res.= $arr1[$fromarr[$i]]['show'];
		$rc=true;
	}
	unset($fromarr);
	return $res;
}

function getVodXml($name,$path)
{
	$arr = array();
	$cp= 'app';
	$cn= $name;
	$ct= 'arr';
	$xmlpath = MAC_ROOT ."/inc/config/" .$name;
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$doc -> load($xmlpath);
	$xmlnode = $doc -> documentElement;
	$nodes = $xmlnode->getElementsByTagName($path);
	foreach($nodes as $node){
		$status = $node->attributes->item(0)->nodeValue;
		$sort = $node->attributes->item(1)->nodeValue;
		$from = $node->attributes->item(2)->nodeValue;
		$show = $node->attributes->item(3)->nodeValue;
		$des = $node->attributes->item(4)->nodeValue;
		$tip = $node->getElementsByTagName("tip")->item(0)->nodeValue;
		
		if(!isNum($status)){$status=0;} else { $status=intval($status);}
		if(!isNum($sort)){$sort=0;} else { $sort=intval($sort);}
		
		if($status==1){
			$arr[$from] = array('sort'=>$sort,'show'=>$show,'des'=>$des,'tip'=>str_replace('\'','\\\'',$tip));
		}
	}
	unset($nodes);
	unset($xmlnode);
	unset($doc);
	$arr = array_sort($arr,'sort','desc');
	return $arr;
}

function getValueByArray($arr,$item,$val)
{
	foreach($arr as $row){
		if($row[$item] == $val){
			$res =  $row;
			break;
		}
	}
	return $res;
}

function loadFile($path)
{
	if(!file_exists($path)){
		echo '缺少文件：'.$path;
		exit;
	}
	return file_get_contents($path);
}

function checkField($fieldName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query('SHOW COLUMNS FROM '.$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row['Field'];
	}
	unset($rs);
	if(in_array($fieldName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function checkIndex($sIndexName,$tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query('SHOW INDEX FROM '.$tableName);
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row['Column_name'];
	}
	if(in_array($sIndexName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function checkTable($tableName)
{
	global $db;
	$dbarr = array();
	$rs = $db->query('SHOW TABLES ');
	while ($row = $db ->fetch_array($rs)){
		$dbarr[] = $row['Tables_in_'.$GLOBALS['MAC']['db']['name']];
	}
	unset($rs);
	if(in_array($tableName,$dbarr)){
		return true;
	}
	else {
		return false;
	}
}

function chkCache($cp,$cn)
{
	if($GLOBALS['MAC']['app']['cache'] ==0){ return false; }
	if($GLOBALS['MAC']['app']['cachetype']==0){
		$cf = MAC_ROOT.'/cache/'.$cp.'/'.$GLOBALS['MAC']['app']['cacheid'].$cn.'.inc';
		$mintime = time() - $GLOBALS['MAC']['app']['cachetime']*60;
		if (file_exists($cf) && ($mintime < filemtime($cf))){ return true; } else{ return false; }
	}
	elseif($GLOBALS['MAC']['app']['cachetype']==1){
		$status = false;
		$mem=new Memcache;
		if($mem->connect($GLOBALS['MAC']['app']['memcachedhost'],$GLOBALS['MAC']['app']['memcachedport'])){
			$v = $mem->get($cp.'_'.$cn);
			if(!empty($v)){
				$status=true;
			}
			$mem->close();
	    };
		return $status;
	}
}

function setCache($cp,$cn,$cv,$ct='txt')
{
	if($GLOBALS['MAC']['app']['cache'] ==0){ return false; }
	if($GLOBALS['MAC']['app']['cachetype']==0){
		$cf = MAC_ROOT.'/cache/'.$cp.'/'.$GLOBALS['MAC']['app']['cacheid'].$cn.'.inc';
		$path = dirname($cf);
		mkdirs($path);
		if($ct=='arr'){
			$cv = "<?php\nreturn ".var_export($cv, true).";\n?>";
		}
		@fwrite(fopen($cf,'wb'),$cv);
	}
	elseif($GLOBALS['MAC']['app']['cachetype']==1){
		$mem=new Memcache;
		if($mem->connect($GLOBALS['MAC']['app']['memcachedhost'],$GLOBALS['MAC']['app']['memcachedport'])){
			$mem->set($cp.'_'.$cn,$cv,0,$GLOBALS['MAC']['app']['cachetime']*60);
			//$mem->replace($cn,$cv,MEMCACHE_COMPRESSED,$GLOBALS['MAC']['app']['cachetime']*60);
			$mem->close();
	    };
	}
}

function getCache($cp,$cn,$ct='txt')
{
	if($GLOBALS['MAC']['app']['cachetype']==0){
		$cf = MAC_ROOT.'/cache/'.$cp.'/'.$GLOBALS['MAC']['app']['cacheid'].$cn.'.inc';
		if($ct=='arr'){
			$res = @include $cf;
		}
		else{
			$res = @file_get_contents($cf);
		}
	}
	elseif($GLOBALS['MAC']['app']['cachetype']==1){
		$mem=new Memcache;
		if($mem->connect($GLOBALS['MAC']['app']['memcachedhost'],$GLOBALS['MAC']['app']['memcachedport'])){
			$res = $mem->get($cp.'_'.$cn);
			$mem->close();
	    };
	}
	return $res;
}

function echoPageCache($cp,$cn)
{
	if($GLOBALS['MAC']['app']['dynamiccache'] ==0){ return false; }
	$cf = MAC_ROOT.'/cache/'.$cp.'/'.$cn.'.html';
	$mintime = time() - $GLOBALS['MAC']['app']['cachetime']*60;
	if (file_exists($cf) && ($mintime < filemtime($cf))){
		$html = loadFile($cf);
		$html = str_replace("{maccms:runtime}",getRunTime(),$html);
		echo $html;
		exit;
	}
}
function setPageCache($cp,$cn,$cv)
{
	if($GLOBALS['MAC']['app']['dynamiccache'] ==0){ return false; }
	$cf = MAC_ROOT.'/cache/'.$cp.'/'.$cn.'.html';
	$path = dirname($cf);
	mkdirs($path);
	@fwrite(fopen($cf,'wb'),$cv);
}

function repPseRnd($tab,$txt,$id)
{
	$id = $id % 7;
	if (isN($txt)){ $txt=""; }
	$psecontent = loadFile(MAC_ROOT. "/inc/config/pse_".$tab."rnd.txt");
	if (isN($psecontent)){ $psecontent = ""; }
	$psecontent = str_replace(chr(10),"",$psecontent);
	$psearr = explode(chr(13),$psecontent);
	$i=count($psearr)+1;
	$j=strpos($txt,"<br>");
	
	if ($j==0){ $j=strpos($txt,"<br/>");}
	if ($j==0){ $j=strpos($txt,"<br />");}
	if ($j==0){ $j=strpos($txt,"</p>");}
	if ($j==0){ $j=strpos($txt,"。")+1;}
	
	if ($j>0){
		$res= substring($txt,$j-1) . $psearr[$id % $i] . substring($txt,strlen($txt)-$j,$j);
	}
	else{
		$res= $psearr[$id % 1]. $txt;
	}
	unset($psearr);
	return $res;
}

function repPseSyn($tab,$txt)
{
	$id = $id % 7;
	if (isN($txt)){ $txt=""; }
	$psecontent = loadFile(MAC_ROOT. "/inc/config/pse_".$tab."syn.txt");
	if (isN($psecontent)){ $psecontent = ""; }
	$psecontent = str_replace(chr(10),"",$psecontent);
	$psearr = explode(chr(13),$psecontent);
	
	foreach($psearr as $a){
		if(!empty($a)){
			$one = explode('=',$a);
			$txt = str_replace($one[0],$one[1],$txt);
			unset($one);
		}
	}
	unset($psearr);
	return $txt;
}


function getKeysLink($key,$ktype)
{
	if (!isN($key)){
		$key = str_replace(array(",","|","/"),array(" "," "," "),$key);
		
		$arr = explode(" ",$key);
		for ($i=0;$i<count($arr);$i++){
			if (!isN($arr[$i])){
				$str = $str . "<a target='_blank' href='".$GLOBALS['MAC']['site']['installdir']."index.php?m=vod-search-".$ktype."-". urlencode($arr[$i])."'>".$arr[$i]."</a>&nbsp;";
			}
		}
	}
	return $str;
}

function getUserFlag($f)
{
	switch ($f)
	{
		case 1:
			$res="包时";
			break;
		case 2:
			$res="IP段";
			break;
		default:
			$res="计点";
			break;
	}
	return $res;
}

function getTypeByPopedomFilter($flag)
{
	if ($GLOBALS['MAC']['user']['status']==0){
		return true;
	}
	
	$a="0,";
	$b="0,";
	$rc=false;
	$userid=intval($_SESSION["userid"]);
	$usergroup=intval($_SESSION["usergroup"]);
	
	$groupcache = $GLOBALS['MAC_CACHE']['usergroup'];
	$curgroup = $groupcache[$usergroup];
	if(!is_array($curgroup)) { $curgroup = array("ug_popvalue"=>0); }
	foreach($groupcache as $arr){
		if (  strpos(",".$arr["ug_popedom"], ",1,") >0  && $arr["ug_popvalue"] > $curgroup["ug_popvalue"] ){
			$a .= $arr["ug_type"];
		}
		if ($arr["ug_popvalue"] <= $curgroup["ug_popvalue"]){
			$b .= $arr["ug_id"]. ",";
		}
	}
	unset($curgroup);
	unset($groupcache);
	$a = str_replace(",,",",",$a);
	$b = str_replace(",,",",",$b);
	
	if (substring($a,1,strlen($a)-1) == ","){ $a = substring($a,strlen($a)-1); }
	if (substring($b,1,strlen($b)-1) == ","){ $b = substring($b,strlen($b)-1); }
	
	if ($flag=="menu"){
		$res = " and t_id not in(". $a .") ";
	}
	else{
		$res = " and d_type not in(". $a .") and d_usergroup in(". $b .") ";
	}
	return $res;
}

function getUserPopedom($id,$flag)
{
	if ($GLOBALS['MAC']['user']['status']==0){
		return true;
	}
	
	$userid=intval($_SESSION["userid"]);
	$usergroup=intval($_SESSION["usergroup"]);
	
	$res=false;
	$ug_popvalue1=0;
	$num=0;
	if ($flag== "list"){
		$flag = "1";
	}
	elseif ($flag== "vod"){
		$flag = "2";
	}
	elseif ($flag=="play"){
		$flag = "3";
	}
	elseif ($flag=="down"){
		$flag = "4";
	}
	$groupcache = $GLOBALS['MAC_CACHE']['usergroup'];
	foreach($groupcache as $arr){
		$ug_id=$arr["ug_id"];
		if ($ug_id==$usergroup){   $ug_popvalue1=$arr["ug_popvalue"]; }
		if (strpos(",".$arr["ug_type"],",".$id.",")>0 && strpos(",".$arr["ug_popedom"],",".$flag.",")>0){
			 $num++;
			 if ($ug_popvalue1 >= $arr["ug_popvalue"]){ $res=true; break;}
		}
	}
	unset($groupcache);
	
	if($num==0){ $res=true; }
	return $res;
}

/* 生成分页 */
function pageshow($page,$pagecount,$halfPer=6,$url,$pagego='',$pl='')
{
	$firstlink = str_replace('{pg}',1,$url);
	if(strpos($url,'?m=')){
		$firstlink = str_replace('-pg-{pg}','',$url);
	}
	elseif($GLOBALS['tpl']->P['static']){
		$firstlink = str_replace('-{pg}','',$url);
	}
	$prelink = $page==2 ? $firstlink : str_replace('{pg}',($page-1),$url);
    $linkPage .= ( $page > 1 )
        ? '<a target="_self" href="'.$firstlink.'" class="pagelink_a">首页</a>&nbsp;<a target="_self" href="'.$prelink.'" class="pagelink_a">上一页</a>&nbsp;' 
        : '<em>首页</em>&nbsp;<em>上一页</em>&nbsp;';
    
	$loopnum1=intval($halfPer/2)+1;
	$loopnum2=intval($halfPer/2);
	if ($halfPer%2>0){ $loopnum1++; }
	$i = $page - $loopnum1+1;
	$j = $page + $loopnum2;
	if ($i<1){
		$i=1; 
		$j=$halfPer;
	} 
	if ($j> $pagecount){
		$i = $i+($pagecount-$j);
		$j = $pagecount;
		if ($i<1){
			$i=1;
		} 
	}
	for ($p=$i;$p<=$j;$p++){
		if ($p > $pagecount){ break; }
		$lnk = str_replace('{pg}',$p,$url);
		if($p==1){
			$lnk = $firstlink;
		}
		$linkPage .= ($p==$page)?'<span class="pagenow">'.$p.'</span>&nbsp;':'<a target="_self" class="pagelink_b" href="'.$lnk.'">'.$p.'</a>&nbsp;'; 
	}
    
    $linkPage .= ( $page < $pagecount )
        ? '<a target="_self" href="'.str_replace('{pg}',($page+1),$url).'" class="pagelink_a">下一页</a>&nbsp;<a target="_self" href="'.str_replace('{pg}',$pagecount,$url).'" class="pagelink_a">尾页</a>'
        : '<em>下一页</em>&nbsp;<em>尾页</em>';
	if(!empty($pagego)){
		$linkPage .='&nbsp;<input type="input" name="page" id="page" size="4" class="pagego"/><input type="button" value="跳 转" onclick="'.$pagego.'" class="pagebtn" />';
	}
    return $linkPage;
}

/* 生成下拉框操作 */
function makeSelect($tabName,$colId,$colName,$colSort,$valUrl,$valspan,$valId)
{
	global $MAC_CACHE;
	if (!isN($colSort)){ $strOrder=" order by ".$colSort." asc";} 
	if (isN($valId)){ $valId=0; }
	
	switch($tabName)
	{
		case "{pre}user_group": $arr = $MAC_CACHE['usergroup']; break;
		case "{pre}art_topic": $arr = $MAC_CACHE['arttopic']; break;
		case "{pre}vod_topic": $arr = $MAC_CACHE['vodtopic']; break;
		default : $rc=false;
	}
	$res="";
	foreach($arr as $arr1){
		if (intval($valId)==$arr1[$colId]){ $strSelected=" selected"; } else{ $strSelected=""; } 
		if (isN($valUrl)){ 
			$strValue=$arr1[$colId]; 
		} 
		else{ 
			$strValue=$valUrl;
			if(strpos($valUrl,"?")>0){ $strValue.="&"; } else{ $strValue.="?"; }
			$strValue.= $tabName."=".$arr1[$colId];
		}
		$res=$res."<option value='".$strValue."' ".$strSelected.">&nbsp;|—".$arr1[$colName]."</option>";
	}
	unset($arr1);
	unset($arr);
	return $res;
}

function makeSelectAll($tabName,$colId,$colName,$colPid,$colSort,$valPid,$valUrl,$valspan,$valId)
{
	global $MAC_CACHE;
	if (isN($valId)){ $valId=0; }
	switch($tabName)
	{
		case "{pre}vod_type": $arr = $MAC_CACHE['vodtype']; break;
		case "{pre}art_type": $arr = $MAC_CACHE['arttype']; break;
	}
	$res="";
	foreach($arr as $arr1){
		if($arr1[$colPid]==0){
			if (intval($valId)==$arr1[$colId]){ $strSelected=" selected"; } else{ $strSelected=""; } 
			if (isN($valUrl)){ $strValue=$arr1[$colId]; } else{ $strValue=$valUrl."?".$tabName."=".$arr1[$colId]; } 
			$res = $res."<option value='".$strValue."' ".$strSelected.">&nbsp;|—".$arr1[$colName]."</option>";
			foreach($arr as $arr2){
				if($arr2[$colPid]==$arr1[$colId]){
					if (intval($valId)==$arr2[$colId]){ $strSelected=" selected"; } else{ $strSelected=""; } 
					if (isN($valUrl)){ $strValue=$arr2[$colId]; } else{ $strValue=$valUrl."?".$tabName."=".$arr2[$colId]; } 
					$res=$res."<option value='".$strValue."' ".$strSelected.">&nbsp;|&nbsp;&nbsp;&nbsp;|—".$arr2[$colName]."</option>";
				}
			}
		}
	}
	unset($arr2);
	unset($arr1);
	unset($arr);
	return $res;
}

function makeSelectXml($f,$t,$v)
{
	$arr1 = $GLOBALS['MAC_CACHE'][$f];
	$rc=false;
	foreach($arr1 as $k=>$v1){
		if ($v == $k) { $sed=" selected"; } else{ $sed=""; }
		$str .= "<option value='" .$k. "' " .$sed. ">" .$v1['show']. "</option>";
	}
	return $str;
}

/* 更新数据缓存 */
function updateCacheFile()
{
	global $db;
	$arr=array();
	//全局数据缓存
	$cachevodclass=array();
	//视频分类缓存
	try{
		$cachevodtype= $db->queryarray('SELECT * FROM {pre}vod_type','t_id');
		$i=0;
		foreach($cachevodtype as $v){
			$strchild='';
			$rc=false;
			$rs= $db->query('SELECT t_id FROM {pre}vod_type WHERE t_pid=' .$v['t_id']);
			while ($row = $db ->fetch_array($rs)){
				if($rc){ $strchild .=','; }
				$strchild .= $row['t_id'];
				$rc=true;
			}
			unset($rs);
			if (isN($strchild)){ $strchild = $v['t_id'];} else{ $strchild = $v['t_id'] . ',' . $strchild; }
			$cachevodtype[$v['t_id']]['childids'] = $strchild;
			
			//$lnk = $tpl->getLink('vod','type',$v,$row,false,1,1,false);
			//$cachevodtype[$v['t_id']]['link'] = $lnk;
			//$plnk = '';
			//if($v['t_pid']>0){
			//	$plnk = $tpl->getLink('vod','type',$cachevodtype[$v['t_pid']],$row,false,1,1,false);
			//}
			//$cachevodtype[$v['t_id']]['plink'] = $plnk;
			$i++;
		}
	}
	catch(Exception $e){ 
		echo '更新视频分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['vodtype'] = $cachevodtype;
	
	
	
	$arr['vodclass'] = $cachevodclass;
	//文章分类缓存
	try{
		$cachearttype=$db->queryarray('SELECT *,\'\' AS childids FROM {pre}art_type','t_id');
		$i=0;
		foreach($cachearttype as $v){
			$strchild='';
			$rc=false;
			$rs= $db->query('SELECT t_id FROM {pre}art_type WHERE t_pid=' .$v['t_id']);
			while ($row = $db ->fetch_array($rs)){
				if($rc){ $strchild .=','; }
				$strchild .= $row['t_id'];
				$rc=true;
			}
			unset($rs);
			if (isN($strchild)){ $strchild = $v['t_id'];} else{$strchild = $v['t_id'] . ',' . $strchild;}
			$cachearttype[$v['t_id']]['childids'] = $strchild;
			//$lnk = $tpl->getLink('art','type',$v,$row,false,1,1,false);
			//$cachearttype[$v['t_id']]['link'] = $lnk;
			//$plnk = '';
			//if($v['t_pid']>0){
			//	$plnk = $tpl->getLink('art','type',$cachearttype[$v['t_pid']],$row,false,1,1,false);
			//}
			//$cachearttype[$v['t_id']]['plink'] = $plnk;
			$i++;
		}
	}
	catch(Exception $e){ 
		echo '更新文章分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['arttype'] = $cachearttype;
	
	//视频剧情分类缓存
	try{
		$cachevodclass=$db->queryarray('SELECT * FROM {pre}vod_class','c_id');
	}
	catch(Exception $e){ 
		echo '更新视频剧情分类缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['vodclass'] = $cachevodclass;
	
	
	//视频专题缓存
	try{
		$cachevodtopic=$db->queryarray('SELECT * FROM {pre}vod_topic','t_id');
	}
	catch(Exception $e){ 
		echo '更新视频专题缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['vodtopic'] = $cachevodtopic;
	
	//文章专题缓存
	try{
		$cachearttopic=$db->queryarray('SELECT * FROM {pre}art_topic','t_id');
	}
	catch(Exception $e){ 
		echo '更新文章专题缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['arttopic'] = $cachearttopic;
	
	//用户组缓存
	try{
		$cacheusergroup=$db->queryarray('SELECT * FROM {pre}user_group','ug_id');
	}
	catch(Exception $e){ 
		echo '更新用户组缓存失败，请检查数据是否合法，是否包含引号、单引号、百分号、尖括号等特殊字符';
		exit;
	}
	$arr['usergroup'] = $cacheusergroup;
	
	
	$arr['vodplay'] = getVodXml('vodplay.xml','play');
	$arr['voddown'] = getVodXml('voddown.xml','down');
	$arr['vodserver'] = getVodXml('vodserver.xml','server');
	
	$cacheValue = '<?php'.chr(10).'$MAC_CACHE = '.compress_html(var_export($arr, true)).';'.chr(10).'?>';
	fwrite(fopen(MAC_ROOT.'/inc/config/cache.php','wb'),$cacheValue);
	
	
	
	foreach($arr['vodplay'] as $k=>$v){
		$plays.= 'mac_show["'.$k.'"]="'.$v['show'].'";';
	}
	foreach($arr['vodserver'] as $k=>$v){
		$plays.= 'mac_show_server["'.$k.'"]="'.$v['des'].'";';
	}
	
	$fp = MAC_ROOT.'/js/playerconfig.js';
	if(!file_exists($fp)){ $fp .= '.bak'; }
	$fc = file_get_contents( $fp );
	$jsb = getBody($fc,'//缓存开始','//缓存结束');
	$fc = str_replace($jsb,"\r\n".$plays."\r\n",$fc);
	@fwrite(fopen(MAC_ROOT.'/js/playerconfig.js','wb'),$fc);
	
	echo '';
}


/* 后台操作所需相关函数*/
function getFrom($f)
{
	switch($f)
	{
		case "百度影音" : $f="baidu";break;
		case "bdhd": $f="baidu";break;
		case "皮皮影音": $f="pipi";break;
		case "闪播Pvod": $f="pvod";break;
		case "迅播高清": $f="gvod";break;
		case "yuku":
		case "优酷":
			$f="youku";break;
		case "qq播客": $f="qq";break;
		default : break;
	}
	return $f;
}

function getVUrl($u)
{
	$arr1 = explode("#",$u);
	$rc=false;
	for ($i=0;$i<count($arr1);$i++){
		if (!isN($arr1[$i])){
			if (strpos( $arr1[$i],"$$") > 0){
				$arr3 = explode("$$",$arr1[$i]);
				$arr2= explode("$",$arr3[1]);
			}
			else{
				$arr2= explode("$",$arr1[$i]);
			}
			if ($rc){ $str = $str . "#";}
			if (count($arr2)==3 || count($arr2)==2){
				$str .= $arr2[0] . "$" . $arr2[1];
			}
			else{
				$str .= $arr2[0];
			}
			$rc = true;
			unset($arr2);
			unset($arr3);
		}
	}
	unset($arr1);
	return $str;
}

function getTag($title,$content){
	$data = getPage('http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title='.rawurlencode($title).'&content='.rawurlencode(substring($content,500)),'utf-8');
	
	if($data) {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $data, $values, $index);
		xml_parser_free($parser);
		$kws = array();
		foreach($values as $valuearray) {
			if($valuearray['tag'] == 'kw') {
				if(strlen($valuearray['value']) > 3){
					$kws[] = trim($valuearray['value']);
				}
			}elseif($valuearray['tag'] == 'ekw'){
				$kws[] = trim($valuearray['value']);
			}
		}
		return implode(',',$kws);
	}
	return false;
}

function format_vodname($vodname){
	$vodname = str_replace(array('【','】','（','）','(',')','{','}'),array('[',']','[',']','[',']','[',']'),$vodname);
	$vodname = preg_replace('/\[([a-z][A-Z])\]|([a-z][A-Z])版/i','',$vodname);
	$vodname = preg_replace('/TS清晰版|枪版|抢先版|HD|BD|TV|DVD|VCD|TS|\/版|\[\]/i','',$vodname);
	return trim($vodname);
}

function savepic($url,$path,$thumbpath,$fname,$flag,&$msg)
{
	$res=false;
	$pathlink = '../'.$path;
	$thumblink = '../'.$thumbpath;
	if(!is_dir($pathlink)){ mkdirs($pathlink); }
	if(!is_dir($thumblink)){ mkdirs($thumblink); }
	$st = strrpos($url,"/");
	$tmpfpath = substring($url,$st,0);
	$tmpfname = rawurlencode( substring($url, strlen($url)-$st,$st+1) );
	$url = $tmpfpath . "/" . $tmpfname;
	
	$errsize = 3;
	$byte= getPage($url,"utf-8");
	$bytelen = strlen($byte);
	$size = round($bytelen/1024,2);
	fwrite(fopen($pathlink.$fname,"wb"),$byte);
	
	$img_mime=array('image/gif','image/jpeg','image/png','image/bmp');
	$re=getimagesize($pathlink.$fname);
	
	
	if( !in_array($re['mime'],$img_mime) ){
		$msg= "（<font color=red>该文件可能是无效图片，跳过保存</font>）<a target=_blank href=".$url.">查看</a>";
		$status=false;
	}
	else{
		
		if($flag=='vod'){
			if($GLOBALS['MAC']['upload']['watermark']==1){
				imageWaterMark($pathlink.$fname,MAC_ROOT.'/inc/common/',$GLOBALS['MAC']['upload']['waterlocation'],$GLOBALS['MAC']['upload']['waterfont']);
			}
			if($GLOBALS['MAC']['upload']['thumb']==1){
				$thumbst = img2thumb($pathlink.$fname,$thumblink.$fname,$GLOBALS['MAC']['upload']['thumbw'],$GLOBALS['MAC']['upload']['thumbh']);
			}
			
			if ($GLOBALS['MAC']['upload']['ftp']==1){
				uploadftp($path,$fname);
				if($thumbst){ uploadftp($thumbpath,$fname); }
			}
		}
		$msg= "PicView：<a href='". $pathlink.$fname ."' target='_blank'>". $pathlink.$fname ."</a> <font color=red>".$size."</font>Kb";
		$status=true;
	}
	unset($img_mime,$re);
	return $status;
}

function imageWaterMark($groundImage,$cpath,$waterPos=0,$waterText="") 
{ 
      $isWaterImage = FALSE; 
      $textFont = 5 ;
      $textColor = '#FF0000';
      $formatMsg = '暂不支持该文件格式，请用图片处理软件将图片转换为Gif、JPG、PNG格式。'; 
      if(!empty($groundImage) && file_exists($groundImage)) {
          $ground_info = @getimagesize($groundImage);
          $ground_w = $ground_info[0];
          $ground_h = $ground_info[1];
          switch($ground_info[2]) {
              case 1:$ground_im = @imagecreatefromgif($groundImage);break; 
              case 2:$ground_im = @imagecreatefromjpeg($groundImage);break; 
              case 3:$ground_im = @imagecreatefrompng($groundImage);break; 
              default: echo $formatMsg;return;
          } 
      } else { 
          echo '需要加水印的图片不存在！'; return;
      } 
		$temp = @imagettfbbox(ceil($textFont*2.5),0,$cpath."arial.ttf",$waterText);
		$w = $temp[2] - $temp[6]; 
		$h = $temp[3] - $temp[7];
		unset($temp); 
		$label = '文字区域'; 
		
      if( ($ground_w<$w) || ($ground_h<$h) ) {
		echo '需要加水印的图片的长度或宽度比水印'.$label.'还小，无法生成水印！'; 
		return; 
      } 
      switch($waterPos) {
          case 0:
              $posX = ($ground_w - $w) / 2; 
              $posY = ($ground_h - $h) / 2; 
              break; 
          case 1:
              $posX = $ground_w - $w; 
              $posY = 0; 
              break; 
          case 2:
              $posX = $ground_w - $w; 
              $posY = $ground_h - $h - 10; 
              break; 
          case 3:
              $posX = 0; 
              $posY = 0; 
              break; 
          case 4:
              $posX = 0; 
              $posY = $ground_h - $h; 
              break; 
          default:
              $posX = rand(0,($ground_w - $w)); 
              $posY = rand(0,($ground_h - $h)); 
              break;
      }
      @imagealphablending($ground_im, true); 
      if( !empty($textColor) && (strlen($textColor)==7) ) {
		$R = hexdec(substr($textColor,1,2));
        $G = hexdec(substr($textColor,3,2));
        $B = hexdec(substr($textColor,5)); 
      }
      else{ 
        echo '水印文字颜色格式不正确！'; return;
      } 
      @imagestring ( $ground_im, $textFont, $posX, $posY, $waterText, @imagecolorallocate($ground_im, $R, $G, $B));
      @unlink($groundImage); 
      switch($ground_info[2]) {
          case 1: @imagegif($ground_im,$groundImage);break; 
          case 2: @imagejpeg($ground_im,$groundImage);break; 
          case 3: @imagepng($ground_im,$groundImage);break; 
          default: echo $errorMsg;return;
      } 
      if(isset($water_info)) unset($water_info); 
      if(isset($water_im)) @imagedestroy($water_im); 
      unset($ground_info); 
      @imagedestroy($ground_im); 
} 

function fileext($file)
{
    return pathinfo($file, PATHINFO_EXTENSION);
}
/**
 * 生成缩略图
 * @param string     源图绝对完整地址{带文件名及后缀名}
 * @param string     目标图绝对完整地址{带文件名及后缀名}
 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
 * @param int        是否裁切{宽,高必须非0}
 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
{
    if(!is_file($src_img)){ return false; }
    $ot = fileext($dst_img);
    switch($ot)
    {
    	case 'jpg':
    	case 'bmp':
    		$oa='jpeg';
    		break;
    	default:
    		$oa=$ot;
    		break;
    }
    $otfunc = 'imagecreatefrom' .$oa;
    if(!function_exists($otfunc)){
    	return false;
    }
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
    
    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;
    
    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
    {
        $proportion = 1;
    }
    if($width> $src_w)
    {
        $dst_w = $width = $src_w;
    }
    if($height> $src_h)
    {
        $dst_h = $height = $src_h;
    }
    if(!$width && !$height && !$proportion)
    {
        return false;
    }
    if(!$proportion)
    {
        if($cut == 0)
        {
            if($dst_w && $dst_h)
            {
                if($dst_w/$src_w> $dst_h/$src_h)
                {
                    $dst_w = $src_w * ($dst_h / $src_h);
                    $x = 0 - ($dst_w - $width) / 2;
                }
                else
                {
                    $dst_h = $src_h * ($dst_w / $src_w);
                    $y = 0 - ($dst_h - $height) / 2;
                }
            }
            else if($dst_w xor $dst_h)
            {
                if($dst_w && !$dst_h)  //有宽无高
                {
                    $propor = $dst_w / $src_w;
                    $height = $dst_h  = $src_h * $propor;
                }
                else if(!$dst_w && $dst_h)  //有高无宽
                {
                    $propor = $dst_h / $src_h;
                    $width  = $dst_w = $src_w * $propor;
                }
            }
        }
        else
        {
            if(!$dst_h)  //裁剪时无高
            {
                $height = $dst_h = $dst_w;
            }
            if(!$dst_w)  //裁剪时无宽
            {
                $width = $dst_w = $dst_h;
            }
            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
            $dst_w = (int)round($src_w * $propor);
            $dst_h = (int)round($src_h * $propor);
            $x = ($width - $dst_w) / 2;
            $y = ($height - $dst_h) / 2;
        }
    }
    else
    {
        $proportion = min($proportion, 1);
        $height = $dst_h = $src_h * $proportion;
        $width  = $dst_w = $src_w * $proportion;
    }
    $src = $createfun($src_img);
    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    if(function_exists('imagecopyresampled'))
    {
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    else
    {
        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    $otfunc($dst, $dst_img);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}

function uploadftp($picpath,$picfile)
{
	$ftp = new AppFtp($GLOBALS['MAC']['upload']['ftphost'],$GLOBALS['MAC']['upload']['ftpuser'],$GLOBALS['MAC']['upload']['ftppass'],$GLOBALS['MAC']['upload']['ftpport'],$GLOBALS['MAC']['upload']['ftpdir']);
	if( $ftp->ftpStatus == 1){;
		$localfile = MAC_ROOT .'/'. $picpath.$picfile;
		$remotefile= $MAC['upload']['ftpdir'].$picpath.$picfile;
		$ftp->mkdirs( $MAC['upload']['ftpdir'].$picpath );
		$ftpput = $ftp->put($localfile, $remotefile);
		if(!$ftpput){
			echo '上传图片到FTP远程服务器失败!';
		}
		else{
			$ftp->bye();
			if($GLOBALS['MAC']['upload']['ftpdel']==1){ unlink( MAC_ROOT .'/' .$picpath . $picfile ); }
		}
	}
	else{
		echo $ftp->ftpStatusDes;exit;
	}
	unset($ftp);
}

function getSavePicPath($flag)
{
	$ym = date('Y').'-'.date('m');
	$ymd = $ym.'-'.date('d');
	
	if($GLOBALS['MAC']['upload']['picpath'] == 1){
		$res = $ym;
	}
	elseif ($GLOBALS['MAC']['upload']['picpath'] == 2){
		for ($i=0; $i<100;$i++)
		{
			$path = $ymd . '-'.$i;
			$path1 = MAC_ROOT. '/upload/'.$flag.'/'.$path . '/';
			if(file_exists($path1)){
				$farr = glob($path1.'*.*');
				if($farr){
					$fcount = count($farr);
					if($fcount>500){
						$res = $ymd . '-'. ($i+1);
					}
				}
				else{
					$res = $path;
					break;
				}
				unset($farr);
			}
			else{
				$res = $path;
				break;
			}
		}
	}
	else{
		$res  = $ymd;
	}
	return $res ;
}
?>