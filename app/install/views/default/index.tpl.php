<?php
/* 
 [QXDream] index_index.tpl.php 2011-05-21
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo DB_CHARSET; ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?php echo $page_title; ?></title>
<style type="text/css">
body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,select,blockquote,q,th,td,p { margin: 0; padding: 0; }
body { font-size: 12px; line-height: 1.5; }
#wrapper { border: 1px solid #ccc; width: 800px; margin: 30px auto 0; padding: 2px 2px 15px; }
h1,h2 { font-size: 100%; }
h1 { background: #666; color: #fff; height: 37px; line-height: 37px; font-size: 22px; padding-left: 10px; margin-bottom: 10px; }
h2 { margin: 0 0 10px 10px; font-size: 14px; }
ol { list-style: none; }
#guide_step { overflow: hidden; font-size: 14px; color: #aaa; width: 800px; }
#guide_step li { float: left;  border-bottom: 3px solid #eee; width: 158px; margin: 0 1px; text-align: center; display: inline; }
#guide_step li span { font-size: 45px; font-style: italic; font-family: Arial; color: #ccc; }
#guide_step #current { color: #000; font-weight: bold; border-bottom-color: #d30000; }
#guide_step #current span { font-weight: normal; color: #444; }
p { margin: 0 1px 8px 10px; }
label { width: 150px; float: left; }
fieldset { border: 0; }
#progress_w { margin-left: 20px; border: 1px solid #333; width: 500px; height: 18px; }
#progress {  background: #999; height: 18px; }
a { color: #1f3a87; }
a:hover { color: #d30000; }
.title { background: #eee; padding: 5px 20px 5px 10px; font-size: 14px; font-weight: bold; margin: 35px 0 15px 0; }
.red { color: #d30000; }
.gray { color: #777; }
#footer { width: 792px; margin: 0 auto; border: 1px solid #ccc; padding: 10px 6px; border-top: 0; text-align: center; background: #fafafa; }
.btn { border: 1px solid #ccc; background: #f8f8f8; font-weight: bold; font-size: 12px; padding: 3px 3px; cursor: pointer; height: 25px; line-height: 17px; margin-top: 10px; }
input { font-family: Arial, Helvetica, sans-serif; }
table { border-collapse: collapse; margin: 0 10px; width: 780px; table-layout: fixed; }
table th, table td { border: 1px solid #ccc; padding: 5px; text-align: center; word-wrap: nowrap; word-break: break-word; }
#info { overflow: auto; margin: 35px 10px 0 10px; border: 1px solid #ccc; padding: 15px; height: 300px; }
#complete { font-size: 35px; margin: 35px 0 25px; font-family: '΢���ź�','����'; font-weight: normal; text-align: center; }
#error { margin: 35px 10px 0 10px; }
</style>
</head>
<body>
<h1><?php echo $install_Wizard; ?></h1>
<div id="wrapper">
	<ol id="guide_step">
		<li<?php if(1 == $step) { echo ' id="current"'; } ?>><span>1.</span>��װЭ��</li>
		<li<?php if(2 == $step) { echo ' id="current"'; } ?>><span>2.</span>�������</li>
		<li<?php if(3 == $step) { echo ' id="current"'; } ?>><span>3.</span>�����˻�</li>
		<li<?php if(4 == $step) { echo ' id="current"'; } ?>><span>4.</span>��װ����</li>
		<li<?php if(5 == $step) { echo ' id="current"'; } ?>><span>5.</span>��װ���</li>
	</ol>
	<div id="content">
	<?php
	switch($step) {
		case 1:
	?>
		<h2 class="title">��װЭ��</h2>
		<p>һ��QXDream Mutiuser�����ж��û���ҵ����ϵͳ��ƣ�Ӣ��ȫ��ΪQXDream Mutiuser Company System��
</p>
		<p>�������������ѧϰ�뽻��ʹ�ã�����������ҵ��;��</p>
		<form action="<?php echo app_url() . 'index/index_2/' ?>" method="post">
		<fieldset>
				<p style="text-align: center;"><input type="submit" class="btn" name="ok" value="ͬ�ⰲװ" /></p>
		</fieldset>
	</form>
	<?php
		break;
		case 2:
			if(isset($error) && !empty($error)) {
				echo $error;
			} else {
	?>
	<h2 class="title">���л���</h2>
	<table>
		<tr>
			<th width="23%">�����Ŀ</th>
			<th width="32%">��ǰ����</th>
			<th width="30%">��������</th>
			<th>�Ƿ�ͨ��</th>
		</tr>
		<?php foreach($development_data as $v) { ?>
		<tr>
			<td><?php echo $v['item']; ?></td>
			<td><?php echo $v['cur_development']; ?></td>
			<td><?php echo $v['need_setting']; ?></td>
			<td><span><img src="<?php echo SITE_URL . PUBLIC_DIR; ?>images/<?php echo $v['pass'] ? 'correct' : 'error'; ?>.gif"></span></td>		
		</tr>
		<?php } ?>
	</table>
	<h2 class="title">�ļ�Ȩ��</h2>
	<table>
		<tr>
			<th width="23%">�����Ŀ</th>
			<th width="32%">��ǰ״̬</th>
			<th width="30%">����״̬</th>
			<th>�Ƿ�ͨ��</th>
		</tr>
		<?php foreach($dir_privilege_data as $v) { ?>
		<tr>
			<td><?php echo $v['item']; ?></td>
			<td><?php echo $v['cur_status']; ?></td>
			<td><?php echo $v['need_status']; ?></td>
			<td><span><img src="<?php echo SITE_URL . PUBLIC_DIR; ?>images/<?php echo $v['pass'] ? 'correct' : 'error'; ?>.gif"></span></td>	
		</tr>
		<?php } ?>
	</table>
	<form action="<?php echo app_url() . 'index/index_' . ($dev_pass && $dir_pass ? '3' : '2') . '/'; ?>" method="post">
		<fieldset>
				<p style="text-align: center;"><input type="submit" class="btn" name="ok" value="��һ��" /></p>
		</fieldset>
	</form>
	<?php
			}
		break;

		case 3:
	?>
		<form action="<?php echo app_url() . 'index/index_4/' ?>" method="post">
		<fieldset>
				<p class="title">���ݿ���Ϣ</p>
				<p><label>��������ַ��</label><input type="text" name="config[db_host]" value="localhost" /> <span class="red">*</span> <span class="gray">(һ��Ϊlocalhost)</span></p>
				<p><label>���ݿ�����</label><input type="text" name="config[db_name]" value="qxdm_mutiuser" /> <span class="red">*</span> <span class="gray">(��Ӣ�ġ����֡��»������,��Ӣ���ֿ�ͷ,�����ݿⲻ����,�����Զ�����)</span></p>
				<p><label>���ݱ�ǰ׺��</label><input type="text" name="config[db_pre]" value="qxdm_" /> <span class="red">*</span></p>
				<p><label>���ݿ��û�����</label><input type="text" name="config[db_user]" /> <span class="red">*</span> <span class="gray">(����mysql���ݿ���˻�����)</span></p>
				<p><label>���ݿ��û����룺</label><input type="password" name="config[db_pass]" /> <span class="red">*</span> <span class="gray">(����mysql���ݿ�����룬���ز���Ϊ�տɲ���)</span></p>
				<p class="title">�û���Ϣ</p>
				<p><label>��Կ��</label><input type="text" name="config[qx_key]" value="qxdm-sa3k-k3j9-883a" /> <span class="red">*</span> <span class="gray">(��¼�û�����֤��Կ)</span></p>
				<p><label>��ʼ���˺ţ�</label><input type="text" name="creator_name" /> <span class="red">*</span> <span class="gray">(��¼��̨�Ĺ���Ա�˺�)</span></p>
				<p><label>���룺</label><input type="password" name="creator_pass" /> <span class="red">*</span> <span class="gray">(��¼��̨�Ĺ���Ա����)</span></p>
				<p><label>����ȷ�ϣ�</label><input type="password" name="creator_pass_again" /> <span class="red">*</span></p>
				<p><input type="hidden" name="step" value="2" /></p>
				<p style="text-align: center;"><input type="submit" class="btn" name="ok" value="��һ��" /></p>
		</fieldset>
		</form>
	<?php
		break;

		case 4:
			if(isset($error) && !empty($error)) {
				echo $error;
			} else {
				function execute_sql($sql_file) {
					$handle = fopen($sql_file, 'r');
					$buffer = fread($handle, filesize($sql_file));
					fclose($handle);
					
					$buffer = str_replace('{table_prefix}_', $_POST['config']['db_pre'], $buffer);
					$arr = explode(";\r\n", $buffer);
					$table_total = preg_match_all("/CREATE TABLE `(.*)` /i", $buffer, $a);
					$n = 0;
					foreach($arr as $query){
						mysql_query($query) or die(mysql_error().' ��װ�޷�����<br />');
						$is = preg_match("/CREATE TABLE `(.*)` /i",$query,$arr_preg);//����ƥ��Ϊ��ȡ������
						if($is){
							$n++;
							echo '������ '.$arr_preg[1].'...<font color=red>�ɹ�</font><br />',
							'<script type="text/javascript">document.getElementById("info").scrollTop = document.getElementById("info").scrollHeight;</script>';
							ob_flush();
							flush();
						}
					}
					ob_end_flush();
				}
				function salt($num = 4) {
					if($num < 4 || $num > 16) $num = 4;
					return substr(uniqid(rand()), -$num);
				}
				function create_pass($input_pass, $salt) {
					return md5(md5($input_pass) . $salt);
				}
				$salt = salt();
				$creator_pass = create_pass($creator_pass, $salt);
	?>
	<div id="info">
		<?php
			execute_sql($sql_file);
			mysql_query("INSERT INTO " . $_POST['config']['db_pre'] . "user (`user_name`, `user_pass`,`salt`,`group_id`) VALUES ('{$creator_name}','{$creator_pass}','{$salt}', '1')");
			$_POST['config']['creator'] = mysql_insert_id();
			set_config($_POST['config']);
		?>
	</div>
	
	<form action="<?php echo app_url() . 'index/index_5/' ?>" method="post">
		<fieldset>
				<p style="text-align: center;"><input type="submit" class="btn" name="ok" value="��һ��" /></p>
		</fieldset>
	</form>
	<?php
		}
		break;

		case 5:
	?>
	<h2 id="complete">��ϲ�㣬��װ�ɹ���^_^</h2>
	<p style="text-align: center; font-weight: bold;"><a href="<?php echo SITE_URL . 'qx-admin.php'; ?>">�����̨</a></p>
	<?php
		break;
	}
	
	?>
	</div>
</div>
<div id="footer">Powered By <a href="<?php echo DEVELOPER_HOMEPAGE; ?>" target="_blank"><b>QXDream Mutiuser V<?php echo PRO_VERSION; ?></b></a></div>
</body>
</html>