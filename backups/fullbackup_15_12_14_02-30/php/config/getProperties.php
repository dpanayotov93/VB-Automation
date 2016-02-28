<?php
	$conn = sqlConnectDefault();

	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$result = getLanguages($conn);
	if (is_null($result))
		$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
	else {
		$select = array("id");
		while($row = $result->fetch_assoc()) {
			$select[] = "name".$row['abreviation'];
			$select[] = "desc".$row['abreviation'];
		}
		$select[] = "searchable";
		$tableName = array("properties");
	
		$result = simpleSelect($select, $tableName, null, null, null, NULL, NULL, $conn);
		
		if ($result->num_rows == 0)
			$statusMessage = makeStatusMessage(14,"error","Error getting data from database...");
		else {
			while ($row = $result->fetch_assoc())
				$data[] = $row;
			$statusMessage = makeStatusMessage(15,"success","Data gathered succesfully.");
		}
	}
	mysqli_close($conn);
	return;
?>