<?php

class HttpBasicAuthentication
{

	/*

		$this->helpers->HttpBasicAuthentication->setVerificationCallback( 
			function($username,$password)
			{ 
				return $this->verifyCredentials($username,$password);
			}
		);
		
		$this->helpers->HttpBasicAuthentication->authenticate() or die('not authorized');

	*/

	
	private $_realm='realm';
	private $_phpAuthUser;
	private $_phpAuthPass;
	private $_verificationCallback;

	public function authenticate()
	{
		$this->setServerVars();

		if ( !isset($this->_phpAuthUser) )
		{
			$this->sendAuthHeaders();
			return false;			
		} 
		else
		{
			return $this->verifyCredentials();
		} 
	}
	
	public function setRealm( $realm )
	{
		$this->_realm=$realm;
	}
	
	public function setVerificationCallback( $callback )
	{
		$this->_verificationCallback=$callback;
	}
	
	private function verifyCredentials()
	{
		/*
			requires a callback that takes two parameters, username and password,
			and that return true or false.
		*/
		if ( isset($this->_verificationCallback) ) 
		{
			return call_user_func( $this->_verificationCallback, $this->_phpAuthUser, $this->_phpAuthPass );
		}
		else
		{
			return !empty($this->_phpAuthUser) && !empty($this->_phpAuthPass);
		}
	}

	private function setServerVars()
	{
		$this->_phpAuthUser=isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
		$this->_phpAuthPass=isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
	}

	private function sendAuthHeaders()
	{
		header('WWW-Authenticate: Basic realm="' . $this->_realm .'"');
		header('HTTP/1.0 401 Unauthorized');
	}
	

}