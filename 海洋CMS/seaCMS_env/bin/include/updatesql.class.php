<?php
if(!defined('sea_INC'))
{
	exit("Request Error!");
}
//包含函数库
require_once(  dirname(__FILE__).'/inc/mysql.php' );
$DBUpdate = new DBManager;
class DBManager
{
    var $dbHost = '';
    var $dbUser = '';
    var $dbPassword = '';
    var $dbSchema = '';
	var $prefix = '';
   
    function __construct()
    {
        $this->dbHost = $GLOBALS['cfg_dbhost'];
        $this->dbUser = $GLOBALS['cfg_dbuser'];
        $this->dbPassword = $GLOBALS['cfg_dbpwd'];
        $this->dbSchema = $GLOBALS['cfg_dbname'];
        $this->prefix = $GLOBALS['cfg_dbprefix'];
    }
   
    function createFromFile($sqlPath,$delimiter = '(;\n)|((;\r\n))|(;\r)',$commenter = array('#','--'))
    {
        //判断文件是否存在
        if(!file_exists($sqlPath))
            return false;
       
        $handle = fopen($sqlPath,'rb');   
       
        $sqlStr = fread($handle,filesize($sqlPath));
       
        //通过sql语法的语句分割符进行分割
        $segment = explode(";",trim($sqlStr));
       
        //var_dump($segment);
       
        //去掉注释和多余的空行
        foreach($segment as & $statement)
        {
            $sentence = explode("\n",$statement);
           
            $newStatement = array();
           
            foreach($sentence as $subSentence)
            {
                if('' != trim($subSentence))
                {
                    //判断是会否是注释
                    $isComment = false;
                    foreach($commenter as $comer)
                    {
                        if(preg_match("/^(".$comer.")/i",trim($subSentence)))
                        {
                            $isComment = true;
                            break;
                        }
                    }
                    //如果不是注释，则认为是sql语句
                    if(!$isComment)
                        $newStatement[] = $subSentence;                   
                }
            }
           
            $statement = $newStatement;
        }    
        //组合sql语句
        foreach($segment as & $statement)
        {
            $newStmt = '';
            foreach($statement as $sentence)
            {
                $newStmt = $newStmt.trim($sentence)."\n";
            }
               
            $statement = $newStmt;
        }
       
        self::saveByQuery($segment);
       
        return true;
    }
   
    private function saveByQuery($sqlArray)
    {
        $conn = mysql_connect($this->dbHost,$this->dbUser,$this->dbPassword);
       
        my_select_db($conn,$this->dbSchema);
       
        foreach($sqlArray as $sql)
        {
			$sql = str_replace('sea_',$this->prefix,$sql);
            mysql_query($sql);
        }       
        mysql_close($conn);
    }
   
}
