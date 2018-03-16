<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
require_once(sea_DATA."/config.ftp.php");
$image = new Image();
class Image
{
	function gatherPicHandle($pic)
	{
		global $cfg_gatherpicset;
		if(!$cfg_gatherpicset) 
		{
			return $pic;
		}elseif(strpos($pic,'uploads/')===0)
		{
			return $pic;
		}else
		{
			return $this->_picHandle($pic);
		}
	}
	
	function downPicHandle($pic,$v_name)
	{
		return $this->_picHandle($pic,$v_name);
	}
	
	function _picHandle($pic,$v_name='')
	{
		global $cfg_upload_dir;
		$isDownOk=false;
		$picUrl=$pic;
	if($picUrl==""){
$ps="";}else{
$ps = explode('/',$picUrl);}  
		$filename=$ps[count($ps)-1];
		if (strpos(" ".$filename,"?")>0){
			$filenamet=explode('?',$filename);
			$filename=$filenamet[0];
		}
		$fileext  = getFileFormat($filename);
		$filename = substr(md5($filename.time()),0,16);
		if (strpos($picUrl,".ykimg.com/")>0){
			$fileext=".gif";
		}
		$picpath = '../'.$cfg_upload_dir.'/allimg/'.MyDate("ymd",time())."/";
		$picfile = $filename.$fileext;
		$filePath = $picpath.$picfile;
		if(file_exists($filePath)){
			$isDownOk=true;
		}else{
			$isDownOk=$this->_downPic($picUrl,$filePath,$v_name);
		}
		if(is_numeric($isDownOk)){
			$this->watermark($filePath);
			if($GLOBALS['app_ftp']==1)
			{ 
				if($ftpreturn=$this->uploadftp( $picpath ,$picfile ,$v_name ,$picUrl)===TRUE)
				{
					if($GLOBALS['app_updatepic']==1)
					{
						$filePath=str_replace('../','',$filePath);
						$filePath = $GLOBALS['app_ftpurl'].$GLOBALS['app_ftpdir'].$filePath;
					}
					if(!empty($v_name))
					echo "数据<font color=red>".$v_name."</font>的图片下载成功并自动转移FTP服务器,大小为<font color=red>".$isDownOk."</font>KB <a target=_blank href=".$filePath.">预览图片</a><br>";	
				}else 
				{
					if(!empty($v_name)) echo $ftpreturn;
				}
			}else
			{
				if(!empty($v_name))
				echo "数据<font color=red>".$v_name."</font>的图片下载成功,大小为<font color=red>".$isDownOk."</font>KB <a target=_blank href=".$filePath.">预览图片</a><br>";
				$filePath=str_replace('../','',$filePath);
			}
			return $filePath;
		}else{
			if(!empty($v_name)) echo $isDownOk;
			return $pic.'#err';
		}
	}
	
	function _downPic($picUrl,$filePath,$vname='')
	{
		$spanstr = "<br/>";
		if(empty($picUrl) || substr($picUrl,0,4)!='http'){
			return "数据<font color=red>".$vname."</font>的图片路径错误1,请检查图片地址是否有效  ".$spanstr;
		}
		$fileext=getFileFormat($filePath);
	if($picUrl==""){
$ps="";}else{
$ps = explode('/',$picUrl);}  
		$filename=urldecode($ps[count($ps)-1]);
		if ($fileext!="" && strpos("|.jpg|.gif|.png|.bmp|.jpeg|",strtolower($fileext))>0){
			if(!(strpos($picUrl,".ykimg.com/")>0)){
				if(empty($filename) || strpos($filename,".")==0){
					return "数据<font color=red>".$vname."</font>的图片路径错误2,请检查图片地址是否有效 ".$spanstr;
				}
			}
			$imgStream=getRemoteContent(substr($picUrl,0,strrpos($picUrl,'/')+1).str_replace('+','%20',urlencode($filename)));
			$createStreamFileFlag=createStreamFile($imgStream,$filePath);
			if($createStreamFileFlag){
				$streamLen=strlen($imgStream);
				if($streamLen<2048){
					return "数据<font color=red>".$vname."</font>的图片下载发生错误5,请检查图片地址是否有效  ".$spanstr;
				}else{
					return number_format($streamLen/1024,2);
				}
			}else{
				return "数据<font color=red>".$vname."</font>的图片下载发生错误4,请检查图片地址是否有效  ".$spanstr;
			}
		}else{
			return "数据<font color=red>".$vname."</font>的图片下载发生错误6,请检查图片地址是否有效  ".$spanstr;
		}
		
	}
	
	//@downorupload 采集或是上传的图片水印
	function watermark($filePath,$downorupload=0)
	{
		global $photo_markup,$photo_markdown,$photo_marktype;
		$filePath=str_replace('../','',$filePath);
		if($downorupload==0)$markornot=$photo_markdown;elseif($downorupload==1) $markornot=$photo_markup;else $markornot=true;
		if($markornot)
		{
			switch ($photo_marktype)
			{
				case 0:
					return $this->_imgwatermark($filePath);
					break;
				case 1:
					return $this->_strwatermark($filePath);
					break;
				default:
					break;
			}
		}
	}
	
	function uploadftp($picpath,$picfile,$v_name,$picUrl)
	{
		require_once(sea_INC."/ftp.class.php");
		$Newpicpath = str_replace("../","",$picpath);
		$ftp = new AppFtp($GLOBALS['app_ftphost'] ,$GLOBALS['app_ftpuser'] ,$GLOBALS['app_ftppass'] , $GLOBALS['app_ftpport'] , $GLOBALS['app_ftpdir']);
		if( $ftp->ftpStatus == 1){;
			$localfile= sea_ROOT .'/'. $Newpicpath . $picfile;
			$remotefile= $GLOBALS['app_ftpdir'].$Newpicpath . $picfile;
			$ftp -> mkdirs( $GLOBALS['app_ftpdir'].$Newpicpath );
			$ftpput = $ftp->put($localfile, $remotefile);
			if(!$ftpput){
				return "数据$v_name上传图片到FTP远程服务器失败!本地地址$picUrl<br>";
			}
			$ftp->bye();
			if ($GLOBALS['app_ftpdel']==1){
				@unlink( $picpath . $picfile );
			}
			return true;
		}
		else{
			return $ftp->ftpStatusDes;
		}
	}
	
	
	 //图片水印, $replace=1:覆盖原文件, 0:生成新文件
	 /*
	  * error1:源图片不存在或图片加载失败
	  * error2:创建水印资源失败,可能 是图片后缀有误
	  * error3:创建源图片资源失败,可能 是图片后缀有误
	  * 
	  * */
	function _imgwatermark($filePath,$replace = 1) {
			global $photo_wwidth,$photo_wheight,$photo_waterpos,$photo_marktrans,$photo_diaphaneity,$photo_markimg;	
			$filePath = sea_ROOT.'/'.$filePath;
			$photo_markimg = sea_ROOT."/data/mark/".$photo_markimg;
			if(!file_exists($filePath) || !$water_info = getimagesize($filePath)) {
					return 1;
			 } 
			 
			$water_im = '';	
			switch($water_info[2]) {
				case 1:@$water_im = imagecreatefromgif($photo_markimg);break;
				case 2:@$water_im = imagecreatefromjpeg($photo_markimg);break;
				case 3:@$water_im = imagecreatefrompng($photo_markimg);break;
				default:break;
			}
			if(empty($water_im)) {
				return 2;
			}
 			$src_info = $water_info;
			$src_w = $src_info[0];
			$src_h = $src_info[1];			
			$src_im = '';
			switch($src_info[2]) {
	    		case 1:
					$fp = fopen($filePath, 'rb');
					$filecontent = fread($fp, filesize($filePath));
					fclose($fp);
					if(strpos($filecontent, 'NETSCAPE2.0') === FALSE) {//动画图不加水印
						@$src_im = imagecreatefromgif($filePath);
					}
					break;
				case 2:@$src_im = imagecreatefromjpeg($filePath);break;
				case 3:@$src_im = imagecreatefrompng($filePath);break;
				default:break;
			}
			if(empty($src_im)) {
				return 3;
			}
			/*if(($src_w < $photo_wwidth + 150) || ($src_h < $photo_wheight + 150)) {
				return false;
			}*/
			switch($photo_waterpos) {
				case 0://随机
					$posx = mt_rand(0, ($src_w - $photo_wwidth));
					$posy = mt_rand(0, ($src_h - $photo_wheight));
					break;					
				case 1://顶端居左
					$posx = 0;
					$posy = 0;
					break;
				case 2://顶端居右
					$posx = $src_w - $photo_wwidth;
					$posy = 0;
						break;
				case 3://底端居左
					$posx = 0;
					$posy = $src_h - $photo_wheight;
					break;
				case 4://底端居右
					$posx = $src_w - $photo_wwidth;
					$posy = $src_h - $photo_wheight;
					break;
				default:
					break;
			}
			@imagealphablending($src_im, true);
			@imagecopymerge($src_im, $water_im, $posx, $posy, 0, 0, $photo_wwidth, $photo_wheight,$photo_diaphaneity);
			if ($replace) {
	  		switch($src_info[2]) {
				case 1:@imagegif ($src_im, $filePath,$photo_marktrans);break;
				case 2:@imagejpeg($src_im, $filePath,$photo_marktrans);break;
				case 3:@imagepng ($src_im, $filePath,$photo_marktrans);break;
				default:return false;
			}
			} else {
			switch($src_info[2]) {
				case 1:@imagegif ($src_im, $filePath.'.new.gif',$photo_marktrans);break;
				case 2:@imagejpeg($src_im, $filePath.'.new.jpg',$photo_marktrans);break;
				case 3:@imagepng ($src_im, $filePath.'.new.png',$photo_marktrans);break;
				default:return false;
				 }
			}
			@imagedestroy($water_im);
			@imagedestroy($src_im);
			return true;
	}
	
	function _strwatermark($filePath){
			global $photo_waterpos,$photo_watertext,$photo_fontsize,$photo_fontcolor,$photo_marktrans;	
			$filePath = sea_ROOT.'/'.$filePath;
			if(!file_exists($filePath) || !$src_info = getimagesize($filePath)) {
				return 1;
			}
			$src_w = $src_info[0];
			$src_h = $src_info[1];			
			$src_im = '';
			switch($src_info[2]) {
	    		case 1:
					$fp = fopen($filePath, 'rb');
					$filecontent = fread($fp, filesize($filePath));
					fclose($fp);
					if(strpos($filecontent, 'NETSCAPE2.0') === FALSE) {//动画图不加水印
						@$src_im = imagecreatefromgif($filePath);
					}
					break;
				case 2:@$src_im = imagecreatefromjpeg($filePath);break;
				case 3:@$src_im = imagecreatefrompng($filePath);break;
				default:break;
			}
			if(empty($src_im)) {
				return 2;
			}
		    $temp = imagettfbbox ( ceil ( $photo_fontsize * 2.5 ), 0 ,sea_DATA."/mark/simhei.ttf" , $photo_watertext ); //取得使用 TrueType 字体的文本的范围
		    $w = $temp [ 2 ] - $temp [ 6 ];
			$h = $temp [ 3 ] - $temp [ 7 ];
			unset( $temp );     			
			switch($photo_waterpos) {
				case 0://随机
					$posx = mt_rand(0, ($src_w - $w));
					$posy = mt_rand(0, ($src_h - $h));
					break;					
				case 1://顶端居左
					$posx = 0;
					$posy = 0;
					break;
				case 2://顶端居右
					$posx = $src_w - $w;
					$posy = 0;
						break;
				case 3://底端居左
					$posx = 0;
					$posy = $src_h - $h;
					break;
				case 4://底端居右
					$posx = $src_w - $w;
					$posy = $src_h - $h;
					break;
				default:
					break;
			}
			if( !empty( $photo_fontcolor ) && ( strlen ( $photo_fontcolor )== 7 ) )
			{ 				
	     	$R = hexdec ( substr ( $photo_fontcolor , 1 , 2 ));
	     	$G = hexdec ( substr ( $photo_fontcolor , 3 , 2 ));
	     	$B = hexdec ( substr ( $photo_fontcolor , 5 )); 				
			}
			else
			{
	    		ShowMsg("水印文字颜色格式不正确！","-1 ");
			}
			@imagealphablending($src_im, true);
			@imagestring($src_im,$photo_fontsize, $posx, $posy,$photo_watertext,imagecolorallocate ( $src_im , $R , $G , $B ) );
	  		switch($src_info[2]) {
				case 1:@imagegif ($src_im, $filePath,$photo_marktrans);break;
				case 2:@imagejpeg($src_im, $filePath,$photo_marktrans);break;
				case 3:@imagepng ($src_im, $filePath,$photo_marktrans);break;
				default:return false;
	  		}
			@imagedestroy($src_im);
			return true;       	
		    
	}	
	
}