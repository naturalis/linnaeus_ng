<?php

include_once ('Controller.php');
include_once ('SearchController.php');

class SearchControllerNSR extends SearchController
{

	private $_suggestionListItemMax=25;

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
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    public function searchAction ()
    {
		if ($this->rHasVal('search')) {

			$search=$this->requestData;
			$results=$this->doSearch($search);
			$this->smarty->assign('search',$search);	
			$this->smarty->assign('results',$results);	

		}
		
		$searchType=isset($this->requestData['type']) ? $this->requestData['type'] : null;

		$this->smarty->assign('type',$searchType);
		$this->smarty->assign('search',$search);

        $this->printPage();
	}


    public function searchExtendedAction ()
    {
		
		if (
			$this->rHasVal('group') ||
			$this->rHasVal('group_id') ||
			$this->rHasVal('author') ||
			$this->rHasVal('author_id') ||
			$this->rHasVal('presence') ||
			$this->rHasVal('images') ||
			$this->rHasVal('distribution') ||
			$this->rHasVal('trend') ||
			$this->rHasVal('dna') ||
			$this->rHasVal('dna_insuff')
		) {
	
			$search=$this->requestData;

			$d=$this->doExtendedSearch($search);

			$this->smarty->assign('results',$d['data']);
			$this->smarty->assign('result_count',$d['count']);

//			if (is_numeric($search['group']))
//				$search['name']=$d['ancestor']['name'].(!empty($d['ancestor']['dutch_name']) ? ' ['.$d['ancestor']['dutch_name'].']' : '');

		}

		$this->smarty->assign('search',isset($search) ? $search : $this->requestData);	

		$this->smarty->assign('presence_statuses',$this->getPresenceStatuses());

        $this->printPage();

q($this->requestData);

    }


    public function ajaxInterfaceAction ()
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
        if ($this->rHasVal('action','name_suggestions'))
		{
	        if (!$this->rHasVal('search')) return;
			$this->smarty->assign('returnText',json_encode($this->getSuggestionsName($this->requestData)));
        }

        $this->printPage();
    
    }


    public function searchPicturesAction ()
    {
		if (
			$this->rHasVal('name') ||
			$this->rHasVal('name_id')||
			$this->rHasVal('group')||
			$this->rHasVal('group_id') ||
			$this->rHasVal('photographer') ||
			$this->rHasVal('photographer')||
			$this->rHasVal('validator')
		)
		{
			
			$this->smarty->assign('search',$this->requestData);	
			$results = $this->doPictureSearch($this->requestData);
			$this->smarty->assign('results',$results);	
			
		}

		$this->smarty->assign('photographers',$this->getPhotographersPictureCount());

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
		if (!empty($search['group_id'])) {
			$d=$this->getSuggestionsGroup(array('id'=>(int)trim($search['group_id']),'match'=>'id'));
		} else {
			$d=$this->getSuggestionsGroup(array('search'=>$search['group'],'match'=>'exact'));
		}
		
		if ($d) 
			$ancestor=$d[0];
		else
			return null;

		$img=!empty($search['images']);

		$dna=(!empty($search['dna']) || !empty($search['dna_insuff']));

		$dna_insuff=!empty($search['dna_insuff']);

		if (!empty($search['author'])) $auth=$search['author'];

		if (!empty($search['presence'])) {
			$pres=array();
			foreach((array)$search['presence'] as $key=>$val) {
				if ($val=='on') $pres[]=intval($key);
			}
		}

		if (!empty($search['limit'])) $limit=$search['limit'];

		if (!empty($search['offset'])) $offset=$search['offset'];


		$data=$this->models->Taxon->freeQuery("
			select
				SQL_CALC_FOUND_ROWS
				_a.id,
				_a.taxon,
				_k.name as dutch_name,
				".($img ? "ifnull(_i.number_of_images,0) as number_of_images," : "" )."
				".($dna ? "ifnull(_j.number_of_barcodes,0) as number_of_barcodes," : "" )."
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				_l.file_name as overview_image
			
			from %PRE%taxa _a

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			". (isset($auth) ? "
				left join %PRE%names _m
					on _a.id=_m.taxon_id
					and _a.project_id=_m.project_id
					and _m.type_id=(select id from %PRE%name_types where project_id = ".
						$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
					and _m.language_id=".LANGUAGE_ID_SCIENTIFIC : "" 
				)."

			". ($img ? "
				left join
					(select project_id,taxon_id,count(*) as number_of_images from %PRE%media_taxon group by project_id,taxon_id) as _i
						on _a.id=_i.taxon_id
						and _i.project_id=_a.project_id" :  "" 
				)."
			
			". ($dna ? "
				left join
				(select project_id,taxon_id,count(*) as number_of_barcodes from %PRE%dna_barcodes group by project_id,taxon_id) as _j
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

			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _f.lower_taxon=1
				and MATCH(_q.parentage) AGAINST ('".$ancestor['id']."' in boolean mode)
			".(isset($pres) ? "and _g.presence_id in (".implode(',',$pres).")" : "")."
			".(isset($auth) ? "and _m.name_author like '". mysql_real_escape_string($auth)."%'" : "")."
			".($img ? "and number_of_images > 0" : "")."
			".($dna ? "and number_of_barcodes ".($dna_insuff ? "between 1 and 3" : "> 0") : "")."

			order by ".($search['sort']=='name-pref-nl' ? "dutch_name" : "_a.taxon")."
			".(isset($offset) ? "offset ".$offset : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			"
		);
		
		
		//q($this->models->Taxon->q());

		$count=$this->models->Taxon->freeQuery('select found_rows() as total');

		return array('count'=>$count[0]['total'],'data'=>$data,'ancestor'=>$ancestor);
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

		$d=$this->models->MediaTaxon->freeQuery("
			select 
			
				_a.id,
				_a.taxon_id,
				_a.file_name,
				_a.thumb_name,
				_c.meta_data as photographer_name,
				_f.lower_taxon,
				_j.name,
				_b.taxon,
				_k.name as dutch_name,
				_meta1.meta_data as meta_datum,
				_meta2.meta_data as meta_short_desc
				
			from  %PRE%media_taxon _a 		

			left join %PRE%media_meta _c
				on _a.project_id=_c.project_id
				and _a.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'

			left join %PRE%media_meta _meta1
				on _a.id=_meta1.media_id
				and _a.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _a.id=_meta2.media_id
				and _a.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijvingKort'

			left join %PRE%names _k
				on _a.taxon_id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."
			
			left join %PRE%taxa _b
				on _a.taxon_id = _b.id
				and _a.project_id = _b.project_id
			
			left join %PRE%projects_ranks _f
				on _b.rank_id=_f.id
				and _b.project_id = _f.project_id

			".(!empty($group_id) ? "left join %PRE%taxon_quick_parentage _q
				on _a.taxon_id=_q.taxon_id
				and _a.project_id=_q.project_id
				" : "" )."
			
			left join %PRE%names _j
				on _a.taxon_id=_j.taxon_id
				and _a.project_id=_j.project_id
				and _j.type_id=(select id from %PRE%name_types where project_id = ".$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."
			
			where _a.project_id = ".$this->getCurrentProjectId()."
			
				".(!empty($p['name_id']) ? "and _a.taxon_id = ".intval($p['name_id'])." and _f.lower_taxon=1"  : "")." 		
				".(!empty($p['name']) && empty($p['name']) ? "and _j.name like '". mysql_real_escape_string($p['name'])."%' and _f.lower_taxon=1"  : "")." 		
				".(!empty($p['photographer'])  ? "and _c.meta_data like '". mysql_real_escape_string($p['photographer'])."%'"  : "")." 		
				".(!empty($group_id) ? "and  MATCH(_q.parentage) AGAINST ('".$group_id."' in boolean mode)"  : "")." 		

			order by ".($p['sort']=='photographer' ?  "_c.meta_data" : "_b.taxon")."
			".(isset($offset) ? "offset ".$offset : "")."
			".(isset($limit) ? "limit ".$limit : "")
			);

//		q($this->models->MediaTaxon->q(),1);
//		q($d,1);

		setlocale(LC_ALL, 'nl_NL.utf8');
		
		foreach((array)$d as $key=>$val) {
			$d[$key]['photographer']=implode(' ',array_reverse(explode(',',$val['photographer_name'])));
			$d[$key]['meta_datum']=strftime('%e %B %Y',strtotime($val['meta_datum']));
		};


		return $d;
		
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
				concat(_a.name,if(_k.name is null,'a',concat(' [',_k.name,']'))) as label

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
				and _g.language_id=".LANGUAGE_ID_DUTCH."

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
				and _k.language_id=".LANGUAGE_ID_DUTCH."

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
							_a.language_id=".LANGUAGE_ID_DUTCH."
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
			$clause="name_author like '".mysql_real_escape_string($p['search'])."%'";
		else
		if ($p['match']=='exact')
			$clause="name_author = '".mysql_real_escape_string($p['search'])."'";

		if (empty($clause)) return;
		
		$d=$this->models->Taxon->freeQuery("
			select
				distinct name_author as label
			from %PRE%names
			where 
				project_id =".$this->getCurrentProjectId()."
				and ".$clause."
			order by name_author
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

		if (empty($clause)) return;
		

		$d=$this->models->MediaTaxon->freeQuery("
			select 
				distinct meta_data as label
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
					'language_id' => LANGUAGE_ID_DUTCH
				),
				'columns'=>'id'
			)
		);
		
		$typeId=$d[0]['id'];


		$d=$this->models->MediaTaxon->freeQuery("
			select
				distinct 
				_a.taxon_id as id,
				if (_a.type_id=".$typeId.",_a.name,concat(_a.name,if(_c.name is null,'',concat(' - [',_c.name,']')))) as label
			from %PRE%names _a
			
			right join %PRE%media_taxon _b
				on _a.taxon_id = _b.taxon_id
				and _a.project_id = _b.project_id
			
			left join %PRE%names _c
				on _a.taxon_id=_c.taxon_id
				and _a.project_id=_c.project_id
				and _c.type_id=".$typeId."
				and _c.language_id=".LANGUAGE_ID_DUTCH."
			
			left join lng_nsr_taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
			
			left join lng_nsr_projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id
				
			where 
				_a.project_id = ".$this->getCurrentProjectId()."
				and _f.lower_taxon=1
				and _a.name like '". mysql_real_escape_string($p['search']) ."%'
				and (_a.language_id=".LANGUAGE_ID_DUTCH." or _a.language_id=".LANGUAGE_ID_SCIENTIFIC.")
				order by label
			limit ".$this->_suggestionListItemMax
		);

		return $d;
		
	}




}