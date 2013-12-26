<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Muticategory
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-26 后台分类控制器 $
	@version  $Id: CategoryAction.class.php 1.0 2011-05-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CategoryAction extends ShareAction {	
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
		parent::_initialize();
		$this->category = $this->load_model('category');
		$this->category->set($GLOBALS['QXDREAM']['qx_company_uid']);
		$this->tree = load_class('tree');
		$this->tree->set($GLOBALS['QXDREAM']['CATEGORY']);
		$this->content = $this->load_model('content');
		$this->content->set($GLOBALS['QXDREAM']['qx_company_id']);
		$this->content->set_model('page'); //控制单页的
	}
	
	/**
	+-----------------------------------------------------------------------
	* 显示分类列表
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		$_GET['parent_id'] = isset($_GET['parent_id']) ? nature_val($_GET['parent_id']) : 0;
		$this->category->pagenation = new Pagenation();
		$this->category->pagenation->list_init();
		$category_data = $this->category->list_info($_GET['parent_id']);
		$page_nav = $this->category->pagenation->page_normal();
		
		$cat_pos = '';
		if($_GET['parent_id'] > 0) {
			$cat_pos = $this->tree->get_pos($_GET['parent_id']);
			$arr = array('<a href="' . app_url() . 'category/' . '">' . $GLOBALS['QXDREAM']['admin_language']['all_category'] . '</a>');
			foreach($cat_pos as $k => $v) {
				$arr[] = '<a href="' . app_url() . 'category/index/parent_id/' . $v['cat_id'] . '/">' . $v['cat_name'] . '</a>';
			}
			$cat_pos = implode(' &gt ', $arr);
			unset($arr);
		}
		
		$this->view->assign('page_title', $GLOBALS['QXDREAM']['admin_language']['category_list']);
		$this->view->assign('page_nav', $page_nav);
		$this->view->assign('cat_pos', $cat_pos);
		$this->view->assign('category_data', $category_data);
		$this->view->display();
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加新分类
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function add() {
		$this->category->get_category();
		if(!isset($_POST['step']) || $_POST['step'] == 1) {
			$page_title = $GLOBALS['QXDREAM']['admin_language']['select_category_type'];
			$parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : '';
			$option_str = $this->tree->get_tree(0, 2, $parent_id); //select下拉选择里的option,除链接
			$this->view->assign('page_title', $page_title);
			$this->view->assign('option_str', $option_str);
			$this->view->display('category_add_first');
		} elseif($_POST['step'] == 2) {
			$page_title = $GLOBALS['QXDREAM']['admin_language']['category_add'];
			$this->view->assign('page_title', $page_title);
			switch($_POST['type']) {
				case 0:
					$this->view->display('category_add');
				break;
				case 1: //单页
					//获取单页模板
					$this->view->assign('page_template_data', get_template('page'));
					$this->view->display('category_page_add');
				break;
				case 2: //外链
					$this->view->display('category_link_add');
				break;
			}
		} elseif($_POST['step'] == 'ok') {
			$cat_name = trim($_POST['category']['cat_name']);
			empty($cat_name) && show_msg('filled_out');
			unset($cat_name);
			if(isset($_POST['category']['url'])) {
				$url = trim($_POST['category']['url']);
				empty($url) && show_msg('filled_out');
				unset($url);
			}
			$category = $_POST['category'];
			check_fields($category, array('cat_id', 'type', 'company_id', 'company_uid', 'model_id', 'parent_id', 'cat_name', 'list_order', 'is_nav', 'url','setting', 'template'));
			if(!isset($category['is_nav'])) $category['is_nav'] = 0; //复选框的
			$category_setting = isset($_POST['category_setting']) ? $_POST['category_setting'] : '';
			if(($last_cat_id = $this->category->add($category, $category_setting))) {
				$this->category->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
				if(3 == $category['model_id']) { //添加单页内容
					$this->content->add(array(
						'company_id'  => $GLOBALS['QXDREAM']['qx_company_id'],
						'company_uid' => $GLOBALS['QXDREAM']['qx_company_uid'],
						'model_id'    => 3,
						'cat_id'      => $last_cat_id,
						'user_id'     => $GLOBALS['QXDREAM']['qx_company_id'],
						'status'      => 1,
						'post_time'   => $GLOBALS['QXDREAM']['timestamp'],
						'update_time' => $GLOBALS['QXDREAM']['timestamp']
					), array(
						'cat_id'      => $last_cat_id,
						'content'     => '',
						'company_id'  => $GLOBALS['QXDREAM']['qx_company_id'],
						'company_uid' => $GLOBALS['QXDREAM']['qx_company_uid'],
					));
					$this->content->cache_page_id($GLOBALS['QXDREAM']['qx_company_id']);
				}
				show_msg('operation_success', app_url() . 'category/add/parent_id/' . $_POST['category']['parent_id'], 0, 1500, FALSE, $this->update_category_menu());
			} else {
				show_msg($this->category->msg(1));
			}	
		}
	}
	
	
	/**
	+-----------------------------------------------------------------------
	* 编辑分类
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function edit() {
		$cat_data = $this->check_record();
		$this->category->get_category();
		$model_para = isset($_GET['model_id']) ? '/model_id/' . intval($_GET['model_id']) : '';
		add_globals(array('content_count' => $cat_data['content_count']));
		if(isset($_POST['btn_submit'])) {
			$cat_name = trim($_POST['category']['cat_name']);
			empty($cat_name) && show_msg('filled_out',app_url() . 'category/edit/cat_id/' . $cat_id . $model_para);
			unset($cat_name);
			$category = $_POST['category'];
			check_fields($category, array('type', 'model_id', 'parent_id', 'cat_name', 'list_order', 'is_nav', 'url','setting', 'template'));
			if(!isset($category['is_nav'])) $category['is_nav'] = 0; //复选框的
			//该类下文章数据大于0,就不让model字段更新
			if($cat_data['content_count'] > 0) unset($category['model_id']);
			$category_setting = isset($_POST['category_setting']) ? $_POST['category_setting'] : '';
			if($this->category->edit($cat_data['cat_id'], $category, $category_setting)) {
				$this->category->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
				show_msg('operation_success', app_url() . 'category/edit/cat_id/' . $cat_data['cat_id'] . $model_para, 0, 1500, FALSE, $this->update_category_menu());
			} else {
				show_msg($this->category->msg(), app_url() . 'category/edit/cat_id/' . $cat_data['cat_id'] . $model_para);
			}	
		}
		if(isset($cat_data['setting']) && !empty($cat_data['setting'])) {
			$setting = $cat_data['setting'];
			eval("\$setting = $setting;");
			$cat_data = array_merge($cat_data, $setting);
			unset($setting, $cat_data['setting']);
		}
		$page_title = $GLOBALS['QXDREAM']['admin_language']['category_edit'];
		$option_str = $this->tree->get_tree(0, 2, $cat_data['parent_id']); //select下拉选择里的option,除链接
		$cat_data = my_htmlspecialchars($cat_data);
		$this->view->assign('page_title', $page_title);
		$this->view->assign('option_str', $option_str);
		$this->view->assign('cat_data', $cat_data);
		//根据类型载入相应的模板
		switch($cat_data['type']) {
			case 0:
				$this->view->display('category_edit');
			break;
			case 1: //单页
				$this->view->assign('page_template_data', get_template('page'));
				$this->view->display('category_page_edit');
			break;
			case 2: //外链
				$this->view->display('category_link_edit');
			break;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 修改分类时更新菜单
	+-----------------------------------------------------------------------
	* @num     int    树型ID
	+-----------------------------------------------------------------------
	* 返回值   更新菜单的JS代码
	+-----------------------------------------------------------------------
	*/
	private function update_category_menu($num = 15) {
		$category = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_category');
		$str = "<script type='text/javascript' src='" . QX_PATH . PUBLIC_DIR . "js/common.js'></script>
		<script type='text/javascript' src='" . QX_PATH . PUBLIC_DIR . "js/qxd_tree.js'></script>
		<script type='text/javascript'>
		if(parent.document.getElementById('qxd_tree" . $num . "')) {
		var treeData = new Array();";
		foreach($category as $k => $v) {
			$str .= "treeData.push({id:" . $v['cat_id'] .", pid:" . $v['parent_id'] .", menuName:'" . $v['cat_name'] . "', url:'" . app_url() . 'content/' . (3 == $v['model_id'] ? 'edit' : 'index') . '/model_id/' . $v['model_id'] . '/cat_id/' . $v['cat_id'] . "/', target:'right'});";
		}
		$str .= "var qxd_tree" . $num . " = new qxdTree('qxd_tree" . $num . "', treeData, 'qxd_tree" . $num . "', {updateParent:true});
		parent.getId('qxd_tree" . $num . "').innerHTML = qxd_tree" . $num . ".treeStr;
		}
		</script>";
		return $str;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除分类
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function delete() {
		$category_data = $this->check_record();
		$this->category->get_category();
		$this->category->remove($_GET['cat_id']);
		$this->category->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
		if(3 == $category_data['model_id']) { //删除单页内容
			$this->content->remove_page($_GET['cat_id']);
			$this->content->cache_page_id($GLOBALS['QXDREAM']['qx_company_id']);
		}
		show_msg('operation_success', HTTP_REFERER, 0, 1500, FALSE, $this->update_category_menu());
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量删除分类
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function batch_delete() {
		if(!isset($_POST['cat_id']) || !is_array($_POST['cat_id'])) { show_msg('one_list_need'); }
		$_POST['cat_id'] = array_map('intval', $_POST['cat_id']);
		$cache_page = FALSE;
		foreach($_POST['cat_id'] as $k => $v) {
			$category_data = $this->category->get($v);
			!is_array($category_data) && show_msg('data_not_exists');
			$this->category->get_category();
			$this->category->remove($v);
			if(3 == $category_data['model_id']) { //删除单页内容
				$this->content->remove_page($v);
				$cache_page = TRUE;
			}
		}
		$cache_page && $this->content->cache_page_id($GLOBALS['QXDREAM']['qx_company_id']);
		$this->category->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
		show_msg('operation_success', HTTP_REFERER, 0, 1500, FALSE, $this->update_category_menu());
	}
	
	/**
	+-----------------------------------------------------------------------
	* 排序
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      无
	+-----------------------------------------------------------------------
	*/
	public function batch_list_order() {
		if(!isset($_POST['list_order'])) show_msg('one_list_need');
		$_POST['list_order'] = array_map('intval', $_POST['list_order']);
		$this->category->list_order($_POST['list_order']);
		$this->category->cache_category($GLOBALS['QXDREAM']['qx_company_id']);
		$this->update_category_menu();
		show_msg('operation_success', HTTP_REFERER, 0, 1500, FALSE, $this->update_category_menu());
	}
	
	/**
	+-----------------------------------------------------------------------
	* 记录是否存在
	+-----------------------------------------------------------------------
	* 无
	+-----------------------------------------------------------------------
	* 返回值      存在返回数组
	+-----------------------------------------------------------------------
	*/
	private function check_record() {
		$_GET['cat_id'] = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
		if(!empty($_GET['cat_id'])) {
			$data = $this->category->get($_GET['cat_id']);
			!is_array($data) && show_msg('data_not_exists'); 
			return $data;
		} else {
			show_msg('invalid_request');
		}
	}
}
?>