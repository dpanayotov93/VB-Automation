<?php
	if (!isset($_POST["email"]) || !isset($_POST["pass"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	$user = $conn->real_escape_string($_POST["email"]);
	$pass = $conn->real_escape_string($_POST["pass"]);
	
	$pass = md5($pass);
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("id", "access");
	$selQ->tableNames = array("users");
	$selQ->where = "user='".$user."' AND password='".$pass."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 1) {
 		$statusMessage = makeStatusMessage(7,"success","Successful login!");
 		while ($row = $selQ->result->fetch_assoc())
 			$data[] = $row;
 	} else if ($selQ->getNumberOfResults() == 0)
 		$statusMessage = makeStatusMessage(8,"error","Invalid user or password!");
 	else
 		$statusMessage = makeStatusMessage(9,"error","Overlapping accounts.");
				
 	mysqli_close($conn);
	return;
?>