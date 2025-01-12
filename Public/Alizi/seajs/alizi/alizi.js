/*
 * 标题：时时订单系统
 * 作者：47478439（QQ号）
 * 官方网址：www.wxrob.com
 * 官方店铺：https://www.wxrob.com/
 * 警示信息：您可以复制使用本站静态文件（html/css/js/images），但请保留原创作者（47478439（QQ号））信息，谢谢。
 */
define(function(require, exports, module) {
	require('../layer/skin/layer.css');	
	var $ = require("jquery"),layer = require("layer/layer"),aliziScroll = require("alizi/scroll"),scrollTo = require("jquery/scrollTo");

	checkForm = function(){
		var handle = $(arguments[0]);
		var name = handle.find('input[name=name]');
		var mobile = handle.find('input[name=mobile]');
		var region = handle.find('.region');
		var address = handle.find('input[name=address]');
		var qq = handle.find('input[name=qq]');
		var mail = handle.find('input[name=mail]');
		var verify = handle.find('input[name=verify]');
		var rule = /^1[3-8][0-9]\d{8}$/,ruleTw=/^(9|09)\d{8}$/,ruleJp=/^([789]|0[789])\d{9}$/;
		var nameReg=/^[\u0391-\uFFE5]+$/;  
	
		try{
			layer = typeof(top.layer) == "undefined"?layer:top.layer
		}catch (ex){
			layer = layer;
		}
		  
   
		if(name.length!=0 && $.trim(name.val()).length<2){layer.msg(lang.emptyName);return false;}
		//if(!nameReg.test(name.val())){ layer.msg('请填写有效的姓名');return false;}
		//if(mobile.length!=0 && (rule.test(mobile.val()) == false && ruleTw.test(mobile.val()) == false) && ruleJp.test(mobile.val()) == false){layer.msg(lang.invalidMobile);return false;}
		if(region.length!=0 && !region.eq(0).val()){layer.msg(lang.SelectRegion);return false;}
		//if(address.length!=0 && !address.val()){layer.msg(lang.emptyAddress);return false;}
		//if(mail.length!=0 && !mail.val()){layer.msg(lang.emptyEmail);return false;}
		if(verify.length!=0 && verify.val().length!= 4){layer.msg(lang.invalidVerify);return false;}
	};
	
	exports.quantity = function(){
		try{  seajs.data.vars.payment}catch(err){ return;}
		//if(!seajs.data.vars.payment) return;
		
		if($('.rows-id-params').hasClass('rows-id-params-checkbox')){
			var allPrice = 0;
			$(".rows-id-params-checkbox label.active").each(function(){     
				allPrice += parseFloat($(this).attr('alizi-value'));     
			})   
			$("input[name=item_price]").val(allPrice);
		}
	
		var paymentData = seajs.data.vars.payment;//require("payment"),
			amount = parseInt(arguments[0]),	
			price = parseFloat($("input[name=item_price]").val()),
			payment = $("input[name=payment]:checked").val(),
			quantiryInput = $("input[name=quantity]"),
			qrcodepay = $("input[name=qrcode_pay]").val(),
			num   = parseInt(quantiryInput.val()),
			math = $("input[name=math]").val(),
			buy_num = $("input[name=buy_num]").val().split(','),
			min_num = parseInt($('input[name=min_num]').val()),
			max_num = parseInt($('input[name=max_num]').val()),
			buy_num_decrease = $("input[name=buy_num_decrease]").val().split(',');
	
		num = (isNaN(num) || num<0)?1:num;
		var totalNum = (amount+num)<1?1:(amount+num);
		totalNum = payment==5 && qrcodepay=='1'?1:totalNum;
		if(totalNum<min_num){
			totalNum = min_num;
			layer.msg(lang.minNum+min_num);
		}
		if(totalNum>max_num){
			totalNum = max_num;
			layer.msg(lang.maxNum+max_num);
		}
		quantiryInput.val(totalNum);
		
		var data = paymentData[payment];
		var count = math.substr(0,1),fee=parseFloat(math.substr(1));
		var totalPrice = count=='+'?(price*totalNum+fee):(price*totalNum*fee);//订单总价
		var shippingCost = shipping(totalNum,totalPrice);//运费
		var subTotal=totalPrice+shippingCost;
		
		var decrease = 0;
		var len = buy_num.length;
		var discountDom = $('.alizi-discount');
		if(buy_num[0]!=='' && totalNum>=buy_num[0]){
			var n=0;
			for(var i = 0; i < len; i++){
				var j = len-1;
				if(parseInt(buy_num[j])<=totalNum){
					n = j;break;
				}else if(parseInt(buy_num[i])>totalNum){
					n = i-1;break;
				}
			}	
			decrease = typeof(buy_num_decrease[n]) == 'undefined'?0:buy_num_decrease[n];
			
			if(isNaN(decrease) && decrease.substr(0,1)=='*'){
				decrease = parseFloat(decrease.substr(1));
				subTotal *= decrease;
				discountDom.html(' ('+(decrease*10)+lang.discount+')');
			}else{
				subTotal -= parseFloat(decrease);
				discountDom.html(' ('+lang.cutoff+decrease+')');
			}
		}else{
			discountDom.html('');
		}
		
		var language = $('input[name=lang]').val();
		var aliziShipping =  $(".alizi-shipping");
		var totalPriceDom =$(".alizi-total-price"),couponPriceDom=$('.alizi-coupon'),couponValue=parseFloat($('input[name=coupon_value]').val());
		aliziShipping.find('.alizi-shipping-price').html(shippingCost);
		aliziShipping.find('.alizi-order-price').html(totalPrice);
		subTotal = language=='zh-cn'?subTotal.toFixed(2):priceFormat((subTotal.toFixed(2)*100)/100);
		
		totalPriceDom.html(subTotal);
		$('#alizi-price').val(subTotal);
		$(".alizi-total-number").html(totalNum);
		
		if(!isNaN(couponValue) && couponValue>0){
			var couponPrice = totalPrice>=couponValue?totalPrice - couponValue:0;
			totalPriceDom.css({'font-size':'14px','color':'#333','text-decoration':'line-through'});
			couponPriceDom.text(couponPrice);
		}else{
			couponPriceDom.text('');
			totalPriceDom.attr('style','');
		}
	}
	exports.num = function(){
		var amount = parseInt(arguments[0]),	
			id = parseInt(arguments[1]),
			numInput = $("#num-"+id),
			num      = parseInt(numInput.val()),
			min_num  = parseInt($("input[name=min_num]").val()),
			max_num  = parseInt($("input[name=max_num]").val()),
			totalPrice=0;
	
		num = (isNaN(num) || num<0)?0:num;
		var totalNum = (amount+num)<1?0:(amount+num);
		numInput.val(totalNum);
		
		var quantity=0;
		$('.alizi-groups').each(function(i){
			var _this = $(this),id=_this.attr('data-id'),price=parseFloat($('#price-'+id).val()),num=parseInt($('#num-'+id).val());
			totalPrice += (price*num);
			quantity += num;
		});
		if(quantity<min_num){
			layer.msg(lang.minNum+min_num);
			numInput.val(num); return false;
		}
		if(quantity>max_num){
			layer.msg(lang.maxNum+max_num);
			numInput.val(num); 
			return false;
		}
		var shippingCost = shipping(quantity,totalPrice);//运费
		var subTotal=totalPrice+shippingCost;
		
		
		$('.alizi-shipping-price').html(shippingCost);
		$('.alizi-total-price').text(subTotal.toFixed(0));
	}
	
	exports.timer = function(elem,intDiff){
		
		window.setInterval(function(){
			var day=0,hour=0,minute=0,second=0,times=intDiff;
			if(intDiff > 0){
				day = Math.floor(intDiff / (60 * 60 * 24));
				hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
				minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
				second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
			}
			
			hour = hour<10?'0'+hour:hour;
			minute = minute<10?'0'+minute:minute;
			second = second<10?'0'+second:second;
				
			if(day==0 && hour=='00' && minute=='00' && second=='00'){
				//$('.alizi-submit').attr('disabled','disabled').val(text);
				$(elem).html(lang.actionEnd);
			}else{
				if(day==0){
					$(elem).find('.aliziDay').remove(); 
				}else{
					$(elem).find('.aliziDay strong').html(day); 
				}
				$(elem).find('.aliziHour strong').html(hour); 
				$(elem).find('.aliziMinute strong').html(minute); 
				$(elem).find('.aliziSecond strong').html(second); 
			}
			intDiff--;
		}, 1000);
	}
	exports.getCode = function(){
		var second=60,
		verify = $("input[name=verify]").val(),
		item_id = $("input[name=item_id]").val(),
		page = $("input[name=page]").val(),
		mobile = $("input[name=mobile]").val(),
		btn = $('#alizi-code');
		
		btn.attr('disabled','disabled');
		var interval = setInterval(function(){
			if(second<=0){
				clearInterval(interval);
				btn.attr('disabled',false).find('i').text('');
			}else{
				var text= "("+second+")";
				btn.find('i').text(text);
				second--;
			}
		}, 1000); 
		$.ajax({
			url:aliziRoot+'index.php?m=Order&a=getCode',
			type:'post',
			data:{item_id:item_id,mobile:mobile,verify:verify,page:page},
			dataType:'json',
			success:function(data){
				if(data.status=='0'){
					layer.msg(data.info);
					clearInterval(interval);
					btn.attr('disabled',false).find('i').text('');
				}
			}
		});
	}	
	exports.payment = function(){
		var index = arguments[0];
		var paymentData = seajs.data.vars.payment;//require("payment"),
		var info = paymentData[arguments[0]]['info'],payment=$('#alizi-payment-info');
		$("input[name=math]").val(paymentData[arguments[0]]['math']);
		if(info){ payment.show().find('.payment-info').html(info);}else{payment.hide();}
		if(index==66){ 
			$('#alizi-creditcard-info').show();
			$('.rows-id-mail .alizi-request').css('visibility','visible');
		}else{ 
			$('#alizi-creditcard-info').hide();
			$('.rows-id-mail .alizi-request').attr('style','');
		}
		
		var Region = $('.region-'+index);
		Region.show().siblings('.region-select').hide();
		Region.find('select').attr('disabled',false);
		Region.siblings('.region-select').find('select').attr('disabled',true);
		//$('#address').val('');
		if(index=='payOnDelivery'){
			//$('input[name=address]').attr('readonly',false).css('background-color','#fff');
			//$('#address').attr({'rows':1,'readonly':false,'style':'background-color:#fff'});
		}else if(index=='711' || index=='quanjia' || index=='heimao'){
			//$('input[name=address]').attr('readonly',true).css('background-color','#eee');
			//$('#address').attr({'rows':2,'readonly':true,'style':'background-color:#eee'});
			getRegion('',index,'province');
		}
		
		if( $('.rows-id-params').hasClass('alizi-group')){exports.num(0);}else{exports.quantity(0);}
		
	}
	exports.resize = function(){
		var show = arguments[0];
		var width = window.document.body.offsetWidth,className='alizi-full-row',main=$('.alizi-main'),side=$('.alizi-side'),scroll=$('.alizi-scroll');
		if(show=='2' && width>600){ 
			main.removeClass(className);side.removeClass(className);scroll.removeClass(className);
			$(".alizi-scroll").height(main.height()-100);
		}else{
			main.addClass(className);side.addClass(className);scroll.addClass(className);
		}
	}
	exports.scroll = function(){
		var id = arguments[0],time=arguments[1]||2500,ul=$("#"+id+" ul");
		setInterval(function(){
			var last=ul.children('li:last'),height=last.height();
			ul.prepend(last.css({height:0}).animate({height:height},'slow'));
		},time);
	}
	exports.coupon = function(){
		var code = arguments[0],len = code.length,couponInput = $('input[name=coupon_value]'),couponPrice=$('.alizi-coupon');
		if(len>=8){
			$.ajax({
				url:aliziRoot+'index.php?m=Api&a=couponCheck',
				type:'post',
				data:{code:code,type:2,format:'json'},
				dataType:'json',
				success:function(data){
					if(data.status=='1'){
						couponInput.val(data.value);
					}else{
						couponInput.val(0); 
					}
					exports.quantity(0);
				}
			}); 
		}else{
			couponInput.val(0); 
			exports.quantity(0);
		}  
	}
	window.weixin = function(){
		var wx='',img='';
		if(arguments[0]){
			wx = '<p class="weixin_title" style="text-align:center">↓长按复制下方微信号↓</p><p class="weixin_number" style="margin:10px auto;background-color: rgb(255, 79, 121);border-radius: 5px;padding: 5px;width: 200px;color: white;font-size: 1.3em;font-weight: 700;color:#fff">'+arguments[0]+'</p>';
		}
		if(arguments[1]){
			img = '<p class="weixin_title" style="text-align:center">↓长按识别下方二维码↓</p><p class="weixin_image"><img src="'+arguments[1]+'" style="max-width:290px"></p>';
		}
		layer.open({
			type: 1,title: "添加微信", skin: 'layui-layer-demo',closeBtn: 0,anim: 2,offset: '50px',shadeClose: true,btn: ['打开微信','关闭'],btnAlign: 'c',
			content: '<div style="padding:20px;width:300px;text-align:center;font-size:18px;">'+img+wx+'</div>',
			  success: function(alizi){
				var btn = alizi.find('.layui-layer-btn');
				btn.find('.layui-layer-btn0').attr({ href: 'weixin://' ,target: '_blank' });
			  }
		});
	}
	window.scrollto = function(){
		$.scrollTo(arguments[0],800);
	}
	
	function shipping(quantity,totalPrice){
		var shippingPrice = 0;
		var shipping = seajs.data.vars.shipping;//require("shipping");
		if(shipping.id==0) return 0;
		if(shipping['is_free_num']==1 && quantity>=shipping['free_num']) return 0;
		if(shipping['is_free_cost']==1 && totalPrice>=shipping['free_cost']) return 0;
		if(quantity <= shipping['less_num']){
			shippingPrice =  parseFloat(shipping['less_num_cost']);
		}else{
			var step = Math.ceil((quantity-parseInt(shipping['less_num']))/parseInt(shipping['step_num']));
			shippingPrice =  parseFloat(shipping['less_num_cost'])+step*parseFloat(shipping['step_num_cost']);
		}
		return (shippingPrice.toFixed(2)*100)/100;
	}
	
	isupload = false;
	window.aliziUpload = function(fileInput){
		if(isupload != false){
			layer.msg("Loadding...");
		}else{
			$(fileInput).click();
		}
	}
	window.uploadImg = function(fileInput,target,thumb){
		var filename = $(fileInput).val();
		if(filename != '' && filename != null){
			isupload = true;
			var pic = $(fileInput)[0].files[0];
			console.log(pic);
			var fd = new FormData();
			fd.append('imgFile', pic);
			$.ajax({
				url:aliziRoot+'index.php?m=Public&a=upload',
				type:"post",
				dataType:'json',
				data: fd,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend:function(){
					layer.msg('Loadding...');
				},
				success:function(data){ 
					if(data.status=='1'){
						$(target).val(data.data);
						layer.msg(lang.uploadSuccess);
					}else{
						layer.msg(data.data);
					}
				},
				error:function (){
					layer.msg("Error");
				}
			});
			isupload = false;
		}
		isupload = false;
	}
	function priceFormat(num) {  
		var reg=/\d{1,3}(?=(\d{3})+$)/g;   
		return (num + '').replace(reg, '$&,');  
	}


	$('.alizi-params').bind('click',function(){
		
		var _this = $(this),className='active';
		var target = _this.attr('alizi-target'),value = _this.attr('alizi-value'),fx = _this.attr('alizi-fx'),params = _this.attr('alizi-fx-params');

		if(_this.hasClass('alizi-checkbox')){
			var isCheck = _this.find('input:checked').length;
			if(isCheck==1){
				_this.addClass(className);
			}else{
				_this.removeClass(className);
			}
		}else{
			_this.addClass(className).siblings().removeClass(className);
		}
		
		if(target){
			if(target.indexOf(':')===0){
				_this.find('input').get(0).checked=true;
			}else{
				$(target).val(value);
			}
			if(fx){ eval(fx+'("'+params+'")');}
		}
	})
	$('.rows-id-params-radio .alizi-radio').bind('click',function(){
		var i = $(this).index()+1;
		$('input[name=item_index]').val(i);
		

		$('.extends').addClass('extends-hidden');
		$('.extends').find('input').attr('disabled','disabled');
		$('.extends').find('select').attr('disabled','disabled');
		
		$('.extends-'+i).removeClass('extends-hidden');
		$('.extends-'+i).find('input').attr('disabled',false);
		$('.extends-'+i).find('select').attr('disabled',false);
	})
	$('.rows-id-params-checkbox .alizi-checkbox').bind('click',function(){
		var i = $(this).index()+1;
		$('input[name=item_index]').val(i);
		
		var isChecked = $(this).find('input[type=checkbox]:checked').length;
		if(isChecked==1){
			$('.extends-'+i).removeClass('extends-hidden');
			$('.extends-'+i).find('input').attr('disabled',false);
		}else{
			$('.extends-'+i).addClass('extends-hidden');
			$('.extends-'+i).find('input').attr('disabled','disabled');
		}
	})
	
	$('.extends-level-1 .alizi-radio').bind('click',function(){
		var i = $(this).index()+1;
		
		$('.subextends').addClass('subextends-hidden');
		$('.subextends').find('input').attr('disabled','disabled');
		$('.subextends').find('select').attr('disabled','disabled');
		
		$('.subextends-'+i).removeClass('subextends-hidden');
		$('.subextends-'+i).find('input').attr('disabled',false);
		$('.subextends-'+i).find('select').attr('disabled',false);

		console.log(i);
	})
	
	$('.alizi-params-change').change(function(){ 
		var _this = $(this),fx = _this.attr('alizi-fx'),params = _this.attr('alizi-fx-params'),price = _this.find("option:selected").attr('value-price');
		$("input[name=item_price]").val(price);
		if(fx)eval(fx+'("'+params+'")');
	});
	
	var scrollHeight = $('.alizi-scroll li').innerHeight();
	$('.alizi-scroll').aliziScroll({speed:80,rowHeight:scrollHeight});
	
});
/*
document.oncontextmenu=function(){ return false;}
document.onkeydown=function(){
    var e = window.event||arguments[0];if(e.keyCode==123){return false;}else if((e.ctrlKey)&&(e.shiftKey)&&(e.keyCode==73)){ return false;}else if((e.ctrlKey)&&(e.keyCode==85)){ return false;}else if((e.ctrlKey)&&(e.keyCode==83)){ return false;}
}
*/