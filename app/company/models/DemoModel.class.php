<?php
class DemoModel extends Model {
	public $table;
	
	public function __construct() {
		parent::__construct();
		$this->set();
	}
	
	private function set() {
		$this->table = DB_PRE . 'company';
	}
	
	public function list_info() {
		return $this->fetch_all("SELECT * FROM " . $this->table . " ORDER BY `company_id` DESC");
	}
	
}
?>