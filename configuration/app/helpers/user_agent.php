<?php

class UserAgent {
	
	private $_agent;
	
	public function __construct()
	{
		$this->agent=$_SERVER['HTTP_USER_AGENT'];
	}
	
	public function getAgent()
	{
		return $this->agent;
	}

	public function isGSM()
	{
		return (bool)preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'.
                    '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $this->getAgent() );
	}
	
	public function isTablet()
	{
		return $this->isMobileDevice() && !$this->isGSM();
	}
	
	public function isMobileDevice()
	{
		return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
                    '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $this->getAgent() );						
	}

	
}