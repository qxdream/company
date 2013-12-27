<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-30 公共函数库 $
	@version  $Id: global.func.php  2011-04-29
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2009-10-03
 * 最后修改时间:       2009-10-03
 * 功能:               获取文件后缀名,如php
 * 参数:               @filename为文件名(带后缀)
 * 返回值:             无
 */
function file_suffix($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1)));
}

//取得不包含后缀的文件名
function filename_remove_suffix($filename) {
	$file = '';
	$file = pathinfo($filename);
	return $file['filename'];
}

/**
 * 作者:              踏雪残情
 *
 * 建立时间:          2009-10-03
 * 最后修改时间:      2011-03-19
 * 功能:              处理文件夹路径
 * 参数:              @path为文件夹路径
 * 返回值:            处理后的路径
 */
function dir_path($path) {
	if(empty($path)) { return FALSE; }
	$path = str_replace('\\', '/', $path);
	//最后一个字符不是正斜扛,就加上
	if(substr($path, -1) != '/') $path = $path .'/';
	return $path;
}

/**
 * 作者:           踏雪残情
 *
 * 建立时间:       2009-10-03
 * 最后修改时间:   2010-03-14
 * 功能:           建立目录(支持a/b/c/d型)
 * 参数:           @path为文件夹路径
 *                 如dir_create(dirname(__FILE__) . '/23423/kk/123123/312231/m12');
 * 返回值:         如建立文件夹出错将返回假,否则返回真
 */
function dir_create($path) {
	$dir = explode('/', dir_path($path));
	//去除最后一个数组元素(出栈),返回这个元素
	//array_push入栈,在数组最后一个元糸中加入,返回数组个数
	array_pop($dir);
	$cur_dir = '';
	foreach($dir as $k => $v) {
		$cur_dir .= $v . '/';
		//如果文件夹存在就跳过这次循环
		if(is_dir($cur_dir)) continue;
		$result = '';
		$result = @mkdir($cur_dir, 0777);
		//建立目录后,再建立一个空的index.htm
		if(!is_file($cur_dir . 'index.htm')) file_put_contents($cur_dir . 'index.htm', ' ');
		if(!$result) { 
			halt("File '", $cur_dir, "' cannot be created!");
			return FALSE;
		}
	}
	return TRUE;
}

//写入日志
function write_log($file, $str) {
	//参数a表示以写入方式打开,将文件指针指向文件末尾,追加内容
	//如果文件不存在则尝试创建之
	$handle = fopen($file, 'a');
	flock($handle, LOCK_EX);
	@fwrite($handle, $str);
	flock($handle, LOCK_UN);
	@fclose($handle);
	@chmod($file, 0777);
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-10-04
 * 最后修改时间:     2011-03-06
 * 功能:             用户通用信息提示
 * 参数:             @show为提示消息
 *                   @url_forward为自动跳转的URL地址,默认(goback)是返回上页
 *                    'stay'为停留在当前页面,不跳转
 *                   @is_admin为1时表示动态执行后台语言
 *                   @millisecond为几毫秒后跳转
 *                   @dynamic在HTML里执行动态JS之类的
 * 返回值:           无
 */
function show_msg($show = 'operation_success', $url_forward = 'goback', $is_admin = 0, $millisecond = 1500, $is_parent = FALSE, $dynamic = '') {
	extract($GLOBALS['QXDREAM'], EXTR_SKIP);//把函数外的$GLOBALS['QXDREAM']数组变量全部提取出来
	$l_arr = empty($is_admin) ? $language : $admin_language;
	if(isset($l_arr[$show])) eval("\$show = \"" . $l_arr[$show] . "\";"); //数组中的变量在执行时是没有定义的,要用单引,显示时再用eval
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
				body { font: 12px/1.8 \'宋体\', Arial, verdana; background: #f9f9f9; }
				#wrapper { border: 1px solid #ccc; background: #f7f7f7; width: 340px; padding: 5px; position: absolute; top: 20%; left: 50%; margin: -71px 0 0 -175px; padding: 5px; -moz-border-radius: 10px; -webkit-border-radius: 5px; }
				#in { width: 300px; background: #fff; padding: 10px 20px; overflow: hidden; word-wrap: break-word;  }
				h1 { font-size: 18px; font-weight: normal; font-family: \'黑体\'; text-align: center; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
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
		eval("\$message_redirect_in_sec = \"" . $l_arr['message_redirect_in_sec'] . "\";"); //要显示几秒跳转,用eval显示变量语言
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2010-02-23
 * 最后修改时间:     2010-02-23
 * 功能:             返回语言信息(里面需要有执行变量时)
 * 参数:             @show为语言数组元素键值
 *                   @is_admin为1时表示动态执行后台语言
 * 返回值:           语言信息
 */
function lang($show, $is_admin = 0) {
	extract($GLOBALS['QXDREAM'], EXTR_SKIP);
	$l_arr = empty($is_admin) ? $language : $admin_language;
	if(isset($l_arr[$show])) eval("\$show = \"" . $l_arr[$show] . "\";");
	return $show;
}

//把变量添加到自定义的全局数组里
function add_globals($arr) {
	if(!is_array($arr)) { return FALSE; }
	foreach($arr as $k => $v) { $GLOBALS['QXDREAM'][$k] = $v; }
}

/**
 * 显示系统出错信息(载入类和方法出错时)
 * @show string 出错信息
 * @arr array 出错语言数组
 * 返回 无
 */
function system_error($show, $arr = '') {
	if(DEBUG) { 
		if(is_array($arr)) { add_globals($arr); } 
	} else {
		$show = 'page_not_exists';
	}
	show_msg($show);
}

//中断操作,调试关闭时不会显示错误
function halt($show, $arr = '', $debug = DEBUG) {
	if(empty($debug)) return FALSE;
	if(is_array($arr)) { add_globals($arr); }
	show_msg($show, 'stay');
}

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-01-07
 * 最后修改时间:       2010-03-07
 * 功能:               执行相关信息
 * 参数:               无
 * 返回值:             无(显示执行时间数,sql执行次数)
 */
function exec_info($display_time = 1) {
	if(IS_SHOW_EXEC_INFO == 0) return FALSE;
	$runtime_stop = microtime(TRUE);;
	$runtime = number_format(($runtime_stop - $GLOBALS['QXDREAM']['runtime_start']), 6);//秒
	//时区加减与date_default_timezone_set相反
	$time = empty($display_time) ? '' : 'GMT' . (TIMEOFFSET > 0 ? '+' . TIMEOFFSET : TIMEOFFSET) . ', ' . format_date('Y-m-d H:iA') . ', ';
	return $time . 'Processed in ' . $runtime .' second(s), ' . $GLOBALS['QXDREAM']['query_num']  . ' queries. ' . user_memory_size();
}

//获取应用根目录,最后带 /
function app_root($app_path = '') {
	return QX_ROOT . APP_DIR . (empty($app_path) ? APP_PATH : $app_path);
}

//获取程序入口名称
function get_entry() {
	$entry = filename_remove_suffix(basename(PHP_SELF));
	return 'index' == $entry && REWRITE ? '' : $entry;
}
get_entry();

//获取应用入口的URL
function app_url($entry = '') {
	return dir_path(QX_PATH . (empty($entry) ? get_entry() : $entry) . (REWRITE ? '' : '.php'));
}

//非主台后目录下的后台应用程序载入共享控制器
function load_admin_share() {
	define('IN_ADMIN', TRUE);
	$admin_share_action = QX_ROOT . APP_DIR . ADMIN_PATH . 'controllers/ShareAction.class.php';
	is_file($admin_share_action) && require_once $admin_share_action;
}

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-01-26
 * 最后修改时间:       2010-11-27
 * 功能:               载入类文件
 * 参数:               @filename为文件名
 *                     @is_create_class为是否实例化
 *                     @is_core为是否是核心
 * 返回值:             is_create_class为1为实例化的类
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
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-01-27
 * 最后修改时间:       2010-02-23
 * 功能:               载入语言包(要引用里面的变量时不要返回include,要在载入函数时在函数名前加include)
 * 参数:               @filename语言包文件名
 * 返回值:             包括的语言($lang为系统语言,$language为界面语言)
 */
function language($filename) {
	$language_pack = QX_ROOT . PUBLIC_DIR . 'language/' . LANG_PACK . '/' . $filename . '.lang.php';
	if(is_file($language_pack)) {
		return include $language_pack;
	} else {
		halt("Language package '" . $language_pack . "' not exists!");
	}
}

//针对该框架修改当前url
function repair_url() {
	$path_info_arr = get_path_info();
	$path_info_arr[0] = isset($path_info_arr[0]) ? $_GET['control'] : DEFAULT_CONTROL;
	$path_info_arr[1] = isset($path_info_arr[1]) ? $_GET['method'] : DEFAULT_METHOD;
	return app_url() . $path_info_arr[0] . '/' . $path_info_arr[1] . '/';
}

//获取域名和框架路径后的/,分成数组返回
function get_path_info() {
	if(!PATH_INFO) return;
	return explode('/', trim(PATH_INFO, '/')); 
}

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-01-10
 * 最后修改时间:       2010-10-31
 * 功能:               获取当前脚本的URL(带参数，包括域名)
 * 参数:               无
 * 返回值:             当前脚本的URL
 */
function current_url() {
	return SCHEME . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-06-21
 * 最后修改时间:     2009-10-30
 * 功能:             把HTML标签转为原义输出(外部提交的数据一定如果不让写HTML,一定要做这个过滤,如注册会员时)
 * 参数:             @str为要转义的内容
 *                   ENT_QUOTES为把单引双引都编码
 * 返回值:           转义后的内容
 */
function my_htmlspecialchars($str) {
	return is_array($str) ? array_map('my_htmlspecialchars', $str) : htmlspecialchars($str, ENT_QUOTES);
}

//检测敏感字符,有的话返回真
function check_badword($str, $name_key = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n",'#','`')) {
	foreach($name_key as $value){
		if (strpos($str, $value) !== FALSE) return TRUE;
	}
	return FALSE;
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-11
 * 最后修改时间:     2009-10-30
 * 功能:             添加转义字符
 * 参数:             @str为数组或字符串
 * 返回值:           返回填加转义字符数组或字符串
 */
function slash($str) {
	return is_array($str) ? array_map('slash', $str) : addslashes($str);
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-10-30
 * 最后修改时间:     2009-10-30
 * 功能:             去除转义字符
 * 参数:             @str为数组或字符串
 * 返回值:           移除转义字符后的结果
 */
function unslash($str) {
	return is_array($str) ? array_map('unslash', $str) : stripslashes($str);
}

/*
 * 作者:              踏雪残情
 *
 * 建立时间:          2009-11-16
 * 最后修改时间:      2009-11-16
 * 功能:              过滤外部提交的sql注入
 * 参数:              @str为外部提交数据
 * 返回值:            过滤后的内容
 */
function filter_sql($str) {
	$search = array('/union(\s*(\/\*.*\*\/)?\s*)+(\(\s*)*select/i', '/load_file(\s*(\/\*.*\*\/)?\s*)+\(/i', '/into(\s*(\/\*.*\*\/)?\s*)+outfile/i');
	$replace = array('union &nbsp; \\3 select', 'load_file &nbsp; (', 'into &nbsp; outfile'); //\\3子匹配左括号，防止UNION注入
	return is_array($str) ? array_map('filter_sql', $str) : preg_replace($search, $replace, $str);
}


/**
 * 作者:           踏雪残情
 *
 * 建立时间:       2009-08-01
 * 最后修改时间:   2010-02-19
 * 功能:           以单位算出容量大小
 * 参数:           @bytes原大小(字节数)
 * 返回值:         统计后的大小
 */
function size($bytes) {
	$arr = array('Byte', 'K', 'M', 'G', 'T', 'P');
	$unit = $arr[0]; //最小单位;
	$count = count($arr);
	for($i = 1; $i < $count && $bytes > 1024; $i++) {
		$bytes /= 1024;
		$unit = $arr[$i];
	}
	return round($bytes, 2) . ' ' . $unit; //四舍五入,保留2位小数
}

//统计PHP程序内存占用大小,php>=4.3.2,php5
function user_memory_size() {
	return size(memory_get_usage());
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-04
 * 最后修改时间:     2010-01-30
 * 功能:             设置时区
 * 参数:             无
 * 返回值:           无
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-04
 * 最后修改时间:     2010-06-27
 * 功能:             格式化时间,用户可设定时区
 * 参数:             @format为时间格式，如Y-m-d H:i:s
 *                   @timestamp为linux时间戳
 *                   @personality为时间是否个性输出，0时为默认输出
 * 返回值:           无
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
	empty($timestamp) && $timestamp = time(); //@timestamp为空,$timestamp才等于time()
	return get_date($format, $timestamp);
}

//获取日期时间
function get_date($format, $timestamp = '') {
	return gmdate($format, $timestamp + TIMEOFFSET * 3600);
}

//返回服务器时间
function now() {
	return format_date('Y-m-d H:i:s');
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-12-07
 * 最后修改时间:     2009-12-07
 * 功能:             获取时间天数差
 * 参数:             @date1日期
 *                   @date2日期,给空为现在日期
 * 返回值:           时间天数差
 */	
function datediff($date1, $date2 = '') {
	if(empty($date2)) $date2 = now();
	return abs(ceil((strtotime($date1) - strtotime($date2))/86400));
}

//服务器解译引擎
function software() {
	return $_SERVER['SERVER_SOFTWARE'];
}

//设置内存占用大小,不宜过大,@size为数字(兆)
//默认16M(如上传图片内存分配不够,可适当增加)
//如Fatal error: Allowed memory size of 8388608 bytes exhausted
function memory_limit($size = 32) {
	@ini_set('memory_limit', $size . 'M'); 
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-04
 * 最后修改时间:     2009-08-04
 * 功能:             判断文件上传信息
 * 参数:             无
 * 返回值:           开启返回最大上传文件信息,否则返回禁止
 */
function is_file_uploads() {
	if(@ini_get('file_uploads')){
		return $GLOBALS['QXDREAM']['language']['max_allowed'] . ' <span class="blue">'.ini_get('upload_max_filesize') . '</span>';
	}else{
		return '<span class="red">' . $GLOBALS['QXDREAM']['language']['banned'] . '</span>';
	}
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-04
 * 最后修改时间:     2009-08-04
 * 功能:             判断register_globals,safe_mode,short_open_tag等是开关状态
 * 参数:             @$varname(magic_quotes_gpc,register_globals等)
 * 返回值:           开启返回最大上传文件信息,否则返回禁止
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-08-04
 * 最后修改时间:     2009-08-04
 * 功能:             判断图像处理开启状态 GD Library
 * 参数:             无
 * 返回值:           开启返回GD信息,否则返回未开启
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-09-28
 * 最后修改时间:     2009-09-28
 * 功能:             设置cookie
 * 参数:             @cookie_name为cookie名称
 *                   @cookie_value为cookie的值
 *                   @time为cookie的时间,如设置1小时后过期,time()+3600
 *                   开始时要配置这几个常量
 *					 define('COOKIE_DOMAIN', ''); //Cookie 作用域
 *					 define('COOKIE_PATH', '/'); //Cookie 作用路径
 *					 define('COOKIE_PRE', 'ATgagpaxdz'); //Cookie 前缀
 * 返回值:           无
 */
function set_cookie($cookie_name, $cookie_value = '', $expires = 0) {
	//如果cookie_value为空，立即清除该cookie
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-09-28
 * 最后修改时间:     2009-09-28
 * 功能:             设置cookie
 * 参数:             @cookie_name为cookie名称
 * 返回值:           返回cookie的值
 */
function get_cookie($cookie_name) {
	//COOKIE_PRE常量，cookie的前缀
	$cookie_name = COOKIE_PRE . $cookie_name;
	return isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : FALSE;
}

//清除cookie
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

//获取IP
function get_ip() {
	//获取ip
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$online_ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$online_ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$online_ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$online_ip = $_SERVER['REMOTE_ADDR'];
	}
	//在PHP5中,环境变量不受magic_quotes_gpc影响,HTTP_X_FORWARDED_FOR可以在COOKIE中伪造
	//把以做转义和正则处理
	$online_ip = addslashes($online_ip);
	preg_match("/[\d\.]{7,15}/", $online_ip, $online_ip_matches); //匹配7-15位的数字或点
	return isset($online_ip_matches[0]) ? $online_ip_matches[0] : 'unknown';
}

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-02-20
 * 最后修改时间:       2011-04-02
 * 功能:               检测表单是否填写完整
 * 参数:               @form_vars为表单数组,如$_GET,$_POST
 *                     @skip_vars为可选择填写的表单框
 * 返回值:             完整验证的数据,不完整返回假
 */
function filled_out($form_vars, $skip_vars = '') {
	!is_array($form_vars) && halt("Parameter '" . $form_vars . "' is not array!");
	foreach($form_vars as $k => $v) {
		if(is_array($v)) { continue; }
		//跳过选择填写的表单框
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

//去除数组的首尾空格，仅限于二维
function my_trim($form_vars, $skip_vars = '') {
	!is_array($form_vars) && halt("Parameter '" . $form_vars . "' is not array!");
	foreach($form_vars as $k => $v) {
		//跳过选择填写的表单框
		if(!empty($skip_vars)) {
			if(is_array($skip_vars) && in_array($k, $skip_vars) || !is_array($skip_vars) && $k == $skip_vars) continue;
		}
		$form_vars[$k] = trim($v);
	}
	return $form_vars;
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-12-10
 * 最后修改时间:     2009-12-10
 * 功能:             确认验证码
 * 参数:             @code为文框提交的验证码字符串
 *                   @enable为是否开启验证码
 *                   @session_code_name为seesion验证码字符串
 * 返回值:           验证码没开启或验证码开启输入正确时返回真
 *                   验理码开启输入错误返回假
 */
function check_code($code, $enable = 1, $session_code_name = 'check_code') {
	if(!$enable) return TRUE; //没有开启返回真
	if(!isset($_SESSION[$session_code_name])) halt('session code is undefined!');
	if(strtolower($code) != strtolower($_SESSION[$session_code_name])) {
		unset($_SESSION[$session_code_name]);
		return FALSE;
	}
	unset($_SESSION[$session_code_name]);
	return TRUE;
}

/**
 * 编码加密
 * 参数:             @str要加密的字符串
 *                   @action默为为编码,DECODE为解码
 *                   如身份验证后set_cookie('qx_author', author_code($uid . "\t" . $username), time() + 86400 * 365);
 *                   (用于前台一般会员) list($qx_uid, $qx_username) = explode("\t", get_cookie('qx_author') ? auth_code(get_cookie('qx_author'), 'DECODE') : array('', ''));
 * 返回值:           加密后的字符串
 */
function auth_code($str, $action = 'ENCODE') {
	//$key = substr(md5($_SERVER['HTTP_USER_AGENT'] . QX_KEY) , 20, 29);用$_SERVER时如果是在swfupload里会有BUG,如用swfupload上传时
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
 * 获取角色名称
 * 参数:             @group_id为所在组ID
 * 返回值:           角色名称
 */
function get_role_name($group_id) {
	return isset($GLOBALS['QXDREAM']['USER_GROUP'][$group_id]['group_name']) ? $GLOBALS['QXDREAM']['USER_GROUP'][$group_id]['group_name'] : '';
}

// 转换时间单位:秒 to XXX,如用在数据库运行时间
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2010-01-02
 * 最后修改时间:     2010-01-02
 * 功能:             读取缓存文件
 * 参数:             @filename缓存文件名称
 *                   @suffix缓存文件后缀名
 *                   @filepath缓存文件路径
 * 返回值:           缓存文件的内容
 */
function cache_read($filename, $suffix = CACHE_FILE_SUFFIX, $filepath = CACHE_PATH) {
	$file = $filepath . $filename . $suffix;
	return @include $file;
}

/**
 * 作者:             踏雪残情
 *
 * 建立时间:         2010-01-02
 * 最后修改时间:     2010-01-02
 * 功能:             写入缓存文件
 * 参数:             @filename缓存文件名称
 *                   @array写入的数组(必须是数组)
 *                   @suffix缓存文件后缀名
 *                   @filepath缓存文件路径
 * 返回值:           写入缓存文件的内容长度
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
 * 作者:             踏雪残情
 *
 * 建立时间:         2010-01-02
 * 最后修改时间:     2010-01-02
 * 功能:             删除缓存文件
 * 参数:             @filename缓存文件名称
 *                   @suffix缓存文件后缀名
 *                   @filepath缓存文件路径
 * 返回值:           资源句柄
 */
function cache_delete($filename, $suffix = CACHE_FILE_SUFFIX, $filepath = CACHE_PATH) {
	$file = $filepath . $filename . $suffix;
	return @unlink($file);
}

/**
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-01-27
 * 最后修改时间:       2010-03-02
 * 功能:               生成fck编辑器
 * @input_id  string   文本框的值
 * @toolbar   string   工具栏
 * @width     int      宽度
 * @height    int      高度
 * 返回值              生成fckeditor编辑器的JS代码字符串
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
 * 修改配置文件
 * @config array     要写入的配置信息,要是一个数组
 * 返回值:           无
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
 * 转化为大于等于0的整数(自然数),防止提交负整数ID
 * @mixed_var  all   要转化的变量
 * 返回值:           转化成的整数
 */
function nature_val($mixed_var) {
	$mixed_var = intval($mixed_var);
	return $number = $mixed_var < 0 ? 0 : $mixed_var;
}

/**
 * 获取公司UID
 * @company_id  int  公司ID
 * 返回值:           公司UID
 */
function get_company_uid($company_id) {
	return $GLOBALS['QXDREAM']['COMPANY'][$company_id]['company_uid'];
}

/**
 * 获取表单提交地址，与方法关联
 * @para  string     其他参数
 * 返回值:           表单提交到的URL
 */
function get_frm_url($para = '') {
	if('add' == $_GET['method']) { 
		$action_url = get_action_url(); 
	} elseif('edit' == $_GET['method']) { 
		$action_url = get_action_url() . $_GET['control'] . '_id/' . $_GET[$_GET['control'] . '_id'] . '/';
	}
	return $action_url . $para;
}

//获取控制器方法全地址
function get_action_url() {
	return app_url() . $_GET['control'] . '/' . $_GET['method'] . '/';
}

/**
 * 阻断非法提交字段,如:stop_fields($_POST['user'], array('group_id'));
 * @post_arr            array     提交的数组
 * @not_allowed_fields  array     允许操作的字段
 * 返回值:                        表单提交到的URL
 */
function check_fields($post_arr, $allowed_fields) {
	$diff_arr = array_diff(array_keys($post_arr), $allowed_fields);
	if(count($diff_arr) > 0) {
		$fields = DEBUG ? '<br /><b>Fields</b>：' . implode(',', $diff_arr) . '</b>' : '';
		show_msg('Sorry, your post exists not allowed field!' . $fields);
	}
}

/**
 * 判断是否是创始人
 * @user_id  int    用户ID
 * 返回值：  boolen 是返回真
 */
function is_creator($user_id) {
	if(strstr(CREATOR, ',') && $user_id == CREATOR) return TRUE;
	$arr = explode(',', CREATOR);
	if(in_array($user_id, $arr)) return TRUE;
}

/**
 * 显示当前向导标题，如: 未处理(1) | 已处理(2)
 * @arr            array   数组
 * @cur_get_name   string  当前$_GET的名称
 * @other_get_str  string  其他的query参数
 *  如: array(0 => array('link_val' => 0, 'text' => '待处理', 'count' => 12))
 * 返回值：        string  向导HTML
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
 * 拼接SQL语句的条件
 * @where      string  条件
 * @has_where  boolen  是否已有条件
 * 返回值：    string  条件
 */
function append_where(&$sql_where, $has_where, $field) {
    return $sql_where .= $has_where ? ' AND ' . $field : ' WHERE ' . $field;
}

/**
 * 数组调试
 * @data       array   数组
 * 返回值：    无
 */
if(DEBUG) {
	function dump($data) {
		echo '<pre>';
		print_r($data);
		echo '</pre>';
	}
}

/**
 * 获取分类类型名称
 * @type       int     类型值
 * 返回值：    string  类型名称
 */
function get_type_name($type) {
	$arr = array(0 => $GLOBALS['QXDREAM']['admin_language']['content_category'], 1 => '<span class="blue">' . $GLOBALS['QXDREAM']['admin_language']['lonely_page'] . '</span>', 2 => '<span class="red">' . $GLOBALS['QXDREAM']['admin_language']['external_link'] . '</span>');
	return $arr[$type];
}

/*
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-03-05
 * 最后修改时间:       2010-03-05
 * 功能:               获取某一前缀的模板
 * 参数:               @prefix为前缀
 *                     @suffix为后缀
 * 返回值:             数组,键为不带后缀的模板名,值为带后缀的模板名
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
 * 载入公司路由
 * 公司company_uid/公司控制器+方法/，如xinyu/即：xinyu/index/
 * 参数      无
 * 返回值：  前台公司控制器名
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
	define('VIEW_PLAN', $_GET['control']); //当前公司主题
	define('VIEW_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/' . VIEW_PLAN . '/'); //当前公司前台视图目录
	define('VIEW_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . VIEW_PLAN . '/css/'); //当前公司前台视图CSS
	return $action;
}

/*
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-03-07
 * 最后修改时间:       2010-03-07
 * 功能:               删除某个ID
 * 参数:               @target_id为从哪里面删,如'1,2,3'
 *                     @delete_id为删除哪些id,如'2,3'
 * 返回值:             返回删除后的id,如上面举例返回1,字符串
 */
function delete_cat_id($target_id, $delete_id) {
	return implode(',', array_diff(explode(',', $target_id), explode(',', $delete_id)));
}

/*
 * 作者:               踏雪残情
 *
 * 建立时间:           2010-03-26
 * 最后修改时间:       2010-03-26
 * 功能:               拆分关键词为数组
 * 参数:               @str为关键词字符串
 *                     @number拆分后取出的最大数组个数
 * 返回值:             拆分后的数组
 */
function explode_keyword($str, $number, $split = ',') {
	return array_values(array_unique(array_slice(array_filter(explode($split, $str)), 0, $number)));
}

/**
 * 二维数组搜索
 * @needle    string 搜索的值
 * @haystack  array  数组
 * 返回值：   array  找到返回该数组,没找到返回假
 */
function multi_array_search($needle, $haystack) {
	while($row = current($haystack)) {
		if(array_search($needle, $row)) { return $row; }
		next($haystack);
	}
	return FALSE;
}

/*
 * 作者:             踏雪残情
 *
 * 建立时间:         2009-01-11
 * 最后修改时间:     2009-01-12
 * 功能:             显示图像
 * 参数:             @src为图像地址
 *                   @alt为图像名
 *                   @width为图像宽
 *                   @height为图像高
 * 返回值:           图像HTML代码
 */
function create_img($src, $alt = '', $width = '', $height = '') {
	$width  = empty($width)  ? '' :  ' width="' . $width . '"';
	$height = empty($height) ? '' : ' height="' . $height . '"';
	$img = '<img src="' . $src . '" alt="' . $alt . '"' . $width . $height . ' />';
	return $img;
}
?>