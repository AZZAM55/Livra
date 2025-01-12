<?php 
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class ArticleModel extends Model {

	protected $_validate = array(
		array('name', 'require', '标题不能为空！',1,'',1),
	);
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
		array('update_time', 'time', 2, 'function'),
	);
	
}
?>