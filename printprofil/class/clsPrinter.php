<?php

final class clsPrinter {

	public static $TYPES = array('laser', 'ink');
	public static $COLORS = array('black', 'color', 'photo');
	public $id;
	public $uid;
	public $type;
	public $colors;
	public $model;
	public $desc;
	public $loc;
	public $on;
	public $last_refreshed;

	function __construct($id = 0, $uid = "", $type = "", $colors = "", $model = "", $desc = "", $loc = "") {
		$this->id = $id;
		$this->uid = $uid;
		$this->type = $type;
		$this->colors = $colors;
		$this->model = $model;
		$this->desc = $desc;
		$this->loc = $loc;
	}
	
	public static function dataValidate($post) {
		$type = isset($_POST['type']) ? $_POST['type'] : 'laser';
		$colors = isset($_POST['colors']) ? $_POST['colors'] : array('black');
		$model = htmlspecialchars($post['model'], ENT_QUOTES, 'UTF-8');
		$desc = htmlspecialchars($post['desc'], ENT_QUOTES, 'UTF-8');
		$loc = htmlspecialchars($post['loc'], ENT_QUOTES, 'UTF-8');
		
		//check type
		if (!in_array($type, clsPrinter::$TYPES)) throw new Exception('nincs ilyen típus: '.$type);
		
		//colors validate
			$sColors = "";
			$i = 0;
			foreach($colors as $c) {
				if (!in_array($c, clsPrinter::$COLORS)) throw new Exception('nincs ilyen szín: '.$c);
				$sColors .= $c;
				if ($i < count($colors)-1) $sColors .= '|';
				++$i;
			}
		//colors end
		
		if (strlen($model) < 3) throw new Exception('túl rövid típus (min 3 kar.)');
		if (strlen($desc) > 500) $desc = substr($desc, 0, 500);
		if (strlen($loc) < 3) throw new Exception('túl rövid szobakód (min 3 kar.)');
		
		return array('type' => $type, 'colors' => $sColors, 'model' => $model, 'desc' => $desc, 'loc' => $loc);
	}
	
	
	public function saveToDb(&$oSql) {
		if ($this->id === 0) {
			$st = $oSql->prepare("INSERT INTO `printers`(`uid`, `type`, `colors`, `model`, `desc`, `loc`) VALUES(?, ?, ?, ?, ?, ?)");
			$st->bind_param('ssssss', $this->uid, $this->type, $this->colors, $this->model, $this->desc, $this->loc);
			$st->execute();
			if ($st->affected_rows != 1) throw new Exception('Adatbázishiba! Nyomtató mentése sikertelen!');
			$st->close();
			$this->id = $oSql->insert_id;
		} else {
			$st = $oSql->prepare("UPDATE `printers` SET `type` = ?, `colors` = ?, `model` = ?, `desc` = ?, `loc` = ? WHERE `id` = ?");
			$st->bind_param('sssssi', $this->type, $this->colors, $this->model, $this->desc, $this->loc, $this->id);
			$st->execute();
			$st->close();
		}
		
		return $this->id;
	}
	
	public function updateData($sType, $sColors, $sModel, $sDesc, $sLoc) {
		$this->type = $sType;
		$this->colors = $sColors;
		$this->model = $sModel;
		$this->desc = $sDesc;
		$this->loc = $sLoc;
	}
	
	public function delFromDb(&$oSql) {
		$st = $oSql->prepare("DELETE FROM `printers` WHERE `id` = ? AND `uid` = ?");
		$st->bind_param('is', $this->id, $this->uid );
		$st->execute();
		$success = $st->affected_rows==1 ? TRUE : FALSE;
		$st->close();
		
		return $success;
	}
}

?>