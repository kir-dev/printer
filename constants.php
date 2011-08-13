<?php

//alkalmazás tesztelési beállítások
//ÉLES TELEPÍTÉSKOR EZT **FALSE** értékre kell állítani!
define('IS_TST_MODE', true);

define('ROOT', 'http://printer.sch.bme.hu/');
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
define('SELF', $_SERVER['PHP_SELF']);
define('BACK', '<a href="#" onclick="history.back();">vissza</a>');

//sso
if (IS_TST_MODE) {
    define('SSO_CLASS', DOC_ROOT . 'printprofil/class/clsDummySSO.php');
} else {
    define('SSO_CLASS', DOC_ROOT . 'printprofil/open-sso.lib/open-sso.class.php');
}

?>