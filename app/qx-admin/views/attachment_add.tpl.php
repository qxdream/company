<?php
/* 
 [QXDream] attachment_add.tpl.php 2010-05-10
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
<form class="m_b_10" action="<?php echo get_frm_url('editor_id/' . $_GET['editor_id'] . '/'); ?>" method="post" enctype="multipart/form-data">
<div><i><?php echo $admin_language['a_file_cannot_beyond'], round(UPLOAD_MAXSIZE / (1024 * 1024)); ?>M</i></div>
<div id="attach_c" class="m_b_16">
		<p id="attach"><?php echo $admin_language['attachment']; ?>: <input type="file" name="upfile[]" /></p>
</div>
<p>
		<input type="button" name="btn" id="btn" class="btn_style" value="<?php echo $admin_language['add']; ?>" />
		<input type="button" name="btn2" id="btn2" class="btn_style" value="<?php echo $admin_language['reduce']; ?>" />
		<input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" />
</p>
</form>
</div>
<script type="text/javascript">
var attach_c = document.getElementById('attach_c');
document.getElementById('btn').onclick = function() {
		//克隆超过4个,即不允许再增加
		if(document.getElementById('attach_c').getElementsByTagName('p').length > 4) return false;
		var newNode = document.getElementById('attach').cloneNode(true);
		attach_c.appendChild(newNode);
}
document.getElementById('btn2').onclick = function() {
		var i = 0;
		for(var j = 0; j < attach_c.childNodes.length; j++) {
				if(!attach_c.childNodes[j].nodeValue) {
						i++;
				}
		} 
		i > 1 ? attach_c.removeChild(attach_c.lastChild) : false;
}
</script>
<!--[if IE]>
<style type="text/css">
html { overflow-y: auto; }
</style>
<![endif]-->
</body>
</html>