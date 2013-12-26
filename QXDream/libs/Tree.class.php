<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2009-10-18 ���ͽṹ�� $
	@version  $Id: Tree.class.php 1.0 2010-03-05
*/
class Tree {
	
	public $category_arr;                   //��ά����,�ɴ����ݿ���,����ID����Ҫ��һ�����ݵ����ֱ��һ��
	public $column_name_catid;              //��ǰID�ֶ���(����)
	public $column_name_parentid;           //��ǰ���ุID�ֶ���
	public $column_name_catname;            //��ǰ������ֶ���
	public $column_name_type;               //��ǰ����������ֶ���
	public $option;                         //�����б���ֵ
	public $comments;                       //���۷���ֵ
	public $icon = '|--';                   //�������ͽṹ�������η���
	
	/**
	+-----------------------------------------------------------------------
	* ���ñ���
	+-----------------------------------------------------------------------
	* @category_arr             Ҫ���ɵ�����(����)
	* @column_name_catid        ��ǰID�ֶ���(����),Ĭ��ֵcatid
	* @column_name_parentid     ��ǰ���ุID�ֶ���,Ĭ��ֵparentid
	* @column_name_catname      ��ǰ������ֶ���,Ĭ��ֵΪname
	+-----------------------------------------------------------------------
	* ����ֵ                    ��
	+-----------------------------------------------------------------------
	*/
	public function set($category_arr, $column_name_catid = 'cat_id', $column_name_parentid = 'parent_id', $column_name_catname = 'cat_name', $column_name_type = 'type') {
		$this->category_arr = $category_arr;
		$this->column_name_catid = $column_name_catid;
		$this->column_name_parentid = $column_name_parentid;
		$this->column_name_catname = $column_name_catname;
		$this->column_name_type = $column_name_type;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ��ǰ�����λ��
	+-----------------------------------------------------------------------
	* @catid              ��ǰ�����ID  
	+-----------------------------------------------------------------------
	* ����ֵ              ��������->��������->...->��ǰ������
	+-----------------------------------------------------------------------
	*/
	public function get_pos($catid, $need_arr = array()) {
		$need_arr[] = $this->category_arr[$catid];
		$pid = $this->category_arr[$catid][$this->column_name_parentid];
		if($pid != 0) return $this->get_pos($pid, $need_arr);
		//��������������
		krsort($need_arr);
		return $need_arr;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ�ӷ���
	+-----------------------------------------------------------------------
	* @parentid           ��ǰ����ĸ�����ID
	*                     ���$parentidΪ0,��ô�ǻ�ȡ�������������
	+-----------------------------------------------------------------------
	* ����ֵ              ��ǰ������ӷ�������
	+-----------------------------------------------------------------------
	*/
	public function get_child($parentid) {
		 $arr = array();
		 foreach($this->category_arr as $key => $val) {
		 	//���������ݸ�ID��Ŀ��ID��$catid���ʱ����˵������������Ŀ��ID������
		 	if($val[$this->column_name_parentid] == $parentid) $arr[$key] = $val; 
		 }
		 return $arr;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���������б�����ͽṹ
	+-----------------------------------------------------------------------
	* @parentid           ��ǰ����ĸ�����ID
	* @selectid           Ĭ�ϱ�ѡ��ķ���ID
	* @type               ���������
	* @add                ��Ϊ�¼�������ӿո�ı���
	*                     ��: һ������
	*                             ��������(������ǰ��ӿո�)
	*                     ��һ�ε��ã�$addû���κ��ַ���������ǰû�ӿո񣬵ݹ�һ�Σ�������Ż�ݹ飩��һ���ո�
	+-----------------------------------------------------------------------
	* ����ֵ              ���ɺõ����ͽṹ
	+-----------------------------------------------------------------------
	*/
	public function get_tree($parentid = 0, $limit_type = 'none', $selectid = '', $add = '') {
		$child_arr = $this->get_child($parentid);
		foreach($child_arr as $key => $val) {
			if($limit_type !== 'none' && $val[$this->column_name_type] == $limit_type) continue;
			//������ǰ���ӷ���
			$padding = $add ? $add . $this->icon : '';
			//��Ĭ�ϵ�ID�Ӹ���һ������
			$selected = $val[$this->column_name_catid] == $selectid ? 'selected="selected"' : NULL;
			$this->option .= '<option value="' . $val[$this->column_name_catid] . '" model_id="' . $val['model_id'] . '" ' . $selected . '>' . $padding . $val[$this->column_name_catname] . "</option>\n"; //����ģ�͸�������,��ѡ��ʱ���Լ�¼״̬
			//���������ݵ�ID�����¼������ݵĸ�IDȥ�ݹ�
			$this->get_tree($val[$this->column_name_catid], $limit_type, $selectid, $add .'&nbsp;&nbsp;');
		}
		return $this->option;
	}
}
?>