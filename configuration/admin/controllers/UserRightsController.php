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

    public function getUserRights()
    {








		return;

        $pru = $this->models->ProjectsRolesUsers->_get(
        array(
            'id' => array(
                'user_id' => $id ? $id : $this->getCurrentUserId()
            ),
            'columns' => 'project_id,role_id,active,\'1\' as member',
            'fieldAsIndex' => 'project_id'
        ));


        if ($this->isCurrentUserSysAdmin())
		{
            $p = $this->models->Projects->_get(array(
                'id' => '*'
            ));

            foreach ((array) $p as $val)
			{
                if (!isset($pru[$val['id']]))
				{
                    $pru[$val['id']] = array(
                        'project_id' => $val['id'],
                        'role_id' => (string) ID_ROLE_SYS_ADMIN,
                        'active' => (string) 1,
                        'member' => 0
                    );
                }
            }
        }

        foreach ((array) $pru as $key => $val)
		{
            $p = $this->models->Projects->_get(array(
                'id' => $val['project_id']
            ));

            // $val['project_id']==0 is the stub for all round system admin
            if ($p || $val['project_id'] == 0)
			{
                $r = $this->models->Roles->_get(array(
                    'id' => $val['role_id']
                ));

                if ($r) {

                    $userProjectRoles[] = array_merge($val,
                    array(
                        'project_name' => $p['sys_name'],
                        'project_title' => $p['title'],
                        'role_name' => $r['role'],
                        'role_description' => $r['description']
                    ));

                    foreach ((array) $rr as $rr_key => $rr_val)
					{
                        $r = $this->models->Rights->_get(array(
                            'id' => $rr_val['right_id']
                        ));

                        $rs[$val['project_id']][$r['controller']][$r['id']] = $r['view'];
                    }

                    $projectCount[$val['project_id']] = $val['project_id'];
                }
            }
        }


        $fmpu = $this->models->FreeModulesProjectsUsers->_get(array(
            'id' => array(
                'user_id' => $id ? $id : $this->getCurrentUserId()
            )
        ));

        foreach ((array) $fmpu as $key => $val)
		{
            $rs[$val['project_id']]['_freeModules'][$val['free_module_id']] = true;
        }

        $d = $this->getCurrentProjectId();

        return array(
            'roles' => isset($userProjectRoles) ? $userProjectRoles : null,
            'rights' => isset($rs) ? $rs : null,
            'number_of_projects' => isset($projectCount) ? count((array) $projectCount) : 0
        );
    		
		
    }

}