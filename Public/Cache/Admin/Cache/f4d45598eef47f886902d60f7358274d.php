<?php if (!defined('THINK_PATH')) exit(); echo W("Main",array('module'=>MODULE_NAME,'action'=>ACTION_NAME,'do'=>$_GET['do']));?>
<script type="text/javascript" src="__PUBLIC__/Assets/js/My97DatePicker/WdatePicker.js"></script>
<div class="layout-main">    
    <div id="breadclumb" class="box">
        <h3><strong><?php echo lang('breadclumb_colon');?></strong><?php echo lang(MODULE_NAME);?><span></span><?php if(empty($_GET["id"])): echo lang('add'); else: echo lang('modify'); endif; ?></h3>
    </div>
    <div class="box clear-fix">
        <div class="layout-block-header"><h2><?php echo lang('order_info');?> <span class="order-no">（<?php echo lang('order_number_colon'); echo ($info["order_no"]); ?>）</span></h2></div>  
        <div class="AccountInfo">
            <div class="info-block">
				<form method="post" action="<?php echo U(MODULE_NAME.'/modify/');?>" enctype="multipart/form-data" id="ajaxform">
                <table class="info-table">
                    <tbody>
                    	<tr>
                            <th width="200"><?php echo lang('item_name_colon');?></th>
                            <td colspan="3"><?php echo ($info["item_name"]); ?></td>
                        </tr>
						<tr>
                            <th><?php echo lang('item_package_colon');?></th>
                            <td colspan="3" class="extends">
								<input type="text" name="item_params" value="<?php echo ($info["item_params"]); ?>" class="ui-text" size="80">
							</td>
                        </tr>
						<tr>
                            <th>商品属性：</th>
                            <td class="extends" colspan="3">
								<?php $extends=json_decode($info['item_extends'],true); foreach($extends as $name=>$value){ $value = is_array($value)?implode('，',$value):$value; echo "<p><i>$name</i>：<span><input type='text' name='item_extends[$name]' value='$value' class='ui-text' size='50'></span></p>"; } ?>
							</td>
                        </tr>
						
						<tr>
							<th><?php echo lang('order_status_colon');?></th>
                            <td>
								<?php $status = C('order_status'); ?>
								<select name="change_status">
									<?php if(is_array($status)): $i = 0; $__LIST__ = $status;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($key) == $info['status']): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
								<input type="hidden" name="status" value="<?php echo (intval($info["status"])); ?>">
							</td>
							<th>支付方式：</th>
                            <td>
								<?php $payment = C('PAYMENT'); ?>
								<select name="payment">
									<?php if(is_array($payment)): $i = 0; $__LIST__ = $payment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($key) == $info['payment']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</td>
                        </tr>
                        <tr>
                            <th><?php echo lang('amount_/_price_colon');?></th>
                            <td>
								<input type="text" name="quantity" value="<?php echo ($info["quantity"]); ?>" class="ui-text" size="5">/
								<input type="text" name="total_price" value="<?php echo ($info["total_price"]); ?>" class="ui-text" size="8"><?php echo lang('yuan');?>
								<?php if(!empty($info["coupon"])): ?><p style='color:#f00'><?php echo lang('coupon_colon'); echo ($info["coupon"]); ?></p><?php endif; ?>
							</td>
							<th>预约日期：</th>
                            <td valign="top"><input type="text" name="datetime" value="<?php echo (substr($info["datetime"],0,10)); ?>" size="12" class="ui-text Wdate" onclick="WdatePicker();"></td>
                        </tr>
						<?php if(in_array('detail',$_SESSION['user']['auth']['Order'])): ?><tr>
                            <th><?php echo lang('realname_colon');?></th>
                            <td width="100"><input type="text" name="name" value="<?php echo ($info["name"]); ?>" class="ui-text" size="30"></td>
                            <th><?php echo lang('mobile_colon');?></th>
                            <td><input type="text" name="mobile" value="<?php echo ($info["mobile"]); ?>" class="ui-text" size="30"></td>
                        </tr>
						<tr>
                            <th><?php echo lang('qq_colon');?></th>
                            <td><input type="text" name="qq" value="<?php echo ($info["qq"]); ?>" class="ui-text" size="30"></td>
                            <th><?php echo lang('email_colon');?></th>
                            <td><input type="text" name="mail" value="<?php echo ($info["mail"]); ?>" class="ui-text" size="30"></td>
                        </tr>
						<tr>
							<th valign="top">微信：</th>
							<td valign="top"><input type="text" name="weixin" id="weixin" class="ui-text" value="<?php echo ($info["weixin"]); ?>" size="30"></td>
							<th><?php echo lang('phone_colon');?></th>
                            <td><input type="text" name="phone" value="<?php echo ($info["phone"]); ?>" class="ui-text" size="30"></a></td>
                        </tr>
                        <tr>
                            <th><?php echo lang('booking_remark_colon');?></th>
                            <td><textarea name="remark" class="ui-text" style="height:40px;width:250px;"><?php echo ($info["remark"]); ?></textarea></td>
                            <th><?php echo lang('address_colon');?></th>
                            <td><input type="text" name="region" value="<?php echo ($info["region"]); ?>" class="ui-text" size="30"><br>
                                <input type="text" name="address" value="<?php echo ($info["address"]); ?>" class="ui-text" size="30"></td>
                        </tr>


						<tr>
                            <th><?php echo lang('express_setting_colon');?></th>
                            <td>
								<select name="delivery_name" id="delivery_name" style="float:left">
									<option value=""><?php echo lang('please_select_express');?></option>
									<?php if(is_array($delivery)): $i = 0; $__LIST__ = $delivery;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($info["delivery_name"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
							</td>
							<th><?php echo lang('express_number_colon');?></th>
							<td>	
								<input type="text" name="delivery_no" id="delivery_no" class="ui-text" value="<?php echo ($info["delivery_no"]); ?>" size="30">
							</td>
                        </tr>
			
						
						<tr>
                            <th>上传图片：</th>
                            <td>
                            	<?php $file = explode(' ',$info['file']); ?>
								<?php if(is_array($file)): $i = 0; $__LIST__ = $file;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$img): $mod = ($i % 2 );++$i;?><a href="<?php echo (imageurl($img)); ?>" target="_blank"><img src="<?php echo (imageurl($img)); ?>" height="40" /></a> &nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                            </td>
                            <th valign="top"><?php echo lang('zcode_colon');?></th>
                            <td valign="top"><input type="text" name="zcode" value="<?php echo ($info["zcode"]); ?>" class="ui-text" size="30"></td>
                        </tr>
						<tr>
                            <th><?php echo lang('action_remark_colon');?></th>
                            <td colspan=3>
                            	<textarea name="action_remark" id="action_remark" class="input-textarea editor" rows="3" style="width:40%"></textarea>
                            </td>
                        </tr><?php endif; ?>
						<tr>
                            <th>&nbsp;</th>
                            <td colspan="3">
								<input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
								<input type="submit" class="btn btn-ok" value="确认修改">
							</td>
                        </tr>
                    </tbody>
                </table>
				</form>
            </div>
        </div>  
    </div><!--.box-->
	
	
	
								
	<div class="box clear-fix">
        <div class="layout-block-header"><h2><?php echo lang('order_info');?> </span></h2></div>  
        <div class="AccountInfo">
            <div class="info-block">
                <table class="info-table">
                    <tbody>
                    	<tr>
                            <th width="200">下单 IP：</th>
                            <td><a href="http://www.ip.cn/index.php?ip=<?php echo ($info["add_ip"]); ?>&from=http://<?php echo ($_SERVER['HTTP_HOST']); echo C('ROOT_FILE');?>" target="_blank"><?php echo ($info["add_ip"]); ?></a></td>
							<th>下单设备：</th>
                            <td><?php if(($info["device"]) == "2"): ?>M<?php else: ?>PC<?php endif; ?></td>
                        </tr>
						<tr>
                            <th>下单地址：</th>
                            <td valign="top" colspan='3'><a href="<?php echo ($info["url"]); ?>" target="_blank"><?php echo ($info["url"]); ?></a></td>
						</tr>
						<tr>
                            <th>来路地址：</th>
                            <td valign="top" colspan='3'><a href="<?php echo ($info["referer"]); ?>" target="_blank"><?php echo ($info["referer"]); ?></a></td>
                        </tr>
						
                    </tbody>
                </table>
            </div>
        </div>  
    </div><!--.box-->
	
	
	<div class="box clear-fix">
        <div class="layout-block-header"><h2><?php echo lang('action_record');?></h2></div>  
        <div class="AccountInfo">
            <div class="ui-table-body ui-table-body-hover">
                <table cellpadding="0" cellspacing="0" width="100%">
                    <thead>
                        <tr class="ui-table-head">
                            <th class="ui-table-hcell" width="150">操作时间</th>
                            <th class="ui-table-hcell" width="80">操作类型</th>
                            <th class="ui-table-hcell" width="80">操作用户</th>
                            <th class="ui-table-hcell">操作备注</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php if(is_array($log)): $i = 0; $__LIST__ = $log;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo (date("Y-m-d H:i:s",$vo["add_time"])); ?></td>
                            <td><?php echo status($vo['status'],'',C('order_status'));?></td>
                            <td><?php if(empty($vo["user_id"])): echo lang('customer'); else: echo (getfields("User","username",$vo["user_id"])); endif; ?></td>
                            <td class="action"><?php echo ($vo["remark"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
                </table>
            </div>
        </div> 
    </div><!--.box-->
	
<script type="text/javascript">
$(function(){
	var order_id = "<?php echo ($info['id']); ?>";
    $('#ajaxform').ajaxForm({
        timeout: 15000,
        error:function(){ $('#ajaxLoading').hide();alert("<?php echo lang('ajaxError');?>");},
        beforeSubmit:function(){ 
			/*
			if( $('#remark').val()==''){
				$.alert('请输入备注内容',0);
				return false;
			}
			*/
			if(!confirm('确认操作？')) return false;

			$('#ajaxLoading').show();
		},
        success:function(data){ 
            $('#ajaxLoading').hide();
            if(data.status==1){
                //var redirectURL = "<?php if(empty($_GET["id"])): echo U('Order/index'); else: echo ($_SERVER['HTTP_REFERER']); endif; ?>";
				$.ajax({ url:"http://<?php echo ($_SERVER['HTTP_HOST']); echo C('ALIZI_ROOT');?>?m=Api&a=send",timeout:100,data:{order_id:order_id,status:data.data,print:1} });
                //$.alert(data.info,data.status,function(){window.location.reload();});
                $.alert(data.info,data.status,function(){window.history.back();});
            }else{
                $.alert(data.info,data.status);
            }
        },
        dataType: 'json'
    });
});
function delivery(){
	var id='<?php echo ($info["id"]); ?>';
	var delivery_name = $('#delivery_name').val();
	var delivery_no = $('#delivery_no').val();
	$.ajax({
		url:'<?php echo U("Order/deliveryUpdate");?>',
		type:'post',
		dataType:'json',
		data:{id:id,delivery_name:delivery_name,delivery_no:delivery_no},
		beforeSend:function(){
			if(!delivery_name){
				$.alert('请选择快递',0);return false;
			}
			if(!delivery_no){
				$.alert('请填写快递单号',0);return false;
			}
		},
		success:function(data){
			$.alert(data.info,data.status);
		}
		
	})
}
function modify(){
	var update_user_id = $('#update_user_id').val();
	if(!update_user_id){
		//$.alert('请选择客服',0);return false;
	}
}
</script>     
       
<?php echo W("Foot");?>