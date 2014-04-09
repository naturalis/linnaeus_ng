<?php

include_once ('Controller.php');

class WebservicesController extends Controller
{
    private $_usage=null;
	private $_fromDate=null;
	private $_taxonId=null;
	private $_project=null;
	private $_matchType=null;
	private $_useOldNsrLinks=false;
	private $_taxonUrl=null;
	private $_thumbBaseUrl = 'http://images.naturalis.nl/thumb/';
	private $_190x100BaseUrl = 'http://images.naturalis.nl/190x100/';


    public $usedModels = array(
		'taxon',
		'commonname',
		'synonym',
		'names',
		'media_taxon',
		'nsr_ids',
		'media_meta',
		'literature2'
    );

    public $controllerPublicName = 'Webservices';

    public function __construct($p=null)
    {
        parent::__construct($p);

		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
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

		// pid is mandatory, now checked in initialise()
		//$this->checkProject();
		$this->checkFromDate();
		
		if (is_null($this->getCurrentProjectId())||is_null($this->getFromDate())) {
			$this->sendErrors();
			return;
		}

		$offset = isset($this->requestData['offset']) && is_numeric($this->requestData['offset']) ? (int)$this->requestData['offset'] : null;
		$rowcount = isset($this->requestData['rows']) && is_numeric($this->requestData['rows']) ? (int)$this->requestData['rows'] : null;
		$onlyCount = $this->rHasVal('count','1');

		if ($onlyCount) {

			$query="
				select
					count(_a.id) as total
				from %PRE%names _a
				left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
				left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_d.project_id
				where _a.project_id=".$this->getCurrentProjectId()."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null";

		} else {
			
			$url=sprintf($this->_taxonUrl,$this->getTaxonId());

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
					_f.default_label as rank,".
				($this->_useOldNsrLinks ?
					"concat('http://www.nederlandsesoorten.nl/',replace(_i.nsr_id,'tn.nlsr.','nsr/'))" :
					"concat('".$url."',_a.taxon_id)"
				)."
					 as url,
					_h.id as taxon_valid_name_id
				from %PRE%names _a
				
				left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
				left join %PRE%languages _c on _a.language_id=_c.id
				left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
				left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_e.project_id
				left join %PRE%ranks _f on _e.rank_id=_f.id
				
				left join %PRE%name_types _g on _a.project_id=_g.project_id and _g.nametype='isValidNameOf'
				left join %PRE%names _h on _h.taxon_id=_a.taxon_id and _h.type_id=_g.id and _a.project_id=_h.project_id

				left join %PRE%ids _i on _a.project_id=_i.project_id and _a.taxon_id=_i.lng_id and _i.item_type='taxon'

				where _a.project_id=".$this->getCurrentProjectId()."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d'))
				)
				and _e.rank_id >= ".SPECIES_RANK_ID."
				and _d.taxon is not null".
				(!is_null($rowcount) ? ' limit '.$rowcount : '').
				(!is_null($rowcount) && !is_null($offset) ? ' offset '.$offset : '');

		}

		$names = $this->models->Names->freeQuery($query);

		$result=array(
			'pId'=>$this->getCurrentProjectId(),
			'from'=>$this->getFromDate()
		);
		
		if (!is_null($rowcount))
			$result['rows']=$rowcount;

		if (!is_null($rowcount) && !is_null($offset))
			$result['offset']=$offset;

		$p=$this->getProject();
		$result['project']=$p['title'];
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

	public function taxonAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?pid=<id>&taxon=<scientific name>
parameters:
  pid".chr(9)." : project id (mandatory)
  taxon".chr(9)." : scientific name of the taxon to retrieve (mandatory)
";

		// pid is mandatory, now checked in initialise()
		//$this->checkProject();
		
		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}

		$this->checkTaxonId();
		
		if (is_null($this->getTaxonId())) {
			$this->sendErrors();
			return;
		}

		$taxon=$this->getTaxonById($this->getTaxonId());

		$ranklabel=$this->models->LabelProjectRank->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'project_rank_id' => $taxon['rank_id'],
				'language_id' => LANGUAGE_ID_ENGLISH
			), 
			'columns' => 'label'
		));

		$ranklabel=$ranklabel[0]['label'];
				

		$query="
			select
				_a.content, _b.page
			from %PRE%content_taxa _a, %PRE%pages_taxa _b
			where _a.project_id=".$this->getCurrentProjectId()."
			and _a.taxon_id =".$this->getTaxonId()."
			and _a.language_id =".LANGUAGE_ID_DUTCH."
			and _b.project_id=".$this->getCurrentProjectId()."
			and _a.page_id=_b.id
			and _b.page='Summary_dutch'
			"
			;

		$descriptions = $this->models->Names->freeQuery($query);

		$summary=strip_tags($descriptions[0]['content']);
		
		if (empty($summary)) $summary=null;
		

		/*
		//and (_b.page='Summary_dutch' or _b.page='Description')
		//order by _b.page desc"

		foreach((array)$descriptions as $val) {
			if (($val['page']=='Description' && empty($description)) || $val['page']=='Summary_dutch')
			$description=$val['content'];
		}
		
		$description = strip_tags($description);
		*/
		
		$url=$this->makeNsrLink();

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
				_b.nametype
			from %PRE%names _a
			
			left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
			left join %PRE%languages _c on _a.language_id=_c.id
			left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id

			where _a.project_id=".$this->getCurrentProjectId()."
			and _a.taxon_id =".$this->getTaxonId()
			;

		$names=$this->models->Names->freeQuery($query);

        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.id as media_id,
				_a.file_name as url,
				_b.meta_data as copyright,
				_c.meta_data as caption,
				_d.meta_data as creator,
				date_format(_e.meta_date,'%e %M %Y') as date_created

			from %PRE%media_taxon _a

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _c
				on _a.id=_c.media_id and _a.project_id=_c.project_id and _c.sys_label = 'beeldbankCaption'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumVervaardiging'

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.taxon_id = ".$this->getTaxonId()."
			order by
				_e.meta_date desc
			");

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

		$this->smarty->assign('json',json_encode($result));
		
		$this->printPage('template');
	}

	public function ezAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?pid=<id>&nsr=<id>
parameters:
  pid".chr(9)." : project id (mandatory)
  nsr".chr(9)." : NSR-id of the taxon to retrieve (mandatory)
";

		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}

		$this->checkNsrId();
		
		if (is_null($this->getTaxonId())) {
			$this->sendErrors();
			return;
		}

		$taxon=$this->getTaxonById($this->getTaxonId());

        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.id as media_id,
				concat('".$this->_thumbBaseUrl."',_a.file_name) as url,
				_b.meta_data as copyright,
				_d.meta_data as creator,
				date_format(_e.meta_date,'%e %M %Y') as date_created

			from %PRE%media_taxon _a

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumVervaardiging'

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.taxon_id = ".$this->getTaxonId()."
			order by
				_e.meta_date desc
			limit 4
		");

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

		$this->smarty->assign('json',json_encode($result));
		
		$this->printPage('template');
	}

	public function lastImageAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?pid=<id>
parameters:
  pid".chr(9)." : project id (mandatory)
";

		// pid is mandatory, now checked in initialise()

		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}

        $media=$this->models->MediaMeta->freeQuery("
			select media_id from %PRE%media_meta where meta_date = 
			(select
				max(meta_date)
			from 
				%PRE%media_meta
			where 
				sys_label = 'beeldbankDatumAanmaak'
				and project_id = ".$this->getCurrentProjectId()."
			)
		");
		
        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.taxon_id,
				_a.id as media_id,
				concat('".$this->_190x100BaseUrl."',_a.file_name) as url_image,
				_b.meta_data as copyright,
				_d.meta_data as fotograaf,
				date_format(_e.meta_date,'%e %M %Y') as date_created,
				_f.meta_data as lokatie,
				_g.meta_data as validator,
				_k.name as dutch_name,
				trim(replace(_m.name,_m.authorship,'')) as scientific_name

			from %PRE%media_taxon _a
			
			left join %PRE%names _k
				on _a.taxon_id=_k.taxon_id
				and _a.project_id=_k.project_id
				and _k.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_PREFERRED_NAME."')
				and _k.language_id=".LANGUAGE_ID_DUTCH."

			left join %PRE%names _m
				on _a.taxon_id=_m.taxon_id
				and _a.project_id=_m.project_id
				and _m.type_id=(select id from %PRE%name_types where project_id = ".
					$this->getCurrentProjectId()." and nametype='".PREDICATE_VALID_NAME."')
				and _m.language_id=".LANGUAGE_ID_SCIENTIFIC."

			left join %PRE%media_meta _b
				on _a.id=_b.media_id and _a.project_id=_b.project_id and _b.sys_label = 'beeldbankCopyright'
			left join %PRE%media_meta _d
				on _a.id=_d.media_id and _a.project_id=_d.project_id and _d.sys_label = 'beeldbankFotograaf'
			left join %PRE%media_meta _e
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumAanmaak'
			left join %PRE%media_meta _f
				on _a.id=_f.media_id and _a.project_id=_f.project_id and _f.sys_label = 'beeldbankLokatie'
			left join %PRE%media_meta _g
				on _a.id=_g.media_id and _a.project_id=_g.project_id and _g.sys_label = 'beeldbankValidator'

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.id = ".$media[0]['media_id']."

			limit 1
		");
		
		$this->setTaxonId($media[0]['taxon_id']);

		$result=array('pId'=>$this->getCurrentProjectId());

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');

		$result['url_recent_images']=$this->makeNsrRecentImagesLink();

	
		$result['image']=$media[0];
		$result['image']['url_taxon']=$this->makeNsrLink();
		// to be done!
		$result['image']['url_image_popup']=$result['image']['url_taxon'];

		$result['labels']=array(
			'taxon_link'=>$this->translate('Bekijk alle gegevens'),
			'more_recent_link'=>$this->translate('Meer recente afbeeldingen'),
			'lokatie'=>$this->translate('Locatie'),
			'fotograaf'=>$this->translate('Fotograaf'),
			'validator'=>$this->translate('Validator'),
			'date_created'=>$this->translate('Datum plaatsing'),
		);

		$this->smarty->assign('json',json_encode($result));
		
		$this->printPage('template');
	}

	public function statisticsAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?pid=<id>
parameters:
  pid".chr(9)." : project id (mandatory)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		$result['labels']=array(
			'title'=>'Stand van zaken',
			'main'=>'Aantal soorten in Nederland',
			'sub'=>'Het soortenregister bevat'
		);


        $d=$this->models->Taxon->freeQuery("
			select
				count(*) as total

			from
				%PRE%taxa _a

			left join %PRE%projects_ranks _f
				on _a.rank_id=_f.id
				and _a.project_id=_f.project_id

			left join %PRE%presence_taxa _g
				on _a.id=_g.taxon_id
				and _a.project_id=_g.project_id

			left join %PRE%presence _h
				on _g.presence_id=_h.id
				and _g.project_id=_h.project_id


			where
				_a.project_id =".$this->getCurrentProjectId()."
				and _f.rank_id = ".SPECIES_RANK_ID."
				and _h.established=1"
		);

		$result['statistics']['species']=
			array(
				'count'=>$d[0]['total'],
				'label'=>$this->translate('Aantal soorten in Nederland'),
				'description'=>$this->translate('Aantal soorten in Nederland met status voorkomen 1, 1a, 2, 2a of 2b.')
			);


        $d=$this->models->MediaTaxon->freeQuery("
			select
				count(distinct taxon_id) as total

			from %PRE%media_taxon _a

			where
				_a.project_id = ".$this->getCurrentProjectId()
		);
		
		$result['statistics']['species_with_image']=
			array(
				'count'=>$d[0]['total'],
				'label'=>$this->translate('Soorten met foto\'s')
			);

        $d=$this->models->MediaTaxon->freeQuery("
			select
				count(id) as total
			from %PRE%media_taxon _a
			where
				_a.project_id = ".$this->getCurrentProjectId()
		);
		
		$result['statistics']['images']=
			array(
				'count'=>$d[0]['total'],
				'label'=>$this->translate('Foto\'s')
			);
		
		$d=$this->models->Names->freeQuery("
				select
					count(_a.id) as total,
					_b.nametype,
					_a.language_id
				
				from %PRE%names _a
				
				left join %PRE%name_types _b
					on _a.project_id = _b.project_id
					and _a.type_id = _b.id
				
				where
					_a.project_id = ".$this->getCurrentProjectId()."
				group by _a.language_id,_b.nametype"
		);

		$t['count_name_accepted']=$t['count_name_dutch']=$t['count_name_english']=0;
		
		foreach((array)$d as $key => $val)
		{
			if ($val['nametype']=='isValidNameOf')
				$t['count_name_accepted']+=$val['total'];

			if ($val['language_id']==LANGUAGE_ID_DUTCH)
				$t['count_name_dutch']+=$val['total'];

			if ($val['language_id']==LANGUAGE_ID_ENGLISH)
				$t['count_name_english']+=$val['total'];
		}

		$result['statistics']['accepted_names']=
			array(
				'count'=>$t['count_name_accepted'],
				'label'=>$this->translate('Geaccepteerde soortnamen')
			);

		$result['statistics']['dutch_names']=
			array(
				'count'=>$t['count_name_dutch'],
				'label'=>$this->translate('Nederlandse namen')
			);

		$result['statistics']['english_names']=
			array(
				'count'=>$t['count_name_english'],
				'label'=>$this->translate('Engelse namen')
			);
	

		$d=$this->models->Taxon->freeQuery("
			select count(distinct id) as total from
			(
				select
					actor_id as id
				from %PRE%presence_taxa
				
				where
					project_id = ".$this->getCurrentProjectId()."
					and actor_id is not null
					
				union

				select
					expert_id as id
				
				from %PRE%names
				
				where
					project_id = ".$this->getCurrentProjectId()."
					and expert_id is not null
				) as unification"


		);

		$result['statistics']['specialist']=
			array(
				'count'=>$d[0]['total'],
				'label'=>$this->translate('Specialisten')
			);
		


        $d=$this->models->Literature2->_get(array(
			'id'=> array('project_id' => $this->getCurrentProjectId()),
			'columns'=>'count(*) as total'
		));

		$result['statistics']['literature']=
			array(
				'count'=>$d[0]['total'],
				'label'=>$this->translate('Literatuurbronnen')
			);
		

		$result['statistics']['distribution_map']=
			array(
				'count'=>'(...)',
				'label'=>$this->translate('Verspreidingskaarten')
			);

		$this->smarty->assign('json',json_encode($result));
		
		$this->printPage('template');
	}




    private function initialise()
    {
		$this->useCache=false;
		$this->checkProject();

		if (is_null($this->getCurrentProjectId()))
		{

			$this->addError('cannot get project settings.');

		} 
		else 
		{

			$this->_taxonUrl = $this->getSetting('ws_names_taxon_url');
			$this->_useOldNsrLinks = $this->getSetting('ws_use_old_nsr_links')==1;
			$this->models->Taxon->freeQuery("SET lc_time_names = 'nl_NL'");

		}

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

			$p = $this->models->Project->_get(array(
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
	
	private function setProject($project)
	{
		$this->_project=$project;
	}

	private function getProject()
	{
		return $this->_project;
	}
	


	private function checkFromDate()
	{
		if (!$this->rHasVal('from')) {

			$this->addError('no starttime specified of retrieval window.');

		} else {

			$d=str_split($this->requestData['from'],2);

			if (strlen($this->requestData['from'])==8 && checkdate($d[2],$d[3],$d[0].$d[1])) {
				$this->setFromDate($this->requestData['from']);
				return true;
			} else {
				$this->addError('illegal date: '.$this->requestData['from'].'.');
			}
		}
		return false;
	}

	private function setFromDate($date)
	{
		$this->_fromDate=$date;
	}

	private function getFromDate()
	{
		return $this->_fromDate;
	}
	


	private function checkTaxonId()
	{
		if (!$this->rHasVal('taxon')) {

			$this->addError('no taxon name specified.');

		} else {
			
			$taxon=trim($this->requestData['taxon']);
			
			$this->setMatchType('literal');

			$t = $this->models->Taxon->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon' => $taxon
				)
			));

			if (!$t) {
				
				$this->setMatchType('name only');

				$t = $this->models->Names->freeQuery("
					select
						_a.taxon_id as id, _a.name
					from %PRE%names _a
					left join %PRE%name_types _b 
						on _a.type_id=_b.id and _a.project_id=_b.project_id
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and trim(REPLACE(_a.name,_a.authorship,''))='". mysql_real_escape_string($taxon) ."'
						and _b.nametype = 'isValidNameOf'"
				);

			}
		
			if (!$t) {
				$this->addError('taxon name "'.$this->requestData['taxon'].'" not found in this project.');
			} else {
				$this->setTaxonId($t[0]['id']);
				return $t;
			}
		}
		return false;
	}

	private function checkNsrId()
	{
		if (!$this->rHasVal('nsr')) {

			$this->addError('no NSR-id specified.');

		} else {
			
			$nsr=trim($this->requestData['nsr']);
			
			$this->setMatchType('literal');

			$t = $this->models->NsrIds->_get(array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'nsr_id' => 'tn.nlsr.concept/'.str_pad(mysql_real_escape_string($nsr),12,'0',STR_PAD_LEFT),
					'item_type' => 'taxon'
				)
			));
			
		
			if (!$t) {
				$this->addError('NSR-id "'.$this->requestData['nsr'].'" not found in this project.');
			} else {
				$this->setTaxonId($t[0]['lng_id']);
				return $t;
			}
		}
		return false;
	}



	private function setTaxonId($id)
	{
		$this->_taxonId=$id;
	}

	private function getTaxonId()
	{
		return $this->_taxonId;
	}
	

	private function setMatchType($t)
	{
		$this->_matchType=$t;
	}

	private function getMatchType()
	{
		return $this->_matchType;
	}
	

	private function sendErrors()
	{
		$this->smarty->assign('json',json_encode(array('errors'=>$this->errors,'usage'=>$this->_usage)));
		$this->printPage('template');
	}

	/*
		NSR project specific, should be changed once NSR migration is complete
	*/
	private function makeNsrLink()
	{
		if (!$this->_useOldNsrLinks) {

			return sprintf($this->_taxonUrl,$this->getTaxonId());

		} else {

			$ids = $this->models->NsrIds->_get(
				array('id'=>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'lng_id' => $this->getTaxonId(),
						'item_type' => 'taxon'
					)
				)
			);

			return 'http://www.nederlandsesoorten.nl/'.str_replace('tn.nlsr.','nsr/',$ids[0]['nsr_id']);
		}

	}

	private function makeNsrMediaLink()
	{
		if (!$this->_useOldNsrLinks)
			return $this->makeNsrLink().'&cat=media';
		else
			return $this->makeNsrLink().'/imagesAndSounds';
	}

	private function makeNsrRecentImagesLink()
	{
		if (!$this->_useOldNsrLinks)
			return 'http://'.$_SERVER['HTTP_HOST'].'/linnaeus_ng/app/views/search/nsr_recent_pictures.php';
		else
			return 'http://www.nederlandsesoorten.nl/nsr/nsr/recentImages.html';
	}







}