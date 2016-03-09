<?php

class UserRights
{
	/*
		class cannot extend Controller class, as it needs to be instantiated in
		Controller itself. therefore, in order to allow it to perform database
		queries, an instantiated model from another class is required; hence
		setModel();
	*/

	private $model;
	private $userid;
	private $projectid;
	private $controller;
	private $moduleid;

    public function __construct( $p )
    {
		$this->setModel( isset( $p['model'] ) ? $p['model'] : null );

		$this->setUserId( isset( $p['userid'] ) ? $p['userid'] : null );
		$this->setProjectId( isset( $p['projectid'] ) ? $p['projectid'] : null );
		$this->setController( isset( $p['controller'] ) ? $p['controller'] : null );
		$this->setModuleId();
    }

    public function isAuthorized()
    {
		// no project id = outside -> open access
		if ( !isset( $this->projectid ) ) return true;

		// no user id = not logged in / project id set -> shouldn't happen -> unauthorized
		if ( isset( $this->projectid ) && !isset($this->userid) ) return false;

		// logged in, project selected, but not within any module yet -> authorized (shouldn't actually happen, though)
		if ( isset( $this->projectid ) && isset($this->userid)  && !isset($this->moduleid) ) return true;

		// logged in, project selected, within a module

/*
		if ( module_id && !hasModule(project_id,module_id) ) return false;
		if ( canAccessAllModules(role_id) ) return true;
		if ( !canAccessModule(project_id,module_id,user_id) ) return false;
		if ( !canUserPerformAction(project_id, module_id, user_id, item_id, item_id) ) return false;
*/
		

		return true;

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







	// OLD
    private function checkAuthorisation($allowNoProjectId = false)
	{

		
        // check if user is logged in, otherwise redirect to login page
        if ($this->isUserLoggedIn())
		{
            // check if there is an active project, otherwise redirect to choose project page
            if ($this->getCurrentProjectId() || $allowNoProjectId)
			{
                // check if the user is authorised for the combination of current page / current project
                if ($this->isUserAuthorisedForProjectPage() || $this->isCurrentUserSysAdmin())
				{
                    return true;
                }
                else
				{
                    $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['notAuthorized']);

                    /*
						user is not authorized and redirected to the index.page;
						if he already *is* on the index.page (and not authorized to be there),
						he is logged out to avoid circular reference.
					*/
                    if ($this->getViewName() == 'Index')
					{
                        $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['logout']);
                    }
                    else
					{
                        $this->redirect('index.php');
                    }
                }
            }
            else
			{
                $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['chooseProject']);
            }
        }
        else
		{
            $this->setLoginStartPage();
            $this->redirect($this->baseUrl . $this->appName . $this->generalSettings['paths']['login']);
        }
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


    /**
     * Checks whether a user is authorized to view/use a page within a project
     *
     * @return     boolean        authorized or not
     * @access     private
     */
    private function isUserAuthorisedForProjectPage ()
    {
		
        $controllerBaseName = ($this->controllerBaseNameMask ? $this->controllerBaseNameMask : $this->getControllerBaseName());

        // is no controller base name is set, we are in /admin/views/utilities/admin_index.php, which is the portal to the modules
        if ($controllerBaseName == '')
            return true;

        if (isset($_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName])) {

            $d = $_SESSION['admin']['user']['_rights'][$this->getCurrentProjectId()][$controllerBaseName];

            foreach ((array) $d as $key => $val) {

                if ($val == '*' || $val == $this->getViewName()) {

                    return true;
                }
            }
        }

        return false;
    }




    public function isSysAdmin()
	{
		return true;
		
        if (!isset($_SESSION['admin']['user']))
		{
            $u = $this->models->Users->_get(array(
                'id' => $this->getCurrentUserId()
            ));

            return $u['superuser'] == '1';
        }

        if ($_SESSION['admin']['user']['superuser'] == 1)
            return true;

        if (!isset($_SESSION['admin']['user']['_roles']))
            return false;

        foreach ((array) $_SESSION['admin']['user']['_roles'] as $key => $val)
		{
            if ($val['project_id'] == 0 && $val['role_id'] == ID_ROLE_SYS_ADMIN)
                return true;
        }

        return false;
	}

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