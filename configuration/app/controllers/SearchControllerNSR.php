<?php

include_once ('Controller.php');
include_once ('SearchController.php');
include_once ('NSRFunctionsController.php');
include_once ('ModuleSettingsReaderController.php');

class SearchControllerNSR extends SearchController
{
	private $_suggestionListItemMax=50;
	private $_resPicsPerPage=12;
	private $_resSpeciesPerPage=50;
	private $_nameTypeIds;
	private $conceptIdPrefix='tn.nlsr.concept/';
	private $httpHost;

	private $_operators;

	public $csvExportSettings=array(
		'field-sep'=>"\t",
		'field-enclose'=>'"',
		'line-end'=>"\n",
		'file-extension'=>".csv"
	);

    public $usedModels = array(
		'names',
		'taxa',
		'presence',
		'presence_labels',
		'media_meta',
		'media_taxon',
		'name_types',
		'traits_groups',
		'traits_traits',
		'traits_values',
		'traits_taxon_values',
		'traits_taxon_freevalues',
		'tab_order'
    );


    public $modelNameOverride = 'SearchNSRModel';
    public $controllerPublicName = 'Search';

    public $usedHelpers = array();

	public $cssToLoad = ['search.css', 'traits.css'];

	public $jsToLoad = ['all' => ['main.js']];
	//['all' => ['traits.js', 'main.js']];

	private $cTabs=[
		'CTAB_NAMES'=>['id'=>-1,'title'=>'Nomenclature'],
		'CTAB_MEDIA'=>['id'=>-2,'title'=>'Media'],
		'CTAB_CLASSIFICATION'=>['id'=>-3,'title'=>'Classification'],
		'CTAB_TAXON_LIST'=>['id'=>-4,'title'=>'Child taxa list'],
		'CTAB_LITERATURE'=>['id'=>-5,'title'=>'Literature'],
		'CTAB_DNA_BARCODES'=>['id'=>-6,'title'=>'DNA barcodes'],
		'CTAB_DICH_KEY_LINKS'=>['id'=>-7,'title'=>'Key links'],
//		'CTAB_NOMENCLATURE'=>['id'=>-8,'title'=>'Nomenclature'],
		'CTAB_PRESENCE_STATUS'=>['id'=>-9,'title'=>'Presence status'],
	];
	
    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->setOperators();

        $this->NSRFunctions=new NSRFunctionsController;
		$this->moduleSettings=new ModuleSettingsReaderController;

		$this->_search_presence_help_url = $this->moduleSettings->getModuleSetting( array('setting'=>'url_help_search_presence','module'=>'utilities') );
		$this->_taxon_base_url_images_main = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_main','module'=>'species' ) );
		$this->_taxon_base_url_images_thumb = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb','module'=>'species' ) );
		$this->_taxon_base_url_images_overview = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_overview','module'=>'species' ) );
		$this->_taxon_base_url_images_thumb_s = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb_s','module'=>'species' ) );

		$this->smarty->assign( 'taxon_base_url_images_main',$this->_taxon_base_url_images_main );
		$this->smarty->assign( 'taxon_base_url_images_thumb',$this->_taxon_base_url_images_thumb );
		$this->smarty->assign( 'taxon_base_url_images_overview',$this->_taxon_base_url_images_overview );
		$this->smarty->assign( 'taxon_base_url_images_thumb_s',$this->_taxon_base_url_images_thumb_s );

		$this->_show_presence_in_results = $this->moduleSettings->getModuleSetting( [ 'setting'=>'show_presence_in_results','module'=>'utilities','subst'=>1 ] )==1;
		$this->_show_all_preferred_names_in_results = $this->moduleSettings->getModuleSetting( [ 'setting'=>'show_all_preferred_names_in_results','module'=>'utilities','subst'=>1 ] )==1;
		$this->_show_taxon_rank_in_results = $this->moduleSettings->getModuleSetting( [ 'setting'=>'show_taxon_rank_in_results','module'=>'utilities','subst'=>1 ] )==1;

		$this->_search_filter_presence = $this->moduleSettings->getModuleSetting( [ 'setting'=>'search_filter_presence','module'=>'utilities','subst'=>0 ] )==1;
		$this->_search_filter_multimedia = $this->moduleSettings->getModuleSetting( [ 'setting'=>'search_filter_multimedia','module'=>'utilities','subst'=>0 ] )==1;
		$this->_search_filter_dna_barcodes = $this->moduleSettings->getModuleSetting( [ 'setting'=>'search_filter_dna_barcodes','module'=>'utilities','subst'=>0 ] )==1;

		$this->smarty->assign( 'show_presence_in_results',$this->_show_presence_in_results );
		$this->smarty->assign( 'show_all_preferred_names_in_results',$this->_show_all_preferred_names_in_results );
		$this->smarty->assign( 'show_taxon_rank_in_results',$this->_show_taxon_rank_in_results );
		$this->smarty->assign( 'search_filter_presence',$this->_search_filter_presence );
		$this->smarty->assign( 'search_filter_multimedia',$this->_search_filter_multimedia );
		$this->smarty->assign( 'search_filter_dna_barcodes',$this->_search_filter_dna_barcodes );
		$this->smarty->assign( 'show_nsr_specific_filters',$this->moduleSettings->getModuleSetting( [ 'setting'=>'show_nsr_specific_filters','module'=>'utilities','subst'=>0 ] )==1);

		$order=$this->models->TabOrder->_get([
			'id'=>['project_id' => $this->getCurrentProjectId()],
			'fieldAsIndex'=>'page_id'
		]);

        foreach((array)$this->cTabs as $key=>$page)
		{
			$this->cTabs[$key]['suppress']=isset($order[$page['id']]) ? $order[$page['id']]['suppress']==1 : false;
        }
		
		$this->smarty->assign( 'automatic_tabs', $this->cTabs );

        /** @setting: 'db_lc_time_names' */
		$this->models->Taxa->freeQuery("SET lc_time_names = '".$this->moduleSettings->getGeneralSetting( array('setting'=>'db_lc_time_names','subst'=>'nl_NL') )."'");

		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
		

		$this->models->SearchNSRModel->setNameTypeIds($this->_nameTypeIds);
		$this->setRobotsDirective( ["index","nofollow"] );
		
		$this->httpHost = $this->getHttpHost();

    }

    public function searchAction()
    {
		$search=null;
		
		if ($this->rHasVal('search'))
		{
			$search=$this->rGetAll();

			$search['search_original']=$search['search'];
			$search['search']=$this->removeSearchNoise($search['search']);

			$results=$this->doSearch($search);

			$search['search']=htmlspecialchars($search['search_original']);

			$this->smarty->assign('search', $search);
			$this->smarty->assign('results',$results);
		}

		$searchType = $this->rHasVar('type') ? $this->rGetVal('type') : null;

		if ($this->rHasVal('action','export'))
		{
			$search['limit']=1000;
			$template='export_search';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);
			$this->smarty->assign('url_taxon_detail', $this->getProtocol() . $this->httpHost.'/nsr/concept/');
			$this->downloadHeaders(
				array(
					'mime'=>'text/csv',
					'charset'=>'utf-8',
					'filename'=>'NSR-export-'.date('Ymd-his').$this->csvExportSettings['file-extension'])
					);
		}
		else
		{
			$template=null;
			$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
			$this->smarty->assign('type',$searchType);
			$this->smarty->assign('searchHR',$this->makeReadableQueryString());
			$this->smarty->assign('url_taxon_detail', $this->getProtocol() . $this->httpHost.'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
		}

        $this->printPage($template);
	}

    public function searchExtendedAction()
    {
		$search=$this->rGetAll();
		$this->smarty->assign('url_taxon_detail', $this->getProtocol() . $this->httpHost.'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
		
		if ($this->rHasVal('action','export')) {
			$search['limit']=5000;
			$template='export_search_extended';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);

			$this->downloadHeaders(
				array(
					'mime'=>'text/csv',
					'charset'=>'utf-8',
					'filename'=>'Export-'.date('Ymd-his').$this->csvExportSettings['file-extension'])
					);
		} else {
			$this->smarty->assign('search',$search);
			$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
			$this->smarty->assign('presence_statuses',$this->getPresenceStatuses());
			$template=null;
		}

		$this->traitGroupsToInclude = $this->getTraitGroups();

		if (count((array)$this->traitGroupsToInclude)>0) {

		    $search['traits']=$this->rHasVal('traits') ? json_decode(urldecode($search['traits']),true) : null;
			$search['trait_group']=$this->rHasVal('trait_group') ? $search['trait_group'] : null;

			$traits=array();
			foreach((array)$this->traitGroupsToInclude as $val)
			{			    
			    $traits = $traits + $this->getTraits($val['id']);
				if ($val['group_id']==$search['trait_group']) {
				    $search['trait_group_name']=$val['group_name'];
				}
			}

            $this->smarty->assign('trait_group_name', isset($search['trait_group_name']) ? $search['trait_group_name'] : null );
			$this->smarty->assign('operators',$this->_operators);
			$this->smarty->assign('traits',$traits);
			$this->smarty->assign('searchTraitsHR',
				$this->makeReadableTraitString(array(
					'traits'=>$traits,
					'trait_group'=>isset($search['trait_group']) ? $search['trait_group'] : null,
					'search'=>isset($search['traits']) ? $search['traits'] : null
				)
			));
		}

		$this->smarty->assign('searchHR',$this->makeReadableQueryString());
		$this->smarty->assign('results',$this->doExtendedSearch($search));
		$this->smarty->assign('search_presence_help_url',$this->_search_presence_help_url);

        $this->printPage($template);
    }

    public function searchPicturesAction()
    {
		$search=$this->requestData;

		if ($this->rHasVal('action','export')) {
			$search['limit']=1000;
			$template='export_search_pictures';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);
			$this->smarty->assign('url_taxon_detail', $this->getProtocol() . $this->httpHost.'/nsr/concept/');
			$this->downloadHeaders(
				array(
					'mime'=>'text/csv',
					'charset'=>'utf-8',
					'filename'=>'NSR-export-'.date('Ymd-his').$this->csvExportSettings['file-extension'])
					);
		} else {
			$template=null;
			$this->smarty->assign('photographers',$this->getPhotographersPictureCount($search));
			$this->smarty->assign('validators',$this->getValidatorPictureCount($search));
			$this->smarty->assign('searchHR',$this->makeReadableQueryString());
			$this->smarty->assign('url_taxon_detail',"//". $this->httpHost.'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
			$this->smarty->assign('imageExport',true);
		}

		if ($this->rHasVal('show','photographers')) {
			$this->smarty->assign('show','photographers');
			$search['limit']='*';
		}

		$results = $this->doPictureSearch( $search );

        $this->smarty->assign('photographer_url',$this->setPicturesResultString($search, 'photographer'));
        $this->smarty->assign('validator_url',$this->setPicturesResultString($search, 'validator'));
		$this->smarty->assign('search',$search);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
		$this->smarty->assign('results',$results);

        $this->printPage($template);
    }

    private function setPicturesResultString ($p, $active = '')
    {
        if (isset($p[$active])) {
            unset($p[$active]);
        }
        $search = !empty($p) ? http_build_query($p) . '&' : '';
        return "//". $this->httpHost . '/linnaeus_ng/' . $this->getAppname() . '/views/search/nsr_search_pictures.php?' .
            $search . $active . '=';
    }

    public function recentPicturesAction()
    {
		$search=$this->rGetAll();
		$results = $this->doPictureSearch($search);
		$this->smarty->assign('search',$search);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
		$this->smarty->assign('results',$results);
		$this->smarty->assign('show','photographers');
		$this->smarty->assign('photographers',$this->getPhotographersPictureCount());
		$this->smarty->assign('validators',$this->getValidatorPictureCount());
        $this->printPage();
    }

    public function photographersAction()
    {
		$this->smarty->assign('validators',$this->getValidatorPictureCount());
		$this->smarty->assign('photographers',$this->getPhotographersPictureCount(array('limit'=>'*')));
        $this->printPage();
    }

    public function validatorsAction()
    {
		$this->smarty->assign('photographers',$this->getPhotographersPictureCount());
		$this->smarty->assign('validators',$this->getValidatorPictureCount(array('limit'=>'*')));
        $this->printPage();
    }

    public function ajaxInterfaceAction()
    {

        if (!$this->rHasVal('action')) return;

		/*
        if ($this->rHasVal('action','group_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsGroup($this->rGetAll())));
        }
		else
		*/
        if ($this->rHasVal('action','author_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsAuthor($this->rGetAll())));
        }
		else
        if ($this->rHasVal('action','photographer_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsPhotographer($this->rGetAll())));
        }
		else
        if ($this->rHasVal('action','validator_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsValidator($this->rGetAll())));
        } 
		else
        if ($this->rHasVal('action','name_suggestions') || $this->rHasVal('action','group_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsName($this->rGetAll())));
        }

        $this->printPage();

    }

	private function getPresenceStatuses()
	{
		return
			$this->models->SearchNSRModel->getPresenceStatuses(array(
				"language_id"=>$this->getCurrentLanguageId(),
				"project_id"=>$this->getCurrentProjectId()
			));
	}

	private function getTaxonOverviewImage( $taxon_id )
	{
		return
			$this->models->SearchNSRModel->getTaxonOverviewImage(array(
				"project_id"=>$this->getCurrentProjectId(),
				"taxon_id"=>$taxon_id
			));
	}

	private function doSearch($p)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resSpeciesPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : null;

		$search=trim($search);

		if (empty($search))
			return null;

		$d=$this->models->SearchNSRModel->doSearch(array(
			"search"=>$search,
			"nsr_id_prefix"=>$this->conceptIdPrefix,
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset
		));

		$data=$d['data'];
		$count=$d['count'];

		if ( $this->_show_all_preferred_names_in_results )
		{
			foreach((array)$data as $key=>$val)
			{
				$data[$key]['common_names']=
					$this->models->Names->_get( [ 'id' => [
						'project_id'=>$this->getCurrentProjectId(),
						'taxon_id'=>$val['taxon_id'],
						'type_id'=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']
					], 'columns'=>'name' ] );
			}
		}

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
			$data[$key]['taxon_download']=html_entity_decode(strip_tags($data[$key]['taxon']));
			$data[$key]['overview_image']=$this->getTaxonOverviewImage($val['taxon_id']);
		}

		return array('count'=>$count,'data'=>$data,'perpage'=>$this->_resSpeciesPerPage);

	}

	private function doExtendedSearch($params)
	{

		$suggestionsGroup=null;

		if (!empty($params['group_id'])) {
			$suggestionsGroup=$this->getSuggestionsGroup(array('id'=>(int)trim($params['group_id']),'match'=>'id'));
			if (empty($suggestionsGroup)) {
				$ancestor_id=$params['group_id'];
			} else {
				$ancestor_id=$suggestionsGroup ? $suggestionsGroup[0]['id'] : null;
			}
		} else if (!empty($params['group'])) {
			$suggestionsGroup=$this->getSuggestionsGroup(array('search'=>$params['group'],'match'=>'exact'));
			$ancestor_id=$suggestionsGroup ? $suggestionsGroup[0]['id'] : null;
		}

		$images_on=(!empty($params['images_on']) && $params['images_on']=='on' ? true : null);
		$images_off=(!empty($params['images_off']) && $params['images_off']=='on' ? true : null);
		$images=!is_null($images_on) || !is_null($images_off);

		$distribution_on=(!empty($params['distribution_on']) && $params['distribution_on']=='on' ? true : null);
		$distribution_off=(!empty($params['distribution_off']) && $params['distribution_off']=='on' ? true : null);
		$distribution=!is_null($distribution_on) || !is_null($distribution_off);

		$trend_on=(!empty($params['trend_on']) && $params['trend_on']=='on' ? true : null);
		$trend_off=(!empty($params['trend_off']) && $params['trend_off']=='on' ? true : null);
		$trend=!is_null($trend_on) || !is_null($trend_off);

		$dna=(!empty($params['dna']) || !empty($params['dna_insuff']));
		$dna_insuff=!empty($params['dna_insuff']);
		$traits=isset($params['traits']) ? $params['traits'] : null;
		$trait_group=isset($params['trait_group']) ? $params['trait_group'] : null;
		$auth=!empty($params['author']) ? $params['author'] : null;

		$pres=null;
		if (!empty($params['presence']))
		{
			$pres=array();
			foreach((array)$params['presence'] as $key=> $val)
			{
				if ($val=='on') $pres[]= (int)$key;
			}
		}

		$limit=!empty($params['limit']) ? $params['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($params['page']) ? $params['page']-1 : 0) * $this->_resSpeciesPerPage;
		$sort=!empty($params['sort']) ? $params['sort'] : null;
		$just_species=!empty($params['just_species']) ? $params['just_species'] : false;

		$suggestionsGroup=$this->models->SearchNSRModel->doExtendedSearch(array(
			"images"=>$images,
			"images_on"=>$images_on,
			"images_off"=>$images_off,
			"auth"=>$auth,
			"trend"=>$trend,
			"trend_on"=>$trend_on,
			"trend_off"=>$trend_off,
			"distribution"=>$distribution ,
			"distribution_on"=>$distribution_on,
			"distribution_off"=>$distribution_off,
			"dna"=>$dna,
			"dna_insuff"=>$dna_insuff,
			"nsr_id_prefix"=>$this->conceptIdPrefix,
			"traits"=>$traits,
			"trait_group"=>$trait_group,
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"specific_rank"=>$just_species ? SPECIES_RANK_ID : null,
			"ancestor_id"=>isset($ancestor_id) ? $ancestor_id : null,
			"presence"=>$pres,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset,
			"operators"=>$this->_operators
		));

		$data=$suggestionsGroup['data'];
		$count=$suggestionsGroup['count'];

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
			$data[$key]['taxon_download']=html_entity_decode(strip_tags($data[$key]['taxon']));
			$data[$key]['overview_image']=$this->getTaxonOverviewImage($val['taxon_id']);
			
			if ( $this->_show_all_preferred_names_in_results ) {
				$data[$key]['common_names']=
					$this->models->Names->_get( [ 'id' => [
						'project_id'=>$this->getCurrentProjectId(),
						'taxon_id'=>$val['taxon_id'],
						'type_id'=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']
					], 'columns'=>'name' ] );
			}
		}

		return
			array(
				'count'=>$count,
				'data'=>$data,
				'perpage'=>$this->_resSpeciesPerPage
			);
	}

	private function getPhotographersPictureCount($p=null)
	{
		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);

		$photographers=$this->models->SearchNSRModel->getPhotographersPictureCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));

		if (!empty($limit) && $limit<count((array)$photographers))
		{
			$photographers=array_slice($photographers,0,$limit);
		}

		return $photographers;
	}

	private function getValidatorPictureCount($p=null)		
	{
		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);
		
		$validators= $this->models->SearchNSRModel->getValidatorPictureCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));
		
		if (!empty($limit) && $limit<count((array)$validators))
		{
			$validators=array_slice($validators,0,$limit);
		}

		return $validators;
	}

	private function doPictureSearch( $p )
	{
		$group_id=null;
		$name_id=null;
		$name=null;

		if (empty($p['group_id']) && !empty($p['group']))
		{
			$d=$this->getSuggestionsGroup(array('search'=>$p['group'],'match'=>'exact'));
			if ($d) $group_id=$d[0];
		} 
		else
		if (!empty($p['group_id']))
		{
			$group_id= (int)$p['group_id'];
		}

		if (!empty($p['name']))
		{
			$name=$p['name'];
		}

		if (!empty($p['name_id']))
		{
			$name_id= (int)$p['name_id'];
		}

		if ( !empty($name) && !empty($name_id) )
		{
			unset($name);
		}

		$photographer=!empty($p['photographer']) ? $p['photographer'] : null;
		$validator=!empty($p['validator']) ? $p['validator'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : null;

		$d=$this->models->SearchNSRModel->doPictureSearch(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"group_id"=>$group_id,
			"name"=>isset($name) ? $name : null,
			"type_id_valid"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"photographer"=>$photographer,
			"validator"=>$validator,
			"project_id"=>$this->getCurrentProjectId(),
			"name_id"=>$name_id,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset
		));

		$data=$d['data'];
		$count=$d['count'];

		foreach((array)$data as $key=>$val)
		{
		    $data[$key]['image'] = implode("/", array_map("rawurlencode", explode("/", $val['image'])));
		    $data[$key]['thumb'] = implode("/", array_map("rawurlencode", explode("/", $val['thumb'])));
		    $data[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name' => $val['taxon'], 'base_rank_id' => $val['base_rank_id'], 'taxon_id' => $val['taxon_id'] ] );
			$data[$key]['validName']=$this->addHybridMarkerAndInfixes( [ 'name' => $val['validName'], 'base_rank_id' => $val['base_rank_id'], 'taxon_id' => $val['taxon_id'] ] );
			$data[$key][0] = $this->NSRFunctions->formatPictureResults( [$val] );

			$names=$this->models->Names->_get(array("id"=>
				array(
					"project_id" => $this->getCurrentProjectId(),
					"taxon_id" => $val["taxon_id"]
				)
			));

			foreach((array)$names as $n)
			{
				if ( $n['type_id']==$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'] && $n['language_id']==$this->getCurrentLanguageId() )
				{
					$data[$key]['common_name']=$n['name'];
				}
				else
				if ( $n['type_id']==$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] && $n['language_id']==LANGUAGE_ID_SCIENTIFIC )
				{
					$data[$key]['uninomial']=$n['uninomial'];
					$data[$key]['specific_epithet']=$n['specific_epithet'];
					$data[$key]['infra_specific_epithet']=$n['infra_specific_epithet'];
					$data[$key]['authorship']=$n['authorship'];
					$data[$key]['nomen']=
						$this->addHybridMarkerAndInfixes( [ 'name'=> trim(str_replace($n['authorship'],'',$n['name'])),'base_rank_id'=>$val['base_rank_id'],'taxon_id' => $val['taxon_id'] ] );
					$data[$key]['name']=
						$this->addHybridMarkerAndInfixes( [
							'name'=>
								(empty($n['uninomial']) ? '' : $n['uninomial'] . ' ') .
								(empty($n['specific_epithet']) ? '' : $n['specific_epithet'] . ' ') .
								(empty($n['infra_specific_epithet']) ? '' : $n['infra_specific_epithet']),
							'base_rank_id'=> $val['base_rank_id'],
							'taxon_id' => $val['taxon_id']
						] );
				}
			}
		}
		
		return
			array(
				'count'=>$count,
				'data'=> $this->NSRFunctions->formatPictureResults( $data ),
				'perpage'=>$this->_resPicsPerPage
			);

	}

	private function getSuggestionsGroup( $p )
	{
		return $this->models->SearchNSRModel->getSuggestionsGroup(array(
			"match"=>isset($p['match']) ? $p['match'] : "like",
			"search"=>isset($p['match']) && $p['match']=='id'? $p['id'] : $p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"limit"=>$this->_suggestionListItemMax,
			"taxon_id"=>isset($p['id']) ? $p['id'] : null,
			"restrict_language"=>false
		));
	}

	private function getSuggestionsAuthor( $p )
	{
		return $this->models->SearchNSRModel->getSuggestionsAuthor(array(
			"match"=>$p['match'],
			"search"=>$p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"limit"=>$this->_suggestionListItemMax
		));
	}

	private function getSuggestionsValidator( $p )
	{
		return $this->models->SearchNSRModel->getSuggestionsValidator(array(
			"match"=>$p['match'],
			"search"=>$p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"limit"=>$this->_suggestionListItemMax
		));
	}

	private function getSuggestionsPhotographer( $p )
	{
		return $this->models->SearchNSRModel->getSuggestionsPhotographer(array(
			"match"=>$p['match'],
			"search"=>$p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"limit"=>$this->_suggestionListItemMax
		));
	}

	private function getSuggestionsName( $p )
	{
		$search=$this->removeSearchNoise($p['search']);

		$data=$this->models->SearchNSRModel->getSuggestionsName(array(
			"search"=>$search,
			"order"=>isset($p['order']) ? $p['order'] : null,
			"project_id"=>$this->getCurrentProjectId(),
			"limit"=>$this->_suggestionListItemMax,
			"language_id"=>$this->getCurrentLanguageId(),
			"restrict_language"=>false
		));

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['label']=$this->addHybridMarkerAndInfixes( [ 'name'=> $val['label'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
			$data[$key]['scientific_name']=$this->addHybridMarkerAndInfixes( [ 'name'=> $val['scientific_name'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
			$data[$key]['nomen']=$this->addHybridMarkerAndInfixes( [ 'name'=> $val['nomen'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
		}
		
		return $data;
	}

	private function reconstructQueryString( $p )
	{
		$search=isset($p['search']) ? $p['search'] : null;
		$ignore=isset($p['ignore']) ? $p['ignore'] : null;

		foreach((array)$ignore as $val)
		{
			unset($search[$val]);
		}

		return (http_build_query((array)$search).'&');

		if (empty($search)) return;

		$querystring=null;

		foreach((array)$this->rGetAll() as $key=>$val)
		{
			if (in_array($key,$ignore)) continue;

			if (is_array($val))
			{
				foreach((array)$val as $k2=>$v2)
				{
					if (is_array($v2))
					{
						foreach((array)$val as $k3=>$v3)
						{
							$querystring.=$key.'['.$k2.']['.$k3.']='.$v3.'&';
						}

					} else {
						$querystring.=$key.'['.$k2.']='.$v2.'&';
					}
				}

			} else {
				$querystring.=$key.'='.$val.'&';
			}
		}

		return htmlspecialchars($querystring);
	}

	private function makeReadableQueryString()
	{
		$querystring=null;

		if ($this->rHasVal('group')) $querystring.='Soortgroep="'.$this->rGetVal('group').'"; ';
		if ($this->rHasVal('author')) $querystring.='Auteur="'.$this->rGetVal('author').'"; ';

		if ($this->rHasVal('presence'))
		{
			$statuses=$this->getPresenceStatuses();
			$querystring.=$this->translate('Status voorkomen=');

			foreach((array)$this->rGetVal('presence') as $key=>$val)
			{
				$querystring.=$statuses[$key]['index_label'].',';
			}
			$querystring=rtrim($querystring,',').'; ';
		}

		if ($this->rHasVal('images_on','on')) $querystring.=$this->translate('Met foto\'s; ');
		if ($this->rHasVal('images_off','on')) $querystring.=$this->translate('Zonder foto\'s; ');
		if ($this->rHasVal('distribution_on','on')) $querystring.=$this->translate('Met verspreidingskaart; ');
		if ($this->rHasVal('distribution_off','on')) $querystring.=$this->translate('Zonder verspreidingskaart; ');
		if ($this->rHasVal('trend_on','on')) $querystring.=$this->translate('Met trendgrafiek; ');
		if ($this->rHasVal('trend_off','on')) $querystring.=$this->translate('Zonder trendgrafiek; ');
		if ($this->rHasVal('dna','on')) $querystring.=$this->translate('Met DNA-exemplaren verzameld; ');
		if ($this->rHasVal('dna_insuff','on')) $querystring.=$this->translate('Met nog DNA-exemplaren te verzamelen; ');

		return trim($querystring);
	}

	private function makeReadableTraitString( $p )
	{
		$traits=isset($p['traits']) ? $p['traits'] : null;
		$search=isset($p['search']) ? $p['search'] : null;
		$trait_group=isset($p['trait_group']) ? $p['trait_group'] : null;

		$str=array();

		if (isset($search))
		{
			foreach((array)$traits as $trait)
			{
				foreach((array)$trait['data'] as $data)
				{
					foreach((array)$search as $val)
					{
						if (!empty($val['valueid']))
						{
							foreach((array)$data['values'] as $value)
							{
								if ($val['valueid']==$value['id'])
								{
									$str[$data['name']][]=
										$value["string_value"].
										$value["numerical_value"].
										$value["numerical_value_end"].
										$value["date"].
										$value["date_end"];
								}
							}
						}
						else
						{
							if (isset($val['traitid']) && $val['traitid']==$data['id'])
							{
								$str[$data['name']][]=
									(!empty($val["operatorlabel"]) ? $val["operatorlabel"]." " : null).
									(!empty($val["valuetext"]) ? $val["valuetext"]." " : null).
									(!empty($val["valuetext"]) && !empty($val["valuetext2"]) ? "& " : null).
									(!empty($val["valuetext2"]) ? $val["valuetext2"] : null);
							}
						}
					}
				}
			}
		}

		array_walk($str,function(&$a){ $a=is_array($a) ? implode(",",$a) : $a; });
		array_walk($str,function(&$a,$key){ $a=$key.'='.trim($a); });

		$str=implode("; ",$str);

		if (!empty($trait_group))
		{
			$str=($str ? $str.';' : '').$traits[$trait_group]['name'].'=*; ';
		}

		return $str;
	}

	private function downloadHeaders( $p )
	{
		$filename=isset($p['filename']) ? $p['filename'] : null;
		$mime=isset($p['mime']) ? $p['mime'] : null;
		$charset=isset($p['charset']) ? $p['charset'] : null;

		header('Content-Description: File Transfer');
		header('Content-type: '.(!empty($mime) ? $mime : '').'; '.(!empty($charset) ? 'charset='.$charset : ''));
		header('Content-Disposition: attachment; '.(!empty($filename) ? 'filename='.$filename : ''));
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');

		if (!empty($charset) && strtolower($charset)=='utf-8')
		{
			//http://stackoverflow.com/questions/5601904/encoding-a-string-as-utf-8-with-bom-in-php
			echo chr(239).chr(187).chr(191);
		}
	}

	private function getTraitGroups()
	{
	    return $this->models->TraitsGroups->freeQuery("
			select
				_grp.id,
				_grp.parent_id,
				_grp.sysname,
				ifnull(_grp_b.translation,_grp.sysname) as group_name,
				_grp_c.translation as group_description,
				_grp.id as group_id,
				_grp.show_order as group_order

			from
				%PRE%traits_groups _grp

			left join
				%PRE%text_translations _grp_b
				on _grp.project_id=_grp_b.project_id
				and _grp.name_tid=_grp_b.text_id
				and _grp_b.language_id=". $this->getCurrentLanguageId() ."

			left join
				%PRE%text_translations _grp_c
				on _grp.project_id=_grp_c.project_id
				and _grp.description_tid=_grp_c.text_id
				and _grp_c.language_id=". $this->getCurrentLanguageId() ."

			where
				_grp.project_id=". $this->getCurrentProjectId()."
			order by _grp.show_order, _grp_b.translation
		");
	}

	private function getTraits( $group )
	{
		if ( empty( $group ) ) return;

		$r=$this->models->SearchNSRModel->getTraits(array(
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"group"=>$group
		));

		$data=array();

		foreach((array)$r as $key=>$trait)
		{
			$trait['values']=$this->getTraitgroupTraitValues($trait['id']);
			$data[$trait['group_id']]['name']=$trait['group_name'];
			$data[$trait['group_id']]['description']=$trait['group_description'];
			$data[$trait['group_id']]['all_link_text']=$trait['group_all_link_text'];
			$data[$trait['group_id']]['show_show_all_link']=$trait['group_show_show_all_link'];
			$data[$trait['group_id']]['help_link_url']=$trait['group_help_link_url'];
            $data[$trait['group_id']]['group_id']=$trait['group_id'];
            $data[$trait['group_id']]['group_order']=$trait['group_order'];
            $data[$trait['group_id']]['data'][]=$trait;
		}

		return $data;
	}

	private function getTraitgroupTrait( $trait_id )
	{
		if (empty($trait_id)) return;

		return $this->models->SearchNSRModel->getTraitgroupTrait(array(
			"project_id"=>$this->getCurrentProjectId(),
			"trait_id"=>$trait_id
		));

	}

	private function getTraitgroupTraitValues( $trait_id )
	{
		if (empty($trait_id)) return;

		$r=$this->models->SearchNSRModel->getTraitgroupTraitValues(array(
			"language_id"=> $this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"trait_id"=>$trait_id
		));

		foreach((array)$r as $key=>$val)
		{
			if ($val['allow_fractures']!='1' && (!empty($val['numerical_value']) || !empty($val['numerical_value_end'])))
			{
				if (!empty($val['numerical_value']))
				{
					$r[$key]['numerical_value']=round($val['numerical_value'],0,PHP_ROUND_HALF_DOWN);
				}
				if (!empty($val['numerical_value_end']))
				{
					$r[$key]['numerical_value_end']=round($val['numerical_value_end'],0,PHP_ROUND_HALF_DOWN);
				}
			}
			else
			if (!empty($val['date']) || !empty($val['date_end'])  && !empty($val['date_format_format']))
			{
			    // @TODO: Check the expression above. May be unclear what it actually does.

				if (!empty($val['date']))
				{
					$r[$key]['date']=$this->formatDbDate($val['date'],$val['date_format_format']);
				}
				if (!empty($val['date_end']))
				{
					$r[$key]['date_end']=$this->formatDbDate($val['date_end'],$val['date_format_format']);
				}
			}
		}

		return $r;

	}

	private function getTaxonTraitValues( $taxon_id )
	{
		if (empty($taxon_id)) return;

		$t1=$this->models->TraitsTaxonValues->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$taxon_id
			)
		));

		$t2=$this->models->SearchNSRModel->getTaxonTraitFreeValues(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$taxon_id
		));

		return
			array(
				'values'=>$t1,
				'freevalues'=>$t2
			);
	}

	private function formatDbDate( $date, $format )
	{
		if ($format=="Y")
		{
			$d=date_parse($date);
			if ($d['month']==0) $d['month']=1;
			if ($d['day']==0) $d['day']=1;
			$date=$d['year']."-".$d['month']."-".$d['day'];
		}
		return is_null($date) ? null : date_format(date_create($date),$format);
	}

	private function setOperators ()
    {
        $this->_operators = array(
            '=='=>array('label'=>$this->translate('equals'),'range'=>false),
            '!='=>array('label'=>$this->translate('does not equal'),'range'=>false),
            '>'=>array('label'=>$this->translate('is greater than'),'range'=>false),
            '<'=>array('label'=>$this->translate('is less than'),'range'=>false),
            '>='=>array('label'=>$this->translate('is greater than or equal to'),'range'=>false),
            '=<'=>array('label'=>$this->translate('is less than or equal to'),'range'=>false),
            'BETWEEN'=>array('label'=>$this->translate('is between'),'range'=>true),
            'NOT BETWEEN'=>array('label'=>$this->translate('is not between'),'range'=>true),
        );
        return $this->_operators;
    }
	
}