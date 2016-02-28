<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) 
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	else if (!isset($_POST["user"]) || !isset($_POST["catid"]) || !isset($_POST["prodid"]) || (!isset($_POST['flat']) && !isset($_POST['percent']))) {
		$discounts = array();
		
		$select = array ("d.id as discountid","flat","percent","userid","user as username");
		$tableNames = array ("discounts as d","users as u");
		$joinTypes = array("JOIN");
		$joins = array("userid = u.id");
		$where = "categoryid = 0";
		$result = simpleSelect($select, $tableNames, $joinTypes, $joins, $where, NULL, NULL, $conn);
		if(is_null($result)) {
			$statusMessage = makeStatusMessage(234, "error", "Error getting discount information.");
			mysqli_close($conn);
			return;
		}
		while ($row = $result->fetch_assoc())
			$discounts[] = array_merge($row,array("categoryid" => "0", "category" => "*","productid" => "0", "product" => "*"));
			
		
		$select = array ("d.id as discountid","flat","percent","userid","user as username","categoryid","c.name".$language." as category","productid");
		$tableNames = array ("discounts as d","users as u","categories as c");
		$joinTypes = array("JOIN","JOIN");
		$joins = array("userid = u.id","categoryid = c.id");
		$where = "categoryid != 0";
		$result = simpleSelect($select, $tableNames, $joinTypes, $joins, $where, NULL, NULL, $conn);
		if (is_null($result)) {
			$statusMessage = makeStatusMessage(1234, "error", "No discounts.");
			mysqli_close($conn);
			return;
		}
		$select = array("name".$language." as product"); //MUST FIX
		while ($row = $result->fetch_assoc()) {
			if ($row['productid'] != 0) {
				$tableNames = array("products");
				$where = "id = '".$row['productid']."'";
				$res = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, NULL, $conn);
				echo mysqli_error($conn);
				if(is_null($res)) {
					$statusMessage = makeStatusMessage(345, "error", "Error with getting information on product.");
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
		$tableName = "discounts";
		if (isset($_POST['delete'])) {
			$where = "id = ".$_POST['discountid'];
			simpleDelete($tableName, $where, $conn);
		} else {
			$update = "userid='".$_POST['user']."',categoryid='".$_POST['catid']."',productid='".$_POST['prodid']."',flat='".$_POST['flat']."',percent='".$_POST['percent']."'";
			$where = "id = ".$_POST['discountid'];
			if (simpleUpdate($update, $tableName, $where, $conn))
				$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");
			else
				$statusMessage = makeStatusMessage(2342, "error", "Error while adding discount key.");
		}
	} else {
		$insertData = array($_POST['user'],$_POST['catid'],$_POST['prodid'],$_POST['flat'],$_POST['percent']);
		$cols = array("userid","categoryid","productid","flat","percent");
		if (simpleInsert($insertData, $tableName, $cols, $conn))
			$statusMessage = makeStatusMessage(2234, "success", "Data successfully added.");
		else 
			$statusMessage = makeStatusMessage(2342, "error", "Error while adding discount key.");
	}
	
	mysqli_close($conn);
	return;
?>