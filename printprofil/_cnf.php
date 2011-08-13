<?php
require_once('../logger.php');

// Included vars: $host, $user, $pass, $db
require_once('../db.php');

// include classes
require_once('class/clsUser.php');
require_once('class/clsPrinter.php');
require_once('funct.php');

require_once(SSO_CLASS);

session_cache_expire(60); //60 minutes
session_start();

?>
