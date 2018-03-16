var seacms = {
//start
'browser':{//浏览器
	'url': document.URL,
	'title': document.title,
	'language': (navigator.browserLanguage || navigator.language).toLowerCase(),//zh-tw|zh-hk|zh-cn
	'canvas' : function(){
		return !!document.createElement('canvas').getContext;
	}(),
	'useragent' : function(){
		var ua = navigator.userAgent;//navigator.appVersion
		return {
			'mobile': !!ua.match(/AppleWebKit.*Mobile.*/), //是否为移动终端 
			'ios': !!ua.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
			'android': ua.indexOf('Android') > -1 || ua.indexOf('Linux') > -1, //android终端或者uc浏览器 
			'iPhone': ua.indexOf('iPhone') > -1 || ua.indexOf('Mac') > -1, //是否为iPhone或者QQHD浏览器 
			'iPad': ua.indexOf('iPad') > -1, //是否iPad
			'trident': ua.indexOf('Trident') > -1, //IE内核
			'presto': ua.indexOf('Presto') > -1, //opera内核
			'webKit': ua.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
			'gecko': ua.indexOf('Gecko') > -1 && ua.indexOf('KHTML') == -1, //火狐内核 
			'weixin': ua.indexOf('MicroMessenger') > -1 //是否微信 ua.match(/MicroMessenger/i) == "micromessenger",			
		};
	}()
},
'click':{//点击事件
	'more': function(){//ajax翻页
		$("#sea-page-next").bind('click', function(){
			$.get($(this).attr('data-ajaxurl')+(cms.page*1+1), function(data){
				if(data){
					$("#sea-page-box").append(data);// 插入新元素
					cms.page++;// 更新页码
				}else{
					$("#sea-page-next").hide();
					$(this).unbind("click");
				}
			},'html');
		});
	},
	'updown': function(){
		$('body').on('click', 'a.sea-updown', function(e){
			var $this = $(this);
			if($(this).attr("data-id")){
				$.ajax({
					url: cms.root+'index.php?s=updown-'+$(this).attr("data-module")+'-id-'+$(this).attr("data-id")+'-type-'+$(this).attr("data-type"),
					cache: false,
					dataType: 'json',
					success: function(json){
						$this.addClass('disabled');
						if(json.status == 1){
							if($this.attr("data-type")=='up'){
								$this.find('.sea-updown-tips').html(json.data.up);
							}else{
								$this.find('.sea-updown-tips').html(json.data.down);
							}
						}else{
							$this.attr('title', json.info);
							$this.tooltip('show');
						}
					}
				});
			}
		});
	}
},
'submit':{//表单提交事件
	'search': function(){
		$("#sea-search button").on("click",function(){
			$action = $(this).attr('data-action');
			if($action){
				$("#sea-search").attr('action', $action);
			}
		});
		$("#sea-search").on("submit", function(){
			$action = $(this).attr('action');
			if(!$action){
				$action = 'search.php';
			}
			$searchword = $('#sea-search #searchword').val();
			if($searchword){
				location.href = $action.encodeURIComponent($searchword);
			}else{
				$("#searchword").focus();
				$("#searchword").attr('data-toggle','tooltip').attr('data-placement','bottom').attr('title','请输入关键字').tooltip('show');
			}
			return false;
		});
	}
},
'key':{//键盘
	'enter': function(){//回车
		$("#sea-search input").keyup(function(event){
			if(event.keyCode == 13){
				location.href = cms.root+'index.php?s=vod-search-wd-'+encodeURIComponent($('#sea-search #searchword').val())+'-p-1.html';
			}
		});
	},
	'page': function(){//翻页
	}
},
'scroll':{//滚动条
	'fixed' : function($id, $top, $width){// 悬浮区域
		var offset = $('#'+$id).offset();
		if(offset){
			if(!$top){
				$top = 5;
			}
			if(!$width){
				$width = $('#'+$id).width();
			}			
			$(window).bind('scroll', function(){
				if($(this).scrollTop() > offset.top){
					$('#'+$id).css({"position":"fixed","top":$top+"px","width":$width+"px"});
				}else{
					$(('#'+$id)).css({"position":"relative"});
				}
			});		
		}
	},
	'totop':function($id, $top){//返回顶部
		// $id:dc-totop $top:偏移值
		$('body').append('<a href="#" class="'+$id+'" id="'+$id+'"><i class="glyphicon glyphicon-chevron-up"></i></a>');
		$(window).bind('scroll', function(){
			if($(this).scrollTop() > $top){
				$('#'+$id).fadeIn("slow");
			}else{
				$('#'+$id).fadeOut("slow");
			}
		});	
	}
},
'load':{//延迟加载
	'images': function(){
		$.ajaxSetup({
			cache: true 
		});
		$.getScript("http://cdn.bootcss.com/jquery_lazyload/1.9.7/jquery.lazyload.min.js", function(response, status) {
			$("img.sea-img").lazyload({
				placeholder : cms.root+"Public/images/no.jpg",
				effect : "fadeIn",
				failurelimit: 15
				//threshold : 400
				//skip_invisible : false
				//container: $(".carousel-inner"),
			}); 
		});
	},
	'flickity':function(){//手机滑动
		if($(".sea-gallery").length && seacms.browser.useragent.mobile){
			$.ajaxSetup({ 
				cache: true 
			});
			$("<link>").attr({ rel: "stylesheet",type: "text/css",href: "https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.1/flickity.min.css"}).appendTo("head");
			$.getScript("https://cdnjs.cloudflare.com/ajax/libs/flickity/1.1.1/flickity.pkgd.min.js", function(response, status) {
				$('.sea-gallery').flickity({
					cellAlign: 'left',
					freeScroll: true,
					contain: true,
					prevNextButtons: false,
					pageDots: false
				});
			});
		}
	},
	'raty': function(){
		$.ajaxSetup({ 
			cache: true 
		});
		if($("#sea-raty").length ){
			$("<link>").attr({ rel: "stylesheet",type: "text/css",href: "http://cdn.bootcss.com/raty/2.7.1/jquery.raty.min.css"}).appendTo("head");
			$.getScript("http://cdn.bootcss.com/raty/2.7.1/jquery.raty.min.js", function(response, status) {
				$('#sea-raty').raty({ 
					starType: 'i',
					number: 5,
					numberMax : 5,
					half: true,
					score : function(){
						return $(this).attr('data-score');
					},
					click: function(score, evt) {
						$.ajax({
							type: 'get',
							url: cms.root+'index.php?s=gold-'+$('#sea-raty').attr('data-module')+'-id-'+$('#sea-raty').attr('data-id')+'-score-'+(score*2),
							timeout: 5000,
							dataType:'json',
							error: function(){
								$('#sea-raty').attr('title', '网络异常！').tooltip('show');
							},
							success: function(json){
								if(json.status == 1){
									$('#sea-raty-tips').html(json.data.gold);
								}else{
									$('#sea-raty').attr('title', json.info).tooltip('show');
								}
							}
						});
					}
				});
			});
		}
	},
	'autocomplete': function(){
		$.ajaxSetup({ 
			cache: true 
		});
		$.getScript("http://cdn.bootcss.com/jquery.devbridge-autocomplete/1.2.26/jquery.autocomplete.min.js", function(response, status) {
			$('#searchword').autocomplete({
				serviceUrl : cms.root+'index.php?s=plus-search-vod',
				params: {'limit': 10},
				paramName: 'wd',
				maxHeight: 400,
				transformResult: function(response) {
					var obj = $.parseJSON(response);
					return {
						suggestions: $.map(obj.data, function(dataItem) {
								return { value: dataItem.vod_name, data: dataItem.vod_link };
						})
					};
				},
				onSelect: function (suggestion) {
					location.href = suggestion.data;
					//alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
				}
			});
		});
	}
},
'cms':{//CMS系统
	'goback': function(){
		if(seacms.browser.useragent.mobile){
			if(history.length > 0 && document.referrer){
				$("#sea-goback").show();
				$('#sea-goback').attr('href','javascript:history.go(-1);');
			}else{
				$("#sea-goback").hide();
			}
		}
	},
	'comment': function(){
		if($(".sea-comment").length ){
			$(".sea-comment").html('<div id="uyan_frame"></div>');
			$.getScript("http://v2.uyan.cc/code/uyan.js?uid=1528513");
		}
	},
	'share': function(){
		if($(".sea-share").length ){
			$(".sea-share").html('<div class="bdsharebuttonbox"><a href="#" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a><a href="#" class="bds_weixin" data-cmd="weixin" title="分享到微信"></a><a href="#" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a><a href="#" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a><a href="#" class="bds_bdysc" data-cmd="bdysc" title="分享到百度云收藏"></a><a href="#" class="bds_copy" data-cmd="copy" title="分享到复制网址"></a></div>');
			window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];
		}
	},
	'nav': function($id){
		$('#nav-'+$id).addClass("active");
	}
	
}
//end
};
/*#sea-search #wd #sea-goback .sea-gallery .sea-raty .sea-img .sea-share*/
$(document).ready(function(){
	seacms.submit.search();
	seacms.scroll.totop('sea-totop',5);
	seacms.click.updown();
	seacms.cms.goback();
	seacms.cms.share();
	seacms.cms.qrcode();
	seacms.cms.comment();
	seacms.load.images();
	seacms.load.raty();
	seacms.load.autocomplete();
	seacms.load.flickity();
});