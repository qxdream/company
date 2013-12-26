<?php
/* 
 [QXDream] home.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>

<body id="operation_page">
<style type="text/css">
#left,
#right { width: 49%; }
#left { margin-right: 2%; }
#recent_comment .gravatar_face img { margin-right: 12px; }
#recent_comment td,
#recent_comment th { vertical-align: top; }
.gravatar_face { margin-top: 5px; }
</style>
<div id="left" class="f_l">
		<!-- 环境信息 -->
		<table class="table_data_box m_b_20">
				<thead>
						<tr>
								<th colspan="2"><?php echo $admin_language['system_setting']; ?></th>	
						</tr>
				</thead>
				<tbody>
						<tr>
								<td width="45%"><?php echo $admin_language['server_time']; ?></td><td class="violet"><?php echo now(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['server_engine']; ?></td><td class="red"><?php echo software(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['gd_library']; ?></td><td class="violet"><?php echo gd_version(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['memory_usage']; ?></td><td class="blue_green"><?php echo user_memory_size(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['mysql_version']; ?></td><td class="red"><?php echo $mysql_version; ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['cur_db_size']; ?></td><td class="blue_green"><?php echo $db_size; ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['os_and_php']; ?></td><td class="red"><?php echo PHP_OS . ' / V' . phpversion(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['file_upload']; ?></td><td class="violet"><?php echo is_file_uploads(); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['register_global_vars']; ?></td><td><?php echo php_cfg('register_globals'); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['safe_mode_status']; ?></td><td><?php echo php_cfg('safe_mode'); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['url_fopen_status']; ?></td><td><?php echo php_cfg('allow_url_fopen'); ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['db_runtime']; ?></td><td class="blue_green"><?php echo $mysql_runtime; ?></td>
						</tr>
				</tbody>
		</table>
</div>
<div id="right" class="f_l">
		<!-- 数据统计 -->
		<table class="table_data_box m_b_20">
				<thead>
						<tr>
								<th colspan="2"><?php echo $admin_language['data_statistics']; ?></th>	
						</tr>
				</thead>
				<tbody>
						<tr>
								<?php if($qx_group_id > 1) { 
									if(2 == $qx_group_id) { 
									?>
									<td><a href="<?php echo app_url() . 'contentAll/'; ?>"><?php echo $admin_language['content']; ?></a> <a href="<?php echo app_url() . 'contentAll/'; ?>" class="georgia_num"><?php echo $content_count; ?></a></td>
									<?php } else { ?>
									<td><?php echo $admin_language['content']; ?> <span class="georgia_num"><?php echo $content_count; ?></span></td>
								<?php 
									}
								} else { ?>
								<td><a href="<?php echo app_url() . 'company/'; ?>"><?php echo $admin_language['company']; ?></a> <a class="georgia_num" href="<?php echo app_url() . 'company/'; ?>"><?php echo $company_count; ?></a></td>
								<?php } ?>
								<td><a href="<?php echo app_url() . 'user/'; ?>"><?php echo $admin_language['user']; ?></a> <a class="georgia_num" href="<?php echo app_url() . 'user/'; ?>"><?php echo $user_count; ?></a></td>
						</tr>
				</tbody>
		</table>
		<!-- 角色信息 -->
		<table class="table_data_box m_b_20">
				<thead>
						<tr>
								<th colspan="4"><?php echo $admin_language['role_info']; ?></th>	
						</tr>
				</thead>
				<tbody>
						<tr>
								<td><?php echo $admin_language['account']; ?></td><td><a href="<?php echo app_url() . 'user/edit/user_id/' . $GLOBALS['QXDREAM']['qx_user_id']; ?>"><?php echo $qx_user_name; ?></a></td><td><?php echo $admin_language['login_time']; ?></td><td class="violet"><?php echo $qx_login_time; ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['login_ip']; ?></td><td class="violet"><?php echo $qx_login_ip; ?></td><td><?php echo $admin_language['login_count']; ?></td><td class="georgia_num grey"><?php echo $qx_login_count; ?></td>
						</tr>
				</tbody>
		</table>
		<!-- 程序相关 -->
		<table class="table_data_box m_b_20">
				<thead>
						<tr>
								<th colspan="2"><?php echo $admin_language['related_program']; ?></th>	
						</tr>
				</thead>
				<tbody>
						<tr>
								<td><?php echo $admin_language['current_version']; ?></td><td class="red"><?php echo 'QXDream Mutiuser V' . PRO_VERSION; ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['program_dev']; ?></td><td class="blue"><?php echo DEVELOPER; ?></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['homepage']; ?></td><td><a href="<?php echo DEVELOPER_HOMEPAGE; ?>" target="_blank"><?php echo DEVELOPER_HOMEPAGE; ?></a></td>
						</tr>
						<tr>
								<td><?php echo $admin_language['admin_style']; ?></td><td><?php echo $admin_theme['name']; ?></td>
						</tr>
				</tbody>
		</table>
</div>
<?php
View::display('footer');
?>
</body>
</html>
