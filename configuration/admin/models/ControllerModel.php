<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class ControllerModel extends AbstractModel
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

    public function getProjectRanks ($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] :  null;
        $language_id = isset($language_id['language_id']) ? $language_id['language_id'] : null;
		
		if ( is_null($project_id) || is_null($language_id) )
			return null;

        $query = "select
						_p.id,
						_p.project_id,
						_p.rank_id,
						_p.parent_id,
						_p.lower_taxon,
						_p.keypath_endpoint,
						_r.rank,
						_r.abbreviation,
						_r.can_hybrid,
						_pr.id as ideal_parent_id,
						replace(ifnull(_q.label,_r.rank),'_',' ') as label
					from
						%PRE%projects_ranks _p

					left join %PRE%ranks _r
						on _p.rank_id = _r.id

					left join %PRE%projects_ranks _pr
						on _p.project_id= _pr.project_id
						and _r.ideal_parent_id = _pr.rank_id

					left join %PRE%labels_projects_ranks _q
						on _p.id=_q.project_rank_id
						and _p.project_id = _q.project_id
						and _q.language_id=" . $language_id . "

					where
						_p.project_id = " . $project_id . "
						order by _p.rank_id
					";

        return
			$this->freeQuery(array(
				"query"=>$query,
				"fieldAsIndex"=>"id"
			));
	}

}

?>