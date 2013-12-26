<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-11 后台模型表控制器 $
	@version  $Id: ModelAction.class.php 1.0 2011-04-13
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ModelAction extends ShareAction {	
    /**
	+-----------------------------------------------------------------------
	* 初始化数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		parent::_initialize();
		$this->model = $this->load_model('model');
		$this->mr = $this->load_model('mr');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示模型列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$this->model->pagenation = new Pagenation();
		$this->model->pagenation->list_init(0);
		$model_data = $this->model->list_info();
		$page_nav = $this->model->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['model_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('model_data', $model_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$this->add_edit();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加编辑模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { $model_data = $this->check_record(); }
		if(isset($_POST['btn_submit'])) {
			$_POST['model'] = my_trim($_POST['model']);
			if(empty($_POST['model']['model_name'])) { show_msg('filled_out'); }
			
			check_fields($_POST['model'], array('model_name', 'model_comment', 'version', 'list_order'));
			if($is_edit) {
				$this->model->edit($_POST['model'], $_GET['model_id'], $model_data['model_name']);
				$this->mr->edit(array('mr_name' => $_POST['model']['model_name'], 'mr_comment' => $_POST['model']['model_comment']), $this->get_mr_id($model_data['model_name']));
			} else {
				$this->model->add($_POST['model']);
				$this->mr->add(array('mr_name' => $_POST['model']['model_name'], 'mr_comment' => $_POST['model']['model_comment'], 'type' => 1));
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		if($is_edit) { $this->view->assign('model_data', my_htmlspecialchars($model_data)); }
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['model_' . $_GET['method']]);
		$this->view->display('model_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取模组资源ID
	+-----------------------------------------------------------------------
	* @model_name   string   模型名
	+-----------------------------------------------------------------------
	* 返回值        int      模组资源ID
	+-----------------------------------------------------------------------
	*/
	private function get_mr_id($model_name) {
		return $GLOBALS['QXDREAM']['MR'][$model_name]['mr_id'];
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$this->add_edit();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$model_data = $this->check_record();
		$this->model->remove($_GET['model_id'], $model_data['model_name']);
		$this->mr->remove($this->get_mr_id($model_data['model_name']));
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与开启模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function disable() {
		$model_data = $this->check_record();
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$this->model->disable($_GET['model_id'], $_GET['value']);
		$this->mr->disable($this->get_mr_id($model_data['model_name']), $_GET['value']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 记录是否存在
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      存在返回数组
	+-----------------------------------------------------------------------
	*/
	private function check_record() {
		$_GET['model_id'] = isset($_GET['model_id']) ? intval($_GET['model_id']) : 0;
		if(!empty($_GET['model_id'])) {
			$data = $this->model->get_one($_GET['model_id']);
			(1 == $data['is_system']) && show_msg('cannot_control_system', 'goback', 1);
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
}
?>