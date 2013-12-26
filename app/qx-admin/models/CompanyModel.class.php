<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-03-06 后台公司模型 $
	@version  $Id: CompanyModel.class.php 1.0 2011-03-23
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CompanyModel extends Model {
	public $table;              //消息提示
	
	/**
	+-----------------------------------------------------------------------
	* 初始化数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		$this->set();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 初始模组信息
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function set() {
		$this->table = DB_PRE . 'company';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取某公司信息
	+-----------------------------------------------------------------------
	* @company_id    int or string 公司ID或英文ID
	* @cid_type      int           ID类型,0为数字ID,1为英文ID
	+-----------------------------------------------------------------------
	* 返回值         有则返回该公司数组,无则返回FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($company_id, $cid_type = 0) {
		$id_field = 0 == $cid_type ? "`company_id`" : "`company_uid`";
		return $this->fetch("SELECT * FROM " . $this->table . " WHERE " . $id_field . "='" . $company_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 公司列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   公司数据
	+-----------------------------------------------------------------------
	*/
	public function list_info() {
		$this->count("SELECT COUNT(*) FROM " . $this->table);
		return $this->fetch_all("SELECT `company_id`,`company_uid`,`company_name`,`post_time`,`hits_count`,`disabled` FROM `{$this->table}` ORDER BY `company_id` DESC" . $this->pagenation->sql_limit(), 'unbuffered');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新公司
	+-----------------------------------------------------------------------
	* @data    array    新增公司数据
	+-----------------------------------------------------------------------
	* 返回值            插入的ID
	+-----------------------------------------------------------------------
	*/
	public function add($data) {
		$data['mr_ids'] = implode(',', $data['mr_id']);
		unset($data['mr_id']);
		$data['post_time'] = $GLOBALS['QXDREAM']['timestamp'];
		$this->insert($this->table, $data);
		$insert_id = $this->last_insert_id();
		$this->cache_all($insert_id);
		return $insert_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 验证要增加的新公司
	+-----------------------------------------------------------------------
	* @data    array    新增公司数据
	+-----------------------------------------------------------------------
	* 返回值            成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function check_addinfo($data) {
		if(!preg_match('/^[A-Za-z\-_1-9]+$/', $data['company_uid'])) {
			$this->msg = 'company_uid_has_badword';
			return FALSE;
		}
		if(strlen($data['company_uid']) > 20) {
			$this->msg = 'company_uid_not_beyond_20_len';
			return FALSE;
		}
		$company_data = $this->get_one($data['company_uid'], 1);
		if(is_array($company_data)) {
			$this->msg = 'company_uid_exists';
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 更新公司
	+-----------------------------------------------------------------------
	* @data    array    公司数据
	+-----------------------------------------------------------------------
	* 返回值            成功返回真
	+-----------------------------------------------------------------------
	*/
	public function edit($data, $company_id) {
		if(isset($data['mr_id'])) {
			$data['mr_ids'] = implode(',', $data['mr_id']);
			unset($data['mr_id']);
		}
		$this->update($this->table, $data, "company_id='" . $company_id . "'");
		$this->cache_all($company_id);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与启用公司
	+-----------------------------------------------------------------------
	* @company_id      int  要禁用的公司ID
	* @disabled_value  int  启用为0,禁用为1
	+-----------------------------------------------------------------------
	* 返回值                成功返回真
	+-----------------------------------------------------------------------
	*/
	public function disable($company_id, $disabled_value) {
		$this->update($this->table, array('disabled' => $disabled_value), "company_id='" . $company_id . "'");
		$this->cache_company();
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除公司(不删除分类内容与附件,其余都删除)
	+-----------------------------------------------------------------------
	* @company_id      int    公司ID
	+-----------------------------------------------------------------------
	* 返回值                  成功返回真
	+-----------------------------------------------------------------------
	*/
	public function remove($company_id) {
		$this->delete($this->table, "company_id='" . $company_id ."'");
		$this->cache_company();
		$this->cache_delete_company($company_id);
		$this->cache_delete_category($company_id);
		$this->cache_delete_page($company_id);
		$this->delete(DB_PRE . 'user', "company_id='" . $company_id ."'");
		$this->delete(DB_PRE . 'user_group', "company_id='" . $company_id ."'");
		$this->delete(DB_PRE . 'category', "company_id='" . $company_id ."'");
		$this->delete(DB_PRE . 'content', "company_id='" . $company_id ."' AND `model_id`='3'"); //删除单页
		$this->delete(DB_PRE . 'content_page', "company_id='" . $company_id ."'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取模组资源
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function get_module_resource() {
		return cache_read('module_resource');
	}
	
}
?>