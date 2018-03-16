<?php
	require(dirname(__FILE__).'/../admin_conn.php');
	chkLogin();
	$action=be("get","action");
	$id=be("get","id");
	$path=be("get","path");
	
	if(isN($id)){$id="pic";}
	$exts=array('jpg','gif','bmp','png',"jpeg","rar","txt","zip");
	$showdir = "upload/". $path . "/" . getSavePicPath($path) . "/";
	$thumbdir = "upload/". $path . "thumb/" . getSavePicPath($path.'thumb') . "/";
	$updir= "../../".$showdir;
	$maxSize=2048;
	
	if(!file_exists($updir)){ mkdir($updir); }
	if($path=='vod'){
		if(!file_exists("../../".$thumbdir)){ mkdir("../../".$thumbdir); }
	}
	
	foreach($_FILES as $FILEa){
		if(empty($FILEa['name'])){ continue; }
		
		if(!in_array(substr($FILEa['name'],-3,3),$exts)){
			$errm = "文件格式不正确　[ <a href=# onclick=history.go(-1)>重新上传</a> ]";
		}
		if($FILEa['size']> $maxSize*1024){
			$errm = "文件大小超过了限制　[ <a onclick=history.go(-1)>重新上传</a> ]";
		}
		if($FILEa['error'] !=0){
			$errm = "未知错误";
		}
		
		if($errm!=''){
			if($action=="xht"){
				$errm = "{'err':'".$errm."','msg':''}";
			}
			echo $errm;
			exit;
		}
		
		$ext = substr($FILEa['name'],-4,4);
		$fname= date('Ymd').time().$ext;
		
		if(function_exists('move_uploaded_file')){
			@move_uploaded_file($FILEa['tmp_name'],$updir.$fname);
		}
		else{
			@copy($FILEa['tmp_name'],$updir.$fname);
		}
		
		$thumbjs='';
		if($MAC['upload']['watermark']==1){
			imageWaterMark($updir.$fname,getcwd(),$MAC['upload']['waterlocation'],$MAC['upload']['waterfont']);
		}
		if($id=='d_pic' && $MAC['upload']['thumb']==1){
			$thumbst = img2thumb($updir.$fname,"../../".$thumbdir.$fname,$MAC['upload']['thumbw'],$MAC['upload']['thumbh']);
			if($thumbst){ $thumbjs="<script>parent.document.getElementById('d_picthumb').value='".$thumbdir.$fname."'</script>"; }
		}
		
		if ($path=='vod' && $action!="xht" && $MAC['upload']['ftp']==1){
			uploadftp($showdir,$fname);
			if($thumbst){ uploadftp($thumbdir,$fname); }
		}
		
		if($action=="xht"){
			echo "{'err':'".$errm."','msg':'".$MAC['app']['installdir'].$showdir.$fname."'}";
		}
		else{
			echo "<script>parent.document.getElementById('".$id."').value='".$showdir.$fname."'</script>上传成功![ <a href=### onclick=history.go(-1)>重新上传</a> ]".$thumbjs;
		}
	}
?>