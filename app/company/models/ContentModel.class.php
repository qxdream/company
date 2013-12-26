<?php
class ContentModel extends Model {
	public $table;
	public $model_id;
	
	public function _initialize() {}
	
	public function set($company_uid) {
		$this->table = DB_PRE . 'content';
		$this->company_uid = $company_uid;
	}
	
	public function list_info($model_id) {
		return $this->fetch_all("SELECT * FROM " . $this->table . " WHERE `company_uid`='{$this->company_uid}' AND `model_id`='{$model_id}' AND `status`='1' ORDER BY `company_id` DESC");
	}
	
	public function get_one($content_id) {
		$data = $this->fetch("SELECT * FROM {$this->table} WHERE `content_id`='{$content_id}' AND `status`='1'");
		if(is_array($data)) {
			$this->model_id = $data['model_id'];
			return array_merge($data, $this->fetch("SELECT * FROM {$this->table}_" . $GLOBALS['QXDREAM']['MODEL'][$data['model_id']]['model_name'] . " WHERE  `content_id`='{$content_id}'"));
		} else {
			return FALSE;
		}
	}
	
}
?>