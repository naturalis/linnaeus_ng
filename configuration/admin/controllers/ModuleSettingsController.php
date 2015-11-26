<?php

/*
// values are max 512 characters!


drop table `module_settings`;

create table `module_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `setting` varchar(64) NULL,
  `info` varchar(1000) NULL,
  `default_value` varchar(512) NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `module_settings_1` (`module_id`),
  UNIQUE `module_settings_2` (`module_id`,`setting`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
;

drop table module_settings_values;


CREATE TABLE `module_settings_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `setting_id` int(11) NOT NULL,
  `value` varchar(512) NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module_settings_3` (`project_id`,`setting_id`),
  KEY `module_settings_1` (`project_id`),
  KEY `module_settings_2` (`project_id`,`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



*/

include_once ('Controller.php');

class ModuleSettingsController extends Controller
{

	public $usedModels = array('module_settings','module_settings_values');
	public $controllerPublicName = 'Module Settings';
	public $cssToLoad = array();
	public $jsToLoad = array();

	private $_modules;
	private $_moduleid;
	private $_module;
	private $_settings;
	private $_settingid;
	private $_settingsvalues;

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		$this->setModules();
	}

    public function indexAction()
    {
        $this->checkAuthorisation();

        $this->setPageName( $this->translate('Module settings') );

		$this->smarty->assign('modules',$this->getModules());

		$this->printPage();
    }

    public function settingsAction()
    {

		// REFAC2015: access level needs to be sysadmin

        $this->checkAuthorisation();

		$this->setModuleId( $this->rGetId() );

		$this->setModule();

		$m=$this->getModule();

		if ( empty($m) )
		{
			$this->redirect('index.php');
		}

		$this->setModuleSettings();

		if ( $this->rHasVal('action','save') )
		{
			$this->saveModuleSetting();
			$this->setModuleSettings();
		}
		else
		if ( $this->rHasVal('action','delete') && $this->rHasVal('setting_id') )
		{
			$this->setSettingId( $this->rGetVal('setting_id') );
			$this->deleteModuleSetting();
			$this->setModuleSettings();
		}

		$m=$this->getModule();

        $this->setPageName( sprintf( $this->translate('Module settings: %s ') , $m['module'] ));

		$this->smarty->assign( 'module', $this->getModule() );
		$this->smarty->assign( 'settings', $this->getModuleSettings() );

		$this->printPage();
    }

    public function valuesAction()
    {

		// REFAC2015: access level needs to be lead expert

        $this->checkAuthorisation();

		$this->setModuleId( $this->rGetId() );

		$this->setModule();

		$m=$this->getModule();

		if ( empty($m) )
		{
			$this->redirect('index.php');
		}

		$this->setModuleSettings();
		$this->setModuleSettingValues();

		if ( $this->rHasVal('action','save') )
		{
			$this->saveModuleSettingValues();
			$this->setModuleSettingValues();
		}

		$m=$this->getModule();

        $this->setPageName( sprintf( $this->translate('Module settings: %s ') , $m['module'] ));

		$this->smarty->assign( 'module', $this->getModule() );
		$this->smarty->assign( 'settings', $this->getModuleSettings() );
		$this->smarty->assign( 'values', $this->getModuleSettingValues() );

		$this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
		if ( $this->rHasVal('action', 'update_info') && $this->rHasId() )
		{
			$this->updateModuleInfo( array( "id"=>$this->rGetId(), "info"=>$this->rGetVal("value") ) );
			$this->smarty->assign( "returnText", "saved" );
        }
		else
		if ( $this->rHasVal('action', 'update_value') && $this->rHasId() )
		{
			$this->updateModuleDefaultValue( array( "id"=>$this->rGetId(), "value"=>$this->rGetVal("value") ) );
			$this->smarty->assign( "returnText", "saved" );
        }

		$this->printPage();
	}




	private function setModules()
	{
		$this->_modules = $this->models->ModuleSettingsModel->setModules();

		$this->_modules[]=array("id"=>-1,"module"=>"General settings");
	}

	private function getModules()
	{
		return $this->_modules;
	}

	private function setModuleId( $id )
	{
		$this->_moduleid=$id;
	}

	private function getModuleId()
	{
		return $this->_moduleid;
	}

	private function setModule( )
	{
		$this->_module=null;

		foreach($this->getModules() as $val)
		{
			if ($val['id']==$this->getModuleId())
			{
				$this->_module=$val;
			}
		}

	}

	private function getModule()
	{
		return $this->_module;
	}

	private function setModuleSettings()
	{
		$this->_settings=$this->models->ModuleSettings->_get(array("id"=>
			array(
				"module_id"=>$this->getModuleId()
			)));
	}

	private function getModuleSettings()
	{
		return $this->_settings;
	}

	private function getModuleSetting( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$setting=isset($p['setting']) ? $p['setting'] : null;

		if ( empty($id) && empty($setting) ) return;

		foreach((array)$this->getModuleSettings() as $val)
		{
			if (
				(empty($id) || (!empty($id) && $id==$val['id'])) &&
				(empty($setting) || (!empty($setting) && $setting==$val['setting']))
			)
			{
				return $val;
			}
		}
	}

	private function setSettingId( $id )
	{
		$this->_settingid=$id;
	}

	private function getSettingId()
	{
		return $this->_settingid;
	}

	private function saveModuleSetting()
	{

		if ( $this->rHasVar('new_setting') && $this->rGetVal('new_setting')!="" )
		{
			if ( $this->getModuleSetting( array( "setting"=>$this->rGetVal('new_setting') ) ) !="" )
			{
				$m=$this->getModule();
				$this->addError( sprintf(
					$this->translate( '%s: a setting with that name already exists in  %s.' ),
					$this->rGetVal('new_setting'), $m['module'] )
				);
			}
			else
			{
			    $this->models->ModuleSettings->insert(array(
    				'module_id' => $this->getModuleId(),
    				'setting' => $this->rGetVal('new_setting'),
    				'info' => $this->rGetVal('new_info'),
    				'default_value' => $this->rGetVal('new_default_value')
    			));
/*
			    $this->models->ModuleSettings->freeQuery("
					insert into %PRE%module_settings
						(module_id,setting,info,default_value)
					values
						(" .
							mysql_real_escape_string( $this->getModuleId() ) . "," .
							($this->rGetVal('new_setting')!="" ? "'" . mysql_real_escape_string( $this->rGetVal('new_setting') ) . "'" : "null" ) .",".
							($this->rGetVal('new_info')!="" ? "'" . mysql_real_escape_string( $this->rGetVal('new_info') ) . "'" : "null" ) .",".
							($this->rGetVal('new_default_value')!="" ? "'" . mysql_real_escape_string( $this->rGetVal('new_default_value') ) . "'" : "null" )  ."
						)
				");
*/
                $this->addMessage( sprintf( $this->translate( 'new setting %s saved.' ),  $this->rGetVal('new_setting') ) );
			}
		}


	}

	private function deleteModuleSetting()
	{
		$this->_settings=$this->models->ModuleSettingsValues->delete(
			array(
				"project_id"=>$this->getCurrentProjectId(),
				"setting_id"=>$this->getSettingId()
			));

		$this->_settings=$this->models->ModuleSettings->delete(
			array(
				"module_id"=>$this->getModuleId(),
				"id"=>$this->getSettingId()
			));

		$this->addMessage( $this->translate( 'setting deleted.' ) );
	}

	private function updateModuleInfo( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$info=isset($p['info']) ? $p['info'] : null;

		if ( empty($id) ) return;

		$this->models->ModuleSettings->update(array('info' => $info, 'id' => $id));
/*
		$this->models->ModuleSettings->freeQuery("
			update
				%PRE%module_settings
			set
				info = " . ( !empty($info) ? "'" . mysql_real_escape_string( $info ) . "'" : "null" ) ."
			where
				id = " . mysql_real_escape_string( $id )
		);
*/
	}

	private function updateModuleDefaultValue( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$value=isset($p['value']) ? $p['value'] : null;

		if ( empty($id) ) return;

		$this->models->ModuleSettings->update(array('default_value' => $value, 'id' => $id));

/*
		$this->models->ModuleSettings->freeQuery("
			update
				%PRE%module_settings
			set
				default_value = " . ( $value!="" ? "'" . mysql_real_escape_string( $value ) . "'" : "null" ) ."
			where
				id = " . mysql_real_escape_string( $id )
		);
*/
	}

	private function setModuleSettingValues()
	{
		$this->_settingsvalues = $this->models->ModuleSettingsModel->setModuleSettingValues(array(
            'projectId' => $this->getCurrentProjectId(),
    		'moduleId' => $this->getModuleId()
		));
	}

	private function getModuleSettingValues()
	{
		return $this->_settingsvalues;
	}

	private function getModuleSettingValue( $setting_id )
	{
		if ( empty($setting_id) ) return;

		foreach((array)$this->getModuleSettingValues() as $val)
		{
			if ( $val['setting_id']==$setting_id )
			{
				return $val;
			}
		}
	}



	private function saveModuleSettingValues()
	{

		foreach((array)$this->rGetVal('value') as $setting_id => $val)
		{
			if ( empty($setting_id) ) continue;

			$curr=$this->getModuleSettingValue( $setting_id );

			if ( $val!="" && !empty($curr) && $val != $curr['value'] )
			{
                $this->models->ModuleSettingsValues->update(array(
                    array('value' => $val),
                    array('project_id' => $this->getCurrentProjectId(), 'id' => $curr['value_id'])
                ));
			    /*
			    $this->models->ModuleSettingsValues->freeQuery("
					update
						%PRE%module_settings_values
					set
						value = '" . mysql_real_escape_string( $val ) . "'
					where
						project_id = " . $this->getCurrentProjectId() . "
						and id = " . $curr['value_id'] . "
				");
                */
				$this->addMessage( sprintf( $this->translate( 'value updated to %s.' ),  $val ) );
			}
			else
			if ( $val!="" && empty($curr) )
			{
                $this->models->ModuleSettingsValues->insert(array(
                    'project_id' => $this->getCurrentProjectId(),
                    'setting_id' => $setting_id,
                    'value' => $val
                ));
			    /*
				$this->models->ModuleSettingsValues->freeQuery("
					insert into %PRE%module_settings_values
						(project_id,setting_id,value)
					values
						(" .
							$this->getCurrentProjectId() . "," .
							mysql_real_escape_string( $setting_id ) .",".
							"'" . mysql_real_escape_string( $val ) . "'
						)
				");
                */
				$this->addMessage( sprintf( $this->translate( 'value %s saved.' ),  $val ) );
			}
			else
			if ( $val=="" && !empty($curr) )
			{

				$this->models->ModuleSettingsValues->delete(
					array(
						"project_id"=>$this->getCurrentProjectId(),
						"id"=>$curr["value_id"]
					));

				$this->addMessage( $this->translate( 'value deleted.' ) );
			}
			else
			{
				// no existing value, no new value
			}

		}
	}
}