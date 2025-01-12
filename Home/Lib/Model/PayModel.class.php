<?php

 
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class PayModel extends Model {

	//当前网站地址
	private $aliziHost = '';

	public function __construct(){
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
        $this->aliziHost = $http_type.$_SERVER['HTTP_HOST'].C('ALIZI_ROOT');
	}
	//PC支付宝即时到账
	public function alipay($data,$aliziConfig){
		import('ORG.AlipayDirect.Alipay');
		$param  = array(
			'notify_url'    => $this->aliziHost.'/Api/alipay.php',    //服务器异步通知页面路径
			'return_url'    => $this->aliziHost.'/Api/alipayCallbak.php',  //页面跳转同步通知页面路径
			'merchant_url'  => $this->aliziHost,  //操作中断返回地址
			'seller_email'  => $aliziConfig['alipay_mail'],
			'out_trade_no'  => $data['order_no'],  //订单号
			'total_fee'     => floatval($data['total_price']),     //付款金额
			'subject'       => $data['item_name'].(empty($data['item_params'])?'':'-'.$data['item_params']),       //订单名称
		);
		$Alipay = new Alipay( $aliziConfig);
		$Alipay->submit($param);
	}

	//Wap支付宝
	public function alipayWap1($data,$aliziConfig){
		import('ORG.AliayWap.Alipay');
		$param  = array(
			'notify_url'    => $this->aliziHost.'/Api/alipayWap.php',    //服务器异步通知页面路径
			'call_back_url' => $this->aliziHost.'/Api/alipayCallbak.php',  //页面跳转同步通知页面路径
			'merchant_url'  => $this->aliziHost,  //操作中断返回地址
			'seller_email'  => $aliziConfig['alipay_mail'],
			'out_trade_no'  => $data['order_no'],  //订单号
			'total_fee'     => floatval($data['total_price']),     //付款金额
			'subject'       => $data['item_name'].(empty($data['item_params'])?'':'-'.$data['item_params']),       //订单名称
		);

		$Alipay = new Alipay($aliziConfig);
		$Alipay->submit($param);    
	}
	public function alipayWap($data,$aliziConfig){
		Vendor('alipay.wap.lib.alipay_submit#class');
		
        $out_trade_no = $data['order_no'];  //订单号
        $subject = $data['item_name'].(empty($data['item_params'])?'':'-'.$data['item_params']); //订单名称
        $total_fee = floatval($data['total_price']);     //付款金额
        $show_url = $data['url'];//收银台页面上，商品展示的超链接，必填
        $body = '';//商品描述，可空


		$alipay_config['partner'] = $aliziConfig['alipay_pid'];
		$alipay_config['seller_id']	= $aliziConfig['alipay_mail'];
		$alipay_config['key']   = $aliziConfig['alipay_key'];
		$alipay_config['notify_url'] = $this->aliziHost.'/Api/alipayWap.php';
		$alipay_config['return_url'] = $this->aliziHost.'/Api/alipayCallbak.php';
		$alipay_config['sign_type']    = strtoupper('MD5');
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';
		$alipay_config['payment_type'] = "1";
		$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";


		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service"       => $alipay_config['service'],
				"partner"       => $alipay_config['partner'],
				"seller_id"  => $alipay_config['seller_id'],
				"payment_type"	=> $alipay_config['payment_type'],
				"notify_url"	=> $alipay_config['notify_url'],
				"return_url"	=> $alipay_config['return_url'],
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
				"out_trade_no"	=> $out_trade_no,
				"subject"	=> $subject,
				"total_fee"	=> $total_fee,
				"show_url"	=> $show_url,
				"app_pay"	=> "Y",//启用此参数能唤起钱包APP支付宝
				"body"	=> $body,	
		);

		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "confirm");
		echo $html_text;  
	}

	//支付宝担保交易
	public function alipayDb($data,$aliziConfig){
		Vendor('alipay.dbPay.alipay#class');
		$param  = array(
			'notify_url'    => $this->aliziHost.'/Api/alipayDb.php',    //服务器异步通知页面路径
			'return_url' => $this->aliziHost.'/Api/alipayCallbak.php',  //页面跳转同步通知页面路径
			'merchant_url'  => $this->aliziHost,  //操作中断返回地址
			'seller_email'  => $aliziConfig['alipay_mail'],
			
			'out_trade_no'  => $data['order_no'],  //订单号
			'price'     => $data['total_price'],     //付款金额
			'subject'       => $data['item_name'].'  '.$data['item_params'],       //订单名称

			'name'       => $data['name'], 
			'address'    => $data['address'], 
			'zcode'       => $data['zcode'], 
			'receive_phone' => $data['mobile'], 
			'receive_mobile' => $data['mobile'], 
		);
		$alipay = new alipay($aliziConfig);
		$alipay->submit($param);    
	}

	
	//支付宝异步操作
	public function alipayNotify($aliziConfig){
        $out_trade_no = $_POST['out_trade_no'];//商户订单号
        $trade_no = $_POST['trade_no'];//支付宝交易号
        $trade_status = $_POST['trade_status'];//交易状态
        //支付记录
        $AlipayLog = M('AlipayLog');
        if($vo = $AlipayLog->create($_POST)){
            $vo['pay_type'] = 1; 
            $AlipayLog->add($vo);
        }
        import('ORG.AlipayDirect.Alipay');
        $Alipay = new Alipay($aliziConfig);
        $alipay_config = $Alipay->getConfig();
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
		
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
			
			
            if($trade_status == 'TRADE_FINISHED') {
                //1、开通了普通即时到账，买家付款成功后。
                //2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月、一年以内可退款等）后。
                logResult("TRADE_FINISHED");
            }else if ($trade_status == 'TRADE_SUCCESS') {
                //该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
				$where  = array('order_no'=>trim($out_trade_no));
				$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
				if($order['status']==0){
	            	$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$trade_no,
					'status'=>1,
					'remark'=>"支付宝单号：{$trade_no} ".htmlspecialchars($_POST['remark']),
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
	            	R('Api/aliziUpdateStatus',array('data'=>$data));
	            }
            }
			
            $this->paySuccess($order['id']);
            echo "success";
        }else {
            echo "fail";
        }
    }
    public function alipayWapNotify1($aliziConfig){
    	
		import('ORG.AliayWap.Alipay');
		$Alipay = new Alipay($aliziConfig);
		$alipayNotify = new AlipayNotify($Alipay->getConfig());
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
			$xml = simplexml_load_string($_REQUEST['notify_data']);
			if( ! empty($xml->notify_id) ) {
				$out_trade_no = $xml->out_trade_no;//商户订单号
				$trade_no = $xml->trade_no;//支付宝交易号
				$trade_status = $xml->trade_status;//交易状态
				$buyer_email = $xml->buyer_email;//购买者邮箱 
				
				$data = (array)$xml;
				$AlipayLog = M('AlipayLog');
				if($vo = $AlipayLog->create($_POST)){
					$vo['pay_type'] = 2; 
					$AlipayLog->add($vo);
				}
				
				//预付款
				if(strstr($out_trade_no,'deposit_')){
					$deposit = $this->depositPaySuccess($out_trade_no);
					if($deposit==true){  echo "success"; }
					exit;
				}
			
				if($trade_status == 'TRADE_FINISHED') {
					//该种交易状态只在两种情况下出现
					//1、开通了普通即时到账，买家付款成功后。
					//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。
					echo "success";		//请不要修改或删除
				}else if ($trade_status == 'TRADE_SUCCESS') {
					//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
					$where  = array('order_no'=>trim($out_trade_no));
					$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
					if($order['status']==0){
			            	$data = array(
							'order_id'=>$order['id'],
							'pay_sn'=>$trade_no,
							'status'=>1,
							'remark'=>"支付宝单号：{$trade_no} ".htmlspecialchars($_POST['remark']),
						);
						$data['sign'] = createSign($data,C('ALIZI_KEY'));
			            	R('Api/aliziUpdateStatus',array('data'=>$data));
			            }
                    $this->paySuccess($order['id']);
					echo "success";		//请不要修改或删除
				}
			}
		}else {
			echo "fail";//验证失败
		}
    }
	public function alipayWapNotify($aliziConfig){
    	
		Vendor('alipay.wap.lib.alipay_notify#class');
		
		$alipay_config['partner'] = $aliziConfig['alipay_pid'];
		$alipay_config['seller_id']	= $aliziConfig['alipay_mail'];
		$alipay_config['key']   = $aliziConfig['alipay_key'];
		$alipay_config['notify_url'] = $this->aliziHost.'/Api/alipayWap.php';
		$alipay_config['return_url'] = $this->aliziHost.'/Api/alipayCallbak.php';
		$alipay_config['sign_type']    = strtoupper('MD5');
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';
		$alipay_config['transport']    = 'http';
		$alipay_config['payment_type'] = "1";
		$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";
		
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		if($verify_result) {//验证成功
	
			//商户订单号

			$out_trade_no = $_POST['out_trade_no'];

			//支付宝交易号
			$trade_no = $_POST['trade_no'];

			//交易状态
			$trade_status = $_POST['trade_status'];

			$AlipayLog = M('AlipayLog');
			if($vo = $AlipayLog->create($_POST)){
				$vo['pay_type'] = 2; 
				$AlipayLog->add($vo);
			}
			
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
		
			if($_POST['trade_status'] == 'TRADE_FINISHED') {
			
			}else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
				$where  = array('order_no'=>trim($out_trade_no));
				$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
				if($order['status']==0){
					$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$trade_no,
					'status'=>1,
					'remark'=>"支付宝单号：{$trade_no} ".htmlspecialchars($_POST['remark']),
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
					R('Api/aliziUpdateStatus',array('data'=>$data));
				}
                $this->paySuccess($order['id']);
				echo "success";		//请不要修改或删除
			}else {
				//验证失败
				echo "fail";
			}

		}
	}
	
    public function alipayDbNotify($aliziConfig){
     	Vendor('alipay.dbPay.alipay#class');
     	$alipay = new alipay($aliziConfig);
     	$alipayNotify = new AlipayNotify($alipay->getConfig());
		$verify_result = $alipayNotify->verifyNotify();
		$verify_result =1;
		if($verify_result) {
			$out_trade_no = $_POST['out_trade_no'];//订单号
			$trade_no = $_POST['trade_no'];//支付宝交易号
			$trade_status = $_POST['trade_status'];//交易状态
			$where  = array('order_no'=>trim($out_trade_no));
			$order = M('Order')->where($where)->find(); 
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}

			if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
				//该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
		     	echo "success";	
		     }else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
				//该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
		     	if($order['status']==0){
		     		$data = array(
						'order_id'=>$order['id'],
						'status'=>1,
						'remark'=>htmlspecialchars($_POST['trade_no']),
					);
					$data['sign'] = createSign($data,C('ALIZI_KEY'));
			          R('Api/aliziUpdateStatus',array('data'=>$data));
		     	}
		        	echo "success";
		     }else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
				//该判断表示卖家已经发了货，但买家还没有做确认收货的操作
	     		$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$_POST['trade_no'],
					'status'=>3,
					'remark'=>htmlspecialchars($_POST['remark']),
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
		          R('Api/aliziUpdateStatus',array('data'=>$data));
		        	echo "success";	
		    }else if($_POST['trade_status'] == 'TRADE_FINISHED') {
				//该判断表示买家已经确认收货，这笔交易完成
			    	$data = array(
					'order_id'=>$order['id'],
					'status'=>4,
					'remark'=>htmlspecialchars($_POST['remark']),
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
		          R('Api/aliziUpdateStatus',array('data'=>$data));
		        echo "success";
		    }else {
				//其他状态判断
		        echo "success";
		    }
		}else {
		    //验证失败
		    echo "fail";
		}
     }

     public function wxPayNotify(){
        $result_code = $_POST['result_code'];
        $out_trade_no_array = explode('-',$_POST['out_trade_no']);
        $transaction_id = $_POST['transaction_id'];
        if('SUCCESS'===$result_code){
			
			$out_trade_no = trim($out_trade_no_array[0]);
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
			
            $where  = array('order_no'=>trim($out_trade_no));
            $order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
            if($order['status']==0){
                $data = array(
	                'order_id'=>$order['id'],
					'pay_sn'=>$transaction_id,
	                'status'=>1,
	                'remark'=>"微信支付单号：".htmlspecialchars($transaction_id),
	            );
	            $data['sign'] = createSign($data,C('ALIZI_KEY'));
	            R('Api/aliziUpdateStatus',array('data'=>$data));
            }
            $this->paySuccess($order['id']);
            echo "success";
        }
     }	
	 
	 public function yunpayNotify($aliziConfig){
		$yun_config['partner'] = $aliziConfig['yunpay_pid'];//合作身份者id
		$yun_config['key'] = $aliziConfig['yunpay_key'];//安全检验码
		$seller_email = $aliziConfig['yunpay_email'];//云会员账户（邮箱）
		$GLOBALS['i2ekeys']=$yun_config['key'];
		Vendor('yunpay.lib.yun_md5#function');
		
		$yunNotify = md5Verify($_REQUEST['i1'],$_REQUEST['i2'],$_REQUEST['i3'],$yun_config['key'],$yun_config['partner']);
		if($yunNotify) {
			
			$out_trade_no = $_REQUEST['i2'];//商户订单号
			$trade_no = $_REQUEST['i4'];//云支付交易号
			$yunprice=$_REQUEST['i1'];//价格
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
			
			$where  = array('order_no'=>trim($out_trade_no));
            $order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
			
			if($order['status']==0){
				$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$trade_no,
					'status'=>1,
					'remark'=>htmlspecialchars($trade_no),
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
				R('Api/aliziUpdateStatus',array('data'=>$data));
			}
            $this->paySuccess($order['id']);
			echo "success";
		}else {
			echo "fail";//验证失败,请不要修改或删除
		}
	 }

    function registNotify(){
        list($user_id, $coupon, $time) = explode('_',$_POST['out_trade_no']);
        M('User')->where('id='.$user_id)->setField('status',1);
        if(!empty($coupon)){
            $data = array('is_used'=>1,'used_user'=>$user_id,'used_time'=>time());
            M('Coupon')->where("code='{$coupon}'")->save($data);
        }
        echo "success";
    }
	
	function codepayNotify($aliziConfig){ 
        
		ksort($_POST); 
		reset($_POST); 
		$codepay_key= $aliziConfig['codepay_key'];
		$sign = '';
		foreach ($_POST AS $key => $val) {
			if ($val == '' || $key == 'sign') continue;
			if ($sign) $sign .= '&'; 
			$sign .= "$key=$val";
		}
		

		if (!$_POST['pay_no'] || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
			exit('fail');  //返回失败 继续补单
		} else { //合法的数据
			//业务处理
			$out_trade_no = trim($_POST['pay_id']); //需要充值的ID 或订单号 或用户名
			$money = (float)$_POST['money']; //实际付款金额
			$price = (float)$_POST['price']; //订单的原价
			$param = $_POST['param']; //自定义参数
			$pay_no = $_POST['pay_no']; //流水号
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
			
			
			$where  = array('order_no'=>trim($out_trade_no));
            $order = M('Order')->field('id,item_id,order_no,status,mobile,mail,total_price')->where($where)->find();
			
			/*
			if($price!=$order['total_price']){
				$data = array(
	                'order_id'=>$order['id'],
	                'status'=>6,
	                'remark'=>htmlspecialchars($pay_no.'实际收到付款金额：'.$price),
	            );
	            $data['sign'] = createSign($data,C('ALIZI_KEY'));
	            R('Api/aliziUpdateStatus',array('data'=>$data));
				exit('success');
			}
			*/
			
            if($order['status']==0){
                $data = array(
	                'order_id'=>$order['id'],
					'pay_sn'=>$pay_no,
	                'status'=>1,
	                'remark'=>htmlspecialchars($pay_no),
	            );
	            $data['sign'] = createSign($data,C('ALIZI_KEY'));
	            R('Api/aliziUpdateStatus',array('data'=>$data));
            }
			
            $this->paySuccess($order['id']);
			
			exit('success'); //返回成功 不要删除哦
		} 
    }
	function paypayNotify($aliziConfig){ 
	
		$api_user = $aliziConfig['paypay_user'];
		$api_key = $aliziConfig['paypay_key'];
	  
		$params = array(
			'ppz_order_id'=>$_POST['ppz_order_id'],
			'order_id'=>$_POST['order_id'],
			'price'=>$_POST['price'],
			'real_price'=>$_POST['real_price'],
			'order_info'=>$_POST['order_info'],
		);
		ksort($params); 
		
		$param_str = $api_key;
		foreach($params as $param){
			$param_str .= $param;
		}
		// 签名 
		$signature = md5($param_str);
		
		if($_POST['signature'] == $signature){
			
			$out_trade_no = trim($_POST['order_id']);
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo "success"; }
				exit;
			}
			
			$where  = array('order_no'=>$out_trade_no);
            $order = M('Order')->field('id,item_id,order_no,status,mobile,mail,total_price')->where($where)->find();
			
			 if($order['status']==0){
                $data = array(
	                'order_id'=>$order['id'],
					'pay_sn'=>$_POST['ppz_order_id'],
	                'status'=>1,
	                'remark'=>htmlspecialchars($_POST['ppz_order_id']),
	            );
	            $data['sign'] = createSign($data,C('ALIZI_KEY'));
	            R('Api/aliziUpdateStatus',array('data'=>$data));
            }
			
            $this->paySuccess($order['id']);
			
			exit('success'); //返回成功 不要删除哦
		}else{
			errorLog($_POST,'paypay_signerror');
		}
    }
	
	function gleepayNotify($aliziConfig){ 
       if(!isset($_POST) || empty($_POST["signInfo"])) {
			$_POST = $_GET;
		}	
		
		//start session             
		$signKey       = $aliziConfig['creditcard_key'];//$_SESSION['signKey'];
		$merNo		   = $aliziConfig["creditcard_mid"]; 
		$gatewayNo     = $aliziConfig["creditcard_gateway"];	
		$tradeNo       = $_POST["tradeNo"];
		$out_trade_no  = $_POST["orderNo"];  
		$orderAmount   = $_POST["orderAmount"];   
		$orderCurrency = $_POST["orderCurrency"]; 
		$orderStatus   = $_POST["orderStatus"]; 
		$orderInfo     = $_POST["orderInfo"];  
		$signInfo      = $_POST["signInfo"];
		$remark        = $_POST["remark"];   
		$sha256src     = $merNo.$gatewayNo.$tradeNo.$out_trade_no.$orderCurrency.$orderAmount.$orderStatus.$orderInfo.$signKey;
		$mysign		   = strtoupper(hash("sha256",$sha256src));
		
		$payResult = "";
		if($mysign == $signInfo){ 
			if($orderStatus == "1"){ 	 
				$payResult = "Congratulations,payment is successful !";
				
				//预付款
				if(strstr($out_trade_no,'deposit_')){
					$deposit = $this->depositPaySuccess($out_trade_no);
					if($deposit==true){  echo $payResult; }
					exit;
				}
				
				$where  = array('order_no'=>trim($out_trade_no));
				$order = M('Order')->field('id,item_id,order_no,status,mobile,mail,total_price')->where($where)->find();
				if($order['status']==0){
					$data = array(
						'order_id'=>$order['id'],
						'pay_sn'=>$tradeNo,
						'status'=>1,
						'remark'=>htmlspecialchars($tradeNo),
					);
					$data['sign'] = createSign($data,C('ALIZI_KEY'));
					R('Api/aliziUpdateStatus',array('data'=>$data));
				}
				$this->paySuccess($order['id']);
				
			
			}else if($orderStatus == "-1" || $orderStatus == "-2"){ 
				$payResult = "Processing";
			}else if($orderStatus == "0"){
				$payResult = "Sorry,payment is failure !";
			}		
		} else {
			$payResult = "Data validation failed";
		}
		
		//destroy session
		session_destroy();
		echo $payResult;
    }
	public function payseraNotify($aliziConfig){
		Vendor('paysera.WebToPay');
		//errorLog($_REQUEST,'./Public/paysera_request.log');
			
		try {
			$response = WebToPay::checkResponse($_REQUEST, array(
				'projectid'     => $aliziConfig['paysera_projectid'],
				'sign_password' => $aliziConfig['paysera_password'],
			));
			errorLog($_REQUEST,'./Public/paysera_request.log');
			errorLog($response,'./Public/paysera_response.log');
			/*
			if ($response['test'] !== '0') {
				throw new Exception('Testing, real payment was not made');
			}
			if ($response['type'] !== 'macro') {
				throw new Exception('Only macro payment callbacks are accepted');
			}
			*/
		 
			$out_trade_no = $response['orderid'];
			$amount = $response['amount'];
			$currency = $response['currency'];
			//@todo: patikrinti, ar užsakymas su $orderId dar nepatvirtintas (callback gali būti pakartotas kelis kartus)
			//@todo: patikrinti, ar užsakymo suma ir valiuta atitinka $amount ir $currency
			//@todo: patvirtinti užsakymą
			
			
			//预付款
			if(strstr($out_trade_no,'deposit_')){
				$deposit = $this->depositPaySuccess($out_trade_no);
				if($deposit==true){  echo 'OK'; }
				exit;
			}
				
			
			$where  = array('order_no'=>$out_trade_no);
            $order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
            if($order['status']==0){
                $data = array(
	                'order_id'=>$order['id'],
					'pay_sn'=>$out_trade_no,
	                'status'=>1,
	                'remark'=>$currency,
	            );
	            $data['sign'] = createSign($data,C('ALIZI_KEY'));
	            R('Api/aliziUpdateStatus',array('data'=>$data));
            }
            $this->paySuccess($order['id']);
			
			echo 'OK';
		} catch (Exception $e) {
			echo get_class($e) . ': ' . $e->getMessage();
		}

     }	

	 public function apipayNotify($aliziConfig){
	 
		$ReturnArray = array( // 返回字段
			"memberid" => $_REQUEST["memberid"], // 商户ID
			"orderid" =>  $_REQUEST["orderid"], // 订单号
			"amount" =>  $_REQUEST["amount"], // 交易金额
			"datetime" =>  $_REQUEST["datetime"], // 交易时间
			"transaction_id" =>  $_REQUEST["transaction_id"], // 支付流水号
			"returncode" => $_REQUEST["returncode"],
		);
	  
		$Md5key = $aliziConfig['apipay_key'];

		ksort($ReturnArray);
		//reset($ReturnArray);
		$md5str = "";
		foreach ($ReturnArray as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}
		$sign = strtoupper(md5($md5str . "key=" . $Md5key));
		if ($sign == $_REQUEST["sign"]) { 
			if ($_REQUEST["returncode"] == "00") {
			
				$where  = array('order_no'=>$_REQUEST["orderid"]);
				$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
				if($order['status']==0){
					$data = array(
						'order_id'=>$order['id'],
						'pay_sn'=>$_REQUEST["transaction_id"],
						'status'=>1,
						'remark'=>$currency,
					);
					$data['sign'] = createSign($data,C('ALIZI_KEY'));
					R('Api/aliziUpdateStatus',array('data'=>$data));
				}
			
				$this->paySuccess($order["id"]);
			    //$str = "交易成功！订单号：".$_REQUEST["orderid"];
			    //file_put_contents("./Public/success.txt",$str."\n", FILE_APPEND);
			    exit("OK");
			}
		}
		 file_put_contents("./Public/orderid.txt",$_REQUEST["orderid"]."\n", FILE_APPEND);

     }	
	 
	public function xorpayNotify($aliziConfig){
		
		Vendor('xorpay.xorpay');
		$xorpay = new xorpay($aliziConfig);

		$sign = $xorpay->sign(array($_POST['aoid'], $_POST['order_id'], $_POST['pay_price'], $_POST['pay_time'], $aliziConfig['xorpay_secret']));

		# 对比签名
		if($sign == $_POST['sign']) {
			# 签名验证成功，更新数据
			$where  = array('order_no'=>$_REQUEST["order_id"]);
			$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
			if($order['status']==0){
				$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$_POST["aoid"],
					'status'=>1,
					'remark'=>$currency,
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
				R('Api/aliziUpdateStatus',array('data'=>$data));
			}
		
			$this->paySuccess($order["id"]);
			
			echo 'ok';
		} else {
			# 签名验证错误

			header("HTTP/1.0 405 Method Not Allowed");
			exit();
		};

	}
	 
	public function ispayNotify($aliziConfig){
		
		Vendor('ispay.lib.Ispay#class');
		$Ispay = new ispayService($aliziConfig['ispay_id'],$aliziConfig['ispay_key']);
		
		//接受ISPAY通知返回的支付渠道
		$Array['payChannel'] = $_POST['payChannel']; //(支付通道)
		//接受ISPAY通知返回的支付金额
		$Array['Money'] = $_POST['Money'];  //(单位分)
		//接受ISPAY通知返回的订单号
		$Array['orderNumber'] = $_POST['orderNumber'];  //(商户订单号)
		//接受ISPAY通知返回的附加数据
		$Array['attachData'] = $_POST['attachData'];  //(商户自定义附加数据)
		//接受ISPAY通知返回的回调签名
		$Array['callbackSign'] = $_POST['callbackSign'];  //(详情查看ISPAY开发文档)
		//回调签名校验
		if($Ispay->callbackSignCheck($Array)){
			$where  = array('order_no'=>$_REQUEST["orderNumber"]);
			$order = M('Order')->field('id,item_id,order_no,status,mobile,mail')->where($where)->find();
			if($order['status']==0){
				$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$_POST["payChannel"],
					'status'=>1,
					'remark'=>$currency,
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
				R('Api/aliziUpdateStatus',array('data'=>$data));
			}
		
			$this->paySuccess($order["id"]);
			
			echo "SUCCESS";
		}else{
			echo "callbackSign fail!";
			exit;
		}
	}
	
	public function vivcmsNotify($aliziConfig){
	
		if($_REQUEST["rtnCode"]=='0000'){
			$tranFlow = explode('-',$_REQUEST["tranFlow"]);
			$where  = array('order_no'=>$tranFlow[0]);
			$order = M('Order')->field('id,item_id,order_no,status,mobile,mail,total_price')->where($where)->find();
			if($order['status']==0 && $order['total_price']==number_format($_REQUEST['amount']/100,2)){
				$data = array(
					'order_id'=>$order['id'],
					'pay_sn'=>$_REQUEST['paySerialNo'],
					'status'=>1,
					'remark'=>$_REQUEST['paySerialNo'],
				);
				$data['sign'] = createSign($data,C('ALIZI_KEY'));
				R('Api/aliziUpdateStatus',array('data'=>$data));
				$this->paySuccess($order["id"]);
				echo "success";
			}
		}
	}

    //支付成功后执行
    function paySuccess($order_id){

        //优惠券支付
        $Order = M('Order');
        $Coupon = M('Coupon');
        $info = $Order->where(array('id'=>$order_id))->field('id,coupon')->find();
        if($info && !empty($info['coupon'])){
            //修改优惠券状态
            $coupon_value = $Coupon->where('is_used=0 and types=2')->getField('value');
            if($coupon_value){
                $data = array('is_used'=>1,'used_user'=>$order_id,'used_time'=>time());
                $Coupon->where("code='{$info['coupon']}'")->save($data);
            }

            //将其它使用优惠券号提交却还没支付的订单,还原价
            $where = "id!=$order_id AND coupon='{$info['coupon']}'";//array('coupon'=>$info['coupon']);
            $Order->where($where)->setInc('total_price',$coupon_value);
            $Order->where($where)->setField('coupon','');
        }
    }
	
	//预付款支付成功
	function depositPaySuccess($out_trade_no){
		$orderNo = explode('_',$out_trade_no);
		$order_no = $orderNo[1];
		$Order = M('Order');
		$where  = array('order_no'=>$order_no);
		$rs = $Order->where($where)->setField('deposit_ispay',1);
		return true;
	}
}