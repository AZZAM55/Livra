<?php if (!defined('THINK_PATH')) exit();?>	
	</div><!--.layout-main-->	
</div>

<a id="scrollDown" href="javascript:;" onclick="$.scrollTo('#bottom',800)"></a>
<a id="scrollUp" href="#top"></a>
<div id="ajaxLoading" style="display:none;"><div class="loading-bar"><img src="__PUBLIC__/Assets/img/waiting.gif"><span><?php echo lang('loading');?></span></div></div>
<div id="bottom"></div>
<script type="text/javascript">
$(function(){
	scrollBottom();
	$(window).scroll(function(){ scrollBottom();});

	$.scrollUp({scrollName: 'scrollUp'});
	$(".pinned").pin();
	$('.ui-table-body-hover tbody tr').hover(function(){ $(this).addClass('ui-table-row-hover');},function(){ $(this).removeClass('ui-table-row-hover');});
});

function scrollBottom(){
	var scrollTop = $(this).scrollTop();
　　var scrollHeight = $(document).height();
　　var windowHeight = $(this).height();
　　if(scrollTop + windowHeight > scrollHeight-200){
　　　　$('#scrollDown').hide();
　　}else{
		$('#scrollDown').show();
	}
}
</script>
</body>
</html>