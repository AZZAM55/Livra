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

$order_notification = lang('booking_notification');
$order_number = lang('order_number');
$item_name = lang('item_name');
$item_package = lang('item_package');
$extends_package = lang('extends_package');
$order_quantity = lang('order_quantity');
$order_totalPrice = lang('order_totalPrice');
$payments = lang('payment');
$customer_realname = lang('customer_realname');
$customer_mobile = lang('customer_mobile');
$customer_phone = lang('customer_phone');
$customer_address = lang('customer_address');
$zcode = lang('zcode');
$customer_qq = lang('customer_qq');
$customer_mail = lang('customer_mail');
$order_remark = lang('order_remark');
$booking_time = lang('booking_time');
$booking_ip = lang('booking_ip');
$booking_address = lang('booking_address');
$item_attribute = lang('item_attribute');

$aliziSendContent = <<<EOF
<h1 style="margin:20px 0;padding:10px 0;font-size:20px;color:#f60;border-bottom:2px solid #ccc;">{$order_notification}</h1>
<div>
	<p>【{$order_number}】{$order['order_no']}</p>
	<p>【{$item_name}】{$order['item_name']}</p>
	<p>【{$item_attribute}】{$order['item_params']}</p>
	<p>{$item_extends}</p>
	<p>【{$order_quantity}】{$order['quantity']}</p>
	<p>【{$order_totalPrice}】<b style="color:#f60;">{$order['total_price']}</b></p>
	<p>【{$payments}】{$payment[$order['payment']]['name']}</p>

	<p>【{$customer_realname}】{$order['name']}</p>
	<p>【{$customer_mobile}】{$order['mobile']}</p>
	<p>【{$customer_phone}】{$order['phone']}</p>
	<p>【{$customer_address}】{$order['region']} {$order['address']}</p>
	<p>【{$zcode}】{$order['zcode']}</p>
	<p>【{$customer_qq}】{$order['qq']}</p>
	<p>【{$customer_mail}】{$order['mail']}</p>
	<p>【{$order_remark}】{$order['remark']}</p>

	<p>【{$booking_time}】{$order['add_time']}</p>
	<p>【{$booking_ip}】{$order['add_ip']}</p>
	<p>【{$booking_address}】{$order['url']}</p>
</div>
EOF;
return $aliziSendContent;
?>