<?php
/*
 * Some examples:

Get:
$this->moduleSession->getModuleSetting('activeLanguage');

Simple set:
$this->moduleSession->setModuleSetting(array(
    'setting' => 'activeLanguage',
    'value' => 'nl'
));

More complicated set with sub-array:
$this->moduleSession->setModuleSetting(array(
    'setting' => array(
        'last_visited' => array(
            $taxon => array(
                $category => $d
            )
        )
    )
));

Get from other module; this requires array rather than single string
$this->moduleSession->getModuleSetting(array(
    'module' => 'language',
    'setting' => 'activeLanguage'
));

Set in other module:
$this->moduleSession->setModuleSetting(array(
    'module' => 'language',
    'setting' => 'activeLanguage',
    'value' => 'nl'
));

*/


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
		// Allow setting value in different module
	    if (isset($p['module'])) {

            $this->setOtherModuleSetting($p);

            return;

		}

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



	private function setOtherModuleSetting ($p) {

		$session = new SessionModuleSettings();

		$session->setModule(array(
            'environment' => 'admin',
		    'controller' => $p['module']
		));

		unset($p['module']);

        $session->setModuleSetting($p);

        unset($session);

	}


	private function getOtherModuleSetting ($p) {

		$session = new SessionModuleSettings();

		$session->setModule(array(
            'environment' => 'admin',
		    'controller' => $p['module']
		));

		isset($p['setting']) ? $session->getModuleSetting($p['setting']) :
            $session->getModuleSetting(null); // triggers error

        unset($session);

	}



}


