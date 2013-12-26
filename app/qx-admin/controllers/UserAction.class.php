<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-03-27 后台用户控制器 $
	@version  $Id: UserAction.class.php 1.0 2011-04-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class UserAction extends ShareAction {	
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
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示用户列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		if(isset($_GET['company_id'])) {
			$company_id = intval($_GET['company_id']);
		} elseif($GLOBALS['QXDREAM']['qx_company_id'] > 0) { 
			$company_id = $GLOBALS['QXDREAM']['qx_company_id'];
		} else {
			$company_id = NULL;
			$_GET['type'] = isset($_GET['type']) ? $_GET['type'] : 'all'; //传给向导数量用
		}
		$this->user->pagenation = new Pagenation();
		$this->user->pagenation->list_init();
		$user_data = $this->user->list_info($company_id, @$_GET['type']);
		$page_nav = $this->user->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['company_list']);
		//会员数量向导
		$guide = display_guide($this->user->get_guide($GLOBALS['QXDREAM']['qx_company_id']), 'type');
		$this->view->assign('guide', $guide);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('user_data', $user_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新用户
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
	* 增加编辑新用户
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { 
			$_GET['user_id'] = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
			$user_data = $this->user->get_one($_GET['user_id'], $GLOBALS['QXDREAM']['qx_company_id']);
			!is_array($user_data) && show_msg('user_not_exists'); //会员不存在,终止
			//如果当前用户不是创始人,则不能编辑创始人信息
			!is_creator($GLOBALS['QXDREAM']['qx_user_id']) && is_creator($user_data['user_id']) && show_msg('must_creator_edit_admin');
		}
		if(isset($_POST['btn_submit'])) {
			if($is_edit) { //编辑
				if($GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id'] ) { //修改我的账户
					if(isset($_POST['user']['password_old'])) {
						if(empty($_POST['user']['password_old']) || empty($_POST['user']['user_pass']) || empty($_POST['user']['password_again'])) { show_msg('filled_out'); }
						if($this->user->create_pass($_POST['user']['password_old'], $user_data['salt']) != $user_data['user_pass']) { show_msg('old_password_uncorrect'); }
						unset($_POST['user']['password_old']);
					}
				}
				if(!$this->user->check_editinfo($_POST['user'])) {
					show_msg($this->user->msg(1));
				}
			} else { //插入
				$_POST['user']['user_name'] = trim($_POST['user']['user_name']);
				if(empty($_POST['user']['user_name']) || empty($_POST['user']['user_pass']) || empty($_POST['user']['password_again'])) {
					show_msg('filled_out');
				}
				if(!$this->user->check_addinfo($_POST['user'])) {
					show_msg($this->user->msg(1));
				}
			}
			if(isset($_POST['user']['company_id'])) {
				//未选公司
				if(-100 == $_POST['user']['company_id']) { show_msg('select_company', 'goback', 1); }
				//获取公司UID
				if(isset($_POST['user']['group_id']) && $_POST['user']['group_id'] > 1) { 
					$_POST['user']['company_uid'] = get_company_uid($_POST['user']['company_id']); 
				}
			}
			if($GLOBALS['QXDREAM']['qx_group_id'] > 1) { //不是超级管理员时,即公司用户,防止添加超级管理员用户
				$_POST['user']['company_id'] = 	$GLOBALS['QXDREAM']['qx_company_id'];
				$_POST['user']['company_uid'] = $GLOBALS['QXDREAM']['qx_company_uid'];
				if(isset($_POST['user']['group_id'])) {
					$_POST['user']['group_id'] = 1 == $_POST['user']['group_id'] ? 2 : $_POST['user']['group_id']; //防止改成超级管理员
				}
			} 
			if($is_edit) {
				check_fields($_POST['user'], array('company_id', 'company_uid', 'user_pass', 'group_id', 'password_again')); //防止非字段
				if(isset($_POST['user']['group_id']) && 1 == $_POST['user']['group_id']) { //超级管理员公司ID为0
					$_POST['user']['company_id'] = 0;
					$_POST['user']['company_uid'] = '';
				}
				$this->user->edit($_POST['user'], $_GET['user_id'], $GLOBALS['QXDREAM']['qx_company_id']);
			} else {
				check_fields($_POST['user'], array('company_id', 'company_uid', 'user_name', 'user_pass', 'group_id', 'password_again'));
				$this->user->add($_POST['user']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		if($is_edit) { $this->view->assign('user_data', $user_data); }
		$cid_prefix = !empty($GLOBALS['QXDREAM']['qx_company_id']) ? $GLOBALS['QXDREAM']['qx_company_id'] . '_' : '';
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['user_' . $_GET['method']]);
		$this->view->assign('company_data', cache_read('company'));
		$this->view->assign('role_data', cache_read($cid_prefix . 'user_group'));
		$this->view->display('user_input_c');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑用户
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
	* 删除用户
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$_GET['user_id'] = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		$_GET['user_id'] == $GLOBALS['QXDREAM']['qx_user_id'] && show_msg('cannot_delete_self');
		is_creator($_GET['user_id']) && show_msg('cannot_delete_creator');
		$this->user->remove($_GET['user_id'], $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量删除用户
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function batch_delete() {
		if(!isset($_POST['user_id']) || !is_array($_POST['user_id'])) { show_msg('one_list_need'); }
		$user_id_arr = array_map('intval', $_POST['user_id']);
		foreach($user_id_arr as $k => $v) {
			//不能删除自己
			if($v == $GLOBALS['QXDREAM']['qx_user_id']) show_msg('cannot_delete_self');
			//不能删除创始人
			is_creator($v) && show_msg('cannot_delete_creator');
		}
		$this->user->batch_remove($user_id_arr, $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与开启用户
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function disable() {
		$_GET['user_id'] = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$_GET['user_id'] == $GLOBALS['QXDREAM']['qx_user_id'] && show_msg('cannot_disable_self');
		is_creator($_GET['user_id']) && show_msg('cannot_disable_creator');
		$this->user->disable($_GET['user_id'], $_GET['value'], $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量禁用与开启用户
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function batch_disable() {
		if(!isset($_POST['user_id']) || !is_array($_POST['user_id'])) { show_msg('one_list_need'); }
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$user_id_arr = array_map('intval', $_POST['user_id']);
		foreach($user_id_arr as $k => $v) {
			//不能删除自己
			if($v == $GLOBALS['QXDREAM']['qx_user_id']) show_msg('cannot_disable_self');
			//不能删除创始人
			is_creator($v) && show_msg('cannot_disable_creator');
		}
		$this->user->batch_disable($user_id_arr, $_GET['value'], $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
}
?>