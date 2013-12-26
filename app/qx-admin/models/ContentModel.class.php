<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Muticategory
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-03-08 ��̨������ $
	@version  $Id: ContentModel.class.php 1.2 2011-05-14
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ContentModel extends Model {

	public $content_table;       //���ݱ�
	public $detail_table;        //�������ݱ�
	public $attachment_table;    //������
	public $category_table;      //�����
	public $user_table;          //�û���
	
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
		$this->content_table = DB_PRE . 'content';
		$this->category_table = DB_PRE . 'category';
		$this->attachment_table = DB_PRE . 'attachment';
		$this->user_table = DB_PRE . 'user';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ò���
	+-----------------------------------------------------------------------
	* @company_id    ��˾ID
	* @company_uid   ��˾UID
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function set($company_id, $company_uid = '') {
		$this->company_id = $company_id;
		$this->company_uid = empty($company_uid) ? $GLOBALS['QXDREAM']['COMPANY'][$company_id]['company_uid'] : $company_uid; 
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ģ��(����ҳʱ��������)
	+-----------------------------------------------------------------------
	* @model_name    ģ����
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function set_model($model_name) {
		$this->detail_table = DB_PRE . 'content_' . $model_name;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡһƪ����
	+-----------------------------------------------------------------------
	* @content_id  ����ID
	+-----------------------------------------------------------------------
	* ����ֵ       ��������
	+-----------------------------------------------------------------------
	*/
	public function get($content_id) {
		$data = array();
		//��ȡcontent_id�Ա����ʱʹ��
		$sql = "SELECT a.content_id,a.model_id,a.cat_id,a.user_id,a.title,a.description,a.status,a.attachment_id,a.hits_count,a.post_time,
				d.* FROM {$this->content_table} a,{$this->detail_table} d
				WHERE a.content_id=d.content_id AND a.content_id='{$content_id}' AND a.company_id='{$this->company_id}'";
		$data = $this->fetch($sql);
		if(is_array($data)) {
			//����
			if(!empty($data['attachment_id'])) {
				$data['attachment'] = $this->fetch_all("SELECT `attachment_id`,`filename`,`file_path`,`file_size`,`post_time`,`is_image` FROM `{$this->attachment_table}` WHERE `attachment_id` IN({$data['attachment_id']}) AND company_id='{$this->company_id}'");
			}
		}
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡselect��������
	+-----------------------------------------------------------------------
	* @model_id        ģ��
	* @select_cat_id   ѡ�еķ���
	+-----------------------------------------------------------------------
	* ����ֵ           ���������ַ���
	+-----------------------------------------------------------------------
	*/
	public function get_category_option($model_id = 0, $select_cat_id = 0) {
		$select_option = $selected = '';
		foreach($GLOBALS['QXDREAM']['CATEGORY'] as $k => $v) {
			$selected = !empty($select_cat_id) && $v['cat_id'] == $select_cat_id ? ' selected="selected"' : ''; 
			if($v['model_id'] == $model_id && $v['type'] == 0 && $v['has_child'] == 0) $select_option .= '<option value="' . $v['cat_id'] . '"' . $selected . '>' . $v['cat_name'] . "</option>\n"; 
		}
		return $select_option;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������
	+-----------------------------------------------------------------------
	* @base_arr     ������Ϣ����
	* @detail_arr   ��ϸ��Ϣ����
	+-----------------------------------------------------------------------
	* ����ֵ        �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function add($base_arr, $detail_arr) {
		if(!is_array($base_arr) || !is_array($detail_arr)) return FALSE;
		$base_arr['user_id'] = $GLOBALS['QXDREAM']['qx_user_id'];
		$base_arr['company_id'] = $this->company_id;
		$base_arr['company_uid'] = $this->company_uid;
		$base_arr['post_time'] = $base_arr['update_time'] = $GLOBALS['QXDREAM']['timestamp'];
		if($this->insert($this->content_table, $base_arr)) {
			$content_id = $this->last_insert_id();
			if(isset($detail_arr['author'])) {
				$detail_arr['author'] = empty($detail_arr['author']) ? $GLOBALS['QXDREAM']['qx_user_name'] : $detail_arr['author'];
			}
			$detail_arr['content_id'] = $content_id;
			$this->insert($this->detail_table, $detail_arr);
			if($base_arr['status'] == 1) {
				$this->add_content_count($base_arr['cat_id'], $GLOBALS['QXDREAM']['qx_user_id']);
			}
			//������
			if(isset($base_arr['attachment_id'])) {
				$this->query("UPDATE `{$this->attachment_table}` SET `content_id`='{$content_id}' WHERE `attachment_id` IN({$base_arr['attachment_id']}) AND company_id='{$this->company_id}'");
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��������
	+-----------------------------------------------------------------------
	* @old_arr      ����ǰ������
	* @base_arr     ������Ϣ����
	* @detail_arr   ��ϸ��Ϣ����
	+-----------------------------------------------------------------------
	* ����ֵ        �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function edit($old_arr, $base_arr, $detail_arr) {
		if(!is_array($old_arr) || !is_array($base_arr) || !is_array($detail_arr)) return FALSE;	
		//�������ʱ��
		$base_arr['update_time'] = $GLOBALS['QXDREAM']['timestamp'];
		$where = "`content_id`='" . $old_arr['content_id'] . "'";
		//������
		if(isset($base_arr['attachment_id'])) {
			$this->query("UPDATE `{$this -> attachment_table}` SET `content_id`='{$old_arr['content_id']}' WHERE `attachment_id` IN({$base_arr['attachment_id']})");
			if(!empty($old_arr['attachment_id'])) { $base_arr['attachment_id'] = $old_arr['attachment_id'] . ',' . $base_arr['attachment_id']; }
		} 
		if($this->update($this->content_table, $base_arr, $where)) {
			//��������
			if(isset($detail_arr['author'])) {
				$detail_arr['author'] = empty($detail_arr['author']) ? $GLOBALS['QXDREAM']['qx_user_name'] : $detail_arr['author'];
			}
			$this->update($this->detail_table, $detail_arr, $where);
			//���ԭ�����ǲݸ�,�Ÿ���ͳ�ƴ���(�ݸ巢��),����Ǳ���û��Ĳݸ�,���û�����,��ô��ƪ����Ϊ��������û���
			if($old_arr['status'] == 3 && $base_arr['status'] == 1) {
				$this->add_content_count($base_arr['cat_id'], $GLOBALS['QXDREAM']['qx_user_id']);
				$base_arr['user_id'] = $GLOBALS['QXDREAM']['qx_user_id'];
			}
			if(3 != $old_arr['model_id']) {
				//����ı���,Ҫͳ�Ʒ�������,ԭ���ǲݸ�Ҳ����ͳ��
				if($old_arr['cat_id'] != $base_arr['cat_id'] && $old_arr['status'] != 3) {
					$this->cat_content_count($old_arr['cat_id'], $base_arr['cat_id']);
				}
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* �޸����ݵķ���ı�ʱ,ͳ�Ʒ�����������
	+-----------------------------------------------------------------------
	* @old_cat_id     �ϵķ���
	* @new_cat_id     �µķ���
	+-----------------------------------------------------------------------
	* ����ֵ          ��
	+-----------------------------------------------------------------------
	*/
	public function cat_content_count($old_cat_id, $new_cat_id) {
		$this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`-1 WHERE `cat_id`='{$old_cat_id}'  AND company_id='{$this->company_id}'");
		$this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`+1 WHERE `cat_id`='{$new_cat_id}'  AND company_id='{$this->company_id}'");
	}
	/**
	+-----------------------------------------------------------------------
	* ����״̬(������)
	+-----------------------------------------------------------------------
	* @content_id      ����ID(��Ϊ����)
	* @value           1Ϊ��ԭ,2Ϊ��������վ
	*                  �ƶ�������վ,��������1,�÷������������1,��֮��1
	*                  �ݸ���������վ����ͳ��
	+-----------------------------------------------------------------------
	* ����ֵ           �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function status($content_id, $value) {
		if($value == 1) {
			$operation = '+';
		} elseif($value == 2) {
			$operation = '-';
		} else {
			show_msg('invalid_request');
		}
		if(1 != $value && 2 != $value) { show_msg('invalid_request'); }
		if(is_array($content_id)) {
			$post_countent_count = count($content_id);
			$content_id_str = implode(',', $content_id);
			//Ҫ���µ�����
			$update_content_data = $this->fetch_all_column_key("SELECT `content_id`,`cat_id`,`status`,`user_id` FROM `{$this->content_table}` WHERE `content_id` IN({$content_id_str}) AND `status`!='{$value}'", 'content_id');
			if($post_countent_count == count($update_content_data)) { //�ύ��������ʵ������һ��ʱ�ż�������ֹ���±�ʱ��������
				$recycle_update_content_data = array(); //��������վʱ��������
				foreach($content_id as $v) {
					if(!($update_content_data[$v]['status'] == 3 && $value == 2)) {
						$cat_id = is_array($update_content_data[$v]) ? $update_content_data[$v]['cat_id'] : -1;
						'+' == $operation ? $this->add_content_count($cat_id, $update_content_data[$v]['user_id']) : $recycle_update_content_data[$cat_id . '_' . $update_content_data[$v]['user_id']] = array('cat_id' => $cat_id, 'user_id' => $update_content_data[$v]['user_id']);
					}
				}
				$this->query("UPDATE `{$this->content_table}` SET `status`='{$value}' WHERE `content_id` IN({$content_id_str})");
				if('-' == $operation) { //����ͳ�ƹ��ܱ���д�ڸ���֮��
					foreach($recycle_update_content_data as $k => $v) { $this->count_content($v['cat_id'], $v['user_id']); }
				}
			}
		} else {
			$data = $this->fetch("SELECT `cat_id`,`status`,`user_id` FROM `{$this->content_table}` WHERE `content_id`='{$content_id}' AND `status`!='{$value}'");
			if(is_array($data)) {
				$this->query("UPDATE `{$this->content_table}` SET `status`='{$value}' WHERE `content_id`='{$content_id}'");
				if(!($data['status'] == 3 && $value == 2)) { //���ԭ���ǲݸ�,��������������վ,��ͳ��
					$cat_id = is_array($data) ? $data['cat_id'] : -1; //����������-1,��operation_content_count��-1�����ø��·����������������,Ҳ����δ����
					'+' == $operation ? $this->add_content_count($cat_id, $data['user_id']) : $this->count_content($cat_id, $data['user_id']);
				}
			}
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��������ͳ��
	+-----------------------------------------------------------------------
	* @cat_id              ����ID
	* @user_id             �û�ID
	+-----------------------------------------------------------------------
	* ����ֵ               ��
	+-----------------------------------------------------------------------
	*/
	public function add_content_count($cat_id, $user_id) { //�Ժ����Ż�
		$this->query("UPDATE `{$this->user_table}` SET `content_count`=`content_count`+1 WHERE `user_id`='{$user_id}' AND company_id='{$this->company_id}'");
		if($cat_id >= 0) $this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`+1 WHERE `cat_id`='{$cat_id}' AND company_id='{$this->company_id}'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ͳ����������(һ��ɾ��ʱʹ��)
	+-----------------------------------------------------------------------
	* @cat_id              ����ID
	* @user_id             �û�ID
	+-----------------------------------------------------------------------
	* ����ֵ               ��
	+-----------------------------------------------------------------------
	*/
	public function count_content($cat_id, $user_id) {
		$user_content_count = $this->result("SELECT COUNT(*) FROM `{$this->content_table}` WHERE `user_id`='{$user_id}' AND `company_id`='{$this->company_id}' AND `status`='1'");
		$this->query("UPDATE `{$this->user_table}` SET `content_count`='{$user_content_count}' WHERE `user_id`='{$user_id}' AND company_id='{$this->company_id}'");
		if($cat_id >= 0) {
			$cat_content_count = $this->result("SELECT COUNT(*) FROM `{$this->content_table}` WHERE `cat_id`='{$cat_id}' AND `company_id`='{$this->company_id}' AND `status`='1'");
			$this->query("UPDATE `{$this->category_table}` SET `content_count`='{$cat_content_count}' WHERE `cat_id`='{$cat_id}' AND `company_id`='{$this->company_id}'");
		}
	} 
	
	/**
	+-----------------------------------------------------------------------
	* ����ɾ������
	+-----------------------------------------------------------------------
	* @content_id    ����ID
	+-----------------------------------------------------------------------
	* ����ֵ         �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function remove($content_id) {
		//��������
		$this->query("DELETE FROM `{$this->content_table}` WHERE `content_id`='{$content_id}' AND model_id!='3' AND company_id='{$this->company_id}'");
		$this->query("DELETE FROM `{$this->detail_table}` WHERE `content_id`='{$content_id}'");
		//������
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id`='{$content_id}' AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ͨ����ĿID��ȡ��ҳ���ݵ�content_id
	+-----------------------------------------------------------------------
	* @cat_id        ��ĿID
	+-----------------------------------------------------------------------
	* ����ֵ         ����content_id
	+-----------------------------------------------------------------------
	*/
	public function get_page_content_id($cat_id) {
		$page_id_data = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_page');
		return $page_id_data[$cat_id];
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ɾ����ҳ��������
	+-----------------------------------------------------------------------
	* @cat_id        ��ĿID
	+-----------------------------------------------------------------------
	* ����ֵ         �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function remove_page($cat_id) {
		$content_id = $this->get_page_content_id($cat_id);
		//��������
		$this->query("DELETE FROM `{$this->content_table}` WHERE `cat_id`='{$cat_id}' AND model_id='3' AND company_id='{$this->company_id}'");
		$this->query("DELETE FROM `{$this->detail_table}` WHERE `cat_id`='{$cat_id}'");
		//������
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id`='{$content_id}' AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ջ���վ(statusΪ2��)
	+-----------------------------------------------------------------------
	* @model_id      ģ��ID
	* @cat_id        ��ĿID      
	+-----------------------------------------------------------------------
	* ����ֵ         �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function empty_recycle_bin($model_id = '', $cat_id = '') {
		$where = !empty($model_id) ? " AND `model_id`='{$model_id}'" : '';
		$where .= !empty($cat_id) ? " AND `cat_id`='{$cat_id}'" : '';
		$data = $this->fetch_all_column_key("SELECT `content_id`, `model_id` FROM `{$this->content_table}` WHERE company_id='{$this->company_id}' AND `status`='2'{$where}", 'content_id');
		if(count($data) == 0) {
			$this->msg = 'recycle_bin_has_not_any_data';
			return FALSE;
		}
		$content_id_str = implode(',', array_keys($data));
		//detail_table��ģ���йأ�����
		if(empty($model_id)) { //�������ģ�͵Ļ���վ
			foreach($data as $v) {
				$this->query("DELETE FROM `" . DB_PRE . 'content_' . $GLOBALS['QXDREAM']['MODEL'][$v['model_id']]['model_name'] . "` WHERE `content_id`='{$v['content_id']}'");
			}
		} else { //���ĳ��ģ�����
			$this->query("DELETE FROM `{$this->detail_table}` WHERE `content_id` IN({$content_id_str})");
		}
		$this->query("DELETE FROM `{$this->content_table}` WHERE company_id='{$this->company_id}' AND `status`='2'{$where}");
			//������
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id` IN({$content_id_str}) AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����б���ʾ
	+-----------------------------------------------------------------------
	* @cat_id     ����ID
	* @user_id    �û�ID
	* @status     ״̬
	* @model_id   ģ��
	* @search_key �����ؼ���
	+-----------------------------------------------------------------------
	* ����ֵ    ��������
	+-----------------------------------------------------------------------
	*/
	public function list_info($cat_id = FALSE, $user_id = 0, $status = 1, $model_id = 0, $search_key = '') {
		$where = '';
		if($cat_id !== FALSE) {
			$where .= empty($cat_id) ? " AND a.cat_id='{$cat_id}'" : (isset($GLOBALS['QXDREAM']['CATEGORY'][$cat_id]) ? " AND a.cat_id IN(" . $GLOBALS['QXDREAM']['CATEGORY'][$cat_id]['all_child_id'] . ")" : '');
		}
		!empty($user_id) && $where .= " AND a.user_id='{$user_id}'";
		!empty($model_id) && $where .= " AND a.model_id='{$model_id}'";
		$search_key = trim($search_key);
		if(!empty($search_key)) {
			//5���ؼ���
			$search_key_arr =  explode_keyword($search_key, 5, ' ');
			$where .= " AND (a.title LIKE ";
			$count = count($search_key_arr);
			for($i=0; $i < $count - 1; $i++) { //���һ����������
				$where .= "'%" . $search_key_arr[$i] . "%' OR a.title LIKE ";
			}
			$where .= "'%" . $search_key_arr[$count - 1] . "%')";
		}
		$where = " WHERE a.status='{$status}' AND a.company_id='{$this->company_id}'" . $where;
		$this->count("SELECT COUNT(*) FROM {$this->content_table} a{$where}");
		$sql = "SELECT a.content_id,a.cat_id,a.title,a.post_time,a.update_time,a.hits_count,a.user_id,a.model_id,u.user_name FROM {$this->content_table} a
				LEFT JOIN {$this->user_table} u ON a.user_id=u.user_id
				{$where} ORDER BY a.content_id DESC" . $this->pagenation->sql_limit();
		$query = $this->query($sql, 'unbuffered');
		$data = array();
		while($row = $this->fetch_array($query)) {
			if($row['post_time'] == $row['update_time']) {
				$row['date_info'] = $GLOBALS['QXDREAM']['admin_language']['already_post'];
				$row['time'] = $row['post_time'];
			} else {
				$row['date_info'] = $GLOBALS['QXDREAM']['admin_language']['last_edit'];
				$row['time'] = $row['update_time'];
			}
			unset($row['post_time'], $row['update_time']);
			$data[] = $row;
		}
		$this->free_result($query);
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������״̬���ݵ�����
	+-----------------------------------------------------------------------
	* @model_id    ģ��ID
	* @cat_id      ����ID
	+-----------------------------------------------------------------------
	* ����ֵ       ����
	+-----------------------------------------------------------------------
	*/
	function content_count($model_id = 0, $cat_id = FALSE) {
		$where = '';
		$has_where = FALSE;
		if(0 != $model_id) { $has_where = append_where($where, $has_where, "`model_id`='{$model_id}'"); }
		if(FALSE !== $cat_id) { $has_where = append_where($where, $has_where, "`cat_id`='{$cat_id}'"); }
		$has_where = append_where($where, $has_where, "`company_id`='{$this->company_id}'");
		$data = array();
		$query = $this->query("SELECT COUNT(*) as `count`,`status` AS `link_val` FROM `{$this->content_table}`{$where} GROUP BY `status` ORDER BY `status`", 'unbuffered');
		$i = 0;
		while($row = $this->fetch_array($query)) {
			switch($row['link_val']) {
				case '1': //����
					$i = 1;
					$row['text'] = $GLOBALS['QXDREAM']['admin_language']['already_post'];
				break;
				
				case '2': //����վ
					$i = 3;
					$row['text'] = $GLOBALS['QXDREAM']['admin_language']['recycle_bin'];
				break;
				
				case '3': //�ݸ�
					$i = 2;
					$row['text'] = $GLOBALS['QXDREAM']['admin_language']['draft'];
				break;
			}
			$data[$i] = $row;
		}
		ksort($data);
		return $data;
	}
}
?>