<?php
return array(
    /** 是否开启应用调试模式 */
    APP_DEBUG               => false,

    /** 模块分组之间的分隔符 */
    APP_GROUP_DEPR          => ".",

    /** 项目模块分组列表，多个组之间用逗号分隔 */
    APP_GROUP_LIST          => "",

    /** URL模式：2为rewrite */
    URL_MODEL               => 2,
    URL_HTML_SUFFIX         => ".xhtml",
    URL_ROUTER_ON           => true,

    /** 数据库配置 */
    DB_CHARSET              => "utf8",
    DB_DEPLOY_TYPE          => 0,
    
    /** 开启数据表字段缓存 */
    DB_FIELDS_CACHE         => true,
    DB_TYPE                 => "mysql",
    DB_HOST                 => "localhost",
    DB_NAME                 => "onlinejudge",
    DB_USER                 => "root",
    DB_PWD                  => "deathmoon", //deathmoon 这份文件如果只是修改数据库连接密码，就不要提交了……2个人的都不一样～
    DB_PORT                 => 3306,
    DB_PREFIX               => "oj_",

    /** 运行时配置 */
    SHOW_RUN_TIME           => true,
    SHOW_ADV_TIME           => false,
    SHOW_DB_TIMES           => true,
	SHOW_USE_MEM			=> false,
    SHOW_PAGE_TRACE         => false,

    /** 令牌设置 */
    TOKEN_ON                => true,
    TOKEN_NAME              => "__OJVERIFY__",

    /** 默认设置 */
    DEFAULT_GROUP           => "Home",

    /** 加密解密配置 */
    ENCRYPTION_KEY          => "XADILLAX",

    /** WEB_DIR */
    WEB_ROOT_DIR            => "/",

    /** 输入、输出数据地址 */
    MAX_DATA_SIZE           => 20480,
    IO_DATA_DIR             => "\\__data#\\",
    RANK_DIR                => "\\__rank#\\",
	TEAM_DIR                => "/oj/__team#",
    IO_DATA_PATH            => "D:\\xampp\\htdocs\\oj\\__data#\\",
    RANK_PATH               => "D:\\xampp\\htdocs\\oj\\__rank#\\",
	TEAM_PATH               => dirname(dirname(dirname(__FILE__))) . "\\__team#",
	ROOT_PATH               => dirname(dirname(dirname(__FILE__))) . "\\",

    /** 分页设置 */
    PROBLEM_NUM_PER_PAGE    => 20,
    PROBLEM_SET_PER_PAGE    => 100,
    STATUS_NUM_PER_PAGE     => 20,
    USER_NUM_PER_PAGE       => 10,
    CONTEST_NUM_PER_PAGE    => 20
);
?>
