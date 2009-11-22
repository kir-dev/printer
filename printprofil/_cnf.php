<?php

// Included vars: $host, $user, $pass, $db
require_once('../db.php');

// include classes
require_once('class/clsUser.php');
require_once('class/clsPrinter.php');
require_once('funct.php');

//config
if (isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] != "") {
	$sso['uid'] = $_SERVER['REMOTE_USER'];
	if (isset($_SERVER['HTTP_EMAIL']))
		$sso['email'] = $_SERVER['HTTP_EMAIL'];
	else { //no mail, error message and exit
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header('Content-type: text/html; charset=utf-8');

		print 'Nincs e-mail címed az adatbázisunkban, enélkül nem tudod használni a portál ezen részét! <br/>Kérlek töltsd ki a <a href="https://idp.sch.bme.hu:443/profile">VIR profilodban</a>!';
		exit();
	}
	if (!isset($_SERVER['HTTP_NICKNAME']))
		$sso['nick'] = $sso['uid'];
	else
		$sso['nick'] = $_SERVER['HTTP_NICKNAME'];
}

$sso['idpCookie'] = 'sunIdentityServerAuthNServer';

//to constants.php
define('ROOT', 'http://printer.kirdev.sch.bme.hu/');
define('SELF', $_SERVER['PHP_SELF']);
define('BACK', '<a href="#" onclick="history.back();">vissza</a>');

session_cache_expire(60); //60 minutes
session_start();

?>
