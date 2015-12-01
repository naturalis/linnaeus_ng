<?php

include_once ('Controller.php');
include_once ('RdfController.php');

class WebservicesController extends Controller
{
    private $_usage=null;
	private $_fromDate=null;
	private $_taxonId=null;
	private $_project=null;
	private $_matchType=null;
	private $_taxonUrl='/linnaeus_ng/app/views/species/nsr_taxon.php?epi=1&id=%s';
	private $_thumbBaseUrl='http://images.naturalis.nl/160x100/';
	private $_190x100BaseUrl='http://images.naturalis.nl/190x100/';
	private $_nsrOriginalImageBaseUrl='http://images.naturalis.nl/original/';
	private $_JSONPCallback=false;
	private $_JSON=null;

    public $usedModels = array(
		'nsr_ids',
		'literature_2',
		'media_meta',
		'taxon_trend_years'
    );

    public $controllerPublicName = 'Webservices';

    public function __construct( $p=null )
    {
        parent::__construct($p);
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->Rdf = new RdfController(array('checkForProjectId'=>false,'checkForSplash'=>false));
		
		$this->checkProject();

		if (is_null($this->getCurrentProjectId()))
		{
			$this->addError('cannot get project settings.');
		} 
		else 
		{
			$this->models->WebservicesModel->initDb( array( "db_lc_time_names" => $this->getSetting('db_lc_time_names') ) );
			$this->checkJSONPCallback();
		}
    }

	public function namesAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&from=<YYYYMMDD>[&rows=<n>[&offset=<n>]][&count=1]
parameters:
  pid".chr(9)." : project id (mandatory)
  from".chr(9)." : date of start of retrieval window, based on last change. format: <YYYYMMDD> (mandatory)
  rows".chr(9)." : limits the number of rows returned. format: <n> (optional)
  offset : specify offset of rows returned. format: <n> (optional; only works in combination with the 'rows' parameter)
  count".chr(9)." : when set to 1, only the number of records in the resultset are returned (optional)
";

		//pid is mandatory, now checked in initialise()
		//$this->checkProject();
		$this->checkFromDate();
		
		if ( is_null($this->getCurrentProjectId() ) || is_null($this->getFromDate()) )
		{
			$this->sendErrors();
			return;
		}

		$offset = null!==$this->rGetVal('offset') && is_numeric($this->rGetVal('offset')) ? (int)$this->rGetVal('offset') : null;
		$rowcount = null!==$this->rGetVal('rows') && is_numeric($this->rGetVal('rows')) ? (int)$this->rGetVal('rows') : null;
		$count_only = $this->rHasVal('count','1');

		if ($count_only)
		{
			$names=$this->models->WebservicesModel->getNameCount(array(
				"project_id"=>$this->getCurrentProjectId(),
				"from_date"=>$this->getFromDate()
			));
		} 
		else
		{
			$data=$this->models->WebservicesModel->getNames(array(
				"project_id"=>$this->getCurrentProjectId(),
				"from_date"=>$this->getFromDate(),
				"rowcount"=>$rowcount,
				"offset"=>$offset,
				"url"=>sprintf($this->_taxonUrl,$this->getTaxonId())
			));
			$names=$data['data'];
		}

		if (is_null($names)) $names=array();

		$result=array(
			'pId'=>$this->getCurrentProjectId(),
			'from'=>$this->getFromDate()
		);
		
		if (!is_null($rowcount))
		{
			$result['rows']=$rowcount;
		}

		if (!is_null($rowcount) && !is_null($offset))
		{
			$result['offset']=$offset;
		}

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');
		
		if ($count_only)
		{
			$result['count']=$names[0]['total'];
		} 
		else
		{
			$result['count']=count((array)$names);
			$result['total_count']=$data['count'];
			$result['names']=$names;
		}

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function taxonAction()	
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&taxon=<scientific name>
parameters:
  pid".chr(9)." : project id (mandatory)
  taxon".chr(9)." : scientific name of the taxon to retrieve (mandatory)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}
		
		$this->checkTaxonId();
		
		if (is_null($this->getTaxonId()))
		{
			$this->sendErrors();
			return;
		}

		$taxon=$this->getTaxonById($this->getTaxonId());

		$ranklabel=$this->models->LabelsProjectsRanks->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'project_rank_id' => $taxon['rank_id'],
					'language_id' => LANGUAGE_ID_ENGLISH
				), 
				'columns' => 'label'
			));

		$ranklabel=$ranklabel[0]['label'];

		$summary=$this->models->WebservicesModel->getTaxonSummary(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$this->getTaxonId(),
		));

		if (empty($summary)) $summary=null;
		
		$url='http://'.$_SERVER['HTTP_HOST'].$this->makeNsrLink();

		$names=$this->models->WebservicesModel->getTaxonNames(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$this->getTaxonId()
		));

		$media=$this->models->WebservicesModel->getTaxonMedia(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$this->getTaxonId(),
			"image_base_url"=>$this->_nsrOriginalImageBaseUrl
		));

		$result=array(
			'pId'=>$this->getCurrentProjectId(),
			'search'=>$this->requestData['taxon'],
			'match'=>$this->getMatchType()
		);
		
		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');
		
		$result['taxon']=array(
			'id'=>$taxon['id'],
			'scientific_name'=>$taxon['taxon'],
			'rank'=>$ranklabel,
			'url'=>$url,
			'summary'=>$summary,
			'names'=>$names,
			'media'=>$media
		);

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function taxonPageAction()	
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&taxon=<scientific name>&cat=<page ID>
parameters:
  pid".chr(9)." : project id (mandatory)
  taxon".chr(9)." : scientific name of the taxon to retrieve (mandatory)
  cat".chr(9)." : page ID of the content page (mandatory)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}
		
		$this->checkTaxonId();

		if (is_null($this->getTaxonId()))
		{
			$this->sendErrors();
			return;
		}

		if (is_null($this->rGetVal('cat')))
		{
			$this->addError("no page category specified (param 'cat')");
			$this->sendErrors();
			return;
		}

		$p=$this->models->WebservicesModel->getTaxonPage(array(
			"taxon_id"=>$this->getTaxonId(),
			"project_id"=>$this->getCurrentProjectId(),
			"page_id"=>$this->rGetVal('cat')
		));

		$page=$title=$rdf=null;

		if ( $p )
		{
			$page=$p['content'];
			$title=$p['title'];

			if (isset($p['id']))
			{
				foreach((array)$this->Rdf->getRdfValues($p['id']) as $val)
				{
					$rdf[]=array('predicate'=>$val['predicate'],'object'=>$val['data']);
				}
			}
		}

		$result=
			array(
				'pId'=>$this->getCurrentProjectId(),
				'taxon'=>$this->rGetVal('taxon'),
				'cat'=>$this->rGetVal('cat'),
				'striptags'=>$this->rHasVal('striptags','1'),
			);
		
		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');
		$result['page']=
			array(
				'title'=>$title,
				'body'=>$page,
				'rdf'=>$rdf
			);

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function ezAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&nsr=<id>
parameters:
  pid".chr(9)." : project id (mandatory)
  nsr".chr(9)." : NSR-id of the taxon to retrieve (mandatory)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		$this->checkNsrId();
		
		if (is_null($this->getTaxonId()))
		{
			$this->sendErrors();
			return;
		}

		$taxon=$this->getTaxonById($this->getTaxonId());

        $media=		$media=$this->models->WebservicesModel->getTaxonMedia(array(
			"project_id"=>$this->getCurrentProjectId(),
			"taxon_id"=>$this->getTaxonId(),
			"image_base_url"=>$this->_nsrOriginalImageBaseUrl,
			"limit"=>4
		));
		
		$result=array(
			'pId'=>$this->getCurrentProjectId(),
			'search'=>$this->requestData['nsr']
		);
		
		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');
		
		$result['taxon']=array(
			'id'=>$taxon['id'],
			'scientific_name'=>$taxon['taxon'],
			'url'=>$this->makeNsrLink(),
			'url_media'=>$this->makeNsrMediaLink(),
			'media'=>$media
		);

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function lastImageAction()
	{
		// returns 1 of the last $poolSize images
		$poolSize=20;

		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&size=<int>
parameters:
  pid".chr(9)." : project id (mandatory)
  size".chr(9)." : size of pool from which random image is selected (optional, max. 1000; default ".$poolSize.")
";

		if (
			$this->rHasVar('size') && 
			is_numeric($this->rGetVal('size')) && 
			$this->rGetVal('size')>0 && 
			$this->rGetVal('size')<1000
		)
		{
			$poolSize=$this->rGetVal('size');
		}

		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}

        $media=$this->models->WebservicesModel->getRandomRecentImage(array(
			"project_id"=>$this->getCurrentProjectId(),
			"pool_size"=>$poolSize,
			"img_base_url"=>$this->_190x100BaseUrl
		));
		
		$this->setTaxonId($media[0]['taxon_id']);

		$result=array('pId'=>$this->getCurrentProjectId());

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');

		$result['url_recent_images']='http://'.$_SERVER['HTTP_HOST'].'/linnaeus_ng/app/views/search/nsr_recent_pictures.php';
	
		$result['image']=$media[0];
		$result['image']['url_taxon']=$this->makeNsrLink();
		$result['image']['url_image_popup']=$this->makeNsrMediaLink()."&img=".$media[0]['file_name'];

		$result['labels']=array(
			'taxon_link'=>$this->translate('Bekijk alle gegevens'),
			'more_recent_link'=>$this->translate('Meer recente afbeeldingen'),
			'lokatie'=>$this->translate('Locatie'),
			'fotograaf'=>$this->translate('Fotograaf'),
			'validator'=>$this->translate('Validator'),
			'date_created'=>$this->translate('Datum plaatsing'),
		);

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function statisticsAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>
parameters:
  pid".chr(9)." : project id (mandatory)
";

		function format_number($n)
		{
			return number_format($n,0,',','.');
		}
	
		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}
		
		$result=$this->models->WebservicesModel->getEstablishedExoticAllTaxa(array(
			"project_id"=>$this->getCurrentProjectId()
		));

		$d=$this->models->WebservicesModel->getTaxonMediaCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));

		$result['statistics']['species_with_image']=
			array(
				'count'=>$d['species_with_image'],
				'label'=>$this->translate('Soorten met foto\'s')
			);

		$result['statistics']['images']=
			array(
				'count'=>$d['images'],
				'label'=>$this->translate('Foto\'s')
			);

		$d=$this->models->WebservicesModel->getNameTypeCount(array(
			"project_id"=>$this->getCurrentProjectId()
		));
		
		$result['statistics']['accepted_names']=
			array(
				'count'=>$d['accepted_names'],
				'label'=>$this->translate('Geaccepteerde soortnamen')
			);

		$result['statistics']['dutch_names']=
			array(
				'count'=>$d['dutch_names'],
				'label'=>$this->translate('Nederlandse namen')
			);
		/*
		$result['statistics']['english_names']=
			array(
				'count'=>$d['english_names'],
				'label'=>$this->translate('Engelse namen')
			);
		*/
	
		$result['statistics']['specialist']=
			array(
				'count'=>$this->models->WebservicesModel->getTaxonSpecialistCount(array("project_id"=>$this->getCurrentProjectId())),
				'label'=>$this->translate('Specialisten')
			);
		
        $d=$this->models->Literature2->_get(array(
			'id'=> array('project_id' => $this->getCurrentProjectId()),
			'columns'=>'count(*) as total'
		));

		$result['statistics']['literature']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Literatuurbronnen')
			);

        $d=$this->models->MediaMeta->_get(array(
			'id'=> array(
				'project_id' => $this->getCurrentProjectId(),
				'sys_label' => 'verspreidingsKaart',
				'meta_data' => 1
			),
			'columns'=>'count(*) as total'
		));

		$result['statistics']['distribution_map']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Verspreidingskaarten')
			);

        $d=$this->models->TaxonTrendYears->_get(array(
			'id'=> array(
				'project_id' => $this->getCurrentProjectId()
			),
			'columns'=>'count(distinct taxon_id) as total'
		));

		$result['statistics']['trend_graph']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Trendgrafieken')
			);

		$exotenGroupId=1;
		
		$result['statistics']['exotics']=
			array(
				'count'=>$this->models->WebservicesModel->getExoticsPassportCount(array(
					"project_id"=>$this->getCurrentProjectId(),
					"group_id"=>$exotenGroupId
				)),
				'label'=>$this->translate('Exotenpaspoorten')
			);


		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function searchAction()
	{
		$minStrLen=3;
		$this->setMatchType('match_all');
		$max=50;

		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&text=<(part of) scientific name>&start=1
parameters:
  pid".chr(9)." : project id (mandatory)
  text".chr(9)." : (part of a) scientific name (mandatory; minimum ".$minStrLen." characters)
  start".chr(9)." : 1 for match start only, or 0 for match everywhere (default) (optional)
  max".chr(9)." : maximum returned number of rows (optional; default 50; maximum 1000)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		if (!$this->rHasVal('text'))
		{
			$this->addError('no search text.');
			$this->sendErrors();
			return;
		}
		
		$search=$this->rGetVal('text');

		if (strlen($search)<$minStrLen)
		{
			$this->addError('search text too short (min '.$minStrLen.' characters).');
			$this->sendErrors();
			return;
		}

		if ($this->rHasVal('start') && $this->rGetVal('start')=='1')
		{
			$this->setMatchType('match_start');
		}

		$max=
			$this->rHasVal('max') && 
			is_numeric($this->rGetVal('max')) &&
			(int)$this->rGetVal('max')>0  &&
			(int)$this->rGetVal('max')<=1000  ? 
				(int)$this->rGetVal('max') : 
				$max;

		$taxa=$this->models->WebservicesModel->getSearchResults(array(
			'project_id'=>$this->getCurrentProjectId(),
			'language_id'=>$this->getCurrentLanguageId(),
			'search'=>$search,
			'match_type'=>$this->getMatchType(),
			'limit'=>$max
		));

		foreach((array)$taxa as $key => $val)
		{
			$taxa[$key]['label']=
				$val['name'].
				' ('.sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']).
					($val['name']!=$val['taxon']?' van '.$val['taxon']:'').
				')'.
				' ['.$val['common_rank'].']';
			unset($taxa[$key]['language_label']);
			unset($taxa[$key]['common_rank']);
			unset($taxa[$key]['nametype']);
		}

		$result=array(
			'pId'=>$this->getCurrentProjectId(),
			'search'=>$search,
			'match'=>$this->getMatchType(),
			'max'=>$max
		);

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');
		$result['count']=count((array)$taxa);
		$result['results']=$taxa;

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	private function checkProject()
	{
		if (!$this->rHasVal('pid'))
		{
			$this->setProject(null);
			$this->setCurrentProjectId(null);
			$this->addError('no project id specified.');
		}
		else
		{
			$p = $this->models->Projects->_get(array(
				'id' => $this->requestData['pid']
			));
		
			if (!$p)
			{
				$this->setProject(null);
				$this->setCurrentProjectId(null);
				$this->addError('illegal project id. there is no project with id '.$this->requestData['pid'].'.');
			} 
			else 
			{
				$this->setProject($p);
				$this->setCurrentProjectId($p['id']);
				return $p;
			}
		}
		return false;
	}
	
	private function setProject( $project )
	{
		$this->_project=$project;
	}

	private function getProject()
	{
		return $this->_project;
	}

	private function checkFromDate()
	{
		if (!$this->rHasVal('from'))
		{
			$this->addError('no starttime specified of retrieval window.');
		} 
		else
		{
			$d=str_split($this->rGetVal('from'),2);

			if (strlen($this->rGetVal('from'))==8 && checkdate($d[2],$d[3],$d[0].$d[1]))
			{
				$this->setFromDate($this->rGetVal('from'));
				return true;
			} 
			else 
			{
				$this->addError('illegal date: '.$this->rGetVal('from').'.');
			}
		}

		return false;
	}

	private function setFromDate( $date )
	{
		$this->_fromDate=$date;
	}

	private function getFromDate()
	{
		return $this->_fromDate;
	}

	private function checkTaxonId()
	{
		if (!$this->rHasVal('taxon'))
		{
			$this->addError('no taxon name specified.');
		} 
		else
		{
			$taxon=trim(strip_tags($this->rGetVal('taxon')));
			
			$this->setMatchType('concept');

			$t=$this->models->Taxa->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => $taxon
				)
			));

			if ($t)
			{
				$this->setTaxonId($t[0]['id']);
			}
			else
			{
				$this->setMatchType('valid name');

				$t = $this->models->WebservicesModel->resolveName(array(
					"project_id"=>$this->getCurrentProjectId(),
					"taxon"=>$taxon
				));

				if (!$t)
				{
					$this->addError('taxon name "'.$this->rGetVal('taxon').'" not found in this project.');
				} 
				else
				{
					$this->setTaxonId($t['id']);
				}
	
			}
		
		}
		return false;
	}

	private function checkNsrId()
	{
		if (!$this->rHasVal('nsr'))
		{
			$this->addError('no NSR-id specified.');
		} 
		else 
		{
			$nsr=trim($this->rGetVal('nsr'));
			
			$this->setMatchType('literal');

			$t = $this->models->NsrIds->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'nsr_id' => 'tn.nlsr.concept/'.str_pad($nsr,12,'0',STR_PAD_LEFT),
					'item_type' => 'taxon'
				)
			));
		
			if (!$t)
			{
				$this->addError('NSR-id "'.$this->requestData['nsr'].'" not found in this project.');
			} 
			else
			{
				$this->setTaxonId($t[0]['lng_id']);
				return $t;
			}
		}
		return false;
	}

	private function setTaxonId( $id )
	{
		$this->_taxonId=$id;
	}

	private function getTaxonId()
	{
		return $this->_taxonId;
	}
	
	private function setMatchType( $t )
	{
		$this->_matchType=$t;
	}

	private function getMatchType()
	{
		return $this->_matchType;
	}
	
	private function setJSON( $json )
	{
		$this->_JSON=$json;
	}

	private function getJSON()
	{
		return $this->_JSON;
	}
	
	private function checkJSONPCallback()
	{
		if ($this->rHasVal('callback'))
		{
			$this->setJSONPCallback($this->rGetVal('callback'));
		}
	}
	
	private function setJSONPCallback( $callback )
	{
		$this->_JSONPCallback=$callback;
	}

	private function getJSONPCallback()
	{
		return $this->_JSONPCallback;
	}
	
	private function hasJSONPCallback()
	{
		return $this->getJSONPCallback()!=false;
	}
	
	private function makeNsrLink()
	{
		return sprintf($this->_taxonUrl,$this->getTaxonId());
	}

	private function makeNsrMediaLink()
	{
		return $this->makeNsrLink().'&cat=media';
	}

	private function sendErrors()
	{
		$this->_usage = $this->_usage."  
function returns data as JSON. for JSONP, add a parameter 'callback=<name>' with the appropriate function name.
";
		$this->setJSON(json_encode(array('errors'=>$this->errors,'usage'=>$this->_usage)));
		header('Content-Type: application/json');			
		$this->printOutput(true);
	}

	private function printOutput( $suppressJSONP=false )
	{
		/*
		JSON looks like this:
			{ "name": "value" }
		Whereas JSONP looks like this:
			functionName({ "name": "value" });
		*/

		if ($this->hasJSONPCallback() && !$suppressJSONP)
		{
			$this->_JSON = $this->getJSONPCallback() . '(' . $this->_JSON .');';
		}

		$this->smarty->assign('json',$this->_JSON);

		header('Content-Type: application/json');			
		$this->printPage('template');
	}


}