<?php
if(!defined('sea_INC')||!defined('sea_ROOT')||!defined('sea_DATA'))
{
	exit("Request Error!");
}
@set_time_limit(0);
ob_implicit_flush();
if(!class_exists('DB_MySQL')) require_once(sea_ROOT."/admin/config.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
$isref=1;//是否使用sock采集 0为不是用，1为使用
$ressite=$rid1;

gatherDay($var_url1);
//清理缓存
autocache_clear(sea_ROOT.'/data/cache');

function gatherDay($var_url1){
	global $rid1;
	if($rid1==32)
	{
		$weburl=$var_url1."-cid--h-24";
	}
	else
	{
		$weburl=$var_url1.(strpos($var_url1,'?')!==false?"&":"?")."ac=videolist&t=0&h=24";
	}
    autoIntoDatabase($weburl);
}

function autoIntoDatabase($url,$page=1)
{
	@session_write_close();
	global $dsql,$col,$isref,$rid1;
	$weburl=$url."&pg=".$page;
	if($rid1==32)
	{
		$weburl=$url."-p-{$page}";
	}
	$content=cget($weburl,$isref);
	$content=filterChar($content);
	$xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA); 
	if(!$xml){	$xml =  simplexml_load_string(cget($weburl,0), 'SimpleXMLElement', LIBXML_NOCDATA); }
	$temparr=array();
	$temparr=getrulevaluearra($content,'v');
	$pagecount = $xml->list['pagecount'];
	$page = $xml->list['page'];	
	$pagesize = $xml->list['pagesize'];
	$recordcount = $xml->list['recordcount'];
	foreach($xml->list->video as $video)
	{
		$xmltid =  $video->tid;//影片分类id
		$name =  $video->name;//影片名称
		$localId = getBindedLocalIda($rid1.'_'.$xmltid);//入库后本地id
		$data = "$$".$video->dl->dd;
		if(!empty($name)&&!empty($data))
		{
			$col->xml_db($video,$localId);
		}
	}	//foreach
	if(intval($page) < intval($pagecount))autoIntoDatabase($url,$page+1);
}

function getBindedLocalIda($libId)
{
	global $dsql;
	$row = $dsql->GetOne("select count(*) as dd from sea_type where unionid<>''");
	if(!is_array($row)) return '';
	$sqlStr="select tid,unionid from sea_type where unionid<>''";
	$dsql->SetQuery($sqlStr);
	$dsql->Execute('unionid_list');
	while($row=$dsql->GetObject('unionid_list'))
	{
		$unionArray=explode(",",$row->unionid); $arrayLen2=count($unionArray);
		for($i=0;$i<$arrayLen2;$i++){
			if (trim($unionArray[$i])==trim($libId)) return $row->tid;
		}
	}
}

function getrulevaluearra($content,$str)
{
	if(!empty($content) && !empty($str)){
		$labelRule = buildregx("<".$str.">(.*?)"."</".$str.">","is");
		preg_match_all($labelRule,$content,$ar);
		return $ar[1];
	}
}


function autocache_clear($dir) {
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
?>