<?php
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
		return;
	}
	
	$adminCheck = 1; //implement admin check
	
	if (isset($_POST["userid"]))
		$userid = $conn->real_escape_string($_POST['userid']);
	
	$user = array("EN" => "User","BG" => "Потребител");
	$oid = array("EN" => "User","BG" => "Потребител");
	$uid = array("EN" => "UserID","BG" => "UserID");
	$payment = array("EN" => "Payment method","BG" => "Начин на плащане");
	$date = array("EN" => "Date of order","BG" => "Дата на поръчка");
	$ip = array("EN" => "Client IP address","BG" => "IP адрес на клиента");
	$status = array("EN" => "Order status","BG" => "Статус на поръчката");
	$address = array("EN" => "Reciever address","BG" => "Адрес за получаване");
	$totalPrice = array("EN" => "Total price","BG" => "Обща стойност");

	$product = array("EN" => "Product","BG" => "Продукт");
	$pid = array("EN" => "ProductID","BG" => "ProductID");
	
	
	$selQ = new selectSQL($conn);
	$selQ->tableNames = array("orders as o");
	$selQ->select = array("o.id as ".$oid[$language],"o.payment as ".$payment[$language],"o.date as ".$date[$language],"o.status as ".$status[$language],"o.address as ".$address[$language],"o.totalprice as".$totalPrice[$language]);
	if ($adminCheck)
		$selQ->select[] = "o.ip as ".$ip[$language];
		$selQ->select[] = "u.id as ".$uid[$language];
		$selQ->select[] = "u.user as ".$user[$language];
		$selQ->tableNames[] = "users as u";
		$selQ->joins = array("o.userid = u.id");
		$selQ->joinTypes = array("JOIN");
	if (isset($userid)) 
		$selQ->where = "userid = '".$userid."'";
	if (isset($_POST['new'])) {
		if (isset($selQ->where))
			$selQ->where .= " AND ";
		$selQ->where = "status = '0'";
	}
	if (isset($_POST['active'])) {
		if (isset($selQ->where))
			$selQ->where .= " AND ";
		$selQ->where = "status != '2'"; //check int of inactive order
	}
	$selQ->order = "o.id DESC";
	if (isset($_POST['countPerPage']) && is_int($_POST['countPerPage']))
		if (isset($_POST['page']) && is_int($_POST['page']))
			$selQ->limit = ($_POST['countPerPage'] - 1)*$_POST['page'].",".$_POST['countPerPage'];
		else
			$selQ->limit = $conn->real_escape_string($_POST['countPerPage']);
	
	if (!$selQ->executeQuery()) {
		$statusMessage = $selQ->status;
		mysqli_close($conn);
		return;
	}
	if ($selQ->getNumberOfResults() < 1) {
		$statusMessage = makeStatusMessage(2342, "error", "No orders to select.");
		mysqli_close($conn);
		return;
	}

	$selQProdNames = new selectSQL($conn);
	$selQProdNames->select = array("p.name".$language." as ".$product[$language],"p.id as ".$pid[$language]);
	$selQProdNames->tableNames = array("products as p","ordered_products as op");
	$selQProdNames->joins = "p.id = op.productid";
	$selQProdNames->joinTypes = "JOIN";
	$data = array();
	while ($r = $selQ->result->fetch_assoc()) {
		$productData = array();
		$selQProdNames->where = $r[$oid[$language]];
		if (!$selQ->executeQuery()) {
			$statusMessage = $selQ->status;
			mysqli_close($conn);
			return;
		}
		while ($row = $selQProdNames->result->fetch_assoc()) 
			$productData = $row;
		
		$r[$status[$language]] = getStatusOfOrder($r[$status[$language]],$language);
		$r[$payment[$language]] = getPaymentMethodOfOrder($r[$payment[$language]],$language);
		$r[$delivery[$language]] = getDeliveryOptionOfOrder($r[$delivery[$language]],$language);
		$data[] = array_merge($r, array("products" => $productData));
		unset($productData);
	}
	
	$statusMessage = makeStatusMessage(234, "success", "THE ORDER IS GIVEN!");
	
	mysqli_close($conn);
	return;
	
	function getStatusOfOrder($o,$l) {
		if ($l = "BG") 
			switch ($o) {
				case '0':
					return "Неодобрена поръчка";
				case '1':
					return "Одобрена поръчка";
				case '2':
					return "Изпратена поръчка";
			}
		else if ($l = "EN")
			switch ($o) {
				case '0':
					return "Unreviewed order";
				case '1':
					return "Reviewed order";
				case '2':
					return "Sent order";	
			}
	}
	
	function getDeliveryOptionOfOrder($o,$l) {
		if ($l = "BG")
			switch ($o) {
				case '0':
					return "Еконт";
			}
		else if ($l = "EN")
			switch ($o) {
				case '0':
					return "Econt";	
			}
	}
	
	function getPaymentMethodOfOrder($o,$l) {
		if ($l = "BG")
			switch ($o) {
				case '0':
					return "Банков превод";
				case '1':
					return "Наложен платеж";
			}
		else if ($l = "EN")
			switch ($o) {
				case '0':
					return "Bank transaction";
				case '1':
					return "Cash on delivery";
			}
	}
?>