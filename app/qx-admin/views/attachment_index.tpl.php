<?php
/* 
 [QXDream] attachment_index.tpl.php 2011-05-08
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_16 font_14"><?php echo $admin_language['attachment_list']; ?></h2>
<form action="?action=attachment&amp;operation=batch" method="post" id="form_submit">
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th>
						<th width="80"><?php echo $admin_language['display']; ?></th>
						<th><?php echo $admin_language['resource_attachment_name']; ?></th>
						<th><?php echo $admin_language['attachment_info']; ?></th>
						<th><?php echo $admin_language['upload_time']; ?></th>
						<th><?php echo $admin_language['in_content']; ?></th>
						<th><?php echo $admin_language['upload_user']; ?></th>
						<th><?php echo $admin_language['attachment_thumb']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($attachment_data)) { 
					foreach($attachment_data as $k => $v) { 
				?>
				<tr>
						<td><input type="checkbox" class="record_box check_box" name="attachment_id[]" value="<?php echo $v['attachment_id']; ?>" /></td>
						<!-- 没有 -->
						<td style="vertical-align: top;"><?php echo create_img(QX_PATH . $v['thumb'], '', (empty($v['has_thumb']) ? '' : (isset($v['thumb_width']) && $v['thumb_width'] < 80 ? $v['thumb_width'] : 80))); ?></td>
						<td>
								<span class="title"><a href="<?php echo QX_PATH . $v['file_path']; ?>" target="_blank"><?php echo $v['filename']; ?></a></span>
								<div class="btn_func">
										<a onClick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'attachment/delete/attachment_id/' . $v['attachment_id']; ?>"><?php echo $admin_language['delete']; ?></a>
								</div>
						</td>
						<td>
								<?php echo $admin_language['size'] . ': ' . size($v['file_size']); if($v['is_image'] == 1) { echo " <i class='blue'>({$admin_language['picture']})</i>"; } else { echo  " <i class='grey'>({$admin_language['no_picture']})</i>"; } ?>
								<p><?php echo $admin_language['type'] . ': ' . $v['file_type']; ?></p>
						</td>
						<td><?php echo format_date('Y/m/d', $v['post_time']); ?></td>
						<td><?php if(empty($v['title'])) { echo $admin_language['none']; } else { echo '<a href="' . app_url() . 'content/edit/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id'] . '/">' . $admin_language['resource_content'] . '</a>'; } ?></td>
						<td><?php if(empty($v['user_name'])) { echo $admin_language['user_not_exists']; } else { echo $v['user_name']; } ?></td>
						<td><?php if(empty($v['has_thumb'])) { echo $admin_language['none']; } else { echo '<span class="red">' . $admin_language['has'] . '</span>'; } ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th>
						<th><?php echo $admin_language['display']; ?></th>
						<th><?php echo $admin_language['resource_attachment_name']; ?></th>
						<th><?php echo $admin_language['attachment_info']; ?></th>
						<th><?php echo $admin_language['upload_time']; ?></th>
						<th><?php echo $admin_language['in_content']; ?></th>
						<th><?php echo $admin_language['upload_user']; ?></th>
						<th><?php echo $admin_language['attachment_thumb']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<select name="batch_select" id="batch_select">
		<option value="choose" selected="selected"><?php echo $admin_language['batch_action']; ?></option>
		<option value="batch_delete"><?php echo $admin_language['delete']; ?></option>
</select>
<input type="submit" style="margin: 0 5px;" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['application']; ?>" />
</form>
<script type="text/javascript">
//<![CDATA[
$('#form_submit').submit(function(){
	if($('#batch_select').val() == 'choose') {
		alert('<?php echo $language['choose_action']; ?>');
		return false;
	}
	if($('#batch_select').val() != 'batch_list_order') { //不等于排序时才判断,至少选择一条记录
		var n = 0;
		for(var i = 0; i < $('.record_box').length; i++){
			if($('.record_box').eq(i).attr('checked') == true) n++;
		}
		if(n == 0) {
			alert('<?php echo $language['one_list_need']; ?>');
			return false;
		}
	}
	if($('#batch_select').val() == 'delete') return confirm('<?php echo $language['check_delete']; ?>');
});
//批量删除,改变form的action中的控制器
$('#batch_select').change(function() {
	var frm_url = $(this).val() != 'choose' ? '<?php echo app_url() . $_GET['control'] . '/'; ?>' + $(this).val() + '/' : '';
	$('#form_submit').attr('action', frm_url);
});
//载入时
var frm_url = $('#batch_select').val() != 'choose' ? '<?php echo app_url() . $_GET['control'] . '/'; ?>' + $('#batch_select').val() + '/' : '';
$('#form_submit').attr('action', frm_url);
//]]>
</script>
<?php
View::display('footer');
?>
</body>
</html>