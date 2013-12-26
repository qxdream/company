<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ģ�� $
	@version  $Id: Model.class.php 1.1 2011-05-01
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Model extends Mysql {
	
	public $pagenation;  //��ҳ����
	public $msg;         //��Ϣ��ʾ
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼ��
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function __construct() {
		parent::__construct();
		//��˾���治���ڣ�����һ������
		if(method_exists($this, '_initialize')) {
			$this->_initialize();
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���㵱ǰ��¼����
	+-----------------------------------------------------------------------
	* @sql     string  sql���,��SELECT COUNT(*) FROM `table`
	+-----------------------------------------------------------------------
	* ����ֵ   ����ҳʱ���ؼ٣����򷵻ؼ�¼����
	+-----------------------------------------------------------------------
	*/
	public function count($sql) {
		return empty($this->pagenation->page_size) ? FALSE : $this->pagenation->row_total = $this->result($sql);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������ݿ��С
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ���ݿ�Ĵ�С(���е�λ)
	+-----------------------------------------------------------------------
	*/
	public function db_size() {
		$size = 0;
		$query = $this->query("SHOW TABLE STATUS LIKE '" . DB_PRE . "%'", 'SILENT');
		while($row = $this->fetch_array($query)) {
			$size += $row['Data_length'] + $row['Index_length'];
		}
		return $size = $size ? size($size) : 'unknow';
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������ݱ�
	+-----------------------------------------------------------------------
	* @table       string  ���ݱ������
	* @append      string  �����ļ�������
	* @fields      string  �ֶ�,Ĭ��Ϊ����
	* @order_field string  ������ֶ�����
	* @where       string  ��ѯ����
	* @is_line     boolen  �Ƿ�ÿ����¼����Ϊһ���ļ�
	* @number      int     ��ѯ��������
	+-----------------------------------------------------------------------
	* ����ֵ       ��
	+-----------------------------------------------------------------------
	*/
	public function cache_table($table, $append = '', $fields = '*', $order_field = '', $asc_desc = 'ASC', $where = '', $is_line = 0, $number = 0) {
		//�ѱ��ǰ׺�滻��,�����ļ���Ϊ����ǰ׺�����ݱ�����
		$arr = array();
		if(preg_match("/^" . DB_PRE ."(.*)$/", $table, $arr)) {
			$remove_pre_table = $arr[1];
			unset($arr);
		} else { //�������ݱ�ǰ׺ֱ�ӷ��ر���
			$remove_pre_table = $table;
			$table = DB_PRE . $table;
		}
		//��ȡ�����ֶ���
		$primary_key = $this->get_primary($table);
		//���������
		$cache_data = array();
		$append = empty($append) ? '' : $append . '_';
		$order_field = empty($order_field) ? $primary_key : $order_field;
		$query = $this->query("SELECT {$fields} FROM `{$table}`" . (empty($where) ? '' : " WHERE {$where}") . " ORDER BY {$order_field} {$asc_desc}" . ($number > 0 ? " LIMIT 0,{$number}" : ''), 'unbuffered');
		while($data = $this->fetch_array($query)) {
			if(isset($data['setting']) && !empty($data['setting'])) {
				$setting = $data['setting'];
				eval("\$setting = $setting;");
				unset($data['setting']);
				$data = array_merge($data, $setting);
			}
			$cache_data[$data[$primary_key]] = $data;
			//һ����¼����Ϊһ���ļ�,����:����+������
			if(!empty($is_line)) cache_write($append . $remove_pre_table . '_' . $data[$primary_key], $data);
		}
		cache_write($append . $remove_pre_table, $cache_data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ����Դ����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function cache_module_resource() {
		$query = $this->query("SELECT * FROM " . DB_PRE . 'module_resource ORDER BY list_order ASC, mr_id ASC', 'unbuffered');
		$cache_mr_data = array();
		while($data = $this->fetch_array($query)) {
			$cache_mr_data[$data['mr_name']] = $data;
		}
		cache_write('module_resource', $cache_mr_data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���湫˾����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function cache_company() { 
		$query = $this->query("SELECT company_id,company_uid,company_name,mr_ids,disabled FROM " . DB_PRE . 'company ORDER BY company_id ASC', 'unbuffered');
		$cache_company_data = $cache_company_uid_data = array();
		while($data = $this->fetch_array($query)) {
			$cache_company_data[$data['company_id']] = $data;
			$cache_company_uid_data[$data['company_uid']] = $data['company_id'];
		}
		cache_write('company', $cache_company_data);
		cache_write('company_uid', $cache_company_uid_data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����ԱȨ��������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function cache_user_group($company_id) {
		if(0 == $company_id) { //�ǳ���ʱ
			$append = '';
			$fields = 'group_id,group_name,mr_ids,is_system,is_super';
			$where = "company_id='" . $company_id . "'";
		} else {
			$append = $company_id;
			$fields = 'group_id,group_name,mr_ids,is_system';
			$where = "company_id IN(0," . $company_id . ") AND is_super=0";
		}
		$this->cache_table('user_group', $append, $fields, 'group_id', 'ASC', $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ��
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function cache_model() {
		$this->cache_table('model');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ���е��ֶ�
	+-----------------------------------------------------------------------
	* @model_id    int   ģ��ID(����ʱ��������)
	+-----------------------------------------------------------------------
	* ����ֵ             ��
	+-----------------------------------------------------------------------
	*/
	public function cache_model_field($model_id = '') {
		if(!empty($model_id)) {
			$this->cache_table('model_field', $model_id, '*', 'list_order', 'ASC', "model_id='" . $model_id . "'");
		} else {
			//��Ҫ��model_id���ź�
			$query = $this->query("SELECT * FROM " . DB_PRE . "model_field ORDER BY model_id ASC,list_order ASC,field_id ASC", 'unbuffered');
			$cache_mf_data = array();
			$temp_model_id = -1; //��ʱģ��ID����
			$i = 0; //����������
			while($row = $this->fetch_array($query)) {
				if(-1 != $temp_model_id && $temp_model_id != $row['model_id']) { //��ʼ��һ������ʱ
					cache_write($temp_model_id . '_model_field', $cache_mf_data[$i]);
					$cache_mf_data[++$i][] = $row;
				} else {
					$cache_mf_data[$i][] = $row;
				}
				$temp_model_id = $row['model_id'];
			}
			if(-1 != $temp_model_id) { //�����ݲŻ���
				cache_write($temp_model_id . '_model_field', $cache_mf_data[$i]);
			}
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ĳ��˾����
	+-----------------------------------------------------------------------
	* @company_id   int  ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ             ��
	+-----------------------------------------------------------------------
	*/
	public function cache_category($company_id) {
		$this->cache_table('category', $company_id, '*', 'list_order,cat_id', 'ASC', "company_id='" . $company_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���浥ҳid��content_idֵ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function cache_page_id($company_id) { 
		$query = $this->query("SELECT content_id,cat_id FROM " . DB_PRE . "content_page WHERE `company_id`='{$this->company_id}' ORDER BY cat_id ASC", 'unbuffered');
		$cache_page_data = array();
		while($data = $this->fetch_array($query)) {
			$cache_page_data[$data['cat_id']] = $data['content_id'];
		}
		cache_write($company_id . '_page', $cache_page_data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ������������
	+-----------------------------------------------------------------------
	* @company_id  int      ��˾ID,Ĭ��-100��û������������
	* @read        boolen   �Ƿ��ȡ����
	+-----------------------------------------------------------------------
	* ����ֵ                ��
	+-----------------------------------------------------------------------
	*/
	public function cache_all($company_id = -100, $read = FALSE) {
		$this->cache_company();
		if($read) { $GLOBALS['QXDREAM']['COMPANY_UID'] = cache_read('company_uid'); }
		if($company_id < 0) {
			$company_id = isset($GLOBALS['QXDREAM']['qx_company_id']) && $GLOBALS['QXDREAM']['qx_company_id'] >= 0 ? $GLOBALS['QXDREAM']['qx_company_id'] : 0;
		}
		$this->cache_module_resource();
		$this->cache_user_group($company_id);
		
		if(0 == $company_id) { //��������Ա
			$this->cache_model();
			$this->cache_model_field();
		} else { //��˾�˻�
			$this->cache_category($company_id);
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ĳ����˾����
	+-----------------------------------------------------------------------
	* @company_id  int    ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ              ��
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_company($company_id) {
		cache_delete($company_id . '_user_group');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ĳ��ģ���ֶλ���
	+-----------------------------------------------------------------------
	* @model_id    int   ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ             ��
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_model_field($model_id) {
		cache_delete($model_id . '_model_field');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ĳ��˾���໺��
	+-----------------------------------------------------------------------
	* @company_id    int  ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ              ��
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_category($company_id) {
		cache_delete($company_id . '_category');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ��ĳ��˾��ҳ����
	+-----------------------------------------------------------------------
	* @company_id    int  ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ              ��
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_page($company_id) {
		cache_delete($company_id . '_page');
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ���ݱ��setting�ֶ�����
	+-----------------------------------------------------------------------
	* @table    string    ���ݱ���
	* @where    string    ��ѯ����
	+-----------------------------------------------------------------------
	* ����ֵ    array     ���õ�����
	+-----------------------------------------------------------------------
	*/
	public function get_setting($table, $where) {
		$data = $this->fetch("SELECT `setting` FROM `" . $table . "` WHERE " . $where);
		//ȥ��ת���ַ�
		$setting = $data['setting'];
		if(!empty($setting)) eval("\$setting = $setting;");
		return $setting;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ���ݱ��setting�ֶ�����
	+-----------------------------------------------------------------------
	* @table          string  ���ݱ���
	* @setting_arr    string  ��ѯ����
	* @where          string  ��������
	+-----------------------------------------------------------------------
	* ����ֵ          boolen  �ɹ�������,ʧ�����ؼ�
	+-----------------------------------------------------------------------
	*/
	public function set_setting($table, $setting_arr, $where) {
		if(!is_array($setting_arr)) return FALSE;
		$setting_arr = unslash($setting_arr); //��ȥת��,��Ϊ����ʱvar_export��Ե�����ת��
		//addslashes����ת��(��������ʱ���ݿ��ȥ��ת��,������)
		$setting = addslashes(var_export($setting_arr, TRUE));
		return $this->query("UPDATE `". $table . "` SET `setting`='" . $setting . "' WHERE " . $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function msg($is_admin = 0) {
		return $GLOBALS['QXDREAM'][(0 == $is_admin ? 'language' : 'admin_language')][$this->msg];
	}
}
?>