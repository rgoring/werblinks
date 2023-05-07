<?

class Params
{
	private $p = array();

	public function exists($index)
	{
		return array_key_exists($index, $this->p);
	}
	
	public function __set($index, $value)
	{
		$this->p[$index] = $value;
	}
	
	public function __get($index)
	{
		if (!$this->exists($index)) {
			return "";
		}
		return $this->p[$index];
	}
}

?>
