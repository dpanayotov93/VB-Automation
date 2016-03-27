<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	$user = getUser($conn);
	if ($user['access'] != 3) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}

	if (!empty($_POST['userid']) && !empty($_POST['type'])) {
		$userid = $conn->real_escape_string($_POST['userid']);
		$type = $conn->real_escape_string($_POST['type']);
		if (!empty($_POST['minprice']))
			$min = $conn->real_escape_string($_POST['minprice']);
		
		$selQ = new selectSQL($conn);
		$selQ->select = array("id");
		$selQ->tableNames = array("delivery_discounts");
		$selQ->where = "userid = '".$userid."'";
		
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		if ($selQ->getNumberOfResults() > 0) {
			$disQ = new updateSQL($conn);
			$disQ->tableName = "delivery_discounts";
			$disQ->where = "userid = '".$userid."'";
			$disQ->update = "type = '".$type."'";
			if(isset($min))
				$disQ->update = " AND minprice = '".$min."'";
			
		} else {
			$disQ = new insertSQL($conn);
			$disQ->tableName = "delivery_discounts";
			$disQ->cols = array("type","userid");
			$disQ->insertData = array($type,$userid);
			if (isset($mmin)) {
				$disQ->cols[] = "minprice";
				$disQ->insertData[] = $min;
			}
		}

		if (!$disQ->executeQuery()) {
			$statusMessage = $disQ->status;
			mysqli_close($conn);
			return;
		}
		
		$statusMessage = makeStatusMessage(14, "success");
		
	}

