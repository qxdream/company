<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-05-14 后台菜单类 $
	@version  $Id: AdminMenu.class.php 1.0 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class AdminMenu {
	public $menu_data = array(); //菜单数据
	public $group_id; //用户组
	public $top_menu_data = array(); //顶级菜单数组
	
	/**
	+-----------------------------------------------------------------------
	* 初始化
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function __construct() {
		$this->group_id = $GLOBALS['QXDREAM']['qx_group_id'];
		$this->company_id = $GLOBALS['QXDREAM']['qx_company_id'];
	}
	
	/**
	+-----------------------------------------------------------------------
	* 设置菜单数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function set($menu_data) {
		$this->menu_data = $menu_data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取菜单
	+-----------------------------------------------------------------------
	* @parent_id  int     父级菜单ID
	+-----------------------------------------------------------------------
	* 返回值      array   顶级菜单
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
	* 确认菜单权限显示
	+-----------------------------------------------------------------------
	* @group_ids  string       可使用的用户组ID
	* @mr_name    string       模组资源名称
	+-----------------------------------------------------------------------
	* 返回值      boolen       可显示返回真，否则返回假
	+-----------------------------------------------------------------------
	*/
	private function check_menu_priv($group_ids, $mr_name) {
		$group_id_data = explode(',', $group_ids);
		 //超管
		if(1 == $this->group_id && in_array($this->group_id, $group_id_data)) { return TRUE; }
		if(2 == $this->group_id) { //公司管理员
			if(in_array($this->group_id, $group_id_data) && 
			   (empty($mr_name) || !empty($mr_name) && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], explode(',', $GLOBALS['QXDREAM']['qx_mr_ids'])))) { 
				return TRUE; 
			}
		}
		//公司其他角色
		if($this->group_id > 2) {
			//有99的加上mr_name为空的显示
			//mr_name不为空的要额外判断权限
			if((in_array('99', $group_id_data) && empty($mr_name)) ||
			    !empty($mr_name) && in_array($GLOBALS['QXDREAM']['MR'][$mr_name]['mr_id'], $this->get_available_mr_id())) { 
				return TRUE; 
			}
		}	
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取该公司有用的模组资源(公司管理员之外的角色使用)
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      array       可用模组资源数组
	+-----------------------------------------------------------------------
	*/
	private function get_available_mr_id() {
		return array_intersect(explode(',', $GLOBALS['QXDREAM']['USER_GROUP'][$this->group_id]['mr_ids']), explode(',', $GLOBALS['QXDREAM']['qx_mr_ids']));
	}
	
	/**
	+-----------------------------------------------------------------------
	* 栏目菜单权限显示
	+-----------------------------------------------------------------------
	* @model_id   int          模型ID
	+-----------------------------------------------------------------------
	* 返回值      boolen       可显示返回真，否则返回假
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
	* 设置顶级菜单(同一页面又显示顶级与子级，此为优化，在get_menu()与get_sidebar_menu()之间使用
	+-----------------------------------------------------------------------
	* @top_menu_data  array   顶级菜单数组
	+-----------------------------------------------------------------------
	* 返回值                   无
	+-----------------------------------------------------------------------
	*/
	public function set_top_menu($top_menu_data) {
		$this->top_menu_data = $top_menu_data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取侧边栏菜单
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   array   侧边栏菜单HTML
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
	* 获取侧边一个块状态菜单
	+-----------------------------------------------------------------------
	* @top_menu   array  侧边栏第一级菜单
	+-----------------------------------------------------------------------
	* 返回值      array   菜单HTML
	+-----------------------------------------------------------------------
	*/
	private function get_block_menu($top_menu) {
		$menu = '';
		$top_h3_class = $top_span_class = $sub_ul_class = ''; //html标签里的class选择器
		$i = 0;
		$top_menu_total = count($top_menu);
		foreach($top_menu as $top_k => $top_v) {
			if($this->check_menu_priv($top_v['group_ids'], $top_v['mr_name'])) {
				$i++;
				$sub_menu = $this->get_menu($top_k); //获取子菜单
				$sub_menu_total = count($sub_menu);
				if($sub_menu_total > 0 || !empty($top_v['tree_sign'])) { //当有子菜单时顶级菜单class选择器
					if($top_v['collapse'] == 1) { //默认是收拢的
						$top_span_class  = ' drop_control_options_show sprites';
						$sub_ul_class = $top_menu_total == $i ? ' sub_menu_last collapse' : ' collapse';
					} else {
						$top_span_class = ' drop_control sprites';
						$sub_ul_class = $top_menu_total == $i ? ' sub_menu_last' : '';
					}
				} else {
					$top_h3_class = ' top_menu_no_options'; //没有子菜单时
					$top_span_class = '';
				}
				
				if(empty($top_v['url']) && $sub_menu_total > 0) { //左侧第一级菜单无链接时，把子菜单的第一个作为此链接
					$first_menu_data = array_slice($sub_menu,0, 1);
					$top_url = str_replace('{qx_user_id}', $GLOBALS['QXDREAM']['qx_user_id'], $first_menu_data[0]['url']);
				} else {
					$top_url = $top_v['url'];
				}
				//当模组不是核心时，会有单独的文件夹，app_url()的参数应传该mr_name
				$menu .= "\t" . '<h3 class="top_menu' . $top_h3_class .'"><span class="f_r' . $top_span_class . '"></span><a class="nor_a" href="' . app_url((isset($GLOBALS['QXDREAM']['MR'][$top_v['mr_name']]) && 1 == $GLOBALS['QXDREAM']['MR'][$top_v['mr_name']]['is_core'] ? '' : $top_v['mr_name'])) . $top_url . '" target="' . $top_v['target'] . '">' . $top_v['name'] . "</a></h3>\r\n";
				if($sub_menu_total > 0) { //有子菜单时
					$menu .= "\t" . '<ul class="sub_menu' . $sub_ul_class . '">' . "\r\n";
					foreach($this->menu_data as $sub_k => $sub_v) {
						if($sub_v['parent_id'] == $top_k && $this->check_menu_priv($sub_v['group_ids'], $sub_v['mr_name'])) {
							$menu .= "\t\t" . '<li><a class="nor_a" href="' . app_url((isset($GLOBALS['QXDREAM']['MR'][$sub_v['mr_name']]) && 1 == $GLOBALS['QXDREAM']['MR'][$sub_v['mr_name']]['is_core'] ? '' : $sub_v['mr_name'])) . str_replace('{qx_user_id}', $GLOBALS['QXDREAM']['qx_user_id'], $sub_v['url']) . '" target="' . $sub_v['target'] . '">' . $sub_v['name'] . "</a></li>\r\n";
						}
					}
					$menu .= "\t" . '</ul>' . "\r\n";
				} elseif(isset($top_v['tree_sign']) && !empty($top_v['tree_sign'])) { //有树型菜单时
					$menu .= "\t" . '<div class="sub_menu' . $sub_ul_class . '"><div class="' . $top_v['tree_sign'] . '" id="' . $top_v['tree_sign'] . $top_k . '"></div></div>' . "\r\n" . $this->get_tree_js($top_v['tree_sign'] . $top_k);
				}
				
			}
		}
		return $menu;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 生成树型菜单JS
	+-----------------------------------------------------------------------
	* @html_tree_id  string  html树型容器ID
	+-----------------------------------------------------------------------
	* 返回值         array   JS代码
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