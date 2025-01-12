<?php

 
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class ItemModel extends Model {

	protected $_validate = array(
		array('name', 'require', '标题不能为空！',1),
		array('sn', 'require', '商品编号不能为空！',1),
		array('sn', 'unique', '商品编号已存在！',1,'unique',3),
	);
	protected $_auto = array(
		array('add_time', 'time', 1, 'function'),
		array('update_time', 'time', 2, 'function'),
		
		array('name', 'strip_tags', 3, 'function'),
		array('sn', 'strFilter', 3, 'function'),
		array('javascript', 'strip_tags', 3, 'function'),
	);

	function _before_update(&$data,$options){

		$data['extends'] = json_encode($_POST['extend']);
		$data['payment'] = empty($_POST['payment'])?'':json_encode($_POST['payment']);
		$data['is_hot'] = intval($_POST['is_hot']);
		$data['is_big'] = intval($_POST['is_big']);
		$data['is_auto_send'] = intval($_POST['is_auto_send']);
		$data['qrcode_pay'] = intval($_POST['qrcode_pay']);
		$data['params'] = json_encode($list);
		$data['sms_send'] = json_encode($_POST['sms_send']);
		$data['slideshow'] = trim($_POST['slideshow'],',');
	}
	function _before_insert(&$data,$options){
		$data['sn'] = $data['sn']?$data['sn']:randCode(8,'abcdevghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
		$this->_before_update($data);
	}

	function _after_delete($data,$options){
		$ItemTemplate = M('ItemTemplate');
		$where = array('id'=>$data['id']);
		$ItemTemplate->where($where)->delete();
	}
	function _after_insert($data,$options){ 
		$this->_after_update($data,$options);
		$this->copyComments($data['id'],intval($_POST['item_id']));
	}
	function _after_update($data,$options){
		$this->template($data);	
		
		$params = $_POST['params'];
		$ids = $keys = array();
		
		$ItemParams = M('ItemParams');	
		if(!empty($_POST['params'])){
			foreach($_POST['params'] as $key =>$val){
				$val['price']= floatval($val['price']);
				if(is_numeric($key) && !empty($_POST['id'])){
					$ids[] = $key;
					//更新
					$ItemParams->save($val);
				}else{
					//增加
					unset($val['id']);
					$val['item_id']= $data['id'];
					$id = $ItemParams->add($val);
					$keys[$key] = $id;
					$ids[] = $id;
				}
			}
			if($ids){
				//删除
				$ItemParams->where(array('item_id'=>$data['id'],'id'=>array('not in',$ids)))->delete();
			}
		}else{
			$ItemParams->where(array('item_id'=>$data['id']))->delete();
		}
		
	}
	
	//更新模板
	function template($data){
		$aliziConfig = cache('aliziConfig');
		$ItemTemplate = M('ItemTemplate');
		
		$_POST['options'] = json_encode($_POST['options']);

		$extend = array(
			'padding'=>$_POST['padding'],
			'bottom_nav_list'=>$_POST['bottom_nav_list'],
		);
	
		$vo = $ItemTemplate->create();
		$vo['id'] = $data['id'];
		$vo['color'] = json_encode($_POST['color']);
		$vo['extend'] = serialize($extend);
		foreach ($vo as &$v) { $v=(!get_magic_quotes_gpc())?addslashes($v):$v; } 
		$ItemTemplate->query("REPLACE INTO __TABLE__(`".implode('`,`',array_keys($vo))."`) VALUES('".implode("','",array_values($vo))."')");

		cache('ItemTemplate'.$vo['id'],$vo); 
	}
	
	function copyComments($item_id,$copy_item_id){
		$Comments = M('Comments');
		$copyComments = $Comments->field('status,title,name,mobile,region,content,reply_content,start,add_time')
			->where(array('item_id'=>$copy_item_id))->select();
		
		foreach($copyComments as $li){
			$li['item_id'] = $item_id;
			$Comments->add($li);
		}
	}
	
}