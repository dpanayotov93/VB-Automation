<?php
	if (!isset($_POST["userid"]) OR !isset($_POST['products']) OR !count($_POST['products'])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	$userid = $conn->real_escape_string($_POST['userid']);
	$user = getUser($conn);
	if ($user['id'] != $userid) {
		$statusMessage = makeStatusMessage(3,"error");
		mysqli_close($conn);
		return;
	}
	
	$log = createLog("","order","","",$userid);
	
	require_once 'orderConfig.php';
	
	$nameLang = array("EN" => "Product","BG" => "Продукт");
	$priceLang = array("EN" => "Price","BG" => "Цена");
	
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
	$selQ->select = array("p.id as prodid","names".$language." as name","IF(dp.flat, dp.flat, dc.flat) as flat","IF(dp.percent, dp.percent, dc.percent) as percent","IF(dp.minprice, dp.minprice, dc.minprice) as minprice","p.price as price");
	$selQ->tableNames = array("products as p","discounts as dp","discounts as dc");
	$selQ->joinTypes = array("LEFT JOIN","LEFT JOIN");
	$selQ->joins = array("p.id = dp.productid","p.catid = dc.categoryid");
	$selQ->where = "p.id IN (".arrToQueryString($prodids, true).")";
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() < 1) {
		$statusMessage = makeStatusMessage(52, "error");
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
	$delivery = $conn->real_escape_string($_POST['delivery']);
	
	$selDelivery = new selectSQL($conn);
	$selDelivery->select = array("type","minprice");
	$selDelivery->tableNames = array("delivery_discounts");
	$selDelivery->where = "userid = ".$userid;
	if (!$selDelivery->executeQuery()) {
		$statusMessage = $selDelivery->status;
		mysqli_close($conn);
		return;
	}
	$r = $selDelivery->result->fetch_assoc();
	$deliveryPayment = $r['type'];
	
	$address = $conn->real_escape_string($_POST['address']);
	$totalPrice = 0;
	foreach ($productInfo as $pi) 
		$totalPrice += $pi[$priceLang[$language]];
	
	$insQ = new insertSQL($conn);
	$insQ->cols = array("userid","payment","delivery","deliverypayment","date","ip","address","totalprice");
	$insQ->insertData = array($userid,$payment,$delivery,$deliveryPayment,time(),ip2long($_SERVER['REMOTE_ADDR']),$address,$totalPrice);
	$insQ->tableName = "orders";
		
	if (!$insQ->executeQuery()) {
		$statusMessage = $insQ->status;
		mysqli_close($conn);
		return;
	}
	
	$selQlast = new selectSQL($conn);
	$selQlast->select = array("id as lastid");
	$selQlast->where = "id = LAST_INSERT_ID()";
	$selQlast->tableNames = array("orders");
	if (!$selQlast->executeQuery()) {
		$statusMessage = $selQlast->status;
		mysqli_close($conn);
		return;
	}
	$row = $selQlast->result->fetch_assoc();
	$lastID = $row['lastid'];
	
	foreach ($prodids as $prod) {
		unset($insQ);
		$insQ = new insertSQL($conn);
		$insQ->cols = array("orderid","productid","productcount");
		$insQ->insertData = array($lastID,$prod,$prodQuantity[$prod]);
		$insQ->tableName = "ordered_products";
		
		if (!$insQ->executeQuery()) {
			$statusMessage = $insQ->status;
			mysqli_close($conn);
			return;
		}
	}
	
	$statusMessage = makeStatusMessage(17, "success");
	
	mysqli_close($conn);
	return;
?>
