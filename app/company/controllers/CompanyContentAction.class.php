<?php
class CompanyContentAction extends CompanyAction {
	public function _initialize() {
		parent::_initialize();
		$this->content = $this->load_model('content');
		$this->content->set($_GET['control']);
	}
	
	public function content_product() {
		$this->view->assign('content_data', $this->content->list_info(2));
		$this->view->display('index');
	}
	
	public function content_show() {
		$_GET['content_id'] = isset($_GET['content_id']) ? intval($_GET['content_id']) : 0;
		$content_data = $this->content->get_one($_GET['content_id']);
		if(!$content_data) { show_msg('data_not_exists'); }
		$this->view->assign('content_data', $content_data);
		if(2 == $this->content->model_id) {
			$this->view->display('content_product_show');
		} else {
			$this->view->display('content_news_show');
		}
	}
}
?>