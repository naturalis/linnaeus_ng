<?php

/*
	to do:
	implement revert

			// for revert:
			$data=$this->requestData;
			unset($data['id']);
			unset($data['action']);
			$this->smarty->assign('data',$data);
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
		'literature2',
		'rdf',
		'nsr_ids'
    );
    public $usedHelpers = array(
    );
    public $cacheFiles = array(
    );
    public $cssToLoad = array(
        'lookup.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );
    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;
	private $_nameTypeIds;
	
	private $conceptId=null;
	private $nameId=null;
	

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialize()
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

    public function taxonNewAction()
    {
		$this->checkAuthorisation();

		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->saveConcept();
			if ($this->getConceptId())
			{
				$this->saveName();
				$this->redirect('taxon.php?id='.$this->getConceptId());
			}		
		}

		$this->smarty->assign('name_type_id',$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']);
		$this->smarty->assign('name_language_id',LANGUAGE_ID_SCIENTIFIC);
		$this->smarty->assign('ranks',$this->newGetProjectRanks());
		$this->smarty->assign('statuses',$this->getStatuses());
		$this->smarty->assign('habitats',$this->getHabitats());

		$this->printPage();
	}

    public function taxonAction()
    {
		$this->checkAuthorisation();
		
		if (!$this->rHasId()) $this->redirect('taxon_new.php');
	
		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			//$this->setConceptId($this->rGetId());
			//$this->deleteVanAllesEnNogWat();
			//$this->deleteConcept();
			//$this->setMessage('Concept verwijderd.');
			//$this->redirect('index.php');
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setConceptId($this->rGetId());
			$this->updateConcept();

		}
		else
		{
			$this->setConceptId($this->rGetId());
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

		$this->checkMessage();
		$this->printPage();
    }


    public function nameAction()
    {
	
		$this->checkAuthorisation();

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->setNameId($this->rGetId());
			$name=$this->getName(array('id'=>$this->getNameId()));
			$this->deleteName();
			$this->setMessage('Naam verwijderd.');
			$this->redirect('taxon.php?id='.$name['taxon_id']);
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setNameId($this->rGetId());
			$this->updateName();
		} 
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setConceptId($this->rGetVal('nameownerid'));
			$this->saveName();
		} 
		else
		{
			$this->setNameId($this->rGetId());
		}
	
		
		if ($this->getNameId())
		{
			$name=$this->getName(array('id'=>$this->getNameId()));
			$concept=$this->getConcept($name['taxon_id']);
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('name',$name);
			$this->smarty->assign('nametypes',$this->getNameTypes());
			$this->smarty->assign('languages',$this->getLanguages());
			$this->smarty->assign('actors',$this->getActors());

			$this->doNameChecks($name);

		}
		else
		if ($this->rHasVal('taxon'))
		{
			$concept=$this->getConcept($this->rGetVal('taxon'));
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('nametypes',$this->getNameTypes());
			$this->smarty->assign('languages',$this->getLanguages());
			$this->smarty->assign('actors',$this->getActors());
			$this->smarty->assign('newname',true);
		}
		else
		{
			$this->addError('Geen ID.');
		}

		$this->printPage();
    }

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

		if ($this->rHasVal('action', 'get_lookup_list') || $this->rHasVal('action', 'species_lookup'))
		{
            $return=$this->getSpeciesLookupList($this->requestData);
        } 
		else
		if ($this->rHasVal('action', 'expert_lookup'))
		{
            $return=$this->getExpertsLookupList($this->requestData);
        } 
		else
		if ($this->rHasVal('action', 'reference_lookup'))
		{
            $return=$this->getReferenceLookupList($this->requestData);
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



	private function setConceptId($id)
	{
		$this->conceptId=$id;
	}

	private function getConceptId()
	{
		return $this->conceptId;
	}

	private function setNameId($id)
	{
		$this->nameId=$id;
	}

	private function getNameId()
	{
		return $this->nameId;
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

	private function getName($p)
	{
		$id=isset($p['id']) ? $p['id'] : null;
		$taxonId=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$typeId=isset($p['type_id']) ? $p['type_id'] : null;
		$languageId=isset($p['language_id']) ? $p['language_id'] : null;
		
        $name=$this->models->Names->freeQuery(
			array(
				'query' => "
					select
						_a.id,
						_a.taxon_id,
						_a.name,
						_a.uninomial,
						_a.specific_epithet,
						_a.infra_specific_epithet,
						_a.authorship,
						_a.name_author,
						_a.authorship_year,
						_a.reference,
						_a.reference_id,
						_h.label as reference_name,
						_a.expert,
						_a.expert_id,
						_f.name as expert_name,
						_a.organisation,
						_a.organisation_id,
						_g.name as organisation_name,
						_a.type_id,
						_b.nametype,
						_a.language_id,
						_c.language,
						_d.label as language_label

					from %PRE%names _a 

					left join %PRE%name_types _b
						on _a.type_id=_b.id 
						and _a.project_id=_b.project_id

					left join %PRE%languages _c
						on _a.language_id=_c.id

					left join %PRE%labels_languages _d
						on _a.language_id=_d.language_id
						and _a.project_id=_d.project_id
						and _d.label_language_id=".$this->getDefaultProjectLanguage()."

					left join %PRE%actors _f
						on _a.expert_id = _f.id 
						and _a.project_id=_f.project_id
		
					left join %PRE%actors _g
						on _a.organisation_id = _g.id 
						and _a.project_id=_g.project_id
		
					left join  %PRE%literature2 _h
						on _a.reference_id = _h.id 
						and _a.project_id=_h.project_id

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						".(isset($taxonId) ? "and _a.taxon_id=".$taxonId: "" )."
						".(isset($languageId) ? "and _a.language_id=".$languageId: "" )."
						".(isset($typeId) ? "and _a.type_id=".$typeId: "" )."
						".(isset($id) ? "and _a.id=".$id: "" )
			)
		);

		return $name[0];
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
						ifnull(_d.label,_c.language) as language_label,
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
						and _a.project_id=_d.project_id
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

	private function getActors()
	{
		return $this->models->Actors->freeQuery(
			"select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id 
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$this->getCurrentProjectId()."

			order by
				_e.is_company, _e.name
		");	
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

	private function getNameTypes()
	{
        $types=$this->models->NameTypes->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId()
			)
		));
		
		return $types;
	}

	private function getLanguages()
	{
        $used=$this->models->Names->freeQuery("
				select count(id) as `count`, language_id
				from %PRE%names
				where project_id=".$this->getCurrentProjectId()."
				group by language_id
				order by `count` asc
		");
		
		$stuff=null;
		foreach((array)$used as $key => $val)
		{
			$stuff .= "when _c.id = ".$val['language_id']." then ".($key+1)."\n";
		}
		
		if (!empty($stuff))
		{
			$stuff = ", case ".$stuff." else 0 end as sort_criterium\n";
		}

        $languages=$this->models->Language->freeQuery("
			select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label
				".$stuff."
			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$this->getCurrentProjectId()."
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."
				order by ".(!empty($stuff) ? "sort_criterium desc, " : "")."label asc
			");

		return $languages;
	}



    private function getSpeciesLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        //$getAll =isset($p['get_all']) ? $p['get_all']==1 : false;
        $concise=isset($p['concise']) ? $p['concise']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;

        //if (empty($search) && !$getAll)
        if (empty($search)) return;

        $taxa = $this->models->Taxon->freeQuery("
			select * from
			(
			". ($taxaOnly ? "" : "

				select
					_a.taxon_id as id,
					_a.name as label,
					_a.uninomial,
					_a.specific_epithet,
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

					_a.uninomial,
					_a.specific_epithet,

				_b.rank_id,
				_d.rank_id as base_rank_id,
				_b.taxon as taxon,
				'taxa' as source,
				_e.rank
			from
				%PRE%taxa _b

			left join
				%PRE%names _a
					on _a.project_id=_b.project_id
					and _a.taxon_id=_b.id
					and _a.type_id = ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."

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

			if ($val['base_rank_id']==GENUS_RANK_ID)
			{
				$taxa[$key]['inheritable_name']=$val['uninomial'];
			}
			else
			if ($val['base_rank_id']==SPECIES_RANK_ID)
			{
				$taxa[$key]['inheritable_name']=$val['uninomial'].' '.$val['specific_epithet'];
			}
			else
			{
				$taxa[$key]['inheritable_name']="";
			}

			unset($taxa[$key]['taxon']);
			unset($taxa[$key]['source']);
			unset($taxa[$key]['uninomial']);
			unset($taxa[$key]['specific_epithet']);
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
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

        //if (empty($search) && !$getAll)
        if (empty($search))
            return;

		$data=$this->models->Actors->freeQuery(
			"select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id 
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$this->getCurrentProjectId()."
				".(!$getAll ? "and _e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'" : "")."

			order by
				_e.is_company, _e.name
		");	

		return
			$this->makeLookupList(
				$data,
				'actors',
				'actor.php?id=%s',
				false,
				true,
				count($data)<$maxResults
			);

    }

    private function getReferenceLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        //$getAll =isset($p['get_all']) ? $p['get_all']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

        //if (empty($search) && !$getAll)
        if (empty($search))
            return;

		$data=$this->models->Literature2->freeQuery(
			"select
				_a.id,
				_a.language_id,
				_a.label,
				_a.date,
				ifnull(_a.author,ifnull(_e.name,'-')) as author,
				_a.publication_type,
				_a.citation,
				_a.source,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link
				
			from %PRE%literature2 _a

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and (
					_a.label like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
					_a.author like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
					_e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
				)

			order by
				_a.label
		");	

		return
			$this->makeLookupList(
				$data,
				'reference',
				'reference.php?id=%s',
				false,
				true,
				count($data)<$maxResults
			);

    }



	private function saveConcept()
	{
		$name=$this->rGetVal('concept_taxon');
		$rank=$this->rGetVal('concept_rank_id');

		$d=$this->models->Taxon->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'is_empty' =>'0',
			'rank_id' => trim($rank['new']),
			'taxon' => trim($name['new']),
		));

		if ($d)
		{
			$this->setConceptId($this->models->Taxon->getNewId());
			$this->addMessage('Nieuw concept aangemaakt.');
			$this->updateConcept();
		}
		else 
		{
			$this->addError('Aanmaak nieuw concept mislukt.');
		}
	}

	private function updateConcept()
	{
		
		$this->createConceptNsrIds();
		$this->createConceptPresence();

		if ($this->rHasVar('concept_taxon'))
		{
			if ($this->updateConceptTaxon($this->rGetVal('concept_taxon')))
			{
				$this->addMessage('Naam opgeslagen.');
			}
			else
			{
				$this->addError('Naam niet opgeslagen.');
			}
		}

		if ($this->rHasVar('concept_rank_id'))
		{
			if ($this->updateConceptRankId($this->rGetVal('concept_rank_id')))
			{
				$this->addMessage('Rang opgeslagen.');
			}
			else
			{
				$this->addError('Rang niet opgeslagen.');
			}
		}

		if ($this->rHasVar('parent_taxon_id'))
		{
			if ($this->updateParentId($this->rGetVal('parent_taxon_id')))
			{
				$this->addMessage('Ouder opgeslagen.');
			}
			else
			{
				$this->addError('Ouder niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_presence_id'))
		{
			if ($this->updateConceptPresenceId($this->rGetVal('presence_presence_id')))
			{
				$this->addMessage('Voorkomensstatus opgeslagen.');
			}
			else
			{
				$this->addError('Voorkomensstatus niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_is_indigenous'))
		{
			if ($this->updateConceptIsIndigeous($this->rGetVal('presence_is_indigenous')))
			{
				$this->addMessage('Status endemisch opgeslagen.');
			}
			else
			{
				$this->addError('Status endemisch niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_habitat_id'))
		{
			if ($this->updateConceptHabitatId($this->rGetVal('presence_habitat_id')))
			{
				$this->addMessage('Habitat opgeslagen.');
			}
			else
			{
				$this->addError('Habitat niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_expert_id'))
		{
			if ($this->updatePresenceExpertId($this->rGetVal('presence_expert_id')))
			{
				$this->addMessage('Expert opgeslagen.');
			}
			else
			{
				$this->addError('Expert niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_organisation_id'))
		{
			if ($this->updatePresenceOrganisationId($this->rGetVal('presence_organisation_id')))
			{
				$this->addMessage('Organisatie opgeslagen.');
			}
			else
			{
				$this->addError('Organisatie niet opgeslagen.');
			}
		}

		if ($this->rHasVar('presence_reference_id'))
		{
			if ($this->updatePresenceReferenceId($this->rGetVal('presence_reference_id')))
			{
				$this->addMessage('Publicatie opgeslagen.');
			}
			else
			{
				$this->addError('Publicatie niet opgeslagen.');
			}
		}
		
	}

	private function updateConceptTaxon($values)
	{
		return $this->models->Taxon->update(
			array('taxon'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptRankId($values)
	{
		return $this->models->Taxon->update(
			array('rank_id'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateParentId($values)
	{
		return $this->models->Taxon->update(
			array('parent_id'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function createConceptPresence()
	{
		$d=$this->models->PresenceTaxa->_get(array(
			'id'=>
				array(
					'project_id'=>$this->getCurrentProjectId(), 
					'taxon_id'=>$this->getConceptId()
				),
			'columns'=>'count(*) as total'
			));

		if ($d[0]['total']>0) return;

		$this->models->PresenceTaxa->insert(
			array(
				'project_id'=>$this->getCurrentProjectId(), 
				'taxon_id'=>$this->getConceptId()
			));

		return $this->models->PresenceTaxa->getNewId();
	}
	
	private function updateConceptPresenceId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('presence_id'=>trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptIsIndigeous($values)
	{
		return $this->models->PresenceTaxa->update(
			array('is_indigenous'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptHabitatId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('habitat_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceExpertId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('actor_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceOrganisationId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('actor_org_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceReferenceId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('reference_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}



	private function generateNsrCode()
	{
		$c='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$r='';
		while(strlen($r)<11)
		{
			$r.=substr($c,rand(0,35),1);
		}
		return str_pad($r,12,'0',STR_PAD_LEFT);
	}
	
	private function createConceptNsrCode()
	{
		$exists=true;
		$i=0;
		$code=null;
		while($exists)
		{
			$code=$this->generateNsrCode();
			$d=$this->models->NsrIds->freeQuery("
				select count(*) as total
				from %PRE%nsr_ids
				where
					project_id = ".$this->getCurrentProjectId()."
					and (
						nsr_id = '.concept/'".$code."' or
						nsr_id = 'tn.nlsr.concept/'".$code."'
					)
			");
			
			if ($d[0]['total']==0)
			{
				$exists=false;
			}
			if ($i>=100)
			{
				$this->addError('Kon geen nieuw uniek NSR ID creëren.');
				return;
			}
			$i++;
		}
		
		
		return $code;
	}

	private function generateRdfId()
	{
		$c='abcdefghijklmnopqrstuvwxyz0123456789';
		$r='';
		while(strlen($r)<32)
		{
			$r.=substr($c,rand(0,35),1);
		}

		return substr($r,0,8).'-'.substr($r,8,4).'-'.substr($r,12,4).'-'.substr($r,16,4).'-'.substr($r,20);
	}
	
	private function createConceptRdfId()
	{
		$exists=true;
		$i=0;
		$code=null;
		while($exists)
		{
			$code=$this->generateRdfId();
			$d=$this->models->NsrIds->freeQuery("
				select count(*) as total
				from %PRE%nsr_ids
				where
					project_id = ".$this->getCurrentProjectId()."
					and rdf_id = 'http://data.nederlandsesoorten.nl/".$code."'"
			);
			
			if ($d[0]['total']==0)
			{
				$exists=false;
			}
			if ($i>=100)
			{
				$this->addError('Kon geen nieuw uniek Rdf ID creëren.');
				return;
			}
			$i++;
		}
		
		return $code;
	}

	private function createConceptNsrIds()
	{
		$d=$this->models->NsrIds->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$this->getConceptId(),
				'item_type'=>'taxon',
			)));

		$rdf=$nsr=null;

		if (empty($d[0]['rdf_id'])) $rdf=$this->createConceptRdfId();
		if (empty($d[0]['nsr_id'])) $nsr=$this->createConceptNsrCode();
		
		if (empty($rdf) && empty($nsr)) return;
		
		if (!empty($rdf) && !empty($nsr))
		{
			$this->models->NsrIds->insert(
				array(
					'project_id'=>$this->getCurrentProjectId(), 
					'rdf_id'=>$rdf,
					'nsr_id'=>$nsr,
					'lng_id'=>$this->getConceptId(),
					'item_type'=>'taxon',
				)); 
		}
		else
		if (!empty($rdf))
		{
			$this->models->NsrIds->update(
				array('rdf_id'=>$rdf),
				array('lng_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId(),'item_type'=>'taxon')
			);
		}
		else
		if (!empty($nsr))
		{
			$this->models->NsrIds->update(
				array('nsr_id'=>$nsr),
				array('lng_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId(),'item_type'=>'taxon')
			);
		}

	}

	private function saveName()
	{
		$type=$this->rGetVal('name_type_id');
		$language=$this->rGetVal('name_language_id');

		$d=$this->models->Names->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $this->getConceptId(),
				'language_id' => trim($language['new']),
				'type_id' => trim($type['new'])
			));
		
		if ($d)
		{
			$this->setNameId($this->models->Names->getNewId());
			$this->addMessage('Nieuwe naam aangemaakt.');
			$this->updateName();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe naam mislukt.');
		}
	}

	private function updateName()
	{
		$name=$this->getName(array('id'=>$this->getNameId()));

		$this->setConceptId($name['taxon_id']);

		if ($this->rHasVar('name_name'))
		{
			if ($this->updateNameName($this->rGetVal('name_name')))
			{
				$this->addMessage('Naam opgeslagen.');
			}
			else
			{
				$this->addError('Naam niet opgeslagen.');
			}
		}

		if ($this->rHasVar('name_uninomial'))
		{
			if ($this->updateNameUninomial($this->rGetVal('name_uninomial')))
			{
				$this->addMessage('Uninomiaal opgeslagen.');
			}
			else
			{
				$this->addError('Uninomiaal niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_specific_epithet'))
		{
			if ($this->updateNameSpecificEpithet($this->rGetVal('name_specific_epithet')))
			{
				$this->addMessage('Specifiek epithet opgeslagen.');
			}
			else
			{
				$this->addError('Specifiek epithet niet opgeslagen.');
			}
		}

		if ($this->rHasVar('name_infra_specific_epithet'))
		{
			if ($this->updateNameInfraSpecificEpithet($this->rGetVal('name_infra_specific_epithet')))
			{
				$this->addMessage('Infra-specifiek epithet opgeslagen.');
			}
			else
			{
				$this->addError('Infra specifiek epithet niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_authorship'))
		{
			if ($this->updateNameAuthorship($this->rGetVal('name_authorship')))
			{
				$this->addMessage('"Authorship" opgeslagen.');
			}
			else
			{
				$this->addError('"Authorship" niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_name_author'))
		{
			if ($this->updateNameAuthor($this->rGetVal('name_name_author')))
			{
				$this->addMessage('Naam auteur opgeslagen.');
			}
			else
			{
				$this->addError('Naam auteur niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_authorship_year'))
		{
			if ($this->updateNameAuthorshipYear($this->rGetVal('name_authorship_year')))
			{
				$this->addMessage('Jaar opgeslagen.');
			}
			else
			{
				$this->addError('Jaar niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_type_id'))
		{
			if ($this->updateNameTypeId($this->rGetVal('name_type_id')))
			{
				$this->addMessage('Type opgeslagen.');
			}
			else
			{
				$this->addError('Type niet opgeslagen.');
			}
		}
		
		if ($this->rHasVar('name_language_id'))
		{
			if ($this->updateNameLanguageId($this->rGetVal('name_language_id')))
			{
				$this->addMessage('Taal opgeslagen.');
			}
			else
			{
				$this->addError('Taal niet opgeslagen.');
			}
		}

		if ($this->rHasVar('name_reference_id'))
		{
			if ($this->updateNameReferenceId($this->rGetVal('name_reference_id')))
			{
				$this->addMessage('Referentie opgeslagen.');
			}
			else
			{
				$this->addError('Referentie niet opgeslagen.');
			}
		}

		if ($this->rHasVar('name_expert_id'))
		{
			if ($this->updateNameExpertId($this->rGetVal('name_expert_id')))
			{
				$this->addMessage('Expert opgeslagen.');
			}
			else
			{
				$this->addError('Expert niet opgeslagen.');
			}
		}

		if ($this->rHasVar('name_organisation_id'))
		{
			if ($this->updateNameOrganisationId($this->rGetVal('name_organisation_id')))
			{
				$this->addMessage('Organisatie opgeslagen.');
			}
			else
			{
				$this->addError('Organisatie niet opgeslagen.');
			}
		}
			
	}

	private function deleteName()
	{
		$this->models->Names->delete(
		array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$this->getNameId()
		));
	}

	private function updateNameName($values)
	{
		// to delete, call deleteName()
		return $this->models->Names->update(
			array('name'=>trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameUninomial($values)
	{
		return $this->models->Names->update(
			array('uninomial'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameSpecificEpithet($values)
	{
		return $this->models->Names->update(
			array('specific_epithet'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameInfraSpecificEpithet($values)
	{
		return $this->models->Names->update(
			array('infra_specific_epithet'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameAuthorship($values)
	{
		return $this->models->Names->update(
			array('authorship'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameAuthor($values)
	{
		return $this->models->Names->update(
			array('name_author'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameAuthorshipYear($values)
	{
		return $this->models->Names->update(
			array('authorship_year'=>(isset($values['delete']) && $values['delete']=='1' ? 'null' : trim($values['new']))),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameTypeId($values)
	{
		return $this->models->Names->update(
			array('type_id'=>trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameLanguageId($values)
	{
		return $this->models->Names->update(
			array('language_id'=>trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameReferenceId($values)
	{
		return $this->models->Names->update(
			array('reference_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameExpertId($values)
	{
		return $this->models->Names->update(
			array('expert_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameOrganisationId($values)
	{
		return $this->models->Names->update(
			array('organisation_id'=>$values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
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



	private function doNameChecks($name)
	{
		if (!$this->checkNameParts($name))
		{
			$this->addWarning("Samengevoegde naamdelen komen niet overeen met de naam.");
		}
		if (!$this->checkAuthorshipYear($name))
		{
			$this->addWarning("'Authorship year' wijkt af van 'Authorship' + 'Year'.");
		}
		if (!$this->checkYear($name))
		{
			$this->addWarning("Geen geldig jaar.");
		}
		if (!$this->checkIfConceptRetainsScientificName($name))
		{
			$this->addWarning("Aan concept is geen wetenschappelijke naam meer gekoppeld.");
		}
		if (!$this->checkIfConceptRetainsDutchName($name))
		{
			$this->addWarning("Aan concept is geen Nederlandse 'preferred name' naam meer gekoppeld.");
		}
	}


	private function checkNameParts($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;
		
		if (
			trim(str_replace('  ',' ',
				$name['uninomial'].' '.
				$name['specific_epithet'].' '.
				$name['infra_specific_epithet'].' '.
				$name['authorship']
		)) != $name['name'])
			return false;

		return true;
	}

	private function checkAuthorshipYear($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;

		if (
			trim(
				$name['name_author'].', '.
				$name['authorship_year']
			) != trim($name['authorship'],')('))
			return false;

		return true;
	}

	private function checkYear($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;
		return is_numeric($name['authorship_year']) && $name['authorship_year'] > 1000 && $name['authorship_year'] <= date('Y');
	}

	private function checkIfConceptRetainsScientificName($name)
	{
		$d=$this->getName(array(
			'taxon_id'=>$name['taxon_id'],
			'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			'language_id'=>LANGUAGE_ID_SCIENTIFIC
		));
		
		return count((array)$d)>0;
	}

	private function checkIfConceptRetainsDutchName($name)
	{
		$d=$this->getName(array(
			'taxon_id'=>$name['taxon_id'],
			'type_id'=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			'language_id'=>$this->getDefaultProjectLanguage()
		));
		
		return count((array)$d)>0;
	}
	
	private function setMessage($m=null)
	{
		if (empty($m))
			unset($_SESSION['admin']['user']['species']['message']);
		else
			$_SESSION['admin']['user']['species']['message']=$m;
	}

	private function getMessage()
	{
		return @$_SESSION['admin']['user']['species']['message'];
	}

	private function checkMessage()
	{
		$m=$this->getMessage();
		if ($m) $this->addMessage($m);
		$this->setMessage();
	}

}
