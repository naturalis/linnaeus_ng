<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class TaxonGroupModel extends AbstractModel
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


	public function getTaxonGroups ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $parentId = isset($params['parentId']) ? $params['parentId'] : null;

        if (is_null($projectId) || is_null($languageId)) {
			return null;
		}

		$query = "
			select
				_a.*,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a

			left join
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $languageId ."

			where
				_a.project_id=". $projectId."
				and _a.parent_id ".(is_null($parentId) ? "is null" : "=".$parentId)."
				order by _a.show_order";

        return $this->freeQuery($query);
	}


	public function getTaxonGroup ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $languageId = isset($params['languageId']) ? $params['languageId'] : null;
        $groupId = isset($params['groupId']) ? $params['groupId'] : null;

        if (is_null($projectId) || is_null($languageId) || is_null($groupId)) {
			return null;
		}

		$query = "
			select
				_a.id,
				_a.parent_id,
				_a.sys_label,
				_b.name,
				_b.description
			from
				%PRE%taxongroups _a

			left join
				%PRE%taxongroups_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.taxongroup_id
				and _b.language_id=". $languageId ."

			where
				_a.project_id=". $projectId."
				and _a.id=".$groupId;

        $d = $this->freeQuery($query);

        return $d[0];
	}

	public function getTaxa ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $parentId = isset($params['parentId']) ? $params['parentId'] : null;

        if (is_null($projectId)) {
			return null;
		}

		$query = "
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank
			from
				%PRE%taxa _t

			left join %PRE%projects_ranks _f
				on _t.project_id = _f.project_id
				and _t.rank_id=_f.id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			where
				_t.project_id=". $projectId."
				and _t.parent_id ".(is_null($parentId) ? "is null" : "=".$parentId)."

			order by
					_t.taxon";

        return $this->freeQuery($query);
	}


	public function getTaxonGroupTaxa ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $groupId = isset($params['groupId']) ? $params['groupId'] : null;

        if (is_null($projectId)) {
			return null;
		}

		$query = "
			select
				_t.id,
				_t.taxon,
				_t.parent_id,
				_t.is_hybrid,
				_q.rank,
				_a.show_order
			from
				%PRE%taxongroups_taxa _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			left join %PRE%projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id = _f.project_id

			left join %PRE%ranks _q
				on _f.rank_id=_q.id

			where
				_a.project_id=".$projectId."
				and _a.taxongroup_id = ".$groupId."
			order by
				_a.show_order";

        return $this->freeQuery($query);
	}


	public function getTaxongroupMemberships ($params)
    {
        $projectId = isset($params['projectId']) ? $params['projectId'] :  null;
        $groupId = isset($params['groupId']) ? $params['groupId'] : null;

        if (is_null($projectId) || is_null($groupId)) {
			return null;
		}

		$query = "
			select
				_a.id,_a.taxongroup_id,_g.sys_label
			from
				%table% _a
			left join %PRE%taxongroups _g
				on _a.project_id=_g.project_id
				and _a.taxongroup_id=_g.id
			where
				_a.project_id=".$projectId."
				and _a.taxon_id = ".$groupId;

        return $this->freeQuery(array(
			"query" => $query,
			"fieldAsIndex" => "taxongroup_id"
		));
	}

	public function getAllCommonnames ($projectId)
    {
        if (!$projectId) {
			return null;
		}

		$query = "
			select
				taxon_id,commonname
			from %PRE%commonnames
			where
				project_id = ".$projectId."
				and language_id=".LANGUAGE_ID_DUTCH."
				order by show_order desc";

        return $this->freeQuery(array(
			"query" => $query,
			"fieldAsIndex" => "taxon_id"
		));
	}



	public function getOrphanedTaxonGroupTaxa ($projectId)
    {
        if (!$projectId) {
			return null;
		}

		$query = "
			select
				_a.id
			from
				%PRE%taxongroups_taxa _a

			left join %PRE%taxa _t
				on _a.taxon_id=_t.id
				and _a.project_id = _t.project_id

			where
				_a.project_id=".$projectId."
				and _t.id is null";

        return $this->freeQuery($query);
	}


	public function getOrphanedTaxonGroups ($projectId)
    {
        if (!$projectId) {
			return null;
		}

		$query = "
			select
				_a.*
			from
				%PRE%taxongroups _a

			left join
				%PRE%taxongroups _b
				on _a.project_id=_b.project_id
				and _a.parent_id=_b.id

			where
				_a.project_id=". $projectId."
				and _a.parent_id is not null
				and _b.id is null";

        return $this->freeQuery($query);
	}




}

