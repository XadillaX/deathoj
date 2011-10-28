<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 死月
 * Date: 11-10-26
 * Time: 下午4:18
 * Index File of Online Judge System
 */
 
/** 定义ThinkPHP框架路径 */
define("THINK_PATH", "ThinkPHP");

/** 定义项目名称和路径 */
define("APP_NAME", "OnlineJudge");
define("APP_PATH", ".");

/** 加载框架入口文件 */
require(THINK_PATH . "/ThinkPHP.php");

/** 实例化网站应用实例 */
App::run();
