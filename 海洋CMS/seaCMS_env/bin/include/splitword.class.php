<?php
/*******************************
//带词性标志的算法，使用方法：
//普通分词 tryNumName，识别数量词及人名，$tryDiff 岐义处理
//$strok = $sp->SplitRMM(string $str,bool $tryNumName,bool $tryDiff)
//本文件必须保持ASCII编码
	********************************/
class SplitWord
{
	var $RankDic = Array();
	var $OneNameDic = Array();
	var $TwoNameDic = Array();
	var $NewWord = Array();
	var $SourceString = '';
	var $ResultString = '';
	var $SplitChar = ' '; //分隔符
	var $SplitLen = 4; //保留词长度
	var $EspecialChar = "和|的|是";
	var $NewWordLimit = "在|的|与|或|就|你|我|他|她|有|了|是|其|能|对|地";

	//这里可以按需要加入常用的量词，
	//程序会检测词语第一个字是否为这些词和上一个词是否为数词，然后结合为单词
	var $CommonUnit = "年|月|日|时|分|秒|点|元|百|千|万|亿|位|辆";

	var $CnNumber = "０|１|２|３|４|５|６|７|８|９|＋|－|％|．|ａ|ｂ|ｃ|ｄ|ｅ|ｆ|ｇ|ｈ|ｉ|ｊ|ｋ|ｌ|ｍ|ｎ|ｏ|ｐ|ｑ|ｒ|ｓ |ｔ|ｕ|ｖ|ｗ|ｘ|ｙ|ｚ|Ａ|Ｃ|Ｄ|Ｅ|Ｆ|Ｇ|Ｈ|Ｉ|Ｊ|Ｋ|Ｌ|Ｍ|Ｎ|Ｏ|Ｐ|Ｑ|Ｒ|Ｓ|Ｔ|Ｕ|Ｖ|Ｗ|Ｘ|Ｙ|Ｚ";
	var $CnSgNum = "一|二|三|四|五|六|七|八|九|十|百|千|万|亿|数";
	var $MaxLen = 13; //词典最大 7 中文字，这里的数值为字节数组的最大索引
	var $MinLen = 3;  //最小 2 中文字，这里的数值为字节数组的最大索引
	var $CnTwoName = "端木 南宫 谯笪 轩辕 令狐 钟离 闾丘 长孙 鲜于 宇文 司徒 司空 上官 欧阳 公孙 西门 东门 左丘 东郭 呼延 慕容 司马 夏侯 诸葛 东方 赫连 皇甫 尉迟 申屠";
	var $CnOneName = "赵钱孙李周吴郑王冯陈褚卫蒋沈韩杨朱秦尤许何吕施张孔曹严华金魏陶姜戚谢邹喻柏水窦章云苏潘葛奚范彭郎鲁韦昌马苗凤花方俞任袁柳酆鲍史唐费廉岑薛雷贺倪汤滕殷罗毕郝邬安常乐于时傅皮卡齐康伍余元卜顾孟平黄穆萧尹姚邵堪汪祁毛禹狄米贝明臧计伏成戴谈宋茅庞熊纪舒屈项祝董粱杜阮蓝闵席季麻强贾路娄危江童颜郭梅盛林刁钟徐邱骆高夏蔡田樊胡凌霍虞万支柯咎管卢莫经房裘缪干解应宗宣丁贲邓郁单杭洪包诸左石崔吉钮龚程嵇邢滑裴陆荣翁荀羊於惠甄魏加封芮羿储靳汲邴糜松井段富巫乌焦巴弓牧隗谷车侯宓蓬全郗班仰秋仲伊宫宁仇栾暴甘钭厉戎祖武符刘姜詹束龙叶幸司韶郜黎蓟薄印宿白怀蒲台从鄂索咸籍赖卓蔺屠蒙池乔阴郁胥能苍双闻莘党翟谭贡劳逄姬申扶堵冉宰郦雍郤璩桑桂濮牛寿通边扈燕冀郏浦尚农温别庄晏柴翟阎充慕连茹习宦艾鱼容向古易慎戈廖庚终暨居衡步都耿满弘匡国文寇广禄阙东殴殳沃利蔚越夔隆师巩厍聂晁勾敖融冷訾辛阚那简饶空曾沙须丰巢关蒯相查后江游竺";
  //------------------------------
  //php4构造函数
  //------------------------------
  function SplitWord($loaddic=true){
  	$this->__construct($loaddic);
  }
  //------------------------------
  //php5构造函数
  //------------------------------
  function __construct($loaddic=true){
  	if($loaddic)
  	{
  	  //载入姓氏词典
  	  for($i=0;$i<strlen($this->CnOneName);$i++){
  		  $this->OneNameDic[$this->CnOneName[$i].$this->CnOneName[$i+1]] = 1;
  		  $i++;
  	  }
  	  $twoname = explode(" ",$this->CnTwoName);
  	  foreach($twoname as $n){ $this->TwoNameDic[$n] = 1; }
  	  unset($twoname);
  	  unset($this->CnTwoName);
  	  unset($this->CnOneName);
  	  //高级分词，预先载入词典以提分词高速度
  	  $dicfile = dirname(__FILE__)."/data/ek-wwwdic.dat";
  	  $fp = fopen($dicfile,'r');
  	  while($line = fgets($fp,64)){
  		  $ws = explode(' ',$line);
  		  $this->RankDic[strlen($ws[0])][$ws[0]] = $ws[1];
  	  }
  	  fclose($fp);
    }//是否载入词典，如果不需要用分词功能，可以不载入。
  }

  //--------------------------
  //析放资源
  //--------------------------
  function Clear()
  {
  	unset($this->RankDic);
  }

  //----------------------------
  //设置源字符串
  //----------------------------
  function SetSource($str)
  {
  	$this->SourceString = $str;
  	$this->ResultString = '';
  }

  //--------------------
  //简单分词
  //仅作词典匹配
  //--------------------
  function SimpleSplit($str)
  {
  	//对文本进行粗分
  	$this->SourceString = $this->ReviseString($str);

  	return $this->SourceString;
  }

  //-----------------------------
  //RMM分词算法
  //-----------------------------
  function SplitRMM($str='',$tryNumName=true,$tryDiff=true)
  {
  	$str = trim($str);

  	//SetSource中不对$str作任何处理
  	if($str!='') $this->SetSource($str);
  	else return '';

  	//对文本进行粗分
  	$this->SourceString = preg_replace('/ {1,}/',' ',$this->ReviseString($this->SourceString));

  	//对特定文本进行分离
  	$spwords = explode(' ',$this->SourceString);
  	$spLen = count($spwords) - 1;
  	$spc = $this->SplitChar;
  	for($i=$spLen;$i>=0;$i--)
  	{
  		//空白符号
  		if(ord($spwords[$i][0])<33) continue;
  		//小于最小词不处理
  		else if(!isset($spwords[$i][$this->MinLen])) $this->ResultString = $spwords[$i].$spc.$this->ResultString;
  		//判断开头大于0x80认为是gbk串，否则为英文串(粗分中已作处理)
  		else if(ord($spwords[$i][0])<0x81)
  		{
  			$this->ResultString = $spwords[$i].$spc.$this->ResultString;
  		}
  		//正常短句进行分词处理
  		else
  		{
  		  $this->ResultString = $this->RunRMM($spwords[$i],$tryNumName,$tryDiff).$spc.$this->ResultString;
  	  }
  	}
	$okstr = $this->ResultString;

  	return $okstr;
  }
  //------------------------
  //对常规数量词进行识别
  //------------------------
  function ParNumber($str)
  {
  	if($str == '') return '';
  	$ws = explode(' ',$str);
  	$wlen = count($ws);
  	$spc = $this->SplitChar;
  	$reStr = '';
  	for($i=0;$i<$wlen;$i++){
  		if($ws[$i]=='') continue;
  		if($i>=$wlen-1) $reStr .= $spc.$ws[$i];
  		else{ $reStr .= $spc.$ws[$i]; }
    }
    return $reStr;
  }
  //-------------------------------
  //进行名字识别和其它数词识别
  //--------------------------------
  function ParOther($WordArray)
  {
  	$wlen = count($WordArray)-1;
  	$rsStr = '';
  	$spc = $this->SplitChar;
  	for($i=$wlen;$i>=0;$i--)
  	{
  		//数量词
  		if(preg_match('/'.$this->CnSgNum.'/',$WordArray[$i]))
  		{
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0 && preg_match('/^'.$this->CommonUnit.'/',$WordArray[$i-1]) )
  			{ $rsStr .= $WordArray[$i-1]; $i--; }
  			else
  			{
  				while($i>0 && preg_match("/".$this->CnSgNum."/",$WordArray[$i-1]) ){ $rsStr .= $WordArray[$i-1]; $i--; }
  			}
  			continue;
  		}
  		//双字姓
  		if(strlen($WordArray[$i])==4 && isset($this->TwoNameDic[$WordArray[$i]]))
  		{
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0&&strlen($WordArray[$i-1])==2){
  				$rsStr .= $WordArray[$i-1];$i--;
  				if($i>0&&strlen($WordArray[$i-1])==2){ $rsStr .= $WordArray[$i-1];$i--; }
  			}
  		}
  		//单字姓
  		else if(strlen($WordArray[$i])==2 && isset($this->OneNameDic[$WordArray[$i]]))
  		{
  			$rsStr .= $spc.$WordArray[$i];
  			if($i>0&&strlen($WordArray[$i-1])==2){
  				 if(preg_match("/".$this->EspecialChar."/",$WordArray[$i-1])) continue;
  				 $rsStr .= $WordArray[$i-1];$i--;
  				 if($i>0 && strlen($WordArray[$i-1])==2 &&
  				  !preg_match("/".$this->EspecialChar."/",$WordArray[$i-1]))
  				 { $rsStr .= $WordArray[$i-1];$i--; }
  			}
  		}
  		//普通词汇
  		else{
  			$rsStr .= $spc.$WordArray[$i];
  		}
  	}
  	//返回本段分词结果
  	$rsStr = preg_replace("/^".$spc."/","",$rsStr);
  	return $rsStr;
  }
   //对全中文字符串进行逆向匹配方式分解
  function RunRMM($str,$tryNumName=true,$tryDiff=true)
  {
  	$spc = $this->SplitChar;
  	$spLen = strlen($str);
  	$rsStr = $okWord = $tmpWord = '';
  	$WordArray = Array();
  	//逆向字典匹配
  	for($i=($spLen-1);$i>=0;)
  	{
  		//当i达到最小可能词的时候
  		if($i<=$this->MinLen){
  			if($i==1){
  			  $WordArray[] = substr($str,0,2);
  		  }else
  			{
  			   $w = substr($str,0,$this->MinLen+1);
  			   if($this->IsWord($w)){
  			   	$WordArray[] = $w;
  			   }else{
  				   $WordArray[] = substr($str,2,2);
  				   $WordArray[] = substr($str,0,2);
  			   }
  		  }
  			$i = -1; break;
  		}
  		//分析在最小词以上时的情况
  		if($i>=$this->MaxLen) $maxPos = $this->MaxLen;
  		else $maxPos = $i;
  		$isMatch = false;
  		for($j=$maxPos;$j>=0;$j=$j-2){
  			 $w = substr($str,$i-$j,$j+1);
  			 if($this->IsWord($w)){
  			 	$WordArray[] = $w;
  			 	$i = $i-$j-1;
  			 	$isMatch = true;
  			 	break;
  			 }
  		}
  		if(!$isMatch){
  			if($i>1) {
  				$WordArray[] = $str[$i-1].$str[$i];
  				$i = $i-2;
  			}
  		}
  	}//End For

  	//名字和数量词识别
  	if($tryNumName)
  	{ $rsStr = $this->ParOther($WordArray); }
  	else{
  		$wlen = count($WordArray)-1;
  		for($i=$wlen;$i>=0;$i--){
  	  	$rsStr .= $spc.$WordArray[$i];
  	  }
  	}

  	//消岐处理
  	if($tryDiff) $rsStr = $this->TestDiff(trim($rsStr));

  	return $rsStr;
  }

  //----------------------------
  //自动摘要功能
  //$keyword是指定的关键字或GetIndexText返回的内容
  //建议不要用太多的关键字
  //此功能未实现
  //----------------------------
  function AutoDescription($str,$keyword,$strlen)
  {
  	$this->SourceString = $this->ReviseString($this->SourceString);
  	//对特定文本进行分离
  	$spwords = explode(" ",$this->SourceString);
  	$keywords = explode(" ",$this->keywords);
  	$regstr = "";
  	foreach($keywords as $k=>$v)
  	{
  		if($v=="") continue;
  		if(ord($v[0])>0x80 && strlen($v)<3) continue;
  		if($regstr=="") $regstr .= "($v)";
  		else $regstr .= "|($v)";
  	}
  }

  //----------------------------------
  //对分词结果进行消岐处理
  //----------------------------------
  function TestDiff($str)
  {
  	$str = preg_replace("/ {1,}/"," ",$str);
  	if($str == ""||$str == " ") return "";
  	$ws = explode(' ',$str);
  	$wlen = count($ws);
  	$spc = $this->SplitChar;
  	$reStr = "";
  	for($i=0;$i<$wlen;$i++)
  	{
  		//循环到最后一个词不处理
  		if($i>=($wlen-1)) {
  			$reStr .= $spc.$ws[$i];
  		}
  		//其它词的处理
  		else
  		{
  			//叠词规则
  			if($ws[$i]==$ws[$i+1]){
  				$reStr .= $spc.$ws[$i].$ws[$i+1];
  				$i++; continue;
  			}
  			//单字词和二三字词之间的岐义处理
  			if(strlen($ws[$i])==2 && strlen($ws[$i+1])<8 && strlen($ws[$i+1])>2)
  			{
  				$addw = $ws[$i].$ws[$i+1];
  				$t = 6;
  				$testok = false;
  				while($t>=4)
  				{
  				  $w = substr($addw,0,$t);
  				  if($this->IsWord($w) && ($this->GetRank($w) > $this->GetRank($ws[$i+1])*2) )
  				  {
  					   $limitW = substr($ws[$i+1],strlen($ws[$i+1])-$t-2,strlen($ws[$i+1])-strlen($w)+2);
  					   if($limitW!="") $reStr .= $spc.$w.$spc.$limitW;
  					   else $reStr .= $spc.$w;
  					   $testok = true;
  					   break;
  				  }
  				  $t = $t-2;
  			  }
  			  if(!$testok) $reStr .= $spc.$ws[$i];
  			  else $i++;
  			}
  			//前后均为二字到三字的词进行交叉岐义处理
  			else if(strlen($ws[$i])>2 && strlen($ws[$i])<8 && strlen($ws[$i+1])>2 && strlen($ws[$i+1])<8)
  			{
  				$t21 = substr($ws[$i+1],0,2);
  				$t22 = substr($ws[$i+1],0,4);
  				//如果上一个词接下一个词的首字为词
  				if($this->IsWord($ws[$i].$t21))
  				{
  					if(strlen($ws[$i])==6||strlen($ws[$i+1])==6){
  						$reStr .= $spc.$ws[$i].$t21.$spc.substr($ws[$i+1],2,strlen($ws[$i+1])-2);
  						$i++;
  					}else{
  						$reStr .= $spc.$ws[$i];
  					}
  				}
  				//对于下一个词为3字词或2字词进行不同的处理
  				else if(strlen($ws[$i+1])==6)
  				{
  					if($this->IsWord($ws[$i].$t22))
  					{
  						$reStr .= $spc.$ws[$i].$t22.$spc.$ws[$i+1][4].$ws[$i+1][5];
  						$i++;
  					}else{ $reStr .= $spc.$ws[$i]; }
  				}
  				//
  				//两字词交叉识别，视情况选择
  				//
  				else if(strlen($ws[$i+1])==4)
  				{
  					$addw = $ws[$i].$ws[$i+1];
  					$t = strlen($ws[$i+1])-2;
  					$testok = false;
  					while($t>0)
  					{
  						$w = substr($addw,0,strlen($ws[$i])+$t);
  						if($this->IsWord($w) && ($this->GetRank($w) > $this->GetRank($ws[$i+1])*2) )
  				    {
  				       $limitW = substr($ws[$i+1],$t,strlen($ws[$i+1])-$t);
  					     if($limitW!="") $reStr .= $spc.$w.$spc.$limitW;
  					     else $reStr .= $spc.$w;
  					     $testok = true;
  					     break;
  				    }
  				    $t = $t-2;
  					}
  					if(!$testok) $reStr .= $spc.$ws[$i];
  			    else $i++;
  				}else {
  					$reStr .= $spc.$ws[$i];
  				}
  			}
  			//超过四字词或小于二字的词不作处理
  			else
  			{
  				$reStr .= $spc.$ws[$i];
  			}
  		}
    }//End For
  	return $reStr;
  }
  //---------------------------------
  //判断词典里是否存在某个词
  //---------------------------------
  function IsWord($okWord){
  	$slen = strlen($okWord);
  	if($slen > $this->MaxLen) return false;
  	else return isset($this->RankDic[$slen][$okWord]);
  }
  //------------------------------
  //整理字符串（对标点符号，中英文混排等初步处理）
  //------------------------------
  function ReviseString($str)
  {
  	$spc = $this->SplitChar;
    $slen = strlen($str);
    if($slen==0) return '';
    $okstr = '';
    $prechar = 0; // 0-空白 1-英文 2-中文 3-符号
    for($i=0;$i<$slen;$i++){
      if(ord($str[$i]) < 0x81)
      {
        //英文的空白符号
        if(ord($str[$i]) < 33){
          //$str[$i]!="\r"&&$str[$i]!="\n"
          if($prechar!=0) $okstr .= $spc;
          $prechar=0;
          continue;
        }else if(preg_match("/[^0-9a-zA-Z@\.%#:\\/\\&_-]/",$str[$i]))
        {
          if($prechar==0)
          {	$okstr .= $str[$i]; $prechar=3;}
          else
          { $okstr .= $spc.$str[$i]; $prechar=3;}
        }else
        {
        	if($prechar==2||$prechar==3)
        	{ $okstr .= $spc.$str[$i]; $prechar=1;}
        	else
        	{
        	  if(preg_match("/@#%:/",$str[$i])){ $okstr .= $str[$i]; $prechar=3; }
        	  else { $okstr .= $str[$i]; $prechar=1; }
        	}
        }
      }
      else{
        //如果上一个字符为非中文和非空格，则加一个空格
        if($prechar!=0 && $prechar!=2) $okstr .= $spc;
        //如果中文字符
        if(isset($str[$i+1])){
          $c = $str[$i].$str[$i+1];

          if(preg_match("/".$this->CnNumber."/",$c))
          { $okstr .= $this->GetAlabNum($c); $prechar = 2; $i++; continue; }

          $n = hexdec(bin2hex($c));
          if($n>0xA13F && $n < 0xAA40)
          {
            if($c=="《"){
            	if($prechar!=0) $okstr .= $spc." 《";
            	else $okstr .= " 《";
            	$prechar = 2;
            }
            else if($c=="》"){
            	$okstr .= "》 ";
            	$prechar = 3;
            }
            else{
            	if($prechar!=0) $okstr .= $spc.$c;
            	else $okstr .= $c;
            	$prechar = 3;
            }
          }
          else{
            $okstr .= $c;
            $prechar = 2;
          }
          $i++;
        }
      }//中文字符
    }//结束循环
    return $okstr;
  }
  //-----------------------------------------
	//尝试识别新词，字符串参数为已经分词处理的串
	//----------------------------------------
  function FindNewWord($str,$maxlen=6)
  {
    $okstr = "";
    return $str;
  }
  //----------------------------------------------
  //除去字串中的重复词，生成索引字符串，字符串参数为已经分词处理的串
  //--------------------------------------------------
  function GetIndexText($str,$ilen=-1)
  {
    if($str=='') return '';
    else $this->SplitRMM($str,true,true);
    $okstr = $this->ResultString;
    $ws = explode(' ',$okstr);
    $okstr = $wks = '';
    foreach($ws as $w)
    {
      $w = trim($w);
      //排除小于2的字符
      if(strlen($w)<2) continue;
      //排除数字或日期
      if(!preg_match("/[^0-9:-]/",$w)) continue;
      if(strlen($w)==2&&ord($w[0])>0x80) continue;
      if(isset($wks[$w])) $wks[$w]++;
      else $wks[$w] = 1;
    }
    if(is_array($wks))
    {
      arsort($wks);
      if($ilen==-1)
      { foreach($wks as $w=>$v)
      	{
      		if($this->GetRank($w)>500) $okstr .= $w." ";
        }
      }
      else
      {
        foreach($wks as $w=>$v){
          if((strlen($okstr)+strlen($w)+1)<$ilen) $okstr .= $w." ";
          else break;
        }
      }
    }
    return trim($okstr);
  }
  //---------------------
  //获得词的词频
  //--------------------
  function GetRank($w){
  	if(isset($this->RankDic[strlen($w)][$w])) return $this->RankDic[strlen($w)][$w];
  	else return 0;
  }
  //----------------------------
  //把全角数字或英文单词转为半角
  //---------------------------
  function GetAlabNum($fnum)
  {
	  $nums = array("０","１","２","３","４","５","６",
	  "７","８","９","＋","－","％","．",
	  "ａ","ｂ","ｃ","ｄ","ｅ","ｆ","ｇ","ｈ","ｉ","ｊ","ｋ","ｌ","ｍ",
	  "ｎ","ｏ","ｐ","ｑ","ｒ","ｓ ","ｔ","ｕ","ｖ","ｗ","ｘ","ｙ","ｚ",
	  "Ａ","Ｂ","Ｃ","Ｄ","Ｅ","Ｆ","Ｇ","Ｈ","Ｉ","Ｊ","Ｋ","Ｌ","Ｍ",
	  "Ｎ","Ｏ","Ｐ","Ｑ","Ｒ","Ｓ","Ｔ","Ｕ","Ｖ","Ｗ","Ｘ","Ｙ","Ｚ");
	  $fnums = "0123456789+-%.abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  $fnum = str_replace($nums,$fnums,$fnum);
	  return $fnum;
  }
}//End Class

?>