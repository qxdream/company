<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2010-11-30 ȫ������ $
	@version  $Id: Config.inc.php 2011-05-09
*/

//���ݿ�������Ϣ
define('DB_HOST', ''); //���ݿ�������ַ
define('DB_USER', ''); //���ݿ��û���
define('DB_PASS', ''); //���ݿ�����
define('DB_NAME', ''); //���ݿ�����
define('DB_PRE', ''); //���ݱ�ǰ׺
define('DB_CHARSET', 'gbk'); //���ݿ��ַ���
define('DB_PCONNECT', FALSE); //�Ƿ����־�����

//COOKIE����
define('COOKIE_DOMAIN', ''); //Cookie ������
define('COOKIE_PATH', '/'); //Cookie ����·��
define('COOKIE_PRE', 'qx_multi_'); //Cookie ǰ׺

//��ǰ�ű���
define('PHP_SELF', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']); 
//��վ·�����ã���ܷ���·�������������
define('QX_PATH', dirname(PHP_SELF) . '/');
define('ADMIN_PATH', './qx-admin/'); //��̨·��,����ָ���̨APP_PATHĿ¼
!defined('APP_PATH') && define('APP_PATH', ADMIN_PATH);

define('DEFAULT_CONTROL', 'index'); //Ĭ�ϵ��õĿ�����
define('DEFAULT_METHOD', 'index'); //Ĭ��ִ�з���

define('APP_DIR', 'app/'); //Ӧ��ģ��Ŀ¼
define('CONTROLLERS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'controllers/'); //������Ŀ¼
define('MODELS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'models/'); //ģ��Ŀ¼
define('PUBLIC_DIR', 'public/'); //�����ļ�Ŀ¼
define('LIBS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'libs/' ); //���Ŀ¼
define('PATH_INFO', isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : (isset($_SERVER['REDIRECT_PATH_INFO']) ? $_SERVER['REDIRECT_PATH_INFO'] : '')));

//��ͼ����
define('ADMIN_PLAN', 'admin_fade_timer'); //��̨������
define('VIEW_ADMIN_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/'); //��̨��ͼĿ¼
define('VIEW_ADMIN_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . ADMIN_PLAN . '/css/'); //��̨��ͼCSS

//���ݻ�������
define('CACHE_PATH', QX_ROOT . PUBLIC_DIR . 'data/cache/');
define('CACHE_FILE_SUFFIX', '.php'); //���ݻ����ļ���׺

//��������
define('UPLOAD', TRUE); //�Ƿ�����ǰ̨�ϴ�����
define('UPLOAD_URL', PUBLIC_DIR . 'uploadfile/'); //����Ŀ¼����·��
define('UPLOAD_ROOT', QX_ROOT . UPLOAD_URL); //������������·��
define('UPLOAD_ALLOW_SUFFIX', 'doc|docx|xls|ppt|wps|zip|rar|txt|jpg|jpeg|gif|bmp|swf|png'); //�����ϴ����ļ���׺�������׺�á�|���ָ�
define('UPLOAD_MAXSIZE', 1024000); //�����ϴ��ĸ������ֵ
define('UPLOAD_ALLOW_PIC_SUFFIX', 'jpg|jpeg|gif|bmp|png'); //ͼƬ��̨

//��ȫ����
define('QX_KEY',''); //��¼��֤��Կ
define('OVERTIME', 0); //��̨�û��Ự��ʱʱ��,0Ϊ������ʱ
define('DEBUG', FALSE); //�Ƿ���ʾ������Ϣ
define('IS_LOG', TRUE); //������Ϣ��¼��־
define('IS_ID_CODE', FALSE); //��֤���Ƿ���,����Ա��½��ע���
define('LOGIN_TIMES', 6); //�����¼��������
define('LOGIN_INTERVAL_TIME', 900); //������¼�������ƺ�,�������ʱ���¼

//��������
define('CREATOR', ''); //��ʼ��ID,����ö��Ÿ���
define('RUNTIME', TRUE); //����RUNTIME����
define('LANG_PACK', 'zh-cn'); //��վ���԰�
define('TIMEOFFSET', 8); //ʱ��ƫ����,8�Ǳ���ʱ��
define('IS_SHOW_EXEC_INFO', FALSE); //ִ��sql���������ʾ
define('REWRITE', FALSE); //ǰ̨�Ƿ����⾲̬,�������֧��apache�ض���
define('PIC_WIDTH', 600); //ͼƬ�������ٿ��,��ʾʱ�����ŵ��˿��(�Է�ͼƬ����ҳ��),Ϊ0ʱ������
define('DEVELOPER_HOMEPAGE', 'http://www.qxhtml.cn');
define('DEVELOPER', '̤ѩ����');
define('PRO_VERSION', '1.0');
?>