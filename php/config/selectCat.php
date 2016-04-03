<?php
	if (!isset($_POST["id"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$catid = $conn->real_escape_string($_POST['id']);
	
	$user = getUser($conn);

	if (empty($user))
		$log = createLog("","",$catid);
	else
		$log = createLog("","",$catid,"",$user['id']);
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("id");
	$selQ->tableNames = array ("categories");
	$selQ->where = "id = '".$catid."' AND visible = 1";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0) {
		$statusMessage = makeStatusMessage(51,"error");
		mysqli_close($conn);
		return;
	}

	$selQ = new selectSQL($conn);
	$selQ->select = array("props.name as n","props.name".$language." as lang","props.langDependant as ld");;
	$selQ->tableNames = array ("props_to_prods as ids", "properties as props");
	$selQ->where = "ids.catid = '".$catid."' AND props.searchable = '1'";
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
		if ($row['ld'])
			$propNames[] = $row['n'].$language;
		else
		     $propNames[] = $row['n'];
		$propLangName[] = $row['lang'];
	}	

	$whereFilters = "visible = 1 AND ";
	if (isset($_POST['filters'])) 
		for ($i = 0; $i < count($propLangName); $i++)
			if (!empty($_POST['filters'][$propLangName[$i]]))
				$whereFilters .=  $propNames[$i]."='".$conn->real_escape_string($_POST['filters'][$propLangName[$i]])."' AND ";
		
	if (isset($_POST['searchFilter'])) {
		$whereFilters .= "(";
		$searchFilter = $conn->real_escape_string($_POST['searchFilter']);
		foreach ($propNames as $p)
			$whereFilters .= "`".$p ."` LIKE '%".$searchFilter."%' OR ";
		$whereFilters = substr($whereFilters, 0, -4);
		$whereFilters .= ")";
	} else 
		$whereFilters = substr($whereFilters, 0, -5);

	$selQ = new selectSQL($conn);
	$selQ->tableNames = array ("products as p");
	$selQ->joins = array();
	$selQ->joinTypes = array();
	if (checkTable($conn, "products_".$catid)) {
		$selQ->tableNames[] = "products_".$catid." as nld";
		$selQ->joins[] = "p.id = nld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	if (checkTable($conn, "products_".$catid."_".$language)) {
		$selQ->tableNames[] = "products_".$catid."_".$language." as ld";
		$selQ->joins[] = "p.id = ld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	$selQ->where = $whereFilters;
	
	$dataF = array();
	for ($i=0;$i<count($propNames);$i++)  {
		if (!isset($_POST['filters'][$propNames[$i]])) {
			$selQ->distinct = true;
			$selQ->select = array($propNames[$i]." as `".$propLangName[$i]."`");
			if (!$selQ->executeQuery()) {
				$statusMessage = $selQ->status;
				mysqli_close($conn);
				return;
			}
			if ($selQ->executeQuery()) {
				$filters = array();
				while ($row = $selQ->result->fetch_assoc())
					$filters[] = $row[$propLangName[$i]];
				$dataF[] = array("name" => $propLangName[$i], $propLangName[$i] => $filters);
			}
		} else 
			$dataF[] = array("name" => $propNames[$i], $propNames[$i] => array($conn->real_escape_string($_POST['filters'][$propNames[$i]])));
	}
	
	include_once 'variables/productVariables.php';
	
	$selQ->distinct = false;
	$selQ->select = array("imgurl as ".$propNamesLang["imgurl"][$language]);
    $selQ->select[] = "p.id as id";
    foreach ($propNamesLang as $key => $p)
    	if ($key != "imgurl" AND !empty($p)) 
	    	$selQ->select = array_merge($selQ->select,array($key." as ".$p[$language]));
	$selQ->select[] = "price as ".$priceProductLang[$language];
	for ($i=0;$i<count($propNames);$i++) {
		// if ($propNames['price']) ADD PRICE SHOWING AND DISCOUNTS 
	
		$selQ->select = array_merge($selQ->select,array($propNames[$i]." as `".$propLangName[$i]."`"));
	}
	$selQ->tableNames = array ("products as p");
	$selQ->joins = array();
	$selQ->joinTypes = array();
	if (checkTable($conn, "products_".$catid)) {
		$selQ->tableNames[] = "products_".$catid." as nld";
		$selQ->joins[] = "p.id = nld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}
	if (checkTable($conn, "products_".$catid."_".$language)) {
		$selQ->tableNames[] = "products_".$catid."_".$language." as ld";
		$selQ->joins[] = "p.id = ld.infoid";
		$selQ->joinTypes[] = "INNER JOIN";
	}	
	

	if (isset($user['id'])) {
		$selQ->select[] = "productid as ".$favoriteProductLang[$language];
		$selQ->tableNames[] = "favorites as f";
		$selQ->joins[] = "p.id = f.id AND userid = '".$user['id']."'";
		$selQ->joinTypes[] = "LEFT JOIN";
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
		$statusMessage = makeStatusMessage(59, "error");
	else {
		$dataP = array();
		while ($row = $selQ->result->fetch_assoc()) {
			$idtmp = $row['id'];
			unset($row['id']);
			$dataP[$idtmp] = $row;
		}
		$statusMessage = makeStatusMessage(22,"success");
		$data = array("filters" => $dataF, "products" => $dataP);	
	}
	mysqli_close($conn);
	return;
?>
