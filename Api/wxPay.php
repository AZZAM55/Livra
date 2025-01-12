<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
require 'aliziApi.php';
$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
if(empty($xml)){
	$xml = file_get_contents('php://input');
}
if($xml ){
	libxml_disable_entity_loader(true);
	$result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	payLog($result,'wxPay');
	http( getNotifyUrl('wxPay',$result['out_trade_no']), 'POST',$result );
}else{
	payLog('empty','wxPay');
}
?>