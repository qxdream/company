<?php
/* 
 [QXDream] index.php 2011-02-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
View::display('header');
?>
<body id="login_page">
<script type="text/javascript">
//防止被框架引用
if (top.location != self.location) top.location=self.location;
</script>
<p id="go_index"><a href="<?php echo QX_PATH; ?>">&larr; 返回 倾行</a></p>
<div id="form_container">
		<form name="user_form" id="user_form" action="<?php echo app_url() . 'login/'; ?>" method="post">
				<?php if(isset($prompt)) { ?>
				<p id="prompt_p"><span id="<?php echo $prompt_id; ?>"><?php echo $prompt; ?></span></p>
				<?php } ?>
				<p id="user_p">
						<label for="user_name"><?php echo $admin_language['user_name']; ?>:</label>
						<input type="text" name="user_name" id="user_name" class="text_box" value="<?php if(isset($_POST['user_name']) && ($error_type > 1 || isset($not_filled) && $not_filled > 0)) { echo $_POST['user_name']; } ?>" size="20" />
				</p>
				<p>			
						<label for="user_pass"><?php echo $admin_language['password']; ?>:</label>
						<input type="password" name="user_pass" id="user_pass" class="text_box" value="<?php if(isset($_POST['user_pass']) && isset($not_filled) && $not_filled > 1) { echo $_POST['user_pass']; } ?>" size="20" />
				</p>
				<?php if(1 == $enable_id_code) { ?>
				<p id="code_p">
						<label for="id_code"><?php echo $admin_language['id_code']; ?>:</label>
						<input name="id_code" id="id_code" class="text_box" size="4" maxlength="4" type="text" value="<?php if(isset($_POST['id_code']) && isset($not_filled) && $not_filled > 2) { echo $_POST['id_code']; } ?>" />
						<img id="code_img" height="38" src="<?php echo app_url('id-code') . 'IdCode/output/'; ?>" alt="<?php echo $admin_language['unclear_refresh']; ?>" />
				</p>
				<script type="text/javascript">
				document.getElementById('code_img').onclick = function() {
					this.src = this.src + '?';
					document.getElementById('id_code').focus();
				}
				</script>
				<?php } ?>			
				<p id="btn_p">
						<input type="submit" name="btn_submit" id="btn_submit" class="btn_style f_r btn_login" value="<?php echo $admin_language['login']; ?>" tabindex="100" />
				</p>
		</form>
</div>
<script type="text/javascript">
<?php
if(isset($not_filled)) {
	if($not_filled < 2) { $focus_id = 'user_name'; } elseif($not_filled < 3) { $focus_id = 'user_pass'; } elseif($not_filled < 4) { $focus_id = 'id_code'; }
} else {
	$focus_id = $error_type <= 1 ? 'user_name' : 'user_pass';
}
?>
try { document.getElementById('<?php echo $focus_id; ?>').focus(); } catch(e) {}
</script>
<?php echo exec_info(); ?>
</body>
</html>