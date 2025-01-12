<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
return array(
	'admin'=>array(
		'Index'   => array( 
			array('name'=>'system_info','list'=>array('index'=>'basic_info','account'=>'account_setting')), 
			array('name'=>'extend_manage','list'=>array('advert'=>'advert_slideshow')),   
		),
		'Item'   => array( 
			array('name'=>'item','list'=>array('index'=>'item_list','edit'=>'add_item')), 
			array('name'=>'category','list'=>array('category'=>'category_list')), 
		),
		'Order'   => array( 
			array('name'=>'order','list'=>array('index'=>'all_order','add'=>'add_order','import'=>'bulk_action')),
			array('name'=>'order_statistics','list'=>array('statistics'=>'order_statistics','channel'=>'channel_statistics','region'=>'region_statistics','time'=>'time_statistics','user'=>'user_statistics')),
		 ),
		 'Article'   => array( 
			array('name'=>'article','list'=>array('index'=>'article_list','edit'=>'add_article')), 
			array('name'=>'category','list'=>array('category'=>'category_list')), 
		 ),
		'User'   => array( 
			array('name'=>'user','list'=>array('index'=>'admin_list','agent'=>'agent_list','add'=>'add_user')), 
			array('name'=>'user_group','list'=>array('adminGroup'=>'admin_group','agentGroup'=>'agent_group')),
		),
		'Setting'=> array( 
			array('name'=>'setting','list'=>array('index'=>'system_setting','database'=>'database_manage','shipping'=>'shipping_manage','user_logs'=>'action_log','coupon'=>'coupon_manage')),
			array('name'=>'recycle','list'=>array('item'=>'item_list','order'=>'order_list')),
		 ),
	),
	'agent' => array(
		'Index'   => array( array('name'=>'system_info','list'=>array('index'=>'basic_info','account'=>'account_setting')),),
		'Item'   => array( array('name'=>'item','list'=>array('index'=>'item_list',)),),
		'Order'   => array( 
			array('name'=>'order','list'=>array('index'=>'all_order','import'=>'bulk_action')),
			array('name'=>'order_statistics','list'=>array('statistics'=>'order_statistics','channel'=>'channel_statistics')),
		 ),
		'User'   => array( array('name'=>'user','list'=>array('index'=>'member_list','add'=>'add_member')), ),
	),
	'member' => array(
		'Index'   => array( array('name'=>'system_info','list'=>array('index'=>'basic_info','account'=>'account_setting')), ),
		'Item'   => array( array('name'=>'item','list'=>array('index'=>'item_list')), ),
		'Order'   => array( 
			array('name'=>'order','list'=>array('index'=>'all_order')), 
			array('name'=>'order_statistics','list'=>array('statistics'=>'order_statistics','channel'=>'channel_statistics')),
		),
	),
); 
?>