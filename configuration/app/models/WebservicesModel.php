<?php

include_once (__DIR__ . "/AbstractModel.php");

class WebservicesModel extends AbstractModel
{

    public function __construct ()
    {

        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

    }


    public function __destruct ()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

    public function getNames( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$from_date = isset($params['from_date']) ? $params['from_date'] : null;
		$rowcount = isset($params['rowcount']) ? $params['rowcount'] : null;
		$offset = isset($params['offset']) ? $params['offset'] : null;
		$url = isset($params['url']) ? $params['url'] : null;
		
		if ( is_null($project_id) || is_null($from_date) )
			return;
		
		$query="
			select
				SQL_CALC_FOUND_ROWS
				_a.id as name_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_c.language,
				_c.iso3 as language_iso3,
				if(_a.last_change='0000-00-00 00:00:00',_a.created,_a.last_change) as last_change,
				_b.nametype,
				_a.taxon_id, 
				_d.taxon, 
				_f.default_label as rank,
				concat('".$url."',_a.taxon_id) as url,
				_h.id as taxon_valid_name_id
			from %PRE%names _a
			
			left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
			left join %PRE%languages _c on _a.language_id=_c.id
			left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
			left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_e.project_id
			left join %PRE%ranks _f on _e.rank_id=_f.id
			
			left join %PRE%name_types _g on _a.project_id=_g.project_id and _g.nametype='isValidNameOf'
			left join %PRE%names _h on _h.taxon_id=_a.taxon_id and _h.type_id=_g.id and _a.project_id=_h.project_id
	
			left join %PRE%nsr_ids _i on _a.project_id=_i.project_id and _a.taxon_id=_i.lng_id and _i.item_type='taxon'
	
			where _a.project_id=".$project_id."
			and (
				(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$from_date."','%Y%m%d')) ||
				(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$from_date."','%Y%m%d'))
			)
			and _e.rank_id >= ".SPECIES_RANK_ID."
			and _d.taxon is not null".
			(!is_null($rowcount) ? ' limit '.$rowcount : '').
			(!is_null($rowcount) && !is_null($offset) ? ' offset '.$offset : '')
		;

		$data=$this->freeQuery( $query );
		//SQL_CALC_FOUND_ROWS
		$count=$this->freeQuery( "select found_rows() as total" );
				
		return array('data'=>$data,'count'=>$count[0]['total']);
	}

    public function getNameCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$from_date = isset($params['from_date']) ? $params['from_date'] : null;
		
		if ( is_null($project_id) || is_null($from_date) )
			return;
		
		$query="
			select
				count(_a.id) as total
			from
				%PRE%names _a
			left join %PRE%taxa _d
				on _a.taxon_id=_d.id 
				and _a.project_id=_d.project_id
			left join %PRE%projects_ranks _e
				on _d.rank_id=_e.id 
				and _a.project_id=_d.project_id
			where
				_a.project_id=".$project_id."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$from_date."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$from_date."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null";;
		
		return $this->freeQuery( $query );
	}

    public function resolveName( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$name = isset($params['name']) ? $params['name'] : null;
		
		if ( is_null($project_id) || is_null($name) )
			return;
		
		$query="
			select
				_a.taxon_id as id, _a.name
			from %PRE%names _a
			left join %PRE%name_types _b 
				on _a.type_id=_b.id and _a.project_id=_b.project_id
			where
				_a.project_id = ".$project_id."
				and trim(REPLACE(_a.name,_a.authorship,''))='". $this->escapeString($name) ."'
				and _b.nametype = 'isValidNameOf'"
		;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;
	}

    public function getTaxonSummary( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.content, _b.page
			from %PRE%content_taxa _a, %PRE%pages_taxa _b
			where _a.project_id=".$project_id."
			and _a. =".$taxon_id."
			and _a.language_id =".LANGUAGE_ID_DUTCH."
			and _b.project_id=".$project_id."
			and _a.page_id=_b.id
			and _b.page='Summary_dutch'
			";

		$d=$this->freeQuery( $query );
		return isset($d[0]) && isset($d[0]['content']) ? strip_tags($d[0]['content']) : null;

	}

    public function getTaxonNames( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.id as name_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_c.language,
				_c.iso3 as language_iso3,
				_b.nametype
			from %PRE%names _a
			
			left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
			left join %PRE%languages _c on _a.language_id=_c.id
			left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id

			where _a.project_id=".$project_id."
			and _a.taxon_id =".$taxon_id
		;

		return $this->freeQuery( $query );
	}

    public function getTaxonMedia( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$image_base_url = isset($params['image_base_url']) ? $params['image_base_url'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) )
			return;
		
		$query="
			select
				_a.id as media_id,
				concat('".$image_base_url."',_a.file_name) as url,
				_b.meta_data as copyright,
				_c.meta_data as caption,
				_d.meta_data as creator,
				date_format(_e.meta_date,'%e %M %Y') as date_created

			from %PRE%media_taxon _a

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _c
				on _a.id=_c.media_id and _a.project_id=_c.project_id and _c.sys_label = 'beeldbankCaption'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumVervaardiging'

			where
				_a.project_id = ".$project_id."
				and _a.taxon_id = ".$taxon_id."
			order by
				_e.meta_date desc
			" . (isset($limit) ? "limit " . $limit : null );

		return $this->freeQuery( $query );
	}

	public function getTaxonParentage ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

        if ( is_null($projectId) || is_null($taxonId) ) return;

        $query = "
                select
                    parentage
                from
                    %PRE%taxon_quick_parentage
                where
                    project_id = ".$projectId."
                    and taxon_id = ".$taxonId;

        $d = $this->freeQuery($query);

        return !empty($d) ? explode(' ',$d[0]['parentage']) : null;
    }

    public function getTaxonPage( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$page_id = isset($params['page_id']) ? $params['page_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) || is_null($page_id) )
			return;
		
		$query="
			select
				_b.id,
				ifnull(_c.title,_a.page) as title,
				_b.content

			from
				%PRE%pages_taxa _a
				
			left join %PRE%content_taxa _b
				on _a.id=_b.page_id
				and _a.project_id=_b.project_id
				and _b.language_id =".LANGUAGE_ID_DUTCH."
				and _b.taxon_id =".$taxon_id."
				
			left join %PRE%pages_taxa_titles _c
				on _a.id=_c.page_id
				and _a.project_id=_c.project_id
				and _c.language_id =".LANGUAGE_ID_DUTCH."

			where
				_a.project_id=".$project_id."
				and _a.id=" . $page_id;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;

	}

    public function getRandomRecentImage( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$pool_size = isset($params['pool_size']) ? $params['pool_size'] : null;
		$img_base_url = isset($params['img_base_url']) ? $params['img_base_url'] : null;

		if ( is_null($project_id) || is_null($pool_size) || is_null($img_base_url) )
			return;
		
		$query="
			select 
				_a.media_id

			from
				%PRE%media_meta _a

			left join %PRE%media_meta _meta9
				on _a.media_id=_meta9.media_id
				and _a.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			where 
				_a.sys_label = 'beeldbankDatumAanmaak'
				and _a.project_id = ".$project_id."
				and ifnull(_meta9.meta_data,0)!=1

			order by 
				_a.meta_date desc

			limit ".$pool_size."
		";

		$d=$this->freeQuery( $query );

		$ids=array();

		foreach((array)$d as $val)
		{
			$ids[]=$val['media_id'];
		}
		
		$ids=implode(',',$ids);

		$query="
			select
				_a.taxon_id,
				_a.id as media_id,
				concat('".$img_base_url."',_a.file_name) as url_image,
				_a.file_name,
				_b.meta_data as copyright,
				_d.meta_data as fotograaf,
				date_format(_e.meta_date,'%e %M %Y') as date_created,
				_f.meta_data as lokatie,
				_g.meta_data as validator,
				_k.name as dutch_name,
				trim(replace(ifnull(_m.name,''),ifnull(_m.authorship,''),'')) as scientific_name

			from %PRE%media_taxon _a
			
			left join %PRE%names _k
				on _a.taxon_id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".
					$project_id." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _m
				on _a.taxon_id=_m.taxon_id
				and _a.project_id=_m.project_id
				and _m.type_id=(select id from %PRE%name_types where project_id = ".
					$project_id." and nametype='".PREDICATE_VALID_NAME."')
				and _m.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumAanmaak'
			left join %PRE%media_meta _f
				on _a.id=_f.media_id and _a.project_id=_f.project_id and _f.sys_label = 'beeldbankLokatie'
			left join %PRE%media_meta _g
				on _a.id=_g.media_id and _a.project_id=_g.project_id and _g.sys_label = 'beeldbankValidator'

			where
				_a.project_id = ".$project_id."
				and _a.id in (".$ids.")

			order by rand() limit 0,1
		";


		return $this->freeQuery( $query );

	}

    public function getEstablishedExoticAllTaxa( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;
		
		$query="
			select
				count(*) as total,
				_h.id as presence_id,
				_h.established as established

			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%presence_taxa _g
				on _a.id=_g.taxon_id
				and _a.project_id=_g.project_id

			left join %PRE%presence _h
				on _g.presence_id=_h.id
				and _g.project_id=_h.project_id

			where
				_a.project_id =".$project_id."
				and _f.rank_id = ".SPECIES_RANK_ID."

			group by 
				_h.id
		";

		$d=$this->freeQuery( $query );

		$result['all']=0;
		$result['all_established']=0;
		$result['established_exotic']=0;
		/*
		6	2a Exoot. Minimaal 100 jaar zelfstandige handhaving
		3	2b Exoot. Tussen 10 en 100 jaar zelfstandige handhaving
		*/
		foreach((array)$d as $key=>$val)
		{
			$result['all']+=$val['total'];

			if ($val['established']=='1')
			{
				$result['all_established']+=$val['total'];
			}

			if ($val['presence_id']==3 || $val['presence_id']==6)
			{
				$result['established_exotic']+=$val['total'];
			}
		}

		$result['all']=$this->format_number($result['all']);
		$result['all_established']=$this->format_number($result['all_established']);
		$result['established_exotic']=$this->format_number($result['established_exotic']);
		$result['main_count']=$result['all_established'];  // backward compat NSR
		
		return $result;
	}

    public function getTaxonMediaCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;
		
		$query="
			select
				count(distinct taxon_id) as total

			from %PRE%media_taxon _a

			where
				_a.project_id = ".$project_id
		;

		$d=$this->freeQuery( $query );
		
        $query="
			select
				count(_m.id) as total

			from
				%PRE%media_taxon _m
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'
			
			where
				_m.project_id = ".$project_id."
				and ifnull(_meta9.meta_data,0)!=1
				and ifnull(_trash.is_deleted,0)=0
		";
		
		$e=$this->freeQuery( $query );
		
		return array(
			"species_with_image"=>$this->format_number($d[0]['total']),
			"images"=>$this->format_number($e[0]['total'])
		);
	}

    public function getNameTypeCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;
		
		$query="
				select
					count(_a.id) as total,
					_b.nametype,
					_a.language_id
				
				from %PRE%names _a
				
				left join %PRE%name_types _b
					on _a.project_id = _b.project_id
					and _a.type_id = _b.id
				
				where
					_a.project_id = ".$project_id."
				group by _a.language_id,_b.nametype"
		;
		
		$d=$this->freeQuery( $query );

		$t['count_name_accepted']=$t['count_name_dutch']=$t['count_name_english']=0;
		
		foreach((array)$d as $key => $val)
		{
			if ($val['nametype']=='isValidNameOf')
				$t['count_name_accepted']+=$val['total'];

			if ($val['language_id']==LANGUAGE_ID_DUTCH)
				$t['count_name_dutch']+=$val['total'];
			/*
			if ($val['language_id']==LANGUAGE_ID_ENGLISH)
				$t['count_name_english']+=$val['total'];
			*/
		}
		
		return array(
			'accepted_names'=>$this->format_number($t['count_name_accepted']),
			'dutch_names'=>$this->format_number($t['count_name_dutch']),
//			'english_names'=>$this->format_number($t['count_name_english']),
		);
	}
	
    public function getTaxonSpecialistCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;
		
		$query="
			select count(distinct id) as total from
			(
				select
					actor_id as id
				from %PRE%presence_taxa
				
				where
					project_id = ".$project_id."
					and actor_id is not null
					
				union

				select
					expert_id as id
				
				from %PRE%names
				
				where
					project_id = ".$project_id."
					and expert_id is not null
				) as unification"
		;

		$d=$this->freeQuery( $query );

		return $this->format_number($d[0]['total']);
	}

    public function getExoticsPassportCount( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$group_id = isset($params['group_id']) ? $params['group_id'] : null;

		if ( is_null($project_id) || is_null($group_id) )
			return;
		
		$query="
			select count(distinct taxon_id) as total from
				(select
					_ttv.taxon_id
	
				from %PRE%traits_taxon_values _ttv
	
				right join %PRE%traits_values _tv
					on _ttv.project_id = _tv.project_id
					and _ttv.value_id = _tv.id
	
				right join %PRE%traits_traits _tt1
					on _tv.project_id = _tt1.project_id
					and _tv.trait_id = _tt1.id
					and _tt1.trait_group_id=".$group_id."
	
				left join %PRE%trash_can _trash
					on _ttv.project_id = _trash.project_id
					and _ttv.taxon_id =  _trash.lng_id
					and _trash.item_type='taxon'
	
				where
					_ttv.project_id = " . $project_id ."
					and ifnull(_trash.is_deleted,0)=0

				union

				select
					_ttf.taxon_id
	
				from %PRE%traits_taxon_freevalues _ttf
	
				right join %PRE%traits_traits _tt2
					on _ttf.project_id = _tt2.project_id
					and _ttf.trait_id = _tt2.id
					and _tt2.trait_group_id=".$group_id."
		
				left join %PRE%trash_can _trash
					on _ttf.project_id = _trash.project_id
					and _ttf.taxon_id =  _trash.lng_id
					and _trash.item_type='taxon'
	
				where
					_ttf.project_id = " . $project_id ."
					and ifnull(_trash.is_deleted,0)=0
				) as unionized
		";

		$d=$this->freeQuery( $query );

		return $d[0]['total'];
	}

    public function getSearchResults( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$search = isset($params['search']) ? $params['search'] : null;
		$match_type = isset($params['match_type']) ? $params['match_type'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : 10;

		if ( is_null($project_id) || is_null($language_id)  || is_null($search)  || is_null($match_type) )
			return;
		
		$query="
			select
				_a.name,
				_b.nametype,
				_e.taxon,
				_q.label as common_rank,
				replace(_r.nsr_id,'tn.nlsr.concept/','') as nsr_id,
				_d.label as language_label
			
			from %PRE%names _a

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _d.label_language_id=".$language_id."
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _q
				on _e.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$language_id."
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%nsr_ids _r
				on _a.project_id = _r.project_id
				and _a.taxon_id=_r.lng_id
				and _r.item_type = 'taxon'

			where _a.project_id =".$project_id."
			". ($match_type=='match_start' ? 
					"and _a.name like '".$this->escapeString($search)."%'" :
					"and _a.name like '%".$this->escapeString($search)."%'" 
			)."
				and (_b.nametype='".PREDICATE_PREFERRED_NAME."' or _b.nametype='".PREDICATE_VALID_NAME."' or _b.nametype='".PREDICATE_ALTERNATIVE_NAME."')
			
			group by _a.taxon_id
			order by _a.name
			limit ".$limit."

		";

		return $this->freeQuery( $query );
	}

    public function getNsrId( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$nsr_id = isset($params['nsr_id']) ? $params['nsr_id'] : null;
		$item_type = isset($params['item_type']) ? $params['item_type'] : 'taxon';
		
		if ( is_null($project_id) || is_null($nsr_id) )
			return;
		
		$query="
			select
				* 
			from
				%PRE%nsr_ids
			where 
				project_id = " . $project_id . "
				and nsr_id = 'tn.nlsr.concept/". str_pad( $this->escapeString($nsr_id) ,12,'0',STR_PAD_LEFT) . "'
				and item_type = '" . $item_type ."'
			";

		return $this->freeQuery( $query );			
	}

	public function getTreeBranch( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$node = isset($params['node']) ? $params['node'] : null;

		if ( is_null($project_id) || is_null($node) ) return;

		$d=$this->getSingleLevel( $params );

		foreach((array)$d as $key=>$val)
		{
			$d[$key]['children']=$this->getSingleLevel( ['project_id'=>$project_id, 'node'=>$val['id'] ] );
			unset($d[$key]['id']);
			if ($d[$key]['children']) array_walk($d[$key]['children'],function(&$a) { unset($a['id']); } );
		}

		return $d;
	}

	private function getSingleLevel( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$node = isset($params['node']) ? $params['node'] : null;

		if ( is_null($project_id) || is_null($node) ) return;

		$query="
			select
				_a.id,
				_a.taxon,
				_r.rank

			from
				%PRE%taxa _a

			left join
				%PRE%taxon_quick_parentage _sq
				on _a.project_id = _sq.project_id
				and _a.id=_sq.taxon_id

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$project_id." 
				and ifnull(_trash.is_deleted,0)=0
				and _a.parent_id = ".$node."

			order by
				_p.rank_id, _a.taxon
		";

		return $this->freeQuery($query);
	}



	private function format_number($n)
	{
		return number_format($n,0,',','.');
	}

			
		



}
