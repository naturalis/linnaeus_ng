<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class TreeModel extends AbstractModel
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

    public function __destruct()
    {
        if ($this->databaseConnection)
		{
            $this->disconnectFromDatabase();
        }
        parent::__destruct();
    }


	public function getTreeTop($projectId)
    {
         if (!$projectId) {
			return null;
		}

		$query = "
			select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where
				_a.project_id = ".$projectId."
				and _a.parent_id is null
				and _r.id < 10";

        return $this->freeQuery($query);
	}

    public function getTreeNode($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $node = isset($params['node']) ? $params['node'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($node)) {
			return null;
		}

        $query = "
			select
				_a.id,
				_a.parent_id,
				_a.is_hybrid,
				_a.rank_id,
				_a.taxon,
				_r.rank,
				_q.label as rank_label,
				_p.rank_id as base_rank

			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%labels_projects_ranks _q
				on _a.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$languageId."

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where
				_a.project_id = ".$projectId."
				and (_a.id = ".$node." or _a.parent_id = ".$node.")

			order by
				taxon_order, label";

        return $this->freeQuery($query);
    }


    public function countChildrenTaxon($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $parentId = isset($params['parentId']) ? $params['parentId'] : null;

        if (is_null($projectId) || is_null($parentId)) {
			return null;
		}

        $query = "
			select
				count(*) as total
			from
				%PRE%taxon_quick_parentage
			where
				project_id = ".$projectId."
				and MATCH(parentage) AGAINST ('".$parentId."' in boolean mode)
				taxon_order, label";

        $d = $this->freeQuery($query);

        return $d[0]['total'];
    }



    public function countChildrenSpecies($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $parentId = isset($params['parentId']) ? $params['parentId'] : null;
        $rankId = isset($params['rankId']) ? $params['rankId'] : null;

        if (is_null($projectId) || is_null($parentId)) {
			return null;
		}

        $query = "
			select
				count(_sq.taxon_id) as total,
				_sq.taxon_id
			from
				%PRE%taxon_quick_parentage _sq

			left join %PRE%taxa _e
				on _sq.taxon_id = _e.id
				and _sq.project_id = _e.project_id

			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _e.project_id = _f.project_id

			where
				_sq.project_id=".$projectId."
				and _f.rank_id".($rankId >= SPECIES_RANK_ID ? ">=" : "=")." ".SPECIES_RANK_ID."
				and MATCH(_sq.parentage) AGAINST ('".$parentId."' IN BOOLEAN MODE)
			group by _sq.taxon_id";

        $d = $this->freeQuery($query);

        return $d[0]['total'];
    }


    public function hasChildren($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $parentId = isset($params['parentId']) ? $params['parentId'] : null;

        if (is_null($projectId) || is_null($parentId)) {
			return null;
		}

        $query = "
			select
				count(*) as total
			from
				%PRE%taxa
			where
				project_id = ".$projectId."
				and parent_id = ".$parentId;

        $d = $this->freeQuery($query);

        return $d[0]['total'] > 0;
    }

}
