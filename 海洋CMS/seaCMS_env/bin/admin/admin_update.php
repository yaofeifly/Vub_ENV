<?php
require(dirname(__FILE__).'/config.php');
//die('<p style="font-size:12px;">暂不开放在线升级功能！</p>');
CheckPurview('');
@set_time_limit(0);

//升级服务器
$updateHost = 'http://update.seacms.net/utf8';

//当前软件版本锁定文件
$verLockFile = sea_ROOT.'/data/admin/ver.txt';

//升级日志文件
$updatelogfile = sea_ROOT.'/data/admin/updatelog.txt';


$zip = new zip;

if($action=="isNew")
{
	AjaxHead();
	$fp = fopen($verLockFile,'r');
	$verLocal = trim(fread($fp,64));
	fclose($fp);
	$verlist = trim(get($updateHost.'/info.txt'));
	if($verlist!=''){
		$verRemote=getrulevalue($verlist,"version");
		if($verLocal!=$verRemote) echo getrulevalue($verlist,"info");
		else echo 'False';
	}
}
elseif($action=="downloadselect")
{
	if(empty($e_id))
	{
		ShowMsg("请选择下载文件！","admin_update.php");
		exit();
	}
	echo "<style type=\"text/css\">body{ font-size:12px;}</style>";
	foreach($e_id as $updatefiles)
	{
		$updatefilesArray=explode('----',$updatefiles);
		$fileLocal="../update/".$updatefilesArray[0].'.zip';
		$fileRemote=$updateHost.'/source/'.$updatefilesArray[0].'.zip';
		get_file($fileRemote,"../update",$updatefilesArray[0].'.zip');
		if(file_exists($fileLocal))
		echo '<font color="#FF0000">'.$fileRemote.'</font>下载成功!<br>';
		else{
		echo '<font color="#FF0000">'.$fileRemote.'</font>下载失败!<a href="#" onclick="history.go(-1)">返回</a>';
		exit();
		}
	}
	alertMsg("下载完成！","admin_update.php");
	exit;
}
elseif($action=="updateselect")
{
	if(empty($e_id))
	{
		ShowMsg("请选择升级文件！","admin_update.php");
		exit();
	}
	echo "<style type=\"text/css\">body{ font-size:12px;}</style>";
	foreach($e_id as $updatefiles)
	{
		$updatefilesArray=explode('----',$updatefiles);
		updateFile($updatefilesArray[0]);
		writeUpdateLog($updatefiles);
	}
	$fp = fopen($verLockFile,'w');
	fwrite($fp,$verRemote);
	fclose($fp);
	alertMsg("升级成功！","admin_update.php");
	exit;
}
else
{
	$fp = fopen($verLockFile,'r');
	$verLocal = trim(fread($fp,64));
	fclose($fp);
	$oktimear = array(0,0,0,0,0);
	$oktimear = explode('.',$verLocal);
	$oktime = $oktime = $oktimear[2].'-'.$oktimear[3].'-'.$oktimear[4];
	include(sea_ADMIN.'/templets/admin_update.htm');
	exit();
}

function SaveToBin($content,$savefilename)
{
	$fp = fopen($savefilename,"w");
	fwrite($fp,$content);
	fclose($fp);
	return true;
}

function getrulevalue($content,$str)
{
	if(!empty($content) && !empty($str)){
		$labelRule = buildregx("<".$str.">(.*?)"."</".$str.">","is");
		preg_match_all($labelRule,$content,$ar);
		return $ar[1][0];
	}
}

function isUpdateFile($src,$updateTime)
{
	global $updatelogfile;
	$fp = fopen($updatelogfile,'r');
	$logFileStr = trim(fread($fp,filesize($updatelogfile)+1));
	fclose($fp);
	if(strpos($logFileStr,$src."----".$updateTime)>0) return true; else return false;
}

function updateFile($relativeFileUrl)
{
	global $updateHost,$zip,$DBUpdate;
	$localFile="../update/".$relativeFileUrl.'.zip';
	$result=$zip->Extract($localFile,'../');
	if(file_exists('../update.sql'))
	{
		if($DBUpdate->createFromFile('../update.sql'))
		@unlink('../update.sql');
		else
		{
			echo '<font color="#FF0000">'.$localFile.'</font>数据库更新失败<br>';
			exit();
		}	
	}
	if($result==-1||$result==-2)
	{echo '<font color="#FF0000">'.$localFile.'</font>更新失败<br><a href="#" onclick="history.go(-1)">返回</a>';exit();
	}
	echo '<font color="#FF0000">'.$localFile."</font>更新成功<br>";
}

function writeUpdateLog($srcAndUpdateTime)
{
	if(!isupdate($srcAndUpdateTime)){
		global $updatelogfile;
		$str="<updatelog>".$srcAndUpdateTime."</updatelog>";
		$fp = fopen($updatelogfile,'a');
		fwrite($fp,$str."\r\n");
		fclose($fp);
	}
}

function isupdate($srcAndUpdateTime)
{
	global $updatelogfile;
	$fp = fopen($updatelogfile,'r');
	$logFileStr = trim(fread($fp,10240));
	fclose($fp);
	if(strpos($logFileStr,$srcAndUpdateTime)>0) return true; else return false;
}

class zip{	
	   var $total_files = 0;
	   var $total_folders = 0; 
	   
	   function Extract ( $zn, $to, $index = Array(-1) )
	   {
		 $ok = 0; $zip = @fopen($zn,'rb');
		 if(!$zip) return(-1);
		 $cdir = $this->ReadCentralDir($zip,$zn);
		 $pos_entry = $cdir['offset'];
	  
		 if(!is_array($index)){ $index = array($index);  }
		 for($i=0; $index[$i];$i++){
			  if(intval($index[$i])!=$index[$i]||$index[$i]>$cdir['entries'])
			  return(-1);
		 }
		 for ($i=0; $i<$cdir['entries']; $i++)
		 {
		   @fseek($zip, $pos_entry);
		   $header = $this->ReadCentralFileHeaders($zip);
		   $header['index'] = $i; $pos_entry = ftell($zip);	
		   @rewind($zip); fseek($zip, $header['offset']);
		   if(in_array("-1",$index)||in_array($i,$index))
		   $stat[$header['filename']]=$this->ExtractFile($header, $to, $zip);
		 }
		 fclose($zip);
		 return $stat;
	   }
	   
	  
		function ReadFileHeader($zip)
		{
		  $binary_data = fread($zip, 30);
		  $data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);
	  
		  $header['filename'] = fread($zip, $data['filename_len']);
		  if ($data['extra_len'] != 0) {
			$header['extra'] = fread($zip, $data['extra_len']);
		  } else { $header['extra'] = ''; }
	  
		  $header['compression'] = $data['compression'];$header['size'] = $data['size'];
		  $header['compressed_size'] = $data['compressed_size'];
		  $header['crc'] = $data['crc']; $header['flag'] = $data['flag'];
		  $header['mdate'] = $data['mdate'];$header['mtime'] = $data['mtime'];
	  
		  if ($header['mdate'] && $header['mtime']){
		   $hour=($header['mtime']&0xF800)>>11;$minute=($header['mtime']&0x07E0)>>5;
		   $seconde=($header['mtime']&0x001F)*2;$year=(($header['mdate']&0xFE00)>>9)+1980;
		   $month=($header['mdate']&0x01E0)>>5;$day=$header['mdate']&0x001F;
		   $header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		  }else{$header['mtime'] = time();}
	  
		  $header['stored_filename'] = $header['filename'];
		  $header['status'] = "ok";
		  return $header;
		}
	  
	   function ReadCentralFileHeaders($zip){
		  $binary_data = fread($zip, 46);
		  $header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);
	  
		  if ($header['filename_len'] != 0)
			$header['filename'] = fread($zip,$header['filename_len']);
		  else $header['filename'] = '';
	  
		  if ($header['extra_len'] != 0)
			$header['extra'] = fread($zip, $header['extra_len']);
		  else $header['extra'] = '';
	  
		  if ($header['comment_len'] != 0)
			$header['comment'] = fread($zip, $header['comment_len']);
		  else $header['comment'] = '';
	  
		  if ($header['mdate'] && $header['mtime'])
		  {
			$hour = ($header['mtime'] & 0xF800) >> 11;
			$minute = ($header['mtime'] & 0x07E0) >> 5;
			$seconde = ($header['mtime'] & 0x001F)*2;
			$year = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month = ($header['mdate'] & 0x01E0) >> 5;
			$day = $header['mdate'] & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		  } else {
			$header['mtime'] = time();
		  }
		  $header['stored_filename'] = $header['filename'];
		  $header['status'] = 'ok';
		  if (substr($header['filename'], -1) == '/')
			$header['external'] = 0x41FF0010;
		  return $header;
	   }
	  
	   function ReadCentralDir($zip,$zip_name){
		  $size = filesize($zip_name);
	  
		  if ($size < 277) $maximum_size = $size;
		  else $maximum_size=277;
		  
		  @fseek($zip, $size-$maximum_size);
		  $pos = ftell($zip); $bytes = 0x00000000;
		  
		  while ($pos < $size){
			  $byte = @fread($zip, 1); $bytes=($bytes << 8) | ord($byte);
			  if ($bytes == 0x504b0506 or $bytes == 0x2e706870504b0506){ $pos++;break;} $pos++;
		  }
		  
		  $fdata=fread($zip,18);
		  
		  $data=@unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',$fdata);
		  
		  if ($data['comment_size'] != 0) $centd['comment'] = fread($zip, $data['comment_size']);
		  else $centd['comment'] = ''; $centd['entries'] = $data['entries'];
		  $centd['disk_entries'] = $data['disk_entries'];
		  $centd['offset'] = $data['offset'];$centd['disk_start'] = $data['disk_start'];
		  $centd['size'] = $data['size'];  $centd['disk'] = $data['disk'];
		  return $centd;
		}
	  
	   function ExtractFile($header,$to,$zip){
		  $header = $this->readfileheader($zip);
		  $header = $this->replaceDir($header);
		  if(substr($to,-1)!="/") $to.="/";
		  if($to=='./') $to = '';	
		  $pth = explode("/",$to.$header['filename']);
		  $mydir = '';
		  for($i=0;$i<count($pth)-1;$i++){
			  if(!$pth[$i]) continue;
			  $mydir .= $pth[$i]."/";
			  if((!is_dir($mydir) && @mkdir($mydir,0777)) || (($mydir==$to.$header['filename'] || ($mydir==$to && $this->total_folders==0)) && is_dir($mydir)) ){
				  @chmod($mydir,0777);
				  $this->total_folders ++;
			  }
		  }
		  
		  if(strrchr($header['filename'],'/')=='/') return;	
	  
		  if (!($header['external']==0x41FF0010)&&!($header['external']==16)){
			  if ($header['compression']==0){
				  $fp = @fopen($to.$header['filename'], 'wb');
				  if(!$fp) return(-1);
				  $size = $header['compressed_size'];
			  
				  while ($size != 0){
					  $read_size = ($size < 2048 ? $size : 2048);
					  $buffer = fread($zip, $read_size);
					  $binary_data = pack('a'.$read_size, $buffer);
					  @fwrite($fp, $binary_data, $read_size);
					  $size -= $read_size;
				  }
				  fclose($fp);
				  touch($to.$header['filename'], $header['mtime']);
			  }else{
				  $fp = @fopen($to.$header['filename'].'.gz','wb');
				  if(!$fp) return(-1);
				  $binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']),
				  Chr(0x00), time(), Chr(0x00), Chr(3));
				  
				  fwrite($fp, $binary_data, 10);
				  $size = $header['compressed_size'];
			  
				  while ($size != 0){
					  $read_size = ($size < 1024 ? $size : 1024);
					  $buffer = fread($zip, $read_size);
					  $binary_data = pack('a'.$read_size, $buffer);
					  @fwrite($fp, $binary_data, $read_size);
					  $size -= $read_size;
				  }
			  
				  $binary_data = pack('VV', $header['crc'], $header['size']);
				  fwrite($fp, $binary_data,8); fclose($fp);
		  
				  $gzp = @gzopen($to.$header['filename'].'.gz','rb') or die("Cette archive est compress閑");
				  if(!$gzp) return(-2);
				  $fp = @fopen($to.$header['filename'],'wb');
				  if(!$fp) return(-1);
				  $size = $header['size'];
			  
				  while ($size != 0){
					  $read_size = ($size < 2048 ? $size : 2048);
					  $buffer = gzread($gzp, $read_size);
					  $binary_data = pack('a'.$read_size, $buffer);
					  @fwrite($fp, $binary_data, $read_size);
					  $size -= $read_size;
				  }
				  fclose($fp); gzclose($gzp);
			  
				  touch($to.$header['filename'], $header['mtime']);
				  @unlink($to.$header['filename'].'.gz');
				  
			  }
		  }
		  
		  $this->total_files ++;
		  return true;
	   }
	   
	   function replaceDir(&$header)
	   {
		   	   
		   $adminDir = preg_replace("|(.*)[/\\\]|is","",dirname(__FILE__));
		   $header['filename'] = str_replace("list/",$GLOBALS['cfg_channel_name']."/",$header['filename']);
		   $header['filename'] = str_replace("detail/",$GLOBALS['cfg_content_name']."/",$header['filename']);
		   $header['filename'] = str_replace("video/",$GLOBALS['cfg_play_name']."/",$header['filename']);
		   $header['filename'] = str_replace("topic/",$GLOBALS['cfg_album_name']."/",$header['filename']);
		   $header['filename'] = str_replace("topiclist/",$GLOBALS['cfg_filesuffix']."/",$header['filename']);
		   $header['filename'] = str_replace("news/",$GLOBALS['cfg_news_name']."/",$header['filename']);
		   $header['filename'] = str_replace("article/",$GLOBALS['cfg_article_name']."/",$header['filename']);
		   $header['filename'] = str_replace("articlelist/",$GLOBALS['cfg_newspart_name']."/",$header['filename']);
		   if(preg_match("|^(admin/)|i",$header['filename'])){
				$header['filename'] = str_replace("admin/",$adminDir."/",$header['filename']);
		   }
		   
		   return $header;
	   }
	  
	  // end class
}
?>