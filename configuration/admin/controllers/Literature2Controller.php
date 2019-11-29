<?php

include_once ('NsrController.php');
include_once ('RdfController.php');
include_once ('ModuleSettingsReaderController.php');

class Literature2Controller extends NsrController
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'literature_taxa',
		'names',
		'actors',
		'presence_taxa',
		'literature2_authors',
		'literature2_publication_types',
		'literature2_publication_types_labels'
    );

    public $controllerPublicName = 'Literature';
    public $usedHelpers = array('csv_parser_helper');
    public $cssToLoad = array('nsr_taxon_beheer.css','literature2.css');

	private $_actors=null;
	private $_literature=null;
	private $_matchThresholdDefault=75;

	public $jsToLoad =
		array(
			'all' => array(
				'nsr_taxon_beheer.js',
				'literature2.js',
				'lookup.js',
			)
		);

	private $referenceBefore;

	private $referenceId=null;

	private $publicationTypes=array();

	private $basePubTypes=array(
		'Artikel',
		'Boek',
		'Boek (deel)',
		'Database',
		'Hoofdstuk',
		'Literatuur',
		'Manuscript',
		'Persbericht',
		'Persoonlijke mededeling',
		'Rapport',
		'Serie',
		'Tijdschrift',
		'Website'
	);

	private $publicationTypesSortOrder;

	private $lit2Columns=
		array(
			'-1'=>'',
			'label'=>'title',
			'alt_label'=>'alt. title',
			'date'=>'date',
			'author'=>'author(s)',
			'publication_type'=>'publication type',
			'publishedin'=>'published in',
			'publisher'=>'publisher',
			'periodical'=>'periodical',
			'pages'=>'page(s)',
			'volume'=>'volume',
			'external_link'=>'link',
			'-2'=>'',
			'_reference_'=>'reference #',
		);

    public function __construct ()
    {
        parent::__construct();
		$this->initialize();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialize()
    {
		$this->Rdf = new RdfController;

		$this->moduleSettings=new ModuleSettingsReaderController;
		$this->_matchThresholdDefault=$this->moduleSettings->getModuleSetting( array( 'setting'=>'literature2_import_match_threshold','subst'=>$this->_matchThresholdDefault));

		$this->initializePublicationTypes();
		$this->setPublicationTypesSortOrder( 'ifnull(_b.label,_a.sys_label)' );
		$this->setPublicationTypes();
    }

    public function indexAction()
	{
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Index'));
		$this->smarty->assign( 'prevSearch', $this->moduleSession->getModuleSetting('lookup_params') );
		$this->smarty->assign( 'authorAlphabet', $this->getAuthorAlphabet() );
		$this->smarty->assign( 'titleAlphabet', $this->getTitleAlphabet() );
		$this->smarty->assign( 'incomplete', count($this->getIncompleteReferences()));
		$this->smarty->assign( 'CRUDstates', $this->getCRUDstates() );

		$this->printPage();
	}

    public function indexByTypeAction()
	{
		$this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect('publication_types.php');

		$this->setPageName($this->translate('Index by publication type'));

		foreach((array)$this->getPublicationTypes() as $val)
			if ($val['id']==$this->rGetId()) $publicationType=$val['sys_label'];

		$this->smarty->assign('publicationType',$publicationType);
		$this->smarty->assign('references',$this->getReferences(array('publication_type'=>$this->rGetId(),'search'=>'*')));
		$this->printPage();
	}

    public function checkAction()
	{
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Incompletely parsed references'));

		$this->smarty->assign('references',$this->getIncompleteReferences());
		$this->printPage();
	}



    public function ajaxInterfaceAction ()
    {
        if (!$this->rHasVal('action')) return;
		$return=null;
		$return=$this->getReferenceLookupList($this->rGetAll());
		$this->moduleSession->setModuleSetting(array('setting'=>'lookup_params','value'=>$this->rGetAll()));
		$this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
		$this->printPage();
    }

    public function editAction()
	{

		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();

			$this->setReferenceId($this->rGetId());
			$this->setReferenceBefore();
			$this->deleteReference();
			$this->setReferenceId(null);
			$this->logChange(array('before'=>$this->getReferenceBefore(),'note'=>'deleted reference '.$this->getReferenceBefore('label')));
			$template='_delete_result';
		}
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();

			$this->setReferenceId($this->rGetId());
			$this->setReferenceBefore();
			$this->updateReference();
			$this->saveReferenceTaxa();
			$this->logChange(array('before'=>$this->getReferenceBefore(),'after'=>$this->getReference(),'note'=>'updated reference '.$this->getReferenceBefore('label')));
		}
		else
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
			$this->checkAuthorisation();

			$this->smarty->assign('reference',$this->rGetAll());
			$this->saveReference();
		}

		if ($this->rHasId())
		{
			$this->setReferenceId($this->rGetId());
		}

		if ($this->getReferenceId())
		{
			$this->setPageName($this->translate('Edit reference'));
			$this->smarty->assign('reference',$this->getReference());
			$this->smarty->assign('links',$this->getReferenceLinks());
		}
		else
		{
			$this->setPageName($this->translate('New reference'));
		}

		$publicationTypes=$this->getPublicationTypes();

		$gepubliceerd_in_ids=$periodiek_ids=array();
		foreach((array)$publicationTypes as $val)
		{
			if ($val['sys_label']=='Boek' || $val['sys_label']=='Website')
			{
				$gepubliceerd_in_ids[]=$val['id'];
			}
			if ($val['sys_label']=='Serie' || $val['sys_label']=='Tijdschrift')
			{
				$periodiek_ids[]=$val['id'];
			}
		}

		$this->smarty->assign('gepubliceerd_in_ids',$gepubliceerd_in_ids);
		$this->smarty->assign('periodiek_ids',$periodiek_ids);
		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('publicationTypes',$publicationTypes);
		$this->printPage(isset($template) ? $template : null);
	}

    public function viewAction()
	{
		$this->checkAuthorisation();

		if ($this->rHasId())
		{
			$this->setReferenceId($this->rGetId());
		}

		if ($this->getReferenceId())
		{
			$this->setPageName($this->translate('View reference'));
			$this->smarty->assign('reference',$this->getReference());
			$this->smarty->assign('links',$this->getReferenceLinks());
		}

		$publicationTypes=$this->getPublicationTypes();

		$gepubliceerd_in_ids=$periodiek_ids=array();
		foreach((array)$publicationTypes as $val)
		{
			if ($val['sys_label']=='Boek' || $val['sys_label']=='Website')
			{
				$gepubliceerd_in_ids[]=$val['id'];
			}
			if ($val['sys_label']=='Serie' || $val['sys_label']=='Tijdschrift')
			{
				$periodiek_ids[]=$val['id'];
			}
		}

		$this->smarty->assign('gepubliceerd_in_ids',$gepubliceerd_in_ids);
		$this->smarty->assign('periodiek_ids',$periodiek_ids);
		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('publicationTypes',$publicationTypes);

		// @TODO: check this template not set in this scope???
		$this->printPage(isset($template) ? $template : null);
	}

    public function publicationTypesAction()
	{
		$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Publication types'));

		if ( $this->rHasVal('action','save') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->savePublicationType();
		}
		else
		if ( $this->rHasVal('action','save_translations') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionUpdate() );
			$this->checkAuthorisation();
			$this->savePublicationTypeTranslations();
		}
		else
		if ( $this->rHasId() && $this->rHasVal('action','delete') )
		{
			$this->UserRights->setActionType( $this->UserRights->getActionDelete() );
			$this->checkAuthorisation();
			$this->deletePublicationType();
		}

		$this->setPublicationTypesSortOrder( 'sys_label' );
		$this->setPublicationTypes();

		$this->smarty->assign( 'languages', $this->getProjectLanguages() );
		$this->smarty->assign( 'publicationTypes', $this->getPublicationTypes() );
		$this->printPage();
	}

	public function bulkUploadAction()
	{
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Bulk upload (matching)'));

		$raw=null;
		$ignorefirst=false;
		$lines=null;
		$delcols=null;
		$emptycols=null;
		$fields=null;
		$matches=null;
		$firstline=null;
		$matching_publication_types=null;

		$ignorefirst=$this->rHasVal('ignorefirst','1');
		$this->moduleSession->setModuleSetting(array(
            'setting' => 'ignorefirst',
            'value' => $ignorefirst
        ));

		if ($this->rHasVal('fields'))
		{
			$fields=$this->rGetVal('fields');
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'fields',
                'value' => $fields
            ));
		}

		if ($this->rHasVal('raw'))
		{
			$raw=$this->rGetVal('raw');
			$hash=md5($raw);
			if ($hash != $this->moduleSession->getModuleSetting('hash'))
			{
				$this->moduleSession->setModuleSetting(array('setting' => 'delcols'));
				$this->moduleSession->setModuleSetting(array('setting' => 'match_ref'));
				$this->moduleSession->setModuleSetting(array('setting' => 'new_ref'));
				$fields=null;
			}
            $this->moduleSession->setModuleSetting(array(
                'setting' => 'hash',
                'value' => $hash
            ));

			$lines=$this->parseRawCsvData($raw);

			if (!$ignorefirst) $firstline=null;

			foreach($lines as $key=>$val)
			{
				if ($key==0)
				{
					foreach($val as $c=>$cell) $emptycols[$c]=true;
				}

				if ($ignorefirst && $key==0)
				{
					$firstline=$val;
					continue;
				}

				foreach($val as $c=>$cell)
				{
					if (strlen(trim($cell))!=0)
					{
						$emptycols[$c]=false;
					}
				}
			}
		}

		if ($lines)
		{
			if ($this->rHasVal('action','delcolreset'))
			{
				$this->moduleSession->setModuleSetting(array('setting' => 'delcols'));
			}
			else
			if ($this->rHasVal('action','delcol') && $this->rHasVal('value'))
			{
				$delcols=(array)$this->moduleSession->getModuleSetting('delcols');
				$delcols[$this->rGetVal('value')]=true;
				$this->moduleSession->setModuleSetting(array(
                    'setting' => 'delcols',
                    'value' => $delcols
                ));
			}

			$this->moduleSession->setModuleSetting(array(
                'setting' => 'lines',
                'value' => $lines
            ));
		}

		if ($this->rHasVal('threshold'))
		{
			$this->_matchThresholdDefault=
				is_numeric($this->rGetVal('threshold')) &&
				$this->rGetVal('threshold')<=100 &&
				$this->rGetVal('threshold')>0 ?
					$this->rGetVal('threshold') :
					$this->_matchThresholdDefault;

            $this->moduleSession->setModuleSetting(array(
                'setting' => 'threshold',
                'value' => $this->rGetVal('threshold')
            ));
		}

		if ($lines && $fields)
		{
			$matches=$this->matchPossibleReferences(array('lines'=>$lines,'ignorefirst'=>$ignorefirst,'fields'=>$fields));

			$this->moduleSession->setModuleSetting(array(
                'setting' => 'matches',
                'value' => $matches
            ));
			$this->moduleSession->setModuleSetting(array('setting' => 'matching_publication_types'));

			// publication_type -> publication_type_id
			if (in_array('publication_type',$fields))
			{
				$these_keys=array_keys($fields,'publication_type');

				foreach((array)$lines as $key=>$cols)
				{
					$first_type=null;  // multiple or concatenated publication_ types make no sense, so we take the first

					foreach( $these_keys as $this_key )
					{
						if ( isset($cols[$this_key]) )
						{
							$first_type=trim($cols[$this_key]);
							break;
						}
					}

					$matching_publication_types[$key]=$this->matchPublicationType( $first_type );
				}

				$this->moduleSession->setModuleSetting(array(
                    'setting' => 'matching_publication_types',
                    'value' => $matching_publication_types
                ));

			}

			foreach((array)$lines[0] as $c=>$cell)
			{
				if(isset($fields[$c]) && $fields[$c]=='author')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_author',
                        'value' => $c
                    ));
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='label')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_label',
                        'value' => $c
                    ));
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='date')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_date',
                        'value' => $c
                    ));
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='publishedin')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_publishedin',
                        'value' => $c
                    ));
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='periodical')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_periodical',
                        'value' => $c
                    ));
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='publication_type')
				{
					$this->moduleSession->setModuleSetting(array(
                        'setting' => 'field_publication_type',
                        'value' => $c
                    ));
				}

			}

			//q($matches,1);

		}

		$this->smarty->assign('field_author',$this->moduleSession->getModuleSetting('field_author'));
		$this->smarty->assign('field_label',$this->moduleSession->getModuleSetting('field_label'));
		$this->smarty->assign('field_date',$this->moduleSession->getModuleSetting('field_date'));
		$this->smarty->assign('field_publishedin',$this->moduleSession->getModuleSetting('field_publishedin'));
		$this->smarty->assign('field_periodical',$this->moduleSession->getModuleSetting('field_periodical'));
		$this->smarty->assign('field_publication_type',$this->moduleSession->getModuleSetting('field_publication_type'));

		$this->smarty->assign('threshold',$this->_matchThresholdDefault);
		$this->smarty->assign('matches',$matches);
		$this->smarty->assign('emptycols',$emptycols);
		$this->smarty->assign('fields',$fields);
		$this->smarty->assign('cols',$this->lit2Columns);
		$this->smarty->assign('delcols',$this->moduleSession->getModuleSetting('delcols'));
		$this->smarty->assign('raw',$raw);
		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('firstline',$firstline);
		$this->smarty->assign('lines',$lines);
		$this->smarty->assign('legal_publication_types',$this->getPublicationTypes());
		$this->smarty->assign('matching_publication_types',$matching_publication_types);

		$this->printPage();
	}

    public function bulkProcessAction()
	{
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Bulk upload (further matching)'));

		$matches=null;
		$match_ref=null;
		$new_ref=null;
		$duplicate_columns=null;
		$matching_authors=null;
		$matching_publishedin=null;
		$matching_periodical=null;

		$ignorefirst=$this->moduleSession->getModuleSetting('ignorefirst');
		$lines=$this->moduleSession->getModuleSetting('lines');
		$fields=$this->moduleSession->getModuleSetting('fields');
		$matches=$this->moduleSession->getModuleSetting('matches');
		$matching_publication_types=$this->moduleSession->getModuleSetting('matching_publication_types');

		if ($this->rHasVal('match_ref'))
		{
			$match_ref=$this->rGetVal('match_ref');
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'match_ref',
                'value' => $match_ref
            ));
		}

		if ($this->rHasVal('new_ref'))
		{
			$new_ref=$this->rGetVal('new_ref');
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'new_ref',
                'value' => $new_ref
            ));

			// opsporen dubbele kolommen
			foreach((array)$fields as $key=>$val)
			{
				if (empty($val)) continue;
				$duplicate_columns[$val][]=$key;
			}

			$d=array();
			foreach((array)$duplicate_columns as $key=>$val)
			{
				if (count((array)$val)>1)
				{
					$d[$key]['columns']=$val;
					foreach((array)$lines as $c=>$line)
					{
						if($ignorefirst && $c==0) continue;

						$all_filled=true;
						foreach((array)$val as $colnr)
						{
							if (empty($line[$colnr]))
							{
								$all_filled=false;
							}
						}
						if ($all_filled)
						{
							$d[$key]['example']=$line;
							break;
						}
					}
				}
			}
			$duplicate_columns=$d;


			// author -> actor_id
			if (in_array('author',$fields))
			{
				$this->setActors();
				$these_keys=array_keys($fields,'author');
				foreach((array)$new_ref as $key=>$ref)
				{
					if($ref=='on')
					{
						foreach((array)$these_keys as $this_key)
						{
							$matching_authors[$key][$this_key]=$this->matchPossibleAuthor($lines[$key][$this_key]);
						}
					}
				}
			}

			// publishedin -> publishedin_id
			if (in_array('publishedin',$fields))
			{
				$this->setLiterature();
				$these_keys=array_keys($fields,'publishedin');
				foreach((array)$new_ref as $key=>$ref)
				{
					if($ref=='on')
					{
						foreach((array)$these_keys as $this_key)
						{
							$matching_publishedin[$key][$this_key]=$this->matchPossibleLabel($lines[$key][$this_key]);
						}
					}
				}
			}

			// periodical -> periodical_id
			if (in_array('periodical',$fields))
			{
				$this->setLiterature();
				$these_keys=array_keys($fields,'periodical');
				foreach((array)$new_ref as $key=>$ref)
				{
					if($ref=='on')
					{
						foreach((array)$these_keys as $this_key)
						{
							$matching_periodical[$key][$this_key]=$this->matchPossibleLabel($lines[$key][$this_key]);
						}
					}
				}
			}
		}

		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('lines',$lines);
		$this->smarty->assign('fields',$fields);
		$this->smarty->assign('matches',$matches);

		$this->smarty->assign('field_author',$this->moduleSession->getModuleSetting('field_author'));
		$this->smarty->assign('field_label',$this->moduleSession->getModuleSetting('field_label'));
		$this->smarty->assign('field_date',$this->moduleSession->getModuleSetting('field_date'));
		$this->smarty->assign('field_publishedin',$this->moduleSession->getModuleSetting('field_publishedin'));
		$this->smarty->assign('field_periodical',$this->moduleSession->getModuleSetting('field_periodical'));
		$this->smarty->assign('field_publication_type',$this->moduleSession->getModuleSetting('field_publication_type'));

		$this->smarty->assign('match_ref',$match_ref);
		$this->smarty->assign('new_ref',$new_ref);

		$this->smarty->assign('duplicate_columns',$duplicate_columns);
		$this->smarty->assign('matching_authors',$matching_authors);
		$this->smarty->assign('matching_publishedin',$matching_publishedin);
		$this->smarty->assign('matching_periodical',$matching_periodical);
		$this->smarty->assign('matching_publication_types',$matching_publication_types);
		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('default_language',$this->getDefaultProjectLanguage());

		$this->printPage();
	}

    public function bulkSaveAction()
	{
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		$this->setPageName($this->translate('Bulk upload (saving)'));

		if (!$this->isFormResubmit())
		{
			$fields=$this->moduleSession->getModuleSetting('fields');
			$lines=$this->moduleSession->getModuleSetting('lines');
			$match_ref=$this->moduleSession->getModuleSetting('match_ref');
			$new_ref=$this->moduleSession->getModuleSetting('new_ref');
			$field_author=$this->moduleSession->getModuleSetting('field_author');
			$field_label=$this->moduleSession->getModuleSetting('field_label');
			$field_date=$this->moduleSession->getModuleSetting('field_date');
			$field_publishedin=$this->moduleSession->getModuleSetting('field_publishedin');
			$field_periodical=$this->moduleSession->getModuleSetting('field_periodical');
			$lpad=$this->moduleSession->getModuleSetting('lpad');
			$infix=$this->moduleSession->getModuleSetting('infix');
			$rpad=$this->moduleSession->getModuleSetting('rpad');
			$matching_publication_types=$this->moduleSession->getModuleSetting('matching_publication_types');

			//q($this->requestData,1);

			$kill=$this->rHasVal('kill') ? $this->rGetVal('kill') : array();
			$new_author=$this->rHasVal('new_author') ? $this->rGetVal('new_author') : array();
			$author=$this->rHasVal('author') ? $this->rGetVal('author') : array();
			$language=$this->rHasVal('language') ? $this->rGetVal('language') : array();
			$new_publishedin=$this->rHasVal('new_publishedin') ? $this->rGetVal('new_publishedin') : array();
			$new_publishedin_language=$this->rHasVal('new_publishedin_language') ? $this->rGetVal('new_publishedin_language') : array();
			$publishedin=$this->rHasVal('publishedin') ? $this->rGetVal('publishedin') : array();
			$new_periodical=$this->rHasVal('new_periodical') ? $this->rGetVal('new_periodical') : array();
			$new_periodical_language=$this->rHasVal('new_periodical_language') ? $this->rGetVal('new_periodical_language') : array();
			$periodical=$this->rHasVal('periodical') ? $this->rGetVal('periodical') : array();

			$this->setActors();
			$this->setLiterature();

			$columns=array();

			$literature_id_index=array();
			$literature_id_index=$match_ref;

			$prev_created_authors=array();
			$prev_created_publications=array();

			// ontdubbelen kolommen
			foreach((array)$fields as $key=>$val)
			{
				if (empty($val)) continue;
				$columns[$val][]=$key;
			}


			foreach((array)$new_ref as $line_number=>$dummy)
			{
				// skipping lines that were marked as "don't save after all"
				if (in_array($line_number,$kill)) continue;

				// get the current line
				$line=$lines[$line_number];

				$line_authors=array();
				$line_publication=null;
				$line_periodical=null;

				// AUTHORS
				if (!empty($field_author))
				{
					// authors selected to be new
					if (isset($new_author[$line_number][$field_author]))
					{
						// parse and match authors
						$pAuthors=$this->matchPossibleAuthor($line[$field_author]);

						foreach((array)$new_author[$line_number][$field_author] as $key=>$val)
						{
							$new_auth_name=null;

							if ($val=='on' && isset($pAuthors[$key]))
							{
								$new_auth_name=trim($pAuthors[$key]['name']);

								if (empty($new_auth_name)) continue;

								if (isset($prev_created_authors[$new_auth_name]))
								{
									$new_auth_id=$prev_created_authors[$new_auth_name];
								}
								else
								{
									$this->models->Actors->save(
									array(
										'project_id' => $this->getCurrentProjectId(),
										'name' => $new_auth_name
									));
									$new_auth_id=$prev_created_authors[$new_auth_name]=$this->models->Actors->getNewId();
									$this->addmessage(sprintf($this->translate('Saved author "%s"'),$new_auth_name));
								}
							}

							if (!empty($new_auth_id)) $line_authors[]=$new_auth_id;
						}
					}

					// existing authors
					if (isset($author[$line_number][$field_author]))
					{
						foreach((array)$author[$line_number][$field_author] as $key=>$auth_id)
						{
							if (!empty($auth_id)) $line_authors[]=$auth_id;
						}
					}
				}


				// PUBLISHED IN
				if (!empty($field_publishedin))
				{
					// publications selected to be new
					if (isset($new_publishedin[$line_number][$field_publishedin]))
					{
						// parse and match publications
						$pPublished=$this->matchPossibleLabel($line[$field_publishedin]);

						foreach((array)$new_publishedin[$line_number][$field_publishedin] as $key=>$val)
						{
							$new_publ_name=null;

							if ($val=='on' && isset($pPublished[$key]))
							{
								$new_publ_name=trim($pPublished[$key]['label']);

								if (empty($new_publ_name)) continue;

								if (isset($prev_created_publications[$new_publ_name]))
								{
									$new_publ_id=$prev_created_publications[$new_publ_name];
								}
								else
								{
									$lit=array(
										'project_id' => $this->getCurrentProjectId(),
										'label' => $new_publ_name
									);

									if (isset($new_publishedin_language[$line_number][$field_publishedin][$key]) &&
										!empty($new_publishedin_language[$line_number][$field_publishedin][$key]))
									{
										$lit['language_id']=$new_publishedin_language[$line_number][$field_publishedin][$key];
									}

									$this->models->Literature2->save($lit);

									$new_publ_id=$prev_created_publications[$new_publ_name]=$this->models->Literature2->getNewId();
									$this->addmessage(sprintf($this->translate('Saved reference "%s" (published in)'),$new_publ_name));
								}
							}

							if (!empty($new_publ_id)) $line_publication=$new_publ_id;
						}
					}

					// existing publications
					if (isset($publishedin[$line_number][$field_publishedin]))
					{
						foreach((array)$publishedin[$line_number][$field_publishedin] as $key=>$publ_id)
						{
							if (!empty($publ_id)) $line_publication=$publ_id;
						}
					}
				}


				// PERIODICAL
				if (!empty($field_periodical))
				{
					// periodicals selected to be new
					if (isset($new_periodical[$line_number][$field_periodical]))
					{
						// parse and match publications
						$pPublished=$this->matchPossibleLabel($line[$field_periodical]);

						foreach((array)$new_periodical[$line_number][$field_periodical] as $key=>$val)
						{
							$new_publ_name=null;

							if ($val=='on' && isset($pPublished[$key]))
							{
								$new_publ_name=trim($pPublished[$key]['label']);

								if (empty($new_publ_name)) continue;

								if (isset($prev_created_publications[$new_publ_name]))
								{
									$new_publ_id=$prev_created_publications[$new_publ_name];
								}
								else
								{

									$lit=array(
										'project_id' => $this->getCurrentProjectId(),
										'label' => $new_publ_name
									);

									if (isset($new_periodical_language[$line_number][$field_periodical][$key]) &&
										!empty($new_periodical_language[$line_number][$field_periodical][$key]))
									{
										$lit['language_id']=$new_periodical_language[$line_number][$field_periodical][$key];
									}

									$this->models->Literature2->save($lit);

									$new_publ_id=$prev_created_publications[$new_publ_name]=$this->models->Literature2->getNewId();
									$this->addmessage(sprintf($this->translate('Saved reference "%s" (periodical)'),$new_publ_name));
								}
							}

							if (!empty($new_publ_id)) $line_periodical=$new_publ_id;
						}
					}

					// existing publications
					if (isset($periodical[$line_number][$field_periodical]))
					{
						foreach((array)$periodical[$line_number][$field_periodical] as $key=>$publ_id)
						{
							if (!empty($publ_id)) $line_periodical=$publ_id;
						}
					}
				}


				// building query
				$d=array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id'=> isset($language[$line_number]) ? $language[$line_number] : $this->getDefaultProjectLanguage()
				);

				if (!empty($line_publication)) $d['publishedin_id']=$line_publication;
				if (!empty($line_periodical)) $d['periodical_id']=$line_periodical;

				foreach((array)$columns as $col_name=>$field)
				{

					if ( $col_name=='publication_type' )
					{
						if ( !empty($matching_publication_types[$line_number]) )
						{
							$d['publication_type_id']=$matching_publication_types[$line_number];
						}
						continue;
					}

					if (count((array)$field)==1)
					{
						$d[$col_name]=trim($line[$field[0]]);
					}
					else
					{
						$f=array();
						foreach((array)$field as $i)
						{
							if (empty($line[$i])) continue;
							$f[]=$line[$i];
						}
						$f=implode((isset($infix[$col_name]) ? $infix[$col_name] : ''),$f );

						$d[$col_name]=
							(!empty($f) && isset($lpad[$col_name]) ? $lpad[$col_name] : '').
							$f.
							(!empty($f) && isset($rpad[$col_name]) ? $rpad[$col_name] : '');
					}
				}




				$this->models->Literature2->save( $d );

				$literature_id_index[$line_number]=$this->models->Literature2->getNewId();

				foreach((array)$line_authors as $sort_order=>$author_id)
				{
					$this->models->Literature2Authors->save(
					array(
						'project_id' => $this->getCurrentProjectId(),
						'literature2_id' => $literature_id_index[$line_number],
						'actor_id' => $author_id,
						'sort_order' => $sort_order
					));
				}

				$this->addmessage(sprintf($this->translate('Saved reference "%s"'),$line[$field_label]));

			}

			$this->moduleSession->setModuleSetting(array(
                'setting' => 'literature_id_index',
                'value' => $literature_id_index
            ));

			$ref=array_search( '_reference_', $fields );
			if ( $ref!==false )
			{
				$this->smarty->assign( 'have_ref_col', true );
			}

		}

		$this->addmessage($this->translate('Done.'));

		$this->printPage();
	}

	public function bulkUploadDownloadAction()
	{
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

		$literature_id_index=$this->moduleSession->getModuleSetting('literature_id_index');

		$fields=$this->moduleSession->getModuleSetting('fields');
		$ref_col=array_search( '_reference_',  $fields );

		$buffer_line=array();
		$buffer=array();

		foreach((array)$this->moduleSession->getModuleSetting('lines') as $key=>$line)
		{
			if ( $this->rHasVal( "action", "ref_only" )  && $ref_col!==false &&
			    $this->moduleSession->getModuleSetting('ignorefirst') && $key==0 )
			{
				continue;
			}

			if ($key==0)
			{
				$buffer_line[]='id';
			}
			else
			{
				if ( !($this->rHasVal( "action", "ref_only" )  && $ref_col!==false) )
				{
					$buffer_line[]=isset($literature_id_index[$key]) ? $literature_id_index[$key] : null;
				}
			}

			foreach((array)$line as $cKey=>$cell)
			{
				if ( $this->rHasVal( "action", "ref_only" ) && $ref_col!==false )
				{
					if ( $cKey==$ref_col )
					{
						$buffer_line[]=$cell;
					}
				}
				else
				{
					$buffer_line[]=$cell;
				}
			}

			if ( $this->rHasVal( "action", "ref_only" )  && $ref_col!==false )
			{
				$buffer_line[]=isset($literature_id_index[$key]) ? $literature_id_index[$key] : null;
			}

			$buffer[]=implode( chr(9), $buffer_line );
			$buffer_line=array();
		}

		if ( $this->rHasVal( "action", "ref_only" ) && $ref_col!==false )
		{
			$this->downloadHeaders( "bulk_literature_upload_ref.txt" );
		}
		else
		{
			$this->downloadHeaders( "bulk_literature_upload.txt" );
		}

		echo implode( chr(10), $buffer );
	}

	public function getActors()
	{
		return $this->models->Literature2Model->getActors($this->getCurrentProjectId());
	}

	private function downloadHeaders( $file )
	{
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $file );
		header( 'Pragma: no-cache' );
	}

	private function parseRawCsvData($raw)
	{
		$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
		$this->helpers->CsvParserHelper->setFieldMax(99);
		$this->helpers->CsvParserHelper->parseRawData($raw);
		$this->addError($this->helpers->CsvParserHelper->getErrors());

		if (!$this->getErrors())
		{
			return $this->helpers->CsvParserHelper->getResults();
		}
	}

	private function setReferenceId($id)
	{
		$this->referenceId=$id;
	}

	private function getReferenceId()
	{
		return isset($this->referenceId) ? $this->referenceId : false;
	}

	private function saveReference()
	{
		$label=$this->rGetVal('label');

		if (empty($label))
		{
			$this->addError('Title missing. Reference could not be saved.');
			$this->setReferenceId(null);
			return;
		}

		$d=$this->models->Literature2->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'label' => $label
		));

		if ($d)
		{
			$this->setReferenceId($this->models->Literature2->getNewId());
			$this->addMessage('New reference created.');
			$this->updateReference();
			$this->saveReferenceTaxa();
			$this->logChange(array('after'=>$this->getReference(),'note'=>'new reference '.$label));
		}
		else
		{
			$this->addError('Failed to create new reference.');
		}
	}

	private function updateReference()
	{
		$f=array(
			'language_id' => 'Taal',
			'label' => 'Titel',
			'alt_label' => 'Alt. label',
			'date' => 'Datum',
			'author' => 'Auteur (verbatim)',
			//'publication_type' => 'Publicatietype',
			'publication_type_id' => 'Publicatievorm',
			'publisher' => 'Uitgever',
			'publishedin' => 'Publicatie (verbatim)',
			'publishedin_id' => 'Publicatie',
			'periodical' => 'Periodiek (verbatim)',
			'periodical_id' => 'Periodiek',
			'pages' => 'Pagina(s)',
			'volume' => 'Volume',
			'external_link' => 'Link',
		);

		foreach($f as $field=>$label)
		{
			if ($this->rHasVar($field))
			{
				if ($this->updateReferenceValue($field,$this->rGetVal($field)))
				{
					if ($this->models->Literature2->getAffectedRows()!=0)
						$this->addMessage($label.' saved.');
				}
				else
				{
					$this->addError($label.' could not be saved.');
				}
			}
		}

		// Always set actor_id to null (incompletely parsed references have value -1)
        $this->updateReferenceValue('actor_id', null);

		// we'll generalize this once another one-many relation appears
		//if ($this->rHasVar('actor_id'))  // no if, or the last quthor won't delete
		{

			$current=$this->getReferenceAuthors();
			$retain=array();
			$new=(array)$this->rGetVal('actor_id');

			foreach((array)$current as $key=>$actor)
			{
				if (!in_array($actor['actor_id'],$new))
				{
					$this->deleteReferenceAuthor($actor['actor_id']);
					$this->addMessage('Actor deleted.');
				}
				else
				{
					array_push($retain,$actor['actor_id']);
				}
			}

			foreach($new as $key=>$actor)
			{
				if (!in_array($actor,$retain))
				{
					$this->saveReferenceAuthor($actor,$key);
					$this->addMessage('Actor added.');
				}
			}
		}
	}

	private function updateReferenceValue($name,$value)
	{
		return $this->models->Literature2->update(
			array($name=>(empty($value) ? 'null' : trim($value))),
			array('id'=>$this->getReferenceId(),'project_id'=>$this->getCurrentProjectId())
		);
	}

	private function deleteReference()
	{
		$id=$this->getReferenceId();

		if (empty($id))
		{
			$this->addError($this->translate("Cannot delete reference (no ID)."));
			return;
		}

        $this->models->Names->update(
            array('reference_id' => null),
            array('project_id' => $this->getCurrentProjectId(), 'reference_id' => $id)
		);
		$this->addMessage("Reference deleted from ".$this->models->Names->getAffectedRows()." names.");

        $this->models->PresenceTaxa->update(
            array('reference_id' => null),
            array('project_id' => $this->getCurrentProjectId(), 'reference_id' => $id)
		);
		$this->addMessage("Reference deleted from ".$this->models->PresenceTaxa->getAffectedRows()." statuses.");

		$this->deleteReferenceTaxa();

		$this->addMessage("Taxa detached.");

		$this->deleteReferenceAuthors();

		$this->addMessage("Actors detached.");

		$this->models->Literature2->freeQuery("delete from %PRE%literature2 where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");

		$this->addMessage("Reference deleted.");

	}

	private function getReferenceAuthors($id=null)
	{
		if (empty($id))
		{
			$id=$this->getReferenceId();
			if (empty($id))
			{
				$this->addError($this->translate("Cannot get reference authors (no ID)."));
				return;
			}
		}

		return $this->models->Literature2Model->getReferenceAuthors(array(
            'projectId' => $this->getCurrentProjectId(),
    		'literatureId' => $id
		));

	}

	private function saveReferenceAuthor($actor,$sort_order=0)
	{
		return $this->models->Literature2Authors->save(
			array(
				'project_id' => $this->getCurrentProjectId(),
				'literature2_id' => $this->getReferenceId(),
				'actor_id' => $actor,
				'sort_order' => $sort_order
			));
	}

	private function deleteReferenceAuthor($actor)
	{
		$id=$this->getReferenceId();

		if (!$id)
		{
			$this->addError($this->translate("Cannot delete reference author (no reference ID)."));
			return;
		}
		if (is_null($actor))
		{
			$this->addError($this->translate("Cannot delete reference author (no author ID)."));
			return;
		}

		$this->models->Literature2Authors->delete(
		    array('project_id' => $this->getCurrentProjectId(), 'literature2_id' => $id, 'actor_id' => $actor)
		);
	}

	private function deleteReferenceAuthors()
	{
		$id=$this->getReferenceId();

		if (empty($id))
		{
			$this->addError($this->translate("Cannot delete reference authors (no ID)."));
			return;
		}

		$this->models->Literature2Authors->delete(
		    array('project_id' => $this->getCurrentProjectId(), 'literature2_id' => $id)
		);
	}

	private function getTitleAlphabet()
	{
		return $this->models->Literature2Model->getTitleAlphabet($this->getCurrentProjectId());
	}

	private function getAuthorAlphabet()
	{
		return $this->models->Literature2Model->getAuthorAlphabet($this->getCurrentProjectId());
	}

	private function getReference($id=null)
	{
		if (empty($id))
			$id=$this->getReferenceId();

		if (empty($id))
			return;

		$l = $this->models->Literature2Model->getReference(array(
            'projectId' => $this->getCurrentProjectId(),
		    'literatureId' => $id
		));

		if ($l)
		{
			$l[0]['authors'] = $this->models->Literature2Model->getReferenceAuthors(array(
                'projectId' => $this->getCurrentProjectId(),
    		    'literatureId' => $id
    		));

			//print_r($l[0]); die();

            $l[0]['formatted'] = $this->formatReference($l[0]);
			
			$l[0]['taxa'] =
                $this->getReferencedTaxa($this->referenceBefore['id']);
			$l[0]['traits'] =
                $this->getReferencedTraits($this->referenceBefore['id']);

			return $l[0];
		}
	}

	private function getIncompleteReferences ()
	{
		$data = $this->models->Literature2Model->getReferences(array(
            'projectId' => $this->getCurrentProjectId(),
    		'incomplete' => true
		));
		foreach ((array)$data as $key => $val) {
			$data['authors'] = $this->getReferenceAuthors($val['id']);
    	}
    	return $data;
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
					$match=$match ? true : (stripos(strip_tags($val['label']),$search)===0);

					if (!$match)
						$match=$match ? true : (stripos($tempauthors,$search)===0);

					if (!$match)
						$match=$match ? true : (stripos($val['author'],$search)===0);
				}
				else
				{
					$match=$match ? true : (stripos(strip_tags($val['label']),$search)!==false);

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
					$startLetterOrd=ord(substr(strtolower(strip_tags($val['label'])),0,1));
					$match=$match ? true : ($startLetterOrd<97 || $startLetterOrd>122);
				}
				else
				{
					if ($matchStartOnly)
						$match=$match ? true : (stripos(strip_tags($val['label']),$searchTitle)===0);
					else
					{
						$match=$match ? true : (stripos(strip_tags($val['label']),$searchTitle)!==false);
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
				$data[]=$val;
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
					return strtolower(strip_tags($a['label']))>strtolower(strip_tags($b['label']));
				}

				return strtolower($aa)>strtolower($bb);
			});
		}
		else
		{
			usort($data,function($a,$b){ return strtolower(strip_tags($a['label']))>strtolower(strip_tags($b['label'])); });
		}

		return $data;

	}
	
	private function getReferencedTaxa ($id)
	{
	    $taxa = $this->models->Literature2Model->getReferencedTaxa(array(
	        'project_id' => $this->getCurrentProjectId(),
	        'literature_id' => $id
	    ));
	    
	    foreach((array)$taxa as $key=>$val)
	    {
	        $taxa[$key]['taxon']=
	        $this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['id'],'parent_id'=>$val['parent_id'] ] );
	    }
	    
	    return $taxa;
	}
	
	private function getReferencedTraits ($id) 
	{
	    $d = $this->models->Literature2Model->getReferenceLinksTraits(array(
	        'projectId' => $this->getCurrentProjectId(),
	        'literatureId' => $id
	    ));
	    
	    $traits=array();
	    foreach((array)$d as $val)
	    {
	        $traits[$val['sysname']][]=
	        array(
	            'group_id'=>$val['trait_group_id'],
	            'taxon_id'=>$val['taxon_id'],
	            'taxon'=>$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] )
	        );
	    }
	    
	    return $traits;
	}

    private function getReferenceLinks ($id=null)
    {
		if (empty($id))
			$id=$this->getReferenceId();

		if (empty($id))
			return;

		// NAMES
		$names = $this->models->Literature2Model->getReferenceLinksNames(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'literatureId' => $id
		));

		foreach((array)$names as $key=>$val)
		{
			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
			$names[$key]['name']=
				$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'nametype'=>$val['nametype'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
		}


		// PRESENCE
		$presences = $this->models->Literature2Model->getReferenceLinksPresences(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'literatureId' => $id
		));

		foreach((array)$presences as $key=>$val)
		{
			$presences[$key]['taxon']=$this->addHybridMarkerAndInfixes( [ 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'],'taxon_id'=>$val['taxon_id'],'parent_id'=>$val['parent_id'] ] );
		}

		// TAXA
		$taxa = $this->getReferencedTaxa($id);

		// TRAITS
		$traits = $this->getReferencedTraits($id);

		// RDF > PASSPORTS
		$passports = $this->models->Literature2Model->getReferenceLinksPassports(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'literatureId' => $id
		));

		foreach((array)$passports as $key=>$val)
		{
			$passports[$key]['taxon']=$this->addHybridMarkerAndInfixes( array( 'name'=>$val['taxon'],'base_rank_id'=>$val['base_rank_id'] ) );
		}


		return
			array(
				'names' => $names,
				'presences'=>$presences,
				'traits'=>$traits,
				'passports'=>$passports,
				'taxa'=>$taxa,
			);

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

	private function getLanguages()
	{
        $used = $this->models->Literature2Model->getLanguagesUsed($this->getCurrentProjectId());

		$stuff=null;
		foreach((array)$used as $key => $val)
		{
			$stuff .= "when _c.id = ".$val['language_id']." then ".($key+1)."\n";
		}

		if (!empty($stuff))
		{
			$stuff = ", case ".$stuff." else 0 end as sort_criterium\n";
		}

		return $this->models->Literature2Model->getLanguages(array(
            'projectId' => $this->getCurrentProjectId(),
    		'languageId' => $this->getDefaultProjectLanguage(),
    		'sort' => $stuff
		));
	}

    private function setPublicationTypesSortOrder( $o )
    {
		$this->publicationTypesSortOrder=$o;
	}

    private function getPublicationTypesSortOrder()
    {
		return $this->publicationTypesSortOrder;
	}

    private function setPublicationTypes()
    {
		$d =
            $this->models->Literature2Model->getPublicationTypes(array(
                'projectId' => $this->getCurrentProjectId(),
                'languageId' => $this->getDefaultProjectLanguage(),
                'sortOrder' => $this->getPublicationTypesSortOrder()
            ));


		if ($d)
		{
			$this->publicationTypes=$d;

			foreach((array)$this->publicationTypes as $key=>$val)
			{
				$this->publicationTypes[$key]['translations']=
					$this->models->Literature2PublicationTypesLabels->_get(array('id'=>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'publication_type_id' => $val['id'],
					),'fieldAsIndex'=>'language_id','columns'=>'label'));
			}
		}
	}

    private function initializePublicationTypes()
    {
		$d =
            $this->models->Literature2Model->getPublicationTypes(array(
                'projectId' => $this->getCurrentProjectId(),
                'languageId' => $this->getDefaultProjectLanguage(),
                'sortOrder' => $this->getPublicationTypesSortOrder()
            ));

		if ( empty($d) )
		{
			foreach((array)$this->basePubTypes as $val)
			{
				$this->models->Literature2PublicationTypes->insert(array(
					'project_id'=>$this->getCurrentProjectId(),
					'sys_label'=>$val,
					'created'=>'now()'
				));
			}
		}
	}

    private function getPublicationTypes()
    {
		return $this->publicationTypes;
	}

    private function savePublicationType()
    {
		$type=trim($this->rGetVal('type'));

		if (empty($type))
		{
			$this->addError('Name missing. Publication type could not be saved.');
			return;
		}

		$d=$this->models->Literature2PublicationTypes->_get(array("id"=>
			array(
				'project_id' => $this->getCurrentProjectId(),
				'sys_label' => $type
			)));

		if ($d)
		{
			$this->addError('Name already exists. Publication type could not be saved.');
			return;
		}

		$d=$this->models->Literature2PublicationTypes->save(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'sys_label' => $type
		));

		$this->addMessage(sprintf('Publication type "%s" saved.',$type));

	}

    private function deletePublicationType()
    {

		$id=$this->rGetId();

		if (empty($id))
		{
			$this->addError($this->translate("Cannot delete publication type (no ID)."));
			return;
		}

		foreach($this->getPublicationTypes() as $val)
		{
			if ( $val['id']==$id ) $label=$val['label'];

			if ( $val['id']==$id && $val['total']>0 )
			{
				$this->addError(sprintf('There are %s references with this publication type. Publication type not deleted.',$val['total']));
				return;
			}
		}

		$this->models->Literature2PublicationTypesLabels->delete(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'publication_type_id' => $id,
		));

		$this->models->Literature2PublicationTypes->delete(
		array(
			'project_id' => $this->getCurrentProjectId(),
			'id' => $id,
		));

		$this->addMessage(sprintf('Publication type %s deleted.',$label));

	}

    private function savePublicationTypeTranslations()
    {

		$translations=$this->rGetVal('translations');

		if (empty($translations)) return;

		foreach((array)$translations as $type_id=>$val)
		{
			if ( is_array($val) && !empty($val) )
			{
				foreach((array)$val as $language_id=>$translation)
				{
					if (empty($translation))
					{
						$this->models->Literature2PublicationTypesLabels->delete(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'publication_type_id' => $type_id,
								'language_id' =>$language_id
							)
						);

						if ($this->models->Literature2PublicationTypesLabels->getAffectedRows()!=0)
							$this->addMessage('Translation deleted.');

						continue;
					}


					foreach((array)$this->getPublicationTypes() as $current)
					{
						if ($current['id']!=$type_id) continue;

						if (isset($current['translations']) && isset($current['translations'][$language_id]))
						{
							if ($current['translations'][$language_id]['label']!=$translation)
							{

								$this->models->Literature2PublicationTypesLabels->update(
									array(
										'label' => $translation
									),
									array(
										'project_id' => $this->getCurrentProjectId(),
										'publication_type_id' => $type_id,
										'language_id' =>$language_id,
									)
								);

								$this->addMessage(sprintf('Translation "%s" saved.',$translation));
							}
						}
						else
						{
							$this->models->Literature2PublicationTypesLabels->save(
							array(
								'project_id' => $this->getCurrentProjectId(),
								'publication_type_id' => $type_id,
								'language_id' =>$language_id,
								'label' => $translation
							));

							$this->addMessage(sprintf('Translation "%s" saved.',$translation));
						}
					}
				}
			}
		}
	}

    private function setActors()
	{
		$this->_actors=$this->getActors();
	}

    private function setLiterature()
	{
		$this->_literature=$this->getReferences(array('search'=>'*'));
	}

	private function matchPossibleAuthor($raw)
	{
		if (empty($this->_actors)) return;
		if (empty($raw)) return;

		if(substr_count($raw,",")>0)
		{
			// expected format: Cuppen, J.G.M., Th. Heijerman, P. van Wielink & A. Loomans
			$a=preg_split('/(,)|\&/',$raw);
			$a[1]=trim($a[1]).' '.trim($a[0]);
			array_shift($a);
		}
		else
		{
			$a=array(trim($raw));
		}

		array_walk($a,function(&$val)
		{
			$val=trim(preg_replace('/(\s)+/',' ',$val));
		});

		//echo $raw;
		//q($a);

		$suggestions=array();
		//$best_best_match=0;

		foreach($a as $key=>$suggestedname)
		{
			$best_match=0;
			$suggestions[$key]=array('name'=>$suggestedname,'suggestions'=>array());

			foreach($this->_actors as $actor)
			{
				$name=$actor['label'];
				$name_alt=$actor['name_alt'];

				$pct1=$pct2=0;

				if (!empty($name))
				{
					if(substr_count($name,",")>0)
					{
						$d=explode(",",$name);
						$name=trim($d[1]).' '.trim($d[0]);
					}
					similar_text($suggestedname,$name,$pct1);
				}

				if (!empty($name_alt))
				{
					if(substr_count($name_alt,",")>0)
					{
						$d=explode(",",$name_alt);
						$name_alt=trim($d[1]).' '.trim($d[0]);
					}
					similar_text($suggestedname,$name_alt,$pct2);
				}

				if ($pct1>=$this->_matchThresholdDefault || $pct2>=$this->_matchThresholdDefault)
				{
					/*
					echo
						$suggestedname,' :: ',
						$name,' (',$actors['name'],') ',' / ',$name_alt,
						' (',round($pct1),'% / ',round($pct2),'%)',
						' [',$actors['id'],']',
						'<br />';
					*/

					$suggestions[$key]['suggestions'][]=
						array(
							'id'=>$actor['id'],
							'names'=>
								array(
									'name'=>$actor['label'],
									'swapped'=>$name,
									'name_alt'=>$name_alt,
								),
							'match'=>
								array(
									'name'=>$pct1,
									'name_alt'=>$pct2
								)
						);

					if ($best_match['match']<($pct1>$pct2?$pct1:$pct2))
					{
						$best_match=($pct1>$pct2?$pct1:$pct2);
					}

				}
			}

			$suggestions[$key]['best_match']=$best_match;
			//$best_best_match=($best_best_match<$best_match?$best_match:$best_best_match);

		}

		if (!empty($suggestions))
		{
			foreach((array)$suggestions as $key=>$val)
			{
				if (!empty($val['suggestions']))
				{
 					usort($suggestions[$key]['suggestions'],function($a,$b)
					{
						$a=($a['match']['name']>=$a['match']['name_alt']?$a['match']['name']:$a['match']['name_alt']);
						$b=($b['match']['name']>=$b['match']['name_alt']?$b['match']['name']:$b['match']['name_alt']);
						return ($a>$b ? -1 : ($a<$b ? 1 : 0 ));
					});
				}
			}
		}

		return $suggestions;

	}

	private function matchPossibleLabel($raw)
	{
		if (empty($this->_literature)) return;
		if (empty($raw)) return;

		$suggestions=array();

		foreach($this->_literature as $_literature)
		{
			$label=$_literature['label'];
			$label_alt=$_literature['alt_label'];

			$pct1=$pct2=0;

			if (!empty($label))
			{
				similar_text($raw,$label,$pct1);
			}

			if (!empty($label_alt))
			{
				similar_text($raw,$label_alt,$pct2);
			}

			if ($pct1>=$this->_matchThresholdDefault || $pct2>=$this->_matchThresholdDefault)
			{
				/*
				echo
					'|',$raw,'|','<br />',
					'|',$label,'|','<br />',
					(!empty($label_alt) ? $label_alt.'<br />' : ''),
					' (',round($pct1),'% / ',round($pct2),'%)',
					' [',$_literature['id'],']',
					'<br /><br />';
				*/

				$suggestions[]=
					array(
						'id'=>$_literature['id'],
						'date'=>$_literature['date'],
						'authors'=>$_literature['authors'],
						'authors_literal'=>$_literature['author'],
						'names'=>
							array(
								'label'=>$label,
								'label_alt'=>$label_alt,
							),
						'match'=>
							array(
								'label'=>$pct1,
								'label_alt'=>$pct2
							)
					);
			}
		}

		if (!empty($suggestions))
		{
			usort($suggestions,function($a,$b)
			{
				$a=($a['match']['label']>=$a['match']['label_alt']?$a['match']['label']:$a['match']['label_alt']);
				$b=($b['match']['label']>=$b['match']['label_alt']?$b['match']['label']:$b['match']['label_alt']);
				return ($a>$b ? -1 : ($a<$b ? 1 : 0 ));
			});
		}

		return $suggestions;

	}

	private function matchPossibleReferences($p)
	{
		$lines=isset($p['lines']) ? $p['lines'] : null;
		$ignorefirst=isset($p['ignorefirst']) ? $p['ignorefirst'] : false;
		$fields=isset($p['fields']) ? $p['fields'] : null;

		if (is_null($lines) || is_null($fields)) return null;

		$this->setActors();
		$this->setLiterature();

		$found=array();
		$suggestions=array();

		// go through all lines (= literature references offered by users)
		foreach((array)$lines as $key=>$line)
		{
			if ($ignorefirst && $key==0) continue;

			$date=null;

			// go through each cell of this reference
			foreach((array)$line as $c=>$cell)
			{
				// this is supposed to be the cell containing the author, let's try and match it to database entries
				if(isset($fields[$c]) && $fields[$c]=='author')
				{
					$suggestions[$key]['authors']=$this->matchPossibleAuthor($cell);
					$found['authors']=true;
				}
				else
				// this is supposed to be the cell containing the title, let's try and match it to database entries
				if(isset($fields[$c]) && $fields[$c]=='label')
				{
					$suggestions[$key]['labels']=$this->matchPossibleLabel($cell);
					$found['labels']=true;
				}
				else
				/*
					this is supposed to be the cell containing the date, let's remember it so we can match it to
					the year of suggested titles from the database later
				*/
				if(isset($fields[$c]) && $fields[$c]=='date')
				{
					$date=$cell;
					$found['date']=true;
				}
			}

			// finished all cells for this line, on to post-processing

			// lets see if the remembered date matches those of the retrieved titles
			if (!is_null($date))
			{
				foreach((array)$suggestions[$key]['labels'] as $klab=>$lab)
				{
					$suggestions[$key]['labels'][$klab]['match']['date']=($lab['date']==$date)*100;
				}
			}

			if (isset($suggestions[$key]['labels']) && count($suggestions[$key]['labels'])>0)
			{
				foreach((array)$suggestions[$key]['labels'] as $val)
				{
					if (!empty($val['authors_literal']))
					{
						$suggestions[$key]['authors']=
							array_map('unserialize',array_unique(array_map('serialize',array_merge(
								(array)$suggestions[$key]['authors'],
								$this->matchPossibleAuthor($val['authors_literal'])
							))));
					}
				}
			}

		}

		if ((!isset($found['authors']) || $found['authors']!==true) || (!isset($found['labels']) || $found['labels']!==true))
		{
			$this->addWarning('choose author and title columns to try match (date/year is not required, but might help)');
		}

		return $suggestions;

	}

	private function matchPublicationType( $str )
	{
		foreach( (array)$this->getPublicationTypes() as $key=>$val )
		{
			if ( $val['sys_label']==$str ) return $val['id'];
		}
	}

	private function setReferenceBefore ()
	{
		$this->referenceBefore = $this->getReference();
	}
	
	private function getReferenceBefore( $f=null )
	{
		if ( $f && isset($this->referenceBefore[$f]) )
		{
			return $this->referenceBefore[$f];
		}
		else
		{
			return $this->referenceBefore;
		}
	}

	private function saveReferenceTaxa()
	{
		foreach((array)$this->rGetVal( 'new_taxa' ) as $taxon_id)
		{
			$this->models->Literature2Model->saveTaxonReference(array(
				"project_id"=>$this->getCurrentProjectId(),
				"taxon_id"=>$taxon_id,
				"literature_id"=>$this->getReferenceId()
			));
		}
	}

	private function deleteReferenceTaxa()
	{
		$id=$this->getReferenceId();

		if (empty($id))
		{
			$this->addError($this->translate("Cannot delete reference (no ID)."));
			return;
		}

		$this->models->LiteratureTaxa->delete(array(
			"project_id"=>$this->getCurrentProjectId(),
			"literature_id"=>$id
		));
	}


}
