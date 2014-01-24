<?php

include_once ('Controller.php');

class NSRSearchController extends Controller
{

    public $usedModels = array(
		'taxa',
		'presence',
		'presence_labels'
	/*
		'content',
        'content_taxon', 
        'page_taxon', 
        'page_taxon_title', 
        'media_taxon',
        'media_descriptions_taxon',
		'synonym',
		'commonname',
		'literature',
		'content_free_module',
		'choice_content_keystep',
		'content_keystep',
		'choice_keystep',
		'keystep',
		'literature',
		'glossary',
		'glossary_media',
		'glossary_synonym',
		'matrix',
		'matrix_name',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'geodata_type_title',
		'occurrence_taxon',
		'content_introduction',
		'name_types',
		'presence',
		'page_taxon_title'
		*/
    );

    public $controllerPublicName = 'Search';

    public $usedHelpers = array(
    );

	public $cssToLoad = array();

	public $jsToLoad = array();

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
		/*
		$this->_minSearchLength = isset($this->controllerSettings['minSearchLength']) ? $this->controllerSettings['minSearchLength'] : $this->_minSearchLength;
		$this->_maxSearchLength = isset($this->controllerSettings['maxSearchLength']) ? $this->controllerSettings['maxSearchLength'] : $this->_maxSearchLength;
		$this->_excerptPreMatchLength = isset($this->controllerSettings['excerptPreMatchLength']) ? $this->controllerSettings['excerptPreMatchLength'] : 35;
		$this->_excerptPostMatchLength = isset($this->controllerSettings['excerptPostMatchLength']) ? $this->controllerSettings['excerptPostMatchLength'] : 35;
		$this->_excerptPrePostMatchString = isset($this->controllerSettings['excerptPrePostMatchString']) ? $this->controllerSettings['excerptPrePostMatchString'] : '...';

		$this->_searchResultSort = $this->getSetting('app_search_result_sort','alpha');
		*/
	
	}

    public function searchExtendedAction ()
    {
		
		
		/*
		if ($this->rHasVal('search')) {

			$_SESSION['app'][$this->spid()]['search'] = array(
				'search' => $this->requestData['search'],
				'modules' => $this->rHasVal('modules') ? $this->requestData['modules'] : null,
				'freeModules' => $this->rHasVal('freeModules') ? $this->requestData['freeModules'] : null
				);
				
			if ($this->validateSearchString($this->requestData['search'])) {
				
				if ($this->rHasVal('extended','1')) {

					$results =
						$this->doSearch(
							array(
								'search'=>$this->requestData['search'],
								'modules'=>$this->rHasVal('modules') ? $this->requestData['modules'] : null ,
								'freeModules'=>$this->rHasVal('freeModules') ? $this->requestData['freeModules'] : null,
								'extended'=>true
							)
						);

				} else {

					$search='"'.trim($this->requestData['search'],'"').'"';

					$results=
						$this->doSearch(
							array(
								'search'=>$search,
								'modules'=>array('species'),
								'freeModules'=>false,
								'extended'=>false
							)
						);

				}

				$this->addMessage(sprintf('Searched for <span class="searched-term">%s</span>',$this->requestData['search']));
				$this->smarty->assign('results',$results);

			} else {

				$this->addError(
					sprintf(
						$this->translate('Search string must be between %s and %s characters in length.'),
						$this->_minSearchLength,
						$this->_maxSearchLength
					)
				);

			}
			
		}

		$this->smarty->assign('CONSTANTS',
			array(
				'C_TAXA_SCI_NAMES'=>self::C_TAXA_SCI_NAMES,
				'C_TAXA_DESCRIPTIONS'=>self::C_TAXA_DESCRIPTIONS,
				'C_TAXA_SYNONYMS'=>self::C_TAXA_SYNONYMS,
				'C_TAXA_VERNACULARS'=>self::C_TAXA_VERNACULARS,
				'C_TAXA_ALL_NAMES'=>self::C_TAXA_ALL_NAMES,
				'C_SPECIES_MEDIA'=>self::C_SPECIES_MEDIA,
			)
		);
	
		$this->smarty->assign('modules',$this->getProjectModules(array('ignore' => MODCODE_MATRIXKEY)));
		$this->smarty->assign('minSearchLength',$this->controllerSettings['minSearchLength']);
		$this->smarty->assign('search',isset($_SESSION['app'][$this->spid()]['search']) ? $_SESSION['app'][$this->spid()]['search'] : null);

		*/
		
		$search=$this->requestData;
		$this->smarty->assign('search',$search);	
		
		if (
			!empty($search['taxon']) ||
			!empty($search['higherTaxon']) ||
			!empty($search['authorName']) ||
			!empty($search['presenceStatus']) ||
			!empty($search['images']) ||
			//!empty($search['externalDistribution']) ||
			//!empty($search['externalTrendChart']) ||
			!empty($search['hasBarcodes'])
			//!empty($search['hasNoBarcodes'])
		) {
	
			if (!empty($search['presenceStatus'])) {
				$d=array();
				foreach((array)$search['presenceStatus'] as $key=>$val) {
					if ($val=='on') $d[]=intval($key);
				}
				$search['presenceStatus']=$d;
			}
		
			$results=$this->doExtendedSearch($search);
		
			$this->smarty->assign('results',$results);
		
		}

		

		$this->smarty->assign('presence_statuses',$this->getPresenceStatuses());

        $this->printPage();
  
    }
	

	private function getPresenceStatuses()
	{
		
		return $this->models->Presence->freeQuery('
			select 
			
				_a.id,
				_a.sys_label,
				_a.settled_species,
				ifnull(_b.label,_a.sys_label) as label,
				_b.information,
				_b.information_short,
				_b.information_title,
				_b.index_label
			
			from lng_nsr_presence _a
			
			left join lng_nsr_presence_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.presence_id 
				and _b.language_id = '.$this->getCurrentLanguageId().'
			
			where
				_a.project_id='.$this->getCurrentProjectId().'
				and _b.index_label is not null
			order by 
				_b.index_label
		');
		
	}


	private function doExtendedSearch($search)
	{

		$d=$this->models->Taxon->freeQuery("
			select
				_a.taxon_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.name_author,
				_a.authorship_year,
				_a.reference,
				_a.reference_id,
				_a.expert,
				_a.expert_id,
				_a.organisation,
				_a.organisation_id,
				_b.nametype,
				_d.label as language_label,
				_e.taxon,
				_e.rank_id,
				_f.lower_taxon,
				_g.presence_id,
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				ifnull(_i.number_of_images,0) as number_of_images,
				ifnull(_j.number_of_barcodes,0) as number_of_barcodes,
				_k.name as dutch_name,
				_l.file_name as overview_image
			
			from %PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id
				
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id
				
			left join %PRE%labels_languages _d 
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id 
				and _d.label_language_id=".$this->getCurrentLanguageId()."
			
				left join %PRE%presence_taxa _g
				on _a.taxon_id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$this->getCurrentLanguageId()."
			
			left join
			(select project_id,taxon_id,count(*) as number_of_images from %PRE%media_taxon group by project_id,taxon_id) as _i
				on _a.taxon_id=_i.taxon_id
				and _i.project_id=_a.project_id
			
			left join
			(select project_id,taxon_id,count(*) as number_of_barcodes from %PRE%dna_barcodes group by project_id,taxon_id) as _j
				on _a.taxon_id=_j.taxon_id
				and _j.project_id=_a.project_id

			left join lng_nsr_names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join lng_nsr_media_taxon _l
				on _a.taxon_id = _l.taxon_id
				and _a.project_id = _l.project_id
				and _l.overview_image=1

			where _a.project_id =".$this->getCurrentProjectId()."

			".(!empty($search['taxon']) ? "and (_a.name like '%".mysql_real_escape_string($search['taxon'])."%' and _f.lower_taxon=1)" : "")."
			".(!empty($search['higherTaxon']) ? "and (_a.name like '%".mysql_real_escape_string($search['taxon'])."%' and _f.lower_taxon=0)" : "")."
			".(!empty($search['authorName']) ? "and _a.name_author like '%".mysql_real_escape_string($search['taxon'])."%'" : "")."
			".(!empty($search['presenceStatus']) ? "and _g.presence_id in (".implode(',',$search['presenceStatus']).")" : "" )."
			".(!empty($search['images']) ? "and number_of_images>0" : "")."
			".(!empty($search['hasBarcodes']) ? "and number_of_barcodes>0" : "")."
			order by ".($search['sort']=='preferredNameNl' ? "dutch_name" : "taxon" )."
			limit ".(!empty($search['limit']) ? intval($search['limit']) : "100" )
		);
	
		//q($this->models->Taxon->q());

		return $d;
		
	}



















    public function searchPicturesAction ()
    {

		if ($this->rHasVal('taxon')||$this->rHasVal('higherTaxon')||$this->rHasVal('photographer')||$this->rHasVal('validator'))
		{

			$_SESSION['app'][$this->spid()]['search_picture'] = array(
				'taxon' => $this->requestData['taxon'],
				'higherTaxon' => $this->requestData['higherTaxon'],
				'photographer' => $this->requestData['photographer'],
				'validator' => $this->requestData['validator']
				);

/*


/*
select _a.meta_data, _b.original_name, _c.taxon
from lng_nsr_media_meta _a

left join lng_nsr_media_taxon _b
	on _a.project_id=_b.project_id
	and _a.media_id=_b.id 

left join lng_nsr_taxa _c
	on _a.project_id=_c.project_id
	and _b.taxon_id=_c.id

	where
	_a.project_id=1 and
	(sys_label = 'beeldbankFotograaf' and meta_data like '%De Vos%') 
*/

		
//			if ($this->validateSearchString($this->requestData['search'])) {
	
				
//			}
			
		}

		$this->smarty->assign('minSearchLength',$this->controllerSettings['minSearchLength']);
		//$this->smarty->assign('search',isset($_SESSION['app'][$this->spid()]['search']) ? $_SESSION['app'][$this->spid()]['search'] : null);

        $this->printPage();
  
    }




}