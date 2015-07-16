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