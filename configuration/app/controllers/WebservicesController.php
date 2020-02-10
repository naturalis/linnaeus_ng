<?php

/*
 * *** START OF RANT ***
 *
 * When this module is initialized outside the context of a Linnaeus project (business as
 * usual I would say), so without an existing project id, a lot of services just fail badly.
 * They work when the page is reloaded in a browser, but this of course is not how web
 * services are supposed to work. Debugging is a f*cking hell.
 *
 * As a consequence, some methods have simply been replicated using fixed values
 * (e.g. getTaxonById()) and probably can't used in a general way.
 * The whole program structure needs a rethink here.
 *
 * *** END OF RANT ***
 */


include_once ('Controller.php');
include_once ('ModuleSettingsReaderController.php');
include_once ('RdfController.php');


class WebservicesController extends Controller
{
    private $_usage=null;
	private $_fromDate=null;
	private $_taxonId=null;
	private $_taxon=null;
	private $_project=null;
	private $_matchType=null;
	private $_taxonUrl='/linnaeus_ng/app/views/species/nsr_taxon.php?epi=1&id=%s';
	private $_thumbBaseUrl='https://images.naturalis.nl/160x100/';
	private $_190x100BaseUrl='https://images.naturalis.nl/190x100/';
	private $_nsrOriginalImageBaseUrl='https://images.naturalis.nl/original/';
	private $_domainNamePatch="www.nederlandsesoorten.nl"; // HTTP_HOST is unreliable (reverse proxy); must become setting REFAC2015
	private $_JSONPCallback=false;
	private $_JSON=null;

    public $usedModels = array(
		'taxa',
		'names',
		'media_taxon',
		'nsr_ids',
		'media',
		'media_meta',
		'literature2',
		'taxon_trend_years',
		'labels_projects_ranks',
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
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&from=<YYYYMMDD>[&rows=<n>[&offset=<n>]][&count=1][&all=1]
parameters:
  pid".chr(9)." : project id (mandatory)
  from".chr(9)." : date of start of retrieval window, based on last change. format: <YYYYMMDD> (mandatory)
  rows".chr(9)." : limits the number of rows returned. format: <n> (optional)
  offset : specify offset of rows returned. format: <n> (optional; only works in combination with the 'rows' parameter)
  count".chr(9)." : when set to 1, only the number of records in the resultset are returned (optional)
  all".chr(9)." : when set to 1, names of all taxa are returned, not just species or lower (optional)
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
		$allTaxa = $this->rHasVal('all','1');

		if ($allTaxa) {
			$rankWhereClause = "";
		}
		else {
			$rankWhereClause = "and _e.rank_id >= ".SPECIES_RANK_ID;
		}

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
				" . $rankWhereClause . "
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
					_f.default_label as rank,
					concat('".$url."',_a.taxon_id) as url,
					_h.id as taxon_valid_name_id,
					_k.addition as remark
				from %PRE%names _a
				
				left join %PRE%name_types _b on _a.type_id=_b.id and _a.project_id=_b.project_id
				left join %PRE%languages _c on _a.language_id=_c.id
				left join %PRE%taxa _d on _a.taxon_id=_d.id and _a.project_id=_d.project_id
				left join %PRE%projects_ranks _e on _d.rank_id=_e.id and _a.project_id=_e.project_id
				left join %PRE%ranks _f on _e.rank_id=_f.id
				
				left join %PRE%name_types _g on _a.project_id=_g.project_id and _g.nametype='isValidNameOf'
				left join %PRE%names _h on _h.taxon_id=_a.taxon_id and _h.type_id=_g.id and _a.project_id=_h.project_id

				left join %PRE%nsr_ids _i on _a.project_id=_i.project_id and _a.taxon_id=_i.lng_id and _i.item_type='taxon'
				
				left join %PRE%names_additions _k
				  on _a.project_id=_k.project_id 
				  and _a.id=_k.name_id 
				  and _k.language_id=" .LANGUAGE_ID_DUTCH."

				where _a.project_id=".$this->getCurrentProjectId()."
				and (
					(_a.last_change='0000-00-00 00:00:00' && _a.created>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d')) ||
					(_a.last_change!='0000-00-00 00:00:00' && _a.last_change>=STR_TO_DATE('".$this->getFromDate()."','%Y%m%d'))
				)
				" . $rankWhereClause . "
				and _d.taxon is not null
				order by _a.taxon_id, _a.type_id, _a.id ".
				(!is_null($rowcount) ? ' limit '.$rowcount : '').
				(!is_null($rowcount) && !is_null($offset) ? ' offset '.$offset : ''
                );

		}

		$names = $this->models->Names->freeQuery($query);
		if (is_null($names)) $names=array();

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
  taxon".chr(9)." : scientific name or id of the taxon to retrieve (mandatory)
";

		// pid is mandatory, now checked in initialise()
		//$this->checkProject();
		
		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}

		$this->resolveTaxonName();
		
		if (is_null($this->getTaxonId())) {
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
		
		$url='https://'.$_SERVER['HTTP_HOST'].$this->makeNsrLink();

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
				concat('".$this->_nsrOriginalImageBaseUrl."',_a.file_name) as url,
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

       /* 
          this is a quick fix! ideally, $overviewImageRS should also include 
	  meta-data and replace $media for projects that have RS
      */
       $overviewImageRS=$this->models->MediaTaxon->freeQuery("
            select
                t1.rs_original
            from media as t1
            left join
                media_modules as t2 on t1.id = t2.media_id
            where
	        t2.overview_image = 1 and
                t2.item_id =  ".$this->getTaxonId()." and
                t2.project_id = ".$this->getCurrentProjectId()." and
                t1.deleted = 0
            order by
                t2.sort_order,
                t1.name");
    
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
	    
	        if (isset($overviewImageRS[0]) && isset($overviewImageRS[0]['rs_original']))
		{
			$result['taxon']['overview_image']=$overviewImageRS[0]['rs_original'];
		}
	    
		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();

	}

	public function taxonPageAction()	
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&taxon=<scientific name>&cat=<page ID>&lang=<lang>
parameters:
  pid".chr(9)." : project id (mandatory)
  taxon".chr(9)." : scientific name or id of the taxon to retrieve (mandatory)
  cat".chr(9)." : page ID of the content page (mandatory)
  lang".chr(9)." : language ID (optional; defaults to project default)
";

		// pid is mandatory, now checked in initialise()
		//$this->checkProject();
		
		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		$this->resolveTaxonName();

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

		if (!is_null($this->rGetVal('lang')))
		{
			$lang = $this->rGetVal('lang');
	        $matches = array_filter($this->getProjectLanguages(),function($a) use ($lang)
	        {
	            return $a["language_id"]==$lang;
	        });

	        $lang = count($matches)==1 ? array_values($matches)[0]["language_id"] : $this->getDefaultLanguageId();
		}
		else
		{
			$lang = $this->getDefaultLanguageId();
		}


		$query="
			select
				_b.id,
				ifnull(_c.title,_a.page) as title,
				_b.content

			from
				%PRE%pages_taxa _a
				
			left join %PRE%content_taxa _b
				on _a.id=_b.page_id
				and _a.project_id=_b.project_id
				and _b.language_id =".$lang."
				and _b.taxon_id =".$this->getTaxonId()."
				
			left join %PRE%pages_taxa_titles _c
				on _a.id=_c.page_id
				and _a.project_id=_c.project_id
				and _c.language_id =".$lang."

			where
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.id=" . $this->rGetVal('cat')
			;

		$p=$this->models->Names->freeQuery($query);

		$page=$title=$rdf=null;

		if ( $p )
		{
			$page=$p[0]['content'];
			$title=$p[0]['title'];

			if (isset($p[0]['id']))
			{
				foreach((array)$this->Rdf->getRdfValues($p[0]['id']) as $val)
				{
					$rdf[]=array('predicate'=>$val['predicate'],'object'=>$val['data']);
				}
			}
		}
		
		$taxon=$this->getTaxon();

		$result=
			array(
				'pId'=>$this->getCurrentProjectId(),
				'request'=>$this->rGetVal('taxon'),
				'taxon'=>$taxon['taxon'],
				'nametype'=>$taxon['nametype'],
				'match'=>$this->getMatchType(),
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
		$taxon['label']=$this->formatTaxon($taxon);

        $media=$this->models->MediaTaxon->freeQuery("
			select
				distinct
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

		/*
		// just the very last image
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
		*/		

        $media=$this->models->MediaMeta->freeQuery("
			select 
				_a.media_id

			from
				%PRE%media_meta _a

			left join %PRE%media_meta _meta9
				on _a.id=_meta9.media_id
				and _a.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			where 
				_a.sys_label = 'beeldbankDatumAanmaak'
				and _a.project_id = ".$this->getCurrentProjectId()."
				and ifnull(_meta9.meta_data,0)!=1

			order by 
				_a.meta_date desc

			limit ".$poolSize."
		");
		
		$ids=array();
		foreach((array)$media as $val)
		{
			$ids[]=$val['media_id'];
		}
		
		$ids=implode(',',$ids);
        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.taxon_id,
				_a.id as media_id,
				concat('".$this->_190x100BaseUrl."',_a.file_name) as url_image,
				_a.file_name,
				_b.meta_data as copyright,
				_d.meta_data as fotograaf,
				date_format(_e.meta_date,'%e %M %Y') as date_created,
				_f.meta_data as lokatie,
				_g.meta_data as validator,
				_k.name as dutch_name,
				trim(replace(ifnull(_m.name,''),ifnull(_m.authorship,''),'')) as scientific_name

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
				on _a.id=_e.media_id and _a.project_id=_e.project_id and _e.sys_label = 'beeldbankDatumVervaardiging'
			left join %PRE%media_meta _f
				on _a.id=_f.media_id and _a.project_id=_f.project_id and _f.sys_label = 'beeldbankLokatie'
			left join %PRE%media_meta _g
				on _a.id=_g.media_id and _a.project_id=_g.project_id and _g.sys_label = 'beeldbankValidator'

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.id in (".$ids.")

			order by rand() limit 0,1
		
		");

		$this->setTaxonId($media[0]['taxon_id']);

		$result=array('pId'=>$this->getCurrentProjectId());

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');

		$result['url_recent_images']='https://'.$this->_domainNamePatch.'/linnaeus_ng/app/views/search/nsr_recent_pictures.php';
	
		$result['image']=$media[0];
		$result['image']['url_taxon']=$this->makeNsrLink();
		$result['image']['url_image_popup']=$this->makeNsrMediaLink()."&img=".$media[0]['file_name'];

		$result['labels']=array(
			'taxon_link'=>$this->translate('Bekijk alle gegevens'),
			'more_recent_link'=>$this->translate('Meer recente afbeeldingen'),
			'lokatie'=>$this->translate('Locatie'),
			'fotograaf'=>$this->translate('Fotograaf'),
			'validator'=>$this->translate('Validator'),
			'date_created'=>$this->translate('Datum vervaardiging'),
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

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}
		
		/*
		$result['labels']=array(
			'title'=>$this->translate('Stand van zaken'),
			'main'=>$this->translate('Aantal soorten in Nederland'),
			'sub'=>$this->translate('Het soortenregister bevat')
		);
		*/

		function format_number($n)
		{
			return number_format($n,0,',','.');
		}


        $d=$this->models->Taxa->freeQuery("
			select
				count(*) as total,
				_h.id as presence_id,
				_h.established as established

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

			group by 
				_h.id
		");


		$result['all']=0;
		$result['all_established']=0;
		$result['established_exotic']=0;
		/*
		6	2a Exoot. Minimaal 100 jaar zelfstandige handhaving
		3	2b Exoot. Tussen 10 en 100 jaar zelfstandige handhaving
		*/
		foreach((array)$d as $key=>$val)
		{
			$result['all']+=$val['total'];

			if ($val['established']=='1')
			{
				$result['all_established']+=$val['total'];
			}

			if ($val['presence_id']==3 || $val['presence_id']==6)
			{
				$result['established_exotic']+=$val['total'];
			}
		}

		$result['all']=format_number($result['all']);
		$result['all_established']=format_number($result['all_established']);
		$result['established_exotic']=format_number($result['established_exotic']);
		$result['main_count']=$result['all_established'];  // backward compat NSR

        $d=$this->models->MediaTaxon->freeQuery("
			select
				count(distinct taxon_id) as total

			from %PRE%media_taxon _a

			where
				_a.project_id = ".$this->getCurrentProjectId()
		);
		
		$result['statistics']['species_with_image']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Soorten met foto\'s')
			);

        $d=$this->models->MediaTaxon->freeQuery("
			select
				count(_m.id) as total

			from
				%PRE%media_taxon _m
			
			left join %PRE%media_meta _meta9
				on _m.id=_meta9.media_id
				and _m.project_id=_meta9.project_id
				and _meta9.sys_label='verspreidingsKaart'

			left join %PRE%taxa _k
				on _m.taxon_id=_k.id
				and _m.project_id=_k.project_id

			left join %PRE%trash_can _trash
				on _k.project_id = _trash.project_id
				and _k.id =  _trash.lng_id
				and _trash.item_type='taxon'
			
			where
				_m.project_id = ".$this->getCurrentProjectId()."
				and ifnull(_meta9.meta_data,0)!=1
				and ifnull(_trash.is_deleted,0)=0
		");
		
		$result['statistics']['images']=
			array(
				'count'=>format_number($d[0]['total']),
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
			/*
			if ($val['language_id']==LANGUAGE_ID_ENGLISH)
				$t['count_name_english']+=$val['total'];
			*/
		}

		/*  https://jira.naturalis.nl/browse/LINNA-834
		$result['statistics']['accepted_names']=
			array(
				'count'=>format_number($t['count_name_accepted']),
				'label'=>$this->translate('Geaccepteerde soortnamen')
			);
		*/

		$result['statistics']['dutch_names']=
			array(
				'count'=>format_number($t['count_name_dutch']),
				'label'=>$this->translate('Nederlandse namen')
			);
		/*
		$result['statistics']['english_names']=
			array(
				'count'=>format_number($t['count_name_english']),
				'label'=>$this->translate('Engelse namen')
			);
		*/
	

		$d=$this->models->Taxa->freeQuery("
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
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Specialisten')
			);

        $d=$this->models->MediaMeta->_get(array(
			'id'=> array(
				'project_id' => $this->getCurrentProjectId(),
				'sys_label' => 'beeldbankFotograaf'
			),
			'columns'=>'count(distinct meta_data) as total'
		));

		$result['statistics']['photographer']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Fotografen')
			);

        $d=$this->models->MediaMeta->_get(array(
			'id'=> array(
				'project_id' => $this->getCurrentProjectId(),
				'sys_label' => 'beeldbankValidator'
			),
			'columns'=>'count(distinct meta_data) as total'
		));

		$result['statistics']['validator']=
			array(
				'count'=>format_number($d[0]['total']),
				'label'=>$this->translate('Validatoren')
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

		$group=1;
		
		$count=$this->models->WebservicesModel->getExoticsPassportCount([
			'project_id'=>$this->getCurrentProjectId(),
			'group_id'=>$group
			]);

		$result['statistics']['exotics']=
			array(
				'count'=>format_number($count),
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

		if (is_null($this->getCurrentProjectId())) {
			$this->sendErrors();
			return;
		}
		if (empty($this->requestData['text'])) {
			$this->addError('no search text.');
			$this->sendErrors();
			return;
		}
		
		$search=$this->requestData['text'];

		if (strlen($search)<$minStrLen) {
			$this->addError('search text too short (min '.$minStrLen.' characters).');
			$this->sendErrors();
			return;
		}

		if (isset($this->requestData['start']) && $this->requestData['start']=='1')
			$this->setMatchType('match_start');

		$max=
			isset($this->requestData['max']) && 
			is_numeric($this->requestData['max']) &&
			(int)$this->requestData['max']>0  &&
			(int)$this->requestData['max']<=1000  ? 
				(int)$this->requestData['max'] : 
				$max;

		$taxa=$this->models->Taxa->freeQuery("
			select
				_a.name,
				_b.nametype,
				_e.taxon,
				_q.label as common_rank,
				replace(_r.nsr_id,'tn.nlsr.concept/','') as nsr_id,
				_d.label as language_label
			
			from %PRE%names _a

			left join %PRE%trash_can as _del 
				on _a.taxon_id = _del.lng_id 
				and _a.project_id = _del.project_id 
				and _del.item_type = 'taxon'

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _d.label_language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%taxa _e
				on _a.taxon_id = _e.id
				and _a.project_id = _e.project_id
				
			left join %PRE%projects_ranks _f
				on _e.rank_id=_f.id
				and _a.project_id = _f.project_id

			left join %PRE%labels_projects_ranks _q
				on _e.rank_id=_q.project_rank_id
				and _a.project_id = _q.project_id
				and _q.language_id=".$this->getCurrentLanguageId()."
			
			left join %PRE%name_types _b 
				on _a.type_id=_b.id 
				and _a.project_id = _b.project_id

			left join %PRE%nsr_ids _r
				on _a.project_id = _r.project_id
				and _a.taxon_id=_r.lng_id
				and _r.item_type = 'taxon'

			where _a.project_id =".$this->getCurrentProjectId()." 
				and _del.is_deleted is null 
			". ($this->getMatchType()=='match_start' ? 
					"and _a.name like '".$this->models->Taxa->escapeString($search)."%'" :
					"and _a.name like '%".$this->models->Taxa->escapeString($search)."%'" 
			)."
				and (_b.nametype='".PREDICATE_PREFERRED_NAME."' or _b.nametype='".PREDICATE_VALID_NAME."' or _b.nametype='".PREDICATE_ALTERNATIVE_NAME."')
			
			order by _a.name
			limit ".$max
		);

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

	public function imagesAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&size=<int>
parameters:
  pid".chr(9)." : project id (mandatory)
";

		if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		$result=array('pId'=>$this->getCurrentProjectId());

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['exported']=date('c');

		$result['url_recent_images']='https://'.$this->_domainNamePatch.'/linnaeus_ng/app/views/search/nsr_recent_pictures.php';

        $media=$this->models->MediaTaxon->freeQuery("
			select
				_a.taxon_id, 
				count(_a.id) as n,
				_m1.meta_data as photographer
	
			from
				%PRE%media_taxon _a

			left join %PRE%taxa _d 
				on _a.taxon_id=_d.id 
				and _a.project_id=_d.project_id

			left join %PRE%projects_ranks _e 
				on _d.rank_id=_e.id 
				and _a.project_id=_d.project_id

			left join %PRE%media_meta _m1
				on _a.id=_m1.media_id 
				and _a.project_id=_m1.project_id 
				and _m1.sys_label = 'beeldbankFotograaf'		

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _e.rank_id >= ".SPECIES_RANK_ID."

			group by
				taxon_id
		");
		
	
		$result['total_images']=0;
		$result['species_with_images']=count((array)$media);
		$d=array();
		foreach((array)$media as $val)
		{
			$result['total_images']+=$val['n'];
			$d[$val['photographer']]=isset($d[$val['photographer']]) ? $d[$val['photographer']] + $val['n'] : $val['n'];
		}
		$result['total_photographers']=count((array)$d);
		
		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function getMediaAction()
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&file=<original filename>
parameters:
  pid".chr(9)." : project id (mandatory)
  file".chr(9)." : original filename
";

		if ( is_null($this->getCurrentProjectId()) )
		{
			$this->sendErrors();
			return;
		}

		$file=$this->rGetVal('file');

		if ( empty($file) )
		{
			$this->addError('no filename');
			$this->sendErrors();
			return;
		}

		$files=$this->models->Media->_get( [ "id" => 
			[
				"project_id"=>$this->getCurrentProjectId(),
				"name"=>$this->models->Media->escapeString($file)
			],
			"columns"=>"name as original_filename,rs_original as url"
		] );

		$p=$this->getProject();

		$result['project']=$p['title'];
		$result['results']=$files;

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

	public function taxonomyAction()	
	{
		$this->_usage=
"url: http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?pid=<id>&taxon=<scientific name>
parameters:
  pid".chr(9)." : project id (mandatory)
  taxon".chr(9)." : scientific name or id of the taxon to retrieve (mandatory)
";

	    if (is_null($this->getCurrentProjectId()))
		{
			$this->sendErrors();
			return;
		}

		$this->resolveTaxonName();
		
		if (is_null($this->getTaxonId()))
		{
			$this->sendErrors();
			return;
		}

 		$taxon=$this->getTaxonById($this->getTaxonId());
		$parent=$this->getTaxonById($taxon['parent_id']);

		$taxon['label']=$this->formatTaxon($taxon);
		$parent['label']=$this->formatTaxon($parent);

		$data = $this->models->WebservicesModel->getTreeBranch( [
			'project_id' => $this->getCurrentProjectId(),
			'node' =>  $this->getTaxonId()
		] );

        $parentIds = $this->models->WebservicesModel->getTaxonParentage([
            'projectId' => $this->getCurrentProjectId(),
            'taxonId' => $this->getTaxonId(),
        ]);

        foreach ($parentIds as $id) {
            $par = $this->getTaxonById((int)$id);
            $class = ['taxon'=> $par['taxon'],'rank'=> $par['rank']];
            if (isset($par['authorship']))
            {
                  $class['authorship'] = $par['authorship'];
            }
            $classification[] = $class;
        }
        
        $class = ['taxon'=>$taxon['taxon'],'rank'=>$taxon['rank']];

        if (isset($taxon['authorship']))
        {
            $class['authorship'] = $taxon['authorship'];
        }

        $classification[] = $class;

        $p=$this->getProject();
		$result['project']=$p['title'];
		$result['taxon']=['taxon'=>$taxon['taxon'],'rank'=>$taxon['rank']];
		$result['parent']=['taxon'=>$parent['taxon'],'rank'=>$parent['rank']];
        $result['classification'] = $classification;
        $result['children']=$data;

		$this->setJSON(json_encode($result));
		header('Content-Type: application/json');			
		$this->printOutput();
	}

    private function initialise()
    {
        $this->Rdf = new RdfController(array('checkForProjectId'=>false));
		$this->moduleSettings=new ModuleSettingsReaderController(array('checkForProjectId'=>false));

		$this->useCache=false;
		$this->checkProject();

        if (is_null($this->getCurrentProjectId()))
		{
			$this->addError('cannot get project settings.');
		} 
		else 
		{
			$this->setProjectLanguages();
 		    $this->models->Taxa->freeQuery("SET lc_time_names = '".$this->moduleSettings->getGeneralSetting( array( 'setting'=>'db_lc_time_names','subst'=>'nl_NL'))."'");
			$this->checkJSONPCallback();
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
		if (!$this->rHasVal('from'))
		{
			$this->addError('no starttime specified of retrieval window.');
		} 
		else 
		{
			$d=str_split($this->requestData['from'],2);

			if (strlen($this->requestData['from'])==8 && checkdate($d[2],$d[3],$d[0].$d[1]))
			{
				$this->setFromDate($this->requestData['from']);
				return true;
			}
			else 
			{
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

	private function resolveTaxonName()
	{
		if (!$this->rHasVal('taxon'))
		{
			$this->addError('no taxon name specified.');
		} 
		else {
            // Sneakily also accept id as input ;)
            if (is_numeric($this->requestData['taxon'])) {

                $t[0]['id'] = $this->requestData['taxon'];

            } else {

                $taxon = trim(strip_tags($this->requestData['taxon']));

                $this->setMatchType('literal');

                $t = $this->models->Taxa->_get(array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'taxon' => $taxon
                    )
                ));
            }

			if ($t) $nametype='isValidNameOf';

			if (!$t)
			{
				$this->setMatchType('valid name without authorship');

				$t = $this->models->Names->freeQuery("
					select
						_a.taxon_id as id, _a.name, _b.nametype
					from %PRE%names _a
					left join %PRE%name_types _b 
						on _a.type_id=_b.id and _a.project_id=_b.project_id
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and trim(REPLACE(_a.name,_a.authorship,''))='". $this->models->Names->escapeString($taxon) ."'
						and _b.nametype = 'isValidNameOf'"
				);
				
				if ($t) $nametype='isValidNameOf';
			}

			if (!$t)
			{
				$this->setMatchType('other name type (literal)');

				$t = $this->models->Names->freeQuery("
					select
						_a.taxon_id as id, _a.name, _b.nametype
					from %PRE%names _a
					left join %PRE%name_types _b 
						on _a.type_id=_b.id and _a.project_id=_b.project_id
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.name='". $this->models->Names->escapeString($taxon) ."'
						and _b.nametype != 'isValidNameOf'"
				);

				if ($t) $nametype=$t[0]['nametype'];
			}

			if (!$t)
			{
				$this->setMatchType('other name type (without authorship)');

				$t = $this->models->Names->freeQuery("
					select
						_a.taxon_id as id, _a.name, _b.nametype
					from %PRE%names _a
					left join %PRE%name_types _b 
						on _a.type_id=_b.id and _a.project_id=_b.project_id
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and trim(REPLACE(_a.name,_a.authorship,''))='". $this->models->Names->escapeString($taxon) ."'
						and _b.nametype != 'isValidNameOf'"
				);

				if ($t) $nametype=$t[0]['nametype'];
			}
		
			if (!$t)
			{
				$this->setMatchType('other name type (literal)');

				$t = $this->models->Names->freeQuery("
					select
						_a.taxon_id as id, _a.name
					from %PRE%names _a
					left join %PRE%name_types _b 
						on _a.type_id=_b.id and _a.project_id=_b.project_id
					where
						_a.project_id = ".$this->getCurrentProjectId()."
						and _a.name='". $this->models->Names->escapeString($taxon) ."'
						and _b.nametype != 'isValidNameOf'"
				);
			}
		
			if (!$t)
			{
				$this->addError('taxon name "'.$this->rGetVal('taxon').'" not found in this project.');
				$nametype=null;
			} 
			else
			{
				$this->setTaxonId($t[0]['id']);
				
				$t = $this->models->Taxa->_get(array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'id' => $this->getTaxonId()
					)
				));

				$t[0]['nametype']=strtolower(rtrim(ltrim($nametype,'is'),'Of'));

				$this->setTaxon($t[0]);
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
			$nsr=trim($this->requestData['nsr']);
			
			$this->setMatchType('literal');

			$t = $this->models->WebservicesModel->getNsrId(array(
				'project_id' => $this->getCurrentProjectId(),
				'nsr_id' => $nsr,
				'item_type' => 'taxon'
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

	private function setTaxonId($id)
	{
		$this->_taxonId=$id;
	}

	private function getTaxonId()
	{
		return $this->_taxonId;
	}
	
	private function setTaxon($taxon)
	{
		$this->_taxon=$taxon;
	}

	private function getTaxon()
	{
		return $this->_taxon;
	}	

	private function setMatchType($t)
	{
		$this->_matchType=$t;
	}

	private function getMatchType()
	{
		return $this->_matchType;
	}

	private function setJSON($json)
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
	
	private function setJSONPCallback($callback)
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

	private function printOutput($suppressJSONP=false, $caching = 0)
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

		$this->smarty->caching = $caching;
		$this->smarty->assign('json',$this->_JSON, true);

		header('Content-Type: application/json');			
		$this->printPage('template');
	}

   /*
    * Copied from Controller. Some settings aren't available when the webservice is
    * called outside the scope of a Linnaeus project
    */
    public function getTaxonById ($id, $formatTaxon = true)
    {
        if (empty($id) || !is_numeric($id) || $id==0) {
            return;
        }

        return $this->models->ControllerModel->getTaxonById(array(
            'trashCanExists' => $this->models->TrashCan->getTableExists(),
            'projectId' => $this->getCurrentProjectId(),
            'languageId'=>LANGUAGE_ID_DUTCH,
            'taxonId' => $id,
            'predicateValidNameId' => 1,
            'predicatePreferredNameId' => 7,
            'scientificLanguageId' => LANGUAGE_ID_SCIENTIFIC,
        ));
    }
}
