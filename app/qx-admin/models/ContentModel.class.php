<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Muticategory
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-03-08 后台内容类 $
	@version  $Id: ContentModel.class.php 1.2 2011-05-14
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class ContentModel extends Model {

	public $content_table;       //内容表
	public $detail_table;        //具体内容表
	public $attachment_table;    //附件表
	public $category_table;      //分类表
	public $user_table;          //用户表
	
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
		$this->content_table = DB_PRE . 'content';
		$this->category_table = DB_PRE . 'category';
		$this->attachment_table = DB_PRE . 'attachment';
		$this->user_table = DB_PRE . 'user';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 设置参数
	+-----------------------------------------------------------------------
	* @company_id    公司ID
	* @company_uid   公司UID
	+-----------------------------------------------------------------------
	* 返回值         无
	+-----------------------------------------------------------------------
	*/
	public function set($company_id, $company_uid = '') {
		$this->company_id = $company_id;
		$this->company_uid = empty($company_uid) ? $GLOBALS['QXDREAM']['COMPANY'][$company_id]['company_uid'] : $company_uid; 
	}
	
	/**
	+-----------------------------------------------------------------------
	* 设置模型(仅单页时才需设置)
	+-----------------------------------------------------------------------
	* @model_name    模型名
	+-----------------------------------------------------------------------
	* 返回值         无
	+-----------------------------------------------------------------------
	*/
	public function set_model($model_name) {
		$this->detail_table = DB_PRE . 'content_' . $model_name;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取一篇内容
	+-----------------------------------------------------------------------
	* @content_id  内容ID
	+-----------------------------------------------------------------------
	* 返回值       内容数组
	+-----------------------------------------------------------------------
	*/
	public function get($content_id) {
		$data = array();
		//读取content_id以便更新时使用
		$sql = "SELECT a.content_id,a.model_id,a.cat_id,a.user_id,a.title,a.description,a.status,a.attachment_id,a.hits_count,a.post_time,
				d.* FROM {$this->content_table} a,{$this->detail_table} d
				WHERE a.content_id=d.content_id AND a.content_id='{$content_id}' AND a.company_id='{$this->company_id}'";
		$data = $this->fetch($sql);
		if(is_array($data)) {
			//附件
			if(!empty($data['attachment_id'])) {
				$data['attachment'] = $this->fetch_all("SELECT `attachment_id`,`filename`,`file_path`,`file_size`,`post_time`,`is_image` FROM `{$this->attachment_table}` WHERE `attachment_id` IN({$data['attachment_id']}) AND company_id='{$this->company_id}'");
			}
		}
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取select下拉分类
	+-----------------------------------------------------------------------
	* @model_id        模型
	* @select_cat_id   选中的分类
	+-----------------------------------------------------------------------
	* 返回值           下拉分类字符串
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
	* 添加内容
	+-----------------------------------------------------------------------
	* @base_arr     基本信息数组
	* @detail_arr   详细信息数组
	+-----------------------------------------------------------------------
	* 返回值        成功返回真
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
			//处理附件
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
	* 更新内容
	+-----------------------------------------------------------------------
	* @old_arr      更新前的数据
	* @base_arr     基本信息数组
	* @detail_arr   详细信息数组
	+-----------------------------------------------------------------------
	* 返回值        成功返回真
	+-----------------------------------------------------------------------
	*/
	public function edit($old_arr, $base_arr, $detail_arr) {
		if(!is_array($old_arr) || !is_array($base_arr) || !is_array($detail_arr)) return FALSE;	
		//处理更新时间
		$base_arr['update_time'] = $GLOBALS['QXDREAM']['timestamp'];
		$where = "`content_id`='" . $old_arr['content_id'] . "'";
		//处理附件
		if(isset($base_arr['attachment_id'])) {
			$this->query("UPDATE `{$this -> attachment_table}` SET `content_id`='{$old_arr['content_id']}' WHERE `attachment_id` IN({$base_arr['attachment_id']})");
			if(!empty($old_arr['attachment_id'])) { $base_arr['attachment_id'] = $old_arr['attachment_id'] . ',' . $base_arr['attachment_id']; }
		} 
		if($this->update($this->content_table, $base_arr, $where)) {
			//处理作者
			if(isset($detail_arr['author'])) {
				$detail_arr['author'] = empty($detail_arr['author']) ? $GLOBALS['QXDREAM']['qx_user_name'] : $detail_arr['author'];
			}
			$this->update($this->detail_table, $detail_arr, $where);
			//如果原数据是草稿,才更新统计次数(草稿发布),如果是别的用户的草稿,该用户发布,那么该篇内容为现在这个用户的
			if($old_arr['status'] == 3 && $base_arr['status'] == 1) {
				$this->add_content_count($base_arr['cat_id'], $GLOBALS['QXDREAM']['qx_user_id']);
				$base_arr['user_id'] = $GLOBALS['QXDREAM']['qx_user_id'];
			}
			if(3 != $old_arr['model_id']) {
				//分类改变了,要统计分类数据,原来是草稿也不用统计
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
	* 修改内容的分类改变时,统计分类内容数据
	+-----------------------------------------------------------------------
	* @old_cat_id     老的分类
	* @new_cat_id     新的分类
	+-----------------------------------------------------------------------
	* 返回值          无
	+-----------------------------------------------------------------------
	*/
	public function cat_content_count($old_cat_id, $new_cat_id) {
		$this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`-1 WHERE `cat_id`='{$old_cat_id}'  AND company_id='{$this->company_id}'");
		$this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`+1 WHERE `cat_id`='{$new_cat_id}'  AND company_id='{$this->company_id}'");
	}
	/**
	+-----------------------------------------------------------------------
	* 操作状态(含批量)
	+-----------------------------------------------------------------------
	* @content_id      内容ID(可为数组)
	* @value           1为还原,2为移至回收站
	*                  移动至回收站,内容数减1,该分类的内容数减1,反之加1
	*                  草稿移至回收站不用统计
	+-----------------------------------------------------------------------
	* 返回值           成功返回真
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
			//要更新的数组
			$update_content_data = $this->fetch_all_column_key("SELECT `content_id`,`cat_id`,`status`,`user_id` FROM `{$this->content_table}` WHERE `content_id` IN({$content_id_str}) AND `status`!='{$value}'", 'content_id');
			if($post_countent_count == count($update_content_data)) { //提交的数量与实际数量一致时才计数，防止重下表单时计数出错
				$recycle_update_content_data = array(); //移至回收站时更新数组
				foreach($content_id as $v) {
					if(!($update_content_data[$v]['status'] == 3 && $value == 2)) {
						$cat_id = is_array($update_content_data[$v]) ? $update_content_data[$v]['cat_id'] : -1;
						'+' == $operation ? $this->add_content_count($cat_id, $update_content_data[$v]['user_id']) : $recycle_update_content_data[$cat_id . '_' . $update_content_data[$v]['user_id']] = array('cat_id' => $cat_id, 'user_id' => $update_content_data[$v]['user_id']);
					}
				}
				$this->query("UPDATE `{$this->content_table}` SET `status`='{$value}' WHERE `content_id` IN({$content_id_str})");
				if('-' == $operation) { //重新统计功能必需写在更新之后
					foreach($recycle_update_content_data as $k => $v) { $this->count_content($v['cat_id'], $v['user_id']); }
				}
			}
		} else {
			$data = $this->fetch("SELECT `cat_id`,`status`,`user_id` FROM `{$this->content_table}` WHERE `content_id`='{$content_id}' AND `status`!='{$value}'");
			if(is_array($data)) {
				$this->query("UPDATE `{$this->content_table}` SET `status`='{$value}' WHERE `content_id`='{$content_id}'");
				if(!($data['status'] == 3 && $value == 2)) { //如果原来是草稿,现在又移至回收站,则不统计
					$cat_id = is_array($data) ? $data['cat_id'] : -1; //不存在设置-1,在operation_content_count里-1不是用更新分类里的内容数量的,也就是未分类
					'+' == $operation ? $this->add_content_count($cat_id, $data['user_id']) : $this->count_content($cat_id, $data['user_id']);
				}
			}
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加内容统计
	+-----------------------------------------------------------------------
	* @cat_id              分类ID
	* @user_id             用户ID
	+-----------------------------------------------------------------------
	* 返回值               无
	+-----------------------------------------------------------------------
	*/
	public function add_content_count($cat_id, $user_id) { //以后考虑优化
		$this->query("UPDATE `{$this->user_table}` SET `content_count`=`content_count`+1 WHERE `user_id`='{$user_id}' AND company_id='{$this->company_id}'");
		if($cat_id >= 0) $this->query("UPDATE `{$this->category_table}` SET `content_count`=`content_count`+1 WHERE `cat_id`='{$cat_id}' AND company_id='{$this->company_id}'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 统计内容数量(一般删除时使用)
	+-----------------------------------------------------------------------
	* @cat_id              分类ID
	* @user_id             用户ID
	+-----------------------------------------------------------------------
	* 返回值               无
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
	* 彻底删除数据
	+-----------------------------------------------------------------------
	* @content_id    内容ID
	+-----------------------------------------------------------------------
	* 返回值         成功返回真
	+-----------------------------------------------------------------------
	*/
	public function remove($content_id) {
		//处理内容
		$this->query("DELETE FROM `{$this->content_table}` WHERE `content_id`='{$content_id}' AND model_id!='3' AND company_id='{$this->company_id}'");
		$this->query("DELETE FROM `{$this->detail_table}` WHERE `content_id`='{$content_id}'");
		//处理附件
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id`='{$content_id}' AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 通过栏目ID获取单页内容的content_id
	+-----------------------------------------------------------------------
	* @cat_id        栏目ID
	+-----------------------------------------------------------------------
	* 返回值         内容content_id
	+-----------------------------------------------------------------------
	*/
	public function get_page_content_id($cat_id) {
		$page_id_data = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_page');
		return $page_id_data[$cat_id];
	}
	
	/**
	+-----------------------------------------------------------------------
	* 彻底删除单页内容数据
	+-----------------------------------------------------------------------
	* @cat_id        栏目ID
	+-----------------------------------------------------------------------
	* 返回值         成功返回真
	+-----------------------------------------------------------------------
	*/
	public function remove_page($cat_id) {
		$content_id = $this->get_page_content_id($cat_id);
		//处理内容
		$this->query("DELETE FROM `{$this->content_table}` WHERE `cat_id`='{$cat_id}' AND model_id='3' AND company_id='{$this->company_id}'");
		$this->query("DELETE FROM `{$this->detail_table}` WHERE `cat_id`='{$cat_id}'");
		//处理附件
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id`='{$content_id}' AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 清空回收站(status为2的)
	+-----------------------------------------------------------------------
	* @model_id      模型ID
	* @cat_id        栏目ID      
	+-----------------------------------------------------------------------
	* 返回值         成功返回真
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
		//detail_table与模型有关，另处理
		if(empty($model_id)) { //清空所有模型的回收站
			foreach($data as $v) {
				$this->query("DELETE FROM `" . DB_PRE . 'content_' . $GLOBALS['QXDREAM']['MODEL'][$v['model_id']]['model_name'] . "` WHERE `content_id`='{$v['content_id']}'");
			}
		} else { //针对某个模型清空
			$this->query("DELETE FROM `{$this->detail_table}` WHERE `content_id` IN({$content_id_str})");
		}
		$this->query("DELETE FROM `{$this->content_table}` WHERE company_id='{$this->company_id}' AND `status`='2'{$where}");
			//处理附件
		$this->query("UPDATE `{$this->attachment_table}` SET `content_id`=0,`status`=-1 WHERE `content_id` IN({$content_id_str}) AND company_id='{$this->company_id}'");
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 内容列表显示
	+-----------------------------------------------------------------------
	* @cat_id     分类ID
	* @user_id    用户ID
	* @status     状态
	* @model_id   模型
	* @search_key 搜索关键字
	+-----------------------------------------------------------------------
	* 返回值    内容数组
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
			//5个关键字
			$search_key_arr =  explode_keyword($search_key, 5, ' ');
			$where .= " AND (a.title LIKE ";
			$count = count($search_key_arr);
			for($i=0; $i < $count - 1; $i++) { //最后一个数组另处理
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
	* 计算各个状态内容的数量
	+-----------------------------------------------------------------------
	* @model_id    模型ID
	* @cat_id      分类ID
	+-----------------------------------------------------------------------
	* 返回值       数组
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
				case '1': //发布
					$i = 1;
					$row['text'] = $GLOBALS['QXDREAM']['admin_language']['already_post'];
				break;
				
				case '2': //回收站
					$i = 3;
					$row['text'] = $GLOBALS['QXDREAM']['admin_language']['recycle_bin'];
				break;
				
				case '3': //草稿
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