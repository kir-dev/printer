<?php
require_once('_cnf.php');

session_destroy();
session_cache_expire(60); //60 minutes
session_start();

$sso = new openSSO;
$sso->trigger();

?>
