<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
ob_start('ob_gzhandler');
define('APP_DEBUG',true);//调试模式
define( 'RUNTIME_PATH' , './Public/Cache/Admin/' );
define( 'COMMON_PATH' , './Public/Common/' );
define('ALIZI_VERSION','Alizi-V3.5');
define('APP_NAME','Admin');
define('APP_PATH','./Admin/');
require 'Public/ThinkPHP/ThinkPHP.php';
?>