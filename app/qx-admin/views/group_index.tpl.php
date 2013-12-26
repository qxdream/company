<?php
/* 
 [QXDream] group_index.tpl.php 2011-03-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_10 font_14"><?php echo $admin_language['group_list']; ?></h2>
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['group_name']; ?></th>
						<th><?php echo $admin_language['use_module']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
						<th width="100"><?php echo $admin_language['add_time']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($group_data)) { 
					foreach($group_data as $k => $v) { 
				?>
				<tr>
						<td><?php echo $v['group_id']; ?></td>
						<td>
								<?php if(0 == $v['is_system']) { ?>
								<a class="title" href="<?php echo app_url() . 'group/edit/group_id/' . $v['group_id']; ?>"><?php echo $v['group_name']; ?></a>
								<?php
								} else {
									echo '<span class="title">' . $v['group_name'] . '</span>';
								}
								?>
								<div class="btn_func">
									<?php if(0 == $v['is_system']) { ?>
										<a href="<?php echo app_url() . 'group/edit/group_id/' . $v['group_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<a onClick="return confirm('<?php echo $language['check_delete']; ?>');" href="<?php echo app_url() . 'group/delete/group_id/' . $v['group_id']; ?>"><?php echo $admin_language['delete']; ?></a>
									<?php
									} else {
										echo '<span class="grey">' . $admin_language['edit'] . '</span> | <span class="grey">' . $admin_language['delete'] . '</span>';
									}
									?>
								</div>
						</td>
						<td><?php echo $v['use_module']; ?></td>
						<td><?php if(0 == $v['is_system']) { echo '<span class="red">¡Á</span>'; } else { echo '<span class="green">¡Ì</span>'; } ?></td>
						<td class="grey"><?php echo $v['post_time']; ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th width="50">ID</th>
						<th width="200"><?php echo $admin_language['group_name']; ?></th>
						<th><?php echo $admin_language['use_module']; ?></th>
						<th><?php echo $admin_language['is_system']; ?></th>
						<th width="100"><?php echo $admin_language['add_time']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<?php
View::display('footer');
?>
</body>
</html>