<?php

	$conn = sqlConnectDefault();	
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}

	if (!$_POST['show']) {
		$statusMessage = makeStatusMessage(1243, "error", "Incomplete quiery request.");
		return;
	}
	
	$selQ = new selectSQL($conn);
 	$selQ->select = array ("u.id as id","u.user as User","i.email as `e-mail`","i.fname as `First Name`","i.lname As `Last Name`","i.phone as Phone","i.firm as Firm","i.country as Country","i.city as City","i.address as Address");
	$selQ->tableNames = array("users as u","user_info as i");
	$selQ->joins = array("u.id = i.userid");
	$selQ->joinTypes = array("LEFT JOIN");
	if (!empty($_POST['userid']))
		$selQ->where = "id = ".$conn->real_escape_string($_POST['userid']);
	
	if (isset($_POST['delivery'])) {
		$selQ->select[] = "type as Type";
		$selQ->select[] = "minpirce as `Minimum price`";
		$selQ->tableNames[] = "delivery_discounts as dd";
		$selQ->joins[] = "u.id = dd.userid";
		$selQ->joinTypes[] = "LEFT JOIN";
	}
	
	if (isset($_POST['countPerPage']) && is_int($_POST['countPerPage']))
		if (isset($_POST['page']) && is_int($_POST['page']))
			$selQ->limit = ($_POST['countPerPage'] - 1)*$_POST['page'].",".$_POST['countPerPage'];
			else
				$selQ->limit = $conn->real_escape_string($_POST['countPerPage']);
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() < 1) {
		$statusMessage = makeStatusMessage(123, "error", "No users to show");
		mysqli_close($conn);
		return;
	}
	$data = array();
	while ($row = $selQ->result->fetch_assoc()) {
		if (isset($_POST['delivery']))
			if($row['Type'] == 0) 
				$row['Type'] = "Covered by client.";
			elseif ($row['Type'] == 1)
				$row['Type'] = "Covered by provider.";
			elseif ($row['Type'] == 2)
				$row['Type'] = "Covered by provider above a minimum order price.";
		$data[] = $row;
	}

	$statusMessage = makeStatusMessage(123, "success", "User info sent.");
	mysqli_close($conn);
	return;
		
	
?>