<?php

/*

 DON'T HAVE SPECIES YET!

*/


include_once ('Controller.php');

class IndexController extends Controller
{
    
    public $usedModels = array(
		'glossary',
		'glossary_synonym',
		'literature',
		'content_free_module',
		'free_module_project'
    );
    
    public $controllerPublicName = 'Index';


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        
        parent::__construct();

    }


    /**
     * Destroys!
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();
    
    }


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;

        if ($this->rHasVal('action','get_lookup_list') && !empty($this->requestData['search'])) {

            $this->getLookupList($this->requestData['search']);

        }
		
        $this->printPage();
    
    }


	private function getLookupList($search)
	{
	
		/*
			excluded:
			- Introduction
			- Dichotomous key
			- Matrix key 
			- Map key
		
		*/

		$g = $this->getGlossaryLookupList($search);
		$l = $this->getLiteratureLookupList($search);
		$s = $this->getSpeciesLookupList($search);
		$m = $this->getModuleLookupList($search);

		$this->smarty->assign(
			'returnText',
			$this->makeLookupList(
				array_merge(
					(array)$g,
					(array)$l,
					(array)$s,
					(array)$m
				),
				$this->controllerBaseName,
				null,
				true
			)
		);	

/*
Species module
	taxn
	syn
	common
Higher taxa 		
	taxn
	syn
	common
*/		
	}


	private function getGlossaryLookupList($search)
	{

		if (empty($search)) return;

		$l1 = $this->models->Glossary->_get(
			array(
				'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'term like' => '%'.$search.'%'
					),
				'columns' => '
					id,
					term as label,
					"glossary" as source,
					concat("views/glossary/edit.php?id=",id) as url'
			)
		);

		$l2 = $this->models->GlossarySynonym->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'synonym like' => '%'.$search.'%'
					),
				'columns' => '
					glossary_id as id,
					synonym as label,
					"glossary synonym" as source,
					concat("views/glossary/edit.php?id=",glossary_id) as url'
			)
		);

		return array_merge((array)$l1,(array)$l2);

	}


	private function getLiteratureLookupList($search)
	{

		if (empty($search)) return;

		$l = $this->models->Literature->_get(
			array('id' =>
				'select
					id, 
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						),
						\' (\',
						year(`year`),
						(
							if(isnull(suffix)!=1,
									suffix,
									\'\'
								)
						),
						\')\'
					) as label,
					lower(author_first) as _a1,
					lower(author_second) as _a2,
					`year`,
					"literature" as source,
					concat("views/literature/edit.php?id=",id) as url
				from %table%
				where
					(author_first like "%'.mysql_real_escape_string($search).'%" or
					author_second like "%'.mysql_real_escape_string($search).'%" or
					`year` like "%'.mysql_real_escape_string($search).'%")
					and project_id = '.$this->getCurrentProjectId().'
				order by _a1,_a2,`year`'
			)
		);

		return $l;

	}

	private function getSpeciesLookupList($search)
	{

	}
		
	private function getModuleLookupList($search)
	{

		if (empty($search)) return;

		$fmp = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
				),
				'fieldAsIndex' => 'id',
				'columns' => 'id, module'
			)
		);

		$cfm = $this->models->ContentFreeModule->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'topic like' => '%'.$search.'%'
					),
				'order' => 'topic',
				'columns' => 'distinct page_id as id, topic as label,
					concat("views/module/index.php?page=",page_id,"&freeId=",module_id) as url,
					module_id'
			)
		);
		
		foreach((array)$cfm as $key => $val) {

			$cfm[$key]['source'] = $fmp[$val['module_id']]['module'].' topic';

		}

		return $cfm;

	}
	
}
