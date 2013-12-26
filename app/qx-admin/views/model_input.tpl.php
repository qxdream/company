<?php
/* 
 [QXDream] model_input.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $page_title; ?></h2>
<form action="<?php echo get_frm_url(); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><?php echo $admin_language['model_name']; ?><p class="grey"><?php echo $admin_language['model_name_intro']; ?></p></td>
						<td><input type="text" name="model[model_name]" class="text_box" value="<?php if(isset($model_data['model_name'])) { echo $model_data['model_name']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['model_comment']; ?></td>
						<td><input type="text" name="model[model_comment]" class="text_box" value="<?php if(isset($model_data['model_comment'])) { echo $model_data['model_comment']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
		</tbody>
</table>
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" /></p>
</form>
<?php
View::display('footer');
?>
</body>
</html>