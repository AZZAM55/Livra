<?php


/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class CategoryAction extends AliziAction {
	public function _initialize(){
        parent::_init();
    }
	function category($type=1){
		if(isset($_GET['do'])){
			$this->edit($_GET['id'],$type);exit;
		}
		$Model = D('Category');
		$where = array('type'=>$type);
		$list  = $Model->where($where)->order('sort_order asc,id asc')->select();

		$this->assign('type',$type);
		$this->assign('list',$list);
	}
	
	//分类和标签编辑
	function edit($id=''){
		$Model = D('Category');
		
		if(!empty($id)){
			$info = $Model->where('id='.$id)->find();
			$this->assign('info',$info);
		}
		$this->display('Category:edit');
	}
	
	//删除分类
	public function delete($id){
		$Model = D('Category');
		$Model->delete($id);
		$this->ajaxReturn(1,'删除成功',1);
	} 
}