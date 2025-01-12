<?php
$config = include("Public/Common/config.php");
$menu = include("./Admin/Conf/config.menu.php");

if($config['DEFAULT_LANG'] != 'zh-cn'){
	$config2 = include("Home/Conf/config-{$config['DEFAULT_LANG']}.php");
	if(!empty($config2)){
		$status = array();
		$payment = array();
		foreach($config2['ORDER_STATUS'] as $key=>$value){
			$status[$key] = $config['ORDER_STATUS'][$key];
		}
		foreach($config2['PAYMENT'] as $key=>$value){
			$payment[$key] = isset($config['PAYMENT'][$key])?$config['PAYMENT'][$key]:$value;
		}
		$config['ORDER_STATUS'] = $status;
		$config['PAYMENT'] = $payment;
	}
}
$config['TOKEN_ON'] = false;  // 是否开启令牌验证
$config['URL_MODEL']       = 0;
$config['URL_HTML_SUFFIX'] = '';    // 静态后缀
$config['CURRENT_LANG'] = $config['DEFAULT_LANG'];
$config['DEFAULT_LANG'] = 'zh-cn';
$config['DEFAULT_THEME'] = '';
$config['MENU'] = $menu;
return $config;
?>