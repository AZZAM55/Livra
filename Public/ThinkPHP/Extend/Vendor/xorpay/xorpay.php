<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class xorpay{
	
	private $url  = "https://xorpay.com/api/";//提交地址
	private $config = array();
	function __construct($apipay_config){
		$this->config = $apipay_config;
	}
	function cashier($order,$is_return=false){
		
		$price = $order['total_price']; # 从 URL 获取充值金额 price
		$name = trim($order['item_name']);  # 订单商品名称
		$pay_type = 'jsapi';     # 付款方式
		$order_id = $order['order_no'];    # 自己创建的本地订单号
		$notify_url = $this->config['notify_url'];   # 回调通知地址
		$return_url = $this->config['return_url'];   # 支付成功返回地址

		$secret  = $this->config['xorpay_secret'];     # app secret, 在个人中心配置页面查看
		$api_url = $this->url.'cashier/'.$this->config['xorpay_aid'];   # 付款请求接口，在个人中心配置页面查看

		$sign = $this->sign(array($name, $pay_type, $price, $order_id, $notify_url, $secret));
		if($is_return==true){
			$return = array(
				'url'=>$api_url,
				'data'=>array(
					'name'=>$name,
					'pay_type'=>$pay_type,
					'price'=>$price,
					'order_id'=>$order_id,
					'notify_url'=>$notify_url,
					'return_url'=>$return_url,
					'sign'=>$sign,
				)
			);
			return $return;
		}else{
echo '<html>
      <head><title>支付跳转中...</title></head>
      <body>
          <form id="post_data" action="'.$api_url.'" method="post">
              <input type="hidden" name="name" value="'.$name.'"/>
              <input type="hidden" name="pay_type" value="'.$pay_type.'"/>
              <input type="hidden" name="price" value="'.$price.'"/>
              <input type="hidden" name="order_id" value="'.$order_id.'"/>
              <input type="hidden" name="notify_url" value="'.$notify_url.'"/>
              <input type="hidden" name="return_url" value="'.$return_url.'"/>
              <input type="hidden" name="sign" value="'.$sign.'"/>
          </form>
          <script>document.getElementById("post_data").submit();</script>
      </body>
      </html>';
	  }
	}	
	
	function alipay($order){
		
		$price = $order['total_price']; # 从 URL 获取充值金额 price
		$name = $order['item_name'];  # 订单商品名称
		$pay_type = 'alipay';     # 付款方式
		$order_id = $order['order_no'];    # 自己创建的本地订单号
		$notify_url = $this->config['notify_url'];   # 回调通知地址

		$secret  = $this->config['xorpay_secret'];     # app secret, 在个人中心配置页面查看
		$api_url = $this->url.'pay/'.$this->config['xorpay_aid'];   # 付款请求接口，在个人中心配置页面查看

		$sign = $this->sign(array($name, $pay_type, $price, $order_id, $notify_url, $secret));
		
		$data = array(
			'name'=>$name,
			'pay_type'=>$pay_type,
			'price'=>$price,
			'order_id'=>$order_id,
			'order_uid'=>'',
			'notify_url'=>$notify_url,
			'return_url'=>$this->config['return_url'],
			'more'=>'',
			'expire'=>7200,
			'sign'=>$sign,
		);
		$result = http($api_url,'post',$data);
		return $result;
		
	}	
	
	function sign($data_arr) { 
		return md5(join('',$data_arr)); 
	}
}