<?php
/* 
 [QXDream] category_link_add.tpl.php 2011-04-30
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_16 font_14"><?php echo $admin_language['category_add']; ?></h2>
<form action="<?php echo get_frm_url(); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><input type="hidden" name="category[parent_id]" value="<?php echo $_POST['parent_category']; ?>" /><?php echo $admin_language['parent_category']; ?></td>
						<td><?php if($_POST['parent_category'] == 0) { echo $admin_language['top_category']; } else { echo $CATEGORY[$_POST['parent_category']]['cat_name']; } ?></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['external_link_name']; ?></td>
						<td>
								<input type="text" name="category[cat_name]" class="text_box" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['external_link_url']; ?></td>
						<td><input type="text" name="category[url]" class="text_box" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['l_keywords']; ?><p class="grey"><?php echo $admin_language['l_keywords_intro']; ?></p></td>
						<td><input type="text" name="category_setting[seo_keywords]" class="text_box" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['l_description']; ?><p class="grey"><?php echo $admin_language['l_description_intro']; ?></p></td>
						<td>
								<p style="margin-top: 5px;"><textarea name="category_setting[seo_description]" rows="3" cols="50" style="width: 93%; height: 50px;"></textarea></p>
						</td>
				</tr>
		</tbody>
</table>
<input type="hidden" name="category[type]" value="<?php echo $_POST['type'] ?>" />
<input type="hidden" name="category[is_nav]" value="1" />
<input type="hidden" name="step" value="ok" />
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" /></p>
</form>
<?php
View::display('footer');
?>
</body>
</html>
