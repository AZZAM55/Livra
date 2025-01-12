<?php
$html .= "<div class='alizi-delivery' style='height:250px;'><div class='alizi-scroll'><ul>";
if($aliziConfig['real_notice']==1){
	$orders = M('Order')->field('item_name,item_params,name,mobile,region,add_time')->where(array('item_id'=>$info['id']))->order('id asc')->limit(25)->select();
	$i=0;
	foreach($orders as $li){
		$region = explode(' ',$li['region']);
		$item_params = empty($li['item_params'])?'':' - '.$li['item_params'];
		$i++;
		$html .= "<li ".($i%2 == 0?"class='even'":'')."><p><span class='alizi-badge'>{$region[0]}</span>".mb_substr($li['name'],0,1,'utf-8')."*[".substr($li['mobile'],0,3)."****".substr($li['mobile'],-4)."]</p><p><span class='alizi-date'>".date('m-d',$li['add_time'])."</span>{$li['item_name']}{$item_params}</p></li>";
	}
}else{
	//$item = json_decode($info['params'],true);
	$item = M('ItemParams')->where("item_id=".$info['id'])->select();
	$province = explode(',',L('scrollProvince'));
	$name = explode(',',L('scrollName'));
	$mobile = explode(',',L('scrollMobile'));
	$time=  explode(',',L('scrollTime'));
	for($i=0;$i<50;$i++){
		$num = rand(0,3);
		$pro = empty($item)?$info['name']:$item[array_rand($item,1)]['title'];
		$pp = $province[array_rand($province,1)];
		$nn = $name[array_rand($name,1)];
		$mm = $mobile[array_rand($mobile,1)].'****'.randCode(4);
		$rand = array_rand($time);
		$html .= "<li ".($i%2 == 0?"class='even'":'')."><p><span class='alizi-badge'>{$pp}</span>{$nn}*[{$mm}]</p><p><span class='alizi-date'>{$time[$rand]}</span>{$pro}</p></li>";
	}	
}
$html .= "</ul></div></div>";
$html .="<script type='text/javascript'>seajs.use(['jquery','alizi'], function(jQuery,alizi) {alizi.resize('1');});</script>";	
return $html;
?>