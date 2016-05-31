<?php

/*

	fails:
	http://localhost/linnaeus_ng/admin/views/utilities/admin_index.php
	WHY!?

	logging!
	
	webservice
	user in multiple projects

	isCurrentUserSysAdmin!!!
	look project-wide for REDESIGN RIGHTS
	
	removeUserFromCurrentProject
		needs deletiog of referenced rights etc

*/

include_once ('Controller.php');

class UsersController extends Controller
{
	private $_expertroleid;
	private $_allprojectusers;
	private $_userid;
	private $_user;
	private $_newuserdata;
	private $_newuserdatasave=false;
	private $_newuserpasswordsave=false;
	private $_newuserrolesave=false;

    public $usedModels = array( 
		'user_module_access',
		'user_item_access'
	);

    public $usedHelpers = array(
        'password_encoder',
    );

	public $cssToLoad = array(
		'nsr_taxon_beheer.css'
	);

    public $jsToLoad = array(
        'all' => array(
			'user.js',
            'lookup.js',
			'nsr_taxon_beheer.js'
        )
    );

    public $controllerPublicName='User administration';
	
	private $checksUsername=array('min' => 4,'max' => 32); // regExp => 
	private $checksPassword=array('min' => 8,'max' => 32); // regExp => 
	private $checksName=array('min' => 1,'max' => 64);
	private $_userBefore;

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	private function initialize()
	{
		$this->setExpertRoleId();

		if ( empty( $this->getExpertRoleId() ) )
		{
			$this->addWarning( $this->translate('No ID for the editor-role defined.') );
		}
	}

    public function indexAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Project collaborators'));
		$this->setAllProjectUsers();
        $this->smarty->assign('users',$this->getAllProjectUsers());
        $this->smarty->assign('non_users',$this->models->UsersModel->getNonProjectUsers(array('project_id'=>$this->getCurrentProjectId())));
        $this->printPage();
    }

    public function viewAction()
    {
        $this->checkAuthorisation();
		
		if (!$this->rHasId()) $this->redirect( 'index.php' );

        $this->setPageName($this->translate('Project collaborator data'));
		$this->setUserId( $this->rGetId() );
		$this->setUser();
		$this->smarty->assign('user',$this->getUser());
		$this->printPage();
    }

    public function addUserAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
        $this->checkAuthorisation();
		
		if (!$this->rHasId()) $this->redirect( 'index.php' );

		$this->setUserId( $this->rGetId() );
		$this->setUserBefore();
		$this->addUserToCurrentProject();
		$this->logUserChange( $this->translate('added user to project') );
		$this->redirect('index.php');
    }

    public function removeUserAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
        $this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect( 'index.php' );

		$this->setUserId( $this->rGetId() );
		if ( $this->canRemoveUser() )
		{
			$this->setUserBefore();
			$this->removeUserFromCurrentProject();
			$this->logUserChange( $this->translate('removed user from project') );
		}
		$this->redirect('index.php');
    }

    public function createAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Create new collaborator'));
		$this->setUserId( null );
		$this->setUserBefore();

		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->setNewUserData( $this->rGetAll() );
			$this->sanitizeNewUserData();
			$this->userDataCheck();
			$this->userPasswordCheck();
			$this->userDataSave();
			$this->userPasswordSave();
			$this->setUser();
			$this->addUserToCurrentProject();
			$this->logUserChange( $this->translate('created user') );
		}
		
		$this->smarty->assign( 'roles', $this->getRoles() );
		$this->smarty->assign('user', $this->rGetAll() );
		$this->printPage( 'edit' );
    }

    public function editAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
        $this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect( 'index.php' );

		$this->setPageName($this->translate('Edit project collaborator'));
		$this->setUserId( $this->rGetId() );
		$this->setUser();
		$this->setUserBefore();

		if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->setNewUserData( $this->rGetAll() );
			$this->sanitizeNewUserData();
			$this->userDataCheck();
			$this->userDataSave();
			$this->userPasswordCheck();
			$this->userPasswordSave();
			$this->userRoleCheck();
			$this->userRoleSave();
			$this->userRightsSave();
			$this->userTaxaSave();
			$this->setUser();
			$this->logUserChange( $this->translate('edited user') );
		} 
		else
		if ($this->rHasVal('action','reset_permissions') && !$this->isFormResubmit())
		{
			$this->setNewUserData( $this->rGetAll() );
			$this->resetUserPermissions();
			$this->logUserChange( $this->translate('reset user permissions') );
			$this->setUser();
		} 
		else
		if ($this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			if ( $this->getUserId()==$this->getCurrentUserId() )
			{
				$this->addError($this->translate('You cannot delete yourself.'));
			}
			elseif ( $this->getUser()['is_sysadmin'] )
			{
				$this->addError($this->translate('You cannot delete a system administrator.'));
			}
			else
			{
				$this->removeUserFromCurrentProject();
				$this->logUserChange( $this->translate('removed user from current project') );
				if ( $this->deleteUserIfWithoutProjects() ) $this->logUserChange( $this->translate('deleted user') );
				$this->redirect( 'index.php ');
			}
		}

		$this->smarty->assign( 'user', $this->getUser() );
		$this->smarty->assign( 'roles', $this->getRoles() );
		$this->smarty->assign( 'expert_role_id', $this->getExpertRoleId() );
		$this->smarty->assign( 'modules', $this->getProjectModulesUser() );
		
		$this->printPage();
    }
	
    public function deleteAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
        $this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect( 'index.php' );

		$this->setPageName($this->translate('Deleting user'));
		$this->setUserId( $this->rGetId() );
		$this->setUser();
		$this->setUserBefore();

		if ($this->rHasVal('action','delete') && !$this->isFormResubmit())
		{
			if ( $this->getUserId()==$this->getCurrentUserId() )
			{
				$this->addError($this->translate('You cannot delete yourself.'));
			}
			elseif ( $this->getUser()['is_sysadmin'] )
			{
				$this->addError($this->translate('You cannot delete a system administrator.'));
			}
			else
			{
				$this->removeUserFromCurrentProject();
				$this->logUserChange( $this->translate('removed user from current project') );
				if ( $this->deleteUserIfWithoutProjects() ) $this->logUserChange( $this->translate('deleted user') );
				$this->addMessage( $this->translate('User deleted.') );
			}
		}

		$this->printPage();
    }

    public function passwordAction()
    {
        $this->setPageName($this->translate('Reset password'));

		if ($this->rHasVal('email') && !$this->isFormResubmit())
		{
			$u = $this->models->Users->_get(
				array(
					'id' => array(
						'email_address' => trim($this->rGetVal('email')
						)
					)
				)
			);

			if (count((array)$u)==1)
			{

				$newPass = $this->generateRandomPassword();

				$this->sendPasswordEmail($u[0],$newPass);

				$r = $this->models->Users->save(
					array(
						'id' => $u[0]['id'],
						'password' => $this->userPasswordEncode($newPass)
					)
				);

				$this->addMessage($this->translate('Your password has been reset. An e-mail with a new password has been sent to you.'));

			}
			else
			{
				$this->addError($this->translate('Invalid or unknown e-mail address.'));
			}
		}

		$this->printPage();

    }

	private function getUserProjectRole( $userid )
	{
		return $this->models->UsersModel->getUserProjectRole(array(
			'project_id'=>$this->getCurrentProjectId(),
			'user_id'=>$userid
		));
	}

	private function getUserModuleAccess( $userid )
	{
		return $this->models->UsersModel->getUserModuleAccess(array(
			'project_id'=>$this->getCurrentProjectId(),
			'user_id'=>$userid
		));
	}

	private function getUserItemAccess( $userid )
	{
		$d=$this->models->UsersModel->getUserItemAccess(array(
			'project_id'=>$this->getCurrentProjectId(),
			'user_id'=>$userid
		));
		
		foreach((array)$d as $key=>$val)
		{
			$d[$key]['label']=$this->formatTaxon( $val );
   			$d[$key]['label']=$this->addHybridMarker( array( 'name'=>$d[$key]['label'],'base_rank_id'=>$val['base_rank_id'] ) );
   			$d[$key]['taxon']=$this->addHybridMarker( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}

		return $d;
	}

	private function getUserCanPublish( $userid )
	{
		$d=$this->models->UserModuleAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$userid
			),
			'limit'=>1
		));

		return $d ? $d[0]['can_publish']==1 : false;
	}			

	private function getUserIsSysadmin( $userid )
	{
		return $this->getUserProjectRole( $userid )['role_id']==ID_ROLE_SYS_ADMIN;
	}			

	private function getRoles()
	{
		return $this->models->Roles->_get( array( 'id' => array('hidden'=>'0') ) );
	}

	private function getProjectModulesUser()
	{
		$d=$this->getProjectModules();
		
		foreach((array)$d['modules'] as $key=>$val)
		{
			if ( $val['show_in_menu']==0 )
			{
				unset( $d['modules'][$key] );
			}
		}

		$u=$this->models->UserModuleAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$this->getUserId(),
				'module_type'=>'standard'
			),
			'fieldAsIndex'=>'module_id'
		));
		
		foreach((array)$d['modules'] as $key=>$val)
		{
			$d['modules'][$key]['access']=
				isset($u[$val['module_id']]) && $u[$val['module_id']]['module_type']=='standard' ? $u[$val['module_id']] : null;
		}
		
		$u=$this->models->UserModuleAccess->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId(),
				'user_id'=>$this->getUserId(),
				'module_type'=>'custom'
			),
			'fieldAsIndex'=>'module_id'
		));
		
		foreach((array)$d['freeModules'] as $key=>$val)
		{
			$d['freeModules'][$key]['access']=
				isset($u[$val['id']]) && $u[$val['id']]['module_type']=='custom' ? $u[$val['id']] : null;
		}
		
		return $d;
	}

    private function addUserToCurrentProject()
    {
		if ( is_null($this->getUserId()) )
		{
			return;
		}
		
		$role_id = isset($this->getNewUserData['role_id']) ? $this->getNewUserData['role_id'] : $this->getExpertRoleId();
		
		if ( is_null($role_id) )
		{
			$this->addError( $this->translate('Cannot add user without role ID.') );
			return;
		}
		
        $this->models->ProjectsRolesUsers->save(array(
            'id' => null,
            'project_id' => $this->getCurrentProjectId(),
            'role_id' => $role_id,
            'user_id' => $this->getUserId(),
            'active' => 1
        ));
		
		$this->addMessage( $this->translate('Added user to current project.') );
    }

	private function removeUserFromCurrentProject()
	{
		$d = array(
			'user_id' => $this->getUserId(),
			'project_id' => $this->getCurrentProjectId(),
		);
			
        $this->models->ProjectsRolesUsers->delete( $d );
        $this->models->UserModuleAccess->delete( $d );
        $this->models->UserItemAccess->delete( $d );
	}

	private function userDataCheck()
	{
		$this->setNewUserDataSave( true );
		$this->isNameCorrect();
		$this->isUsernameCorrect();
		$this->isUsernameUnique();
		$this->isEmailAddressCorrect();
		$this->isEmailAddressUnique();
	}

	private function userDataSave()
	{
		if ( $this->getNewUserDataSave()==false )
		{
			$this->addMessage( $this->translate('Data not saved.') );
			return;
		}

		$data=$this->getNewUserData();

		unset( $data['password'] );
		unset( $data['module'] );
		unset( $data['module_read'] );
		unset( $data['module_write'] );
		unset( $data['custom'] );
		unset( $data['custom_read'] );
		unset( $data['custom_write'] );
		unset( $data['role_id'] );
		unset( $data['can_publish'] );
	
		$this->models->Users->save( $data );

		if ( empty($data['id']) )
		{
			$this->setUserId( $this->models->Users->getNewId() );
			$this->addMessage( $this->translate('New user created.') );
		}
		else
		{
			$this->addMessage( $this->translate('Data saved.') );
		}
	}

	private function userPasswordCheck()
	{
		$this->setNewUserPasswordSave( true );
		$this->isPasswordCorrect();
	}

	private function userPasswordSave()
	{
		$password=$this->getNewUserData()['password'];

		if ( !empty($password) && $this->getNewUserPasswordSave()==false )
		{
            $this->addMessage( $this->translate('Password not saved.') );
		}
		else
		if ( !empty($password) && empty($this->getUserId()) )
		{
            $this->addMessage( $this->translate('Cannot save password (empty user ID).') );
		}
		else
		if ( !empty($password) && !empty($this->getUserId()) )
		{
			$password=$this->userPasswordEncode( $password );
			$this->models->Users->save( array('id'=>$this->getUserId(),'password'=>$password) );
            $this->addMessage( $this->translate('Password saved.') );
			$this->models->Users->save( array('id'=>$this->getUserId(),'password_changed'=>'now()') );
		}
	}

	private function userRoleCheck()
	{
		$this->setNewUserRoleSave( true );
	}

	private function userRoleSave()
	{
		if ( $this->getNewUserRoleSave()==false )
		{
            $this->addMessage( $this->translate('Data not saved.') );
			return;
		}

		$role_id=$this->getNewUserData()['role_id'];

		$user=$this->getUser();
		
		if ( $role_id==$user['project_role']['role_id'] )
		{
			return;
		}

		if ( empty($role_id) )
		{
            $this->addMessage( $this->translate('Cannot save empty role ID).') );
		}
		else
		if ( empty($user['project_role']['id']) )
		{
			$this->models->ProjectsRolesUsers->save(array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'role_id' => $role_id,
				'user_id' => $this->getUserId(),
				'active' => 1
			));

            $this->addMessage( $this->translate('Saved role.') );
		}
		else
		{
			$this->models->ProjectsRolesUsers->save(array(
				'id' => $user['project_role']['id'],
				'project_id' => $this->getCurrentProjectId(),
				'role_id' => $role_id,
				'user_id' => $this->getUserId(),
				'active' => 1
			));

            $this->addMessage( $this->translate('Updated role.') );
		}
	}

	private function userRightsSave()
	{
		$data=$this->getNewUserData();
		
		$this->models->UserModuleAccess->delete(
			array(
				'user_id' => $this->getUserId(),
				'project_id' => $this->getCurrentProjectId()
			));
			
		$modules=isset($data['module']) ? $data['module'] : null;
		$custom=isset($data['custom']) ? $data['custom'] : null;
		
		if ( $data['role_id']>=$this->getExpertRoleId() )
		{
			$can_publish=isset($data['can_publish']) ? $data['can_publish'] : '0';
		}
		else
		{
			$can_publish='1';
		}
		
		foreach((array)$modules as $key=>$val)
		{
			$can_read=isset($data['module_read']) && isset($data['module_read'][$key]) ? $data['module_read'][$key]=='on' : false;
			$can_write=isset($data['module_write']) && isset($data['module_write'][$key]) ? $data['module_write'][$key]=='on' : false;

			$this->models->UserModuleAccess->save(
				array(
					'id'=>null,
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $key,
					'module_type'=>'standard',
					'user_id' => $this->getUserId(),
					'can_read' => $can_read ? '1' : '0',
					'can_write' => $can_write ? '1' : '0',
					'can_publish' => $can_publish,
				));
		}


		foreach((array)$custom as $key=>$val)
		{
			$can_read=isset($data['custom_read']) && isset($data['custom_read'][$key]) ? $data['custom_read'][$key]=='on' : false;
			$can_write=isset($data['custom_write']) && isset($data['custom_write'][$key]) ? $data['custom_write'][$key]=='on' : false;
			
			$this->models->UserModuleAccess->save(
				array(
					'id'=>null,
					'project_id' => $this->getCurrentProjectId(),
					'module_id' => $key,
					'module_type'=>'custom',
					'user_id' => $this->getUserId(),
					'can_read' => $can_read ? '1' : '0',
					'can_write' => $can_write ? '1' : '0',
					'can_publish' => $can_publish,
				));
		}
		
		$this->addMessage( $this->translate('Updated rights.') );
	}

	private function userTaxaSave()
	{
		$d=$this->getNewUserData();

		if ( isset($d['taxon']) )
		{
			$taxa=$d['taxon'];
		}

		$this->models->UserItemAccess->delete(
			array(
				'user_id' => $this->getUserId(),
				'project_id' => $this->getCurrentProjectId()
			));
			

		if ( $this->models->UserItemAccess->getAffectedRows() > 0 )
			$this->addMessage( $this->translate('Updated taxa.') );
	
		if ( isset($taxa) ) 
		{
			foreach((array)$taxa as $key=>$val)
			{
				$this->models->UserItemAccess->save(
					array(
						'id'=>null,
						'project_id' => $this->getCurrentProjectId(),
						'user_id' => $this->getUserId(),
						'item_id' => $val,
						'item_type'=>'taxon'
					));
			}
			
			if ( $this->models->UserItemAccess->getAffectedRows() > 0 )
				$this->addMessage( $this->translate('Updated taxa.') );
		}
		

	}

    private function isNameCorrect()
    {
		$first_name=$this->getNewUserData()['first_name'];
		$last_name=$this->getNewUserData()['last_name'];
		
		$str1=sprintf($this->translate('First name should be between %s and %s characters.'),$this->checksName['min'],$this->checksName['max']);
		$str2=sprintf($this->translate('Last name should be between %s and %s characters.'),$this->checksName['min'],$this->checksName['max']);

        if ( strlen($first_name) < $this->checksName['min'] )
		{
            $this->addError( $str1 );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($first_name) > $this->checksName['max'] )
		{
            $this->addError( $str1 );
			$this->setNewUserDataSave( false );
        }

        if ( strlen($last_name) < $this->checksName['min'] )
		{
            $this->addError( $str2 );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($last_name) > $this->checksName['max'] )
		{
            $this->addError( $str2 );
			$this->setNewUserDataSave( false );
        }
    }

    private function isUsernameCorrect()
    {
		$username=$this->getNewUserData()['username'];
		
		$str=sprintf($this->translate('Username should be between %s and %s characters.'),$this->checksUsername['min'],$this->checksUsername['max']);

        if ( strlen($username) < $this->checksUsername['min'] )
		{
            $this->addError( $str );
			$this->setNewUserDataSave( false );
        } else
        if ( strlen($username) > $this->checksUsername['max'] )
		{
            $this->addError( $str );
			$this->setNewUserDataSave( false );
        } else
        if (isset($this->checksUsername['regExp']) && !preg_match($this->checksUsername['regExp'],$username) )
		{
            $this->addError( $this->translate('Username has incorrect format.') );
			$this->setNewUserDataSave( false );
        }
    }

    private function isUsernameUnique()
    {
		$username=$this->getNewUserData()['username'];

		$d=$this->models->Users->_get(array(
			'id'=>array( 'username'=>$username, 'id !='=>$this->getUserId() ),
			'columns'=>'count(*) as total'
		));

		if ($d[0]['total']>0)
		{
			$this->addError( $this->translate('Username already exists.') );
			$this->setNewUserDataSave( false );
		}
    }

    private function isEmailAddressCorrect()
    {
		$email_address=$this->getNewUserData()['email_address'];

        if ( !filter_var($email_address, FILTER_VALIDATE_EMAIL) )
		{
            $this->addError( $this->translate('Invalid e-mail address.') );
			$this->setNewUserDataSave( false );
        }
    }

    private function isEmailAddressUnique()
    {
		$email_address=$this->getNewUserData()['email_address'];

		$d=$this->models->Users->_get(array(
			'id'=>array( 'email_address'=>$email_address, 'id !='=>$this->getUserId() ),
			'columns'=>'count(*) as total'
		));

		if ($d[0]['total']>0)
		{
			$this->addError($this->translate('E-mail address already exists.'));
			$this->setNewUserDataSave( false );
		}
    }

    private function isPasswordCorrect()
    {
		$p1=$this->getNewUserData()['password'];
		$p2=$this->getNewUserData()['password_repeat'];
		$id=$this->getNewUserData()['id'];
		
		// existing user not entering new passwords: no change
        if ( !empty($id) && ( empty($p1) && empty($p2) ) ) return;
		
		$str=sprintf($this->translate('Password should be between %s and %s characters.'),$this->checksPassword['min'],$this->checksPassword['max']);

        if ( $p1!=$p2 )
		{
            $this->addError( $this->translate('Passwords not the same.') );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( empty($id) && empty($p1) && empty($p2) )
		{
            $this->addError( $this->translate('Passwords is required.') );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( strlen($p1) < $this->checksPassword['min'] )
		{
            $this->addError( $str );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( strlen($p1) > $this->checksPassword['max'] )
		{
            $this->addError( $str );
			$this->setNewUserPasswordSave( false );
        }
		else
        if ( isset($this->checksPassword['regExp']) && !preg_match($this->checksPassword['regExp'],$p1) )
		{
            $this->addError( $this->translate('Password has incorrect format.') );
			$this->setNewUserPasswordSave( false );
        }
    }





	private function setExpertRoleId()
	{
		$d=$this->models->Roles->_get(array('id'=>array('role'=>'Editor')));
		$this->_expertroleid=$d[0] ? $d[0]['id'] : null;
	}

	private function getExpertRoleId()
	{
		return $this->_expertroleid;
	}

	private function setAllProjectUsers()
	{
		$this->_allprojectusers=$this->models->UsersModel->getProjectUsers(array(
			'project_id'=>$this->getCurrentProjectId()
		));
		
		foreach((array)$this->_allprojectusers as $key=>$user)
		{
			$this->_allprojectusers[$key]['module_access']=$this->getUserModuleAccess( $user['id'] );
			$this->_allprojectusers[$key]['item_access']=$this->getUserItemAccess( $user['id'] );
			$this->_allprojectusers[$key]['project_role']=$this->getUserProjectRole( $user['id'] );
			$this->_allprojectusers[$key]['can_publish']=$this->getUserCanPublish( $user['id'] );
			$this->_allprojectusers[$key]['is_sysadmin']=$this->getUserIsSysadmin( $user['id'] );
			$this->_allprojectusers[$key]['hidden']=$this->getUserIsSysadmin( $user['id'] );
		}
	}

	private function getAllProjectUsers()
	{
		return $this->_allprojectusers;
	}

	private function setUserId( $userid )
	{
		$this->_userid = $userid;
	}

	private function getUserId()
	{
		return $this->_userid;
	}

	private function setUser()
	{
		$this->_user=$this->models->Users->_get(array(
			'id' => array( 'id' => $this->getUserId() ),
			'columns' => '*, datediff(curdate(),created) as days_active'
		));
		
		if ( $this->_user ) 
		{
			$this->_user=$this->_user[0];
			$this->_user['module_access']=$this->getUserModuleAccess( $this->getUserId() );
			$this->_user['item_access']=$this->getUserItemAccess( $this->getUserId() );
			$this->_user['project_role']=$this->getUserProjectRole( $this->getUserId() );
			$this->_user['can_publish']=$this->getUserCanPublish( $this->getUserId() );
			$this->_user['is_sysadmin']=$this->getUserIsSysadmin( $this->getUserId() );
		}
		else
		{
			$this->_user=null;
		}
	}
			
	private function getUser()
	{
		return $this->_user;
	}

	private function setNewUserData( $data )
	{
		$this->_newuserdata=$data;
	}

	private function getNewUserData()
	{
		return $this->_newuserdata;
	}

	private function setNewUserDataSave( $state )
	{
		$this->_newuserdatasave=$state;
	}

	private function getNewUserDataSave()
	{
		return $this->_newuserdatasave;
	}

	private function setNewUserPasswordSave( $state )
	{
		$this->_newuserpasswordsave=$state;
	}

	private function getNewUserPasswordSave()
	{
		return $this->_newuserpasswordsave;
	}

	private function setNewUserRoleSave( $state )
	{
		$this->_newuserrolesave=$state;
	}

	private function getNewUserRoleSave()
	{
		return $this->_newuserrolesave;
	}



	private function sanitizeNewUserData()
    {
		$data=$this->getNewUserData();

        if (isset($data['email_address']))
		{
            $data['email_address'] = strtolower($data['email_address']);
        }

        foreach ((array) $data as $key => $val)
		{
			if ($key=='password' || $key=='password_repeat') continue;

			if (is_array($val))
			{
		        foreach ((array) $val as $key2 => $val2)
				{
		            $val[$key2] = trim($val2);
				}
			}
			else
			{
	            $data[$key] = trim($val);
			}
        }

		$this->setNewUserData( $data );
    }

    private function userPasswordEncode( $password )
    {
		$this->helpers->PasswordEncoder->setForceMd5( false );
		$this->helpers->PasswordEncoder->setPassword( $password );
		$this->helpers->PasswordEncoder->encodePassword();
		return $this->helpers->PasswordEncoder->getHash();
    }

	private function generateRandomPassword()
	{
		$chars='abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		srand((double)microtime()*1000000);

		$i = 0;
		$pass = '' ;

		while ($i <= $this->dataChecks['password']['default_length'])
		{
			$num = rand()%33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass.$tmp;
			$i++;
		}

		return $pass;
	}
	
	private function resetUserPermissions()
	{
		$modules=array();
		$custom=array();
		$module_read=array();
		$module_write=array();
		$custom_read=array();
		$custom_write=array();

		$d=$this->getProjectModulesUser();

		foreach((array)$d['modules'] as $val)
		{
			$modules[$val['module_id']]='on';
			$module_read[$val['module_id']]='on';
		}
		
		foreach((array)$d['freeModules'] as $val)
		{
			$custom[$val['id']]='on';
			$custom_read[$val['id']]='on';
		}

		/*		
		if( $this->getUser()['project_role']['role_id']==ID_ROLE_SYS_ADMIN )
		{
			// unnecessary, ID_ROLE_SYS_ADMIN has default full access
		}
		else
		*/
		if( $this->getUser()['project_role']['role_id']==ID_ROLE_LEAD_EXPERT )
		{
			foreach((array)$d['modules'] as $val)
			{
				
				$module_write[$val['module_id']]='on';
			}
			
			foreach((array)$d['freeModules'] as $val)
			{
				$custom_write[$val['id']]='on';
			}
			$can_publish='1';
		}
		else
		if( $this->getUser()['project_role']['role_id']==ID_ROLE_EDITOR )
		{
			$can_publish='0';
		}	

		$this->_newuserdata['module']=$modules;
		$this->_newuserdata['custom']=$custom;
		$this->_newuserdata['module_read']=$module_read;
		$this->_newuserdata['module_write']=$module_write;
		$this->_newuserdata['custom_read']=$custom_read;
		$this->_newuserdata['custom_write']=$custom_write;
		$this->_newuserdata['can_publish']=$can_publish;
			
		$this->userRightsSave();

	}

	private function deleteUser()
	{
		$this->models->Users->delete(array('id' => $this->getUserId()));
	}

	private function deleteUserIfWithoutProjects()
	{
        $d=$this->models->ProjectsRolesUsers->_get( array(
			'id'=> array('user_id' => $this->getUserId()),
			'columns'=>'count(*) as total'
		));

		if ( $d[0]['total']==0 )
		{
			$this->deleteUser();
			return true;
		}
	}
	
	private function setUserBefore()
	{
		if ( is_null($this->getUserId()) )
		{
			$this->_userBefore=null;
		}
		else
		{
			$this->_userBefore=$this->getUser();
		}
	}

	private function getUserBefore()
	{
		return $this->_userBefore;
	}

	private function canRemoveUser()
	{
		$this->setUser();
		return $this->getUser()['is_sysadmin']!=true;
	}

	private function logUserChange( $note )
	{
		$this->setUser();
		$b=$this->getUserBefore();
		$a=$this->getUser();
		unset($b['password']);
		unset($a['password']);
		$this->logChange(array('before'=>$b,'after'=>$a,'note'=>$note));
	}

}

