<?php

abstract class basicSQL {
	var $conn;
	var $sqlQuery;
	var $result;
	var $status;
	
	function basicSQL($conn) {
		$this->conn = $conn;
	}
	
	function executeQuery() {
		if($this->makeQuery()) {
			debugMessages($this->sqlQuery,"debugSQL");
			$this->result = $this->conn->query($this->sqlQuery);
			if ($this->checkForError())
				return false;
			else
				return true;
		}
		
		return false;
	}
	
	function makeQuery() {
		if ($this->checkParameters()) {
			$this->makeQueryText();
			return true;
		}
		debugMessages("Incomplete sql query parameters.","debugSQL");
		$this->status = makeStatusMessage(1,"error","Database error.");
		return false;
	}
	
	abstract function checkParameters();
	abstract function makeQueryText();
	
	function getNumberOfResults() {
		if (isset($this->result))
			return $this->result->num_rows;
		return 0;
	}
	
	function checkForError() {
		if (empty(mysqli_error($this->conn)))
			return false;
		debugMessages(mysqli_error($this->conn), "debugSQL");
		$this->status = makeStatusMessage(1,"error","Database error.");
		return true;
	} 	
	
}

class selectSQL extends  basicSQL {
	
	var $select, $distinct, $tableNames, $joinTypes, $joins, $where, $having, $order;
	
	function checkParameters() {
		//if (empty($this->select) OR empty($this->tableNames))
		//	return false;
		//if (!empty($this->joins) AND ((count($this->tableNames) != count($this->joins) + 1 OR count($this->joins) != count($this->joinTypes))))
		//	return false;
		return true;
	}
	
	function makeQueryText () {
		$qText = "SELECT ";
		if ($this->distinct)
			$qText .= "DISTINCT ";
		$qText .= arrToQueryString($this->select, false) . " FROM " . $this->tableNames[0];
		if (count($this->joins))
			for ($i = 0; $i < count($this->joins); $i++)
				$qText .= " " . $this->joinTypes[$i] . " " . $this->tableNames[$i+1] . " ON " . $this->joins[$i];
		if (!empty($this->where))
			$qText .= " WHERE " . $this->where;
		if (!empty($this->having))
			$qText .= " HAVING " . $this->having;
		if (!empty($this->order))
			$qText .= " ORDER BY" . $this->order;
		$qText .= ";";
		$this->sqlQuery = $qText;
		return;
	}
}

class insertSQL extends  basicSQL {
	
	var $insertData, $tableName, $cols;
	
	function checkParameters() {
		if (empty($this->insertData) OR empty($this->tableName) OR empty($this->cols))
			return false;
		return true;
	}
	
	function makeQueryText () {
			$this->sqlQuery = "INSERT INTO " . $this->tableName . " (" . arrToQueryString($this->cols, false) . ") VALUES (" . arrToQueryString($this->insertData, true) . ")";
			return;
	}
}

class updateSQL extends  basicSQL {

	var $update, $tableName, $where;

	function checkParameters() {
		if (empty($this->update) OR empty($this->tableName) OR empty($this->where))
			return false;
		return true;
	}

	function makeQueryText () {
		$this->sqlQuery = "UPDATE " . $this->tableName . " SET " . $this->update . " WHERE " . $this->where;
		return;
	}
}
	
class deleteSQL extends  basicSQL {

	var $tableName, $where;

	function checkParameters() {
		if (empty($this->tableName) OR empty($this->where))
			return false;
		return true;
	}

	function makeQueryText () {
		$this->sqlQuery = "DELETE FROM " . $this->tableName . " WHERE " . $this->where;
		return;
	}
}

class createTableSQL extends  basicSQL {

	var $name, $cols, $colTypes;

	function checkParameters() {
		if (count($cols) != count($colTypes))
			return false;
		if (empty($this->name) OR empty($this->cols) OR empty($this->colTypes))
			return false;
		return true;
	}

	function makeQueryText () {
		$qText =  "CREATE TABLE " . $this->name . " (`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
		for ($i = 0; $i < count($cols); $i++)
			$qText .= ", `" . $this->cols[$i] . "` " . $this->colTypes[$i];
		$qText .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
		$this->sqlQuery = $qText;
		return;
	}
}
	
?>