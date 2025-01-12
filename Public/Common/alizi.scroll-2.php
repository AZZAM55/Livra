<?php
$html = "<style>.alizi-scroll2{height:160px;overflow:hidden;line-height:30px;border-radius: 5px; box-shadow: 0 1px 2px 2px #E4E4E4;background: #fafafa;margin:10px 5px}.alizi-scroll2 li span{margin-right:10px;}</style>";
$html .= "<div class='alizi-scroll2'><div style='padding: 5px;'><ul>";
if($aliziConfig['real_notice']==1){
	$orders = M('Order')->field('item_name,item_params,name,mobile,region,add_time')->where(array('item_id'=>$info['id']))->order('id asc')->limit(25)->select();
	$i=0;
	foreach($orders as $li){
		$region = explode(' ',$li['region']);
		$item_params = empty($li['item_params'])?$li['item_name']:$li['item_params'];
		$i++;
		$html .= "<li ".($i%2 == 0?"class='even'":'')."><span class='alizi-badge'>{$region[0]}</span><span class='alizi-mobile'>".mb_substr($li['name'],0,1,'utf-8')."*[".substr($li['mobile'],0,3)."****".substr($li['mobile'],-4)."]</span><span class='alizi-date'>".date('m-d',$li['add_time'])."</span>{$item_params}</li>";
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
		$html .= "<li ".($i%2 == 0?"class='even'":'')."><span class='alizi-badge'>{$pp}</span><span class='alizi-mobile'>{$nn}*[{$mm}]</span><span class='alizi-item'>{$pro}</span><span class='alizi-date'>{$time[$rand]}</span></li>";
	}	
}
$html .= "</ul></div></div>";
$html .= "<script>function scollDown(c,b){var a=$(c+' ul li').height();var b=b||2500;setInterval(function(){ $(c+' ul').prepend($(c+' ul li:last').css('height','0px').animate({height:a+'px'},'slow'))},b)}seajs.use(['jquery'],function(a){scollDown('.alizi-scroll2',3000)});</script>";

return $html;
?>

