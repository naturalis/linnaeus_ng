<?php

class SessionModuleSettings
{
	private $environment;
	private $controller;
	private $setting;
	private $projectId;
	private $value;

    public function __construct ()
    {
	}

    public function setModule( $p )
    {
		$this->setEnvironment( $p['environment'] );
		$this->setController( $p['controller'] );
		isset($p['projectId']) ? $this->setProjectId($p['projectId']) : $this->setProjectId(null);
    }

	public function setModuleSetting( $p )
	{
		$this->setSetting( $p['setting'] );

		$this->setValue( isset($p['value']) ? $p['value'] : null );

		$this->initialize();

		// admin; no project id in session
		if (is_null($this->getProjectId())) {

			if ( is_null($this->getValue()) ) {

			    unset( $_SESSION[$this->getEnvironment()][$this->getController()][$this->getSetting()] );

			} else {

			    $_SESSION[$this->getEnvironment()][$this->getController()][$this->getSetting()]=$this->getValue();
    		}

    	// app; project id required
		} else {

			if ( is_null($this->getValue()) ) {

			    unset( $_SESSION[$this->getEnvironment()][$this->getProjectId()][$this->getController()][$this->getSetting()] );

			} else {

			    $_SESSION[$this->getEnvironment()][$this->getProjectId()][$this->getController()][$this->getSetting()]=$this->getValue();
    		}
		}
	}

	public function getModuleSetting( $setting )
	{
		if ( !isset($setting)) return;

		$this->setSetting( $setting );

		$this->initialize();

		if (is_null($this->getProjectId())) {

			return isset($_SESSION[$this->getEnvironment()][$this->getController()][$this->getSetting()]) ?
				$_SESSION[$this->getEnvironment()][$this->getController()][$this->getSetting()] :
				null;

		}

		return isset($_SESSION[$this->getEnvironment()][$this->getProjectId()][$this->getController()][$this->getSetting()]) ?
			$_SESSION[$this->getEnvironment()][$this->getProjectId()][$this->getController()][$this->getSetting()] :
			null;

	}

    private function setEnvironment( $environment )
    {
		$this->environment=$environment;
    }

    private function getEnvironment()
    {
		return $this->environment;
    }

    private function setController( $controller )
    {
		$this->controller=$controller;
    }

    private function getController()
    {
		return $this->controller;
    }

    private function setSetting( $setting )
    {
		$this->setting=$setting;
    }

    private function getSetting()
    {
		return $this->setting;
    }

    private function setProjectId ($projectId)
    {
		$this->projectId = $projectId;
    }

    private function getProjectId ()
    {
		return $this->projectId;
    }

    private function setValue( $value )
    {
		$this->value=$value;
    }

    private function getValue()
    {
		return $this->value;
    }

    private function initialize()
    {
		if ( empty($this->getEnvironment()) )
			die( 'no environment' );

		if ( empty($this->getController()) )
			die( 'no controller' );

		if (empty($this->getSetting()) )
			die( 'no setting' );
    }

}


