<?php

include_once ('NsrController.php');
include_once ('ModuleSettingsReaderController.php');

class NsrTaxonImportController extends NsrController
{
    public $modelNameOverride='NsrTaxonModel';
    public $controllerPublicName = 'Taxon editor';

    public $usedModels = array(
		'tab_order',
		'pages_taxa',
		'pages_taxa_titles',
		'labels_languages',
		'content_taxa'
	);

    public $usedHelpers = array(
        'csv_parser_helper',
        'file_upload_helper',
    );

	private $importColumns = [ 'conceptName' => 0, 'rank' => 1, 'parent' => 2, 'commonName' => 3 ];
	private $importRows = [ 'topicNames' => 0, 'languages' => 1 ];
	private $doNotImport=[];
	private $_nameTypeIds;

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
		$this->_nameTypeIds=$this->models->NameTypes->_get(array(
			'id'=>array(
				'project_id'=>$this->getCurrentProjectId()
			),
			'columns'=>'id,nametype',
			'fieldAsIndex'=>'nametype'
		));
		$this->lines = new stdClass;
	}

    public function importFileAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxon file upload'));

        if ($this->requestDataFiles)
		{
            unset($_SESSION['admin']['system']['csv_data']);

            switch ($this->rGetVal("delimiter"))
			{
                case 'tab':
                    $this->helpers->CsvParserHelper->setFieldDelimiter("\t");
                    break;
                case 'semi-colon':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(';');
                    break;
				default:
                //case 'comma':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(',');
            }

            $this->helpers->CsvParserHelper->parseFile( $this->requestDataFiles[0]["tmp_name"] );

            $this->addError( $this->helpers->CsvParserHelper->getErrors() );

            if (!$this->getErrors())
			{
				$this->setLines( $this->helpers->CsvParserHelper->getResults() );
				$this->cleanLines();
				$this->addLineId();
				$this->checkConcepts();
				$this->checkRanks();
				$this->checkParents();
				$this->checkNames();
				$this->setSessionLines();

				$this->smarty->assign( 'lines', $this->getLines() );
				$this->smarty->assign( 'importColumns', $this->importColumns );
			}
			
		}
		
        $pr = $this->getProjectRanks();

        $this->smarty->assign('projectRanks', $pr);
        $this->printPage();
	}

    public function importFileResetAction()
    {
		$this->setLines( null );
		$this->setSessionLines();
		$this->redirect( 'import_file.php' );
	}

    public function importFileProcessAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Processing taxon file upload'));

		$lines=$this->getSessionLines();
		$this->setLines( $lines );

		if ( $this->rHasVar('action','save') && !$this->isFormResubmit() )
		{
			$this->setDoNotImport( $this->rGetVal('do_not_import') );
			$this->saveConcepts();
			$this->saveParents();
			$this->saveNames();
			$this->setSessionLines();

			$this->smarty->assign( 'lines', $this->getLines() );
			$this->smarty->assign( 'importColumns', $this->importColumns );

		}
		
        $this->printPage();
	
	}

    public function importPassportFileAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );
		$this->checkAuthorisation();

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Taxon passport content upload'));

        if ($this->requestDataFiles)
		{
            unset($_SESSION['admin']['system']['csv_data']);

            switch ($this->rGetVal("delimiter"))
			{
                case 'tab':
                    $this->helpers->CsvParserHelper->setFieldDelimiter("\t");
                    break;
                case 'semi-colon':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(';');
                    break;
				default:
                //case 'comma':
                    $this->helpers->CsvParserHelper->setFieldDelimiter(',');
            }

            $this->helpers->CsvParserHelper->setMaxLineLength( 100000 );
            $this->helpers->CsvParserHelper->parseFile( $this->requestDataFiles[0]["tmp_name"] );

            $this->addError( $this->helpers->CsvParserHelper->getErrors() );

            if (!$this->getErrors())
			{
				$this->setLines( $this->helpers->CsvParserHelper->getResults() );
				$this->cleanLines();
				$this->prepPassportData();
				$this->checkLanguages();
				$this->checkTopics();
				$this->setSessionLines();
				$this->smarty->assign( 'lines', $this->getLines() );
			}
		}

		$this->smarty->assign( 'importColumns', $this->importColumns );
		$this->smarty->assign( 'importRows', $this->importRows );
		$this->smarty->assign( 'categories', $this->getCategories() );
		$this->smarty->assign( 'languages', $this->getProjectLanguages() );

        $this->printPage();
	}

    public function importPassportFileResetAction()
    {
		$this->setLines( null );
		$this->setSessionLines();
		$this->redirect( 'import_passport_file.php' );
	}

    public function importPassportFileProcessAction()
    {
		$this->UserRights->setActionType( $this->UserRights->getActionCreate() );

        $this->checkAuthorisation();

        $this->setPageName($this->translate('Processing taxon passport file upload'));

		$lines=$this->getSessionLines();
		$this->setLines( $lines );

		if ( $this->rHasVar('action','save') && !$this->isFormResubmit() )
		{
			$this->setHandleExisting( $this->rGetVal('handle_existing') );
			$this->saveTopics();

			$this->smarty->assign( 'lines', $this->getLines() );

		}
		
        $this->printPage();
	
	}

    public function importPassportFileExampleAction()
    {
        $this->checkAuthorisation();

		$taxa=$this->models->Taxa->_get( [
			'id' => [ 'project_id' => $this->getCurrentProjectId() ],
			'order' => 'rank_id desc, taxon asc',
			'limit'=> 10,
			'columns'=>'taxon'
		] );

		$this->smarty->assign( 'taxa', $taxa );
		$this->smarty->assign( 'categories', $this->getCategories() );
		$this->smarty->assign( 'languages', $this->getProjectLanguages() );

		header('Content-disposition:attachment;filename=example-taxon-passport-import-file.csv');
		header('Content-type:text/csv; charset=utf-8');
		
        $this->printPage();
	}



    private function saveTopics()
    {
		$lines=$this->getLines();
		$results=array();

		foreach((array)$lines['data'] as $key1=>$line)
		{
			foreach((array)$line['data'] as $key2=>$val)
			{
				$results[$key1]['cells'][$key2]['saved']=true;
				
				if ( isset($line['taxon']['error']) ) 
				{
					$results[$key1]['cells'][$key2]['saved']=false;
					$results[$key1]['cells'][$key2]['errors'][]=$line['taxon']['error'];
				}

				if ( isset($lines['languages'][$key2]['error']) ) 
				{
					$results[$key1]['cells'][$key2]['saved']=false;
					$results[$key1]['cells'][$key2]['errors'][]=$lines['languages'][$key2]['error'];
				}

				if ( isset($lines['topics'][$key2]['error']) ) 
				{
					$results[$key1]['cells'][$key2]['saved']=false;
					$results[$key1]['cells'][$key2]['errors'][]=$lines['topics'][$key2]['error'];
				}


				if ( $results[$key1]['cells'][$key2]['saved']==false )
				{
					continue;
				}
			
				$where=[
					'project_id'=>$this->getCurrentProjectId(),
					'taxon_id'=>$line['taxon']['id'],
					'language_id'=>$lines['languages'][$key2]['language_id'],
					'page_id'=>$lines['topics'][$key2]['page_id'],
				];
	
				$d=$this->models->ContentTaxa->_get( [ 'id'=> $where ] );
				
				if ( $d ) 
				{
					$handle_existing=$this->getHandleExisting();
					
					if ( $handle_existing=='skip' )
					{
						$results[$key1]['cells'][$key2]['saved']=false;
						$results[$key1]['cells'][$key2]['errors'][]=$this->translate( 'skipped (data exists)' );
						continue;
					}
	
					$where=[
						'id'=>$d[0]['id'],
						'project_id'=>$this->getCurrentProjectId(),
						'taxon_id'=>$line['taxon']['id'],
						'language_id'=>$lines['languages'][$key2]['language_id'],
						'page_id'=>$lines['topics'][$key2]['page_id'],
					];
				
					if ( $handle_existing=='overwrite' ) $data=$val;
					if ( $handle_existing=='append' ) $data=$d[0]['content'] . "\n" . $val;
					if ( $handle_existing=='prepend' ) $data=$val . "\n" . $d[0]['content'];
					
					$this->models->ContentTaxa->update( [ 'content'=>$data, 'publish'=>1 ], $where );
				}
				else
				{
					$this->models->ContentTaxa->save( array_merge( [ 'content'=>$val, 'publish'=>1 ], $where ) );
				}				
			}
		}	
		
		$lines['results']=$results;

		$this->setLines( $lines );

	}
 
    private function setLines( $lines )
    {
		$this->lines=$lines;
	}

    private function getLines()
    {
		return $this->lines;
	}
	
    private function setSessionLines()
    {
		$this->moduleSession->setModuleSetting( [ 'setting' => 'lines', 'value' => $this->getLines() ] );
	}

    private function getSessionLines()
    {
		return $this->moduleSession->getModuleSetting( 'lines' );
	}	
	
    private function cleanLines()
    {
		$lines=$this->getLines();
		array_walk( $lines, function(&$a) { array_walk( $a, function(&$b) { $b=trim($b); } ); } );
		$this->setLines( $lines );
	}
	
    private function addLineId()
    {
		$lines=$this->getLines();
		array_walk( $lines, function(&$a) { $a['line_id']=md5( ++$this->tmp); } );
		$this->setLines( $lines );
	}	

    private function conceptNameWellFormed( $name ) 
	{
		return ( strlen( $name ) > 0 && strlen( $name ) < 255 );
	}

    private function checkConcepts()
    {
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( empty($val[$this->importColumns['conceptName']]))
			{ 
				$lines[$key]['errors'][]=[ 'message' => $this->translate('no concept name') ];
				continue;
			}
			
			$name=$val[$this->importColumns['conceptName']];
			$key2=array_search( $name, array_column($lines, $this->importColumns['conceptName'] ) );
			
			if ( $key2!==false && $key2!==$key ) 
			{
				$lines[$key2]['errors'][]=[ 'message' => $this->translate('duplicate name in list') ];
			}
		}

		foreach((array)$lines as $key=>$val)
		{
			if ( empty($val[$this->importColumns['conceptName']])) continue;

			$name=$val[$this->importColumns['conceptName']];
			
			if ( !$this->conceptNameWellFormed( $name ) ) 
			{
				$lines[$key]['errors'][]=[ 'message' => $this->translate('name not well formed') ];
				continue;
			}
			
			$d=$this->models->Taxa->_get( [ 'id' =>
				[
					'project_id' => $this->getCurrentProjectId(),
					'taxon like' => strtolower($name) . '%'
				]
			] );

			if ( $d )
			{
				if ( strtolower($d[0]['taxon'])==strtolower($name) )
				{
					$lines[$key]['errors'][]=[ 'message' => sprintf($this->translate('"%s" already exists in database'), $name), 'data' => [ 'id' => $d[0]['id'], 'taxon' => $d[0]['taxon'] ] ];
				}
				else
				{
					$lines[$key]['warnings'][]=[ 'message' => $this->translate('similar taxon exists in database'), 'data' => [ 'id' => $d[0]['id'], 'taxon' => $d[0]['taxon'] ] ];
				}
			}
		}
		
		$this->setLines( $lines );
	}

    private function checkRanks()
    {
		$lines=$this->getLines();
		$validranks=$this->getProjectRanks();

		foreach((array)$lines as $key=>$val)
		{
			if ( !isset($val[$this->importColumns['rank']])) continue;

			$rank=$val[$this->importColumns['rank']];

			$key2=array_search( $rank, array_column( $validranks, 'rank' ) );
			
			if ( $key2===false ) 
			{
				$lines[$key]['errors'][]=[ 'message' => sprintf($this->translate('illegal rank "%s"'), $rank) ];
			}
			else
			{
				$lines[$key]['rank_id']=$validranks[$key2]['id'];
			}
		}

		$this->setLines( $lines );
	}

    private function checkParents()
    {
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( !isset($val[$this->importColumns['parent']])) 
			{
				$lines[$key]['warnings'][]=[ 'message' => $this->translate('no valid parent found (will save taxon as orphan)') ];
				continue;
			}
			
			$parent=$val[$this->importColumns['parent']];

			$key2=array_search( $parent, array_column($lines, $this->importColumns['conceptName'] ) );

			if ( $key2!==false )
			{
				
				if ( ( !isset($lines[$key2]['errors']) || count((array)$lines[$key2]['errors'])==0 ) && $key2!==$key )
				{
					$lines[$key]['parent_id']=[ 'source' => 'new', 'id' => $lines[$key2]['line_id'] ];
				}
				else
				{
					if ( isset($lines[$key2]['errors']) && count((array)$lines[$key2]['errors'])>0  )
					{
						$lines[$key]['warnings'][]=[ 'message' => sprintf( $this->translate('proposed parent "%s" has errors'), $parent) ];
					}
					
					if ( $key2==$key )
					{
						$lines[$key]['warnings'][]=[ 'message' => $this->translate('taxon cannot be its own parent') ];
					}

					$d=$this->getTaxonByName(strtolower($parent));
					
					if ( $d )
					{
						$lines[$key]['parent_id']=[ 'source' => 'existing', 'id' => $d['id'] ];
						$lines[$key]['warnings'][]=[ 'message' => $this->translate('will use valid parent from database'), 'data' => [ 'taxon'=>$d['taxon'], 'rank'=>$d['rank'], 'id'=>$d['id'] ] ];
					}
					else
					{
						$lines[$key]['warnings'][]=[ 'message' => $this->translate('no valid parent found (will save taxon as orphan)') ];
					}

				}
			}
		}

		$this->setLines( $lines );
	}

    private function checkNames()
    {
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( !isset($val[$this->importColumns['commonName']])) continue;

			$name=$val[$this->importColumns['commonName']];
			
			// checks go here

			$lines[$key]['common_names'][]=[ 'name' => $name, 'language_id' => $this->getDefaultProjectLanguage() ];
		}

		$this->setLines( $lines );
	}

    private function checkLanguages()
    {
		$lines=$this->getLines();
		$line=$lines[$this->importRows['languages']];

		foreach((array)$line as $key=>$language)
		{
			if ( $key==0 ) continue;

			if ( empty($language) )
			{ 
				$lines['languages'][$key]=[ 'column' => $language, 'error'=> $this->translate('no language') ];
				continue;
			}
			
			$d=$this->models->Languages->_get( [ 'id' =>
				[
					'language' => strtolower($language)
				]
			] );
			
			if ( $d ) 
			{
				$lines['languages'][$key]=[ 'column' => $language, 'language_id'=> $d[0]['id'] ];
			}
			else
			{
				$d=$this->models->LabelsLanguages->_get( [ 'id' =>
					[
						'project_id' => $this->getCurrentProjectId(),
						'label' => strtolower($language)
					]
				] );

				if ( $d ) 
				{
					$lines['languages'][$key]=[ 'column' => $language, 'language_id'=> $d[0]['language_id'] ];
				}
				else
				{
					$lines['languages'][$key]=[ 'column' => $language, 'error'=> $this->translate('unknown language') ];
				}

			}
		}
		
		$this->setLines( $lines );

	}

    private function checkTopics()
    {
		$lines=$this->getLines();
		$line=$lines[$this->importRows['topicNames']];

		foreach((array)$line as $key=>$topic)
		{
			if ( $key==0 ) continue;

			if ( empty($topic) )
			{ 
				$lines['topics'][$key]=[ 'column' => $topic, 'error'=> $this->translate('no topic') ];
				continue;
			}
			
			$d=$this->models->PagesTaxaTitles->_get( [ 'id' =>
				[
					'project_id' => $this->getCurrentProjectId(),
					'title' => strtolower($topic)
				]
			] );
			
			if ( count((array)$d)>1 ) 
			{
				$lines['topics'][$key]=[ 'column' => $topic, 'error'=> $this->translate('there are multiple topics with that name.') ];
			}
			else
			if ( $d ) 
			{
				$lines['topics'][$key]=[ 'column' => $topic, 'page_id'=> $d[0]['page_id'] ];
			}
			else
			{
				$d=$this->models->PagesTaxa->_get( [ 'id' =>
					[
						'project_id' => $this->getCurrentProjectId(),
						'page' => strtolower($topic)
					]
				] );

				if ( $d ) 
				{
					$lines['topics'][$key]=[ 'column' => $topic, 'page_id'=> $d[0]['id'] ];
				}
				else
				{
					$lines['topics'][$key]=[ 'column' => $topic, 'error'=> $this->translate('unknown topic') ];
				}

			}
		}
		
		$this->setLines( $lines );

	}

    private function prepPassportData()
    {
		$lines=$this->getLines();
		
		$d=array();
		$i=0;

		foreach((array)$lines as $i=>$line)
		{
			if ( array_search( $i, $this->importRows)!==false ) continue;

			foreach((array)$line as $key=>$cell)
			{
				if ( $key==$this->importColumns['conceptName'] ) 
				{
					$d[$i]['taxon']['conceptName']=$cell;
					
					$taxon=$this->models->Taxa->_get( [ 'id' =>
						[
							'project_id' => $this->getCurrentProjectId(),
							'taxon' => strtolower($cell)
						]
					] );
					
					if ( $taxon ) 
					{
						$d[$i]['taxon']['id']=$taxon[0]['id'];
					}
					else
					{
						$d[$i]['taxon']['error']=$this->translate('unknown taxon');
					}
				}
				else
				{
					$d[$i]['data'][$key]=$cell;
				}
			}
			
			$i++;

		}

		$lines['data']=$d;
		
		$this->setLines( $lines );

	}

	private function setDoNotImport( $ids )
	{
		$this->doNotImport=$ids;
	}

	private function getDoNotImport()
	{
		return $this->doNotImport;
	}

	private function setHandleExisting( $setting )
	{
		$this->handleExisting=$setting;
	}

	private function getHandleExisting()
	{
		return $this->handleExisting;
	}

    private function saveName( $p )
	{
		$project_id=isset($p['project_id']) ? $p['project_id'] : null;
		$taxon_id=isset($p['taxon_id']) ? $p['taxon_id'] : null;
		$name=isset($p['name']) ? $p['name'] : null;
		$language_id=isset($p['language_id']) ? $p['language_id'] : null;
		$type_id=isset($p['type_id']) ? $p['type_id'] : null;

		if ( is_null($project_id) || is_null($taxon_id) || is_null($name) || is_null($language_id) || is_null($type_id) )
			return;

		$d=$this->models->Names->save( [
			'project_id' => $project_id,
			'taxon_id' => $taxon_id,
			'language_id' => $language_id,
			'type_id' => $type_id,
			'name' => $name
		] );
		
		if ( $d ) 
		{
			return $this->models->Names->getNewId();
		}
	}
	
    private function saveConcepts()
    {
		$ignore=$this->getDoNotImport();
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( isset($val['errors']) && count((array)$val['errors'])>0 )
			{
				$lines[$key]['import_messages'][]=$this->translate('skipped (due to errors)');
				$lines[$key]['saved']=false;
				continue;
			}
			
			if ( in_array($val['line_id'],(array)$ignore) )
			{
				$lines[$key]['import_messages'][]=$this->translate('skipped (by request)');
				$lines[$key]['saved']=false;
				continue;
			}
			
			$name=$val[$this->importColumns['conceptName']];

			$d=$this->models->Taxa->save(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'is_empty' =>'0',
					'rank_id' => $val['rank_id'],
					'taxon' => $name,
				));	
				
			if ( $d )
			{
				$taxon_id=$this->models->Taxa->getNewId();
				$lines[$key]['taxon_id']=$taxon_id;
				$lines[$key]['import_messages'][]=$this->translate('created taxon concept');
				$lines[$key]['saved']=true;
				$this->createNsrIds(array('id'=>$taxon_id,'type'=>'taxon'));

				$d=$this->savename( [ 
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $taxon_id,
					'language_id' => LANGUAGE_ID_SCIENTIFIC,
					'type_id' => $this->_nameTypeIds[PREDICATE_VALID_NAME]['id'],
					'name' => $name,
				] );

				if ( $d )
				{
					$lines[$key]['valid_name_id']=$d;
					$lines[$key]['import_messages'][]=$this->translate('saved valid name');
				}
				else
				{
					$lines[$key]['import_messages'][]=$this->translate('creating valid name failed');
				}

			}
			else
			{
				$lines[$key]['import_messages'][]=$this->translate('creating taxon failed');
				$lines[$key]['saved']=false;
			}
		}	

		$this->setLines( $lines );

	}

    private function saveNames()
    {
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( $val['saved']!==true ) continue;
			if ( !isset($val['common_names']) ) continue;

			foreach((array)$val['common_names'] as $ckey=>$cval)
			{
				$d=$this->savename( [ 
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $val['taxon_id'],
					'language_id' => $cval['language_id'],
					'type_id' => $this->_nameTypeIds[PREDICATE_PREFERRED_NAME]['id'],
					'name' => $cval['name'],
				] );			

				if ( $d )
				{
					$lines[$key]['import_messages'][]=$this->translate('saved common name');
					$lines[$key]['common_names'][$ckey]['valid_name_id']=$d;
				}
				else
				{
					$lines[$key]['import_messages'][]=$this->translate('saving common name failed');
				}
			}
		}	

		$this->setLines( $lines );
	}

    private function saveParents()
    {
		$lines=$this->getLines();

		foreach((array)$lines as $key=>$val)
		{
			if ( $val['saved']!==true ) continue;

			if ( !isset($val['parent_id']) )
			{
				$lines[$key]['import_messages'][]=$this->translate('saving without parent');
				continue;
			}
			
			$parent_id=null;

			if ( $val['parent_id']['source']=='new' )
			{
				$key2=array_search( $val['parent_id']['id'], array_column($lines, 'line_id' ) );

				if ( $key2 && isset($lines[$key2]['taxon_id']))
				{
					$parent_id=$lines[$key2]['taxon_id'];
				}
			}
			else
			if ( $val['parent_id']['source']=='existing' && isset($val['parent_id']['id']))
			{
				$parent_id=$val['parent_id']['id'];
			}
			
			if ( is_null($parent_id) ) continue;

			$d=$this->models->Taxa->update(
				[ 'parent_id' => $parent_id ],
				[ 'id' => $val['taxon_id'], 'project_id' => $this->getCurrentProjectId() ]);	
				
			if ( $d )
			{
				$lines[$key]['import_messages'][]=$this->translate('saved parent');
			}
			else
			{
				$lines[$key]['import_messages'][]=$this->translate('saving parent failed');
			}
		}	

		$this->setLines( $lines );
	}


}