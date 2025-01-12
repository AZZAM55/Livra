<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class EmptyAction extends AliziAction {
	
public function index(){
	header('HTTP/1.1 404 Not Found');  
	parent::_init();
	$this->display('Order:404');
}

}?>