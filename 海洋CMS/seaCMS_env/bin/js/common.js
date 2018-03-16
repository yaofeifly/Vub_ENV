function AJAX(G) {
	var K = [],
	$ = this,
	L = AJAX.__pool__ || (AJAX.__pool__ = []); (function(E) {
		var D = function() {};
		E = E ? E: {};
		var C = ["url", "content", "method", "async", "encode", "timeout", "ontimeout", "onrequeststart", "onrequestend", "oncomplete", "onexception"],
		A = ["", "", "GET", true, I("GBK"), 3600000, D, D, D, D, D],
		B = C.length;
		while (B--) $[C[B]] = _(E[C[B]], A[B]);
		if (!N()) return false;
	})(G);
	function _(_, $) {
		return _ != undefined ? _: $
	}
	function N() {
		var A, $ = [window.XMLHttpRequest, "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];
		for (var B = 0; B < L.length; B += 1) if (L[B].readyState == 0 || L[B].readyState == 4) return L[B];
		for (B = 0; B < $.length; B += 1) {
			try {
				if (window.XMLHttpRequest) {
					A = ($[B] && typeof($[B]) == "function" ? new $[B] : new XMLHttpRequest($[B]));
				}else
				{
					A = ($[B] && typeof($[B]) == "function" ? new $[B] : new ActiveXObject($[B]));
				}	
				break
			} catch(_) {
				A = false;
				continue
			}
		}
		if (!A) {
			throw "Cannot init XMLHttpRequest object!";
			return false
		} else {
			L[L.length] = A;
			
			return A
		}
	}
	function E($) {
		return document.getElementById($)
	}
	function C($) {
		var _ = $ * 1;
		return (isNaN(_) ? 0 : _)
	}
	function D($) {
		return (typeof($) == "string" ? ($ = E($)) ? $: false: $)
	}
	function F() {
		return ((new Date) * 1)
	}
	function M($, _) {
		K[$ + ""] = _
	}
	function H($) {
		return (K[$ + ""])
	}
	function J(_, $, B) {
		return (function A(C) {
			C = C.replace(/([^\u0080-\u00FF]+)/g,
			function($0, $1) {
				return _($1)
			}).replace(/([\u0080-\u00FF])/g,
			function($0, $1) {
				return escape($1).replace("%", "%u00")
			});
			for (var E = 0,
			D = $.length; E < D; E += 1) C = C.replace($[E], B[E]);
			return (C)
		})
	}
	function I($) {
		if ($.toUpperCase() == "UTF-8") return (encodeURIComponent);
		else return (J(escape, [/\+/g], ["%2B"]))
	}
	function O(A, B) {
		if (!A.nodeName) return;
		var _ = "|" + A.nodeName.toUpperCase() + "|";
		if ("|INPUT|TEXTAREA|OPTION|".indexOf(_) > -1) A.value = B;
		else {
			try {
				A.innerHTML = B
			} catch($) {}
		}
	}
	function P(_) {
		if (typeof(_) == "function") return _;
		else {
			_ = D(_);
			if (_) return (function($) {
				O(_, $.responseText)
			});
			else return $.oncomplete
		}
	}
	function B(_, A, $) {
		var C = 0,
		B = [];
		while (C < _.length) {
			B[C] = _[C] ? ($[C] ? $[C](_[C]) : _[C]) : A[C];
			C += 1
		}
		while (C < A.length) {
			B[C] = A[C];
			C += 1
		}
		return B
	}
	function A() {
		var E, C = false,
		K = N(),
		J = B(arguments, [$.url, $.content, $.oncomplete, $.method, $.async, null], [null, null, P, null, null, null]),
		G = J[0],
		I = J[1],
		L = J[2],
		M = J[3],
		H = J[4],
		A = J[5],
		O = M.toUpperCase() == "POST" ? true: false;
		if (!G) {
			throw "url is null";
			return false
		}
		var _ = {
			url: G,
			content: I,
			method: M,
			params: A
		};
		if (!O) G += (G.indexOf("?") > -1 ? "&": "?") + "timestamp=" + F();
		K.open(M, G, H);
		$.onrequeststart(_);
		if (O) K.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		K.setRequestHeader("X-Request-With", "XMLHttpRequest");
		E = setTimeout(function() {
			C = true;
			K.abort()
		},
		$.timeout);
		var D = function() {
			if (C) {
				$.ontimeout(_);
				$.onrequestend(_)
			} else if (K.readyState == 4) {
				clearTimeout(E);
				_.status = K.status;
				try {
					if (K.status == 200) L(K, A);
					else $.onexception(_)
				} catch(B) {
					$.onexception(_)
				}
				$.onrequestend(_)
			}
		};
		K.onreadystatechange = D;
		if (O) K.send(I);
		else K.send("");
		if (H == false) D();
		return true
	}
	this.setcharset = function(_) {
		if (!$.encode) $.encode = I(_)
	};
	this.get = function(C, B, _) {
		return A(C, "", B, "GET", $.async, _)
	};
	this.update = function(H, J, _, D, E) {
		_ = C(_);
		D = C(D);
		if (_ < 1) D = 1;
		var B = function() {
			A(J, "", H, "GET", $.async, E)
		},
		G = F(),
		I = function($) {
			B();
			$--;
			if ($ > 0) M(G, setTimeout(function() {
				I($)
			},
			_))
		};
		I(D);
		return G
	};
	this.stopupdate = function($) {
		clearTimeout(H($))
	};
	this.post = function(D, _, C, B) {
		return A(D, _, C, "POST", $.async, B)
	};
	this.postf = function(O, J, B) {
		var H = [],
		L,
		_,
		G,
		I,
		M,
		K = arguments.length,
		C = arguments;
		O = O ? D(O) : false;
		if (!O || O.nodeName != "FORM") return false;
		validfoo = O.getAttribute("onvalidate");
		validfoo = validfoo ? (typeof(validfoo) == "string" ? new Function(validfoo) : validfoo) : null;
		if (validfoo && !validfoo()) return false;
		var E = O.getAttribute("action"),
		N = O.getAttribute("method"),
		F = $.formToStr(O);
		if (F.length == 0) return false;
		if (N.toUpperCase() == "POST") return A(E, F, J, "POST", true, B);
		else {
			E += (E.indexOf("?") > -1 ? "&": "?") + F;
			return A(E, "", J, "GET", true, B)
		}
	};
	this.formToStr = function(C) {
		var B = "",
		E = "",
		_, A;
		for (var D = 0; D < C.length; D += 1) {
			_ = C[D];
			if (_.name != "") {
				switch (_.type) {
				case "select-one":
					if (_.selectedIndex > -1) A = _.options[_.selectedIndex].value;
					else A = "";
					break;
				case "checkbox":
				case "radio":
					if (_.checked == true) A = _.value;
					break;
				default:
					A = _.value
				}
				A = $.encode(A);
				B += E + _.name + "=" + A;
				E = "&"
			}
		}
		return B
	}
}
//-------------------------------------------------------


function checkAll(bool,tagname,name)
{
	var checkboxArray;checkboxArray=getElementsByName(tagname,name)
	for (var i=0;i<checkboxArray.length;i++){checkboxArray[i].checked = bool;}
}

function checkOthers(tagname,name)
{
	var checkboxArray;checkboxArray=getElementsByName(tagname,name)
	for (var i=0;i<checkboxArray.length;i++){
		if (checkboxArray[i].checked == false){
			checkboxArray[i].checked = true;
		}else if (checkboxArray[i].checked == true){
			checkboxArray[i].checked = false;
		}
	}
}

function textareasize(obj){
	if(obj.scrollHeight > 70){
		obj.style.height = obj.scrollHeight + 'px';
	}
}

function set(obj,value){
	obj.innerHTML = value
}

function view(id){
	document.getElementById(id).style.display='inline'	
}

function hide(id){
	document.getElementById(id).style.display='none'		
}

function getScroll(){var t;if(document.documentElement&&document.documentElement.scrollTop){t=document.documentElement.scrollTop;}else if(document.body){t=document.body.scrollTop;}return(t);} 


function HtmlEncode(str)
{   
	 var s = "";
	 if(str.length == 0) return "";
	 s    =    str.replace(/&/g,"&amp;");
	 s    =    s.replace(/</g,"&lt;");
	 s    =    s.replace(/>/g,"&gt;");
	 s    =    s.replace(/ /g,"&nbsp;");
	 s    =    s.replace(/\'/g,"&#39;");
	 s    =    s.replace(/\"/g,"&quot;"); 
	 return   s;   
}  

function getElementsByName(tag,name){
    var rtArr=new Array();
    var el=document.getElementsByTagName(tag);
    for(var i=0;i<el.length;i++){
        if(el[i].name==name)
              rtArr.push(el[i]);
    }
    return rtArr;
}

function closeWin(){
	document.body.removeChild(document.getElementById("bg")); 
	document.body.removeChild(document.getElementById("msg"));
	if(document.getElementById("searchtype"))document.getElementById("searchtype").style.display="";
}

function openWindow(zindex,width,height,alpha){
	var iWidth = document.documentElement.scrollWidth; 
	var iHeight = document.documentElement.clientHeight; 
	var bgDiv = document.createElement("div");
	bgDiv.id="bg";
	bgDiv.style.cssText = "top:0;width:"+iWidth+"px;height:"+document.documentElement.scrollHeight+"px;filter:Alpha(Opacity="+alpha+");opacity:0.3;z-index:"+zindex+";";
	document.body.appendChild(bgDiv); 
	var msgDiv=document.createElement("div");
	msgDiv.id="msg";
	msgDiv.style.cssText ="z-index:"+(zindex+1)+";width:"+width+"px; height:"+(parseInt(height)-0+29+16)+"px;left:"+((iWidth-width-2)/2)+"px;top:"+(getScroll()+(height=="auto"?150:(iHeight>(parseInt(height)+29+2+16+30)?(iHeight-height-2-29-16-30)/2:0)))+"px";
	msgDiv.innerHTML="<div class='msgtitle'><div id='msgtitle'></div><img onclick='closeWin()' src='/"+sitePath+"pic/btn_close.gif' /></div><div id='msgbody' style='height:"+height+"px'></div>";
	document.body.appendChild(msgDiv);
}

function openWindow2(zindex,width,height,alpha){
	var iWidth = document.documentElement.scrollWidth; 
	var bgDiv = document.createElement("div");
	bgDiv.id="bg";
	bgDiv.style.cssText = "top:0;width:"+iWidth+"px;height:"+document.documentElement.scrollHeight+"px;filter:Alpha(Opacity="+alpha+");opacity:0.3;z-index:"+zindex+";";
	document.body.appendChild(bgDiv); 
	var msgDiv=document.createElement("div");
	msgDiv.id="msg";
	msgDiv.style.cssText ="position: absolute;z-index:"+(zindex+1)+";width:"+width+"px; height:"+(height=="auto"?height:(height+"px"))+";";
	document.body.appendChild(msgDiv);	
}

function selectTogg(){
	var selects=document.getElementsByTagName("select");
	for(var i=0;i<selects.length;i++){
		selects[i].style.display=(selects[i].style.display=="none"?"":"none");
	}
}
function checkInput(str,type){
	switch(type){
		case "mail":
			if(!/^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/gi.test(str)){alert('邮箱填写错误');return false;}
			break;
		case "num" :
			if(isNaN(str)){alert('QQ填写错误');return false;}
			break;
	}
	return true;
}

function copyToClipboard(txt) {    
	if(window.clipboardData){    
		window.clipboardData.clearData();    
		window.clipboardData.setData("Text", txt);
		alert('复制成功！')
	}else{
		alert('请手动复制！')	
	}   
}
function   getUrlArgs()   
  {   
     return  location.pathname;
  }






//