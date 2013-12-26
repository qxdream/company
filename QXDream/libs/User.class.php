<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-02-24 �û�ģ�� $
	@version  $Id: User.class.php 1.2 2011-04-30
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class User extends Model {

	public $user_table;       //��Ա��
	public $total = 0;        //��¼����(ʹ���ڷ�ҳ֮��)
	public $error_type = 0;   //��¼����ʽ,1Ϊ�û���,2Ϊ�����,3Ϊ��֤���
	
    /**
	+-----------------------------------------------------------------------
	* ��ʼ������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function _initialize() {
		if(empty($GLOBALS['QXDREAM']['COMPANY_UID'])) {
			$this->cache_all(-100, TRUE); 
		}
		$GLOBALS['QXDREAM']['COMPANY'] = cache_read('company');
		$GLOBALS['QXDREAM']['MR'] = cache_read('module_resource');
		$GLOBALS['QXDREAM']['MODEL'] = cache_read('model');
		$this->set();
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��������
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function set() {
		$this->user_table = DB_PRE . 'user';
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����û��Ƿ��¼
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function check_user() {
		$qx_auth = get_cookie('qx_auth');
		$hash = get_cookie('hash');
		if(!empty($qx_auth)) { //��¼״̬��Ҫ�ж�
			list($GLOBALS['QXDREAM']['qx_user_id'], $qx_user_pass, $GLOBALS['QXDREAM']['qx_login_count']) = explode("\t", auth_code($qx_auth, 'DECODE'));
			$data = '';
			$data = $this->fetch("SELECT `user_id`,`company_id`,`company_uid`,`user_pass`,`user_name`,`login_ip`,`login_time`,`group_id`,`salt`,`disabled` FROM `{$this->user_table}` WHERE `user_id`='" . $GLOBALS['QXDREAM']['qx_user_id'] . "'");
			if(is_array($data) && auth_code($hash, 'DECODE') == md5(md5(QX_KEY) . $data['user_id'] . $data['group_id'] .$data['user_name']) && $data['user_pass'] == $qx_user_pass) {
				if($data['group_id'] > 1) {
					$company_data = $GLOBALS['QXDREAM']['COMPANY'][$data['company_id']];
					$this->check_company_disabled($company_data) && show_msg('company_disabled'); //��˾������
					$GLOBALS['QXDREAM']['qx_company_id']  = $data['company_id']; //��Ϊ��ʱ��˵���ǹ�˾��¼
					$GLOBALS['QXDREAM']['qx_mr_ids']      = $company_data['mr_ids']; //��˾ģ��
					unset($company_data);
					$GLOBALS['QXDREAM']['USER_GROUP']     = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_user_group'); //��ǰ����Ա��˾�û���ɫ
					$GLOBALS['QXDREAM']['CATEGORY']       = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_category');
					define('VIEW_COMPANY_ROOT', QX_ROOT . APP_DIR . 'company/views/' . $data['company_uid'] . '/'); //ǰ̨��ͼĿ¼
					define('VIEW_COMPANY_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . $data['company_uid'] . '/css/'); //ǰ̨��ͼCSS
				} else {
					$GLOBALS['QXDREAM']['qx_company_id']  = 0;
					$GLOBALS['QXDREAM']['USER_GROUP']     = cache_read('user_group'); 
				}
				$GLOBALS['QXDREAM']['qx_user_name']   = $data['user_name'];
				$GLOBALS['QXDREAM']['qx_group_id']    = $data['group_id']; //��Ա��
				$GLOBALS['QXDREAM']['qx_disabled']    = $data['disabled']; //�Ƿ����
				$GLOBALS['QXDREAM']['qx_login_ip']    = $data['login_ip'];
				$GLOBALS['QXDREAM']['qx_login_time']  = format_date('Y-m-d H:i:s', $data['login_time']);
				$GLOBALS['QXDREAM']['qx_company_uid'] = $data['company_uid'];
				$GLOBALS['QXDREAM']['qx_role']        = get_role_name($data['group_id']); //��Ա��ɫ
			} else {
				$GLOBALS['QXDREAM']['qx_user_id']     = 0;
				$GLOBALS['QXDREAM']['qx_group_id']    = -100; //�������ж�
				$GLOBALS['QXDREAM']['qx_company_id']  = -100;
				$GLOBALS['QXDREAM']['qx_mr_ids']      = '';
				set_cookie(array('hash', 'qx_auth'), array('', ''));
			}
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��⹫˾�Ƿ񱻽��� 
	+-----------------------------------------------------------------------
	* @company_id  int     ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ       boolen  �����÷����棬���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_company_disabled($company_data) {
		return !empty($company_data['disabled']) ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����û��Ƿ��ܵ�¼��̨
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function check_admin() {
		$this->check_user();
		//���Ӧ����Ҳ�к�̨����
		$entry = defined('IN_ADMIN') && APP_PATH != ADMIN_PATH ? str_replace(array('.', '/'), '', ADMIN_PATH) : '';
		if(empty($GLOBALS['QXDREAM']['qx_user_id']) || !isset($_SESSION['is_admin']) || !$this->check_group($GLOBALS['QXDREAM']['qx_group_id'])) { //û�е�¼��״̬
			$_GET['control'] != 'login' && redirect(app_url($entry) . 'login/'); //����������login�ų�����ת�Ի���
		} else {
			$_GET['control'] == 'login' && $_GET['method'] != 'logout' && redirect(app_url($entry));
			//��ֹ�û����ù��̵�ʱ��,��ɲ��ܵ�½��̨,��������5����ʱ,��ʱʱ��Ϊ5����
			$overtime = OVERTIME;
			if(OVERTIME > 0 && OVERTIME < 300) $overtime = 300;
			if(OVERTIME > 0) $this->check_admin_ontime($overtime); //��¼��ʱ
		}
		if(!$this->check_priv()) { show_msg('maybe_permission_limit', 'stay', 1); }
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����û�������
	+-----------------------------------------------------------------------
	* @cur_groupd_id  int      ��ǰ��ID
	* @max_group_id   int      ϵͳ�����ID
	+-----------------------------------------------------------------------
	* ����ֵ          boolen   �������ڿɲ�����Χ�ڷ����棬���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_group($cur_group_id) {
		return $cur_group_id <= 0 ? FALSE : TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����û�Ȩ��
	+-----------------------------------------------------------------------
	* ����            ��
	+-----------------------------------------------------------------------
	* ����ֵ          boolen   ��Ȩ�޷����棬���򷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_priv() {
		//mr_model����ģ���е�Ȩ������,��model_id,������get��model_id���ֿ�
		$control = isset($_GET['mr_model']) ? $GLOBALS['QXDREAM']['MODEL'][$_GET['mr_model']]['model_name'] : $_GET['control']; //ģ�ͺ�ģ��
		if(isset($GLOBALS['QXDREAM']['MR'][$control]) && 1 == $GLOBALS['QXDREAM']['MR'][$control]['disabled']) { //ģ�鱻����
			add_globals(array('mod' => $control));
			show_msg('module_unenabled');
		}
		//��������Ա
		if(isset($GLOBALS['QXDREAM']['qx_group_id']) && 1 == $GLOBALS['QXDREAM']['qx_group_id']) { return TRUE; }
		//�������е�
		if(in_array($control, array('login', 'index', 'attachment'))) { return TRUE; }
		//��д��ģ����Դ��,ֻ��˾����Ա�ɲ�����
		if(2 == $GLOBALS['QXDREAM']['qx_group_id'] && in_array($control, array('cache', 'setting', 'group', 'category', 'contentAll'))) { return TRUE; }
		if(isset($GLOBALS['QXDREAM']['MR'][$control]) && $GLOBALS['QXDREAM']['qx_company_id'] >= 1) { //����ģ����Դ�Ĳſ�ʹ��
			//��˾����Ա�ӹ�˾���ж�mr_ids,����Ӹù�˾���Ӧ���û����ж�mr_ids
			$mr_ids = 2 == $GLOBALS['QXDREAM']['qx_group_id'] ? $GLOBALS['QXDREAM']['COMPANY'][$GLOBALS['QXDREAM']['qx_company_id']]['mr_ids'] : $GLOBALS['QXDREAM']['USER_GROUP'][$GLOBALS['QXDREAM']['qx_group_id']]['mr_ids'];
			$mr_ids_data = explode(',', $mr_ids );
			//�жϵ�ǰ������ģ����ԴID�Ƿ�ù�˾��ɫ��ӵ�е�ģ����ԴID
			if(in_array($GLOBALS['QXDREAM']['MR'][$control]['mr_id'], $mr_ids_data)) { return TRUE; }
			//�û���������,���������޸�������user��
			if('user' == $control && 'edit' == $_GET['method']) { return TRUE; }
		}
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �û���¼��ʱʱ��(��)
	* ÿ��ˢ����SESSION��ҳ��ʱ,�������$online_time��ֵ,��$_SESSION['ontime'] = mktime();
	+-----------------------------------------------------------------------
	* @long int  ��¼��ʱ��ʱ��
	+-----------------------------------------------------------------------
	* ����ֵ     ��
	+-----------------------------------------------------------------------
	*/
	public function check_admin_ontime($long = 3600) {
		$timestamp = $GLOBALS['QXDREAM']['timestamp'];
		$online_time = $_SESSION['ontime']; //�û���½�ѳ����¼session
		if ($timestamp - $online_time > $long) { //3600��
			session_destroy();
			show_msg('OT', app_url() . '/login/');
		} else {
			$_SESSION['ontime'] =  $timestamp;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* �û���¼
	+-----------------------------------------------------------------------
	* @user_name       string              �û���
	* @user_pass       string              �û�����
	* @id_code         string              ��֤��
	* @enable_id_code  boolen              �Ƿ�������֤��
	* @cookie_time     int                 cookie����ʱ��(Ϊ0ʱ,�ر��������ʧЧ)
	+-----------------------------------------------------------------------
	* ����ֵ           boolen or array     ��ȷ��¼�����û���Ϣ(����),���󷵻ؼ�
	+-----------------------------------------------------------------------
	*/
	public function login($user_name, $user_pass, $id_code, $enable_id_code = TRUE, $cookie_time = 0) {
		$data = '';
		$sql = "SELECT `user_id`,`company_id`,`user_name`,`user_pass`,`salt`,`group_id`,`login_count`,`disabled` FROM {$this->user_table} WHERE `user_name`='{$user_name}'";
		$data = $this->fetch($sql);
		if(!is_array($data)) {
			$this->msg = 'uncorrect_user_name';
			$this->error_type = 1;
			return FALSE;
		}
		$create_pass = $this->create_pass($user_pass, $data['salt']);
		if($data['user_pass'] != $create_pass) {
			$this->msg = 'uncorrect_user_pass';
			$this->error_type = 2;
			return FALSE;
		}
		if(isset($data['id_code']) && !check_code($id_code, $enable_id_code)) {
			$this->msg = 'uncorrect_id_code';
			$this->error_type = 3;
			return FALSE;
		}
		$user_id     = $data['user_id'];
		$group_id    = $data['group_id'];
		$login_count = $data['login_count'] + 1; //��¼������1
		$hash = md5(md5(QX_KEY) . $user_id . $group_id . $user_name);
		$time = 0;
		$time = get_cookie('cookie_time');
		$cookie_time = empty($time) ? $cookie_time : $time;
		set_cookie('hash', auth_code($hash), $cookie_time);
		set_cookie('qx_auth', auth_code("{$user_id}\t{$create_pass}\t{$login_count}"), $cookie_time);
		set_cookie('cookie_time', $cookie_time);
		$this->query("UPDATE {$this->user_table} SET `login_count`='{$login_count}',`login_ip`='{$GLOBALS['QXDREAM']['online_ip']}',`login_time`='{$GLOBALS['QXDREAM']['timestamp']}' WHERE `user_id`='{$user_id}'");
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �������븽������ִ�--���ڻ�Ա����md5(md5($password).$salt)
	+-----------------------------------------------------------------------
	* @num     int     λ��
	+-----------------------------------------------------------------------
	* ����ֵ   string  ���븽������ִ�
	+-----------------------------------------------------------------------
	*/
	private function salt($num = 4) {
		if($num < 4 || $num > 16) $num = 4;
		return substr(uniqid(rand()), -$num);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���ɼ�������
	+-----------------------------------------------------------------------
	* @input_password string   �ⲿ�ύ����
	* @salt           string   ���ݿ���������ִ�
	+-----------------------------------------------------------------------
	* ����ֵ          string   ���ɺ������
	+-----------------------------------------------------------------------
	*/
	public function create_pass($input_pass, $salt) {
		return md5(md5($input_pass) . $salt);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �û��˳���¼
	+-----------------------------------------------------------------------
	* ����ֵ   boolen  �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function logout() {
		if(isset($_SESSION)) session_destroy();
		clear_cookie(array('hash', 'qx_auth', 'cookie_time'));
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����û�
	+-----------------------------------------------------------------------
	* @data            array    ������˾����
	* |--@user_name       string  �û���
	* |--@user_pass       string  ����
	* |--@password_again  string  ����ȷ��
	* |--@company_id      int     ��˾ID
	* |--@company_uid     int     ��˾Ӣ��ID
	* |--@group_id        int     �û���
	* |----------------------------------------------
	+-----------------------------------------------------------------------
	* ����ֵ           boolen   �ɹ�������Դ,���ɹ����ؼ�
	+-----------------------------------------------------------------------
	*/
	public function add($data) {
		$data['salt']       = $this->salt();
		$data['user_pass']  = $this->create_pass($data['user_pass'], $data['salt']);
		unset($data['password_again']);
		return $this->insert($this->user_table, $data);
	}
	
	/**
	+-----------------------------------------------------------------------
	* �༭�û�
	+-----------------------------------------------------------------------
	* @data            array    ������˾����
	* |--@user_name       string  �û���
	* |--@user_pass       string  ����
	* |--@password_again  string  ����ȷ��
	* |--@company_id      int     ��˾ID
	* |--@company_uid     int     ��˾Ӣ��ID
	* |--@group_id        int     �û���
	* |----------------------------------------------
	* @user_id            int     �û�ID
	* @company_id         int     ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ           boolen     �ɹ�������Դ,���ɹ����ؼ�
	+-----------------------------------------------------------------------
	*/
	public function edit($data, $user_id, $company_id = 0) {
		if(!empty($data['user_pass']) && !empty($data['password_again'])) {
			$data['salt']       = $this->salt();
			$data['user_pass']  = $this->create_pass($data['user_pass'], $data['salt']);
		} else {
			unset($data['user_pass']);
		}
		unset($data['password_again']);
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		return $this->update($this->user_table, $data, "user_id='" . $user_id . "'" . $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��֤Ҫ���ӵ��û�
	+-----------------------------------------------------------------------
	* @data            array    �����û�����
	* |--@user_name       string  �û���
	* |--@user_pass       string  ����
	* |--@password_again  string  ����ȷ��
	* |--@id_code         string  ��֤��
	* |----------------------------------------------
	* @enable_id_code  boolen  �Ƿ�����֤��
	+-----------------------------------------------------------------------
	* ����ֵ           boolen  ûͨ����֤���ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_addinfo($data, $enable_id_code = FALSE) {
		if(check_badword($data['user_name'])) {
			$this->msg = 'user_name_has_badword';
			return FALSE;
		}
		if(strlen($data['user_name']) > 30) {
			$this->msg = 'user_name_not_beyond_30_len';
			return FALSE;
		}
		$user_data = '';
		$sql = "SELECT `user_id` FROM {$this->user_table} WHERE `user_name`='{$data['user_name']}'";
		$user_data = $this->fetch($sql);
		if(is_array($user_data)) {
			$this->msg = 'user_name_has_used';
			return FALSE;
		}
		if(!empty($data['user_pass']) && !empty($data['password_again'])) {
			if(check_badword($data['user_pass'], array('"',"'"))) {
				$this->msg = 'user_pass_has_badword';
				return FALSE;
			}
			if($data['user_pass'] != $data['password_again']) {
				$this->msg = 'password_not_same';
				return FALSE;
			}
		}
		if(isset($data['id_code']) && !check_code($data['id_code'], $enable_id_code)) {
			$this->msg = 'uncorrect_id_code';
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��֤Ҫ�༭���û�
	+-----------------------------------------------------------------------
	* @data            array    ������˾����
	* |--@user_name       string  �û���
	* |--@user_pass       string  ����
	* |--@password_again  string  ����ȷ��
	* |----------------------------------------------
	+-----------------------------------------------------------------------
	* ����ֵ           boolen     ûͨ����֤���ؼ�
	+-----------------------------------------------------------------------
	*/
	public function check_editinfo($data) {
		if(check_badword($data['user_pass'], array('"',"'"))) {
			$this->msg = 'user_pass_has_badword';
			return FALSE;
		}
		if($data['user_pass'] != $data['password_again']) {
			$this->msg = 'password_not_same';
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���һ�Ա��ϸ��¼
	+-----------------------------------------------------------------------
	* @user_id       int     �û�ID
	* @company_id    int     ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ         boolen  
	+-----------------------------------------------------------------------
	*/
	public function get_one($user_id, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		return $this->fetch("SELECT `user_id`,`user_name`,`company_id`,`user_pass`,`salt`,`group_id`,`login_count`,`login_ip`,`login_time`,`content_count`,`disabled` FROM `{$this -> user_table}` WHERE `user_id`='{$user_id}'" . $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��Ա�б�
	+-----------------------------------------------------------------------
	* @company_id   int      ��˾ID
	* @disable      string   ���������ֵ��Ĭ��all��ʾ����
	+-----------------------------------------------------------------------
	* ����ֵ        array    ��Ա����
	+-----------------------------------------------------------------------
	*/
	public function list_info($company_id = NULL, $disable = 'all') {
		$where = '';
		$has_where = FALSE;
		if(NULL !== $company_id) { $has_where = append_where($where, $has_where, "company_id='{$company_id}'"); }
		if('all' != $disable) { $has_where = append_where($where, $has_where, "disabled='{$disable}'"); }
		$this->count("SELECT COUNT(*) FROM " . $this->user_table . $where);
		$sql = "SELECT `user_id`,`user_name`,`company_id`,`group_id`,`login_count`,`login_ip`,`login_time`,`content_count`,`disabled` FROM `{$this->user_table}`{$where} ORDER BY `user_id` DESC" . $this->pagenation->sql_limit();
		$query = $this->query($sql, 'unbuffered');
		$data = array();
		while($row = $this->fetch_array($query)) {
			$row['role']           = get_role_name($row['group_id']);
			$row['company_name']   = isset($GLOBALS['QXDREAM']['COMPANY'][$row['company_id']]['company_name']) ? $GLOBALS['QXDREAM']['COMPANY'][$row['company_id']]['company_name'] : $GLOBALS['QXDREAM']['admin_language']['none'];
			$row['login_ip']       = empty($row['login_ip']) ? $GLOBALS['QXDREAM']['language']['never_login'] : $row['login_ip'];
			$row['login_time']     = empty($row['login_time']) ? $GLOBALS['QXDREAM']['language']['never_login'] : format_date('Y-m-d H:iA', $row['login_time']);
			unset($row['group_id']);
			$data[] = $row;
		}
		$this->free_result($query);
		return $data;
	}
	
	/**
	+-----------------------------------------------------------------------
	* �����������û�
	+-----------------------------------------------------------------------
	* @user_id         int     Ҫ���õ��û�ID
	* @disabled_value  int     ����Ϊ0,����Ϊ1
	* @company_id      int     ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ           boolen  �ɹ�������
	+-----------------------------------------------------------------------
	*/
	public function disable($user_id, $disabled_value, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$this->update($this->user_table, array('disabled' => $disabled_value), "user_id='" . $user_id . "'" . $where);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ���������������û�
	+-----------------------------------------------------------------------
	* @user_id_arr     array  Ҫ�������û�ID
	* @disabled_value  int    ����Ϊ0,����Ϊ1
	* @company_id      int    ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ                  ִ�в���Ӱ���д���1������,����Ϊ��
	+-----------------------------------------------------------------------
	*/
	public function batch_disable($user_id_arr, $disabled_value, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$user_id_all = implode(',', $user_id_arr);
		$this->update("{$this->user_table}", array('disabled' => $disabled_value), "user_id IN({$user_id_all})" . $where);
		return $this->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ɾ����Ա
	+-----------------------------------------------------------------------
	* @user_id         int      Ҫɾ�����û�ID
	* @company_id      int      ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ           boolen   ִ��ɾ��Ӱ���д���1������,����Ϊ��
	+-----------------------------------------------------------------------
	*/
	public function remove($user_id, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$this->delete("{$this->user_table}", "user_id='{$user_id}'" . $where);
		return $this->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ����ɾ����Ա
	+-----------------------------------------------------------------------
	* @user_id_arr   array    Ҫɾ�����û�ID
	* @company_id    int      ��˾ID(ֻ���ǹ�˾�˻����������)
	+-----------------------------------------------------------------------
	* ����ֵ         boolen   ִ��ɾ��Ӱ���д���1������,����Ϊ��
	+-----------------------------------------------------------------------
	*/
	public function batch_remove($user_id_arr, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$user_id_all = implode(',', $user_id_arr);
		$this->delete("{$this->user_table}", "user_id IN({$user_id_all})" . $where);
		return $this->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* ��ȡ��Ա����������
	+-----------------------------------------------------------------------
	* @company_id  int     ��˾ID
	+-----------------------------------------------------------------------
	* ����ֵ       array   ����
	+-----------------------------------------------------------------------
	*/
	public function get_guide($company_id = 0) {
		$where = empty($company_id) ? '' : " WHERE company_id='" . $company_id . "'";
		$sql = "SELECT COUNT(*) AS count,disabled AS link_val FROM $this->user_table{$where} GROUP BY disabled ORDER BY user_id";
		$query = $this->query($sql, 'unbuffered');
		$data = array();
		$total = 0;
		$data['all']['link_val'] = 'all';
		$data['all']['text'] = $GLOBALS['QXDREAM']['admin_language']['all'];
		while($row = $this->fetch_array($query)) {
			$row['text'] = 0 == $row['link_val'] ? $GLOBALS['QXDREAM']['admin_language']['enable'] : $GLOBALS['QXDREAM']['admin_language']['disable']; //��ʾ����;
			$total += $row['count'];
			$data[$row['link_val']] = $row;
		}
		$data['all']['count'] = $total;
		return $data;
	}
}
?>