<?php
/* 
 [QXDream] category.tpl.php 2011-03-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $admin_language['user_manage']; ?></h2>
<div class="m_b_10"><?php echo $guide; ?></div>
<form action="" method="post" id="form_submit">
<table id="content" class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" />
						<th><?php echo $admin_language['user']; ?></th>
						<?php if(1 == $qx_group_id) { ?>
						<th><?php echo $admin_language['company']; ?></th>
						<?php } ?>
						<th><?php echo $admin_language['role']; ?></th>
						<th><?php echo $admin_language['login_count']; ?></th>
						<th><?php echo $admin_language['last_login_ip']; ?></th>
						<th><?php echo $admin_language['last_login_time']; ?></th>
						<th width="7%"><?php echo $admin_language['content']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($user_data)) { 
					foreach($user_data as $k => $v) { 
				?>
				<tr>
						<td><input type="checkbox" class="record_box check_box" name="user_id[]" value="<?php echo $v['user_id']; ?>"<?php if($v['user_id'] == $GLOBALS['QXDREAM']['qx_user_id'] || is_creator($v['user_id'])) { echo ' disabled="disabled"'; } ?> /></td>
						<td>
								<a class="title" href="<?php echo app_url() . 'user/edit/user_id/' . $v['user_id']; ?>"><?php echo $v['user_name']; ?></a>
								<div class="btn_func">
										<a href="<?php echo app_url() . 'user/edit/user_id/' . $v['user_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
									<?php if($v['user_id'] != $GLOBALS['QXDREAM']['qx_user_id'] && !is_creator($v['user_id'])) { ?>
										<a onClick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'user/delete/user_id/' . $v['user_id']; ?>"><?php echo $admin_language['delete']; ?></a> | 
										<?php if(empty($v['disabled'])) { ?><a href="<?php echo app_url() . 'user/disable/user_id/' . $v['user_id'] . '/value/1/'; ?>"><?php echo $admin_language['disable']; ?></a>
										<?php } else { ?> 
										<a class="red" href="<?php echo app_url() . 'user/disable/user_id/' . $v['user_id'] . '/value/0/'; ?>"><?php echo $admin_language['enable']; ?></a>
										<?php } 
									} else {
										echo '<span class="grey">' . $admin_language['delete'] . '</span> | <span class="grey">' . $admin_language['disable'] . '</span>';
									}
									?>
								</div>
						</td>
						<?php if(1 == $qx_group_id) { ?>
						<td><a href="<?php echo app_url() . 'user/index/company_id/' . $v['company_id']; ?>"><?php echo $v['company_name']; ?></a></td>
						<?php } ?>
						<td><?php echo !empty($v['role']) ? $v['role'] : $admin_language['other']; ?></td>
						<td class="georgia_num"><?php echo $v['login_count']; ?></td>
						<td><?php echo $v['login_ip']; ?></td>
						<td><?php echo $v['login_time']; ?></td>
						<td class="georgia_num">
						<?php if(empty($v['content_count']) || 1 == $qx_group_id) { echo $v['content_count']; } else { echo '<a href="' . app_url() . 'contentAll/index/user_id/' . $v['user_id'] . '/">' . $v['content_count'] . '</a>'; } ?>
						</td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" />
						<th><?php echo $admin_language['user']; ?></th>
						<?php if(1 == $qx_group_id) { ?>
						<th><?php echo $admin_language['company']; ?></th>
						<?php } ?>
						<th><?php echo $admin_language['role']; ?></th>
						<th><?php echo $admin_language['login_count']; ?></th>
						<th><?php echo $admin_language['last_login_ip']; ?></th>
						<th><?php echo $admin_language['last_login_time']; ?></th>
						<th width="7%"><?php echo $admin_language['content']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<select name="batch_select" id="batch_select">
		<option value="choose" selected="selected"><?php echo $admin_language['batch_action']; ?></option>
		<option value="batch_delete"><?php echo $admin_language['delete']; ?></option>
		<?php if(!isset($_GET['type']) || 'all' == $_GET['type'] || '1' == $_GET['type']) { ?>
		<option value="batch_disable/value/0"><?php echo $admin_language['enable']; ?></option>
		<?php 
		}
		if(!isset($_GET['type']) || 'all' == $_GET['type'] || '0' == $_GET['type']) { 
		?>
		<option value="batch_disable/value/1"><?php echo $admin_language['disable']; ?></option>
		<?php
		}
		?>
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