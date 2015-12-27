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
		$GLOBALS[$statusMessage] = makeStatusMessage(342, "sadERROR", "Not workin' yet.");
	}

	function updProd($conn) {
		$GLOBALS[$statusMessage] = makeStatusMessage(342, "sadERROR", "Not workin' yet.");
	}

	function insProd($conn) {
		$GLOBALS[$statusMessage] = makeStatusMessage(342, "sadERROR", "Not workin' yet.");
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
		$w = "(";
		foreach ($catid as $i)
			$w .= "p.catid = '".$i."' OR ";
		$selQ->where = substr($w, 0, -3).")";
		if (isset($_POST['deleted']))
			$selQ->where .= " AND p.visible != 1";
		else
			$selQ->where .= " AND p.visible = 1";
		$selQ->tableNames = array("products as p");
		$selQ->joins = array();
		$selQ->joinTypes = array();
		foreach ($catid as $i) {
			$selQ->tableNames[] = "products_".$i;
			$selQ->joins[] = "p.infoid = products_".$i.".id";
			$selQ->joinTypes[] = "JOIN";
			while ($r = $GLOBALS['langResult']->fetch_assoc()) {
				$selQ->tableNames[] = "products_".$i."_".$r['abreviation'];
				$selQ->joins[] = "p.infoid = products_".$i."_".$r['abreviation'].".id";
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
	
?>