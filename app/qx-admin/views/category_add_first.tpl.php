<?php
/* 
 [QXDream] category_add_first.tpl.php 2010-02-03
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_16 font_14"><?php echo $admin_language['select_category_type']; ?></h2>
<form action="<?php echo get_frm_url(); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><?php echo $admin_language['parent_category']; ?><p class="grey"><?php echo $admin_language['select_category_intro']; ?></p></td>
						<td>
								<select name="parent_category" id="parent_cat">
										<option value="0" model="0"><?php echo $admin_language['plese_select_category']; ?></option>
										<?php echo $option_str; ?>
								</select>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['category_type']; ?></td>
						<td>
								<p><label><input name="type" value="0" checked="checked" type="radio">&nbsp;&nbsp;<?php echo $admin_language['content_category']; ?></label></p>
								<p><label><input name="type" value="1" type="radio">&nbsp;&nbsp;<?php echo $admin_language['lonely_page']; ?></label></p>
								<p><label><input name="type" value="2" type="radio">&nbsp;&nbsp;<?php echo $admin_language['external_link']; ?></label>&nbsp;&nbsp;&nbsp;<i>(外部链接不允许有子类)</i></p>
						</td>
				</tr>
		</tbody>
</table>
<input type="hidden" value="2" name="step" />
<input type="hidden" id="model_input" value="<?php if(isset($_GET['model'])) { echo $_GET['model']; } ?>" name="model_id" />
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['next_step']; ?>" /></p>
</form>
<script type="text/javascript">
function loadSelectedModel() {
	var model_id = ($('#parent_cat option:selected').attr('model_id'));
	$('#model_input').val(model_id);
}
$('#parent_cat').change(function() {
	loadSelectedModel();
});
loadSelectedModel();
</script>
<?php
View::display('footer');
?>
</body>
</html>
