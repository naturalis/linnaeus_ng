<?php

class Functions
{
	
	private $_keyValueGlue=null;
	
	private function fission($v,$k)
	{ 
		return (!empty($k) ? $k.$this->_keyValueGlue : '').$v;
	}
	
	public function nuclearImplode($keyValueGlue,$elementGlue,$array,$skipEmptyValues=false)
	{
		if (!is_array($array))
			return $array;
			
		$this->_keyValueGlue=$keyValueGlue;
		
		if ($skipEmptyValues) {
			foreach($array as $key=>$val)
				if (empty($val)) unset($array[$key]);
		}

		return implode($elementGlue,array_map(array($this,'fission'), $array, array_keys($array)));

	}

	public function serverIsWindows()
	{
		return (strtolower(substr(PHP_OS,0,3))==='win');
	}

}