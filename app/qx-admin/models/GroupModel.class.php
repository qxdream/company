<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-20 后台用户组模型 $
	@version  $Id: GroupModel.class.php 1.0 2011-04-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class GroupModel extends Model {
	public $table;
	public $company_id;
	
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
	* 初始用户组信息
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	private function set() {
		$this->table = DB_PRE . 'user_group';
		$this->company_id = $GLOBALS['QXDREAM']['qx_company_id'];
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取某用户组信息
	+-----------------------------------------------------------------------
	* @group_id         int or string    用户组ID
	+-----------------------------------------------------------------------
	* 返回值         array or boolen  有则返回该用户组数组,无则返回FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($group_id) {
		return $this->fetch("SELECT * FROM `" . $this->table . "` WHERE `group_id`='" . $group_id . "' AND `company_id`='" . $this->company_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 用户组列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   array 用户组数据
	+-----------------------------------------------------------------------
	*/
	public function list_info() {
		$this->count("SELECT COUNT(*) FROM " . $this->table);
		$query = $this->query("SELECT * FROM `{$this->table}` WHERE `company_id` IN(" . $this->company_id . ",0) AND `is_super`!='1' ORDER BY `group_id` ASC" . $this->pagenation->sql_limit(), 'unbuffered');
		$data = array();
		$company_mr_ids_data = explode(',', $GLOBALS['QXDREAM']['COMPANY'][$this->company_id]['mr_ids']);
		while($row = $this->fetch_array($query)) {
			$row['post_time'] = format_date('Y/m/d', $row['post_time']);
			$mr_ids_data = 2 == $row['group_id'] ? $company_mr_ids_data : array_intersect(explode(',', $row['mr_ids']), $company_mr_ids_data);
			$mr_ids_comment_data = array();
			foreach($mr_ids_data as $mr_id) {
				$comment = $this->get_mr_comment($mr_id);
				if(!empty($comment)) {
					$mr_ids_comment_data[] = $comment;
				}
			}
			$row['use_module'] = implode(',', $mr_ids_comment_data);
			$data[] = $row;
		}
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取模组资源备注
	+-----------------------------------------------------------------------
	* @mr_id   int     模组资源ID
	+-----------------------------------------------------------------------
	* 返回值   string  模组资源备注
	+-----------------------------------------------------------------------
	*/
	public function get_mr_comment(&$mr_id) {
		foreach($GLOBALS['QXDREAM']['MR'] as $mr) {
			if($mr['mr_id'] == $mr_id && 0 == $mr['is_hidden']) { return $mr['mr_comment']; }
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新用户组
	+-----------------------------------------------------------------------
	* @data    array    新增用户组数据
	+-----------------------------------------------------------------------
	* 返回值   int      插入的ID
	+-----------------------------------------------------------------------
	*/
	public function add($data) {
		if(!$this->check_info($data)) { show_msg($this->msg(1)); }
		$data['company_id'] = $this->company_id;
		$this->insert($this->table, $data);
		$insert_id = $this->last_insert_id();
		$this->cache_user_group($this->company_id);
		return $insert_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 验证要操作的新用户组
	+-----------------------------------------------------------------------
	* @data    array    新增用户组数据
	+-----------------------------------------------------------------------
	* 返回值   boolen   成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function check_info($data, $group_id = '') {
		if(strlen($data['group_name']) > 20) {
			$this->msg = 'group_name_not_beyond_20_len';
			return FALSE;
		}
		$group_data = array();
		$append_where = empty($group_id) ? '' : " AND `group_id`!='{$group_id}'";
		$sql = "SELECT `group_id` FROM {$this->table} WHERE `group_name`='{$data['group_name']}' AND company_id IN(" . $this->company_id . ",0)" . $append_where;
		$group_data = $this->fetch($sql);
		if(is_array($group_data)) {
			$this->msg = 'group_name_has_used';
			return FALSE;
		}
		unset($group_data);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 更新用户组
	+-----------------------------------------------------------------------
	* @data    array    用户组数据
	+-----------------------------------------------------------------------
	* 返回值   boolen   成功返回真
	+-----------------------------------------------------------------------
	*/
	public function edit($data, $group_id) {
		if(!$this->check_info($data, $group_id)) { show_msg($this->msg(1)); }
		$this->update($this->table, $data, "group_id='" . $group_id . "' AND company_id='" . $this->company_id . "'");
		$this->cache_user_group($this->company_id);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除用户组
	+-----------------------------------------------------------------------
	* @group_id      int    用户组ID
	+-----------------------------------------------------------------------
	* 返回值                  成功返回真
	+-----------------------------------------------------------------------
	*/
	public function remove($group_id) {
		$this->delete($this->table, "group_id='" . $group_id . "' AND company_id='" . $this->company_id . "'");
		$this->cache_user_group($this->company_id);
		return TRUE;
	}
	
}
?>