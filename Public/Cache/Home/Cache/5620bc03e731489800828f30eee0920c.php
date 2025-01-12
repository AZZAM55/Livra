<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html <?php if(C('DEFAULT_LANG') == 'arab'): ?>dir="rtl"<?php endif; ?>>
<head>
<title>
	<?php if(!empty($info["name"])): echo ($info["name"]); else: ?>
	<?php switch(ACTION_NAME): case "category": echo lang('item_category');?> -<?php break;?>
		<?php case "query": echo lang('order_query');?> -<?php break;?>
		<?php case "article": echo (getfields('Category','name',$_GET["id"])); ?> -<?php break; endswitch;?>
	<?php echo ($aliziConfig["title"]); endif; ?>
</title>
<meta charset="utf-8" />
<meta content="yes" name="apple-mobile-web-app-capable"/>
<meta content="yes" name="apple-touch-fullscreen"/>
<meta content="telephone=no" name="format-detection"/>
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0,minimum-scale=1.0, user-scalable=no" name="viewport">
<meta name="description" content="<?php if(empty($info["brief"])): echo ($aliziConfig["description"]); else: echo ($info["brief"]); endif; ?>">
<meta name="keywords" content="<?php if(!empty($info["tags"])): echo str_replace('#',',',$info['tags']);?>,<?php endif; echo ($aliziConfig["keywords"]); ?>">
<link rel="shortcut icon" href="<?php echo ($aliziConfig["alizi_host"]); echo C('ALIZI_ROOT');?>alizi.ico?v=<?php echo (ALIZI_VERSION); ?>" />
<link href="__PUBLIC__/Alizi/amazeui/css/amazeui.min.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo ($aliziConfig["alizi_host"]); ?>__PUBLIC__/Alizi/alizi-order.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet">
<?php if(!empty($template["template"])): ?><link href="<?php echo ($aliziConfig["alizi_host"]); echo C('ALIZI_ROOT');?>Home/Tpl/Alizi/<?php echo ($template['template']); ?>/assets/alizi.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet"><?php endif; ?>
<!--[if lt IE 9]><link href="<?php echo ($aliziConfig["alizi_host"]); ?>__PUBLIC__/Alizi/alizi-ie.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet"><![endif]-->
<script type="text/javascript" src="<?php echo ($aliziConfig["alizi_host"]); ?>__PUBLIC__/Alizi/seajs/seajs/sea.js?v=<?php echo C('ALIZI_VERSION');?>"></script>
<script type="text/javascript">
var aliziHost = "<?php echo ($aliziConfig["alizi_host"]); ?>",aliziRoot = "<?php echo ($aliziConfig["alizi_host"]); echo C('ALIZI_ROOT');?>",aliziVersion="<?php echo (ALIZI_VERSION); ?>",lang="<?php echo C('DEFAULT_LANG');?>";
seajs.config({ base: '<?php echo ($aliziConfig["alizi_host"]); ?>__PUBLIC__/Alizi/seajs/',alias: {'jquery': 'jquery/jquery','alizi': 'alizi/alizi','lang': 'alizi/lang-'+lang}, map: [['.css', '.css?v=' + aliziVersion]]});
function traceExpress(com,num,order_id,order_no){
	window.location.href= "<?php echo C('ALIZI_ROOT');?>index.php?m=Order&a=traceExpress&com="+com+"&num="+num+"&order_id="+order_id+"&order_no="+order_no;
}
</script>
<?php if(isset($info['header'])): echo ($info["header"]); endif; ?>
<?php if(session('fbpid')): $fbpid = explode(',',session('fbpid')); ?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
<?php if(is_array($fbpid)): $i = 0; $__LIST__ = $fbpid;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fb): $mod = ($i % 2 );++$i;?>fbq('init', '<?php echo ($fb); ?>');<?php endforeach; endif; else: echo "" ;endif; ?>
fbq('track', 'PageView');
fbq('track', 'ViewContent');
</script>
<noscript>
<?php if(is_array($fbpid)): $i = 0; $__LIST__ = $fbpid;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fb): $mod = ($i % 2 );++$i;?><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo ($fb); ?>&ev=PageView&noscript=1" /><?php endforeach; endif; else: echo "" ;endif; ?>
</noscript>
<!-- End Facebook Pixel Code --><?php endif; ?>


<style>.aliziAlert{display:none;}</style>

</head>
<body <?php if(C('DEFAULT_LANG') == 'arab'): ?>class="arab"<?php endif; ?>>

<?php if(!empty($aliziConfig["notice"])): ?><div class="aliziAlert" style="position: fixed; "><a class="close" onclick="$('.aliziAlert').slideUp(500);">×</a><?php echo ($aliziConfig["notice"]); ?></div>
<div class="aliziAlert">&nbsp;</div><?php endif; ?>

<?php $deposit = false; if(floatval($order['deposit'])>0 && !empty($order['deposit_payment'])){ $deposit = true; } ?>
<div class="result">
	<h1><?php if(empty($order["status"])): ?><img src="__PUBLIC__/Alizi/success.png"> <?php echo lang('submit_success'); else: echo lang('order_info'); endif; ?></h1>
    <div class="innner order_div success">
        <div class="order" style="min-height: calc(100vh - 244px);">
			<?php if(($order["status"]) == "3"): $item = M('Item')->where('is_delete=0 and id='.$order['item_id'])->field('is_auto_send,send_content')->find(); ?>
				<?php if(!empty($item["is_auto_send"])): ?><div class="alizi-alert">
					<span><?php echo (nl2br($item["send_content"])); ?></span>
				</div><?php endif; endif; ?>
					
            <ul>
				<?php if(($order["payment"]) != "1"): if(!empty($order["status"])): ?><li><label><?php echo lang('order_status_colon');?></label><span><?php $status=C('ORDER_STATUS'); echo ($status[$order['status']]); ?></span></li><?php endif; endif; ?>
				<li><label><?php echo lang('order_number_colon');?></label><span><?php echo ($order["order_no"]); ?></span></li>
				<li><label><?php echo lang('item_name_colon');?></label><span><?php echo ($order["item_name"]); ?></span></li>
				<?php if(!empty($order["item_params"])): $params_name = getFields("Item","params_name",$order['item_id']); ?>
				<li>
					<label><?php if(empty($params_name)): echo lang('itemPackage_colon'); else: echo ($params_name); echo lang('colon'); endif; ?></label>
					<?php $item_params = explode('#',$order['item_params']); $show_params = array(); foreach($item_params as $params){ $param = explode('||',$params); $show_params[] = $param[0]; } ?>
					<span><?php echo implode('#',$show_params);?></span>
				</li><?php endif; ?>
				<?php $extends = json_decode($order['item_extends'],true); ?>
				<?php if(!empty($extends)): if(is_array($extends)): $i = 0; $__LIST__ = $extends;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
						<label><?php echo ($key); echo lang('colon');?></label>
						<?php if(is_array($vo)){ $ret = array(); foreach($vo as $v){ $rs = explode('||',$v); $ret[] = $rs[0]; } $ret = implode('#',$ret); }else{ $ret = $vo; } ?>
						<span><?php echo ($ret); ?></span>
					</li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				<?php if(!empty($options)): $opt=C('TEMPLATE_OPTIONS');$payment = C('PAYMENT'); $file = explode(' ',$order['file']); ?>
				<?php if(is_array($options)): $i = 0; $__LIST__ = $options;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$name): $mod = ($i % 2 );++$i; if(in_array($name,array('verify','code','coupon','product','extends'))){continue;} ?>
					<li>
						<label><?php echo $opt[$name]['name']; echo lang('colon');?></label>
						<span>
							<?php switch($name): case "price": ?><b><?php echo ($order['total_price']); ?></b><?php break;?>
								<?php case "payment": echo $payment[$order[$name]]['name']; break;?>
								<?php case "file": ?><img src="<?php echo (imageurl($order["file"])); ?>" style="max-height:100px" /><?php break;?>
								<?php case "file": ?><img src="<?php echo (imageurl($file[0])); ?>" style="max-height:100px" /><?php break;?>
								<?php case "file2": ?><img src="<?php echo (imageurl($file[1])); ?>" style="max-height:100px" /><?php break;?>
								<?php default: echo ($order[$name]); endswitch;?>
						</span>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(!empty($deposit) && $order['payment'] == 'payOnDelivery'): ?><li>
						<label><?php echo lang('deposit_colon');?></label>
						<span><?php echo ($order["deposit"]); ?></span>
					</li>
					<li>
						<label><?php echo lang('pay_status_colon');?></label>
						<span><?php if(empty($order["deposit_ispay"])): ?><i style='color:#f00'>未支付</i><?php else: ?><i style='color:#06c'>已支付</i><?php endif; ?></span>
					</li><?php endif; ?>
				<?php else: ?>
					<!--li><?php echo lang('paymentSubmit');?></li--><?php endif; ?>
			</ul>
			<div class="result_info"><?php echo ($aliziConfig["result_info"]); ?></div>
        </div>
		
        <div class="foot" style="padding:10px;">
            <?php if($deposit == true && empty($order['deposit_ispay']) && $order['payment'] == 'payOnDelivery'): ?><a href="<?php echo U('Order/pay',array('order_no'=>$order['order_no'],'deposit'=>1,'payment'=>$order['deposit_payment']));?>" class="foot_btn" style="border-radius:6px;"><?php echo lang('pay');?></a><?php endif; ?>
            <a href="<?php echo ($redirectUrl); ?>" class="foot_btn" style="background-color:#AAA;border-radius:6px;"><?php echo lang('goback');?></a>
			<p><?php echo ($aliziConfig["footer"]); ?></p>
        </div>
    </div>
</div>
<script>
pushHistory();  
window.addEventListener("popstate", function(e) {  
	window.location.href = "<?php echo ($redirectUrl); ?>";
	pushHistory();  
}, false);  
function pushHistory() {  
	var state = { title: "title", url: "#" };  
	window.history.pushState(state, "title", "#");  
}
</script>
<?php if(!empty($aliziConfig["mail_send"])): ?><script type="text/javascript">
seajs.use(['jquery'],function($){ var order_id = "<?php echo ($order['id']); ?>";$.ajax({ url:"<?php echo C('ALIZI_ROOT');?>index.php?m=Api&a=send&order_id="+order_id,timeout:1000 });});
</script><?php endif; ?>



<?php if(isset($_GET['buildHtml'])): ?><script type="text/javascript">
seajs.use(['jquery/query','jquery/cookie'],function(){var ac = $.query.get('ac');ac=ac?ac:$.query.get('gzid');if(ac){ $.cookie('ac',ac);$('input[name=channel_id]').val(ac);}})
</script><?php endif; ?>
<?php if(!empty($aliziConfig["contact_line"])): ?><a href="<?php echo ($aliziConfig["contact_line"]); ?>" target="_blank" style="display:inline-block;position: fixed;z-index:100;right:10px;bottom:100px;">
<img src="__PUBLIC__/Alizi/line.png" />
</a><?php endif; ?>
</body>
</html>