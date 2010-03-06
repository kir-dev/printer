<?php
require_once('_cnf.php');

//sso stuff --------------------------------

$sso = new openSSO;

// login, logout actions
if(isset($_GET['action']))
{
	if($_GET['action'] == 'login') $sso->logIn();
	if($_GET['action'] == 'logout') { $sso->logOut();}
}

//if user isn't logged in go to index
if ( ! ($sso->isLogin()))
    header('Location: ' . ROOT);


//sso end --------------------------------

header('Content-type: text/html; charset=utf-8');

//set timezone (php conf error?)
date_default_timezone_set('Europe/Berlin');

//set mysql object
$oSql = new mysqli($host, $user, $pass, $db);

if (!empty($oSql->connect_error)) die('cannot connect to database');

$oSql->set_charset('utf8');

//init user from sso data
try {
	$oUser = new clsUser($sso->getUserData('user_name'), $sso->getUserData('sso_email'));
} catch (Exception $e) {
	die('cannot init user: ' . $e->getMessage());
}

//load user data from db
$oUser->loadFromDb($oSql);

if ($oUser->getNick() == '') $oUser->setNick($sso->getUserData('sso_nickname'));

$p = isset($_GET['p']) ? $_GET['p'] : '';

try {
    switch($p) {
        case 'addPrinter'	: addPrinter(); break;
        case 'modifyPrinter'	: modifyPrinter(); break;
        case 'delPrinter' 	: delPrinter(); break;
        case 'changeNick'	: changeNick(); break;
        case 'requestNewAppkey'	: requestNewAppkey(); break;
        case 'printFaq'		: printFaq(); break;
        //
        case 'alldatadel'	: allDataDel(); break;
        //
        default			: indexPage();
    }
} catch (Exception $e) {

    design_Header($sso->isLogin());

    print '
    <div class="box">
            <div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
            <div class="inner-content">
                    <h2>Hiba történt!</h2><br/>
                    Hiba oka: ' . $e->getMessage() . '<br/>
                    ' . BACK . '
            </div>
            <div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
    </div>';

    design_Footer();
}

pageUnload();


//functions

function indexPage() {

	//global
	global $oUser, $oSql, $sso;

	//welcome...
	
	design_Header($sso->isLogin());

	print '<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
			
				<h2>Felhasználói információk</h2>
				<form action="'.SELF.'?p=changeNick" method="post">
				<p><label style="display: block; float: left; text-align: right;">Nickname: </label><input type="text" name="nick" value="'.$oUser->getNick().'" maxlength="50" /> 
											<input type="submit" value="Mentés" /></p>
				<p><label style="display: block; float: left; text-align: right;">Titkos appkey: </label><span>';
				
				if ($oUser->getAppkey() == "")
					print 'Adj hozzá egy nyomtatót, hogy kapj!';
				else
					print $oUser->getAppkey().'&nbsp;&nbsp; <a href="'.SELF.'?p=requestNewAppkey">[új kérése]</a><br/>';
				
				print '
				</span></p>
				</form>
			
			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>';



		print '<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
				
				<h2>Nyomtatóim</h2>
					<p><input type="button" value="Nyomtató hozzáadása" onclick="collapse(\'newForm\');" /></p>';
					//
					genForm('addPrinter', 'newForm', null);
					//
					
					$aPrinters = $oUser->getPrinters();
					
		print ' <table style="width: 100%;">
					<tr><th>Modell</th><th>Típus</th><th>Nyomtat</th><th>Info</th><th></th><th></th></tr>';
					for ($i = 0; $i < count($aPrinters); ++$i) {
						$id = $aPrinters[$i]->id;
						
						switch ($aPrinters[$i]->type) {
							case 'laser' : $type = 'lézer'; break;
							case 'ink' : $type = 'tintasugaras'; break;
							default : $type = 'hiba!';
						}
						
						print '
						<tr>
							<td><div style="width: 150px; owerflow: hidden;">' . $aPrinters[$i]->model . '</div></td>
							<td>' . $type . '</td>
							<td>';
								$str = '';
								$colors = explode('|', $aPrinters[$i]->colors);
								for ($j = 0; $j < count($colors); ++$j) {
									switch($colors[$j]) {
										case 'black': $str .= 'fekete'; break;
										case 'color': $str .= 'színes'; break;
										case 'photo': $str .= 'fotó'; break;
										default: $str .= 'hiba!';
									}
									if ($j < count($colors) -1) $str .= ', ';
								}
								print $str.'</td>
							<td><div style="width: 370px; owerflow: hidden;">' . nl2br($aPrinters[$i]->desc) . '</div></td>
							<td><a href="#" onclick="collapse(\'printer_'.$id.'\');">Szerkesztés</a></td>
							<td><a href="#" onclick="delConfirm(\''.SELF.'\', '.$id.');">Törlés</a></td>
						</tr>';
						genForm('modifyPrinter', 'printer_'.$id, $aPrinters[$i]);
					}
		print '	</table>
				
				<br/><br/>
				<h2>Kliens</h2>

					<div style="margin-left: 10px; margin-bottom: 5px;">
						<img src="../res/img/icons/Help-circle-blue-32.png" alt="" align="left" />
						<div class="iconcaption"><a href="?p=printFaq">GY.I.K.</a></div>
					</div>
					<div style="margin-left: 10px;">
						<img src="../res/img/icons/Download-32.png" alt="" align="left" />
						<div class="iconcaption"><a href="../Kir-Dev%20Printer%20Client.msi">LETÖLTÉS (win msi)</a></div>
					</div>

			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>';

	print '
		<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
				<div style="height: 40px;">
					<img src="../res/img/icons/skull_small.png" alt="" align="left" />
					<br/>&nbsp;<a href="'.SELF.'?p=alldatadel">Minden adat törlése</a> (<b>visszavonhatatlan!</b>)
				</div>
			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>';
	design_Footer();
} 

function addPrinter() {

	//global
	global $oUser, $oSql, $sso;

        if (!isset($_POST['model'])) throw new Exception('Nem kaptam megfelelő POST adatot!');
        $data = clsPrinter::dataValidate($_POST);

        //if not registered yet: request appkey and save user
        if (is_null($oUser->getAppkey())) {
                $oUser->requestAppKey();
                $oUser->saveToDb($oSql);
        }

        $newPrinter = new clsPrinter(0, $oUser->getUid(), $data['type'], $data['colors'], $data['model'], $data['desc'], $data['loc']);
        $oUser->addPrinter($newPrinter, $oSql);

        pageUnload();

        header('Location: '.SELF); //if everything went ok
		
} 

function changeNick() {

	//global
	global $oUser, $oSql, $sso;
	

        if (!isset($_POST['nick'])) throw new Exception('Nem kaptam megfelelő POST adatot!');

        $nick = htmlspecialchars($_POST['nick']);

        if (strlen($nick) < 3 || strlen($nick) > 50) throw new Exception('túl rövid vagy túl hosszú név (min 3, max 50 kar.)');

        //set session data
        $oUser->setNick($nick);

        if ($oUser->isRegistered()) $oUser->saveToDb($oSql);

        pageUnload();

        header('Location: '.SELF);

} 

function modifyPrinter() {

	//global
	global $oUser, $oSql, $sso;
	
        if (!isset($_POST['printer_id']) || (int)$_POST['printer_id'] == 0) throw new Exception('Nem kaptam megfelelő POST adatot!');

        $id = (int)$_POST['printer_id'];

        $data = clsPrinter::dataValidate($_POST);

        $oUser->updatePrinter($oSql, $id, $data['type'], $data['colors'], $data['model'], $data['desc'], $data['loc']);

        pageUnload();

        header('Location: '.SELF);

} 

function delPrinter() {

	//global
	global $oUser, $oSql, $sso;

	if ((int)$_GET['id'] == 0 || $oUser->delPrinter((int)$_GET['id'], $oSql) === FALSE) {
		//error
		design_Header($sso->isLogin());
		
		print '
		<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
				<h2>Hiba történt!</h2><br/>
				Nem sikerült törölni a nyomtatót az adatbázisból!<br/>
				' . BACK . '
			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>';
			
		design_Footer();
	}
	
	pageUnload();
	
	header('Location: '.SELF);
} 

function allDataDel() {

	//global
	global $oUser, $oSql, $sso;

	if (!isset($_GET['sure'])) {
		design_Header($sso->isLogin());
		print '<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
			Ezzel a mûvelettel törlöd magad és a felvett nyomtatóid a Printer.SCH adatbázisából. <b>A mûvelet visszavonhatatlan. </b>
			<br/>
			Folytatod?<br/>
			<input type="button" value="Mégse" onclick="history.back();"><input type="button" value="Tovább" onclick="window.location=\''.SELF.'?p=alldatadel&amp;sure=1\';">
			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>';
		design_Footer();
		
	} else {
		$st = $oSql->prepare("DELETE FROM `printers` where `uid` = ?");
		$st->bind_param('s', $oUser->getUid());
		$st->execute();
		$st->close();
		
		$st = $oSql->prepare("DELETE FROM `users` where `uid` = ?");
		$st->bind_param('s', $oUser->getUid());
		$st->execute();
		$st->close();

                $sso->logOut();
		
		$_SESSION = array(); //unset session variables

		session_destroy(); //destroy session
		
		pageUnload();
		
		header('Location: ../index.php');
	}
}

function requestNewAppkey() {
	
	//globals
	global $oUser, $oSql, $sso;
	
	$oUser->requestAppKey();

        $oUser->saveToDb($oSql);

        pageUnload();

        header('Location: '.SELF);
}

function printFaq() {
    global $sso;

design_Header($sso->isLogin());
?>
<div class="box">
<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
<div class="inner-content">
			
<h2>Printer GYIK</h2>

<h3>Mi ez a program, miért kell feltelepítenem?</h3>
<p align="justify">
Ahhoz, hogy a weboldal ki tudja írni az aktív nyomtatóidat, valahogy jelezned kell felénk, hogy mikor tudsz nyomtatni. Ennek a legegyszerűbb módja, hogy felraksz egy kicsi, háttérben futó programot, ami megteszi ezt helyetted. Ily módon, ha be van kapcsolva a géped, a program automatikusan küld egy üzenetet nekünk, és mi tudjuk, hogy lehet nálad nyomtatni. A továbbiakban ezt fogjuk "kliens program"-nak hívni.
</p>

<h3>Felraktam a programot, de indításkor valami AppKey-t kér. Ide mit kell írni?</h3>
<p align="justify">
Amikor hozzáadtad az első nyomtatód a <a href="<?php print ROOT; ?>"><?php print ROOT; ?></a> oldalon, akkor kaptál egy AppKey-t (bejelentkezés után nézd meg a nyomtatóid listáját, azon az oldalon van). Ez egy sok betűből és számból álló kód, és a kliens ez alapján tudja megmondani, hogy ki vagy, hány/milyen nyomtatód van stb.
</p>

<h3>Elindítottam a programot, de nem jött elő semmilyen ablak!</h3>
<p align="justify">
A program alaphelyzetben egy kis nyomtató ikon a tálcán. Erre jobb egérgombbal kattintva előjön egy menü, amiben állíthatod az állapotod, megváltoztathatod az AppKey-ed, beállíthatod, hogy a program automatikusan elinduljon-e, illetve előhozhatod a státusz ablakot (ezt az ikonra duplán kattintva is megteheted).
<br/><br/>
A státusz ablakban láthatod a neved (ha más nevét látod, akkor valami hiba történt, légyszíves adj fel egy ticketet a <a href="http://support.sch.bme.hu">support.sch.bme.hu</a> -n. Láthatod az állapotod, a nyomtatóid, az AppKey-ed, és ha valami hiba történt, akkor a hiba okát. Az állapotod, AppKey-ed stb. Ezeket itt is meg tudod változtatni a megfelelő gombokra kattintva.
</p>

<h3>Az ikon mellett egy kis sárga háromszög jelent meg egy felkiáltójellel! Hiba történt!</h3>
<p align="justify">
A státusz ablakban megtudhatod a hiba okát.

<h4>Hibás AppKey!</h4>
Valószínűleg elgépelted az AppKey-edet, mikor megadtad. A legegyszerűbb, ha kimásolod a weboldalról, és beilleszted a programba. Figyelj arra, hogy az elején/végén ne legyenek szóközök!

<h4>Egyéb hiba, ami nem szerepelt itt a listában.</h4>
A kliens megpróbál ismételten kapcsolódni bizonyos időközönként. Lehetséges, hogy csak ideiglenes hálózati gond, ami pár perc múlva elmúlik. Ha mégis úgy gondolod/a hibából arra következtetsz, hogy a hiba a mi oldalunkon van, akkor adj fel egy ticketet a <a href="http://support.sch.bme.hu">support.sch.bme.hu</a> -n.
</p>

<h3>Nem jelent meg a kis nyomtató ikon, vagy hirtelen eltűnt!</h3>
<p align="justify">
Adj fel egy ticketet a <a href="http://support.sch.bme.hu">support.sch.bme.hu</a> -n, amihez csatold a hibanaplód is!
<ul>
<li>Win2000/XP alatt:<br/>
<pre>C:\Documents and Settings\[Userneved]\Application Data\Kir-Dev\PrinterClientError.log</pre></li>

<li>Vista/Win7 alatt:<br/>
<pre>C:\Users\[Userneved]\AppData\Roaming\Kir-Dev\PrinterClientError.log</pre></li>

<!--li>Linux alatt:</li-->
</ul>
</p>

<h3>Más is megtudta az AppKey-emet, ez most baj nekem?</h3>
<p align="justify">
Ha más is megtudta az AppKey-edet, akkor ők is át tudják állítani az elérhető/elfoglalt állapotod. Ha nem zavar, ha hajnali 3-kor kopognak az ajtódon, hogy nyomtatni szeretnének, akkor nincs semmi baj. Ha mégis, akkor célszerű újat kérni.
<br/><br/>
Kattints az "Új AppKey generálása" gombra a <a href="<?php print ROOT; ?>"><?php print ROOT; ?></a> oldalon, bejelentkezés után. Ne felejtsd el a kliensben is átírni: jobb-klikk menüből az "Új AppKey" menüponttal vagy a státusz ablakban az AppKey melletti "Módosítás" gombbal.
<br/><br/>
<h3>Mi van, ha a gépem be van kapcsolva, de most mégsem szeretnék nyomtatni?</h3>
<p align="justify">
A jobb-klikk menüben a "Váltás elfoglalt állapotba"-ra kattintasz
<br/><br/>
VAGY
A státusz ablakban az "Elérhető" gombra kattintasz
<br/><br/>
Természetesen elfoglalt módból vissza lehet váltani elérhetőbe, a jobb-klikk menüben a "Váltás elérhető állapotba" menüponttal, vagy a státusz ablakban az "Elfoglalt" gombra kattintva.
</p>

<h3>Több nyomtatóm van, de az egyikből kifogyott a patron/ellopták a fotópapírt/kijött belőle a füst és most már nem működik. Törölnöm kell a listából?</h3>
<p align="justify">
Ha több nyomtatód van, akkor a kliens engedi, hogy a nyomtatók állapotát külön-külön is beállítsd, megteheted, hogy az egyik nyomtatód elérhető állapotban hagyod, míg a másikat nem. Ezt a zöld/piros kis karika jelzi a nyomtató mellett mind a jobb-klikk menüben, mind a státusz ablakban.
</p>

<h3>Elfoglalt vagyok, de a nyomtatóim nem váltottak át pirosba!</h3>
<p align="justify">
Ne aggódj, ha elfoglalt vagy, egyik nyomtatód sem látszik. A kliensben azért marad zöld nyomtató ikonja, hogy ha visszaváltasz elérhető állapotba, akkor látszódjon, melyik nyomtató fog látszani és melyik nem.
</p>

<h3>Kiírta, hogy kiadtak egy új verziót a programból, és szeretném-e letölteni, de bármit válaszolok, kilép!</h3>
<p align="justify">
Ha új verzió jelenik meg a kliensből, annak általában oka van (pl. biztonsági frissítések). Épp ezért a régebbi verziójú programok nem fognak elindulni, ha látják, hogy létezik újabb is (akár frissíted, akár nem).
</p>
<div style="text-align: center"><?php print BACK; ?></div>
</div>
<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
</div>
<?php
design_Footer();
}

function pageUnload() {

	global $oUser, $oSql, $sso;
	unset( $oUser );
        unset($sso);
	$oSql->close();
}

?>
