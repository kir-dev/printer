<?php
	session_start();

	if (!isset($_COOKIE[$sso['idpCookie']])) {
		$_SESSION = array(); //unset variables

		session_destroy();
	}

	header('Location: ../index.php');
?>