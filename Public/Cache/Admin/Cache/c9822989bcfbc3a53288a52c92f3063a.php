<?php if (!defined('THINK_PATH')) exit(); echo W("Main",array('module'=>MODULE_NAME,'action'=>ACTION_NAME,'do'=>$_GET['do']));?>
<style>
.sms-send a{display:inline-block;margin:2px -2px;}
.button{margin-left:5px;padding:3px;border-radius:2px;background:#f60;color:#fff;border:none;cursor:pointer;font-size:12px;}
.button:hover{background:#f90;}
</style>
<div class="layout-main">    
    <div id="breadclumb" class="box">
        <h3><strong><?php echo lang('breadclumb_colon');?></strong><?php echo lang(MODULE_NAME);?><span></span><?php echo lang('order_list');?></h3>
    </div>
    <div id="CooperationMain" class="box clear-fix">   
        <div class="layout-block-header">
            <form action="__SELF__" method="get" id="searchform">
            	<input type="hidden" name="s" value="<?php echo (MODULE_NAME); ?>" />
				<input type="hidden" name="a" value="<?php echo (ACTION_NAME); ?>" />
				<input type="hidden" name="channel_id" value="<?php echo ($_GET['channel_id']); ?>" />
                <label><?php echo lang('order_search_colon');?></label>
				<select name="fields">
					<?php $fields=array('order_no'=>lang('order_number'),'qudaonum'=>'渠道单号','item_sn'=>lang('item_number'),'item_name'=>lang('item_name'),'name'=>lang('customer_realname'),'mobile'=>lang('customer_mobile'),'address'=>'详细地址','channel_id'=>lang('channel'),); ?>
					<?php if(is_array($fields)): $i = 0; $__LIST__ = $fields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($_GET["fields"]) == $key): ?>selected='selected'<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
                <input type="text" name="keyword" value="<?php echo (trim($_GET['keyword'])); ?>" class="ui-text" autocomplete="off" size="40">
                <button type="submit" class="btn btn-ok"><?php echo lang('search');?></button>
				<?php if(in_array('detail',$_SESSION['user']['auth']['Order'])): ?><button type="submit" class="btn" name="aliziExcel"><?php echo lang('export_order');?></button><?php endif; ?>
				
				<div class="search-list filter clear-fix">
                    <label><?php echo lang('booking_time_colon');?></label>
                    <input type="text" name="time_start" value="<?php echo (trim($_GET['time_start'])); ?>" size="18" class="ui-text Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});"><?php echo lang('to');?><input type="text" name="time_end" value="<?php echo (trim($_GET['time_end'])); ?>" size="18" class="ui-text Wdate" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});">
			
					<select name="pageSize">
						<?php $pageSize=array('25','50','100','500'); ?>
						<?php if(is_array($pageSize)): $i = 0; $__LIST__ = $pageSize;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo); ?>" <?php if(($vo) == $_GET['pageSize']): ?>selected<?php endif; ?>>每页显示<?php echo ($vo); ?>条</option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<select name="user_id">
						<?php $users=M('User')->where("status=1")->select(); ?>
						<option value="">选择用户</option>
						<?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $_GET['user_id']): ?>selected<?php endif; ?>><?php echo ($vo["username"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
                </div>
				
				<div class="search-list filter clear-fix">
					<div class="title"><?php echo lang('order_status_colon');?></div>
					<div class="all"><q onclick="searchButtun('#status','')" <?php if(!is_numeric($_GET['status'])): ?>class="select_item"<?php endif; ?>>所有</q></div>
					<div class="division">|</div>
					<div class="scope"><?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><q onclick="searchButtun('#status','<?php echo ($key); ?>')" <?php if(is_numeric($_GET['status']) && $_GET['status'] == $key): ?>class="select_item"<?php endif; ?>><?php echo (strip_tags($vo["name"])); ?>(<?php echo ($vo["count"]); ?>)</q><?php endforeach; endif; else: echo "" ;endif; ?></div>
					<input type="hidden" name="status" id="status" value="<?php echo ($_GET["status"]); ?>">
				</div>
			
				<div class="search-list filter clear-fix">
					<div class="title"><?php echo lang('payment_colon');?></div>
					<div class="all"><q onclick="searchButtun('#payment','')" <?php if(empty($_GET['payment'])): ?>class="select_item"<?php endif; ?>>所有</q></div>
					<div class="division">|</div>
					<div class="scope"><?php $_result=C('PAYMENT');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><q onclick="searchButtun('#payment','<?php echo ($key); ?>')" <?php if($_GET['payment'] == $key): ?>class="select_item"<?php endif; ?>><?php echo (strip_tags($vo["name"])); ?></q><?php endforeach; endif; else: echo "" ;endif; ?></div>
					<input type="hidden" name="payment" id="payment" value="<?php echo ($_GET["payment"]); ?>">
				</div>
				
				
            </form>
        </div>
        
		<form action="<?php echo U('Order/deleteAll');?>" method="post" id="deleteform">
        <div class="ui-table">
            <div class="ui-table-body ui-table-body-hover">
                <table cellpadding="0" cellspacing="0" width="100%" >
                    <thead>
                        <tr class="ui-table-head">
                            <th class="ui-table-hcell" width="15"><input type="checkbox" id="check_box" onclick="$.Select.All(this,'id[]');" ></th>
                            <th class="ui-table-hcell" width="80"><?php echo lang('id_/_order_number');?></th>
                            <th class="ui-table-hcell"><?php echo lang('order_info');?></th>
                            <th class="ui-table-hcell" >
								<?php echo lang('customer_info');?>
								<button type="button" class="button" id="getReply">获取回复短信</button>
							</th>
                            <th class="ui-table-hcell" width="80"><?php echo lang('amount_price');?></th>
							<th class="ui-table-hcell" width="80"><?php echo lang('status');?></th>
                            <th class="ui-table-hcell" width="80"><?php echo lang('remark');?></th>
                            <th class="ui-table-hcell" width="85"><?php echo lang('time');?></th>
                            <th class="ui-table-hcell" width="100"><?php echo lang('action');?></th>
                        </tr>
                    </thead>
                    <tbody>
						<?php $payment = C('PAYMENT'); ?>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="row-<?php echo ($vo["id"]); ?>">
                            <td>
								<input type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>" onclick="$.Select.This(this);">
								<p class="sms-send">
									<?php if(empty($vo['sent'][0])): ?><a href="javascript:;" onclick="smsSend('<?php echo ($vo["id"]); ?>',0,0)" class="send-0" title="下单通知未发送"><img src="__PUBLIC__/Assets/img/false.png"></a>
									<?php else: ?>
									<a href="javascript:;" class="send-0" onclick="smsSend('<?php echo ($vo["id"]); ?>',0,1)" title="下单通知已发送"><img src="__PUBLIC__/Assets/img/true.png"></a><?php endif; ?>
									
									<?php if(empty($vo['sent'][1])): ?><a href="javascript:;" onclick="smsSend('<?php echo ($vo["id"]); ?>',1,0)" class="send-1" title="支付通知未发送"><img src="__PUBLIC__/Assets/img/false.png"></a>
									<?php else: ?>
									<a href="javascript:;" class="send-1" onclick="smsSend('<?php echo ($vo["id"]); ?>',1,1)" title="支付通知已发送"><img src="__PUBLIC__/Assets/img/true.png"></a><?php endif; ?>
									
									<?php if(empty($vo['sent'][3])): ?><a href="javascript:;" class="send-3" onclick="smsSend('<?php echo ($vo["id"]); ?>',3,0)" title="发货通知未发送"><img src="__PUBLIC__/Assets/img/false.png"></a>
									<?php else: ?>
									<a href="javascript:;" class="send-3" onclick="smsSend('<?php echo ($vo["id"]); ?>',3,1)" title="发货通知已发送"><img src="__PUBLIC__/Assets/img/true.png"></a><?php endif; ?>
								</p>
							</td>
                            <td>
								<?php if(($vo["num"]) > "1"): ?><a href="<?php echo U('Order/index',array('item_id'=>$vo['item_id'],'mobile'=>$vo['mobile']));?>" style='display:inline-block;background:#f60;padding:1px 8px;font-size:10px;border-radius:4px;color:#fff'><?php echo ($vo["num"]); ?></a><br /><?php endif; ?>
								ID:<?php echo ($vo["id"]); ?><br><?php echo ($vo["order_no"]); ?>
							</td>
                            <td> 
								<div style="float:left;">
								<p>【商品编号】<?php echo ($vo["item_sn"]); ?></p>
								<p>【商品名称】<?php echo ($vo["item_name"]); ?></p>
								<?php if(!empty($vo["item_params"])): ?><p><i class="fa fa-newspaper-o" aria-hidden="true"></i> 【商品套餐】<?php echo (str_replace("#","<br>",$vo["item_params"])); ?></p><?php endif; ?>
								<?php $item_extends = json_decode($vo['item_extends'],true); if(!empty($item_extends)){ foreach($item_extends as $key=>$val){ $val = is_array($val)?implode('#',$val):$val; echo "<p>【".$key."】$val</p>"; } } ?>
								<?php if(!empty($vo["channel_id"])): ?><p>【推广渠道】<?php echo ($vo["channel_id"]); ?></p><?php endif; ?>
								</div>
							</td>
                            <td>
								<?php if(($vo["user_id"]) > "1"): ?><p style='color:#f00'>【用户】<?php echo (getfields("User","username",$vo["user_id"])); ?></p><?php endif; ?>
								
									<?php if(!empty($vo["name"])): ?>【姓名】<?php echo ($vo["name"]); ?><br><?php endif; ?>
								<?php if(in_array('detail',$_SESSION['user']['auth']['Order'])): if(!empty($vo["mobile"])): ?>【手机】<?php echo ($vo["mobile"]); ?><br><?php endif; ?>
									<?php if(!empty($vo["mail"])): ?>【邮箱】<?php echo ($vo["mail"]); ?><br><?php endif; ?>
									<?php if(!empty($vo["address"])): ?>【地址】<?php echo ($vo["region"]); ?> <?php echo ($vo["address"]); ?><br><?php endif; ?>
									<?php if(!empty($vo["weixin"])): ?>【微信】<?php echo ($vo["weixin"]); ?><br><?php endif; endif; ?>
								<?php if(!empty($vo["receive"])): if(is_array($vo["receive"])): $i = 0; $__LIST__ = $vo["receive"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($i % 2 );++$i;?><p style="color:#f00">【<?php echo ($li["receive_time"]); ?> 回复】<?php echo ($li["receive_content"]); ?></p><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							</td>
                            <td>
								<?php echo ($vo["quantity"]); ?>/<b class="alert"><?php echo (floatval($vo["total_price"])); ?></b>
								<?php if(floatval($vo['deposit']) > 0): ?><p style="margin-top:10px;text-align:left;">预付款:<?php echo (floatval($vo["deposit"])); ?></p>
									<p style="text-align:left;"><?php if(($vo["deposit_ispay"]) == "1"): ?><a href="javascript:;">已付款</a><?php else: ?><i style="color:#f00">未付款</i><?php endif; ?></p><?php endif; ?>
							</td>
                            <td>
								<?php echo ($payment[$vo['payment']]['name']); ?>
								<p style="padding:8px; 0"><?php echo status($vo['status'],'text',C('order_status'));?><p>
								<!--select name="select" class="status-select" data-id="<?php echo ($vo["id"]); ?>" data-status="<?php echo ($vo["status"]); ?>"></select-->
								<?php echo experss($vo['delivery_name'],$vo['delivery_no'],$vo['id'],$vo['order_no']);?>
							</td>
                            <td>
								<?php echo $vo['remark']; ?>
							</td>
                            <td><?php echo (date("y-m-d H:i",$vo["add_time"])); ?></td>
                            <td class="action">
                                <a <?php if(in_array('modify',$_SESSION['user']['auth']['Order'])): ?>href="<?php echo U('Order/'.ACTION_NAME,array('do'=>'modify','id'=>$vo['id'],'status'=>$_GET['status']));?>"<?php else: ?>href="javascript:;" class="grey"<?php endif; ?>>修改</a>|
								<a <?php if(in_array('delete',$_SESSION['user']['auth']['Order'])): ?>href="javascript:del('<?php echo ($vo["id"]); ?>');"<?php else: ?>href="javascript:;" class="grey"<?php endif; ?>>删除</a>
								
							</td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
          
        <div class="ui-pager-bar clearfix" style="padding-left:10px;">
			<div class="float-left">
				<input type="hidden" name="model" value="<?php echo (MODULE_NAME); ?>">
				<input type="checkbox" id="check_box" onclick="$.Select.All(this,'id[]');" >选择/反选 
				<input type="hidden" name="del" value="1">
				<input type="button" value="批量操作" class="btn btn-ok" onclick="batch()">
			</div>
			<div class="ui-pager" style="float:right"><?php echo ($page); ?></div>
		</div>
		
		</form>
</div><!--.box-->
<script type="text/javascript" src="__PUBLIC__/Assets/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
$(function(){
	$('#getReply').on('click',function(){
		$.ajax({
			url:"<?php echo U('Order/getReply');?>",
			type:'get',
			dataType:'json',
			success:function(ret){
				$.alert(ret.info,ret.status,function(){
					if(ret.status=='1')window.location.reload();
				});
			}
		})
	})
	
	$('.status-select').on('change',function(){
		var id = $(this).attr('data-id');
		var status = $(this).val();
		$.ajax({
			url:"<?php echo U('Order/modify');?>",
			type:'post',
			data:{id:id,change_status:status},
			dataType:'json',
			success:function(ret){
				$.alert(ret.info,ret.status,function(){
					//if(ret.status=='1')window.location.reload();
				});
			}
		})
	})
})
function delConfirm(){
	$.confirm('是否要删除？',function(){ 
		$('#deleteform').submit();
	},true);
}
function del(id){
	$.confirm('是否要删除？',function(){ 
		$.ajax({
			url:"<?php echo U('Order/proccess');?>",
			data:{'do':'del','id':id},
			type:'get',
			dataType:'json',
			success:function(ret){
				$.alert(ret.info,ret.status);
				if(ret.status=='1'){ $('#row-'+id).remove(); }
			}
		})
	},true);
}
function smsSend(id,order_status,send_status){
	var msg = send_status==1?'是否重新发送短信通知？':'是否发送短信通知？';
	$.confirm(msg,function(){ 
		$.ajax({
			url:"<?php echo U('Order/smsSend');?>",
			data:{order_id:id,order_status:order_status,send_status:send_status},
			type:'get',
			dataType:'json',
			success:function(ret){
				$.alert(ret.info,ret.status);
			}
		})
	},true);
}
function batch(){
	var id = new Array();
	$('tbody input:checkbox:checked').each(function() {
		id.push($(this).val());
	});
	$.open("<?php echo U('Order/batch');?>&id="+id,{title:'批量操作',width:850,height:400})

}
function traceExpress(com,num,order_id,order_no){
	var url = "?m=Order&a=traceExpress&com="+com+"&num="+num+"&order_id="+order_id+"&order_no="+order_no;
	$.open(url,{title:'物流跟踪',width:650,height:500})
}
</script>
<?php echo W("Foot");?>