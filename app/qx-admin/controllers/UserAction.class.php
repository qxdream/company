<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-03-27 ��̨�û������� $
	@version  $Id: UserAction.class.php 1.0 2011-04-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class UserAction extends ShareAction {	
    /**
	+-----------------------------------------------------------------------
	* ��ʼ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		parent::_initialize();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʾ�û��б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		if(isset($_GET['company_id'])) {
			$company_id = intval($_GET['company_id']);
		} elseif($GLOBALS['QXDREAM']['qx_company_id'] > 0) { 
			$company_id = $GLOBALS['QXDREAM']['qx_company_id'];
		} else {
			$company_id = NULL;
			$_GET['type'] = isset($_GET['type']) ? $_GET['type'] : 'all'; //������������
		}
		$this->user->pagenation = new Pagenation();
		$this->user->pagenation->list_init();
		$user_data = $this->user->list_info($company_id, @$_GET['type']);
		$page_nav = $this->user->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['company_list']);
		//��Ա������
		$guide = display_guide($this->user->get_guide($GLOBALS['QXDREAM']['qx_company_id']), 'type');
		$this->view->assign('guide', $guide);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('user_data', $user_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$this->add_edit();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ӱ༭���û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { 
			$_GET['user_id'] = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
			$user_data = $this->user->get_one($_GET['user_id'], $GLOBALS['QXDREAM']['qx_company_id']);
			!is_array($user_data) && show_msg('user_not_exists'); //��Ա������,��ֹ
			//�����ǰ�û����Ǵ�ʼ��,���ܱ༭��ʼ����Ϣ
			!is_creator($GLOBALS['QXDREAM']['qx_user_id']) && is_creator($user_data['user_id']) && show_msg('must_creator_edit_admin');
		}
		if(isset($_POST['btn_submit'])) {
			if($is_edit) { //�༭
				if($GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id'] ) { //�޸��ҵ��˻�
					if(isset($_POST['user']['password_old'])) {
						if(empty($_POST['user']['password_old']) || empty($_POST['user']['user_pass']) || empty($_POST['user']['password_again'])) { show_msg('filled_out'); }
						if($this->user->create_pass($_POST['user']['password_old'], $user_data['salt']) != $user_data['user_pass']) { show_msg('old_password_uncorrect'); }
						unset($_POST['user']['password_old']);
					}
				}
				if(!$this->user->check_editinfo($_POST['user'])) {
					show_msg($this->user->msg(1));
				}
			} else { //����
				$_POST['user']['user_name'] = trim($_POST['user']['user_name']);
				if(empty($_POST['user']['user_name']) || empty($_POST['user']['user_pass']) || empty($_POST['user']['password_again'])) {
					show_msg('filled_out');
				}
				if(!$this->user->check_addinfo($_POST['user'])) {
					show_msg($this->user->msg(1));
				}
			}
			if(isset($_POST['user']['company_id'])) {
				//δѡ��˾
				if(-100 == $_POST['user']['company_id']) { show_msg('select_company', 'goback', 1); }
				//��ȡ��˾UID
				if(isset($_POST['user']['group_id']) && $_POST['user']['group_id'] > 1) { 
					$_POST['user']['company_uid'] = get_company_uid($_POST['user']['company_id']); 
				}
			}
			if($GLOBALS['QXDREAM']['qx_group_id'] > 1) { //���ǳ�������Աʱ,����˾�û�,��ֹ��ӳ�������Ա�û�
				$_POST['user']['company_id'] = 	$GLOBALS['QXDREAM']['qx_company_id'];
				$_POST['user']['company_uid'] = $GLOBALS['QXDREAM']['qx_company_uid'];
				if(isset($_POST['user']['group_id'])) {
					$_POST['user']['group_id'] = 1 == $_POST['user']['group_id'] ? 2 : $_POST['user']['group_id']; //��ֹ�ĳɳ�������Ա
				}
			} 
			if($is_edit) {
				check_fields($_POST['user'], array('company_id', 'company_uid', 'user_pass', 'group_id', 'password_again')); //��ֹ���ֶ�
				if(isset($_POST['user']['group_id']) && 1 == $_POST['user']['group_id']) { //��������Ա��˾IDΪ0
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
	* �༭�û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$this->add_edit();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ���û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ����ɾ���û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function batch_delete() {
		if(!isset($_POST['user_id']) || !is_array($_POST['user_id'])) { show_msg('one_list_need'); }
		$user_id_arr = array_map('intval', $_POST['user_id']);
		foreach($user_id_arr as $k => $v) {
			//����ɾ���Լ�
			if($v == $GLOBALS['QXDREAM']['qx_user_id']) show_msg('cannot_delete_self');
			//����ɾ����ʼ��
			is_creator($v) && show_msg('cannot_delete_creator');
		}
		$this->user->batch_remove($user_id_arr, $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����뿪���û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ���������뿪���û�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function batch_disable() {
		if(!isset($_POST['user_id']) || !is_array($_POST['user_id'])) { show_msg('one_list_need'); }
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$user_id_arr = array_map('intval', $_POST['user_id']);
		foreach($user_id_arr as $k => $v) {
			//����ɾ���Լ�
			if($v == $GLOBALS['QXDREAM']['qx_user_id']) show_msg('cannot_disable_self');
			//����ɾ����ʼ��
			is_creator($v) && show_msg('cannot_disable_creator');
		}
		$this->user->batch_disable($user_id_arr, $_GET['value'], $GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
}
?>