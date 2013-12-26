<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-02-24 后台登录控制器 $
	@version  $Id: LoginAction.class.php 1.0 2011-04-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class LoginAction extends ShareAction {
	public $prompt; //提示信息
	public $prompt_id; //提示信息标签ID
	private $enable_id_code = FALSE;
	
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
		parent::_initialize();
		$this->safe_time = load_class('SafeTimes');
		$this->safe_time->set('login');
		$this->enable_id_code = $this->safe_time->get_times() > 0 ? TRUE : FALSE; //是否启用验证码
	}
	/**
	+-----------------------------------------------------------------------
	* 初始载入与登录
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function index() {
		if(isset($_POST['btn_submit'])) {
			$result = '';
			$result = $this->safe_time->check(); //判断在规定时间内被限制登录的IP
			if(is_array($result)) {
				add_globals(array('lang_minute' => format_timespan($result['minute']))); //剩余多少时间可登录的语言
				$this->prompt = lang('login_limit');
				$this->prompt_id = 'login_prompt_error';
				$this->show_error();
			}
			
			if(!filled_out($_POST, 'user_pass')) {
				$this->prompt = lang('filled_out');
				$this->prompt_id = 'login_prompt_error';
				if(!trim($_POST['user_name'])) { //用户名为空
					$not_filled = 1;
				} elseif(!$_POST['user_pass']) { //密码为空
					$not_filled = 2;
				} elseif(!$_POST['id_code']) { //验证码为空
					$not_filled = 3;
				}
				$this->view->assign('not_filled', $not_filled);
				$this->show_error();
			}
			
			$user_name = trim($_POST['user_name']); //编码如果是gbk,中文用户名要转成gbk的,不然AJAX传输会乱码,致使用户名无效
			$user_pass = $_POST['user_pass'];
			$id_code   = isset($_POST['id_code']) ? trim($_POST['id_code']) : '';
			
			$result = '';
			//如果要设置COOKIE保存一个小时，时间参数就为$timestamp + 3600
			$result = $this->user->login($user_name, $user_pass, $id_code, $this->enable_id_code);
			//公司被禁用
			if(is_array($result)) { //正确登录
				if($result['group_id'] > 1 && $this->user->check_company_disabled($GLOBALS['QXDREAM']['COMPANY'][$result['company_id']])) {
					$this->prompt = lang('company_disabled');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
				//账号被禁用
				if(!empty($result['disabled'])) {
					$this->prompt = lang('account_disabled');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
				//用户组超范围,没有登录后台权限
				if(!$this->user->check_group($result['group_id'])) {
					$this->prompt = lang('is_not_admin');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
			} else { //错误登录
				$times = $this->safe_time->add();
				if($times > 0) {
					add_globals(array('times' => $times));
					$try_times = lang('try_times');
				} else { //有效时间内登录剩余次数０
					$time = format_timespan(LOGIN_INTERVAL_TIME);
					add_globals(array('time' => $time));
					$try_times = lang('login_fail_limit');
				}
				$this->prompt = lang($this->user->msg) . $try_times;
				$this->prompt_id = 'login_prompt_error';
				unset($times, $try_times);
				$this->show_error($this->user->error_type);
			}
			$this->safe_time->remove();
			unset($result);
			
			//全部验证通过,给予管理员session
			$_SESSION['is_admin'] = 'qx_admin';
			if(OVERTIME > 0) $_SESSION['ontime'] = $GLOBALS['QXDREAM']['timestamp'];
			redirect(app_url() . 'index/');
		}
		$this->view->assign('error_type', 0); //初始error_type是没有的,所以错误信息不显示
		$this->display();
	}
	/**
	+-----------------------------------------------------------------------
	* 登出
	+-----------------------------------------------------------------------
	* 参数     无
	+-----------------------------------------------------------------------
	* 返回值   无
	+-----------------------------------------------------------------------
	*/
	public function logout() {
		$this->user->logout(1);
		$this->view->assign('error_type', 0);
		$this->prompt = lang('been_logouted');
		$this->prompt_id = 'login_prompt_normal';
		$this->show_error();
		$this->display(1);
	}
	/**
	+-----------------------------------------------------------------------
	* 显示错误信息
	+-----------------------------------------------------------------------
	* @error_type   int   错误类型(0为登录时错误)
	+-----------------------------------------------------------------------
	* 返回值              无
	+-----------------------------------------------------------------------
	*/
	private function show_error($error_type = 0) {
		$this->view->assign('error_type', $error_type);
		$this->view->assign('prompt', $this->prompt);
		$this->view->assign('prompt_id', $this->prompt_id);
		if(!$this->enable_id_code) {
			$this->enable_id_code = $this->safe_time->get_times() > 0 ? TRUE : FALSE; //是否启用验证码
		}
		$this->display();
		exit();
	}
	/**
	+-----------------------------------------------------------------------
	* 显示界面
	+-----------------------------------------------------------------------
	* @error_type   int   错误类型(0为登录时错误，1为登出时提示)
	+-----------------------------------------------------------------------
	* 返回值              无
	+-----------------------------------------------------------------------
	*/
	public function display($type = 0) {
		$page_title = 0 == $type ? $GLOBALS['QXDREAM']['admin_language']['login'] : $GLOBALS['QXDREAM']['admin_language']['quited'];
		$this->enable_id_code;
		$this->view->assign('enable_id_code', $this->enable_id_code);
		$this->view->assign('page_title', $page_title);
		$this->view->display('login');
	}
}
?>