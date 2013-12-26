<?php
/* 
 [QXDream] setting_index.tpl.php 2011-03-26
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $admin_language['site_setting']; ?></h2>
<form action="" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<?php if(1 == $qx_group_id) { ?>
				<tr>
						<td><?php echo $admin_language['default_timezone']; ?><p class="grey"><?php echo $admin_language['default_timezone_intro']; ?></p></td>
						<td>
								<select name="config[TIMEOFFSET]">
										<option value="-12"<?php if(TIMEOFFSET == -12) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-12']; ?></option>
										<option value="-11"<?php if(TIMEOFFSET == -11) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-11']; ?></option>
										<option value="-10"<?php if(TIMEOFFSET == -10) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-10']; ?></option>
						
										<option value="-9"<?php if(TIMEOFFSET == -9) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-9']; ?></option>
										<option value="-8"<?php if(TIMEOFFSET == -8) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-8']; ?></option>
										<option value="-7"<?php if(TIMEOFFSET == -7) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-7']; ?></option>
										<option value="-6"<?php if(TIMEOFFSET == -6) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-6']; ?></option>
										<option value="-5"<?php if(TIMEOFFSET == -5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-5']; ?></option>
										<option value="-4"<?php if(TIMEOFFSET == -4) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-4']; ?></option>
						
										<option value="-3.5"<?php if(TIMEOFFSET == -3.5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-3.5']; ?></option>
										<option value="-3"<?php if(TIMEOFFSET == -3) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-3']; ?></option>
										<option value="-2"<?php if(TIMEOFFSET == -2) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-2']; ?></option>
										<option value="-1"<?php if(TIMEOFFSET == -1) echo ' selected="selected"';?>><?php echo $language['timeoffset']['-1']; ?></option>
										<option value="0"<?php if(TIMEOFFSET == 0) echo ' selected="selected"';?>><?php echo $language['timeoffset']['0']; ?></option>
										<option value="1"<?php if(TIMEOFFSET == 1) echo ' selected="selected"';?>><?php echo $language['timeoffset']['1']; ?></option>
						
										<option value="2"<?php if(TIMEOFFSET == 2) echo ' selected="selected"';?>><?php echo $language['timeoffset']['2']; ?></option>
										<option value="3"<?php if(TIMEOFFSET == 3) echo ' selected="selected"';?>><?php echo $language['timeoffset']['3']; ?></option>
										<option value="3.5"<?php if(TIMEOFFSET == 3.5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['3.5']; ?></option>
										<option value="4"<?php if(TIMEOFFSET == 4) echo ' selected="selected"';?>><?php echo $language['timeoffset']['4']; ?></option>
										<option value="4.5"<?php if(TIMEOFFSET == 4.5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['4.5']; ?></option>
										<option value="5"<?php if(TIMEOFFSET == 5) echo ' selected="selected"';?>>(<?php echo $language['timeoffset']['5']; ?></option>
						
										<option value="5.5"<?php if(TIMEOFFSET == 5.5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['5.5']; ?></option>
										<option value="6"<?php if(TIMEOFFSET == 6) echo ' selected="selected"';?>><?php echo $language['timeoffset']['6']; ?></option>
										<option value="7"<?php if(TIMEOFFSET == 7) echo ' selected="selected"';?>><?php echo $language['timeoffset']['7']; ?></option>
										<option value="8"<?php if(TIMEOFFSET == 8) echo ' selected="selected"';?>><?php echo $language['timeoffset']['8']; ?></option>
										<option value="9"<?php if(TIMEOFFSET == 9) echo ' selected="selected"';?>><?php echo $language['timeoffset']['9']; ?></option>
										<option value="9.5"<?php if(TIMEOFFSET == 9.5) echo ' selected="selected"';?>><?php echo $language['timeoffset']['9.5']; ?></option>
						
										<option value="10"<?php if(TIMEOFFSET == 10) echo ' selected="selected"';?>><?php echo $language['timeoffset']['10']; ?></option>
										<option value="11"<?php if(TIMEOFFSET == 11) echo ' selected="selected"';?>><?php echo $language['timeoffset']['11']; ?></option>
										<option value="12"<?php if(TIMEOFFSET == 12) echo ' selected="selected"';?>><?php echo $language['timeoffset']['12']; ?></option>
							</select>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['admin_login_overtime']; ?><p class="grey"><?php echo $admin_language['admin_login_overtime_intro']; ?></p></td>
						<td>
								<label><input type="text" name="config[OVERTIME]" class="text_box" value="<?php echo OVERTIME; ?>" size="5" />&nbsp;&nbsp;<?php echo $language['second']; ?></label>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['open_apache_rewrite']; ?><p class="grey"><?php echo $admin_language['open_apache_rewrite_intro']; ?></p></td>
						<td>
								<label><input name="config[REWRITE]" value="TRUE" <?php if(REWRITE) echo 'checked="checked"' ?>type="radio">&nbsp;&nbsp;<?php echo $admin_language['yes']; ?></label>&nbsp;&nbsp;
								<label><input name="config[REWRITE]" value="FALSE" <?php if(!REWRITE) echo 'checked="checked"' ?>type="radio">&nbsp;&nbsp;<?php echo $admin_language['no']; ?></label>
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['system_style']; ?></td>
						<td>
								<select name="config[ADMIN_PLAN]">
										<option value="admin_default"<?php if('admin_default' == ADMIN_PLAN) echo ' selected="selected"'; ?>><?php echo $admin_language['admin_default']; ?></option>
										<option value="admin_fade_timer"<?php if('admin_fade_timer' == ADMIN_PLAN) echo ' selected="selected"'; ?>><?php echo $admin_language['admin_fade_timer']; ?></option>
								</select>
						</td>
				</tr>
				<?php } else { ?>
				<tr>
						<td width="20%"><?php echo $admin_language['company_name']; ?></td>
						<td><input type="text" name="company[company_name]" class="text_box" value="<?php if(isset($company_data['company_name'])) { echo $company_data['company_name']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['site_name']; ?><p class="grey"><?php echo $admin_language['site_name_intro']; ?></p></td>
						<td><input type="text" name="company[site_name]" class="text_box" value="<?php if(isset($company_data['site_name'])) { echo $company_data['site_name']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['site_keywords']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td><input type="text" name="company[keywords]" class="text_box" value="<?php if(isset($company_data['keywords'])) { echo $company_data['keywords']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['site_description']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td><textarea name="company[description]" rows="3" cols="50" style="width: 93%; height: 50px;"><?php if(isset($company_data['description'])) { echo $company_data['description']; } ?></textarea></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['copyright_info']; ?></p><p class="grey"><?php echo $admin_language['copryight_info_intro']; ?></p></td>
						<td><textarea name="company[copyright]" id="copyright" cols="60" rows="4" style="width: 93%; height: 50px;"><?php if(isset($company_data['copyright'])) { echo $company_data['copyright']; } ?></textarea></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['ICP']; ?><p class="grey"><?php echo $admin_language['ICP_intro']; ?></p></td>
						<td><input type="text" name="company[icp_no]" class="text_box" value="<?php if(isset($company_data['icp_no'])) { echo $company_data['icp_no']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<?php } ?>
		</tbody>
</table>
<p style="padding-bottom: 25px;"><input type="submit" name="btn_submit" id="btn_submit" class="btn_style" value="<?php echo $admin_language['submit']; ?>" /></p>
</form>
<?php
View::display('footer');
?>
</body>
</html>