<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
require 'aliziApi.php';
if($_POST['rtnCode']=='0000'){
	payLog($_POST,'vivcms-success');
	echo http( getNotifyUrl('vivcms'), 'POST',$_POST);
}else{
	payLog($_POST,'vivcms-failure');
}
?>