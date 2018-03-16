<?php
	require("inc/conn.php");
	$action = be("all","action");
	$db;
	if(file_exists('inc/install.lock')){
		echo '重新安装程序,请先删除inc/install.lock文件'; exit;
	}
	
	switch($action)
	{
		case "ckdb": ckdb();break;
		case "a": show_header(); stepA(); show_footer();break;
		case "b": show_header(); stepB(); show_footer();break;
		case "c": show_header(); stepC(); show_footer();break;
		case "d": show_header(); stepD(); show_footer();break;
		default : show_header(); main(); show_footer();break;
	}
	dispseObj();
	
	function getcon($varName)
	{
		switch($res = get_cfg_var($varName))
		{
		case 0:
			return "NO";
			break;
		case 1:
			return "YES";
			break;
		default: 
			return $res;
			break;
		}
	}
	function isExistTable($tableName,$dbname)
	{
		global $db;
		$dbarr = array();
		$rs = $db->query("SHOW TABLES ");
		while ($row = $db ->fetch_array($rs)){
			$dbarr[] = $row["Tables_in_".$dbname];
		}
		if(in_array($tableName,$dbarr)){
			return true;
		}
		else {
			return false;
		}
	}
	function ckdb()
	{
		$server=be("get","server");
		$dbname=be("get","db");
		$id=be("get","id");
		$pwd=be("get","pwd");
		$lnk=mysql_connect($server,$id,$pwd);
		if(!$lnk){
			die('servererror');
		}
		else{
			$rs = @mysql_select_db($dbname,$lnk);
			if(!$rs){
				$rs = @mysql_query(" CREATE DATABASE `$dbname`; ",$lnk);
				if(!$rs)
			    {
			    	die('dberror');
			    }
			}
		}
		@mysql_close($lnk);
		die("ok");
	}
	
	function show_header()
	{
		echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>苹果CMS安装向导</title>
<link rel="stylesheet" href="images/install/style.css" type="text/css" media="all" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
	function showmessage(message) {
		document.getElementById('notice').innerHTML += message + '<br />';
	}
</script>
<meta content="Comsenz Inc." name="Copyright" />
</head>
<div class="container">
	<div class="header">
		<h1>苹果CMS 安装向导</h1>
		<span>8x.UTF8版</span>
EOT;
	}
	
	function show_footer()
	{
	echo <<<EOT
		<div class="footer">&copy;2008 - 2014 <a href="http://www.maccms.com/">苹果CMS</a> Inc.</div>
	</div>
</div>
</body>
</html>
EOT;
	}
	
	function iswriteable($file)
	{
		$res=false;
		if(is_dir($file)){
			$dir=$file;
			if($fp = @fopen("$dir/index.htm", 'w')){
				@fclose($fp);
				//@unlink("$dir/index.htm");
				$res = true;
			}
			else{
				$res = false;
			}
		}
		else
		{
			if($fp = @fopen($file, 'a+')){
				@fclose($fp);
				$res = true;
			}
			else{
				$res = false;
			}
		}
		return $res;
	}
	
	
	function show_step($n,$t,$c)
	{
	$laststep = 4;
	$stepclass = array();
	for($i = 1; $i <= $laststep; $i++) {
		$stepclass[$i] = $i == $n ? 'current' : ($i < $n ? '' : 'unactivated');
	}
	$stepclass[$laststep] .= ' last';
	echo <<<EOT
	<div class="setup step{$n}">
		<h2>$t</h2>
		<p>$c</p>
	</div>
	<div class="stepstat">
		<ul>
			<li class="$stepclass[1]">检查安装环境</li>
			<li class="$stepclass[2]">设置运行环境</li>
			<li class="$stepclass[3]">创建数据库</li>
			<li class="$stepclass[4]">安装</li>
		</ul>
		<div class="stepstatbg stepstat1"></div>
	</div>
</div>
<div class="main">
EOT;
	}
	
	function main()
	{
		echo <<<EOT
</div>
<div class="main" style="margin-top:-123px;">
	<div class="licenseblock">
	请您在使用(苹果MacCMS)前仔细阅读如下条款。包括免除或者限制作者责任的免责条款及对用户的权利限制。您的安装使用行为将视为对本《用户许可协议》的接受，并同意接受本《用户许可协议》各项条款的约束。 <br /><br />
				一、安装和使用： <br />苹果MacCMS是免费和开源提供给您使用的，您可安装无限制数量副本。 您必须保证在不进行非法活动，不违反国家相关政策法规的前提下使用本软件。 <br /><br />
二：郑重声明： <br />
1、任何个人或组织不得在未经授权的情况下删除、修改、拷贝本软件及其他副本上一切关于版权的信息。 <br />
2、苹果工作室保留此软件的法律追究权利。
<br /><br />
				三、免责声明：  <br />
				本软件并无附带任何形式的明示的或暗示的保证，包括任何关于本软件的适用性, 无侵犯知识产权或适合作某一特定用途的保证。  <br />
				在任何情况下，对于因使用本软件或无法使用本软件而导致的任何损害赔偿，作者均无须承担法律责任。作者不保证本软件所包含的资料,文字、图形、链接或其它事项的准确性或完整性。作者可随时更改本软件，无须另作通知。  <br />
				所有由用户自己制作、下载、使用的第三方信息数据和插件所引起的一切版权问题或纠纷，本软件概不承担任何责任。<br /><br />
	<strong>版权所有 (c) 2008-2013，苹果MacCMS,
	  保留所有权利</strong>。 
	</div>
	<div class="btnbox marginbot">
		<form method="get" autocomplete="off" action="install.php">
		<input type="hidden" name="action" value="a">
		<input type="submit" name="submit" value="我同意" style="padding: 2px">&nbsp;
		<input type="button" name="exit" value="我不同意" style="padding: 2px" onclick="javascript: window.close(); return false;">
		</form>
	</div>
EOT;
	}

function stepA()
{
	show_step(1,"开始安装","环境以及文件目录权限检查");
	$os = PHP_OS;
	$pv = PHP_VERSION;
	$up = getcon("upload_max_filesize");
	$cj1 = getcon("allow_url_fopen");
	
	echo <<<EOT
<div class="main"><h2 class="title">环境检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;">
<tr>
	<th>项目</th>
	<th class="padleft">所需配置</th>
	<th class="padleft">最佳配置</th>
	<th class="padleft">当前服务器</th>
</tr>
<tr>
<td>操作系统</td>
<td class="padleft">不限制</td>
<td class="padleft">类Unix</td>
<td class="w pdleft1">$os</td>
</tr>
<tr>
<td>PHP 版本</td>
<td class="padleft">4.4</td>
<td class="padleft">5.0</td>
<td class="w pdleft1">$pv</td>
</tr>
<tr>
<td>附件上传</td>
<td class="padleft">不限制</td>
<td class="padleft">2M</td>
<td class="w pdleft1">$up</td>
</tr>
<tr>
<td>远程访问</td>
<td class="padleft">allow_url_fopen</td>
<td class="padleft">开启</td>
<td class="w pdleft1">$cj1</td>
</tr>
</table>
<h2 class="title">目录、文件权限检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
	<tr>
	<th>目录文件</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;
	$arr = array("inc/config/config.php","inc/config/cache.php","inc/config/config.collect.bind.php","inc/config/license.php","inc/config/timmingset.xml","inc/config/voddown.xml","inc/config/vodplay.xml","inc/config/vodserver.xml","inc/config/pse_artrnd.txt","inc/config/pse_artsyn.txt","inc/config/pse_vodrnd.txt","inc/config/pse_vodsyn.txt","cache/","cache/break/","cache/export/","upload/","upload/art/","upload/arttopic/","upload/vod/","upload/vodthumb/","upload/vodtopic/","upload/playdata/","upload/downdata/","js/playerconfig.js","admin/bak/");
	foreach($arr as $f){
		$st="可写";
		$cs="w";
		
		$status = iswriteable($f);
		
		
		if(!$status){
			$st="不可写";
			$cs="nw";
		}
		
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">可写</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);
	echo <<<EOT
</table>
<h2 class="title">函数依赖性检查</h2>
<table class="tb" style="margin:20px 0 20px 55px;width:90%;">
<tr>
	<th>函数名称</th>
	<th class="padleft">所需状态</th>
	<th class="padleft">当前状态</th>
</tr>
EOT;
	
	$arr=array("mysql_connect","curl_init","curl_exec","mb_convert_encoding","dom_import_simplexml");
	foreach($arr as $f){
		$st="支持";
		$cs="w";
		if(!function_exists($f)){
			$st="不支持";
			$cs="nw";
		}
		echo '<tr><td>'.$f.'</td><td class="w pdleft1">支持</td><td class="'.$cs.' pdleft1">'.$st.'</td></tr>';
	}
	unset($arr);
	
	echo <<<EOT
</table>
</div>
<form method="get" autocomplete="off" action="install.php">
<input type="hidden" name="action" value="b" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="submit" value="下一步">
</div>
</form>
EOT;
}

function stepB()
{
	show_step(2,"安装配置","网站默认配置信息");
	
	$strpath = $_SERVER["SCRIPT_NAME"];
	$strpath = substring($strpath, strripos($strpath, "/")+1);
?>
<script language="javascript">
		$(function(){
			$("#btnNext").click(function(){
				if($("#site_url").val()==""){
					alert("网站域名不能为空");
					$("#site_url").focus();
					return;
				}
				if($("#site_installdir").val()==""){
					alert("网站安装目录不能为空");
					$("#site_installdir").focus();
					return;
				}
				if($("#site_name").val()==""){
					alert("网站名称不能为空");
					$("#site_name").focus();
					return;
				}
				if($("#m_name").val()==""){
					alert("帐号不能为空");
					$("#m_name").focus();
					return;
				}
				if($("#m_password1").val()==""){
					alert("密码不能为空");
					$("#m_password1").focus();
					return;
				}
				if($("#app_safecode").val()==""){
					alert("安全码不能为空");
					$("#app_safecode").focus();
					return;
				}
				if($("#m_password1").val() != $("#m_password2").val()){
					alert("验证密码不同");
					$("#m_password2").focus();
					return;
				}
				$("#form2").submit();
			});
		});

function checkdb(){
    	var server=$("#db_server").val();
		var dbname=$("#db_name").val();
		var id=$("#db_user").val();
		var pwd=$("#db_pass").val();
		if(server=="" || dbname=="" || id=="" || pwd==""){
			alert("数据库信息不能为空");return;
		}
		$("#btnNext").attr("disabled","disabled");
    	$.ajax({cache: false, dataType: 'html', type: 'GET', url: 'install.php?action=ckdb&server='+server+'&db='+dbname+'&id='+id+'&pwd='+pwd,
    		success: function(obj){
				if(obj=='ok'){
					$("#checkinfo").html( "<font color=green>&nbsp;&nbsp;连接数据库服务器成功!</font>" );
					$("#btnNext").removeAttr("disabled");
				}
				else if(obj=='dberror'){
					$("#checkinfo").html ("<font color=red>&nbsp;&nbsp;连接数据库服务器成功，但是找不到该数据库，也没有权限创建该数据库!</font>");
				}
				else {
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接数据库服务器失败!</font>");
				}
			},
			complete: function (XMLHttpRequest, textStatus) {
				if( XMLHttpRequest.responseText.length >10){
					$("#checkinfo").html("<font color=red>&nbsp;&nbsp;连接服务器失败!</font>");
				}
			}
		});
    }
</script>
<div class="main"><form name="form2" id="form2" action="install.php?action=b" method="post" onSubmit="">
<div id="form_items_3" ><br /><div class="desc"><b>填写网站配置信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp;网站域名:</th>
	<td><input class="txt" type="text" name="site_url" id="site_url" value="<?php echo $_SERVER["SERVER_NAME"]?>" /></td>
	<td>网站的域名不带http://</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;网站名称:</th>
	<td><input class="txt" type="text" name="site_name" id="site_name" value="苹果CMS" /></td>
	<td>网站的名称</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;安装路径:</th>
	<td><input class="txt" type="text" name="site_installdir" id="site_installdir" value="<?php echo $strpath?>" /></td>
	<td>根目录/，/二级目录/</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;网站关键字:</th>
	<td><input class="txt" type="text" name="site_keywords" id="site_keywords" value="免费在线电影" /></td>
	<td>网站的关键字利于seo优化</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;网站描述:</th>
	<td><input class="txt" type="text" name="site_description" id="site_description" value="提供最新最快的影视资讯和在线播放" /></td>
	<td>网站的描述信息利于seo优化</td>
	</tr>
	</table>
	<div class="desc"><b>填写数据库信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp;数据库类型:</th>
	<td><select name="db_type" id="db_type" ><option value="mysql">mysql数据库</option></select></td>
	<td>网站使用数据库的类型</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;表前缀:</th>
	<td><input class="txt" type="text" name="db_tablepre" id="db_tablepre" value="mac_" /></td>
	<td>数据库表名前缀</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库服务器:</th>
	<td><input class="txt" type="text" name="db_server" id="db_server" value="127.0.0.1" /></td>
	<td>数据库服务器地址, 一般为 localhost</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库名称:</th>
	<td><input class="txt" type="text" name="db_name" id="db_name" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库用户名:</th>
	<td><input class="txt" type="text" name="db_user" id="db_user" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;数据库密码:</th>
	<td><input class="txt" type="text" name="db_pass" id="db_pass" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;测试连接:</th>
	<td><strong><a onclick="checkdb()" style="cursor:pointer;"><font color="red">>>>MYSQL连接测试</font></a></strong></td>
	<td>测试可用后下一步按钮可用</td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp;</th>
	<td><span id="checkinfo"></span></td>
	<td></td>
	</tr>
	</table> 
	<div class="desc"><b>填写管理员信息</b></div>
	<table class="tb2">
	<tr><th class="tbopt" align="left">&nbsp; 管理员账号:</th>
	<td><input class="txt" type="text" name="m_name" id="m_name" value="admin" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 管理员密码:</th>
	<td><input class="txt" type="password" name="m_password1" id="m_password1" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 确认密码:</th>
	<td><input class="txt" type="password" name="m_password2" id="m_password2" value="" /></td>
	<td></td>
	</tr>
	<tr><th class="tbopt" align="left">&nbsp; 安全码:</th>
	<td><input class="txt" type="password" name="app_safecode" id="app_safecode" value="" /></td>
	<td></td>
	</tr>
	</table>
	</div>
	<table class="tb2"><tr><th class="tbopt" align="left">&nbsp;</th><td>
<input type="hidden" name="action" value="c" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="button" id="btnNext" value="下一步" disabled></td><td></td></tr>
</table>
</form>
<iframe src="http://www.maccms.com/tongji.html?8x-php" MARGINWIDTH="0" MARGINHEIGHT="0" HSPACE="0" VSPACE="0" FRAMEBORDER="0" SCROLLING="no" width="0" height="0"></iframe>
<?php
}

function stepC()
{
	global $db;
	$site_url = be("post","site_url");
	$site_name = be("post","site_name");
	$site_installdir = be("post","site_installdir");
	$site_keywords = be("post","site_keywords");
	$site_description = be("post","site_description");
	$db_type = be("post","db_type");
	$db_path = "inc/" & be("post","db_path");
	$db_server = be("post","db_server");
	$db_name = be("post","db_name");
	$db_user = be("post","db_user");
	$db_pass = be("post","db_pass");
	$db_tablepre = be("post","db_tablepre");
	$m_name = be("post","m_name");
	$m_password1 = be("post","m_password1");
	$m_password2 = be("post","m_password2");
	$app_safecode = be("post","app_safecode");
	
	show_step(3,"安装数据库","正在执行数据库安装写入配置文件");
	
echo <<<EOT
	<div class="main"> 
	<div class="btnbox"><div id="notice"></div></div>
	<div class="btnbox margintop marginbot"><form method="get" autocomplete="off" action="install.php">
	<table class="tb2"><tr><th class="tbopt" align="left">&nbsp;</th><td>
<input type="hidden" name="action" value="d" /><div class="btnbox marginbot"><input type="button" onclick="history.back();" value="上一步"><input type="submit" value="下一步"></td><td></td></tr></table></form></div>
EOT;
	
	$config = $GLOBALS['MAC'];
	
	$config['db']['server'] = $db_server;
	$config['db']['name'] = $db_name;
	$config['db']['user'] = $db_user;
	$config['db']['pass'] = $db_pass;
	$config['db']['tablepre'] = $db_tablepre;
	$GLOBALS['MAC']['db']['tablepre'] = $db_tablepre;
	
	$config['app']['safecode'] = $app_safecode;
	
	$config['site']['url'] = $site_url;
	$config['site']['name'] = $site_name;
	$config['site']['installdir'] = $site_installdir;
	$config['site']['keywords'] = $site_keywords;
	$config['site']['description'] = $site_description;
	
	$configstr = '<?php'. chr(10) .'$MAC = '.var_export($config, true).';'. chr(10) .'?>';
	fwrite(fopen("inc/config/config.php","wb"),$configstr);
	echo '<script type="text/javascript">showmessage(\'写入网站配置文件... 成功  \');</script>';
	
	error_reporting(E_NOTICE );
	$dbck=false;
	
	$lnk=@mysql_connect($db_server,$db_user,$db_pass);
	if(!$lnk){
		echo '<script type="text/javascript">showmessage(\'数据库设置出错：mysql请检查数据库连接信息... \');</script>';
	}
	else{
		if(!@mysql_select_db($db_name,$lnk)){
			echo '<script type="text/javascript">showmessage(\'数据库服务器连接成功，没有找到【 '.$db_name.' 】数据... \');</script>';
		}
		else{
			$dbck=true;
		} 
	}
	error_reporting(7 );
	
	if ($dbck){
	
	$db = new AppDb($db_server,$db_user,$db_pass,$db_name);
	
	echo '<script type="text/javascript">showmessage(\'开始创建数据库结构... \');</script>';
	
	if(!isExistTable("".$db_tablepre."art",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."art` (
  `a_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_name` varchar(255) NOT NULL,
  `a_subname` varchar(255) NOT NULL,
  `a_enname` varchar(255) NOT NULL,
  `a_letter` char(1) NOT NULL,
  `a_color` char(6) NOT NULL,
  `a_from` varchar(32) NOT NULL,
  `a_author` varchar(32) NOT NULL,
  `a_tag` varchar(64) NOT NULL,
  `a_pic` varchar(255) NOT NULL,
  `a_type` smallint(6) NOT NULL DEFAULT '0',
  `a_topic` varchar(255) NOT NULL,
  `a_level` tinyint(1) NOT NULL DEFAULT '0',
  `a_hide` tinyint(1) NOT NULL DEFAULT '0',
  `a_lock` tinyint(1) NOT NULL DEFAULT '0',
  `a_up` mediumint(8) NOT NULL DEFAULT '0',
  `a_down` mediumint(8) NOT NULL DEFAULT '0',
  `a_hits` mediumint(8) NOT NULL DEFAULT '0',
  `a_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_addtime` int(10) NOT NULL,
  `a_time` int(10) NOT NULL,
  `a_hitstime` int(10) NOT NULL,
  `a_maketime` int(10) NOT NULL,
  `a_remarks` varchar(255) NOT NULL,
  `a_content` mediumtext NOT NULL,
  PRIMARY KEY (`a_id`),
  KEY `a_type` (`a_type`),
  KEY `a_level` (`a_level`),
  KEY `a_hits` (`a_hits`),
  KEY `a_dayhits` (`a_dayhits`),
  KEY `a_weekhits` (`a_weekhits`),
  KEY `a_monthhits` (`a_monthhits`),
  KEY `a_addtime` (`a_addtime`),
  KEY `a_time` (`a_time`),
  KEY `a_maketime` (`a_maketime`),
  KEY `a_hide` (`a_hide`),
  KEY `a_letter` (`a_letter`),
  KEY `a_down` (`a_down`),
  KEY `a_up` (`a_up`),
  KEY `a_tag` (`a_tag`),
  KEY `a_name` (`a_name`),
  KEY `a_enname` (`a_enname`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'art... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."art_relation",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."art_relation` (
  `r_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `r_type` tinyint(1) NOT NULL DEFAULT '0',
  `r_a` mediumint(8) NOT NULL DEFAULT '0',
  `r_b` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `r_type` (`r_type`),
  KEY `r_a` (`r_a`),
  KEY `r_b` (`r_b`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'art_relation... \');</script>';
	}
	
	
	if(!isExistTable("".$db_tablepre."art_topic",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."art_topic` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_tpl` varchar(128) NOT NULL,
  `t_pic` varchar(255) NOT NULL,
  `t_content` varchar(255) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_level` tinyint(1) NOT NULL DEFAULT '0',
  `t_up` mediumint(8) NOT NULL DEFAULT '0',
  `t_down` mediumint(8) NOT NULL DEFAULT '0',
  `t_score` decimal(3,1) NOT NULL,
  `t_scoreall` mediumint(8) NOT NULL,
  `t_scorenum` smallint(6) NOT NULL,
  `t_hits` mediumint(8) NOT NULL DEFAULT '0',
  `t_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_addtime` int(10) NOT NULL,
  `t_time` int(10) NOT NULL,
  `t_hitstime` int(10) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`),
  KEY `t_level` (`t_level`),
  KEY `t_up` (`t_up`),
  KEY `t_down` (`t_down`),
  KEY `t_score` (`t_score`),
  KEY `t_scoreall` (`t_scoreall`),
  KEY `t_scorenum` (`t_scorenum`),
  KEY `t_hits` (`t_hits`),
  KEY `t_dayhits` (`t_dayhits`),
  KEY `t_weekhits` (`t_weekhits`),
  KEY `t_monthhits` (`t_monthhits`),
  KEY `t_addtime` (`t_addtime`),
  KEY `t_time` (`t_time`),
  KEY `t_hitstime` (`t_hitstime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'art_topic... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."art_type",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."art_type` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_pid` smallint(6) NOT NULL DEFAULT '0',
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_tpl` varchar(64) NOT NULL,
  `t_tpl_list` varchar(64) NOT NULL,
  `t_tpl_art` varchar(64) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_union` varchar(255) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_pid` (`t_pid`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'art_type... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."comment",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."comment` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_type` int(11) DEFAULT '0',
  `c_vid` int(11) DEFAULT '0',
  `c_rid` int(11) DEFAULT '0',
  `c_hide` tinyint(1) DEFAULT '0',
  `c_name` varchar(64) NOT NULL,
  `c_ip` varchar(32) NOT NULL,
  `c_content` varchar(128) NOT NULL,
  `c_time` int(10) NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_vid` (`c_vid`),
  KEY `c_type` (`c_type`),
  KEY `c_rid` (`c_rid`),
  KEY `c_time` (`c_time`),
  KEY `c_hide` (`c_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'comment... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."gbook",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."gbook` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_vid` int(11) DEFAULT '0',
  `g_hide` tinyint(1) DEFAULT '0',
  `g_sort` smallint(6) NOT NULL DEFAULT '0',
  `g_name` varchar(64) NOT NULL,
  `g_content` varchar(255) NOT NULL,
  `g_reply` varchar(255) NOT NULL,
  `g_ip` int(11) NOT NULL,
  `g_time` int(10) NOT NULL,
  `g_replytime` int(10) NOT NULL,
  PRIMARY KEY (`g_id`),
  KEY `g_vid` (`g_vid`),
  KEY `g_time` (`g_time`),
  KEY `g_hide` (`g_hide`),
  KEY `g_sort` (`g_sort`),
  KEY `g_replytime` (`g_replytime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'gbook... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."link",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."link` (
  `l_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `l_name` varchar(64) NOT NULL,
  `l_url` varchar(255) NOT NULL,
  `l_logo` varchar(255) NOT NULL,
  `l_type` tinyint(1) NOT NULL DEFAULT '0',
  `l_sort` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`l_id`),
  KEY `l_sort` (`l_sort`),
  KEY `l_type` (`l_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'link... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."manager",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."manager` (
  `m_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `m_name` varchar(32) NOT NULL,
  `m_password` varchar(32) NOT NULL,
  `m_levels` varchar(32) NOT NULL,
  `m_random` varchar(32) NOT NULL,
  `m_status` tinyint(1) NOT NULL DEFAULT '0',
  `m_logintime` int(10) NOT NULL,
  `m_loginip` int(10) NOT NULL,
  `m_loginnum` smallint(6) NOT NULL,
  PRIMARY KEY (`m_id`),
  KEY `m_status` (`m_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'manager... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."user",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."user` (
  `u_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `u_qid` varchar(32) NOT NULL,
  `u_name` varchar(32) NOT NULL,
  `u_password` varchar(32) NOT NULL,
  `u_qq` varchar(16) NOT NULL,
  `u_email` varchar(32) NOT NULL,
  `u_phone` varchar(16) NOT NULL,
  `u_status` tinyint(1) NOT NULL DEFAULT '0',
  `u_flag` tinyint(1) NOT NULL DEFAULT '0',
  `u_question` varchar(255) NOT NULL,
  `u_answer` varchar(255) NOT NULL,
  `u_group` smallint(6) NOT NULL DEFAULT '0',
  `u_points` smallint(6) NOT NULL DEFAULT '0',
  `u_regtime` int(11) NOT NULL,
  `u_logintime` int(11) NOT NULL,
  `u_loginnum` smallint(6) NOT NULL DEFAULT '0',
  `u_extend` smallint(6) NOT NULL DEFAULT '0',
  `u_loginip` int(11) NOT NULL,
  `u_random` varchar(32) NOT NULL,
  `u_fav` text NOT NULL,
  `u_plays` text NOT NULL,
  `u_downs` text NOT NULL,
  `u_start` int(11) NOT NULL,
  `u_end` int(11) NOT NULL,
  PRIMARY KEY (`u_id`),
  KEY `u_group` (`u_group`),
  KEY `u_status` (`u_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'user... \');</script>';
	}
	
	
	if(!isExistTable("".$db_tablepre."user_card",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."user_card` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_number` varchar(16) NOT NULL,
  `c_pass` varchar(8) NOT NULL,
  `c_money` smallint(11) NOT NULL DEFAULT '0',
  `c_point` smallint(11) NOT NULL DEFAULT '0',
  `c_used` tinyint(1) NOT NULL DEFAULT '0',
  `c_sale` tinyint(1) NOT NULL DEFAULT '0',
  `c_user` smallint(6) NOT NULL DEFAULT '0',
  `c_addtime` int(11) NOT NULL,
  `c_usetime` int(11) NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_used` (`c_used`),
  KEY `c_sale` (`c_sale`),
  KEY `c_user` (`c_user`),
  KEY `c_addtime` (`c_addtime`),
  KEY `c_usetime` (`c_usetime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'user_card... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."user_group",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."user_group` (
  `ug_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `ug_name` varchar(32) NOT NULL,
  `ug_type` varchar(255) NOT NULL,
  `ug_popedom` varchar(32) NOT NULL,
  `ug_upgrade` smallint(6) NOT NULL DEFAULT '0',
  `ug_popvalue` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ug_id`),
  KEY `ug_upgrade` (`ug_upgrade`),
  KEY `ug_popvalue` (`ug_popvalue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'user_group... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."user_pay",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."user_pay` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_order` int(11) NOT NULL DEFAULT '0',
  `p_uid` mediumint(8) NOT NULL DEFAULT '0',
  `p_price` smallint(6) NOT NULL DEFAULT '0',
  `p_time` int(11) NOT NULL DEFAULT '0',
  `p_point` smallint(6) NOT NULL DEFAULT '0',
  `p_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`),
  KEY `p_order` (`p_order`),
  KEY `p_uid` (`p_uid`),
  KEY `p_status` (`p_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'user_pay... \');</script>';
	}
	
	
	if(!isExistTable("".$db_tablepre."user_visit",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."user_visit` (
  `uv_id` int(11) NOT NULL AUTO_INCREMENT,
  `uv_uid` int(11) DEFAULT '0',
  `uv_ip` int(11) NOT NULL,
  `uv_ly` varchar(128) NOT NULL,
  `uv_time` int(10) NOT NULL,
  PRIMARY KEY (`uv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'user_visit... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."vod",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."vod` (
  `d_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `d_name` varchar(255) NOT NULL,
  `d_subname` varchar(255) NOT NULL,
  `d_enname` varchar(255) NOT NULL,
  `d_letter` char(1) NOT NULL,
  `d_color` char(6) NOT NULL,
  `d_pic` varchar(255) NOT NULL,
  `d_picthumb` varchar(255) NOT NULL,
  `d_picslide` varchar(255) NOT NULL,
  `d_starring` varchar(255) NOT NULL,
  `d_directed` varchar(255) NOT NULL,
  `d_tag` varchar(64) NOT NULL,
  `d_remarks` varchar(64) NOT NULL,
  `d_area` varchar(16) NOT NULL,
  `d_lang` varchar(16) NOT NULL,
  `d_year` smallint(4) NOT NULL,
  `d_type` smallint(6) NOT NULL DEFAULT '0',
  `d_type_expand` varchar(255) NOT NULL,
  `d_class` varchar(255) NOT NULL,
  `d_topic` varchar(255) NOT NULL DEFAULT '0',
  `d_hide` tinyint(1) NOT NULL DEFAULT '0',
  `d_lock` tinyint(1) NOT NULL,
  `d_state` int(8) NOT NULL DEFAULT '0',
  `d_level` tinyint(1) NOT NULL DEFAULT '0',
  `d_usergroup` smallint(6) NOT NULL DEFAULT '0',
  `d_stint` smallint(6) NOT NULL DEFAULT '0',
  `d_stintdown` smallint(6) NOT NULL DEFAULT '0',
  `d_hits` mediumint(8) NOT NULL DEFAULT '0',
  `d_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_duration` smallint(6) NOT NULL DEFAULT '0',
  `d_up` mediumint(8) NOT NULL DEFAULT '0',
  `d_down` mediumint(8) NOT NULL DEFAULT '0',
  `d_score` decimal(3,1) NOT NULL DEFAULT '0.0',
  `d_scoreall` mediumint(8) NOT NULL,
  `d_scorenum` smallint(6) NOT NULL DEFAULT '0',
  `d_addtime` int(10) NOT NULL,
  `d_time` int(10) NOT NULL,
  `d_hitstime` int(10) NOT NULL,
  `d_maketime` int(10) NOT NULL,
  `d_content` text NOT NULL,
  `d_playfrom` varchar(255) NOT NULL,
  `d_playserver` varchar(255) NOT NULL,
  `d_playnote` varchar(255) NOT NULL,
  `d_playurl` mediumtext NOT NULL,
  `d_downfrom` varchar(255) NOT NULL,
  `d_downserver` varchar(255) NOT NULL,
  `d_downnote` varchar(255) NOT NULL,
  `d_downurl` mediumtext NOT NULL,
  PRIMARY KEY (`d_id`),
  KEY `d_type` (`d_type`),
  KEY `d_state` (`d_state`),
  KEY `d_level` (`d_level`),
  KEY `d_hits` (`d_hits`),
  KEY `d_dayhits` (`d_dayhits`),
  KEY `d_weekhits` (`d_weekhits`),
  KEY `d_monthhits` (`d_monthhits`),
  KEY `d_stint` (`d_stint`),
  KEY `d_stintdown` (`d_stintdown`),
  KEY `d_hide` (`d_hide`),
  KEY `d_usergroup` (`d_usergroup`),
  KEY `d_score` (`d_score`),
  KEY `d_addtime` (`d_addtime`),
  KEY `d_time` (`d_time`),
  KEY `d_maketime` (`d_maketime`),
  KEY `d_topic` (`d_topic`),
  KEY `d_letter` (`d_letter`),
  KEY `d_name` (`d_name`),
  KEY `d_enname` (`d_enname`),
  KEY `d_year` (`d_year`),
  KEY `d_area` (`d_area`),
  KEY `d_language` (`d_lang`),
  KEY `d_starring` (`d_starring`),
  KEY `d_directed` (`d_directed`),
  KEY `d_tag` (`d_tag`),
  KEY `d_type_expand` (`d_type_expand`),
  KEY `d_class` (`d_class`),
  KEY `d_lock` (`d_lock`),
  KEY `d_up` (`d_up`),
  KEY `d_down` (`d_down`),
  KEY `d_scoreall` (`d_scoreall`),
  KEY `d_scorenum` (`d_scorenum`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'vod... \');</script>';
	}
	
	
	if(!isExistTable("".$db_tablepre."vod_class",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."vod_class` (
    `c_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `c_name` varchar(64) NOT NULL,
  `c_pid` smallint(6) NOT NULL DEFAULT '0',
  `c_sort` smallint(6) NOT NULL DEFAULT '0',
  `c_hide` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sort` (`c_sort`),
  KEY `c_pid` (`c_pid`),
  KEY `c_hide` (`c_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'vod_class... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."vod_relation",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."vod_relation` (
  `r_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `r_type` tinyint(1) NOT NULL DEFAULT '0',
  `r_a` mediumint(8) NOT NULL DEFAULT '0',
  `r_b` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `r_type` (`r_type`),
  KEY `r_a` (`r_a`),
  KEY `r_b` (`r_b`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'vod_relation... \');</script>';
	}
	
	
	if(!isExistTable("".$db_tablepre."vod_topic",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."vod_topic` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_tpl` varchar(128) NOT NULL,
  `t_pic` varchar(255) NOT NULL,
  `t_content` varchar(255) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_level` tinyint(1) NOT NULL DEFAULT '0',
  `t_up` mediumint(8) NOT NULL DEFAULT '0',
  `t_down` mediumint(8) NOT NULL DEFAULT '0',
  `t_score` decimal(3,1) NOT NULL,
  `t_scoreall` mediumint(8) NOT NULL,
  `t_scorenum` smallint(6) NOT NULL,
  `t_hits` mediumint(8) NOT NULL DEFAULT '0',
  `t_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_addtime` int(10) NOT NULL,
  `t_time` int(10) NOT NULL,
  `t_hitstime` int(10) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`),
  KEY `t_level` (`t_level`),
  KEY `t_up` (`t_up`),
  KEY `t_down` (`t_down`),
  KEY `t_score` (`t_score`),
  KEY `t_scoreall` (`t_scoreall`),
  KEY `t_scorenum` (`t_scorenum`),
  KEY `t_hits` (`t_hits`),
  KEY `t_dayhits` (`t_dayhits`),
  KEY `t_weekhits` (`t_weekhits`),
  KEY `t_monthhits` (`t_monthhits`),
  KEY `t_addtime` (`t_addtime`),
  KEY `t_time` (`t_time`),
  KEY `t_hitstime` (`t_hitstime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'vod_topic... \');</script>';
	}
	
	if(!isExistTable("".$db_tablepre."vod_type",$db_name)){
	$db->query( "CREATE TABLE `".$db_tablepre."vod_type` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_pid` smallint(6) NOT NULL DEFAULT '0',
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_tpl` varchar(64) NOT NULL,
  `t_tpl_list` varchar(64) NOT NULL,
  `t_tpl_vod` varchar(64) NOT NULL,
  `t_tpl_play` varchar(64) NOT NULL,
  `t_tpl_down` varchar(64) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_union` varchar(255) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_pid` (`t_pid`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	echo '<script type="text/javascript">showmessage(\'创建数据表 '.$db_tablepre.'vod_type... \');</script>';
	}
	
	
	if(!isExistTable("tmptable",$db_name)){
	$db->query( "CREATE TABLE `tmptable` (
  `d_name1` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");
	echo '<script type="text/javascript">showmessage(\'创建数据表 tmptable... \');</script>';
	}
	
	
	echo '<script type="text/javascript">showmessage(\'数据库结构创建完成... \');</script>';
	
	
	$db->query( "insert into ".$db_tablepre."manager(m_id,m_name,m_password,m_status,m_levels) values('1','".$m_name."','".md5($m_password1)."',1,'b,c,d,e,f,g,h,i,j')");
	echo '<script type="text/javascript">showmessage(\'管理员帐号'.$m_name.'初始化成功... \');</script>';
	
	$db->query( "INSERT into ".$db_tablepre."user_group (ug_id,ug_name,ug_type,ug_popedom,ug_upgrade,ug_popvalue) values ('1','普通会员','','',0,1)");
	echo '<script type="text/javascript">showmessage(\'默认会员组初始化完毕... \');</script>';
	
	
	$db->query( "INSERT INTO `".$db_tablepre."vod_type` (`t_id`, `t_name`, `t_enname`, `t_pid`, `t_sort`, `t_hide`, `t_tpl`, `t_tpl_list`, `t_tpl_vod`, `t_tpl_play`, `t_tpl_down`, `t_key`, `t_des`, `t_title`, `t_union`) VALUES
(1, '电影', 'dianying', 0, 1, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(2, '连续剧', 'lianxuju', 0, 2, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(3, '综艺', 'zongyi', 0, 3, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(4, '动漫', 'dongman', 0, 4, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(5, '动作片', 'dongzuopian', 1, 11, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(6, '喜剧片', 'xijupian', 1, 12, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(7, '爱情片', 'aiqingpian', 1, 13, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(8, '科幻片', 'kehuanpian', 1, 14, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(9, '恐怖片', 'kongbupian', 1, 14, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(10, '剧情片', 'juqingpian', 1, 16, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(11, '战争片', 'zhanzhengpian', 1, 17, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(12, '国产剧', 'guochanju', 2, 21, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(13, '港台剧', 'gangtaiju', 2, 22, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(14, '日韩剧', 'rihanju', 2, 23, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', ''),
(15, '欧美剧', 'oumeiju', 2, 24, 0, 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
");


	$db->query( "INSERT INTO `".$db_tablepre."art_type` (`t_id`, `t_name`, `t_enname`, `t_pid`, `t_sort`, `t_hide`, `t_tpl`, `t_tpl_list`, `t_tpl_art`, `t_key`, `t_des`, `t_title`, `t_union`) VALUES
(1, '站内新闻', 'zhanneixinwen', 0, 1, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', ''),
(2, '娱乐动态', 'yuledongtai', 0, 2, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', ''),
(3, '八卦爆料', 'baguabaoliao', 0, 3, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', ''),
(4, '影片资讯', 'yingpianzixun', 0, 4, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', ''),
(5, '明星资讯', 'mingxingzixun', 0, 5, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', ''),
(6, '电视资讯', 'dianshizixun', 0, 6, 0, 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
");
	
	$db->query( "INSERT INTO `".$db_tablepre."vod_class` ( `c_name`, `c_pid`, `c_sort`, `c_hide`) VALUES
( '惊悚', 1, 1, 0),( '悬疑', 1, 2, 0),( '魔幻', 1, 3, 0),( '罪案', 1, 4, 0),( '灾难', 1, 5, 0),( '动画', 1, 6, 0),( '古装', 1, 7, 0),( '青春', 1, 8, 0),( '歌舞', 1, 9, 0),( '文艺', 1, 10, 0),( '生活', 1, 10, 0),( '历史', 1, 10, 0),( '励志', 1, 10, 0),( '预告片', 1, 10, 0),
		
( '言情', 2, 1, 0),( '都市', 2, 2, 0),( '家庭', 2, 3, 0),( '生活', 2, 4, 0),( '偶像', 2, 5, 0),( '喜剧', 2, 6, 0),( '历史', 2, 7, 0),( '古装', 2, 8, 0),
( '武侠', 2, 9, 0),( '刑侦', 2, 10, 0),( '战争', 2, 11, 0),( '神话', 2, 12, 0),( '军旅', 2, 13, 0),( '谍战', 2, 14, 0),( '商战', 2, 15, 0),( '校园', 2, 16, 0),( '穿越', 2, 17, 0),( '悬疑', 2, 18, 0),( '犯罪', 2, 19, 0),( '科幻', 2, 20, 0),( '预告片', 2, 21, 0),

( '脱口秀', 3, 1, 0),( '真人秀', 3, 2, 0),( '选秀', 3, 3, 0),( '情感', 3, 4, 0),( '访谈', 3, 5, 0),( '时尚', 3, 6, 0),( '晚会', 3, 7, 0),( '财经', 3, 8, 0),( '益智', 3, 9, 0),( '音乐', 3, 10, 0),( '游戏', 3, 11, 0),( '职场', 3, 12, 0),( '美食', 3, 13, 0),( '旅游', 3, 14, 0),
	
( '冒险', 4, 1, 0),( '热血', 4, 2, 0),( '搞笑', 4, 3, 0),( '少女', 4, 4, 0),( '推理', 4, 5, 0),( '竞技', 4, 6, 0),( '益智', 4, 7, 0),( '童话', 4, 8, 0),( '经典', 4, 9, 0);
");


	echo '<script type="text/javascript">showmessage(\'数据分类初始化成功... \');</script>';
	updateCacheFile();
	echo '<script type="text/javascript">showmessage(\'数据缓存初始化成功... \');</script>';
	}
	unset($db);
}
 
function stepD()
{
	show_step(4,"安装完毕","稍后进入后台管理页面");
	fwrite(fopen("inc/install.lock","wb"),'');
?>

<div class="main"><div class="desc">5秒后自动跳转到后台管理登录页面...</div>
<script> setTimeout("jump();",5000); function jump(){location.href='admin/index.php';} </script>
<?php
}
?>