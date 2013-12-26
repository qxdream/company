<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-07 后台模组资源控制器 $
	@version  $Id: mrAction.class.php 1.0 2011-04-10
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class MrAction extends ShareAction {	
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
		$this->mr = $this->load_model('mr');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示模组资源列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$this->mr->pagenation = new Pagenation();
		$this->mr->pagenation->list_init();
		$mr_data = $this->mr->list_info();
		$page_nav = $this->mr->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['mr_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('mr_data', $mr_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加模组资源
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
	* 增加编辑模组资源
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { $mr_data = $this->check_record(); }
		if(isset($_POST['btn_submit'])) {
			$_POST['mr'] = my_trim($_POST['mr']);
			if(empty($_POST['mr']['mr_name'])) { show_msg('filled_out'); }
			
			check_fields($_POST['mr'], array('mr_name', 'mr_comment', 'version', 'list_order'));
			if($is_edit) {
				$this->mr->edit($_POST['mr'], $_GET['mr_id']);
			} else {
				$this->mr->add($_POST['mr']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		if($is_edit) { $this->view->assign('mr_data', my_htmlspecialchars($mr_data)); }
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['mr_' . $_GET['method']]);
		$this->view->display('mr_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑模组资源
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
	* 编辑模组资源
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$this->check_record();
		$this->mr->remove($_GET['mr_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与开启模组资源
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function disable() {
		$this->check_record();
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$this->mr->disable($_GET['mr_id'], $_GET['value']);
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
		$_GET['mr_id'] = isset($_GET['mr_id']) ? intval($_GET['mr_id']) : 0;
		if(!empty($_GET['mr_id'])) {
			$data = $this->mr->get_one($_GET['mr_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			(1 == $data['is_core'] || 1 == $data['type']) && show_msg('cannot_control_core_or_model', 'goback', 1);
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 排序
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      无
	+-----------------------------------------------------------------------
	*/
	public function batch_list_order() {
		if(!isset($_POST['list_order'])) show_msg('one_list_need');
		$_POST['list_order'] = array_map('intval', $_POST['list_order']);
		$this->mr->list_order($_POST['list_order']);
		show_msg('operation_success', HTTP_REFERER);
	}
}
?>