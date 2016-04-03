<?php
	require_once ("config/config.php");
	sec_session_start();
	
	$q = "";
	$data = array();
	
	if(empty($_POST["lang"]))
		$language = "EN";
	else
		$language = $_POST["lang"];
	
	if (!empty($_POST["q"]))
		if (file_exists("config/".$_POST["q"].".php")) {
			require_once("config/".$_POST["q"].".php");
			$q = $_POST['q'];
		}
		else 
			$statusMessage = makeStatusMessage(1, "error", "Incorrect query request...");
	else 
		$statusMessage = makeStatusMessage(0, "error", "Empty query request...");
	
	if (!empty($data))
		$main = array($q => nullToEmptyString($data), "status" => $statusMessage);
	else 
		$main = array("status" => $statusMessage);

	echo json_encode($main,JSON_UNESCAPED_UNICODE);	
	
	if (isset($log))
		if ($statusMessage['type'] == "error")
			writeLog($log, $statusMessage['message']);
		else 
			writeLog($log);
		
			
			
	if(isset($GLOBALS['debugSQL']) && $GLOBALS['debugSQL'])
		echo "<form method=post action=handle.php>
		<input type=text name=q />
		<input type=text name='showCats' value=1 />
				 
		<input type=hidden value=1 name=debug />
		<input type=submit>
		</form>";
?>