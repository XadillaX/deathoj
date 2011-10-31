CREATE DATABASE `OnlineJudge`;

-- OJ管理员角色表
CREATE TABLE IF NOT EXISTS `oj_role`(
  `roleid` INT NOT NULL AUTO_INCREMENT,
  `rolename` VARCHAR(32) NOT NULL DEFAULT '',
  `privilege` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleid`),
  UNIQUE KEY `rolename` (`rolename`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 用户表
CREATE TABLE IF NOT EXISTS `oj_user`(
    `userid` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(32) NOT NULL DEFAULT '',
    `password` CHAR(32) NOT NULL,
    `roleid` INT NOT NULL DEFAULT 1,
    `nickname` VARCHAR(32) NOT NULL,
    `regtime` INT NOT NULL,
    `solved` INT NOT NULL DEFAULT 0,
    `submit` INT NOT NULL DEFAULT 0,
    `school` VARCHAR(255) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `motto` TEXT NOT NULL DEFAULT '',
    
    PRIMARY KEY(`userid`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `nickname` (`nickname`),
    FOREIGN KEY `roleid` (`roleid`) REFERENCES `oj_role` (`roleid`) ON DELETE SET NULL
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 语言表
CREATE TABLE IF NOT EXISTS `oj_language`(
    `languageid` INT NOT NULL AUTO_INCREMENT,
    `language` VARCHAR(32) NOT NULL DEFAULT '',
    
    PRIMARY KEY(`languageid`),
    UNIQUE KEY `language` (`language`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 题目表
CREATE TABLE IF NOT EXISTS `oj_problem`(
    `problemid` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT NOT NULL DEFAULT '',
    `sampleinput` TEXT NOT NULL DEFAULT '',
    `sampleoutput` TEXT NOT NULL DEFAULT '',
    `hint` TEXT NOT NULL DEFAULT '',
    `source` TEXT NOT NULL DEFAULT '',
    `addtime` INT NOT NULL DEFAULT 0,
    `timelimit` INT NOT NULL DEFAULT 1000,
    `memorylimit` INT NOT NULL DEFAULT 65535,
    
    PRIMARY KEY(`problemid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 比赛表
CREATE TABLE IF NOT EXISTS `oj_contest`(
    `contestid` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT NOT NULL DEFAULT '',
    `private` TINYINT NOT NULL DEFAULT 0,
    `starttime` INT DEFAULT NULL,
    `endtime` INT DEFAULT NULL,
    `addtime` INT DEFAULT 0 NOT NULL,
    
    -- 通过languageid以|分割
    `language` TEXT NOT NULL DEFAULT '',
    `submit` INT NOT NULL DEFAULT 0,
    
    PRIMARY KEY(`contestid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 比赛题目表
CREATE TABLE IF NOT EXISTS `oj_contestproblem`(
    `contestproblemid` INT NOT NULL AUTO_INCREMENT,
    `contestid` INT NOT NULL,
    `problemid` INT NOT NULL,
    `index` VARCHAR(32) NOT NULL DEFAULT '',
    `submit` INT NOT NULL DEFAULT 0,
    `solved` INT NOT NULL DEFAULT 0,
    
    PRIMARY KEY(`contestproblemid`),
    UNIQUE KEY `INDEX` (`contestid`, `index`),
    FOREIGN KEY `contestid` (`contestid`) REFERENCES `oj_contest`(`contestid`) ON DELETE CASCADE,
    FOREIGN KEY `problemid` (`problemid`) REFERENCES `oj_problem`(`problemid`) ON DELETE CASCADE
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 比赛用户表
CREATE TABLE IF NOT EXISTS `oj_contestuser`(
    `contestuserid` INT NOT NULL AUTO_INCREMENT,
    `contestid` INT NOT NULL,
    `userid` INT NOT NULL,
    
    PRIMARY KEY(`contestuserid`),
    FOREIGN KEY `contestid` (`contestid`) REFERENCES `oj_contest`(`contestid`) ON DELETE CASCADE,
    FOREIGN KEY `userid` (`userid`) REFERENCES `oj_user`(`userid`) ON DELETE CASCADE,
    UNIQUE KEY `INDEX` (`contestid`, `userid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 问题结果文本表
CREATE TABLE IF NOT EXISTS `oj_result`(
    `resultid` INT NOT NULL,
    `result` VARCHAR(32) NOT NULL,
    
    PRIMARY KEY(`resultid`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 0;

-- 问题提交表
CREATE TABLE IF NOT EXISTS `oj_submit`(
    `totsubmitid` INT NOT NULL AUTO_INCREMENT,
    `submitid` INT NOT NULL,
    `contestid` INT NOT NULL,
    `problemid` INT NOT NULL,
    `userid` INT NOT NULL,
    
    `time` INT NOT NULL DEFAULT 0,
    `memory` INT NOT NULL DEFAULT 0,
    `length` INT NOT NULL DEFAULT 0,
    
    `submittime` INT NOT NULL DEFAULT 0,
    `languageid` INT NOT NULL DEFAULT 0,
    `resultid` INT DEFAULT NULL,
    
    PRIMARY KEY(`totsubmitid`),
    UNIQUE KEY `INDEX` (`submitid`, `contestid`),
    FOREIGN KEY `contestid` (`contestid`) REFERENCES `oj_contest`(`contestid`) ON DELETE CASCADE,
    FOREIGN KEY `problemid` (`problemid`) REFERENCES `oj_problem`(`problemid`) ON DELETE CASCADE,
    FOREIGN KEY `userid` (`userid`) REFERENCES `oj_user`(`userid`) ON DELETE CASCADE,
    FOREIGN KEY `resultid` (`resultid`) REFERENCES `oj_result`(`resultid`) ON DELETE SET NULL,
    FOREIGN KEY `languageid` (`languageid`) REFERENCES `oj_language`(`languageid`) ON DELETE SET NULL
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 代码表
CREATE TABLE IF NOT EXISTS `oj_code`(
    `totsubmitid` INT NOT NULL,
    `code` TEXT NOT NULL DEFAULT '',
    
    PRIMARY KEY(`totsubmitid`),
    FOREIGN KEY `sid` (`totsubmitid`) REFERENCES `oj_submit` ON DELETE CASCADE
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- CE信息
CREATE TABLE IF NOT EXISTS `oj_codeerror`(
    `totsubmitid` INT NOT NULL,
    `content` TEXT NOT NULL DEFAULT '',
    
    PRIMARY KEY(`totsubmitid`),
    FOREIGN KEY `sid` (`totsubmitid`) REFERENCES `oj_submit` ON DELETE CASCADE
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 配置表信息
CREATE TABLE IF NOT EXISTS `oj_config`(
    `configid` INT NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(255) NOT NULL DEFAULT '',
    `value` TEXT NOT NULL DEFAULT '',
    
    PRIMARY KEY(`configid`),
    UNIQUE KEY `key` (`key`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 AUTO_INCREMENT = 1;

-- 初始数据
INSERT INTO `oj_result`(`resultid`, `result`) VALUES
(0, 'QUEUING'),
(1, 'COMPILING'),
(2, 'RUNNING'),
(3, 'ACCEPTED'),
(4, 'PRESENTATION_ERROR'),
(5, 'WRONG_ANSWER'),
(6, 'RUNTIME_ERROR'),
(7, 'TIME_LIMIT_EXCEEDED'),
(8, 'TIME_LIMIT_EXCEEDED'),
(9, 'MEMORY_LIMIT_EXCEEDED'),
(10, 'OUTPUT_LIMIT_EXCEEDED'),
(11, 'COMPILATION_ERROR'),
(12, 'COMPILATION_SUC'),
(13, 'SYSTEM_ERROR'),
(14, 'OUT_OF_CONTEST_TIME');

INSERT INTO `oj_language`(`language`) VALUES
('GCC'),
('G++');

INSERT INTO `oj_contest`(`title`, `description`, `language`) VALUES
('Practice', 'The problems for practice.', '1|2');

INSERT INTO `oj_role`(`rolename`, `privilege`) VALUES
('USER', 0),
('EDITOR', 0),
('ADMIN', 0);
