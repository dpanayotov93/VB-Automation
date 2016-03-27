<?php
	// SET BRUTEFORCE PARAMETERS
	$loginTimeout = 5 * 60;
	$maxLoginAttempts = 10;

	if (!isset($_POST["email"]) || !isset($_POST["pass"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$user = $conn->real_escape_string($_POST["email"]);
	$pass = $conn->real_escape_string($_POST["pass"]);
	$pass = md5($pass);
	$userdata = getUser($conn,$user,$pass);
	
	if ($maxLoginAttempts < checkLoginAttempts($conn, $loginTimeout) OR $maxLoginAttempts < checkLoginAttempts($conn, $loginTimeout, $user)) {
		$statusMessage = makeStatusMessage(15, "error");
		mysqli_close($conn);
		return;
	}
	
	if ($userdata) {
 		$statusMessage = makeStatusMessage(1,"success");
 		// MAKE SESSION ENTRY!!!!
 		$_SESSION['username'] = $user;
 		$_SESSION['password'] = $pass;
 		$data[] = $userdata;
 		$result = 1;
 	} else {
 		$statusMessage = makeStatusMessage(11,"error");
 		$result = 0;
 	}
 	
 	$insQ = new insertSQL($conn);
 	$insQ->cols = array("date","ip","user","result");
 	$insQ->insertData = array(time(),$_SERVER['REMOTE_ADDR'],$user,$result);
 	$insQ->tableName = "login_logs";
 	if (!$insQ->executeQuery()) {
 		$statusMessage = makeStatusMessage(12, "error");
 		mysqli_close($conn);
 		return;
 	}
 	
 	mysqli_close($conn);
	return;
?>