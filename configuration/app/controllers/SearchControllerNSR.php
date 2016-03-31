<?php

include_once ('Controller.php');
include_once ('SearchController.php');
include_once ('NSRFunctionsController.php');
include_once ('ModuleSettingsReaderController.php');

class SearchControllerNSR extends SearchController
{
	private $_suggestionListItemMax=25;
	private $_resPicsPerPage=12;
	private $_resSpeciesPerPage=50;
	private $_nameTypeIds;
	private $conceptIdPrefix='tn.nlsr.concept/';

	private $_operators=array(
		'=='=>array('label'=>'is gelijk aan','range'=>false),
		'!='=>array('label'=>'is ongelijk aan','range'=>false),
		'>'=>array('label'=>'na','range'=>false),
		'<'=>array('label'=>'voor','range'=>false),
		'>='=>array('label'=>'na of gelijk aan','range'=>false),
		'=<'=>array('label'=>'voor of gelijk aan','range'=>false),
		'BETWEEN'=>array('label'=>'ligt tussen','range'=>true),
		'NOT BETWEEN'=>array('label'=>'ligt niet tussen','range'=>true),
	);

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
		'traits_taxon_freevalues'
    );


    public $modelNameOverride = 'SearchNSRModel';
    public $controllerPublicName = 'Search';

    public $usedHelpers = array();

	public $cssToLoad = array('search.css');

	public $jsToLoad = array();

	private $_suppressTab_DNA_BARCODES=false;

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
		$this->NSRFunctions=new NSRFunctionsController;
		$this->moduleSettings=new ModuleSettingsReaderController;

		$this->_search_presence_help_url = $this->moduleSettings->getModuleSetting( array('setting'=>'url_help_search_presence','module'=>'utilities') );
		$this->_taxon_base_url_images_main = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_main','module'=>'species','subst'=>'http://images.naturalis.nl/original/') );
		$this->_taxon_base_url_images_thumb = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb','module'=>'species','subst'=>'http://images.naturalis.nl/160x100/') );
		$this->_taxon_base_url_images_overview = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_overview','module'=>'species','subst'=>'http://images.naturalis.nl/510x272/') );
		$this->_taxon_base_url_images_thumb_s = $this->moduleSettings->getModuleSetting( array('setting'=>'base_url_images_thumb_s','module'=>'species','subst'=>'http://images.naturalis.nl/120x75/') );

		$this->smarty->assign( 'taxon_base_url_images_main',$this->_taxon_base_url_images_main );
		$this->smarty->assign( 'taxon_base_url_images_thumb',$this->_taxon_base_url_images_thumb );
		$this->smarty->assign( 'taxon_base_url_images_overview',$this->_taxon_base_url_images_overview );
		$this->smarty->assign( 'taxon_base_url_images_thumb_s',$this->_taxon_base_url_images_thumb_s );

		$this->models->Taxa->freeQuery("SET lc_time_names = '".$this->moduleSettings->getGeneralSetting( array('setting'=>'db_lc_time_names','subst'=>'nl_NL') )."'");

		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
    }

    public function searchAction()
    {
		if ($this->rHasVal('search'))
		{
			$search=$this->rGetAll();
			$results=$this->doSearch($search);

			$search['search']=htmlspecialchars($search['search']);

			$this->smarty->assign('search', $search);
			$this->smarty->assign('results',$results);
		}

		$searchType = $this->rHasVar('type') ? $this->rGetVal('type') : null;

		if ($this->rHasVal('action','export'))
		{
			$search['limit']=1000;
			$template='export_search';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/nsr/concept/');
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
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
		}

        $this->printPage($template);
	}

    public function searchExtendedAction()
    {
		$search=$this->rGetAll();

		if ($this->rHasVal('action','export'))
		{
			$search['limit']=1000;
			$template='export_search_extended';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/nsr/concept/');

			$this->downloadHeaders(
				array(
					'mime'=>'text/csv',
					'charset'=>'utf-8',
					'filename'=>'NSR-export-'.date('Ymd-his').$this->csvExportSettings['file-extension'])
					);

		}
		else
		{
			$this->smarty->assign('search',$search);
			$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
			$this->smarty->assign('presence_statuses',$this->getPresenceStatuses());
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
			$template=null;
		}

		$this->traitGroupsToInclude=$this->getTraitGroups();

		if (count((array)$this->traitGroupsToInclude)>0)
		{
			$search['traits']=$this->rHasVal('traits') ? json_decode(urldecode($search['traits']),true) : null;
			$search['trait_group']=$this->rHasVal('trait_group') ? $search['trait_group'] : null;

			$traits=array();
			foreach((array)$this->traitGroupsToInclude as $val)
			{
				$traits=$traits+$this->getTraits($val['id']);
			}

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
		$this->smarty->assign('suppressDnaBarcodes',$this->_suppressTab_DNA_BARCODES);
		$this->smarty->assign('search_presence_help_url',$this->_search_presence_help_url);

        $this->printPage($template);
    }

    public function searchPicturesAction()
    {
		$search=$this->requestData;

		if ($this->rHasVal('action','export'))
		{
			$search['limit']=1000;
			$template='export_search_pictures';
			$this->smarty->assign('csvExportSettings',$this->csvExportSettings);
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/nsr/concept/');
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
			$this->smarty->assign('photographers',$this->getPhotographersPictureCount($search));
			$this->smarty->assign('validators',$this->getValidatorPictureCount($search));
			$this->smarty->assign('searchHR',$this->makeReadableQueryString());
			$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
			$this->smarty->assign('imageExport',true);
		}

		if ($this->rHasVal('show','photographers'))
		{
			$this->smarty->assign('show','photographers');
			$search['limit']='*';
		}

		$results = $this->doPictureSearch( $search );

		$this->smarty->assign('search',$search);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('search'=>$search,'ignore'=>array('page'))));
		$this->smarty->assign('results',$results);

        $this->printPage($template);
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

        if ($this->rHasVal('action','group_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsGroup($this->rGetAll())));
        } else
        if ($this->rHasVal('action','author_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsAuthor($this->rGetAll())));
        } else
        if ($this->rHasVal('action','photographer_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsPhotographer($this->rGetAll())));
        } else
        if ($this->rHasVal('action','validator_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsValidator($this->rGetAll())));
        } else
        if ($this->rHasVal('action','name_suggestions'))
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
			"type_id_preferred"=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			"project_id"=>$this->getCurrentProjectId(),
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset
		));

		$data=$d['data'];
		$count=$d['count'];

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['taxon']=$this->addHybridMarker( $val );
			$data[$key]['overview_image']=$this->getTaxonOverviewImage($val['taxon_id']);
		}

		return array('count'=>$count,'data'=>$data,'perpage'=>$this->_resSpeciesPerPage);

	}

	private function doExtendedSearch($p)
	{

		$d=null;

		if (!empty($p['group_id']))
		{
			$d=$this->getSuggestionsGroup(array('id'=>(int)trim($p['group_id']),'match'=>'id'));
		}
		else
		if (!empty($p['group']))
		{
			$d=$this->getSuggestionsGroup(array('search'=>$p['group'],'match'=>'exact'));
		}

		$ancestor=$d ? $d[0] : null;
		
		$images_on=(!empty($p['images_on']) && $p['images_on']=='on' ? true : null);
		$images_off=(!empty($p['images_off']) && $p['images_off']=='on' ? true : null);
		$images=!is_null($images_on) || !is_null($images_off);

		$distribution_on=(!empty($p['distribution_on']) && $p['distribution_on']=='on' ? true : null);
		$distribution_off=(!empty($p['distribution_off']) && $p['distribution_off']=='on' ? true : null);
		$distribution=!is_null($distribution_on) || !is_null($distribution_off);

		$trend_on=(!empty($p['trend_on']) && $p['trend_on']=='on' ? true : null);
		$trend_off=(!empty($p['trend_off']) && $p['trend_off']=='on' ? true : null);
		$trend=!is_null($trend_on) || !is_null($trend_off);

		$dna=(!empty($p['dna']) || !empty($p['dna_insuff']));
		$dna_insuff=!empty($p['dna_insuff']);
		$traits=isset($p['traits']) ? $p['traits'] : null;
		$trait_group=isset($p['trait_group']) ? $p['trait_group'] : null;
		$auth=!empty($p['author']) ? $p['author'] : null;

		$pres=null;
		if (!empty($p['presence']))
		{
			$pres=array();
			foreach((array)$p['presence'] as $key=>$val)
			{
				if ($val=='on') $pres[]=intval($key);
			}
		}

		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resSpeciesPerPage;
		$sort=!empty($p['sort']) ? $p['sort'] : null;

		$d=$this->models->SearchNSRModel->doExtendedSearch(array(
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
			"type_id_preferred"=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			"type_id_valid"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"language_id"=>$this->getCurrentLanguageId(),
			"project_id"=>$this->getCurrentProjectId(),
			"ancestor_id"=>$ancestor['id'],
			"presence"=>$pres,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset,
			"operators"=>$this->_operators
		));

		$data=$d['data'];
		$count=$d['count'];

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['taxon']=$this->addHybridMarker( $val );
			$data[$key]['overview_image']=$this->getTaxonOverviewImage($val['taxon_id']);
		}

		return
			array(
				'count'=>$count,
				'data'=>$data,
				'perpage'=>$this->_resSpeciesPerPage,
				'ancestor'=>isset($ancestor) ? $ancestor : null
			);
	}

	private function getPhotographersPictureCount($p=null)
	{
		$tCount=$this->models->SearchNSRModel->getPhotographersPictureCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));

		$photographers=$this->models->MediaMeta->_get(
			array(
				'id'=>array(
					'project_id'=>$this->getCurrentProjectId(),
					'sys_label' => 'beeldbankFotograaf'
				),
				'columns'=>"count(*) as total, meta_data,
					trim(concat(
						trim(substring(meta_data, locate(',',meta_data)+1)),' ',
						trim(substring(meta_data, 1, locate(',',meta_data)-1))
					)) as photographer",
				'group'=>'meta_data, photographer',
				'order'=>'count(*) desc, meta_data desc',
				'fieldAsIndex'=>'meta_data'
			)
		);

		foreach((array)$photographers as $key=>$val)
		{
			$photographers[$key]['taxon_count']=isset($tCount[$val['meta_data']]) ? $tCount[$val['meta_data']]['taxon_count'] : 0;
		}

		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);

		if (!empty($limit) && $limit<count((array)$photographers))
		{
			$photographers=array_slice($photographers,0,$limit);
		}

		return $photographers;

	}

	private function getValidatorPictureCount($p=null)
	{
		$tCount= $this->models->SearchNSRModel->getValidatorPictureCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));

		$validators=$this->models->MediaMeta->_get(
			array(
				'id'=>array(
					'project_id'=>$this->getCurrentProjectId(),
					'sys_label' => 'beeldbankValidator'
				),
				'columns'=>"count(*) as total, meta_data,
					trim(concat(
						trim(substring(meta_data, locate(',',meta_data)+1)),' ',
						trim(substring(meta_data, 1, locate(',',meta_data)-1))
					)) as validator",
				'group'=>'meta_data, validator',
				'order'=>'count(*) desc, meta_data desc',
				'fieldAsIndex'=>'meta_data'
			)
		);

		foreach((array)$validators as $key=>$val)
		{
			$validators[$key]['taxon_count']=isset($tCount[$val['meta_data']]) ? $tCount[$val['meta_data']]['taxon_count'] : 0;
		}


		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);

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
		} else
		if (!empty($p['group_id']))
		{
			$group_id=intval($p['group_id']);
		}

		if (!empty($p['name']))
		{
			$name=$p['name'];
		}

		if (!empty($p['name_id']))
		{
			$name_id=intval($p['name_id']);
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
			"name"=>$name,
			"type_id_valid"=>$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
			"photographer"=>$photographer,
			"validator"=>$validator,
			"project_id"=>$this->getCurrentProjectId(),
			"group_id"=>$group_id,
			"name_id"=>$name_id,
			"sort"=>$sort,
			"limit"=>$limit,
			"offset"=>$offset
		));

		$data=$d['data'];
		$count=$d['count'];

		foreach((array)$data as $key=>$val)
		{
			$meta=$this->models->MediaMeta->_get(array("id"=>
				array(
					"project_id" => $this->getCurrentProjectId(),
					"media_id" => $val["id"]
				)
			));

			$data[$key]['photographer']="";
			$data[$key]['meta_datum']="";
			$data[$key]['meta_short_desc']="";
			$data[$key]['meta_geografie']="";
			$data[$key]['meta_copyrights']="";
			$data[$key]['meta_validator']="";
			$data[$key]['meta_adres_maker']="";
			$data[$key]['meta_license']="";

			foreach((array)$meta as $m)
			{
				if ($m['sys_label']=='beeldbankFotograaf')
				{
					$data[$key]['photographer']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankDatumVervaardiging')
				{
					// REFAC2015: well...
					if (strtoupper(substr(PHP_OS, 0, 3))==='WIN')
					{
						setlocale(LC_ALL,'nld_nld'); // windows only
						$data[$key]['meta_datum']=strftime( '%d %B %Y',strtotime($m['meta_date']));
					}
					else
					{
						if (!setlocale(LC_ALL,'nl_NL'))
							setlocale(LC_ALL,'nl_NL.utf8');
						$data[$key]['meta_datum']=strftime( '%e %B %Y',strtotime($m['meta_date']));
					}
				}
				else
				if ($m['sys_label']=='beeldbankOmschrijving')
				{
					$data[$key]['meta_short_desc']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankLokatie')
				{
					$data[$key]['meta_geografie']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankCopyright')
				{
					$data[$key]['meta_copyrights']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankValidator')
				{
					$data[$key]['meta_validator']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankAdresMaker')
				{
					$data[$key]['meta_adres_maker']=$m['meta_data'];
				}
				else
				if ($m['sys_label']=='beeldbankLicentie')
				{
					$data[$key]['meta_license']=$m['meta_data'];
				}
			}

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
				} else
				if ( $n['type_id']==$this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] && $n['language_id']==LANGUAGE_ID_SCIENTIFIC )
				{
					$data[$key]['uninomial']=$n['uninomial'];
					$data[$key]['specific_epithet']=$n['specific_epithet'];
					$data[$key]['infra_specific_epithet']=$n['infra_specific_epithet'];
					$data[$key]['authorship']=$n['authorship'];
					$data[$key]['nomen']=trim(str_replace($n['authorship'],'',$n['name']));
					$data[$key]['name']=
						(empty($n['uninomial']) ? '' : $n['uninomial'] . ' ') .
						(empty($n['specific_epithet']) ? '' : $n['specific_epithet'] . ' ') .
						(empty($n['infra_specific_epithet']) ? '' : $n['infra_specific_epithet']);
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
			"match"=>$p['match'],
			"search"=>$p['match']=='id'? $p['id'] : $p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"limit"=>$this->_suggestionListItemMax,
			"taxon_id"=>isset($p['id']) ? $p['id'] : null
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
		return $this->models->SearchNSRModel->getSuggestionsValidator(array(
			"match"=>$p['match'],
			"search"=>$p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"limit"=>$this->_suggestionListItemMax
		));
	}

	private function getSuggestionsName( $p )
	{
		return $this->models->SearchNSRModel->getSuggestionsValidator(array(
			"search"=>$p['search'],
			"project_id"=>$this->getCurrentProjectId(),
			"type_id_preferred"=>$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
			"limit"=>$this->_suggestionListItemMax,
			"language_id"=>$this->getCurrentLanguageId()
		));
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
		if ($this->rHasVal('distribution_on','on')) $querystring.=$this->translate('Met verspreidingskaart(en); ');
		if ($this->rHasVal('distribution_off','on')) $querystring.=$this->translate('Zonder verspreidingskaart(en); ');
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
				_grp_b.translation as group_name,
				_grp_c.translation as group_description,
				_grp.id as group_id

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
			$data[$trait['trait_group_id']]['name']=$trait['group_name'];
			$data[$trait['trait_group_id']]['description']=$trait['group_description'];
			$data[$trait['trait_group_id']]['all_link_text']=$trait['group_all_link_text'];
			$data[$trait['trait_group_id']]['show_show_all_link']=$trait['group_show_show_all_link'];
			$data[$trait['trait_group_id']]['help_link_url']=$trait['group_help_link_url'];
			$data[$trait['trait_group_id']]['group_id']=$trait['group_id'];
			$data[$trait['trait_group_id']]['data'][]=$trait;
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
		return is_null($date) ? null : date_format(date_create($date),$format);
	}

}