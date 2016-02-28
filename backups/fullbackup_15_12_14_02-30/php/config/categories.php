<?php

	$conn = sqlConnectDefault();
	
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}

	$select = array ("id","parentid","name".$language,"desc".$language,"imgurl");
	$tableName = array("categories");
	$data = getCat("parentid IS NULL");
	if (!empty($data))
		$statusMessage = makeStatusMessage(23, "error", "Data successfully sent.");
	else
		$statusMessage = makeStatusMessage(23, "error", "Data successfully sent.");
	
	mysqli_close($conn);
	return;
	
function getCat($where) {
	$result = simpleSelect($GLOBALS['select'], $GLOBALS['tableName'], NULL, NULL, $where, NULL, NULL, $GLOBALS['conn']);
	$data = array();
	if (is_null($result)) {
		return NULL;
	} else {
		while ($row = $result->fetch_assoc()) {
			$subCats = getCat("parentid = '".$row['id']."'");
			if ($subCats)
				$data[] = array_merge($row, array("subCategories" => $subCats));
			else 
				$data[] = $row;
		}
		return $data;
	}
	
}
?>