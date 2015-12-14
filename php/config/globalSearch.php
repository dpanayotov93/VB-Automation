<?php
	if (!isset($_POST["searchValue"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	if (isset($_POST['categoryid'])) {
	
		$select = array("id");
		$tableNames = array ("categories");
		$where = "id = '".$_POST['categoryid']."'";
		$result = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, NULL, $conn);
		
		if ($result->num_rows == 0) {
			$statusMessage = makeStatusMessage(24,"error","Category not found!");
			mysqli_close($conn);
			return;
		} else {
			$tmp = $result->fetch_assoc();
			$catid = $tmp['id'];
		}
	}
	
	$select = array("props.name as n");
	$tableNames = array ("props_to_prods as ids", "properties as props");
	$where = "ids.catid = '".$_POST['id']."' AND props.searchable = '1'";
	$joinTypes = array ("JOIN");
	$joins = array ("ids.propid = props.id");
	$result = simpleSelect($select, $tableNames, $joinTypes, $joins, $where, NULL, NULL, $conn);
	while ($row = $result->fetch_assoc())
		$propNames[] = $row['n'];
	
	$tableNames = array ("products_".$_POST['id']);

	$whereFilters = "";
	if (isset($_POST['filters'])) {
		foreach ($_POST['filters'] as $key => $filter) {
			if (!empty($filter)) {
				$whereFilters .=  $key."='".$filter."' AND ";
			}
		}
		$whereFilters = substr($whereFilters, 0, -5);
	}
	if (isset($_POST['searchFilter'])) {
		if (!empty($whereFilters))
			$whereFilters .= " AND ";
		$whereFilters .= "(";
		foreach ($propNames as $p) {
			$whereFilters .= $p ." LIKE '%".$_POST['searchFilter']."%' OR ";
		}
		$whereFilters = substr($whereFilters, 0, -4);
		$whereFilters .= ")";
	}
	
	$dataF = array();
	foreach ($propNames as $p) {
		if (!isset($_POST['filters'][$p])) {
			$select = array("DISTINCT ".$p);
			$result = simpleSelect($select, $tableNames, NULL, NULL, $whereFilters, NULL, NULL, $conn);
			if ($result->num_rows != 0) {
				$filters = array();
				while ($row = $result->fetch_assoc())
					$filters[] = $row[$p];
				$dataF[] = array("name" => $p, $p => $filters);
			}
		} else 
			$dataF[] = array($p => $_POST['filters'][$p]);
	}
	
	$select = $propNames;
	$select[] = "imgurl";
	$tableNames = array ("products_".$_POST['id']);
	$result = simpleSelect($select, $tableNames, NULL, NULL, $whereFilters, NULL, NULL, $conn);
	if ($result->num_rows == 0)
		$statusMessage = makeStatusMessage(25, "error", "Nothing to select.");
	else {
		$dataP = array();
		while ($row = $result->fetch_assoc())
			$dataP[] = $row;
		$statusMessage = makeStatusMessage(15,"success","Data sent succesfully.");
		$data = array("filters" => $dataF, "products" => $dataP);	
	}
	mysqli_close($conn);
	return;
?>