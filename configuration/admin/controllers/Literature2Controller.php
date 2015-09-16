<?php

include_once ('Controller.php');
include_once ('RdfController.php');

class Literature2Controller extends Controller
{

	private $_lookupListMaxResults=99999;

    public $usedModels = array(
		'literature2',
		'literature_taxon',
		'names',
		'actors',
		'presence_taxa',
		'literature2_authors'
    );
   
    public $controllerPublicName = 'Literatuur (v2)';
    public $cacheFiles = array();
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

	private $referenceId=null;
	
	private $publicationTypes=array(
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
		'Verslag',
		'Website'
	);	

	private $lit2Columns=
		array(
			'-1'=>'',
			'label'=>'titel',
			'alt_label'=>'alt. titel',
			'date'=>'datum',
			'author'=>'auteur(s)',
			'publication_type'=>'type publicatie',
			'citation'=>'citatie',
			'source'=>'bron',
			'publishedin'=>'gepubliceerd in',
			'publisher'=>'uitgever',
			'periodical'=>'periodiek',
			'pages'=>'pagina(s)',
			'volume'=>'volume',
			'external_link'=>'link',
			'-2'=>'',
			'_reference_'=>'reference #',
		);

    public function __construct ()
    {
        parent::__construct();
		$this->initialise();
    }

    public function __destruct ()
    {
        parent::__destruct();
    }

    private function initialise()
    {
		$this->Rdf = new RdfController;
		$this->_matchThresholdDefault=$this->getSetting('literature2_import_match_threshold',$this->_matchThresholdDefault);
    }

    public function indexAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Index'));
		$this->smarty->assign('authorAlphabet',$this->getAuthorAlphabet());
		$this->smarty->assign('titleAlphabet',$this->getTitleAlphabet());
		$this->printPage();
	}

    public function ajaxInterfaceAction ()
    {
        if (!isset($this->requestData['action'])) return;
		$return=null;
		$return=$this->getReferenceLookupList($this->requestData);
        $this->allowEditPageOverlay = false;
		$this->smarty->assign('returnText',$return);
        $this->printPage();
    }

    public function editAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Edit reference'));

		if ($this->rHasId() && $this->rHasVal('action','delete'))
		{
			$this->setReferenceId($this->rGetId());
			$this->deleteReference();
			$this->setReferenceId(null);
			$template='_delete_result';
		} 
		else
		if ($this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->setReferenceId($this->rGetId());
			$this->updateReference();
			
		} 
		if (!$this->rHasId() && $this->rHasVal('action','save'))
		{
			$this->smarty->assign('reference',$this->requestData);
			$this->saveReference();
		}
		if ($this->rHasId())
		{
			$this->setReferenceId($this->rGetId());
		} 

		if ($this->getReferenceId())
		{
			$this->smarty->assign('reference',$this->getReference());
			$this->smarty->assign('links',$this->getReferenceLinks());
		}

		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('actors',$this->getActors());
		$this->smarty->assign('publicationTypes',$this->getPublicationTypes());
		$this->printPage(isset($template) ? $template : null);
	}

    public function bulkUploadAction()
	{
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

		$ignorefirst=$this->rHasVal('ignorefirst','1');
		$this->setSessionVar('ignorefirst',$ignorefirst);

		if ($this->rHasVal('fields'))
		{
			$fields=$this->rGetVal('fields');
			$this->setSessionVar('fields',$fields);
		}
		
		if ($this->rHasVal('raw'))
		{
			$raw=$this->rGetVal('raw');
			$hash=md5($raw);
			if ($hash!=$this->getSessionVar('hash'))
			{
				$this->setSessionVar('delcols',null);
				$this->setSessionVar('match_ref',null);
				$this->setSessionVar('new_ref',null);
				$fields=null;
			}
			$this->setSessionVar('hash',$hash);
			
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
				$this->setSessionVar('delcols',null);
			}
			else
			if ($this->rHasVal('action','delcol') && $this->rHasVal('value'))
			{
				$delcols=(array)$this->getSessionVar('delcols');
				$delcols[$this->rGetVal('value')]=true;
				$this->setSessionVar('delcols',$delcols);
			}

			$this->setSessionVar('lines',$lines);
		}

		if ($this->rHasVal('threshold'))
		{
			$this->_matchThresholdDefault=
				is_numeric($this->rGetVal('threshold')) && 
				$this->rGetVal('threshold')<=100 &&
				$this->rGetVal('threshold')>0 ? 
					$this->rGetVal('threshold') : 
					$this->_matchThresholdDefault;

			$this->setSessionVar('threshold',$this->rGetVal('threshold'));
		}
		
		if ($lines && $fields) 
		{
			$matches=$this->matchPossibleReferences(array('lines'=>$lines,'ignorefirst'=>$ignorefirst,'fields'=>$fields));
			$this->setSessionVar('matches',$matches);

			foreach((array)$lines[0] as $c=>$cell)
			{
				if(isset($fields[$c]) && $fields[$c]=='author')
				{
					$this->setSessionVar('field_author',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='label')
				{
					$this->setSessionVar('field_label',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='date')
				{
					$this->setSessionVar('field_date',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='publishedin')
				{
					$this->setSessionVar('field_publishedin',$c);
				}
				else
				if(isset($fields[$c]) && $fields[$c]=='periodical')
				{
					$this->setSessionVar('field_periodical',$c);
				}
			}


			//q($matches,1);

		}

		$this->smarty->assign('field_author',$this->getSessionVar('field_author'));
		$this->smarty->assign('field_label',$this->getSessionVar('field_label'));
		$this->smarty->assign('field_date',$this->getSessionVar('field_date'));
		$this->smarty->assign('field_publishedin',$this->getSessionVar('field_publishedin'));
		$this->smarty->assign('field_periodical',$this->getSessionVar('field_periodical'));

		$this->smarty->assign('threshold',$this->_matchThresholdDefault);
		$this->smarty->assign('matches',$matches);
		$this->smarty->assign('emptycols',$emptycols);
		$this->smarty->assign('fields',$fields);
		$this->smarty->assign('cols',$this->lit2Columns);
		$this->smarty->assign('delcols',$this->getSessionVar('delcols'));
		$this->smarty->assign('raw',$raw);
		$this->smarty->assign('ignorefirst',$ignorefirst);
		$this->smarty->assign('firstline',$firstline);
		$this->smarty->assign('lines',$lines);

		$this->printPage();
	}

    public function bulkProcessAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Bulk upload (further matching)'));

		$matches=null;
		$match_ref=null;
		$new_ref=null;
		$duplicate_columns=null;
		$matching_authors=null;
		$matching_publishedin=null;
		$matching_periodical=null;

		$ignorefirst=$this->getSessionVar('ignorefirst');
		$lines=$this->getSessionVar('lines');
		$fields=$this->getSessionVar('fields');
		$matches=$this->getSessionVar('matches');

		if ($this->rHasVal('match_ref'))
		{
			$match_ref=$this->rGetVal('match_ref');
			$this->setSessionVar('match_ref',$match_ref);
		}
		
		if ($this->rHasVal('new_ref'))
		{
			$new_ref=$this->rGetVal('new_ref');
			$this->setSessionVar('new_ref',$new_ref);

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

		$this->smarty->assign('field_author',$this->getSessionVar('field_author'));
		$this->smarty->assign('field_label',$this->getSessionVar('field_label'));
		$this->smarty->assign('field_date',$this->getSessionVar('field_date'));
		$this->smarty->assign('field_publishedin',$this->getSessionVar('field_publishedin'));
		$this->smarty->assign('field_periodical',$this->getSessionVar('field_periodical'));

		$this->smarty->assign('match_ref',$match_ref);
		$this->smarty->assign('new_ref',$new_ref);

		$this->smarty->assign('duplicate_columns',$duplicate_columns);

		$this->smarty->assign('matching_authors',$matching_authors);
		$this->smarty->assign('matching_publishedin',$matching_publishedin);
		$this->smarty->assign('matching_periodical',$matching_periodical);
		$this->smarty->assign('languages',$this->getLanguages());
		$this->smarty->assign('default_language',$this->getDefaultProjectLanguage());
	
		$this->printPage();
	}

    public function bulkSaveAction()
	{
		$this->checkAuthorisation();
		$this->setPageName($this->translate('Bulk upload (saving)'));
		
		if (!$this->isFormResubmit())
		{
			$fields=$this->getSessionVar('fields');
			$lines=$this->getSessionVar('lines');
			$match_ref=$this->getSessionVar('match_ref');
			$new_ref=$this->getSessionVar('new_ref');
			$field_author=$this->getSessionVar('field_author');
			$field_label=$this->getSessionVar('field_label');
			$field_date=$this->getSessionVar('field_date');
			$field_publishedin=$this->getSessionVar('field_publishedin');
			$field_periodical=$this->getSessionVar('field_periodical');
			$lpad=$this->getSessionVar('lpad');
			$infix=$this->getSessionVar('infix');
			$rpad=$this->getSessionVar('rpad');
	
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
			
			$this->setSessionVar('literature_id_index',$literature_id_index);
			
			$ref=array_search( '_reference_', $fields );
			if ( $ref!==false )
			{
				$this->smarty->assign( 'have_ref_col', true );
			}
			
		}
		
		$this->addmessage($this->translate('Done.'));

		$this->printPage();
	}
	
	private function downloadHeaders( $file )
	{
		header( 'Content-Type: text/plain; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $file );
		header( 'Pragma: no-cache' );
	}

	public function bulkUploadDownloadAction()
	{
		$this->checkAuthorisation();

		$literature_id_index=$this->getSessionVar('literature_id_index');

		$fields=$this->getSessionVar('fields');
		$ref_col=array_search( '_reference_',  $fields );
		
		$buffer_line=array();
		$buffer=array();

		foreach((array)$this->getSessionVar('lines') as $key=>$line)
		{
			if ( $this->rHasVal( "action", "ref_only" )  && $ref_col!==false && $this->getSessionVar('ignorefirst') && $key==0 )
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



	private function setSessionVar($var,$val=null)
	{
		if (is_null($val))
		{
			unset($_SESSION['admin']['system']['literature2'][$var]);
		}
		else
		{
			$_SESSION['admin']['system']['literature2'][$var]=$val;
		}
	}

	private function getSessionVar($var)
	{
		return isset($_SESSION['admin']['system']['literature2'][$var]) ? $_SESSION['admin']['system']['literature2'][$var] : null;
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
			$this->addError('Geen titel. Referentie niet opgeslagen.');
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
			$this->addMessage('Nieuw referentie aangemaakt.');
			$this->updateReference();
		}
		else 
		{
			$this->addError('Aanmaak nieuwe referentie mislukt.');
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
			'publication_type' => 'Publicatietype',
			'citation' => 'Citatie',
			'source' => 'Bron',
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
						$this->addMessage($label.' opgeslagen.');
				}
				else
				{
					$this->addError($label.' niet opgeslagen.');
				}
			}
		}
		

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
					$this->addMessage('Auteur verwijderd.');
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
					$this->addMessage('Auteur toegevoegd.');
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
			$this->addError("Geen ID.");
			return;
		}

        $this->models->Names->freeQuery(
			"update %PRE%names set reference_id = null where project_id = ".$this->getCurrentProjectId()." and reference_id = ".$id
		);
		$this->addMessage("Referentie verwijderd van ".$this->models->Names->getAffectedRows()." namen.");

        $this->models->PresenceTaxa->freeQuery(
			"update %PRE%presence_taxa set reference_id = null where project_id = ".$this->getCurrentProjectId()." and reference_id = ".$id
		);
		$this->addMessage("Referentie verwijderd van ".$this->models->PresenceTaxa->getAffectedRows()." statussen.");

		$this->deleteReferenceAuthors();

		$this->addMessage("Auteurs ontkoppeld.");

		$this->models->Literature2->freeQuery("delete from %PRE%literature2 where project_id = ".$this->getCurrentProjectId()." and id = ".$id." limit 1");	
		
		$this->addMessage("Referentie verwijderd.");

	}

	private function getReferenceAuthors($id=null)
	{
		if (empty($id))
		{
			$id=$this->getReferenceId();
			if (empty($id))
			{
				$this->addError("Geen ID.");
				return;
			}
		}

		$d=$this->models->Literature2Authors->freeQuery("
			select
				_a.actor_id, _b.name
	
			from %PRE%literature2_authors _a
	
			left join %PRE%actors _b
				on _a.actor_id = _b.id 
				and _a.project_id=_b.project_id
	
			where
				_a.project_id = ".$this->getCurrentProjectId()."
				and _a.literature2_id =".$id."
			order by _b.name
		");
		
		return $d;
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

		if (empty($id)||empty($actor))
		{
			$this->addError("Geen ID.");
			return;
		}

		$this->models->Literature2Authors->freeQuery("delete from %PRE%literature2_authors where project_id = ".$this->getCurrentProjectId()." and literature2_id = ".$id." and actor_id = ".$actor." limit 1");	
	}

	private function deleteReferenceAuthors()
	{
		$id=$this->getReferenceId();

		if (empty($id))
		{
			$this->addError("Geen ID.");
			return;
		}

		$this->models->Literature2Authors->freeQuery("delete from %PRE%literature2_authors where project_id = ".$this->getCurrentProjectId()." and literature2_id = ".$id);	
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

	private function getReference($id=null)
	{
		if (empty($id))
			$id=$this->getReferenceId();

		if (empty($id))
			return;

		$l=$this->models->Literature2->freeQuery(
			"select
				_a.*,
				_h.label as publishedin_label,
				_i.label as periodical_label

			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()." 
				and _a.id = ".$id
		);

		
		if ($l)
		{
			$authors=$this->models->Literature2Authors->freeQuery("
				select
					_a.actor_id, _b.name
	
				from %PRE%literature2_authors _a
	
				left join %PRE%actors _b
					on _a.actor_id = _b.id 
					and _a.project_id=_b.project_id
	
				where
					_a.project_id = ".$this->getCurrentProjectId()."
					and _a.literature2_id =".$id."
				order by _a.sort_order, _b.name
			");
		
			$l[0]['authors']=$authors;
			
			return $l[0];
		}

	}

    private function getReferences($p)
    {
        $search=isset($p['search']) ? $p['search'] : null;
        $matchStartOnly = isset($p['match_start']) ? $p['match_start']==1 : false;
        $searchTitle=isset($p['search_title']) ? $p['search_title'] : null;
        $searchAuthor=isset($p['search_author']) ? $p['search_author'] : null;
        $publicationType=isset($p['publication_type']) ? $p['publication_type'] : null;

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


		$all=$this->models->Literature2->freeQuery(
			"select
				_a.id,
				_a.language_id,
				_a.label,
				_a.alt_label,
				_a.alt_label_language_id,
				_a.date,
				_a.author,
				_a.publication_type,
				_a.citation,
				_a.source,
				ifnull(_a.publishedin,ifnull(_h.label,null)) as publishedin,
				ifnull(_a.periodical,ifnull(_i.label,null)) as periodical,
				_a.pages,
				_a.volume,
				_a.external_link
				
			from %PRE%literature2 _a

			left join  %PRE%literature2 _h
				on _a.publishedin_id = _h.id 
				and _a.project_id=_h.project_id

			left join %PRE%literature2 _i 
				on _a.periodical_id = _i.id 
				and _a.project_id=_i.project_id

			where
				_a.project_id = ".$this->getCurrentProjectId()."
				".(isset($publicationType) ? 
					"and ".
					(is_array($publicationType) ? 
						"_a.publication_type in ('" . implode("','",array_map('mysql_real_escape_string',$publicationType)). "')" : 
						"_a.publication_type = '" . mysql_real_escape_string($publicationType) . "'") : 
					"" )."
			");	

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
				$data[]=$val;
			}
				 
		}

		if (!empty($searchAuthor))
		{
			usort($data,function($a,$b)
			{ 
				$aa=isset($a['authors'][0]['name']) ? $a['authors'][0]['name'] : $a['author'];
				$bb=isset($b['authors'][0]['name']) ? $b['authors'][0]['name'] : $b['author'];
				return strtolower($aa)>strtolower($bb);
			});
		}
		else
		{
			usort($data,function($a,$b){ return strtolower($a['label'])>strtolower($b['label']); });
		}

		return $data;
		
	}

    private function getReferenceLinks($id=null)
    {
		if (empty($id))
			$id=$this->getReferenceId();

		if (empty($id))
			return;

		// NAMES
        $names=$this->models->Names->freeQuery("
			select
				_a.taxon_id,
				_a.name,
				_b.nametype,
				_c.language,
				_d.label as language_label,
				_g.taxon

			from %PRE%names _a 

			left join %PRE%name_types _b
				on _a.type_id=_b.id 
				and _a.project_id=_b.project_id

			left join %PRE%languages _c
				on _a.language_id=_c.id

			left join %PRE%labels_languages _d
				on _a.language_id=_d.language_id
				and _a.project_id=_d.project_id
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id 
				and _a.project_id=_g.project_id

		where
			_a.project_id = ".$this->getCurrentProjectId()."
			and _a.reference_id=".$id
		);
		
		foreach((array)$names as $key=>$val)
		{
			$names[$key]['nametype_label']=sprintf($this->Rdf->translatePredicate($val['nametype']),$val['language_label']);
		}
		// NAMES

		// PRESENCE
		$presences=$this->models->PresenceTaxa->freeQuery(
			"select
				_a.taxon_id,
				_g.taxon,
				_a.presence_id,
				_b.label as presence_label,
				_a.reference_id
				
			from %PRE%presence_taxa _a

			left join %PRE%taxa _g
				on _a.taxon_id = _g.id 
				and _a.project_id=_g.project_id

			left join %PRE%presence_labels _b
				on _a.presence_id = _b.presence_id 
				and _a.project_id=_b.project_id 
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			where _a.project_id = ".$this->getCurrentProjectId()."
				and _a.reference_id=".$id
		);	
		// PRESENCE
		
		// TRAITS
		$d=$this->models->Literature2->freeQuery("
			select
				_a.taxon_id,
				_b.taxon,
				_a.trait_group_id,
				_c.sysname
			from
				%PRE%traits_taxon_references _a
				
			left join %PRE%taxa _b
				on _a.project_id=_b.project_id
				and _a.taxon_id=_b.id
			
			left join %PRE%traits_groups _c
				on _a.project_id=_c.project_id
				and _a.trait_group_id=_c.id
				
			where
				_a.project_id=".$this->getCurrentProjectId()." 
				and _a.reference_id=".$id."
			order
				by _b.taxon
		");
		
		$traits=array();
		foreach((array)$d as $val)
		{
			$traits[$val['sysname']][]=
				array(
					'group_id'=>$val['trait_group_id'],
					'taxon_id'=>$val['taxon_id'],
					'taxon'=>$val['taxon']
				);
		}
		// TRAITS
		
		// RDF > PASSPORTS
		$passports=$this->models->Literature2->freeQuery("		
		select _b.taxon_id, _e.taxon, _d.title
			from 
				%PRE%rdf _a
		
			left join
				%PRE% content_taxa _b
				on _a.subject_id=_b.id
				and _a.project_id=_b.project_id
				and _b.language_id=".$this->getDefaultProjectLanguage()."

			left join
				%PRE% taxa _e
				on _b.taxon_id=_e.id
				and _a.project_id=_e.project_id

			left join
				%PRE% pages_taxa _c
				on _b.page_id=_c.id
				and _a.project_id=_c.project_id

			left join
				%PRE% pages_taxa_titles _d
				on _c.id=_d.page_id
				and _c.project_id=_d.project_id
				and _d.language_id=".$this->getDefaultProjectLanguage()."


			where 
				_a.project_id=".$this->getCurrentProjectId()."
				and _a.object_type = 'reference'
				and _a.subject_type = 'passport'
				and _a.object_id = ".$id."
				
			order by
				_e.taxon, _c.show_order
				
		");
		// RDF > PASSPORTS

		return array(
			'names' => $names,
			'presences'=>$presences,
			'traits'=>$traits,
			'passports'=>$passports,
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

	private function getActors()
	{
		return $this->models->Actors->freeQuery(
			"select
				_e.id,
				_e.name as label,
				_e.name_alt,
				_e.homepage,
				_e.gender,
				_e.is_company,
				_e.employee_of_id,
				_f.name as company_of_name,
				_f.name_alt as company_of_name_alt,
				_f.homepage as company_of_homepage

			from %PRE%actors _e

			left join %PRE%actors _f
				on _e.employee_of_id = _f.id 
				and _e.project_id=_f.project_id

			where
				_e.project_id = ".$this->getCurrentProjectId()."

			order by
				_e.is_company, _e.name
		");	
	}
		
	private function getLanguages()
	{
        $used=$this->models->Names->freeQuery("
				select count(id) as `count`, language_id
				from %PRE%names
				where project_id=".$this->getCurrentProjectId()."
				group by language_id
				order by `count` asc
		");
		
		$stuff=null;
		foreach((array)$used as $key => $val)
		{
			$stuff .= "when _c.id = ".$val['language_id']." then ".($key+1)."\n";
		}
		
		if (!empty($stuff))
		{
			$stuff = ", case ".$stuff." else 0 end as sort_criterium\n";
		}

        $languages=$this->models->Language->freeQuery("
			select
				_c.id,
				_c.language,
				ifnull(_d.label,_c.language) as label
				".$stuff."
			from %PRE%languages _c

			left join %PRE%labels_languages _d
				on _c.id=_d.language_id
				and _d.project_id = ".$this->getCurrentProjectId()."
				and _d.label_language_id=".$this->getDefaultProjectLanguage()."
				order by ".(!empty($stuff) ? "sort_criterium desc, " : "")."label asc
			");

		return $languages;
	}

    private function getPublicationTypes()
    {
		$d=$this->models->Literature2->freeQuery("
				select 
					distinct publication_type 
				from 
					%PRE%literature2 
				where 
					publication_type is not null 
				order by 
					publication_type
			");

		foreach((array)$d as $val)
		{
			$this->publicationTypes[]=$val['publication_type'];
		}

		$x=array_unique($this->publicationTypes);
		array_walk($x,function(&$a){ $a=$this->translate($a);q($a);});
		
		return $x;
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
		
		


	
}
