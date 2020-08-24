<?php
/** @noinspection PhpMissingParentCallMagicInspection */
include_once (__DIR__ . "/AbstractModel.php");

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
		$project_id=isset($params['project_id']) ? $params['project_id'] : null;

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
					and _b.user_id = " . $user_id  ."
				where 1=1 ";
		}
		else
		{
			$query .= "
				right join %PRE%projects_roles_users _b
					on _a.id=_b.project_id
				where _b.user_id = " . $user_id . "
			";
		}

		if ( isset($project_id) )
		{
			$query .= "
				and _a.id = " . $project_id ;
		}

		$query .= "
			order by title, sys_name
		";

		return $this->freeQuery( $query );
	}

	public function getProjectModules( $params )
	{
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $include_hidden = isset($params['include_hidden']) ? $params['include_hidden'] : false;

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
			" . (!$include_hidden ? "and _b.show_in_menu=1 " : "") . "
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

    public function getProjectsWithUsers ()
    {
        // @todo: temporarily changed the t3.last_login to last_change,
        // because last_login was no longer saved correctly in many linnaeus
        // installations, this needs to be corrected again

        $query = '
            select
                t1.sys_name as project,
                t1.published as project_is_published,
                t3.username as user_name,
                t3.first_name,
                t3.last_name,
                t4.role as role,
                t3.email_address,
                t3.active as user_is_active,
                t3.last_change,
                t2.last_project_select as project_last_selected,
                t3.last_password_change as password_last_changed
            from
                %PRE%projects as t1
            left join
                %PRE%projects_roles_users as t2 on t1.id = t2.project_id
            left join
                %PRE%users as t3 on t2.user_id = t3.id
            left join
                %PRE%roles as t4 on t2.role_id = t4.id
            order by
                t1.sys_name, t3.username';

        return $this->freeQuery($query);
    }

    public function getProjectManagementModules ($p)
    {
        $projectId = isset($p['project_id']) ? $p['project_id'] : null;
        $showHidden = isset($p['show_hidden']) ? $p['show_hidden'] : false;

        $q = "
            select *
            from %PRE%modules
            where " . (!$showHidden ? '(show_in_menu = 1 or show_in_public_menu = 1) and ' : '') .
            "controller not in ('users', 'projects')
            order by show_order";

        return $this->freeQuery($q);
    }
}
