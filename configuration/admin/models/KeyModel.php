<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class KeyModel extends AbstractModel
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

	public function getStepsLeadingToThisOne( $params )
	{
		$res_keystep_id=isset($params['res_keystep_id']) ? $params['res_keystep_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($res_keystep_id) || is_null($language_id) || is_null($project_id) )
			return;

		$query="
			select
				_a.id,
				_a.number,
				_b.res_keystep_id,
				_c.title,
				_c.content

			from %PRE%keysteps _a

			left join %PRE%choices_keysteps _b
				on _b.project_id = _a.project_id
				and _b.keystep_id = _a.id

			left join %PRE%content_keysteps _c
				on _c.project_id = _a.project_id
				and _c.keystep_id = _a.id
				and _c.language_id = " . $language_id . "

			where _a.project_id = " . $project_id . "
				and _a.is_start = 0
				and _b.res_keystep_id = " . $res_keystep_id . "

			order by
				_c.title
		";

		return $this->freeQuery($query);
	}

	public function getKeySections( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($language_id) || is_null($project_id) )
			return;

		$query="
				SELECT
					_a.id, _a.number, _b.res_keystep_id, _c.title, _c.content

				from %PRE%keysteps _a

				left join %PRE%choices_keysteps _b
					on _b.project_id = _a.project_id
					and _b.res_keystep_id = _a.id

				left join %PRE%content_keysteps _c
					on _c.project_id = _a.project_id
					and _c.keystep_id = _a.id
					and _c.language_id = " . $language_id . "

				where _a.project_id = " . $project_id ."
					and _a.is_start = 0
					and _b.res_keystep_id is null

				order by _c.title
			";

		return $this->freeQuery($query);
	}

    public function getKeysteps( $params )
	{
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$id=isset($params['id']) ? $params['id'] : null;
		$exclude=isset($params['exclude']) ? $params['exclude'] : null;
		$is_start=isset($params['is_start']) ? $params['is_start'] : null;
        $order=isset($params['order']) ? $params['order'] : 'number';

		if ( is_null($language_id) || is_null($project_id) )
			return;

        return $this->freeQuery("
			select
				_a.*,
				_b.title,
				_b.content

			from %PRE%keysteps _a

			left join %PRE%content_keysteps _b
				on _a.project_id=_b.project_id
				and _a.id=_b.keystep_id
				and _b.language_id = " . $language_id . "

			where
				_a.project_id = " . $project_id . "
				".( !empty($id) ? "and _a.id = ".$id : "" )."
				".( !empty($exclude) ? "and _a.id != ".$exclude : "" )."
				".( !empty($is_start) ? "and _a.is_start = 1" : "" )."
			order by
				_a.".$order."
		");

    }

    public function getTaxaInKey( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$order=isset($params['order']) ? $params['order'] : null;

		if ( is_null($project_id) )
			return;

        return $this->freeQuery("
			select
				_a.id,
				_a.taxon,
				_a.rank_id,
				_b.res_taxon_id,
				_d.rank,
				_c.lower_taxon,
				_c.keypath_endpoint

			from %PRE%taxa _a

			left join %PRE%choices_keysteps _b
				on _a.id = _b.res_taxon_id
				and _a.project_id = _b.project_id

			left join %PRE%projects_ranks _c
				on _a.rank_id = _c.id
				and _a.project_id = _c.project_id

			left join %PRE%ranks _d
				on _c.rank_id = _d.id

			where _a.project_id = " . $project_id . "

			order by ".($order=='rank' ? '_c.rank_id,_a.taxon' : '_a.taxon')
		);

		return $taxa;

    }

    public function getEndPointCount( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        $d=$this->freeQuery("
			select
				count(_b.id) as total
			from
				%PRE%projects_ranks _a
			left join %PRE%taxa _b
				on _a.project_id = _b.project_id
				and _a.id =  _b.rank_id
			where
				_a.project_id = " . $project_id . "
				and _a.keypath_endpoint = 1
		");

		return $d[0]['total'];
    }

    public function getAllKeyConnectedTaxa( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

		return $this->freeQuery(array(
			"query" => "
				select
					_a.id,
					_a.taxon
				from
					%PRE%taxa _a
				right join %PRE%choices_keysteps _b
					on _a.id=_b.res_taxon_id
					and _a.project_id=_b.project_id
				where
					_a.project_id = " . $project_id,
			"fieldAsIndex" => "id"
		));
	}

    public function getKeystepChoices( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$keystep_id=isset($params['keystep_id']) ? $params['keystep_id'] : null;
		$is_start=isset($params['is_start']) ? $params['is_start'] : false;

		if ( is_null($project_id) || ( is_null($keystep_id) && is_null($is_start) )  )
			return;

        return $this->freeQuery("
			select
				_b.id as choice_id,
				_b.keystep_id,
				_b.res_keystep_id,
				_b.res_taxon_id
			from
				%PRE%choices_keysteps _b

			left join %PRE%keysteps _a

				on _a.project_id=_b.project_id
				and _a.id=_b.keystep_id

			where
				_a.project_id = " . $project_id . "
			" . ( $is_start ? "and _a.is_start = 1" : "" ) . "
			" . ( !is_null($keystep_id) ? "and _b.keystep_id = " . $keystep_id : "" ) . "
		");

	}

    public function getSteplessChoices( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        return $this->freeQuery("
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%keysteps _b
				on _a.keystep_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = " . $project_id . "
			and _b.id is null
		");
	}

    public function getEmptyChoices( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        return $this->freeQuery("
			select _a.*,_b.choice_txt
			from %PRE%choices_keysteps _a
			left join %PRE%choices_content_keysteps _b
				on _a.id = _b.choice_id
				and _a.project_id = _b.project_id
			where _a.project_id = " . $project_id . "
				and _a.choice_img is null
				and _a.res_keystep_id is null
				and _a.res_taxon_id is null
				and _b.choice_txt is null
			and _b.id is null
		");
	}

    public function getNonExistantKeyTargets( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        return $this->freeQuery("
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%keysteps _b
				on _a.res_keystep_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = " . $project_id . "
			and _b.id is null
		");
	}

    public function getNonExistantKeyTaxa( $params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

        return $this->freeQuery("
			select _a.*
			from %PRE%choices_keysteps _a
			left join %PRE%taxa _b
				on _a.res_taxon_id = _b.id
				and _a.project_id = _b.project_id
			where _a.project_id = " . $project_id . "
			and _b.id is null
		");
	}

    public function getDeadEndChoicesKeysteps ($params )
    {
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$language_id=isset($params['language_id']) ? $params['language_id'] : null;

		if ( is_null($project_id) || is_null($language_id) )
			return;

        return $this->freeQuery("
			SELECT
				_a.*,
				_b.title,
				_c.number,
				_d.choice_txt

			from %PRE%choices_keysteps _a

			left join %PRE%content_keysteps _b
				on _b.keystep_id = _a.keystep_id
				and _b.language_id = ". $language_id . "
				and _a.project_id = _b.project_id

			left join %PRE%keysteps _c
				on _c.id = _a.keystep_id
				and _a.project_id = _c.project_id

			left join %PRE%choices_content_keysteps _d
				on _a.id = _d.choice_id
				and _a.project_id = _d.project_id

			where _a.project_id = " . $project_id ."
				and (_a.res_keystep_id = -1 or _a.res_keystep_id is null) and _a.res_taxon_id is null

			order by
				_a.keystep_id, _a.id
			");

    }

    public function getInternalLinksKeysteps ($params)
    {
		$projectId = isset($params['projectId']) ? $params['projectId'] : null;
		$languageId = isset($params['languageId']) ? $params['languageId'] : null;

		if (is_null($projectId) || is_null($languageId))
			return;

		return $this->freeQuery("
			SELECT _a.*, _b.title, _c.number, _d.choice_txt
				from %PRE%choices_keysteps _a
				left join %PRE%content_keysteps _b
					on _b.keystep_id = _a.keystep_id
					and _b.language_id = ".$languageId."
					and _a.project_id = _b.project_id
				left join %PRE%keysteps _c
					on _c.id = _a.keystep_id
					and _a.project_id = _c.project_id
				left join %PRE%choices_content_keysteps _d
					on _a.id = _d.choice_id
					and _a.project_id = _d.project_id
				where _a.project_id = " . $projectId ."
				order by _a.keystep_id, title
			");

    }

}
