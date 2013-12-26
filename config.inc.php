<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2010-11-30 全局配置 $
	@version  $Id: Config.inc.php 2011-05-09
*/

//数据库配置信息
define('DB_HOST', ''); //数据库主机地址
define('DB_USER', ''); //数据库用户名
define('DB_PASS', ''); //数据库密码
define('DB_NAME', ''); //数据库名称
define('DB_PRE', ''); //数据表前缀
define('DB_CHARSET', 'gbk'); //数据库字符集
define('DB_PCONNECT', FALSE); //是否开启持久连接

//COOKIE设置
define('COOKIE_DOMAIN', ''); //Cookie 作用域
define('COOKIE_PATH', '/'); //Cookie 作用路径
define('COOKIE_PRE', 'qx_multi_'); //Cookie 前缀

//当前脚本名
define('PHP_SELF', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']); 
//网站路径配置，框架访问路径，相对于域名
define('QX_PATH', dirname(PHP_SELF) . '/');
define('ADMIN_PATH', './qx-admin/'); //后台路径,必须指向后台APP_PATH目录
!defined('APP_PATH') && define('APP_PATH', ADMIN_PATH);

define('DEFAULT_CONTROL', 'index'); //默认调用的控制器
define('DEFAULT_METHOD', 'index'); //默认执行方法

define('APP_DIR', 'app/'); //应用模块目录
define('CONTROLLERS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'controllers/'); //控制器目录
define('MODELS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'models/'); //模型目录
define('PUBLIC_DIR', 'public/'); //公共文件目录
define('LIBS_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'libs/' ); //类库目录
define('PATH_INFO', isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : (isset($_SERVER['REDIRECT_PATH_INFO']) ? $_SERVER['REDIRECT_PATH_INFO'] : '')));

//视图设置
define('ADMIN_PLAN', 'admin_fade_timer'); //后台主题风格
define('VIEW_ADMIN_ROOT', QX_ROOT . APP_DIR . APP_PATH . 'views/'); //后台视图目录
define('VIEW_ADMIN_CSS', QX_PATH . PUBLIC_DIR . 'theme/' . ADMIN_PLAN . '/css/'); //后台视图CSS

//数据缓存设置
define('CACHE_PATH', QX_ROOT . PUBLIC_DIR . 'data/cache/');
define('CACHE_FILE_SUFFIX', '.php'); //数据缓存文件后缀

//附件设置
define('UPLOAD', TRUE); //是否允许前台上传附件
define('UPLOAD_URL', PUBLIC_DIR . 'uploadfile/'); //附件目录访问路径
define('UPLOAD_ROOT', QX_ROOT . UPLOAD_URL); //附件保存物理路径
define('UPLOAD_ALLOW_SUFFIX', 'doc|docx|xls|ppt|wps|zip|rar|txt|jpg|jpeg|gif|bmp|swf|png'); //允许上传的文件后缀，多个后缀用“|”分隔
define('UPLOAD_MAXSIZE', 1024000); //允许上传的附件最大值
define('UPLOAD_ALLOW_PIC_SUFFIX', 'jpg|jpeg|gif|bmp|png'); //图片后台

//安全设置
define('QX_KEY',''); //登录验证密钥
define('OVERTIME', 0); //后台用户会话超时时间,0为永不超时
define('DEBUG', FALSE); //是否显示调试信息
define('IS_LOG', TRUE); //错误信息记录日志
define('IS_ID_CODE', FALSE); //验证码是否开启,除会员登陆和注册的
define('LOGIN_TIMES', 6); //错误登录次数限制
define('LOGIN_INTERVAL_TIME', 900); //超出登录次数限制后,间隔多少时间登录

//其他设置
define('CREATOR', ''); //创始人ID,多个用逗号隔开
define('RUNTIME', TRUE); //启用RUNTIME功能
define('LANG_PACK', 'zh-cn'); //网站语言包
define('TIMEOFFSET', 8); //时区偏移量,8是北京时区
define('IS_SHOW_EXEC_INFO', FALSE); //执行sql语句数量显示
define('REWRITE', FALSE); //前台是否开启拟静态,需服务器支持apache重定向
define('PIC_WIDTH', 600); //图片超过多少宽度,显示时就缩放到此宽度(以防图片撑破页面),为0时不缩放
define('DEVELOPER_HOMEPAGE', 'http://www.qxhtml.cn');
define('DEVELOPER', '踏雪残情');
define('PRO_VERSION', '1.0');
?>