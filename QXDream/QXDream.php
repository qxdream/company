<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 �����ļ� $
	@version  $Id: QXDream.php 1.0 2011-03-17
*/

(defined('APP_PATH') || defined('IN_ADMIN')) or die('<h1>Forbidden!</h1>');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set("magic_quotes_runtime", 0); //�ر��ַ��� \"' д�����ݿ�ʱת��

//��ʼ��������ȫ������
$GLOBALS['QXDREAM'] = $libs = array();
$GLOBALS['QXDREAM']['runtime_start'] = microtime(TRUE);

define('QX_ROOT', str_replace('\\','/',substr(dirname(__FILE__), 0, -7)));
define('IN_QX', TRUE); //�����ڰ����ļ��ӿ�

set_include_path(QX_ROOT . 'QXDream/libs/'); //���ð����ļ���·��Ŀ¼
require QX_ROOT . 'config.inc.php'; //�����ļ�
is_file('install.php') && !strstr(APP_PATH, 'install') && redirect(QX_PATH . 'install.php');
$libs = require 'core.inc.php'; //�����ļ��嵥
if(RUNTIME) {
	$runtime_file = QX_ROOT . 'QXDream/~runtime.php';
	if(!is_file($runtime_file)) {
		$code = '';
		foreach($libs as $file) { $code .= php_strip_whitespace($file); }
		file_put_contents($runtime_file, "<?php defined('IN_QX') or die('<h1>Forbidden!</h1>');" . str_replace(array("\n", "\t","\r","defined('IN_QX') or die('<h1>Forbidden!</h1>');"), '', preg_replace(array("/^[^<'\"]*<\?php/i", "/\?><\?php/"), '', $code)));
		chmod($runtime_file, 0777);
	}
	require $runtime_file;
	unset($runtime_file);
} else {
	foreach($libs as $file) { require $file; }
}
unset($libs);

function redirect($url) {
	header('Location: ' . $url);
	exit();
}
?>