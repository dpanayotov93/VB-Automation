<?php

	$conn = sqlConnectDefault();
	
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}

	$data = getCat("parentid IS NULL");
	if (empty($data))
		$statusMessage = makeStatusMessage(23, "error", "No categories found.");
	else
		$statusMessage = makeStatusMessage(23, "success", "Data successfully gathered.");
	
	mysqli_close($conn);
	return;
	
function getCat($where) {
	$selQ = new selectSQL($GLOBALS['conn']);
	$selQ->select = array ("id","parentid","name".$GLOBALS['language'],"desc".$GLOBALS['language'],"imgurl");
	$selQ->tableNames = array("categories");
	$selQ->where = $where;
	if (!$selQ->executeQuery()) 
		return;
	if ($selQ->getNumberOfResults() > 0) {
		while ($row = $selQ->result->fetch_assoc()) {
			$subCats = getCat("parentid = '".$row['id']."'");
			if ($subCats)
				$data[] = array_merge($row, array("subCategories" => $subCats));
			else 
				$data[] = $row;
		}
		return $data;
	} else
		return;
}
?>