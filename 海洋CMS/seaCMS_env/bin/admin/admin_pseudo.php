<?php
require_once(dirname(__FILE__)."/config.php");
$RepWordFile=sea_DATA."/admin/repword.txt";
$textsegment=sea_DATA.'/admin/textsegment.xml';
if(empty($action))
{
	$action = '';
}

if($action=='save')
{
	$xml = simplexml_load_file($textsegment);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($textsegment));}
	$i = 0;
	if(empty($id))
	{
		$xml->addChild('item',htmlspecialchars($newtxt));
		$xml->asXML($textsegment);
		echo "<script>alert('添加成功！');parent.location.href='admin_pseudo.php';</script>";
	}
	foreach ($xml as $item)
	{
		$i++;
		if($i == $id)
		{
			$xml->item[$i-1] = htmlspecialchars($newtxt);
			$xml->asXML($textsegment);
			echo "<script>alert('修改成功！');parent.location.href='admin_pseudo.php';</script>";
		}
	}


}

if($action=='read')
{
	$xml = simplexml_load_file($textsegment);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($textsegment));}
	$i = 0;
	foreach ($xml as $item)
	{
		$i++;
		if($i == $id)
		{
		$tmp = $xml->item[$i-1];
		$tmp = str_replace(chr(10),'\n',str_replace(chr(13),'',$tmp));
		echo "<script>parent.$('newhit').innerHTML='修改';parent.$('newtxt').value='$tmp';parent.$('id').value='$id';</script>";
	}
	}
}

if($action=='del')
{
	$xml = simplexml_load_file($textsegment);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($textsegment));}
	$i = 0;
	foreach ($xml as $item)
	{
	$i++;
	if($i == $id)
	{
	unset($xml->item[$i-1]);
	$xml->asXML($textsegment);
	echo "<script>alert('删除成功！');</script>";
	}
	}
	
}

if($action=='saverepword')
{
	$repwords = stripcslashes($txt);
	$fp = fopen($RepWordFile, 'w');
	fwrite($fp, $repwords);
	fclose($fp);
	echo "<script>location.href='admin_pseudo.php?action=replacewd&istart=$iStart&iend=$iEnd';</script>";
}
elseif($action=='replacewd')
{
	$pagesize = 30;
	$sql = " select d.v_id,d.v_name,c.body as v_content from `sea_data` d left join `sea_content` c on c.v_id=d.v_id";
	$dsql->SetQuery($sql);
	$dsql->Execute('totalnum');
	$num = $dsql->GetTotalRow('totalnum');
	if ($num%$pagesize) {
		$zongye=ceil($num/$pagesize);
	}elseif($num%$pagesize==0){
		$zongye=$num/$pagesize;
	}
	if($pageval<=1)$pageval=1;
	if($_GET['page']){
		$pageval=$_GET['page'];
		$page=($pageval-1)*$pagesize; 
		$page.=',';
	}
	if($pageval>$zongye)
	{
		echo "<script>alert('修改成功！');location.href='admin_pseudo.php'</script>";
	}
	$fp = fopen($RepWordFile, 'r');
	$repwords = fread($fp, filesize($RepWordFile));
	fclose($fp);
	$repwords=str_replace(chr(13).chr(10),"#",$repwords);
	$txt_temp=explode("#", $repwords);
	$whereStr ="  ";
	switch ($istart)
	{
		case '不限':
			if($iend!='不限')
			{
				$whereStr .="where d.v_id<$iend";
			}
			break;
		default:
			$whereStr .="where d.v_id>$istart";
			if($iend!='不限')
			{
				$whereStr .=" and d.v_id<$iend";
			}
			break;
	}
	$sql = "select d.v_id,d.v_name,c.body as v_content from `sea_data` d left join `sea_content` c on c.v_id=d.v_id ".$whereStr." order by v_id ASC limit $page $pagesize";
	$dsql->SetQuery($sql);
	$dsql->Execute('relacewd');
	echo "<div style='font-size:13px'>正在更新。。。<br>";
	while($row = $dsql->GetArray('relacewd'))
	{
		foreach ($txt_temp as $repword)
		{
			if($repword<>'')
			{
			$reparr = explode('=', $repword);
			$row['v_content'] = str_replace($reparr[0], $reparr[1], $row['v_content']);
			}
		}
		$upSql = "update `sea_content` set body='".$row['v_content']."' where v_id =".$row[v_id];
		$dsql->ExecNoneQuery($upSql);
		echo '成功更新&nbsp;ID:'.$row[v_id];
		echo '&nbsp;<font color=red>'.$row[v_name].'</font><br>';
	}
	echo "请等待3秒更新下一页<div>";
	echo "<script>function urlto(){location.href='admin_pseudo.php?action=replacewd&page=".($pageval+1)."&istart=".$istart."&iend=".$iend."';}setInterval('urlto()',3000);</script>";
	exit();	
}
else 
{
	include(sea_ADMIN.'/templets/admin_pseudo.htm');
	exit();
}