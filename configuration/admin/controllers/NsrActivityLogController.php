<?php

include_once ('NsrController.php');

class NsrActivityLogController extends NsrController
{

	private $_logLinesPerPage=25;

    public $usedModels = array(
		'activity_log',
    );

    public $cssToLoad = array(
		'paginator.css',
		'activity_log.css'
	);

    public $modelNameOverride='NsrActivityLogModel';
    public $controllerPublicName = 'Project management';
    public $includeLocalMenu = false;

    public function __construct()
    {
        parent::__construct();
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );	
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function indexAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Activity log'));

	    $this->setBreadcrumbIncludeReferer(
          array(
             'name' => 'Project overview',
             'url' => $this->baseUrl . $this->appName . '/views/projects/overview.php'
           )
         );
	
	
		$search=(null!==$this->rGetAll() ? $this->rGetAll() : null);

		$results=$this->getLogLines($search);

		$this->smarty->assign('search',$search);
        $this->smarty->assign('results',$results);
		$this->smarty->assign('querystring',$this->reconstructQueryString(array('page')));

		$this->printPage('activity_log');
    }

	private function getLogLines($p=null)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_logLinesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_logLinesPerPage;

		$tz=isset($this->generalSettings['serverTimeZone']) ? $this->generalSettings['serverTimeZone'] : 'Europe/Amsterdam';

		$d=$this->models->NsrActivityLogModel->getActivityLog(array(
			"timezone"=>$tz,
			"project_id"=>$this->getCurrentProjectId(),
			"search"=>$search,
			"limit"=>$limit,
			"offset"=>$offset
		));

		function splitOldName($name)
		{
			if (strpos($name,'(')!==false)
			{
				$d=preg_split('/\(/',$name);
				$e=preg_split('/ - /',$d[1]);
				return array(
					'original'=>$name,
					'name'=>trim($d[0]),
					'username'=>trim($e[0]),
					'email_address'=>isset($e[1]) ? trim($e[1],' )') : ''
				);
			}
			else
			{
				return array('original'=>$name,'name'=>$name,'username'=>'','email_address'=>'');
			}
		}

		foreach((array)$d as $key =>$val)
		{
			$d[$key]['user']=splitOldName($val['user']);
			$d[$key]['data_before']=@unserialize($val['data_before']);
			$d[$key]['data_after']=@unserialize($val['data_after']);
		}

		$count=$this->models->ActivityLog->freeQuery('select found_rows() as total');

		return array('count'=>$count[0]['total'],'data'=>$d,'perpage'=>$this->_logLinesPerPage);

	}

	private function getLineDifferences($a,$b)
	{

		if ((!is_array($a) && is_array($b)) || (is_array($a) && !is_array($b)))
		{
			return array('before'=>$a,'after'=>$b);
		} else if (!is_array($a)) {
			return $a==$b ? null : array('before'=>$a,'after'=>$b);
		} else {
			$ta=array();
			$tb=array();
			$tc=array();
			$td=array();

			foreach($a as $key=>$val)
			{
				if (is_array($val))
				{
					$ta[$key]=array_diff($a[$key],$b[$key]);
					$tb[$key]=array_diff($b[$key],$a[$key]);
				} else {
					$tc[$key]=$a[$key];
					$td[$key]=$b[$key];
				}
			}

			$te=array_diff($tc,$td);
			$tf=array_diff($td,$tc);

			return array(
				'before'=>array_merge($ta,$te),
				'after'=>array_merge($tb,$tf)
			);

		}
	}

	private function reconstructQueryString($ignore)
	{
		if (null===$this->rGetAll()) {
            return;
        }

		$querystring=null;

		foreach((array)$this->rGetAll() as $key=>$val)
		{
			if (in_array($key,$ignore)) {
                continue;
            }

			if (is_array($val))
			{
				foreach((array)$val as $k2=>$v2)
				{
					$querystring.=$key.'['.$k2.']='.$v2.'&';
				}

			} else {
				$querystring.=$key.'='.$val.'&';
			}
		}

		return $querystring;
	}

}
