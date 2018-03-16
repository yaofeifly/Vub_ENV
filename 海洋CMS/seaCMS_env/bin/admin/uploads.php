<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
require_once(sea_DATA."/config.ftp.php");
$photo_markimg = sea_ROOT."/data/mark/".$photo_markimg;
$up = new uploader($photo_markup,$photo_markdown,$photo_marktype,$photo_wwidth,$photo_wheight,$photo_waterpos,$photo_watertext,$photo_fontsize,$photo_fontcolor,$photo_marktrans,$photo_diaphaneity,$photo_markimg);
//修改默认配置
if($is=='slide')
{
	$up->config(array('saveDir'=>'../pic/slide','imageWidth'=>$cfg_ddimg_width));
}
else
{
	$up->config(array('saveDir'=>'../'.$cfg_upload_dir.'/s','imageWidth'=>$cfg_ddimg_width));
}
//upfile为上传表单时的文件名
$up->saveFile('file1');

if ( $up )
{
	$spic = $up->_fileName;
	$spath = str_replace('../','',$up->_fileUrl);
	
	if($is=='slide')
	{
		echo "<script>parent.document.getElementById('item_url$id').value='$spath';</script>";
	}
	else
	{
		if($app_ftp==1)
		{
			$urlupload = uploadftp2($spath);
			if($urlupload){
				$spath = $app_ftpurl.$app_ftpdir.$spath;
			}
		}
		echo "<script>parent.document.getElementById('addform').v_spic.value='$spath';</script>";
	}
	echo "<table><tr><td bgcolor=#FBFEFF>".$spic."上传成功！[<a href=# onclick=history.go(-1)>重新上传</a>]</td></tr></table>";
	exit( );
}
echo "<table><tr><td bgcolor=#FBFEFF>".$pic[1]."[　<a href=# onclick=history.go(-1)>重新上传</a>　]</td></tr></table>";
exit( );

class uploader {
        var $saveDir = 'uploads/allimg';
        var $subDir = 'Ym';
        var $allowExts = array('jpg', 'gif',  'png', 'rar', 'zip', 'bmp');
        var $maxSize = '2048';
        var $hasThumb = 0; //是否生成缩略图
        var $imageWidth= '300';
        var $thumbWidth = '100';
        
        function __construct($markup,$markdown,$marktype,$wwidth,$wheight,$waterpos,$watertext,$fontsize,$fontcolor,$marktrans,$diaphaneity,$markimg){
        	$this->_markup = $markup;
        	$this->_markdown = $markdown;
        	$this->_marktype = $marktype;
        	$this->_wwidth = $wwidth;
        	$this->_wheight = $wheight;
        	$this->_waterpos = $waterpos;
        	$this->_watertext = $watertext;
        	$this->_fontsize = $fontsize;
        	$this->_fontcolor = $fontcolor;
        	$this->_marktrans = $marktrans;
        	$this->_diaphaneity = $diaphaneity;
        	$this->_markimg = $markimg;
        		
        }
		
        function config($options) {
                if(is_array($options)) {
                        foreach($options as $key=>$val) {
                                if(isset($val))$this->$key = $val;
                        }
                }
        }
        function getExt($filename) {
                return substr($filename,strrpos($filename,".")+1);
        }
        function mkDirs($path){
                if (!file_exists($path)){
                        $this->mkDirs(dirname($path));
                        mkdir($path, 0777) or exit('创建目录:'.$path.'时出错,请确认该目录可写!');
                        touch($path.'/index.html');
                }
        }
        function check($upfile) {
				global $is;
                //是否为上传文件
                if(!is_uploaded_file($_FILES[$upfile]['tmp_name'])) {
                        exit('非法上传,你觉得这有意思么?');
                }
                //上传是否出错
                if($_FILES[$upfile]['error'] > 0) {
                        exit('文件上传出错!代码为:'.$_FILES[$upfile]['error']);
                }
                //文件大小是否超过系统设置(这个是自定义并非服务器环境设置)
                if($_FILES[$upfile]['size'] > $this->maxSize*1024) {
                        exit('上传文件大小超过系统设置!');
                }
                //设置上传文件属性并检测文件格式是否合法
                $this->_upFile = $_FILES[$upfile]['tmp_name'];
                //文件名
                $this->_fileName = $_FILES[$upfile]['name'];
                //文件大小
                $this->_fileSize = $_FILES[$upfile]['size'];
                //文件格式
                $this->_fileExt = $this->getExt($this->_fileName);
                if(!in_array(strtolower($this->_fileExt), $this->allowExts)) {
                        exit('上传文件的格式未经允许!');
                }
                //设置保存目录属性并生成该目录
                if($is=='slide')
				{
					$this->_savePath = $this->saveDir;
				}else
				{
					$this->_savePath = $this->saveDir."/".date($this->subDir);
				}$this->mkDirs($this->_savePath);
                //上传文件是否为图片
                if(in_array(strtolower($this->_fileExt), array('jpg','gif','png'))) {
                        $this->_isimage = true;
                }else {
                        $this->_isimage = false;
                }
        }
        function saveToFile() {
				global $is;
                //文件保存路径
                $this->_destination = $this->_savePath."/".'file_'.date('dHis').'.'.$this->_fileExt;
                if($is=='slide')
				{
                $this->_fileUrl = 'file_'.date('dHis').'.'.$this->_fileExt;
				}
				else
				{
                $this->_fileUrl = $this->saveDir."/".date($this->subDir)."/".'file_'.date('dHis').'.'.$this->_fileExt;
				}
				copy($this->_upFile, $this->_destination) or exit('复制文件时出错!');
        }
        function saveToImage() {
				global $is;
                //获取上传图片的信息[0:宽度,1:高度,2:格式,3:宽高字符串,还可返回MIME]
                $imginfo = getImageSize($this->_upFile);
                //上传图片宽度属性
                $this->_width = $imginfo[0];
                //上传图片高度属性
                $this->_height = $imginfo[1];
                //上传图片MIME属性
                $this->_mime = $imginfo['mime'];
				//保存图片文件名
				$this->_saveName = substr(md5($this->_fileName.time()),0,16);
                //图片文件的存放路径
                $this->_destination = $this->_savePath."/".$this->_saveName.'.'.$this->_fileExt;
                //函数返回文件路径,不包括根目录
                if($is=='slide')
				{
                $this->_fileUrl = $this->_saveName .'.'.$this->_fileExt;
				}else
				{
                $this->_fileUrl = $this->saveDir."/".date($this->subDir)."/".$this->_saveName.'.'.$this->_fileExt;
				}
				//如果不支持GD则直接保存图片不进行裁剪
                if(!function_exists('imagecreatetruecolor')) {
                        $this->saveToFile();
                        return ;
                }
                //上传图片宽度超过定义则进行裁剪,否则直接复制
                
                        //图片宽度没有超过定义则直接复制,这会极大加快处理速度(GD太消耗系统资源)
                        copy($this->_upFile, $this->_destination) or exit('复制文件时出错!');
              
                //如果设置自动缩略图var $hasThumb = 1则生成缩略图
                if($this->hasThumb) {
                        $this->_destination = $this->_savePath."/".'image_'.date('dHis').'_thumb'.'.'.$this->_fileExt;
                        if($this->_width > $this->thumbWidth) {
                                $this->image($this->thumbWidth);
                        }else {
                                copy($this->_upFile, $this->_destination) or exit('复制文件时出错!');
                        }
                }
        }
        function image($width='600') {
                //相关注释请查看PHP手册GD篇章
                $height = $width/($this->_width/$this->_height);
                $newImage = imagecreatetruecolor($width, $height) or die("Cannot Initialize new GD image stream");
                switch($this->_mime) {
                case 'image/jpeg':
                        $imagecreatefunc = 'imagecreatefromjpeg';
                        $imagefunc = 'imagejpeg';
                        break;
                case 'image/gif':
                        $imagecreatefunc = 'imagecreatefromgif';
                        $imagefunc = 'imagegif';
                        break;
                case 'image/png':
                        $imagecreatefunc = 'imagecreatefrompng';
                        $imagefunc = 'imagepng';
                        break;
                }
                $image = $imagecreatefunc($this->_upFile);
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height,$this->_width,$this->_height);
                if($imginfo['mime'] == 'image/jpeg') {
                        imagejpeg($newImage,$this->_destination, 100);

                }else {
                        $imagefunc($newImage, $this->_destination);
                }
                imagedestroy($image);
                imagedestroy($newImage);
        }
        //图片水印, $replace=1:覆盖原文件, 0:生成新文件
		function imgwatermark($replace = 1) {
				$this->_destination = sea_ROOT.substr($this->_destination,2);
				if(!file_exists($this->_markimg) || !$water_info = getimagesize($this->_markimg)) {
    					return 'err';
   				 } 
   				 
				$water_im = '';	
    			switch($water_info[2]) {
        			case 1:@$water_im = imagecreatefromgif($this->_markimg);break;
        			case 2:@$water_im = imagecreatefromjpeg($this->_markimg);break;
        			case 3:@$water_im = imagecreatefrompng($this->_markimg);break;
        			default:break;
    			}
				if(empty($water_im)) {
					return '';
				}
    			if(!file_exists($this->_destination) || !$src_info = getimagesize($this->_destination)) {
    			return '';
    			}
  				$src_w = $src_info[0];
   				$src_h = $src_info[1];			
    			$src_im = '';
	    		switch($src_info[2]) {
    	    		case 1:
        				$fp = fopen($this->_destination, 'rb');
						$filecontent = fread($fp, filesize($this->_destination));
						fclose($fp);
						if(strpos($filecontent, 'NETSCAPE2.0') === FALSE) {//动画图不加水印
        					@$src_im = imagecreatefromgif($this->_destination);
						}
        				break;
        			case 2:@$src_im = imagecreatefromjpeg($this->_destination);break;
        			case 3:@$src_im = imagecreatefrompng($this->_destination);break;
        			default:break;
    			}
	    		if(empty($src_im)) {
    				return '';
    			}
	    		/*if(($src_w < $this->_wwidth + 150) || ($src_h < $this->_wheight + 150)) {
    				return '';
    			}*/
				switch($this->_waterpos) {
					case 0://随机
						$posx = mt_rand(0, ($src_w - $this->_wwidth));
						$posy = mt_rand(0, ($src_h - $this->_wheight));
						break;					
					case 1://顶端居左
						$posx = 0;
						$posy = 0;
						break;
					case 2://顶端居右
						$posx = $src_w - $this->_wwidth;
						$posy = 0;
							break;
					case 3://底端居左
						$posx = 0;
						$posy = $src_h - $this->_wheight;
						break;
					case 4://底端居右
						$posx = $src_w - $this->_wwidth;
						$posy = $src_h - $this->_wheight;
						break;
					default:
						break;
				}
				@imagealphablending($src_im, true);
				@imagecopymerge($src_im, $water_im, $posx, $posy, 0, 0, $this->_wwidth, $this->_wheight,$this->_diaphaneity);
	    		if ($replace) {
    	  		switch($src_info[2]) {
        			case 1:@imagegif ($src_im, $this->_destination,$this->_marktrans);break;
        			case 2:@imagejpeg($src_im, $this->_destination,$this->_marktrans);break;
        			case 3:@imagepng ($src_im, $this->_destination,$this->_marktrans);break;
        			default:return '';
      			}
				} else {
      			switch($src_info[2]) {
        			case 1:@imagegif ($src_im, $this->_destination.'.new.gif',$this->_marktrans);break;
        			case 2:@imagejpeg($src_im, $this->_destination.'.new.jpg',$this->_marktrans);break;
        			case 3:@imagepng ($src_im, $this->_destination.'.new.png',$this->_marktrans);break;
       				default:return '';
     				 }
				}
				@imagedestroy($water_im);
				@imagedestroy($src_im);
		}
        function strwatermark(){
				$this->_destination = sea_ROOT.substr($this->_destination,2);
    			if(!file_exists($this->_destination) || !$src_info = getimagesize($this->_destination)) {
    			return '';
    			}
  				$src_w = $src_info[0];
   				$src_h = $src_info[1];			
    			$src_im = '';
	    		switch($src_info[2]) {
    	    		case 1:
        				$fp = fopen($this->_destination, 'rb');
						$filecontent = fread($fp, filesize($this->_destination));
						fclose($fp);
						if(strpos($filecontent, 'NETSCAPE2.0') === FALSE) {//动画图不加水印
        					@$src_im = imagecreatefromgif($this->_destination);
						}
        				break;
        			case 2:@$src_im = imagecreatefromjpeg($this->_destination);break;
        			case 3:@$src_im = imagecreatefrompng($this->_destination);break;
        			default:break;
    			}
	    		if(empty($src_im)) {
    				return '';
    			}
        	    $temp = imagettfbbox ( ceil ( $this->_fontsize * 2.5 ), 0 ,sea_DATA."/mark/simhei.ttf" , $this->_watertext ); //取得使用 TrueType 字体的文本的范围
        	    $w = $temp [ 2 ] - $temp [ 6 ];
        		$h = $temp [ 3 ] - $temp [ 7 ];
        		unset( $temp );     			
    			switch($this->_waterpos) {
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
        		if( !empty( $this->_fontcolor ) && ( strlen ( $this->_fontcolor )== 7 ) )
        		{ 				
             	$R = hexdec ( substr ( $this->_fontcolor , 1 , 2 ));
             	$G = hexdec ( substr ( $this->_fontcolor , 3 , 2 ));
             	$B = hexdec ( substr ( $this->_fontcolor , 5 )); 				
        		}
        		else
        		{
            		die( "水印文字颜色格式不正确！" );
        		}
        		@imagealphablending($src_im, true);
				@imagestring($src_im,$this->_fontsize, $posx, $posy,$this->_watertext,imagecolorallocate ( $src_im , $R , $G , $B ) );
    	  		switch($src_info[2]) {
        			case 1:@imagegif ($src_im, $this->_destination,$this->_marktrans);break;
        			case 2:@imagejpeg($src_im, $this->_destination,$this->_marktrans);break;
        			case 3:@imagepng ($src_im, $this->_destination,$this->_marktrans);break;
        			default:return '';
    	  		}
				@imagedestroy($src_im);        	
        	    
        }
        function saveFile($upfile) {          
                $this->check($upfile);
                if($this->_isimage) {
                        $this->saveToImage();
                        if($this->_markup==1){
						if($this->_marktype==1)
                		{
                		$this->strwatermark();
                		}else{
						$this->imgwatermark();
                		}
                        }
                        else
                        {
                        }
                        return array('name'=>$this->_fileName,'ext'=>$this->_fileExt,'size'=>$this->_fileSize,'path'=>$this->_destination,'url'=>$this->_fileUrl,'isimage'=>'1','width'=>$this->_width,'height'=>$this->_height);
                }else {
                        $this->saveToFile();
                        return array('name'=>$this->_fileName,'ext'=>$this->_fileExt,'size'=>$this->_fileSize,'path'=>$this->_destination,'url'=>$this->_fileUrl,'isimage'=>'0');
                }
        }
}


?>
