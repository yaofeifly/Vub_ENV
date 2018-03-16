<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>会员中心</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="templets/default/images/style.css" type="text/css" media="all" />
    <script src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script type="text/javascript">
		$(document).ready(function() {  
            var $div_li = $("#tab_menu ul li");
            $div_li.click(function () {
                $(this).addClass("current").siblings().removeClass("current");
                var div_index = $div_li.index(this);
                $("#tab_box>div").eq(div_index).show().siblings().hide();
            }).hover(function () {
                $(this).addClass("hover");
            }, function () {
                $(this).removeClass("hover");
            });
});
        </script>
<style type=text/css>
body{background:none repeat scroll 0 0 #fff;color:#8a8a8a;font:12px/1.7 Helvetica,Arial,Tahoma,sans-serif,"宋体"}.row{width:960px;margin:10px auto;font-size:16px}.u-menu{width:170px;float:left}li.item{margin-bottom:5px}.u-nav h3,.u-nav .sub a{background:#5db400;padding-left:50px;color:#fff}.u-nav .sub{padding:5px 0}.u-nav .sub li{height:32px;overflow:hidden;padding-top:1px}.u-nav .sub a{display:block;height:32px;line-height:32px;overflow:hidden;color:#fff;font-size:14px;background:#ddd;text-decoration:none;padding-left:58px}.u-nav .sub .current{color:#fff;text-decoration:none;background:#84db27}.u-nav .sub a:hover{color:#fff;text-decoration:none;background:#84db27}.u-main{float:right;width:780px}.u-tab{height:35px;border-bottom:2px solid #fd9c28}.u-tab li{background:#ffefd1;float:left;height:35px;line-height:37px;text-align:center;width:115px;margin-right:5px;font-size:14px;overflow:hidden;cursor:pointer}.u-tab .current{font-weight:bold;background:#ff9c01}.u-tab a{line-height:37px;color:#c76001}.u-tab .current a{color:#fff}.u-tab .current a:hover{color:#fff}.u-form-wrap{border:2px solid #ff9c01;border-top:0;padding:20px;zoom:1}.u-table{width:100%}.u-table th,.u-table td{border:1px solid #ebebeb;height:35px;text-align:center}.u-table th{background:#fafafa;font-size:14px;font-weight:normal}.u-table-left td{border:1px solid #ebebeb;height:35px;text-align:left;padding-left:30px}body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,p,form,fieldset,legend,input,button,textarea,th,td{margin:0;padding:0}a{color:#8a8a8a;text-decoration:none}table{border-collapse:collapse;border-spacing:0}li{list-style:none}html{background:#fff}body{background:#fff;color:#8a8a8a;font:12px/1.7 Helvetica,Arial,Tahoma,sans-serif,"\5B8B\4F53"}.clearfix:after,.row:after{clear:both;content:'\0020';display:block;height:0}.clearfix,.row{zoom:1}.clear{clear:both}
</style>
</head>
<?php
session_start();
require_once("include/common.php");
require_once(sea_INC.'/main.class.php');
if($cfg_user==0)
{
	ShowMsg('系统已关闭会员功能!','-1');
	exit();
}

$action = isset($action) ? trim($action) : 'cc';
$page = isset($page) ? intval($page) : 1;
$uid=$_SESSION['sea_user_id'];
$uid = intval($uid);
$hashstr=md5($cfg_dbpwd.$cfg_dbname.$cfg_dbuser);//构造session安全码
if(empty($uid) OR $_SESSION['hashstr'] !== $hashstr)
{
	showMsg("请先登录","login.php");
	exit();
}
if($action=='chgpwdsubmit')
{
	if(trim($newpwd)<>trim($newpwd2))
	{
		ShowMsg('两次输入密码不一致','-1');	
		exit();	
	}
	if(!empty($newpwd)||!empty($email))
	{
	if(empty($newpwd)){$pwd = $oldpwd;} else{$pwd = substr(md5($newpwd),5,20);};
	$dsql->ExecuteNoneQuery("update `sea_member` set password = '$pwd' ".(empty($email)?'':",email = '$email'")." where id= '$uid'");
	ShowMsg('资料修改成功','-1');	
	exit();	
	}
}
elseif($action=='cancelfav')
{
	$dsql->executeNoneQuery("delete from sea_favorite where id=".$id);
	echo "<script>location.href='?action=favorite'</script>";
	exit();
}elseif($action=='cancelfavs')
{
	if(empty($fid))
	{
		showMsg("请选择要取消收藏的视频","-1");
		exit();
	}
	foreach($fid as $id)
	{
		$dsql->executeNoneQuery("delete from sea_favorite where id=".$id);
	}
	echo "<script>location.href='?action=favorite'</script>";
	exit();
}
elseif($action=='cz')
{
	$key=mysql_real_escape_string($_POST['cckkey'],$dsql->linkID);
	$key = RemoveXSS(stripslashes($key));
	$key = addslashes(cn_substr($key,200));
	if($key==""){showMsg("没有输入充值卡号","-1");exit;}
	$sqlt="SELECT * FROM sea_cck where ckey='$key'";
	$row1 = $dsql->GetOne($sqlt);
    if(!is_array($row1) OR $row1['status']<>0){
        showMsg("充值卡不正确或已被使用","-1");exit;
    }else{
		$uname=$_SESSION['sea_user_name'];
		$points=$row1['climit'];
        $dsql->executeNoneQuery("UPDATE sea_cck SET usetime=NOW(),uname='$uname',status='1' WHERE ckey='$key'");
		$dsql->executeNoneQuery("UPDATE sea_member SET points=points+$points WHERE username='$uname'");
		showMsg("恭喜！充值成功！","member.php?action=cc");exit;
    }
}
elseif($action=='hyz')
{
	//对所有数据进行重新查询，防止伪造POST数据进行破解
	
	//获取会员组基本信息
	$gid = intval($gid);
	if(empty($gid))
	{showMsg("请选择要购买的会员组","member.php?action=cc");exit;}
	$sqlhyz1="SELECT * FROM sea_member_group where gid='$gid'"; 
	$rowhyz1 = $dsql->GetOne($sqlhyz1);
    if(!is_array($rowhyz1)){
        showMsg("会员组不存在","-1");exit;
    }else{
		$hyzjf=$rowhyz1['g_upgrade']; //购买会员组所需积分  
    }
	//获取会员基本信息
	$uname=$_SESSION['sea_user_name'];
	$uname = RemoveXSS($uname);
	$sqlhyz2="SELECT * FROM sea_member where username='$uname'"; 
	$rowhyz2 = $dsql->GetOne($sqlhyz2);
    if(!is_array($rowhyz2)){
        showMsg("会员信息不存在","-1");exit;
    }else{
		$userjf=$rowhyz2['points']; //购买会员组所需积分
    }
	
	if($userjf<$hyzjf)
	{
		showMsg("积分不足","-1");exit; //判断积分是否足够购买
	} 
	else
	{
		$dsql->executeNoneQuery("UPDATE sea_member SET points=points-$hyzjf,gid=$gid where username='$uname'");
		showMsg("恭喜！购买会员组成功，重新登陆后会员组生效！","member.php?action=cc");exit;
	}
	
}
elseif($action=='cc')
{
	$ccgid=intval($_SESSION['sea_user_group']);
	$ccuid=intval($_SESSION['sea_user_id']);
	$cc1=$dsql->GetOne("select * from sea_member_group where gid=$ccgid");
	$ccgroup=$cc1['gname'];
	$ccgroupupgrade=$cc1['g_upgrade'];
	$cc2=$dsql->GetOne("select * from sea_member where id=$ccuid");
	$ccjifen=$cc2['points'];
	$ccemail=$cc2['email'];
	$cclog=$cc2['logincount'];
	echo <<<EOT
	        
<body>
    <div class="row" style="margin-top: 10px;"></div>       
	    <div class="row">
        <div class="u-menu">
            <ul class="u-nav" id="user_menu">
                <li class="item" id="user_menu_my" name="user_menu_my">
                    <h3 class="t1">会员中心</h3>
                    <ul class="sub">
                        <li><a class="current" href="?action=cc"   >基本资料</a></li>
	        <li><a href="?action=favorite"  >我的收藏</a></li>
			<li><a href="?action=buy"  >购买记录</a></li>
			<li><a href="exit.php">退出账户</a></li>
                </li>
            </ul>
        </div>
        <div class="u-main">
            <div id="tab_menu">
                <ul class="u-tab clearfix">
                    <li class="current"><a>个人资料</a></li>
                    <li><a>修改密码</a></li>
                </ul>
            </div>
            <div id="tab_box">
                <div class="u-form-wrap" style="display: block; ">                 
						<div class="info-main">
                            <div class="row">
                                <label style="color:#aaa">
                                    您的序号：</label>{$_SESSION['sea_user_id']}</div>
                            <div class="row">
                                <label style="color:#aaa">
                                    您的账户：</label>{$_SESSION['sea_user_name']}</div>
                            <div class="row"><label style="color:#aaa">您的邮箱：</label>{$ccemail}</div>
							<div class="row"><label style="color:#aaa">登陆次数：</label>{$cclog}</div>
                            <div class="row">
EOT;
                            echo  "<label style=\"color:#aaa\">".
								  "用户级别：</label><span class=\"orange\">{$ccgroup}</span>";
							$sql="select * from sea_member_group where g_upgrade > $ccgroupupgrade";
							$dsql->SetQuery($sql);
							$dsql->Execute('al');
							while($rowr=$dsql->GetObject('al'))
							{
								echo "<input type=\"submit\" style=\"background:#B17354;width: 100px; height: 25px;border:0;color:#fff;font-size:14px;margin:0 0 0 10px;\" value='".升级."".$rowr->gname."' onClick=\"self.location='?action=hyz&gid=".$rowr->gid."';\"></span>";
							}
								echo "<div class=\"row\">".
                                 "<label style=\"color:#aaa\">".
                                    "当前积分：</label>{$ccjifen}</div><div class=\"row\">".
                                 "<label style=\"color:#aaa\">".
                                    "推广链接：</label>http://{$_SERVER['HTTP_HOST']}/i.php?uid={$_SESSION['sea_user_id']}</div>".
								"<div class=\"row\">".
                                "<form action=\"?action=cz\" method=\"post\"><label style=\"color:#aaa\">充值积分：</label><input type=text style=\"width: 300px; height: 25px;\" name=cckkey id=cckkey> <input type=submit name=cckb id=cckb style=\"background:#f90;width: 50px; height: 29px;border:0;color:#fff;font-size:14px;margin:0 0 0 10px;\" value='提交' ></form></div>";
	echo <<<EOT
                        	</div>
                        	<div class="clear"></div>
                		</div>
            		</div>
                <div class="u-form-wrap" style="display: none;">                    
<div class="info-main">
                            <div class="row">
EOT;
$row1=$dsql->GetOne("select * from sea_member where id='$uid'");
	$oldpwd=$row1['password'];
	$oldemail=$row1['email'];
							echo "<form id=\"f_Activation\"   action=\"?action=chgpwdsubmit\" method=\"post\">".
                                "<label style=\"color:#aaa\">".
                                    "输旧密码：</label><input class=\"i-inp\" type=\"password\" name=\"oldpwd\" value=\"$oldpwd\" style=\"width: 200px; height: 25px;\" /><span style=\"color:red;padding-left:5px;\">*</span>".
								"</div>".
                            "<div class=\"row\">".
                                "<label style=\"color:#aaa\">".
                                   "输新密码：</label><input class=\"i-inp\" type=\"password\" name=\"newpwd\" style=\"width: 200px; height: 25px;\" /></div>".
                            "<div class=\"row\">".
                                "<label style=\"color:#aaa\">".
                                    "再次确认：</label><input class=\"i-inp\" type=\"password\" name=\"newpwd2\" style=\"width:200px; height: 25px;\" /></div>".
									                            "<div class=\"row\">".
                                "<label style=\"color:#aaa\">".
                                    "邮箱地址：</label><input class=\"i-inp\" type=\"test\" name=\"email\" value=\"$oldemail\" style=\"width: 200px; height: 25px;\" /></div>".
									"<div class=\"row\">".
									"<input type=\"submit\" name=\"gaimi\" style=\"background:#f90;width: 80px; height: 30px;border:0;color:#fff;font-size:14px;margin:10px 0 0 80px;\" value=\"确认修改\">".
                                    "</div>".
						"</form>".
                        "</div>";
						echo <<<EOT
                </div>                
            </div>
        </div>

    </div>
</body>
EOT;
}
elseif($action=='favorite')
{
	$page = $_GET["page"]; 
	$pcount = 20;
	$row=$dsql->getOne("select count(id) as dd from sea_favorite where uid=".$uid);
	$rcount=$row['dd'];
	$page_count = ceil($rcount/$pcount); 
	if(empty($_GET['page'])||$_GET['page']<0){ 
	$page=1; 
	}else { 
	$page=$_GET['page']; 
	}  
	$select_limit = $pcount; 
	$select_from = ($page - 1) * $pcount.','; 
	$pre_page = ($page == 1)? 1 : $page - 1; 
	$next_page= ($page == $page_count)? $page_count : $page + 1 ; 	
	$dsql->setQuery("select * from sea_favorite where uid=".$uid." limit ".($page-1)*$pcount.",$pcount");
	$dsql->Execute('favlist');
	echo <<<EOT
	<body>
	<div class="row" style="margin-top: 10px;">
    </div>
    <div class="row">
        <div class="u-menu">
            <ul class="u-nav" id="user_menu">
                <li class="item" id="user_menu_my" name="user_menu_my">
                    <h3 class="t1">会员中心</h3>
                    <ul class="sub">
                        <li><a href="?action=cc"   >基本资料</a></li>
	        <li><a class="current" href="?action=favorite"  >我的收藏</a></li>
			<li><a href="?action=buy"  >购买记录</a></li>
			<li><a href="exit.php">退出账户</a></li>
						</ul>
                </li>
            </ul>
        </div>
        <div class="u-main">
            <div id="tab_menu">
                <ul class="u-tab clearfix">
                    <li class="current"><a>收藏记录</a></li>
                </ul>
            </div>
            <div id="tab_box">
                <div class="u-form-wrap">                 
                <div class="m-sub-til" style="padding: 0;">
                    共{$rcount}个视频，每页显示{$pcount}条
</div>
                <table class="u-table">
                    <tr>
                        <th width="30%">
                            视频
                        </th>
                        <th width="20%">
                            收藏时间
                        </th>
                        <th width="11%">
                            播放数
                        </th>
                        <th width="11%">
                            连载集数
                        </th>
                        <th width="12%">
                            状态
                        </th>
                        <th width="12%">
                            操作
                        </th>

                    </tr>
EOT;
while($row=$dsql->getArray('favlist'))
{
	$rs=$dsql->getOne("select v_hit,v_state,v_pic,v_name,v_enname,v_note,v_addtime,tid from sea_data where v_id=".$row['vid']);
	if(!$rs) {echo "<tr><td colspan=\"5\">该视频不存在</td></tr>";continue;}
	$hit=$rs['v_hit'];
	$pic=$rs['v_pic'];
	$name=$rs['v_name'];
	$state=$rs['v_state'];
	$note=$rs['v_note'];

echo <<<EOT
                    <tr>
						<td>
						<a href="
EOT;
						echo getContentLink($rs['tid'],$row['vid'],"",date('Y-n',$rs['v_addtime']),$rs['v_enname']);
						echo <<<EOT
						" target="_blank" >
EOT;
						echo $name;
						echo <<<EOT
						</a>
						</td>					
                        <td>
EOT;
						echo date('Y-m-d',$row['kptime']);
						echo <<<EOT
						</td>
						<td>{$hit}</td>
                        <td>{$state}</td>
                        <td>{$note}</td>			
                        <td>
						<a onClick="return(confirm('确定取消收藏该影片？'))" href="?action=cancelfav&id=
EOT;
						echo $row['id'];
echo <<<EOT
						">取消收藏</a>				
						</td>
                    </tr>
EOT;
					  }			
 echo <<<EOT
 </table>
 第 $page/$page_count 页
                <a href="?action=favorite&page=1" class="btn">首页</a> 
				<a href="?action=favorite&page={$pre_page}">上一页</a> 
                <a href="?action=favorite&page={$next_page}">下一页</a>
				<a href="?action=favorite&page={$page_count}">尾页</a>
                        <div class="clear">
                        </div>
                </div>
            </div>
        </div>
</body>
EOT;
?>
<script src="js/common.js" type="text/javascript"></script>
<script>
function submitForm()
{
	$('favform').submit()
}
function showpic(event,imgsrc){	
	var left = event.clientX+document.documentElement.scrollLeft+20;
	var top = event.clientY+document.documentElement.scrollTop+20;
	$("preview").style.display="";
	$("preview").style.left=left+"px";
	$("preview").style.top=top+"px";
	$("pic_a1").setAttribute('src',imgsrc);
}
function hiddenpic(){
	$("preview").style.display="none";
}
</script>
<?php
}
elseif($action=='buy')
{
	$page = $_GET["page"];
	$pcount = 20;
	$row=$dsql->getOne("select count(id) as dd from sea_buy where uid=".$uid);
	$rcount=$row['dd'];	
	$page_count = ceil($rcount/$pcount); 
	if(empty($_GET['page'])||$_GET['page']<0){ 
	$page=1; 
	}else { 
	$page=$_GET['page']; 
	}
	$select_limit = $pcount; 
	$select_from = ($page - 1) * $pcount.','; 
	$pre_page = ($page == 1)? 1 : $page - 1; 
	$next_page= ($page == $page_count)? $page_count : $page + 1 ; 
	$dsql->setQuery("select * from sea_buy where uid=".$uid." limit ".($page-1)*$pcount.",$pcount");
	$dsql->Execute('buylist');
	echo <<<EOT
	<body>
    <div class="row" style="margin-top: 10px;">
    </div>
    <div class="row">
        <div class="u-menu">
            <ul class="u-nav" id="user_menu">
                <li class="item" id="user_menu_my" name="user_menu_my">
                    <h3 class="t1">会员中心</h3>
                    <ul class="sub">
                        <li><a href="?action=cc"   >基本资料</a></li>
	        <li><a href="?action=favorite"  >我的收藏</a></li>
			<li><a class="current" href="?action=buy"  >购买记录</a></li>
			<li><a href="exit.php">退出账户</a></li>
						</ul>
                </li>
            </ul>
        </div>
        <div class="u-main">
            <div id="tab_menu">
                <ul class="u-tab clearfix">
                    <li class="current"><a>购买记录</a></li>
                </ul>
            </div>
            <div id="tab_box">
                <div class="u-form-wrap">                 
                <div class="m-sub-til" style="padding: 0;">
                    共{$rcount}个视频，每页显示{$pcount}条
</div>
                <table class="u-table">
                    <tr>
                        <th width="16%">
                            视频
                        </th>
                        <th width="20%">
                            购买时间
                        </th>
                        <th width="11%">
                            播放数
                        </th>
                        <th width="11%">
                            连载集数
                        </th>
                        <th width="12%">
                            状态
                        </th>
                        <th width="12%">
                            操作
                        </th>

                    </tr>
EOT;
	while($row=$dsql->getArray('buylist'))
{
	$rs=$dsql->getOne("select v_hit,v_state,v_pic,v_name,v_enname,v_note,v_addtime,tid from sea_data where v_id=".$row['vid']);
	if(!$rs) {echo "<tr><td align=\"left\"><input type=\"checkbox\"></td><td colspan=\"5\">该视频不存在或已经删除</td></tr>";continue;}
	$hit=$rs['v_hit'];
	$pic=$rs['v_pic'];
	$name=$rs['v_name'];
	$state=$rs['v_state'];
	$note=$rs['v_note'];
	echo <<<EOT
                    <tr>
                        <td>
						<a href="
EOT;
						echo getContentLink($rs['tid'],$row['vid'],"",date('Y-n',$rs['v_addtime']),$rs['v_enname']);
						echo <<<EOT
						" target="_blank">
EOT;
						echo $name;
						echo <<<EOT
						</a>
                        </td>
                        <td>
EOT;
                            echo date('Y-m-d',$row['kptime']);
							echo <<<EOT
                        </td>
                        <td>
						{$hit}
                        </td>
                        <td>
						{$state}
                        </td>
                        <td>
						{$note}
                        </td>
                        <td>
						已购买
                        </td>
                    </tr>
EOT;
					}
					echo <<<EOT
                </table>
				 第 $page/$page_count 页

                <a href="?action=buy&page=1" class="btn">首页</a> 
				<a href="?action=buy&page={$pre_page}">上一页</a> 
                <a href="?action=buy&page={$next_page}">下一页</a>
				<a href="?action=buy&page={$page_count}">尾页</a>
                        <div class="clear">
                        </div>
                </div>
            </div>
        </div>
    </div>
</body>
EOT;
?>
<script src="js/common.js" type="text/javascript"></script>
<script>
function submitForm()
{
	$('favform').submit()
}
function showpic(event,imgsrc){	
	var left = event.clientX+document.documentElement.scrollLeft+20;
	var top = event.clientY+document.documentElement.scrollTop+20;
	$("preview").style.display="";
	$("preview").style.left=left+"px";
	$("preview").style.top=top+"px";
	$("pic_a1").setAttribute('src',imgsrc);
}
function hiddenpic(){
	$("preview").style.display="none";
}
</script>
<?php
}
else
{
	
}
?>