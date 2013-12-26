<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2011-05-14 后台菜单数据 $
	@version  $Id: admin_menu.inc.php 1.0 2011-05-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

//菜单,键值为菜单ID,不能重复
return array(
	/**********************************
	 * 管理首页
	 **********************************/
	 1 => array(
		'parent_id' => 0, //父菜单ID
		'name' => $GLOBALS['QXDREAM']['admin_language']['admin_index'], //菜单名称
		'target' => '', //链接打开目标
		'url' => '#', //链接地址
		//组为时1可以访问,即需要的权限,组为2以上时，要先判断是否2可访问，再判断相应组的模块，
		//99时为2以后的权限均可访问,1,2,99时即所有均可访问，模组资源即mr_name有值时还会要根据mr_ids判断，默认一定要写group_ids为2
		'group_ids' => '1,2,99',
		'mr_name' => '',
	),
		//===============================
		// 常用操作
		//===============================
		2 => array(
			'parent_id' => 1,
			'name' => $GLOBALS['QXDREAM']['admin_language']['common_operation'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '1,2,99',
			'collapse' => 0,  //如果有子菜单,再判断是否是收拢的,只有父类里有这个属性
		),
			3 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['user_manage'],
				'target' => 'right',
				'url' => 'user/',
				'mr_name' => 'user',
				'group_ids' => '1,2',
			),
			4 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['user_add'],
				'target' => 'right',
				'url' => 'user/add/',
				'mr_name' => 'user',
				'group_ids' => '1,2',
			),
			5 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['add_company'],
				'target' => 'right',
				'url' => 'company/add/',
				'mr_name' => '',
				'group_ids' => '1',
			),
			6 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['update_cache'],
				'target' => 'right',
				'url' => 'cache/',
				'mr_name' => '',
				'group_ids' => '1,2',
			),
			7 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['site_setting'],
				'target' => 'right',
				'url' => 'setting/',
				'mr_name' => '',
				'group_ids' => '1,2',
			),
			8 => array(
				'parent_id' => 2,
				'name' => $GLOBALS['QXDREAM']['admin_language']['my_setting'],
				'target' => 'right',
				'url' => 'user/edit/user_id/{qx_user_id}',
				'mr_name' => '',
				'group_ids' => '1,2,99',
			),
	/**********************************
	 * 系统设置
	 **********************************/
	 9 => array(
		'parent_id' => 0,
		'name' => $GLOBALS['QXDREAM']['admin_language']['system_setting'],
		'target' => '',
		'url' => '#',
		'group_ids' => '1,2',
		'mr_name' => '',
	),
		//===============================
		// 模型管理
		//===============================
		10 => array(
			'parent_id' => 9,
			'name' => $GLOBALS['QXDREAM']['admin_language']['model_manage'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '1',
			'collapse' => 0,
		),
			11 => array(
				'parent_id' => 10,
				'name' => $GLOBALS['QXDREAM']['admin_language']['model_list'],
				'target' => 'right',
				'url' => 'model/',
				'mr_name' => '',
				'group_ids' => '1',
			),
			12 => array(
				'parent_id' => 10,
				'name' => $GLOBALS['QXDREAM']['admin_language']['model_add'],
				'target' => 'right',
				'url' => 'model/add/',
				'mr_name' => '',
				'group_ids' => '1',
			),
		//===============================
		// 网站配置
		//===============================
		13 => array(
			'parent_id' => 9,
			'name' => $GLOBALS['QXDREAM']['admin_language']['site_setting'],
			'target' => 'right',
			'url' => 'setting/',
			'mr_name' => '',
			'group_ids' => '1,2',
			'collapse' => 0,
		),
	/**********************************
	 * 栏目管理
	 **********************************/
	 14 => array(
		'parent_id' => 0,
		'name' => $GLOBALS['QXDREAM']['admin_language']['category_manage'],
		'target' => '',
		'url' => '#',
		'mr_name' => '',
		'group_ids' => '2,99',
		'mr_name' => '',
	),
		//===============================
		// 内容发布管理
		//===============================
		15 => array(
			'parent_id' => 14,
			'name' => $GLOBALS['QXDREAM']['admin_language']['content_manage'],
			'target' => 'right',
			'url' => 'contentAll/', //2以上的不显示链接，另做处理
			'mr_name' => '',
			'group_ids' => '2,99',
			'tree_sign' => 'qxd_tree', //另外调用树型,标签id与class为qxd_tree+key
			'collapse' => 0,  //如果有子菜单,再判断是否是收拢的,只有父类里有这个属性
		),
		//===============================
		// 栏目管理
		//===============================
		16 => array(
			'parent_id' => 14,
			'name' => $GLOBALS['QXDREAM']['admin_language']['category_manage'],
			'target' => 'right',
			'url' => 'category/',
			'mr_name' => '',
			'group_ids' => '2',
			'collapse' => 1,
		),
			17 => array(
				'parent_id' => 16,
				'name' => $GLOBALS['QXDREAM']['admin_language']['category_list'],
				'target' => 'right',
				'url' => 'category/',
				'mr_name' => '',
				'group_ids' => '2',
			),
			18 => array(
				'parent_id' => 16,
				'name' => $GLOBALS['QXDREAM']['admin_language']['category_add'],
				'target' => 'right',
				'url' => 'category/add/',
				'mr_name' => '',
				'group_ids' => '2',
			),
	 /**********************************
	 * 公司管理
	 **********************************/
	 19 => array(
		'parent_id' => 0,
		'name' => $GLOBALS['QXDREAM']['admin_language']['company_manage'],
		'target' => '',
		'url' => '#',
		'mr_name' => '',
		'group_ids' => '1',
		'mr_name' => '',
	),
		//===============================
		// 公司管理
		//===============================
		20 => array(
			'parent_id' => 19,
			'name' => $GLOBALS['QXDREAM']['admin_language']['company_manage'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '1',
			'collapse' => 0,
		),
			21 => array(
				'parent_id' => 20,
				'name' => $GLOBALS['QXDREAM']['admin_language']['company_list'],
				'target' => 'right',
				'url' => 'company/',
				'mr_name' => '',
				'group_ids' => '1',
			),
			22 => array(
				'parent_id' => 20,
				'name' => $GLOBALS['QXDREAM']['admin_language']['add_company'],
				'target' => 'right',
				'url' => 'company/add/',
				'mr_name' => '',
				'group_ids' => '1',
			),
	
	 /**********************************
	 * 资源管理
	 **********************************/
	 23 => array(
		'parent_id' => 0,
		'name' => $GLOBALS['QXDREAM']['admin_language']['mr'],
		'target' => '',
		'url' => '#',
		'mr_name' => '',
		'group_ids' => '1',
		'mr_name' => '',
	),
		//===============================
		// 模组资源管理
		//===============================
		24 => array(
			'parent_id' => 23,
			'name' => $GLOBALS['QXDREAM']['admin_language']['mr_manage'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '1',
			'collapse' => 0,
		),
			25 => array(
				'parent_id' => 24,
				'name' => $GLOBALS['QXDREAM']['admin_language']['mr_list'],
				'target' => 'right',
				'url' => 'mr/',
				'mr_name' => '',
				'group_ids' => '1',
			),
			26 => array(
				'parent_id' => 24,
				'name' => $GLOBALS['QXDREAM']['admin_language']['mr_add'],
				'target' => 'right',
				'url' => 'mr/add/',
				'mr_name' => '',
				'group_ids' => '1',
			),
	
	 /**********************************
	 * 模块管理
	 **********************************/
	 27 => array(
		'parent_id' => 0,
		'name' => $GLOBALS['QXDREAM']['admin_language']['module_manage'],
		'target' => '',
		'url' => '#',
		'mr_name' => '',
		'group_ids' => '1,2,99',
		'mr_name' => '',
	),
		//===============================
		// 用户管理
		//===============================
		28 => array(
			'parent_id' => 27,
			'name' => $GLOBALS['QXDREAM']['admin_language']['user_manage'],
			'target' => 'right',
			'url' => '',
			'mr_name' => 'user',
			'group_ids' => '1,2',
			'collapse' => 0,
		),
			29 => array(
				'parent_id' => 28,
				'name' => $GLOBALS['QXDREAM']['admin_language']['user_list'],
				'target' => 'right',
				'url' => 'user/',
				'mr_name' => 'user',
				'group_ids' => '1,2',
			),
			30 => array(
				'parent_id' => 28,
				'name' => $GLOBALS['QXDREAM']['admin_language']['user_add'],
				'target' => 'right',
				'url' => 'user/add/',
				'mr_name' => 'user',
				'group_ids' => '1,2',
			),
			31 => array(
				'parent_id' => 28,
				'name' => $GLOBALS['QXDREAM']['admin_language']['my_setting'],
				'target' => 'right',
				'url' => 'user/edit/user_id/{qx_user_id}',
				'mr_name' => '',
				'group_ids' => '1,2,99',
			),
		//===============================
		// 用户组管理
		//===============================
		32 => array(
			'parent_id' => 27,
			'name' => $GLOBALS['QXDREAM']['admin_language']['group_manage'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '2',
			'collapse' => 0,
		),
			33 => array(
				'parent_id' => 32,
				'name' => $GLOBALS['QXDREAM']['admin_language']['group_list'],
				'target' => 'right',
				'url' => 'group/',
				'mr_name' => '',
				'group_ids' => '2',
			),
			34 => array(
				'parent_id' => 32,
				'name' => $GLOBALS['QXDREAM']['admin_language']['group_add'],
				'target' => 'right',
				'url' => 'group/add/',
				'mr_name' => '',
				'group_ids' => '2',
			),
		//===============================
		// 附件菜单
		//===============================
		35 => array(
			'parent_id' => 27,
			'name' => $GLOBALS['QXDREAM']['admin_language']['attachment_manage'],
			'target' => 'right',
			'url' => 'attachment/',
			'mr_name' => '',
			'group_ids' => '2,99',
			'collapse' => 0,
		),
);
?>