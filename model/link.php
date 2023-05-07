<?

require_once(_BASE_PATH . "/app/database.php");

class Link
{
	private static function valid_name(&$name)
	{
		if (!isset($name)) {
			return false;
		}
		$len = strlen($name);
		if ($len < 1 || $len > 100) {
			return false;
		}
		//check for illegal characters & length
		//if (!isset($name) || !preg_match('/^[A-Za-z0-9\s]{1,100}$/i', $name)) {
		//if (!isset($name) || !preg_match('/^[A-Za-z0-9\s\-=+~\[\]{}:<>,.\/\\_!@#$%^&*()?\'\"\|]{1,100}$/i', $name)) {
		//	return false;
		//}
		return true;
	}

	private static function valid_url(&$url)
	{
		if (!isset($url)) {
			return false;
		}

		//make sure it starts with a protocol
		if (stripos($url, "://") === false) {
			$url = "https://".$url;
		}

		//check for illegal characters & length
		if (!preg_match('/^[A-Za-z0-9~_?#\/\s=,.:+%\-&]{1,300}$/i', $url)) {
			return false;
		}
		return true;
	}

	public static function add($name, $url, $gid)
	{
		global $params;
		$mysql = Database::get_database();

		if (!self::valid_name($name)) {
			throw new Exception("Invalid link name");
		}
		if (!self::valid_url($url)) {
			throw new Exception("Invalid URL");
		}
		$wsid = $params->user->wsid;
		if (!isset($gid) || !is_numeric($gid) || $gid === 0) {
			$gid = 0;
			$wsid = 0;
		}

		//check to make sure group is owned by user
		if ($gid != 0) {
			if (!$mysql->query("SELECT * FROM werblinks.groups WHERE groupid=? AND userid=?", array($gid, $params->user->get_id()))) {
				throw new Exception("Unable to get group");
			}
			if ($mysql->get_num_rows() != 1) {
				throw new Exception("Invalid group id");
			}
		}
		
		//TODO make sure workspace id is still valid
		
		//get the index position
		if (!$mysql->query("SELECT MAX(position) FROM werblinks.links WHERE groupid=? AND userid=?", array($gid, $params->user->get_id()))) {
			throw new Exception("Unable to get link order");
		}
		$idx = 0;
		if ($mysql->get_num_rows() > 0) {
			$row = $mysql->fetch_row();
			if ($row['MAX(position)'] == NULL) {
				$idx = 0;
			} else {
				$idx = $row['MAX(position)'] + 1;
			}
		}

		if (!$mysql->query("INSERT INTO werblinks.links (userid, groupid, workspaceid, position, name, url) VALUES(?, ?, ?, ?, ?, ?)", 
							array($params->user->get_id(), $gid, $wsid, $idx, $name, $url))) {
			throw new Exception("Unable to add new link ".mysql_error());
		}
		
		$lnk = array();
		$lnk['linkid'] = $mysql->get_last_id();
		$lnk['userid'] = $params->user->get_id();
		$lnk['groupid'] = $gid;
		$lnk['workspaceid'] = $wsid;
		$lnk['position'] = $idx;
		$lnk['name'] = $name;
		$lnk['url'] = $url;
		
		return array($lnk);
	}
	
	public static function delete($id)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("SELECT position,groupid FROM werblinks.links WHERE userid=? AND linkid=?", array($params->user->get_id(), $id))) {
			throw new Exception("Unable to get link to delete");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Couldn't find link to delete");
		}
		$orderrow = $mysql->fetch_row();
		$idx = $orderrow['position'];
		$gid = $orderrow['groupid'];

		//delete the link
		if (!$mysql->query("DELETE FROM werblinks.links WHERE userid=? AND linkid=?", array($params->user->get_id(), $id))) {
			throw new Exception("Unable to delete link $id");
		}
		if ($mysql->get_num_rows() == 0) {
			throw new Exception("No link to delete");
		}

		if (!$mysql->query("UPDATE werblinks.links SET position=position - 1 WHERE groupid=? AND userid=? AND position>?",
						   array($gid, $params->user->get_id(), $idx))) {
			throw new Exception("Unable to update position");
		}
	}

	public static function move_to_bucket($id)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown link id");
		}

		global $params;
		$mysql = Database::get_database();
		
		//get current link position
		if (!$mysql->query("SELECT position,groupid FROM werblinks.links WHERE linkid=? AND userid=?", array($id, $params->user->get_id()))) {
			throw new Exception("Unable to get link to move");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Couldn't find link to move");
		}
		$posrow =  $mysql->fetch_row();
		$oldidx = $posrow['position'];
		$oldgid = $posrow['groupid'];
		
		//check max position of new group to make sure pos is valid
		if (!$mysql->query("SELECT MAX(position) FROM werblinks.links WHERE workspaceid=? AND groupid=? AND userid=?", array(0, 0, $params->user->get_id()))) {
			throw new Exception("Unable to get link order");
		}
		$max = 0;
		if ($mysql->get_num_rows() > 0) {
			$maxrow = $mysql->fetch_row();
			if ($maxrow['MAX(position)'] == NULL) {
				$max = 0;
			} else {
				$max = $maxrow['MAX(position)'] + 1;
			}
		}

		//update position of old group links
		if (!$mysql->query("UPDATE werblinks.links SET position=position - 1 WHERE groupid=? AND userid=? AND position>?",
						   array($oldgid, $params->user->get_id(), $oldidx))) {
			throw new Exception("Unable to update order for old group");
		}
		
		//update link
		if (!$mysql->query("UPDATE werblinks.links SET position=?,groupid=?,workspaceid=? WHERE linkid=? AND userid=?",
						   array($max, 0, 0, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update link position");
		}
		if ($mysql->get_num_rows() == 0) {
			throw new Exception("No link to update");
		}
		return true;
	}

	public static function move($id, $gid, $pos)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown link id");
		}
		if (!isset($gid) || !is_numeric($gid) || $gid < 0) {
			throw new Exception("Unknown group id");
		}
		if (!isset($pos) || !is_numeric($pos) || $pos < 0) {
			throw new Exception("Unknown position");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		//get current link position
		if (!$mysql->query("SELECT position,groupid FROM werblinks.links WHERE linkid=? AND userid=?", array($id, $params->user->get_id()))) {
			throw new Exception("Unable to get link to move");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Couldn't find link to move");
		}
		$posrow =  $mysql->fetch_row();
		$oldidx = $posrow['position'];
		$oldgid = $posrow['groupid'];
		
		//check max position of new group to make sure pos is valid
		if (!$mysql->query("SELECT MAX(position) FROM werblinks.links WHERE groupid=? AND userid=?", array($gid, $params->user->get_id()))) {
			throw new Exception("Unable to get link order");
		}
		$max = 0;
		if ($mysql->get_num_rows() > 0) {
			$maxrow = $mysql->fetch_row();
			if ($maxrow['MAX(position)'] == NULL) {
				$max = 0;
			} else {
				$max = $maxrow['MAX(position)'] + 1;
			}
		}
		if ($pos > $max) {
			throw new Exception("New index out of bounds for group");
		}

		//update position of old group links
		if (!$mysql->query("UPDATE werblinks.links SET position=position - 1 WHERE groupid=? AND userid=? AND position>?",
						   array($oldgid, $params->user->get_id(), $oldidx))) {
			throw new Exception("Unable to update order for old group");
		}
		
		//update position of new group links
		if (!$mysql->query("UPDATE werblinks.links SET position=position + 1 WHERE groupid=? AND userid=? AND position>=?",
						   array($gid, $params->user->get_id(), $pos))) {
			throw new Exception("Unable to update order for old group");
		}

		//update link
		if (!$mysql->query("UPDATE werblinks.links SET position=?,groupid=?,workspaceid=(SELECT workspaceid FROM werblinks.groups WHERE groupid=? AND userid=?) WHERE linkid=? AND userid=?",
						   array($pos, $gid, $gid, $params->user->get_id(), $id, $params->user->get_id()))) {
			throw new Exception("Unable to update link position");
		}
		if ($mysql->get_num_rows() == 0) {
			throw new Exception("No link to update");
		}
		return true;
	}
	
	public static function update($id, $name, $url)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id");
		}
		if (!self::valid_name($name)) {
			throw new Exception("Invalid link name");
		}
		if (!self::valid_url($url)) {
			throw new Exception("Invalid URL");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE links SET name=?,url=? WHERE linkid=? AND userid=?",
						   array($name, $url, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update link information");
		}
		
		$lnk = array();
		
		$lnk['name'] = $name;
		$lnk['url'] = $url;
		$lnk['linkid'] = $id;
		
		return array($lnk);
	}
}

?>