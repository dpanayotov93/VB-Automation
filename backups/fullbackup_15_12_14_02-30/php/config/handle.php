<?php
	require_once ("config/config.php");
	
	
	$q = "";
	$data = array();
	
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
		$main = array($q => $data, "status" => $statusMessage);
	else 
		$main = array("status" => $statusMessage);
	
	$main = utf8_string_array_encode($main);
	echo json_encode($main);
		
	if(isset($GLOBALS['debugSQL']) && $GLOBALS['debugSQL'])
		echo "<form method=post action=handle.php>
		<input type=text name=q />
		<input type=text value=email name=email />
		<input type=text value=pass name=pass />
		<input type=text value=id name=id />
		<input type=text value=name name=names[EN] />
		<input type=text value=desc name=desc[EN] />
				
		
		<input type=hidden value=1 name=fid[] />
		<input type=hidden value=2 name=fid[] />
		<input type=hidden value=3 name=fid[] />
		<input type=hidden value=4 name=fid[] />
		<input type=hidden value=5 name=fid[] />
		<input type=hidden value=6 name=fid[] />
		<input type=hidden value=7 name=fid[] />
		<input type=hidden value=8 name=fid[] />
		<input type=hidden value=9 name=fid[] />
		<input type=hidden value=12 name=fid[] />
				
		<input type=text value=Cable name=filters[connection] />
				 
		<input type=hidden value=1 name=debug />
		<input type=submit>
		</form>";
?>