<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ǰ̨��� $
	@version  $Id: index.php 2010-11-30
*/

define('APP_PATH', './company/'); //������appĿ¼

require dirname(__FILE__) . '/QXDream/QXDream.php';
$controller = new Controller();
$controller->run();
?>