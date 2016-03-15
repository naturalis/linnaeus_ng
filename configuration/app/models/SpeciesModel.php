<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class SpeciesModel extends AbstractModel
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


    public function getFirstTaxonId ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;

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


    public function getBrowseOrder ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;

        $query = '
			select _a.id,_a.taxon
			from %PRE%taxa _a
			left join %PRE%projects_ranks _b on _a.rank_id=_b.id
			where _a.project_id = '.$projectId.'
			and _b.lower_taxon = '.($taxonType == 'higher' ? 0 : 1).'
			order by _a.taxon_order, _a.taxon';

        return $this->freeQuery($query);
    }


    public function getTaxa ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonType = isset($params['taxonType']) ? $params['taxonType'] : false;
        $getAll = isset($params['getAll']) ? $params['getAll'] : false;
        $listMax = isset($params['listMax']) ? $params['listMax'] : false;
        $regExp = isset($params['regExp']) ? $params['regExp'] : false;

        $query = "
			select
				SQL_CALC_FOUND_ROWS
				_a.id, _a.taxon, _a.rank_id, _a.parent_id, _a.is_hybrid
			from
				%PRE%taxa _a
			left join %PRE%projects_ranks _b
				on _a.rank_id=_b.id
			where
				_a.project_id = ".$projectId."
				and _b.lower_taxon = ".($taxonType == 'higher' ? 0 : 1)."
				".($getAll ? "" : "and _a.taxon REGEXP '".$regExp."'")."
			order by taxon
			".(!empty($listMax) ? "limit ".$listMax : "");

        $taxa = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = $count[0]['total'];

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($taxa, $total);
    }


    public function getTaxonParentage ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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


    public function getTaxonNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$predicatePreferredNameId =
            isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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
				_f.rank_id,
				_f.lower_taxon,
				_g.label as rank,
				ifnull(_q.label,_x.rank) as rank_label,
				ifnull(_k.name,_kk.name) as common_name

			from %PRE%taxa _a

			left join %PRE%names _m
				on _a.id=_m.taxon_id
				and _a.project_id=_m.project_id
				and _m.type_id= ".$predicatePreferredNameId."
				and _m.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%names _k
				on _a.id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id = ".$predicatePreferredNameId."
				and _k.language_id=".$languageId."

			left join %PRE%names _kk
				on _a.id=_kk.taxon_id
				and _a.project_id=_kk.project_id
				and _kk.type_id = ".$predicatePreferredNameId."
				and _kk.language_id=".LANGUAGE_ID_DUTCH."

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
				and _q.language_id=".$languageId."

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id =".$projectId."
				and _a.id=".$taxonId."
				and ifnull(_trash.is_deleted,0)=0";

        return $this->freeQuery($query);
    }


    public function getSpeciesCountNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$rankId = isset($params['rankId']) ? $params['rankId'] : false;
		$speciesRankId = isset($params['speciesRankId']) ? $params['speciesRankId'] : false;

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
				and MATCH(_sq.parentage) AGAINST ('".$taxonId."' in boolean mode)
				and _sp.presence_id is not null
				and _f.rank_id".($rankId >= $speciesRankId ? ">=" : "=")." ".$speciesRankId."
				and ifnull(_trash.is_deleted,0)=0

			group by _sr.established";

		return $this->freeQuery(array(
			'query' => $query,
			'fieldAsIndex' => 'established'
		));
    }



    public function getTaxonChildrenNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$predicateValidNameId =
            isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

        $query = "
            select
				_a.id,
				_a.taxon,
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
				ifnull(_g.label,_gg.label) as rank_label,
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

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%labels_projects_ranks _g
				on _a.rank_id=_g.project_rank_id
				and _a.project_id = _g.project_id
				and _g.language_id=". $languageId."

			left join %PRE%labels_projects_ranks _gg
				on _a.rank_id=_gg.project_rank_id
				and _a.project_id = _gg.project_id
				and _gg.language_id=". LANGUAGE_ID_DUTCH."

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


    public function getNameNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$predicateType = isset($params['predicateType']) ? $params['predicateType'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$nameId = isset($params['nameId']) ? $params['nameId'] : false;

        $query = "
            select
				_a.id,
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
				_a.type_id,
				_b.nametype,
				_a.language_id,
				_c.language,
				_d.label as language_label,
				_e.name as expert_name,
				_f.name as organisation_name,
				_g.label as reference_label,
				_g.author as reference_author,
				_g.date as reference_date

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id = _b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$languageId."

			left join %PRE%actors _e
				on _a.expert_id = _e.id
				and _a.project_id=_e.project_id

			left join %PRE%actors _f
				on _a.organisation_id = _f.id
				and _a.project_id=_f.project_id

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


    public function getNamesNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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
				end as sort_criterium_language

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _d.label_language_id=".$languageId."

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


    public function getPresenceDataNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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


    public function getTrendDataByYear ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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


    public function getTrendDataByTrend ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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



    public function getReferenceAuthorsNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$literatureId = isset($params['literatureId']) ? $params['literatureId'] : false;

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


    public function getReferenceNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$literatureId = isset($params['literatureId']) ? $params['literatureId'] : false;

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


    public function doesLanguageHavePreferredNameNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$predicatePreferredName =
            isset($params['predicatePreferredName']) ? $params['predicatePreferredName'] : false;

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
				and _b.nametype= '".$predicatePreferredName . "'";

        $d = $this->freeQuery($query);

		return $d[0]['total'] > 0;

    }


    public function getFirstTaxonIdNsr ($projectId)
    {

		if (!$projectId) {
		    return false;
		}

        $query = "
			select
				_a.id,
				_a.taxon

			from %PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id =  _trash.lng_id
				and _trash.item_type='taxon'

			where
				_a.project_id =".$projectId."
				and _a.taxon <>''
				and ifnull(_trash.is_deleted,0)=0

			order by
				_a.taxon
			limit 1";

        $d = $this->freeQuery($query);

        return $d[0]['id'];

    }


    public function getCategoriesNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$hasRedirectTo = isset($params['hasRedirectTo']) ? $params['hasRedirectTo'] : false;
		$hasCheckQuery = isset($params['hasCheckQuery']) ? $params['hasCheckQuery'] : false;
		$hasAlwaysHide = isset($params['hasAlwaysHide']) ? $params['hasAlwaysHide'] : false;
		$hasExternalReference = isset($params['hasExternalReference']) ? $params['hasExternalReference'] : false;

        $query = "
			select
				_a.id,
				ifnull(_b.title,_a.page) as title,
				concat('TAB_',replace(upper(_a.page),' ','_')) as tabname,
				".(isset($taxonId) ? "if(length(_c.content)>0 && _c.publish=1,0,1) as is_empty, " : "")."
				_a.def_page,
			".($hasRedirectTo ? '_a.redirect_to,' : '')."
			".($hasCheckQuery ? '_a.check_query,' : '')."
			".($hasAlwaysHide ? '_a.always_hide,' : '')."
			".($hasExternalReference ? '_a.external_reference,' : '')."
				_a.show_order
			from
				%PRE%pages_taxa _a

			left join %PRE%pages_taxa_titles _b
				on _a.project_id=_b.project_id
				and _a.id=_b.page_id
				and _b.language_id = ". $languageId ."

			".(isset($taxonId) ? "
				left join %PRE%content_taxa _c
					on _a.project_id=_c.project_id
					and _a.id=_c.page_id
					and _c.taxon_id =".$taxonId."
					and _c.language_id = ". $languageId ."
				" : "")."

			where
				_a.project_id=".$projectId."
				".($hasAlwaysHide ? 'and _a.always_hide = 0' : '')."

			order by
				_a.show_order";

        $d = $this->freeQuery($query);

        return $d ? $d : array();

    }


    public function getTaxonMediaNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$overview = isset($params['overview']) ? $params['overview'] : false;
        $distributionMaps = isset($params['distributionMaps']) ? $params['distributionMaps'] : false;
        $limit = isset($params['limit']) ? $params['limit'] : false;
        $offset = isset($params['offset']) ? $params['offset'] : false;
        $sort = isset($params['sort']) ? $params['sort'] : false;
        $predicatePreferredNameId =
            isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : false;
        $predicateValidNameId =
            isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : false;

        $query = "
			select
				SQL_CALC_FOUND_ROWS
				_m.id,
				_m.taxon_id,
				file_name as image,
				file_name as thumb,
				_k.taxon,
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
				_m.project_id=".$projectId."
				and _m.taxon_id=".$taxonId."
				and ifnull(_meta9.meta_data,0)!=".($distributionMaps?'0':'1')."
				".($overview ? "and _m.overview_image=1" : "")."

			".(isset($sort) ? "order by ".$sort : "")."
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "");

        $media = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = $count[0]['total'];

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($media, $total);
    }


    public function getCollectedLowerTaxonMediaNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$languageId = isset($params['languageId']) ? $params['languageId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
        $limit = isset($params['limit']) ? $params['limit'] : false;
        $offset = isset($params['offset']) ? $params['offset'] : false;
        $predicatePreferredNameId =
            isset($params['predicatePreferredNameId']) ? $params['predicatePreferredNameId'] : false;
        $predicateValidNameId =
            isset($params['predicateValidNameId']) ? $params['predicateValidNameId'] : false;

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

					left join %PRE%media_meta _meta9
						on _m.id=_meta9.media_id
						and _m.project_id=_meta9.project_id
						and _meta9.sys_label='verspreidingsKaart'

					where
						_m.taxon_id = _q.taxon_id
						and ifnull(_meta9.meta_data,0)!=1
						and _m.project_id=".$projectId."
					order by
						_m.overview_image desc,_meta4.meta_date desc
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
				and (MATCH(_q.parentage) AGAINST ('".$taxonId."' in boolean mode))
				and ifnull(_trash.is_deleted,0)=0

			order by taxon
			".(isset($limit) ? "limit ".$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".$offset : "");

        $media = $this->freeQuery($query);

		$count = $this->freeQuery('select found_rows() as total');
		$total = $count[0]['total'];

		// Non-associative array, so list() can be used to quickly assign variables in controller
		return array($media, $total);
    }


    public function taxonMediaCountNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;

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
				and (MATCH(_q.parentage) AGAINST ('".$taxonId."' in boolean mode))
				and ifnull(_trash.is_deleted,0)=0";

        $d = $this->freeQuery($query);

		return isset($d[0]) ? $d[0]['total'] : null;

    }


    public function checkQueryResult( $query )
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
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$taxonId = isset($params['taxonId']) ? $params['taxonId'] : false;
		$organisation = isset($params['organisation']) ? $this->escapeString($params['organisation']) : false;

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
				and lower(_a.name) = '". $organisation ."'";

        return $this->freeQuery($query);

    }


    public function getExternalOrgNsr ($params)
    {
		if (!$params) {
		    return false;
		}

		$projectId = isset($params['projectId']) ? $params['projectId'] : false;
		$organisation = isset($params['organisation']) ? $this->escapeString($params['organisation']) : false;

        $query = "
			select
				name,
				organisation_url,
				general_url,
				service_url
			from %PRE%external_orgs
			where
				project_id = ".$projectId."
				and lower(name) = '".$organisation ."'";

        return $this->freeQuery($query);

    }

}

?>