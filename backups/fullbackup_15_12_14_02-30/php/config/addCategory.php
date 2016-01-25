<?php
	if (!isset($_POST["names"]) || !isset($_POST["fid"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$result = getLanguages($conn);
	if (is_null($result)) {
		$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		mysqli_close($conn);
		return;
	}

	$insertData = array();
	$cols = array();
	while ($row = $result->fetch_assoc()) {
		if (isset($_POST['names'][$row['abreviation']])) {
			$insertData[] = $_POST['names'][$row['abreviation']];
			$cols[] = "name".$row['abreviation'];
		}
		if (isset($_POST['desc'][$row['abreviation']])) {
			$insertData[] = $_POST['desc'][$row['abreviation']];
			$cols[] = "desc".$row['abreviation'];
		}
	}
	if (isset($_POST['imgurl'])) {
		$insertData[] = $_POST['imgurl'];
		$cols[] = "imgurl";
	}
	if (isset($_POST['parent'])) {
		$insertData[] = $_POST['parent'];
		$cols[] = "parentid";
	}
	$tableNames = array ("categories");
	$resultAddCat = simpleInsert($insertData, $tableNames[0], $cols, $conn);
	if ($resultAddCat) {
		$statusMessage = makeStatusMessage(24, "error", "Could not create category.");
		mysqli_close($conn);
		return;
	}
	if(!isset($_POST['names']['EN']))
		$_POST['names']['EN'] = "";
	if(!isset($_POST['names']['BG']))
		$_POST['names']['BG'] = "";
	$where = "nameEN = '".$_POST['names']['EN']."' OR nameBG = '".$_POST['names']['BG']."'";
	$order = "id DESC";
	$tableNames = array("categories");
	$select = array("id");
	$result = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, $order, $conn);

	if (!$result) {
		$statusMessage = makeStatusMessage(351, "error", "Could not get category.");
		mysqli_close($conn);
		return;
    }
    
	$row = $result->fetch_assoc();
	$catid = $row['id'];

	$select = array ("name");
	$tableNames = array ("properties");
	$where = "";
	foreach ($_POST['fid'] as $f) {
		$where .= "id = '".$f."' OR ";
	}
	$where = substr($where, 0, -4);
	
	$result = simpleSelect($select, $tableNames, NULL, NULL, $where, NULL, NULL, $conn);
	if (empty($result->num_rows)) {
		$statusMessage = makeStatusMessage(234, "error", "Error getting category properties.");
		mysqli_close($conn);
		return;
	}
	
	$cols = array();
	$colTypes = array();
	$name = "products_". $catid;
	while ($row = $result->fetch_assoc()) {
		$cols[] = $row['name'];
		$colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
	}
	$resultCrT = createTable($name, $cols, $colTypes, $conn);
	
	if (!$resultCrT) {
		$statusMessage = makeStatusMessage(234, "error", "Error creating product table for this category.");
		mysqli_close($conn);
		return;
	}
	
	$cols = array ("catid", "propid");
	$tableNames = array ("props_to_prods");
	foreach ($_POST['fid'] as $f) {
		$insertData = array($catid,$f);
		$resultAddProps = simpleInsert($insertData, $tableNames[0], $cols, $conn);
	}
	
	if (!$resultAddProps) {
		$statusMessage = makeStatusMessage(3,"error","Could not assign properties to category.");
		mysqli_close($conn);
		return;
	}
	$statusMessage = makeStatusMessage(21,"success","Category successfully added!");
	
	mysqli_close($conn);
	return;
?>