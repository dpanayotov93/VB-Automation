<?php

///?????????????????????????????????????????????
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	require_once 'languageConfig.php';
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("id");
	foreach ($langArr as $l) {
		$selQ->select[] = "name".$l;
		$selQ->select[] = "desc".$l;
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