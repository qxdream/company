<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-02-14 后台入口 $
	@version  $Id: qx-admin.php 2011-02-14
*/

define('IN_ADMIN', TRUE);

require dirname(__FILE__) . '/QXDream/QXDream.php';
$controller = new Controller();
$controller->run();
?>