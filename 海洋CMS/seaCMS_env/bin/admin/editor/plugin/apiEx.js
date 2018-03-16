(function(TQE){
if(!TQE){ throw('必须先加载 TQEditor.js '); return ;}
var pe=TQE.prototype;

//接口, 取得编辑器的所有图片,返值为数组['image_1_src_url','image_2_src_url']
//参数 full_uri 是否是取完整的uri
pe.images=function(full_uri){
	var $=this, result=[],htm='',doc=$._getDoc(),a,i;
	if('code'===$.currentMode()){
		return result;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('IMG');
	for(i=0;i<a.length;i++){
		result.push(full_uri ? a[i].src : a[i].getAttribute('src'));
	}
	return result;
};
//接口, 取得编辑器的所有flash,返值为数组
pe.flashs=function(){
	var $=this, result=[],htm='',doc=$._getDoc(),a,i;
	if('code'===$.currentMode()){
		return result;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('EMBED');
	for(i=0;i<a.length;i++){
		if(a[i].getAttribute('type').toLowerCase().indexOf('shockwave-flash')<0 ||
			a[i].getAttribute('flashvars').toLowerCase().indexOf('.flv')) continue;
		result.push(full_uri ? a[i].src : a[i].getAttribute('src'));
	}
	return result;
};
//接口, 取得编辑器的所有flv,返值为数组
pe.flvs=function(){
	var $=this, result=[],htm='',doc=$._getDoc(),a,i;
	if('code'===$.currentMode()){
		return result;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('EMBED');
	for(i=0;i<a.length;i++){
		if(a[i].getAttribute('flashvars').toLowerCase().indexOf('vcastr_file')<0) continue;
		/vcastr_file=([^\"& ]+)/i.exec(a[i].getAttribute('flashvars'));
		if(RegExp.$1)result.push(RegExp.$1);
	}
	return result;
};
//删除所有链接, 参数是回调函数, 返值表示是否允许删除
pe.removeLinks=function(callback){
	var $=this, doc=$._getDoc(),a,i,r;
	if('code'===$.currentMode()){
		return ;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('A');
	if('function'===typeof callback){
		for(i=a.length-1;i>=0;i--){
			r=callback(a[i].getAttribute('href'))
			if(r)TQE.removeNode(a[i], false);
		}
	}else{
		for(i=a.length-1;i>=0;i--){
			TQE.removeNode(a[i], false);
		}
	}
	return ;
};
//删除所有图片, 参数是回调函数, 返值表示是否允许删除
pe.removeImages=function(callback){
	var $=this, doc=$._getDoc(),a,i,r;
	if('code'===$.currentMode()){
		return ;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('IMG');
	if('function'===typeof callback){
		for(i=a.length-1;i>=0;i--){
			r=callback(a[i].getAttribute('src'))
			if(r)TQE.removeNode(a[i], false);
		}
	}else{
		for(i=a.length-1;i>=0;i--){
			TQE.removeNode(a[i], false);
		}
	}
	return ;
};
//删除所有图片, 参数是回调函数, 返值表示是否允许删除
pe.removeFlashs=function(callback){
	var $=this, doc=$._getDoc(),a,i,r;
	if('code'===$.currentMode()){
		return ;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('EMBED');
	if('function'===typeof callback){
		for(i=a.length-1;i>=0;i--){
			r=callback(a[i].getAttribute('src'))
			if(r)TQE.removeNode(a[i], false);
		}
	}else{
		for(i=a.length-1;i>=0;i--){
			TQE.removeNode(a[i], false);
		}
	}
	return ;
};
//删除所有Object对象
pe.removeObjects=function(){
	var $=this, doc=$._getDoc(),a,i,r;
	if('code'===$.currentMode()){
		return ;
		//htm=$.content();
	}
	a=doc.getElementsByTagName('OBJECT');
	for(i=a.length-1;i>=0;i--){
		TQE.removeNode(a[i], false);
	}
};

})(window.TQE);