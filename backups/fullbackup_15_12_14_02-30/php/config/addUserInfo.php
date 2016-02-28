<?php
	if (!isset($_POST["email"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$select = "u.id as uid, i.id as iid";
	$tableName = array("user_info as i", "users as u");
	$joinTypes = array("RIGHT OUTER JOIN");
	$joins = array("u.id = i.userid");
	$where = "u.email='".$_POST["email"]."'";
	
	$result = simpleSelect($select, $tableName, $joinTypes, $joins, $where, NULL, NULL, $conn);
	
	if (is_null($result)) {
		$statusMessage = makeStatusMessage(14,"error","Error getting data from database...");
		mysql_close($conn);
		return;
	}
	
	if ($result->num_rows > 1) {
		$statusMessage = makeStatusMessage(13,"error","Multiple results for this user...");
		mysql_close($conn);
		return;
	}
	
	$row = $result->fetch_assoc();
	
	if (is_null($row['iid'])) {
		$cols = "userid";
		$data = "'".$row['uid']."'";
		$columns = array("fname","lname","firm","address","city","country","phone");
		foreach ($columns as $c)
			if (!empty($_POST[$c])) {
				$data .= ",'".$_POST[$c]."'";
				$cols .= ",".$c;
			}
		$tableName = "user_info";
		if (simpleInsert($data, $tableName, $cols, $conn))
			$statusMessage = makeStatusMessage(10,"type:success","Data saved successfully!");
		else 
			$statusMessage = makeStatusMessage(11,"error","DB error while creating data!");
	} else  {
		$update = "";
		$columns = array("fname","lname","firm","address","city","country","phone");
		foreach ($columns as $c)
			if (!empty($_POST[$c])) 
				$update .= $c."='".$_POST[$c]."',";
		if(substr($update, -1, 1) == ',')
			$update = substr($update, 0, -1);
		$tableName = "user_info";
		$where = "id='".$row['iid']."'";
		if (simpleUpdate($update, $tableName, $where, $conn))
			$statusMessage = makeStatusMessage(12,"type:success","Data updated successfully!");
		else 
			$statusMessage = makeStatusMessage(11,"error","DB error while creating data!");
	}
	
	mysqli_close($conn);
	return;
?>