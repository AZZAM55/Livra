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


<link href="__PUBLIC__/Alizi/pc/alizi.css?v=<?php echo (ALIZI_VERSION); ?>" rel="stylesheet">
<script type="text/javascript">
seajs.use(['jquery/lazyload'], function() {
	$(".alizi-lazy").lazyload({ placeholder : "__PUBLIC__/Alizi/alizi.gif",effect : "fadeIn"});
});
</script>

</head>
<body <?php if(C('DEFAULT_LANG') == 'arab'): ?>class="arab"<?php endif; ?>>

<?php if(!empty($aliziConfig["notice"])): ?><div class="aliziAlert" style="position: fixed; "><a class="close" onclick="$('.aliziAlert').slideUp(500);">×</a><?php echo ($aliziConfig["notice"]); ?></div>
<div class="aliziAlert">&nbsp;</div><?php endif; ?>

<div class="pbody">
	
<div class="header">
	<div class="mainwidth">
		<div class="headtop">
			<span class="place cur_city_name">
				<form action="<?php echo U('Index/category');?>" method="get" class="search_form">
					<input type="text" name="kw" class="search_input" placeholder="<?php echo lang('item_search');?>" value="<?php echo ($_GET['kw']); ?>" />
					<input type="submit" value="" class="search_button">
					<input type="hidden" name="m" value="Index" class="search_button">
					<input type="hidden" name="a" value="category" class="search_button">
				</form>
			</span>
			
			<div class="topright">
				<?php  ?>
				<a href="<?php echo U($wap_theme.'/index');?>" class="phone"><?php echo lang('themeMobile');?></a>
			</div>
			
		</div>
		<div class="logobox">
			<a href="<?php echo ($aliziHost); ?>" class="logo">
				<img title="<?php echo ($aliziConfig["title"]); ?>" alt="<?php echo ($aliziConfig["title"]); ?>" src="<?php if(empty($aliziConfig["logo_pc"])): ?>__PUBLIC__/Alizi/pc/logo.png<?php else: echo (imageurl($aliziConfig["logo_pc"])); endif; ?>">
			</a>
		</div>
		<div class="nav">
			<?php $navArray = explode("<br />",nl2br($aliziConfig['header_nav'])); foreach($navArray as $li){ $navArr = explode('||',$li); $navArr[1] = strpos($navArr[1],'index.php')===0?C('ALIZI_ROOT').$navArr[1]:$navArr[1]; $nav[] = $navArr; } ?>
			<ul>
				<li <?php if((ACTION_NAME) == "index"): ?>class="active"<?php endif; ?>><a href="<?php echo U('Index/index');?>"><span data-hover="<?php echo lang('index');?>"><?php echo lang('index');?></span></a></li>
				<?php if(isset($nav[0])): ?><li class="li_2"><a href="<?php echo ($nav[0][1]); ?>"><span data-hover="<?php echo ($nav[0][0]); ?>"><?php echo ($nav[0][0]); ?></span></a></li><?php endif; ?>
				<?php if(isset($nav[1])): ?><li class="li_3"><a href="<?php echo ($nav[1][1]); ?>"><span data-hover="<?php echo ($nav[1][0]); ?>"><?php echo ($nav[1][0]); ?></span></a></li><?php endif; ?>
				<?php if(isset($nav[2])): ?><li class="li_4"><a href="<?php echo ($nav[2][1]); ?>"><span data-hover="<?php echo ($nav[2][0]); ?>"><?php echo ($nav[2][0]); ?></span></a></li><?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<?php if(!empty($aliziConfig["theme_color"])): ?><style> 
	.booking-now,.alizi-btn,.alizi-plug .price,.alizi-badge{background-color: #<?php echo $aliziConfig['theme_color']; ?> !important;border-color: #<?php echo $aliziConfig['theme_color']; ?> !important;}
	.alizi-btn.active {color:#<?php echo $aliziConfig['theme_color']; ?>;border-bottom-color:#<?php echo $aliziConfig['theme_color']; ?>;}
	.side-menu a.active{border-left-color:#<?php echo $aliziConfig['theme_color']; ?>;}
	.btn-buy,.alizi-foot-nav{background-color:#<?php echo $aliziConfig['theme_color']; ?> !important;}
</style><?php endif; ?>


<div class="container">
	<div class="mainwidth">
		
		<?php if(!empty($slider)): ?><div class="newbanner">
		  <div class="newflexslider">
			<ul class="newslides">
				<?php if(is_array($slider)): $i = 0; $__LIST__ = $slider;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($vo["link"]); ?>" target="<?php echo ($vo["target"]); ?>"><img src="<?php echo (imageurl($vo["banner"])); ?>" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		  </div>
		</div>
		<script type="text/javascript">
		seajs.use(['jquery/newflexslider'], function(){ $('.newflexslider').newflexslider({ directionNav: true, pauseOnAction: false,newslideshowSpeed:5000 });});
		</script><?php endif; ?>
		
		


	<?php if(!empty($hot["list"])): ?><div class="indprocon alizi-hot">
		<div class="toptitle">
			<span class="titlein"><strong><?php echo lang('newArrival');?> / <span class="en">New Products</span></strong></span>
		</div>
		
		<ul class="indprolist">
			<?php if(is_array($hot["list"])): $i = 0; $__LIST__ = $hot["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
				<div class="img">
				<a href="<?php echo U('Index/order',array('id'=>$vo['sn']));?>">
					<img src="<?php echo (imageurl($vo["image"])); ?>" class="alizi-lazy"><span></span>
				</a>
				</div>
				<a href="<?php echo U('Index/order',array('id'=>$vo['sn']));?>" class="title ellipsis" title="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a>
				<p class="price">
					<strong class="alizi-price"><?php echo (getprice($vo["price"])); ?></strong>
					<?php if(floatval($vo['original_price']) > 0): ?><del><?php echo (getprice($vo["original_price"])); ?></del><?php endif; ?>
				</p>
				<span class="text"><?php echo ($vo["brief"]); ?></span>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
	</div><?php endif; ?>

	<?php $category = M('Category')->where('type=1')->order('sort_order asc')->select(); ?>
	<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $id = intval($_GET['id']); if($id && $id!=$vo['id']){continue;} $limit = empty($id)?$aliziConfig['item_category_num']:1000; $list = M('Item')->where('status=1 AND is_delete=0 AND category_id='.$vo['id'])->field('id,sn,name,image,price,brief')->order('sort_order asc')->limit($limit)->select(); if(empty($id) && empty($list)){continue;} ?>
		<div class="indprocon">
			<div class="toptitle">
				<span class="titlein"><a href="<?php echo U('Item/category',array('id'=>$vo['id']));?>" class="title"><strong><?php echo ($vo["name"]); ?></strong></a></span>
			</div>
		</div>
 
		<ul class="indprolist">
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
				<div class="img">
				<a href="<?php echo U('Index/order',array('id'=>$vo['sn']));?>">
					<img src="<?php echo (imageurl($vo["image"])); ?>" class="alizi-lazy"><span></span>
				</a>
				</div>
				<a href="<?php echo U('Index/order',array('id'=>$vo['sn']));?>" class="title ellipsis" title="<?php echo ($vo["name"]); ?>"><?php echo ($vo["name"]); ?></a>
				<span class="price">
					<strong class="alizi-price"><?php echo (getprice($vo["price"])); ?></strong>
					<?php if(floatval($vo['original_price']) > 0): ?><del><?php echo (getprice($vo["original_price"])); ?></del><?php endif; ?>
				</span>
				<span class="text"><?php echo ($vo["brief"]); ?></span>
			</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul><?php endforeach; endif; else: echo "" ;endif; ?>
	
	<?php $category = M('Category')->where('type=2')->order('sort_order asc ,id asc')->limit(3)->select(); $isNull = true; foreach($category as &$cate){ $list = M('Article')->where('is_delete=0 and status=1 and category_id='.$cate['id'])->order('sort_order asc ,id desc')->limit(10)->select(); if($list){ $isNull = false; $cate['list'] = $list; } } ?>
	<?php if(empty($isNull)): ?><div class="indprocon">
		<div class="toptitle">
			<span class="titlein"><strong><?php echo lang('newsInfo');?> / <span class="en">News Infomation</span></strong></span>
		</div>
		<div class="wrap clearfix">
			<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="news-list">
				
				<div class="list-title">
					<?php if(!empty($vo["image"])): ?><a href="<?php echo U('Index/article',array('id'=>$vo['id']));?>" title="<?php echo ($vo["name"]); ?>"><img src="<?php echo (imageurl($vo["image"])); ?>" alt="<?php echo ($vo["name"]); ?>"></a>
					<?php else: ?>
					<a href="<?php echo U('Index/article',array('id'=>$vo['id']));?>" title="<?php echo ($vo["name"]); ?>">
						<strong><span class="am-icon-book"></span> <?php echo ($vo["name"]); ?></strong>
					</a><?php endif; ?>
				</div>
				<ul>
					<?php if(is_array($vo["list"])): $i = 0; $__LIST__ = $vo["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('Index/detail',array('id'=>$li['id']));?>" target="_blank"><?php echo ($li["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div><?php endif; ?>
	
</div>
    </div>
<div id="downArrowBox"></div>
</div>


<div class="footer">
	<div class="mainwidth">
		<?php if(empty($aliziConfig['contact_tel']) && empty($aliziConfig['contact_qq'])): ?><style>.alizi-copyright{width:100% !important;padding-right:0 !important;text-align:center;}.alizi-contact{display:none !important;}</style><?php endif; ?>
		<ul class="footcon">
			<li class="alizi-copyright">
				<div class="copyright"><?php echo ($aliziConfig["footer"]); ?></div>
			</li>
			<li class="alizi-contact">
				<div class="foottel">
					<?php if(!empty($aliziConfig["contact_tel"])): ?><span class="tell"><?php echo lang('serviceNumber');?><span class="num"><?php echo ($aliziConfig["contact_tel"]); ?></span></span><?php endif; ?>
					<?php if(!empty($aliziConfig["contact_qq"])): ?><span class="online"><?php echo lang('online_service');?> <span class="num"><a href="https://wpa.qq.com/msgrd?v=3&uin=<?php echo ($aliziConfig["contact_qq"]); ?>&site=qq&menu=yes" target="_blank"><img border="0" src="https://wpa.qq.com/pa?p=2:<?php echo ($aliziConfig["contact_qq"]); ?>:51 &amp;r=0.22914223582483828"></a></span></span>
					<?php else: ?><br /><?php endif; ?>
				</div>
				
			</li>
		</ul>
	</div>
</div>
<div id="aliziUp"></div>
<script type="text/javascript">
seajs.use(['jquery/scrollup'], function(){ $.scrollUp({scrollName: 'aliziUp'}); });
</script>

<?php if(isset($_GET['buildHtml'])): ?><script type="text/javascript">
seajs.use(['jquery/query','jquery/cookie'],function(){var ac = $.query.get('ac');ac=ac?ac:$.query.get('gzid');if(ac){ $.cookie('ac',ac);$('input[name=channel_id]').val(ac);}})
</script><?php endif; ?>
<?php if(!empty($aliziConfig["contact_line"])): ?><a href="<?php echo ($aliziConfig["contact_line"]); ?>" target="_blank" style="display:inline-block;position: fixed;z-index:100;right:10px;bottom:100px;">
<img src="__PUBLIC__/Alizi/line.png" />
</a><?php endif; ?>
</body>
</html>