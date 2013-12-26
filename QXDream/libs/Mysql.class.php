<?php
/* 
    [QXDream] Copyright (C)2010-2011 QXDream Mutiuser
	
    @homepage http://www.qxhtml.cn 倾行博客
	@author   踏雪残情 <xuexian_123@163.com>
	
	@create   2009-04-09 数据库基本操作 $
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
	public $conn;                      //数据库连接句柄
	public $fetch_mode = MYSQL_ASSOC;  //三种取出数据类型的模式 MYSQL_NUM,MYSQL_BOTH
	public $debug = DEBUG;             //是否开启调试信息
	public $is_log = IS_LOG;           //是否开启出错日志
	public $row_num = 0;               //记录集数量

	public function __construct(){
		//记录连接状态，防止重复连接
		if(isset($GLOBALS['qx_connection'])) { $this->conn = $GLOBALS['qx_connection']; return TRUE; }
		$this->connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET, DB_PRE, DB_PCONNECT);
 	}
	
	/* 建立数据库连接
	 * $db_host 主机名
	 * $db_user 用户名
	 * $db_pass 密码
	 * $charset 字符编码
	 * $database 数据库名称
	 * $db_pre 数据表前缀,默认无前缀
	 * $pconnect 是否为持久连接
	 */
	public function connect($db_host,$db_user, $db_password, $database, $charset, $db_pre = '', $pconnect = 0){
		$func = $pconnect == 1 ? 'mysql_pconnect' : 'mysql_connect';
		if($this->conn = @$func($db_host, $db_user, $db_password)) {
			$GLOBALS['qx_connection'] = $this->conn;
			$this->select_db($database);
			if($this->mysql_version() > '4.1') {
				//为了统一,把utf-8替换成utf8,因为在html中编码是utf-8,而mysql中是utf8
				$charset = !empty($charset) && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $charset;
				$server_charset = !empty($charset) ? 'character_set_connection=' . $charset . ', character_set_results=' . $charset . ',character_set_client=binary' : '';
				$server_charset .= $this->mysql_version() > '5.0.1' ? (empty($server_charset) ? '' : ',') . "sql_mode=''" : '';
				!empty($server_charset) && mysql_query("SET {$server_charset}", $this->conn);
			}
		} else {
			$this->halt('No', "Can not connect to MySQL server,please check 'host','user','password' are correct!");
		}
 	}
	//选择数据库
	public function select_db($database) {
		if(!@mysql_select_db($database, $this->conn)) $this->halt('No', "Unknown database '" .  $database . "'");
		$this->database = $database; //把对象属性赋于参数的值,方便在对象外选择另外的数据库
	}
	//用于一般的查询、修改、删除、更新,无返回结果
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
	//返回前一次MySQL操作所影响的记录行数
	public function affected_rows() {
		return mysql_affected_rows($this->conn);
	}
	//取出一条记录, MYSQL_ASSOC会去掉数字键值,只显示关键字键值
	public function fetch_array($query, $fetch_mode = '') {
		$mode = empty($fetch_mode) ? $this->fetch_mode : $fetch_mode;
		return @mysql_fetch_array($query, $mode);
	}
	//取出一条只带数字键的记录
	public function fetch_row($query) {
		return mysql_fetch_row($query);
	}
	//移动内部指针
	public function data_seek($query, $row_number) {
		return mysql_data_seek($query, $row_number);
	}
	//释放查询记录
	public function free_result($result) {
		return @mysql_free_result($result);
	}
	//用于返回一条记录
	public function fetch($query){
		$result = $this->query($query);
		$row = $this->fetch_array($result);
		$this->free_result($result);
		return $row;
	}
	//用于循环查询,返回所有记录的二维数组
	public function fetch_all($query, $type = ''){
		$output = array();
		$result = $this->query($query, $type);
		while($row = $this->fetch_array($result)){
			$output[] = $row;
		}
		$this->free_result($result);//释放资源
		return $output;
	}
	//获取主键的ID值作为键值,其他字段的值做为值,形成一维数组
	public function fetch_all_config($query, $id_column_name, $other_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[$row[$id_column_name]] = $row[$other_column_name];
		}
		$this->free_result($result);
		return $data;
	}
	//取出数据,把主键的ID值作为一条数据的键值,用于树型结构或缓存数据到文件中,$id_column_name为主键字段名
	public function fetch_all_column_key($query, $id_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[$row[$id_column_name]] = $row;
		}
		$this->free_result($result);//释放资源
		return $data;
	}
	//返回主键的一维数组,用于把每条记录的ID用逗号连接
	public function fetch_all_id($query, $id_column_name) {
		$result = $this->query($query);
		$data = array();
		while($row = $this->fetch_array($result)) {
			$data[] = $row[$id_column_name];
		}
		$this->free_result($result);//释放资源
		return $data;
	}
	//取出记录的一个单元内容
	public function result($query, $field = 0) {
		$query = $this->query($query, 'unbuffered');
		$data = $this->fetch_row($query, $field);
		$this->free_result($query);
		return isset($data[$field]) ? $data[$field] : FALSE;
	}
	//用于取出最后插入数据库的ID
	public function last_insert_id(){
		$recent_id = mysql_insert_id($this->conn);
		return $recent_id;
	}

	//用于计算查询结果记录数
	public function num_rows($query){
		$total = mysql_num_rows($query);
		return $total;
	}
	
	//返回字段数组
	public function get_fields($table) {
		$fields_array = array();
		$result = $this->query('SHOW COLUMNS FROM `' . $table . '`', 'unbuffered');
		while($row_array = $this->fetch_array($result)) {
		   /**
			* $row查出来的数组一个例子，Filed为字段
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
	
	//验证表中的字段存在$array中的数组关键字，不存在的话程序将报错,私有方法
	private function check_fields($table, $array) {
		if(!is_array($array)) halt('Parameter \'' . $array . '\' is not array!');
		$fields_array = $this->get_fields($table);
		foreach($array as $k => $v) {
			if(!in_array($k, $fields_array)) { //in_array具有值的对比功能
				if($this->debug) { $this->halt('No', 'Field \'' . $k . '\' not exists in table \'' . $table . '\''); } else { unset($array[$k]); }
			}
		}
		return $array;
	}
	
	//插入数据
	public function insert($table, $array) {
		$array = $this->check_fields($table, $array);
		return $this->query("INSERT INTO `{$table}` (`" . implode('`,`', array_keys($array)) . "`) VALUES ('" .implode("','", $array) . "')");
	}
	
	//批量插入数据，@array为二维数组
	/*
	 如：$arr = array(array('id' => 'NULL', 'title' => '人生如梦'),
					  array('id' => 'NULL', 'title' => '嘿嘿'),
					  array('id' => 'NULL', 'title' => '好')	
			);
			id,title为字段名
	 */
	public function insert_multiple($table, $array) {
		$value_multi = '';
		foreach($array as $k => $v) {
			if(!is_array($v)) return;
			$value_multi .= "('" . implode("','", $v) . "'),";
		}
		$this->check_fields($table, $v); //每条数据的字段是相同的
		$value_multi = rtrim($value_multi, ',');
		return $this->query('INSERT INTO `' . $table . '` (`' . implode('`,`', array_keys($v)) . "`) VALUES " . $value_multi);
	}
	
	//更新数据,@where为 id=$_POST['id']型
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
	
	//删除数据,不写@where删除所有
	public function delete($table, $where = '') {
		if(!empty($where)) $where = ' WHERE ' . $where;;
		return $this->query('DELETE FROM `' . $table . '`' . $where);
	}

	//返回主键的关键字
	public function get_primary($table) {
		$fields_array = array();
		$result = $this->query('SHOW COLUMNS FROM `' . $table . '`', 'unbuffered');
		while($row_array = $this->fetch_array($result)) {
			if($row_array['Key'] == 'PRI') break;
		}
		$this->free_result($result);
		return $row_array['Field'];
	}
	
	//用于计算总的数据表数目
	public function num_tables(){
		$result = mysql_list_tables($this->database);
		$data = array();
		while($row = $this->fetch_array($result, MYSQL_NUM)) {
			if(preg_match("/^" . $this->db_pre . "/", $row[0])) $data[] = $row[0];
		}
		$this->free_result($result);
		return count($data);
	}
	
	//用于取出所有数据表的名称
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
	
	//返回mysql的版本
	public function mysql_version() {
		return mysql_get_server_info($this->conn);
	}
	
	//返回mysql错误信息
	public function error() {
		return $this->conn ? mysql_error($this->conn) : mysql_error();
	}
	
	//返回mysql错误编号
	public function errno() {
		return intval($this->conn ? mysql_errno($this->conn) : mysql_errno());
	}
	
	//返回数据库运行时间
	public function mysql_runtime() {
		$query = $this->query('SHOW STATUS');
		while($row = $this->fetch_array($query)) {
			if (preg_match("/^uptime/i", $row['Variable_name'])) return $row['Value'];
		}
	}
	
	//关闭数据库连接
	public function close() {
		mysql_close($this->conn);
	}
	
	//数据库出错记录,写入日志,@prompt自定义错误提示信息
	public function halt($query = '', $prompt = ''){
		if($this->is_log == 1 && $query){
			//日志文件名
			$log_file = QX_ROOT . PUBLIC_DIR . 'data/logs/dberror_log.php';
			$str = "<?php exit('Access Denied');?>\t";
			//时间
			$str .= $GLOBALS['QXDREAM']['timestamp'] . "\t";
			//IP
			$str .= $GLOBALS['QXDREAM']['online_ip'] . "\t";
			//当前文件名
			$require = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
			$str .= basename($_SERVER['PHP_SELF']) . $require . "\t";
			//MYSQL的报错信息
			$str .= $this->error() . " on line " . $this->errno() . "\t";
			//SQL语句
			$str .= str_replace(array("\r", "\n", "\t"), array(' ', ' ', ' '), trim(htmlspecialchars($query))) . "\n";
			//写入日志
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