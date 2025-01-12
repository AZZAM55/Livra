<?php
$reply = lang('reply_colon');
$realname = lang('realname');
$mobile = lang('mobile');
$content = lang('content');
$submit = lang('submit');
$alizi_root = C('ALIZI_ROOT');

$aliziComment = <<<EOF
<style>
#alizi-comments{overflow:hidden;}
#alizi-comments img{width:40%;}
#alizi-comments ul li{margin:0;padding:0;}
.recom_list{margin:12px;border:0}
.recom_list dt{background:none;display:block;padding-bottom:5px;}
.recom_list dd{margin-left:0;margin-bottom:8px;border-bottom:1px solid #ccc;padding-bottom:8px;padding-left:0}
.recom_list .fr{float:right}
.recom_list .reply{border:1px solid #eed3d7;background:#fbeedf;url("http://download.csdn.net/images/commend_reply.png") no-repeat 3px 3px;padding:5px;margin-top:10px;color:#D00;border-radius:2px;}

.comment-frame{padding:10px;10px;height:200px;position:relative;}
.comment-frame .alizi-rows{padding-right:15px;}
.comment-frame .rows-head b{color:#f00;}
.comment-frame .rows-params{margin-left:0}
[class^="diamond-"],[class^="heart-"]{margin-left:5px;margin-bottom:-3px;display:inline-block;height:16px;background:url('{$alizi_root}Public/Alizi/diamond.gif');background-position:left bottom;}
[class^="heart-"]{background:url('{$alizi_root}Public/Alizi/heart.gif');}
.diamond-1,.heart-1{width:16px;}
.diamond-2,.heart-2{width:32px;}
.diamond-3,.heart-3{width:48px;}
.diamond-4,.heart-4{width:64px;}
.diamond-5,.heart-5{width:80px;}
.comment-title{color:#999;text-indent:0.5em;}
#comments p{line-height:1.5em;}
</style>
<div  class="box-content">

	<div class="recom_list"  id="alizi-comments" style="height:380px">
		<ul id="comments"></ul>
	</div>
</div>


<script id="alizi-query" type="text/html">
    {{each list as value i}}
		<li>
		<dt>
			<a href="javascript:;" class="user_name">{{if value.name}}{{value.name}}{{else}}匿名{{/if}}</a><span class="{{value.start}}"></span>
		</dt>
		<dd>
			{{#value.content}}
			{{if value.title}}<p class="comment-title">{{value.title}}</p>{{/if}}
			{{if value.reply_content}}<div class="reply">{{#value.reply_content}}</div>{{/if}}
		</dd>
		</li>
    {{/each}}
</script>
</script>
<script type="text/javascript">
seajs.use(['jquery','art/template','alizi','jquery/form'],function($,template,alizi){
	
	$('#commentsAjax').click(function(){ comments(20); })
	comments(20);
	function comments(page){
		var item_id = '{$info['id']}',commentsAjax=$('#commentsAjax');
		var currentPage = commentsAjax.attr('data-page');
		$.ajax({
		   type: "POST",
		   url: "{$alizi_root}index.php?m=Order&a=getComments",
		   data: {item_id:item_id,page:page,currentPage:currentPage},
		   dataType: 'json',
		   success: function(data){
				commentsAjax.attr('data-page',data.data.currentPage)
				commentsAjax.find('b').html(data.data.leftPage);
				var html = template('alizi-query', data.data);
				$('#comments').append(html);
				if(parseInt(data.data.leftPage)==0){ commentsAjax.hide(); }
		   }
		 });
	}
	
	$('#aliziComment').ajaxForm({
		cache: true,
		timeout: 500,
		dataType: 'json',
		error:function(){ layer.closeAll(); alert(lang.ajaxError); },
		beforeSubmit:function(){
			layer.closeAll();layer.load();
		},
		success:function(data){
			if(data.status==1){
				$('#commentName').val('');
				$('#commentMobile').val('');
				$('#commentContent').val('');
			}
			layer.closeAll();layer.closeAll();
			layer.msg(data.info);
		}
	});
	
	alizi.scroll('alizi-comments',3000);
});

</script>
EOF;
return $aliziComment;
?>