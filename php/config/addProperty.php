<?php
	$conn = sqlConnectDefault();
	if(is_null($conn)) { 
		$statusMessage = makeStatusMessage(1,"error");
	} 
	$user = getUser($conn);
	if ($user['access'] != 3) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}

	$log = createLog(1); // ADD ADMIN LOG
	
	elseif (isset($_POST['showProps'])) {
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
		
		require_once 'languageConfig.php';
		$id = $conn->real_escape_string($_POST['id']);
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$updQ->tableName = "properties";
		$updQ->where = "id = '".$id."'";
		foreach ($langArr as $l) {
			if (isset($_POST['names'][$l])) 
				$updQ->update .= "name".$l." = '".$conn->real_escape_string($_POST['names'][$l])."',";
			if (isset($_POST['desc'][$l])) 
				$updQ->update .= "desc".$l." = '".$conn->real_escape_string($_POST['desc'][$l])."',";
		}
		if (isset($_POST['searchable']))
			$updQ->update .= "searchable = 1";
		else
			$updQ->update .= "searchable = 0";
	
		if (!$updQ->executeQuery())
			$statusMessage = $updQ->status;
		else
			$statusMessage = makeStatusMessage(33, "suscces");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function insProp($conn) {

		require_once 'languageConfig.php';
		$propName = $conn->real_escape_string($_POST['name']);
		$selQ = new selectSQL($conn);
		$selQ->select = array ("id");
		$selQ->tableNames = array ("properties");
		$selQ->where = "name = '".$propName."'";
		$selQ->executeQuery();
		if ($selQ->getNumberOfResults() > 0) {
			$statusMessage = makeStatusMessage(102, "error");
			return;
		}
	
		$insQ = new insertSQL($conn);
		$insQ->insertData = array($conn->real_escape_string($_POST['name']));
		$insQ->cols = array("name");
		foreach ($langArr as $l) {
			if (isset($_POST['names'][$l])) {
				$insQ->insertData[] = $conn->real_escape_string($_POST['names'][$l]);
				$insQ->cols[] = "name".$l;
			}
			if (isset($_POST['desc'][$l])) {
				$insQ->insertData[] = $conn->real_escape_string($_POST['desc'][$l]);
				$insQ->cols[] = "desc".$l;
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
			$statusMessage = makeStatusMessage(13, "suscces");
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
			$statusMessage = makeStatusMessage(43, "suscces");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getPropFields($conn) {
		
		require_once 'languageConfig.php';
		
		$data = array("Unique name" => "name");
		foreach ($langArr as $l) {
			$data = array_merge($data,array("Name ".$l => "names[".$l."]"));
			$data = array_merge($data,array("Discription ".$l => "desc[".$l."]"));
		}
		$tmp = array("Appears in filters" => "searchable","Differs in languages" => "langDependant");
		$data = array("input" => $data,"checkbox" => $tmp);
		$statusMessage = makeStatusMessage(29, "success");
		
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
			$statusMessage = makeStatusMessage(59, "error");
		} else {
			while ($row = $selQ->result->fetch_assoc())
				$data[] = $row;
			$statusMessage = makeStatusMessage(23,"success");
			$GLOBALS['data'] = $data;
		}
		$GLOBALS['statusMessage'] = $statusMessage;
	}
?>