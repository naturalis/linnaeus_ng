<?php
include_once (__DIR__ . "/AbstractModel.php");

final class SearchNSRModel extends AbstractModel
{
	
	private $_operators;
	private $_nameTypeIds;

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
	
	public function setNameTypeIds( $p )
	{
		$this->_nameTypeIds=$p;
	}

	public function getPresenceStatuses( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		
		if ( is_null($project_id) ||  is_null($language_id) )
			return;
		
		$query="
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
				and _b.language_id = " . $language_id . "

			where
				_a.project_id=" . $project_id . "
				and _b.index_label is not null
			order by 
				_b.index_label
		";
			
		return $this->freeQuery( array( "query"=>$query, "fieldAsIndex"=>"id") );
		
	}

	public function getTaxonOverviewImage( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id) ||  is_null($taxon_id) )
			return;

		$query="
			select

				_m.file_name
			
			from  %PRE%media_taxon _m

			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'
			
			where
				_m.project_id=".$project_id."
				and _m.taxon_id=".$taxon_id."
				and ifnull(_meta9.meta_data,0)!=1

			order by 
				_m.overview_image desc,_meta4.meta_date desc,_meta1.meta_date desc

			limit 1
		";
			
		$d=$this->freeQuery( $query );
		return (isset($d[0]) && isset($d[0]['file_name']))  ? $d[0]['file_name'] : null;

	}

	public function doSearch( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$search = isset($params['search']) ? $params['search'] : null;
		$nsr_id_prefix = isset($params['nsr_id_prefix']) ? $params['nsr_id_prefix'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$sort = isset($params['sort']) ? $params['sort'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$offset = isset($params['offset']) ? $params['offset'] : null;
		
		if ( is_null($project_id) ||  is_null($search) ||  is_null($language_id) )
			return;

		$search=$this->escapeString($search);
		
		$query="
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
				_e.parent_id,
				_x.id as base_rank_id,
				_f.lower_taxon,
				_k.name as common_name,
				ifnull(_q.label,_x.rank) as common_rank,
				_g.presence_id,
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				ifnull(_j.number_of_barcodes,0) as number_of_barcodes,
	
				case
					when
						_a.name REGEXP '^".$search."$' = 1
						or
						trim(concat(
							if(_a.uninomial is null,'',concat(_a.uninomial,' ')),
							if(_a.specific_epithet is null,'',concat(_a.specific_epithet,' ')),
							if(_a.infra_specific_epithet is null,'',concat(_a.infra_specific_epithet,' '))
						)) REGEXP '^".$search."$' = 1
					then 100
					when
						_a.name REGEXP '^".$search."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 95
					when
						_a.name REGEXP '^(.*)[[:<:]]".$search."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 90
					when
						_a.name REGEXP '^".$search."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 85
					when
						_a.name REGEXP '^(.*)[[:<:]]".$search."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 80
					when 
						_a.name REGEXP '^(.*)".$search."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 75
					when
						_a.name REGEXP '^".$search."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 70
					when
						_a.name REGEXP '^(.*)[[:<:]]".$search."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 65
					when
						_a.name REGEXP '^".$search."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 60
					when
						_a.name REGEXP '^(.*)[[:<:]]".$search."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 55
					when 
						_a.name REGEXP '^(.*)".$search."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 50

					else 10
				end as match_percentage,
				replace(_ids.nsr_id,'".$nsr_id_prefix."','') as nsr_id
				
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
				and _q.language_id=".$language_id."
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%presence_taxa _g
				on _a.taxon_id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id = _h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$language_id."

			left join
				(select project_id,taxon_id,count(*) as number_of_barcodes from %PRE%dna_barcodes group by project_id,taxon_id) as _j
				on _a.taxon_id=_j.taxon_id
				and _j.project_id=_a.project_id
									
			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _k.language_id=".$language_id."

			left join %PRE%nsr_ids _ids
				on _a.taxon_id=_ids.lng_id
				and _a.project_id=_ids.project_id
				and _ids.item_type='taxon'

			where _a.project_id =".$project_id."
				and _a.name like '%".$search."%' 
				and _a.type_id in (
					" . $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_ALTERNATIVE_NAME]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_SYNONYM]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_SYNONYM_SL]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_HOMONYM]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_BASIONYM]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_MISSPELLED_NAME]['id'] . ",
					" . $this->_nameTypeIds[PREDICATE_INVALID_NAME]['id'] . "
				)
				and ifnull(_trash.is_deleted,0)=0
		
			group by
				_a.taxon_id

			order by 
				match_percentage desc, _e.taxon asc, _f.rank_id asc, ".
				(isset($sort) && $sort=='preferredNameNl' ? "common_name" : "taxon" )."
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		;

		$data=$this->freeQuery( $query );
		//SQL_CALC_FOUND_ROWS
		$count=$this->freeQuery( "select found_rows() as total" );
		
		return array('data'=>$data,'count'=>$count[0]['total']);

	}

	public function doExtendedSearch( $params )
    {
		$images=isset($params['images']) ? $params['images'] : null;
		$images_on=isset($params['images_on']) ? $params['images_on'] : null;
		$images_off=isset($params['images_off']) ? $params['images_off'] : null;
		$auth=isset($params['auth']) ? $params['auth'] : null;
		$trend=isset($params['trend']) ? $params['trend'] : null;
		$trend_on=isset($params['trend_on']) ? $params['trend_on'] : null;
		$trend_off=isset($params['trend_off']) ? $params['trend_off'] : null;
		$distribution=isset($params['distribution']) ? $params['distribution'] : null;
		$distribution_on=isset($params['distribution_on']) ? $params['distribution_on'] : null;
		$distribution_off=isset($params['distribution_off']) ? $params['distribution_off'] : null;
		$dna=isset($params['dna']) ? $params['dna'] : null;
		$dna_insuff=isset($params['dna_insuff']) ? $params['dna_insuff'] : null;
		$nsr_id_prefix=isset($params['nsr_id_prefix']) ? $params['nsr_id_prefix'] : null;
		$traits=isset($params['traits']) ? $params['traits'] : null;
		$trait_group=isset($params['trait_group']) ? $params['trait_group'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$ancestor_id=isset($params['ancestor_id']) ? $params['ancestor_id'] : null;
		$presence=isset($params['presence']) ? $params['presence'] : null;
		$sort=isset($params['sort']) ? $params['sort'] : null;
		$limit=isset($params['limit']) ? $params['limit'] : null;
		$offset=isset($params['offset']) ? $params['offset'] : null;
		$specific_rank=isset($params['specific_rank']) ? $params['specific_rank'] : null;
		$this->_operators=isset($params['operators']) ? $params['operators'] : null;

		if ( is_null($project_id) ||  is_null($language_id) )
			return;

		$trait_joins=$this->getTraitJoins( [ "traits" => $traits, "project_id" => $project_id ] );
		$traitgroup_joins=$this->getTraitGroupJoin( $trait_group );
		
		$query="
			select
				SQL_CALC_FOUND_ROWS
				_a.id,
				_a.id as taxon_id,
				_a.parent_id,
				_f.rank_id as base_rank_id,
				_a.taxon,
				_k.name as common_name,
				".( $images ? "ifnull(_i.number_of_images,0) as number_of_images," : "" )."
				".( $trend ? "ifnull(_trnd.number_of_trend_years,0) as number_of_trend_years," : "" )."
				".( $distribution ? "ifnull(_ii.number_of_maps,0) as number_of_maps," : "" )."
				".( $dna ? "ifnull(_j.number_of_barcodes,0) as number_of_barcodes," : "" )."
				_h.information_title as presence_information_title,
				_h.index_label as presence_information_index_label,
				_l.file_name as overview_image,
				replace(_ids.nsr_id,'".$nsr_id_prefix."','') as nsr_id

			from %PRE%taxa _a
			
			" . $trait_joins . "
			" . ( isset($traitgroup_joins['join']) ? $traitgroup_joins['join'] : "" ) . "

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _k.language_id=".$language_id."

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

			".(isset($ancestor_id) ? "
				right join %PRE%taxon_quick_parentage _q
					on _a.id=_q.taxon_id
					and _a.project_id=_q.project_id" : ""
			)."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%presence_taxa _g
				on _a.id=_g.taxon_id
				and _a.project_id=_g.project_id
			
			left join %PRE%presence_labels _h
				on _g.presence_id=_h.presence_id 
				and _g.project_id=_h.project_id 
				and _h.language_id=".$language_id."
			
			left join %PRE%media_taxon _l
				on _a.id=_l.taxon_id
				and _a.project_id=_l.project_id
				and _l.overview_image=1

			left join %PRE%nsr_ids _ids
				on _a.id=_ids.lng_id
				and _a.project_id=_ids.project_id
				and _ids.item_type='taxon'

			where
				_a.project_id =".$project_id."
				and ifnull(_trash.is_deleted,0)=0
				".(isset($specific_rank) ? "and _f.rank_id=" . $specific_rank : "and _f.lower_taxon=1" )." 
				".(isset($ancestor_id) ? "and (MATCH(_q.parentage) AGAINST ('". $this->generateTaxonParentageId( $ancestor_id ) ."' in boolean mode) or _a.id = ".$ancestor_id.")" : "")."
				".(isset($presence) ? "and _g.presence_id in (".implode(',',$presence).")" : "")."
				".(isset($auth) ? "and _m.authorship like '". $this->escapeString($auth)."%'" : "")."
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
				
				" . ( isset($traitgroup_joins['where']) ? $traitgroup_joins['where'] : "" ) . "
				" . ( !empty($trait_joins) || !empty($traitgroup_joins) ? "group by _a.id" : "" ) . "
				" . ( isset($traitgroup_joins['having']) ? $traitgroup_joins['having'] : "" ) . "

			order by
			" . (isset($sort) && $sort=='name-pref-nl' ? "ifnull(common_name,'zzzz'),_a.taxon" : "_a.taxon")."
			" . (isset($limit) ? "limit ".$limit : "")."
			" . (isset($offset) & isset($limit) ? "offset ".$offset : "")
		;

		$data=$this->freeQuery( $query );

		$count=$this->freeQuery( "select found_rows() as total" );

		return array('data'=>$data,'count'=>$count[0]['total']);

	}

	public function getPhotographersPictureCount( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		
		if ( is_null($project_id) )
			return;

		$query="
			select 
				count(distinct _b.media_id) as picture_count,
				count(distinct _a.taxon_id) as taxon_count,
				_b.meta_data as name
			from 
				%PRE%media_taxon _a
	
			right join %PRE%media_meta _b
				on _a.project_id=_b.project_id
				and _a.id = _b.media_id
				and _b.sys_label = 'beeldbankFotograaf'
			
			where
				_a.project_id=".$project_id."
			group by
				_b.meta_data
			order by
				picture_count desc
			";

		return $this->freeQuery( [ "query"=>$query, "fieldAsIndex"=>"name" ] );
	}

	public function getValidatorPictureCount( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		
		if ( is_null($project_id) )
			return;

		$query="
			select 
				count(distinct _b.media_id) as picture_count,
				count(distinct _a.taxon_id) as taxon_count,
				_b.meta_data as name
			from 
				%PRE%media_taxon _a
	
			right join %PRE%media_meta _b
				on _a.project_id=_b.project_id
				and _a.id = _b.media_id
				and _b.sys_label = 'beeldbankValidator'
			
			where
				_a.project_id=".$project_id."
			group by
				_b.meta_data
			order by
				picture_count desc
			";

		return $this->freeQuery( [ "query"=>$query, "fieldAsIndex"=>"name" ] );
	}
    
	public function doPictureSearch( $params )
	{
	    $language_id = isset($params['language_id']) ? $params['language_id'] : null;
	    $group_id = isset($params['group_id']) ? $params['group_id'] : null;
	    $name = isset($params['name']) ? $params['name'] : null;
	    $photographer = isset($params['photographer']) ? $params['photographer'] : null;
	    $validator = isset($params['validator']) ? $params['validator'] : null;
	    $project_id = isset($params['project_id']) ? $params['project_id'] : null;
	    $group_id = isset($params['group_id']) ? $params['group_id'] : null;
	    $name_id = isset($params['name_id']) ? $params['name_id'] : null;
	    $sort = isset($params['sort']) ? $params['sort'] : null;
	    $limit = isset($params['limit']) ? $params['limit'] : null;
	    $offset = isset($params['offset']) ? $params['offset'] : null;
	    
	    if ( is_null($project_id) || is_null($language_id) )
	        return;
	        
	        if ( !empty($photographer) )
	        {
	            //$photographer="_c.meta_data='".$this->escapeString($photographer)."'";
	            //$photographer="_c.meta_data like '%".$this->escapeString($photographer)."%'";
	            
	            $photographer="
				(
					_c.meta_data like '%".$this->escapeString($photographer)."%'
					or
					concat(
						trim(substring(_c.meta_data, locate(',',_c.meta_data)+1)),' ',
						trim(substring(_c.meta_data, 1, locate(',',_c.meta_data)-1))
					) like '%".$this->escapeString($photographer)."%'
				)";
	        }
	        
	        if ( !empty($validator) )
	        {
	            //$validator="_meta6.meta_data='".$this->escapeString($validator)."'";
	            //$validator="_meta6.meta_data like '%".$this->escapeString($validator)."%'";
	            
	            $validator="
				(
					_meta6.meta_data like '%".$this->escapeString($validator)."%'
					or
					concat(
						trim(substring(_meta6.meta_data, locate(',',_meta6.meta_data)+1)),' ',
						trim(substring(_meta6.meta_data, 1, locate(',',_meta6.meta_data)-1))
					) like '%".$this->escapeString($validator)."%'
				)";
	        }
	        
	        $sort="_meta4.meta_date desc, _meta1.meta_date desc";
	        
	        if ( isset($sort) && $sort=='photographer' )
	        {
	            $sort="_c.meta_data asc";
	        }
	        
	        if ( !empty($photographer) || !empty($validator) )
	        {
	            $sort="_meta4.meta_date desc, _k.taxon, _meta1.meta_date desc";
	        }
	        
	        if ( !empty($photographer) || !empty($validator) )
	        {
	            $sort="_meta4.meta_date desc, _k.taxon, _meta1.meta_date desc";
	        }
	        
	        $query="
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_m.file_name as image,
				_m.file_name as thumb,
				_f.rank_id as base_rank_id,
				_k.taxon,
				_k.taxon as validName,
				NULL as name,
				NULL as nomen,
	            
				case
					when
						date_format(_meta1.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta1.meta_date)!='0'
					then
						date_format(_meta1.meta_date,'%e %M %Y')
					when
						date_format(_meta1.meta_date,'%M %Y') is not null
					then
						date_format(_meta1.meta_date,'%M %Y')
					when
						date_format(_meta1.meta_date,'%Y') is not null
						and YEAR(_meta1.meta_date)!='0000'
					then
						date_format(_meta1.meta_date,'%Y')
					when
						YEAR(_meta1.meta_date)='0000'
					then
						null
					else
						_meta1.meta_date
				end as meta_datum,
	            
				NULL as meta_short_desc,
				NULL as meta_geografie,
	            
				case
					when
						date_format(_meta4.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta4.meta_date)!=0
					then
						date_format(_meta4.meta_date,'%e %M %Y')
					when
						date_format(_meta4.meta_date,'%M %Y') is not null
					then
						date_format(_meta4.meta_date,'%M %Y')
					when
						date_format(_meta4.meta_date,'%Y') is not null
						and YEAR(_meta4.meta_date)!='0000'
					then
						date_format(_meta4.meta_date,'%Y')
					when
						YEAR(_meta4.meta_date)='0000'
					then
						null
					else
						_meta4.meta_date
				end as meta_datum_plaatsing,
	            
				NULL as meta_copyrights,
				NULL as meta_validator,
				NULL as meta_adres_maker,
				NULL as photographer,
				NULL as meta_license
	            
			from  %PRE%media_taxon _m
	            
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id
	            
			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id
	            
			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'
	            
			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'
	            
			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'

            ".(!empty($validator) ?
                "left join %PRE%media_meta _meta6
                on _m.id=_meta6.media_id
                and _m.project_id=_meta6.project_id
                and _meta6.sys_label='beeldbankValidator'
				" : "" )."
	            
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
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC : "" )."
				    
			".(!empty($photographer) ?
                "left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'
				" : "" )."
					    
			where _m.project_id = ".$project_id."
			    
				and ifnull(_meta9.meta_data,0)!=1
				and ifnull(_trash.is_deleted,0)=0
			    
				".(isset($photographer)  ? "and ".$photographer : "")."
				".(isset($validator)  ? "and ".$validator : "")."
				".(!empty($group_id) ? "and  ( MATCH(_q.parentage) AGAINST ('". $this->generateTaxonParentageId( $group_id )."' in boolean mode) or _m.taxon_id = " .$group_id. ") "  : "")."
				".(!empty($name_id) ? "and _m.taxon_id = ". (int)$name_id : "")."
				".(!empty($name) ? "and _j.name like '". $this->escapeString($name)."%'"  : "")."
				    
			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
			;

	        $data=$this->freeQuery( $query );
	        
 	        //SQL_CALC_FOUND_ROWS
	        $count=$this->freeQuery( "select found_rows() as total" );
	        
	        foreach ($data as $k => $v) {
	            $data[$k] = array_merge($v, $this->getMediaMetadata($v['taxon_id'], $v['id'], $project_id));
	        }
	        
	        return array('data'=>$data,'count'=>$count[0]['total']);
	        
	}
	
	private function getMediaMetadata ($taxonId, $mediaId, $projectId) {
	    
	    $metaLabels = [
	        'beeldbankOmschrijving' => 'meta_short_desc',
	        'beeldbankLokatie' => 'meta_geografie',
	        'beeldbankFotograaf' => 'photographer',
	        'beeldbankAdresMaker' => 'meta_adres_maker',
	        'beeldbankLicentie' => 'meta_license',
	        'beeldbankValidator' => 'meta_validator',
	        'beeldbankCopyright' => 'meta_copyrights',
	    ];
	    $output = [];
	    
	    // Meta data
	    $query = 'select sys_label, meta_data from %PRE%media_meta where project_id = ' . $projectId . ' 
            and media_id = ' . $mediaId;
	    $metadata = $this->freeQuery($query);
	    
        foreach ($metadata as $meta) {
            if (!empty($meta['meta_data'])) {
                $output[$metaLabels[$meta['sys_label']]] = $meta['meta_data'];
	        }
	    }
	    
	    // Names
	    $query = "
            select 
                name,
				trim(replace(name,ifnull(authorship,''),'')) as nomen
            from %PRE%names 
            where 
                project_id = " . $projectId . '
                and taxon_id = ' . $taxonId . ' 
                and language_id = ' . LANGUAGE_ID_SCIENTIFIC . '
	            and type_id = ' . $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'];
	    $names = $this->freeQuery($query);
	    
	    if (!empty($names)) {
	        $output = array_merge($output, $names[0]);
	    }
	    
        return $output;
	}
	
	/*
					
	public function doPictureSearch( $params )
    {
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$group_id = isset($params['group_id']) ? $params['group_id'] : null;
		$name = isset($params['name']) ? $params['name'] : null;
		$photographer = isset($params['photographer']) ? $params['photographer'] : null;
		$validator = isset($params['validator']) ? $params['validator'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$group_id = isset($params['group_id']) ? $params['group_id'] : null;
		$name_id = isset($params['name_id']) ? $params['name_id'] : null;
		$sort = isset($params['sort']) ? $params['sort'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$offset = isset($params['offset']) ? $params['offset'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return;

		if ( !empty($photographer) )
		{
			//$photographer="_c.meta_data='".$this->escapeString($photographer)."'";
			//$photographer="_c.meta_data like '%".$this->escapeString($photographer)."%'";

			$photographer="
				(
					_c.meta_data like '%".$this->escapeString($photographer)."%'
					or 
					concat(
						trim(substring(_c.meta_data, locate(',',_c.meta_data)+1)),' ',
						trim(substring(_c.meta_data, 1, locate(',',_c.meta_data)-1))
					) like '%".$this->escapeString($photographer)."%'
				)";
		}

		if ( !empty($validator) )
		{
			//$validator="_meta6.meta_data='".$this->escapeString($validator)."'";
			//$validator="_meta6.meta_data like '%".$this->escapeString($validator)."%'";

			$validator="
				(
					_meta6.meta_data like '%".$this->escapeString($validator)."%'
					or 
					concat(
						trim(substring(_meta6.meta_data, locate(',',_meta6.meta_data)+1)),' ',
						trim(substring(_meta6.meta_data, 1, locate(',',_meta6.meta_data)-1))
					) like '%".$this->escapeString($validator)."%'
				)";
		}

		$sort="_meta4.meta_date desc, _meta1.meta_date desc";
		
		if ( isset($sort) && $sort=='photographer' )
		{
			$sort="_c.meta_data asc";
		}

		if ( !empty($photographer) || !empty($validator) )
		{
			$sort="_meta4.meta_date desc, _k.taxon, _meta1.meta_date desc";
		}

		if ( !empty($photographer) || !empty($validator) )
		{
			$sort="_meta4.meta_date desc, _k.taxon, _meta1.meta_date desc";
		}

		$query="		
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_m.file_name as image,
				_m.file_name as thumb,
				_f.rank_id as base_rank_id,
				_k.taxon,
				_k.taxon as validName,
				_j.name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,

				case
					when
						date_format(_meta1.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta1.meta_date)!='0'
					then
						date_format(_meta1.meta_date,'%e %M %Y')
					when
						date_format(_meta1.meta_date,'%M %Y') is not null
					then
						date_format(_meta1.meta_date,'%M %Y')
					when
						date_format(_meta1.meta_date,'%Y') is not null
						and YEAR(_meta1.meta_date)!='0000'
					then
						date_format(_meta1.meta_date,'%Y')
					when
						YEAR(_meta1.meta_date)='0000'
					then
						null
					else
						_meta1.meta_date
				end as meta_datum,

				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,

				case
					when
						date_format(_meta4.meta_date,'%e %M %Y') is not null
						and DAYOFMONTH(_meta4.meta_date)!=0
					then
						date_format(_meta4.meta_date,'%e %M %Y')
					when
						date_format(_meta4.meta_date,'%M %Y') is not null
					then
						date_format(_meta4.meta_date,'%M %Y')
					when
						date_format(_meta4.meta_date,'%Y') is not null
						and YEAR(_meta4.meta_date)!='0000'
					then
						date_format(_meta4.meta_date,'%Y')
					when
						YEAR(_meta4.meta_date)='0000'
					then
						null
					else
						_meta4.meta_date
				end as meta_datum_plaatsing,

				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer,
				_meta10.meta_data as meta_license

			from  %PRE%media_taxon _m
			
			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id

			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%media_meta _meta1
				on _m.id=_meta1.media_id
				and _m.project_id=_meta1.project_id
				and _meta1.sys_label='beeldbankDatumVervaardiging'

			left join %PRE%media_meta _meta2
				on _m.id=_meta2.media_id
				and _m.project_id=_meta2.project_id
				and _meta2.sys_label='beeldbankOmschrijving'

			left join %PRE%media_meta _meta3
				on _m.id=_meta3.media_id
				and _m.project_id=_meta3.project_id
				and _meta3.sys_label='beeldbankLokatie'

			left join %PRE%media_meta _meta4
				on _m.id=_meta4.media_id
				and _m.project_id=_meta4.project_id
				and _meta4.sys_label='beeldbankDatumAanmaak'

			left join %PRE%media_meta _meta5
				on _m.id=_meta5.media_id
				and _m.project_id=_meta5.project_id
				and _meta5.sys_label='beeldbankCopyright'

			left join %PRE%media_meta _meta6
				on _m.id=_meta6.media_id
				and _m.project_id=_meta6.project_id
				and _meta6.sys_label='beeldbankValidator'

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'

			left join %PRE%media_meta _meta8
				on _m.id=_meta8.media_id
				and _m.project_id=_meta8.project_id
				and _meta8.sys_label='beeldbankFotograaf'

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%media_meta _meta10
				on _m.id=_meta10.media_id
				and _m.project_id=_meta10.project_id
				and _meta10.sys_label='beeldbankLicentie'

			".(!empty($group_id) ? 
				"right join %PRE%taxon_quick_parentage _q
					on _m.taxon_id=_q.taxon_id
					and _m.project_id=_q.project_id
				" : "" )."

				left join %PRE%names _j
					on _m.taxon_id=_j.taxon_id
					and _m.project_id=_j.project_id
					and _j.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
					and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."

				left join %PRE%media_meta _c
					on _m.project_id=_c.project_id
					and _m.id = _c.media_id
					and _c.sys_label = 'beeldbankFotograaf'

			where _m.project_id = ".$project_id."

				and ifnull(_meta9.meta_data,0)!=1
				and ifnull(_trash.is_deleted,0)=0
		
				".(isset($photographer)  ? "and ".$photographer : "")." 		
				".(isset($validator)  ? "and ".$validator : "")." 		
				".(!empty($group_id) ? "and  ( MATCH(_q.parentage) AGAINST ('". $this->generateTaxonParentageId( $group_id )."' in boolean mode) or _m.taxon_id = " .$group_id. ") "  : "")."
				".(!empty($name_id) ? "and _m.taxon_id = ". (int)$name_id : "")." 		
				".(!empty($name) ? "and _j.name like '". $this->escapeString($name)."%'"  : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "")
		;
		
		die($query);
		
		$data=$this->freeQuery( $query );
		//SQL_CALC_FOUND_ROWS
		$count=$this->freeQuery( "select found_rows() as total" );

		return array('data'=>$data,'count'=>$count[0]['total']);

	}
	*/

	public function getSuggestionsGroup( $params )
    {
		$search = isset($params['search']) ? $params['search'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$match = isset($params['match']) ? $params['match'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$restrict_language = isset($params['restrict_language']) ? $params['restrict_language'] : true;
		
		if ( is_null($search) || is_null($project_id) || is_null($match) || is_null($language_id) )
			return;

		$clause=null;

		if ($match=='start')
			$clause="_a.name like '".$this->escapeString($search)."%'";
		else
		if ($match=='exact')
			$clause="_a.name = '".$this->escapeString($search)."'";
		else
		if ($match=='like')
			$clause="_a.name like '%".$this->escapeString($search)."%'";
		else
		if ($match=='id')
			$clause="_a.taxon_id = ".(int)$taxon_id;

		if (empty($clause)) return;
		
		$query="
			select
				_a.taxon_id as id,
				_a.name,
				_b.nametype,
				ifnull(_g.label,_r.default_label) as rank,
				_k.name as dutch_name,
				if (_a.type_id=" . $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] . ",
						concat(_a.name,if(_k.name is null,'',concat('  - ',_k.name)),' [',ifnull(_g.label,_r.default_label),']'),
						concat(_a.name,'',' [',ifnull(_g.label,_r.default_label),']')
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
				and _g.language_id=".$language_id."

			left join ranks _r
				on _f.rank_id=_r.id

			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%names _k
				on _e.id=_k.taxon_id
				and _e.project_id=_k.project_id
				and _k.type_id=" . $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'] ." 
				and _k.language_id=".$language_id."

			where 
				_a.project_id =".$project_id."
				and _f.lower_taxon!=1
				and ifnull(_trash.is_deleted,0)=0 "
				. ( $restrict_language ? "and _a.language_id=".$language_id : "" ) . "
				and " . $clause . "

			order by name
			" . ( !is_null($limit) ? "limit ".$limit : "" )
		;

		/*
				and (
						_a.type_id=" . $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'] . "
						or
						(
							_a.type_id=" . $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'] ."
							" . ( $restrict_language ? "and _a.language_id=".$language_id : "" ) . "
						)
					)
		*/

		return $this->freeQuery( $query );
	}

	public function getSuggestionsAuthor( $params )
    {
		$search = isset($params['search']) ? $params['search'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$match = isset($params['match']) ? $params['match'] : null;
		
		if ( is_null($search) || is_null($limit) || is_null($project_id) || is_null($match) )
			return;

		$clause=null;

		if ($match=='start')
			$clause="meta_data like '".$this->escapeString($search)."%'";
		else
		if ($match=='exact')
			$clause="meta_data = '".$this->escapeString($search)."'";
		else
		if ($match=='like')
			$clause="meta_data like '%".$this->escapeString($search)."%'";

		if (empty($clause)) return;

		$query="
			select
				distinct authorship as label
			from %PRE%names
			where 
				project_id =".$project_id."
				and ".$clause."
			order by authorship
			limit ".$limit
		;

		return $this->freeQuery( $query );
		
	}

	public function getSuggestionsValidator( $params )
    {
		$search = isset($params['search']) ? $params['search'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$match = isset($params['match']) ? $params['match'] : null;
		
		if ( is_null($search) || is_null($limit) || is_null($project_id) || is_null($match) )
			return;

		$clause=null;

		if ($match=='start')
			$clause="meta_data like '".$this->escapeString($search)."%'";
		else
		if ($match=='exact')
			$clause="meta_data = '".$this->escapeString($search)."'";
		else
		if ($match=='like')
			$clause="meta_data like '%".$this->escapeString($search)."%'";

		if (empty($clause)) return;

		$query="
			select 
				distinct
				meta_data as original_label,
				trim(concat(
						trim(substring(meta_data, locate(',',meta_data)+1)),' ',
						trim(substring(meta_data, 1, locate(',',meta_data)-1))
					)) as label
			from %PRE%media_meta
			where
				project_id=".$project_id."
				and sys_label = 'beeldbankValidator'
				and ".$clause."
			order by meta_data
			limit ".$limit
		;

		return $this->freeQuery( $query );
		
	}

	public function getSuggestionsPhotographer( $params )
    {
		$search = isset($params['search']) ? $params['search'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$match = isset($params['match']) ? $params['match'] : null;
		
		if ( is_null($search) || is_null($limit) || is_null($project_id) || is_null($match) )
			return;

		$clause=null;

		if ($match=='start')
			$clause="meta_data like '".$this->escapeString($search)."%'";
		else
		if ($match=='exact')
			$clause="meta_data = '".$this->escapeString($search)."'";
		else
		if ($match=='like')
			$clause="meta_data like '%".$this->escapeString($search)."%'";

		if (empty($clause)) return;

		$query="
			select 
				distinct
				meta_data as original_label,
				trim(concat(
						trim(substring(meta_data, locate(',',meta_data)+1)),' ',
						trim(substring(meta_data, 1, locate(',',meta_data)-1))
					)) as label
			from %PRE%media_meta
			where
				project_id=".$project_id."
				and sys_label = 'beeldbankFotograaf'
				and ".$clause."
			order by meta_data
			limit ".$limit
		;

		return $this->freeQuery( $query );

	}

	public function getSuggestionsName( $params )
    {
		$search = isset($params['search']) ? $params['search'] : null;
		$limit = isset($params['limit']) ? $params['limit'] : null;
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$restrict_language = isset($params['restrict_language']) ? $params['restrict_language'] : true;
		$order = isset($params['order']) ? $params['order'] : null;
		$lower_only = isset($params['lower_only']) ? $params['lower_only'] : false;
		$match = isset($params['match']) ? $params['match'] : "like";
		
		if ( is_null($search) || is_null($limit) || is_null($project_id) || is_null($language_id) )
			return;

		if ( is_null($order) )
		{
			//$order="ifnull(_c.name,_d.taxon)";
			$order="match_percentage desc, _d.taxon asc, _f.rank_id asc ";
		}


		$clause=null;

		if ($match=='start')
			$clause="_a.name like '".$this->escapeString($search)."%'";
		else
		if ($match=='exact')
			$clause="_a.name = '".$this->escapeString($search)."'";
		else
		if ($match=='like')
			$clause="_a.name like '%".$this->escapeString($search)."%'";
		else
		if ($match=='id')
			$clause="_a.taxon_id = ".(int)$taxon_id;

		if (empty($clause)) return;
		
		$query="
			select
				_a.taxon_id as id,
				concat(_d.taxon,if(_c.name is null,'',concat(' - ',_c.name)),' [',ifnull(_g.label,_r.rank),']') as label,
				_d.taxon as scientific_name,
				_d.parent_id,
				_c.name as common_name,
				trim(replace(_sci.name,_sci.authorship,'')) as nomen,
				_r.id as base_rank_id,
	
				case
					when
						_a.name REGEXP '^".$this->escapeString($search)."$' = 1
						or
						trim(concat(
							if(_a.uninomial is null,'',concat(_a.uninomial,' ')),
							if(_a.specific_epithet is null,'',concat(_a.specific_epithet,' ')),
							if(_a.infra_specific_epithet is null,'',concat(_a.infra_specific_epithet,' '))
						)) REGEXP '^".$this->escapeString($search)."$' = 1
					then 100
					when
						_a.name REGEXP '^".$search."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 95
					when
						_a.name REGEXP '^(.*)[[:<:]]".$this->escapeString($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 90
					when
						_a.name REGEXP '^".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 85
					when
						_a.name REGEXP '^(.*)[[:<:]]".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 80
					when 
						_a.name REGEXP '^(.*)".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id >= ".SPECIES_RANK_ID."
					then 75
					when
						_a.name REGEXP '^".$this->escapeString($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 70
					when
						_a.name REGEXP '^(.*)[[:<:]]".$this->escapeString($search)."[[:>:]](.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 65
					when
						_a.name REGEXP '^".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 60
					when
						_a.name REGEXP '^(.*)[[:<:]]".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 55
					when 
						_a.name REGEXP '^(.*)".$this->escapeString($search)."(.*)$' = 1 
						and
						_f.rank_id < ".SPECIES_RANK_ID."
					then 50

					else 10
				end as match_percentage

			from %PRE%names _a
			
			right join %PRE%taxa _d
				on _a.taxon_id = _d.id
				and _a.project_id = _d.project_id

			left join %PRE%trash_can _trash
				on _d.project_id = _trash.project_id
				and _d.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%names _c
				on _a.taxon_id=_c.taxon_id
				and _a.project_id=_c.project_id
				and _c.type_id=".$this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id']."
				and _c.language_id=".$language_id."
			
			left join %PRE%names _sci
				on _a.taxon_id=_sci.taxon_id
				and _a.project_id=_sci.project_id
				and _sci.type_id=".$this->_nameTypeIds[PREDICATE_VALID_NAME]['id']."
			
			left join %PRE%projects_ranks _f
				on _d.rank_id=_f.id
				and _d.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _g
				on _d.rank_id=_g.project_rank_id
				and _d.project_id = _g.project_id
				and _g.language_id=".$language_id."

			left join ranks _r
				on _f.rank_id=_r.id
			
			where 
				_a.project_id = ".$project_id."
				and ifnull(_trash.is_deleted,0)=0
				" . ( $lower_only ? "and _f.rank_id >= ".SPECIES_RANK_ID : "" )."
				and " . $clause ."
				" . ( $restrict_language ? "and (_a.language_id=".$language_id." or _a.language_id=".LANGUAGE_ID_SCIENTIFIC.")" : "" ) . "

			group by _a.taxon_id

			order by " . $order . "
			" . ( !is_null($limit) ? "limit ".$limit : "" )
		;

		return $this->freeQuery( $query );
		
	}

	public function getTraitGroup( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return;

		$query="
				_grp.id,
				_grp.parent_id,
				_grp.sysname,
				ifnull(_grp_b.translation,_grp.sysname) as group_name,
				_grp_c.translation as group_description,
				_grp.id as group_id

			from
				%PRE%traits_groups _grp

			left join 
				%PRE%text_translations _grp_b
				on _grp.project_id=_grp_b.project_id
				and _grp.name_tid=_grp_b.text_id
				and _grp_b.language_id=". $language_id ."

			left join 
				%PRE%text_translations _grp_c
				on _grp.project_id=_grp_c.project_id
				and _grp.description_tid=_grp_c.text_id
				and _grp_c.language_id=". $language_id ."

			where
				_grp.project_id=". $project_id."
			order by _grp.show_order, _grp_b.translation
		";
		
		return $this->freeQuery( $query );
		
	}

	public function getTraits( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$group = isset($params['group']) ? $params['group'] : null;
		
		if ( is_null($project_id) || is_null($language_id) || is_null($group) )
			return;

		$query="
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
				_grp.help_link_url as group_help_link_url,
                _grp.show_order as group_order

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
				and _grp_b.language_id=". $language_id ."

			left join 
				%PRE%text_translations _grp_c
				on _grp.project_id=_grp_c.project_id
				and _grp.description_tid=_grp_c.text_id
				and _grp_c.language_id=". $language_id ."
				
			left join 
				%PRE%text_translations _grp_d
				on _grp.project_id=_grp_d.project_id
				and _grp.all_link_text_tid=_grp_d.text_id
				and _grp_d.language_id=". $language_id ."
				
			left join 
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.text_id
				and _b.language_id=". $language_id ."

			left join 
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.code_tid=_c.text_id
				and _c.language_id=". $language_id ."

			left join 
				%PRE%text_translations _d
				on _a.project_id=_d.project_id
				and _a.description_tid=_d.text_id
				and _d.language_id=". $language_id ."

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
				_a.project_id=". $project_id."
				and _a.trait_group_id 
				" . ( is_array($group) ? " in (".implode(",",$group).") " : " = " . $group ) ."
				and _grp.show_in_search=1

			group by
				_a.id

			order by
				_a.show_order
		";
		
		return $this->freeQuery( $query );

	}

	public function getTraitgroupTrait( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$trait_id = isset($params['trait_id']) ? $params['trait_id'] : null;
		
		if ( is_null($project_id) || is_null($trait_id) )
			return;

		$query="
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
				_a.project_id=". $project_id."
				and _a.id=".$trait_id."
		";

		$d=$this->freeQuery( $query );

		return isset($d[0]) ? $d[0] : null;

	}

	public function getTraitgroupTraitValues( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$trait_id = isset($params['trait_id']) ? $params['trait_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) || is_null($trait_id) )
			return;

		$query="
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
				and _trans.language_id=". $language_id ."
				
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
				_a.project_id = ".$project_id." 
				and _a.trait_id = ".$trait_id." 
			order by 
				_a.show_order
		";

		return $this->freeQuery( $query );

	}

	public function getTaxonTraitFreeValues( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		
		if ( is_null($project_id) || is_null($taxon_id) )
			return;

		$query="
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
				_a.project_id=".$project_id."
				and _a.taxon_id=".$taxon_id."
		";

		return $this->freeQuery( $query );

	}

	private function getTraitJoins( $params )
	{

		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$traits = isset($params['traits']) ? $params['traits'] : null;

		if (empty($project_id) || empty($traits))	return;
		
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
			$trait=$this->getTraitgroupTrait( [ "trait_id" => $id, "project_id" => $project_id ] );

			$column = strpos($trait['type_sysname'], 'date') === false ? 'numerical' : 'date';

			$trait_joins .=
			"
				right join %PRE%traits_taxon_freevalues _trait_values".$id."
					on _a.project_id = _trait_values".$id.".project_id
					and _trait_values".$id.".trait_id = ".$id."
					and _a.id = _trait_values".$id.".taxon_id
					and ifnull(_trait_values".$id."." . $column . "_value_end,_trait_values".$id."." . $column . "_value) is not null
					and (
			";

			foreach((array)$vals as $key=>$val)
			{
				$value1=$value2=null;

				switch ($trait['type_sysname'])
				{
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
							_trait_values".$id.".".$column2." is null AND
							_trait_values".$id.".".$column1." = ".$value1."
						)";
						
					if (isset($value2))
					{
						
						$x .= "
						OR
						(
							_trait_values".$id.".".$column2." is not null AND
							(
								_trait_values".$id.".".$column1." <= ".$value1." AND
								_trait_values".$id.".".$column2." >= ".$value2."
							)
						)
						";
					}
				
					$x= " (".$x.") ";
				
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

	private function getTraitGroupJoin( $group )
	{
		if (empty($group))	return;
		
		return
			[ "join"=>
				 "left join %PRE%traits_taxon_values _ttv
						on _a.project_id = _ttv.project_id
						and _a.id = _ttv.taxon_id
		
					left join %PRE%traits_values _tv
						on _ttv.project_id = _tv.project_id
						and _ttv.value_id = _tv.id
		
					left join %PRE%traits_traits _tt1
						on _tv.project_id = _tt1.project_id
						and _tv.trait_id = _tt1.id
						and _tt1.trait_group_id=".$group."
		
					left join %PRE%traits_taxon_freevalues _ttf
						on _a.project_id = _ttf.project_id
						and _a.id = _ttf.taxon_id
		
					left join %PRE%traits_traits _tt2
						on _ttf.project_id = _tt2.project_id
						and _ttf.trait_id = _tt2.id
						and _tt2.trait_group_id=".$group."
					",
				"having"=>
					"having count(_tt1.id)+count(_tt2.id) > 0"
			 ];
	}


}
