<?php
	if (!isset($_POST["names"]) || !isset($_POST["fid"])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}

	$result = getLanguages($conn);
	if (is_null($result)) {
		$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		mysqli_close($conn);
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	} elseif (isset($_POST['id'])) {
		if (isset($_POST['delete']))
			delCat($conn,1);
		elseif (isset($_POST['restore']))
			delCat($conn,0);
		else
			updCat($conn);
	} elseif (isset($_POST['name']) && isset($_POST['names']) && isset($_POST['desc']))
		insCat($conn);
	elseif (isset($_POST['showProps'])) 
		getCats($conn);
	else 
		getCatFields($conn);
		

	mysqli_close($conn);
	return;
		

	function delCat($conn) {
		$statusMessage = makeStatusMessage(-1, "shiiit", "Not done yet.");
	}
	
	function updCat($conn) {
		$statusMessage = makeStatusMessage(-1, "shiiit", "Not done yet.");
	}
	
	function getCatFields($conn) {
		$statusMessage = makeStatusMessage(-1, "shiiit", "Not done yet.");
	}
	
	function getCats($conn) {
		include_once 'categories.php';
	}
	
	function insCat($conn) {
		$insQ = new insertSQL($conn);
		$insQ->insertData = array();
		$insQ->cols = array();
		while ($row = $result->fetch_assoc()) {
			if (isset($_POST['names'][$row['abreviation']])) {
				$insQ->insertData[] = $_POST['names'][$row['abreviation']];
				$insQ->cols[] = "name".$row['abreviation'];
			}
			if (isset($_POST['desc'][$row['abreviation']])) {
				$insQ->insertData[] = $_POST['desc'][$row['abreviation']];
				$insQ->cols[] = "desc".$row['abreviation'];
			}
		}
		if (isset($_POST['imgurl'])) {
			$insQ->insertData[] = $_POST['imgurl'];
			$insQ->cols[] = "imgurl";
		}
		if (isset($_POST['parent'])) {
			$insQ->insertData[] = $_POST['parent'];
			$insQ->cols[] = "parentid";
		}
		
		$insQ->tableNames = array ("categories");
		
		if (!$insQ->executeQuery()) {
			$statusMessage = $insQ->status;
			mysqli_close($conn);
			return;
		}
		
		$selQ = new selectSQL($conn);
		$selQ->where = "";
		while ($row = $result->fetch_assoc()) 
			if (isset($_POST['names'][$row['abreviation']]))
				$selQ->where = "name".$row['abreviation']." = '".$conn->real_escape_string($_POST['names'][$row['abreviation']])."' OR ";
			
		$selQ->where = substr($selQ->where, 0, -4);
		$selQ->order = "id DESC";
		$selQ->tableNames = array("categories");
		$selQ->select = array("id");
		
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		    
		$row = $selQ->result->fetch_assoc();
		$catid = $row['id'];
	
		$selQ = new selectSQL($conn);
		$selQ->select = array ("name");
		$selQ->tableNames = array ("properties");
		$selQ->where = "";
		foreach ($_POST['fid'] as $f) {
			$selQ->where .= "id = '".$f."' OR ";
		}
		$selQ->where = substr($selQ->where, 0, -4);
		
		if (!$selQ->executeQuery() OR $selQ->getNumberOfResults() == 0) {
			$statusMessage = makeStatusMessage(234, "error", "Error getting category properties.");
			mysqli_close($conn);
			return;
		}
		
		$ctQ = new createTableSQL($conn);
		
		$ctQ->cols = array();
		$ctQ->colTypes = array();
		$ctQ->name = "products_". $catid;
		while ($row = $ctQ->result->fetch_assoc()) {
			$$ctQ->cols[] = $row['name'];
			$ctQ->colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
		}
		
		if (!$ctQ->executeQuery()) {
			$statusMessage = $ctQ->status;
			mysqli_close($conn);
			return;
		}
		$insQ = new insertSQL($conn);
		$insQ->cols = array ("catid", "propid");
		$insQ->tableNames = array ("props_to_prods");
		foreach ($_POST['fid'] as $f) {
			$insQ->insertData = array($catid,$f);
			if (!$insQ->executeQuery())
				$resultAddProps = true; 
		}
		
		if (!$resultAddProps)
			$statusMessage = makeStatusMessage(3,"error","Could not assign properties to category.");
		else
			$statusMessage = makeStatusMessage(21,"success","Category successfully added!");
	}
?>