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
		
		if ($this->rHasVal('action','save') && $this->rHasId() && $this->rHasVal('group'))
		{
			$this->saveTaxonTraitData(array(
				'taxon'=>$this->rGetId(),
				'trait'=>$this->rGetVal('trait'),
				'values'=>$this->rGetVal('values')
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
			)));
		}

		$this->printPage('ajax_interface');
    }


    private function getTaxonValues($p)
	{
		$project=$this->getCurrentProjectId();
		$language=$this->getDefaultProjectLanguage();
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;

		$data=array();

		if (empty($project)||empty($taxon)||empty($language))
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
					_b.show_order as _show_order_2
			
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
					_a.project_id=".$project."
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
					null as _show_order_2
					
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
					_a.project_id=".$project."
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

	private function saveTaxonTraitData($p)
	{
		$project=$this->getCurrentProjectId();
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$trait=isset($p['trait']) ? $p['trait'] : null;
		$values=isset($p['values']) ? $p['values'] : null;

		if (empty($project)||empty($taxon)||empty($trait))
		{
			return;
		}

		$existing=$this->getTaxonValues(array('taxon'=>$taxon,'trait'=>$trait));
		$t=$this->getTraitgroupTrait(array('trait'=>$trait));

		if ($existing)
		{
			$existing=$existing[0];
		}
		
		if ($t['type_sysname']=='stringfree')
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
		if ($t['type_sysname']=='stringlist')
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
				$this->models->TraitsTaxonValues->save(array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$taxon,
					'value_id'=>$val
				));
			}		
		}
	}

	private function getReferences($p)
	{
		$taxon=isset($p['taxon']) ? $p['taxon'] : null;
		$group=isset($p['group']) ? $p['group'] : null;
		$project=$project=$this->getCurrentProjectId();

		if (empty($taxon)||empty($group)||empty($project))
			return;

		$l=$this->models->Literature2->freeQuery("
			select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			right join %PRE%traits_taxon_references _ttr
				on _a.id = _ttr.reference_id 
				and _a.project_id=_ttr.project_id
				and _ttr.trait_group_id=".$group."

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id=".$project." 
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
					_a.project_id = ".$project."
					and _a.literature2_id =".$val['id']."
				order by _b.name
			");
		
			$l[$key]['authors']=$authors;
			
		}
		
		return $l;

	}


}
