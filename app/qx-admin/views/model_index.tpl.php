<?php
/* 
 [QXDream] model_index.tpl.php 2011-04-16
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_10 font_14"><?php echo $admin_language['model_list']; ?></h2>
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['model_name']; ?></th>
						<th><?php echo $admin_language['model_comment']; ?></th>
						<th><?php echo $admin_language['content_count']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($model_data)) { 
					foreach($model_data as $k => $v) { 
				?>
				<tr>
						<td><?php echo $v['model_id']; ?></td>
						<td>
								<a class="title" href="<?php echo app_url() . 'model/edit/model_id/' . $v['model_id']; ?>"><?php echo $v['model_name']; ?></a>
								<div class="btn_func">
										<a href="<?php echo app_url() . 'field/index/model_id/' . $v['model_id']; ?>"><?php echo $admin_language['field_manage']; ?></a> | 
									<?php if(0 == $v['is_system']) { ?>
										<a href="<?php echo app_url() . 'model/edit/model_id/' . $v['model_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<a onclick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'model/delete/model_id/' . $v['model_id']; ?>"><?php echo $admin_language['delete']; ?></a> | 
										<?php if(empty($v['disabled'])) { ?><a href="<?php echo app_url() . 'model/disable/model_id/' . $v['model_id'] . '/value/1/'; ?>"><?php echo $admin_language['disable']; ?></a>
										<?php } else { ?> 
										<a class="red" href="<?php echo app_url() . 'model/disable/model_id/' . $v['model_id'] . '/value/0/'; ?>"><?php echo $admin_language['enable']; ?></a>
										<?php } 
									} else {
										echo '<span class="grey">' . $admin_language['edit'] . '</span> | <span class="grey">' . $admin_language['delete'] . '</span> | <span class="grey">' . $admin_language['disable'] . '</span>';
									}
									?>
								</div>
						</td>
						<td><?php echo $v['model_comment']; ?></td>
						<td class="georgia_num"><?php echo $v['content_count']; ?></td>
						<td><?php if(0 == $v['is_system']) { echo '<span class="red">¡Á</span>'; } else { echo '<span class="green">¡Ì</span>'; } ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['model_name']; ?></th>
						<th><?php echo $admin_language['model_comment']; ?></th>
						<th><?php echo $admin_language['content_count']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>

<?php
View::display('footer');
?>
</body>
</html>