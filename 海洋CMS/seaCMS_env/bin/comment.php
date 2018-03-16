<?php
session_start();
require_once("include/common.php");
require_once(sea_INC.'/main.class.php');


AjaxHead();
header('Content-Type:text/html;charset=UTF-8');
if($cfg_gbookstart=='0'){
echo '对不起，评论暂时关闭';
exit();
}
$itype=$type;
$itype=is_numeric($itype)?$itype:0;
if(!isset($action))
{
	$action = '';
}
$ischeck = $cfg_feedbackcheck=='Y' ? 0 : 1;
$id = $_REQUEST['id'];
$id = (isset($id) && is_numeric($id)) ? $id : 0;
$page=empty($page) ? 1 : intval($page);
if($page==0) $page=1;
if(empty($id))
{
	echo "err";
	exit();
}
?>

<iframe id="parentframe" width="100%" frameborder="0" scrolling="no" src="/<?php echo $GLOBALS['cfg_cmspath']; ?>comment/comment.html?id=<?php echo $id?>&type=<?php echo $itype?>&iscaptcha=<?php echo $GLOBALS['cfg_feedback_ck']; ?>&islogin=<?php echo (!empty($_SESSION['sea_user_auth'])?1:0) ;?>&title=" marginheight="0" marginwidth="0" name="comment" style="height:auto"></iframe>
