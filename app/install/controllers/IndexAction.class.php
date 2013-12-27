<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-21 ϵͳ��װ������ $
	@version  $Id: IndexAction.class.php 1.0 2011-05-22
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class IndexAction extends Controller {
	public function _initialize() {
		@set_time_limit(1000);
		$this->install_Wizard = '���ж��û���ҵ����ϵͳ(QXDream Mutiuser)��װ��';
		$this->view->assign('install_Wizard', $this->install_Wizard);
	}
	//��װЭ��
	public function index() {
		$this->view->assign('page_title', '��װЭ�� - ' . $this->install_Wizard);
		$this->view->assign('step', 1);
		$this->view->display('index');
	}
	//�������
	public function index_2() {
		$dev_pass = $dir_pass = TRUE;
		$development_data = $dir_privilege_data = array();
		$development_data[] = array('item' => '����ϵͳ', 'cur_development' => php_uname(), 'need_setting' => 'Windows_NT/Linux/Freebsd', 'pass' => TRUE);
		$development_data[] = array('item' => 'WEB������', 'cur_development' => $_SERVER['SERVER_SOFTWARE'], 'need_setting' => 'Apache/Nginx/IIS', 'pass' => TRUE);
		if(phpversion() > '5.2.0') {
			$development_data[] = array('item' => 'PHP�汾', 'cur_development' => 'PHP' . phpversion(), 'need_setting' => 'PHP5.2.0������', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'PHP�汾', 'cur_development' => 'PHP' . phpversion(), 'need_setting' => 'PHP5.2.0������', 'pass' => FALSE);
			$dev_pass = FALSE;
		}
		if(extension_loaded('mysql')) {
			$development_data[] = array('item' => 'MYSQL��չ', 'cur_development' => '��', 'need_setting' => '���뿪��', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'MYSQL��չ', 'cur_development' => '��', 'need_setting' => '���뿪��', 'pass' => FALSE);
			$dev_pass = FALSE;
		}
		if(extension_loaded('gd')) {
			$development_data[] = array('item' => 'GD��չ', 'cur_development' => '��', 'need_setting' => '���鿪��', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'GD��չ', 'cur_development' => '��', 'need_setting' => '���鿪��', 'pass' => FALSE);
		}
		$files_arr = array('config.inc.php', PUBLIC_DIR . 'data/cache/', PUBLIC_DIR . 'data/logs/', UPLOAD_URL, UPLOAD_URL . 'thumbnails/', 'QXDream/');
		foreach($files_arr as $file) {
			if(is_writable($file)) {
				$dir_privilege_data[] = array('item' => $file, 'cur_status' => '��д', 'need_status' => '��д', 'pass' => TRUE);
			} else {
				$dir_privilege_data[] = array('item' => $file, 'cur_status' => '����д', 'need_status' => '��д', 'pass' => FALSE);
				$dir_pass = FALSE;
			}
		}
		if(isset($_POST['ok'])) {
			if(!$dev_pass) {
				$this->view->assign('error', '<p id="error">���л�����ͨ����������������</p><p><input type="button" class="btn" value="����" onclick="history.back(1)" /></p>');
			}
			if(!$dir_pass) {
				$this->view->assign('error', '<p id="error">�ļ������Ŀ���в���д�ģ����ֶ����ģ��������linux����,�����Ӧ��Ŀ¼���ļ�Ȩ�޸�Ϊ0777��</p><p><input type="button" class="btn" value="����" onclick="history.back(1)" /></p>');
			}
		}
		$this->view->assign('page_title', '������� - ' . $this->install_Wizard);
		$this->view->assign('step', 2);
		$this->view->assign('development_data', $development_data);
		$this->view->assign('dir_privilege_data', $dir_privilege_data);
		$this->view->assign('dev_pass', $dev_pass);
		$this->view->assign('dir_pass', $dir_pass);
		$this->view->display('index');
	}
	//�����˺�
	public function index_3() {
		$pass = TRUE;
		$this->view->assign('page_title', '�����˺� - ' . $this->install_Wizard);
		$this->view->assign('step', 3);
		$this->view->assign('pass', $pass);
		$this->view->display('index');
	}
	//��װ����
	public function index_4() {
		$this->view->assign('page_title', '��װ���� - ' . $this->install_Wizard);
		$this->view->assign('step', 4);
		if(isset($_POST['ok'])) {
			$db_host = $_POST['config']['db_host'] = trim($_POST['config']['db_host']);
			$db_name = $_POST['config']['db_name'] = trim($_POST['config']['db_name']);
			$db_pre  = $_POST['config']['db_pre']  = trim($_POST['config']['db_pre']);
			$db_user = $_POST['config']['db_user'] = trim($_POST['config']['db_user']);
			$db_pass = $_POST['config']['db_pass'] = trim($_POST['config']['db_pass']);
			$qx_key  = $_POST['config']['qx_key']  = trim($_POST['config']['qx_key']);
			
			$creator_name = trim($_POST['creator_name']);
			$creator_pass = trim($_POST['creator_pass']);
			$creator_pass_again = trim($_POST['creator_pass_again']);
			
			$sql_file = PUBLIC_DIR . 'data/install_sql/qxd_mutiuser_release.sql'; //sql��װ�ļ�
			
			if($db_host == '' || $db_name == '' || $db_pre == '' || $db_user == '' || $qx_key == '' || $creator_name == '' || $creator_pass == '' || $creator_pass_again == ''){
				$this->view->assign('error', '<p id="error">��ȷ�ϱ�����</p><p><input type="button" class="btn" value="��һ��" onclick="history.back(1)" /></p>');	
				$this->view->display('index');
				return;
			}
			if($creator_pass != $creator_pass_again) {
				$this->view->assign('error','<p id="error">�û���Ϣ��������������벻һ��</p><p><input type="button" class="btn" value="��һ��" onclick="history.back(1)" /></p>');	
				$this->view->display('index');
				return;
			}
			if(!($conn = @mysql_connect($db_host, $db_user, $db_pass))) {
			 	$this->view->assign('error', '<p id="error">��ȷ�Ϸ�������ַ�����ݿ��û��������ݿ��û������Ƿ���ȷ</p><p><input type="button" class="btn" value="��һ��" onclick="history.back(1)" /></p>');
				$this->view->display('index');
				return;	
			} else {
				mysql_query("CREATE DATABASE IF NOT EXISTS `".$db_name."`") or die(mysql_error());//�����ھʹ���������ݿ�
				mysql_select_db($db_name, $conn) or die('û�и����ݿ�');
				mysql_query("set names '" . DB_CHARSET . "'");
				
				//��ʼ��װmysql����
				if(is_readable($sql_file)){
					$this->view->assign('sql_file', $sql_file);
					$this->view->assign('creator_name', $creator_name);
					$this->view->assign('creator_pass', $creator_pass);
				} else{
					$this->view->assign('error', '<p id="error">SQL�����ļ������ڻ��߲��ɶ�,��װ�޷�������</p><p>' . $sql_file . '</p><p><input type="button" class="btn" value="��һ��" onclick="history.back(1)" /></p>');
				}
				$this->view->display('index');
			}
		}
	}
	//��װ���
	public function index_5() {
		$this->view->assign('page_title', '��װ��� - ' . $this->install_Wizard);
		$this->view->assign('step', 5);
		$this->view->display('index');
		unlink('install.php');
	}
}
?>