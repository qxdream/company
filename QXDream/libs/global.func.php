<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ���������� $
	@version  $Id: global.func.php  2011-04-29
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2009-10-03
 * ����޸�ʱ��:       2009-10-03
 * ����:               ��ȡ�ļ���׺��,��php
 * ����:               @filenameΪ�ļ���(����׺)
 * ����ֵ:             ��
 */
function file_suffix($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}

//ȡ�ò�������׺���ļ���
function filename_remove_suffix($filename) {
	$file = '';
	$file = pathinfo($filename);
	return $file['filename'];
}

/**
 * ����:              ̤ѩ����
 *
 * ����ʱ��:          2009-10-03
 * ����޸�ʱ��:      2011-03-19
 * ����:              �����ļ���·��
 * ����:              @pathΪ�ļ���·��
 * ����ֵ:            ������·��
 */
function dir_path($path) {
	if(empty($path)) { return FALSE; }
	$path = str_replace('\\', '/', $path);
	//���һ���ַ�������б��,�ͼ���
	if(substr($path, -1) != '/') $path = $path .'/';
	return $path;
}

/**
 * ����:           ̤ѩ����
 *
 * ����ʱ��:       2009-10-03
 * ����޸�ʱ��:   2010-03-14
 * ����:           ����Ŀ¼(֧��a/b/c/d��)
 * ����:           @pathΪ�ļ���·��
 *                 ��dir_create(dirname(__FILE__) . '/23423/kk/123123/312231/m12');
 * ����ֵ:         �罨���ļ��г������ؼ�,���򷵻���
 */
function dir_create($path) {
	$dir = explode('/', dir_path($path));
	//ȥ�����һ������Ԫ��(��ջ),�������Ԫ��
	//array_push��ջ,���������һ��Ԫ���м���,�����������
	array_pop($dir);
	$cur_dir = '';
	foreach($dir as $k => $v) {
		$cur_dir .= $v . '/';
		//����ļ��д��ھ��������ѭ��
		if(is_dir($cur_dir)) continue;
		$result = '';
		$result = @mkdir($cur_dir, 0777);
		//����Ŀ¼��,�ٽ���һ���յ�index.htm
		if(!is_file($cur_dir . 'index.htm')) file_put_contents($cur_dir . 'index.htm', ' ');
		if(!$result) { 
			halt("File '", $cur_dir, "' cannot be created!");
			return FALSE;
		}
	}
	return TRUE;
}

//д����־
function write_log($file, $str) {
	//����a��ʾ��д�뷽ʽ��,���ļ�ָ��ָ���ļ�ĩβ,׷������
	//����ļ����������Դ���֮
	$handle = fopen($file, 'a');
	flock($handle, LOCK_EX);
	@fwrite($handle, $str);
	flock($handle, LOCK_UN);
	@fclose($handle);
	@chmod($file, 0777);
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-10-04
 * ����޸�ʱ��:     2011-03-06
 * ����:             �û�ͨ����Ϣ��ʾ
 * ����:             @showΪ��ʾ��Ϣ
 *                   @url_forwardΪ�Զ���ת��URL��ַ,Ĭ��(goback)�Ƿ�����ҳ
 *                    'stay'Ϊͣ���ڵ�ǰҳ��,����ת
 *                   @is_adminΪ1ʱ��ʾ��ִ̬�к�̨����
 *                   @millisecondΪ���������ת
 *                   @dynamic��HTML��ִ�ж�̬JS֮���
 * ����ֵ:           ��
 */
function show_msg($show = 'operation_success', $url_forward = 'goback', $is_admin = 0, $millisecond = 1500, $is_parent = FALSE, $dynamic = '') {
	extract($GLOBALS['QXDREAM'], EXTR_SKIP);//�Ѻ������$GLOBALS['QXDREAM']�������ȫ����ȡ����
	$l_arr = empty($is_admin) ? $language : $admin_language;
	if(isset($l_arr[$show])) eval("\$show = \"" . $l_arr[$show] . "\";"); //�����еı�����ִ��ʱ��û�ж����,Ҫ�õ���,��ʾʱ����eval
	$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=' . DB_CHARSET . '" />
			<title>' .  $l_arr['message_prompt'] . '</title>';
	if(defined('IN_ADMIN')) {
		$msg .= '<link href="' . VIEW_ADMIN_CSS . 'show_msg.css" rel="stylesheet" />';
	} else {
		$msg .=	'<style type="text/css">
				* { margin: 0; padding: 0; }
				body { font: 12px/1.8 \'����\', Arial, verdana; background: #f9f9f9; }
				#wrapper { border: 1px solid #ccc; background: #f7f7f7; width: 340px; padding: 5px; position: absolute; top: 20%; left: 50%; margin: -71px 0 0 -175px; padding: 5px; -moz-border-radius: 10px; -webkit-border-radius: 5px; }
				#in { width: 300px; background: #fff; padding: 10px 20px; overflow: hidden; word-wrap: break-word;  }
				h1 { font-size: 18px; font-weight: normal; font-family: \'����\'; text-align: center; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
				#red,a:hover { color: #bc2931; }
				a { color: #1f3a87; }
				b { color: #000; }
				</style>';
	}		
	$msg .= '</head>
			<body>
			<div id="wrapper">
			<div id="in">
			<h1>' . $l_arr['message_prompt'] . '</h1>
			<p id="red">' . $show . '</p>';
	if($url_forward == 'goback') {
		$msg .= '<a href="javascript:history.go(-1);" >'. $l_arr['message_goback'] . '</a>';
	} elseif($url_forward == 'stay') {
		$msg .= '';
	} else {
		$sec = $millisecond / 1000;
		eval("\$message_redirect_in_sec = \"" . $l_arr['message_redirect_in_sec'] . "\";"); //Ҫ��ʾ������ת,��eval��ʾ��������
		eval("\$message_click_to = \"" . $l_arr['message_click_to'] . "\";");
		$location = $is_parent ? 'parent.location' : 'location';
		$msg .= '<script type="text/javascript">setTimeout(function(){' . $location . '.href="' . $url_forward . '"},' . $millisecond . ');</script>
				<p>' . $message_redirect_in_sec . '</p>
				<p>' . $message_click_to  . '</p>';
	}
	$msg .= '<p id="exec_info">' . exec_info(0) . '</p>' . $dynamic . '
			</div>
			</div>
			</body>
			</html>';
	exit($msg);
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2010-02-23
 * ����޸�ʱ��:     2010-02-23
 * ����:             ����������Ϣ(������Ҫ��ִ�б���ʱ)
 * ����:             @showΪ��������Ԫ�ؼ�ֵ
 *                   @is_adminΪ1ʱ��ʾ��ִ̬�к�̨����
 * ����ֵ:           ������Ϣ
 */
function lang($show, $is_admin = 0) {
	extract($GLOBALS['QXDREAM'], EXTR_SKIP);
	$l_arr = empty($is_admin) ? $language : $admin_language;
	if(isset($l_arr[$show])) eval("\$show = \"" . $l_arr[$show] . "\";");
	return $show;
}

//�ѱ�����ӵ��Զ����ȫ��������
function add_globals($arr) {
	if(!is_array($arr)) { return FALSE; }
	foreach($arr as $k => $v) { $GLOBALS['QXDREAM'][$k] = $v; }
}

/**
 * ��ʾϵͳ������Ϣ(������ͷ�������ʱ)
 * @show string ������Ϣ
 * @arr array ������������
 * ���� ��
 */
function system_error($show, $arr = '') {
	if(DEBUG) { 
		if(is_array($arr)) { add_globals($arr); } 
	} else {
		$show = 'page_not_exists';
	}
	show_msg($show);
}

//�жϲ���,���Թر�ʱ������ʾ����
function halt($show, $arr = '', $debug = DEBUG) {
	if(empty($debug)) return FALSE;
	if(is_array($arr)) { add_globals($arr); }
	show_msg($show, 'stay');
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-01-07
 * ����޸�ʱ��:       2010-03-07
 * ����:               ִ�������Ϣ
 * ����:               ��
 * ����ֵ:             ��(��ʾִ��ʱ����,sqlִ�д���)
 */
function exec_info($display_time = 1) {
	if(IS_SHOW_EXEC_INFO == 0) return FALSE;
	$runtime_stop = microtime(TRUE);;
	$runtime = number_format(($runtime_stop - $GLOBALS['QXDREAM']['runtime_start']), 6);//��
	//ʱ���Ӽ���date_default_timezone_set�෴
	$time = empty($display_time) ? '' : 'GMT' . (TIMEOFFSET > 0 ? '+' . TIMEOFFSET : TIMEOFFSET) . ', ' . format_date('Y-m-d H:iA') . ', ';
	return $time . 'Processed in ' . $runtime .' second(s), ' . $GLOBALS['QXDREAM']['query_num']  . ' queries. ' . user_memory_size();
}

//��ȡӦ�ø�Ŀ¼,���� /
function app_root($app_path = '') {
	return QX_ROOT . APP_DIR . (empty($app_path) ? APP_PATH : $app_path);
}

//��ȡ�����������
function get_entry() {
	$entry = filename_remove_suffix(basename(PHP_SELF));
	return 'index' == $entry && REWRITE ? '' : $entry;
}
get_entry();

//��ȡӦ����ڵ�URL
function app_url($entry = '') {
	return dir_path(QX_PATH . (empty($entry) ? get_entry() : $entry) . (REWRITE ? '' : '.php'));
}

//����̨��Ŀ¼�µĺ�̨Ӧ�ó������빲�������
function load_admin_share() {
	define('IN_ADMIN', TRUE);
	$admin_share_action = QX_ROOT . APP_DIR . ADMIN_PATH . 'controllers/ShareAction.class.php';
	is_file($admin_share_action) && require_once $admin_share_action;
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-01-26
 * ����޸�ʱ��:       2010-11-27
 * ����:               �������ļ�
 * ����:               @filenameΪ�ļ���
 *                     @is_create_classΪ�Ƿ�ʵ����
 *                     @is_coreΪ�Ƿ��Ǻ���
 * ����ֵ:             is_create_classΪ1Ϊʵ��������
 */
function load_class($filename, $is_create_class = 1, $is_core = 1) {
	$filename = ucwords($filename);
	$file = (1 == $is_core ? QX_ROOT . 'QXDream/' : app_root()) . 'libs/' . $filename . '.class.php';
	is_file($file) ? require $file : halt("File '" . $file . "' not exists!"); 
	if(!empty($is_create_class)) {
		return new $filename();
	}
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-01-27
 * ����޸�ʱ��:       2010-02-23
 * ����:               �������԰�(Ҫ��������ı���ʱ��Ҫ����include,Ҫ�����뺯��ʱ�ں�����ǰ��include)
 * ����:               @filename���԰��ļ���
 * ����ֵ:             ����������($langΪϵͳ����,$languageΪ��������)
 */
function language($filename) {
	$language_pack = QX_ROOT . PUBLIC_DIR . 'language/' . LANG_PACK . '/' . $filename . '.lang.php';
	if(is_file($language_pack)) {
		return include $language_pack;
	} else {
		halt("Language package '" . $language_pack . "' not exists!");
	}
}

//��Ըÿ���޸ĵ�ǰurl
function repair_url() {
	$path_info_arr = get_path_info();
	$path_info_arr[0] = isset($path_info_arr[0]) ? $_GET['control'] : DEFAULT_CONTROL;
	$path_info_arr[1] = isset($path_info_arr[1]) ? $_GET['method'] : DEFAULT_METHOD;
	return app_url() . $path_info_arr[0] . '/' . $path_info_arr[1] . '/';
}

//��ȡ�����Ϳ��·�����/,�ֳ����鷵��
function get_path_info() {
	if(!PATH_INFO) return;
	return explode('/', trim(PATH_INFO, '/')); 
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-01-10
 * ����޸�ʱ��:       2010-10-31
 * ����:               ��ȡ��ǰ�ű���URL(����������������)
 * ����:               ��
 * ����ֵ:             ��ǰ�ű���URL
 */
function current_url() {
	return SCHEME . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-06-21
 * ����޸�ʱ��:     2009-10-30
 * ����:             ��HTML��ǩתΪԭ�����(�ⲿ�ύ������һ���������дHTML,һ��Ҫ���������,��ע���Աʱ)
 * ����:             @strΪҪת�������
 *                   ENT_QUOTESΪ�ѵ���˫��������
 * ����ֵ:           ת��������
 */
function my_htmlspecialchars($str) {
	return is_array($str) ? array_map('my_htmlspecialchars', $str) : htmlspecialchars($str, ENT_QUOTES);
}

//��������ַ�,�еĻ�������
function check_badword($str, $name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','`')) {
	foreach($name_key as $value){
		if (strpos($str, $value) !== FALSE) return TRUE;
	}
	return FALSE;
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-11
 * ����޸�ʱ��:     2009-10-30
 * ����:             ���ת���ַ�
 * ����:             @strΪ������ַ���
 * ����ֵ:           �������ת���ַ�������ַ���
 */
function slash($str) {
	return is_array($str) ? array_map('slash', $str) : addslashes($str);
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-10-30
 * ����޸�ʱ��:     2009-10-30
 * ����:             ȥ��ת���ַ�
 * ����:             @strΪ������ַ���
 * ����ֵ:           �Ƴ�ת���ַ���Ľ��
 */
function unslash($str) {
	return is_array($str) ? array_map('unslash', $str) : stripslashes($str);
}

/*
 * ����:              ̤ѩ����
 *
 * ����ʱ��:          2009-11-16
 * ����޸�ʱ��:      2009-11-16
 * ����:              �����ⲿ�ύ��sqlע��
 * ����:              @strΪ�ⲿ�ύ����
 * ����ֵ:            ���˺������
 */
function filter_sql($str) {
	$search = array('/union(\s*(\/\*.*\*\/)?\s*)+(\(\s*)*select/i', '/load_file(\s*(\/\*.*\*\/)?\s*)+\(/i', '/into(\s*(\/\*.*\*\/)?\s*)+outfile/i');
	$replace = array('union &nbsp; \\3 select', 'load_file &nbsp; (', 'into &nbsp; outfile'); //\\3��ƥ�������ţ���ֹUNIONע��
	return is_array($str) ? array_map('filter_sql', $str) : preg_replace($search, $replace, $str);
}


/**
 * ����:           ̤ѩ����
 *
 * ����ʱ��:       2009-08-01
 * ����޸�ʱ��:   2010-02-19
 * ����:           �Ե�λ���������С
 * ����:           @bytesԭ��С(�ֽ���)
 * ����ֵ:         ͳ�ƺ�Ĵ�С
 */
function size($bytes) {
	$arr = array('Byte', 'K', 'M', 'G', 'T', 'P');
	$unit = $arr[0]; //��С��λ;
	$count = count($arr);
	for($i = 1; $i < $count && $bytes > 1024; $i++) {
		$bytes /= 1024;
		$unit = $arr[$i];
	}
	return round($bytes, 2) . ' ' . $unit; //��������,����2λС��
}

//ͳ��PHP�����ڴ�ռ�ô�С,php>=4.3.2,php5
function user_memory_size() {
	return size(memory_get_usage());
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-04
 * ����޸�ʱ��:     2010-01-30
 * ����:             ����ʱ��
 * ����:             ��
 * ����ֵ:           ��
 */
function timezone() {
	$timeoffset = (TIMEOFFSET > 0 ? '-' : '+') . abs(TIMEOFFSET);
	if(function_exists('date_default_timezone_set')){
		@date_default_timezone_set('Etc/GMT' . $timeoffset);
	}else{
		@ini_set('date.timezone','Etc/GMT' . $timeoffset);
	}
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-04
 * ����޸�ʱ��:     2010-06-27
 * ����:             ��ʽ��ʱ��,�û����趨ʱ��
 * ����:             @formatΪʱ���ʽ����Y-m-d H:i:s
 *                   @timestampΪlinuxʱ���
 *                   @personalityΪʱ���Ƿ���������0ʱΪĬ�����
 * ����ֵ:           ��
 */
function format_date($format = '', $timestamp = '', $personality = 1) {
	if(!empty($personality) && !empty($timestamp)) {
		$second = time() - $timestamp;
		$today = get_date('Y-m-d', time());
		$yesterday = get_date('Y-m-d', time() - 86400);
		$two_day_before = get_date('Y-m-d', time() - 172800);
		$now_date = get_date('Y-m-d', $timestamp);
		if($now_date == $today) {
			if($second == 0) return $GLOBALS['QXDREAM']['language']['just'];
			if($second < 60) return $second . $GLOBALS['QXDREAM']['language']['second_before'];
			if($second < 3600) return  ceil($second / 60) . $GLOBALS['QXDREAM']['language']['minute_before'];
			if($second < 86400) return  ceil($second / 3600) . $GLOBALS['QXDREAM']['language']['hour_before'];
		}
		if($now_date == $yesterday) {
			return  $GLOBALS['QXDREAM']['language']['yesterday'] . ' ' . get_date('H:i', $timestamp);
		}
		if($now_date == $two_day_before) {
			return  $GLOBALS['QXDREAM']['language']['day_before_yesterday'] . ' ' . get_date('H:i', $timestamp);
		}
	}
	empty($timestamp) && $timestamp = time(); //@timestampΪ��,$timestamp�ŵ���time()
	return get_date($format, $timestamp);
}

//��ȡ����ʱ��
function get_date($format, $timestamp = '') {
	return gmdate($format, $timestamp + TIMEOFFSET * 3600);
}

//���ط�����ʱ��
function now() {
	return format_date('Y-m-d H:i:s');
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-12-07
 * ����޸�ʱ��:     2009-12-07
 * ����:             ��ȡʱ��������
 * ����:             @date1����
 *                   @date2����,����Ϊ��������
 * ����ֵ:           ʱ��������
 */	
function datediff($date1, $date2 = '') {
	if(empty($date2)) $date2 = now();
	return abs(ceil((strtotime($date1) - strtotime($date2))/86400));
}

//��������������
function software() {
	return $_SERVER['SERVER_SOFTWARE'];
}

//�����ڴ�ռ�ô�С,���˹���,@sizeΪ����(��)
//Ĭ��16M(���ϴ�ͼƬ�ڴ���䲻��,���ʵ�����)
//��Fatal error: Allowed memory size of 8388608 bytes exhausted
function memory_limit($size = 32) {
	@ini_set('memory_limit', $size . 'M'); 
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-04
 * ����޸�ʱ��:     2009-08-04
 * ����:             �ж��ļ��ϴ���Ϣ
 * ����:             ��
 * ����ֵ:           ������������ϴ��ļ���Ϣ,���򷵻ؽ�ֹ
 */
function is_file_uploads() {
	if(@ini_get('file_uploads')){
		return $GLOBALS['QXDREAM']['language']['max_allowed'] . ' <span class="blue">'.ini_get('upload_max_filesize') . '</span>';
	}else{
		return '<span class="red">' . $GLOBALS['QXDREAM']['language']['banned'] . '</span>';
	}
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-04
 * ����޸�ʱ��:     2009-08-04
 * ����:             �ж�register_globals,safe_mode,short_open_tag���ǿ���״̬
 * ����:             @$varname(magic_quotes_gpc,register_globals��)
 * ����ֵ:           ������������ϴ��ļ���Ϣ,���򷵻ؽ�ֹ
 */
function php_cfg($varname){
	switch(get_cfg_var($varname)){
		case 0:
			return '<span class="red">' . $GLOBALS['QXDREAM']['language']['off'] . '</span>';
		break;
		case 1:
			return '<span class="blue">' . $GLOBALS['QXDREAM']['language']['on'] . '</span>';
		break;
	}
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-08-04
 * ����޸�ʱ��:     2009-08-04
 * ����:             �ж�ͼ������״̬ GD Library
 * ����:             ��
 * ����ֵ:           ��������GD��Ϣ,���򷵻�δ����
 */
function gd_version(){
	if(function_exists('gd_info')){
		$arr = gd_info();
		return $arr['GD Version'];
	}else{
		return '<span class="red">' . $GLOBALS['QXDREAM']['language']['not_open'] . '</span>';
	}
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-09-28
 * ����޸�ʱ��:     2009-09-28
 * ����:             ����cookie
 * ����:             @cookie_nameΪcookie����
 *                   @cookie_valueΪcookie��ֵ
 *                   @timeΪcookie��ʱ��,������1Сʱ�����,time()+3600
 *                   ��ʼʱҪ�����⼸������
 *					 define('COOKIE_DOMAIN', ''); //Cookie ������
 *					 define('COOKIE_PATH', '/'); //Cookie ����·��
 *					 define('COOKIE_PRE', 'ATgagpaxdz'); //Cookie ǰ׺
 * ����ֵ:           ��
 */
function set_cookie($cookie_name, $cookie_value = '', $expires = 0) {
	//���cookie_valueΪ�գ����������cookie
	$expires = $expires > 0 ? $expires : 0;
	$is_security = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	if(is_array($cookie_name)) {
		foreach($cookie_name as $k => $v) {
			setcookie(COOKIE_PRE . $v, $cookie_value[$v], $expires, COOKIE_PATH, COOKIE_DOMAIN, $is_security);
		}
	} else {
		setcookie(COOKIE_PRE . $cookie_name, $cookie_value, $expires, COOKIE_PATH, COOKIE_DOMAIN, $is_security);
	}
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-09-28
 * ����޸�ʱ��:     2009-09-28
 * ����:             ����cookie
 * ����:             @cookie_nameΪcookie����
 * ����ֵ:           ����cookie��ֵ
 */
function get_cookie($cookie_name) {
	//COOKIE_PRE������cookie��ǰ׺
	$cookie_name = COOKIE_PRE . $cookie_name;
	return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : FALSE;
}

//���cookie
function clear_cookie($cookie_name) {
	$is_security = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
	if(is_array($cookie_name)) {
		foreach($cookie_name as $k => $v) {
			setcookie(COOKIE_PRE . $v, '', time()-24*3600*1000, COOKIE_PATH, COOKIE_DOMAIN, $is_security);
		}
	} else {
		setcookie(COOKIE_PRE . $cookie_name, '', time()-24*3600*1000, COOKIE_PATH, COOKIE_DOMAIN, $is_security);
	}
}

//��ȡIP
function get_ip() {
	//��ȡip
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$online_ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$online_ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$online_ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$online_ip = $_SERVER['REMOTE_ADDR'];
	}
	//��PHP5��,������������magic_quotes_gpcӰ��,HTTP_X_FORWARDED_FOR������COOKIE��α��
	//������ת���������
	$online_ip = addslashes($online_ip);
	preg_match("/[\d\.]{7,15}/", $online_ip, $online_ip_matches); //ƥ��7-15λ�����ֻ��
	return isset($online_ip_matches[0]) ? $online_ip_matches[0] : 'unknown';
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-02-20
 * ����޸�ʱ��:       2011-04-02
 * ����:               �����Ƿ���д����
 * ����:               @form_varsΪ������,��$_GET,$_POST
 *                     @skip_varsΪ��ѡ����д�ı���
 * ����ֵ:             ������֤������,���������ؼ�
 */
function filled_out($form_vars, $skip_vars = '') {
	!is_array($form_vars) && halt("Parameter '" . $form_vars . "' is not array!");
	foreach($form_vars as $k => $v) {
		if(is_array($v)) { continue; }
		//����ѡ����д�ı���
		if(!empty($skip_vars)) {
			if(is_array($skip_vars) && in_array($k, $skip_vars) || !is_array($skip_vars) && $k == $skip_vars) continue;
		}
		$form_vars[$k] = trim($v);
		if((!isset($v)) || empty($form_vars[$k])) {
			return FALSE;
		}
	}
	return $form_vars;
}

//ȥ���������β�ո񣬽����ڶ�ά
function my_trim($form_vars, $skip_vars = '') {
	!is_array($form_vars) && halt("Parameter '" . $form_vars . "' is not array!");
	foreach($form_vars as $k => $v) {
		//����ѡ����д�ı���
		if(!empty($skip_vars)) {
			if(is_array($skip_vars) && in_array($k, $skip_vars) || !is_array($skip_vars) && $k == $skip_vars) continue;
		}
		$form_vars[$k] = trim($v);
	}
	return $form_vars;
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-12-10
 * ����޸�ʱ��:     2009-12-10
 * ����:             ȷ����֤��
 * ����:             @codeΪ�Ŀ��ύ����֤���ַ���
 *                   @enableΪ�Ƿ�����֤��
 *                   @session_code_nameΪseesion��֤���ַ���
 * ����ֵ:           ��֤��û��������֤�뿪��������ȷʱ������
 *                   �����뿪��������󷵻ؼ�
 */
function check_code($code, $enable = 1, $session_code_name = 'check_code') {
	if(!$enable) return TRUE; //û�п���������
	if(!isset($_SESSION[$session_code_name])) halt('session code is undefined!');
	if(strtolower($code) != strtolower($_SESSION[$session_code_name])) {
		unset($_SESSION[$session_code_name]);
		return FALSE;
	}
	unset($_SESSION[$session_code_name]);
	return TRUE;
}

/**
 * �������
 * ����:             @strҪ���ܵ��ַ���
 *                   @actionĬΪΪ����,DECODEΪ����
 *                   �������֤��set_cookie('qx_author', author_code($uid . "\t" . $username), time() + 86400 * 365);
 *                   (����ǰ̨һ���Ա) list($qx_uid, $qx_username) = explode("\t", get_cookie('qx_author') ? auth_code(get_cookie('qx_author'), 'DECODE') : array('', ''));
 * ����ֵ:           ���ܺ���ַ���
 */
function auth_code($str, $action = 'ENCODE') {
	//$key = substr(md5($_SERVER['HTTP_USER_AGENT'] . QX_KEY) , 20, 29);��$_SERVERʱ�������swfupload�����BUG,����swfupload�ϴ�ʱ
	$key = substr(md5(QX_KEY) , 20, 29);
	$len = strlen($key);
	$str = $action == 'ENCODE' ? $str : base64_decode($str);
	$code = '';
	for($i = 0; $i < strlen($str); $i++){
		$k      = $i % $len;
		$code  .= $str[$i] ^ $key[$k];
	}
	$code = $action == 'DECODE' ? $code : base64_encode($code);
	return $code;
}

/**
 * ��ȡ��ɫ����
 * ����:             @group_idΪ������ID
 * ����ֵ:           ��ɫ����
 */
function get_role_name($group_id) {
	return isset($GLOBALS['QXDREAM']['USER_GROUP'][$group_id]['group_name']) ? $GLOBALS['QXDREAM']['USER_GROUP'][$group_id]['group_name'] : '';
}

// ת��ʱ�䵥λ:�� to XXX,���������ݿ�����ʱ��
function format_timespan($seconds = '') {
	if ($seconds == '') $seconds = 1;
	$str = '';
	$years = floor($seconds / 31536000);
	if ($years > 0) {
		$str .= $years.$GLOBALS['QXDREAM']['language']['year'].', ';
	}
	$seconds -= $years * 31536000;
	$months = floor($seconds / 2628000);
	if ($years > 0 || $months > 0) {
		if ($months > 0) {
			$str .= $months.$GLOBALS['QXDREAM']['language']['month'].', ';
		}
		$seconds -= $months * 2628000;
	}
	$weeks = floor($seconds / 604800);
	if ($years > 0 || $months > 0 || $weeks > 0) {
		if ($weeks > 0)	{
			$str .= $weeks.$GLOBALS['QXDREAM']['language']['week'].', ';
		}
		$seconds -= $weeks * 604800;
	}
	$days = floor($seconds / 86400);
	if ($months > 0 || $weeks > 0 || $days > 0) {
		if ($days > 0) {
			$str .= $days.$GLOBALS['QXDREAM']['language']['day'].', ';
		}
		$seconds -= $days * 86400;
	}
	$hours = floor($seconds / 3600);
	if ($days > 0 || $hours > 0) {
		if ($hours > 0) {
			$str .= $hours.$GLOBALS['QXDREAM']['language']['hour'].', ';
		}
		$seconds -= $hours * 3600;
	}
	$minutes = floor($seconds / 60);
	if ($days > 0 || $hours > 0 || $minutes > 0) {
		if ($minutes > 0) {
			$str .= $minutes.$GLOBALS['QXDREAM']['language']['minute'].', ';
		}
		$seconds -= $minutes * 60;
	}
	if ($str == '') {
		$str .= $seconds.$GLOBALS['QXDREAM']['language']['second'].', ';
	}
	$str = substr(trim($str), 0, -1);
	return $str;
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2010-01-02
 * ����޸�ʱ��:     2010-01-02
 * ����:             ��ȡ�����ļ�
 * ����:             @filename�����ļ�����
 *                   @suffix�����ļ���׺��
 *                   @filepath�����ļ�·��
 * ����ֵ:           �����ļ�������
 */
function cache_read($filename, $suffix = CACHE_FILE_SUFFIX, $filepath = CACHE_PATH) {
	$file = $filepath . $filename . $suffix;
	return @include $file;
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2010-01-02
 * ����޸�ʱ��:     2010-01-02
 * ����:             д�뻺���ļ�
 * ����:             @filename�����ļ�����
 *                   @arrayд�������(����������)
 *                   @suffix�����ļ���׺��
 *                   @filepath�����ļ�·��
 * ����ֵ:           д�뻺���ļ������ݳ���
 */
function cache_write($filename, $array, $suffix = CACHE_FILE_SUFFIX, $filepath = CACHE_PATH) {
	if(!is_array($array)) halt('Parameter \'' . $array . '\' is not array!');
	$file = $filepath . $filename . $suffix;
	$array = "<?php\nreturn " . var_export($array, TRUE) . "\n?>";
	$strlen = file_put_contents($file, $array);
	@chmod($file, 0777);
	return $strlen;
}

/**
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2010-01-02
 * ����޸�ʱ��:     2010-01-02
 * ����:             ɾ�������ļ�
 * ����:             @filename�����ļ�����
 *                   @suffix�����ļ���׺��
 *                   @filepath�����ļ�·��
 * ����ֵ:           ��Դ���
 */
function cache_delete($filename, $suffix = CACHE_FILE_SUFFIX, $filepath = CACHE_PATH) {
	$file = $filepath . $filename . $suffix;
	return @unlink($file);
}

/**
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-01-27
 * ����޸�ʱ��:       2010-03-02
 * ����:               ����fck�༭��
 * @input_id  string   �ı����ֵ
 * @toolbar   string   ������
 * @width     int      ���
 * @height    int      �߶�
 * ����ֵ              ����fckeditor�༭����JS�����ַ���
 */
function editor($textarea_id = 'content', $toolbar = 'Default', $width = '', $height = '') {
	$skin_str = defined('IN_ADMIN') ? 'oFCKeditor.skinPlan = "' . ADMIN_PLAN . '";' : '';
	$str = '';
	if(!defined('LOADEDITOR')) {
		$str .= '<script type="text/javascript" src="' . SITE_URL . PUBLIC_DIR . 'fckeditor/fckeditor.js"></script>';
		define('LOADEDITOR', TRUE);
	}
	$str .= '<script type="text/javascript">
				var sBasePath = "' . SITE_URL . PUBLIC_DIR . 'fckeditor/";
				var oFCKeditor = new FCKeditor("' . $textarea_id . '") ;
				oFCKeditor.BasePath = sBasePath;' . $skin_str . '
				oFCKeditor.Height = "'.$height.'";
				oFCKeditor.Width = "'.$width.'"; 
				oFCKeditor.ToolbarSet = "'.$toolbar.'";
				oFCKeditor.ReplaceTextarea();
			</script>';
	return $str;
}

/**
 * �޸������ļ�
 * @config array     Ҫд���������Ϣ,Ҫ��һ������
 * ����ֵ:           ��
 */
function set_config($config) {
	if(!is_array($config)) return FALSE;
	$configfile = QX_ROOT . 'config.inc.php';
	if(!is_writable($configfile)) show_msg('Please chmod ./config.inc.php to 0777 !');
	$pattern = $replacement = array();
	foreach($config as $k=>$v) {
		$k = strtoupper($k);
		$pattern[$k] = "/define\(\s*['\"]".$k."['\"]\s*,\s*([']?)[^']*([']?)\s*\)/is";
        $replacement[$k] = "define('".$k."', \${1}".$v."\${2})";
	}
	$str = file_get_contents($configfile);
	$str = preg_replace($pattern, $replacement, $str);
	file_put_contents($configfile, $str);
}

/**
 * ת��Ϊ���ڵ���0������(��Ȼ��),��ֹ�ύ������ID
 * @mixed_var  all   Ҫת���ı���
 * ����ֵ:           ת���ɵ�����
 */
function nature_val($mixed_var) {
	$mixed_var = intval($mixed_var);
	return $number = $mixed_var < 0 ? 0 : $mixed_var;
}

/**
 * ��ȡ��˾UID
 * @company_id  int  ��˾ID
 * ����ֵ:           ��˾UID
 */
function get_company_uid($company_id) {
	return $GLOBALS['QXDREAM']['COMPANY'][$company_id]['company_uid'];
}

/**
 * ��ȡ���ύ��ַ���뷽������
 * @para  string     ��������
 * ����ֵ:           ���ύ����URL
 */
function get_frm_url($para = '') {
	if('add' == $_GET['method']) { 
		$action_url = get_action_url(); 
	} elseif('edit' == $_GET['method']) { 
		$action_url = get_action_url() . $_GET['control'] . '_id/' . $_GET[$_GET['control'] . '_id'] . '/';
	}
	return $action_url . $para;
}

//��ȡ����������ȫ��ַ
function get_action_url() {
	return app_url() . $_GET['control'] . '/' . $_GET['method'] . '/';
}

/**
 * ��ϷǷ��ύ�ֶ�,��:stop_fields($_POST['user'], array('group_id'));
 * @post_arr            array     �ύ������
 * @not_allowed_fields  array     ����������ֶ�
 * ����ֵ:                        ���ύ����URL
 */
function check_fields($post_arr, $allowed_fields) {
	$diff_arr = array_diff(array_keys($post_arr), $allowed_fields);
	if(count($diff_arr) > 0) {
		$fields = DEBUG ? '<br /><b>Fields</b>��' . implode(',', $diff_arr) . '</b>' : '';
		show_msg('Sorry, your post exists not allowed field!' . $fields);
	}
}

/**
 * �ж��Ƿ��Ǵ�ʼ��
 * @user_id  int    �û�ID
 * ����ֵ��  boolen �Ƿ�����
 */
function is_creator($user_id) {
	if(strstr(CREATOR, ',') && $user_id == CREATOR) return TRUE;
	$arr = explode(',', CREATOR);
	if(in_array($user_id, $arr)) return TRUE;
}

/**
 * ��ʾ��ǰ�򵼱��⣬��: δ����(1) | �Ѵ���(2)
 * @arr            array   ����
 * @cur_get_name   string  ��ǰ$_GET������
 * @other_get_str  string  ������query����
 *  ��: array(0 => array('link_val' => 0, 'text' => '������', 'count' => 12))
 * ����ֵ��        string  ��HTML
 */
function display_guide($arr, $cur_get_name, $other_get_str = '') {
	$str = '';
	foreach($arr as $k => $v) {
		if(0 == $v['count']) { continue; }
		if(isset($_GET[$cur_get_name]) && $_GET[$cur_get_name] == $v['link_val']) {
			$str .= '<span><b>' . $v['text'] . '</b>(' . $v['count'] . ')</span> | ';
		} else {
			$str .= '<a href="' . get_action_url() . $cur_get_name . '/' . $v['link_val'] . '/' . $other_get_str . '">' . $v['text'] . '(' . $v['count'] . ')</a> | ';
		}
	}
	return rtrim($str, ' | ');
}

/**
 * ƴ��SQL��������
 * @where      string  ����
 * @has_where  boolen  �Ƿ���������
 * ����ֵ��    string  ����
 */
function append_where(&$sql_where, $has_where, $field) {
    return $sql_where .= $has_where ? ' AND ' . $field : ' WHERE ' . $field;
}

/**
 * �������
 * @data       array   ����
 * ����ֵ��    ��
 */
if(DEBUG) {
	function dump($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

/**
 * ��ȡ������������
 * @type       int     ����ֵ
 * ����ֵ��    string  ��������
 */
function get_type_name($type) {
	$arr = array(0 => $GLOBALS['QXDREAM']['admin_language']['content_category'], 1 => '<span class="blue">' . $GLOBALS['QXDREAM']['admin_language']['lonely_page'] . '</span>', 2 => '<span class="red">' . $GLOBALS['QXDREAM']['admin_language']['external_link'] . '</span>');
	return $arr[$type];
}

/*
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-03-05
 * ����޸�ʱ��:       2010-03-05
 * ����:               ��ȡĳһǰ׺��ģ��
 * ����:               @prefixΪǰ׺
 *                     @suffixΪ��׺
 * ����ֵ:             ����,��Ϊ������׺��ģ����,ֵΪ����׺��ģ����
 */
function get_template($prefix, $suffix = '.tpl.php') {
	if(empty($GLOBALS['QXDREAM']['qx_company_uid'])) { return; }
	$file = VIEW_COMPANY_ROOT . $prefix . '*' . $suffix;
	$file_data = glob($file);
	$data = array();
	foreach($file_data as $v) {
		$key = substr(basename($v),0, -strlen($suffix));
		$data[$key] = basename($v);
	}
	return $data;
}

/**
 * ���빫˾·��
 * ��˾company_uid/��˾������+����/����xinyu/����xinyu/index/
 * ����      ��
 * ����ֵ��  ǰ̨��˾��������
 */
function load_company_routing() {
	require CONTROLLERS_ROOT . 'CompanyAction.class.php';
	$arr = explode('_', $_GET['method']);
	$action = ucwords($arr[0]) . 'Action';
	$file = CONTROLLERS_ROOT . 'Company' . $action . '.class.php';
	if(!is_file($file)) {
		system_error('control_file_not_exists', array('control_file' => $file));
	}
	require $file;
	if(!class_exists('Company' . $action)) {
		system_error('control_class_not_exists', array('control_class' => 'Company' . $action));
	}
	define('VIEW_PLAN', $_GET['control']); //��ǰ��˾����
	define('VIEW_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/' . VIEW_PLAN . '/'); //��ǰ��˾ǰ̨��ͼĿ¼
	define('VIEW_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . VIEW_PLAN . '/css/'); //��ǰ��˾ǰ̨��ͼCSS
	return $action;
}

/*
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-03-07
 * ����޸�ʱ��:       2010-03-07
 * ����:               ɾ��ĳ��ID
 * ����:               @target_idΪ��������ɾ,��'1,2,3'
 *                     @delete_idΪɾ����Щid,��'2,3'
 * ����ֵ:             ����ɾ�����id,�������������1,�ַ���
 */
function delete_cat_id($target_id, $delete_id) {
	return implode(',', array_diff(explode(',', $target_id), explode(',', $delete_id)));
}

/*
 * ����:               ̤ѩ����
 *
 * ����ʱ��:           2010-03-26
 * ����޸�ʱ��:       2010-03-26
 * ����:               ��ֹؼ���Ϊ����
 * ����:               @strΪ�ؼ����ַ���
 *                     @number��ֺ�ȡ��������������
 * ����ֵ:             ��ֺ������
 */
function explode_keyword($str, $number, $split = ',') {
	return array_values(array_unique(array_slice(array_filter(explode($split, $str)), 0, $number)));
}

/**
 * ��ά��������
 * @needle    string ������ֵ
 * @haystack  array  ����
 * ����ֵ��   array  �ҵ����ظ�����,û�ҵ����ؼ�
 */
function multi_array_search($needle, $haystack) {
	while($row = current($haystack)) {
		if(array_search($needle, $row)) { return $row; }
		next($haystack);
	}
	return FALSE;
}

/*
 * ����:             ̤ѩ����
 *
 * ����ʱ��:         2009-01-11
 * ����޸�ʱ��:     2009-01-12
 * ����:             ��ʾͼ��
 * ����:             @srcΪͼ���ַ
 *                   @altΪͼ����
 *                   @widthΪͼ���
 *                   @heightΪͼ���
 * ����ֵ:           ͼ��HTML����
 */
function create_img($src, $alt = '', $width = '', $height = '') {
	$width  = empty($width)  ? '' :  ' width="' . $width . '"';
	$height = empty($height) ? '' : ' height="' . $height . '"';
	$img = '<img src="' . $src . '" alt="' . $alt . '"' . $width . $height . ' />';
	return $img;
}
?>