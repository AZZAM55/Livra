<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
require 'aliziApi.php';
payLog($_POST,'codepay');
echo http( getNotifyUrl('codepay',$_POST['pay_id']), 'POST',$_POST);
?>