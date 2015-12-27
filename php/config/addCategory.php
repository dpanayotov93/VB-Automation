<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$langResult = getLanguages($conn);
	if (is_null($langResult)) {
		$statusMessage = makeStatusMessage(324, "error", "Could not get language information.");
		mysqli_close($conn);
		return;
	}
	
	if (isset($_POST['id'])) {
		if (isset($_POST['delete']))
			delCat($conn,1);
		elseif (isset($_POST['restore']))
			delCat($conn,0);
		else
			updCat($conn);
	} elseif (isset($_POST['names']) && isset($_POST['desc']))
		insCat($conn);
	elseif (isset($_POST['showCats'])) 
		getCats();
	else 
		getCatFields($conn);
		
	mysqli_close($conn);
	return;
		
	function delCat($conn,$del) {
		$updQ = new updateSQL($conn);
		$updQ->tableName = "categories";
		$updQ->where = "id = '".$conn->real_escape_string($_POST['id'])."'";
		if ($del)
			$updQ->update = "visible = 0";
		else 
			$updQ->update = "visible = 1";

		if (!$updQ->executeQuery())
			$statusMessage = $updQ->sqlQuery;
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Category deleted successfully.");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function updCat($conn) {
		$id = $conn->real_escape_string($_POST['id']);
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$updQ->tableName = "categories";
		$updQ->where = "id = '".$id."'";
		while ($row = $GLOBALS['langResult']->fetch_assoc()) {
			if (isset($_POST['names'][$row['abreviation']])) 
				$updQ->update .= "name".$row['abreviation']." = '".$conn->real_escape_string($_POST['names'][$row['abreviation']]."',");
			if (isset($_POST['desc'][$row['abreviation']])) 
				$updQ->update .= "desc".$row['abreviation']." = '".$conn->real_escape_string($_POST['desc'][$row['abreviation']])."',";
		}
		if (isset($_POST['parentid']))
			$updQ->update .= "parentid = '".$conn->real_escape_string($_POST['parentid'])."',";
		if (isset($_POST['imgurl']))
			$updQ->update .= "imgurl = '".$conn->real_escape_string($_POST['imgurl'])."'";
		else
			$updQ->update = substr($updQ->update, 0, -1);
	
		if (!$updQ->executeQuery())
			$statusMessage = $updQ->status;
		else
			$statusMessage = makeStatusMessage(1234, "suscces", "Category updated successfully.");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getCatFields($conn) {
		$data = array("Parent id" => "parentid");
		while($row = $GLOBALS['langResult']->fetch_assoc()) {
			$data = array_merge($data,array("Name ".$row["abreviation"] => "names".$row["abreviation"]));
			$data = array_merge($data,array("Discription ".$row["abreviation"] => "desc".$row["abreviation"]));
		}
		$data = array_merge($data,array("Link to image" => "imgurl"));
		$statusMessage = makeStatusMessage(12342, "success", "Info sent!");
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getCats() {
		include_once 'categories.php';
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function insCat($conn) {
		$insQ = new insertSQL($conn);
		$insQ->insertData = array();
		$insQ->cols = array();
		while ($row = $GLOBALS(langResult)->fetch_assoc()) {
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
		
		$insQ->tableName = "categories";
		
		if (!$insQ->executeQuery())
			$statusMessage = $insQ->status;
		else {
			$selQid = new selectSQL($conn);
			$selQid->where = "";
			while ($row = $GLOBALS(langResult)->fetch_assoc()) 
				if (isset($_POST['names'][$row['abreviation']]))
					$selQid->where = "name".$row['abreviation']." = '".$conn->real_escape_string($_POST['names'][$row['abreviation']])."' OR ";
				
			$selQid->where = substr($selQid->where, 0, -4);
			$selQid->order = "id DESC";
			$selQid->tableNames = array("categories");
			$selQid->select = array("id");
			
			if (!$selQid->executeQuery())
				$statusMessage = $selQid->status;
			else {
				$row = $selQid->result->fetch_assoc();
				$catid = $row['id'];
			
				$selQ = new selectSQL($conn);
				$selQ->select = array ("name,langDependant");
				$selQ->tableNames = array ("properties");
				$selQ->where = "";
				foreach ($_POST['fid'] as $f) {
					$selQ->where .= "id = '".$f."' OR ";
				}
				$selQ->where = substr($selQ->where, 0, -4);
				
				if (!$selQ->executeQuery() OR $selQ->getNumberOfResults() == 0)
					$statusMessage = makeStatusMessage(234, "error", "Error getting category properties.");
				else {
					$propsDef = array();
					$propsLang = array();
					while ($row = $selQ->result->fetch_assoc()) {
						if($row['langDependant'])
							$propsDef[] = $row['name'];
						else
							$propsLang[] = $row['name'];
					}
					$ctQ = new createTableSQL($conn);
					
					$ctQ->cols = array();
					$ctQ->colTypes = array();
					$ctQ->name = "products_".$catid;
					foreach ($propsDef as $pr) {
						$$ctQ->cols[] = $pr;
						$ctQ->colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
					}
					
					if (!$ctQ->executeQuery()) 
						$statusMessage = $ctQ->status;
					else {
						while ($row = $GLOBALS(langResult)->fetch_assoc()) {
							$ctQ->cols = array();
							$ctQ->colTypes = array();
							$ctQ->name = "products_".$catid."_".$row['abreviation'];
							foreach ($propsLang as $pr) {
								$$ctQ->cols[] = $pr;
								$ctQ->colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
							}
							
							if (!$ctQ->executeQuery()) {
								$GLOBALS['statusMessage'] = $ctQ->status;
								mysqli_close($conn);
								return;
							}
						}
						
						$insQ = new insertSQL($conn);
						$insQ->cols = array ("catid", "propid");
						$insQ->tableName = "props_to_prods";
						foreach ($_POST['fid'] as $f) {
							$insQ->insertData = array($catid,$f);
							if (!$insQ->executeQuery())
								$resultAddProps = true; 
						}
						
						if (isset($resultAddProps))
							$statusMessage = makeStatusMessage(3,"error","Could not assign properties to category.");
						else
							$statusMessage = makeStatusMessage(21,"success","Category successfully added!");
					}
				}
			}
		}
		$GLOBALS['statusMessage'] = $statusMessage;
	}
?>