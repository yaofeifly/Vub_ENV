<?php
define('sea_ADMIN', preg_replace("|[/\\\]{1,}|",'/',dirname(__FILE__) ) );
require_once(sea_ADMIN."/../include/common.php");
require_once(sea_INC."/check.admin.php");
require_once(sea_ADMIN."/coplugins/Snoopy.class.php");
header("Cache-Control:private");
$dsql->safeCheck = false;
$dsql->SetLongLink();

//获得当前脚本名称，如果你的系统被禁用了$_SERVER变量，请自行更改这个选项
$EkNowurl = $s_scriptName = '';
$isUrlOpen = @ini_get("allow_url_fopen");
$EkNowurl = GetCurUrl();
$EkNowurls = explode('?',$EkNowurl);
$s_scriptName = $EkNowurls[0];
$Pirurl=getreferer();
if(empty($Pirurl)) $Pirurl=$EkNowurl;

//检验用户登录状态
$cuserLogin = new userLogin();
$hashstr=md5($cfg_dbpwd.$cfg_dbname.$cfg_dbuser);//构造session安全码
if($cuserLogin->getUserID()==-1 OR $_SESSION['hashstr'] !== $hashstr)
{
	header("location:login.php?gotopage=".urlencode($EkNowurl));
	exit();
}


function makeTopicSelect($selectName,$strSelect,$topicId)
{
	global $dsql,$cfg_iscache;
	$sql="select id,name from sea_topic order by sort asc";
	if($cfg_iscache){
	$mycachefile=md5('array_Topic_Lists_all');
	setCache($mycachefile,$sql);
	$rows=getCache($mycachefile);
	}else{
	$rows=array();
	$dsql->SetQuery($sql);
	$dsql->Execute('al');
	while($rowr=$dsql->GetObject('al'))
	{
	$rows[]=$rowr;
	}
	unset($rowr);
	}
	$str = "<select name='".$selectName."' id='".$selectName."' >";
	if(!empty($strSelect)) $str .= "<option value='0'>".$strSelect."</option>";
	foreach($rows as $row)
	{
		if(!empty($topicId) && ($row->id==$topicId)){
		$str .= "<option value='".$row->id."' selected>$row->name</option>";
		}else{
		$str .= "<option value='".$row->id."'>$row->name</option>";
		}
	}
	$str .= "</select>";
	return $str;
}

function makeTypeOptionSelected($topId,$separateStr,$span="",$compareValue,$tptype=0)
{
	$tlist=getTypeListsOnCache($tptype);
	if ($topId!=0){$span.=$separateStr;}else{$span="";}

	foreach($tlist as $row)
	{
		
		if($row->upid==$topId)
		{
		
			if ($row->tid==$compareValue){$selectedStr=" selected";}else{$selectedStr="";}	
			echo "<option value='".$row->tid."'".$selectedStr.">".$span."&nbsp;|—".$row->tname."</option>";
			makeTypeOptionSelected($row->tid,$separateStr,$span,$compareValue,$tptype);
			
		}
	}
	if (!empty($span)){$span=substr($span,(strlen($span)-strlen($separateStr)));}
}
function makeTypeOptionSelected_Multiple($topId,$separateStr,$span="",$compareValue,$tptype=0)
{
	$tlist=getTypeListsOnCache($tptype);
	if ($topId!=0){$span.=$separateStr;}else{$span="";}
	if($compareValue==""){
$ids_arr="";}else{
$ids_arr = preg_split('[,]',$compareValue);} 
	foreach($tlist as $row)
	{
		
		if($row->upid==$topId)
		{
			
			for($i=0;$i<count($ids_arr);$i++)
			{
				if ($row->tid==$ids_arr[$i]){
					$selectedStr=" checked=checked";
					break;
					}
					else
					{
					$selectedStr="";
					}
			}
			
			echo "<input name=v_type_extra[] type=checkbox value=".$row->tid." ".$selectedStr.">".$row->tname."&nbsp;&nbsp;";
			makeTypeOptionSelected_Multiple($row->tid,$separateStr,$span,$compareValue,$tptype);
			
		}
	}
	if (!empty($span)){$span=substr($span,(strlen($span)-strlen($separateStr)));}
	
}


function makeTypeOptionSelected_Jq($topId,$separateStr,$span="",$compareValue,$tptype=0)
{
	$tlist=getjqTypeListsOnCache($tptype);
	if ($topId!=0){$span.=$separateStr;}else{$span="";}
	if($compareValue==""){
$ids_arr="";}else{
$ids_arr = preg_split('[,]',$compareValue);}  
	foreach($tlist as $row)
	{
		
		if($row->upid==$topId)
		{
			
			for($i=0;$i<count($ids_arr);$i++)
			{
				if ($row->tname==$ids_arr[$i]){
					$selectedStr=" checked=checked";
					break;
					}
					else
					{
					$selectedStr="";
					}
			}
			
			echo "<input name=v_jqtype_extra[] type=checkbox value=".$row->tname." ".$selectedStr.">".$row->tname."&nbsp;&nbsp;";
			makeTypeOptionSelected_Jq($row->tid,$separateStr,$span,$compareValue,$tptype);
			
		}
	}
	if (!empty($span)){$span=substr($span,(strlen($span)-strlen($separateStr)));}
	
}

function getreferer()
{
	if(isset($_SERVER['HTTP_REFERER']))
	$refurl=$_SERVER['HTTP_REFERER'];
	$url='';
	if(!empty($refurl)){
		$refurlar=explode('/',$refurl);
		$i=count($refurlar)-1;
		$url=$refurlar[$i];
	}
	return $url;
}

function downSinglePic($picUrl,$vid,$vname,$filePath,$infotype)
{
	$spanstr=empty($infotype) ? "" : "<br/>";
	if(empty($picUrl) || substr($picUrl,0,7)!='http://'){
		echo "数据<font color=red>".$vname."</font>的图片路径错误1,请检查图片地址是否有效  ".$spanstr;
		return false;
	}
	$fileext=getFileFormat($filePath);
	$ps=preg_split("/",$picUrl);
	$filename=urldecode($ps[count($ps)-1]);
	if ($fileext!="" && strpos("|.jpg|.gif|.png|.bmp|.jpeg|",strtolower($fileext))>0){
		if(!(strpos($picUrl,".ykimg.com/")>0)){
			if(empty($filename) || strpos($filename,".")==0){
				echo "数据<font color=red>".$vname."</font>的图片路径错误2,请检查图片地址是否有效 ".$spanstr;
				return false;
			}
		}
		$imgStream=getRemoteContent(substr($picUrl,0,strrpos($picUrl,'/')+1).str_replace('+','%20',urlencode($filename)));
		$createStreamFileFlag=createStreamFile($imgStream,$filePath);
		if($createStreamFileFlag){
			$streamLen=strlen($imgStream);
			if($streamLen<2048){
				echo "数据<font color=red>".$vname."</font>的图片下载发生错误5,请检查图片地址是否有效  ".$spanstr;
				return false;
			}else{
				return number_format($streamLen/1024,2);
			}
		}else{
			if(empty($vid)){
				echo "数据<font color=red>".$vname."</font>的图片下载发生错误3,请检查图片地址是否有效  ".$spanstr;
				return false;
			}else{
				echo "数据<font color=red>".$vname."</font>的图片下载发生错误4,id为<font color=red>".$vid."</font>,请检查图片地址是否有效  ".$spanstr;
				return false;
			}
		}
	}else{
		echo "数据<font color=red>".$vname."</font>的图片下载发生错误6,请检查图片地址是否有效  ".$spanstr;
		return false;
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
			echo "数据$v_name上传图片到FTP远程服务器失败!本地地址$picUrl<br>";
			return false;
		}
		$ftp->bye();
		if ($GLOBALS['app_ftpdel']==1){
			unlink( $picpath . $picfile );
		}
	}
	else{
		echo $ftp->ftpStatusDes;return false;
	}
}

function uploadftp2($picUrl)
{
	require_once(sea_INC."/ftp.class.php");
	$ftp = new AppFtp($GLOBALS['app_ftphost'] ,$GLOBALS['app_ftpuser'] ,$GLOBALS['app_ftppass'] , $GLOBALS['app_ftpport'] , $GLOBALS['app_ftpdir']);
	$picpath = dirname($picUrl).'/';
	if( $ftp->ftpStatus == 1){;
		$localfile= sea_ROOT .'/'. $picUrl;
		$remotefile= $GLOBALS['app_ftpdir'].$picUrl;
		$ftp -> mkdirs( $GLOBALS['app_ftpdir'].$picpath );
		$ftpput = $ftp->put($localfile, $remotefile);
		if(!$ftpput){
			return false;
		}
		$ftp->bye();
		if ($GLOBALS['app_ftpdel']==1){
			unlink( sea_ROOT .'/'. $picUrl );
		}
		return true;
	}
	else{
		echo $ftp->ftpStatusDes;return false;
	}
}

function cache_clear($dir) {
  $dh=@opendir($dir);
  while ($file=@readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(is_file($fullpath)) {
          @unlink($fullpath);
      }
    }
  }
  closedir($dh); 
}

function getFolderList($cDir)
{
	$dh = dir($cDir);
	$k=0;
	while($filename=$dh->read())
	{
		if($filename=='.' || $filename=='..' || m_ereg("\.inc",$filename)) continue;
		$filetime = filemtime($cDir.'/'.$filename);
		$f[$k]['filetime'] = isCurrentDay($filetime);
		$f[$k]['filename']=$filename;
		if(!m_ereg("\.",$filename)){
			$f[$k]['fileinfo']="文件夹";
		}else{
			$f[$k]['fileinfo']=getTemplateType($filename);
		}
		if(!m_ereg("\.",$filename)){
			$f[$k]['filesize']=getRealSize(getDirSize($cDir.'/'.$filename));
		}else{
			$f[$k]['filesize']=getRealSize(filesize($cDir.'/'.$filename));
		}
		$f[$k]['fileicon']=viewIcon($filename);
		$f[$k]['filetype']=getFileType($filename);
		$k++;
	}
	return $f;
}

function getFileType($filedir)
{
	if(!m_ereg("\.",$filedir)){
		return "folder";
	}else{
		$filetype=strtolower(getfileextend($filedir));
		$imgFileStr=".jpg|.jpeg|.gif|.bmp|.png";
		$pageFileStr =".html|.htm|.js|.css|.txt";
		if(strpos($imgFileStr,$filetype)>0) return "img";
		if(strpos($pageFileStr,$filetype)>0) return "txt";
	}
}

function viewIcon($filename)
{
	if(!m_ereg("\.",$filename)){
		return "folder";
	}else{
		$fileType=strtolower(getfileextend($filename));
		if($fileType=="js" || $fileType=="css"){
			return $fileType;
		}else{
			if ($fileType=="jpg" || $fileType=="jpeg") return "jpg";
			if ($fileType=="htm" || $fileType=="html" || $fileType=="shtml") return "html";
			if ($fileType=="gif" || $fileType=="png") return "gif";
			return "file";
		}
	}
}

function getfileextend($filename)
{ 
	$extend =explode(".", $filename);
	$va=count($extend)-1;
	return $extend[$va];
}

/*老函数，去除
//获取默认文件说明信息
function GetInfoArray($filename)
{
	$arrs = array();
	$dlist = file($filename);
	foreach($dlist as $d)
	{
		$d = trim($d);
		if($d!='')
		{
			list($dname,$info) = explode(',',$d);
			$arrs[$dname] = $info;
		}
	}
	return $arrs;
}
*/

function getDirSize($dir)
{ 
	$handle = opendir($dir);
	$sizeResult = '';
	while (false!==($FolderOrFile = readdir($handle)))
	{ 
		if($FolderOrFile != "." && $FolderOrFile != "..") 
		{ 
			if(is_dir("$dir/$FolderOrFile"))
			{ 
				$sizeResult += getDirSize("$dir/$FolderOrFile"); 
			}
			else
			{ 
				$sizeResult += filesize("$dir/$FolderOrFile"); 
			}
		}    
	}
	closedir($handle);
	return $sizeResult;
}

// 单位自动转换函数
function getRealSize($size)
{ 
	$kb = 1024;         // Kilobyte
	$mb = 1024 * $kb;   // Megabyte
	$gb = 1024 * $mb;   // Gigabyte
	$tb = 1024 * $gb;   // Terabyte
	if($size == 0){
		return "0 B";
	}
	else if($size < $mb)
	{ 
     	return round($size/$kb,2)." K";
	}
	else if($size < $gb)
	{ 
    	return round($size/$mb,2)." M";
	}
	else if($size < $tb)
	{ 
    	return round($size/$gb,2)." G";
	}
	else
	{ 
     	return round($size/$tb,2)." T";
	}
}

//修改 by 心情
function getTemplateType($filename){
	switch(strtolower($filename)){
		case 'index.html':
			$getTemplateType="首页模版";
			break;
		case "head.html":
			$getTemplateType="模板头文件";
			break;
		case "cascade.html":
			$getTemplateType="筛选页文件";
			break;	
		case "foot.html":
			$getTemplateType="模板尾文件";
			break;
		case "play.html":
			$getTemplateType="播放页模板";
			break;
		case "map.html":
			$getTemplateType="HTML地图页模板";
			break;
		case "search.html":
			$getTemplateType="搜索页模板";
			break;
		case "topic.html":
			$getTemplateType="专题页模板";
			break;
		case "topicindex.html":
			$getTemplateType="专题首页模板";
			break;
		case "comment.html":
			$getTemplateType="评论页模板";
			break;
		case "channel.html":
			$getTemplateType="分类页模板";
			break;
		case "openplay.html":
			$getTemplateType="播放页模板(弹窗模式)";
			break;
		case "content.html":
			$getTemplateType="内容页模板";
			break;
		case "gbook.html":
			$getTemplateType="留言本页面模板";
			break;
		case "login.html":
            $getTemplateType="登陆页面模板";
            break;
        case "news.html":
            $getTemplateType="文章内容页面模板";
            break;
        case "newsindex.html":
            $getTemplateType="文章首页面模板";
            break;
        case "newspage.html":
            $getTemplateType="文章列表页面模板";
            break;
		case "newssearch.html":
			$getTemplateType="文章搜索页面模板";
			break;
		case "reg.html":
			$getTemplateType="会员注册页面模板";
			break;
		case "newsmap.html":
			$getTemplateType="文章地图页面模板";
			break;
		case "newsjs.html":
			$getTemplateType="文章js调用模板";
			break;		
		default:
			if(stristr($filename,'.gif') or stristr($filename,'.jpg') or stristr($filename,'.png')){
				$getTemplateType="图片文件";
				}
			elseif(stristr($filename,'.css')){
				 $getTemplateType="样式文件";
				}
			elseif(stristr($filename,'self')){
				 $getTemplateType="自定义模板";
				}
			elseif(stristr($filename,'http.txt')){
				 $getTemplateType="伪静态配置模板";
				}
			elseif(stristr($filename,'.html') or stristr($filename,'.htm')){
				 $getTemplateType="静态页面文件";
				}
			elseif(stristr($filename,'.js')){
				$getTemplateType="脚本文件";
				}	
			else{
				$getTemplateType="其它文件";
				}
	}
	return $getTemplateType;
}

function viewFoot()
{
	global $dsql,$starttime;
	echo "<div align=center>";
	$starttime = explode(' ', $starttime);
	$endtime = explode(' ', microtime()); 
	echo "</div><div class=\"bottom\"><table width=\"100%\" cellspacing=\"5\"><tr><td align=\"center\">本页面用时".
	($endtime[0]+($endtime[1]-$starttime[1])-$starttime[0])."秒,共执行".$dsql->QueryTimes()."次数据查询</td></tr><tr><td align=\"center\"><a target=\"_blank\" href=\"http://www.seacms.net/\">Powered By Seacms</a></td></tr></table></div>\n</body>\n</html>";
}

function viewHead($str)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<TITLE>海洋影视管理系统</TITLE>
<link href="img/style.css" rel="stylesheet" type="text/css" />
<script src="js/common.js" type="text/javascript"></script>
<script src="js/main.js" type="text/javascript"></script>
</head>
<body>
<?php }?>