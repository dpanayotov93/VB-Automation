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

	$log = createLog(1); // ADD ADMIN LOG
	
	if (isset($_POST['delete']) && isset($_POST['discountid'])) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "discounts";
		$delQ->where = "id = ".$conn->real_escape_string($_POST['discountid']);
		if (!$delQ->executeQuery())
			$statusMessage = $delQ->status;
		else 
			$statusMessage = makeStatusMessage(46, "success");
	}else if (!(isset($_POST["catid"]) || isset($_POST["prodid"])) || !(isset($_POST['flat']) || isset($_POST['percent']))) {
		$discounts = array();
		$selQ = new selectSQL($conn);
		
		$selQ->select = array ("d.id as `Discount ID`","userid as `User ID`","user as User","flat as `Flat Discount`","percent as `Percent Discount`","minprice as `Minumun price for discount`","categoryid as `Category ID`","c.name".$language." as `Category Name`","productid as `Product ID`","p.names".$language." as `Product Name`");
		$selQ->tableNames = array ("discounts as d","users as u","categories as c","products as p");
		$selQ->joinTypes = array("LEFT JOIN","LEFT JOIN","LEFT JOIN");
		$selQ->joins = array("userid = u.id","d.categoryid = c.id","d.productid = p.id");
		if (!empty($_POST['userid'])) 
			$selQ->where = "userid = ".$conn->real_escape_string($_POST['userid']);
		
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		
		if ($selQ->getNumberOfResults() < 1) {
			$statusMessage = makeStatusMessage(56, "error");
			mysqli_close($conn);
			return;
		}
		
		while ($row = $selQ->result->fetch_assoc()) {
			if ($row['User ID'] == 0) 
				$row['User ID'] = "All Users";
			if ($row['Category ID'] == 0) 
				$row['Category Name'] = "All Categories";
			if ($row['Product ID'] == 0) 
				$row['Product Name'] = "All Products";
			$discounts[] = $row;
		}
		
		$data = $discounts;
		$statusMessage = makeStatusMessage(26, "succes");
	} else if (isset($_POST['discountid'])) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "discounts";
		$updQ = new updateSQL($conn);
		$updQ->update = "userid='".$conn->real_escape_string($_POST['userid'])."',categoryid='".$conn->real_escape_string($_POST['catid'])."',productid='".$conn->real_escape_string($_POST['prodid'])."',flat='".$conn->real_escape_string($_POST['flat'])."',percent='".$conn->real_escape_string($_POST['percent'])."',minprice='".$conn->real_escape_string($_POST['minprice'])."'";
		$updQ->where = "id = ".$conn->real_escape_string($_POST['discountid']);
		if ($updQ->executeQuery())
			$statusMessage = makeStatusMessage(46, "success");
		else
			$statusMessage = $updQ->status;
	} else if (isset($_POST['userid'])) {
		$insQ = new insertSQL($conn);
		$insQ->tableName = "discounts";
		$insQ->insertData = array($conn->real_escape_string($_POST['userid']));
		$insQ->cols = array("userid");

		if (!empty($_POST['catid'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['catid']);
			$insQ->cols[] = "categoryid";
		}
		if (!empty($_POST['flat'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['flat']);
			$insQ->cols[] = "flat";
		}
		if (!empty($_POST['percent'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['percent']);
			$insQ->cols[] = "percent";
		}
		if (!empty($_POST['minprice'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['minprice']);
			$insQ->cols[] = "minprice";
		}
		$selQ = new selectSQL($conn);
		$selQ->select = array("id");
		$selQ->tableNames = array("dicounts");
		if (isset ($_POST['prodid']) && count($_POST['prodid'])) {
			$insCount = count($insQ->insertData);
			foreach ($_POST['prodid'] as $pid) {
				$pid = $conn->real_escape_string($pid);
				$selQ->where = "productid = '".$pid."' AND userid = '".$conn->real_escape_string($_POST['userid'])."'";
				if (!$selQ->executeQuery()) {
					$statusMessage = $selQ->status;
					$error = 1;
				} else if ($selQ->getNumberOfResults()) {
					$statusMessage = makeStatusMessage(105, "error");
					$error = 1;
				} else {
					$insQ->insertData[$insCount] = $pid;
					$insQ->cols[$insCount] = "productid";
					if (!$insQ->executeQuery()) {
						$statusMessage = $insQ->status;
						$error = 1;
					}
				}
			}
		} else if (!empty($_POST['catid'])) {
			$selQ->where = "categoryid = '".$conn->real_escape_string($_POST['catid'])."' AND userid = '".$conn->real_escape_string($_POST['userid'])."'";
			if (!$selQ->executeQuery()) {
				$statusMessage = $selQ->status;
				$error = 1;
			} else if ($selQ->getNumberOfResults()) {
				$statusMessage = makeStatusMessage(104, "error");
				$error = 1;
			} else {
				$insQ->insertData[] = $conn->real_escape_string($_POST['catid']);
				$insQ->cols[] = "categoryid";
				if (!$insQ->executeQuery()) {
					$statusMessage = $insQ->status;
					$error = 1;
				}
			}
		} else 
			$statusMessage = makeStatusMessage(4, "error");
		
		if (!isset($error))
			$statusMessage = makeStatusMessage(16, "success");

	} else 
		$statusMessage = makeStatusMessage(4, "error");
	
	mysqli_close($conn);
	return;
?>