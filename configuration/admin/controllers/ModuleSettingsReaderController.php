<?php /** @noinspection PhpMissingParentCallMagicInspection */

include_once ('Controller.php');

class ModuleSettingsReaderController extends Controller
{
    public $usedModels = array(
        'module_settings',
        'module_settings_values'
    );

	private $_modulecontroller;
	private $_moduleid;
	private $_settingsvalues;
	private $_generalsettingsvalues;
	private $_usedefaultwhennovalue=false;
	public $modelNameOverride = 'ModuleSettingsModel';
	private $lastSettingId;

    /**
     * ModuleSettingsReaderController constructor.
     * @param null $p
     */
    public function __construct($p = null)
    {
        parent::__construct($p);

		$this->setModuleController( $this->controllerBaseName );
		$this->initialize();
	}

    /**
     * ModuleSettingsReaderController destructor
     */
    public function __destruct()
    {
        parent::__destruct();
    }

	public function setController( $controller )
	{
		$this->setModuleController( $controller );
		$this->initialize();
	}

    /**
     * Picks the value of a module setting read from the database
     *
     * @param $p
     * @return mixed|null
     */
    public function getModuleSetting($p )
    {
		//if ( !$this->getAuthorisationState() ) return;

		if ( is_array( $p ))
		{
			$setting=isset($p['setting']) ? $p['setting'] : null;
			$subst=isset($p['subst']) ? $p['subst'] : null;
			$module=isset($p['module']) ? $p['module'] : null;
		}
		else
		{
			$setting=$p;
		}
		
		$this->lastSettingId=null;

		if ( isset($module) && $module!=$this->getModuleController() )
		{
			return $this->getExtraneousModuleSetting( $p );
		}

		if ( empty($setting) ) return false;

		foreach((array)$this->getModuleSettingsValues() as $val)
		{
			if ($val['setting']==$setting) $this->lastSettingId=$val['id'];

			if ($val['setting']==$setting && !is_null($val['value']))
			{
				return $val['value'];
			}
		}

		if ( isset($subst) )
		{
			return $subst;
		}
    }

    /**
     * Picks the value of a general setting read from the database
     *
     * @param $p
     * @return mixed|null
     */
    public function getGeneralSetting($p )
    {
		if ( is_array( $p ))
		{
			$setting=isset($p['setting']) ? $p['setting'] : null;
			$subst=isset($p['subst']) ? $p['subst'] : null;
			$no_auth_check=isset($p['no_auth_check']) && $p['no_auth_check']===true ? true : false;
		}
		else
		{
			$no_auth_check=false;
			$setting=$p;
		}

		//if ( !$no_auth_check && !$this->getAuthorisationState() ) return;
		
		$this->lastSettingId=null;

        /**
         *  Traverse the general settings array and match
         */
		foreach((array)$this->getGeneralSettingsValues() as $val)
		{
			if ($val['setting']==$setting) $this->lastSettingId=$val['id'];
			
			if ($val['setting']==$setting && !is_null($val['value']))
			{
				return $val['value'];
			}
            if ($val['setting']==$setting && is_null($val['value']) && !is_null($val['default_value']))
            {
                return $val['default_value'];
            }
		}

		if ( isset($subst) )
		{
			return $subst;
		}
    }

	public function assignModuleSettings( &$settings )
	{
		//if ( !$this->getAuthorisationState() ) return;

		$settings = new stdClass();
		foreach((array)$this->getModuleSettingsValues() as $val)
		{
			if ( is_null($val['value']) && $this->getUseDefaultWhenNoValue() && !is_null($val['default_value']) )
			{
				$settings->{$val['setting']}=$val['default_value'];
			}
			else
			{
				$settings->{$val['setting']}=$val['value'];
			}
		}
	}

	public function assignGeneralSettings( &$settings )
	{
		//if ( !$this->getAuthorisationState() ) return;

		$settings = new stdClass();
		foreach((array)$this->getGeneralSettingsValues() as $val)
		{
			if ( is_null($val['value']) && $this->getUseDefaultWhenNoValue() && !is_null($val['default_value']) )
			{
				$settings->{$val['setting']}=$val['default_value'];
			}
			else
			{
				$settings->{$val['setting']}=$val['value'];
			}
		}
	}

    public function setUseDefaultWhenNoValue( $state )
    {
		//if ( !$this->getAuthorisationState() ) return;
		if ( is_bool($state) ) $this->_usedefaultwhennovalue=$state;
    }

    public function getLastSettingId()
    {	
		return $this->lastSettingId;
    }


	private function initialize()
	{
		$this->UserRights->setRequiredLevel( ID_ROLE_EDITOR );
		$this->setModuleId();
		$this->setModuleSettingsValues();
		$this->setGeneralSettingsValues();
    }

    private function getUseDefaultWhenNoValue()
    {
		return $this->_usedefaultwhennovalue;
    }

    private function setModuleController( $m )
    {
		$this->_modulecontroller=$m;
    }

    private function getModuleController()
    {
		return $this->_modulecontroller;
    }

    private function resolveModuleId( $controller )
    {
		$d=$this->models->Modules->_get(array("id"=>array("controller"=>$controller)));

		if ($d)
		{
			return $d[0]['id'];
		}
    }

    private function setModuleId()
    {
		$this->_moduleid=$this->resolveModuleId( $this->getModuleController() );
    }

    private function getModuleId()
    {
		return $this->_moduleid;
    }

	private function setModuleSettingsValues()
	{
		if (is_null($this->getModuleId())) return;

		$this->_settingsvalues = $this->models->ModuleSettingsModel->setModuleReaderSettingValues(array(
            'projectId' => $this->getCurrentProjectId(),
		    'moduleId' => $this->getModuleId()
		));
	}

	private function getModuleSettingsValues()
	{
        return $this->_settingsvalues;
	}

	private function setGeneralSettingsValues()
	{
		$this->_generalsettingsvalues = $this->models->ModuleSettingsModel->setModuleReaderSettingValues(array(
            'projectId' => $this->getCurrentProjectId(),
		    'moduleId' => GENERAL_SETTINGS_ID
		));
	}

	private function getGeneralSettingsValues()
	{
        return $this->_generalsettingsvalues;
	}

	private function getExtraneousModuleSetting( $p )
	{
		$setting=isset($p['setting']) ? $p['setting'] : null;
		$subst=isset($p['subst']) ? $p['subst'] : null;
		$module=isset($p['module']) ? $p['module'] : null;

		if ( empty($setting) ) return;

		$moduleid=$this->resolveModuleId( $module );

		if ( empty($moduleid) ) return;

		$settingsvalues = $this->models->ModuleSettingsModel->setModuleReaderSettingValues(array(
            'projectId' => $this->getCurrentProjectId(),
		    'moduleId' => $moduleid,
		    'setting' => $setting
		));

		if ( $settingsvalues )
		{
			$this->lastSettingId=$settingsvalues[0]['id'];

			if (!is_null($settingsvalues[0]['value']))
			{
				return $settingsvalues[0]['value'];
			}
		}

		if ( isset($subst) )
		{
			return $subst;
		}
    }

}