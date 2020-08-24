<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

class SpeciesModel extends AbstractModel
{

    public function __construct()
    {
        parent::__construct();

        $this->connectToDatabase() or die(_('Failed to connect to database '.
            $this->databaseSettings['database'].
        	' with user ' . $this->databaseSettings['user'] . '. ' .
            mysqli_connect_error() . '. Correct the getDatabaseSettings() settings
        	in configuration/admin/config.php.'));

    }

    public function __destruct()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }

    public function getFirstTaxonId($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : null;

		if ( is_null($projectId) || is_null($taxonType) ) return;

        $query = "
			select _a.id
			from %PRE%taxa _a
			left join %PRE%projects_ranks _b on _a.rank_id=_b.id
			left join %PRE%ranks _c on _b.rank_id=_c.id
			where _a.project_id = ".$projectId."
			and _b.lower_taxon = ".($taxonType == 'higher' ? 0 : 1)."
			order by _a.taxon_order, _a.taxon
			limit 1";

        $t = $this->freeQuery($query);

       	return isset($t) ? $t[0]['id'] : null;
    }

    public function getBrowseOrder($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : null;

		if ( is_null($projectId) || is_null($taxonType) ) return;

        $query = '
			select _a.id,_a.taxon
			from %PRE%taxa _a
			left join %PRE%projects_ranks _b on _a.rank_id=_b.id
			where _a.project_id = '.$projectId.'
			and _b.lower_taxon = '.($taxonType == 'higher' ? 0 : 1).'
			order by _a.taxon_order, _a.taxon';

        return $this->freeQuery($query);
    }

    public function getTaxa($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : null;
        $getAll = isset($params['getAll']) ? $params['getAll'] : false;
        $listMax = isset($params['listMax']) ? $params['listMax'] : null;
        $regExp = isset($params['regExp']) ? $params['regExp'] : null;
        $predicateValidNameId = isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : null;
        
		if ( is_null($projectId) ) return;// || is_null($taxonType) ) return;
/*
        $query = "
			select
				SQL_CALC_FOUND_ROWS
				_a.id, _a.taxon, _a.rank_id, _a.parent_id, _a.is_hybrid

            from
				%PRE%taxa _a

            left join %PRE%projects_ranks _b
				on _a.rank_id=_b.id

            left join %PRE%trash_can _tr
				on _tr.lng_id = _a.id
				and _tr.project_id = _a.project_id
				and _tr.item_type = 'taxon'

            where
				_a.project_id = ".$projectId."
				" . ( isset($taxonType) ? " and _b.lower_taxon = ".($taxonType == 'higher' ? 0 : 1) : "" ) . "
				".($getAll ? "" : "and _a.taxon REGEXP '".$regExp."'")."
				and _tr.is_deleted is null

			order by taxon

			".(!empty($listMax) ? "limit ".$listMax : "");
*/
		$query = "
			select
				SQL_CALC_FOUND_ROWS
				_a.id,
 				if(_n.authorship is null, _n.name, trim(replace(_n.name, _n.authorship, ''))) as taxon,
 				_n.authorship as author,
				_n.authorship,
                _a.rank_id,
                _a.parent_id,
                _a.is_hybrid
		    
            from
				%PRE%taxa _a
		    
            left join %PRE%projects_ranks _b
				on _a.rank_id=_b.id
		    
            left join %PRE%trash_can _tr
				on _tr.lng_id = _a.id
				and _tr.project_id = _a.project_id
				and _tr.item_type = 'taxon'
		    
            left join %PRE%names _n
				on _a.id=_n.taxon_id
				and _a.project_id=_n.project_id
				and _n.language_id=" . LANGUAGE_ID_SCIENTIFIC . "
                and _n.type_id=" . $predicateValidNameId . "
                    
            where
				_a.project_id = ".$projectId."
				" . ( isset($taxonType) ? " and _b.lower_taxon = ".($taxonType == 'higher' ? 0 : 1) : "" ) . "
				".($getAll ? "" : "and _a.taxon REGEXP '".$regExp."'")."
				and _tr.is_deleted is null
				    
			order by taxon
				    
			".(!empty($listMax) ? "limit ".$listMax : "");
		
        $taxa = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
        $total = isset($count) ? $count[0]['total'] : 0;

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($taxa, $total);
    }

    public function getTaxonParentage($params)
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

    public function getTaxon($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$nametype_id_validname = isset($params['nametype_id_validname']) ? $params['nametype_id_validname'] : null;
		$nametype_id_preferredname = isset($params['nametype_id_preferredname']) ? $params['nametype_id_preferredname'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if (
			is_null($project_id) ||
			is_null($taxon_id) ||
			is_null($nametype_id_validname) ||
			is_null($nametype_id_preferredname) ||
			is_null($language_id)
		) return;

        $query = "
			select
				_a.id,
				_a.taxon,
				_a.parent_id,
				trim(if(_m.authorship is null,_m.name,replace(_m.name,_m.authorship,''))) as name,
				_m.uninomial,
				_m.specific_epithet,
				_m.infra_specific_epithet,
				_m.authorship,
				_f.rank_id as base_rank_id,
				_f.lower_taxon,
				_g.label as rank,
				ifnull(_q.label,_x.rank) as rank_label,
				_k.name as common_name

			from %PRE%taxa _a

			left join %PRE%names _m
				on _a.id=_m.taxon_id
				and _a.project_id=_m.project_id
				and _m.type_id= ".$nametype_id_validname."
				and _m.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id = ".$nametype_id_preferredname."
				and _k.language_id=".$language_id."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%labels_projects_ranks _g
				on _a.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=". LANGUAGE_ID_SCIENTIFIC ."

			left join %PRE%ranks _x
				on _f.rank_id=_x.id

			left join %PRE%labels_projects_ranks _q
				on _a.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$language_id."

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id =".$project_id."
				and _a.id=".$taxon_id."
				and ifnull(_trash.is_deleted,0)=0";

        $d=$this->freeQuery($query);

		return $d ? $d[0] : null;
    }

    public function getSpeciesCountNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
		$rankId = isset($params['rankId']) ? $params['rankId'] : null;
		$speciesRankId = isset($params['speciesRankId']) ? $params['speciesRankId'] : null;

		if ( is_null($projectId) || is_null($taxonId) || is_null($rankId)  || is_null($speciesRankId) ) return;

        $query = "
			select
				count(_sq.taxon_id) as total,
				ifnull(_sr.established,'undefined') as established
			from
				%PRE%taxon_quick_parentage _sq

			left join %PRE%presence_taxa _sp
				on _sq.project_id=_sp.project_id
				and _sq.taxon_id=_sp.taxon_id

			left join %PRE%presence _sr
				on _sp.project_id=_sr.project_id
				and _sp.presence_id=_sr.id

			left join %PRE%taxa _e
				on _sq.taxon_id = _e.id
				and _sq.project_id = _e.project_id

			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _e.project_id = _f.project_id

			left join %PRE%trash_can _trash
				on _e.project_id = _trash.project_id
				and _e.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_sq.project_id=".$projectId."
				and MATCH(_sq.parentage) AGAINST ('". $this->generateTaxonParentageId( $taxonId ) ."' in boolean mode)
				/* and _sp.presence_id is not null */
				and _f.rank_id".($rankId >= $speciesRankId ? ">=" : "=")." ".$speciesRankId."
				and ifnull(_trash.is_deleted,0)=0

			group by
				_sr.established";

		return $this->freeQuery(array(
			'query' => $query,
			'fieldAsIndex' => 'established'
		));
    }

    public function getTaxonChildrenNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$predicateValidNameId = isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : null;
		$predicatePreferredNameId = isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($predicateValidNameId) || is_null($languageId)  || is_null($taxonId) ) return;

        $query = "
            select
				_a.id,
				_a.taxon,
				_a.parent_id,
				if (
					length(
						trim(
							concat(
								if(_k.uninomial is null,'',concat(_k.uninomial,' ')),
								if(_k.specific_epithet is null,'',concat(_k.specific_epithet,' ')),
								if(_k.infra_specific_epithet is null,'',concat(_k.infra_specific_epithet,' '))
							)
						)
					)=0,
					_k.name,
					trim(
						concat(
							if(_k.uninomial is null,'',concat(_k.uninomial,' ')),
							if(_k.specific_epithet is null,'',concat(_k.specific_epithet,' ')),
							if(_k.infra_specific_epithet is null,'',concat(_k.infra_specific_epithet,' '))
						)
					)
				) as name,
				_f.rank_id,
				_f.lower_taxon,
				ifnull(_g.label,_r.rank) as rank_label,
				".( !is_null($predicatePreferredNameId) && !is_null($languageId) ? "_kpref.name as commonname,"	: "" ) . "
				_k.uninomial,
				_k.specific_epithet,
				_k.infra_specific_epithet,
				_k.authorship

			from %PRE%taxa _a

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=".$predicateValidNameId."
				and _k.language_id=".LANGUAGE_ID_SCIENTIFIC."
			" . 

			( !is_null($predicatePreferredNameId) && !is_null($languageId) ? "
				left join %PRE%names _kpref
					on _a.id=_kpref.taxon_id
					and _a.project_id=_kpref.project_id
					and _kpref.type_id=".$predicatePreferredNameId."
					and _kpref.language_id=".$languageId
				: "" ) . "

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%labels_projects_ranks _g
				on _a.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=". $languageId."

			left join %PRE%ranks _r
				on _f.rank_id=_r.id

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id =".$projectId."
				and _a.parent_id = ".$taxonId."
				and ifnull(_trash.is_deleted,0)=0
			order by _a.taxon";

        return $this->freeQuery($query);
    }

    public function getNameNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$predicateType = isset($params['predicateType']) ? $params['predicateType'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
		$nameId = isset($params['nameId']) ? $params['nameId'] : null;

		if ( is_null($projectId) || is_null($languageId) ) return;

        $query = "
            select
				_a.id,
				_a.taxon_id,
				_t.parent_id,
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
				_a.type_id,
				_x.rank_id as synonym_base_rank_id,
                _b.nametype,
				_a.language_id,
				_c.language,
				_d.label as language_label,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date,
				_p.rank_id as base_rank_id

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id = _b.project_id

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id=_t.project_id

			left join %PRE%projects_ranks _p
				on _t.rank_id=_p.id
				and _t.project_id=_p.project_id

			left join %PRE%projects_ranks _x
				on _a.rank_id=_x.id
				and _a.project_id=_x.project_id

            left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$languageId."

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id
				and _a.project_id=_g.project_id

			where _a.project_id = ".$projectId.
			(!empty($nameId) ? " and _a.id=".$nameId : "").
			(!empty($taxonId) ? " and _a.taxon_id=".$taxonId : "").
			(!empty($predicateType) ? " and _b.nametype=".$predicateType : "");

        $d = $this->freeQuery($query);
        return isset($d[0]) ? $d[0] : null;

    }

    public function getNamesNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($languageId) || is_null($taxonId) ) return;

        $query = "
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
				_x.rank_id as synonym_base_rank_id,
                _b.nametype,
				_a.language_id,
				_c.language,
				_d.label as language_label,
				case
					when _b.nametype = '".PREDICATE_PREFERRED_NAME."' then 10
					when _b.nametype = '".PREDICATE_ALTERNATIVE_NAME."' then 9
					when _b.nametype = '".PREDICATE_VALID_NAME."' then 8
					when _b.nametype = '".PREDICATE_SYNONYM."' then 7
					when _b.nametype = '".PREDICATE_SYNONYM_SL."' then 6

					when _b.nametype = '".PREDICATE_HOMONYM."' then 5
					when _b.nametype = '".PREDICATE_MISSPELLED_NAME."' then 4
					when _b.nametype = '".PREDICATE_INVALID_NAME."' then 3
					else 0
				end as sort_criterium_nametype,
				case
					when _a.language_id = ".LANGUAGE_ID_DUTCH." then 10
					when _a.language_id = ".LANGUAGE_ID_SCIENTIFIC." then 0
					else 5
				end as sort_criterium_language,
				ifnull(_q.label,_r.rank) as rank_label,
				_tf.rank_id as taxon_base_rank_id

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _d.label_language_id=".$languageId."

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%ranks _r
				on _f.rank_id=_r.id

    		left join %PRE%taxa _t
    			on _a.taxon_id=_t.id
    			and _a.project_id = _t.project_id

    		left join %PRE%projects_ranks _tf
    			on _t.rank_id=_tf.id
    			and _t.project_id = _tf.project_id

			left join %PRE%projects_ranks _x
				on _a.rank_id=_x.id
				and _a.project_id=_x.project_id

			left join %PRE%labels_projects_ranks _q
				on _f.id=_q.project_rank_id
				and _f.project_id = _q.project_id
				and _q.language_id=".$languageId."

			where
				_a.project_id = ".$projectId."
				and _a.taxon_id=".$taxonId."

			order by
				sort_criterium_language desc,
				language_label,
				sort_criterium_nametype desc";

        return $this->freeQuery(array(
            'query' => $query,
            'fieldAsIndex' => 'id'
        ));
    }

    public function getPresenceDataNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($languageId) || is_null($taxonId) ) return;

        $query = "
            select
				ifnull(_a.is_indigenous,0) as is_indigenous,
				_a.presence_id,
				_a.presence82_id,
				_a.reference_id,
				_b.label as presence_label,
				_b.information as presence_information,
				_b.information_title as presence_information_title,
				_b.index_label as presence_index_label,
				_c.label as presence82_label,
				_d.label as habitat_label,
				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date

			from %PRE%presence_taxa _a

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$languageId."

			left join %PRE%presence_labels _c
				on _a.presence82_id = _c.presence_id
				and _a.project_id=_c.project_id
				and _c.language_id=".$languageId."

			left join %PRE%habitat_labels _d
				on _a.habitat_id = _d.habitat_id
				and _a.project_id=_d.project_id
				and _d.language_id=".$languageId."

			left join %PRE%actors _e
				on _a.actor_id = _e.id
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.actor_org_id = _f.id
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.reference_id = _g.id
				and _a.project_id=_g.project_id

			where _a.project_id = ".$projectId."
				and _a.taxon_id =".$taxonId;

        return $this->freeQuery($query);
    }

    public function getTrendDataByYear($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($taxonId) ) return;

        $query = "
			select
				_a.trend_year,
				_a.trend,
				_b.source
			from %PRE%taxon_trend_years _a

			left join %PRE%trend_sources _b
				on _a.project_id=_b.project_id
				and _a.source_id=_b.id

			where
				_a.project_id = ".$projectId."
				and _a.taxon_id = ".$taxonId."
			order by _a.trend_year";

        return $this->freeQuery($query);
    }

    public function getTrendDataByTrend($params)
    {

		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($taxonId) ) return;

        $query = "
			select
				_a.trend_label,
				_a.trend,
				_b.source
			from %PRE%taxon_trends _a

			left join %PRE%trend_sources _b
				on _a.project_id=_b.project_id
				and _a.source_id=_b.id

			where
				_a.project_id = ".$projectId."
				and _a.taxon_id = ".$taxonId."
			order by _a.trend_label";

        return $this->freeQuery($query);
    }

    public function getReferenceAuthorsNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

		if ( is_null($projectId) || is_null($literatureId) ) return;

        $query = "
			select
				_a.actor_id, _b.name

			from %PRE%literature2_authors _a

			left join %PRE%actors _b
				on _a.actor_id = _b.id
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$projectId."
				and _a.literature2_id =".$literatureId."
			order by _a.sort_order,_b.name";

        return $this->freeQuery($query);
    }

    public function getReferenceNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

		if ( is_null($projectId) || is_null($literatureId) ) return;

        $query = "
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
				_a.project_id = ".$projectId."
				and _a.id = ".$literatureId;

        $l = $this->freeQuery($query);
        return isset($l[0]) ? $l[0] : null;
    }

    public function doesLanguageHavePreferredNameNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;

		if ( is_null($projectId) || is_null($taxonId) || is_null($languageId) ) return;

        $query = "
			select
				count(*) as total
			from
				%PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id = _b.project_id

			where
				_a.project_id = " . $projectId . "
				and _a.taxon_id=" . $taxonId . "
				and _a.language_id=" . $languageId . "
				and _b.nametype= 'PREDICATE_PREFERRED_NAME'";

        $d = $this->freeQuery($query);

		return isset($d) ? ($d[0]['total'] > 0) : false;
    }

    public function getFirstTaxonIdNsr($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$lower = isset($params['lower']) ? $params['lower'] : true;

		if ( is_null($project_id) ) return;
		
		/*
			query first orders by a "corrected" first character, which is the 
			- ASCII-value of the first character of the lowercased value of the taxon if it's 
				- a letter (ASCII val 97-122), 
				- a number (ASCII val 48-57) or 
				- a opening bracket ( (ASCII val 40).
			- ASCII-value + 122 of the first character it's anything else
			this will avoid the species opening with anything that starts with a strange character other than ( (like a ? for instance)
		*/

        $query = "
			select
				_a.id,
				_a.taxon,
				if(((ascii(lower(_a.taxon)) BETWEEN 0 AND 47) or (ascii(lower(_a.taxon)) BETWEEN 58 AND 96)) and (ascii(lower(_a.taxon))!=40),ascii(lower(_a.taxon))+122,ascii(lower(_a.taxon))) as first_letter_corrected
			from
				%PRE%taxa _a

			left join
				%PRE%trash_can _trash
					on _a.project_id = _trash.project_id
					and _a.id =  _trash.lng_id
					and _trash.item_type='taxon'

			left join
				%PRE%projects_ranks _p
					on _a.project_id = _p.project_id
					and _a.rank_id =  _p.id

			where
				_a.project_id =".$project_id."
				and _a.taxon <>''
				and ifnull(_trash.is_deleted,0)=0
				and _p.lower_taxon= " . ($lower ? "1" : "0" ) . "

			order by
				" . ($lower ? "first_letter_corrected,_a.taxon" : "_p.rank_id asc, first_letter_corrected,_a.taxon" ) . "

			limit 1";


        $d = $this->freeQuery($query);

        return isset($d[0]) && isset($d[0]['id']) ? $d[0]['id'] : null;

    }

    public function getCategoriesNsr($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id)  || is_null($language_id) ) return;

		$query = "
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				".(isset($taxon_id) ? "if(length(_c.content)>0 && _c.publish=1,0,1) as is_empty, " : "")."
				_a.def_page,
				_a.always_hide,
				_a.external_reference,
				_a.page_blocks,
				_a.show_order,
				0 as auto_tab
			from
				%PRE%pages_taxa _a

			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $language_id ."

			".(isset($taxon_id) ? "
				left join %PRE%content_taxa _c
					on _a.project_id=_c.project_id
					and _a.id=_c.page_id
					and _c.taxon_id =".$taxon_id."
					and _c.language_id = ". $language_id ."
				" : "")."

			where
				_a.project_id=".$project_id."
				and _a.always_hide = 0

			order by
				_a.show_order";

        $d = $this->freeQuery($query);

        return $d ? $d : array();

    }

    public function getTaxonMediaNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
		$overview = isset($params['overview']) ? $params['overview'] : false;
        $distributionMaps = isset($params['distributionMaps']) ? $params['distributionMaps'] : false;
        $limit = isset($params['limit']) ? $params['limit'] : null;
        $offset = isset($params['offset']) ? $params['offset'] : null;
        $sort = isset($params['sort']) ? $params['sort'] : null;
        $predicatePreferredNameId =
            isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : null;
        $predicateValidNameId =
            isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : null;


		if (
			is_null($projectId) ||
			is_null($languageId) ||
			is_null($taxonId) ||
			is_null($predicatePreferredNameId) ||
			is_null($predicateValidNameId)
		) return;

        $query = "
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				_f.rank_id as base_rank_id,
				file_name as image,
				file_name as thumb,
				_k.taxon,
				_k.id as taxon_id,
				_k.parent_id,
				ifnull(_zz.name,_z.name) as common_name,
				_j.name,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				".($distributionMaps?
					"_map1.meta_data as meta_map_source,
					 _map2.meta_data as meta_map_description,": "")."

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

			left join %PRE%media_meta _c
				on _m.project_id=_c.project_id
				and _m.id = _c.media_id
				and _c.sys_label = 'beeldbankFotograaf'

			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id

			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%names _z
				on _m.taxon_id=_z.taxon_id
				and _m.project_id=_z.project_id
				and _z.type_id=".$predicatePreferredNameId."
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _zz
				on _m.taxon_id=_zz.taxon_id
				and _m.project_id=_zz.project_id
				and _zz.type_id=".$predicatePreferredNameId."
				and _zz.language_id=".$languageId."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=".$predicateValidNameId."
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%media_meta _map1
				on _m.id=_map1.media_id
				and _m.project_id=_map1.project_id
				and _map1.sys_label='verspreidingsKaartBron'

			left join %PRE%media_meta _map2
				on _m.id=_map2.media_id
				and _m.project_id=_map2.project_id
				and _map2.sys_label='verspreidingsKaartTitel'

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

			where
				_m.project_id=".$projectId."
				and _m.taxon_id=".$taxonId."
				and ifnull(_meta9.meta_data,0)!=".($distributionMaps?'0':'1')."
				".($overview ? "and _m.overview_image=1" : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "");

        $media = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = isset($count) ? $count[0]['total'] : 0;

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($media, $total);
    }

    public function getCollectedLowerTaxonMediaNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
        $limit = isset($params['limit']) ? $params['limit'] : null;
        $offset = isset($params['offset']) ? $params['offset'] : null;
        $predicatePreferredNameId =
            isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : null;
        $predicateValidNameId =
            isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : null;

		if (
			is_null($projectId) ||
			is_null($languageId) ||
			is_null($taxonId) ||
			is_null($predicatePreferredNameId) ||
			is_null($predicateValidNameId)
		) return;

        $query = "
            select
				SQL_CALC_FOUND_ROWS
				_q.taxon_id,
				_m.file_name as image,
				_m.file_name as thumb,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as nomen,
				trim(replace(_j.name,ifnull(_j.authorship,''),'')) as taxon,
				ifnull(_zz.name,_z.name) as name,
				date_format(_meta1.meta_date,'%e %M %Y') as meta_datum,
				_meta2.meta_data as meta_short_desc,
				_meta3.meta_data as meta_geografie,
				date_format(_meta4.meta_date,'%e %M %Y') as meta_datum_plaatsing,
				_meta5.meta_data as meta_copyrights,
				_meta6.meta_data as meta_validator,
				_meta7.meta_data as meta_adres_maker,
				_meta8.meta_data as photographer,
				_meta10.meta_data as meta_license

			from
				%PRE%taxon_quick_parentage _q

			right join %PRE%media_taxon _m
				on _q.taxon_id=_m.taxon_id
				and _q.project_id=_m.project_id
				and _m.id = (
					select
						_m.id
					from
						%PRE%media_taxon _m

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
						_m.taxon_id = _q.taxon_id
						and ifnull(_meta9.meta_data,0)!=1
						and _m.project_id=".$projectId."
					order by
						_m.overview_image desc,_meta4.meta_date desc,_meta1.meta_date desc
					limit 1
				)

			left join %PRE%taxa _k
				on _q.taxon_id=_k.id
				and _q.project_id=_k.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'

			left join %PRE%names _z
				on _q.taxon_id=_z.taxon_id
				and _q.project_id=_z.project_id
				and _z.type_id=".$predicatePreferredNameId."
				and _z.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _zz
				on _m.taxon_id=_zz.taxon_id
				and _m.project_id=_zz.project_id
				and _zz.type_id=".$predicatePreferredNameId."
				and _zz.language_id=".$languageId."

			left join %PRE%names _j
				on _m.taxon_id=_j.taxon_id
				and _m.project_id=_j.project_id
				and _j.type_id=".$predicateValidNameId."
				and _j.language_id=".LANGUAGE_ID_SCIENTIFIC."

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
				and _meta6.language_id=".$languageId."

			left join %PRE%media_meta _meta7
				on _m.id=_meta7.media_id
				and _m.project_id=_meta7.project_id
				and _meta7.sys_label='beeldbankAdresMaker'
				and _meta7.language_id=".$languageId."

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

			where
				_q.project_id=".$projectId."
				and (MATCH(_q.parentage) AGAINST ('". $this->generateTaxonParentageId( $taxonId )."' in boolean mode))
				and ifnull(_trash.is_deleted,0)=0

			order by taxon
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "");

        $media = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = isset($count) ? $count[0]['total'] : 0;

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($media, $total);
    }

    public function taxonMediaCountNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;

		if ( is_null($projectId) || is_null($taxonId) ) return;

        $query = "
			select
				count(distinct _m.taxon_id) as total

			from
				%PRE%taxon_quick_parentage _q

			right join %PRE%media_taxon _m
				on _q.taxon_id=_m.taxon_id
				and _q.project_id=_m.project_id

			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%taxa _k
				on _q.taxon_id=_k.id
				and _q.project_id=_k.project_id

			left join %PRE%projects_ranks _f
				on _k.rank_id=_f.id
				and _k.project_id=_f.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_q.project_id=".$projectId."
				and ifnull(_meta9.meta_data,0)!=1
				and (MATCH(_q.parentage) AGAINST ('". $this->generateTaxonParentageId( $taxonId ) ."' in boolean mode))
				and ifnull(_trash.is_deleted,0)=0";

        $d = $this->freeQuery($query);

		return isset($d[0]) ? $d[0]['total'] : null;
    }

    public function runCheckQuery( $query )
    {
		if (!$query) return;

        $d = $this->freeQuery($query);

		if ($d)
		{
			return $d[0]['result']!=1;
		}
    }

    public function getExternalIdNsr ($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : null;
		$organisation = isset($params['organisation']) ?$params['organisation'] : null;

		if ( is_null($projectId) || is_null($taxonId) || is_null($organisation) ) return;

        $query = "
			select
				name,
				organisation_url,
				general_url,
				service_url,
				external_id
			from %PRE%external_orgs _a

			right join %PRE%external_ids _b
				on _a.project_id=_b.project_id
				and _a.id=_b.org_id
				and _b.taxon_id=".$taxonId."

			where
				_a.project_id = ".$projectId."
				and lower(_a.name) = '" .  $this->escapeString( $organisation ) ."'";

        return $this->freeQuery($query);
    }

    public function getExternalOrgNsr($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$organisation = isset($params['organisation']) ? $params['organisation'] : null;

		if ( is_null($projectId) || is_null($organisation) ) return;

        $query = "
			select
				name,
				organisation_url,
				general_url,
				service_url
			from %PRE%external_orgs
			where
				project_id = ".$projectId."
				and lower(name) = '". $this->escapeString( $organisation ) ."'";

        return $this->freeQuery($query);
    }

    public function getTaxonTraitValue( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
		$trait_group_id = isset($params['trait_group_id']) ? $params['trait_group_id'] : null;
		$trait_id = isset($params['trait_id']) ? $params['trait_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) || is_null($trait_group_id) || is_null($trait_id) ) return;

        $query = "
			select value from
			(
				select
					_b.string_value as value

				from
					%PRE%traits_taxon_values _a

				left join
					%PRE%traits_values _b
					on _a.project_id=_b.project_id
					and _a.value_id=_b.id

				where
					_a.project_id = ".$project_id."
					and _a.taxon_id = ".$taxon_id."
					and _b.trait_id  = ".$trait_id."

				union

				select
					_a.string_value as value

				from
					%PRE%traits_taxon_freevalues _a

				where
					_a.project_id = ".$project_id."
					and _a.taxon_id = ".$taxon_id."
					and _a.trait_id  = ".$trait_id."

			) as unionized
			limit 1
		";

        $d=$this->freeQuery($query);

		return $d ? $d[0]['value'] : null;
	}

    public function getTaxonReferences( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;

        if ( is_null($project_id) || is_null($taxon_id) ) return;

        $literature = [];

        $baseQuery = "
            select
				_a.id,
				_a.language_id,
				_a.label,
				_a.alt_label,
				_a.alt_label_language_id,
				_a.date,
				_a.author,
				_a.publication_type,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
                _a.publisher,
				_a.volume,
				_a.external_link,
				ifnull(_b.name,_a.author) as author

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i
				on _a.periodical_id = _i.id
				and _a.project_id=_i.project_id

			right join [join]

			left join %PRE%actors _b
				on _a.actor_id = _b.id
				and _a.project_id=_b.project_id

			where [where]";

        $joins = [
            // Literate linked directly to taxon
            [
                'join' => "
                    %PRE%literature_taxa _t
                    on _a.project_id=_t.project_id
                    and _a.id=_t.literature_id
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _t.taxon_id= " . $taxon_id . "
                ",
            ],
            // Literature linked to presence status
            [
                'join' => "
                    %PRE%presence_taxa _pt
                    on _a.project_id=_pt.project_id
                    and _a.id=_pt.reference_id
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _pt.taxon_id= " . $taxon_id . "
				    and _pt.reference_id is not null
                ",
            ],
            // Literature linked to traits
            [
                'join' => "
                    %PRE%traits_taxon_references _ttr
                    on _a.project_id=_ttr.project_id
                    and _a.id=_ttr.reference_id
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _ttr.taxon_id= " . $taxon_id . "
                ",
            ],
            // Literature linked to names
            [
                'join' => "
                    %PRE%names _n
                    on _a.project_id=_n.project_id
                    and _a.id=_n.reference_id
                ",
                'where' => "
                    _a.project_id = ".$project_id."
				    and _n.taxon_id= " . $taxon_id . "
				    and _n.reference_id is not null
                ",
            ],
        ];

        foreach ($joins as $d) {
            $query = str_replace(['[join]', '[where]'], [$d['join'], $d['where']], $baseQuery);
            $new = $this->freeQuery($query);
            if ($new) {
                $literature = array_merge($literature, $new);
            }
        }

        // Literature linked to metadata
        $query = "
            select
                t3.id, 
                t3.language_id, 
                t3.label, 
                t3.alt_label, 
                t3.alt_label_language_id, 
                t3.date, 
                t3.author, 
                t3.publication_type, 
                ifnull(t3.publishedin,ifnull(t4.label,null)) as publishedin, 
                ifnull(t3.periodical,ifnull(t5.label,null)) as periodical, 
                t3.pages, 
                t3.volume, 
                t3.external_link, 
                ifnull(t6.name,t3.author) as author 
 
                from 
                     content_taxa as t1
                
                left join rdf as t2 
                    on t1.id = t2.subject_id
                    and t1.project_id = t2.project_id 

                left join literature2 as t3 
                    on t2.object_id = t3.id
                    and t2.project_id = t3.project_id 

                left join literature2 as t4 on 
                    t3.publishedin_id = t4.id 
                    and t3.project_id = t4.project_id 

                left join literature2 as t5 
                    on t3.periodical_id = t5.id 
                    and t3.project_id = t5.project_id

                left join actors as t6 
                    on t3.actor_id = t6.id 
                    and t1.project_id = t6.project_id 

                where 
                    t1.project_id = $project_id 
                    and t1.taxon_id = $taxon_id 
                    and t2.object_type = 'reference'
                
                group by 
                     t2.object_id";

        $new = $this->freeQuery($query);
        if ($new) {
            $literature = array_merge($literature, $new);
        }

        // Sort by author, year, label as per original query
        usort($literature, function($a, $b) {
            $r = $a['author'] <=> $b['author'];
            if ($r == 0) {
                $r = $a['date'] <=> $b['date'];
                if ($r == 0) {
                    $r = $a['label'] <=> $b['label'];
                }
            }
            return $r;
        });

        // Use this existing loop to remove any duplicates
        $done = [];
		foreach((array)$literature as $key=>$val)
		{
		    if (in_array($val['id'], $done)) {
                unset($literature[$key]);
                continue;
            }
            $query = "
                select
                    _a.actor_id,
                    _b.name,
                    _b.name_alt,
                    _b.homepage,
                    _b.gender,
                    _b.is_company,
                    _b.logo_url,
                    _b.employee_of_id

                from %PRE%literature2_authors _a

                left join %PRE%actors _b
                    on _a.actor_id = _b.id
                    and _a.project_id=_b.project_id

                where
                    _a.project_id = " . $project_id . "
                    and _a.literature2_id =" . $val['id'] . "

                order by
                    _a.sort_order,_b.name";

            $literature[$key]['authors'] = $this->freeQuery($query);
            $done[] = $val['id'];
		}

        return $literature;
    }

    public function getTaxonActors ( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
        
        if ( is_null($project_id) || is_null($taxon_id) ) return;
        
        $query = "
            select
                t3.id as actors_taxa_id,
                t1.id as actor_id,
                t1.name as label,
                t1.name_alt,
                t1.homepage,
                t1.gender,
                t1.logo_url,
                t1.employee_of_id,
                t1.is_company,
                t2.name as company_of_name,
                t2.name_alt as company_of_name_alt,
                t2.homepage as company_of_homepage
                
            from
                %PRE%actors as t1
                
            left join
                %PRE%actors as t2 on t1.employee_of_id = t2.id
                
            left join
                %PRE%actors_taxa as t3 on t3.actor_id = t1.id
                
            where
                t3.project_id = $project_id and
                t3.taxon_id = $taxon_id
                
            order by
				t3.sort_order, t1.name";

        return $this->freeQuery($query);
    }
    
    public function getTaxonKeyLinks( $params )
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $language_id = isset($params['language_id']) ? $params['language_id'] : null;

        if ( is_null($project_id) || is_null($taxon_id) || is_null($language_id) ) return;

        $query = "
			select
				*
			from
				%PRE%keysteps _a

			right join %PRE%keysteps_taxa _b
				on _a.project_id=_b.project_id
				and _a.id=_b.keystep_id

			left join %PRE%content_keysteps _c
				on _a.project_id=_c.project_id
				and _a.id=_c.keystep_id
				and _c.language_id= " . $language_id . "

			where
				_a.project_id = " . $project_id ."
				and _b.taxon_id = " . $taxon_id ."
				order by _a.number
			";

        return $this->freeQuery($query);
    }

    public function getCategories($params)
    {
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;
		$language_id = isset($params['language_id']) ? $params['language_id'] : null;
		$taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if ( is_null($project_id) || is_null($language_id) ) return;

		$query = "
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				_a.page,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				".(isset($taxon_id) ? "if(length(_c.content)>0 && _c.publish=1,0,1) as is_empty, " : "")."
				_a.always_hide,
				_a.external_reference,
				_a.page_blocks
			from
				%PRE%pages_taxa _a

			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $language_id ."

			".(isset($taxon_id) ? "
				left join %PRE%content_taxa _c
					on _a.project_id=_c.project_id
					and _a.id=_c.page_id
					and _c.taxon_id =".$taxon_id."
					and _c.language_id = ". $language_id ."
				" : "")."

			where
				_a.project_id=".$project_id;

		return $this->freeQuery($query);
    }

}
