<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
set_time_limit(300);
defined('THINK_PATH') OR exit();
class ApiAction extends Action{

    private $aliziConfig = array();
    public function _initialize(){
        $this->aliziConfig = $this->aliziConfig();
    }
    private function auth($data){
        $sign = createSign($data,C('ALIZI_KEY'));
        if($sign!=$data['sign']){
            return array('status'=>0,'message'=>lang('illegal_sign'));
        }else{
            return array('status'=>1,'message'=>lang('success'));
        }
    }
    public function aliziBooking(array $data){
		
        session_commit();
        $sign = $this->auth($data);
        if($sign['status']==0) return $sign;
        foreach($data as $k=>$v){ $data[$k] = is_string($v)?strip_tags($v):$v; }
        $data['item_id']  = (int)$data['item_id'];
        $data['quantity'] = $data['quantity']<$data['min_num']?$data['min_num']:$data['quantity'];
        $data['quantity'] = $data['quantity']>$data['max_num']?$data['max_num']:$data['quantity'];
        $data['quantity'] = empty($data['quantity'])?1:intval($data['quantity']);
        $data['payment'] = empty($data['payment'])?'payOnDelivery':trim($data['payment']);

        $itemMap = array('sn'=>trim($_POST['sn']));
        $item = getCache('Item',$itemMap,true);
        $data['quantity'] = $data['payment']=='qrcode' && $item['qrcode_pay']==1?1:$data['quantity'];
        if(empty($item)  || $item['is_delete']==1 || $item['status']==2) return array('status'=>0,'message'=>lang('empty_item'));
        //$item_params=json_decode($item['params'],true);
		$item_params = M('ItemParams')->where(array('item_id'=>$data['item_id']))->order('sort_order asc,id asc')->select();

		$selected_params = array();
        if($item_params){
			$params_name = 	$item['params_name']?$item['params_name']:lang('package');
            if($item['params_type']=='group'){
				$is_choose = false; 
				$total_price = 0;
				$total_quantity =0;
				foreach($_POST['params'] as $id=>$params){
					if($params['num']>0){
						$is_choose=true;
						$total_price += floatval($params['price'])*$params['num'];
						$data['item_params'][] = $params['title'].'X'.$params['num'];
						$total_quantity +=$params['num'];
					}
				}
			
				$data['quantity'] = $total_quantity;
				if($is_choose == false){
					return array('status'=>0,'message'=>lang('pleaseSelect').$params_name);
				} 
			}else{
				$paramsArray = array();
				foreach($item_params as $params){ 
					$paramsArray[] = $params['title']; 
				}
				
				if(empty($data['item_params'])) return array('status'=>0,'message'=>lang('pleaseSelect').$params_name);
				foreach($data['item_params'] as $param){ 
					if(!in_array($param,$paramsArray)){
						return array('status'=>0,'message'=>lang('pleaseSelect').$params_name);
					}
				}
			}
        }
		$data['item_params'] = empty($data['item_params'])?'':implode("#",$data['item_params']);
		 
		
		$extends = json_decode($item['extends'],true);		
        if(!empty($extends)){
		
			$checkExtends = array();
			if($item['params_type']=='checkbox'){
				$data_item_params = explode('#',$data['item_params']);
				for($i=0;$i<count($item_params);$i++){
					if(in_array($item_params[$i]['title'],$data_item_params)){
						$checkExtends[] = $i+1;
					}
				}
			} 
	
            foreach($extends as $ext){
                $key = $ext['title'];
				
				preg_match("/^(\d+)\#/i",$key,$match);
				if(!empty($checkExtends) && $match){
					if(in_array($match[1],$checkExtends)){
						$key = preg_replace("/\d\#/i","",$key);
					}else{
						continue;
					}
				}else{
					if(strstr($key,'#')){
						if(!empty($data['item_index']) && strstr($key,$data['item_index'].'#')){
							$key = preg_replace("/\d\#/i","",$key);
						}else{
							continue;
						}
					}
				}
				
				
				
				if(empty($data['extends'][$key])){
					if(in_array($ext['type'],array('radio','checkbox','select'))){
						return array('status'=>0,'message'=>lang('pleaseSelect_['.$key.']'));
					}else{
						return array('status'=>0,'message'=>lang($key.'_notEmpty'));
					}
				}
            }
			$data['item_extends'] =  $data['extends'];
        }
		
		

        if(!empty($this->aliziConfig['item_quantity']) && $data['quantity']>$item['quantity']){
            return array('status'=>0,'message'=>lang('quantityNotEnough'));
        }

        $check = $this->aliziCheck($data,$extends);
        if($check['status']==0) return $check;
        $safe = $this->aliziSafe( $data['item_id'],$data['mobile']);
        if($safe['status']==0) return $safe;
        $same = $this->aliziSameOrderCheck( $data);
        if($same['status']==0) return $same;

        $Order = M('Order');
        $price = $this->getAliziPrice($data);
		
        if($alizi=$Order->create($data)){
			if(!empty($item['buy_num']) && !empty($item['buy_num_decrease'])){
				$buy_num = explode(',',$item['buy_num']);
				$buy_num_decrease = explode(',',$item['buy_num_decrease']);
				if(count($buy_num)>0 && count($buy_num_decrease)>0){
					$decrease = 0;
					$len = count($buy_num);
					if($len>0 && $data['quantity']>=$buy_num[0]){
						$n=0;
						for($i = 0; $i < $len; $i++){
							$j = $len-1;
							if(intval($buy_num[$j])<=$data['quantity']){
								$n = $j;break;
							}else if(intval($buy_num[$i])>$data['quantity'] ){
								$n = $i-1;break;
							}
						}	
						$decrease = $buy_num_decrease[$n];
					}
					$price['total_price'] -= floatval($decrease);
				}
			}
			
            $code = date('ym').randCode(6);
			
            //$total_price = isset($total_price)?$total_price:floatval($price['total_price']);
            $total_price = $price['total_price'];

            $orderData = array(
                'status'=>0,
                'item_sn'=>$item['sn'],
                'item_name'=>$item['name'],
                'item_params'=>$data['item_params'],
                'item_extends'=>json_encode($data['item_extends']),
                'item_price'=>$item['price'],
                'order_price'=>floatval($price['order_price']),
                'shipping_price'=>floatval($price['shipping_price']),
                'total_price'=>$total_price,
                'device'=>isMobile()?2:1,
                'add_time'=>$_SERVER['REQUEST_TIME'],
                'order_no'=>$code,
                'add_ip'=>get_client_ip(),
            );
			
			//优惠券减免
            if(!empty($data['coupon_value'])){
                $orderData['total_price'] -= $data['coupon_value'];
				$orderData['total_price'] = $orderData['total_price']>=0?$orderData['total_price']:0;
                $orderData['coupon'] = $data['coupon'];
            }

            $orderData = array_merge($alizi,$orderData);
            $order_id = $Order->add($orderData);
			//echo $Order->_sql();exit;

            if($order_id){
			
				if(0==$this->aliziConfig['agent_settlement']){
					$commission = array(
						'user_id'=>$alizi['user_id'],
						'item_id'=>$alizi['item_id'],
						'order_id'=>$order_id,
						'order_status'=>0,
						'total_price'=>$orderData['total_price'],
					);
					$this->commission($commission);
				}
				
				//优惠券减免
				if(!empty($data['coupon_value'])){
					$couponData = array('is_used'=>1,'used_time'=>time(),'used_user'=>$order_id);
					M('Coupon')->where(array('code'=>$data['coupon']))->save($couponData);
				}
				
                if($this->aliziConfig['item_quantity']=='1'){
                    $this->decQuantity($data['item_id'],$data['quantity']);
                }
                if(intval($item['salenum'])>0)M('Item')->where($itemMap)->setInc('salenum',$data['quantity']);
                
                if($this->aliziConfig['record_order']==1){ cookie('order',$orderData,array('expire'=>2592000)); }
                unset($_SESSION['verify']);
                $this->aliziOrderLog($order_id,0,$data['remark']);
                $orderData['order_id'] = $order_id;
                return array('status'=>1,'message'=>lang('success'),'data'=>$orderData);
            }else{
                return array('status'=>0,'message'=>lang('error_colon_01'));
            }
        }else{
            return array('status'=>0,'message'=>lang('error'));
        }
    }

    public function aliziUpdateStatus(array $data){
        $sign = $this->auth($data);
        if($sign['status']==0) return $sign;
        $Model = M('Order');
        $order_id = (int)$data['order_id'];
        $status = (int)$data['status'];
        $user_id = isset($data['user_id'])?(int)$data['user_id']:0;
        $remark = strip_tags($data['remark']);
        $order = $Model->where(array('id'=>$order_id))->find();


        if($order && $status!=$order['status']){
            $update = array(
                'id'=>$order_id,
                'status'=>$status,
                'update_time'=>$_SERVER['REQUEST_TIME'],
            );
			if($status==1){ 
				$update['is_pay']=1; 
				$update['pay_time']=time(); 
				$update['pay_sn']=$data['pay_sn']; 
			}
            if($status==3){
                $update['delivery_name']=$data['delivery_name'];
                $update['delivery_no']=$data['delivery_no'];
            }
			if($status==9 && $order['payment']=='vivcms' && !empty($order['pay_sn'])){
				//退款
                $refund = array(
					'merchantNo'=>$this->aliziConfig['vivcms_id'],
					'tranSerialNum'=>$order['pay_sn'],
					'amount'=>$order['total_price']*100,
					'oldTranSerialNum'=>$order['order_no'],
				);
				$ret = http("http://pay.vivcms.com:8080/pay/wechat/refund",'POST',$refund);
				@file_put_contents("./Public/Cache/vivcms-refund.log",$ret."\n",FILE_APPEND);
            }
			
			if($status==$this->aliziConfig['agent_settlement']){
				$commission = array(
					'user_id'=>$order['user_id'],
					'item_id'=>$order['item_id'],
					'order_id'=>$order['id'],
					'total_price'=>$order['total_price'],
					'order_status'=>$status,
				);
				$this->commission($commission);
			}
			
            $flag = $Model->save($update);
            if($flag){
                $this->aliziOrderLog($order_id,$status,$remark,$user_id);

                if(($this->aliziConfig['item_quantity']-1)==$status){
                    $this->decQuantity($order['item_id'],$order['quantity']);
                }

                //自动发货
                if($data['status']==1 && $order['status']==0){
                    $item = M('Item')->where('id='.$order['item_id'])->field('id,is_auto_send,send_content')->find();
                    if(!empty($item['is_auto_send'])){
                        $data['status'] = 3;
                        $data['remark'] = $item['send_content'];
                        $data['sign'] = createSign($data,C('ALIZI_KEY'));
                        $this->aliziUpdateStatus($data);
                    }
                }

                return array('status'=>1,'message'=>lang('success'));
            }
        }else{
            return array('status'=>0,'message'=>lang('error'));
        }
    }
	
	public function getAliziPayment($sn,$payment_id=''){
		$item = getCache('Item',array('sn'=>$sn));
        $payment = C('PAYMENT');
		$aliziPayment = array();
		foreach($payment as $key=>$pay){
			if($key=='qrcode' && in_array($item['qrcode_pay'],array(1,2))){
				$aliziPayment[$key] = $pay;
				$aliziPayment[$key]['info'] = preg_replace('/\r\n/', '',nl2br($item['qrcode_pay_info']));
			}else if($this->aliziConfig['payment_global']=='1' && $this->aliziConfig[$key.'_status']!='1') {
				continue;
			}else{
				$aliziPayment[$key] = $pay;
				$aliziPayment[$key]['info'] = preg_replace('/\r\n/', '',nl2br($this->aliziConfig[$key.'_discount_info']));
				$math = substr($pay['math'],0,1);
				$aliziPayment[$key]['math'] = empty($this->aliziConfig[$key.'_discount'])&&$math=='*'?$pay['math']:substr($pay['math'],0,1).floatval($this->aliziConfig[$key.'_discount']);
				
				switch($key){
					case 'codepay':
						$codepay_type = json_decode($this->aliziConfig['codepay_type'],true);
						if($codepay_type){
							$class = array(1=>'payment-alipay',2=>'payment-tenpay',3=>'payment-wxpay');
							foreach($codepay_type as $type){
								$num = $key.'-'.$type;
								$aliziPayment[$num] = array(
									'name'=>$aliziPayment[$key]['type'][$type],
									'info'=>$aliziPayment[$key]['info'],
									'math'=>$aliziPayment[$key]['math'],
									'classname'=>$class[$type],
								);
							}
						}
						unset($aliziPayment[$key]);
					break;
					case 'paypay':
						$paypay_type = json_decode($this->aliziConfig['paypay_type'],true);
						if($paypay_type){
							$class = array(1=>'payment-wxpay',2=>'payment-alipay');
							foreach($paypay_type as $type){
								$num = $key.'-'.$type;
								$aliziPayment[$num] = array(
									'name'=>$aliziPayment[$key]['type'][$type],
									'info'=>$aliziPayment[$key]['info'],
									'math'=>$aliziPayment[$key]['math'],
									'classname'=>$class[$type],
								);
							}
						}
						unset($aliziPayment[$key]);
					break;
					case 'ispay':
						$ispay_type = json_decode($this->aliziConfig['ispay_type'],true);
				
						if($ispay_type){
							$class = array('alipay'=>'payment-alipay','wxpay'=>'payment-wxpay','qqpay'=>'payment-tenpay','bank_pc'=>'payment-bankpay','wxgzhpay'=>'payment-wxpay');
							foreach($ispay_type as $type){
								$num = $key.'-'.$type;
								$aliziPayment[$num] = array(
									'name'=>$aliziPayment[$key]['type'][$type],
									'info'=>$aliziPayment[$key]['info'],
									'math'=>$aliziPayment[$key]['math'],
									'classname'=>$class[$type],
								);
							}
						}
						unset($aliziPayment[$key]);
					break;
					case 'apipay':
						$codepay_type = json_decode($this->aliziConfig['apipay_type'],true);
						if($codepay_type){
							$class = array(928=>'payment-alipay',927=>'payment-wxpay');
							foreach($codepay_type as $type){
								$num = $key.'-'.$type;
								$aliziPayment[$num] = array(
									'name'=>$aliziPayment[$key]['type'][$type],
									'info'=>$aliziPayment[$key]['info'],
									'math'=>$aliziPayment[$key]['math'],
									'classname'=>$class[$type],
								);
							}
						}
						unset($aliziPayment[$key]);
					break;
					case 'xorpay':
						$codepay_type = json_decode($this->aliziConfig['xorpay_type'],true);
						if($codepay_type){
							$class = array('alipay'=>'payment-alipay','jsapi'=>'payment-wxpay','cashier_jsapi'=>'payment-wxpay');
							foreach($codepay_type as $type){
								$num = $key.'-'.$type;
								$aliziPayment[$num] = array(
									'name'=>$aliziPayment[$key]['type'][$type],
									'info'=>$aliziPayment[$key]['info'],
									'math'=>$aliziPayment[$key]['math'],
									'classname'=>$class[$type],
								);
							}
						}
						unset($aliziPayment[$key]);
					break;
					case 'qrcode_pay':
						$aliziPayment[$key]['info'] = preg_replace('/\r\n/', '',nl2br($item['qrcode_pay_info']));
					break;
				}
			}
		}
		
		if($this->aliziConfig['payment_global']!='1'){
            $itemPayment = json_decode($item['payment'],true);
			
            if($itemPayment){
                foreach($aliziPayment as $k=>$v){ 
					preg_match('/([a-zA-Z0-9]+)\-(.*)/',$k,$match);
					$key = $match?$match[1]:$k;
					
					if(!in_array($key, $itemPayment)){
						//unset($aliziPayment[$key]); 
						unset($aliziPayment[$k]); 
					}
				}
            }
        }
			
				
		return $payment_id?$aliziPayment[$payment_id]:$aliziPayment;
	}
    

    public function getAliziPrice(array $data){
        $sn  = trim($data['sn']);
        $quantity = empty($data['quantity'])?1:intval($data['quantity']);
        $params  = trim($data['item_params']);
		
        $payment_id  = $data['payment'];

        $item = getCache('Item',array('sn'=>$data['sn']));
        $item_price = $item['price'];
        $item_params = M('ItemParams')->where(array('item_id'=>$data['item_id']))->order('sort_order asc,id asc')->select();
		
		$item_params_post = explode('#',$data['item_params']);
		
		//组合套餐
		if(isset($data['params'])){
			foreach($data['params'] as $param){
                $order_price += $param['price']*$param['num']; 
            }
		}else if($item_params){
			$item_price = 0;
			foreach($item_params as $param){
                if(in_array($param['title'],$item_params_post)){  
					$item_price += $param['price']; 
				}
            }
			$order_price = $quantity*$item_price;
        }else{
			$order_price = $quantity*$item_price;
		}
	
        
		
        $payment = $this->getAliziPayment($sn,$payment_id);
        $num = substr($payment['math'], 1);
        switch (substr($payment['math'], 0,1)) {
            case '+': $order_price += $num; break;
            case '*': $order_price *= $num; break;
        }
        $shipping_price = $this->getAliziShipping($item['shipping_id'],$quantity,$order_price);
 
        return array(
            'status'=>1,
            'order_price'=>$order_price,
            'shipping_price'=>$shipping_price,
            'total_price' =>$order_price+$shipping_price,
        );
    }

    public function aliziSafe($item_id,$mobile=''){
        $Model = M('Order');
        $ip = get_client_ip();
        $today = date('Y-m-d');
		
		$limit_num = M('Item')->where("id={$item_id}")->getField('limit_num');
		$count_num = $Model->where("item_id={$item_id} AND mobile='{$mobile}'")->count();
		if(!empty($limit_num) && intval($limit_num)<=intval($count_num)){
			 return array('status'=>0,'message'=>sprintf(lang('limitNum'),$limit_num));
		}

        $lastOrderTime = $Model->where("item_id={$item_id} AND status=0 AND  add_ip='{$ip}' ")->limit(1)->order('id DESC')->getField('add_time');
        if(($lastOrderTime+$this->aliziConfig['safe_order_interval'])>$_SERVER['REQUEST_TIME']) return array('status'=>0,'message'=>lang('intervalLimit'));

        $mobileCount = $Model->where("item_id={$item_id} AND mobile='{$mobile}' AND FROM_UNIXTIME(add_time,'%Y-%m-%d')='{$today}' ")->count();
        if($mobileCount>=$this->aliziConfig['safe_mobile_limit']) return array('status'=>0,'message'=>lang('mobileLimit'));

        if(!empty($this->aliziConfig['safe_ip_limit'])){
            $ipCount = $Model->where("add_ip='{$ip}' AND add_time >".($_SERVER['REQUEST_TIME']-3600))->count();
            if($ipCount>=$this->aliziConfig['safe_ip_limit']) return array('status'=>0,'message'=>lang('orderLimit'));
        }
        return array('status'=>1,'message'=>lang('success'));
    }

    private function aliziOrderLog($order_id,$status=1,$remark='',$user_id=0){
   
        $OrderLog = M('OrderLog');
        //$flag = $OrderLog->where(array('order_id'=>$order_id,'status'=>$status))->find('id');
        //if($flag) return false;
        $data = array(
            'order_id' => $order_id,
            'user_id' => $user_id,
            'status'=>$status,
            'add_time'=>$_SERVER['REQUEST_TIME'],
            'remark'=>$remark,
        );
        $rs= $OrderLog->add($data);
      
      	$this->sendSMS($order_id);
        if($status>0) $this->send($order_id,$status,$remark);//发送信息
      	return $rs;
    }
    public function send($order_id,$status=0,$remark='',$print=false){

        if(empty($this->aliziConfig['mail_send'])) return array('status'=>0);
        $status = intval($status);
        $order = M('Order')->where(array('id'=>$order_id))->find();
        if(empty($order)) return array('status'=>0);
        if($order['is_sent']==1 && $status==0) return array('status'=>0);//已发送
        $item = $item = M('Item')->where('id='.$order['item_id'])->field('id,is_auto_send,send_content')->find();
        $send_status = json_decode($this->aliziConfig['mail_send_status'],true);
        $orderStatus  = C('ORDER_STATUS');

        if(in_array($status, $send_status) || ($status==3 && $item['is_auto_send']==1)){

			
            $content = include(COMMON_PATH."alizi.mail.tpl{$status}.php");
			$file =  file_exists(COMMON_PATH."alizi.mail.tpl{$status}.php");

            //$content .= "<div style='display:block !important;font-family:MicroSoft Yahei;padding-top:80px;text-align:right;font-size:12px;color:#888;'>&copy; 2015-".date('Y')." <a href='http://www.wxrob.com' target='_blank'>时时订单系统</a> All Rights Reserved.</div>";

            //$email = in_array($status, array(2,3))?$order['mail']:explode(',', $this->aliziConfig['mail_to']);
			if($this->aliziConfig['mail_to'])$email = explode(',', $this->aliziConfig['mail_to']);
			if($order['mail']) $email[] = $order['mail'];
			
			//$email = in_array($status, array(2,3))?$order['mail']:explode(',', $this->aliziConfig['mail_to']);
            if(empty($email)) return array('status'=>0);
            $title  = str_replace(array('[AliziStatus]','[AliziTitle]','[AliziName]'), array(strip_tags($orderStatus[$status]),$order['item_name'],$order['name']), $this->aliziConfig['mail_title']);
            $send = $this->sendMail($email,$title,$content);
            if($send['status']==1 && $status==0){
                M('Order')->where(array('id'=>$order_id))->setField('is_sent',1);
            }

            if($print){
                print_r($send);
            }else{
                return $send;
            }
        }

        return array('status'=>0);
    }
    private function sendSMS($order_id){
        if(empty($this->aliziConfig['sms_send'])){
            return array('status'=>0);
        }
        $order = M('Order')->where(array('id'=>$order_id))->find();
        if(empty($order)) return array('status'=>0);

        
        $item = M('Item')->where('id='.$order['item_id'])->field('id,sms_send')->find();
        $sms = json_decode($item['sms_send'],true);
        $status=$order['status'];
        if($sms[$status]['status']==1 && !empty($sms[$status]['content'])){
            //发送内容替换
            $express = C('DELIVERY');

            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $aliziHost = $http_type.$_SERVER['HTTP_HOST'].C('ALIZI_ROOT');
            $confirmUrl = $aliziHost.(C('URL_MODEL')==2?'a/'.$order['order_no']:'Api/confirm.php?id='.$order['order_no']);
			
			$mobiles = $order['mobile'];
			if(!empty($this->aliziConfig['sms_admin_mobile'])){
				switch ($this->aliziConfig['sms_admin']) {
					case '1': $mobiles = $this->aliziConfig['sms_admin_mobile'];break;
					case '2': $mobiles .= ','.$this->aliziConfig['sms_admin_mobile'];break;
				}
			}
			
            $replace = array(
                '{[AliziTitle]}'     => $order['item_name'],
                '{[AliziParams]}'     => $order['item_params'],
                '{[AliziName]}'     => $order['name'],
                '{[AliziQuantity]}'     => $order['quantity'],
                '{[AliziPrice]}'     => $order['total_price'],
                '{[AliziExpress]}'     => $express[$order['delivery_name']],
                '{[AliziExpressNum]}'     => $order['delivery_no'],
                '{[AliziConfirmUrl]}'     => $confirmUrl,

                '#title#'     => $order['item_name'],
                '#params#'     => $order['item_params'],
                '#name#'     => $order['name'],
                '#mobile#'     => $order['mobile'],
                '#quantity#'     => $order['quantity'],
                '#price#'     => $order['total_price'],
                '#express#'     => $express[$order['delivery_name']],
                '#expressNum#'     => $order['delivery_no'],
                '#confirmUrl#'     => $confirmUrl,
                '#payUrl#'     => shortUrl($this->aliziHost.'Api/pay.php?id='.$order['order_no']),
                '#orderNum#'     => $order['order_no'],
            );
            $content = str_replace(array_keys($replace),array_values($replace),$sms[$status]['content']);
          
            $data = array(
				'method'=>'send',
				'account'=>$this->aliziConfig['sms_account'],
				'password'=>$this->aliziConfig['sms_password'],
				'mobile'=>$mobiles,
				'content'=>$content,
			);
			$rs = http(C('ALIZI_API').'/sms/','POST',$data);
			
			$ret = json_decode($rs,true);
			if($ret['status']==1 || $ret['msg']=='ok'){
				$sendData = array(
					'order_id'=>$order_id,
					'order_status'=>$order['status'],
					'mobile'=>$mobiles,
					'sent_content'=>$content,
					'sent_time'=>date('Y-m-d H:i:s'),
					'sent_status'=>1,
				);
				M('Sent')->add($sendData);
			}
        }
    }
    private function aliziCheck(&$data,$extends){
        //if(empty($data['alizi_token']) || $data['alizi_token']!=password($data['item_id'].get_client_ip())){ return array('status'=>0,'message'=>lang('invalid_token'));}
		
        $page  = trim($data['page']);
        if(in_array($page,array('index','item','wap')) && $data['order_page']!='detail'){
            $options = json_decode($this->aliziConfig['order_options'],true);
        }else{
            $template = getCache('ItemTemplate',array('id'=>(int)$data['item_id']));
            $options = $template?json_decode($template['options'],true):C('TEMPLATE_OPTIONS');
        }

        $template_options = C('TEMPLATE_OPTIONS');

		//信用卡支付验证
		if($data['payment']=='creaditcard'){
			$template_options['mail']['request'] = true;//信用卡支付必须填写邮件
			foreach($data['creditcard'] as $k=>$v){
				$v = trim($v);
				if(empty($v) || !is_numeric($v)){
					return array('status'=>0,'message'=>lang('pleaseTpyeCreditCardInfo'));
				}
				if($k=='num' && !preg_match('/^[3|4|5]{1}[0-9]{15}$/',$v)){
					return array('status'=>0,'message'=>lang('invalid_creditCardNum'));
				}
			}
			$data['creditcard'] = json_encode($data['creditcard']);
		}

        if(isset($data['region'])){
			if(is_string($data['region'])){
				list($province,$city,$area) = explode('/',$data['region']);
				$data['region'] = array( 'province'=>$province, 'city'=>$city, 'area'=>$area, );
			}	
			$data['province'] = preg_replace("/(\d+)\|/","",trim($data['region']['province']));
			$data['city'] = preg_replace("/(\d+)\|/","",trim($data['region']['city']));
			$data['area'] = preg_replace("/(\d+)\|/","",trim($data['region']['area']));
		
			if(C('DEFAULT_LANG')=='zh-cn'){
				foreach($data['region'] as $key=>&$region){
					$region = preg_replace("/(\d+)\|/","",trim($region));
					if($template_options['region']['request']==true && empty($region)){  return array('status'=>0,'message'=>lang('pleaseSelect_'.$key)); }
				}
			}else if(empty($data['region']['province'])){
				return array('status'=>0,'message'=>lang('pleaseSelect_province'));
			}
          	if(!empty($this->aliziConfig['ban_region']) && !empty($this->aliziConfig['ban_region_msg'])){
              	$region = $data['province'].$data['city'].$data['area'];
              	$ban_region = explode('|',$this->aliziConfig['ban_region']);
              	foreach($ban_region as $ban){
                	if(strstr($region,$ban)){
                    	return array('status'=>0,'message'=>$this->aliziConfig['ban_region_msg']);
                    }
                }
            }
        }
	
        if($this->aliziConfig['safe_check_mobile']==1 && !empty($data['mobile'])){
            $rs = $this->regionMatch($data['mobile'],$data['region'].$data['address']);
            if($rs==false){
                return array('status'=>0,'message'=>lang('invalid_mobile'));
            }
        }

        foreach($options as $opt){
            $options_value = is_array($data[$opt])?implode(' ',$data[$opt]):$data[$opt];
            $data[$opt] = strip_tags(trim($options_value));
            if($template_options[$opt]['request'] && empty($data[$opt])){
                return array('status'=>0,'message'=>lang($opt.'_notEmpty'));
            }
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'mobile':  if(isMobileNum($value)==false)  return array('status'=>0,'message'=>lang('invalid_'.$key)); break;
                case 'mail': if(!empty($value) && isEmail($value)==false)   return array('status'=>0,'message'=>lang('invalid_'.$key)); break;
                //case 'name':  if(mb_strlen($value,'utf8')<2)   return array('status'=>0,'message'=>lang('invalid_'.$key)); break;
                //case 'address':  if(mb_strlen($value,'utf8')<3 && !empty($value))   return array('status'=>0,'message'=>lang('invalid_'.$key)); break;
                case 'verify':  if(md5($value)!=$_SESSION['verify'])   return array('status'=>0,'message'=>lang('invalid_'.$key)); break;
                case 'coupon':
                    if(!empty($value)){
						$coupon = $this->couponCheck($value,2);
                        if($coupon['status']!='1'){
							return array('status'=>0,'message'=>$coupon['message']);
						}
                    }
                    $data['coupon_value'] = $coupon['value'];
                    break;
                case 'code':
                    $CodeModel=M('Code');
                    $map = array('mobile'=>$data['mobile'],'item_id'=>$data['item_id'],'status'=>0);
                    $code = $CodeModel->where($map)->field('code,add_time')->order('id desc')->find();
                    if(($code['add_time']+1800)<time() ||$value!=$code['code'] ) return array('status'=>0,'message'=>lang('invalidMobileCode'));
                    $CodeModel->where($map)->setField('status',1);
                    break;
            }
        }
        return array('status'=>1);
    }
	
	public function couponCheck($code,$type=2,$format='array'){
		$code = trim($code);
		if(!empty($code)){
			$coupon = M('Coupon')->where("types={$type} AND code='{$code}'")->find();
			if(empty($coupon)){
				$rs = array('status'=>0,'message'=>lang('invalid_coupon'));
			}else{
				switch($coupon['is_used']){
					case '0':$rs =  array('status'=>1,'message'=>'ok','value'=>$coupon['value']); break;
					case '1':$rs =  array('status'=>2,'message'=>lang('couponIsUsed')); break;
					default:$rs =  array('status'=>2,'message'=>lang('invalid_status')); break;
				}
			}
		}else{
			$rs =  array('status'=>0,'message'=>lang('couponisEmpty'));
		}
		if($format=='array'){
			return $rs;
		}else{
			echo json_encode($rs);
		}
	}
    private function aliziSameOrderCheck($data){
        if($this->aliziConfig['repeat_order']==1) return array('status'=>1);
        $data['item_extends'] = json_encode($data['extends']);
        $check = array('item_id','item_params','item_extends','name','mobile','region','address','quantity','payment');
        $cookie = cookie('order');
        foreach($check as $ck){
            if($data[$ck]!=$cookie[$ck]){ return array('status'=>1); }
        }
        return array('status'=>0,'message'=>lang('sameOrder'));
    }
    public function getAliziShipping($shipping_id,$quantity,$total_price){
        $cost = 0;
        if(!empty($shipping_id)){
            $shipping = getCache('Shipping',array('id'=>$shipping_id));
            if($shipping){
                if($shipping['is_free_num'] && $quantity>=$shipping['free_num']) return $cost;//达到一定数量免运费
                if($shipping['is_free_cost'] && $total_price>=$shipping['free_cost']) return $cost;//达到一定金额免运费
                if($quantity <= $shipping['less_num']){
                    $cost = $shipping['less_num_cost'];
                }else{
                    $step = ceil(($quantity-$shipping['less_num'])/$shipping['step_num']);
                    $cost = $shipping['less_num_cost']+$step*$shipping['step_num_cost'];
                }
            }
        }
        return $cost;
    }
    private function aliziConfig(){
        $config = cache('aliziConfig');
        if(empty($config)){
            $list = M('Setting')->select();
            foreach($list as $li) $config[$li['name']] = $li['value'];
            cache('aliziConfig',$config,8640000);
        }
        return $config;
    }

    public function payQrcode($data=array()){
        $item = getCache('Item',array('id'=>$data['item_id']));
        $params = json_decode($item['params'],true);
        if(empty($params)){
            $qrcode = $item['qrcode'];
        }else{
            $itemParams = explode(' - ', $data['item_params']);
            foreach($params as $k=>$v){  if($v['title']==$itemParams[0]){ $qrcode = $v['qrcode'];break;} }
        }
        return $qrcode;
    }
    public function payWxpay($data=array()){
        Vendor('wxPay.WxPay#NativePay');
        WxPayConfig::setConfig($this->aliziConfig);
        $notify = new NativePay();

        $order_no = $data['order_no'];
        $total_price = $data['total_price']*100;//价格，单位为分
        $item_id = $data['item_id'];
		$item_name = $data['item_params']?$data['item_params']:$data['item_name'];

        $input = new WxPayUnifiedOrder();
        $input->SetBody($item_name);
        $input->SetOut_trade_no($order_no);
        $input->SetTotal_fee($total_price);
        $input->SetProduct_id($item_id);
        $input->SetAttach(L('aliziSystem'));
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3000));
        $input->SetGoods_tag(L('aliziSystem'));
        $input->SetNotify_url($this->aliziHost."Api/wxPay.php");
        $input->SetTrade_type("NATIVE");
        return $notify->GetPayUrl($input);
    }

    Public function payAlipay($order_no){
		
		$orderNo = explode('_',$order_no);
		if(count($orderNo)==2 && $orderNo[0]=='deposit'){
			//货到付款之预付款，格式为：deposit-xxxxxxxx
			$is_deposit= true;
			$order_no = $orderNo[1];
		}else{
			$is_deposit= false;
		}
        $Model = D('Pay');
		$data = M('Order')->where(array('order_no'=>$order_no))->find();
		if($is_deposit==true){//货到付款之预付款
			$data['order_no'] = $order_no;
			$data['total_price'] = $data['deposit'] ;
			$data['item_name'] = lang('deposit');
		}
        $alipayType = json_decode($this->aliziConfig['alipay_type'],true);
        if((isMobile() && in_array('2', $alipayType)) || !in_array('1', $alipayType)){
            $Model->alipayWap($data,$this->aliziConfig);
        }else{
            $Model->alipay($data,$this->aliziConfig);
        }
    }
    public function sendEmail(){
        $sign = $this->auth($_POST);
        if($sign['status']==0){
            $json = array('status'=>0,'info'=>lang('illegal_sign'));
        }else{
            $json = $this->sendMail($_POST['email'],$_POST['title'],$_POST['content']);
        }
        echo json_encode($json);
    }
    public function alipayNotify(){
        D('Pay')->alipayNotify($this->aliziConfig);
    }
    public function alipayDbNotify(){
        D('Pay')->alipayDbNotify($this->aliziConfig);
    }
    public function alipayWapNotify(){
        D('Pay')->alipayWapNotify($this->aliziConfig);
    }
    public function wxPayNotify(){
        D('Pay')->wxPayNotify();
    }
    public function yunpayNotify(){
        D('Pay')->yunpayNotify($this->aliziConfig);
    }
    public function registNotify(){
        D('Pay')->registNotify($this->aliziConfig);
    }
	public function codepayNotify(){
        D('Pay')->codepayNotify($this->aliziConfig);
    }
	public function gleepayNotify(){
        D('Pay')->gleepayNotify($this->aliziConfig);
    }
	public function paypayNotify(){
        D('Pay')->paypayNotify($this->aliziConfig);
    }
	public function payseraNotify(){
        D('Pay')->payseraNotify($this->aliziConfig);
    }
	public function apipayNotify(){
        D('Pay')->apipayNotify($this->aliziConfig);
    }
	public function xorpayNotify(){
        D('Pay')->xorpayNotify($this->aliziConfig);
    }
	public function ispayNotify(){
        D('Pay')->ispayNotify($this->aliziConfig);
    }
	public function vivcmsNotify(){
        D('Pay')->vivcmsNotify($this->aliziConfig);
    }
    private function sendMail($email,$title,$content){
        $aliziConfig = S('aliziConfig');
        $email = is_array($email)?$email:explode(',', $email);

        if($aliziConfig['mail_proxy']){
            $data = array(
                'email'=>$email,
                'title'=>$title,
                'content'=>$content,
				'mail_ssl'=>$aliziConfig['mail_ssl'],
                'mail_smtp'=>$aliziConfig['mail_smtp'],
                'mail_port'=>$aliziConfig['mail_port'],
                'mail_account'=>$aliziConfig['mail_account'],
                'mail_password'=>$aliziConfig['mail_password'],
            );
            $result =  http(C('ALIZI_API').'/mail/', 'POST', $data );
            return json_decode($result,true);
        }

        import("ORG.PHPMailer.PHPMailer");
        $mail  = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        $mail->AltBody = "";
        $mail->CharSet = "UTF-8";
        $mail->SMTPSecure = $aliziConfig['mail_ssl'];

        $mail->Host = $aliziConfig['mail_smtp'];
        $mail->Port     = $aliziConfig['mail_port'];
        $mail->Username =  $aliziConfig['mail_account'];
        $mail->Password = $aliziConfig['mail_password'];
        $mail->From     =$aliziConfig['mail_account'];
        $mail->FromName = $aliziConfig['title'];
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->AddReplyTo($aliziConfig['mail_account'], "Information");
		foreach($email as $m){ $mail->AddAddress($m); }
		
       /*
		$mailaddress = array_shift($email);
        $mail->AddAddress($mailaddress);
        if(count($email)>0){  foreach($email as $m){  if(isEmail($m)){ $mail->AddBCC($m);}  } }
		$mail->SMTPDebug = 1;
		*/
        $status =  $mail->Send();
        return array('status'=>$status?1:0,'info'=>$mail->ErrorInfo);
    }


    //确认取消订单
    public function confirm(){
        $op = intval($_POST['op']);
        $id = trim($_POST['id']);
        $Order = M('Order');
        if(!is_numeric($id)){ $this->ajaxReturn(0,L('OrderNotExist'),0); }
        $where = array('order_no'=>$id);
        $info = $Order->where($where)->field('id,status')->find();
        if(empty($info)){ $this->ajaxReturn(0,L('OrderNotExist'),0); }
        switch($info['status']){
            case '0':
                $data = array(
                    'order_id'=>$info['id'],
                    'status'=>$op,
                    'remark'=>($op=='2'?L('customerConfirm'):L('customerCancel')),
                );
                $data['sign'] = createSign($data,C('ALIZI_KEY'));
                $ret = $this->aliziUpdateStatus($data);
                if($ret['status']==1){
                    $msg = $op=='2'?L('confirmSuccess'):L('cancelSuccess');
                    $this->ajaxReturn(0,$msg,0);
                }else{
                    $this->ajaxReturn(0,L('failure'),0);
                }
                break;
            case '2': $this->ajaxReturn(0,L('OrderConfirmed'),0);break;
            case '6': $this->ajaxReturn(0,L('OrderCanceled'),0);break;
            default: $this->ajaxReturn(0,L('statusMismatching'),0);
        }

    }
    function regionMatch($mobile,$address=''){

        //获取手机归属地
        $str = http("https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$mobile);
        preg_match('/province:\'(.+)\'/i',$str,$region);
        $province = iconv('GB2312','UTF-8',$region[1]);

        if(empty($address)){
            //获取IP归属地
            $str = http("http://ip.taobao.com/service/getIpInfo.php?ip=".get_client_ip());
            $json = json_decode($str,true);
            $address = $json['data']['region'];
        }
        if(strstr($address,$province)){
            return true;
        }else{
            return false;
        }
    }

    //减库存
    private function decQuantity($item_id,$num=1){
        M('Item')->where('id='.(int)$item_id)->setDec('quantity',$num);
    }
	
	private function commission($data = array()){
		
		if($data['order_status']==$this->aliziConfig['agent_settlement']){
			
			$Commission = M('Commission');
			$user_id = $data['user_id'];//$_COOKIE['alizi_uid'];
			
			$exists = $Commission->where(array('user_id'=>$user_id,'order_id'=>$data['order_id']))->getField('id');
			if(!$exists){
				$group_id = M('User')->where(array('id'=>$user_id))->getField('group_id');
				$discount = M('UserGroup')->where(array('id'=>$group_id))->getField('discount');
				
				$fee = intval($discount)/100*$data['total_price'];
				$add = array(
					'user_id'=>$user_id,
					'status'=>0,
					'item_id'=>$data['item_id'],
					'order_id'=>$data['order_id'],
					'total_price'=>$data['total_price'],
					'order_status'=>$data['order_status'],
					'fee'=>$fee,
					'add_time'=>time(),
				);
				$Commission->add($add);
			}
		}
	}

}