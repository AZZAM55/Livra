<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */
class MainWidget extends Widget {

	public function render($data){
		
		$allMenu = C('MENU');
		$menu = $allMenu[$_SESSION['user']['role']];
		$auth = $_SESSION['user']['auth'];
		
		foreach($menu as $key=>$value){
			if($key=='Index'){ 
				if($_SESSION['user']['id']!='1'){unset($menu[$key][1]);}//非最高管理员不显示幻灯片
				continue;
			}
			if(!in_array($key,array_keys($auth))){ unset($menu[$key]);}//一级栏目
			
			foreach($value as $k=>$v){
				foreach($v['list'] as $n=>$m){
					if(!in_array($n,$auth[$key])){
						unset($menu[$key][$k]['list'][$n]);
					}
				}
				if(empty($menu[$key][$k]['list'])){
					unset($menu[$key][$k]);
				}
			}
			if($menu[$key]) $menu[$key] = array_values($menu[$key]);//重置索引
		}	 
		
		$data['menu'] = $menu;
		$data['user'] = $_SESSION['user'];
		
		return $this->renderFile ("index", $data);
			
			
	}
}
?>