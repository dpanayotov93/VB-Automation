<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) 
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	else if (!isset($_POST["user"]) || !isset($_POST["catid"]) || !isset($_POST["prodid"]) || (!isset($_POST['flat']) && !isset($_POST['percent']))) {
		$discounts = array();
		$selQ = new selectSQL($conn);
		
		$selq->select = array ("d.id as discountid","flat","percent","userid","user as username");
		$selq->tableNames = array ("discounts as d","users as u");
		$selq->joinTypes = array("JOIN");
		$selq->joins = array("userid = u.id");
		$selq->where = "categoryid = 0";
		
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		
		while ($row = $selQ->result->fetch_assoc())
			$discounts[] = array_merge($row,array("categoryid" => "0", "category" => "*","productid" => "0", "product" => "*"));
			
		$selQ = new selectSQL($conn);
		$selQ->select = array ("d.id as discountid","flat","percent","userid","user as username","categoryid","c.name".$language." as category","productid");
		$selQ->tableNames = array ("discounts as d","users as u","categories as c");
		$selQ->joinTypes = array("JOIN","JOIN");
		$selQ->joins = array("userid = u.id","categoryid = c.id");
		$selQ->where = "categoryid != 0";
		
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		
		$selQ->select = array("name".$language." as product"); //MUST FIX
		$res = $selQ->result;
		while ($row = $res->fetch_assoc()) {
			if ($row['productid'] != 0) {
				$selQ->tableNames = array("products");
				$selQ->where = "id = '".$row['productid']."'";
				
				if(!$selQ->executeQuery()) {
					$statusMessage = $selQ->status;
					mysqli_close($conn);
					return;
				}
				$tmparr = $res->fetch_assoc();
				$discounts[] = array_merge($row,$tmparr);
			} else
				$discounts[] = array_merge($row, array("product" => "*"));
		}
		$data = $discounts;
		$statusMessage = makeStatusMessage(234, "succes", "Information gathered");
	} else if (isset($_POST['discountid'])) {
		$delQ = new deleteSQL($conn);
		$delQ->tableName = "discounts";
		if (isset($_POST['delete'])) {
			$delQ->where = "id = ".$conn->real_escape_string($_POST['discountid']);
			if (!$delQ->executeQuery())
				$statusMessage = $delQ->status;
			else 
				$statusMessage = makeStatusMessage(234, "success", "Discount deteled.");
		} else {
			$updQ = new updateSQL($conn);
			$updQ->update = "userid='".$_POST['user']."',categoryid='".$_POST['catid']."',productid='".$_POST['prodid']."',flat='".$_POST['flat']."',percent='".$_POST['percent']."'";
			$updQ->where = "id = ".$_POST['discountid'];
			if ($updQ->executeQuery())
				$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");
			else
				$statusMessage = $updQ->status;
		}
	} else {
		$insQ = new insertSQL($conn);
		$insQ->insertData = array($_POST['user'],$_POST['catid'],$_POST['prodid'],$_POST['flat'],$_POST['percent']);
		$insQ->cols = array("userid","categoryid","productid","flat","percent");
		if ($insQ->executeQuery())
			$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");
		else 
			$statusMessage = $insQ->status;
	}
	
	mysqli_close($conn);
	return;
?>