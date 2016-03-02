<?php

class PasswordEncoder
{
	private $_force_md5=false;
	private $_password;
	private $_hash;
	
	public function setForceMd5( $state )
	{
		if ( !is_bool($state) ) return;
		$this->_force_md5=$state;
	}

	public function setPassword( $password )
	{
		if ( empty($password) ) return;
		$this->_password=$password;
	}

	public function encodePassword()
    {
		if ( $this->_force_md5 )
		{
        	$this->setHash( md5( $this->getPassword() ) );
		}
		else
		{
			$this->setHash( password_hash( $this->getPassword(), PASSWORD_DEFAULT ) );
		}
    }
	
	public function getHash()
	{
		return $this->_hash;
	}

	private function setHash( $hash )
	{
		$this->_hash=$hash;
	}

	private function getPassword()
	{
		return $this->_password;
	}
	
}