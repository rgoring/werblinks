<?

class Control
{
	public static function redirect($controller = null, $action = null)
	{
		$route = _SITE_URL;
		if ($controller || $action) {
			$route .= "/?r=$controller/$action";
		}
		header("Location: $route");
		exit;
	}
}
?>