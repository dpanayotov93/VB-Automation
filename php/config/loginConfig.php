<?php

function sec_session_start() {
	$session_name = 'automation-point_login';
	$secure = false;
	$httponly = true;
	if (ini_set('session.use_only_cookies', 1) === FALSE)
		return null;
	
	$cookieParams = session_get_cookie_params();
	session_set_cookie_params($cookieParams["lifetime"],$cookieParams["path"], $cookieParams["domain"], $secure,$httponly);

	session_name($session_name);
	session_start();
	session_regenerate_id(true);
	return true;
}

function clearSessionData() {
	setcookie(session_name(),'', time() - 42000);
	session_destroy();
}

function getUser($conn, $user = null, $pass = null) {
	if (!isset($user) || !isset($pass)) {
		if (isset($_SESSION['username']))
			$user = $_SESSION['username'];
		else
			return null;
		if (isset($_SESSION['password']))
			$pass = $_SESSION['password'];
		else
			return null;
	} else {
		$user = $conn->real_escape_string($user);
		$pass = $conn->real_escape_string($pass);
	}

	$selQ = new selectSQL($conn);
	$selQ->select = array("id","access","user as name");
	$selQ->tableNames = array("users");
	$selQ->where = "user = '".$user."' AND password = '".$pass."'";
	if (!$selQ->executeQuery())
		return null;
	else {
		$r = $selQ->result->fetch_assoc();
		if ($selQ->getNumberOfResults() != 1)
			return null;
		else
			return $r;
	}
}

function checkLoginAttempts($conn, $seconds, $user = null) {
	$timeout = time() - $seconds;
	$ip = ip2long($_SERVER['REMOTE_ADDR']);
	$selQ = new selectSQL($conn);
	$selQ->select = array("date");
	$selQ->tableNames = array("login_logs");
	if (isset($user))
		$selQ->where = "(user = '".$user."' OR ip = '".$ip."')";
	else
		$selQ->where = "ip = '".$ip."'";
	
	$selQ->where .= " AND date > '".$timeout."' AND result = '0'";
	if (!$selQ->executeQuery())
		return -1;
	else
		return $selQ->getNumberOfResults();
}

?>