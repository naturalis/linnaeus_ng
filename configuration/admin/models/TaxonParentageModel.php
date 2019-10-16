<?php
include_once (__DIR__ . "/AbstractModel.php");

final class TaxonParentageModel extends AbstractModel
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

	public function getTreeTop( $params )
	{
		$project_id = isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

		$baseQuery="
			select
				_a.id,
				_a.taxon,
				_r.rank
			from
				%PRE%taxa _a

			left join %PRE%trash_can _trash
				on _a.project_id = _trash.project_id
				and _a.id = _trash.lng_id
				and _trash.item_type='taxon'
					
			left join %PRE%projects_ranks _p
				on _a.project_id=_p.project_id
				and _a.rank_id=_p.id

			left join %PRE%ranks _r
				on _p.rank_id=_r.id

			where 
				_a.project_id = ".$project_id." 
				and ifnull(_trash.is_deleted,0)=0
				and _a.parent_id is null
			";

		// Original query
		$r = $this->freeQuery($baseQuery . " and _r.id < 10");

		// but the tree top isn't necessarily always among the first 10 rows...
		if (empty($r)) {
            $r = $this->freeQuery($baseQuery);
        }

        return $r;
	}
	
}
