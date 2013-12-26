<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-04-30 �ֶα������� $
	@version  $Id: Form.class.php 1.1 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Form {

	/**
	+-----------------------------------------------------------------------
	* �����ı�
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ          string  �ı�html
	+-----------------------------------------------------------------------
	*/
	public function text($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" class="text_box" size="20" style="width: 300px;"' . $default . ' />';
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����ı�
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ       string  �����ı�html
	+-----------------------------------------------------------------------
	*/
	public function textarea($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : $default;
		$name = $is_system ? 'content' : 'content_detail';
		return '<textarea name="' . $name . '[' . $field_name . ']" rows="3" cols="50" style="width: 93%; height: 50px;">' . $default . '</textarea>';
	}
	
	/**
	+-----------------------------------------------------------------------
	* �༭��
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ       string  �༭��html
	+-----------------------------------------------------------------------
	*/
	public function editor($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : $default;
		$name = $is_system ? 'content' : 'content_detail';
		return '<textarea name="' . $name . '[' . $field_name . ']" id="' . $field_name . '" cols="60" rows="4">' . $default . '</textarea>' . editor($field_name, 'Default', '94%', '500') . '<p style="margin-top: 8px;"><input type="button" id="' . $field_name . '_upload" editor_id="' . $field_name . '" class="btn_style btn_file_upload" data="' . $field_comment . '" value="' . $GLOBALS['QXDREAM']['admin_language']['upload'] . '" /></p>';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ       string  ����html
	+-----------------------------------------------------------------------
	*/
	public function number($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" class="text_box" size="5"' . $default . ' />';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��Ŀ
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ       string  ��Ŀhtml
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
	* ͼƬ�ϴ�
	+-----------------------------------------------------------------------
	* @field_name     string  �ֶ���
	* @field_comment  string  �ֶα�ע
	* @default        string  ȱʡֵ
	* @is_system      string  �Ƿ���ϵͳ����
	+-----------------------------------------------------------------------
	* ����ֵ       string  ͼƬ�ϴ�html
	+-----------------------------------------------------------------------
	*/
	public function image($field_name, $field_comment = '', $default = '', $is_system = 1) {
		$default = empty($default) ? '' : ' value="' . $default . '"';
		$name = $is_system ? 'content' : 'content_detail';
		return '<input type="text" name="' . $name . '[' . $field_name . ']" id="' . $field_name . '" class="text_box" size="20"' . $default . ' style="width: 300px;" />&nbsp;&nbsp;<input type="button" id="' . $field_name . '_picture" pic_id="' . $field_name . '" class="btn_style btn_picture_upload" data="' . $field_comment . '" value="' . $GLOBALS['QXDREAM']['admin_language']['upload'] . '" />';
	}
}
?>