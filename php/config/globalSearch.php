<?php
	if (!isset($_POST["searchValue"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	if (isset($_POST['categoryid'])) {
	
		$select = array("id");
		$tableNames = array ("categories");
		$where = "id = '".$conn->real_escape_string($_POST['categoryid'])."'";
		$result = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, NULL, $conn);
		
		if ($result->num_rows == 0) {
			$statusMessage = makeStatusMessage(51,"error");
			mysqli_close($conn);
			return;
		} else {
			$tmp = $result->fetch_assoc();
			$catid = $tmp['id'];
		}
	}
	
	$select = array("props.name as n");
	$tableNames = array ("props_to_prods as ids", "properties as props");
	$where = "ids.catid = '".$conn->real_escape_string($_POST['id'])."' AND props.searchable = '1'";
	$joinTypes = array ("JOIN");
	$joins = array ("ids.propid = props.id");
	$result = simpleSelect($select, $tableNames, $joinTypes, $joins, $where, NULL, NULL, $conn);
	while ($row = $result->fetch_assoc())
		$propNames[] = $row['n'];
	
	$tableNames = array ("products_".$conn->real_escape_string($_POST['id']));

	$whereFilters = "";
	if (isset($_POST['filters'])) {
		foreach ($_POST['filters'] as $key => $filter) {
			if (!empty($filter)) {
				$whereFilters .=  $conn->real_escape_string($key)."='".$conn->real_escape_string($filter)."' AND ";
			}
		}
		$whereFilters = substr($whereFilters, 0, -5);
	}
	if (isset($_POST['searchFilter'])) {
		if (!empty($whereFilters))
			$whereFilters .= " AND ";
		$whereFilters .= "(";
		foreach ($propNames as $p) {
			$whereFilters .= $p ." LIKE '%".$conn->real_escape_string($_POST['searchFilter'])."%' OR ";
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
			$dataF[] = array($p => $conn->real_escape_string($_POST['filters'][$p]));
	}
	
	$select = $propNames;
	$select[] = "imgurl";
	$tableNames = array ("products_".$conn->real_escape_string($_POST['id']));
	$result = simpleSelect($select, $tableNames, NULL, NULL, $whereFilters, NULL, NULL, $conn);
	if ($result->num_rows == 0)
		$statusMessage = makeStatusMessage(59, "error");
	else {
		$dataP = array();
		while ($row = $result->fetch_assoc())
			$dataP[] = $row;
		$statusMessage = makeStatusMessage(29,"success");
		$data = array("filters" => $dataF, "products" => $dataP);	
	}
	mysqli_close($conn);
	return;
?>