<?php

include_once ('Controller.php');

class ModuleSettingsReaderController extends Controller
{
    public $usedModels = array('module_settings','module_settings_values');

	private $_modulecontroller;
	private $_moduleid;
	private $_settingsvalues;
	private $_generalsettingsvalues;
	private $_usedefaultwhennovalue=false;
	

    public function __construct($p = null)
    {
        parent::__construct($p);

		$this->setModuleController( $this->controllerBaseName );
		$this->setModuleId();
		$this->setModuleSettingsValues();
		$this->setGeneralSettingsValues();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function getModuleSetting( $p )
    {
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
		
		if ( isset($module) && $module!=$this->getModuleController() )
		{
			return $this->getExtraneousModuleSetting( $p );
		}
		
		if ( empty($setting) ) return;
		
		foreach((array)$this->getModuleSettingsValues() as $val)
		{
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

	public function assignModuleSettings( &$settings )
	{
		$settings = new stdClass();
		foreach((array)$this->getModuleSettingsValues() as $val)
		{
			if ( is_null($val['value']) && $this->getUseDefaultWhenNoValue() && !is_null($val['default_value']) )
			{
				$settings->$val['setting']=$val['default_value'];
			}
			else
			{
				$settings->$val['setting']=$val['value'];
			}
		}
	}

	public function assignGeneralSettings( &$settings )
	{
		$settings = new stdClass();
		foreach((array)$this->getGeneralSettingsValues() as $val)
		{
			if ( is_null($val['value']) && $this->getUseDefaultWhenNoValue() && !is_null($val['default_value']) )
			{
				$settings->$val['setting']=$val['default_value'];
			}
			else
			{
				$settings->$val['setting']=$val['value'];
			}
		}
	}

    public function setUseDefaultWhenNoValue( $state )
    {
		if ( is_bool($state) ) $this->_usedefaultwhennovalue=$state;
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

		$this->_settingsvalues=$this->models->ModuleSettingsValues->freeQuery("
			select
				_a.value as value,
				_b.setting,
				_b.default_value as default_value

			from
				%PRE%module_settings _b

			left join
				%PRE%module_settings_values _a
				on _b.id=_a.setting_id
				and _a.project_id = " . $this->getCurrentProjectId() . "

			where
				_b.module_id = " . $this->getModuleId() . "
			");
	}

	private function getModuleSettingsValues()
	{
        return $this->_settingsvalues;
	}

	private function setGeneralSettingsValues()
	{
		$this->_generalsettingsvalues=$this->models->ModuleSettingsValues->freeQuery("
			select
				_a.value as value,
				_b.setting,
				_b.default_value as default_value

			from
				%PRE%module_settings _b

			left join
				%PRE%module_settings_values _a
				on _b.id=_a.setting_id
				and _a.project_id = " . $this->getCurrentProjectId() . "

			where
				_b.module_id = -1
			");
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
		
		if (is_null($this->getModuleId())) return;

		$settingsvalues=$this->models->ModuleSettingsValues->freeQuery("
			select
				_a.value as value,
				_b.setting,
				_b.default_value as default_value

			from
				%PRE%module_settings _b

			left join
				%PRE%module_settings_values _a
				on _b.id=_a.setting_id
				and _a.project_id = " . $this->getCurrentProjectId() . "

			where
				_b.module_id = " . $moduleid . "
				and _b.setting = '" . $setting . "'
			");
			
		if ( $settingsvalues )
		{
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