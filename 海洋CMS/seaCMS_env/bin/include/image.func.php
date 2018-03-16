<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

//检测用户系统支持的图片格式
global $cfg_photo_type,$cfg_photo_typenames,$cfg_photo_support;
$cfg_photo_type['gif'] = false;
$cfg_photo_type['jpeg'] = false;
$cfg_photo_type['png'] = false;
$cfg_photo_type['wbmp'] = false;
$cfg_photo_typenames = Array();
$cfg_photo_support = '';
if(function_exists("imagecreatefromgif") && function_exists("imagegif"))
{
	$cfg_photo_type["gif"] = true;
	$cfg_photo_typenames[] = "image/gif";
	$cfg_photo_support .= "GIF ";
}
if(function_exists("imagecreatefromjpeg") && function_exists("imagejpeg"))
{
	$cfg_photo_type["jpeg"] = true;
	$cfg_photo_typenames[] = "image/pjpeg";
	$cfg_photo_typenames[] = "image/jpeg";
	$cfg_photo_support .= "JPEG ";
}
if(function_exists("imagecreatefrompng") && function_exists("imagepng"))
{
	$cfg_photo_type["png"] = true;
	$cfg_photo_typenames[] = "image/png";
	$cfg_photo_typenames[] = "image/xpng";
	$cfg_photo_support .= "PNG ";
}
if(function_exists("imagecreatefromwbmp") && function_exists("imagewbmp"))
{
	$cfg_photo_type["wbmp"] = true;
	$cfg_photo_typenames[] = "image/wbmp";
	$cfg_photo_support .= "WBMP ";
}

//缩图片自动生成函数，来源支持bmp、gif、jpg、png
//但生成的小图只用jpg或png格式
function ImageResize($srcFile,$toW,$toH,$toFile="")
{
	global $cfg_photo_type;
	if($toFile=="")
	{
		$toFile = $srcFile;
	}
	$info = "";
	$srcInfo = GetImageSize($srcFile,$info);
	switch ($srcInfo[2])
	{
		case 1:
			if(!$cfg_photo_type['gif'])
			{
				return false;
			}
			$im = imagecreatefromgif($srcFile);
			break;
		case 2:
			if(!$cfg_photo_type['jpeg'])
			{
				return false;
			}
			$im = imagecreatefromjpeg($srcFile);
			break;
		case 3:
			if(!$cfg_photo_type['png'])
			{
				return false;
			}
			$im = imagecreatefrompng($srcFile);
			break;
		case 6:
			if(!$cfg_photo_type['bmp'])
			{
				return false;
			}
			$im = imagecreatefromwbmp($srcFile);
			break;
	}
	$srcW=ImageSX($im);
	$srcH=ImageSY($im);
	if($srcW<=$toW && $srcH<=$toH )
	{
		return true;
	}
	$toWH=$toW/$toH;
	$srcWH=$srcW/$srcH;
	if($toWH<=$srcWH)
	{
		$ftoW=$toW;
		$ftoH=$ftoW*($srcH/$srcW);
	}
	else
	{
		$ftoH=$toH;
		$ftoW=$ftoH*($srcW/$srcH);
	}
	if($srcW>$toW||$srcH>$toH)
	{
		if(function_exists("imagecreatetruecolor"))
		{
			@$ni = imagecreatetruecolor($ftoW,$ftoH);
			if($ni)
			{
				imagecopyresampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
			}
			else
			{
				$ni=imagecreate($ftoW,$ftoH);
				imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
			}
		}
		else
		{
			$ni=imagecreate($ftoW,$ftoH);
			imagecopyresized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
		}
		switch ($srcInfo[2])
		{
			case 1:
				imagegif($ni,$toFile);
				break;
			case 2:
				imagejpeg($ni,$toFile,85);
				break;
			case 3:
				imagepng($ni,$toFile);
				break;
			case 6:
				imagebmp($ni,$toFile);
				break;
			default:
				return false;
		}
		imagedestroy($ni);
	}
	imagedestroy($im);
	return true;
}

//获得GD的版本
function gdversion()
{
	//没启用php.ini函数的情况下如果有GD默认视作2.0以上版本
	if(!function_exists('phpinfo'))
	{
		if(function_exists('imagecreate'))
		{
			return '2.0';
		}
		else
		{
			return 0;
		}
	}
	else
	{
		ob_start();
		phpinfo(8);
		$module_info = ob_get_contents();
		ob_end_clean();
		if(preg_match("/\bgd\s+version\b[^\d\n\r]+?([\d\.]+)/i", $module_info,$matches))
		{
			$gdversion_h = $matches[1];
		}
		else
		{
			$gdversion_h = 0;
		}
		return $gdversion_h;
	}
}

//图片自动加水印函数
function WaterImg($srcFile,$fromGo='up')
{
	include(sea_DATA.'/mark/inc_photowatermark_config.php');
	require_once(sea_INC.'/image.class.php');
	if($photo_markup!='1')
	{
		return;
	}
	$info = '';
	$srcInfo = @getimagesize($srcFile,$info);
	$srcFile_w    = $srcInfo[0];
	$srcFile_h    = $srcInfo[1];
		
	if($srcFile_w < $photo_wwidth || $srcFile_h < $photo_wheight)
	{
		return;
	}
	if($fromGo=='up' && $photo_markup=='0')
	{
		return;
	}
	if($fromGo=='down' && $photo_markdown=='0')
	{
		return;
	}
 	$trueMarkimg = sea_DATA.'/mark/'.$photo_markimg;
	if(!file_exists($trueMarkimg) || empty($photo_markimg))
	{
		$trueMarkimg = "";
	}
	if($photo_waterpos == 0)
	{
		$photo_waterpos = rand(1, 9);
	}
	$cfg_watermarktext = array();
	if($photo_marktype == '2')
	{
	if(file_exists(sea_DATA.'/mark/simhei.ttf'))
	{
		$cfg_watermarktext['fontpath'] =  sea_DATA.'/mark/simhei.ttf';
	}
	else
	{
		return ;
	}}
	$cfg_watermarktext['text'] = $photo_watertext;
	$cfg_watermarktext['size'] = $photo_fontsize;
	$cfg_watermarktext['angle'] = '0';
	$cfg_watermarktext['color'] = '0,0,0';
	$cfg_watermarktext['shadowx'] = '0';
	$cfg_watermarktext['shadowy'] = '0';
	$cfg_watermarktext['shadowcolor'] = '0,0,0';

	$img = new image($srcFile,0, $cfg_watermarktext, $photo_waterpos, $photo_diaphaneity, $photo_wheight, $photo_wwidth, $photo_marktype, $photo_marktrans,$trueMarkimg);
	$img->watermark(0);
}

?>