<?php
/* 
 [QXDream] field_input.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $page_title; ?></h2>
<div class="m_b_10 ccc">
	<a href="<?php echo app_url() . 'field/index/model_id/' . $_GET['model_id']; ?>" class="btn_a_12">×Ö¶Î¹ÜÀí</a> | 
	<a href="<?php echo app_url() . 'field/add/model_id/' . $_GET['model_id']; ?>" class="btn_a_12">×Ö¶ÎÌí¼Ó</a>
</div>
<form action="<?php echo get_frm_url('model_id/' . $_GET['model_id']); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><?php echo $admin_language['field_name']; ?><p class="grey"><?php echo $admin_language['field_name_intro']; ?></p></td>
						<td>
							<?php if('edit' == $_GET['method']) { 
								echo $field_data['field_name'];
							} else {
							?>
							<input type="text" name="field[field_name]" class="text_box" value="<?php if(isset($field_data['field_name'])) { echo $field_data['field_name']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i>
							<?php 
							} 
							?>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['field_comment']; ?></td>
						<td><input type="text" name="field[field_comment]" class="text_box" value="<?php if(isset($field_data['field_comment'])) { echo $field_data['field_comment']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['field_tips']; ?><p class="grey"><?php echo $admin_language['field_tips_intro']; ?></p></td>
						<td><textarea name="field[tips]" rows="3" cols="50" style="width: 93%; height: 50px;"><?php if(isset($field_data['tips'])) { echo $field_data['tips']; } ?></textarea></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['type']; ?></td>
						<td>
							<?php if('edit' == $_GET['method']) { 
								echo $field_type_data[$field_data['type']];
							} else {
							?>
							<select name="field[type]">
								<?php 
								foreach($field_type_data as $k => $v) { 
								if('cat_id' == $k) { continue; }
								?>
								<option value="<?php echo $k; ?>"<?php if(isset($field_data['type']) && $v['type'] == $field_data['type']) { echo ' selected="selected"'; } ?>><?php echo $v; ?></option>
								<?php } ?>
							</select>
							<?php } ?>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['is_require']; ?></td>
						<td>
							<label><input name="field[is_require]" value="1" <?php if(!isset($field_data['is_require']) || isset($field_data['is_require']) && 1 == $field_data['is_require']) { echo ' checked="checked"'; } ?>type="radio">&nbsp;&nbsp;<?php echo $admin_language['yes']; ?></label>&nbsp;&nbsp;
							<label><input name="field[is_require]" value="0" <?php if(!isset($field_data['is_require']) || isset($field_data['is_require']) && 0 == $field_data['is_require']) { echo ' checked="checked"'; } ?>type="radio">&nbsp;&nbsp;<?php echo $admin_language['no']; ?></label>&nbsp;&nbsp;
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