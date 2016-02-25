<?php

$debugSQL = false;
if (isset($_GET["debug"]) OR isset($_POST["debug"]))
	$debugSQL = True;


	iconv_set_encoding("internal_encoding", "UTF-8");
	iconv_set_encoding("output_encoding", "UTF-8");
	iconv_set_encoding("input_encoding", "UTF-8");
	
	
include "sqlClasses.inc";


function sqlConnectDefault() {
	$servername = "localhost";
	$username = "emag";
	$password = "emagpass";
	$dbname = "emag";

	$conn = new mysqli($servername, $username, $password, $dbname);
	$conn->set_charset("utf8");
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

function checkTable($conn,$tableName) {
	if ($conn->query("select 1 from `".$conn->real_escape_string($tableName)."` LIMIT 1"))
		return true;
	else 
		return false;
}

function debugMessages($msg, $type) {
	if ($GLOBALS[$type])
		echo "DEBUG INFO[".$type."]:<br>".$msg."<br></br>";
}

function makeStatusMessage($id, $type, $message) {
	$sm = array ("type" => $type, "message" => $message);
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

function checkUserCredentials($id,$type) { // TO DO!!!!!
	return 1;
}
?>