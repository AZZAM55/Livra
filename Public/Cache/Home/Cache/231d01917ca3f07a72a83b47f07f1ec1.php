<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>时时订单系统 - www.wxrob.com</title>
<link rel="shortcut icon" href="<?php echo C('ALIZI_ROOT');?>alizi.ico?v=<?php echo (ALIZI_VERSION); ?>" />
<style>
body{ margin:0; padding:0; font-family:Arial; font-size:12px;text-align:left;}
img{border:0;}
h2{font-size:14px;padding:5px;margin:0px;}
a {color:#336699;text-decoration:none;}
.header{background:#3A81C0;width:100%;overflow:hidden;}
.header .head{width:960px;height:60px;margin:0 auto;overflow:hidden;}
.header .head .logo{float:left;margin:10px 0 0 0;color:#FFFFFF;}
.header .head .menu{float:right;margin:20px 0 0 0;color:#FFFFFF;}
.midder{width:960px;margin:0 auto;overflow:hidden;}
.footer{background:#F0F0F0;padding:10px 0;text-align:center;color:#999999;}
</style>
</head>
<body>
<!--header-->
<div class="header">
<div class="head">
<div class="logo"><a href="http://www.wxrob.com" target="_blank"><img src="__PUBLIC__/Assets/img/Alizi-logo.png" alt="时时订单系统" /></a></div>
<div class="menu">时时订单系统安装 - www.wxrob.com</div>
</div>
</div>

<style>
.main{background:none repeat scroll 0 0 #fff;border:1px solid #dfdfdf;border-radius:11px 11px 11px 11px;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto;padding:1em 2em;width:700px}
p,li,dd,dt{font-size:12px;line-height:18px;padding-bottom:2px}
.step{margin:20px 0 15px}
.step,th{padding:0;text-align:left}
.submit input,.button,.button-secondary { -moz-box-sizing:content-box;border:1px solid #bbb;border-radius:15px 15px 15px 15px;color:#464646;cursor:pointer;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;font-size:14px!important;line-height:16px;padding:6px 12px;text-decoration:none}
.button:hover,.button-secondary:hover,.submit input:hover{border-color:#666;color:#000}
.button,.submit input,.button-secondary{background:#f2f2f2}
.button:active,.submit input:active,.button-secondary:active{background:#eee}
.form-table{border-collapse:collapse;margin-top:1em;width:100%}
.form-table td{border-bottom:1px solid #fff;font-size:12px;margin-bottom:9px;padding:10px}
.form-table th{border-bottom:1px solid #fff;font-size:13px;padding:16px 10px 10px;text-align:left;vertical-align:top;width:130px}
.form-table tr{background:none repeat scroll 0 0 #f3f3f3}
.form-table code{font-size:18px;line-height:18px}
.form-table p{font-size:11px;margin:4px 0 0}
.form-table input{font-size:15px;line-height:20px;padding:2px}
.form-table th p{font-weight:normal}
#error-page{margin-top:50px}
#error-page p{font-size:12px;line-height:18px;margin:25px 0 20px}
#error-page code,.code{font-family:Consolas,Monaco,Courier,monospace}
#pass-strength-result{background-color:#eee;border-color:#ddd!important;border-style:solid;border-width:1px;display:none;margin:5px 5px 5px 1px;padding:5px;text-align:center;width:200px}
#pass-strength-result.bad{background-color:#ffb78c;border-color:#ff853c!important}
#pass-strength-result.good{background-color:#ffec8b;border-color:#fc0!important}
#pass-strength-result.short{background-color:#ffa0a0;border-color:#f04040!important}
#pass-strength-result.strong{background-color:#c3ff88;border-color:#8dff1c!important}
.message{background-color:#ffffe0;border:1px solid #e6db55;margin:5px 0 15px;padding:.3em .6em}

</style>

<!--main-->
<div class="midder">

<div class="main">

<form action="index.php?m=Install&a=setup" method="post">
	<p>请在下方输入数据库相关信息。若您不清楚，请咨询主机提供商。</p>
	<table class="form-table">
		<tbody>
		
		<tr>
			<th scope="row"><label for="dbhost">数据库连接地址</label></th>
			<td width="200"><input type="text" value="localhost" size="25" id="dbhost" name="DB[DB_HOST]"></td>
			<td>通常情况下，应填写 <code>localhost</code>，若测试连接失败，请联系主机提供商咨询。</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbport">数据库端口</label></th>
			<td><input type="text" value="3306" size="25" id="dbport" name="DB[DB_PORT]"></td>
			<td>默认3306</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbname">数据库名称</label></th>
			<td><input type="text" value="alizi" size="25" id="dbname" name="DB[DB_NAME]"></td>
			<td>若数据库不存在，将自动创建</td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">数据库账号</label></th>
			<td><input type="text" value="root" size="25" id="uname" name="DB[DB_USER]"></td>
			<td></td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">数据库密码</label></th>
			<td><input type="text" value="" size="25" id="pwd" name="DB[DB_PWD]"></td>
			<td></td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">表名前缀</label></th>
			<td><input type="text" size="25" value="alizi<?php echo randCode(3);?>_" id="prefix" name="DB[DB_PREFIX]"></td>
			<td>若数据库已存在其他表，请修改本项以区分</td>
		</tr>
	</tbody></table>


<p>请在下面输入网站后台管理员信息</p>

	<table class="form-table">
		<tbody>
		
			<tr>
				<th scope="row"><label for="username">网站后台登录账号：</label></th>
				<td width="200"><input type="text" value="admin" size="25" name="ADMIN[username]"></td>
				<td>后台登陆用户名</td>
			</tr>
			
			<tr>
				<th scope="row"><label for="password">后台登录密码：</label></th>
				<td><input type="text" value="123456" size="25" name="ADMIN[pwd]"></td>
				<td>您的登录密码</td>
			</tr>
			
		</tbody>
	</table>

	<p class="step">
		<input type="hidden" value="<?php echo ($root); ?>" name="DB[ALIZI_ROOT]">
		<input type="submit" class="button" value="立即安装" name="submit">
		<span><input name="install_demo" type="checkbox" checked style="vertical-align:middle;">安装测试数据</span>
	</p>

</form>

</div>

</div>
﻿<!--footer-->
<div class="footer">Powered by 时时订单系统（<a href="http://www.wxrob.com" target="_blank">www.wxrob.com</a>）</div>
</body>
</html>