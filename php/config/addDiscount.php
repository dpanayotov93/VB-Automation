<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) 
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	else if (isset($_POST['delete']) && isset($_POST['discountid'])) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "discounts";
		$delQ->where = "id = ".$conn->real_escape_string($_POST['discountid']);
		if (!$delQ->executeQuery())
			$statusMessage = $delQ->status;
		else 
			$statusMessage = makeStatusMessage(234, "success", "Discount deteled.");
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
			$statusMessage = makeStatusMessage(123, "error", "No discounts to show.");
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
		$statusMessage = makeStatusMessage(234, "succes", "Information gathered");
	} else if (isset($_POST['discountid'])) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "discounts";
		$updQ = new updateSQL($conn);
		$updQ->update = "userid='".$conn->real_escape_string($_POST['userid'])."',categoryid='".$conn->real_escape_string($_POST['catid'])."',productid='".$conn->real_escape_string($_POST['prodid'])."',flat='".$conn->real_escape_string($_POST['flat'])."',percent='".$conn->real_escape_string($_POST['percent'])."',minprice='".$conn->real_escape_string($_POST['minprice'])."'";
		$updQ->where = "id = ".$conn->real_escape_string($_POST['discountid']);
		if ($updQ->executeQuery())
			$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");
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
		if (isset ($_POST['prodid']) && count($_POST['prodid'])) {
			$insCount = count($insQ->insertData);
			foreach ($_POST['prodid'] as $pid) {
				$insQ->insertData[$insCount] = $conn->real_escape_string($pid);
				$insQ->cols[$insCount] = "productid";
				if (!$insQ->executeQuery()) {
					$statusMessage = $insQ->status;
					$error = 1;
				}
			}
		} else {
			if (!$insQ->executeQuery()) {
				$statusMessage = $insQ->status;
				$error = 1;
			}
		}
		if (!isset($error))
			$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");

	} else 
		$statusMessage = makeStatusMessage(14, "error", "Incomplete query request.");
	
	mysqli_close($conn);
	return;
?>