<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}

$dsql->SetQuery("Show Tables");
$dsql->Execute('t');
while($row = $dsql->GetArray('t',MYSQL_BOTH))
{
	if(m_ereg("^{$cfg_dbprefix}",$row[0]))
	{
		$tables[] = $row[0];
	}
}
	
$bkdir = sea_DATA.'/'.$cfg_backup_dir;
if(!is_dir($bkdir))
{
	MkdirAll($bkdir,$cfg_dir_purview);
}
//初始化使用到的变量
if(!isset($startpos)) $startpos = 0;
if(empty($fsize)) $fsize = 2048;
$fsizeb = $fsize * 1024;
//第一页的操作
$dh = dir($bkdir);
while($filename=$dh->read())
{
	if(!m_ereg("txt$",$filename))
	{
		continue;
	}
	$filename = $bkdir."/$filename";
	if(!is_dir($filename))
	{
		unlink($filename);
	}
}
$dh->close();
$bkfile = $bkdir."/tables_struct_".substr(md5(time().mt_rand(1000,5000).$cfg_cookie_encode),0,16).".txt";
$mysql_version = $dsql->GetVersion();
$fp = fopen($bkfile,"w");
foreach($tables as $t)
{
	fwrite($fp,"DROP TABLE IF EXISTS `$t`;\r\n\r\n");
	$dsql->SetQuery("SHOW CREATE TABLE ".$dsql->dbName.".".$t);
	$dsql->Execute('me');
	$row = $dsql->GetArray('me',MYSQL_BOTH);

	//去除AUTO_INCREMENT
	$row[1] = m_eregi_replace("AUTO_INCREMENT=([0-9]{1,})[ \r\n\t]{1,}","",$row[1]);

	//4.1以下版本备份为低版本
	if($mysql_version < 4.0)
	{
		$eng1 = "ENGINE=MyISAM[ \r\n\t]{1,}DEFAULT[ \r\n\t]{1,}CHARSET=".$cfg_db_language;
		$tableStruct = m_eregi_replace($eng1,"TYPE=MyISAM",$row[1]);
	}

	//4.1以上版本备份为高版本
	else if($mysql_version > 4.1)
	{
		$eng1 = "ENGINE=MyISAM DEFAULT CHARSET={$cfg_db_language}";
		$tableStruct = m_eregi_replace("TYPE=MyISAM",$eng1,$row[1]);
	}

	//普通备份
	else
	{
		$tableStruct = $row[1];
	}
	fwrite($fp,''.$tableStruct.";\r\n\r\n");
}
fclose($fp);

for($i=0;$i<count($tables);$i++)
{
	$nowtable=$tables[$i];
	$j = 0;
	$fs = $bakStr = '';

	//分析表里的字段信息
	$dsql->GetTableFields($nowtable);
	$intable = "INSERT INTO `$nowtable` VALUES(";
	while($r = $dsql->GetFieldObject())
	{
		$fs[$j] = trim($r->name);
		$j++;
	}
	$fsd = $j-1;

	//读取表的内容
	$dsql->SetQuery("Select * From `$nowtable` ");
	$dsql->Execute();
	$m = 0;
	$bakfilename = "$bkdir/{$nowtable}_{$startpos}_".substr(md5(time().mt_rand(1000,5000).$cfg_cookie_encode),0,16).".txt";
	while($row2 = $dsql->GetArray())
	{
		if($m < $startpos)
		{
			$m++;
			continue;
		}

		//检测数据是否达到规定大小
		if(strlen($bakStr) > $fsizeb)
		{
			$fp = fopen($bakfilename,"w");
			fwrite($fp,$bakStr);
			fclose($fp);
		}

		//正常情况
		$line = $intable;
		for($j=0;$j<=$fsd;$j++)
		{
			if($j < $fsd)
			{
				$line .= "'".autoRpLine(addslashes($row2[$fs[$j]]))."',";
			}
			else
			{
				$line .= "'".autoRpLine(addslashes($row2[$fs[$j]]))."');\r\n";
			}
		}
		$m++;
		$bakStr .= $line;
	}

	//如果数据比卷设置值小
	if($bakStr!='')
	{
		$fp = fopen($bakfilename,"w");
		fwrite($fp,$bakStr);
		fclose($fp);
	}
}
	
function autoRpLine($str)
{
	$str = str_replace("\r","\\r",$str);
	$str = str_replace("\n","\\n",$str);
	return $str;
}

