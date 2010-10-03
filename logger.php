<?php

//turn off all error reporting (logging in to a file: logger.php)
error_reporting(0);

$log_file = $_SERVER['DOCUMENT_ROOT']."/log/log.txt";

set_error_handler('log_handler');

function log_handler ( $errno, $errstr,  $errfile, $errline, $errcontext ) {
    //$context = var_export($errcontext, TRUE);
    $page = isset($_GET['p']) ? $_GET['p'] : 'indexpage';
    log_error_ereg("errno:$errno ($errstr) file:$errfile, line:$errline\n, page:$page");

    /* Don't execute PHP internal error handler */
    return true;
}

function log_error_ereg($mess) {
    global $log_file;

    $fd = fopen($log_file, 'a');
    if(!$fd) {
        echo "<pre>$mess</pre>";
    }
    else {
        if(!fwrite($fd, date('Y-m-d H:i:s')." ERR : \n$mess\n\n")) {
            echo "<pre>$mess</pre>";
        }
        fclose($fd);
    }
}

?>
