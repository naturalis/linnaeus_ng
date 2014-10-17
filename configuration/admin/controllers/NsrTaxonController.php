<?php

/*
	to do:
	implement revert

			// for revert:
			$data=$this->requestData;
			unset($data['id']);
			unset($data['action']);
			$this->smarty->assign('data',$data);


	notes:

	augustus 2014
	'endemisch' veranderd naar 'inheems', en vervolgens verwijderd, op
	verzoek van roy kleukers en ed colijn. het veld is een overblijfsel
	van een uiteindelijk niet geïmplementeerde aanpasing door trezorix.
	(betreft invoerveld in taxon en taxon_new, plus de verwerking van de
	waarde in updateConcept() -> updateConceptIsIndigeous())
	

*/

include_once ('NsrController.php');

class NsrTaxonController extends NsrController
{
	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'names',
		'name_types',
		'presence_taxa',
		'actors',
		'literature2',
		'rdf',
		'nsr_ids',
		'taxon_quick_parentage',
		'media_taxon',
		'media_meta'
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
	private $_projectRankIds;
	
	private $conceptId=null;
	private $nameId=null;
	
	private $_resPicsPerPage=100;
	

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

		$this->_projectRankIds=$this->models->ProjectRank->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id',
			'fieldAsIndex'=>'rank_id'
		));
		
	}

    public function taxonNewAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('New taxon concept'));

		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->saveConcept();

			if ($this->getConceptId())
			{
				$this->saveName();
				$this->saveDutchName();
				$this->saveTaxonParentage($this->getConceptId());
				$this->resetTree();
				$this->redirect('taxon.php?id='.$this->getConceptId());
			}		
		}

		if ($this->rHasVal('parent'))
		{
			$parent=$this->getSpeciesList(array('id'=>$this->rGetVal('parent'),'taxa_only'=>true));
			if (isset($parent[0]))
				$this->smarty->assign('parent',$parent[0]);
		}

		if ($this->rHasVal('newrank'))
		{
			$this->smarty->assign('newrank',$this->rGetVal('newrank'));
		}

		$this->smarty->assign('name_type_id',$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']);
		$this->smarty->assign('name_language_id',LANGUAGE_ID_SCIENTIFIC);
		$this->smarty->assign('ranks',$this->newGetProjectRanks());
		$this->smarty->assign('statuses',$this->getStatuses());
		$this->smarty->assign('habitats',$this->getHabitats());
		$this->smarty->assign('actors',$this->getActors());

		$this->printPage();
	}
	
    public function taxonAction()
    {
		$this->checkAuthorisation();
		
		if (!$this->rHasId()) $this->redirect('taxon_new.php');

        $this->setPageName($this->translate('Edit taxon concept'));
	
		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			//$this->setConceptId($this->rGetId());
			//$this->deleteVanAllesEnNogWat();
			//$this->deleteConcept();
			//$this->logNsrChange(array('before'=>'???','after'=>'???','note'=>'deleted concept'));
			//$this->setMessage('Concept verwijderd.');
			//$this->resetTree();
			//$this->redirect('index.php');
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setConceptId($this->rGetId());
			$this->updateConcept();
			$this->saveTaxonParentage($this->getConceptId());
			$this->resetTree();
		}
		else
		{
			$this->setConceptId($this->rGetId());
		}
	

		if ($this->rHasId())
		{
			$concept=$this->getConcept($this->rGetId());

			$this->doNameReferentialChecks($this->getConceptId());

			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('names',$this->getNames($concept));
			$this->smarty->assign('presence',$this->getPresenceData($this->rGetId()));
			$this->smarty->assign('ranks',$this->newGetProjectRanks());
			$this->smarty->assign('statuses',$this->getStatuses());
			$this->smarty->assign('habitats',$this->getHabitats());

			$this->smarty->assign('rank_id_species',$this->_projectRankIds[SPECIES_RANK_ID]['id']);
			$this->smarty->assign('rank_id_subspecies',$this->_projectRankIds[SUBSPECIES_RANK_ID]['id']);
	
		}

		$this->checkMessage();
		$this->printPage();
    }

    public function synonymAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Edit synonym'));
		$this->_nameAndSynonym();
		$this->printPage();
    }

    public function nameAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Edit name'));
		$this->_nameAndSynonym();
		$this->printPage();
	}

    private function _nameAndSynonym()
    {
	
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
			$this->updateConceptBySciName();
			$this->doNameIntegrityChecks($this->getName(array('id'=>$this->getNameId())));
		} 
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setConceptId($this->rGetVal('nameownerid'));
			$this->saveName();
			$this->updateConceptBySciName();
			$this->doNameIntegrityChecks($this->getName(array('id'=>$this->getNameId())));
		} 
		else
		{
			$this->setNameId($this->rGetId());
		}
	
		
		if ($this->getNameId())
		{
			$name=$this->getName(array('id'=>$this->getNameId()));
			$concept=$this->getConcept($name['taxon_id']);

			if (!in_array($name['nametype'],array(PREDICATE_PREFERRED_NAME,PREDICATE_ALTERNATIVE_NAME)))
			{
				if (!$this->checkNameParts($name))
				{
					if ($concept['base_rank']<SPECIES_RANK_ID)
					{
						$this->addWarning("
							Let op: het synoniem komt niet overeen met de samengestelde naamdelen. Dit is
							waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b.
							de juiste uninomial en eventueel auteurschap in om de naamkaart volledig te maken.
						");
					}
					else
					{
						$this->addWarning("
							Let op: het synoniem komt niet overeen met de samengestelde naamdelen. Dit is
							waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b.
							de juiste genus, soort, eventuele derde naamdeel en auteurschap in om de 
							naamkaart volledig te maken.
						");
					}
				}
			}

			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('name',$name);
			$this->smarty->assign('nametypes',$this->getNameTypes());
			$this->smarty->assign('languages',$this->getLanguages());
			$this->smarty->assign('actors',$this->getActors());
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

    }

    public function imagesAction()
    {
		if (!$this->rHasId()) $this->redirect('taxon_new.php');
		
		if ($this->rHasId() && $this->rHasVal('image') && $this->rHasVal('action','delete'))
		{
			$this->setConceptId($this->rGetId());
			$this->disconnectTaxonMedia($this->rGetVal('image'));
			$this->setMessage('Afbeelding ontkoppeld.');
		} 
		
		$this->checkAuthorisation();
		$this->setConceptId($this->rGetId());
        $this->setPageName($this->translate('Taxon images'));
		$this->smarty->assign('concept',$this->getConcept($this->rGetVal('id')));
		$this->smarty->assign('images',$this->getTaxonMedia());

		$this->checkMessage();
		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

		if (
			$this->rHasVal('action', 'get_lookup_list') || 
			$this->rHasVal('action', 'species_lookup') ||
			$this->rHasVal('action', 'parent_taxon_id')
		)
		{
            $return=$this->getSpeciesLookupList($this->requestData);
        } 
		else
		if (
			$this->rHasVal('action', 'expert_lookup') ||
			$this->rHasVal('action', 'name_expert_id') ||
			$this->rHasVal('action', 'name_organisation_id') ||
			$this->rHasVal('action', 'dutch_name_expert_id') ||
			$this->rHasVal('action', 'dutch_name_organisation_id') ||
			$this->rHasVal('action', 'presence_expert_id') ||
			$this->rHasVal('action', 'presence_organisation_id')
		)
		{
            $return=$this->getExpertsLookupList($this->requestData);
        }
		else
		if ($this->rHasVal('action', 'get_inheritable_name'))
		{
			$return=$this->getInheritableName(array('id'=>$this->rGetVal('id')));
        }
		
		
        $this->allowEditPageOverlay=false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
    }




	private function setConceptId($id)
	{
		$this->conceptId=$id;
	}

	private function getConceptId()
	{
		return isset($this->conceptId) ? $this->conceptId : false;
	}

	private function setNameId($id)
	{
		$this->nameId=$id;
	}

	private function getNameId()
	{
		return $this->nameId;
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
			$names[$key]['name_no_tags']=strip_tags($names[$key]['name']);
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
				_gg.name as reference_author,
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

			left join %PRE%actors _gg
				on _g.actor_id = _gg.id 
				and _g.project_id=_gg.project_id


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
		/*
			take note: presence_taxa contains a column 'presence82_id'.
			this is an obsolete leftover from a previous version. the
			values are displayed nowhere, but still exist in the database.
			connected, presence contains several statuses that are used
			only by 'presence82_id', and are therefore also obsolete.
			these statuses get a index_label of 99, based on the fact that
			they, and they alone, have no actual index_label, and are
			subsequently excluded from the list in the wehre-statement.
		*/
		
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
			and index_label != 99
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

		foreach($types as $key=>$val)
		{
			$types[$key]['noNameParts']= in_array($val['nametype'],array(PREDICATE_PREFERRED_NAME,PREDICATE_ALTERNATIVE_NAME)) ? true : false ;
		}
		
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

    private function getTaxonMedia()
    {
		$id=$this->getConceptId();

		if (empty($id))
			return;

		$overview=isset($p['overview']) ? $p['overview'] : false;
		$distributionMaps=isset($p['distribution_maps']) ? $p['distribution_maps'] : false;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : '_meta4.meta_date desc';

		$data=$this->models->Taxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				file_name as image,
				file_name as thumb,
				_k.taxon,
				_z.name as common_name,
				_j.name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				".($distributionMaps?
					"_map1.meta_data as meta_map_source,
					 _map2.meta_data as meta_map_description,": "")."
				date_format(_meta1.meta_date,'%e %M %Y') as meta_datum,
				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,
				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing,
				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer
			
			from  %PRE%media_taxon _m
			
			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
			
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _m.taxon_id=_z.taxon_id
				and _m.project_id=_z.project_id
				and _z.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
				

			left join %PRE%media_meta _map1
				on _m.id=_map1.media_id
				and _m.project_id=_map1.project_id
				and _map1.sys_label='verspreidingsKaartBron'

			left join %PRE%media_meta _map2
				on _m.id=_map2.media_id
				and _m.project_id=_map2.project_id
				and _map2.sys_label='verspreidingsKaartTitel'

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
			
			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
			
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
			
			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'
				and _meta6.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%media_meta _meta8
				on _m.id=_meta8.media_id
				and _m.project_id=_meta8.project_id
				and _meta8.sys_label='beeldbankFotograaf'
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
			
			where
				_m.project_id=".$this->getCurrentProjectId()."
				and _m.taxon_id=".$id."
				and ifnull(_meta9.meta_data,0)!=".($distributionMaps?'0':'1')."
				".($overview ? "and _m.overview_image=1" : "")."


			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);

		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');
		
		foreach((array)$data as $key=>$val)
		{
			$data[$key]['label']=
				trim(
					(isset($val['photographer']) ? $val['photographer'].', ' : '' ).
					(isset($val['meta_datum']) ? $val['meta_datum'].', ' : '' ).
					(isset($val['meta_geografie']) ? $val['meta_geografie'] : ''),
					', '
				);			

			$data[$key]['meta']=$this->models->Taxon->freeQuery("		
				select
					* 
				from
					%PRE%media_meta 
				where 
					project_id=".$this->getCurrentProjectId()."
					and media_id = ".$val['id']."
					and language_id=".$this->getDefaultProjectLanguage()."
			");

		}

		return array('count'=>$count[0]['total'],'data'=>$data,'perpage'=>$this->_resPicsPerPage);

    }

	private function disconnectTaxonMedia($image)
	{
		$id=$this->getConceptId();

		if (empty($id) || empty($image))
			return;

		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'media_id'=>$image
		);
		$data=$this->models->MediaMeta->_get(array('id'=>$p));
		$this->models->MediaMeta->delete($p);
		$this->logNsrChange(array('before'=>$data,'note'=>'deleted media meta-data'));

		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$image,
			'taxon_id'=>$id
		);
		$data=$this->models->MediaTaxon->_get(array('id'=>$p));
		$this->models->MediaTaxon->delete($p);
		$this->logNsrChange(array('before'=>$data,'note'=>'disconnected media from taxon'));
	}




	private function getSpeciesList($p)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
        $id=isset($p['id']) ? (int)$p['id'] : null;
        $nametype=isset($p['nametype']) ? (int)$p['nametype'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        //$formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;
		$limit=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
		
		$search=trim($search);
		
		if (empty($search) && empty($id))
			return null;

		$taxa=$this->models->Names->freeQuery("
			select
				_a.taxon_id as id,
				concat(_a.name,' [',_q.rank,']') as label,
				_e.rank_id,
				_e.taxon,
				_f.rank_id as base_rank_id,
				_q.rank as rank,
				_a.uninomial,
				_a.specific_epithet,
				
				_b.nametype,
	
				case
					when _a.name REGEXP '^".mysql_real_escape_string($search)."$' = 1 then 100
					when _a.name REGEXP '^".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 then 95
					when _a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 then 90
					when _a.name REGEXP '^".mysql_real_escape_string($search)."(.*)$' = 1 then 80
					when _a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."(.*)$' = 1 then 70
					when _a.name REGEXP '^(.*)".mysql_real_escape_string($search)."(.*)$' = 1 then 60
					else 50
				end as match_percentage,
	
				case
					when _f.rank_id >= ".SPECIES_RANK_ID." then 100
					else 50
				end as adjusted_rank
			
			from %PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%nsr_ids _ids
				on _a.taxon_id =_ids.lng_id 
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'taxon'

			where _a.project_id =".$this->getCurrentProjectId()."
				and _a.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
				and _b.nametype in (
					'".PREDICATE_PREFERRED_NAME."',
					'".PREDICATE_VALID_NAME."',
					'".PREDICATE_ALTERNATIVE_NAME."',
					'".PREDICATE_SYNONYM."',
					'".PREDICATE_SYNONYM_SL."',
					'".PREDICATE_HOMONYM."',
					'".PREDICATE_BASIONYM."',
					'".PREDICATE_MISSPELLED_NAME."'
				)

			".($taxaOnly ? "and _a.type_id = ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] : "" )."
			".($rankAbove ? "and _f.rank_id < ".$rankAbove : "" )."
			".($rankEqualAbove ? "and _f.rank_id <= ".$rankEqualAbove : "" )."
			".($id ? "and _a.taxon_id = ".$id : "" )."

			".($nametype ? "and _b.nametype = ".$nametype : "" )."
			
			group by _a.taxon_id

			order by 
				adjusted_rank desc, match_percentage desc, _a.name asc, _f.rank_id asc, ".
				(!empty($p['sort']) && $p['sort']=='preferredNameNl' ? "common_name" : "taxon" )."
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		);

		foreach ((array) $taxa as $key => $val)
		{
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

			unset($taxa[$key]['match_percentage']);
			unset($taxa[$key]['adjusted_rank']);
			unset($taxa[$key]['uninomial']);
			unset($taxa[$key]['specific_epithet']);
		}

		return $taxa;

	}

	private function getInheritableName($p)
	{
        $taxonId=isset($p['id']) ? (int)$p['id'] : null;

		if (empty($taxonId))
			return null;

		$taxa=$this->models->Names->freeQuery("
			select
				_a.*, _f.rank_id as base_rank_id
			from
				%PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _a.type_id = ".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
				and _a.taxon_id =".$taxonId
		);
		
		$val=$taxa[0];
		
		if ($val['base_rank_id']==GENUS_RANK_ID)
		{
			$inheritableName=$val['uninomial'];
		}
		else
		if ($val['base_rank_id']==SPECIES_RANK_ID)
		{
			$inheritableName=$val['uninomial'].' '.$val['specific_epithet'];
		}
		else
		{
			$inheritableName="";
		}

		return $inheritableName;

	}

    private function getSpeciesList_ORG($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;
        $id=isset($p['id']) ? (int)$p['id'] : null;

        //if (empty($search) && !$getAll)
        if (empty($search) && empty($id)) return;

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
			".($id ? "and id = ".$id : "")."
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

		return $taxa;
		
	}

    private function getSpeciesLookupList($p)
    {
		$taxa=$this->getSpeciesList($p);
		
		$maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

		return
			$this->makeLookupList(array(
				'data'=>$taxa,
				'module'=>'species',
				'url'=>'../species/taxon.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($taxa)<$maxResults
			));

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
			$this->makeLookupList(array(
				'data'=>$data,
				'module'=>'actors',
				'url'=>'actor.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($data)<$maxResults
			));

    }



	private function saveConcept()
	{
		$name=$this->rGetVal('concept_taxon');
		$rank=$this->rGetVal('concept_rank_id');
		$name=trim($name['new']);
		$rank=trim($rank['new']);
					
		if (empty($name) || empty($rank))
		{
			if (empty($name)) $this->addError('Lege conceptnaam. Concept niet opgeslagen.');
			if (empty($rank)) $this->addError('Geen rang. Concept niet opgeslagen.');
			$this->setConceptId(null);
			return;
		}

		$d=$this->models->Taxon->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'is_empty' =>'0',
			'rank_id' => $rank,
			'taxon' => $name,
		));
		
		if ($d)
		{
			$this->setConceptId($this->models->Taxon->getNewId());
			$this->addMessage('Nieuw concept aangemaakt.');
			$newconcept=$this->getConcept($this->getConceptId());
			$this->logNsrChange(array('after'=>$newconcept,'note'=>'new concept '.$newconcept['taxon']));
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
		
		$before=$this->getConcept($this->rGetId());
		$before['presence']=$this->getPresenceData($this->rGetId());

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

		$after=$this->getConcept($this->rGetId());
		$after['presence']=$this->getPresenceData($this->rGetId());

		$this->logNsrChange(array('before'=>$before,'after'=>$after,'note'=>'updated concept '.$before['taxon']));
		
	}

	private function updateConceptTaxon($values)
	{
		$before=$this->models->Taxon->_get(array(
			'id'=>array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId()),
			'columns'=>'taxon'
		));

		$result=$this->models->Taxon->update(
			array('taxon'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);

		if ($result)
		{
			$after=$this->models->Taxon->_get(array(
				'id'=>array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId()),
				'columns'=>'taxon'
			));
			$this->logNsrChange(array('before'=>$before,'after'=>$after,'note'=>'updated concept name '.$before['taxon']));
		}
		return $result;
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
				
			$this->logNsrChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));

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

			$this->logNsrChange(array('after'=>$nsr,'note'=>'created NSR ID '.$nsr));
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
			$newname=$this->getName(array('id'=>$this->getNameId()));
			$this->logNsrChange(array('after'=>$newname,'note'=>'new name '.$newname['name']));
			$this->addMessage('Nieuwe naam aangemaakt.');
			$this->updateName();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe naam mislukt.');
		}
	}

	private function saveDutchName()
	{
		$name=$this->rGetVal('dutch_name');

		if (!isset($name['new'])) return;

		$d=$this->models->Names->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $this->getConceptId(),
				'language_id' => LANGUAGECODE_DUTCH,
				'type_id' => $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
				'name' => trim($name['new'])
			));

		if ($d)
		{
			$this->setNameId($this->models->Names->getNewId());
			$this->addMessage('Nederlandse naam aangemaakt.');

			$newname=$this->getName(array('id'=>$this->getNameId()));
			$this->logNsrChange(array('after'=>$newname,'note'=>'new dutch name '.$newname['name']));

			if ($this->rHasVar('dutch_name_reference_id'))
			{
				if (!$this->updateNameReferenceId($this->rGetVal('dutch_name_reference_id')))
				{
					$this->addError('Nederlandse naam: referentie niet opgeslagen.');
				}
			}
	
			if ($this->rHasVar('dutch_name_expert_id'))
			{
				if (!$this->updateNameExpertId($this->rGetVal('dutch_name_expert_id')))
				{
					$this->addError('Nederlandse naam: expert niet opgeslagen.');
				}
			}
	
			if ($this->rHasVar('dutch_name_organisation_id'))
			{
				if (!$this->updateNameOrganisationId($this->rGetVal('dutch_name_organisation_id')))
				{
					$this->addError('Nederlandse naam: organisatie niet opgeslagen.');
				}
			}

		}
		else 
		{
			$this->addError('Aanmaak Nederlandse naam mislukt.');
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

		$after=$this->getName(array('id'=>$this->getNameId()));
		$this->logNsrChange(array('before'=>$name,'after'=>$after,'note'=>'updated name '.$before['name']));
			
	}

	private function deleteName()
	{
		$p=array(
			'project_id'=>$this->getCurrentProjectId(),
			'id'=>$this->getNameId()
		);
		$before=$this->models->Names->_get(array('id'=>$p));
		$d=$this->models->Names->delete($p);
		if ($d)
		{
			$this->logNsrChange(array('before'=>$before,'note'=>'deleted name '.$name['name']));
		}
		return $d;
	}

	private function updateConceptBySciName()
	{
		$name=$this->getName(array('id'=>$this->getNameId()));

		if (!empty($name['type_id']) && $name['type_id']==$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] && $this->rHasVar('name_name'))
		{
			if ($this->updateConceptTaxon($this->rGetVal('name_name')))
			{
				$this->addMessage('Conceptnaam opgeslagen.');
			}
			else
			{
				$this->addError('Conceptnaam niet opgeslagen.');
			}

		}
	}

	private function updateNameName($values)
	{
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


	private function getProgeny($parent,$level,$family)
	{
		$result = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'parent_id' => $parent
				),
				'columns' => 'id,parent_id,taxon,'.$level.' as level'
			)
		);

		$family[]=$parent;

		foreach((array)$result as $row)
		{
			$row['parentage']=$family;
			$this->tmp[]=$row;
			$this->getProgeny($row['id'],$level+1,$family);
		}
	}

	private function doNameIntegrityChecks($name)
	{
		if (!$this->checkNameParts($name))
		{
			$this->addWarning("Samengevoegde naamdelen komen niet overeen met de naam.");
		}
		if (!$this->checkAuthorshipYear($name))
		{
			$this->addWarning("'Auteurschap' wijkt af van 'auteur(s)' + 'jaar'.");
		}
		if (!$this->checkYear($name))
		{
			$this->addWarning("Geen geldig jaar.");
		}
	}

	private function doNameReferentialChecks($concept)
	{
		if (!$this->checkIfConceptRetainsScientificName($concept))
		{
			$this->addWarning("Aan concept is geen wetenschappelijke naam gekoppeld.");
		}
		if (!$this->checkIfConceptRetainsDutchName($concept))
		{
			$this->addWarning("Aan concept is geen Nederlandse voorkeursnaam gekoppeld.");
		}
	}

	private function checkNameParts($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;
		
		if (
			trim(str_replace('  ',' ',
				(!empty($name['uninomial']) ? $name['uninomial'].' ' : null).
				(!empty($name['specific_epithet']) ? $name['specific_epithet'].' ' : null).
				(!empty($name['infra_specific_epithet']) ? $name['infra_specific_epithet'].' ' : null).
				(!empty($name['authorship']) ? $name['authorship'] : null)
		)) != $name['name'])
			return false;

		return true;
	}

	private function checkAuthorshipYear($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;
		if ($name['language_id']==LANGUAGE_ID_SCIENTIFIC && empty($name['authorship_year']) && empty($name['name_author'])) return true;

		if (
			trim(
				(!empty($name['name_author']) ? $name['name_author'].', ' : null).
				(!empty($name['authorship_year']) ? $name['authorship_year'] : null),', '
			) != trim($name['authorship'],')( '))
			return false;

		return true;
	}

	private function checkYear($name)
	{
		if ($name['language_id']!=LANGUAGE_ID_SCIENTIFIC) return true;
		if ($name['language_id']==LANGUAGE_ID_SCIENTIFIC && empty($name['authorship_year'])) return true;
		return is_numeric($name['authorship_year']) && $name['authorship_year'] > 1000 && $name['authorship_year'] <= date('Y');
	}

	private function checkIfConceptRetainsScientificName($concept)
	{
		$d=$this->getName(array(
			'taxon_id'=>$concept,
			'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			'language_id'=>LANGUAGE_ID_SCIENTIFIC
		));
		
		return count((array)$d)>0;
	}

	private function checkIfConceptRetainsDutchName($concept)
	{
		$d=$this->getName(array(
			'taxon_id'=>$concept,
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





















