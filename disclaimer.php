<?php
        require_once('logger.php');

	session_start();

        require_once 'db.php'; //kell az SSO lib miatt
        require_once(SSO_CLASS);
	require_once('printprofil/funct.php');

	header('Content-type: text/html; charset=utf-8');

        //set timezone (php conf error?)
        date_default_timezone_set('Europe/Berlin');

        $sso = new openSSO; //kell, hogy konzisztens maradjon a bejelentkezés tényének kijelzése

	design_Header($sso->isLogin());
?>
		
		<div class="box">
			<div class="top"><div class="right-corner"></div><div class="left-corner"></div></div>
			<div class="inner-content">
				
				<h2>Printer.SCH<br/>
				Felhasználási feltételek</h2>

<p>A Kollégiumi Információs Rendszer fejlesztõcsapata <i>(továbbiakban Kir-Dev)</i> nem vállal felelősséget az oldalon, illetve a felhasználók oldalain megjelenő információk, adatok valódiságáért, és a bennük elhelyezett tartalomért.</p>

<p>A weblapunk használata a hallgatók számára ingyenes. Weblapunkat használó személy(ek) <i>(továbbiakban Felhasználó)</i> automatikusan elfogadják jelen dokumentum tartalmát.</p>

<p>A Kir-Dev nem felel a linkekben előforduló márkanevek, védjegyek használatáért, valamint azért, ha a máshol készült tartalmi szolgáltatás sérti bárki hitét, ízlését, meggyőződését.</p>

<p>A felhasználó vállalja a felelősséget az általa feltöltött ÖSSZES adatért.</p>

<p>A Felhasználó által publikált adatok tartalmáért, az azokból eredő károkért a Kir-Dev -et nem terheli felelősség.</p>

<!--<p>Amennyiben törölni szeretnéd magad a rendszerünkből: Lépj be, majd kattints a <i>&quot;Minden adat törlése&quot;</i> linkre.</p>-->

<p>A Kir-Dev fenntartja a jogot, hogy bármikor részben vagy egészében -saját meglátása szerint-, megváltoztassa, módosítsa a szabályzatot.</p>

<p>A Kir-Dev nem vállal felelősséget a szolgáltatás teljes egészére vagy csak egy részére vonatkozó - akár előzetes értesítés nélküli szüneteltetéséből, illetve a teljes leállításából eredő károkért.</p>

<p>A Kir-Dev nagyfokú gondossággal jár el a Felhasználó adatainak megőrzése és védelme érdekében, azonban semmiféle felelősség nem terheli azok esetleges részbeni vagy teljes elvesztéséért.</p>

<p>Felhasználó felelőssége továbbá jelszava megjegyzése és titokban tartása, valamint az, hogy az általa megadott email cím, amelyre a Kir-Dev rendszerüzeneteket küldhet, valódi, működő cím legyen, és olyan, amelyet Felhasználó kizárólagosan ellenőriz.</p>

<p>A rendszer működésére hatással lehetnek a Kir-Dev által nem befolyásolható külső tényezők. Kir-Dev semmilyen felelősséget nem vállal a felhasználók által közölt adatokért illetve ajánlatokért. Kir-Dev nem vesz részt a weblap segítségével létrejött megrendelések teljesítésére kötött megállapodásokban, ezért Kir-Dev mentes mindennemű felelősségtől és kárigénytől a felhasználókkal szemben, beleértve a bizonyított, feltételezett, vélt ill. valós károkat és követeléseket. A felelősség kizárása kiterjed az ajánlatkérésekkel, az ajánlatok megtételével és elfogadásával, a megállapodások megkötésével illetve teljesítésével kapcsolatos esetleges igényekre is. Kir-Dev nem tartozik felelősséggel semmiféle egyéb károkért és veszteségekért (például amelyek valakinek a jó hírét érintik).</p>

<p>Budapest, 2009.09.03</p>

<p>Kir-Dev team</p>
<!--<br/><br/>
A Kir-Dev kijelenti, hogy a honlapon megjelenő grafikák, szövegek, képek, információk és egyéb anyagok, illetve azok elrendezése szerzői jogi oltalom alatt állnak, azok felhasználására és felhasználási jogának átruházása kizárólag a Kir-Dev jogosult. A szerzői jogi oltalommal védett anyagok jogosulatlan felhasználása jogszabályba ütközik.
<br/><br/>
A Kir-Dev előzetes írásbeli engedélye nélkül tilos a weboldalak tartalmának egészét vagy részeit bármilyen formában felhasználni, reprodukálni, átruházni, terjeszteni, átdolgozni, vagy tárolni. Kir-Dev azonban beleegyezik abba, hogy saját, személyes használatra, a szabad felhasználás körében, Felhasználó ezen oldalak tartalmát, vagy kivonatait számítógépén tárolja vagy kinyomtassa.-->
				
			</div>
			<div class="bottom"><div class="right-corner"></div><div class="left-corner"></div></div>
		</div>		
<?php
	design_Footer();
?>
