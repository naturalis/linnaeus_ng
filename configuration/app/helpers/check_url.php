<?php

class CheckUrl {
	
	private $_url;
	private $_headers;
	private $_response;
	private $_httpCode;
	
	public function __construct()
	{
	}
	
	public function setUrl( $url )
	{
		$this->_url=$url;
	}

	public function exists()
	{
		if ( empty($this->_url) ) return;
		$this->fetch();
		return ($this->_httpCode>=200 && $this->_httpCode < 400);
	}
	
	public function getHeaders()
	{
		return $this->_headers;
	}

	public function getHeader( $type )
	{
		foreach((array)$this->_headers as $key=>$val)
		{
			if (strtolower($key)==strtolower($type))
			{
				return $val;
			}
		}
	}

	private function fetch()
	{
		$this->_headers=@get_headers($this->_url, 1);
		$this->_response=$this->_headers[0];
		$this->_httpCode=substr(str_ireplace('HTTP/1.1 ','',$this->_response),0,3);
	}
	
}