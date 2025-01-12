<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<link href="__PUBLIC__/Assets/css/esui.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/Assets/css/union.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/Assets/js/jquery.min.js"></script>
<script type="text/javascript" src="__PUBLIC__/Assets/js/jquery.form.js"></script>
<script type="text/javascript" src="__PUBLIC__/Assets/js/jscolor.min.js"></script>
<style>
.info-table th, .info-table td{padding:3px 5px;}
.input-prepend{display:inline-block;margin:0;margin-right:5px;height: 22px;}
.input-prepend .add-on {float:left;margin:0;cursor:pointer;display: inline-block;height: 20px;min-width: 35px;padding: 0 2px;font-size: 14px;font-weight: normal;line-height: 20px;text-align: center;text-shadow: 0 1px 0 #fff;background-color: #eee;border: 1px solid #ccc;}
.input-prepend .add-on:hover{background:#fbeedf;}
.input-prepend .span2{display:none;float:left;height: 20px;width:120px;padding:0 2px;background-color: #fff; border: 1px solid #ccc;border-left:none;}
input::-webkit-input-placeholder,textarea::-webkit-input-placeholder {color: #CCCCCC;}
input:-moz-placeholder,textarea:-moz-placeholder{color: #CCCCCC;}
input::-moz-placeholder,textarea::-moz-placeholder {color: #CCCCCC;}
input:-ms-input-placeholder,textarea:-ms-input-placeholder {color: #CCCCCC;}
.button{margin-right:5px;padding:5px;border-radius:2px;background:#428bca;color:#fff;border:1px solid #428bca;cursor:pointer;}
.button:hover{opacity:0.8}
</style>
</head>
<body>
<div class="layout-main">    

    <div class="box clear-fix">
        <div class="AccountInfo">
            <div class="info-block" style="margin:20px;">
				<form method="post" action="<?php echo U('Order/batch');?>" id="ajaxform" enctype="multipart/form-data">
                <table class="info-table">
                    <tbody>
                    	<tr>
							<th width="150"><?php echo lang('action_status_colon');?></th>
                            <td>
								<select name="status" id="order-status" onchange="changeStatus(this.value)">
									<?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo (strip_tags($vo)); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
								<select name="delivery_name" id="delivery_name" style="display:none;">
									<?php if(is_array($delivery)): $i = 0; $__LIST__ = $delivery;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>"><?php echo (strip_tags($vo)); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</td>
                        </tr>
						<tr>
                            <th>订单ID：</th>
                            <td class="extends">
								<?php if(is_array($order)): $i = 0; $__LIST__ = $order;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span class="input-prepend">
								  <span class="add-on" title="点击删除"><?php echo ($vo["id"]); ?></span>
								  <input class="span2" type="text" data-id="<?php echo ($vo["id"]); ?>" data-no="<?php echo ($vo["order_no"]); ?>" name="id[<?php echo ($vo["id"]); ?>]" value="<?php echo ($vo["delivery_no"]); ?>" placeholder="快递编号">
								</span><?php endforeach; endif; else: echo "" ;endif; ?>
							</td>
                        </tr>
						<tr>
                            <th>批量操作：</th>
                            <td class="extends">
								<input type="hidden" class="btn btn-ok" name="upload" value="1">
								<input type="hidden" class="btn btn-ok" name="todo" value="modify">
								<button type="submit" class="button" value="modify" style="background:#f60;border:1px solid #f60;">修改订单状态</button>
								<button type="submit" class="button" value="send0">发送下单通知</button>
								<button type="submit" class="button" value="send3">发送发货通知</button>
								<button type="submit" class="button" value="del">批量删除订单</button>
								<!--button type="submit" class="button" name="todo" value="down">批量导出订单</button-->
							</td>
                        </tr>
						
                    </tbody>
                </table>
				</form>
            </div>
        </div>  
    </div><!--.box-->
	
<div id="ajaxLoading" style="display:none;"><div class="loading-bar"><img src="__PUBLIC__/Assets/img/waiting.gif"><span><?php echo lang('loading');?></span></div></div>
	
<script type="text/javascript">
$(function(){
	var todo = 'modify';
	$('.button').click(function(){
		todo = $(this).val();
	})
	var ajaxError = "<?php echo lang('ajaxError');?>";
    $('#ajaxform').ajaxForm({
        timeout: 15000,
        error:function(){  $('#ajaxLoading').hide();alert(ajaxError); },
        beforeSubmit:function(){ 
			if(!confirm('确认操作？')) return false;
			var opt = $('.span2');
			
			var len = opt.length;
			var j=0;
			for(var i=0;i< len;i++){
				j++;
				var status = $('select[name=status]').val();
				var delivery_name = $('select[name=delivery_name]').val();
				(function(i) {
					setTimeout(function() {
						i = i-1;
						console.log(i);
						var order_id = opt.eq(i).attr('data-id');
						var order_no = opt.eq(i).attr('data-no');
						var delivery_no = opt.eq(i).val();
						$.ajax({
							url:"<?php echo U('Order/import');?>",
							type:"POST",
							data:{todo:todo,status:status,delivery_name:delivery_name,order_no:order_no,order_id:order_id,delivery_no:delivery_no,remark:''},
							dataType:"json",
							success:function(rs){
								$('#ajaxLoading').show().find('span').text("已完成 "+i+"/"+len);
								if(i>=(len-1)){
									$('#ajaxLoading').hide();
									top.$.alert("成功",1,false);
								}
							}
						})
					},(j-1)*500);
				})(j);
			}
			return false;
			//$('#ajaxLoading').show().find('span').text("<?php echo lang('loading');?>");
			
		},
        success:function(data){
            $('#ajaxLoading').hide();
			top.$.alert(data.info,data.status,function(){
				if(parseInt(data.status)==1)parent.location.reload();
			});
        },
        dataType: 'json'
    });
	
	$('.input-prepend .add-on').on('click',function(){
		$(this).parent().remove();
	})
});
function changeStatus(id){
	if(id=='3'){
		$('.input-prepend .span2').show()
		$('#delivery_name').show()
	}else{
		$('.input-prepend .span2').hide()
		$('#delivery_name').hide()
	}
}
</script>