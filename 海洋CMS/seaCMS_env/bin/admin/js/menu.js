$(document).ready(function(e){
		
	$("#line").click(function(){
		if ($("#left").is(":hidden")) {
			$("#left").css("display","")
			$("#line").css("background","url(img/arrow.gif)  center no-repeat")
			$("#td_left").css("display","")
			} 
		else{
			 $("#left").css("display","none")
			$("#line").css("background","url(img/arrow1.gif)  center no-repeat")
			 $("#td_left").css("display","none")
			}		
	});
	
	$("li[m=1]").click(function(){
		//$("#showme").css("display","none");
		//$("#showme2").css("display","none");
		var strMenu = $(this).attr("id") + "Menu";
		$("div[id$='Menu']").hide();
		//头部样式更改
		$("li[m=1]").removeClass("two");
		$("li[m=1]").addClass("one");
		var show=$(this).attr("id");
		$("#"+show).addClass("two");
		//左侧主菜单样式更改
		//左侧主菜单子菜单样式更改
		var listname=$("#"+strMenu).find("li").attr("id");
		$("li[m=2]").removeClass("list1");
		$("li[m=2]").addClass("list2");
		$("#"+listname).addClass("list1");
		$("#" + strMenu).show();
	});
	
	$("li[m=2]").mouseover(function(){
		$("li[m=2]").removeClass("list1");
		$("li[m=2]").addClass("list2");
		$("#"+$(this).attr("id")).addClass("list1");
	});	

	
	$("div[m=3]").click(function(){
		var divnameone=$(this).attr("id");
		var divnametwo=divnameone+"_1";
		$("#"+divnametwo).slideToggle("slow");
		//切换图片
		$("#"+divnameone).toggle(function(){
			$(this).removeClass("title2");
			$(this).removeClass("title1");
			$(this).addClass("title1");
		},function(){
			$(this).removeClass("title2");
			$(this).removeClass("title1");
			$(this).addClass("title2");
		});
		
	});
	$("#ShowMenu").click(function (){
		$("#ShowMenu").removeClass("one");
		$("#ShowMenu").addClass("two");
		
	});
	
});

function updatec(){
	   $("#reup").text("Loading....");							 
	   $.post("admin_ajax.php?action=updatecache",function(R){
		      if(R=="ok"){
				  $("#reup").text("，缓存更新成功！");
				  }								
			  else{
				  $("#reup").text("，缓存更新失败！");  
			  }
        });
	  
	  }