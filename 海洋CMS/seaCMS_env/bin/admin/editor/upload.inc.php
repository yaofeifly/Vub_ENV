<?php
function uploadFile($save_dir, $save_url, $file_field_name='file', $max_size=0, $exts='jpg;gif;png;jpeg;', $auto_create_sub_dir=true, $index=-1)
{
	$result=array();
	$result['error']=0;
	$result['message']='';
	$result['url']='';//上传后文件网址
	$result['file']='';//相对保存目录的文件路径
	$result['name']='';
	$result['ext']='';//上传文件的后缀小写形式,如".jpg"
	//$result['exec_js']='';

	if(!isset($_FILES[$file_field_name]['tmp_name'])){
		$result['error']=1;
		$result['message']='失败:上传失败';
		return $result;
	}
	if(is_array($_FILES[$file_field_name]['tmp_name'])){
		if(!isset($_FILES[$file_field_name]['tmp_name'][$index])){
			$result['error']=6;
			$result['message']='失败:对应索引的文件不存在';
			return $result;
		}
		$tmp_file=$_FILES[$file_field_name]['tmp_name'][$index];
		$file_name=$_FILES[$file_field_name]['name'][$index];
		$file_size=$_FILES[$file_field_name]['size'][$index];
		$file_type=$_FILES[$file_field_name]['size'][$index];//mime: image/jpeg; image/png
		$file_error=$_FILES[$file_field_name]['error'][$index];
	}else{
		$tmp_file=$_FILES[$file_field_name]['tmp_name'];
		$file_name=$_FILES[$file_field_name]['name'];
		$file_size=$_FILES[$file_field_name]['size'];
		$file_type=$_FILES[$file_field_name]['size'];//mime: image/jpeg; image/png
		$file_error=$_FILES[$file_field_name]['error'];
		
	}
	
	if(!is_uploaded_file($tmp_file)){
		$result['error']=1;
		$result['message']='失败:上传失败';
		return $result;
	}

	//检查大小
	if($max_size>0  && $file_size>$max_size){
		$result['error']=2;
		
		if($max_size>1048576)//1M
		{
			$size = round($max_size / 1048576,2) .'MB';
		}elseif($max_size>1024){
			$size = round($max_size / 1024,2) .'KB';
		}else{	
			$size = $max_size .'B';
		}

		$result['message']='失败:文件太大, 请上传小于 '.$size.' 的文件.';
		return $result;
	}

	$result['name'] = $file_name;
	$ext = $result['ext'] = '.'.strtolower(substr(strrchr($file_name,'.'),1));
	//文件后缀检查
	if($exts!='*.*' && $exts!='*' && $exts!=''){
		$exts= explode(';',strtolower($exts));
		if(!in_array(substr($ext,1),$exts)){
			$result['error']=3;
			$result['message']='失败:请上传(*.'.implode(';*.',$exts).')格式文件';
			return $result;
		}
	}
	$p = $auto_create_sub_dir ? date('Ym/d/') :'';
	if(!is_dir($save_dir . $p) && !mkdir($save_dir . $p, 0755, true)){
		$result['error']=4;
		$result['message']='失败:建目录 [UPLOAD_DIR]'.$p.' 失败';
		return $result;
	}

	$f = $p.time().$ext;
	while(is_file($save_dir.$f)){
		$f = $p.time().'_'.uniqid().$ext;
	}
	if(!move_uploaded_file($tmp_file, $save_dir.$f)){
		$result['error']=5;
		$result['message']='失败, 写入文件['.$save_dir.$f.']失败.';
		return $result;
	}

	$result['url'] = $save_url . $f;
	$result['file'] = $f;

	return $result;
}
//上传图片
function uploadImage($save_dir, $save_url, $file_field_name='file', $max_size=2097152, $exts='jpg;gif;png', $auto_create_sub_dir=true,$index=-1)
{
	$result=uploadFile($save_dir, $save_url, $file_field_name, $max_size, $exts, $auto_create_sub_dir,$index);
	$result['width']=0;
	$result['height']=0;
	if($result['error']==0){
		$size_info = @getimagesize($save_dir.$result['file']);
		if(false==$size_info){
			return $result;
		}
		$result['width']=$size_info[0];
		$result['height']=$size_info[1];
		//list($width,$height,$type,$text)
	}
	return $result;
}
?>