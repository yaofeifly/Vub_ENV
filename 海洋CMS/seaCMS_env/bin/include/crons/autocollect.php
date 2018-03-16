<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
require_once(sea_INC."/collection.func.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
 
//开始采集列表
autogetlistsbyid($collectID);
//开始采集内容
autogetconbyid($collectID);
//清理缓存
autocache_clear(sea_ROOT.'/data/cache');



function autogetlistsbyid($id)
{
	@session_write_close();
	if($id==0) return false;
	global $dsql,$collectPageNum;
	$row = $dsql->GetOne("Select t.coding,t.sock,t.playfrom,t.autocls,t.classid,t.getherday,t.listconfig,c.cid,c.getlistnum from `sea_co_type` t left join `sea_co_config` c on c.cid=t.cid where t.tid='$id'");
	$listconfig=$row['listconfig'];
	$coding=$row['coding'];
	$sock=$row['sock'];
	$playfrom=$row['playfrom'];
	$classid=$row['classid'];
	$autocls=$row['autocls'];
	$getherday=$row['getherday'];
	$cid=$row['cid'];
	$getlistnum=$row['getlistnum'];
	$labelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($labelRule,$listconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	$lista=getrulevalue($loopstr,"lista");
	$listb=getrulevalue($loopstr,"listb");
	$mlinka=getrulevalue($loopstr,"mlinka");
	$mlinkb=getrulevalue($loopstr,"mlinkb");
	$picmode=getrulevalue($loopstr,"picmode");
	$pica=getrulevalue($loopstr,"pica");
	$picb=getrulevalue($loopstr,"picb");
	$pic_trim=getrulevalue($loopstr,"pic_trim");
	//处理页面链接
	$pageset=$attrDictionary["pageset"];
	if($pageset==0){
		$pageurl0=$attrDictionary["pageurl0"];
		$istart=0;
		$iend=0;
		$dourl[0][0]=$pageurl0;
	}else{
		$pageurl1=$attrDictionary["pageurl1"];
		$pageurl2=$attrDictionary["pageurl2"];
		$istart=$attrDictionary["istart"];
		$iend=$attrDictionary["iend"];
		$pageurlarr=GetUrlFromListRule($pageurl1,$pageurl2,$istart,$iend);
		$dourl=$pageurlarr;
	}
	$k=count($dourl);
	if (is_numeric($collectPageNum)>$k) $collectPageNum=$k;
	if (is_numeric($collectPageNum)<=0) $collectPageNum=$k;

	if ($collectPageNum>=0)
	{
		for ($i=0; $i<$collectPageNum; $i++)
		{
			$listurl =$dourl[$i][0];
			$html = cget($listurl,$sock);
			$html = ChangeCode($html,$coding);
			if($html=='')
			{
				//echo "读取网址： $listurl 时失败！\r\n";
			}
			if( trim($lista) !='' && trim($listb) != '' )
			{
				$areabody = $lista.'[var:区域]'.$listb;
				$html = GetHtmlArea('[var:区域]',$areabody,$html);
			}
			if( trim($mlinka) !='' && trim($mlinkb) != '' )
			{
				$linkrulex = $mlinka.'(.*)'.$mlinkb;
				$link = GetHtmlarray($html,$linkrulex);
				foreach($link as $s)
				{
					$links[][url] = FillUrl($listurl,$s);
				}
			}
			if(trim($picmode)==1 && trim($pica) !='' && trim($picb) != '' )
			{
				$picrulex = $mlinka.'(.*)'.$mlinkb;
				$piclink = GetHtmlarray($html,$picrulex);
				foreach($piclink as $s)
				{
					if(!empty($pic_trim)) $s=Gettrimvalue($pic_trim,$s);
					$links[][pic] = FillUrl($listurl,$s);
				}
			}
			$per_count = !$per_count?count($links):$per_count;
			if (!empty($links))
			{
				for ($j=0;$j<count($links);$j++)
				{
					$url=$links[$j][url];
					$pic=$links[$j][pic];
					$rowt=$dsql->GetOne("Select uid from `sea_co_url` where tid='$id' and url='$url'");
					if(is_array($rowt)){
						$dsql->ExecuteNoneQuery("update `sea_co_url` set succ='0',err='0' where uid=".$rowt['uid']);
					}else{
						$sql="insert into `sea_co_url`(cid,tid,url,pic) values ('$cid','$id','$url','$pic')";
						$dsql->ExecuteNoneQuery($sql);
					}
				}//for
			}//if
			unset($links);
		}
	}
}


function autogetconbyid($id)
{
	@session_write_close();
	if($id==0) return false;
	global $dsql,$col,$getconnum,$cfg_gatherset;
	$row = $dsql->GetOne("Select t.coding,t.sock,t.playfrom,t.autocls,t.classid,t.getherday,t.listconfig,t.itemconfig,c.cid,c.getconnum from `sea_co_type` t left join `sea_co_config` c on c.cid=t.cid where t.tid='$id'");
	$listconfig=$row['listconfig'];
	$itemconfig=$row['itemconfig'];
	//列表规则
	$listlabelRule = buildregx("{seacms:listrule(.*?)}(.*?){/seacms:listrule}","is");
	preg_match_all($listlabelRule,$listconfig,$listar);
	$listattrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$listar[1][0]));
	$listloopstr=$listar[2][0];
	$listattrDictionary=parseAttr($listattrStr);
	$reverse=$listattrDictionary["reverse"];
	//页面规则
	$labelRule = buildregx("{seacms:itemconfig(.*?)}(.*?){/seacms:itemconfig}","is");
	preg_match_all($labelRule,$itemconfig,$ar);
	$attrStr = trim(preg_replace("/[ \r\n\t\f]{1,}/"," ",$ar[1][0]));
	$loopstr=$ar[2][0];
	$attrDictionary=parseAttr($attrStr);
	//读出列表url
	$wheresql=" where err<3 and succ='0' and tid='$id'";
	$csql="select count(*) as dd from `sea_co_url` $wheresql";
	$rowd = $dsql->GetOne($csql);
	if(is_array($rowd)){
	$TotalResult = $rowd['dd'];
	}else{
	$TotalResult = 0;
	}
	if (is_numeric($getconnum)&&$getconnum>$TotalResult) $getconnum=$TotalResult;
	if (is_numeric($getconnum)&&$getconnum<=0) $getconnum=$TotalResult;
	$sqlStr="select * from `sea_co_url` $wheresql order by uid asc limit 0,$getconnum ";

	if($TotalResult!=0){
		$dsql->SetQuery($sqlStr);
		$dsql->Execute('url_list');
		while($rowt=$dsql->GetAssoc('url_list'))
		{
			$col->collect_db($listattrDictionary,$attrDictionary,$row,$rowt,$loopstr,'');
		}
		$dsql->ExecuteNoneQuery("delete from sea_co_url where tid='$id'");
	}
	unset($attrDictionary);
	unset($listattrDictionary);
	$dsql->ExecuteNoneQuery("update sea_co_type set cjtime='".time()."' where tid='$id'");
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