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
	
	$id = $conn->real_escape_string($_POST['id']);
	
	$user = getUser($conn);
	
	$selQ = new selectSQL($conn);
	$selQ->select = array("catid");
	$selQ->tableNames = array ("products");
	$selQ->where = "id = '".$id."'";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	
	if ($selQ->getNumberOfResults() == 0) {
		$statusMessage = makeStatusMessage(52,"error");
		mysqli_close($conn);
		return;
	}
	
	$tmp = $selQ->result->fetch_assoc();
	$catid = $tmp['catid'];
	
	unset($selQ);
	$selQ = new selectSQL($conn);
	$selQ->select = array("props.name as n","props.name".$language." as lang","props.langDependant as ld");;
	$selQ->tableNames = array ("props_to_prods as ids", "properties as props");
	$selQ->where = "ids.catid = '".$catid."'";
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

	include_once 'variables/productVariables.php';
	
	unset($selQ);
	$selQ = new selectSQL($conn);
	$selQ->select = array("imgurl as ".$propNamesLang["imgurl"][$language]);
	foreach ($propNamesLang as $key => $p)
    	if ($key != "imgurl" AND !empty($p))
	    	$selQ->select = array_merge($selQ->select,array($key." as ".$p[$language]));
	$selQ->where = "p.id = '".$id."'";
	$cleanProps = array();
	for ($i=0;$i<count($propNames);$i++)
		$selQ->select = array_merge($selQ->select,array($propNames[$i]." as `".$propLangName[$i]."`"));
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

	if (!$selQ->executeQuery()){
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() == 0)
		$statusMessage = makeStatusMessage(59, "error");
	else {
		$data = array();
		while ($row = $selQ->result->fetch_assoc())
			$data[] = $row;
		$statusMessage = makeStatusMessage(22,"success");
	}
	mysqli_close($conn);

?>