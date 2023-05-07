<?

require_once(_BASE_PATH . '/model/workspace.php');

class workspaceController extends Control
{
	public static function init()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}
		
		$response = new DataResponse();
		
		try {
			$response->ws = Workspace::get_workspaces();
			$response->groups = Workspace::get_groups();
			$response->links = Workspace::get_links();
			$response->htmls = Workspace::get_htmlobjs();
		} catch(Exception $e) {
			$response->err = $e->getMessage();
		}
		
		$response->render();
	}
	
	public static function add()
	{
		global $params;
		if (!$params->user->logged_in()) {
			throw new Exception("Not logged in");
		}

		$response = new DataResponse();

		try {
			$response->ws = Workspace::addws($_POST['name']);
		} catch (Exception $e) {
			$response->err = $e->getMessage();
		}
		$response->render();
	}
}

?>