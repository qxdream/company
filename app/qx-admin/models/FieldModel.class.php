<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-04-16 ��̨ģ���ֶ�ģ�� $
	@version  $Id: FieldModel.class.php 1.0 2011-04-16
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class FieldModel extends Model {
	public $table;
	public $model_id;
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼģ����Ϣ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function set($model_id = 0) {
		$this->table = DB_PRE . 'model_field';
		$this->model_id = $model_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡĳģ���ֶ���Ϣ
	+-----------------------------------------------------------------------
	* @field_id         int or string    ģ���ֶ�ID
	+-----------------------------------------------------------------------
	* ����ֵ            array or boolen  ���򷵻ظ�ģ���ֶ�����,���򷵻�FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($field_id) {
		return $this->fetch("SELECT * FROM " . $this->table . " WHERE field_id='" . $field_id . "' AND model_id='" . $this->model_id  . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ģ���ֶ��б�
	+-----------------------------------------------------------------------
	* @model_id   int    ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ      array  ģ���ֶ�����
	+-----------------------------------------------------------------------
	*/
	public function list_info() {
		$this->count("SELECT COUNT(*) FROM " . $this->table);
		return $this->fetch_all("SELECT * FROM `{$this->table}` WHERE `model_id`='{$this->model_id}' ORDER BY `list_order`,`field_id` ASC" . $this->pagenation->sql_limit(), 'unbuffered');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ������ģ���ֶ�
	+-----------------------------------------------------------------------
	* @data         array    ����ģ���ֶ�����
	* @model_name   string   ģ������
	+-----------------------------------------------------------------------
	* ����ֵ        int      �����ID
	+-----------------------------------------------------------------------
	*/
	public function add($data, $model_name) {
		if(!$this->check_info($data)) { show_msg($this->msg(1)); }
		$data['model_id'] = $this->model_id;
		$data['is_system'] = 0;
		$this->insert($this->table, $data);
		$insert_id = $this->last_insert_id();
		$this->cache_model_field($this->model_id);
		$this->add_table_field($data['field_name'], $data['type'], $model_name);
		return $insert_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������ģ�ͱ�Ķ�Ӧ�ֶ�
	+-----------------------------------------------------------------------
	* @field_name   array    �ֶ�����
	* @type         string   �ֶ�����
	* @model_name   string   ģ������
	+-----------------------------------------------------------------------
	* ����ֵ        int      �����ID
	+-----------------------------------------------------------------------
	*/
	public function add_table_field($field_name, $type, $model_name) {
		$field_description = '';
		switch($type) {
			case 'text':
			case 'image':
				$field_description = 'VARCHAR(255) CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL';
			break;
			case 'textarea':
			case 'editor':
				$field_description = 'TEXT CHARACTER SET gbk COLLATE gbk_chinese_ci NOT NULL';
			break;
			case 'number':
				$field_description = 'INT(11) NOT NULL';
			break;
		}
		$this->query("ALTER TABLE `" . DB_PRE . "content_" . $model_name . "` ADD `" . $field_name . "` " . $field_description);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��֤Ҫ��������ģ���ֶ�
	+-----------------------------------------------------------------------
	* @data    array    ����ģ���ֶ�����
	+-----------------------------------------------------------------------
	* ����ֵ   boolen   �ɹ�������,ʧ�ܷ��ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_info($data) {
		if(!preg_match('/^[A-Za-z\-_1-9]+$/', $data['field_name'])) {
			$this->msg = 'field_name_has_badword';
			return FALSE;
		}
		if(strlen($data['field_name']) > 20) {
			$this->msg = 'field_name_not_beyond_20_len';
			return FALSE;
		}
		$field_data = array();
		$sql = "SELECT `field_id` FROM {$this->table} WHERE `field_name`='{$data['field_name']}' AND `model_id`='{$this->model_id}'";
		$field_data = $this->fetch($sql);
		if(is_array($field_data)) {
			$this->msg = 'field_name_has_used';
			return FALSE;
		}
		unset($field_data);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ���ֶ�
	+-----------------------------------------------------------------------
	* @data         array    ģ���ֶ�����
	* @model_name   string   ģ������
	+-----------------------------------------------------------------------
	* ����ֵ        boolen   �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function edit($data, $field_id, $model_name) {
		$this->update($this->table, $data, "field_id='" . $field_id . "' AND model_id='" . $this->model_id  . "'");
		$this->cache_model_field($this->model_id);
		return TRUE;
	}
		
	/**
	+-----------------------------------------------------------------------
	* ����������ģ���ֶ�
	+-----------------------------------------------------------------------
	* @field_id      int  Ҫ���õ�ģ���ֶ�ID
	* @disabled_value  int  ����Ϊ0,����Ϊ1
	+-----------------------------------------------------------------------
	* ����ֵ                �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function disable($field_id, $disabled_value) {
		$this->update($this->table, array('disabled' => $disabled_value), "field_id='" . $field_id . "' AND model_id='" . $this->model_id  . "'");
		$this->cache_model_field($this->model_id);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ģ���ֶ�
	+-----------------------------------------------------------------------
	* @field_id      int    ģ���ֶ�ID
	+-----------------------------------------------------------------------
	* ����ֵ                  �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function remove($field_id, $field_name, $model_name) {
		$this->delete($this->table, "field_id='" . $field_id . "' AND model_id='" . $this->model_id  . "'");
		$this->query("ALTER TABLE `" . DB_PRE . "content_{$model_name}` DROP `{$field_name}`");
		$this->cache_model_field($this->model_id);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ģ���ֶ�����
	+-----------------------------------------------------------------------
	* @id_arr      array     ���ύ������
	+-----------------------------------------------------------------------
	* ����ֵ       boolen    �ɹ������棬ʧ�ܷ��ؼ�
	+-----------------------------------------------------------------------
	*/
	public function list_order($id_arr) {
		if(!is_array($id_arr)) { return FALSE; }
		foreach($id_arr as $k => $v) {
			$this->update($this->table, array('list_order' => $v) , "field_id='" . $k . "' AND model_id='" . $this->model_id  . "'");
		}
		$this->cache_model_field($this->model_id);
		return TRUE;
	}
}
?>