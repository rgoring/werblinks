<?

require_once(_BASE_PATH . '/model/user.php');

class userController extends Control
{
	public static function index()
	{
		echo "user index";
	}

	public static function login()
	{
		global $params;
		if ($params->user->logged_in()) {
			self::redirect();
		}

		try {
			if (User::login($_POST['username'], $_POST['password'], $_POST['ws'], $_POST['remember'])) {
				self::redirect();
			}
		} catch (Exception $e) {
			//show the login screen
			$view = new Viewer("login.php");
			//$view->add_js("login.js");
			$view->add_css("common.css");
			$view->add_css("login.css");
			$view->title = "Werbweb Links Login";
			$view->loginerr = $e->getMessage();
			$view->render();
		}
	}

	public static function register()
	{
		global $params;
		if ($params->user->logged_in()) {
			self::redirect();
		}

		try {
			$uname = User::register($_POST['reg_email'], $_POST['reg_pass'], $_POST['reg_pass_confirm']);
			$view = new Viewer("info.php");
			$view->add_css("common.css");
			$view->add_css("info.css");
			$view->title = "Registration Success";
			$view->ititle = "Registration Success";
			$view->msg = "An email has been sent to $uname. Please check your email and click the link to confirm your email address. Once you confirm the email address, you will be able to log in.";
			$view->render();
		} catch (Exception $e) {
			//show the login screen
			$view = new Viewer("login.php");
			$view->add_js("login.js");
			$view->add_css("common.css");
			$view->add_css("login.css");
			$view->title = "Werbweb Links Login";
			$view->regerr = $e->getMessage();
			$view->render();
		}
	}

	public static function logout()
	{
		User::logout();
		self::redirect();
	}

	public static function settings()
	{
		global $params;
		if (!$params->user->logged_in()) {
			self::redirect();
		}

		$view = new Viewer("settings.php");
		$view->add_css("common.css");
		$view->add_css("settings.css");
		$view->add_js("settings.js");
		$view->add_js("jquery-1.7.1.min.js");
		$view->title = "Account Settings";
		$view->email = $params->user->email;
		$view->numlinks = User::get_linkcount();
		$view->numgroups = User::get_groupcount();
		$view->numworkspaces = User::get_wscount();
		$view->numhobjs = User::get_hobjcount();
		$view->render();
	}

	public static function changepw()
	{
		global $params;
		if (!$params->user->logged_in()) {
			self::redirect();
		}

		$response = new DataResponse();

		//change password
		try {
			User::change_password($_POST['password'], $_POST['npassword'], $_POST['cnpassword']);
			$response->success = "Password successfully updated";
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}

		$response->render();
	}

	public static function confirm()
	{
		//show the confirmation screen
		$view = new Viewer("info.php");
		$view->add_css("common.css");
		$view->add_css("info.css");
		$view->title = "Account Confirmation";
		try {
			User::confirm_user($_GET['email'], $_GET['tok']);
			$view->ititle = "Registration Confirmed";
			$view->msg = "Email confirmation of {$_GET['email']} Successful.";
		} catch (Exception $e) {
			$view->ititle = "Email Confirmation Error";
			$view->msg = $e->getMessage();
		}

		$view->render();
	}

	public static function test()
	{
		global $params;

		$raw = $params->user->save_to_cookie();
		if (!$params->user->restore_from_cookie($raw)) {
			die("test failed;");
		}
		die("test successful");
	}
}

?>
