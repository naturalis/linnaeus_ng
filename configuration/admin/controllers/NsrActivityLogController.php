<?php


include_once ('NsrController.php');

class NsrActivityLogController extends NsrController
{

	private $_logLinesPerPage=100;

    public $usedModels = array(
		'activity_log',
    );

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
	
	
 	private function nbcHandleOverlappingItemsFromDetails($p)
	{
		//return $p['data'];

        $data = isset($p['data']) ? $p['data'] : null;
        $action = isset($p['action']) ? $p['action'] : 'remove';

		if (count((array)$data)==1)
			return $data;

		$d = array();
		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal)	{
				foreach((array)$cVal['states'] as $state)	{
					if (isset($state['id']))
						$d[$key][] = $characteristic_id.':'.$state['id']; // characteristic_id:state_id
				}
			}
		}

		$common = call_user_func_array('array_intersect',$d);

		foreach((array)$data as $key => $dVal) {
			foreach((array)$dVal['d'] as $characteristic_id => $cVal) {
				foreach((array)$cVal['states'] as $sVal => $state)	{
					
					if (isset($state['id'])) {

						if (in_array($characteristic_id.':'.$state['id'],$common)) {
							if ($action=='remove') {
								unset($data[$key]['d'][$characteristic_id]['states'][$sVal]);
							} else
							if ($action=='tag') {
								$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'] = '<span class="overlapState">'.$data[$key]['d'][$characteristic_id]['states'][$sVal]['label'].'</span>';
							}
						}
						
					}
					
				}
				
				if (count((array)$data[$key]['d'][$characteristic_id]['states'])==0 && $action=='remove') {

					unset($data[$key]['d'][$characteristic_id]);

				}
			}
		}

		return $data;
		
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
		}

		q($d,1);


		$count=$this->models->ActivityLog->freeQuery('select found_rows() as total');
		
		return array('count'=>$count[0]['total'],'data'=>$d,'perpage'=>$this->_logLinesPerPage);

	}



}
