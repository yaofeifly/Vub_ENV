<?php
//for >=maxzcms2.0 only
function maxzcms($str)
{
$str=unescape($str);
$maxzarray=explode("','",$str);
$maxzstr=str_replace($maxzarray[1],"$$",$maxzarray[0]);
//$maxzstr=unescape($maxzstr);
return $maxzstr;
}
?>