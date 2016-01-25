<?php
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$result = getLanguages($conn);
	if (is_null($result)) {
		$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		mysqli_close($conn);
		return;	
	}
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("id");
	while($row = $result->fetch_assoc()) {
		$selQ->select[] = "name".$row['abreviation'];
		$selQ->select[] = "desc".$row['abreviation'];
	}
	$selQ->select[] = "searchable";
	$selQ->tableName = array("properties");

	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0)
		$statusMessage = makeStatusMessage(14,"error","Error getting data from database...");
	else {
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(15,"success","Data gathered succesfully.");
	}

	mysqli_close($conn);
	return;
?>