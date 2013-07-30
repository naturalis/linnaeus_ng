<?php


include_once ('Controller.php');

class ExportController extends Controller
{

    public $usedModels = array(
		'content_taxon',
		'page_taxon', 
		'page_taxon_title', 
		'commonname',
		'synonym',
		'media_taxon',
		'media_descriptions_taxon',
		'content',
		'literature',
		'literature_taxon',
		'keystep',
		'content_keystep',
		'choice_keystep',
		'choice_content_keystep',
        'module_project_user', 
		'matrix',
		'matrix_name',
		'matrix_taxon',
		'matrix_taxon_state',
		'characteristic',
		'characteristic_matrix',
		'characteristic_label',
		'characteristic_state',
		'characteristic_label_state',
		'glossary',
		'glossary_synonym',
		'glossary_media',
		'free_module_project',
		'free_module_project_user',
		'free_module_page',
		'free_module_media',
		'content_free_module',
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title',
		'content_introduction',
		'introduction_page',
		'introduction_media',
		'user_taxon',
		'nbc_extras',
		'taxa_relations',
		'matrix_variation',
		'variation_relations',
		'chargroup',
		'characteristic_chargroup',
		'chargroup_label',
		'gui_menu_order'
    );
   
    public $controllerPublicName = 'Export';

    public $usedHelpers = array('array_to_xml','mysql_2_sqlite');

	public $cssToLoad = array();
	public $jsToLoad = array();

	private $_appExpSkipCols = array('created','last_change');
	private $_exportDump=null;
	private $_sqliteQueries=null;
	private $_removePrefix=false;
	private $_includeCode=true;
	private $_dataSize=0;


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
     * Destroys
     *
     * @access     public
     */
    public function __destruct ()
    {
        
        parent::__destruct();

    }

    /**
     * Index
     *
     * @access    public
     */
    public function exportAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Select modules to export'));
		
		$pModules = $this->getProjectModules();

		if ($this->rHasVal('action','export')) {
		
			$data = array();
			
			$d = $this->newGetProjectRanks();
			
			$r = $this->models->Rank->_get(array('id' => '*','fieldAsIndex' => 'id'));

			foreach((array)$d as $key => $val) $e[$key] = array('name' => $r[$val['rank_id']]['rank'], 'lower_taxon' => $val['lower_taxon']);

			$_SESSION['admin']['export']['languages'] = $this->getAllLanguages();
			$_SESSION['admin']['export']['ranks'] = $e;
			$_SESSION['admin']['export']['taxa'] = $this->models->Taxon->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'fieldAsIndex' => 'id'
				)
			);

			$d = $this->exportProject();

			$data['file'] = array(
				'exportdate' => date('c'),
				'filename' => $this->makeFileName($d['system_name'])
			);

			$data['project'] = $d;

			if ($this->rHasVal('modules')) {
		
				foreach ((array)$this->requestData['modules'] as $val) {
	
					$d = 'export'.ucfirst($val);
					
					if (method_exists($this,$d)) {
					
						if ($d=='exportSpecies') $data['ranks'] = $this->exportRanks();

						$data[$val] = $this->$d();

					} else {

						$this->addError($this->translate('Missing function "'.get_class($this).'::'.$d.'"'));

					}
						
				}
				
			}
		
			if ($this->rHasVal('freeModules')) {
		
				foreach ((array)$this->requestData['freeModules'] as $val) {
	
					$fmp = $this->models->FreeModuleProject->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'id' => $val
							)
						)
					);
					
					$data[$fmp[0]['module']] = $this->exportFreemodule($val);

				}
				
			}		

			$this->exportData($data,$this->makeFileName($data['project']['system_name']));
			
			unset($_SESSION['admin']['export']);
			
		}

		$this->smarty->assign('modules',$pModules);

        $this->printPage();
    
    }
	
	public function actionMatrixAppExport()
	{

        $this->checkAuthorisation();
        
        $this->setPageName($this->translate('Export matrix key database for Linnaeus Mobile'));
		
		$pModules = $this->getProjectModules();
		
		$matrices = $this->getMatrices();

		$config = new configuration;
		$dbSettings = $config->getDatabaseSettings();

		if ($this->rHasVal('action','export')) {
			
			$this->_removePrefix = isset($this->requestData['removePrefix']) && $this->requestData['removePrefix']=='y' ? $dbSettings['tablePrefix'] : false;
			$this->_includeCode = isset($this->requestData['includeCode']) && $this->requestData['includeCode']=='y' ? true : false;

			
			$d = explode('-',$this->requestData['id']);
			$matrixId = $d[0];
			$languageId = $d[1];

			$this->_filename = $this->makeFileName($matrices[$matrixId]['names'][$languageId]['name'].' '.$matrices[$matrixId]['names'][$languageId]['language'],'sql');
			$this->_dbname = $this->makeDatabaseName($matrices[$matrixId]['names'][$languageId]['name'].' '.$matrices[$matrixId]['names'][$languageId]['language']);
			$this->_projectName = $matrices[$matrixId]['names'][$languageId]['name'];

			$this->makeMatrixDump($matrixId,$languageId);
			$this->convertDumpToSQLite();
			$this->downloadSQLite();
			
		}

        $d = $this->models->ModuleProject->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'active' => 'y',
					'module_id' => MODCODE_MATRIXKEY
				), 
				'columns' => 'id'
			));
			
		if ($d[0]['id']) {

			$this->smarty->assign('dbSettings',$dbSettings);
			$this->smarty->assign('matrices',$matrices);
			$this->smarty->assign('default_langauge',$this->getDefaultProjectLanguage());
			
		} else {

			$this->smarty->assign('matrices',false);
		}
		
        $this->printPage();
		
	}


	// GENERIC EXPORT
	private function makeFileName($projectName,$ext='xml')
	{

		return strtolower(preg_replace('/\W/','_',$projectName)).(is_null($ext) ? null : '.'.$ext);

	}

	private function exportProject()
	{

		$p = $this->models->Project->_get(
			array(
				'id' => $this->getCurrentProjectId(),
				'columns' => '
					title as project_name,
					sys_name as system_name,
					sys_description as system_desctription,
					includes_hybrids,
					keywords as html_meta_keywords,
					description as html_meta_description'
			)
		);	

		return $p;
	
	}

	private function exportGlossary()
	{

		$e = array();

		$g = $this->models->Glossary->_get(array('id'=>array('project_id' => $this->getCurrentProjectId())));

		foreach((array)$g as $key => $val) {

			$d = $this->models->GlossarySynonym->_get(array('id'=>array('glossary_id' => $val['id'])));
			
			foreach((array)$d as $sKey => $sVal) {
			
				$s['synonym'.$sKey] = array(
					'id' => $sVal['id'],
					'language' => $_SESSION['admin']['export']['languages'][$sVal['language_id']]['language'],
					'synonym' => $sVal['synonym']
				);
			
			}

			$d = $this->models->GlossaryMedia->_get(
				array(
					'id'=> array('glossary_id' => $val['id'])
					)
				);
				
			foreach((array)$d as $sKey => $sVal) {
			
				$m['file'.$key] = array(
					'file_name' => $sVal['file_name'],
					'mime_type' => $sVal['mime_type'],
					'file_size' => $sVal['file_size'],
				);
			
			}

			$e['item'.$key] = array(
				'id' => $val['id'],
				'language' => $_SESSION['admin']['export']['languages'][$val['language_id']]['language'],
				'term' => $val['term'],
				'definition' => $val['definition'],
				'synonyms' => isset($s) ? $s : null,
				'media' => isset($m) ? $m : null,
			);

		}

		return $e;
	
	}

	private function exportLiterature()
	{

		$e = array();

		$g = $this->models->Literature->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => '
					id,
					author_first,
					author_second,
					concat(
						author_first,
						(
							if(multiple_authors=1,
								\' et al.\',
								if(author_second!=\'\',concat(\' & \',author_second),\'\')
							)
						)
					) as author_full,
					year(`year`) as `year`,
					suffix,
					concat(year(`year`),if(suffix!=\'\',suffix,\'\')) as `year_full`,
					text'
			)
		);

		foreach((array)$g as $key => $val) {

			$e['reference'.$key] = array(
				'id' => $val['id'],
				'author_first' => $val['author_first'],
				'author_second' => $val['author_second'],
				'author_full' => $val['author_full'],
				'year' => $val['year'],
				'suffix' => $val['suffix'],
				'year_full' => $val['year_full'],
				'text' => $val['text']
			);

			$d = $this->models->LiteratureTaxon->_get(array('id'=>array('literature_id' => $val['id'])));
			
			foreach((array)$d as $sKey => $sVal) {
			
				$e['reference'.$key]['taxa']['taxon'.$sKey] = array(
					'id' => $sVal['taxon_id'],
					'taxon' => $_SESSION['admin']['export']['taxa'][$sVal['taxon_id']]['taxon']
				);
			
			}


		}

		return $e;
	
	}
	
	private function exportRanks()
	{
	
		foreach((array)$_SESSION['admin']['export']['ranks'] as $key => $val) {

			$e['rank'.$key] = array(
				'name' => $val['name'],
				'higher_lower' => ($val['lower_taxon']=='1' ? 'lower' : 'higher')
			);

		}
		
		return $e;

	}

	private function getSpeciesContent($id,$page)
	{

		$e = array();

		$d = $this->models->ContentTaxon->_get(
			array('id' => 
				array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id,
					'page_id' => $page
				)
			)
		);
		
		return $d;

	}

	private function getSpeciesPageCategories()
	{
	
		$e = array();

        $pages = $this->models->PageTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'order' => 'show_order'
			)
        );
        
        foreach ((array) $pages as $key => $page) {
            
			$tpt = $this->models->PageTaxonTitle->_get(
				array('id' =>
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'page_id' => $page['id'],
					),
					'columns' => 'language_id,title',
					'fieldAsIndex' => 'language_id'
				)
			);

			$e[] = array(
				'id' => $page['id'],
				'label' => $tpt[$_SESSION['admin']['project']['default_language_id']]['title'],
				'labels' => $tpt
			);
	
        }
		
		return $e;

	}

	private function exportSpecies()
	{

		$e = array();

		$t = $this->models->Taxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'id,taxon,parent_id,rank_id,is_hybrid',
			)
		);
		
		$c = $this->getSpeciesPageCategories();

		// taxa
		foreach((array)$t as $key => $val) {
		
			unset($content);
			unset($common);
			unset($synonym);
			unset($media);
		
			// pages
			foreach((array)$c as $sKey => $sVal) {
			
				$d = $this->getSpeciesContent($val['id'],$sVal['id']);

				foreach((array)$d as $dKey => $dVal) {
				
					if ($dVal['content']!='') {

						$dummy['translation'.$dKey] = array(
							'title' => $sVal['labels'][$dVal['language_id']]['title'],
							'text' => $dVal['content'],
							'language' => $_SESSION['admin']['export']['languages'][$dVal['language_id']]['language'],
						);
						
					}
				
				}
				
				if (isset($dummy)) $content['page'.$sKey] = $dummy;
				
				unset($dummy);

			}
			
			// common names
			$c = $this->models->Commonname->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['id']
					)
				)
			);
			
			foreach((array)$c as $cKey => $cVal) {
			
				$common['commonname'.$cKey] = array(
					'commonname' => $cVal['commonname'],
					'transliteration' => $cVal['transliteration'],
					'language' => $_SESSION['admin']['export']['languages'][$cVal['language_id']]['language'],
				);
			
			}

			// synonyms
			$c = $this->models->Synonym->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['id']
					),
					'order' => 'show_order'
				)
			);
			
			foreach((array)$c as $cKey => $cVal) {
			
				$synonym['synonym'.$cKey] = array(
					'synonym' => isset($cVal['synonym']) ? $cVal['synonym'] : null,
					'remark' => isset($cVal['remark']) ? $cVal['remark'] : null,
					'literature_id' => isset($cVal['lit_ref_id']) ? $cVal['lit_ref_id'] : null,
				);
			
			}

			// media
			$m = $this->models->MediaTaxon->_get(
				array('id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'taxon_id' => $val['id']
					)
				)
			);
				
			foreach((array)$m as $mKey => $mVal) {
			
                $d = $this->models->MediaDescriptionsTaxon->_get(
					array('id' =>
						array(
							'project_id' => $this->getCurrentProjectId(),
							'media_id' => $mVal['id'],
						)
					)
                );
				
				foreach((array)$d as $dKey => $dVal) {
				
					$trans['translation'.$dKey] = array(
						'description' => $dVal['description'],
						'language' => $_SESSION['admin']['export']['languages'][$dVal['language_id']]['language'],
					);

				}

				$media['file'.$mKey] = array(
					'id' => $mVal['id'],
					'file_name' => $mVal['file_name'],
					'thumb_name' => $mVal['thumb_name'],
					'original_name' => $mVal['original_name'],
					'mime_type' => $mVal['mime_type'],
					'file_size' => $mVal['file_size'],
					'overview_image' => $mVal['overview_image'],
					'descriptions' => isset($trans) ? $trans : null
				);
				
				unset($trans);
			
			}	


			$e['taxon'.$key] = array(
				'id' => $val['id'],
				'taxon' => $val['taxon'],
				'is_hybrid' => $val['is_hybrid'],
				'rank' => $_SESSION['admin']['export']['ranks'][$val['rank_id']]['name'],
				'parent_id' => $val['parent_id'],
				'parent' => @$_SESSION['admin']['export']['taxa'][$val['parent_id']]['taxon'],
				'pages' => isset($content) ? $content : null,
				'common_names' => isset($common) ? $common : null,
				'synonyms' => isset($synonym) ? $synonym : null,
				'media' => isset($media) ? $media : null,
			);

			unset($content);
			unset($common);
			unset($synonym);
			unset($media);

		}

		return $e;
	
	}

	private function exportIntroduction()
	{

		$ip = $this->models->IntroductionPage->_get(
			array('id' =>
				array(
					'project_id' => $this->getCurrentProjectId(),
					'got_content' => '1'
				),
				'order' => 'show_order'
			)
		);
		
		foreach((array)$ip as $key => $val) {

			$ci = $this->models->ContentIntroduction->_get(
				array('id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $val['id'],
					),
					'fieldAsIndex' => 'language_id'
				)
			);

			foreach((array)$ci as $sKey => $sVal) {
			
				$dummy['translation'.$sKey] = array(
					'topic' => $sVal['topic'],
					'content' => $sVal['content'],
					'language' => $_SESSION['admin']['export']['languages'][$sVal['language_id']]['language'],
				);
			
			}
			
			$e['pages'.$key] = array(
				//'topic' => $ci[$_SESSION['admin']['project']['default_language_id']]['topic'],
				'translations' => $dummy
			);
			
			unset($dummy);
			
		}
		
		return isset($e) ? $e : null;
	
	}

	private function exportContent()
	{

		$e = array();

		$c = $this->models->Content->_get(
			array('id' =>
				array(
					'project_id' => $this->getCurrentProjectId(),	
				),
				'order' => 'subject'
			)
		);

		foreach((array)$c as $key => $val) {

			$d[$val['subject']]['translation'.$key] = array(
				'content' => $val['content'],
				'language' => $_SESSION['admin']['export']['languages'][$val['language_id']]['language'],
			);
			
		}
	
		$i=0;
		// whatever
		foreach((array)$d as $key => $val) {

			$e['subject'.$i] = $val;
			$e['subject'.$i]['label'] = $key;
			$i++;
			
		}

		return $e;
	
	}

	private function getMapKeyLegend()
	{

		$e = array();

		$t = $this->models->GeodataType->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'fieldAsIndex'=>'id',
				'columns' => 'id'
			)
		);

		foreach((array)$t as $key => $val) {
		
			$e['type'.$key] = $val;

			$d = $this->models->GeodataTypeTitle->_get(
				array(
					'id' => array('type_id' => $val['id']),
					'fieldAsIndex'=>'language_id'
				)
			);

			foreach((array)$d as $sKey => $sVal) {
			
				$e['type'.$key]['label']['translation'.$sKey] = array(
					'language' => $_SESSION['admin']['export']['languages'][$sKey]['language'],
					'label' => $sVal['title']
				);
				
				if ($sKey == $this->getDefaultProjectLanguage())
					$_SESSION['admin']['export']['mapLegend'][$key] = $sVal['title'];

			}
			
		}

		return $e;

	}

	private function exportMapkey()
	{

		$l = $this->getMapKeyLegend();

		$e = array();

		$o = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'taxon_id,type_id,type,latitude,longitude,boundary_nodes'
			)
		);
		
		foreach((array)$o as $key => $val) {
		
			$e['occurrence'.$key] = array(
				'taxon' => $_SESSION['admin']['export']['taxa'][$val['taxon_id']]['taxon'],
				'taxon_id' => $val['taxon_id'],
				'type' => @$_SESSION['admin']['export']['mapLegend'][$val['type_id']],
				'type_id' => $val['type_id'],
				'shape' => $val['type']=='marker' ? 'point' : $val['type'],
				'point_coordinates' => array(
					'latitude' => $val['latitude'],
					'longitude' => $val['longitude'],
				),
				'polygon_boundary_nodes' => $val['boundary_nodes']
			);
		
		}

		$this->loadControllerConfig('MapKey');

		$srid = $this->controllerSettings['SRID'];
			
		$this->loadControllerConfig();

		return array(
			'srid' => $srid,
			'legend' => $l,
			'occurrences' => $e,
		);
	
	}

	private function exportKey()
	{

		$e = array();

		$k = $this->models->Keystep->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

		foreach ((array)$k as $key => $val) {

			// step content
			$c = $this->models->ContentKeystep->_get(
				array('id' => 
					array(
						'project_id' => $this->getCurrentProjectId(),
						'keystep_id' => $val['id'],
					)
				)
			);

			foreach ((array)$c as $sKey => $sVal) {
			
				$trans['translation'.$sKey] = array(
					'title' => $sVal['title'],
					'text' => $sVal['content'],
					'language' => $_SESSION['admin']['export']['languages'][$sVal['language_id']]['language']
				);
			
			}

			$c = $this->models->ChoiceKeystep->_get(
				array('id' => 
					array(
						'project_id' => $this->getCurrentProjectId(),
						'keystep_id' => $val['id']
					)
				)
			);			

			// step choices
			foreach ((array)$c as $cKey => $cVal) {

				$d  = $this->models->ChoiceContentKeystep->_get(
					array('id' =>
						array(
							'project_id' => $this->getCurrentProjectId(),
							'choice_id' => $cVal['id'],
						)
					)
				);

				foreach ((array)$d as $dKey => $dVal) {
				
					$trans2['translation'.$sKey] = array(
						'text' => $dVal['choice_txt'],
						'language' => $_SESSION['admin']['export']['languages'][$dVal['language_id']]['language']
					);
				
				}
							
				$choices['choice'.$cKey] = array(
					'show_order' => $cVal['show_order'],
					'choice_img' => $cVal['choice_img'],
					'choice_image_params' => $cVal['choice_image_params'],
					'target_step_id' => $cVal['res_keystep_id'],
					'target_taxon' => isset($cVal['res_taxon_id']) ? $_SESSION['admin']['export']['taxa'][$cVal['res_taxon_id']]['taxon'] : null,
					'target_taxon_id' => isset($cVal['res_taxon_id']) ? $cVal['res_taxon_id'] : null,
					'text' => isset($trans2) ? $trans2 : null
				);

				unset($trans2);
			
			}

			$e['step'.$key] = array(
				'id' => $val['id'],
				'display_number' => $val['number'],
				'is_start' => $val['is_start'],
				'image' => $val['image'],
				'text' => isset($trans) ? $trans : null,
				'choices' => isset($choices) ? $choices : null
			);
			
			unset($trans);
		
		}
		
		
		return $e;

	}
	
	private function exportMatrixkey()
	{

		$e = array();

		// matrices
		$m = $this->models->Matrix->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'got_names' => 1
				)
			)
		);
		
		// matrix names
		foreach((array)$m as $mKey => $mVal) {

			$mn = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mVal['id'],
						'language_id' => $this->getDefaultProjectLanguage()
					)
				)
			);
			
			$_SESSION['admin']['export']['matrices'][$mVal['id']] = $mn[0]['name'];
		
		}

		foreach((array)$m as $mKey => $mVal) {

			// available taxa
			$mt = $this->models->MatrixTaxon->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mVal['id'],
					),
					'columns' => 'taxon_id'
				)
			);
			
			foreach((array)$mt as $mtKey => $mtVal) {
			
				$mTaxa['taxon'.$mtKey] = array(
					'taxon' => $_SESSION['admin']['export']['taxa'][$mtVal['taxon_id']]['taxon'],
					'id' => $mtVal['taxon_id']
				);
			
			}
					
			// matrix names
			$n = $this->models->MatrixName->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mVal['id']
					)
				)
			);

			foreach((array)$n as $nKey => $nVal) {

				$mNames['translation'.$nKey] = array(
					'name' => $nVal['name'],
					'language' => $_SESSION['admin']['export']['languages'][$nVal['language_id']]['language'],
				);
				
				//	if ($nVal['language_id']==$this->getDefaultProjectLanguage()) 
				//		$_SESSION['admin']['export']['matrices'][$mVal['id']] = $nVal['name'];

			}

			// matrix characters
			$cm = $this->models->CharacteristicMatrix->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mVal['id']
					),
					'columns' => 'characteristic_id'
				)
			);

			foreach((array)$cm as $cmKey => $cmVal) {

				// matrix characters' names
				$c = $this->models->Characteristic->_get(
					array(
						'id' => array(
							'id' => $cmVal['characteristic_id'],
							'project_id' => $this->getCurrentProjectId(),
						),
						'columns' => 'id,type'
					)
				);

				$cl = $this->models->CharacteristicLabel->_get(
					array(
						'id' => array(
							'characteristic_id' => $cmVal['characteristic_id'],
							'project_id' => $this->getCurrentProjectId(),
						)
					)
				);
				
				foreach((array)$cl as $clKey => $clVal) {

					$cNames['translation'.$clKey] = array(
						'name' => $clVal['label'],
						'language' => $_SESSION['admin']['export']['languages'][$clVal['language_id']]['language'],
					);				

				}

				// matrix characters states'
				$cs = $this->models->CharacteristicState->_get(
					array(
						'id' => array(
							'project_id' => $this->getCurrentProjectId(),
							'characteristic_id' => $cmVal['characteristic_id'],
							'got_labels' => 1
						)
					)
				);
				
				foreach((array)$cs as $csKey => $csVal) {

					// matrix characters states' names
					$cls = $this->models->CharacteristicLabelState->_get(
						array(
							'id' => array(
								'state_id' => $csVal['id'],
								'project_id' => $this->getCurrentProjectId(),
							)
						)
					);
					
					foreach((array)$cls as $clsKey => $clsVal) {
	
						$csNames['translation'.$clsKey] = array(
							'name' => $clsVal['label'],
							'language' => $_SESSION['admin']['export']['languages'][$clsVal['language_id']]['language'],
						);				
	
					}

					// matrix characters states' taxa
					$mts = $this->models->MatrixTaxonState->_get(
						array(
							'id' => array(
								'project_id' => $this->getCurrentProjectId(),
								'matrix_id' => $mVal['id'],
								'characteristic_id' => $cmVal['characteristic_id'],
								'state_id' => $csVal['id'],
							)
						)
					);

					foreach((array)$mts as $mtsKey => $mtsVal) {

						if (isset($mtsVal['taxon_id'])) {

							$sTaxa['taxon'.$mtsKey] = array(
								'taxon' =>  $_SESSION['admin']['export']['taxa'][$mtsVal['taxon_id']]['taxon'],
								'taxon_id' => $mtsVal['taxon_id']
							);
							
						} else
						if (isset($mtsVal['ref_matrix_id']) && isset($_SESSION['admin']['export']['matrices'][$mtsVal['ref_matrix_id']])) {

							$sMatrices['matrix'.$mtsKey] = array(
								'matrix' => $_SESSION['admin']['export']['matrices'][$mtsVal['ref_matrix_id']],
								'matrix_id' => $mtsVal['ref_matrix_id']
							);
							
						}
													
					}
					
					$cStates['state'.$csKey] = array(
						'file_name' => $csVal['file_name'],
						'lower' => $csVal['lower'],
						'upper' => $csVal['upper'],
						'mean' => $csVal['mean'],
						'sd' => $csVal['sd'],
						'name' => isset($csNames) ? $csNames : null,
						'taxa' => isset($sTaxa) ? $sTaxa : null,
						'matrices' => isset($sMatrices) ? $sMatrices : null,
					);

					unset($csNames);
					unset($sTaxa);
					unset($sMatrices);
					
				}

				if (!empty($c[0]['type']) || isset($cNames) || isset($cStates)) {
							
					$chars['character'.$cmKey] = array(
						'type' => $c[0]['type'],
						'name' => isset($cNames) ? $cNames : null,
						'states' => isset($cStates) ? $cStates : null
					);
	
					unset($cNames);
					unset($cStates);
					
				}

			}

			$e['matrix'.$mKey] = array(
				'id' => $mVal['id'],
				'name' => isset($mNames) ? $mNames : null,
				'characters' => isset($chars) ? $chars : null,
				'possible_taxa' => isset($mTaxa) ? $mTaxa : null
			);
			
			unset($mTaxa);
			unset($mNames);
			unset($chars);
			
		}

		return $e;	

	}

	private function exportFreemodule($mId)
	{

		$m = $this->models->FreeModuleProject->_get(
			array(
				'id' => array(
					'id' => $mId,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$fmp = $this->models->FreeModulePage->_get(
			array(
				'id' => array(
					'module_id' => $mId,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);
		
		// pages		
		foreach((array)$fmp as $pKey => $pVal) {

			$cfm = $this->models->ContentFreeModule->_get(
				array(
					'id' => array(
						'module_id' => $mId,
						'project_id' => $this->getCurrentProjectId(),
						'page_id' => $pVal['id'],
					)
				)
			);
			
			foreach((array)$cfm as $cKey => $cVal) {

				$dummy['translation'.$cKey] = array(
					'language' => $_SESSION['admin']['export']['languages'][$cVal['language_id']]['language'],
					'topic' => $cVal['topic'],
					'text' => $cVal['content'],
				);

			}
			
			if (isset($dummy)) $content['page'.$pKey]['translations'] = $dummy;
			
			unset($dummy);

			$fmm = $this->models->FreeModuleMedia->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'page_id' =>$pVal['id'],
					),
					'columns' => 'file_name,thumb_name,original_name,mime_type,file_size'
				)
			);

			foreach((array)$fmm as $cKey => $cVal) {

				$dummy['mediafile'.$cKey] = array(
					'label' => $cVal['original_name'],
					'filename' => $cVal['file_name'],
					'thumb_filename' => $cVal['thumb_name'],
					'mime_type' => $cVal['mime_type'],
					'file_size' => $cVal['file_size'],
				);

			}
			
			if (isset($dummy)) $content['page'.$pKey]['mediafiles'] = $dummy;
			
			unset($dummy);

		}
		
		return $content;
	
	}

	private function exportData($data,$filename)
	{

		//return;
		//q($data,1);

		header('Content-disposition:attachment;filename='.$filename);
		header('Content-type:text/xml');

		echo $this->helpers->ArrayToXml->toXml($data);
		
		die();
	
	}


	// MATRIX EXPORT
    private function getMatrices ()
    {
		$m = $this->models->Matrix->_get(
		array(
			'id' => array(
				'project_id' => $this->getCurrentProjectId(), 
				'got_names' => 1
			), 
			'fieldAsIndex' => 'id', 
			'columns' => 'id,got_names,\'matrix\' as type, `default`'
		));
		
		foreach ((array) $m as $key => $val) {
			
			$mn = $this->models->MatrixName->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'matrix_id' => $val['id']
				), 
				'columns' => 'name,language_id',
				'fieldAsIndex' => 'language_id'
			));

			foreach((array)$mn as $mKey =>$mVal)
				$mn[$mKey]['language'] = $_SESSION['admin']['project']['languageList'][$mVal['language_id']]['language'];
		

			$m[$key]['names']= $mn;

		}

        return $m;
    }

	private function removeUnwantedColumns($s)
	{
		$d = explode(chr(10),$s);
		foreach((array)$d as $key => $val) {
			if (preg_match('/^(\s*)(`?)('.implode('|',$this->_appExpSkipCols).')(`?)/',$val)==1)
				unset($d[$key]);
		}
		return implode(chr(10),$d);
	}
	
	private function fixTablePrefix($s,$table=null)
	{

		if ($this->_removePrefix===false) return $s;
		
		if (is_null($table))
			return str_ireplace($this->_removePrefix,'',$s);
		else
			return preg_replace('/(`'.$table.'`)/','`'.str_ireplace($this->_removePrefix,'',$table).'`',$s);
		
	}

    private function makeMatrixDump ($matrixId,$languageId)
	{
		
		$d = 
			array(
				'project_id' => $this->getCurrentProjectId(),
				'matrix_id' => $matrixId,
				'language_id' => $languageId
			);

		$NbcExtrasT = $this->models->NbcExtras->_get(array('id' => array_merge($d,array('ref_type'=>'taxon')),'fieldAsIndex' => 'ref_id'));
		$this->_exportDump->MatrixTaxon = $this->models->MatrixTaxon->_get(array('id' => $d,'fieldAsIndex' => 'taxon_id'));
		$this->_exportDump->Taxon = $this->models->Taxon->_get(array('id' => $d));
		$this->_exportDump->Commonname = $this->models->Commonname->_get(array('id' => $d,'fieldAsIndex' => 'taxon_id'));
		$this->_exportDump->TaxaRelations = $this->models->TaxaRelations->_get(array('id' => $d,'fieldAsIndex' => 'taxon_id'));

		foreach((array)$this->_exportDump->Taxon as $key => $val) {
			if (!isset($this->_exportDump->MatrixTaxon[$val['id']])) {
				unset($this->_exportDump->Taxon[$key]);			
				unset($this->_exportDump->Commonname[$key]);			
				unset($this->_exportDump->TaxaRelations[$key]);
				unset($NbcExtrasT[$key]);
			}
		}

		$NbcExtrasV = $this->models->NbcExtras->_get(array('id' => array_merge($d,array('ref_type'=>'variation')),'fieldAsIndex' => 'ref_id'));
		$this->_exportDump->MatrixVariation = $this->models->MatrixVariation->_get(array('id' => $d,'fieldAsIndex' => 'variation_id'));
		$this->_exportDump->TaxonVariation = $this->models->TaxonVariation->_get(array('id' => $d));
		$this->_exportDump->VariationRelations = $this->models->VariationRelations->_get(array('id' => $d,'fieldAsIndex' => 'variation_id'));
		$this->_exportDump->VariationLabel = $this->models->VariationLabel->_get(array('id' => $d,'fieldAsIndex' => 'variation_id'));

		foreach((array)$this->_exportDump->TaxonVariation as $key => $val) {
			if (!isset($this->_exportDump->MatrixVariation[$val['id']])) {
				unset($this->_exportDump->TaxonVariation[$key]);			
				unset($this->_exportDump->VariationRelations[$key]);			
				unset($this->_exportDump->VariationLabel[$key]);			
				unset($NbcExtrasV[$key]);
			}
		}
		
		$this->_exportDump->MatrixTaxonState = $this->models->MatrixTaxonState->_get(array('id' => $d));
		$this->_exportDump->NbcExtras = array_merge($NbcExtrasT,$NbcExtrasV);

		$this->_exportDump->Characteristic = $this->models->Characteristic->_get(array('id' => $d));
		$this->_exportDump->CharacteristicLabel = $this->models->CharacteristicLabel->_get(array('id' => $d));
		$this->_exportDump->CharacteristicState = $this->models->CharacteristicState->_get(array('id' => $d));
		$this->_exportDump->CharacteristicLabelState = $this->models->CharacteristicLabelState->_get(array('id' => $d));
		$this->_exportDump->CharacteristicMatrix = $this->models->CharacteristicMatrix->_get(array('id' => $d));
		$this->_exportDump->Chargroup = $this->models->Chargroup->_get(array('id' => $d));
		$this->_exportDump->ChargroupLabel = $this->models->ChargroupLabel->_get(array('id' => $d));
		$this->_exportDump->CharacteristicChargroup = $this->models->CharacteristicChargroup->_get(array('id' => $d));

		$this->_exportDump->GuiMenuOrder = $this->models->GuiMenuOrder->_get(array('id' => $d));
		
	}
	
	private function convertDumpToSQLite()
	{

		$setsPerInsert = 1; // phonegap webdb doesn't seem to support inserting multiple sets at once
		
		$this->helpers->Mysql2Sqlite->setRemoveUniqueConstraints(true);
		
		foreach((array)$this->_exportDump as $class => $data) {

			$table = $this->models->$class->getTableName();
			$inserts = array();
			
			$c = $this->models->Taxon->freeQuery('show create table '.$table);
			$this->helpers->Mysql2Sqlite->convert($this->fixTablePrefix($this->removeUnwantedColumns($c[0]['Create Table'].';'),$table));

			$this->_sqliteQueries[] = $this->helpers->Mysql2Sqlite->getSqlDropTable();
			$this->_sqliteQueries = array_merge($this->_sqliteQueries,$this->helpers->Mysql2Sqlite->getSqlDropKeys());

			$this->_sqliteQueries[] = $this->helpers->Mysql2Sqlite->getSqlTable();
			$this->_sqliteQueries = array_merge($this->_sqliteQueries,$this->helpers->Mysql2Sqlite->getSqlKeys());
			
			$this->dataCount[$this->fixTablePrefix($table)] = count((array)$data);

			foreach((array)$data as $row) {

				foreach((array)$row as $column => $value) {
					if (in_array($column,$this->_appExpSkipCols))
						unset($row[$column]);
				}
				
				$inserts[] = "('".implode("','",array_map(function($str){return trim(preg_replace(array('/\\\'/','/\\\"/'),array("''",'"'), $str));},$row))."')";
				
				if (count((array)$inserts)>=$setsPerInsert) {
					$d = implode(',',$inserts);
					$this->_sqliteQueries[] = $this->fixTablePrefix('insert into `'.$table.'` values '.$d.';',$table);
					$this->_dataSize += strlen($d);
					$inserts = array();
				}

			}

			if (count((array)$inserts)!=0) {
				$d = implode(',',$inserts);
				$this->_sqliteQueries[] = $this->fixTablePrefix('insert into `'.$table.'` values '.$d.';',$table);
				$this->_dataSize += strlen($d);
			}
			
			$this->_sqliteQueries = array_merge($this->_sqliteQueries,$this->helpers->Mysql2Sqlite->getSqlReindexKeys());

		}
		
		unset($this->_exportDump);
		
	}

	private function makeDatabaseName($projectName,$ext='xml')
	{

		return strtolower(preg_replace(array('/\W/','/[aeiouy]/i'),array('_',''),$projectName));

	}
	
	private function downloadSQLite()
	{

		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='.$this->_dbname.'-app-installer.js');
		header('Content-Type: text/javascript ');

		if ($this->_includeCode) {
			$mostRecords = array(null,0);
			foreach((array)$this->dataCount as $table => $rowCount) {
				if ($rowCount>$mostRecords[1])
					$mostRecords = array($table,$rowCount);
			}
			
			//echo '<pre>';
			echo '/*

    // to be included in the appController-class in app-controller.js:

	var credentials = {
		dbName:\''.$this->_dbname.'\',
		dbVersion: \'1.0\', 
		dbDisplayName: \''.$this->_projectName.'\', 
		dbEstimatedSize: '.floor($this->_dataSize * 1.2).'
	};
	var pId = '.$this->getCurrentProjectId().';


    // installer is called from main program file:

	var db = appController.connect();
	if (db) {
		installDb(db);
		if (debugging_in_an_actual_browser) {
			console.log(installMsg);
			console.dir(installErrors);
		}
	}
	
    // rename this file to app-installer.js, remove these lines and the lines above,
    // and place it in the app\'s javascript-directory.
    // alternatively, copy all the lines below this comment block and paste them over
    // the contents of an existing app-installer.js that is already in place.

*/

var installMsg;
var installErrors=Array();
			
function installDb(db,callback)
{

  var forceInstall=false;
  var queryCount='.count((array)$this->_sqliteQueries).';
  var queriesExecuted=0;
  var installResult=true;

  if (forceInstall) {

    installMsg="installing (forced)";
    loadRecords();

  } else {

    db.transaction(function (tx) {
      tx.executeSql(
        "select count(*) as total from '.$mostRecords[0].'",[],
        function(tx,r) {
          if(r.rows.item(0).total!='.$mostRecords[1].') {
            installMsg="installing (`'.$mostRecords[0].'`recordcount mismatch)";
            loadRecords();
          } else {
            installMsg="skipped install (`'.$mostRecords[0].'` recordcount match)";
            callback(null);
          }
        },
        function(tx,e){
          installMsg="installing (recordcount failure; assuming tables don\'t exist)";
          loadRecords();
        }
      )
    });
  }

  function s()
  {
      queriesExecuted++;
      finished();
  }

  function e(e)
  {
    installResult=false;
    installErrors.push(e.message);
    queriesExecuted++;
    finished();
  }

  function finished()
  {
    if (queriesExecuted>=queryCount)
      callback(installResult);
  }


  function loadRecords()
  {

    db.transaction(function (tx) {
';
	foreach ((array)$this->_sqliteQueries as $key => $val) {
		echo '      tx.executeSql("'.$val.'",[],function(tx,r){s(r);},function(tx,e){e(e);});'.chr(10);
	}
echo '
    });
  }

}
';

		} else {

			echo nl2br(implode(chr(10),$this->_sqliteQueries));

		}
		
		die();
	
	}

}
