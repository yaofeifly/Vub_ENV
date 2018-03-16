<?php
$config=array();

$config['type']=array("flash","img"); //上传允许type值

$config['img']=array("jpg","bmp","gif","png"); //img允许后缀
$config['flash']=array("flv","swf"); //flash允许后缀

$config['flash_size']=200; //上传flash大小上限 单位：KB
$config['img_size']=500; //上传img大小上限 单位：KB

$config['message']="上传成功"; //上传成功后显示的消息，若为空则不显示

$config['name']=mktime(); //上传后的文件命名规则 这里以unix时间戳来命名

$config['flash_dir']="../uploads/editor"; //上传flash文件地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"
$config['img_dir']="../uploads/editor"; //上传img文件地址 采用绝对地址 采用绝对地址 方便upload.php文件放在站内的任何位置 后面不加"/"

$config['site_url']=""; //网站的网址 这与图片上传后的地址有关 最后不加"/" 可留空

//文件上传
uploadfile();

function uploadfile()
{
global $config;
//判断是否是非法调用
if(empty($_GET['CKEditorFuncNum']))
   mkhtml(1,"","错误的功能调用请求");
$fn=$_GET['CKEditorFuncNum'];
if(!in_array($_GET['type'],$config['type']))
   mkhtml(1,"","错误的文件调用请求");
$type=$_GET['type'];
if(is_uploaded_file($_FILES['upload']['tmp_name']))
{
   //判断上传文件是否允许
   $filearr=pathinfo($_FILES['upload']['name']);
   $filetype=$filearr["extension"];
   if(!in_array($filetype,$config[$type]))
    mkhtml($fn,"","错误的文件类型！");
   //判断文件大小是否符合要求
   if($_FILES['upload']['size']>$config[$type."_size"]*1024)
    mkhtml($fn,"","上传的文件不能超过".$config[$type."_size"]."KB！");
   //$filearr=explode(".",$_FILES['upload']['name']);
   //$filetype=$filearr[count($filearr)-1];
   $file_host=$config[$type."_dir"]."/".$config['name'].".".$filetype;
//   $file_host=$_SERVER['DOCUMENT_ROOT'].$file_abso;
  
   if(move_uploaded_file($_FILES['upload']['tmp_name'],$file_host))
   {
    mkhtml($fn,$config['site_url'].$file_abso,$config['message']);
   }
   else
   {
    mkhtml($fn,"","文件上传失败，请检查上传目录设置和目录读写权限");
   }
}
}
//输出js调用
function mkhtml($fn,$fileurl,$message)
{
$str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
exit($str);
}
?>