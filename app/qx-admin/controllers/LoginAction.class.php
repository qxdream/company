<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-02-24 ��̨��¼������ $
	@version  $Id: LoginAction.class.php 1.0 2011-04-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class LoginAction extends ShareAction {
	public $prompt; //��ʾ��Ϣ
	public $prompt_id; //��ʾ��Ϣ��ǩID
	private $enable_id_code = FALSE;
	
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
		parent::_initialize();
		$this->safe_time = load_class('SafeTimes');
		$this->safe_time->set('login');
		$this->enable_id_code = $this->safe_time->get_times() > 0 ? TRUE : FALSE; //�Ƿ�������֤��
	}
	/**
	+-----------------------------------------------------------------------
	* ��ʼ�������¼
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
	+-----------------------------------------------------------------------
	*/
	public function index() {
		if(isset($_POST['btn_submit'])) {
			$result = '';
			$result = $this->safe_time->check(); //�ж��ڹ涨ʱ���ڱ����Ƶ�¼��IP
			if(is_array($result)) {
				add_globals(array('lang_minute' => format_timespan($result['minute']))); //ʣ�����ʱ��ɵ�¼������
				$this->prompt = lang('login_limit');
				$this->prompt_id = 'login_prompt_error';
				$this->show_error();
			}
			
			if(!filled_out($_POST, 'user_pass')) {
				$this->prompt = lang('filled_out');
				$this->prompt_id = 'login_prompt_error';
				if(!trim($_POST['user_name'])) { //�û���Ϊ��
					$not_filled = 1;
				} elseif(!$_POST['user_pass']) { //����Ϊ��
					$not_filled = 2;
				} elseif(!$_POST['id_code']) { //��֤��Ϊ��
					$not_filled = 3;
				}
				$this->view->assign('not_filled', $not_filled);
				$this->show_error();
			}
			
			$user_name = trim($_POST['user_name']); //���������gbk,�����û���Ҫת��gbk��,��ȻAJAX���������,��ʹ�û�����Ч
			$user_pass = $_POST['user_pass'];
			$id_code   = isset($_POST['id_code']) ? trim($_POST['id_code']) : '';
			
			$result = '';
			//���Ҫ����COOKIE����һ��Сʱ��ʱ�������Ϊ$timestamp + 3600
			$result = $this->user->login($user_name, $user_pass, $id_code, $this->enable_id_code);
			//��˾������
			if(is_array($result)) { //��ȷ��¼
				if($result['group_id'] > 1 && $this->user->check_company_disabled($GLOBALS['QXDREAM']['COMPANY'][$result['company_id']])) {
					$this->prompt = lang('company_disabled');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
				//�˺ű�����
				if(!empty($result['disabled'])) {
					$this->prompt = lang('account_disabled');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
				//�û��鳬��Χ,û�е�¼��̨Ȩ��
				if(!$this->user->check_group($result['group_id'])) {
					$this->prompt = lang('is_not_admin');
					$this->prompt_id = 'login_prompt_error';
					$this->user->logout();
					$this->show_error();
				}
			} else { //�����¼
				$times = $this->safe_time->add();
				if($times > 0) {
					add_globals(array('times' => $times));
					$try_times = lang('try_times');
				} else { //��Чʱ���ڵ�¼ʣ�������
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
			
			//ȫ����֤ͨ��,�������Աsession
			$_SESSION['is_admin'] = 'qx_admin';
			if(OVERTIME > 0) $_SESSION['ontime'] = $GLOBALS['QXDREAM']['timestamp'];
			redirect(app_url() . 'index/');
		}
		$this->view->assign('error_type', 0); //��ʼerror_type��û�е�,���Դ�����Ϣ����ʾ
		$this->display();
	}
	/**
	+-----------------------------------------------------------------------
	* �ǳ�
	+-----------------------------------------------------------------------
	* ����     ��
	+-----------------------------------------------------------------------
	* ����ֵ   ��
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
	* ��ʾ������Ϣ
	+-----------------------------------------------------------------------
	* @error_type   int   ��������(0Ϊ��¼ʱ����)
	+-----------------------------------------------------------------------
	* ����ֵ              ��
	+-----------------------------------------------------------------------
	*/
	private function show_error($error_type = 0) {
		$this->view->assign('error_type', $error_type);
		$this->view->assign('prompt', $this->prompt);
		$this->view->assign('prompt_id', $this->prompt_id);
		if(!$this->enable_id_code) {
			$this->enable_id_code = $this->safe_time->get_times() > 0 ? TRUE : FALSE; //�Ƿ�������֤��
		}
		$this->display();
		exit();
	}
	/**
	+-----------------------------------------------------------------------
	* ��ʾ����
	+-----------------------------------------------------------------------
	* @error_type   int   ��������(0Ϊ��¼ʱ����1Ϊ�ǳ�ʱ��ʾ)
	+-----------------------------------------------------------------------
	* ����ֵ              ��
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