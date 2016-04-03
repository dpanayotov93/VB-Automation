<?php
	
	require_once 'logConfig.php';
	
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
	
	if ($_POST['type'] == "visitsCount") {
		if (!empty($_POST['startDate']) && is_int($_POST['startDate']))
			$startDate = $_POST['startDate'];
		else 
			$startDate = null;
		
		if (!empty($_POST['endDate']) && is_int($_POST['endDate']))
			$endDate = $_POST['endDate'];
		else 
			$endDate = null;
		
		if (!empty($_POST['unique']))
			$unique = true;
		else 
			$unique = null;
		
		if (!empty($_POST['item']) && ($_POST['item'] == "category" || $_POST['item'] == "product" || $_POST['item'] == "page"))
			$item = $_POST['item'];
		else 
			$item = null;
		
		if (!empty($_POST['period']) && is_int($_POST['period']))
			$period = $_POST['period'];
		else 
			$period = 24 * 60;
		
		$data = getRecursiveVisits($conn, $period, $startDate, $endDate, $unique, $item);
	}
	
	mysqli_close($conn);
	return;

	
	

?>