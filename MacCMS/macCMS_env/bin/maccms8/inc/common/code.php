<?php
	header("Content-type:image/jpeg");
	session_start();
	$authnum_session = ''; 
	$str = '1234567890'; 
	$l = strlen($str);
	for($i=1;$i<=4;$i++){ 
		$num=rand(0,$l-1); 
		$authnum_session.= $str[$num]; 
	}
	$sname = "code_" . $_REQUEST["a"];
	$_SESSION[$sname] = $authnum_session;
	//session_register("authnum_session");
	srand((double)microtime()*1000000);
	$im = imagecreate(50,20);
	$black = ImageColorAllocate($im, 0,0,0);
	$white = ImageColorAllocate($im, 255,255,255);
	$gray = ImageColorAllocate($im, 200,200,200); 
	imagefill($im,68,30,$gray);
	$li = ImageColorAllocate($im, 220,220,220);
	for($i=0;$i<3;$i++){
		imageline($im,rand(0,30),rand(0,21),rand(20,40),rand(0,21),$li);
	} 
	imagestring($im, 5, 8, 2, $authnum_session, $white);
	for($i=0;$i<90;$i++){
		imagesetpixel($im, rand()%70 , rand()%30 , $gray);
	}
	Imagejpeg($im);
	ImageDestroy($im);
?>