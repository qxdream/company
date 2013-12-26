<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2009-10-04 �����ϴ�ģ���� $
	@version  $Id: AttachmentModel.class.php 1.8 2011-05-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class AttachmentModel extends Model {
	public $allow_upload = UPLOAD;     //�Ƿ������ϴ�
	public $save_path;                 //������������·��
	public $save_url = UPLOAD_URL;     //����url����·��
	public $upload_types;              //�����ϴ�������
	public $upload_maxsize;            //�����ϴ������ֵ
	public $attachment_table;          //������
	public $content_table;             //���±�       
	public $user_table;                //�û���
	public $content_id;                //��ǰ����ID
	
	/**
	+-----------------------------------------------------------------------
	* ��ʼ������
	+-----------------------------------------------------------------------
	* ����        ��
	+-----------------------------------------------------------------------
	* ����ֵ      ��
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
	* ���ò���
	+-----------------------------------------------------------------------
	* @company_id    ��˾ID
	* @company_uid   ��˾UID
	* @content_id ����ID(��ע��)
	+-----------------------------------------------------------------------
	* ����ֵ         ��
	+-----------------------------------------------------------------------
	*/
	public function set($company_id, $company_uid, $content_id = 0) {
		$this->company_id = $company_id;
		$this->company_uid = $company_uid;
		$this->content_id = $content_id;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �ϴ�����
	+-----------------------------------------------------------------------
	* @file               ��������Ϊfile��input��nameֵ
	* @upload_types       �����ϴ�����������
	* @content_id         
	* @upload_maxsize     �����ϴ����������ֵ
	* @overwrite          ��������ͬ��ʱ�Ƿ񸲸�(Ĭ��0������)     
	+-----------------------------------------------------------------------
	* ����ֵ              �ϴ�������URL(����)
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
		//�����ļ���,�����ļ��д����û�������ؼ�
		if(!dir_create($this->save_path)) return FALSE;
		$uploadfiles_array = array();
		//�ѵ����ļ��ϴ������ļ��ϴ�����һ����ά������,���ٺ����ж��ƶ��ļ������������������
		if(is_array($error)) { //���ļ��ϴ�
			$n = 0;
			foreach($error as $key => $val) {
				//����ô�ѭ��û���ļ����������ѭ��
				if($val === UPLOAD_ERR_NO_FILE) {
					$n++; //����¼����û���ϴ��ļ�
					continue;
				}
				if($val !== UPLOAD_ERR_OK) {
					echo $this->error($val, $name[$key]);
					return FALSE;
				}
				//���ڶ�ά������,ѭ��һ��һ�м�¼
				$uploadfiles_array[$key] = array('name' => $name[$key], 'type' => $type[$key], 'size' => $size[$key], 'tmp_name' => $tmp_name[$key], 'error' => $error[$key]);
			}
			if($n == count($error)) { //ȫ��Ϊ�յĻ�
				echo $GLOBALS['QXDREAM']['admin_language']['not_any_file_upload'];
				return FALSE;
			}
			unset($n);
		} else { //���ļ��ϴ�
			if($error != 0) {
				echo $this->error($error, $name);
				return FALSE;
			}
			//���ڶ�ά������,ֻ��һ�м�¼
			$uploadfiles_array[] = array('name' => $name, 'type' => $type, 'size' => $size, 'tmp_name' => $tmp_name, 'error' => $error);
		}
		$file_array = $attachment_id_arr = array(); //$attachment_id��¼��session��,�Ա����·���ʱ,�������¸���
		$image_obj = load_class('image'); //����ͼƬ��
		$image_obj->set($this->company_uid);
		foreach($uploadfiles_array as $key => $val) {
			//��ȡ�ļ���׺
			$file_suffix = file_suffix($val['name']);
			$named = $this->named($file_suffix);
			//�����ڷ��������ļ���(����·��)
			$save_file = $this->save_path . $named;
			if(!$this->overwrite && file_exists($save_file)) { //�ظ����Ʋ����Ǵ���
				$j=0;
				do {
					$j++;
					$temp_file = str_replace('.' . $file_suffix, '(' . $j . ').' . $file_suffix, $save_file);
				} while(file_exists($temp_file));
				$save_file = $temp_file;
				unset($temp_file, $j);
				$named = basename($save_file); //��ȡ�µ��ļ���
			}
			@chmod($this->save_path, 0777); //���ϴ��ļ���Ȩ�޸�Ϊ���,��Ȼ��linux�����»�û��дȨ��
			//url����·��
			$file_url = $this->save_url . $named; 
			if(!preg_match("/" . $this->upload_types . "/i", $file_suffix)) {
				echo $GLOBALS['QXDREAM']['admin_language']['this_file_type_not_allow'] . ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>';
				return FALSE;
			}
			if($val['size'] > $this->upload_maxsize) { //����Ա�����ƴ�С
				echo $GLOBALS['QXDREAM']['admin_language']['upload_file_cannot_beyond'] . size($this->upload_maxsize) . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $val['name'] . '</b>';
				return FALSE;
			}
			//�ƶ���ʱ������Ŀ��·��
			if(@is_uploaded_file($val['tmp_name'])) {
				if(@move_uploaded_file($val['tmp_name'], $save_file)) {
					$is_image = $this->is_image($file_suffix);
					$file_array[$key] = array( //�����ϴ��ɹ�����ļ���Ϣ
									'filename'      => $val['name'],  //ԭ�ļ���
									'file_type'     => $val['type'],  //����
									'file_size'     => $val['size'],  //��С
									'file_suffix'   => $file_suffix,  //��׺
									'file_path'     => $file_url,     //�ļ�url
									'is_image'      => $is_image      //�Ƿ���ͼ��
								);
					$insert_arr = $file_array[$key];
					if($is_image ==1) { //��ͼƬ,��������ͼ
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
		//�Ѳ���ĸ�����¼��session�Ա��������
		if(isset($_SESSION['attachment_id'])) {
			$_SESSION['attachment_id'] .= ',' . implode(',', $attachment_id_arr);
		} else {
			$_SESSION['attachment_id'] = implode(',', $attachment_id_arr);
		}
		return $file_array;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ������¼�����ݿ�
	+-----------------------------------------------------------------------
	* @array   ����
	+-----------------------------------------------------------------------
	* ����ֵ   �ɹ����뵽���ݿⷵ�ظ���ID,���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	function add($array) {
		unset($array['r_name']); //ɾ����������ݿ��
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
	* �ж��Ƿ�Ϊͼ��
	+-----------------------------------------------------------------------
	* @file_suffix    �ϴ��ļ��ĺ�׺��
	+-----------------------------------------------------------------------
	* ����ֵ          0��1
	+-----------------------------------------------------------------------
	*/
	function is_image($file_suffix) {
		$is_image = 0;
		if(preg_match("/jpg|jpeg|gif|bmp|png/i", $file_suffix)) $is_image = 1;
		return $is_image;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ϴ��ĸ�������
	+-----------------------------------------------------------------------
	* @file_suffix    �ϴ��ļ��ĺ�׺��
	+-----------------------------------------------------------------------
	* ����ֵ          ��������ļ���
	+-----------------------------------------------------------------------
	*/
	function named($file_suffix) {
		return date('YmdHis') . rand(1000, 9999) . '.' . $file_suffix;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ������Ϣ(˽�з���)
	+-----------------------------------------------------------------------
	* @error_number   ������
	*                 �ϴ����ļ���,Ĭ��Ϊ��
	+-----------------------------------------------------------------------
	* ����ֵ          ������Ϣ��ʾ����
	+-----------------------------------------------------------------------
	*/
	function error($error_number, $filename = '') {
		$str = '';
		if(!empty($filename)) $file_info = ',' . $GLOBALS['QXDREAM']['admin_language']['filename'] . ':<b>' . $filename . '</b>';
		switch($error_number) {
			//UPLOAD_ERR_OKΪ0,�ϴ��ɹ�
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
	* �����б���ʾ
	+-----------------------------------------------------------------------
	* ����        Ԫ
	+-----------------------------------------------------------------------
	* ����ֵ      ��������
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
			//������ͼ��ʾ����ͼ.����ͼƬ��ʾĬ�ϵ�ͼƬ
			$row['has_thumb'] = !empty($row['thumb']) && substr(($basename = basename($row['thumb'])), 0, 6) == 'thumb_' && ($row['thumb_width'] = substr($basename, 6, strpos($basename, '_', 6) - 6)) ? 1 : 0;
			$row['thumb'] = $row['is_image'] == 1 ? (empty($row['thumb']) ? $row['file_path'] : $row['thumb']) : PUBLIC_DIR . 'theme/' . ADMIN_PLAN . '/images/no-image-normal.gif';
			$data[] = $row;
		}
		$this->free_result($query);
		return $data;
	}
	/**
	+-----------------------------------------------------------------------
	* ��ȡ��������
	+-----------------------------------------------------------------------
	* @attachment_id    ����ID
	+-----------------------------------------------------------------------
	* ����ֵ     ��Դ���
	+-----------------------------------------------------------------------
	*/
	function get($attachment_id) {
		return $this->fetch("SELECT `file_path`,`is_image`,`content_id` FROM `{$this->attachment_table}` WHERE `attachment_id`='{$attachment_id}' AND `company_id`='{$this->company_id}'");
	}
	/**
	+-----------------------------------------------------------------------
	* ɾ������
	+-----------------------------------------------------------------------
	* @attachment_id  ������ID
	* @file_path      ����·��
	* @is_image       �Ƿ���ͼ��
	+-----------------------------------------------------------------------
	* ����ֵ   �ɹ�������
	+-----------------------------------------------------------------------
	*/
	function remove($attachment_id, $file_path, $is_image) {
		$this->query("DELETE FROM `{$this->attachment_table}` WHERE `attachment_id`='{$attachment_id}' AND `company_id`='{$this->company_id}'");
		if($is_image == 1) { //��ͼ��Ļ�,ɾ�����и�ͼ�������ͼ
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
	* �������ش���
	+-----------------------------------------------------------------------
	* @attachment_id  ����ID
	+-----------------------------------------------------------------------
	* ����ֵ          ��Դ���
	+-----------------------------------------------------------------------
	*/
	function download($attachment_id) {
		$attachment_id = intval($attachment_id);
		return $this->query("UPDATE " . $this->attachment_table . " SET `download_count`=`download_count`+1 WHERE `attachment_id`=" . $attachment_id);
	}
}
?>