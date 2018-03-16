<?php
require_once("include/common.php");
$name=$_REQUEST['name'];
$url=$_REQUEST['url'];
$name = FilterSearch(stripslashes($name));
$Shortcut = "[InternetShortcut]

URL={$url}

";
header("Content-type: application/octet-stream"); 
header("Content-Disposition: attachment; filename=".str_replace(" ","",$name).".url;"); 
echo $Shortcut;
?>