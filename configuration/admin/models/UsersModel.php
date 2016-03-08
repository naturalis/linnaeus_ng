<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class UsersModel extends AbstractModel
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

	public function getProjectUsers( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
			return;
		
		$query="
				select
					_a.*, datediff(curdate(),_b.created) as days_active
				from
					%PRE%users _a

				right join %PRE%projects_roles_users _b
					on _a.id=_b.user_id
				
				where
					_b.project_id = ". $project_id ."

				order by _a.last_name, _a.first_name
			";

		return $this->freeQuery( $query );
	}
	
	public function getNonProjectUsers( $params )
	{
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

		if( !isset( $project_id ) )
			return;
		
		$query="
				select
					distinct _a.*
				from
					%PRE%users _a

				left join %PRE%projects_roles_users _b
					on _a.id=_b.user_id
				
				where
					(
						_b.project_id is null OR
						_b.project_id != ". $project_id ."
					)

				order by _a.last_name, _a.first_name
			";

		return $this->freeQuery( $query );
	}
	
	public function getUserProjectRole ($params)
    {
        $project_id = isset($params['project_id']) ? $params['project_id'] :  null;
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;

        if (is_null($project_id) || is_null($user_id)) {
			return null;
		}

		$query = "
		    select
				_a.id,
				_a.role_id,
				_a.active,
				_a.last_project_select,
				_a.project_selects,
				_e.role,
				_e.description,
				_e.hidden
			from %PRE%projects_roles_users _a

			left join %PRE%roles _e
				on _a.role_id = _e.id

			where
				_a.project_id = " . $project_id."
				and _a.user_id = " . $user_id;

        $d = $this->freeQuery( $query );
		
		if ( $d ) return $d[0];
	}

}
