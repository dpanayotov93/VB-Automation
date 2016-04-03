<?php

function writeLog($log, $errorMessage = null) {
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		1; // make error somewhere
		return;
	}
	if ($log['type'] == "0") {
		$insQ = new insertSQL($conn);
		$insQ->tableName = "visit_logs";
		$insQ->cols = array("date","url","catid","prodid","ip","userid");
		$insQ->insertData = array(time(),$log['page'],$log['catid'],$log['prodid'],ip2long($_SERVER['REMOTE_ADDR']),$log['user']);
		if ($errorMessage) {
			$insQ->cols[] = "error";
			$insQ->cols[] = "message";
			$insQ->insertData[] = "1";
			$insQ->insertData[] = $errorMessage;
		}
		if (!$insQ->executeQuery()) 
			1; // make error somewhere
		return;
	}
}

function createLog($type = 0, $page = null, $catid = null, $prodid = null, $user = null) {
	if (empty($type))
		$type = 0;
	if (empty($page))
		$page = null;
	if (empty($catid))
		$catid = null;
	if (empty($prodid))
		$prodid = null;
	if (empty($user))
		$user = null;
		
	return array("type" => $type,"page" => $page, "catid" => $catid, "prodid" => $prodid, "user" => $user);
}

function getLog($conn, $page, $catid, $prodid, $user, $ip, $dateStart, $dateEnd) {
	$selQ = new selectSQL($conn);
	
	$selQ->select = array("date as Date","url as Page","catid","NameEN as Category","prodid","NamesEN as Product","ip as `IP Address`","user as User","userid");
	
	$selQ->tableNames = array("visit_logs","users","categories","products");
	$selQ->joins = array("visit_logs.user = users.id","visit_logs.catid = categories.id","visit_logs.prodid = products.id");
	$selQ->joinTypes = array("LEFT JOIN","LEFT JOIN","LEFT JOIN");
	
	
	$selQ->where = "";
	if (isset($page))
		$selQ->where .= "url = '".$conn->real_escape_string($page)."' AND ";
	
	if (isset($catid)) 
		$selQ->where .= "catid = '".$conn->real_escape_string($catid)."' AND ";
	
	if (isset($prodid)) 
		$selQ->where .= "prodid = '".$conn->real_escape_string($prodid)."' AND ";
	
	if (isset($user))		
		if (is_int($user) && $user != 0)
			$selQ->where .= "userid = '".$conn->real_escape_string($user)."' AND ";
		else 
			$selQ->where .= "userid = null AND ";
	
	if (isset($ip)) 
		$selQ->where .= "ip = '".$conn->real_escape_string(long2ip($ip))."' AND ";
	
	if (isset($dateStart)) 
		$selQ->where .= "date > '".$conn->real_escape_string($dateStart)."' AND ";
	
	if (isset($dateEnd)) 
		$selQ->where .= "date < '".$conn->real_escape_string($dateEnd)."' AND ";
	
	$selQ->where = rtrim($selQ->where, " AND ");
	
	if (!$selQ->executeQuery()) 
		return null;
	else {
		$data = array();
		while ($r = $selQ->result->fetch_assoc()) 
			$data[] = $r;
		return $data;
	}
}

function getLogVisits($conn, $dateStart=null, $dateEnd=null, $unique=null, $item=null) {
	$selQ = new selectSQL($conn);
	$selQ->tableNames = array("visit_logs");
	
	switch ($item){
		case "category":
			$selQ->select = array("visit_logs.catid as Category ID","nameEN as Name");
			$selQ->tableNames[] = "categories";
			$selQ->joins = array("visit_logs.catid = categories.id");
			$selQ->joinTypes = array("RIGHT JOIN");
			$selQ->groupby = "visit_logs.catid";
			break;
		case "product":
			$selQ->select = array("visit_logs.prodid as Product ID","namesEN as Name");
			$selQ->tableNames[] = "products";
			$selQ->joins = array("visit_logs.prodid = products.id");
			$selQ->joinTypes = array("RIGHT JOIN");
			$selQ->groupby = "visit_logs.prodid";
			break;
		case "page":
			$selQ->select = array("visit_logs.url as Page");
			$selQ->groupby = "visit_logs.page";
			break;
		default: 
			$selQ->select = array();	
	}

	if ($unique) 
		$selQ->select[] = "count(DISTINCT visit_logs.ip) as Visits";
	else 
		$selQ->select[] = "count(visit_logs.id) as Visits";
		
	if (isset($dateStart))
		$selQ->where .= "date > '".$conn->real_escape_string($dateStart)."' AND ";
	if (isset($dateEnd))
		$selQ->where .= "date < '".$conn->real_escape_string($dateEnd)."' AND ";

	$selQ->where = rtrim($selQ->where, " AND ");

	if (!$selQ->executeQuery())
		return null;
	else {
		$data = array();
		while ($r = $selQ->result->fetch_assoc())
			$data[] = $r;
		return $data;
	}
}

function getRecursiveVisits($conn, $period, $startDate=null, $endDate=null, $unique=null, $item=null) {
	if (empty($endDate))
		$endDate = time();
	if (empty($startDate))
		$startDate = strtotime("1st April 2016");
	if ($startDate > $endDate)
		return -1;
	$data = array();
	for ($i = $startDate; $i < $endDate; $i += $period) {
		$index = date("d.m.Y", $i)." - ".date("d.m.Y", $i + $period);
		$data[$index] = getLogVisits($conn,$i,$i + $period,$unique, $item);
		if (empty($data[$index]))
			$data[$index] = "n/a";

	}
	return $data;
}

?>