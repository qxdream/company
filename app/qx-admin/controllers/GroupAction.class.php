<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-04-20 ��̨�û�������� $
	@version  $Id: GroupAction.class.php 1.0 2011-04-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class GroupAction extends ShareAction {	
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
		$this->group = $this->load_model('group');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʾ�û����б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$this->group->pagenation = new Pagenation();
		$this->group->pagenation->list_init();
		$group_data = $this->group->list_info();
		$page_nav = $this->group->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['group_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('group_data', $group_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����û���
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
	* ���ӱ༭�û���
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { $group_data = $this->check_record(); }
		if(isset($_POST['btn_submit'])) {
			$_POST['group']['group_name'] = trim($_POST['group']['group_name']);
			if(empty($_POST['group']['group_name'])) { show_msg('filled_out'); }
			if(isset($_POST['group']['mr_id'])) {
				count(array_diff($_POST['group']['mr_id'], explode(',', $GLOBALS['QXDREAM']['qx_mr_ids']))) > 0 && show_msg('invalid_request');
				$_POST['group']['mr_ids'] = implode(',', $_POST['group']['mr_id']);
				unset($_POST['group']['mr_id']);
			}
			check_fields($_POST['group'], array('group_name', 'mr_ids'));
			if($is_edit) {
				$this->group->edit($_POST['group'], $_GET['group_id']);
			} else {
				$this->group->add($_POST['group']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		$mr_data = array();
		$mr_ids_data = explode(',', $GLOBALS['QXDREAM']['qx_mr_ids']);
		if($is_edit) { 
			$this->view->assign('group_data', my_htmlspecialchars($group_data));
			$cur_mr_data = explode(',', $group_data['mr_ids']); //��ǰ�û����ʹ�õ�ģ��
			foreach($mr_ids_data as $k => $v) {
				$comment = $this->group->get_mr_comment($v);
				if(!empty($comment)) {
					$mr_data[$v] = array('mr_id' => $v, 'mr_comment' => $comment, 'is_used' => (in_array($v, $cur_mr_data) ? 1 : 0));
				}
			}
		} else {
			foreach($mr_ids_data as $k => $v) {
				$comment = $this->group->get_mr_comment($v);
				if(!empty($comment)) {
					$mr_data[$v] = array('mr_id' => $v, 'mr_comment' => $comment);
				}
			}
		}
		$this->view->assign('mr_data', $mr_data);
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['group_' . $_GET['method']]);
		$this->view->display('group_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* �༭�û���
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
	* �༭�û���
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$this->check_record();
		$this->group->remove($_GET['group_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��¼�Ƿ����
	+-----------------------------------------------------------------------
	* ��
	+-----------------------------------------------------------------------
	* ����ֵ      ���ڷ�������
	+-----------------------------------------------------------------------
	*/
	private function check_record() {
		$_GET['group_id'] = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
		if(!empty($_GET['group_id'])) {
			$data = $this->group->get_one($_GET['group_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			1 == $data['is_system'] && show_msg('cannot_control_system', 'goback', 1);
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
}
?>