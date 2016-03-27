<?php
	if (!isset($_POST["email"]) || !isset($_POST["pass"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	$user = getUser($conn);
// 	if (isset($user) && $user['access'] != '3') {
// 		$statusMessage = makeStatusMessage(12,"error", "Already logged in.");
// 		mysqli_close($conn);
// 		return;
// 	}

	$user = $conn->real_escape_string($_POST["email"]);
	$pass = $conn->real_escape_string($_POST["pass"]);
	$pass = md5($pass);
	$selQ = new selectSQL($conn);
	$selQ->select = array("id");
	$selQ->tableNames = array ("users");
	$selQ->where = "user='".$user."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() > 0) {
		$statusMessage = makeStatusMessage(13,"error");
		mysqli_close($conn);
		return;
	}
	
	$insSQL = new insertSQL($conn);
	$insSQL->insertData = array($user, $pass, "0");
	$insSQL->cols = array("user", "password", "access");
	$insSQL->tableName = "users";
	
	if (!$insSQL->executeQuery()) {
		$statusMessage = $insSQL->status;
		mysqli_close($conn);
		return;
	}
	
	include_once 'signin.php';
	
?>