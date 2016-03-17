<?php

/*
	$params=array(
		'sci_name'=>'taxon'
	);
	
	$bla=array(
		'url' => 'https://leiden.maps.arcgis.com/apps/Embed/index.html?webmap=137d9ed9a52149d7b6b48fa722421dc1&extent=-27.631,-5.2922,39.2762,25.2059&home=true&zoom=true&scale=true&search=true&searchextent=false&legend=true&disable_scroll=false&theme=dark',
		'parameters' => $params,
		'link_embed' => 'embed',
		'check_type' => 'none',
		'query' => 'select 1 as result',
		'template' => '_verspreiding.tpl',
	);

	echo json_encode($bla);

	die();

*/
/*
	'url' => URL (as is)
	'parameters' => array of dynamic params (see below)
	'link_embed' => embed / link (*) / link_new   [ embedded, link (same window), link (new window) ]
	'check_type' => none (*) / query
	'query' => 'select 1 as show', (should return one row with one field called 'show' with value 1 or 0)
	'template' => full local template name (when embedding; defaults to general _webservice.tpl )
	
	params: name /value
		make list of names
		for now it's just 'taxon' which resolves to the full scientific name, so:

		$params=array(
			'sci_name'=>'taxon'
		)	

		$bla=array(
			'url' => 'https://webservice.com/?get_info
			'parameters' => $params,
		);
	
		would result in 
		
		https://webservice.com/?get_info&sci_name=Meles meles (Linnaeus, 1758)
	

	normal title + content applies as well!
	make _webservice.tpl

	

*/
		

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

class NsrTaxonManagement extends NsrController
{

    public $usedModels = array(
        'labels_sections',
        'pages_taxa',
        'pages_taxa_titles',
        'sections',
    );
	
    public $controllerPublicName = 'Soortenregister beheer';

	private $maxCategories = 50;
	
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
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );	
	}

    public function tabsAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Define categories'));

        if ($this->rHasVal('new_page') && !$this->isFormResubmit())
		{
            $tp=$this->createTaxonCategory($this->rGetVal('new_page'), $this->rGetVal('show_order'));
            if (!$tp)
			{
                $this->addError($this->translate('Could not save category.'), 1);
                $this->addError('(' . $tp . ')', 1);
            }
        }

        $lp=$this->getProjectLanguages();

        $pages = $this->models->PagesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'order' => 'show_order'
        ));

        foreach((array)$pages as $key=>$page)
		{
            foreach((array)$lp as $k=>$language)
			{
                $tpt = $this->models->PagesTaxaTitles->_get(
                array(
                    'id' => array(
                        'project_id' => $this->getCurrentProjectId(),
                        'page_id' => $page['id'],
                        'language_id' => $language['language_id']
                    )
                ));

                $pages[$key]['page_titles'][$language['language_id']] = $tpt[0]['title'];
            }
            $nextShowOrder = $page['show_order'] + 1;
        }

        $this->smarty->assign('nextShowOrder', $nextShowOrder);
        $this->smarty->assign('maxCategories', $this->maxCategories);
        $this->smarty->assign('languages', $lp);
        $this->smarty->assign('pages', $pages);
        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());

        $this->printPage();
    }

    public function ranksAction()
    {
		
		die('to be done');
		
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxonomic ranks'));

        if ($this->rHasVal('ranks') && !$this->isFormResubmit()) {

			$pr = $this->newGetProjectRanks();

            $parent = 'null';

            $isLowerTaxon = false;

            foreach ((array) $this->rGetVal('ranks') as $key => $rank) {

                if ($this->rGetVal('higherTaxaBorder') == $rank) {

                    $isLowerTaxon = true;

                }

                $d = $this->models->ProjectsRanks->_get(array(
                    'id' => array(
                        'rank_id' => $rank,
                        'project_id' => $this->getCurrentProjectId()
                    )
                ));

                if ($d) {

                    $this->models->ProjectsRanks->save(
						array(
							'id' => $d[0]['id'],
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));

                    $parent = $d[0]['id'];

                }
                else {

					$this->models->ProjectsRanks->save(
						array(
							'id' => null,
							'project_id' => $this->getCurrentProjectId(),
							'rank_id' => $rank,
							'parent_id' => $parent,
							'lower_taxon' => $isLowerTaxon ? '1' : '0'
						));

					$this->logChange($this->models->ProjectsRanks->getDataDelta());

                    $parent = $this->models->ProjectsRanks->getNewId();
                }
            }

            $this->models->ProjectsRanks->update(array(
                'keypath_endpoint' => 0
            ), array(
                'project_id' => $this->getCurrentProjectId(),
                'lower_taxon' => 0
            ));


            foreach ((array) $pr as $key => $rank) {

                if (!in_array($rank['rank_id'], $this->rGetVal('ranks'))) {

                    $pr = $this->models->ProjectsRanks->_get(
                    array(
                        'id' => array(
                            'project_id' => $this->getCurrentProjectId(),
                            'rank_id' => $rank['rank_id']
                        )
                    ));

                    foreach ((array) $pr as $key => $val) {

                        $this->models->LabelsProjectsRanks->delete(array(
                            'project_id' => $this->getCurrentProjectId(),
                            'project_rank_id' => $val['id']
                        ));
                    }

                    $this->models->ProjectsRanks->delete(array(
                        'project_id' => $this->getCurrentProjectId(),
                        'rank_id' => $rank['rank_id']
                    ));
                }
            }

            if (isset($_SESSION['admin']['project']['ranklist']))
                unset($_SESSION['admin']['project']['ranklist']);
            if (isset($_SESSION['admin']['user']['species']['tree']))
                unset($_SESSION['admin']['user']['species']['tree']);

            $this->addMessage($this->translate('Ranks saved.'));
        }

        $r = array_merge($this->models->Ranks->_get(array(
            'id' => array(
                'parent_id is' => 'null'
            ),
            'order' => 'parent_id',
            'fieldAsIndex' => 'id'
        )), $this->models->Ranks->_get(array(
            'id' => array(
                'parent_id !=' => -1
            ),
            'order' => 'parent_id',
            'fieldAsIndex' => 'id'
        )), $this->models->Ranks->_get(array(
            'id' => array(
                'parent_id' => -1
            ),
            'order' => 'parent_id',
            'fieldAsIndex' => 'id'
        )));

        $pr = $this->newGetProjectRanks(array(
            'forceLookup' => true
        ));

        $this->smarty->assign('ranks', $r);

        $this->smarty->assign('projectRanks', $pr);

        $this->printPage();
    }

    public function ranklabelsAction()
    {
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxonomic ranks: labels'));

        $pr = $this->getProjectRanks(array(
            'includeLanguageLabels' => true
        ));

        $this->smarty->assign('projectRanks', $pr);

        $this->smarty->assign('languages', $this->getProjectLanguages());

        $this->printPage();
    }

    public function sectionsAction()
    {
		die('to be done');
		
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Define sections'));

        if ($this->rHasVal('new') && !$this->isFormResubmit()) {

            foreach ((array) $this->rGetVal('new') as $key => $val) {

				if (empty($val)) continue;

				$d = $this->models->Sections->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $key
					),
					'columns' => 'max(show_order) as max_show_order',
				));

				$d = $d ? $d[0]['max_show_order']+1 : 0;

                $this->models->Sections->save(
                array(
                    'id' => null,
                    'project_id' => $this->getCurrentProjectId(),
                    'page_id' => $key,
                    'section' => $val,
                    'show_order' => $d
                ));

				$this->logChange($this->models->Sections->getDataDelta());
            }
        }

        $lp = $this->getProjectLanguages();

        $pages = $this->models->PagesTaxa->_get(
        array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId()
            ),
            'columns' => '*',
            'order' => 'show_order'
        ));

        foreach ((array) $pages as $key => $val) {

            $s = $this->models->Sections->_get(
            array(
                'id' => array(
                    'project_id' => $this->getCurrentProjectId(),
                    'page_id' => $val['id']
                ),
                'columns' => '*, ifnull(show_order,999) as show_order',
                'order' => 'show_order'
            ));

            $pages[$key]['sections'] = $s;
        }

        $this->smarty->assign('languages', $lp);

        $this->smarty->assign('pages', $pages);

        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());

        $this->printPage();
    }



    private function createTaxonCategory($name, $show_order = false, $isDefault = false)
    {
        $d=$this->models->PagesTaxa->save(
        array(
            'id' => null,
            'page' => $name,
            'show_order' => $show_order !== false ? $show_order : 0,
            'project_id' => $this->getCurrentProjectId(),
            'def_page' => $isDefault ? '1' : '0'
        ));
		$this->logChange($this->models->PagesTaxa->getDataDelta());
		return $d;
    }



}

