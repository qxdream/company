<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-21 ��װ�ļ���� $
	@version  $Id: index.php 2011-05-21
*/

define('APP_PATH', './install/'); //������appĿ¼

require dirname(__FILE__) . '/QXDream/QXDream.php';
$controller = new Controller();
$controller->run();
?>