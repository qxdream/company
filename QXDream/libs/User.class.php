<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-02-24 用户模型 $
	@version  $Id: User.class.php 1.2 2011-04-30
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class User extends Model {

	public $user_table;       //会员表
	public $total = 0;        //记录总数(使用在分页之中)
	public $error_type = 0;   //登录错误方式,1为用户无,2为密码错,3为验证码错
	
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
	* 设置数据
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function set() {
		$this->user_table = DB_PRE . 'user';
	}
	
	/**
	+-----------------------------------------------------------------------
	* 检测用户是否登录
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function check_user() {
		$qx_auth = get_cookie('qx_auth');
		$hash = get_cookie('hash');
		if(!empty($qx_auth)) { //登录状态就要判断
			list($GLOBALS['QXDREAM']['qx_user_id'], $qx_user_pass, $GLOBALS['QXDREAM']['qx_login_count']) = explode("\t", auth_code($qx_auth, 'DECODE'));
			$data = '';
			$data = $this->fetch("SELECT `user_id`,`company_id`,`company_uid`,`user_pass`,`user_name`,`login_ip`,`login_time`,`group_id`,`salt`,`disabled` FROM `{$this->user_table}` WHERE `user_id`='" . $GLOBALS['QXDREAM']['qx_user_id'] . "'");
			if(is_array($data) && auth_code($hash, 'DECODE') == md5(md5(QX_KEY) . $data['user_id'] . $data['group_id'] .$data['user_name']) && $data['user_pass'] == $qx_user_pass) {
				if($data['group_id'] > 1) {
					$company_data = $GLOBALS['QXDREAM']['COMPANY'][$data['company_id']];
					$this->check_company_disabled($company_data) && show_msg('company_disabled'); //公司被禁用
					$GLOBALS['QXDREAM']['qx_company_id']  = $data['company_id']; //不为空时，说明是公司登录
					$GLOBALS['QXDREAM']['qx_mr_ids']      = $company_data['mr_ids']; //公司模组
					unset($company_data);
					$GLOBALS['QXDREAM']['USER_GROUP']     = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_user_group'); //当前管理员或公司用户角色
					$GLOBALS['QXDREAM']['CATEGORY']       = cache_read($GLOBALS['QXDREAM']['qx_company_id'] . '_category');
					define('VIEW_COMPANY_ROOT', QX_ROOT . APP_DIR . 'company/views/' . $data['company_uid'] . '/'); //前台视图目录
					define('VIEW_COMPANY_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . $data['company_uid'] . '/css/'); //前台视图CSS
				} else {
					$GLOBALS['QXDREAM']['qx_company_id']  = 0;
					$GLOBALS['QXDREAM']['USER_GROUP']     = cache_read('user_group'); 
				}
				$GLOBALS['QXDREAM']['qx_user_name']   = $data['user_name'];
				$GLOBALS['QXDREAM']['qx_group_id']    = $data['group_id']; //会员组
				$GLOBALS['QXDREAM']['qx_disabled']    = $data['disabled']; //是否禁用
				$GLOBALS['QXDREAM']['qx_login_ip']    = $data['login_ip'];
				$GLOBALS['QXDREAM']['qx_login_time']  = format_date('Y-m-d H:i:s', $data['login_time']);
				$GLOBALS['QXDREAM']['qx_company_uid'] = $data['company_uid'];
				$GLOBALS['QXDREAM']['qx_role']        = get_role_name($data['group_id']); //会员角色
			} else {
				$GLOBALS['QXDREAM']['qx_user_id']     = 0;
				$GLOBALS['QXDREAM']['qx_group_id']    = -100; //增加组判断
				$GLOBALS['QXDREAM']['qx_company_id']  = -100;
				$GLOBALS['QXDREAM']['qx_mr_ids']      = '';
				set_cookie(array('hash', 'qx_auth'), array('', ''));
			}
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 检测公司是否被禁用 
	+-----------------------------------------------------------------------
	* @company_id  int     公司ID
	+-----------------------------------------------------------------------
	* 返回值       boolen  被禁用返回真，否则返回假
	+-----------------------------------------------------------------------
	*/
	public function check_company_disabled($company_data) {
		return !empty($company_data['disabled']) ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 检测用户是否能登录后台
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function check_admin() {
		$this->check_user();
		//其的应用里也有后台程序
		$entry = defined('IN_ADMIN') && APP_PATH != ADMIN_PATH ? str_replace(array('.', '/'), '', ADMIN_PATH) : '';
		if(empty($GLOBALS['QXDREAM']['qx_user_id']) || !isset($_SESSION['is_admin']) || !$this->check_group($GLOBALS['QXDREAM']['qx_group_id'])) { //没有登录的状态
			$_GET['control'] != 'login' && redirect(app_url($entry) . 'login/'); //控制器不是login才出现跳转对话框
		} else {
			$_GET['control'] == 'login' && $_GET['method'] != 'logout' && redirect(app_url($entry));
			//防止用户设置过短的时间,造成不能登陆后台,设置少于5分钟时,超时时间为5分钟
			$overtime = OVERTIME;
			if(OVERTIME > 0 && OVERTIME < 300) $overtime = 300;
			if(OVERTIME > 0) $this->check_admin_ontime($overtime); //登录超时
		}
		if(!$this->check_priv()) { show_msg('maybe_permission_limit', 'stay', 1); }
	}
	
	/**
	+-----------------------------------------------------------------------
	* 检测用户所在组
	+-----------------------------------------------------------------------
	* @cur_groupd_id  int      当前组ID
	* @max_group_id   int      系统最大组ID
	+-----------------------------------------------------------------------
	* 返回值          boolen   当组组在可操作范围内返回真，否则返回假
	+-----------------------------------------------------------------------
	*/
	public function check_group($cur_group_id) {
		return $cur_group_id <= 0 ? FALSE : TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 检测用户权限
	+-----------------------------------------------------------------------
	* 参数            无
	+-----------------------------------------------------------------------
	* 返回值          boolen   有权限返回真，否则返回假
	+-----------------------------------------------------------------------
	*/
	public function check_priv() {
		//mr_model用于模型中的权限限制,即model_id,但需与get的model_id区分开
		$control = isset($_GET['mr_model']) ? $GLOBALS['QXDREAM']['MODEL'][$_GET['mr_model']]['model_name'] : $_GET['control']; //模型和模组
		if(isset($GLOBALS['QXDREAM']['MR'][$control]) && 1 == $GLOBALS['QXDREAM']['MR'][$control]['disabled']) { //模组被禁用
			add_globals(array('mod' => $control));
			show_msg('module_unenabled');
		}
		//超级管理员
		if(isset($GLOBALS['QXDREAM']['qx_group_id']) && 1 == $GLOBALS['QXDREAM']['qx_group_id']) { return TRUE; }
		//适用所有的
		if(in_array($control, array('login', 'index', 'attachment'))) { return TRUE; }
		//不写入模组资源表,只公司管理员可操作的
		if(2 == $GLOBALS['QXDREAM']['qx_group_id'] && in_array($control, array('cache', 'setting', 'group', 'category', 'contentAll'))) { return TRUE; }
		if(isset($GLOBALS['QXDREAM']['MR'][$control]) && $GLOBALS['QXDREAM']['qx_company_id'] >= 1) { //导入模组资源的才可使用
			//公司管理员从公司表中读mr_ids,否则从该公司相对应的用户组中读mr_ids
			$mr_ids = 2 == $GLOBALS['QXDREAM']['qx_group_id'] ? $GLOBALS['QXDREAM']['COMPANY'][$GLOBALS['QXDREAM']['qx_company_id']]['mr_ids'] : $GLOBALS['QXDREAM']['USER_GROUP'][$GLOBALS['QXDREAM']['qx_group_id']]['mr_ids'];
			$mr_ids_data = explode(',', $mr_ids );
			//判断当前控制器模组资源ID是否该公司角色所拥有的模组资源ID
			if(in_array($GLOBALS['QXDREAM']['MR'][$control]['mr_id'], $mr_ids_data)) { return TRUE; }
			//用户管理另处理,个人密码修改密码在user里
			if('user' == $control && 'edit' == $_GET['method']) { return TRUE; }
		}
		return FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 用户登录超时时间(秒)
	* 每次刷新需SESSION的页面时,都会更更$online_time的值,即$_SESSION['ontime'] = mktime();
	+-----------------------------------------------------------------------
	* @long int  登录超时的时间
	+-----------------------------------------------------------------------
	* 返回值     无
	+-----------------------------------------------------------------------
	*/
	public function check_admin_ontime($long = 3600) {
		$timestamp = $GLOBALS['QXDREAM']['timestamp'];
		$online_time = $_SESSION['ontime']; //用户登陆把程序记录session
		if ($timestamp - $online_time > $long) { //3600秒
			session_destroy();
			show_msg('OT', app_url() . '/login/');
		} else {
			$_SESSION['ontime'] =  $timestamp;
		}
	}
	
	/**
	+-----------------------------------------------------------------------
	* 用户登录
	+-----------------------------------------------------------------------
	* @user_name       string              用户名
	* @user_pass       string              用户密码
	* @id_code         string              验证码
	* @enable_id_code  boolen              是否启用验证码
	* @cookie_time     int                 cookie保存时间(为0时,关闭浏览器就失效)
	+-----------------------------------------------------------------------
	* 返回值           boolen or array     正确登录返回用户信息(数组),错误返回假
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
		$login_count = $data['login_count'] + 1; //登录次数加1
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
	* 生成密码附带随机字串--用于会员密码md5(md5($password).$salt)
	+-----------------------------------------------------------------------
	* @num     int     位数
	+-----------------------------------------------------------------------
	* 返回值   string  密码附带随机字串
	+-----------------------------------------------------------------------
	*/
	private function salt($num = 4) {
		if($num < 4 || $num > 16) $num = 4;
		return substr(uniqid(rand()), -$num);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 生成加密密码
	+-----------------------------------------------------------------------
	* @input_password string   外部提交密码
	* @salt           string   数据库里存的随机字串
	+-----------------------------------------------------------------------
	* 返回值          string   生成后的密码
	+-----------------------------------------------------------------------
	*/
	public function create_pass($input_pass, $salt) {
		return md5(md5($input_pass) . $salt);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 用户退出登录
	+-----------------------------------------------------------------------
	* 返回值   boolen  成功返回真
	+-----------------------------------------------------------------------
	*/
	public function logout() {
		if(isset($_SESSION)) session_destroy();
		clear_cookie(array('hash', 'qx_auth', 'cookie_time'));
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 增加用户
	+-----------------------------------------------------------------------
	* @data            array    新增公司数据
	* |--@user_name       string  用户名
	* |--@user_pass       string  密码
	* |--@password_again  string  密码确认
	* |--@company_id      int     公司ID
	* |--@company_uid     int     公司英文ID
	* |--@group_id        int     用户组
	* |----------------------------------------------
	+-----------------------------------------------------------------------
	* 返回值           boolen   成功返回资源,不成功返回假
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
	* 编辑用户
	+-----------------------------------------------------------------------
	* @data            array    新增公司数据
	* |--@user_name       string  用户名
	* |--@user_pass       string  密码
	* |--@password_again  string  密码确认
	* |--@company_id      int     公司ID
	* |--@company_uid     int     公司英文ID
	* |--@group_id        int     用户组
	* |----------------------------------------------
	* @user_id            int     用户ID
	* @company_id         int     公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值           boolen     成功返回资源,不成功返回假
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
	* 验证要增加的用户
	+-----------------------------------------------------------------------
	* @data            array    新增用户数据
	* |--@user_name       string  用户名
	* |--@user_pass       string  密码
	* |--@password_again  string  密码确认
	* |--@id_code         string  验证码
	* |----------------------------------------------
	* @enable_id_code  boolen  是否开启验证码
	+-----------------------------------------------------------------------
	* 返回值           boolen  没通过验证返回假
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
	* 验证要编辑的用户
	+-----------------------------------------------------------------------
	* @data            array    新增公司数据
	* |--@user_name       string  用户名
	* |--@user_pass       string  密码
	* |--@password_again  string  密码确认
	* |----------------------------------------------
	+-----------------------------------------------------------------------
	* 返回值           boolen     没通过验证返回假
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
	* 查找会员详细记录
	+-----------------------------------------------------------------------
	* @user_id       int     用户ID
	* @company_id    int     公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值         boolen  
	+-----------------------------------------------------------------------
	*/
	public function get_one($user_id, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		return $this->fetch("SELECT `user_id`,`user_name`,`company_id`,`user_pass`,`salt`,`group_id`,`login_count`,`login_ip`,`login_time`,`content_count`,`disabled` FROM `{$this -> user_table}` WHERE `user_id`='{$user_id}'" . $where);
	}
	
	/**
	+-----------------------------------------------------------------------
	* 会员列表
	+-----------------------------------------------------------------------
	* @company_id   int      公司ID
	* @disable      string   禁用与否用值，默认all显示所有
	+-----------------------------------------------------------------------
	* 返回值        array    会员数组
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
	* 禁用与启用用户
	+-----------------------------------------------------------------------
	* @user_id         int     要禁用的用户ID
	* @disabled_value  int     启用为0,禁用为1
	* @company_id      int     公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值           boolen  成功返回真
	+-----------------------------------------------------------------------
	*/
	public function disable($user_id, $disabled_value, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$this->update($this->user_table, array('disabled' => $disabled_value), "user_id='" . $user_id . "'" . $where);
		return TRUE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量禁用与启用用户
	+-----------------------------------------------------------------------
	* @user_id_arr     array  要操作的用户ID
	* @disabled_value  int    启用为0,禁用为1
	* @company_id      int    公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值                  执行操作影响行大于1返回真,否则为假
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
	* 删除会员
	+-----------------------------------------------------------------------
	* @user_id         int      要删除的用户ID
	* @company_id      int      公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值           boolen   执行删除影响行大于1返回真,否则为假
	+-----------------------------------------------------------------------
	*/
	public function remove($user_id, $company_id = 0) {
		$where = empty($company_id) ? '' : " AND company_id='" . $company_id . "'";
		$this->delete("{$this->user_table}", "user_id='{$user_id}'" . $where);
		return $this->affected_rows() > 0 ? TRUE : FALSE;
	}
	
	/**
	+-----------------------------------------------------------------------
	* 批量删除会员
	+-----------------------------------------------------------------------
	* @user_id_arr   array    要删除的用户ID
	* @company_id    int      公司ID(只有是公司账户才需加条件)
	+-----------------------------------------------------------------------
	* 返回值         boolen   执行删除影响行大于1返回真,否则为假
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
	* 获取会员数量向导数据
	+-----------------------------------------------------------------------
	* @company_id  int     公司ID
	+-----------------------------------------------------------------------
	* 返回值       array   数组
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
			$row['text'] = 0 == $row['link_val'] ? $GLOBALS['QXDREAM']['admin_language']['enable'] : $GLOBALS['QXDREAM']['admin_language']['disable']; //显示文字;
			$total += $row['count'];
			$data[$row['link_val']] = $row;
		}
		$data['all']['count'] = $total;
		return $data;
	}
}
?>