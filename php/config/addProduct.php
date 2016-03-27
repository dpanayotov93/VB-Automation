<?php

	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$langResult = getLanguages($conn);
	if (is_null($langResult)) {
		$statusMessage = makeStatusMessage(2, "error");
		mysqli_close($conn);
		return;
	}
	
	$user = getUser($conn);
	if ($user['access'] != 3) {
		$statusMessage = makeStatusMessage(3,"error");
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
	} elseif (isset($_POST['names']) && isset($_POST['catid']))
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
		
		if (!$updQ->executeQuery())
			$GLOBALS['statusMessage'] = $updQ->status;
		else 
			$GLOBALS['statusMessage'] = makeStatusMessage(42, "success");
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
		
		$langArr = array();
		while ($l = $GLOBALS['langResult']) 
			$langArr[] = $l['abreviation'];
		
		$updQ = new updateSQL($conn);
		$updQ->tableName = "products";
		$updQ->where = "id = ".$conn->real_escape_string($_POST['id']);
		$updQ->update = "";
		foreach ($propNamesDef as $pn)
			if (isset($_POST[$pn])) 
				$updQ->update[] = $pn." = '".$conn->real_escape_string($_POST[$pn])."' AND ";
		foreach ($propNamesDefld as $pn)
			foreach ($langArr as $l)
				if (isset($_POST[$pn][$l])) 
					$updQ->update[] = $pn.$l." = '".$conn->real_escape_string($_POST[$pn][$l])."' AND ";

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
		
		if (count($propNames)) {
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
		}
		
		if (count($propNamesld)) {
			foreach ($langArr as $l) {
				$updQ3 = new updateSQL($conn);
				$updQ3->tableName = "products_".$catid."_".$l;
				$updQ3->where = "infoid = ".$infoID;
				$updQ3->update = "";
				foreach ($propNamesld as $pn)
					if (isset($_POST[$pn][$l]))
						$updQ3->update[] = $pn.$l." = '".$conn->real_escape_string($_POST[$pn][$l])."' AND ";
				
				$updQ3->update = substr($updQ3->update, 0, -5);
		
				if (!$updQ3Q->executeQuery()) {
					$GLOBALS['statusMessage'] = $updQ3->status;
					return;
				}
			}
		}
		
		$GLOBALS['statusMessage'] = makeStatusMessage(32, "success");
	}

	function insProd($conn) {
		$catid = $conn->real_escape_string($_POST['catid']);

		$langArr = array();
		while($l = $GLOBALS['langResult']->fetch_assoc())
			$langArr[] = $l['abreviation'];
		
		$arr = getPropsForCat($conn,$catid, null, $langArr);
		if (!$arr)
			return;
		
		$propNamesDef = $arr['propNamesDef'];
		$propNamesDefld =  $arr['propNamesDefld'];
		$propNames = $arr['propNames'];
		$propNamesld = $arr['propNamesld'];
		
		$insQdef = new insertSQL($conn);
		$insQdef->tableName = "products";
		$insQdef->cols = array();
		$insQdef->insertData = array();
		foreach ($propNamesDef as $p)
			if (isset($_POST[$p])) {
				$insQdef->cols[] = $p;
				$insQdef->insertData[] = $conn->real_escape_string($_POST[$p]);
			}
		
		foreach ($propNamesDefld as $p)
			foreach($langArr as $l) 
				if (isset($_POST[$p][$l])) {
					$insQdef->cols[] = $p.$l;
					$insQdef->insertData[] = $conn->real_escape_string($_POST[$p][$l]);
				}

		if (!$insQdef->executeQuery()) {
			$GLOBALS['statusMessage'] = $insQdef->status;
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
		
		if (count($propNames)) {
			$insQ = new insertSQL($conn);
			$insQ->tableName = "products_".$catid;
			$insQ->cols = array();
			$insQ->cols[] = "infoid";
			$insQ->insertData = array();
			$insQ->insertData[] = $infoID;
			foreach ($propNames as $pn)
				$tmpArr = explode(" as ", $pn); //0 is what to insert, 1 is what to listen for
				$tmpArr[1] = str_replace(" ", "_", trim($tmpArr[1], "`"));
				if (isset($_POST[$tmpArr[1]])) {
					$insQ->cols[] = $tmpArr[0];
					$insQ->insertData[] = $conn->real_escape_string($_POST[$tmpArr[1]]);
				}
			
			if (!$insQ->executeQuery()) {
				$GLOBALS['statusMessage'] = $insQ->status;
				return;
			}
		}
		if (count($propNamesld)) {
			foreach($langArr as $l) {
				$insQld = new insertSQL($conn);
				$insQld->tableName = "products_".$catid."_".$l;
				$insQld->cols = array("infoid");
				$insQld->insertData = array($infoID);
				foreach ($propNamesld as $p)
					$tmpArr = explode(" as ", $p); //0 is what to insert, 1 is what to listen for
					$tmpArr[1] = str_replace(" ", "_", trim($tmpArr[1], "`"));
					if (isset($_POST[$tmpArr[1]][$l])) {
						$insQld->cols[] = $tmpArr[0].$l;
						$insQld->insertData[] = $conn->real_escape_string($_POST[$tmpArr[1]][$l]);
					}
				if (!$insQld->executeQuery()) {
					$GLOBALS['statusMessage'] = $insQld->status;
					return;
				}
			}
		}
		
		$GLOBALS['statusMessage'] = makeStatusMessage(12, "success");
	}

	function getProdFields($conn) {
		$selQ = new selectSQL($conn);
		$selQ->select = array("propid");
		$selQ->where = "catid = ".$conn->real_escape_string($_POST['catid']);
		$selQ->tableNames = array("props_to_prods");
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(53, "error");
			return;
		}
		
		$props = "";
		while($r = $selQ->result->fetch_assoc()) 
			$props .= $r['propid'].",";
		$props = substr($props,0,-1);
		
		$selQ = new selectSQL($conn);
		$selQ->select = array("name".$GLOBALS['language']." as name","langDependant");
		$selQ->tableNames = array("properties");
		$selQ->where = "id IN (".$props.")";
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(53, "error");
			return;
		}
		$def = array();
		$langArr = array();
		while ($l = $GLOBALS['langResult']->fetch_assoc()) {
			$def = array_merge($def, array("Name ".$l['abreviation'] => "names[".$l['abreviation']."]"));
			$def = array_merge($def, array("Description ".$l['abreviation'] => "desc[".$l['abreviation']."]"));
			$langArr[] = $l['abreviation'];
		}

		$def = array_merge($def, array("Price" => "price","Quantity" => "qty","Image" => "imgurl"));		
		
		$ld = array();
		$lid = array();
		while($r = $selQ->result->fetch_assoc())
			if ($r['langDependant'])
				foreach ($langArr as $l)
					$def = array_merge($def, array($r['name']." ".$l => $r['name']."[".$l."]"));
			else 
				$def = array_merge($def, array($r['name'] => $r['name']));
		
		$GLOBALS['data'] = $def;
		$GLOBALS['statusMessage'] = makeStatusMessage(23, "success");
	}

	function getProds($conn) {
		$langArr = array();
		while ($r = $GLOBALS['langResult']->fetch_assoc())
			$langArr[] = $r['abreviation'];
		
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
				$GLOBALS['statusMessage'] = makeStatusMessage(51, "error");
				return;
			}
			while ($r = $selQ->result->fetch_assoc())
				$catid[] = $r['id'];
			$propNames = array("*");
		} else {
			$catid[] = $conn->real_escape_string($_POST['catid']);
			$arr = getPropsForCat($conn,$catid[0],$langArr);
			if (!$arr)
				return;
			
			$propNames = array_merge(array("p.id as id"),$arr['propNamesDef']);
			foreach ($arr['propNamesDefld'] as $pd)
				$propNames[] = $pd;
			foreach ($arr['propNamesld'] as $p)
				$propNames[] = $p;
			
			$propNames = array_merge($propNames,$arr['propNames']);
		}
		
		$selQ = new selectSQL($conn);
		$selQ->select = $propNames;
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
			if (checkTable($conn,"products_".$i)) {
				$selQ->tableNames[] = "products_".$i;
				$selQ->joins[] = "p.id = products_".$i.".infoid";
				$selQ->joinTypes[] = "LEFT JOIN";
			}
			foreach ($langArr as $l) {
				if (checkTable($conn, "products_".$i."_".$l)) {
					$selQ->tableNames[] = "products_".$i."_".$l;
					$selQ->joins[] = "p.id = products_".$i."_".$l.".infoid";
					$selQ->joinTypes[] = "LEFT JOIN";
				}
			}
		}
		if(!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			mysqli_close($conn);
			return;
		}
		$data = array();
		while ($r=$selQ->result->fetch_assoc()) 
			$data[] = $r;
		
		$GLOBALS['data'] = $data;
		$GLOBALS['statusMessage'] = makeStatusMessage(22, "success");
		
	}

	function getPropsForCat($conn,$catid,$langArr = null,$insertQueryLangArr = null) {
		
		$propNamesDef = array("catid","price","qty","imgurl","promo");
		$propNamesDefldtemp = array("names","desc");
		
		
		if (isset($langArr)) {
			$propNamesDefld = array();
			foreach ($propNamesDefldtemp as $tmp)
				foreach ($langArr as $lan)
					$propNamesDefld[] = $tmp.$lan;
		} else 
			$propNamesDefld = $propNamesDefldtemp;
		
		$propNames = array();
		$propNamesld = array();
		
		if (isset($insertQueryLangArr))
			$langArr = $insertQueryLangArr;
		
		$conn = sqlConnectDefault();
		if(is_null($conn)) {
			$statusMessage = makeStatusMessage(1,"error");
			return;
		}

		$selQ = new selectSQL($conn);
		$selQ->tableNames = array("props_to_prods as ptp","properties as p");
		$selQ->joins = array("p.id = ptp.propid");
		$selQ->joinTypes = array("INNER JOIN");
		$selQ->select = array("p.name as propName", "p.langDependant as ld", "p.name".$GLOBALS['language']." as `langName`");
		$selQ->where = "ptp.catid = '".$catid."'";
		if (!$selQ->executeQuery()) {
			$GLOBALS['statusMessage'] = $selQ->status;
			return null;
		} elseif ($selQ->getNumberOfResults() == 0) {
			$GLOBALS['statusMessage'] = makeStatusMessage(53, "error");
			return null;
		} elseif (isset($langArr)) {
			while ($r = $selQ->result->fetch_assoc())
				if ($r['ld'])
					if (isset($insertQueryLangArr))
						$propNamesld[] = $r['propName']." as `".$r['langName']."`";
					else
						foreach ($langArr as $lan)
							$propNamesld[] = $r['propName'].$lan." as `".$r['langName']."`";
				else
					$propNames[] = $r['propName']." as `".$r['langName']."`";
			
		} else 
			while ($r = $selQ->result->fetch_assoc())
				if ($r['ld'])
					$propNamesld[] = $r['propName'];
				else
					$propNames[] = $r['propName'];
		
		return array("propNamesDefld" => $propNamesDefld,"propNamesDef" => $propNamesDef,"propNamesld" => $propNamesld,"propNames" => $propNames);
								
	}
	
?>
