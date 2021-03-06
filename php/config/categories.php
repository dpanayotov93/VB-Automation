<?php

	$conn = sqlConnectDefault();
	
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	$user = getUser($conn);

	if (empty($user))
		$log = createLog("","categories");
	else 
		$log = createLog("","categories","","",$user['id']); 
	
	if (!isset($language))
		$language = $GLOBALS['language'];

	if (isset($_POST['catid']))
		$where = "id = '".$conn->real_escape_string($_POST['catid'])."'";
	elseif (isset($catid))
		$where = "id = '".$catid."'";
	else 
		$where = "parentid IS NULL OR parentid = 0";
	if (isset($allLangs)) {
		require_once 'languageConfig.php';
		$data = getCat($where,$conn,null,$langResult);
	} else
		$data = getCat($where,$conn,$GLOBALS['language'],null);
	
	if (empty($data))
		$statusMessage = makeStatusMessage(51, "error");
	else
		$statusMessage = makeStatusMessage(21, "success");
	
	mysqli_close($conn);
	return;
	
function getCat($where, $conn,$lang,$langArr) {
	$selQ = new selectSQL($conn);
	if (!empty($lang))
		$selQ->select = array ("id","parentid","name".$lang." as nameEN","desc".$lang." as descEN","imgurl");
	else {
		$selQ->select = array ("id","parentid");
		foreach ($langArr as $l) {
			$selQ->select[] = "name".$l;
			$selQ->select[] = "desc".$l;
		}
		$selQ->select[] = "imgurl";
	}
	$selQ->tableNames = array("categories");
	$selQ->where = $where;
	if(isset($_POST['deleted']))
		$selQ->where .= " AND visible = 0";
	else
		$selQ->where .= " AND visible = 1";
	if (!$selQ->executeQuery()) 
		return;
	if ($selQ->getNumberOfResults() > 0) {
		while ($row = $selQ->result->fetch_assoc()) {
			$subCats = getCat("parentid = '".$row['id']."'",$conn,$lang,$langArr);
			if ($subCats)
				$data[] = array_merge($row, array("subCategories" => $subCats));
			else 
				$data[] = $row;
		}
		return $data;
	} else
		return;
}
?>
