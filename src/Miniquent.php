<?php

namespace Miniquent;

/**
 * Eloquentモデル・ライクなO/Rマッパーです。
 * PDO拡張クラスを使用して安全なデータベースの接続と管理をします。
 * 特に配列を利用することで、長大なSQL文の作成と、静的プレースホルダを用いた安全な接続が可能です。
 * @author Masaharu Inoue <pasteur1822@gmail.com>
 * @license MIT 
 */
require_once ("config.php");

class Miniquent
{
	protected $table = 'users';
	public $column;
	protected $pagenate;
	protected $data;
	protected $db;
	protected $value_list;
	protected $sql;
	protected $left_join;
	protected $orderby;
	protected $limit;
	protected $offset;
	protected static $where;
	protected static $where_flg;
	protected $perPage;
    protected $active_page;
	protected $page_length;
	protected $primaryKey = 'id';
	protected $include_pager_file = "page_template.php";
	
/**
 * @constructor
 *
 */
	public function __construct()
	{
		try{
			$this->db = new \PDO(DSN,DB_USERNAME,DB_PASSWORD);
			$this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
		  echo $e->getMessage();
		  exit ;
		}

		$this->where_flg = false;

	}

  

	protected function execute()
	{
		$sql = $this->sql;
		$value_list = $this->value_list;

		if ($this->value_list == null) {
			$stmt = $this->db->query($this->sql);
			return $stmt->fetchAll(\PDO::FETCH_OBJ);
		}
	
		// 実行
		$this->db->beginTransaction();
		try{

			$stmt = $this->db->prepare($sql);
			for ($i=0;$i < count($this->value_list); $i++){
				$type = $this->typeSelection($value_list[$i]);
				$stmt->bindParam($i+1, $value_list[$i], $type);
			}
			//成功ならTrue
			$result = $stmt->execute();

			$this->db->commit();
		} catch(Excetipn $e){
			$db->rollBack();
		}
		
		if(!$result) {
			$this->outputError($sql, $result, $error_msg, $exit_flg);
			$ret = null;
		} else {
			$ret = true;
		}
		return $ret;
	}
/**
 * 一度に表示する量
 *
 * @param string $num	一度に表示したい数量
 * @return object
 */
	public function limit ($num){
		$this->limit = "limit $num";
		return $this;
	}

	public function all (){
		
	}

	public function first()
	{
		$this->limit="limit 1";
		return $this->get();
	}

	public function find ($id){
		$this->where($this->primaryKey,$id);
		return $this;
	}

	public function destroy($id){
		$del = $this->where($this->primaryKey,$id);
		$del->delete();
	}

	public function count(){
		$this->column = "count(*)";
		$sql = $this->get(true);
		$stmt = $this->db->query($sql);
		$total_num = $stmt->fetchColumn();
		return $total_num;
	}


	public function orderBy($order_column, $order_seq = 'asc')
	{
		$this->orderby = "ORDER BY $order_column  $order_seq";
		return $this; 
	}

	static public function where ()
	{
		// 引数を抽出
		$args = func_get_args();

		if (!self::$where_flg) {
			self::$where .="WHERE";
		} else {
			self::$where .= "AND";
		}

		switch (count($args)) {
			case 2:
				self::$where .= " $args[0] = '$args[1]' ";
				break;
			case 3:
				self::$where .= " $args[0] $args[1] '$args[2]' ";
				break;
		}

		self::$where_flg=true;
		return new static;
	}

	public function orWhere(){
		// 引数を抽出
		$args = func_get_args();
		if (!self::$where_flg) {
			return $this;
		} else {
			self::$where .= "OR";
		}

		switch (count($args)) {
			case 2:
				self::$where .= " $args[0] = '$args[1]' ";
			case 3:
				self::$where .= " $args[0] $args[1] '$args[2]' ";
		}

		return $this;
	}

	public function pagination($page_unit){

		$this->pagenate = $page_unit;
		$this->limit= "limit $this->pagenate";
		if (isset($_GET['page'])){
			$off = $this->pagenate * ($_GET['page']-1) ??  0;
		} else {
			$off =0;
		}
		$this->offset = "OFFSET $off" ;
		return $this;
	}

	public function links(){

		// 総数の計算
		$this->column[] = "count(*)";
		$tmp_limit = $this->limit;
		$this->limit = "";
		$tmp_offset = $this->offset;
		$this->offset = "";
		$sql = $this->get(true);
		$stmt = $this->db->query($sql);
		$total_num = (int) $stmt->fetchColumn();

		$this->limit = $tmp_limit ;
		$this->offset = $tmp_offset;
    	$this->active_page=$_GET['page'] ?? 1;

		$this->page_length = ceil($total_num/$this->pagenate);

		if ($this->page_length < 1){
			$this->page_length=1;
		}

		ob_start();
		include $this->include_pager_file;
		ob_end_flush();

	}


	public function leftJoin($alt_table,$col){

		$this->left_join = "LEFT JOIN $this->table ON $alt_table.$col = $this->table.$col ";

		return $this;
	}


	// setter
   public function __set($key, $value){
		$this->data[$key] = $value;
	}


	public function get($sql_only_flg=false){

		// カラム用変数
		$column_list='';
		if ($this->column == null || !is_array($this->column)) {
			$column_list='*';
		} else {
			foreach ($this->column as $col){
				if(!$column_list){
					$column_list = "$col ";
				}else {
					$column_list .= ", $col";
				}
			}
		}

		$this->sql = "SELECT $column_list FROM $this->table $this->left_join ". self::$where . " $this->orderby $this->limit $this->offset";
		print $this->sql;

		if (!$sql_only_flg) {
			return $this->execute();
		} else {
			return $this->sql;
		}

		
	}


	public function delete()
	{
		$sql = "DELETE FROM $this->table " . self::$where;
		return $this->execute();
	}


	public function save(){
		$name_list = "";
		$prepare_list="";
		$flg=false;
	
		foreach ($test->data as $key=>$value) {
			if (self::$where_flg) {
				// 更新
				if ($prepare_list === "") {
					$prepare_list = "`$key` = ?";
					$this->value_list[] = $value;
				} else {
					$prepare_list .= ", `$key` = ?";
					$this->value_list[] = $value;
				}

			} else {
			//新規登録
				if ($flg === false) {
					$name_list	= "`$key`";
					$prepare_list	= "?";
					$this->value_list[] = $value;
					$flg=!$flg;
				} else {
					$name_list	.= ", `$key`";
					$prepare_list	.= ", ?";
					$this->value_list[] = $value;
				}

			}

		}


	 // 更新
	if (self::$where_flg) {
		$this->sql = "UPDATE $this->table SET  $prepare_list " .self::$where .";";
	// 新規登録
	} else {
   	   $this->sql = "INSERT INTO $this->table ($name_list) VALUES($prepare_list);";
   	}

	$this->execute();
  }


	function sqlSet($data,$action)
	{
		$sql = null;
		$name_list = "";
		$prepare_list="";
		$value_list=array();

		//カラム指定がある場合(DELETEはなし)
		if (isset($data['column']) === true && is_array($data['column']) === true) {
			foreach ($data['column'] as $key=>$value) {
				switch ($action) {
				case "values":
				case "ins":
				case "rep":
					if ($name_list === "") {
						$name_list	= "`" . $key . "`";
						$prepare_list	= "?";
						$value_list[] = $value;
					} else {
						$name_list	.= ", `" . $key . "`";
						$prepare_list	.= ", ?";
						$value_list[] = $value;
					}
					break;

				case "upd":
					if (strpos($key, ".") == false) {
						//フィールド名 = "値"の形式
						if ($prepare_list === "") {
							$prepare_list = "`" . $key . "` = ?";
							$value_list[] = $value;
						} else {
							$prepare_list .= ", `" . $key . "` = ?";
							$value_list[] = $value;
						}
					} else {
						//テーブル名.フィールド名 = "値"の形式
						if ($prepare_list === "") {
							$prepare_list = $key . " = ?";
							$value_list[] = $value;
						} else {
							$prepare_list .= "," . $key . " = ?";
							$value_list[] = $value;
						}
					}
					break;

				default:
					break;

				}
			}
		}

		$where_prepare='';
		if (isset($data['where']) === true && is_array($data['where']) === true) {
				foreach ($data['where'] as $key=>$value) {
					if ($where_prepare === '') {
						$where_prepare = "$key = ?";
						$value_list[] = $value;
					} else {
						$where_prepare .= " AND $key = ?" ;
						$value_list[] = $value;
					}
				}
			} 
		if ($where_prepare !== "") {
			$where_prepare = " WHERE {$where_prepare}";
		}

		$ignore = "";
		if (isset($data['ignore']) == true && $data['ignore'] == true) {
			$ignore = " IGNORE";
		}

		$extension = "";
		if (isset($data['extension']) == true) {
			$extension = " " . $data['extension'];
		}

		switch ($action) {
		// レコードの挿入
		case "ins":
			$sql = "INSERT" . $ignore . " INTO " . $this->table . "(" . $name_list . ") VALUES(" . $prepare_list . ")" . $extension . ";";
			break;
		// 置換
		case "rep":
			$sql = "REPLACE INTO " . $this->table . "(" . $name_list . ") VALUES(" . $prepare_list . ")" . $extension . ";";
			break;
		// 更新
		case "upd":
			$sql = "UPDATE" . $ignore . " " . $this->table . " SET " . $prepare_list . $where_prepare . $extension;
			break;
		// 削除
		case "del":
			$sql = "DELETE FROM " . $this->table . $where_prepare . $extension;
			break;

		case "values":
			$sql = "(" . $prepare_list . ")" . $extension;
		}

		$this->value_list =$value_list;
		$this->sql = $sql;
		return $this;

		//return $this->setSqlExecute($sql,$value_list);




	}



	//@ref http://d.hatena.ne.jp/uunfo/20090204/1233728629
	public function typeSelection($bind){
		$type = \PDO::PARAM_STR;
		switch(true){
			case is_bool($bind) :
				$type = \PDO::PARAM_BOOL;
				break;
			case is_null($bind) :
				$type = \PDO::PARAM_NULL;
				break;
			case is_int($bind) :
				$type = \PDO::PARAM_INT;
				break;
			case is_float($bind) :
			case is_numeric($bind) :
			case is_string($bind) :
			default:
				$type = \PDO::PARAM_STR;
				break;
		}
		return $type;

	}

	function outputError($sql, &$result = null, $error_msg = null, $exit_flg = null)
	{
		// trace
		$trace_str   = "";
		$trace_array = debug_backtrace();
		if (count($trace_array) > 0)
		{
			$trace_str .= "TRACE";
			for ($i=0; $i < count($trace_array)-1; $i++)
			{
				$trace_str .= "file：" . (isset($trace_array[$i]['file']) ? $trace_array[$i]['file'] : '') . "\n";
				$trace_str .= "line：" . (isset($trace_array[$i]['line']) ? $trace_array[$i]['line'] : '') . "\n";
				if (isset($trace_array[1]))
					$trace_str .= "function：" . (isset($trace_array[$i+1]['function']) ? $trace_array[$i+1]['function'] : '') . "\n\n";
			}
		}


		if ($error_msg != "") {
			print "<b style=\"color: red;\">".$error_msg."</b>";
		}
		if (DEBUG_MODE) {
			$err_sql = str_replace("\n", "<br>", $sql);
			$err_sql = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $err_sql);
			print "<div>";
			print "<br><br><b>ErrorReport</b><br>";
			print "-- NGSQL --<br>".$err_sql."<br>";
			print "<br>-- ErrorMsg --<br>".mysql_error()."<br>";
			print "<br>-- ErrorCode --<br>".mysql_errno()."<br>";
			print str_replace("\n", "<br>", $trace_str)."<br>";
			print "</div>";
		}

		$error_msg .= "\nError Report\n";
		if ($server_option === "mysql") {
			$error_msg .= "-- ErrorMsg --\n" . mysql_error() . "\n-- ErrorCode --\n" . mysql_errno() . "\n";
		}

		$error_msg .= "-- NGSQL --\n" . $sql;
		trigger_error($error_msg, E_USER_ERROR);

		if ($exit_flg == 1){
			exit;
		}
		$ret = null;
	}




}


