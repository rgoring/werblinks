<?

require_once(_BASE_PATH . "/app/database.php");

class User {
	private $keydat = "sdkljfh3813jkn46nASD343i5h4DtP4y5H6h6G32fgf5pwqopewyutrjnxmvcai3";
	private $cipher = 'AES-128-CBC';

	public function __construct()
	{
		//start the session
		session_name("werb_links");
		session_set_cookie_params(0, "/");
		session_start();
		
		if (!isset($_SESSION['user']['logged'])) {
			$_SESSION['user']['logged'] = false;
		}
		$this->logged = $_SESSION['user']['logged'];
	}
	
	public function __set($index, $value)
	{
		$_SESSION['user'][$index] = $value;
	}
	
	public function __get($index)
	{
		return $_SESSION['user'][$index];
	}
	
	public function logged_in()
	{
		return $_SESSION['user']['logged'];
	}
	
	public function save_to_cookie()
	{
		global $config;

		$plaintext = serialize($_SESSION['user']);
		$ivlen = openssl_cipher_iv_length($this->cipher);
		$iv = openssl_random_pseudo_bytes($ivlen);
		$ciphertext_raw = openssl_encrypt($plaintext, $this->cipher, $this->keydat, OPENSSL_RAW_DATA, $iv);
		$hmac = hash_hmac('sha256', $ciphertext_raw, $this->keydat, $as_binary=true);
		$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);

		if (strlen($ciphertext) >= 4096) {
			throw new Exception("cookie data too long");
		}

		setcookie("token", $ciphertext, time()+$config['user']['expire'], "/");

		return $ciphertext;
	}
	
	public function restore_from_cookie($ciphertext)
	{
		$decoded = base64_decode($ciphertext);
		$ivlen = openssl_cipher_iv_length($this->cipher);
		$iv = substr($decoded, 0, $ivlen);
		$hmac = substr($decoded, $ivlen, 32);
		$ciphertext_raw = substr($decoded, $ivlen+32);
		$sdata = openssl_decrypt($ciphertext_raw, $this->cipher, $this->keydat, OPENSSL_RAW_DATA, $iv);
		$calcmac = hash_hmac('sha256', $ciphertext_raw, $this->keydat, $as_binary=true);

		if (hash_equals($hmac, $calcmac) === false)
		{
			throw new Exception("Suspect data tampering");
		}
		
		$data = unserialize($sdata);
		if ($data === false) {
			throw new Exception("Invalid restore data");
		}

		//TODO: not override everything? Do queries to DB and re-init
		$_SESSION['user'] = $data;
		
		return true;
	}
	
	public function get_id()
	{
		return $_SESSION['user']['id'];
	}
	
	private static function valid_email($username) {
		//check for illegal characters & length
		if (strlen($username) > 300) {
			return false;
		}
		if (!preg_match('/^[A-Za-z0-9!#$%&*+\-\/?^._~]{1,64}@[A-Za-z]+[A-Za-z0-9-.]*$/i',$username)) {
			return false;
		}
		return true;
	}
	
	private static function valid_password($password) {
		//check for illegal characters & length
		if(!preg_match('/^[A-Za-z0-9_!@#$%^&*()?]{6,30}$/i', $password)) {
			return false;
		}
		return true;
	}
	
	private static function gen_salt($slen = 128)
	{
		$saltbase = "1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$baselen = strlen($saltbase);
		$saltstr = "";

		mt_srand();
		for ($i = 0; $i < $slen; $i++) {
			$saltstr .= $saltbase[mt_rand(0, $baselen - 1)];
		}
		
		return $saltstr;
	}

	private static function gen_hash($data, $algo = "sha512")
	{
		return hash($algo, $data);
	}

	private static function get_cryptpw($plaintext, $salt)
	{
		return self::gen_hash(_SITE_HASH.$plaintext.$salt);
	}
	
	private static function get_conftok($email, $salt)
	{
		return self::gen_hash($email.$salt, "sha256");
	}
	
	private static function check_pass($mysql, $email, $plaintext)
	{
		if (!$mysql->query("SELECT * FROM werblinks.users WHERE email=?", array($email))) {
			throw new Exception("Invalid username or password");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Invalid username or password");
		}
		
		$line = $mysql->fetch_row();
	
		//check their password
		$salt = substr($line['password'], 0, 128);
		$dbpass = substr($line['password'], 128);
		if (strcmp(self::get_cryptpw($plaintext, $salt), $dbpass) != 0) {
			throw new Exception("Invalid username or password");
		}
		
		return $line;
	}
	
	public static function register($reg_email, $reg_pass, $reg_pass_conf)
	{
		if (!self::valid_email($reg_email) || !self::valid_password($reg_pass)) {
			throw new Exception("Email or password does not meet minimum requirements.");
		}
		
		if (strcmp($reg_pass, $reg_pass_conf) != 0) {
			throw new Exception("Passwords do not match.");
		}
		
		$mysql = Database::get_database();
		$email = strtolower($reg_email);
		
		if (!$mysql->query("SELECT * FROM werblinks.users WHERE email=?", array($email))) {
			throw new Exception("Unable to check for duplicate users.");
		}
		if ($mysql->get_num_rows() != 0) {
			//found an email that matches
			throw new Exception("That username is already in use.");
		}
		
		$salt = self::gen_salt();
		$password = self::get_cryptpw($reg_pass, $salt);
		
		if (!$mysql->query("INSERT INTO werblinks.users (email, password, lastlogin) VALUES(?, ?, ?)", array($email, $salt.$password, date("Y-m-d H:i:s")))) {
			throw new Exception("Unable to register user in database");
		}
		
		$uid = $mysql->get_last_id();
		
		$regurl = _SITE_URL."/?r=user/confirm&email=$email&tok=".self::get_conftok($email, $salt);
		$subject = "Links Page Registration";
		$msg = "Thanks for signing up for Links.\n\nClick the link (or copy and paste the url into your browser) to complete registration:";
		$msg .= "\n<a href=\"$regurl\">$regurl</a>";
		$headers = 'From: Links <russell@werbweb.com>' . "\r\n" . 'Reply-To: russell@werbweb.com'. "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 

		if (!mail($email, $subject, $msg, $headers)) {
			throw new Exception("Registration mail not sent");
		}

		return $email;
	}
	
	public static function login($email, $password, $ws, $remember)
	{
		global $params;
	
		$params->user->logged = false;
		
		if(!self::valid_email($email) || !self::valid_password($password)) {
			throw new Exception("Email or password does not meet minimum requirements.");
		}
		
		$mysql = Database::get_database();

		$email = strtolower($email);

		$line = self::check_pass($mysql, $email, $password);
		
		if ($line['confirmed'] == 0) {
			throw new Exception("Account has not been confirmed");
		}
		
		//they're valid
		
		//set their workspace
		if (isset($ws) && $ws != "") {
			//get first workspace from database
			$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? AND name=?", array($line['userid'], $ws));
		} else {
			//get workspace id from database
			$mysql->query("SELECT * FROM werblinks.workspaces WHERE userid=? LIMIT 1", array($line['userid']));
		}
		if (!$mysql->get_result() || $mysql->get_num_rows() == 0) {
			throw new Exception("Unable to get workspace");
		}
		$wsinfo = $mysql->fetch_row();
		$params->user->wsid = $wsinfo['workspaceid'];
		$params->user->wsname = $wsinfo['name'];

		//update the login time
		//datetime field = YYYY-MM-DD HH:MM:SS (e.g. 2006-12-25 13:43:15)
		$mysql->query("UPDATE werblinks.users SET lastlogin=? WHERE userid=?", array(date("Y-m-d H:i:s"), $line['userid']));

		//TODO: reauth here??

		//all good; set their variables here
		$params->user->logged = true;
		$params->user->id = $line['userid'];
		$params->user->email = $line['email'];

		if (isset($remember)) {
			$params->user->save_to_cookie();
		}

		return true;
	}
	
	private static function set_password($newpass, $newpass_conf)
	{
		if (!self::valid_password($newpass)) {
			throw new Exception("New password does not meet minimum requirements");
		}
		if (strcmp($newpass, $newpass_conf) != 0) {
			throw new Exception("New passwords do not match");
		}
		
		global $params;
		$mysql = Database::get_database();
		
		$salt = self::gen_salt();
		$password = self::get_cryptpw($newpass, $salt);
		
		if (!$mysql->query("UPDATE werblinks.users SET password=? WHERE userid=?", array($salt.$password, $params->user->get_id()))) {
			throw new Exception("Unable to update password");
		}
	}
	
	public static function change_password($curpass, $newpass, $newpass_conf)
	{
		global $params;
		$mysql = Database::get_database();

		$line = self::check_pass($mysql, $params->user->email, $curpass);

		self::set_password($newpass, $newpass_conf);
		
		return true;
	}
	
	public static function confirm_user($email, $tok)
	{
		if (!isset($email) || $email == "") {
			throw new Exception("Invalid user");
		}
		if (!isset($tok) || $tok == "") {
			throw new Exception("Invalid token");
		}
		
		global $params;
		$mysql = Database::get_database();

		if (!$mysql->query("SELECT * FROM werblinks.users WHERE email=?", array($email))) {
			throw new Exception("Invalid user");
		}
		if ($mysql->get_num_rows() != 1) {
			throw new Exception("Invalid user");
		}
		
		$line = $mysql->fetch_row();
		
		if ($line['confirmed'] != 0) {
			throw new Exception("Account already activated");
		}
		
		$salt = substr($line['password'], 0, 128);
		$gentok = self::get_conftok($email, $salt);
		
		if (strcmp($gentok, $tok) != 0) {
			throw new Exception("Invalid token");
		}

		//create a default workspace for them
		if (!$mysql->query("INSERT INTO werblinks.workspaces (userid, name) VALUES(?, ?)", array($line['userid'], "Home"))) {
			throw new Exception("Unable to create default workspace");
		}
		
		//confirm them
		if (!$mysql->query("UPDATE werblinks.users SET confirmed=? WHERE userid=?", array(1, $line['userid']))) {
			throw new Exception("Unable to update password");
		}
		
		return true;
		
	}
	
	public static function logout()
	{
		global $params;
		
		if (isset($_COOKIE['token'])) {
			setcookie("token", "", time()-3600);
		}

		// Unset all of the session variables.
		setcookie(session_name(), session_id(), 0, "/");
		session_unset();
		session_destroy();
		$_SESSION = array();
	
		//set a new cookie
		session_start();
		session_regenerate_id(true);
		setcookie(session_name(), session_id(), 0, "/");
	}
	
	public static function get_linkcount()
	{
		global $params;
		$mysql = Database::get_database();
		if (!$mysql->query("SELECT COUNT(linkid) FROM werblinks.links WHERE userid=?", array($params->user->get_id()))) {
			throw new Exception("Unable to get link count");
		}
		$row = $mysql->fetch_row();
		return $row['COUNT(linkid)'];
	}
	
	public static function get_wscount()
	{
		global $params;
		$mysql = Database::get_database();
		if (!$mysql->query("SELECT COUNT(workspaceid) FROM werblinks.workspaces WHERE userid=?", array($params->user->get_id()))) {
			throw new Exception("Unable to get workspace count");
		}
		$row = $mysql->fetch_row();
		return $row['COUNT(workspaceid)'];
	}
	
	public static function get_groupcount()
	{
		global $params;
		$mysql = Database::get_database();
		if (!$mysql->query("SELECT COUNT(groupid) FROM werblinks.groups WHERE userid=?", array($params->user->get_id()))) {
			throw new Exception("Unable to get group count");
		}
		$row = $mysql->fetch_row();
		return $row['COUNT(groupid)'];
	}
	
	public static function get_hobjcount()
	{
		global $params;
		$mysql = Database::get_database();
		if (!$mysql->query("SELECT COUNT(htmlid) FROM werblinks.htmlobjs WHERE userid=?", array($params->user->get_id()))) {
			throw new Exception("Unable to get html object count");
		}
		$row = $mysql->fetch_row();
		return $row['COUNT(htmlid)'];
	}

}

?>
