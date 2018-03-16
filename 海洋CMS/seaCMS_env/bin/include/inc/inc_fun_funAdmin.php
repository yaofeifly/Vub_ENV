<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

function SpCreateDir($spath)
{
	global $cfg_dir_purview,$cfg_basedir,$isSafeMode;
	if($spath=='')
	{
		return true;
	}
	$flink = false;
	$truepath = $cfg_basedir;
	$truepath = str_replace("\\","/",$truepath);
	$spaths = explode("/",$spath);
	$spath = "";
	foreach($spaths as $spath)
	{
		if($spath=="")
		{
			continue;
		}
		$spath = trim($spath);
		$truepath .= "/".$spath;
		if(!is_dir($truepath) || !is_writeable($truepath))
		{
			if(!is_dir($truepath))
			{
				$isok = MkdirAll($truepath,$cfg_dir_purview);
			}
			else
			{
				$isok = ChmodAll($truepath,$cfg_dir_purview);
			}
			if(!$isok)
			{
				echo "创建或修改目录：".$truepath." 失败！<br>";
				return false;
			}
		}
	}
	return true;
}


function SpGetNewInfo()
{
	global $cfg_version;
	$nurl = $_SERVER['HTTP_HOST'];
	if( m_eregi("[a-z\-]{1,}\.[a-z]{2,}",$nurl) ) {
		$nurl = urlencode($nurl);
	}
	else {
		$nurl = "test";
	}
	$offUrl = "http://www.ek"."eke.net/newinfo.php?version={$cfg_version}&formurl={$nurl}";
	return $offUrl;
}

function SpGetEditor($fname,$fvalue)
{
	
	/************
	include(sea_ADMIN.'/editor/ckeditor.php') ;
	$oCKeditor = new CKEditor();
	$oCKeditor->config['width'] = '700';
	$oCKeditor->config['height'] = '400';
	$oCKeditor->basePath=dirname($_SERVER['PHP_SELF']).'/editor/';
	$oCKeditor->editor($fname,$fvalue,$config);
	********/
}