<?php
/* 
 [QXDream] attachment_pic_add.tpl.php 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<style type="text/css">
#btn, #btn2 { margin-right: 8px; }
#operation_page { padding-right: 14px; }
</style>
<div id="attach_content">
<form class="m_b_10" action="<?php echo get_frm_url('type/2/pic_id/' . $_GET['pic_id'] . '/'); ?>" method="post" enctype="multipart/form-data">
<div><i><?php echo $admin_language['a_file_cannot_beyond'], round(UPLOAD_MAXSIZE / (1024 * 1024)) . 'M, ' . $admin_language['allow_picture_type']; ?></i></div>
<div id="attach_c" class="m_b_16">
		<p id="attach"><?php echo $admin_language['attachment']; ?>: <input type="file" name="upfile" /></p>
</div>
<p>
		<input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" />
</p>
</form>
</div>
<!--[if IE]>
<style type="text/css">
html { overflow-y: auto; }
</style>
<![endif]-->
</body>
</html>
