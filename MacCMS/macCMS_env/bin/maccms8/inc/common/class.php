<?php
class AppDb
{
	var $sql_id;
	var $sql_qc=0;
	
	function AppDb($dbhost, $dbuser, $dbpw, $dbName = '', $charset = 'utf8',$newlink=false)
	{
		if(!$this->sql_id=@mysql_connect($dbhost, $dbuser, $dbpw, $newlink)) {
			showErr("DataBase","MYSQL 连接数据库失败,请确定数据库用户名,密码设置正确<br>");
		}
		if(!@mysql_select_db($dbName,$this->sql_id)){
			showErr("DataBase","MYSQL 连接成功,但当前使用的数据库 {$dbName} 不存在<br>");
		}
		
		if( mysql_get_server_info($this->sql_id) > '4.1' ){
			if($charset){
				mysql_query("SET character_set_connection=$charset,character_set_results=$charset,character_set_client=binary",$this->sql_id);
			}
			else{
				mysql_query("SET character_set_client=binary",$this->sql_id);
			}
			if( mysql_get_server_info($this->sql_id) > '5.0' ){
				mysql_query("SET sql_mode=''",$this->sql_id);
			}
		}
		else{
			showErr("DataBase","本系统仅支持MYSQL4.1以上版本");
		}
	}
	
	function oldAppDb($dbhost, $dbuser, $dbpw, $dbName = '', $charset = 'utf8',$newlink=false)
	{
		if(!($this->sql_id = mysql_connect($dbhost, $dbuser, $dbpw ,$newlink))){
			showErr("DataBase","Can't pConnect MySQL Server($dbhost)!");
		}
		mysql_query("SET NAMES " . $charset, $this->sql_id);
		mysql_query("SET character_set_client " . $charset, $this->sql_id);
		mysql_query("SET character_set_results " . $charset, $this->sql_id);
		@mysql_query($this->sql_id);
		if ($dbName){
			if (mysql_select_db($dbName, $this->sql_id) === false ){
				showErr("DataBase","Can't select MySQL database($dbName)!");
				return false;
			}
			else{
				return true;
			}
		}
	}
	
	function close() {
		$this->sql_qc=0;
		return mysql_close($this->sql_id);
	}
	
	function select_database($dbName)
	{
		return mysql_select_db($dbName, $this->sql_id);
	}
	
	function fetch_array($query, $result_type = MYSQL_ASSOC)
	{
		return mysql_fetch_array($query, $result_type);
	}
	
	function query($sql)
	{
		$this->sql_qc++;
		$sql = str_replace("{pre}",$GLOBALS['MAC']['db']['tablepre'],$sql);
		//echo $sql."<br>";
		return mysql_query($sql, $this->sql_id);
	}
	
	function queryArray($sql,$keyf='')
	{
		$array = array();
		$result = $this->query($sql);
		while($r = $this->fetch_array($result))
		{
			if($keyf){
				$key = $r[$keyf];
				$array[$key] = $r;
			}
			else{
				$array[] = $r;
			}
		}
		return $array;
	}
	
	function affected_rows()
	{
		return mysql_affected_rows($this->sql_id);
	}
	
	function num_rows($query)
	{
		return mysql_num_rows($query);
	}
	
	function insert_id()
	{
		return mysql_insert_id($this->sql_id);
	}
	
	function selectLimit($sql, $num, $start = 0)
	{
		if ($start == 0){
			$sql .= ' LIMIT ' . $num;
		}
		else{
			$sql .= ' LIMIT ' . $start . ', ' . $num;
		}
		return $this->query($sql);
	}
	
	function getOne($sql, $limited = false)
	{
		if ($limited == true){
			$sql = trim($sql . ' LIMIT 1');
		}
		$res = $this->query($sql);
		if ($res !== false){
			$row = mysql_fetch_row($res);
			return $row[0];
		}
		else{
			return false;
		}
	}
	function getRow($sql)
	{
		$res = $this->query($sql);
		if ($res !== false){
			return mysql_fetch_assoc($res);
		}
		else{
			return false;
		}
	}
	
	function getAll($sql)
	{
		$res = $this->query($sql);
		if ($res !== false){
			$arr = array();
			while ($row = mysql_fetch_assoc($res)){
				$arr[] = $row;
			}
			return $arr;
		}
		else{
			return false;
		}
	}
	
	function getTableFields($dbName,$tabName)
	{
		$tabName = str_replace("{pre}",$GLOBALS['MAC']['db']['tablepre'],$tabName);
		return mysql_list_fields($dbName,$tabName,$this->sql_id);
	}
	
	function Exist($tabName,$fieldName ,$ID)
	{
		$SqlStr="SELECT * FROM ".$tabName." WHERE ".$fieldName."=".$ID;
		$res=false;
		try{
			$row = $this->getRow($SqlStr);
			if($row){ $res=true; }
			unset($row);
		}
		catch(Exception $e){
		}
		return $res;
	}
	
	function AutoID($tabName,$colname)
	{
		$n = $this->getOne("SELECT Max(".$colname.") FROM [".$tabName."]");
		if (!isNum(n)){ $n=0; }
		return $n;
	}
	
	function Add($tabName,$arrFieldName ,$arrValue)
	{
		$res=false;
		if (chkArray($arrFieldName,$arrValue)){
			$sqlcol = "";
			$sqlval = "";
			$rc=false;
			foreach($arrFieldName as $a){
				if($rc){ $sqlcol.=",";}
				$sqlcol .= $a;
				$rc=true;
			}
			$rc=false;
			foreach($arrValue as $b){
				if($rc){ $sqlval.=",";}
				$sqlval .= "'".$b."'";
				$rc=true;
			}
			$sql = " INSERT INTO " . $tabName." (".$sqlcol.") VALUES(".$sqlval.")" ;
			//echo $sql."<br>";exit;
			$res = $this->query($sql);
			if($res){
				//echo "ok";
			}
			else{
				//echo "err";
			}
		}
		return $res;
	}
	
	function Update($tabName,$arrFieldName , $arrValue ,$KeyStr,$f=0)
	{
		$res=false;
		if (chkArray($arrFieldName,$arrValue)){
			$sqlval = "";
			$rc=false;
			
			for($i=0;$i<count($arrFieldName);$i++){
				if($rc){ $sqlval.=",";}
				if($f==0){
					$sqlval .= $arrFieldName[$i]."='".$arrValue[$i]."'";
				}
				else{
					$sqlval .= $arrFieldName[$i]."='".$arrValue[$arrFieldName[$i]]."'";
				}
				$rc=true;
			}
			$sql = " UPDATE " . $tabName." SET ".$sqlval." WHERE ".$KeyStr."";
			//echo $sql."<br>";exit;
			$res = $this->query($sql);
			if($res){
				//echo "ok";
			}
			else{
				//echo "err";
			}
		}
		return $res;
	}
	
	function Delete($tabName,$KeyStr)
	{
		$res=false;
		$sql = "DELETE FROM ".$tabName." WHERE ".$KeyStr;
		$res = $this->query($sql);
		return $res;
	}
	
}

class AppFtp{
	var $ftpUrl = "127.0.0.1";
	var $ftpUser = "maccms";
	var $ftpPass = "123456";
	var $ftpDir = "/wwwroot/";
	var $ftpPort = "21";
	var $ftpR = ''; //R ftp资源;
	var $ftpStatus = 0;
	var $ftpStatusDes = "";
	//R 1:成功;2:无法连接ftp; 3:用户错误;
	function AppFtp($ftpUrl="", $ftpUser="", $ftpPass="",  $ftpPort="",  $ftpDir="") {
		if($ftpUrl){
			$this->ftpUrl=$ftpUrl;
		}
		if($ftpUser){
			$this->ftpUser=$ftpUser;
		}
		if($ftpPass){
			$this->ftpPass=$ftpPass;
		}
		if($ftpUrl){
			$this->ftpDir=$ftpDir;
		}
		if($ftpPort){
			$this->ftpPost=$ftpPort;
		}
	   if ($this->ftpR = @ftp_connect($this->ftpUrl, $this->ftpPost)) {
	     if (@ftp_login($this->ftpR, $this->ftpUser, $this->ftpPass)) {
			if (!empty($this->ftpDir)) {
				@ftp_chdir($this->ftpR, $this->ftpDir);
			}
	     	@ftp_pasv($this->ftpR, true);
	     	$this->ftpStatus = 1;
	     	$this->ftpStatusDes = "连接ftp成功";
	     }
	     else {
	     	$this->ftpStatus = 3;
	     	$this->ftpStatusDes = "连接ftp成功，但用户或密码错误";
	     }
	   }
	   else {
	     $this->ftpStatus = 2;
	     $this->ftpStatusDes = "连接ftp失败";
	   }
	}

	//R 切换目录;
	function cd($dir) {
	   return ftp_chdir($this->ftpR, $dir);
	}
	//R 返回当前路劲;
	function pwd() {
	   return ftp_pwd($this->ftpR);
	}
	function mkdirs($path)
	{
		$path_arr  = explode('/',$path);
		$file_name = array_pop($path_arr); 
		$path_div  = count($path_arr); 
		foreach($path_arr as $val)
		{
			if(@ftp_chdir($this->ftpR,$val) == FALSE)
			{
				$tmp = @ftp_mkdir($this->ftpR,$val);
				if($tmp == FALSE)
				{
					echo "目录创建失败，请检查权限及路径是否正确！";
					exit;
				}
				@ftp_chdir($this->ftpR,$val);
			}
		}
		for($i=1;$i<=$path_div;$i++)
		{
			@ftp_cdup($this->ftpR);
		}
	}

	//R 创建目录
	function mkdir($directory) {
	   return ftp_mkdir($this->ftpR,$directory);
	}
	//R 删除目录
	function rmdir($directory) {
	   return ftp_rmdir($this->ftpR,$directory);
	}
	//R 上传文件;
	function put($localFile, $remoteFile = ''){
	   if ($remoteFile == '') {
	     $remoteFile = end(explode('/', $localFile));
	   }
	   $res = ftp_nb_put($this->ftpR, $remoteFile, $localFile, FTP_BINARY);
	   while ($res == FTP_MOREDATA) {
	     $res = ftp_nb_continue($this->ftpR);
	   }
	   if ($res == FTP_FINISHED) {
	     return true;
	   } elseif ($res == FTP_FAILED) {
	     return false;
	   }
	}
	//R 下载文件;
	function get($remoteFile, $localFile = '') {
	   if ($localFile == '') {
	     $localFile = end(explode('/', $remoteFile));
	   }
	   if (ftp_get($this->ftpR, $localFile, $remoteFile, FTP_BINARY)) {
	     $flag = true;
	   } else {
	     $flag = false;
	   }
	   return $flag;
	}
	//R 文件大小;
	function size($file) {
	   return ftp_size($this->ftpR, $file);
	}
	//R 文件是否存在;
	function isFile($file) {
	   if ($this->size($file) >= 0) {
	     return true;
	   } else {
	     return false;
	   }
	}
	//R 文件时间
	function fileTime($file) {
	   return ftp_mdtm($this->ftpR, $file);
	}
	//R 删除文件;
	function unlink($file) {
	   return ftp_delete($this->ftpR, $file);
	}
	function nlist($dir = '/service/resource/') {
	   return ftp_nlist($this->ftpR, $dir);
	}
	//R 关闭连接;
	function bye() {
	   return ftp_close($this->ftpR);
	}
}

class AppZip
{
	var $total_files = 0;
	var $total_folders = 0;
	
	function Extract($zn, $to, $index = Array(-1) )
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
	$filename = $header['filename'];
	$filename = str_replace('admin/',$GLOBALS['adpath'].'/',$filename);
	if(substr($to,-1)!="/") $to.="/";
	if($to=='./') $to = '';	
	$pth = explode("/",$to.$filename);
	$mydir = '';
	
	
	for($i=0;$i<count($pth)-1;$i++){
		if(!$pth[$i]) continue;
		$mydir .= $pth[$i]."/";
		if((!is_dir($mydir) && @mkdir($mydir,0777)) || (($mydir==$to.$filename || ($mydir==$to && $this->total_folders==0)) && is_dir($mydir)) ){
			@chmod($mydir,0777);
			$this->total_folders ++;
			echo "目录: $mydir\n";
			ob_flush();flush();
		}
	}
	
	
	
	if(strrchr($filename,'/')=='/') return;	
	if (!($header['external']==0x41FF0010)&&!($header['external']==16)){
		if ($header['compression']==0){
			$fp = @fopen($to.$filename, 'wb');
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
			@touch($to.$filename, $header['mtime']);
		}
		else{
			$fp = @fopen($to.$filename.'.gz','wb');
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
			$gzp = @gzopen($to.$filename.'.gz','rb') or die("Cette archive est compress");
			if(!$gzp) return(-2);
			$fp = @fopen($to.$filename,'wb');
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
			@touch($to.$filename, $header['mtime']);
			@unlink($to.$filename.'.gz');
		}
	}
	$this->total_files ++;
	echo "文件: $to$filename\n";
	ob_flush();flush();
	return true;
 }
}
?>