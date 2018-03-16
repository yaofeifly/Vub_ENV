<?php
if(!defined('MAC_ADMIN')){
	exit('Access Denied');
}
ob_end_clean();
ob_implicit_flush(true);
ini_set('max_execution_time', '0');

$bindcache = @include(MAC_ROOT."/inc/config/config.collect.bind.php");
$backurl = getReferer();
$typearr=array();


$ac2 = $p['ac2'];
$apiurl = $p['apiurl'];
$flag = $p['flag']; //资源标识
$xt = $p['xt'];  //xml类型
$ct = $p['ct'];  //存储类型play or down
$group = $p['group']; //指定播放组
$wd = $p['wd'];
$type = intval($p['type']);
$pg = intval($p['pg']);
$hour = intval($p['hour']);

if($method=='break')
{
    echo getBreak("union"). "正在载入断点续传数据，请稍后......";
}

elseif($method=='union')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	$status = chkBreak('union');
	$plt->set_if('main','isbreak',$status);
}

elseif($method=='list')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
	
	
	$colarr=array('action','type','pg','apiurl','wd','hour','xt','group','xt','ct','flag');
	$valarr=array($method,$type,$pg,$apiurl,$wd,$hour,$xt,$group,$xt,$ct,$flag);
	for($i=0;$i<count($colarr);$i++){
		$n = $colarr[$i];
		$v = $valarr[$i];
		$plt->set_var($n,$v);
	}
	
	$type>0 ? $istype=true : $istype=false;
	$plt->set_if('main','istype',$istype);
	
	if($xt=='0'){
    	$url = $apiurl . '?action=list&rpage='.$pg.'&rtype='.$type.'&rkey='.urlencode($wd);
    	$xn_list = '<vods pagesize="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 1;
    	$xn_pagecount = 2;
    	$xn_recordcount = 3;
    	$xn_type = '/<type id="([0-9]+)">([\s\S]*?)<\/type>/';
		$xn_d = '/<vod><id>([0-9]+)<\/id><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><starring><\!\[CDATA\[([\s\S]*?)\]\]><\/starring><type>([\s\S]*?)<\/type><from>([\s\S]*?)<\/from><time>([\s\S]*?)<\/time><\/vod>/';
		$xn_d_id=1;
		$xn_d_name = 2;
	    $xn_d_starring=3;
	    $xn_d_type=4;
	    $xn_d_from=5;
	    $xn_d_time=6;
	}
    elseif($xt=='1'){
    	$url = $apiurl . '?ac=list&pg=' . $pg . '&rid='.$group . '&t=' . $type . '&wd=' . urlencode($wd);
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
		$xn_d = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><dt>([\s\S]*?)<\/dt><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note>/';
		$xn_d_id=2;
		$xn_d_name = 4;
	    $xn_d_starring=7;
	    $xn_d_type=5;
	    $xn_d_from=6;
	    $xn_d_time=1;
    }
    elseif($xt=='2'){
    	$url = $apiurl . '-action-list-cid-'. $type . '-h-'. $hour. '-p-' . $pg. '-wd-'. urlencode($wd);
    	$url = str_replace('|','-',$url);
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_type = '/<ty id="([0-9]+)">([\s\S]*?)<\/ty>/';
		$xn_d = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des>([\s\S]*?)<\/video>/';
		
		$xn_url = '/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/';
		$xn_d_id=2;
		$xn_d_name = 4;
	    $xn_d_starring=6;
	    $xn_d_type=5;
	    $xn_d_from=0;
	    $xn_d_time=1;
	    $xn_d_urls=14;
    }
    $html = getPage($url, 'utf-8');
    
    
    preg_match($xn_list ,$html,$array1);
	$pgsize = $array1[$xn_pagesize];
	$pgcount = $array1[$xn_pagecount];
	$recordcount = $array1[$xn_recordcount];
	unset($array1);
	
    preg_match_all($xn_type,$html,$array2);
    $colarr=array('v','n');
    $rn='type';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
    foreach($array2[1] as $k=>$v){
		$typeid = $v;
		$typename = $array2[2][$k];
		$isbind = false;
		$uid = intval( $bindcache[$flag.$typeid] );
        if ($uid>0){
            $isbind=true;
        }
        $valarr=array($typeid,$typename);
        for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		$plt->set_if('rows_'.$rn,'isbind',$isbind);
    }
    unset($array2);
    
	preg_match_all($xn_d,$html,$array3);
    if( count($array3[1])==0){
        $plt->set_if('main','isnull',true);
        return;
    }
    $plt->set_if('main','isnull',false);
    
    $colarr=array('id','name','typename','from','time','chk','nameencode');
    $rn='data';
	$plt->set_block('main', 'list_'.$rn, 'rows_'.$rn);
    foreach($array3[1] as $key=>$value){
		$id = $array3[$xn_d_id][$key];
		$name = $array3[$xn_d_name][$key];
		$nameencode = urlencode(substring($name,4));
		$typename = $array3[$xn_d_type][$key];
		$now = date('Y-m-d',time());
		
		$time = $array3[$xn_d_time][$key];
		$sc = substr_count($time,'-');
		if($sc==1){ $time = date('Y-',time()).$time; }
		$time = getColorDay(strtotime($time));
		$chk = strpos(','.$time,$now)>0 ? 'checked' : '';
		
		if($xt=='2'){
			$d_playurls = $array3[$xn_d_urls][$key];
			preg_match_all($xn_url,$d_playurls,$fromarr);
			$from = implode('$$$',$fromarr[1]);
		}
		else{
			$from = $array3[$xn_d_from][$key];
		}
		
		$valarr=array($id,$name,$typename,$from,$time,$chk,$nameencode);
		for($i=0;$i<count($colarr);$i++){
			$n = $colarr[$i];
			$v = $valarr[$i];
			$plt->set_var($n,$v);
		}
		$plt->parse('rows_'.$rn,'list_'.$rn,true);
		
    }
    unset($array3);
    unset($colarr);
    unset($valarr);
    
    $pgurl = '?m=collect-list-pg-{pg}-xt-'.$xt.'-group-'.$group.'-flag-'.$flag.'-type-'.$type.'-wd-'.urlencode($wd).'-apiurl-'.$apiurl;
	$pgs = '共'.$recordcount.'条数据&nbsp;当前:'.$pg.'/'.$pgcount.'页&nbsp;'.pageshow($pg,$pgcount,5,$pgurl,'pagego(\''.$pgurl.'\','.$pgcount.')');
	$plt->set_var('pages', $pgs );
}

elseif($method=='cj'){
	headAdmin2('数据采集');
	switch($ac2)
	{
		case 'sel':
			$ids = be("arr", "ids");
			if(empty($ids)) { errMsg ("采集提示", "请选择采集数据");}
			switch($xt)
			{
				case '0': $url = "?action=cjsel&ids=".$ids;
					break;
				case '1': $url = "?ac=videolist&rid=".$group."&ids=".$ids;
					break;
				case '2': $url = "-action-ids-vodids-".$ids."-cid--play--inputer--wd--h-0-p-1";
					break;
			}
			break;
		case 'day':
			switch($xt)
			{
				case '0': $url = "?action=cjday&rday=".$hour."&rpage=".$pg;
					break;
				case '1': $url = "?ac=videolist&rid=".$group."&h=".$hour."&pg=".$pg;
					break;
				case '2': $url = "-action-day-vodids--cid--play--inputer--wd--h-".$hour."-p-".$pg;
					break;
			}
			break;
		case 'type':
			if(empty($type)){ showMsg ("请先进入分类,否则无法使用采集分类!", $backurl); exit; }
			switch($xt)
			{
				case '0': $url = "?action=cjtype&rpage=".$pg."&rtype=". $type;
					break;
				case '1': $url = "?ac=videolist&rid=".$group."&pg=" . $pg . "&t=" . $type;
					break;
				case '2': $url = "-action-all-vodids--cid-".$type."-play--inputer--wd--h-0-p-".$pg;
					break;
			}
			break;
		case 'all':
			switch($xt)
			{
				case '0': $url = "?action=cjall&rpage=".$pg;
					break;
				case '1': $url = "?ac=videolist&rid=".$group."&pg=". $pg;
					break;
				case '2': $url = "-action-all-vodids--cid--play--inputer--wd--h-0-p-" . $pg;
					break;
			}
			break;
	}
	$url = $apiurl.$url;
    
    if($xt=="0"){
    	$xn_list = '/<pagecount>([0-9]+)<\/pagecount>/';
    	$xn_pagesize = 1;
    	$xn_pagecount = 1;
    	$xn_recordcount = 1;
    	$xn_d = '/<vod><id>([0-9]+)<\/id><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><state>([\s\S]*?)<\/state><type>([\s\S]*?)<\/type><starring><\!\[CDATA\[([\s\S]*?)\]\]><\/starring><directed><\!\[CDATA\[([\s\S]*?)\]\]><\/directed><pic>([\s\S]*?)<\/pic><time>([\s\S]*?)<\/time><year>([\s\S]*?)<\/year><area><\!\[CDATA\[([\s\S]*?)\]\]><\/area><language><\!\[CDATA\[([\s\S]*?)\]\]><\/language><urls>([\s\S]*?)<\/urls><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des><\/vod>/';
    	$xn_url = '/<url from="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/url>/';
    	$xn_d_id=1;
	    $xn_d_name=2;
	    $xn_d_remarks=3;
	    $xn_d_state=4;
	    $xn_d_type=5;
	    $xn_d_starring=6;
	    $xn_d_directed=7;
	    $xn_d_pic=8;
	    $xn_d_time=9;
	    $xn_d_year=10;
	    $xn_d_area=11;
	    $xn_d_lang=12;
	    $xn_d_des=14;
	    $xn_d_urls=13;
    }
    elseif($xt=="1"){
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_d = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des>([\s\S]*?)<\/video>/';
    	$xn_url = '/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/';
    	$xn_d_time=1;
    	$xn_d_id=2;
	    $xn_d_type=3;
	    $xn_d_name=4;
	    $xn_d_pic=6;
	    $xn_d_lang=7;
	    $xn_d_area=8;
	    $xn_d_year=9;
	    $xn_d_state=10;
	    $xn_d_remarks=11;
	    $xn_d_starring=12;
	    $xn_d_directed=13;
	    $xn_d_urls=14;
	    $xn_d_content=15;
    }
    elseif($xt=="2"){
    	$url = str_replace('|','-',$url);
    	$xn_list = '<list page="([\s\S]*?)" pagecount="([0-9]+)" pagesize="([0-9]+)" recordcount="([0-9]+)">';
    	$xn_pagesize = 3;
    	$xn_pagecount = 2;
    	$xn_recordcount = 4;
    	$xn_d = '/<video><last>([\s\S]*?)<\/last><id>([0-9]+)<\/id><tid>([0-9]+)<\/tid><name><\!\[CDATA\[([\s\S]*?)\]\]><\/name><type>([\s\S]*?)<\/type><pic>([\s\S]*?)<\/pic><lang>([\s\S]*?)<\/lang><area>([\s\S]*?)<\/area><year>([\s\S]*?)<\/year><state>([\s\S]*?)<\/state><note><\!\[CDATA\[([\s\S]*?)\]\]><\/note><actor><\!\[CDATA\[([\s\S]*?)\]\]><\/actor><director><\!\[CDATA\[([\s\S]*?)\]\]><\/director><dl>([\s\S]*?)<\/dl><des><\!\[CDATA\[([\s\S]*?)\]\]><\/des>([\s\S]*?)<\/video>/';
    	$xn_url = '/<dd flag="([\s\S]*?)"><\!\[CDATA\[([\s\S]*?)\]\]><\/dd>/';
    	$xn_d_time=1;
    	$xn_d_id=2;
	    $xn_d_type=3;
	    $xn_d_name=4;
	    $xn_d_pic=6;
	    $xn_d_lang=7;
	    $xn_d_area=8;
	    $xn_d_year=9;
	    $xn_d_state=10;
	    $xn_d_remarks=11;
	    $xn_d_starring=12;
	    $xn_d_directed=13;
	    $xn_d_urls=14;
	    $xn_d_content=15;
    }
    
    setBreak ("union", "?m=collect-cj-ac2-".$ac2."-xt-".$xt."-ct-".$ct."-group-".$group."-flag-".$flag."-pg-".$pg."-type-" .$type."-wd-".$wd."-apiurl-".$apiurl);
    
    $html = getPage($url, "utf-8");
    preg_match($xn_list ,$html,$array1);
	$pgsize = $array1[$xn_pagesize];
	$pgcount = $array1[$xn_pagecount];
	$recordcount = $array1[$xn_recordcount];
	unset($array1);
	
	if(count($recordcount)==0){
		echo '没有任何可用数据'. jump('?m=collect-list-xt-'.$xt."-ct-".$ct.'-group-'.$group.'-flag-'.$flag.'-apiurl-'.$apiurl,1);
		return;
	}
	
	echo '当前采集任务<strong class="green">'.$pg.'</strong>/<span class="green">'.$pgcount.'</span>页 采集地址&nbsp;'.$url;
	ob_flush();flush();
	
	$inrule = $MAC['collect']['vod']['inrule'];
	$uprule = $MAC['collect']['vod']['uprule'];
	$filter = $MAC['collect']['vod']['filter'];
	
    preg_match_all($xn_d,$html,$array3);
    $i=0;
    foreach($array3[1] as $key=>$value){
    	$i++;
        $rc = false;
        $d_id = $array3[$xn_d_id][$key];
        $d_name = format_vodname($array3[$xn_d_name][$key]);
        $d_remarks = $array3[$xn_d_remarks][$key];
        $d_state = intval($array3[$xn_d_state][$key]);
        $d_type = $xt=='0'? $array3[$xn_d_type][$key] : $flag.$array3[$xn_d_type][$key];
        $d_type = intval( $bindcache[$d_type] );
        $d_starring = htmlspecialchars_decode($array3[$xn_d_starring][$key]);
        $d_directed = htmlspecialchars_decode($array3[$xn_d_directed][$key]);
        $d_pic = $array3[$xn_d_pic][$key];
        $d_time = $array3[$xn_d_time][$key];
        $d_year = intval($array3[$xn_d_year][$key]);
        $d_area = $array3[$xn_d_area][$key];
        $d_lang = $array3[$xn_d_lang][$key];
        $d_content = htmlspecialchars_decode($array3[$xn_d_content][$key]);
        $d_playurls = htmlspecialchars_decode($array3[$xn_d_urls][$key]);
        $d_playurls = str_replace("'", "''",$d_playurls);
        preg_match_all($xn_url,$d_playurls,$array4);
        
        $d_enname = Hanzi2PinYin($d_name);
        $d_letter = strtoupper(substring($d_enname,1));
        $d_addtime = time();
        $d_time = $d_addtime;
        $d_hitstime = "";
        $d_hits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
        $d_dayhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
        $d_weekhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
        $d_monthhits = rand($MAC['collect']['vod']['hitsstart'],$MAC['collect']['vod']['hitsend']);
		
		$d_scorenum = rand(1,500);
        $d_scoreall = $d_scorenum * rand(1,10);
        $d_score = round( $d_scoreall / $d_scorenum ,1);
        
        $d_hide = $MAC['collect']['vod']['hide'];
        if($MAC['collect']['vod']['psernd']==1){
        	$d_content = repPseRnd('vod',$d_content,$i);
        }
        if($MAC['collect']['vod']['psesyn']==1){
        	$d_content = repPseSyn('vod',$d_content);
        }
        
        $d_downfrom='';
        $d_downserver='';
        $d_downnote='';
        $d_downurl='';
        $d_playfrom='';
        $d_playserver='';
        $d_playnote='';
        $d_playurl='';
        $d_tag='';
        $color='red';
        $msg='';
        
        if($d_type<1) { $des = "分类未绑定,系统跳过采集。"; }
        elseif(empty($d_name)) { $des="数据不完整,不进行处理。"; }
        elseif(strpos(','.$filter,$d_name)) { $des="数据在过滤单中,系统跳过采集。"; }
        else{
	        $sql = "SELECT * FROM {pre}vod WHERE d_name ='".$d_name."' ";
	        if(strpos($inrule,'b')){ $sql.=' and d_type='.$d_type; }
	        if(strpos($inrule,'c')){ $sql.=' and d_year='.$d_year; }
	        if(strpos($inrule,'d')){ $sql.=' and d_area=\''.$d_area.'\''; }
	        if(strpos($inrule,'e')){ $sql.=' and d_lang=\''.$d_lang.'\''; }
	        if(strpos($inrule,'f')){ $sql.=' and d_starring=\''.$d_starring.'\''; }
	        if(strpos($inrule,'g')){ $sql.=' and d_directed=\''.$d_directed.'\''; }
	        
	        if($MAC['collect']['vod']['tag']==1){
				$d_tag = getTag($d_name,$d_content);
			}
	        
	        $row = $db->getRow($sql);
	        if(!$row){
	        	foreach($array4[1] as $key=>$value){
	        		if ($rc){ $d_playfrom .= "$$$"; $d_playserver .= "$$$"; $d_playnote .= "$$$"; $d_playurl .= "$$$";}
	        		$d_playfrom .= getFrom($value);
	        		$d_playurl .=  getVUrl($array4[2][$key]);
	        		$d_playserver .='0';
	        		$d_playnote .='';
	        		$rc = true;
	        	}
	        	if($MAC['collect']['vod']['pic']==1){
		    		$ext = @substr($d_pic,strlen($d_pic)-3);
		    		if($ext!='jpg' || $ext!='bmp' || $ext!='gif'){ $ext='jpg'; }
		    		$fname = time() .$i .'.'. $ext;
		    		$path = "upload/vod/" . getSavePicPath('') . "/";
		    		$thumbpath = "upload/vodthumb/" . getSavePicPath('vodthumb') . "/";
		    		$ps = savepic($d_pic,$path,$thumbpath,$fname,'vod',$msg);
		    		if($ps){ $d_pic=$path.$fname; $d_picthumb= $thumbpath.$fname; }
		    	}
		    	if($ct=="1"){
                	$d_downfrom=$d_playfrom;
                	$d_downserver="";
                	$d_downnote = "";
                	$d_downurl=$d_playurl;
                	$d_playfrom="";
                	$d_playserver="";
                	$d_playnote = "";
                	$d_playurl="";
                }
                else{
                	$d_playserver="";
                	$d_downfrom="";
			        $d_downserver="";
			        $d_downnote="";
			        $d_downurl="";
                }
                
	        	$db->Add ("{pre}vod", array("d_type", "d_name","d_enname","d_letter","d_state","d_remarks","d_tag","d_pic",'d_picthumb',"d_hits","d_dayhits","d_weekhits","d_monthhits","d_score","d_scoreall","d_scorenum","d_starring", "d_directed","d_year","d_area","d_lang","d_addtime","d_time","d_hide","d_content","d_playfrom", "d_playserver","d_playnote","d_playurl","d_downfrom" , "d_downserver","d_downnote","d_downurl"), array($d_type,$d_name,$d_enname,$d_letter,$d_state,$d_remarks,$d_tag,$d_pic,$d_picthumb,$d_hits,$d_dayhits,$d_weekhits,$d_monthhits,$d_score,$d_scoreall,$d_scorenum,$d_starring,$d_directed,$d_year,$d_area, $d_lang,$d_addtime,$d_time,$d_hide,$d_content,$d_playfrom,$d_playserver,$d_playnote,$d_playurl,$d_downfrom,$d_downserver,$d_downnote,$d_downurl));
	        	$color='green'; 
	        	$des= "无重名,新加入库成功。";
	        }
	        else{
                if($row['d_lock']==1){
                	$des = "数据已经锁定,系统跳过采集更新。";
                }
                else{
                	if($ct=="1"){
                		$n_from = $row["d_downfrom"];
		                $n_server = $row["d_downserver"];
		                $n_note = $row['d_downnote'];
		                $n_url = $row["d_downurl"];
		                
                	}
                	else{
		                $n_from = $row["d_playfrom"];
		                $n_server = $row["d_playserver"];
		                $n_note = $row['d_playnote'];
		                $n_url = $row["d_playurl"];
		                
		            }
	                $color='red';
                	
	                foreach($array4[1] as $key=>$value){
						$d_playfrom = getFrom($value);
						$d_playserver = '0';
						$d_playurl = getVUrl($array4[2][$key]);
				        
				        $rc = false;
	                    if($n_url==$d_playurl){
	                         $des = "数据相同,暂不更新数据"; continue;
	                    }
	                    elseif(isN($d_playfrom)){
	                    	$des = "播放器类型为空，跳过组合播放地址"; continue;
	                    }
	                    elseif(strpos(",".$n_from,$d_playfrom) <= 0){
	                    	$rc=true;
	                    	$color='green';
	                        $des = "数据发生改变,新增播放组。";
	                        $n_url .= "$$$" . $d_playurl;
	                        $n_from .= "$$$" . $d_playfrom;
	                        $n_server .= "$$$" . $d_playserver;
	                        $n_note .= "$$$" . $d_playnote;
	                    }
	                    else{
	                    	$color='green';
	                        $des = "数据发生改变,更新播放组。";
	                        $arr1 = explode("$$$",$n_url);
	                        $arr2 = explode("$$$",$n_from);
	                        $n_url = "";
	                        
	                        for ($k=0;$k<count($arr2);$k++){
	                            if ($rc){ $n_url .= "$$$";}
	                            if(count($arr1)>=$k){
	                            	if ($arr2[$k] == $d_playfrom){ $arr1[$k] = $d_playurl;}
	                            	$n_url .= $arr1[$k];
	                            }
	                            else{
	                            	$n_url .= $d_playurl;
	                            }
	                            $rc = true;
	                        }
		                }
		            }
		        	
		            if($rc){
		            	
	                	$colarr = array();
	                	$valarr = array();
	                	array_push($colarr,'d_time');
	                	array_push($valarr,time());
	                	
	                	if(strpos(','.$uprule,'a') && $ct!=1){
	                		array_push($colarr,'d_playfrom','d_playserver','d_playnote','d_playurl');
	                		array_push($valarr,$n_from,$n_server,$n_note,$n_url);
	                	}
	                	if(strpos(','.$uprule,'b') && $ct==1){
	                		array_push($colarr,'d_downfrom','d_downserver','d_downnote','d_downurl');
	                		array_push($valarr,$n_from,$n_server,$n_note,$n_url);
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
					    		$ext = @substr($d_pic,strlen($d_pic)-3);
					    		if($ext!='jpg' || $ext!='bmp' || $ext!='gif'){$ext='jpg';}
					    		$fname = time() .$i .'.'. $ext;
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
	            }
            }
            unset($row);
        	unset($array4);
		}
echo <<<EOT
<div>$i.  $d_name  <font color="$color"> $des</font>  $msg </div>
EOT;
ob_flush();flush();
	}
	unset($array3);
	unset($pinyins);
    
    if ($ac2 == "sel"){
        delBreak ("union");
		echo "<br>数据采集完成";
		jump('?m=collect-list-xt-'.$xt."-ct-".$ct.'-group-'.$group.'-flag-'.$flag.'-pg-'.$pg.'-type-'.$type.'-apiurl-'. $apiurl,3);
    }
    else{
		if ($pg >= $pgcount){
            delBreak ("union");
            echo "<br>数据采集完成";
            jump('?m=collect-list-xt-'.$xt."-ct-".$ct.'-group-'.$group.'-flag-'.$flag.'-type-'.$type.'-apiurl-'. $apiurl,1);
        }
        else{
        	jump('?m=collect-cj-ac2-'.$ac2.'-pg-'.($pg+1).'-type-'.$type.'-hour-'.$hour.'-xt-'.$xt."-ct-".$ct.'-group-'.$group.'-flag-'.$flag.'-apiurl-'. $apiurl,3);
        }
    }
}

elseif($method=='vod')
{
	echo '预留功能';
}

elseif($method=='art')
{
	echo '预留功能';
}

elseif($method=='ds')
{
	$plt->set_file('main', $ac.'_'.$method.'.html');
}

elseif($method=='dsgo')
{
	
}

else
{
	showErr('System','未找到指定系统模块');
}
?>