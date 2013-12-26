<?php
/* 
 [QXDream] field_index.tpl.php 2011-04-16
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_10 font_14"><?php echo $page_title; ?></h2>
<div class="m_b_10 ccc">
	<a href="<?php echo app_url() . 'field/index/model_id/' . $_GET['model_id']; ?>" class="btn_a_12"><?php echo $admin_language['field_manage']; ?></a> | 
	<a href="<?php echo app_url() . 'field/add/model_id/' . $_GET['model_id']; ?>" class="btn_a_12"><?php echo $admin_language['field_add']; ?></a>
</div>
<form action="" method="post" id="form_submit">
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="30"><?php echo $admin_language['list_order']; ?></th>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['field_name']; ?></th>
						<th><?php echo $admin_language['field_comment']; ?></th>
						<th><?php echo $admin_language['type']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
						<th><?php echo $admin_language['is_require']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($field_data)) { 
					foreach($field_data as $k => $v) { 
				?>
				<tr>
						<td width="30"><input style="width: 40px;" type="text" class="text_box" name="list_order[<?php echo $v['field_id']; ?>]" value="<?php echo $v['list_order']; ?>" /></td>
						<td><?php echo $v['field_id']; ?></td>
						<td>
								<a class="title" href="<?php echo app_url() . 'field/edit/field_id/' . $v['field_id'] . '/model_id/' . $_GET['model_id']; ?>"><?php echo $v['field_name']; ?></a>
								<div class="btn_func">
									<?php if(0 == $v['is_system']) { ?>
										<a href="<?php echo app_url() . 'field/edit/field_id/' . $v['field_id'] . '/model_id/' . $_GET['model_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<a onClick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'field/delete/field_id/' . $v['field_id'] . '/model_id/' . $_GET['model_id']; ?>"><?php echo $admin_language['delete']; ?></a> | 
										<?php if(empty($v['disabled'])) { ?><a href="<?php echo app_url() . 'field/disable/field_id/' . $v['field_id'] . '/model_id/' . $_GET['model_id'] . '/value/1/'; ?>"><?php echo $admin_language['disable']; ?></a>
										<?php } else { ?> 
										<a class="red" href="<?php echo app_url() . 'field/disable/field_id/' . $v['field_id'] . '/model_id/' . $_GET['model_id'] . '/value/0/'; ?>"><?php echo $admin_language['enable']; ?></a>
										<?php } 
									} else {
										echo '<span class="grey">' . $admin_language['edit'] . '</span> | <span class="grey">' . $admin_language['delete'] . '</span> | <span class="grey">' . $admin_language['disable'] . '</span>';
									}
									?>
								</div>
						</td>
						<td><?php echo $v['field_comment']; ?></td>
						<td><?php echo $type_data[$v['type']]; ?></td>
						<td><?php if(0 == $v['is_system']) { echo '<span class="red">×</span>'; } else { echo '<span class="green">√</span>'; } ?></td>
						<td><?php if(0 == $v['is_require']) { echo '<span class="red">×</span>'; } else { echo '<span class="green">√</span>'; } ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="30"><?php echo $admin_language['list_order']; ?></th>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['field_name']; ?></th>
						<th><?php echo $admin_language['field_comment']; ?></th>
						<th><?php echo $admin_language['type']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
						<th><?php echo $admin_language['is_require']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<select name="batch_select" id="batch_select">
		<option value="choose" selected="selected"><?php echo $admin_language['batch_action']; ?></option>
		<option value="batch_list_order"><?php echo $admin_language['list_order']; ?></option>
</select>
<input type="submit" style="margin: 0 5px;" name="btn_submit" id="btn_submit" class="btn_style"  value="<?php echo $admin_language['application']; ?>" />
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
	var frm_url = $(this).val() != 'choose' ? '<?php echo app_url() . $_GET['control'] . '/'; ?>' + $(this).val() + '/<?php echo 'model_id/' . $_GET['model_id'] . '/ '?>' : '';
	$('#form_submit').attr('action', frm_url);
});
//载入时
var frm_url = $('#batch_select').val() != 'choose' ? '<?php echo app_url() . $_GET['control'] . '/'; ?>' + $('#batch_select').val() + '/<?php echo 'model_id/' . $_GET['model_id'] . '/ '?>' : '';
$('#form_submit').attr('action', frm_url);
//]]>
</script>
<?php
View::display('footer');
?>
</body>
</html>