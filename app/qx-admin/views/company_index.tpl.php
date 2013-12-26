<?php
/* 
 [QXDream] category.tpl.php 2011-03-12
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<h2 class="m_b_10 font_14"><?php echo $admin_language['company_list']; ?></h2>
<table class="table_data_box m_b_10">
		<thead>
				<tr>
						<th width="100"><?php echo $admin_language['company_id']; ?></th>
						<th width="180"><?php echo $admin_language['company_name']; ?></th>
						<th><?php echo $admin_language['company_uid']; ?></th>
						<th><?php echo $admin_language['hit']; ?></th>
						<th width="100"><?php echo $admin_language['add_time']; ?></th>
				</tr>
		</thead>
		<tbody>
				<?php
				if(is_array($company_data)) { 
					foreach($company_data as $k => $v) { 
				?>
				<tr>
						<td><?php echo $v['company_id']; ?></td>
						<td>
								<a class="title" href="<?php echo app_url() . 'company/edit/company_id/' . $v['company_id']; ?>"><?php echo $v['company_name']; ?></a>
								<div class="btn_func">
										<a href="<?php echo app_url() . 'company/edit/company_id/' . $v['company_id']; ?>"><?php echo $admin_language['edit']; ?></a> | 
										<a onClick="return confirm('<?php echo $admin_language['check_company_delete']; ?>');" href="<?php echo app_url() . 'company/delete/company_id/' . $v['company_id']; ?>"><?php echo $admin_language['delete']; ?></a> | 
										<?php if(empty($v['disabled'])) { ?><a href="<?php echo app_url() . 'company/disable/company_id/' . $v['company_id'] . '/value/1/'; ?>"><?php echo $admin_language['disable']; ?></a>
										<?php } else { ?> 
										<a class="red" href="<?php echo app_url() . 'company/disable/company_id/' . $v['company_id'] . '/value/0/'; ?>"><?php echo $admin_language['enable']; ?></a>
										<?php } ?>
								</div>
						</td>
						<td><?php echo $v['company_uid']; ?></td>
						<td><?php echo $v['hits_count']; ?></td>
						<td class="grey"><?php echo format_date('Y/m/d', $v['post_time']); ?></td>
				</tr>
				<?php
					} 
				}
				?>
		</tbody>
		<tfoot>
				<tr>
						<th><?php echo $admin_language['company_id']; ?></th>
						<th><?php echo $admin_language['company_name']; ?></th>
						<th><?php echo $admin_language['company_uid']; ?></th>
						<th><?php echo $admin_language['hit']; ?></th>
						<th><?php echo $admin_language['add_time']; ?></th>
				</tr>
		</tfoot>
</table>
<div class="page f_r"><?php echo $page_nav; ?></div>
<?php
View::display('footer');
?>
</body>
</html>
