<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class VersionAction extends Action {

	
    public function index(){
		header('content-type:text/html;charset=utf-8');
		
		//V2.6.0升级
		$Model = M();
		$prefix = C('DB_PREFIX');
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}sent` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,`order_id` int(12) DEFAULT '0',`order_status` tinyint(1) DEFAULT '0',`sent_status` tinyint(1) DEFAULT '0',`mobile` varchar(15) DEFAULT NULL,`sent_content` text,`sent_time` datetime DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}receive` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,`order_id` int(12) DEFAULT '0',`mobile` varchar(15) DEFAULT NULL,`receive_content` text,`receive_time` datetime DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}order_fengye` ( `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, `adgroup_id` varchar(20) DEFAULT NULL, `adgroup_name` varchar(200) DEFAULT NULL, `advertiser_id` varchar(20) DEFAULT NULL, `account_id` varchar(20) DEFAULT NULL, `order_id` varchar(20) DEFAULT NULL, `page_name` varchar(100) DEFAULT NULL, `package_info` text, `quantity` int(5) DEFAULT NULL, `price` decimal(12,2) DEFAULT NULL, `total_price` decimal(12,2) DEFAULT NULL, `user_name` varchar(20) DEFAULT NULL, `user_phone` varchar(20) DEFAULT NULL, `user_province` varchar(20) DEFAULT NULL, `user_city` varchar(20) DEFAULT NULL, `user_area` varchar(20) DEFAULT NULL, `user_address` varchar(100) DEFAULT NULL, `user_ip` varchar(20) DEFAULT NULL, `order_time` datetime DEFAULT NULL, `user_message` varchar(100) DEFAULT NULL, `url` varchar(255) DEFAULT NULL, `add_time` int(10) DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `order_id` (`order_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}coupon` (`id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,`name` varchar(20) DEFAULT NULL, `code` varchar(12) DEFAULT NULL, `types` tinyint(1) NOT NULL DEFAULT '1',`value` float(12,0) DEFAULT NULL, `is_used` tinyint(1) NOT NULL DEFAULT '0', `used_user` int(12) NOT NULL DEFAULT '0',`used_time` int(11) DEFAULT NULL,`start_time` int(11) DEFAULT NULL,`expire_time` int(11) DEFAULT NULL,`add_time` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}item_pv` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,`user_id` int(12) DEFAULT NULL,`item_id` bigint(12) DEFAULT NULL,`pv` bigint(20) DEFAULT NULL,`date` date DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}item_pv` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,`user_id` int(12) DEFAULT NULL,`item_id` bigint(12) DEFAULT NULL,`pv` bigint(20) DEFAULT NULL,`date` date DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}commission` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,`status` tinyint(1) DEFAULT '0' COMMENT '0未结算，1已结算',`user_id` int(12) NOT NULL,`item_id` int(12) NOT NULL DEFAULT '0',`order_id` int(12) NOT NULL,`order_status` tinyint(1) NOT NULL DEFAULT '0',`total_price` decimal(12,2) DEFAULT NULL,`fee` decimal(12,2) NOT NULL DEFAULT '1.00',`add_time` int(10) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}qrcode` (`id` int(12) unsigned NOT NULL AUTO_INCREMENT,`sid` varchar(30) DEFAULT NULL,`user_id` int(12) DEFAULT NULL,`name` varchar(100) DEFAULT NULL,`count` int(10) DEFAULT '1',`pv` bigint(20) DEFAULT '1',`add_time` int(12) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}qrcode_list` (`id` int(12) unsigned NOT NULL AUTO_INCREMENT,`sid` varchar(30) DEFAULT NULL,`uuid` varchar(50) DEFAULT NULL,`image` varchar(200) DEFAULT NULL,`pv` bigint(20) DEFAULT '1',`add_time` int(12) DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		
		
		$Order = M('Order');
		$Order->query('alter table __TABLE__ modify column payment varchar(20);');
		$fields = $Order->query('desc __TABLE__ `update_user_id`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `update_user_id`  int(12) NOT NULL DEFAULT  '0'  after update_time"); }
		$fields = $Order->query('desc __TABLE__ `file`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `file` varchar(200) after add_time"); }
		
		$fields = $Order->query('desc __TABLE__ `deposit`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `deposit` decimal(12,2) DEFAULT '0' after is_read"); }
		$fields = $Order->query('desc __TABLE__ `deposit_payment`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `deposit_payment` varchar(200) DEFAULT '' after deposit"); }
		$fields = $Order->query('desc __TABLE__ `deposit_ispay`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `deposit_ispay` tinyint(1) DEFAULT '0' after deposit"); }
		$fields = $Order->query('desc __TABLE__ `is_pay`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `is_pay` tinyint(1) DEFAULT '0' after deposit_ispay"); }
		$fields = $Order->query('desc __TABLE__ `pay_time`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `pay_time` int(10) DEFAULT '0' after is_pay"); }
		$fields = $Order->query('desc __TABLE__ `pay_sn`');
		if(empty($fields)){ $Order->query("ALTER TABLE __TABLE__ add `pay_sn` varchar(50) DEFAULT '0' after pay_time"); }
		
		
		$Order->query('update __TABLE__ set is_pay="1" where status="1"');
		$Order->query('update __TABLE__ set payment="payOnDelivery" where payment="1"');
		$Order->query('update __TABLE__ set payment="alipay" where payment="2"');
		$Order->query('update __TABLE__ set payment="wxpay" where payment="3"');
		$Order->query('update __TABLE__ set payment="codepay-1" where payment="4"');
		$Order->query('update __TABLE__ set payment="paypay-1" where payment="44"');
		$Order->query('update __TABLE__ set payment="creaditcard" where payment="66"');
		
		
		$Item = M('Item');
		$fields = $Item->query('desc __TABLE__ `slideshow`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `slideshow`  text  after thumb"); }
		$Item->query("UPDATE __TABLE__ SET slideshow=image WHERE slideshow IS NULL");
		$fields = $Item->query('desc __TABLE__ `weixin`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `weixin` text after redirect_uri"); }
		$fields = $Item->query('desc __TABLE__ `min_num`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `min_num` int(3) DEFAULT 1 after salenum "); }
		$fields = $Item->query('desc __TABLE__ `max_num`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `max_num` int(5) DEFAULT 10 after min_num"); }
		$fields = $Item->query('desc __TABLE__ `javascript`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `javascript` text after remark"); }
		$fields = $Item->query('desc __TABLE__ `purchase_url`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `purchase_url` text after add_time"); }
		$fields = $Item->query('desc __TABLE__ `domain`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `domain` text after purchase_url"); }
		$fields = $Item->query('desc __TABLE__ `facebook_pixel_id`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `facebook_pixel_id` varchar(30) DEFAULT '' after domain"); }
		$fields = $Item->query('desc __TABLE__ `deposit`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `deposit` decimal(12,2) DEFAULT '0' after facebook_pixel_id"); }
		$fields = $Item->query('desc __TABLE__ `deposit_payment`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `deposit_payment` varchar(20) DEFAULT '' after deposit"); }
		$fields = $Item->query('desc __TABLE__ `limit_num`');
		if(empty($fields)){ $Item->query("ALTER TABLE __TABLE__ add `limit_num` int(3) DEFAULT 10 after max_num"); }
		
		$Setting = M('Setting');
		$Setting->query('update __TABLE__ set name="payOnDelivery_discount" where name="payOnDelivery_fee"');
		$Setting->query('update __TABLE__ set name="payOnDelivery_discount_info" where name="payOnDelivery_info"');
		
		$update = './Public/Database/update.lock';
		if(file_exists($update)){ unlink($update); }
		
		$this->itemParams();
		$this->success('升级完成',U('Index/index'));
    }
	
	private function itemParams(){
		$prefix = C('DB_PREFIX');
		$Model = M();
		$Model->query("CREATE TABLE IF NOT EXISTS `{$prefix}item_params` (`id` int(12) unsigned NOT NULL AUTO_INCREMENT,`item_id` varchar(20) DEFAULT NULL,`title` varchar(100) DEFAULT NULL,`price` decimal(12,2) DEFAULT NULL,`image` varchar(255) DEFAULT NULL,`qrcode` varchar(255) DEFAULT NULL,`num` mediumint(5) DEFAULT '100',`sort_order` mediumint(5) DEFAULT '0',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8");
		$Item = M('Item');
		
		$ItemParams = M('ItemParams');
		$list = $Item->field('id,params')->where("params!=''")->order('id asc')->select();
		if($list){
			foreach($list as $li){
				$params = json_decode($li['params'],true);
				if($params){
					foreach($params as $param){
						$data = array(
							'item_id'=>$li['id'],
							'title'=>$param['title'],
							'price'=>$param['price'],
							'image'=>$param['image'],
							'qrcode'=>$param['qrcode'],
							'num'=>100,
							'sort_order'=>0,
						);
						$ItemParams->add($data);
					}
				}
				$Item->where(array('id'=>$li['id']))->setField('params','');
			}
		}
	}
}	