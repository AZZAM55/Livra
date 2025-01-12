<?php
$message = array();
if($aliziConfig['real_notice']==1){
	$orders = M('Order')->field('item_name,item_params,name,mobile,region,add_time')->where(array('item_id'=>$info['id']))->order('id asc')->limit(25)->select();
	$i=0;
	foreach($orders as $li){
		$region = explode(' ',$li['region']);
		$item_params = empty($li['item_params'])?'':' - '.$li['item_params'];
		$i++;
		$message[] = lang('newOrder').$region[0].mb_substr($li['name'],0,1,'utf-8')."**({$li['item_name']})";
	}
}else{
	$item = json_decode($info['params'],true);
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
		$message[] = lang('newOrder')."{$pp}{$nn}**({$mm})";
	}	
}

$msg = json_encode($message);
$public = C('ALIZI_ROOT').'Public/';
$html = <<<EOF
<style>
#fadein{line-height:40px;height:40px;overflow:hidden; position:fixed; left:0%; bottom:50%; overflow:hidden;opacity:0;font-size:11px; z-index:99999;}
#fadein img,#fadein a{float:left;border:none;}
#fadein .img_x1{width:22px;}
#fadein .img_x2{width:11px;}
#fadein a{background:#282828;font-size:11px !important;color:#FFF !important;}
</style>
<div id="fadein">
<img src="{$public}Alizi/x1.png" class="img_x1">
<a id="msg"></a>
<img src="{$public}Alizi/x2.png" class="img_x2">
</div>
<script>
var fadeinMsg={$msg};
var y=window.innerHeight/2;
function fadein(){
	var n=parseInt(Math.random()*fadeinMsg.length)
	$("#fadein #msg").html(fadeinMsg[n]);
	$("#fadein").css({"display":"block"}).animate({"bottom":"70%","opacity":"0.5"},1000).animate({"bottom":"70%","opacity":"0.9"},1000,
		function(){ $(this).animate({"opacity":"0.9"},3000).animate({"bottom":"80%","opacity":"0"},500,function(){ $(this).css({"bottom":"50%"}); })}
	)
}			
window.setInterval(fadein,6000);
</script>

EOF;
return $html;
?>