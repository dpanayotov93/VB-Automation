<?php
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$email = $conn->real_escape_string($_POST["email"]);
	
	$selQ = new selectSQL($conn);
	$selQ->select = array ("email", "fname", "lname", "firm", "address", "city", "country", "phone");
	$selQ->tableName = array("user_info as i", "users as u");
	$selQ->joinTypes = array("RIGHT OUTER JOIN");
	$selQ->joins = array("u.id = i.userid");
	$selQ->where = "u.email='".$email."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_error($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0)
		$statusMessage = makeStatusMessage(14,"error","Error getting data from database...");
	else if ($selQ->getNumberOfResults() > 1)
		$statusMessage = makeStatusMessage(13,"error","Multiple results for this user...");
	else {
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(15,"success","Data gathered succesfully.");
	}
	mysqli_close($conn);
	return;
?>