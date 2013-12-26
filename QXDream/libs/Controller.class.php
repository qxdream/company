<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-30 控制器 $
	@version  $Id: Controller.class.php 1.1 2011-05-17
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Controller {
	private $control;    //控制器
	private $method;     //方法
	protected $view;     //视图
	
	/**
	+-----------------------------------------------------------------------
	* 初始化
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function __construct() {
		$this->view = new View();
		if(method_exists($this, '_initialize')) {
			$this->_initialize();
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 调用不存在的方法
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function __call($func, $args) {
		system_error('method_not_exists', array('method' => $func));
	}
	
	/**
	+-----------------------------------------------------------------------
	* 预载信息
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function init() {
		//定义一些常量
		define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		define('SCHEME', $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'); //scheme[ski:m] n 计划,策划,scheme：//host：port/path
		define('SITE_URL', SCHEME . $_SERVER['HTTP_HOST'] . QX_PATH);
		
		//按指定编码输出
		header('Content-Type: text/html; charset=' . DB_CHARSET);
		//支持页面回跳
		session_cache_limiter('private, must-revalidate');
		session_start();
		//打开缓冲
		ob_start();
		//设置时区
		timezone();
		// 重新分配脚本站用最大内存
		if(get_cfg_var('memory_limit') < '32M') memory_limit();
		
		$GLOBALS['QXDREAM']['online_ip']   = get_ip();
		$GLOBALS['QXDREAM']['timestamp']   = time();
		$GLOBALS['QXDREAM']['language']    = language('QXDream'); 
		$GLOBALS['QXDREAM']['query_num']   = 0; //执行的sql语句
		$GLOBALS['QXDREAM']['COMPANY_UID'] = cache_read('company_uid'); //公司缓存
		if(APP_PATH != ADMIN_PATH && defined('IN_ADMIN')) { //此条件满足，说明非主后台目录下的后台应用程序
		} else {
			//主后台下的ShareAction会验证是否登录后台，而前台的共享控制器名也为ShareAction
			//因为在非主后台目录下的后台应用程序里要引入主后台下的ShareAction
			//导入共享控制器
			$share_action =  CONTROLLERS_ROOT . 'ShareAction.class.php';
			is_file($share_action) && require_once $share_action;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 运行系统
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function run() {
		$this->init();
		$this->build_url();
		$this->control = ucwords($this->control) . 'Action';
		//构造控制器文件路径
		$control_file = CONTROLLERS_ROOT . $this->control . '.class.php';
		if(!is_file($control_file)) {
			system_error('control_file_not_exists', array('control_file' => $control_file));
		}
		require $control_file;
		if(!class_exists($this->control)) {
			system_error('control_class_not_exists', array('control_class' => $this->control));
		}
		//控制器文件和类都存在，实例化调用的对象
		$instance = new $this->control();
		$method = $this->method; //类的对象必须是字符串，把以赋给$method
		if(!method_exists($instance, $method)) {
			system_error('method_not_exists', array('method' => $method));
		}
		if(!defined('VIEW_PLAN')) { //除公司外的前台视图路径，公司的前台页会另外定义
			define('VIEW_PLAN', 'default'); //当前主题
			define('VIEW_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/' . VIEW_PLAN . '/'); //前台视图目录
			define('VIEW_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . VIEW_PLAN . '/css/'); //前台视图CSS
		}
		//存在就调用该方法
		$instance->$method();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 建立URL
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function build_url() {
		$url_data = array(); //临时的url数据，之后再赋给$_GET是保持先后顺序
		$path_info_array = get_path_info();
		$this->control = isset($path_info_array[0]) ? $path_info_array[0] : '';
		$this->method  = isset($path_info_array[1]) ? $path_info_array[1] : '';
		//判断控制器的值是否为空，为空时使用默认值
		$url_data['control'] = $this->control = !empty($this->control) ? $this->control : DEFAULT_CONTROL;
		$url_data['method']  = $this->method  = !empty($this->method)  ? $this->method  : DEFAULT_METHOD;
		
		if(in_array($this->method, array('__construct', 'init', 'run', 'build_url', 'load_model', '_initialize'))) {
			show_msg('invalid_request', 'stay');
		}
		
		$count = count($path_info_array);
		for($i = 2; $i < $count; $i = $i + 2) {
			$val = $i + 1;
			if(!isset($path_info_array[$val])) { continue; }
			$url_data[$path_info_array[$i]] = $path_info_array[$val];
		}
		
		//优化query_string的参数
		if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			parse_str($_SERVER['QUERY_STRING'], $pair_arr);
			$url_data = array_merge($url_data, array_filter($pair_arr));
			$other_para = '';
			foreach($url_data as $k => $v) {
				if('control' == $k || 'method' == $k) { continue; }
				$other_para .= $k . '/' . $v . '/';
			}
			redirect(app_url() . $this->control . '/' . $this->method . '/' . $other_para); 
		}
		$_GET = $url_data;
		//外部数据校验过滤
		if($_REQUEST) {
			if(!get_magic_quotes_gpc()) { //没有开启魔法引号就用slash转义
				$_POST   = slash($_POST);
				$_GET    = slash($_GET);
				$_COOKIE = slash($_COOKIE);
			}
			$_POST   = filter_sql($_POST);
			$_GET    = filter_sql($_GET);
			$_COOKIE = filter_sql($_COOKIE);
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 载入模型
	+-----------------------------------------------------------------------
	* @model_class string  模型类的名称
	+-----------------------------------------------------------------------
	* 返回值               模型实例对象
	+-----------------------------------------------------------------------
	*/
	protected function load_model($model_class) {
		$class = ucwords($model_class) . 'Model';
		$model_file = MODELS_ROOT . $class . '.class.php';
		if(!is_file($model_file)) {
			system_error('model_file_not_exists', array('model_file' => $model_file));
		}
		require $model_file;
		if(!class_exists($class)) {
			system_error('model_class_not_exists', array('model_class' => $class));
		}
		$model = new $class();
		return $model; //返回实例化对象
	}

}
?>