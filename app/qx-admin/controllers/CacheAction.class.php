<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-04-05 后台缓存更新控制器 $
	@version  $Id: cacheAction.class.php 1.0 2011-04-05
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class cacheAction extends ShareAction {
	
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
		$this->model = new model();
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
		if(0 == $GLOBALS['QXDREAM']['qx_company_id']) { //超级后台
			$this->model->cache_all();
		} else { //公司管理后台
			$this->model->cache_all($GLOBALS['QXDREAM']['qx_company_id']);
		}
		show_msg('all_cache_update_ok');
	}
}
?>