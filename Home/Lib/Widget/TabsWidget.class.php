<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class TabsWidget extends Widget 
{
	public function render($data)
	{
		if(empty($data['status'])) return false;
		/*
		$ids = is_array($data['id'])?implode(',', $data['id']):$data['id'];
		if(empty($ids)){ return false;}
		$category = M('Category')->where("type=1 AND id IN($ids)")->order("field(id,$ids)")->select();
		*/
		$category = M('Category')->where("type=1")->order("sort_order asc,id asc")->limit(4)->select();
		$Item = M('Item');
		foreach($category as &$cat){
			$where = "is_delete=0 AND status=1 AND image!='' AND category_id={$cat['id']}";
			$cat['data'] = $Item->where($where)->field('id,sn,name,brief,price,image,thumb')->limit($data['num'])->order('sort_order ASC')->select();
		}
		$list['data'] = $category;
		$list['aliziConfig'] = Cache('aliziConfig');
		return $this->renderFile ("index", $list);
	}
}
?>