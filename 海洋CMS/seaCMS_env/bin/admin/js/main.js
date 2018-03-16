var ajax = new AJAX();ajax.setcharset("utf-8");

 var getElementById = function(el){ 
    var id = el; 
    el = document.getElementById(el); 
    if(el.id === id) 
      return el; 
    else{ 
      var nodes = document.all[id]; 
      for(var i = 0,len = nodes.length;i < len;i ++) 
        if(nodes[i].id == id) 
          return nodes[i]; 
    } 
  } 
//document.getElementByIdNew = getElementById;


function openUpdateWin(width, height,str){
	openWindow2(101,width,height,50)
	var msgDiv=document.getElementById("msg")
	var bgDiv=document.getElementById("bg")
	var iWidth = document.documentElement.scrollWidth
	var str="<div style='width:400px;'><div class='divboxtitle'><span onclick=\"closeWin();set(document.getElementById('update'),'<font color=red >版本暂未升级...</font>');\" ><img src='../pic/btn_close.gif'/></span>seacms在线升级</div><div  class='divboxbody'>"+str+"<input type='button' value='进入升级页面' id='openwin' class='rb1'/>&nbsp;&nbsp;&nbsp;&nbsp;<input id='closewin' type='button' value='取   消' name='button' class='rb1'  /></div><div class='divboxbottom'>Power By seacms</div></div>"
	msgDiv.style.cssText += "FONT-SIZE: 12px;top:100px;left:"+(iWidth-width)/2+"px;text-align:center;";
	set(msgDiv,str)
	document.getElementById("closewin").onclick = function(){
		closeWin()
		set(document.getElementById("update"),"<font color='red'>版本暂未升级...</font>");
	}
	document.getElementById("openwin").onclick = function(){
		closeWin()
		location.href='admin_update.php';
	}
}

function openCollectWin(width, height,str,url){
	openWindow2(101,width,height,50)
	var msgDiv=document.getElementById("msg")
	var bgDiv=document.getElementById("bg")
	var iWidth = document.documentElement.scrollWidth
	var str="<div style='width:400px;'><div class='divboxtitle'><span onclick=\"closeWin();\" ><img src='../pic/btn_close.gif'/></span>seacms温馨提示</div><div  class='divboxbody'>"+str+"<br><input type='button' value='继续采集' id='openwin' class='btn'/>&nbsp;&nbsp;<input id='closewin' type='button' value='取   消' name='button' class='btn'  />&nbsp;&nbsp;<input id='clearColHis' type='button' value='取消记录' name='button' class='btn'  /></div><div class='divboxbottom'>Power By seacms</div></div>"
	msgDiv.style.cssText += "FONT-SIZE: 12px;top:100px;left:"+(iWidth-width)/2+"px;text-align:center;";
	set(msgDiv,str)
	document.getElementById("closewin").onclick = function(){
		closeWin()
	}
	document.getElementById("clearColHis").onclick = function(){
		clearColHis()
		closeWin()
	}
	document.getElementById("openwin").onclick = function(){
		closeWin()
		location.href=url;
	}
}

function clearColHis()
{
	ajax.get(
		"admin_ajax.php?action=clearColHis", 
		function(obj) {
		}
	);
}
 
function checkNewVersionCallBack(obj){
	 
	if (obj.responseText == "False"){
		set(document.getElementById("update"),"<font color='red'>&nbsp;&nbsp;当前已是最新版本!</font>");
	}else{
		openUpdateWin(400, "auto",obj.responseText)	
	}
}

function checkNewVersion(){
	set(document.getElementById("update"),"<font color='red'>&nbsp;&nbsp;请稍等，正在检测新版本...</font>");
	var getUrl = "admin_update.php?action=isNew";
	ajax.get(
		getUrl,
		checkNewVersionCallBack
	);	
}

function ajaxFunction(url){
	set(document.getElementById("wait"),"<font color='red'>目标网站正在采集中，请稍后.....</font>");
	location.href=url;
}




function checkRepeat(){
	ajax.get(
		"admin_ajax.php?action=checkrepeat&v_name="+encodeURI(document.getElementById('v_name').value),
		function(obj){
			if (obj.responseText == "ok"){set(document.getElementById("v_name_ok"),"<img src='img/yes.gif' border=0></img>");}else{set(document.getElementById("v_name_ok"),"<img src='img/no.gif' border=0></img>");}
		}
	);	
}

function setVideoTopic(videoId,topicId){
	openWindow2(101,230,20,0)
	var msgDiv=document.getElementById("msg")
	var topicTDObj = document.getElementById("topic"+videoId);
	var topicTDTop = topicTDObj.offsetTop;
    var topicTDLeft = topicTDObj.offsetLeft; 
    while (topicTDObj = topicTDObj.offsetParent){topicTDTop+=topicTDObj.offsetTop; topicTDLeft+=topicTDObj.offsetLeft;}
    msgDiv.style.cssText+="border:1px solid #55BBFF;background: #C1E7FF;padding:3px 0px 3px 4px;"
	msgDiv.style.top = (topicTDTop-1)+"px";
    msgDiv.style.left = (topicTDLeft-1)+"px"; 
	msgDiv.innerHTML="正在加载内容....";	
	ajax.get(
		"admin_ajax.php?id="+videoId+"&action=select&topicid="+topicId, 
		function(obj) {
			msgDiv.innerHTML=obj.responseText;
		}
	);
}

function setVideoState(videoId){
	openWindow2(101,250,20,0)
	var msgDiv=document.getElementById("msg")
	var topicTDObj = document.getElementById("state"+videoId);
	var topicTDTop = topicTDObj.offsetTop;
    var topicTDLeft = topicTDObj.offsetLeft; 
    while (topicTDObj = topicTDObj.offsetParent){topicTDTop+=topicTDObj.offsetTop; topicTDLeft+=topicTDObj.offsetLeft;}
    msgDiv.style.cssText+="border:1px solid #55BBFF;background: #C1E7FF;padding:3px 0px 3px 4px;"
	msgDiv.style.top = (topicTDTop-1)+"px";
    msgDiv.style.left = (topicTDLeft-1)+"px"; 
	msgDiv.innerHTML="状态：连载到第<input type='text' size='5' id='series' name='series'>集<input type='button' value='确定' onclick='submitVideoState("+videoId+")' class='rb1' /><input type='button' value='取消' onclick='closeWin()' class='rb1' />";	
}

function submitVideoTopic(videoId){
	var topic = document.getElementById("topicselect").value
	if (topic.length==0) {
		alert('请选择专题')
		return false
	}
	ajax.get(
		"admin_ajax.php?id="+videoId+"&topic="+topic+"&action=submittopic", 
		function(obj) {
			if(obj.responseText == "submitok"){
				set(document.getElementById("topic"+videoId),"<font color='red'>"+document.getElementById("topicselect").options[document.getElementById("topicselect").selectedIndex].text+"</font>");
				closeWin();
			}else{
				set(document.getElementById("topic"+videoId),"<font color='red'>发生错误</font>");		
			}
		}
	);
}

function submitVideoState(videoId){
	var state = document.getElementById("series").value
	if (isNaN(state)) {
		alert('集数为数字')
		return false
	}else if(state == 'undefined' || state == ''){
		alert('集数不能为空')
		return false
	}
	ajax.get(
		"admin_ajax.php?id="+videoId+"&state="+state+"&action=submitstate", 
		function(obj) {
			if(obj.responseText == "submitok"){
				set(document.getElementById("state"+videoId),"<font color='red'>(连载到"+state+"集)</font>");
				closeWin();
			}else{
				set(document.getElementById("state"+videoId),"<font color='red'>发生错误</font>");		
			}
		}
	);
}

function selectPicLink(selectObj,str){
	var selectValue=selectObj.options[selectObj.selectedIndex].value
	if 	(selectValue==str)
		document.getElementById("tr_v_pic").style.display=""
	else 
		document.getElementById("tr_v_pic").style.display="none"
}

function openAdWin(divid,path,url){
	document.getElementById(divid).style.display="block";
	selfLabelWindefault(divid);	
	document.getElementById("adpath").value='<script type=\"text/javascript\" language=\"javascript" src=\"'+url+path.replace("../","")+'\"></script>';	
}

function openHtmlToJsWin(divid){
	document.getElementById(divid).style.display="block";
	selfLabelWindefault(divid);	
}

function insertHtmlToJsWin(divid,divid2,divid3){
	hide(divid);
	document.getElementById(divid2).value=document.getElementById(divid3).value
}

function openSelfLabelWin(divid,id){
	document.getElementById(divid).style.display="block";
	selfLabelWindefault(divid);	
	set(document.getElementById("labelcontent"),"代码加载中...");	
	ajax.get(
		"admin_ajax.php?id="+id+"&action=getselflabel", 
		function(obj) {
			if(obj.responseText == "err"){
				set(document.getElementById("labelcontent"),"发生错误");	
			}else{
				set(document.getElementById("labelcontent"),obj.responseText);
			}
		}
	);
}

function selfLabelWindefault(divid){	
	document.getElementById(divid).style.left=(document.documentElement.clientWidth-568)/2+"px"
	document.getElementById(divid).style.top=(getScroll()+60)+"px"
}

function viewCurrentAdTr(id){
	var adtrObj=getElementsByName("tr","adtr")
	var n=adtrObj.length
	for (var i=0;i<=n-1;i++){
		adtrObj[i].className="";
	}
	document.getElementById("adtr"+id).className="editlast";
}

function isExistUsername(id){
	var username=document.getElementById("username").value
	if (username.length == 0){
		set(document.getElementById("checkmanagername"),"管理员名称不能为空");
		return false; 
	}
	ajax.get(
		"admin_ajax.php?username="+username+"&action=checkuser&id="+id, 
		function(obj) {
			var value = obj.responseText
			if(value == "no"){
				set(document.getElementById("checkmanagername"),"发生错误");	
			}else{
				if (value == "1")
					set(document.getElementById("checkmanagername"),"已经存在此管理员，请更换名称");	
				else if (value == "0")
					set(document.getElementById("checkmanagername"),"恭喜，该用户名可用");	
			}
		}
	);
}

function  starView(level,vid,type){
	var i,j,htmlStr;
	var htmlStr=""
	if (level==0){level=0}
	if (level>0){htmlStr+="<img src='img/starno.gif' border='0' style='cursor:pointer;margin-left:2px;' title='取消推荐'  onclick='commendVideo("+vid+",0,"+type+")'/>"}
	for (i=1;i<=level;i++){
		htmlStr+= "<img src='img/star0.gif' border='0' style='cursor:pointer;margin-left:2px;' onclick='commendVideo("+vid+","+i+","+type+")' title='推荐为"+i+"星级' id='star"+vid+"_"+i+"'  />"
	}
	for(j=level+1;j<=5;j++){
		htmlStr+= "<img src='img/star1.gif' border='0' style='cursor:pointer;margin-left:2px;' onclick='commendVideo("+vid+","+j+","+type+")' title='推荐为"+j+"星级' id='star"+vid+"_"+j+"' />"
	}
	set(document.getElementById('star'+vid),htmlStr)
}

function commendVideo(vid,commendid,type){

	ajax.get(
		"admin_ajax.php?id="+vid+"&commendid="+commendid+"&type="+type+"&action=commend", 
		function(obj){
			if(obj.responseText == "submitok"){
				starView(commendid,vid,type);
			}else{
				set(document.getElementById("star"+vid),"<font color='red'>发生错误</font>");		
			}
		}
	);
}

function viewCurrentTopicTr(id){
	var topictrObj=getElementsByName("tr","topictr")
	var n=topictrObj.length
	for (var i=0;i<=n-1;i++){
		topictrObj[i].style.background="#ffffff";	
	}
	document.getElementById("topictr"+id).style.background="#E7E7E7";
}

function openTopicDesWin(divid,id){
	selfLabelWindefault(divid);	
	view(divid)
	document.getElementById("f_id").value=id
	set(document.getElementById("f_des"),"加载中...");	
	ajax.get(
		"admin_ajax.php?id="+id+"&action=gettopicdes", 
		function(obj) {
			if(obj.responseText == "err"){
				set(document.getElementById("f_des"),"发生错误");	
			}else{
				set(document.getElementById("f_des"),obj.responseText);
			}
		}
	);
}

function submitTopicDes(divid){
	ajax.postf(
		document.getElementById("formdes"),
		function(obj){if(obj.responseText=="ok"){hide(divid);alert('修改成功');}else{alert('发生错误');}}
	);
}

function gather(){
	var url=document.getElementById("gatherurl").value
	if(url.length == 0) 
		return false
	else{
		view("loading")
		ajax.get(
			"admin_webgather.php?url="+url+"&action=gather", 
			function(obj) {
				if(obj.responseText == "err"){
					document.getElementById("gathercontent").value = "发生错误";	
				}else{
					document.getElementById("gathercontent").value = obj.responseText;
				}
				hide("loading")
			}
		);
	}
}

function insertResult(i){
	var id=document.getElementById("areaid").value
	document.getElementById("v_playurl"+id).value += (document.getElementById("v_playurl"+id).value!='' ? "\n" : '')+document.getElementById("gathercontent").value
	document.getElementById("gatherurl").value=''
	document.getElementById("gathercontent").value=''
	document.getElementById("areaid").value=i;
	hide('gathervideo')
}

function loadXML(xmlFile)
{
	var xmlDoc;
    try //Internet Explorer
      {
        xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        xmlDoc.async = false;
        xmlDoc.load(xmlFile);
    }
    catch (e) {
        try //Firefox, Mozilla, Opera
           {
            xmlDoc = document.implementation.createDocument("", "", null);
            xmlDoc.async = false;
            xmlDoc.load(xmlFile);
        }
        catch (e) {
            try //Google Chrome
              {
                var xmlhttp = new window.XMLHttpRequest();
                xmlhttp.open("GET", xmlFile, false);
                xmlhttp.send(null);
                xmlDoc = xmlhttp.responseXML;
            }
            catch (e) {
                error = e.message;
            }
        }
    }
	return xmlDoc;
	/*var xmlDoc;
	if(window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
		xmlDoc.async = false;
		xmlDoc.load(xmlFile);
	}
	else if (navigator.userAgent.indexOf("Firefox") > 0)
	{
		xmlDoc = document.implementation.createDocument('', '', null);
		xmlDoc.async = false;
		xmlDoc.load(xmlFile);
	}
	else
	{
		return null;
	}
	return xmlDoc;*/
}




function getReferedId(str){
	var  xml  =  loadXML("../data/admin/playerKinds.xml");
 	var dogNodes = xml.getElementsByTagName("player");
	for (var i = 0; i < dogNodes.length; i++)
    {
		var _postfix = dogNodes[i].attributes[2].value;
		var _flag = dogNodes[i].attributes[3].value;
		if(str.indexOf(_flag)>-1) return _postfix;
    }

/*	if(str.indexOf('新浪高清')>-1) return "hd_iask"
	if(str.indexOf('搜狐高清')>-1) return "hd_sohu"
	if(str.indexOf('天线高清')>-1) return "hd_openv"
	if(str.indexOf('56高清')>-1) return "hd_56"
	if(str.indexOf('56')>-1) return "56"
	if(str.indexOf('优酷')>-1) return "youku"
	if(str.indexOf('土豆')>-1) return "tudou"
	if(str.indexOf('搜狐')>-1) return "sohu"
	if(str.indexOf('新浪')>-1) return "iask"
	if(str.indexOf('六间房')>-1) return "6rooms"
	if(str.indexOf('qq')>-1) return "qq"
	if(str.indexOf('youtube')>-1) return "youtube"
	if(str.indexOf('17173')>-1) return "17173"
	if(str.indexOf('ku6视频')>-1) return "ku6"
	if(str.indexOf('FLV')>-1) return "flv"
	if(str.indexOf('SWF')>-1) return "swf"
	if(str.indexOf('real')>-1) return "real"
	if(str.indexOf('media')>-1) return "media"
	if(str.indexOf('qvod')>-1) return "qvod"
	if(str.indexOf('ppstream')>-1) return "pps"
	if(str.indexOf('迅播高清')>-1) return "gvod"
	if(str.indexOf('远古高清')>-1) return "wp2008"
	if(str.indexOf('播客CC')>-1) return "cc"
	if(str.indexOf('ppvod高清')>-1) return "ppvod"
	if(str.indexOf('PVOD')>-1) return "pvod"
	if(str.indexOf('海洋影音')>-1) return "ssea"
*/	return ""
}

function repairUrl(i){
	var urlStr,urlArray,newStr,j,flagCount,fromText
	fromText=$("v_playfrom"+i).options[$("v_playfrom"+i).selectedIndex].value
	if (fromText.length==0){alert('请选择播放器类型');return false;}
	urlStr=$('v_playurl'+i).value
	if (urlStr.length==0){alert('请填写地址');return false;}
	if(navigator.userAgent.indexOf("Chrome")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Firefox")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Safari")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Opera")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("rv:11")>0){urlArray=urlStr.split("\n");}
	else{urlArray=urlStr.split("\r\n");}
	newStr="";
	for(j=0;j<urlArray.length;j++){
		if(urlArray[j].length>0){
			flagCount=urlArray[j].split('$').length-1
			switch(flagCount){
				case 0:
					urlArray[j]='第'+(j+1)+'集$'+urlArray[j]+'$'+getReferedId(fromText)
					break;
				case 1:
					urlArray[j]=urlArray[j]+'$'+getReferedId(fromText)
					break;
				case 2:
					break;
			}
			newStr+=urlArray[j]+"\r\n";
		}
	}
	$('v_playurl'+i).value=trimOuterStr(newStr,"\r\n");
}


function repairUrl2(i){
	var urlStr,urlArray,newStr,j,flagCount,fromText
	fromText=$("m_downfrom"+i).options[$("m_downfrom"+i).selectedIndex].value
	if (fromText.length==0){alert('请选择下载类型');return false;}
	urlStr=$('m_downurl'+i).value
	if (urlStr.length==0){alert('请填写地址');return false;}
	if(navigator.userAgent.indexOf("Chrome")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Firefox")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Safari")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("Opera")>0){urlArray=urlStr.split("\n");}
	else if(navigator.userAgent.indexOf("rv:11")>0){urlArray=urlStr.split("\n");}
	else{urlArray=urlStr.split("\r\n");}
	newStr="";
	for(j=0;j<urlArray.length;j++){
		if(urlArray[j].length>0){
			flagCount=urlArray[j].split('$').length-1
			switch(flagCount){
				case 0:
					urlArray[j]='第'+(j+1)+'集$'+urlArray[j]+'$down'
					break;
				case 1:
					urlArray[j]=urlArray[j]+'$down'
					break;
				case 2:
					break;
			}
			newStr+=urlArray[j]+"\r\n";
		}
	}
	$('m_downurl'+i).value=trimOuterStr(newStr,"\r\n");
}
function expendPlayArea(i,optionStr,type){
	if(expendPlayArea.i===false){
		expendPlayArea.i=i
	}else{
		i=++expendPlayArea.i;
	}
	optionStr=unescape(optionStr)
	var n=i-1,m=i+1
	var sparkStr=(type==1)?"&nbsp;&nbsp;<font color='red'>＊</font>":""
	var sparkStr2=(type==1)?"<font color='blue'>数据地址单集格式： <font color='red'>标题$ID$来源</font>(如果多集就用行隔开)</font>":""
	var area="<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='playfb"+i+"'><tr><td height='30' width='70' class='td_border'>播放来源"+i+"：</td><td class='td_border'><select id='v_playfrom"+i+"' name='v_playfrom["+i+"]'><option value=''>暂无数据"+i+"</option>"+optionStr+"</select>&nbsp;&nbsp;<img onclick=\"var tb=document.getElementById('playfb"+i+"');tb.parentNode.removeChild(tb);\"  src='img/btn_dec.gif' class='pointer' alt='删除播放来源"+i+"' align='absmiddle' />&nbsp;&nbsp;<a href=\"javascript:moveTableUp(document.getElementById('playfb"+i+"'))\">上移</a>&nbsp;&nbsp;<a href=\"javascript:moveTableDown(document.getElementById('playfb"+i+"'))\">下移</a>"+sparkStr+sparkStr2+"</td></tr><tr><td  class='td_border'>数据地址"+i+"：<br/><input type='button' value='手动校正' title='一般情况下不需要手动校正，系统会自动进行校正' class='rb1'  onclick='repairUrl("+i+")'/></td><td align='left' class='td_border'><textarea id='v_playurl"+i+"' name='v_playurl["+i+"]' rows='8'  style='width:695px'></textarea>"+sparkStr+"</td></tr></table>"
	var _nextdiv=document.createElement("div");
	_nextdiv.innerHTML=area
	document.getElementById('v_playarea').appendChild(_nextdiv.getElementsByTagName('table')[0])
}
expendPlayArea.i=false;

function expendDownArea(i,optionStr,type){
	if(expendDownArea.i===false){
		expendDownArea.i=i
	}else{
		i=++expendDownArea.i;
	}
	optionStr=unescape(optionStr)
	var n=i-1,m=i+1
	var sparkStr=(type==1)?"&nbsp;&nbsp;<font color='blue' style='position:absolute;left: 350px; line-height:23px;white-space:nowrap' >地址单集格式：<font color='red'>下载名称$下载地址$down</font>(多集换行隔开)</font>":""
	var area="<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' id='downfb"+i+"'><tr><td  height='30' width='70' >下载来源"+i+"：</td><td class='td_border'>"+sparkStr+"<select id='m_downfrom"+i+"' name='m_downfrom["+i+"]'><option value=''>暂无数据"+i+"</option>"+optionStr+"</select>&nbsp;&nbsp;<img onclick=\"var tb=document.getElementById('downfb"+i+"');tb.parentNode.removeChild(tb);\"  src='img/btn_dec.gif' class='pointer' alt='删除下载来源"+i+"' align='absmiddle' />&nbsp;&nbsp;<a href=\"javascript:moveTableUp(document.getElementById('downfb"+i+"'))\">上移</a>&nbsp;&nbsp;<a href=\"javascript:moveTableDown(document.getElementById('downfb"+i+"'))\">下移</a></td></tr><tr><td  class='td_border'>下载地址"+i+"：<br/><input type='button' value='手动校正' title='一般情况下不需要手动校正，系统会自动进行校正' class='rb1'  onclick='repairUrl2("+i+")'/></td><td align='left' class='td_border'><textarea id='m_downurl"+i+"' name='m_downurl["+i+"]' rows='8' style='width:695px'></textarea></td></tr></table>";
	var _nextdiv=document.createElement("div");
	_nextdiv.innerHTML=area
	document.getElementById('m_downarea').appendChild(_nextdiv.getElementsByTagName('table')[0]);
	_nextdiv=null;
}
expendDownArea.i=false;


function expendPlayerSkin(i){
	var str="皮肤"+(i+1)+" :背景颜色 #<input type='text' name='playerbgcolor'  alt='clrDlg0"+i+"' /> 文字颜色 #<input type='text' name='playerfontcolor'  alt='clrDlg1"+i+"' /><br><div id='addplayerskin"+i+"'><img  src='img/btn_add.gif' onclick='expendPlayerSkin("+(i+1)+")'  style='cursor:pointer'/></div>";
	var _nextdiv=document.createElement("div")
	set(_nextdiv,str)
	document.getElementById('playerskindiv').appendChild(_nextdiv)
	if(i>0){hide("addplayerskin"+(i-1))}
}

function viewGatherWin(i){
	document.getElementById('gathervideo').style.display='block';document.getElementById("areaid").value=i;selfLabelWindefault('gathervideo');
}

function alertUpdatePic(){
	ajax.get(
		"admin_ajax.php?action=updatepic", 
		function(obj) {
			if(obj.responseText > 0){
				view('updatepic');set(document.getElementById("updatepicnum"),obj.responseText)
			}
		}
	);
	floatBtttom("updatepic",500);
	}

	
function floatBtttom(id,retime) {
	document.getElementById(id).style.top = (document.documentElement.scrollTop-0+document.documentElement.clientHeight-document.getElementById(id).clientHeight)+"px";
	timer = setTimeout("floatBtttom('"+id+"',"+retime+");",retime);
}

function isViewState(){
	if (document.getElementById("v_statebox").checked){document.getElementById("v_statespan").style.display="inline";}	else{hide("v_statespan");document.getElementById("v_state").value='';}
}

function isViewClass(){
	if (document.getElementById("v_classbox").checked){document.getElementById("v_classspan").style.display="inline";}	else{hide("v_classspan");}
}

function htmlToJs(htmlinput,jsinput){
	if(document.all)
	{
	document.getElementById(jsinput).value="document.writeln(\""+document.getElementById(htmlinput).value.replace(/\\/g,"\\\\").replace(/\//g,"\\/").replace(/\'/g,"\\\'").replace(/\"/g,"\\\"").split('\r\n').join("\");\ndocument.writeln(\"")+"\")";
	}
	else
	{
	document.getElementById(jsinput).value="document.writeln(\""+document.getElementById(htmlinput).value.replace(/\\/g,"\\\\").replace(/\//g,"\\/").replace(/\'/g,"\\\'").replace(/\"/g,"\\\"").split('\n').join("\");\ndocument.writeln(\"")+"\")";
	}
} 

function jstohtml(jsinput,htmlinput){
	document.getElementById(htmlinput).value=document.getElementById(jsinput).value.replace(/document.writeln\("/g,"").replace(/document.write\("/g,"").replace(/"\);/g,"").replace(/\\\"/g,"\"").replace(/\\\'/g,"\'").replace(/\\\//g,"\/").replace(/\\\\/g,"\\").replace(/document.writeln\('/g,"").replace(/"\)/g,"").replace(/'\);/g,"").replace(/'\)/g,"");
}

function trimOuterStr(str,outerstr){
	var len1
	len1=outerstr.length;
	if(str.substr(0,len1)==outerstr){str=str.substr(len1)}
	if(str.substr(str.length-len1)==outerstr){str=str.substr(0,str.length-len1)}
	return str
}

function reverseOrder(){
	if(document.getElementById('gathercontent').value==""){alert("没有地址内容");return;}
	if(navigator.userAgent.indexOf("Firefox")>0){var listArray=document.getElementById('gathercontent').value.split("\n");}else{var listArray=document.getElementById('gathercontent').value.split("\r\n");}
	var newStr="";
	for(var i=listArray.length-1;i>=0;i--){
			newStr+=listArray[i]+"\r\n";
	}
	document.getElementById('gathercontent').value=trimOuterStr(newStr,"\r\n");
}

function replaceStr(){
	var contentObj=document.getElementById('gathercontent'),str="gi";
	if(contentObj.value==""){alert("没有地址内容");return;}
	var replace1=document.getElementById("replace1").value,replace2=document.getElementById("replace2").value;
	var content=contentObj.value;
	var reg=new RegExp(replace1,str);
	contentObj.value=content.replace(reg,replace2);	
}

function viewComMakeOps(){hide('tr_makenews_all');hide('tr_makenews_type');hide('tr_makenews_content');view('tr_make_all');view('tr_make_type');view('tr_make_content');hide('tr_make_zt');hide('tr_make_self');document.getElementById("makeHtmlTab").getElementsByTagName("li")[0].className="hover";document.getElementById("makeHtmlTab").getElementsByTagName("li")[1].className="";document.getElementById("makeHtmlTab").getElementsByTagName("li")[2].className="";}

function viewNewsMakeOps(){view('tr_makenews_all');view('tr_makenews_type');view('tr_makenews_content');hide('tr_make_all');hide('tr_make_type');hide('tr_make_content');hide('tr_make_zt');hide('tr_make_self');document.getElementById("makeHtmlTab").getElementsByTagName("li")[1].className="hover";document.getElementById("makeHtmlTab").getElementsByTagName("li")[0].className="";document.getElementById("makeHtmlTab").getElementsByTagName("li")[2].className="";}

function viewSpeMakeOps(){hide('tr_make_all');hide('tr_make_type');hide('tr_make_content');hide('tr_makenews_all');hide('tr_makenews_type');hide('tr_makenews_content');view('tr_make_zt');view('tr_make_self');document.getElementById("makeHtmlTab").getElementsByTagName("li")[2].className="hover";document.getElementById("makeHtmlTab").getElementsByTagName("li")[0].className="";document.getElementById("makeHtmlTab").getElementsByTagName("li")[1].className="";}

function Nav(){
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
  else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
  else return "OT";
}

function SelTrim(selfield)
{
	var tagobj = document.getElementById(selfield);
	if(Nav()=='IE'){ var posLeft = window.event.clientX-200; var posTop = window.event.clientY; }
      else{ var posLeft = 100;var posTop = 100; }
	window.open("templets/admin_co_trimrule.html?"+selfield, "coRule", "scrollbars=no,resizable=yes,statebar=no,width=320,height=180,left="+posLeft+", top="+posTop);
}

function moveTableUp(o){
	if(!!o.previousSibling){
		o.parentNode.insertBefore(o,o.previousSibling);
	}
}
function moveTableDown(o){
	if(!!o.nextSibling){
		o.parentNode.insertBefore(o.nextSibling,o);
	}
}
function view(id){
	document.getElementById(id).style.display='';
}
function hide(id){
	document.getElementById(id).style.display='none';
}


function selecMakeMode(value){
	if (value=='dir1' || value=='dir3' || value=='dir5' || value=='dir7'|| value=='dir9'){
		view("dir1");
		hide("dir2");
	}
	if (value=='dir2' || value=='dir4' || value=='dir6' || value=='dir8'){
		hide("dir1");
		view("dir2");
	}
}

function selecRunMode(value){
	if (value=='1'){
		hide("static");view("dynamic");hide("forgedStatic");hide('ismakeplaytr');hide("dir1");hide("dir2");selectMakeplay();
	}
	if (value=='0'){
		view("static");hide("dynamic");hide("forgedStatic");view('ismakeplaytr');selecMakeMode(document.getElementById('makemode')[document.getElementById('makemode').selectedIndex].value);selectMakeplay();
	}
	if (value=='2'){
		hide("static");view("forgedStatic");hide("dynamic");hide('ismakeplaytr');hide("dir1");hide("dir2");selectMakeplay();
	}
}

function selecNewsMakeMode(value){
	if (value=='dir1' || value=='dir3' || value=='dir5' || value=='dir7'){
		hide("newsdir2");view("newsdir1");
	}
	if (value=='dir2' || value=='dir4' || value=='dir6' || value=='dir8'){
		view("newsdir2");hide("newsdir1");
	}
}

function selecNewsRunMode(value){
	if (value=='1'){
		hide("newsstatic");view("newsdynamic");hide("newsforgedStatic");hide("newsdir2");hide("newsdir1");
	}
	if (value=='0'){
		view("newsstatic");hide("newsdynamic");hide("newsforgedStatic");selecNewsMakeMode(document.getElementById('newsmakemode')[document.getElementById('newsmakemode').selectedIndex].value);
	}
	if (value=='2'){
		hide("newsstatic");view("newsforgedStatic");hide("newsdynamic");hide("newsdir2");hide("newsdir1");
	}
}

function selectCacheSearch(v){
	document.getElementById('CacheSearch').style.display=v=='1' ? '' : 'none';
}

function selectMakeplay(){
return;
	var y=document.getElementById('ismakeplay'),v=y[y.selectedIndex].value,b=document.getElementById('paramset2'),r=document.getElementById('runmode'),x=r[r.selectedIndex].value;
	if(v=='3' && x=='static'){
		var a=document.getElementById('paramset1');
		selectMakeplay.i = a.checked ? 1 : 2;
		a.checked=true;a.onclick();
		b.disabled=true;
	}else if (b.disabled){
		if(selectMakeplay.i==2){
			b.checked=true;b.onclick();
		}
		b.disabled=false;
	}
}

function onTxtChange(input){
	input.value=input.value.replace(/[^\w_]/ig,'');
}


function selectAlertWin(value){
	if (value==1){view("alertwinset");}
	if (value==0){hide("alertwinset");}
}

function selectCache(value){
	if (value==1){view("cacheset");}
	if (value==0){hide("cacheset");}
}

function clearCache(){
	set(document.getElementById("upcacheresult"),'加载中...')
	ajax.get(
		"admin_ajax.php?action=updatecache", 
		function(obj) {
			if(obj.responseText == 'ok'){
				set(document.getElementById("upcacheresult"),'缓存更新成功')
			}else{
				set(document.getElementById("upcacheresult"),'缓存更新失败')
			}
		}
	);	
}

function JsonMenu(resid){
	var menu = {"APIres":["(01)\u3010BDHD\u3011\u516b\u5ea6\u5f71\u97f3(\u767e\u5ea6\u5f71\u97f3\uff09\u5f71\u89c6\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","(02)\u3010BDHD\u3011\u767e\u5ea6\u5f71\u97f3(www.bdyyzy.com\uff09\u5f71\u89c6\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","(03)\u3010BDHD\u3011\u767e\u5ea6\u5f71\u97f3\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","(04)\u3010\u767e\u5ea6\u5f71\u97f3\u3011sfdy.net \u5185\u5bb9\u4e30\u5bcc.\u9996\u53d1\u9ad8\u6e05\u7247\u591a.\u7b80\u4ecb\u5185\u5bb9\u5f88\u539f\u521b.\u66f4\u5bb9\u6613\u88ab\u767e\u5ea6\u6536\u5f55,\u7a33\u5b9a.\u5feb\u901f.\u66f4\u65b0\u53ca\u65f6-\u63a8\u8350","(05)\u3010QVOD\u3011qvodzy.me\u5728\u7ad9\u957f\u4e2d\u4f7f\u7528\u975e\u5e38\u5e7f\u6cdb\u3001\u7a33\u5b9a\u3001\u5feb\u901f\u3001\u66f4\u65b0\u53ca\u65f6---\u63a8\u8350","(06)\u3010QVOD\u3011dydg.cc \u975e\u5e38\u9002\u5408\u505a\u7535\u5f71.\u7535\u89c6\u5267\u96c6\u7ad9.\u9996\u53d1\u7247\u591a.\u7a33\u5b9a\u3001\u5feb\u901f\u3001\u66f4\u65b0\u53ca\u65f6---\u63a8\u8350","(07)\u3010QVOD\u3011kuaizy.com\u65e0\u5f39\u7a97\u3001\u901f\u5ea6\u5feb\u3001\u66f4\u65b0\u591a","(08)\u3010GVOD\u3011gvodzi.com\u66f4\u65b0\u8f83\u4e3a\u53ca\u65f6,\u6709\u826f\u6027\u5e7f\u544a,gvodzi\u7ad9\u957f\u6bcf\u65e5\u66f4\u65b0---\u63a8\u8350","(09)\u3010QVOD\u3011hacow.me\u65e0\u5f39\u7a97\u3001\u901f\u5ea6\u5feb\u3001\u66f4\u65b0\u591a)"],"sseares":["\u3010BDHD\u3011\u516b\u5ea6\u5f71\u97f3(\u767e\u5ea6\u5f71\u97f3\uff09\u5f71\u89c6\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","\u3010BDHD\u3011\u767e\u5ea6\u5f71\u97f3(www.bdyyzy.com\uff09\u5f71\u89c6\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","(03)\u3010BDHD\u3011\u767e\u5ea6\u5f71\u97f3\u8d44\u6e90\u7ad9\u3001\u65e0\u5f39\u7a97\u3001\u8d28\u91cf\u9ad8\u3001\u66f4\u65b0\u5feb","(04)\u3010\u767e\u5ea6\u5f71\u97f3\u3011sfdy.net \u5185\u5bb9\u4e30\u5bcc.\u9996\u53d1\u9ad8\u6e05\u7247\u591a.\u7b80\u4ecb\u5185\u5bb9\u5f88\u539f\u521b.\u66f4\u5bb9\u6613\u88ab\u767e\u5ea6\u6536\u5f55,\u7a33\u5b9a.\u5feb\u901f.\u66f4\u65b0\u53ca\u65f6-\u63a8\u8350","(05)\u3010QVOD\u3011qvodzy.me\u5728\u7ad9\u957f\u4e2d\u4f7f\u7528\u975e\u5e38\u5e7f\u6cdb\u3001\u7a33\u5b9a\u3001\u5feb\u901f\u3001\u66f4\u65b0\u53ca\u65f6---\u63a8\u8350","(06)\u3010QVOD\u3011dydg.cc \u975e\u5e38\u9002\u5408\u505a\u7535\u5f71.\u7535\u89c6\u5267\u96c6\u7ad9.\u9996\u53d1\u7247\u591a.\u7a33\u5b9a\u3001\u5feb\u901f\u3001\u66f4\u65b0\u53ca\u65f6---\u63a8\u8350","(07)\u3010QVOD\u3011kuaizy.com\u65e0\u5f39\u7a97\u3001\u901f\u5ea6\u5feb\u3001\u66f4\u65b0\u591a","(08)\u3010GVOD\u3011gvodzi.com\u66f4\u65b0\u8f83\u4e3a\u53ca\u65f6,\u6709\u826f\u6027\u5e7f\u544a,gvodzi\u7ad9\u957f\u6bcf\u65e5\u66f4\u65b0---\u63a8\u8350","(09)\u3010QVOD\u3011hacow.me\u65e0\u5f39\u7a97\u3001\u901f\u5ea6\u5feb\u3001\u66f4\u65b0\u591a)"]};
	var res = resid==0?'sseares':'APIres';
	var resfrom =document.getElementById('resourcefrom');
	if(res=='sseares'){
		for(var i=0;i<9;i++)
		{
			resfrom.options[i].text=menu.sseares[i];
		}
	}
	if(res=='APIres'){
		for(var i=0;i<9;i++)
		{
			resfrom.options[i].text=menu.APIres[i];
		}
	}
}

function selecPlan(selecVal)
{
	if(selecVal=='0')
	{
		view('collectItem');
		hide('autocollectItem');
	}else if(selecVal=='2')
	{
		view('autocollectItem');
		hide('collectItem');
	}else
	{
		hide('autocollectItem');
		hide('collectItem');
	}
}

function setBindType(curid,classid){
	openWindow2(101,280,28,0)
	var msgDiv=document.getElementById("msg")
	var topicTDObj = document.getElementById("bind_"+curid);
	var topicTDTop = topicTDObj.offsetTop;
    var topicTDLeft = topicTDObj.offsetLeft; 
    while (topicTDObj = topicTDObj.offsetParent){topicTDTop+=topicTDObj.offsetTop; topicTDLeft+=topicTDObj.offsetLeft;}
    msgDiv.style.cssText+="border:1px solid #55BBFF;background: #C1E7FF;padding:3px 0px 3px 4px;"
	msgDiv.style.top = (topicTDTop-2)+"px";
    msgDiv.style.left = (topicTDLeft+1)+"px"; 
	//msgDiv.innerHTML="正在加载内容....";	
	ajax.get(
		"admin_reslib.php?curid="+curid+"&ac=bind&classid="+classid, 
		function(obj) {
			msgDiv.innerHTML=obj.responseText;
		}
	);
}

function submitBindType(tid,curid,oldtype){
	/*var tid = document.getElementById("tid").value
	var v_oldtype = document.getElementById("v_oldtype").value
	var curid = document.getElementById("curid").value*/
	ajax.get(
		"admin_reslib.php?ac=bindsubmit&curid="+curid+"&tid="+tid+"&v_oldtype="+oldtype, 
		function(obj) {
			if(obj.responseText == "bindok"){
				set(document.getElementById("bind_"+curid)," <b><a href='javascript:void(0)' id='bind_"+curid+"' onClick=\"setBindType('"+curid+"',0);\">已绑定</a></b>");
				hideBind();
			}else if(obj.responseText == "nobind"){
				set(document.getElementById("bind_"+curid)," <b><a href='javascript:void(0)' id='bind_"+curid+"' onClick=\"setBindType('"+curid+"',0);\"><font color='red'>未绑定</font></a></b>");
				hideBind();
			}else{ 
				set(document.getElementById("bind_"+curid),"<font color='red'>发生错误</font>");		
			}
		}
	);
}


function hideBind(){
	document.body.removeChild(document.getElementById("msg")); 
	document.body.removeChild(document.getElementById("bg")); 
}

function getSelect_Value(obj_id)
{
	var obj = document.getElementById(obj_id); //selectid
	var index = obj.selectedIndex; // 选中索引
	var value = obj.options[index].value; // 选中值
	return value;
}