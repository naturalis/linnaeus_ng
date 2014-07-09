<?php

/*

	to do:
	implement revert

*/

include_once ('Controller.php');
include_once ('RdfController.php');
class NsrTaxonController extends Controller
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'names',
		'name_types',
		'presence_taxa',
		'actors',
		'rdf',
		'nsr_ids'
    );
    public $usedHelpers = array(
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'lookup.css',
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
        )
    );
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $_nameTypeIds;

    public function __construct ()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize ()
    {
		$this->Rdf = new RdfController;
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
    }

	private function setConceptId($id)
	{
		$this->conceptId=$id;
	}

	private function getConceptId()
	{
		return $this->conceptId;
	}


	private function updateConceptRankId($values)
	{
		$this->models->Taxon->update(
			array('rank_id'=>$values['new']),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateParentId($values)
	{
		$this->models->Taxon->update(
			array('parent_id'=>$values['new']),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptPresenceId($values)
	{
		$this->models->PresenceTaxa->update(
			array('presence_id'=>$values['new']),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptIsIndigeous($values)
	{
		$this->models->PresenceTaxa->update(
			array('is_indigenous'=>$values['new']=='-1' ? 'null' : $values['new']),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptHabitatId($values)
	{
		$this->models->PresenceTaxa->update(
			array('habitat_id'=>$values['new']),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

    public function indexAction()
    {
		
		// need authorization!
		
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setConceptId($this->rGetVal('id'));

			if ($this->rHasVar('concept_rank_id'))
			{
				$this->updateConceptRankId($this->rGetVal('concept_rank_id'));
			}

			if ($this->rHasVar('parent_taxon_id'))
			{
				$this->updateParentId($this->rGetVal('parent_taxon_id'));
			}

			if ($this->rHasVar('presence_presence_id'))
			{
				$this->updateConceptPresenceId($this->rGetVal('presence_presence_id'));
			}

			if ($this->rHasVar('presence_is_indigenous'))
			{
				$this->updateConceptIsIndigeous($this->rGetVal('presence_is_indigenous'));
			}

			if ($this->rHasVar('presence_habitat_id'))
			{
				$this->updateConceptHabitatId($this->rGetVal('presence_habitat_id'));
			}
			/*
			$data=$this->requestData;
			unset($data['id']);
			unset($data['action']);
			$this->smarty->assign('data',$data);
			*/
		}
		
		
		
		
		if ($this->rHasId())
		{
			$concept=$this->getConcept($this->rGetId());
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('names',$this->getNames($concept));
			$this->smarty->assign('presence',$this->getPresenceData($this->rGetId()));
			$this->smarty->assign('ranks',$this->newGetProjectRanks());

			$this->smarty->assign('statuses',$this->getStatuses());
			$this->smarty->assign('habitats',$this->getHabitats());



		}
		else
		{
			$this->addError('No id');
		}
		$this->printPage();
    }

	private function getNsrId($p)
	{
		$data=$this->models->NsrIds->_get(array(
			'id'=>array(
				'lng_id' => $p['id'],
				'item_type' => $p['item_type']
			),
			'columns'=>'nsr_id'
		));

		return str_replace('tn.nlsr.concept/','',$data[0]['nsr_id']);
	}
		
	private function getConcept($id)
	{
		$c=$this->getTaxonById($id);
		$c['nsr_id']=$this->getNsrId(array('id'=>$c['id'],'item_type'=>'taxon'));
		$c['parent']=$this->getTaxonById($c['parent_id']);
		return $c;
	}

	private function getNames($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$base_rank_id=isset($p['base_rank']) ? $p['base_rank'] : null;

        $names=$this->models->Names->freeQuery(
			array(
				'query' => "
					select
						_a.id,
						_a.name,
						_a.uninomial,
						_a.specific_epithet,
						_a.infra_specific_epithet,
						_a.authorship,
						_a.name_author,
						_a.authorship_year,
						_a.reference,
						_a.reference_id,
						_a.expert,
						_a.expert_id,
						_a.organisation,
						_a.organisation_id,
						_b.nametype,
						_a.language_id,
						_c.language,
						_d.label as language_label,
						case
							when _b.nametype = '".PREDICATE_VALID_NAME."' then 11
							when _b.nametype = '".PREDICATE_PREFERRED_NAME."' then 10
							when _b.nametype = '".PREDICATE_ALTERNATIVE_NAME."' then 9
							when _b.nametype = '".PREDICATE_SYNONYM."' then 7
							when _b.nametype = '".PREDICATE_SYNONYM_SL."' then 6

							when _b.nametype = '".PREDICATE_HOMONYM."' then 5
							when _b.nametype = '".PREDICATE_MISSPELLED_NAME."' then 4
							when _b.nametype = '".PREDICATE_INVALID_NAME."' then 3
							else 0
						end as sort_criterium

					from %PRE%names _a 

					left join %PRE%name_types _b
						on _a.type_id=_b.id 
						and _a.project_id=_b.project_id

					left join %PRE%languages _c
						on _a.language_id=_c.id

					left join %PRE%labels_languages _d
						on _a.language_id=_d.language_id
						and _d.label_language_id=".$this->getDefaultProjectLanguage()."

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.taxon_id=".$id."
					order by 
						sort_criterium desc
						",
				'fieldAsIndex' => 'id'
			)
		);
		

		$prefferedname=null;
		$scientific_name=null;
		$nomen=null;

		foreach((array)$names as $key=>$val)
		{
			if ($val['nametype']==PREDICATE_PREFERRED_NAME && $val['language_id']==$this->getDefaultProjectLanguage())
			{
				$prefferedname=$val['name'];
			}

			if (!empty($val['expert_id']))
				$names[$key]['expert']=$this->getActor($val['expert_id']);

			if (!empty($val['organisation_id']))
				$names[$key]['organisation']=$this->getActor($val['organisation_id']);

			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);


			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']==PREDICATE_VALID_NAME)
			{
				$scientific_name=trim($val['name']);
				$nomen=trim($val['uninomial']).' '.trim($val['specific_epithet']).' '.trim($val['infra_specific_epithet']);
				
				if (strlen(trim($nomen))==0)
					$nomen=trim(str_replace($val['authorship'],'',$val['name']));

				if ($base_rank_id>=GENUS_RANK_ID)
				{
					$nomen='<i>'.trim($nomen).'</i>';
					$names[$key]['name']=trim($nomen.' '.$val['authorship']);
				}
			}
		}

		return
			array(
				'scientific_name'=>$scientific_name,
				'nomen'=>$nomen,
				'nomen_no_tags'=>trim(strip_tags($nomen)),
				'preffered_name'=>$prefferedname,
				'list'=>$names
			);
	}

	private function getPresenceData($id)
	{
		$data=$this->models->PresenceTaxa->freeQuery(
			"select
				_a.is_indigenous,
				_a.presence_id,
				_a.presence82_id,
				_a.reference_id,
				_b.label as presence_label,
				_b.information as presence_information,
				_b.information_title as presence_information_title,
				_b.index_label as presence_index_label,
				_c.label as presence82_label,
				_d.habitat_id,
				_d.label as habitat_label,
				_e.id as expert_id,
				_f.id as organisation_id,
				_e.name as expert_name,
				_f.name as organisation_name,
				_a.reference_id,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date
				
			from %PRE%presence_taxa _a

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%presence_labels _c
				on _a.presence82_id = _c.presence_id 
				and _a.project_id=_c.project_id 
				and _c.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%habitat_labels _d
				on _a.habitat_id = _d.habitat_id 
				and _a.project_id=_d.project_id 
				and _d.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.actor_org_id = _f.id 
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id 
				and _a.project_id=_g.project_id

			where _a.project_id = ".$this->getCurrentProjectId()."
				and _a.taxon_id =".$id
		);	
		
		$data[0]['presence_information_one_line']=str_replace(array("\n","\r","\r\n"),'<br />',$data[0]['presence_information']);
		
		return $data[0];
	}

	private function getActor($id)
	{
		$data=$this->models->Actors->_get(array(
			'id' => array(
				'project_id'=>$this->getCurrentProjectId(),
				'id'=>$id
			)
		));	
		return $data[0];
	}

	private function getStatuses()
	{
		$data=$this->models->PresenceTaxa->freeQuery(
			"select
            	_a.id,
            	_b.label,
            	_b.information,
            	_b.information_short,
            	_b.information_title,
            	ifnull(_b.index_label,99) as index_label

			from %PRE%presence _a

			left join %PRE%presence_labels _b
				on _a.id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			where _a.project_id = ".$this->getCurrentProjectId()."
			order by index_label"
		);	

		return $data;
	}

	private function getHabitats()
	{
		$data=$this->models->PresenceTaxa->freeQuery(
			"select
            	_a.id,
            	_b.label

			from %PRE%habitats _a

			left join %PRE%habitat_labels _b
				on _a.id = _b.habitat_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			where _a.project_id = ".$this->getCurrentProjectId()
		);	

		return $data;
	}



	
	
	

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

		if ($this->rHasVal('action', 'species_lookup'))
		{
            $return=$this->getSpeciesLookupList($this->requestData);
        } 
		else
		if ($this->rHasVal('action', 'get_tree_node'))
		{
			$return=json_encode($this->getTreeNode($this->requestData));
        }
		else
		if ($this->rHasVal('action', 'store_tree'))
		{	
	        $_SESSION['admin']['user']['species']['tree']=$this->requestData['tree'];
			$return='saved';
        }
		else
		if ($this->rHasVal('action', 'restore_tree'))
		{
	        $return=json_encode($this->restoreTree());
        }
        
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
    }

    private function getSpeciesLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $getAll =isset($p['get_all']) ? $p['get_all']==1 : false;
        $concise=isset($p['concise']) ? $p['concise']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;

        if (empty($search) && !$getAll)
            return;

        $taxa = $this->models->Taxon->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "

				select
					_a.taxon_id as id,
					_a.name as label,
					_b.rank_id,
					_c.rank_id as base_rank_id,
					_b.taxon as taxon,
					'names' as source,
					_d.rank
				from
					%PRE%names _a

				left join
					%PRE%taxa _b
						on _a.project_id=_b.project_id
						and _a.taxon_id=_b.id

				left join
					%PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id=_c.id

				left join
					%PRE%ranks _d
					on _c.rank_id=_d.id


				where
					_a.project_id =  ".$this->getCurrentProjectId()."
					and _a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					and _a.type_id != ".
						(
							isset($this->_nameTypeIds[PREDICATE_VALID_NAME]['id']) ?
								$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : -1
						)."
				union

			")."

			select
				_b.id,
				_b.taxon as label,
				_b.rank_id,
				_d.rank_id as base_rank_id,
				_b.taxon as taxon,
				'taxa' as source,
				_e.rank
			from
				%PRE%taxa _b

			left join
				%PRE%projects_ranks _d
					on _b.project_id=_d.project_id
					and _b.rank_id=_d.id

				left join
					%PRE%ranks _e
					on _d.rank_id=_e.id

			where
				_b.project_id = ".$this->getCurrentProjectId()."
				and _b.taxon like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'

			) as unification
			where 1
			".($rankAbove ? "and base_rank_id < ".$rankAbove : "")."
			".($rankEqualAbove ? "and base_rank_id <= ".$rankEqualAbove : "")."

			order by base_rank_id, label
			limit ".$maxResults
		);

        foreach ((array) $taxa as $key => $val)
		{
			if ($val['source']=='taxa')
			{
				if ($formatted)
					$taxa[$key]['label']=$this->formatTaxon($val);
			}
			else
			{
				if ($formatted)
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$this->formatTaxon($val).')';
				else
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$val['taxon'].')';
			}

			$taxa[$key]['label']=$taxa[$key]['label'].' ['.$val['rank'].']';

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
		}

		return
			$this->makeLookupList(
				$taxa,
				'species',
				'../species/taxon.php?id=%s',
				false,
				true,
				count($taxa)<$maxResults
			);

    }

    private function getExpertsLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $getAll =isset($p['get_all']) ? $p['get_all']==1 : false;
        $concise=isset($p['concise']) ? $p['concise']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;

        if (empty($search) && !$getAll)
            return;

        $taxa = $this->models->Taxon->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "

				select
					_a.taxon_id as id,
					_a.name as label,
					_b.rank_id,
					_c.rank_id as base_rank_id,
					_b.taxon as taxon,
					'names' as source,
					_d.rank
				from
					%PRE%names _a

				left join
					%PRE%taxa _b
						on _a.project_id=_b.project_id
						and _a.taxon_id=_b.id

				left join
					%PRE%projects_ranks _c
						on _b.project_id=_c.project_id
						and _b.rank_id=_c.id

				left join
					%PRE%ranks _d
					on _c.rank_id=_d.id


				where
					_a.project_id =  ".$this->getCurrentProjectId()."
					and _a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					and _a.type_id != ".
						(
							isset($this->_nameTypeIds[PREDICATE_VALID_NAME]['id']) ?
								$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : -1
						)."
				union

			")."

			select
				_b.id,
				_b.taxon as label,
				_b.rank_id,
				_d.rank_id as base_rank_id,
				_b.taxon as taxon,
				'taxa' as source,
				_e.rank
			from
				%PRE%taxa _b

			left join
				%PRE%projects_ranks _d
					on _b.project_id=_d.project_id
					and _b.rank_id=_d.id

				left join
					%PRE%ranks _e
					on _d.rank_id=_e.id

			where
				_b.project_id = ".$this->getCurrentProjectId()."
				and _b.taxon like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'

			) as unification
			where 1
			".($rankAbove ? "and base_rank_id < ".$rankAbove : "")."
			".($rankEqualAbove ? "and base_rank_id <= ".$rankEqualAbove : "")."

			order by base_rank_id, label
			limit ".$maxResults
		);

        foreach ((array) $taxa as $key => $val)
		{
			if ($val['source']=='taxa')
			{
				if ($formatted)
					$taxa[$key]['label']=$this->formatTaxon($val);
			}
			else
			{
				if ($formatted)
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$this->formatTaxon($val).')';
				else
					$taxa[$key]['label']=$taxa[$key]['label'].' ('.$val['taxon'].')';
			}

			$taxa[$key]['label']=$taxa[$key]['label'].' ['.$val['rank'].']';

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
		}

		return
			$this->makeLookupList(
				$taxa,
				'species',
				'../species/taxon.php?id=%s',
				false,
				true,
				count($taxa)<$maxResults
			);

    }

	private function restoreTree()
	{
		return isset($_SESSION['admin']['user']['species']['tree']) ?  $_SESSION['admin']['user']['species']['tree'] : null;
	}

	private function treeGetTop()
	{
		/*
			get the top taxon = no parent
			"_r.id < 10" added as there might be orphans, which are ususally low-level ranks 
		*/
		$p=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a
					
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.parent_id is null
				and _r.id < 10

		");

		if ($p && count((array)$p)==1)
		{
			$p=$p[0]['id'];
		} 
		else
		{
			$p=null;
		}

		if (count((array)$p)>1)
		{
			$this->addError('Detected multiple high-order taxa without a parent. Unable to determine which is the top of the tree.');
		}

		return $p;
	}
	
	private function getTreeNode($p)
	{
		
		$node=isset($p['node']) && $p['node']!==false ? $p['node'] : $this->treeGetTop();
		$count=isset($p['count']) && in_array($p['count'],array('none','taxon','species')) ? $p['count'] : 'none';

		if (is_null($node))
			return;

		$taxa=
			$this->models->Taxon->freeQuery("
				select
					_a.id,
					_a.parent_id,
					_a.is_hybrid,
					_a.rank_id,
					_a.taxon,
					ifnull(_k.name,_l.commonname) as name,
					_m.authorship,
					_r.rank,
					_q.label as rank_label,
					_p.rank_id as base_rank

				from
					%PRE%taxa _a

				left join %PRE%projects_ranks _p
					on _a.project_id=_p.project_id
					and _a.rank_id=_p.id

				left join %PRE%labels_projects_ranks _q
					on _a.rank_id=_q.project_rank_id
					and _a.project_id = _q.project_id
					and _q.language_id=".$this->getDefaultProjectLanguage()."

				left join %PRE%ranks _r
					on _p.rank_id=_r.id

				left join %PRE%commonnames _l
					on _a.id=_l.taxon_id
					and _a.project_id=_l.project_id
					and _l.language_id=".$this->getDefaultProjectLanguage()."

				left join %PRE%names _k
					on _a.id=_k.taxon_id
					and _a.project_id=_k.project_id
					and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
					and _k.language_id=".$this->getDefaultProjectLanguage()."
	
				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
	
				where 
					_a.project_id = ".$this->getCurrentProjectId()." 
					and (_a.id = ".$node." or _a.parent_id = ".$node.")

				order by
					label
			");
			
		$taxon=$progeny=array();

		foreach((array)$taxa as $key=>$val)
		{
			if ($count=='taxon') 
			{
				$d=$this->models->Taxon->freeQuery("
					select
						count(*) as total
					from
						%PRE%taxon_quick_parentage
					where 
						project_id = ".$this->getCurrentProjectId()." 
						and MATCH(parentage) AGAINST ('".$val['id']."' in boolean mode)
					");
					
				$val['child_count']['taxon']=$d[0]['total'];
			}
			else
			if ($count=='species') 
			{
	
				$d=$this->models->Taxon->freeQuery(array(
					'query'=> "
						select
							count(_sq.taxon_id) as total,
							_sq.taxon_id,
							_sp.presence_id,
							ifnull(_sr.established,'undefined') as established
						from 
							%PRE%taxon_quick_parentage _sq
						
						left join %PRE%presence_taxa _sp
							on _sq.project_id=_sp.project_id
							and _sq.taxon_id=_sp.taxon_id
						
						left join %PRE%presence _sr
							on _sp.project_id=_sr.project_id
							and _sp.presence_id=_sr.id
		
						left join %PRE%taxa _e
							on _sq.taxon_id = _e.id
							and _sq.project_id = _e.project_id
						
						left join %PRE%projects_ranks _f
							on _e.rank_id=_f.id
							and _e.project_id = _f.project_id
						
						where
							_sq.project_id=".$this->getCurrentProjectId()."
							and _sp.presence_id is not null
							and _f.rank_id".($val['base_rank']>=SPECIES_RANK_ID ? ">=" : "=")." ".SPECIES_RANK_ID."

							and MATCH(_sq.parentage) AGAINST ('".$val['id']."' in boolean mode)
							
						group by _sr.established",
					'fieldAsIndex' =>"established"
					)
				);
	
				$val['child_count']=
					array(
						'total'=>
							(int)
								(isset($d['undefined']['total'])?$d['undefined']['total']:0)+
								(isset($d[0]['total'])?$d[0]['total']:0)+
								(isset($d[1]['total'])?$d[1]['total']:0),
						'established'=>
								(int)(isset($d[1]['total'])?$d[1]['total']:0),
						'not_established'=>
								(int)(isset($d[0]['total'])?$d[0]['total']:0)
					);
			} else
			{
				$val['child_count']=null;
			}
			
			if ($val['base_rank']>=SPECIES_RANK_ID)
			{
				if ($val['authorship']!='')
				{
					$val['taxon']=
						'<i>'.
						str_replace($val['authorship'],'',$val['taxon']).
						'</i>'.' '.$val['authorship'];
				}
				else
				{
					$val['taxon']=$this->formatTaxon($val);
				}
			}

			$val['label']=empty($val['name']) ? $val['taxon'] : $val['name'].' ('.$val['taxon'].')';

			unset($val['parent_id']);
			unset($val['is_hybrid']);
			unset($val['rank_id']);
			unset($val['base_rank']);

			if ($val['id']==$node)
			{
				$taxon=$val;
			}
			else 
			{
				$d=$this->models->Taxon->freeQuery("
					select
						count(*) as total
					from
						%PRE%taxa
					where 
						project_id = ".$this->getCurrentProjectId()." 
						and parent_id = ".$val['id']
					);
					
				$val['has_children']=$d[0]['total']>0;
				$progeny[]=$val;
			}
		}
		
		usort(
			$progeny,
			function($a,$b)
			{
				return (strtolower($a['label'])==strtolower($b['label']) ? 0 : (strtolower($a['label'])>strtolower($b['label']) ? 1 : -1)); 
			}
		);

		return
			array(
				'node'=>$taxon,
				'progeny'=>$progeny
			);
		
	}

}
