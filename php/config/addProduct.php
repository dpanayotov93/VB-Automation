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
	if (isset($_POST['show'])) 
		getProds($conn);
	elseif (isset($_POST['id'])) {
		if (isset($_POST['delete']))
			delProd($conn,1);
		elseif (isset($_POST['restore']))
			delProd($conn,0);
		else
			updProd($conn);
	} elseif (isset($_POST['names']) && isset($_POST['desc']) && isset($_POST['catid']))
		insProd($conn);
	elseif (isset($_POST['catid']))
		getProdFields($conn);
	else
		getProds($conn);
		
	mysqli_close($conn);
	return;

	function delProd($conn,$del) {		
		$updQ = new updateSQL($conn);
		$updQ->tableName = "products";
		$updQ->where = "id = ".$conn->real_escape_string($_POST['id']);
		if ($del)
			$updQ->update = "visible = 0";
		else
			$updQ->update = "visible = 1";
		
		if (!$insQ->executeQuery())
			$GLOBALS['statusMessage'] = $insQ->status;
		else 
			$GLOBALS['statusMessage'] = makeStatusMessage(123123, "success", "Product updated.");
	}

	function updProd($conn) {
		$catid = $conn->real_escape_string($_POST['catid']);
		$arr = getPropsForCat($conn,$catid);
		if (!$arr)
			return;
		
		$propNamesDef = $arr['propNamesDef'];
		$propNamesDefld =  $arr['propNamesDefld'];
		$propNames = $arr['propNames'];
		$propNamesld = $arr['propNamesld'];
		
		$updQ = new updateSQL($conn);
		$updQ->tableName = "products";
		$updQ->where = "id = ".$conn->real_escape_string($_POST['id']);
		$updQ->update = "";
		foreach ($propNamesDef as $pn)
			if (isset($_POST[$pn])) 
				$updQ->update[] = $pn." = '".$conn->real_escape_string($_POST[$pn])."' AND ";
		foreach ($propNamesDefld as $pn)
			while ($l = $GLOBALS['langResult'])
				if (isset($_POST[$pn][$l['abreviation']])) 
					$updQ->update[] = $pn.$l['abreviation']." = '".$conn->real_escape_string($_POST[$pn][$l['abreviation']])."' AND ";

		$updQ->update = substr($updQ->update, 0, -5);
		
		if (!$updQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $updQ->status;
			return;
		}
		
		$selQProp = new selectSQL($conn);
		$selQProp->tableNames = array("products");
		$selQProp->select = array("id");
		$selQProp->order = "id DESC";
		if(!$selQProp->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQProp->status;
			return;
		}
		$tmp = $selQProp->result->fetch_assoc();
		$infoID = $tmp['id'];
		
		$updQ2 = new updateSQL($conn);
		$updQ2->tableName = "products_".$catid;
		$updQ2->where = "id = ".$infoID;
		$updQ2->update = "";
		foreach ($propNames as $pn)
			if (isset($_POST[$pn]))
				$updQ2->update[] = $pn." = '".$conn->real_escape_string($_POST[$pn])."' AND ";
		
		$updQ2->update = substr($updQ2->update, 0, -5);

		if (!$updQ2Q->executeQuery()) {
			$GLOBALS['statusMessage'] = $updQ2->status;
			return;
		}

		while ($l = $GLOBALS['langResult']) {
			$updQ3 = new updateSQL($conn);
			$updQ3->tableName = "products_".$catid."_".$l['abreviation'];
			$updQ3->where = "infoid = ".$infoID;
			$updQ3->update = "";
			foreach ($propNames as $pn)
				if (isset($_POST[$pn]))
					$updQ3->update[] = $pn." = '".$conn->real_escape_string($_POST[$pn])."' AND ";
			
			$updQ3->update = substr($updQ3->update, 0, -5);
	
			if (!$updQ3Q->executeQuery()) {
				$GLOBALS['statusMessage'] = $updQ3->status;
				return;
			}
		}
		
		$GLOBALS['statusMessage'] = makeStatusMessage(123123, "success", "Product updated.");
	}

	function insProd($conn) {
		$catid = $conn->real_escape_string($_POST['catid']);
		
		$arr = getPropsForCat($conn,$catid);
		if (!$arr)
			return;
		
		$propNamesDef = $arr['propNamesDef'];
		$propNamesDefld =  $arr['propNamesDefld'];
		$propNames = $arr['propNames'];
		$propNamesld = $arr['propNamesld'];
			
		$insQ = new insertSQL($conn);
		$insQ->tableName = "products_".$catid;
		$insQ->cols = array();
		$insQ->insertData = array();
		foreach ($propNames as $pn)
			if (isset($_POST[$pn])) {
				$insQ->cols[] = $pn;
				$insQ->insertData[] = $conn->real_escape_string($_POST[$pn]);
			}
		
		if (!$insQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $insQ->status;
			return;
		}	
	
		$selQProp = new selectSQL($conn);
		$selQProp->tableNames = array("products");
		$selQProp->select = array("id");
		$selQProp->order = "id DESC";
		if(!$selQProp->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQProp->status;
			return;
		}
		$tmp = $selQProp->result->fetch_assoc();
		$infoID = $tmp['id'];
			
		while($l = $GLOBALS['langResult']) {
			$insQld = new insertSQL($conn);
			$insQld->tableName = "products_".$catid."_".$l['abreviation'];
			$insQld->cols = array("infoid");
			$insQld->insertData = array($infoID);
			foreach ($propNames as $pn)
				if (isset($_POST[$pn])) {
					$insQld->cols[] = $pn;
					$insQld->insertData[] = $conn->real_escape_string($_POST[$pn]);
				}
			if (!$insQld->executeQuery()) {
				$GLOBALS['statusMessage'] = $insQld->status;
				return;
			}
		}
		
		$insQdef = new insertSQL($conn);
		$insQdef->tableName = "products";
		$insQdef->cols = array("infoid","dateadded");
		$insQdef->insertData = array($infoID,time());
		foreach ($propNamesDef as $pn)
			if (isset($_POST[$pn])) {
				$insQdef->cols[] = $pn;
				$insQdef->insertData[] = $conn->real_escape_string($_POST[$pn]);
			}
		
		foreach ($propNamesDefld as $pn)
			while ($l = $GLOBALS['langResult']->fetch_assoc())
				if (isset($_POST[$pn][$l['abreviation']])) {
					$insQdef->cols[] = $pn.$l['abreviation'];
					$insQdef->insertData[] = $conn->real_escape_string($_POST[$pn][$l['abreviation']]);
				}
			if (!$insQdef->executeQuery()) {
				$GLOBALS['statusMessage'] = $insQdef->status;
				return;
			}
		
		$GLOBALS['statusMessage'] = makeStatusMessage(234, "success", "Product added succesfully.");
	}

	function getProdFields($conn) {
		$selQ = new selectSQL($conn);
		$selQ->select = array("propid");
		$selQ->where = "catid = ".$conn->real_escape_string($_POST['catid']);
		if (isset($_POST['deleted']))
			$selQ->where .= " AND visible != 1";
		else
			$selQ->where .= " AND visible = 1";
		$selQ->tableNames = array("props_to_prods");
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(34, "error", "No properties for this category.");
			return;
		}
		
		$props = "";
		while($r = $selQ->result->fetch_assoc()) 
			$props .= $r['propid'].",";
		$props = substr($props,0,-1);
		
		$selQ = new selectSQL($conn);
		$selQ->select = array("name,langDependant");
		$selQ->tableNames = array("properties");
		$selQ->where = "id IN (".$props.")";
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(34, "error", "No properties to select.");
			return;
		}
		$def = array();
		while ($l = $GLOBALS['langResult']->fetch_assoc())
			$def = array_merge($def, array("Name ".$l['abreviation'] => "names".$l['abreviation']));

		$def = array_merge($def, array("Category" => "catid","Price" => "price","Quantity" => "qty"));		
		
		$ld = array();
		$lid = array();
		while($r = $selQ->result->fetch_assoc())
			if ($r['langDependant'])
				while ($l = $GLOBALS['langResult']->fetch_assoc())
					$ld = array_merge($ld, array($r["name"]." (".$l['abreviation'].")" => $r['name']."[".$l['abreviation']."]"));
			else 
				$lid[] = $r['name'];
		
		$GLOBALS['data'] = array_merge($def,array_merge($ld,$lid));
		$GLOBALS['statusMessage'] = makeStatusMessage(2142, "success", "Properties sent successfully.");
	}

	function getProds($conn) {
		$catid = array();
		if (empty($_POST['catid'])) {
			$selQ = new selectSQL($conn);
			$selQ->select = array("id");
			$selQ->tableNames = array("categories");
			$selQ->where = "visible = 1";
			if (!$selQ->executeQuery()) {
				$GLOBALS['statusMessage'] = $selQ->status;
				return;
			}
			if ($selQ->getNumberOfResults() == 0) {
				$GLOBALS['statusMessage'] = makeStatusMessage(123, "error", "No categories to select.");
				return;
			}
			while ($r = $selQ->result->fetch_assoc())
				$catid[] = $r['id'];
		} else
			$catid[] = $conn->real_escape_string($_POST['catid']);
		
		$selQ = new selectSQL($conn);
		$selQ->select = array("*");
		if (isset($_POST['id'])) {
			$selQ->where = "id = ".$conn->real_escape_string($_POST['id']);
		} else {
			$w = "(";
			foreach ($catid as $i)
				$w .= "p.catid = '".$i."' OR ";
			$selQ->where = substr($w, 0, -3).")";
			if (isset($_POST['deleted']))
				$selQ->where .= " AND p.visible != 1";
			else
				$selQ->where .= " AND p.visible = 1";
		}
		$selQ->tableNames = array("products as p");
		$selQ->joins = array();
		$selQ->joinTypes = array();
		foreach ($catid as $i) {
			$selQ->tableNames[] = "products_".$i;
			$selQ->joins[] = "p.infoid = products_".$i.".id";
			$selQ->joinTypes[] = "JOIN";
			while ($r = $GLOBALS['langResult']->fetch_assoc()) {
				$selQ->tableNames[] = "products_".$i."_".$r['abreviation'];
				$selQ->joins[] = "p.infoid = products_".$i."_".$r['abreviation'].".infoid";
				$selQ->joinTypes[] = "JOIN";
			}
		}
		if(!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			mysqli_close($conn);
			return;
		}
		$data;
		while ($r=$selQ->result->fetch_assoc()) 
			$data[] = $r;
		
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = makeStatusMessage(234, "success", "Products printed successfully.");
		
	}

	function getPropsForCat($conn,$catid) {
		include 'categories.php';
		if(empty($data))
			return null;
	
		$propNamesDef = array("catid","price","qty");
		$propNamesDefld = array("name,desc");
		$propNames = array();
		$propNamesld = array();

		$selQ = new selectSQL($conn);
		$selQ->tableNames = array("ptp.prop_to_prod","p.properties");
		$selQ->joins = "p.id = ptp.propid";
		$selQ->joinTypes = "INNER JOIN";
		$selQ->select = array("p.name as propName, p.langDependant as ld");
		$selQ->where = "ptp.catid = '".$conn->real_escape_string($_POST['catid'])."'";
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return null;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(234, "error", "No properties to select");
			return null;
		} else
			while ($r = $selQ->result->fetch_assoc())
				if ($r['ld'])
					$propNamesld[] = $r['propName'];
				else
					$propNames[] = $r['propName'];

		return array("propNamesDefld" => $propNamesDefld,"propNamesDef" => $propNamesDef,"propNamesld" => $propNamesld,"propNames" => $propNames);
								
	}
	
?>