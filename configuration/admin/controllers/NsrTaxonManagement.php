<?php

/*
	'url' => URL (as is)
	'parameters' => array of dynamic params (see below)
	'substitute' => array of strings to replace in the url (alternative for params)
	'link_embed' => embed / embed_link / link (*) / link_new  [ embedded (fetch actual content), embedded (just hand parametrized URL to template), link (same window), link (new window) ]
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

    public $modelNameOverride='NsrTaxonModel';

    public $usedModels = array(
        'labels_sections',
        'tab_order',
        'pages_taxa',
        'pages_taxa_titles',
        'sections',
		'content_taxa'
    );

    public $cssToLoad = array(
        'nsr_taxon_management.css',
    );

    public $jsToLoad = array(
        'all' => array(
            'taxon.js'
        )
    );
	
    public $controllerPublicName = 'Taxon editor';

	private $maxCategories = 50;

	private $basicSubstitutionFields=
		[
			['field'=>'taxon','label'=>'scientific name'],
			['field'=>'name:nomen','label'=>'nomen (scientific name w/o author)'],
			['field'=>'name:uninomial','label'=>'uninomial (valid name 1st part)'],
			['field'=>'name:specific_epithet','label'=>'specific epithet (valid name 2nd part)'],
			['field'=>'name:infra_specific_epithet','label'=>'infra specific epithet (valid name 3rd part)'],
			['field'=>'id','label'=>'taxon ID'],
			['field'=>'project_id','label'=>'project ID'],
			['field'=>'language_id','label'=>'language ID'],
		];

	private $linkEmbedTypes=
		[
			['field'=>'embed','label'=>'fetch content into embedded template'],
			['field'=>'template','label'=>'fetch content into stand-alone template (no header/footer)'],
			['field'=>'embed_link','label'=>'serve parametrized URL to embedded template (no content fetching)'],
			['field'=>'template_link','label'=>'serve parametrized URL to stand-alone template (no content, no header/footer)'],
			['field'=>'link','label'=>'link'],
			['field'=>'link_new','label'=>'link (new window)']
		];

	private $checkTypes=
		[
			['field'=>'none','label'=>'no check'],
			['field'=>'query','label'=>'check by query'],
			['field'=>'output','label'=>'check by webservice output'],
		] ;
		
	private $regularDataBlock = ["id"=>"data","label"=>"Regular page content"];

	
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

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->moduleSettings->setController( 'species' );

		$this->show_nsr_specific_stuff=$this->moduleSettings->getGeneralSetting( 'show_nsr_specific_stuff' , 0)==1;
		$this->smarty->assign( 'use_page_blocks', $this->moduleSettings->getModuleSetting( 'use_page_blocks', 0 )==1 );

		
		if ($this->show_nsr_specific_stuff ) 
		{
			$this->basicSubstitutionFields[]=['field'=>'nsr_id','label'=>'NSR ID'];
		}
	}

    public function tabsAction()
    {
        $this->checkAuthorisation();
        $this->setPageName($this->translate('Define categories'));

        if ($this->rHasVal('new_page') && !$this->isFormResubmit())
		{
            $tp=$this->createCategory( $this->rGetVal('new_page') );
            if (!$tp)
			{
                $this->addError($this->translate('Could not save category.'), 1);
                $this->addError('(' . $tp . ')', 1);
            }
        }

        if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$this->savePageTitles( $this->rGetAll()['pages_taxa_titles'] );
			$this->saveTabOrder( $this->rGetAll()['tab_order'] );
			if ( isset($this->rGetAll()['suppress']) ) $this->saveSuppressState( $this->rGetAll()['suppress'] );
			$this->saveStartOrder( $this->rGetAll()['start_order'] );
			$this->saveShowWhenEmpty( $this->rGetAll()['show_when_empty'] );
        }		

        $this->smarty->assign( 'maxCategories', $this->maxCategories );
        $this->smarty->assign( 'languages', $this->getProjectLanguages() );
        $this->smarty->assign( 'pages', $this->getCategories() );
        $this->smarty->assign( 'defaultLanguage', $this->getDefaultProjectLanguage() );

        $this->printPage();
    }
	
    public function tabAction()
    {
		$this->UserRights->setRequiredLevel( ID_ROLE_SYS_ADMIN );	

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Category'));

		if ( !$this->rHasId() ) $this->redirect('tabs.php');

        if ($this->rHasVal('action','save') && !$this->isFormResubmit())
		{
			$d=$this->models->PagesTaxa->update(
				array( 'always_hide' => (( isset($this->rGetAll()['always_hide']) && $this->rGetAll()['always_hide']=='on' ) ? '1' : '0' ) ),
				array( 'project_id' => $this->getCurrentProjectId(), 'id' => $this->rGetId() )
			);

			$this->logChange($this->models->PagesTaxa->getDataDelta() + ['note'=>'Updated tab definition']);
			$this->saveExternalReference();
			$this->savePageBlocks();
			$this->addMessage( $this->translate( 'Saved.' ) );
        }

		$traits=$this->models->{$this->modelNameOverride}->getSubstitutableTraits(array('project_id'=>$this->getCurrentProjectId()));
	
        $this->smarty->assign( 'page', $this->getCategory( $this->rGetId() ) );
        $this->smarty->assign( 'dynamic_fields', array_merge($this->basicSubstitutionFields,(array)$traits) );
        $this->smarty->assign( 'check_types', $this->checkTypes );
        $this->smarty->assign( 'link_embed',  $this->linkEmbedTypes );
        $this->smarty->assign( 'encoding_methods', ['none','urlencode','rawurlencode'] );
        $this->smarty->assign( 'tabs', $this->getCategories() );

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

					$this->logChange($this->models->ProjectsRanks->getDataDelta() + ['note'=>'Updated project ranks']);

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
            if (isset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']))
                unset($_SESSION['admin']['user']['species'][$this->getCurrentProjectId()]['tree']);

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

				$this->logChange($this->models->Sections->getDataDelta() + ['note'=>'Updated sections']);
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

    private function createCategory( $name )
    {
        $d=$this->models->PagesTaxa->save(
			array(
				'page' => $name,
				'show_order' => 99,
				'project_id' => $this->getCurrentProjectId(),
				'def_page' => '0'
			));
		$this->logChange($this->models->PagesTaxa->getDataDelta() + ['note'=>'New tab definition']);
		return $d;
    }

	private function getCategory( $id )
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
		
		if ( !empty($page['page_blocks']) )
		{
			// fetch the names + "Regular page content"
			$cat=$this->getCategories();
			$d=json_decode($page['page_blocks']);
			foreach((array)$d as $val)
			{
				if ($val==$this->regularDataBlock['id'])
					$page['page_blocks_decoded'][]=$this->regularDataBlock;
				else
					$page['page_blocks_decoded'][]=["id"=>$val,"label"=>$cat[array_search($val, array_column($this->getCategories(), 'id'))]['page']];
			}
		}
		else
		{
			$page['page_blocks_decoded'][0]=$this->regularDataBlock;
		}
		return $page;
	}

	private function saveExternalReference()
	{
		$data=$this->rGetAll()['external_reference'];

		$data['url']=trim($data['url']);
		
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
			/*
				would be more acurate when checking the URL including the 
				substitution of values of an actual taxon. however, if the
				parameters include traits, actual values might not have
				been entered into the system yet at the time of saving this
				URL.
			*/
			if ( !filter_var($data['url'], FILTER_VALIDATE_URL) )
			{
				$this->addWarning( $this->translate( 'Invalid URL (might be by design).' ) );
			}
			
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

		$this->logChange($this->models->PagesTaxa->getDataDelta() + ['note'=>'Updated tab definition']);
		
	}

	private function savePageBlocks()
	{

		//alter table pages_taxa add column `page_blocks` varchar(255) DEFAULT NULL after always_hide;
		$data=$this->rGetAll()['page_blocks'];
		if ( empty($data) ) $data[0]=$this->regularDataBlock;

		$d=$this->models->PagesTaxa->update(
			array(
				'page_blocks'=> json_encode($data)
			),
			array(
				'id' => $this->rGetId(),
				'project_id' => $this->getCurrentProjectId(),
			)
		);

		$this->logChange($this->models->PagesTaxa->getDataDelta() + ['note'=>'Updated page blocks']);
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

			$this->logChange($this->models->LabelsProjectsRanks->getDataDelta() + ['note'=>'Updated rank translation']);
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

		$this->models->TabOrder->delete(array(
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
	
	private function savePageTitles( $titles )
	{
		foreach((array)$titles as $page_id=>$languages)
		{
			foreach((array)$languages as $language_id=>$label)
			{
				$label=trim($label);

	            if ( empty($label) )
				{
					$this->models->PagesTaxaTitles->delete(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $language_id,
						'page_id' => $page_id
					));

					if ($this->models->PagesTaxaTitles->getAffectedRows()>0)
						$this->addMessage( $this->translate( 'Label deleted.' ) );

            	}
	            else
				{
					$d = $this->models->PagesTaxaTitles->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'language_id' => $language_id,
							'page_id' => $page_id
						)
					));

					$this->models->PagesTaxaTitles->save(
					array(
						'id' => isset($d[0]['id']) ? $d[0]['id'] : null,
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $language_id,
						'page_id' => $page_id,
						'title' => $label
					));

					if ($this->models->PagesTaxaTitles->getAffectedRows()>0)
						$this->addMessage( $this->translate( isset($d[0]['id']) ? 'Label updated.' : 'Label added.' ) );

					$change=$this->models->PagesTaxaTitles->getDataDelta();
					$this->logChange(['before'=>@$change['before'][0],'after'=>@$change['after'][0],'note'=>'Changed tab label']);

				}
			}
		}
	}

	private function saveTabOrder( $order )
	{
		foreach((array)$order as $key=>$page_id)
		{
			$p=$this->models->TabOrder->_get( [ 'id' => [
				'project_id' => $this->getCurrentProjectId(),
				'page_id' => $page_id,
			] ] );
			
			if ($p)
			{
				$this->models->TabOrder->update(
					['show_order' => $key+1],
					['id' => $p[0]['id'], 'project_id' => $this->getCurrentProjectId(),'page_id' => $page_id]
				);
			}
			else
			{
				$this->models->TabOrder->save(
					['id' => null, 'project_id' => $this->getCurrentProjectId(),'page_id' => $page_id, 'show_order' => $key+1]
				);
			}
		}
	}

	private function saveSuppressState( $states )
	{
		$this->models->TabOrder->update(
			[ 'suppress' => 0 ],
			[ 'project_id' => $this->getCurrentProjectId() ]
		);

		foreach((array)$states as $key=>$val)
		{
			if ($val=='on')
			{
				$this->models->TabOrder->update(
					[ 'suppress' => '1' ],
					[ 'page_id' => $key, 'project_id' => $this->getCurrentProjectId() ]
				);
			}
		}
	}

	private function saveStartOrder( $order )
	{
		foreach((array)$order as $page_id=>$start_order)
		{
			$this->models->TabOrder->update(
				['start_order' => $start_order=='' ? 'null' : $start_order ],
				['page_id' => $page_id, 'project_id' => $this->getCurrentProjectId(),]
			);
		}
	}

	private function saveShowWhenEmpty( $states )
	{
		foreach((array)$states as $page_id=>$state)
		{
			$this->models->TabOrder->update(
				['show_when_empty' => $state ],
				['page_id' => $page_id, 'project_id' => $this->getCurrentProjectId(),]
			);
		}
	}

}

