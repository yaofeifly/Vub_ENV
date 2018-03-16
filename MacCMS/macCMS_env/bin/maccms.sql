create database maccms;
use maccms;

/*
Navicat MySQL Data Transfer

Source Server         : 192.168.159.131
Source Server Version : 50554
Source Host           : 192.168.159.131:3306
Source Database       : maccms

Target Server Type    : MYSQL
Target Server Version : 50554
File Encoding         : 65001

Date: 2017-06-15 19:07:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mac_art
-- ----------------------------
DROP TABLE IF EXISTS `mac_art`;
CREATE TABLE `mac_art` (
  `a_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_name` varchar(255) NOT NULL,
  `a_subname` varchar(255) NOT NULL,
  `a_enname` varchar(255) NOT NULL,
  `a_letter` char(1) NOT NULL,
  `a_color` char(6) NOT NULL,
  `a_from` varchar(32) NOT NULL,
  `a_author` varchar(32) NOT NULL,
  `a_tag` varchar(64) NOT NULL,
  `a_pic` varchar(255) NOT NULL,
  `a_type` smallint(6) NOT NULL DEFAULT '0',
  `a_topic` varchar(255) NOT NULL,
  `a_level` tinyint(1) NOT NULL DEFAULT '0',
  `a_hide` tinyint(1) NOT NULL DEFAULT '0',
  `a_lock` tinyint(1) NOT NULL DEFAULT '0',
  `a_up` mediumint(8) NOT NULL DEFAULT '0',
  `a_down` mediumint(8) NOT NULL DEFAULT '0',
  `a_hits` mediumint(8) NOT NULL DEFAULT '0',
  `a_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `a_addtime` int(10) NOT NULL,
  `a_time` int(10) NOT NULL,
  `a_hitstime` int(10) NOT NULL,
  `a_maketime` int(10) NOT NULL,
  `a_remarks` varchar(255) NOT NULL,
  `a_content` mediumtext NOT NULL,
  PRIMARY KEY (`a_id`),
  KEY `a_type` (`a_type`),
  KEY `a_level` (`a_level`),
  KEY `a_hits` (`a_hits`),
  KEY `a_dayhits` (`a_dayhits`),
  KEY `a_weekhits` (`a_weekhits`),
  KEY `a_monthhits` (`a_monthhits`),
  KEY `a_addtime` (`a_addtime`),
  KEY `a_time` (`a_time`),
  KEY `a_maketime` (`a_maketime`),
  KEY `a_hide` (`a_hide`),
  KEY `a_letter` (`a_letter`),
  KEY `a_down` (`a_down`),
  KEY `a_up` (`a_up`),
  KEY `a_tag` (`a_tag`),
  KEY `a_name` (`a_name`),
  KEY `a_enname` (`a_enname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_art
-- ----------------------------

-- ----------------------------
-- Table structure for mac_art_relation
-- ----------------------------
DROP TABLE IF EXISTS `mac_art_relation`;
CREATE TABLE `mac_art_relation` (
  `r_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `r_type` tinyint(1) NOT NULL DEFAULT '0',
  `r_a` mediumint(8) NOT NULL DEFAULT '0',
  `r_b` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `r_type` (`r_type`),
  KEY `r_a` (`r_a`),
  KEY `r_b` (`r_b`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_art_relation
-- ----------------------------

-- ----------------------------
-- Table structure for mac_art_topic
-- ----------------------------
DROP TABLE IF EXISTS `mac_art_topic`;
CREATE TABLE `mac_art_topic` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_tpl` varchar(128) NOT NULL,
  `t_pic` varchar(255) NOT NULL,
  `t_content` varchar(255) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_level` tinyint(1) NOT NULL DEFAULT '0',
  `t_up` mediumint(8) NOT NULL DEFAULT '0',
  `t_down` mediumint(8) NOT NULL DEFAULT '0',
  `t_score` decimal(3,1) NOT NULL,
  `t_scoreall` mediumint(8) NOT NULL,
  `t_scorenum` smallint(6) NOT NULL,
  `t_hits` mediumint(8) NOT NULL DEFAULT '0',
  `t_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_addtime` int(10) NOT NULL,
  `t_time` int(10) NOT NULL,
  `t_hitstime` int(10) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`),
  KEY `t_level` (`t_level`),
  KEY `t_up` (`t_up`),
  KEY `t_down` (`t_down`),
  KEY `t_score` (`t_score`),
  KEY `t_scoreall` (`t_scoreall`),
  KEY `t_scorenum` (`t_scorenum`),
  KEY `t_hits` (`t_hits`),
  KEY `t_dayhits` (`t_dayhits`),
  KEY `t_weekhits` (`t_weekhits`),
  KEY `t_monthhits` (`t_monthhits`),
  KEY `t_addtime` (`t_addtime`),
  KEY `t_time` (`t_time`),
  KEY `t_hitstime` (`t_hitstime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_art_topic
-- ----------------------------

-- ----------------------------
-- Table structure for mac_art_type
-- ----------------------------
DROP TABLE IF EXISTS `mac_art_type`;
CREATE TABLE `mac_art_type` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_pid` smallint(6) NOT NULL DEFAULT '0',
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_tpl` varchar(64) NOT NULL,
  `t_tpl_list` varchar(64) NOT NULL,
  `t_tpl_art` varchar(64) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_union` varchar(255) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_pid` (`t_pid`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_art_type
-- ----------------------------
INSERT INTO `mac_art_type` VALUES ('1', '站内新闻', 'zhanneixinwen', '0', '1', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
INSERT INTO `mac_art_type` VALUES ('2', '娱乐动态', 'yuledongtai', '0', '2', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
INSERT INTO `mac_art_type` VALUES ('3', '八卦爆料', 'baguabaoliao', '0', '3', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
INSERT INTO `mac_art_type` VALUES ('4', '影片资讯', 'yingpianzixun', '0', '4', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
INSERT INTO `mac_art_type` VALUES ('5', '明星资讯', 'mingxingzixun', '0', '5', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');
INSERT INTO `mac_art_type` VALUES ('6', '电视资讯', 'dianshizixun', '0', '6', '0', 'art_type.html', 'art_list.html', 'art_detail.html', '', '', '', '');

-- ----------------------------
-- Table structure for mac_comment
-- ----------------------------
DROP TABLE IF EXISTS `mac_comment`;
CREATE TABLE `mac_comment` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `c_type` int(11) DEFAULT '0',
  `c_vid` int(11) DEFAULT '0',
  `c_rid` int(11) DEFAULT '0',
  `c_hide` tinyint(1) DEFAULT '0',
  `c_name` varchar(64) NOT NULL,
  `c_ip` varchar(32) NOT NULL,
  `c_content` varchar(128) NOT NULL,
  `c_time` int(10) NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_vid` (`c_vid`),
  KEY `c_type` (`c_type`),
  KEY `c_rid` (`c_rid`),
  KEY `c_time` (`c_time`),
  KEY `c_hide` (`c_hide`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_comment
-- ----------------------------

-- ----------------------------
-- Table structure for mac_gbook
-- ----------------------------
DROP TABLE IF EXISTS `mac_gbook`;
CREATE TABLE `mac_gbook` (
  `g_id` int(11) NOT NULL AUTO_INCREMENT,
  `g_vid` int(11) DEFAULT '0',
  `g_hide` tinyint(1) DEFAULT '0',
  `g_sort` smallint(6) NOT NULL DEFAULT '0',
  `g_name` varchar(64) NOT NULL,
  `g_content` varchar(255) NOT NULL,
  `g_reply` varchar(255) NOT NULL,
  `g_ip` int(11) NOT NULL,
  `g_time` int(10) NOT NULL,
  `g_replytime` int(10) NOT NULL,
  PRIMARY KEY (`g_id`),
  KEY `g_vid` (`g_vid`),
  KEY `g_time` (`g_time`),
  KEY `g_hide` (`g_hide`),
  KEY `g_sort` (`g_sort`),
  KEY `g_replytime` (`g_replytime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_gbook
-- ----------------------------

-- ----------------------------
-- Table structure for mac_link
-- ----------------------------
DROP TABLE IF EXISTS `mac_link`;
CREATE TABLE `mac_link` (
  `l_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `l_name` varchar(64) NOT NULL,
  `l_url` varchar(255) NOT NULL,
  `l_logo` varchar(255) NOT NULL,
  `l_type` tinyint(1) NOT NULL DEFAULT '0',
  `l_sort` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`l_id`),
  KEY `l_sort` (`l_sort`),
  KEY `l_type` (`l_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_link
-- ----------------------------

-- ----------------------------
-- Table structure for mac_manager
-- ----------------------------
DROP TABLE IF EXISTS `mac_manager`;
CREATE TABLE `mac_manager` (
  `m_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `m_name` varchar(32) NOT NULL,
  `m_password` varchar(32) NOT NULL,
  `m_levels` varchar(32) NOT NULL,
  `m_random` varchar(32) NOT NULL,
  `m_status` tinyint(1) NOT NULL DEFAULT '0',
  `m_logintime` int(10) NOT NULL,
  `m_loginip` int(10) NOT NULL,
  `m_loginnum` smallint(6) NOT NULL,
  PRIMARY KEY (`m_id`),
  KEY `m_status` (`m_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_manager
-- ----------------------------
INSERT INTO `mac_manager` VALUES ('1', 'feifei', '693a801911d51c54cb48d830f01a6e47', 'b,c,d,e,f,g,h,i,j', 'bf84d6410e498fd4798816d217a24eb9', '1', '1497520809', '2147483647', '0');

-- ----------------------------
-- Table structure for mac_user
-- ----------------------------
DROP TABLE IF EXISTS `mac_user`;
CREATE TABLE `mac_user` (
  `u_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `u_qid` varchar(32) NOT NULL,
  `u_name` varchar(32) NOT NULL,
  `u_password` varchar(32) NOT NULL,
  `u_qq` varchar(16) NOT NULL,
  `u_email` varchar(32) NOT NULL,
  `u_phone` varchar(16) NOT NULL,
  `u_status` tinyint(1) NOT NULL DEFAULT '0',
  `u_flag` tinyint(1) NOT NULL DEFAULT '0',
  `u_question` varchar(255) NOT NULL,
  `u_answer` varchar(255) NOT NULL,
  `u_group` smallint(6) NOT NULL DEFAULT '0',
  `u_points` smallint(6) NOT NULL DEFAULT '0',
  `u_regtime` int(11) NOT NULL,
  `u_logintime` int(11) NOT NULL,
  `u_loginnum` smallint(6) NOT NULL DEFAULT '0',
  `u_extend` smallint(6) NOT NULL DEFAULT '0',
  `u_loginip` int(11) NOT NULL,
  `u_random` varchar(32) NOT NULL,
  `u_fav` text NOT NULL,
  `u_plays` text NOT NULL,
  `u_downs` text NOT NULL,
  `u_start` int(11) NOT NULL,
  `u_end` int(11) NOT NULL,
  PRIMARY KEY (`u_id`),
  KEY `u_group` (`u_group`),
  KEY `u_status` (`u_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_user
-- ----------------------------

-- ----------------------------
-- Table structure for mac_user_card
-- ----------------------------
DROP TABLE IF EXISTS `mac_user_card`;
CREATE TABLE `mac_user_card` (
  `c_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_number` varchar(16) NOT NULL,
  `c_pass` varchar(8) NOT NULL,
  `c_money` smallint(11) NOT NULL DEFAULT '0',
  `c_point` smallint(11) NOT NULL DEFAULT '0',
  `c_used` tinyint(1) NOT NULL DEFAULT '0',
  `c_sale` tinyint(1) NOT NULL DEFAULT '0',
  `c_user` smallint(6) NOT NULL DEFAULT '0',
  `c_addtime` int(11) NOT NULL,
  `c_usetime` int(11) NOT NULL,
  PRIMARY KEY (`c_id`),
  KEY `c_used` (`c_used`),
  KEY `c_sale` (`c_sale`),
  KEY `c_user` (`c_user`),
  KEY `c_addtime` (`c_addtime`),
  KEY `c_usetime` (`c_usetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_user_card
-- ----------------------------

-- ----------------------------
-- Table structure for mac_user_group
-- ----------------------------
DROP TABLE IF EXISTS `mac_user_group`;
CREATE TABLE `mac_user_group` (
  `ug_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `ug_name` varchar(32) NOT NULL,
  `ug_type` varchar(255) NOT NULL,
  `ug_popedom` varchar(32) NOT NULL,
  `ug_upgrade` smallint(6) NOT NULL DEFAULT '0',
  `ug_popvalue` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ug_id`),
  KEY `ug_upgrade` (`ug_upgrade`),
  KEY `ug_popvalue` (`ug_popvalue`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_user_group
-- ----------------------------
INSERT INTO `mac_user_group` VALUES ('1', '普通会员', '', '', '0', '1');

-- ----------------------------
-- Table structure for mac_user_pay
-- ----------------------------
DROP TABLE IF EXISTS `mac_user_pay`;
CREATE TABLE `mac_user_pay` (
  `p_id` int(11) NOT NULL AUTO_INCREMENT,
  `p_order` int(11) NOT NULL DEFAULT '0',
  `p_uid` mediumint(8) NOT NULL DEFAULT '0',
  `p_price` smallint(6) NOT NULL DEFAULT '0',
  `p_time` int(11) NOT NULL DEFAULT '0',
  `p_point` smallint(6) NOT NULL DEFAULT '0',
  `p_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`p_id`),
  KEY `p_order` (`p_order`),
  KEY `p_uid` (`p_uid`),
  KEY `p_status` (`p_status`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of mac_user_pay
-- ----------------------------

-- ----------------------------
-- Table structure for mac_user_visit
-- ----------------------------
DROP TABLE IF EXISTS `mac_user_visit`;
CREATE TABLE `mac_user_visit` (
  `uv_id` int(11) NOT NULL AUTO_INCREMENT,
  `uv_uid` int(11) DEFAULT '0',
  `uv_ip` int(11) NOT NULL,
  `uv_ly` varchar(128) NOT NULL,
  `uv_time` int(10) NOT NULL,
  PRIMARY KEY (`uv_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_user_visit
-- ----------------------------

-- ----------------------------
-- Table structure for mac_vod
-- ----------------------------
DROP TABLE IF EXISTS `mac_vod`;
CREATE TABLE `mac_vod` (
  `d_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `d_name` varchar(255) NOT NULL,
  `d_subname` varchar(255) NOT NULL,
  `d_enname` varchar(255) NOT NULL,
  `d_letter` char(1) NOT NULL,
  `d_color` char(6) NOT NULL,
  `d_pic` varchar(255) NOT NULL,
  `d_picthumb` varchar(255) NOT NULL,
  `d_picslide` varchar(255) NOT NULL,
  `d_starring` varchar(255) NOT NULL,
  `d_directed` varchar(255) NOT NULL,
  `d_tag` varchar(64) NOT NULL,
  `d_remarks` varchar(64) NOT NULL,
  `d_area` varchar(16) NOT NULL,
  `d_lang` varchar(16) NOT NULL,
  `d_year` smallint(4) NOT NULL,
  `d_type` smallint(6) NOT NULL DEFAULT '0',
  `d_type_expand` varchar(255) NOT NULL,
  `d_class` varchar(255) NOT NULL,
  `d_topic` varchar(255) NOT NULL DEFAULT '0',
  `d_hide` tinyint(1) NOT NULL DEFAULT '0',
  `d_lock` tinyint(1) NOT NULL,
  `d_state` int(8) NOT NULL DEFAULT '0',
  `d_level` tinyint(1) NOT NULL DEFAULT '0',
  `d_usergroup` smallint(6) NOT NULL DEFAULT '0',
  `d_stint` smallint(6) NOT NULL DEFAULT '0',
  `d_stintdown` smallint(6) NOT NULL DEFAULT '0',
  `d_hits` mediumint(8) NOT NULL DEFAULT '0',
  `d_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `d_duration` smallint(6) NOT NULL DEFAULT '0',
  `d_up` mediumint(8) NOT NULL DEFAULT '0',
  `d_down` mediumint(8) NOT NULL DEFAULT '0',
  `d_score` decimal(3,1) NOT NULL DEFAULT '0.0',
  `d_scoreall` mediumint(8) NOT NULL,
  `d_scorenum` smallint(6) NOT NULL DEFAULT '0',
  `d_addtime` int(10) NOT NULL,
  `d_time` int(10) NOT NULL,
  `d_hitstime` int(10) NOT NULL,
  `d_maketime` int(10) NOT NULL,
  `d_content` text NOT NULL,
  `d_playfrom` varchar(255) NOT NULL,
  `d_playserver` varchar(255) NOT NULL,
  `d_playnote` varchar(255) NOT NULL,
  `d_playurl` mediumtext NOT NULL,
  `d_downfrom` varchar(255) NOT NULL,
  `d_downserver` varchar(255) NOT NULL,
  `d_downnote` varchar(255) NOT NULL,
  `d_downurl` mediumtext NOT NULL,
  PRIMARY KEY (`d_id`),
  KEY `d_type` (`d_type`),
  KEY `d_state` (`d_state`),
  KEY `d_level` (`d_level`),
  KEY `d_hits` (`d_hits`),
  KEY `d_dayhits` (`d_dayhits`),
  KEY `d_weekhits` (`d_weekhits`),
  KEY `d_monthhits` (`d_monthhits`),
  KEY `d_stint` (`d_stint`),
  KEY `d_stintdown` (`d_stintdown`),
  KEY `d_hide` (`d_hide`),
  KEY `d_usergroup` (`d_usergroup`),
  KEY `d_score` (`d_score`),
  KEY `d_addtime` (`d_addtime`),
  KEY `d_time` (`d_time`),
  KEY `d_maketime` (`d_maketime`),
  KEY `d_topic` (`d_topic`),
  KEY `d_letter` (`d_letter`),
  KEY `d_name` (`d_name`),
  KEY `d_enname` (`d_enname`),
  KEY `d_year` (`d_year`),
  KEY `d_area` (`d_area`),
  KEY `d_language` (`d_lang`),
  KEY `d_starring` (`d_starring`),
  KEY `d_directed` (`d_directed`),
  KEY `d_tag` (`d_tag`),
  KEY `d_type_expand` (`d_type_expand`),
  KEY `d_class` (`d_class`),
  KEY `d_lock` (`d_lock`),
  KEY `d_up` (`d_up`),
  KEY `d_down` (`d_down`),
  KEY `d_scoreall` (`d_scoreall`),
  KEY `d_scorenum` (`d_scorenum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_vod
-- ----------------------------

-- ----------------------------
-- Table structure for mac_vod_class
-- ----------------------------
DROP TABLE IF EXISTS `mac_vod_class`;
CREATE TABLE `mac_vod_class` (
  `c_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `c_name` varchar(64) NOT NULL,
  `c_pid` smallint(6) NOT NULL DEFAULT '0',
  `c_sort` smallint(6) NOT NULL DEFAULT '0',
  `c_hide` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `c_sort` (`c_sort`),
  KEY `c_pid` (`c_pid`),
  KEY `c_hide` (`c_hide`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_vod_class
-- ----------------------------
INSERT INTO `mac_vod_class` VALUES ('1', '惊悚', '1', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('2', '悬疑', '1', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('3', '魔幻', '1', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('4', '罪案', '1', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('5', '灾难', '1', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('6', '动画', '1', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('7', '古装', '1', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('8', '青春', '1', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('9', '歌舞', '1', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('10', '文艺', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('11', '生活', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('12', '历史', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('13', '励志', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('14', '预告片', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('15', '言情', '2', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('16', '都市', '2', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('17', '家庭', '2', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('18', '生活', '2', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('19', '偶像', '2', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('20', '喜剧', '2', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('21', '历史', '2', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('22', '古装', '2', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('23', '武侠', '2', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('24', '刑侦', '2', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('25', '战争', '2', '11', '0');
INSERT INTO `mac_vod_class` VALUES ('26', '神话', '2', '12', '0');
INSERT INTO `mac_vod_class` VALUES ('27', '军旅', '2', '13', '0');
INSERT INTO `mac_vod_class` VALUES ('28', '谍战', '2', '14', '0');
INSERT INTO `mac_vod_class` VALUES ('29', '商战', '2', '15', '0');
INSERT INTO `mac_vod_class` VALUES ('30', '校园', '2', '16', '0');
INSERT INTO `mac_vod_class` VALUES ('31', '穿越', '2', '17', '0');
INSERT INTO `mac_vod_class` VALUES ('32', '悬疑', '2', '18', '0');
INSERT INTO `mac_vod_class` VALUES ('33', '犯罪', '2', '19', '0');
INSERT INTO `mac_vod_class` VALUES ('34', '科幻', '2', '20', '0');
INSERT INTO `mac_vod_class` VALUES ('35', '预告片', '2', '21', '0');
INSERT INTO `mac_vod_class` VALUES ('36', '脱口秀', '3', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('37', '真人秀', '3', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('38', '选秀', '3', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('39', '情感', '3', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('40', '访谈', '3', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('41', '时尚', '3', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('42', '晚会', '3', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('43', '财经', '3', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('44', '益智', '3', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('45', '音乐', '3', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('46', '游戏', '3', '11', '0');
INSERT INTO `mac_vod_class` VALUES ('47', '职场', '3', '12', '0');
INSERT INTO `mac_vod_class` VALUES ('48', '美食', '3', '13', '0');
INSERT INTO `mac_vod_class` VALUES ('49', '旅游', '3', '14', '0');
INSERT INTO `mac_vod_class` VALUES ('50', '冒险', '4', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('51', '热血', '4', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('52', '搞笑', '4', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('53', '少女', '4', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('54', '推理', '4', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('55', '竞技', '4', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('56', '益智', '4', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('57', '童话', '4', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('58', '经典', '4', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('59', '惊悚', '1', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('60', '悬疑', '1', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('61', '魔幻', '1', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('62', '罪案', '1', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('63', '灾难', '1', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('64', '动画', '1', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('65', '古装', '1', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('66', '青春', '1', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('67', '歌舞', '1', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('68', '文艺', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('69', '生活', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('70', '历史', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('71', '励志', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('72', '预告片', '1', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('73', '言情', '2', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('74', '都市', '2', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('75', '家庭', '2', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('76', '生活', '2', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('77', '偶像', '2', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('78', '喜剧', '2', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('79', '历史', '2', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('80', '古装', '2', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('81', '武侠', '2', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('82', '刑侦', '2', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('83', '战争', '2', '11', '0');
INSERT INTO `mac_vod_class` VALUES ('84', '神话', '2', '12', '0');
INSERT INTO `mac_vod_class` VALUES ('85', '军旅', '2', '13', '0');
INSERT INTO `mac_vod_class` VALUES ('86', '谍战', '2', '14', '0');
INSERT INTO `mac_vod_class` VALUES ('87', '商战', '2', '15', '0');
INSERT INTO `mac_vod_class` VALUES ('88', '校园', '2', '16', '0');
INSERT INTO `mac_vod_class` VALUES ('89', '穿越', '2', '17', '0');
INSERT INTO `mac_vod_class` VALUES ('90', '悬疑', '2', '18', '0');
INSERT INTO `mac_vod_class` VALUES ('91', '犯罪', '2', '19', '0');
INSERT INTO `mac_vod_class` VALUES ('92', '科幻', '2', '20', '0');
INSERT INTO `mac_vod_class` VALUES ('93', '预告片', '2', '21', '0');
INSERT INTO `mac_vod_class` VALUES ('94', '脱口秀', '3', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('95', '真人秀', '3', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('96', '选秀', '3', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('97', '情感', '3', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('98', '访谈', '3', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('99', '时尚', '3', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('100', '晚会', '3', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('101', '财经', '3', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('102', '益智', '3', '9', '0');
INSERT INTO `mac_vod_class` VALUES ('103', '音乐', '3', '10', '0');
INSERT INTO `mac_vod_class` VALUES ('104', '游戏', '3', '11', '0');
INSERT INTO `mac_vod_class` VALUES ('105', '职场', '3', '12', '0');
INSERT INTO `mac_vod_class` VALUES ('106', '美食', '3', '13', '0');
INSERT INTO `mac_vod_class` VALUES ('107', '旅游', '3', '14', '0');
INSERT INTO `mac_vod_class` VALUES ('108', '冒险', '4', '1', '0');
INSERT INTO `mac_vod_class` VALUES ('109', '热血', '4', '2', '0');
INSERT INTO `mac_vod_class` VALUES ('110', '搞笑', '4', '3', '0');
INSERT INTO `mac_vod_class` VALUES ('111', '少女', '4', '4', '0');
INSERT INTO `mac_vod_class` VALUES ('112', '推理', '4', '5', '0');
INSERT INTO `mac_vod_class` VALUES ('113', '竞技', '4', '6', '0');
INSERT INTO `mac_vod_class` VALUES ('114', '益智', '4', '7', '0');
INSERT INTO `mac_vod_class` VALUES ('115', '童话', '4', '8', '0');
INSERT INTO `mac_vod_class` VALUES ('116', '经典', '4', '9', '0');

-- ----------------------------
-- Table structure for mac_vod_relation
-- ----------------------------
DROP TABLE IF EXISTS `mac_vod_relation`;
CREATE TABLE `mac_vod_relation` (
  `r_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `r_type` tinyint(1) NOT NULL DEFAULT '0',
  `r_a` mediumint(8) NOT NULL DEFAULT '0',
  `r_b` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`r_id`),
  KEY `r_type` (`r_type`),
  KEY `r_a` (`r_a`),
  KEY `r_b` (`r_b`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_vod_relation
-- ----------------------------

-- ----------------------------
-- Table structure for mac_vod_topic
-- ----------------------------
DROP TABLE IF EXISTS `mac_vod_topic`;
CREATE TABLE `mac_vod_topic` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_tpl` varchar(128) NOT NULL,
  `t_pic` varchar(255) NOT NULL,
  `t_content` varchar(255) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_level` tinyint(1) NOT NULL DEFAULT '0',
  `t_up` mediumint(8) NOT NULL DEFAULT '0',
  `t_down` mediumint(8) NOT NULL DEFAULT '0',
  `t_score` decimal(3,1) NOT NULL,
  `t_scoreall` mediumint(8) NOT NULL,
  `t_scorenum` smallint(6) NOT NULL,
  `t_hits` mediumint(8) NOT NULL DEFAULT '0',
  `t_dayhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_weekhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_monthhits` mediumint(8) NOT NULL DEFAULT '0',
  `t_addtime` int(10) NOT NULL,
  `t_time` int(10) NOT NULL,
  `t_hitstime` int(10) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_hide` (`t_hide`),
  KEY `t_level` (`t_level`),
  KEY `t_up` (`t_up`),
  KEY `t_down` (`t_down`),
  KEY `t_score` (`t_score`),
  KEY `t_scoreall` (`t_scoreall`),
  KEY `t_scorenum` (`t_scorenum`),
  KEY `t_hits` (`t_hits`),
  KEY `t_dayhits` (`t_dayhits`),
  KEY `t_weekhits` (`t_weekhits`),
  KEY `t_monthhits` (`t_monthhits`),
  KEY `t_addtime` (`t_addtime`),
  KEY `t_time` (`t_time`),
  KEY `t_hitstime` (`t_hitstime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_vod_topic
-- ----------------------------

-- ----------------------------
-- Table structure for mac_vod_type
-- ----------------------------
DROP TABLE IF EXISTS `mac_vod_type`;
CREATE TABLE `mac_vod_type` (
  `t_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `t_name` varchar(64) NOT NULL,
  `t_enname` varchar(128) NOT NULL,
  `t_pid` smallint(6) NOT NULL DEFAULT '0',
  `t_sort` smallint(6) NOT NULL DEFAULT '0',
  `t_hide` tinyint(1) NOT NULL DEFAULT '0',
  `t_tpl` varchar(64) NOT NULL,
  `t_tpl_list` varchar(64) NOT NULL,
  `t_tpl_vod` varchar(64) NOT NULL,
  `t_tpl_play` varchar(64) NOT NULL,
  `t_tpl_down` varchar(64) NOT NULL,
  `t_key` varchar(255) NOT NULL,
  `t_des` varchar(255) NOT NULL,
  `t_title` varchar(255) NOT NULL,
  `t_union` varchar(255) NOT NULL,
  PRIMARY KEY (`t_id`),
  KEY `t_sort` (`t_sort`),
  KEY `t_pid` (`t_pid`),
  KEY `t_hide` (`t_hide`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mac_vod_type
-- ----------------------------
INSERT INTO `mac_vod_type` VALUES ('1', '电影', 'dianying', '0', '1', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('2', '连续剧', 'lianxuju', '0', '2', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('3', '综艺', 'zongyi', '0', '3', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('4', '动漫', 'dongman', '0', '4', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('5', '动作片', 'dongzuopian', '1', '11', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('6', '喜剧片', 'xijupian', '1', '12', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('7', '爱情片', 'aiqingpian', '1', '13', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('8', '科幻片', 'kehuanpian', '1', '14', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('9', '恐怖片', 'kongbupian', '1', '14', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('10', '剧情片', 'juqingpian', '1', '16', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('11', '战争片', 'zhanzhengpian', '1', '17', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('12', '国产剧', 'guochanju', '2', '21', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('13', '港台剧', 'gangtaiju', '2', '22', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('14', '日韩剧', 'rihanju', '2', '23', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');
INSERT INTO `mac_vod_type` VALUES ('15', '欧美剧', 'oumeiju', '2', '24', '0', 'vod_type.html', 'vod_list.html', 'vod_detail.html', 'vod_play.html', 'vod_down.html', '', '', '', '');

-- ----------------------------
-- Table structure for tmptable
-- ----------------------------
DROP TABLE IF EXISTS `tmptable`;
CREATE TABLE `tmptable` (
  `d_name1` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tmptable
-- ----------------------------
