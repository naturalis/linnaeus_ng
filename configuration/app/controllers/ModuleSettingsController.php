<?php

include_once ('Controller.php');
class ModuleSettingsController extends Controller
{
    public $usedModels = array('module_settings');

	private $_module;
	private $_settings;
	private $_defaultvalues;

    public function __construct($p = null)
    {
        parent::__construct($p);
		$this->setModule( $this->controllerBaseName );
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function setModule( $m )
    {
		$this->_module=$m;
    }

    public function getModule()
    {
		return $this->_module;
    }

	public function setModuleSettings()
	{
        $this->_settings = $this->models->ModuleSettings->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'module' => $this->_module
				),
				'columns' => 'lng_id,item_type,setting,value'));
	}

	public function getModuleSettings()
	{
        return $this->_settings;
	}

	public function setModuleDefaults( $d )
	{
		$this->_defaultvalues=$d;
	}

    public function getModuleSetting( $p )
    {
		$setting=isset($p['setting']) ? $p['setting'] : null;
		$id=isset($p['id']) ? $p['id'] : null;
		$type=isset($p['type']) ? $p['type'] : null;
		$subst=isset($p['subst']) ? $p['subst'] : null;

		if ( empty($setting) ) return;
		
		foreach((array)$this->_settings as $key=>$val)
		{
			if ($val['setting']==$setting && !is_null($val['value']))
			{
				if (
					(!is_null($id) && $val['lng_id']==$id) &&
					(!is_null($type) && $val['item_type']==$type)
				)
				{
					return $val['value'];
				}
				else
				if (
					(!is_null($id) && $val['lng_id']==$id) &&
					(is_null($type) && is_null($val['item_type']))
				)
				{
					return $val['value'];
				}
				else
				if (
					(is_null($id) && is_null($val['lng_id'])) &&
					(!is_null($type) && $val['item_type']==$type)
				)
				{
					return $val['value'];
				}
				if (
					(is_null($id) && is_null($val['lng_id'])) && 
					(is_null($type) && is_null($val['item_type']))
				)
				{
					return $val['value'];
				}
			}
			
			if (isset($this->_defaultvalues[ $setting ]))
				return $this->_defaultvalues[ $setting ];
		}
		
    }



}	