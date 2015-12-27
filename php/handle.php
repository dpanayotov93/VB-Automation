<?php
	require_once ("config/config.php");
	
	$q = "";
	$data = array();
	
	if (!empty($_POST["q"]))
		if (file_exists("config/".$_POST["q"].".php")) {
			if(empty($_POST["lang"]))
				$language = "EN";
			else 
				$language = $_POST["lang"];
			require_once("config/".$_POST["q"].".php");
			$q = $_POST['q'];
		}
		else 
			$statusMessage = makeStatusMessage(1, "error", "Incorrect query request...");
	else 
		$statusMessage = makeStatusMessage(0, "error", "Empty query request...");
	
	if (!empty($data))
		$main = array($q => $data, "status" => $statusMessage);
	else 
		$main = array("status" => $statusMessage);
	
	//$main = utf8_string_array_encode($main);

	echo json_encode($main,JSON_UNESCAPED_UNICODE);	
		
	if(isset($GLOBALS['debugSQL']) && $GLOBALS['debugSQL'])
		echo "<form method=post action=handle.php>
		<input type=text name=q />
		<input type=text name='show' value=1 />
				 
		<input type=hidden value=1 name=debug />
		<input type=submit>
		</form>";
?>