<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) 
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	else if (!isset($_POST["names"]) || !isset($_POST["desc"]) || !isset($_POST["name"])) {
		$result = getLanguages($conn);
		if (is_null($result)) 
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		else {
			$data = array("Unique name" => "name");
			while($row = $result->fetch_assoc()) {
				$data[] = array("Name ".$row["abreviation"] => "name".$row["abreviation"]);
				$data[] = array("Discription ".$row["abreviation"] => "desc".$row["abreviation"]);
			}
			$data[] = array("Link to image" => "imgurl");
			$data[] = array("Appears in filters" => "searchable");
			$statusMessage = makeStatusMessage(12342, "success", "Language info sent!");
		}
	} else {
		$select = array ("id");
		$tableNames = array ("properties");
		$where = "name = '".$_POST['name']."'";
		$result = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, NULL, $conn);
		
		if ($result->num_rows > 0) {
			$statusMessage = makeStatusMessage(20,"error","Property with that name already exists.");
			return;
		}
			
		$select = array("abreviation");
		$tableNames = array ("languages");
		$result = simpleSelect($select, $tableNames, NULL, NULL, NULL, NULL, NULL, $conn);
	
		if ($result->num_rows == 0) {
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
			return;
		}
	
		$insertData = array();
		$cols = array();
		while ($row = $result->fetch_assoc()) {
			$insertData[] = $_POST['names'][$row['abreviation']];
			$insertData[] = $_POST['desc'][$row['abreviation']];
			$cols[] = "name".$row['abreviation'];
			$cols[] = "desc".$row['abreviation'];
		}
		
		if (isset($_POST['searchable'])) {
			$insertData[] = "1";
			$cols[] = "searchable";
		}		
		
		$tableNames = array ("properties");
		
		$result = simpleInsert($insertData, $tableNames[0], $cols, $conn);
		if ($result) 
			$statusMessage = makeStatusMessage(24, "error", "Could not create property.");
		else 
			$statusMessage = makeStatusMessage(1234, "suscces", "Propery saved successfully.");
	}
	
	mysqli_close($conn);
	return;
?>