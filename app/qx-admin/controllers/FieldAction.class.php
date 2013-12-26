<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-04-16 ��̨ģ���ֶο����� $
	@version  $Id: FieldAction.class.php 1.0 2011-04-16
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class FieldAction extends ShareAction {	
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
		$this->model = $this->load_model('model');
		$this->model_data = $this->check_model_record();
		$this->field = $this->load_model('field');
		$this->field->set($_GET['model_id']);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʾģ���ֶ��б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$this->field->pagenation = new Pagenation();
		$this->field->pagenation->list_init();
		$field_data = $this->field->list_info($_GET['model_id']);
		$page_nav = $this->field->pagenation->page_normal();
		$type_data = require 'field.inc.php'; //�ֶ�����
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['field_list'] . ' - ' . $this->model_data['model_comment']);
		$this->view->assign('type_data', $type_data);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('field_data', $field_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ���ֶ�
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
	* ���ӱ༭ģ���ֶ�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function add_edit() {
		$is_edit = 'edit' == $_GET['method'] ? TRUE : FALSE;
		if($is_edit) { $field_data = $this->check_record(); }
		if(isset($_POST['btn_submit'])) {
			$_POST['field'] = my_trim($_POST['field']);
			if($is_edit) {
				if(empty($_POST['field']['field_comment'])) { show_msg('filled_out'); }
				check_fields($_POST['field'], array('field_comment', 'tips', 'list_order', 'is_require'));
				$this->field->edit($_POST['field'], $_GET['field_id'], $this->model_data['model_name']);
			} else {
				if(empty($_POST['field']['field_name']) || empty($_POST['field']['field_comment'])) { show_msg('filled_out'); }
				check_fields($_POST['field'], array('field_name', 'field_comment', 'type', 'tips', 'list_order', 'is_require'));
				$this->field->add($_POST['field'], $this->model_data['model_name']);
			}
			show_msg('operation_success', HTTP_REFERER);
		}
		
		if($is_edit) { $this->view->assign('field_data', my_htmlspecialchars($field_data)); }
		$field_type_data = require 'field.inc.php';
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['field_' . $_GET['method']] . ' - ' . $this->model_data['model_comment']);
		$this->view->assign('field_type_data', $field_type_data);
		$this->view->display('field_input');
	}
	
	/**
	+-----------------------------------------------------------------------
	* �༭ģ���ֶ�
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
	* �༭ģ���ֶ�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$field_data = $this->check_record();
		$this->field->remove($_GET['field_id'], $field_data['field_name'], $this->model_data['model_name']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����뿪��ģ���ֶ�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function disable() {
		$this->check_record();
		$_GET['value'] = isset($_GET['value']) ? intval($_GET['value']) : 0;
		$this->field->disable($_GET['field_id'], $_GET['value']);
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
		$_GET['field_id'] = isset($_GET['field_id']) ? intval($_GET['field_id']) : 0;
		if(!empty($_GET['field_id'])) {
			$data = $this->field->get_one($_GET['field_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			(1 == $data['is_system']) && show_msg('cannot_control_system', 'goback', 1);
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ģ�ͼ�¼�Ƿ����
	+-----------------------------------------------------------------------
	* ��
	+-----------------------------------------------------------------------
	* ����ֵ      ���ڷ�������
	+-----------------------------------------------------------------------
	*/
	private function check_model_record() {
		$_GET['model_id'] = isset($_GET['model_id']) ? intval($_GET['model_id']) : 0;
		if(!empty($_GET['model_id'])) {
			$data = $this->model->get_one($_GET['model_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����
	+-----------------------------------------------------------------------
	* ��
	+-----------------------------------------------------------------------
	* ����ֵ      ��
	+-----------------------------------------------------------------------
	*/
	public function batch_list_order() {
		if(!isset($_POST['list_order'])) show_msg('one_list_need');
		$_POST['list_order'] = array_map('intval', $_POST['list_order']);
		$this->field->list_order($_POST['list_order']);
		show_msg('operation_success', HTTP_REFERER);
	}
}
?>