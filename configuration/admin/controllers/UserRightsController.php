<?php

/*

	[ initialization ]
	UserRights-object is initiated in:
		Controller::initUserRights()
	as parameters, it takes
		getCurrentUserId()
		getCurrentProjectId()
		getControllerBaseName()
	and an arbitrary Table-model for queries (the UserRights-class cannot
	extend Controller class, as it needs to be instantiated in Controller
	itself. therefore, in order to allow it to perform database queries,
	some instantiated Table-model is required; hence setModel()).


	[ implementation in Controller ]
	Controller::checkAuthorisation( ) 	--> redirects when fail
	Controller::getAuthorisationState()	--> returns boolean
	Controller::getCRUDstates()			--> returns array of booleans: [can_create,can_read,can_update,can_delete]
	checkAuthorisation() & getAuthorisationState() perform several tests:
		$this->UserRights->canAccessModule()		-> checks access to a specific module
		$this->UserRights->canManageItem()			-> checks specific assigned taxa
		$this->UserRights->canPerformAction()		-> checks specific actions (CRUD, publish)
		$this->UserRights->hasAppropriateLevel()	-> check based on role only
	- access and read/write-rights for individual modules, as well as
	  general right to publish set in users/edit.php.
	- access to items is restricted to access to specific taxa and
	  subjacent taxa (i.e., branches), also set in users/edit.php (no
	  specified items for a user means access to all taxa).
	- for CRUD actions, see below (specific action types).


	[ "in situ" configuration options ]
	to be used anywhere (set before call to checkAuthorisation() or
	getAuthorisationState())
	- set a specific action type (checked in canPerformAction()):
		$this->UserRights->setActionType( $this->UserRights->getActionRead() );
	  there are five action types:
		$this->UserRights->getActionRead()
		$this->UserRights->getActionCreate()
		$this->UserRights->getActionUpdate()
		$this->UserRights->getActionDelete()
		$this->UserRights->getActionPublish()
	  however, currently there are only two assignable action-rights
	  per module, 'read' and 'write'. getActionRead() resolves to 'read',
	  ...Create, ...Update and ...Delete to 'write'.
	  the right to publish is set for the entire project, not per module
	  (even if it is stored for each module, and only implemented in the
	  SpeciesModule for the publishing of "taxon passport pages").
	  please note that CUD-rights are sometimes implemented in a non-literal
	  sense: for instance, deletion of an image that belongs with a
	  glossary-item is interpreted as an update action of that glossary-item.
	  default getActionRead()
	- set a specific item/taxon (checked in canManageItem()):
		$this->UserRights->setItemId( $this->rGetId() );
	  default null.
	- set a level (checked in hasAppropriateLevel()):
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
	  default ID_ROLE_EDITOR (other: ID_ROLE_SYS_ADMIN, ID_ROLE_LEAD_EXPERT)
	- disable check for module access:
		$this->UserRights->setDisableUserAccesModuleCheck( true );
	- for pages that allow for the absence of a project ID, call
		$this->UserRights->setAllowNoProjectId( true );
	- for rare cases of non-module pages, checks can be run withou a module,
	   by setting:
		$this->UserRights->setNoModule( true );
	  this disables all module-related checks (including canRead, canWrite
	  etc.), effectively reducing checks to being logged in and being
	  assigned to a project.

*/

class UserRights
{
	private $model;
	private $userid;
	private $user;
	private $projectid;
	private $controller;
	private $moduleid;
	private $moduletype;
	private $nomodule=false;
	private $usermoduleaccess;
	private $itemid;
	private $itemtype;
	private $action;
	private $requiredlevel;
	private $allowNoProjectId=false;
	private $disableUserAccesModuleCheck=false;
	private $checkOnlyIfLoggedIn=false;
	private $authorizestate;
	private $manageitemstate;
	private $actionstate;
	private $levelstate;
	private $status;
	private $useritems;
	private $subjacentitems;

	protected $C_moduleTypes=array( 'standard','custom' );
	protected $C_itemTypes=array( 'taxon' );
	protected $C_actions=array( 'create','read','update','delete','publish' );
	protected $C_levels=array( ID_ROLE_SYS_ADMIN,ID_ROLE_LEAD_EXPERT,ID_ROLE_EDITOR );


    public function __construct( $p )
    {
		$this->setAuthorizeState( false );
		$this->setModel( isset( $p['model'] ) ? $p['model'] : null );
		$this->setUserId( isset( $p['userid'] ) ? $p['userid'] : null );
		$this->setProjectId( isset( $p['projectid'] ) ? $p['projectid'] : null );
		$this->setController( isset( $p['controller'] ) ? $p['controller'] : null );
		$this->setModuleType( $this->getModuleTypeStandard() );
		$this->setItemType( $this->getItemTypeTaxon() );
		$this->setActionType( $this->getActionRead() );
		$this->setRequiredLevel( ID_ROLE_EDITOR );
		$this->setUser();
		$this->setModuleId();
		$this->setUserModuleAccess();
		$this->setNoModule( false );
    }

    public function canAccessModule()
    {
		$p=isset( $this->projectid );
		$u=isset( $this->userid );
		$c=isset( $this->controller );
		$m=isset( $this->moduleid );
		
		$this->setStatus( 'no access: unspecified error' );
		
		if ( !$u )
		{
			$this->setStatus( 'no access: user not logged in' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $u && $this->isSysAdmin() )
		{
			$this->setStatus( 'access: user logged in, is sysadmin' );
			$this->setAuthorizeState( true );
		}
		else
		if ( $u && $this->getAllowNoProjectId() )
		{
			$this->setStatus( 'access: user logged in, page allows absence of project ID' );
			$this->setAuthorizeState( true );
		}
		else
		if ( !$p )
		{
			$this->setStatus( 'no access: no project selected' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && !$u )
		{
			$this->setStatus( 'no access: attempting to access a project page without being logged in' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u  && !$c && !$m && !$this->getNoModule() )
		{
			$this->setStatus( 'access: accessing non-module page' );
			$this->setAuthorizeState( true );
		}
		else
		if ( $p && $u && $c && !$m && !$this->getNoModule() && !$this->getDisableUserAccesModuleCheck() )
		{
			$this->setStatus( 'no access: attempting access to unknown module' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u && $c && !$m && $this->getNoModule() )
		{
			$this->setStatus( 'access: accessing a non-module page' );
			$this->setAuthorizeState( true );
		}
		else
		if ( $p && $u && $c && $m && !$this->getDisableUserAccesModuleCheck() && !$this->canUserAccessModule() )
		{
			$this->setStatus( 'no access: attempting access to module without proper rights' );
			$this->setAuthorizeState( false );
		}
		else
		if ( $p && $u && $c && $m && ( $this->getDisableUserAccesModuleCheck() || $this->canUserAccessModule() ) )
		{
			$this->setStatus( 'access: accessing module' );
			$this->setAuthorizeState( true );
		}

		//echo "getAuthorizeState(): " . ( $this->getAuthorizeState() ? "y" : "n" ) . " (" . $this->getStatus() . ")"; die();

		return $this->getAuthorizeState();
    }

    public function canManageItem()
    {
		if( is_null( $this->itemid ) || $this->isSysAdmin() )
		{
			$this->setManageItemState( true );
			$this->setStatus( 'access: no item specified' );
		}
		else
		{
			$this->setUserItems( true );

			if ( count((array)$this->useritems)==0 )
			{
				$this->setManageItemState( true );
				$this->setStatus( 'access: item specified, no user items defined' );
			}
			else
			{
				$this->setUserSubjacentItems();
				$d=$this->isCurrentItemInUserOrSubjacentItems();
				$this->setManageItemState( $d );
				$this->setStatus( sprintf('%s to item for user', ( $d ? 'access' : 'no access' ) ) );
			}
		}

		return $this->getManageItemState();
    }

    public function canPerformAction()
    {
		if( is_null( $this->action ) || $this->isSysAdmin() )
		{
			$this->setActionState( true );
			$this->setStatus( 'access: no action specified' );
		}
		elseif ( isset( $this->moduleid ) && $this->getDisableUserAccesModuleCheck() )
		{
			$this->setActionState( true );
			$this->setStatus( 'access: module check disabled' );
		}
		else
		{
			$d=$this->canUserPerformAction();
			$this->setActionState( $d );
			$this->setStatus( sprintf("action '%s' %s for user", $this->action , ( $d ? 'allowed' : 'not allowed' ) ) );
		}

		return $this->getActionState();
    }

    public function hasAppropriateLevel()
    {
		if( is_null( $this->requiredlevel ) )
		{
			$this->setLevelState( true );
			$this->setStatus( 'access: no minimum required level specified' );
		}
		else
		{
			$d=$this->doesUserHaveAppropriateLevel();
			$this->setLevelState( $d );
			$this->setStatus( sprintf("minmum required role %s", $this->translateRole( $this->requiredlevel ) ) );
		}

		return $this->getLevelState();
    }


    public function setUserItems()
	{
		$this->useritems=array();

		$d=$this->model->freeQuery( "
			select
				item_id
			from
				%PRE%user_item_access
			where
				project_id = " . $this->projectid . "
				and user_id = " . $this->userid . "
				and item_type ='" . $this->itemtype . "'
		");

		foreach((array)$d as $val)
		{
			array_push( $this->useritems,$val['item_id'] );
		}
	}

	public function getUserItems()
	{
		return $this->useritems;
	}

	public function getUserRoleId()
	{
	    return $this->user['role_id'];
	}

	public function setAllowNoProjectId( $state )
	{
		if ( is_bool($state) )
		{
			$this->allowNoProjectId=$state;
		}
	}

	public function setDisableUserAccesModuleCheck( $state )
	{
		if ( is_bool($state) )
		{
			$this->disableUserAccesModuleCheck=$state;
		}
	}

	public function setCheckOnlyIfLoggedIn( $state )
	{
		if ( is_bool($state) )
		{
			$this->checkOnlyIfLoggedIn=$state;
		}
	}

	public function getCheckOnlyIfLoggedIn()
	{
		return $this->checkOnlyIfLoggedIn;
	}


	public function setModuleType( $type )
	{
		if ( in_array($type,$this->C_moduleTypes) )
		{
			$this->moduletype=$type;
		}
	}

	public function setItemId( $id )
	{
		/*
			be aware: currently we recognize only a single legal itemType ('taxon') so for 
			convenience sake, the item type is set automatically at initialization. should
			there ever be other item types, either all existing calls to setItemId() should
			be preceded by a call to setItemType('taxon'), or 'taxon' should  be made the
			default value of item type.
		*/
		$this->itemid=$id;
	}

	public function setItemType( $type )
	{
		if ( in_array( $type, $this->C_itemTypes ) )
		{
			$this->itemtype=$type;
		}
	}

	public function isSysAdmin()
	{
		return isset($this->user['role_id']) && $this->user['role_id']==ID_ROLE_SYS_ADMIN ? true : false;
	}

    public function getStatus()
	{
		return $this->status;
	}

	public function getModuleTypeStandard()
	{
		return $this->C_moduleTypes[array_search('standard',$this->C_moduleTypes)];
	}

	public function getModuleTypeCustom()
	{
		return $this->C_moduleTypes[array_search('custom',$this->C_moduleTypes)];
	}

	public function getItemTypeTaxon()
	{
		return $this->C_itemTypes[array_search('taxon',$this->C_itemTypes)];
	}

	public function getActionCreate()
	{
		return $this->C_actions[array_search('create',$this->C_actions)];
	}

	public function getActionRead()
	{
		return $this->C_actions[array_search('read',$this->C_actions)];
	}

	public function getActionUpdate()
	{
		return $this->C_actions[array_search('update',$this->C_actions)];
	}

	public function getActionDelete()
	{
		return $this->C_actions[array_search('delete',$this->C_actions)];
	}

	public function getActionPublish()
	{
		return $this->C_actions[array_search('publish',$this->C_actions)];
	}

	public function setActionType( $action )
	{
		if ( in_array( $action, $this->C_actions ) )
		{
			$this->action=$action;
		}
	}

	public function setRequiredLevel( $level )
	{
		if ( in_array( $level, $this->C_levels ) )
		{
			$this->requiredlevel=$level;
		}
	}

	public function setNoModule( $state )
	{
		if ( is_bool($state) )
		{
			$this->nomodule=$state;
		}
	}

	public function getNoModule()
	{
		return $this->nomodule;
	}

    private function setAuthorizeState( $state )
	{
		$this->authorizestate=$state;
	}

    private function getAuthorizeState()
	{
		return $this->authorizestate;
	}

    private function setManageItemState( $state )
	{
		$this->manageitemstate=$state;
	}

    private function getManageItemState()
	{
		return $this->manageitemstate;
	}

	private function setActionState( $state )
	{
		$this->actionstate=$state;
	}

	private function getActionState()
	{
		return $this->actionstate;
	}

	private function setLevelState( $state )
	{
		$this->levelstate=$state;
	}

	private function getLevelState()
	{
		return $this->levelstate;
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

    private function setStatus( $status )
	{
		$this->status=$status;
	}

	private function getAllowNoProjectId()
	{
		return $this->allowNoProjectId;
	}

	private function getDisableUserAccesModuleCheck()
	{
		return $this->disableUserAccesModuleCheck;
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

	private function setUserModuleAccess()
	{
		if ( is_null($this->moduleid) || is_null($this->moduletype) )
			return;

		$d=$this->model->freeQuery( "
			select
				can_read,
				can_write,
				can_publish
			from
				%PRE%user_module_access
			where
				project_id = " . $this->projectid . "
				and user_id = " . $this->userid . "
				and module_id = " . $this->moduleid . "
				and module_type ='" . $this->moduletype . "'
		");

		if ( $d )
		{
			$this->usermoduleaccess=$d[0];
		}
	}

	private function canUserAccessModule()
	{
		return isset($this->usermoduleaccess['can_read']) && $this->usermoduleaccess['can_read']==1 ? true : false;
	}

    private function setUserSubjacentItems()
	{
		$this->subjacentitems=array();

		foreach((array)$this->useritems as $item)
		{
			$d=$this->model->freeQuery( "
				select
					taxon_id
				from
					%PRE%taxon_quick_parentage _sq
				where
					_sq.project_id = " . $this->projectid . "
					and MATCH(_sq.parentage) AGAINST ('" . $item . "' in boolean mode)
			");

			foreach((array)$d as $val)
			{
				array_push( $this->subjacentitems, $val['taxon_id'] );
			}
		}
	}

	private function isCurrentItemInUserOrSubjacentItems()
	{
		return in_array( $this->itemid, (array)$this->useritems ) || in_array( $this->itemid, (array)$this->subjacentitems );
	}

	private function canUserPerformAction()
	{
		if ( $this->getNoModule() ) return true;
		if ( $this->action==$this->getActionCreate() && $this->usermoduleaccess['can_write']==1 ) return true;
		if ( $this->action==$this->getActionRead() && $this->usermoduleaccess['can_read']==1 ) return true;
		if ( $this->action==$this->getActionUpdate() && $this->usermoduleaccess['can_write']==1 ) return true;
		if ( $this->action==$this->getActionDelete() && $this->usermoduleaccess['can_write']==1 ) return true;
		if ( $this->action==$this->getActionPublish() && $this->usermoduleaccess['can_publish']==1 ) return true;
		return false;
	}

	private function doesUserHaveAppropriateLevel()
	{
		if ( isset($this->user['role_id']) )
		{
			return (int)$this->user['role_id'] <= $this->requiredlevel;
		}
		return false;
	}

	private function translateRole( $role_id )
	{
		switch( $role_id )
		{
			case ID_ROLE_SYS_ADMIN:
				return 'system administrator';
				break;
			case ID_ROLE_LEAD_EXPERT:
				return 'lead expert';
				break;
			case ID_ROLE_EDITOR:
				return 'editor';
				break;
			default:
				return $role_id;
		}
	}

}
