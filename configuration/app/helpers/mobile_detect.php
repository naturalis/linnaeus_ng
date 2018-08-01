<?php

/*
	this helper uses third party library Mobile Detect
	
	http://mobiledetect.net/

	Developers: Șerban Ghiță, Nick Ilyin.
	Original author: Victor Stanciu.
	© Mobile Detect is an open-source script released under MIT License.
*/
include_once('Mobile-Detect-2.8.28/Mobile_Detect.php');

class MobileDetect {

	private $detect;
	
	public function __construct()
	{
		$this->detect = new Mobile_Detect;
	}

	public function isMobile()
	{
		// Any mobile device (phones or tablets).
		return $this->detect->isMobile();
	}
 
	public function isTablet()
	{
		// Any tablet device.
		return $this->detect->isTablet(); 
	}

	public function isPhone()
	{
		return $this->detect->isMobile() && !$this->detect->isTablet(); 
	}

	public function isiOS()
	{
		// Check for a specific platform with the help of the magic methods:
		return $this->detect->isiOS();	 
	}
 
	public function isAndroidOS()
	{
		return $this->detect->isAndroidOS();
	}	

	public function is( $str )
	{
		//example: Chrome, iOS, UC Browser
		//BETA
		return $this->detect->is( $str );
	}	

	public function version( $str )
	{
		//example: iPad, iPhone, Android, Opera Mini
		//BETA
		return $this->detect->version( $str );
	}	

}