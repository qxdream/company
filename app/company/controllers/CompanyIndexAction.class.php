<?php
class CompanyIndexAction extends CompanyAction {
	//列表显示
	public function index() { //Company与Action中的主模块名+"_"+该方法名
		$this->content = $this->load_model('content');
		$this->content->set($_GET['control']);
		$this->view->assign('content_data', $this->content->list_info(1));
		$this->view->display('index');
	}
}
?>