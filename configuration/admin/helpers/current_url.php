<?php

class CurrentUrl {
	
	private $_url;
	
	public function __construct()
	{
		$this->_url=parse_url((stripos($_SERVER['SERVER_PROTOCOL'],'https')===0 ? 'https://' : 'http://'). $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$this->setRemoveQuery( false );
		$this->setRemoveFragment( false );
	}
	
	public function setRemoveQuery( $state ) 
	{
		$this->removeQuery=$state;
	}

	public function setRemoveFragment( $state ) 
	{
		$this->removeFragment=$state;
	}


	public function getUrl()
	{
		return
			$this->_url['scheme']."://".
				(isset($this->_url['user']) ? $this->_url['user'] : "").
				(isset($this->_url['user']) || isset($this->_url['pass']) ? ":" : "").
				(isset($this->_url['pass']) ? $this->_url['pass'] : "").
				(isset($this->_url['user']) || isset($this->_url['pass']) ? "@" : "").
			$this->_url['host'].
				(isset($this->_url['port']) ? ":" . $this->_url['port'] : "" ).
			$this->_url['path'].
				(isset($this->_url['query']) && !$this->removeQuery ? "?" . $this->_url['query'] : "" ).
				(isset($this->_url['fragment']) && !$this->removeFragment ? "#" . $this->_url['fragment'] : "" )
			;

		return $this->_url;
	}

	public function getParts()
	{
		return $this->_url;
	}
	
}