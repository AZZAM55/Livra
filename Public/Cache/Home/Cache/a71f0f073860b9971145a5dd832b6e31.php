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


<?php $color = $template['color']; ?>
<style>
.alizi-border,.alizi-side.alizi-full-row{ border-color:#<?php echo ($aliziConfig['theme_color']); ?>;}
<?php if(isset($template['width'])): ?>.alizi-detail-wrap{ max-width:<?php echo ($template["width"]); ?> !important;}<?php endif; ?>
<?php if(!empty($color)): ?>body,.alizi-order-wrap{ background-color:#<?php echo ($color["body_bg"]); ?>;}
.alizi-detail-content h2{ border-top-color:#<?php echo ($color["body_bg"]); ?>;}
.alizi-border,.alizi-side.alizi-full-row{ border-color:#<?php echo ($color["border"]); ?>;}
.alizi-detail-header dt{ color:#<?php echo ($color["font"]); ?>;}
.alizi-order{ color:#<?php echo ($color["font"]); ?>;background-color:#<?php echo ($color['form_bg']); ?>;}
.alizi-title{ background-color:#<?php echo ($color["title_bg"]); ?>;}
.alizi-btn,.alizi-btn:hover, .alizi-btn:active,.alizi-badge,.alizi-params.active,.alizi-group-box input:checked + label:after{ background-color:#<?php echo ($color['button_bg']); ?>;border-color:#<?php echo ($color["button_bg"]); ?>;}
.alizi-foot-nav{ background-color:#<?php echo ($color["nav_bg"]); ?>;}
.alizi-group.alizi-params.alizi-checkbox.active:hover{ background-color:#<?php echo ($color["button_bg"]); ?> !important;border-color:#<?php echo ($color["button_bg"]); ?> !important;color:#fff !important;}<?php endif; ?>
</style>

</head>
<body <?php if(C('DEFAULT_LANG') == 'arab'): ?>class="arab"<?php endif; ?>>

<?php if(!empty($aliziConfig["notice"])): ?><div class="aliziAlert" style="position: fixed; "><a class="close" onclick="$('.aliziAlert').slideUp(500);">×</a><?php echo ($aliziConfig["notice"]); ?></div>
<div class="aliziAlert">&nbsp;</div><?php endif; ?>
<link href="<?php echo ($aliziConfig["alizi_host"]); echo C('ALIZI_ROOT'); echo C('ALIZI_THEME_ROOT');?>alizi/assets/alizi.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet" type="text/css" /><?php if(!empty($aliziConfig["lazyload"])): ?><script type="text/javascript">seajs.use(['jquery/lazyload'], function() {	$(".alizi-detail-content img").lazyload({ placeholder : "__PUBLIC__/Alizi/alizi.gif",effect : "fadeIn",threshold:500})});</script><?php endif; ?><!--[if lt IE 9]><style>.header h1,.footer{color:#666;}</style><![endif]--><div class="wrapper alizi-detail-wrap">	<div class="header">		<h1><?php echo ($info["name"]); ?></h1>	</div>			<div class="alizi-page">		<?php if(!empty($info["slideshow"])): ?><div class="box box-image">			<h2 class="title"><?php echo lang('itemImage');?></h2>			<?php if(!empty($info["slideshow"])): ?><div class="box-image">
	<?php if(strpos($info['slideshow'],',') > 0): ?><div class="newbanner">
		  <div class="newflexslider">
			<ul class="newslides">
				<?php $_result=explode(',',$info['slideshow']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><img src="<?php echo (imageurl($vo)); ?>" /></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		  </div>
		</div>
		<script type="text/javascript">
		seajs.use(['jquery/newflexslider'], function(){ $('.newflexslider').newflexslider({ directionNav: true, pauseOnAction: false,newslideshowSpeed:4000 });});
		</script>
	<?php else: ?>
		<img src="<?php echo (imageurl($info['slideshow'])); ?>" /><?php endif; ?>
</div><?php endif; ?>		</div><?php endif; ?>		<div class="box alizibox-1">			<h2 class="title"><?php echo lang('purchase');?></h2>			<div class="box-content">				<div class="alizi-plug clearfix">					<div class="price" <?php if(empty($info["timer"])): ?>style="width:100%;"<?php endif; ?>>						<span class="symbol"><?php echo ($aliziConfig['symbol']?$aliziConfig['symbol']:lang('symbol')); ?></span>						<span class="current-price"><?php echo (floatval($info["price"])); ?></span>						<span class="group">							<?php if(floatval($info['original_price']) > 0): ?><del class="original-price"><?php echo ($aliziConfig['symbol']?$aliziConfig['symbol']:lang('symbol')); echo ($info["original_price"]); ?></del>							<?php else: ?><span class="original-price" style="height:10px;">&nbsp;</span><?php endif; ?>							<?php if(!empty($info["salenum"])): ?><span class="salenum"><?php echo lang('sold'); echo ($info["salenum"]); ?></span><?php endif; ?>						</span>					</div>					<?php if(!empty($info["timer"])): ?><div class="timer">						<p class="tt"><?php echo lang('countdown');?></p>						<div id="alizi-timer" class="alizi-timer">						<span class="aliziDay"><strong>0</strong></span><span class="aliziHour"><strong>00</strong></span><span>:</span><span class="aliziMinute"><strong>00</strong></span><span>:</span><span class="aliziSecond"><strong>00</strong></span>						</div>						<script type="text/javascript">							seajs.use(['alizi'],function(alizi) {								alizi.timer('#alizi-timer', '<?php echo ($info["timer"]); ?>');							})						</script>					</div><?php endif; ?>				</div>								<div class="alizi-content-title">					<!--h1><?php echo ($info["name"]); ?></h1-->					<p><?php echo ($info["brief"]); ?></p>				</div>				<?php if(!empty($info["tags"])): ?><div class="baoyou">					<?php $_result=explode('#',$info['tags']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span><?php echo ($vo); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>				</div><?php endif; ?>				<div class="gou">					<a href="javascript:scrollto('#aliziOrder');"><?php echo lang('buyNow');?></a>				</div>				<?php if(!empty($aliziConfig["contact_tel"])): ?><div class="gou"><a href="tel:<?php echo ($aliziConfig["contact_tel"]); ?>"><?php echo lang('BuybyMobile'); echo ($aliziConfig["contact_tel"]); ?></a></div><?php endif; ?>				<?php if(!empty($aliziConfig["contact_phone"])): ?><div class="gou"><a href="sms:<?php echo ($aliziConfig["contact_phone"]); ?>"><?php echo lang('BuybySMS'); echo ($aliziConfig["contact_phone"]); ?></a></div><?php endif; ?>				<?php if(!empty($aliziConfig["contact_qq"])): ?><div class="gou"><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo ($aliziConfig["contact_qq"]); ?>&site=qq&menu=yes"><?php echo lang('contactQQ'); echo ($aliziConfig["contact_qq"]); ?></a></div><?php endif; ?>							</div>		</div>					<div class="box alizibox-2">			<h2 class="title"><?php echo lang('itemDescription');?></h2>			<div class="box-content alizi-detail-content"><?php echo ($info['content']); ?></div>			<?php echo W('Relate',array_merge($info,array('moduel'=>'Order')));?> 		</div>	</div>			
<div class='alizi-remark'><?php echo ($info['remark']); ?></div>
<div class="alizi-footer"><?php echo ($aliziConfig["footer"]); ?></div>
<?php $showNav = count(array_filter($template['extend']['bottom_nav_list'])); if($showNav>0){ if($showNav==1){ $style = "style='width:100%'";}elseif($showNav==2){ $style = "style='width:49%'";}else{ $style = '';} $hidden = (strpos($template['extend']['bottom_nav_list'][1],'http')===0)?"style='display:none;'":""; $aliziOrder = "javascript:scrollto('body');"; $html = '<div class="alizi-foot-nav"><a class="alizi-up" href="'.$aliziOrder.'" '.$hidden.'>'.lang('top').'</a><ul>'; for($i=1;$i<=$showNav;$i++){ $nav = explode('||',$template['extend']['bottom_nav_list'][$i]); $class = isset($nav[2])?'icon '.$nav[2]:''; $text = $hidden = (strpos($nav[0],'http')===0)?'<img src="'.$nav[0].'" style="max-width:100%;">':'<span class="am-icon-'.$nav[2].'"></span><strong class="'.$class.'">'.$nav[0].'</strong>'; $html .= '<li class="foot-nav-'.$i.'" '.$style.'><a href="'.$nav[1].'">'.$text.'</a></li>'; } echo $html.'</ul></div>'; } ?>


<?php if($aliziConfig['show_qrcode'] == 1 && isMobile() == false): ?><div id="qrcode"><div class="qrcode"><div id="alizi-qrcode"></div><span><?php echo lang('qrcodeAddress');?></span></div></div>
<script type="text/javascript" src="__PUBLIC__/Assets/js/qrcode.min.js"></script>	
<script type="text/javascript">new QRCode(document.getElementById('alizi-qrcode'), {text:window.location.href,width:200,height:200});</script><?php endif; ?>

<script type="text/javascript">
seajs.use(['alizi','jquery/form','lang'],function(alizi){
	 			
	window.alizi = alizi;
	alizi.quantity(0);
	var btnSubmit = $('.alizi-submit');
	$('#aliziForm').ajaxForm({
		cache: true,
		timeout: 50000,
		dataType: 'json',
		error:function(){ layer.closeAll(); alert(lang.ajaxError); btnSubmit.attr('disabled',false).val(lang.submit); },
		beforeSubmit:function(){
			if(checkForm('#aliziForm')==false) return false; 
			layer.closeAll();layer.load();
			btnSubmit.attr('disabled',true).val(lang.loading);
		},
		success:function(data){
			layer.closeAll();layer.closeAll();
			if(data.status=='1'){
				//var redirect = "<?php echo ($aliziConfig["payment_url"]); echo U('Order/pay',array('order_no'=>'__orderNo__'));?>";
				var redirect = "<?php echo ($aliziConfig["payment_url"]); echo C('ALIZI_ROOT');?>index.php?m=Order&a=pay&item_id=<?php echo ($info["id"]); ?>&order_no="+data.data.order_no;
				var jumpUrl  = redirect.replace('__orderNo__',data.data.order_no);
				<?php if(!empty($info['javascript']) || session('fbpid')): echo ($info["javascript"]); ?>;
					<?php if(session('fbpid')): ?>fbq('track', 'Purchase', {value: data.data.total_price,currency: '{$aliziConfig.currency}'});<?php endif; ?>
					setTimeout(function(){ window.location.href = jumpUrl;},1000 );  
				<?php else: ?>
					window.location.href = jumpUrl;<?php endif; ?>
			}else{
				btnSubmit.attr('disabled',false).val(lang.submit);
				layer.msg(data.info);
			}
		}
	});
	setTimeout(function(){ $.get("<?php echo C('ALIZI_ROOT');?>index.php?m=Order&a=item_pv&item_id=<?php echo ($info['id']); ?>"); },1000);
});
var wx = <?php echo (json_encode($info["wx"])); ?>; 
</script>


<?php if(!empty($aliziConfig["weixin_status"])): $slideshow = explode(',',$info['slideshow']);$imgUrl=$slideshow[0]; ?>
<script>
define("wxShare",["jquery","https://res.wx.qq.com/open/js/jweixin-1.0.0.js"],function(a){
	var $=a("jquery"),wx=a('https://res.wx.qq.com/open/js/jweixin-1.0.0.js');
	var url = encodeURIComponent(location.href.split('#')[0]);
    $.ajax({
        type : "get",
        url : "<?php echo C('ALIZI_ROOT');?>index.php?m=Order&a=wx&url="+url,
        dataType : "json",
        async : false,
        success:function(share){
            wx.config({
                debug: false,
                appId: share.appId,
                timestamp: share.timestamp,
                nonceStr: share.nonceStr,
                url: share.url,
                signature: share.signature,
				//jsApiList:['onMenuShareAppMessage', 'onMenuShareTimeline', 'hideAllNonBaseMenuItem', 'showMenuItems','hideMenuItems']
                jsApiList: [ 'onMenuShareTimeline', 'onMenuShareAppMessage']
            });
        },
		complete: function(e){
			console.log('complete');
		},
        error:function(data){  alert(data); }
    });
	wx.ready(function () {
		var shareData = {
			title: '<?php echo ($info["name"]); ?>',
			desc: '<?php echo ($info["brief"]); ?>',
			link: '<?php echo (urldecode($thisUrl)); ?>',
			imgUrl: '<?php echo (imageurl($imgUrl)); ?>',
			success: function () {
				alert('分享成功！');
			}
		};
		wx.hideAllNonBaseMenuItem();
		wx.onMenuShareAppMessage(shareData);
		wx.onMenuShareTimeline(shareData);
	});
});
seajs.use("wxShare");
</script><?php endif; ?>
</div>

<?php if(isset($_GET['buildHtml'])): ?><script type="text/javascript">
seajs.use(['jquery/query','jquery/cookie'],function(){var ac = $.query.get('ac');ac=ac?ac:$.query.get('gzid');if(ac){ $.cookie('ac',ac);$('input[name=channel_id]').val(ac);}})
</script><?php endif; ?>
<?php if(!empty($aliziConfig["contact_line"])): ?><a href="<?php echo ($aliziConfig["contact_line"]); ?>" target="_blank" style="display:inline-block;position: fixed;z-index:100;right:10px;bottom:100px;">
<img src="__PUBLIC__/Alizi/line.png" />
</a><?php endif; ?>
</body>
</html>