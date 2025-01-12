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
.main{ background:none repeat scroll 0 0 #fff;border:1px solid #dfdfdf;border-radius:11px 11px 11px 11px;color:#333;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;margin:2em auto;padding:1em 2em;width:700px}
p,li,dd,dt{ font-size:12px;line-height:18px;padding-bottom:2px}
.step{ margin:20px 0 15px}
.step,th{ padding:0;text-align:left}
.submit input,.button,.button-secondary{ -moz-box-sizing:content-box;border:1px solid #bbb;border-radius:15px 15px 15px 15px;color:#464646;cursor:pointer;font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;font-size:14px!important;line-height:16px;padding:6px 12px;text-decoration:none}
.button:hover,.button-secondary:hover,.submit input:hover{ border-color:#666;color:#000}
.button,.submit input,.button-secondary{ background:#f2f2f2}
.button:active,.submit input:active,.button-secondary:active{ background:#eee}
</style>

<!--main-->
<div class="midder">

<div class="main">
<p>欢迎使用 <span style="color:#F00; font-size:18px; font-weight: bolder">时时订单系统（www.wxrob.com）</span></p>
<p>时时订单系统，是采用国内优秀框架ThinkPHP开发的导航程序，安全，高效，界面优美。</p>
<p>在安装之前，您需要准备以下信息：</p>
<ol>
<li>数据库名</li>
<li>数据库用户用户名</li>
<li>数据库用户密码</li>
<li>数据库主机</li>
</ol>

<p>大多数的互联网主机服务提供商都向您提供了数据库的信息。若您不知道这些信息，您需要先询问好，再进行安装。若您已准备好 …</p>

<?php if(empty($publicDir)): ?><p>检测到以下目录不可写</p>
<p>
./Public
</p>

<p>请将不可写的目录设置为可写(777)权限。</p>

<p><a class="button" href="javascript:;" onclick="window.reload();">设置完毕，点击刷新</a></p>
<?php else: ?>

<p class="step"><a class="button" href="index.php?m=Install&a=next">点击开始安装！</a></p><?php endif; ?>
</div>

</div>

﻿<!--footer-->
<div class="footer">Powered by 时时订单系统（<a href="http://www.wxrob.com" target="_blank">www.wxrob.com</a>）</div>
</body>
</html>