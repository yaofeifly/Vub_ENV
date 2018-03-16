<?php
@set_time_limit(0);
ob_implicit_flush();
require_once(dirname(__FILE__)."/config.php");
require_once(sea_DATA."/mark/inc_photowatermark_config.php");
CheckPurview();
if(empty($action))
{
	$action = '';
}

if($action=="downpic")
{
	include_once(sea_DATA."/config.ftp.php");
	@session_write_close();
	$isDownOk=false;
	$wheresql="where instr(v_pic,'#err')=0".($app_ftp==0?"":" and instr(v_pic,'$app_ftpurl')=0")." and instr(v_pic,'http')<>0";
	$trow = $dsql->GetOne("Select count(*) as dd From `sea_data` $wheresql");
	$totalnum = $trow['dd'];
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$pagesize=30;
	if(empty($totalpage)) $totalpage=ceil($totalnum/$pagesize);
	if($totalnum==0 || $page>$totalpage){
		ShowMsg("恭喜，所有资源站图片已经成功下载到本地!","admin_datarelate.php?action=checkpic");
		exit();
	}
	echo "<div style='font-size:13px'><font color=red>共".$totalpage."页,正在开始下载第".$page."页数据的的图片</font><br>";
	$dsql->SetQuery("Select v_id,v_name,v_pic From `sea_data` $wheresql order by v_addtime desc limit 0,$pagesize");
	$dsql->Execute('getpic');
	while($row=$dsql->GetObject('getpic'))
	{
		$picUrl=$row->v_pic;
		$v_id=$row->v_id;
		$v_name=$row->v_name;
		$picUrl = $image->downPicHandle($picUrl,$v_name);
		$query = "Update `sea_data` set v_pic='$picUrl' where v_id='$v_id'";
		$dsql->ExecuteNoneQuery($query);
		@ob_flush();
	    @flush();

	}	
	echo "<br>暂停3秒后继续下载<script language=\"javascript\">setTimeout(\"gatherNextPagePic();\",3000);function gatherNextPagePic(){location.href='?action=downpic&downtype=".$downtype."&page=".($page+1)."&totalpage=".$totalpage."';}</script></div>";
}
elseif($action=="uplpictoftp")
{
	include_once(sea_DATA."/config.ftp.php");
	@session_write_close();
	$isDownOk=false;
	$wheresql="where v_pic like 'uploads/%' ";
	$trow = $dsql->GetOne("Select count(*) as dd From `sea_data` $wheresql");
	$totalnum = $trow['dd'];
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$pagesize=30;
	if(empty($totalpage)) $totalpage=ceil($totalnum/$pagesize);
	if($totalnum==0 || $page>$totalpage){
		ShowMsg("恭喜，所有本地图片已经成功上传至FTP!","admin_datarelate.php?action=checkpic");
		exit();
	}
	echo "<font color=red>共".$totalpage."页,正在开始上传第".$page."页数据的的图片</font><br>";
	$dsql->SetQuery("Select v_id,v_name,v_pic From `sea_data` $wheresql order by v_addtime desc limit 0,$pagesize");
	$dsql->Execute('getpic');
	while($row=$dsql->GetObject('getpic'))
	{
		$picUrl=$row->v_pic;
		$v_id=$row->v_id;
		$v_name=$row->v_name;
		$urlupload = uploadftp2($picUrl);
		if($urlupload){
			$picUrl = $app_ftpurl.$app_ftpdir.$picUrl;
			if($app_updatepic==1)
			{
				echo "数据<font color=red>".$v_name."</font>的图片上传成功<a target=_blank href=".$picUrl.">预览图片</a><br />";
				$query = "Update `sea_data` set v_pic='$picUrl' where v_id='$v_id'";
				$dsql->ExecuteNoneQuery($query);
			}
			else
			{
				echo "数据<font color=red>".$v_name."</font>的图片上传成功,不更新图片地址<a target=_blank href=".$picUrl.">预览图片</a><br />";
			}
		}else{
			echo "数据<font color=red>".$v_name."</font>的图片上传失败,图片地址不更新<br / >";
		}
	}
	echo "<br>暂停3秒后继续上传<script language=\"javascript\">setTimeout(\"gatherNextPagePic();\",3000);function gatherNextPagePic(){location.href='?action=uplpictoftp&page=".($page+1)."&totalpage=".$totalpage."';}</script>";
}
elseif($action=="watermark")
{
	@session_write_close();
	$wheresql="where v_pic like 'uploads/%' and v_pic not like '%marked' ";
	$trow = $dsql->GetOne("Select count(*) as dd From `sea_data` $wheresql");
	$totalnum = $trow['dd'];
	$page = isset($page) ? intval($page) : 1;
	$pagesize=30;
	if(empty($totalpage)) $totalpage=ceil($totalnum/$pagesize);
	if($totalnum==0 || $page>$totalpage){
		ShowMsg("恭喜，所有本地图片已经成功加好水印","admin_datarelate.php?action=checkpic");
		exit();
	}
	echo "<font color=red>共".$totalpage."页,正在开始对第".$page."页数据的的图片加水印</font><br>";
	$dsql->SetQuery("Select v_id,v_pic,v_name From `sea_data` $wheresql order by v_addtime desc limit 0,$pagesize");
	$dsql->Execute('getpic');
	while($row=$dsql->GetObject('getpic'))
	{
		$errno = $image->watermark($row->v_pic,2);
		if($errno===true)
		{
			echo "数据<font color=red>".$row->v_name."</font>的图片水印完成<a target=_blank href='../".$row->v_pic."'>预览图片</a><br>";
			$dsql->ExecNoneQuery("update sea_data set v_pic= '".$row->v_pic."#marked' where v_id = ".$row->v_id);
		}else 
		{
			echo "数据<font color=red>".$row->v_name."</font>的图片水印失败,错误号$errno<br>";
			$dsql->ExecNoneQuery("update sea_data set v_pic= '".$row->v_pic."#error_".$errno."_marked' where v_id = ".$row->v_id);
		}
	}
	echo "<br>暂停3秒后继续<script language=\"javascript\">setTimeout(\"gatherNextPagePic();\",3000);function gatherNextPagePic(){location.href='?action=watermark&page=".($page+1)."&totalpage=".$totalpage."';}</script>";
}
elseif($action=="ftppic")
{
	require_once(sea_DATA."/config.ftp.php");
	include(sea_ADMIN.'/templets/admin_datarelate.htm');
	exit();
}
elseif($action=="saveftppic")
{
	$configfile = sea_DATA."/config.ftp.php";
	foreach($_POST as $k=>$v)
	{
		if(m_ereg("^edit__",$k))
		{
			if(is_array($$k))
			$v = cn_substrR(implode(',',$$k),500);
			else
			$v = cn_substrR(${$k},500);
		}
		else
		{
			continue;
		}
		$k = m_ereg_replace("^edit__","",$k);
		$configstr .="\${$k} = '$v';\r\n";
	}
	if(!is_writeable($configfile))
	{
		echo "配置文件'{$configfile}'不支持写入，无法修改系统配置参数！";
		exit();
	}
	$fp = fopen($configfile,'w');
	flock($fp,3);
	fwrite($fp,"<"."?php\r\n");
	fwrite($fp,$configstr);
	fwrite($fp,"?".">");
	fclose($fp);
	ShowMsg("成功更改站点配置！","admin_datarelate.php?action=ftppic");
	exit();
}
elseif($action=="batch")
{
	include(sea_ADMIN.'/templets/admin_datarelate.htm');
	exit();
}
elseif($action=="batchsubmit")
{
	viewHead("数据批量替换");
	echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" class=\"tb\"><tr class=\"thead\" align='left'><td>已处理数据</td></tr><tr><td>结果</td></tr>";
	if($v_field=="v_content"){
		$sql="select v_id,body from `sea_content` where body like '%".$v_str1."%'";
		$dsql->SetQuery($sql);
		$dsql->Execute('replacestr');
		while($row=$dsql->GetObject('replacestr'))
		{
			if($dsql->ExecuteNoneQuery("update `sea_content` set body='".addslashes(str_replace($v_str1,$v_str2,$row->body))."' where v_id='".$row->v_id."'"))
			{echo  "<tr><td>ID为".$row->v_id."的数据替换成功</td></tr>";}
		}
	}elseif($v_field=="v_playdata"){
		$sql="select v_id,body from `sea_playdata` where body like '%".$v_str1."%'";
		$dsql->SetQuery($sql);
		$dsql->Execute('replacestr');
		while($row=$dsql->GetObject('replacestr'))
		{
			$dsql->ExecuteNoneQuery("update `sea_playdata` set body='".addslashes(str_replace($v_str1,$v_str2,$row->body))."' where v_id='".$row->v_id."'");
			echo  "<tr><td>ID为".$row->v_id."的数据替换成功</td></tr>";
		}
	}elseif($v_field=="v_downdata"){
		$sql="select v_id,body1 from `sea_playdata` where body1 like '%".$v_str1."%'";
		$dsql->SetQuery($sql);
		$dsql->Execute('replacestr');
		while($row=$dsql->GetObject('replacestr'))
		{
			$dsql->ExecuteNoneQuery("update `sea_playdata` set body1='".addslashes(str_replace($v_str1,$v_str2,$row->body1))."' where v_id='".$row->v_id."'");
			echo  "<tr><td>ID为".$row->v_id."的数据替换成功</td></tr>";
		}
	}else{
		$sql="select v_id,".$v_field." from `sea_data` where ".$v_field." like '%".$v_str1."%'";
		$dsql->SetQuery($sql);
		$dsql->Execute('replacestr');
		while($row=$dsql->GetObject('replacestr'))
		{
			$query="update `sea_data` set ".$v_field."='".addslashes(str_replace($v_str1,$v_str2,$row->$v_field))."' where v_id='".$row->v_id."'";
			$dsql->ExecuteNoneQuery($query);
			echo  "<tr><td>ID为".$row->v_id."的数据替换成功</td></tr>";
		}
	}
	echo "</table>";
	viewFoot();
}
elseif($action=="delvideoform")
{
	include(sea_ADMIN.'/templets/admin_datarelate.htm');
	exit();
}
//清除图片不存在地址
elseif($action=="existpic")
{
	include(sea_DATA."/config.ftp.php");
	$sql = "select v_id,v_pic from sea_data order by v_id ASC "; 
	$dsql->SetQuery($sql);
	$dsql->Execute('existpic');
	echoHead();
	echo "正在更新影片图片地址。。。<br>";
	while($row = $dsql->GetArray('existpic'))
	{
		if($row[v_pic]!=''&&strpos($row[v_pic],'uploads/')===0){
			$row[v_pic] = preg_replace("/#.*?marked/", '', $row[v_pic]);
			if(!file_exists('../'.$row[v_pic])&&strpos($row['v_pic'],$app_ftpurl)===false)
			{
				$upSql = "update `sea_data` set v_pic='' where v_id =".$row[v_id];
				$dsql->ExecNoneQuery($upSql);
				echo '成功清空&nbsp;ID:'.$row[v_id].'图片地址<br>';
			}
		}
	}
	echo "<script>alert('恭喜已经搞定！');location.href='admin_datarelate.php?action=checkpic'</script>";
	echoFoot();
	exit();	
}

//重新标记下载失败的视频文章图片为可下载
elseif($action=="redown")
{
	$query="update `sea_data` set v_pic =replace(v_pic,'#err','')";
	$dsql->ExecuteNoneQuery($query);
	$query2="update `sea_news` set n_pic =replace(n_pic,'#err','')";
	$dsql->ExecuteNoneQuery($query2);
	echoHead();
	echo "<h3><br><br>已成功标记下载失败的视频文章图片为可下载<br><br>请在图片工具里重新点击下载视频及文章图片<br><br><br></h3>";
	echoFoot();
	exit();	
}

//清除多余数据图片
elseif($action=="sumitcheck"){
	echoHead();
	$pre = array('lit','all');
	foreach ($pre as $p){
		$dir = '../uploads/'.$p.'img';
		$folder = getFolderList($dir);
		foreach ($folder as $file)
		{
			if($file['filetype']=='folder')
			{
				$folder2 = getFolderList($dir.'/'.$file['filename']);
				foreach ($folder2 as $imfile)
				{
					$imdir = substr($dir,3).'/'.$file['filename'].'/'.$imfile['filename'];
					$sql = "select v_id from sea_data where v_pic ='". $imdir."' or v_pic like '". $imdir."%marked'";
					$dsql->SetQuery($sql);
					$dsql->Execute('querypic');
					$sql2 = "select n_id from sea_news where n_pic ='". $imdir."' or n_pic like '". $imdir."%marked'";
					$dsql->SetQuery($sql2);
					$dsql->Execute('querypic2');
					if($dsql->GetTotalRow('querypic')==0 AND $dsql->GetTotalRow('querypic2')==0)
					{	
						unlink('../'.$imdir);
						echo "成功删除 图片路径".$imdir."<br>";
					}	
				}
			}
		}
	}
	echo "<script>alert('恭喜已经搞定！');location.href='admin_datarelate.php?action=checkpic'</script>";	
	echoFoot();
	exit();	
}
elseif($action=="sql")
{
	include(sea_ADMIN.'/templets/admin_datarelate_sql.htm');
	exit();
}
elseif($action=="result"){
	include(sea_ADMIN.'/templets/admin_datarelate_result.htm');
	exit();
}
elseif($action=="randomset")
{
	include(sea_ADMIN.'/templets/admin_datarelate_randomset.htm');
	exit();	
}
elseif($action=="fileperms")
{
	include(sea_ADMIN.'/templets/admin_datarelate_fileperms.htm');
	exit();	
}
elseif($action=="repeat")
{
	include(sea_ADMIN.'/templets/admin_reapeat.htm');
	exit();	
}
elseif($action=="checkpic")
{
	include(sea_ADMIN.'/templets/admin_datarelate_checkpic.htm');
	exit();	
}
elseif($action=="repairplaydata")
{
	$do=isset($do) ? $do : '';
	if($do!=true)
	{
	include(sea_ADMIN.'/templets/admin_repairplaydata.htm');
	exit();	
	}
	else 
	{
    $pagesize = 500;
	$row=$dsql->GetOne("select count(*) as dd from sea_playdata");
	if(is_array($row))
	{
		$num = $row['dd'];
		if($num==0){
		echo "<div class=\"container\" id=\"cpcontainer\">没有数据</div>";
		}
		else{
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
				echo "<script>alert('修改成功！');location.href='admin_datarelate.php?action=repairplaydata'</script>";
			}	
			$rcount=0;
			$patten='/^[^\$]+\$\$[^\$#]+\$[^\$]+\$[\w\s]+(#[^\$#]+\$[^\$]+\$[\w\s]+)*((\$\$\$)[^\$]+\$\$[^\$#]+\$[^\$]+\$[\w\s]+(#[^\$#]+\$[^\$]+\$[\w\s]+)*)*$/is';
			$patten2='/^[^\$]+\$\$[^\$#]+\$[^\$]+\$[\w\s]+(#[^\$#]+\$[^\$]+\$[\w\s]+)*/is';
			$fs='hd_tudou|hd_iask|hd_sohu|hd_openv|hd_56|youku|tudou|sohu|iask|6rooms|qq|youtube|ku6|flv|swf|real|media|qvod|pps|gvod|wp2008|cc|ppvod|pipi|56|17173|joy';
			$patten3='/(\$('.$fs.'))\s*(\$('.$fs.'))*#+/is';
			echo "<div class=\"container\" id=\"cpcontainer\">正在检验并修复数据,当前是第<font color='red'>".$pageval."</font>页,共<font color='red'>".$zongye."</font>页,已成功修复<font color='red'>".$rcount."</font>部数据<hr style=\"border:1px solid #DEEFFA\"/>";
			$sql="select v_id,tid,body from sea_playdata limit $page $pagesize";
			$dsql->SetQuery($sql);
			$dsql->Execute('repairplaydata');
			while($row = $dsql->GetArray('repairplaydata'))
				{
					$splay = trim($row['body']);
					$change = false;
					if(!empty($splay))
					{
						if(substr($splay,-1)=="#")
						{
							$splay=substr($splay,0,strlen($splay)-1);
							$change=true;
						}
						if(!preg_match($patten,$splay))
						{
							$change=true;
							$splay=str_replace("'","",str_replace("$$$$","$"."qvod$$$",str_replace("$$$$$","$$$"."qvod$$",str_replace("$$$$$$","$$$",str_replace("#$$$","$$$",$splay)))));
							$splay=rtrim(ltrim($splay,"$$$"),"$$$");
							if(!preg_match($patten,$splay))
							{
								$dd=explode("$$$",$splay);
								$dy=array();
								foreach ($dd as &$d)
								{
									$d=preg_replace($patten3,"$1#",$d);
										$ff=explode("$$",$d);
										if(count($ff)>0)
										{
											$ul=explode("#",$ff[1]);
											$lt=array();
											foreach ($ul as $k=>$u)
											{
												if(!empty($u))
												{
													$li=explode("$",$u);
													if(strpos($li[0],"://")>0)
													{
														$li[2]=$li[1];
														$li[1]=$li[0];
														$li[0]="";
													}
													if(strpos(" ".$fs,trim($li[2]))==0)
													{
														$li[2]="";
													}
													if(!empty($li[1]))
													{
														if(empty($li[2]))
														{
															if(strpos(" ".$li[1],"qvod://")>0)
															$li[2]='qvod';
															elseif(strpos(" ".$li[1],"gvod://")>0)
															$li[2]='gvod';
															else 
															$li[2]=getReferedId($ff[0]);
														}
														if(empty($li[0]))
														{
															if(count($ul)>1)
															$li[0]="第".($k+1)."集";	
															else 
															$li[0]="全集";
														}
														$li[0]=substr($li[0],0,50);
														foreach ($li as &$l)
														{
															$l=$l.'$';
														}
														$lib=implode($li);
														$lib=rtrim($lib,'$');
														$lt[]=$lib;
													}
													
												}
											}
											foreach ($lt as &$t)
											{
												$t=$t.'#';
											}
											$ff[1]=rtrim(implode($lt),'#');
										}
										foreach ($ff as &$f)
										{
											$f=$f.'$$';
										}
										$d = implode($ff);
										$d = rtrim($d,'$$');
										if(preg_match($patten2,$d)) $dy[] = $d;
									
									else 
									{
										$dy[] = $d;
									}
								}
								$splay=rtrim(implode($dy),'$$$');
								$splay=preg_match($patten,$splay)?$splay:"";
							}
						}
						if($change)
						{
							$rcount++;
							$fined=true;
							$dsql->ExecNoneQuery("update sea_playdata set body= '".$splay."' where v_id=".$row['v_id']);
							echo "ID为:<font color=red>".$row['v_id']."</font>的数据修复成功<br>";
						}
					}
				}
				echo "<br>暂停3秒后继续检验并修复数据</div><script language=\"javascript\">setTimeout(function (){location.href='?action=repairplaydata&do=true&page=".($pageval+1)."';},3000);</script>";
			
				}
		}else 
		{
		echo "<div class=\"container\" id=\"cpcontainer\">查询数据时出现问题</div>";
		}
	}
}
elseif($action=="dorandomset"){
	$pagesize = 30;
	$sql = " select v_id,v_name,v_hit from `sea_data`";
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
		echo "<script>alert('修改成功！');location.href='admin_datarelate.php?action=randomset'</script>";
	}
	$sql = "select v_id,v_name,v_hit from `sea_data` order by v_id ASC limit $page $pagesize";
	$dsql->SetQuery($sql);
	$dsql->Execute('randomset');
	echo "<div style='font-size:13px'>正在更新。。。<br>";
	while($row = $dsql->GetArray('randomset'))
	{
		$hit=rand(0, $maxHit);
		$upSql = "update `sea_data` set v_hit=".$hit." where v_id =".$row[v_id];
		$dsql->ExecNoneQuery($upSql);
		echo '<body>成功更新&nbsp;ID:'.$row[v_id];
		echo '&nbsp;<font color=red>'.$row[v_name].'</font>';
		echo '&nbsp;点击量:&nbsp;'.$hit.'<br>';
	}
	echo "请等待".($time/1000)."秒更新下一页</div>";
	echo "<script>function urlto(){location.href='admin_datarelate.php?action=dorandomset&time=".$time."&maxHit=".$maxHit."&page=".($pageval+1)."';}
	setInterval('urlto()',".$time.");</script>";
	exit();	
}
elseif($action=="delByDown")
{
	
	//$wtime = $_POST[wtime];
	//$domain = $_POST[domain];
	$id=gethouzhui($from);
	if($from=="" OR empty($from) OR $from==NULL){
		ShowMsg("请选择要批量删除的下载类型!","admin_datarelate.php?action=delvideoform");
		exit();
	}

	$likestr=empty($domain) ? "" : $domain."%";
	$wherestr=" where body1 like '%".$likestr."$".$id."%'";
	$numPerPage=30;
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$csqlStr="select count(*) as dd from `sea_playdata`".$wherestr;
	$row = $dsql->GetOne($csqlStr);
	if(is_array($row)){
	$TotalResult = $row['dd'];
	}else{
	$TotalResult = 0;
	}
	if(empty($TotalPage)) $TotalPage = ceil($TotalResult/$numPerPage);
	if(!empty($domain)) $str="(".$domain.").+"; else $str="";
	$regstr=$from.'.+'.$str.'\$'.$id;
	if($TotalResult==0 || $page>$TotalPage){
		ShowMsg("恭喜，已经搞定!","admin_datarelate.php?action=delvideoform");
		exit();
	}
	$regex=buildregx($regstr,'is');
	$limitstart = ($page-1) * $numPerPage;
	if($limitstart<0) $limitstart=0;
	echo "<style>body{font-size: 12px;}</style>正在准备删除<font color='red' >".$from."</font>来源,共<font color='red' >".$TotalPage."</font>页，当前<font color='red' >".$page."</font>页，每页<font color='red' >".$numPerPage."</font>个<br/>";
	$sql="select v_id,body1 from sea_playdata ".$wherestr." limit 0,$numPerPage";
	$dsql->SetQuery($sql);
	$dsql->Execute('delByDown');
	while($row=$dsql->GetObject('delByDown'))
	{
		$playdata = $row->body1;
		$playdata=str_replace('$$$$$$','$$$',str_replace('$$$$$$','$$$',ltrim(rtrim(ltrim(rtrim(preg_replace($regex,'',$playdata),'#'),'$'),'$$$'),'$$$')));
		$dsql->ExecuteNoneQuery("update sea_playdata set body1='$playdata' where v_id=".$row->v_id);
		echo "数据ID<font color='red' >".$row->v_id."</font>的<font color='red' >".$from."</font>来源删除成功<br/>";
	}
	echo "<br>暂停".$wtime."秒后继续<script language=\"javascript\">setTimeout(\"transNextPage();\",".$wtime."*1000);function transNextPage(){location.href='?action=delByDown&page=".($page+1)."&TotalPage=".$TotalPage."&wtime=".$wtime."&from=".$from."&domain=".$domain."';}</script>";
}
elseif($action=="delByFrom")
{
	
	//$wtime = $_POST[wtime];
	//$domain = $_POST[domain];
	$id=gethouzhui($from);
	if($from=="" OR empty($from) OR $from==NULL){
		ShowMsg("请选择要批量删除的播放器类型!","admin_datarelate.php?action=delvideoform");
		exit();
	}

	$likestr=empty($domain) ? "" : $domain."%";
	$wherestr=" where body like '%".$likestr."$".$id."%'";
	$numPerPage=30;
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$csqlStr="select count(*) as dd from `sea_playdata`".$wherestr;
	$row = $dsql->GetOne($csqlStr);
	if(is_array($row)){
	$TotalResult = $row['dd'];
	}else{
	$TotalResult = 0;
	}
	if(empty($TotalPage)) $TotalPage = ceil($TotalResult/$numPerPage);
	if(!empty($domain)) $str="(".$domain.").+"; else $str="";
	$regstr=$from.'.+'.$str.'\$'.$id;
	if($TotalResult==0 || $page>$TotalPage){
		ShowMsg("恭喜，已经搞定!","admin_datarelate.php?action=delvideoform");
		exit();
	}
	$regex=buildregx($regstr,'is');
	$limitstart = ($page-1) * $numPerPage;
	if($limitstart<0) $limitstart=0;
	echo "<style>body{font-size: 12px;}</style>正在准备删除<font color='red' >".$from."</font>来源,共<font color='red' >".$TotalPage."</font>页，当前<font color='red' >".$page."</font>页，每页<font color='red' >".$numPerPage."</font>个<br/>";
	$sql="select v_id,body from sea_playdata ".$wherestr." limit 0,$numPerPage";
	$dsql->SetQuery($sql);
	$dsql->Execute('delByFrom');
	while($row=$dsql->GetObject('delByFrom'))
	{
		$playdata = $row->body;
		$playdata=str_replace('$$$$$$','$$$',str_replace('$$$$$$','$$$',ltrim(rtrim(ltrim(rtrim(preg_replace($regex,'',$playdata),'#'),'$'),'$$$'),'$$$')));
		$dsql->ExecuteNoneQuery("update sea_playdata set body='$playdata' where v_id=".$row->v_id);
		echo "数据ID<font color='red' >".$row->v_id."</font>的<font color='red' >".$from."</font>来源删除成功<br/>";
	}
	echo "<br>暂停".$wtime."秒后继续<script language=\"javascript\">setTimeout(\"transNextPage();\",".$wtime."*1000);function transNextPage(){location.href='?action=delByFrom&page=".($page+1)."&TotalPage=".$TotalPage."&wtime=".$wtime."&from=".$from."&domain=".$domain."';}</script>";
}elseif($action=="checkfileperms")
{
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
	include(sea_ADMIN.'/templets/admin_datarelate_fileperms.htm');
	exit();	
}
elseif($action=="downnewspic")
{
	include_once(sea_DATA."/config.ftp.php");
	@session_write_close();
	$isDownOk=false;
	$wheresql="where instr(n_pic,'#err')=0".($app_ftp==0?"":" and instr(n_pic,'$app_ftpurl')=0")." and instr(n_pic,'http')<>0";
	$trow = $dsql->GetOne("Select count(*) as dd From `sea_news` $wheresql");
	$totalnum = $trow['dd'];
	$page = isset($page) ? intval($page) : 1;
	if($page==0) $page=1;
	$pagesize=30;
	if(empty($totalpage)) $totalpage=ceil($totalnum/$pagesize);
	if($totalnum==0 || $page>$totalpage){
		ShowMsg("恭喜，所有文章图片已经成功下载到本地!","admin_datarelate.php?action=checkpic");
		exit();
	}
	echo "<div style='font-size:13px'><font color=red>共".$totalpage."页,正在开始下载第".$page."页文章的图片</font><br>";
	$dsql->SetQuery("Select n_id,n_title,n_pic From `sea_news` $wheresql order by n_addtime desc limit 0,$pagesize");
	$dsql->Execute('getpic');
	while($row=$dsql->GetObject('getpic'))
	{
		$picUrl=$row->n_pic;
		$n_id=$row->n_id;
		$n_name=$row->n_title;
		$picUrl = $image->downPicHandle($picUrl,$n_title);
		$query = "Update `sea_news` set n_pic='$picUrl' where n_id='$n_id'";
		$dsql->ExecuteNoneQuery($query);
		echo '已下载<font color=red>';
		echo $n_name;
		echo '</font>的图片<br>';
		@ob_flush();
	    @flush();

	}	
	echo "<br>暂停3秒后继续下载<script language=\"javascript\">setTimeout(\"gatherNextPagePic();\",3000);function gatherNextPagePic(){location.href='?action=downnewspic&downtype=".$downtype."&page=".($page+1)."&totalpage=".$totalpage."';}</script></div>";
}

function echoFieldOptions()
{
	$fieldOptionArray[0][0]="数据名称" ; $fieldOptionArray[0][1]="v_name";
	$fieldOptionArray[1][0]="数据图片" ; $fieldOptionArray[1][1]="v_pic";
	$fieldOptionArray[2][0]="数据主演" ; $fieldOptionArray[2][1]="v_actor";
	$fieldOptionArray[3][0]="数据内容简介" ; $fieldOptionArray[3][1]="v_content";
	$fieldOptionArray[4][0]="数据添加时间" ; $fieldOptionArray[4][1]="v_addtime";
	$fieldOptionArray[5][0]="数据标题颜色" ; $fieldOptionArray[5][1]="v_color";
	$fieldOptionArray[6][0]="数据发行年份" ; $fieldOptionArray[6][1]="v_publishyear";
	$fieldOptionArray[7][0]="数据发行地区" ; $fieldOptionArray[7][1]="v_publisharea";
	$fieldOptionArray[8][0]="主分类ID" ; $fieldOptionArray[8][1]="tid";
	$fieldOptionArray[9][0]="扩展分类ID" ; $fieldOptionArray[9][1]="v_extratype";
	$fieldOptionArray[10][0]="数据推荐" ; $fieldOptionArray[10][1]="v_commend";
	$fieldOptionArray[11][0]="数据点击量" ; $fieldOptionArray[11][1]="v_hit";
	$fieldOptionArray[12][0]="播放地址" ; $fieldOptionArray[12][1]="v_playdata";
	$fieldOptionArray[13][0]="下载地址" ; $fieldOptionArray[13][1]="v_downdata";
	$fieldOptionArray[14][0]="影片导演" ; $fieldOptionArray[14][1]="v_director";
	$fieldOptionArray[15][0]="数据语言" ; $fieldOptionArray[15][1]="v_lang";
	$fieldOptionArray[16][0]="顶的次数" ; $fieldOptionArray[16][1]="v_digg";
	$fieldOptionArray[17][0]="踩的次数" ; $fieldOptionArray[17][1]="v_tread";
	$fieldOptionArray[18][0]="总评分"; $fieldOptionArray[18][1]="v_score";
	$fieldOptionArray[19][0]="评分次数" ; $fieldOptionArray[19][1]="v_scorenum";
	$fieldOptionArray[20][0]="更新周期" ; $fieldOptionArray[20][1]="v_reweek";
	$fieldOptionArray[21][0]="电视台" ; $fieldOptionArray[21][1]="v_tvs";
	$fieldOptionArray[22][0]="关键词" ; $fieldOptionArray[22][1]="v_tags";
	$fieldOptionArray[23][0]="发行公司" ; $fieldOptionArray[23][1]="v_company";
	$fieldOptionArray[24][0]="影片别名" ; $fieldOptionArray[24][1]="v_nickname";
	$fieldOptionArray[25][0]="剧情分类" ; $fieldOptionArray[25][1]="v_jq";
	$fieldOptionArray[26][0]="豆瓣评分" ; $fieldOptionArray[26][1]="v_douban";
	$fieldOptionArray[27][0]="时光网评分" ; $fieldOptionArray[27][1]="v_mtime";
	$fieldOptionArray[28][0]="IMDB评分" ; $fieldOptionArray[28][1]="v_imdb";
	$fieldOptionArray[29][0]="日点击量" ; $fieldOptionArray[29][1]="v_dayhit";
	$fieldOptionArray[30][0]="周点击量" ; $fieldOptionArray[30][1]="v_weekhit";
	$fieldOptionArray[31][0]="月点击量" ; $fieldOptionArray[31][1]="v_monthhit";
	$fieldOptionArray[32][0]="视频长度" ; $fieldOptionArray[32][1]="v_len";
	$fieldOptionArray[33][0]="视频集数" ; $fieldOptionArray[33][1]="v_total";
	$fieldOptionArray[34][0]="影片备注" ; $fieldOptionArray[34][1]="v_note";
	$fieldOptionArray[35][0]="影片版本" ; $fieldOptionArray[35][1]="v_ver";
	$fieldOptionArray[36][0]="备用说明" ; $fieldOptionArray[36][1]="v_longtxt";
	$fieldOptionArray[37][0]="幻灯图片" ; $fieldOptionArray[37][1]="v_spic";
	$fieldOptionArray[38][0]="背景图片" ; $fieldOptionArray[38][1]="v_gpic";
	$arrayLen=count($fieldOptionArray);
	for ($i=0;$i<$arrayLen;$i++){
		echo "<option value=\"".$fieldOptionArray[$i][1]."\">[".$fieldOptionArray[$i][0]."]</option>";
	}
}

function TestWrite($d)
{
	$tfile = '_pipi.txt';
	$d = m_ereg_replace('/$','',$d);
	$fp = @fopen($d.'/'.$tfile,'w');
	if(!$fp) return false;
	else
	{
		fclose($fp);
		$rs = @unlink($d.'/'.$tfile);
		if($rs) return true;
		else return false;
	}
}


function makePlayerSelect($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/playerKinds.xml";
	
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==$player['flag']){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			if($player['open']==1)
			$allstr .="<option value='".$player['flag']."' $selectstr>".gbutf8(stripslashes($player['flag']))."</option>";
			
	}
	return $allstr;
}

function makePlayerSelect2($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/playerKinds.xml";
	
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==$player['flag']){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			if($player['open']==1)
			$allstr .="<option value='".$player['flag']."' $selectstr>".gbutf8(stripslashes($player['flag']))."</option>";
			
	}
	return $allstr;
}
function makePlayerSelect3($flag)
{
	$playerArray=array();
	$m_file = sea_DATA."/admin/downKinds.xml";
	
	$xml = simplexml_load_file($m_file);
	if(!$xml){$xml = simplexml_load_string(file_get_contents($m_file));}
	$i = 0;
	$a = 0;
	foreach($xml as $player){
		$i++;
		if($flag==$player['flag']){
			$selectstr=" selected";
			}else{
			$selectstr="";
			}
			if($player['open']==1)
			$allstr .="<option value='".$player['flag']."' $selectstr>".gbutf8(stripslashes($player['flag']))."</option>";
			
	}
	return $allstr;
}

function gethouzhui($str)
{
	$playerKindsfile="../data/admin/playerKinds.xml";
					$xml = simplexml_load_file($playerKindsfile);
					if(!$xml){$xml = simplexml_load_string(file_get_contents($playerKindsfile));}
					$id=0;
					$z=array();
					foreach($xml as $player){
					$k=$player['postfix'];
					$z=$player['flag'];
					if (m_ereg("$z",$str)) return "$k";
					}
	
	
	
}