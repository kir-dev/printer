<?php
/**
 * design modifier functions
 */

function design_Header() {
    $loggedIn = isset($_SESSION['loginned']) && $_SESSION['loginned'] === true && isset($_SESSION['oUser']['uid']);

    print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    ?><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>printer.sch.bme.hu</title>
        <link type="text/css" href="../res/css/style.css" rel="stylesheet" />
        <script src="../res/script.js" type="text/javascript"></script>
        <!-- navbar script include -->
        <script type="text/javascript">
               var NavBarProtocol = ((document.location.protocol == "https:") ? "https" : "http");
               document.write(unescape("%3Cscript src='" + NavBarProtocol + "://idp.sch.bme.hu/navbar/vir-navbar3.js' type='text/javascript'%3E%3C/script%3E"));
       </script>
    </head>
    <body>
        <!-- navbar config and call -->
        <script type="text/javascript">
            //<!--
            var navbarConf = {
                loginLink: '<?php print ROOT; ?>printprofil/trigger.php',
                logoutLink: 'https://idp.sch.bme.hu/opensso/UI/Logout?goto=<?php print ROOT; ?>printprofil/logout.php',
                loggedIn: <?php print (int)$loggedIn; ?>,
                /*theme: 'blue',*/
                support: 36,
                width: 900,
                container: 'navbar_container'
            };
            printNavbar(navbarConf);
            //-->
        </script>
        <div id="page">
            <div id="navbar_container"></div>
            <div id="header">
                <ul id="menu">
                    <li>
                        <a href="<?php print ROOT; ?>index.php" id="btn-frontpage"></a>
                    </li>
                </ul>
            </div>
            <div id="content">
<?php
}

function design_Footer() {
?>
            </div>
            <div id="footer">
                <a href="../disclaimer.php">Felhasználási feltételek</a>
                <br/>
		printer.sch.bme.hu | © <?php print date('Y');?> <a href="http://kir-dev.sch.bme.hu">Kir-Dev</a> | design by kir-dev team
            </div>
        </div>
    </body>
    <!-- ga -->
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
        document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
        try {
            var pageTracker = _gat._getTracker("UA-9244343-7");
            pageTracker._trackPageview();
        } catch(err) {}</script>
</html>
<?php
}

function genForm($formActionPage, $divId = "newForm", $oPrinter = null) {

    $id = $model = $desc = $loc = "";

    $laser = $ink = "";
    $black = $color = $photo = "";

    $colors = "";

    if (!is_null($oPrinter)) {
        $id = (int)$oPrinter->id;
        $model = $oPrinter->model;
        $desc = $oPrinter->desc;
        $loc = $oPrinter->loc;

        $type = $oPrinter->type;
        $$type = 'checked="checked"';

        $colors = explode('|', $oPrinter->colors);
        foreach($colors as $c) {
            $$c = 'checked="checked"';
        }
    }

    if ($id != "")
        print '<tr id="' . $divId . '" style="display: none; background: #d3dadc;"><td colspan="6" style="padding-left: 30px;">';
    else
        print '<div id="' . $divId . '" style="display: none; margin-left: 30px; width: 470px; margin-bottom: 20px;">';
    ?>

<form action="<?php print SELF.'?p='.$formActionPage; ?>" method="post" onsubmit="return checkForm(this);">
        <?php if ($id != "") print '<input type="hidden" name="printer_id" value="'.$id.'" />'; ?>
    <table cellpadding="2" cellspacing="2">
        <tr>
            <td style="vertical-align: top;">Technológia, színek: </td>
            <td><?php print '<b>Technológia:</b>
                            <p><input type="radio" name="type" value="laser" id="laser'.$id.'" '.$laser.' /> <label for="laser'.$id.'">Lézer</label></p>
                            <p><input type="radio" name="type" value="ink" id="ink'.$id.'" '.$ink.' /> <label for="ink'.$id.'">Tintasugaras</label></p>';

                    print '<b>Színek:</b>
                            <p><input type="checkbox" name="colors[]" value="black" id="black'.$id.'" '.$black.' /> <label for="black'.$id.'">Fekete</label> </p>
                            <p><input type="checkbox" name="colors[]" value="color" id="color'.$id.'" '.$color.' /> <label for="color'.$id.'">Színes</label> </p>
                            <p><input type="checkbox" name="colors[]" value="photo" id="photo'.$id.'" '.$photo.' /> <label for="photo'.$id.'">Fotó</label> </p>
                            ';
                    ?>
            </td>
        </tr>
        <tr>
            <td>Modell <small><i>(Pl. HP LaserJet P1005)</i></small>: </td>
            <td><input type="text" name="model" maxlength="70" value="<?php print $model; ?>" /></td>
        </tr>
        <tr>
            <td>Megjegyzés <small><i>(Pl. kétoldalasan is)</i>:<br/>
                    <b>csak nyomtatással kapcsolatos!</b></small>
            </td>
            <td><textarea name="desc" rows="4" cols="30" ><?php print $desc; ?></textarea></td>
        </tr>
        <tr>
            <td>Szoba <small><i>(Pl. SCH819)</i>:<br/>
                    Keresésben való szerepléshez <br/>használd az SCH ill. a TTNY<br/> előtagot!</small></td>
            <td><input type="text" name="loc" maxlength="10" value="<?php print $loc; ?>" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="text-align: right;">
                <br/>
                <input type="submit" value="Mentés" />
                <input type="button" value="Mégse" onclick="collapse('<?php print $divId; ?>');" />
                    <?php
                    if ($id != "")
                        print '<input type="button" value="Törlés" onclick="delConfirm(\''.SELF.'\', '.$id.');" />';
                    ?>
            </td>
        </tr>
    </table>

</form>
    <?php
    if ($id != "") print '</td></tr>';
    else print '</div>';
}

?>
