<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-02 ��̨�������ݿ����� $
	@version  $Id: ContentAllAction.class.php 1.0 2011-05-14
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ContentAllAction extends ShareAction {	
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
		$this->content = $this->load_model('content');
		$this->content->set($GLOBALS['QXDREAM']['qx_company_id']);
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
			$status = 1; //����������
		} else {
			$status = $_GET['status'] = isset($_GET['status']) ? intval($_GET['status']) : 1;
		}
		//����������
		$guide = display_guide($this->content->content_count(), 'status');
	
		$this->content->pagenation = new Pagenation();
		$this->content->pagenation->list_init();
		$content_data = $this->content->list_info($cat_id, $user_id, $status, 0, $search_key);
		$page_nav = $this->content->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['content_list']);
		$this->view->assign('cat_id', $cat_id);
		$this->view->assign('user_id', $user_id);
		$this->view->assign('status', $status);
		$this->view->assign('guide', $guide);
		$this->view->assign('search_key', $search_key);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('content_data', $content_data);
		$this->view->display('content_index');
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
			//��ɾ���������ݻ��ǲ�ͬ��ģ��
			$content_data = $this->content->check_record();
			$this->content->set_model($GLOBALS['QXDREAM']['MODEL'][$content_data['model_id']]['model_name']);
			$this->content->remove($v);
		}
		$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
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
		if($this->content->empty_recycle_bin()) {
			$this->content->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
			show_msg('operation_success', HTTP_REFERER);
		} else {
			show_msg($this->content->msg);
		}
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
}
?>