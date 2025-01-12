<?php
require 'aliziApi.php';
payLog($_REQUEST,'paysera');
echo http( getNotifyUrl('paysera',$_REQUEST['orderid']), 'POST',$_REQUEST);