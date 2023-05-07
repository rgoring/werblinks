<?

class Database
{
	private $db = NULL;
	
	public static function get_database()
	{
		global $params;

		if (!$params->exists("db")) {
			$params->db = new MySql();
		}
		return $params->db;
	}
}

class MySql
{
	private $connection = NULL;
	private $result = NULL;
	private $query = NULL;

	function __construct()
	{
		global $config;
		$this->connection = new mysqli($config['database']['address'], $config['database']['username'], $config['database']['password'], $config['database']['dbname']);
		if ($this->connection->connect_errno) {
			die("Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error);
		}
	}

	function __destruct()
	{
		$this->disconnect();
	}
	
	private function disconnect()
	{
		if ($this->connection) {
			$this->connection->close();
			$this->connection = NULL;
		}
	}

	public function get_result()
	{
		if (!$this->result) {
			return false;
		}
		return true;
	}
	
	public function get_last_id()
	{
		return $this->connection->insert_id;
	}
	
	public function get_query()
	{
		return $this->query;
	}
	
	private function escape_params($qstr, $params)
	{
		$qcpy = $qstr;
		$pos = 0;
		
		foreach ($params as $param) {
//			echo "before1: $param<br>";
			if ($param === NULL) {
				// NULL can't go through real_escape_string, or it will end up as string
				$param = "NULL";
			} else {
				$param = $this->connection->real_escape_string($param);

				if (is_numeric($param)) {
					$param = "$param";
				} else {
					$param = "'".$param."'";
				}
			}
//			echo "before2: $param<br>";
			//do replacement
			$pos = strpos($qcpy, '?', $pos);
			if ($pos === false) {
				//no suitable matching string
				continue;
			}
//			echo "after1: $param | $qcpy<br>";
			$qcpy = substr_replace($qcpy, $param, $pos, 1);
//			echo "after2: $param | $qcpy<br>";
			$pos = $pos + strlen($param);
		}
		return $qcpy;
	}
	
	public function get_num_rows()
	{
		$qtype = explode(' ', $this->query, 1);
		if ($qtype == "SELECT" || $qtype == "SHOW") {
			return $this->connection->num_rows;
		}
		return $this->connection->affected_rows;
	}
	
	public function fetch_all_rows()
	{
		$rows = array();
		while ($row = $this->result->fetch_assoc()) {
			$rows[] = $row;
		}
		return $rows;
	}
	
	public function fetch_row()
	{
		return $this->result->fetch_assoc();
	}

	public function query($qstr, $params)
	{
		$this->query = NULL;
		$this->result = NULL;

		$this->query = $this->escape_params($qstr, $params);
		if (!$this->query) {
			return false;
		}
		$this->result = $this->connection->query($this->query);
		if (!$this->result) {
			return false;
		}
		return true;
	}

	public function errstr()
	{
		return $this->connection->error;
	}
}

?>