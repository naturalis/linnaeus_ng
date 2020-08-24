<?php /** @noinspection PhpMissingParentCallMagicInspection */
/**
 *
 *  	$this->moduleSettings=new ModuleSettingsController;
 *		echo $this->moduleSettings->getModuleSetting( 'setting_name' );
 *      or
 *		$this->moduleSettings->assignModuleSettings( $this->settings );
 *		echo $this->settings->setting_name;
 *
 *      general settings are stored under module_id -1
 *
 */

include_once ('Controller.php');

class ModuleSettingsController extends Controller
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
        // Allow override of module name, as this sometimes does not match (e.g. search = utiltiies)
		if (!empty($p['controllerBaseName'])) {
			$this->controllerBaseName = $p['controllerBaseName'];
		}
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
		}
		else
		{
			$setting=$p;
		}
		
		if ( empty($setting) ) return ;
		
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

    private function setModuleId()
    {
		$d=$this->models->Modules->_get(array("id"=>array("controller"=>$this->getModuleController())));

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


}	