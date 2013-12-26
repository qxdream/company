<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2009-10-04 附件上传模型类 $
	@version  $Id: AttachmentModel.class.php 1.8 2011-05-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class AttachmentModel extends Model {
	public $allow_upload = UPLOAD;     //是否允许上传
	public $save_path;                 //附件保存物理路径
	public $save_url = UPLOAD_URL;     //附件url访问路径
	public $upload_types;              //允许上传的类型
	public $upload_maxsize;            //允许上传的最大值
	public $attachment_table;          //附件表
	public $content_table;             //文章表       
	public $user_table;                //用户表
	public $content_id;                //当前文章ID
	
	/**
	+-----------------------------------------------------------------------
	* 初始化数据
	+-----------------------------------------------------------------------
	* 参数        无
	+-----------------------------------------------------------------------
	* 返回值      无
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		$this->upload_maxsize = UPLOAD_MAXSIZE;
		$this->attachment_table = DB_PRE . 'attachment';
		$this->content_table = DB_PRE . 'content';
		$this->user_table = DB_PRE . 'user';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 设置参数
	+-----------------------------------------------------------------------
	* @company_id    公司ID
	* @company_uid   公司UID
	* @content_id 文章ID(请注意)
	+-----------------------------------------------------------------------
	* 返回值         无
	+-----------------------------------------------------------------------
	*/
	public function set($company_id, $company_uid, $content_id = 0) {
		$this->company_id = $company_id;
		$this->company_uid = $company_uid;
		$this->content_id = $content_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 上传附件
	+-----------------------------------------------------------------------
	* @file               表单中类型为file的input的name值
	* @upload_types       允许上传附件的类型
	* @content_id         
	* @upload_maxsize     允许上传附件的最大值
	* @overwrite          服务器上同名时是否覆盖(默认0不覆盖)     
	+-----------------------------------------------------------------------
	* 返回值              上传附件的URL(数组)
	+-----------------------------------------------------------------------
	*/
	function upload($file, $upload_types = UPLOAD_ALLOW_SUFFIX, $upload_maxsize = UPLOAD_MAXSIZE, $overwrite = 0) {
		if(!$this->allow_upload || !isset($_FILES[$file])) {
			echo $GLOBALS['QXDREAM']['admin_language']['not_allow_upload_or_invalid'];
			return FALSE;
		}
		$name = $_FILES[$file]['name'];
		$type = $_FILES[$file]['type'];
		$tmp_name = $_FILES[$file]['tmp_name'];
		$error = $_FILES[$file]['error'];
		$size = $_FILES[$file]['size'];
		$this->upload_maxsize = $upload_maxsize;
		$this->upload_types = $upload_types;
		$this->overwrite = $overwrite;
		$this->save_path = UPLOAD_ROOT . $this->company_uid . '/' . date('Y/md/');
		$this->save_url = UPLOAD_URL . $this->company_uid . '/' .  date('Y/md/');
		//建立文件夹,建立文件夹错误或没建立返回假
		if(!dir_create($this->save_path)) return FALSE;
		$uploadfiles_array = array();
		//把单个文件上传或多个文件上传存在一个二维数组里,减少后面判断移动文件到服务器的冗余代码
		if(is_array($error)) { //多文件上传
			$n = 0;
			foreach($error as $key => $val) {
				//如果该次循环没有文件就跳过这层循环
				if($val === UPLOAD_ERR_NO_FILE) {
					$n++; //并记录几个没有上传文件
					continue;
				}
				if($val !== UPLOAD_ERR_OK) {
					echo $this->error($val, $name[$key]);
					return FALSE;
				}
				//存在二维数组里,循环一次一行记录
				$uploadfiles_array[$key] = array('name' => $name[$key], 'type' => $type[$key], 'size' => $size[$key], 'tmp_name' => $tmp_name[$key], 'error' => $error[$key]);
			}
			if($n == count($error)) { //全部为空的话
				echo $GLOBALS['QXDREAM']['admin_language']['not_any_file_upload'];
				return FALSE;
			}
			unset($n);
		} else { //单文件上传
			if($error != 0) {
				echo $this->error($error, $name);
				return FALSE;
			}
			//存在二维数组里,只有一行记录
			$uploadfiles_array[] = array('name' => $name, 'type' => $type, 'size' => $size, 'tmp_name' => $tmp_name, 'error' => $error);
		}
		$file_array = $attachment_id_arr = array(); //$attachment_id记录到session中,以便文章发表时,更新文章附件
		$image_obj = load_class('image'); //载入图片类
		$image_obj->set($this->company_uid);
		foreach($uploadfiles_array as $key => $val) {
			//获取文件后缀
			$file_suffix = file_suffix($val['name']);
			$named = $this->named($file_suffix);
			//保存在服务器的文件名(物理路径)
			$save_file = $this->save_path . $named;
			if(!$this->overwrite && file_exists($save_file)) { //重复名称不覆盖处理
				$j=0;
				do {
					$j++;
					$temp_file = str_replace('.' . $file_suffix, '(' . $j . ').' . $file_suffix, $save_file);
				} while(file_exists($temp_file));
				$save_file = $temp_file;
				unset($temp_file, $j);
				$named = basename($save_file); //获取新的文件名
			}
			@chmod($this->save_path, 0777); //把上传文件的权限改为最高,不然在linux主机下会没有写权限
			//url访问路径
			$file_url = $this->save_url . $named; 
			if(!preg_match("/" . $this->upload_types . "/i", $file_suffix)) {
				echo $GLOBALS['QXDREAM']['admin_language']['this_file_type_not_allow'] . ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>';
				return FALSE;
			}
			if($val['size'] > $this->upload_maxsize) { //管理员不限制大小
				echo $GLOBALS['QXDREAM']['admin_language']['upload_file_cannot_beyond'] . size($this->upload_maxsize) . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>';
				return FALSE;
			}
			//移动临时附件至目的路径
			if(@is_uploaded_file($val['tmp_name'])) {
				if(@move_uploaded_file($val['tmp_name'], $save_file)) {
					$is_image = $this->is_image($file_suffix);
					$file_array[$key] = array( //返回上传成功后的文件信息
									'filename'      => $val['name'],  //原文件名
									'file_type'     => $val['type'],  //类型
									'file_size'     => $val['size'],  //大小
									'file_suffix'   => $file_suffix,  //后缀
									'file_path'     => $file_url,     //文件url
									'is_image'      => $is_image      //是否是图像
								);
					$insert_arr = $file_array[$key];
					if($is_image ==1) { //是图片,创建缩略图
						$thumb_width  = 200;
						$thumb_height = 150;
						$file_url = $image_obj->thumb($file_url, $thumb_width, $thumb_height);
						$insert_arr['thumb'] = $file_url;
						unset($thumb_width, $thumb_height);
					};
					$last_insert_attach_id = $this->add($insert_arr);
					if(empty($last_insert_attach_id)) {
						echo $GLOBALS['QXDREAM']['admin_language']['insert_mysql_error'];
						return FALSE;
					}
					$attachment_id_arr[] = $last_insert_attach_id;
					unset($inser_arr);
				} else {
					echo $GLOBALS['QXDREAM']['admin_language']['cannot_move_destination_dir'] . ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>,' . $GLOBALS['QXDREAM']['admin_language']['maybe_permission_limit'];
					return FALSE;
				}
			} else {
				echo $GLOBALS['QXDREAM']['admin_language']['file_maybe_been_blocked'] . ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>';
				return FALSE;
			}
		}
		//把插件的附件记录到session以便关联文章
		if(isset($_SESSION['attachment_id'])) {
			$_SESSION['attachment_id'] .= ',' . implode(',', $attachment_id_arr);
		} else {
			$_SESSION['attachment_id'] = implode(',', $attachment_id_arr);
		}
		return $file_array;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 附件记录到数据库
	+-----------------------------------------------------------------------
	* @array   数组
	+-----------------------------------------------------------------------
	* 返回值   成功插入到数据库返回附件ID,否则返回假
	+-----------------------------------------------------------------------
	*/
	function add($array) {
		unset($array['r_name']); //删除不插进数据库的
		$array['attachment_id'] = NULL;
		$array['content_id'] = $this->content_id;
		$array['post_time'] = $array['update_time'] = $GLOBALS['QXDREAM']['timestamp']; 
		$array['user_id'] = $GLOBALS['QXDREAM']['qx_user_id'];
		$array['company_id'] = $this->company_id;
		$array['company_uid'] = $this->company_uid;
		if($this->insert($this->attachment_table, $array)) {
			$attachment = $this->last_insert_id();
			return $attachment;
		}
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 判断是否为图像
	+-----------------------------------------------------------------------
	* @file_suffix    上传文件的后缀名
	+-----------------------------------------------------------------------
	* 返回值          0或1
	+-----------------------------------------------------------------------
	*/
	function is_image($file_suffix) {
		$is_image = 0;
		if(preg_match("/jpg|jpeg|gif|bmp|png/i", $file_suffix)) $is_image = 1;
		return $is_image;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 对上传的附件命名
	+-----------------------------------------------------------------------
	* @file_suffix    上传文件的后缀名
	+-----------------------------------------------------------------------
	* 返回值          命名后的文件名
	+-----------------------------------------------------------------------
	*/
	function named($file_suffix) {
		return date('YmdHis') . rand(1000, 9999) . '.' . $file_suffix;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 错误信息(私有方法)
	+-----------------------------------------------------------------------
	* @error_number   错误编号
	*                 上传的文件名,默认为空
	+-----------------------------------------------------------------------
	* 返回值          错误信息提示内容
	+-----------------------------------------------------------------------
	*/
	function error($error_number, $filename = '') {
		$str = '';
		if(!empty($filename)) $file_info = ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $filename . '</b>';
		switch($error_number) {
			//UPLOAD_ERR_OK为0,上传成功
			//UPLOAD_ERR_INI_SIZE
			case 1:
				$str = $GLOBALS['QXDREAM']['admin_language']['upload_file_beyond_ini_set'] . $file_info;
				break;
			//UPLOAD_ERR_FORM_SIZE
			case 2:
				$str = $GLOBALS['QXDREAM']['admin_language']['upload_file_beyond_form_set'] . $file_info;
				break;
			//UPLOAD_ERR_PARTIAL
			case 3:
				$str = $GLOBALS['QXDREAM']['admin_language']['file_upload_not_perfect'] . $file_info;
				break;
			//UPLOAD_ERR_NO_FILE
			case 4:
				$str = $GLOBALS['QXDREAM']['admin_language']['not_any_file_upload'];
				break;
			//UPLOAD_NO_TMP_DIR
			case 6:
				$str = $GLOBALS['QXDREAM']['admin_language']['phpini_has_not_give_tmp_dir'];
				break;
			//UPLOAD_ERR_CANT_WRITE
			case 7:
				$str = $GLOBALS['QXDREAM']['admin_language']['file_fail_to_write_in_disc'];
				break;
		}
		return $str;
	}
	/**
	+-----------------------------------------------------------------------
	* 附件列表显示
	+-----------------------------------------------------------------------
	* 参数        元
	+-----------------------------------------------------------------------
	* 返回值      附件数组
	+-----------------------------------------------------------------------
	*/
	function list_info() {
		$this->count("SELECT COUNT(*) FROM `{$this->attachment_table}` WHERE `company_id`='{$this->company_id}'");
		$sql = "SELECT `a`.`attachment_id`,`a`.`filename`,`a`.`file_path`,`a`.`file_type`,`a`.`file_size`,`a`.`content_id`,`a`.`user_id`,
				`a`.`is_image`,`a`.`thumb`,`a`.`post_time`,`a`.`download_count`,`c`.`title`,`c`.`model_id`,`u`.`user_name`
				FROM `{$this->attachment_table}` `a`
				LEFT JOIN `{$this->content_table}` `c` ON `a`.`content_id`=`c`.`content_id`
				LEFT JOIN `{$this->user_table}` `u` ON `a`.`user_id`=`u`.`user_id`
				WHERE `a`.`company_id`='{$this->company_id}'
				ORDER BY `a`.`attachment_id` DESC"  . $this->pagenation->sql_limit();
		$query = $this->query($sql);
		$data = array();
		while($row = $this->fetch_array($query)) {
			//有缩略图显示缩略图.不是图片显示默认的图片
			$row['has_thumb'] = !empty($row['thumb']) && substr(($basename = basename($row['thumb'])), 0, 6) == 'thumb_' && ($row['thumb_width'] = substr($basename, 6, strpos($basename, '_', 6) - 6)) ? 1 : 0;
			$row['thumb'] = $row['is_image'] == 1 ? (empty($row['thumb']) ? $row['file_path'] : $row['thumb']) : PUBLIC_DIR . 'theme/' . ADMIN_PLAN . '/images/no-image-normal.gif';
			$data[] = $row;
		}
		$this->free_result($query);
		return $data;
	}
	/**
	+-----------------------------------------------------------------------
	* 获取单个附件
	+-----------------------------------------------------------------------
	* @attachment_id    附件ID
	+-----------------------------------------------------------------------
	* 返回值     资源句柄
	+-----------------------------------------------------------------------
	*/
	function get($attachment_id) {
		return $this->fetch("SELECT `file_path`,`is_image`,`content_id` FROM `{$this->attachment_table}` WHERE `attachment_id`='{$attachment_id}' AND `company_id`='{$this->company_id}'");
	}
	/**
	+-----------------------------------------------------------------------
	* 删除附件
	+-----------------------------------------------------------------------
	* @attachment_id  附件的ID
	* @file_path      附件路径
	* @is_image       是否是图像
	+-----------------------------------------------------------------------
	* 返回值   成功返回真
	+-----------------------------------------------------------------------
	*/
	function remove($attachment_id, $file_path, $is_image) {
		$this->query("DELETE FROM `{$this->attachment_table}` WHERE `attachment_id`='{$attachment_id}' AND `company_id`='{$this->company_id}'");
		if($is_image == 1) { //是图像的话,删除所有该图像的缩略图
			$files = glob(UPLOAD_ROOT . '/thumbnails/' . $this->company_uid . '/thumb_*' . basename($file_path));
			foreach($files as $k => $v) {
				file_exists($v) && @unlink($v);
			}
		}
		$data = $this->fetch("SELECT `attachment_id` FROM `{$this->content_table}` WHERE `content_id`='{$this->content_id}' AND `company_id`='{$this->company_id}'");
		if(is_array($data)) {
			$attachment_id = delete_cat_id($data['attachment_id'], $attachment_id);
			$this->query("UPDATE `{$this->content_table}` SET `attachment_id`='{$attachment_id}' WHERE `content_id`='{$this->content_id}' AND `company_id`='{$this->company_id}'");
		}
		file_exists(QX_ROOT . $file_path) && @unlink(QX_ROOT . $file_path);
		return TRUE;
	}
	/**
	+-----------------------------------------------------------------------
	* 附件下载次数
	+-----------------------------------------------------------------------
	* @attachment_id  附件ID
	+-----------------------------------------------------------------------
	* 返回值          资源句柄
	+-----------------------------------------------------------------------
	*/
	function download($attachment_id) {
		$attachment_id = intval($attachment_id);
		return $this->query("UPDATE " . $this->attachment_table . " SET `download_count`=`download_count`+1 WHERE `attachment_id`=" . $attachment_id);
	}
}
?>