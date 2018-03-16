create database seacms;
use seacms;
-- MySQL dump 10.13  Distrib 5.5.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: seacms
-- ------------------------------------------------------
-- Server version	5.5.54-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Table structure for table `sea_admin`
--

DROP TABLE IF EXISTS `sea_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_admin` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `logincount` smallint(6) NOT NULL DEFAULT '0',
  `loginip` varchar(16) NOT NULL DEFAULT '',
  `logintime` int(10) NOT NULL DEFAULT '0',
  `groupid` smallint(4) NOT NULL,
  `state` smallint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_admin`
--

LOCK TABLES `sea_admin` WRITE;
/*!40000 ALTER TABLE `sea_admin` DISABLE KEYS */;
INSERT INTO `sea_admin` VALUES (1,'feifei','d8601ff38987b19d2721',0,'127.0.0.1',1496720039,1,1);
/*!40000 ALTER TABLE `sea_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_arcrank`
--

DROP TABLE IF EXISTS `sea_arcrank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_arcrank` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `rank` smallint(6) NOT NULL DEFAULT '0',
  `membername` char(20) NOT NULL DEFAULT '',
  `adminrank` smallint(6) NOT NULL DEFAULT '0',
  `money` smallint(8) unsigned NOT NULL DEFAULT '500',
  `scores` mediumint(8) NOT NULL DEFAULT '0',
  `purviews` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_arcrank`
--

LOCK TABLES `sea_arcrank` WRITE;
/*!40000 ALTER TABLE `sea_arcrank` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_arcrank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_buy`
--

DROP TABLE IF EXISTS `sea_buy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_buy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `vid` int(11) NOT NULL DEFAULT '0',
  `kptime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_buy`
--

LOCK TABLES `sea_buy` WRITE;
/*!40000 ALTER TABLE `sea_buy` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_buy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_cck`
--

DROP TABLE IF EXISTS `sea_cck`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_cck` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_cck`
--

LOCK TABLES `sea_cck` WRITE;
/*!40000 ALTER TABLE `sea_cck` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_cck` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_cls`
--

DROP TABLE IF EXISTS `sea_co_cls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_cls` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `clsname` varchar(50) NOT NULL DEFAULT '',
  `sysclsid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cotype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sysclsid` (`sysclsid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_cls`
--

LOCK TABLES `sea_co_cls` WRITE;
/*!40000 ALTER TABLE `sea_co_cls` DISABLE KEYS */;
INSERT INTO `sea_co_cls` VALUES (1,'大陆',0,0),(2,'香港',0,0),(3,'台湾',0,0),(4,'日本',0,0),(5,'韩国',0,0),(6,'欧美',0,0),(7,'日韩',0,0),(8,'中国',0,0);
/*!40000 ALTER TABLE `sea_co_cls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_config`
--

DROP TABLE IF EXISTS `sea_co_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_config` (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) NOT NULL DEFAULT '',
  `getlistnum` int(10) NOT NULL DEFAULT '0',
  `getconnum` int(10) NOT NULL DEFAULT '0',
  `cotype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_config`
--

LOCK TABLES `sea_co_config` WRITE;
/*!40000 ALTER TABLE `sea_co_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_data`
--

DROP TABLE IF EXISTS `sea_co_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_data` (
  `v_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `tname` char(60) NOT NULL DEFAULT '',
  `v_name` char(60) NOT NULL DEFAULT '',
  `v_state` int(10) unsigned NOT NULL DEFAULT '0',
  `v_pic` char(255) NOT NULL DEFAULT '',
  `v_spic` char(255) NOT NULL DEFAULT '',
  `v_gpic` char(255) NOT NULL DEFAULT '',
  `v_hit` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `v_money` smallint(6) NOT NULL DEFAULT '0',
  `v_rank` smallint(6) NOT NULL DEFAULT '0',
  `v_digg` smallint(6) NOT NULL DEFAULT '0',
  `v_tread` smallint(6) NOT NULL DEFAULT '0',
  `v_commend` smallint(6) NOT NULL DEFAULT '0',
  `v_wrong` smallint(8) unsigned NOT NULL DEFAULT '0',
  `v_director` varchar(200) NOT NULL DEFAULT '',
  `v_enname` varchar(200) NOT NULL DEFAULT '',
  `v_lang` varchar(200) NOT NULL DEFAULT '',
  `v_actor` varchar(200) NOT NULL DEFAULT '',
  `v_color` char(7) NOT NULL DEFAULT '',
  `v_publishyear` char(20) NOT NULL DEFAULT '0',
  `v_publisharea` char(20) NOT NULL DEFAULT '',
  `v_addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `v_topic` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `v_note` char(30) NOT NULL DEFAULT '',
  `v_tags` char(30) NOT NULL DEFAULT '',
  `v_letter` char(3) NOT NULL DEFAULT '',
  `v_from` char(255) NOT NULL DEFAULT '',
  `v_inbase` enum('0','1') NOT NULL DEFAULT '0',
  `v_des` text,
  `v_playdata` text,
  `v_downdata` text,
  PRIMARY KEY (`v_id`),
  KEY `tid` (`v_rank`,`tid`,`v_commend`,`v_hit`),
  KEY `v_addtime` (`v_addtime`,`v_digg`,`v_tread`,`v_inbase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_data`
--

LOCK TABLES `sea_co_data` WRITE;
/*!40000 ALTER TABLE `sea_co_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_filters`
--

DROP TABLE IF EXISTS `sea_co_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_filters` (
  `ID` mediumint(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `rColumn` tinyint(1) NOT NULL,
  `uesMode` tinyint(1) NOT NULL,
  `sFind` varchar(255) NOT NULL,
  `sStart` varchar(255) NOT NULL,
  `sEnd` varchar(255) NOT NULL,
  `sReplace` varchar(255) NOT NULL,
  `Flag` tinyint(1) NOT NULL,
  `cotype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_filters`
--

LOCK TABLES `sea_co_filters` WRITE;
/*!40000 ALTER TABLE `sea_co_filters` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_filters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_news`
--

DROP TABLE IF EXISTS `sea_co_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_news` (
  `n_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `n_title` char(60) NOT NULL DEFAULT '',
  `n_keyword` varchar(80) DEFAULT NULL,
  `n_pic` char(255) NOT NULL DEFAULT '',
  `n_hit` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `n_author` varchar(80) DEFAULT NULL,
  `n_addtime` int(10) NOT NULL DEFAULT '0',
  `n_letter` char(3) NOT NULL DEFAULT '',
  `n_content` mediumtext,
  `n_outline` char(255) DEFAULT NULL,
  `tname` char(60) NOT NULL DEFAULT '',
  `n_from` char(50) NOT NULL DEFAULT '',
  `n_inbase` enum('0','1') NOT NULL DEFAULT '0',
  `n_entitle` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`n_id`),
  KEY `tid` (`tid`,`n_hit`),
  KEY `v_addtime` (`n_inbase`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_news`
--

LOCK TABLES `sea_co_news` WRITE;
/*!40000 ALTER TABLE `sea_co_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_type`
--

DROP TABLE IF EXISTS `sea_co_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_type` (
  `tid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tname` varchar(50) NOT NULL DEFAULT '',
  `siteurl` char(200) NOT NULL DEFAULT '',
  `getherday` smallint(5) unsigned NOT NULL DEFAULT '0',
  `playfrom` varchar(50) NOT NULL DEFAULT '',
  `autocls` enum('0','1') NOT NULL DEFAULT '0',
  `classid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coding` varchar(10) NOT NULL DEFAULT 'gb2312',
  `sock` enum('0','1') NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `cjtime` int(10) unsigned NOT NULL DEFAULT '0',
  `listconfig` text,
  `itemconfig` text,
  `isok` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cotype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `cid` (`cid`,`classid`),
  KEY `addtime` (`addtime`,`cjtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_type`
--

LOCK TABLES `sea_co_type` WRITE;
/*!40000 ALTER TABLE `sea_co_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_co_url`
--

DROP TABLE IF EXISTS `sea_co_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_co_url` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `url` char(255) NOT NULL DEFAULT '',
  `pic` char(255) NOT NULL DEFAULT '',
  `succ` enum('0','1') NOT NULL DEFAULT '0',
  `err` int(5) NOT NULL DEFAULT '0',
  `cotype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `cid` (`cid`,`tid`),
  KEY `succ` (`succ`,`err`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_co_url`
--

LOCK TABLES `sea_co_url` WRITE;
/*!40000 ALTER TABLE `sea_co_url` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_co_url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_comment`
--

DROP TABLE IF EXISTS `sea_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `v_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `username` char(20) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  `ischeck` smallint(6) NOT NULL DEFAULT '0',
  `dtime` int(10) unsigned NOT NULL DEFAULT '0',
  `msg` text,
  `m_type` int(6) unsigned NOT NULL DEFAULT '0',
  `reply` int(6) unsigned NOT NULL DEFAULT '0',
  `agree` int(6) unsigned NOT NULL DEFAULT '0',
  `anti` int(6) unsigned NOT NULL DEFAULT '0',
  `pic` char(255) NOT NULL DEFAULT '',
  `vote` int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `v_id` (`v_id`,`ischeck`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_comment`
--

LOCK TABLES `sea_comment` WRITE;
/*!40000 ALTER TABLE `sea_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_content`
--

DROP TABLE IF EXISTS `sea_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_content` (
  `v_id` mediumint(8) NOT NULL DEFAULT '0',
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `body` mediumtext,
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_content`
--

LOCK TABLES `sea_content` WRITE;
/*!40000 ALTER TABLE `sea_content` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_count`
--

DROP TABLE IF EXISTS `sea_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userip` varchar(16) DEFAULT NULL,
  `serverurl` varchar(255) DEFAULT NULL,
  `updatetime` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_count`
--

LOCK TABLES `sea_count` WRITE;
/*!40000 ALTER TABLE `sea_count` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_count` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_crons`
--

DROP TABLE IF EXISTS `sea_crons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_crons` (
  `cronid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `available` tinyint(1) NOT NULL DEFAULT '0',
  `type` enum('user','system') NOT NULL DEFAULT 'user',
  `name` char(50) NOT NULL DEFAULT '',
  `filename` char(255) NOT NULL DEFAULT '',
  `lastrun` int(10) unsigned NOT NULL DEFAULT '0',
  `nextrun` int(10) unsigned NOT NULL DEFAULT '0',
  `weekday` tinyint(1) NOT NULL DEFAULT '0',
  `day` tinyint(2) NOT NULL DEFAULT '0',
  `hour` tinyint(2) NOT NULL DEFAULT '0',
  `minute` char(36) NOT NULL DEFAULT '',
  PRIMARY KEY (`cronid`),
  KEY `nextrun` (`available`,`nextrun`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_crons`
--

LOCK TABLES `sea_crons` WRITE;
/*!40000 ALTER TABLE `sea_crons` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_crons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_data`
--

DROP TABLE IF EXISTS `sea_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_data` (
  `v_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `v_name` char(60) NOT NULL DEFAULT '',
  `v_state` int(10) unsigned NOT NULL DEFAULT '0',
  `v_pic` char(255) NOT NULL DEFAULT '',
  `v_spic` char(255) NOT NULL DEFAULT '',
  `v_gpic` char(255) NOT NULL DEFAULT '',
  `v_hit` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `v_money` smallint(6) NOT NULL DEFAULT '0',
  `v_rank` smallint(6) NOT NULL DEFAULT '0',
  `v_digg` smallint(6) NOT NULL DEFAULT '0',
  `v_tread` smallint(6) NOT NULL DEFAULT '0',
  `v_commend` smallint(6) NOT NULL DEFAULT '0',
  `v_wrong` smallint(8) unsigned NOT NULL DEFAULT '0',
  `v_ismake` smallint(1) unsigned NOT NULL DEFAULT '0',
  `v_actor` varchar(200) DEFAULT NULL,
  `v_color` char(7) NOT NULL DEFAULT '',
  `v_publishyear` int(10) DEFAULT '0',
  `v_publisharea` char(20) NOT NULL DEFAULT '',
  `v_addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `v_topic` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `v_note` char(30) NOT NULL DEFAULT '',
  `v_tags` char(30) NOT NULL DEFAULT '',
  `v_letter` char(3) NOT NULL DEFAULT '',
  `v_isunion` smallint(6) NOT NULL DEFAULT '0',
  `v_recycled` smallint(6) NOT NULL DEFAULT '0',
  `v_director` varchar(200) DEFAULT NULL,
  `v_enname` varchar(200) DEFAULT NULL,
  `v_lang` varchar(200) DEFAULT NULL,
  `v_score` int(10) DEFAULT '0',
  `v_scorenum` int(10) DEFAULT '0',
  `v_extratype` text,
  `v_jq` text,
  `v_nickname` char(60) DEFAULT NULL,
  `v_reweek` char(60) DEFAULT NULL,
  `v_douban` float DEFAULT '0',
  `v_mtime` float DEFAULT '0',
  `v_imdb` float DEFAULT '0',
  `v_tvs` char(60) DEFAULT NULL,
  `v_company` char(60) DEFAULT NULL,
  `v_dayhit` int(10) DEFAULT NULL,
  `v_weekhit` int(10) DEFAULT NULL,
  `v_monthhit` int(10) DEFAULT NULL,
  `v_daytime` int(10) DEFAULT NULL,
  `v_weektime` int(10) DEFAULT NULL,
  `v_monthtime` int(10) DEFAULT NULL,
  `v_len` varchar(6) DEFAULT NULL,
  `v_total` varchar(6) DEFAULT NULL,
  `v_ver` varchar(20) DEFAULT NULL,
  `v_psd` varchar(30) DEFAULT NULL,
  `v_longtxt` text,
  PRIMARY KEY (`v_id`),
  KEY `idx_tid` (`tid`,`v_recycled`,`v_addtime`),
  KEY `idx_addtime` (`v_addtime`),
  KEY `idx_name` (`v_name`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_data`
--

LOCK TABLES `sea_data` WRITE;
/*!40000 ALTER TABLE `sea_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_erradd`
--

DROP TABLE IF EXISTS `sea_erradd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_erradd` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vid` mediumint(8) unsigned NOT NULL,
  `author` char(60) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL DEFAULT '',
  `errtxt` mediumtext,
  `sendtime` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_erradd`
--

LOCK TABLES `sea_erradd` WRITE;
/*!40000 ALTER TABLE `sea_erradd` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_erradd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_favorite`
--

DROP TABLE IF EXISTS `sea_favorite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_favorite` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `vid` int(11) NOT NULL DEFAULT '0',
  `kptime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_favorite`
--

LOCK TABLES `sea_favorite` WRITE;
/*!40000 ALTER TABLE `sea_favorite` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_favorite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_flink`
--

DROP TABLE IF EXISTS `sea_flink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_flink` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `sortrank` smallint(6) NOT NULL DEFAULT '0',
  `url` char(60) NOT NULL DEFAULT '',
  `webname` char(30) NOT NULL DEFAULT '',
  `msg` char(200) NOT NULL DEFAULT '',
  `email` char(50) NOT NULL DEFAULT '',
  `logo` char(60) NOT NULL DEFAULT '',
  `dtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ischeck` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_flink`
--

LOCK TABLES `sea_flink` WRITE;
/*!40000 ALTER TABLE `sea_flink` DISABLE KEYS */;
INSERT INTO `sea_flink` VALUES (1,0,'http://www.seacms.net','海洋cms','','','',1432312055,1);
/*!40000 ALTER TABLE `sea_flink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_guestbook`
--

DROP TABLE IF EXISTS `sea_guestbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_guestbook` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(60) NOT NULL DEFAULT '',
  `mid` mediumint(8) unsigned DEFAULT '0',
  `posttime` int(10) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `dtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ischeck` smallint(6) NOT NULL DEFAULT '1',
  `msg` text,
  PRIMARY KEY (`id`),
  KEY `ischeck` (`ischeck`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_guestbook`
--

LOCK TABLES `sea_guestbook` WRITE;
/*!40000 ALTER TABLE `sea_guestbook` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_guestbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_ie`
--

DROP TABLE IF EXISTS `sea_ie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_ie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `addtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_ie`
--

LOCK TABLES `sea_ie` WRITE;
/*!40000 ALTER TABLE `sea_ie` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_ie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_jqtype`
--

DROP TABLE IF EXISTS `sea_jqtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_jqtype` (
  `tid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `tname` char(30) NOT NULL DEFAULT '',
  `ishidden` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_jqtype`
--

LOCK TABLES `sea_jqtype` WRITE;
/*!40000 ALTER TABLE `sea_jqtype` DISABLE KEYS */;
INSERT INTO `sea_jqtype` VALUES (1,'解密',0),(2,'乡村',0),(3,'都市',0),(4,'少儿',0),(5,'对话',0),(6,'搞笑',0);
/*!40000 ALTER TABLE `sea_jqtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_member`
--

DROP TABLE IF EXISTS `sea_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '',
  `nickname` varchar(20) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `email` char(255) NOT NULL DEFAULT '',
  `logincount` smallint(6) NOT NULL DEFAULT '0',
  `regip` varchar(16) NOT NULL DEFAULT '',
  `regtime` int(10) NOT NULL DEFAULT '0',
  `gid` smallint(4) NOT NULL,
  `points` int(10) NOT NULL DEFAULT '0',
  `state` smallint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_member`
--

LOCK TABLES `sea_member` WRITE;
/*!40000 ALTER TABLE `sea_member` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_member_group`
--

DROP TABLE IF EXISTS `sea_member_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_member_group` (
  `gid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gname` varchar(32) NOT NULL DEFAULT '',
  `gtype` varchar(255) NOT NULL DEFAULT '',
  `g_auth` varchar(32) NOT NULL DEFAULT '',
  `g_upgrade` int(11) NOT NULL DEFAULT '0',
  `g_authvalue` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_member_group`
--

LOCK TABLES `sea_member_group` WRITE;
/*!40000 ALTER TABLE `sea_member_group` DISABLE KEYS */;
INSERT INTO `sea_member_group` VALUES (1,'匿名用户','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','1,2,3',0,0),(2,'普通会员','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','1,2,3',10,0);
/*!40000 ALTER TABLE `sea_member_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_myad`
--

DROP TABLE IF EXISTS `sea_myad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_myad` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `adname` varchar(100) NOT NULL DEFAULT '',
  `adenname` varchar(60) NOT NULL DEFAULT '',
  `timeset` int(10) unsigned NOT NULL DEFAULT '0',
  `intro` char(255) NOT NULL DEFAULT '',
  `adsbody` text,
  PRIMARY KEY (`aid`),
  KEY `timeset` (`timeset`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_myad`
--

LOCK TABLES `sea_myad` WRITE;
/*!40000 ALTER TABLE `sea_myad` DISABLE KEYS */;
INSERT INTO `sea_myad` VALUES (1,'channel200x200bt','channel200x200bt',1344397254,'排行榜右下200x200','document.writeln(\"<div class=\\\"right_b_neirong\\\"><\\/div>\")'),(2,'channel200x200top','channel200x200top',1344397271,'排行榜右上200x200','document.writeln(\"<div class=\\\"right_t_neirong\\\"><\\/div>\")'),(3,'channel728x90','channel728x90',1344397299,'频道页上方728x90','document.writeln(\"<div class=\\\"guanggao_neirong\\\"><\\/div>\")'),(4,'content360x300','content360x300',1344397346,'内容页右侧360x300','document.writeln(\"<div class=\\\"gao_neirong\\\"><\\/div>\")'),(5,'content460x60','content460x60',1344397379,'内容页460x60','document.writeln(\"<div class=\\\"xia_ad_neirong\\\"><\\/div>\")'),(6,'foot960x90','foot960x90',1344397396,'底部960x90','document.writeln(\"<div class=\\\"ad_neirong\\\"><\\/div>\")'),(7,'head960x90','head960x90',1344397422,'导航下方960x90','document.writeln(\"<div class=\\\"topad_neirong\\\"><\\/div>\")'),(8,'index728x90','index728x90',1344397439,'首页728x90','document.writeln(\"<div class=\\\"rm_ad_neirong\\\"><\\/div>\")'),(9,'play300x250top','play300x250top',1344397508,'播放页右上300x250','document.writeln(\"<div class=\\\"play_ad_topneirong\\\"><\\/div>\")'),(10,'play300x250bt','play300x250bt',1344397533,'播放页右下300x250','document.writeln(\"<div class=\\\"play_ad_btneirong\\\"><\\/div>\")');
/*!40000 ALTER TABLE `sea_myad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_mytag`
--

DROP TABLE IF EXISTS `sea_mytag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_mytag` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tagname` varchar(30) NOT NULL DEFAULT '',
  `tagdes` varchar(50) NOT NULL DEFAULT '0',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `tagcontent` text,
  PRIMARY KEY (`aid`),
  KEY `tagname` (`tagname`,`addtime`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_mytag`
--

LOCK TABLES `sea_mytag` WRITE;
/*!40000 ALTER TABLE `sea_mytag` DISABLE KEYS */;
INSERT INTO `sea_mytag` VALUES (1,'areasearch','地区搜索',1251590919,'<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=大陆\' target=\"_blank\">大陆</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=香港\'target=\"_blank\">香港</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=台湾\'target=\"_blank\">台湾</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=日本\' target=\"_blank\">日本</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=韩国\' target=\"_blank\">韩国</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=欧美\' target=\"_blank\">欧美</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=2&searchword=其它\' target=\"_blank\">其它</a>'),(2,'yearsearch','按发行年份查看电影',1251509338,'<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2009\' target=\"_blank\">2009</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2008\'target=\"_blank\">2008</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2007\' target=\"_blank\">2007</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2006\' target=\"_blank\">2006</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2005\' target=\"_blank\">2005</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2004\' target=\"_blank\">2004</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2003\' target=\"_blank\">2003</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2002\' target=\"_blank\">2002</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=3&searchword=2001\' target=\"_blank\">2001</a>'),(3,'actorsearch','演员名字',1251590973,'<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=成龙\' target=\"_blank\">成龙</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周星驰\'target=\"_blank\">周星驰</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周润发\'target=\"_blank\">周润发</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=舒淇\' target=\"_blank\">舒淇</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=葛优\' target=\"_blank\">葛优</a> \r\n<a href=\'/{seacms:sitepath}search.php?searchtype=1&searchword=周杰伦\' target=\"_blank\">周杰伦</a> '),(4,'nav_bottom_banner','导航栏下方通栏广告',1251591021,'aaaaaaaaaaaaaaaaaaaaaa\r\n$$$\r\nbbbbbbbbbbbbbbbbbbbbbb\r\n$$$\r\neeeeeeeeeeeeeeeeeeeeee');
/*!40000 ALTER TABLE `sea_mytag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_news`
--

DROP TABLE IF EXISTS `sea_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_news` (
  `n_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `n_title` char(255) NOT NULL DEFAULT '',
  `n_pic` char(255) NOT NULL DEFAULT '',
  `n_hit` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `n_money` smallint(6) NOT NULL DEFAULT '0',
  `n_rank` smallint(6) NOT NULL DEFAULT '0',
  `n_digg` smallint(6) NOT NULL DEFAULT '0',
  `n_tread` smallint(6) NOT NULL DEFAULT '0',
  `n_commend` smallint(6) NOT NULL DEFAULT '0',
  `n_author` varchar(80) DEFAULT NULL,
  `n_color` char(7) NOT NULL DEFAULT '',
  `n_addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `n_note` smallint(6) NOT NULL DEFAULT '0',
  `n_letter` char(3) NOT NULL DEFAULT '',
  `n_isunion` smallint(6) NOT NULL DEFAULT '0',
  `n_recycled` smallint(6) NOT NULL DEFAULT '0',
  `n_entitle` varchar(200) DEFAULT NULL,
  `n_outline` varchar(255) DEFAULT NULL,
  `n_keyword` varchar(80) DEFAULT NULL,
  `n_from` varchar(50) DEFAULT NULL,
  `n_score` bigint(10) DEFAULT '0',
  `n_scorenum` int(10) DEFAULT '0',
  `n_content` mediumtext,
  PRIMARY KEY (`n_id`),
  KEY `tid` (`n_rank`,`tid`,`n_commend`,`n_hit`),
  KEY `v_addtime` (`n_addtime`,`n_digg`,`n_tread`,`n_isunion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_news`
--

LOCK TABLES `sea_news` WRITE;
/*!40000 ALTER TABLE `sea_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_playdata`
--

DROP TABLE IF EXISTS `sea_playdata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_playdata` (
  `v_id` mediumint(8) NOT NULL DEFAULT '0',
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `body` mediumtext,
  `body1` mediumtext,
  PRIMARY KEY (`v_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_playdata`
--

LOCK TABLES `sea_playdata` WRITE;
/*!40000 ALTER TABLE `sea_playdata` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_playdata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_search_keywords`
--

DROP TABLE IF EXISTS `sea_search_keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_search_keywords` (
  `aid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` char(30) NOT NULL DEFAULT '',
  `spwords` char(50) NOT NULL DEFAULT '',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `result` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0',
  `tid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_search_keywords`
--

LOCK TABLES `sea_search_keywords` WRITE;
/*!40000 ALTER TABLE `sea_search_keywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_search_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_tags`
--

DROP TABLE IF EXISTS `sea_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_tags` (
  `tagid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tag` char(30) NOT NULL DEFAULT '',
  `usenum` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `vids` text NOT NULL,
  PRIMARY KEY (`tagid`),
  KEY `usenum` (`usenum`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_tags`
--

LOCK TABLES `sea_tags` WRITE;
/*!40000 ALTER TABLE `sea_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_temp`
--

DROP TABLE IF EXISTS `sea_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_temp` (
  `v_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` smallint(8) unsigned NOT NULL DEFAULT '0',
  `v_name` char(60) NOT NULL DEFAULT '',
  `v_state` int(10) unsigned NOT NULL DEFAULT '0',
  `v_pic` char(100) NOT NULL DEFAULT '',
  `v_actor` varchar(200) DEFAULT NULL,
  `v_publishyear` char(20) NOT NULL DEFAULT '0',
  `v_publisharea` char(20) NOT NULL DEFAULT '',
  `v_addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `v_note` char(30) NOT NULL DEFAULT '',
  `v_letter` char(3) NOT NULL DEFAULT '',
  `v_playdata` mediumtext,
  `v_des` mediumtext,
  `v_director` varchar(200) DEFAULT NULL,
  `v_enname` varchar(200) DEFAULT NULL,
  `v_lang` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`v_id`),
  KEY `tid` (`tid`),
  KEY `v_addtime` (`v_addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_temp`
--

LOCK TABLES `sea_temp` WRITE;
/*!40000 ALTER TABLE `sea_temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_topic`
--

DROP TABLE IF EXISTS `sea_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_topic` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) NOT NULL DEFAULT '',
  `enname` char(60) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  `template` char(50) NOT NULL DEFAULT '',
  `pic` char(80) NOT NULL DEFAULT '',
  `des` text,
  `vod` text NOT NULL,
  `keyword` text,
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_topic`
--

LOCK TABLES `sea_topic` WRITE;
/*!40000 ALTER TABLE `sea_topic` DISABLE KEYS */;
/*!40000 ALTER TABLE `sea_topic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sea_type`
--

DROP TABLE IF EXISTS `sea_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sea_type` (
  `tid` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `upid` tinyint(6) unsigned NOT NULL DEFAULT '0',
  `tname` char(30) NOT NULL DEFAULT '',
  `tenname` char(60) NOT NULL DEFAULT '',
  `torder` int(11) NOT NULL DEFAULT '0',
  `templist` char(50) NOT NULL DEFAULT '',
  `templist_1` char(50) NOT NULL DEFAULT '',
  `title` char(50) NOT NULL DEFAULT '',
  `keyword` char(50) NOT NULL DEFAULT '',
  `description` char(50) NOT NULL DEFAULT '',
  `ishidden` smallint(6) NOT NULL DEFAULT '0',
  `unionid` mediumtext,
  `tptype` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `upid` (`upid`,`ishidden`),
  KEY `torder` (`torder`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sea_type`
--

LOCK TABLES `sea_type` WRITE;
/*!40000 ALTER TABLE `sea_type` DISABLE KEYS */;
INSERT INTO `sea_type` VALUES (1,0,'新闻','xinwen',1,'channel.html','content.html','','','',0,'31_1',0),(2,0,'娱乐','yule',2,'channel.html','content.html','','','',0,'',0),(3,0,'军事','junshi',3,'channel.html','content.html','','','',0,'',0),(4,0,'猎奇','lieqi',4,'channel.html','content.html','','','',0,'',0),(5,1,'体育','tiyu',5,'channel.html','content.html','','','',0,'',0),(6,1,'汽车','qiche',6,'channel.html','content.html','','','',0,'',0),(7,1,'科技','keji',7,'channel.html','content.html','','','',0,'',0),(8,1,'财经','caijing',8,'channel.html','content.html','','','',0,'',0),(9,1,'股市','gushi',9,'channel.html','content.html','','','',0,'',0),(10,1,'地方','difang',10,'channel.html','content.html','','','',0,'',0),(11,1,'母婴','muying',11,'channel.html','content.html','','','',0,'',0),(12,1,'生活','shenghuo',12,'channel.html','content.html','','','',0,'',0),(13,2,'明星','mingxing',13,'channel.html','content.html','','','',0,'',0),(14,2,'音乐','yinyue',14,'channel.html','content.html','','','',0,'',0),(15,2,'游戏','youxi',15,'channel.html','content.html','','','',0,'',0),(16,2,'原创','yuanchuang',16,'channel.html','content.html','','','',0,'',0),(17,0,'国内','guonei',17,'newspage.html','news.html','','','',0,'',1),(18,0,'国际','guoji',18,'newspage.html','news.html','','','',0,'',1),(19,0,'社会','shehui',19,'newspage.html','news.html','','','',0,'',1),(20,0,'军事','junshi',20,'newspage.html','news.html','','','',0,'',1),(21,0,'娱乐','yule',21,'newspage.html','news.html','','','',0,'',1),(22,0,'八卦','bagua',22,'newspage.html','news.html','','','',0,'',1),(23,0,'科技','keji',23,'newspage.html','news.html','','','',0,'',1),(24,0,'财经','caijing',24,'newspage.html','news.html','','','',0,'',1),(25,0,'公益','gongyi',25,'newspage.html','news.html','','','',0,'',1),(26,0,'评论','pinglun',26,'newspage.html','news.html','','','',0,'',1),(27,0,'时尚','shishang',27,'newspage.html','news.html','','','',0,'',1);
/*!40000 ALTER TABLE `sea_type` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-06 11:40:50
