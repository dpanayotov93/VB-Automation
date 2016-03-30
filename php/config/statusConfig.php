<?php

function makeStatusMessage($id,$type,$additional=null) {
	require_once 'variables/statusMessageVariables.php';
	
	if ($type == "error") 
		if (isset($errorMsg[$GLOBALS['language']][$id]))
			$message = $errorMsg[$GLOBALS['language']][$id];
		else 
			$message = "";
	else if ($type == "success")
		if (isset($succesMsg[$GLOBALS['language']][$id]))
			$message = $succesMsg[$GLOBALS['language']][$id];
		else 
			$message = "";
	
	return array ("type" => $type, "message" => $message." ".$additional);
}
?>