<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

class MatrixKeyModel extends AbstractModel
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

	public function getCharactersNotInGroups( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;
		$matrix_id=isset($params['matrix_id']) ? $params['matrix_id'] : null;

		if ( is_null($project_id) || is_null($matrix_id) )
			return;

		$query="
			select
				_a.characteristic_id, _a.show_order, _c.id as characteristic_chargroup_id 

			from %PRE%characteristics_matrices _a

			left join %PRE%characteristics_chargroups _c
				on _c.characteristic_id = _a.characteristic_id
				and _c.project_id = _a.project_id

			where
				_a.matrix_id =" . $matrix_id . "
				and _a.project_id =" . $project_id . "

			and _c.id is null
		";
		
		return $this->freeQuery(array('query'=>$query,'fieldAsIndex' => 'characteristic_id'));
	}

	public function deleteObsoleteMatrices( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

		$query='delete from %PRE%matrices
			where project_id =  ' . $project_id . '
			and got_names = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)';
		
		return $this->freeQuery($query);
	}

	public function deleteObsoleteCharacters( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if ( is_null($project_id) )
			return;

		$query='delete from %PRE%characteristics
			where project_id =  ' . $project_id . '
			and got_labels = 0
			and created < DATE_ADD(now(), INTERVAL -7 DAY)';
		
		return $this->freeQuery($query);
	}

}
