<?php



/*
	'url' => URL (as is)
	'parameters' => array of dynamic params (see below)
	'substitute' => array of strings to replace in the url (alternative for params)
	'link_embed' => embed / link (*) / link_new   [ embedded, link (same window), link (new window) ]
	'check_type' => none (*) / query
	'query' => 'select 1 as show', (should return one row with one field called 'show' with value 1 or 0)
	'template' => full local template name (when embedding; defaults to general _webservice.tpl )
	
	params / subst: name /value
		make list of names
		for now it's just 'taxon' which resolves to the full scientific name, so:

		$bla=array(
			'url' => 'https://webservice.com/?get_info
			'parameters' => array('sci_name'=>'taxon')
		);
	
		would result in 
		https://webservice.com/?get_info&sci_name=Meles+meles+(Linnaeus,+1758)


		$bla=array(
			'url' => 'https://webservice.com/get_info/%SCI_NAME%
			'substitute' => array('%SCI_NAME%'=>'taxon')
		);
		would result in 
		https://webservice.com/get_info/Meles+meles+(Linnaeus,+1758)

	goes into pages_taxa.external_reference
	
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
		'content_taxa'
    );

    public $jsToLoad = array(
        'all' => array(
            'taxon.js'
        )
    );
	
    public $controllerPublicName = 'Taxon editor';

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

        $this->smarty->assign('nextShowOrder', isset($nextShowOrder) ? $nextShowOrder : 0 );
        $this->smarty->assign('maxCategories', $this->maxCategories);
        $this->smarty->assign('languages', $lp);
        $this->smarty->assign('pages', $pages);
        $this->smarty->assign('defaultLanguage', $this->getDefaultProjectLanguage());

        $this->printPage();
    }
	
    public function tabAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );	

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Category'));

		if ( !$this->rHasId() ) $this->redirect('tabs.php');

        if ($this->rHasVal('action','save') ) //&& !$this->isFormResubmit())
		{
			$d=$this->models->PagesTaxa->update(
				array( 'always_hide' => (( isset($this->rGetAll()['always_hide']) && $this->rGetAll()['always_hide']=='on' ) ? '1' : '0' ) ),
				array( 'project_id' => $this->getCurrentProjectId(), 'id' => $this->rGetId() )
			);

			$this->logChange($this->models->PagesTaxa->getDataDelta());
			$this->saveExternalReference();
			$this->addMessage( $this->translate( 'Saved.' ) );
        }

        $this->smarty->assign( 'page', $this->getPage( $this->rGetId() ) );
        $this->smarty->assign( 'dynamic_fields',
			[
				['field'=>'taxon','label'=>'scientific name'],
				['field'=>'id','label'=>'taxon ID'],
				['field'=>'project_id','label'=>'project ID'],
				['field'=>'language_id','label'=>'language ID'],
				['field'=>'nsr_id','label'=>'NSR ID']
			] );
        $this->smarty->assign( 'check_types', [['field'=>'none','label'=>'no check'],['field'=>'query','label'=>'check by query']] );
        $this->smarty->assign( 'link_embed', [['field'=>'embed','label'=>'embed'],['field'=>'link','label'=>'link'],['field'=>'link_new','label'=>'link (new window)']] );
        $this->smarty->assign( 'encoding_methods', ['none','urlencode','rawurlencode'] );

        $this->printPage();
    }

    public function ranksAction()
    {
	
        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxonomic ranks'));

        if ($this->rHasVal('ranks') && !$this->isFormResubmit())
		{
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

        $pr = $this->newGetProjectRanks();

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

    public function ajaxInterfaceAction()
    {
        if (!$this->rHasVal('action')) return;

		if ($this->rGetVal('action')=='get_rank_labels')
		{
			$d=$this->getRankLabels( array('language_id'=>$this->rGetVal('language') ) );
			$this->smarty->assign('returnText', json_encode($d));
        }
        else 
		if ($this->rGetVal('action')=='save_rank_label')
		{
			$d=$this->saveRankLabel( array('id'=>$this->rGetId(),'language_id'=>$this->rGetVal('language'),'label'=>$this->rGetVal('label') ) );
			$this->smarty->assign('returnText', $d ? 'saved' : 'failed');
        }
        else 
		if ($this->rGetVal('action')=='delete_page')
		{
            $this->deletePage( array('id'=>$this->rGetId() ) );
        }
        else 
		if ($this->rGetVal('action')=='get_page_labels')
		{
            $d=$this->getPageTitles( array('language_id'=>$this->rGetVal('language') ) );
			$this->smarty->assign('returnText', json_encode($d));
        }

        $this->printPage('ajax_interface');
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

	private function getPage( $id )
	{
        $page=$this->models->PagesTaxa->_get(array(
            'id' => array(
                'project_id' => $this->getCurrentProjectId(),
                'id' => $this->rGetId()
            )
        ));
		
		$page=$page ? $page[0] : null;
		
		if ( !empty($page['external_reference']) )
		{
			$page['external_reference_decoded']=json_decode($page['external_reference']);
		}
		
		return $page;

	}

	private function saveExternalReference()
	{
		$data=$this->rGetAll()['external_reference'];
		
		if ( empty($data['url']) )
		{
			$d=$this->models->PagesTaxa->update(
				array(
					'external_reference'=> 'null'
				),
				array(
					'id' => $this->rGetId(),
					'project_id' => $this->getCurrentProjectId(),
				)
			);
		}
		else
		{
			$d=array();
			foreach((array)$data['substitute']['name'] as $key=>$val)
			{
				if ( empty($val) ) continue;
				$d[$val]=$data['substitute']['value'][$key];
			}
			$data['substitute']=$d;
			
			$d=array();
			foreach((array)$data['parameters']['name'] as $key=>$val)
			{
				if ( empty($val) ) continue;
				$d[$val]=$data['parameters']['value'][$key];
			}
			$data['parameters']=$d;

			$d=$this->models->PagesTaxa->update(
				array(
					'external_reference'=> json_encode($data)
				),
				array(
					'id' => $this->rGetId(),
					'project_id' => $this->getCurrentProjectId(),
				)
			);
		}

		$this->logChange($this->models->PagesTaxa->getDataDelta());
		
	}

    private function saveRankLabel( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;
		$label=isset($p['label']) ? trim($p['label']) : null;
		
		if ( !isset($id) || !isset($language_id) ) return;

		if ( empty($label) )
		{
			$this->models->LabelsProjectsRanks->delete(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $language_id,
				'project_rank_id' => $id
			));
		}
		else
		{
			$lpr = $this->models->LabelsProjectsRanks->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $language_id,
					'project_rank_id' => $id
				)
			));

			$this->models->LabelsProjectsRanks->save(
			array(
				'id' => isset($lpr[0]['id']) ? $lpr[0]['id'] : null,
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $language_id,
				'project_rank_id' => $id,
				'label' => $label
			));

			$this->logChange($this->models->LabelsProjectsRanks->getDataDelta());
        }
		
		return true;
		
    }

    private function getRankLabels( $p )
    {
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;
		
		if ( !isset($language_id) ) return;

		$l = $this->models->Languages->_get(array(
			'id' =>$language_id,
			'columns' => 'direction'
		));

		return $this->models->LabelsProjectsRanks->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $language_id
				),
				'columns' => '*, \'' . $l['direction'] . '\' as direction'
			));

    }

    private function deletePage( $p )
    {
		$id=isset($p['id']) ? $p['id'] : null;
		
		if ( !isset($id) ) return;

		$this->models->ContentTaxa->delete(array(
			'project_id' => $this->getCurrentProjectId(),
			'page_id' => $id
		));

		$this->models->PagesTaxaTitles->delete(array(
			'project_id' => $this->getCurrentProjectId(),
			'page_id' => $id
		));

		$this->models->Sections->delete(array(
			'project_id' => $this->getCurrentProjectId(),
			'page_id' => $id
		));

		$this->models->PagesTaxa->delete(array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id
		));
    }

    private function getPageTitles( $p )
    {
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;
		
		if ( !isset($language_id) ) return;

		return $this->models->PagesTaxaTitles->_get(array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(),
				'language_id' => $this->rGetVal('language')
			),
			'columns' => 'id,title,page_id,language_id'
		));

    }


}

