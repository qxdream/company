<?php
/* 
 [QXDream] content_index.tpl.php 2011-05-04
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<style type="text/css">
</style>
<h2 class="m_b_10 font_14"><?php echo $page_title; ?></h2>
<div style="overflow: hidden;" class="m_b_10" >
<form class="f_r" method="get" action="<?php echo app_url() . $_GET['control'] . '/index/' . ('contentAll' == $_GET['control'] ? '' : 'model_id/' . $_GET['model_id'] . '/' . (isset($_GET['cat_id']) ? 'cat_id/' . $_GET['cat_id'] . '/' : '')); ?>" name="search_form" id="search_form">
		<input style="margin-right: 10px;" type="text" name="search_box" id="search_box" class="text_box" value="<?php echo $search_key; ?>" />
		<input type="submit" name="search_btn" id="search_btn" class="btn_style" value="<?php echo $admin_language['search']; ?>" />
</form>

<div>
	<?php if(isset($_GET['model_id']) && isset($_GET['cat_id']) && 'contentAll' != $_GET['control']) { ?>
	<span class="ccc">
	<a href="<?php echo app_url() . 'content/add/model_id/' . $_GET['model_id'] . '/cat_id/' . $_GET['cat_id'] . '/'; ?>" class="btn_a_12"><?php echo $admin_language['content_add']; ?></a> |
	</span>
	<?php 
	}
	echo $guide; 
	?>
	</div>
</div>
<form action="" method="post" id="form_submit">
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th>
						<th><?php echo $admin_language['title']; ?></th>
						<th width="10%"><?php echo $admin_language['post_user']; ?></th>
						<?php if('contentAll' == $_GET['control']) { ?>
						<th width="10%"><?php echo $admin_language['model']; ?></th>
						<?php } ?>
						<th width="10%"><?php echo $admin_language['category_dir']; ?></th>
						<th width="8%"><?php echo $admin_language['hit']; ?></th>
						<th width="120"><?php echo $admin_language['date']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($content_data)) { 
					foreach($content_data as $k => $v) { 
				?>
				<tr>
						<td><?php if(3 != $v['model_id']) { ?><input type="checkbox" class="record_box check_box" name="content_id[]" value="<?php echo $v['content_id']; ?>" /> <?php } else { ?><input type="checkbox" class="record_box check_box" name="content_id[]" class="record_box check_box" disabled="disabled" /><?php } ?></td>
						<td>
								<span class="title">
									<?php if(3 != $v['model_id']) { ?>
									<a href="<?php echo app_url() . 'content/edit/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $v['title']; ?></a>
									<?php } else { 
										echo $CATEGORY[$v['cat_id']]['cat_name'];
									}
									?>
								</span>
								<div class="btn_func">
									<?php 
									if(3 != $v['model_id']) {
										if($status == 1 || $status ==3) { ?>
										<!-- 修改 -->
										<a href="<?php echo app_url() . 'content/edit/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<?php } ?>
										
										<?php if($status ==3) { ?>
										<!-- 发布 -->
										<a href="<?php echo app_url() . 'content/status/value/1/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $admin_language['post']; ?></a> |
										<?php } ?>
										
										<?php if($status == 1 || $status ==3) { ?>
										<!-- 移至回收站 -->
										<a href="<?php echo app_url() . 'content/status/value/2/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $admin_language['move_to_recycle_bin']; ?></a>
										<?php } ?>
										
										<?php if($status ==2) { ?>
										<!-- 还原 -->
										<a href="<?php echo app_url() . 'content/status/value/1/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $admin_language['recover']; ?></a> |
										<!-- 彻底删除 -->
										<a href="<?php echo app_url() . 'content/really_delete/model_id/' . $v['model_id'] . '/content_id/' . $v['content_id']; ?>"><?php echo $admin_language['really_delete']; ?></a>
										<?php 
										} 
									} else {
										echo '<span class="grey">' . $admin_language['edit'] . ' | ' . $admin_language['move_to_recycle_bin'] . '</span>';
									}
									?>
								</div>
						</td>
						<td><?php if(empty($v['user_name'])) { echo $admin_language['user_not_exists']; } else { echo '<a href="' . app_url() . $_GET['control'] . '/index/model_id/' . $v['model_id'] . (isset($_GET['cat_id']) ? '/cat_id/' . $v['cat_id'] : '') . '/user_id/' . $v['user_id'] . '/">' . $v['user_name'] . '</a>'; } ?></td>
						<?php if('contentAll' == $_GET['control']) { echo '<td>' . $GLOBALS['QXDREAM']['MODEL'][$v['model_id']]['model_comment'] . '</td>'; } ?>
						<td><?php if(!isset($CATEGORY[$v['cat_id']]) && $v['cat_id'] != 0) { echo $admin_language['none_or_delete']; } elseif($v['cat_id'] == 0) { echo '<a href="' . app_url() . $_GET['control'] . '/index/model_id/' . $v['model_id'] . '/cat_id/0/">' . $admin_language['not_take_category'] . '</a>'; } else { echo '<a href="' . app_url() . $_GET['control'] . '/index/model_id/' . $v['model_id'] . '/cat_id/' . $v['cat_id'] . '/">' . $CATEGORY[$v['cat_id']]['cat_name'] . '</a>'; } ?></td>
						<td><?php echo $v['hits_count']; ?></td>
						<td class="grey"><?php echo format_date('Y/m/d', $v['time']); ?><p><?php echo $v['date_info']; ?></p></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="30"><input class="check_all check_box" type="checkbox" /></th>
						<th><?php echo $admin_language['title']; ?></th>
						<th><?php echo $admin_language['post_user']; ?></th>
						<?php if('contentAll' == $_GET['control']) { ?>
						<th width="10%"><?php echo $admin_language['model']; ?></th>
						<?php } ?>
						<th><?php echo $admin_language['category_dir']; ?></th>
						<th><?php echo $admin_language['hit']; ?></th>
						<th><?php echo $admin_language['date']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<select name="batch_select" id="batch_select">
		<option value="choose" selected="selected"><?php echo $admin_language['batch_action']; ?></option>
		<?php if($status == 3) { ?>
		<option value="batch_status/<?php echo isset($_GET['model_id']) ? 'model_id/' . $_GET['model_id'] . '/' : ''; ?>value/1"><?php echo $admin_language['post']; ?></option>
		<?php } ?>
		<?php if($status == 2) { ?>
		<option value="batch_status/<?php echo isset($_GET['model_id']) ? 'model_id/' . $_GET['model_id'] . '/' : ''; ?>value/1"><?php echo $admin_language['recover']; ?></option>
		<option value="batch_really_delete/<?php echo isset($_GET['model_id']) ? 'model_id/' . $_GET['model_id'] : ''; ?>"><?php echo $admin_language['really_delete']; ?></option>
		<?php } ?>
		<?php if($status == 1 || $status ==3) { ?>
		<option value="batch_status/<?php echo isset($_GET['model_id']) ? 'model_id/' . $_GET['model_id'] . '/' : ''; ?>value/2"><?php echo $admin_language['move_to_recycle_bin']; ?></option>
		<?php } ?>
</select>
<input type="submit" style="margin: 0 5px;" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['application']; ?>" />
<?php if($status == 2) { ?>
<input type="button" id="empty_recycle_bin" class="btn_style btn_4" value="<?php echo $admin_language['empty_recycle_bin']; ?>" />
<?php } ?>
</form>
<script type="text/javascript">
//<![CDATA[
$('#empty_recycle_bin').click(function() {
		if(!confirm('<?php echo $admin_language['confirm_empty_recycle_bin']; ?>')) {
				return false;
		}
		location.href = '<?php echo app_url() . $_GET['control'] . '/empty_all/' . (isset($_GET['model_id']) ? 'model_id/' . $_GET['model_id'] . '/' : '') . (isset($_GET['cat_id']) ? 'cat_id/' . $_GET['cat_id'] . '/' : ''); ?>';
});
$('#form_submit').submit(function() {
		if($('#batch_select').val() == 'choose') {
			alert('<?php echo $language['choose_action']; ?>');
			return false;
		}
		if($('#batch_select').val() != 'list_order') { //不等于排序时才判断,至少选择一条记录
				var n = 0;
				for(var i = 0; i < $('.record_box').length; i++){
						if($('.record_box').eq(i).attr('checked') == true) n++;
				}
				if(n == 0) {
					alert('<?php echo $language['one_list_need']; ?>');
					return false;
				}
		}
		if($('#batch_select').val() == 'really_delete') return confirm('<?php echo $language['check_delete']; ?>');
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