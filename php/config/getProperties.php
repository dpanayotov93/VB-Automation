<?php

///?????????????????????????????????????????????
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$result = getLanguages($conn);
	if (is_null($result)) {
		$statusMessage = makeStatusMessage(2, "error");
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
		$statusMessage = makeStatusMessage(53,"error");
	else {
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(23,"success");
	}

	mysqli_close($conn);
	return;
?>