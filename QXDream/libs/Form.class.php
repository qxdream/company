<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-30 字段表单类型类 $
	@version  $Id: Form.class.php 1.1 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Form {

	/**
	+-----------------------------------------------------------------------
	* 单行文本
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值          string  文本html
	+-----------------------------------------------------------------------
	*/
	public function text($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" class="text_box" size="20" style="width: 300px;"' . $default . ' />';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 多行文本
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值       string  多行文本html
	+-----------------------------------------------------------------------
	*/
	public function textarea($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : $default;
		$name = $is_system ? 'content' : 'content_detail';
		return '<textarea name="' . $name . '[' . $field_name . ']" rows="3" cols="50" style="width: 93%; height: 50px;">' . $default . '</textarea>';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 编辑器
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值       string  编辑器html
	+-----------------------------------------------------------------------
	*/
	public function editor($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : $default;
		$name = $is_system ? 'content' : 'content_detail';
		return '<textarea name="' . $name . '[' . $field_name . ']" id="' . $field_name . '" cols="60" rows="4">' . $default . '</textarea>' . editor($field_name, 'Default', '94%', '500') . '<p style="margin-top: 8px;"><input type="button" id="' . $field_name . '_upload" editor_id="' . $field_name . '" class="btn_style btn_file_upload" data="' . $field_comment . '" value="' . $GLOBALS['QXDREAM']['admin_language']['upload'] . '" /></p>';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 数字
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值       string  数字html
	+-----------------------------------------------------------------------
	*/
	public function number($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" class="text_box" size="5"' . $default . ' />';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 栏目
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值       string  栏目html
	+-----------------------------------------------------------------------
	*/
	public function cat_id($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$name = $is_system ? 'content' : 'content_detail';
		$select_option = '<select name="' . $name . '[' . $field_name . ']">';
		$selected = '';
		foreach($GLOBALS['QXDREAM']['CATEGORY'] as $k => $v) {
			$selected = !empty($default) && $v['cat_id'] == $default || isset($_GET['cat_id']) && $v['cat_id'] == $_GET['cat_id'] ? ' selected="selected"' : ''; 
			if($v['model_id'] == $_GET['model_id'] && $v['type'] == 0 && $v['has_child'] == 0) $select_option .= '<option value="' . $v['cat_id'] . '"' . $selected . '>' . $v['cat_name'] . "</option>\n"; 
		}
		return $select_option . '</select>';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 图片上传
	+-----------------------------------------------------------------------
	* @field_name     string  字段名
	* @field_comment  string  字段备注
	* @default        string  缺省值
	* @is_system      string  是否是系统内置
	+-----------------------------------------------------------------------
	* 返回值       string  图片上传html
	+-----------------------------------------------------------------------
	*/
	public function image($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" id="' . $field_name . '" class="text_box" size="20"' . $default . ' style="width: 300px;" />&nbsp;&nbsp;<input type="button" id="' . $field_name . '_picture" pic_id="' . $field_name . '" class="btn_style btn_picture_upload" data="' . $field_comment . '" value="' . $GLOBALS['QXDREAM']['admin_language']['upload'] . '" />';
	}
}
?>