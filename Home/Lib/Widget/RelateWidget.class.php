<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class RelateWidget extends Widget 
{
	public function render($request){
		
		
		$Alizi = new AliziAction();
		$aliziConfig = $Alizi->aliziConfig();
		if($aliziConfig['relate_item_show']){
			$num = $aliziConfig['relate_item_num'];
			
			$list = M('Item')->where("is_delete=0 AND status=1 AND image!='' AND category_id={$request['category_id']} AND id!={$request['id']}")->limit($num)->order('sort_order asc,is_hot desc,id desc')->select();
			if(empty($list)) return false;
			include('Order/Relate.php');
		}else{
			return false;
		}
	}
}
?>