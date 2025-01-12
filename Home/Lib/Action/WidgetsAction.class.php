<?php
defined('THINK_PATH') OR exit();
class WidgetsAction extends AliziAction{
	
	public function form($request){
		
		$page = strtolower(MODULE_NAME);
		if($page=='order'){
			$page = isset($_GET['tpl'])?trim($_GET['tpl']):'single';
		}
		$sn = trim($request['id']);
		
		$aliziConfig = parent::aliziConfig();
		$info = getCache('Item',array('sn'=>$sn));
		$shipping = empty($info['shipping_id'])?array('id'=>0):getCache('Shipping',array('id'=>$info['shipping_id']));

		$cookie = array();
		if($aliziConfig['record_order']==1){ $cookie = cookie('order');$cookie['region'] = explode(' ', $cookie['region']);}
		$cookie['ac']=cookie('ac');
		$cookie['uid']=cookie('uid');

		if(in_array($page,array('index','item')) && $request['page']!='detail'){
			$template = array(
				'options'=>$aliziConfig['order_options'],
				'theme'=>$aliziConfig['system_theme'],
				'show_notice'=>$aliziConfig['show_notice'],
			);
		}else{
			$template = getCache('ItemTemplate',array('id'=>$info['id']),true);
			$template['extend'] = json_decode($template['extend'],true);
			
			if($template['template']){
				$config = include('Home/Tpl/Alizi/'.$template['template'].'/config.php');
				$request['options'] = $config['TEMPLATE_OPTIONS'];
			}
		}
		if(!empty($request['template'])) $template['theme'] = $request['template'];

		$list = array(
			'params'=>parent::getItemParams($template['options'],$request['options']),
			//'product'=>json_decode($info['params'],true),
			'product'=>M('ItemParams')->where(array('item_id'=>$info['id']))->order('sort_order asc,id asc')->select(),
			'extends'=>json_decode($info['extends'],true),
			'payment'=>parent::getAliziPayment($sn),
			'aliziConfig'=>$aliziConfig,
			'cookie'=>$cookie,
			'color'=>json_decode($template['color'],true),
			'token'=>password($info['id'].get_client_ip()),
		);

		foreach($list['payment'] as $payment){ $list['paymentDefault'] = $payment;break; }
		extract($list);
	
		$html = include("./Public/Common/alizi.form.{$template['theme']}.php");
		if(!$html){
			$tpl = isset($request['formTemplate'])?$request['formTemplate']:'order';
			$html = include('./Public/Common/alizi.form.'.$tpl.'.php');
		}
		return $html;
	}
	
	public function comment($request,$num=1){
		$sn = trim($request['id']);
		$aliziConfig = parent::aliziConfig();
		$info = getCache('Item',array('sn'=>$sn));
		return include("./Public/Common/alizi.comment-{$num}.php");
	}
	
	public function scroll($request,$num=1){
		$sn = trim($request['id']);
		$aliziConfig = parent::aliziConfig();
		$info = getCache('Item',array('sn'=>$sn));
		return include("./Public/Common/alizi.scroll-{$num}.php");
	}
}	
?>