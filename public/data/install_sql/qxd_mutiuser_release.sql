-- -----------------------------------------
-- QXDREAM MUTIUSER V1.0 SQL 2011-05-21
-- -----------------------------------------

--
-- 表的结构 `{table_prefix}_attachment`
--
DROP TABLE IF EXISTS `{table_prefix}_attachment`;
CREATE TABLE `{table_prefix}_attachment` (
  `attachment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `filename` char(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` char(50) NOT NULL,
  `file_size` int(10) unsigned NOT NULL DEFAULT '0',
  `file_suffix` char(10) NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_image` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `thumb` varchar(255) NOT NULL,
  `post_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `download_count` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `company_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `company_uid` char(20) NOT NULL,
  PRIMARY KEY (`attachment_id`),
  KEY `content_id` (`content_id`),
  KEY `company_id` (`company_id`),
  KEY `company_uid` (`company_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_category`
--
DROP TABLE IF EXISTS `{table_prefix}_category`;
CREATE TABLE `{table_prefix}_category` (
  `cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `company_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `company_uid` char(20) NOT NULL,
  `model_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `has_child` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `all_child_id` text NOT NULL,
  `cat_name` char(30) NOT NULL,
  `hits_count` int(11) unsigned NOT NULL DEFAULT '0',
  `list_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_nav` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `url` varchar(100) NOT NULL,
  `content_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `setting` text NOT NULL,
  `template` char(80) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `parent_id` (`parent_id`,`list_order`),
  KEY `company_id` (`company_id`),
  KEY `company_uid` (`company_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_company`
--
DROP TABLE IF EXISTS `{table_prefix}_company`;
CREATE TABLE `{table_prefix}_company` (
  `company_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `company_uid` char(20) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `site_name` varchar(200) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `copyright` text NOT NULL,
  `icp_no` char(50) NOT NULL,
  `mr_ids` text NOT NULL,
  `post_time` int(10) unsigned NOT NULL DEFAULT '0',
  `hits_count` int(11) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `company_uid` (`company_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_content`
--
DROP TABLE IF EXISTS `{table_prefix}_content`;
CREATE TABLE `{table_prefix}_content` (
  `content_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `company_uid` char(20) NOT NULL,
  `model_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` char(80) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `post_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  `hits_count` int(11) unsigned NOT NULL DEFAULT '0',
  `attachment_id` text NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `status` (`status`),
  KEY `cat_id` (`cat_id`),
  KEY `hits_count` (`hits_count`),
  KEY `post_time` (`post_time`),
  KEY `company_uid` (`company_uid`),
  KEY `company_id` (`company_id`),
  KEY `model_id` (`model_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_content_news`
--
DROP TABLE IF EXISTS `{table_prefix}_content_news`;
CREATE TABLE `{table_prefix}_content_news` (
  `content_id` mediumint(8) unsigned NOT NULL,
  `content` mediumtext NOT NULL,
  `author` char(30) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_content_page`
--
DROP TABLE IF EXISTS `{table_prefix}_content_page`;
CREATE TABLE `{table_prefix}_content_page` (
  `content_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `company_id` int(8) NOT NULL,
  `company_uid` char(20) NOT NULL,
  PRIMARY KEY (`content_id`),
  UNIQUE KEY `cat_id` (`cat_id`),
  KEY `company_id` (`company_id`),
  KEY `company_uid` (`company_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_content_product`
--
DROP TABLE IF EXISTS `{table_prefix}_content_product`;
CREATE TABLE `{table_prefix}_content_product` (
  `content_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  `image` varchar(255) NOT NULL,
  `author` char(30) NOT NULL,
  `size` varchar(255) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;


-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_model`
--
DROP TABLE IF EXISTS `{table_prefix}_model`;
CREATE TABLE `{table_prefix}_model` (
  `model_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `model_name` char(30) NOT NULL,
  `model_comment` char(20) NOT NULL,
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`model_id`),
  KEY `model_name` (`model_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `{table_prefix}_model`
--

INSERT INTO `{table_prefix}_model` (`model_id`, `model_name`, `model_comment`, `is_system`, `disabled`, `is_hidden`) VALUES
(1, 'news', '新闻发布', 1, 0, 0),
(2, 'product', '产品展示', 1, 0, 0),
(3, 'page', '单页', 1, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_model_field`
--
DROP TABLE IF EXISTS `{table_prefix}_model_field`;
CREATE TABLE `{table_prefix}_model_field` (
  `field_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `model_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `field_name` char(20) NOT NULL,
  `field_comment` char(20) NOT NULL,
  `type` char(30) NOT NULL,
  `tips` text NOT NULL,
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_require` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `list_order` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`),
  KEY `model_id` (`model_id`,`list_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `{table_prefix}_model_field`
--

INSERT INTO `{table_prefix}_model_field` (`field_id`, `model_id`, `field_name`, `field_comment`, `type`, `tips`, `is_system`, `is_require`, `disabled`, `list_order`) VALUES
(1, 1, 'cat_id', '栏目', 'cat_id', '仅限于在同一模型上更换内容所属栏目', 1, 1, 0, 1),
(2, 1, 'title', '标题', 'text', '请填写', 1, 1, 0, 0),
(3, 1, 'description', '描述', 'textarea', '', 1, 0, 0, 3),
(4, 1, 'content', '内容', 'editor', '', 0, 0, 0, 4),
(5, 1, 'author', '作者', 'text', '', 0, 0, 0, 2),
(6, 2, 'cat_id', '栏目', 'cat_id', '仅限于在同一模型上更换内容所属栏目', 1, 1, 0, 1),
(7, 2, 'title', '标题', 'text', '', 1, 1, 0, 0),
(8, 2, 'description', '描述', 'textarea', '', 1, 0, 0, 3),
(9, 2, 'content', '产品介绍', 'editor', '', 0, 0, 0, 5),
(10, 2, 'author', '作者', 'text', '', 0, 0, 0, 2),
(11, 3, 'content', '内容', 'editor', '', 0, 0, 0, 0),
(12, 2, 'image', '产品图片', 'image', '点击右边的的上传按钮,上传成功后图片的地址会自动插入文本框内', 0, 0, 0, 4),
(13, 2, 'size', '产品尺寸', 'text', '', 0, 0, 0, 0),
(14, 2, 'serial_number', '产品编号', 'text', '', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_module_resource`
--
DROP TABLE IF EXISTS `{table_prefix}_module_resource`;
CREATE TABLE `{table_prefix}_module_resource` (
  `mr_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `mr_name` char(20) NOT NULL,
  `mr_comment` char(20) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `version` char(50) NOT NULL,
  `is_core` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `add_time` datetime NOT NULL,
  `list_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mr_id`),
  UNIQUE KEY `mr_name` (`mr_name`),
  KEY `list_order` (`list_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `{table_prefix}_module_resource`
--

INSERT INTO `{table_prefix}_module_resource` (`mr_id`, `mr_name`, `mr_comment`, `type`, `version`, `is_core`, `add_time`, `list_order`, `is_hidden`, `disabled`) VALUES
(1, 'user', '用户管理', 0, '1.0', 1, '2011-03-06 16:16:18', 122, 0, 0),
(2, 'news', '新闻管理', 1, '', 1, '2011-03-06 16:45:33', 12, 0, 0),
(3, 'product', '产品管理', 1, '', 1, '2011-03-06 16:45:33', 2, 0, 0),
(4, 'page', '单页', 1, '1.0', 1, '2011-04-30 10:23:05', 0, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_safe_times`
--
DROP TABLE IF EXISTS `{table_prefix}_safe_times`;
CREATE TABLE `{table_prefix}_safe_times` (
  `action` char(20) NOT NULL,
  `ip` char(15) NOT NULL,
  `time_at` int(10) unsigned NOT NULL DEFAULT '0',
  `times` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`action`,`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=gbk;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_user`
--
DROP TABLE IF EXISTS `{table_prefix}_user`;
CREATE TABLE `{table_prefix}_user` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `company_uid` char(20) NOT NULL,
  `user_name` char(30) NOT NULL,
  `user_pass` char(32) NOT NULL,
  `salt` char(16) NOT NULL,
  `group_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `login_count` int(11) unsigned NOT NULL DEFAULT '0',
  `login_ip` char(15) NOT NULL,
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `content_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  KEY `company_id` (`company_id`),
  KEY `company_uid` (`company_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `{table_prefix}_user_group`
--
DROP TABLE IF EXISTS `{table_prefix}_user_group`;
CREATE TABLE `{table_prefix}_user_group` (
  `group_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` char(20) NOT NULL,
  `mr_ids` text NOT NULL,
  `post_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_super` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` mediumint(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`),
  UNIQUE KEY `group_name` (`group_name`),
  KEY `company_id` (`company_id`),
  KEY `is_system` (`is_system`)
) ENGINE=MyISAM  DEFAULT CHARSET=gbk AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `{table_prefix}_user_group`
--

INSERT INTO `{table_prefix}_user_group` (`group_id`, `group_name`, `mr_ids`, `post_time`, `is_system`, `is_super`, `company_id`) VALUES
(1, '超级管理员', '', 1301309799, 1, 1, 0),
(2, '公司管理员', '1,2,3', 1301309806, 1, 0, 0),
(3, '公司编辑员', '2,3', 1301309843, 1, 0, 0);