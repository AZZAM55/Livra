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

</head>
<body <?php if(C('DEFAULT_LANG') == 'arab'): ?>class="arab"<?php endif; ?>>

<?php if(!empty($aliziConfig["notice"])): ?><div class="aliziAlert" style="position: fixed; "><a class="close" onclick="$('.aliziAlert').slideUp(500);">×</a><?php echo ($aliziConfig["notice"]); ?></div>
<div class="aliziAlert">&nbsp;</div><?php endif; ?>


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
		
		<div class="storyhead">
			<a <?php if(empty($_GET["id"])): ?>class="active"<?php endif; ?> href="<?php echo U('Index/article');?>"><span><?php echo lang('all_category');?></span></a>
			<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a <?php if(($vo["id"]) == $info["category_id"]): ?>class="active"<?php endif; ?> href="<?php echo U('Index/article',array('id'=>$vo['id']));?>" title="<?php echo ($vo["name"]); ?>"><span><?php echo ($vo["name"]); ?></span></a><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		
		<div class="alizi-detail-wrap clearfix">
			<h1 class="article-title"><?php echo ($info["name"]); ?></h1>
			<div class="article-content"><?php echo ($info["content"]); ?></div>
		</div>
	</div>
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