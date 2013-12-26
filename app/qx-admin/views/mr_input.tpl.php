<?php
/* 
 [QXDream] mr_input.tpl.php 2011-03-06
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
						<td width="20%"><?php echo $admin_language['mr_name']; ?><p class="grey"><?php echo $admin_language['mr_name_intro']; ?></p></td>
						<td><input type="text" name="mr[mr_name]" class="text_box" value="<?php if(isset($mr_data['mr_name'])) { echo $mr_data['mr_name']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['mr_comment']; ?></td>
						<td><input type="text" name="mr[mr_comment]" class="text_box" value="<?php if(isset($mr_data['mr_comment'])) { echo $mr_data['mr_comment']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['type']; ?></td>
						<td>
							<?php echo $admin_language['module']; ?>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['version']; ?></td>
						<td><input type="text" name="mr[version]" class="text_box" value="<?php if(isset($mr_data['version'])) { echo $mr_data['version']; } ?>" size="20" style="width: 300px;" /></td>
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