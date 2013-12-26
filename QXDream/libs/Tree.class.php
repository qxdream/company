<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2009-10-18 树型结构类 $
	@version  $Id: Tree.class.php 1.0 2010-03-05
*/
class Tree {
	
	public $category_arr;                   //二维数组,可从数据库查出,主键ID数字要和一行数据的数字编号一致
	public $column_name_catid;              //当前ID字段名(主键)
	public $column_name_parentid;           //当前分类父ID字段名
	public $column_name_catname;            //当前分类的字段名
	public $column_name_type;               //当前分类的类型字段名
	public $option;                         //下拉列表返回值
	public $comments;                       //评论返回值
	public $icon = '|--';                   //生成树型结构所需修饰符号
	
	/**
	+-----------------------------------------------------------------------
	* 设置变量
	+-----------------------------------------------------------------------
	* @category_arr             要生成的数组(二级)
	* @column_name_catid        当前ID字段名(主键),默认值catid
	* @column_name_parentid     当前分类父ID字段名,默认值parentid
	* @column_name_catname      当前分类的字段名,默认值为name
	+-----------------------------------------------------------------------
	* 返回值                    无
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
	* 获取当前分类的位置
	+-----------------------------------------------------------------------
	* @catid              当前分类的ID  
	+-----------------------------------------------------------------------
	* 返回值              父类数组->子类数组->...->当前类数组
	+-----------------------------------------------------------------------
	*/
	public function get_pos($catid, $need_arr = array()) {
		$need_arr[] = $this->category_arr[$catid];
		$pid = $this->category_arr[$catid][$this->column_name_parentid];
		if($pid != 0) return $this->get_pos($pid, $need_arr);
		//把数组逆向排列
		krsort($need_arr);
		return $need_arr;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取子分类
	+-----------------------------------------------------------------------
	* @parentid           当前分类的父分类ID
	*                     如果$parentid为0,那么是获取顶级分类的子类
	+-----------------------------------------------------------------------
	* 返回值              当前分类的子分类数组
	+-----------------------------------------------------------------------
	*/
	public function get_child($parentid) {
		 $arr = array();
		 foreach($this->category_arr as $key => $val) {
		 	//当该条数据父ID和目标ID即$catid相等时，就说明这条数据是目标ID的子类
		 	if($val[$this->column_name_parentid] == $parentid) $arr[$key] = $val; 
		 }
		 return $arr;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 生成下拉列表的树型结构
	+-----------------------------------------------------------------------
	* @parentid           当前分类的父分类ID
	* @selectid           默认被选择的分类ID
	* @type               分类的类型
	* @add                作为下级分类叠加空格的变量
	*                     如: 一级分类
	*                             二级分类(作用在前面加空格)
	*                     第一次调用，$add没有任何字符，顶级类前没加空格，递归一次（有子类才会递归）加一个空格
	+-----------------------------------------------------------------------
	* 返回值              生成好的树型结构
	+-----------------------------------------------------------------------
	*/
	public function get_tree($parentid = 0, $limit_type = 'none', $selectid = '', $add = '') {
		$child_arr = $this->get_child($parentid);
		foreach($child_arr as $key => $val) {
			if($limit_type !== 'none' && $val[$this->column_name_type] == $limit_type) continue;
			//顶级类前不加符号
			$padding = $add ? $add . $this->icon : '';
			//给默认的ID加赋给一个变量
			$selected = $val[$this->column_name_catid] == $selectid ? 'selected="selected"' : NULL;
			$this->option .= '<option value="' . $val[$this->column_name_catid] . '" model_id="' . $val['model_id'] . '" ' . $selected . '>' . $padding . $val[$this->column_name_catname] . "</option>\n"; //加了模型个性内容,在选择时可以记录状态
			//把这条数据的ID当作下几条数据的父ID去递归
			$this->get_tree($val[$this->column_name_catid], $limit_type, $selectid, $add .'&nbsp;&nbsp;');
		}
		return $this->option;
	}
}
?>