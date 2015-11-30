<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class Literature2Model extends AbstractModel
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


    public function getReferenceAuthors ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($literatureId)) {
			return null;
		}

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

    public function getTitleAlphabet ($projectId)
    {
        if (!$projectId) {
			return null;
		}

        $query = "
            select
				distinct
                    if(
                        ord(substr(lower(_a.label),1,1))<97||ord(substr(lower(_a.label),1,1))>122,
                        '#',
                        substr(lower(_a.label),1,1)
                    )
                as letter
			from
				%PRE%literature2 _a
			where
				_a.project_id = ".$projectId;

        return $this->freeQuery($query);
    }


    public function getAuthorAlphabet ($projectId)
    {
        if (!$projectId) {
			return null;
		}

        $query = "
            select distinct * from (
				select
					distinct if(ord(substr(lower(_a.author),1,1))<97||ord(substr(lower(_a.author),1,1))>122,'#',substr(lower(_a.author),1,1)) as letter
				from
					%PRE%literature2 _a
				where
					_a.project_id = ".$projectId."
			union
				select
					distinct if(ord(substr(lower(_f.name),1,1))<97||ord(substr(lower(_f.name),1,1))>122,'#',substr(lower(_f.name),1,1)) as letter

				from
					%PRE%literature2 _a

				left join %PRE%actors _f
					on _a.actor_id = _f.id
					and _a.project_id=_f.project_id

				where
					_a.project_id = ".$projectId."
			) as unification
			order by letter";

        return $this->freeQuery($query);
    }


    public function getReference ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($literatureId)) {
			return null;
		}

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

        return $this->freeQuery($query);
    }


    public function getReferences ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $publicationTypeId = isset($params['publicationTypeId']) ? $params['publicationTypeId'] : null;

        if (is_null($projectId)) {
			return null;
		}

        $query = "
            select
				_a.id,
				_a.language_id,
				_a.label,
				_a.alt_label,
				_a.alt_label_language_id,
				_a.date,
				_a.author,
				_a.publication_type,
				_a.citation,
				_a.source,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i
				on _a.periodical_id = _i.id
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$projectId."
				".(!is_null($publicationTypeId) ?
					"and ".
					(is_array($publicationTypeId) ?
						"_a.publication_type_id in (" . implode(",",array_map('intval',$publicationTypeId)). ")" :
						"_a.publication_type_id = " .
					        mysqli_real_escape_string($this->databaseConnection, intval($publicationTypeId)) ) :
					"" )."";

        return $this->freeQuery($query);
    }


    public function getReferenceLinksNames ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($literatureId)) {
			return null;
		}

        $query = "select
				_a.taxon_id,
				_a.name,
				_b.nametype,
				_c.language,
				_d.label as language_label,
				_g.taxon

			from %PRE%names _a

			left join %PRE%name_types _b
				on _a.type_id=_b.id
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$languageId."

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id
				and _a.project_id=_g.project_id

		where
			_a.project_id = ".$projectId."
			and _a.reference_id=".$literatureId;

        return $this->freeQuery($query);
    }


    public function getReferenceLinksPresences ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($literatureId)) {
			return null;
		}

        $query = "elect
				_a.taxon_id,
				_g.taxon,
				_a.presence_id,
				_b.label as presence_label,
				_a.reference_id

			from %PRE%presence_taxa _a

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id
				and _a.project_id=_g.project_id

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$languageId."

			where _a.project_id = ".$projectId."
				and _a.reference_id=".$literatureId;

        return $this->freeQuery($query);
    }

    public function getReferenceLinksTraits ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($literatureId)) {
			return null;
		}

        $query = "select
				_a.taxon_id,
				_b.taxon,
				_a.trait_group_id,
				_c.sysname
			from
				%PRE%traits_taxon_references _a

			left join %PRE%taxa _b
				on _a.project_id=_b.project_id
				and _a.taxon_id=_b.id

			left join %PRE%traits_groups _c
				on _a.project_id=_c.project_id
				and _a.trait_group_id=_c.id

			where
				_a.project_id=".$projectId."
				and _a.reference_id=".$literatureId."
			order
				by _b.taxon";

        return $this->freeQuery($query);
    }

    public function getReferenceLinksPassports ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $literatureId = isset($params['literatureId']) ? $params['literatureId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($literatureId)) {
			return null;
		}

        $query = "
            select _b.taxon_id, _e.taxon, _d.title
			from
				%PRE%rdf _a

			left join
				%PRE% content_taxa _b
				on _a.subject_id=_b.id
				and _a.project_id=_b.project_id
				and _b.language_id=".$languageId."

			left join
				%PRE% taxa _e
				on _b.taxon_id=_e.id
				and _a.project_id=_e.project_id

			left join
				%PRE% pages_taxa _c
				on _b.page_id=_c.id
				and _a.project_id=_c.project_id

			left join
				%PRE% pages_taxa_titles _d
				on _c.id=_d.page_id
				and _c.project_id=_d.project_id
				and _d.language_id=".$languageId."


			where
				_a.project_id=".$projectId."
				and _a.object_type = 'reference'
				and _a.subject_type = 'passport'
				and _a.object_id = ".$literatureId."

			order by
				_e.taxon, _c.show_order";

        return $this->freeQuery($query);
    }


    public function getActors ($projectId)
    {
        if (!$projectId) {
			return null;
		}

        $query = "
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
				_e.project_id = ".$projectId."

			order by
				_e.is_company, _e.name";

        return $this->freeQuery($query);
    }

    public function getLanguages ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $sort = isset($params['sort']) ? $params['sort'] : null;

        if (is_null($projectId) || is_null($languageId)) {
			return null;
		}

        $query = "
            select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label
				".$sort."
			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$projectId."
				and _d.label_language_id=".$languageId."
				order by ".(!empty($sort) ? "sort_criterium desc, " : "")."label asc";

        return $this->freeQuery($query);
    }

    public function getPublicationTypes ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] : null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $sortOrder = isset($params['sortOrder']) ? $params['sortOrder'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($sortOrder)) {
			return null;
		}

        $query = "
            select
				_a.id,
				_a.sys_label,
				ifnull(_b.label,_a.sys_label) as label,
				count(_c.id) as total

			from %PRE%literature2_publication_types _a

			left join %PRE%literature2_publication_types_labels _b
				on _a.id = _b.publication_type_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$languageId."

			left join %PRE%literature2 _c
				on _a.id = _c.publication_type_id
				and _a.project_id=_c.project_id

			where
				_a.project_id = ".$projectId ."
			group by
				_a.id
			order by " . $sortOrder;

        return $this->freeQuery($query);
    }



}

?>