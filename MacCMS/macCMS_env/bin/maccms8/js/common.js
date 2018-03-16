
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
