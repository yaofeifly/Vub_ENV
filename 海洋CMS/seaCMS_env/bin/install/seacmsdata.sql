
INSERT INTO `sea_arcrank` VALUES (1, 0, '开放浏览', 5, 0, 0, ''),
(2, 10, '注册会员', 5, 0, 100, ''),
(3, 50, '中级会员', 5, 300, 200, ''),
(4, 100, '高级会员', 5, 800, 500, '');

INSERT INTO `sea_mytag` VALUES('1','areasearch','地区搜索','1251590919','<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=大陆\' target=\"_blank\">大陆</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=香港\'target=\"_blank\">香港</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=台湾\'target=\"_blank\">台湾</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=日本\' target=\"_blank\">日本</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=韩国\' target=\"_blank\">韩国</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=欧美\' target=\"_blank\">欧美</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=其它\' target=\"_blank\">其它</a>');
INSERT INTO `sea_mytag` VALUES('2','yearsearch','按发行年份查看电影','1251509338','<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2009\' target=\"_blank\">2009</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2008\'target=\"_blank\">2008</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2007\' target=\"_blank\">2007</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2006\' target=\"_blank\">2006</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2005\' target=\"_blank\">2005</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2004\' target=\"_blank\">2004</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2003\' target=\"_blank\">2003</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2002\' target=\"_blank\">2002</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2001\' target=\"_blank\">2001</a>');
INSERT INTO `sea_mytag` VALUES('3','actorsearch','演员名字','1251590973','<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=成龙\' target=\"_blank\">成龙</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周星驰\'target=\"_blank\">周星驰</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周润发\'target=\"_blank\">周润发</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=舒淇\' target=\"_blank\">舒淇</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=葛优\' target=\"_blank\">葛优</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周杰伦\' target=\"_blank\">周杰伦</a> ');
INSERT INTO `sea_mytag` VALUES('4','nav_bottom_banner','导航栏下方通栏广告','1251591021','aaaaaaaaaaaaaaaaaaaaaa\r\n$$$\r\nbbbbbbbbbbbbbbbbbbbbbb\r\n$$$\r\neeeeeeeeeeeeeeeeeeeeee');

INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (1, '大陆', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (2, '香港', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (3, '台湾', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (4, '日本', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (5, '韩国', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (6, '欧美', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (7, '日韩', 0,0);
INSERT INTO `sea_co_cls` (`id`, `clsname`, `sysclsid`,`cotype`) VALUES (8, '中国', 0,0);

INSERT INTO `sea_myad` VALUES (1,'channel200x200bt','channel200x200bt',1344397254,'排行榜右下200x200','document.writeln(\"<div class=\\\"right_b_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (2,'channel200x200top','channel200x200top',1344397271,'排行榜右上200x200','document.writeln(\"<div class=\\\"right_t_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (3,'channel728x90','channel728x90',1344397299,'频道页上方728x90','document.writeln(\"<div class=\\\"guanggao_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (4,'content360x300','content360x300',1344397346,'内容页右侧360x300','document.writeln(\"<div class=\\\"gao_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (5,'content460x60','content460x60',1344397379,'内容页460x60','document.writeln(\"<div class=\\\"xia_ad_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (6,'foot960x90','foot960x90',1344397396,'底部960x90','document.writeln(\"<div class=\\\"ad_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (7,'head960x90','head960x90',1344397422,'导航下方960x90','document.writeln(\"<div class=\\\"topad_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (8,'index728x90','index728x90',1344397439,'首页728x90','document.writeln(\"<div class=\\\"rm_ad_neirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (9,'play300x250top','play300x250top',1344397508,'播放页右上300x250','document.writeln(\"<div class=\\\"play_ad_topneirong\\\"><\\/div>\")');
INSERT INTO `sea_myad` VALUES (10,'play300x250bt','play300x250bt',1344397533,'播放页右下300x250','document.writeln(\"<div class=\\\"play_ad_btneirong\\\"><\\/div>\")');

INSERT INTO `sea_type` (`tid`, `upid`, `tname`, `tenname`, `torder`, `templist`,`templist_1`,`keyword`,`description`, `ishidden`, `unionid`, `tptype`) VALUES
(1,0,'新闻','xinwen',1,'channel.html','content.html','','',0,'31_1',0),
(2,0,'娱乐','yule',2,'channel.html','content.html','','',0,'',0),
(3,0,'军事','junshi',3,'channel.html','content.html','','',0,'',0),
(4,0,'猎奇','lieqi',4,'channel.html','content.html','','',0,'',0),
(5,1,'体育','tiyu',5,'channel.html','content.html','','',0,'',0),
(6,1,'汽车','qiche',6,'channel.html','content.html','','',0,'',0),
(7,1,'科技','keji',7,'channel.html','content.html','','',0,'',0),
(8,1,'财经','caijing',8,'channel.html','content.html','','',0,'',0),
(9,1,'股市','gushi',9,'channel.html','content.html','','',0,'',0),
(10,1,'地方','difang',10,'channel.html','content.html','','',0,'',0),
(11,1,'母婴','muying',11,'channel.html','content.html','','',0,'',0),
(12,1,'生活','shenghuo',12,'channel.html','content.html','','',0,'',0),
(13,2,'明星','mingxing',13,'channel.html','content.html','','',0,'',0),
(14,2,'音乐','yinyue',14,'channel.html','content.html','','',0,'',0),
(15,2,'游戏','youxi',15,'channel.html','content.html','','',0,'',0),
(16,2,'原创','yuanchuang',16,'channel.html','content.html','','',0,'',0),
(17,0,'国内','guonei',17,'newspage.html','news.html','','',0,'',1),
(18,0,'国际','guoji',18,'newspage.html','news.html','','',0,'',1),
(19,0,'社会','shehui',19,'newspage.html','news.html','','',0,'',1),
(20,0,'军事','junshi',20,'newspage.html','news.html','','',0,'',1),
(21,0,'娱乐','yule',21,'newspage.html','news.html','','',0,'',1),
(22,0,'八卦','bagua',22,'newspage.html','news.html','','',0,'',1),
(23,0,'科技','keji',23,'newspage.html','news.html','','',0,'',1),
(24,0,'财经','caijing',24,'newspage.html','news.html','','',0,'',1),
(25,0,'公益','gongyi',25,'newspage.html','news.html','','',0,'',1),
(26,0,'评论','pinglun',26,'newspage.html','news.html','','',0,'',1),
(27,0,'时尚','shishang',27,'newspage.html','news.html','','',0,'',1);

INSERT INTO `sea_member_group` (`gid`, `gname`, `gtype`, `g_auth`, `g_upgrade`) VALUES ('1', '匿名用户', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15', '1,2,3', '0');
INSERT INTO `sea_member_group` (`gid`, `gname`, `gtype`, `g_auth`, `g_upgrade`) VALUES ('2', '普通会员', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15', '1,2,3', '10');

INSERT INTO `sea_jqtype` (`tid`, `tname`, `ishidden`) VALUES
(1, '解密', 0),
(2, '乡村', 0),
(3, '都市', 0),
(4, '少儿', 0),
(5, '对话', 0),
(6, '搞笑', 0);
