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

    public $controllerPublicName = 'Soortenregister beheer';
    public $includeLocalMenu = false;

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function initialize()
    {
    }

    public function indexAction()
    {
		$this->checkAuthorisation();

        $this->setPageName($this->translate('Activity log'));
		
		$search=isset($this->requestData) ? $this->requestData : null;
		
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
		//$order=!empty($p['order']) ? $p['order'] : null;

		$tz=isset($this->generalSettings['serverTimeZone']) ? $this->generalSettings['serverTimeZone'] : 'Europe/Amsterdam';

		$d=$this->models->ActivityLog->freeQuery("
			select
				SQL_CALC_FOUND_ROWS	
				_a.id,
				_a.user_id,
				_a.user,
				_a.controller,
				_a.view,
				_a.data_before,
				_a.data_after,
				_a.note,
				DATE_FORMAT(CONVERT_TZ(_a.created,'UTC','".$tz."'),'%d %b %Y, %T') as last_change_hr,
				_u.id as user_user_id,
				_u.username as user_username,
				_u.first_name as user_first_name,
				_u.last_name as user_last_name,
				_u.email_address as user_email_address,
				_u.active as user_active
	
			from %PRE%activity_log _a
			
			left join %PRE%users _u
				on _a.user_id=_u.id

			where _a.project_id =".$this->getCurrentProjectId()."
			". (!is_null($search) ? " 
				and (
						_a.user like '%". mysql_real_escape_string($search) ."%' or
						_a.data_before like '%". mysql_real_escape_string($search) ."%' or
						_a.data_after like '%". mysql_real_escape_string($search) ."%' or
						_a.note like '%". mysql_real_escape_string($search) ."%' or
						DATE_FORMAT(_a.created,'%d %b %Y, %T') like '%". mysql_real_escape_string($search) ."%' or 
						concat(_u.first_name,' ',_u.last_name) like '%". mysql_real_escape_string($search) ."%' or 
						_u.email_address like '%". mysql_real_escape_string($search) ."%'
					) " : 
				"") ."
			order by 
				_a.created desc, _a.id desc
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		);
		
		function splitOldName($name)
		{
			$d=preg_split('/\(/',$name);
			$e=preg_split('/ - /',$d[1]);

			return array('original'=>$name,'name'=>trim($d[0]),'username'=>trim($e[0]),'email_address'=>trim($e[1],' )'));
		}
		
		foreach((array)$d as $key =>$val)
		{
			$d[$key]['user']=splitOldName($val['user']);
			$d[$key]['data_before']=unserialize($val['data_before']);
			$d[$key]['data_after']=unserialize($val['data_after']);
			//$d[$key]['differences']=$this->getLineDifferences($d[$key]['data_before'],$d[$key]['data_after']);
		}

		$count=$this->models->ActivityLog->freeQuery('select found_rows() as total');
		
		return array('count'=>$count[0]['total'],'data'=>$d,'perpage'=>$this->_logLinesPerPage);

	}

	private function getLineDifferences($a,$b)
	{

		if ((!is_array($a) && is_array($b)) || (is_array($a) && !is_array($b)))
		{
			return array('before'=>$a,'after'=>$b);
		} 
		else
		if (!is_array($a))
		{
			return $a==$b ? null : array('before'=>$a,'after'=>$b);
		} 
		else
		{
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
				}
				else
				{
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
		if (!isset($this->requestData)) return;

		$querystring=null;

		foreach((array)$this->requestData as $key=>$val)
		{
			if (in_array($key,$ignore)) continue;

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
