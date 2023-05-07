<?

	define ('_BASE_PATH', realpath(dirname(__FILE__)));
	define ('_SITE_URL', "https://links.werbweb.com");
	define ('_SITE_HASH', '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef');

	require(_BASE_PATH . '/app/app.php');

	$linkapp = new App();
	$linkapp->main();

?>
