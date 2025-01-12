<?php 
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class CategoryModel extends Model {

	protected $_validate = array(
		array('name', 'require', '标题名称不能为空！',1,'',1),
	);

}
?>