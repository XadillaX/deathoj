<?php
/**
 * NBUT Online Judge System
 *
 * @author XadillaX(admin@xcoder.in)
 * @version $Id$
 * @copyright XadillaX 11-10-31 下午1:39
 */

/** 定义ThinkPHP框架路径 */
define("THINK_PATH", "ThinkPHP");

/** 定义项目名称和路径 */
define("APP_NAME", "OnlineJudge");
define("APP_PATH", "./Home");

/** 加载框架入口文件 */
require(THINK_PATH . "/ThinkPHP.php");

/** 实例化网站应用实例 */
App::run();

/** @version $Id$ */
//import("@.Plugin.Remove");
//$rmdir = new removeDir();
//$rmdir->deleteDir("Home/Runtime");
