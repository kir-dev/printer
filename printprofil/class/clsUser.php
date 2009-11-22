<?php

final class clsUser {

	//user preferences
	private $uid;
	private $email;
	private $nick = "";
	private $appkey = "";
	private $inDb = FALSE;
	
	//user's printers
	private $aPrinters = array();
	
	/**
	 * PHP5 constructor
	 * new obj with sso data
	 */
	function __construct($uid = "", $email = "") {
		//load data from sso
		if ($uid == "" || $email == "") throw new Exception("Az uid és az email mezõ üres! Érvénytelen adatok az sso-tól!");
		$this->uid = $uid;
		$this->email = $email;
	}
	
	public function loadFromDb(&$oSql) {
		$st = $oSql->prepare("SELECT `nick`,`appkey` FROM `users` WHERE `uid` = ?");
		$st->bind_param('s', $this->uid);
		$st->execute();
		
		//we don't have to check uid duplicates (uid is primary key)
		$st->bind_result($nick, $appkey);

		while ( $st->fetch() ) { //run once, if in db (else (s)he can't have printers)
			//user already in db - load appkey and printers
			//don't load email again to avoid inconsistent data (maybe loaded newest from sso; example new email address)
			$this->inDb = TRUE;
			$this->appkey = $appkey;
			$this->nick = $nick;
		}
		$st->close();

		if ($this->inDb) {
			$this->aPrinters = array();

			$stPr = $oSql->prepare("SELECT `id`, `type`, `colors`, `model`, `desc`, `loc` FROM `printers` WHERE `uid` = ?");
			$stPr->bind_param('s', $this->uid);
			$stPr->execute();
			
			$stPr->bind_result($id, $type, $colors, $model, $desc, $loc);
			
			//load printers
			while ( $stPr->fetch() ) $this->aPrinters[] = new clsPrinter($id, $this->uid, $type, $colors, $model, $desc, $loc);
			$stPr->close();
		}
	}
	
	
	//@TODO: updateFields[] parameter for update sql query -> if null all fields update, else: only in array...
	public function saveToDb(&$oSql) {

		if ($this->inDb) {	//already in db - update
			$stUpd = $oSql->prepare("UPDATE `users` SET `email` = ?, `nick` = ?, `appkey` = ? WHERE `uid` = ?");
			$stUpd->bind_param('ssss', $this->email, $this->nick, $this->appkey, $this->uid);
			$stUpd->execute();
			$stUpd->close();
		} else { //insert
			$stUserIns = $oSql->prepare("INSERT INTO `users` (`uid`, `email`, `nick`, `appkey`) VALUES(?, ?, ?, ?)");
			$stUserIns->bind_param('ssss', $this->uid, $this->email, $this->nick, $this->appkey);
			$stUserIns->execute();
			if ($stUserIns->affected_rows != 1) throw new Exception("Adatbázishiba! A felhasználó mentése sikertelen!");

			$stUserIns->close();
		}

		$this->inDb = TRUE;
	}
	
	private function getPrinterIndexFromId($iPrinterId) {
		//search printer by id
		$i = 0;
		while ($i < count($this->aPrinters) && $this->aPrinters[$i]->id != $iPrinterId ) ++$i;
		if ($i == count($this->aPrinters)) throw new Exception('nincs ilyen nyomtatód');
		
		return $i;
	}
	
	public function addPrinter($oPrinter, &$oSql) {
		$oPrinter->saveToDb($oSql);
		$this->aPrinters[] = $oPrinter;
	}
	
	public function delPrinter($iPrinterId, &$oSql) {

		$index = $this->getPrinterIndexFromId($iPrinterId);

		$success = $this->aPrinters[$index]->delFromDb($oSql);

		unset($this->aPrinters[$index]);

		return $success;
	}
	
	public function updatePrinter(&$oSql, $iPrinterId, $sType, $sColors, $sModel, $sDesc, $sLoc) {
		$index = $this->getPrinterIndexFromId($iPrinterId);
		
		$this->aPrinters[$index]->updateData($sType, $sColors, $sModel, $sDesc, $sLoc);

		$this->aPrinters[$index]->saveToDb(&$oSql);
	}

	public function isRegistered() { return $this->inDb; }
	public function getUid() { return $this->uid; }
	public function getNick() { return $this->nick; }
	public function setNick($nick) { $this->nick = $nick; }
	public function getAppkey() { return $this->appkey; }
	public function getPrinters() { return $this->aPrinters; }
	
	private function genAppkey() {
		//super secret, super random appkey algorithm
		//calculate md5( random_order(uid, timestamp, random number) )
		$data = array($this->uid, time(), mt_rand(1, 100), $_SERVER['UNIQUE_ID']);
		shuffle($data);
		$str = '';
		while (count($data)) {
			$str .= array_shift($data);
		}
		return md5($str);
	}
	
	public function requestAppKey() {
		$this->appkey = $this->genAppKey();
		return $this->appkey;
	}

	function __destruct() {
		//free printers
		for($i = 0; $i < count($this->aPrinters); ++$i) unset($this->aPrinters[$i]);
	}
}

?>