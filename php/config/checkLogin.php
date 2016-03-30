<?php
	if (!isset($_POST["user"])) {
		$statusMessage = makeStatusMessage(4,"error");
		return;
	}
	
	$conn = sqlConnectDefault();
	if(is_null($conn)) {
		$statusMessage = makeStatusMessage(1,"error");
		return;
	}
	
	$user = $conn->real_escape_string($_POST["user"]);
	$userdata = getUser($conn);
	
	if (!isset($userdata))
		$statusMessage = makeStatusMessage(10, "error");
	else if ($userdata['user'] == $user)
		$statusMessage = makeStatusMessage(1, "success");
	else 
		$statusMessage = makeStatusMessage(101, "error");
			
	mysqli_close($conn);
	return;

?>