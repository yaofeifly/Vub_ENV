<?php
session_start();

//获取随机字符
$rndstring = '';
for($i=0; $i<4; $i++) $rndstring .= chr(mt_rand(65,90));

//如果支持GD，则绘图
if(function_exists("imagecreate"))
{
	//Firefox部份情况会多次请求的问题，5秒内刷新页面将不改变session
	//$ntime = time();
	$_SESSION['sea_ckstr'] = strtolower($rndstring);
	$_SESSION['sea_ckstr_last'] = '';
	$rndcodelen = strlen($rndstring);

	//创建图片，并设置背景色
	$im = imagecreate(50,20);
	ImageColorAllocate($im, 255,255,255);

	//背景线
	$lineColor1 = ImageColorAllocate($im,240,220,180);
	$lineColor2 = ImageColorAllocate($im,255,255,255);
	for($j=3;$j<=16;$j=$j+3)
	{
		imageline($im,2,$j,48,$j,$lineColor1);
	}
	for($j=2;$j<52;$j=$j+(mt_rand(3,6)))
	{
		imageline($im,$j,2,$j-6,18,$lineColor2);
	}

	//画边框
	$bordercolor = ImageColorAllocate($im, 0x99,0x99,0x99);
	imagerectangle($im, 0, 0, 46, 18, $bordercolor);

	//输出文字
	$fontColor = ImageColorAllocate($im, 48,61,50);
	for($i=0;$i<$rndcodelen;$i++)
	{
		$bc = mt_rand(0,1);
		$rndstring[$i] = strtoupper($rndstring[$i]);
		imagestring($im, 5, $i*10+6, mt_rand(2,4), $rndstring[$i], $fontColor);
	}

	header("Pragma:no-cache\r\n");
	header("Cache-Control:no-cache\r\n");
	header("Expires:0\r\n");

	//输出特定类型的图片格式，优先级为 gif -> jpg ->png
	if(function_exists("imagejpeg"))
	{
		header("content-type:image/jpeg\r\n");
		imagejpeg($im);
	}
	else
	{
		header("content-type:image/png\r\n");
		imagepng($im);
	}
	ImageDestroy($im);
	exit();
}
else
{
	//不支持GD，只输出字母 ABCD
	$_SESSION['sea_ckstr'] = "abcd";
	$_SESSION['sea_ckstr_last'] = '';
	header("content-type:image/jpeg\r\n");
	header("Pragma:no-cache\r\n");
	header("Cache-Control:no-cache\r\n");
	header("Expires:0\r\n");
	$fp = fopen("data/vdcode.jpg","r");
	echo fread($fp,filesize("data/vdcode.jpg"));
	fclose($fp);
	exit();
}

?>