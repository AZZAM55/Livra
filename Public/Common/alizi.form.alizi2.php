<?php

$html = "";
$url = isset($_GET['url'])?$_GET['url']:"http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$show_notice = $template['show_notice']<2?'alizi-full-row':'';
$user_id = isset($cookie['uid'])?$cookie['uid']:$_SESSION['user']['id'];
$userInfo = M('User')->where(array('id'=>$user_id))->field('pid,role')->find();
$user_pid = $userInfo['role']=='member'?$userInfo['pid']:$user_id;
$referer  = isset($_GET['buildHtml'])?'':$_SERVER['HTTP_REFERER'];
$item_index = 0;
$package = !empty($info['params_name'])?$info['params_name']:lang('itemPackage');
$symbol = $aliziConfig['symbol']?$aliziConfig['symbol']:lang('symbol');

$agent_discount = 100;
if($user_id){
	$UserGroup = M('UserGroup');
	$discount = $UserGroup ->where(array('id'=>$userInfo['group_id']))->getField('discount');
	$agent_discount = $discount&&intval($discount)>0?$discount:$agent_discount;
}

$template['theme'] = $template['theme']?$template['theme']:'thin';
$html .= "<link href='".C('ALIZI_ROOT')."Public/Alizi/theme/{$template['theme']}/alizi.css?v=".ALIZI_VERSION."' rel='stylesheet'>";

$color = json_decode($template['color'],true);

$html .= "<style>.alizi-border,.alizi-side.alizi-full-row{border-color:#{$aliziConfig['theme_color']};}";
if(isset($template['width'])){ $html .= ".alizi-detail-wrap{ max-width:{$template['width']};}"; }
if($color){
    $html .= "body,.alizi-order-wrap{background-color:#{$color['body_bg']};}.alizi-detail-content h2{border-top-color:#{$color['body_bg']};}.alizi-border,.alizi-side.alizi-full-row{border-color:#{$color['border']};}.alizi-detail-header dt{color:#{$color['font']};}.alizi-order{color:#{$color['font']};background-color:#{$color['form_bg']};}.alizi-title{background-color:#{$color['title_bg']};}.alizi-btn,.alizi-btn:hover, .alizi-btn:active,.alizi-badge,.alizi-params.active,.alizi-group-box input:checked + label:after{background-color:#{$color['button_bg']};border-color:#{$color['button_bg']};}.alizi-foot-nav{background-color:#{$color['nav_bg']};}.alizi-group.alizi-params.alizi-checkbox.active:hover{background-color:#{$color['button_bg']} !important;border-color:#{$color['button_bg']} !important;color:#fff !important;}";
}
$html .= "</style>";

$html .= "<div class='alizi-order alizi-border alizi-lang-".C('DEFAULT_LANG')." clearfix' id='aliziOrder'><div class='alizi-main alizi-border {$show_notice}'><div class='alizi-title alizi-title-order alizi-border ellipsis'><i class='icon-cart'></i>{$info['name']}</div>";
		
		$html .= "<div class='alizi-form-content alizi-border'><form action='".C('ALIZI_ROOT')."index.php?m=Order&a=aliziBooking' method='post' id='aliziForm'><input type='hidden' name='lang' value='".C('DEFAULT_LANG')."'><input type='hidden' name='user_id' value='{$user_id}'><input type='hidden' name='user_pid' value='{$user_pid}'><input type='hidden' name='sn' value='{$info['sn']}'><input type='hidden' name='item_id' value='{$info['id']}'><input type='hidden' name='item_name' value='{$info['name']}'><input type='hidden' name='item_price' id='item_price' value='".($product?$product[0]['price']:$info['price'])."'><input type='hidden' name='url' value='{$url}'><input type='hidden' name='redirect' value='".($template['redirect_uri']?$template['redirect_uri']:$url)."'><input type='hidden' name='referer' value='{$referer}'><input type='hidden' name='order_page' value='{$request['page']}'><input type='hidden' name='channel_id' value='{$cookie['ac']}'><input type='hidden' name='qrcode_pay' value='{$info['qrcode_pay']}'><input type='hidden' name='math' value='{$paymentDefault['math']}'><input type='hidden' name='page' value='{$page}'><input type='hidden' name='alizi_token' value='{$token}'><input type='hidden' name='buy_num' value='{$info['buy_num']}'><input type='hidden' name='buy_num_decrease' value='{$info['buy_num_decrease']}'><input type='hidden' name='min_num' value='{$info['min_num']}'><input type='hidden' name='max_num' value='{$info['max_num']}'><input type='hidden' name='coupon_value' value='0'><input type='hidden' name='deposit' value='{$info['deposit']}'><input type='hidden' name='deposit_payment' value='{$info['deposit_payment']}'>";
				

		if(!empty($template['info']) && $request['page'] == 'single'){
			$html .= "<div class='alizi-brief clearfix'>{$template['info']}</div>";
		}
		if(!empty($product)){
			$product_html = "<div class='alizi-box' id='alizi-box-product'><div class='alizi-rows clearfix rows-id-params rows-id-params-{$info['params_type']} alizi-{$info['params_type']}'><label class='rows-head'>".$package.lang('request')."</label><div class='rows-params'>";
			$selected = isset($_GET['selected'])&&$_GET['selected']>0?$_GET['selected']:1;
			switch ($info['params_type']) {
				case 'select':
					$product_html .= "<select class='alizi-params-change' name='item_params[]' alizi-fx='alizi.quantity' alizi-fx-params='0'>";
						foreach($product as $vo){
							$title = explode('||',$vo['title']);
							$product_html .= "<option value='{$vo['title']}'".($i==$selected?' selected ':'')." value-price='{$vo['price']}'>{$title[0]}</option>";
						}
					$product_html .= "</select>";
				break;
				case 'group':
					$i=0;
					foreach($product as $vo){
						$i++;
						$product_html .= "<div class='alizi-groups' id='group-{$vo['id']}' data-id='{$vo['id']}'>";
						$product_html .= "<label class='item-label'>{$vo['title']}</label><input type='hidden' name='params[{$vo['id']}][title]' value='{$vo['title']}' ".($i==$selected?'checked':'')."><input type='hidden' name='params[{$vo['id']}][price]' value='{$vo['price']}' id='price-{$vo['id']}'>";
						$product_html .=  "<div class='alizi-quantity-group item-num'><a class='quantity-dec' href='javascript:;' onclick='alizi.num(-1,\"{$vo['id']}\",\"{$vo['price']}\")'>-</a><input type='tel' class='alizi-quantity' size='5' value='".($i==$selected?$info['min_num']:0)."' name='params[{$vo['id']}][num]' onkeyup='alizi.num(0,\"{$vo['id']}\",\"{$vo['price']}\")' id='num-{$vo['id']}'><a class='quantity-inc' href='javascript:;' onclick='alizi.num(1,\"{$vo['id']}\",\"{$vo['price']}\")'>+</a></div><strong class='item-price' id='item-price-{$vo['id']}'>".$symbol."<span>{$vo['price']}</span></strong></div>";
						$product_html .= '<style>.rows-id-quantity{display:none}</style>';
					}
				break;
				default:
					$i=0;
					foreach($product as $vo){
						$i++;
						$title = explode('||',$vo['title']);
						$product_html .= "<label alizi-value='{$vo['price']}' alizi-target='#item_price' alizi-fx='alizi.quantity' alizi-fx-params='0' class='".($vo['image']?' alizi-params-image':' alizi-params-text')." alizi-params  alizi-{$info['params_type']}".($i==$selected?' active ':'')."' title='{$title[0]}'>";
						if($vo['image']){
							$product_html .= "<p class='item-image'><img src='".imageUrl($vo['image'])."' /></p>";
						}
						$product_html .= "<p class='item-desc'><input type='{$info['params_type']}' name='item_params[]' value='{$vo['title']}' ".($i==$selected?'checked':'').">{$title[0]}</p></label>";
					}
				break;
			}
			$product_html .= "</div></div></div><!--.alizi-box-->";
		}
		
		if(!empty($extends)){
			$extends_html = "<div class='alizi-box' id='alizi-box-extends'>";
			foreach($extends as $k=>$vo){
				$disabled  = '';
				$level = '';
				if(strstr($vo['title'],'#')){
					list($index,$vo['title'])= explode('#',$vo['title'],2);
					$item_index =1;
					$level = "1";
					if($index==1){
						$disabled = '';
						$class = "extends extends-{$index}";
					}else{
						$disabled = 'disabled';
						$class = "extends extends-hidden extends-{$index}";
					}
				}
				
				if(strstr($vo['title'],'#')){
					list($index2,$vo['title'])= explode('#',$vo['title'],2);
					
					if($index2==1){
						$disabled = '';
						$class2 = "subextends subextends-{$index2}";
					}else{
						$disabled = 'disabled';
						$class2 = "subextends subextends-hidden subextends-{$index2}";
					}
					$level = "2";
				}
				
				
				$extends_html .= "<div data-level='{$level}' class='alizi-rows clearfix rows-id-extends {$class} {$class2} extends-level-{$level} alizi-{$vo['type']}'><label class='rows-head'>{$vo['title']}".lang('request')."</label><div class='rows-params'>";
				
				$vo['value'] = is_array($vo['value'])?$vo['value']:explode('#',$vo['value']);
				
				$value = explode('||',$vo['value'][0]);
				switch ($vo['type']) {
					case 'text':
						$extends_html .= "<input type='text' name='extends[{$vo['title']}]' placeholder='{$value[0]}' autocomplete='off' class='alizi-input-text' {$disabled} {$disabled2}>";
					break;
					case 'password':
						$extends_html .= "<input type='password' name='extends[{$vo['title']}]' placeholder='{$value[0]}' autocomplete='off' class='alizi-input-text' {$disabled} {$disabled2}>";
					break;
					case 'select':
						$extends_html .= "<select name='extends[{$vo['title']}]' {$disabled} {$disabled2}>";
						foreach($vo['value'] as $li){
							if(empty($li)){
								$extends_html .= "<option value=''>".lang('pleaseSelect')."</option>";
							}else{
								$value = explode('||',$li);
								$extends_html .= "<option value='{$li}'>{$value[0]}</option>";
							}
						}
						$extends_html .= "</select>";
					break;
					default:
						$i=0;
						$selected = 100;
						foreach($vo['value'] as $k=>$li){
							$i++;
							$hidden = empty($li)?'style="display:none;"':'';
							$value = explode('||',$li);
							$extends_image = trim($vo['image'][$k]);
							if($extends_image ){
								$image_class = ' alizi-params-image ';
								$image = "<p class='item-image'><img src='".imageUrl($extends_image)."' /></p>";
							}else{
								$image = '';
								$image_class = '';
							}
							
							$extends_html .= "<label class='alizi-group alizi-params alizi-{$vo['type']} {$image_class}".($i==$selected?'active':'')."' {$hidden}>{$image}<span class='alizi-group-box'><input alizi-value='{$li}' class='{$vo['type']}-{$k}' id='{$vo['title']}{$key}' name='extends[{$vo['title']}]".($vo['type']=='checkbox'?'[]':'')."' type='{$vo['type']}' value='{$li}' ".($i==$selected?'checked':'')." {$disabled}><label class='selected-icon' for='{$vo['title']}{$key}'></label></span>{$value[0]}</label>";
						}
					break;
				}
				$extends_html .= "</div></div>";	
			}
			$extends_html .= "</div><!--.alizi-box-->";						
		}
		
		
		$alizi_box_begin = "<div class='alizi-box alizi-box-params'>";
		$alizi_box_end = "</div><!--.alizi-box .alizi-box-params-->";
		$i = 0;
		foreach($params as $key=>$vo){
			if(empty($vo['checked'])){ continue;} 
			
			if(!in_array($key,array('product','extends'))){
				if(!strstr($html,$alizi_box_begin)){ $html .= $alizi_box_begin; }
				$html .= "<div class='alizi-rows clearfix rows-id-{$key}'><label class='rows-head'>{$vo['name']}<span class='alizi-request ".($vo['request']?'':'alizi-request-none')."'>*</span></label><div class='rows-params'>";
			} 
			switch ($key) {
				case 'product':
					if(strstr($html,$alizi_box_begin) && !strstr($html,$alizi_box_end)){$html .= $alizi_box_end; }
					$html .= $product_html;
				break;
				case 'extends':
					if(strstr($html,$alizi_box_begin) && !strstr($html,$alizi_box_end)){$html .= $alizi_box_end; }
					$html .= $extends_html;
				break;
				case 'price':
					$html .= "<span class='alizi-shipping' ".($info['shipping_id']?'':"style='display:none;'")."><strong class='alizi-order-price'>0.00</strong>+<strong class='alizi-shipping-price'>0.00</strong>(".lang('shippingPrice').")=</span><span class='alizi-price'>".getPrice($product?$product[0]['price']:$info['price'],true,'alizi-total-price')."</span><strong class='alizi-coupon'></strong><span class='alizi-discount'></span>".$vo['info'];
					$html .= "</div></div><div><div>";
				break;
				case 'payment':
					$html .= "<div class='alizi-payment clearfix'>";
					$i=0;
					$firstPayment =1;
					foreach($payment as $key=>$vo){
						if($key == 5 && empty($info['qrcode_pay'])){ continue;}
						$i++;
						if($i==1) $firstPayment=$key;
						$html .= "<label alizi-value='{$key}' alizi-target=':payment' alizi-fx='alizi.payment' alizi-fx-params='{$key}' class='ellipsis alizi-params alizi-payment-{$key} ".($i==1?'active':'')."'><input type='radio' name='payment' value='{$key}' ".($i==1?'checked':'').">{$vo['name']}</label>";
						if($key==66){
							$html .= '<script type="text/javascript" src="https://h.online-metrix.net/fp/tags.js?org_id=b7rx7nkg&session_id='.session_id().'"></script>';
						}
					}
					$monthRange = range(1,12);
					foreach($monthRange as $m){$m = $m<10?'0'.$m:$m;$month .= "<option value='{$m}'>{$m}</option>";}
					for($i=0;$i<12;$i++){
						$y = date('Y')+$i;
						$year .= "<option value='{$y}'>{$y}</option>";
					}
					$html .= "</div><div id='alizi-creditcard-info' class='clearfix' style=''><input type='text' name='creditcard[num]' class='alizi-input-text creditcard-num' placeholder='".lang('creditCardNum')."'><input type='text' name='creditcard[cvv]' class='alizi-input-text creditcard-cvv' placeholder='".lang('cvv')."'><select name='creditcard[month]' class='creditcard-month'><option value=''>".lang('month')."</option>{$month}</select><select name='creditcard[year]' class='creditcard-year'><option value=''>".lang('year')."</option>{$year}</select></div>";
					$html .= "<div id='alizi-payment-info' class='alizi-alert clearfix' ".($payment[$firstPayment]['info']?'':"style='display:none;'")."><div class='payment-info'>{$payment[$firstPayment]['info']}</div></div>";
				break;
				case 'mobile':
					$html .= "<input type='tel' name='{$key}' placeholder='{$vo['info']}' autocomplete='off' class='alizi-input-text' alizi-request='{$vo['request']}' value='{$cookie[$key]}'>";
				break;
				case 'salenum':
					$html .= "<span class='alizi-salenum'>{$info['salenum']}</span><!--span class='alizi-stock'>(".lang('stock')."{$info['quantity']})</span-->";
				break;
				case 'quantity':
					$html .= "<div class='alizi-quantity-group'><a class='quantity-dec' href='javascript:;' onclick='alizi.quantity(-1)'>-</a><input type='tel' class='alizi-quantity' size='4' value='1' name='quantity' onkeyup='alizi.quantity(0)'><a class='quantity-inc' href='javascript:;' onclick='alizi.quantity(1)'>+</a></div>";
				break;
				case 'datetime':
					$date = date('Y-m-d',strtotime("+0 day"));
					$html .= "<input type='text' name='{$key}' placeholder='{$vo['info']}' autocomplete='off' class='alizi-input-text Wdate' alizi-request='{$vo['request']}' style='width:50%;' onfocus=\"WdatePicker({minDate:'$date'})\" value='{$cookie[$key]}'>
						<script type='text/javascript' src='__PUBLIC__/Assets/js/My97DatePicker/WdatePicker.js'></script>";
				break;
				case 'file':
					$html .= "<input name='file' type='hidden' class='ui-text left' id='file'><button type='button' class='upload-button alizi-input-text alizi-btn' onclick='aliziUpload(\"#alizi-file\")'>{$vo['name']}</button><input type='file' name='file2' autocomplete='off' id='alizi-file' alizi-request='{$vo['request']}'   onchange='uploadImg(\"#alizi-file\",\"#file\")' class='hidden'>";
				break;
				case 'region':
					$regions = "<div class='region-select region-payOnDelivery {$aliziConfig['region']}'><select name='region[province]' id='province' class='alizi-region alizi-region-province' alizi-request='{$vo['request']}'></select><select name='region[city]' id='city' class='alizi-region alizi-region-city' alizi-request='{$vo['request']}'></select><select name='region[area]' id='area' class='alizi-region alizi-region-area' alizi-request='{$vo['request']}'></select></div>";
					
					if(C('DEFAULT_LANG')=='zh-tw'){
						$options711 = '';
						foreach($city as $k=>$c){$options711 .= "<option value='{$c}'>{$c}</option>";}
						$regions .= "<div class='region-select region-711' style='display:none;'><select onchange=\"getRegion($(this),'711','city')\" disabled name='region[province]' id='province-711' class='alizi-region alizi-region-province' alizi-request='{$vo['request']}'>{$options711}</select><select onchange=\"getRegion($(this),'711','area')\" disabled name='region[city]' id='city-711' class='alizi-region alizi-region-city' alizi-request='{$vo['request']}'><option value=''>請選擇</option></select><select onchange=\"getRegionDetail(this.value,'711')\" disabled name='region[area]' id='area-711' class='alizi-region alizi-region-area' alizi-request='{$vo['request']}'><option value=''>請選擇</option></select></div>";
						
						$quanjia = '';
						foreach($city as $k=>$c){$quanjia .= "<option value='{$c}'>{$c}</option>";}
						$regions .= "<div class='region-select region-quanjia' style='display:none;'><select onchange=\"getRegion($(this),'quanjia','city')\" disabled name='region[province]' id='province-quanjia' class='alizi-region alizi-region-province' alizi-request='{$vo['request']}'>{$quanjia}</select><select onchange=\"getRegion($(this),'quanjia','area')\" disabled name='region[city]' id='city-quanjia' class='alizi-region alizi-region-city' alizi-request='{$vo['request']}'><option value=''>請選擇</option></select><select onchange=\"getRegionDetail(this.value,'quanjia')\" disabled name='region[area]' id='area-quanjia' class='alizi-region alizi-region-area' alizi-request='{$vo['request']}'><option value=''>請選擇</option></select></div>";
						
						$heimao = '';
						foreach($city as $k=>$c){$heimao .= "<option value='{$c}'>{$c}</option>";}
						$regions .= "<div class='region-select region-heimao' style='display:none;'><select onchange=\"getRegion($(this),'heimao','city')\" disabled name='region[province]' id='province-heimao' class='alizi-region alizi-region-province' alizi-request='{$vo['request']}'>{$heimao}</select><select onchange=\"getRegionDetail(this.value,'heimao')\" disabled name='region[city]' id='city-heimao' class='alizi-region alizi-region-city' alizi-request='{$vo['request']}'><option value=''>請選擇</option></select></div>";
						
						
						$ajaxUrl = C('ALIZI_ROOT')."index.php?m=Order&a=getRegion";//U('Order/getRegion');
						$regions .= "<script>function getRegion(_this,type,target){ var name=_this;var pid=0;if(typeof(_this)=='object'){var name=_this.val();pid=_this.find('option:selected').attr('data-id');}$.ajax({url:'{$ajaxUrl}',type:'post',data:{pid:pid,name:name,type:type,target:target},dataType:'json',success:function(ret){ if(ret.status=='1'){var opt='<option value=\'\'>請選擇</option>'; for(var i=0;i<ret.data.length;i++){ var data=ret.data[i];var info='';if(data.info!=null){info=' - '+data.info.address;}opt+='<option value=\''+data.name+'\' data-id=\''+data.id+'\'>'+data.name+info+'</option>';} $('#'+target+'-'+type).html(opt);$('#address').val(''); } }});} function getRegionDetail(name,type){ $.ajax({url:'{$ajaxUrl}',type:'post',data:{name:name,type:type,target:''},dataType:'json',success:function(ret){ $('#address').val(ret.address+'。 店名：'+ret.shop+'。 店号：'+ret.number+'。 電話：'+ret.phone); } });}</script>";
					}
					
					if(empty($aliziConfig['region'])){
						if(L('regions') && L('regions')!= 'REGIONS'){
							$regionArray = explode(',',L('regions'));
							$region = '';
							foreach($regionArray as $reg){ $region .= "<option value='{$reg}'>{$reg}</option>";}
							$html .= "<select name='region[province]' id='province' class='alizi-region alizi-region-province' style='width:98% !important;'>{$region}</select>";
						}else if(C('DEFAULT_LANG')=='zh-cn'){
							$html .= '<input name="region" class="alizi-input-text alizi-city-picker" readonly type="text" value="" data-toggle="city-picker"><script type="text/javascript">seajs.use(["alizi/city-picker"],function(region){});</script>';
						}else{	
							$html .= $regions;
							$html .= "<script type='text/javascript'>var lang='".C('DEFAULT_LANG')."';seajs.use(['alizi/region-'+lang],function(region){ new PCAS('region[province]','region[city]','region[area]','{$cookie['region'][0]}','{$cookie['region'][1]}','{$cookie['region'][2]}');});</script>";
						}	
					}else{
						switch($aliziConfig['region']){
							case 'city-picker':
								$html .= '<input name="region" class="alizi-input-text alizi-city-picker" readonly type="text" value="" data-toggle="city-picker"><script type="text/javascript">seajs.use(["alizi/city-picker"],function(region){});</script>';
							break;
							case 'region-zh-cn':
								$html .= $regions;
								$html .= "<script type='text/javascript'>var region='".$aliziConfig['region']."';seajs.use(['alizi/'+region],function(region){});</script>";
							break;
							case 'region-thai':
								$html .= $regions;
								$html .= "<script type='text/javascript'>var region='".$aliziConfig['region']."';seajs.use(['alizi/'+region],function(region){});</script>";
							break;
							default:
								$html .= $regions;
								$html .= "<script type='text/javascript'>var region='".$aliziConfig['region']."';seajs.use(['alizi/'+region],function(region){ new PCAS('region[province]','region[city]','region[area]','{$cookie['region'][0]}','{$cookie['region'][1]}','{$cookie['region'][2]}');});</script>";
							break;	
						}
					}
					
				break;
				case 'address':
					//$html .= "<div class='region-select region-payOnDelivery'><textarea rows='1' id='address' name='{$key}' placeholder='{$vo['info']}' autocomplete='off' alizi-request='{$vo['request']}' class='alizi-input-text' >{$cookie[$key]}</textarea></div>";
					$html .= "<div class='region-select region-payOnDelivery'><input id='address' name='{$key}' placeholder='{$vo['info']}' autocomplete='off' alizi-request='{$vo['request']}' class='alizi-input-text' value='{$cookie[$key]}' /></div>";
				break;
				case 'remark':
					$html .= "<textarea name='{$key}' placeholder='{$vo['info']}' class='alizi-input-text' alizi-request='{$vo['request']}' rows='2'></textarea>";
				break;
				case 'verify':
					$verify='http://'.$_SERVER['HTTP_HOST'].C('ALIZI_ROOT').'index.php?m=Alizi&a=verify';
					if(!empty($request['verify'])) $verify .= '&'.http_build_query($request['verify']);
					$html .= "<input type='tel' name='{$key}' placeholder='{$vo['info']}' class='alizi-input-text' autocomplete='off' alizi-request='{$vo['request']}' style='width:30%;'>
						<img class='verify' src='{$verify}' onclick=\"$(this).attr('src','{$verify}&t='+new Date().getTime())\" />
						<a href='javascript:;' class='bright' onclick=\"$('.verify').attr('src','{$verify}&t='+new Date().getTime())\" />".lang('changeVerifyCode')."</a>";
				break;
				case 'code':
					$html .= "<input type='tel' name='{$key}' placeholder='{$vo['info']}' class='alizi-input-text' autocomplete='off' alizi-request='{$vo['request']}' style='width:50%;float:left;'>
						<button type='button' id='alizi-code' class='alizi-btn-min alizi-btn alizi-btn-ok' onclick=\"alizi.getCode()\" style='width:48%;font-weight:normal;float:right;padding:0.6rem 0;font-size:14px;' />".lang('getMobileCode')."<i></i></button>";
				break;
				default:
					$html .= "<input type='text' name='{$key}' placeholder='{$vo['info']}' autocomplete='off' alizi-request='{$vo['request']}' class='alizi-input-text' value='{$cookie[$key]}'>";
				break;
			}	
			if(!in_array($key,array('product','extends'))){ $html .= "</div></div>";}	
			 
		}

		$html .=  "<input type='hidden' name='item_index' value='{$item_index}'>";
		$html .= "<div class='alizi-rows alizi-id-btn clearfix'><input type='submit' id='alizi-submit' class='alizi-btn alizi-submit' value='".lang('orderSubmit')."' /></div></form></div></div></div>";

		if($template['show_notice']){
			$html .= "<div class='alizi-box alizi-box-notice {$show_notice}'><div class='alizi-side alizi-border {$show_notice}'><div class='alizi-title alizi-title-scroll alizi-border ellipsis'><i class='icon-shipping'></i>".lang('orderNotification')."</div><div class='alizi-delivery'><div class='alizi-scroll {$show_notice}'><ul>";
			if($aliziConfig['real_notice']==1){
				$orders = M('Order')->field('item_name,item_params,name,mobile,region,add_time')->where(array('item_id'=>$info['id']))->order('id asc')->limit(25)->select();
				$i=0;
				foreach($orders as $li){
					$region = explode(' ',$li['region']);
					$item_params = empty($li['item_params'])?'':' - '.$li['item_params'];
					$i++;
					$html .= "<li ".($i%2 == 0?"class='even'":'')."><p><span class='alizi-badge'>{$region[0]}</span>".mb_substr($li['name'],0,1,'utf-8')."*[".substr($li['mobile'],0,3)."****".substr($li['mobile'],-4)."]</p><p><span class='alizi-date'>".date('m-d',$li['add_time'])."</span>{$li['item_name']}{$item_params}</p></li>";
				}
			}else{
				//$item = json_decode($info['params'],true);
				$item = M('ItemParams')->where("item_id=".$info['id'])->select();
				$province = explode(',',L('scrollProvince'));
				$name = explode(',',L('scrollName'));
				$mobile = explode(',',L('scrollMobile'));
				$time=  explode(',',L('scrollTime'));
				for($i=0;$i<50;$i++){
					$num = rand(0,3);
					$pro = empty($item)?$info['name']:$item[array_rand($item,1)]['title'];
					$pp = $province[array_rand($province,1)];
					$nn = $name[array_rand($name,1)];
					$mm = $mobile[array_rand($mobile,1)].'****'.randCode(4);
					$rand = array_rand($time);
					$html .= "<li ".($i%2 == 0?"class='even'":'')."><p><span class='alizi-badge'>{$pp}</span>{$nn}*[{$mm}]</p><p><span class='alizi-date'>{$time[$rand]}</span>{$pro}</p></li>";
				}	
			}
			$html .= "</ul></div></div></div></div><!--.alizi-box-->";
		}
	
	$html .="<script type='text/javascript'>seajs.use(['jquery','alizi'], function(jQuery,alizi) { $('input[name=coupon]').bind('keyup keydown blur',function(){ var code = $(this).val();if(code.length>=8){alizi.coupon(code);}});alizi.resize('{$template['show_notice']}');});</script>";	
$html .= "</div>";

$html .= "<script type='text/javascript'>seajs.config({vars: {'payment':".json_encode($payment).",'shipping':".json_encode($shipping).",'discount':".$agent_discount."}});</script>";
return $html;
?>
