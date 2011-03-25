<?php

/*

	make default central point configurable
			$this->smarty->assign('middelLat',24.886436490787712);
			$this->smarty->assign('middelLng',-70.2685546875);
			$this->smarty->assign('initZoom',5);

*/


/*

keuze google map(s)
	vierkant op basis coordinaten
	handmatig getekend
	een of andere google string
		coordinaten
		lagen
		zoom level

per soort: voorkomen
	punt
	polygoon
	per unit (punt/polygoon) metadata
		wanneer
		hoeveel
		url
	in kmz-file / csv
	één kmz-file per soort
	kmz files
		uploadnaam
		nieuwe eigen onveranderlijke unieke naam geven
		date of adding
		user comment (opt)

browsing
	lijst van soorten, download/delete/add kmz file
	lijst van soorten met kmz
	lijst van soorten zonder kmz

zoeken
	kies kaart
	draw polygon
	click something
	results:
		txt: welke soorten
		map: incidences + polygons

weergave
	gebied -> welke soorten
	soort -> welke gebieden
	soorten -> welke gebieden
	download map as png
		lokaal opslaan
		display only
		meta:
			"naam" (whatever)
			datum van opslag
			koppeling welke kmz-files
			user comment (opt)
		lijst van afbeeldingen
		lijst van soorten -> afbeeldingem

auto-switch
	detect mechanism
	als offline, list of pics
	als online, map + avial list of pics

*/

include_once ('Controller.php');

class MapKeyController extends Controller
{
    
    public $usedModels = array(
		'map',
		'map_name',
		'occurrence_taxon'
	);
    
    public $usedHelpers = array('csv_parser_helper');

    public $controllerPublicName = 'Map key';

	public $cssToLoad = array('map.css');

	public $jsToLoad = array(
		'all' => 
			array(
				'mapkey.js',
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

        $this->printPage();
    
    }

	public function chooseSpeciesAction()
	{

        $this->checkAuthorisation();

		$this->setPageName(_('Choose a species'));

		$this->getTaxonTree(null,true);

		if(isset($this->treeList)) $this->smarty->assign('taxa',$this->treeList);

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
							'coord'=> $val
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
							'coord' => $val
						)
					);
	
					if ($s===true) $saved++;
	
				}

			}

		}

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
		
		$this->smarty->assign('taxon',$taxon);

		$this->smarty->assign('isOnline',$this->checkRemoteServerAccessibility());

        $this->printPage();
    
    }

    public function speciesAction()
    {

        $this->checkAuthorisation();

		$this->setPageName(_('Choose an occurrence'));

		$this->getTaxonTree(null,true);

		foreach((array)$this->treeList as $key => $val) {

			$ot = $this->getTaxonOccurrences($val['id']);
			
			if ($ot) {

				$val['occurrences'] = $ot;
				$taxa[] = $val;

			}

		}	

		if(isset($taxa)) $this->smarty->assign('taxa',$taxa);

		$this->printPage();
		
    }


	public function speciesShowAction()
	{

        $this->checkAuthorisation();

		$isOnline = $this->checkRemoteServerAccessibility();
		
		if (!$this->rHasId() && !$this->rHasVal('t')) {

			$this->redirect('species.php');

		}

        $this->setBreadcrumbIncludeReferer(
            array(
                'name' => _('Choose an occurrence'), 
                'url' => $this->baseUrl . $this->appName . '/views/' . $this->controllerBaseName . '/species.php'
            )
        );

		$this->setPageName(_('Species occurrences'));

		$allNodes = array();

		if ($this->rHasVal('t') && $isOnline) {

			$this->getTaxonTree(null,true);
			
			$taxon = $this->treeList[$this->requestData['t']];
		
			$this->setPageName(sprintf(_('Species occurrences for "%s"'),$taxon['taxon']));

			$so = $this->getTaxonOccurrences($this->requestData['t']);

			foreach((array)$so as $key => $val) {

				if ($val['type']=='polygon') {

					$so[$key]['nodes'] = json_decode($val['boundary_nodes']);
					$allNodes = array_merge($allNodes,$so[$key]['nodes']);

				} else {
					
					$a[] = array($val['latitude'],$val['longitude']);
					$allNodes = array_merge($allNodes,$a);
				
				}

			}

		} elseif ($this->rHasId() && $isOnline) {
		
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
				
				$r = $this->helpers->CsvParserHelper->getResults();
				
				$line = 0;
				
				foreach((array)$r as $key => $val) {
				
					$line++;
				
					if (is_numeric($val[0])) {
					
						$taxonId = $val[0];
						
						if (isset($this->treeList[$taxonId])) {

							if (trim(strtolower($this->treeList[$taxonId]['taxon']))==trim(strtolower($val[1]))) {
							
								if ($val[4]=='') {
								// marker
								
									if (!empty($val[2]) && !empty($val[3])) {
									
										$val[2] = $this->sanitizeDotsAndCommas($val[2]);
										$val[3] = $this->sanitizeDotsAndCommas($val[3]);
										
										$ot = $this->saveOccurrenceMarker(
											array(
												'taxonId' => $taxonId,
												'lat' => $val[2],
												'lng' => $val[3]
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
												(empty($val[2]) ? _('latitude') : _('longitude') )
											)
										);
									
									}
								
								} else {
								// polygon
								
									$continue=true;
									$i=0;
									$geoStr = array();
									$nodes = array();
									
									while($continue && $i<=9999) {
									
										$cLat = isset($val[4+$i]) ? $val[4+$i] : null;
										$cLon = isset($val[5+$i]) ? $val[5+$i] : null;
										
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
												
												$geoStr[] = $cLat.' '.$cLon;
												$nodes[] = array($cLat,$cLon);
											
											}
										
										}
										
										$i=$i+2;
									
									}
									
									if (count($geoStr)>0) {

										$ot = $this->saveOccurrencePolygon(
											array(
												'taxonId' => $taxonId,
												'geoStr' => implode(',',$geoStr),
												'nodes' => $nodes
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
						
							$this->addError(sprintf(_('Row %s: unknown ID %s'),$line,$taxonId));
						
						}
					
					} else {
					
						// first col should be ID, if not an integer: skip line
					
					}
				
				}
			
			}

		}
		
		$this->printPage();

	}


    public function downloadCSVAction()
    {

        $this->checkAuthorisation();

		$this->getTaxonTree(null,true);

		$this->smarty->assign('taxa',$this->treeList);

		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=taxa.csv");
		header("Content-Type: text/csv");
		header("Content-Transfer-Encoding: binary");

		$this->printPage();
		
    }

    /**
     * AJAX interface for this class
     *
     * @access    public
     */
    public function ajaxInterfaceAction ()
    {

        if (!$this->rHasVal('action')) return;
        
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

		if (!isset($lat) && !isset($lng) && isset($p['coord'])) {

			$d = explode(',',str_replace(array('(',')'),'',$p['coord']));
			$lat = trim($d[0]);
			$lng = trim($d[1]);

		}

		if (!isset($taxonId) || !isset($lat) || !isset($lng)) return;

		return $this->models->OccurrenceTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $taxonId,
				'type' => 'marker',
				'coordinate' => '#Point('.$lat.','.$lng.')',
				'latitude' => $lat,
				'longitude' => $lng,
				'nodes_hash' => md5($lat.','.$lng)
			)
		);
	
	}

	private function saveOccurrencePolygon($p)
	{

		$taxonId = isset($p['taxonId']) ? $p['taxonId'] : null;
		$geoStr = isset($p['geoStr']) ? $p['geoStr'] : null;
		$nodes = isset($p['nodes']) ? $p['nodes'] : null;
		
		if (!isset($geoStr) && !isset($nodes) && isset($p['coord'])) {
		
			$d = explode('),(',trim($p['coord'],'()'));
			
			if (count((array)$d)>2) {

				foreach((array)$d as $key => $val) {
	
					$g = explode(',',$val);
					$geoStr[] = $g[0].' '.$g[1];
					$nodes[] = array($g[0],$g[1]);
	
				}
	
				// mysql polygon has to be closed, i.e. first and last nodes must be identical
				$g = explode(',',$d[0]);
				
				$geoStr = implode(',',$geoStr).','.$g[0].' '.$g[1];

			}

		}

		if (!isset($taxonId) || !isset($geoStr) || !isset($nodes)) return;

		if (count((array)$nodes)<=2) return;

		return $this->models->OccurrenceTaxon->save(
			array(
				'id' => null,
				'project_id' => $this->getCurrentProjectId(),
				'taxon_id' => $taxonId,
				'type' => 'polygon',
				'boundary' => "#GeomFromText('POLYGON((".$geoStr."))')",
				'boundary_nodes' => json_encode($nodes),
				'nodes_hash' => md5(json_encode($nodes))
			)
		);

	}

	private function getTaxonOccurrences($id)
	{
	
		if (!isset($id)) return;

		return $this->models->OccurrenceTaxon->_get(
			array(
				'id' => array(
					'project_id' => $this->getCurrentProjectId(),
					'taxon_id' => $id,
				),
				'columns' => 'id,type,latitude,longitude,boundary_nodes'
			)
		);
	
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























