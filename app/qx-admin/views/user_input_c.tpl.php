<?php
/* 
 [QXDream] company_input.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $page_title; ?></h2>
<form action="<?php echo get_frm_url(); ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<?php
				View::display('user_input');
				//公司列表显示条件：
				//1、增加时，即没有设置过$user_data['user_id']；
				//2、超级管理员并且不是修改当前这个超级管理员密码时，修改本身的密码时公司项不用修改。
				if(1 == $GLOBALS['QXDREAM']['qx_group_id'] && (!isset($user_data['user_id']) || $GLOBALS['QXDREAM']['qx_user_id'] != $user_data['user_id'])) {
				?>
				<tr id="in_company">
						<td width="20%"><?php echo $admin_language['in_company']; ?></td>
						<td>
							<?php 
							if(!empty($user_data['company_id']) && $GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) {
								echo $company_data[$user_data['company_id']]['company_name'];
							} else {
							?>
							<select name="user[company_id]" id="company_sel">
								<option value="-100">==<?php echo $admin_language['select_company']; ?>==</option>
								<?php 
								if(is_array($company_data)) { 
									foreach($company_data as $k => $v) {
								?>
								<option value="<?php echo $v['company_id']; ?>"<?php if(isset($user_data['company_id']) && $v['company_id'] == $user_data['company_id']) { echo ' selected="selected"'; } ?>><?php echo $v['company_name']; ?></option>
								<?php
									}
								}
								?>
							</select>
							<?php } ?>
						</td>
				</tr>
				<?php
				}
				?>
				<tr>
						<td width="20%"><?php echo $admin_language['role']; ?></td>
						<td>
							<?php 
							if(isset($user_data['user_id']) && $GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) {
								echo $role_data[$user_data['group_id']]['group_name'];
							} else {
							?>
							<select name="user[group_id]" id="sel_role">
								<?php 
								if(is_array($role_data)) {
									foreach($role_data as $k => $v) {
								?>
								<option value="<?php echo $v['group_id']; ?>"<?php if(isset($v['is_super']) && 1 == $v['is_super']) { echo ' id="super"'; } if(isset($user_data['group_id']) && $v['group_id'] == $user_data['group_id']) { echo ' selected="selected"'; } ?>><?php echo $v['group_name']; ?></option>
								<?php
									}
								}
								?>
							</select>
							<script type="text/javascript">
							function role_load() {
								if('super' == $('#sel_role option:selected').attr('id')) {
									$('#in_company').hide().find('#company_sel').attr('disabled', 'disabled');
								} else {
									$('#in_company').show().find('#company_sel').removeAttr('disabled');
								}
							}
							$('#sel_role').bind('change', function() { role_load(); });
							role_load();
							</script>
							<?php
							}
							?>
						</td>
				</tr>
		</tbody>
</table>
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" /></p>
</form>
<?php
View::display('footer');
?>
</body>
</html>