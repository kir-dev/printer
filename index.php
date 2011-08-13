<?php
require_once('logger.php');
require_once('db.php');

require_once(SSO_CLASS);

session_start();

$sso = new OpenSSO;
$loggedIn = $sso->isLogin();

//set timezone (php conf error?)
date_default_timezone_set('Europe/Berlin');

$oSql = new mysqli($host, $user, $pass, $db);

if (!empty($my->connect_error)) die($my->connect_error);


$oSql->set_charset('utf8');


//update inactive printers to off
$q = $oSql->prepare("UPDATE `printers` SET `on` = FALSE WHERE TIMEDIFF(NOW(), `last_refreshed`) > '00:06:00'");
$q->execute();
$q->close();

//---

$q = $oSql->prepare("SELECT printers.type,
                    printers.colors,
                    printers.model,
                    printers.desc,
                    printers.loc,
                    printers.last_refreshed,
                    printers.on,
                    users.uid,
                    users.nick
                    FROM printers
                    INNER JOIN users ON users.uid = printers.uid " .
                    // ...only newer than 1 months (30 * 24 hours)
                    "WHERE UNIX_TIMESTAMP(printers.last_refreshed) > UNIX_TIMESTAMP(CURRENT_TIMESTAMP())-30*24*60*60");

$q->execute();
$q->bind_result($type, $colors, $model, $desc, $loc, $last_refreshed, $pr_on, $uid, $nick);
/**
 * convert timestamp diff to human readable format string
 *
 * @param int two timestamp difference
 * return string
 */
function lastRefreshedText($tsDiff) {
    $str = '';

    $day_sec = 60*60*24;
    $week_sec = $day_sec * 7;
    //$month_sec = $day_sec * 30;

    /*$months = floor($tsDiff / $month_sec);
    if ($months > 0) {
            $str = $months.' hónapja ';
            $tsDiff -= $months * $month_sec;
    }*/

    $weeks = floor($tsDiff / $week_sec);
    if ($weeks > 0) {
        $str .= $weeks.' hete ';
        $tsDiff -= $weeks * $week_sec;
    }

    $days = floor($tsDiff / $day_sec);
    if ($days > 0) {
        $str .= $days.' napja ';
        $tsDiff -= $days * $week_sec;
    }

    if (!$weeks && !$days) {
        if (floor($tsDiff / 3600) > 0)
            $str = floor($tsDiff / 3600) . ' órája';
        else
            $str = 'kevesebb, mint <br/>egy órája';
    }

    return $str;
}

$printers = array();
$offline_printers = array();
while ( $q->fetch() ) {

    $last_refreshed = time() - strtotime($last_refreshed); //calculate the difference in seconds

    switch ($type) {
        case 'laser' : $type = 'lézer'; break;
        case 'ink' : $type = 'tintasugaras'; break;
        default : $type = '!hiba!';
    }

    $colors = explode('|', $colors);
    $str_colors = '';
    for ($i = 0; $i < count($colors); ++$i) {
        switch($colors[$i]) {
            case 'black': $str_colors .= 'fekete'; break;
            case 'color': $str_colors .= 'színes'; break;
            case 'photo': $str_colors .= 'fotó'; break;
            default: $str_colors .= '!hiba!';
        }
        if ($i < count($colors) -1) $str_colors .= ', ';
    }

    $printer = array("type" => $type,
        "colors" => $str_colors,
        "model" => $model,
        "desc" => ($loggedIn ? nl2br($desc) : " - "), //ha be van jelentkezve megjelenítjük az infókat
        "loc" => $loc,
	"last_refreshed_ts" => $last_refreshed,
        "last_refreshed" => (int)($last_refreshed / 60)." perce",
        "uid" => $uid,
        "nick" => $nick
    );
    if (intval($pr_on))
        $printers[] = $printer;
    else {
        $printer['last_refreshed'] = lastRefreshedText($last_refreshed);
        $offline_printers[]= $printer;
    }

    unset($printer);
}

$q->close();

$oSql->close();

//sort offline printers
//compare function, parameters are printer array
function cmp($a, $b) {
	if ($a['last_refreshed_ts'] == $b['last_refreshed_ts']) return 0;
	
	return ($a['last_refreshed_ts'] > $b['last_refreshed_ts']) ? 1 : -1;
}
uasort($offline_printers, 'cmp');

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-type: text/html; charset=utf-8');

$root = 'http://printer.sch.bme.hu/';

require_once('gui.php');

?>
