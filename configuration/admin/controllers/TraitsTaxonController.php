<?php 

include_once ('TraitsController.php');

class TraitsTaxonController extends TraitsController
{
    public $usedModels = array(
		'traits_settings',
		'traits_groups',
		'traits_types',
		'traits_project_types',
		'traits_traits',
		'text_translations',
		'traits_values',
		'traits_taxon_values',
		'traits_taxon_freevalues',
		'traits_taxon_references',
		'literature2',
		'literature2_authors'
    );
   
    public $modelNameOverride='TraitsTaxonModel';
    public $controllerPublicName = 'Traits';
	public $controllerBaseName='traits';

    public $extraModels = array(
        'TraitsModel'
    );

    public $cssToLoad = array(
		'traits.css',
//		'taxon_groups.css',
		'nsr_taxon_beheer.css'
	);

	public $jsToLoad=array(
        'all' => array(
			'traits.js',
			'jquery.mjs.nestedSortable.js',
			'nsr_taxon_beheer.js'
		)
	);

    public $usedHelpers = array(
        'session_messages'
    );

    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function taxonAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();
		$this->setPageName( $this->translate('Taxon trait data') );

        if ($this->rHasVal('action','savereferences'))
        {
            $this->saveReferences(array(
                'taxon'=>$this->rGetVal( 'id' ),
                'group'=>$this->rGetVal( 'group' ),
                'references'=>$this->rGetVal( 'references' )
            ));
            $this->addMessage('Saved');
        }
        else
        if ($this->rHasVal('action','deletegroup') && $this->rHasVal('id') && $this->rHasVal('group'))
		{
			$this->deleteTaxonTraitData(array('taxon'=>$this->rGetVal( 'id' ),'group'=>$this->rGetVal( 'group' )));
			$this->deleteReferences(array('taxon'=>$this->rGetVal( 'id' ),'group'=>$this->rGetVal( 'group' )));
			$this->addMessage('Deleted');
		} 
		else
		if ($this->rHasVal('action','deleteall') && $this->rHasVal('id'))
		{
			// not actually implemented
			$this->deleteTaxonTraitData(array('taxon'=>$this->rGetVal( 'id' )));
			$this->deleteReferences(array('taxon'=>$this->rGetVal( 'id' )));
			$this->addMessage('Deleted');
		} 
		else
		if ($this->rHasVal('action','save') && $this->rHasId() && $this->rHasVal('group'))
		{
			$this->saveTaxonTraitData(array(
				'taxon'=>$this->rGetId(),
				'trait'=>$this->rGetVal('trait'),
				'values'=>$this->rGetVal('values'),
				'value_start'=>$this->rGetVal('value_start'),
				'value_end'=>$this->rGetVal('value_end'),
			));

			// Possibly references have been set before the trait was edited;
            // if so, they have been appended to this form.
            if (!empty($this->rGetVal( 'references' ))) {
                $this->saveReferences(array(
                    'taxon'=>$this->rGetVal( 'id' ),
                    'group'=>$this->rGetVal( 'group' ),
                    'references'=>$this->rGetVal( 'references' )
                ));

            }

			$this->addMessage('Saved');
		}
		
		if ($this->rHasId('id') && $this->rHasVal('group'))
		{
			$this->smarty->assign('references',$this->getReferences(array('taxon'=>$this->rGetId(),'group'=>$this->rGetVal('group'))));
			$this->smarty->assign('group',$this->getTraitgroup($this->rGetVal('group')));
			$this->smarty->assign('traits',$this->getTraitgroupTraits($this->rGetVal('group')));
			$this->smarty->assign('concept',$this->getTaxonById($this->rGetId()));
			$this->smarty->assign('values',$this->getTaxonValues(array('taxon'=>$this->rGetId(),'group'=>$this->rGetVal('group'))));
		}

		$this->printPage();
    }

    public function taxaAction()
    {
		$this->checkAuthorisation();
		$this->setPageName( $this->translate('Taxon trait data') );
		
		if ( $this->rHasVal('trait') ) 
		{
			$taxa=$this->models->TraitsTaxonModel->getTaxaTraitValues( [
				'project_id'=>$this->getCurrentProjectId(),
				'language_id' => $this->getDefaultProjectLanguage(),
				'trait_id'=>$this->rGetVal('trait')
			] );
			
			if ($taxa)
			{
				
				$group=$this->models->TraitsModel->getTraitgroup( [
					'project_id'=>$this->getCurrentProjectId(),
					'language_id' => $this->getDefaultProjectLanguage(),
					'group_id'=>$taxa[0]['trait_group_id']
				] );				
				
				$this->smarty->assign('group',$group);
				$this->smarty->assign('taxa',$taxa);
			}
			
		}

		$this->printPage();
    }

    public function ajaxInterfaceAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','get_taxon_trait'))
		{
			$d=$this->getTaxonValues( $this->rGetAll() );

			$this->smarty->assign('returnText',json_encode(array(
				'trait'=>$this->getTraitgroupTrait( $this->rGetAll() ),
				'taxon_values'=>isset($d[0]) ? $d[0] : null,
				'default_project_language' => $this->getDefaultProjectLanguage()
			)));
		}

		$this->printPage('ajax_interface');
    }

    public function getTaxonValues( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$group=isset($p['group']) ? $p['group'] : null;

		$language=$this->getDefaultProjectLanguage();

		if ( empty($taxon) || empty($language) )
		{
			return;
		}

		$r=$this->models->TraitsTaxonModel->getTraitsTaxonValues(array(
			'language_id'=>$language,
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon,
			'trait_id'=>$trait,
			'group_id'=>$group
		));

		return $this->formatTraitsTaxonValues($r);
	}

	public function formatTraitsTaxonValues ($r)
    {
        $data=array();

        $d=array();

        foreach((array)$r as $key=>$val)
        {
            $d[$val['trait_id']]['trait']=
                array(
                    'id'=>$val['trait_id'],
                    'sysname'=>$val['trait_sysname'],
                    'name'=>$val['trait_name'],
                    'code'=>$val['trait_code'],
                    'description'=>$val['trait_description'],
                    'type'=>$val['trait_type_sysname'],
                );

            if (!empty($val['_date_value']))
                $val['value_start']=$this->formatDbDate($val['_date_value'],$val['_date_format']);
            if (!empty($val['_date_value_end']))
                $val['value_end']=$this->formatDbDate($val['_date_value_end'],$val['_date_format']);

            $d[$val['trait_id']]['values'][]=
                array(
                    'id'=>$val['id'],
                    'value_id'=>$val['value_id'],
                    'value_start'=>$val['value_start'],
                    'value_end'=>$val['value_end'],
                );
        }

        foreach($d as $val)
        {
            $data[]=$val;
        }

        return $data;
    }

	private function saveTaxonTraitData( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$values=isset($p['values']) ? $p['values'] : null;
		$value_start=isset($p['value_start']) ? $p['value_start'] : null;
		$value_end=isset($p['value_end']) ? $p['value_end'] : null;

		if ( empty($taxon) || empty($trait) )
		{
			return;
		}

		$existing=$this->getTaxonValues(array('taxon'=>$taxon,'trait'=>$trait));
		$t=$this->getTraitgroupTrait(array('trait'=>$trait));
		$this->getTraitsSettings();

		if ($existing)
		{
			$existing=$existing[0];
		}

		$func=array($this,$t['type_verification_function_name']);

		if (!is_callable($func))
		{
			$this->addWarning( sprintf($this->translate('No check function for type %s'),$t['type_sysname']) );
		}
								
		if ( $t['type_sysname']=='stringfree' )
		{

			if (isset($existing['values'][0]['id']))
			{
				if (empty($values[0]))
				{
					$this->models->TraitsTaxonFreevalues->delete(array(
						'project_id'=>$this->getCurrentProjectId(),
						'id'=>$existing['values'][0]['id']
					));
				}
				else
				{
					if (is_callable($func))
					{
						$r=call_user_func( $func,array('value'=>$values[0],'trait'=>$t) );

						if (!$r['pass'])
						{
							$this->addError( $r['error'] );
							$this->addError(sprintf($this->translate('Value "%s" didn\'t pass check function'),$values[0]));
							return;
						}
					}
					
					$this->models->TraitsTaxonFreevalues->save(array(
						'project_id'=>$this->getCurrentProjectId(),
						'id'=>$existing['values'][0]['id'],
						'taxon_id'=>$taxon,
						'trait_id'=>$trait,
						'string_value'=>$values[0]
					));
				}
			}
			else
			{
				if (!empty($values[0]))
				{

					if (is_callable($func))
					{
						$r=call_user_func( $func,array('value'=>$values[0],'trait'=>$t) );

						if (!$r['pass'])
						{
							$this->addError( $r['error'] );
							$this->addError(sprintf($this->translate('Value "%s" didn\'t pass check function'),$values[0]));
							return;
						}
					}
					
					$this->models->TraitsTaxonFreevalues->save(array(
						'project_id'=>$this->getCurrentProjectId(),
						'taxon_id'=>$taxon,
						'trait_id'=>$trait,
						'string_value'=>$values[0]
					));
				}
			}
		}
		else
		if ( $t['type_sysname']=='datefree' || $t['type_sysname']=='floatfree' )
		{

			$passed=true;

			foreach((array)$value_start as $key=>$val)
			{
				if (is_callable($func))
				{
					$dummy=$val.(isset($value_end) && !empty($value_end[$key]) ? '-'.$value_end[$key] : '' );

					$r=call_user_func( $func,array('value'=>$dummy,'trait'=>$t) );

					if (!$r['pass'])
					{
						$this->addError( $r['error'] );
						$this->addError(sprintf($this->translate('Value "%s" didn\'t pass check function'),$dummy));
						$passed=false;
						continue;
					}
				}
			}
			
			if ( !$passed ) return;

			$this->models->TraitsTaxonFreevalues->delete(array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$taxon,
				'trait_id'=>$trait,
			));

			foreach((array)$value_start as $key=>$val)
			{
				if (empty($val) && (!isset($value_end) || empty($value_end[$key])))
					continue;
				
				$d=array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$taxon,
					'trait_id'=>$trait,
				);

				if ($t['type_sysname']=='datefree') {
                    $d['date_value'] = $this->makeInsertableDate( $val, $t['date_format_format'] );
                    if (isset($value_end) && !empty($value_end[$key])) {
                        $d['date_value_end'] = $this->makeInsertableDate($value_end[$key], $t['date_format_format']);
                    }
                } else {
                    $d['numerical_value'] = $val;
                    if (isset($value_end) && !empty($value_end[$key])) {
                        $d['numerical_value_end'] = $value_end[$key];
                    }
               }
				


				$this->models->TraitsTaxonFreevalues->save( $d );
			}
		}
		else
		if ( $t['type_sysname']=='stringlist' )
		{
			if (isset($existing['values']))
			{
				foreach((array)$existing['values'] as $key=>$val)
				{
					$this->models->TraitsTaxonValues->delete(array(
						'project_id'=>$this->getCurrentProjectId(),
						'id'=>$val['id']
					));
				}
			}
	
			foreach((array)$values as $val)
			{
				/*
				
				// no values are being passed! just id's
				if (is_callable($func))
				{
					$r=call_user_func( $func,array('value'=>$val,'trait'=>$t) );

					if (!$r['pass'])
					{
						$this->addError( $r['error'] );
						$this->addError(sprintf($this->translate('Value "%s" didn\'t pass check function'),$val));
						continue;
					}
				}
				*/
				
				$this->models->TraitsTaxonValues->save(array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$taxon,
					'value_id'=>$val
				));
			}		
		}
		
		
		$after=$this->getTaxonValues(array('taxon'=>$taxon,'trait'=>$trait));
		$taxon=$this->getTaxonById( $taxon );

		$this->logChange( [ 'before'=>$existing,'after'=>$after,'note'=> 'updated values for trait ' . $t['sysname'] . ' for ' . $taxon['nomen'] ] );

	}

	private function getReferences( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;

		if ( empty($taxon) || empty($group) )
			return;

		$l=$this->models->TraitsTaxonModel->getLiterature2(array(
			'trait_group_id'=>$group,
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon
		));

		foreach((array)$l as $key=>$val)
		{
			$authors=$this->models->TraitsTaxonModel->getLiterature2Authors(array(
				'project_id'=>$this->getCurrentProjectId(),
				'literature2_id'=>$val['literature_id']
			));
		
			$l[$key]['authors']=$authors;
			
		}
	
		return $l;
	}

	private function saveReferences( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;
		$references=isset($p['references']) ? $p['references'] : null;

		if ( empty($taxon) || empty($group) )
			return;

		$before=$this->getReferences( $p );

		$keep=array(-1);
		$new=array();

		foreach((array)$references as $val)
		{
			$d=explode(",",$val);

			if ($d[0]==-1)
				$new[]=$d[1];
			else
				$keep[]=$d[0];
		}

		$this->models->TraitsTaxonReferences->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon,
			'trait_group_id'=>$group,
			'id not in #'=>'('.implode(',',$keep).')'
		));
		
		foreach((array)$new as $val)
		{
			$this->models->TraitsTaxonReferences->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_group_id'=>$group,
				'taxon_id'=>$taxon,
				'reference_id'=>$val
			));
		}
		
		$after=$this->getReferences( $p );
		$taxon=$this->getTaxonById( $taxon );
		$group=$this->getTraitgroup( $group );
		
		$this->logChange( [ 'before'=>$before,'after'=>$after,'note'=> 'updated references for trait group ' . $group['sysname'] . ' for ' . $taxon['nomen'] ] );

	}

	private function deleteTaxonTraitData( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;

		if ( empty($taxon) || empty($group) )
		{
			return;
		}

		$data = $this->getTaxonValues(array('taxon'=>$taxon,'group'=>$group));

		foreach((array)$data as $trait)
		{
			foreach((array)$trait['values'] as $val)
			{
				if (substr($trait['trait']['type'],-4)=='free')
				{
					$this->models->TraitsTaxonFreevalues->delete(array(
						'project_id'=>$this->getCurrentProjectId(),
						'id'=>$val['id'],
						'taxon_id'=>$taxon,
					));					
				}
				else
				{
					$this->models->TraitsTaxonValues->delete(array(
						'project_id'=>$this->getCurrentProjectId(),
						'id'=>$val['id'],
						'taxon_id'=>$taxon
					));
				}
			}
		}
		
		$taxon=$this->getTaxonById( $taxon );
		$group=$this->getTraitgroup( $group );
		
		$this->logChange( [ 'before'=>$data,'note'=> 'deleted values for trait group ' . $group['sysname'] . ' for ' . $taxon['nomen'] ] );

	}
						
	private function deleteReferences( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;

		if ( empty($taxon) || empty($group) )
			return;

		$this->models->TraitsTaxonReferences->delete(array(
			'project_id'=>$this->getCurrentProjectId(),
			'trait_group_id'=>$group,
			'taxon_id'=>$taxon
		));

	}

}
