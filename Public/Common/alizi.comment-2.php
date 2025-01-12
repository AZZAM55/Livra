<?php
$reply = lang('reply_colon');
$alizi_root = C('ALIZI_ROOT');
$list = M('Comments')->where(array('item_id'=>$info['id'],'status'=>1))->order('id desc')->select();
$totalPage = count($list);
$comments = "";
foreach($list as $li){
	$region = $li['region']?"<i class='alizi-badge right'>{$li['region']}</i>":"";
	$reply = $li['reply_content']?"<div class='reply' style='color:#f00;'>{$li['reply_content']}</div>":"";

	$comments .= "<li><div class='ov'><img class='omg' src='__PUBLIC__/Alizi/avatar2.png'><em>{$li['name']}</em><em class='vip'></em>{$region}</div><div class='con'>{$li['content']}</div>{$reply}</li>";
}


$aliziComment = <<<EOF
<style>
em,i{font-style: normal;font-weight: normal;}
#comments li{line-height:2.2rem;border-bottom:1px solid #eaeaea;padding:10px;overflow:hidden;position:relative;}
#comments .omg{border-radius:45px;margin-right:10px;width:2.5rem;height:2.5rem;}
#comments .right{font-size:1rem;float:right;}
#comments .ov{line-height:3rem;margin-bottom:6px;color:#999;}
#comments .con{color:#333;}
#comments img{width:40%;}

.pager{margin:10px 0;position:relative;text-align:center;zoom:1}
.pager:before,.pager:after{content:"";display:table}
.pager:after{clear:both;overflow:hidden}
.pager span{margin:0 2px;width:30px;height:30px;line-height:30px;color:#bdbdbd;font-size:14px}
.pager .active{display:inline-block;margin:0 5px;width:30px;height:30px;line-height:30px;background:#F60;color:#eee;font-size:14px;border:1px solid #F60}
.pager a,.pager span{display:inline-block;margin:0 2px;width:30px;height:30px;line-height:30px;background:#eee;border:1px solid #ebebeb;color:#333;font-size:14px;text-decoration:none;}
.pager a:hover,.pager .curPage{color:#eee;background:#F60}
.pager .noPage{color:#bdbdbd;}
</style>
<div id="alizi-comments">
	<ul class="comment" id="comments"></ul>
	<ul id="hiddenresult" style="display:none">{$comments}</ul>
	<div class="pager" id="pagination"></div>
</div>
 
<script type="text/javascript">
seajs.use(['jquery','jquery/pager'],function($,pager){
	var pageSize = 5,totalData = $("#hiddenresult li").length;
	var opt = {
		pageIndex: 0,
        pageSize: pageSize,
        itemCount: totalData,
        maxButtonCount: 4,
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
});
</script>
EOF;
return $aliziComment;
?>