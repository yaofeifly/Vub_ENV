<?php
require_once(dirname(__FILE__)."/config.php");

if(empty($action))
{
	$action = '';
}

$m_file = sea_ROOT."/pic/slide/slide.xml";

//修改
if($action=='modifyside')
{
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
    $i = 0;
  	foreach ($xml as $item){
    $i++;
	if($i==$id){
		$item['item_url']=$item_url;
		$item['link']=$link;
		$item['title']=$title;
		$item['desc']=$desc;
		$xml->asXML($m_file);		
		}
	}
	echo "<script>alert('修改成功！');</script>";
	exit();
}
//删除
if($action=='delside')
{
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$item = $xml->item;
	unset($item[$id-1]);
	$xml->asXML($m_file);
	echo "<script>alert('删除成功！');location.href='admin_expand.php';</script>";
	exit();			
}
//批量修改
if($action=='modifyallside')
{
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
    $i = 0;	
	$a = 0;
	foreach($xml as $item){
		$i++;
		if(in_array($i,$e_id)){
			$item['item_url']=${'item_url'.$e_id[$a]};
			$item['link']=${'link'.$e_id[$a]};
			$item['title']=${'title'.$e_id[$a]};
			$item['desc']=${'desc'.$e_id[$a]};	
			$a++;
			$xml->asXML($m_file);
			}
	}
	echo "<script>alert('修改成功！');location.href='admin_expand.php';</script>";
	exit();
}
if($action=='addside')
{
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$item = $xml->addChild('item','');
	$item->addAttribute('item_url',$item_url);
	$item->addAttribute('link',$link);
	$item->addAttribute('title',$title);
	$item->addAttribute('desc',$desc);	
	$xml->asXML($m_file);
	echo "<script>alert('添加成功！');location.href='admin_expand.php';</script>";
	exit();
}
include(sea_ADMIN.'/templets/admin_expand.htm');
exit();
?>