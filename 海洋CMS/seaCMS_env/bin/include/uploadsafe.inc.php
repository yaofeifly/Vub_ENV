<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

if(isset($_FILES['GLOBALS']))
{
	exit('Request not allow!');
}

//为了防止用户通过注入的可能性改动了数据库
//这里强制限定的某些文件类型禁止上传
$cfg_not_allowall = "php|pl|cgi|asp|asa|cer|aspx|jsp|php3|shtm|shtml";
$keyarr = array('name','type','tmp_name','size');

foreach($_FILES as $_key=>$_value)
{
	foreach($keyarr as $k)
	{
		if(!isset($_FILES[$_key][$k]))
		{
			exit('Request Error!');
		}
	}
	$$_key = $_FILES[$_key]['tmp_name'] = str_replace("\\\\","\\",$_FILES[$_key]['tmp_name']);
	${$_key.'_name'} = $_FILES[$_key]['name'];
	${$_key.'_type'} = $_FILES[$_key]['type'] = m_eregi_replace('[^0-9a-z\./]','',$_FILES[$_key]['type']);
	${$_key.'_size'} = $_FILES[$_key]['size'] = m_ereg_replace('[^0-9]','',$_FILES[$_key]['size']);
	if(!empty(${$_key.'_name'}) && (m_eregi("\.(".$cfg_not_allowall.")$",${$_key.'_name'}) || !m_ereg("\.",${$_key.'_name'})) )
	{
			exit('Upload filetype not allow !');
	}
	if(empty(${$_key.'_size'}))
	{
		${$_key.'_size'} = @filesize($$_key);
	}
}
?>