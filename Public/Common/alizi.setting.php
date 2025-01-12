<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */

include_once('./config.php');
return array(
	'setting' => array(
		'basic_info' => array(
			'alizi_host'=> array('name'=>'静态文件网址','tags'=>'text','options'=>'','decription'=>'静态文件包含js/css/img等，可用CDN加速，格式如：http://www.wxrob.com','width'=>50,'height'=>0,'separator'=>0,),
			'logo_pc'=> array('name'=>'PC端Logo','tags'=>'file','options'=>'','decription'=>'（宽高为：160X70）','width'=>50,'height'=>0,'separator'=>0,),
			'logo'=> array('name'=>'Wap端Logo','tags'=>'file','options'=>'','decription'=>'（宽高为：160X40）','width'=>50,'height'=>0,'separator'=>0,),
			'title'=> array('name'=>'网站标题','tags'=>'text','options'=>'','decription'=>'','width'=>80,'height'=>0,'separator'=>0,),
			'keywords'=> array('name'=>'网站关键词','tags'=>'text','options'=>'','decription'=>'','width'=>80,'height'=>0,'separator'=>0,),
			'description'=> array('name'=>'网站描述','options'=>'','tags'=>'textarea','decription'=>'','width'=>80,'height'=>3,'separator'=>0,),
			'footer'=> array('name'=>'网站底部','options'=>'','tags'=>'textarea','decription'=>'','width'=>80,'height'=>6,'separator'=>0,),
			'notice'=> array('name'=>'顶部公告','options'=>'','tags'=>'text','decription'=>'','width'=>95,'height'=>2,'separator'=>0,),
			'result_info'=> array('name'=>'提交成功页面底部','options'=>'','tags'=>'textarea','decription'=>'','width'=>95,'height'=>2,'separator'=>1,),
			'contact_tel'=> array('name'=>'固定电话','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
			'contact_phone'=> array('name'=>'手机号码','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
			'contact_qq'=> array('name'=>'联系QQ','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
			'contact_weixin'=> array('name'=>'联系微信','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
			'contact_line'=> array('name'=>'Line联系','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
			'contact_email'=> array('name'=>'Email联系','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>25,'separator'=>0,),
		),

		'website_setting'=>array(
			'system_status'=> array('name'=>'网站状态','options'=>array('1'=>'运行','2'=>'关闭PC站','3'=>'关闭Wap站','0'=>'关闭整站',),'tags'=>'select','decription'=>'网站关闭后将不显示前台页面，后台可以正常使用','width'=>35,'height'=>0,'separator'=>0,),
			'DEFAULT_THEME'=> array('name'=>'网站模板','options'=>array('_Default'=>'默认模板',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'system_close_info'=> array('name'=>'默认首页','options'=>array('1'=>'运行','0'=>'关闭',),'tags'=>'text','decription'=>'填写【商品编号】则默认该商品为首页，填写网址则跳转，如：http://www.wxrob.com','width'=>35,'height'=>0,'separator'=>0,),
			'html_file'=> array('name'=>'生成静态目录','options'=>'','tags'=>'text','decription'=>'生成静态页面保存的目录，如：Html/','width'=>35,'height'=>0,'separator'=>0,),
			'is_encode'=> array('name'=>'内容加密输出','value'=>'','options'=>array('0'=>'不加密','1'=>'加密'),'tags'=>'select','decription'=>'','width'=>10,'height'=>0,'class'=>'', 'separator'=>0,),
			'URL_MODEL'=> array('name'=>'网站运行模式','options'=>array('0'=>'动态模式','2'=>'伪静态模式'),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'DEFAULT_LANG'=> array('name'=>'语言版本','options'=>array('zh-cn'=>'简体中文','zh-tw'=>'繁体(台湾)','zh-hk'=>'繁体(香港)'),'tags'=>'select','decription'=>'前台语言切换，后台依然是简体中文','width'=>35,'height'=>0,'separator'=>0,),
			'region'=> array('name'=>'地区选择','options'=>array(''=>'选择地区','city-picker'=>'中国地区①','region-zh-cn'=>'中国地区②','region-zh-tw'=>'台湾地区①','region-zh-tw2'=>'台湾地区②','region-zh-hk'=>'港澳地区',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>1,), 
			 
			'symbol'=> array('name'=>'货币符号','options'=>'','tags'=>'text','decription'=>'如：￥,$','width'=>15,'height'=>0,'separator'=>0,),
			'currency'=> array('name'=>'货币代码','options'=>'','tags'=>'text','decription'=>'如：CNY,USD','width'=>15,'height'=>0,'separator'=>0,),
			'theme_color'=> array('name'=>'主题颜色','value'=>'','options'=>'','tags'=>'text','decription'=>'手机版前台主题颜色','width'=>10,'height'=>0,'class'=>' jscolor', 'separator'=>1,),

            'item_quantity'=> array('name'=>'商品库存','value'=>1,'options'=>array('0'=>'不使用','1'=>'下单减库存','2'=>'支付减库存','4'=>'发货减库存',),'tags'=>'select','decription'=>'是否使用商品库存','width'=>35,'height'=>0,'separator'=>0,),
            'show_qrcode'=> array('name'=>'单页显示二维码','value'=>0,'options'=>array('0'=>'关闭','1'=>'显示'),'tags'=>'select','decription'=>'单页右下角的二维码','width'=>35,'height'=>0,'separator'=>0,),
            'shop_links'=> array('name'=>'商城链接','value'=>0,'options'=>array('1'=>'默认','2'=>'链接到单页'),'tags'=>'select','decription'=>'商品跳转链接','width'=>35,'height'=>0,'separator'=>1,),

			
			'system_theme'=> array('name'=>'表单模板','options'=>$config['ALIZI_TEMPLATE'],'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'order_options'=> array('name'=>'表单选项','options'=>array('product'=>'价格套餐','extends'=>'产品属性','price'=>'产品价格','coupon'=>'优惠券','salenum'=>'已售数量','quantity'=>'订购数量','payment'=>'支付方式','datetime'=>'选择时间','name'=>'客户姓名','mobile'=>'客户手机','phone'=>'客户电话','region'=>'地区选择','address'=>'详细地址','zcode'=>'邮政编码','qq'=>'QQ 号码','weixin'=>'微信账号','mail'=>'电子邮箱','file'=>'上传图片','remark'=>'备注留言','verify'=>'验证号码','code'=>'手机验证',),'tags'=>'checkbox','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'show_notice'=> array('name'=>'显示滚动订单','options'=>array('0'=>'不显示','1'=>'下方显示','2'=>'右侧显示',),'tags'=>'radio','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'real_notice'=> array('name'=>'滚动订单','options'=>array('0'=>'虚假','1'=>'真实',),'tags'=>'radio','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'record_order'=> array('name'=>'记录客户信息','options'=>array('0'=>'不记录','1'=>'记录'),'tags'=>'radio','decription'=>'记录则客户再次下单时不需填写信息','width'=>35,'height'=>0,'separator'=>0,),
		 
			'lazyload'=> array('name'=>'图片延迟加载','options'=>array('1'=>'延迟','0'=>'不延迟'),'tags'=>'radio','decription'=>'使用延迟加载可提升网页打开速度','width'=>35,'height'=>0,'separator'=>1,),

            'slider_show'=> array('name'=>'首页幻灯片','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'slider_num'=> array('name'=>'','value'=>5,'options'=>'','tags'=>'text','decription'=>'条','width'=>5,'height'=>0,'separator'=>0,),
			'item_hot_show'=> array('name'=>'首页新品推荐','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'item_hot_num'=> array('name'=>'','value'=>5,'options'=>'','tags'=>'text','decription'=>'条','width'=>5,'height'=>0,'separator'=>0,),
			'relate_item_show'=> array('name'=>'相关产品推荐','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'relate_item_num'=> array('name'=>'','value'=>5,'options'=>'','tags'=>'text','decription'=>'条','width'=>5,'height'=>0,'separator'=>0,),
			'item_category_show'=> array('name'=>'首页分类展示','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'item_category_num'=> array('name'=>'','value'=>3,'options'=>'','tags'=>'text','decription'=>'条','width'=>5,'height'=>0,'separator'=>1),

			'show_header'=> array('name'=>'显示头部信息','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'手机版头部','width'=>35,'height'=>0,'separator'=>0,),
			'show_bottom_nav'=> array('name'=>'显示底部导航','value'=>1,'options'=>array('1'=>'显示','0'=>'关闭',),'tags'=>'select','decription'=>'控制手机版和单页底部导航栏','width'=>35,'height'=>0,'separator'=>1,),
			
			'header_nav'=> array('name'=>'电脑站头部导航','options'=>'','tags'=>'textarea','decription'=>'每行一个，格式如下：<span style="color:#333">名称<b style="color:#F00">||</b>链接地址</span>','width'=>60,'height'=>4,'separator'=>0,),
			'footer_nav'=> array('name'=>'手机站底部导航','options'=>'','tags'=>'textarea','decription'=>'每行一个，格式如下：<span style="color:#333">名称<b style="color:#F00">||</b>链接地址<b style="color:#F00">||</b>图标</span>，<a href="http://www.wxrob.com/alizi/icon.htm" target="_blank">【查看图标】</a>','width'=>60,'height'=>4,'separator'=>1,),
			'order_footer_nav'=> array('name'=>'手机站订单页<br>底部导航','options'=>'','tags'=>'textarea','decription'=>'每行一个，格式如下：<span style="color:#333">名称<b style="color:#F00">||</b>链接地址<b style="color:#F00">||</b>图标</span>，<a href="http://www.wxrob.com/alizi/icon.htm" target="_blank">【查看图标】</a>','width'=>60,'height'=>4,'separator'=>1,),
			
			'facebook_pixel_id'=> array('name'=>'Facebook像素','options'=>'','tags'=>'text','decription'=>'只须填写像素ID，不须填写整段代码，多个像素请用英文逗号隔开','width'=>60,'height'=>0,'separator'=>1,),

		),
		
		'agent_setting' => array(
			'agent_register'=> array('name'=>'会员注册','options'=>array('0'=>'关闭','1'=>'启用',),'tags'=>'select','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'agent_status'=> array('name'=>'注册审核','options'=>array('0'=>'需要管理员审核','1'=>'不需要审核',),'tags'=>'select','decription'=>'选择需要管理员审核，则会员注册后需要管理员审核通过后才能登陆会员后台','width'=>50,'height'=>0,'separator'=>0,),
			'agent_group'=> array('name'=>'注册分配到该分组','options'=>array(),'tags'=>'select','decription'=>'','width'=>50,'height'=>0,'separator'=>1,),
			
			'agent_settlement'=> array('name'=>'佣金结算方式','options'=>array('0'=>'下单结算','1'=>'支付结算','2'=>'确认结算','3'=>'发货结算','4'=>'签收结算',),'tags'=>'select','decription'=>'在用户分组里可以针对某个分组进行佣金比率设置','width'=>50,'height'=>0,'separator'=>0,),
		),

		'payment_setting' => array(
			'payment_global'=> array('name'=>'全站通用','value'=>1,'options'=>array('1'=>'是','0'=>'否',),'tags'=>'select','decription'=>'非全站通用，可单独对某个产品设置支付方式','width'=>35,'height'=>0,'separator'=>0,),
			'payment_url'=> array('name'=>'支付域名','options'=>'','tags'=>'text','decription'=>'跳转到该域名进行支付，如：http://www.wxrob.com','width'=>30,'height'=>0,'separator'=>1,),
			
			'payOnDelivery_status'=> array('name'=>'货到付款','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'payOnDelivery_discount'=> array('name'=>'到付额外费用','value'=>0,'options'=>'','tags'=>'text','decription'=>'元（选择货到付款需客户补交费用。0则不需要）','width'=>5,'height'=>0,'separator'=>0,),
			'payOnDelivery_discount_info'=> array('name'=>'选择到付时提示','options'=>'','tags'=>'textarea','decription'=>'选择货到付款时的提示文字，这里为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
 
			
			'alipay_status'=> array('name'=>'支付宝','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'开通支付宝即时到账请到<a href="https://b.alipay.com/order/productIndex.htm" target="_blank">支付宝</a>官网申请','width'=>50,'height'=>0,'separator'=>0,),
			'alipay_type'=> array('name'=>'支付宝类型','options'=>array('1'=>'即时到账（网页版）','2'=>'手机网站支付'),'tags'=>'checkbox','decription'=>'申请即时到账需要有企业资质','width'=>50,'height'=>0,'separator'=>0,),
			'alipay_mail'=> array('name'=>'支付宝账户','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'alipay_pid'=> array('name'=>'合作者身份(PID)','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'alipay_key'=> array('name'=>'安全校验码(KEY)','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'alipay_discount'=> array('name'=>'享受折扣','options'=>'','tags'=>'text','decription'=>'0.85为85折，1不打折','width'=>5,'height'=>0,'separator'=>0,),
			'alipay_discount_info'=> array('name'=>'选择支付宝时提示','options'=>'','tags'=>'textarea','decription'=>'为空则不提示','width'=>50,'height'=>3,'separator'=>1,),

			'wxpay_status'=> array('name'=>'微信支付','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'开通微信支付请到<a href="https://pay.weixin.qq.com" target="_blank">微信支付</a>官网申请','width'=>35,'height'=>0,'separator'=>0,),
			'wxpay_appid'=> array('name'=>'微信APPID','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'wxpay_mchid'=> array('name'=>'微信MCHID','options'=>'','tags'=>'text','decription'=>'微信商户号','width'=>50,'height'=>0,'separator'=>0,),
			'wxpay_key'=> array('name'=>'支付密钥KEY','options'=>'','tags'=>'text','decription'=>'微信API密钥','width'=>50,'height'=>0,'separator'=>0,),
			'wxpay_secret'=> array('name'=>'微信支付应用密钥','options'=>'','tags'=>'text','decription'=>'AppSecret(应用密钥)','width'=>50,'height'=>0,'separator'=>0,),
			'wxpay_type'=> array('name'=>'微信支付类型','options'=>array('1'=>'扫码支付','2'=>'H5支付',),'tags'=>'checkbox','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'wxpay_discount'=> array('name'=>'享受折扣','options'=>'','tags'=>'text','decription'=>'0.85为85折，1不打折','width'=>5,'height'=>0,'separator'=>0,),
			'wxpay_discount_info'=> array('name'=>'选择微信支付时提示','options'=>'','tags'=>'textarea','decription'=>'为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
			
		
			'codepay_status'=> array('name'=>'码支付:','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'<span style="color:#f60">个人二维码收款，直接到账且有返回通知。申请地址<a href="https://codepay.fateqq.com/i/34676" target="_blank"><b>【码支付】</b></a></span>','width'=>50,'height'=>0,'separator'=>0,),
			'codepay_type'=> array('name'=>'支付方式','options'=>array('1'=>'支付宝','2'=>'QQ钱包','3'=>'微信支付'),'tags'=>'checkbox','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'codepay_id'=> array('name'=>'码支付ID','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'codepay_key'=> array('name'=>'通信密钥','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'codepay_token'=> array('name'=>'token','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'codepay_discount'=> array('name'=>'享受折扣','options'=>'','tags'=>'text','decription'=>'0.85为85折，1不打折','width'=>5,'height'=>0,'separator'=>0,),
			'codepay_discount_info'=> array('name'=>'选择时提示','options'=>'','tags'=>'textarea','decription'=>'为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
		
			'xorpay_status'=> array('name'=>'XorPay支付:','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'<span style="color:#f60">第三方接口，有返回通知。申请地址<a href="https://xorpay.com?r=alizi" target="_blank"><b>www.xorpay.com</b></a></span>','width'=>50,'height'=>0,'separator'=>0,),
			'xorpay_type'=> array('name'=>'支付方式','options'=>array('cashier_jsapi'=>'微信支付','alipay'=>'支付宝',),'tags'=>'checkbox','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'xorpay_aid'=> array('name'=>'aid','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'xorpay_secret'=> array('name'=>'secret','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'xorpay_discount'=> array('name'=>'享受折扣','options'=>'','tags'=>'text','decription'=>'0.85为85折，1不打折','width'=>5,'height'=>0,'separator'=>0,),
			'xorpay_discount_info'=> array('name'=>'选择时提示','options'=>'','tags'=>'textarea','decription'=>'为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
			
			'711_status'=> array('name'=>'711超商取貨','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'711_discount'=> array('name'=>'到付额外费用','value'=>0,'options'=>'','tags'=>'text','decription'=>'元（选择时需客户补交费用。0则不需要）','width'=>5,'height'=>0,'separator'=>0,),
			'711_discount_info'=> array('name'=>'选择到付时提示','options'=>'','tags'=>'textarea','decription'=>'选择时的提示文字，这里为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
			
			'quanjia_status'=> array('name'=>'全家店取','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'radio','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'quanjia_discount'=> array('name'=>'到付额外费用','value'=>0,'options'=>'','tags'=>'text','decription'=>'元（选择时需客户补交费用。0则不需要）','width'=>5,'height'=>0,'separator'=>0,),
			'quanjia_discount_info'=> array('name'=>'选择到付时提示','options'=>'','tags'=>'textarea','decription'=>'选择时的提示文字，这里为空则不提示','width'=>50,'height'=>3,'separator'=>1,),
			
		),

		'safe_setting' => array( 
			'safe_mobile_limit'=> array('name'=>'手机号下单限制','value'=>5,'options'=>'','tags'=>'text','decription'=>'笔（一个手机每天可对某一产品下单笔数）','width'=>5,'height'=>0,'separator'=>0,),
			'safe_order_interval'=> array('name'=>'下单间隔限制','value'=>20,'options'=>'','tags'=>'text','decription'=>'秒（对同一产品设置下单间隔，设置时长可以有效防止重复下单）','width'=>5,'height'=>0,'separator'=>0,),
			'safe_ip_limit'=> array('name'=>'IP下单限制','value'=>10,'options'=>'','tags'=>'text','decription'=>'笔（限制每个IP每小时可下单笔数，0 则不限制）','width'=>5,'height'=>0,'separator'=>0,),
			'safe_ip_denied'=> array('name'=>'黑名单IP','options'=>'','tags'=>'textarea','decription'=>'每个IP用#分隔开，IP段可用*号代替','width'=>80,'height'=>3,'separator'=>1,),
          
         	 'ban_region_msg'=> array('name'=>'禁止下单提示文字','tags'=>'text','options'=>'提示文字','decription'=>'','width'=>80,'height'=>0,'separator'=>0,),
         	 'ban_region'=> array('name'=>'禁址下单的地区','tags'=>'textarea','options'=>'','decription'=>'多个地区需要用竖线（ | ）隔开，如：海南|北京','width'=>65,'height'=>3,'separator'=>1,),
			 
			'main_domain'=> array('name'=>'入口域名','options'=>'','tags'=>'text','decription'=>'域名如：<b style="color:#f00">www.wxrob.com</b>','width'=>48,'height'=>3,'separator'=>0,),
			'redirect_domains'=> array('name'=>'落地域名','options'=>'','tags'=>'textarea','decription'=>'每行一个域名如：<b style="color:#f00">www.alizi.com.cn</b>，如果填写多个域名则随机跳转','width'=>50,'height'=>3,'separator'=>1,),
			
			'login_domain'=> array('name'=>'后台登陆域名','options'=>'','tags'=>'text','decription'=>'只需要填写域名，不要加http://，填写后只能使用该域名登陆后台。','width'=>35,'height'=>0,'separator'=>0,),
		),

		'mail_setting' => array(
			'mail_send'=> array('name'=>'邮件发送','options'=>array('1'=>'启用','0'=>'关闭',),'tags'=>'select','decription'=>'','width'=>5,'height'=>0,'separator'=>0,),
			'mail_proxy'=> array('name'=>'','options'=>array('0'=>'不使用邮件代理','1'=>'使用邮件代理',),'tags'=>'select','decription'=>'如果服务器不支持邮件发送，请使用代理','width'=>5,'height'=>0,'separator'=>0,),
			'mail_send_status'=> array('name'=>'发送通知','options'=>array('0'=>'下单通知','1'=>'付款通知','2'=>'确认通知','3'=>'发货通知',),'tags'=>'checkbox','decription'=>'','width'=>5,'height'=>0,'separator'=>0,),
			'mail_smtp'=> array('name'=>'SMTP服务器','options'=>'','tags'=>'text','decription'=>'如QQ邮箱：smtp.qq.com','width'=>50,'height'=>0,'separator'=>0,),
			'mail_ssl'=> array('name'=>'使用SSL','value'=>25,'options'=>array(''=>'不使用','ssl'=>'使用SSL',),'tags'=>'select','decription'=>'QQ，网易邮箱要使用SSL','width'=>10,'height'=>0,'separator'=>0,),
			'mail_port'=> array('name'=>'SMTP服务器端口','value'=>25,'options'=>'','tags'=>'text','decription'=>'默认为25,使用SSL用465','width'=>10,'height'=>0,'separator'=>0,),
			'mail_account'=> array('name'=>'发送邮箱','options'=>'','tags'=>'text','decription'=>'发送邮箱的帐号','width'=>50,'height'=>0,'separator'=>0,),
			'mail_password'=> array('name'=>'邮箱密码或授权码','options'=>'','tags'=>'text','decription'=>'发送邮箱的密码','width'=>50,'height'=>0,'separator'=>0,),
			'mail_to'=> array('name'=>'接收邮箱','options'=>'','tags'=>'text','decription'=>'多个邮箱请用英文逗号隔开','width'=>50,'height'=>0,'separator'=>0,),
			'mail_title'=> array('name'=>'邮件标题','options'=>'','tags'=>'text','decription'=>'[AliziStatus]订单状态，[AliziTitle]产品名，[AliziName]客户名','width'=>50,'height'=>0,'separator'=>0,),
		),

		'sms_setting' => array(
			'sms_send'=> array('name'=>'短信发送','options'=>array('0'=>'关闭','1'=>'启用',),'tags'=>'select','decription'=>'','width'=>30,'height'=>0,'separator'=>0,),
			'sms_admin'=> array('name'=>'通知对象','value'=>1,'options'=>array('0'=>'只通知客户','1'=>'只通知管理员','2'=>'同时通知管理员和客户',),'tags'=>'select','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'sms_admin_mobile'=> array('name'=>'','options'=>'','tags'=>'text','decription'=>'请填写管理员手机号，多个号码请用英文逗号隔开','width'=>30,'height'=>0,'separator'=>0,),
			'sms_account'=> array('name'=>'发送账号','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>0,'separator'=>0,),
			'sms_password'=> array('name'=>'发送密码','options'=>'','tags'=>'text','decription'=>'','width'=>30,'height'=>0,'separator'=>0,),
		),

		'express_setting' => array(
			'delivery_id'=> array('name'=>'用户ID','options'=>'','tags'=>'text','decription'=>'快递查询接口请到<a href="http://www.kdniao.com/reg" target="_blank">【快递鸟】</a>申请','width'=>35,'height'=>0,'separator'=>0,),
			'delivery_key'=> array('name'=>'接口Key','options'=>'','tags'=>'text','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
			'delivery_setting'=> array('name'=>'设置常用快递','options'=>$express['DELIVERY'],'tags'=>'checkbox','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
		),
		'export_setting' => array(
			'export_type'=> array('name'=>'导出格式','options'=>array('excel'=>'导出Excel','csv'=>'导出CSV',),'tags'=>'select','decription'=>'CSV格式可以导出大数据','width'=>30,'height'=>0,'separator'=>0,),
			'export_order'=> array('name'=>'导出订单','options'=>array('id'=>'订单 ID','order_no'=>'订单编号','item_sn'=>'商品编号','item_name'=>'商品名称','item_params'=>'商品套餐','item_extends'=>'商品属性','file'=>'图片','quantity'=>'订单数量','total_price'=>'订单总价','name'=>'姓名','mobile'=>'手机','phone'=>'电话','province'=>'省份','city'=>'城市','area'=>'地区','address'=>'地址','zcode'=>'邮编','payment'=>'支付方式','status'=>'订单状态','delivery_name'=>'快递名称','delivery_no'=>'快递单号','remark'=>'客户备注','qq'=>'QQ','weixin'=>'微信','mail'=>'Email','sms'=>'回复短信','channel_id'=>'推广渠道','url'=>'下单地址','referer'=>'来路地址','user_pid'=>'代理商','user_id'=>'分销商','datetime'=>'预约日期','add_ip'=>'下单IP','add_time'=>'下单时间','admin_remark'=>'管理员备注'),'tags'=>'checkbox','decription'=>'','width'=>35,'height'=>0,'separator'=>0,),
		),
		'weixin_share_setting' => array(
			'weixin_status'=> array('name'=>'微信分享','options'=>array('0'=>'关闭','1'=>'启用',),'tags'=>'select','decription'=>'需要登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”','width'=>50,'height'=>0,'separator'=>0,),
			'weixin_appid'=> array('name'=>'AppID','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'weixin_appsecret'=> array('name'=>'AppSecret','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
		),
		'ipcloak' => array(
			'ipcloak_status'=> array('name'=>'ipcloak','options'=>array('0'=>'关闭','1'=>'启用',),'tags'=>'select','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'ipcloak_debug'=> array('name'=>'运行模式','options'=>array('0'=>'生产模式','1'=>'调试模式',),'tags'=>'select','decription'=>'测试期间请选择调试模式，正式投放再切换为生产模式','width'=>50,'height'=>0,'separator'=>0,),
			'ipcloak_test_ip'=> array('name'=>'模拟访问IP','options'=>'','tags'=>'text','decription'=>'模拟该IP进行访问测试。如：香港61.244.148.166 ,台湾140.113.215.224,马来58.71.128.0,新加坡：220.255.1.166,美国208.109.192.70','width'=>15,'height'=>0,'separator'=>1,),
			'ipcloak_username'=> array('name'=>'用户名','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'ipcloak_password'=> array('name'=>'密码','options'=>'','tags'=>'text','decription'=>'','width'=>50,'height'=>0,'separator'=>0,),
			'ipcloak_countries'=> array('name'=>'广告投放国家代码','options'=>'','tags'=>'text','decription'=>'查看<a href="http://www.wxrob.com/alizi/country.htm" target="_blank">国家代码</a>，多个代码请用+号分开，如：CN+US+GB','width'=>50,'height'=>0,'separator'=>0,), 
		),
		
	)
);

?>