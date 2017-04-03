<?php
class Db
{
	public $result = Array();
	private $connect;

	public function __construct($_host=false, $_user=false, $_pass=false, $_name=false)
	{
		$host = DB_HOST;
        $user = DB_USER;
        $pass = DB_PASSWORD;
        $name = DB_NAME;

		if(!isset($host)){
			$host = $_host;
            $user = $_user;
            $pass = $_pass;
            $name = $_name;
		}

		if($host === false){
			die('DB credidentials missing.');
		}

		$this->connect = new mysqli($host, $user, $pass, $name);
		if(mysqli_connect_errno() !== 0){
			throw new Exception("DB connection error : ".mysqli_connect_error());
		} else {
			$this->connect->query("SET NAMES 'utf8'");
			$this->connect->query("SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
			return $this->connect;
		}
	}

	public function execute($sql)
	{
		$query_arr = explode(" ", trim($sql));
		$query_type = strtoupper($query_arr[0]);

		if($query_type == 'SELECT' || $query_type == 'SHOW'){
			return $this->selectable($sql);
		} else if($query_type == 'UPDATE' || $query_type == 'DELETE' || $query_type == 'DROP' || $query_type == 'INSERT' || $query_type == 'ALTER' || $query_type == 'CREATE') {
			return $this->modifiable($sql);
		}

		return false;
	}

	private function selectable($query)
	{
		$this->result = array();

		$result = $this->connect->query($query);
		if(!$result){
			return false;
		} else {
			while (($db_result = $result->fetch_assoc()) !== null)
			{
				array_push($this->result, $db_result);
				//$this->result[] = $db_result;
			}

			return $this->result;
		}

		$this->close();
	}

	private function modifiable($query)
	{
		$this->result = array();

		$result = $this->connect->query($query);
		return (!($result)) ? false : true;
	}

	public function getRow()
	{
		$row = mysqli_fetch_row($this->result);
		return $row;
	}

	public function affectedRows()
	{
		$rows=$this->connect->affected_rows;
		return $rows;
	}

	public function count()
	{
		$count = count($this->result);
		return (!$count || !is_int($count)) ? 0 : $count;
	}

	public function mysql_real_escape_equiv($data)
	{
		// Strip the slashes if Magic Quotes is on:
		if (get_magic_quotes_gpc()) $data = stripslashes($data);

		// Apply trim() and mysqli_real_escape_string():
		return mysqli_real_escape_string ($this->connect, trim ($data));
	}

	public function lastInsertID()
	{
		return $this->connect->insert_id;
	}

	public function getError()
	{
		$error = mysqli_error($this->connect);
		return $error;
	}

	public function close()
	{
		if(isset($this->connect)) 
		{
			mysqli_close($this->connect);
			unset($this->connect);
		}
	}

	public function __destruct()
	{
		$this->close();
	}
}

?>
