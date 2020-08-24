<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

final class ImportNBCModel extends AbstractModel
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

	public function resolveState( $params )
	{

		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id=isset($params['matrix_id']) ? $params['matrix_id'] : null;
		$state_name=isset($params['state_name']) ? $params['state_name'] : null;
		$char_name=isset($params['char_name']) ? $params['char_name'] : null;
		$group_name=isset($params['group_name']) ? $params['group_name'] : null;

		if( is_null($project_id) || is_null($matrix_id) || is_null($state_name) || is_null($char_name) ) return;

		$query= "
			select
				_a.*
			from
				%PRE%characteristics_states _a
			
			right join
				%PRE%characteristics _b
					on _a.characteristic_id = _b.id
					and _a.project_id = _b.project_id
				
			right join
				%PRE%characteristics_matrices _c
					on _b.id = _c.characteristic_id
					and _b.project_id = _b.project_id
				
			right join
				%PRE%matrices _d
					on _c.matrix_id = _d.id
					and _c.project_id = _d.project_id
				
			left join
				%PRE%characteristics_chargroups _e
					on _b.id = _e.characteristic_id
					and _b.project_id = _e.project_id
			
			left join
				%PRE%chargroups _f
					on _e.chargroup_id = _f.id
					and _e.project_id = _f.project_id
				
			where
				_a.project_id = " . $project_id . "
				and _d.id = " . $matrix_id  ."
				and lower(_a.sys_name) = '" . $this->escapeString(strtolower(trim($state_name))) . "'
				and lower(_b.sys_name) = '" . $this->escapeString(strtolower(trim($char_name))) . "'
				and (_f.label = '" . $this->escapeString($group_name) . "' or _e.chargroup_id is null)
		";

		$d=$this->freeQuery( $query );
		
		return $d ? $d[0] : null;
	
	}

}
