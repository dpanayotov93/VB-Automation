<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	if (empty($_POST['userid'])) {
		$statusMessage = makeStatusMessage(4, "error");
		return;
	}
	$userid = $conn->real_escape_string($_POST['userid']);
	$user = getUser($conn);
	if ($user['id'] != $userid) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}
	
	$log = createLog("","favorites","","",$userid);
	
	if (isset($_POST['add']) && (!empty($_POST['productid']) || !empty($_POST['categoryid']))) {
		$fieldArr = array("userid","productid","categoryid");
		$insQ = new insertSQL($conn);
		$insQ->insertData = array();
		foreach ($fieldArr as $f) {
			if (!empty($_POST[$f])) {
				$insQ->insertData[] = $conn->real_escape_string($_POST[$f]);
				$insQ->cols[] = $f;
			}
		}
		$insQ->tableName = "favorites";
		if (!$insQ->executeQuery()) {
			$statusMessage = $insQ->status;
			mysqli_close($conn);
			return;
		}
		
		$statusMessage = makeStatusMessage(15, "success");
		
	} else if (isset($_POST['remove']) && (!empty($_POST['productid']) || !empty($_POST['categoryid']))) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "favorites";
		$delQ->where = "userid = '".$userid."' AND ";
		if (isset($_POST['productid']))
			$delQ->where .= "productid = ".$conn->real_escape_string($_POST['productid'])."'";
		else if (isset($_POST['categoryid']))
			$delQ->where .= "categoryid = ".$conn->real_escape_string($_POST['categoryid'])."'";
		
		$delQ->tableName = "favorites";
		if (!$delQ->executeQuery()) {
			$statusMessage = $delQ->status;
			mysqli_close($conn);
			return;
		}

		$statusMessage = makeStatusMessage(45, "success");
	} else if (!empty($_POST['products']) || !empty($_POST['categories'])) {
		$selQ = new selectSQL($conn);
		$selQ->select = array ();
		$selQ->tableNames = array("favorites as f");
		$selQ->joins = array();
		$selQ->joinTypes = array();
		$selQ->where = "f.userid = ".$userid;
	
		if (isset($_POST['products'])) {
			$selQ->select[] = "p.names".$language." as `Product name`";
			$selQ->select[] = "p.id as `Product ID`";
			$selQ->tableNames[] = "products as p";
			$selQ->joins[] = "f.productid = p.id";
			$selQ->joinTypes[] = "JOIN";
		} else if (isset($_POST['categories'])) {
			$selQ->select[] = "c.name".$language." as `Category name`";
			$selQ->select[] = "c.id as `Category ID`";
			$selQ->tableNames[] = "categories as c";
			$selQ->joins[] = "f.productid = c.id";
			$selQ->joinTypes[] = "JOIN";
		}
	
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		if ($selQ->getNumberOfResults() < 1) {
			$statusMessage = makeStatusMessage(55, "error");
			mysqli_close($conn);
			return;
		}
		$data = array();
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
	
	
		$statusMessage = makeStatusMessage(25, "success");
	}
	
	mysqli_close($conn);
	return;


?>