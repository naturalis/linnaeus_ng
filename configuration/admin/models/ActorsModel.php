<?php
include_once (__DIR__ . "/AbstractModel.php");

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
        $limit = isset($params['limit']) ? (int)$params['limit'] : 100000;

        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
                SQL_CALC_FOUND_ROWS
				_a.taxon_id,
				_a.name,
				_b.nametype,
				_c.language,
				_d.label as language_label,
				_g.taxon,
				_g.parent_id,
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
    			")

            limit $limit";

        return $this->freeQuery($query);
    }

    public function getActorPresences ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 100000;

        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
                SQL_CALC_FOUND_ROWS
				_a.taxon_id,
				_g.taxon,
				_g.parent_id,
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
			")
            
            LIMIT $limit";

        return $this->freeQuery($query);
    }

    public function getActorPassports ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 100000;
        
        if (is_null($projectId) || is_null($languageId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
                SQL_CALC_FOUND_ROWS
 				_a.id,
				_a.subject_type,
				_a.predicate,
				_c.taxon,
				_c.id as taxon_id,
				_c.parent_id,
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
 			
            order by taxon, title       
            
            limit $limit ";

        return $this->freeQuery($query);
    }

    public function getActorLiterature ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $expertId = isset($params['expertId']) ? $params['expertId'] : null;
        $name = isset($params['name']) ? $params['name'] : null;
        $nameAlt = isset($params['nameAlt']) ? $params['nameAlt'] : null;
        $limit = isset($params['limit']) ? (int)$params['limit'] : 100000;
        
        if (is_null($projectId) || is_null($expertId)) {
			return null;
		}

        $query = "
            select
                SQL_CALC_FOUND_ROWS
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
			)

            limit $limit";

        return $this->freeQuery($query);
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
    
    public function saveTaxonActor ($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $actor_id = isset($params['actor_id']) ? $params['actor_id'] : null;
        $sort_order = isset($params['sort_order']) ? $params['sort_order'] : 99;
        
        if (is_null($project_id) || is_null($taxon_id) || is_null($actor_id)) {
            return;
        }
        
        $this->freeQuery("select id from %PRE%actors_taxa where project_id = $project_id and
            taxon_id = $taxon_id and actor_id = $actor_id");
        
        if ($this->getAffectedRows() == 0) {
            
            $query = "
    			insert into %PRE%actors_taxa
    				(project_id, taxon_id, actor_id, sort_order, created)
    			values
    				(".$project_id.",".$taxon_id.",".$actor_id.",".$sort_order.", now())";
            
            $this->freeQuery($query);
            
            return $this->getAffectedRows();
        }
        
        return;
    }
    
    public function deleteTaxonActor ($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $taxon_id = isset($params['taxon_id']) ? $params['taxon_id'] : null;
        $actors_taxa_id = isset($params['actors_taxa_id']) ? $params['actors_taxa_id'] : null;
        
        if (is_null($project_id) || is_null($taxon_id) || is_null($actors_taxa_id)) {
            return;
        }
        
        $query = "
			delete from
				%PRE%actors_taxa
			where
				id = " . $actors_taxa_id . "
				and project_id = " . $project_id . "
				and taxon_id = " . $taxon_id;
        
        $this->freeQuery($query);
    }
    
    
    public function getActorTaxa ($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $actor_id = isset($params['actor_id']) ? $params['actor_id'] : null;
        
        if (is_null($project_id) || is_null($actor_id)) return;
        
        $query = "
            select
            
				_a.id,
				_a.taxon,
				_r.id as base_rank_id,
				_r.rank
            
			from %PRE%taxa _a
            
			right join %PRE%actors_taxa _t
				on _a.project_id=_t.project_id
				and _a.id=_t.taxon_id
            
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id
            
			left join %PRE%ranks _r
				on _p.rank_id=_r.id
            
			where
				_a.project_id = ".$project_id."
				and _t.actor_id= " . $actor_id . "
				    
			order by
				_a.taxon
			";
        
        return $this->freeQuery($query);
    }
    
    
}