<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta content="telephone=no" name="format-detection">
<meta content="yes" name="apple-mobile-web-app-capable">
<meta content="black" name="apple-mobile-web-app-status-bar-style">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui">
<title>订单支付</title>
<script type="text/javascript" src="../Public/Assets/js/jquery.min.js"></script>
<style type="text/css">
.alert{min-height:3rem;margin:.7em;text-align:center;font-size:1.2em;}
.btn{width:4.5em;height:1.2em;display:inline-block;padding:.29em 1em;margin:0 .2em;font-size:1em;font-weight:400;text-align:center;white-space:nowrap;vertical-align:middle;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:.2em;overflow:hidden;text-decoration:none;}
.btn-grey{color:#fff;background-color:#878787;border-color:#878787}
.btn-red{color:#fff;background-color:#fb2b0a;border-color:#fb2b0a}
body{padding:0;margin:0;border:0;outline:0;text-align:center;-webkit-text-size-adjust:none;-webkit-tap-highlight-color:rgba(0,0,0,0)}
input[type="text"]{font-family:Microsoft YaHei,arial;outline:0;-webkit-appearance:none;-moz-appearance:none}
#box{margin:4em 0 0 0;text-align:left;font-family:'Microsoft YaHei',arial}
.section{width:14.5em;margin:0 auto .8em}
.title em{font-style:normal;font-size:.6em}
.red{color:red}
.gray{color:#aaa}
.section .tip{display:inline-block;width:4.2em;height:2em;line-height:2em;font-weight:bold;text-align:right}
.section .tip em{font-size:.7em;color:#f40}
.section .tip span{font-size:.7em;display:inline-block;color:#000}
#mobile,#code{font-size:.7em;display:inline-block;width:6.5em;height:2em;margin:0 .2em 0 0;padding:0 .4em;border:1px solid #aaa;border-radius:.3em}
#code{width:4em}
#get,#verify{display:inline-block;width:4.2em;height:2em;line-height:2em}
#get em{padding:0 .3em;width:7em;height:2.3em;line-height:2.5em;display:inline-block;font-style:normal;font-size:.6em;text-align:center;background:#4da4fe;border-radius:.5em;cursor:pointer;border:0;color:#FFF}
#verify{font-size:.8em;width:100%;text-align:center;background:#f40;border-radius:.2em;cursor:pointer;border:0;color:#FFF}
.dupl{display:none;}
</style>
<body>

<?php
$url = "../index.php?m=Order&a=pay&order_no=".trim($_GET['id']);
?>
<div class="dupl"> 
	<div>
		<a class="btn btn-red fileinput-button" href="<?php echo $url;?>">支付订单</a>
	</div>
</div>

<script>
$(function(){
	window.location.href="<?php echo $url;?>";
})
</script>
</body>
</html>
