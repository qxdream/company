<?php
/* 
 [QXDream] ShareAction.class.php 2011-03-03
*/
defined('IN_QX') or die('<h1>Forbidden!</h1>');

// �� ------------------------------------------
// �� ����      ̤ѩ����
// �� ------------------------------------------
// �� ��������  2011-02-24
// �� ------------------------------------------
// �� ��������  2011-03-03
// �� ------------------------------------------
// �� ����      ��̨���ÿ�����
// �� ------------------------------------------
// �� �汾      ver 1.0
// �� ------------------------------------------

class ShareAction extends Controller {
	
	public function _initialize() {
		$GLOBALS['QXDREAM']['admin_language'] = language('QXDream_admin'); //�����̨���԰�
		$this->user = new User();
		$this->user->check_admin();
	}
}
?>