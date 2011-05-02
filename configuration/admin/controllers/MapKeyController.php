<?php

/*

	delete type = delete data!

	make default central point configurable
			$this->smarty->assign('middelLat',24.886436490787712);
			$this->smarty->assign('middelLng',-70.2685546875);
			$this->smarty->assign('initZoom',5);

*/

include_once ('Controller.php');

class MapKeyController extends Controller
{

    public $usedModels = array(
		'occurrence_taxon',
		'geodata_type',
		'geodata_type_title'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Map key';

	public $cssToLoad = array('map.css');

	public $jsToLoad = array(
		'all' => 
			array(
				'mapkey.js',
				'jscolor/jscolor.js',
				'http://maps.google.com/maps/api/js?sensor=false'
			)
		);


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

    public function indexAction()
    {
    
        $this->checkAuthorisation();
        
        $this->setPageName( _('Index'));
		
		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

		$gt = $this->models->GeodataType->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentprojectId()
				),
				'columns' => 'count(*) as total'
			)
		);

		$this->smarty->assign('dataTypeCount',$gt[0]['total']);

        $this->printPage();
    
    }

	public function chooseSpeciesAction()
	{

        $this->checkAuthorisation();

		$this->setPageName(_('Choose a species'));

		$this->getTaxonTree(null,true);
		
		foreach ((array)$this->treeList as $key => $val) {

			if ($val['lower_taxon']=='1') $taxa[$key] = $val;

		}
	
		$pagination = $this->getPagination($taxa,$this->controllerSettings['speciesPerPage']);

		$this->smarty->assign('prevStart', $pagination['prevStart']);
	
		$this->smarty->assign('nextStart', $pagination['nextStart']);

		if(isset($this->treeList)) $this->smarty->assign('taxa',$pagination['items']);

		$this->printPage();	

	}

    public function drawSpeciesAction()
    {

		if (!$this->rHasId()) {
		
			$this->redirect('choose_species.php');
		
		}

        $this->checkAuthorisation();
		
		$this->getTaxonTree(null,true);

		$taxon = $this->treeList[$this->requestData['id']];

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Choose a species'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/choose_species.php'
            )
        );

        $this->setPageName(sprintf(_('Add occurrences for "%s"'),$taxon['taxon']));
		
		$saved = 0;

		if ($this->rHasVal('rnd') && !$this->isFormResubmit()) {
		
			if(isset($this->requestData['markers'])) {

				foreach((array)$this->requestData['markers'] as $key => $val) {

					$s = $this->saveOccurrenceMarker(
						array(
							'taxonId'=> $this->requestData['id'],
							'coord'=> $val,
							'type_id' => $this->requestData['markersDatatype'][$key]
						)
					);
					
					if ($s===true) $saved++;
	
				}

			}

			if(isset($this->requestData['polygons'])) {

				foreach((array)$this->requestData['polygons'] as $key => $val) {
				
					$s = $this->saveOccurrencePolygon(
						array(
							'taxonId' => $this->requestData['id'],
							'coord' => $val,
							'type_id' => $this->requestData['polygonsDatatype'][$key]
						)
					);
	
					if ($s===true) $saved++;
	
				}

			}

		}

		$ot = $this->getTaxonOccurrences($this->requestData['id']);

		if ($ot) $taxon['occurrences'] = $ot;

		if ($this->rHasVal('mapCentre') || isset($_SESSION['system']['mapCentre'])) {

			$middle = (
				$this->rHasVal('mapCentre') ?
					explode(',',trim($this->requestData['mapCentre'],'()')) :
					explode(',',trim($_SESSION['system']['mapCentre'],'()'))
			);

			$zoom = 
				$this->rHasVal('mapZoom') ? 
					$this->requestData['mapZoom'] : 
					(isset($_SESSION['system']['mapZoom']) ? $_SESSION['system']['mapZoom'] : 7);

			$this->smarty->assign('mapInitString','{lat:'.$middle[0].',lng:'.$middle[1].',zoom:'.$zoom.'}');

			if ($this->rHasVal('mapCentre')) $_SESSION['system']['mapCentre'] = $this->requestData['mapCentre'];
			if ($this->rHasVal('mapZoom')) $_SESSION['system']['mapZoom'] = $this->requestData['mapZoom'];

		}

		$this->smarty->assign('geodataTypes',$this->getGeodataTypes());

		$this->smarty->assign('taxon',$taxon);
//q($taxon,1);
		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }

    public function speciesSelectAction()
    {

        $this->checkAuthorisation();

		$this->setPageName(_('Choose a species'));

		$this->getTaxonTree(null,true);

		foreach((array)$this->treeList as $key => $val) {

			$ot = $this->getTaxonOccurrences($val['id']);
			
			if ($ot) $taxa[] = $val;

		}	

		if(isset($taxa)) $this->smarty->assign('taxa',$taxa);

		$this->printPage();
		
    }


    public function speciesAction()
    {

        $this->checkAuthorisation();

		if ($this->rHasId()) {

			$this->setPageName(_('Choose an occurrence'));
	
			$this->setBreadcrumbIncludeReferer(
				array(
					'name' => _('Choose a species'), 
					'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/species_select.php'
				)
			);

			$this->getTaxonTree(null,true);

			$taxon = $this->treeList[$this->requestData['id']];
	
			$ot = $this->getTaxonOccurrences($this->requestData['id']);

			if ($ot) $taxon['occurrences'] = $ot;

			//$ot = $this->customSortArray($ot, array('key' => 'type_id'));

		} else {

			$this->redirect('species_select.php');

		} 

		if(isset($taxon)) $this->smarty->assign('taxon',$taxon);

		$this->printPage();
		
    }


	public function speciesShowAction()
	{

        $this->checkAuthorisation();

		if (!$this->rHasId()) $this->redirect('species.php');

		$isOnline = $this->checkRemoteServerAccessibility();
		
        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Choose an occurrence'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/species.php?id='.$this->requestData['s']
            )
        );

		$this->getTaxonTree(null,true);

		$taxon = $this->treeList[$this->requestData['s']];

//		$this->setPageName(sprintf(_('Species occurrences of "%s"'),$taxon['taxon']));
		$this->setPageName(sprintf(_('"%s"'),$taxon['taxon']));

		if ($this->rHasId() && $isOnline) {
		
			$allNodes = array();

			foreach((array)$this->requestData['id'] as $key => $val) {

				$d = $this->getOccurrence($val);

				if ($d['type']=='polygon') {

					$d['nodes'] = json_decode($d['boundary_nodes']);
					$allNodes = array_merge($allNodes,$d['nodes']);

				} else {
					
					$a[] = array($d['latitude'],$d['longitude']);
					$allNodes = array_merge($allNodes,$a);
				
				}

				$so[] = $d;

			}

        }

		if (count($allNodes)>0) {

			$middle = $this->getPolygonCentre($allNodes);
	
			$sLat = $sLng = 999;
			$lLat = $lLng = -999;
	
			foreach((array)$allNodes as $key => $val) {

				if (!empty($val[0]) && $val[0] < $sLat) $sLat = $val[0];
				if (!empty($val[0]) && $val[0] > $lLat) $lLat = $val[0];
				if (!empty($val[1]) && $val[1] < $sLng) $sLng = $val[1];
				if (!empty($val[1]) && $val[1] > $lLng) $lLng = $val[1];

			}
	
			$this->smarty->assign('mapBorder',
				array(
					'sw' => array('lat' => $sLat,'lng' => $sLng),
					'ne' => array('lat' =>  $lLat,'lng' =>  $lLng)
				)
			);

		}

		$this->smarty->assign('isOnline',$isOnline);

		$this->smarty->assign('occurrences',$so);

		$this->smarty->assign('geodataTypes',$this->getGeodataTypes());

		if (isset($taxon)) $this->smarty->assign('taxon',$taxon);

		if (isset($middle)) $this->smarty->assign('mapInitString','{lat:'.$middle[0].',lng:'.$middle[1].',zoom:7}');

        $this->printPage();

	}


    public function fileAction()
    {

		$this->checkAuthorisation();

		$this->setPageName(_('Occurrence file upload'));

		if ($this->requestDataFiles && !$this->isFormResubmit()) {

			switch ($this->requestData["delimiter"]) {
				case 'comma' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(',');
					break;
				case 'semi-colon' :
					$this->helpers->CsvParserHelper->setFieldDelimiter(';');
					break;
				case 'tab' :
					$this->helpers->CsvParserHelper->setFieldDelimiter("\t");
					break;
			}
			
			$this->helpers->CsvParserHelper->parseFile($this->requestDataFiles[0]["tmp_name"]);
			
			$this->addError($this->helpers->CsvParserHelper->getErrors());
			
			if (!$this->getErrors()) {

				$this->getTaxonTree(null,true);

				$geodataTypes = $this->getGeodataTypes();

				$r = $this->helpers->CsvParserHelper->getResults();
				
				$line = 0;
				$saved = 0;

				foreach((array)$r as $key => $val) {
				
					$line++;
				
					if (is_numeric($val[0])) {

						$taxonId = $val[0];
						
						if (isset($this->treeList[$taxonId])) {

							$geodataTypeId = $val[2];

							if (isset($geodataTypes[$geodataTypeId])) {

								if (trim(strtolower($this->treeList[$taxonId]['taxon']))==trim(strtolower($val[1]))) {
								
									if ($val[5]=='') {
									// marker
									
										if (!empty($val[3]) && !empty($val[4])) {
										
											$val[3] = $this->sanitizeDotsAndCommas($val[3]);
											$val[4] = $this->sanitizeDotsAndCommas($val[4]);
											
											$ot = $this->saveOccurrenceMarker(
												array(
													'taxonId' => $taxonId,
													'lat' => $val[3],
													'lng' => $val[4],
													'type_id' => $geodataTypeId
												)
											);
	
											if ($ot===true) {
											
												$this->addMessage(
													sprintf(
														_('Row %s: saved marker for "%s".'),
														$line,
														$val[1]
													)
												);

												$saved++;
											
											} else {
	
												$this->addError(
													sprintf(
														_('Row %s: unable to save marker for "%s". Duplicate?'),
														$line,
														$val[1]
													)
												);
	
											}
										
										} else {
										
											$this->addError(
												sprintf(
													_('Row %s: marker for "%s" misses %s.'),
													$line,
													$val[1],
													(empty($val[3]) ? _('latitude') : _('longitude') )
												)
											);
										
										}
									
									} else {
									// polygon
									
										$continue=true;
										$i=0;
										$nodes = array();
										
										while($continue && $i<=9999) {
										
											$cLat = isset($val[3+$i]) ? $val[3+$i] : null;
											$cLon = isset($val[4+$i]) ? $val[4+$i] : null;
											
											if ($cLat=='') {
											
												$continue = false;
											
											} else {
											
												if ($cLon=='') {
												
													$this->addError(
														sprintf(
															_('Row %s: polygon node for "%s" misses longitude'),
															$line,
															$val[1]
														)
													);
													
												} else {
												
													$cLat = $this->sanitizeDotsAndCommas($cLat);
													$cLon = $this->sanitizeDotsAndCommas($cLon);
													
													$nodes[] = array($cLat,$cLon);
												
												}
											
											}
											
											$i=$i+2;
										
										}
										
										if (count((array)$nodes)>0) {
	
											$ot = $this->saveOccurrencePolygon(
												array(
													'taxonId' => $taxonId,
													'nodes' => $nodes,
													'type_id' => $geodataTypeId
												)
											);
											
											if ($ot===true) {
											
												$this->addMessage(
													sprintf(
														_('Row %s: saved polygon for "%s".'),
														$line,
														$val[1]
													)
												);
												
												$saved++;
											
											} else {
	
												$this->addError(
													sprintf(
														_('Row %s: unable to save polygon for "%s". Duplicate?'),
														$line,
														$val[1]
													)
												);
											
											}										
											
											unset($geoStr);
											unset($nodes);
	
										}
									
									}
								
								} else {
								
									$this->addError(
										sprintf(
											_('Row %s: mismatch for taxa %s: "%s" (file) <=> "%s" (database)'),
											$line,
											$taxonId,
											$val[1],
											$this->treeList[$taxonId]['taxon']
										)
									);
	
								}
							
							} else {

								$this->addError(sprintf(_('Row %s: unknown data type ID %s'),$line,$geodataTypeId));

							}
	
						} else {
						
							$this->addError(sprintf(_('Row %s: unknown taxon ID %s'),$line,$taxonId));
						
						}
					
					} else {
					
						// first col should be ID, if not an integer: skip line
					
					}
				
				}
				
				if ($saved==0 && count((array)$this->getErrors())==0) {

					$this->addMessage(_('No data to process. Please check that your data is complete and that you are using the field delimiter you have specified above.'));

				}
			
			}

		}

		$this->smarty->assign('geodataTypes',$this->getGeodataTypes());
		
		$this->printPage();

	}


    public function downloadCSVAction()
    {

        $this->checkAuthorisation();

		$this->getTaxonTree(null,true);

		$this->smarty->assign('taxa',$this->treeList);
		$this->smarty->assign('geodataTypes',$this->getGeodataTypes());

		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=taxa.csv");
		header("Content-Type: text/csv");
		header("Content-Transfer-Encoding: binary");

		$this->printPage();
		
    }

	private function getGeodataTypes($id=null)
	{
	
		$d['project_id'] = $this->getCurrentProjectId();
		if (isset($id)) $d['id'] = $id;
	
		$gt = $this->models->GeodataType->_get(
			array(
				'id' => $d,
				'fieldAsIndex' => 'id'
			)
		);

		foreach ((array)$gt as $key => $val) {

			$gtl = $this->models->GeodataTypeTitle->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $_SESSION['project']['default_language_id'],
						'type_id' => $val['id']
					)
				)
			);
			
			$gt[$key]['title'] = isset($gtl[0]['title']) ? $gtl[0]['title'] : '-';

		}
		
		return (isset($id)) ? array_shift($gt) : $gt;
	
	}

	private function saveGeodataTitle($params)
	{

		$languageId = isset($params['language_id']) ? $params['language_id'] : null;
		$title = isset($params['title']) ? $params['title'] : null;
		$type_id = isset($params['type_id']) ? $params['type_id'] : null;

		if ($languageId==null || $type_id==null) return _('Insufficient data.');

		if (empty($params['title'])) {

			return $this->models->GeodataTypeTitle->delete(
				array(
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $languageId,
					'type_id' => $type_id
				)
			);

		} else {

			$gtt = $this->models->GeodataTypeTitle->_get(
				array(
					'id' => array(
						'project_id' => $this->getCurrentProjectId(),
						'language_id' => $languageId,
						'type_id' => $type_id
					)
				)
			);
			
			if (isset($gtt[0]['title']) && $gtt[0]['title']==$title) return true;

			return $this->models->GeodataTypeTitle->save(
				array(
					'id' => (isset($gtt[0]['id']) ? $gtt[0]['id'] : 'null' ),
					'project_id' => $this->getCurrentProjectId(),
					'language_id' => $languageId,
					'type_id' => $type_id,
					'title' => $title
				)
			);

		}
	
	}

	private function saveGeodataColour($params)
	{

		$id = isset($params['id']) ? $params['id'] : null;
		$colour = isset($params['colour']) ? $params['colour'] : null;

		if (!isset($id) ||!isset($colour)) return false;

		return $this->models->GeodataType->save(
			array(
				'id' => $id, 
				'project_id' => $this->getCurrentProjectId(), 
				'colour' => $colour
			)
		);

	}
			
    private function createGeodataType ($title,$languageId)
    {

		$gtt = $this->models->GeodataTypeTitle->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'title' => $title, 
					'language_id' => $languageId
				),
				'columns' => 'count(*) as total'
			)
		);
		
		if ($gtt[0]['total']>0) return _('A data type with that name already exists.');

		$this->models->GeodataType->save(
			array(
				'id' => null, 
				'project_id' => $this->getCurrentProjectId(), 
			)
		);

		$typeId = $this->models->GeodataType->getNewId();

		$gt = $this->saveGeodataTitle(
			array(
				'title' => $title,
				'language_id' => $languageId,
				'type_id' => $typeId
			)
		);
		
		if ($gt!==true) {

			$this->models->GeodataType->delete(
				array(
					'id' => $typeId, 
					'project_id' => $this->getCurrentProjectId(), 
				)
			);

		}
		
		return $gt;

    }

    private function deleteGeodataType ($id)
    {

		if (!$id) return;

		$this->models->GeodataTypeTitle->delete(
			array(
				'type_id' => $id, 
				'project_id' => $this->getCurrentProjectId(), 
			)
		);
		
		$this->models->GeodataType->delete(
			array(
				'id' => $id, 
				'project_id' => $this->getCurrentProjectId(), 
			)
		);

    }
	

	public function dataTypesAction()
	{
	
		$this->checkAuthorisation();
		
		$this->setPageName( _('Data types'));

		$lp = $_SESSION['project']['languages'];
		
		$defaultLanguage = $_SESSION['project']['default_language_id'];
		
        if ($this->rHasVal('del_type') && !$this->isFormResubmit()) {
		// deleting a type
        
            $tp = $this->deleteGeodataType($this->requestData['del_type']);

        } else
        if ($this->rHasVal('new_type') && !$this->isFormResubmit()) {
        // adding a new type
        
            $tp = $this->createGeodataType($this->requestData['new_type'],$defaultLanguage);
            
            if ($tp !== true) $this->addError($tp);

        }

		$types = $this->models->GeodataType->_get(
			array(
				'id' => array('project_id' => $this->getCurrentProjectId()),
			)
		);

		foreach ((array) $types as $key => $type) {

			foreach ((array) $lp as $k => $language) {

				$gtt = $this->models->GeodataTypeTitle->_get(
					array(
						'id' =>
					array(
						'project_id' => $this->getCurrentProjectId(), 
						'type_id' => $type['id'], 
						'language_id' => $language['language_id']
						)
					)
				);
				
				$types[$key]['type_titles'][$language['language_id']] = $gtt[0]['title'];
			
			}
		
		}

        $this->smarty->assign('maxTypes', $this->controllerSettings['maxTypes']);

		$this->smarty->assign('languages', $lp);
		
		$this->smarty->assign('defaultLanguage', $defaultLanguage);
		
        $this->smarty->assign('types', $types);

		$this->printPage();
		
	}

	private function ajaxGetTypeLabels()
	{
	
		if (!$this->rHasVal('language')) return;

		return $this->models->GeodataTypeTitle->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
					'language_id' => $this->requestData['language']
					)
				)
		);

	}

	private function ajaxGetTypeColours()
	{
	
		return $this->models->GeodataType->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(), 
				),
				'columns' => 'id,colour'
				)
		);

	}


    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
        if ($this->requestData['action'] == 'save_type_label') {
		
			$d = $this->saveGeodataTitle(
				array(
					'language_id' => $this->requestData['language'],
					'title' => $this->requestData['value'],
					'type_id' => $this->requestData['id']
				)
			);

			$this->smarty->assign('returnText',$d ? _('saved') : _('could not save'));

        } else
        if ($this->requestData['action'] == 'get_type_labels') {
		
			$this->smarty->assign('returnText',json_encode($this->ajaxGetTypeLabels()));

        } else
        if ($this->requestData['action'] == 'save_type_colour') {
		
			$d = $this->saveGeodataColour(
				array(
					'id' => $this->requestData['id'],
					'colour' => $this->requestData['value']
				)
			);

			$this->smarty->assign('returnText',$d ? _('saved') : _('could not save'));

        } else
        if ($this->requestData['action'] == 'get_type_colours') {
		
			$this->smarty->assign('returnText',json_encode($this->ajaxGetTypeColours()));

        } else
        if ($this->requestData['action'] == 'delete_occurrence') {
            
            if ($this->deleteOccurrence($this->requestData['id']))

				$this->smarty->assign('returnText',1);

			else

				$this->smarty->assign('returnText',0);

        }

        $this->printPage();
    
    }

	private function checkRemoteServerAccessibility()
	{

		$f = @fopen($this->controllerSettings['urlToCheckConnectivity'], 'r');

		if (!$f) return false;

		fclose($f);

		return true;
		
	}

	private function saveOccurrenceMarker($p)
	{

		$taxonId = isset($p['taxonId']) ? $p['taxonId'] : null;
		$lat = isset($p['lat']) ? $p['lat'] : null;
		$lng = isset($p['lng']) ? $p['lng'] : null;
		$type_id = isset($p['type_id']) ? $p['type_id'] : null;

		if (!isset($lat) && !isset($lng) && isset($p['coord'])) {

			$d = explode(',',str_replace(array('(',')'),'',$p['coord']));
			$lat = trim($d[0]);
			$lng = trim($d[1]);

		}

		if (!isset($taxonId) || !isset($lat) || !isset($lng) || !isset($type_id)) return;

		// 'Point' is the newer method, but seems unable to take the SRID into account
		return $this->models->OccurrenceTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $taxonId,
				'type_id' => $type_id,
				'type' => 'marker',
				'coordinate' => "#GeomFromText('POINT((".$lat." ".$lng."))',".$this->controllerSettings['SRID'].")",
//				'coordinate' => '#Point('.$lat.','.$lng.')',
				'latitude' => $lat,
				'longitude' => $lng,
				'nodes_hash' => md5($lat.','.$lng)
			)
		);

	}


	private function saveOccurrencePolygon($p)
	{

		/*
			called from map, has a single string of coordinates:
				["coord"]=> "(51.36, 6.66),(51.33, 4.09),(52.87, 4.78)"

			called from file load, has an array of nodes:
				["nodes"]=> array(array(51.36,6.66),array(51.33,4.09),array(52.87,4.78));
			
			furthermore, the mysql function POLYGON requires a string in the form
				GeomFromText('POLYGON((51.36 6.66,51.33 4.09,52.87 4.78,51.36 6.66))',SRID) 
				
		*/

		$taxonId = isset($p['taxonId']) ? $p['taxonId'] : null;
		$type_id = isset($p['type_id']) ? $p['type_id'] : null;
		$nodes = isset($p['nodes']) ? $p['nodes'] : null;
		$coord = isset($p['coord']) ? $p['coord'] : null;

		if (!isset($nodes) && isset($coord)) {

			$d = explode('),(',trim($coord,'()'));

			if (count((array)$d)>2) {

				foreach((array)$d as $key => $val) {
	
					$g = explode(',',$val);
					$nodes[] = array($g[0],$g[1]);
	
				}
	
			}

		}

		if (count((array)$nodes)<=2) return;

		// remove the last node if it is identical to the first, just in case
		if ($nodes[0]==$nodes[count((array)$nodes)-1]) array_pop($nodes);

		// create a string for mysql (which does require the first and last to be the same)
		foreach((array)$nodes as $key => $val) $geoStr[] = $val[0].' '.$val[1];
		$geoStr = implode(',',$geoStr).','.$geoStr[0];

		if (!isset($taxonId) || !isset($geoStr) || !isset($nodes) || !isset($type_id)) return;

		return $this->models->OccurrenceTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $taxonId,
				'type_id' => $type_id,
				'type' => 'polygon',
				'boundary' => "#GeomFromText('POLYGON((".$geoStr."))',".$this->controllerSettings['SRID'].")",
				'boundary_nodes' => json_encode($nodes),
				'nodes_hash' => md5(json_encode($nodes))
			)
		);

	}

	private function getTaxonOccurrences($id)
	{
	
		if (!isset($id)) return;

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id,
				),
				'columns' => 'id,type,type_id,latitude,longitude,boundary_nodes,boundary'
			)
		);

		foreach((array)$ot as $key => $val) {
		
			$d = $this->getGeodataTypes($val['type_id']);	
			$ot[$key]['type_title'] = $d['title'];
			$ot[$key]['colour'] = $d['colour'];
			if ($val['type']=='polygon' && isset($val['boundary_nodes'])) $ot[$key]['nodes'] = json_decode($val['boundary_nodes']);

		}
	
		return $ot;
	
	}

	private function getOccurrence($id)
	{

		$ot = $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'id' => $id,
					'project_id' => $this->getCurrentProjectId(),
				)
			)
		);

		$d = $this->getGeodataTypes($ot[0]['type_id']);	
		$ot[0]['type_title'] = $d['title'];
		$ot[0]['colour'] = $d['colour'];

		$this->getTaxonTree(null,true);

		if ($ot) $ot[0]['taxon'] = $this->treeList[$ot[0]['taxon_id']];

		return $ot[0];
	
	}

	private function deleteOccurrence($id)
	{

		if (!isset($id)) return;
	
		return $this->models->OccurrenceTaxon->delete(
			array(
				'id' => $id,
				'project_id' => $this->getCurrentProjectId()
			)
		);

	}

	private function getPolygonCentre($p)
	{

		if (count((array)$p)==0) return null;

		$x = $y = 0;

		foreach((array)$p as $key => $val) {
			
			$x += $val[0];
			$y += $val[1];
	
		}
	
		return array($x/count($p),$y/count($p));
	
	}

	private function sanitizeDotsAndCommas($val)
	{
	
		if (strpos($val,',')!==false && strpos($val,'.')!==false) {

			$val = str_replace(',','',$val);

		} else
		if (strpos($val,',')!==false && strpos($val,'.')===false) {
	
			$val = str_replace(',','.',$val);

		}
		if (substr_count($val,'.')>1) {

			$x = strrpos($val,'.');

			$val = str_replace('.',substr($val,0,$x-1)).substr($val,$x);

		}

		return $val;
	
	}


}