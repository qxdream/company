<?php
class IndexAction extends Controller {
	
	public function _initialize() {
		$this->view->assign('title', '����ʾ-��վ���ܱ���');
		$this->company = $this->load_model('demo');
	}
	
	public function index() {
		$this->view->assign('company_data', $this->company->list_info());
		$this->view->display();
	}
	
	
}
?>