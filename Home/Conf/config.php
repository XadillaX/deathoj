<?php
return array(
    /** 是否开启应用调试模式 */
    APP_DEBUG               => true,

    /** 模块分组之间的分隔符 */
    APP_GROUP_DEPR          => ".",

    /** 项目模块分组列表，多个组之间用逗号分隔 */
    APP_GROUP_LIST          => "",

    /** URL模式：2为rewrite */
    URL_MODEL               => 2,
    URL_HTML_SUFFIX         => ".xhtml",

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
    SHOW_RUN_TIME           => false,
    SHOW_ADV_TIME           => true,
    SHOW_DB_TIMES           => true,
    SHOW_PAGE_TRACE         => true,

    /** 令牌设置 */
    TOKEN_ON                => true,
    TOKEN_NAME              => "__OJVERIFY__",

    /** 默认设置 */
    DEFAULT_GROUP           => "Home",

    /** 加密解密配置 */
    ENCRYPTION_KEY          => "XADILLAX"
);
?>
