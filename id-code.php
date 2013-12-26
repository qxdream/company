<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-02-14 验证码入口 $
	@version  $Id: qx-admin.php 2011-02-14
*/

define('APP_PATH', './id-code/'); 

require dirname(__FILE__) . '/QXDream/QXDream.php';
$controller = new Controller();
$controller->run();
?>