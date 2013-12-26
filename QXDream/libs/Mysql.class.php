<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn ���в���
	@author   ̤ѩ���� <xuexian_123@163.com>
	
	@create   2009-04-09 ���ݿ�������� $
	@version  $Id: Mysql.class.php 3.9 2011-03-26
*/

defined('IN_QX') or die('<h1>Forbidden!</h1>');

class Mysql{

 	public $db_host;
 	public $db_user;
 	public $db_password;
 	public $charset;
 	public $database;
	public $db_pre;
	public $conn;                      //���ݿ����Ӿ��
	public $fetch_mode = MYSQL_ASSOC;  //����ȡ���������͵�ģʽ MYSQL_NUM,MYSQL_BOTH
	public $debug = DEBUG;             //�Ƿ���������Ϣ
	public $is_log = IS_LOG;           //�Ƿ���������־
	public $row_num = 0;               //��¼������

	public function __construct(){
		//��¼����״̬����ֹ�ظ�����
		if(isset($GLOBALS['qx_connection'])) { $this->conn = $GLOBALS['qx_connection']; return TRUE; }
		$this->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET, DB_PRE, DB_PCONNECT);
 	}
	
	/* �������ݿ�����
	 * $db_host ������
	 * $db_user �û���
	 * $db_pass ����
	 * $charset �ַ�����
	 * $database ���ݿ�����
	 * $db_pre ���ݱ�ǰ׺,Ĭ����ǰ׺
	 * $pconnect �Ƿ�Ϊ�־�����
	 */
	public function connect($db_host,$db_user, $db_password, $database, $charset, $db_pre = '', $pconnect = 0){
		$func = $pconnect == 1 ? 'mysql_pconnect' : 'mysql_connect';
		if($this->conn = @$func($db_host, $db_user, $db_password)) {
			$GLOBALS['qx_connection'] = $this->conn;
			$this->select_db($database);
			if($this->mysql_version() > '4.1') {
				//Ϊ��ͳһ,��utf-8�滻��utf8,��Ϊ��html�б�����utf-8,��mysql����utf8
				$charset = !empty($charset) && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $charset;
				$server_charset = !empty($charset) ? 'character_set_connection=' . $charset . ', character_set_results=' . $charset . ',character_set_client=binary' : '';
				$server_charset .= $this->mysql_version() > '5.0.1' ? (empty($server_charset) ? '' : ',') . "sql_mode=''" : '';
				!empty($server_charset) && mysql_query("SET {$server_charset}", $this->conn);
			}
		} else {
			$this->halt('No', "Can not connect to MySQL server,please check 'host','user','password' are correct!");
		}
 	}
	//ѡ�����ݿ�
	public function select_db($database) {
		if(!@mysql_select_db($database, $this->conn)) $this->halt('No', "Unknown database '" .  $database . "'");
		$this->database = $database; //�Ѷ������Ը��ڲ�����ֵ,�����ڶ�����ѡ����������ݿ�
	}
	//����һ��Ĳ�ѯ���޸ġ�ɾ��������,�޷��ؽ��
	public function query($query, $type = '') {
		$func = 'unbuffered' == $type && function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!$this->conn) { halt('Cannot find any link identifer!'); }
		if($result = $func($query, $this->conn)) {
			$GLOBALS['QXDREAM']['query_num']++;
			return $result;
		} else {
			$this->halt($query);
		}
	}
	//����ǰһ��MySQL������Ӱ��ļ�¼����
	public function affected_rows() {
		return mysql_affected_rows($this->conn);
	}
	//ȡ��һ����¼, MYSQL_ASSOC��ȥ�����ּ�ֵ,ֻ��ʾ�ؼ��ּ�ֵ
	public function fetch_array($query, $fetch_mode = '') {
		$mode = empty($fetch_mode) ? $this->fetch_mode : $fetch_mode;
		return @mysql_fetch_array($query, $mode);
	}
	//ȡ��һ��ֻ�����ּ��ļ�¼
	public function fetch_row($query) {
		return mysql_fetch_row($query);
	}
	//�ƶ��ڲ�ָ��
	public function data_seek($query, $row_number) {
		return mysql_data_seek($query, $row_number);
	}
	//�ͷŲ�ѯ��¼
	public function free_result($result) {
		return @mysql_free_result($result);
	}
	//���ڷ���һ����¼
	public function fetch($query){
		$result = $this->query($query);
		$row = $this->fetch_array($result);
		$this->free_result($result);
		return $row;
	}
	//����ѭ����ѯ,�������м�¼�Ķ�ά����
	public function fetch_all($query, $type = ''){
		$output = array();
		$result = $this->query($query, $type);
		while($row = $this->fetch_array($result)){
			$output[] = $row;
		}
		$this->free_result($result);//�ͷ���Դ
		return $output;
	}
	//��ȡ������IDֵ��Ϊ��ֵ,�����ֶε�ֵ��Ϊֵ,�γ�һά����
	public function fetch_all_config($query, $id_column_name, $other_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[$row[$id_column_name]] = $row[$other_column_name];
		}
		$this->free_result($result);
		return $data;
	}
	//ȡ������,��������IDֵ��Ϊһ�����ݵļ�ֵ,�������ͽṹ�򻺴����ݵ��ļ���,$id_column_nameΪ�����ֶ���
	public function fetch_all_column_key($query, $id_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[$row[$id_column_name]] = $row;
		}
		$this->free_result($result);//�ͷ���Դ
		return $data;
	}
	//����������һά����,���ڰ�ÿ����¼��ID�ö�������
	public function fetch_all_id($query, $id_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[] = $row[$id_column_name];
		}
		$this->free_result($result);//�ͷ���Դ
		return $data;
	}
	//ȡ����¼��һ����Ԫ����
	public function result($query, $field = 0) {
		$query = $this->query($query, 'unbuffered');
		$data = $this->fetch_row($query, $field);
		$this->free_result($query);
		return isset($data[$field]) ? $data[$field] : FALSE;
	}
	//����ȡ�����������ݿ��ID
	public function last_insert_id(){
		$recent_id = mysql_insert_id($this->conn);
		return $recent_id;
	}

	//���ڼ����ѯ�����¼��
	public function num_rows($query){
		$total = mysql_num_rows($query);
		return $total;
	}
	
	//�����ֶ�����
	public function get_fields($table) {
		$fields_array = array();
		$result = $this->query('SHOW COLUMNS FROM `' . $table . '`', 'unbuffered');
		while($row_array = $this->fetch_array($result)) {
		   /**
			* $row�����������һ�����ӣ�FiledΪ�ֶ�
			* [Field] => id
			* [Type] => mediumint(8) unsigned
			* [Null] => NO
			* [Key] => PRI
			* [Default] => 
			* [Extra] => auto_increment
			*/
			$fields_array[] = $row_array['Field'];
		}
		$this->free_result($result);
		return $fields_array;
	}
	
	//��֤���е��ֶδ���$array�е�����ؼ��֣������ڵĻ����򽫱���,˽�з���
	private function check_fields($table, $array) {
		if(!is_array($array)) halt('Parameter \'' . $array . '\' is not array!');
		$fields_array = $this->get_fields($table);
		foreach($array as $k => $v) {
			if(!in_array($k, $fields_array)) { //in_array����ֵ�ĶԱȹ���
				if($this->debug) { $this->halt('No', 'Field \'' . $k . '\' not exists in table \'' . $table . '\''); } else { unset($array[$k]); }
			}
		}
		return $array;
	}
	
	//��������
	public function insert($table, $array) {
		$array = $this->check_fields($table, $array);
		return $this->query("INSERT INTO `{$table}` (`" . implode('`,`', array_keys($array)) . "`) VALUES ('" .implode("','", $array) . "')");
	}
	
	//�����������ݣ�@arrayΪ��ά����
	/*
	 �磺$arr = array(array('id' => 'NULL', 'title' => '��������'),
					  array('id' => 'NULL', 'title' => '�ٺ�'),
					  array('id' => 'NULL', 'title' => '��')	
			);
			id,titleΪ�ֶ���
	 */
	public function insert_multiple($table, $array) {
		$value_multi = '';
		foreach($array as $k => $v) {
			if(!is_array($v)) return;
			$value_multi .= "('" . implode("','", $v) . "'),";
		}
		$this->check_fields($table, $v); //ÿ�����ݵ��ֶ�����ͬ��
		$value_multi = rtrim($value_multi, ',');
		return $this->query('INSERT INTO `' . $table . '` (`' . implode('`,`', array_keys($v)) . "`) VALUES " . $value_multi);
	}
	
	//��������,@whereΪ id=$_POST['id']��
	public function update($table, $array, $where = '') {
		$this->check_fields($table, $array);
		if($where) {
			$sql = '';
			foreach($array as $k => $v) {
				$sql .= "`{$k}`='{$v}',";
			}
			$sql = rtrim($sql,',');
			$sql = 'UPDATE `' . $table . '` SET ' . $sql . ' WHERE ' . $where;
		} else {
			$sql = "REPLACE INTO `{$table}` (`" . implode('`,`', array_keys($array)) . "`) VALUES ('" .implode("','", $array) . "')";
		}
		return $this->query($sql);
	}
	
	//ɾ������,��д@whereɾ������
	public function delete($table, $where = '') {
		if(!empty($where)) $where = ' WHERE ' . $where;;
		return $this->query('DELETE FROM `' . $table . '`' . $where);
	}

	//���������Ĺؼ���
	public function get_primary($table) {
		$fields_array = array();
		$result = $this->query('SHOW COLUMNS FROM `' . $table . '`', 'unbuffered');
		while($row_array = $this->fetch_array($result)) {
			if($row_array['Key'] == 'PRI') break;
		}
		$this->free_result($result);
		return $row_array['Field'];
	}
	
	//���ڼ����ܵ����ݱ���Ŀ
	public function num_tables(){
		$result = mysql_list_tables($this->database);
		$data = array();
		while($row = $this->fetch_array($result, MYSQL_NUM)) {
			if(preg_match("/^" . $this->db_pre . "/", $row[0])) $data[] = $row[0];
		}
		$this->free_result($result);
		return count($data);
	}
	
	//����ȡ���������ݱ������
	public function fetch_tables(){
		$result = mysql_list_tables($this->database);
		$i = 0;
		$table = '';
		while($i < mysql_num_rows($result)){
			$table = mysql_tablename($result,$i);
			if(preg_match("/^" . $this->db_pre . "/", $table)) $table_name[] = $table;
			$i++;
		}
		$this->free_result($result);
		return $table_name;
	}
	
	//����mysql�İ汾
	public function mysql_version() {
		return mysql_get_server_info($this->conn);
	}
	
	//����mysql������Ϣ
	public function error() {
		return $this->conn ? mysql_error($this->conn) : mysql_error();
	}
	
	//����mysql������
	public function errno() {
		return intval($this->conn ? mysql_errno($this->conn) : mysql_errno());
	}
	
	//�������ݿ�����ʱ��
	public function mysql_runtime() {
		$query = $this->query('SHOW STATUS');
		while($row = $this->fetch_array($query)) {
			if (preg_match("/^uptime/i", $row['Variable_name'])) return $row['Value'];
		}
	}
	
	//�ر����ݿ�����
	public function close() {
		mysql_close($this->conn);
	}
	
	//���ݿ�����¼,д����־,@prompt�Զ��������ʾ��Ϣ
	public function halt($query = '', $prompt = ''){
		if($this->is_log == 1 && $query){
			//��־�ļ���
			$log_file = QX_ROOT . PUBLIC_DIR . 'data/logs/dberror_log.php';
			$str = "<?php exit('Access Denied');?>\t";
			//ʱ��
			$str .= $GLOBALS['QXDREAM']['timestamp'] . "\t";
			//IP
			$str .= $GLOBALS['QXDREAM']['online_ip'] . "\t";
			//��ǰ�ļ���
			$require = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
			$str .= basename($_SERVER['PHP_SELF']) . $require . "\t";
			//MYSQL�ı�����Ϣ
			$str .= $this->error() . " on line " . $this->errno() . "\t";
			//SQL���
			$str .= str_replace(array("\r", "\n", "\t"), array(' ', ' ', ' '), trim(htmlspecialchars($query))) . "\n";
			//д����־
			write_log($log_file, $str);
		}
		if(empty($this->debug)) return FALSE;
		$prompt = empty($prompt) ? $this->error() : $prompt;
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
			<title>Sorry,MySQL Error!</title>
			<style type="text/css">
			#m_wrapper * { margin: 0; padding: 0; }
			#m_wrapper { font: 12px/1.5 "Courier New", Courier, monospace; border: 1px solid #ccc; background: #f7f7f7; width: 720px; position: absolute;  left: 50%; margin: 80px 0 0 -360px; padding: 5px; -moz-border-radius: 10px; -webkit-border-radius: 5px; }
			#m_wrapper #in { width: 680px; background: #fff; padding: 10px 20px; overflow: hidden; }
			#m_wrapper h1 { font-size: 18px; font-family: verdana; text-align: center; border-bottom: 1px solid #ccc; margin-bottom: 10px; }
			#m_wrapper b { float: left; width: 50px; }
			#m_wrapper p { overflow: hidden; height: 1%; }
			#m_wrapper p span { float: left; width: 630px; }
			#m_wrapper b,#m_wrapper #err { font-weight: normal; color: #d30000; }
			#m_wrapper #err { font-weight: bold; font: 24px/1.0 Arial; }
			</style>
			</head>
			<body>
			<div id="m_wrapper">
			<div id="in">
			<h1><span id="err">&hearts;</span>MySQL Error</h1>
			<p><b>SQL: </b><span>' . $query . '</span></p>
			<p><b>Error: </b><span>' . $prompt . '</span></p>
			<p><b>Errno: </b><span> line ' .  $this->errno() . '</span></p>
			<p><b>File: </b><span>' . PHP_SELF. '</span></p>
			</div>
			</div>
			</body>
			</html>';
		exit();
	}

}
?>