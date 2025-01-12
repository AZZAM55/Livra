<?php 
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class UserModel extends Model {

	protected $_validate = array(
		array('username', 'require', '用户名不能为空！',1,'',1),
		array('username', 'require', '该用户名不可用！',1,'unique',1),
		array('realname', 'require', '姓名不能为空！',1,'',1),
		//array('group_id', 'require', '请选择分组',1,'',1),
		array('mobile', 'require', '手机号不能为空！',1,'',1),
		array('password', 'require', '密码不能为空！',1,'',1),
		array('opassword', 'checkPassword', '原密码有误！',2,'callback',2),
		array('repassword','password','确认密码不正确！',0,'confirm'),
		array('email', 'email', '邮箱格式有误！',2),
	);
	protected $_auto = array(
		array('create_time', 'time', 1, 'function'),
		array('update_time', 'time', 2, 'function'),
		array('password','password',1,'function'),
	);
	
	//修改密码时验证原密码是否正确
	function checkPassword($opassword){
		$password = M('User')->where('id='.(int)$_REQUEST['id'])->getField('password');
		if($password!=password($opassword)) return false;
	}
	
	function _before_update(&$data, $options){
		if(!empty($data['password']))$data['password'] = password($data['password']);
		if(!empty($data['info']))$data['info'] = htmlspecialchars($data['info']);
    }
	function _before_insert(&$data, $options){
		$data['pid'] = empty($_POST['pid'])?$_SESSION['user']['id']:intval($_POST['pid']);
    }
	
}
?>