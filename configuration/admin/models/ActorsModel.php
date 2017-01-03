<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class ActorsModel extends AbstractModel
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

    public function getCompanyAlphabet ($projectId)
    {
		if (!$projectId) {
		    return false;
		}

        $query = "
            select
				distinct if(ord(substr(lower(name),1,1))<97||ord(substr(lower(name),1,1))>122,'#',substr(lower(name),1,1)) as letter
			from
				%PRE%actors
			where
				project_id = ".$projectId."
				and is_company != 1
			order by letter";

        return $this->freeQuery($query);
    }

	public function getIndividualAlphabet ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $actorId = isset($params['actorId']) ? $params['actorId'] : null;

        if (is_null($projectId) || is_null($actorId)) {
			return null;
		}

		$query = "
		    select
				_a.*,
				_e.name as employer_name,
				nsr_id

			from %PRE%actors _a

			left join %PRE%actors _e
				on _a.employee_of_id = _e.id
				and _a.project_id=_e.project_id

			left join %PRE%nsr_ids _ids
				on _a.id =_ids.lng_id
				and _a.project_id = _ids.project_id
				and _ids.item_type = 'actor'

			where
				_a.project_id = ".$projectId."
				and _a.id = ".$actorId;

        return $this->freeQuery($query);
	}

    public function getAllActors ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $search = isset($params['search']) ? $params['search'] : null;
        $isCompany = isset($params['isCompany']) ? $params['isCompany'] : null;
        $matchStartOnly = isset($params['matchStartOnly']) ? $params['matchStartOnly'] : null;

        if (is_null($projectId)) {
			return null;
		}

        $query = "
            select
				_a.*, _e.name as employer_name

			from %PRE%actors _a

			left join %PRE%actors _e
				on _a.employee_of_id = _e.id
				and _a.project_id=_e.project_id

			where
				_a.project_id = ".$projectId."
				and _a.is_company = ".($isCompany ? '1' : '0' )."
				". ($search!='*' ? "
					and (
						_a.name like '".($matchStartOnly ? '':'%').
				            mysqli_real_escape_string($this->databaseConnection, $search)."%' or
						_a.name_alt like '".($matchStartOnly ? '':'%').
				            mysqli_real_escape_string($this->databaseConnection, $search)."%'
					)" : "")."

			order by _a.name";

        return $this->freeQuery($query);
    }

    public function getActorNames ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
				_a.taxon_id,
				_a.name,
				_b.nametype,
				_c.language,
				_d.label as language_label,
				_g.taxon,
				_p.rank_id as base_rank_id

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

			left join %PRE%projects_ranks _p
				on _g.rank_id = _p.id
				and _g.project_id=_p.project_id

    		where
    			_a.project_id = ".$projectId."
    			and (
    				_a.expert_id=".$expertId." or
    				_a.organisation_id=".$expertId.
    			")";

        return $this->freeQuery($query);
    }

    public function getActorPresences ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
				_a.taxon_id,
				_g.taxon,
				_a.presence_id,
				_b.label as presence_label,
				_a.reference_id,
				_p.rank_id as base_rank_id

			from %PRE%presence_taxa _a

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id
				and _a.project_id=_g.project_id

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id
				and _a.project_id=_b.project_id
				and _b.language_id=".$languageId."

			left join %PRE%projects_ranks _p
				on _g.rank_id = _p.id
				and _g.project_id=_p.project_id

			where _a.project_id = ".$projectId."
				and (
				_a.actor_id=".$expertId." or
				_a.actor_org_id=".$expertId.
			")";

        return $this->freeQuery($query);
    }

    public function getActorPassports ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
				_a.id,
				_a.subject_type,
				_a.predicate,
				_c.taxon,
				_d.title,
				_b.taxon_id,
				_p.rank_id as base_rank_id

			from
				%PRE%rdf _a

			left join %PRE%content_taxa _b
				on _a.project_id = _b.project_id
				and _a.subject_id = _b.id

			left join %PRE%pages_taxa_titles _d
				on _a.project_id = _d.project_id
				and _b.page_id = _d.page_id
				and _d.language_id = ".$languageId."

			left join %PRE%taxa _c
				on _a.project_id = _b.project_id
				and _b.taxon_id = _c.id

			left join %PRE%projects_ranks _p
				on _c.rank_id = _p.id
				and _c.project_id=_p.project_id

			where
				_a.project_id = ".$projectId."
				and _a.object_id=".$expertId."
				and _a.object_type='actor'
				and _a.subject_type='passport'
			order by taxon, title";

        return $this->freeQuery($query);
    }

    public function getActorLiterature ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;
        $name = isset($params['name']) ? $params['name'] : null;
        $nameAlt = isset($params['nameAlt']) ? $params['nameAlt'] : null;

        if (is_null($projectId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
				distinct
				_b.id,
				_b.label

			from
				%PRE%literature2_authors _a

			left join %PRE%literature2 _b
				on _a.literature2_id = _b.id
				and _a.project_id=_b.project_id

			where
				_a.project_id = ".$projectId."
				and (
					_a.actor_id =".$expertId."
					".( !empty($name) ? "or _b.author like '%".
					   mysqli_real_escape_string($this->databaseConnection, $name) ."%'" : "" )."
					".( !empty($nameAlt) ? "or _b.author like '%".
					   mysqli_real_escape_string($this->databaseConnection, $nameAlt) ."%'" : "" )."
				)";

        return $this->freeQuery($query);
    }

}