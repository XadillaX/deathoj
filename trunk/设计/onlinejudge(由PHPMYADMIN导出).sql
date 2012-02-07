-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 02 月 07 日 17:04
-- 服务器版本: 5.1.41
-- PHP 版本: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `onlinejudge`
--

-- --------------------------------------------------------

--
-- 表的结构 `oj_code`
--

CREATE TABLE IF NOT EXISTS `oj_code` (
  `totsubmitid` int(11) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`totsubmitid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oj_config`
--

CREATE TABLE IF NOT EXISTS `oj_config` (
  `configid` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`configid`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_contest`
--

CREATE TABLE IF NOT EXISTS `oj_contest` (
  `contestid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  `language` text NOT NULL,
  `submit` int(11) NOT NULL DEFAULT '0',
  `resultversion` varchar(14) NOT NULL DEFAULT '00000000000000',
  PRIMARY KEY (`contestid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_contestproblem`
--

CREATE TABLE IF NOT EXISTS `oj_contestproblem` (
  `contestproblemid` int(11) NOT NULL AUTO_INCREMENT,
  `contestid` int(11) NOT NULL,
  `problemid` int(11) NOT NULL,
  `index` varchar(32) NOT NULL DEFAULT '',
  `submit` int(11) NOT NULL DEFAULT '0',
  `solved` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contestproblemid`),
  UNIQUE KEY `INDEX` (`contestid`,`index`),
  KEY `problemid` (`problemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_contestuser`
--

CREATE TABLE IF NOT EXISTS `oj_contestuser` (
  `contestuserid` int(11) NOT NULL AUTO_INCREMENT,
  `contestid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `teamname` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`contestuserid`),
  UNIQUE KEY `INDEX` (`contestid`,`userid`),
  UNIQUE KEY `teamname` (`teamname`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=105 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_language`
--

CREATE TABLE IF NOT EXISTS `oj_language` (
  `languageid` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`languageid`),
  UNIQUE KEY `language` (`language`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_problem`
--

CREATE TABLE IF NOT EXISTS `oj_problem` (
  `problemid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `input` text NOT NULL,
  `output` text NOT NULL,
  `sampleinput` text NOT NULL,
  `sampleoutput` text NOT NULL,
  `hint` text NOT NULL,
  `source` text NOT NULL,
  `addtime` int(11) NOT NULL DEFAULT '0',
  `timelimit` int(11) NOT NULL DEFAULT '1000',
  `memorylimit` int(11) NOT NULL DEFAULT '65535',
  `inputmd5` varchar(32) NOT NULL DEFAULT '',
  `outputmd5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`problemid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1021 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_result`
--

CREATE TABLE IF NOT EXISTS `oj_result` (
  `resultid` int(11) NOT NULL,
  `result` varchar(32) NOT NULL,
  PRIMARY KEY (`resultid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `oj_role`
--

CREATE TABLE IF NOT EXISTS `oj_role` (
  `roleid` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(32) NOT NULL DEFAULT '',
  `privilege` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleid`),
  UNIQUE KEY `rolename` (`rolename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_runtimeerror`
--

CREATE TABLE IF NOT EXISTS `oj_runtimeerror` (
  `totsubmitid` int(11) NOT NULL,
  `message` text CHARACTER SET utf8,
  PRIMARY KEY (`totsubmitid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `oj_submit`
--

CREATE TABLE IF NOT EXISTS `oj_submit` (
  `totsubmitid` int(11) NOT NULL AUTO_INCREMENT,
  `submitid` int(11) NOT NULL,
  `contestid` int(11) NOT NULL,
  `index` varchar(32) NOT NULL,
  `userid` int(11) NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `memory` int(11) NOT NULL DEFAULT '0',
  `length` int(11) NOT NULL DEFAULT '0',
  `submittime` int(11) NOT NULL DEFAULT '0',
  `languageid` int(11) NOT NULL DEFAULT '0',
  `resultid` int(11) DEFAULT NULL,
  PRIMARY KEY (`totsubmitid`),
  UNIQUE KEY `INDEX` (`submitid`,`contestid`),
  KEY `problemid` (`index`),
  KEY `userid` (`userid`),
  KEY `resultid` (`resultid`),
  KEY `languageid` (`languageid`),
  KEY `orderbytime` (`contestid`,`index`,`userid`,`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=174 ;

-- --------------------------------------------------------

--
-- 表的结构 `oj_user`
--

CREATE TABLE IF NOT EXISTS `oj_user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '要区分大小写。当等于32位时为MD5加密，小于等于16位时为未加密。',
  `roleid` int(11) NOT NULL DEFAULT '1',
  `nickname` varchar(32) NOT NULL,
  `regtime` int(11) NOT NULL,
  `solved` int(11) NOT NULL DEFAULT '0',
  `submit` int(11) NOT NULL DEFAULT '0',
  `school` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `motto` text NOT NULL,
  `language` int(11) NOT NULL,
  `submitlist` text NOT NULL,
  `solvedlist` text NOT NULL,
  `submitnum` int(11) NOT NULL DEFAULT '0',
  `solvednum` int(11) NOT NULL DEFAULT '0',
  `avatarbar` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
