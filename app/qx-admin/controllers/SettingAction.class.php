<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-03-26 ��̨��˾������ $
	@version  $Id: SettingAction.class.php 1.0 2011-03-27
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class SettingAction extends ShareAction {	
	/**
	+-----------------------------------------------------------------------
	* ��ʾ���ӹ�˾��
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		if(1 != $GLOBALS['QXDREAM']['qx_group_id']) {
			$company = $this->load_model('company');
			$company_data = $company->get_one($GLOBALS['QXDREAM']['qx_company_id']);
			$this->view->assign('company_data', my_htmlspecialchars($company_data));
		}
		
		if(isset($_POST['btn_submit'])) {
			if(1 == $GLOBALS['QXDREAM']['qx_group_id']) {
				$_POST['config']['OVERTIME'] = nature_val($_POST['config']['OVERTIME']);
				set_config($_POST['config']);
				if($_POST['config']['ADMIN_PLAN'] == ADMIN_PLAN) {
					$go_to_url = HTTP_REFERER;
					$is_parent = FALSE;
				} else {
					$go_to_url = app_url();
					$is_parent = TRUE;
				}
				show_msg('operation_success', $go_to_url, 0, 1500, $is_parent);
			} else {
				check_fields($_POST['company'], array('company_name', 'site_name', 'keywords', 'description', 'copyright', 'icp_no'));
				$company->edit($_POST['company'], $GLOBALS['QXDREAM']['qx_company_id']);
				show_msg('operation_success', HTTP_REFERER);
			}
		}
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['system_setting']);
		$this->view->display();
	}
}
?>