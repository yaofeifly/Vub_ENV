<?php
require_once('common.php');

if($action=='reporterr'){
$id=$_SERVER['HTTP_REFERER'];
$id=getSubStrByFromAndEnd($id,"?id=","","start");
$id = !empty($id) && is_numeric($id) ? $id : 0;
$ip = GetIP();
$author = HtmlReplace($author);
$errtxt = trimMsg(cn_substr($errtxt,2000),1);
$time = time();
	$query = "INSERT INTO `sea_erradd`(vid,author,ip,errtxt,sendtime)
                  VALUES ('$id','$author','$ip','$errtxt','$time'); ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("谢谢您对本网站的支持，我们会尽快处理您的建议！","javascript:window.close();");
	exit();
}
?>