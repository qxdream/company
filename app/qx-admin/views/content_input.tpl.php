<?php
/* 
 [QXDream] content_add.tpl.php 2011-05-15
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<script type="text/javascript">
$(function(){
		$('.btn_file_upload').click(function() {
				show('<?php echo app_url() . 'attachment/add/editor_id/'; ?>' + $(this).attr('editor_id') + '/', $(this).attr('data') + ' - <?php echo $admin_language['file_upload']; ?>');
		});
		$('.btn_picture_upload').click(function() {
				show('<?php echo app_url() . 'attachment/add/type/2/pic_id/'; ?>' + $(this).attr('pic_id') + '/', $(this).attr('data') + ' - <?php echo $admin_language['picture_upload']; ?>');
		});
})
</script>
<h2 class="m_b_16 font_14 f_l"><?php echo $page_title; ?></h2>
<div class="clear"></div>
<form action="<?php echo get_frm_url('model_id/' . $_GET['model_id'] . '/' . (isset($_GET['cat_id']) ? 'cat_id/' . $_GET['cat_id'] . '/' : '')); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<?php if(3 == $_GET['model_id']) { ?>
				<tr>
						<td width="20%"><?php echo $admin_language['title']; ?></td>
						<td><?php echo $CATEGORY[$_GET['cat_id']]['cat_name']; ?></td>
				</tr>
				<?php
				}
				foreach($field_data as $k => $v) {
				if(1 == $v['disabled']) { continue; }
				?>
				<tr>
						<td width="20%">
							<?php echo $v['field_comment']; ?>
							<?php if(!empty($v['tips'])) { ?>
							<p class="grey"><?php echo $v['tips']; ?></p>
							<?php } ?>
						</td>
						<td>
								<?php
								$default = isset($content_data[$v['field_name']]) ? $content_data[$v['field_name']] : '';
								echo Form::$v['type']($v['field_name'], $v['field_comment'], $default, $v['is_system']);
								if(1 == $v['is_require']) {
								?>
									&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i>
								<?php
								}
								?>
						</td>
				</tr>
				<?php } ?>
		</tbody>
</table>
<p style="padding-bottom: 25px;">
		<input type="hidden" name="data_post" value="ok" />
		<input type="submit" name="btn_release" id="btn_release" class="btn_style btn_4" value="<?php if(!isset($content_data['status']) || $content_data['status'] == 3) { echo $admin_language['content_release']; } else { echo $admin_language['update_content']; } ?>" />
		<?php if(!isset($content_data['status']) || $content_data['status'] == 3) { ?>
		<input type="submit" name="btn_save" id="btn_save" style="margin-left: 10px;" class="btn_style btn_4" value="<?php echo $admin_language['content_save']; ?>" />
		<?php } ?>
</p>
</form>
<?php
View::display('footer');
?>
</body>
</html>
