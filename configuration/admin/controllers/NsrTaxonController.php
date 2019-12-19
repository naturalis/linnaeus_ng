<?php

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

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
    public $cssToLoad = array(
        'lookup.css',
        '../vendor/prettyPhoto/css/prettyPhoto.css',
		'nsr_taxon_beheer.css'
    );
    public $jsToLoad = array(
        'all' => array(
            'lookup.js',
            '../vendor/prettyPhoto/js/jquery.prettyPhoto.js',
			'nsr_taxon_beheer.js'
        )
    );
    public $modelNameOverride='NsrTaxonModel';
    public $controllerPublicName = 'Taxon editor';
    public $includeLocalMenu = false;

	private $_nameTypeIds;
	private $_projectRankIds;

	private $nameId=null;
	private $firstTaxon=false;

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

		$this->_projectRankIds=$this->models->ProjectsRanks->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id',
			'fieldAsIndex'=>'rank_id'
		));
		
		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->_taxon_main_image_base_url=$this->moduleSettings->getGeneralSetting( 'taxon_main_image_base_url' );
		$this->smarty->assign( 'taxon_main_image_base_url',$this->_taxon_main_image_base_url );

		$this->_suppress_parent_child_relation_checks=$this->moduleSettings->getModuleSetting( [ 'module'=>'species','setting'=>'suppress_parent_child_relation_checks', 'subst'=>0 ] )==1;
	}

    public function taxonNewAction()
    {

		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
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
				$data=$this->rGetAll();
				array_walk($data, function(&$val, $key){if (isset($val['new'])) $val=$val['new'];});
				unset($data['action']);
				$texts=null;

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
		if (!$this->rHasId()) $this->redirect('taxon_new.php');

		$this->UserRights->setItemId( $this->rGetId() );
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Edit taxon concept'));

		if ($this->rHasId() && $this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			$this->setConceptId( $this->rGetId() );
			$this->toggleConceptDeleted(true);
			$this->setMessage($this->translate('Concept marked as deleted.'));
			$this->resetTree();
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','undelete') && !$this->isFormResubmit())
		{
			$this->setConceptId( $this->rGetId() );
			$this->toggleConceptDeleted(false);
			$this->setMessage($this->translate('Concept no longer marked as deleted.'));
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
		//$this->checkAuthorisation();
        $this->setPageName($this->translate('Edit scientific name'));
		$this->_nameAndSynonym();
		$this->printPage();
    }

    public function nameAction()
    {
		//$this->checkAuthorisation();
        $this->setPageName($this->translate('Edit common name'));
		$this->_nameAndSynonym();
		$this->printPage();
	}

    private function _nameAndSynonym()
    {

		if ( $this->rHasId() )
		{
			$this->setNameId($this->rGetId());
			$name=$this->getName(array('id'=>$this->getNameId()));
			$this->UserRights->setItemId( $name['taxon_id'] );
		}

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$this->setNameId($this->rGetId());
			$name=$this->getName(array('id'=>$this->getNameId()));
			$this->deleteName();
			$this->setMessage($this->translate('Name deleted.'));
			$this->redirect('taxon.php?id='.$name['taxon_id']);
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->setNameId($this->rGetId());
		

			// check whether a concept already exist with the new name *before* updating the valid name (and subsequently the concept)
			$name=$this->getName(array('id'=>$this->getNameId()));
			$exist=$this->getTaxonByName($this->rGetVal('name_name')['new']);
			
			if (
				!empty($name['type_id']) && 
				$name['type_id']==$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] && 
				$this->rHasVar('name_name') &&
				!empty($exist)
			)
			{
				$this->addError(sprintf($this->translate('Name already exists for') . ' <a target="_new" href="taxon.php?id=%s">' . $this->translate('another taxon concept') . '</a>.',$exist['id']));
			}
			else
			{
				$this->updateName();
	
				if ($this->needParentChange()!=false && $this->canParentChange()!=false)
				{
					$this->doParentChange();
					$name=$this->getName(array('id'=>$this->getNameId()));
					$this->saveTaxonParentage($name['taxon_id']);
				}
				else
				{
					$this->updateConceptBySciName();
					$this->doNameIntegrityChecks($this->getName(array('id'=>$this->getNameId())));
				}
			}
		}
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

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
						$this->addWarning($this->translate("
						    Note: the scientific name does not match the composite of individual name elements. Please enter the correct uninomial and (if applicable) authorship to complete the name card.
						"));
					}
					else
					{
						$this->addWarning($this->translate("
						    Note: the scientific name does not match the composite of individual name elements. Please enter the correct genus, species and (if applicable) subspecific epithet and authorship to complete the name card.
						"));
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
			$this->addError($this->translate('No ID.'));
		}

		if (isset($concept))
		{
			$this->smarty->assign('concept',$concept);
			$this->smarty->assign('ranks',$this->newGetProjectRanks());
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
		if ( !$this->rHasId() ) $this->redirect('taxon_new.php');

		$this->UserRights->setItemId( $this->rGetId() );
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

        $this->setPageName( $this->translate('Rename taxon concept directly') );
		$this->setConceptId( $this->rGetId() );

		if ( $this->rHasVal('taxon') && $this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			if ($this->updateConceptTaxon(array('new'=>$this->rGetVal('taxon'))))
			{
				$this->addMessage($this->translate('Name saved'));
				$this->resetTree();
			}
			else
			{
				$this->addWarning($this->translate('Name not saved'));
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
		if ( !$this->rHasId() ) $this->redirect('synonym.php');

        $this->setPageName( $this->translate('Edit common name directly') );

		$this->setNameId( $this->rGetId() );
		$name=$this->getName(array('id'=>$this->getNameId()));

		$this->setConceptId( $name['taxon_id'] );
		$concept=$this->getConcept( $this->getConceptId() );

		$this->UserRights->setItemId( $this->getConceptId() );
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		if ($name['type_id']!=$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'])
		{
			$this->redirect('synonym.php');
		}

		if ( $this->rHasVal('action','save') && !$this->isFormResubmit())
		{

			$this->updateName();
			$this->addMessage($this->translate('Name saved'));
			$this->resetTree();

			/*
			see: https://jira.naturalis.nl/browse/LINNA-588
			
			// check whether a concept already exist with the new name *before* updating the valid name (and subsequently the concept)
			$exist=$this->getTaxonByName($this->rGetVal('name_name')['new']);
			if ( !empty($exist) )
			{
				$this->addError(sprintf($this->translate('Name already exists for') . ' <a target="_new" href="taxon.php?id=%s">' . $this->translate('another taxon concept') . '</a>.',$exist['id']));
			}
			else
			{
				$this->updateName();
				$this->addMessage($this->translate('Name saved'));
				$this->resetTree();
			}
			*/
		}

		$this->smarty->assign('concept',$concept);
		$this->smarty->assign('name',$this->getName(array('id'=>$this->getNameId())));
		$this->printPage();
	}

    public function taxonDeletedAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Taxa marked as deleted'));
		$this->smarty->assign('concepts',$this->getDeletedSpeciesList());
		$this->printPage();
	}

    public function updateParentageAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Update index table'));

		if ($this->rHasVal('action','update') && !$this->isFormResubmit())
		{
			$this->saveTaxonParentage();
			$this->addMessage($this->translate('Table updated'));
			$this->logChange( ['note'=>'Manually updated index table'] );
		}

		$this->printPage();
	}

    public function nsrIdResolverAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
		$this->checkAuthorisation();

        $this->setPageName($this->translate('External ID resolver'));

		if ( ($this->rHasVal('action','resolve') || $this->rHasVal('action','download')) && $this->rHasVal('codes') )
		{
			$t="_tmp_" . substr( "abcdefghijklmnopqrstuvwxyz", mt_rand( 0, 25 ), 1 ) .substr( md5( time() ), 1 ) . "_p". $this->getCurrentProjectId();

			$this->models->NsrTaxonModel->dropTempTable(array(
				'table_name'=>$t
			));

			$this->models->NsrTaxonModel->createTempTable(array(
				'table_name'=>$t
			));

			$codes=explode(PHP_EOL,trim($this->rGetVal('codes')));
			array_walk($codes,function(&$val,$key){ $val=substr(str_pad(trim($val),12,"0", STR_PAD_LEFT),-12);});

			$this->models->NsrTaxonModel->fillTempTable(array(
				'table_name'=>$t,
				'id_prefix'=>'tn.nlsr.concept/',
				'codes'=>$codes
			));

			$result=$this->models->NsrTaxonModel->getResolvedCodes(array(
				'table_name'=>$t,
				'project_id'=>$this->getCurrentProjectId()
			));

			$this->models->NsrTaxonModel->dropTempTable(array(
				'table_name'=>$t
			));

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

    public function taxonOrphansAction()
    {
		$this->checkAuthorisation();
        $this->setPageName($this->translate('Orphaned taxa'));

		$this->smarty->assign('concepts',$this->getOrphanedSpeciesList());
		$this->smarty->assign('treetop',$this->treeGetTop());
		$this->printPage();
	}

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action'))
		{
            return;
		}

		$this->UserRights->setActionType( $this->UserRights->getActionRead() );

		$return=null;

		if ( $this->getAuthorisationState()==false ) return;

		if (
			$this->rHasVal('action', 'get_lookup_list') ||
			$this->rHasVal('action', 'species_lookup') ||
			$this->rHasVal('action', 'taxon_id') ||
			$this->rHasVal('action', 'parent_taxon_id')
		)
		{
		    $p = $this->rGetAll();
		    if ($this->rHasVal('action', 'parent_taxon_id') && $this->rHasVal('base_rank_id')) {
		        $p['rank_above'] = $this->rGetVal('base_rank_id');
		    }
		    $return=$this->getSpeciesLookupList($p);
        }
		else
		if (
		    $this->rHasVal('action', 'expert_lookup') ||
		    $this->rHasVal('action', 'taxon_actor_id') ||
		    $this->rHasVal('action', 'name_expert_id') ||
			$this->rHasVal('action', 'name_organisation_id') ||
			$this->rHasVal('action', 'main_language_name_expert_id') ||
			$this->rHasVal('action', 'main_language_name_organisation_id') ||
			$this->rHasVal('action', 'presence_expert_id') ||
			$this->rHasVal('action', 'presence_organisation_id')
		)
		{
            $return=$this->getExpertsLookupList($this->rGetAll());
        }
		else
		if ($this->rHasVal('action', 'get_inheritable_name'))
		{
			$return=$this->getInheritableName(array('id'=>$this->rGetId()));
        }


        $this->allowEditPageOverlay=false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
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
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$type_id=isset($p['type_id']) ? $p['type_id'] : null;
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;

        $name=$this->models->NsrTaxonModel->getName(array(
			'label_language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon_id,
			'language_id'=>$language_id,
			'type_id'=>$type_id,
			'name_id'=>$id
		));

		if ( isset($name['id']) )
		{
			$name['addition']=$this->getNameAddition(array('name_id'=>$name['id']));
		}

		return $name;
	}

	private function getNames($p)
	{
		$taxon_id=isset($p['id']) ? $p['id'] : null;
		$base_rank_id=isset($p['base_rank']) ? $p['base_rank'] : null;

        $names=$this->models->NsrTaxonModel->getNames(array(
			'label_language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon_id
		));

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

			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']==PREDICATE_VALID_NAME)
			{
				$names[$key]['name_no_tags']=strip_tags($this->addHybridMarkerAndInfixes( [ 'name'=>$names[$key]['name_no_tags'],'base_rank_id'=>$base_rank_id,'taxon_id'=>$taxon_id ] ));
			}
			else
			if ($val['language_id']==LANGUAGE_ID_SCIENTIFIC && $val['nametype']!=PREDICATE_VALID_NAME && isset($val['rank_id']))
			{
				$names[$key]['name_no_tags']=strip_tags($this->addHybridMarkerAndInfixes( [ 'name'=>$names[$key]['name_no_tags'],'base_rank_id'=>$val['rank_id'],'taxon_id'=>$taxon_id ] ));
			}

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

	private function getPreferredNames( $taxon_id )
	{
		if (empty($taxon_id))
		{
			return;
		}

        return $this->models->NsrTaxonModel->getPreferredNames(array(
			'label_language_id'=>$this->getDefaultProjectLanguage(),
			'project_id'=>$this->getCurrentProjectId(),
			'taxon_id'=>$taxon_id,
			'type_id'=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']
		));
	}

	private function getPresenceData($id)
	{
		$data=$this->models->NsrTaxonModel->getPresenceData(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$id
		));

		$data['presence_information_one_line']=str_replace(array("\n","\r","\r\n"),'<br />',$data['presence_information']);

		return $data;
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
			subsequently excluded from the list in the where-statement.
		*/

		return $this->models->NsrTaxonModel->getStatuses(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId()
		));

	}

	private function getHabitats()
	{
		return $this->models->NsrTaxonModel->getHabitats(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"project_id"=>$this->getCurrentProjectId()
		));
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
        return $this->models->NsrTaxonModel->getLanguages(array(
			"project_id"=>$this->getCurrentProjectId(),
			"label_language_id"=>$this->getDefaultProjectLanguage()
		));
	}

	private function getDeletedSpeciesList()
	{
		$taxa=$this->models->NsrTaxonModel->getDeletedSpeciesList( [ "project_id"=>$this->getCurrentProjectId() ] );
		foreach((array)$taxa as $key=>$val)
		{
			$taxa[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
		}
		return $taxa;
	}

	private function getOrphanedSpeciesList()
	{
		$taxa=$this->models->NsrTaxonModel->getOrphanedSpeciesList([ "project_id"=>$this->getCurrentProjectId() ] );

		foreach((array)$taxa as $key=>$val)
		{
			$taxa[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['taxon_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
		}

		return $taxa;
	}

	private function getSpeciesList($p)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
        $id=isset($p['id']) ? (int)$p['id'] : null;
        $nametype=isset($p['nametype']) ? (int)$p['nametype'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $formatted=isset($p['formatted']) ? $p['formatted']==1 : true;
        $taxaOnly=isset($p['taxa_only']) ? $p['taxa_only']==1 : false;
        $rankAbove=isset($p['rank_above']) ? (int)$p['rank_above'] : false;
        $rankEqualAbove=isset($p['rank_equal_above']) ? (int)$p['rank_equal_above'] : false;
		$limit=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;
        $haveDeleted = isset($p['have_deleted']) ? $p['have_deleted'] : 'no'; // yes, no, only
        $sort = isset($p['sort']) ? $p['sort'] : null;
        $offset = isset($p['offset']) ? $p['offset'] : null;

		$search=trim($this->removeSearchNoise($search));

		if (empty($search) && empty($id) && $haveDeleted!='only')
		{
			return null;
		}

		$taxa=$this->models->NsrTaxonModel->getSpeciesList(array(
			"search"=>$search,
			"language_id"=>$this->getDefaultProjectLanguage(),
			"type_id_preferred"=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			"type_id_valid"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"project_id"=>$this->getCurrentProjectId(),
			"match_start_only"=>$matchStartOnly,
			"taxa_only"=>$taxaOnly,
			"rank_above"=>$rankAbove,
			"rank_equal_above"=>$rankEqualAbove,
			"taxon_id"=>$id,
			"nametype"=>$nametype,
			"have_deleted"=>$haveDeleted,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset,
		));

		foreach ((array)$taxa as $key => $val)
		{
			
			$taxa[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );

			if ($val['nametype']==PREDICATE_VALID_NAME)
			{
				$taxa[$key]['label']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['label'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
			}
			else
			if (isset($val['synonym_base_rank_id']) && !empty($val['synonym_base_rank_id']))
			{
				$taxa[$key]['label']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['label'],'base_rank_id'=>$val['synonym_base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
			}

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
				$taxa[$key]['label']=
					$this->addHybridMarkerAndInfixes( [ 'name'=>$val['name'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] ) . $val['label_suffix'];
				$taxa[$key]['label']=sprintf($taxa[$key]['label'],'; '.sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']));
				
			}
			else
			{
				$taxa[$key]['label']=sprintf($taxa[$key]['label'],'');
			}

			if (!$formatted)
			{
				$taxa[$key]['label']=strip_tags($taxa[$key]['label']);
			}

		}

		return $taxa;

	}

	private function getInheritableName($p)
	{
        $taxonId=isset($p['id']) ? (int)$p['id'] : null;

		if (empty($taxonId))
			return null;

		$val=$this->models->NsrTaxonModel->getInheritableName(array(
			"project_id"=>$this->getCurrentProjectId(),
			"type_id"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"taxon_id"=>$taxonId
		));

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
        $p['formatted']=0;
        
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

		$data=$this->models->NsrTaxonModel->getExpertsLookupList(array(
			"project_id"=>$this->getCurrentProjectId(),
			"get_all"=>$getAll,
			"match_start_only"=>$matchStartOnly,
			"search"=>$search
		));
		
		// Ruud 31-10: append organisation to expert name if available
		foreach ($data as $i => $v) {
		    if (!empty($v['company_of_name'])) {
		        $data[$i]['label'] .= ' (' . $data[$i]['company_of_name'] . ')';
		    }
		}

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

		if ( $this->_suppress_parent_child_relation_checks )
		{
			if ( $parent_base_rank < $child_base_rank )
			{
				$this->setMessage( $this->translate("Concept saved with irregular parent (allowed through the 'suppress_parent_child_relation_checks' setting).") );
				return true;
			}
			else
			{
				$this->addError( $this->translate("Parent rank cannot be same as, or below concept rank (even with 'suppress_parent_child_relation_checks' setting in effect).") );
				$this->addError( $this->translate("Concept not saved.") );
				return false;
			}
		}

		$ranks=$this->models->Ranks->_get(array(
			'id'=>'*',
			'columns'=>'id,rank',
			'fieldAsIndex'=>'id'
		));

		$error=null;

		if (($child_base_rank==NOTHOSPECIES_RANK_ID && $parent_base_rank!=NOTHOGENUS_RANK_ID) &&
		   ($child_base_rank==NOTHOSPECIES_RANK_ID && $parent_base_rank!=GENUS_RANK_ID))
		{
			// nothospecies alleen onder nothogenus of genus
			$error=array($ranks[NOTHOGENUS_RANK_ID]['rank'],$ranks[GENUS_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==NOTHOSUBSPECIES_RANK_ID || $child_base_rank==NOTHOVARIETAS_RANK_ID) && $parent_base_rank!=NOTHOSPECIES_RANK_ID)
		{
			$error=array($ranks[NOTHOSPECIES_RANK_ID]['rank']);
		}
		else
		if (
			$child_base_rank>SPECIES_RANK_ID &&
				$child_base_rank!=NOTHOGENUS_RANK_ID &&
				$child_base_rank!=NOTHOSPECIES_RANK_ID &&
				$child_base_rank!=NOTHOSUBSPECIES_RANK_ID &&
				$child_base_rank!=NOTHOVARIETAS_RANK_ID &&
			$parent_base_rank!=SPECIES_RANK_ID)
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
			($child_base_rank==SPECIES_RANK_ID && $parent_base_rank!=SUBGENUS_RANK_ID))
		{
			// soort moet onder genus of subgenus
			$error=array($ranks[GENUS_RANK_ID]['rank'],$ranks[SUBGENUS_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==SUBGENUS_RANK_ID && $parent_base_rank!=GENUS_RANK_ID)
		{
			// subgenus moet onder genus
			$error=array($ranks[GENUS_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==NOTHOGENUS_RANK_ID || $child_base_rank==GENUS_RANK_ID) &&
			(
				$parent_base_rank!=FAMILIA_RANK_ID && 
				$parent_base_rank!=SUBFAMILIA_RANK_ID && 
				$parent_base_rank!=TRIBUS_RANK_ID
			))
		{
			// genus moet onder familie, subfamilia of tribus
			$error=array($ranks[FAMILIA_RANK_ID]['rank'],$ranks[SUBFAMILIA_RANK_ID]['rank'],$ranks[TRIBUS_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==TRIBUS_RANK_ID && $parent_base_rank!=FAMILIA_RANK_ID) &&
			($child_base_rank==TRIBUS_RANK_ID && $parent_base_rank!=SUBFAMILIA_RANK_ID))
		{
			// tribus moet onder familia of subfamilia
			$error=array($ranks[FAMILIA_RANK_ID]['rank'],$ranks[SUBFAMILIA_RANK_ID]['rank']);
		}
		if ($child_base_rank==SUBFAMILIA_RANK_ID && $parent_base_rank!=FAMILIA_RANK_ID)
		{
			// subfamilie moet onder familie
			$error=array($ranks[FAMILIA_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==FAMILIA_RANK_ID && $parent_base_rank!=INFRAORDER_RANK_ID) &&
			($child_base_rank==FAMILIA_RANK_ID && $parent_base_rank!=SUBORDO_RANK_ID) &&
			($child_base_rank==FAMILIA_RANK_ID && $parent_base_rank!=ORDO_RANK_ID) &&
			($child_base_rank==FAMILIA_RANK_ID && $parent_base_rank!=SUPERFAMILIA_RANK_ID))
		{
			// familie moet onder infraorder, suborde, orde of superfamilia
			$error=array($ranks[INFRAORDER_RANK_ID]['rank'],$ranks[SUBORDO_RANK_ID]['rank'],$ranks[ORDO_RANK_ID]['rank'],$ranks[SUPERFAMILIA_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==SUPERFAMILIA_RANK_ID && $parent_base_rank!=ORDO_RANK_ID) &&
			($child_base_rank==SUPERFAMILIA_RANK_ID && $parent_base_rank!=INFRAORDER_RANK_ID) &&
			($child_base_rank==SUPERFAMILIA_RANK_ID && $parent_base_rank!=SUBORDO_RANK_ID))
		{
			// superfamilia moet onder orde, subordo of infraorde
			$error=array($ranks[ORDO_RANK_ID]['rank'],$ranks[SUBORDO_RANK_ID]['rank'],$ranks[INFRAORDER_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==INFRAORDER_RANK_ID && $parent_base_rank!=SUBORDO_RANK_ID)
		{
			// infraorder moet onder subordo
			$error=array($ranks[ORDO_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==SUBORDO_RANK_ID && $parent_base_rank!=ORDO_RANK_ID)
		{
			// suborde moet onder orde
			$error=array($ranks[ORDO_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==ORDO_RANK_ID && $parent_base_rank!=SUBCLASSIS_RANK_ID) &&
			($child_base_rank==ORDO_RANK_ID && $parent_base_rank!=CLASSIS_RANK_ID) &&
			($child_base_rank==ORDO_RANK_ID && $parent_base_rank!=SUPERORDER_RANK_ID)
			)
		{
			// orde moet onder subklasse, klasse of superorder
			$error=array($ranks[SUBCLASSIS_RANK_ID]['rank'],$ranks[CLASSIS_RANK_ID]['rank'],$ranks[SUPERORDER_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==SUPERORDER_RANK_ID && $parent_base_rank!=CLASSIS_RANK_ID) &&
			($child_base_rank==SUPERORDER_RANK_ID && $parent_base_rank!=SUBCLASSIS_RANK_ID))
		{
			// superordo moet onder klasse of subclassis
			$error=array($ranks[CLASSIS_RANK_ID]['rank'],$ranks[SUBCLASSIS_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==SUBCLASSIS_RANK_ID && $parent_base_rank!=CLASSIS_RANK_ID)
		{
			// subklasse moet onder klasse
			$error=array($ranks[CLASSIS_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==CLASSIS_RANK_ID && $parent_base_rank!=SUPERCLASS_RANK_ID) &&
			($child_base_rank==CLASSIS_RANK_ID && $parent_base_rank!=SUBPHYLUM_RANK_ID) &&
			($child_base_rank==CLASSIS_RANK_ID && $parent_base_rank!=PHYLUM_RANK_ID))
		{
			// klasse moet onder superclassis, subphylum of phylum
			$error=array($ranks[SUPERCLASS_RANK_ID]['rank'],$ranks[SUBPHYLUM_RANK_ID]['rank'],$ranks[PHYLUM_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==SUBPHYLUM_RANK_ID && $parent_base_rank!=PHYLUM_RANK_ID)
		{
			// subphylum moet onder phylum
			$error=array($ranks[PHYLUM_RANK_ID]['rank']);
		}
		else
		if (($child_base_rank==PHYLUM_RANK_ID && $parent_base_rank!=SUBREGNUM_RANK_ID) &&
			($child_base_rank==PHYLUM_RANK_ID && $parent_base_rank!=REGNUM_RANK_ID))
		{
			// phylum moet moet onder subrijk of rijk
			$error=array($ranks[SUBREGNUM_RANK_ID]['rank'],$ranks[REGNUM_RANK_ID]['rank']);
		}
		else
		if ($child_base_rank==SUBREGNUM_RANK_ID && $parent_base_rank!=REGNUM_RANK_ID)
		{
			// subrijk moet onder rijk
			$error=array($ranks[REGNUM_RANK_ID]['rank']);
		}
		else
		if ( $parent_base_rank >= $child_base_rank )
		{
			$error=array('a higher rank');
		}

		if ($error)
		{
			$this->addError(
				sprintf(
					$this->translate("A %s can only have %s as a parent."),
					$ranks[$child_base_rank]['rank'],
					implode(" " . $this->translate('or') . " ",$error)
				).
				" " . $this->translate("Concept not saved.")

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
			$this->addError($this->translate("Authorship missing. Concept not saved."));
			return false;
		}

		return true;
	}

	private function checkNamePartsMatchRank($baseRank,$uninomial,$specificEpithet,$infraSpecificEpithet)
	{
		if ( $baseRank<SPECIES_RANK_ID )
		{
			if ( empty($uninomial) && ($baseRank<SPECIES_RANK_ID && $baseRank>=GENUS_RANK_ID) )
			{
				$this->addError($this->translate("Scientific name: genus is missing. Concept not saved."));
				return false;
			}
			else
			if ( empty($uninomial) && $baseRank<GENUS_RANK_ID )
			{
				$this->addError($this->translate("Scientific name: uninomial is missing. Concept not saved."));
				return false;
			}
			else
			if ( !empty($specificEpithet) || !empty($infraSpecificEpithet) )
			{
				$this->addError($this->translate("Scientific name can consist of only a single element. Concept not saved."));
				return false;
			}
		}
		else
		if ( $baseRank==SPECIES_RANK_ID || $baseRank==NOTHOSPECIES_RANK_ID )
		{
			if ( empty($uninomial) || empty($specificEpithet) )
			{
				$this->addError($this->translate("Scientific name incomplete. Concept not saved."));
				return false;
			}
			else
			if ( !empty($infraSpecificEpithet) )
			{
				$this->addError($this->translate("Scientific name cannot have a third name element. Concept not saved."));
				return false;
			}

		}
		else
		if ( $baseRank>SPECIES_RANK_ID )
		{
			if (empty($uninomial) || empty($specificEpithet) || empty($infraSpecificEpithet))
			{
				$this->addError($this->translate("Scientific name incomplete. Concept not saved."));
				return false;
			}
		}

		return true;

	}

	private function checkNameUniqueness($name,$childRankId,$parentId)
	{
		$d=$this->models->NsrTaxonModel->checkNameUniqueness(array(
			"project_id"=>$this->getCurrentProjectId(),
			"name"=>$name,
			"child_rank_id"=>$childRankId,
			"parent_id"=>$parentId
		));

		if ($d)
		{
			$this->addError(
				$this->translate("Combination of name, rank and parent already exists") .
				( $d[0]['is_deleted']==1 ? " (deleted taxon)" : "" ).". ".
				$this->translate("Concept not saved.") .
				" <a href=\"taxon.php?id=".$d[0]['id']."\">" . $this->translate('Show taxon') . "</a>");
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

		if (empty($name) || empty($rank) || empty($uninomial))
		{
			if (empty($name)) $this->addError($this->translate('Empty concept name. Concept not saved.'));
			if (empty($rank)) $this->addError($this->translate('Rank missing. Concept not saved.'));
			if (empty($parent)) $this->addError($this->translate('Parent missing. Concept not saved.'));
			if (empty($uninomial)) $this->addError($this->translate('Genus or uninomial missing. Concept not saved.'));
			$this->setConceptId(null);
			return;
		}

		if ( empty($parent) )
		{
			$num=$this->models->NsrTaxonModel->getNumberOfUndeletedTaxa(['project_id'=>$this->getCurrentProjectId()]);
			$this->firstTaxon=($num==0);

			if ( !$this->firstTaxon )
			{
				$this->addError($this->translate('Parent missing. Concept not saved.'));
				$this->setConceptId(null);
				return;
			}
			else
			{
				$this->addWarning($this->translate('Concept saved without parent.'));
			}
		}
/*
		if (!$this->checkAuthorshipAgainstRank($baseRank,$authorship))
		{
			$this->setConceptId(null);
			return;
		}
*/
		if (!$this->firstTaxon && !$this->checkParentChildRelationship($baseRank,$parent))
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
			$this->addError($this->translate('Presence data cannot be saved for higher taxa. Concept not saved.'));
			$this->setConceptId(null);
			return;
		} else
		if (
			(empty($presencePresenceId) || empty($presenceExpertId) || empty($presenceOrganisationId) || empty($presenceReferenceId)) &&

			$rank>=SPECIES_RANK_ID
		)
		{
			$this->addWarning($this->translate('Incomplete presence data. Concept saved nonetheless.'));
		}

		if ($baseRank==GENUS_RANK_ID && empty($authorship))
		{
			$this->addWarning($this->translate("No authorship. Concept saved nonetheless."));
		}


		// we passed all tests!
		$d=$this->models->Taxa->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'is_empty' =>'0',
			'rank_id' => $rank,
			'taxon' => $name,
		));

		if ($d)
		{
			$this->setConceptId($this->models->Taxa->getNewId());
			$this->addMessage($this->translate('New concept created.'));
			$this->logChange(array(
				'after'=>array('id'=>$this->getConceptId(),
				'taxon'=>$name,'rank_id' =>$rank),
				'note'=>'new concept '.$name . ($this->firstTaxon ? ' (first taxon)' : '' )));
			//$this->setIsNewRecord(true);
			$this->updateConcept();
		}
		else
		{
			$this->addError($this->translate('Creation of new concept failed.'));
		}
	}

	private function updateConcept()
	{
		$this->createConceptNsrIds();
		$this->createConceptPresence();

		$before=$this->getConcept($this->getConceptId());
		$before['presence']=$this->getPresenceData($this->getConceptId());

		if ($this->rHasVar('concept_taxon'))
		{
			if ($this->updateConceptTaxon($this->rGetVal('concept_taxon')))
			{
				$this->addMessage($this->translate('Name saved.'));
			}
			else
			{
				$this->addError($this->translate('Name not saved.'));
			}
		}

		if ($this->rHasVar('concept_rank_id'))
		{
			if ($this->updateConceptRankId($this->rGetVal('concept_rank_id')))
			{
				$this->addMessage($this->translate('Rank saved.'));
			}
			else
			{
				$this->addError($this->translate('Rank not saved.'));
			}
		}

		if ($this->rHasVar('parent_taxon_id'))
		{
			if ($this->updateParentId($this->rGetVal('parent_taxon_id')))
			{
				$this->addMessage($this->translate('Parent connection saved.'));
			}
			else
			{
				$this->addError($this->translate('Parent connection not saved.'));
			}
		}

		if ($this->rHasVar('presence_presence_id'))
		{
			if ($this->updateConceptPresenceId($this->rGetVal('presence_presence_id')))
			{
				$this->addMessage($this->translate('Presence data saved.'));
			}
			else
			{
				$this->addError($this->translate('Presence data not saved.'));
			}
		}

		if ($this->rHasVar('presence_habitat_id'))
		{
			if ($this->updateConceptHabitatId($this->rGetVal('presence_habitat_id')))
			{
				$this->addMessage($this->translate('Habitat saved.'));
			}
			else
			{
				$this->addError($this->translate('Habitat not saved.'));
			}
		}

		if ($this->rHasVar('presence_expert_id'))
		{
			if ($this->updatePresenceExpertId($this->rGetVal('presence_expert_id')))
			{
				$this->addMessage($this->translate('Expert saved.'));
			}
			else
			{
				$this->addError($this->translate('Expert not saved.'));
			}
		}

		if ($this->rHasVar('presence_organisation_id'))
		{
			if ($this->updatePresenceOrganisationId($this->rGetVal('presence_organisation_id')))
			{
				$this->addMessage($this->translate('Organisation saved.'));
			}
			else
			{
				$this->addError($this->translate('Organisation not saved.'));
			}
		}

		if ($this->rHasVar('presence_reference_id'))
		{
			if ($this->updatePresenceReferenceId($this->rGetVal('presence_reference_id')))
			{
				$this->addMessage($this->translate('Publication saved.'));
			}
			else
			{
				$this->addError($this->translate('Publication not saved.'));
			}
		}

		$this->deleteEmptyPresenceData();

		$after=$this->getConcept($this->rGetId());
		$after['presence']=$this->getPresenceData($this->rGetId());

		$this->logChange(array('before'=>$before,'after'=>$after,'note'=>'updated concept '.$before['taxon']));
	}

	private function updateConceptTaxon($values)
	{
		$before=$this->models->Taxa->_get(array(
			'id'=>array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId()),
			'columns'=>'taxon'
		));

		$this->models->Taxa->resetAffectedRows();

		$result=$this->models->Taxa->update(
			array('taxon'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);

		if ($result && $this->models->Taxa->getAffectedRows()!=0)
		{
			$after=$this->models->Taxa->_get(array(
				'id'=>array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId()),
				'columns'=>'taxon'
			));
			$this->logChange(array('before'=>$before[0],'after'=>$after[0],'note'=>'updated concept name '.$before[0]['taxon']));
			return true;
		}
		else
		{
			$exist=$this->getTaxonByName(trim($values['new']));
			if ($exist)
			{
				$this->addError(sprintf($this->translate('Name already exists for') . ' <a target="_new" href="taxon.php?id=%s">' . $this->translate('another taxon concept') . '</a>.',$exist['id']));
			}

			$this->addError($this->translate('Update name taxon concept failed.'));
			return false;
		}
	}

	private function updateConceptRankId($values)
	{
		return $this->models->Taxa->update(
			array('rank_id'=>trim($values['new'])),
			array('id'=>$this->getConceptId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function updateParentId($values)
	{
		return $this->models->Taxa->update(
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


	private function deleteEmptyPresenceData()
	{
		$d=$this->models->PresenceTaxa->_get(array(
			'id'=>
				array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$this->getConceptId()
				)
			));

		if ($d)
		{
			if (is_null($d[0]['presence_id']) &&
				is_null($d[0]['habitat_id']) &&
				is_null($d[0]['actor_id']) &&
				is_null($d[0]['actor_org_id']) &&
				is_null($d[0]['reference_id']))
			{
				$this->models->PresenceTaxa->delete(array(
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$this->getConceptId()
				));
			}
		}
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
			$this->addMessage($this->translate('New name created.'));
			$this->setIsNewRecord(true);
			$this->updateName();
		}
		else
		{
			$this->addError($this->translate('Creation of new name failed.'));
		}
	}

	private function checkMainLanguageCommonName()
	{
		$name=$this->rGetVal('main_language_name');

		if (!isset($name['new'])) return;

		$d=$this->models->NsrTaxonModel->checkMainLanguageCommonName(array(
			"project_id"=>$this->getCurrentProjectId(),
			"name"=>$name['new']
		));

		if ($d)
		{
			$this->setMessage(sprintf($this->translate('Common name "%s" already exists (name saved nonetheless):')),$name['new']);
			foreach((array)$d as $val)
			{
				$this->setMessage('<a href="name.php?id='.$val['id'].'">' . $this->translate('Name of:') . ' '.$val['taxon'].'</a>');
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
			$this->addMessage($this->translate('Common name created.'));

			$newname=$this->getName(array('id'=>$this->getNameId()));
			$this->logChange(array('after'=>$newname,'note'=>'new main language name '.$newname['name']));

			if ($this->rHasVar('main_language_name_reference_id'))
			{
				if (!$this->updateNameReferenceId($this->rGetVal('main_language_name_reference_id')))
				{
					$this->addError($this->translate('Common name: reference not saved.'));
				}
			}

			if ($this->rHasVar('main_language_name_expert_id'))
			{
				if (!$this->updateNameExpertId($this->rGetVal('main_language_name_expert_id')))
				{
					$this->addError($this->translate('Common name: expert not saved.'));
				}
			}

			if ($this->rHasVar('main_language_name_organisation_id'))
			{
				if (!$this->updateNameOrganisationId($this->rGetVal('main_language_name_organisation_id')))
				{
					$this->addError($this->translate('Common name: organisation not saved.'));
				}
			}

		}
		else
		{
			$this->addError($this->translate('Creation of common name failed.'));
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
				$this->addMessage($this->translate('Name saved.'));
			}
			else
			{
				$this->addError($this->translate('Name not saved.'));
			}
		}

		if ($this->rHasVar('name_uninomial'))
		{
			if ($this->updateNameUninomial($this->rGetVal('name_uninomial')))
			{
				$this->addMessage($this->translate('Uninomial saved.'));
			}
			else
			{
				$this->addError($this->translate('Uninomial not saved.'));
			}
		}

		if ($this->rHasVar('name_specific_epithet'))
		{
			if ($this->updateNameSpecificEpithet($this->rGetVal('name_specific_epithet')))
			{
				$this->addMessage($this->translate('Specific epithet saved.'));
			}
			else
			{
				$this->addError($this->translate('Specific epithet not saved.'));
			}
		}

		if ($this->rHasVar('name_infra_specific_epithet'))
		{
			if ($this->updateNameInfraSpecificEpithet($this->rGetVal('name_infra_specific_epithet')))
			{
				$this->addMessage($this->translate('Infraspecific epithet saved.'));
			}
			else
			{
				$this->addError($this->translate('Infraspecific epithet not saved.'));
			}
		}

		if ($this->rHasVar('name_authorship'))
		{
			if ($this->updateNameAuthorship($this->rGetVal('name_authorship')))
			{
				$this->addMessage($this->translate('Authorship saved.'));
			}
			else
			{
				$this->addError($this->translate('Authorship not saved.'));
			}
		}

		if ($this->rHasVar('name_name_author'))
		{
			if ($this->updateNameAuthor($this->rGetVal('name_name_author')))
			{
				$this->addMessage($this->translate('Name author saved.'));
			}
			else
			{
				$this->addError($this->translate('Name author not saved.'));
			}
		}

		if ($this->rHasVar('name_authorship_year'))
		{
			if ($this->updateNameAuthorshipYear($this->rGetVal('name_authorship_year')))
			{
				$this->addMessage($this->translate('Year saved.'));
			}
			else
			{
				$this->addError($this->translate('Year not saved.'));
			}
		}

		if ($this->rHasVar('name_type_id'))
		{
			if ($this->updateNameTypeId($this->rGetVal('name_type_id')))
			{
				$this->addMessage($this->translate('Type saved.'));
			}
			else
			{
				$this->addError($this->translate('Type not saved.'));
			}
		}


		if ($this->rHasVar('name_rank_id'))
		{
			if ($this->updateNameRankId($this->rGetVal('name_rank_id')))
			{
				$this->addMessage($this->translate('Rank saved.'));
			}
			else
			{
				$this->addError($this->translate('Rank not saved.'));
			}
		}

		if ($this->rHasVar('name_language_id'))
		{
			if ($this->updateNameLanguageId($this->rGetVal('name_language_id')))
			{
				$this->addMessage($this->translate('Language saved.'));
			}
			else
			{
				$this->addError($this->translate('Language not saved.'));
			}
		}

		if ($this->rHasVar('name_reference_id'))
		{
			if ($this->updateNameReferenceId($this->rGetVal('name_reference_id')))
			{
				$this->addMessage($this->translate('Reference saved.'));
			}
			else
			{
				$this->addError($this->translate('Reference not saved.'));
			}
		}

		if ($this->rHasVar('name_expert_id'))
		{
			if ($this->updateNameExpertId($this->rGetVal('name_expert_id')))
			{
				$this->addMessage($this->translate('Expert saved.'));
			}
			else
			{
				$this->addError($this->translate('Expert not saved.'));
			}
		}

		if ($this->rHasVar('name_organisation_id'))
		{
			if ($this->updateNameOrganisationId($this->rGetVal('name_organisation_id')))
			{
				$this->addMessage($this->translate('Organisation saved.'));
			}
			else
			{
				$this->addError($this->translate('Organisation not saved.'));
			}
		}

		if ($this->rHasVar('aanvulling'))
		{
			if ($this->saveNameAanvulling($this->rGetVal('aanvulling')))
			{
				$this->addMessage($this->translate('Remark saved.'));
			}
			else
			{
				$this->addError($this->translate('Remark not saved.'));
			}
		}

		$after=$this->getName(array('id'=>$this->getNameId()));
		$this->logChange(
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
			$this->logChange(array('before'=>$before,'note'=>'deleted name '.$before[0]['name']));
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
				$this->addMessage($this->translate('Concept name saved.'));
			}
			else
			{
				$this->addError($this->translate('Concept name not saved.'));
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

	private function updateNameRankId($values)
	{
		return $this->models->Names->update(
			array('rank_id'=>empty($values['new']) ? 'null' : trim($values['new'])),
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
			$this->addWarning($this->translate("Composition of name elements does not match the name."));
		}
		if (!$this->checkAuthorshipYear($name))
		{
			$this->addWarning($this->translate("Authorship differs from 'author(s)' + 'year'."));
		}
		if (!$this->checkYear($name))
		{
			$this->addWarning($this->translate("Invalid year."));
		}
	}

	private function doNameReferentialChecks($concept)
	{
		if (!$this->checkIfConceptRetainsScientificName($concept['id']))
		{
			$this->addWarning($this->translate("No scientific name linked to this concept."));
		}

		if (!$this->checkIfConceptRetainsNameInMainProjectLanguage($concept['id']) && $concept['base_rank']>=SPECIES_RANK_ID)
		{
			$this->addWarning($this->translate("No preferred common name linked to this concept."));
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

		$a=trim((!empty($name['name_author']) ? $name['name_author'].', ' : null).
				(!empty($name['authorship_year']) ? $name['authorship_year'] : null),', ');

		$b=trim(substr($name['authorship'],0,1)=='(' && substr($name['authorship'],-1)==')' ? trim($name['authorship'],')( ') : $name['authorship']);

		return ($a==$b);
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

			$this->logChange(
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

			$this->logChange(
				array(
					'before'=>$before,
					'after'=>$after,
					'note'=>'removed deletion mark from '.$before['taxon']
				)
			);

		}
	}

	private function getReference($literature_id=null)
	{
		if (empty($literature_id))
			return;

		return $this->models->NsrTaxonModel->getReference(array(
			"project_id"=>$this->getCurrentProjectId(),
			"literature_id"=>$literature_id
		));
	}

	private function getTaxonBranch( $parent )
	{
		return $this->models->NsrTaxonModel->getTaxonBranch(array(
			"type_id"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"project_id"=>$this->getCurrentProjectId(),
			"parent_id"=>$parent['id']
		));
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

		$d=$this->models->NsrTaxonModel->checkIfNameExistsInConceptsKingdom(array(
			"project_id"=>$this->getCurrentProjectId(),
			"type_id"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"intended_new_concept_name"=>$intendedNewConceptName,
			"taxon_id"=>$concept['id']
		));

		if ($d)
		{
			// checking whether everything is in the same kingdom
			$a[]=$concept['id'];
			foreach((array)$d as $key=>$val)
				$a[]=$val['taxon_id'];

			$parentage=$this->models->TaxonQuickParentage->_get(
				array(
					"columns" => "taxon_id,parentage",
					"id"=> array("project_id"=>$this->getCurrentProjectId(),"taxon_id #"=>"in (".implode(",",$a).")"),
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

		$data=$this->rGetAll();

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

		$data=$this->rGetAll();

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
				$this->addError($this->translate('Valid name of current taxon does not have a separate uninomial.'));
				$canChange=false;
			}
			if ($concept['base_rank']>=SPECIES_RANK_ID && empty($name['specific_epithet']))
			{
				$this->addError($this->translate('Valid name of current taxon does not have a separate species element.'));
				$canChange=false;
			}
			if (empty($parentName['uninomial']))
			{
				$this->addError($this->translate('Valid name of selected parent does not have a separate uninomial.'));
				$canChange=false;
			}
			if ($parent['base_rank']>=SPECIES_RANK_ID && empty($parentName['specific_epithet']))
			{
				$this->addError($this->translate('Valid name of selected parent does not have a separate species element.'));
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
			    	sprintf(
						$this->translate('Taxon %s already exists in the same kingdom.'), '"<a href="taxon.php?id='.$d.'">'.$intendedNewConceptName.'</a>"'
			    	)
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

			$d=$this->models->NsrTaxonModel->checkIfGenusWithSameNameExists(array(
				"project_id"=>$this->getCurrentProjectId(),
				"type_id"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
				"intended_new_concept_name"=>$intendedNewConceptName,
				"parent_id"=>$concept['parent_id'],
				"taxon_id"=>$concept['id']
			));

			if ($d)
			{
			    $this->addError(
			    	sprintf(
						$this->translate('Taxon %s with the same parent already exists.'), '"<a href="taxon.php?id='.$d[0]['taxon_id'].'">'.$intendedNewConceptName.'</a>"'
			    	)
			    );
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
			    	sprintf(
						$this->translate('Taxon %s already exists in the same kingdom.'), '"<a href="taxon.php?id='.$d.'">'.$iName.'</a>"'
			    	)
			    );
				return false;
			}
		}

		return true;
	}

	private function doParentChange()
	{
		// preliminairies
		$data=$this->rGetAll();

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

			verwijderd nav LINNA-577:
			als authorship nog geen haakjes had, krijgt hij die nu.

		c) update de naam van het concept op basis van de nieuwe geaccepeerde naam.
		*/

		foreach((array)$taxa as $key=>$val)
		{

			$newSynonym=$val['name'];

			$d=$this->models->Names->_get(array(
				"id"=>
					array(
						"project_id"=>$this->getCurrentProjectId(),
						"taxon_id"=>$val['taxon_id'],
						"name"=>$this->models->Names->escapeString($newSynonym),
						"type_id"=>$this->_nameTypeIds[PREDICATE_SYNONYM]['id']
					)
			));

			if ($d)
			{
			    $this->addWarning(
			    	sprintf(
						$this->translate("Synonym %s already exists; duplicate synonym created."), "'$newSynonym'"
			    	)
			    );
			}

			$this->models->Names->update(
				array(
					"type_id"=>$this->_nameTypeIds[PREDICATE_SYNONYM]['id']
				),
				array(
					"project_id"=>$this->getCurrentProjectId(),
					"id"=>$val['id']
				)
			);

			$after=$this->models->Names->_get(array('id'=> array('id'=>$val['id'])));
			$this->logChange(array('before'=>$name,'after'=>$after[0],'note'=>'changed valid name '.$newSynonym.' to synonym'));
			$this->addMessage($this->translate('Accepted name converted to synonym.'));


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
				$infraSpecificEpithet=trim($data['name_infra_specific_epithet']['new']);
			}
			else
			{
				$infraSpecificEpithet=$val['infra_specific_epithet'];
			}

			$authorship=
				(!empty($val['name_author']) ? $val['name_author'] : null).
				(!empty($val['name_author']) && !empty($val['authorship_year']) ? ', ' : '').
				(!empty($val['authorship_year']) ? $val['authorship_year'] : null);
/*
			$authorship=
				trim(!empty($authorship) ? '('.$authorship.')' : '');
*/
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

			$this->logChange(array('after'=>$after[0],'note'=>'created new valid name '.$newName));
			$this->addMessage($this->translate('New accepted name created.'));

			$this->setConceptId($val['taxon_id']);
			$this->updateConceptTaxon(array('new'=>$newName));
			$this->addMessage($this->translate('Name concept updated.'));

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
		
		$g=$this->models->NsrTaxonModel->getTraitgroups(array(
			"language_id"=>$this->getDefaultProjectLanguage(),
			"taxon_id"=>$this->getConceptId(),
			"project_id"=>$this->getCurrentProjectId(),
			"parent_id"=>$parent
		));
		
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


	Ruud 24-06-16:
	- Nederlandse strings vertaald naar Engelse
	- messages en warnings met $this->translate
	- alle vertaalde strings:

    $translate = array(
        '"Authorship" niet opgeslagen.' => 'Authorship not saved.',
        '"Authorship" opgeslagen.' => 'Authorship saved.',
        '× toevoegen aan hybriden & infixes (subsp., f., var.) toevoegen aan infraspecies' => 'add × to hybrids & infixes (subsp., f., var.) to infraspecies',
        'Aanmaak Nederlandse naam mislukt.' => 'Creation of common name failed.',
        'Aanmaak nieuw concept mislukt.' => 'Creation of new concept failed.',
        'Aanmaak nieuwe naam mislukt.' => 'Creation of new name failed.',
        'afbeeldingen' => 'images',
        'alle huidige tabbladen met tekst' => 'all tabs containing text',
        'alle meta-gegevens verwijderen' => 'delete all metadata',
        'alleen deze rang' => 'just this rank',
        'Alleen taxa tonen met de volgende rang' => 'Display only taxa with the following rank',
        'alles verwijderen' => 'remove all',
        'auteur toevoegen' => 'add author',
        'auteur' => 'author',
        'auteur(s)' => 'author(s)',
        'Auteurschap is niet ingevuld.' => 'Authorship has not been entered.',
        'auteurschap' => 'authorship',
        'Beschikbaar' => 'Available',
        'Bewerk het concept.' => 'Edit the concept.',
        'classificatie' => 'classification',
        'Concept gemarkeerd als verwijderd.' => 'Concept marked as deleted.',
        'CONCEPT IS GEMARKEERD ALS VERWIJDERD' => 'CONCEPT IS MARKED AS DELETED',
        'Concept niet langer gemarkeerd als verwijderd.' => 'Concept no longer marked as deleted.',
        'Concept opgeslagen zonder ouder.' => 'Concept saved without parent.',
        'concept' => 'concept',
        'conceptkaart' => 'concept card',
        'Conceptnaam niet opgeslagen.' => 'Concept name not saved.',
        'Conceptnaam opgeslagen.' => 'Concept name saved.',
        'conceptnaam wordt automatische samengesteld op basis van de geldige wetenschappelijke naam.' => 'concept name is concatenated automatically based on the valid scientific name.',
        'CSV- en bestandsinstellingen' => 'CSV and file settings',
        'de geldige naam' => 'the valid name',
        'derde naamdeel' => 'third name element',
        'Deze functie werkt de extra indextabel bij waarin ouder-kindrelaties van de taxonconcepten worden bijgehouden.' => 'This function updates the extra index table containing parent-child relationships.',
        'deze rang en lager' => 'this rank and lower',
        'deze rangen' => 'these ranks',
        'directe ouder' => 'direct parent',
        'Dit zijn oude paspoorttitels die overlappen met nieuwe titels.' => 'These are passport titles that overlap with new titles.',
        'doel' => 'output',
        'dubbelklik of klik op pijl om toe te voegen' => 'double-click or click arrow to add',
        'dubbelklik om te verwijderen' => 'double-click to remove',
        'een ander taxonconcept' => 'another taxon concept',
        'end of file-marker toevoegen (voor controle complete download)' => 'add end of file-marker (to check complete download)',
        'Engelse naam' => 'Name in English',
        'Er bestaat al een taxon %s in hetzelfde koninkrijk.' => 'Taxon %s already exists in the same kingdom.',
        'Er bestaat al een taxon %s in hetzelfde koninkrijk.' => 'Taxon %s already exists in the same kingdom.',
        'Er bestaat al een taxon %s onder dezelfde ouder.' => 'Taxon %s with the same parent already exists.',
        'Expert niet opgeslagen.' => 'Expert not saved.',
        'Expert niet opgeslagen.' => 'Expert not saved.',
        'Expert opgeslagen.' => 'Expert saved.',
        'Expert opgeslagen.' => 'Expert saved.',
        'expert' => 'expert',
        'exporteren' => 'export',
        'Extra kolommen' => 'Extra columns',
        'familie' => 'family',
        'Geaccepteerde naam omgezet naar synoniem.' => 'Accepted name converted to synonym.',
        'geen content (onzichtbaar)' => 'no content (invisible)',
        'geen dubbele quotes om waarden' => 'do not enclose values with double quotes',
        'Geen genus of uninomial. Concept niet opgeslagen.' => 'Genus or uninomial missing. Concept not saved.',
        'Geen ID.' => 'No ID.',
        'Geen ouder. Concept niet opgeslagen.' => 'Parent missing. Concept not saved.',
        'Geen rang. Concept niet opgeslagen.' => 'Rank missing. Concept not saved.',
        'geen waarde toekennen' => 'assign no value',
        'Geldige naam beoogde ouder heeft geen los specifiek epithet.' => 'Valid name of selected parent does not have a separate species element.',
        'Geldige naam beoogde ouder heeft geen losse uninominaal.' => 'Valid name of selected parent does not have a separate uninomial.',
        'geldige naam direct aanpassen' => 'rename valid name directly',
        'Geldige naam huidige taxon heeft geen los specifiek epithet.' => 'Valid name of current taxon does not have a separate species element.',
        'Geldige naam huidige taxon heeft geen losse uninominaal.' => 'Valid name of current taxon does not have a separate uninomial.',
        'geldige wetenschappelijke naam' => 'valid scientific name',
        'geldt ook voor synoniemen, als die ook geëxporteerd worden' => 'also applies to synonyms if these are part of the export',
        'genus of uninomial' => 'genus or uninomial',
        'genus/uninomial' => 'genus/uninomial',
        'gevestigde soorten' => 'established species',
        'Habitat niet opgeslagen.' => 'Habitat not saved.',
        'Habitat opgeslagen.' => 'Habitat saved.',
        'heeft content, is gepubliceerd' => 'has content, is published',
        'heeft content, niet gepubliceerd (onzichtbaar)' => 'has content, not published (invisible)',
        'Hoe toe te passen' => 'How to use',
        'Houd er rekening mee dat het bijwerken ongeveer een minuut in beslag kan nemen.' => 'Please take into account that an update may take a minute or so.',
        'html-tags in wetenschappelijke namen behouden (voor infixes)' => 'preserve html tags in scientific names (for infixes)',
        'huidige tabbladen met tekst zonder meta-gegevens' => 'current tabs with text lacking metadata',
        'In principe wordt de tabel automatische bijgewerkt, maar mocht blijken dat bijvoorbeeld de aantallen onderliggende soorten in de taxonomische boom niet overeenkomen met het werkelijke aantal, werk hem dan handmatig bij.' => 'This table should be updated automatically, but e.g. if the number of child taxa in the taxonomic tree is off, the table can be force-updated.',
        'Incomplete voorkomensgegevens. Concept wel opgeslagen.' => 'Incomplete presence data. Concept saved nonetheless.',
        'Indextabel bijwerken' => 'Update index table',
        'indien beschikbaar' => 'when present',
        'Infra specifiek epithet niet opgeslagen.' => 'Infraspecific epithet not saved.',
        'Infra-specifiek epithet opgeslagen.' => 'Infraspecific epithet saved.',
        'Jaar niet opgeslagen.' => 'Year not saved.',
        'Jaar opgeslagen.' => 'Year saved.',
        'jaar' => 'year',
        'kenmerken toevoegen' => 'add traits',
        'kiezen' => 'select',
        'klasse' => 'class',
        'komma' => 'comma',
        'Koppeling ouder niet opgeslagen.' => 'Parent connection not saved.',
        'Koppeling ouder opgeslagen.' => 'Parent connection saved.',
        'leeg' => 'empty',
        'Lege conceptnaam. Concept niet opgeslagen.' => 'Empty concept name. Concept not saved.',
        'Let op dat alle delen los moeten worden ingevoerd, er wordt niets automatisch samengevoegd!' => 'Note that all elements should be entered separately, nothing will be concatenated automatically!',
        'let op: bestaande meta-gegevens van de geselecteerde tab(s) worden overschreven' => 'note: existing metadata of the selected tab(s) will be overwritten',
        'Let op: de wetenschappelijke naam komt niet overeen met de samengestelde naamdelen. Dit is waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b. de juiste genus, soort, eventuele derde naamdeel en auteurschap in om de naamkaart volledig te maken.' => 'Note: the scientific name does not match the composite of individual name elements. Please enter the correct genus, species and (if applicable) subspecific epithet and authorship to complete the name card.',
        'Let op: de wetenschappelijke naam komt niet overeen met de samengestelde naamdelen. Dit is waarschijnlijk een overerving uit de oude Soortenregister-database. Vul a.u.b. de juiste uninomial en eventueel auteurschap in om de naamkaart volledig te maken.' => 'Note: the scientific name does not match the composite of individual name elements. Please enter the correct uninomial and (if applicable) authorship to complete the name card.',
        'Let op: wijzig om het genus te veranderen de taxonomische ouder van het concept.' => 'Note: you need to modify the taxonomic parent of the concept to change the genus.',
        'Let op' => 'Note',
        'literatuur' => 'literature',
        'losse naamdelen' => 'separate name elements',
        'Met deze default-waarden wordt een CSV gegenereerd die goed te openen is in Excel.' => 'The default values generate a CSV file that can be opened in Excel.',
        'Meta-gegevens kunnen alleen worden toegewezen aan tabbladen met tekst. Geen van de tabbladen bevat tekst. Voeg eerst teksten toe, alvorens meta-gegevens toe te wijzen.' => 'Metadata can only be assigned to tabs with texts. None of the tabs contains text. Please add texts before assigning metadata.',
        'meta-gegevens verwijderen' => 'delete metadata',
        'n.v.t.' => 'n.a.',
        'Naam auteur niet opgeslagen.' => 'Name author not saved.',
        'Naam auteur opgeslagen.' => 'Name author saved.',
        'Naam bestaat al voor' => 'Name already exists for',
        'Naam concept gewijzigd.' => 'Name concept updated.',
        'Naam niet opgeslagen.' => 'Name not saved.',
        'Naam niet opgeslagen' => 'Name not saved',
        'Naam opgeslagen.' => 'Name saved.',
        'Naam opgeslagen' => 'Name saved',
        'naam taxon concept direct aanpassen' => 'rename taxon concept directly',
        'Naam van:' => 'Name of:',
        'Naam verwijderd.' => 'Name deleted.',
        'naam' => 'name',
        'namen' => 'names',
        'Nederlandse "%s" bestaat al (naam wel opgeslagen):' => 'Common name "%s" already exists (name saved nonetheless):',
        'Nederlandse naam aangemaakt.' => 'Common name created.',
        'Nederlandse naam: expert niet opgeslagen.' => 'Common name: expert not saved.',
        'Nederlandse naam: organisatie niet opgeslagen.' => 'Common name: organisation not saved.',
        'Nederlandse naam: referentie niet opgeslagen.' => 'Common name: reference not saved.',
        'Niet alle data is opgeslagen!\nPagina toch verlaten?' => 'Not all data has been saved!\nLeave page anyway?',
        'niet filteren op voorkomensstatus' => 'do not filter on presence status',
        'niet gevestigde soorten' => 'non-established species',
        'niet-wetenschappelijke naam toevoegen' => 'add common name',
        'niets gevonden' => 'nothing found',
        'Nieuw concept aangemaakt.' => 'New concept created.',
        'nieuw concept' => 'new concept',
        'Nieuwe geaccepteerde naam aangemaakt.' => 'New accepted name created.',
        'Nieuwe naam aangemaakt.' => 'New name created.',
        'onderliggend taxon toevoegen aan "%s"' => 'add child taxon to "%s"',
        'ondersoort toevoegen aan "%s"' => 'add subspecies to "%s"',
        'ondersoort, forma, varietas, etc.' => 'subspecies, forma, varietas, etc.',
        'ook synoniemen van taxa exporteren' => 'export synonyms',
        'Opmerking niet opgeslagen.' => 'Remark not saved.',
        'Opmerking opgeslagen.' => 'Remark saved.',
        'opslaan' => 'save',
        'orde' => 'order',
        'Organisatie niet opgeslagen.' => 'Organisation not saved.',
        'Organisatie opgeslagen.' => 'Organisation saved.',
        'organisatie toevoegen' => 'add organisation',
        'organisatie' => 'organisation',
        'ouder' => 'parent',
        'Overzicht huidige meta-gegevens' => 'Overview of current metadata',
        'overzicht verwijderde taxa' => 'show deleted taxa',
        'paspoort bekijken (nieuw venster)' => 'view passport (in new window)',
        'paspoort' => 'passport',
        'paspoorten (meta-gegevens)' => 'passports (metadata)',
        'phylum' => 'phylum',
        'Populaire naam' => 'Common name',
        'Publicatie niet opgeslagen.' => 'Publication not saved.',
        'Publicatie opgeslagen.' => 'Publication saved.',
        'publicatie' => 'publication',
        'publiceren' => 'publish',
        'query parameters afdrukken' => 'print query parameters',
        'Rang niet opgeslagen.' => 'Rank not saved.',
        'Rang niet opgeslagen.' => 'Rank not saved.',
        'Rang opgeslagen.' => 'Rank saved.',
        'Rang opgeslagen.' => 'Rank saved.',
        'rang' => 'rank',
        'Referentie niet opgeslagen.' => 'Reference not saved.',
        'Referentie opgeslagen.' => 'Reference saved.',
        'referentie toevoegen' => 'add reference',
        'regeleinde' => 'line ending',
        'rijk' => 'kingdom',
        'samengestelde naam' => 'concatenated name',
        'scherm (opent in tab)' => 'screen (opens in tab)',
        'selecteer een taxon.' => 'select a taxon.',
        'selecteer tenminste één kolom.' => 'select at least one column.',
        'selecteer tenminste één rang.' => 'select at least one rank.',
        'Selectiecriteria' => 'Selection criteria',
        'soort toevoegen aan "%s"' => 'add species to "%s"',
        'soort' => 'species',
        'Sorteren' => 'Sorting',
        'Specifiek epithet niet opgeslagen.' => 'Specific epithet not saved.',
        'Specifiek epithet opgeslagen.' => 'Specific epithet saved.',
        'Standaardkolommen' => 'Standard columns',
        'status voorkomen kan alleen worden ingevuld voor soorten en lager.' => 'status presence can only be entered for species and lower.',
        'status' => 'status',
        'synoniem' => 'synonym',
        'synoniemen worden getoond in een eigen sectie, onder de reguliere export' => 'synonyms are included in a separate section, below the regular export',
        'Synoniemen' => 'Synonyms',
        'Taal niet opgeslagen.' => 'Language not saved.',
        'Taal opgeslagen.' => 'Language saved.',
        'tab' => 'tab',
        'tabbladen' => 'tabs',
        'Tabel bijgewerkt' => 'Table updated',
        'Taxa gemarkeerd als verwijderd' => 'Taxa marked as deleted',
        'Taxa van alle rangen tonen' => 'Show taxa of all ranks',
        'taxon bekijken in front-end (nieuw venster)' => 'view taxon in front-end (new window)',
        'taxon markeren als verwijderd' => 'mark taxon as deleted',
        'Taxon tonen' => 'Show taxon',
        'taxonomische ouders' => 'taxonomic parents',
        'Taxonomische rangen' => 'Taxonomic ranks',
        'Te exporteren gegevens' => 'Data to export',
        'tekens in veld' => 'characters in field',
        'terug' => 'back',
        'toevoegen aan' => 'add to',
        'Top van de te exporteren tak' => 'Top of branch to be exported',
        'Type niet opgeslagen.' => 'Type not saved.',
        'Type opgeslagen.' => 'Type saved.',
        'type' => 'type',
        'underscores in kolom-headers vervangen door spaties' => 'replace underscores in headers with spaces',
        'Uninomiaal niet opgeslagen.' => 'Uninomial not saved.',
        'Uninomiaal opgeslagen.' => 'Uninomial saved.',
        'uninomial' => 'uninomial',
        'Update naam taxon concept mislukt.' => 'Update name taxon concept failed.',
        'URL naar NSR-pagina concept' => 'Url to NSR page concept',
        'UTF8 naar UTF16 converteren' => 'convert UTF8 to UTF16',
        'UTF8-BOM toevoegen aan download-bestand' => 'add UTF8-BOM to file',
        'veldscheider' => 'separator',
        'Verouderde paspoorttitel' => 'Obsolete passport title',
        'Verouderde paspoorttitels' => 'Obsolete passport titles',
        'Verplaats voor de consistentie de tekst s.v.p. van het oude naar het nieuwe paspoort' => "For consistency's sake, please migrate the text from the old to the new passport:",
        'verwijderen' => 'delete',
        'verwijdering ongedaan maken' => 'undo deletion',
        'Via dit scherm kan de conceptnaam direct worden aangepast, zonder checks. Doet dit alleen in uitzonderingsgevallen waarin er een discrepantie bestaat tussen de conceptnaam en de geldige naam. De correcte manier om de naam van een concept aan te passen is via' => "This screen allows you to change the concept name directly, circumventing any checks. Use it only in exceptional cases, when there's a discrepancy between the concept name and the valid name. The correct way to change the concept name is through",
        'Via dit scherm kunnen alle delen van de geldige naam direct worden aangepast, zonder checks. Doet dit alleen in uitzonderingsgevallen waarin er een discrepantie bestaat tussen de conceptnaam en de geldige naam.' => "This screen allows you to change the concept name directly, circumventing any checks. Use it only in exceptional cases, when there's a discrepancy between the concept name and the synonym.",
        'voorkomen' => 'presence',
        'Voorkomensgegevens kunnen niet worden ingevuld voor hogere taxa. Concept niet opgeslagen.' => 'Presence data cannot be saved for higher taxa. Concept not saved.',
        'Voorkomensstatus niet opgeslagen.' => 'Presence data not saved.',
        'Voorkomensstatus opgeslagen.' => 'Presence data saved.',
        'Voorkomensstatus' => 'Presence status',
        'vul de volledige waarde voor "auteurschap" in, inclusief komma, jaartal en haakjes; het programma leidt de waarden voor auteur en jaar automatisch af.' => 'enter the complete value for authorship, including comma, year and brackets; the program automatically deduces the values for author and year.',
        'Vul eerst een soortsnaam in!' => 'First enter a species name!',
        'wetenschappelijke naam toevoegen' => 'add scientific name',
        'wetenschappelijke naam wordt automatische samengesteld.' => 'scientific name is concatenated automatically.',
        'wetenschappelijke naam' => 'scientific name',
        'worden, indien van toepassing, opgenomen als extra cellen aan het eind van iedere regel' => 'will be appended as extra cells if applicable',
        'wordt automatisch gegenereerd' => 'generated automatically',
        "'Auteurschap' wijkt af van 'auteur(s)' + 'jaar'." => "Authorship differs from 'author(s)' + 'year'.",
        "Aan dit concept is geen Nederlandse voorkeursnaam gekoppeld." => "No preferred common name linked to this concept.",
        "Aan dit concept is geen wetenschappelijke naam gekoppeld." => "No scientific name linked to this concept.",
        "Auteurschap" => "Authorship",
        "Combinatie naam, rang en ouder bestaat al" => "Combination of name, rank and parent already exists",
        "Derde naamdeel is niet ingevuld." => "Third name element has not been entered.",
        "Een %s kan geen derde naamdeel bevatten." => "A %s cannot contain a third name element.",
        "Een %s kan geen soortsnaam bevatten." => "A %s cannot contain a species name.",
        "Fout opgetreden: geen ID gevonden." => "An error occurred: could not locate an ID.",
        "Geen auteurschap. Concept niet opgeslagen." => "Authorship missing. Concept not saved.",
        "Geen auteurschap. Concept wel opgeslagen." => "No authorship. Concept saved nonetheless.",
        "Geen geldig jaar." => "Invalid year.",
        "Gemarkeerde taxa worden niet werkelijk verwijderd, maar zijn niet langer zichtbaar." => "Marked taxa are not really deleted, but they are no longer visible.",
        "Genus is niet ingevuld." => "Genus has not been entered.",
        "Onderstaande velden zijn niet ingevuld. Toch opslaan?" => "The fields below have not been entered. Save anyway?",
        "Open het te downloaden bestand niet direct in Excel, maar sla het eerst op en importeer het vervolgens in een Excel-leeg sheet via 'Data' > 'From text'." => "Do not open de file directly in Excel, but first save it and ubsequently open it in an empty Excel sheet through 'Data' > 'From text'.",
        "Samengevoegde naamdelen komen niet overeen met de naam." => "Composed name elements do not match the name.",
        "Soortsnaam is niet ingevuld." => "Species name has not been entered.",
        "Synoniem %s bestaat al; duplicaat synoniem aangemaakt." => "Synonym %s already exists; duplicate synonym created.",
        "Uninomial is niet ingevuld." => "Uninomial has not been entered.",
        "Voorkomen kan niet worden ingevuld voor hogere taxa." => "Presence cannot be entered for higher taxa.",
        "Voorkomen: expert is niet ingevuld." => "Presence: expert has not been entered.",
        "Voorkomen: organisatie is niet ingevuld." => "Presence: organisation has not been entered.",
        "Voorkomen: publicatie is niet ingevuld." => "Presence: publication has not been entered.",
        "Voorkomen: status is niet ingevuld." => "Presence: status has not been entered.",
        "Weet u het zeker?" => "Are you sure?",
        "Wetenschappelijke naam incompleet. Concept niet opgeslagen." => "Scientific name incomplete. Concept not saved.",
        "Wetenschappelijke naam kan geen derde naamdeel hebben. Concept niet opgeslagen." => "Scientific name cannot have a third name element. Concept not saved.",
        "Wetenschappelijke naam kan maar uit één deel bestaan. Concept niet opgeslagen." => "Scientific name can consist of only a single element. Concept not saved.",
        "Wetenschappelijke naam: expert" => "Scientific name: expert",
        "Wetenschappelijke naam: genus ontbreekt. Concept niet opgeslagen." => "Scientific name: genus is missing. Concept not saved.",
        "Wetenschappelijke naam: organisatie" => "Scientific name: organisation",
        "Wetenschappelijke naam: publicatie" => "Scientific name: publication",
        "Wetenschappelijke naam: uninomial ontbreekt. Concept niet opgeslagen." => "Scientific name: uninomial is missing. Concept not saved.",
        "Wilt u dit taxon markeren als verwijderd?" => "Do you want to mark this taxon as deleted?",
        "Wilt u dit taxon weer zichtbaar maken?" => "Do you want to make this taxon visible again?"
    );

*/