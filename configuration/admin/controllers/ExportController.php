<?php


include_once ('Controller.php');

class ExportController extends Controller
{

    public $usedModels = array(
		'content_taxa',
        'pages_taxa',
		'pages_taxa_titles',
		'commonnames',
		'synonyms',
		'media_taxon',
		'media_descriptions_taxon',
		'content',
		'literature',
		'literature_taxa',
		'keysteps',
		'content_keysteps',
		'choices_keysteps',
		'choices_content_keysteps',
		'matrices',
		'matrices_names',
		'matrices_taxa',
		'matrices_taxa_states',
		'characteristics',
		'characteristics_matrices',
		'characteristics_labels',
		'characteristics_states',
		'characteristics_labels_states',
		'glossary',
		'glossary_synonyms',
		'glossary_media',
		'free_modules_projects',
		'free_modules_pages',
		'free_module_media',
		'content_free_modules',
		'occurrences_taxa',
		'geodata_types',
		'geodata_types_titles',
		'content_introduction',
		'introduction_pages',
		'introduction_media',
		'nbc_extras',
		'taxa_relations',
    /*
		'matrix_variation',
		'variation_relations',
		'chargroup',
		'characteristic_chargroup',
		'chargroup_label',
		'gui_menu_order',
		'names'
	*/
    );

    public $controllerPublicName = 'Export';

    public $usedHelpers = array('array_to_xml','mysql_2_sqlite');

	public $cssToLoad = array();
	public $jsToLoad = array();

	private $_exportDump=null;


    /**
     * Constructor, calls parent's constructor
     *
     * @access     public
     */
    public function __construct ()
    {
        parent::__construct();
		$this->UserRights->setRequiredLevel( ID_ROLE_LEAD_EXPERT );
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

			$r = $this->models->Ranks->_get(array('id' => '*','fieldAsIndex' => 'id'));

			$taxa = $this->models->Taxa->_get(
				array(
					'id' => array('project_id' => $this->getCurrentProjectId()),
					'fieldAsIndex' => 'id'
				)
			);

			foreach((array)$d as $key => $val) $e[$key] = array('name' => $r[$val['rank_id']]['rank'], 'lower_taxon' => $val['lower_taxon']);

			$this->moduleSession->setModuleSetting(array(
                'setting' => 'languages',
                'value' => $this->getAllLanguages()
            ));
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'ranks',
                'value' => $e
            ));
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'languages',
                'value' => $this->getAllLanguages()
            ));
			$this->moduleSession->setModuleSetting(array(
                'setting' => 'taxa',
                'value' => $taxa
            ));

			$d = $this->exportProject();

			$data['file'] = array(
				'exportdate' => date('c'),
				'filename' => $this->makeFileName($d['system_name'])
			);

			$data['project'] = $d;

			if ($this->rHasVal('modules')) {

				foreach ((array)$this->rGetVal('modules') as $val) {

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

				foreach ((array)$this->rGetVal('freeModules') as $val) {

					$fmp = $this->models->FreeModulesProjects->_get(
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

			$this->exportDataDownload($data,$this->makeFileName($data['project']['system_name']));

			unset($_SESSION['admin']['export']);

		}

		$this->smarty->assign('modules',$pModules);

        $this->printPage();

    }

	private function makeFileName($projectName,$ext='xml')
	{
		return strtolower(preg_replace('/\W/','_',$projectName)).(is_null($ext) ? null : '.'.$ext);
	}

	private function exportProject()
	{

		$p = $this->models->Projects->_get(
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

		$languages = $this->moduleSession->getModuleSetting('languages');

		foreach((array)$g as $key => $val) {

			$d = $this->models->GlossarySynonyms->_get(array('id'=>array('glossary_id' => $val['id'])));

			foreach((array)$d as $sKey => $sVal) {

				$s['synonym'.$sKey] = array(
					'id' => $sVal['id'],
					'language' => $languages[$sVal['language_id']]['language'],
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
				'language' => $languages[$val['language_id']]['language'],
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

		$taxa = $this->moduleSession->getModuleSetting('taxa');

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

			$d = $this->models->LiteratureTaxa->_get(array('id'=>array('literature_id' => $val['id'])));

			foreach((array)$d as $sKey => $sVal) {

				$e['reference'.$key]['taxa']['taxon'.$sKey] = array(
					'id' => $sVal['taxon_id'],
					'taxon' => $taxa[$sVal['taxon_id']]['taxon']
				);

			}


		}

		return $e;

	}

	private function exportRanks()
	{

	    foreach((array)$this->moduleSession->getModuleSetting('ranks') as $key => $val) {

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

		$d = $this->models->ContentTaxa->_get(
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

        $pages = $this->models->PagesTaxa->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'order' => 'show_order'
			)
        );

        foreach ((array) $pages as $key => $page) {

			$tpt = $this->models->PagesTaxaTitles->_get(
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

		$t = $this->models->Taxa->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				),
				'columns' => 'id,taxon,parent_id,rank_id,is_hybrid',
			)
		);

		$c = $this->getSpeciesPageCategories();

		$languages = $this->moduleSession->getModuleSetting('languages');
		$ranks = $this->moduleSession->getModuleSetting('ranks');
		$taxa = $this->moduleSession->getModuleSetting('taxa');

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
							'language' => $languages[$dVal['language_id']]['language'],
						);

					}

				}

				if (isset($dummy)) $content['page'.$sKey] = $dummy;

				unset($dummy);

			}

			// common names
			$c = $this->models->Commonnames->_get(
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
					'language' => $languages[$cVal['language_id']]['language'],
				);

			}

			// synonyms
			$c = $this->models->Synonyms->_get(
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
						'language' => $languages[$dVal['language_id']]['language'],
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
				'rank' => $ranks[$val['rank_id']]['name'],
				'parent_id' => $val['parent_id'],
				'parent' => isset($taxa[$val['parent_id']]['taxon']) ? $taxa[$val['parent_id']]['taxon'] : null,
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

		$languages = $this->moduleSession->getModuleSetting('languages');

		$ip = $this->models->IntroductionPages->_get(
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
					'language' => $languages[$sVal['language_id']]['language'],
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

		$languages = $this->moduleSession->getModuleSetting('languages');

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
				'language' => $languages[$val['language_id']]['language'],
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

		$languages = $this->moduleSession->getModuleSetting('languages');
		$mapLegend = array();

		$t = $this->models->GeodataTypes->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'fieldAsIndex'=>'id',
				'columns' => 'id'
			)
		);

		foreach((array)$t as $key => $val) {

			$e['type'.$key] = $val;

			$d = $this->models->GeodataTypesTitles->_get(
				array(
					'id' => array('type_id' => $val['id']),
					'fieldAsIndex'=>'language_id'
				)
			);

			foreach((array)$d as $sKey => $sVal) {

				$e['type'.$key]['label']['translation'.$sKey] = array(
					'language' => $languages[$sKey]['language'],
					'label' => $sVal['title']
				);

				if ($sKey == $this->getDefaultProjectLanguage()) {
				    $mapLegend[$key] = $sVal['title'];

				}

			}

		}

		$this->moduleSession->setModuleSetting(array(
            'setting' => 'mapLegend',
            'value' => $mapLegend
        ));

		return $e;

	}

	private function exportMapkey()
	{

		$l = $this->getMapKeyLegend();

		$e = array();

		$taxa = $this->moduleSession->getModuleSetting('taxa');
		$mapLegend = $this->moduleSession->getModuleSetting('mapLegend');

		$o = $this->models->OccurrencesTaxa->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
				'columns' => 'taxon_id,type_id,type,latitude,longitude,boundary_nodes'
			)
		);

		foreach((array)$o as $key => $val) {

			$e['occurrence'.$key] = array(
				'taxon' => $taxa[$val['taxon_id']]['taxon'],
				'taxon_id' => $val['taxon_id'],
				'type' => isset($mapLegend[$val['type_id']]) ? $mapLegend[$val['type_id']] : null,
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

		$languages = $this->moduleSession->getModuleSetting('languages');
		$taxa = $this->moduleSession->getModuleSetting('taxa');

		$k = $this->models->Keysteps->_get(array('id' => array('project_id' => $this->getCurrentProjectId())));

		foreach ((array)$k as $key => $val) {

			// step content
			$c = $this->models->ContentKeysteps->_get(
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
					'language' => $languages[$sVal['language_id']]['language']
				);

			}

			$c = $this->models->ChoicesKeysteps->_get(
				array('id' =>
					array(
						'project_id' => $this->getCurrentProjectId(),
						'keystep_id' => $val['id']
					)
				)
			);

			// step choices
			foreach ((array)$c as $cKey => $cVal) {

				$d  = $this->models->ChoicesContentKeysteps->_get(
					array('id' =>
						array(
							'project_id' => $this->getCurrentProjectId(),
							'choice_id' => $cVal['id'],
						)
					)
				);

				foreach ((array)$d as $dKey => $dVal) {

					$trans2['translation'.$dKey] = array(
						'text' => $dVal['choice_txt'],
						'language' => $languages[$dVal['language_id']]['language']
					);

				}

				$choices['choice'.$cKey] = array(
					'show_order' => $cVal['show_order'],
					'choice_img' => $cVal['choice_img'],
					'choice_image_params' => $cVal['choice_image_params'],
					'target_step_id' => $cVal['res_keystep_id'],
					'target_taxon' => isset($cVal['res_taxon_id']) ? $taxa[$cVal['res_taxon_id']]['taxon'] : null,
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

		$e = $matrices = array();
		$taxa = $this->moduleSession->getModuleSetting('taxa');
		$languages = $this->moduleSession->getModuleSetting('languages');

		// matrices
		$m = $this->models->Matrices->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		// matrix names
		foreach((array)$m as $mKey => $mVal) {

			$mn = $this->models->MatricesNames->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'matrix_id' => $mVal['id'],
						'language_id' => $this->getDefaultProjectLanguage()
					)
				)
			);

			$matrices[$mVal['id']] = $mn[0]['name'];

		}

        $this->moduleSession->setModuleSetting(array(
            'setting' => 'matrices',
            'value' => $matrices
        ));

		foreach((array)$m as $mKey => $mVal) {

			// available taxa
			$mt = $this->models->MatricesTaxa->_get(
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
					'taxon' => $taxa[$mtVal['taxon_id']]['taxon'],
					'id' => $mtVal['taxon_id']
				);

			}

			// matrix names
			$n = $this->models->MatricesNames->_get(
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
					'language' => $languages[$nVal['language_id']]['language'],
				);

				//	if ($nVal['language_id']==$this->getDefaultProjectLanguage())
				//		$_SESSION['admin']['export']['matrices'][$mVal['id']] = $nVal['name'];

			}

			// matrix characters
			$cm = $this->models->CharacteristicsMatrices->_get(
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
				$c = $this->models->Characteristics->_get(
					array(
						'id' => array(
							'id' => $cmVal['characteristic_id'],
							'project_id' => $this->getCurrentProjectId(),
						),
						'columns' => 'id,type'
					)
				);

				$cl = $this->models->CharacteristicsLabels->_get(
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
						'language' => $languages[$clVal['language_id']]['language'],
					);

				}

				// matrix characters states'
				$cs = $this->models->CharacteristicsStates->_get(
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
					$cls = $this->models->CharacteristicsLabelsStates->_get(
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
							'language' => $languages[$clsVal['language_id']]['language'],
						);

					}

					// matrix characters states' taxa
					$mts = $this->models->MatricesTaxaStates->_get(
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
								'taxon' =>  $taxa[$mtsVal['taxon_id']]['taxon'],
								'taxon_id' => $mtsVal['taxon_id']
							);

						} else
						if (isset($mtsVal['ref_matrix_id']) && isset($matrices[$mtsVal['ref_matrix_id']])) {

							$sMatrices['matrix'.$mtsKey] = array(
								'matrix' => $matrices[$mtsVal['ref_matrix_id']],
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

		$languages = $this->moduleSession->getModuleSetting('languages');

		$m = $this->models->FreeModulesProjects->_get(
			array(
				'id' => array(
					'id' => $mId,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$fmp = $this->models->FreeModulesPages->_get(
			array(
				'id' => array(
					'module_id' => $mId,
					'project_id' => $this->getCurrentProjectId()
				)
			)
		);

		// pages
		foreach((array)$fmp as $pKey => $pVal) {

			$cfm = $this->models->ContentFreeModules->_get(
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
					'language' => $languages[$cVal['language_id']]['language'],
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


	private function arrayToXml($data, &$simpleXmlObject)
	{
		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				if(!is_numeric($key))
				{
					if (strpos($key,'__')!==false)
					{
						$key=substr($key,0,strpos($key,'__'));
					}
					$subnode = $simpleXmlObject->addChild("$key");
					$this->arrayToXml($value, $subnode);
				}
				else
				{
					$subnode = $simpleXmlObject->addChild("item$key");
					$this->arrayToXml($value, $subnode);
				}
			}
			else
			{
				$simpleXmlObject->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}

	private function exportDataDownload($data,$filename)
	{
		//return;
		//q($data,1);

		header('Content-disposition:attachment;filename='.$filename);
		header('Content-type:text/xml; charset=utf-8');

		//echo '<pre>';

		print $this->arrayToXml($data);
		die();

		//echo $this->helpers->ArrayToXml->toXml($data);
		//die();
	}

	private function exportDataToFile($p)
	{
		$data=isset($p['data']) ? $p['data'] : null;
		$filename=isset($p['filename']) ? $p['filename'] : null;
		$savefolder=isset($p['savefolder']) ? $p['savefolder'] : null;
		$rootelement=isset($p['rootelement']) ? $p['rootelement'] : 'root';
		$prettify=isset($p['prettify']) ? $p['prettify'] : true;
		$addexportdate=isset($p['addexportdate']) ? $p['addexportdate'] : true;

		$xml=
			'<?xml version="1.0"?><'.$rootelement.($addexportdate ? ' exportdate="'.date('c').'"' : '' ).'></'.$rootelement.'>';

		$simpleXmlObject = new SimpleXMLElement($xml);

		$this->arrayToXml($data,$simpleXmlObject);

		if ($prettify)
		{
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($simpleXmlObject->asXML());
			$out=$dom->saveXML();
		}
		else
		{
			$out=$simpleXmlObject->asXML();
		}

		file_put_contents($savefolder.$filename,$out);

	}

}
