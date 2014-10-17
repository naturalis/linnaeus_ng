<?php


include_once ('NsrController.php');

class NsrActivityLogController extends NsrController
{

	private $_logLinesPerPage=100;

    public $usedModels = array(
		'activity_log',
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
		
		$results=$this->getLogLines();
		
        $this->smarty->assign('results',$results);

		$this->printPage('activity_log');
    }
	
	
	private function getLogLines($p=null)
	{
		$search=!empty($p['search']) ? $p['search'] : null;
		$limit=!empty($p['limit']) ? $p['limit'] : $this->_logLinesPerPage;
		$offset=(!empty($p['page']) ? $p['page']-1 : 0) * $this->_logLinesPerPage;
		//$order=!empty($p['order']) ? $p['order'] : null;

		$d=$this->models->ActivityLog->freeQuery("
			select
				SQL_CALC_FOUND_ROWS	
				id,
				user_id,
				user,
				controller,
				view,
				data_before,
				data_after,
				note,
				DATE_FORMAT(created,'%d %b %Y, %T') as last_change_hr
	
			from %PRE%activity_log _a

			where _a.project_id =".$this->getCurrentProjectId()."

			order by 
				created desc
			".(isset($limit) ? "limit ".(int)$limit : "")."
			".(isset($offset) & isset($limit) ? "offset ".(int)$offset : "")
		);
		
		foreach((array)$d as $key =>$val)
		{
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





}
