<?php
$reply = lang('reply_colon');
$realname = lang('realname');
$mobile = lang('mobile');
$content = lang('content');
$submit = lang('submit');
$alizi_root = C('ALIZI_ROOT');
$date = date('Y-m-d');
$list = M('Comments')->where(array('item_id'=>$info['id'],'status'=>1))->order('id desc')->select();
$totalPage = count($list);
$comments = "";
foreach($list as $li){
	$reply = $li['reply_content']?"<div class='reply'>{$li['reply_content']}</div>":"";
	$firstName = strtoupper(mb_substr($li['name'],0,1,'utf-8'));
	$comments .= "<li><dt><span class='time'>{$date}</span><div class='content_images'>{$firstName}</div><a href='javascript:;' class='user_name'>{$li['name']}</a><span class='{$li['start']}'></span></dt><dd>{$li['content']}<p class='comment-title'>{$li['title']}</p>{$reply}</dd></li>";
}
$aliziComment = <<<EOF
<style>
#alizi-comments{margin-top:20px;}
#alizi-comments ul li{margin:0;padding:0;}
.recom_list{margin:12px;border:0}
.recom_list dt{background:none;display:block;padding-bottom:5px;}
.recom_list dd{margin-left:0;margin-bottom:8px;border-bottom:1px dashed #ccc;padding-bottom:8px;padding-left:0}
.recom_list .fr{float:right}
.recom_list .reply{border:1px solid #eed3d7;background:#fbeedf;url("http://download.csdn.net/images/commend_reply.png") no-repeat 3px 3px;padding:5px;margin-top:10px;color:#D00;border-radius:2px;}

.comment-frame{padding:10px;10px;position:relative;}
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
#comments img {max-width:40%;}
.user_name{font-weight:bold;}

.content_images {margin-top: 3px;margin-right:5px;width: 37px;height: 37px;font-size: 22px;position: relative;float: left;margin-left: 1%;background: #f7bb02;line-height: 37px;text-align: center;color: #fff;border-radius: 50%;}
.time{float:right;margin-right:10px;font-size:12px;}
.discuss_pages{text-align: center;height: 40px;line-height: 40px;margin: 0 auto;font-size: 20px;color: #666;background: #fff;}
#btnComment{display: inline-block;margin: 0px auto;width: 140px;cursor: pointer;font-size: 14px;border: 1px solid #ccc;line-height: 28px;border-radius: 20px;color: #666;width: 140px !important;background: #FF6600 !important;text-align:center;}.current{color:#ff0000;}
.alizi-comment.alizi-order .rows-head{width:4rem;text-align:center;float: left;}
.alizi-comment.alizi-order .rows-params{padding-left:5rem;}
#comment-submit{width:95%;}
.star_ul{height:35px;}
.star_ul li{float:left;list-style:none;}
.xing_hui{width:25px;}
.alizi-comment select,.alizi-comment  .alizi-input-text{width:95%;}
.alizi-id-btn{text-align:center}

.pager{margin:10px 0;position:relative;text-align:center;zoom:1}
.pager:before,.pager:after{content:"";display:table}
.pager:after{clear:both;overflow:hidden}
.pager span{margin:0 2px;width:30px;height:30px;line-height:30px;color:#bdbdbd;font-size:14px}
.pager .active{display:inline-block;margin:0 5px;width:30px;height:30px;line-height:30px;background:#F60;color:#eee;font-size:14px;border:1px solid #F60}
.pager a,.pager span{display:inline-block;margin:0 2px;width:30px;height:30px;line-height:30px;background:#eee;border:1px solid #ebebeb;color:#333;font-size:14px;text-decoration:none;}
.pager a:hover,.pager .curPage{color:#eee;background:#F60}
.pager .noPage{color:#bdbdbd;}
</style>
<div  class="box-content">
	<div class="recom_list"  id="alizi-comments" >
		<ul id="comments"></ul>
		<ul id="hiddenresult" style="display:none;">{$comments}</ul>
	</div>
	<div class="pager" id="pagination"></div>
	
	<div class="discuss_pages"><a id="btnComment" style=" color:#fff; width:300px;">我要評價</a></div>
</div>
  
<script type="text/javascript">
seajs.use(['jquery', 'alizi','jquery/form','jquery/pager'],function($,alizi){

	var pageSize = 5,totalData = $("#hiddenresult li").length;
	var opt = {
		pageIndex: 0,
        pageSize: pageSize,
        itemCount: totalData,
        maxButtonCount: 4,
        prevText: "<<",
        nextText: ">>",
		onPageChanged:pageselectCallback
	}
	var pager = $("#pagination").pager(opt);
	function pageselectCallback(currentPage){
		var startNum = currentPage*pageSize;
		var content = "";
		$("#comments").html('');
		for(var i=0;i<pageSize;i++){
			var start = startNum+i; 
			content = $("#hiddenresult li:eq("+start+")").clone();
			$("#comments").append(content);
		}
		if(currentPage>0){scrollto('#alizi-comments');}
		return false;
	}
	pageselectCallback(0);

	

	$('#btnComment').click(function(){
		layer.open({
			title:'我要評價',
		  type: 1,
		  skin: 'layui-layer-rim', //加上边框
		  area: ['95%', '360px'], //宽高
		  content: '<div class="alizi-order alizi-comment"><form method="post" action="index.php?m=Order&a=comment" id="aliziComment"><div class="alizi-rows clearfix rows-id-name"><label class="rows-head">姓名</label><div class="rows-params"><input type="text" id="commentName" name="name" placeholder="請輸入姓名" autocomplete="off" alizi-request="1" class="alizi-input-text" value=""></div></div><div class="alizi-rows clearfix rows-id-name"><label class="rows-head">電話</label><div class="rows-params"><input type="text" id="commentMobile" name="mobile" placeholder="請輸入電話" autocomplete="off" alizi-request="1" class="alizi-input-text" value=""></div></div><div class="alizi-rows clearfix rows-id-name"><label class="rows-head">評分</label><div class="rows-params"><div class="main-wrap" id="mystar"><ul class="star_ul"><li num="1"><img src="__PUBLIC__/Alizi/x_full.png" class="xing_hui"></li><li num="2"><img src="__PUBLIC__/Alizi/x_full.png" class="xing_hui"></li><li num="3"><img src="__PUBLIC__/Alizi/x_full.png" class="xing_hui"></li><li num="4"><img src="__PUBLIC__/Alizi/x_full.png" class="xing_hui"></li><li num="5"><img src="__PUBLIC__/Alizi/x_full.png" class="xing_hui"></li></ul></div></div></div><div class="alizi-rows clearfix rows-id-name"><label class="rows-head">評價</label><div class="rows-params"><textarea name="content" placeholder="" class="alizi-input-text" alizi-request="" rows="2" placeholder="請輸入評價内容" id="commentContent"></textarea></div></div><!--div class="alizi-rows clearfix rows-id-file"><label class="rows-head">圖片<span class="alizi-request ">*</span></label><div class="rows-params"><input name="comment_file" type="hidden" class="ui-text left" id="comment_file"><button type="button" class="upload-button alizi-input-text alizi-btn" onclick="aliziUpload(\'#alizi-comment-file\')" style="background-color:#06c;border-color:#06c">上傳圖片</button><input type="file" name="comment_file2" autocomplete="off" id="alizi-comment-file" alizi-request="1" onchange="uploadImg(\'#alizi-comment-file\',\'#comment_file\')" class="hidden"></div></div--><div class="alizi-rows alizi-id-btn clearfix"><input type="button" id="comment-submit" onclick="commentSubmit()" class="alizi-btn alizi-submit" value="評價" ><input type="hidden" id="start" name="start" value="diamond-5"></div></form></div>'
		});
		initEvent('mystar');
	});
	
	/*评价*/
	 var isclick = false;
	function change(mydivid,num) {
		if (!isclick) {
			var tds = $("#"+mydivid+" ul li");
			for (var i = 0; i < num; i++) {
				var td = tds[i];
				$(td).find("img").attr("src","__PUBLIC__/Alizi/x_full.png");
			}
			var tindex = $("#"+mydivid).attr("currentIndex");
			tindex = tindex==0?0:tindex+1;
			for (var j = num; j < tindex; j++) {
				var td = tds[j];
				$(td).find("img").attr("src","__PUBLIC__/Alizi/x_empty.png");
			}
			$("#"+mydivid).attr("currentIndex",num);
		}
	}
	function repeal(mydivid,num) {
		if (!isclick) {
			var tds = $("#"+mydivid+" ul li");
			var tindex = $("#"+mydivid).attr("currentIndex");
			tindex = tindex==0?0:tindex+1;
			for (var i = tindex; i < num; i++) {
				var td = tds[i];
				$(td).find("img").attr("src","__PUBLIC__/Alizi/x_.png");
			}
			$("#"+mydivid).attr("currentIndex",num);
		}
	}
	function change1(mydivid,num) {
		if (!isclick) {
			change(mydivid,num);
			$('#start').val('diamond-'+num);
		}
		else {
			alert("Sorry,You had clicked me!");
		}
	}
	
	function initEvent(mydivid) {
		var tds = $("#"+mydivid+" ul li");
		for (var i = 0; i < tds.length; i++) {
			var td = tds[i];
			$(td).on('mouseover',function(){var num = $(this).attr("num");change(mydivid,num);});
			$(td).on('click',function(){var num = $(this).attr("num");change1(mydivid,num);});
		}
	}
	/*评价*/
  
	
});

function commentSubmit(){
	var name = $('#commentName');
	var mobile = $('#commentMobile');
	var content = $('#commentContent');
	var comment_file = $('#comment_file');
	var start = $('#start');
	var item_id = '{$info['id']}';
	//layer.msg("您輸入的信息有誤");return false;
	$.ajax({
		url:'index.php?m=Order&a=comment',
		data:{name:name.val(),mobile:mobile.val(),content:content.val(),start:start.val(),item_id:item_id,comment_file:comment_file.val()},
		type:'post',
		dataType:'json',
		success:function(data){
			alert(data.info);
			if(data.status==1){
				 layer.closeAll();
			}
		}
	})
}
</script>
EOF;
return $aliziComment;
?>