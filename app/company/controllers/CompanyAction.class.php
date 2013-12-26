<?php
//公司公共
class CompanyAction extends Controller {
	//列表显示
	public function _initialize() { //Company与Action中的主模块名+"_"+该方法名
		$company_id = $GLOBALS['QXDREAM']['COMPANY_UID'][$_GET['control']];
		$GLOBALS['QXDREAM']['COMPANY'] = cache_read('company');
		$GLOBALS['QXDREAM']['CATEGORY'] = cache_read($company_id . '_category');
		$GLOBALS['QXDREAM']['MODEL'] = cache_read('model');
		$this->view->assign('company_data', $GLOBALS['QXDREAM']['COMPANY'][$company_id]);
		$this->view->assign('company_url' , app_url() . $_GET['control'] . '/'); //公司的基本URL
	}
}
?>