<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
class AppFtp{
	var $ftpUrl = "127.0.0.1";
	var $ftpUser = "seacms";
	var $ftpPass = "123456789";
	var $ftpDir = "/www/";
	var $ftpPort = "21";
	var $ftpR = ''; 
	var $ftpStatus = 0;
	var $ftpStatusDes = "";
	
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
	   if ($this->ftpR = ftp_connect($this->ftpUrl, $this->ftpPost)) {
		 if (ftp_login($this->ftpR, $this->ftpUser, $this->ftpPass)) {
			if (!empty($this->ftpDir)) {
				ftp_chdir($this->ftpR, $this->ftpDir);
			}
			ftp_pasv($this->ftpR, true);
			$this->ftpStatus = 1;
			$this->ftpStatusDes = "连接ftp成功";
		 }
		 else {
			$this->ftpStatus = 3;
			$this->ftpStatusDes = "连接ftp用户或密码错误";
		 }
	   }
	   else {
		 $this->ftpStatus = 2;
		 $this->ftpStatusDes = "连接ftp失败";
	   }
	}
	
	
	function cd($dir) {
	   return ftp_chdir($this->ftpR, $dir);
	}
	
	function pwd() {
	   return ftp_pwd($this->ftpR);
	}
	function mkdirs($path)
	{
		$path_arr  = explode('/',$path);
		$file_name = array_pop($path_arr); 
		$path_div  = count($path_arr); 
		$tmpdir = '';
		foreach($path_arr as $val)
		{
			$tmpdir .= '/'.$val;
			$tmpdir  = str_replace('//','/',$tmpdir);
			if(@ftp_chdir($this->ftpR,$tmpdir) == FALSE)
			{
				$tmp = @ftp_mkdir($this->ftpR,$tmpdir);
				if($tmp == FALSE)
				{
					echo "目录创建失败，请检查权限及路径是否正确！";
					exit;
				}
				@ftp_chdir($this->ftpR,$tmpdir);
			}
		}
		for($i=1;$i<=$path_div;$i++)
		{
			@ftp_cdup($this->ftpR);
		}
	}
	
	function mkdir($directory) {
	   return ftp_mkdir($this->ftpR,$directory);
	}
	
	function rmdir($directory) {
	   return ftp_rmdir($this->ftpR,$directory);
	}
	
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
	
	function size($file) {
	   return ftp_size($this->ftpR, $file);
	}
	
	function isFile($file) {
	   if ($this->size($file) >= 0) {
		 return true;
	   } else {
		 return false;
	   }
	}
	
	function fileTime($file) {
	   return ftp_mdtm($this->ftpR, $file);
	}
	
	function unlink($file) {
	   return ftp_delete($this->ftpR, $file);
	}
	
	function nlist($dir = '/service/resource/') {
	   return ftp_nlist($this->ftpR, $dir);
	}
	
	function bye() {
	   return ftp_close($this->ftpR);
	}
}
?>