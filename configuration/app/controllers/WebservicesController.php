<?php


include_once ('Controller.php');

class WebservicesController extends Controller
{
    private $_usage=null;

	private $_taxonUrls=array(
//		1=>'http://dev2.etibioinformatics.nl/linnaeus_ng_nsr/app/views/species/taxon.php?epi=1&id='
		1=>'http://localhost/linnaeus_ng/app/views/species/taxon.php?epi=1&id='
	);
	
    public $usedModels = array(
		'commonname',
		'synonym',
		'names'
    );
    
    public $controllerPublicName = 'Webservives';

    public function __construct($p=null)
    {
        parent::__construct($p);
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

	public function resolveProjectId()
	{
		if (!$this->rHasVal('pid')) {

			$this->addError('no project id specified.');

		} else {

			$p = $this->models->Project->_get(array(
				'id' => (isset($this->requestData['pid']) ? $this->requestData['pid'] : $this->requestData['epi'])
			));
		
			if (!$p) {
				$this->addError('illegal project id. there is no project with id '.$this->requestData['pid'].'.');
			} else {
				return $p;
			}
		}
		return false;
	}

	private function verifyFromDate()
	{
		if (!$this->rHasVal('from')) {

			$this->addError('no starttime specified of retrieval window.');

		} else {

			$d=str_split($this->requestData['from'],2);

			if (strlen($this->requestData['from'])==8 && checkdate($d[2],$d[3],$d[0].$d[1])) {
				return true;
			} else {
				$this->addError('illegal date: '.$this->requestData['from'].'.');
			}
		}
		return false;
	}

	public function namesAction()
	{

		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?pid=<id>&from=<YYYYMMDD>[&rows=<n>[&offset=<n>]][&count=1]
parameters:
  pid".chr(9)." : project id (mandatory)
  from".chr(9)." : date of start of retrieval window, based on last change. format: <YYYYMMDD> (mandatory)
  rows".chr(9)." : limits the number of rows returned. format: <n> (optional)
  offset : specify offset of rows returned. format: <n> (optional; only works in combination with the 'rows' parameter)
  count".chr(9)." : when set to 1, only the number of records in the resultset are returned (optional)
";

		$project=$this->resolveProjectId();
		$from=$this->verifyFromDate();
		
		if ($project===false||$from===false) {
			$this->sendErrors();
			return;
		}

		$offset = isset($this->requestData['offset']) && is_numeric($this->requestData['offset']) ? (int)$this->requestData['offset'] : null;
		$rowcount = isset($this->requestData['rows']) && is_numeric($this->requestData['rows']) ? (int)$this->requestData['rows'] : null;
		$fromDate = $this->requestData['from'];
		$onlyCount = $this->rHasVal('count','1');

		if ($onlyCount) {

			$query="
				select
					count(_a.id) as total
				from %PRE%names _a
				left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
				left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_d.project_id
				where _a.project_id=".$project['id']."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$fromDate."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$fromDate."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null";

		} else {
			
			$url=$this->_taxonUrls[$project['id']];
			
			$query="
				select
					_a.id as name_id,
					_a.name,
					_a.uninomial,
					_a.specific_epithet,
					_a.infra_specific_epithet,
					_a.authorship,
					_c.language,
					_c.iso3 as language_iso3,
					if(_a.last_change='0000-00-00 00:00:00',_a.created,_a.last_change) as last_change,
					_b.nametype,
					_a.taxon_id, 
					_d.taxon, 
					_f.default_label as rank,
					concat('".$url."',_a.taxon_id) as url,

					_h.id as taxon_valid_name_id

				from %PRE%names _a
				
				left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
				left join %PRE%languages _c on _a.language_id=_c.id
				left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
				left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_e.project_id
				left join %PRE%ranks _f on _e.rank_id=_f.id
				
				left join %PRE%name_types _g on _a.project_id=_g.project_id and _g.nametype='isValidNameOf'
				left join %PRE%names _h on _h.taxon_id=_a.taxon_id and _h.type_id=_g.id and _a.project_id=_h.project_id

				where _a.project_id=".$project['id']."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$fromDate."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$fromDate."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null".
				(!is_null($rowcount) ? ' limit '.$rowcount : '').
				(!is_null($rowcount) && !is_null($offset) ? ' offset '.$offset : '');

		}

		$names = $this->models->Names->freeQuery($query);

		$result=array(
			'pId'=>$project['id'],
			'from'=>$fromDate,
		);
		
		if (!is_null($rowcount))
			$result['rows']=$rowcount;

		if (!is_null($rowcount) && !is_null($offset))
			$result['offset']=$offset;

		$result['project']=$project['title'];
		$result['exported']=date('c');
		
		if ($onlyCount) {
			$result['count']=$names[0]['total'];
		} else {
			$result['count']=count((array)$names);
			$result['names']=$names;
		}

		$this->smarty->assign('json',json_encode($result));
		
		$this->printPage('template');

	}

	private function sendErrors()
	{
		$this->smarty->assign('json',json_encode(array('errors'=>$this->errors,'usage'=>$this->_usage)));
		$this->printPage('template');
	}


/*


multimedia:
	uri to image in NSR
	link to img
	caption
	creator


*/












	public function searchAction()
	{
		
		if (!$this->rHasVal('s'))
			die('no search string');
			
		$s = $this->requestData['s'];
		
		if (strlen($s) < 3)
			die('search string too short');

		/*

			- must check if project and module is published at all!		
			- *REAL* LSID's!
		
		*/

		$taxa = $this->models->Taxon->_get(
			array(
				'id' => array('taxon like' => '%'.$s.'%'),
				'columns' => 'project_id,id,taxon,rank_id',
			)
		);
		
		$res = array();
		
		foreach((array)$taxa as $key => $val) {
			
			$d = $this->models->Project->_get(
				array(
					'id' => array('id' => $val['project_id']),
				)
			);

			$res[$key] = array(
			'scientific_name' => $val['taxon'],
			'taxon_id' => $val['id'],
			'project' => $d[0]['title'],
			'project_id' => $d[0]['id'],
			'rank_id' => $val['rank_id'],
			'LSID' => 'urn:lsid:etibioinformatics.nl:'.$d[0]['sys_name'].':'.md5('')
			);

		}
		
		echo '<pre>';
		print_r($res);
		
	}


//
//taxonAction

}
