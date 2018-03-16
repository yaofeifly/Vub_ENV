<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview();

if(empty($dopost))
{
	$dopost = "";
}
$configfile = sea_DATA.'/config.cache.inc.php';
$publishareatxt=sea_DATA."/admin/publisharea.txt";
$publishyeartxt=sea_DATA."/admin/publishyear.txt";
$publishyuyantxt=sea_DATA."/admin/publishyuyan.txt";
$iplisttxt=sea_DATA."/admin/iplist.txt";
$vertxt=sea_DATA."/admin/verlist.txt";
$m_file = sea_ROOT."/js/play.js";

//保存配置的改动
if($dopost=="save")
{
	if(!m_ereg("^[0-9a-zA-Z_\-]+$",$edit___cfg_df_style) || !m_ereg("^[0-9a-zA-Z_\-]+$",$edit___cfg_df_html) || !m_ereg("^[0-9a-zA-Z_\-]+$",$edit___cfg_ads_dir) || !m_ereg("^[0-9a-zA-Z_\-]+$",$edit___cfg_upload_dir) || !m_ereg("^[0-9a-zA-Z_\-]+$",$edit___cfg_backup_dir)){
		ShowMsg("请检查模板与路径设置里的文件夹路径设置是否含有非法字符！","-1");
		exit;
	}
	if ($edit___cfg_runmode=='2'&&$edit___cfg_automake=='1')
	{
		$content=loadFile(sea_DATA."/admin/RewriteRule.config");
		$content = str_replace("{cmspath}",$edit___cfg_cmspath,$content);
		$content = str_replace("{channelDirName3}",$edit___cfg_channel_name3,$content);
		$content = str_replace("{channelpagename3}",$edit___cfg_channelpage_name3,$content);
		$content = str_replace("{contentdirname3}",$edit___cfg_content_name3,$content);
		$content = str_replace("{contentpagename3}",$edit___cfg_contentpage_name3,$content);
		$content = str_replace("{playdirname3}",$edit___cfg_play_name3,$content);
		$content = str_replace("{channelDirName}",$edit___cfg_channel_name,$content);
		$content = str_replace("{contentDirName}",$edit___cfg_content_name,$content);
		$content = str_replace("{playDirName}",$edit___cfg_play_name,$content);
		$content = str_replace("{topicDirName}",$edit___cfg_album_name,$content);
		$content = str_replace("{topicListName}",$edit___cfg_filesuffix,$content);
		$content = str_replace("{fileSuffix}",$edit___cfg_filesuffix2,$content);
		$content = str_replace("{fileSuffix3}",$edit___cfg_filesuffix3,$content);
		$content = str_replace("{newsName}",$edit___cfg_news_name,$content);
		$content = str_replace("{newsName3}",$edit___cfg_news_name3,$content);
		$content = str_replace("{newspartName}",$edit___cfg_newspart_name,$content);
		$content = str_replace("{newspartName3}",$edit___cfg_newspart_name3,$content);
		$content = str_replace("{articleName}",$edit___cfg_article_name,$content);
		$content = str_replace("{articleName3}",$edit___cfg_article_name3,$content);
		$content = str_replace("{newspartpageName3}",$edit___cfg_newspartpage_name3,$content);
		$content = str_replace("{articlepageName3}",$edit___cfg_articlepage_name3,$content);
		createTextFile($content,sea_ROOT."/httpd.ini");
		$content=loadFile(sea_DATA."/admin/ApacheRule.config");
		$content = str_replace("{cmspath}",$edit___cfg_cmspath,$content);
		$content = str_replace("{channelDirName3}",$edit___cfg_channel_name3,$content);
		$content = str_replace("{channelpagename3}",$edit___cfg_channelpage_name3,$content);
		$content = str_replace("{contentdirname3}",$edit___cfg_content_name3,$content);
		$content = str_replace("{contentpagename3}",$edit___cfg_contentpage_name3,$content);
		$content = str_replace("{playdirname3}",$edit___cfg_play_name3,$content);
		$content = str_replace("{channelDirName}",$edit___cfg_channel_name,$content);
		$content = str_replace("{contentDirName}",$edit___cfg_content_name,$content);
		$content = str_replace("{playDirName}",$edit___cfg_play_name,$content);
		$content = str_replace("{topicDirName}",$edit___cfg_album_name,$content);
		$content = str_replace("{topicListName}",$edit___cfg_filesuffix,$content);
		$content = str_replace("{fileSuffix}",$edit___cfg_filesuffix2,$content);
		$content = str_replace("{fileSuffix3}",$edit___cfg_filesuffix3,$content);
		$content = str_replace("{newsName}",$edit___cfg_news_name,$content);
		$content = str_replace("{newsName3}",$edit___cfg_news_name3,$content);
		$content = str_replace("{newspartName}",$edit___cfg_newspart_name,$content);
		$content = str_replace("{newspartName3}",$edit___cfg_newspart_name3,$content);
		$content = str_replace("{articleName}",$edit___cfg_article_name,$content);
		$content = str_replace("{articleName3}",$edit___cfg_article_name3,$content);
		$content = str_replace("{newspartpageName3}",$edit___cfg_newspartpage_name3,$content);
		$content = str_replace("{articlepageName3}",$edit___cfg_articlepage_name3,$content);
		createTextFile($content,sea_ROOT."/.htaccess");
	}
	foreach($_POST as $k=>$v)
	{
		if(m_ereg("^edit___",$k))
		{
			if(is_array($$k))
				$v = cn_substr(str_replace("'","\'",str_replace("\\","\\\\",stripslashes(implode(',',$$k)))),500); 
			else
				$v = cn_substr(str_replace("'","\'",str_replace("\\","\\\\",stripslashes(${$k}))),500); 
		}
		else
		{
			continue;
		}
		$k = m_ereg_replace("^edit___","",$k);
		$configstr .="\${$k} = '$v';\r\n";
	}
	if(!is_writeable($configfile))
	{
		echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
		exit();
	}

	$moveType=$edit___cfg_runmode;
	$moveType2=$edit___cfg_makemode;
	switch ($moveType) {
		case "1":
			$dir_channel=$edit___cfg_channel_name; if(empty($dir_channel)) exit("栏目页目录名不能为空");
			$dir_content=$edit___cfg_content_name; if(empty($dir_content)) exit("内容页目录名不能为空");
			$dir_play=$edit___cfg_play_name; if(empty($dir_play)) exit("播放页目录名不能为空");
			$dir_topic=$edit___cfg_album_name; if(empty($dir_topic)) exit("专题主目录名不能为空");
			$dir_topicpage=$edit___cfg_filesuffix;if(empty($dir_topicpage)) exit("专题页目录名不能为空");
			$dir_news=$edit___cfg_news_name;if(empty($dir_news)) exit("新闻主目录名不能为空");
			$dir_newspage=$edit___cfg_newspart_name;if(empty($dir_newspage)) exit("栏目页目录名不能为空");
			$dir_article=$edit___cfg_article_name;if(empty($dir_article)) exit("文章页目录名不能为空");
			if (isEqualOther($dir_channel."||".$dir_content."||".$dir_play."||".$dir_topic."||".$dir_topicpage."||".$dir_news."||".$dir_newspage."||".$dir_article)) exit("目录名不能存在雷同");
			moveFolder($cfg_channel_name,$dir_channel);
			moveFolder($cfg_content_name,$dir_content);
			moveFolder($cfg_play_name,$dir_play);
			moveFolder($cfg_album_name,$dir_topic);
			moveFolder($cfg_filesuffix, $dir_topicpage);			
			moveFolder($cfg_news_name, $dir_news);
			moveFolder($cfg_newspart_name, $dir_newspage);
			moveFolder($cfg_article_name, $dir_article);
		break;
		case "0":
			if($moveType2=="dir2"){
				$dir_channel=$edit___cfg_channel_name2; if(empty($dir_channel)) exit("栏目页目录名不能为空");
				$dir_content=$edit___cfg_content_name2; if(empty($dir_content)) exit("内容页目录名不能为空");
				$dir_play=$edit___cfg_play_name2; if(empty($dir_play)) exit("播放页目录名不能为空");
				$dir_topic=$edit___cfg_album_name; if(empty($dir_topic)) exit("专题目录名不能为空");
				$dir_topicpage=$edit___cfg_filesuffix;if(empty($dir_topicpage)) exit("专题页目录名不能为空");
				$dir_news=$edit___cfg_news_name;if(empty($dir_news)) exit("新闻主目录名不能为空");
				$dir_newspage=$edit___cfg_newspart_name;if(empty($dir_newspage)) exit("栏目页目录名不能为空");
				$dir_article=$edit___cfg_article_name;if(empty($dir_article)) exit("文章页目录名不能为空");
				if (isEqualOther($dir_channel."||".$dir_content."||".$dir_play."||".$dir_topic."||".$dir_topicpage."||".$dir_news."||".$dir_newspage."||".$dir_article)) exit("目录名不能存在雷同");
			}
		break;
	}
	if(empty($edit___cfg_df_html)) exit("模板文件所在文件夹不能为空");
	if(empty($edit___cfg_upload_dir)) exit("图片文件夹不能为空");
	if(empty($edit___cfg_ads_dir)) exit("JS广告文件夹不能为空");
	moveFolder('templets/'.$cfg_df_style.'/'.$cfg_df_html,'templets/'.$cfg_df_style.'/'.$edit___cfg_df_html);
	moveFolder($cfg_upload_dir,$edit___cfg_upload_dir);
	moveFolder('js/'.$cfg_ads_dir,'js/'.$edit___cfg_ads_dir);

	$fp = fopen($configfile,'w');
	flock($fp,3);
	fwrite($fp,"<"."?php\r\n");
	fwrite($fp,$configstr);
	fwrite($fp,"?".">");
	fclose($fp);
	$fpp = fopen($m_file,'r');
	$player = fread($fpp,filesize($m_file));
	fclose($fpp);
	$player=preg_replace("/alertwinw='(\d+)';/is","alertwinw='".$edit___cfg_alertwinw."';",$player);
	$player=preg_replace("/alertwinh='(\d+)';/is","alertwinh='".$edit___cfg_alertwinh."';",$player);
	$player=preg_replace("/alertwin='(\d+)';/is","alertwin='".$edit___cfg_isalertwin."';",$player);
	$fpp = fopen($m_file,'w');
	flock($fpp,3);
	fwrite($fpp,$player);
	fclose($fpp);
	$fp = @fopen($iplisttxt,'w');
	flock($fp,3);
	fwrite($fp,$iplist);
	fclose($fp);
	$iplist = @fread($fp,filesize($iplisttxt));
	@fclose($fp);
	$areaarr = explode('|',$publisharea);
	$fp = @fopen($publishareatxt,'w');
	@flock($fp,3);
	foreach($areaarr as $area)
	{
		@fwrite($fp,$area."\r\n");
	}
	@fclose($fp);
	$yeararr = explode('|',$publishyear);
	$fp = @fopen($publishyeartxt,'w');
	@flock($fp,3);
	foreach($yeararr as $year)
	{
		@fwrite($fp,$year."\r\n");
	}
	@fclose($fp);
	$yuyanarr = explode('|',$publishyuyan);
	$fp = @fopen($publishyuyantxt,'w');
	@flock($fp,3);
	foreach($yuyanarr as $yuyan)
	{
		@fwrite($fp,$yuyan."\r\n");
	}
	@fclose($fp);
	$verarr = explode('|',$ver);
	$fp = @fopen($vertxt,'w');
	@flock($fp,3);
	foreach($verarr as $ver)
	{
		@fwrite($fp,$ver."\r\n");
	}
	@fclose($fp);
	ShowMsg("成功更改站点配置！","admin_config.php");
	exit();
}

include(sea_ADMIN.'/templets/admin_config.htm');
exit();

function isEqualOther($str)
{
	$strArray=explode("||",$str);
	$isEqualOther=false;
	foreach($strArray as $v)
	{
		$temarray=explode("|".$v."|","|".$str."|");
		if (count($temarray)>2){
			$isEqualOther=true;
			return $isEqualOther;
		}
	}
	return $isEqualOther;
}
?>