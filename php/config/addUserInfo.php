<?php
	if (!isset($_POST["id"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	$id = $conn->real_escape_string($_POST['id']);
	$user = getUser($conn);
	
	if ($id != $user['id']) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}
	
	$log = createLog("","changeUserInfo","","",$id);

	$selQ = new selectSQL($conn);
	$selQ->select = array("u.id as uid", "i.userid as iid");
	$selQ->tableNames = array("user_info as i", "users as u");
	$selQ->joinTypes = array("RIGHT OUTER JOIN");
	$selQ->joins = array("u.id = i.userid");
	$selQ->where = "u.id='".$id."'";
	
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
		$insQ->cols = array("fname","lname","firm","address","city","country","email","phone");
		foreach ($insQ->cols as $c)
			if (isset($_POST[$c]) && !empty(($_POST[$c])))
				$insQ->insertData[] = $conn->real_escape_string($_POST[$c]);
			else
				$insQ->insertData[] = "";
		$insQ->cols[] = "userid";
		$insQ->insertData[] = $id;
		$insQ->tableName = "user_info";
		
		if ($insQ->executeQuery())
			$statusMessage = makeStatusMessage(10,"success");
		else 
			$statusMessage = $insQ->status;
	} else  {
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$columns = array("fname","lname","firm","address","city","country","email","phone");
		foreach ($columns as $c)
			if (isset($_POST[$c])) 
				$updQ->update .= $c."='". $conn->real_escape_string($_POST[$c])."',";
		if (empty($updQ->update)) {
			$statusMessage = makeStatusMessage(59,"error");
			mysqli_close($conn);
			return;
		}
		if(substr($update, -1, 1) == ',')
			$updQ->update = substr($updQ->update, 0, -1);
		$updQ->tableName = "user_info";
		$updQ->where = "id='".$id."'";
		if ($updQ->executeQuery())
			$statusMessage = makeStatusMessage(30,"success");
		else 
			$statusMessage = $updQ->status;
	}
	
	mysqli_close($conn);
	return;
?>