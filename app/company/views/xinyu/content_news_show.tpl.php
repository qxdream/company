<?php defined('IN_QX') or die('<h1>Forbidden!</h1>'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo '����ʾ - ' . $company_data['company_name']; ?></title>
</head>

<body>
<h1><?php echo '����ʾ - ' . $company_data['company_name']; ?></h1>
<h2><a href="<?php echo SITE_URL; ?>">INDEX</a></h2>
<p>
<?php if('index' == $_GET['method']) { ?><strong>��˾����</strong><?php } else { ?><a href="<?php echo $company_url; ?>">��˾����</a><?php } ?> |
<?php if('content_product' == $_GET['method']) { ?><strong>��Ʒչʾ</strong><?php } else { ?><a href="<?php echo $company_url . 'content_product/'; ?>">��Ʒչʾ</a><?php } ?>
</p>

<h3><?php echo $content_data['title']; ?></h3>
<p><?php echo format_date('Y-m-d H:i:s', $content_data['post_time'], 0) ?> �����ˣ�<?php echo $content_data['author']; ?></p>
<div><?php echo $content_data['content']; ?></div>
<?php
View::display('powered_by');
?>
</body>
</html>
