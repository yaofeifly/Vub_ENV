var playi=false;downi=false,arti=false,refreshi=false;

String.prototype.trim=function(){
        return this.replace(/(^[\s\u3000]*)|([\s\u3000]*$)/g, "");
}
String.prototype.ltrim=function(){
	return this.replace(/(^\s*)/g, "");
}
String.prototype.rtrim=function(){
	return this.replace(/(\s*$)/g, "");
}

function checkAll(objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = true;
	});
}

function checkOther(objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = !this.checked;
	});
}

function checkCount(objname){
	var res=0;
	$("input[name='"+objname+"']").each(function() {
		if(this.checked){ res++; }
	});
	return res;
}

function rndNum(under, over){
 switch(arguments.length){ 
 	case 1: return parseInt(Math.random()*under+1);
 	case 2: return parseInt(Math.random()*(over-under+1) + under);
 	default: return 0;
 }
}

function pagego($url,$total){
	$page=$('#page').val();
	if($page>0&&($page<=$total)){
		$url=$url.replace('{pg}',$page);
		location.href=$url;
	}
	return false;
}

function copyData(text){
	if (window.clipboardData){
		window.clipboardData.setData("Text",text);
	} 
	else{
		var flash_copy = null;
		if( !$('#flash_copy') ){
			var flash_copy = document.createElement("div");
	    	flash_copy.id = 'flash_copy';
	    	document.body.appendChild(flash_copy);
		}
		flash_copy = $('#flash_copy');
		flash_copy.innerHTML = '<embed src="../../images/_clipboard.swf" FlashVars="clipboard='+escape(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
	}
	alert("复制成功");
	return true;
}

function showpic(event,imgsrc)
{
	if(imgsrc.trim()==""){ return; }
	var left = event.clientX+document.body.scrollLeft+20;
	var top = event.clientY+document.body.scrollTop+20;
	$("#showpic").css({left:left,top:top,display:""});
	if(imgsrc.indexOf('://')<0){ imgsrc = "../"+imgsrc;	}
	$("#showpic_img").attr("src",imgsrc);
}
function hiddenpic()
{
	$("#showpic").css("display","none");
}
function updatecache(f,s)
{
	$("#msg_cache").text("Loading....");
	$.get("?m=admin-updatecache-flag-"+f+"-rnd-"+Math.random(),function(obj){
		if(obj !="" && obj !=undefined){
			$("#msg_cache").text(s+'缓存更新失败!');
		}
		else{
			$("#msg_cache").text(s+'缓存更新完毕!');
		}
	});
}

function appendplay(i,playStr,serverStr){
	playStr=unescape(playStr);
	serverStr=unescape(serverStr);
	if(playi==false){ playi=i; } else{ i=++playi; }
	var area="<tr><td>&nbsp;播放地址"+i+":</td><td><input id='playurlid"+i+"' name='playurlid[]' type='hidden' value='0'/>&nbsp;&nbsp;播放器：<select id='playfrom"+i+"' name='playfrom[]'><option value='no'>暂无数据</option>"+playStr+"</select>&nbsp;&nbsp;服务器组：<select id='playserver"+i+"' name='playserver[]'><option value='no'>暂无数据</option>"+serverStr+"</select>&nbsp;&nbsp;备注：<input id='playnote"+i+"' name='playnote[]' size='50' value=''>&nbsp;&nbsp;操作：&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"clearSect('playurl"+i+"')\">清空</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"removeSect(this)\">删除</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"upSect(this)\">上移</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"downSect(this)\">下移</a>&nbsp;&nbsp;<br><br><textarea id='playurl"+i+"' name='playurl[]' style='width:100%;height:150px;'></textarea></td></tr>"
	$("#playlist").append(area);
}

function appenddown(i,downStr,serverStr){
	downStr=unescape(downStr);
	serverStr=unescape(serverStr);
	if(downi==false){ downi=i; } else{ i=++downi; }
	var area="<tr><td>&nbsp;下载地址"+i+":</td><td><input id='downurlid"+i+"' name='downurlid[]' type='hidden' value='0'/>&nbsp;&nbsp;下载类型：<select id='downfrom"+i+"' name='downfrom[]'><option value='no'>暂无数据</option>"+downStr+"</select>&nbsp;&nbsp;服务器组：<select id='downserver"+i+"' name='downserver[]'><option value='no'>暂无数据</option>"+serverStr+"</select>&nbsp;&nbsp;备注：<input id='downnote"+i+"' name='downnote[]' size='50' value=''>&nbsp;&nbsp;操作：&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"clearSect('downurl"+i+"')\">清空</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"removeSect(this)\">删除</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"upSect(this)\">上移</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"downSect(this)\">下移</a>&nbsp;&nbsp;<br><br><textarea id='downurl"+i+"' name='downurl[]' style='width:100%;height:150px;'></textarea></td></tr>"
	$("#downlist").append(area);
}

function appendart(i){
	if(arti==false){ arti=i; } else{ i=++arti; }
	var area="<tr><td>&nbsp;分页内容"+i+":</td><td><span style='float:left;padding:3px;'>操作：&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"clearSect('a_content"+i+"')\">清空</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"removeSect(this)\">删除</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"upSect(this)\">上移</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"downSect(this)\">下移</a>&nbsp;&nbsp;<br></span> <textarea name='a_content[]' id='a_content"+i+"' class=\"xheditor {tools:'BtnBr,Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink,Img,Flash,Media,Table,Source,Fullscreen',width:'100%',height:'250',upBtnText:'上传',html5Upload:false,upMultiple:1,upLinkUrl:'{editorRoot}upload.php?action=xht&path=art',upImgUrl:'{editorRoot}upload.php?action=xht&path=art'}\"></textarea></td></tr>"
	$("#artlist").append(area);
	$('#a_content'+i).xheditor();
}

function clearSect(m){ $('#'+m).val(''); }
function removeSect(m){ $(m).parent().parent().remove(); }

function upSect(m){
	var current=$(m).parent().parent();
    var prev=current.prev();
    if(current.index()>0)
    {
        current.insertBefore(prev);
    }
}
function downSect(m){
	var current=$(m).parent().parent();
	var next=current.next();
    if(next)
    {
        current.insertAfter(next);
    }
}

function FindNote(s){
	var res="";
	if (s.indexOf("DVD")>0){
		res="DVD";
	}
	else if (s.indexOf("TS")>0 || s.indexOf("TC")>0 || s.indexOf("抢先版")>0) {
		res="抢先版";
	}
	else if (s.indexOf("HD")>0){
		res="HD";
	}
	else if (s.indexOf("BD")>0){
		res="BD";
	}
	else if (s.indexOf("蓝光高清")>0){
		res="蓝光高清";
	}
	else if (s.indexOf("高清")>0){
		res="高清";
	}
	else if (s.indexOf("VCD")>0){
		res="VCD";
	}
	
	if (s.indexOf("国粤语")>0){
		res +="国粤语";
	}
	else if (s.indexOf("国语")>0){
		res +="国语";
	}
	else if (s.indexOf("粤语")>0){
		res +="粤语";
	}
	else if (s.indexOf("台语")>0){
		res +="台语";
	}
	else if (s.indexOf("英语")>0){
		res +="英语";
	}
	else if (s.indexOf("中文字幕")>0){
		res +="中文字幕";
	}
	return res;
}

function getPatName(n,l,s){
	var res="";
	var rc=false;
	if(s.indexOf("qvod:")>-1 || s.indexOf("bdhd:")>-1 || s.indexOf("cool:")>-1){
		var arr = s.split('|');
		if(arr.length>=2){
			res = arr[2].replace(/[^0-9]/ig,"");
			rc=true;
			
			if(res!=""){
				if(res.length>3){
					res += "期";
				}
				else if(l==1){
					res = "全集";
				}
				else{
					res = '第' + res + '集';
				}
				
			}
			else{
				res = FindNote(s);
				if (s==""){
					if (l==1){
						res="全集";
					}
					else{
						rc=false;
					}
				}
			}
		}
	}
	if(!rc){
		res = '第' + (n<9 ? '0' : '') + (n+1) + '集';
	}
	return res;
}

function repairUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	s1 = s1.replaceAll("\r","");
	arr1 = s1.split("\n");
	arr1len = arr1.length;
	for(j=0;j<arr1len;j++){
		if(arr1[j].length>0){
			urlarr = arr1[j].split('$'); urlarrcount = urlarr.length-1;
			if(urlarrcount==0){
				arr1[j]= getPatName(j,arr1len,arr1[j]) + '$' + arr1[j];
			}
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}

function orderUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	s1 = s1.replaceAll("\r","");
	arr1=s1.split("\n");
	for(j=arr1.length-1;j>=0;j--){
		if(arr1[j].length>0){
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}

function delnameUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	s1 = s1.replaceAll("\r","");
	arr1=s1.split("\n");
	for(j=0;j<arr1.length;j++){
		if(arr1[j].length>0){
			urlarr = arr1[j].split('$'); urlarrcount = urlarr.length-1;
			if(urlarrcount==0){
				arr1[j] = arr1[j];
			}
			else{
				arr1[j] = urlarr[1];
			}
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}

function creatediv(z,w,h)
{
	if( $("#confirm").get(0) ==undefined ){
		$('<div id="confirm"></div>')
		.css('position','absolute')
		.css('z-index',z+1)
		.css('top','200px')
		.css('left','300px')
		.css('border','1px solid #55BBFF')
		.css('background','#C1E7FF')
		.css('padding',' 3px 0px 3px 4px')
		.appendTo("body");
	}
}

function closew(){ $("#confirm").remove(); }


function ajaxshow(trigger,ac,tab,colid,col,id)
{
	var ids = "";
	if(id==''){
		$("input[name='"+colid+"[]']").each(function() {
			if(this.checked){ ids =  ids + this.value + ","; }
		});
		ids = ids.substring(0,ids.length-1);
	}
	else{
		ids=id;
	}
	if (ids!=''){
		var topicName=$("#"+trigger);
		var offset=topicName.offset();
		var topicTop=offset.top;
		var topicLeft=offset.left;
	    creatediv(99997,250,20);
		var sdiv=$("#confirm");
		sdiv.css('top',topicTop-4+'px').css('left',topicLeft-100+'px').html('正在加载内容......');
		sdiv.load("admin_data.php?ac="+ac+"&id="+ids+"&show=1&tab="+tab+'&colid='+colid+'&col='+col+'&rnd='+Math.random() );
	}
	else{
		alert("请至少选择一条数据!");
	}
}

function ajaxsubmit(ac,tab,colid,col,id)
{
	var par="ac="+ac+"&tab="+tab+'&colid='+colid+'&col='+col+"&id="+id+"&show=2"+"&val="+$("#val").val()+"&val2="+$("#val2").val();
	$.get("admin_data.php",par, function(obj) {
		oncomplete(obj);
	});
}

function oncomplete(r)
{
	if(r=="reload"){
		location=location;
	}
	else if(r.length>6){
		var arr = r.split('|||');
		for (i=0;i<arr.length;i++){
			if (arr[i] !=""){
				r=arr[i].split('$');
				$("#"+r[0]).html(r[1]);
			}
		}
	}
	closew();
}


function setunion(tab,val,bind)
{
	$("#u_type").val(bind);
	var offset=$("#type"+bind).offset();
	creatediv(99997,250,20);
	$("#confirm").css('top',offset.top-4+'px').css('left',offset.left-100+'px').html('正在加载内容......');
	$("#confirm").show();
	$.ajax({url: 'admin_data.php?ac=type_bind&show=1&tab='+tab+'&val='+val+'&bind='+bind,cache: false,
		success: function(res){
			$("#confirm").html(res);
		}
	});
}
function bindsave(tab,bind)
{
	var val = $("#confirm").find("#val").val();
	var text = '已绑定',color='red';
	if(val==""){ text='未绑定';color='';}
	$.ajax({url: 'admin_data.php?ac=type_bind&show=2&tab='+tab+'&val='+val+'&bind='+bind,cache: false,
		success: function(res){
		    $('#type'+bind).text("["+text+"]");
		   	$('#type'+bind).css("color",color);
		}
	});
	$("#confirm").remove();
}