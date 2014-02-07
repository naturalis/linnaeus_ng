<?php

include_once ('Controller.php');
include_once ('SearchController.php');

class SearchControllerNSR extends SearchController
{

    public $usedModels = array(
		'taxa',
		'presence',
		'presence_labels',
		'media_meta',
		'media_taxon'
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

    public function searchAction ()
    {
		if ($this->rHasVal('search')) {

			$search=$this->requestData;
			$results=$this->doSearch($search);
			$this->smarty->assign('search',$search);	
			$this->smarty->assign('results',$results);	

		}

		$this->smarty->assign('type',$this->requestData['type']);
		$this->smarty->assign('search',$search);

        $this->printPage();
	}


    public function searchExtendedAction ()
    {
		
		if (
			$this->rHasVal('name') ||
			$this->rHasVal('author') ||
			$this->rHasVal('presence') ||
			$this->rHasVal('images') ||
			$this->rHasVal('distribution') ||
			$this->rHasVal('trend') ||
			$this->rHasVal('dna') ||
			$this->rHasVal('dna_insuff')
		) {
	
			$search=$this->requestData;
			$this->smarty->assign('search',$search);	
		
			if (!empty($search['presence'])) {
				$d=array();
				foreach((array)$search['presence'] as $key=>$val) {
					if ($val=='on') $d[]=intval($key);
				}
				$search['presence']=$d;
			}
		
			$results=$this->doExtendedSearch($search);
		
			$this->smarty->assign('results',$results);
		
		}

		$this->smarty->assign('search',$this->requestData);	

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
			
			from %PRE%presence _a
			
			left join %PRE%presence_labels _b
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


	private function doSearch($search)
	{
		$d=$this->models->Names->freeQuery("
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
				_e.taxon,
				_e.rank_id,
				_f.lower_taxon,
				_k.name as dutch_name,

			_g.presence_id,
			_h.information_title as presence_information_title,
			_h.index_label as presence_information_index_label,
			_l.file_name as overview_image,

				case
					when _a.name REGEXP '^".mysql_real_escape_string($search['search'])."$' = 1 then 100
					when _a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search['search'])."[[:>:]](.*)$' = 1 then 90
					when _a.name REGEXP '^(.*)[[:<:]]".mysql_real_escape_string($search['search'])."(.*)$' = 1 then 80
					when _a.name REGEXP '^(.*)".mysql_real_escape_string($search['search'])."(.*)$' = 1 then 70
					else 60
				end as match_percentage
			
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

			left join %PRE%presence_taxa _g
				on _a.taxon_id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$this->getCurrentLanguageId()."
				
			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%media_taxon _l
				on _a.taxon_id = _l.taxon_id
				and _a.project_id = _l.project_id
				and _l.overview_image=1

			where _a.project_id =".$this->getCurrentProjectId()."
			and _f.lower_taxon=1
			and _a.language_id =".LANGUAGE_ID_DUTCH."
			and _a.name like '%".mysql_real_escape_string($search['search'])."%'
			and (_b.nametype='".PREDICATE_PREFERRED_NAME."' or _b.nametype='".PREDICATE_VALID_NAME."')
			order by 
				match_percentage desc, ".
				(!empty($search['sort']) && $search['sort']=='preferredNameNl' ? "dutch_name" : "taxon" )."
			limit ".(!empty($search['limit']) ? intval($search['limit']) : "1000" )."
			"
		);

		return $d;
	}


	private function doExtendedSearch($search)
	{
		if (is_numeric($search)) {

			$ancestor=$search;

		} else {

			$d=$this->models->Taxon->freeQuery("
				select

					_a.taxon_id,
					_a.uninomial,
					_a.authorship,
					_a.name,
					_b.nametype,
					_f.lower_taxon
	
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
	
				where _a.project_id =".$this->getCurrentProjectId()."
					and _f.lower_taxon!=1
					and (
							(
								_a.uninomial = '".mysql_real_escape_string($search['name'])."' and
								_b.nametype = '".PREDICATE_VALID_NAME."'
							)
							or
							(
								_a.name = '".mysql_real_escape_string($search['name'])."' and
								_b.nametype = '".PREDICATE_PREFERRED_NAME."' and
								_a.language_id=".LANGUAGE_ID_DUTCH."
							)
						)
				"
			);
			
			if ($d) $ancestor=$d[0]['taxon_id'];
	
		}
		

		if (empty($ancestor))
			return null;
			
		$this->tmp=array();
		$this->getChildren($ancestor);
		q(count($this->tmp),1);

		// 		iterate: get where parent=id
		//			when rank >= species, save
		//	sort
		//	print
		
	}
	
	
	
	private function getChildren($parent)
	{

		/*
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
			
			right join %PRE%taxa _e
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

			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%media_taxon _l
				on _a.taxon_id = _l.taxon_id
				and _a.project_id = _l.project_id
				and _l.overview_image=1

			where _a.project_id =".$this->getCurrentProjectId()."
			and _e.parent_id = ".$parent
		);
		*/

		$d=$this->models->Taxon->freeQuery("
			select
				_a.taxon_id,
				_a.name,
				_f.lower_taxon
			
			from %PRE%names _a
			
			right join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id
				
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			where _a.project_id =".$this->getCurrentProjectId()."
			and _e.parent_id = ".$parent
		);
			
		foreach((array)$d as $val) {
			if ($val['lower_taxon']==1)
				$this->tmp[]=$val;
			//if (count($this->tmp)>1000) return;
			$this->getChildren($val['taxon_id']);
		}
		
	}

	private function getPhotographersPictureCount($p=null)
	{

		$id=isset($p['name']) ? $p['name'] : null;
		$limit=isset($p['limit']) ? $p['limit'] : 5;
		
		$where=
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'sys_label' => 'beeldbankFotograaf'
			);

		if (isset($id)) $d['meta_data']=$name;
		
		$params=
			array(
				'id'=> $where,
				'columns'=>'count(*) as total, meta_data',
				'group'=>'meta_data',
				'order'=>'count(*) desc',
				'limit'=>5
			);


		if (isset($limit)) $params['limit']=$limit;

		$mm=$this->models->MediaMeta->_get($params);

		foreach((array)$mm as $key=>$val) {

			$d=$this->models->Taxon->freeQuery("
				select 
					count(distinct _a.taxon_id) as taxon_count
				from 
					%PRE%media_taxon _a
	
				right join %PRE%media_meta _b
					on _a.project_id=_b.project_id
					and _a.id = _b.media_id
					and _b.sys_label = 'beeldbankFotograaf'
					and _b.meta_data = '". mysql_real_escape_string($val['meta_data'])."'
				
				where
					_a.project_id=".$this->getCurrentProjectId()
			);
			
			$mm[$key]['taxon_count']=$d[0]['taxon_count'];
		}
		
		return $mm;
		
	}


	private function doPictureSearch($search)
	{
		$d=$this->models->MediaTaxon->freeQuery("
			select 
				_a.file_name,
				_a.thumb_name,
				_a.taxon_id,
				_c.meta_data as photographer_name,
				_d.name,
				trim(
					ifnull(
						concat(_j.uninomial,' ',_j.specific_epithet,' ',_j.infra_specific_epithet),
						replace(_j.name,_j.authorship,'')
					) 
				)  as taxon
			from %PRE%media_taxon _a ".
			(!empty($search['photographer']) ? "
				right join %PRE%media_meta _b
					on _a.project_id=_b.project_id
					and _a.id=_b.media_id
					and _b.sys_label='beeldbankFotograaf'
					and _b.meta_data like '%".mysql_real_escape_string($search['photographer'])."%'"
					: ""
			).
			(!empty($search['taxon']) || !empty($search['higherTaxon']) ? "
				right join %PRE%names _d
					on _a.project_id=_d.project_id
					and _a.taxon_id=_d.taxon_id"
				: ""
			)."
			left join %PRE%media_meta _c
				on _a.project_id=_c.project_id
				and _a.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'

			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%names _j
				on _a.taxon_id=_j.taxon_id
				and _a.project_id=_j.project_id
				and _j.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."

			where
				_a.project_id=".$this->getCurrentProjectId()."

				".(!empty($search['taxon']) ? "and (_d.name like '%".mysql_real_escape_string($search['taxon'])."%' and _f.lower_taxon=1)" : "")."
				".(!empty($search['higherTaxon']) ? "and (_d.name like '%".mysql_real_escape_string($search['higherTaxon'])."%' and _f.lower_taxon=0)" : "")."
				
			order by ".($search['sort']=='photographer' ? "photographer_name" : "taxon" )."

			limit ".(!empty($search['limit']) ? intval($search['limit']) : "100" )
		);
	
		//q($this->models->MediaTaxon->q());
		//q($d,1);
		return $d;
		
	}




    public function searchPicturesAction ()
    {

		if (
			$this->rHasVal('taxon') ||
			$this->rHasVal('higherTaxon')||
			$this->rHasVal('photographer')||
			$this->rHasVal('validator')
		)
		{
			
			$search=$this->requestData;

			$this->smarty->assign('search',$search);	

			//q($this->doPictureSearch($search),1);	
			$this->smarty->assign('results',$this->doPictureSearch($search));	
			
			
			

/*
			$_SESSION['app'][$this->spid()]['search_picture'] = array(
				'taxon' => $this->requestData['taxon'],
				'higherTaxon' => $this->requestData['higherTaxon'],
				'photographer' => $this->requestData['photographer'],
				'validator' => $this->requestData['validator']
				);

*/


/*
select _a.meta_data, _b.original_name, _c.taxon
from %PRE%media_meta _a

left join %PRE%media_taxon _b
	on _a.project_id=_b.project_id
	and _a.media_id=_b.id 

left join %PRE%taxa _c
	on _a.project_id=_c.project_id
	and _b.taxon_id=_c.id

	where
	_a.project_id=1 and
	(sys_label = 'beeldbankFotograaf' and meta_data like '%De Vos%') 
*/

		
//			if ($this->validateSearchString($this->requestData['search'])) {
	
				
//			}
			
		}

		//$this->smarty->assign('search',isset($_SESSION['app'][$this->spid()]['search']) ? $_SESSION['app'][$this->spid()]['search'] : null);

		$this->smarty->assign('photographers',$this->getPhotographersPictureCount());

        $this->printPage();
  
    }




}