<?

require_once(_BASE_PATH . '/model/group.php');

class groupController extends Control
{
	public static function add()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			$response->groups = Group::add($_POST['name']);
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
			$response->groups = Group::move($_POST['id'], $_POST['x'], $_POST['y']);
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
	
	public static function resize()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			Group::resize($_POST['id'], $_POST['w'], $_POST['h']);
			$response->success = true;
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
			Group::delete($_POST['id']);
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
			$response->groups = Group::update($_POST['id'], $_POST['name']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
	
	public static function sticky()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			Group::sticky($_POST['id']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
}

?>