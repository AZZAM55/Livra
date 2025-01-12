<?php
/*
 * 标题：时时订单管理系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 */

class AliziTags {
	
	public static function alizi($item){
		preg_match_all("/(\{\[(Alizi([\w\d\-]+))\]\})/",$item['content'],$matches);
		if(!in_array('AliziWeixin',$matches[2])){
			array_push($matches[2],'AliziWeixin');
		}
	
		if(!empty($matches[2])){
			foreach($matches[2] as $tags){
				$tag = explode('-',$tags);
				$action = $tag[0];
				if(method_exists('AliziTags',$action)){
					$argv = isset($tag[1])?$tag[1]:'';
					$item['content'] = self::$action($item,$argv);
				}	
			}
			
		}
		return $item;
	}
	
	
	//订单标签
	private function AliziOrder(&$item,$num){
		return str_replace('{[AliziOrder]}',R('Widgets/form',array('request'=>$_GET)),$item['content']);
	}
	
	//评论标签
	private function AliziComment(&$item,$num){
		return str_replace('{[AliziComment-'.$num.']}',R('Widgets/comment',array('request'=>$_GET,'num'=>$num)),$item['content']);
	}
	
	//滚动标签
	private function AliziScroll(&$item,$num){
		return str_replace('{[AliziScroll-'.$num.']}',R('Widgets/scroll',array('request'=>$_GET,'num'=>$num)),$item['content']);
	}
	
	//微信客服标签
	private function AliziWeixin(&$item,$num){
		
		$weixin = explode(';',$item['weixin']);
		$wx = array('name'=>'','img'=>'');
		if(!empty($weixin)){
			$index = intval(array_rand($weixin));
			$arr = explode('|',$weixin[$index]);
			$wx = array('name'=>$arr[0],'img'=>isset($arr[1])?$arr[1]:'');
		}
		$item['wx'] = $wx;
		return str_replace('{[AliziWeixin]}',$wx['name'],$item['content']);
	}
	

}?>