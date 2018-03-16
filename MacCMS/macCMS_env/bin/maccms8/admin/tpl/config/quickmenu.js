var menu = {
	"ma":{"text":"首页","default":"welcome","children":{ "welcome":{"text":"欢迎页面","url":"?m=admin-wel"},"leftdim_config":{"text":"快捷菜单配置","url":"?m=admin-quickmenu"} }},
	
	"mb":{"text":"系统","default":"basic_config","children":{"basic_config":{"text":"网站参数配置","url":"?m=system-config"},"url_config":{"text":"URL地址配置","url":"?m=system-configurl"},"play_config":{"text":"播放器参数配置","url":"?m=system-configplay"},"collect_config":{"text":"采集参数配置","url":"?m=system-configcollect"},"interface":{"text":"站外入库配置","url":"?m=system-configinterface"},"api_config":{"text":"开放API配置","url":"?m=system-configapi"},"connect_config":{"text":"整合登录配置","url":"?m=system-configconnect"},"buy_config":{"text":"在线支付配置","url":"?m=system-configpay"},"s2-1":{"text":"","url":"#"},"timming_config":{"text":"定时任务管理","url":"?m=system-timming"}}},
	
	"mc":{"text":"扩展","default":"link","children":{"pic":{"text":"图片管理","url":"?m=extend-pic"},"link":{"text":"友情链接","url":"?m=extend-link"},"gbook":{"text":"系统留言本","url":"?m=extend-gbook"},"comment":{"text":"系统评论","url":"?m=extend-comment"}}},
	
	"md":{"text":"视频","default":"vod","children":{"server":{"text":"服务器组","url":"?m=vod-server"},"player_config":{"text":"播放器","url":"?m=vod-player"},"vodtype":{"text":"视频分类","url":"?m=vod-type"},"vodclass":{"text":"剧情分类","url":"?m=vod-class"},"vodtopic":{"text":"视频专题","url":"?m=vod-topic"},"vodrepeat":{"text":"检测重复数据","url":"?m=vod-list-repeat-ok"},"vod":{"text":"视频数据","url":"?m=vod-list"},"vodadd":{"text":"添加视频","url":"?m=vod-info"} }},
	
	"me":{"text":"文章","default":"art","children":{"arttype":{"text":"文章分类","url":"?m=art-type"},"arttopic":{"text":"文章专题","url":"?m=art-topic"},"artrepeat":{"text":"检测重复数据","url":"?m=art-list-repeat-ok"},"art":{"text":"文章数据","url":"?m=art-list"},"artadd":{"text":"添加文章","url":"?m=art-info"}}},
	
	"mf":{"text":"用户","default":"user","children":{"manager":{"text":"管理员","url":"?m=user-manager"},"usergroup":{"text":"会员组","url":"?m=user-group"},"user":{"text":"会员","url":"?m=user-list"},"usercard":{"text":"充值卡","url":"?m=user-card"}}},
	
	"mg":{"text":"模版","default":"template","children":{"template":{"text":"页面模板","url":"?m=template-list"},"custom":{"text":"自定义页面","url":"?m=template-list-label-show"},"ads":{"text":"自定义广告","url":"?m=template-ads"},"wizard":{"text":"标签向导","url":"?m=template-wizard"}}},
	
	"mh":{"text":"生成","default":"make","children":{"make":{"text":"生成选项","url":"?m=make-option"},"makeindex":{"text":"生成首页","url":"?m=make-index"},"makemap":{"text":"生成地图","url":"?m=make-map"},"makeartindex":{"text":"生成文章首页","url":"?m=make-index-tab-art"},"makeartmap":{"text":"生成文章地图","url":"?m=make-map-tab-art"}}},
	
	
	"mi":{"text":"采集","default":"maccj","children":{"maccj":{"text":"联盟资源库","url":"?m=collect-union"},"ds":{"text":"定时挂机采集","url":"?m=collect-ds"} }},
		
	"mj":{"text":"数据库","default":"database","children":{"database":{"text":"数据库管理","url":"?m=db-list"},"sql":{"text":"执行SQL语句","url":"?m=db-sql"},"datarep":{"text":"数据批量替换","url":"?m=db-datarep"} }}
};
var currTab = 'ma';
var firstOpen = [];
var levels = $.cookie("adminlevels");
if(levels==null){ levels=""; }
levels = 'a,'+ levels;
