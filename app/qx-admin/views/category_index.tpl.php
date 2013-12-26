<?php
/* 
 [QXDream] category.tpl.php 2010-04-26
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_10 font_14"><?php echo $admin_language['category_list']; ?></h2>
<?php if($_GET['parent_id'] != 0) { ?><p style="margin-bottom: 5px;"><span class="grey"><?php echo $admin_language['current_position']; ?>: </span><?php echo $cat_pos; ?></p><?php } ?>
<form action="" method="post" id="form_submit">
<table id="article" class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th><th width="30"><?php echo $admin_language['list_order']; ?></th><th width="40">ID</th><th><?php echo $admin_language['category_dir']; ?></th><th><?php echo $admin_language['category_type']; ?></th><th><?php echo $admin_language['bind_model']; ?></th><th><?php echo $admin_language['navigation']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($category_data)) { 
					foreach($category_data as $k => $v) { 
				?>
				<tr>
						<td><input type="checkbox" class="record_box check_box" name="cat_id[]" value="<?php echo $v['cat_id']; ?>" /></td>
						<td width="30"><input style="width: 40px;" type="text" class="text_box" name="list_order[<?php echo $v['cat_id']; ?>]" value="<?php echo $v['list_order']; ?>" /></td>
						<td><?php echo $v['cat_id']; ?></td>
						<td>
								<a class="title" href="<?php echo app_url() . 'category/edit/cat_id/' . $v['cat_id'] . '/'; ?>"><?php echo $v['cat_name']; ?></a>
								<div class="btn_func">
										<?php if($v['has_child'] == 1) { ?>
										<a href="<?php echo app_url() . 'category/index/parent_id/' . $v['cat_id'] . '/'; ?>"><?php echo $admin_language['child_category']; ?></a>
										<?php } else { echo '<span class="grey">' . $admin_language['child_category'] . '</span>'; } ?> | 
										<?php if($v['type'] == 2) { ?>
										<?php echo  '<span class="grey">' . $admin_language['add_child_category'] . '</span>'; } else { ?>
										<a href="<?php echo app_url() . 'category/add/parent_id/' . $v['cat_id'] . '/model_id/' . $v['model_id'] . '/'; ?>"><?php echo $admin_language['add_child_category']; ?></a>
										<?php } ?> | 
										<?php if($v['type'] == 0 && $v['has_child'] != 1) { ?>
										<a href="<?php echo app_url() . 'content/add/model_id/' . $v['model_id'] . '/cat_id/' . $v['cat_id'] . '/'; ?>">添加新内容</a> | 
										<?php } elseif($v['type'] == 1) { ?>
										<a href="<?php echo app_url() . 'content/edit/model_id/' . $v['model_id'] . '/cat_id/' . $v['cat_id'] . '/'; ?>"><?php echo $admin_language['edit_content']; ?></a> | 
										<?php } ?>
										<a href="<?php echo app_url() . 'category/edit/cat_id/' . $v['cat_id'] . '/model_id/' . $v['model_id'] . '/'; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<a onClick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'category/delete/cat_id/' . $v['cat_id'] . '/'; ?>"><?php echo $admin_language['delete']; ?></a>
								</div>
						</td>
						<td><?php echo $v['type_name']; ?></td>
						<td><?php echo $v['model_comment']; ?></td>
						<td><?php if($v['is_nav'] == 1) { echo '<span class="red">' . $admin_language['yes'] . '</span>'; } else { echo $admin_language['no']; } ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th><th width="30"><?php echo $admin_language['list_order']; ?></th><th width="40">ID</th><th><?php echo $admin_language['category_dir']; ?></th><th><?php echo $admin_language['category_type']; ?></th><th><?php echo $admin_language['bind_model']; ?></th><th><?php echo $admin_language['navigation']; ?></th>
				</tr>
		</tfoot>
</table>
<select name="batch_select" id="batch_select">
		<option value="choose" selected="selected"><?php echo $admin_language['batch_action']; ?></option>
		<option value="batch_delete"><?php echo $admin_language['delete']; ?></option>
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
