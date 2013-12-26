<?php defined('IN_QX') or die('<h1>Forbidden!</h1>'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title><?php echo $title; ?></title>
</head>

<body>
<h1>Demo - 公司列表</h1>
<ol>
<?php
foreach($company_data as $k => $v) {
?>
<li><a href="<?php echo app_url() . $v['company_uid'] . '/'; ?>"><?php echo $v['company_name']; ?></a></li>
<?php
}
?>
</ol>
<?php
View::display('powered_by');
?>
</body>
</html>
