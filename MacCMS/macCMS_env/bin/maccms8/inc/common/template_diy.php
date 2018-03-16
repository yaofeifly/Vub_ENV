<?php
//统计二次开发接口 $tpl->H 可直接访问经过系统内置标签系统解析的html代码
function replaceTplCustom()
{
	global $db,$tpl;//载入数据库操作对象、模版对象
	
	//测试函数1
	//test1();
}

function test1()
{
	global $db,$tpl;//载入数据库操作对象、模版对象
	
	$s = '{custom:test}';
	$tpl->H = str_replace($s,'测试自定函数',$tpl->H);
}

?>