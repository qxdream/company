<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-30 视图 $
	@version  $Id: View.class.php 1.0 2010-11-30
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class View {
	public $view_dir;
	public $suffix = '.tpl.php';
	protected $vars = array(); //模板变量
	protected $filename; //模板文件名,不含后缀
	protected $app_name; //应用名称
	
	/**
	+-----------------------------------------------------------------------
	* 获取模板文件路径
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   不能页时返回假，否则返回记录总数
	+-----------------------------------------------------------------------
	*/
	protected function get_tpl_file() {
		if(defined('IN_ADMIN')) {
			$this->view_dir = empty($this->app_name) ? QX_ROOT . APP_DIR . APP_PATH . 'views/' : QX_ROOT . APP_DIR . $this->app_name . '/views/';
		} else {
			$this->view_dir = empty($this->app_name) ? QX_ROOT . APP_DIR . APP_PATH . 'views/' . VIEW_PLAN : QX_ROOT . APP_DIR . $this->app_name . '/views/' . VIEW_PLAN;
		}
		$view_dir = dir_path($this->view_dir);
		return $view_dir . $this->filename . $this->suffix;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 分配变量
	+-----------------------------------------------------------------------
	* @tpl_var string        模板变量
	* @data string or array  数组变量
	+-----------------------------------------------------------------------
	* 返回值                 无
	+-----------------------------------------------------------------------
	*/
	public function assign($tpl_var, $data) {
		$this->vars[$tpl_var] = $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示模板
	+-----------------------------------------------------------------------
	* @filename string  模板文件名称
	* @app_name         应用名称,对应app下的目录名称
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function display($filename = '', $app_name = '') {
		$this->filename = empty($filename) ? $_GET['control'] . '_' . $_GET['method'] : $filename;
		$this->app_name = $app_name;
		$view_file = $this->get_tpl_file();
		if(!is_file($view_file)) {
			system_error('tpl_file_not_exists', array('view_file' => $view_file));
		}
		extract($this->vars); //提取分配的变量
		extract($GLOBALS['QXDREAM'], EXTR_SKIP);
		include $view_file;
	}
}
?>