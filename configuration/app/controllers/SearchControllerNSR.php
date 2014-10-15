<?php

include_once ('Controller.php');
include_once ('SearchController.php');

class SearchControllerNSR extends SearchController
{
	private $_suggestionListItemMax=25;
	private $_resPicsPerPage=12;
	private $_resSpeciesPerPage=50;
	private $_nameTypeIds;

    public $usedModels = array(
		'taxa',
		'presence',
		'presence_labels',
		'media_meta',
		'media_taxon',
		'name_types'
    );

    public $controllerPublicName = 'Search';

    public $usedHelpers = array();

	public $cssToLoad = array();

	public $jsToLoad = array();

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
		$this->models->Taxon->freeQuery("SET lc_time_names = 'nl_NL'");
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
			$this->smarty->assign('search',$search);	
			$this->smarty->assign('results',$results);	
		}
		
		$searchType=isset($this->requestData['type']) ? $this->requestData['type'] : null;

		$this->smarty->assign('querystring',$this->reconstructQueryString(array('page')));
		$this->smarty->assign('type',$searchType);
		$this->smarty->assign('searchHR',$this->makeReadableQueryString());
		$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');

        $this->printPage($this->rHasVal('action','export') ? 'export' : null);
	}

    public function searchExtendedAction()
    {
		$this->smarty->assign('results',$this->doExtendedSearch($this->requestData));
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('page')));
		$this->smarty->assign('search',$this->requestData);	
		$this->smarty->assign('presence_statuses',$this->getPresenceStatuses());
		$this->smarty->assign('searchHR',$this->makeReadableQueryString());
		$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');

        $this->printPage($this->rHasVal('action','export') ? 'export' : null);
    }

    public function searchPicturesAction()
    {
		$results = $this->doPictureSearch($this->requestData);

		$this->smarty->assign('search',$this->requestData);	
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('page')));
		$this->smarty->assign('results',$results);	
			
		$p=$this->requestData;

		if ($this->rHasVal('show','photographers'))
		{
			$this->smarty->assign('show','photographers');
			$p['limit']='*';
		}

		$this->smarty->assign('photographers',$this->getPhotographersPictureCount($p));
		$this->smarty->assign('validators',$this->getValidatorPictureCount($p));
		$this->smarty->assign('searchHR',$this->makeReadableQueryString());
		$this->smarty->assign('url_taxon_detail',"http://". $_SERVER['HTTP_HOST'].'/linnaeus_ng/'.$this->getAppname().'/views/species/taxon.php?id=');
		$this->smarty->assign('imageExport',true);

        $this->printPage($this->rHasVal('action','export') ? 'export' : null);
    }

    public function recentPicturesAction()
    {
		$results = $this->doPictureSearch($this->requestData);
		$this->smarty->assign('search',$this->requestData);	
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('page')));
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

	private function doSearch($p)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resSpeciesPerPage;

		$search=trim($search);
		
		if (empty($search))
			return null;

		$d=$this->models->Names->freeQuery("
			select
				SQL_CALC_FOUND_ROWS	
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
				end as match_percentage
				
			from %PRE%names _a
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
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
					'".PREDICATE_MISSPELLED_NAME."'
				)
			
			group by _a.taxon_id

			order by 
				match_percentage desc, _a.name asc, _f.rank_id asc, ".
				(!empty($p['sort']) && $p['sort']=='preferredNameNl' ? "common_name" : "taxon" )."
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		);
		
		//q($d,1);
		//q($this->models->Names->q(),1);

		//SQL_CALC_FOUND_ROWS
		$count=$this->models->Names->freeQuery('select found_rows() as total');

		foreach((array)$d as $key=>$val)
		{
			$img=$this->models->MediaTaxon->freeQuery("
				select
					_a.file_name
				from
					%PRE%media_taxon _a, %PRE%media_meta _b
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id=".$val['taxon_id']."
					and _a.id=_b.media_id
					and _a.project_id=_b.project_id
					and _b.sys_label='beeldbankDatumAanmaak'
					order by meta_date desc
				limit 1
			");
			$d[$key]['overview_image']=$img[0]['file_name'];
		}

		return array('count'=>$count[0]['total'],'data'=>$d,'perpage'=>$this->_resSpeciesPerPage);

	}

	private function doExtendedSearch($p)
	{
		$d=null;
		if (!empty($p['group_id']))
			$d=$this->getSuggestionsGroup(array('id'=>(int)trim($p['group_id']),'match'=>'id'));
		else
		if (!empty($p['group']))
			$d=$this->getSuggestionsGroup(array('search'=>$p['group'],'match'=>'exact'));

		if ($d) 
			$ancestor=$d[0];
//		else
//			return null;

		$img=!empty($p['images']);
		$distribution=!empty($p['distribution']);
		$trend=!empty($p['trend']);

		$dna=(!empty($p['dna']) || !empty($p['dna_insuff']));

		$dna_insuff=!empty($p['dna_insuff']);

		if (!empty($p['author'])) $auth=$p['author'];

		if (!empty($p['presence'])) {
			$pres=array();
			foreach((array)$p['presence'] as $key=>$val) {
				if ($val=='on') $pres[]=intval($key);
			}
		}

		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resSpeciesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resSpeciesPerPage;

		$data=$this->models->Taxon->freeQuery("
			select
				SQL_CALC_FOUND_ROWS
				_a.id,
				_a.id as taxon_id,
				_a.taxon,
				_k.name as common_name,
				".($img ? "ifnull(_i.number_of_images,0) as number_of_images," : "" )."
				".($dna ? "ifnull(_j.number_of_barcodes,0) as number_of_barcodes," : "" )."
				".($trend ? "ifnull(_trnd.number_of_trend_years,0) as number_of_trend_years," : "" )."
				".($distribution ? "ifnull(_ii.number_of_maps,0) as number_of_maps," : "" )."
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				_l.file_name as overview_image
			
			from %PRE%taxa _a

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

			". ($img ? "
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
			
			". ($dna ? "
				left join
				(select project_id,taxon_id,count(*) as number_of_barcodes from %PRE%dna_barcodes group by project_id,taxon_id) as _j
					on _a.id=_j.taxon_id
					and _j.project_id=_a.project_id" :  "" 
				)."

			". ($trend ? "
				left join
				(select project_id,taxon_id,count(*) as number_of_trend_years from %PRE%taxon_trend_years group by project_id,taxon_id) as _trnd
					on _a.id=_trnd.taxon_id
					and _trnd.project_id=_a.project_id" :  "" 
				)."

			". ($distribution ? "
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

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _f.lower_taxon=1
			".(isset($ancestor['id']) ? "and MATCH(_q.parentage) AGAINST ('".$ancestor['id']."' in boolean mode)" : "")."
			".(isset($pres) ? "and _g.presence_id in (".implode(',',$pres).")" : "")."
			".(isset($auth) ? "and _m.authorship like '". mysql_real_escape_string($auth)."%'" : "")."
			".($img ? "and number_of_images > 0" : "")."
			".($dna ? "and number_of_barcodes ".($dna_insuff ? "between 1 and 3" : "> 0") : "")."
			".($trend ? "and number_of_trend_years > 0" : "")."
			".($distribution ? "and number_of_maps > 0" : "")."

			order by ".(isset($p['sort']) && $p['sort']=='name-pref-nl' ? "common_name" : "_a.taxon")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);
	
		//q($this->models->Taxon->q(),1);

		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');

		foreach((array)$data as $key=>$val)
		{
			$img=$this->models->MediaTaxon->freeQuery("
				select
					_a.file_name
				from
					%PRE%media_taxon _a, %PRE%media_meta _b
				where
					_a.project_id=".$this->getCurrentProjectId()."
					and _a.taxon_id=".$val['taxon_id']."
					and _a.id=_b.media_id
					and _a.project_id=_b.project_id
					and _b.sys_label='beeldbankDatumAanmaak'
					order by meta_date desc
				limit 1
			");
			$data[$key]['overview_image']=$img[0]['file_name'];
		}

		return array('count'=>$count[0]['total'],'data'=>$data,'perpage'=>$this->_resSpeciesPerPage,'ancestor'=>isset($ancestor) ? $ancestor : null);
	}

	private function getPhotographersPictureCount($p=null)
	{
		$photographers=$this->getCache('search-photographer-count');

		if (!$photographers)
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
			
			foreach((array)$photographers as $key=>$val) {
				
				$photographers[$key]['taxon_count']=isset($tCount[$val['meta_data']]) ? $tCount[$val['meta_data']]['taxon_count'] : 0;
				
			}
			
			$this->saveCache('search-photographer-count',$photographers);
			
		}

		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);

		if (!empty($limit) && $limit<count((array)$photographers)) {
			$photographers=array_slice($photographers,0,$limit);
		}
		
		return $photographers;
		
	}

	private function getValidatorPictureCount($p=null)
	{

		$validators=$this->getCache('search-validator-count');

		if (!$validators)
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
					group by _b.meta_data",
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
			
			foreach((array)$validators as $key=>$val) {
				
				$validators[$key]['taxon_count']=isset($tCount[$val['meta_data']]) ? $tCount[$val['meta_data']]['taxon_count'] : 0;
				
			}
			
			$this->saveCache('search-validator-count',$validators);
			
		}

		$limit=!isset($p['limit']) ? 5 : ($p['limit']=='*' ? null : $p['limit']);

		if (!empty($limit) && $limit<count((array)$validators)) {
			$validators=array_slice($validators,0,$limit);
		}

		return $validators;		
	}

	private function doPictureSearch($p)
	{
		$group_id=null;

		if (empty($p['group_id']) && !empty($p['group'])) {
			$d=$this->getSuggestionsGroup(array('search'=>$p['group'],'match'=>'exact'));
			if ($d) 
				$group_id=$d[0];
		} else
		if (!empty($p['group_id'])) {
			$group_id=intval($p['group_id']);
		}

		if (!empty($p['photographer'])) {
			$photographer="_c.meta_data='".mysql_real_escape_string($p['photographer'])."'";
		}

		if (!empty($p['validator'])) {
			$photographer="_meta6.meta_data='".mysql_real_escape_string($p['validator'])."'";
		}

		$limit=!empty($p['limit']) ? $p['limit'] : $this->_resPicsPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_resPicsPerPage;
		

		$sort="_meta4.meta_date desc";
		
		if (isset($p['sort']) && $p['sort']=='photographer')
			$sort="_c.meta_data asc";
		else
		if (isset($p['sort']))
			$sort=$p['sort'];
		if (!empty($p['photographer']) || !empty($p['validator']))
			$sort="_meta4.meta_date desc, _k.taxon";

		$data=$this->models->MediaTaxon->freeQuery("		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				file_name as image,
				file_name as thumb,
				_c.meta_data as photographer,
				_k.taxon,
				_k.taxon as validName,
				_z.name as common_name,
				_j.uninomial,
				_j.specific_epithet,
				_j.infra_specific_epithet,
				_j.authorship,		
				concat(
					if(_j.uninomial is null,'',concat(_j.uninomial,' ')),
					if(_j.specific_epithet is null,'',concat(_j.specific_epithet,' ')),
					if(_j.infra_specific_epithet is null,'',_j.infra_specific_epithet)
				) as name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				date_format(_meta1.meta_date,'%e %M %Y') as meta_datum,
				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,
				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing,
				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker
			
			from  %PRE%media_taxon _m
			
			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
				and _c.language_id=".$this->getCurrentLanguageId()."
		
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id
				
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _m.taxon_id=_z.taxon_id
				and _m.project_id=_z.project_id
				and _z.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _z.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
				
			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'
				and _meta1.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'
				and _meta2.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'
				and _meta3.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'
				and _meta4.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'
				and _meta5.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'
				and _meta6.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$this->getCurrentLanguageId()."

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
			
			".(!empty($group_id) ? 
				"right join %PRE%taxon_quick_parentage _q
					on _m.taxon_id=_q.taxon_id
					and _m.project_id=_q.project_id
					" : "" )."
			
			where _m.project_id = ".$this->getCurrentProjectId()."

				and ifnull(_meta9.meta_data,0)!=1
			
				".(!empty($p['name_id']) ? "and _m.taxon_id = ".intval($p['name_id'])." and _f.lower_taxon=1"  : "")." 		
				".(!empty($p['name']) && empty($p['name']) ?
					"and _j.name like '". mysql_real_escape_string($p['name'])."%' and _f.rank_id>= ".SPECIES_RANK_ID  : "")."
				".(isset($photographer)  ? "and ".$photographer : "")." 		
				".(!empty($group_id) ? "and  MATCH(_q.parentage) AGAINST ('".$group_id."' in boolean mode)"  : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		);

		$count=$this->models->MediaTaxon->freeQuery('select found_rows() as total');
		
		return array('count'=>$count[0]['total'],'data'=>$this->formatPictureResults($data),'perpage'=>$this->_resPicsPerPage);
		
	}

	private function formatPictureResults($data)
	{
		foreach((array)$data as $key=>$val)
		{
			$metaData=array(
				'' => '<span class="pic-meta-label">'.(!empty($val['common_name']) ? $val['common_name'].' (<i>'.$val['nomen'].'</i>)' : '<i>'.$val['nomen'].'</i>').'</span>',
				'Fotograaf' => $val['photographer'],
				'Datum' => $val['meta_datum'],
				'Locatie' => $val['meta_geografie'],
				'Validator' => $val['meta_validator'],
				'Geplaatst op' => $val['meta_datum_plaatsing'],
				'Copyright' => $val['meta_copyrights'],
				'Contactadres fotograaf' => $val['meta_adres_maker'],
				'Omschrijving' => $val['meta_short_desc'],
			);

			$data[$key]['photographer']=$val['photographer'];
			$data[$key]['label']=
				trim(
					(!empty($val['photographer']) ? $val['photographer'].', ' : '') .
					(!empty($val['meta_datum']) ? $val['meta_datum'].', ' : '') .
					$val['meta_geografie'], ', '
				);
			$data[$key]['meta_data']=$this->helpers->Functions->nuclearImplode('</span>: ','<br /><span class="pic-meta-label">',$metaData,true);
			
		}
		
		return  $data;
	
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
		
		/*		
		if ($p['match']=='start')
			$clause="meta_data like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="meta_data = '".mysql_real_escape_string($p['search'])."'";
		*/

		if ($p['match']=='start')
			$clause="meta_data like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="meta_data = '".mysql_real_escape_string($p['search'])."'";

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
		$d=$this->models->NameTypes->_get(
			array(
				'id'=>array(
				 	'project_id' => $this->getCurrentProjectId(),
					'nametype' => PREDICATE_PREFERRED_NAME,
					'language_id' => $this->getCurrentLanguageId()
				),
				'columns'=>'id'
			)
		);
		
		$typeId=$d[0]['id'];

		$d=$this->models->MediaTaxon->freeQuery("
			select
				distinct 
				_a.taxon_id as id,
				concat(_d.taxon,if(_c.name is null,'',concat(' - ',_c.name)),' [',_g.label,']') as label
				
			from %PRE%names _a
			
			right join %PRE%taxa _d
				on _a.taxon_id = _d.id
				and _a.project_id = _d.project_id

			right join %PRE%media_taxon _b
				on _a.taxon_id = _b.taxon_id
				and _a.project_id = _b.project_id
			
			left join %PRE%names _c
				on _a.taxon_id=_c.taxon_id
				and _a.project_id=_c.project_id
				and _c.type_id=".$typeId."
				and _c.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
			
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _g
				on _e.rank_id=_g.project_rank_id
				and _e.project_id = _g.project_id
				and _g.language_id=".$this->getCurrentLanguageId()."

			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and _f.rank_id >= ".SPECIES_RANK_ID."
				and _a.name like '". mysql_real_escape_string($p['search']) ."%'
				and (_a.language_id=".$this->getCurrentLanguageId()." or _a.language_id=".LANGUAGE_ID_SCIENTIFIC.")
				and ifnull(label,'') != ''
			order by label

			limit ".$this->_suggestionListItemMax
		);

		return $d;
		
	}

	private function reconstructQueryString($ignore)
	{
		$querystring=null;

		foreach((array)$this->requestData as $key=>$val)
		{
			if (in_array($key,$ignore)) continue;

			if (is_array($val))
			{
				foreach((array)$val as $k2=>$v2)
				{
					$querystring.=$key.'['.$k2.']='.$v2.'&';
				}

			} else {
				$querystring.=$key.'='.$val.'&';
			}
		}
		
		return $querystring;
	}

	private function makeReadableQueryString()
	{
		$querystring=null;
		
		if ($this->rHasVal('group')) $querystring.='Soortgroep="'.$this->requestData['group'].'"; ';
		if ($this->rHasVal('author')) $querystring.='Auteur="'.$this->requestData['author'].'"; ';
		
		if ($this->rHasVal('presence'))
		{
			$statuses=$this->getPresenceStatuses();
			$querystring.='Status voorkomen="';
		
			foreach((array)$this->requestData['presence'] as $key=>$val)
			{
				$querystring.=$statuses[$key]['index_label'].', ';
			}
			$querystring=rtrim($querystring,' ,').'; ';
		}
					
		if ($this->rHasVal('images','on')) $querystring.='met foto\'s; ';
		if ($this->rHasVal('dna','on')) $querystring.='met DNA-exemplaren verzameld; ';
		if ($this->rHasVal('dna_insuff','on')) $querystring.='met nog DNA-exemplaren te verzamelen; ';
		
		
		return trim($querystring);
	}

}