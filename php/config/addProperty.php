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
	} else if (isset($_POST['id'])) {
		$langArray = getLanguages($conn);
		if (is_null($result)) {
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
			mysqli_close($conn);
			return;
		}
		
		$insQ = new insertSQL($conn);
		
		$insQ->insertData = array();
		$id = $conn->real_escape_string($_POST['id']);
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$updQ->tableNames = array ("properties");
		$selQ->where = "id = '".$id."'";
		while ($row = $langArray->fetch_assoc()) {
			if (isset($_POST['names'][$row['abreviation']])) {
				$updQ->update .= "name".$row['abreviation']." = '".$conn->real_escape_string($_POST['names'][$row['abreviation']]."',");
				$updQ->update .= "desc".$row['abreviation']." = '".$conn->real_escape_string($_POST['desc'][$row['abreviation']])."',";
			}
		}
		if (isset($_POST['searchable']))
			$updQ->update .= "searchable = 1";
		else 
			$updQ->update .= "searchable = 0";
		
		if (!$updQ->executeQuery())
			$statusMessage = makeStatusMessage(24, "error", "Could not update property.");
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Propery updated successfully.");
		
	} else {
		$propName = $conn->real_escape_string($_POST['name']);
		$selQ = new selectSQL($conn);
		$selQ->select = array ("id");
		$selQ->tableNames = array ("properties");
		$selQ->where = "name = '".$propName."'";
		
		$selQ->executeQuery();
		
		if ($selQ->getNumberOfResults() > 0) {
			$statusMessage = $selQ->status;
			return;
		}
			
		$langArray = getLanguages($conn);
		if (is_null($result)) {
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
			mysqli_close($conn);
			return;
		}
		
		$insQ = new insertSQL($conn);
	
		$insQ->insertData = array();
		$insQ->cols = array();
		while ($row = $langArray->fetch_assoc()) {
			if (isset($_POST['names'][$row['abreviation']])) {
				$insQ->insertData[] = $conn->real_escape_string($_POST['names'][$row['abreviation']]);
				$insQ->cols[] = "name".$row['abreviation'];
			}
			if (isset($_POST['desc'][$row['abreviation']])) {
				$insQ->insertData[] = $conn->real_escape_string($_POST['desc'][$row['abreviation']]);
				$insQ->cols[] = "desc".$row['abreviation'];
			}
		}
		
		if (isset($_POST['searchable'])) {
			$insQ->insertData[] = "1";
			$insQ->cols[] = "searchable";
		}		
		
		$insQ->tableName = "properties";
		
		if (!$selQ->executeQuery()) 
			$statusMessage = makeStatusMessage(24, "error", "Could not create property.");
		else 
			$statusMessage = makeStatusMessage(1234, "suscces", "Propery saved successfully.");
	}
	
	mysqli_close($conn);
	return;
?>