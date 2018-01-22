<?php
include_once (__DIR__ . "/AbstractModel.php");

final class VersatileExportModel extends AbstractModel
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

	public function getPresenceStatuses( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_b.index_label,
				_b.label,
				_a.established
			from
				%PRE%presence _a
			left join %PRE%presence_labels _b
				on _a.project_id=_b.project_id
				and _a.id=_b.presence_id
				and _b.language_id=".LANGUAGE_ID_DUTCH."
			where
				_a.project_id = ". $project_id ." 
					and _b.index_label!=''
			order by
				_b.index_label
			";
			
		return $this->freeQuery( $query );

	}

	public function getRanks( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
		{
			return;
		}

		$query="
			select
				_a.*,
				ifnull(_c.label,_a.rank) as label
			from
				%PRE%projects_ranks _b
				
			right join %PRE%ranks _a
				on _a.id=_b.rank_id
				
			left join %PRE%labels_projects_ranks _c
				on _b.project_id=_c.project_id 
				and _b.id=_c.project_rank_id
				and _c.language_id = " . LANGUAGE_ID_DUTCH . "

			where 
				_b.project_id = ". $project_id ." 

			order by
				_a.id
			";

		return $this->freeQuery( array("query"=>$query,"fieldAsIndex"=>"id" ));
	}

	public function doMainQuery( $params )
	{
		$query=isset($params['query']) ? $params['query'] : null;
		return $this->freeQuery( $query );
	}

	public function findAncestor( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$taxon_id=isset($params['taxon_id']) ? $params['taxon_id'] : null;

		if( !isset( $project_id ) || !isset( $taxon_id ) )
		{
			return;
		}

		$query="
			select
				_t.taxon,_t.id, _t.parent_id, _f.rank_id
			from
				taxa  _t
			left join projects_ranks _f
				on _t.rank_id=_f.id
				and _t.project_id=_f.project_id
			where 
				_t.project_id=".$project_id."
				and _t.id = ".$taxon_id
		;

		$d=$this->freeQuery( $query );

		return isset($d[0] ) ? $d[0] : null;
	}

	public function doSynonymsQuery( $params )
	{
		$query=isset($params['query']) ? $params['query'] : null;
		return $this->freeQuery( $query );
	}



























}
