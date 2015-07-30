<?php

/*

		$this->moduleSettings=new ModuleSettingsController;
			echo $this->moduleSettings->getModuleSetting( 'setting_name' );
		or
			$this->moduleSettings->assignModuleSettings( $this->settings );
			echo $this->settings->setting_name;

*/


include_once ('Controller.php');
class ModuleSettingsController extends Controller
{
    public $usedModels = array('module_settings','module_settings_values');

	private $_modulecontroller;
	private $_moduleid;
	private $_settingsvalues;

    public function __construct($p = null)
    {
        parent::__construct($p);

		$this->setModuleController( $this->controllerBaseName );
		$this->setModuleId();
		$this->setModuleSettingsValues();
		

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
		}
		else
		{
			$setting=$p;
		}
		
		if ( empty($setting) ) return;
		
		foreach((array)$this->getModuleSettingsValues() as $val)
		{
			if ($val['setting']==$setting && !is_null($val['value']))
			{
				return $val['value'];
			}
			
			if ( isset($subst) )
			{
				return $subst;
			}
		}
    }

	public function assignModuleSettings( &$settings )
	{
		$settings = new stdClass();
		foreach((array)$this->getModuleSettingsValues() as $val)
		{
			$settings->$val['setting']=$val['value'];
		}
	}

    private function setModuleController( $m )
    {
		$this->_modulecontroller=$m;
    }

    private function getModuleController()
    {
		return $this->_modulecontroller;
    }

    private function setModuleId()
    {
		$d=$this->models->Module->_get(array("id"=>array("controller"=>$this->getModuleController())));
		if ($d)
		{
			$this->_moduleid=$d[0]['id'];
		}
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
				_b.setting

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


}	