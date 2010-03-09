<?php
if (substr($_SERVER["SCRIPT_NAME"], -10) != '/index.php') die('Hiba: a fájl direkt hívása nem engedélyezett');

print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>printer.sch.bme.hu</title>
        <link type="text/css" href="res/css/style.css" rel="stylesheet" />
        <!-- navbar script include -->
	<script type="text/javascript">
		var NavBarProtocol = ((document.location.protocol == "https:") ? "https" : "http");
		document.write(unescape("%3Cscript src='" + NavBarProtocol + "://idp.sch.bme.hu/navbar/vir-navbar3.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
        <meta http-equiv="refresh" content="180" />
    </head>
    <body>
        <!-- navbar config and call -->
        <script type="text/javascript">
            //<!--
            var navbarConf = {
                loginLink: 'https://printer.sch.bme.hu/printprofil/?action=login',
                logoutLink: 'https://printer.sch.bme.hu/printprofil/?action=logout',
                loggedIn: <?php print (int)$loggedIn; ?>,
                support: 36,
                theme: 'grey',
                width: 900,
                container: 'navbar_container'
            };
            printNavbar(navbarConf);
            //-->
        </script>
        <div id="page">
            <div id="navbar_container"></div>
            <div id="header">
                <?php
                if ($loggedIn)
                    print '<ul id="menu">
                                <li>
                                    <a href="printprofil/index.php" id="btn-myprinters"></a>
                                </li>
                           </ul>';
                ?>
            </div>
            <div id="content">

                <div class="box">
                    <div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
                    <div class="inner-content">

                        <h2>Aktív nyomtatók</h2>
                        <p>
					@<?php print date('Y.m.d. H:i'); ?>
                        </p>
                        <table style="width: 100%;">
                            <tr><th>Név</th><th>Szoba</th><th>Nyomtató</th><th>Info</th><th>Frissítve</th></tr>

                            <?php
                            foreach ($printers as $printer) {

                                print '
                                    <tr>
                                        <td><a href="https://profile.sch.bme.hu/profile/show/uid/' . $printer['uid'] . '/">' . $printer['nick'] . '</a></td>
                                        <td>' . $printer['loc'] . '</td>
                                        <td><div style="width: 150px; overflow: hidden;">' . $printer['model'] . '</div><br /><i>Típus</i>: ' . $type . '<br /><i>Nyomtat</i>: ';
                                        print $printer['colors'].'
                                        </td>
                                        <td><div style="width: 400px; overflow: hidden;">' . $printer['desc'] . '</div></td>
                                        <td>' . $printer['last_refreshed'] . '</td>
                                    </tr>';
                            }
                            ?>
                        </table>

                    </div>
                    <div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
                </div>

                <div class="box">
                    <div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
                    <div class="inner-content">

                        <h2>Inaktív nyomtatók</h2>
                        <b>Figyelem: </b> ez a lista azokat a nyomtatókat tartalmazza, amik a rendszerben vannak (és az elmúlt 3 hónapban aktívak voltak), de <b><u>most nem lehet náluk nyomtatni</u></b>!
                        <br/><br/>
                        <table style="width: 100%;">
                            <tr><th>Név</th><th>Szoba</th><th>Nyomtató</th><th>Info</th><th>Frissítve</th></tr>

                            <?php
                            foreach ($offline_printers as $printer) {

                                print '
                                <tr style="color: grey">
                                    <td><a href="https://profile.sch.bme.hu/profile/show/uid/' . $printer['uid'] . '/">' . $printer['nick'] . '</a></td>
                                    <td>' . $printer['loc'] . '</td>
                                    <td><div style="width: 150px; overflow: hidden;">' . $printer['model'] . '</div><br /><i>Típus</i>: ' . $type . '<br /><i>Nyomtat</i>: ';
                                    print $printer['colors'].'
                                    </td>
                                    <td><div style="width: 400px; overflow: hidden;">' . $printer['desc'] . '</div></td>
                                    <td>' . $printer['last_refreshed'] . '</td>
                                </tr>';
                            }
                            ?>
                        </table>

                    </div>
                    <div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
                </div>

            </div>
            <div id="footer">
                <div style="float: left; width: 180px;"><a href="http://simonyi.bme.hu"><img src="res/img/banners/simonyi.jpg" alt="Simonyi Károly Szakkollégium" border="0" height="50" /></a></div>
                <div style="text-align: center; width: 509px; float: left;">
                    <a href="disclaimer.php">Felhasználási feltételek</a>
                    <br/>
                    printer.sch.bme.hu | © <?php print date('Y');?> <a href="http://kir-dev.sch.bme.hu">Kir-Dev</a> | design by kir-dev team
                </div>
                <div style="float: right; margin-right: 8px; margin-top: -35px;">
                    <a href="http://www.sch.bme.hu"><img src="res/img/banners/schonherz.jpg" alt="Schönherz Zoltán Kollégium" border="0" height="50" /></a> 
                    <a href="http://kir-dev.sch.bme.hu"><img src="res/img/banners/kirdev.png" alt="Kir-Dev web developer team" border="0" height="50" /></a>
                </div>
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
