<?php
include_once (dirname(__FILE__) . "/AbstractModel.php");

final class ProjectsModel extends AbstractModel
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

	public function getUserProjects( $params )
	{
		$user_id=isset($params['user_id']) ? $params['user_id'] : null;
		$show_all=isset($params['show_all']) ? $params['show_all'] : false;

		if( !isset( $user_id ) ) return;
		
		$query="
				select
					_a.id,
					_a.sys_name,
					_a.sys_description,
					_a.title,
					_a.published,
					_b.role_id as user_project_role_id,
					_b.active as user_project_active

				from
					%PRE%projects _a
				";
				
		if ( $show_all ) 
		{
			$query .= "
				left join %PRE%projects_roles_users _b
					on _a.id=_b.project_id
					and _b.user_id = " . $user_id ;
		}
		else
		{
			$query .= "
				right join %PRE%projects_roles_users _b
					on _a.id=_b.project_id
				where _b.user_id = " . $user_id . "
			";
		}

		$query .= "
			order by title, sys_name
		";

		return $this->freeQuery( $query );
	}
	
	public function getProjectModules( $params )
	{
        $project_id = isset($params['project_id']) ? $params['project_id'] :  null;

        if ( is_null($project_id) )
			return;
		
		$query = "
			select
				_a.module_id,
				_b.module,
				_b.description,
				_b.controller,
				_b.show_in_menu,
				_a.active
			from
				%PRE%modules_projects _a
				
			left join %PRE%modules _b
				on _a.module_id = _b.id
			
			where 
				_a.project_id = " . $project_id ." 
			order by _b.module asc
			";

		$modules=$this->freeQuery( $query );

		$query = "
			select
				*
			from
				%PRE%free_modules_projects
			where
				project_id = " . $project_id ." 
			";

		$custom=$this->freeQuery( $query );

		return array( 'modules' => $modules, 'custom' => $custom );

	}


}
