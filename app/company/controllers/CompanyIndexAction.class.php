<?php
class CompanyIndexAction extends CompanyAction {
	//�б���ʾ
	public function index() { //Company��Action�е���ģ����+"_"+�÷�����
		$this->content = $this->load_model('content');
		$this->content->set($_GET['control']);
		$this->view->assign('content_data', $this->content->list_info(1));
		$this->view->display('index');
	}
}
?>