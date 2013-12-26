<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Muticategory
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-02-24 管理首页控制器 $
	@version  $Id: QXDreamAction.class.php 1.0 2011-05-14
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class IndexAction extends ShareAction {
	protected $table;
	private $test;
	protected $id;
	
	/**
	+-----------------------------------------------------------------------
	* 首页显示
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$admin_menu_data = include LIBS_ROOT . 'admin_menu.inc.php';
		$menu = load_class('adminMenu', 1, 0);
		$menu->set($admin_menu_data);
		$top_menu = $menu->get_menu();
		$menu->set_top_menu($top_menu);
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['admin_manage_system']);
		$this->view->assign('top_menu', $top_menu);
		$this->view->assign('sidebar_menu', $menu->get_sidebar_menu());
		$this->view->display('index');
	}
	
	/**
	+-----------------------------------------------------------------------
	* home页显示
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function home() {
		$model = new model();
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['admin_index']);
		$this->view->assign('mysql_version', $this->user->mysql_version());
		$this->view->assign('db_size', $this->user->db_size());
		$this->view->assign('mysql_runtime', format_timespan($this->user->mysql_runtime()));
		if($GLOBALS['QXDREAM']['qx_group_id'] > 1) {
			$this->view->assign('content_count', $model->result("SELECT COUNT(*) FROM " . DB_PRE . "content WHERE `company_id`='" . $GLOBALS['QXDREAM']['qx_company_id'] . "' AND `status`='1'"));
			$this->view->assign('user_count', $model->result("SELECT COUNT(*) FROM " . DB_PRE . "user WHERE `company_id`='" . $GLOBALS['QXDREAM']['qx_company_id'] . "'"));
		} else {
			$this->view->assign('company_count', $model->result("SELECT COUNT(*) FROM " . DB_PRE . "company"));
			$this->view->assign('user_count', $model->result("SELECT COUNT(*) FROM " . DB_PRE . "user"));
		}
		$admin_theme = require QX_ROOT . PUBLIC_DIR . 'theme/' . ADMIN_PLAN . '/admin_theme.inc.php';
		$this->view->assign('admin_theme', $admin_theme);
		$this->view->display();
	}
}
?>