<?php

include_once ('Controller.php');

class UserRights extends Controller
{

	private $_userid;

    public function setUserId( $userid )
    {
		$this->_userid=$userid;
    }

    public function getUserId()
    {
		return $this->_userid;
    }


	public function isAuthorized()
	{
		$proj=$this->getCurrentProjectId();
		$user=$this->getCurrentUserId();

		$role
		$modu
		$item
		$acti
		
		if ( !project_id ) return true;
		if ( project_id && ( !user_id || !isLoggedIn(user_id) ) ) return false;
		if ( !module_id ) return true;
		if ( module_id && !hasModule(project_id,module_id) ) return false;
	
		if ( canAccessAllModules(role_id) ) return true;
		if ( !canAccessModule(project_id,module_id,user_id) ) return false;
		if ( !canUserPerformAction(project_id, module_id, user_id, item_id, item_id) ) return false;

		return true;
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



}