<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-04-11 ��̨ģ�ͱ��� $
	@version  $Id: ModelModel.class.php 1.0 2011-04-13
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ModelModel extends Model {
	public $table;
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		$this->set();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼģ����Ϣ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	private function set() {
		$this->model_table = DB_PRE . 'model';
		$this->model_field_table = DB_PRE . 'model_field';
		$this->content_table = DB_PRE . 'content';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡĳģ����Ϣ
	+-----------------------------------------------------------------------
	* @model_id         int or string    ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ            array or boolen  ���򷵻ظ�ģ������,���򷵻�FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($model_id) {
		return $this->fetch("SELECT * FROM " . $this->model_table . " WHERE model_id='" . $model_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ģ���б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   array ģ������
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
	* ������ģ��
	+-----------------------------------------------------------------------
	* @data    array    ����ģ������
	+-----------------------------------------------------------------------
	* ����ֵ   int      �����ID
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
	* ����ģ�����ݱ�
	+-----------------------------------------------------------------------
	* @table_name   string   ����
	* @model_name   string   ģ����
	+-----------------------------------------------------------------------
	* ����ֵ        boolen   �ɹ�������,ʧ�ܷ��ؼ�
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
	* �����ֶμ�¼
	+-----------------------------------------------------------------------
	* @model_id     int      ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ        boolen   �ɹ�������,ʧ�ܷ��ؼ�
	+-----------------------------------------------------------------------
	*/
	public function create_model_field($model_id) {
		return $this->insert_multiple($this->model_field_table, array(
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'cat_id', 'field_comment' => '��Ŀ', 'type' => 'cat_id', 'tips' => '', 'is_system' => 1, 'is_require' => 1, 'disabled' => 0, 'list_order' => 1),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'title', 'field_comment' => '����', 'type' => 'text', 'tips' => '', 'is_system' => 1, 'is_require' => 1, 'disabled' => 0, 'list_order' => 0),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'description', 'field_comment' => '����', 'type' => 'textarea', 'tips' => '', 'is_system' => 1, 'is_require' => 0, 'disabled' => 0, 'list_order' => 3),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'content', 'field_comment' => '����', 'type' => 'editor', 'tips' => '', 'is_system' => 0, 'is_require' => 0, 'disabled' => 0, 'list_order' => 4),
			array('field_id' => NULL, 'model_id' => $model_id, 'field_name' => 'author', 'field_comment' => '����', 'type' => 'text', 'tips' => '', 'is_system' => 0, 'is_require' => 0, 'disabled' => 0, 'list_order' => 2),
		));
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��֤Ҫ��������ģ��
	+-----------------------------------------------------------------------
	* @data        array    ����ģ������
	* @model_id    int      ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ       boolen   �ɹ�������,ʧ�ܷ��ؼ�
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
	* ����ģ��
	+-----------------------------------------------------------------------
	* @data       array    ģ������
	* @model_id   int      ģ��ID 
	+-----------------------------------------------------------------------
	* ����ֵ      boolen   �ɹ�������
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
	* ����������ģ��
	+-----------------------------------------------------------------------
	* @model_id         int      Ҫ���õ�ģ��ID
	* @disabled_value   int      ����Ϊ0,����Ϊ1
	+-----------------------------------------------------------------------
	* ����ֵ            boolen   �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function disable($model_id, $disabled_value) {
		$this->update($this->model_table, array('disabled' => $disabled_value), "model_id='" . $model_id . "'");
		$this->cache_model();
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ģ��
	+-----------------------------------------------------------------------
	* @model_id      int     ģ��ID
	* @model_name    string  ģ������
	+-----------------------------------------------------------------------
	* ����ֵ         boolen  �ɹ�������
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