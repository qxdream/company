<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-05-21 系统安装控制器 $
	@version  $Id: IndexAction.class.php 1.0 2011-05-22
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class IndexAction extends Controller {
	public function _initialize() {
		@set_time_limit(1000);
		$this->install_Wizard = '倾行多用户企业管理系统(QXDream Mutiuser)安装向导';
		$this->view->assign('install_Wizard', $this->install_Wizard);
	}
	//安装协议
	public function index() {
		$this->view->assign('page_title', '安装协议 - ' . $this->install_Wizard);
		$this->view->assign('step', 1);
		$this->view->display('index');
	}
	//环境检测
	public function index_2() {
		$dev_pass = $dir_pass = TRUE;
		$development_data = $dir_privilege_data = array();
		$development_data[] = array('item' => '操作系统', 'cur_development' => php_uname(), 'need_setting' => 'Windows_NT/Linux/Freebsd', 'pass' => TRUE);
		$development_data[] = array('item' => 'WEB服务器', 'cur_development' => $_SERVER['SERVER_SOFTWARE'], 'need_setting' => 'Apache/Nginx/IIS', 'pass' => TRUE);
		if(phpversion() > '5.2.0') {
			$development_data[] = array('item' => 'PHP版本', 'cur_development' => 'PHP' . phpversion(), 'need_setting' => 'PHP5.2.0及以上', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'PHP版本', 'cur_development' => 'PHP' . phpversion(), 'need_setting' => 'PHP5.2.0及以上', 'pass' => FALSE);
			$dev_pass = FALSE;
		}
		if(extension_loaded('mysql')) {
			$development_data[] = array('item' => 'MYSQL扩展', 'cur_development' => '√', 'need_setting' => '必须开启', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'MYSQL扩展', 'cur_development' => '×', 'need_setting' => '必须开启', 'pass' => FALSE);
			$dev_pass = FALSE;
		}
		if(extension_loaded('gd')) {
			$development_data[] = array('item' => 'GD扩展', 'cur_development' => '√', 'need_setting' => '建议开启', 'pass' => TRUE);
		} else {
			$development_data[] = array('item' => 'GD扩展', 'cur_development' => '×', 'need_setting' => '建议开启', 'pass' => FALSE);
		}
		$files_arr = array('config.inc.php', PUBLIC_DIR . 'data/cache/', PUBLIC_DIR . 'data/logs/', UPLOAD_URL, UPLOAD_URL . 'thumbnails/', 'QXDream/');
		foreach($files_arr as $file) {
			if(is_writable($file)) {
				$dir_privilege_data[] = array('item' => $file, 'cur_status' => '可写', 'need_status' => '可写', 'pass' => TRUE);
			} else {
				$dir_privilege_data[] = array('item' => $file, 'cur_status' => '不可写', 'need_status' => '可写', 'pass' => FALSE);
				$dir_pass = FALSE;
			}
		}
		if(isset($_POST['ok'])) {
			if(!$dev_pass) {
				$this->view->assign('error', '<p id="error">运行环境不通过，请升级环境。</p><p><input type="button" class="btn" value="返回" onclick="history.back(1)" /></p>');
			}
			if(!$dir_pass) {
				$this->view->assign('error', '<p id="error">文件检查项目中有不可写的，请手动更改，如果您是linux主机,请把相应的目录和文件权限改为0777。</p><p><input type="button" class="btn" value="返回" onclick="history.back(1)" /></p>');
			}
		}
		$this->view->assign('page_title', '环境检测 - ' . $this->install_Wizard);
		$this->view->assign('step', 2);
		$this->view->assign('development_data', $development_data);
		$this->view->assign('dir_privilege_data', $dir_privilege_data);
		$this->view->assign('dev_pass', $dev_pass);
		$this->view->assign('dir_pass', $dir_pass);
		$this->view->display('index');
	}
	//配置账号
	public function index_3() {
		$pass = TRUE;
		$this->view->assign('page_title', '配置账号 - ' . $this->install_Wizard);
		$this->view->assign('step', 3);
		$this->view->assign('pass', $pass);
		$this->view->display('index');
	}
	//安装过程
	public function index_4() {
		$this->view->assign('page_title', '安装过程 - ' . $this->install_Wizard);
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
			
			$sql_file = PUBLIC_DIR . 'data/install_sql/qxd_mutiuser_release.sql'; //sql安装文件
			
			if($db_host == '' || $db_name == '' || $db_pre == '' || $db_user == '' || $qx_key == '' || $creator_name == '' || $creator_pass == '' || $creator_pass_again == ''){
				$this->view->assign('error', '<p id="error">请确认必填项</p><p><input type="button" class="btn" value="上一步" onclick="history.back(1)" /></p>');	
				$this->view->display('index');
				return;
			}
			if($creator_pass != $creator_pass_again) {
				$this->view->assign('error','<p id="error">用户信息中两次输入的密码不一致</p><p><input type="button" class="btn" value="上一步" onclick="history.back(1)" /></p>');	
				$this->view->display('index');
				return;
			}
			if(!($conn = @mysql_connect($db_host, $db_user, $db_pass))) {
			 	$this->view->assign('error', '<p id="error">请确认服务器地址、数据库用户名、数据库用户密码是否正确</p><p><input type="button" class="btn" value="上一步" onclick="history.back(1)" /></p>');
				$this->view->display('index');
				return;	
			} else {
				mysql_query("CREATE DATABASE IF NOT EXISTS `".$db_name."`") or die(mysql_error());//不存在就创建这个数据库
				mysql_select_db($db_name, $conn) or die('没有该数据库');
				mysql_query("set names '" . DB_CHARSET . "'");
				
				//开始安装mysql数据
				if(is_readable($sql_file)){
					$this->view->assign('sql_file', $sql_file);
					$this->view->assign('creator_name', $creator_name);
					$this->view->assign('creator_pass', $creator_pass);
				} else{
					$this->view->assign('error', '<p id="error">SQL导入文件不存在或者不可读,安装无法继续！</p><p>' . $sql_file . '</p><p><input type="button" class="btn" value="上一步" onclick="history.back(1)" /></p>');
				}
				$this->view->display('index');
			}
		}
	}
	//安装完成
	public function index_5() {
		$this->view->assign('page_title', '安装完成 - ' . $this->install_Wizard);
		$this->view->assign('step', 5);
		$this->view->display('index');
		unlink('install.php');
	}
}
?>