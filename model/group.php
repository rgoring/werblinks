<?

require_once(_BASE_PATH . "/app/database.php");

class Group
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

	public static function add($name)
	{
		if (!self::valid_name($name)) {
			throw new Exception("Invalid group name");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		$coord = 40;
		
		//TODO make sure workspace id is still valid
		
		//get a 'unique' coordinate so groups don't overlay each other
		if ($mysql->query("SELECT MAX(groupid) FROM werblinks.groups WHERE userid=?", array($params->user->get_id()))) {
			if ($mysql->get_num_rows() > 0) {
				$row = $mysql->fetch_row();
				if ($row['MAX(groupid)'] != NULL) {
					$coord = 40 + (($row['MAX(groupid)'] % 50) * 10);
				}
			}
		}
		
		if (!$mysql->query("INSERT INTO werblinks.groups (userid, workspaceid, xpos, ypos, width, height, name) VALUES(?, ?, ?, ?, ?, ?, ?)", 
							array($params->user->get_id(), $params->user->wsid, $coord, $coord, 150, 150, $name))) {
			throw new Exception("Unable to add new link");
		}
		
		$grp = array();
		$grp['userid'] = $params->user->get_id();
		$grp['groupid'] = $mysql->get_last_id();
		$grp['workspaceid'] = $params->user->wsid;
		$grp['xpos'] = $coord;
		$grp['ypos'] = $coord;
		$grp['width'] = 150;
		$grp['height'] = 150;
		$grp['name'] = $name;

		return array($grp);
	}
	
	public static function delete($id)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id: $id");
		}
		
		global $params;
		$mysql = Database::get_database();

		//delete the group
		if (!$mysql->query("DELETE FROM werblinks.groups WHERE userid=? AND groupid=?", array($params->user->get_id(), $id))) {
			throw new Exception("Unable to delete group $id");
		}
		if ($mysql->get_num_rows() == 0) {
			throw new Exception("No group to delete");
		}
		
		//remove associated links
		if (!$mysql->query("DELETE FROM werblinks.links WHERE userid=? AND groupid=?", array($params->user->get_id(), $id))) {
			throw new Exception("Unable to delete links for group $id");
		}
	}
	
	public static function move($id, $x, $y)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id");
		}
		if (!isset($x) || !is_numeric($x) || $x <= 0) {
			throw new Exception("Unknown x position");
		}
		if (!isset($y) || !is_numeric($y) || $y <= 0) {
			throw new Exception("Unknown y position");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.groups SET xpos=?,ypos=? WHERE groupid=? AND userid=?",
						   array($x, $y, $id, $params->user->get_id()))) {
			throw new Exception("Unable to change position");
		}
		
		return true;
	}
	
	public static function resize($id, $w, $h)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id");
		}
		if (!isset($w) || !is_numeric($w) || $w <= 0) {
			throw new Exception("Unknown width");
		}
		if (!isset($h) || !is_numeric($h) || $h <= 0) {
			throw new Exception("Unknown height");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.groups SET width=?,height=? WHERE groupid=? AND userid=?",
						   array($w, $h, $id, $params->user->get_id()))) {
			throw new Exception("Unable to change size");
		}
		
		return true;
	}

	public static function update($id, $name)
	{
		if (!isset($id) || !is_numeric($id) || $id <= 0) {
			throw new Exception("Unknown group id");
		}
		if (!self::valid_name($name)) {
			throw new Exception("Invalid group name");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("UPDATE werblinks.groups SET name=? WHERE groupid=? AND userid=?",
						   array($name, $id, $params->user->get_id()))) {
			throw new Exception("Unable to update group information");
		}
		
		$grp = array();
		
		$grp['name'] = $name;
		$grp['groupid'] = $id;
		
		return array($grp);
	}

	public static function move_to_workspace_name($groupid, $wsname)
	{
		global $params;

		// get the new workspace id
		$wsid = Workspace::get_wsid_from_name($wsname);

		if ($params->user->wsid == $wsid) {
			// no need to update the database, they selected the same workspace
			$grp = array();
			$grp['id'] = $groupid;
			$grp['nomove'] = 1;
			return array($grp);
		}

		$mysql = Database::get_database();

		// change the group workspace id to the new workspace id
		$mysql->query("UPDATE werblinks.groups SET workspaceid=? WHERE groupid=? AND userid=?", array($wsid, $groupid, $params->user->get_id()));
		if (!$mysql->get_result()) {
			throw new Exception("Unable to update group workspace");
		}

		// change all links for the current group id + workspace id to the new workspace id
		$mysql->query("UPDATE werblinks.links SET workspaceid=? WHERE groupid=? AND userid=?", array($wsid, $groupid, $params->user->get_id()));
		if (!$mysql->get_result()) {
			throw new Exception("Unable to update links for group workspace");
		}

		$grp = array();
		$grp['newws'] = $wsname;
		$grp['id'] = $groupid;

		return array($grp);
	}

	public static function sticky($id)
	{
		if (!isset($id) || !is_numeric($id) || $id < 0) {
			throw new Exception("Unknown group id");
		}
		
		global $params;
		$mysql = Database::get_database();

		//toggle sticky
		$mysql->query("UPDATE werblinks.groups SET workspaceid=IF(workspaceid=0, ?, 0) WHERE groupid=? AND userid=?", array($params->user->wsid, $id, $params->user->get_id()));
		if (!$mysql->get_result()) {
			throw new Exception("Unable to update group workspace");
		}
		
		//toggle sticky
		$mysql->query("UPDATE werblinks.links SET workspaceid=IF(workspaceid=0, ?, 0) WHERE groupid=? AND userid=?", array($params->user->wsid, $id, $params->user->get_id()));
		if (!$mysql->get_result()) {
			throw new Exception("Unable to update links for group workspace");
		}
		
		return true;
	}
}

?>