<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
defined('THINK_PATH') OR exit();
class LiteAction extends AliziAction {

    public function _initialize(){
        parent::_init();
    }

    public function index($id){
		header('content-type:text/html;charset=utf-8');
	   
		global $info;
        $info  = M('Item')->where(array('sn'=>$id))->find();

        $template  = M('ItemTemplate')->where(array('id'=>$info['id']))->find();
		if( $template){
			$template['extend'] = unserialize($template['extend']);
			$template['color'] = json_decode($template['color'],true);
			if(isset($_GET['theme'])) $template['template']=str_replace('-', '/', $_GET['theme']);
		}
		
		$list = array(
			'params'=>parent::getItemParams($template['options'],$request['options']),
			'product'=>json_decode($info['params'],true),
			'extends'=>json_decode($info['extends'],true),
			'payment'=>parent::getAliziPayment($sn),
			'aliziConfig'=>$aliziConfig,
			'cookie'=>$cookie,
			'color'=>json_decode($template['color'],true),
			'token'=>password($info['id'].get_client_ip()),
		);
		
		echo $this->form($info,$template);
    }
	
	private function form($info,$template){
		$params = parent::getItemParams($template['options']);
		$product = json_decode($info['params'],true);
		$extends = json_decode($info['extends'],true);
		$payment = parent::getAliziPayment($info['sn']);
		$aliziConfig = parent::aliziConfig();
		$host =  $this->aliziHost;
		$package = !empty($info['params_name'])?$info['params_name']:lang('itemPackage');
		return $html = include("./Public/Common/alizi.form.lite.php");
		
	}

   

}