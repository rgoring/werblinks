<?

require_once(_BASE_PATH . '/model/hobject.php');

class hobjectController extends Control
{
	public static function add()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			$response->htmls = Hobject::add($_POST['name'], $_POST['code']);
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
			Hobject::move($_POST['id'], $_POST['x'], $_POST['y']);
			$response->success = true;
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
			Hobject::resize($_POST['id'], $_POST['w'], $_POST['h']);
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
			$response->htmls = Hobject::update($_POST['id'], $_POST['name'], $_POST['code']);
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
			Hobject::delete($_POST['id']);
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
			Hobject::sticky($_POST['id']);
			$response->success = true;
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
}

?>