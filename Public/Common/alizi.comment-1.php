<?php
$reply = lang('reply_colon');
$comments = M('Comments')->where(array('item_id'=>$info['id'],'status'=>1))->order('id DESC')->select();
if(empty($comments)){return false;}
$list  = "";
foreach($comments as $i=>$vo){
	$class = $i%2==0?'l':'r';
	$name = empty($vo['name'])?'':"<div style='float:left;'>{$vo['name']}：</div>";
	$replaycontent = empty($vo['reply_content'])?'':"<div class='reply' style='color:#f00;'>{$reply}{$vo['reply_content']}</div>";
	$list .= "<li class='{$class}'><div class='text clearfix'><div class='clearfix'>{$name}<div style='float:left;'>{$vo['content']}</div></div>{$replaycontent}</div><span class='corner'></span><span class='avatar'></span></li>";
}

$aliziComment = <<<EOF
<style>#alizi-comments img{width:40%;}</style>
<div id="alizi-comments" style="height:250px;overflow:hidden;"><ul class="list">{$list}</ul></div>	
<script type="text/javascript">
seajs.use(['alizi'],function(alizi){ alizi.scroll('alizi-comments',3000);});
</script>
EOF;
return $aliziComment;
?>