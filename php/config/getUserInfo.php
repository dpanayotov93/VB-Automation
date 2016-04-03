<?php
	if (!isset($_POST["user"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$username = $conn->real_escape_string($_POST["user"]);
	$user = getUser($conn);
	if ($user['access'] != 3 && $user['name'] != $username) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}
	if ($user['access'] != 3)
		$log = createLog("","profile");
	else 
		$log = createLog(1); // ADD ADMIN LOG
	
	$selQ = new selectSQL($conn);
	$selQ->select = array ("email", "fname", "lname", "firm", "address", "city", "country", "phone");
	$selQ->tableNames = array("user_info as i", "users as u");
	$selQ->joinTypes = array("RIGHT OUTER JOIN");
	$selQ->joins = array("u.id = i.userid");
	$selQ->where = "u.user='".$username."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_error($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0)
		$statusMessage = makeStatusMessage(5,"error");
	else if ($selQ->getNumberOfResults() > 1)
		$statusMessage = makeStatusMessage(16,"error");
	else {
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(20,"success");
	}
	mysqli_close($conn);
	return;
?>