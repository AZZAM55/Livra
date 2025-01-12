<?php
class apipay{
	
	private $url  = "http://www.zhongtuanpay.com/Pay_Index.html";//提交地址
	private $config = array();
	function __construct($apipay_config){
		$this->config = $apipay_config;
	}
	function curl_post($order){
		$apipay_config = $this->config;
		
		$payment = explode('-',$order['payment']);
		
		$pay_memberid = $this->config['apipay_memberid'];   //商户ID
		$Md5key = $this->config['apipay_key'];   //密钥

		$pay_orderid = $order['order_no'];    //订单号
		$pay_amount =  $order['total_price'];    //交易金额
		$pay_bankcode = (int)$payment[1];

		//if(empty($pay_memberid)||empty($pay_amount)||empty($pay_bankcode)){ die("信息不完整！"); }

		$pay_applydate = date("Y-m-d H:i:s");  //订单时间
		$pay_notifyurl = $apipay_config["notify_url"];   //服务端返回地址
		$pay_callbackurl = $apipay_config["return_url"];  //页面跳转返回地址


		//扫码
		$native = array(
			"pay_memberid" => $pay_memberid,
			"pay_orderid" => $pay_orderid,
			"pay_amount" => $pay_amount,
			"pay_applydate" => $pay_applydate,
			"pay_bankcode" => $pay_bankcode,
			"pay_notifyurl" => $pay_notifyurl,
			"pay_callbackurl" => $pay_callbackurl,
		);

	
		$sign = $this->getSign($native);
		
		$native["pay_md5sign"] = $sign;
		$native['pay_attach'] = $order['item_sn'];
		$native['pay_productname'] = $order['item_name'];

		$native['only_get_code'] = 'yes';

		$res = $this->post_request($this->url,$native);
		return $res;

	}	
	
	function getSign($params){
		ksort($params);
		$md5str = "";
		foreach ($params as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}

		return strtoupper(md5($md5str . "key=" . $this->config['apipay_key']));
	}
	
	function post_request($url, $data = []){
		$curlObj = curl_init();

		curl_setopt($curlObj, CURLOPT_URL, $url);
		curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlObj, CURLOPT_POST, 1);
		curl_setopt($curlObj, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curlObj);

		curl_close($curlObj);

		return $result;
	}	
}