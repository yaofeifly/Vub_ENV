var $MH={
	limit: 10,
	width:960,
	height: 170,
	style: 'pic',
	setCookie: function(name, value) {
		var Days = 365;
		var exp = new Date;
		exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
		document.cookie = name + ("=" + (value) + ";expires=" + exp.toGMTString() + ";path=/;");
	},
	getCookie: function(name) {
		var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
		if (arr != null) {
			return (arr[2]);
		}
		return null;
	},
	getDc: function(){
		var x,y=document.getElementById('HISTORY');
		return y;
	},
	piclist: function (){
		var a = $MH.getCookie("HISTORY"), c = 1,img_li = "";
		a = (a !='' && ''+a != 'null') ? $MH.tryjosn(a) : {video:[]};
		for(var i=0;i<a.video.length;i++){
			if(c>$MH.limit){break;}
			if(a.video[i].link && a.video[i].pic && a.video[i].name){
			img_li += "<li style=\"width:86px;height:142px;text-align:center;margin:3px 0 3px 9px !important;float:left;display:inline;overflow:hidden\"><div><a href=\"" + a.video[i].link + "\" target=\"_self\"><img width=\"86\" height=\"120\" src=\"" + a.video[i].pic + "\" alt=\"" + a.video[i].name + "\" border=\"0\"/></a></div>\
						<p style=\"margin:0;padding:0\"><a href=\"" + a.video[i].link + "\" target=\"_self\" style=\"font-size:12px;color:#000;line-height:24px;height:24px;text-decoration:none\">" + a.video[i].name + "</a></p></li>"
				c++;
			}
		}
		img_li = img_li != "" ? img_li : '<li style="width:100%;text-align:center;line-height:'+($MH.height-25)+'px;color:red">\u6CA1\u6709\u8BB0\u5F55</li>';
		return "<div id=\"mh-box\" style=\"border:1px solid #ccc;height:"+$MH.height+"px;overflow:hidden\"><div style=\"height:24px;line-height:24px\" id=\"mh-title\"><div style=\"float:right;margin-right:5px;display:inline\"><a href=\"javascript:void(0)\" onClick=\"$MH.showHistory(2);\" style=\"font-size:12px;color: #000000;line-height:24px;height:24px;text-decoration:none\">\u6E05\u7A7A</a>&nbsp;<a href=\"javascript:void(0)\" onClick=\"$MH.showHistory(1);\" style=\"font-size:12px;color: #000000;line-height:24px;height:24px;text-decoration:none\">\u9690\u85CF</a></div><strong style=\"padding-left:5px;font-size:14px\">\u6211\u7684\u89C2\u770B\u5386\u53F2</strong></div><div id=\"mh-ul\"><ul style=\"margin:0px;border:0px;padding:0\">" + img_li + "</ul><div style=\"clear:both\"></div></div></div>";
	},
	fontlist: function (){
		var a = $MH.getCookie("HISTORY"), c = 1,img_li = "";
		a = (a !='' && ''+a != 'null') ? $MH.tryjosn(a)  : {video:[]} ;
		for(var i=0;i<a.video.length;i++){
			if(c>$MH.limit){break;}
			if(a.video[i].link && a.video[i].pic && a.video[i].name){
			img_li += "<li style=\"list-style:none;margin:0 5px\"><small>"+c+".</small><a href=\"" + a.video[i].link + "\" target=\"_self\" style=\"font-size:12px;color:#000;text-decoration:none\">" + a.video[i].name + "</a></li>"
				c++;
			}
		}
		img_li = img_li != "" ? img_li : '<li style="width:100%;text-align:center;line-height:'+($MH.height-25)+'px;color:red;list-style:none">\u6CA1\u6709\u8BB0\u5F55</li>';
		return "<div id=\"mh-box\" style=\"border:1px solid #ccc;height:"+$MH.height+"px;overflow:hidden\"><div style=\"height:24px;line-height:24px\" id=\"mh-title\"><div style=\"float:right;margin-right:5px;display:inline\"><a href=\"javascript:void(0)\" onClick=\"$MH.showHistory(2);\" style=\"font-size:12px;color: #000000;line-height:24px;height:24px;text-decoration:none\">\u6E05\u7A7A</a>&nbsp;<a href=\"javascript:void(0)\" onClick=\"$MH.showHistory(1);\" style=\"font-size:12px;color: #000000;line-height:24px;height:24px;text-decoration:none\">\u9690\u85CF</a></div><strong style=\"padding-left:5px;font-size:14px\">\u6211\u7684\u89C2\u770B\u5386\u53F2</strong></div><div id=\"mh-ul\"><ul style=\"margin:0px;border:0px;padding:0\">" + img_li + "</ul><div style=\"clear:both\"></div></div></div>";
	},
	WriteHistoryBox: function(w,h,c){
		document.write('<div id="HISTORY" style="width:'+($MH.width=w)+'px;"></div>');
		$MH.height=h;$MH.style= c=='font' ? 'font' : 'pic';
		this.showHistory();
	},
	showHistory: function(ac) {
		var a = $MH.getCookie("HISTORY"),dc=$MH.getDc();
		var ishistory = $MH.getCookie("ishistory");
		if(!dc) return;
		if (ac == 1) {
			if (ishistory != 1) {
				$MH.setCookie("ishistory", 1);
				ishistory = 1;
			} else {
				$MH.setCookie("ishistory", 0);
				ishistory = 0;
			}
		}
		if (ac == 2) {
			ishistory = 0;
			$MH.setCookie("ishistory", 0);
			$MH.setCookie("HISTORY", 'null');
		}
		if(ishistory == 1){
			dc.innerHTML = $MH[$MH.style+'list']();
			dc.style.display = "";
		} else {
			dc.innerHTML = "";
			dc.style.display = "none";
		}
	},
	recordHistory: function(video){
		if(video.link.indexOf('http://')==-1 || window.max_Player_File) return;
		var a = $MH.getCookie('HISTORY'), b = new Array(), c = 1;
		if(a !='' && a != null && a != 'null'){
			a = $MH.tryjosn(a);
			for(var i=0;i<a.video.length;i++){
				if(c>$MH.limit){break;}
				if(video.link != a.video[i].link && a.video[i].pic){b.push('{"name":"'+ $MH.u8(a.video[i].name) +'","link":"'+ $MH.u8(a.video[i].link) +'","pic":"'+ $MH.u8(a.video[i].pic) +'"}');c++;}
			}
		}
		b.unshift('{"name":"'+ $MH.u8(video.name) +'","link":"'+ $MH.u8(video.link) +'","pic":"'+ $MH.u8(video.pic) +'"}');
		$MH.setCookie("HISTORY",'{video:['+ b.join(",") +']}');
		b = null;
		a=null;
	},
	u8: function (s){
		return unescape(escape(s).replace(/%u/ig,"\\u")).replace(/;/ig,"\\u003b");
	},
	tryjosn: function (json){
		try{
			return eval('('+ json +')');
		}catch(ig){
			return {video:[]};
		}
	}
}