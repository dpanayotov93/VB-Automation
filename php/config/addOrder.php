<?php
	if (!isset($_POST["userid"]) OR !isset($_POST['products']) OR !count($_POST['products'])) {
		$statusMessage = makeStatusMessage(2,"error","Incomplete query request...");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$nameLang = array("EN" => "Product","BG" => "Продукт");
	$PriceLang = array("EN" => "Price","BG" => "Цена");
	
	$userid = $conn->real_escape_string($_POST['userid']);
	$prodids = array();
	$prodQuantity = array();
	foreach ($_POST['products'] as $pid => $q) {
		$pid = $conn->real_escape_string($pid);
		$prodids[] = $pid;
		if (!is_int($q) || $q < 1)
			$q = 1;
		$prodQuantity[$pid] = $q;
		
	}	
	
	$selQ = new selectSQL($conn);
	$selQ->distinct = true;
	$selQ->select = array("p.id as prodid","name".$language." as name","IF(dp.flat, dp!.flat, dc.flat) as flat","IF(dp.percent, dp.percent, dc.percent) as percent","IF(dp.minprice, dp.minprice, dc.minprice) as minprice","p.price as price");
	$selQ->tableNames = array("products as p","discounts as dp","discounts as dc");
	$selQ->joinTypes = array("LEFT OUTER JOIN","LEFT OUTER JOIN");
	$selQ->joins = array("p.id = dc.productid","p.catid = dp.categoryid");
	$selQ->where = "p.id IN (".arrToQueryString($prodids, true).")";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() < 1) {
		$statusMessage = makeStatusMessage(2342, "error", "No products to select.");
		mysqli_close($conn);
		return;
	}

	$productInfo = array();
	while ($r = $selQ->result->fetch_assoc()) {
		$r['price'] *= $prodQuantity[$r['prodid']];
		if (!empty($r['percent']))
			$r['price'] *= $r['percent']/100;
		if (!empty($r['flat']) AND $r['price'] > $r['minprice'])
			$r['price'] -= $r['flat'];  
		$productInfo[] = array($nameLang[$language] => $r['name'],$priceLang[$language] => $r['price']);
	}
	
	$payment = $conn->real_escape_string($_POST['payment']);
	$address = $conn->real_escape_string($_POST['address']);
	$totalPrice = 0;
	foreach ($productInfo as $pi) 
		$totalPrice += $pi['price'];
	
	$insQ = new insertSQL($conn);
	$insQ->cols = array("userid","payment","date","ip","address","totalprice");
	$insQ->insertData = array($userid,$payment,time(),ip2long($_SERVER['REMOTE_ADDR']),$address,$totalPrice);
	$insQ->tableName = "orders";
		
	if (!$insQ->executeQuery()) {
		$statusMessage = $insQ->status;
		mysqli_close($conn);
		return;
	}
	
	$selQlast = new selectSQL($conn);
	$selQlast->select = array("LAST_INSERT_ID() as lastid");
	if (!$selQlast->executeQuery()) {
		$statusMessage = $selQlast->status;
		mysqli_close($conn);
		return;
	}
	$row = $selQlast->result->fetch_assoc();
	$lastID = $row['lastid'];
	
	foreach ($productInfo as $prod) {
		unset($insQ);
		$insQ = new insertSQL($conn);
		$insQ->cols = array("orderid","productid","productcount");
		$insQ->insertData = array($lastID,$prod);
		$insQ->tableName = "ordered_products";
		
		if (!$insQ->executeQuery()) {
			$statusMessage = $insQ->status;
			mysqli_close($conn);
			return;
		}
	}
	
	$statusMessage = makeStatusMessage(234, "success", "THE ORDER IS GIVEN!");
	
	mysqli_close($conn);
	return;
?>