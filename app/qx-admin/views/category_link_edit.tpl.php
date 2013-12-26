<?php
/* 
 [QXDream] category_link_edit.tpl.php 2010-02-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_16 font_14"><?php echo $admin_language['category_edit']; ?></h2>
<form action="<?php echo app_url() . 'category/edit/cat_id/' . $cat_data['cat_id'] . '/'; ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><?php echo $admin_language['parent_category']; ?><p class="grey"><?php echo $admin_language['select_category_intro']; ?></p></td>
						<td>
								<select name="category[parent_id]">
										<option value="0"><?php echo $admin_language['plese_select_category']; ?></option>
										<?php echo $option_str; ?>
								</select>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['external_link_name']; ?></td>
						<td>
								<input type="text" name="category[cat_name]" class="text_box" size="20" value="<?php echo $cat_data['cat_name']; ?>" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['external_link_url']; ?></td>
						<td><input type="text" name="category[url]" class="text_box" size="20" value="<?php echo $cat_data['url']; ?>" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['l_keywords']; ?><p class="grey"><?php echo $admin_language['l_keywords_intro']; ?></p></td>
						<td><input type="text" name="category_setting[seo_keywords]" class="text_box" size="20" value="<?php echo $cat_data['seo_keywords']; ?>" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['l_description']; ?><p class="grey"><?php echo $admin_language['l_description_intro']; ?></p></td>
						<td>
								<p style="margin-top: 5px;"><textarea name="category_setting[seo_description]" rows="3" cols="50" style="width: 93%; height: 50px;"><?php echo $cat_data['seo_description']; ?></textarea></p>
						</td>
				</tr>
		</tbody>
</table>
<input type="hidden" name="category[is_nav]" value="1" />
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" /></p>
</form>
<?php
View::display('footer');
?>
</body>
</html>
