DROP TABLE IF EXISTS `sea_admin`;
CREATE TABLE `sea_admin` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `logincount` smallint(6) NOT NULL default '0',
  `loginip` varchar(16) NOT NULL default '',
  `logintime` int(10) NOT NULL default '0',
  `groupid` smallint(4) NOT NULL,
  `state` smallint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_arcrank`;
CREATE TABLE `sea_arcrank` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `rank` smallint(6) NOT NULL default '0',
  `membername` char(20) NOT NULL default '',
  `adminrank` smallint(6) NOT NULL default '0',
  `money` smallint(8) unsigned NOT NULL default '500',
  `scores` mediumint(8) NOT NULL default '0',
  `purviews` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_comment`;
CREATE TABLE `sea_comment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `v_id` mediumint(8) unsigned NOT NULL default '0',
  `typeid` smallint(5) unsigned NOT NULL default '0',
  `username` char(20) NOT NULL default '',
  `ip` char(15) NOT NULL default '',
  `ischeck` smallint(6) NOT NULL default '0',
  `dtime` int(10) unsigned NOT NULL default '0',
  `msg` text,
  `m_type` int(6) unsigned NOT NULL default '0',
  `reply` int(6) unsigned NOT NULL default '0',
  `agree` int(6) unsigned NOT NULL default '0',
  `anti` int(6) unsigned NOT NULL default '0',
  `pic` char(255) NOT NULL default '',
  `vote` int(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `v_id` (`v_id`,`ischeck`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_content`;
CREATE TABLE `sea_content` (
  `v_id` mediumint(8) NOT NULL default '0',
  `tid` smallint(8) unsigned NOT NULL default '0',
  `body` mediumtext,
  PRIMARY KEY  (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_count`;
CREATE TABLE `sea_count` (
  `id` int(11) NOT NULL auto_increment,
  `userip` varchar(16) default NULL,
  `serverurl` varchar(255) default NULL,
  `updatetime` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sea_co_cls`;
CREATE TABLE `sea_co_cls` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `clsname` varchar(50) NOT NULL default '',
  `sysclsid` smallint(5) unsigned NOT NULL default '0',
  `cotype` tinyint(4) NOT NULL default '0',  
  PRIMARY KEY  (`id`),
  KEY `sysclsid` (`sysclsid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_co_config`;
CREATE TABLE `sea_co_config` (
  `cid` mediumint(8) unsigned NOT NULL auto_increment,
  `cname` varchar(50) NOT NULL default '',
  `getlistnum` int(10) NOT NULL default '0',
  `getconnum` int(10) NOT NULL default '0',
  `cotype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_co_data`;
CREATE TABLE `sea_co_data` (
  `v_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` smallint(8) unsigned NOT NULL default '0',
  `tname` char(60) NOT NULL default '',
  `v_name` char(60) NOT NULL default '',
  `v_state` int(10) unsigned NOT NULL default '0',
  `v_pic` char(255) NOT NULL default '',
  `v_spic` char(255) NOT NULL default '',
  `v_gpic` char(255) NOT NULL default '',
  `v_hit` mediumint(8) unsigned NOT NULL default '0',
  `v_money` smallint(6) NOT NULL default '0',
  `v_rank` smallint(6) NOT NULL default '0',
  `v_digg` smallint(6) NOT NULL default '0',
  `v_tread` smallint(6) NOT NULL default '0',
  `v_commend` smallint(6) NOT NULL default '0',
  `v_wrong` smallint(8) unsigned NOT NULL default '0',
  `v_director` varchar(200) NOT NULL default '',
  `v_enname` varchar(200) NOT NULL default '',
  `v_lang` varchar(200) NOT NULL default '',
  `v_actor` varchar(200) NOT NULL default '',
  `v_color` char(7) NOT NULL default '',
  `v_publishyear` char(20) NOT NULL default '0',
  `v_publisharea` char(20) NOT NULL default '',
  `v_addtime` int(10) unsigned NOT NULL default '0',
  `v_topic` mediumint(8) unsigned NOT NULL default '0',
  `v_note` char(30) NOT NULL default '',
  `v_tags` char(30) NOT NULL default '',
  `v_letter` char(3) NOT NULL default '',
  `v_from` char(255) NOT NULL default '',
  `v_inbase` enum('0','1') NOT NULL default '0',
  `v_des` text,
  `v_playdata` text,
  `v_downdata` text,
  PRIMARY KEY  (`v_id`),
  KEY `tid` (`v_rank`,`tid`,`v_commend`,`v_hit`),
  KEY `v_addtime` (`v_addtime`,`v_digg`,`v_tread`,`v_inbase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_co_news`;
CREATE TABLE `sea_co_news` (
  `n_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` smallint(8) unsigned NOT NULL default '0',
  `n_title` char(60) NOT NULL default '',
  `n_keyword` varchar(80) default NULL,
  `n_pic` char(255) NOT NULL default '',
  `n_hit` mediumint(8) unsigned NOT NULL default '0',
  `n_author` varchar(80) default NULL,
  `n_addtime` int(10) NOT NULL default '0',
  `n_letter` char(3) NOT NULL default '',
  `n_content` mediumtext,
  `n_outline` char(255) default NULL,
  `tname` char(60) NOT NULL default '',
  `n_from` char(50) NOT NULL default '',
  `n_inbase` enum('0','1') NOT NULL default '0',
  `n_entitle` varchar(100) default NULL,
  PRIMARY KEY  (`n_id`),
  KEY `tid` (`tid`,`n_hit`),
  KEY `v_addtime` (`n_inbase`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `sea_co_filters`;
CREATE TABLE `sea_co_filters` (
  `ID` mediumint(8) NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL,
  `rColumn` tinyint(1) NOT NULL,
  `uesMode` tinyint(1) NOT NULL,
  `sFind` varchar(255) NOT NULL,
  `sStart` varchar(255) NOT NULL,
  `sEnd` varchar(255) NOT NULL,
  `sReplace` varchar(255) NOT NULL,
  `Flag` tinyint(1) NOT NULL,
  `cotype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `sea_co_type`;
CREATE TABLE `sea_co_type` (
  `tid` mediumint(8) unsigned NOT NULL auto_increment,
  `cid` smallint(5) unsigned NOT NULL default '0',
  `tname` varchar(50) NOT NULL default '',
  `siteurl` char(200) NOT NULL default '',
  `getherday` smallint(5) unsigned NOT NULL default '0',
  `playfrom` varchar(50) NOT NULL default '',
  `autocls` enum('0','1') NOT NULL default '0',
  `classid` smallint(5) unsigned NOT NULL default '0',
  `coding` varchar(10) NOT NULL default 'gb2312',
  `sock` enum('0','1') NOT NULL default '0',
  `addtime` int(10) unsigned NOT NULL default '0',
  `cjtime` int(10) unsigned NOT NULL default '0',
  `listconfig` text,
  `itemconfig` text,
  `isok` tinyint(1) unsigned NOT NULL default '0',
  `cotype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  KEY `cid` (`cid`,`classid`),
  KEY `addtime` (`addtime`,`cjtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_co_url`;
CREATE TABLE `sea_co_url` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `cid` smallint(5) unsigned NOT NULL default '0',
  `tid` smallint(5) unsigned NOT NULL default '0',
  `url` char(255) NOT NULL default '',
  `pic` char(255) NOT NULL default '',
  `succ` enum('0','1') NOT NULL default '0',
  `err` int(5) NOT NULL default '0',
  `cotype` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `cid` (`cid`,`tid`),
  KEY `succ` (`succ`,`err`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_crons`;
CREATE TABLE `sea_crons` (
  `cronid` smallint(6) unsigned NOT NULL auto_increment,
  `available` tinyint(1) NOT NULL default '0',
  `type` enum('user','system') NOT NULL default 'user',
  `name` char(50) NOT NULL default '',
  `filename` char(255) NOT NULL default '',
  `lastrun` int(10) unsigned NOT NULL default '0',
  `nextrun` int(10) unsigned NOT NULL default '0',
  `weekday` tinyint(1) NOT NULL default '0',
  `day` tinyint(2) NOT NULL default '0',
  `hour` tinyint(2) NOT NULL default '0',
  `minute` char(36) NOT NULL default '',
  PRIMARY KEY  (`cronid`),
  KEY `nextrun` (`available`,`nextrun`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_data`;
CREATE TABLE `sea_data` (
  `v_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` smallint(8) unsigned NOT NULL default '0',
  `v_name` char(60) NOT NULL default '',
  `v_state` int(10) unsigned NOT NULL default '0',
  `v_pic` char(255) NOT NULL default '',
  `v_spic` char(255) NOT NULL default '',
  `v_gpic` char(255) NOT NULL default '',
  `v_hit` mediumint(8) unsigned NOT NULL default '0',
  `v_money` smallint(6) NOT NULL default '0',
  `v_rank` smallint(6) NOT NULL default '0',
  `v_digg` smallint(6) NOT NULL default '0',
  `v_tread` smallint(6) NOT NULL default '0',
  `v_commend` smallint(6) NOT NULL default '0',
  `v_wrong` smallint(8) unsigned NOT NULL default '0',
  `v_ismake` smallint(1) unsigned NOT NULL default '0',
  `v_actor` varchar(200) default NULL,
  `v_color` char(7) NOT NULL default '',
  `v_publishyear` int(10) NULL default '0',
  `v_publisharea` char(20) NOT NULL default '',
  `v_addtime` int(10) unsigned NOT NULL default '0',
  `v_topic` mediumint(8) unsigned NOT NULL default '0',
  `v_note` char(30) NOT NULL default '',
  `v_tags` char(30) NOT NULL default '',
  `v_letter` char(3) NOT NULL default '',
  `v_isunion` smallint(6) NOT NULL default '0',
  `v_recycled` smallint(6) NOT NULL default '0',
  `v_director` varchar(200) default NULL,
  `v_enname` varchar(200) default NULL,
  `v_lang` varchar(200) default NULL,
  `v_score` int(10) NULL default '0',
  `v_scorenum` int(10) default '0',
  `v_extratype` text,
  `v_jq` text,
  `v_nickname` CHAR( 60 ) NULL ,
  `v_reweek` CHAR( 60 ) NULL ,
  `v_douban` FLOAT NULL default '0',
  `v_mtime` FLOAT NULL default '0',
  `v_imdb` FLOAT NULL default '0',
  `v_tvs` CHAR( 60 ) NULL ,
  `v_company` CHAR( 60 ) NULL ,
  `v_dayhit` INT( 10 ) NULL ,
  `v_weekhit` INT( 10 ) NULL ,
  `v_monthhit` INT( 10 ) NULL ,
  `v_daytime` INT( 10 ) NULL ,
  `v_weektime` INT( 10 ) NULL ,
  `v_monthtime` INT( 10 ) NULL ,
  `v_len` VARCHAR( 6 ) NULL ,
  `v_total` VARCHAR( 6 ) NULL ,
  `v_ver` VARCHAR( 20 ) NULL ,
  `v_psd` VARCHAR( 30 ) NULL ,
  `v_longtxt` text ,
  PRIMARY KEY  (`v_id`),
  KEY `idx_tid` (`tid`,`v_recycled`,`v_addtime`),
  KEY `idx_addtime` (`v_addtime`),
  KEY `idx_name` (`v_name`,`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE sea_favorite(
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` int(11) NOT NULL default 0,
  `vid` int(11) NOT NULL default 0,
  `kptime` int(10) NOT NULL default 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_erradd`;
CREATE TABLE `sea_erradd` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vid` mediumint(8) unsigned NOT NULL,
  `author` char(60) NOT NULL default '',
  `ip` char(15) NOT NULL default '',
  `errtxt` mediumtext,
  `sendtime` int(10) unsigned NOT NULL default '0',
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_flink`;
CREATE TABLE `sea_flink` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `sortrank` smallint(6) NOT NULL default '0',
  `url` char(60) NOT NULL default '',
  `webname` char(30) NOT NULL default '',
  `msg` char(200) NOT NULL default '',
  `email` char(50) NOT NULL default '',
  `logo` char(60) NOT NULL default '',
  `dtime` int(10) unsigned NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_guestbook`;
CREATE TABLE `sea_guestbook` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `mid` mediumint(8) unsigned default '0',
  `posttime` int(10) unsigned NOT NULL default '0',
  `uname` varchar(30) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `dtime` int(10) unsigned NOT NULL default '0',
  `ischeck` smallint(6) NOT NULL default '1',
  `msg` text,
  PRIMARY KEY  (`id`),
  KEY `ischeck` (`ischeck`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_myad`;
CREATE TABLE `sea_myad` (
  `aid` mediumint(8) unsigned NOT NULL auto_increment,
  `adname` varchar(100) NOT NULL default '',
  `adenname` varchar(60) NOT NULL default '',
  `timeset` int(10) unsigned NOT NULL default '0',
  `intro` char(255) NOT NULL default '',
  `adsbody` text,
  PRIMARY KEY  (`aid`),
  KEY `timeset` (`timeset`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_mytag`;
CREATE TABLE `sea_mytag` (
  `aid` mediumint(8) unsigned NOT NULL auto_increment,
  `tagname` varchar(30) NOT NULL default '',
  `tagdes` varchar(50) NOT NULL default '0',
  `addtime` int(10) unsigned NOT NULL default '0',
  `tagcontent` text,
  PRIMARY KEY  (`aid`),
  KEY `tagname` (`tagname`,`addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_news`;
CREATE TABLE `sea_news` (
  `n_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` smallint(8) unsigned NOT NULL default '0',
  `n_title` char(255) NOT NULL default '',
  `n_pic` char(255) NOT NULL default '',
  `n_hit` mediumint(8) unsigned NOT NULL default '0',
  `n_money` smallint(6) NOT NULL default '0',
  `n_rank` smallint(6) NOT NULL default '0',
  `n_digg` smallint(6) NOT NULL default '0',
  `n_tread` smallint(6) NOT NULL default '0',
  `n_commend` smallint(6) NOT NULL default '0',
  `n_author` varchar(80) default NULL,
  `n_color` char(7) NOT NULL default '',
  `n_addtime` int(10) unsigned NOT NULL default '0',
  `n_note` smallint(6) NOT NULL default '0',
  `n_letter` char(3) NOT NULL default '',
  `n_isunion` smallint(6) NOT NULL default '0',
  `n_recycled` smallint(6) NOT NULL default '0',
  `n_entitle` varchar(200) default NULL,
  `n_outline` varchar(255) default NULL,
  `n_keyword` varchar(80) default NULL,
  `n_from` varchar(50) default NULL,
  `n_score` bigint(10) default '0',
  `n_scorenum` int(10) default '0',
  `n_content` mediumtext,
  PRIMARY KEY  (`n_id`),
  KEY `tid` (`n_rank`,`tid`,`n_commend`,`n_hit`),
  KEY `v_addtime` (`n_addtime`,`n_digg`,`n_tread`,`n_isunion`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_playdata`;
CREATE TABLE `sea_playdata` (
  `v_id` mediumint(8) NOT NULL default '0',
  `tid` smallint(8) unsigned NOT NULL default '0',
  `body` mediumtext,
  `body1` mediumtext,
  PRIMARY KEY  (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_search_keywords`;
CREATE TABLE `sea_search_keywords` (
  `aid` mediumint(8) unsigned NOT NULL auto_increment,
  `keyword` char(30) NOT NULL default '',
  `spwords` char(50) NOT NULL default '',
  `count` mediumint(8) unsigned NOT NULL default '1',
  `result` mediumint(8) unsigned NOT NULL default '0',
  `lasttime` int(10) unsigned NOT NULL default '0',
  `tid` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`aid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_tags`;
CREATE TABLE `sea_tags` (
  `tagid` int(11) unsigned NOT NULL auto_increment,
  `tag` char(30) NOT NULL default '',
  `usenum` mediumint(6) unsigned NOT NULL default '0',
  `vids` text NOT NULL,
  PRIMARY KEY  (`tagid`),
  KEY `usenum` (`usenum`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_temp`;
CREATE TABLE `sea_temp` (
  `v_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` smallint(8) unsigned NOT NULL default '0',
  `v_name` char(60) NOT NULL default '',
  `v_state` int(10) unsigned NOT NULL default '0',
  `v_pic` char(100) NOT NULL default '',
  `v_actor` varchar(200) default NULL,
  `v_publishyear` char(20) NOT NULL default '0',
  `v_publisharea` char(20) NOT NULL default '',
  `v_addtime` int(10) unsigned NOT NULL default '0',
  `v_note` char(30) NOT NULL default '',
  `v_letter` char(3) NOT NULL default '',
  `v_playdata` mediumtext,
  `v_des` mediumtext,
  `v_director` varchar(200) default NULL,
  `v_enname` varchar(200) default NULL,
  `v_lang` varchar(200) default NULL,
  PRIMARY KEY  (`v_id`),
  KEY `tid` (`tid`),
  KEY `v_addtime` (`v_addtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_topic`;
CREATE TABLE `sea_topic` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `name` char(30) NOT NULL default '',
  `enname` char(60) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `template` char(50) NOT NULL default '',
  `pic` char(80) NOT NULL default '',
  `des` text,
  `vod` text NOT NULL,
  `keyword` TEXT NULL,
  PRIMARY KEY  (`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_type`;
CREATE TABLE `sea_type` (
  `tid` smallint(6) unsigned NOT NULL auto_increment,
  `upid` tinyint(6) unsigned NOT NULL default '0',
  `tname` char(30) NOT NULL default '',
  `tenname` char(60) NOT NULL default '',
  `torder` int(11) NOT NULL default '0',
  `templist` char(50) NOT NULL default '',
  `templist_1` char(50) NOT NULL default '',
  `title` char(50) NOT NULL default '',
  `keyword` char(50) NOT NULL default '',
  `description` char(50) NOT NULL default '',
  `ishidden` smallint(6) NOT NULL default '0',
  `unionid` mediumtext,
  `tptype` smallint(1) NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  KEY `upid` (`upid`,`ishidden`),
  KEY `torder` (`torder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_jqtype`;
CREATE TABLE `sea_jqtype` (
  `tid` smallint(6) unsigned NOT NULL auto_increment,
  `tname` char(30) NOT NULL default '',
  `ishidden` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_member`;
CREATE TABLE `sea_member` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `nickname` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` char(255) NOT NULL default '',
  `logincount` smallint(6) NOT NULL default '0',
  `regip` varchar(16) NOT NULL default '',
  `regtime` int(10) NOT NULL default '0',
  `gid` smallint(4) NOT NULL,
  `points` int(10) NOT NULL default '0', 
  `state` smallint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_member_group`;
CREATE TABLE `sea_member_group` (
  `gid` int(11) unsigned NOT NULL auto_increment,
  `gname` varchar(32) NOT NULL default '',
  `gtype` varchar(255) NOT NULL default '',
  `g_auth` varchar(32) NOT NULL default '',
  `g_upgrade` int(11) NOT NULL default '0',
  `g_authvalue` int(11) NOT NULL default '0',
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_buy`;
CREATE TABLE IF NOT EXISTS `sea_buy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `vid` int(11) NOT NULL DEFAULT '0',
  `kptime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sea_cck`;
CREATE TABLE IF NOT EXISTS `sea_cck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ckey` varchar(80) NOT NULL,
  `climit` int(11) NOT NULL,
  `maketime` timestamp NULL DEFAULT NULL,
  `usetime` timestamp NULL DEFAULT NULL,
  `uname` varchar(20) DEFAULT NULL,
  `status` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `sea_ie`;
CREATE TABLE IF NOT EXISTS `sea_ie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `addtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;