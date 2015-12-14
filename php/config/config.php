<?php

$debugSQL = false;
if (isset($_GET["debug"]) OR isset($_POST["debug"]))
	$debugSQL = True;

include "sqlClasses.inc";

function sqlConnectDefault() {
	$servername = "localhost";
	$username = "emag";
	$password = "emagpass";
	$dbname = "emag";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if (checkConnection($conn))
		return $conn;
	else 
		return NULL;
}

function sqlConnectDB($dbname) {
	$servername = "localhost";
	$username = "emag";
	$password = "emagpass";

	$conn = new mysqli($servername, $username, $password, $dbname);
	if (checkConnection($conn))
		return $conn;
}

function checkConnection($conn) {
	if ($conn->connect_error) {
		debugMessages("Connection failed: " . $conn->connect_error, "debugSQL");
		return false;
	} else 
		return true;
}

function simpleSelect($select, $tableNames, $joinTypes, $joins, $where, $having, $order, $conn) {
	if (!checkConnection($conn))
		return NULL;
	
	if (!empty($joins) AND (count($tableNames) != count($joins) + 1 OR count($joins) != count($joinTypes))) {
			debugMessages("Incorrect sql query parameters.".mysqli_error($conn),"debugSQL");
			return NULL;
	}
	
	$tableName = $tableNames[0];

	$sqlQuery = "SELECT " . arrToQueryString($select, false) . " FROM " . $tableName;
	
	if (count($joins))
		for ($i = 0; $i < count($joins); $i++) 
			$sqlQuery .= " " . $joinTypes[$i] . " " . $tableNames[$i+1] . " ON " . $joins[$i];
	if (!empty($where))
		$sqlQuery .= " WHERE " . $where;
	if (!empty($having))
		$sqlQuery .= " HAVING " . $having;
	if (!empty($order))
		$sqlQuery .= " ORDER BY" . $order;
	$sqlQuery .= ";";
	
	$conn->real_escape_string($sqlQuery);
	
	debugMessages($sqlQuery,"debugSQL");
	
	$res = $conn->query($sqlQuery);
	if (!empty(mysqli_error($conn)))
		debugMessages(mysqli_error($conn), "debugSQL");
	return $res; 
}

function simpleInsert($insertData, $tableName, $cols, $conn) {
	$sqlQuery = "INSERT INTO " . $tableName . " (" . arrToQueryString($cols, false) . ") VALUES (" . arrToQueryString($insertData, true) . ")";
	$conn->real_escape_string($sqlQuery);
	debugMessages($sqlQuery,"debugSQL");
	$res = $conn->query($sqlQuery);
	if (!empty(mysqli_error($conn)))
		debugMessages(mysqli_error($conn), "debugSQL");
	return $res;
}
	
function simpleUpdate($update, $tableName, $where, $conn) {
	$sqlQuery = "UPDATE " . $tableName . " SET " . $update . " WHERE " . $where;
	$conn->real_escape_string($sqlQuery);
	debugMessages($sqlQuery,"debugSQL");
	$res = $conn->query($sqlQuery);
	if (!empty(mysqli_error($conn)))
		debugMessages(mysqli_error($conn), "debugSQL");
	return $res;
}
	
function simpleDelete($tableName, $where, $conn) {
	$sqlQuery = "DELETE FROM " . $tableName . " WHERE " . $where;
	$con->real_escape_string($sqlQuery);
	debugMessages($sqlQuery,"debugSQL");
	$res = $conn->query($sqlQuery);
	if (!empty(mysqli_error($conn)))
		debugMessages(mysqli_error($conn), "debugSQL");
	return $res;
}

function createTable($name, $cols, $colTypes, $conn) {
	if (count($cols) != count($colTypes)) {
		if ($GLOBALS["debugSQL"])
			echo "Incorrect sql query parameters.";
		return NULL;
	}
	$sqlQuery = "CREATE TABLE " . $name . " (`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ";
	for ($i = 0; $i < count($cols); $i++) 
		$sqlQuery .= ", `" . $cols[$i] . "` " . $colTypes[$i];
	$sqlQuery .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
	
	$conn->real_escape_string($sqlQuery);
	debugMessages($sqlQuery,"debugSQL");
	$res = $conn->query($sqlQuery);
	if (!empty(mysqli_error($conn)))
		debugMessages(mysqli_error($conn), "debugSQL");
	return $res;
}

function debugMessages($msg, $type) {
	if ($GLOBALS[$type])
		echo "DEBUG INFO[".$type."]:<br>".$msg."<br></br>";
}

function makeStatusMessage($id, $type, $message) {
	$sm = array ('id' => $id,"type" => $type, "message" => $message);
	return $sm;
}

function utf8_string_array_encode(&$array){
	$func = function(&$value,&$key){
		if(is_string($value))
			$value = utf8_encode($value);
		if(is_string($key))
			$key = utf8_encode($key);
		if(is_array($value))
			utf8_string_array_encode($value);
	};
	array_walk($array,$func);
	return $array;
}

function arrToQueryString($arr, $inQuotes) {
	$res = "";
	if($inQuotes)
		foreach ($arr as $s)
			$res .= "'".$s."',";
	else
		foreach ($arr as $s)
			$res .= $s.",";
	$res = substr($res, 0, -1);
	return $res;
}

function getLanguages($conn) {
	$selQ = new selectSQL($conn);
	$selQ->select = array("abreviation");
	$selQ->tableNames = array ("languages");
	$selQ->executeQuery();
	
	if ($selQ->getNumberOfResults() == 0)
		return null;
	else 
		return $selQ->result;
}
?>