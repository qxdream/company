<?php
/* 
 [QXDream] ShareAction.class.php 2011-03-03
*/
defined('IN_QX') or die('<h1>Forbidden!</h1>');

// ★ ------------------------------------------
// ↓ 作者      踏雪残情
// ★ ------------------------------------------
// ↓ 建立日期  2011-02-24
// ★ ------------------------------------------
// ↓ 更新日期  2011-03-03
// ★ ------------------------------------------
// ↓ 功能      后台共用控制器
// ★ ------------------------------------------
// ↓ 版本      ver 1.0
// ★ ------------------------------------------

class ShareAction extends Controller {
	
	public function _initialize() {
		$GLOBALS['QXDREAM']['admin_language'] = language('QXDream_admin'); //载入后台语言包
		$this->user = new User();
		$this->user->check_admin();
	}
}
?>