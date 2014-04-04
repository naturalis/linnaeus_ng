<?php

	/*

	the content for 'About ETI' is the same for all projects. it is stored in the same table 
	as the project specific content, using project ID=-10 (defined in the configuration file).
	it needs to be available in the same language(s) that the project uses in order for
	it to be displayed.
	there is no editing interface for the 'About ETI' content. it should be edited directly
	in the database (SELECT * FROM dev_content WHERE project_id = -10)
	(dutch = 24, english = 26)

	*/

include_once ('Controller.php');

class LinnaeusController extends Controller
{

    public $usedModels = array(
		'content',
        'content_taxon', 
		'content_free_module',

/*
        'page_taxon', 
        'page_taxon_title', 
        'media_taxon',
        'media_descriptions_taxon',
		'synonym',
		'commonname',
		'content_introduction',
		'literature',
		
		'choice_content_keystep',
		'content_keystep',
		'choice_keystep',
		'keystep',
		'literature',
		'glossary_media',
		'matrix',
		'matrix_name',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_label',
		'characteristic_label_state',
		'characteristic_matrix',
		'characteristic_label_state',
		'characteristic_state',
		'occurrence_taxon',
		'names'
*/	
		    );

    public $usedHelpers = array(
    );

	public $cssToLoad = array(
	    'linnaeus.css'
	);

	public $jsToLoad = array('all' => array(
		'main.js',
		'lookup.js',
		'dialog/jquery.modaldialog.js'
	));
	
	public $controllerBaseName = 'linnaeus';
		
    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct($p=null)
    {

        parent::__construct($p);

    }

    /**
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    /**
     * Set the active project ID
     *
     * @access    public
     */
    public function setProjectAction ()
    {

		unset($_SESSION['app']['project']);
		unset($_SESSION['app']['user']);

		$this->resolveProjectId();

		if (!$this->getCurrentProjectId()) {

			$this->addError($this->translate('Unknown project or invalid project ID.'));

	        $this->printPage();

		} else {

	        $this->setUrls();

			$this->setCurrentProjectData();
			
			$this->setCssFiles();
		
			if ($this->rHasVal('r')) {

				$url = $this->requestData['r'];

			} else {
				
				$url = $this->getSetting('start_page');
				
				if (empty($url))
					$url = 'index.php';

			}
			
			$this->redirect($url);

		}

    }


    /**
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function indexAction ()
    {

		if ($this->rHasVal('show','icongrid')) {

			$this->printPage();

		} else {

			$this->setStoreHistory(false);

			$this->redirect('../linnaeus/content.php?sub=Welcome');
		
		}

    }

    /**
     * Index of project: introduction (or other content pages)
     *
     * @access    public
     */
    public function contentAction ()
    {
		if (!$this->rHasVal('sub') && !$this->rHasVal('id')) {

			$d = $this->getContent('Welcome');

		} else {
		
			$d = $this->getContent(
				(isset($this->requestData['sub']) ? $this->requestData['sub'] : null),
				(isset($this->requestData['id']) ? $this->requestData['id'] : null)
			);

		}

		$this->setPageName($this->translate($d['subject']));

		$this->smarty->assign('subject',$this->matchHotwords($this->matchGlossaryTerms($d['subject'])));
		$this->smarty->assign('content',$this->matchHotwords($this->matchGlossaryTerms($d['content'])));
		//$this->smarty->assign('subject',$d['subject']);
		//$this->smarty->assign('content',$d['content']);

        $this->printPage();
  
    }
	
    public function rootIndexAction()
	{

		if (defined('FIXED_PROJECT_ID'))
			$this->redirect('app/views/linnaeus/set_project.php?p='.FIXED_PROJECT_ID);

		$id = $this->resolveProjectShortName();
		
		if ($id)
			$this->redirect('app/views/linnaeus/set_project.php?p='.$id);
		
		$projects = $this->models->Project->_get(
			array(
				'id' => array('published' => 1),
				'order' => 'title'
			)
		);

		if ($this->rHasVar('nopid'))
			$this->smarty->assign('error',$this->translate('No or illegal project ID specified.'));
		$this->smarty->assign('hasEntryProgram',$this->doesEntryProgramExist());
		$this->smarty->assign('showEntryProgramLink',$this->generalSettings['showEntryProgramLink']);
		$this->smarty->assign('projects',$projects);
		$this->smarty->assign('excludeLogout',true);

        $this->printPage('root_index');
	
	}

    public function noProjectAction()
	{

		$this->redirect($this->baseUrl.'?nopid');
		/*
		$projects = $this->models->Project->_get(array('id' => array('published' => 1)));

		$this->smarty->assign('excludeLogout',true);

        $this->printPage();
		*/
	
	}

	public function getContent($sub=null,$id=null)
	{

		// see note at top of file
		if ($sub==$this->controllerSettings['contentAboutETI']['sub']) {

			$d = array(
				'project_id' => $this->controllerSettings['contentAboutETI']['projectID'],
				'language_id' => $this->getCurrentLanguageId()
			);

		} else {

			$d = array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->getCurrentLanguageId()
			);
			
			if ($id!=null) $d['id'] = $id;
			elseif ($sub!=null) $d['subject'] = $sub;
			else return;
		
		}
		
		$c = $this->models->Content->_get(array('id' => $d));

		return isset($c[0]) ? $c[0] : null;
	
	}

	private function resolveProjectShortName()
	{
		$path = explode('/',$this->getfullPath());
		$n = strtolower($path[1]);

		if ($n!=$this->getAppName() && $n!='linnaeus_ng') {

            $p = $this->models->Project->_get(array('id'=>array('short_name !='=>'null'),'columns'=>'id,short_name'));

			if ($p) {
				foreach((array)$p as $val) {
					if (empty($val['short_name']))
						continue;
					$d=explode(';',$val['short_name']);
					array_walk($d,function(&$a,$b){$a=trim($a);});
					if (in_array($n,$d)) {
						return $val['id'];
						exit;
					}
				}
			}


		}
		
		return null;
		
	}

    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData);

        }

		$this->allowEditPageOverlay = false;
		
        $this->printPage();
    
    }

	// general
	public function getLookupList($p)
	{
		
		$search=isset($p['search']) ? $p['search'] : null;
		$match_start=isset($p['match_start']) ? $p['match_start']==1 : false;

		$data=$this->models->Taxon->freeQuery("
			select * from
			(
				select
					id,taxon as label,'species' as source, concat('../species/taxon.php?id=',id) as url, rank_id
				from
					%PRE%taxa
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and taxon like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union
			
				select
					id,concat(
						author_first,
						if(multiple_authors=1,
							' et al.',
							if(author_second!='',concat(' & ',author_second),'')
						),
						', ',
						year,
						ifnull(suffix,'')
					) as label,'literature' as source, concat('../literature/reference.php?id=',id) as url, null as rank_id
				from %PRE%literature
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "
						and (
							author_first like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%' or
							author_second like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%' or
							year like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'
						)
					")."
					
			union

				select 
					id,topic as label,'introduction' as source, concat('../introduction/topic.php?id=',id) as url, null as rank_id
				from
					%PRE%content_introduction
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and topic like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."


			) as unification
			order by label
			limit 100
		");
		
		foreach((array)$data as $key=>$val)
		{
			if ($val['source']=='species')
				$taxa[$key]['label']=$this->formatTaxon(array('taxon'=>$val['label'],'rank_id'=>$val['rank_id'],'rankpos'=>'post'));
		}
		
		
/*

			union
	
				select
					id,term as label,'glossary' as source, concat('../glossary/term.php?id=',id) as url
				from
					%PRE%glossary
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and term like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union
	
				select
					taxon_id as id,commonname as label,'species' as source, concat('../species/taxon.php?cat=names&id=',taxon_id) as url
				from
					%PRE%commonnames
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and commonname like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union
	
				select
					taxon_id as id,synonym as label,'species' as source, concat('../species/taxon.php?cat=names&id=',taxon_id) as url
				from
					%PRE%synonyms
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and synonym like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

			union
	
				select
					glossary_id as id,synonym as label,'glossary' as source, concat('../glossary/term.php?id=',glossary_id) as url
				from
					%PRE%glossary_synonyms
				where
					project_id = ".$this->getCurrentProjectId() ."
					".($search=='*' ? "" : "and synonym like '".(!$match_start ? '%' : ''). mysql_real_escape_string($search)."%'" )."

*/
		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				$data,
				$this->controllerBaseName,
				null,
				true
			)
		); 
			
	}


	

}
