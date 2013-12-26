<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-30 模型 $
	@version  $Id: Model.class.php 1.1 2011-05-01
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Model extends Mysql {
	
	public $pagenation;  //分页对象
	public $msg;         //消息提示
	
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
		parent::__construct();
		//公司缓存不存在，缓存一下数据
		if(method_exists($this, '_initialize')) {
			$this->_initialize();
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 计算当前记录总数
	+-----------------------------------------------------------------------
	* @sql     string  sql语句,如SELECT COUNT(*) FROM `table`
	+-----------------------------------------------------------------------
	* 返回值   不能页时返回假，否则返回记录总数
	+-----------------------------------------------------------------------
	*/
	public function count($sql) {
		return empty($this->pagenation->page_size) ? FALSE : $this->pagenation->row_total = $this->result($sql);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 计算数据库大小
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   数据库的大小(带有单位)
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
	* 缓存数据表
	+-----------------------------------------------------------------------
	* @table       string  数据表的名称
	* @append      string  缓存文件附加名
	* @fields      string  字段,默认为所有
	* @order_field string  排序的字段名称
	* @where       string  查询条件
	* @is_line     boolen  是否每条记录缓存为一个文件
	* @number      int     查询出多少条
	+-----------------------------------------------------------------------
	* 返回值       无
	+-----------------------------------------------------------------------
	*/
	public function cache_table($table, $append = '', $fields = '*', $order_field = '', $asc_desc = 'ASC', $where = '', $is_line = 0, $number = 0) {
		//把表的前缀替换掉,生成文件名为不带前缀的数据表名称
		$arr = array();
		if(preg_match("/^" . DB_PRE ."(.*)$/", $table, $arr)) {
			$remove_pre_table = $arr[1];
			unset($arr);
		} else { //不带数据表前缀直接返回表名
			$remove_pre_table = $table;
			$table = DB_PRE . $table;
		}
		//获取主键字段名
		$primary_key = $this->get_primary($table);
		//缓存的数组
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
			//一条记录缓存为一个文件,命名:表名+主键名
			if(!empty($is_line)) cache_write($append . $remove_pre_table . '_' . $data[$primary_key], $data);
		}
		cache_write($append . $remove_pre_table, $cache_data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 缓存模组资源数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
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
	* 缓存公司数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
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
	* 缓存会员权限组数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function cache_user_group($company_id) {
		if(0 == $company_id) { //是超管时
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
	* 缓存模型
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function cache_model() {
		$this->cache_table('model');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 缓存模型中的字段
	+-----------------------------------------------------------------------
	* @model_id    int   模型ID(留空时缓存所有)
	+-----------------------------------------------------------------------
	* 返回值             无
	+-----------------------------------------------------------------------
	*/
	public function cache_model_field($model_id = '') {
		if(!empty($model_id)) {
			$this->cache_table('model_field', $model_id, '*', 'list_order', 'ASC', "model_id='" . $model_id . "'");
		} else {
			//定要按model_id先排好
			$query = $this->query("SELECT * FROM " . DB_PRE . "model_field ORDER BY model_id ASC,list_order ASC,field_id ASC", 'unbuffered');
			$cache_mf_data = array();
			$temp_model_id = -1; //临时模型ID变量
			$i = 0; //索引计数器
			while($row = $this->fetch_array($query)) {
				if(-1 != $temp_model_id && $temp_model_id != $row['model_id']) { //开始下一个索引时
					cache_write($temp_model_id . '_model_field', $cache_mf_data[$i]);
					$cache_mf_data[++$i][] = $row;
				} else {
					$cache_mf_data[$i][] = $row;
				}
				$temp_model_id = $row['model_id'];
			}
			if(-1 != $temp_model_id) { //有数据才缓存
				cache_write($temp_model_id . '_model_field', $cache_mf_data[$i]);
			}
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 缓存某公司分类
	+-----------------------------------------------------------------------
	* @company_id   int  公司ID
	+-----------------------------------------------------------------------
	* 返回值             无
	+-----------------------------------------------------------------------
	*/
	public function cache_category($company_id) {
		$this->cache_table('category', $company_id, '*', 'list_order,cat_id', 'ASC', "company_id='" . $company_id . "'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 缓存单页id与content_id值
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
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
	* 缓存所有数组
	+-----------------------------------------------------------------------
	* @company_id  int      公司ID,默认-100，没传参数，调用
	* @read        boolen   是否读取缓存
	+-----------------------------------------------------------------------
	* 返回值                无
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
		
		if(0 == $company_id) { //超级管理员
			$this->cache_model();
			$this->cache_model_field();
		} else { //公司账户
			$this->cache_category($company_id);
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除某个公司缓存
	+-----------------------------------------------------------------------
	* @company_id  int    公司ID
	+-----------------------------------------------------------------------
	* 返回值              无
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_company($company_id) {
		cache_delete($company_id . '_user_group');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除某个模型字段缓存
	+-----------------------------------------------------------------------
	* @model_id    int   模型ID
	+-----------------------------------------------------------------------
	* 返回值             无
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_model_field($model_id) {
		cache_delete($model_id . '_model_field');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除某公司分类缓存
	+-----------------------------------------------------------------------
	* @company_id    int  模型ID
	+-----------------------------------------------------------------------
	* 返回值              无
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_category($company_id) {
		cache_delete($company_id . '_category');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除某公司单页缓存
	+-----------------------------------------------------------------------
	* @company_id    int  模型ID
	+-----------------------------------------------------------------------
	* 返回值              无
	+-----------------------------------------------------------------------
	*/
	public function cache_delete_page($company_id) {
		cache_delete($company_id . '_page');
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取数据表的setting字段内容
	+-----------------------------------------------------------------------
	* @table    string    数据表名
	* @where    string    查询条件
	+-----------------------------------------------------------------------
	* 返回值    array     配置的数组
	+-----------------------------------------------------------------------
	*/
	public function get_setting($table, $where) {
		$data = $this->fetch("SELECT `setting` FROM `" . $table . "` WHERE " . $where);
		//去除转义字符
		$setting = $data['setting'];
		if(!empty($setting)) eval("\$setting = $setting;");
		return $setting;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取数据表的setting字段内容
	+-----------------------------------------------------------------------
	* @table          string  数据表名
	* @setting_arr    string  查询条件
	* @where          string  更新条件
	+-----------------------------------------------------------------------
	* 返回值          boolen  成功返回真,失几返回假
	+-----------------------------------------------------------------------
	*/
	public function set_setting($table, $setting_arr, $where) {
		if(!is_array($setting_arr)) return FALSE;
		$setting_arr = unslash($setting_arr); //先去转义,因为插入时var_export会对单引号转义
		//addslashes对其转义(插入数据时数据库会去掉转义,这层抵消)
		$setting = addslashes(var_export($setting_arr, TRUE));
		return $this->query("UPDATE `". $table . "` SET `setting`='" . $setting . "' WHERE " . $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 语言
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function msg($is_admin = 0) {
		return $GLOBALS['QXDREAM'][(0 == $is_admin ? 'language' : 'admin_language')][$this->msg];
	}
}
?>