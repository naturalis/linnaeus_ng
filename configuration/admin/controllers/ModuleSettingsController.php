<?php

/*

drop table `module_settings`;

create table `module_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `module` varchar(64) NOT NULL,
  `lng_id` int(11) NULL,
  `item_type` varchar(64) NULL,
  `setting` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `module_settings_1` (`project_id`),
  KEY `module_settings_2` (`project_id`,`module`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
;

truncate module_settings ;
insert into module_settings values
(null,65,'matrixkey',null,null,'state_image_max_height',300,now(),now()),
(null,65,'matrixkey',null,null,'browse_style','expand',now(),now()),
(null,65,'matrixkey',null,null,'state_image_per_row',4,now(),now()),
(null,65,'matrixkey',null,null,'items_per_page',16,now(),now()),
(null,65,'matrixkey',null,null,'items_per_line',4,now(),now()),
(null,65,'matrixkey',null,null,'use_character_groups',1,now(),now()),
(null,65,'matrixkey',null,null,'allow_empty_species',1,now(),now()),
(null,65,'matrixkey',null,null,'calc_char_h_val',0,now(),now()),
(null,65,'matrixkey',null,null,'use_emerging_characters',1,now(),now()),
(null,65,'matrixkey',null,null,'score_threshold',100,now(),now()),
(null,65,'matrixkey',null,null,'img_to_thumb_regexp_pattern',"/http:\\/\\/images.naturalis.nl\\/original\\//",now(),now()),
(null,65,'matrixkey',null,null,'img_to_thumb_regexp_replacement',"http://images.naturalis.nl/comping/",now(),now())
;


*/

include_once ('Controller.php');

class ModuleSettingsController extends Controller
{

	public $usedModels = array('module_settings');
	public $controllerPublicName = 'Module Settings';
	public $cssToLoad = array();
	public $jsToLoad = array();
	
	private $_modules;
	private $_module;
	private $_settings;


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
	
    public function moduleAction()
    {
        $this->checkAuthorisation();
        
		$this->setModule( $this->rGetVal('module') );
		
        $this->setPageName( sprintf( $this->translate('Module settings: %s ') , $this->getModule() ));
        
		if ( !$this->rHasVar('module') )
		{
			$this->redirect('index.php');
		}

		if ( $this->rHasVal('action','save') )
		{
			$this->saveValues();
		}

		$this->setModuleSettings();
		
		$this->smarty->assign('module',$this->rGetVal('module'));
		$this->smarty->assign('settings',$this->getModuleSettings());
		
		$this->printPage();
    }
	
	private function setModules()
	{
		$this->_modules=$this->models->ModuleSettings->freeQuery("
			select
				distinct module
			from
				%PRE%module_settings
			where
				project_id = " . $this->getCurrentProjectId() . "
			order by
				module
		");
	}

	private function getModules()
	{
		return $this->_modules;
	}

	private function setModule( $module )
	{
		$this->_module=$module;
	}

	private function getModule()
	{
		return $this->_module;
	}

	private function setModuleSettings()
	{
		$this->_settings=$this->models->ModuleSettings->freeQuery("
			select
				*
			from
				%PRE%module_settings
			where
				project_id = " . $this->getCurrentProjectId() . "
				and module = '" . mysql_real_escape_string( $this->getModule() ) . "'
		");
	}

	private function getModuleSettings()
	{
		return $this->_settings;
	}
	
	private function getModuleSetting( $setting )
	{
		$this->setModuleSettings();

		foreach((array)$this->getModuleSettings() as $val)
		{
			if ($val['setting']==$setting)
			{
				return $val['value'];
			}
		}
	}
	
	private function saveValues()
	{
		$this->setModuleSettings();

		$curr=$this->getModuleSettings();

		$new=$this->rGetVal('setting');
		
		foreach((array)$new as $key=>$val)
		{
			if ( empty($key) ) continue;
			
			foreach((array)$curr as $cur)
			{
				if ($key==$cur['id'] && $val!=$cur['value'])
				{
					if ( !empty($val) )
					{
						$this->_settings=$this->models->ModuleSettings->freeQuery("
							update
								%PRE%module_settings
							set
								value='" . mysql_real_escape_string($val) . "'
							where
								project_id = " . $this->getCurrentProjectId() . "
								and module = '" . mysql_real_escape_string( $this->getModule() ) . "'
								and id = " . mysql_real_escape_string( $key ) ." 
						");				

						$this->addMessage( sprintf( $this->translate( '%s updated to %s.' ), $cur['setting'], $val ) );
								
					}
					else
					{
						$this->_settings=$this->models->ModuleSettings->freeQuery("
							delete from %PRE%module_settings
							where
								project_id = " . $this->getCurrentProjectId() . "
								and module = '" . mysql_real_escape_string( $this->getModule() ) . "'
								and id = " . mysql_real_escape_string( $key ) ." 
						");	

						$this->addMessage( sprintf( $this->translate( '%s deleted.' ), $cur['setting'] ) );
							
					}
				}
			}
		}
		
		if (
			$this->rHasVar('new_setting') && !empty($this->rGetVal('new_setting')) &&
			$this->rHasVar('new_value') && !empty($this->rGetVal('new_value'))
		)
		{
			if ( $this->getModuleSetting( $this->rGetVal('new_setting') ) )
			{
				$this->addError( sprintf( $this->translate( '%s: a setting with that name already exists.' ), $this->rGetVal('new_value') ) );
			}
			else
			{
				$this->_settings=$this->models->ModuleSettings->freeQuery("
					insert into %PRE%module_settings
						(project_id,module,setting,value)
					values
						(" . 
							$this->getCurrentProjectId() . ", '" . 
							mysql_real_escape_string( $this->getModule() ) . "','" . 
							mysql_real_escape_string( $this->rGetVal('new_setting') ) . "','" . 
							mysql_real_escape_string( $this->rGetVal('new_value') ) . "'
						)
				");	

				$this->addMessage(
					sprintf(
						$this->translate( 'new setting %s saved with value %s.' ),  $this->rGetVal('new_setting'), $this->rGetVal('new_value')
					)
				);
			}
		}
		
		
	}


}