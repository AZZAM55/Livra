<?php if (!defined('THINK_PATH')) exit(); echo W("Main",array('module'=>MODULE_NAME,'action'=>ACTION_NAME,'do'=>$_GET['do']));?>
<script type="text/javascript">
isupload = false;
function aliziUpload(fileInput){
	if(isupload != false){
		$.alert("其他文件正在上传...请稍后");
	}else{
		$(fileInput).click();
	}
}
function uploadImg(fileInput,target,thumb){
	var filename = $(fileInput).val();
    if(filename != '' && filename != null){
    	isupload = true;
        var pic = $(fileInput)[0].files[0];
		console.log(pic);
        var fd = new FormData();
        fd.append('imgFile', pic);
        $.ajax({
            url:'<?php echo U("Public/upload");?>',
            type:"post",
            dataType:'json',
            data: fd,
            cache: false,
            contentType: false,
            processData: false,
			beforeSend:function(){
				$('#ajaxLoading').show().find('span').html('正在上传中...');
			},
            success:function(data){
				$('#ajaxLoading').hide();
                if(data.status=='1'){
					if(thumb==true){
						var imgUrl = "__PUBLIC__/Uploads/"+data.data;
						var img = "<p class='img'><img src='"+imgUrl+"' /><span class='del'  onclick='imgDel($(this),\""+data.data+"\")'>删除</span></p>";
						var imgInput = $('input[name='+target.substr(1)+']');
						var imgArray = imgInput.val().split(';');
						imgArray.push(data.data);
						$(target).append(img);
						imgInput.val(imgArray.toString());
						console.log(imgArray);
					}else{
						$(target).val(data.data);
					}
					$.alert('上传成功',1,'',{time:1500});
                }else{
					$.alert(data.data,0,'',{time:1500});
				}
            },
            error:function (){
				$.alert("上传出错了",0,'',{time:2500});
            }
        });
        isupload = false;
    }
    isupload = false;
}

function imgDel(_this,img){ 
	_this.parent().remove();
	var imgInput = $('input[name=slideshow]');
	var imgArray = imgInput.val().split(',');
	var index = imgArray.indexOf(img);
	imgArray.splice(index, 1);
	imgInput.val(imgArray.toString());
	console.log(imgArray);
}
</script>
<style>
.extends-tr .extends-list{display:none;}
.extends-tr .extends-list:first-child,.show .extends-list{display:block;}
.extends-list .item_params{display:none;}
.show .extends-list .item_params{display:table-row;}
#slideshow .img{float:left;position:relative;width:60px;height:40px;margin-right:5px;overflow:hidden;border:1px solid #888;background:#888;text-align:center;}
#slideshow .img img{width:100%;}
#slideshow .img .del{display:none;width:100%;height:20px;line-height:20px;background:#f00;color:#fff;text-align:center;bottom:0;position:absolute;cursor:pointer;opacity:0.8;}
#slideshow .img:hover .del{display:block;}
.slideshow .button{float:left;display: inline-block;cursor: pointer;background: #00b7ee;padding: 13px 20px;color: #fff;text-align: center;border-radius: 3px;overflow: hidden;margin-right:20px;border:none;}
.slideshow .button:hover{opacity:0.8;}
</style>
<div class="layout-main">    
    <div id="breadclumb" class="box">
        <h3><strong><?php echo lang('breadclumb_colon');?></strong><?php echo lang(MODULE_NAME);?><span></span><?php if(empty($_GET["id"])): echo lang('add'); else: echo lang('edit');?><span></span><?php echo ($info["name"]); endif; ?> <?php if(($_GET['do']) == "copy"): ?><b class="alert">【<?php echo lang('item_copy');?>】</b><?php endif; ?></h3>
    </div>
    <div class="box clear-fix" id="CooperationMain">
		
		<ul class="ui-tab-group" style="border-bottom:none;">
			
			<li id="basic_info" class="active"><a href="#basic_info">基本信息</a></li>
			<li id="website_setting" class=""><a href="#website_setting">价格套餐</a></li>
			<li id="payment_setting" class=""><a href="#payment_setting">商品图片</a></li>
			<li id="sms_setting" class=""><a href="#sms_setting">短信通知</a></li>
			<li id="ads_setting" class=""><a href="#ads_setting">广告设置</a></li> 
			<li id="page_setting" class=""><a href="#page_setting">单页模板</a></li>
		</ul>
			
        <div id="AccountInfo">
            <form method="post" action="<?php echo U(MODULE_NAME.'/proccess/');?>" id="ajaxform" enctype="multipart/form-data">
				
				<div class="info-block">
					<table class="info-table">
						<tbody>
							<tr>
								<th><b class="verifing">*</b><?php echo lang('item_number');?></th>
								<td>
									<?php if(!empty($_GET['id']) && $_GET['do'] != 'copy'): ?><input name="sn" type="text" class="ui-text validate[required,minSize[4],custom[onlyLetterNumber]]" size="30" value="<?php echo ($info["sn"]); ?>">
									<?php else: ?>
									<input name="sn" type="text" class="ui-text validate[required,minSize[4],custom[onlyLetterNumber]]" size="30" value="<?php echo randCode(6,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0987654321');?>"><?php endif; ?>
									<span class="ui-validityshower-info">（只能填写字母和数字）</span>
								</td>
							</tr>
							<tr>
								<th><b class="verifing">*</b><?php echo lang('name_colon');?></th>
								<td><input name="name" type="text" class="ui-text validate[required,minSize[2]]" size="100" value="<?php echo ($info["name"]); if(($_GET['do']) == "copy"): ?>【<?php echo lang('copy');?>】<?php endif; ?>"></td>
							</tr>
							<tr>
								<th><b class="verifing">*</b><?php echo lang('category_colon');?></th>
								<td>
									<select name="category_id" class="validate[required]">
										<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($info["category_id"]) == $vo["id"]): ?>selected='selected'<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th>已售数量：</th>
								<td>
									<input name="salenum" class="ui-text" size="4" type="number" value="<?php echo isset($info['salenum'])?$info['salenum']:randCode(3);?>" min="0">
								</td>
							</tr>
							<tr>
								<th>最少订购数量：</th>
								<td>
									<input name="min_num" class="ui-text left" size="3" type="number" value="<?php echo isset($info['min_num'])?intval($info['min_num']):1;?>" min="1">
									
									<label class="left" style="margin-left:30px;">最多可订购数量：</label>
									<div class="left">
										<input name="max_num" type="number" class="ui-text validate[required]" value="<?php echo isset($info['max_num'])?intval($info['max_num']):100;?>" size="3" min="1">
									</div>
								</td>
							</tr>
							<tr>
								<th>下单限制：</th>
								<td>
									<input name="limit_num" class="ui-text left" size="3" type="number" value="<?php echo isset($info['limit_num'])?intval($info['limit_num']):10;?>" min="1">
									<span class="ui-validityshower-info">（一个客户/手机可下单次数）</span>
								</td>
							</tr>
							<?php if(!empty($aliziConfig["item_quantity"])): ?><tr>
								<th>商品库存：</th>
								<td>
									<input name="quantity" class="ui-text" size="4" type="number" value="<?php echo ($info["quantity"]); ?>"  min="0">
								</td>
							</tr><?php endif; ?>
							
							<tr>
								<th><?php echo lang('tags_colon');?></th>
								<td>
									<input name="tags" type="text" class="ui-text" value="<?php echo ($info["tags"]); ?>" size="80">
									<span class="ui-validityshower-info">（多个标签请用#分开）</span>
								</td>
							</tr>
							<tr>
								<th><?php echo lang('brief_colon');?></th>
								<td>
									<textarea name="brief" class="input-textarea" cols="82" rows="3"><?php echo ($info["brief"]); ?></textarea>
									<span class="ui-validityshower-info">（一句话的简介）</span>
								</td>
							</tr>
							
							<tr>
								<th><?php echo lang('content_colon');?></th>
								<td>
									<textarea name="content" id="content" class="input-textarea editor" cols="80" rows="6"><?php if(empty($info["content"])): ?>{[AliziOrder]}<?php else: echo ($info["content"]); endif; ?></textarea>
								</td>
							</tr>
							

							<tr>
								<th><?php echo lang('status_colon');?></th>
								<td>
									<select name="status"><?php echo (status($info["status"],"select","1:显示;0:隐藏;2:禁用")); ?></select>
									<span class="ui-validityshower-info">（隐藏则商城前台不显示，禁用则没法下单）</span>
								</td>
							</tr>
							
							<tr>
								<th><?php echo lang('hot_colon');?></th>
								<td>
									<input name="is_hot" type="checkbox" value="1" <?php if(($info["is_hot"]) == "1"): ?>checked<?php endif; ?>>
									<span class="ui-validityshower-info">（显示在商城首页推荐）</span>
								</td>
							</tr>
							
							
							<tr>
								<th>排序：</th>
								<td>
									<input name="sort_order" type="text" class="ui-text" value="<?php echo isset($info['sort_order'])?$info['sort_order']:100;?>" size="10">
									<span class="ui-validityshower-info">（ 数值越小越靠前）</span>
								</td>
							</tr>
							<tr>
								<th>倒计时：</th>
								<td>
									<input name="timer" type="text" class="ui-text" value="<?php echo ($info["timer"]); ?>" size="10">
									<span class="ui-validityshower-info">（ 秒。为空或0则不显示）</span>
								</td>
							</tr>
							
							<?php $aliziConfig = S('aliziConfig');$payment = C('PAYMENT');$itemPayment=json_decode($info['payment']); ?>
							<?php if(empty($aliziConfig["payment_global"])): ?><tr>
								<th><?php echo lang('payment_colon');?></th>
								<td>
									<?php if(is_array($payment)): $i = 0; $__LIST__ = $payment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="checkbox" name="payment[]" value="<?php echo ($key); ?>" <?php if(in_array($key,$itemPayment)): ?>checked="checked"<?php endif; ?>>
									<label class="ui-group-label"><?php echo ($vo["name"]); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
								</td>
							</tr><?php endif; ?>
							
							<tr>
								<th>微信客服：</th>
								<td>
									<input name="weixin" type="text" class="ui-text" value="<?php echo ($info["weixin"]); ?>" size="60">
									<span class="ui-validityshower-info">格式：微信号|微信二维码图片地址。多个微信号请用英文分号(;)隔开</span>
								</td>
							</tr>
							
							<tr>
								<th>绑定域名：</th>
								<td>
									<input name="domain" type="text" class="ui-text" value="<?php echo ($info["domain"]); ?>" size="60">
									<span class="ui-validityshower-info">绑定域名打开则只显示该单页，如：www.wxrob.com</span>
								</td>
							</tr><tr>
								<th>附加内容：</th>
								<td>
									<p><span class="ui-validityshower-info">显示在单页内容下面</span></p>
									<textarea name="remark" id="remark" class="input-textarea" cols="125" rows="3"><?php echo ($info["remark"]); ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!--.info-block-->
				
				<div class="info-block" style="display:none;">
					<table class="info-table">
						<tbody>
							<tr>
								<th><?php echo lang('qrcode_payment_colon');?></th>
								<td>
									<select name="qrcode_pay" id="qrcode_pay" onchange="qrcodepay(this.value)">
										<option value="0">不使用二维码</option>
										<option value="1" <?php if(($info["qrcode_pay"]) == "1"): ?>selected="selected"<?php endif; ?>>固定金额二维码</option>
										<option value="2" <?php if(($info["qrcode_pay"]) == "2"): ?>selected="selected"<?php endif; ?>>不定金额二维码</option>
									</select>
									<span class="ui-validityshower-info">（可使用个人二维码收款，但不能同步订单状态。不推荐使用！）</span>
								</td>
							</tr>
							<tr class="qrcode <?php if(!empty($info["qrcode_pay"])): ?>show<?php endif; ?>">
								<th><?php echo lang('payment_说明_colon');?></th>
								<td>
									<input name="qrcode_pay_info" class="ui-text" size="50" id="qrcode_pay_info" type="text" value="<?php echo ($info["qrcode_pay_info"]); ?>" >
									<span class="ui-validityshower-info">（换行请用&lt;br&gt;）</span>
								</td>
							</tr>
							<tr>
								<th><?php echo lang('price_colon');?></th>
								<td>
									<div class="left" style="margin-right: 20px;">
										<label>原价：</label>
										<input name="original_price" class="ui-text" size="4" type="text" value="<?php echo ($info["original_price"]); ?>">
										<span class="ui-validityshower-info"><?php echo lang('yuan');?></span>
									</div>

									<label class="left"><b class="verifing">*</b>现价：</label>
									<div class="left">
										<input name="price" type="text" class="ui-text validate[required]" value="<?php echo ($info["price"]); ?>" size="4">
										<span class="ui-validityshower-info"><?php echo lang('yuan');?></span>
									</div>
									<div class="left qrcode <?php if(!empty($info["qrcode_pay"])): ?>show<?php endif; ?>" style="margin-left:20px;">
										<label class="left"><?php echo lang('qrcode_colon');?></label>
										<div class="left">
											<input name="qrcode" type="text" class="ui-text left" value="<?php echo ($info["qrcode"]); ?>" id="qrcode">
											<button type="button" class="button" onclick="aliziUpload('#alizi-qrcode')">上传图片</button>
											<input type="file" class="hidden" id="alizi-qrcode"  onchange="uploadImg('#alizi-qrcode','#qrcode');" />
											<span class="ui-validityshower-info">（收款二维码）</span>
										</div>
									</div>
								</td>
							</tr>

							<tr>
								<th>优惠：</th>
								<td>
									购买 <input name="buy_num" class="ui-text" size="20" type="text" value="<?php echo ($info["buy_num"]); ?>"> 件，
									优惠 <input name="buy_num_decrease" class="ui-text" size="40" type="text" value="<?php echo ($info["buy_num_decrease"]); ?>">元（*折）
								</td>
							</tr>
							
							<tr>
								<th>货到付款预付金：</th>
								<td>
									<input name="deposit" class="ui-text" size="20" type="number" value="<?php echo ($info["deposit"]); ?>">
									<select name="deposit_payment">
										<option value="0">选择支付方式</option>
										<?php if(is_array($payment)): $i = 0; $__LIST__ = $payment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(!$vo['onlinepay']){continue;}$selected = $key==$info['deposit_payment']?'selected':''; ?>
										<option value="<?php echo ($key); ?>" <?php echo ($selected); ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
									不需要预付款请留空
								</td>
								
							</tr>

							<tr>
								<th><?php echo lang('item_package_colon');?></th>
								<td>
									<div>
										<input type="button" class="ui-button" value="<?php echo lang('add_package');?>" onclick="itemAdd()" />
										<input type="text" class="ui-text" name="params_name" placeholder="<?php echo lang('package_name');?>" value="<?php echo ($info["params_name"]); ?>" />
										<select name="params_type">
											<option value="radio">单选项</option>
											<option value="select" <?php if(($info["params_type"]) == "select"): ?>selected="selected"<?php endif; ?>>下拉框</option>
											<option value="checkbox" <?php if(($info["params_type"]) == "checkbox"): ?>selected="selected"<?php endif; ?>>多选项</option>
											<option value="checkbox" <?php if(($info["params_type"]) == "checkbox"): ?>selected="selected"<?php endif; ?>>多选项</option>
											<option value="group" <?php if(($info["params_type"]) == "group"): ?>selected="selected"<?php endif; ?>>组合项</option>
										</select>
									</div>
									<div class="item-list">
										<table class="table-detail table-params" width="80%">
											<tr><th style="width:40px;">排序</th><th>名称</th><th>价格</th><th style="width:300px;">图片</th><th class="qrcode <?php if(!empty($info["qrcode_pay"])): ?>show<?php endif; ?>" style="width:200px;">二维码</th><th style="width:50px">操作</th></tr>
											<?php if(!empty($info["params"])): if(is_array($info["params"])): $i = 0; $__LIST__ = $info["params"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr id="params-<?php echo ($vo["id"]); ?>">
													<td><input name="params[<?php echo ($vo["id"]); ?>][sort_order]" type="text" class="ui-text" value="<?php echo ($vo["sort_order"]); ?>" style="width:100%"></td>
													<td><input name="params[<?php echo ($vo["id"]); ?>][id]" type="hidden" value="<?php echo ($vo["id"]); ?>"><input name="params[<?php echo ($vo["id"]); ?>][title]" type="text" class="ui-text" value="<?php echo ($vo["title"]); ?>" style="width:100%"></td>
													<td><input name="params[<?php echo ($vo["id"]); ?>][price]" type="text" class="ui-text" value="<?php echo ($vo["price"]); ?>" size="4"></td>
													<td><input name="params[<?php echo ($vo["id"]); ?>][image]" type="text" class="ui-text" value="<?php echo ($vo["image"]); ?>" id="image-<?php echo ($key); ?>" size="30" style="float:left;"><button type="button" class="button" onclick="aliziUpload('#alizi-image-<?php echo ($key); ?>')">上传</button><input type="file" class="hidden" id="alizi-image-<?php echo ($key); ?>"  onchange="uploadImg('#alizi-image-<?php echo ($key); ?>','#image-<?php echo ($key); ?>');" /></td>
													<td class="qrcode <?php if(!empty($info["qrcode_pay"])): ?>show<?php endif; ?>"><input name="params[<?php echo ($vo["id"]); ?>][qrcode]" type="text" class="ui-text validate[required]" value="<?php echo ($vo["qrcode"]); ?>" id="qrcode-<?php echo ($key); ?>" size="15" style="float:left;"><button type="button" class="button" onclick="aliziUpload('#alizi-qrcode-<?php echo ($key); ?>')">上传</button><input type="file" class="hidden" id="alizi-qrcode-<?php echo ($key); ?>"  onchange="uploadImg('#alizi-qrcode-<?php echo ($key); ?>','#qrcode-<?php echo ($key); ?>');" /></td>
													<td><input type="button" class="ui-button" value="<?php echo lang('delete');?>" onclick="itemDel($(this))" /></td>
												</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
										</table>
									</div>
								</td>
							</tr>
							<tr>
								<th>商品属性：</th>
								<td>
									<p>
										<input type="button" class="ui-button" value="添加属性" onclick="itemForm()" />
									</p>
									<div class="extend-list">
										<table class="table-detail table-extends" width="80%">
											<tr><th style="width:75px">选择类型</th><th style="width:85px">名称</th><th>内容项目</th><th style="width:50px">操作</th></tr>
											<?php if(!empty($info["extends"])): if(is_array($info["extends"])): $i = 0; $__LIST__ = $info["extends"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $kk = $key;$show = in_array($vo['type'],array('radio','checkbox','select'))?'show':''; ?>
													<tr class="extends-tr">
														<td><select name='extend[<?php echo ($key); ?>][type]' onchange="params( $(this), $(this).val())"><option value='text' <?php if(($vo["type"]) == "text"): ?>selected<?php endif; ?>>文本框</option><option value='radio' <?php if(($vo["type"]) == "radio"): ?>selected<?php endif; ?>>单选项</option><option value='checkbox' <?php if(($vo["type"]) == "checkbox"): ?>selected<?php endif; ?>>多选项</option><option value='select' <?php if(($vo["type"]) == "select"): ?>selected<?php endif; ?>>下拉框</option><option value='password' <?php if(($vo["type"]) == "password"): ?>selected<?php endif; ?>>密码框</option></select></td>
														
														<td><input name="extend[<?php echo ($key); ?>][title]" type="text" class="ui-text" value="<?php echo ($vo["title"]); ?>" size="8"></td>
														<td class="<?php echo ($show); ?>">
															<?php if(is_array($vo["value"])): $k = 0; $__LIST__ = $vo["value"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$li): $mod = ($k % 2 );++$k; $k2 = $kk.$k; ?>
															<div class="extends-list">
																<input name="extend[<?php echo ($kk); ?>][value][]" value="<?php echo ($li); ?>" type="text" class="ui-text" style="float:left;width:40%"> <p class="item_params"  style="float:left;width:55%"><input name="extend[<?php echo ($kk); ?>][image][]" value="<?php echo ($vo['image'][$k-1]); ?>" type="text" class="ui-text" id="extend_img-<?php echo ($k2); ?>" size="20" style="float:left;"><button type="button" class="button" onclick="aliziUpload('#extend-<?php echo ($k2); ?>')">上传图片</button><input type="file" class="hidden" id="extend-<?php echo ($k2); ?>" onchange="uploadImg('#extend-<?php echo ($k2); ?>','#extend_img-<?php echo ($k2); ?>');" /> <input type="button" class="ui-button" value="添加" onclick="addParams($(this),<?php echo ($kk); ?>,<?php echo ($kk); ?>)" /><input type="button" class="ui-button" value="删除" onclick="delParams( $(this))" /></p>
															</div><?php endforeach; endif; else: echo "" ;endif; ?>
														</td>
														
														<td><input type="button" class="ui-button" value="<?php echo lang('delete');?>" onclick="itemDel($(this))" /></td>
													</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
										</table>

									</div>
								</td>
							</tr>
							<tr>
								<th><?php echo lang('运费模板_colon');?></th>
								<td>
									<select name="shipping_id" id="shipping_id">
										<option value="0">卖家承担运费</option>
										<?php if(is_array($shipping)): $i = 0; $__LIST__ = $shipping;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $info["shipping_id"]): ?>selected="selected"<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
									<button type="button" class="btn" onclick="shipping()">添加模板</button>
									<a href="<?php echo U('Setting/shipping');?>">管理运费模板</a>
								</td>
							</tr>
						
						</tbody>
					</table>
				</div><!--.info-block-->
				
				<div class="info-block" style="display:none;">
					<table class="info-table">
						<tbody>
							<tr>
								<th>封面图片：</th>
								<td>
									<input name="image" id="image" type="text" class="ui-text" value="<?php echo ($info["image"]); ?>" size="60" style="float:left">
									<button type="button" class="button" onclick="aliziUpload('#alizi-image')">上传图片</button>
									<input type="file" class="hidden" id="alizi-image"  onchange="uploadImg('#alizi-image','#image');" />
									<a id="alizi-image" href="<?php echo (imageurl($info["image"])); ?>" target="_blank">【查看图片】</a>
									<span class="ui-validityshower-info" style="margin-left:15px;">推荐宽高：500x500</span>
								</td>
							</tr>
							<!--tr>
								<th>手机封面：</th>
								<td>
									<input name="thumb" id="thumb" type="text" class="ui-text" value="<?php echo ($info["thumb"]); ?>" size="80" style="float:left">
									<button type="button" class="button" onclick="aliziUpload('#alizi-thumb')">上传图片</button>
									<input type="file" class="hidden" id="alizi-thumb"  onchange="uploadImg('#alizi-thumb','#thumb');" />
									<span class="ui-validityshower-info" style="margin-left:15px;">推荐宽高：375x300</span>
								</td>
							</tr-->
							<tr>
								<th>幻灯片：</th>
								<td class="slideshow">
									<button type="button" class="button" onclick="aliziUpload('#alizi-slideshow')">上传图片</button>
									<input type="file" class="hidden" id="alizi-slideshow"  onchange="uploadImg('#alizi-slideshow','#slideshow',true);" />
									<input type="hidden" name="slideshow" value="<?php echo ($info["slideshow"]); ?>" />
									<div id="slideshow">
										<?php if(!empty($info["slideshow"])): $_result=explode(',',$info['slideshow']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><p class="img" ><img src="<?php echo imageUrl($vo);?>"><span class="del" onclick="imgDel($(this),'<?php echo ($vo); ?>')">删除</span></p><?php endforeach; endif; else: echo "" ;endif; endif; ?>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!--.info-block-->
							 
				
				<div class="info-block" style="display:none;">
					<table class="info-table">
						<tbody>
							
							<?php $aliziConfig = S('aliziConfig'); ?>
							<tr>
								<th><?php echo lang('sms_send_colon');?></th>
								<td>
									<input name="sms_send[0][status]" type="checkbox" value="1" <?php if(!empty($info['sms_send'][0]['status'])): ?>checked="checked"<?php endif; ?> onclick="isShow(this,'.sms_send_0')">
									<span class="ui-validityshower-info">下单通知</span>
									
									<input name="sms_send[1][status]" type="checkbox" value="1" <?php if(!empty($info['sms_send'][1]['status'])): ?>checked="checked"<?php endif; ?> onclick="isShow(this,'.sms_send_1')">
									<span class="ui-validityshower-info">支付通知</span>
									
									<input name="sms_send[3][status]" type="checkbox" value="1" <?php if(!empty($info['sms_send'][3]['status'])): ?>checked="checked"<?php endif; ?> onclick="isShow(this,'.sms_send_3')">
									<span class="ui-validityshower-info">发货通知</span>

									<style>.tags span{margin:0 5px;color:#06c;}</style>
									<p class="tags">以下标签将替换为实际发送的内容：<br>标题<span>#title#</span>，订单编号<span>#orderNum#</span>，套餐<span>#params#</span>，姓名<span>#name#</span>，手机<span>#mobile#</span>，数量<span>#quantity#</span>，价格<span>#price#</span>，快递名称<span>#express#</span>，快递单号<span>#expressNum#</span>，确认网址<span>#confirmUrl#</span></p>
								</td>
							</tr>
							<tr class="smsSend sms_send_0 <?php if(!empty($info['sms_send'][0]['status'])): ?>show<?php endif; ?>">
								<th><?php echo lang('下单通知内容_colon');?></th>
								<td>
									<textarea name="sms_send[0][content]" class="input-textarea" cols="125" rows="3"><?php if(!empty($info['sms_send'][0]['content'])): echo ($info['sms_send'][0]['content']); else: ?>#name#，您已成功订购#title#，数量#quantity#件，我们将尽快安排发货，感谢支持<?php endif; ?></textarea>
								</td>
							</tr>
							<tr class="smsSend sms_send_1 <?php if(!empty($info['sms_send'][1]['status'])): ?>show<?php endif; ?>">
								<th><?php echo lang('支付通知内容_colon');?></th>
								<td>
									<textarea name="sms_send[1][content]" class="input-textarea" cols="125" rows="3"><?php if(!empty($info['sms_send'][1]['content'])): echo ($info['sms_send'][1]['content']); else: ?>#name#，您已成功订购#title#，价格#price#元，我们将尽快安排发货，敬请留意<?php endif; ?></textarea>
								</td>
							</tr>
							<tr class="smsSend sms_send_3 <?php if(!empty($info['sms_send'][3]['status'])): ?>show<?php endif; ?>">
								<th><?php echo lang('发货通知内容_colon');?></th>
								<td>
									<textarea name="sms_send[3][content]" class="input-textarea" cols="125" rows="3"><?php if(!empty($info['sms_send'][3]['content'])): echo ($info['sms_send'][3]['content']); else: ?>#name#，您订购的#title#，已发#express#，单号#expressNum#，请注意查收<?php endif; ?></textarea>
								</td>
							</tr>
							
							<tr>
								<th>自动发货：</th>
								<td>
									<input name="is_auto_send" id="is_auto_send" type="checkbox" value="1" <?php if(!empty($info["is_auto_send"])): ?>checked="checked"<?php endif; ?> onclick="isShow(this,'.inform')">
									<span class="ui-validityshower-info">（选择自动发货，则用户支付后将自动发送以下内容到用户邮箱，并显示在支付成功页面）</span>
								</td>
							</tr>
							<tr class="inform <?php if(!empty($info["is_auto_send"])): ?>show<?php endif; ?>">
								<th><?php echo lang('发送内容_colon');?></th>
								<td>
									<textarea name="send_content" id="send_content" class="input-textarea" cols="125" rows="3"><?php echo ($info["send_content"]); ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!--.info-block-->
				
				
				
				<div class="info-block" style="display:none;">
					<table class="info-table">
						<tbody>
							
							
							<?php if(C('CURRENT_LANG') != 'zh-cn'): ?><tr>
								<th>Facebook像素：</th>
								<td>
									<input name="facebook_pixel_id" type="text" class="ui-text" value="<?php echo ($info["facebook_pixel_id"]); ?>" size="60">
									<span class="ui-validityshower-info">只须填写像素ID，不须填写整段代码，多个像素请用英文逗号隔开</span>
								</td>
							</tr><?php endif; ?>
							
							
							<tr>
								<th>head基础代码：</th>
								<td>
									<p><span class="ui-validityshower-info">该代码将加入到head头部，不会显示在页面上</span></p>
									<textarea name="header" class="input-textarea" cols="125" rows="2"><?php echo ($info["header"]); ?></textarea>
								</td>
							</tr>
							<tr>
								<th>事件代码：</th>
								<td>
									<p><span class="ui-validityshower-info">提交成功后执行，只能添加javascript代码，不能加&lt;javascript&gt;&lt;/javascript&gt;标签</span></p>
									<textarea name="javascript" id="javascript" class="input-textarea" cols="125" rows="2"><?php echo ($info["javascript"]); ?></textarea>
								</td>
							</tr>
					
							
							<?php if($aliziConfig['ipcloak_status'] == 1): ?><tr style="margin-top:10px;background-color:#EEE;border-bottom:1px solid #eee;border-bottom:1px solid #ccc;">
								<th colspan="2" style="text-align:center;">IPCloak</th>
							</tr>
							<tr>
								<th>黑五或仿品页：</th>
								<td>
									<input name="purchase_url" type="text" class="ui-text" value="<?php echo ($info["purchase_url"]); ?>" size="50">
									<span class="ui-validityshower-info">填写黑五或仿品页<b style="color:#F00">商品编号</b>或完整URL地址。</span>
									<span style="color:#f00">广告投放的国家/地区将显示该页面！</span>
								</td>
							</tr>
							<tr>
								<th>国家/地区代码：</th>
								<td>
									<input name="ipcloak_countries" type="text" class="ui-text" value="<?php echo ($info["ipcloak_countries"]); ?>" size="50">
									广告投放的国家/地区代码！
									<span class="ui-validityshower-info"><a href="http://www.wxrob.com/alizi/country.htm" target="_blank">【查看代码】</a>，多个代码请用+号分开，如：CN+TW+US。</span>
								</td>
							</tr><?php endif; ?>
						</tbody>
					</table>
				</div><!--.info-block-->
				
			
				
				<div class="info-block" style="display:none;">
					<div style="padding:8px;font-size:14px;margin-bottom: 1rem;text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);background-color: #fbeedf;border: 1px solid #eed3d7;border-radius: 4px;color: #c00;line-height: 1.5rem;">注意：以下设置只针对单页有效</div>
					
					<table class="info-table">
						<tbody>
							<tr>
								<th><b class="verifing">*</b>模板选择：</th>
								<td>
									<select name="template" onchange="changeTheme('Alizi/'+this.value)">
										<option value="">请选择模板</option>
										<?php if(!empty($custom)): if(is_array($custom)): $i = 0; $__LIST__ = $custom;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($temp["template"]) == $vo["id"]): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
										</optgroup><?php endif; ?>
									</select>
									<select name="theme">
										<?php $template = C('ALIZI_TEMPLATE'); ?>
										<?php if(is_array($template)): $i = 0; $__LIST__ = $template;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($temp["theme"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th>模板宽度：</th>
								<td>	
									<input type="text" class="ui-text" name="width" size="6" value="<?php if(empty($temp["width"])): ?>750px<?php else: echo ($temp["width"]); endif; ?>">
									<span class="ui-validityshower-info">（单位px或%）</span>
								</td>
							</tr>
							<!--tr>
								<th>边距宽度：</th>
								<td>	
									<input type="text" class="ui-text" name="padding" size="6" value="<?php if(empty($extend["padding"])): ?>0<?php else: echo ($extend["padding"]); endif; ?>">
									<span class="ui-validityshower-info">（单位px）</span>
								</td>
							</tr>
							<tr>
								<th>表单滚动信息</th>
								<td>
									<select name="show_notice">
										<?php echo status($temp['show_notice'],'select','0:不显示;1:下方显示;2:右侧显示','show_notice');?>
									</select>

								</td>
							</tr-->
							<tr>
								<?php $color = json_decode($temp['color'],true); ?>
								<th><b class="verifing">*</b><?php echo lang('模板颜色_colon');?></th>
								<td class="colors">
									<?php if(is_array($deaultColor)): $i = 0; $__LIST__ = $deaultColor;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><label class="ui-group-label"><?php echo (lang($key)); ?><input type="text" id="color_<?php echo ($key); ?>" name="color[<?php echo ($key); ?>]" size="3" class="jscolor" value="<?php if(empty($color)): ?>#<?php echo ($value); else: echo ($color[$key]); endif; ?>"></label><?php endforeach; endif; else: echo "" ;endif; ?>
									<button type="button"class="ui-button" onclick="resetColor()" />重置</button>	
								</td>
							</tr>
							<tr>
								<th><b class="verifing">*</b><?php echo lang('表单选项_colon');?></th>
								<td>
									<?php if(is_array($options)): $i = 0; $__LIST__ = $options;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><label><input name="options[]" type="checkbox" value="<?php echo ($key); ?>" <?php if(!empty($vo["checked"])): ?>checked<?php endif; ?>><?php echo ($vo["name"]); ?></label>&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
								</td>
							</tr>
							
							<tr>
								<th>底部导航内容<br><a href="javascript:;" onclick="showExample()">查看示例</a></th>
								<td>
									<p class="bottom_nav_list-1"><input type="text" class="ui-text"  size="100" name="bottom_nav_list[1]" value="<?php echo ($extend['bottom_nav_list'][1]); ?>" ></p>
									<p class="bottom_nav_list-2"><input type="text" class="ui-text"  size="100" name="bottom_nav_list[2]" value="<?php echo ($extend['bottom_nav_list'][2]); ?>" ></p>
									<p class="bottom_nav_list-3"><input type="text" class="ui-text"  size="100" name="bottom_nav_list[3]" value="<?php echo ($extend['bottom_nav_list'][3]); ?>" ></p>
								</td>
							</tr>
		
							
							<tr>
								<th>返回页面</th>
								<td> 
									<input name="redirect_uri" class="ui-text" size="50" id="redirect_uri" type="text" value="<?php echo ($temp["redirect_uri"]); ?>" >
									<span class="ui-validityshower-info">（下单成功后点击返回页面）</span>
								</td>
							</tr>
									
						</tbody>
					</table>
				</div><!--.info-block-->
				
				<table class="info-table">
					<tbody>
						<tr>
							<th>&nbsp;</th>
							<td>
								<?php if(!empty($_GET['id']) && $_GET['do'] != 'copy'): ?><input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" /><?php endif; ?>
								<?php if($_GET['do'] == 'copy'): ?><input type="hidden" name="item_id" value="<?php echo ($info["id"]); ?>" /><?php endif; ?>
								<input type="hidden" name="user_id" value="<?php echo ($_SESSION["user"]["id"]); ?>" />
								<input type="hidden" type="show_notice" value="0" />
								<input type="submit" class="btn btn-ok" value="<?php echo lang('confirm');?>" />
								<a class="btn" href="<?php if(empty($_GET["id"])): echo U('Item/index'); else: echo ($_SERVER['HTTP_REFERER']); endif; ?>"><?php echo lang('goback');?></a>
								<?php if(!empty($_GET["id"])): ?><a class="btn" href="index.php?m=Order&id=<?php echo ($info["sn"]); ?>&tpl=detail" target='_blank'>单页预览</a><?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>	
            </form>
        </div>  
    </div><!--.box-->
<link href="__PUBLIC__/Assets/js/validation/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/Assets/js/validation/jquery.validationEngine.js"></script>
<script type="text/javascript" src="__PUBLIC__/Assets/js/validation/jquery.validationEngine-zh_CN.js"></script>
<script type="text/javascript">
$(function(){
	var index = $(window.location.hash).index();
	$('#CooperationMain').aliziTabs({selected: index>0?index:0});	 

	$("#ajaxform").validationEngine('attach', {promptPosition : "centerRight", autoPositionUpdate : true}); 
    $('#ajaxform').ajaxForm({
        timeout: 15000,
        error:function(){ $('#ajaxLoading').hide();alert("<?php echo lang('ajaxError');?>");},
        beforeSubmit:function(){ $('#ajaxLoading').show();},
        success:function(data){ 
            $('#ajaxLoading').hide();
            if(data.status==1){
                var redirectURL = "<?php if(($_GET["do"]) == "edit"): ?>#<?php else: echo U('Item/index'); endif; ?>";
                $.alert(data.info,data.status,function(){window.location.href=redirectURL});
				$.get("index.php?m=Order&tpl=detail&buildHtml=H5&id=<?php echo ($info['sn']); ?>");
            }else{
                $.alert(data.info,data.status);
            }
        },
        dataType: 'json'
    });
});

function qrcodepay(id){
	id = parseInt(id);
	if(id>0){$('.qrcode').addClass('show');}else{$('.qrcode').removeClass('show');}
}
function itemAdd(){
	var show = $('#qrcode_pay').val()>0?' show':'';
	var params_type = $('#params_type').val()=='group'?' show':'';
	var rand = 'Add-'+new Date().getTime();
    //var item = '<tr><td><input name="title[]" type="text" class="ui-text" value="<?php echo ($vo["title"]); ?>" style="width:100%"></td><td><input name="params_price[]" type="text" class="ui-text" value="<?php echo ($vo["price"]); ?>" size="4"></td><td><input name="params_image[]" type="text" class="ui-text" value="<?php echo ($vo["image"]); ?>" id="image-'+rand+'" size="25" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#ali-image-'+rand+'\')">上传</button><input type="file" class="hidden" id="ali-image-'+rand+'" onchange="uploadImg(\'#ali-image-'+rand+'\',\'#image-'+rand+'\');" /></td><td class="qrcode '+show+'"><input name="params_qrcode[]" type="text" class="ui-text validate[required]" value="<?php echo ($vo["qrcode"]); ?>" id="qrcode-'+rand+'" size="15" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#ali-qrcode-'+rand+'\')">上传</button><input type="file" class="hidden" id="ali-qrcode-'+rand+'" onchange="uploadImg(\'#ali-qrcode-'+rand+'\',\'#qrcode-'+rand+'\');" /></td><td><input type="button" class="ui-button" value="<?php echo lang("delete");?>" onclick="itemDel($(this))" /></td></tr>';
    var item = '<tr id="params-'+rand+'"><td><input name="params['+rand+'][sort_order]" type="text" class="ui-text" value="" style="width:100%"></td><td><input name="params['+rand+'][id]" type="hidden" value=""><input name="params['+rand+'][title]" type="text" class="ui-text" value="" style="width:100%"></td><td><input name="params['+rand+'][price]" type="text" class="ui-text" value="" size="4"></td><td><input name="params['+rand+'][image]" type="text" class="ui-text" value="" id="image-'+rand+'" size="30" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#ali-image-'+rand+'\')">上传</button><input type="file" class="hidden" id="ali-image-'+rand+'" onchange="uploadImg(\'#ali-image-'+rand+'\',\'#image-'+rand+'\');" /></td><td class="qrcode '+show+'"><input name="params_qrcode[]" type="text" class="ui-text validate[required]" value="" id="qrcode-'+rand+'" size="15" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#ali-qrcode-'+rand+'\')">上传</button><input type="file" class="hidden" id="ali-qrcode-'+rand+'" onchange="uploadImg(\'#ali-qrcode-'+rand+'\',\'#qrcode-'+rand+'\');" /></td><td><input type="button" class="ui-button" value="<?php echo lang("delete");?>" onclick="itemDel($(this))" /></td></tr>';
	$('.table-params').append(item);
}
function itemDel(obj){
	obj.parent().parent().remove();
}
function itemForm(){
	var rand = new Date().getTime();
	var select = '<select name="extend['+rand+'][type]" onchange="params( $(this), $(this).val())"><option value="text">文本框</option><option value="radio">单选项</option><option value="checkbox">多选项</option><option value="select">下拉框</option><option value="password">密码框</option></select>';
	var params = '<div class="extends-list"><input name="extend['+rand+'][value][]" type="text" class="ui-text" style="float:left;width:40%"> <p class="item_params"  style="float:left;width:55%"><input name="extend['+rand+'][image][]" type="text" class="ui-text" id="extend_img-'+rand+'" size="20" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#extend-'+rand+'\')">上传图片</button><input type="file" class="hidden" id="extend-'+rand+'" onchange="uploadImg(\'#extend-'+rand+'\',\'#extend_img-'+rand+'\');" /> <input type="button" class="ui-button" value="添加" onclick="addParams($(this),'+rand+','+rand+')" /><input type="button" class="ui-button" value="删除" onclick="delParams( $(this))" /></p></div>';
    var item = '<tr id="items-'+rand+'"><td>'+select+'</td><td><input name="extend['+rand+'][title]" type="text" class="ui-text btn-delete" value="" size="8"></td> <td> '+params+'</td><td><input type="button" class="ui-button" value="<?php echo lang("delete");?>" onclick="itemDel($(this))" /></td></tr>';
	$('.table-extends').append(item);
}
function params(_this,val){
	if(val=='radio' || val=='checkbox' || val=='select'){
		_this.parent('td').siblings('td').addClass('show');
	}else{
		_this.parent('td').siblings('td').removeClass('show');
	}
}
function addParams(_this,key,id){
	var rand = new Date().getTime();
	var params = '<div class="extends-list"><input name="extend['+id+'][value][]" type="text" class="ui-text" style="float:left;width:40%"> <p class="item_params"  style="float:left;width:55%"><input name="extend['+id+'][image][]" type="text" class="ui-text" id="extend_img-'+rand+'" size="20" style="float:left;"><button type="button" class="button" onclick="aliziUpload(\'#extend-'+rand+'\')">上传图片</button><input type="file" class="hidden" id="extend-'+rand+'" onchange="uploadImg(\'#extend-'+rand+'\',\'#extend_img-'+rand+'\');" /> <input type="button" class="ui-button" value="添加" onclick="addParams($(this),'+rand+','+id+')" /><input type="button" class="ui-button" value="删除" onclick="delParams( $(this))" /></p></div>';
	_this.parent().parent().parent().append(params);
}
function delParams(_this){
	var li = _this.parent().parent(),len=li.siblings('.extends-list').length;
	if(len>=1){
		li.remove();
	}else{
		alert('删除失败，至少保留一项');
	}
	
}


function isShow(_this,target){
	var target = $(target);
	if(_this.checked==true){
		target.addClass('show');
	}else{
		target.removeClass('show');
	}
}
function shipping(id){
	var url = "?m=Shipping&a=edit&page=2";
	$.open(url,{title:'运费模板',width:680,height:250});
}
</script>
 
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/Assets/js/ueditor-1.4.3/ueditor.config.js?v=3.2"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/Assets/js/ueditor-1.4.3/ueditor.all.js?v=3.2"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/Assets/js/ueditor-1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/Assets/js/ueditor-1.4.3/ueditor.alizi.plugin.js?v=3.2"></script>

<script type="text/javascript">
 function setContent(contents,msg) {
     UE.getEditor('content').execCommand('insertHtml', contents);
     $.alert(msg?msg:'添加成功',1);
 }
 function insertHtml() {
     var value = prompt('插入html代码', '');
     UE.getEditor('content').execCommand('insertHtml', value)
 }
var ue = UE.getEditor('content');

</script>

<script type="text/javascript" src="__PUBLIC__/Assets/js/jscolor.min.js"></script>
<script type="text/javascript">

function showExample(){
	top.$.dialog({
		title: '底部导航设置示例',
		content: '<div style="line-height:25px;"><p>立即下单||javascript:scrollto(\'#aliziOrder\');||shopping-cart</p>'
				+ '<p>电话咨询||tel:13888888888||phone-square</p>'
				+ '<p>短信订购||sms:13888888888||envelope</p>'
				+ '<p>订单查询||<?php echo C('ALIZI_ROOT');?>index.php?m=Order&a=query||search</p>'
				+ '<p>微信咨询||javascript:weixin(wx.name,wx.img);||weixin </p>'
				+ '<p>QQ 咨询||http://wpa.qq.com/msgrd?v=3&site=qq&menu=yes&uin=QQ||qq </p>'
				+ '<p>外部链接||http://www.wxrob.com/</p></div>',
		lock: true,
		fixed: true,
		cancelValue: '关闭',
		cancel: function () {},
		button: [
			{
				value: '查看图标',
				callback: function () {
					window.open("http://www.wxrob.com/alizi/icon.htm");
					return false;
				},
				focus: true
			}
		] 
	});
}		
function changeTheme(theme){
	$.getJSON("?m=Item&a=getCustomColor&tpl="+theme, function(color) {
		resetColor(color.data);
	});
}
function resetColor(color){ 
	var color = color?color:<?php echo (json_encode($deaultColor)); ?>;
	for(var key in color){ 
		var fontColor = key=='body_bg' || key=='form_bg'?'#000000':'#FFFFFF';
		$('#color_'+key).val(color[key]).css({"background-color":'#'+color[key],'color':fontColor});
	}
}
</script>
 <?php echo W("Foot");?>