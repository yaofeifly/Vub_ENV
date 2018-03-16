var mini = function() {
	function b(l, m) {
		m = m || document;
		if (!/^[\w\-_#]+$/.test(l) && m.querySelectorAll) return f(m.querySelectorAll(l));
		if (l.indexOf(",") > -1) {
			l = l.split(/,/g);
			for (var n = [], h = 0, j = l.length; h < j; ++h) n = n.concat(b(l[h], m));
			return o(n)
		}
		l = l.match(c);
		j = l.pop();
		n = (j.match(g) || k)[1];
		h = !n && (j.match(e) || k)[1];
		j = !n && (j.match(i) || k)[1];
		if (h && !j && m.getElementsByClassName) j = f(m.getElementsByClassName(h));
		else {
			j = !n && f(m.getElementsByTagName(j || "*"));
			if (h) j = a(j, "className", RegExp("(^|\\s)" + h + "(\\s|$)"));
			if (n) return (m = m.getElementById(n)) ? [m] : []
		}
		return l[0] && j[0] ? d(l, j) : j
	}
	function f(l) {
		try {
			return Array.prototype.slice.call(l)
		} catch(m) {
			for (var n = [], h = 0, j = l.length; h < j; ++h) n[h] = l[h];
			return n
		}
	}
	function d(l, m, n) {
		var h = l.pop();
		if (h === ">") return d(l, m, true);
		var j = [],
		q = -1,
		p = (h.match(g) || k)[1],
		r = !p && (h.match(e) || k)[1];
		h = !p && (h.match(i) || k)[1];
		var v = -1,
		u, s, t;
		for (h = h && h.toLowerCase(); u = m[++v];) {
			s = u.parentNode;
			do {
				t = (t = (t = !h || h === "*" || h === s.nodeName.toLowerCase()) && (!p || s.id === p)) && (!r || RegExp("(^|\\s)" + r + "(\\s|$)").test(s.className));
				if (n || t) break
			} while ( s = s.parentNode );
			if (t) j[++q] = u
		}
		return l[0] && j[0] ? d(l, j) : j
	}
	function a(l, m, n) {
		for (var h = -1,j, q = -1,p = []; j = l[++h];) if (n.test(j[m])) p[++q] = j;
		return p
	}
	var c = /(?:[\w\-\\.#]+)+(?:\[\w+?=([\'"])?(?:\\\1|.)+?\1\])?|\*|>/ig,
	e = /^(?:[\w\-_]+)?\.([\w\-_]+)/,
	g = /^(?:[\w\-_]+)?#([\w\-_]+)/,
	i = /^([\w\*\-_]+)/,
	k = [null, null],
	o = function() {
		var l = +new Date,
		m = function() {
			var n = 1;
			return function(h) {
				var j = h[l],
				q = n++;
				if (!j) {
					h[l] = q;
					return true
				}
				return false
			}
		} ();
		return function(n) {
			for (var h = n.length,j = [], q = -1, p = 0, r; p < h; ++p) {
				r = n[p];
				if (m(r)) j[++q] = r
			}
			l += 1;
			return j
		}
	} ();
	return b
} ();
if (typeof Le == "undefined") var Le = {};
Function.prototype.method = function(b, f) {
	this.prototype[b] = f;
	return this
};
(function() {
	Le.register = function(b) {
		function f(d,m) {
			if (typeof d == "string") {
				d = mini(d,m);
				if (!d || d == "" || typeof d == "undefined" == "undefined") return false;
				if (d.length == 1) this.el = d[0];
				else if (d.length > 1) this.el = d
			} else if (d.nodeType == 1) this.el = d
		}
		f.method(b.each,function(d) {
			if (this.el) {
				if (this.el.length) for (var a = 0,
				c = this.el.length; a < c; ++a) d.call(this, this.el[a], a);
				else d.call(this, this.el);
				return this
			}
		}).method(b.hasClass,function(d, a) {
			this.each(function(c) {
				var e = c.className.split(/\s+/).toString().indexOf(d) > -1 ? true: false; (function() {
					a(e)
				})()
			});
			return this
		}).method(b.addClass,function(d) {
			this.each(function(a) {
				for (var c = (d || "").split(/\s+/), e = 0; e < c.length; e++) {
					var g = c[e];
					this.hasClass(a,
					function(i) {
						i || (a.className += (a.className ? " ": "") + g)
					})
				}
			});
			return this
		}).method(b.removeClass,function(d) {
			this.each(function(a) {
				if (d != undefined) {
					for (var c = a.className.split(/\s+/), e = [], g = 0, i = c.length; g < i; ++g) {
						var k = c[g];
						k != d && e.push(k)
					}
					a.className = e.join(" ")
				} else a.className = ""
			});
			return this
		}).method(b.replaceClass,function(d, a) {
			this.removeClass(d);
			this.addClass(a);
			return this
		}).method(b.css,function(d, a) {
			this.each(function(c) {
				c.style[d] = a
			});
			return this
		}).method(b.setCSS,function(d) {
			for (var a in d) d.hasOwnProperty(a) && this.css(a, d[a]);
			return this
		}).method(b.attr,function(d, a) {
			var ret=[];
			this.each(function(c) {
				ret.push(c.getAttribute(d));
				if(a!==undefined) c.setAttribute(d, a);
			});
			return ret.length>1 ? ret : ret.pop();
		}).method(b.removeAttr,function(d) {
			this.each(function(c) {
				c.removeAttribute(d);
			});
			return this
		}).method(b.val,function(a) {
			var ret=this.el.value;
			if(a!=undefined) this.el.value=a;
			return ret;
		}).method(b.show,function(d) {
			if (d == 0) this.css("display", "");
			else d == 1 ? this.css("display", "") : this.css("display", "block");
			return this
		}).method(b.hide,function() {
			this.css("display", "none");
			return this
		}).method(b.toggle,function(d) {
			this.each(function(a) {
				if (a.style.display == "none"){
					if (d){
						d == 1 ? (a.style.display = "inline") : (a.style.display = "");
					}else{
						a.style.display = "block";
					}
				}else{
					a.style.display = "none";
				}
			});
			return this
		}).method(b.bind,function(d, a) {
			var c = function(e) {
				var g = function(ev) {
					a.call(e,ev)
				};
				if (window.addEventListener) e.addEventListener(d, g, false);
				else window.attachEvent && e.attachEvent("on" + d, g)
			};
			if (this.el) {
				this.el.length == 0 ? c(this.el) : this.each(function(e) {
					c(e)
				});
				return this
			}
		}).method(b.html,function(a) {
			this.each(function (c){
				c.innerHTML=a;
			});
			return this
		}).method(b.focus,function() {
			this.el.focus();
			return this
		}).method(b.remove,function(d) {
			d && this.el.removeChild(d);
			return this
		});
		window[b.namespace] = function(d) {
			return new f(d)
		};
		Le.extendChain = function(d, a) {
			f.method(d, a)
		}
	}
})();
Le.register({
	namespace: "$",
	each: "each",
	addClass: "addClass",
	hasClass: "hasClass",
	removeClass: "removeClass",
	replaceClass: "replaceClass",
	setCSS: "setCSS",
	css: "css",
	attr: "attr",
	val: "val",
	show: "show",
	hide: "hide",
	toggle: "toggle",
	bind: "bind",
	focus: "focus",
	html: "html",
	remove: "remove",
	removeAttr: "removeAttr"
});

var Json=(function (){
	var $=["MSXML2.XMLHTTP", "Microsoft.XMLHTTP",window.XMLHttpRequest],_=[],
		http=function (){
			for(var i=0,x;i<_.length;i++) if (_[i].readyState == 0 || _[i].readyState == 4) return _[i];
			for(i=0;i<$.length;i++){
				try{
					if (window.XMLHttpRequest) {
					x=typeof $[i] == "function" ? new $[i]() : new XMLHttpRequest($[i]);
				}else
				{
					x=typeof $[i] == "function" ? new $[i]() : new ActiveXObject($[i]);
				}
					
					break;
				}catch(ig){
					x=null;
				}
			}
			if(!x){
				throw "Cannot init XMLHttpRequest object!";
				return false;
			}else{
				return _[_.length]=x;
			}
		};
	return function (url,sfun,efun,timeout){
		var o=http(),x=0,t=timeout || 3600000,sf=sfun || function (){},ef=efun || function (){};
		o.open("get",url,true);
		x=window.setTimeout(function (){
			o.abort()
		},t);
		o.onreadystatechange=function (){
			if(o.readyState == 4){
				window.clearTimeout(x);
				if(o.status == 200){
					var a = eval("(" + o.responseText + ")");
					sf.call(o,a);
				}else{
					ef.call(o);
				}
			}
		};
		o.send("");
	}
})();
var viewmsg=(function (){
var $=["MSXML2.XMLHTTP", "Microsoft.XMLHTTP",window.XMLHttpRequest],_=[],
		http=function (){
			for(var i=0,x;i<_.length;i++) if (_[i].readyState == 0 || _[i].readyState == 4) return _[i];
			for(i=0;i<$.length;i++){
				try{
				if (window.XMLHttpRequest) {
					x=typeof $[i] == "function" ? new $[i]() : new XMLHttpRequest($[i]);
				}else
				{
					x=typeof $[i] == "function" ? new $[i]() : new ActiveXObject($[i]);
				}
					break;
				}catch(ig){
					x=null;
				}
			}
			if(!x){
				throw "Cannot init XMLHttpRequest object!";
				return false;
			}else{
				return _[_.length]=x;
			}
		};
	return function (id,type,page){
		var o=http();
		o.open("get","api/send.php?gid="+id+"&type="+type+"&page="+page,true);
		o.onreadystatechange=function (){
			if(o.readyState == 4){
			  ShowMsg(o.responseText);
			 }
		};
		o.send("");
}
})();	

//iframeheight
function resizeiframe(){
    try{var frames=parent.document.documentElement.getElementsByTagName("iframe");
        for(var i=0;i<frames.length;i++){
            if(frames[i].getAttribute('name')=='comment'){
                frames[i].style.height=document.body.scrollHeight+"px";
                var topPos=location.href.indexOf("#top");
                if(topPos>0){location.href=location.href.substring(0,topPos)+"#top1"}
				setTimeout('resizeiframe();',1);
                break;
            }
        }
    }catch(e){ return;}
}


//cookie
var user = function(){var getcookie = function(name){var cookievalue = "",search = name + "="; if(document.cookie.length > 0){offset = document.cookie.indexOf(search); if (offset != -1){ offset += search.length; end = document.cookie.indexOf(";", offset); if (end == -1) end = document.cookie.length; cookieValue = unescape(document.cookie.substring(offset, end)) }}return cookievalue;},uid=getcookie("uid"),uname=getcookie("username");return {"id":uid||0,"name":uname||"","nick":"","face":"",upfile:""};}();
//init
(function(){
	$("#tit").html(""+unescape(param.gname)+"相关评论");
	//viewmsg(param.gid,param.type,1);
	if(parseInt(param.allowup) === 1)viewform1();
	if(parseInt(param.islogin) === 1)
	{
		$("#anonymous").hide();
		$("#anonylabel").hide();
	}
	if(parseInt(param.iscaptcha)==0)$("#iscaptcha").hide();
	page(param.type,param.gid,param.page);
	$("#ctype").val(param.type);
	$("#gid").val(param.gid);
	$("#uid").val(user.id);
	$("#uname").val(user.name);
	$("#unick").val(user.nick);
	$(".exit").bind('click',function(){$("#editor").hide();})
	$("#anonymous").bind('click',function(){
		$("#anonylabel").html(this.checked ? '<input type="text" name="tmpname" id="tmpname" value="匿名" maxlength="20" onkeydown="if(event.keyCode == 13) {submitform();}"/>' : '匿名');
	});
	$("#cmt").bind('click',function(){
		if(document.getElementById('editor').style.display=="none"){$("#editor").show();}
		else{$("#editor").hide();}
	});
	$("#talk").bind('keydown',function(ev){
		if(ev.ctrlKey && ev.keyCode == 13 || ev.altKey && ev.keyCode == 83) {
			return submitform();
		}
	});
})();
function trim(s){return s.replace(/[\t\s]+/g,"");}
//allowuppic
function viewform1(){
	$("#uploadpic").html('<form id="form1" name="form1" style="padding:0;margin:0" action="api/send.php?action=1" method="post" target="myiframe" enctype="multipart/form-data">图片：<input type="file" name="pfile" id="pfile" style="cursor:pointer;*padding-top:2px;" onchange="user.upfile=\'\';"/><input type="checkbox" name="vote" id="vote" value="1" checked />参加评选</form>');
	$("#uploadpic").show();
}
//page
function page(t,g,p,c){
	Json("api/index.php?type="+t+"&gid="+g+"&page="+p+"&ran="+Math.random(),function(data){
	show(data)
	});
	return false;
}
//view
function show(data){
	var mobj=data.mlist,mol=data.mlist.length,robj=data.rlist,pobj=data.page;
	var htmlstr=[],htmlstr1=[],uname,ctxt,ctime,allowreply,pagenum_s,pagenum_e;
	var _regex = function(str,rep){return str.replace(/\[em:(\d+):]/gi,rep).replace(/[\t\s]/gi,rep);};
	var tmp1,tmp2,tmp3;
	for(var i=0;i<mol;i++){
		allowreply = true;
		tmp1 = mobj[i];
		uname = tmp1.anony ? (tmp1.from || "匿名")+"网友" : tmp1.tmp || tmp1.nick || (tmp1.from || "匿名")+"网友";
		ctxt = _regex(tmp1.content,"");
		htmlstr.push("<div class=\"row\">");
		htmlstr.push("<h3><span>" + uname + "</span>");
		if(tmp1.star)htmlstr.push("<span class=\"star\">"+star(tmp1.star)+"</span>");
		htmlstr.push("<label>"+tmp1.time+"</label></h3>");
		htmlstr.push("<div class=\"con\">");
		//reply_S
		if(tmp1.reply){
			tmp2 = tmp1.reply.split(",");
			for(var x=0;x<tmp2.length;x++){if(robj[tmp2[x]]){htmlstr.push("<div class=\"reply\">");}}
			for(var y=0,j=tmp2.length-1;y<tmp2.length;y++,j--){
				tmp3 = robj[tmp2[j]];
				if(tmp3){
					htmlstr.push("<h4><span>");
					htmlstr.push(tmp3.anony ? (tmp3.from || "匿名")+"网友" : tmp3.tmp || tmp3.nick || (tmp3.from || "匿名")+"网友");
					htmlstr.push("</span><label>"+(y)+"</label></h4>");
					htmlstr.push("<p>");
					if(tmp3.pic && parseInt(tmp3.check))htmlstr.push("<img src=\"upload/"+tmp3.pic+"\" /><br/>");
					else if(tmp3.pic)htmlstr.push("<span style=\"color:#f00\">[图片审核中]</span><br/>");
					htmlstr.push(tmp3.content.replace(/\[em:(\d+):\]/gi,"<img src=\"images/cmt/$1.gif\" />").replace(/[\r\n]{1,2}/gi,"<br />"));
					htmlstr.push("</p></div>");
				}
			}
			if(tmp2.length>=20)allowreply=false;
		}
		//reply_E
		htmlstr.push("<div class=\"mycon\">");
		if(tmp1.pic && parseInt(tmp1.check)){
			htmlstr.push("<img src=\"upload/"+tmp1.pic+"\" />");
			htmlstr.push(tmp1.allow ? "<span class=\"flower\" onclick=\"clk(this,"+tmp1.cmid+","+tmp1.vote+",4);\">献花["+tmp1.vote.toString()+"]</span><br/>" : "<br/>");
		}
		else if(tmp1.pic)htmlstr.push("<span style=\"color:#f00\">[图片审核中]</span><br/>");
		htmlstr.push(tmp1.content.replace(/\[em:(\d+):]/gi,"<img src=\"images/cmt/$1.gif\" />").replace(/[\r\n]{1,2}/gi,"<br />"));
		htmlstr.push("</div>");
		htmlstr.push("</div>");
		htmlstr.push("<div class=\"menu\">");
		htmlstr.push("<a href=\"#\" onclick=\"return clk(this,"+tmp1.cmid+","+tmp1.aginst+",3);\" class=\"item3\">反对[-"+tmp1.aginst+"]</a>");
		htmlstr.push("<a href=\"#\" onclick=\"return clk(this,"+tmp1.cmid+","+tmp1.agree+",2);\" class=\"item2\">同意[+"+tmp1.agree+"]</a>");
		if(allowreply)htmlstr.push("<a href=\"#cmt\" onclick=\"reply("+tmp1.cmid+",'"+ctxt.substring(0,20)+"');\" class=\"item1\">回复</a>");
		htmlstr.push("</div>");
		htmlstr.push("</div>");
	}
	//page
	if(pobj.count){
		pagenum_s = Math.floor((parseInt(pobj.page)-1)/10)*10+1;
		pagenum_e = pagenum_s + 9;
		if(pobj.page != 1)htmlstr1.push("<a href=\"#\" onclick=\"return page("+param.type+","+param.gid+",1);\">&#8249;&#8249; 第一页</a><a href=\"#\" onclick=\"return page("+param.type+","+param.gid+","+(pobj.page-1)+");\">&#8249; 上一页</a>");
		//if(pagenum_s >= 11)htmlstr.push("<a href=\"#\" onclick=\"page("+param.type+","+param.gid+","+(pagenum_e-19)+");\" class=\"non\">...</a>");
		for(var z = pagenum_s;z<=(pobj.count>pagenum_e?pagenum_e:pobj.count);z++){
			if(z==pobj.page)htmlstr1.push("<span>"+z+"</span>");
			else htmlstr1.push("<a href=\"#\" onclick=\"return page("+param.type+","+param.gid+","+z+");\">"+z+"</a>");
		}
		//if(pagenum_e<pobj.count)htmlstr.push("<a href=\"#\" onclick=\"page("+param.type+","+param.gid+","+(pagenum_e+1)+");\" class=\"non\">...</a>");
		if(pobj.page !== pobj.count)htmlstr1.push("<a href=\"#\" onclick=\"return page("+param.type+","+param.gid+","+(pobj.page+1)+");\">下一页 &#8250;</a><a href=\"#\" onclick=\"return page("+param.type+","+param.gid+","+pobj.count+");\">最后页 &#8250;&#8250;</a>");
		$("#pager").html(htmlstr1.join(""));
	}
	//write
	$("#comment").html(htmlstr.join(""));
	resizeiframe();
}


function ShowMsg(msg)
{
	var _div = document.getElementById("comment");
	_div.innerHTML = msg;
}




//face
$("#face img").each(function (c,i){c.onclick=function(){insertFace(i+1);}});
function insertFace(num) {
	var reg = /\[em:(.+?):]/gi;
	var texts = "[em:"+num+":]";
	var obj = document.getElementById("talkwhat");
	var pos=obj.value.match(reg);
	if(pos != null && pos.length>2){alert("添加表情不能超过3个");return false;}
	$("#talkwhat").focus();
	if(typeof (obj.selectionStart) != 'undefined') {
		var opn = obj.selectionEnd + 0;
		obj.value = obj.value.substr(0, obj.selectionEnd) + texts + obj.value.substr(obj.selectionEnd);
		obj.selectionStart = opn + texts.length;
		obj.selectionEnd = obj.selectionStart;
	} else if(document.selection && document.selection.createRange) {
		var sel = document.selection.createRange();
		sel.collapse(false);
		sel.text = texts;
		sel.select();
	} else {
		obj.value += texts;
	}
}

//userrank
function star(n){
	var starlist = "";
	var n1 = 0, n2 = 0;
	var a = new Array([81,27,9,3,1],["<img src=\"images/cmt/diadem.gif\" />","<img src=\"images/cmt/diamond.gif\" />","<img src=\"images/cmt/sun.gif\" />","<img src=\"images/cmt/moon.gif\" />","<img src=\"images/cmt/star.gif\" />"]);
	n = Math.floor(n/50)+1;
	for(var b=0;b<a[0].length;b++){
		n = n-n2;
		n1 = Math.floor(n/a[0][b]);
		n2 = n1 * a[0][b]; 
		if(n1>0){
			for(var i=1;i<=n1;i++){
				starlist += a[1][b];
			}
		}
	}
	return starlist;
}
function reply(cmid,cmcon){
	$("#cparent").val(cmid);
	$("#editor").show();
	$("#cancel").show();
	$("#cancel").html("回复："+cmcon+"&nbsp;&nbsp;<a href=\"#\" onclick=\"$('#cancel').html('');$('#cparent').val(0);$('#cancel').hide();return false;\">取消</a>");
}
function clk(o,i,n,t){
	if(typeof o.num == "undefined")o.num = n;
	o.num = parseInt(o.num)+1;
	o.innerHTML = (t==2)?"已同意[+"+o.num+"]":(t==3)?"已反对[-"+o.num+"]":"已献花["+o.num+"]";
	$(o).removeAttr("onclick");
	$(o).bind("click",function(e){return false;});
	/*$.get("api/send.asp",{"gid":i,"action":t,"ran":Math.random()});*/
	(new Image()).src='api/send.php?gid='+i+'&action='+t+'&ran='+Math.random();
	return false;
}
function getcaptcha(){
	var uri = "../include/vdimgck.php?r="+Math.random();;
	$("#gcaptcha").val("");
	$('#gcaptcha').focus();
	$("#getcode").css('display','inline');
	$("#codeimg").html("<img src=\""+uri+"\" align=\"top\" />");
	return false;
}
//submit
function submitform(){
	var captchastr = trim($("#gcaptcha").val()),reg = /[\u4e00-\u9fa5]/,str=trim($("#talkwhat").val());
	if(parseInt(param.iscaptcha)=='1'){
		if(!captchastr){alert("验证码不能为空！");return false;}
	}
	if(!str){alert("评论内容不能为空！");return false;}
	if(str.length > 255){alert("评论内容不能超过255个字符！");return false;}
	if(!reg.test(str)){alert("评论内容请包含中文！");return false;}
	else $("#captcha").val($("#gcaptcha").val());
	submit_2(user.upfile || "");
}
function submit_2(path){
	var ay=$("#anonymous").attr("checked")
	$("#pvote").val($("#vote").attr("checked")?1:0);
	$("#ppath").val(path || "");
	$("#utmpname").val(ay ? $("#tmpname").val() : user.name);
	$("#anony").val(ay ? 1 : 0);
	//$("#submit1").attr("disabled","disabled");
	$("#form2").el.submit();
}
function success(url){
	alert("评论成功！");
	if(parseInt(param.allowup) === 1)viewform1();
	$("#ppath").val("");
	$("#cancel").hide();
	$("#cparent").val(0);
	$("#cancel").html("");
	$("#talkwhat").val("");
	$("#gcaptcha").val("");
	$("#getcode").hide();
	$("#submit1").attr("value"," 发表评论 ");
	//$("#submit1").attr("disabled","");
	user.upfile='';
	page(param.type,param.gid,1);
}
function cerr(str){
	$("#getcode").hide();
	$("#submit1").attr("value"," 发表评论 ");
	$("#submit1").attr("disabled","");
	alert(str);
}
function onSendBack(ok,filename,msg){
	if(ok==true){
		if(filename==""){
			success();
		}else{
			user.upfile=filename;submit_2(filename);
		}
	}else{
		cerr(msg);
	}
}