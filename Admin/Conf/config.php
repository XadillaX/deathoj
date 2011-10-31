<?php
if (!defined('THINK_PATH')) exit();

$config_array = require_once '../Home/Conf/config.php';
//$config_array['URL_MODEL'] = 1;
$config_array['APP_DEBUG'] = 0;
return $config_array;
?>