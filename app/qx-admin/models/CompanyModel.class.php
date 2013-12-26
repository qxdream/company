<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-03-06 ��̨��˾ģ�� $
	@version  $Id: CompanyModel.class.php 1.0 2011-03-23
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CompanyModel extends Model {
	public $table;              //��Ϣ��ʾ
	
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
		$this->table = DB_PRE . 'company';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡĳ��˾��Ϣ
	+-----------------------------------------------------------------------
	* @company_id    int or string ��˾ID��Ӣ��ID
	* @cid_type      int           ID����,0Ϊ����ID,1ΪӢ��ID
	+-----------------------------------------------------------------------
	* ����ֵ         ���򷵻ظù�˾����,���򷵻�FASLE
	+-----------------------------------------------------------------------
	*/
	public function get_one($company_id, $cid_type = 0) {
		$id_field = 0 == $cid_type ? "`company_id`" : "`company_uid`";
		return $this->fetch("SELECT * FROM " . $this->table . " WHERE " . $id_field . "='" . $company_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��˾�б�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��˾����
	+-----------------------------------------------------------------------
	*/
	public function list_info() {
		$this->count("SELECT COUNT(*) FROM " . $this->table);
		return $this->fetch_all("SELECT `company_id`,`company_uid`,`company_name`,`post_time`,`hits_count`,`disabled` FROM `{$this->table}` ORDER BY `company_id` DESC" . $this->pagenation->sql_limit(), 'unbuffered');
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����¹�˾
	+-----------------------------------------------------------------------
	* @data    array    ������˾����
	+-----------------------------------------------------------------------
	* ����ֵ            �����ID
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
	* ��֤Ҫ���ӵ��¹�˾
	+-----------------------------------------------------------------------
	* @data    array    ������˾����
	+-----------------------------------------------------------------------
	* ����ֵ            �ɹ�������,ʧ�ܷ��ؼ�
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
	* ���¹�˾
	+-----------------------------------------------------------------------
	* @data    array    ��˾����
	+-----------------------------------------------------------------------
	* ����ֵ            �ɹ�������
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
	* ���������ù�˾
	+-----------------------------------------------------------------------
	* @company_id      int  Ҫ���õĹ�˾ID
	* @disabled_value  int  ����Ϊ0,����Ϊ1
	+-----------------------------------------------------------------------
	* ����ֵ                �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function disable($company_id, $disabled_value) {
		$this->update($this->table, array('disabled' => $disabled_value), "company_id='" . $company_id . "'");
		$this->cache_company();
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ����˾(��ɾ�����������븽��,���඼ɾ��)
	+-----------------------------------------------------------------------
	* @company_id      int    ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ                  �ɹ�������
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
		$this->delete(DB_PRE . 'content', "company_id='" . $company_id ."' AND `model_id`='3'"); //ɾ����ҳ
		$this->delete(DB_PRE . 'content_page', "company_id='" . $company_id ."'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡģ����Դ
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function get_module_resource() {
		return cache_read('module_resource');
	}
	
}
?>