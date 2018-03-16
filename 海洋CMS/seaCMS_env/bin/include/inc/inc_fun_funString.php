<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

function SpHtml2Text($str)
{
	$str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU","",$str);
	$str = preg_replace("|</{0,1}[^>]*?>|", '', $str);
	$str = preg_replace("/[ ]+/s"," ",$str);
	return $str;
}

?>