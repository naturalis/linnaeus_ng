<?php

/// freemoduleid???

class UserRights
{
	/*
		class cannot extend Controller class, as it needs to be instantiated in
		Controller itself. therefore, in order to allow it to perform database
		queries, an instantiated model from another class is required; hence
		setModel();

		for pages that allow for the absence of a project ID, call
			$this->UserRights->setAllowNoProjectId( true );
		before
			$this->checkAuthorisation();
	*/

	// available constants
	// ID_ROLE_SYS_ADMIN
	// ID_ROLE_LEAD_EXPERT


	private $model;
	private $userid;
	private $user;
	private $projectid;
	private $controller;
	private $moduleid;
	private $allowNoProjectId=false;
	
	private $authorizestate;
	private $message;

    public function __construct( $p )
    {
		$this->setAuthorizeState( false );
		$this->setModel( isset( $p['model'] ) ? $p['model'] : null );
		$this->setUserId( isset( $p['userid'] ) ? $p['userid'] : null );
		$this->setProjectId( isset( $p['projectid'] ) ? $p['projectid'] : null );
		$this->setController( isset( $p['controller'] ) ? $p['controller'] : null );
		$this->setUser();
		$this->setModuleId();
    }

    public function isAuthorized()
    {
		
		return true;
		
		$p=isset( $this->projectid );
		$u=isset( $this->userid );
		$c=isset( $this->controller );
		$m=isset( $this->moduleid );

		if ( !$u ) 
		{
			$this->setMessage( '9: user not logged in' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $u && $this->isSysAdmin() ) 
		{
			$this->setMessage( '1: user logged in, is sysadmin (full access)' );
			$this->setAuthorizeState( true );
		}
		else
		if ( $u && $this->getAllowNoProjectId() )
		{
			$this->setMessage( '8: user logged in, page allows absence of project ID' );
			$this->setAuthorizeState( true );
		}
		else
		if ( !$p ) 
		{
			$this->setMessage( '2: no project selected (no project ID set)' );
			$this->setAuthorizeState( false );
		} 
		else
		if ( $p && !$u ) 
		{
			$this->setMessage( '3: attempting to access a project page without being logged in (project ID set, user ID not set)' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u  && !$c && !$m )
		{
			$this->setMessage( '4: accessing non-module page (project ID set, user ID set, no controller & module ID set)' );
			$this->setAuthorizeState( true );
		}
		else
		if ( $p && $u  && $c && !$m )
		{
			$this->setMessage( '5: attempting access to uknown module (project ID set, user ID set, controller set, module ID not set)' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u && $c && $m && !$this->canUserAccessModule() )
		{
			$this->setMessage( '6: attempting access to module without proper rights (project ID set, user ID set, module ID set, no rights or can_read is false)' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u && $c && $m && $this->canUserAccessModule() )
		{
			$this->setMessage( '7: accessing module (project ID set, user ID set, module ID set, can_read is true)' );
			$this->setAuthorizeState( true );
		}

		return $this->getAuthorizeState();
    }

	public function setAllowNoProjectId( $state )
	{
		if ( is_bool($state) )
		{
			$this->allowNoProjectId=$state;
		}
	}

	public function isSysAdmin()
	{
		return isset($this->user['role_id']) && $this->user['role_id']==ID_ROLE_SYS_ADMIN ? true : false;
	}

    public function getMessage()
	{
		return $this->message;
	}








	


	private function getUserModuleStatus()
	{
		$d=$this->model->freeQuery( "
			select
				* 
			from
				%PRE%user_module_access 
			where
				project_id = " . $this->projectid ." 
				and user_id = " . $this->userid ." 
				and module_id = " . $this->moduleid ." 
				and module_type ='standard'
		");

		return $d ? $d[0] : null;
	}
	
	private function canUserAccessModule()
	{
		$d=$this->getUserModuleStatus();
		return isset($d['can_read']) && $d['can_read']==1 ? true : false;
	}

    private function setAuthorizeState( $state )
	{
		$this->authorizestate=$state;
	}

    private function getAuthorizeState()
	{
		return $this->authorizestate;
	}

    private function setModel( $model )
    {
		$this->model=$model;
    }

    private function setUserId( $userid )
    {
		$this->userid=$userid;
    }

    private function setProjectId( $projectid )
    {
		$this->projectid=$projectid;
    }

    private function setController( $controller )
    {
		$this->controller=$controller;
    }

    private function setModuleId()
    {
		if ( empty($this->controller) ) return;
		
		$d=$this->model->freeQuery( "
			select
				* 
			from
				%PRE%modules 
			where
				controller = '" . mysqli_real_escape_string($this->model->databaseConnection, $this->controller) ."'
		");
		
		if ( $d ) 
		{
			$this->moduleid=$d[0]['id'];
		}
    }

    private function setUser()
    {
		$d=$this->model->freeQuery( "
			select
				_a.username,
				_a.first_name,
				_a.last_name,
				_a.email_address,
				_a.active,
				_a.last_login,
				_a.logins,
				_a.last_password_change,
				_b.role_id,
				_c.role
			from
				%PRE%users _a
				
			left join
				%PRE%projects_roles_users _b
				on _a.id=_b.user_id
				
			left join
				%PRE%roles _c
				on _b.role_id=_c.id
				
			where
				_a.id = " . $this->userid
		);

		$this->user = $d ? $d[0] : null;
    }

    private function setMessage( $message )
	{
		$this->message=$message;
	}

	private function getAllowNoProjectId()
	{
		return $this->allowNoProjectId;
	}








		
/*

Maarten:
Logic:

user logged in? y → user_id
select a project (project_id, user_id) → project_id, role_id
available modules per project (project_id, module_id) → (print list)
access module (project_id, module_id, user_id) → ControllerBaseName

within a module:

access item (action R) (project_id, module_id, user_id) → (display item)
access item (action CUD) (project_id, module_id, user_id, item_id) → (add, alter, remove item; R implicit)

function in pseudo code:

public boolean function isAuthorized( project_id, user_id, role_id, module_id, item_id, action )
{
    if ( !project_id ) return true;
    if ( project_id && ( !user_id || !isLoggedIn(user_id) ) ) return false;
    if ( !module_id ) return true;
    if ( module_id && !hasModule(project_id,module_id) ) return false;

    if ( canAccessAllModules(role_id) ) return true;
    if ( !canAccessModule(project_id,module_id,user_id) ) return false;
    if ( !canUserPerformAction(project_id, module_id, user_id, item_id, item_id) )
return false;

return true;
}


*/

    public function reInitializeRights()
    {
		die('UserRights::reInitializeRights');

        $cur = $this->getUserRights(isset($userId) ? $userId : $this->getCurrentUserId());
        $this->setUserSessionRights($cur['rights']);
        $this->setUserSessionRoles($cur['roles']);
        $this->setUserSessionNumberOfProjects($cur['number_of_projects']);
    }

    public function setUserRoleId()
    {
		die('UserRights::setUserRoleId');
	
		
        if (!isset($_SESSION['admin']['user']['_roles']))
		{
            $_SESSION['admin']['user']['currentRole'] = null;
        }
        else
		{
            $d = $this->getCurrentProjectId();

            if (is_null($d))
			{
                $_SESSION['admin']['user']['currentRole'] = null;
            }
            else
			{
                foreach ((array) $_SESSION['admin']['user']['_roles'] as $val)
				{
                    if ($val['project_id'] == $d)
                        $_SESSION['admin']['user']['currentRole'] = $val["role_id"];
                }
            }
        }
    }

}