<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-03-28 用户模型 $
	@version  $Id: CategoryModel.class.php 1.0 2011-05-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class CategoryModel extends Model {

	public $category_table;      //分类表
	public $msg;                 //消息提示
	private $is;                 //用来递归判断是否移动到子类
	public $category = array();  //分类数组
	
	/**
	+-----------------------------------------------------------------------
	* 设置数据
	+-----------------------------------------------------------------------
	* @company_uid   boolen or string 公司UID
	* @company_id    boolen or string 公司ID
	+-----------------------------------------------------------------------
	* 返回值                无
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
	* 获取分类
	+-----------------------------------------------------------------------
	* @cat_id  分类ID
	+-----------------------------------------------------------------------
	* 返回值   资源句柄
	+-----------------------------------------------------------------------
	*/
	public function get($cat_id) {
		return $this->fetch("SELECT `cat_id`,`type`,`model_id`,`parent_id`,`cat_name`,`is_nav`,`content_count`,`setting`,`template`,`url` FROM `{$this->category_table}` WHERE `cat_id`='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取分类
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   资源句柄
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
	* 添加分类
	* 添加一个分类,需要把这条记录中的has_child的更新为0,all_child_id更新为刚
	* 插入的ID,只要有父类,或则是父类的父类,都要把父类的has_child更新为1,把父
	* 类的all_child_id更新为父类的all_child_id逗号连接现在插入分类的ID,递归,
	* 直到父类的parent_id为0
	+-----------------------------------------------------------------------
	* @array     插入的数组
	* @set_array 为配置里的数组,插入到setting字段
	+-----------------------------------------------------------------------
	* 返回值     成功返回真,否则返回假
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
	* 递归更新all_child_id字段,父ID为0结束(私有方法)
	+-----------------------------------------------------------------------
	* @parent_id     父ID
	* @cat_id        添加类的ID
	+-----------------------------------------------------------------------
	* 返回值         无
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
	* 更新分类
	* 更新如果是在本类中,那么只移动该类,如果更新到顶级分类或是其他类中,那么把
	* 子类也移动到新类中
	+-----------------------------------------------------------------------
	* @cat_id   分类ID
	* @array    更新的数组
	* @set_array 为配置里的数组,更新到setting字段
	+-----------------------------------------------------------------------
	* 返回值    成功返回真,失败或者没有修改内容返回假
	+-----------------------------------------------------------------------
	*/
	public function edit($cat_id, $array, $set_array) {
		if(!is_array($array)) return FALSE;
		if($array['parent_id'] == $cat_id) {
			$this->msg = 'parent_cannot_be_self';
			return FALSE;
		}
		//不允许从父级移动到子级
		if($this->is_move_parent_to_child($cat_id, $array['parent_id'])) {
			$this->msg = 'not_allowed_move_to_child';
			return FALSE;
		}
		$cat_name = $array['cat_name'];
		//查询不是该ID的其他类名有没有等于表单提交的
		$data = $this->fetch("SELECT `cat_id` FROM `{$this->category_table}` WHERE `cat_name`='{$cat_name}' AND `cat_id`!='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
		unset($cat_name);
		if(is_array($data)) {
			$this->msg = 'category_name_exists';
			return FALSE;
		}
		$where = "`cat_id`='" . $cat_id . "'";
		//计数影响mysql列数
		$n = 0;
		$this->update($this->category_table, $array, $where);
		$this->affected_rows() > 0 && $n++;
		!empty($set_array) && $this->set_setting($this->category_table, $set_array, $where);
		$this->affected_rows() > 0 && $n++;
		unset($where);
		if($n > 0) { //如果记录没有修改过,影响列数还是0,mysql_affected_rows是指最近update,insert,delete影响的列数
			//该类别的父类改变了才执行以下操作
			if($array['parent_id'] != $this->category[$cat_id]['parent_id']) {
				//是否只在本类移动,逐个查找该ID的父类们是否等于目标父类($array['parent_id'])(除父ID为0),找到的话,说明是在本类中移动
				//顶级分类为0时,也不是在本类中移动
				if($this->is_move_in_self_category($cat_id, $array['parent_id']) && $array['parent_id'] != 0) {
					/* 如果该类的has_child为1,那么把该类第一层子类的parent_id改为该类的parent_id、has_child更新为0,
					 * 把该类的all_child_id更新为$cat_id,递归去掉目标父类与该类之间记录的all_child_id中的该类ID
					 */
				 	if($this->category[$cat_id]['has_child'] == 1) { //处理该分类下的子类们
						$this->query("UPDATE `{$this->category_table}` SET `has_child`=0,`all_child_id`='{$cat_id}' WHERE `cat_id`='{$cat_id}' AND `company_uid`='{$this->company_uid}'");
						$id_arr = array();
						foreach($this->category as $k => $v) {
							if($v['parent_id'] == $cat_id) $id_arr[] = $k;
						}
						$id_arr = implode(',', $id_arr);
						$this->query("UPDATE `{$this->category_table}` SET `parent_id`='" . $this->category[$cat_id]['parent_id'] . "' WHERE `cat_id` IN({$id_arr}) AND `company_uid`='{$this->company_uid}'");
					}
					//处理该分类的父类们(除目标分类及上级分类)
					$this->delete_id_from_old_parent($cat_id, $this->category[$cat_id]['parent_id'], $array['parent_id']);
				} else { //不是在本类中移动
					$parent_id = $this->category[$cat_id]['parent_id'];
					$all_child_id = $this->category[$cat_id]['all_child_id'];
					$parent_id != 0 && $this->delete_all_child_id($parent_id, $all_child_id);
					if($array['parent_id'] != 0) { //不是移动到顶级分类,要把本类的all_child_id逐个加到父类的,all_child_id中,父类的has_child为0的话就要更新为1
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
	* 递归更新all_child_id字段(增加),父ID为0结束,一定要在顶级类更新后结束(私有方法)
	+-----------------------------------------------------------------------
	* @parent_id     父ID
	* @all_child_id  所有子类ID(包括本类)
	+-----------------------------------------------------------------------
	* 返回值         无
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
	* 递归删除父分类中的该ID(私有方法)
	+-----------------------------------------------------------------------
	* @cat_id          要删除的ID
	* @parent_id       父分类ID
	* @post_parent_id  表单提交的ID
	+-----------------------------------------------------------------------
	* 返回值           递归结束时返回假
	+-----------------------------------------------------------------------
	*/
	public function delete_id_from_old_parent($cat_id, $parent_id, $post_parent_id) {
		if($parent_id == $post_parent_id) return FALSE;
		$parent_all_child_id = $this->category[$parent_id]['all_child_id'];
		$parent_all_child_id = delete_cat_id($parent_all_child_id, $cat_id);
		//父类的all_child_id中只有一个ID时(不含有逗号),即说明没有子类,更新has_child为0
		$set = !strstr($parent_all_child_id, ',') ? ',`has_child`=0' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$parent_all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		return $this->delete_id_from_old_parent($cat_id, $this->category[$parent_id]['parent_id'], $post_parent_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 递归判断是否在本类中移动--从子类到父类(私有方法)
	+-----------------------------------------------------------------------
	* @parent_id       父ID
	* @post_parent_id  表单提交的ID
	+-----------------------------------------------------------------------
	* 返回值           是返回真,否则返回假
	+-----------------------------------------------------------------------
	*/
	public function is_move_in_self_category($parent_id, $post_parent_id) {
		if($parent_id == 0) return FALSE; //这时退出递归，说明没有不是在子类移动
		if($this->category[$parent_id]['parent_id'] == $post_parent_id) return TRUE;
		//这里要加return,不然最后没有返回值
		return $this->is_move_in_self_category($this->category[$parent_id]['parent_id'], $post_parent_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取下一层子类
	+-----------------------------------------------------------------------
	* @parent_id       父ID
	+-----------------------------------------------------------------------
	* 返回值           子类数组
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
	* 递归判断是否在本类中移动--从父类到子类(私有方法)
	+-----------------------------------------------------------------------
	* @parent_id       父ID
	* @post_parent_id  表单提交的ID
	+-----------------------------------------------------------------------
	* 返回值           是返回真,否则返回假
	+-----------------------------------------------------------------------
	*/
	public function is_move_parent_to_child($parent_id, $post_parent_id) {
		$child_arr = array();
		$child_arr = $this->get_child($parent_id);//获取parent_id的子分类
		foreach($child_arr as $k => $v) { 
			if($v['cat_id'] == $post_parent_id) $this->is = TRUE;
			//递归获取$v['cat_id']分类的子分类
			$this->is_move_parent_to_child($v['cat_id'], $post_parent_id);
		}
		return $this->is;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 递归更新all_child_id字段,父ID为0结束,一定要在顶级类更新后结束(私有方法)
	+-----------------------------------------------------------------------
	* @parent_id     父ID
	* @all_child_id  所有子类ID(包括本类)
	+-----------------------------------------------------------------------
	* 返回值         无
	+-----------------------------------------------------------------------
	*/
	public function delete_all_child_id($parent_id, $all_child_id) {
		$parent_all_child_id = $this->category[$parent_id]['all_child_id'];
		$parent_all_child_id = delete_cat_id($parent_all_child_id, $all_child_id);
		//父类的all_child_id中只有一个ID时(不含有逗号),即说明没有子类,更新has_child为0
		$set = !strstr($parent_all_child_id, ',') ? ',`has_child`=0' : '';
		$this->query("UPDATE `{$this->category_table}` SET `all_child_id`='{$parent_all_child_id}'{$set} WHERE `cat_id`='{$parent_id}' AND `company_uid`='{$this->company_uid}'");
		if($this->category[$parent_id]['parent_id'] == 0) return FALSE;
		$this->delete_all_child_id($this->category[$parent_id]['parent_id'], $all_child_id);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 删除分类(会把子分类一起删除)
	* 删除一个分类,要递归把所有父分类中的all_child_id中该分类的ID去掉,还要在
	* 第一次递归时,把父分类的has_child更新为1
	+-----------------------------------------------------------------------
	* @cat_id  分类的ID
	+-----------------------------------------------------------------------
	* 返回值   成功返回真,失败返回假
	+-----------------------------------------------------------------------
	*/
	public function remove($cat_id) {
		$all_child_id = $this->category[$cat_id]['all_child_id'];
		//没有子类的,条件为等于,有子类的,条件为in
		$where = $this->category[$cat_id]['has_child'] == 0 ? "`cat_id`='{$cat_id}'" : "`cat_id` IN({$all_child_id})";
		$where .= " AND `company_uid`='{$this->company_uid}'";
		$this->query("DELETE FROM `{$this->category_table}` WHERE " . $where);
		$number = $this->affected_rows(); //这条一定要写在delete的下面,要特别注意
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
	* 分类列表显示
	+-----------------------------------------------------------------------
	* @parent_id  父类ID
	+-----------------------------------------------------------------------
	* 返回值      分类数组
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
	* 排序
	+-----------------------------------------------------------------------
	* @id_arr     表单提交的数组
	+-----------------------------------------------------------------------
	* 返回值      成功返回真
	+-----------------------------------------------------------------------
	*/
	public function list_order($id_arr) {
		if(!is_array($id_arr)) return FALSE;
		//$k为分类ID,$v为分类顺序
		foreach($id_arr as $k => $v) {
			$this->query("UPDATE `{$this->category_table}` SET `list_order`='{$v}' WHERE `cat_id`='{$k}' AND `company_uid`='{$this->company_uid}'");
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 获取栏目总数
	+-----------------------------------------------------------------------
	* @company_id   公司ID
	+-----------------------------------------------------------------------
	* 返回值        某个公司的栏目数量
	+-----------------------------------------------------------------------
	*/
	public function get_count() {
		return $this->result("SELECT COUNT(*) FROM `{$this->category_table}` WHERE `company_id`='{$this->company_id}'");
	}
}
?>