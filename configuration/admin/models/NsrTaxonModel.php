<?php

include_once (__DIR__ . "/AbstractModel.php");

final class NsrTaxonModel extends AbstractModel
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

	public function getName( $params )
	{
		$label_language_id=isset($params['label_language_id']) ? $params['label_language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		$name_id=isset($params['name_id']) ? $params['name_id'] : null;

		if( !isset( $label_language_id )  || !isset( $project_id ) )
		{
			return;
		}

		$query= "
			select
				_a.id,
				_a.taxon_id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_a.name_author,
				_a.authorship_year,
				_a.reference,
				_a.reference_id,
				_x.rank_id as synonym_base_rank_id,
		        _h.label as reference_name,
				_a.expert,
				_a.expert_id,
				_f.name as expert_name,
				_a.organisation,
				_a.organisation_id,
				_g.name as organisation_name,
				_a.type_id,
				_a.rank_id,
				_b.nametype,
				_a.language_id,
				_c.language,
				_d.label as language_label,
				replace(_ids.nsr_id,'tn.nlsr.name/','') as nsr_id

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=" . $label_language_id . "

			left join %PRE%actors _f
				on _a.expert_id = _f.id
				and _a.project_id=_f.project_id

			left join %PRE%actors _g
				on _a.organisation_id = _g.id
				and _a.project_id=_g.project_id

			left join  %PRE%literature2 _h
				on _a.reference_id = _h.id
				and _a.project_id=_h.project_id

			left join %PRE%projects_ranks _x
				on _a.rank_id=_x.id
				and _a.project_id=_x.project_id

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'name'

			where
				_a.project_id = " . $project_id . "
				".(isset($taxon_id) ? "and _a.taxon_id=".$taxon_id: "" )."
				".(isset($language_id) ? "and _a.language_id=".$language_id: "" )."
				".(isset($type_id) ? "and _a.type_id=".$type_id: "" )."
				".(isset($name_id) ? "and _a.id=".$name_id: "" );

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;

	}

	public function getNames( $params )
	{
		$label_language_id=isset($params['label_language_id']) ? $params['label_language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $label_language_id ) || !isset( $project_id ) || !isset( $taxon_id ) )
		{
			return;
		}

		$query=  "
			select
				_a.id,
				_a.name,
				_a.uninomial,
				_a.specific_epithet,
				_a.infra_specific_epithet,
				_a.authorship,
				_a.name_author,
				_a.authorship_year,
				_a.reference,
				_a.reference_id,
				_a.expert,
				_a.expert_id,
				_a.organisation,
				_a.organisation_id,
				_b.nametype,
				_a.language_id,
				_c.language,
				ifnull(_d.label,_c.language) as language_label,
				case
					when _b.nametype = '".PREDICATE_VALID_NAME."' then 11
					when _b.nametype = '".PREDICATE_PREFERRED_NAME."' then 10
					when _b.nametype = '".PREDICATE_ALTERNATIVE_NAME."' then 9
					when _b.nametype = '".PREDICATE_SYNONYM."' then 7
					when _b.nametype = '".PREDICATE_SYNONYM_SL."' then 6

					when _b.nametype = '".PREDICATE_HOMONYM."' then 5
					when _b.nametype = '".PREDICATE_MISSPELLED_NAME."' then 4
					when _b.nametype = '".PREDICATE_INVALID_NAME."' then 3
					else 0
				end as sort_criterium,
				_f.rank_id,
				ifnull(_q.label,_r.rank) as rank_label

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$label_language_id."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _r
				on _f.rank_id=_r.id

			left join %PRE%labels_projects_ranks _q
				on _f.id=_q.project_rank_id
				and _f.project_id = _q.project_id
				and _q.language_id=".$label_language_id."

			where
				_a.project_id = ".$project_id."
				and _a.taxon_id=".$taxon_id."
			order by
				sort_criterium desc
				";

		return $this->freeQuery( array( 'query' => $query, 'fieldAsIndex' => 'id' ) );

	}

	public function getPreferredNames( $params )
	{
		$label_language_id=isset($params['label_language_id']) ? $params['label_language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;

		if( !isset( $label_language_id ) || !isset( $project_id ) || !isset( $taxon_id ) || !isset( $type_id ) )
		{
			return;
		}

		$query=  "
			select
				_a.id,
				_a.name,
				_a.language_id,
				_c.language,
				ifnull(_d.label,_c.language) as language_label

			from %PRE%names _a

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$label_language_id."

			where
				_a.project_id = ".$project_id."
				and _a.taxon_id = ".$taxon_id."
				and _a.type_id = ".$type_id."
				";

		return $this->freeQuery( $query );

	}

	public function getPresenceData( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $language_id ) || !isset( $project_id ) || !isset( $taxon_id ))
		{
			return;
		}

		$query="
			select
				_a.presence_id,
				_a.presence82_id,
				_a.reference_id,
				_b.label as presence_label,
				_b.information as presence_information,
				_b.information_title as presence_information_title,
				_b.index_label as presence_index_label,
				_c.label as presence82_label,
				_d.habitat_id,
				_d.label as habitat_label,
				_e.id as expert_id,
				_f.id as organisation_id,
				_e.name as expert_name,
				_f.name as organisation_name,
				_a.reference_id,
				_g.label as reference_label,
				_gg.name as reference_author,
				_g.date as reference_date

			from %PRE%presence_taxa _a

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$language_id."

			left join %PRE%presence_labels _c
				on _a.presence82_id = _c.presence_id
				and _a.project_id=_c.project_id
				and _c.language_id=".$language_id."

			left join %PRE%habitat_labels _d
				on _a.habitat_id = _d.habitat_id
				and _a.project_id=_d.project_id
				and _d.language_id=".$language_id."

			left join %PRE%actors _e
				on _a.actor_id = _e.id
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.actor_org_id = _f.id
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id
				and _a.project_id=_g.project_id

			left join %PRE%actors _gg
				on _g.actor_id = _gg.id
				and _g.project_id=_gg.project_id

			where _a.project_id = ".$project_id."
				and _a.taxon_id =".$taxon_id;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;

	}

	public function getStatuses( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $language_id ) || !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_a.id,
				_b.label,
				_b.information,
				_b.information_short,
				_b.information_title,
				ifnull(_b.index_label,99) as index_label

			from %PRE%presence _a

			left join %PRE%presence_labels _b
				on _a.id = _b.presence_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$language_id."

			where _a.project_id = ".$project_id."
			and index_label != 99
			order by index_label";

		return $this->freeQuery( $query );
	}

	public function getHabitats( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $language_id ) || !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
            	_a.id,
            	ifnull(_b.label,_a.sys_label) as label

			from %PRE%habitats _a

			left join %PRE%habitat_labels _b
				on _a.id = _b.habitat_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$language_id."

			where _a.project_id = ".$project_id
		;

		return $this->freeQuery( $query );
	}

	public function getLanguages( $params )
	{
		$label_language_id=isset($params['label_language_id']) ? $params['label_language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $label_language_id ) || !isset( $project_id ) )
		{
			return;
		}

        $query="
			select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label,
				case
					when _c.id= " . LANGUAGE_ID_SCIENTIFIC. " then 99
					when _c.id= " . $label_language_id . " then 98
					when _c.id= " . LANGUAGE_ID_DUTCH . " then 97
					when _c.id= " . LANGUAGE_ID_ENGLISH . " then 97
					else 0
				end as sort_criterium

			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$project_id."
				and _d.label_language_id=".$label_language_id."
				order by sort_criterium desc, label asc
			";

		return $this->freeQuery( $query );
	}

	public function getDeletedSpeciesList( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( is_null( $project_id ) ) return;

		$query="
			select
				_a.id,
				_a.taxon,
				_a.parent_id,
				_q.rank,
				_q.id as base_rank_id,
				concat(_user.first_name,' ',_user.last_name) as deleted_by,
				date_format(_trash.created,'%d-%m-%Y %T') as deleted_when

			from %PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%users _user
				on _trash.user_id = _user.id

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'taxon'

			where _a.project_id =".$project_id."
				and ifnull(_trash.is_deleted,0)=1

			order by _trash.created desc
		";

		return $this->freeQuery( $query );
	}

	public function getOrphanedSpeciesList( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( is_null( $project_id ) ) return;

		$query="
			select
				_a.id,
				_a.taxon,
				_a.parent_id,
				_q.rank,
				_q.id as base_rank_id,
				ifnull(_trash.is_deleted,0) as is_deleted

			from %PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			where _a.project_id =".$project_id."
				and _a.parent_id is null

			order by
				ifnull(_trash.is_deleted,0) asc, _a.taxon
		";

		return $this->freeQuery( $query );
	}

	public function getSpeciesList( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$type_id_preferred=isset($params['type_id_preferred']) ? $params['type_id_preferred'] : null;
		$search=isset($params['search']) ? $params['search'] : null;
		$type_id_valid=isset($params['type_id_valid']) ? $params['type_id_valid'] : null;

		$match_start_only=isset($params['match_start_only']) ? $params['match_start_only'] : null;
		$taxa_only=isset($params['taxa_only']) ? $params['taxa_only'] : null;
		$rank_above=isset($params['rank_above']) ? $params['rank_above'] : null;
		$rank_equal_above=isset($params['rank_equal_above']) ? $params['rank_equal_above'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$nametype=isset($params['nametype']) ? $params['nametype'] : null;
		$have_deleted=isset($params['have_deleted']) ? $params['have_deleted'] : null;
		$sort=isset($params['sort']) ? $params['sort'] : null;
		$limit=isset($params['limit']) ? $params['limit'] : null;
		$offset=isset($params['offset']) ? $params['offset'] : null;

		if( !isset( $search ) || !isset( $language_id ) || !isset( $type_id_preferred ) || !isset( $project_id ) || !isset( $type_id_valid ) )
		{
			return;
		}

		$query="
			select
				_a.taxon_id as id,
				concat(_a.name,' [',ifnull(_q.label,_x.rank),'%s]') as label,
				concat(' [',ifnull(_q.label,_x.rank),'%s]') as label_suffix,
				_e.rank_id,
				_e.taxon,
				_e.parent_id,
				_a.name,
				_common.name as common_name,
				_f.rank_id as base_rank_id,
				_z.rank_id as synonym_base_rank_id,
		        _x.rank,
				_a.uninomial,
				_a.specific_epithet,
				_b.nametype,
				ifnull(_d.label,_c.language) as language_label,
				ifnull(_trash.is_deleted,0) as is_deleted,

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
						_a.name REGEXP '^".$this->escapeString($search)."[[:>:]](.*)$' = 1
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
				end as match_percentage,

				case
					when _f.rank_id >= ".SPECIES_RANK_ID." then 100
					else 50
				end as adjusted_rank

			from %PRE%names _a

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$language_id."

			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%names _common
				on _e.id = _common.taxon_id
				and _e.project_id = _common.project_id
				and _common.type_id = ".$type_id_preferred."
				and _common.language_id=".$language_id."

			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%projects_ranks _z
				on _a.rank_id=_z.id
				and _a.project_id = _z.project_id

			left join %PRE%ranks _x
				on _f.rank_id=_x.id

			left join %PRE%labels_projects_ranks _q
				on _e.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$language_id."

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id = _b.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id = _trash.lng_id
				and _trash.item_type='taxon'

			where _a.project_id =".$project_id."
				and _a.name like '".($match_start_only ? '':'%').$this->escapeString($search)."%'
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

			".($taxa_only ? "and _a.type_id = ".$type_id_valid : "" )."
			".($rank_above ? "and _f.rank_id < ".$rank_above : "" )."
			".($rank_equal_above ? "and _f.rank_id <= ".$rank_equal_above : "" )."
			".($taxon_id ? "and _a.taxon_id = ".$taxon_id : "" )."
			".($nametype ? "and _b.nametype = ".$nametype : "" )."
			".($have_deleted=='no' ? "and ifnull(_trash.is_deleted,0)=0" :  "" )."
			".($have_deleted=='only' ? "and ifnull(_trash.is_deleted,0)=1" : "" )."

			order by
				match_percentage desc,
				_e.taxon asc,
				_f.rank_id asc, ".
				(!empty($sort) && $sort=='preferredNameNl' ?
					"common_name" :
					"taxon"
				)."
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		;

		return $this->freeQuery( $query );

	}

	public function getInheritableName( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $project_id ) || !isset( $type_id ) || !isset( $taxon_id ) )
		{
			return;
		}

		$query="
			select
				_a.*, _f.rank_id as base_rank_id
			from
				%PRE%names _a

			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			where
				_a.project_id =".$project_id."
				and _a.type_id = ".$type_id."
				and _a.taxon_id =".$taxon_id
		;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;
	}

	public function getNumberOfUndeletedTaxa( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( is_null($project_id) ) return;

		$query="
			select
				count(*) as total

			from %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id = _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id =".$project_id."
				and ifnull(_trash.is_deleted,0)=0
		";

		$d=$this->freeQuery( $query );

		return $d ? $d[0]['total'] : null;

	}

    public function getExpertsLookupList( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$get_all=isset($params['get_all']) ? $params['get_all'] : null;
		$match_start_only=isset($params['match_start_only']) ? $params['match_start_only'] : null;
		$search=isset($params['search']) ? $params['search'] : null;

		if( !isset( $project_id ) || !isset( $search ) )
		{
			return;
		}

		$query="
			select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$project_id."
				".(!$get_all ? "and _e.name like '".($match_start_only ? '':'%').$this->escapeString($search)."%'" : "")."

			order by
				_e.is_company, _e.name
		";

		return $this->freeQuery( $query );

    }

	public function checkNameUniqueness( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$name=isset($params['name']) ? $params['name'] : null;
		$child_rank_id=isset($params['child_rank_id']) ? $params['child_rank_id'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;

		if( !isset( $project_id ) || !isset( $name ) || !isset( $child_rank_id ) || !isset( $parent_id ) )
		{
			return;
		}

		$query="
			select
				_a.*,ifnull(_trash.is_deleted,0) as is_deleted
			from
				%PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id = ".$project_id."
				and _a.taxon like '". $this->escapeString($name) ."'
				and _a.rank_id = ". $this->escapeString($child_rank_id) ."
				and _a.parent_id = ". $this->escapeString($parent_id)
			;

		return $this->freeQuery( $query );

	}

	public function checkMainLanguageCommonName( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$name=isset($params['name']) ? $params['name'] : null;

		if( !isset( $project_id ) || !isset( $name ) )
		{
			return;
		}

		$query="
			select
				_a.id,
				_b.taxon
			from
				%PRE%names _a
			left join %PRE%taxa _b
				on _a.project_id = _b.project_id
				and _a.taxon_id=_b.id
			where
				_a.project_id = ".$project_id."
				and lower(_a.name) = '" . $this->escapeString(trim($name)) . "'
		";

		return $this->freeQuery( $query );

	}

	public function getReference( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$literature_id=isset($params['literature_id']) ? $params['literature_id'] : null;

		if( !isset( $project_id ) || !isset( $literature_id ) )
		{
			return;
		}

		$query="
			select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i
				on _a.periodical_id = _i.id
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$project_id."
				and _a.id = ".$literature_id
		;

		$d=$this->freeQuery( $query );
		return isset($d[0]) ? $d[0] : null;
	}

	public function getTaxonBranch( $params )
	{
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;

		if( !isset( $type_id ) || !isset( $project_id ) || !isset( $parent_id ) )
		{
			return;
		}

		$query="
			select
				_b.*
			from
				%PRE%taxon_quick_parentage _a

			left join %PRE%names _b
				on _a.project_id = _b.project_id
				and _a.taxon_id = _b.taxon_id
				and _b.type_id =".$type_id."

			where
				_a.project_id = ".$project_id."
				and MATCH(_a.parentage) AGAINST ('". $this->generateTaxonParentageId( $parent_id ) ."' in boolean mode)
		";

		return $this->freeQuery( $query );

	}

	public function checkIfNameExistsInConceptsKingdom( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		$intended_new_concept_name=isset($params['intended_new_concept_name']) ? $params['intended_new_concept_name'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $project_id ) || !isset( $type_id ) || !isset( $intended_new_concept_name )  || !isset( $taxon_id ) )
		{
			return;
		}

		$query="
			select
				*
			from
				%PRE%names
			where
				project_id = ".$project_id."
				and type_id=".$type_id."
				and language_id=".LANGUAGE_ID_SCIENTIFIC."
				and (
					trim(replace(name,ifnull(authorship,''),'')) = '". $this->escapeString($intended_new_concept_name) ."'
						or
					concat(
						if(uninomial is null,'',concat(uninomial,' ')),
						if(specific_epithet is null,'',concat(specific_epithet,' ')),
						if(infra_specific_epithet is null,'',infra_specific_epithet)
					) = '". $this->escapeString($intended_new_concept_name) ."'
				)
				and taxon_id != ".$this->escapeString($taxon_id)."
		";

		return $this->freeQuery( $query );

	}

	public function checkIfGenusWithSameNameExists( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$type_id=isset($params['type_id']) ? $params['type_id'] : null;
		$intended_new_concept_name=isset($params['intended_new_concept_name']) ? $params['intended_new_concept_name'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $project_id ) || !isset( $type_id ) || !isset( $intended_new_concept_name ) || !isset( $parent_id )  || !isset( $taxon_id ) )
		{
			return;
		}

		$query="
			select
				_a.*
			from
				%PRE%names _a

			left join %PRE%taxa _b
				on _a.project_id = _b.project_id
				and _a.taxon_id = _b.id

			where
				_a.project_id = ".$project_id."
				and _a.type_id=".$type_id."
				and _a.language_id=".LANGUAGE_ID_SCIENTIFIC."
				and (
					trim(replace(name,ifnull(_a.authorship,''),'')) = '". $this->escapeString($intended_new_concept_name) ."'
						or
					concat(
						if(_a.uninomial is null,'',concat(_a.uninomial,' ')),
						if(_a.specific_epithet is null,'',concat(_a.specific_epithet,' ')),
						if(_a.infra_specific_epithet is null,'',_a.infra_specific_epithet)
					) = '". $this->escapeString($intended_new_concept_name) ."'
				)
				and _b.parent_id = ".$this->escapeString($parent_id)."
				and _b.id != ".$this->escapeString($taxon_id)."
		";
		
		return $this->freeQuery( $query );
	}

	public function getTraitgroups( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$parent_id=isset($params['parent_id']) ? $params['parent_id'] : null;

		if( !isset( $language_id ) || !isset( $taxon_id ) || !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_a.*,
				_b.translation as name,
				_c.translation as description,
				count(_tt.id) as trait_count,
				count(_ttf.id) as taxon_freevalue_count,
				count(_ttv.id) as taxon_value_count,
				count(_ttf.id)+count(_ttv.id) as taxon_count

			from
				%PRE%traits_groups _a

			left join
				%PRE%text_translations _b
				on _a.project_id=_b.project_id
				and _a.name_tid=_b.id
				and _b.language_id=". $language_id ."

			left join
				%PRE%text_translations _c
				on _a.project_id=_c.project_id
				and _a.description_tid=_c.id
				and _c.language_id=". $language_id ."

			left join
				%PRE%traits_traits _tt
				on _a.project_id=_tt.project_id
				and _a.id=_tt.trait_group_id

			left join
				%PRE%traits_taxon_freevalues _ttf
				on _tt.project_id=_ttf.project_id
				and _tt.id=_ttf.trait_id
				and _ttf.taxon_id =". $taxon_id ."

			left join
				%PRE%traits_values _tv
				on _tt.project_id=_tv.project_id
				and _tt.id=_tv.trait_id

			left join
				%PRE%traits_taxon_values _ttv
				on _tv.project_id=_ttv.project_id
				and _tv.id=_ttv.value_id
				and _ttv.taxon_id =". $taxon_id ."

			where
				_a.project_id=". $project_id."
				and _a.parent_id ".(is_null($parent_id) ? "is null" : "=".$parent_id)."
			group by _a.id
			order by _a.show_order, _a.sysname
		";
		
		return $this->freeQuery( $query );
	}

	public function dropTempTable( $params )
	{
		$table_name=isset($params['table_name']) ? $params['table_name'] : null;

		if( !isset( $table_name ) )
		{
			return;
		}

		$query="drop table %PRE%".$table_name;

		@$this->freeQuery( $query );
	}

	public function createTempTable( $params )
	{
		$table_name=isset($params['table_name']) ? $params['table_name'] : null;

		if( !isset( $table_name ) )
		{
			return;
		}

		$query="
			create table %PRE%".$table_name." (
				{t}id{t} int(11) not null primary key,
				{t}code_1{t} varchar(16) not null,
				{t}code_2{t} varchar(32) not null,
				key {t}key_code_1{t} ({t}code_1{t}), key {t}key_code_2{t} ({t}code_2{t}))";

		$this->freeQuery( $query );
	}

	public function fillTempTable( $params )
	{
		$table_name=isset($params['table_name']) ? $params['table_name'] : null;
		$id_prefix=isset($params['id_prefix']) ? $params['id_prefix'] : null;
		$codes=isset($params['codes']) ? $params['codes'] : null;

		if( !isset( $table_name ) || !isset( $id_prefix )  || !isset( $codes ) )
		{
			return;
		}

		$buffer=array();
		$queries=array();

		foreach((array)$codes as $line=>$code)
		{
			$buffer[]= "(".$line.",'" . $this->escapeString( $code ) . "','". $this->escapeString( $id_prefix . $code ) . "')";
			if ($line > 0 && ($line % 500)==0)
			{
				$queries[]="insert into ".$table_name." values ".implode(",",$buffer);
				$buffer=array();
			}
		}

		if (!empty($buffer))
		{
			$queries[]="insert into ".$table_name." values ".implode(",",$buffer);
		}

		foreach($queries as $query)
		{
			$this->freeQuery( $query );
		}

	}

	public function getResolvedCodes( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$table_name=isset($params['table_name']) ? $params['table_name'] : null;

		if( !isset( $project_id ) || !isset( $table_name ) )
		{
			return;
		}

		$query="select
					_a.id as line,
					_a.code_1 as code,
					ifnull(_b.lng_id,_c.lng_id) as lng_id,
					ifnull(_t1.taxon,_t2.taxon) as taxon

				from %PRE%".$table_name." _a

				left join %PRE%nsr_ids _b
					on _a.code_1 = _b.nsr_id
					and _b.project_id = ".$project_id."
					and _b.item_type = 'taxon'
					and _b.lng_id is not null

				left join %PRE%nsr_ids _c
					on _a.code_2 = _c.nsr_id
					and _c.project_id = ".$project_id."
					and _c.item_type = 'taxon'
					and _c.lng_id is not null

				left join %PRE%taxa _t1
					on _b.lng_id = _t1.id
					and _t1.project_id = ".$project_id."

				left join %PRE%taxa _t2
					on _c.lng_id = _t2.id
					and _t2.project_id = ".$project_id."

				group by _a.id
				order by _a.id
			";

		return $this->freeQuery( $query );
	}

	public function getSubstitutableTraits( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( is_null( $project_id )  ) return;

		$query="
			select
				concat(_a.sysname,': ',_b.sysname) as label,
				concat('trait',':',_b.trait_group_id,':',_b.id) as field

			from
				%PRE%traits_groups _a

			left join
				%PRE%traits_traits _b
				on _a.id=_b.trait_group_id
				and _a.project_id=_b.project_id

			left join
				%PRE%traits_project_types _c
				on _b.project_type_id=_c.id
				and _b.project_id=_c.project_id

			left join
				%PRE%traits_types _d
				on _c.type_id=_d.id

			where
				_a.project_id = " .$project_id  ."
				and _d.sysname in ('stringfree','stringlist','stringlistfree')
		";

		return $this->freeQuery( $query );
	}

    public function getCategories( $params )
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($language_id) ) return;

        $query = "
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				_a.page,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				_a.def_page,
				_a.always_hide,
				_a.external_reference
			from
				%PRE%pages_taxa _a

			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $language_id ."

			where
				_a.project_id=".$project_id;

		return $this->freeQuery($query);

    }



}
