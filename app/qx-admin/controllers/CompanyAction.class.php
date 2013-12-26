<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-03-06 后台公司控制器 $
	@version  $Id: CompanyAction.class.php 1.0 2011-05-07
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CompanyAction extends ShareAction {	
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
		$this->company = $this->load_model('company');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示增加公司列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$this->company->pagenation = new Pagenation();
		$this->company->pagenation->list_init();
		$company_data = $this->company->list_info();
		$page_nav = $this->company->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['company_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('company_data', $company_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新公司
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function add() {
		if(isset($_POST['btn_submit'])) {
			$_POST['user']['user_name'] = trim($_POST['user']['user_name']);
			if(empty($_POST['user']['user_name']) || empty($_POST['user']['user_pass']) || empty($_POST['user']['password_again'])) {
				show_msg('filled_out');
			}
			$_POST['company'] = my_trim($_POST['company'], 'mr_id');
			if(empty($_POST['company']['company_name']) || empty($_POST['company']['company_uid'])) {
				show_msg('filled_out');
			}
			if(!$this->company->check_addinfo($_POST['company'])) {
				show_msg($this->company->msg(1));
			}
			if(!$this->user->check_addinfo($_POST['user'])) {
				show_msg($this->user->msg(1));
			}
			$_POST['user']['company_id'] = $this->company->add($_POST['company']);
			$_POST['user']['company_uid'] = $_POST['company']['company_uid'];
			$_POST['user']['group_id'] = 2; //公司管理员
			$this->user->add($_POST['user']);
			if(isset($_POST['create_default_category']) && 1 == $_POST['create_default_category']) { //插入默认栏目
				$this->category = $this->load_model('category');
				$this->category->set($_POST['user']['company_uid'], $_POST['user']['company_id']);
				$this->add_default_category($_POST['user']['company_id'], $_POST['user']['company_uid']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['add_company']);
		$this->view->assign('include_user_input', TRUE); //包含用户增加框
		$this->view->assign('create_default_category', TRUE);
		$this->view->assign('module_resource_data', $this->company->get_module_resource());
		$this->view->display('company_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑公司
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$company_data = $this->check_record();
		$this->category = $this->load_model('category');
		$this->category->set(FALSE, $_GET['company_id']);
		if(isset($_POST['btn_submit'])) {
			if(isset($_POST['company']['company_uid'])) { unset($_POST['company']['company_uid']); } //uid是不能修改
			if(empty($_POST['company']['company_name'])) {
				show_msg('filled_out');
			}
			$this->company->edit($_POST['company'], $_GET['company_id']);
			if(isset($_POST['create_default_category']) && 1 == $_POST['create_default_category']) { //插入默认栏目
				$this->add_default_category($_GET['company_id'], $GLOBALS['QXDREAM']['COMPANY'][$_GET['company_id']]['company_uid']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		$company_data = my_htmlspecialchars($this->company->get_one($_GET['company_id']));
		$this->view->assign('company_data', $company_data);
		$this->view->assign('create_default_category', $this->category->get_count($_GET['company_id']) > 0 ? FALSE : TRUE);
		$this->view->assign('module_resource_data', $this->company->get_module_resource());
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['edit_company']);
		$this->view->display('company_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 插入默认栏目数据
	+-----------------------------------------------------------------------
	* @company_id   int  公司ID
	* @company_uid  int  公司UID
	+-----------------------------------------------------------------------
	* 返回值             无
	+-----------------------------------------------------------------------
	*/
	private function add_default_category($company_id, $company_uid) {
		$insert_category_data = array(
			array('type' => '0', 'model_id' => 1, 'parent_id' => 0, 'cat_name' => $GLOBALS['QXDREAM']['admin_language']['news_release'], 'is_nav' => 1, 'setting' => "array ( \'seo_keywords\' => \'\', \'seo_description\' => \'\', )", 'template' => '' ),
			array('type' => '0', 'model_id' => 2, 'parent_id' => 0, 'cat_name' => $GLOBALS['QXDREAM']['admin_language']['product_show'], 'is_nav' => 1, 'setting' => "array ( \'seo_keywords\' => \'\', \'seo_description\' => \'\', )", 'template' => '' ),
			array('type' => '1', 'model_id' => 3, 'parent_id' => 0, 'cat_name' => $GLOBALS['QXDREAM']['admin_language']['about_us'], 'is_nav' => 1, 'setting' => "array ( \'seo_keywords\' => \'\', \'seo_description\' => \'\', )", 'template' => 'page' ),
			array('type' => '1', 'model_id' => 3, 'parent_id' => 0, 'cat_name' => $GLOBALS['QXDREAM']['admin_language']['contact_us'], 'is_nav' => 1, 'setting' => "array ( \'seo_keywords\' => \'\', \'seo_description\' => \'\', )", 'template' => 'page' )
		);
		$this->content = $this->load_model('content');
		$this->content->set($company_id, $company_uid);
		$this->content->set_model('page');
		foreach($insert_category_data as $category) {
			if(($last_cat_id = $this->category->add($category))) {
				$this->category->cache_category($company_id);
				if(3 == $category['model_id']) { //添加单页内容
					$this->content->add(array(
						'company_id'  => $company_id,
						'company_uid' => $company_uid,
						'model_id'    => 3,
						'cat_id'      => $last_cat_id,
						'user_id'     => '',
						'status'      => 1,
						'post_time'   => $GLOBALS['QXDREAM']['timestamp'],
						'update_time' => $GLOBALS['QXDREAM']['timestamp']
					), array(
						'cat_id'      => $last_cat_id,
						'content'     => '',
						'company_id'  => $company_id,
						'company_uid' => $company_uid,
					));
					$this->content->cache_page_id($company_id);
				}
			}
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑公司
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$this->check_record();
		$this->company->remove($_GET['company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与开启公司
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function disable() {
		$this->check_record();
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$this->company->disable($_GET['company_id'], $_GET['value']);
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
		$_GET['company_id'] = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;
		if(!empty($_GET['company_id'])) {
			$data = $this->company->get_one($_GET['company_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
}
?>