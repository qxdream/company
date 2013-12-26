<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-08 ��̨���������� $
	@version  $Id: AttachmentAction.class.php 1.0 2011-05-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class AttachmentAction extends ShareAction {	
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
		$this->attachment = $this->load_model('attachment');
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
		$this->attachment->set($GLOBALS['QXDREAM']['qx_company_id'], $GLOBALS['QXDREAM']['qx_company_uid']);
		$this->attachment->pagenation = new Pagenation();
		$this->attachment->pagenation->list_init();
		$attachment_data = $this->attachment->list_info();
		$page_nav = $this->attachment->pagenation->page_normal();
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['attachment_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('attachment_data', $attachment_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ϴ�����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$this->attachment->set($GLOBALS['QXDREAM']['qx_company_id'], $GLOBALS['QXDREAM']['qx_company_uid']);
		$type = isset($_GET['type']) ? $_GET['type'] : 0; //�ж��ϴ�����
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['attachment_upload']);
		if($type == 0) { //������ϴ�
			$this->view->display();
			if(isset($_POST['btn_submit'])) {
				$file_arr = array();
				if($file_arr = $this->attachment->upload('upfile')) { //�ɹ��ϴ�
					$insert_editor = $onload = '';
					foreach($file_arr as $k => $v) { //����ͼƬ�����Ͳ���ͼƬ��,Ҫ����༭��
						$r_name = filename_remove_suffix($v['filename']);
						if($v['is_image'] == 1) {
							$img_url = QX_PATH . $v['file_path'];
							$onload = PIC_WIDTH == 0 ? '' :  ' onload="if(this.offsetWidth>' . PIC_WIDTH . ')this.width=' . PIC_WIDTH . ';"'; //��ͼ����
							$insert_editor .= '<a href="' . $img_url . '" target="_blank"><img src="' . $img_url . '" alt="' . $GLOBALS['QXDREAM']['language']['click_view_resource'] . '"' . $onload . ' /></a><br />';
						} else {
							$file_url = QX_PATH . $v['file_path'];
							$insert_editor .= '<a href="' . $file_url . '" target="_blank">' . $r_name . '</a><br />';
						}
					}
					unset($file_arr);
					//����js����
					$insert_editor = '<textarea id="insert" class="m_b_10" rows="12" cols="50" style="width: 100%">' . $insert_editor . '</textarea>';
					$insert_editor .= '<p class="text_center"><input type="button" id="btn_insert" class="btn_style" value="' . $GLOBALS['QXDREAM']['admin_language']['insert'] . '" /></p>';
					//�˴��븡����ܽ�����һ��Ƕ��
					echo '<script type="text/javascript">
							$(\'#attach_content\').html(\'' . $insert_editor . '\');
							var sel = undefined !== parent.right ? parent.right.getSel(\'' . $_GET['editor_id'] . '\') : parent.getSel(\'' . $_GET['editor_id'] . '\');
							$("#btn_insert").click(function(){
								if(undefined !== parent.right) {
									parent.right.addhtml($("#insert").val(), \'' . $_GET['editor_id'] . '\', sel);
								} else {
									parent.addhtml($("#insert").val(), \'' . $_GET['editor_id'] . '\', sel);
								}
								parent.$("#overlay").remove();
								parent.$("#dark_bg").remove();
								parent.$("#new_dialogue").remove();
							});
						 </script>';
				}
			}
		} elseif($type == 2) { //text��ͼƬ�ϴ�
			$this->view->display('attachment_pic_add');
			if(isset($_POST['btn_submit'])) {
				$file_arr = array();
				if($file_arr = $this->attachment->upload('upfile', UPLOAD_ALLOW_PIC_SUFFIX)) { //�ɹ��ϴ�
					$file_url = $file_arr[0]['file_path'];
					unset($file_arr);
					echo '<script type="text/javascript">
									parent.right.$("#' . $_GET['pic_id'] . '").val("' . $file_url . '");
									parent.$("#overlay").remove();
									parent.$("#dark_bg").remove();
									parent.$("#new_dialogue").remove();
						 </script>';
				}
			}
		}
	}
	
	
	/**
	+-----------------------------------------------------------------------
	* ɾ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$this->attachment->set($GLOBALS['QXDREAM']['qx_company_id'], $GLOBALS['QXDREAM']['qx_company_uid']);
		$attachment_data = $this->check_record();
		$this->attachment->content_id = $attachment_data['content_id'];
		$this->attachment->remove($_GET['attachment_id'], $attachment_data['file_path'], $attachment_data['is_image']);
		show_msg('operation_success', HTTP_REFERER);
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
	public function batch_delete() {
		$this->attachment->set($GLOBALS['QXDREAM']['qx_company_id'], $GLOBALS['QXDREAM']['qx_company_uid']);
		if(!isset($_POST['attachment_id'])) show_msg('one_list_need');
		$attachment_id_arr = array_map('intval', $_POST['attachment_id']);
		foreach($attachment_id_arr as $k => $v) {
			$attachment_data = $this->attachment->get($v);
			$this->attachment->content_id = $attachment_data['content_id'];
			$this->attachment->remove($v, $attachment_data['file_path'], $attachment_data['is_image']);
		}
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
		$_GET['attachment_id'] = isset($_GET['attachment_id']) ? intval($_GET['attachment_id']) : 0;
		if(!empty($_GET['attachment_id'])) {
			$data = $this->attachment->get($_GET['attachment_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
	
}
?>