<?php
	if (!isset($_POST["id"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$id = $conn->real_escape_string($_POST['id']);
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("id");
	$selQ->tableNames = array ("categories");
	$selQ->where = "id = '".$id."' AND visible = 1";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0) {
		$statusMessage = makeStatusMessage(24,"error","Category not found!");
		mysqli_close($conn);
		return;
	}

	$selQ = new selectSQL($conn);
	$selQ->select = array("props.name as n","props.name".$language." as lang");
	$selQ->tableNames = array ("props_to_prods as ids", "properties as props");
	$selQ->where = "ids.catid = '".$id."' AND props.searchable = '1'";
	$selQ->joinTypes = array ("JOIN");
	$selQ->joins = array ("ids.propid = props.id");
	
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($coon);
		return;
	}
	$propNames = array();
	$propLangName = array();
	while ($row = $selQ->result->fetch_assoc()) {
		$propNames[] = $row['n'];
		$propLangName[] = $row['lang'];
	}	

	$whereFilters = "visible = 1 AND ";
	if (isset($_POST['filters'])) 
		foreach ($_POST['filters'] as $key => $filter) {
			$key = $conn->real_escape_string($key);
			$filter = $conn->real_escape_string($filter);
			if (!empty($filter)) {
				$whereFilters .=  $key."='".$filter."' AND ";
			}
		}
	if (isset($_POST['searchFilter'])) {
		$whereFilters .= "(";
		$searchFilter = $conn->real_escape_string($_POST['searchFilter']);
		foreach ($propNames as $p)
			$whereFilters .= $p ." LIKE '%".$searchFilter."%' OR ";
		$whereFilters = substr($whereFilters, 0, -4);
		$whereFilters .= ")";
	} else 
		$whereFilters = substr($whereFilters, 0, -5);

	$selQ = new selectSQL($conn);
	$selQ->tableNames = array ("products as p");
	$selQ->joins = array();
	$selQ->joinTypes = array();
	if (checkTable($conn, "products_".$id)) {
		$selQ->tableNames[] = "products_".$id." as nld";
		$selQ->joins[] = "p.id = nld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	if (checkTable($conn, "products_".$id."_".$language)) {
		$selQ->tableNames[] = "products_".$id."_".$language." as ld";
		$selQ->joins[] = "p.id = ld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	$selQ->where = $whereFilters;
	
	$dataF = array();
	foreach ($propNames as $p) {
		if (!isset($_POST['filters'][$p])) {
			$selQ->distinct = true;
			$selQ->select = array($p);
			if (!$selQ->executeQuery()) {
				$statusMessage = $selQ->status;
				mysqli_close($conn);
				return;
			}
			if ($selQ->executeQuery() != 0) {
				$filters = array();
				while ($row = $selQ->result->fetch_assoc())
					$filters[] = $row[$p];
				$dataF[] = array("name" => $p, $p => $filters);
			}
		} else 
			$dataF[] = array("name" => $p, $p => array($_POST['filters'][$p]));
	}

	$selQ->distinct = false;
	$selQ->select = array("imgurl as Image");
	$selQ->select[] = "names".$language;
	$selQ->select[] = "price";
	$cleanProps = array();
	for ($i=0;$i<count($propNames);$i++) 
		$selQ->select = array_merge($selQ->select,array($propNames[$i]." as ".$propLangName[$i]));
	$selQ->tableNames = array ("products as p");
	$selQ->joins = array();
	$selQ->joinTypes = array();
	if (checkTable($conn, "products_".$id)) {
		$selQ->tableNames[] = "products_".$id." as nld";
		$selQ->joins[] = "p.id = nld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	if (checkTable($conn, "products_".$id."_".$language)) {
		$selQ->tableNames[] = "products_".$id."_".$language." as ld";
		$selQ->joins[] = "p.id = ld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	if (isset($_POST['countPerPage']) && is_int($_POST['countPerPage']))
		if (isset($_POST['page']) && is_int($_POST['page']))
			$selQ->limit = ($_POST['countPerPage'] - 1)*$_POST['page'].",".$_POST['countPerPage']; 
		else 
			$selQ->limit = $conn->real_escape_string($_POST['countPerPage']); 
		
	if (!$selQ->executeQuery()){
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() == 0)
		$statusMessage = makeStatusMessage(25, "error", "Nothing to select.");
	else {
		$dataP = array();
		while ($row = $selQ->result->fetch_assoc())
			$dataP[] = $row;
		$statusMessage = makeStatusMessage(15,"success","Data sent succesfully.");
		$data = array("filters" => $dataF, "products" => $dataP);	
	}
	mysqli_close($conn);
	return;
?>