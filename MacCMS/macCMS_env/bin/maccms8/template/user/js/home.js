
String.prototype.replaceAll  = function(s1,s2){ return this.replace(new RegExp(s1,"gm"),s2); }
String.prototype.trim=function(){ return this.replace(/(^\s*)|(\s*$)/g, ""); }
function pagego($url,$total){
	$page=$('#page').val();
	if($page>0&&($page<=$total)){
		if($page>1 || document.URL.indexOf('?')>-1){
			$url=$url.replace('{pg}',$page);
		}
		else{
			if($page==1){
				$url=$url.replace('-{pg}','').replace('{pg}','');
			}
		}
		location.href=$url;
	}
	return false;
}

var MAC={
	'Url': document.URL,
	'Title': document.title,
	'Copy': function(s){
		if (window.clipboardData){ window.clipboardData.setData("Text",s); } 
		else{
			if( $("#mac_flash_copy").get(0) ==undefined ){ $('<div id="mac_flash_copy"></div>'); } else {$('#mac_flash_copy').html(''); }
			$('#mac_flash_copy').html('<embed src='+SitePath+'"images/_clipboard.swf" FlashVars="clipboard='+escape(s)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>');
		}
		alert("复制成功");
	},
	'Home': function(o,u){
		try{
	    	o.style.behavior='url(#default#homepage)'; o.setHomePage(u);
		}
	    catch(e){
	         if(window.netscape){
	         	try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
	        	catch(e){alert("此操作被浏览器拒绝！请手动设置");}
	            var moz = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
	            moz.setCharPref('browser.startup.homepage',u);
	          }
	     }
	},
	'Fav': function(u,s){
		try{ window.external.addFavorite(u, s);}
		catch (e){
			try{window.sidebar.addPanel(s, u, "");}catch (e){alert("加入收藏出错，请使用键盘Ctrl+D进行添加");}
		}
	},
	'Open': function(u,w,h){
		window.open(u,'macopen1','toolbars=0, scrollbars=0, location=0, statusbars=0,menubars=0,resizable=yes,width='+w+',height='+h+'');
	},
	'Cookie': {
		'Set': function(name,value,days){
			var exp = new Date();
			exp.setTime(exp.getTime() + days*24*60*60*1000);
			var arr=document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			document.cookie=name+"="+escape(value)+";path=/;expires="+exp.toUTCString();
		},
		'Get': function(name){
			var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
			if(arr != null){ return unescape(arr[2]); return null; }
		},
		'Del': function(name){
			var exp = new Date();
			exp.setTime(exp.getTime()-1);
			var cval = this.Get(name);
			if(cval != null){ document.cookie = name+"="+escape(cval)+";path=/;expires="+exp.toUTCString(); }
		}
	},
	'Login':{
		'Display': true,
		'Init':function($id){
			if($("#"+$id).length==0){ return; }
			this.Create($id);
			$('#'+$id).hover(function(){
				MAC.Login.Show();
			}, function(){
				MAC.Login.FlagHide();
			});
			$("#loginbtn").click(function(){
				MAC.Login.In();
			});
		},
		'Show': function(){
			$('#login_box').show();
		},
		'Hide': function(){
			$('#login_box').hide();
		},
		'FlagHide': function(){
			$('#login_box').hover(function(){
				MAC.Login.Display = false;
				MAC.Login.Show();
			}, function(){
				MAC.Login.Display = true;
				MAC.Login.Hide();
			});
			if(MAC.Login.Display){
				MAC.Login.Hide();
			}
		},
		'Info':function(){
			$.ajax({type:'get',url: SitePath + 'index.php?m=user-ajaxinfo',dataType:'json',timeout: 5000,
				error: function(){
					alert('登录失败');
				},
				success: function($r){
					$("#username").html($r.u_name);
					$("#userpoint").html($r.u_points);
					$("#groupname").html($r.ug_name);
				}
			});
		},
		'Create':function($id){
			if($("#login_box").length>0){ $("#login_box").remove(); }
			html = '<div class="drop-box login_box" id="login_box" style="display: none;">';
			if(MAC.Cookie.Get('userid')!=undefined && MAC.Cookie.Get('userid')!=''){ 
				html+='<ul class="logged"><li><a target="_blank" href="'+SitePath+'index.php?m=user-index">我的资料</a></li><li class="logout"><a class="logoutbt" href="javascript:;" onclick="MAC.Login.Out(\''+$id+'\');" target="_self"><i class="ui-icon user-logout"></i>退出</a></li></ul>';
			}
			else{
				html+='<form id="loginform" onsubmit="return false;" action="'+SitePath+'index.php?user-check" method="post"><div class="formitem"><label>用户：</label><input name="username" type="text"  class="input" id="username"/></div><div class="formitem"><label>密码：</label><input name="userpass" type="password" class="input" id="userpass"/></div><div class="formitem"><a class="qq-login" href="'+SitePath+'index.php?m=user-reg-ref-qqlogin.html"></a> <input class="formbutton" id="loginbtn" type="submit" value="登 录"></div><div class="formitem"><a title="忘记密码" class="forgotpass" href="'+SitePath+'index.php?m=user-findpass.html">忘记密码?</a>  <a class="reg-btn" href="'+SitePath+'index.php?m=user-reg.html" target="_blank">还没有账号?</a></div></form>';
			}
			
			html += '</div>';
			$('#'+$id).after(html);
			var w = $('#'+$id).width();
			var h = $('#'+$id).height();
			var position = $('#'+$id).position();
			$('#login_box').css({'left':position.left,'top':(position.top+h)});
		},
		'Out':function($id){
			$.ajax({type:'get',url: SitePath + 'index.php?m=user-logout',timeout: 5000,
				success: function($r){
					MAC.Cookie.Set('userid','',0);
					MAC.Login.Create($id);
				}
			});
		},
		'In':function(){
			if($("#username").val()==''){ alert('请输入账户'); }
			if($("#userpass").val()==''){ alert('请输入密码'); }
			$.ajax({type: 'post',url: SitePath + 'index.php?m=user-check', data:'u_name='+$("#username").val()+'&u_password='+$("#userpass").val(),timeout: 5000,
				error: function(){
					alert('登录失败');
				},
				success: function($r){
					if($r.indexOf('您输入')>-1){
						alert('账户或密码错误，请重试!');
					}
					else if($r.indexOf('登录成功')){
						location.href=location.href;
					}
					else{
						
					}
				}
			});
		}
	},
	'History': {
		'Limit':10,
		'Days':7,
		'Json': '',
		'Display': true,
		'List': function($id){
			if($("#"+$id).length==0){ return; }
			this.Create($id);
			$('#'+$id).hover(function(){
				MAC.History.Show();
			}, function(){
				MAC.History.FlagHide();
			});
		},
		'Clear': function(){
			MAC.Cookie.Del('mac_history');
			$('#history_box').html('<li class="hx_clear">已清空观看记录。</li>');
		},	
		'Show': function(){
			$('#history_box').show();
		},
		'Hide': function(){
			$('#history_box').hide();
		},
		'FlagHide': function(){
			$('#history_box').hover(function(){
				MAC.History.Display = false;
				MAC.History.Show();
			}, function(){
				MAC.History.Display = true;
				MAC.History.Hide();
			});
			if(MAC.History.Display){
				MAC.History.Hide();
			}
		},
		'Create': function($id){
			var jsondata = [];
			if(this.Json){
				jsondata = this.Json;
			}else{
				var jsonstr = MAC.Cookie.Get('mac_history');
				if(jsonstr != undefined){
					jsondata = eval(jsonstr);
				}
			}
			html = '<dl class="drop-box history_box" id="history_box" style="display:none;position:absolute;">';
			html +='<dt><a target="_self" href="javascript:void(0)" onclick="MAC.History.Clear();">清空</a> | <a target="_self" href="javascript:void(0)" onclick="MAC.History.Hide();">关闭</a></dt>';
			if(jsondata.length > 0){
				for($i=0; $i<jsondata.length; $i++){
					if($i%2==1){
						html +='<dd class="odd">';
					}else{
						html +='<dd class="even">';
					}
					html +='<a href="'+jsondata[$i].link+'" class="hx_title">'+jsondata[$i].name+'</a></dd>';
				}
			}else{
				html +='<dd class="hide">暂无观看记录</dd>';
			}
			html += '</dl>';
			$('#'+$id).after(html);
			
			var w = $('#'+$id).width();
			var h = $('#'+$id).height();
			var position = $('#'+$id).position();
			$('#history_box').css({'left':position.left,'top':(position.top+h)});
		},	
		'Insert': function(name,link,typename,typelink,pic){
			var jsondata = MAC.Cookie.Get('mac_history');
			if(jsondata != undefined){
				this.Json = eval(jsondata);
				for($i=0;$i<this.Json.length;$i++){
					if(this.Json[$i].link == link){
						return false;
					}
				}
				if(!link){ link = document.URL; }
				jsonstr = '{video:[{"name":"'+name+'","link":"'+link+'","typename":"'+typename+'","typelink":"'+typelink+'","pic":"'+pic+'"},';
				for($i=0; $i<=this.Limit; $i++){
					if(this.Json[$i]){
						jsonstr += '{"name":"'+this.Json[$i].name+'","link":"'+this.Json[$i].link+'","typename":"'+this.Json[$i].typename+'","typelink":"'+this.Json[$i].typelink+'","pic":"'+this.Json[$i].pic+'"},';
					}else{
						break;
					}
				}
				jsonstr = jsonstr.substring(0,jsonstr.lastIndexOf(','));
				jsonstr += "]}";
			}else{
				jsonstr = '{video:[{"name":"'+name+'","link":"'+link+'","typename":"'+typename+'","typelink":"'+typelink+'","pic":"'+pic+'"}]}';
			}
			this.Json = eval(jsonstr);
			MAC.Cookie.Set('mac_history',jsonstr,this.Days);
		}
	},
	'Suggest': {
		'Show': function($id,$limit,$ajaxurl,$jumpurl){
			try{
			$("#"+$id).autocomplete(SitePath+$ajaxurl,{
				width: 175,scrollHeight: 300,minChars: 1,matchSubset: 1,max: $limit,cacheLength: 10,multiple: true,matchContains: true,autoFill: false,dataType: "json",
				parse:function(obj) {
					if(obj.status){
						var parsed = [];
						for (var i = 0; i < obj.data.length; i++) {
							parsed[i] = {
								data: obj.data[i],value: obj.data[i].d_name,result: obj.data[i].d_name
							};
						}
						return parsed;
					}else{
						return {data:'',value:'',result:''};
					}
				},
				formatItem: function(row,i,max) {
					return row.d_name;
				},
				formatResult: function(row,i,max) {
					return row.d_name;
				}
			}).result(function(event, data, formatted) {
				location.href = SitePath+ $jumpurl + encodeURIComponent(data.d_name);
			});
			}catch(e){}
		}
	},
	'Score': {
		'ajaxurl': 'inc/ajax.php?ac=score',
		'Show':function($f,$tab,$id){
			var str = '';
			if($f==1){
				str = '<div style="padding:5px 10px;border:1px solid #CCC"><div style="color:#000"><strong>我要评分(感谢参与评分，发表您的观点)</strong></div><div>共 <strong style="font-size:14px;color:red" id="star_count"> 0 </strong> 个人评分， 平均分 <strong style="font-size:14px;color:red" id="star_pjf"> 0 </strong>， 总得分 <strong style="font-size:14px;color:red" id="star_all"> 0 </strong></div><div>';
				for(var i=1;i<=10;i++){ str += '<input type="radio" name="score" id="rating'+i+'" value="1"/><label for="rating'+i+'">'+i+'</label>'; }
				str += '&nbsp;<input type="button" value=" 评 分 " id="scoresend" style="width:55px;height:21px"/></div></div>';
			}
			else{
				str += '<div class="star"><span id="star_tip"></span><ul><li class="star_current"></li>';
				for(var i=1;i<=10;i++){ str += '<li><a value="'+i+'" class="star_'+i+'">'+i+'</a></li>'; }
				str += '</ul>';
				str +='<span id="star_hover"></span><p><span id="star_shi">0</span><span id="star_ge">.0</span><span class="star_no">(已有<label id="star_count">0</label>人评分)</span></p></div>';
			}
			document.write(str);
			$.ajax({type: 'get',url: SitePath + this.ajaxurl + '&tab='+$tab+'&id='+$id,timeout: 5000,
				error: function(){
					alert('评分加载失败');
					$(".star").html('评分加载失败');
				},
				success: function($r){
					MAC.Score.View($r);
					if($f==1){
						$("#scoresend").click(function(){
							var rc=false;
							for(var i=1;i<=10;i++){ if( $('#rating'+i).get(0).checked){ rc=true; break; } }
							if(!rc){alert('你还没选取分数');return;}
							MAC.Score.Send( '&tab='+$tab+'&id='+$id+'&score='+i );
						});
					}
					else{
						
						var tip=new Array("","很差，浪费生命","很差，浪费生命","不喜欢","不喜欢","一般，不妨一看","一般，不妨一看","一般，不妨一看","喜欢，值得推荐","喜欢，值得推荐","非常喜欢，不容错过");
						$(".star>ul>li>a").mouseover(function(){
							$("#star_hover").html($(this).attr('value')+"分 ");
							$("#star_tip").html(tip[$(this).attr('value')]);
							$(".star_current").css("display","none");
						});
						$(".star>ul>li>a").mouseout(function(){
							$("#star_hover").html("");
							$("#star_tip").html("");
							$(".star_current").css("display","block");
						});
						$(".star>ul>li>a").click(function(){
							MAC.Score.Send( '&tab='+$tab+'&id='+$id+'&score='+$(this).attr('value') );
						});
					}
				}
			});
		},
		'Send':function($u){
			$.ajax({type: 'get',url: SitePath + this.ajaxurl + $u,timeout: 5000,
				error: function(){
					$(".star").html('评分加载失败');
				},
				success: function($r){
					if($r=="haved"){
						alert('你已经评过分啦');
					}else{
						MAC.Score.View($r);
					}
				}
			});
		},
		'View':function($r){
			$r = eval('(' + $r + ')');
			$("#rating"+Math.floor($r.score)).attr('checked',true);
			$("#star_count").text( $r.scorenum );
			$("#star_all").text( $r.scoreall );
			$("#star_pjf").text( $r.score );
			$("#star_shi").text( parseInt($r.score) );
			$("#star_ge").text( "." +  $r.score.toString().split('.')[1] );
			$(".star_current").width($r.score*10);
		}
	},
	'Digg': {
		'Show': function($ajaxurl) {
			$('.digg_vodup').click(function(){
				MAC.Digg.Send($ajaxurl,'vod','up');
			});
			$('.digg_voddown').click(function(){
				MAC.Digg.Send($ajaxurl,'vod','down');
			});
			$('.digg_artup').click(function(){
				MAC.Digg.Send($ajaxurl,'art','up');
			});
			$('.digg_artdown').click(function(){
				MAC.Digg.Send($ajaxurl,'art','down');
			});
			
			if($(".digg_vodup").length || $("#digg_voddown").length){
				MAC.Digg.Send($ajaxurl,'vod','');
			}
			if($(".digg_artup").length || $("#digg_artdown").length){
				MAC.Digg.Send($ajaxurl,'art','');
			}
		},	
		'Send': function($ajaxurl,$tab,$ac){
			$.ajax({type: 'get',timeout: 5000, url: SitePath + $ajaxurl + "&tab="+$tab+"&ac2="+$ac ,
				error: function(){
					alert('顶踩数据加载失败');
				},
				success: function($r){
					if($r=="haved"){
						alert('你已经评过分啦');
					}else{
						MAC.Digg.View($tab,$r);
					}
				}
			});
		},
		'View': function ($tab,$r){
			if($tab == 'vod'){
				$(".digg_vodup>span").html($r.split(':')[0]);
				$(".digg_voddown>span").html($r.split(':')[1]);
			}
			else if($tab = 'art'){
				var Digs = $r.split(':');
				var sUp = parseInt(Digs[0]);
				var sDown = parseInt(Digs[1]);
				var sTotal = sUp+sDown;
				var spUp=(sUp/sTotal)*100;
				spUp = Math.round(spUp*10)/10;
				var spDown = 100-spUp;
				spDown = Math.round(spDown*10)/10;
				if(sTotal!=0){
					$('#digg_artup_val').html(sUp);
					$('#digg_artdown_val').html(sDown);
					$('#digg_artup_sp').html(spUp+'%');
					$('#digg_artdown_sp').html(spDown+'%');
					$('#digg_artup_img').width(parseInt((sUp/sTotal)*55));
					$('#digg_artdown_img').width(parseInt((sDown/sTotal)*55));
				}
			}
		}
	},
	'Comment':{
		'Show':function($ajaxurl){
			if($("#comment").length>0){
				if($ajaxurl.indexOf('{pg}')>0){
					$ajaxurl = $ajaxurl.replace('{pg}',$("#page").val() );
				};
				$.ajax({
					type: 'get',
					url: $ajaxurl,
					timeout: 5000,
					error: function(){
						$("#comment").html('评论加载失败，请刷新...');
					},
					success:function($r){
						$("#comment").html($r);
					}
				});
			}
		},
		'Post':function(){
			if($("#c_content").val() == '请在这里发表您的个人看法，最多200个字。'){
				alert('请发表您的评论观点！');
				return false;
			}
			$.ajax({
				type: 'post',
				url: SitePath+'index.php?m=comment-save',
				data: "vid="+SiteId+"&aid="+SiteAid+"&c_name="+$("#c_name").val()+"&c_content="+$("#c_content").val()+"&c_code="+$("#c_code").val(),
				success:function($r){
					if($r == 'ok'){
						MAC.Comment.Show(SitePath+'index.php?m=comment-show-aid-'+SiteAid+'-vid-'+SiteId);
					}
					else{
						alert($r);
					}
					$("#c_code_img").attr("src",SitePath+'inc/common/code.php?a=comment&s='+Math.random());
				}
			});
		},
		'Reply':function($id){
			$("#c_rid").val($id);
			window.scrollTo(0, $("#c_content").offset().top-30);
		}
	},
	'Lazyload':{
		'Show': function(){
			$("img.lazy").lazyload();
			try {  }catch(e){};
		},
		'Box': function($id){
			$("img.lazy").lazyload({
				 container: $("#"+$id)
			});	
		}
	},
	'Desktop':function(s){
		location.href= SitePath + "inc/ajax.php?ac=desktop&name="+encodeURI(s)+"&url=" + encodeURI(location.href);
	},
	'Timming':function(){
		var t=(new Image());t.src=SitePath+'inc/timming.php?t='+Math.random();
	},
	'Hits':function(tab,id){
		$.get(SitePath+"inc/ajax.php?ac=hits&tab="+tab+"&id="+id,function(r){$('#hits').html(r);});
	},
	'Error':function(tab,id,name){
		MAC.Open(SitePath+"inc/err.html?tab="+tab+"&id="+id+"&name="+ encodeURI(name),400,220);
	},
	'UserFav':function(id){
		$.get(SitePath+"inc/ajax.php?ac=userfav&id="+id+"&rnd="+Math.random(),function(r){
			if(r=="ok"){ alert("会员收藏成功"); }
			else if(r=="login"){ alert('请先登录会员中心再进行会员收藏操作'); }
			else if(r=="haved"){ alert('您已经收藏过了'); }
			else{ alert('发生错误'); }
		});
	},
	'AddEm':function(obj,i){
		var oldtext = $('#'+obj).val();
 		$('#'+obj).val( oldtext + '[em:' + i +']' );
	},
	'Remaining':function(obj,len,spid){
		var count = len - $(obj).val().length;
		if(count < 0){ 
			count = 0;
			$(obj).val($(obj).val().substr(0,200));
		}
		$('#'+spid).text(count);
	},
	'AdsWrap':function(w,h,n){
		document.writeln('<img width="'+w+'" height="'+h+'" alt="'+n+'" style="background-color: #CCCCCC" />');
	},
	'Js':function(){
	
	}
}

$(function(){
	//异步加载图片初始化
	MAC.Lazyload.Show();
	//历史记录初始化
	MAC.History.List('history');
	//用户登录初始化
	MAC.Login.Init('login');
	//搜索联想初始化
	MAC.Suggest.Show('wd',10,'inc/ajax.php?ac=suggest&aid='+SiteAid,'index.php?m=vod-search-wd-');
	//顶踩初始化
	MAC.Digg.Show('inc/ajax.php?ac=digg&aid='+SiteAid+'&id='+SiteId);
	//ajax评论初始化
	//MAC.Comment.Show('index.php?m=comment-show-aid-'+SiteAid+'-vid-'+SiteId);
	//定时任务初始化
	MAC.Timming();
});
