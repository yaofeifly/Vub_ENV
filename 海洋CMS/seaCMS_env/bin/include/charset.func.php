<?php
if(!defined('sea_INC'))
{
	exit('seacms');
}

$UC2GBTABLE = $CODETABLE = $BIG5_DATA = $GB_DATA = '';
$GbkUniDic = null;

//UTF-8 转GB编码
function utf82gb($utfstr)
{
	if(function_exists('iconv'))
	{
		return iconv('utf-8','gbk//ignore',$utfstr);
	}
	global $UC2GBTABLE;
	$okstr = "";
	if(trim($utfstr)=="")
	{
		return $utfstr;
	}
	if(empty($UC2GBTABLE))
	{
		$filename = sea_INC."/data/gb2312-utf8.dat";
		$fp = fopen($filename,"r");
		while($l = fgets($fp,15))
		{
			$UC2GBTABLE[hexdec(substr($l, 7, 6))] = hexdec(substr($l, 0, 6));
		}
		fclose($fp);
	}
	$okstr = "";
	$ulen = strlen($utfstr);
	for($i=0;$i<$ulen;$i++)
	{
		$c = $utfstr[$i];
		$cb = decbin(ord($utfstr[$i]));
		if(strlen($cb)==8)
		{
			$csize = strpos(decbin(ord($cb)),"0");
			for($j=0;$j < $csize;$j++)
			{
				$i++; $c .= $utfstr[$i];
			}
			$c = utf82u($c);
			if(isset($UC2GBTABLE[$c]))
			{
				$c = dechex($UC2GBTABLE[$c]+0x8080);
				$okstr .= chr(hexdec($c[0].$c[1])).chr(hexdec($c[2].$c[3]));
			}
			else
			{
				$okstr .= "&#".$c.";";
			}
		}
		else
		{
			$okstr .= $c;
		}
	}
	$okstr = trim($okstr);
	return $okstr;
}


// 判断字符串是否utf-8  2012年1月23日， 巴特尔添加
function is_utf8($word) 
{ 
	if(preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word))
	{
		return true;
	}
	else
	{
	return false;
	}
} 

//GB转UTF-8编码
function gb2utf8($gbstr)
{
	if(is_utf8($gbstr))return $gbstr;
	if(function_exists('iconv'))
	{
		return iconv('gbk','utf-8//ignore',$gbstr);
	}
	global $CODETABLE;
	if(trim($gbstr)=="")
	{
		return $gbstr;
	}
	if(empty($CODETABLE))
	{
		$filename = sea_INC."/data/gb2312-utf8.dat";
		$fp = fopen($filename,"r");
		while ($l = fgets($fp,15))
		{
			$CODETABLE[hexdec(substr($l, 0, 6))] = substr($l, 7, 6);
		}
		fclose($fp);
	}
	$ret = "";
	$utf8 = "";
	while ($gbstr != '')
	{
		if (ord(substr($gbstr, 0, 1)) > 0x80)
		{
			$thisW = substr($gbstr, 0, 2);
			$gbstr = substr($gbstr, 2, strlen($gbstr));
			$utf8 = "";
			@$utf8 = u2utf8(hexdec($CODETABLE[hexdec(bin2hex($thisW)) - 0x8080]));
			if($utf8!="")
			{
				for ($i = 0;$i < strlen($utf8);$i += 3)
				$ret .= chr(substr($utf8, $i, 3));
			}
		}
		else
		{
			$ret .= substr($gbstr, 0, 1);
			$gbstr = substr($gbstr, 1, strlen($gbstr));
		}
	}
	return $ret;
}

//Unicode转utf8
function u2utf8($c)
{
	
	for ($i = 0;$i < count($c);$i++)
	{
		$str = "";
	}
	if ($c < 0x80)
	{
		$str .= $c;
	}
	else if ($c < 0x800)
	{
		$str .= (0xC0 | $c >> 6);
		$str .= (0x80 | $c & 0x3F);
	}
	else if ($c < 0x10000)
	{
		$str .= (0xE0 | $c >> 12);
		$str .= (0x80 | $c >> 6 & 0x3F);
		$str .= (0x80 | $c & 0x3F);
	}
	else if ($c < 0x200000)
	{
		$str .= (0xF0 | $c >> 18);
		$str .= (0x80 | $c >> 12 & 0x3F);
		$str .= (0x80 | $c >> 6 & 0x3F);
		$str .= (0x80 | $c & 0x3F);
	}
	return $str;
}

//utf8转Unicode
function utf82u($c)
{
	switch(strlen($c))
	{
		case 1:
			return ord($c);
		case 2:
			$n = (ord($c[0]) & 0x3f) << 6;
			$n += ord($c[1]) & 0x3f;
			return $n;
		case 3:
			$n = (ord($c[0]) & 0x1f) << 12;
			$n += (ord($c[1]) & 0x3f) << 6;
			$n += ord($c[2]) & 0x3f;
			return $n;
		case 4:
			$n = (ord($c[0]) & 0x0f) << 18;
			$n += (ord($c[1]) & 0x3f) << 12;
			$n += (ord($c[2]) & 0x3f) << 6;
			$n += ord($c[3]) & 0x3f;
			return $n;
	}
}

//Big5码转换成GB码
function big52gb($Text)
{
	if(function_exists('iconv'))
	{
		return iconv('big5','gbk//ignore',$Text);
	}
	global $BIG5_DATA;
	if(empty($BIG5_DATA))
	{
		$filename = sea_INC."/data/big5-gb.dat";
		$fp = fopen($filename, "rb");
		$BIG5_DATA = fread($fp,filesize($filename));
		fclose($fp);
	}
	$max = strlen($Text)-1;
	for($i=0;$i<$max;$i++)
	{
		$h = ord($Text[$i]);
		if($h>=0x80)
		{
			$l = ord($Text[$i+1]);
			if($h==161 && $l==64)
			{
				$gbstr = "　";
			}
			else
			{
				$p = ($h-160)*510+($l-1)*2;
				$gbstr = $BIG5_DATA[$p].$BIG5_DATA[$p+1];
			}
			$Text[$i] = $gbstr[0];
			$Text[$i+1] = $gbstr[1];
			$i++;
		}
	}
	return $Text;
}

//GB码转换成Big5码
function gb2big5($Text)
{
	if(function_exists('iconv'))
	{
		return iconv('gbk','big5//ignore',$Text);
	}
	global $GB_DATA;
	if(empty($GB_DATA))
	{
		$filename = sea_INC."/data/gb-big5.dat";
		$fp = fopen($filename, "rb");
		$gb = fread($fp,filesize($filename));
		fclose($fp);
	}
	$max = strlen($Text)-1;
	for($i=0;$i<$max;$i++)
	{
		$h = ord($Text[$i]);
		if($h>=0x80)
		{
			$l = ord($Text[$i+1]);
			if($h==161 && $l==64)
			{
				$big = "　";
			}
			else
			{
				$p = ($h-160)*510+($l-1)*2;
				$big = $GB_DATA[$p].$GB_DATA[$p+1];
			}
			$Text[$i] = $big[0];
			$Text[$i+1] = $big[1];
			$i++;
		}
	}
	return $Text;
}

//unicode url编码转gbk编码函数
function UnicodeUrl2Gbk($str)
{
	//载入对照词典
	if(!isset($GLOBALS['GbkUniDic']))
	{
		$fp = fopen(sea_INC.'/data/gbk-unicode.dat','rb');
		while(!feof($fp))
		{
			$GLOBALS['GbkUniDic'][bin2hex(fread($fp,2))] = fread($fp,2);
		}
		fclose($fp);
	}

	//处理字符串
	$str = str_replace('$#$','+',$str);
	$glen = strlen($str);
	$okstr = "";
	for($i=0; $i < $glen; $i++)
	{
		if($glen-$i > 4)
		{
			if($str[$i]=='%' && $str[$i+1]=='u')
			{
				$uni = strtolower(substr($str,$i+2,4));
				$i = $i+5;
				if(isset($GLOBALS['GbkUniDic'][$uni]))
				{
					$okstr .= $GLOBALS['GbkUniDic'][$uni];
				}
				else
				{
					$okstr .= "&#".hexdec('0x'.$uni).";";
				}
			}
			else
			{
				$okstr .= $str[$i];
			}
		}
		else
		{
			$okstr .= $str[$i];
		}
	}
	return $okstr;
}

?>