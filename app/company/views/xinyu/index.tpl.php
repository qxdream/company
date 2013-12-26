<?php defined('IN_QX') or die('<h1>Forbidden!</h1>'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo '简单演示 - ' . $company_data['company_name']; ?></title>
</head>

<body>
<h1><?php echo '简单演示 - ' . $company_data['company_name']; ?></h1>
<h2><a href="<?php echo SITE_URL; ?>">INDEX</a></h2>
<p>
<?php if('index' == $_GET['method']) { ?><strong>公司新闻</strong><?php } else { ?><a href="<?php echo $company_url; ?>">公司新闻</a><?php } ?> |
<?php if('content_product' == $_GET['method']) { ?><strong>产品展示</strong><?php } else { ?><a href="<?php echo $company_url . 'content_product/'; ?>">产品展示</a><?php } ?>
</p>
<ol>
<?php
foreach($content_data as $k => $v) {
?>
<li><a href="<?php echo app_url() . $v['company_uid'] . '/content_show/content_id/' . $v['content_id']; ?>"><?php echo $v['title']; ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<?php if(isset($CATEGORY[$v['cat_id']])) { echo $CATEGORY[$v['cat_id']]['cat_name']; } else { echo '<i>分类被删</i>'; } ?>]</li>
<?php
}
?>
</ol>
<?php
View::display('powered_by');
?>
</body>
</html>
