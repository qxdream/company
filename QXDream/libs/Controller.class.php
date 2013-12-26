<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ������ $
	@version  $Id: Controller.class.php 1.1 2011-05-17
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Controller {
	private $control;    //������
	private $method;     //����
	protected $view;     //��ͼ
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼ��
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ���ò����ڵķ���
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function __call($func, $args) {
		system_error('method_not_exists', array('method' => $func));
	}
	
	/**
	+-----------------------------------------------------------------------
	* Ԥ����Ϣ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function init() {
		//����һЩ����
		define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		define('SCHEME', $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://'); //scheme[ski:m] n �ƻ�,�߻�,scheme��//host��port/path
		define('SITE_URL', SCHEME . $_SERVER['HTTP_HOST'] . QX_PATH);
		
		//��ָ���������
		header('Content-Type: text/html; charset=' . DB_CHARSET);
		//֧��ҳ�����
		session_cache_limiter('private, must-revalidate');
		session_start();
		//�򿪻���
		ob_start();
		//����ʱ��
		timezone();
		// ���·���ű�վ������ڴ�
		if(get_cfg_var('memory_limit') < '32M') memory_limit();
		
		$GLOBALS['QXDREAM']['online_ip']   = get_ip();
		$GLOBALS['QXDREAM']['timestamp']   = time();
		$GLOBALS['QXDREAM']['language']    = language('QXDream'); 
		$GLOBALS['QXDREAM']['query_num']   = 0; //ִ�е�sql���
		$GLOBALS['QXDREAM']['COMPANY_UID'] = cache_read('company_uid'); //��˾����
		if(APP_PATH != ADMIN_PATH && defined('IN_ADMIN')) { //���������㣬˵��������̨Ŀ¼�µĺ�̨Ӧ�ó���
		} else {
			//����̨�µ�ShareAction����֤�Ƿ��¼��̨����ǰ̨�Ĺ����������ҲΪShareAction
			//��Ϊ�ڷ�����̨Ŀ¼�µĺ�̨Ӧ�ó�����Ҫ��������̨�µ�ShareAction
			//���빲�������
			$share_action =  CONTROLLERS_ROOT . 'ShareAction.class.php';
			is_file($share_action) && require_once $share_action;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ϵͳ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function run() {
		$this->init();
		$this->build_url();
		$this->control = ucwords($this->control) . 'Action';
		//����������ļ�·��
		$control_file = CONTROLLERS_ROOT . $this->control . '.class.php';
		if(!is_file($control_file)) {
			system_error('control_file_not_exists', array('control_file' => $control_file));
		}
		require $control_file;
		if(!class_exists($this->control)) {
			system_error('control_class_not_exists', array('control_class' => $this->control));
		}
		//�������ļ����඼���ڣ�ʵ�������õĶ���
		$instance = new $this->control();
		$method = $this->method; //��Ķ���������ַ��������Ը���$method
		if(!method_exists($instance, $method)) {
			system_error('method_not_exists', array('method' => $method));
		}
		if(!defined('VIEW_PLAN')) { //����˾���ǰ̨��ͼ·������˾��ǰ̨ҳ�����ⶨ��
			define('VIEW_PLAN', 'default'); //��ǰ����
			define('VIEW_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/' . VIEW_PLAN . '/'); //ǰ̨��ͼĿ¼
			define('VIEW_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . VIEW_PLAN . '/css/'); //ǰ̨��ͼCSS
		}
		//���ھ͵��ø÷���
		$instance->$method();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����URL
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function build_url() {
		$url_data = array(); //��ʱ��url���ݣ�֮���ٸ���$_GET�Ǳ����Ⱥ�˳��
		$path_info_array = get_path_info();
		$this->control = isset($path_info_array[0]) ? $path_info_array[0] : '';
		$this->method  = isset($path_info_array[1]) ? $path_info_array[1] : '';
		//�жϿ�������ֵ�Ƿ�Ϊ�գ�Ϊ��ʱʹ��Ĭ��ֵ
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
		
		//�Ż�query_string�Ĳ���
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
		//�ⲿ����У�����
		if($_REQUEST) {
			if(!get_magic_quotes_gpc()) { //û�п���ħ�����ž���slashת��
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
	* ����ģ��
	+-----------------------------------------------------------------------
	* @model_class string  ģ���������
	+-----------------------------------------------------------------------
	* ����ֵ               ģ��ʵ������
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
		return $model; //����ʵ��������
	}

}
?>