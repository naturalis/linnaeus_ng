<?php

include_once ('Controller.php');
include_once ('SearchController.php');
include_once ('NSRFunctionsController.php');

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

    public $controllerPublicName = 'Search';

    public $usedHelpers = array();

	public $cssToLoad = array();

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

		$this->_suppressTab_DNA_BARCODES = $this->getSetting('species_suppress_autotab_dna_barcodes',0)==1;
		$this->_search_presence_help_url = $this->getSetting( "nbc_search_presence_help_url" );

		$this->_taxon_base_url_images_main = $this->getSetting( "taxon_base_url_images_main", "http://images.naturalis.nl/original/" );
		$this->_taxon_base_url_images_thumb = $this->getSetting( "taxon_base_url_images_thumb", "http://images.naturalis.nl/160x100/" );
		$this->_taxon_base_url_images_overview = $this->getSetting( "taxon_base_url_images_overview", "http://images.naturalis.nl/510x272/" );
		$this->_taxon_base_url_images_thumb_s = $this->getSetting( "taxon_base_url_images_thumb_s", "http://images.naturalis.nl/120x75/" );



		$this->smarty->assign( 'taxon_base_url_images_main',$this->_taxon_base_url_images_main );
		$this->smarty->assign( 'taxon_base_url_images_thumb',$this->_taxon_base_url_images_thumb );
		$this->smarty->assign( 'taxon_base_url_images_overview',$this->_taxon_base_url_images_overview );
		$this->smarty->assign( 'taxon_base_url_images_thumb_s',$this->_taxon_base_url_images_thumb_s );

		$this->models->Taxon->freeQuery("SET lc_time_names = '".$this->getSetting('db_lc_time_names','nl_NL')."'");
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
			$search=$this->requestData;
			$results=$this->doSearch($search);

			$search['search']=htmlspecialchars($search['search']);

			$this->smarty->assign('search', $search);
			$this->smarty->assign('results',$results);	
		}
		
		$searchType=isset($this->requestData['type']) ? $this->requestData['type'] : null;

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
		$search=isset($this->requestData) ? $this->requestData : null;

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
		$search=$this->requestData;
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
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsGroup($this->requestData)));
        } else
        if ($this->rHasVal('action','author_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsAuthor($this->requestData)));
        } else
        if ($this->rHasVal('action','photographer_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsPhotographer($this->requestData)));
        } else
        if ($this->rHasVal('action','validator_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsValidator($this->requestData)));
        } else
        if ($this->rHasVal('action','name_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsName($this->requestData)));
        }

        $this->printPage();
    
    }



	private function getPresenceStatuses()
	{
		return
			$this->models->Presence->freeQuery(array(
				'query'=>'
					select 
					
						_a.id,
						_a.sys_label,
						_a.established,
						ifnull(_b.label,_a.sys_label) as label,
						_b.information,
						_b.information_short,
						_b.information_title,
						_b.index_label
					
					from %PRE%presence _a
					
					left join %PRE%presence_labels _b
						on _a.project_id=_b.project_id
						and _a.id=_b.presence_id 
						and _b.language_id = '.$this->getCurrentLanguageId().'
					
					where
						_a.project_id='.$this->getCurrentProjectId().'
						and _b.index_label is not null
					order by 
						_b.index_label',
				'fieldAsIndex'=>'id'
				)
			);
		
	}
	
	private function getTaxonOverviewImage( $id ) 
	{
		$img=$this->models->MediaTaxon->freeQuery("
			select
				_a.file_name
			from
				%PRE%media_taxon _a, %PRE%media_meta _b
			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.taxon_id=". $id ."
				and _a.id=_b.media_id
				and _a.project_id=_b.project_id
				and _b.sys_label='beeldbankDatumAanmaak'
				order by overview_image desc,meta_date desc
			limit 1
		");
		return isset($img[0]) ? $img[0]['file_name'] : null;
	}

	private function doSearch($p)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resSpeciesPerPage;

		$search=trim($search);
		
		if (empty($search))
			return null;

		$data=$this->models->Names->freeQuery("
			select
				SQL_CALC_FOUND_ROWS	
				_a.taxon_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.name_author,
				_a.authorship_year,
				_a.authorship,
				_a.reference,
				_a.reference_id,
				_a.expert,
				_a.expert_id,
				_a.organisation,
				_a.organisation_id,
				_b.nametype,
				_e.taxon,
				_e.rank_id,
				_f.lower_taxon,
				_k.name as common_name,
				ifnull(_q.label,_x.rank) as common_rank,
				_g.presence_id,
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				ifnull(_j.number_of_barcodes,0) as number_of_barcodes,
	
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
				replace(_ids.nsr_id,'".$this->conceptIdPrefix."','') as nsr_id
				
			from %PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id =  _trash.lng_id
				and _trash.item_type='taxon'
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _x
				on _f.rank_id=_x.id

			left join %PRE%labels_projects_ranks _q
				on _e.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%presence_taxa _g
				on _a.taxon_id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$this->getCurrentLanguageId()."


			left join
				(select project_id,taxon_id,count(*) as number_of_barcodes from %PRE%dna_barcodes group by project_id,taxon_id) as _j
				on _a.taxon_id=_j.taxon_id
				and _j.project_id=_a.project_id
									
			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _k.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%nsr_ids _ids
				on _a.taxon_id=_ids.lng_id
				and _a.project_id=_ids.project_id
				and _ids.item_type='taxon'

			where _a.project_id =".$this->getCurrentProjectId()."
				and _a.name like '%".mysql_real_escape_string($search)."%' 
				and _b.nametype in (
					'".PREDICATE_PREFERRED_NAME."',
					'".PREDICATE_VALID_NAME."',
					'".PREDICATE_ALTERNATIVE_NAME."',
					'".PREDICATE_SYNONYM."',
					'".PREDICATE_SYNONYM_SL."',
					'".PREDICATE_HOMONYM."',
					'".PREDICATE_BASIONYM."',
					'".PREDICATE_MISSPELLED_NAME."',
					'".PREDICATE_INVALID_NAME."'
				)
				and ifnull(_trash.is_deleted,0)=0
		
			group by _a.taxon_id

			order by 
				match_percentage desc, _e.taxon asc, _f.rank_id asc, ".
				(!empty($p['sort']) && $p['sort']=='preferredNameNl' ? "common_name" : "taxon" )."
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		);
		
		//q($data,1);
		//q($this->models->Names->q(),1);

		//SQL_CALC_FOUND_ROWS
		$count=$this->models->Names->freeQuery('select found_rows() as total');

		foreach((array)$data as $key=>$val)
		{
			$data[$key]['overview_image']=$this->getTaxonOverviewImage($val['taxon_id']);
		}

		return array('count'=>$count[0]['total'],'data'=>$data,'perpage'=>$this->_resSpeciesPerPage);

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

		if ($d) 
		{
			$ancestor=$d[0];
		}


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

		if (!empty($p['author'])) $auth=$p['author'];

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

		$trait_joins=$this->getTraitJoins($traits);
		$traitgroup_joins=$this->getTraitGroupJoin($trait_group);

		$data=$this->models->Taxon->freeQuery("
			select
				SQL_CALC_FOUND_ROWS
				_a.id,
				_a.id as taxon_id,
				_a.taxon,
				_k.name as common_name,
				".( $images ? "ifnull(_i.number_of_images,0) as number_of_images," : "" )."
				".( $trend ? "ifnull(_trnd.number_of_trend_years,0) as number_of_trend_years," : "" )."
				".( $distribution ? "ifnull(_ii.number_of_maps,0) as number_of_maps," : "" )."
				".($dna ? "ifnull(_j.number_of_barcodes,0) as number_of_barcodes," : "" )."
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				_l.file_name as overview_image,
				replace(_ids.nsr_id,'".$this->conceptIdPrefix."','') as nsr_id

			from %PRE%taxa _a
			
			".$trait_joins."
			".$traitgroup_joins."

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _k.language_id=".$this->getCurrentLanguageId()."

			". (isset($auth) ? "
				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _m.language_id=".LANGUAGE_ID_SCIENTIFIC : "" 
				)."

			".( $images ? "
				left join
					(
						select 
							_sub1.project_id,taxon_id,count(*) as number_of_images 
						from
							%PRE%media_taxon as _sub1

						left join %PRE%media_meta _meta9
							on _sub1.id=_meta9.media_id
							and _sub1.project_id=_meta9.project_id
							and _meta9.sys_label='verspreidingsKaart'

						where
							ifnull(_meta9.meta_data,0)!=1

						group by
							_sub1.project_id,taxon_id
					) as _i
					on _a.id=_i.taxon_id
					and _i.project_id=_a.project_id" :  "" 
				)."

			".( $trend ? "
				left join
					(
						select 
							project_id,taxon_id,count(*) as number_of_trend_years 
						from
							%PRE%taxon_trend_years 
						group by 
							project_id,taxon_id
					) as _trnd
					on _a.id=_trnd.taxon_id
					and _trnd.project_id=_a.project_id" :  "" 
				)."

			".( $distribution ? "
				left join
					(
						select 
							_sub2.project_id,taxon_id,count(*) as number_of_maps
						from
							%PRE%media_taxon as _sub2
	
						left join %PRE%media_meta _meta19
							on _sub2.id=_meta19.media_id
							and _sub2.project_id=_meta19.project_id
							and _meta19.sys_label='verspreidingsKaart'
	
						where
							_meta19.meta_data=1
	
						group by
							_sub2.project_id,taxon_id
					) as _ii
					on _a.id=_ii.taxon_id
					and _ii.project_id=_a.project_id" :  "" 
			)."

			".($dna ? "
				left join
					(
						select 
							project_id,taxon_id,count(*) as number_of_barcodes 
						from
							%PRE%dna_barcodes group by project_id,taxon_id
					) as _j
					on _a.id=_j.taxon_id
					and _j.project_id=_a.project_id" :  "" 
				)."

			right join %PRE%taxon_quick_parentage _q
				on _a.id=_q.taxon_id
				and _a.project_id=_q.project_id

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%presence_taxa _g
				on _a.id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id=_h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%media_taxon _l
				on _a.id=_l.taxon_id
				and _a.project_id=_l.project_id
				and _l.overview_image=1

			left join %PRE%nsr_ids _ids
				on _a.id=_ids.lng_id
				and _a.project_id=_ids.project_id
				and _ids.item_type='taxon'

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _f.lower_taxon=1
				and ifnull(_trash.is_deleted,0)=0
				".(isset($ancestor['id']) ? "and MATCH(_q.parentage) AGAINST ('".$ancestor['id']."' in boolean mode)" : "")."
				".(isset($pres) ? "and _g.presence_id in (".implode(',',$pres).")" : "")."
				".(isset($auth) ? "and _m.authorship like '". mysql_real_escape_string($auth)."%'" : "")."
				".($dna ? "and number_of_barcodes ".($dna_insuff ? "between 1 and 3" : "> 0") : "")."

				".( $images ? " and (
					".(!is_null($images_on) ? "ifnull(_i.number_of_images,0) > 0" : "")."
					".(!is_null($images_on) && !is_null($images_off) ? " or " : "" )."
					".(!is_null($images_off) ? "ifnull(_i.number_of_images,0) = 0" : "" )."
				) " : "" )."

				".( $distribution ? " and (
					".(!is_null($distribution_on) ? "ifnull(_ii.number_of_maps,0) > 0" : "")."
					".(!is_null($distribution_on) && !is_null($distribution_off) ? " or " : "" )."
					".(!is_null($distribution_off) ? "ifnull(_ii.number_of_maps,0) = 0" : "" )."
				) " : "" )."

				".( !is_null($trend_on) || !is_null($trend_off) ? " and (
					".(!is_null($trend_on) ? "ifnull(_trnd.number_of_trend_years,0) > 0" : "")."
					".(!is_null($trend_on) && !is_null($trend_off) ? " or " : "" )."
					".(!is_null($trend_off) ? "ifnull(_trnd.number_of_trend_years,0) = 0" : "" )."
				) " : "" )."

				".(!empty($trait_joins) || !empty($traitgroup_joins) ? "group by _a.id" : "" )."
				".(!empty($traitgroup_joins) ? "having count(_ttv.id)+count(_ttf.id) > 0" : "" )."
			order by ".
				(isset($p['sort']) && $p['sort']=='name-pref-nl' ? "common_name,_a.taxon" : "_a.taxon")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);

//		q($this->models->Taxon->q(),1);
//		q($data,1);

		$count=$this->models->Taxon->freeQuery('select found_rows() as total');
		$count=$count[0]['total'];
		
		foreach((array)$data as $key=>$val)
		{
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
		$tCount=$this->models->MediaTaxon->freeQuery(array(
			'query'=>
				"select 
					count(distinct _a.taxon_id) as taxon_count,
					_b.meta_data
				from 
					%PRE%media_taxon _a
	
				right join %PRE%media_meta _b
					on _a.project_id=_b.project_id
					and _a.id = _b.media_id
					and _b.sys_label = 'beeldbankFotograaf'
				
				where
					_a.project_id=".$this->getCurrentProjectId()."
				group by _b.meta_data",
			'fieldAsIndex'=>'meta_data'
			)
		);
		
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
		$tCount=$this->models->MediaTaxon->freeQuery(array(
			'query'=>
				"select 
					count(distinct _a.taxon_id) as taxon_count,
					_b.meta_data
				from 
					%PRE%media_taxon _a
	
				right join %PRE%media_meta _b
					on _a.project_id=_b.project_id
					and _a.id = _b.media_id
					and _b.sys_label = 'beeldbankValidator'
				
				where
					_a.project_id=".$this->getCurrentProjectId()."
				group by
					_b.meta_data",
			'fieldAsIndex'=>'meta_data'
			)
		);
		
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

	private function doPictureSearch($p)
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

		if (!empty($p['photographer']))
		{
			//$photographer="_c.meta_data='".mysql_real_escape_string($p['photographer'])."'";
			$photographer="_c.meta_data like '%".mysql_real_escape_string($p['photographer'])."%'";
		}

		if (!empty($p['validator']))
		{
			//$validator="_meta6.meta_data='".mysql_real_escape_string($p['validator'])."'";
			$validator="_meta6.meta_data like '%".mysql_real_escape_string($p['validator'])."%'";
		}


		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;

		$sort="_meta4.meta_date desc";
		
		if (isset($p['sort']) && $p['sort']=='photographer')
		{
			$sort="_c.meta_data asc";
		}
		else
		if (isset($p['sort']))
		{
			$sort=$p['sort'];
		}

		if (!empty($p['photographer']) || !empty($p['validator']))
		{
			$sort="_meta4.meta_date desc, _k.taxon";
		}

		if (!empty($p['photographer']) || !empty($p['validator']))
		{
			$sort="_meta4.meta_date desc, _k.taxon";
		}


		$data=$this->models->MediaTaxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_m.file_name as image,
				_m.file_name as thumb,

				_k.taxon,
				_k.taxon as validName,

				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing

			from  %PRE%media_taxon _m
			
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
				and _meta4.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			".(!empty($group_id) ? 
				"right join %PRE%taxon_quick_parentage _q
					on _m.taxon_id=_q.taxon_id
					and _m.project_id=_q.project_id
				" : "" )."

			".(!empty($name) ? 
				"left join %PRE%names _j
					on _m.taxon_id=_j.taxon_id
					and _m.project_id=_j.project_id
					and _j.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
				" : "" )."


			".(isset($photographer) ? 
				"left join %PRE%media_meta _c
					on _m.project_id=_c.project_id
					and _m.id = _c.media_id
					and _c.sys_label = 'beeldbankFotograaf'
				" : "" )."
					
			".(isset($validator) ? 
				"left join %PRE%media_meta _meta6
					on _m.id=_meta6.media_id
					and _m.project_id=_meta6.project_id
					and _meta6.sys_label='beeldbankValidator'
					and _meta6.language_id=".$this->getCurrentLanguageId()."
				" : "" )."					

			where _m.project_id = ".$this->getCurrentProjectId()."

				and ifnull(_meta9.meta_data,0)!=1
				and ifnull(_trash.is_deleted,0)=0
		
				".(isset($photographer)  ? "and ".$photographer : "")." 		
				".(isset($validator)  ? "and ".$validator : "")." 		
				".(!empty($group_id) ? "and  MATCH(_q.parentage) AGAINST ('".$group_id."' in boolean mode)"  : "")."
				".(!empty($name_id) ? "and _m.taxon_id = ".intval($name_id)  : "")." 		
				".(!empty($name) ? "and _j.name like '". mysql_real_escape_string($name)."%'"  : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);
		
		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');
		
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
			
			//$data[$key]['nsr_id']=replace(_ids.nsr_id,'".$this->conceptIdPrefix."','') as nsr_id
			
	
		}
		
		return
			array(
				'count'=>$count[0]['total'],
				'data'=> $this->NSRFunctions->formatPictureResults( $data ),
				'perpage'=>$this->_resPicsPerPage
			);

	}

	private function getSuggestionsGroup($p)
	{
		$clause=null;

		if ($p['match']=='start')
			$clause="_a.name like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="_a.name = '".mysql_real_escape_string($p['search'])."'";
		else
		if ($p['match']=='id')
			$clause="_a.taxon_id = ".(int)$p['id'];

		if (empty($clause)) return;
		
		$d=$this->models->Taxon->freeQuery("
			select

				_a.taxon_id as id,
				_a.name,
				_b.nametype,
				_g.label as rank,
				_k.name as dutch_name,
				if (_b.nametype='".PREDICATE_VALID_NAME."',
						concat(_a.name,if(_k.name is null,'',concat('  - ',_k.name)),' [',_g.label,']'),
						concat(_a.name,'',' [',_g.label,']')
					)  as label

			from %PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id =  _trash.lng_id
				and _trash.item_type='taxon'
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _g
				on _e.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=
					(
						select id 
						from %PRE%name_types 
						where project_id = ".$this->getCurrentProjectId()." 
						and nametype='".PREDICATE_PREFERRED_NAME."'
					)
				and _k.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id =".$this->getCurrentProjectId()."
				and _f.lower_taxon!=1
				and ifnull(_trash.is_deleted,0)=0
				and 
				".$clause."
				and (
					_b.nametype='".PREDICATE_VALID_NAME."'
					or
						(
							_b.nametype='".PREDICATE_PREFERRED_NAME."' and
							_a.language_id=".$this->getCurrentLanguageId()."
						)
					)
			order by name
			limit ".$this->_suggestionListItemMax
		);
		return $d;
	}

	private function getSuggestionsAuthor($p)
	{	
		$clause=null;
		
		if ($p['match']=='start')
			$clause="authorship like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="authorship = '".mysql_real_escape_string($p['search'])."'";

		if (empty($clause)) return;
		
		$d=$this->models->Taxon->freeQuery("
			select
				distinct authorship as label
			from %PRE%names
			where 
				project_id =".$this->getCurrentProjectId()."
				and ".$clause."
			order by authorship
			limit ".$this->_suggestionListItemMax
		);
		return $d;
	}

	private function getSuggestionsValidator($p)
	{
		$clause=null;

		if ($p['match']=='start')
			$clause="meta_data like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="meta_data = '".mysql_real_escape_string($p['search'])."'";
		else
		if ($p['match']=='like')
			$clause="meta_data like '%".mysql_real_escape_string($p['search'])."%'";

		if (empty($clause)) return;

		$d=$this->models->MediaTaxon->freeQuery("
			select 
				distinct
				meta_data as label
			from %PRE%media_meta
			where
				project_id=".$this->getCurrentProjectId()."
				and sys_label = 'beeldbankValidator'
				and ".$clause."
			order by meta_data
			limit ".$this->_suggestionListItemMax
		);

		return $d;
		
	}

	private function getSuggestionsPhotographer($p)
	{
		$clause=null;

		if ($p['match']=='start')
			$clause="meta_data like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="meta_data = '".mysql_real_escape_string($p['search'])."'";
		else
		if ($p['match']=='like')
			$clause="meta_data like '%".mysql_real_escape_string($p['search'])."%'";

		if (empty($clause)) return;

		$d=$this->models->MediaTaxon->freeQuery("
			select 
				distinct
				meta_data as label
			from %PRE%media_meta
			where
				project_id=".$this->getCurrentProjectId()."
				and sys_label = 'beeldbankFotograaf'
				and ".$clause."
			order by meta_data
			limit ".$this->_suggestionListItemMax
		);

		return $d;
		
	}

	private function getSuggestionsName($p)
	{
		$d=$this->models->MediaTaxon->freeQuery("
			select
				distinct 
				_a.taxon_id as id,
				concat(_d.taxon,if(_c.name is null,'',concat(' - ',_c.name)),' [',_g.label,']') as label
				
			from %PRE%names _a
			
			right join %PRE%taxa _d
				on _a.taxon_id = _d.id
				and _a.project_id = _d.project_id

			left join %PRE%trash_can _trash
				on _d.project_id = _trash.project_id
				and _d.id =  _trash.lng_id
				and _trash.item_type='taxon'

			right join %PRE%media_taxon _b
				on _a.taxon_id = _b.taxon_id
				and _a.project_id = _b.project_id
			
			left join %PRE%names _c
				on _a.taxon_id=_c.taxon_id
				and _a.project_id=_c.project_id
				and _c.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _c.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%projects_ranks _f
				on _d.rank_id=_f.id
				and _d.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _g
				on _d.rank_id=_g.project_rank_id
				and _d.project_id = _g.project_id
				and _g.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and ifnull(_trash.is_deleted,0)=0
				and _f.rank_id >= ".SPECIES_RANK_ID."
				and _a.name like '". mysql_real_escape_string($p['search']) ."%'
				and (_a.language_id=".$this->getCurrentLanguageId()." or _a.language_id=".LANGUAGE_ID_SCIENTIFIC.")
				and ifnull(label,'') != ''
			order by label

			limit ".$this->_suggestionListItemMax
		);

		return $d;
		
	}

	private function reconstructQueryString($p)
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

		foreach((array)$this->requestData as $key=>$val)
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

	private function makeReadableTraitString($p)
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

	private function downloadHeaders($p)
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
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				ifnull(_b.translation,_a.sysname) as name,
				_c.translation as code,
				_d.translation as description,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_g.sysname as type_sysname,
				_g.allow_values as type_allow_values,
				_g.allow_select_multiple as type_allow_select_multiple,
				_g.allow_max_length as type_allow_max_length,
				_g.allow_unit as type_allow_unit,
				count(_v.id) as value_count,
				_grp_b.translation as group_name,
				_grp_c.translation as group_description,
				_grp_d.translation as group_all_link_text,
				_grp.id as group_id,
				_grp.show_show_all_link as group_show_show_all_link,
				_grp.help_link_url as group_help_link_url

			from
				%PRE%traits_traits _a

			right join 
				%PRE%traits_groups _grp
				on _a.project_id=_grp.project_id
				and _a.trait_group_id=_grp.id
				
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
				
			left join 
				%PRE%text_translations _grp_d
				on _grp.project_id=_grp_d.project_id
				and _grp.all_link_text_tid=_grp_d.text_id
				and _grp_d.language_id=". $this->getCurrentLanguageId() ."
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.text_id
				and _b.language_id=". $this->getCurrentLanguageId() ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.text_id
				and _c.language_id=". $this->getCurrentLanguageId() ."

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.text_id
				and _d.language_id=". $this->getCurrentLanguageId() ."

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id
				
			left join
				%PRE%traits_values _v
				on _a.project_id=_v.project_id
				and _a.id=_v.trait_id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.trait_group_id 
				and _grp.show_in_search=1
				
				" . ( is_array($group) ? " in (".implode(",",$group).") " : " = " . $group ) ."

			group by
				_a.id

			order by
				_a.show_order
		");
		
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

	private function getTraitgroupTrait($id)
	{
		if (empty($id)) return;
		
		$r=$this->models->TraitsTraits->freeQuery("
			select
				_a.*,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_e.format_db as date_format_format_db,
				_g.sysname as type_sysname,
				_g.verification_function_name as type_verification_function_name
			from
				%PRE%traits_traits _a

			left join 
				%PRE%traits_date_formats _e
				on _a.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _a.project_id=_f.project_id
				and _a.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id=". $this->getCurrentProjectId()."
				and _a.id=".$id."
		");

		$r = isset($r[0]) ? $r[0] : null;

		return $r;
	}

	private function getTraitgroupTraitValues($id)
	{
		if (empty($id)) return;

		$r=$this->models->TraitsValues->freeQuery("
			select
				_a.id,
				_a.trait_id,
				if(length(ifnull(_trans.translation,''))=0,_a.string_value,_trans.translation) as string_value,
				_a.numerical_value,
				_a.numerical_value_end,
				_a.date,
				_a.date_end,
				_a.is_lower_limit,
				_a.is_upper_limit,
				_a.lower_limit_label,
				_a.upper_limit_label,						
				_g.allow_fractures,
				_e.format as date_format_format

			from 
				%PRE%traits_values _a

			left join 
				%PRE%text_translations _trans
				on _a.project_id=_trans.project_id
				and _a.string_label_tid=_trans.text_id
				and _trans.language_id=". $this->getCurrentLanguageId() ."
				
			left join 
				%PRE%traits_traits _b
				on _a.project_id=_b.project_id
				and _a.trait_id=_b.id

			left join 
				%PRE%traits_date_formats _e
				on _b.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _b.project_id=_f.project_id
				and _b.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.trait_id = ".$id." 
			order by 
				_a.show_order
		
		");
		
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

	private function getTaxonTraitValues($taxon)
	{
		if (empty($taxon)) return;

		$t1=$this->models->TraitsTaxonValues->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'taxon_id'=>$taxon
			)
		));

		$t2=$this->models->TraitsTaxonFreevalues->freeQuery("
			select
				_a.*,
				_e.sysname as date_format_name,
				_e.format as date_format_format,
				_e.format_hr as date_format_format_hr,
				_b.sysname,
				_b.can_be_null,
				_b.can_have_range,
				_g.sysname,
				_g.verification_function_name as type_verification_function_name

			from 
				%PRE%traits_taxon_freevalues _a

			left join %PRE%traits_traits _b
				on _a.project_id=_b.project_id
				and _a.trait_id=_b.id

			left join 
				%PRE%traits_date_formats _e
				on _b.date_format_id=_e.id

			left join 
				%PRE%traits_project_types _f
				on _b.project_id=_f.project_id
				and _b.project_type_id=_f.id

			left join 
				%PRE%traits_types _g
				on _f.type_id=_g.id
							
			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.taxon_id=".$taxon."
		");
		
		return 
			array(
				'values'=>$t1,
				'freevalues'=>$t2
			);
	}

	private function formatDbDate($date,$format)
	{
		return is_null($date) ? null : date_format(date_create($date),$format);
	}

	private function getTraitJoins($traits)
	{
		if (empty($traits))	return;
		
		$trait_joins='';

		$trait_vals=array();
		$traits_free=array();

		function cleanupfloat($s)
		{
			return (float)$s;
			// return str_replace(",",".",str_replace(".","",strrev(str_replace(".",",",strrev(str_replace(",",".",$s)),1))));
		}

		function cleanupint($s)
		{
			return (int)preg_replace('/\D*/','',$s);
		}

		foreach((array)$traits as $trait)
		{
			if (isset($trait['valueid']) && $trait['value']=='on')
			{
				$trait_vals[$trait['traitid']][]=$trait['valueid'];
			}
			else
			{
				if (isset($trait['value']) && isset($trait['operator']) && isset($this->_operators[$trait['operator']]))
				{
					$d=array('value1'=>$trait['value'],'operator'=>$trait['operator']);
					
					if (isset($trait['value2']) && $this->_operators[$trait['operator']]['range'])
					{
						$d['value2']=$trait['value2'];
					}
					$traits_free[$trait['traitid']][]=$d;
				}
			}

		}
		
		foreach((array)$trait_vals as $trait=>$val)
		{		
			$trait_joins .=
			"
				right join %PRE%traits_taxon_values _trait_values".$trait."
					on _a.project_id = _trait_values".$trait.".project_id
					and _a.id = _trait_values".$trait.".taxon_id
					and _trait_values".$trait.".value_id in (".implode(",",$val).")
			";
		}

		foreach((array)$traits_free as $id=>$vals)
		{		
			$trait=$this->getTraitgroupTrait($id);

			$trait_joins .=
			"
				right join %PRE%traits_taxon_freevalues _trait_values".$id."
					on _a.project_id = _trait_values".$id.".project_id
					and _trait_values".$id.".trait_id = ".$id."
					and _a.id = _trait_values".$id.".taxon_id
					and ifnull(_trait_values".$id.".date_value_end,_trait_values".$id.".date_value) is not null
					and (
			";

			foreach((array)$vals as $key=>$val)
			{
				$value1=$value2=null;
				
				switch ($trait['type_sysname'])
				{
					case 'datefree':
					case 'datelist':
					case 'datelistfree':
					case 'datefree':
						$value1="STR_TO_DATE('".$val['value1']."', '".$trait['date_format_format_db']."')";
						$value2=isset($val['value2']) ? "STR_TO_DATE('".$val['value2']."', '".$trait['date_format_format_db']."')" : null;
						$column1="date_value";
						$column2="date_value_end";
						break;
					case 'floatfree':
					case 'floatfreelimit':
					case 'floatlist':
					case 'floatlistfree':
						$value1=cleanupfloat($val['value1']);
						$value2=isset($val['value2']) ? cleanupfloat($val['value2']) : null;
						$column1="numerical_value";
						$column2="numerical_value_end";
						break;
					case 'intfree':
					case 'intfreelimit':
					case 'intlist':
					case 'intlistfree':
						$value1=cleanupint($val['value1']);
						$value2=isset($val['value2']) ? cleanupint($val['value2']) : null;
						$column1="numerical_value";
						$column2="numerical_value_end";
						break;
				};
				
				if (is_null($value1) && is_null($value2)) continue;

				$operator=$val['operator'];
				
				if ($operator=='==')
				{
					$x= "
					(
						(
							_trait_values".$id.".".$column2." is null AND
							_trait_values".$id.".".$column1." = ".$value1."
						)
						OR
						(
							_trait_values".$id.".".$column2." is not null AND
							(
								_trait_values".$id.".".$column1." <= ".$value1." AND
								_trait_values".$id.".".$column2." >= ".$value2."
							)
						)
					)
				";
				} else
				if ($operator=='!=')
				{
					$x= "
					(
						(
							_trait_values".$id.".".$column2." is null AND
							_trait_values".$id.".".$column1." !=".$value1."
						)
						OR
						(
							_trait_values".$id.".".$column2." is not null AND
							(
								_trait_values".$id.".".$column1." > ".$value1." OR
								_trait_values".$id.".".$column2." < ".$value2."
							)
						)
					)
				";
				}
				else
				if ($operator=='>' || $operator=='>=')
				{
					$x= "
					(
						ifnull(_trait_values".$id.".".$column2.",_trait_values".$id.".".$column1.") ".$operator." ".$value1."
					)
					";
				}
				else
				if ($operator=='<' || $operator=='=<')
				{
					$x= "
					(
						_trait_values".$id.".".$column1." ".$operator." ".$value1."
					)
					";
				}
				else
				if ($operator=='<' || $operator=='=<')
				{
					$x= "
					(
						_trait_values".$id.".".$column1." ".$operator." ".$value1."
					)
					";
				}
				else
				if ($operator=='BETWEEN' || $operator=='NOT BETWEEN')
				{
					$x= "
					(
						_trait_values".$id.".".$column1." ".$operator." ".$value1." AND ".$value2."
					)
					";
				}
				
				$trait_joins .= ($key>0 ? " || " : "" ).$x;
			}
			
			$trait_joins .= "
					)
					";
		}
							
		return $trait_joins;
	}

	private function getTraitGroupJoin($group)
	{
		if (empty($group))	return;
		
		return
			"
				left join %PRE%traits_taxon_values _ttv
					on _a.project_id = _ttv.project_id
					and _a.id = _ttv.taxon_id

				left join %PRE%traits_values _tv
					on _ttv.project_id = _tv.project_id
					and _ttv.value_id = _tv.id

				left join %PRE%traits_traits _tt
					on _tv.project_id = _tt.project_id
					and _tv.trait_id = _tt.id
					and _tt.trait_group_id=".$group."


				left join %PRE%traits_taxon_freevalues _ttf
					on _a.project_id = _ttf.project_id
					and _a.id = _ttf.taxon_id

				left join %PRE%traits_traits _tt2
					on _ttf.project_id = _tt2.project_id
					and _ttf.trait_id = _tt2.id
					and _tt2.trait_group_id=".$group."
			";
	}

}