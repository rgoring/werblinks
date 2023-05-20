<?

require_once(_BASE_PATH . "/app/database.php");

class Workspace
{
	public static function get_workspaces()
	{
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=?", array($params->user->get_id()))) {
			throw new Exception("Unable to select workspace objects");
		}
		
		return $mysql->fetch_all_rows();
	}
	
	public static function get_groups()
	{
		global $params;
		$mysql = Database::get_database();
		
		// "groups" is now a reserved keyword in mysql 8, so needs to be escaped with backticks or identified with table prefix
		if (!$mysql->query("SELECT * FROM werblinks.groups WHERE userid=? AND (workspaceid=? OR workspaceid='0')", array($params->user->get_id(), $params->user->wsid))) {
			throw new Exception("Unable to select group objects");
		}

		return $mysql->fetch_all_rows();
	}
	
	public static function get_links()
	{
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("SELECT * FROM werblinks.links WHERE userid=? AND (workspaceid=? OR workspaceid='0') ORDER BY position", array($params->user->get_id(), $params->user->wsid))) {
			throw new Exception("Unable to select link objects");
		}

		return $mysql->fetch_all_rows();
	}
	
	public static function getRenderedHTML($path)
	{
		ob_start();
		include($path);
		$var=ob_get_contents(); 
		ob_end_clean();
		return $var;
	}
	
	public static function get_htmlobjs()
	{
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("SELECT * FROM werblinks.htmlobjs WHERE userid=? AND (workspaceid=? OR workspaceid='0')", array($params->user->get_id(), $params->user->wsid))) {
			throw new Exception("Unable to select html objects");
		}

		return $mysql->fetch_all_rows();
//		$rows = $mysql->fetch_all_rows();
//		foreach ($rows as &$row) {
//			if ($row['script'] == '1') {
//				$row['html'] = Workspace::getRenderedHTML($row['html']);
//				$row['html'] = preg_replace('/document.write\("(.+)"\);/', "$1", $row['html']);
//			}
//		}
//		return $rows;
	}

	public static function get_wsid_from_name($wsname)
	{
		global $params;
		$mysql = Database::get_database();
		
		if (!$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? AND name=?", array($params->user->get_id(), $wsname))) {
			throw new Exception("Unable to select html objects");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Unable to select workspace $wsname");
		}
		
		$row = $mysql->fetch_row();
		return $row['workspaceid'];
	}

	public static function change($ws)
	{
		if (!isset($ws) || $ws == "") {
			throw new Exception("Unspecified workspace");
		}

		global $params;
		$mysql = Database::get_database();

		if (!$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? AND name=?", array($params->user->get_id(), $ws))) {
			throw new Exception("Unable to select html objects");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Unable to select workspace $ws");
		}
		
		$row = $mysql->fetch_row();
		$params->user->wsid = $row['workspaceid'];
		$params->user->wsname = $row['name'];

		$params->user->save_to_cookie();

		return true;
	}
	
	private static function valid_wsname($name)
	{
		if (!isset($name) || $name == "") {
			return false;
		}
		
		if (!preg_match('/^[A-Za-z0-9_\-]{1,50}$/i', $name)) {
			return false;
		}

		return true;
	}
		
	public static function addws($name)
	{
		if (!self::valid_wsname($name)) {
			throw new Exception("Invalid workspace name. Only: numbers, letters, underscore, dashes");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		//no duplicate names
		if (!$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? AND name=?", array($params->user->get_id(), $name))) {
			throw new Exception("Unable to duplicate check new workspace");
		}
		if ($mysql->get_num_rows() > 0) {
			throw new Exception("A workspace with that name already exists");
		}
		
		//add the workspace
		if (!$mysql->query("INSERT INTO werblinks.workspaces (userid, name) VALUES(?, ?)", array($params->user->get_id(), $name))) {
			throw new Exception("Unable to add new workspace");
		}
		
		$ws = array();
		$ws['name'] = $name;
		$ws['workspaceid'] = $mysql->get_last_id();

		return array($ws);
	}

	public static function renamews($oldname, $newname)
	{
		if (!self::valid_wsname($newname)) {
			throw new Exception("Invalid workspace name. Only: numbers, letters, underscore, dashes");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		//no duplicate names
		if (!$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? AND name=?", array($params->user->get_id(), $newname))) {
			throw new Exception("Unable to duplicate check workspace name");
		}
		if ($mysql->get_num_rows() > 0) {
			throw new Exception("A workspace with that name already exists");
		}
		
		//rename the workspace
		if (!$mysql->query("UPDATE werblinks.workspaces SET name=? WHERE name=? AND userid=?",
						   array($newname, $oldname, $params->user->get_id()))) {
							throw new Exception("Unable to rename workspace");
		}
		
		$ws = array();
		$ws['newname'] = $newname;
		$ws['oldname'] = $oldname;
		$ws['workspaceid'] = $mysql->get_last_id();

		return array($ws);
	}
}

?>
