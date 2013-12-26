<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-14 ��̨�˵��� $
	@version  $Id: AdminMenu.class.php 1.0 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class AdminMenu {
	public $menu_data = array(); //�˵�����
	public $group_id; //�û���
	public $top_menu_data = array(); //�����˵�����
	
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
		$this->group_id = $GLOBALS['QXDREAM']['qx_group_id'];
		$this->company_id = $GLOBALS['QXDREAM']['qx_company_id'];
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ò˵�����
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function set($menu_data) {
		$this->menu_data = $menu_data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ�˵�
	+-----------------------------------------------------------------------
	* @parent_id  int     �����˵�ID
	+-----------------------------------------------------------------------
	* ����ֵ      array   �����˵�
	+-----------------------------------------------------------------------
	*/
	public function get_menu($parent_id = 0) {
		$data = array();
		foreach($this->menu_data as $k => $v) {
			if($parent_id == $v['parent_id'] && $this->check_menu_priv($v['group_ids'], $v['mr_name'])) { $data[$k] = $v; }
		}
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ȷ�ϲ˵�Ȩ����ʾ
	+-----------------------------------------------------------------------
	* @group_ids  string       ��ʹ�õ��û���ID
	* @mr_name    string       ģ����Դ����
	+-----------------------------------------------------------------------
	* ����ֵ      boolen       ����ʾ�����棬���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	private function check_menu_priv($group_ids, $mr_name) {
		$group_id_data = explode(',', $group_ids);
		 //����
		if(1 == $this->group_id && in_array($this->group_id, $group_id_data)) { return TRUE; }
		if(2 == $this->group_id) { //��˾����Ա
			if(in_array($this->group_id, $group_id_data) && 
			   (empty($mr_name) || !empty($mr_name) && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], explode(',', $GLOBALS['QXDREAM']['qx_mr_ids'])))) { 
				return TRUE; 
			}
		}
		//��˾������ɫ
		if($this->group_id > 2) {
			//��99�ļ���mr_nameΪ�յ���ʾ
			//mr_name��Ϊ�յ�Ҫ�����ж�Ȩ��
			if((in_array('99', $group_id_data) && empty($mr_name)) ||
			    !empty($mr_name) && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], $this->get_available_mr_id())) { 
				return TRUE; 
			}
		}	
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ�ù�˾���õ�ģ����Դ(��˾����Ա֮��Ľ�ɫʹ��)
	+-----------------------------------------------------------------------
	* ��
	+-----------------------------------------------------------------------
	* ����ֵ      array       ����ģ����Դ����
	+-----------------------------------------------------------------------
	*/
	private function get_available_mr_id() {
		return array_intersect(explode(',', $GLOBALS['QXDREAM']['USER_GROUP'][$this->group_id]['mr_ids']), explode(',', $GLOBALS['QXDREAM']['qx_mr_ids']));
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��Ŀ�˵�Ȩ����ʾ
	+-----------------------------------------------------------------------
	* @model_id   int          ģ��ID
	+-----------------------------------------------------------------------
	* ����ֵ      boolen       ����ʾ�����棬���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	private function check_cat_menu_priv($model_id) {
		if(1 == $this->group_id) { return FALSE; }
		$mr_name = $GLOBALS['QXDREAM']['MODEL'][$model_id]['model_name'];
		if(2 == $this->group_id && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], explode(',', $GLOBALS['QXDREAM']['qx_mr_ids'])) || 
		   $this->group_id > 2 && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], $this->get_available_mr_id())) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ö����˵�(ͬһҳ������ʾ�������Ӽ�����Ϊ�Ż�����get_menu()��get_sidebar_menu()֮��ʹ��
	+-----------------------------------------------------------------------
	* @top_menu_data  array   �����˵�����
	+-----------------------------------------------------------------------
	* ����ֵ                   ��
	+-----------------------------------------------------------------------
	*/
	public function set_top_menu($top_menu_data) {
		$this->top_menu_data = $top_menu_data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ������˵�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   array   ������˵�HTML
	+-----------------------------------------------------------------------
	*/
	public function get_sidebar_menu() {
		$menu = '';
		foreach($this->top_menu_data as $top_k => $top_v) {
			$bmc_cur = 1 == $top_k ? ' bmc_cur' : '';
			$menu .= '<div class="block_menu_content' . $bmc_cur . '">' . "\r\n";
			$sidebar_menu_data = $this->get_menu($top_k);
			$menu .= $this->get_block_menu($sidebar_menu_data) . '</div>' . "\r\n";
		}
		return $menu;
	}	
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ���һ����״̬�˵�
	+-----------------------------------------------------------------------
	* @top_menu   array  �������һ���˵�
	+-----------------------------------------------------------------------
	* ����ֵ      array   �˵�HTML
	+-----------------------------------------------------------------------
	*/
	private function get_block_menu($top_menu) {
		$menu = '';
		$top_h3_class = $top_span_class = $sub_ul_class = ''; //html��ǩ���classѡ����
		$i = 0;
		$top_menu_total = count($top_menu);
		foreach($top_menu as $top_k => $top_v) {
			if($this->check_menu_priv($top_v['group_ids'], $top_v['mr_name'])) {
				$i++;
				$sub_menu = $this->get_menu($top_k); //��ȡ�Ӳ˵�
				$sub_menu_total = count($sub_menu);
				if($sub_menu_total > 0 || !empty($top_v['tree_sign'])) { //�����Ӳ˵�ʱ�����˵�classѡ����
					if($top_v['collapse'] == 1) { //Ĭ������£��
						$top_span_class  = ' drop_control_options_show sprites';
						$sub_ul_class = $top_menu_total == $i ? ' sub_menu_last collapse' : ' collapse';
					} else {
						$top_span_class = ' drop_control sprites';
						$sub_ul_class = $top_menu_total == $i ? ' sub_menu_last' : '';
					}
				} else {
					$top_h3_class = ' top_menu_no_options'; //û���Ӳ˵�ʱ
					$top_span_class = '';
				}
				
				if(empty($top_v['url']) && $sub_menu_total > 0) { //����һ���˵�������ʱ�����Ӳ˵��ĵ�һ����Ϊ������
					$first_menu_data = array_slice($sub_menu,0, 1);
					$top_url = str_replace('{qx_user_id}', $GLOBALS['QXDREAM']['qx_user_id'], $first_menu_data[0]['url']);
				} else {
					$top_url = $top_v['url'];
				}
				//��ģ�鲻�Ǻ���ʱ�����е������ļ��У�app_url()�Ĳ���Ӧ����mr_name
				$menu .= "\t" . '<h3 class="top_menu' . $top_h3_class .'"><span class="f_r' . $top_span_class . '"></span><a class="nor_a" href="' . app_url((isset($GLOBALS['QXDREAM']['MR'][$top_v['mr_name']]) && 1 == $GLOBALS['QXDREAM']['MR'][$top_v['mr_name']]['is_core'] ? '' : $top_v['mr_name'])) . $top_url . '" target="' . $top_v['target'] . '">' . $top_v['name'] . "</a></h3>\r\n";
				if($sub_menu_total > 0) { //���Ӳ˵�ʱ
					$menu .= "\t" . '<ul class="sub_menu' . $sub_ul_class . '">' . "\r\n";
					foreach($this->menu_data as $sub_k => $sub_v) {
						if($sub_v['parent_id'] == $top_k && $this->check_menu_priv($sub_v['group_ids'], $sub_v['mr_name'])) {
							$menu .= "\t\t" . '<li><a class="nor_a" href="' . app_url((isset($GLOBALS['QXDREAM']['MR'][$sub_v['mr_name']]) && 1 == $GLOBALS['QXDREAM']['MR'][$sub_v['mr_name']]['is_core'] ? '' : $sub_v['mr_name'])) . str_replace('{qx_user_id}', $GLOBALS['QXDREAM']['qx_user_id'], $sub_v['url']) . '" target="' . $sub_v['target'] . '">' . $sub_v['name'] . "</a></li>\r\n";
						}
					}
					$menu .= "\t" . '</ul>' . "\r\n";
				} elseif(isset($top_v['tree_sign']) && !empty($top_v['tree_sign'])) { //�����Ͳ˵�ʱ
					$menu .= "\t" . '<div class="sub_menu' . $sub_ul_class . '"><div class="' . $top_v['tree_sign'] . '" id="' . $top_v['tree_sign'] . $top_k . '"></div></div>' . "\r\n" . $this->get_tree_js($top_v['tree_sign'] . $top_k);
				}
				
			}
		}
		return $menu;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������Ͳ˵�JS
	+-----------------------------------------------------------------------
	* @html_tree_id  string  html��������ID
	+-----------------------------------------------------------------------
	* ����ֵ         array   JS����
	+-----------------------------------------------------------------------
	*/
	private function get_tree_js($html_tree_id) {
		$menu = '<script type="text/javascript">
				var treeData = new Array();';
		foreach($GLOBALS['QXDREAM']['CATEGORY'] as $k => $v) {
			if($this->check_cat_menu_priv($v['model_id'])) {
				$menu .= 'treeData.push({id:' . $v['cat_id'] . ', pid:' . $v['parent_id'] . ', menuName:"' . $v['cat_name'] . '", url:"' . app_url() . 'content/' . (3 == $v['model_id'] ? 'edit' : 'index') . '/model_id/' . $v['model_id'] . '/cat_id/' . $v['cat_id'] . '/", target:"right", isOpen:true});';
			}
		}
		$menu .= 'var ' . $html_tree_id . ' = new qxdTree("' . $html_tree_id . '", treeData, "' . $html_tree_id . '");
		</script>';
		return $menu;
	}
}
?>