<?php
/* 
 [QXDream] company_input.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="operation_page">
<h2 class="m_b_16 font_14"><?php echo $page_title; ?></h2>
<form action="<?php if('add' == $_GET['method']) { echo app_url() . 'company/add/'; } elseif('edit' == $_GET['method']) { echo app_url() . 'company/edit/company_id/' . $_GET['company_id'] . '/'; } ?>" method="post">
<table class="table_no_border m_b_10">
		<tbody>
				<tr>
						<td width="20%"><?php echo $admin_language['company_name']; ?></td>
						<td><input type="text" name="company[company_name]" class="text_box" value="<?php if(isset($company_data['company_name'])) { echo $company_data['company_name']; } ?>" size="20" style="width: 300px;" />&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i></td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['company_uid']; ?><p class="grey"><?php echo $admin_language['company_uid_intro']; ?></p></td>
						<td>
							<label><input name="create_default_category" value="1" <?php if(!$create_default_category) echo 'disabled="disabled"' ?>type="checkbox" checked="checked" />&nbsp;&nbsp;<?php echo $admin_language['create_defaut_category']; ?></label>
							<p style="margin-top: 5px;"><input type="text" name="company[company_uid]" class="text_box" value="<?php if(isset($company_data['company_uid'])) { echo $company_data['company_uid']; } ?>" size="20" style="width: 300px;"<?php if('edit' == $_GET['method']) { echo 'disabled="disabled"'; } ?> /><?php if('edit' != $_GET['method']) { ?>&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i><?php } ?></p>
						</td>
				</tr>	
				<?php
				if(isset($include_user_input) && $include_user_input) { View::display('user_input'); }
				?>
				<tr>
						<td width="20%"><?php echo $admin_language['company_use_module']; ?></td>
						<td>
								<?php 
								$i = 0;
								if(isset($company_data['mr_ids'])) { $mr_ids_arr = explode(',', $company_data['mr_ids']); }
								foreach($module_resource_data as $k => $v) {
									$i++;
									if(1 == $v['is_core']) {
								?>
										<label><input type="checkbox" name="company[mr_id][]" disabled="disabled" checked="checked" /> <?php echo $v['mr_comment']; ?></label>&nbsp;
										<input type="hidden" name="company[mr_id][]" value="<?php echo $v['mr_id']; ?>" />
								<?php 
									} else {
								?>
										<label><input type="checkbox" name="company[mr_id][]" value="<?php echo $v['mr_id']; ?>"<?php if(isset($company_data['mr_ids']) && in_array($v['mr_id'], $mr_ids_arr)) { echo ' checked="checked"'; } ?> /> <?php echo $v['mr_comment']; ?></label>&nbsp;
								<?php 
									}
									if(0 == $i % 4) { echo '<br />'; }
								} 
								?>
						</td>
				</tr>
				<tr>
						<td><?php echo $admin_language['company_ICP']; ?><p class="grey"><?php echo $admin_language['ICP_intro']; ?></p></td>
						<td><input type="text" name="company[icp_no]" class="text_box" value="<?php if(isset($company_data['icp_no'])) { echo $company_data['icp_no']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['company_keywords']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td><input type="text" name="company[keywords]" class="text_box" value="<?php if(isset($company_data['keywords'])) { echo $company_data['keywords']; } ?>" size="20" style="width: 300px;" /></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['company_description']; ?><p class="grey"><?php echo $admin_language['site_for_seo']; ?></p></td>
						<td><textarea name="company[description]" rows="3" cols="50" style="width: 93%; height: 50px;"><?php if(isset($company_data['description'])) { echo $company_data['description']; } ?></textarea></td>
				</tr>
				<tr>
						<td><?php echo $admin_language['company_copyright_info']; ?></p><p class="grey"><?php echo $admin_language['copryight_info_intro']; ?></p></td>
						<td><textarea name="company[copyright]" id="copyright" cols="60" rows="4" style="width: 93%; height: 50px;"><?php if(isset($company_data['copyright'])) { echo $company_data['copyright']; } ?></textarea></td>
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