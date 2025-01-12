<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */

$payment = C('PAYMENT');
$order['item_extends'] = json_decode($order['item_extends'],true);
$order['add_time'] = date('Y-m-d H:i:s',$order['add_time']);
foreach($order['item_extends'] as $k=>$v){ 
	$v = is_array($v)?implode('，', $v):$v;
	$item_extends .= "<span>【{$k}】{$v}</span><br>";
}

$pay_notification = lang('pay_notification');
$order_number = lang('order_number');
$item_name = lang('item_name');
$item_package = lang('item_package');
$extends_package = lang('extends_package');
$order_quantity = lang('order_quantity');
$order_totalPrice = lang('order_totalPrice');
$payments = lang('payment');

$aliziSendContent = <<<EOF
<h1 style="margin:20px 0;padding:10px 0;font-size:20px;color:#f60;border-bottom:2px solid #ccc;">{$pay_notification}</h1>
<div>
	<p>【{$order_number}】{$order['order_no']}</p>
	<p>【{$item_name}】{$order['item_name']}</p>
	<p>【{$item_package}】{$order['item_params']}</p>
	<p>{$item_extends}</p>
	<p>【{$order_quantity}】{$order['quantity']}</p>
	<p>【{$order_totalPrice}】<b style="color:#f60;">{$order['total_price']}</b></p>
	<p>【{$payments}】{$payment[$order['payment']]['name']}</p>
</div>
EOF;
return $aliziSendContent;
?>