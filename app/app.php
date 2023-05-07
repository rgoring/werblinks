<?

//examples
// default action is "index"
// links.com/?r=user/login
// links.com/?r=user/logout
// links.com/?r=user/register
// links.com/russell -> links.com/?r=workspace&n=russell, should be links.com/#russell -> links.com/?r=workspace&n=russell
// links.com -> links.com/?r=default

require(_BASE_PATH . '/app/config.php');
require(_BASE_PATH . '/app/params.php');
require(_BASE_PATH . '/app/viewer.php');
require(_BASE_PATH . '/model/user.php');
require(_BASE_PATH . '/app/config.php');
require(_BASE_PATH . '/controller/control.php');

class App {
	function __construct()
	{
		global $params;
		global $config;

		$params = new Params();
		$params->config = $config;
		$params->user = new User();
	}
	
	public static function load_controller($controller)
	{
		$controller = basename($controller);
		if (!file_exists(_BASE_PATH . "/controller/$controller.php")) {
			return false;
		}
		
		include_once(_BASE_PATH . "/controller/$controller.php");
		return true;
	}

	private static function action_exists($controller, $action)
	{
		$classname = $controller."Controller";
		return method_exists($classname, $action);
	}

	public static function do_route($controller, $action, $params)
	{
		global $config;

		if ($config['maintenance']) {
			$controller = "page";
			$controller = "maintenance";
		}

		//load the controller
		//have to load the controller before we check for action
		if (self::load_controller($controller)) {
			//check to make sure the action exists
			if (!self::action_exists($controller, $action)) {
				die("no action $action");
			}
		} else {
			die("no controller $controller");
		}

		//run the controller action
		$classname = $controller."Controller";
		$ctrlobj = new $classname();
		if ($params == NULL) {
			$ctrlobj->{$action}();
		} else {
			$ctrlobj->{$action}($params);
		}
	}
	
	public function main()
	{		
		global $params;

		//see if route is specified
		$route = "";
		if (isset($_POST['r'])) {
			$route = $_POST['r'];
		} else if (isset($_GET['r'])) {
			$route = $_GET['r'];
		}
		
		//dissect the route
		$routepart = explode("/", $route);
		
		//get the controller name
		if (isset($routepart[0]) && $routepart[0] != "") {
			$cname = trim($routepart[0]);
		} else {
			$cname = $params->config['default']['controller'];
		}

		//get the action name
		if (isset($routepart[1]) && $routepart[1] != "" && $routepart[1][0] != "_") {
			$aname = trim($routepart[1]);
		} else {
			$aname = $params->config['default']['action'];
		}

		try {
			self::do_route($cname, $aname, NULL);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}

?>