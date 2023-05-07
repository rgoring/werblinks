<?

require_once(_BASE_PATH . "/app/database.php");

class Hobject
{
	private static function valid_name($name)
	{
		//check for illegal characters & length
		//if (!isset($name) || !preg_match('/^[A-Za-z0-9\s]{1,100}$/i', $name)) {
		if (!isset($name) || !preg_match('/^[A-Za-z0-9\s\-=+~\[\]{}:<>,.\/\\_!@#$%^&*()?\'\"]{1,100}$/i', $name)) {
			return false;
		}
		return true;
	}

	public static function move($id, $x, $y)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown html obj id");
		}
		if (!isset($x) || !is_numeric($x) || $x <= 0) {
			throw new Exception("Unknown x position");
		}
		if (!isset($y) || !is_numeric($y) || $y <= 0) {
			throw new Exception("Unknown y position");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.htmlobjs SET xpos=?,ypos=? WHERE htmlid=? AND userid=?",
						   array($x, $y, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update html object position");
		}
		
		return true;
	}
	
	public static function resize($id, $w, $h)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown html obj id");
		}
		if (!isset($w) || !is_numeric($w) || $w <= 0) {
			throw new Exception("Unknown width");
		}
		if (!isset($h) || !is_numeric($h) || $h <= 0) {
			throw new Exception("Unknown height");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.htmlobjs SET width=?,height=? WHERE htmlid=? AND userid=?",
						   array($w, $h, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update html object size");
		}
		
		return true;
	}
	
	public static function add($name, $code)
	{
		if (!self::valid_name($name)) {
			throw new Exception("Invalid html object name");
		}
		if (!isset($code)) {
			throw new Exception("Invalid code for HTML object");
		}

		global $params;
		$mysql = Database::get_database();
		
		$coord = 40;
		
		//TODO make sure workspace id is still valid
		
		//get a 'unique' coordinate so groups don't overlay each other
		if ($mysql->query("SELECT MAX(htmlid) FROM werblinks.htmlobjs WHERE userid=?", array($params->user->get_id()))) {
			if ($mysql->get_num_rows() > 0) {
				$row = $mysql->fetch_row();
				if ($row['MAX(htmlid)'] != NULL) {
					$coord = 40 + (($row['MAX(htmlid)'] % 50) * 10);
				}
			}
		}
		
		if (!$mysql->query("INSERT INTO werblinks.htmlobjs (userid, workspaceid, xpos, ypos, width, height, name, html) VALUES(?, ?, ?, ?, ?, ?, ?, ?)", 
							array($params->user->get_id(), $params->user->wsid, $coord, $coord, 150, 150, $name, $code))) {
			throw new Exception("Unable to add new html object");
		}
		
		$obj = array();
		$obj['userid'] = $params->user->get_id();
		$obj['htmlid'] = mysql_insert_id();
		$obj['workspaceid'] = $params->user->wsid;
		$obj['xpos'] = $coord;
		$obj['ypos'] = $coord;
		$obj['width'] = 150;
		$obj['height'] = 150;
		$obj['name'] = $name;
		$obj['html'] = $code;

		return array($obj);
	}
	
	public static function update($id, $name, $code)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown HTML object id");
		}
		
		if (!self::valid_name($name)) {
			throw new Exception("Invalid HTML object name");
		}
		
		if (!isset($code)) {
			throw new Exception("Invalid HTML object code");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.htmlobjs SET name=?,html=? WHERE htmlid=? AND userid=?",
						   array($name, $code, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update html object info");
		}
		
		$obj = array();
		$obj['name'] = $name;
		$obj['html'] = $code;
		$obj['htmlid'] = $id;
		
		return array($obj);
	}
	
	public static function delete($id)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown HTML object id");
		}
		
		global $params;
		$mysql = Database::get_database();

		//delete the object
		if (!$mysql->query("DELETE FROM werblinks.htmlobjs WHERE userid=? AND htmlid=?", array($params->user->get_id(), $id))) {
			throw new Exception("Unable to delete HTML object");
		}

		return true;
	}
	
	public static function sticky($id)
	{
		if (!isset($id) || !is_numeric($id) || $id < 0) {
			throw new Exception("Unknown HTML object id");
		}
		
		global $params;
		$mysql = Database::get_database();

		//toggle sticky
		$mysql->query("UPDATE werblinks.htmlobjs SET workspaceid=IF(workspaceid=0, ?, 0) WHERE htmlid=? AND userid=?", array($params->user->wsid, $id, $params->user->get_id()));
		if (!$mysql->get_result()) {
			throw new Exception("Unable to update HTML sticky");
		}
		
		return true;
	}

}

?>