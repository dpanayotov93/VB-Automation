<?php
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$select = array ("email", "fname", "lname", "firm", "address", "city", "country", "phone");
	$tableName = array("user_info as i", "users as u");
	$joinTypes = array("RIGHT OUTER JOIN");
	$joins = array("u.id = i.userid");
	$where = "u.email='".$_POST["email"]."'";
	
	$result = simpleSelect($select, $tableName, $joinTypes, $joins, $where, NULL, NULL, $conn);
	
	if ($result->num_rows == 0)
		$statusMessage = makeStatusMessage(14,"error","Error getting data from database...");
	else if ($result->num_rows > 1)
		$statusMessage = makeStatusMessage(13,"error","Multiple results for this user...");
	else {
		while ($row = $result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(15,"success","Data gathered succesfully.");
	}
	mysqli_close($conn);
	return;
?>