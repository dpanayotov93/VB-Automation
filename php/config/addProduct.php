<?php
$conn = sqlConnectDefault();
if(is_null($conn)) {
	$statusMessage = makeStatusMessage(6,"error","Could not connect to database!");
	return;
}

?>