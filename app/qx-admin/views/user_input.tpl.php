<?php
/* 
 [QXDream] user_input.tpl.php 2011-03-06
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
?>	
				<tr>
						<?php if('edit' == $_GET['method']) { ?>
						<td width="20%"><?php echo $admin_language['user_name']; ?></td>
						<td>
							<?php echo $user_data['user_name']; ?>
						</td>
						<?php } else { ?>
						<td width="20%"><?php echo $admin_language['user_name']; ?><p class="grey"><?php echo $language['user_name_not_beyond_30_len']; ?></p></td>
						<td>
							<input type="text" name="user[user_name]" class="text_box" value="<?php if(isset($user_data['user_name'])) { echo $user_data['user_name']; } ?>" size="20" maxlength="30" style="width: 300px;" />
							&nbsp;&nbsp;&nbsp;<i>(<?php echo $admin_language['must_fill']; ?>)</i>
						</td>
						<?php } ?>
				</tr>
				<?php if('edit' == $_GET['method'] && $GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) { ?>
				<tr>
						<td width="20%"><?php echo $admin_language['password_old']; ?></td>
						<td>
							<input type="password" name="user[password_old]" class="text_box" size="20" style="width: 300px;" />
							&nbsp;&nbsp;&nbsp;<i>(<?php if($GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) { echo $admin_language['must_fill']; } else { echo $language['not_change_password_intro']; }?>)
						</td>
				</tr>
				<?php } ?>
				<tr>
						<td width="20%"><?php echo $admin_language['password']; ?></td>
						<td>
							<input type="password" name="user[user_pass]" class="text_box" size="20" style="width: 300px;" />
							&nbsp;&nbsp;&nbsp;<i>(<?php if('edit' != $_GET['method'] || $GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) { echo $admin_language['must_fill']; } else { echo $language['not_change_password_intro']; }?>)
						</td>
				</tr>
				<tr>
						<td width="20%"><?php echo $admin_language['password_again']; ?><p class="grey"><?php echo $language['password_must_same']; ?></p></td>
						<td>
							<input type="password" name="user[password_again]" class="text_box" size="20" style="width: 300px;" />
							&nbsp;&nbsp;&nbsp;<i>(<?php if('edit' != $_GET['method'] || $GLOBALS['QXDREAM']['qx_user_id'] == $user_data['user_id']) { echo $admin_language['must_fill']; } else { echo $language['not_change_password_intro']; } ?>)
						</td>
				</tr>