<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

$magic_quotes_gpc = ini_get('magic_quotes_gpc');
function _FilterAll($fk, &$svar)
{
    global $cfg_notallowstr,$cfg_replacestr;
    if( is_array($svar) )
    {
        foreach($svar as $_k => $_v)
        {
            $svar[$_k] = _FilterAll($fk,$_v);
        }
    }
    else
    {
        if($cfg_notallowstr!='' && preg_match("#".$cfg_notallowstr."#i", $svar))
        {
            ShowMsg(" $fk has not allow words!",'-1');
            exit();
        }
        if($cfg_replacestr!='')
        {
            $svar = preg_replace('/'.$cfg_replacestr.'/i', "***", $svar);
        }
    }
    if (!$magic_quotes_gpc) {
        $svar = addslashes($svar);
    }
    return $svar;
}
 
/* 对_GET,_POST,_COOKIE进行过滤 */
foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
    foreach($$_request as $_k => $_v)
    {
        ${$_k} = _FilterAll($_k,$_v);
    }
}

function _Replace_Badword(&$var)
{
	global $cfg_notallowstr;
	
	$myarr = explode('|',$cfg_notallowstr);
	
	for($i=0;$i<count($myarr);$i++)
	{
		$var = str_replace($myarr[$i],"*",$var);	
	}
	return $var;
}
/*foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
	foreach($$_request as $_k => $_v)
	{
		${$_k} = _FilterAll($_k,$_v);
	}
}
*/?>