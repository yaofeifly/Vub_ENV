<?php
require_once(dirname(__FILE__)."/config.php");
$up = new uploader();
$up->config(array('saveDir'=>'../uploads/zt'));
$up->saveFile('file1');

if ( $up )
{
	$spic = $up->_fileName;
	echo "<script>parent.onUploadBack('".$up->_fileUrl."');</script>";
	echo "<table><tr><td bgcolor=#FBFEFF>".$spic."上传成功！[　<a href=# onclick=history.go(-1)>重新上传</a>　]</td></tr></table>";
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

		function __construct(){
				if(!is_dir($this->saveDir)){
					CreateDir($this->saveDir);
				}
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
                $this->_savePath = $this->saveDir;
                $this->mkDirs($this->_savePath);
                //上传文件是否为图片
                if(in_array(strtolower($this->_fileExt), array('jpg','gif','png'))) {
                        $this->_isimage = true;
                }else {
                        $this->_isimage = false;
                }
        }
        function saveToFile() {
                //文件保存路径
                $this->_destination = $this->_savePath."/".'file_'.date('dHis').'.'.$this->_fileExt;
                $this->_fileUrl = 'file_'.date('dHis').'.'.$this->_fileExt;
                copy($this->_upFile, $this->_destination) or exit('复制文件时出错!');
        }
        function saveToImage() {
                //获取上传图片的信息[0:宽度,1:高度,2:格式,3:宽高字符串,还可返回MIME]
                $imginfo = getImageSize($this->_upFile);
                //上传图片宽度属性
                $this->_width = $imginfo[0];
                //上传图片高度属性
                $this->_height = $imginfo[1];
                //上传图片MIME属性
                $this->_mime = $imginfo['mime'];
                //图片文件的存放路径
                $this->_destination = $this->_savePath."/".'image_'.date('dHis').'.'.$this->_fileExt;
                //函数返回文件路径,不包括根目录
                $this->_fileUrl = 'image_'.date('dHis').'.'.$this->_fileExt;
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
 
        function saveFile($upfile) {          
                $this->check($upfile);
                if($this->_isimage) {
                        $this->saveToImage();
                        return array('name'=>$this->_fileName,'ext'=>$this->_fileExt,'size'=>$this->_fileSize,'path'=>$this->_destination,'url'=>$this->_fileUrl,'isimage'=>'1','width'=>$this->_width,'height'=>$this->_height);
                }else {
                        $this->saveToFile();
                        return array('name'=>$this->_fileName,'ext'=>$this->_fileExt,'size'=>$this->_fileSize,'path'=>$this->_destination,'url'=>$this->_fileUrl,'isimage'=>'0');
                }
        }
}


?>
