<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ��ͼ $
	@version  $Id: View.class.php 1.0 2010-11-30
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class View {
	public $view_dir;
	public $suffix = '.tpl.php';
	protected $vars = array(); //ģ�����
	protected $filename; //ģ���ļ���,������׺
	protected $app_name; //Ӧ������
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡģ���ļ�·��
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ����ҳʱ���ؼ٣����򷵻ؼ�¼����
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
	* �������
	+-----------------------------------------------------------------------
	* @tpl_var string        ģ�����
	* @data string or array  �������
	+-----------------------------------------------------------------------
	* ����ֵ                 ��
	+-----------------------------------------------------------------------
	*/
	public function assign($tpl_var, $data) {
		$this->vars[$tpl_var] = $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʾģ��
	+-----------------------------------------------------------------------
	* @filename string  ģ���ļ�����
	* @app_name         Ӧ������,��Ӧapp�µ�Ŀ¼����
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function display($filename = '', $app_name = '') {
		$this->filename = empty($filename) ? $_GET['control'] . '_' . $_GET['method'] : $filename;
		$this->app_name = $app_name;
		$view_file = $this->get_tpl_file();
		if(!is_file($view_file)) {
			system_error('tpl_file_not_exists', array('view_file' => $view_file));
		}
		extract($this->vars); //��ȡ����ı���
		extract($GLOBALS['QXDREAM'], EXTR_SKIP);
		include $view_file;
	}
}
?>