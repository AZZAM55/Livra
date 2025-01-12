<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
require 'aliziApi.php';
$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
$host = $http_type.$_SERVER['HTTP_HOST'].substr(dirname($_SERVER['SCRIPT_NAME']), 0,-3);

$url = $host."index.php?m=Order&a=payWxPayJsApi&code={$_GET['code']}&state={$_GET['state']}";
Header("Location: $url");
?>