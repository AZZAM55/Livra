<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
$config= array(
	'URL_MODEL' => 0,
	'ALIZI_VERSION' => 'V3.5',
	'SHOW_PAGE_TRACE'     => false, // 调式跟踪信息
	'TOKEN_ON'=>false,  // 是否开启令牌验证
	'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true
	'TOKEN_NAME'=>'__hash__',
	'TOKEN_TYPE'=>'md5', 
	'DATA_CACHE_PATH' => './Public/Cache/', 
	'ALIZI_API'=>'http://api.alizi.net/',

	'DEFAULT_THEME' => '_Default',
	'LANG_AUTO_DETECT'=>true,
	'DEFAULT_LANG'=>isset($_COOKIE['alizi_think_language'])?$_COOKIE['alizi_think_language']:'zh-cn',
	'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'LANG_AUTO_DETECT' => false, // 自动侦测语言 开启多语言功能后有效
    'VAR_LANGUAGE' => 'l', // 默认语言切换变量
    'LANG_LIST' => 'zh-cn,zh-tw,en,jp,thai,cam,laos,arab', // 允许切换的语言列表 用逗号分隔
	'WAP_THEME'=>'Item',
	
	'HTML_PATH' => '/Html',
	'HTML_FILE_SUFFIX' => '.html',// 默认静态文件后缀

	'DEFAULT_AJAX_RETURN' => 'json', 
	'TMPL_ACTION_ERROR'   => 'Public/success.html', 
    'TMPL_ACTION_SUCCESS' => 'Public/success.html', 
	'ALIZI_THEME_ROOT'   => 'Home/Tpl/Alizi/',
	'ALIZI_TEMPLATE'=>array(
		'thin'=>'紧凑表单',
		'alizi'=>'宽松表单①',
		'alizi2'=>'宽松表单②',
		'skin'=>'块状表单',
		'ugly'=>'老套表单',
	),
	'ORDER_STATUS' => array(
		0=>'<i class="statis-0">未付款</i>',
		1=>'<i class="statis-1">已付款</i>',
		2=>'<i class="statis-2">已确认</i>',
		3=>'<i class="statis-3">已发货</i>',
		4=>'<i class="statis-4">已签收</i>',
		5=>'<i class="statis-5">已拒收</i>',
		6=>'<i class="statis-6">已关闭</i>',
		7=>'<i class="statis-7">已完结</i>',
		8=>'<i class="statis-8">申请退款</i>',
		9=>'<i class="statis-9">已退款</i>',
		//10=>'<i class="statis-9">关联单</i>',
	),
	'PAYMENT' => array(
		'payOnDelivery'=>array('name'=>'货到付款','info'=>'','classname'=>'payment-cod','math'=>'+0','onlinepay'=>false),
		'alipay'=>array('name'=>'支付宝','info'=>'','classname'=>'payment-alipay','math'=>'*1','onlinepay'=>true),
		'wxpay'=>array('name'=>'微信支付','info'=>'','classname'=>'payment-wxpay','math'=>'*1','onlinepay'=>true),
		'qrcode'=>array('name'=>'扫码支付','info'=>'','classname'=>'payment-qrcode','math'=>'+0','onlinepay'=>false),
		'codepay'=>array('name'=>'码支付','info'=>'','classname'=>'payment-codepay','math'=>'*1','type'=>array(1=>'支付宝',2=>'QQ钱包',3=>'微信支付'),'onlinepay'=>true),
		'xorpay'=>array('name'=>'XorPay支付','info'=>'','classname'=>'payment-xorpay','math'=>'*1','type'=>array('cashier_jsapi'=>'微信支付','alipay'=>'支付宝',)),
		
		/*
		'ispay'=>array('name'=>'ISPAY支付','info'=>'','classname'=>'payment-ispay','math'=>'*1','type'=>array('alipay'=>'支付宝','wxpay'=>'微信支付','qqpay'=>'QQ钱包','bank_pc'=>'网银电脑端','wxgzhpay'=>'微信公众号支付'),'onlinepay'=>true),
		'apipay'=>array('name'=>'API支付','info'=>'','classname'=>'payment-apipay','math'=>'*1','type'=>array(927=>'微信扫码',928=>'支付宝',)),
		'bankpay'=>array('name'=>'银行汇款','info'=>'','classname'=>'payment-bankpay','math'=>'+0'),
		'vivcms'=>array('name'=>'微信支付(第三方)','info'=>'','classname'=>'payment-wxpay','math'=>'*1','onlinepay'=>true),
		*/
	), 
	'COMMENTS_TEMPLATE' => array(
		1=>'评论模板一',
		2=>'评论模板二',
		3=>'评论模板三',
	),
	'TEMPLATE_OPTIONS'=>array(
		'product'=>array('name'=>'价格套餐','request'=>false, 'checked'=>true),
		'extends'=>array('name'=>'产品属性','request'=>false, 'checked'=>true),
		
		'salenum'=>array('name'=>'已售数量','request'=>false,'checked'=>false),
		'quantity'=>array('name'=>'订购数量','request'=>true,'checked'=>true),
		'price'=>array('name'=>'订单总价','request'=>false, 'checked'=>true),
		'coupon'=>array('name'=>'优惠券','request'=>false,'info'=>'','checked'=>true),
		'datetime'=>array('name'=>'选择时间','request'=>true,'info'=>'', 'checked'=>true),
		'name'=>array('name'=>'您的姓名','request'=>true,'info'=>'','checked'=>true),
		'mobile'=>array('name'=>'手机号码','request'=>true,'info'=>'','checked'=>true),
		
		'phone'=>array('name'=>'联系电话','request'=>false,'info'=>'','checked'=>true),
		'region'=>array('name'=>'选择地区','request'=>true,'checked'=>true),
		'address'=>array('name'=>'详细地址','request'=>true,'info'=>'','checked'=>true),
		'zcode'=>array('name'=>'邮政编码','request'=>false,'info'=>'','checked'=>false),
		'weixin'=>array('name'=>'微信账号','request'=>true,'info'=>'','checked'=>false),
		'qq'=>array('name'=>'QQ 号码','request'=>true,'info'=>'','checked'=>false),
		'mail'=>array('name'=>'电子邮箱','request'=>false,'info'=>'','checked'=>false),
		'file'=>array('name'=>'上传图片','request'=>true,'info'=>'','checked'=>false),
		'remark'=>array('name'=>'留言备注','request'=>false,'info'=>'','checked'=>true),
		'verify'=>array('name'=>'图片验证','request'=>true,'info'=>'','checked'=>true),
		'code'=>array('name'=>'短信验证','request'=>true,'info'=>'','checked'=>true),
		'payment'=>array('name'=>'支付方式','request'=>true, 'checked'=>true),
		
		
	),
	'DEFAULT_OPTIONS'=>array('product','extends','quantity','price','name','mobile','region','address','remark','payment'),
	'DEFAULT_COLOR'=>array(
		'body_bg'=>'F4F4F4','form_bg'=>'FFFFFF','title_bg'=>'666666','button_bg'=>'FF6600','font'=>'333333','border'=>'AAAAAA','nav_bg'=>'EE3300'
	),
	'ALIZI_ROUTE' => true,
	'ROUTE_RULES' => array(
		'Index'=>array(
			'a'=>':a.html',
			'order'=>array('id'=>'id/:id.html',),
			'category'=>array('id'=>'category-:id.html',),
			'article'=>array('id'=>'article-:id.html',),
			'detail'=>array('id'=>'detail-:id.html',),
			'result'=>array('order_no'=>'result/:order_no.html',),
			'pay'=>array('order_no'=>'pay-:order_no.html',),
		),
		'Item'=>array(
			'a'=>'item/:a.html',
			'index'=>array('uid'=>'item/index.html',),
			'category'=>array('id'=>'item/category-:id.html',),
			'order'=>array('id'=>'item/id-:id.html',),
			'detail'=>array('id'=>'item/detail-:id.html',),
		),
		'Wap'=>array(
			'a'=>'wap/:a.html',
			'index'=>array('uid'=>'wap/index.html',),
			'order'=>array('id'=>'wap/id-:id.html',),
			'category'=>array('cid'=>'wap/category-:cid.html',),
		),
		'Order'=>array(
			'index'=>array('id'=>'single/:id.html'),
			'pay'=>array('order_no'=>'Order/id-:order_no.html',),
			'payWxPayJsApi'=>array('order_id'=>'Order/wxpay-:order_id.html',),
			'payWxPayWap'=>array('order_id'=>'Order/payWxPayWap-:order_id.html',),
		),
	),
	
	'ADMIN_AUTH'=>array(
		'Item'=>array(
			'index'    => '商品查看',
			'edit'     => '商品编辑',
			'category' => '商品分类',
			'auth'     => '商品授权',
			'comments' => '商品评论',
		),
		'Order'=>array(
			'index'    => '订单列表',
			'modify'   => '订单修改',
			'detail'   => '查看客户信息',
			'add'      => '订单添加',
			'delete'   => '删除订单',
			'import'   => '批量操作',
			'statistics' => '订单统计',
			'channel' => '渠道统计',
			'region' => '地区统计',
			'time' => '时间统计',
			'user' => '用户统计',
		),
		'Article'=>array(
			'index'    => '文章列表',
			'edit'     => '文章编辑',
			'category' => '文章分类',
		),
		'User'=>array(
			'index'    => '管理员查看',
			'agent'    => '会员查看',
			'add'      => '添加用户',
			'edit'     => '修改用户',
			'adminGroup' => '管理员分组',
			'agentGroup' => '会员分组',
		),
		'Setting'=>array(
			'index'    => '系统设置',
			'database' => '数据库',
			'shipping'  => '运费管理',
			'user_logs' => '操作日志',
			'coupon' => '优惠券',
			'item' => '商品回收站',
			'order' => '订单回收站',
		),
	),
	'AGENT_AUTH'=>array(
		'Item'=>array(
			'index'    => '商品推广',
			'qrcode'   => '活码管理',
		),
		'Order'=>array(
			'index'    => '订单列表',
			'detail'   => '查看客户信息',
			
			'statistics' => '订单统计',
			'channel' => '渠道统计',
			'commission' => '佣金统计',
		),
	),
	
);

$db_file = "./Public/Common/alizi.db.php";
$db = file_exists($db_file)?include($db_file):array();
$express = include("alizi.express.php");
$setting = include("alizi.setting.php");
$auth = include("alizi.auth.php");
return array_merge($config,$db,$express,$setting,$auth);
?>