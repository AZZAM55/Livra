<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
require 'aliziApi.php';
payLog($_POST,'apipay');
echo http( getNotifyUrl('apipay'), 'POST',$_POST);
?>