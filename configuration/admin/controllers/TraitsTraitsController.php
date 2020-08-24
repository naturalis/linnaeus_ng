<?php /** @noinspection PhpMissingParentCallMagicInspection */

/*

	REFAC2015
	dude, are you COMPLETELY deleting trait groups!?
	NO! need to delete traits as well! + texts
	
*/	

include_once ('TraitsController.php');

class TraitsTraitsController extends TraitsController
{

    public $modelNameOverride='TraitsTraitsModel';
	private $_lookupListMaxResults=99999;
	
    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'traits_values',
		'traits_taxon_values',
		'traits_taxon_freevalues',
		'traits_date_formats',
		'text_translations',
    );
   
    public $controllerPublicName = 'Traits';

    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css'
	);

	public $jsToLoad=array(
        'all' => array('traits.js','jquery.mjs.nestedSortable.js')
	);

    public $usedHelpers = array(
        'session_messages'
    );

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
		$this->smarty->assign('defaultMaxLengthStringValue',$this->_defaultMaxLengthStringValue);
		$this->smarty->assign('defaultMaxLengthIntegerValue',$this->_defaultMaxLengthIntegerValue);
		$this->smarty->assign('defaultMaxLengthFloatValue',$this->_defaultMaxLengthFloatValue);
    }

    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Index'));
		$this->printPage();
	}
	
    public function projectTypesAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Project data types'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','add'))
		{
			$this->addDatatypeToProject($this->rGetAll());
			$this->addMessage('Data type added to project.');
		}
		else
		if ($this->rHasVal('action','remove'))
		{
			$this->removeDatatypeFromProject($this->rGetAll());
			$this->addMessage('Data type removed project.');
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->printPage();
    }

    public function traitgroupsAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait groups'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveTraitgroupOrder($this->rGetAll());
			if ($i>0) $this->addMessage('New order saved.');
		}

		$this->smarty->assign('groups',$this->getTraitgroups());
		$this->printPage();
    }

    public function traitgroupAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();
		
		if ($this->rHasVal('action','save'))
		{
			$this->saveTraitgroup($this->rGetAll());
			$this->helpers->SessionMessages->setMessage('Saved.');
			$this->redirect('traitgroups.php');
		}
		else 
		if ($this->rHasVal('action','delete'))
		{
			$this->deleteTraitgroup($this->rGetAll());
			$this->helpers->SessionMessages->setMessage('Group deleted.');
			$this->redirect('traitgroups.php');
		}
		else 
		if (!$this->rHasId())
		{
			$this->smarty->assign('newgroup',true);
			$this->setPageName($this->translate('New trait group'));
		}
		else
		if ($this->rHasId())
		{
			$this->smarty->assign('group',$this->getTraitgroup($this->rGetId()));
			$this->smarty->assign('newgroup',false);
			$this->setPageName($this->translate('Edit trait group'));
		}
		else
		{
			$this->smarty->assign('newgroup',true);
		}

		$this->smarty->assign('groups',$this->getTraitgroups());
		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->printPage();
    }

    public function traitgroupTraitsAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Trait group traits'));

		$this->addMessage($this->helpers->SessionMessages->getMessages());

		if ($this->rHasVal('action','saveorder'))
		{
			$i=$this->saveTraitgroupTraitsOrder($this->rGetAll());
			if ($i>0) $this->addMessage('New order saved.');
		}

		$this->smarty->assign('datatypes',$this->getDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$this->getTraitgroup($this->rGetVal('group')));
		$this->printPage();
    }

    public function traitgroupTraitAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();
		
		if (!$this->rHasId() && !$this->rHasVar('group'))
			$this->redirect('index.php');
		
        $this->setPageName($this->translate('Trait group traits'));

		if ($this->rHasId())
		{
			$trait=$this->getTraitgroupTrait(array('trait'=>$this->rGetId()));
			$group=$this->getTraitgroup($trait['trait_group_id']);
		}
		else
		if ($this->rHasVar('group'))
		{
			$trait=null;
			$group=$this->getTraitgroup($this->rGetVal('group'));
		}
		
		if ($this->rHasVal('action','save'))
		{

			$r=$this->saveTraitgroupTrait($this->rGetAll());
			
			if ($r)
			{
				if ($this->rHasId())
				{
					$this->addMessage('Data saved.');
					$trait=$this->getTraitgroupTrait(array('trait'=>$this->rGetId()));
				}
				else
				{
					$this->helpers->SessionMessages->setMessage('Trait saved.');
					$this->redirect('traitgroup_traits.php?group='.$group['id']);
				}
			}
			else
			{
				$this->helpers->SessionMessages->setMessage('Saving trait failed.');
				$trait=$this->getTraitgroupTrait(array('trait'=>$this->rGetId()));
			}
			
		}
		else
		if ($this->rHasVal('action','delete') && $this->rHasId())
		{
			$this->deleteTraitgroupTrait($this->rGetAll());
			$this->helpers->SessionMessages->setMessage('Trait deleted.');
			$this->redirect('traitgroup_traits.php?group='.$group['id']);
		}

		if (count($this->getProjectDatatypes())==0)
		{
			$this->addError(
				$this->translate('No trait datatypes have been added to your project.').' '.
				'<a href="project_types.php">'. $this->translate('Add datatypes now.').'</a>'
			);
			$this->smarty->assign('cantSave',true);
		}

		$this->smarty->assign('languages',$this->getProjectLanguages());
		$this->smarty->assign('datatypes',$this->getProjectDatatypes());
		$this->smarty->assign('dateformats',$this->getDateFormats());
		$this->smarty->assign('group',$group);
		$this->smarty->assign('trait',$trait);
		$this->printPage();
    }

    public function traitgroupTraitValuesAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );

		$this->checkAuthorisation();
		
		if (!$this->rHasId() && !$this->rHasVar('trait'))
		{
			$this->redirect('index.php');
		}
		
        $this->setPageName($this->translate('Trait values'));

		if ($this->rHasVal('action','save'))
		{
			$this->saveTraitgroupTraitValues($this->rGetAll());
		}

		if ($this->rHasVar('trait'))
		{
			$trait=$this->getTraitgroupTrait( array('trait'=>$this->rGetVal('trait')) );
			$group=$this->getTraitgroup( $trait['trait_group_id'] );
		}

		$this->smarty->assign( 'languages', $this->getProjectLanguages() );
		$this->smarty->assign( 'dateformats', $this->getDateFormats() );
		$this->smarty->assign( 'group', $group );
		$this->smarty->assign( 'trait', $trait );
		$this->printPage();
    }
    
    // Rename system value
    public function traitgroupTraitValueAction ()
    {
        $this->UserRights->setActionType( $this->UserRights->getActionCreate() );
        
        $this->checkAuthorisation();
        
        if (!$this->rHasId()) {
            $this->redirect('index.php');
        }
        
        $this->setPageName($this->translate('Rename trait value'));
        $trait_value = $this->models->TraitsValues->_get(['id' => $this->rGetId()]);

        if ($this->rHasVal('action','save'))  {
            $this->saveTraitgroupTraitValue(['id' => $this->rGetId(), 'sysname' => $this->rGetVal('sysname')]);
            $this->logChange([
                 'before' => $trait_value,
                 'after' => $this->models->TraitsValues->_get(['id' => $this->rGetId()]),
                 'note'=> 'updated trait value ' . $trait_value['string_value']
            ]);
            $this->redirect('traitgroup_trait_values.php?trait=' . $trait_value['trait_id']);
        }
         
        $trait = $this->getTraitgroupTrait(['trait' => $trait_value['trait_id']]);
        $group = $this->getTraitgroup( $trait['trait_group_id'] );
        
        $this->smarty->assign( 'trait_value', $trait_value );
        $this->smarty->assign( 'group', $group );
        $this->smarty->assign( 'trait', $trait );
        $this->printPage();
    }
    
	
    public function settingsAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Traits settings'));
        $this->printPage();
    }
	
    public function ajaxInterfaceAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','verifydate'))
		{
			if (!$this->rGetVal('date') || !$this->rGetVal('format'))
			{
				$r=!$this->rGetVal('date') ? 'missing date' : 'missing format';
			}
			else
			{
				$r=$this->verifyDate($this->rGetVal('date'),$this->rGetVal('format'));
			}

			$this->smarty->assign('returnText',json_encode($r));

		}
		
		$this->printPage();
    }
	

	private function getDatatypes()
	{
		return $this->models->TraitsTraitsModel->getDatatypes(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId()
		));
	}

	private function getProjectDatatypes()
	{
		return $this->models->TraitsTraitsModel->getProjectDatatypes(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId()
		));
	}

	private function getProjectDatatype($type_id)
	{
		return $this->models->TraitsTraitsModel->getProjectDatatype(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId(),
			"type_id"=>$type_id
		));
	}

	private function getDateFormats()
	{
		return $this->models->TraitsDateFormats->_get(array('id'=>'*','order'=>'show_order'));
	}

	private function addDatatypeToProject($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if ( is_null($id) ) return false;

		$this->models->TraitsProjectTypes->save(array(
			'project_id'=>$this->getCurrentProjectId(),
			'type_id'=>(int)$id
		));

		return true;
	}

	private function removeDatatypeFromProject($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if ( is_null($id) ) return false;

		$this->models->TraitsProjectTypes->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>(int)$id
		));
		
		return true;
	}
	
	private function saveTraitgroup($p)
	{
	    $id=isset($p['id']) ? $p['id'] : null;
		$parent_id=!empty($p['parent_id']) ? (int)$p['parent_id'] : 'null';
		$sysname=isset($p['sysname']) ? $p['sysname'] : null;
		$names=isset($p['names']) ? $p['names'] : null;
		$descriptions=isset($p['descriptions']) ? $p['descriptions'] : null;
		$all_link_texts=isset($p['all_link_texts']) ? $p['all_link_texts'] : null;
		$show_in_search=isset($p['show_in_search']) && in_array($p['show_in_search'],array('0','1'))? $p['show_in_search'] : null;
		$show_show_all_link=isset($p['show_show_all_link']) && in_array($p['show_show_all_link'],array('0','1'))? $p['show_show_all_link'] : null;
		$help_link_url=!empty($p['help_link_url']) ? $p['help_link_url'] : 'null';

		if ( is_null($sysname) || !array_filter($names)) return false;
		
		if (!empty($id))
		{
			$before=$this->getTraitgroup($id);
			unset($before['parent']);
		}
		else
		{
			$before=null;
		}

		$this->models->TraitsGroups->save(array(
			'id'=>$id,
			'project_id'=>$this->getCurrentProjectId(),
			'parent_id'=> $parent_id,
			'sysname'=>$this->models->TraitsTraitsModel->escapeString($sysname),
			'show_in_search'=>$show_in_search,
			'show_show_all_link'=>$show_show_all_link,
			'help_link_url'=>$this->models->TraitsTraitsModel->escapeString($help_link_url)
		));

		if (empty($id)) $id=$this->models->TraitsGroups->getNewId();

		$textids=$this->storeTranslations(
			array(
				'record'=>$this->getTraitgroup($id),
				'data'=>array(
					'name_tid'=>$names,
					'description_tid'=>$descriptions,
					'all_link_text_tid'=>$all_link_texts
				)
			)
		);
		
		
		foreach((array)$textids as $col=>$text_id)
		{
			$this->models->TraitsGroups->update(array($col=>$text_id),array('id'=>$id));
		}
	
		$this->logChange(array('before'=>$before,'after'=>$this->getTraitgroup($id),'note'=> (is_null($before) ? 'created' : 'updated') . ' trait group '.$sysname));

		return true;
	}

	private function saveTraitgroupOrder($p)
	{
		$groups=isset($p['sortable']) ? $p['sortable'] : null;

		if (empty($groups))
			return false;
			
		$i=0;
		foreach((array)$groups as $key=>$group)
		{
			$this->models->TraitsGroups->save(array(
				'id'=>$group,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->TraitsGroups->getAffectedRows();
		}
		return $i;
	}

	private function deleteTraitgroup($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;
		
		$g=$this->getTraitgroup($id);

		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'text_id'=>$g['name_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'text_id'=>$g['description_tid']
		));		
					
		$this->models->TraitsGroups->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$id
		));

		$this->models->TraitsGroups->update(
			array('parent_id'=>'null'),
			array('project_id'=>$this->getCurrentProjectId(),'parent_id'=>(int)$id)
		);

		$this->logChange(array('before'=>$g,'note'=> 'deleted trait group '.$g['sysname']));

		return true;
	}

	private function saveTraitgroupTrait($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$trait_group_id=isset($p['trait_group_id']) ? $p['trait_group_id'] : null;
		$project_type_id=isset($p['project_type_id']) ? $p['project_type_id'] : null;
		$date_format_id=isset($p['date_format_id']) ? $p['date_format_id'] : null;
		$sysname=isset($p['sysname']) ? $p['sysname'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$code=isset($p['code']) ? $p['code'] : null;
		$description=isset($p['description']) ? $p['description'] : null;
		$unit=isset($p['unit']) ? $p['unit'] : null;
		$can_be_null=isset($p['can_be_null']) ? $p['can_be_null'] : null;
		$can_select_multiple=isset($p['can_select_multiple']) ? $p['can_select_multiple'] : null;
		$can_include_comment=isset($p['can_include_comment']) ? $p['can_include_comment'] : null;
		$can_have_range=isset($p['can_have_range']) ? $p['can_have_range'] : null;
		$show_index_numbers=isset($p['show_index_numbers']) ? $p['show_index_numbers'] : null;
		$show_order=isset($p['show_order']) && is_numeric($p['show_order'])  ? $p['show_order'] : (empty($id) ? 999 : null);
		$max_length=isset($p['max_length']) && is_numeric($p['max_length']) ? $p['max_length'] : null;
		
		if (empty($trait_group_id)||empty($project_type_id)||empty($sysname)||empty($name))
		{
			if (empty($trait_group_id)) $this->addError($this->translate('Missing group ID.'));
			if (empty($project_type_id)) $this->addError($this->translate('Missing type ID.'));
			if (empty($sysname)) $this->addError($this->translate('Missing system name.'));
			if (empty($name)) $this->addError($this->translate('Missing name.'));
			$this->addError($this->translate('Trait not saved'));
			return;
		}

		$type=$this->getProjectDatatype($project_type_id);

		if (!empty($max_length) && $max_length < 1)
		{
			$max_length=null;
			$this->addWarning($this->translate('Max. length cannot be smaller than 1'));
		}
		else
		if (!empty($max_length))
		{
			
			if (
				 	(
						$type['sysname']=='stringlist' || 
						$type['sysname']=='stringlistfree' || 
						$type['sysname']=='stringfree'
					) &&
			 		$max_length > $this->_defaultMaxLengthStringValue
				)
			{
				$max_length=$this->_defaultMaxLengthStringValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthStringValue));
			}
			else
			if (
				 	(
						$type['sysname']=='intlist' || 
						$type['sysname']=='intlistfree' || 
						$type['sysname']=='intfree' ||
						$type['sysname']=='intfreelimit'
					) &&
			 		$max_length > $this->_defaultMaxLengthIntegerValue
				)
			{
				$max_length=$this->_defaultMaxLengthIntegerValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthIntegerValue));
			}
			else
			if (
				 	(
						$type['sysname']=='floatlist' || 
						$type['sysname']=='floatlistfree' || 
						$type['sysname']=='floatfree' ||
						$type['sysname']=='floatfreelimit'
					) &&
			 		$max_length > $this->_defaultMaxLengthFloatValue
				)
			{
				$max_length=$this->_defaultMaxLengthFloatValue;
				$this->addWarning(sprintf($this->translate('Max. length cannot exceed %s'),$this->_defaultMaxLengthFloatValue));
			}
		}


		$d=array(
				'project_id' => $this->getCurrentProjectId(),
				'trait_group_id' => $trait_group_id,
				'project_type_id' => $project_type_id,
				'date_format_id' => $date_format_id,
				'sysname' => trim($sysname),
				'unit' => $unit,
				'can_select_multiple' => ($can_select_multiple=='y' ? 1 : 0),
				'can_include_comment' => ($can_include_comment=='y' ? 1 : 0),
				'can_be_null' => ($can_be_null=='y' ? 1 : 0),
				'can_have_range' => ($can_have_range=='y' ? 1 : 0),
				'show_index_numbers' => ($show_index_numbers=='y' ? 1 : 0),
				'show_order' => $show_order,
				'max_length' => $max_length
			);

		if (!empty($id)) $d['id']=$id;
		
		if (!empty($id))
		{
			$before=$this->getTraitgroupTrait( [ 'trait'=>$id ] );
		}
		else
		{
			$before=null;
		}

		$d=$this->models->TraitsTraits->save($d);

		if ($d)
		{

			if (empty($id)) $id=$this->models->TraitsTraits->getNewId();
			
			$trait=$this->models->TraitsTraits->_get(array('id'=>array(
				'id'=>$id,
				'project_id'=>$this->getCurrentProjectId(),
			)));
			
			$textids=$this->storeTranslations(
				array(
					'record'=>$trait[0],
					'data'=>array(
						'name_tid'=>$name,
						'code_tid'=>$code,
						'description_tid'=>$description
					)
				)
			);

			foreach((array)$textids as $col=>$text_id)
			{
				$this->models->TraitsTraits->update(array($col=>$text_id),array('id'=>$id));
			}

			$this->logChange( [ 'before'=>$before,'after'=>$this->getTraitgroupTrait( [ 'trait'=>$id ] ),'note'=> (is_null($before) ? 'created' : 'updated' ) . ' trait '.$sysname ] );

			return true;

		}
		else 
		{
			return false;
		}
			
	}

	private function saveTraitgroupTraitsOrder($p)
	{
		$traits=isset($p['sortable']) ? $p['sortable'] : null;

		if (empty($traits))
			return false;
			
		$i=0;
		foreach((array)$traits as $key=>$trait)
		{
			$this->models->TraitsTraits->save(array(
				'id'=>$trait,
				'project_id'=>$this->getCurrentProjectId(),
				'show_order'=>$key
			));
			
			$i+=$this->models->TraitsTraits->getAffectedRows();
		}
		return $i;
	}

	private function deleteTraitgroupTrait($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;

		if (empty($id))
			return false;
		
		$g=$this->getTraitgroupTrait(array('trait'=>$id));
		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['name_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['code_tid']
		));		
		$this->models->TextTranslations->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$g['description_tid']
		));		
		
		$this->models->TraitsTraits->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$id
		));

		return true;
	}
	
	private function saveTraitgroupTraitValue ($p)
	{
	    $id = isset($p['id']) ? $p['id'] : null;
	    $sysname = isset($p['sysname']) ? trim($p['sysname']) : null;
	    
	    if (is_null($id) || is_null($sysname)) {
	        return false;
	    }
	    
	    $this->models->TraitsValues->update(
	        ['string_value' => $sysname], 
	        ['id' => $id, 'project_id' => $this->getCurrentProjectId()]
	    );
	    
	    //echo $this->models->TraitsValues->q(); die();
	}
	
	private function saveTraitgroupTraitValues($p)
	{
		$trait_id=isset($p['trait']) ? $p['trait'] : null;
		$values=isset($p['values']) ? $p['values'] : null;
		$valuelabels=isset($p['valuelabels']) ? $p['valuelabels'] : null;
		$sortable=isset($p['sortable']) ? $p['sortable'] : null;

		if (empty($trait_id))
		{
			if (empty($trait_id)) $this->addError($this->translate('Missing trait ID.'));
			if (empty($values)) $this->addError($this->translate('No values to save.'));
			$this->addError($this->translate('Values not saved'));
			return;
		}
		
		$trait=$this->getTraitgroupTrait($p);

		$base=
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_id'=>$trait['id']
			);

		$before=$this->getTraitgroupTraitValues( [ 'trait' => $trait_id ] );

		// get the current values
		$current=$this->models->TraitsValues->_get( [ 'id' => $base ] );
		
		if ($trait['type_sysname']=='stringlist' || $trait['type_sysname']=='stringlistfree')
		{

			$index=array();
			$showorder=0;
			foreach((array)$values as $key=>$val)
			{
				$base['show_order']=$showorder++;
				$base['string_value']=trim($val);
				
				if ($key>=0)
				// update
				{
					$this->models->TraitsValues->update($base,array('id'=>$key,'project_id'=>$this->getCurrentProjectId()));
					$value=$this->models->TraitsValues->_get(array('id'=>$key));
					
					if (isset($valuelabels[$key]))
					{
						$textids=$this->storeTranslations(
							array(
								'record'=>$value,
								'data'=>array('string_label_tid'=>$valuelabels[$key])
							)
						);
				
						foreach((array)$textids as $col=>$text_id)
						{
							$this->models->TraitsValues->update(array($col=>$text_id),array('id'=>$key));
						}
					}

				}
				else
				// insert
				{
					$this->models->TraitsValues->save($base);
					$id=$this->models->TraitsValues->getNewId();
					
					if (isset($valuelabels[$key]))
					{
						$textids=$this->storeTranslations(
							array(
								'record'=>array('string_label_tid'=>null), // all new values, none have a text_id yet
								'data'=>array('string_label_tid'=>$valuelabels[$key],)
							)
						);
				
						foreach((array)$textids as $col=>$text_id)
						{
							$this->models->TraitsValues->update(array($col=>$text_id),array('id'=>$id));
						}
					}
				}
			}

			// delete previous values that are no longer part of the new set
			foreach((array)$current as $key=>$val)
			{
				if (!array_key_exists($val['id'],(array)$values))
				{
					$this->deleteTranslations(array('text_id'=>$val['string_label_tid']));
					$this->models->TraitsValues->delete(array('id'=>$val['id'],'project_id'=>$this->getCurrentProjectId()));
				}
			}

			$this->addMessage( ($this->translate('Values saved' ) ) );
		}
		else
		if (
			$trait['type_sysname']=='intlist' || $trait['type_sysname']=='intlistfree' ||
			$trait['type_sysname']=='floatlist' || $trait['type_sysname']=='floatlistfree'
			)
		{
			$this->models->TraitsValues->delete($base);

			$saved=0;

			foreach((array)$values as $key=>$val)
			{
				$d=$base+
					array(
						'numerical_value'=>trim($val),
						'show_order'=>$key
					);
				
				$r=$this->models->TraitsValues->save($d);
				
				if (!$r)
				{
					$this->addError(sprintf($this->translate('Value %s not saved'),$val));
				}
				else
				{
					$saved++;
				}

			}
			
			$this->addMessage(sprintf($this->translate('%s values saved'),$saved));

		}
		else
		if ($trait['type_sysname']=='datelist' || $trait['type_sysname']=='datelistfree')
		{
			$this->models->TraitsValues->delete($base);

			$saved=0;

			foreach((array)$values as $key=>$val)
			{
				$r=date_parse_from_format($trait['date_format_format'],$val);
				
				if ($r['error_count']==0)
				{
					/*
						we want to be able to do:
						  date_format(date_create($row['date']),'Y');
						and since
						  date_format(date_create('1996-00-00'),'Y')
						outputs "1995" (1995-11-30, even!), we default empty months and
						days to 01 rather than 00 to avoid unpleasentness. the distinction
						between '1996-01-01' equalling 'january 1st 1996' or '1996' (or
						'january 1996') is made based upon te chosen date format of the
						trait (Y, Y-m-d or Y-m).

						column is `date` so the time parts are somewhat pointless
					*/
					$d=$base+
						array(
							'date'=>$this->makeInsertableDate($val,$trait['date_format_format']),
							'show_order'=>$key
						);

					$r=$this->models->TraitsValues->save($d);
				}
				else
				{
					$r=false;
				}
				
				
				if (!$r)
				{
					$this->addError(sprintf($this->translate('Value %s not saved'),$val));
				}
				else
				{
					$saved++;
				}

			}
			
			$this->addMessage(sprintf($this->translate('%s values saved'),$saved));

		}

		$after=$this->getTraitgroupTraitValues( [ 'trait'=>$trait_id ] );
		
		$this->logChange( [ 'before'=>$before,'after'=>$after,'note'=> 'updated values for trait '.$trait['sysname'] ] );
		
		if ( !is_null($sortable) )
		{
			foreach((array)$sortable as $key=>$val)
			{
				$this->models->TraitsValues->update(
					[ 'show_order'=>$key ],
					[ 'project_id'=>$this->getCurrentProjectId(),'id'=>$val, 'trait_id'=>$trait['id'] ]
				);
			}

			$this->addMessage( $this->translate('Saved show order.') );
		}
	}

	private function verifyDate($date,$format)
	{
		$r=date_parse_from_format($format,$date);

		if ($r['error_count']==0)
		{
			return true;
		}
		else
		{
			return implode("\n",$r['errors']);
		}
	}

	private function getNextTextId()
	{
		$d=$this->models->TextTranslations->_get(array(
			'columns'=>'ifnull(max(text_id)+1,1) as next',
			'id'=>array('project_id'=>$this->getCurrentProjectId())
		));
		return $d[0]['next'];
	}

	private function storeTranslations($p)
	{
		$record=isset($p['record']) ? $p['record'] : null;
		$data=isset($p['data']) ? $p['data'] : null;

		/*		
		array(
			'record'=>$this->getTraitgroup(), // any record with column x_tid ref. text_id
			'data'=>array(
				'x_tid'=>$xs, // array(language_id=>translation,language_id=>translation)
				...=>...
			)
		*/

		if (empty($record)) return;

		$index=array();

		foreach((array)$data as $column=>$translations)
		{
			foreach((array)$translations as $language=>$translation)
			{

				if (!array_key_exists($column,$record)) continue;

				$newTextId=false;
				
				// make sure this record has a valid text_id (regardless of language)
				$text_id=$record[$column];

				if (empty($text_id) && isset($index[$column]))
				{
					$text_id=$index[$column];
				}
				
				if (empty($text_id))
				{
					$text_id=$this->getNextTextId();
					$newTextId=true;
				}
				else
				{
					$d=$this->models->TextTranslations->_get(array('id'=>array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id)));
					if (empty($d))
					{
						$newTextId=true;
					}
				}

				if ($newTextId)
				{
					$this->models->TextTranslations->save(array(
						'project_id'=>$this->getCurrentProjectId(),
						'language_id'=>$language,
						'text_id'=>$text_id,
						'translation'=>$translation
					));
				}
				
				$index[$column]=$text_id;
				
				if ($newTextId)
				{
					// we already inserted this first translation, to get a new text_id
					continue;
				}

				$base=array('project_id'=>$this->getCurrentProjectId(),'text_id'=>$text_id,'language_id'=>$language);

				// see if a translation exist for this combination of text_id and language
				$d=$this->models->TextTranslations->_get(array('id'=>$base));

				$base+=array('translation'=>(empty($translation) ? 'null' : $translation));

				// update translation if the combination text_id+language already exists
				if (!empty($d))
				{
					$this->models->TextTranslations->update($base,array('id'=>$d[0]['id']));
				}
				else
				// insert if it doesn't
				{
					$this->models->TextTranslations->save($base);
				}
				
				//echo $text_id,$column,$language,'<br />';
			}
		}

		return $index;
	}

	private function deleteTranslations($p)
	{
		$text_id=isset($p['text_id']) ? $p['text_id'] : null;
		$language=isset($p['language']) ? $p['language'] : null;

		if (empty($text_id)) return;
		
		$base=
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'text_id'=>$text_id
			);

		if (!empty($language)) $base+=array('language_id'=>$language);

		$d=$this->models->TextTranslations->delete($base);
	}

}



