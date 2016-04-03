<?php

$langArr = getLanguages($conn);
if (is_null($langArr))
	return;

function getLanguages($conn) {
	$selQ = new selectSQL($conn);
	$selQ->select = array("abreviation");
	$selQ->tableNames = array ("languages");
	if (!$selQ->executeQuery() || $selQ->getNumberOfResults() == 0) {
		$GLOBALS['statusMessage'] = makeStatusMessage(2, "error");
		mysqli_close($conn);
		return null;
	} else {
		$lang = array();
		while ($r=$selQ->result->fetch_assoc())
			$lang[] = $r['abreviation'];
			return $lang;
	}
}
?>