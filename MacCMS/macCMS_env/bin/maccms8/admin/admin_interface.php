<?php
require(dirname(__FILE__) .'/admin_conn.php');

$ac = be("all","ac");
$pass = be("all","pass");

function gettypere($flag,$tn)
{
    $res = 0;
    if ($flag=="art"){
        $typearr = $GLOBALS['MAC_CACHE']['arttype'];
        $file = "../inc/config/interface_arttype.txt";
    }
    else{
        $typearr = $GLOBALS['MAC_CACHE']['vodtype'];
        $file = "../inc/config/interface_vodtype.txt";
	}
    
    if(strpos($tn,'/')){
    	$arr = explode('/',$tn);
    	$tn = trim($arr[0]);
    	unset($arr);
    }
    
    $str = file_get_contents($file);
    if (!isN($str)){
        $str = str_replace( Chr(10),Chr(13),$str);
        $arr1 = explode(Chr(13),$str);
        
        for ($i=0;$i<count($arr1);$i++){
            if (!isN($arr1[$i])){
                $str1 = $arr1[$i];
                $arr2 = explode("=",$str1);
                
                if (trim($tn) == trim($arr2[1])){
                    foreach($typearr as $t){
                        if (trim($t["t_name"]) == trim($arr2[0])){
                            return $t["t_id"];
                            break;
                    	}
                    }
					break;
                }
            }
        }
    }
}

if($ac=='vod')
{
	if ($MAC['interface']['pass'] != $pass){ echo "非法使用err";exit; }
	
	$d_addtime = time(); $d_time = time();
    $d_id = be("all", "d_id");
    $d_name = be("all", "d_name"); $d_subname = be("all", "d_subname"); $d_enname = be("all", "d_enname"); 
    
    $d_type = be("all", "d_type"); $d_remarks = be("all", "d_remarks"); $d_tag = be("all","d_tag");
    $d_state = be("all", "d_state"); $d_color = be("all", "d_color"); $d_level = be("all", "d_level");
    $d_starring = be("all", "d_starring"); $d_directed = be("all", "d_directed"); $d_lang = be("all", "d_lang");
    $d_year = be("all", "d_year");  $d_area = be("all", "d_area"); $d_class = be("all", "d_class");
    
    $d_hits = be("all", "d_hits"); $d_dayhits = be("all", "d_dayhits");
    $d_weekhits = be("all", "d_weekhits"); $d_monthhits = be("all", "d_monthhits"); 
    $d_content = be("all", "d_content");
    
    $d_hide = be("all", "d_hide"); $d_up = be("all", "d_up"); $d_down = be("all", "d_down"); 
    
    $d_pic = be("all", "d_pic"); $d_picthumb = be("all", "d_picthumb"); $d_picslide = be("all", "d_picslide");
    
    $d_playurl = be("all", "d_playurl");  $d_playfrom = be("all", "d_playfrom");
    $d_playserver = be("all", "d_playserver"); $d_playnote = be("all", "d_playnote");
    
    $d_downurl = be("all","d_downurl");   $d_downfrom = be("all", "d_downfrom");
    $d_downserver = be("all", "d_downserver"); $d_downnote = be("all", "d_downnote");
    
    $d_stint = be("all", "d_stint"); $d_usergroup = be("all", "d_usergroup");
    $d_duration = be("all","d_duration"); $d_stintdown = be("all", "d_stintdown");
    $d_score = be("all","d_score"); $d_scorenum = be("all","d_scorenum"); $d_scoreall = be("all","d_scoreall");
    
    $d_pic = stripslashes($d_pic); $d_picthumb = stripslashes($d_picthumb); $d_picslide = stripslashes($d_picslide);
    $d_name = str_replace("'", "''",$d_name);
    $d_starring = str_replace("'", "''",$d_starring);
    $d_directed = str_replace("'", "''",$d_directed);
    $d_content = stripslashes($d_content); $d_content = str_replace("'", "''",$d_content);
    
    if (!isNum($d_usergroup)) { $d_usergroup = 0;}
    if (isN($d_name)) { echo "视频名称不能为空err"; exit;}
    if (isN($d_type)) { echo "视频分类不能为空err"; exit;}
    
    if(!isNum($d_level)) { $d_level = 0;}
    if(!isNum($d_hits)) { $d_hits = 0;}
    if(!isNum($d_dayhits)) { $d_dayhits = 0;}
    if(!isNum($d_weekhits)) { $d_weekhits = 0;}
    if(!isNum($d_monthhits)) { $d_monthhits = 0;}
    if(!isNum($d_stint)) { $d_stint = 0;}
    if(!isNum($d_stintdown)) { $d_stintdown = 0;}
    if(!isNum($d_state)) { $d_state = 0;}
    if(!isNum($d_up)) { $d_up=0;}
    if(!isNum($d_down)) { $d_down=0;}
    if(!isNum($d_scorenum)) { $d_scorenum=0;}
    if(!isNum($d_scoreall)) { $d_scoreall=0;}
    if(!isNum($d_score)) { $d_score=0;}
    if(!isNum($d_hide)) { $d_hide = 0;}
    if(!isNum($d_duration)) { $d_duration=0;}
    if(isN($d_enname)) { $d_enname = Hanzi2PinYin($d_name); }
    if(strpos($d_enname, "*")>0 || strpos($d_enname, ":")>0 || strpos($d_enname, "?")>0 || strpos($d_enname, "\"")>0 || strpos($d_enname, "<")>0 || strpos($d_enname, ">")>0 || strpos($d_enname, "|")>0 || strpos($d_enname, "\\")>0){
       echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err";exit;
    }
    $d_letter = strtoupper(substring($d_enname,1));
    
    if($MAC['collect']['vod']['psernd']==1){
		$d_content = repPseRnd('vod',$d_content,0);
    }
    if($MAC['collect']['vod']['psesyn']==1){
		$d_content = repPseSyn('vod',$d_content);
    }
    if($d_hits==0){
    	$d_hits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
    }
    if($d_dayhits==0){
    	$d_dayhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
    }
    if($d_weekhits==0){
    	$d_weekhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
    }
    if($d_monthhits==0){
    	$d_monthhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
    }
    if($d_score==0 || $d_scoreall==0 || $d_scorenum==0){
		if($MAC['collect']['vod']['score']==1){
			$d_scorenum = rand(1,500);
			$d_scoreall = $d_scorenum * rand(1,10);
			$d_score = round( $d_scoreall / $d_scorenum ,1);
		}
    }
	
	
    $rc = false;
    if (!isN($d_playurl)){
	    if (isN($d_playfrom)) { echo "视频播放器类型不能为空err";exit;}
	    $d_playurl = str_replace(chr(13), "",$d_playurl);
        $d_playurl = str_replace(chr(10), "#",$d_playurl);
        $d_playurl = str_replace("##", "#",$d_playurl);
        
        if (substring($d_playurl, 3,strlen(utf8_decode($d_playurl))-3) == '$$$'){ $d_playurl = substring($d_playurl, strlen(utf8_decode($d_playurl))-3);}
        if (substring($d_playfrom, 3,strlen($d_playfrom)-3) == '$$$'){ $d_playfrom = substring($d_playfrom, strlen($d_playfrom)-3);}
        if (substring($d_playserver, 3,strlen($d_playserver)-3) == '$$$'){ $d_playserver = substring($d_playserver, strlen($d_playserver)-3);}
        if (substring($d_playnote, 3,strlen(utf8_decode($d_playnote))-3) == '$$$'){ $d_playnote = substring($d_playnote, strlen(utf8_decode($d_playnote))-3);}
        
	    $playurlarr = explode('$$$',$d_playurl);           $playfromarr = explode('$$$',$d_playfrom);
	    $playserverarr = explode('$$$',$d_playserver);     $playnotearr = explode('$$$',$d_playnote);
	    if (count($playurlarr) != count($playfromarr)){
	    	echo '播放器类型、播放地址数量不一致,多组数据请用$$$连接err' ; exit;
	    }
	    
    }
    if (!isN($d_downurl)){
	    if (isN($d_downfrom)) { echo "视频下载类型不能为空err";exit;}
	    $d_downurl = str_replace(chr(13), "",$d_downurl);
        $d_downurl = str_replace(chr(10), "#",$d_downurl);
        $d_downurl = str_replace("##", "#",$d_downurl);
        
        if (substring($d_downurl, 3,strlen(utf8_decode($d_downurl))-3) == '$$$'){ $d_downurl = substring($d_downurl, strlen(utf8_decode($d_downurl))-3);}
        if (substring($d_downfrom, 3,strlen($d_downfrom)-3) == '$$$'){ $d_downfrom = substring($d_downfrom, strlen($d_downfrom)-3);}
        if (substring($d_downserver, 3,strlen($d_downserver)-3) == '$$$'){ $d_downserver = substring($d_downserver, strlen($d_downserver)-3);}
        if (substring($d_downnote, 3,strlen(utf8_decode($d_downnote))-3) == '$$$'){ $d_downnote = substring($d_downnote, strlen(utf8_decode($d_downnote))-3);}
        
	    $downurlarr = explode('$$$',$d_downurl);          $downfromarr = explode('$$$',$d_downfrom);
	    $downserverarr = explode('$$$',$d_downserver);    $downnotearr = explode('$$$',$d_downnote);
	    if (count($downurlarr) != count($downfromarr)){
	    	echo '下载类型、下载地址数量不一致,多组数据请用$$$连接err' ; exit;
	    }
    }
    
    $newtype = "";
    $newtypeid = 0;
    $newtype = $d_type;
    if (!isNum($d_type)) { $newtypeid = gettypere("vod", $d_type);} else{ $newtypeid = intval($d_type);}
    if ($newtypeid ==0) { echo $d_name . " " . $newtype . " 没有找到转换的分类err";exit; }
    $d_type = $newtypeid;
    
    
	if(!isN($d_class)){
    	$arr=explode(',',$d_class);
    	$d_class='';
    	$rc=false;
    	foreach($arr as $a){
    		$n = $db->getOne('select c_id from {pre}vod_class where c_name=\''.$a.'\'');
    		if($rc){ $d_class.=','; }
    		if($n<1){
    			$c_pid = $MAC['vodtype'][$d_type]['t_pid'];
    			if($c_pid==0){ $c_pid=$d_type; }
    			$db->Add('{pre}vod_class',array('c_name','c_pid'),array($a,$c_pid));
    			$n=$db->insert_id();
    		}
    		$rc=true;
    		$d_class.=$n;
    	}
    	$d_class=','.$d_class.',';
    }
    
    $inrule = $MAC['collect']['vod']['inrule'];
	$uprule = $MAC['collect']['vod']['uprule'];
	$filter = $MAC['collect']['vod']['filter'];
    if(strpos(','.$filter,$d_name)) { $des="数据在过滤单中,系统跳过采集err";exit; }
    if($d_tag=='' && $MAC['collect']['vod']['tag']==1){
		$d_tag = getTag($d_name,$d_content);
	}
    
    $sql = "SELECT * FROM {pre}vod WHERE d_name ='" .$d_name. "' ";
    if(strpos($inrule,'b')){ $sql.=' and d_type='.$d_type; }
	if(strpos($inrule,'c') ){ $sql.=' and d_year='.$d_year; }
	if(strpos($inrule,'d') ){ $sql.=' and d_area=\''.$d_area.'\''; }
	if(strpos($inrule,'e') ){ $sql.=' and d_lang=\''.$d_lang.'\''; }
	if(strpos($inrule,'f') ){ $sql.=' and d_starring=\''.$d_starring.'\''; }
	if(strpos($inrule,'g') ){ $sql.=' and d_directed=\''.$d_directed.'\''; }
	
	
    $row = $db->getRow($sql);
    if (!$row){
        $resultdes = "新增数据ok";
        $db->Add ("{pre}vod", array("d_name", "d_subname", "d_enname", "d_type","d_class", "d_state", "d_letter", "d_color", "d_pic","d_picthumb","d_picslide", "d_starring", "d_directed", "d_area", "d_year", "d_lang", "d_level", "d_stint","d_stintdown", "d_hits","d_dayhits","d_weekhits","d_monthhits", "d_content", "d_remarks","d_tag", "d_usergroup", "d_score","d_scoreall" , "d_scorenum","d_up","d_down","d_hide","d_duration","d_addtime", "d_time", "d_playurl", "d_playfrom", "d_playserver","d_playnote","d_downurl","d_downfrom", "d_downserver","d_downnote"), array($d_name, $d_subname, $d_enname, $d_type,$d_class, $d_state, $d_letter, $d_color, $d_pic,$d_picthumb,$d_picslide, $d_starring, $d_directed, $d_area, $d_year, $d_lang, $d_level, $d_stint,$d_stintdown, $d_hits,$d_dayhits,$d_weekhits,$d_monthhits,$d_content, $d_remarks,$d_tag, $d_usergroup,$d_score, $d_socreall, $d_scorenum,$d_up,$d_down,$d_hide,$d_duration, $d_addtime, $d_time, $d_playurl, $d_playfrom, $d_playserver,$d_playnote,$d_downurl, $d_downfrom, $d_downserver,$d_downnote));
    }
    else{
    	if($row['d_lock']==1){
        	$des = "数据已经锁定,系统跳过采集更新err";exit;
        }
    	
    	if (strpos(",".$row["d_pic"], "http:") > 0) { } else { $d_pic= $row["d_pic"];}
        if (strpos(",".$row["d_picthumb"], "http:") > 0) { } else { $d_picthumb= $row["d_picthumb"];}
        if (strpos(",".$row["d_picslide"], "http:") > 0) { } else { $d_picslide= $row["d_picslide"];}
        
        if (!isN($d_playurl)){
	        $oldplayfrom = $row["d_playfrom"];
	    	$oldplayurl = $row["d_playurl"];
	    	$oldplayserver = $row["d_playserver"];
	    	$oldplaynote = $row['d_playnote'];
	    	
	        $playurl_new="";
	        $playfrom_new="";
	        $playserver_new="";
	        $playnote_new="";
	        
	        if ($row["d_playurl"] ==$d_playurl){
	            $resultdes = "无需更新播放地址ok";
	            $playfrom_new = $oldplayfrom;
	            $playurl_new = $oldplayurl;
	            $playserver_new = $oldplayserver;
	            $playnote_new = $oldplaynote;
	        }
	        else if(isN($oldplayfrom)){
	        	$resultdes = "新增播放地址ok";
	            $playfrom_new = $d_playfrom;
	            $playurl_new = $d_playurl;
	            $playserver_new = $d_playserver;
	            $playurl_new = $d_playnote;
	        }
	        else{
	            $resultdes = "更新播放地址ok";
	            $arr1 = explode('$$$',$oldplayurl);
	            $arr2 = explode('$$$',$oldplayfrom);
	            $arr3 = explode('$$$',$oldplayserver);
	            $arr4 = explode('$$$',$oldplaynote);
	            $rc = false;
	            
	            for ($j=0;$j<count($arr2);$j++){
					if ($rc){
	            		$playurl_new = $playurl_new . '$$$';
	            		$playfrom_new = $playfrom_new . '$$$';
	            		$playserver_new = $playserver_new .'$$$';
	            		$playnote_new = $playnote_new .'$$$';
	            	}
	            	for ($k=0;$k<count($playfromarr);$k++){
						if ($arr2[$j] == $playfromarr[$k]){
							$arr1[$j] = $playurlarr[$k];
							break;
						}
						
					}
					$playurl_new = $playurl_new . $arr1[$j];
		            $playfrom_new = $playfrom_new . $arr2[$j];
		            $playserver_new = $playserver_new . $arr3[$j];
		            $playnote_new = $playnote_new . $arr4[$j];
		            
		            $rc=true;
				}
		        
	            for ($k=0;$k<count($playfromarr);$k++){
		            for ($j=0;$j<count($arr2);$j++){
		            	if (strpos(",".$oldplayfrom,$playfromarr[$k])<=0){
		            		$playfrom_new = $playfrom_new . '$$$'. $playfromarr[$k];
		            		$playurl_new = $playurl_new .'$$$'. $playurlarr[$k];
		            		$playserver_new = $playserver_new .'$$$'. $playserverarr[$k];
		            		$playnote_new = $playnote_new .'$$$'. $playnotearr[$k];
		            		
		            		$oldplayfrom = $oldplayfrom . '$$$'. $playfromarr[$k];
		            		$resultdes = "新增播放地址ok";
		            	}
		            }
	            }
	            unset($arr1);
	            unset($arr2);
	            unset($arr3);
	            unset($arr4);
			}
		}
		else{
			$playfrom_new = $row["d_playfrom"];
	    	$playurl_new = $row["d_playurl"];
	    	$playserver_new = $row["d_playserver"];
	    	$playnote_new = $row["d_playnote"];
		}
        $playurl_new = str_replace(Chr(13), "#",$playurl_new);
        
        if (!isN($d_downurl)){
	        $olddownfrom = $row["d_downfrom"];
	    	$olddownurl = $row["d_downurl"];
	    	$olddownserver = $row["d_downserver"];
	    	$olddownnote = $row["d_downnote"];
	    	
	        $downurl_new="";
	        $downfrom_new="";
	        $downserver_new="";
	        $downnote_new="";
	        
	        if ($row["d_downurl"] ==$d_downurl){
	            $resultdes = "无需更新下载地址ok";
	            $downfrom_new = $olddownfrom;
	            $downurl_new = $olddownurl;
	            $downserver_new = $olddownserver;
	            $downnote_new = $olddownnote;
	        }
	        else if(isN($olddownfrom)){
	        	$resultdes = "新增下载地址ok";
	            $downfrom_new = $d_downfrom;
	            $downurl_new = $d_downurl;
	            $downserver_new = $d_downserver;
	            $downnote_new = $d_downnbte;
	        }
	        else{
	            $resultdes = "更新下载地址ok";
	            $arr1 = explode('$$$',$olddownurl);
	            $arr2 = explode('$$$',$olddownfrom);
	            $arr3 = explode('$$$',$olddownserver);
	            $arr4 = explode('$$$',$olddownnote);
	            $rc = false;
	            
	            for ($j=0;$j<count($arr2);$j++){
					if ($rc){
	            		$downurl_new = $downurl_new . '$$$';
	            		$downfrom_new = $downfrom_new . '$$$';
	            		$downserver_new = $downserver_new .'$$$';
	            		$downnote_new = $downnote_new .'$$$';
	            	}
	            	for ($k=0;$k<count($downfromarr);$k++){
						if ($arr2[$j] == $downfromarr[$k]){
							$arr1[$j] = $downurlarr[$k];
							break;
						}
						
					}
					$downurl_new = $downurl_new . $arr1[$j];
		            $downfrom_new = $downfrom_new . $arr2[$j];
		            $downserver_new = $downserver_new . $arr3[$j];
		            $downnotenew = $downnote_new . $arr4[$j];
		            
		            $rc=true;
				}
		        
	            for ($k=0;$k<count($downfromarr);$k++){
		            for ($j=0;$j<count($arr2);$j++){
		            	if (strpos(",".$olddownfrom,$downfromarr[$k])<=0){
		            		$downfrom_new = $downfrom_new . '$$$'. $downfromarr[$k];
		            		$downurl_new = $downurl_new .'$$$'. $downurlarr[$k];
		            		$downserver_new = $downserver_new .'$$$'. $downserverarr[$k];
		            		$downnote_new = $downnote_new .'$$$'. $downnotearr[$k];
		            		
		            		$olddownfrom = $olddownfrom . '$$$'. $downfromarr[$k];
		            		$resultdes = "新增下载地址ok";
		            	}
		            }
	            }
	            unset($arr1);
	            unset($arr2);
	            unset($arr3);
	            unset($arr4);
			}
		}
		else{
			$downfrom_new = $row["d_downfrom"];
	    	$downurl_new = $row["d_downurl"];
	    	$downserver_new = $row["d_downserver"];
	    	$downnote_new = $row["d_downnote"];
		}
		$downurl_new = str_replace(Chr(13), "#",$downurl_new);
		
		
		$colarr = array();
		$valarr = array();
		array_push($colarr,'d_time');
		array_push($valarr,time());
		if(strpos(','.$uprule,'a')){
			array_push($colarr,'d_playfrom','d_playserver','d_playnote','d_playurl');
			array_push($valarr,$playfrom_new,$playserver_new,$playnote_new,$playurl_new);
		}
		if(strpos(','.$uprule,'b')){
			array_push($colarr,'d_downfrom','d_downserver','d_downnote','d_downurl');
			array_push($valarr,$downfrom_new,$downserver_new,$downnote_new,$downurl_new);
		}
		if(strpos(','.$uprule,'c')){ array_push($colarr,'d_state'); array_push($valarr,$d_state); }
		if(strpos(','.$uprule,'d')){ array_push($colarr,'d_remarks'); array_push($valarr,$d_remarks); }
		if(strpos(','.$uprule,'e')){ array_push($colarr,'d_directed'); array_push($valarr,$d_directed); }
		if(strpos(','.$uprule,'f')){ array_push($colarr,'d_starring'); array_push($valarr,$d_starring); }
		if(strpos(','.$uprule,'g')){ array_push($colarr,'d_year'); array_push($valarr,$d_year); }
		if(strpos(','.$uprule,'h')){ array_push($colarr,'d_area'); array_push($valarr,$d_area); }
		if(strpos(','.$uprule,'i')){ array_push($colarr,'d_lang'); array_push($valarr,$d_lang); }
		if(strpos(','.$uprule,'j')){
			if($MAC['collect']['vod']['pic']==1){
				$st = strrpos($d_pic,'/');
				$fname = substring($d_pic,strlen($d_pic)-$st,$st+1);
				$path = "upload/vod/" . getSavePicPath('') . "/";
				$thumbpath = "upload/vodthumb/" . getSavePicPath('vodthumb') . "/";
				$ps = savepic($d_pic,$path,$thumbpath,$fname,'vod',$msg);
				if($ps){
					$d_pic=$path.$fname; $d_picthumb= $thumbpath.$fname; 
					array_push($colarr,'d_pic'); array_push($valarr,$d_pic);
					array_push($colarr,'d_picthumb'); array_push($valarr,$d_picthumb);
				}
			}
		}
		if(strpos(','.$uprule,'k')){ array_push($colarr,'d_content'); array_push($valarr,$d_content); }
		if(strpos(','.$uprule,'l')){ array_push($colarr,'d_tag'); array_push($valarr,$d_tag); }
		if(count($colarr)>0){
			$db->Update ("{pre}vod",$colarr,$valarr,"d_id=".$row["d_id"]);
		}
    }
    unset($row);
    echo $resultdes;
}


else if($ac=='art')
{
    global $db,$pass;
	if ($MAC['interface']['pass'] != $pass){ echo "非法使用err";exit; }
    
    $a_id = be("all", "a_id"); $a_name = be("all", "a_name");
    $a_subname = be("all", "a_subname"); $a_enname = be("all", "a_enname");
    $a_type = be("all", "a_type");$a_content = be("all", "a_content");
    $a_author = be("all", "a_author"); $a_color = be("all", "a_color");
    $a_hits = be("all", "a_hits"); $a_dayhits = be("all", "a_dayhits");
    $a_weekhits = be("all", "a_weekhits");$a_monthhits = be("all", "a_monthhits");
    $a_from = be("all", "a_from"); $a_hide = be("all", "a_hide"); $a_pic = be("all", "a_pic");
    $a_level = be("all", "a_level"); $a_remarks = be("all", "a_remarks");
    $a_up = be("all", "a_up"); $a_down = be("all", "a_down");  $a_tag = be("all","a_tag");
    
    $a_addtime = time(); $a_time = time();
    $a_name = str_replace("'", "''",$a_name);
    $a_author = str_replace("'", "''",$a_author);
    $a_content = stripslashes($a_content); $a_content = str_replace("'", "''",$a_content);
    
    if (isN($a_name)) { echo "文章名称不能为空err"; exit;}
    if (isN($a_type)) { echo "文章分类不能为空err"; exit;}
    if (!isNum($a_hide)) { $a_hide = 0;}
    if (!isNum($a_level)) { $a_level = 0;}
    if (!isNum($a_hits)) { $a_hits = 0;}
    if (!isNum($a_dayhits)) { $a_dayhits = 0;}
    if (!isNum($a_weekhits)) { $a_weekhits = 0;}
    if (!isNum($a_monthhits)) { $a_monthhits = 0;}
    if (!isNum($a_up)) { $a_up = 0;}
    if (!isNum($a_down)) { $a_down = 0;}
    if (isN($a_enname)) { $a_enname = Hanzi2PinYin($a_name); }
    
    if (strpos($a_enname, "*")>0 || strpos($a_enname, ":")>0 || strpos($a_enname, "?")>0 || strpos($a_enname, "\"")>0 || strpos($a_enname, "<")>0 || strpos($a_enname, ">")>0 || strpos($a_enname, "|")>0 || strpos($a_enname, "\\")>0){
        echo "名称和拼音名称中: 不能出现英文输入状态下的 * : ? \" < > | \ 等特殊符号err"; exit;
    }
    $a_letter = strtoupper(substring($a_enname,1));
    if (!isNum($a_type)) { $a_type = gettypere("art", $a_type);}
    if ($a_type== 0) { echo "没有找到转换的分类err";exit;}
    if($MAC['collect']['art']['psernd']==1){
		$a_content = repPseRnd('art',$a_content,0);
    }
    if($MAC['collect']['art']['psesyn']==1){
		$a_content = repPseSyn('art',$a_content);
    }
    
    $inrule = $MAC['collect']['art']['inrule'];
	$uprule = $MAC['collect']['art']['uprule'];
	$filter = $MAC['collect']['art']['filter'];
    if(strpos(','.$filter,$d_name)) { $des="数据在过滤单中,系统跳过采集err";exit; }
    if($a_tag=='' && $MAC['collect']['art']['tag']==1){
		$a_tag = getTag($a_name,$a_content);
	}
    
    $sql = "SELECT * FROM {pre}art WHERE a_name ='" . $a_name . "' ";
    if(strpos($inrule,'b')){ $sql.=' and a_type='.$a_type; }
    $row = $db->getRow($sql);
    if(!$row){
        $db->Add ("{pre}art", array("a_name", "a_subname", "a_enname", "a_type","a_letter" ,"a_content", "a_author", "a_color", "a_from","a_pic","a_hide", "a_hits","a_dayhits","a_weekhits","a_monthhits","a_up","a_down", "a_level","a_remarks","a_tag","a_addtime", "a_time"), array($a_name, $a_subname, $a_enname, $a_type, $a_letter, $a_content, $a_author, $a_color, $a_from,$a_pic,$a_hide, $a_hits,$a_dayhits,$a_weekhits,$a_monthhits,$a_up,$a_down,$a_level,$a_remarks,$a_tag, $a_addtime, $a_time));
    }
    else{
    	if($row['a_lock']==1){
        	$des = "数据已经锁定,系统跳过采集更新err";exit;
        }
        
        
    	$colarr = array();
		$valarr = array();
		array_push($colarr,'a_time');
		array_push($valarr,time());
    	if(strpos(','.$uprule,'a')){ array_push($colarr,'a_content'); array_push($valarr,$a_content); }
    	if(strpos(','.$uprule,'b')){ array_push($colarr,'a_author'); array_push($valarr,$a_author); }
    	if(strpos(','.$uprule,'c')){ array_push($colarr,'a_from'); array_push($valarr,$a_from); }
    	if(strpos(','.$uprule,'d')){
			if($MAC['collect']['art']['pic']==1){
				$st = strrpos($a_pic,'/');
				$fname = substring($a_pic,strlen($a_pic)-$st,$st+1);
				$path = "upload/art/" . getSavePicPath('') . "/";
				$ps = savepic($a_pic,$path,$thumbpath,$fname,'art',$msg);
				if($ps){
					$a_pic=$path.$fname;
					array_push($colarr,'a_pic'); array_push($valarr,$a_pic);
				}
			}
		}
		if(strpos(','.$uprule,'e')){ array_push($colarr,'a_tag'); array_push($valarr,$a_tag); }
		
        if(count($colarr)>0){
			$db->Update ("{pre}art",$colarr,$valarr,"a_id=".$row["a_id"]);
		}
    }
    unset($row);
    echo "ok";
}

else{
	
}
?>