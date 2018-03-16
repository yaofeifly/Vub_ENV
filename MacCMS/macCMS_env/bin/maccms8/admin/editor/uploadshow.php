<?php
require(dirname(__FILE__) .'/../admin_conn.php');
chkLogin();
$action=be("get","action");
$id=be("get","id");
$path=be("get","path");
?>
<link rel="stylesheet" type="text/css" href="../tpl/images/style.css" />
<script>
function checkForm(){
	var f = document.getElementsByName("file1")[0].value;
	if(f==''){ return false; } else { return true; }
}
</script>
<body style="margin:0px;padding:0px;text-align:left">
<form name="form" enctype="multipart/form-data" action="upload.php?action=<?php echo $action?>&id=<?php echo $id?>&path=<?php echo $path?>" method="post" onSubmit="return checkForm()">
<input type="file" id="file1" name="file1" style="width: 200px; height: 25px;">
<input type="submit" name="submit" class="input" value="上传">
</form>