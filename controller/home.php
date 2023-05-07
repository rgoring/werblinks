<?

require_once(_BASE_PATH . '/model/workspace.php');

class homeController extends Control
{
	public static function index()
	{
		global $params;
		
		if ($params->user->logged_in()) {
			if (isset($_GET['ws'])) {
				try {
					Workspace::change($_GET['ws']);
				} catch (Exception $e) {
					self::redirect();
				}
			}

			//show the workspace
			$view = new Viewer("workspace.php");
			$view->title = "Werbweb Links: ".$params->user->wsname;
			$view->add_css("common.css");
			$view->add_css("workspace.css");
			$view->add_css("links.css");
			$view->add_css("jquery-ui-1.8.16.custom.css");
			$view->add_js("jquery-1.7.1.min.js");
			$view->add_js("jquery-ui-1.8.16.custom.min.js");
			$view->add_js("links.js");
			$view->render();
		} else {
			if (isset($_COOKIE['token'])) {
				try {
					if ($params->user->restore_from_cookie($_COOKIE['token']) == true) {
						self::redirect();
					}
				} catch (Exception $e) {
				}
			}
			//show the login screen
			$view = new Viewer("login.php");
			$view->add_css("common.css");
			$view->add_css("login.css");
			$view->add_js("login.js");
			$view->title = "Werbweb Links Login";
			$view->render();
		}
	}
	
	public static function help()
	{
		global $params;
		$view = new Viewer("help.php");
		$view->add_css("common.css");
		$view->add_css("help.css");
		$view->title = "Links Help";
		$view->render();
	}
}

?>