<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-11 后台模型表类 $
	@version  $Id: ModelModel.class.php 1.0 2011-04-13
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ModelModel extends Model {
	public $table;
	
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
		$this->model_table = DB_PRE . 'model';
		$this->model_field_table = DB_PRE . 'model_field';
		$this->content_table = DB_PRE . 'content';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取某模型信息
	+-----------------------------------------------------------------------
	* @model_id         int or string    模型ID
	+-----------------------------------------------------------------------
	* 返回值            array or boolen  有则返回该模型数组,无则返回FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($model_id) {
		return $this->fetch("SELECT * FROM " . $this->model_table . " WHERE model_id='" . $model_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 模型列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   array 模型数据
	+-----------------------------------------------------------------------
	*/
	public function list_info() {
		$this->count("SELECT COUNT(*) FROM " . $this->model_table);
		$model_data = $content_count_data = array();
		$content_count_data = $this->fetch_all_column_key("SELECT `model_id`,COUNT(`model_id`) AS `content_count` FROM `{$this->content_table}` GROUP BY `model_id` ORDER BY `model_id`", 'model_id');
		$query = $this->query("SELECT * FROM `{$this->model_table}` WHERE `is_hidden`='0' ORDER BY `model_id` ASC" . $this->pagenation->sql_limit(), 'unbuffered');
		while($row = $this->fetch_array($query)) {
			$row['content_count'] = isset($content_count_data[$row['model_id']]['content_count']) ? $content_count_data[$row['model_id']]['content_count'] : 0;
			$model_data[] = $row;
		}
		$this->free_result($query);
		return $model_data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新模型
	+-----------------------------------------------------------------------
	* @data    array    新增模型数据
	+-----------------------------------------------------------------------
	* 返回值   int      插入的ID
	+-----------------------------------------------------------------------
	*/
	public function add($data) {
		if(!$this->check_info($data)) { show_msg($this->msg(1)); }
		$this->create_table($data['model_name'], $data['model_comment']);
		$this->insert($this->model_table, $data);
		$insert_id = $this->last_insert_id();
		$this->create_model_field($insert_id);
		$this->cache_model();
		$this->cache_model_field($insert_id);
		return $insert_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 建立模型内容表
	+-----------------------------------------------------------------------
	* @table_name   string   表名
	* @model_name   string   模型名
	+-----------------------------------------------------------------------
	* 返回值        boolen   成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function create_table($table_name, $model_name) {
		return $this->query("CREATE TABLE `"  . DB_PRE ."content_{$table_name}`(
					 `content_id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
					 `content` MEDIUMTEXT CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL,
					 `author` CHAR(30) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL,
					 PRIMARY KEY (`content_id`)
					 ) ENGINE=MYISAM CHARACTER SET gbk COLLATE gbk_chinese_ci COMMENT='{$model_name}';");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 建立字段记录
	+-----------------------------------------------------------------------
	* @model_id     int      模型ID
	+-----------------------------------------------------------------------
	* 返回值        boolen   成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function create_model_field($model_id) {
		return $this->insert_multiple($this->model_field_table, array(
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'cat_id', 'field_comment' => '栏目', 'type' => 'cat_id', 'tips' => '', 'is_system' => 1, 'is_require' => 1, 'disabled' => 0, 'list_order' => 1),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'title', 'field_comment' => '标题', 'type' => 'text', 'tips' => '', 'is_system' => 1, 'is_require' => 1, 'disabled' => 0, 'list_order' => 0),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'description', 'field_comment' => '描述', 'type' => 'textarea', 'tips' => '', 'is_system' => 1, 'is_require' => 0, 'disabled' => 0, 'list_order' => 3),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'content', 'field_comment' => '内容', 'type' => 'editor', 'tips' => '', 'is_system' => 0, 'is_require' => 0, 'disabled' => 0, 'list_order' => 4),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'author', 'field_comment' => '作者', 'type' => 'text', 'tips' => '', 'is_system' => 0, 'is_require' => 0, 'disabled' => 0, 'list_order' => 2),
		));
	}
	
	/**
	+-----------------------------------------------------------------------
	* 验证要操作的新模型
	+-----------------------------------------------------------------------
	* @data        array    新增模型数据
	* @model_id    int      模型ID
	+-----------------------------------------------------------------------
	* 返回值       boolen   成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function check_info($data, $model_id = '') {
		if(!preg_match('/^[A-Za-z\-_1-9]+$/', $data['model_name'])) {
			$this->msg = 'model_name_has_badword';
			return FALSE;
		}
		if(strlen($data['model_name']) > 20) {
			$this->msg = 'model_name_not_beyond_20_len';
			return FALSE;
		}
		$model_data = array();
		$append_where = empty($model_id) ? '' : " AND `model_id`!='{$model_id}'";
		$sql = "SELECT `model_id` FROM {$this->model_table} WHERE `model_name`='{$data['model_name']}'" . $append_where;
		$model_data = $this->fetch($sql);
		if(is_array($model_data)) {
			$this->msg = 'model_name_has_used';
			return FALSE;
		}
		unset($model_data);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 更新模型
	+-----------------------------------------------------------------------
	* @data       array    模型数据
	* @model_id   int      模型ID 
	+-----------------------------------------------------------------------
	* 返回值      boolen   成功返回真
	+-----------------------------------------------------------------------
	*/
	public function edit($data, $model_id, $old_model_name) {
		if(!$this->check_info($data, $model_id)) { show_msg($this->msg(1)); }
		$this->update($this->model_table, $data, "model_id='" . $model_id . "'");
		$this->cache_model();
		if($data['model_name'] != $old_model_name) { $this->query("RENAME TABLE `" . DB_PRE . "content_" . $old_model_name . "` TO `" . DB_PRE . "content_" . $data['model_name'] . "`"); }
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 禁用与启用模型
	+-----------------------------------------------------------------------
	* @model_id         int      要禁用的模型ID
	* @disabled_value   int      启用为0,禁用为1
	+-----------------------------------------------------------------------
	* 返回值            boolen   成功返回真
	+-----------------------------------------------------------------------
	*/
	public function disable($model_id, $disabled_value) {
		$this->update($this->model_table, array('disabled' => $disabled_value), "model_id='" . $model_id . "'");
		$this->cache_model();
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除模型
	+-----------------------------------------------------------------------
	* @model_id      int     模型ID
	* @model_name    string  模型名称
	+-----------------------------------------------------------------------
	* 返回值         boolen  成功返回真
	+-----------------------------------------------------------------------
	*/
	public function remove($model_id, $model_name) {
		$this->delete($this->model_table, "model_id='" . $model_id ."'");
		$this->cache_model();
		$this->query("DROP TABLE `" . DB_PRE . "content_" . $model_name . "`");
		$this->delete($this->model_field_table, "model_id='" . $model_id ."'");
		$this->cache_delete_model_field($model_id);
		return TRUE;
	}
}
?>