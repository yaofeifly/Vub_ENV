<?php
require(dirname(__FILE__) .'/admin_conn.php');
chkLogin();

$ac = be("all","ac");
if($ac=='getinfoxml')
{
	$tab = be("all","tab");
	$val = be("all","val");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	$arr = array();
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			if ($tab=="vodplay"){
				$path="play";
			}
			elseif ($tab=="voddown"){
				$path="down";
			}
			elseif ($tab=="vodserver"){
				$path="server";
			}
			$doc -> load("../inc/config/". $tab.".xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			foreach($nodes as $node){
				if ($val == $node->attributes->item(2)->nodeValue){
					$status = $node->attributes->item(0)->nodeValue;
					$sort = $node->attributes->item(1)->nodeValue;
					$show = $node->attributes->item(3)->nodeValue;
					$des = $node->attributes->item(4)->nodeValue;
					$tip = $node->getElementsByTagName("tip")->item(0)->nodeValue;
					break;
				}
			}
			$arr = array("from"=>"$val",
			"status"=>"$status",
			"sort"=>"$sort",
			"des"=>"$des",
			"tip"=>"$tip",
			"show"=>"$show"
			);
			unset($nodes);
			unset($xmlnode);
			break;
		case "timming":
			$doc -> load("../inc/config/timmingset.xml");
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			foreach($nodes as $node){
				if ($val == $node->getElementsByTagName("name")->item(0)->nodeValue){
					$des = $node->getElementsByTagName("des")->item(0)->nodeValue;
					$status = $node->getElementsByTagName("status")->item(0)->nodeValue;
					$file = $node->getElementsByTagName("file")->item(0)->nodeValue;
					$paramets = $node->getElementsByTagName("paramets")->item(0)->nodeValue;
					$weeks = $node->getElementsByTagName("weeks")->item(0)->nodeValue;
					$hours = $node->getElementsByTagName("hours")->item(0)->nodeValue;
					break;
				}
			}
			$arr = array("name"=>"$val",
			"des"=>"$des",
			"status"=>"$status",
			"file"=>"$file",
			"paramets"=>"$paramets",
			"weeks"=>"$weeks",
			"hours"=>"$hours"
			);
			unset($nodes);
			unset($xmlnode);
			break;
		default:
			break;
	}
	unset($doc);
	echo json_encode($arr);
}

elseif($ac=='delxml')
{
	$cache=true;
	$tab = be("all","tab");
	$val = be("all","val");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			if ($tab=="vodplay"){
				$path="play";
			}
			elseif ($tab=="voddown"){
				$path="down";
			}
			elseif ($tab=="vodserver"){
				$path="server";
			}
			$xp="../inc/config/". $tab.".xml";
			$doc->load($xp);
			$xmlnode = $doc->documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			foreach($nodes as $node){
				if ($val == $node->attributes->item(2)->nodeValue){
					$xmlnode->removeChild($node);
					break;
				}
			}
			$doc->save($xp);
			unset($nodes);
			unset($xmlnode);
			break;
		case "timming":
			$cache=false;
			$xp="../inc/config/timmingset.xml";
			$doc->load($xp);
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			foreach($nodes as $node){
				if ($val == $node->getElementsByTagName("name")->item(0)->nodeValue){
					$xmlnode->removeChild($node);
					break;
				}
			}
			$doc->save($xp);
			unset($nodes);
			unset($xmlnode);
			break;
		default:
			break;
	}
	unset($doc);
	if($cache){ @fwrite(fopen(MAC_ROOT.'/cache/cache_data.lock','wb'),''); }
	redirect( getReferer() );
}

elseif($ac=='savexml')
{
	$tab = be("all","tab");
	$flag = be("all","flag");
	$backurl = be("all","backurl");
	
	$cache=true;
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			$from = be("post","from");
			$status = be("post","status");
			$des = stripslashes(be("post","des"));
			$tip = stripslashes(be("post","tip"));
			$sort = be("post","sort");
			$show = be("post","show");
			if ($tab=="vodplay"){
				$path="play";
			}
			elseif ($tab=="voddown"){
				$path="down";
			}
			elseif ($tab=="vodserver"){
				$path="server";
			}
			$xp="../inc/config/". $tab.".xml";
			$doc->load($xp);
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			
			if ($flag=="edit"){
				foreach($nodes as $node){
					if ($from == $node->attributes->item(2)->nodeValue){
						$node->attributes->item(0)->nodeValue = $status;
						$node->attributes->item(1)->nodeValue = $sort;
						$node->attributes->item(3)->nodeValue = $show;
						$node->attributes->item(4)->nodeValue = $des;
						$node->getElementsByTagName("tip")->item(0)->nodeValue = "";
						$node->getElementsByTagName("tip")->item(0)->appendChild($doc->createCDATASection($tip));
						break;
					}
				}
				$doc->save($xp);
				unset($nodes);
				unset($xmlnode);
			}
			else{
				$nodenew = $doc -> createElement($path);
				
				$nodestatus1 =  $doc -> createAttribute("status");
				$nodestatus2 =  $doc -> createTextNode($status);
				$nodestatus1 -> appendChild($nodestatus2);
				
				$nodesort1 =  $doc -> createAttribute("sort");
				$nodesort2 =  $doc -> createTextNode($sort);
				$nodesort1 -> appendChild($nodesort2);
				
				$nodefrom1 =  $doc -> createAttribute("from");
				$nodefrom2 =  $doc -> createTextNode($from);
				$nodefrom1 -> appendChild($nodefrom2);
				
				$nodeshow1 =  $doc -> createAttribute("show");
				$nodeshow2 =  $doc -> createTextNode($show);
				$nodeshow1 -> appendChild($nodeshow2);
 				
				$nodedes1 =  $doc -> createAttribute("des");
				$nodedes2 =  $doc -> createTextNode($des);
				$nodedes1 -> appendChild($nodedes2);
				
				$nodetip1 = $doc -> createElement("tip");
				$nodetip2 = $doc -> createCDATASection($tip);
				$nodetip1 -> appendChild($nodetip2);
				
				$nodenew -> appendChild($nodestatus1);
				$nodenew -> appendChild($nodesort1);
				$nodenew -> appendChild($nodefrom1);
				$nodenew -> appendChild($nodeshow1);
				$nodenew -> appendChild($nodedes1);
				$nodenew -> appendChild($nodetip1);
				
				$doc->getElementsByTagName($path."s")-> item(0)  -> appendChild($nodenew);
				$doc -> save($xp);
				unset($nodenew);
			}
			break;
		case "timming":
			$cache=false;
			$name = be("post","name");
			$des = stripslashes(be("post","des"));
			$status = be("post","status");
			$file = be("post","file");
			$paramets = be("post","paramets"); $paramets = str_replace("&","&amp;",$paramets);
			$weeks = be("arr","weeks");
			$hours = be("arr","hours");
			
			
			$xp="../inc/config/timmingset.xml";
			$doc->load($xp);
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName("timming");
			
			if ($flag=="edit"){
				foreach($nodes as $node){
					if ($name == $node->getElementsByTagName("name")->item(0)->nodeValue){
						$node->getElementsByTagName("des")->item(0)->nodeValue = $des;
						$node->getElementsByTagName("status")->item(0)->nodeValue = $status;
						$node->getElementsByTagName("file")->item(0)->nodeValue = $file;
						$node->getElementsByTagName("paramets")->item(0)->nodeValue = $paramets;
						$node->getElementsByTagName("weeks")->item(0)->nodeValue = $weeks;
						$node->getElementsByTagName("hours")->item(0)->nodeValue = $hours;
						
						if( $node->getElementsByTagName("runtime")->length==0){
							$noderuntime1 = $doc -> createElement("runtime");
							$noderuntime2 = $doc -> createTextNode("");
							$noderuntime1 -> appendChild($noderuntime2);
							$node -> appendChild($noderuntime1);
						}
					}
				}
				$doc -> save($xp);
			}
			else{
				
				$nodenew = $doc -> createElement('timming');
				
				$nodename1 =  $doc -> createElement("name");
				$nodename2 =  $doc -> createTextNode($name);
				$nodename1 -> appendChild($nodename2);
				
				$nodedes1 = $doc -> createElement("des");
				$nodedes2 = $doc -> createTextNode($des);
				$nodedes1 -> appendChild($nodedes2);
				
				$nodestatus1 = $doc -> createElement("status");
				$nodestatus2 = $doc -> createTextNode($status);
				$nodestatus1 -> appendChild($nodestatus2);
				
				$nodefile1 = $doc -> createElement("file");
				$nodefile2 = $doc -> createTextNode($file);
				$nodefile1 -> appendChild($nodefile2);
				
				$nodeparamets1 = $doc -> createElement("paramets");
				$nodeparamets2 = $doc -> createTextNode($paramets);
				$nodeparamets1 -> appendChild($nodeparamets2);
				
				$nodeweeks1 = $doc -> createElement("weeks");
				$nodeweeks2 = $doc -> createTextNode($weeks);
				$nodeweeks1 -> appendChild($nodeweeks2);
				
				$nodehours1 = $doc -> createElement("hours");
				$nodehours2 = $doc -> createTextNode($hours);
				$nodehours1 -> appendChild($nodehours2);
				
				$noderuntime1 = $doc -> createElement("runtime");
				$noderuntime2 = $doc -> createTextNode("");
				$noderuntime1 -> appendChild($noderuntime2);
				
				
				$nodenew -> appendChild($nodename1);
				$nodenew -> appendChild($nodedes1);
				$nodenew -> appendChild($nodestatus1);
				$nodenew -> appendChild($nodefile1);
				$nodenew -> appendChild($nodeparamets1);
				$nodenew -> appendChild($nodeweeks1);
				$nodenew -> appendChild($nodehours1);
				$nodenew -> appendChild($noderuntime1);
				
				$doc->getElementsByTagName("timmings")-> item(0) -> appendChild($nodenew);
				$doc->save($xp);
			}
			break;
		default:
			break;
	}
	unset($doc);
	if($cache){ @fwrite(fopen(MAC_ROOT.'/cache/cache_data.lock','wb'),''); }
	showMsg('数据已保存',$backurl);
}

elseif($ac=='exportxml')
{
	$tab = be("all","tab");
	$val = be("all","val");
	$doc = new DOMDocument();
	$doc -> formatOutput = true;
	switch($tab)
	{
		case "vodplay":
		case "voddown":
		case "vodserver":
			if ($tab=="vodplay"){
				$path="play";
			}
			elseif ($tab=="voddown"){
				$path="down";
			}
			elseif ($tab=="vodserver"){
				$path="server";
			}
			$xp="../inc/config/". $tab.".xml";
			$doc->load($xp);
			$xmlnode = $doc -> documentElement;
			$nodes = $xmlnode->getElementsByTagName($path);
			foreach($nodes as $node){
				if ($val == $node->attributes->item(2)->nodeValue){
					$status = $node->attributes->item(0)->nodeValue;
					$sort = $node->attributes->item(1)->nodeValue;
					$show = $node->attributes->item(3)->nodeValue;
					$des = $node->attributes->item(4)->nodeValue;
					$tip = $node->getElementsByTagName('tip')->item(0)->nodeValue;
					$code = @file_get_contents('../player/'.$val.'.js');
				}
			}
			
			$res= '<status>'.$status.'</status>';
			$res.= '<sort>'.$sort.'</sort>';
			$res.= '<from>'.$val.'</from>';
			$res.= '<show>'.$show.'</show>';
			$res.= '<des>'.$des.'</des>';
			$res.= '<tip>'.$tip.'</tip>';
			$res.= '<code>'.$code.'</code>';
			$fp='../cache/export/vodplay_'.$val.'.txt';
			unset($xmlnode,$nodes);
			fwrite(fopen($fp,"wb"),$res);
			downFile('vodplay_'.$val.'.txt');
			break;
		case "timming":
			break;
	}
	
	unset($doc);
}

else{
	redirect( getReferer() );
}
?>