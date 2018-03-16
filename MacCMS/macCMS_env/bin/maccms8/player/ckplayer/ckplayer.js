/*
-------------------------------------------------------------------------
  说明:
  ckplayer6.3,有问题请访问http://www.ckplayer.com
  请注意，该文件为UTF-8编码，不需要改变编码即可使用于各种编码形式的网站内	
-------------------------------------------------------------------------
第一部分，加载插件
以下为加载的插件部份
插件的设置参数说明：
	1、插件名称
	2、水平对齐方式（0左，1中，2右）
	3、垂直对齐方式（0上，1中，2下）
	4、水平方向位置偏移量
	5、垂直方向位置偏移量
	6、插件的等级+竖线
	7、插件是否绑定在控制栏上，0不绑定，1绑定，当值是1的时候该插件将会随着控制栏一起隐藏或缓动
	插件名称尽量不要相同，对此的详细说明请到网站查看
*/
function ckcpt(){
    var cpt = '';
	cpt += 'right.swf,2,1,-75,-100,2,0|';//右边开关灯，调整，分享按钮的插件
	cpt += 'share.swf,1,1,-180,-100,3,0|';//分享插件
	cpt += 'adjustment.swf,1,1,-180,-100,3,0|';//调整大小和颜色的插件
    return cpt;
}
/*
插件的定义结束
以下是对播放器功能进行配置
*/
function ckstyle() { //定义总的风格
    var ck = {
    cpath:'',
	/*
	播放器风格压缩包文件的路径，默认的是style.swf
	如果调用不出来可以试着设置成绝对路径试试
	如果不知道路径并且使用的是默认配置，可以直接留空，播放器会
	*/
	language:'',
	/*播放器所使用的语言配置文件，需要和播放器在同目录下，默认是language.xml*/
	flashvars:'',
	/*
	这里是用来做为对flashvars值的补充，除了c和x二个参数以外的设置都可以在这里进行配置
	*/
	setup:'1,1,1,1,1,2,0,1,2,0,0,1,200,0,2,1,0,1,1,1,2,10,3,0,1,2,3000,0,0,0,1,1,1,1,1,1,1,250,0',
	/*
	这是配置文件里比较重要的一个参数，共有N个功能控制参数，并且以后会继续的增加，各控制参数以英文逗号(,)隔开。下面列出各参数的说明：
		1、鼠标经过按钮是否使用手型，0普通鼠标，1手型鼠标
		2、是否支持单击暂停，0不支持，1是支持
		3、是否支持双击全屏，0不支持，1是支持
		4、在播放前置广告时是否同时加载视频，0不加载，1加载
		5、广告显示的参考对象，0是参考视频区域，1是参考播放器区域
		6、广告大小的调整方式,只针对swf和图片有效,视频是自动缩放的
			=0是自动调整大小，意思是说大的话就变小，小的话就变大
			=1是大的化变小，小的话不变
			=2是什么也不变，就这么大
			=3是跟参考对像(第5个控制)参数设置的一样宽高
		7、前置广告播放顺序，0是顺序播放，1是随机播放
		8、对于视频广告是否采用修正，0是不使用，1是使用，如果是1，则用户在网速慢的情况下会按设定的倒计时进行播放广告，计时结束则放正片（比较人性化），设置成0的话，则强制播放完广告才能播放正片
		9、是否开启滚动文字广告，0是不开启，1是开启且不使用关闭按钮，2是开启并且使用关闭按钮，开启后将在加载视频的时候加载滚动文字广告
		10、视频的调整方式
			=0是自动调整大小，意思是说大的话就变小，小的话就变大，同时保持长宽比例不变
			=1是大的化变小，小的话不变
			=2是什么也不变，就这么大
			=3是跟参考对像(pm_video的设置)参数设置的一样宽高
		11、是否在多视频时分段加载，0不是，1是
		12、缩放视频时是否进行平滑处理，0不是，1是
		13、视频缓冲时间,单位：毫秒,建议不超过300
		14、初始图片调整方式(
			=0是自动调整大小，意思是说大的话就变小，小的话就变大，同时保持长宽比例不变
			=1是大的化变小，小的话不变
			=2是什么也不变，就这么大
			=3是跟pm_video参数设置的一样宽高
		15、暂停广告调整方式(
			=0是自动调整大小，意思是说大的话就变小，小的话就变大，同时保持长宽比例不变
			=1是大的化变小，小的话不变
			=2是什么也不变，就这么大
			=3是跟pm_video参数设置的一样宽
		16、暂停广告是否使用关闭广告设置，0不使用，1使用
		17、缓冲时是否播放广告，0是不显示，1是显示并同时隐藏掉缓冲图标和进度，2是显示并不隐藏缓冲图标
		18、是否支持键盘空格键控制播放和暂停0不支持，1支持
		19、是否支持键盘左右方向键控制快进快退0不支持，1支持
		20、是否支持键盘上下方向键控制音量0不支持，1支持
		21、播放器返回js交互函数的等级，0-2,等级越高，返回的参数越多
			0是返回少量常用交互
			1返回播放器在播放的时候的参数，不返回广告之类的参数
			2返回全部参数
			3返回全部参数，并且在参数前加上"播放器ID->"，用于多播放器的监听
		22、快进和快退的秒数
		23、界面上图片元素加载失败重新加载次数
		24、开启加载皮肤压缩文件包的加载进度提示
		25、使用隐藏控制栏时显示简单进度条的功能,0是不使用，1是使用，2是只在普通状态下使用
		26、控制栏隐藏设置(0不隐藏，1全屏时隐藏，2都隐藏
		27、控制栏隐藏延时时间，即在鼠标离开控制栏后多少毫秒后隐藏控制栏
		28、左右滚动时是否采用无缝，默认0采用，1是不采用
		29、0是正常状态，1是控制栏默认隐藏，播放状态下鼠标经过播放器显示控制栏，2是一直隐藏控制栏
		30、在播放rtmp视频时暂停后点击播放是否采用重新链接的方式,这里一共分0-3四个等级
		31、进度条是否采用前端优化，默认0不采用，1是采用，即加载进度不跟随实际进度，而是展现给用户一个比较流畅的感觉
		32、是否启用播放按钮和暂停按钮
		33、是否启用中间暂停按钮
		34、是否启用静音按钮
		35、是否启用全屏按钮
		36、是否启用进度调节栏
		37、是否启用调节音量
		38、计算时间的间隔，毫秒
		39、前置logo至少显示的时间，单位：毫秒
	*/
	pm_bg:'0x000000,100,230,180',
	/*播放器整体的背景配置，请注意，这里只是一个初始化的设置，如果需要真正的改动播放器的背景和最小宽高，需要在风格文件里找到相同的参数进行更改。
		1、整体背景颜色
		2、背景透明度
		3、播放器最小宽度
		4、播放器最小高度
	*/
	mylogo:'logo.swf',
	/*
	视频加载前显示的logo文件，不使用设置成null，即ck.mylogo='null';
	*/
	pm_mylogo:'1,1,-100,-55',
	/*
	视频加载前显示的logo文件(mylogo参数的)的位置
	本软件所有的四个参数控制位置的方式全部都是统一的意思，如下
		1、水平对齐方式，0是左，1是中，2是右
		2、垂直对齐方式，0是上，1是中，2是下
		3、水平偏移量，举例说明，如果第1个参数设置成0左对齐，第3个偏移量设置成10，就是离左边10个像素，第一个参数设置成2，偏移量如果设置的是正值就会移到播放器外面，只有设置成负值才行，设置成-1，按钮就会跑到播放器外面
		4、垂直偏移量 
	*/
	logo:'cklogo.png',
	/*
	默认右上角一直显示的logo，不使用设置成null，即ck.logo='null';
	*/
	pm_logo:'2,0,-100,20',
	/*
	播放器右上角的logo的位置
		1、水平对齐方式，0是左，1是中，2是右
		2、垂直对齐方式，0是上，1是中，2是下
		3、水平偏移量
		4、垂直偏移量 
	以下是播放器自带的二个插件
	*/
	control_rel:'related.swf,/ckplayer/related.xml,0',
	/*
	视频结束显示精彩视频的插件
		1、视频播放结束后显示相关精彩视频的插件文件（注意，视频结束动作设置成3时(即var flashvars={e:3})有效），
		2、xml文件是调用精彩视频的示例文件，可以自定义文件类型（比如asp,php,jsp,.net只要输出的是xml格式就行）,实际使用中一定要注意第二个参数的路径要正确
		3、第三个参数是设置配置文件的编码，0是默认的utf-8,1是gbk2312 
	*/
	control_pv:'Preview.swf,105,2000',
	/*
	视频预览插件
		1、插件文件名称(该插件和上面的精彩视频的插件都是放在风格压缩包里的)
		2、离进度栏的高(指的是插件的顶部离进度栏的位置)
		3、延迟时间(该处设置鼠标经过进度栏停顿多少毫秒后才显示插件)
		建议一定要设置延时时间，不然当鼠标在进度栏上划过的时候就会读取视频地址进行预览，很占资源 
	*/
	pm_repc:'',
	/*
	视频地址替换符，该功能主要是用来做简单加密的功能，使用方法很简单，请注意，只针对f值是视频地址的时候有效，其它地方不能使用。具体的请查看http://www.ckplayer.com/manual.php?id=4#title_25
	*/
	pm_spac:'|',
	/*
	视频地址间隔符，这里主要是播放多段视频时使用普通调用方式或网址调用方式时使用的。默认使用|，如果视频地址里本身存在|的话需要另外设置一个间隔符，注意，即使只有一个视频也需要设置。另外在使用rtmp协议播放视频的时候，如果视频存在多级目录的话，这里要改成其它的符号，因为rtmp协议的视频地址多级的话也需要用到|隔开流地址和实例地址 
	*/
	pm_fpac:'file->f',
	/*
	该参数的功能是把自定义的flashvars里的变量替换成ckplayer里对应的变量，默认的参数的意思是把flashvars里的file值替换成f值，因为ckplayer里只认f值，多个替换之间用竖线隔开
	*/
	pm_advtime:'2,0,-110,10,0,300,0',
	/*
	前置广告倒计时文本位置，播放前置 广告时有个倒计时的显示文本框，这里是设置该文本框的位置和宽高，对齐方式的。一共7个参数，分别表示：
		1、水平对齐方式，0是左对齐，1是中间对齐，2是右对齐
		2、垂直对齐方式，0是上对齐，1是中间对齐，2是低部对齐
		3、水平位置偏移量
		4、垂直位置偏移量
		5、文字对齐方式，0是左对齐，1是中间对齐，2是右对齐，3是默认对齐
		6、文本框宽席
		7、文本框高度 
	*/
	pm_advstatus:'1,2,2,-200,-40',
	/*
	前置广告静音按钮，静音按钮只在是视频广告时显示，当然也可以控制不显示 
		1、是否显示0不显示，1显示
		2、水平对齐方式
		3、垂直对齐方式
		4、水平偏移量
		5、垂直偏移量
	*/
	pm_advjp:'1,1,2,2,-100,-40',
	/*
	前置广告跳过广告按钮的位置
		1、是否显示0不显示，1是显示
		2、跳过按钮触发对象(值0/1,0是直接跳转,1是触发js:function ckadjump(){})
		3、水平对齐方式
		4、垂直对齐方式
		5、水平偏移量
		6、垂直偏移量
	*/
	pm_padvc:'2,0,-10,-10',
	/*
	暂停广告的关闭按钮的位置
		1、水平对齐方式
		2、垂直对齐方式
		3、水平偏移量
		4、垂直偏移量
	*/
	pm_advms:'2,2,-46,-56',
	/*
	滚动广告关闭按钮位置
		1、水平对齐方式
		2、垂直对齐方式
		3、水平偏移量
		4、垂直偏移量
	*/
	pm_zip:'1,1,-20,-8,1,0,0',
	/*
	加载皮肤压缩包时提示文字的位置
		1、水平对齐方式，0是左对齐，1是中间对齐，2是右对齐
		2、垂直对齐方式，0是上对齐，1是中间对齐，2是低部对齐
		3、水平位置偏移量
		4、垂直位置偏移量
		5、文字对齐方式，0是左对齐，1是中间对齐，2是右对齐，3是默认对齐
		6、文本框宽席
		7、文本框高度
	*/
	pm_advmarquee:'1,2,50,-60,50,18,0,0x000000,50,0,20,1,15,2000',
	/*
	滚动广告的控制，要使用的话需要在setup里的第9个参数设置成1
	这里分二种情况,前六个参数是定位控制，第7个参数是设置定位方式(0：相对定位，1：绝对定位)
	第一种情况：第7个参数是0的时候，相对定位，就是播放器长宽变化的时候，控制栏也跟着变
		1、默认1:中间对齐
		2、上中下对齐（0是上，1是中，2是下）
		3、离左边的距离
		4、Y轴偏移量
		5、离右边的距离
		6、高度
		7、定位方式
	第二种情况：第7个参数是1的时候，绝对定位，就是播放器长宽变化的时候，控制栏不跟着变，这种方式一般使用在控制栏大小不变的时候
		1、左中右对齐方式（0是左，1是中间，2是右）
		2、上中下对齐（0是上，1是中，2是下）
		3、x偏移量
		4、y偏移量
		5、宽度
		6、高度
		7、定位方式
	以上是前7个参数的作用
		8、是文字广告的背景色
		9、置背景色的透明度
		10、控制滚动方向，0是水平滚动（包括左右），1是上下滚动（包括向上和向下）
		11、移动的单位时长，即移动单位像素所需要的时长，毫秒
		12、移动的单位像素,正数同左/上，负数向右/下
		13、是行高，这个在设置向上或向下滚动的时候有用处
		14、控制向上或向下滚动时每次停止的时间
	*/
	advmarquee:escape('{a href="http://www.ckplayer.com"}{font color="#FFFFFF" size="12"}这里可以放文字广告，播放器默认使用这里设置的广告内容，如果不想在这里使用可以清空这里的内容，如果想在页面中实时定义滚动文字广告内容，可以清空这里的内容，然后在页面中设置广告函数。{/font}{/a}'),
	/*
	该处是滚动文字广告的内容，如果不想在这里设置，就把这里清空并且在页面中使用js的函数定义function ckmarqueeadv(){return '广告内容'}
	*/
	myweb:escape(''),
	/*
	------------------------------------------------------------------------------------------------------------------
	以下内容部份是和插件相关的配置，请注意，自定义插件以及其配置的命名方式要注意，不要和系统的相重复，不然就会替换掉系统的相关设置，删除相关插件的话也可以同时删除相关的配置
	------------------------------------------------------------------------------------------------------------------
	以下内容定义自定义插件的相关配置，这里也可以自定义任何自己的插件需要配置的内容，当然，如果你某个插件不使用的话，也可以删除相关的配置
	------------------------------------------------------------------------------------------------------------------
	*/
	cpt_lights:'1',
	/*
	该处定义是否使用开关灯，和right.swf插件配合作用,使用开灯效果时调用页面的js函数function closelights(){};
	*/
	cpt_share:'/player/ckplayer/share.xml',
	/*
	分享插件调用的配置文件地址
	调用插件开始
	*/
    cpt_list:ckcpt()
	/*
	ckcpt()是本文件最上方的定义插件的函数
	*/
	}
    return ck;
}
/*
html5部分开始
以下代码是支持html5的，如果你不需要，可以删除。
html5代码块的代码可以随意更改以适合你的应用，欢迎到论坛交流更改心得
*/
(function() {	
	var CKobject= {
		_K_:function(d){return document.getElementById(d);},
		getVideo:function(s){
			var v='';
			if(s){
				for(var i=0;i<s.length;i++){
					var a=s[i].split('->');
					if(a && a[0]){
						v+='<source src="'+a[0]+'"';
					}
					if(a.length==2 && a[1]){
						v+=' type="'+a[1]+'"';
					}
					v+='>';
				}
			}
			return v;
		},
		getVars:function(v,k){
			if(v[k]){
				return v[k];
			}
		},
		getParams:function(v){
			var p='';
			if(v){
				if(this.getVars(v,'p')==1 && this.getVars(v,'m')!=1){
					p+=' autoplay="autoplay"';
				}
				if(this.getVars(v,'e')==1){
					p+=' loop="loop"';
				}
				if(this.getVars(v,'m')==1){
					p+=' preload="meta"';
				}
				if(this.getVars(v,'i')){
					p+=' poster="'+this.getVars(v,'i')+'"';
				}
			}
			return p;
		},
		browser:function(){
			var m = (function(ua){
				var a=new Object();
				var b = {
					msie: /msie/.test(ua) && !/opera/.test(ua),
					opera: /opera/.test(ua),
					safari: /webkit/.test(ua) && !/chrome/.test(ua),
					firefox: /firefox/.test(ua),
					chrome: /chrome/.test(ua)
				};
				var vMark = '';
				for (var i in b) {
					if (b[i]) { vMark = 'safari' == i ? 'version' : i; break; }
				}
				b.version = vMark && RegExp('(?:' + vMark + ')[\\/: ]([\\d.]+)').test(ua) ? RegExp.$1 : '0';
				b.ie = b.msie;
				b.ie6 = b.msie && parseInt(b.version, 10) == 6;
				b.ie7 = b.msie && parseInt(b.version, 10) == 7;
				b.ie8 = b.msie && parseInt(b.version, 10) == 8;
				a.B=vMark;
				a.V=b.version;
				return a;
			})(window.navigator.userAgent.toLowerCase());
			return m;
		},
		Platform:function(){
			var w=''; 
			var u = navigator.userAgent, app = navigator.appVersion;              
			var b={                  
				iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1,
				iPad: u.indexOf('iPad') > -1,
				ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
				android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,
				webKit: u.indexOf('AppleWebKit') > -1,
				gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,
				presto: u.indexOf('Presto') > -1,
				trident: u.indexOf('Trident') > -1,       
				mobile: !!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/),
				webApp: u.indexOf('Safari') == -1
			}; 
			for (var k in b){
				if(b[k]){
					w=k;
					break;
				}
			}
			return w;
		},
		getpath:function(z) {
			var d = unescape(window.location.href).replace('file:///', '');
			var k = parseInt(document.location.port);
			var u = document.location.protocol + '//' + document.location.hostname;
			var l = '',e = '',t = '';
			var s = 0;
			var r = z.split('//');
			if (r.length > 0) {
				l = r[0] + '//'
			}
			var h = 'http|https|ftp|rtsp|mms|ftp|rtmp';
			var a = h.split('|');
			if(k!=80){
				u+=':'+k;
			}
			for (i = 0; i < a.length; i++){
				if ((a[i] + '://') == l){
					s = 1;
					break;
				}
			}
			if (s == 0) {
				if (z.substr(0, 1) == '/'){
					t = u + z;
				}
				else {
					e = d.substring(0, d.lastIndexOf('/') + 1).replace('\\', '/');
					var w = z.replace('../', './');
					var u = w.split('./');
					var n = u.length;
					var r = w.replace('./', '');
					var q = e.split('/');
					var j = q.length - n;
					for (i = 0; i < j; i++) {
						t += q[i] + '/';
					}
					t += r;
				}
			}
			else {
				t = z;
			}
			return t;
		},
		Flash:function(){
			var f=false,v=0;
			if(document.all){
				try { 
					var s=new ActiveXObject('ShockwaveFlash.ShockwaveFlash'); 
					f=true;
					var z=s.GetVariable('$version');
					v=parseInt(z.split(' ')[1].split(',')[0]);
				} 
				catch(e){} 
			}
			else{
				if (navigator.plugins && navigator.plugins.length > 0){
					var s=navigator.plugins['Shockwave Flash'];
					if (s){
						f=true;
						var w = s.description.split(' ');
						for (var i = 0; i < w.length; ++i){
							if (isNaN(parseInt(w[i]))) continue;
								v = parseInt(w[i]);
							}
						}
				}
			}
			return {f:f,v:v};
		},
		embedHTML5:function(C,P,W,H,V,A,S){
			var v='',
			b=this.browser()['B'],
			v=this.browser()['V'],
			x=v.split('.'),
			t=x[0],
			m=b+v,
			n=b+t,
			w='',
			s=false,
			f=this.Flash()['f'],
			a=false;
			if(!S){
				S=['iPad','iPhone','ios'];
			}
			for(var i=0;i<S.length;i++){
				w=S[i];
				if (w.indexOf('+')>-1){
					w=w.split('+')[0];
					a=true;
				}
				else{
					a=false;
				}
				if(this.Platform()==w|| m==w || n==w || b==w){
					if(a){
						if(!f){
							s=true;
							break;
						}
					}
					else{
						s=true;
						break;
					}
				}
			}
			if(s){
				v='<video controls id="'+P+'" width="'+W+'" height="'+H+'"'+this.getParams(A)+'>'+this.getVideo(V)+'</video>';
				this._K_(C).innerHTML=v;
				this._K_(C).style.width=W+'px';
				this._K_(C).style.height=H+'px';
				this._K_(C).style.backgroundColor='#000';
			}
		},
		getflashvars:function(s){
			var v='',i=0;
			if(s){
				for(var k in s){
					if(i>0){
						v+='&';
					}
					if(k=='f' && s[k] && !ckstyle()['pm_repc']){
						s[k]=this.getpath(s[k]);
						if(s[k].indexOf('&')>-1){
							s[k]=encodeURIComponent(s[k]);
						}
					}
					if(k=='y' && s[k]){
						s[k]=this.getpath(s[k]);
					}
					v+=k+'='+s[k];
					i++;
				}
			}
			return v;
		},
		getparam:function(s){
			var w='',v='',
			o={
				allowScriptAccess:'always',
				allowFullScreen:true,
				quality:'high',
				bgcolor:'#000'
			};
			if(s){
				for(var k in s){
					o[k]=s[k];
				}
			}
			for(var e in o){
				w+=e+'="'+o[e]+'" ';
				v+='<param name="'+e+'" value="'+o[e]+'" />';
			}
			w=w.replace('movie=','src=');
			return {w:w,v:v};
		},
		getObjectById:function (s){
			var X = null,
			Y = this._K_(s),
			r = 'embed';
			if (Y && Y.nodeName == 'OBJECT') {
				if (typeof Y.SetVariable != 'undefined') {
					X = Y;
				} else {
					var Z = Y.getElementsByTagName(r)[0];
					if (Z) {
						X = Z;
					}
				}
			}
			return X;
		},
		embedSWF:function(C,D,N,W,H,V,P){
			if(!N){N='ckplayer_a1'}
			if(!P){P={};}
			var u='undefined',
			j=document,
			r='http://www.macromedia.com/go/getflashplayer',
			t='<a href="'+r+'" target="_blank">请点击此处下载安装最新的flash插件</a>',
			error={
				w:'您的网页不符合w3c标准，无法显示播放器',
				f:'您没有安装flash插件，无法播放视频，'+t,
				v:'您的flash插件版本过低，无法播放视频，'+t
			},
			w3c=typeof j.getElementById != u && typeof j.getElementsByTagName != u && typeof j.createElement != u,
			i='id="'+N+'" name="'+N+'" ',
			s='',
			l='';
			P['movie']=C;
			P['flashvars']=this.getflashvars(V);
			s+='<object  pluginspage="http://www.macromedia.com/go/getflashplayer" ';
			s+='classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
			s+='codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" ';
			s+='width="'+W+'" ';
			s+='height="'+H+'" ';
			s+=i;
			s+='align="middle">';
			s+=this.getparam(P)['v'];
			s+='<embed ';
			s+=this.getparam(P)['w'];
			s+=' width="'+W+'" height="'+H+'" name="'+N+'" id="'+N+'" align="middle" '+i;
			s+='type="application/x-shockwave-flash" pluginspage="'+r+'" />';
			s+='</object>';
			if(!w3c){
				l=error['w'];
			}
			else{
				if(!this.Flash()['f']){
					l=error['f'];
				}
				else{
					if(this.Flash()['v']<10){
						l=error['f'];
					}
					else{
						l=s;
					}
				}
			}
			if(l){
				this._K_(D).innerHTML=l;
				this._K_(D).style.color='#FFDD00';
			}
		}
	}
	window.CKobject = CKobject;
})();
/*
html5 部分结束
======================================================
SWFObject v2.2
如果你的网站里已经有swfobject类，可以删除下面的
*/
var swfobject=function(){var D="undefined",r="object",S="Shockwave Flash",W="ShockwaveFlash.ShockwaveFlash",q="application/x-shockwave-flash",R="SWFObjectExprInst",x="onreadystatechange",O=window,j=document,t=navigator,T=false,U=[h],o=[],N=[],I=[],l,Q,E,B,J=false,a=false,n,G,m=true,M=function(){var aa=typeof j.getElementById!=D&&typeof j.getElementsByTagName!=D&&typeof j.createElement!=D,ah=t.userAgent.toLowerCase(),Y=t.platform.toLowerCase(),ae=Y?/win/.test(Y):/win/.test(ah),ac=Y?/mac/.test(Y):/mac/.test(ah),af=/webkit/.test(ah)?parseFloat(ah.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,X=!+"\v1",ag=[0,0,0],ab=null;if(typeof t.plugins!=D&&typeof t.plugins[S]==r){ab=t.plugins[S].description;if(ab&&!(typeof t.mimeTypes!=D&&t.mimeTypes[q]&&!t.mimeTypes[q].enabledPlugin)){T=true;X=false;ab=ab.replace(/^.*\s+(\S+\s+\S+$)/,"$1");ag[0]=parseInt(ab.replace(/^(.*)\..*$/,"$1"),10);ag[1]=parseInt(ab.replace(/^.*\.(.*)\s.*$/,"$1"),10);ag[2]=/[a-zA-Z]/.test(ab)?parseInt(ab.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0}}else{if(typeof O.ActiveXObject!=D){try{var ad=new ActiveXObject(W);if(ad){ab=ad.GetVariable("$version");if(ab){X=true;ab=ab.split(" ")[1].split(",");ag=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}}catch(Z){}}}return{w3:aa,pv:ag,wk:af,ie:X,win:ae,mac:ac}}(),k=function(){if(!M.w3){return}if((typeof j.readyState!=D&&j.readyState=="complete")||(typeof j.readyState==D&&(j.getElementsByTagName("body")[0]||j.body))){f()}if(!J){if(typeof j.addEventListener!=D){j.addEventListener("DOMContentLoaded",f,false)}if(M.ie&&M.win){j.attachEvent(x,function(){if(j.readyState=="complete"){j.detachEvent(x,arguments.callee);f()}});if(O==top){(function(){if(J){return}try{j.documentElement.doScroll("left")}catch(X){setTimeout(arguments.callee,0);return}f()})()}}if(M.wk){(function(){if(J){return}if(!/loaded|complete/.test(j.readyState)){setTimeout(arguments.callee,0);return}f()})()}s(f)}}();function f(){if(J){return}try{var Z=j.getElementsByTagName("body")[0].appendChild(C("span"));Z.parentNode.removeChild(Z)}catch(aa){return}J=true;var X=U.length;for(var Y=0;Y<X;Y++){U[Y]()}}function K(X){if(J){X()}else{U[U.length]=X}}function s(Y){if(typeof O.addEventListener!=D){O.addEventListener("load",Y,false)}else{if(typeof j.addEventListener!=D){j.addEventListener("load",Y,false)}else{if(typeof O.attachEvent!=D){i(O,"onload",Y)}else{if(typeof O.onload=="function"){var X=O.onload;O.onload=function(){X();Y()}}else{O.onload=Y}}}}}function h(){if(T){V()}else{H()}}function V(){var X=j.getElementsByTagName("body")[0];var aa=C(r);aa.setAttribute("type",q);var Z=X.appendChild(aa);if(Z){var Y=0;(function(){if(typeof Z.GetVariable!=D){var ab=Z.GetVariable("$version");if(ab){ab=ab.split(" ")[1].split(",");M.pv=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}else{if(Y<10){Y++;setTimeout(arguments.callee,10);return}}X.removeChild(aa);Z=null;H()})()}else{H()}}function H(){var ag=o.length;if(ag>0){for(var af=0;af<ag;af++){var Y=o[af].id;var ab=o[af].callbackFn;var aa={success:false,id:Y};if(M.pv[0]>0){var ae=c(Y);if(ae){if(F(o[af].swfVersion)&&!(M.wk&&M.wk<312)){w(Y,true);if(ab){aa.success=true;aa.ref=z(Y);ab(aa)}}else{if(o[af].expressInstall&&A()){var ai={};ai.data=o[af].expressInstall;ai.width=ae.getAttribute("width")||"0";ai.height=ae.getAttribute("height")||"0";if(ae.getAttribute("class")){ai.styleclass=ae.getAttribute("class")}if(ae.getAttribute("align")){ai.align=ae.getAttribute("align")}var ah={};var X=ae.getElementsByTagName("param");var ac=X.length;for(var ad=0;ad<ac;ad++){if(X[ad].getAttribute("name").toLowerCase()!="movie"){ah[X[ad].getAttribute("name")]=X[ad].getAttribute("value")}}P(ai,ah,Y,ab)}else{p(ae);if(ab){ab(aa)}}}}}else{w(Y,true);if(ab){var Z=z(Y);if(Z&&typeof Z.SetVariable!=D){aa.success=true;aa.ref=Z}ab(aa)}}}}}function z(aa){var X=null;var Y=c(aa);if(Y&&Y.nodeName=="OBJECT"){if(typeof Y.SetVariable!=D){X=Y}else{var Z=Y.getElementsByTagName(r)[0];if(Z){X=Z}}}return X}function A(){return !a&&F("6.0.65")&&(M.win||M.mac)&&!(M.wk&&M.wk<312)}function P(aa,ab,X,Z){a=true;E=Z||null;B={success:false,id:X};var ae=c(X);if(ae){if(ae.nodeName=="OBJECT"){l=g(ae);Q=null}else{l=ae;Q=X}aa.id=R;if(typeof aa.width==D||(!/%$/.test(aa.width)&&parseInt(aa.width,10)<310)){aa.width="310"}if(typeof aa.height==D||(!/%$/.test(aa.height)&&parseInt(aa.height,10)<137)){aa.height="137"}j.title=j.title.slice(0,47)+" - Flash Player Installation";var ad=M.ie&&M.win?"ActiveX":"PlugIn",ac="MMredirectURL="+O.location.toString().replace(/&/g,"%26")+"&MMplayerType="+ad+"&MMdoctitle="+j.title;if(typeof ab.flashvars!=D){ab.flashvars+="&"+ac}else{ab.flashvars=ac}if(M.ie&&M.win&&ae.readyState!=4){var Y=C("div");X+="SWFObjectNew";Y.setAttribute("id",X);ae.parentNode.insertBefore(Y,ae);ae.style.display="none";(function(){if(ae.readyState==4){ae.parentNode.removeChild(ae)}else{setTimeout(arguments.callee,10)}})()}u(aa,ab,X)}}function p(Y){if(M.ie&&M.win&&Y.readyState!=4){var X=C("div");Y.parentNode.insertBefore(X,Y);X.parentNode.replaceChild(g(Y),X);Y.style.display="none";(function(){if(Y.readyState==4){Y.parentNode.removeChild(Y)}else{setTimeout(arguments.callee,10)}})()}else{Y.parentNode.replaceChild(g(Y),Y)}}function g(ab){var aa=C("div");if(M.win&&M.ie){aa.innerHTML=ab.innerHTML}else{var Y=ab.getElementsByTagName(r)[0];if(Y){var ad=Y.childNodes;if(ad){var X=ad.length;for(var Z=0;Z<X;Z++){if(!(ad[Z].nodeType==1&&ad[Z].nodeName=="PARAM")&&!(ad[Z].nodeType==8)){aa.appendChild(ad[Z].cloneNode(true))}}}}}return aa}function u(ai,ag,Y){var X,aa=c(Y);if(M.wk&&M.wk<312){return X}if(aa){if(typeof ai.id==D){ai.id=Y}if(M.ie&&M.win){var ah="";for(var ae in ai){if(ai[ae]!=Object.prototype[ae]){if(ae.toLowerCase()=="data"){ag.movie=ai[ae]}else{if(ae.toLowerCase()=="styleclass"){ah+=' class="'+ai[ae]+'"'}else{if(ae.toLowerCase()!="classid"){ah+=" "+ae+'="'+ai[ae]+'"'}}}}}var af="";for(var ad in ag){if(ag[ad]!=Object.prototype[ad]){af+='<param name="'+ad+'" value="'+ag[ad]+'" />'}}aa.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+ah+">"+af+"</object>";N[N.length]=ai.id;X=c(ai.id)}else{var Z=C(r);Z.setAttribute("type",q);for(var ac in ai){if(ai[ac]!=Object.prototype[ac]){if(ac.toLowerCase()=="styleclass"){Z.setAttribute("class",ai[ac])}else{if(ac.toLowerCase()!="classid"){Z.setAttribute(ac,ai[ac])}}}}for(var ab in ag){if(ag[ab]!=Object.prototype[ab]&&ab.toLowerCase()!="movie"){e(Z,ab,ag[ab])}}aa.parentNode.replaceChild(Z,aa);X=Z}}return X}function e(Z,X,Y){var aa=C("param");aa.setAttribute("name",X);aa.setAttribute("value",Y);Z.appendChild(aa)}function y(Y){var X=c(Y);if(X&&X.nodeName=="OBJECT"){if(M.ie&&M.win){X.style.display="none";(function(){if(X.readyState==4){b(Y)}else{setTimeout(arguments.callee,10)}})()}else{X.parentNode.removeChild(X)}}}function b(Z){var Y=c(Z);if(Y){for(var X in Y){if(typeof Y[X]=="function"){Y[X]=null}}Y.parentNode.removeChild(Y)}}function c(Z){var X=null;try{X=j.getElementById(Z)}catch(Y){}return X}function C(X){return j.createElement(X)}function i(Z,X,Y){Z.attachEvent(X,Y);I[I.length]=[Z,X,Y]}function F(Z){var Y=M.pv,X=Z.split(".");X[0]=parseInt(X[0],10);X[1]=parseInt(X[1],10)||0;X[2]=parseInt(X[2],10)||0;return(Y[0]>X[0]||(Y[0]==X[0]&&Y[1]>X[1])||(Y[0]==X[0]&&Y[1]==X[1]&&Y[2]>=X[2]))?true:false}function v(ac,Y,ad,ab){if(M.ie&&M.mac){return}var aa=j.getElementsByTagName("head")[0];if(!aa){return}var X=(ad&&typeof ad=="string")?ad:"screen";if(ab){n=null;G=null}if(!n||G!=X){var Z=C("style");Z.setAttribute("type","text/css");Z.setAttribute("media",X);n=aa.appendChild(Z);if(M.ie&&M.win&&typeof j.styleSheets!=D&&j.styleSheets.length>0){n=j.styleSheets[j.styleSheets.length-1]}G=X}if(M.ie&&M.win){if(n&&typeof n.addRule==r){n.addRule(ac,Y)}}else{if(n&&typeof j.createTextNode!=D){n.appendChild(j.createTextNode(ac+" {"+Y+"}"))}}}function w(Z,X){if(!m){return}var Y=X?"visible":"hidden";if(J&&c(Z)){c(Z).style.visibility=Y}else{v("#"+Z,"visibility:"+Y)}}function L(Y){var Z=/[\\\"<>\.;]/;var X=Z.exec(Y)!=null;return X&&typeof encodeURIComponent!=D?encodeURIComponent(Y):Y}var d=function(){if(M.ie&&M.win){window.attachEvent("onunload",function(){var ac=I.length;for(var ab=0;ab<ac;ab++){I[ab][0].detachEvent(I[ab][1],I[ab][2])}var Z=N.length;for(var aa=0;aa<Z;aa++){y(N[aa])}for(var Y in M){M[Y]=null}M=null;for(var X in swfobject){swfobject[X]=null}swfobject=null})}}();return{registerObject:function(ab,X,aa,Z){if(M.w3&&ab&&X){var Y={};Y.id=ab;Y.swfVersion=X;Y.expressInstall=aa;Y.callbackFn=Z;o[o.length]=Y;w(ab,false)}else{if(Z){Z({success:false,id:ab})}}},getObjectById:function(X){if(M.w3){return z(X)}},embedSWF:function(ab,ah,ae,ag,Y,aa,Z,ad,af,ac){var X={success:false,id:ah};if(M.w3&&!(M.wk&&M.wk<312)&&ab&&ah&&ae&&ag&&Y){w(ah,false);K(function(){ae+="";ag+="";var aj={};if(af&&typeof af===r){for(var al in af){aj[al]=af[al]}}aj.data=ab;aj.width=ae;aj.height=ag;var am={};if(ad&&typeof ad===r){for(var ak in ad){am[ak]=ad[ak]}}if(Z&&typeof Z===r){for(var ai in Z){if(typeof am.flashvars!=D){am.flashvars+="&"+ai+"="+Z[ai]}else{am.flashvars=ai+"="+Z[ai]}}}if(F(Y)){var an=u(aj,am,ah);if(aj.id==ah){w(ah,true)}X.success=true;X.ref=an}else{if(aa&&A()){aj.data=aa;P(aj,am,ah,ac);return}else{w(ah,true)}}if(ac){ac(X)}})}else{if(ac){ac(X)}}},switchOffAutoHideShow:function(){m=false},ua:M,getFlashPlayerVersion:function(){return{major:M.pv[0],minor:M.pv[1],release:M.pv[2]}},hasFlashPlayerVersion:F,createSWF:function(Z,Y,X){if(M.w3){return u(Z,Y,X)}else{return undefined}},showExpressInstall:function(Z,aa,X,Y){if(M.w3&&A()){P(Z,aa,X,Y)}},removeSWF:function(X){if(M.w3){y(X)}},createCSS:function(aa,Z,Y,X){if(M.w3){v(aa,Z,Y,X)}},addDomLoadEvent:K,addLoadEvent:s,getQueryParamValue:function(aa){var Z=j.location.search||j.location.hash;if(Z){if(/\?/.test(Z)){Z=Z.split("?")[1]}if(aa==null){return L(Z)}var Y=Z.split("&");for(var X=0;X<Y.length;X++){if(Y[X].substring(0,Y[X].indexOf("="))==aa){return L(Y[X].substring((Y[X].indexOf("=")+1)))}}}return""},expressInstallCallback:function(){if(a){var X=c(R);if(X&&l){X.parentNode.replaceChild(l,X);if(Q){w(Q,true);if(M.ie&&M.win){l.style.display="block"}}if(E){E(B)}}a=false}}}}();