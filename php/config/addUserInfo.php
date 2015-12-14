<?php
	if (!isset($_POST["id"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$id = $conn->real_escape_string($_POST['id']);
	
	$selQ->select = "u.id as uid, i.userid as iid";
	$selQ->tableName = array("user_info as i", "users as u");
	$selQ->joinTypes = array("RIGHT OUTER JOIN");
	$selQ->joins = array("u.id = i.userid");
	$selQ->where = "u.email='".$id."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() > 1) {
		$statusMessage = $selQ->status;
		mysql_close($conn);
		return;
	}
	
	$row = $selQ->result->fetch_assoc();
	
	if (is_null($row['iid'])) {
		$insQ = new insertSQL($conn);
		$insQ->insertData = array();
		$insQ->cols = array("fname","lname","firm","address","city","country","phone");
		foreach ($columns as $c)
			if (!empty($_POST[$c])) 
				$insQ->insertData[] = $conn->real_escape_string($_POST[$c]);
		$insQ->cols[] = "userid";
		$insQ->insertData[] = $id;
		$insQ->tableName = "user_info";
		
		if ($insQ->executeQuery())
			$statusMessage = makeStatusMessage(10,"type:success","Data saved successfully!");
		else 
			$statusMessage = $insQ->status;
	} else  {
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$columns = array("fname","lname","firm","address","city","country","phone");
		foreach ($columns as $c)
			if (!empty($_POST[$c])) 
				$updQ->update .= $c."='". $conn->real_escape_string($_POST[$c])."',";
		if(substr($update, -1, 1) == ',')
			$updQ->update = substr($updQ->update, 0, -1);
		$updQ->tableName = "user_info";
		$updQ->where = "id='".$id."'";
		if ($updQ->executeQuery())
			$statusMessage = makeStatusMessage(12,"type:success","Data updated successfully!");
		else 
			$statusMessage = $updQ->status;
	}
	
	mysqli_close($conn);
	return;
?>