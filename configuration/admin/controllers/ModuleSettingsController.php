<?php /** @noinspection PhpMissingParentCallMagicInspection */
/**
 * Module Settings Controller, has access to module specific settings
 *
 * Settings that work but not in the list:
 * 	general: support_email (support email adress)
 * 	general: show_hidden_modules_in_select_list (toggle for showing "show_in_menu=false" modules in project selection list)
 * 	general: admin_message_fade_out_delay (delay in ms before the admin messaged fade out)
 * 	general: front_end_use_basic_auth
 *
 * 	species: 404_content ('{"title":"Page not found","body":"The requested page could not be found."}' )
 * 	species: use_embedded_templates (allow embedded templates in passports; see SpeciesController::processEmbeddedTemplates   )
 * 	species: use_page_blocks (allow "building" passport pages from other pages; see SpeciesController::buildPageFromBlocks   )
 * 	species: show_inherited_literature (also show links to literature about taxa higher up in the classification on the literature tab.)
 * 	species: tree_taxon_count_style (possible values: species_only (show only species count), species_established (species count & established count), none (removes count altogether))
 * 	species: tree_initital_expand_levels (initial taxon tree auto expansion for n levels
 * 	species: show_inherited_literature
 * 	species: suppress_parent_child_relation_checks (suppresses the strenuous checks on parent/child relations while editing taxonomy. only requirement is that the rank of a taxon is below that of its parent.)
 *
 * 	search: show_presence_in_results (show the presence status in results)
 * 	search: show_all_preferred_names_in_results (show all preferred names in results, not just the one in the active language)
 *
 * @todo: technical
 * - create default settings-set as base data
 * - create default value set for new projects
 *
 * @todo: content
 * - simplify settings (for instance multiple image base URLs)
 *
 * @todo: move the traits settings!
 *
 * @usage
 * include_once ('ModuleSettingsReaderController.php');
 *
 * $this->moduleSettings=new ModuleSettingsReaderController;
 *
 * // optional:
 * $this->moduleSettings->setController( 'species' );
 * $this->moduleSettings->setUseDefaultWhenNoValue( true );
 * $this->moduleSettings->assignModuleSettings( $this->settings );
 *
 * $a=$this->moduleSettings->getModuleSetting('a_setting');
 * $b=$this->moduleSettings->getModuleSetting( array( 'setting'=>'a_setting','subst'=>alt_value,'module'=>'other_module'));
 * $g=$this->moduleSettings->getGeneralSetting('a_setting');
 *
 *  values are max 512 characters!
 *
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

    /**
     * ModuleSettingsController constructor.
     */
    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    /**
     * ModuleSettingsController destructor.
     */
    public function __destruct ()
    {
        parent::__destruct();
    }

    /**
     * ModuleSettingsController initialisation
     */
	private function initialize()
	{
		if (!defined('GENERAL_SETTINGS_ID')) define('GENERAL_SETTINGS_ID',-1);

		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );	

		$this->setModules();
	}

    /**
     * ModuleSettingsController showing the settings page
     */
    public function indexAction()
    {
        $this->checkAuthorisation();

        $this->setPageName( $this->translate('Settings') );

		$this->smarty->assign('modules',$this->getModules());

		$this->printPage();
    }

    /**
     * ModuleSettingsController change settings
     */
    public function settingsAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );	
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

        $this->setPageName( sprintf( $this->translate('Module settings: %s ') , $m['module'] ), $this->translate('Module settings') );

		$this->smarty->assign( 'module', $this->getModule() );
		$this->smarty->assign( 'settings', $this->getModuleSettings() );

		$this->printPage();
    }

    /**
     * ModuleSettingsController change values of settings
     */
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
			$this->saveModuleSettingValues( $this->rGetVal('value') );
			$this->setModuleSettingValues();
		}

		$m=$this->getModule();

        $this->setPageName( sprintf( $this->translate('Module settings: %s ') , $m['module'] ), $this->translate('Module settings') );

		$this->smarty->assign( 'module', $this->getModule() );
		$this->smarty->assign( 'settings', $this->getModuleSettings() );
		$this->smarty->assign( 'values', $this->getModuleSettingValues() );

		$this->printPage();
    }

    /**
     * ModuleSettingsController edit settings asynchronous
     */
    public function ajaxInterfaceAction ()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );	
		if ( !$this->getAuthorisationState() ) return;

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


    /**
     * set the Modules
     */
    private function setModules()
	{
		$this->_modules = $this->models->ModuleSettingsModel->setModules();

		$this->_modules[]=array( "id"=>GENERAL_SETTINGS_ID, "module"=>$this->translate("General settings") );
	}

    /**
     * get the Modules
     * @return array
     */
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

    /**
     * set the Module settings
     */
    private function setModuleSettings()
	{
		$this->_settings=$this->models->ModuleSettings->_get(array(
			"id"=> array("module_id"=>$this->getModuleId()),
			"order"=>"setting"
		));

	}

	private function getModuleSettings()
	{
		return $this->_settings;
	}

	private function getModuleSetting( $p , $default = null)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$setting=isset($p['setting']) ? $p['setting'] : null;

		if ( empty($id) && empty($setting) ) {
		    return $default;
        }

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

		return $default;
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
		if ( $this->rHasVar('new_setting') && trim($this->rGetVal('new_setting'))!="" )
		{
			$new_setting=trim($this->rGetVal('new_setting'));
			
			if ( $this->getModuleSetting( array( "setting"=>$new_setting ) ) !="" )
			{
				$m=$this->getModule();
				$this->addError( sprintf(
					$this->translate( '%s: a setting with that name already exists in  %s.' ),
					$new_setting, $m['module'] )
				);
			}
			else
			{
				$d=[
    				'module_id' => $this->getModuleId(),
    				'setting' => $new_setting,
    				'info' => $this->rGetVal('new_info'),
    				'default_value' => $this->rGetVal('new_default_value')
				];

			    $this->models->ModuleSettings->insert($d);
                $this->addMessage( sprintf( $this->translate( 'new setting %s saved.' ), $new_setting ) );
				$this->logChange( array( 'after'=>$d, 'note'=>sprintf( 'New setting "%s"', $new_setting ) ) );
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
			
		$d=[
			"module_id"=>$this->getModuleId(),
			"id"=>$this->getSettingId()
		];

		$b=$this->models->ModuleSettings->_get( [ "id"=>$d ] )[0];
		$this->_settings=$this->models->ModuleSettings->delete($d);
		$this->addMessage( $this->translate( 'setting deleted.' ) );
		$this->logChange(array('before'=>$b,'note'=>sprintf('Deleted setting "%s"', $b['setting'])));
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

	private function saveModuleSettingValues( $values )
	{

		foreach((array)$values as $setting_id => $val)
		{
			if ( empty($setting_id) ) continue;

			$curr=$this->getModuleSettingValue( $setting_id );

			if ( $val!="" && !empty($curr) && $val != $curr['value'] )
			{
                $this->models->ModuleSettingsValues->update(
                    array('value' => $val),
                    array('project_id' => $this->getCurrentProjectId(), 'id' => $curr['value_id'])
                );

				$this->addMessage( sprintf( $this->translate( 'value updated to %s.' ),  $val ) );
				$note='Updated value for %s setting "%s"';
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
				$note='Saved value for %s setting "%s"';
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
				$note='Deleted value for %s setting "%s"';
			}
			else
			{
				// no existing value, no new value
				unset($note);
			}

			$this->setModuleSettingValues();
			$a=$this->getModuleSettingValue( $setting_id );
			$setting=$this->getModuleSetting( ["id"=>(isset($a['setting_id']) ? $a['setting_id'] : $curr['setting_id'])] );
			if (isset($note))
			{
				$this->logChange(array('before'=>$curr,'after'=>$a,'note'=>sprintf($note,$this->getModule()['module'], $setting['setting'])));
			}
		}
	}




    /**
     * getOldSettings is the old way settings were set in modules
     *
     * below are semi-manual conversion functions that can be removed once all active
     * projects have run it once. if you do remove them, be sure to also remove:
     * - convert_old_settings.tpl
     * - convert_old_settings.php
     * - `settings` table
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

    /**
     * convert Old settings to the new settings
     */
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