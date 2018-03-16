var zzid=0;
var pisea_Player_File="play2.swf";//已无效参数
var playerw='685';//电脑端播放器宽度
var playerh='420';//电脑端播放器高度
var mplayerw='400';//手机端播放器宽度
var mplayerh='320';//手机端播放器高度
var skinColor='d3e3f3,999999|d1d3a2,3300FF|94d2e2,000000|000000,000000|c9abca,000000';//已无效参数
var openMenu="2";//已无效参数
var logoURL="logo.png";//已无效参数
var adsPage="http://www.seacms.net/api/loading.html";//视频播放前广告页路径
var adsTime=3;//视频播放前广告时间，单位秒
var showFullBtn="0"; //已无效参数
var rehref="0";//已无效参数
var alertwin='0';//已无效参数
var alertwinw='';//已无效参数
var alertwinh='';//已无效参数
var btnName="上一集,下一集";
var qvod_str_downurl="http://dl.qvod.com/QvodSetup360.exe";//已无效参数
var qvod_str_alert="您的电脑未安装Qvodplayer播放软件,请点击下载安装后刷新本页面播放";//已无效参数
var w3c = (document.getElementById) ? true : false;
var agt = navigator.userAgent.toLowerCase();
var ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1) && (agt.indexOf("omniweb") == -1));
var ie5 = (w3c && ie) ? true : false;
var ns6 = (w3c && (navigator.appName == "Netscape")) ? true : false;
var op8 = (navigator.userAgent.toLowerCase().indexOf("opera") == -1) ? false : true;

function IsPC() {
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone",
                "SymbianOS", "Windows Phone",
                "iPad", "iPod"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
 
var flag = IsPC(); //true为PC端，false为手机端
if(flag==false)
{
	playerw=mplayerw;
	playerh=mplayerh;
}


__ = ['|', "innerWidth", "body", "clientWidth", "innerHeight", "clientHeight", "documentElement", "getElementById", "contentDocument", "frames", "document", "cciiframe", "style", "width", 'px', "height", "left", "0", "top", 'cciplay', "split", ",", "scroll", "px", "pisea_player", "piswf", "getASVars", "open", "close", "appName", "indexOf", "Microsoft", "userAgent", "toLowerCase", "match", "ie", "firefox", "chrome", "opera", "safari", "IE", "FIREFOX", "CHROME", "OPERA", "SAfARI", "-1", "src", "/", ".html", "write", '<div id="cc1play" style="height:0px; width:0px;display:none;wiidth:', 'px;heiight:', 'px;"></div>', "offsetLeft", "offsetTop", "currentStyle", "borderLeftWidth", "", "borderTopWidth", "offsetParent", "_lOlOl01l", "_lOlOl100", ',', 'cciiframe', "1", '0', "onresize", "<div id='cci1frame'style='height:0px; width:0px;display:none;wiidth: ", "px; heiight: ", "px;'></div>", "moveTo", "resizeTo", "availWidth", "availHeight", "onerror"];
var _lOlO1000 = 0;
var _lOlO1001 = 0;
function _lOlOl000() {
	return VideoInfoList
};
function _lOlOl001() {
	return paras[2] + __[0] + paras[1] + __[0] + paras[0]
};
function _lOlOl00O() {
	if (window[__[1]]) {
		_lOlO1000 = window[__[1]]
	} else if ((document[__[2]]) && (document[__[2]][__[3]])) {
		_lOlO1000 = document[__[2]][__[3]]
	}
	if (window[__[4]]) {
		_lOlO1001 = window[__[4]]
	} else if ((document[__[2]]) && (document[__[2]][__[5]])) {
		_lOlO1001 = document[__[2]][__[5]]
	}
	if (document[__[6]] && document[__[6]][__[5]] && document[__[6]][__[3]]) {
		_lOlO1001 = document[__[6]][__[5]];
		_lOlO1000 = document[__[6]][__[3]]
	}
};
function _lOlOl00l(__0) {
	return document[__[7]](__0)[__[8]] || document[__[9]][__0][__[10]]
};
function _lOlOl010(__0) {
	var __1 = document.getElementById(__[11]);
	_lOlOl00O();
	if (__0 == 1) {
		__1[__[12]][__[13]] = (_lOlO1000) + __[14];
		__1[__[12]][__[15]] = (_lOlO1001) + __[14];
		__1[__[12]][__[16]] = __[17];
		__1[__[12]][__[18]] = (_lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[0]) + __[14];
		window[__[22]](0, (_lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[0]))
	} else if (__0 == 2) {
		__1[__[12]][__[13]] = playerw + __[14];
		__1[__[12]][__[15]] = playerh + __[14];
		__1[__[12]][__[18]] = _lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[0] + __[23];
		__1[__[12]][__[16]] = _lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[1] + __[23];
		_lOlOl101()
	} else if (__0 == 3) {
		var __2 = _lOlOl00l(__[11]);
		__1[__[12]][__[13]] = (_lOlO1000) + __[14];
		__1[__[12]][__[15]] = (_lOlO1001) + __[14];
		__1[__[12]][__[16]] = __[17];
		__1[__[12]][__[18]] = (_lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[0]) + __[14];
		_lOlO100O = __2[__[7]](__[24]);
		if (__2[__[25]][__[26]]() == __[27]) {
			_lOlO100O[__[12]][__[13]] = (_lOlO1000 - 162) + __[14];
			_lOlO100O[__[12]][__[15]] = (_lOlO1001 - 25) + __[14]
		} else if (__2[__[25]][__[26]]() == __[28]) {
			_lOlO100O[__[12]][__[13]] = (_lOlO1000) + __[14];
			_lOlO100O[__[12]][__[15]] = (_lOlO1001 - 25) + __[14]
		}
	}
};
function _lOlOl011(__0) {
	if (navigator[__[29]][__[30]](__[31]) != -1) {
		return window[__0]
	} else {
		return document[__0]
	}
};
function _lOlOl01O() {
	var __0 = {};
	var __1 = navigator[__[32]][__[33]]();
	var __2; (__2 = __1[__[34]](/msie ([\d.]+)/)) ? __0[__[35]] = __2[1] : (__2 = __1[__[34]](/firefox\/([\d.]+)/)) ? __0[__[36]] = __2[1] : (__2 = __1[__[34]](/chrome\/([\d.]+)/)) ? __0[__[37]] = __2[1] : (__2 = __1[__[34]](/opera.([\d.]+)/)) ? __0[__[38]] = __2[1] : (__2 = __1[__[34]](/version\/([\d.]+).*safari/)) ? __0[__[39]] = __2[1] : 0;
	if (__0[__[35]]) {
		return __[40]
	} else if (__0[__[36]]) {
		return __[41]
	} else if (__0[__[37]]) {
		return __[42]
	} else if (__0[__[38]]) {
		return __[43]
	} else if (__0[__[38]]) {
		return __[44]
	} else {
		return __[45]
	}
};
function _lOlOl10l(__0, __1, __2) {
	var __3, __4, __5, __6, __7;
	__7 = __2;
	__3 = handleParas(__0, __1);
	__4 = document.getElementById(__[11]);
	try {
		autoSubmitErr(__3, __1)
	} catch(e) {};
	
	document[__[49]](__[50] + playerw + __[51] + playerh + __[52]);
	__4[__[12]][__[13]] = playerw + __[23];
	__4[__[12]][__[15]] = playerh + __[23];
	__4[__[12]][__[18]] = _lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[0] + __[23];
	__4[__[12]][__[16]] = _lOlOl100(_lOlOl01l(__[19]))[__[20]](__[21])[1] + __[23]
};
function _lOlOl01l(__0) {
	return document.getElementById(__0) ? document.getElementById(__0) : __0
};
function _lOlOl100(__0) {
	var __1 = 0;
	var __2 = 0;
	var __3 = __0;
	while (__3 != null && __3 != document[__[2]]) {
		__1 += __3[__[53]];
		__2 += __3[__[54]];
		if (!ns6) {
			parseInt(__3[__[55]][__[56]]) > 0 ? __1 += parseInt(__3[__[55]][__[56]]) : __[57];
			parseInt(__3[__[55]][__[58]]) > 0 ? __2 += parseInt(__3[__[55]][__[58]]) : __[57]
		};
		__3 = __3[__[59]]
	};
	return __2 + __[21] + __1
};
if (alertwin == __[17]) {
	var _lOlOl101 = function() {
		var __0 = window[__[60]](__[19]);
		_lOlO100l = window[__[61]](__0)[__[20]](__[62])[1] + __[14];
		var __1 = document.getElementById(__[63]);
		if (__1[__[12]][__[13]] == playerw + __[14]) {
			__1[__[12]][__[16]] = _lOlO100l
		} else {
			_lOlOl010(3)
		}
	}
} else if (alertwin == __[64]) {
	playerw = alertwinw;
	playerh = alertwinh;
	showFullBtn = __[65];
	window[__[66]] = function() {
		_lOlOl101()
	};
	var _lOlOl101 = function() {
		_lOlOl010(3)
	}
};
function _lOlOl10O() {
	return true
};
document[__[49]](__[67] + playerw + __[68] + playerh + __[69]);

var now = "";
var next = "";
var nextPage = "";
var prePage = "";
var zno= "123321";
window.onload = function () {
var videolist = VideoInfoList.split("$$");
var all = VideoInfoList.split("$$$");
var pn_num = paras[1];
var cji = paras[2];
var pn=all[pn_num];
var pn=pn.split("$$");
var now3=pn;
var pn=pn[1];
var pn=pn.split("$");
var pn=pn[2];
var pn=pn.split("#");
var pn=pn[0];
//alert(now3);
getPlay(cji,now3,pn,videolist);
}

function getPlay(cji,now1,pn,videolist) {
	 var cji=parseInt(cji);
	 var now3=now1;
	 var now2=now1;
	 var now3 = now3.join("");
	 var now2 = now2.join("");
	 var now3 = now3.split("#");
	 var now2 = now2.split("#");
	 var jishu= now3.length;
	 var now3 = now3[cji];
	 var now2 = now2[cji+1];
     var now4 = now3.split("$");
	 now = now4[1];
	 pn = now4[2];
	 

	 
	 
	if (jishu > (cji + 1)) {
        var nextNum = (cji + 1);
		var now2 = now2.split("$");
        next = now2[1];
		
    }
	
	///////////////////////
	// by海洋 播放器上下页参数
var ssurl =window.location.href;

//下页
var xs=ssurl;
var xa=xs.split("/"); 
var xn=xa.length; 
var xxs=xa[xn-1]; 
var xxa=xxs.split("-"); 
var xxn=xxa.length; 
var xxxs=xxa[xxn-1]; 
var xxxa=xxxs.split(".");
var xxxn=xxxa.length; 
var xxxxs=xxxa[0];
var xxxx=parseInt(xxxs)+1;
xxxa[0]=xxxx;
xxxs=xxxa.join(".");
xxa[xxn-1]=xxxs;
xxs=xxa.join("-");
xa[xn-1]=xxs;
xs=xa.join("/");
nextPage=xs;
if(cji == (jishu-1)){nextPage="";}


///////////////////////
	
setTimeout(appendFrm(pn),1000);
}
function appendFrm(pn) {
	var pn=pn;
	document.getElementById("cciframe").width = playerw;
	document.getElementById("cciframe").height = playerh;
    document.getElementById("cciframe").src = '/js/player/'+ pn + '.html';
}

/* By Yaokun */
var video_openFlag = 0;
var video_url_path = '';//视频路径
var video_url_id = '';//视频id
var video_url_group = 0;//组id
var video_url_index = 0;//序数
var video_url_type = '';//视频扩展名

var pageUrl = window.location.href;
var pageUrl_index = pageUrl.lastIndexOf('/');
	video_url_path = pageUrl.substr(0, pageUrl_index + 1);
var pageUrl = pageUrl.substr(pageUrl_index + 1);		
var video_get=[];

//pageUrl = pageUrl.replace('?','');
pageUrl = pageUrl.split('.');
video_url_type = pageUrl[pageUrl.length-1];//扩展名
pageUrl = pageUrl[0];//得到:视频-组id-index格式

video_get = pageUrl.split('-');
video_url_group = parseInt(video_get[video_get.length-2],10);
video_url_index = parseInt(video_get[video_get.length-1],10);
video_url_id = pageUrl.split('-'+ video_url_group + '-')[0];
pageUrl = null;

/* Video List */
var video_level_1 = [];//某站点
var video_level_2 = [];//某站集数
function video_diy(){
	var obj_ele = VideoInfoList;
	var video_site = obj_ele.split('$$$');
	var tmp_1=[] ,tmp_2=[] ,tmp_3=[] ;
	var color1,color2;
	var video_html ='<ul>';
	var iframe = document.getElementById("cciframe").contentWindow;
	
	for(var i=0; i< video_site.length;i++){	
		tmp_1 = video_site[i].split('$$');
		tmp_2 = tmp_1[1].split(/\$\w+\#/);		
		video_level_1.push(tmp_1[0]);
		
		video_level_2[i] = new Array();
		for(var j=0;j< tmp_2.length;j++){
			tmp_3 = tmp_2[j].split('$');
			video_level_2[i][j] = tmp_3[0];
		}
	}
	for(var i=0;i<video_level_1.length;i++){		
		if(video_url_group == i){
			video_html += '<li><p class="current1">'+ video_level_1[i] +'</p><dl style="display:block;">';
		}else{
			video_html += '<li><p>'+ video_level_1[i] +'</p><dl>';
		} 

		for(var j=0;j<video_level_2[i].length;j++){
			color2 = (video_url_group == i && video_url_index == j) ? ' class="current2"' : '';
			video_html += '<dd><a href="'+ video_url_path + video_url_id + '-' + i + '-'+ j +'.'+ video_url_type + '" target="_parent"'+ color2 + '>'+ video_level_2[i][j] +'</a></dd>';
		}
		video_html += '</dl></li>';
	}
	video_html +='</ul>';
	
	//----------------播放器导航----------------------
	var header1 = "<a href=\"javascript:void()\" onclick=\"return parent.video_jump(-1)\">上一集</a><a href=\"javascript:void()\" onclick=\"return parent.video_jump(1)\">下一集</a>";
	var header3 = "<a href=\"javascript:void(0)\" onClick=\"parent.video_opens(this);\">开启/关闭列表</a>";
		
	iframe.document.body.style.margin = 0;
	iframe.document.getElementById('video_menu').style.height = (parseInt(playerh,10) - 34)+'px';
	iframe.document.getElementById('video_menu').innerHTML = video_html;
	iframe.document.getElementById('header1').innerHTML = header1;
	iframe.document.getElementById('header3').innerHTML = header3;
	
	//P显示与隐藏
	var video_li = iframe.document.getElementById('video_menu').getElementsByTagName('p');
	for(var j= 0;j<video_li.length;j++){
		video_li[j].onclick = function(){
			// 关闭所有的dl
			var dl = iframe.document.getElementById('video_menu').getElementsByTagName('dl');
			for(var jj= 0;jj<dl.length;jj++){
				dl[jj].style.display = 'none';
			}
			// 开启当前的dl
			this.parentNode.getElementsByTagName('dl')[0].style.display = 'block';
		};
	}
	video_title();	
	tmp_1 = tmp_2 = tmp_3 = null;
};

//-------------

/* video close/open */
function video_opens(e){	
	var iframe = document.getElementById("cciframe").contentWindow;
	if (video_openFlag == 0){ 
		video_openFlag = 1;
		iframe.document.getElementById('video_menu').style.width = '28%';
		iframe.document.getElementById('player').style.width = '72%';
	}
	else
	{
		video_openFlag = 0;
		iframe.document.getElementById('video_menu').style.width = '0';
		iframe.document.getElementById('player').style.width = '100%';
	} 	
	//e.preventDefault();
}

//video title
var vidow_title_f = 0;
function video_title(){
	var val = '';
	if(vidow_title_f){
		val = window.location.href;
		val = val.toLowerCase();
		val = val.replace('http:\/\/','');
		val = val.substr(0,val.indexOf('/'));
	}
	else
	val = playn + ' &nbsp; ' + playp;
	var iframe = document.getElementById("cciframe").contentWindow;
	iframe.document.getElementById('header2').innerHTML = val;
	
	vidow_title_f = vidow_title_f ? 0:1;
	setTimeout("video_title()",10000);
}

//video redirect
function video_jump(flag){
	var f = 0;
	var video_len = video_level_2[video_url_group].length -1;	
	var video_current = video_url_index + flag; 
	
	if(flag == -1 && video_current < 0){
		alert('已经是第一集啦!');
		return false;
	}else if(flag == 1 && video_len < video_current){
		alert('已经是最后一集啦!');
		return false;
	}else{
		window.location.href = video_url_path + video_url_id + '-'+ video_url_group + '-' + video_current + '.' + video_url_type;	
	}
}


	///////////////////////
	// by海洋 播放器上下页参数
var ssurl =window.location.href;

//上页
var ss=ssurl;
var sa=ss.split("/"); 
var sn=sa.length; 
var sss=sa[sn-1]; 
var ssa=sss.split("-"); 
var ssn=ssa.length; 
var ssss=ssa[ssn-1]; 
var sssa=ssss.split(".");
var sssn=sssa.length; 
var sssss=sssa[0];
var ssss=parseInt(ssss)-1;
sssa[0]=ssss;
ssss=sssa.join(".");
ssa[ssn-1]=ssss;
sss=ssa.join("-");
sa[sn-1]=sss;
ss=sa.join("/");
if(sssss==0){prePage=='';}
else{
prePage=ss;
}






/*function killerrors() { return true; } 
window.onerror = killerrors; */