<?php
if(!defined('MAC_ROOT')){
	exit('Access Denied');
}
include(MAC_ROOT.'/inc/common/phplib.php');
include(MAC_ROOT.'/inc/user/qqconnect.php');
@include(MAC_ROOT.'/inc/user/ucenter/config.inc.php');
@include(MAC_ROOT.'/inc/user/ucenter/uc_client/client.php');

if($MAC['user']['status'] == 0){ echo '会员系统关闭中';exit;  }
$db = new AppDb($MAC['db']['server'],$MAC['db']['user'],$MAC['db']['pass'],$MAC['db']['name']);
$tpl->P["siteaid"] = 40;

function chklogin()
{
	global $user;
	if (!empty($_SESSION["userid"])){
		$sql = "SELECT * FROM {pre}user where u_id=".$_SESSION["userid"];
		$user = $GLOBALS['db']->getRow($sql);
		$user['u_regtime'] = date('Y-m-d H:i:s',$user['u_regtime']);
		$user['u_logintime'] = date('Y-m-d H:i:s',$user['u_logintime']);
		$user['u_loginip'] = long2ip($user['u_loginip']);
		if(!empty($user['u_start'])) $user['u_start'] = date('Y-m-d',$user['u_start']);
		if(!empty($user['u_end'])) $user['u_end'] = date('Y-m-d',$user['u_end']);
		
		$loginValidate = md5($user["u_random"] .$user["u_id"]);
		if ($user && $_SESSION["usercheck"] != $loginValidate){
			$_SESSION["userid"] = "";
			$_SESSION["username"] = "";
			$_SESSION["usergourp"] ="";
			$_SESSION["usercheck"] ="";
			sCookie('userid','');
			redirect('?m=user-login.html','top.');
		}
	}
	else{
		redirect('?m=user-login.html','top.');
	}
}

if($method=='iframe')
{
	$logged = false;
    $fname = "userlogin";
    if(!empty($_SESSION["userid"])){ $logged = true; $fname = "userlogged"; }
    $tpl->H = loadFile(MAC_ROOT."/template/".$MAC['site']['templatedir']."/".$MAC['site']['htmldir']."/".$fname.".html");
	$tpl->H = str_replace(array("{maccms:userlink}","{maccms:userreglink}","{maccms:userfindpasslink}","{maccms:userlogoutlink}"), array(MAC_PATH."index.php?m=user-index.html",MAC_PATH."index.php?m=user-reg.html",MAC_PATH."index.php?m=user-findpass.html",MAC_PATH."index.php?m=user-logout.html"),$tpl->H);
	
    if ($logged){
        $row = $db->getRow("SELECT u_id,u_name,u_qq,u_email,u_phone,u_regtime,u_status,u_points,u_extend,u_loginnum, u_logintime,u_loginip,u_flag,u_start,u_end,u_group FROM {pre}user where u_id=".intval($_SESSION["userid"]) );
        if($row){
        	$grouparr = $MAC_CACHE['usergroup'][$row['u_group']];
			$tpl->H = str_replace(array("{maccms:userid}","{maccms:username}","{maccms:userqq}","{maccms:useremail}","{maccms:userphone}","{maccms:userregtime}","{maccms:userpoints}","{maccms:userextend}","{maccms:userlogintime}","{maccms:userloginnum}","{maccms:userloginip}","{maccms:usergroupid}","{maccms:usergroupname}"), array($row["u_id"],$row["u_name"],$row["u_qq"],$row["u_email"],$row["u_phone"],date('Y-m-d H:i:s',$row["u_regtime"]),$row["u_points"],$row["u_extend"],date('Y-m-d H:i:s',$row["u_logintime"]),$row["u_loginnum"],long2ip($row["u_loginip"]),$grouparr["ug_id"],$grouparr["ug_name"]),$tpl->H);
			
        }
        unset ($row);
    }
    $tpl->mark();
}

elseif($method=='ajaxinfo')
{
	chklogin();
	$grouparr = $MAC_CACHE['usergroup'][$user['u_group']];
	$user['ug_name'] = $grouparr["ug_name"];
	echo json_encode($user);
}

elseif($method=='check')
{
	$u_name = be("post","u_name");
	$u_password = md5(be("post","u_password"));
	$flag = be("all","flag"); $backurl = be("all","backurl");
	if (isN($flag)){ $flag = "iframe";}
	
	$row = $db->getRow("SELECT u_id,u_name,u_flag,u_start,u_end,u_group,u_loginnum FROM {pre}user where u_name='".$u_name."' and u_password= '".$u_password."' and u_status=1");
	
	if (!$row){
		alert("您输入的用户名和密码不正确或者您的账户已经被锁定!","");
	}
	else{
		$randnum = md5(rand(1,99999999));
		$u_flag = $row["u_flag"];
		
		if ($u_flag == 1){
			if ( time() > $row["u_end"] ){
				$u_flag = 0;
				$u_start="";
				$u_end="";
			}
			else{
				$u_start=$row["u_start"];
				$u_end=$row["u_end"];
			}
		}
		else{
			$ugroup=$row["u_group"];
		}
		
		$_SESSION["userid"] = $row["u_id"];
		$_SESSION["username"] = $row["u_name"];
		$_SESSION["usergroup"] = $row["u_group"];
		$_SESSION["usercheck"] = md5($randnum .$row["u_id"]);
		sCookie('userid',$row["u_id"]);
		$db->Update ("{pre}user",array("u_logintime","u_loginip","u_random","u_loginnum","u_flag","u_start","u_end"),array(time(),ip2long(getIP()),$randnum,$row["u_loginnum"]+1,$u_flag,$u_start,$u_end),"u_id=".$row["u_id"]);
		
		unset($row);
		
		if($MAC['connect']['uc']['status']==1){
			list($uid, $username, $password, $email) = uc_user_login($u_name, $u_password);
			if($uid>0){
				$ucsynlogin = uc_user_synlogin($uid);
			}
		}
		if (isN($backurl)){
			if ($flag=="iframe"){
				showMsg($ucsynlogin.'登录成功','index.php?m=user-iframe.html');
				//redirect('index.php?m=user-iframe.html');
			}
			else{
				showMsg($ucsynlogin.'登录成功','index.php?m=user-index.html');
				//redirect('index.php?m=user-index.html','top.');
			}
		}
		else{
			showMsg($ucsynlogin.'登录成功',$backurl);
			//redirect($backurl,'top.');
		}
	}
}

elseif($method=='logout')
{
	$flag = $tpl->P['flag'];
	$_SESSION["userid"] = "";
	$_SESSION["username"] = "";
	$_SESSION["usergroup"] = "";
	$_SESSION["usercheck"] = "";
	sCookie('userid','');
	if($MAC['connect']['uc']['status']==1){
		$ucsynlogout = uc_user_synlogout();
		echo $ucsynlogout;
	}
	if ($flag=="iframe"){
		redirect('index.php?m=user-iframe.html');
	}
	else{
		redirect('index.php?m=user-login.html','top.');
	}
}

elseif($method=='regcheck')
{
	$status="true";
	$s=$tpl->P['s'];
	$t=$tpl->P['t'];
	switch($t)
	{
		case "u_name": $where = " AND u_name='" .$s ."'";break;
		case "u_email": $where = " AND u_email='" . $s ."'";break;
		case "u_qq": $where = " AND u_qq='" . $s ."'";break;
		case "u_code": if ($_SESSION["code_userreg"] != $s){ $status="false"; } break;
		default : $where="";break;
	}
	if($t!="u_code" && $status=="true"){
		$sql = "SELECT count(*) FROM {pre}user WHERE 1=1 " . $where;
		$num = $db->getOne($sql);
		if($num>0){ $status= "false"; }
	}
	echo "{\"res\":".$status."}";;
}

elseif($method=='regsave')
{
	if ($MAC['user']['reg'] == 0){ showErr('System','系统已经关闭注册');return;}
	
	$u_code = be("post","u_code");
	if ($u_code ==""){ alert ("请返回输入确认码。返回后请刷新登陆页面后重新输入正确的信息。");exit;}
	if (trim($u_code) != $_SESSION["code_userreg"]) { alert ("确认码错误，请重新输入。返回后请刷新登陆页面后重新输入正确的信息。") ;exit;}
	
	$u_name = be("post","u_name");
	$u_password1 = be("post","u_password1");
	$u_password2 = be("post","u_password2");
	$u_email = be("post","u_email");
	
	if ($u_password1 != $u_password2){ alert ("两次密码不同");exit;}
	if (strlen($u_name) >32) { $u_name= substring($u_name,32);}
	if (strlen($u_password1) >32) { $u_password1=substring($u_password1,32);}
	if (strlen($u_email) > 32) { $u_email=substring($u_email,32);}
	$u_name = strip_tags($u_name);
	
	if($MAC['connect']['uc']['status']==1){
		$uid = uc_user_register($u_name, $u_password1, $u_email);
		if($uid <= 0) {
			if($uid == -1) {
				alert('UC同步注册失败：用户名不合法');
			} elseif($uid == -2) {
				alert( 'UC同步注册失败：包含要允许注册的词语');
			} elseif($uid == -3) {
				alert( 'UC同步注册失败：用户名已经存在');
			} elseif($uid == -4) {
				alert( 'UC同步注册失败：Email 格式有误');
			} elseif($uid == -5) {
				alert( 'UC同步注册失败：Email 不允许注册');
			} elseif($uid == -6) {
				alert( 'UC同步注册失败：该 Email 已经被注册');
			} else {
				alert( 'UC同步注册失败：未定义');
			}
		}
	}
	
	$u_password1 = md5($u_password1);
	
	$row = $db->getRow("SELECT * FROM {pre}user WHERE u_name='".$u_name."'");
	if (!$row){
		 $db->Add  ("{pre}user",array("u_name", "u_password","u_qq","u_email","u_regtime","u_status","u_points","u_group","u_phone","u_question","u_answer"),array($u_name,$u_password1,$u_qq,$u_email,time(),$MAC['user']['regstate'],$MAC['user']['regpoint'],$MAC['user']['reggroup'],$u_phone,$u_question,$u_answer));
		alertUrl ("注册成功,正在转向登录页面","?m=user-login.html");
		return;
	}
	else{
		alert ("注册失败,该用户名已经被使用" );
		return;
	}
	unset($row);
}

elseif($method=='findpasssave')
{
	$u_name = be("post","u_name");
	$u_password = be("post","u_password");
	$u_email = be("post","u_email");
	$u_question = be("post","u_question");
	$u_answer = be("post","u_answer");
	
	if (strlen($u_name) >32) { $u_name= substring($u_name,32);}
	if (strlen($u_question) >255) { $u_question=substring($u_question,255);}
	if (strlen($u_answer) >255) { $u_answer=substring($u_answer,255);}
	$u_password = md5($u_password);
	if(isN($u_question) || isN($u_answer) || isN($u_password) || isN($u_name)){
		alert ("表单信息不完整,请重填!"); exit;
	}
	if (getTimeSpan("last_findpass") < 5){ alert ("系统繁忙，请稍候重试");exit;}
	
	$_SESSION["last_findpass"] = time();
	
	$row = $db->getRow("SELECT * FROM {pre}user WHERE u_name='".$u_name."'");
	if (!$row){
		alert ("重置密码失败1"); return;
	}
	else{
		if ($u_question != $row["u_question"] || $u_answer != $row["u_answer"]){ alert ("重置密码失败2"); return;}
		$db->Update ("{pre}user",array("u_password"),array($u_password),"u_id=". $row["u_id"]);
		alertUrl ("重置密码成功,正在转向登录页面","?action=login"); return;
	}
	unset($row);
}

elseif($method=='tg')
{
	$userid = intval($tpl->P['uid']);
	if($userid>0){
		$ip = ip2long(getIP());
		$ly=  getReferer();
		
		$todaydate = time();
		$tommdate = strtotime('+1 day');
		
		$sql="select * from {pre}user_visit where uv_uid=".$userid." and uv_ip='".$ip."' and uv_time>=".$todayunix." and uv_time <=".$tommunix;
		
		$row1 = $db->getRow($sql);
		if (!$row1){
			$db->Add ("{pre}user_visit",array("uv_uid","uv_ip","uv_ly","uv_time"), array($userid,$ip,$ly, time()));
			$db->query ("update {pre}user set u_extend=u_extend+1,u_points=u_points+".$MAC['user']['popularize']." where u_id=".$userid);
			$sql="delete from {pre}user_visit where uv_time<".$todayunix;
			$db->query($sql);
		}
		unset($row1);
	}
	redirect ("../");
}

elseif($method=='save')
{
	chklogin();
	$oldpass = be("post","u_oldpass");
	$password1 = be("post","u_password1");
	$password2 = be("post","u_password2");
	$u_qq= be("post","u_qq");
	$u_email = be("post","u_email");
	$u_phone = be("post","u_phone");
	$u_question = be("post","u_question");
	$u_answer = be("post","u_email");
	
	if (strlen($u_email)>32) { $u_email = substring($u_email,32);}
	if (strlen($u_qq)>16) { $u_qq = substring($u_qq,16);}
	if (strlen($u_phone)>16) { $u_phone = substring($u_phone,16);}
	
	$col = array("u_qq","u_email","u_password","u_phone","u_question","u_answer") ;
	$val = array($u_qq,$u_email,$password1,$u_phone,$u_question,$u_answer);
	
	if ($password1 != ""){
		if ($password1 != $password2){ alert ("两次密码不同");exit; }
		$password1 = md5($password1);
		array_push($col,"u_password");
		array_push($val,$password1);
	}
	$db->Update ("{pre}user",$col ,$val ,"u_id=".$user["u_id"]);
	alertUrl ("修改成功！","index.php?m=user-info.html");
}

elseif($method=='upgradesave')
{
	chkLogin();
	$flag=be("all","flag");
	
	if($flag=="2"){
		$baoshi = intval(be("post","baoshi"));
		switch($baoshi)
		{
			case 2:
				$baoshi = $MAC['user']['monthpoint']; $dn=30;
				break;
			case 3;
				$baoshi = $MAC['user']['yearpoint']; $dn=365;
				break;
			default :
				$baoshi = $MAC['user']['weekpoint']; $dn=7;
				break;
		}
		if($user['u_points']< $baoshi) { alert ("您的积分不够，无法包时长!"); return; }
		$send = strtotime( $user['u_end'] );
		if($send < time()){
			$u_end = time()+ 86400*$dn;
		}
		else{
			$u_end = strtotime($user['u_end'])+ 86400*$dn;
		}
		$sql= "UPDATE {pre}user set u_points=u_points-".$baoshi.",u_flag=1,u_start='".time()."',u_end='".$u_end."' WHERE u_id=". $user["u_id"];
	}
	else{
		$u_group = intval(be("post","u_group"));
		$curgroup = $GLOBALS['MAC_CACHE']['usergroup'][$user['u_group']];
		$newgroup = $GLOBALS['MAC_CACHE']['usergroup'][$u_group];
		if(!is_array($newgroup)) { alert("获取目标会员组失败，请重试"); return; }
		
		if ($u_group == $user['u_group']){ alert ("您已经是该会员组成员无需升级!");}
		if ($curgroup["ug_popvalue"] >= $newgroup["ug_popvalue"]){ alert("您现在所属会员组的权限制大于等于目标会员组权限值，不需要升级!"); return;}
		if ($user['u_points'] < $newgroup["ug_upgrade"]) { alert ("您的积分不够，无法升级到该会员组!"); return;}
		$sql= "UPDATE {pre}user set u_points=u_points-".$newgroup["ug_upgrade"].",u_group=".$u_group."  WHERE u_id=".$user["u_id"];
		unset($curgroup);
		unset($newgroup);
	}
	$db->query($sql);
	alertUrl ("会员权限升级成功,请重新登陆！","?m=user-upgrade.html");
}

elseif($method=='del')
{
	chkLogin();
	$flag = $tpl->P['flag'];;
	$clear = $tpl->P['clear'];
	
	if($flag=="plays"){} elseif ($flag=="downs") {} elseif ($flag=="fav"){} else { echo "参数错误"; return; }
	
	if(!empty($clear)){
		$db->Update  ("{pre}user" ,array("u_".$flag), array("") ,"u_id=".$user["u_id"] );
	}
	else{
		$ids = be("arr","d_id");  $ids = ",".$ids.",";
		$data = $user['u_'.$flag];
		$dataarr = explode(",",$data);  $data="";
		$rc=false;
		foreach($dataarr as $a){
			if(!empty($a)){
				$one = explode("-",$a);
				$id = $one[0];
				if(strpos($ids,$id)<=0){
					if($rc) { $data.=","; }
					$data .= $a;
					$rc=true;
				}
			}
		}
		unset($dataarr);
		if(!empty($data)){ $data = ",".$data.","; }
		$db->Update  ("{pre}user" ,array("u_".$flag), array($data) ,"u_id=".$user["u_id"] );
	}
	alerturl ("操作成功!","?m=user-".$flag.".html");
}

elseif($method=='paysave')
{
	chkLogin();
	$cardnum = be("post","cardnum");
	$cardpwd = be("post","cardpwd");
	if (isN($cardnum)){ alert ("卡号不能为空！" );exit;}
	if (isN($cardpwd)) { alert ("卡号密码不能为空" ); exit;}
	
	$sql = "SELECT * FROM {pre}user_card WHERE c_number='". $cardnum ."'and c_pass='". $cardpwd."'";
    $row = $db->getRow($sql);
	if (!$row){
		alert ("该充值卡不存在或者卡号密码错了");exit;
	}
	else{
		if ($row["c_used"]==1){
		   alert ("此卡已经充值，请不要重复使用此卡");exit;
		}
		$c_id = $row["c_id"];
		$c_point = $row["c_point"];
	}
	unset($rs);
	
	$sql = "SELECT * FROM {pre}user WHERE u_id=" .$user["u_id"]. "";
	$row1 = $db->getRow($sql);
	if (!$row1){
		alert ("获取会员信息出错,充值失败"); exit ;
	}
	else{
		$db->query("update {pre}user set u_points=u_points+".$c_point." where u_id = ". $user["u_id"]);
	}
	unset($row);
	unset($row1);
	$db->query("update {pre}user_card set c_used=1,c_user=". $user["u_id"] .",c_sale=1,c_usetime='". date('Y-m-d H:i:s',time()) ."' where c_id = ". $c_id);
	alert ("充值成功");
}

elseif($method=='paysave2')
{
	chkLogin();
	$buynum = be("post","buynum");
	if (!isNum($buynum)){ alert ("充值金额必须是数字！" );exit; } else { $buynum=intval($buynum); }
	if ($buynum < app_buymin) { alert ("最小充值金额是".app_buymin."元，请重填！" );exit; }
}

elseif($method=='paysave')
{
	chkLogin();
	$cardnum = be("post","cardnum");
	$cardpwd = be("post","cardpwd");
	if (isN($cardnum)){ alert ("卡号不能为空！" );exit;}
	if (isN($cardpwd)) { alert ("卡号密码不能为空" ); exit;}
	
	$sql = "SELECT * FROM {pre}user_card WHERE c_number='". $cardnum ."'and c_pass='". $cardpwd."'";
    $row = $db->getRow($sql);
	if (!$row){
		alert ("该充值卡不存在或者卡号密码错了");exit;
	}
	else{
		if ($row["c_used"]==1){
		   alert ("此卡已经充值，请不要重复使用此卡");exit;
		}
		$c_id = $row["c_id"];
		$c_point = $row["c_point"];
	}
	unset($rs);
	
	$sql = "SELECT * FROM {pre}user WHERE u_id=" . $user["u_id"] . "";
	$row1 = $db->getRow($sql);
	if (!$row1){
		alert ("获取会员信息出错,充值失败"); exit ;
	}
	else{
		$db->query("update {pre}user set u_points=u_points+".$c_point." where u_id = ". $user["u_id"]);
	}
	unset($row);
	unset($row1);
	$db->query("update {pre}user_card set c_used=1,c_user=". $user["u_id"] .",c_sale=1,c_usetime='". date('Y-m-d H:i:s',time()) ."' where c_id = ". $c_id);
	alert ("充值成功");
}

elseif($method=='paysave2')
{
	chkLogin();
	$buynum = be("post","buynum");
	if (!isNum($buynum)){ alert ("充值金额必须是数字！" );exit; } else { $buynum=intval($buynum); }
	if ($buynum < app_buymin) { alert ("最小充值金额是".app_buymin."元，请重填！" );exit; }
}

elseif($method=='reg')
{
	if ($MAC['user']['reg'] == 0){ echo "系统已经关闭注册";return;}
	$ref= $tpl->P['ref'];
	$MAC['connect']['qq']['url'] = "http://" .$_SERVER["HTTP_HOST"] ."/index.php?m=user-reg-ref-qqlogged";
	
	if ($ref=="qqlogin"){
		if($MAC['connect']['qq']['status']==0) { echo 'QQ一键登录已关闭';return; }
		$qc = new QqConnect();
		$url = $qc->create_login_url();
		unset($qc);
		redirect($url);
	}
	elseif ($ref=="qqlogged"){
		if($MAC['connect']['qq']['status']==0) { echo 'QQ一键登录已关闭';return; }
		$qc = new QqConnect();
		if($qc->checkLogin()){
			$qc->callback();
			$qqid = $qc->get_openid();
			$userinfo = $qc->get_user_info();
			$nickname = $userinfo["nickname"]; $nickname=replaceStr($nickname,"'","");
			$tmpname = $nickname;
			
			$i=0;
			$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_qid='" . $qqid . "'");
			if ($rscount == 0){
				$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_name='" . $tmpname . "'");
				
				while ($rscount>0)
				{
					$tmpname = $nickname . $i;
					$rscount = $db->getOne("SELECT count(*) FROM {pre}user where u_name='" . $tmpname . "'");
					$i++;
				}
				$nickname = $tmpname;
				
				$db->Add( "{pre}user",array("u_name","u_qid", "u_password","u_qq","u_email","u_regtime","u_status","u_points","u_group","u_phone","u_question","u_answer"),array($nickname,$qqid,md5(""),"","",time(),$MAC['user']['regstate'],$MAC['user']['regpoint'],$MAC['user']['reggroup'],"","",""));
			}
			
			$row = $db->getRow("SELECT u_id,u_qid,u_name,u_qq,u_email,u_regtime,u_status,u_points,u_extend,u_loginnum, u_logintime,u_loginip,u_random,u_flag,u_start,u_end,u_group FROM {pre}user where u_qid='".$qqid."' and u_status =1");
			
			if($row){
				$randnum = md5(rand(1,99999999));
				if ($row["u_flag"] == 1){
					if ( time() > strtotime($row["u_end"]) ){
						$u_flag = $MAC['user']['reggroup'];
						$u_start="";
						$u_end="";
					}
					else{ 
						$u_flag = $row["u_flag"];
						$u_start=$row["u_start"];
						$u_end=$row["u_end"];
					}
				}
				else{
					$ugroup=$row["u_group"];
				}
				$db->Update ("{pre}user",array("u_logintime","u_loginip","u_random","u_loginnum","u_flag","u_start","u_end"),array(time(),ip2long(getIP()),$randnum,$row["u_loginnum"]+1,$u_flag,$u_start,$u_end),"u_id=".$row["u_id"]);
				
				$_SESSION["userid"] = $row["u_id"];
				$_SESSION["username"] = $row["u_name"];
				$_SESSION["usergroup"] = $row["u_group"];
				$_SESSION["usercheck"] = md5($randnum . $row["u_id"]);
				sCookie('userid',$row["u_id"]);
			}
			unset($row);
			if ($randnum !=""){ redirect('../');  }
		}
		unset($qc);
	}
	else{
		$plt = new Template(MAC_ROOT."/template/user/html/"); 
		$plt->set_file("main","user_reg.html");
		$plt->set_file("header", "user_head.html");
		$plt->set_file("footer", "user_foot.html");
		$plt->parse("head", "header");
		$plt->parse("foot", "footer");
		$plt->parse("mains", "main");
		$tpl->H = $plt->get_var('mains');
		$tpl->mark();
	}
}

elseif($method=='login' || $method=='findpass')
{
	$plt = new Template(MAC_ROOT."/template/user/html/"); 
	$plt->set_file("main","user_".$method.".html");
	$plt->set_file("header", "user_head.html");
	$plt->set_file("footer", "user_foot.html");
	$plt->parse("head", "header");
	$plt->parse("foot", "footer");
	$plt->parse("mains", "main");
	$tpl->H = $plt->get_var('mains');
	$tpl->mark();
}

else
{
	chkLogin();
	if(empty($method)){ $method="index"; }
	$grouparr = $GLOBALS['MAC_CACHE']['usergroup'][$user['u_group']];
	
	
	$plt = new Template(MAC_ROOT."/template/user/html/"); 
	$plt->set_file("main","user_". $method .".html");
	$plt->set_file("header", "user_head.html");
	$plt->set_file("footer", "user_foot.html");
	$plt->parse("head", "header");
	$plt->parse("foot", "footer");
	$plt->set_var("ug_name",$grouparr['ug_name']);
	$plt->set_var("u_flag",getUserFlag($user['u_flag']));
	$plt->set_var("u_start",$user['u_start']==0 ?'':$user['u_start'] );
	$plt->set_var("u_end",$user['u_end']==0 ?'':$user['u_end'] );
	
	$col = array("u_id","u_name","u_qq","u_email","u_phone","u_question","u_answer","u_regtime","u_loginip","u_logintime","u_points","u_loginnum","u_extend");
	foreach($col as $a){
		$plt->set_var($a,$user[$a]);
	}
	unset($col);
	
	if($method=='popedom'){
		$plt->set_block( "main", "row", "rows" );
		$typearr = $GLOBALS['MAC_CACHE']['vodtype'];
		foreach( $typearr as $a ) {
	    	$plt->set_var( 't_name', $a['t_name'] );
	    	$ck1 = getUserPopedom( $a["t_id"],"list") ==true ? "ok.png" : "cancel.png";
	    	$ck2 = getUserPopedom( $a["t_id"],"vod") ==true ? "ok.png" : "cancel.png";
	    	$ck3 = getUserPopedom( $a["t_id"],"play") ==true ? "ok.png" : "cancel.png";
	    	$ck4 = getUserPopedom( $a["t_id"],"down") ==true ? "ok.png" : "cancel.png";
			$plt->set_var( 'chk1', $ck1 );
			$plt->set_var( 'chk2', $ck2 );
			$plt->set_var( 'chk3', $ck3 );
			$plt->set_var( 'chk4', $ck4 );
			$plt->parse( 'rows', 'row', true );
	   }
	}
	elseif($method=='upgrade'){
		$plt->set_block( "main", "row", "rows" );
		foreach($GLOBALS['MAC_CACHE']['usergroup'] as $a){
			$num++;
			$plt->set_var( 'sel_val', $a['ug_id'] );
			$plt->set_var( 'sel_name', $a['ug_name'] );
			$plt->set_var( 'sel_upgrade', $a['ug_upgrade'] );
			$plt->parse( 'rows', 'row', true );
		}
		if( $num==0 ){
			$plt->parse("","row",true);
			$plt->set_var("rows","");
		}
		if($user['u_flag']==1){
			$u_flagdes = "起始时间：".getColorDay($user['u_start'])." - 截止时间：".getColorDay($user['u_end']);
		}
		elseif($user['u_flag']==2){
			$u_flagdes = "起始IP：".long2ip($user['u_start'])." - 截止IP：".long2ip($user['u_end']);
		}
		else{
			$u_flagdes = "剩余点数：".$user['u_points'];
		}
		$plt->set_var( 'u_flagdes', $u_flagdes );
		$plt->set_var( 'weekpoint', $MAC['user']['weekpoint'] );
		$plt->set_var( 'monthpoint', $MAC['user']['monthpoint'] );
		$plt->set_var( 'yearpoint', $MAC['user']['yearpoint'] );
	}
	elseif($method=='plays' || $method=='downs' || $method=="fav"){
		
		$plt->set_block( "main", "row", "rows" );
		$s = $user['u_'.$method];
		if(!empty($s)){ $s = substring($s,strlen($s)-2,1); }
		
		$page = intval(be("get","page")); if ($page < 1) { $page = 1;}
		$rc=false;
		$arr = explode(",",$s);
		foreach($arr as $a){
			if(!empty($a)){
				$one = explode("-",$a);
				$id = $one[0];
				if(strpos(",".$ids,$id)<=0){
					if($rc){ $ids.=","; }
					$ids.= $id;
					$rc=true;
				}
				unset($one);
			}
			
		}
		unset($arr);
		$num=0;
		if(!empty($ids)){
			$sql = "SELECT d_id,d_name,d_enname,d_type,d_stint,d_stintdown,d_addtime FROM {pre}vod  where d_id in (". $ids.") order by d_time desc ";
			$sql .= " limit ".(30 * ($page-1)) .",30";
			
			$rs = $db->query($sql);
			
			while ($row = $db ->fetch_array($rs))
			{
				$num++;
				
				$typearr =  $GLOBALS['MAC_CACHE']['vodtype'][$row['d_type']];
				if ($MAC['vod']['playtype']==0){
				 	$alink = "../".$tpl->getLink('vod','detail',$typearr,$row);
					$alink = str_replace("../".MAC_PATH,"../",$alink);
				}
				 else{
				 	$alink = "../".$tpl->getLink('vod','play',$typearr,$row);
				 	$alink = str_replace("../".MAC_PATH,"../",$alink);
				 	$alink = str_replace("javascript:OpenWindow1('","",$alink);
				 	$alink = str_replace("',popenW,popenH);","",$alink);
			 	}
				if (substring($alink,1,strlen($alink)-1)=="/") { $alink .= "index.". $MAC['vod']['suffix'];}
				$plt->set_var( 't_name', $typearr['t_name'] );
				$plt->set_var( 'd_id', $row['d_id'] );
				$plt->set_var( 'd_name', $row['d_name'] );
				$plt->set_var( 'd_stint', $row['d_stint'] );
				$plt->set_var( 'd_link', $alink );
				$plt->parse( 'rows', 'row', true );
			}
			unset($rs);
		}
		if( $num==0 ){
			$plt->parse("","row",true);
			$plt->set_var("rows","");
		}
	}
	elseif($method=='pay'){
		
	}
	elseif($method=='pay2'){
		$plt->set_var( 'pay_min', $MAC['pay']['app']['min'] );
		$plt->set_var( 'pay_exc', $MAC['pay']['app']['exc'] );
		$plt->set_var( 'pay_order', time() );
		$plt->set_var( 'pay_returnurl', 'http://'. $MAC['site']['url']."/index.php?m=user-buyreturnurl.html" );
		$plt->set_var( 'pay_notifyurl', 'http://'. $MAC['site']['url']."/index.php?m=user-buynotifyurl.html" );
		$plt->set_var( 'pay_alipay_id', $MAC['pay']['alipay']['id'] );
		$plt->set_var( 'pay_alipay_key', $MAC['pay']['alipay']['key'] );
		$plt->set_var( 'pay_alipay_no', $MAC['pay']['alipay']['no'] );
		$plt->set_var( 'pay_ys_id', $MAC['pay']['ys']['id'] );
		$plt->set_var( 'pay_ys_key', $MAC['pay']['ys']['key'] );
	}
	elseif($method=='info'){
		
	}
	elseif($method=='wel'){
		
	}
	elseif($method=='index'){
		
	}
	else
	{
		showErr('System','未找到指定系统模块');
	}
	
	$plt->set_var("siteurl",$MAC['site']['url']);
	$plt->set_var("sitename",$MAC['site']['name']);
	$plt->set_var("runtime",getRunTime());
	$plt->parse("mains", "main");
	$tpl->H = $plt->get_var('mains');
	$tpl->mark();
}
unset($user);
?>