<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-01 ��̨���ݿ����� $
	@version  $Id: ContentAction.class.php 1.0 2011-05-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ContentAction extends ShareAction {	
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
		@$_GET['mr_model'] = $_GET['model_id'];
		parent::_initialize();
		$this->content = $this->load_model('content');
		$this->content->set($GLOBALS['QXDREAM']['qx_company_id']);
		$this->model = $this->load_model('model');
		$this->model_data = $this->check_model_record();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʾ�����б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$cat_id = isset($_GET['cat_id']) ? nature_val($_GET['cat_id']) : FALSE; //���ں���ж�
		$user_id = isset($_GET['user_id']) ? nature_val($_GET['user_id']) : 0;
		$search_key = isset($_GET['search_box']) ? $_GET['search_box'] : '';
		if(isset($_GET['user_id']) || isset($_GET['cat_id']) && 'content' != $_GET['control'] || !empty($search_key)) {
			$status = 1; //���������򵼣�����Ϊ��ʱ���ڲ�Ĭ���׸�������ʶ
		} else {
			$status = $_GET['status'] = isset($_GET['status']) ? intval($_GET['status']) : 1;
		}
		//����������
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
	* ��������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$field_data = cache_read($this->model_data['model_id'] . '_model_field');
		if(isset($_POST['data_post'])) {
			$content_arr = $this->check_post_fields($_POST['content'], $field_data);
			$_POST['content_detail'] = $this->check_post_fields($_POST['content_detail'], $field_data);
			$content_arr['cat_id'] = isset($content_arr['cat_id']) ? $content_arr['cat_id'] : 0;
			$content_arr['model_id'] = $this->model_data['model_id'];
			if(isset($_POST['btn_save'])) { //�ݸ�
				$content_arr['status'] = 3;
			} elseif(isset($_POST['btn_release'])) { //����״̬
				$content_arr['status'] = 1;
			}
			unset($_POST['content']);
			//�����ϴ��ĸ���
			if(isset($_SESSION['attachment_id'])) {
				$content_arr['attachment_id'] = $_SESSION['attachment_id'];
				unset($_SESSION['attachment_id']);
			}
			if($this->content->add($content_arr, $_POST['content_detail'])) {
				$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']); //�������,��Ϊ�������¼����������
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
	* �����ύ�ֶ���֤����
	+-----------------------------------------------------------------------
	* @post_fields   array   �ύ���ֶ�
	* @field_data    array   �����ֶ�
	+-----------------------------------------------------------------------
	* ����ֵ         array   �ܹ������ֶ�
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
	* �༭����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$cat_id = isset($_GET['cat_id']) ? nature_val($_GET['cat_id']) : FALSE;
		$field_data = cache_read($this->model_data['model_id'] . '_model_field');
		if(3 == $this->model_data['model_id']) { //��ҳʱ
			$_GET['content_id'] = $this->content->get_page_content_id($cat_id);
		}
		$content_data = $this->check_record();
		if(isset($_POST['data_post'])) {
			$content_arr = $this->check_post_fields(@$_POST['content'], $field_data);
			$_POST['content_detail'] = $this->check_post_fields($_POST['content_detail'], $field_data);
			if(isset($_POST['btn_save'])) { //�ݸ�
				$content_arr['status'] = 3;
			} elseif(isset($_POST['btn_release'])) { //����״̬
				$content_arr['status'] = 1;
			}
			unset($_POST['content']);
			//�����ϴ��ĸ���
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
	* �༭����״̬
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ����ɾ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function really_delete() {
		$this->check_record();
		$this->content->remove($_GET['content_id']);
		show_msg('operation_success', HTTP_REFERER);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ������ݻ���վ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ��������ɾ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ��������״̬
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ��¼�Ƿ����
	+-----------------------------------------------------------------------
	* ��
	+-----------------------------------------------------------------------
	* ����ֵ      ���ڷ�������
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
			$this->content->set_model($data['model_name']); //����ģ����
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
}
?>