<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-03-28 �û�ģ�� $
	@version  $Id: CategoryModel.class.php 1.0 2011-05-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CategoryModel extends Model {

	public $category_table;      //�����
	public $msg;                 //��Ϣ��ʾ
	private $is;                 //�����ݹ��ж��Ƿ��ƶ�������
	public $category = array();  //��������
	
	/**
	+-----------------------------------------------------------------------
	* ��������
	+-----------------------------------------------------------------------
	* @company_uid   boolen or string ��˾UID
	* @company_id    boolen or string ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ                ��
	+-----------------------------------------------------------------------
	*/
	public function set($company_uid, $company_id = FALSE) {
		$this->category_table = DB_PRE . 'category';
		if(FALSE !== $company_id && FALSE !== $company_uid) {
			$this->company_uid = $company_uid;
			$this->company_id = $company_id;
		} elseif(FALSE !== $company_uid) {
			$this->company_uid = $company_uid;
			$this->company_id = $GLOBALS['QXDREAM']['COMPANY_UID'][$company_uid];
		} else {
			$this->company_id = $company_id;
			$this->company_uid = $GLOBALS['QXDREAM']['COMPANY'][$company_id]['company_uid'];
		}
		$this->content_table  = DB_PRE . 'content';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ����
	+-----------------------------------------------------------------------
	* @cat_id  ����ID
	+-----------------------------------------------------------------------
	* ����ֵ   ��Դ���
	+-----------------------------------------------------------------------
	*/
	public function get($cat_id) {
		return $this->fetch("SELECT `cat_id`,`type`,`model_id`,`parent_id`,`cat_name`,`is_nav`,`content_count`,`setting`,`template`,`url` FROM `{$this->category_table}` WHERE `cat_id`='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��Դ���
	+-----------------------------------------------------------------------
	*/
	public function get_category() {
		$query = $this->query("SELECT `parent_id`,`cat_id`,`has_child`,`all_child_id`,`type` FROM `{$this -> category_table}` WHERE `company_uid`='{$this->company_uid}'", 'unbuffered');
		while($row = $this->fetch_array($query)) {
			$this->category[$row['cat_id']] = $row;
		}
		$this->free_result($query);
		return $this->category;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ӷ���
	* ���һ������,��Ҫ��������¼�е�has_child�ĸ���Ϊ0,all_child_id����Ϊ��
	* �����ID,ֻҪ�и���,�����Ǹ���ĸ���,��Ҫ�Ѹ����has_child����Ϊ1,�Ѹ�
	* ���all_child_id����Ϊ�����all_child_id�����������ڲ�������ID,�ݹ�,
	* ֱ�������parent_idΪ0
	+-----------------------------------------------------------------------
	* @array     ���������
	* @set_array Ϊ�����������,���뵽setting�ֶ�
	+-----------------------------------------------------------------------
	* ����ֵ     �ɹ�������,���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function add($array, $set_array = '') {
		if(!is_array($array)) return FALSE;
		$cat_name = $array['cat_name'];
		$data = $this->fetch("SELECT `cat_id` FROM `{$this->category_table}` WHERE `cat_name`='{$cat_name}' AND `company_uid`='{$this->company_uid}'");
		unset($cat_name);
		if(is_array($data)) {
			$this->msg = 'category_name_exists';
			return FALSE;
		}
		$array['company_id'] = $this->company_id;
		$array['company_uid'] = $this->company_uid;
		$this->insert($this->category_table, $array);
		if($this->affected_rows() > 0) {
			$cat_id = $this->last_insert_id();
			$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$cat_id}' WHERE `cat_id`='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
			$where = "`cat_id`='" . $cat_id . "'";
			!empty($set_array) && $this->set_setting($this->category_table, $set_array, $where);
			if($array['parent_id'] > 0) {
				$this->update_all_child_id($array['parent_id'], $cat_id);
			}
			return $cat_id;
		} else {
			$this->msg = 'operation_fail';
			return FALSE;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ����all_child_id�ֶ�,��IDΪ0����(˽�з���)
	+-----------------------------------------------------------------------
	* @parent_id     ��ID
	* @cat_id        ������ID
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function update_all_child_id($parent_id, $cat_id) {
		if($parent_id == 0) return FALSE;
		$all_child_id = $this->category[$parent_id]['all_child_id'] . ',' . $cat_id;
		$set = $this->category[$parent_id]['has_child'] == 0 ? ',`has_child`=1' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		$this->update_all_child_id($this->category[$parent_id]['parent_id'], $cat_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���·���
	* ����������ڱ�����,��ôֻ�ƶ�����,������µ��������������������,��ô��
	* ����Ҳ�ƶ���������
	+-----------------------------------------------------------------------
	* @cat_id   ����ID
	* @array    ���µ�����
	* @set_array Ϊ�����������,���µ�setting�ֶ�
	+-----------------------------------------------------------------------
	* ����ֵ    �ɹ�������,ʧ�ܻ���û���޸����ݷ��ؼ�
	+-----------------------------------------------------------------------
	*/
	public function edit($cat_id, $array, $set_array) {
		if(!is_array($array)) return FALSE;
		if($array['parent_id'] == $cat_id) {
			$this->msg = 'parent_cannot_be_self';
			return FALSE;
		}
		//������Ӹ����ƶ����Ӽ�
		if($this->is_move_parent_to_child($cat_id, $array['parent_id'])) {
			$this->msg = 'not_allowed_move_to_child';
			return FALSE;
		}
		$cat_name = $array['cat_name'];
		//��ѯ���Ǹ�ID������������û�е��ڱ��ύ��
		$data = $this->fetch("SELECT `cat_id` FROM `{$this->category_table}` WHERE `cat_name`='{$cat_name}' AND `cat_id`!='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
		unset($cat_name);
		if(is_array($data)) {
			$this->msg = 'category_name_exists';
			return FALSE;
		}
		$where = "`cat_id`='" . $cat_id . "'";
		//����Ӱ��mysql����
		$n = 0;
		$this->update($this->category_table, $array, $where);
		$this->affected_rows() > 0 && $n++;
		!empty($set_array) && $this->set_setting($this->category_table, $set_array, $where);
		$this->affected_rows() > 0 && $n++;
		unset($where);
		if($n > 0) { //�����¼û���޸Ĺ�,Ӱ����������0,mysql_affected_rows��ָ���update,insert,deleteӰ�������
			//�����ĸ���ı��˲�ִ�����²���
			if($array['parent_id'] != $this->category[$cat_id]['parent_id']) {
				//�Ƿ�ֻ�ڱ����ƶ�,������Ҹ�ID�ĸ������Ƿ����Ŀ�길��($array['parent_id'])(����IDΪ0),�ҵ��Ļ�,˵�����ڱ������ƶ�
				//��������Ϊ0ʱ,Ҳ�����ڱ������ƶ�
				if($this->is_move_in_self_category($cat_id, $array['parent_id']) && $array['parent_id'] != 0) {
					/* ��������has_childΪ1,��ô�Ѹ����һ�������parent_id��Ϊ�����parent_id��has_child����Ϊ0,
					 * �Ѹ����all_child_id����Ϊ$cat_id,�ݹ�ȥ��Ŀ�길�������֮���¼��all_child_id�еĸ���ID
					 */
				 	if($this->category[$cat_id]['has_child'] == 1) { //����÷����µ�������
						$this->query("UPDATE `{$this->category_table}` SET `has_child`=0,`all_child_id`='{$cat_id}' WHERE `cat_id`='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
						$id_arr = array();
						foreach($this->category as $k => $v) {
							if($v['parent_id'] == $cat_id) $id_arr[] = $k;
						}
						$id_arr = implode(',', $id_arr);
						$this->query("UPDATE `{$this->category_table}` SET `parent_id`='" . $this->category[$cat_id]['parent_id'] . "' WHERE `cat_id` IN({$id_arr}) AND `company_uid`='{$this->company_uid}'");
					}
					//����÷���ĸ�����(��Ŀ����༰�ϼ�����)
					$this->delete_id_from_old_parent($cat_id, $this->category[$cat_id]['parent_id'], $array['parent_id']);
				} else { //�����ڱ������ƶ�
					$parent_id = $this->category[$cat_id]['parent_id'];
					$all_child_id = $this->category[$cat_id]['all_child_id'];
					$parent_id != 0 && $this->delete_all_child_id($parent_id, $all_child_id);
					if($array['parent_id'] != 0) { //�����ƶ�����������,Ҫ�ѱ����all_child_id����ӵ������,all_child_id��,�����has_childΪ0�Ļ���Ҫ����Ϊ1
						$this->add_all_child_id($array['parent_id'], $all_child_id);
					}
				}
			}
			return TRUE;
		} else {
			$this->msg = 'operation_fail_or_not_repair';
			return FALSE;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ����all_child_id�ֶ�(����),��IDΪ0����,һ��Ҫ�ڶ�������º����(˽�з���)
	+-----------------------------------------------------------------------
	* @parent_id     ��ID
	* @all_child_id  ��������ID(��������)
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function add_all_child_id($parent_id, $all_child_id) {
		$parent_all_child_id = $this->category[$parent_id]['all_child_id'] . ',' . $all_child_id;
		$set = $this->category[$parent_id]['has_child'] == 0 ? ',`has_child`=1' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$parent_all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		if($this->category[$parent_id]['parent_id'] == 0) return FALSE;
		$this->add_all_child_id($this->category[$parent_id]['parent_id'], $all_child_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ�ɾ���������еĸ�ID(˽�з���)
	+-----------------------------------------------------------------------
	* @cat_id          Ҫɾ����ID
	* @parent_id       ������ID
	* @post_parent_id  ���ύ��ID
	+-----------------------------------------------------------------------
	* ����ֵ           �ݹ����ʱ���ؼ�
	+-----------------------------------------------------------------------
	*/
	public function delete_id_from_old_parent($cat_id, $parent_id, $post_parent_id) {
		if($parent_id == $post_parent_id) return FALSE;
		$parent_all_child_id = $this->category[$parent_id]['all_child_id'];
		$parent_all_child_id = delete_cat_id($parent_all_child_id, $cat_id);
		//�����all_child_id��ֻ��һ��IDʱ(�����ж���),��˵��û������,����has_childΪ0
		$set = !strstr($parent_all_child_id, ',') ? ',`has_child`=0' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$parent_all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		return $this->delete_id_from_old_parent($cat_id, $this->category[$parent_id]['parent_id'], $post_parent_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ��ж��Ƿ��ڱ������ƶ�--�����ൽ����(˽�з���)
	+-----------------------------------------------------------------------
	* @parent_id       ��ID
	* @post_parent_id  ���ύ��ID
	+-----------------------------------------------------------------------
	* ����ֵ           �Ƿ�����,���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function is_move_in_self_category($parent_id, $post_parent_id) {
		if($parent_id == 0) return FALSE; //��ʱ�˳��ݹ飬˵��û�в����������ƶ�
		if($this->category[$parent_id]['parent_id'] == $post_parent_id) return TRUE;
		//����Ҫ��return,��Ȼ���û�з���ֵ
		return $this->is_move_in_self_category($this->category[$parent_id]['parent_id'], $post_parent_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ��һ������
	+-----------------------------------------------------------------------
	* @parent_id       ��ID
	+-----------------------------------------------------------------------
	* ����ֵ           ��������
	+-----------------------------------------------------------------------
	*/
	public function get_child($parent_id) {
		$child_arr = array();
		foreach($this->category as $k => $v) {
			if($v['parent_id'] == $parent_id) $child_arr[$k] = $v;
		}
		return $child_arr;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ��ж��Ƿ��ڱ������ƶ�--�Ӹ��ൽ����(˽�з���)
	+-----------------------------------------------------------------------
	* @parent_id       ��ID
	* @post_parent_id  ���ύ��ID
	+-----------------------------------------------------------------------
	* ����ֵ           �Ƿ�����,���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function is_move_parent_to_child($parent_id, $post_parent_id) {
		$child_arr = array();
		$child_arr = $this->get_child($parent_id);//��ȡparent_id���ӷ���
		foreach($child_arr as $k => $v) { 
			if($v['cat_id'] == $post_parent_id) $this->is = TRUE;
			//�ݹ��ȡ$v['cat_id']������ӷ���
			$this->is_move_parent_to_child($v['cat_id'], $post_parent_id);
		}
		return $this->is;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ݹ����all_child_id�ֶ�,��IDΪ0����,һ��Ҫ�ڶ�������º����(˽�з���)
	+-----------------------------------------------------------------------
	* @parent_id     ��ID
	* @all_child_id  ��������ID(��������)
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function delete_all_child_id($parent_id, $all_child_id) {
		$parent_all_child_id = $this->category[$parent_id]['all_child_id'];
		$parent_all_child_id = delete_cat_id($parent_all_child_id, $all_child_id);
		//�����all_child_id��ֻ��һ��IDʱ(�����ж���),��˵��û������,����has_childΪ0
		$set = !strstr($parent_all_child_id, ',') ? ',`has_child`=0' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$parent_all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		if($this->category[$parent_id]['parent_id'] == 0) return FALSE;
		$this->delete_all_child_id($this->category[$parent_id]['parent_id'], $all_child_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ������(����ӷ���һ��ɾ��)
	* ɾ��һ������,Ҫ�ݹ�����и������е�all_child_id�и÷����IDȥ��,��Ҫ��
	* ��һ�εݹ�ʱ,�Ѹ������has_child����Ϊ1
	+-----------------------------------------------------------------------
	* @cat_id  �����ID
	+-----------------------------------------------------------------------
	* ����ֵ   �ɹ�������,ʧ�ܷ��ؼ�
	+-----------------------------------------------------------------------
	*/
	public function remove($cat_id) {
		$all_child_id = $this->category[$cat_id]['all_child_id'];
		//û�������,����Ϊ����,�������,����Ϊin
		$where = $this->category[$cat_id]['has_child'] == 0 ? "`cat_id`='{$cat_id}'" : "`cat_id` IN({$all_child_id})";
		$where .= " AND `company_uid`='{$this->company_uid}'";
		$this->query("DELETE FROM `{$this->category_table}` WHERE " . $where);
		$number = $this->affected_rows(); //����һ��Ҫд��delete������,Ҫ�ر�ע��
		if($number > 0) {
			$parent_id = $this->category[$cat_id]['parent_id'];
			$parent_id != 0 && $this->delete_all_child_id($parent_id, $all_child_id);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����б���ʾ
	+-----------------------------------------------------------------------
	* @parent_id  ����ID
	+-----------------------------------------------------------------------
	* ����ֵ      ��������
	+-----------------------------------------------------------------------
	*/
	public function list_info($parent_id = 0) {
		$where = "WHERE `parent_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'";
		$query = $this->query("SELECT `cat_id`,`type`,`model_id`,`has_child`,`cat_name`,`hits_count`,`list_order`,`content_count`,`is_nav` FROM `{$this->category_table}` $where ORDER BY `list_order` ASC,`cat_id` ASC");
		$data = array();
		while($row = $this->fetch_array($query)) {
			$row['type_name'] = get_type_name($row['type']);
			$row['model_comment'] = @$GLOBALS['QXDREAM']['MODEL'][$row['model_id']]['model_comment'];
			$data[] = $row;
		}
		$this->free_result($query);
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����
	+-----------------------------------------------------------------------
	* @id_arr     ���ύ������
	+-----------------------------------------------------------------------
	* ����ֵ      �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function list_order($id_arr) {
		if(!is_array($id_arr)) return FALSE;
		//$kΪ����ID,$vΪ����˳��
		foreach($id_arr as $k => $v) {
			$this->query("UPDATE `{$this->category_table}` SET `list_order`='{$v}' WHERE `cat_id`='{$k}' AND `company_uid`='{$this->company_uid}'");
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ��Ŀ����
	+-----------------------------------------------------------------------
	* @company_id   ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ        ĳ����˾����Ŀ����
	+-----------------------------------------------------------------------
	*/
	public function get_count() {
		return $this->result("SELECT COUNT(*) FROM `{$this->category_table}` WHERE `company_id`='{$this->company_id}'");
	}
}
?>