<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_INC."/image.func.php");
if($cfg_photo_support=='')
{
	echo "你的系统没安装GD库，不允许使用本功能！";
	exit();
}
$ImageWaterConfigFile = sea_DATA."/mark/inc_photowatermark_config.php";
if(empty($dopost))
{
	$dopost = "";
}
if($dopost=="save")
{

	$vars = array('photo_markup','photo_markdown','photo_marktype','photo_wwidth','photo_wheight','photo_waterpos','photo_watertext','photo_fontsize','photo_fontcolor','photo_marktrans','photo_diaphaneity');
	$configstr = "";
	foreach($vars as $v)
	{
		${$v} = str_replace("'","",${$v});
		$configstr .= "\${$v} = '".${$v}."';\r\n";
	}
	$shortname = "";
	if(is_uploaded_file($newimg))
	{
		$allowimgtype= explode('|',$cfg_imgtype);
		$finfo=pathinfo($newimg_name);
		$imgfile_type = $finfo['extension'];
		if(!in_array($imgfile_type,$allowimgtype))
		{
			ShowMsg("上传的图片格式错误，请使用 {$cfg_photo_support}格式的其中一种！","-1");
			exit();
		}
		if($imgfile_type=='image/xpng')
		{
			$shortname = ".png";
		}
		else if($imgfile_type=='image/gif')
		{
			$shortname = ".gif";
		}
		else if($imgfile_type=='image/jpeg')
		{
			$shortname = ".jpg";
		}
		else 
		{
			$shortname = ".gif";
		}
		$photo_markimg = 'mark'.$shortname;
		@move_uploaded_file($newimg,sea_DATA."/mark/".$photo_markimg);
	}
	$configstr .= "\$photo_markimg = '{$photo_markimg}';\r\n";
	$configstr = "<"."?php\r\n".$configstr."?".">\r\n";
	$fp = fopen($ImageWaterConfigFile,"w") or die("写入文件 $ImageWaterConfigFile 失败，请检查权限！");
	fwrite($fp,$configstr);
	fclose($fp);
	ShowMsg("成功更改图片水印设置！","admin_config_mark.php");
	exit();
}
require_once($ImageWaterConfigFile);
include(sea_ADMIN.'/templets/admin_config_mark.htm');
exit();
?>