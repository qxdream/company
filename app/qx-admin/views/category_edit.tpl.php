<?php
/* 
 [QXDream] category_edit.tpl.php 2010-02-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_16 font_14"><?php echo $admin_language['category_edit']; ?></h2>
<form action="<?php echo app_url() . 'category/edit/cat_id/' . $cat_data['cat_id'] . '/model_id/' . $cat_data['model_id'] . '/'; ?>" method="post">
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
						<td width="20%"><?php echo $admin_language['bind_model']; ?></td>
						<td>
								<?php if($cat_data['content_count'] > 0) { ?>
								<span><?php echo get_type_name($cat_data['type']); ?></span>(<span class="grey"><?php echo lang('category_exists_data'); ?></span>)
								<?php } else { ?>
								<select name="category[model_id]">
									<?php 
									foreach($MODEL as $k => $v) { 
									if('1' == $v['is_hidden']) { continue; }
									$model_name = $GLOBALS['QXDREAM']['MODEL'][$v['model_id']]['model_name'];
									if(!in_array($GLOBALS['QXDREAM']['MR'][$model_name]['mr_id'], explode(',', $GLOBALS['QXDREAM']['qx_mr_ids']))) { continue; }
									?>
									<option value="<?php echo $v['model_id']; ?>"<?php if($cat_data['model_id'] == $v['model_id']) { echo 'selected="selected"'; } ?>><?php echo $v['model_comment']; ?></option>
									<?php } ?>
								</select>
								<?php } ?>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['category_name']; ?></td>
						<td>
								<input type="text" name="category[cat_name]" class="text_box" size="20" value="<?php echo $cat_data['cat_name']; ?>" style="width: 300px;" />
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['category_keywords']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td><input type="text" name="category_setting[seo_keywords]" class="text_box" size="20" value="<?php echo $cat_data['seo_keywords']; ?>" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['category_description']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td>
								<label>
										<!-- 为导航是选中的,不为导航时不选中,但提交表单时接到不到不选择的值,所以提交时要判断,没有得到这个值,把这个值设置为0 -->
										<input name="category[is_nav]" value="1" type="checkbox"<?php if($cat_data['is_nav'] == 1) echo ' checked="checked"'; ?>>&nbsp;&nbsp;<?php echo $admin_language['set_navigation']; ?>
								</label>
								<p style="margin-top: 5px;"><textarea name="category_setting[seo_description]" rows="3" cols="50" style="width: 93%; height: 50px;"><?php echo $cat_data['seo_description']; ?></textarea></p>
						</td>
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
