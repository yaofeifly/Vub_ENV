function $(id){
	return document.getElementById(id)	
}

function focusLogin(){
	formsearch.input_name.focus();	
}

function getMenu(key)
{
	if(key=='common')
		return "首页";
	if(key=='content')
		return "数据";
	if(key=='template')
		return "模板";
	if(key=='make')
		return "生成";
	if(key=='tool')
		return "工具";
	if(key=='ads')
		return "广告";
	if(key=='gathersoft')
		return "采集";
	if(key=='webhelper')
		return "括展";
	if(key=='system')
		return "系统";
	if(key=='user')
		return "用户";
	
}

function changeMenu(key, url){
	if(key == 'index' && url == 'index.php'){
		parent.location.href = 'index.php';
		return false;
	}
	//左侧菜单切换操控
	for(var k in topMenus){
		if($('menu_' + topMenus[k])){
			$('menu_' + topMenus[k]).style.display = topMenus[k] == key ? '' : 'none';
		}
	}
	//上部菜单当前样式操控
	var lis = $('topmenu').getElementsByTagName('li');
	for(var i = 0; i < lis.length; i++){
		if(lis[i].className == 'navon') lis[i].className = '';
	}
	$('header_' + key).parentNode.parentNode.className = 'navon';
	//左侧菜当前链接显示操控
	if(url){
		parent.I2.location = url;
		var hrefs = $('menu_' + key).getElementsByTagName('a');
		for(var j = 0; j < hrefs.length; j++){
			hrefs[j].className=hrefs[j].href.indexOf(url)!=-1? 'menucurr' : (hrefs[j].className == 'menucurr' ? '' : hrefs[j].className);
		}
	}
	//导航
	if(parent.$('admincpnav')) parent.$('admincpnav').innerHTML='后台首页&nbsp;&raquo;&nbsp;'+ getMenu(key) ;

	
	
	return false;
}

function update_nav(val)
{
	
	if(val!='')
		{
				if(parent.$('admincpnav')) parent.$('admincpnav').innerHTML= parent.$('admincpnav').innerHTML + '&nbsp;&raquo;&nbsp;' + val;
		}
			
}

function initMenu(menuContainerid){
	var key = '';
	var hrefs = $(menuContainerid).getElementsByTagName('a');
	for(var i = 0; i < hrefs.length; i++){			
		//初始化默认当前左链接
		if(menuContainerid == 'leftmenu' && !key){
			key = hrefs[i].parentNode.parentNode.id.substr(5);
			hrefs[i].className = 'menucurr';
		}
		//左侧点击切换显示
		hrefs[i].onclick = function(){					
			var lis = $(menuContainerid).getElementsByTagName('li');
			for(var k = 0; k < lis.length; k++){
				if(lis[k].firstChild!=null)
					lis[k].firstChild.className = '';
			}
			if(this.className == '') this.className = menuContainerid == 'leftmenu' ? 'menucurr' : 'bold';
		}
	}
	return key;
}