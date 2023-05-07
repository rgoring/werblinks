<?

class Viewer
{
	private $hdr;
	private $ftr;
	private $body;
	private $css;
	private $js;

	private $pagevars;

	function __construct($main)
	{
		$this->hdr = "header.php";
		$this->ftr = "footer.php";
		$this->body = $main;
		$this->css = array();
		$this->js = array();
		$this->pagevars = array();
		$this->pagevars['title'] = "Werbweb Links";
	}
	
	public function add_js($name)
	{
		$this->js[] = $name;
	}
	
	public function add_css($name)
	{
		$this->css[] = $name;
	}
	
	public function __set($index, $value)
	{
		$this->pagevars[$index] = $value;
	}
	
	function render()
	{
		// load page variables
		foreach ($this->pagevars as $key => $value) {
			$$key = $value;
		}

		$js = $this->js;
		$css = $this->css;

		include(_BASE_PATH."/view/".$this->hdr);
		include(_BASE_PATH."/view/".$this->body);
		include(_BASE_PATH."/view/".$this->ftr);
	}
}

class DataResponse
{
	private $response;
	private $callback;
	
	function __construct()
	{
		$this->response = array();
	}
	
	public function __set($index, $value)
	{
		$this->response[$index] = $value;
	}
	
	function set_callback($cb)
	{
		$this->callback = $cb;
	}
	
	function render()
	{
		if ($this->callback) {
			echo $this->callback;
			echo "(";
			echo json_encode($this->response);
			echo ");";
		} else {
			echo json_encode($this->response);
		}
	}
}

?>