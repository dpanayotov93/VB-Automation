<?php
	$conn = sqlConnectDefault();
	if(is_null($conn)) { 
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	} elseif (isset($_POST['showProps'])) {
		getProps($conn);
	} elseif (isset($_POST['id'])) {
		if (isset($_POST['delete']))
			delProp($conn,1);
		elseif (isset($_POST['restore']))
			delProp($conn,0);
		else		
			updProp($conn);
	} elseif (isset($_POST["names"]) && isset($_POST["desc"]) && isset($_POST["name"])) {
		insProp($conn);
	} else {
		getPropFields($conn);
	}
	
	mysqli_close($conn);
	return;
	
	//FUNCTIONS DOING THE STUFF:
	
	function updProp($conn) {
		$langArray = getLanguages($conn);
		if (is_null($langArray)) {
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
			mysqli_close($conn);
			return;
		}
	
		$id = $conn->real_escape_string($_POST['id']);
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$updQ->tableName = "properties";
		$updQ->where = "id = '".$id."'";
		while ($row = $langArray->fetch_assoc()) {
			if (isset($_POST['names'][$row['abreviation']])) 
				$updQ->update .= "name".$row['abreviation']." = '".$conn->real_escape_string($_POST['names'][$row['abreviation']])."',";
			if (isset($_POST['desc'][$row['abreviation']])) 
				$updQ->update .= "desc".$row['abreviation']." = '".$conn->real_escape_string($_POST['desc'][$row['abreviation']])."',";
		}
		if (isset($_POST['searchable']))
			$updQ->update .= "searchable = 1";
		else
			$updQ->update .= "searchable = 0";
	
		if (!$updQ->executeQuery())
			$statusMessage = $updQ->status;
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Property updated successfully.");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function insProp($conn) {
		$propName = $conn->real_escape_string($_POST['name']);
		$selQ = new selectSQL($conn);
		$selQ->select = array ("id");
		$selQ->tableNames = array ("properties");
		$selQ->where = "name = '".$propName."'";
		$selQ->executeQuery();
		if ($selQ->getNumberOfResults() > 0) {
			$statusMessage = makeStatusMessage(234, "error", "Propery with that name already exist.");
			return;
		}
		$langArray = getLanguages($conn);
		if (is_null($langArray)) {
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
			mysqli_close($conn);
			return;
		}
	
		$insQ = new insertSQL($conn);
		$insQ->insertData = array($conn->real_escape_string($_POST['name']));
		$insQ->cols = array("name");
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
		if (!empty($_POST['searchable'])) {
			$insQ->insertData[] = "1";
			$insQ->cols[] = "searchable";
		}
		if (!empty($_POST['langDependant'])) {
			$insQ->insertData[] = "1";
			$insQ->cols[] = "langDependant";
		}
		$insQ->tableName = "properties";
	
		if (!$insQ->executeQuery())
			$statusMessage = $insQ->status;
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Property saved successfully.");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function delProp($conn,$del) {
		$updQ = new updateSQL($conn);
		$updQ->tableName = "properties";
		$updQ->where = "id = '".$conn->real_escape_string($_POST['id'])."'";
		if ($del)
			$updQ->update .= "visible = 0";
		else 
			$updQ->update .= "visible = 1";

		if (!$updQ->executeQuery())
			$statusMessage = $updQ->sqlQuery;
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Property deleted successfully.");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getPropFields($conn) {
		$langArray = getLanguages($conn);
		if (is_null($langArray))
			$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		else {
			$data = array("Unique name" => "name");
			while($row = $langArray->fetch_assoc()) {
				$data = array_merge($data,array("Name ".$row["abreviation"] => "names[".$row["abreviation"]."]"));
				$data = array_merge($data,array("Discription ".$row["abreviation"] => "desc[".$row["abreviation"]."]"));
			}
			$tmp = array("Appears in filters" => "searchable","Differs in languages" => "langDependant");
			$data = array("input" => $data,"checkbox" => $tmp);
			$statusMessage = makeStatusMessage(12342, "success", "Language info sent!");
		}
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getProps($conn) {
		$selQ = new selectSQL($conn);
		$selQ->tableNames = array("properties");
		$selQ->select = array("*");
		if (isset($_POST['deleted']))
			$selQ->where = "visible != 1";
		else
			$selQ->where = "visible = 1";
		if (isset($_POST['id']))
			$selQ->where = " AND id = ".$conn->real_escape_string($_POST['id']);

		if (!$selQ->executeQuery())
			$statusMessage = $selQ->sqlQuery;
		elseif ($selQ->getNumberOfResults() == 0) {
			$statusMessage = makeStatusMessage(234, "error", "Nothing to select from database.");
		} else {
			while ($row = $selQ->result->fetch_assoc())
				$data[] = $row;
			$statusMessage = makeStatusMessage(15,"success","Data gathered succesfully.");
			$GLOBALS['data'] = $data;
		}
		$GLOBALS['statusMessage'] = $statusMessage;
	}
?>