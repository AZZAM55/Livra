<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>用户登陆</title>
<meta name="Author" content="www.wxrob.com">
<meta name="Version" content="<?php echo C('ALIZI_VERSION');?>">
<link rel="stylesheet" charset="utf-8" type="text/css" href="__PUBLIC__/Assets/css/login.css" />
<link rel="shortcut icon" href="<?php echo C('ALIZI_ROOT');?>alizi.ico?v=<?php echo (ALIZI_VERSION); ?>" />
</head>
<body>	

<div id="header">
	<div class="w-980 clearfix">
    	<div class="logo"><a href="http://<?php echo ($_SERVER['HTTP_HOST']); ?>" style="color:#fff;font-size:25px;"><img src="__PUBLIC__/Assets/img/Alizi-logo.png" height="35"></a></div>
    </div>
</div>

	<!-- passport 登录 -->
	<div class="w-980 login-area pr">
		<div class="login-box pa">
			<h2 class="hidden"></h2>
			<ul class="login-tab">
				<li class="tab-item tab-item-left tab-selected" onclick="userRole($(this),1)"><a onclick="javascript:;" hidefocus="true"><?php echo lang('user_login');?></a></li>
			</ul>
			<div id="bp_pass_login_form" class="tang-pass-login">
            	<form method="POST" action="<?php echo U('Public/login');?>" style="zoom: 1;" onSubmit="return checkLogin()">

                <p class="pass-form-item pass-form-item-userName" style="display:block">
                    <label class="pass-label pass-label-userName"><?php echo lang('account_colon');?><span class="pass-generalError username-error"></span></label>
                    <input id="username" type="text" name="username" class="pass-text-input pass-text-input-userName pass-phone" autocomplete="off">
                </p>
                <p class="pass-form-item pass-form-item-password" style="display:block">
                    <label class="pass-label pass-label-password"><?php echo lang('password_colon');?><span class="pass-generalError password-error"></span></label>
                    <input id="password" type="password" name="password" class="pass-text-input pass-text-input-userName pass-phone" autocomplete="off">
                </p>
                <!--p class="pass-form-item pass-form-item-verifyCode" style="display:none;">
                    <label class="pass-label pass-label-verifyCode">验证码</label>
                    <input type="text" name="verifyCode" class="pass-text-input pass-text-input-verifyCode pass-verify" maxlength="6">
                    <span><img title="验证码图片" alt="验证码图片" class="pass-verifyCode" src=""></span>
                    <a class="pass-change-verifyCode">换一张</a>
                </p-->
                <p class="pass-form-item pass-form-item-submit">
                    <input type="submit" value="<?php echo lang('login');?>" class="pass-button pass-button-submit">
                </p>
                <input type="hidden" id="userrole" name="userrole" value="1">
                </form>
            </div>
            <!--
			<div class="login-box-bottom">
				<a class="register"><?php echo lang('register_account');?></a>
			</div>
            -->
		</div>
	</div>
	<!-- end of passport 登录 -->
	
<div class="footer">
	<div class="footer-message">
		<p class="copyright">&nbsp;<?php echo lang('copyRight');?></p>
	</div>
</div>
<script type="text/javascript" charset="utf-8" src="__PUBLIC__/Assets/js/jquery.min.js"></script>
<script>
function checkLogin(){
	var username = $('#username'),password = $('#password');	
	if($.trim(username.val())==''){
		$('.username-error').html('请输入账号');username.focus();	return false;
	}else{
		$('.username-error').html('');
	}
	if($.trim(password.val())==''){
		$('.password-error').html('请输入密码');password.focus();	return false;
	}else{
		$('.password-error').html('');
	}
}
</script>
</body>
</html>