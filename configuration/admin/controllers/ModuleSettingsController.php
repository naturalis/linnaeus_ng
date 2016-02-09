<?php

/*

	to do technical:
	- admin: replace $this->saveSetting(...);

	- create default settings-set as base data
	- create default value set for new projects

	- find out what the hell this does:
	
			private function initProjectSettings()
			{
		
				foreach((array)$this->_availableProjectSettings as $key=>$val) {
		
					$this->_availableProjectSettings[$key][2] =
						preg_replace_callback(
							'/\%(.*)\%/',
							function($m)
							{
								switch ($m[1]) {
									case 'MEDIA_DIR' :
										return isset($_SESSION['admin']['project']['urls']['project_media']) ? $_SESSION['admin']['project']['urls']['project_media'] : $m[1];
										break;
									case 'ID' :
										return isset($_SESSION['admin']['project']['id']) ? $_SESSION['admin']['project']['id'] : $m[1];
										break;
									default:
										return $m[1];
								}
							},
							$val[2]);
				}
		
			}
		


	to do content:
	- are these still relevant in the new skin:
		species_suppress_autotab_names
		species_suppress_autotab_classification
		species_suppress_autotab_literature
		species_suppress_autotab_media
		species_suppress_autotab_dna_barcodes
		admin_species_allow_embedded_images
		species_default_tab
	- simplify settings (for instance multiple image base URLs)



	// usage
	include_once ('ModuleSettingsReaderController.php');
	
	$this->moduleSettings=new ModuleSettingsReaderController;
	
	// optional:
	$this->moduleSettings->setUseDefaultWhenNoValue( true );
	$this->moduleSettings->assignModuleSettings( $this->settings );
	
	$a=$this->moduleSettings->getModuleSetting('a_setting');
	$b=$this->moduleSettings->getModuleSetting( array( 'setting'=>'a_setting','subst'=>alt_value,'module'=>'other_module'));
	$g=$this->moduleSettings->getGeneralSetting('a_setting');




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
	public $controllerPublicName = 'Settings';
	
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
		if (!defined('GENERAL_SETTINGS_ID')) define('GENERAL_SETTINGS_ID',-1);

		$this->setModules();
	}

    public function indexAction()
    {
        $this->checkAuthorisation();

        $this->setPageName( $this->translate('Settings') );

		$this->smarty->assign('modules',$this->getModules());

		$this->printPage();
    }

    public function settingsAction()
    {
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

		$this->_modules[]=array( "id"=>GENERAL_SETTINGS_ID, "module"=>$this->translate("General settings") );
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
	}

	private function updateModuleDefaultValue( $p )
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$value=isset($p['value']) ? $p['value'] : null;

		if ( empty($id) ) return;

		$this->models->ModuleSettings->update(array('default_value' => $value, 'id' => $id));
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




	/*
		below are semi-manual conversion functions that can be removed once all active 
		projects have run it once. if you do remove them, be sure to also remove:
		- convert_old_settings.tpl
		- convert_old_settings.php
		- `settings` table
	*/
	private function getOldSettings()
	{
		return $this->models->ModuleSettings->freeQuery(array(
			"query"=>"
			select 
				_a.id as old_value_id,
				_a.setting as old_setting,
				CASE _a.setting
					WHEN 'nbc_search_presence_help_url' then 'url_help_search_presence'
					WHEN 'taxa_use_variations' then 'use_taxon_variations'
					WHEN 'taxon_base_url_images_main' then 'base_url_images_main'
					WHEN 'taxon_base_url_images_thumb' then 'base_url_images_thumb'
					WHEN 'taxon_base_url_images_overview' then 'base_url_images_overview'
					WHEN 'taxon_base_url_images_thumb_s' then 'base_url_images_thumb_s'
					WHEN 'nbc_image_root' then 'image_root_skin'
					ELSE _a.setting
				END as old_setting_translated,
				_b.setting as new_setting,
				_a.value as old_value,
				_c.value as new_value,
				_c.id as new_value_id,
				_b.id as new_setting_id
				
			from 
				%PRE%settings _a

			left join %PRE%module_settings _b
				on 
				(CASE _a.setting
					WHEN 'nbc_search_presence_help_url' then 'url_help_search_presence'
					WHEN 'taxa_use_variations' then 'use_taxon_variations'
					WHEN 'taxon_base_url_images_main' then 'base_url_images_main'
					WHEN 'taxon_base_url_images_thumb' then 'base_url_images_thumb'
					WHEN 'taxon_base_url_images_overview' then 'base_url_images_overview'
					WHEN 'taxon_base_url_images_thumb_s' then 'base_url_images_thumb_s'
					WHEN 'nbc_image_root' then 'image_root_skin'
					ELSE _a.setting
				END) = _b.setting

			left join %PRE%module_settings_values _c
				on _b.id=_c.setting_id
				and _c.project_id= ".$this->getCurrentProjectId()." 

			where 
				_a.project_id= ".$this->getCurrentProjectId()." 
			order by 
				_a.setting
			",
			"fieldAsIndex"=>"old_value_id"));
	}

    public function convertOldSettingsAction()
    {
        $this->checkAuthorisation();

        $this->setPageName( 'Convert old general settings to module settings' );

		$s=$this->getOldSettings();
		
        if ( $this->rHasVal('action','convert') )
		{
			$vals=$this->rGetVal('values');
			
			foreach((array)$vals as $old_value_id)
			{
				if ( isset($s[$old_value_id]) )
				{
					$temp=$s[$old_value_id];

					if ( !empty($temp['new_value_id']) )
					{
						$this->models->ModuleSettingsValues->update(array(
							array('value' => $temp['old_value']),
							array('project_id' => $this->getCurrentProjectId(), 'id' => $temp['new_value_id'])
						));

						$this->addMessage( 'value updated' );
					}
					else
					{
						$this->models->ModuleSettingsValues->insert(array(
							'project_id' => $this->getCurrentProjectId(),
							'setting_id' => $temp['new_setting_id'],
							'value' => $temp['old_value']
						));

						$this->addMessage( 'value saved' );
					}
				}
			}
			
			$s=$this->getOldSettings();

		}

        $this->smarty->assign('settings',$s);

        $this->printPage();

    }

}