<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2011-05-14 ��̨�˵����� $
	@version  $Id: admin_menu.inc.php 1.0 2011-05-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

//�˵�,��ֵΪ�˵�ID,�����ظ�
return array(
	/**********************************
	 * ������ҳ
	 **********************************/
	 1 => array(
		'parent_id' => 0, //���˵�ID
		'name' => $GLOBALS['QXDREAM']['admin_language']['admin_index'], //�˵�����
		'target' => '', //���Ӵ�Ŀ��
		'url' => '#', //���ӵ�ַ
		//��Ϊʱ1���Է���,����Ҫ��Ȩ��,��Ϊ2����ʱ��Ҫ���ж��Ƿ�2�ɷ��ʣ����ж���Ӧ���ģ�飬
		//99ʱΪ2�Ժ��Ȩ�޾��ɷ���,1,2,99ʱ�����о��ɷ��ʣ�ģ����Դ��mr_name��ֵʱ����Ҫ����mr_ids�жϣ�Ĭ��һ��Ҫдgroup_idsΪ2
		'group_ids' => '1,2,99',
		'mr_name' => '',
	),
		//===============================
		// ���ò���
		//===============================
		2 => array(
			'parent_id' => 1,
			'name' => $GLOBALS['QXDREAM']['admin_language']['common_operation'],
			'target' => 'right',
			'url' => '',
			'mr_name' => '',
			'group_ids' => '1,2,99',
			'collapse' => 0,  //������Ӳ˵�,���ж��Ƿ�����£��,ֻ�и��������������
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
	 * ϵͳ����
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
		// ģ�͹���
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
		// ��վ����
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
	 * ��Ŀ����
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
		// ���ݷ�������
		//===============================
		15 => array(
			'parent_id' => 14,
			'name' => $GLOBALS['QXDREAM']['admin_language']['content_manage'],
			'target' => 'right',
			'url' => 'contentAll/', //2���ϵĲ���ʾ���ӣ���������
			'mr_name' => '',
			'group_ids' => '2,99',
			'tree_sign' => 'qxd_tree', //�����������,��ǩid��classΪqxd_tree+key
			'collapse' => 0,  //������Ӳ˵�,���ж��Ƿ�����£��,ֻ�и��������������
		),
		//===============================
		// ��Ŀ����
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
	 * ��˾����
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
		// ��˾����
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
	 * ��Դ����
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
		// ģ����Դ����
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
	 * ģ�����
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
		// �û�����
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
		// �û������
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
		// �����˵�
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