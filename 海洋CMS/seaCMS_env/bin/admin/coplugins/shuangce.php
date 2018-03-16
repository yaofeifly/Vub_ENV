<?php
function shuangce($str)
{
$str=base64_decode(unescape($str));
return $str;
}
?>