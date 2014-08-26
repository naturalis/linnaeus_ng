<?php

include_once ('Controller.php');

class Literature2Controller extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'literature_taxon',
    );
   
    public $controllerPublicName = 'References (v2)';

    public $cacheFiles = array(
    );
    
    public $cssToLoad = array('lookup.css','taxonomy.css');

	public $jsToLoad =
		array(
			'all' => array('lookup.js')
		);


    public function __construct ()
    {
        parent::__construct();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }


    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Find reference'));
		$this->smarty->assign('authorAlphabet',$this->getAuthorAlphabet());
		$this->smarty->assign('titleAlphabet',$this->getTitleAlphabet());
		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!isset($this->requestData['action'])) return;
        
		$return=null;

		if (
			$this->rHasVal('action', 'reference_lookup') ||
			$this->rHasVal('action', 'name_reference_id') ||
			$this->rHasVal('action', 'dutch_name_reference_id') ||
			$this->rHasVal('action', 'presence_reference_id')
		)
		{
            $return=$this->getReferenceLookupList($this->requestData);
        }
       
        $this->allowEditPageOverlay = false;

		$this->smarty->assign('returnText',$return);

        $this->printPage();
    }


    public function editAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Edit reference'));
		$this->smarty->assign('reference',$this->getReference($this->rGetId()));
		$this->printPage();
	}

    private function getReferenceLookupList($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

        $searchTitle=isset($p['search_title']) ? $p['search_title'] : null;
        $searchAuthor=isset($p['search_author']) ? $p['search_author'] : null;

        if (empty($search) && empty($searchTitle) && empty($searchAuthor))
            return;

		$data=$this->models->Literature2->freeQuery(
			"select
				_a.id,
				_a.language_id,
				_a.label,
				_a.date,
				ifnull(_a.author,ifnull(_e.name,'-')) as author,
				_a.publication_type,
				_a.citation,
				_a.source,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link
				
			from %PRE%literature2 _a

			left join %PRE%actors _e
				on _a.actor_id = _e.id 
				and _a.project_id=_e.project_id

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()."

			". (!empty($search) ? "	
					and (
						_a.label like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
						_a.author like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%' or
						_e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($search)."%'
					)" : "")."

			". (!empty($searchTitle) ? "	
					and (
						_a.label like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchTitle)."%'
					)" : "")."

			". (!empty($searchAuthor) ? "	
					and (
						_a.author like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchAuthor)."%' or
						_e.name like '".($matchStartOnly ? '':'%').mysql_real_escape_string($searchAuthor)."%'
					)" : "")."

			order by ".
				(!empty($search) || !empty($searchTitle) ? "_a.label, _a.author " : "" ).
				(!empty($searchAuthor) ? "_a.author, _a.label " : "" )
			);	

		return
			$this->makeLookupList(
				$data,
				'reference',
				'reference.php?id=%s',
				false,
				true,
				count($data)<$maxResults
			);

    }




	private function getTitleAlphabet()
	{
		$alpha=$this->models->Literature2->freeQuery("
			select
				distinct if(ord(substr(lower(_a.label),1,1))<97||ord(substr(lower(_a.label),1,1))>122,'#',substr(lower(_a.label),1,1)) as letter
			from			
				%PRE%literature2 _a
			where
				_a.project_id = ".$this->getCurrentProjectId()."
			");

		return $alpha;
	}

	private function getAuthorAlphabet()
	{
		$alpha=$this->models->Literature2->freeQuery("
			select distinct * from (
				select
					distinct if(ord(substr(lower(_a.author),1,1))<97||ord(substr(lower(_a.author),1,1))>122,'#',substr(lower(_a.author),1,1)) as letter
				from			
					%PRE%literature2 _a
				where
					_a.project_id = ".$this->getCurrentProjectId()."
			union
				select
					distinct if(ord(substr(lower(_f.name),1,1))<97||ord(substr(lower(_f.name),1,1))>122,'#',substr(lower(_f.name),1,1)) as letter

				from			
					%PRE%literature2 _a

				left join %PRE%actors _f
					on _a.actor_id = _f.id 
					and _a.project_id=_f.project_id		

				where
					_a.project_id = ".$this->getCurrentProjectId()."
			) as unification
			order by letter
		");

		return $alpha;
	}

	private function getLookupList($p)
	{
		$search = isset($p['search']) ? $p['search'] : null;
		$get_all = isset($p['get_all']) ? $p['get_all'] : null;
		$match_start = isset($p['match_start']) ? $p['match_start'] : false;
		$max_results = isset($p['max_results']) ? $p['max_results'] : true;
		$action = $p['action'];
		/*
		lookup_title_letter
		lookup_title
		lookup_author_letter
		lookup_author
		*/

		if (empty($search))
			return;
			
		$label="concat(_a.label,if(ifnull(_a.author,_f.name) is null,'',concat(' (',ifnull(_a.author,_f.name),')')))";
		$where=null;
		$order="_a.label";
		if ($search!='*')
		{
			if ($action=='lookup_author' || $action=='lookup_author_letter')
			{
				$order="ifnull(_a.author,_f.name)";
				$label="concat(if(ifnull(_a.author,_f.name) is null,'',concat(ifnull(_a.author,_f.name),if(_a.label is null,'',': '))),_a.label)";

				if ($action=='lookup_author_letter')
					$where=" and (_a.author like '".$search."%' or _f.name like '".$search."%')";
				else
					$where=" and (_a.author like '%".$search."%' or _f.name like '%".$search."%')";
			}
			else
			if ($action=='lookup_title' || $action=='lookup_title_letter')
			{
				if ($action=='lookup_title_letter')
					$where=" and _a.label like '".$search."%'";
				else
					$where=" and _a.label like '%".$search."%'";
				
			}
		}
		
		$l = $this->models->Literature2->freeQuery("
			select
				_a.id,
				".$label." as label
			from			
				%PRE%literature2 _a

			left join %PRE%actors _f
				on _a.actor_id = _f.id 
				and _a.project_id=_f.project_id

			left join %PRE%literature2 _g
				on _a.publishedin_id = _g.id 
				and _a.project_id=_g.project_id
	
			left join %PRE%literature2 _h
				on _a.periodical_id = _h.id 
				and _a.project_id=_h.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()."
			".$where."
			order by ".$order."
			");

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$l,
				$this->controllerBaseName,
				'../literature2/edit.php?id=%s'
			)
		);
		
	}

	private function getReference($id)
	{
		if (empty($id))
			return;

		$l=$this->models->Literature2->_get(array('id'=>
			array(
				'project_id'=>$this->getCurrentProjectId(),
				'id'=>$id
				)
			)
		);
		
		if ($l)
			return $l[0];

	}



	
}
