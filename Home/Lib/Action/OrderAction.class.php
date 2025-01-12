<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
defined('THINK_PATH') OR exit();
class OrderAction extends AliziAction {

    public function _initialize(){
        parent::_init();
		C('DEFAULT_THEME','');
    }

    public function index($id,$tpl='index'){
	    if(empty($id) || !ctype_alnum($id))$this->error(lang('error'));
		$sn = $id;
		
		/*
		$adsDefend = parent::adsDefend($sn);
		if(in_array($adsDefend['status'],array(2,3)) ){
			$target = $adsDefend['target'];
			if(ctype_alnum($target)){
				$id = $target;
			}else{
				header('location:'.$target);exit;
			}
		}
		*/
		
		
		$ipCloak = parent::ipCloak($sn);
	
		if($ipCloak['status']==1){
			$target = $ipCloak['target'];
			if(ctype_alnum($target)){
				$id = $target;
			}else{
				header('location:'.$target);exit;
			}
		}
		
		global $info;
        $info  = M('Item')->where(array('sn'=>$id))->find();
		  
		if(empty($info) || $info['is_delete']==1 || $info['status']==2) $this->error(lang('empty_item'));
		if(!empty($info['facebook_pixel_id']) && !isset($_GET['fbpid'])){ session('fbpid',$info['facebook_pixel_id']);}
 
        
        $template  = getCache('ItemTemplate',array('id'=>$info['id']),true);
        $template['extend'] = unserialize($template['extend']);
        $template['color'] = json_decode($template['color'],true);
        if(isset($_GET['theme'])) $template['template']=str_replace('-', '/', $_GET['theme']);
	
		$info['original_content'] = $info['content'];
		if($tpl=='detail'){	
			$info['content'] = str_replace('<img src="/','<img src="'.$this->aliziConfig['alizi_host'].'/',$info['content']);
			$_GET['page'] = 'detail';
			$tpl = file_exists(TMPL_PATH.'Alizi/'.$template['template'].'/detail.html')?"Alizi/{$template['template']}/detail":'Order/detail';
        }else{
			$_GET['page'] = 'single';
			$info['content'] = "{[AliziOrder]}";
            $tpl = "/Order/".$tpl;
        }
	
		$_GET['id'] = $id;
		
		import("Home.Alizi.AliziTags");
		$info = AliziTags::alizi($info);
		
		
		
		//parent::item_pv($info['id']);//统计

		//加密内容	
		if($this->aliziConfig['is_encode']){
			$info['content'] = '<script tyle="text/javascript" src="'.$this->aliziHost.'Public/Alizi/seajs/alizi/base64.js?v='.ALIZI_VERSION.'"></script><script tyle="text/javascript">var content="Alizi'.base64_encode($info['content']).'";var b = new Base64();document.write(b.decode(content));</script>';
		}
		
		$this->assign('template',$template);
        $this->assign('info',$info);
 
		
        if(isset($_GET['buildHtml'])){
			$sn = intval($_GET['uid'])<=1?$sn:$sn.'-'.$_GET['uid'];
			$html = $this->aliziHost.$this->aliziConfig['html_file'].$sn.C('HTML_FILE_SUFFIX');
			$this->assign('thisUrl',$html);
			
            $this->buildHtml($sn,$this->aliziConfig['html_file']?$this->aliziConfig['html_file']:'./',$tpl);
            header("location:".$html);
        }else{
			$html = $this->host.urlencode($_SERVER['REQUEST_URI']);
			$this->assign('thisUrl',$html);
			$this->display($tpl);
		}
    }
	public function admin($id,$tpl='admin'){
		if($_SESSION['user']['role']!='admin'){
			$this->error('404');
		}
		$this->index($id,$tpl);
	}
	
	public function adminBooking(){
		if($_SESSION['user']['role']!='admin'){
			echo json_encode(array('status'=>0,'message'=>lang('error')));exit;
		}
		$Order = D('Order');
		if($vo=$Order->create()){
			$order_id = $Order->add();
			$rs = array('status'=>1,'message'=>lang('success'),'data'=>$orderData);
			echo json_encode($rs);
		}else{
			echo json_encode(array('status'=>0,'message'=>lang('error')));
		}
	}
    public function aliziBooking(){
		$_POST = $_REQUEST;
		if(isset($_POST['name']))$_POST['name'] = strFilter($_POST['name']);
        $_POST['sign'] = createSign($_POST,C('ALIZI_KEY'));
		 
        $result = R('Api/aliziBooking',array('data'=>$_POST));

        $data  = array();
        if($result['status']==1){
            $data = array('order_id'=>$result['data']['order_id'],'order_no'=>$result['data']['order_no'],'total_price'=>$result['data']['total_price']);
        }
		
		if(isset($_REQUEST['callback'])){
			$ret = array('data'=>$data,'info'=>$result['message'],'status'=>$result['status']);
			$this->ajaxReturn($data,'jsonp');exit;
		}
        if(IS_AJAX){
            $this->ajaxReturn($data,$result['message'],$result['status']);
        }else{
            if($result['status']==1){
                header("location:".U('Order/pay',array('order_no'=>$data['order_no'])));
            }else{
                $this->error($result['message']);
            }
        }
    }

    public function getAliziPrice(){
		$payment = explode('-',$_POST['payment']);
        $data = array(
            'sn'=>$_POST['sn'],
            'quantity'=>(int)$_POST['quantity'],
            'item_params'=>trim($_POST['params']),
            'payment'=>$payment[0],
        );
        $data['sign'] = createSign($data,C('ALIZI_KEY'));
        $result = R('Api/getAliziPrice',array('data'=>$data));
        $this->ajaxReturn($result,'success',1);
    }

    public function query(){
        if(IS_POST){
            $kw = strFilter($_POST['kw']);
            $Model = M('Order');
            $Item  = M('Item');
            $OrderLog = M('OrderLog');
            $status = C('ORDER_STATUS');
            $where = "is_delete=0 AND (mobile='{$kw}' OR order_no='{$kw}' OR name='{$kw}')";
            $orders = $Model->where($where)->order('id desc')->select();

            $list = array();
            if($orders){
                foreach($orders as $li){
                    $item_extends = json_decode($li['item_extends'],true);
                    $itemExtends = '';
                    foreach($item_extends as $k=>$v){$itemExtends.=$k.lang('colon').(is_array($v)?implode(' ', $v):$v)."<br>";}
					
					if($li['status']==3){
						$items = $Item->field('is_auto_send,send_content')->where(array('id'=>$li['item_id']))->find();
					}else{
						$items = array( 'is_auto_send'=>0, 'send_content'=>'', );
					}
                    $list[] = array(
                        'title'=>$li['item_name'],
                        'order_no'=>$li['order_no'],
                        'order_status'=>(int)$li['status'],
                        'status'=>strip_tags($status[$li['status']]),
                        'payment'=>$li['payment'],
                        'quantity'=>$li['quantity'],
                        'price'=>getPrice($li['total_price']),
                        'name'=>$li['name'],
                        'params'=>$li['item_params'],
                        'itemExtends'=>$itemExtends,
                        'datetime'=>substr($li['datetime'],0,10),

                        'address'=>$li['region'].$li['address'],
                        'time'=>date('Y-m-d H:i:s',$li['add_time']),
                        'express'=>experss($li['delivery_name'],$li['delivery_no'],$li['id'],$li['order_no']),
                        'qq'=>$li['qq'],
                        'is_auto_send'=>$items['is_auto_send'],
                        'send_content'=>nl2br($items['send_content']),
                    );
                }
            }
            if($list){
                $this->ajaxReturn(array('title'=>'query','list'=>$list),'true',1);
            }else{
                $this->ajaxReturn(array('title'=>'query','list'=>null),lang('empty'),0);
            }
        }else{
            $this->display();
        }
    }
    public function apply(){
        if(IS_POST){
            $order_no = strFilter($_POST['order_no']);
            $mobile = strFilter($_POST['mobile']);
            $Model = M('Order');
            $where = array('order_no'=>$order_no,'mobile'=>$mobile);
            $order = $Model->where($where)->field('id,status')->find();

            if(empty($order)){
                $msg = lang('empty');
            }else{
                switch($order['status']){
                    case '1':
                        $msg = lang('applySuccess');
                        $data = array(
                            'order_id'=>$order['id'],
                            'status'=>8,
                            'remark'=>htmlspecialchars($_POST['refund_payment'].' , '.$_POST['refund_account']),
                        );
                        $data['sign'] = createSign($data,C('ALIZI_KEY'));
                        $rs = http($this->aliziHost."index.php?m=Api&a=aliziUpdateStatus", 'POST', array('data'=>$data));
                        break;
                    case '8':$msg = lang('applyIn');break;
                    default: $msg = lang('status_err');
                }
            }
            $this->ajaxReturn(1,$msg,1);
        }else{
            $this->display();
        }
    }

    public function pay(){
		
		//设置支付域名
		if(!empty($this->aliziConfig['payment_url'])){
			$payment_url = parse_url($this->aliziConfig['payment_url']);
			if(isset($payment_url['host']) && $payment_url['host']!=$_SERVER['HTTP_HOST']){
				header("location:{$this->aliziConfig['payment_url']}{$_SERVER['REQUEST_URI']}");exit;
			}
		}
		
        $order_no = strFilter($_GET['order_no']);
        $is_deposit = isset($_GET['deposit']);//预付款
        $order = M('Order')->where(array('order_no'=>$order_no))->find();
        if($order['status']!=0){
            $this->redirect('Order/result',array('order_no'=>$order['order_no']));
        }
		
		//预付款
		if($is_deposit){
			$order['item_params'] = L('deposit');
			$order['order_no'] = "deposit_".$order['order_no'];
			$order['total_price'] = $order['deposit'];
		}
		
        $payment = isset($_GET['payment'])?$_GET['payment']:$order['payment'];
		$subpayment = explode('-',$payment);

		$redirectUrl = $order['url'];
		$options = json_decode($this->aliziConfig['order_options'],true);
		if(in_array($order['order_page'],array('single','detail'))){
			$template = M('ItemTemplate')->where(array('id'=>$order['item_id']))->field('redirect_uri,options')->find();
			if($template){
				if($template['redirect_uri']){
					$redirectUrl = $template['redirect_uri']=='1'?$order['referer']:$template['redirect_uri'];
				}
				$options = json_decode($template['options'],true);
			}
		}
		$this->assign('redirectUrl',$redirectUrl);
				
        $this->assign('order',$order);
        switch ($subpayment[0]) {
            case 'alipay':
                $this->alipayInWx($order);
                $this->display('alipay');
				$this->payAlipay($order);
                break;
            case 'codepay':
				Vendor('codepay.codepay');
				$this->aliziConfig['notify_url'] = $this->aliziHost."Api/codepay.php";
				$this->aliziConfig['return_url'] = $this->aliziHost."index.php?m=Order&a=result&order_no={$order['order_no']}";
				$codepay = new codepay($this->aliziConfig);
				$pay = $codepay->create_order($order);
				
				$this->assign('pay',json_decode($pay,true));
				$this->assign('payJson',$pay);
				$this->display('codepay');
                break;
            case 'paypay':
				$api_url = 'https://www.paypayzhu.com/api/pay';
				$api_user = $this->aliziConfig['paypay_user'];
				$api_key = $this->aliziConfig['paypay_key'];
				// 从网页传入 type [1: 微信, 2: 支付宝]
				//$type = $this->aliziConfig['paypay_type'];
				$type = $subpayment[1];
				$price = $order['total_price'];
				$order_id = $order['order_no'];
				$order_info = trim($order['item_name']);
				// 用户支付成功之后, 跳转到的页面
				$redirect = $this->aliziHost."index.php?m=Order&a=result&order_no={$order['order_no']}";

				// 签名 
				$signature = md5($api_key. $api_user. $order_id. $order_info. $price. $redirect. $type);

				$pay['api_user'] = $api_user;
				$pay['price'] = $price;
				$pay['type'] = $type;
				$pay['redirect'] = $redirect;
				$pay['order_id'] = $order_id;
				$pay['order_info'] =$order_info;
				$pay['signature'] = $signature;
				//$pay = http( $api_url, 'POST',$ret);
				
				$this->assign('pay',$pay);
				$this->display('paypay');
                break;
			case 'apipay':
				Vendor('apipay.apipay');
				$this->aliziConfig['notify_url'] = $this->aliziHost."Api/apipay.php";
				$this->aliziConfig['return_url'] = $this->aliziHost."Api/apipayResult.php";
				$apipay = new apipay($this->aliziConfig);
				$pay = $apipay->curl_post($order);
				
				$payment = explode('-',$order['payment']);

				if($payment[1]=='928'){
					//支付宝
					$payArray = json_decode($pay,true);
					if(isMobile() && isWeixin()==false){ header("location:".$payArray['payurl']);exit; }
					
					$this->assign('payment','alipay');
					$this->assign('pay',$payArray);
					$this->assign('qrcode',$payArray['imgurl']);
					$this->display('apipay');
				}else{
					//print_r($pay);
					preg_match('/<img.*?src=[\"|\']?(.*?)[\"|\']?>/i', $pay,$images);

					$this->assign('payment','wxpay');
					$this->assign('qrcode',$images[1]);
					$this->display('apipay');
					exit;
				}
                break;
			case 'xorpay':
				Vendor('xorpay.xorpay');
				$this->aliziConfig['notify_url'] = $this->aliziHost."Api/xorpay.php";
				$this->aliziConfig['return_url'] = $this->aliziHost."index.php?m=Order&a=result&order_no=".$order['order_no'];
				$xorpay = new xorpay($this->aliziConfig);
				
				
				$payment = explode('-',$order['payment']);
				
				switch($payment[1]){
					case 'cashier_jsapi':
						//微信收银台支付
						if(isWeixin()==true){
							$result = $xorpay->cashier($order,true);
							
							$this->assign('result',$result);
							$this->display('xorpayCashier');
						}else{
							$url = $this->host.U('Order/pay',array('order_no'=>$order['order_no']));
							$this->assign('code_url',urlencode($url));
							$this->display('xorpayQrcode');
						}
					break;
					case 'alipay':
						//支付宝
						$json = $xorpay->alipay($order);
						$result = json_decode($json,true);
						if($result['status']=='ok'){
							$this->assign('result',$result);
							$this->display('xorpayAlipay');
						}else{
							$this->error($result['info']);
						}
					break;
				}
                break;
            case 'wxpay':
                if(isWeixin()==true && in_array(2, json_decode($this->aliziConfig['wxpay_type'],true))){
                    $this->redirect('Order/payWxPayJsApi',array('order_id'=>$order['id']));exit;
                }elseif(isWeixin()==false && isMobile()==true && in_array(2, json_decode($this->aliziConfig['wxpay_type'],true)) ){
					$this->redirect('Order/payWxPayWap',array('order_id'=>$order['id']));exit;
				}else{
                    $result = R('Api/payWxpay',array('data'=>$order));
                    if($result['return_code']=='FAIL'){
                        $this->error($result['return_msg']);
                    }else{
                        $this->assign('result',$result);
                        $this->display('payWxpay');
                    }
                }
                break;
            case 'qrcode'://二维码
                $qrcode = R('Api/payQrcode',array('data'=>$order));
                $this->assign('qrcode',$qrcode);

                $this->display('payQrcode');
                break;
			case 'ispay':
				Vendor('ispay.lib.Ispay#class');
                $self_url = getSelfUrl();
				
				$ISPAY = new ispayService($this->aliziConfig['ispay_id'],$this->aliziConfig['ispay_key']);
				$request =array(
					'payId'     => $this->aliziConfig['ispay_id'],
					'payChannel' => $subpayment[1],
					'Subject'   => mb_substr($order['item_name'],0,30,'UTF-8'),
					'Money'        => floatval($order['total_price'])*100,
					'orderNumber'       => $order['order_no'],
					'attachData'       => 'Alizi',
				);
				$request['Notify_url'] = $self_url.'Api/ispayCallback.php';
				$request['Return_url'] = $self_url.'Api/ispayResult.php';
				$request['Sign'] = $ISPAY->Sign($request);
				//$ret = $ISPAY->Order($request);
			
				$this->assign('result',$request);
				$this->display('ispay');
                break;
			case 'vivcms': 
				$this->redirect('Order/payVivcms',array('order_id'=>$order['id']));exit;
                break;
			case 'paysera':
				Vendor('paysera.WebToPay');
                $self_url = getSelfUrl();
				
				$request = WebToPay::redirectToPayment(array(
					'projectid'     => $this->aliziConfig['paysera_projectid'],
					'sign_password' => $this->aliziConfig['paysera_password'],
					'orderid'       => $order['order_no'],
					'p_firstname'   => $order['name'],
					'p_email'       => $order['mail'],
					'amount'        => floatval($order['total_price'])*100,
					'currency'      => $this->aliziConfig['paysera_currency'],
					'country'       => $this->aliziConfig['paysera_country'],
					'lang'       => $this->aliziConfig['paysera_lang'],
					'accepturl'     => $self_url."/index.php?m=Order&a=result&order_no={$order['order_no']}",
					'cancelurl'     => $order['url'],
					'callbackurl'   => $self_url.'/Api/payseraCallback.php',
					'test'          => $this->aliziConfig['paysera_test'],
				));
                break;
            case '7':
                $this->yunpay($order);
                break;
            case '8':
                $this->display('alipay');
                $link = M('Item')->where(array('id'=>$order['item_id']))->getField('link_pay_url');
                header('location:'.$link);exit;
                break;
			case '9':
				Vendor('aiyangPay.aiyangPay');
				$aiyang = new aiyangPay($this->aliziConfig['ay_partner'],$this->aliziConfig['ay_key']);
				$data = array(
					'type'=>1007,
					'total_price'=>$order['total_price'],
					'out_trade_no'=>$order['order_no'].'-'.time(),
					'notify_url'=>$this->aliziHost.'Api/aiyangpay.php',
					'return_url'=>$this->aliziHost.'Api/aiyangpayCallbak.php',
					'attach'=>$order['order_no'],
				);
				$payURl = $aiyang->getPayUrl($data);
				header('location:'.$payURl);exit;
			break;
			case 'creaditcard':
				$this->gleepay($order);
			break;
            default:$this->result($order);
        }
    }
	public function payAlipay($order){
		$this->assign('order',$order);
		$this->display('payAlipay');
        //R('Api/payAlipay',array('data'=>$order));
	}
	public function gleepay($order){
		Vendor('gleepay.gleepay');
		$Pay = new gleepay($this->aliziConfig);
		
		$card = json_decode($order['creditcard'],true);
		
		// Other Information
		$os = '';
		$brower = $Pay->getBrowser();
		$browerLang = $Pay->getBrowserLang();
		$ip = get_client_ip();
		$acceptLang = $_SERVER ['HTTP_ACCEPT_LANGUAGE'];
		$userAgent = $_SERVER ['HTTP_USER_AGENT'];
		$webSite = $_SERVER ['HTTP_HOST'];
		// set cookie
		$newCookie = 'billCountry=' . $order['country'];
		$newCookie .= '&email=' . $order['mail'];
		$newCookie .= '&timeZone=' . C('DEFAULT_TIMEZONE');
		$newCookie .= '&orderNo=' . $order ['order_no'];
		$newCookie .= '&lang=' . $browerLang;
		$newCookie .= '&ip=' . $ip;
		$oldCookie = '';
		if (isset ( $_COOKIE ['CARD_PAY_COOKIE'] )) {
			$oldCookie = $_COOKIE ['CARD_PAY_COOKIE'];
			$oldCookie = strlen ( $oldCookie ) > 1000 ? substr ( $oldCookie, 0, 1000 ) : $oldCookie;
		}
		$newCookie = $newCookie . (empty ( $oldCookie ) ? "" : '$$' . $oldCookie);
		$newCookie = strlen ( $newCookie ) > 1000 ? substr ( $newCookie, 0, 1000 ) : $newCookie;
		setcookie ( "CARD_PAY_COOKIE", $newCookie, time () + 21474836 );
		
		// Parameter combination encryption
		$signSrc = $this->aliziConfig['creditcard_mid'] . $this->aliziConfig['creditcard_gateway'] . $order['order_no'] . L('currency') . $order['total_price'] . $card['num'] . $card ['year'] . $card ['month'] . $card ['cvv'] .$this->aliziConfig['creditcard_key'];
		$signInfo = hash ( 'sha256', $signSrc );
		$goodsInfo = $order['item_name'].'#,#'.$order['item_id'].'#,#'.$order['item_price'].'#,#'.$order['quantity'];
		$country = L('default_country');
		
		$data = array (
			'merNo' => $this->aliziConfig['creditcard_mid'],
			'gatewayNo' => $this->aliziConfig['creditcard_gateway'],
			'orderNo' => $order ['order_no'],
			'orderAmount' => $order ['total_price'],
			'orderCurrency' => L('currency'),
			'shipFee' => $order ['shipping_pricee2'],
			'firstName' => mb_substr($order['name'],0,1,'utf-8'),
			'lastName' => mb_substr($order['name'],1,20,'utf-8'),
			'email' => $order ['mail'],
			'phone' => $order ['mobile'],
			'zip' => $order ['zcode'],
			'address' => $order ['address'],
			'city' => $order ['city'],
			'state' => $order ['province'],
			'country' => $country,
			'shipFirstName' => mb_substr($order['name'],0,1,'utf-8'),
			'shipLastName' => mb_substr($order['name'],1,20,'utf-8'),
			'shipPhone' => $order ['mobile'],
			'shipEmail' => $order ['mail'],
			'shipCountry' => $country,
			'shipState' => $order ['province'],
			'shipCity' => $order ['city'],
			'shipAddress' => $order ['address'],
			'shipZip' => $order ['zcode'],
			
			'returnUrl' => $this->aliziHost.'Api/gleepayCallbak.php',
			'notifyUrl' => $this->aliziHost.'Api/gleepay.php',
			'uniqueId' => session_id(),
		
			'signInfo' => $signInfo,
			'cardNo' => $card['num'],
			'cardSecurityCode' => $card ['cvv'],
			'cardExpireMonth' => $card ['month'],
			'cardExpireYear' => $card ['year'],
			'issuingBank' => '',
			'webSite' => $webSite,
			'ip' => $ip,
			'brower' => $brower,
			'browerLang' => $browerLang,
			'os' => $os,
			'timeZone' => C('DEFAULT_TIMEZONE'),
			'resolution' => '',
			'isCopyCard' => 0,
			'goodsInfo' => $Pay->string_replace ( $goodsInfo ),
			'oldCookie' => $Pay->string_replace ( $oldCookie ),
			'newCookie' => $Pay->string_replace ( $newCookie ),
			'acceptLang' => $Pay->string_replace ( $acceptLang ),
			'userAgent' => $Pay->string_replace ( $userAgent ),
			'remark' => $Pay->string_replace ( $order ['remark'] ) 
		);
		
		$result = $Pay->payment_submit ( $this->aliziConfig['creditcard_url'], http_build_query ( $data, '', '&' ) );
		$payXml = $Pay->xml_parser( $result );
		
		
		$redirectUrl = $order['url'];
        $options = json_decode($this->aliziConfig['order_options'],true);
        if(in_array($order['order_page'],array('single','detail'))){
            $template = M('ItemTemplate')->where(array('id'=>$order['item_id']))->field('redirect_uri,options')->find();
            if($template){
                if($template['redirect_uri']=='1' && !empty($order['referer'])){
                    $redirectUrl = $order['referer'];
                }elseif($template['redirect_uri']!='1' && !empty($template['redirect_uri'])){
                    $redirectUrl = $template['redirect_uri'];
                }
                $options = json_decode($template['options'],true);
            }
        }
        $this->assign('redirectUrl',$redirectUrl);
		
		//print_r($payXml);
		$this->assign('pay',$payXml);
		$this->assign('order',$order);
		$this->display('gleepay');
	}
    public function yunpay($order){

        $yun_config['partner'] = $this->aliziConfig['yunpay_pid'];//合作身份者id
        $yun_config['key'] = $this->aliziConfig['yunpay_key'];//安全检验码
        $seller_email = $this->aliziConfig['yunpay_email'];//云会员账户（邮箱）
        $GLOBALS['i2ekeys']=$yun_config['key'];
        Vendor('yunpay.lib.yun_md5#function');

        $out_trade_no = $order['order_no'];//商户网站订单系统中唯一订单号，必填
        $subject = $order['item_name'];//订单名称，必填
        $total_fee = $order['total_price'];//付款金额,必填 需为整数
        $body = $order['item_params'];//订单描述

        $nourl = $this->aliziHost.'Api/yunpay.php'; //服务器异步通知页面路径,需http://格式的完整路径,不能加参数
        $reurl = $this->aliziHost.'Api/yunpayCallbak.php';//页面跳转同步通知页面路径,需http://格式的完整路径，不能加?id=123这类自定义参数

        $orurl = '';
        //商品展示地址需http://格式的完整路径，不能加?id=123这类自定义参数，如原网站带有 参数请彩用伪静态或短网址解决
        $orimg = '';
        //商品形象图片地址 需http://格式的完整路径，必须为图片完整地址

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "partner" => trim($yun_config['partner']),
            "seller_email"	=> $seller_email,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "body"	=> $body,
            "nourl"	=> $nourl,
            "reurl"	=> $reurl,
            "orurl"	=> $orurl,
            "orimg"	=> $orimg
        );
        //dump($parameter);exit;
        $html_text = i2e($parameter, "支付进行中...");
        echo $html_text;
    }
    public function payWxPayJsApi(){

        $order_id = intval($_GET['order_id']);
        $order = M('Order')->where(array('id'=>$order_id))->find();
        $redirectUrl = $this->aliziHost."index.php?m=Order&a=payWxPayJsApi&order_id={$order_id}";
        //$redirectUrl = $this->aliziHost."Api/wxPayJsApi.php?order_id={$order_id}";


        Vendor('wxPay.WxPay#JsApiPay');
        WxPayConfig::setConfig($this->aliziConfig);
        $JsApiPay = new JsApiPay();
        $openid = $JsApiPay->GetOpenid($redirectUrl);
        $total_price = $order['total_price']*100;//价格，单位为分
		$title = $order['item_params']?$order['item_params']:$order['item_name'];
		
        $input = new WxPayUnifiedOrder();
        $input->SetOpenid($openid);
        $input->SetBody($title);
        $input->SetOut_trade_no($order['order_no'].'-'.time());//$order['order_no']
        $input->SetTotal_fee($total_price);
        $input->SetProduct_id($order['item_id']);
        $input->SetAttach(L('aliziSystem'));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag('Alizi'.$order['order_no']);
        $input->SetNotify_url($this->aliziHost."Api/wxPay.php");
        $input->SetTrade_type("JSAPI");

        $param = WxPayApi::unifiedOrder($input);

        if(empty($param)){
            $this->error('error');exit;
        }
        if($param['result_code']=='FAIL'){
            $this->error($param['err_code_des']);exit;
        }
        $wxPayRequest = $param?$JsApiPay->GetJsApiParameters($param):array();
        $this->assign('wxPayRequest',$wxPayRequest);
        $this->assign('order',$order);
        $this->assign('config',$this->aliziConfig);
        $this->display('Order:payWxPayJsApi');

    }
	 public function payWxPayWap(){

        $order_id = intval($_GET['order_id']);
        $order = M('Order')->where(array('id'=>$order_id))->find();
        $redirectUrl = $this->aliziHost."Api/payWxPayH5.php?order_id={$order_id}";


        Vendor('wxPay.WxPay#JsApiPay');
        WxPayConfig::setConfig($this->aliziConfig);
        $total_price = $order['total_price']*100;//价格，单位为分
		$title = $order['item_params']?$order['item_params']:$order['item_name'];
		
        $input = new WxPayUnifiedOrder();
        $input->SetBody($title);
        $input->SetOut_trade_no($order['order_no'].'-'.time());//$order['order_no']
        $input->SetTotal_fee($total_price);
        $input->SetProduct_id($order['item_id']);
        $input->SetAttach(L('aliziSystem'));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 300));
        $input->SetGoods_tag('Alizi'.$order['order_no']);
        $input->SetNotify_url($this->aliziHost."Api/wxPay.php");
        $input->SetTrade_type("MWEB");
        $input->SetSpbill_create_ip(get_client_ip());

        $param = WxPayApi::unifiedOrder($input);
		//$mweb_url = $param['mweb_url'].'&redirect_url='.urlencode($this->aliziHost.'?m=Order&a=result&order_no=17091957052298');
        if(strtoupper($param['return_code'])=='FAIL'){
        	$this->error($param['return_msg'],U('Order/result',array('order_no'=>$order['order_no'])));exit;
        }else{
			//header("location:".$param['mweb_url']);
			$this->assign('order',$order);
			$this->assign('pay',$param);
			$this->display('payWxPayWap');
        }
    }

	public function payVivcms($order_id){
        $order = M('Order')->where(array('id'=>$order_id))->find();
        $redirectUrl = $this->aliziHost."index.php?m=Order&a=payVivcms&order_id={$order_id}";


        Vendor('wxPay.WxPay#JsApiPay');
        WxPayConfig::setConfig($this->aliziConfig);
        $JsApiPay = new JsApiPay();
        $openid = $JsApiPay->GetOpenid($redirectUrl);
		
		//print_r($openid);
		$url = "http://pay.vivcms.com:8080/pay/wechat";
		
		$data = array(
			'merchantNo'=>$this->aliziConfig['vivcms_id'],//'S20190816005569',//'S20190725003632',
			'appId'=>$this->aliziConfig['wxpay_appid'],
			'openId'=>$openid,
			'tranFlow'=>$order['order_no'],
			'amount'=>$order['total_price']*100,
			
			'notifyUrl'=>$this->aliziHost."Api/vivcms.php",
			'goodsName'=>$order['item_name'],
			'clientIp'=>get_client_ip(),
			'remark'=>$order['name'].":".$order['mobile'],
		); 
		$json = http($url,'post',$data); 
			
		$this->assign('wxPayRequest',$json);
		$this->assign('result',json_decode($json,true));
		$this->assign('order',$order);
        $this->assign('config',$this->aliziConfig);
		$this->display('payWxPayJsApi');
	}
    public function result($order=array()){
		
		$order_no = strFilter($_GET['order_no']);
        if(empty($order) && isset($_GET['order_no'])){
            $order = M('Order')->where(array('order_no'=>$order_no))->find();
        }

        //优惠券支付成功提示页面
        if(isset($_GET['order_no']) && strstr($order_no,'_')){
            $this->display('UserWap/paySuccess');exit;
        }

        $redirectUrl = $order['url'];
        $options = json_decode($this->aliziConfig['order_options'],true);

        if(in_array($order['order_page'],array('single','detail'))){
            $template = M('ItemTemplate')->where(array('id'=>$order['item_id']))->field('redirect_uri,options')->find();
            if($template){
                if($template['redirect_uri']=='1' && !empty($order['referer'])){
                    $redirectUrl = $order['referer'];
                }elseif($template['redirect_uri']!='1' && !empty($template['redirect_uri'])){
                    $redirectUrl = $template['redirect_uri'];
                }
                $options = json_decode($template['options'],true);
            }
        }

        foreach($options as $k=>$opt){ if(in_array($opt, array('salenum'))) unset($options[$k]); }
        $this->assign('options',$options);
        $this->assign('order',$order);
        $this->assign('redirectUrl',$redirectUrl);
        $this->display('result');
    }
    private function alipayInWx($data){
        if(isWeixin()==true){
            $this->assign('info',$data);
            $this->display('Order:payInWx');exit;
        }
    }
    //订单轮询
    public function orderQuery($order_no){
		$order_no = strFilter($order_no);
        if(strstr($order_no,'_')){
            $no = explode('_',$order_no);
            $status = M('User')->where(array('id'=>$no[0]))->getField('status');
            $this->ajaxReturn(null,null,(int)$status);
        }
        $status = M('Order')->where(array('order_no'=>$order_no))->getField('status');
        $this->ajaxReturn(null,null,(int)$status);
    }

    public function getComments(){
        if(IS_POST){
            $Model = M('Comments');
            $item_id = intval($_POST['item_id']);
            $currentPage = intval($_POST['currentPage']);
            $page = intval($_POST['page']);
            $order = isset($_POST['order'])?trim($_POST['order']):'id DESC';

            $where = array('item_id'=>$item_id,'status'=>1);
            $total = $Model->where($where)->count();
            $list = $Model->where($where)->limit($currentPage*$page,$page)->order($order)->select();
			//echo $Model->_sql();
            $count = count($list);
            $data = array(
                'list'=>$list,
                'currentPage'=>$currentPage+1,
                'leftPage'=>$total-$count-$currentPage*$page,
            );
            $this->ajaxReturn($data,1,1);
        }
    }
    public function comment(){
        if(IS_POST){
            $item_id = intval($_POST['item_id']);
            $name = strip_tags(trim($_POST['name']));
            $mobile = strip_tags(trim($_POST['mobile']));
            $content = strip_tags(trim($_POST['content']));
            $start = strip_tags(trim($_POST['start']));
            $comment_file = trim($_POST['comment_file']);
		
            if(empty($name) || mb_strlen($name,'utf-8')>5){  $this->ajaxReturn('name',lang('invalid_realname'),0);  }
            if(empty($mobile)){  $this->ajaxReturn('mobile',lang('invalid_mobile'),0);  }
            if(empty($content)){  $this->ajaxReturn('content',lang('content_notempty'),0); }
			
			
			$where = array('item_id'=>$item_id,'mobile'=>$mobile);
			$order = M('Order')->where(array_merge($where,array('status'=>array('EGT',1))))->field('id,province')->find();
			if(empty($order)){
				$this->ajaxReturn('mobile',lang('commentErrorMessage'),0);
			}
			
			if(empty($order['province'])){
				$str = http("http://ip.taobao.com/service/getIpInfo.php?ip=".get_client_ip());
				$json = json_decode($str,true);
				$address = $json['data']['region'];
			}else{
				$address = $order['province'];
			}
			
			$Comments = M('Comments');
			$count   = $Comments->where($where)->count();
			if($count>=5){
				$this->ajaxReturn('mobile',lang('TooMuchComment'),0);
			}
			
			if(!empty($comment_file)){
				$comment_file = imageUrl($comment_file);
				$content .= "<img src='{$comment_file}' />";
			}
            $data = array(
                'item_id'=>$item_id,
                'status'=>0,
                'mobile'=>$mobile,
                'region'=>$address,
                'name'=>$name,
                'content'=>$content,
                'start'=>$start?$start:'diamond-5',
                'add_time'=>date('Y-m-d'),
            );
			
            $Comments->add($data);
            $this->ajaxReturn(1,lang('submit_success'),1);
        }
    }
    public function getCode(){
        $item_id = intval($_POST['item_id']);
        $mobile = strFilter($_POST['mobile']);
        $verify = $_POST['verify'];
        $page = $_POST['page'];

        if(isMobileNum($mobile)==false){  $this->error(lang('pleaseInputMobile'));}
        if(in_array($page,array('single','detail'))){
            $options = M('ItemTemplate')->where(array('id'=>$item_id))->getField('options');
        }else{
            $options = $this->aliziConfig['order_options'];
        }
        $optionArr = json_decode($options,true);

        if(in_array('verify',$optionArr)){
            if(empty($verify)){ $this->error(lang('pleaseInputCode'));}
            if(md5($verify)!=$_SESSION['verify']){  $this->error(lang('invalid_verify'));}
        }
        $code = randCode(4);

		$rs = parent::send_sms($mobile,sprintf(L('smsCodeContent'),$code));
	
        if(strtolower($rs['status'])=='1'){
            $data = array(
                'item_id'=>$item_id,
                'mobile'=>$mobile,
                'code'=>$code,
                'type'=>1,
                'status'=>0,
                'add_time'=>time(),
            );
            $flag = M('Code')->add($data);
			$this->success(lang('send_success'));
        }else{
			$this->error(lang('send_failure_colon'));
        }
    }
	
	//物流跟踪
	public function traceExpress(){
		$com = trim($_GET['com']);
		$num = trim($_GET['num']);
		$order_id = intval($_GET['order_id']);
		$order_no = trim($_GET['order_no']);
		$express = C('DELIVERY');
		$json = traceExpress($com,$num,$order_no); 
		$ret  = json_decode($json,true);
		
		$state = array('0'=>'无轨迹','1'=>'已揽收','2'=>'在途中','3'=>'签收','4'=>'问题件',);
		
		
		//更新状态为签收
		if($ret['State']=='3'){
			$data = array(
				'order_id'=>$order_id,
				'status'=>4,
				'user_id'=>(int)$this->uid,
				'remark'=>'接口签收',
			);
			$data['sign'] = createSign($data,C('ALIZI_KEY'));
			include("./Home/Lib/Action/ApiAction.class.php");
			R('Api/aliziUpdateStatus',array('data'=>$data));
		}
		
		$this->assign('list',$ret);
		$this->assign('com',$express[$com]);
		$this->assign('num',$num);
		$this->assign('traceState',$state[$ret['State']]);
		$this->display('traceExpress',false);
	}
	 
    public function wx(){
		if($this->aliziConfig['weixin_status']){
			$url = urldecode(trim($_GET['url']));
			Vendor('wxShare.jssdk');
			$jssdk = new JSSDK($this->aliziConfig['weixin_appid'], $this->aliziConfig['weixin_appsecret']);
			$signPackage = $jssdk->GetSignPackage($url);
			echo json_encode($signPackage);
		}
    }
	
	
	function getRegion(){
		$request = $_REQUEST;
		$pid = intval($request['pid']);
		$name = strFilter($request['name']);
		$type = strFilter($request['type']);
		$target = trim($request['target']);
		
		$Region = M('Region');
		if(empty($target)){
			$where = "name='{$name}' AND item='{$type}'";
			$info = $Region->where($where)->getField('info');
			echo $info;
		}else{
			$map = "pid={$pid} AND item='{$type}'";
			$list = $Region->field('id,name,info')->where($map)->select();
			if($list){
				foreach($list as &$li){ $li['info']=json_decode($li['info'],true);}
				$ret = array('status'=>1,'data'=>$list);
			}else{
				$ret = array('status'=>0,'data'=>'');
			}
			echo json_encode($ret);
		}
	}

}