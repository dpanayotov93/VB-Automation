<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$user = getUser($conn);
	if ($user['access'] != 3) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}
	
	$log = createLog(1); // ADD ADMIN LOG
	
	if (isset($_POST['id'])) {
		if (isset($_POST['delete']))
			delCat($conn,1);
		elseif (isset($_POST['restore']))
			delCat($conn,0);
		else
			updCat($conn);
	} elseif (isset($_POST['names']))
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
			$statusMessage = makeStatusMessage(41, "suscces");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function updCat($conn) {
		
		require_once 'languageConfig.php';
		
		$id = $conn->real_escape_string($_POST['id']);
		$updQ = new updateSQL($conn);
		$updQ->update = "";
		$updQ->tableName = "categories";
		$updQ->where = "id = '".$id."'";
		foreach ($langArr as $l) {
			if (isset($_POST['names'][$l])) 
				$updQ->update .= "name".$l." = '".$conn->real_escape_string($_POST['names'][$l])."',";
			if (isset($_POST['desc'][$l])) 
				$updQ->update .= "desc".$l." = '".$conn->real_escape_string($_POST['desc'][$l])."',";
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
			$statusMessage = makeStatusMessage(41, "suscces");
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getCatFields($conn) {
		require_once 'languageConfig.php';
		$data = array("Parent id" => "parentid");
		foreach ($langArr as $l) {
			$data = array_merge($data,array("Name ".$l => "names[".$l."]"));
			$data = array_merge($data,array("Discription ".$l => "desc[".$l."]"));
		}
		$data = array_merge($data,array("Link to image" => "imgurl"));
		$statusMessage = makeStatusMessage(21, "success");
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function getCats() {
		$allLangs = 1;
		include_once 'categories.php';
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = $statusMessage;
	}
	
	function insCat($conn) {
		require_once 'languageConfig.php';
		
		$insQ = new insertSQL($conn);
		$insQ->insertData = array();
		$insQ->cols = array();
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
		if (isset($_POST['imgUrl'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['imgUrl']);
			$insQ->cols[] = "imgurl";
		}
		if (isset($_POST['parentid'])) {
			$insQ->insertData[] = $conn->real_escape_string($_POST['parentid']);
			$insQ->cols[] = "parentid";
		}
		
		$insQ->tableName = "categories";
		
		if (!$insQ->executeQuery())
			$statusMessage = $insQ->status;
		else {
			$selQid = new selectSQL($conn);
			$selQid->where = "";
			foreach ($langArr as $l)
				if (isset($_POST['names'][$l]))
					$selQid->where = "name".$l." = '".$conn->real_escape_string($_POST['names'][$l])."' OR ";
				
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
				$selQ->select = array ("name","langDependant");
				$selQ->tableNames = array ("properties");
				$tmp = array();
				foreach ($_POST['fid'] as $f) 
					$tmp[] = $conn->real_escape_string($f);
				$selQ->where = "id IN (".arrToQueryString($tmp,null).")";
				
				if (!$selQ->executeQuery() OR $selQ->getNumberOfResults() == 0)
					$statusMessage = makeStatusMessage(53, "error");
				else {
					$propsDef = array();
					$propsLang = array();
					while ($row = $selQ->result->fetch_assoc()) {
						if($row['langDependant'])
							$propsLang[] = $row['name'];
						else
							$propsDef[] = $row['name'];
					}
					$ctQ = new createTableSQL($conn);
					
					$ctQ->cols = array();
					$ctQ->cols[] = "infoid";
					$ctQ->colTypes = array();
					$ctQ->colTypes[] = "int(11) NOT NULL";
					$ctQ->name = "products_".$catid;
					
					if (count($propsDef)) {
						foreach ($propsDef as $pr) {
							$ctQ->cols[] = $pr;
							$ctQ->colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
						}
						
						if (!$ctQ->executeQuery()) { 
							$statusMessage = $ctQ->status;
							mysqli_close($conn);
					 		return;
						}
					}
					
					if (count($propsLang)) {
						foreach ($langArr as $l) {
							unset($ctQ->cols);
							$ctQ->cols[] = "infoid";
							unset($ctQ->colTypes);
							$ctQ->colTypes[] = "int(11) NOT NULL";
							$ctQ->name = "products_".$catid."_".$l;
							foreach ($propsLang as $pr) {
								$ctQ->cols[] = $pr.$l;
								$ctQ->colTypes[] = "varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL";
							}
							
							if (!$ctQ->executeQuery()) {
								$GLOBALS['statusMessage'] = $ctQ->status;
								mysqli_close($conn);
								return;
							}
						}
					}
					
					$insQ = new insertSQL($conn);
					$insQ->cols = array ("catid", "propid");
					$insQ->tableName = "props_to_prods";
					foreach ($_POST['fid'] as $f) {
						$insQ->insertData = array($catid,$conn->real_escape_string($f));
						if (!$insQ->executeQuery())
							$resultAddProps = true; 
					}
					
					if (isset($resultAddProps))
						$statusMessage = makeStatusMessage(103,"error");
					else
						$statusMessage = makeStatusMessage(11,"success");
					
				}
			}
		}
		$GLOBALS['statusMessage'] = $statusMessage;
	}
?>