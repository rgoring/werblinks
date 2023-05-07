<?

require_once(_BASE_PATH . '/model/link.php');

class linkController extends Control
{
	public static function add()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();

		try {
			$response->links = Link::add($_POST['name'], $_POST['url'], $_POST['gid']);
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
	
	public static function bookmark()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		if (isset($_GET['callback'])) {
			$response->set_callback($_GET['callback']);
		}
		
		if (!isset($_GET['name']) || !isset($_GET['url'])) {
			throw new Exception("Missing bookmark parameters");
		}
		
		$dname = urldecode($_GET['name']);
		$durl = urldecode($_GET['url']);

		try {
			$response->links = Link::add($dname, $durl, 0);
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}

	public static function del()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			Link::delete($_POST['id']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
	
	public static function move()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			Link::move($_POST['id'], $_POST['grp'], $_POST['pos']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}

	public static function move_to_bucket()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			Link::move_to_bucket($_POST['id']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
	
	public static function update()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			$response->links = Link::update($_POST['id'], $_POST['name'], $_POST['url']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
}

?>