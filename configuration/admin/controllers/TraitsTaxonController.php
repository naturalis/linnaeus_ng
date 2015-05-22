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
   
    public $controllerPublicName = 'Kenmerken';

    public $cacheFiles = array();
    
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
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
    }
	
    public function taxonAction()
    {
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Taxon trait data'));
		
		//$this->isFormResubmit()
		//q( $this->requestData );
		
		if ($this->rHasVal('action','deleteall'))
		{
			$this->deleteTaxonTraitData(array('taxon'=>$this->rGetVal( 'id' ),'group'=>$this->rGetVal( 'group' )));
			$this->deleteReferences(array('taxon'=>$this->rGetVal( 'id' ),'group'=>$this->rGetVal( 'group' )));
			$this->addMessage('Deleted');
		} 
		else
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
		if ($this->rHasVal('action','save') && $this->rHasId() && $this->rHasVal('group'))
		{
			$this->saveTaxonTraitData(array(
				'taxon'=>$this->rGetId(),
				'trait'=>$this->rGetVal('trait'),
				'values'=>$this->rGetVal('values'),
				'value_start'=>$this->rGetVal('value_start'),
				'value_end'=>$this->rGetVal('value_end'),
			));
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

    public function ajaxInterfaceAction()
    {
		$this->checkAuthorisation();

		if ($this->rHasVal('action','get_taxon_trait'))
		{
			$d=$this->getTaxonValues($this->requestData);

			$this->smarty->assign('returnText',json_encode(array(
				'trait'=>$this->getTraitgroupTrait($this->requestData),
				'taxon_values'=>isset($d[0]) ? $d[0] : null,
				'default_project_language' => $this->getDefaultProjectLanguage()
			)));
		}

		$this->printPage('ajax_interface');
    }



    private function getTaxonValues( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$language=$this->getDefaultProjectLanguage();

		$data=array();

		if ( empty($taxon) || empty($language) )
		{
			return;
		}

		$r=$this->models->TraitsTaxonValues->freeQuery("
			select * from (
			
				select
					_a.id,
					_b.id as value_id,
					_b.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN 
							if(length(ifnull(_t4.translation,''))=0,_b.string_value,_t4.translation)
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN
							_b.numerical_value
						WHEN locate('date',_g.sysname)=1 THEN
							_b.date
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN _b.numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN _b.date_end
						ELSE null
					END) AS value_end,

					_b.date as _date_value,
					_b.date_end as _date_value_end,
					_e.format as _date_format,
					
					_c.show_order as _show_order_1,
					_b.show_order as _show_order_2,
					'fixed' as type
			
				from
					%PRE%traits_taxon_values _a
				
				left join %PRE%traits_values _b
					on _a.project_id=_b.project_id
					and _a.value_id=_b.id
			
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _b.trait_id=_c.id
			
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language."
			
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language."
			
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language."
			
				left join %PRE%text_translations _t4
					on _b.project_id=_t4.project_id
					and _b.string_label_tid=_t4.text_id
					and _t4.language_id=".$language."
			
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
			
				left join  %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
							
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id=".$taxon."
					and _b.trait_id is not null
			
				union
			
				select
					_a.id,
					null as value_id,
					_a.trait_id,
					_c.sysname as trait_sysname,
					if(length(ifnull(_t1.translation,''))=0,_c.sysname,_t1.translation) as trait_name,
					_t2.translation as trait_code,
					_t3.translation as trait_description,
					_g.sysname as trait_type_sysname,
					(CASE 
						WHEN locate('string',_g.sysname)=1 THEN string_value
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value
						WHEN locate('date',_g.sysname)=1 THEN date_value
						ELSE null
					END) AS value_start,
					(CASE 
						WHEN (locate('int',_g.sysname)=1 || locate('float',_g.sysname)=1) THEN numerical_value_end
						WHEN locate('date',_g.sysname)=1 THEN date_value_end
						ELSE null
					END) AS value_end,

					date_value as _date_value,
					date_value_end as _date_value_end,
					_e.format as _date_format,
			
					_c.show_order as _show_order_1,
					null as _show_order_2,
					'free' as type
					
				from
					%PRE%traits_taxon_freevalues _a
				
				left join %PRE%traits_traits _c
					on _a.project_id=_c.project_id
					and _a.trait_id=_c.id
				
				left join %PRE%text_translations _t1
					on _c.project_id=_t1.project_id
					and _c.name_tid=_t1.text_id
					and _t1.language_id=".$language."
				
				left join %PRE%text_translations _t2
					on _c.project_id=_t2.project_id
					and _c.code_tid=_t2.text_id
					and _t2.language_id=".$language."
				
				left join %PRE%text_translations _t3
					on _c.project_id=_t3.project_id
					and _c.description_tid=_t3.text_id
					and _t3.language_id=".$language."
				
				left join %PRE%traits_project_types _f
					on _c.project_id=_f.project_id
					and _c.project_type_id=_f.id
				
				left join %PRE%traits_types _g
					on _f.type_id=_g.id

				left join 
					%PRE%traits_date_formats _e
					on _c.date_format_id=_e.id
				
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id=".$taxon."
			
			) as unionized
			".( $trait ? "where trait_id =".$trait : "" )."
			order by _show_order_1,_show_order_2
		");
	
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
		if ( $t['type_sysname']=='datefree' )
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
				$d=array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$taxon,
					'trait_id'=>$trait,
					'date_value'=>$this->makeInsertableDate( $val, $t['date_format_format'] )
				);
				
				if (isset($value_end) && !empty($value_end[$key]))
					$d['date_value_end']=$this->makeInsertableDate( $value_end[$key], $t['date_format_format'] );

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
	}

	private function getReferences( $p )
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;

		if ( empty($taxon) || empty($group) )
			return;

		$l=$this->models->Literature2->freeQuery("
			select
				_ttr.id,
				_a.id as literature_id,
				_a.label,
				_a.citation

			from %PRE%literature2 _a

			right join %PRE%traits_taxon_references _ttr
				on _a.id = _ttr.reference_id 
				and _a.project_id=_ttr.project_id
				and _ttr.trait_group_id=".$group."

			where
				_a.project_id=".$this->getCurrentProjectId()." 
				and _ttr.taxon_id=".$taxon
		);

		foreach((array)$l as $key=>$val)
		{
			$authors=$this->models->Literature2Authors->freeQuery("
				select
					_a.actor_id, _b.name
	
				from %PRE%literature2_authors _a
	
				left join %PRE%actors _b
					on _a.actor_id = _b.id 
					and _a.project_id=_b.project_id
	
				where
					_a.project_id = ".$this->getCurrentProjectId()."
					and _a.literature2_id =".$val['literature_id']."
				order by _b.name
			");
		
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

		$keep=array(-1);
		$new=array();

		foreach((array)$references as $val)
		{
			$d=explode(",",$val);

			if ($d[0]==-1)
				$new=$d[1];
			else
				$keep[]=$d[0];
		}

		$this->models->Literature2->freeQuery("
			delete from 
				%PRE%traits_taxon_references
			where
				project_id=".$this->getCurrentProjectId()." 
				and taxon_id=".$taxon."
				and id not in (".implode(",",$keep).")
		");
		
		foreach((array)$new as $val)
		{
			$this->models->TraitsTaxonReferences->save(array(
				'project_id'=>$this->getCurrentProjectId(),
				'trait_group_id'=>$group,
				'taxon_id'=>$taxon,
				'reference_id'=>$val
			));
		}

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
