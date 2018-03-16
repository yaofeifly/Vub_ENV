function ubb2html(str)
{
	var i=0 ,codes=[], s=''+str, split_str='', tqeUrl='http://www.qs.com/vt/inc/client/js/tqEditor/', 
		escUrl=function(url){return url.replace(/\[\]/g, function(c){return {'[':'%5b',']':'%5d'}[c];})};//TQE.url;
	s=s.replace(/[<>\" ]/g,function(c){return {'<':'&lt;','>':'&gt;','"':'&quot;',' ':'&nbsp;'}[c];} ).replace(/\r?\n/g, '<br>');
	do{split_str= '__CODE__'+Math.random()+'_';}while(s.indexOf(split_str)>=0);
	s = s.replace(/\[code[=\]].*?\[\/code\]/ig, function(cs){codes.push(cs); return split_str+(i++)+'_'})
	 .replace(/\[flv(?:\s*=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\](.*?)\[\/flv\]/ig, function(all,w,h,auto_start,url){
		if(!w) w=320;
		if(!h) h=240;
		return '<embed id="flvPlayer" src="'+tqeUrl+'flvPlayer.swf" flashvars="vcastr_file='+ escUrl(url)+'&IsAutoPlay='+auto_start+'" width="'+w+'" height="'+h+'" quality="high" bgcolor="#0E0E0E" name="play" align="middle" allowscriptaccess="sameDomain" allowfullscreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="opaque" />'})
	 .replace(/\[rm(?:\s*=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\](.*?)\[\/rm\]/ig, function(all,w,h,auto_start,url){
		if(!w) w=320;
		if(!h) h=240;
		return '<embed src="'+ escUrl(url)+'" width='+w+' height='+h+' autostart="'+auto_start+'}" type="audio/x-pn-realaudio-plugin" console="Clip1" controls="ImageWindow" ></embed>';})

	.replace(/\[mp3\](.*?)\[\/mp3\]/ig, '<object id="mp3Player"  classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6"  type="application/x-ms-wmp" width="230" height="64"><PARAM NAME="URL" VALUE="$1" /><PARAM NAME="autoStart" VALUE="0" /><PARAM NAME="invokeURLs" VALUE="false"><PARAM NAME="playCount" VALUE="100"><PARAM NAME="Volume" VALUE="100"><PARAM NAME="defaultFrame" VALUE="datawindow"></object>')
	.replace(/\[img\](.*?)\[\/img\]/ig, '<img src=$1 />')
	.replace(/\[img(?:=\s*(\d+)\s*,\s*(\d+)\s*)\]\s*([\w_\-\.\/:]*)\s*\[\/img\]/ig, '<img src=$3 width=$1  height=$2 />')
	.replace(/\[(color|size)\s*=\s*([^\]]+)\s*\]/ig, '<font $1=$2>').replace(/\[\/(color|size)\]/ig, '</font>')
	.replace(/\[bg\s*=\s*([^\]]+)\s*\]/ig, '<span style="background:$1">').replace(/\[\/bg]/ig, '</span>')
	.replace(/\[(left|center|right)]/ig, '<div style="text-align:$1">').replace(/\[\/(left|center|right)]/ig, '</div>')
	.replace(/(?:<br>|\s)?\[list\](.*?)(<br>|\s)*\[\/list\]/ig, function(a,c){ return '<ul>'+c.replace(/\<br\>(\s|&nbsp;)*\[\*\]/g, '<li>')+'</ul>';})
	.replace(/(?:<br>|\s)?\[numlist\](.*?)(<br>|\s)*\[\/numlist\]/ig, function(a,c){ return '<ol>'+c.replace(/\<br\>(\s|&nbsp;)*\[\*\]/g, '<li>')+'</ol>';})
	.replace(/\[\:(\d+)]/g, '<img src="'+tqeUrl+'face/$1.gif" emot="$1" align="absMiddle"  >') //表情
	.replace(/\[(\/?(?:b|u|i|s|sup|sub|h[1-6]))\]/ig, '<$1>')
	.replace(/\[url\]\s*(((?!")[\s\S])*?)(?:"[\s\S]*?)?\s*\[\/url\]/ig,'<a href="$1">$1</a>')
	.replace(/\[url\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]*?)\s*\[\/url\]/ig,'<a href="$1">$2</a>')
	.replace(/\[email\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/email\]/ig,'<a href="mailto:$1">$1</a>')
	.replace(/\[email\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]+?)\s*\[\/email\]/ig,'<a href="mailto:$1">$2</a>')
	.replace(/\[qq\]\s*(\d+)\s*\[\/qq\]/ig,'<a href="tencent://message/?uin=$1">$1</a>')
	.replace(/\[qq\s*=\s*(\d+)\s*\]\s*([\s\S]+?)\s*\[\/qq\]/ig,'<a href="tencent://message/?uin=$1">$2</a>')
	.replace(/\[quote\]([\s\S]*?)\[\/quote\]/ig,'<blockquote>$1</blockquote>');

	while(/\[table\](((?!\[table)[\s\S])+?)\[\/table\]/i.test(s)){
		s=s.replace(/\[table\](((?!\[table)[\s\S])+?)\[\/table\]/ig, function(a,c){
			return '<table style="border-collapse: collapse;" width=98% border="1" cellPadding="3">'+c.replace(/\[(\/?(?:tr|td|th))\]/ig, '<$1>')+'</table>';
		});
	}
	i=-1;
	s= s.replace(new RegExp(split_str+'\\d+_','g'), function(c){return codes[++i];});
	return s;

}

function html2ubb(str)
{
	var i=0 ,codes=[], s=''+str, split_str='';
	do{split_str= '__CODE__'+Math.random()+'_';}while(s.indexOf(split_str)>=0);
	s = s.replace(/[\r\n]/g,'').replace(/<br[^>]*>/ig,"\n");
	s = s.replace(/\[code[=\]].*?\[\/code\]/ig, function(cs){codes.push(cs); return split_str+(i++)+'_'})
		.replace(/<embed ([^>]*)>/ig, function(all,attr){
			var a,url,w='',h='',as='',attrStr=attr.toLowerCase();
			a=/width\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
			if(a){
				w='='+a[1];
			}
			if(w!=''){
				a=/height\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
				if(a){
					h=','+a[1];
				}
			}
			//flv
			if(attrStr.indexOf('vcastr_file')){
				a= /vcastr_file=([^\"&]+)/.exec(attr);
				url=a[1];
				if(''!=w){
					a=/IsAutoPlay=(0|1)/.exec(attr);
					if(a){
						as=','+a[1];
					}
				}
				return '[flv'+w+h+as+']'+url+'[/flv]';
			}
			//flash
			if(attrStr.indexOf('shockwave-flash')){
				a=/src\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
				url=a[1];
				return '[flash'+w+h+']'+url+'[/flash]';
			}
			//rm
			if(attrStr.indexOf('realaudio-plugin')){
				a=/src\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
				url=a[1];
				if(''!=w){
					a=/autostart\s*=\s*\"?(0|1)/.exec(attrStr);
					if(a){
						as=','+a[1];
					}
				}
				return '[rm'+w+h+']'+url+'[/rm]';
			}
			return '';
		})
		.replace(/<object (.*?)<\/object>/ig, function(all,attr){
			var a,url,w='',h='',as='',attrStr=attr.toLowerCase();
			a=/width\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
			if(a){
				w='='+a[1];
			}
			if(w!=''){
				a=/height\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
				if(a){
					h=','+a[1];
				}
			}
			a=/<PARAM\s+NAME\s*=\s*"?URL"?\s+VALUE\s*=\s*"?([^\"> ]+)"?/i.exec(attr);
			url=a[1];
			//mp3
			if(attrStr.indexOf('mp3player')){
				return '[mp3]'+url+'[/mp3]';
			}
			return all;
		})
		.replace(/<img ([^>]+)>/ig, function(all,attr){
			var a,url,w='',h='',as='',attrStr=attr.toLowerCase();
			a=/width\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
			if(a){
				w='='+a[1];
			}
			if(w!=''){
				a=/height\s*=\s*\"?([^ \"]+)/i.exec(attrStr);
				if(a){
					h=','+a[1];
				}
			}
			//emot
			a= /emot\s*=\s*"?(\d+)"?/i.exec(attr);
			if(a){
				return '[:'+a[1]+']';
			}
			//img
			a=/src\s*=\s*"?([^\"> ]+)"?/i.exec(attr);
			url=a[1];
			return '[img'+w+h+']'+url+'[/img]';
		})
		//list
		.replace(/<ul[^>]*>/ig, '\n[list]')
		.replace(/\s*<\/ul[^>]*>/ig, '\n[/list]')
		.replace(/<ol[^>]*>/ig, '\n[numlist]')
		.replace(/\s*<\/ol[^>]*>/ig, '\n[/numlist]')
		.replace(/\s*<li[^>]*>/ig, '\n[*]')
		.replace(/<\/li[^>]*>/ig, '')
		
		.replace(/<(\/?(?:b|u|i|s|sup|sub|table|tr|td|th|h[1-6]))(?![a-z])[^>]*>/ig, '[$1]')
		.replace(/<(\/)?(strong|em|strike|del)(?![a-z])[^>]*>/ig, function(a,b,c){
			var t={'strong':'b', 'em':'i', 'strike':'s', 'del':'s'}[c.toLowerCase()];
			if(b!=='/')b='';
			return '['+b+t + ']';
		})
		.replace(/<blockquote(?:\s+[^>]*?)?>([\s\S]+?)<\/blockquote>/ig,'[quote]$1[/quote]');

	var reg,a,
		rgb2color=function(r,g,b){
			var color= (r*65536+g*256+b*1).toString(16),l=color.length;
			while(l++<6) color='0'+color;
			return '#'+color;
		},
		getFormat=function(aStr){
			var pre='', end='';
			aStr=aStr.toLowerCase();
			
			//left,center,right
			if( /align\s*[=:]\s*\"?(left|center|right)/.exec(aStr) ){
				pre+='['+RegExp.$1+']';
				end='[/'+RegExp.$1+']'+end;
			}
			//size
			if( /[^-]size\s*=\s*\"?(\d+\s*(?:px|pc|pt|em|ex|cm|mm)?)/.exec(aStr) ){
				pre+='[size='+RegExp.$1+']';
				end='[/size]'+end;
			}
			//color
			if( /[^-]color\s*[:=]\s*\"?rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/.exec(aStr) ){
				pre+='[color='+rgb2color(RegExp.$1,RegExp.$2,RegExp.$3)+']';
				end='[/color]'+end;
			}else if( /[^-]color\s*[:=]\s*\"?([a-z0-9#]+)/.exec(aStr) ){
				pre+='[color='+RegExp.$1+']';
				end='[/color]'+end;
			}
			//background-color
			if( /background(?:\-color)?\s*:\s*rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/.exec(aStr) ){
				pre+='[bg='+rgb2color(RegExp.$1,RegExp.$2,RegExp.$3)+']';
				end ='[/bg]'+end;
			}else if( /background(?:\-color)?\s*:\s*([a-z0-9#]+)/.exec(aStr) ){
				pre+='[bg='+RegExp.$1+']';
				end ='[/bg]'+end;
			};

			return {'pre':pre, 'end':end};
		};
	while(/<div([^>]*)>(((?!<div)[\s\S])*?)<\/div>/i.test(s)){// /<div/i.test(s)
		s=s.replace(/<div([^>]*)>(((?!<div)[\s\S])*?)<\/div>/ig, function(all, attr, text){
			a=getFormat(attr);
			return "\n"+a.pre+text+a.end+"\n";
		});
	}
	while(/<p([^>]*)>(((?!<p)[\s\S])*?)<\/p>/i.test(s)){// /<div/i.test(s)
		s=s.replace(/<p([^>]*)>(((?!<p)[\s\S])*?)<\/p>/ig, function(all, attr, text){
			a=getFormat(attr);
			return "\n"+a.pre+text+a.end+"\n";
		});
	}
	while(/<span([^>]*)>(((?!<span)[\s\S])*?)<\/span>/i.test(s)){// /<div/i.test(s)
		s=s.replace(/<span([^>]*)>(((?!<span)[\s\S])*?)<\/span>/ig, function(all, attr, text){
			a=getFormat(attr);
			return a.pre+text+a.end;
		});
	}
	while(/<font([^>]*)>(((?!<font)[\s\S])*?)<\/font>/i.test(s)){// /<div/i.test(s)
		s=s.replace(/<font([^>]*)>(((?!<font)[\s\S])*?)<\/font>/ig, function(all, attr, text){
			a=getFormat(attr);
			return a.pre+text+a.end;
		});
	}

	i=-1;
	s= s.replace(/<a .*?href\s*=\s*["']?([^'" ]+)[^>]*>([\s\S]*?)<\/a>/ig,function(all,url,text){
			var tag='url';
			if('mailto:'==url.substr(0,7).toLowerCase()){
				tag='email';
				url=url.substr(7,url.length);
			}else if('tencent:'==url.substr(0,8)){
				tag='qq';
				var a= /uin=(\d+)/i.exec(url);
				url=a[1];
			}
			if(text==url) return '['+tag+']'+text+'[/'+tag+']';
			return '['+tag+'='+url+']'+text+'[/'+tag+']';
		})
		.replace(/<\/?[a-z][^>]*>/ig,'')//删除未处理的标签
		.replace(new RegExp(split_str+'\\d+_','g'), function(c){return codes[++i];}) //恢复code
		.replace(/(&nbsp;|&gt;|&lt;|&quot;)/g,function(c){return {'&nbsp;':' ','&gt;':'>','&lt;':'<','&quot;':'"'}[c];})
		.replace(/\n\s*\n/g,'\n\n');//最多两个空行

	return s;

}