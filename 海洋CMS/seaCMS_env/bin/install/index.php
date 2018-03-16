<?php
@set_time_limit(0);
error_reporting(0);
$verMsg = ' V6.x UTF8';
$s_lang = 'utf-8';
$dfDbname = 'seacms';
$errmsg = '';
$insLockfile = dirname(__FILE__).'/install_lock.txt';

define('sea_INC',dirname(__FILE__).'/../include');
define('sea_DATA',dirname(__FILE__).'/../data');
define('sea_ROOT',preg_replace("|[\\\/]install|",'',dirname(__FILE__)));
header("Content-Type: text/html; charset={$s_lang}");

require_once(sea_ROOT.'/install/install.inc.php');
require_once(sea_INC.'/common.func.php');

if(PHP_VERSION < '4.1.0') {
	$_GET = &$HTTP_GET_VARS;
	$_POST = &$HTTP_POST_VARS;
	$_COOKIE = &$HTTP_COOKIE_VARS;
	$_SERVER = &$HTTP_SERVER_VARS;
	$_ENV = &$HTTP_ENV_VARS;
	$_FILES = &$HTTP_POST_FILES;
}
foreach(Array('_GET','_POST','_COOKIE') as $_request)
{
	 foreach($$_request as $_k => $_v) ${$_k} = RunMagicQuotes($_v);
}


if( file_exists(dirname(__FILE__).'/install_lock.txt') )
{
	exit(" 程序已运行安装，如果你确定要重新安装，请先从FTP中删除 install/install_lock.txt！");
}

if(empty($step))
{
	$step = 1;
}

$PHP_SELF = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];

$bbserver = 'http://'.preg_replace("/\:\d+/", '', $_SERVER['HTTP_HOST']).($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '');
$default_ucapi = $bbserver.'/ucenter';
$default_appurl = $bbserver.substr($PHP_SELF, 0, strpos($PHP_SELF, 'install/') - 1);
 
if($step==1)
{
	include('./templates/step-1.html');
	exit();
}

else if($step==2)
{
	 $phpv = phpversion();
	 $sp_os = @getenv('OS');
	 $sp_gd = gdversion();
	 $sp_server = $_SERVER['SERVER_SOFTWARE'];
	 $sp_host = (empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR']);
	 $sp_name = $_SERVER['SERVER_NAME'];
	 $sp_max_execution_time = ini_get('max_execution_time');
	 $sp_allow_reference = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_allow_url_fopen = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_fsockopen = (function_exists('fsockopen')?'<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_iconv = (function_exists('iconv')?'<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_safe_mode = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');
   $sp_gd = ($sp_gd>0 ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_curl = (function_exists('curl_init') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
   $sp_mysql = (function_exists('mysql_connect') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');

   if($sp_mysql=='<font color=red>[×]Off</font>')
   {
   		$sp_mysql_err = true;
   }
   else
   {
   		$sp_mysql_err = false;
   }

   $sp_testdirs = array(
        
        '/',
        '/data',
        '/data/admin',
        '/data/backupdata',
        '/data/cache',
        '/data/mark',
		'/install',
        '/uploads/allimg',
        '/uploads/editor',
        '/uploads/litimg',
		'/js',
		'/js/ads'
        
   );
	 include('./templates/step-2.html');
	 exit();
}
else if($step==3)
{
	@include sea_DATA.'/config.ucenter.php';
  if(!empty($_SERVER['REQUEST_URI']))
  {
  	$scriptName = $_SERVER['REQUEST_URI'];
  }
  else
  {
  	$scriptName = $_SERVER['PHP_SELF'];
  }

  $basepath = m_eregi_replace('install(.*)$','',$scriptName);
  $basepath = ltrim($basepath,'/');
  if(empty($_SERVER['HTTP_HOST']))
  {
  	$baseurl = 'http://'.$_SERVER['HTTP_HOST'];
  }
  else
  {
  	$baseurl = "http://".$_SERVER['SERVER_NAME'];
  }

  $rnd_cookieEncode = chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('A'),ord('Z'))).chr(mt_rand(ord('a'),ord('z'))).mt_rand(1000,9999).chr(mt_rand(ord('A'),ord('Z')));

 	$ucapi = defined('UC_API') && UC_API ? UC_API : $default_ucapi;
  include('./templates/step-3.html');
	exit();
}

else if($step==4)
{
  @include sea_DATA.'/config.ucenter.php';
  $configfile = sea_DATA.'/config.ucenter.php';
  $handle = fopen($configfile,'r');
  $configstr = fread($handle,filesize($configfile));
  $configstr = trim($configstr);
  $configstr = substr($configstr, -2) == '?>' ? substr($configstr, 0, -2) : $configstr;
  fclose($handle);
  $configstr = str_replace("define('INTEG_UC', ".addslashes(INTEG_UC).")", "define('INTEG_UC', ".$inuc.")", $configstr);
  $fp = fopen($configfile,'w');
  flock($fp,3);
  fwrite($fp,$configstr);
  fclose($fp);
  

  $conn = mysql_connect($dbhost,$dbuser,$dbpwd) or die("<script>alert('数据库服务器或登录密码无效，\\n\\n无法连接数据库，请重新设定！');history.go(-1);</script>");

  mysql_query("CREATE DATABASE IF NOT EXISTS `".$dbname."`;",$conn);
	
  my_select_db($conn,$dbname) or die("<script>alert('选择数据库失败，可能是你没权限，请预先创建一个数据库！');history.go(-1);</script>");

  //获得数据库版本信息
  $rs = mysql_query("SELECT VERSION();",$conn);
  $row = mysql_fetch_array($rs);
  $mysqlVersions = explode('.',trim($row[0]));
  $mysqlVersion = $mysqlVersions[0].".".$mysqlVersions[1];

  mysql_query("SET NAMES '$dblang',character_set_client=binary,sql_mode='';",$conn);

  $fp = fopen(dirname(__FILE__)."/common.inc.php","r");
  $configStr1 = fread($fp,filesize(dirname(__FILE__)."/common.inc.php"));
  fclose($fp);

  $fp = fopen(dirname(__FILE__)."/config.cache.inc.php","r");
  $configStr2 = fread($fp,filesize(dirname(__FILE__)."/config.cache.inc.php"));
  fclose($fp);

  //common.inc.php
  $configStr1 = str_replace("~dbhost~",$dbhost,$configStr1);
	$configStr1 = str_replace("~dbname~",$dbname,$configStr1);
	$configStr1 = str_replace("~dbuser~",$dbuser,$configStr1);
	$configStr1 = str_replace("~dbpwd~",$dbpwd,$configStr1);
	$configStr1 = str_replace("~dbprefix~",$dbprefix,$configStr1);
  $configStr1 = str_replace("~dblang~",$dblang,$configStr1);

  @chmod(sea_ROOT.'/data',0777);
  $fp = fopen(sea_ROOT."/data/common.inc.php","w") or die("<script>alert('写入配置失败，请检查../data目录是否可写入！');history.go(-1);</script>");
  fwrite($fp,$configStr1);
  fclose($fp);

	//config.cache.inc.php
	$cmspath = trim(m_ereg_replace('/{1,}','/',$cmspath));
	//if($cmspath!='' && !m_ereg('^/',$cmspath)) $cmspath = '/'.$cmspath;

	if($cmspath=='') $indexUrl = '/';
	else $indexUrl = $cmspath;

	$configStr2 = str_replace("~baseurl~",$baseurl,$configStr2);
	$configStr2 = str_replace("~basepath~",$cmspath,$configStr2);
	$configStr2 = str_replace("~indexurl~",$indexUrl,$configStr2);
	$configStr2 = str_replace("~webname~",$webname,$configStr2);

	$fp = fopen(sea_ROOT.'/data/config.cache.inc.php','w');
  fwrite($fp,$configStr2);
  fclose($fp);

  $fp = fopen(sea_ROOT.'/data/config.cache.bak.php','w');
  fwrite($fp,$configStr2);
  fclose($fp);

  if($mysqlVersion >= 4.1)
  {
  	$sql4tmp = "ENGINE=MyISAM DEFAULT CHARSET=".$dblang;
  }
  
  //创建数据表
  
  $query = '';
  $fp = fopen(dirname(__FILE__).'/seacms.sql','r');
	while(!feof($fp))
	{
		 $line = rtrim(fgets($fp,1024));
		 if(m_ereg(";$",$line))
		 {
			   $query .= $line."\n";
			   $query = str_replace('sea_',$dbprefix,$query);
			   if($mysqlVersion < 4.1)
			   {
			   		$rs = mysql_query($query,$conn);
			   }
			   else
			   {
			   		if(m_eregi('CREATE',$query))
			   		{
			   			$rs = mysql_query(m_eregi_replace('TYPE=MyISAM',$sql4tmp,$query),$conn);
			   		}
			   		else
			   		{
			   			$rs = mysql_query($query,$conn);
			   		}
			   }
			   $query='';
		 }
		 else if(!m_ereg("^(//|--)",$line))
		 {
			   $query .= $line;
		 }
	}
	fclose($fp);	
	//导入默认数据
	$query = '';
	$fp = fopen(dirname(__FILE__).'/seacmsdata.sql','r');
	while(!feof($fp))
	{
		 $line = rtrim(fgets($fp,1024));
		 if(m_ereg(";$",$line))
		 {
			   $query .= $line;
			   $query = str_replace('sea_',$dbprefix,$query);
			   if($mysqlVersion < 4.1) $rs = mysql_query($query,$conn);
			   else $rs = mysql_query(str_replace('#~lang~#',$dblang,$query),$conn);
			   $query='';
		 }
		 else if(!m_ereg("^(//|--)",$line))
		 {
			   $query .= $line;
		 }
	}
	fclose($fp);

	//增加管理员帐号
	$adminquery = "INSERT INTO `{$dbprefix}admin` (name,password,logincount,loginip,logintime,groupid,state) VALUES ('$adminuser', '".substr(md5($adminpwd),5,20)."', 0, '127.0.0.1', '".time()."', 1, 1);";
	mysql_query($adminquery,$conn);
	
	$flinkquery = "INSERT INTO `{$dbprefix}flink` (`id`, `sortrank`, `url`, `webname`, `msg`, `email`, `logo`, `dtime`, `ischeck`) VALUES (NULL, '0', 'http://www.seacms.net', '海洋cms', '', '', '', '1432312055', '1');";
	mysql_query($flinkquery,$conn);

  mysql_close($conn);

  	//锁定安装程序
  	$fp = fopen($insLockfile,'w');
  	fwrite($fp,'ok');
  	fclose($fp);
  	include('./templates/step-5.html');
  	exit();

}

else if($step==10)
{
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	$conn = @mysql_connect($dbhost,$dbuser,$dbpwd);
	if($conn)
	{
	  $rs = my_select_db($conn,$dbname);
	  if(!$rs)
	  {
		   $rs = mysql_query(" CREATE DATABASE `$dbname`; ",$conn);
		   if($rs)
		   {
		  	  mysql_query(" DROP DATABASE `$dbname`; ",$conn);
		  	  echo "<font color='green'>信息正确</font>";
		   }
		   else
		   {
		      echo "<font color='red'>数据库不存在，也没权限创建新的数据库！</font>";
		   }
	  }
	  else
	  {
		    echo "<font color='green'>信息正确</font>";
	  }
	}
	else
	{
		echo "<font color='red'>数据库连接失败！</font>";
	}
	@mysql_close($conn);
	exit();
}

function dfopen($url, $limit = 0, $post = '', $cookie = '', $bysocket = FALSE, $ip = '', $timeout = 15, $block = TRUE) {
	$return = '';
	$matches = parse_url($url);
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'].(isset($matches['query']) && $matches['query'] ? '?'.$matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;

	if($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: '.strlen($post)."\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	}

	if(function_exists('fsockopen')) {
		$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	} elseif (function_exists('pfsockopen')) {
		$fp = @pfsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	} else {
		$fp = false;
	}

	if(!$fp) {
		return '';
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if(!$status['timed_out']) {
			while (!feof($fp)) {
				if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
					break;
				}
			}

			$stop = false;
			while(!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				}
			}
		}
		@fclose($fp);
		return $return;
	}
}

function save_uc_config($config, $file) {

	$success = false;

	list($appauthkey, $appid, $ucdbhost, $ucdbname, $ucdbuser, $ucdbpw, $ucdbcharset, $uctablepre, $uccharset, $ucapi, $ucip) = $config;

	$link = mysql_connect($ucdbhost, $ucdbuser, $ucdbpw, 1);
	$uc_connnect = $link && my_select_db($link,$ucdbname) ? 'mysql' : '';

	$date = gmdate("Y-m-d H:i:s", time() + 3600 * 8);
	$year = date('Y');
	$config = <<<EOT
<?php


define('UC_CONNECT', '$uc_connnect');

define('UC_DBHOST', '$ucdbhost');
define('UC_DBUSER', '$ucdbuser');
define('UC_DBPW', '$ucdbpw');
define('UC_DBNAME', '$ucdbname');
define('UC_DBCHARSET', '$ucdbcharset');
define('UC_DBTABLEPRE', '`$ucdbname`.$uctablepre');
define('UC_DBCONNECT', 0);

define('UC_CHARSET', '$uccharset');
define('UC_KEY', '$appauthkey');
define('UC_API', '$ucapi');
define('UC_APPID', '$appid');
define('UC_IP', '$ucip');
define('UC_PPP', 20);
?>
EOT;

	if($fp = fopen($file, 'w')) {
		fwrite($fp, $config);
		fclose($fp);
		$success = true;
	}
	return $success;
}
?>
