<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-05-01 后台内容控制器 $
	@version  $Id: ContentAction.class.php 1.0 2011-05-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ContentAction extends ShareAction {	
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
		@$_GET['mr_model'] = $_GET['model_id'];
		parent::_initialize();
		$this->content = $this->load_model('content');
		$this->content->set($GLOBALS['QXDREAM']['qx_company_id']);
		$this->model = $this->load_model('model');
		$this->model_data = $this->check_model_record();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示内容列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$cat_id = isset($_GET['cat_id']) ? nature_val($_GET['cat_id']) : FALSE; //用于恒等判断
		$user_id = isset($_GET['user_id']) ? nature_val($_GET['user_id']) : 0;
		$search_key = isset($_GET['search_box']) ? $_GET['search_box'] : '';
		if(isset($_GET['user_id']) || isset($_GET['cat_id']) && 'content' != $_GET['control'] || !empty($search_key)) {
			$status = 1; //用于数量向导，条件为真时用于不默认首个导航标识
		} else {
			$status = $_GET['status'] = isset($_GET['status']) ? intval($_GET['status']) : 1;
		}
		//内容数量向导
		$guide = display_guide($this->content->content_count($this->model_data['model_id'], $cat_id), 'status', 'model_id/' . $this->model_data['model_id'] . '/cat_id/' . $cat_id);
	
		$this->content->pagenation = new Pagenation();
		$this->content->pagenation->list_init();
		$content_data = $this->content->list_info($cat_id, $user_id, $status, $this->model_data['model_id'], $search_key);
		$page_nav = $this->content->pagenation->page_normal();
		
		$this->view->assign('page_title', (!empty($search_key) ? $GLOBALS['QXDREAM']['admin_language']['search'] : (isset($GLOBALS['QXDREAM']['CATEGORY'][$cat_id]) ? $GLOBALS['QXDREAM']['CATEGORY'][$cat_id]['cat_name'] : '')) . ' - ' . $this->model_data['model_comment'] . $GLOBALS['QXDREAM']['admin_language']['list']);
		$this->view->assign('cat_id', $cat_id);
		$this->view->assign('user_id', $user_id);
		$this->view->assign('status', $status);
		$this->view->assign('guide', $guide);
		$this->view->assign('search_key', $search_key);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('content_data', $content_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加内容
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$field_data = cache_read($this->model_data['model_id'] . '_model_field');
		if(isset($_POST['data_post'])) {
			$content_arr = $this->check_post_fields($_POST['content'], $field_data);
			$_POST['content_detail'] = $this->check_post_fields($_POST['content_detail'], $field_data);
			$content_arr['cat_id'] = isset($content_arr['cat_id']) ? $content_arr['cat_id'] : 0;
			$content_arr['model_id'] = $this->model_data['model_id'];
			if(isset($_POST['btn_save'])) { //草稿
				$content_arr['status'] = 3;
			} elseif(isset($_POST['btn_release'])) { //发布状态
				$content_arr['status'] = 1;
			}
			unset($_POST['content']);
			//处理上传的附件
			if(isset($_SESSION['attachment_id'])) {
				$content_arr['attachment_id'] = $_SESSION['attachment_id'];
				unset($_SESSION['attachment_id']);
			}
			if($this->content->add($content_arr, $_POST['content_detail'])) {
				$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']); //缓存分类,因为分类里记录着文章数量
				show_msg('operation_success', HTTP_REFERER);
			} else {
				show_msg('operation_fail', HTTP_REFERER);
			}
		}
		require 'Form.class.php';
		$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
		$option_str = $this->content->get_category_option($this->model_data['model_id'], $cat_id);
		$this->view->assign('page_title', $this->model_data['model_comment'] . $GLOBALS['QXDREAM']['admin_language']['add']);
		$this->view->assign('field_data', $field_data);
		$this->view->assign('option_str', $option_str);
		$this->view->display('content_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 内容提交字段验证处理
	+-----------------------------------------------------------------------
	* @post_fields   array   提交的字段
	* @field_data    array   固有字段
	+-----------------------------------------------------------------------
	* 返回值         array   能过返回字段
	+-----------------------------------------------------------------------
	*/
	private function check_post_fields($post_fields, $field_data) {
		if(!is_array($post_fields)) { return FALSE; }
		$field_arr = array();
		foreach($post_fields as $k => $v) {
			$field_arr = multi_array_search($k, $field_data);
			if(is_array($field_arr)) {
				if(!is_array($v)) {
					$post_fields[$k] = $v = trim($v);
					empty($v) && 1 == $field_arr['is_require'] && show_msg('filled_out');
				}
			} else {
				show_msg('Sorry, your post exists not allowed field');
			}
		}
		return $post_fields;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑内容
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$cat_id = isset($_GET['cat_id']) ? nature_val($_GET['cat_id']) : FALSE;
		$field_data = cache_read($this->model_data['model_id'] . '_model_field');
		if(3 == $this->model_data['model_id']) { //单页时
			$_GET['content_id'] = $this->content->get_page_content_id($cat_id);
		}
		$content_data = $this->check_record();
		if(isset($_POST['data_post'])) {
			$content_arr = $this->check_post_fields(@$_POST['content'], $field_data);
			$_POST['content_detail'] = $this->check_post_fields($_POST['content_detail'], $field_data);
			if(isset($_POST['btn_save'])) { //草稿
				$content_arr['status'] = 3;
			} elseif(isset($_POST['btn_release'])) { //发布状态
				$content_arr['status'] = 1;
			}
			unset($_POST['content']);
			//处理上传的附件
			if(isset($_SESSION['attachment_id'])) {
				$content_arr['attachment_id'] = $_SESSION['attachment_id'];
				unset($_SESSION['attachment_id']);
			}
			if($this->content->edit($content_data, $content_arr, $_POST['content_detail'])) {
				if($content_arr['status'] == 1) $this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
				show_msg('operation_success', HTTP_REFERER);
			} else {
				show_msg('operation_fail', HTTP_REFERER);
			}
		}
		require 'Form.class.php';
		$content_data = my_htmlspecialchars($content_data);
		$option_str = $this->content->get_category_option($this->model_data['model_id'], $content_data['cat_id']);
		$this->view->assign('page_title', $this->model_data['model_comment'] . $GLOBALS['QXDREAM']['admin_language']['edit']);
		$this->view->assign('field_data', $field_data);
		$this->view->assign('content_data', $content_data);
		$this->view->display('content_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑内容状态
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function status() {
		!isset($_GET['value']) && show_msg('invalid_request');
		$content_data = $this->check_record();
		if($this->content->status($content_data['content_id'], $_GET['value'])) {
			$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
			show_msg('operation_success', HTTP_REFERER);
		} else {
			show_msg('operation_fail');
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 彻底删除内容
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function really_delete() {
		$this->check_record();
		$this->content->remove($_GET['content_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 清空内容回收站
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function empty_all() {
		if($this->content->empty_recycle_bin($this->model_data['model_id'], $_GET['cat_id'])) {
			$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
			show_msg('operation_success', HTTP_REFERER);
		} else {
			show_msg($this->content->msg);
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量彻底删除内容
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function batch_really_delete() {
		if(!isset($_POST['content_id'])) show_msg('one_list_need');
		$content_id_arr = array_map('intval', $_POST['content_id']);
		foreach($content_id_arr as $k => $v) {
			$this->content->remove($v);
		}
		$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量设置状态
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function batch_status() {
		if(!isset($_POST['content_id'])) show_msg('one_list_need');
		!isset($_GET['value']) && show_msg('invalid_request');
		$content_id_arr = array_map('intval', $_POST['content_id']);
		if($this->content->status($content_id_arr, $_GET['value'])) {
			$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
			show_msg('operation_success', HTTP_REFERER);
		} else {
			show_msg('operation_fail');
		}
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
		$_GET['content_id'] = isset($_GET['content_id']) ? intval($_GET['content_id']) : 0;
		if(!empty($_GET['content_id'])) {
			$data = $this->content->get($_GET['content_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 模型记录是否存在
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      存在返回数组
	+-----------------------------------------------------------------------
	*/
	private function check_model_record() {
		$_GET['model_id'] = isset($_GET['model_id']) ? intval($_GET['model_id']) : 0;
		if(!empty($_GET['model_id'])) {
			$data = $this->model->get_one($_GET['model_id']);
			$this->content->set_model($data['model_name']); //设置模型名
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
}
?>