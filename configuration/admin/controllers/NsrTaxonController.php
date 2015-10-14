<?php

/*
	notes:

	augustus 2014
	'endemisch' veranderd naar 'inheems', en vervolgens verwijderd, op
	verzoek van roy kleukers en ed colijn. het veld is een overblijfsel
	van een uiteindelijk niet geïmplementeerde aanpasing door trezorix.
	(betreft invoerveld in taxon en taxon_new, plus de verwerking van de
	waarde in updateConcept() -> updateConceptIsIndigeous())
	
	
	REFAC2015: need language names adjectives throughout! search for
	"Nederlandse" and replace with an adjectivized resolved language_id

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
		'trash_can',
		'traits_groups',
		'names_additions'
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

		$this->_taxon_main_image_base_url = $this->getSetting( "taxon_main_image_base_url", "http://images.naturalis.nl/comping/" );
		$this->smarty->assign( 'taxon_main_image_base_url',$this->_taxon_main_image_base_url );

	}

    public function taxonNewAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('New taxon concept'));

		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
		
			$this->saveConcept();

			if ( $this->getConceptId() )
			{
				$this->saveName();
				$this->checkMainLanguageCommonName();
				$this->saveMainLanguageCommonName();
				$this->saveTaxonParentage( $this->getConceptId() );
				
				$this->redirect('taxon.php?id='.$this->getConceptId());
			}		
			else
			{
				$data=$this->requestData;
				array_walk($data, function(&$val, $key){if (isset($val['new'])) $val=$val['new'];});
				unset($data['action']);
				
				if (isset($data['parent_taxon_id']))
				{
					$d=$this->getConcept($data['parent_taxon_id']);
					$texts['parent_taxon']=$d['taxon'];
				}
				if (isset($data['name_reference_id']))
				{
					$d=$this->getReference($data['name_reference_id']);
					$texts['name_reference']=$d['label'];
				}
				if (isset($data['main_language_name_reference_id']))
				{
					$d=$this->getReference($data['main_language_name_reference_id']);
					$texts['main_language_name_reference']=$d['label'];
				}
				if (isset($data['presence_reference_id']))
				{
					$d=$this->getReference($data['presence_reference_id']);
					$texts['presence_reference']=$d['label'];
				}
				$this->smarty->assign('data',$data);
				$this->smarty->assign('texts',$texts);
			}
		}

		if ($this->rHasVal('parent'))
		{
			$parent=$this->getSpeciesList(array('id'=>$this->rGetVal('parent'),'taxa_only'=>true));

			if (isset($parent[0]))
			{
				$this->smarty->assign('parent',$parent[0]);
			}
		}

		if ($this->rHasVal('newrank'))
		{
			$this->smarty->assign('newrank',$this->rGetVal('newrank'));
		}

		$this->smarty->assign('name_type_id',$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']);
		$this->smarty->assign('name_language_id',LANGUAGE_ID_SCIENTIFIC);
		$this->smarty->assign('main_language_name_language_id',$this->getDefaultProjectLanguage());

		$this->smarty->assign('main_language_name_header',$this->getDefaultProjectLanguage());

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
	
		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			$this->setConceptId( $this->rGetId() );
			$this->toggleConceptDeleted(true);
			$this->setMessage('Concept gemarkeerd als verwijderd.');
			$this->resetTree();
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','undelete') && !$this->isFormResubmit())
		{
			$this->setConceptId( $this->rGetId() );
			$this->toggleConceptDeleted(false);
			$this->setMessage('Concept niet langer gemarkeerd als verwijderd.');
			$this->resetTree();
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->setConceptId( $this->rGetId() );

			if ($this->needParentChange() && $this->canParentChange())
			{
				$this->updateConcept();
				$this->saveTaxonParentage( $this->getConceptId() );
				$this->doParentChange();
			}
			else
			{
				$this->updateConcept();
				$this->saveTaxonParentage( $this->getConceptId() );
			}
			$this->resetTree();
		}
		else
		{
			$this->setConceptId( $this->rGetId() );
		}
	

		if ($this->rHasId())
		{
			$concept=$this->getConcept($this->rGetId());

			$this->doNameReferentialChecks($this->getConcept( $this->getConceptId() ));

			$rankIdSpecies=!empty($this->_projectRankIds[SPECIES_RANK_ID]['id']) ? $this->_projectRankIds[SPECIES_RANK_ID]['id'] : -1;
			$rankIdSubSpecies=!empty($this->_projectRankIds[SUBSPECIES_RANK_ID]['id']) ? $this->_projectRankIds[SUBSPECIES_RANK_ID]['id'] : -1; 

			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('names',$this->getNames($concept));
			$this->smarty->assign('presence',$this->getPresenceData($this->rGetId()));
			$this->smarty->assign('ranks',$this->newGetProjectRanks());
			$this->smarty->assign('statuses',$this->getStatuses());
			$this->smarty->assign('habitats',$this->getHabitats());
			$this->smarty->assign('actors',$this->getActors());
			$this->smarty->assign('traitgroups',$this->getTraitgroups());
			$this->smarty->assign('rank_id_species',$rankIdSpecies);
			$this->smarty->assign('rank_id_subspecies',$rankIdSubSpecies);
			$this->smarty->assign('main_language_name_language_id',$this->getDefaultProjectLanguage());
		}

		$this->checkMessage();
		$this->printPage();
    }

    public function synonymAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Bewerk wetenschappelijke naam'));
		$this->_nameAndSynonym();
		$this->printPage();
    }

    public function nameAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Bewerk naam'));
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
			
			if ($this->needParentChange()!=false && $this->canParentChange()!=false)
			{
				$this->doParentChange();
				$name=$this->getName(array('id'=>$this->getNameId()));
				$this->saveTaxonParentage($name['taxon_id']);
			}
			else
			{
				$this->updateName();
				$this->updateConceptBySciName();
				$this->doNameIntegrityChecks($this->getName(array('id'=>$this->getNameId())));
			}
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
				if (!$this->checkNamePartsMatchName($name))
				{
					if ($concept['base_rank']<SPECIES_RANK_ID)
					{
						$this->addWarning("
							Let op: de wetenschappelijke naam komt niet overeen met de samengestelde naamdelen.
							Dit is waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b.
							de juiste uninomial en eventueel auteurschap in om de naamkaart volledig te maken.
						");
					}
					else
					{
						$this->addWarning("
							Let op: de wetenschappelijke naam komt niet overeen met de samengestelde naamdelen.
							Dit is waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b.
							de juiste genus, soort, eventuele derde naamdeel en auteurschap in om de naamkaart
							volledig te maken.
						");
					}
				}
			}

			$this->smarty->assign('name',$name);
		}
		else
		if ($this->rHasVal('taxon'))
		{
			$concept=$this->getConcept($this->rGetVal('taxon'));
			$this->smarty->assign('newname',true);
		}
		else
		{
			$this->addError('Geen ID.');
		}
		
		if (isset($concept))
		{
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('preferrednames',$this->getPreferredNames($concept['id']));
			$this->smarty->assign('preferrednameid',$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']);
			$this->smarty->assign('alternativenameid',$this->_nameTypeIds[PREDICATE_ALTERNATIVE_NAME]['id']);
			$this->smarty->assign('hasvalidname',$this->checkIfConceptRetainsScientificName($concept['id']));
			$this->smarty->assign('validnameid',$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']);
			$this->smarty->assign('nametypes',$this->getNameTypes());
			$this->smarty->assign('languages',$this->getLanguages());
			$this->smarty->assign('actors',$this->getActors());
			$this->smarty->assign('projectlanguages',$this->getProjectLanguages());
			$this->smarty->assign('defaultprojectlanguage',$this->getDefaultProjectLanguage());
		}

    }

    public function taxonEditConceptDirectAction()
    {
		$this->checkAuthorisation();

		if ( !$this->rHasId() ) $this->redirect('taxon_new.php');

        $this->setPageName( $this->translate('Naam concept direct aanpassen') );
		$this->setConceptId( $this->rGetId() );
		
		if ( $this->rHasVal('taxon') && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			if ($this->updateConceptTaxon(array('new'=>$this->rGetVal('taxon'))))
			{
				$this->addMessage( 'Naam opgeslagen' );
				$this->resetTree();
			}
			else
			{
				$this->addWarning( 'Naam niet opgeslagen' );
			}
		}

		$concept=$this->getConcept($this->getConceptId());
		$this->smarty->assign('concept',$concept);
		$this->smarty->assign('validname',
			$this->getName(array('taxon_id'=>$this->rGetId(),'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']))
		);
		$this->printPage();
	}

    public function taxonEditSynonymDirectAction()
    {
		$this->checkAuthorisation();

		if ( !$this->rHasId() ) $this->redirect('synonym.php');

        $this->setPageName( $this->translate('Geldige naam direct aanpassen') );

		$this->setNameId( $this->rGetId() );
		$name=$this->getName(array('id'=>$this->getNameId()));

		$this->setConceptId( $name['taxon_id'] );
		$concept=$this->getConcept($this->getConceptId());
		
		if ($name['type_id']!=$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'])
		{
			$this->redirect('synonym.php');
		}
		
		if ( $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->updateName();
			$this->addMessage( 'Naam opgeslagen' );
			$this->resetTree();
		}

		$this->smarty->assign('concept',$concept);
		$this->smarty->assign('name',$this->getName(array('id'=>$this->getNameId())));
		$this->printPage();
	}


    public function taxonDeletedAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Taxa gemarkeerd als verwijderd'));
		$this->smarty->assign('concepts',$this->getDeletedSpeciesList());
		$this->printPage();
	}

    public function updateParentageAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Indextabel bijwerken'));

		if ($this->rHasVal('action','update') && !$this->isFormResubmit())
		{
			$this->saveTaxonParentage();
			$this->addMessage('Tabel bijgewerkt');
		}
		
		$this->printPage();
	}

    public function nsrIdResolverAction()
    {

		$this->checkAuthorisation();
		
        $this->setPageName($this->translate('NSR ID resolver'));

		if ( ($this->rHasVal('action','resolve') || $this->rHasVal('action','download')) && $this->rHasVal('codes') )
		{
			$t="%PRE%_tmp_".substr( "abcdefghijklmnopqrstuvwxyz", mt_rand( 0, 25 ), 1 ) .substr( md5( time() ), 1 );

			$this->models->NsrIds->freeQuery("drop table ".$t);
			$this->models->NsrIds->freeQuery("
				create table ".$t." (
					`id` int(11) not null primary key,
					`code_1` varchar(16) not null, 
					`code_2` varchar(32) not null, 
					key `key_code_1` (`code_1`), key `key_code_2` (`code_2`))");

			$codes=explode(PHP_EOL,trim($this->rGetVal('codes')));
			array_walk($codes,function(&$val,$key){ $val=substr(str_pad(trim($val),12,"0", STR_PAD_LEFT),-12);});

			$pre='tn.nlsr.concept/';
			$buffer=array();
			foreach((array)$codes as $line=>$code)
			{
				$buffer[]= "(".$line.",'" . mysql_real_escape_string( $code ) . "','". mysql_real_escape_string( $pre . $code ) . "')";
				if ($line > 0 && ($line % 500)==0)
				{
					$this->models->NsrIds->freeQuery("insert into ".$t." values ".implode(",",$buffer));
					$buffer=array();
				}
			}

			$this->models->NsrIds->freeQuery("insert into ".$t." values ".implode(",",$buffer));

			$result=$this->models->NsrIds->freeQuery("
				select
					_a.id as line,
					_a.code_1 as code,
					ifnull(_b.lng_id,_c.lng_id) as lng_id,
					ifnull(_t1.taxon,_t2.taxon) as taxon
					
				from ".$t." _a

				left join %PRE%nsr_ids _b
					on _a.code_1 = _b.nsr_id
					and _b.project_id = ".$this->getCurrentProjectId()."
					and _b.item_type = 'taxon'
					and _b.lng_id is not null

				left join %PRE%nsr_ids _c
					on _a.code_2 = _c.nsr_id
					and _c.project_id = ".$this->getCurrentProjectId()."
					and _c.item_type = 'taxon'
					and _c.lng_id is not null

				left join %PRE%taxa _t1
					on _b.lng_id = _t1.id
					and _t1.project_id = ".$this->getCurrentProjectId()."
					
				left join %PRE%taxa _t2
					on _c.lng_id = _t2.id
					and _t2.project_id = ".$this->getCurrentProjectId()."
					
				group by _a.id

				order by _a.id
			");
				
			@$this->models->NsrIds->freeQuery("drop table ".$t);

			if ($this->rHasVal('action','download'))
			{
				header('Content-Type: text/plain');
				header('Content-Disposition: attachment; filename=nsr_id-lng_id-match--'.date('Ymd-His').'.txt');
				header('Pragma: no-cache');	
	
				foreach($result as $val)
				{
					echo 
						$val["line"] . chr(9) . 
						$val["code"] . chr(9) . 
						$val["lng_id"] . chr(9) . 
						$val["taxon"] . PHP_EOL;
				}	
				die();		
			}
		}

		$this->smarty->assign('result',isset($result) ? $result : null);
		$this->smarty->assign('codes',$this->rGetVal('codes'));

		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action'))
            return;

		if (
			$this->rHasVal('action', 'get_lookup_list') || 
			$this->rHasVal('action', 'species_lookup') ||
			$this->rHasVal('action', 'taxon_id') ||
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
			$this->rHasVal('action', 'main_language_name_expert_id') ||
			$this->rHasVal('action', 'main_language_name_organisation_id') ||
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

	private function setIsNewRecord($state)
	{
		$this->isNewRecord=$state;
	}

	private function getIsNewRecord()
	{
		return isset($this->isNewRecord) ? $this->isNewRecord : false;
	}

	private function getNameAddition($p)
	{
		$name_id=isset($p['name_id']) ? $p['name_id'] : null;

		if (is_null($name_id)) return;

		return $this->models->NamesAdditions->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'name_id'=>$name_id
			),
			'columns'=>'id,language_id,addition',
			'fieldAsIndex'=>'language_id'
		));
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
						_d.label as language_label,
						replace(_ids.nsr_id,'tn.nlsr.name/','') as nsr_id

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

					left join %PRE%nsr_ids _ids
						on _a.id =_ids.lng_id 
						and _a.project_id = _ids.project_id
						and _ids.item_type = 'name'

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						".(isset($taxonId) ? "and _a.taxon_id=".$taxonId: "" )."
						".(isset($languageId) ? "and _a.language_id=".$languageId: "" )."
						".(isset($typeId) ? "and _a.type_id=".$typeId: "" )."
						".(isset($id) ? "and _a.id=".$id: "" )
			)
		);

		$name=$name[0];
		
		$name['addition']=$this->getNameAddition(array('name_id'=>$name['id']));
		
		return $name;
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

			$names[$key]['addition']=$this->getNameAddition(array('name_id'=>$val['id']));

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

	private function getPreferredNames($concept)
	{
		
		if (empty($concept))
			return;

        $names=$this->models->Names->freeQuery(
			array(
				'query' => "
					select
						_a.id,
						_a.name,
						_a.language_id,
						_c.language,
						ifnull(_d.label,_c.language) as language_label

					from %PRE%names _a 

					left join %PRE%languages _c
						on _a.language_id=_c.id

					left join %PRE%labels_languages _d
						on _a.language_id=_d.language_id
						and _a.project_id=_d.project_id
						and _d.label_language_id=".$this->getDefaultProjectLanguage()."

					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.taxon_id = ".$concept."
						and _a.type_id = ".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
						"
			)
		);
		
		return $names;

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
            	ifnull(_b.label,_a.sys_label) as label

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
		

		foreach((array)$types as $key=>$val)
		{
			$types[$key]['nametype_label']=$this->Rdf->translatePredicate($val['nametype'],true);
			$types[$key]['noNameParts']= in_array($val['nametype'],array(PREDICATE_PREFERRED_NAME,PREDICATE_ALTERNATIVE_NAME)) ? true : false ;
		}
		
		return $types;
	}

	private function getLanguages()
	{
        $languages=$this->models->Language->freeQuery("
			select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label,
				case
					when _c.id= " . LANGUAGE_ID_SCIENTIFIC. " then 99
					when _c.id= " . $this->getDefaultProjectLanguage() . " then 98
					when _c.id= " . LANGUAGE_ID_DUTCH . " then 97
					when _c.id= " . LANGUAGE_ID_ENGLISH . " then 97
					else 0 
				end as sort_criterium

			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$this->getCurrentProjectId()."
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."
				order by sort_criterium desc, label asc
			");
			
		return $languages;
	}

	private function getDeletedSpeciesList()
	{
		$taxa=$this->models->Taxon->freeQuery("
			select
				_a.id,
				_a.taxon,
				_q.rank,
				concat(_user.first_name,' ',_user.last_name) as deleted_by,
				date_format(_trash.created,'%d-%m-%Y %T') as deleted_when
			
			from %PRE%taxa _a
			
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%users _user
				on _trash.user_id = _user.id
				
			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id 
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'taxon'

			where _a.project_id =".$this->getCurrentProjectId()."
				and ifnull(_trash.is_deleted,0)=1

			order by _trash.created desc
		");

		return $taxa;
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
        $haveDeleted = isset($p['have_deleted']) ? $p['have_deleted'] : 'no'; // yes, no, only
		
		$search=trim($search);
		
		if (empty($search) && empty($id) && $haveDeleted!='only')
		{
			return null;
		}

		$taxa=$this->models->Names->freeQuery("
			select
				_a.taxon_id as id,
				concat(_a.name,' [',ifnull(_q.label,_x.rank),'%s]') as label,
				_e.rank_id,
				_e.taxon,
				_a.name,
				_common.name as common_name,
				_f.rank_id as base_rank_id,
				_x.rank,
				_a.uninomial,
				_a.specific_epithet,
				_b.nametype,
				ifnull(_d.label,_c.language) as language_label,
				ifnull(_trash.is_deleted,0) as is_deleted,
	
				case
					when
						_a.name REGEXP '^".mysql_real_escape_string($search)."$' = 1
						or
						trim(concat(
							if(_a.uninomial is null,'',concat(_a.uninomial,' ')),
							if(_a.specific_epithet is null,'',concat(_a.specific_epithet,' ')),
							if(_a.infra_specific_epithet is null,'',concat(_a.infra_specific_epithet,' '))
						)) REGEXP '^".mysql_real_escape_string($search)."$' = 1
					then 100
					when
						_a.name REGEXP '^".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 95
					when
						_a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 90
					when
						_a.name REGEXP '^".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 85
					when
						_a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 80
					when 
						_a.name REGEXP '^(.*)".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 75
					when
						_a.name REGEXP '^".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 70
					when
						_a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 65
					when
						_a.name REGEXP '^".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 60
					when
						_a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 55
					when 
						_a.name REGEXP '^(.*)".mysql_real_escape_string($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 50

					else 10
				end as match_percentage,
	
				case
					when _f.rank_id >= ".SPECIES_RANK_ID." then 100
					else 50
				end as adjusted_rank
				
			from %PRE%names _a

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%names _common
				on _e.id = _common.taxon_id
				and _e.project_id = _common.project_id
				and _common.type_id = ".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _common.language_id=".$this->getDefaultProjectLanguage()."
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _x
				on _f.rank_id=_x.id

			left join %PRE%labels_projects_ranks _q
				on _e.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$this->getDefaultProjectLanguage()."
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id = _trash.lng_id
				and _trash.item_type='taxon'

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
			".($haveDeleted=='no' ? "and ifnull(_trash.is_deleted,0)=0" :  "" )."
			".($haveDeleted=='only' ? "and ifnull(_trash.is_deleted,0)=1" : "" )."
		
			order by 
				match_percentage desc, 
				_e.taxon asc, 
				_f.rank_id asc, ".
				(!empty($p['sort']) && $p['sort']=='preferredNameNl' ?
					"common_name" :
					"taxon" 
				)."
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
			
			if ($val['nametype']!=PREDICATE_VALID_NAME && $val['nametype']!=PREDICATE_PREFERRED_NAME)
			{
				$taxa[$key]['label']=sprintf($taxa[$key]['label'],'; '.sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']));
			}
			else
			{
				$taxa[$key]['label']=sprintf($taxa[$key]['label'],'');
			}
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

	private function checkParentChildRelationship($child_base_rank,$parent_id)
	{
		$d=$this->getTaxonById($parent_id);
		$parent_base_rank=$d['base_rank'];
		
		$ranks=$this->models->Rank->_get(array(
			'id'=>'*',
			'columns'=>'id,rank',
			'fieldAsIndex'=>'id'
		));
		
		$error=null;
		
		if ($child_base_rank>SPECIES_RANK_ID && $parent_base_rank!=SPECIES_RANK_ID)
		{
			/*
			forma moet onder soort
			varietas moet onder soort
			cultivar moet onder soort
			forma specialis moet onder soort
			ondersoort moet onder soort
			*/
			$error=array($ranks[SPECIES_RANK_ID]['rank']);
		} 
		else
		if (($child_base_rank==SPECIES_RANK_ID && $parent_base_rank!=GENUS_RANK_ID) &&
			($child_base_rank==SPECIES_RANK_ID && $ranks[$parent_base_rank]['rank']!='subgenus'))
		{
			// soort moet onder genus of subgenus
			$error=array($ranks[GENUS_RANK_ID]['rank'],'subgenus');
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subgenus' && $parent_base_rank!=GENUS_RANK_ID)
		{
			// subgenus moet onder genus
			$error=array($ranks[GENUS_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==GENUS_RANK_ID && $ranks[$parent_base_rank]['rank']!='subfamilia') &&
			($child_base_rank==GENUS_RANK_ID && $parent_base_rank!=FAMILY_RANK_ID))
		{
			// genus moet onder subfamilie of familie
			$error=array('subfamilia',$ranks[FAMILY_RANK_ID]['rank']);
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subfamilia' && $parent_base_rank!=FAMILY_RANK_ID)
		{
			// subfamilie moet onder familie
			$error=array($ranks[FAMILY_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==FAMILY_RANK_ID && $ranks[$parent_base_rank]['rank']!='subordo') &&
			($child_base_rank==FAMILY_RANK_ID && $ranks[$parent_base_rank]['rank']!='ordo') &&
			($child_base_rank==FAMILY_RANK_ID && $ranks[$parent_base_rank]['rank']!='superfamilia'))
		{
			// familie moet onder suborde, orde of superfamilia
			$error=array('subordo','ordo','superfamilia');
		}
		else
		if (($ranks[$child_base_rank]['rank']=='superfamilia' && $ranks[$parent_base_rank]['rank']!='ordo') &&
			($ranks[$child_base_rank]['rank']=='superfamilia' && $ranks[$parent_base_rank]['rank']!='subordo'))
		{
			// superfamilia moet onder orde of subordo
			$error=array('ordo','subordo');
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subordo' && $ranks[$parent_base_rank]['rank']!='ordo')
		{
			// suborde moet onder orde
			$error=array('ordo');
		}
		else
		if (($ranks[$child_base_rank]['rank']=='ordo' && $ranks[$parent_base_rank]['rank']!='subclassis') &&
			($ranks[$child_base_rank]['rank']=='ordo' && $ranks[$parent_base_rank]['rank']!='classis') &&
			($ranks[$child_base_rank]['rank']=='ordo' && $ranks[$parent_base_rank]['rank']!='superorder') 
			)
		{
			// orde moet onder subklasse, klasse of superorder
			$error=array('subclassis','classis','superorder');
		}
		else
		if (($ranks[$child_base_rank]['rank']=='superorder' && $ranks[$parent_base_rank]['rank']!='classis') && 
			($ranks[$child_base_rank]['rank']=='superorder' && $ranks[$parent_base_rank]['rank']!='subclassis'))
		{
			// superordo moet onder klasse of subclassis
			$error=array('classis','subclassis');
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subclassis' && $ranks[$parent_base_rank]['rank']!='classis')
		{
			// subklasse moet onder klasse
			$error=array('classis');
		}
		else
		if (($ranks[$child_base_rank]['rank']=='classis' && $ranks[$parent_base_rank]['rank']!='subphylum') &&
			($ranks[$child_base_rank]['rank']=='classis' && $ranks[$parent_base_rank]['rank']!='phylum'))
		{
			// klasse moet onder subphylum of phylum
			$error=array('subphylum','phylum');
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subphylum' && $ranks[$parent_base_rank]['rank']!='phylum')
		{
			// subphylum moet onder phylum
			$error=array('phylum');
		}
		else
		if (($ranks[$child_base_rank]['rank']=='phylum' && $ranks[$parent_base_rank]['rank']!='subregnum') &&
			($ranks[$child_base_rank]['rank']=='phylum' && $ranks[$parent_base_rank]['rank']!='regnum'))
		{
			// phylum moet moet onder subrijk of rijk
			$error=array('subregnum','regnum');
		}
		else
		if ($ranks[$child_base_rank]['rank']=='subregnum' && $ranks[$parent_base_rank]['rank']!='regnum')
		{
			// subrijk moet onder rijk
			$error=array('regnum');
		}

		if ($error)
		{
			$this->addError(
				sprintf(
					"Een %s kan alleen %s als ouder hebben.",
					$ranks[$child_base_rank]['rank'],
					implode(" of ",$error)
				).
				" Concept niet opgeslagen."

			);
			return false;
		}

		return true;
	}

	private function checkAuthorshipAgainstRank($baseRank,$authorship)
	{
		//if ($baseRank>=GENUS_RANK_ID && empty($authorship))
		if ($baseRank>GENUS_RANK_ID && empty($authorship))
		{
			$this->addError("Geen auteurschap. Concept niet opgeslagen.");
			return false;
		}

		return true;
	}

	private function checkNamePartsMatchRank($baseRank,$uninomial,$specificEpithet,$infraSpecificEpithet)
	{

		if ($baseRank<SPECIES_RANK_ID)
		{
			if (empty($uninomial) && ($baseRank<SPECIES_RANK_ID && $baseRank>=GENUS_RANK_ID))
			{
				$this->addError("Wetenschappelijke naam: genus ontbreekt. Concept niet opgeslagen.");
				return false;
			}
			else
			if (empty($uninomial) && $baseRank<GENUS_RANK_ID)
			{
				$this->addError("Wetenschappelijke naam: uninomial ontbreekt. Concept niet opgeslagen.");
				return false;
			}
			else
			if (!empty($specificEpithet) || !empty($infraSpecificEpithet))
			{
				$this->addError("Wetenschappelijke naam kan maar uit één deel bestaan. Concept niet opgeslagen.");
				return false;
			}
		}
		else
		if ($baseRank==SPECIES_RANK_ID)
		{
			if (empty($uninomial) || empty($specificEpithet))
			{
				$this->addError("Wetenschappelijke naam incompleet. Concept niet opgeslagen.");
				return false;
			}
			else
			if (!empty($infraSpecificEpithet))
			{
				$this->addError("Wetenschappelijke naam kan geen derde naamdeel hebben. Concept niet opgeslagen.");
				return false;
			}
		
		}
		else
		if ($baseRank>SPECIES_RANK_ID)
		{
			if (empty($uninomial) || empty($specificEpithet) || empty($infraSpecificEpithet))
			{
				$this->addError("Wetenschappelijke naam incompleet. Concept niet opgeslagen.");
				return false;
			}
		}

		return true;
			
	}

	private function checkNameUniqueness($name,$childRankId,$parentId)
	{
		$d=$this->models->Taxon->freeQuery("
			select
				_a.*,ifnull(_trash.is_deleted,0) as is_deleted
			from
				%PRE%taxa _a
	
			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'
	
			where 
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.taxon like '". mysql_real_escape_string($name) ."'
				and _a.rank_id = ". mysql_real_escape_string($childRankId) ."
				and _a.parent_id = ". mysql_real_escape_string($parentId)
			);

		if ($d)
		{
			$this->addError(
				"Combinatie naam, rang en ouder bestaat al".
				( $d[0]['is_deleted']==1 ? " (verwijderd taxon)" : "" ).".".
				" Concept niet opgeslagen.".
				" <a href=\"taxon.php?id=".$d[0]['id']."\">Taxon tonen</a>");
			return false;
		}

		return true;		
	}
		


	private function saveConcept()
	{
		$name=$this->rGetVal('concept_taxon');
		$rank=$this->rGetVal('concept_rank_id');
		$parent=$this->rGetVal('parent_taxon_id');
		$uninomial=$this->rGetVal('name_uninomial');
		$authorship=$this->rGetVal('name_authorship');
		$specificEpithet=$this->rGetVal('name_specific_epithet');
		$infraSpecificEpithet=$this->rGetVal('name_infra_specific_epithet');
		$presencePresenceId=$this->rGetVal('presence_presence_id');
		$presenceExpertId=$this->rGetVal('presence_expert_id');
		$presenceOrganisationId=$this->rGetVal('presence_organisation_id');
		$presenceReferenceId=$this->rGetVal('presence_reference_id');

		$name=trim($name['new']);
		$rank=trim($rank['new']);
		$parent=trim($parent['new']);
		$uninomial=trim($uninomial['new']);
		$authorship=trim($authorship['new']);
		$specificEpithet=trim($specificEpithet['new']);
		$infraSpecificEpithet=trim($infraSpecificEpithet['new']);
		$presencePresenceId=trim($presencePresenceId['new']);
		$presenceExpertId=trim($presenceExpertId['new']);
		$presenceOrganisationId=trim($presenceOrganisationId['new']);
		$presenceReferenceId=trim($presenceReferenceId['new']);
		
		foreach((array)$this->_projectRankIds as $val)
		{
			if ($val['id']==$rank)
			{
				$baseRank=$val['rank_id'];
			}
		}

		if (empty($name) || empty($rank) || empty($parent) || empty($uninomial))
		{
			if (empty($name)) $this->addError('Lege conceptnaam. Concept niet opgeslagen.');
			if (empty($rank)) $this->addError('Geen rang. Concept niet opgeslagen.');
			if (empty($parent)) $this->addError('Geen ouder. Concept niet opgeslagen.');
			if (empty($uninomial)) $this->addError('Geen genus of uninomial. Concept niet opgeslagen.');
			$this->setConceptId(null);
			return;
		}

		if (!$this->checkAuthorshipAgainstRank($baseRank,$authorship))
		{
			$this->setConceptId(null);
			return;
		}

		if (!$this->checkParentChildRelationship($baseRank,$parent))
		{
			$this->setConceptId(null);
			return;
		}

		if (!$this->checkNamePartsMatchRank($baseRank,$uninomial,$specificEpithet,$infraSpecificEpithet))
		{
			$this->setConceptId(null);
			return;
		}

		if (!$this->checkNameUniqueness($name,$rank,$parent))
		{
			$this->setConceptId(null);
			return;
		}

		if (
			(!empty($presencePresenceId) || !empty($presenceExpertId) || !empty($presenceOrganisationId) ||  !empty($presenceReferenceId)) &&
			 $baseRank<SPECIES_RANK_ID
			)
		{
			$this->addError('Voorkomensgegevens kunnen niet worden ingevuld voor hogere taxa. Concept niet opgeslagen.');
			$this->setConceptId(null);
			return;
		} else
		if (
			(empty($presencePresenceId) || empty($presenceExpertId) || empty($presenceOrganisationId) || empty($presenceReferenceId)) && 

			$rank>=SPECIES_RANK_ID
		)
		{
			$this->addWarning('Incomplete voorkomensgegevens. Concept wel opgeslagen.');
		}

		if ($baseRank==GENUS_RANK_ID && empty($authorship))
		{
			$this->addWarning("Geen auteurschap. Concept wel opgeslagen.");
		}
			
		
		// we passed the tests!
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
			$this->logNsrChange(array('after'=>array('id'=>$this->getConceptId(),'taxon'=>$name,'rank_id' =>$rank),'note'=>'new concept '.$name));
			//$this->setIsNewRecord(true);
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
				$this->addMessage('Koppeling ouder opgeslagen.');
			}
			else
			{
				$this->addError('Koppeling ouder niet opgeslagen.');
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

		if ($result && $this->models->Taxon->getAffectedRows()!=0)
		{
			$after=$this->models->Taxon->_get(array(
				'id'=>array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId()),
				'columns'=>'taxon'
			));
			$this->logNsrChange(array('before'=>$before[0],'after'=>$after[0],'note'=>'updated concept name '.$before[0]['taxon']));
			return true;
		}
		else
		{
			$exist=$this->getTaxonByName(trim($values['new']));
			if ($exist)
			{
				$this->addError(sprintf('Naam bestaat al voor een <a target="_new" href="taxon.php?id=%s">ander taxonconcept</a>.',$exist['id']));
			}
		
			$this->addError('Update naam taxon concept mislukt.');
			return false;
		}
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
			array('presence_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateConceptHabitatId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('habitat_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceExpertId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('actor_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceOrganisationId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('actor_org_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updatePresenceReferenceId($values)
	{
		return $this->models->PresenceTaxa->update(
			array('reference_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function createConceptNsrIds()
	{
		$this->createNsrIds(array('id'=>$this->getConceptId(),'type'=>'taxon'));
	}

	private function createNameNsrIds()
	{
		$this->createNsrIds(array('id'=>$this->getNameId(),'type'=>'name'));
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
			$this->setIsNewRecord(true);
			$this->updateName();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe naam mislukt.');
		}
	}

	private function checkMainLanguageCommonName()
	{
		$name=$this->rGetVal('main_language_name');

		if (!isset($name['new'])) return;

		$d=$this->models->Names->freeQuery("
			select
				_a.id, 
				_b.taxon
			from 
				%PRE%names _a
			left join %PRE%taxa _b
				on _a.project_id = _b.project_id
				and _a.taxon_id=_b.id
			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and lower(_a.name) = '" . mysql_real_escape_string(trim($name['new'])) . "'
		");

		if ($d)
		{
			$this->setMessage(sprintf('Nederlandse "%s" bestaat al (naam wel opgeslagen):'),$name['new']);
			foreach((array)$d as $val)
			{
				$this->setMessage('<a href="name.php?id='.$val['id'].'">Naam van: '.$val['taxon'].'</a>');
			}
		}

	}

	private function saveMainLanguageCommonName()
	{
		$name=$this->rGetVal('main_language_name');

		if (!isset($name['new'])) return;
		
		if ($this->rHasVal('main_language_name_language_id'))
		{
			$d=$this->rGetVal('main_language_name_language_id');
			$language_id=$d['new'];
		}
		else
		{
			$language_id=LANGUAGECODE_DUTCH;
		}

		$d=$this->models->Names->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $this->getConceptId(),
				'language_id' => $language_id,
				'type_id' => $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
				'name' => trim($name['new'])
			));

		if ($d)
		{
			$this->setNameId($this->models->Names->getNewId());
			$this->addMessage('Nederlandse naam aangemaakt.');

			$newname=$this->getName(array('id'=>$this->getNameId()));
			$this->logNsrChange(array('after'=>$newname,'note'=>'new main language name '.$newname['name']));

			if ($this->rHasVar('main_language_name_reference_id'))
			{
				if (!$this->updateNameReferenceId($this->rGetVal('main_language_name_reference_id')))
				{
					$this->addError('Nederlandse naam: referentie niet opgeslagen.');
				}
			}
	
			if ($this->rHasVar('main_language_name_expert_id'))
			{
				if (!$this->updateNameExpertId($this->rGetVal('main_language_name_expert_id')))
				{
					$this->addError('Nederlandse naam: expert niet opgeslagen.');
				}
			}
	
			if ($this->rHasVar('main_language_name_organisation_id'))
			{
				if (!$this->updateNameOrganisationId($this->rGetVal('main_language_name_organisation_id')))
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



	private function updateName($new=false)
	{
		$this->createNameNsrIds();

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

		if ($this->rHasVar('aanvulling'))
		{
			if ($this->saveNameAanvulling($this->rGetVal('aanvulling')))
			{
				$this->addMessage('Opmerking opgeslagen.');
			}
			else
			{
				$this->addError('Opmerking niet opgeslagen.');
			}
		}

		$after=$this->getName(array('id'=>$this->getNameId()));
		$this->logNsrChange(
			array(
				'before'=>(!$this->getIsNewRecord() ? $name : null),
				'after'=>$after,
				'note'=>(!$this->getIsNewRecord() ? 'updated name '.$name['name'] : 'new name '.$after['name'])
			)
		);
		$this->setIsNewRecord(false);
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
			$this->models->NsrIds->delete(array('project_id'=>$this->getCurrentProjectId(),'lng_id'=>$this->getNameId(),'item_type'=>'name'));
			$this->logNsrChange(array('before'=>$before,'note'=>'deleted name '.$before[0]['name']));
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
			array('reference_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameExpertId($values)
	{
		return $this->models->Names->update(
			array('expert_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateNameOrganisationId($values)
	{
		return $this->models->Names->update(
			array('organisation_id'=>empty($values['new']) || $values['new']=='-1' ? 'null' : trim($values['new'])),
			array('id'=>$this->getNameId(),'taxon_id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function saveNameAanvulling($values)
	{
		$current=$this->models->NamesAdditions->_get(
			array(
				'id'=>array('project_id'=>$this->getCurrentProjectId(),'name_id'=>$this->getNameId()),
				'fieldAsIndex'=>'language_id'
			)
		);
		
		$results=array();
		foreach((array)$values as $language_id=>$vals)
		{
			if ( isset($vals['new']) )
				$new=trim($vals['new']);
			else
				$new=null;
			
			if ( !isset($current[$language_id]) )
			{
				//insert
				$results[]=$this->models->NamesAdditions->save(
					array(
						'project_id'=>$this->getCurrentProjectId(),
						'name_id'=>$this->getNameId(),
						'language_id'=>$language_id,
						'addition'=>$new
					)
				);
			}
			else
			if ( isset($current[$language_id]) && $current[$language_id]!=$new && !empty($new) )
			{
				//update
				$results[]=$this->models->NamesAdditions->update(
					array( 'addition'=>$new ),
					array( 'project_id'=>$this->getCurrentProjectId(), 'id'=>$current[$language_id]['id'] )
				);
			}
			else
			if ( isset($current[$language_id]) && empty($new) )
			{
				//delete
				$results[]=$this->models->NamesAdditions->delete(
					array( 'project_id'=>$this->getCurrentProjectId(), 'id'=>$current[$language_id]['id'] )
				);
			}
			
		}
		
		return ( !in_array(false,$results) );
		
	}

	private function doNameIntegrityChecks($name)
	{
		if (!$this->checkNamePartsMatchName($name))
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
		if (!$this->checkIfConceptRetainsScientificName($concept['id']))
		{
			$this->addWarning("Aan dit concept is geen wetenschappelijke naam gekoppeld.");
		}
		
		if (!$this->checkIfConceptRetainsNameInMainProjectLanguage($concept['id']) && $concept['base_rank']>=SPECIES_RANK_ID)
		{
			$this->addWarning("Aan dit concept is geen Nederlandse voorkeursnaam gekoppeld.");
		}
	}

	private function checkNamePartsMatchName($name)
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

	private function checkIfConceptRetainsNameInMainProjectLanguage($concept)
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
	
	private function toggleConceptDeleted($delete)
	{
		$concept=$this->getConcept($this->rGetId());
		$before=$after=array('id'=>$concept['id'],'taxon'=>$concept['taxon'],'is_deleted'=>$concept['is_deleted']);

		if ($delete)
		{
			$d=$this->models->TrashCan->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$this->rGetId(),
				'item_type'=>'taxon'
			)));
			
			$this->models->TrashCan->save(
			array(
				'id' => isset($d[0]['id']) ? $d[0]['id'] : null,
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$this->rGetId(),
				'item_type'=>'taxon',
				'user_id'=>$this->getCurrentUserId(),
				'is_deleted'=>1
			));		

			$after['is_deleted']=1;

			$this->logNsrChange(
				array(
					'before'=>$before,
					'after'=>$after,
					'note'=>'marked '.$before['taxon'].' as deleted'
				)
			);

		}
		else 
		{
			$d=$this->models->TrashCan->delete(
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'lng_id'=>$this->rGetId(),
				'item_type'=>'taxon'
			));

			$after['is_deleted']=0;

			$this->logNsrChange(
				array(
					'before'=>$before,
					'after'=>$after,
					'note'=>'removed deletion mark from '.$before['taxon']
				)
			);

		}
	}

	private function getReference($id=null)
	{
		if (empty($id))
			return;

		$l=$this->models->Literature2->freeQuery(
			"select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.id = ".$id
		);

		return $l[0];
	}
	
	private function getTaxonBranch($parent)
	{
		return $this->models->Taxon->freeQuery("
			select
				_b.*
			from 
				%PRE%taxon_quick_parentage _a

			left join %PRE%names _b
				on _a.project_id = _b.project_id
				and _a.taxon_id = _b.taxon_id
				and _b.type_id =".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."

			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and MATCH(_a.parentage) AGAINST ('".$parent['id']."' in boolean mode)
		");
	}

	private function checkIfNameExistsInConceptsKingdom($intendedNewConceptName,$concept)
	{
		/*
			deze functie zoekt naar bestaande concepten die een naam hebben gelijk aan de
			beoogde nieuwe naam van het concept. omdat namen uniek geacht worden te zijn
			binnen een kingdom (animalia, plantae, funghi), wordt ook bekeken in welk
			kingdom de eventueel gevonden dubbele namen vallen. hier treedt wel een kip/ei
			kwestie op: deze test maakt onderdeel uit van een test die uitmaakt of een
			taxon van parent kan veranderen. maar verandering van parent kan in principe
			het kingdom waar het taxon toe behoort wijzigen, waardoor de uitkomst van deze
			test anders zou kunnen zijn. gemakshalve wordt er van uit gegaan dat een 
			bestaand taxon nooit van kingdom verandert, zodat alleen hoeft te worden 
			getest de overlappende namen wel of niet in hetzelfde kingdom vallen waar het
			ongewijzigde concept valt.
		*/
		
		$d=$this->models->Taxon->freeQuery("		
			select
				*
			from
				%PRE%names
			where 
				project_id = ".$this->getCurrentProjectId()."
				and type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
				and language_id=".LANGUAGE_ID_SCIENTIFIC."
				and (
					trim(replace(name,ifnull(authorship,''),'')) = '". mysql_real_escape_string($intendedNewConceptName) ."'
						or
					concat(
						if(uninomial is null,'',concat(uninomial,' ')),
						if(specific_epithet is null,'',concat(specific_epithet,' ')),
						if(infra_specific_epithet is null,'',infra_specific_epithet)
					) = '". mysql_real_escape_string($intendedNewConceptName) ."'
				)
				and taxon_id != ".mysql_real_escape_string($concept['id'])."
		");

		if ($d)
		{
			// checking whether everything is in the same kingdom
			$a[]=$concept['id'];
			foreach((array)$d as $key=>$val)
				$a[]=$val['taxon_id'];

			$parentage=$this->models->Taxon->freeQuery(array(
				"query" => "
					select
						taxon_id,parentage
					from 
						%PRE%taxon_quick_parentage
					where 
						project_id = ".$this->getCurrentProjectId()."
						and taxon_id in (".implode(",",$a).")",
				"fieldAsIndex"=>"taxon_id"
				)
			);

			/*
			example:
			+----------+--------------------------------------------------+
			| taxon_id | parentage                                        |
			+----------+--------------------------------------------------+
			|   138998 | 116297 116298 138384 138887 138978 138985 138997 |
			|   138999 | 116297 116298 138384 138887 138978               |
			+----------+--------------------------------------------------+				
			first two parts are realm (life) and kingdom (plantae, animalia, funghi)
			*/		

			if (isset($parentage[$concept['id']]))
			{
				$d1=explode(' ',$parentage[$concept['id']]['parentage']);
			
				foreach((array)$parentage as $key=> $val)
				{
					$d2=explode(' ',$val['parentage']);
					if (($d1[0]==$d2[0])||($d1[1]==$d2[1]))
					{
						return $key;
					}
				}
			}
		}
		
		return false;
	}

	private function getParentChangeStyle()
	{
		/*
			two possible entry points:
			- edit concept	-> $this->getConceptId()
			- edit name		-> $this->getNameId()
		*/
		
		$data=$this->requestData;

		if ( $this->getConceptId() )
		{

			$taxon=$this->getConcept($this->rGetId());

			// A. WANNEER VAN EEN BESTAANDE (ONDER)SOORT DE PARENT WORDT GEWIJZIGD.
			if (
				$taxon['base_rank']>=SPECIES_RANK_ID &&
				isset($data['parent_taxon_id']['new']) && 
				$data['parent_taxon_id']['new']!=$taxon['parent_id']
			)
			{
				return 'A';
			}

		}
		else
		if ($this->getNameId())
		{

			$name=$this->getName(array('id'=>$this->getNameId()));
			if ($name['nametype']!=PREDICATE_VALID_NAME)
				return;


			if (
				!isset($data['name_uninomial']['new']) && 
				!isset($data['name_specific_epithet']['new']) && 
				!isset($data['name_infra_specific_epithet']['new'])
			)
			{
				// nothing relevant changed
				return;
			}
			
			$name=$this->getName(array('id'=>$this->getNameId()));
			$concept=$this->getConcept($name['taxon_id']);
			
			if ($concept['base_rank']>SPECIES_RANK_ID &&
				!isset($data['name_uninomial']['new']) && 
				!isset($data['name_specific_epithet']['new'])
			)
			{
				// nothing relevant changed
				return;
			}
			
			// B. WANNEER VAN EEN BESTAANDE (ONDER)SOORT DE GEACCEPETEERDE NAAM WORDT GEWIJZIGD
			if ($concept['base_rank']>=SPECIES_RANK_ID) return 'B';
			// C. WANNEER VAN EEN BESTAAND GENUS DE GEACCEPETEERDE NAAM WORDT GEWIJZIGD
			if ($concept['base_rank']==GENUS_RANK_ID) return 'C';

		}

		return;
	}

	private function needParentChange()
	{
		return $this->getParentChangeStyle()!=null;
	}
	
	private function canParentChange()
	{
		// preliminairies
		$style=$this->getParentChangeStyle();
		
		if (is_null($style)) return true;
		
		$canChange=true;

		$data=$this->requestData;

		if ( $this->getConceptId() )
		{
			$concept=$this->getConcept( $this->getConceptId() );
			$name=$this->getName(
				array(
					'taxon_id'=>$this->getConceptId(),
					'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
				)
			);
			$parent=$this->getConcept($concept['parent_id']);
		}
		else
		if ($this->getNameId())
		{
			$name=$this->getName(array('id'=>$this->getNameId()));
			$concept=$this->getConcept($name['taxon_id']);
			$parent=$this->getConcept($concept['parent_id']);
		}


		/*
		A. WANNEER VAN EEN BESTAANDE (ONDER)SOORT DE PARENT WORDT GEWIJZIGD.		
		1. stel de beoogde nieuwe naam (BNN; sorry) samen op basis van 
		NIEUWE (maar wel al bestaande) ouder (uninominaal) & BESTAANDE epithet (& infra sp. eph.)
		*/
		if ($style=='A')
		{

			$parent=$this->getConcept($data['parent_taxon_id']['new']);
			$parentName=$this->getName(
				array(
					'taxon_id'=>$data['parent_taxon_id']['new'],
					'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
				)
			);

			// checking if name parts are complete
			if (empty($name['uninomial']))
			{
				$this->addError('Geldige naam huidige taxon heeft geen losse uninominaal.');
				$canChange=false;
			}
			if ($concept['base_rank']>=SPECIES_RANK_ID && empty($name['specific_epithet']))
			{
				$this->addError('Geldige naam huidige taxon heeft geen los specifiek epithet.');
				$canChange=false;
			}
			if (empty($parentName['uninomial']))
			{
				$this->addError('Geldige naam beoogde ouder heeft geen losse uninominaal.');
				$canChange=false;
			}
			if ($parent['base_rank']>=SPECIES_RANK_ID && empty($parentName['specific_epithet']))
			{
				$this->addError('Geldige naam beoogde ouder heeft geen los specifiek epithet.');
				$canChange=false;
			}
			
			if (!$canChange) return false;

			// creating the intended new name based on the intended new parents name
			if ($parent['base_rank']>=SPECIES_RANK_ID)
			{
				$intendedNewConceptName=
					trim($parentName['uninomial']).' '.
					trim($name['specific_epithet']).' '.
					trim($name['infra_specific_epithet']);
			}
			else
			{
				$intendedNewConceptName=
					trim($parentName['uninomial']).' '.
					trim($name['specific_epithet']);
			}

		}


		/*
		B. WANNEER VAN EEN BESTAANDE (ONDER)SOORT DE GEACCEPETEERDE NAAM WORDT GEWIJZIGD	
		*/
		if ($style=='B')
		{
			$spcEpithet=
				(isset($data['name_specific_epithet']['new']) ? 
					trim($data['name_specific_epithet']['new']) : 
					$name['specific_epithet']);

			$infraSpEp=
				(isset($data['name_infra_specific_epithet']['new']) ? 
					trim($data['name_infra_specific_epithet']['new']) :
					$name['infra_specific_epithet']);


			// creating the intended new name
			$intendedNewConceptName=
				(isset($data['name_uninomial']['new']) ? 
					trim($data['name_uninomial']['new']) : 
					$name['uninomial']).
				(!empty($spcEpithet) ? ' '.$spcEpithet : '').
				(!empty($infraSpEp) ? ' '.$infraSpEp : '');
		
		}


		/*
		2. bestaat BNN al in de database? test op volledige naam zonder authorship en inclusief de grandparent (kingdom)
		(we zouden het liefst zoeken op uninomial=uninomial, specific_epithet=specific_epithet etc, maar helaas zijn in
		de productiedatabase de losse naamdelen niet altijd volledig ingevuld. derhalve zoeken we ook op 
		beoogde_naam=volledge_geldige_naam.replace(auhorship,'').
		als ook de auhorship niet bestaat, dan... ??? (we miss out)
		*/
		if ($style=='A'||$style=='B')
		{

			$d=$this->checkIfNameExistsInConceptsKingdom($intendedNewConceptName,$concept);

			if ($d!==false)
			{
				$this->addError(
					'Er bestaat al een taxon "<a href="taxon.php?id='.$d.'">'.$intendedNewConceptName.'</a>" in hetzelfde koninkrijk.'
				);
				return false;
			}

		}


		/*
		C. WANNEER VAN EEN BESTAAND GENUS DE GEACCEPETEERDE NAAM WORDT GEWIJZIGD
		1. controleer of er niet al een genus bestaat met de nieuwe naam binnen dezelfde parent.
		*/
		if ($style=='C')
		{

			$intendedNewConceptName=$data['name_uninomial']['new'];

			$d=$this->models->Taxon->freeQuery("		
				select
					_a.*
				from
					%PRE%names _a
				
				left join %PRE%taxa _b
					on _a.project_id = _b.project_id
					and _a.taxon_id = _b.id
				
				where 
					_a.project_id = ".$this->getCurrentProjectId()."
					and _a.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _a.language_id=".LANGUAGE_ID_SCIENTIFIC."
					and (
						trim(replace(name,ifnull(_a.authorship,''),'')) = '". mysql_real_escape_string($intendedNewConceptName) ."'
							or
						concat(
							if(_a.uninomial is null,'',concat(_a.uninomial,' ')),
							if(_a.specific_epithet is null,'',concat(_a.specific_epithet,' ')),
							if(_a.infra_specific_epithet is null,'',_a.infra_specific_epithet)
						) = '". mysql_real_escape_string($intendedNewConceptName) ."'
					)
					and _b.parent_id = ".mysql_real_escape_string($concept['parent_id'])."
					and _b.id != ".mysql_real_escape_string($concept['id'])."
			");

			if ($d)
			{
				$this->addError('Er bestaat al een taxon "<a href="taxon.php?id='.$d[0]['taxon_id'].'">'.$intendedNewConceptName.'</a>" onder dezelfde ouder.');
				return false;
			}

		}


		/*
		3. haal de hele taxonomische tak op onder het te wijzigen taxon.
		*/
		$children=$this->getTaxonBranch($concept);

		/*
		4. doorloop alle taxa uit die tak en doe eenzelfde test: maak een beoogde nieuwe naam 
		op basis van de nieuwe uninominaal en de bestaande epithet (& infra sp. eph.), maar geen auteur, en kijk of ze al bestaan.
		BNN + grandparent (realm)
		*/
		foreach((array)$children as $key=>$val)
		{
			$unin=(isset($data['name_uninomial']['new']) ? trim($data['name_uninomial']['new']) : trim($val['uninomial']));
			$spEp=(isset($data['name_specific_epithet']['new']) ? trim($data['name_specific_epithet']['new']) : trim($val['specific_epithet']));
			
			$iName=
				(!empty($unin) ? $unin : '').
				(!empty($spEp) ? ' '.$spEp : '').
				(isset($val['infra_specific_epithet']) ? ' '.trim($val['infra_specific_epithet']) : null);			

			$d=$this->checkIfNameExistsInConceptsKingdom($iName,$val);

			if ($d!==false)
			{
				$this->addError(
					'Er bestaat al een taxon "<a href="taxon.php?id='.$d.'">'.$iName.'</a>" in hetzelfde koninkrijk.'
				);
				return false;
			}
		}

		return true;
	}
	
	private function doParentChange()
	{
		// preliminairies
		$data=$this->requestData;

		if ( $this->getConceptId() )
		{
			$concept=$this->getConcept( $this->getConceptId() );
			$name=$this->getName(
				array(
					'taxon_id'=>$this->getConceptId(),
					'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
				)
			);
		}
		else
		if ($this->getNameId())
		{
			$name=$this->getName(array('id'=>$this->getNameId()));
			$concept=$this->getConcept($name['taxon_id']);
		}


		if (isset($data['parent_taxon_id']['new']))
		{
			$newParentName=$this->getName(
				array(
					'taxon_id'=>$data['parent_taxon_id']['new'],
					'type_id'=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']
				)
			);
		}

		
		/*
		3. taxon + hele taxonomische tak onder het te wijzigen taxon.
		*/
		$taxa=$this->getTaxonBranch($concept);

		if ($taxa)
		{
			array_unshift($taxa,$name);
		}
		else
		{
			$taxa=array($name);
		}


		/*
		5. doe voor taxon + alle taxa uit die tak het volgende:
		a) verander hun geaccepteerde naam van type naar synoniem.
			i. maak het beoogde nieuwe synoniem aan (BNS)
			ii. kijk of het BNS al bestaat (deze keer inclusief authorship).
			zo ja, meld het en negeer dat BNS verder (maar ga door met de transactie)
			zo nee => iii
			iii. verander hun geaccepteerde naam van type naar synoniem.
		
		b) maak een nieuwe geaccepteerde naam aan op basis van BNN + authorship van de oude geaccepeerde naam; 
			als authorship nog geen haakjes had, krijgt hij die nu.

		c) update de naam van het concept op basis van de nieuwe geaccepeerde naam.
		*/
		
		foreach((array)$taxa as $key=>$val)
		{
			
			$newSynonym=$val['name'];

			$d=$this->models->Names->freeQuery("
				select
					*
				from
					%PRE%names
				where 
					project_id = ".$this->getCurrentProjectId()."
					and taxon_id = ".$val['taxon_id']."
					and name = '". mysql_real_escape_string($newSynonym) ."'
					and type_id = ".$this->_nameTypeIds[PREDICATE_SYNONYM]['id']
			);

			if ($d)
			{
				$this->addWarning("Synoniem \"".$newSynonym."\" bestaat al; duplicaat synoniem aangemaakt.");
			}

			$this->models->Names->freeQuery("
				update
					%PRE%names
				set
					type_id = ".$this->_nameTypeIds[PREDICATE_SYNONYM]['id']."
				where 
					project_id = ".$this->getCurrentProjectId()."
					and id = ".$val['id']."
				limit 1
			");	

			$after=$this->models->Names->_get(array('id'=> array('id'=>$val['id'])));
			$this->logNsrChange(array('before'=>$name,'after'=>$after[0],'note'=>'changed valid name '.$newSynonym.' to synonym'));
			$this->addMessage('Geaccepteerde naam omgezet naar synoniem.');
			
			
			if (isset($data['name_uninomial']['new']))
			{
				$uninomial=trim($data['name_uninomial']['new']);
			}
			else
			if (isset($newParentName['uninomial']))
			{
				$uninomial=$newParentName['uninomial'];
			}
			else
			{
				$uninomial=$val['uninomial'];
			}
			
			if (isset($data['name_specific_epithet']['new']))
			{
				$specificEpithet=trim($data['name_specific_epithet']['new']);
			}
			else
			if (isset($newParentName['specific_epithet']))
			{
				$specificEpithet=$newParentName['specific_epithet'];
			}
			else
			{
				$specificEpithet=$val['specific_epithet'];
			}

			if (isset($data['name_infra_specific_epithet']['new']))
			{
				$infraSpecificEpithet=trim($data['name_infra_specific_epithett']['new']);
			}
			else
			{
				$infraSpecificEpithet=$val['infra_specific_epithet'];
			}

			$authorship=
				(!empty($val['name_author']) ? $val['name_author'] : null).
				(!empty($val['name_author']) && !empty($val['authorship_year']) ? ', ' : '').
				(!empty($val['authorship_year']) ? $val['authorship_year'] : null);

			$authorship=
				trim(!empty($authorship) ? '('.$authorship.')' : '');
				
			$newName=
				trim(
					$uninomial.
					(!empty($specificEpithet) ? ' '.$specificEpithet : null).
					(!empty($infraSpecificEpithet) ? ' '.$infraSpecificEpithet : null).
					(!empty($authorship) ? ' '.$authorship : null)
				);

			$this->models->Names->save(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $val['taxon_id'],
					'language_id' => LANGUAGE_ID_SCIENTIFIC,
					'type_id' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
					'name' => $newName,
					'uninomial' => $uninomial,
					'specific_epithet' => (!empty($specificEpithet) ? $specificEpithet : null),
					'infra_specific_epithet' => (!empty($infraSpecificEpithet) ? $infraSpecificEpithet : null),
					'authorship' => $authorship,
					'name_author' => $val['name_author'],
					'authorship_year' => $val['authorship_year'],
					'reference' => $val['reference'],
					'reference_id' => $val['reference_id'],
					'expert' => $val['expert'],
					'expert_id' => $val['expert_id'],
					'organisation' => $val['organisation'],
					'organisation_id' => $val['organisation_id'],
				));

			$id=$this->models->Names->getNewId();
			$after=$this->models->Names->_get(array('id'=> array('id'=>$id)));

			$this->logNsrChange(array('after'=>$after[0],'note'=>'created new valid name '.$newName));
			$this->addMessage('Nieuwe geaccepteerde naam aangemaakt.');

			$this->setConceptId($val['taxon_id']);
			$this->updateConceptTaxon(array('new'=>$newName));
			$this->addMessage('Naam concept gewijzigd.');

			if (isset($data['parent_taxon_id']['new']))
			{
				/*
					de ouder van deze "ondertaxa" is van parent gewijzigd, parentage
					opnieuw vaststellen (en vooral *niet* de parent van deze ondertaxa
					ook wijzigen)
					
				*/
				$this->saveTaxonParentage($val['taxon_id']);
			}

		}

		$this->setConceptId($name['taxon_id']);
		$this->resetTree();

	}

	private function getTraitgroups($p=null)
	{
		$parent=isset($p['parent']) ? $p['parent'] : null;
		$level=isset($p['level']) ? $p['level'] : 0;
		$stopLevel=isset($p['stop_level']) ? $p['stop_level'] : null;
		
		$g=$this->models->TraitsGroups->freeQuery("
			select
				_a.*,
				_b.translation as name,
				_c.translation as description,
				count(_tt.id) as trait_count,
				count(_ttf.id) as taxon_freevalue_count,
				count(_ttv.id) as taxon_value_count,
				count(_ttf.id)+count(_ttv.id) as taxon_count

			from
				%PRE%traits_groups _a
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $this->getDefaultProjectLanguage() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=". $this->getDefaultProjectLanguage() ."
				
			left join 
				%PRE%traits_traits _tt
				on _a.project_id=_tt.project_id
				and _a.id=_tt.trait_group_id

			left join 
				%PRE%traits_taxon_freevalues _ttf
				on _tt.project_id=_ttf.project_id
				and _tt.id=_ttf.trait_id
				and _ttf.taxon_id =". $this->getConceptId() ."

			left join 
				%PRE%traits_values _tv
				on _tt.project_id=_tv.project_id
				and _tt.id=_tv.trait_id

			left join 
				%PRE%traits_taxon_values _ttv
				on _tv.project_id=_ttv.project_id
				and _tv.id=_ttv.value_id
				and _ttv.taxon_id =". $this->getConceptId() ."

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.parent_id ".(is_null($parent) ? "is null" : "=".$parent)."
			group by _a.id
			order by _a.show_order, _a.sysname
		");
		
		foreach((array)$g as $key=>$val)
		{
			$g[$key]['level']=$level;	
			//$g[$key]['taxa']=$this->getTaxongroupTaxa($val['id']);
			if (!is_null($stopLevel) && $stopLevel<=$level)
			{
				continue;
			}
			$g[$key]['children']=$this->getTraitgroups(array('parent'=>$val['id'],'level'=>$level+1,'stop_level'=>$stopLevel));
		}
		
		return $g;
	}


}

