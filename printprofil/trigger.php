<?php

/**
 * OpenSSO által átadott adatok ($_SERVER['<valtozo nev>'])
 *
 * |--------------------|------------------------------------|
 * |  REMOTE_USER		|	felhasználói név				 |
 * |  HTTP_EMAIL		|	e-mail cím						 |
 * |  HTTP_NICKNAME		|	becenév							 |
 * |--------------------|------------------------------------|
 *
 */

require_once('_cnf.php');

session_destroy();
session_start();

//$_SESSION['oUser'] = array("uid" => $sso['uid'], "email" => $sso['email']);

$_SESSION['oUser'] = array("uid" => 'testUser', "email" => "test_user@test.com");

$_SESSION['loginned'] = true;

header("Location: index.php");

?>
