<?php
require_once(dirname(__FILE__)."/config.php");
require_once(sea_INC."/charset.func.php");


CheckPurview();
if(empty($action))
{
	$action = '';
}
global $cfg_ismakeplay,$cfg_alertwinw,$cfg_alertwinh;
$m_file = sea_ROOT."/js/play.js";
$playerKindsfile = sea_DATA."/admin/downKinds.xml";
if($action=="edit")
{
	foreach ($playerbgcolor as $k=>$bgcolor)
	{
	$skinColor.= "|".$bgcolor.",".$playerfontcolor[$k];		
	}
	$skinColor = ltrim($skinColor,"|");
	$fp = fopen($m_file,'r');
	$player = fread($fp,filesize($m_file));
	fclose($fp);
	$player=preg_replace("/playerw='(\d+)';/is","playerw='".$playerwidth."';",$player);
	$player=preg_replace("/playerh='(\d+)';/is","playerh='".$playerheight."';",$player);
	$player=preg_replace("/adsPage=(.*?)\";/is","adsPage=\"".$adbeforeplay."\";",$player);
	$player=preg_replace("/adsTime=(\d+);/is","adsTime=".$adtimebeforeplay.";",$player);
	$player=preg_replace("/skinColor='(.*?)';/is","skinColor='".$skinColor."';",$player);
	$player=preg_replace("/sea_Player_File=(.*?)\";/is","sea_Player_File=\"$playerset\";",$player);
	$player=preg_replace("/autoPlay=(.*?)\";/is","autoPlay=\"$autoPlay\";",$player);
	$player=preg_replace("/logoURL=(.*?)\";/is","logoURL=\"$logourl\";",$player);
	$player=preg_replace("/btnName=(.*?)\";/is","btnName=\"$btnName\";",$player);
	$player=preg_replace("/showFullBtn=(.*?)\";/is","showFullBtn=\"$showFullBtn\";",$player);
	$player=preg_replace("/rehref=(.*?)\";/is","rehref=\"$rehref\";",$player);
	$player=preg_replace("/openMenu=(.*?)\";/is","openMenu=\"$openMenu\";",$player);
	$fp = fopen($m_file,'w');
	flock($fp,3);
	fwrite($fp,$player);
	fclose($fp);
	ShowMsg("成功保存设置!","admin_playerdown.php");
	exit;
}
elseif($action=="save")
{
	if(empty($e_id))
	{
		ShowMsg("请选择要修改的项目","-1");
		exit();
	}
	$xml = simplexml_load_file($playerKindsfile);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if(in_array($i,$e_id)){
			$player['sort']=stripslashes(${'sort'.$e_id[$a]});
			$player['postfix']=stripslashes(${'postfix'.$e_id[$a]});
			$player['flag']=gb2utf8(stripslashes(${'flag'.$e_id[$a]}));
			$player->intro=gb2utf8(stripslashes(${'info'.$e_id[$a]}));
			$a++;
			$xml->asXML($playerKindsfile);
			}
	}
	
	/*  Modify Database */
	
	$sql = "select * from `sea_playdata` ";
	
	
	
	
	
	ShowMsg("成功保存设置!","admin_playerdown.php?action=boardsource");
	exit;
}
elseif($action=="boardsource")
{
	include(sea_ADMIN.'/templets/admin_playerdown.htm');
	exit();
}
elseif($action=="modifysourceban")
{
	$xml = simplexml_load_file($playerKindsfile);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
	$i=0;
	foreach($xml as $player){
		$i++;
		if($i==$id){
			if($player['open']==0)
			$player['open']=1;
			else
			$player['open']=0;
			$xml->asXML($playerKindsfile);
			}
		}	
	header('Location: admin_playerdown.php?action=boardsource');
	exit();
}
elseif($action=="modifysource")
{
	
	$xml = simplexml_load_file($playerKindsfile);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
	$i=0;
	foreach($xml as $player){
		$i++;
		if($i==$id){
			$player['sort']=$sort;
			$player['postfix']=$postfix;
			$player->intro=gb2utf8($info);
			$xml->asXML($playerKindsfile);
			}
	
	}
	echo "<script>alert('修改成功！');</script>";
//	header('Location: admin_playerdown.php?action=boardsource');
	exit();
}
elseif($action=="addnew")
{
	//add new
	$playername=$_POST[playername];
	$info=$_POST[info];
	$order=$_POST[order];
	$trail=$_POST[trail];
	if($playername==''||$trail==''||$order=='')
	{
		ShowMsg("请输入播放器名字，后缀，排序。","-1");
		exit();
	}
	$playername = gb2utf8($playername);
	$info = gb2utf8($info);
	$doc = new DOMDocument();   
	$doc -> formatOutput = true;
 
	$doc->load($playerKindsfile);     
	
	$root = $doc->documentElement;
	$index = $doc->createElement('player');
	$root->appendChild($index);
	
	$open = $doc->createAttribute("open");
	$openvalue = $doc -> createTextNode('1');
	$open-> appendChild($openvalue);
	$index -> appendChild($open);

	$sort = $doc->createAttribute("sort");
	$sortvalue = $doc->createTextNode($order);
	$sort->appendChild($sortvalue);
	$index->appendChild($sort);
	
	$postfix = $doc->createAttribute("postfix");
	$postfixvalue = $doc->createTextNode($trail);
	$postfix->appendChild($postfixvalue);
	$index->appendChild($postfix);
	
	$flag = $doc->createAttribute("flag");
	$flagvalue = $doc->createTextNode($playername);
	$flag->appendChild($flagvalue);
	$index->appendChild($flag);
	
	$des = $doc->createAttribute("des");
	$desvalue = $doc->createTextNode("");
	$des->appendChild($desvalue);
	$index->appendChild($des);
	
	$intro = $doc->createElement("intro");
	$introvalue = $doc->createCDATASection($info);
	$intro->appendChild($introvalue);
	$index->appendChild($intro);	
	
	$doc -> save($playerKindsfile);

	
	
	echo("<script>location.href='admin_playerdown.php?action=boardsource'</script>");
	exit();
}
elseif($action=="delete")
{

	$xml = simplexml_load_file($playerKindsfile);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
	$i=0;
	foreach($xml as $player){
		$i++;
		if($i==$id){
			unset($xml->player[$i-1]); //索引从0开始。
			$xml->asXML($playerKindsfile);
			}
	
	}

	echo("<script>location.href='admin_playerdown.php?action=boardsource'</script>");
	exit();

}
else
{
	$fp = fopen($m_file,'r');
	$player = fread($fp,filesize($m_file));
	fclose($fp);
	$playerWidth=getrulevalue($player,"playerw='","';");
	$playerHeight=getrulevalue($player,"playerh='","';");
	$playerBeforeAdUrl=getrulevalue($player,"adsPage=\"","\";");
	$playerBeforeTime=getrulevalue($player,"adsTime=",";");
	$autoPlay=getrulevalue($player, "autoplay=\"","\";");
	$logourl=getrulevalue($player,"logoURL=\"","\";");
	$btnName=getrulevalue($player,"btnName=\"","\";");
	$showFullBtn=getrulevalue($player,"showFullBtn=\"","\";");
	$rehref=getrulevalue($player,"rehref=\"","\";");
	$playerset=getrulevalue($player,"sea_Player_File=\"","\";");
	$openMenu=getrulevalue($player,"openMenu=\"","\";");
	$skinColor=getrulevalue($player,"skinColor='","';");
	include(sea_ADMIN.'/templets/admin_playerdown.htm');
	exit();
}

function getrulevalue($content,$str1,$str2)
{
	if(!empty($content) && !empty($str1) && !empty($str2)){
		$labelRule = buildregx($str1."(.*?)".$str2,"is");
		preg_match_all($labelRule,$content,$ar);
		return $ar[1][0];
	}
}
?>