<?php


class codepay{
	
	private $config = array();
	function __construct($codepay_config){
		$this->config = $codepay_config;
	}
	function create_order($order){
		$codepay_config = $this->config;
		
	/**
	 * 这里可以自行创建站内订单将用户提交的数据保存到数据库生成订单号
	 *
	 * 嫌麻烦pay_id直接传送用户ID或用户名(中文用户名请确认编码一致)
	 * 我们支持GBK,gb2312,utf-8 如发送中文遇到编码困扰无法解决 可以尽量使用UTF-8
	 * 万能解决方法：base64或者urlencode加密后发送我们. 处理业务的时候转回来
	 */
	//构造要请求的参数数组，无需改动
	$payment = explode('-',$order['payment']);
	$parameter = array(
		"id" => (int)$codepay_config['codepay_id'],//平台ID号
		"type" => (int)$payment[1],//支付方式
		
		"price" => (float)$order['total_price'],//原价
		"pay_id" => $order['order_no'], //可以是用户ID,站内商户订单号,用户名
		"param" => '',//自定义参数
		"act" => 0,//此参数即将弃用
		"outTime" => 360,//二维码超时设置
		"page" => 4,//订单创建返回JS 或者JSON
		"return_url" => $codepay_config["return_url"],//付款后附带加密参数跳转到该页面
		"notify_url" => $codepay_config["notify_url"],//付款后通知该页面处理业务
		"style" => 1,//付款页面风格
		"pay_type" => 1,//支付宝使用官方接口
		"user_ip" => $this->getIp(),//付款人IP
		"qrcode_url" => '',//本地化二维码
		"chart" => 'utf-8',//字符编码方式
	);
	
	//print_r($parameter);exit;
	//简单的创建订单方式
	/*
	ksort($parameter); //重新排序$data数组
	reset($parameter); //内部指针指向数组中的第一个元素
	$sign = ''; //初始化需要签名的字符为空
	$urls = ''; //初始化URL参数为空
	foreach ($parameter AS $key => $val) { //遍历需要传递的参数
	    if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
	    if ($sign != '') { //后面追加&拼接URL
	        $sign .= "&";
	       $urls .= "&";
	   }
	    $sign .= "$key=$val"; //拼接为url参数形式
	    $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
	
	}
	$query = $urls . '&sign=' . md5($sign .$codepay_config['key']); //创建订单所需的参数
	$url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面
	header("Location:{$url}"); //跳转到支付页面
	*/
	
	
	
		$back = $this->create_link($parameter, $codepay_config['codepay_key']);



		/**
		 * 高级模式 云端创建订单。(注意不要外泄密钥key)
		 * 可自行根据订单返回的参数做一些高级功能。 以下demo只是简单的功能 其他需要自行开发
		 * 比如根据money type 参数调用本地的二维码图片。
		 * 比如根据云端订单状态创建失败 展示自定义转账的二维码。
		 * 比如可自行开发付款后的同步通知实现。
		 * 比如可自行开发软件端某个支付方式掉线。 自动停用该付款方式。
		 * 如使用云端同步通知  请附带必要的参数 码支付的用户id,pay_id,type,money,order_id,tag,notiry_key
		 * 必须将notiry_key参数返回 因为该参数为服务解密参数(会随时变化)。否则影响云端同步通知
		 */

		//$back = $this->create_link($parameter, $codepay_config['key'],$codepay_config['gateway']); //生成支付URL
		
		/*
		if (function_exists('file_get_contents')) { //如果开启了获取远程HTML函数 file_get_contents
			$codepay_json = file_get_contents($back['url']); //获取远程HTML
		} else if (function_exists('curl_init')) {
			$ch = curl_init(); //使用curl请求
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $back['url']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$codepay_json = curl_exec($ch);
			curl_close($ch);
		}
		*/
		$codepay_json = http($back['url']); //获取远程HTML
		//print_r($back);
		//dump($codepay_json);
		return $codepay_json;
	

		if (empty($codepay_json)) { //如果没有获取到远程HTML 则走JS创建订单
			$parameter['call'] = "callback";
			$parameter['page'] = "3";
			$back = $this->create_link($parameter, $codepay_config['codepay_key'],'https://codepay.fateqq.com/creat_order/?');
			$codepay_html = '<script src="' . $back['url'] . '"></script>'; //JS数据
		} else { //获取到了JSON
			$codepay_data = json_decode($codepay_json);
			$qr = $codepay_data ? $codepay_data->qrcode : '';
			$codepay_html = "<script>callback({$codepay_json})</script>"; //JSON数据
		}
	}



	//简单的创建订单方式
	//ksort($parameter); //重新排序$data数组
	//reset($parameter); //内部指针指向数组中的第一个元素
	//
	//$sign = ''; //初始化需要签名的字符为空
	//$urls = ''; //初始化URL参数为空
	//
	//foreach ($parameter AS $key => $val) { //遍历需要传递的参数
	//    if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
	//    if ($sign != '') { //后面追加&拼接URL
	//        $sign .= "&";
	//        $urls .= "&";
	//    }
	//    $sign .= "$key=$val"; //拼接为url参数形式
	//    $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
	//
	//}
	//$query = $urls . '&sign=' . md5($sign .$codepay_config['key']); //创建订单所需的参数
	//$url = "http://api2.fateqq.com:52888/creat_order/?{$query}"; //支付页面
	//
	//header("Location:{$url}"); //跳转到支付页面



	/**
	 * 加密函数
	 * @param $params 需要加密的数组
	 * @param $codepay_key //码支付密钥
	 * @param string $host //使用哪个域名
	 * @return array
	 */
	function create_link($params, $codepay_key, $host = ""){
		ksort($params); //重新排序$data数组
		reset($params); //内部指针指向数组中的第一个元素
		$sign = '';
		$urls = '';
		foreach ($params AS $key => $val) {
			if ($val == '') continue;
			if ($key != 'sign') {
				if ($sign != '') {
					$sign .= "&";
					$urls .= "&";
				}
				$sign .= "$key=$val"; //拼接为url参数形式
				$urls .= "$key=" . urlencode($val); //拼接为url参数形式
			}
		}

		$key = md5($sign . $codepay_key);//开始加密
		$query = $urls . '&sign=' . $key; //创建订单所需的参数
		$apiHost = ($host ? $host : "http://api2.fateqq.com:52888/creat_order/?"); //网关
		$url = $apiHost . $query; //生成的地址
		return array("url" => $url, "query" => $query, "sign" => $sign, "param" => $urls);
	}
		
	
	//获取客户端IP地址
	function getIp()
	{ //取IP函数
		static $realip;
		if (isset($_SERVER)) {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$realip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if (getenv('HTTP_X_FORWARDED_FOR')) {
				$realip = getenv('HTTP_X_FORWARDED_FOR');
			} else {
				$realip = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : getenv('REMOTE_ADDR');
			}
		}
		return $realip;
	}	
	
}