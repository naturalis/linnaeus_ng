<?php


/*

	at some point column literature2.actor_id can be dropped; now replaced with literature2_authors

*/


include_once ('Controller.php');

class Literature2Controller extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'literature2_authors'
    );
   
    public $controllerPublicName = 'Literary references';

	public $cssToLoad = array(
		'literature.css'
	);

	public $jsToLoad =
		array(
			'all' => array(
				'main.js',
				'lookup.js',
				'literature2.js',
				'dialog/jquery.modaldialog.js'
//				'tablesorter/jquery-latest.js',
//				'tablesorter/jquery.tablesorter.js',
			),
			'IE' => array(
			)
		);

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

    public function indexAction()
    {
		$this->smarty->assign( 'authorAlphabet', $this->getAuthorAlphabet() );
		$this->smarty->assign( 'titleAlphabet', $this->getTitleAlphabet() );
        $this->printPage();
    }

    public function referenceAction()
    {
		if (!$this->rHasId()) $this->redirect('index.php');

		$ref=$this->getReference( $this->rGetId() );

		//$this->setPageName($ref['label'].', '.$ref['source']);
		$this->setPageName($ref['label']);

		$this->smarty->assign( 'ref', $ref );
		$this->smarty->assign( 'taxa', $this->getReferencedTaxa( $this->rGetId() ) );

        $this->printPage();
    }

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action')) return;
		$return=null;
		$return=$this->getReferenceLookupList($this->rGetAll());
		$this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
    }


	private function getReference($id)
	{
		return $this->models->Literature2Model->getReference(array(
			"project_id"=>$this->getCurrentProjectId(),
			"language_id"=>$this->getCurrentLanguageId(),
			"literature2_id"=>$id
		));
	}

	private function getReferencedTaxa( $id )
	{
		return $this->models->Literature2Model->getReferencedTaxa(array(
            'project_id' => $this->getCurrentProjectId(),
    		'literature_id' => $id
		));
	}

	private function getTitleAlphabet()
	{
		return $this->models->Literature2Model->getTitleAlphabet( array( 'project_id'=>$this->getCurrentProjectId() ) );
	}

	private function getAuthorAlphabet()
	{
		return $this->models->Literature2Model->getAuthorAlphabet(array( 'project_id'=>$this->getCurrentProjectId() ) );
	}

	private function getReferenceAuthors( $id )
	{
		return $this->models->Literature2Model->getReferenceAuthors(array(
            'projectId' => $this->getCurrentProjectId(),
    		'literatureId' => $id
		));
	}

    private function getReferences($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $searchTitle=isset($p['search_title']) ? $p['search_title'] : null;
        $searchAuthor=isset($p['search_author']) ? $p['search_author'] : null;
        $publication_type_id=isset($p['publication_type']) ? $p['publication_type'] : null;

        if (empty($search) && empty($searchTitle) && empty($searchAuthor))
            return;

		if (empty($search) && $matchStartOnly && ($searchTitle=='#' || $searchAuthor=='#'))
		{
			$fetchNonAlpha=true;
		}
		else
		{
			$fetchNonAlpha=false;
		}

		$all = $this->models->Literature2Model->getReferences(array(
            'projectId' => $this->getCurrentProjectId(),
    		'publicationTypeId' => $publication_type_id
		));

		$data=array();

		foreach((array)$all as $key => $val)
		{
			$org=$val;
			$val['label']=preg_replace('/[^A-Za-z0-9]/','',strip_tags($val['label']));

			$authors=$this->getReferenceAuthors($val['id']);

			$tempauthors='';
			if ($authors)
			{
				foreach((array)$authors as $author)
				{
					$tempauthors.=$author['name'].', ';
				}
			}

			$match=false;

			if(!empty($search) && $search=='*')
			{
				$match=true;
			}
			else
			if(!empty($search))
			{
				if ($matchStartOnly)
				{
					$match=$match ? true : (stripos($val['label'],$search)===0);

					if (!$match)
						$match=$match ? true : (stripos($tempauthors,$search)===0);

					if (!$match)
						$match=$match ? true : (stripos($val['author'],$search)===0);
				}
				else
				{
					$match=$match ? true : (stripos($val['label'],$search)!==false);

					if (!$match)
						$match=$match ? true : (stripos($tempauthors,$search)!==false);

					if (!$match)
						$match=$match ? true : (stripos($val['author'],$search)!==false);
				}

			}
			else
			if(!empty($searchTitle))
			{
				if ($fetchNonAlpha)
				{
					$startLetterOrd=ord(substr(strtolower($val['label']),0,1));
					$match=$match ? true : ($startLetterOrd<97 || $startLetterOrd>122);
				}
				else
				{
					if ($matchStartOnly)
						$match=$match ? true : (stripos($val['label'],$searchTitle)===0);
					else
					{
						$match=$match ? true : (stripos($val['label'],$searchTitle)!==false);
					}
				}

			}
			else
			if (!empty($searchAuthor))
			{
				if ($matchStartOnly)
				{
					$match=$match ? true : (stripos($tempauthors,$searchAuthor)===0);
					if (!$match)
						$match=$match ? true : (stripos($val['author'],$searchAuthor)===0);
				}
				else
				{
					$match=$match ? true : (stripos($tempauthors,$searchAuthor)!==false);
					if (!$match)
						$match=$match ? true : (stripos($val['author'],$searchAuthor)!==false);
				}

				if (!$match)
				{
					if ($fetchNonAlpha)
					{
						$startLetterOrd=ord(substr(strtolower($tempauthors),0,1));
						$match=$match ? true : ($startLetterOrd<97 || $startLetterOrd>122);
						if (!$match)
						{
							$startLetterOrd=ord(substr(strtolower($val['author']),0,1));
							$match=$match ? true : ($startLetterOrd<97 || $startLetterOrd>122);
						}
					}
					else
					{
						if ($matchStartOnly)
						{
							$match=$match ? true : (stripos($tempauthors,$searchAuthor)===0);
							if (!$match)
								$match=$match ? true : (stripos($val['author'],$searchAuthor)===0);
						}
						else
						{
							$match=$match ? true : (stripos($tempauthors,$searchAuthor)!==false);
							if (!$match)
								$match=$match ? true : (stripos($val['author'],$searchAuthor)!==false);
						}
					}
				}
			}

			if ($match)
			{
				$val['authors']=$authors;
				$data[]=$org;//$val;
			}

		}

		if (!empty($searchAuthor))
		{
			usort($data,function($a,$b)
			{
				$aa=isset($a['authors'][0]['name']) ? $a['authors'][0]['name'] : $a['author'];
				$bb=isset($b['authors'][0]['name']) ? $b['authors'][0]['name'] : $b['author'];
				
				if (strtolower($aa)==strtolower($bb))
				{
					return strtolower($a['label'])>strtolower($b['label']);
				}

				return strtolower($aa)>strtolower($bb);
			});
		}
		else
		{
			usort($data,function($a,$b){ return strtolower($a['label'])>strtolower($b['label']); });
		}

		return $data;

	}

    private function getReferenceLookupList($p)
    {
		$data=$this->getReferences($p);
		
        $maxResults=isset($p['max_results']) && (int)$p['max_results']>0 ? (int)$p['max_results'] : $this->_lookupListMaxResults;

		return
			$this->makeLookupList(array(
				'data'=>$data,
				'module'=>'reference',
				'reference.php?id=%s',
				'encode'=>true,
				'isFullSet'=>count($data)<$maxResults
			));
    }



}

