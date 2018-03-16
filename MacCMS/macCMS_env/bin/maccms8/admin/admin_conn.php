<?php
define('MAC_ADMIN', preg_replace("|[/\\\]{1,}|",'/',dirname(__FILE__) ) );
require(MAC_ADMIN."/../inc/conn.php");
require(MAC_ADMIN.'/../inc/common/phplib.php');
define('MAC_VERSION','2014.05.23');

$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);

function chkLogin()
{
	global $db;
	$m_id = getCookie('adminid');
	$m_name = getCookie('adminname');
	$m_check = getCookie('admincheck');
	
	if (!isN($m_name) && !isN($m_id)){
		$row = $db->getRow('SELECT * FROM {pre}manager WHERE m_name=\'' . $m_name .'\' AND m_id= \''.$m_id .'\' AND m_status=1');
		if($row){
			$loginValidate = md5($row['m_random'] . $row['m_name'] . $row['m_id']);
			if ($m_check != $loginValidate){ 
			   sCookie ('admincheck','');
			   redirect('?m=admin-login','top.');
			}
		}
		else{
			sCookie ('admincheck','');
		    redirect('?m=admin-login','top.');
		}
	}
	else{
		redirect('?m=admin-login','top.');
	}
}

function headAdmin2($title)
{
echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta http-equiv="X-UA-Compatible" content="IE=7" /><title>$title - 苹果CMS</title><link rel="stylesheet" type="text/css" href="tpl/images/style.css" /></head><body style="line-height:20px">
EOT;
}

function footAdmin()
{
	echo '<div style="text-align:center; margin-top:10px; color: #CCCCCC">'.getRunTime().'</div></body></html>';
}

function chkBreak($f)
{
	if(file_exists('../cache/break/'.$f.'.html')) { return true ;} else { return false;}
}

function delBreak($f)
{
	if(file_exists('../cache/break/'.$f.'.html'))  { unlink('../cache/break/'.$f.'.html'); }
}

function setBreak($f,$url)
{
	$fdes = '<meta http-equiv="refresh" content="0;url='.$url.'">';
	@fwrite(fopen('../cache/break/'.$f.'.html','wb'),$fdes);
}

function getBreak($f)
{
	return @file_get_contents('../cache/break/'.$f.'.html');
}

function SpecialChar($str)
{
	$str = ','.$str;
	if( strpos($str,'*')>0 || strpos($str,':')>0 || strpos($str,'?')>0 || strpos($str,'"')>0 || strpos($str,'\'')>0  || strpos($str,'<')>0  || strpos($str,'>')>0  || strpos($str,'|')>0 || strpos($str,'\\')>0 ){
		alert('每项生成配置中均不能出现 * : ? \' " < > | \ 等特殊符号');
	}
	
	if(! (strpos($str,'{id}') || strpos($str,'{name}') || strpos($str,'{enname}')) ) {
		alert('每项生成配置中:{id},{name},{enname} 3个变量至少出现1个');
	}
}

function make_header($table)
{
	$sql = 'DROP TABLE IF EXISTS '.$table."\n";
	$row = $GLOBALS['db']->getRow('show create table '.$table);
	$tmp = preg_replace("/\n/","",$row["Create Table"]);
	$sql.= $tmp."\n";
	unset($row);
	return $sql;
}

function make_manager($table)
{
	$sql = make_header($table);
	$rsfield = $GLOBALS['db']->getTableFields($GLOBALS['MAC']['db']['name'],$table);
	$i=0;
	while($rowfield = mysql_fetch_field($rsfield)){
		$fs[$i] = trim($rowfield->name);
		$i++;
	}
	unset($rsfield);
	$fsd=$i-1;
	$rsdata = $GLOBALS['db']->getAll('select * from '.$table);
	$rscount = count($rsdata);
	$intable = 'INSERT INTO '.$table.' VALUES(';
	for($j=0;$j<$rscount;$j++){
		$line = $intable;
		for($k=0;$k<=$fsd;$k++){
			if($k < $fsd){
				$line.= "'".mysql_escape_string($rsdata[$j][$fs[$k]])."',";
			}
			else{
				$line.= "'".mysql_escape_string($rsdata[$j][$fs[$k]])."');\r\n";
			}
		}
		$sql.=$line;
	}
	unset($fs);
	unset($rsdata);
	return $sql;
}

function delDirAndFile( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delDirAndFile( "$dirName/$item" );
				} else {
					if( unlink( "$dirName/$item" ) ) { 
						//echo "成功删除文件： $dirName/$item<br />\n"; 
					}
				}
	   		}
	   }
	   closedir( $handle );
	   if( rmdir( $dirName ) ) {
	   	//echo "成功删除目录： $dirName<br />\n"; 
	   }
	}
	unset($handle);
}
function delFileUnderDir( $dirName,$ext='*')
{
	if ( $handle = opendir( "$dirName" ) ) {
		while ( false !== ( $item = readdir( $handle ) ) ) {
			if ( $item != "." && $item != ".." ) {
				if ( is_dir( "$dirName/$item" ) ) {
					delFileUnderDir( "$dirName/$item" );
				} else {
					if($ext=='*'){
						unlink( "$dirName/$item" );
					}
					else{
						if(strpos($item,'.'.$ext)){
							unlink( "$dirName/$item" );
						}
					}
					//echo "成功删除文件： $dirName/$item<br />\n";
				}
			}
   		}
	closedir( $handle );
	}
	unset($handle);
}

function downFile($f,$e='.txt')
{
	$fp = "../cache/export/". iconv("UTF-8", "GBK", $f);
	$file = fopen($fp,"r");
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($fp));
	if(strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")){
 		Header("Content-Disposition: attachment; filename=". urlencode($f).";");
 	}
 	else{
 		Header("Content-Disposition: attachment; filename=". $f .";");
 	}
	echo fread($file,filesize($fp));
	fclose($file);
}

function getTabName($tab)
{
	if (strpos($tab,'art_relation')>0){
		return '文章关系';
	}
	if (strpos($tab,'art_topic')>0){
		return '文章专题';
	}
	if (strpos($tab,'art_type')>0){
		return '文章分类';
	}
	if (strpos($tab,'art')>0){
		return '文章';
	}
	if (strpos($tab,'comment')>0){
		return '评论信息';
	}
	if (strpos($tab,'gbook')>0){
		return '留言本';
	}
	if (strpos($tab,'manager')>0){
		return '后台用户';
	}
	if (strpos($tab,'link')>0){
		return '友情链接';
	}
	if (strpos($tab,'user_card')>0){
		return '会员充值卡';
	}
	if (strpos($tab,'user_group')>0){
		return '会员组别';
	}
	if (strpos($tab,'user')>0){
		return '会员';
	}
	if (strpos($tab,'user_visit')>0){
		return '会员推广记录';
	}
	if (strpos($tab,'vod_relation')>0){
		return '视频关系';
	}
	if (strpos($tab,'vod_topic')>0){
		return '视频专题';
	}
	if (strpos($tab,'vod_type')>0){
		return '视频分类';
	}
	if (strpos($tab,'vod')>0){
		return '视频';
	}
}

function getTemplateFlag($f)
{
	switch($f)
	{
		case "home_include.html":$str="引入资源模板";break;
		case "home_head.html":$str="头部模板";break;
		case "home_foot.html":$str="底部模板";break;
		case "home_gbook.html":$str="留言本模板";break;
		case "home_comment.html":$str="评论模板";break;
		
		case "vod_index.html":$str="首页模板";break;
		case "art_index.html":$str="文章首页模版";break;
		case "art_map.html":$str="文章地图页";break;
		case "art_detail.html":$str="文章内容页";break;
		case "art_list.html":$str="文章筛选页";break;
		case "art_type.html":$str="文章分类页";break;
		case "art_search.html":$str="文章搜索页";break;
		case "art_topicindex.html":$str="文章专题首页";break;
		case "art_topiclist.html":$str="文章专题数据列表";break;
		
		case "vod_detail.html":$str="视频内容页";break;
		case "vod_list.html":$str="视频筛选页";break;
		case "vod_type.html":$str="视频分类页";break;
		case "vod_map.html":$str="视频地图页";break;
		case "vod_play.html":$str="视频播放页";break;
		case "vod_down.html":$str="视频下载页";break;
		case "vod_search.html":$str="视频搜索页";break;
		case "vod_topicindex.html":$str="视频专题首页";break;
		case "vod_topiclist.html":$str="视频专题数据列表";break;
		case "vod_playopen.html":$str="弹窗播放页面";break;
		
		case "userlogin.html":$str="登陆框未登录模板";break;
		case "userlogged.html": $str="登陆框已登录模板";break;
		case "config.xml" : $str="模版配置文件";break;
		default: $str="自定义文件";break;
	}
	return $str;
}
?>