<?php
//��˾����
class CompanyAction extends Controller {
	//�б���ʾ
	public function _initialize() { //Company��Action�е���ģ����+"_"+�÷�����
		$company_id = $GLOBALS['QXDREAM']['COMPANY_UID'][$_GET['control']];
		$GLOBALS['QXDREAM']['COMPANY'] = cache_read('company');
		$GLOBALS['QXDREAM']['CATEGORY'] = cache_read($company_id . '_category');
		$GLOBALS['QXDREAM']['MODEL'] = cache_read('model');
		$this->view->assign('company_data', $GLOBALS['QXDREAM']['COMPANY'][$company_id]);
		$this->view->assign('company_url' , app_url() . $_GET['control'] . '/'); //��˾�Ļ���URL
	}
}
?>