<?php

/**
 * 
 */
class DB
{
	private static $instance = null;
	private $_pdo, 
			$_query, 
			$_error = false, 
			$_results, 
			$_count = 0;
	
	private function __construct()
	{
		try {

			$server = Config::get('mysql/host');
			$dbname = Config::get('mysql/dbname');
			$this->_pdo = new PDO("mysql:host={$server};dbname={$dbname}", Config::get('mysql/username'), Config::get('mysql/password'));
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new DB();
		}
		return self::$instance;
	}

	public function query($method, $data = array(), $table = null,$column = null, $limit = null)
	{
		$this->_error = false;
		switch ($method) {
			case 'select':
				return $this->select($table, $data, $column, $limit);
				break;
			case 'insert':
				return $this->insert($table, $data);
				break;
			case 'update':
				return $this->update($table, $data,
					$column);
				break;
			case 'delete':
				return $this->delete($table, $data);
				break;
			default:
				if ($this->_query = $this->_pdo->prepare($method)) {
					$x = 1;
					if (isset($data)) {
						if (count($data)) {
							foreach ($data as $key => $val) {
								$this->_query->bindValue($x, $val);
								$x++;
							}
						}
					}
					if($this->_query->execute()){
						$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
						$this->_count = $this->_query->rowCount();
					}else{
						$this->_error = true;
					}
				}
				break;
		}
		return $this;
	}
	public function error()
	{
		return $this->_error;
	}
	public function result()
	{
		return $this->_results;
	}

	public function first($column = null)
	{
		return (empty($column))? $this->_results[0] : $this->_results[0]->$column;
	}
	public function _count()
	{
		return $this->_count;
	}

	public function select($table, $data = array(),$column = null, $limit = null)
	{
		$lmt = $limit ?? "25";
		$col = $column ?? "*";
		if (empty($data)) {
			$sql = "SELECT {$col} FROM {$table} LIMIT {$lmt}";
		}
		else{
			$sql = "SELECT $col FROM {$table} ".$this->sql($data);
		}
		
		if(!$this->query($sql, $data)->error()){
		 	return $this;
		}
		return false;
	}
	public function delete($table, $data = array())
	{
		if (empty($data)) {
			return false;
		}
		else{
			$sql = "DELETE FROM {$table} ".$this->sql($data);
		}
		if(!$this->query($sql, $data)->error()){
		 	return $this;
		}
		return false;
	}

	public function insert($table, $data = array())
	{
		if(empty($data)){
			return false;
		}
		else{
			if (count($data)) {
				$keys = implode('`, `',array_keys($data));
				$value = '';
				$z = 1; 
				foreach ($data as $was) {
					$value.= "?";
					if ($z<count($data)) {
						$value .= ", ";
					}
					$z++;
				}
				$sql = "INSERT INTO {$table} (`{$keys}`) VALUES ({$value})";
				if(!$this->query($sql, $data)->error()){
				 	return $this;
				}
			}
		}
		return false;
		
	}
	public function update($table, $data, $id)
	{
		$set = "";
		$x = 1;
		$tb_id = $this->findIdName($table);
		foreach ($data as $key => $value) {
			$set .= "{$key} = ?";
			if ($x < count($data)) {
				$set .= ", ";
			}
			$x++;
		}

		$sql = "UPDATE {$table} SET {$set} WHERE {$tb_id} = {$id}";

		if(!$this->query($sql, $data)->error()){
		 	return $this;
		}
		return false;
	}

	public function findIdName($table)
	{
		foreach ($this->query("SHOW COLUMNS FROM {$table}")->result() as $col_pre) {
			foreach ($col_pre as $key) {
				if ($key == "PRI") {
					return $col_pre->Field;
				}
			}
		}
	}
	public function sql($data)
	{
		$sql1 = "WHERE ";
		$sql2 = "";
		$d = 0;
		if (count($data) < 3) {
			foreach ($data as $col => $val) { 
				for ($l=0; $l < count($data); $l++) { 
					if (2**$l == $d+1) {
				
							$sql2 = "".$sql2;
						
					}
				}
				$sql2 .= "{$col} = ?";

				for ($g=0; $g < count($data); $g++) { 
					if (2**$g == $d+1) {
						if ($d < count($data)-1) {
							$sql2 = $sql2." AND ";
						}
						else{
							$sql2.= "";
						}
					}
				}
				$d++;
			}
		}
		
		return $sql1.$sql2;
	}
}